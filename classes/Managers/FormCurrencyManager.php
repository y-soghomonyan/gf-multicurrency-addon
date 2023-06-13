<?php


namespace LeverageIT\GFMultiCurrency\Managers;


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
        add_filter( 'gform_currencies', array($this, 'fix_eur_separators') );
        add_filter( 'gform_currency', array(&$this, 'change_currency') );

        // add_filter('gform_yaadpay_form_query', [$this, 'yaadpay_integration'], 20, 2);
        add_filter( 'gform_entry_pre_update', [$this, 'pre_entry_update'], 20, 2 );
//        add_filter( 'gform_notification', [$this, 'modify_notification_fields'], 10, 3 );

    }

    public function modify_notification_fields( $notification, $form, $entry )
    {
        $currency = $form['multi_currency_selector'];
        if(!$currency) return $entry;
        $entry['currency'] = $currency;
        $this->_currency = $currency;
        // Update the entry in the database
        \GFAPI::update_entry( $entry );

        // Return the modified notification object
        return $notification;
    }

    public function pre_entry_update( $entry, $original_entry )
    {
        $form_id = rgar($entry, 'form_id');
        $form = \GFAPI::get_form($form_id);
        $currency = $form['multi_currency_selector'];
        if(!$currency) return $entry;

        $entry['currency'] = $currency;

        $gform_order = maybe_unserialize( rgar( $entry, 'gform_order' ) );
        if ( is_array( $gform_order ) ) {
            $gform_order['currency'] = 'USD'; // Replace 'USD' with the desired currency code
            gform_update_meta( $entry['id'], 'gform_order', maybe_serialize( $gform_order ) );
        }

        return $entry;
    }

    public function yaadpay_integration( $query_string, $form )
    {
        parse_str($query_string, $query_array);
        $currency = $form['multi_currency_selector'];
//        $currency = 'ILS';
        if(!$currency) return $query_string;

        $query_array['Coin'] = $currency;
        return http_build_query($query_array);
    }

	public function change_currency($currency)
	{
        $this->set_currency();
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

