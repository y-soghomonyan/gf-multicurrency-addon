<?php

namespace GFMultiCurrency;

use LeverageIT\GFMultiCurrency\Managers\FormCurrencyManager;
use LeverageIT\GFMultiCurrency\Managers\FormFieldsManager;

class Plugin
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$this->activateManagers();
	}

	public function activateManagers()
	{
		FormFieldsManager::init();
		FormCurrencyManager::init();
	}

}
