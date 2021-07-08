<?php

/**
 * Plugin Name: MV Slider
 * Plugin URI: https://www.wordpress.org/mv-slider
 * Description: My plugin's description
 * Version: 1.0
 * Requires at least: 5.6
 * Author: Marcelo Vieira
 * Author URI: https://www.codigowp.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mv-slider
 * Domain Path: /languages
 */

 /*
MV Slider is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
MV Slider is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with MV Slider. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if( ! defined( 'ABSPATH') ){
    exit;
}

if( ! class_exists( 'MV_Slider' ) ){
    class MV_Slider{
        function __construct(){
            $this->define_constants();

            $this->load_textdomain();

            require_once( MV_SLIDER_PATH . 'functions/functions.php' );

            add_action( 'admin_menu', array( $this, 'add_menu' ) );

            require_once( MV_SLIDER_PATH . 'post-types/class.mv-slider-cpt.php' );
            $MV_Slider_Post_Type = new MV_Slider_Post_Type();

            require_once( MV_SLIDER_PATH . 'class.mv-slider-settings.php' );
            $MV_Slider_Settings = new MV_Slider_Settings();

            require_once( MV_SLIDER_PATH . 'shortcodes/class.mv-slider-shortcode.php' );
            $MV_Slider_Shortcode = new MV_Slider_Shortcode();

            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 999 );
            add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts') );
        }

        public function define_constants(){
            define( 'MV_SLIDER_PATH', plugin_dir_path( __FILE__ ) );
            define( 'MV_SLIDER_URL', plugin_dir_url( __FILE__ ) );
            define( 'MV_SLIDER_VERSION', '1.0.0' );
        }

        public static function activate(){
            update_option( 'rewrite_rules', '' );
        }

        public static function deactivate(){
            flush_rewrite_rules();
            unregister_post_type( 'mv-slider' );
        }

        public static function uninstall(){

            delete_option( 'mv_slider_options' );

            $posts = get_posts(
                array(
                    'post_type' => 'mv-slider',
                    'number_posts'  => -1,
                    'post_status'   => 'any'
                )
            );

            foreach( $posts as $post ){
                wp_delete_post( $post->ID, true );
            }
        }

        public function load_textdomain(){
            load_plugin_textdomain(
                'mv-slider',
                false,
                dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            );
        }

        public function add_menu(){
            add_menu_page(
                esc_html__( 'MV Slider Options', 'mv-slider' ),
                'MV Slider',
                'manage_options',
                'mv_slider_admin',
                array( $this, 'mv_slider_settings_page' ),
                'dashicons-images-alt2'
            );

            add_submenu_page(
                'mv_slider_admin',
                esc_html__( 'Manage Slides', 'mv-slider' ),
                esc_html__( 'Manage Slides', 'mv-slider' ),
                'manage_options',
                'edit.php?post_type=mv-slider',
                null,
                null
            );

            add_submenu_page(
                'mv_slider_admin',
                esc_html__( 'Add New Slide', 'mv-slider' ),
                esc_html__( 'Add New Slide', 'mv-slider' ),
                'manage_options',
                'post-new.php?post_type=mv-slider',
                null,
                null
            );

        }

        public function mv_slider_settings_page(){
            if( ! current_user_can( 'manage_options' ) ){
                return;
            }

            if( isset( $_GET['settings-updated'] ) ){
                add_settings_error( 'mv_slider_options', 'mv_slider_message', esc_html__( 'Settings Saved', 'mv-slider' ), 'success' );
            }
            
            settings_errors( 'mv_slider_options' );

            require( MV_SLIDER_PATH . 'views/settings-page.php' );
        }

        public function register_scripts(){
            wp_register_script( 'mv-slider-main-jq', MV_SLIDER_URL . 'vendor/flexslider/jquery.flexslider-min.js', array( 'jquery' ), MV_SLIDER_VERSION, true );
            wp_register_style( 'mv-slider-main-css', MV_SLIDER_URL . 'vendor/flexslider/flexslider.css', array(), MV_SLIDER_VERSION, 'all' );
            wp_register_style( 'mv-slider-style-css', MV_SLIDER_URL . 'assets/css/frontend.css', array(), MV_SLIDER_VERSION, 'all' );
        }

        public function register_admin_scripts(){
            global $typenow;
            if( $typenow == 'mv-slider'){
                wp_enqueue_style( 'mv-slider-admin', MV_SLIDER_URL . 'assets/css/admin.css' );
            }
        }

    }
}

if( class_exists( 'MV_Slider' ) ){
    register_activation_hook( __FILE__, array( 'MV_Slider', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'MV_Slider', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'MV_Slider', 'uninstall' ) );

    $mv_slider = new MV_Slider();
} 
