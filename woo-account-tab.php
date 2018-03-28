<?php
/*
Plugin Name: Woo Account Tab Plugin
Description: Job test
Author: Ricardo Negri
Version: 0.1
*/

/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function my_custom_endpoints()
{
    add_rewrite_endpoint('extra-config', EP_ROOT | EP_PAGES);
}

add_action('init', 'my_custom_endpoints');

/**
 * Flush rewrite rules on plugin activation.
 */
function my_custom_flush_rewrite_rules()
{
    add_rewrite_endpoint('extra-config', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'my_custom_flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'my_custom_flush_rewrite_rules');

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
function my_custom_my_account_menu_items($items)
{
    // Remove the logout menu item.
    $logout = $items['customer-logout'];
    unset($items['customer-logout']);

    // Insert your custom endpoint.
    $items['extra-config'] = __('User Extra', 'woocommerce');

    // Insert back the logout item.
    $items['customer-logout'] = $logout;

    return $items;
}

add_filter('woocommerce_account_menu_items', 'my_custom_my_account_menu_items');

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function my_custom_query_vars($vars)
{
    $vars[] = 'extra-config';

    return $vars;
}

add_filter('query_vars', 'my_custom_query_vars', 0);


/**
 * Endpoint HTML content.
 */
function my_custom_endpoint_content()
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

add_action('woocommerce_account_extra-config_endpoint', 'my_custom_endpoint_content');

/*
 * Change endpoint title.
 *
 * @param string $title
 * @return string
 */
function my_custom_endpoint_title($title)
{
    global $wp_query;

    $is_endpoint = isset($wp_query->query_vars['extra-config']);

    if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
        // New page title.
        $title = __('User Extra Config', 'woocommerce');

        remove_filter('the_title', 'my_custom_endpoint_title');
    }

    return $title;
}

add_filter('the_title', 'my_custom_endpoint_title');

function woo_account_tab()
{

    $ajax_url = admin_url('admin-ajax.php');

    wp_register_style('woo_account_tab', plugins_url('woo-account-tab.css', __FILE__));
    wp_enqueue_style('woo_account_tab');

    wp_register_script(
        'woo_account_tab',                             // Our Custom Handle
        plugins_url('woo-account-tab.js', __FILE__),  // Script URL, this script is located for me in `theme-name/scripts/um-modifications.js`
        array('jquery'),                              // Dependant Array
        '1.0',                                          // Script Version ( Arbitrary )
        true                                            // Enqueue in Footer
    );

    wp_localize_script(
        'woo_account_tab',
        'ajax_url',
        $ajax_url
    );

//    wp_register_script( 'woo_account_tab', plugins_url('woo-account-tab.js',__FILE__ ));
    wp_enqueue_script('woo_account_tab');

}

$page_name = trim($_SERVER["REQUEST_URI"], '/');
if (strpos($page_name, 'extra-config') > 0) {
    add_action('init', 'woo_account_tab');
}

/**
 * AJAX Callback
 * Always Echos and Exits
 */
function um_modifications_callback()
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

add_action('wp_ajax_nopriv_um_cb', 'um_modifications_callback');
add_action('wp_ajax_um_cb', 'um_modifications_callback');

/**
 * @package My Custom
 */
class My_Custom_Widget extends WP_Widget
{

    public $id;
    public $cache_id;

    function __construct()
    {

        parent::__construct(
            'my_custom_widget',
            __('Custom Widget'),
            array('description' => __('Show a list of values'))
        );

        $this->cache_id = 'my_custom_transient_id_' . get_current_user_id();

        if (is_active_widget(false, false, $this->id_base)) {
            add_action('wp_head', array($this, 'css'));
        }
    }

    function css()
    {
        ?>

        <style type="text/css">

        </style>

        <?php
    }

    function form($instance)
    {
        if ($instance && isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Spam Blocked');
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>

        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function widget($args, $instance)
    {
        $user_id = get_current_user_id();
        $meta_var = get_user_meta($user_id, 'woo_account_search', true);

        // get cache if exist
        $code = get_transient($this->cache_id);

        if (!isset($instance['title'])) {
            $instance['title'] = __('List of Values', 'akismet');
        }

        if (!empty($instance['title'])) {
            echo $args['before_title'];
            echo esc_html($instance['title']);
            echo $args['after_title'];
        }

        if ($code === false) {


            $result_array = array();
            if (count($meta_var)) {
                $args = array(
                    'body' => array('terms' => $meta_var),
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

                    $code = '<div class="a-stats"><ul class="example-transient">';

                    if (count($result_array)) {
                        foreach ($result_array as $k => $v) {
                            $code .= '<li>' . $v . '</li>';
                        }
                    } else {
                        $code .= '<li>' . __('No items') . '</li>';
                    }
                    $code .= '</ul></div>';
                }
            }

            // set html cache
            set_transient($this->cache_id, $code, YEAR_IN_SECONDS);
        }
        echo $args['before_widget'];
        echo $code;
        echo $args['after_widget'];
    }
}

function my_custom_register_widgets()
{
    register_widget('My_Custom_Widget');
}

add_action('widgets_init', 'my_custom_register_widgets');

?>
