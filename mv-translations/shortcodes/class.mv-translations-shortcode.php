<?php 

if( ! class_exists('MV_Translations_Shortcode')){
    class MV_Translations_Shortcode{
        public function __construct(){
            add_shortcode( 'mv_translations', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode(){
            
            ob_start();
            require( MV_TRANSLATIONS_PATH . 'views/mv-translations_shortcode.php' );
            wp_enqueue_script( 'custom_js' );
            wp_enqueue_script( 'validate_js' );
            return ob_get_clean();
        }
    }
}
