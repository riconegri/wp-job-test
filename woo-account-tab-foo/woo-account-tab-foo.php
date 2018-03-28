<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://localhost/rnegri
 * @since             1.0.0
 * @package           Woo_Account_Tab_Foo
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Account Tab Foo
 * Plugin URI:        http://localhost
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            R. Negri
 * Author URI:        http://localhost/rnegri
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-account-tab-foo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-account-tab-foo-activator.php
 */
function activate_woo_account_tab_foo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-account-tab-foo-activator.php';
	Woo_Account_Tab_Foo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-account-tab-foo-deactivator.php
 */
function deactivate_woo_account_tab_foo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-account-tab-foo-deactivator.php';
	Woo_Account_Tab_Foo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_account_tab_foo' );
register_deactivation_hook( __FILE__, 'deactivate_woo_account_tab_foo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-account-tab-foo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_account_tab_foo() {

	$plugin = new Woo_Account_Tab_Foo();
	$plugin->run();

}
run_woo_account_tab_foo();
