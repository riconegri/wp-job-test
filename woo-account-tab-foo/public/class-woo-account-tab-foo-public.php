<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://localhost/rnegri
 * @since      1.0.0
 *
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Account_Tab_Foo
 * @subpackage Woo_Account_Tab_Foo/public
 * @author     R. Negri <rnegri@gmail.com>
 */
class Woo_Account_Tab_Foo_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Account_Tab_Foo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Account_Tab_Foo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
		    $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woo-account-tab-foo-public.css',
            array(), $this->version,
            'all'
        );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Account_Tab_Foo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Account_Tab_Foo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
		    $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/woo-account-tab-foo-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );

	}

    public function custom_endpoint()
    {
        add_rewrite_endpoint('user-extra-info', EP_ROOT | EP_PAGES);
    }

    public function custom_woo_my_account_menu_items($items)
    {
        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);

        // Insert your custom endpoint.
        $items['user-extra-info'] = __('User Extra Info', 'woo-account-tab-foo');

        // Insert back the logout item.
        $items['customer-logout'] = $logout;

        return $items;
    }

    public function custom_query_vars($vars)
    {
        $vars[] = 'user-extra-info';

        return $vars;
    }

    public function custom_endpoint_content()
    {
        $user_id = get_current_user_id();
        $meta_var = get_user_meta($user_id, 'woo_account_search');
//    print_r( $user_id, $meta_var );
        ?>
        <h3><?php _e("User Extra Configuration", "blank") ?></h3>
        <form id="um_form" method="POST">
            <table class="form-table">
                <tr>
                    <th><label for="search-woo-account"><?php _e("User Meta Search"); ?></label></th>
                    <td>
                        <input type="text" name="search-woo-account" id="search-woo-account"
                               value="<?php echo esc_attr(implode(', ', $meta_var[0])); ?>" class="regular-text"/><br/>
                        <span class="description"><?php _e("More than one term, separate it by a comma"); ?></span>
                    </td>
                    <td>
                        <button type="submit"><?php _e('Save!') ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <h3><?php _e('Result\'s List') ?></h3>

        <?php

        $cache = get_transient('my_custom_transient_id_' . $user_id);

        if (false === $cache) {
            echo '<ul><li>' . __('No items') . '</li></ul>';
        } else {
            echo $cache;
        }
    }

    public function custom_endpoint_title($title)
    {
        global $wp_query;

        $is_endpoint = isset($wp_query->query_vars['user-extra-info']);

        if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
            // New page title.
            $title = __('User Extra Config', 'woo-account-tab-foo');

            remove_filter('the_title', 'custom_endpoint_title');
        }

        return $title;
    }

    public function account_tab()
    {

        $ajax_url = admin_url('admin-ajax.php');

        wp_localize_script(
            'woo_account_tab',
            'ajax_url',
            $ajax_url
        );

//    wp_register_script( 'woo_account_tab', plugins_url('woo-account-tab.js',__FILE__ ));
        wp_enqueue_script('woo_account_tab');

    }

    /**
     * AJAX Callback
     * Always Echos and Exits
     */
    public function modifications_callback()
    {

        $transient_id = 'my_custom_transient_id_' . get_current_user_id();

        // Ensure we have the data we need to continue
        if (!isset($_POST) || empty($_POST) || !is_user_logged_in()) {

            // If we don't - return custom error message and exit
            header('HTTP/1.1 400 Empty POST Values');
            echo 'Could Not Verify POST Values.';
            exit;
        }

        // remove old cache
        delete_transient($transient_id);

        $user_id = get_current_user_id();                            // Get our current user ID
        $um_val = explode(',', sanitize_text_field($_POST['woo-account-search']));      // Sanitize our user meta value
        $clean_array = array();

        foreach ($um_val as $k => $v) {
            if ($v) {
                $clean_array[trim($k)] = trim($v);
            }
        }

        update_user_meta($user_id, 'woo_account_search', $clean_array, false);                // Update our user meta

        $html_code = '';
        $result_array = array();
        if (count($clean_array)) {
            $args = array(
                'body' => array('terms' => $clean_array),
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array()
            );
            $response = wp_remote_post('https://httpbin.org/post', $args);

            if ($response['response']['code'] === 200) {
                foreach ($response['headers'] as $key => $header) {
                    $result_array[sanitize_text_field($key)] = sanitize_text_field($header);
                }

                $html_code = '<div class="a-stats"><ul class="example-transient">';

                if (count($result_array)) {
                    foreach ($result_array as $k => $v) {
                        $html_code .= '<li>' . $v . '</li>';
                    }
                } else {
                    $html_code .= '<li>' . __('No items') . '</li>';
                }
                $html_code .= '</ul></div>';

                set_transient($transient_id, $html_code);
            }
        }

        $response_array = array('data' => $result_array, 'fragment' => array('element' => 'example-transient', 'content' => $html_code));

        wp_send_json($response_array);
        exit;
    }

    public function custom_register_widgets()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-account-tab-foo-widgets.php';
        register_widget('Woo_Account_Tab_Foo_Custom_Widget');
    }
//wp-content/plugins/woo-account-tab-foo/includes/class-woo-account-tab-foo-widgets.php
}
