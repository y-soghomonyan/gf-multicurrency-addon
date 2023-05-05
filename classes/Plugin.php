<?php

namespace GFMultiCurrency;

use GFMultiCurrency\Managers\FormCurrencyManager;
use GFMultiCurrency\Managers\FormFieldsManager;

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
