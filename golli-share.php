<?php

/**
 * Plugin Name: Social Network Links by gol.li
 * Plugin URI: https://gol.li/
 * Description: Widget for linking social media icons to your profiles through service gol.li
 * Version: 1.0
 * Author: David Neustadt
 * Author URI: https://neustadt.dev/
 **/

function dne_gollishare_load_widget()
{
    register_widget( 'dne_gollishare' );
}
add_action('widgets_init', 'dne_gollishare_load_widget');

class dne_gollishare extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'dne_gollishare',
            __('Social Network Links by gol.li', 'dne_gollishare_domain'),
            [
                'description' => __( 'Widget for linking social media icons to your profiles through service gol.li', 'dne_gollishare_domain' ),
            ]
        );

        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_footer-widgets.php', [$this, 'print_scripts'], 9999);
    }

    public function widget($args, $instance)
    {
        $username = apply_filters('widget_username', $instance['username']);
        $height = (int) apply_filters('widget_height', $instance['height']);
        $width = (int) apply_filters('widget_width', $instance['width']);
        $bg_color = substr(apply_filters('widget_bgcolor', $instance['bgcolor']), 1);
        $border_color = substr(apply_filters('widget_bordercolor', $instance['bordercolor']), 1);

        echo $args['before_widget'];

        echo __(
            sprintf(
                '<iframe src="https://gol.li/%s/share?%s%s"
                style="width: 100%%;
                       max-width: %spx;
                       height: %spx;
                       border: 1px solid %s;"></iframe>',
                $username,
                (!empty($bg_color) ? "bg=$bg_color&" : ''),
                (!empty($border_color) ? "b=$border_color" : ''),
                $width,
                $height,
                (!empty($border_color) ? "#$border_color" : '#ccc')
            ),
            'dne_gollishare_domain'
        );

        echo $args['after_widget'];
    }

    public function enqueue_scripts($hook_suffix)
    {
        if ('widgets.php' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('underscore');
    }

    public function print_scripts()
    {
        ?>
        <script>
            (function($){
                function initColorPicker(widget) {
                    widget.find('.color-picker').wpColorPicker( {
                        change: _.throttle(function() {
                            $(this).trigger('change');
                        }, 3000)
                    });
                }

                function onFormUpdate(event, widget) {
                    initColorPicker(widget);
                }

                $(document).on('widget-added widget-updated', onFormUpdate);

                $(document).ready(function() {
                    $('#widgets-right .widget:has(.color-picker)').each(function () {
                        initColorPicker($(this));
                    });
                } );
            }(jQuery));
        </script>
        <?php
    }

    public function form($instance)
    {
        $username = isset($instance['username']) ? $instance['username'] : '';
        $height = isset($instance['height']) ? $instance['height'] : '80';
        $width = isset($instance['width']) ? $instance['width'] : '230';
        $bg_color = isset($instance['bgcolor']) ? $instance['bgcolor'] : '';
        $border_color = isset($instance['bordercolor']) ? $instance['bordercolor'] : '';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>">
                <?php _e('gol.li Username:'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('username'); ?>"
                   name="<?php echo $this->get_field_name('username'); ?>"
                   type="text"
                   value="<?php echo esc_attr($username); ?>"
                   required />
            <small>
                <?php _e('Enter your gol.li username. You can create a free account at '); ?>
                <a href="https://gol.li" target="_blank">https://gol.li</a>
            </small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>">
                <?php _e('Widget Height:'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('height'); ?>"
                   name="<?php echo $this->get_field_name('height'); ?>"
                   type="number"
                   value="<?php echo esc_attr($height); ?>"
                   required />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>">
                <?php _e('Widget Width:'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('width'); ?>"
                   name="<?php echo $this->get_field_name('width'); ?>"
                   type="number"
                   value="<?php echo esc_attr($width); ?>"
                   required />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('bgcolor'); ?>">
                <?php _e('Widget Background Color:'); ?>
            </label>
            <input class="color-picker widefat"
                   id="<?php echo $this->get_field_id('bgcolor'); ?>"
                   name="<?php echo $this->get_field_name('bgcolor'); ?>"
                   type="text"
                   value="<?php echo esc_attr($bg_color); ?>" />
            <small><?php _e('(Optional) Background color of the widget'); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('bordercolor'); ?>">
                <?php _e('Widget Border Color:'); ?>
            </label>
            <input class="color-picker widefat"
                   id="<?php echo $this->get_field_id('bordercolor'); ?>"
                   name="<?php echo $this->get_field_name('bordercolor'); ?>"
                   type="text"
                   value="<?php echo esc_attr($border_color); ?>" />
            <small><?php _e('(Optional) Border color of the widget'); ?></small>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['username'] = !empty($new_instance['username']) ? strip_tags($new_instance['username']) : '';
        $instance['height'] = !empty($new_instance['height']) ? intval($new_instance['height']) : 80;
        $instance['width'] = !empty($new_instance['width']) ? intval($new_instance['width']) : 230;
        $instance['bgcolor'] = !empty($new_instance['bgcolor']) ? strip_tags($new_instance['bgcolor']) : '';
        $instance['bordercolor'] = !empty($new_instance['bordercolor']) ? strip_tags($new_instance['bordercolor']) : '';

        return $instance;
    }
}