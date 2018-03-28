<?php

class Woo_Account_Tab_Foo_Custom_Widget extends WP_Widget
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