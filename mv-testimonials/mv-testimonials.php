<?php

/**
* Plugin Name: MV Testimonials
* Plugin URI: https://www.wordpress.org/mv-testimonials
* Description: My plugin's description
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Marcelo Vieira
* Author URI: https://www.codigowp.net
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: mv-testimonials
* Domain Path: /languages
*/
/*
MV Testimonials is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
MV Testimonials is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with MV Testimonials. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'MV_Testimonials' ) ){

    class MV_Testimonials{

        public function __construct() {

            $this->load_textdomain();

            // Define constants used througout the plugin
            $this->define_constants(); 
            
            require_once( MV_TESTIMONIALS_PATH . 'post-types/class.mv-testimonials-cpt.php' );
            $MVTestimonialsPostType = new MV_Testimonials_Post_Type();

            require_once( MV_TESTIMONIALS_PATH . 'widgets/class.mv-testimonials-widget.php' );
            $MVTestimonialsWidget = new MV_Testimonials_Widget();   
            
            add_filter( 'archive_template', array( $this, 'load_custom_archive_template' ) );
            add_filter( 'single_template', array( $this, 'load_custom_single_template' ) );
        }

         /**
         * Define Constants
         */
        public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
            define ( 'MV_TESTIMONIALS_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'MV_TESTIMONIALS_URL', plugin_dir_url( __FILE__ ) );
            define ( 'MV_TESTIMONIALS_VERSION', '1.0.0' );  
            define ( 'MV_TESTIMONIALS_OVERRIDE_PATH_DIR', get_stylesheet_directory() . '/mv-testimonials/' );   
        }

        public function load_custom_archive_template( $tpl ){
            if( current_theme_supports( 'mv-testimonials' ) ){
                if( is_post_type_archive( 'mv-testimonials' ) ){
                    $tpl = $this->get_template_part_location( 'archive-mv-testimonials.php' );
                }
            }
            return $tpl;
        }

        public function load_custom_single_template( $tpl ){
            if( current_theme_supports( 'mv-testimonials' ) ){
                if( is_singular( 'mv-testimonials' ) ){
                    $tpl = $this->get_template_part_location( 'single-mv-testimonials.php' );
                }
            }
            return $tpl;
        }

        public function get_template_part_location( $file ){
            if( file_exists( MV_TESTIMONIALS_OVERRIDE_PATH_DIR . $file ) ){
                $file = MV_TESTIMONIALS_OVERRIDE_PATH_DIR . $file;
            }else{
                $file = MV_TESTIMONIALS_PATH . 'views/templates/' . $file;
            }
            return $file;
        }

        public function load_textdomain(){
            load_plugin_textdomain(
                'mv-testimonials',
                false,
                dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            );
        }

        /**
         * Activate the plugin
         */
        public static function activate(){
            update_option('rewrite_rules', '' );
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            unregister_post_type( 'mv-testimonials' );
            flush_rewrite_rules();
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall(){

            delete_option( 'widget_mv-testimonials' );

            $posts = get_posts(
                array(
                    'post_type' => 'mv-testimonials',
                    'number_posts'  => -1,
                    'post_status'   => 'any'
                )
            );

            foreach( $posts as $post ){
                wp_delete_post( $post->ID, true );
            }
        }

    }
}

if( class_exists( 'MV_Testimonials' ) ){
    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'MV_Testimonials', 'activate'));
    register_deactivation_hook( __FILE__, array( 'MV_Testimonials', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'MV_Testimonials', 'uninstall' ) );

    $mv_testimonials = new MV_Testimonials();
}