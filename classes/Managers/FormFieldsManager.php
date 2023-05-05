<?php

namespace GFMultiCurrency\Managers;

class FormFieldsManager
{
	public static function init()
	{
		new static();
	}

	public function __construct()
	{
		$this->addFilters();
	}

	public function addFilters()
	{
		add_filter( 'gform_form_settings', array($this, 'add_form_settings'), 20, 2 );
		add_filter( 'gform_pre_form_settings_save', array($this, 'save_form_settings'), 20, 2 );
	}

	public function add_form_settings( $settings, $form )
	{
		ob_start();
		$field_value = rgar($form, 'multi_currency_selector');
		if(empty($field_value)) {
			$field_value = get_option('rg_gforms_currency');
		}
		?>
		<tr>
			<th><label  for="multi_currency_selector"><?= __('Choose Currency', 'gf-multicurrency-addon') ?></label></th>
			<td>
				<select name="multi_currency_selector" id="multi_currency_selector">
					<?php foreach (\RGCurrency::get_currencies() as $code => $currency): ?>
						<option value="<?php echo $code ?>" <?php selected($field_value,$code)?>><?php echo $currency['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
		$settings['GF Currency Settings']['gf_currency_settings'] = ob_get_clean();

		return $settings;
	}

	public function save_form_settings($form)
	{
		$form['multi_currency_selector'] = rgpost( 'multi_currency_selector' );
		return $form;
	}
}