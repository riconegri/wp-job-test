<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://localhost/rnegri
 * @since      1.0.0
 *
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 * @author     R. Negri <rnegri@gmail.com>
 */
class Woo_Account_Tab_Foo_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        flush_rewrite_rules();
	}

}
