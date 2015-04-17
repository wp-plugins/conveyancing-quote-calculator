<?php

/**
 * Plugin Name: Conveyancing Quote Calculator
 * Description: Add a conveyancing quote calculator to your website allowing your visitors to get <strong>instant fee estimates</strong> on screen and have their conveyancing quote automatically emailed to them as well. You will be alerted via email to any quotes which have been sent to prospective clients and all the clients information will be stored in <a href="http://www.hoowla.com/">Hoowla's online conveyancing platform</a>.
 * Plugin URI:  http://blog.hoowla.com/setup-conveyancing-quote-calculator-hoowla/
 * Version:     1.0.2
 * Author:      Hoowla Ltd.
 * Author URI:  http://www.hoowla.com/
 * License:     COPYRIGHT 2014 Hoowla Ltd.
 * License URI: https://www.hoowla.com/terms.php
 */

include_once 'widget.php';

const HOOWLA_PLUGIN_NAME = 'hoowla_plugin';
const HOOWLA_PLUGIN_FILENAME = 'hoowla_plugin.php';
const HOOWLA_OPTIONS_NAME = 'hoowla_plugin';


/* ==========================================================================
    Add Settings link to Plugin List
   ========================================================================== */

if (!function_exists('plugin_add_settings_link')) {

    function plugin_add_settings_link($links) {
        array_push($links, '<a href="options-general.php?page=' . HOOWLA_PLUGIN_FILENAME . '">Settings</a>');
        return $links;
    }
    add_filter("plugin_action_links_" . plugin_basename( __FILE__ ), 'plugin_add_settings_link');

}



/* ==========================================================================
    Add CDN Script to Footer
   ========================================================================== */

if (!function_exists('hoowla_add_footer_code')) {

    function hoowla_add_footer_code() {
        if (!is_admin()) {
            echo "<script>!function() { var s = document.createElement('script'); s.type='text/javascript'; s.async=true; s.src = 'http://cdn.hoowla.com/hoowla-quote-plugin-min.js'; var e = document.getElementsByTagName('script')[0]; e.parentNode.insertBefore(s, e); }();</script>";
        }
    }
    add_action('wp_footer', 'hoowla_add_footer_code');

}



/* ==========================================================================
     Register the widget button.
   ========================================================================== */

if (!function_exists('register_hoowla_widget')) {

    function register_hoowla_widget() {
        register_widget( 'HoowlaPlugin\Hoowla_Plugin_Widget' );
    }
    add_action( 'widgets_init', 'register_hoowla_widget' );

}



/* ==========================================================================
     Print out the Button <div />
   ========================================================================== */

if (!function_exists('print_hoowla_div')) {

    function print_hoowla_div($id, $text, $color, $size = '') {
        if (is_numeric($id) && strlen($id) == 7) {
            echo '<div class="hoowla-quote" data-id="' . $id . '" data-text="' . $text . '" data-color="' . $color . '" data-size="' . $size . '"></div>';
        }
    }

}



/* ==========================================================================
     Hoowla Button Shortcode
   ========================================================================== */

if (!function_exists('hoowla_shortcode')) {

    function hoowla_shortcode($attrs) {
        extract( 
            shortcode_atts( 
                array(
                    'id' => '',
                    'text' => '',
                    'color' => '',
                    'colour' => '',
                    'size' => '',
                ), 
                $attrs
            ) 
        );

        // Allow either spelling of colour.
        if (!empty($colour) && empty($color)) {
            $color = $colour;
        }

        // Check on $attrs here make them required fields.
        // ID is required.
        print_hoowla_div($id, $text, $color, $size);
    }
    add_shortcode( 'hoowlabutton', 'hoowla_shortcode');

}



/* ==========================================================================
     Add button to TinyMCE Editor
   ========================================================================== */

// Add JS file for TinyMCE.
function hoowla_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['hoowla_test'] = plugins_url( '/editor.js', __FILE__ );
    return $plugin_array;
}

// Add the Button key.
function hoowla_add_tinymce_button( $buttons ) {
    array_push($buttons, 'hoowla_test_button_key');
    return $buttons;
}

function hoowla_add_tinymce() {
    global $typenow;

    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return ;

    add_filter( 'mce_external_plugins', 'hoowla_add_tinymce_plugin' );
    add_filter( 'mce_buttons', 'hoowla_add_tinymce_button' );
}
add_action( 'admin_head', 'hoowla_add_tinymce' );



/* ==========================================================================
     Admin Page
   ========================================================================== */

function hoowla_admin_menu() {
    add_options_page( 'Quote Plugin', 'Quote Plugin', 'manage_options', HOOWLA_PLUGIN_FILENAME, 'hoowla_admin_page');  
}
add_action('admin_menu', 'hoowla_admin_menu');


