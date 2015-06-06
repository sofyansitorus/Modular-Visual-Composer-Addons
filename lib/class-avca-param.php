<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

abstract class AvcaParam {

	protected $_plugin_data;

	public static function init($plugin_data = array()) {
		$this->_plugin_data = $_plugin_data;
	}

	protected static function run() {
		$this->run();
	}

	public static function get_settings() {
		return array();
	}

}