<?php
/*
* Plugin Name: MultiCurrency add-on for Gravity Forms
* Description: Creates a form setting to select paying currency for each form
* Version: 1.0
* Author: Yervand Soghomonyan
* Text Domain: gf-multicurrency-addon
*/
if (! defined('ABSPATH')) {
	exit;
}

try {
	if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
		throw new \Exception("File not found: " . __DIR__ . '/vendor/autoload.php');
	}
	require_once __DIR__ . '/vendor/autoload.php';
} catch (\Exception $e) {
	echo 'Caught exception: ', $e->getMessage(), "\n";
}

use GFMultiCurrency\Plugin;

if (class_exists(Plugin::class)) :
	new Plugin();
endif;