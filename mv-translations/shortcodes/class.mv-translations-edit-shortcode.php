<?php 

if( ! class_exists('MV_Translations_Edit_Shortcode')){
    class MV_Translations_Edit_Shortcode{
        public function __construct(){
            add_shortcode( 'mv_translations_edit', array( $this, 'add_shortcode' ) );
        }

        public function add_shortcode(){
            
            ob_start();
            require( MV_TRANSLATIONS_PATH . 'views/mv-translations_edit_shortcode.php' );
            wp_enqueue_script( 'custom_js' );
            wp_enqueue_script( 'validate_js' );
            return ob_get_clean();
        }
    }
}