function hoowla_admin_page() {
    // Inc. JS scripts.
    // farbtastic = in-built color picker
    wp_enqueue_style('farbtastic');
    wp_enqueue_script('farbtastic');
    wp_enqueue_script('my-theme-options', get_template_directory_uri() . '/js/theme-options.js', array( 'farbtastic', 'jquery' ));

    wp_enqueue_script('hoowla-admin-js', plugins_url( '/hoowla-admin.js', __FILE__ ), array());

?>

    <div class="section panel">
      <h1>Conveyancing Quote Calculator Settings Page</h1><br />

      <p>Don't have a Hoowla account? You can easily <a href="https://www.hoowla.com/app/signup">create one</a> to get your form setup.</p>

      <p>Are you a Conveyancer, Solicitor, Estate Agent, Mortgage Broker or simply doing some web work for them and want to find out more about our Conveyancing Platform? Get in touch (hello@hoowla.com) and we'd be happy to answer any of your questions or help you setup the conveyancing calculator.</p><br />

      <form method="post" enctype="multipart/form-data" action="options.php">
        <?php 
            settings_fields(HOOWLA_OPTIONS_NAME); 
            do_settings_sections(HOOWLA_PLUGIN_FILENAME);
        ?>
        <p class="submit">
            <br /> 
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
        </p>
      </form>
    </div>

    <?php
}


function hoowla_register_settings() {

    // Register the settings with Validation callback
    register_setting(HOOWLA_OPTIONS_NAME, HOOWLA_OPTIONS_NAME, 'hoowla_validate_settings');

    // Add settings section
    add_settings_section('hoowla_text_section', 'Default Settings', 'hoowla_blank', HOOWLA_PLUGIN_FILENAME);

    // Default ID
    $field_args = array(
      'type'      => 'text',
      'id'        => 'hoowla_default_id',
      'name'      => 'hoowla_default_id',
      'desc'      => 'The ID used by default for submitting to Hoowla.<br />Should be a 7 digit number.',
      'std'       => '',
      'label_for' => 'hoowla_default_id',
      'class'     => 'css_class'
    );
    add_settings_field('hoowla_default_id', 'Quote Form ID', 'hoowla_display_input', HOOWLA_PLUGIN_FILENAME, 'hoowla_text_section', $field_args );

    // Default Text
    $field_args = array(
      'type'      => 'text',
      'id'        => 'hoowla_default_text',
      'name'      => 'hoowla_default_text',
      'desc'      => 'The text which will be displayed on the button.',
      'std'       => '',
      'label_for' => 'hoowla_default_text',
      'class'     => 'css_class'
    );
    add_settings_field('hoowla_default_text', 'Button Text', 'hoowla_display_input', HOOWLA_PLUGIN_FILENAME, 'hoowla_text_section', $field_args );

    // Default Colour
    $field_args = array(
      'type'      => 'color',
      'id'        => 'hoowla_default_color',
      'name'      => 'hoowla_default_color',
      'desc'      => 'The colour of the button.',
      'std'       => '',
      'label_for' => 'hoowla_default_color',
      'class'     => 'css_class color-picker'
    );
    add_settings_field('hoowla_default_color', 'Button Colour', 'hoowla_display_input', HOOWLA_PLUGIN_FILENAME, 'hoowla_text_section', $field_args );

    // Default Size
    $field_args = array(
      'type'      => 'checkbox',
      'id'        => 'hoowla_default_size',
      'name'      => 'hoowla_default_size',
      'desc'      => 'The size of the button leave blank or type \'large\'.',
      'std'       => '',
      'label_for' => 'hoowla_default_size',
      'class'     => 'css_class'
    );
    add_settings_field('hoowla_default_size', 'Button Size', 'hoowla_display_input', HOOWLA_PLUGIN_FILENAME, 'hoowla_text_section', $field_args );
}
add_action('admin_init', 'hoowla_register_settings');


function hoowla_blank() {
    // Needs to be used (and not be blank).
    // null just don' cut it no more (doesn't work in Wordpress 3.4)
    // called in add_settings_section().
}


function hoowla_display_input($args) { 

    extract( $args );
    $options = get_option(HOOWLA_OPTIONS_NAME);

    switch ( $type ) {  
        case 'text':
            // Text Input (ID & Button Text)
            $options[$id] = stripslashes($options[$id]);
            $options[$id] = esc_attr( $options[$id]);
            echo "<input class='regular-text $class' type='text' id='$id' name='" . HOOWLA_OPTIONS_NAME . "[$id]' value='$options[$id]' />";
            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
            break;
        case 'color':
            // Colour Input (Button Colour)
            $options[$id] = stripslashes($options[$id]);  
            $options[$id] = esc_attr( $options[$id]);  
            echo "<input class='regular-text $class' type='hidden' id='$id' name='" . HOOWLA_OPTIONS_NAME . "[$id]' value='$options[$id]' />";  
            echo    "<div class=\"color-picker\" style=\"position: relative;\">";
            echo    "<div style=\"\" id=\"colorpicker\"></div>";
            echo "</div>";
            break;
        case 'checkbox':
            // Checkbox (IsLarge?)
            if (isset($options[$id])) {
                $options[$id] = stripslashes($options[$id]);  
                $options[$id] = esc_attr( $options[$id]);
            }
            echo "<input type='checkbox' id='$id' name='" . HOOWLA_OPTIONS_NAME . "[$id]' " . checked( isset( $options[$id] ), true, false ) . " /><label for='$id'>Extra Large Button?</label>";  
            break;
        default:
            break;
    }
}


function hoowla_validate_settings($input) {

    foreach($input as $k => $v) {
        $newinput[$k] = trim($v);

        // Check the input is a letter or a number
        if(!preg_match('/^[#A-Z0-9 _]*$/i', $v)) {
            $newinput[$k] = '';
        }

    }

    return $newinput;
}


