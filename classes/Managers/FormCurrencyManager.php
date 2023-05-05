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