<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://localhost/rnegri
 * @since      1.0.0
 *
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 * @author     R. Negri <rnegri@gmail.com>
 */
class Woo_Account_Tab_Foo_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-account-tab-foo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
