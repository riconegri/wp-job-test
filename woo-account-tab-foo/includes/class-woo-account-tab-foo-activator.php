<?php

/**
 * Fired during plugin activation
 *
 * @link       http://localhost/rnegri
 * @since      1.0.0
 *
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/includes
 * @author     R. Negri <rnegri@gmail.com>
 */
class Woo_Account_Tab_Foo_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        add_rewrite_endpoint('user-extra-info', EP_ROOT | EP_PAGES);
        flush_rewrite_rules();
	}

}
