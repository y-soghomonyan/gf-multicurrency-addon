<?php


namespace GFMultiCurrency\Managers;


class FormCurrencyManager {

	private static $instance;

	private $_currency;

	public static function init()
	{
		if (!self::$instance) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function __construct()
	{
		if (!$this->is_gravityforms_supported()) {
			return false;
		}
		$this->addFilters();
	}

	public function addFilters()
	{
		if (is_admin()) {
			add_action( 'gform_admin_pre_render', array( &$this, 'render_form' ) );
		} else {
			add_filter( 'gform_pre_render', array( &$this, 'render_form' ) );
		}
		add_action( 'get_header', [$this, 'set_currency'], 9999 );
		add_filter( 'gform_currencies', array($this, 'fix_eur_separators') );
		add_filter( 'gform_currency', array(&$this, 'change_currency') );
	}

	public function change_currency($currency)
	{
		if ($this->_currency) {
			$currency = $this->_currency;
		}

		return $currency;
	}

	public function set_currency(  )
	{
		global $post;

		if (empty($post)) {
			return; // Exit if no post object is available
		}

		// Regular expression pattern to match the Gravity Forms shortcode
		$pattern = '/\[gravityform[\s\S]*id="(.*?)"/i';

		// Check if the post content contains the Gravity Forms shortcode
		if (has_shortcode($post->post_content, 'gravityform') || has_shortcode($post->post_content, 'gravityforms')) {
			preg_match($pattern, $post->post_content, $matches);
			if (isset($matches[1])) {
				$form_id = $matches[1];
				// var_dump($form_id);die;

				// Form ID is available, you can now perform actions based on it
				// For example, echo the form ID:
				$form = \GFAPI::get_form($form_id);

				if (!is_wp_error($form)) {
					$this->_currency = $form['multi_currency_selector'];
				}
			}
		}
	}

	public function render_form($form)
	{
		if (isset($form['multi_currency_selector']) && $form['multi_currency_selector']) {
			$this->_currency = $form['multi_currency_selector'];
		}
		return $form;
	}

	public function fix_eur_separators( $currencies )
	{
		$currencies['EUR']['thousand_separator'] = ',';
		$currencies['EUR']['decimal_separator'] = '.';

		return $currencies;
	}

	private function is_gravityforms_supported()
	{
		if (class_exists("\GFCommon")) {
			return true;
		}

		return false;
	}

}

