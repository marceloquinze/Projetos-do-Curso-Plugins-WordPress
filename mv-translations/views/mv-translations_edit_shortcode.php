<?php

if( ! is_user_logged_in() ){
    mvt_register_user();
    return;
}

if( isset( $_POST['mv_translations_nonce'] ) ){
    if( ! wp_verify_nonce( $_POST['mv_translations_nonce'], 'mv_translations_nonce' ) ){
        return;
    }
}

$errors = array();
$hasError = false;

if( isset( $_POST['submitted'])){
    $title              = $_POST['mv_translations_title'];
    $content            = $_POST['mv_translations_content'];
    $singer             = $_POST['mv_translations_singer'];
    $transliteration    = $_POST['mv_translations_transliteration'];
    $video              = $_POST['mv_translations_video_url'];

    if( trim( $title ) === '' ){
        $errors[] = esc_html__( 'Please, enter a title', 'mv-translations' );
        $hasError = true;
    }

    if( trim( $content ) === '' ){
        $errors[] = esc_html__( 'Please, enter some content', 'mv-translations' );
        $hasError = true;
    }

    if( trim( $singer ) === '' ){
        $errors[] = esc_html__( 'Please, enter some singer', 'mv-translations' );
        $hasError = true;
    }

    if( $hasError === false ){
        $post_info = array(
            'post_type' => 'mv-translations',
            'post_title'    => sanitize_text_field( $title ),
            'post_content'  => wp_kses_post( $content ),
            'tax_input' => array(
                'singers'   => sanitize_text_field( $singer )
            ),
            'ID'    => $_GET['post']
        );

        $post_id = wp_update_post( $post_info );

        global $post;
        MV_Translations_Post_Type::save_post( $post_id, $post );        
    }

}

global $current_user;
global $wpdb; 
$q = $wpdb->prepare(
    "SELECT ID, post_author, post_title, post_content, meta_key, meta_value
    FROM $wpdb->posts AS p
    INNER JOIN $wpdb->translationmeta AS tm
    ON p.ID = tm.translation_id
    WHERE p.ID = %d
    AND p.post_author = %d
    ORDER BY p.post_date DESC",
    $_GET['post'],
    $current_user->ID
);
$results = $wpdb->get_results( $q, ARRAY_A );
if( current_user_can( 'edit_post', $_GET['post'] )):
?>
<div class="mv-translations">
    <form action="" method="POST" id="translations-form">
        <h2><?php esc_html_e( 'Edit translation' , 'mv-translations' ); ?></h2>

        <?php 
            if( $errors != '' ){
                foreach( $errors as $error ){
                    ?>
                        <span class="error">
                            <?php echo $error; ?>
                        </span>
                    <?php
                }
            }
        ?>
        
        <label for="mv_translations_title"><?php esc_html_e( 'Title', 'mv-translations' ); ?> *</label>
        <input type="text" name="mv_translations_title" id="mv_translations_title" value="<?php echo esc_html( $results[0]['post_title'] ); ?>" required />
        <br />
        <label for="mv_translations_singer"><?php esc_html_e( 'Singer', 'mv-translations' ); ?> *</label>
        <input type="text" name="mv_translations_singer" id="mv_translations_singer" value="<?php echo strip_tags( get_the_term_list( $_GET['post'], 'singers', '', ', ' ) ); ?>" required />

        <br />
        <?php 
            wp_editor( $results[0]['post_content'], 'mv_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) );
        ?>
        </br />
        
        <fieldset id="additional-fields">
            <label for="mv_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'mv-translations' ); ?></label>
            <select name="mv_translations_transliteration" id="mv_translations_transliteration">
                <option value="Yes" <?php selected( $results[0]['meta_value'], "Yes" ); ?>><?php esc_html_e( 'Yes', 'mv-translations' ); ?></option>
                <option value="No" <?php selected( $results[0]['meta_value'], "No" ); ?>><?php esc_html_e( 'No', 'mv-translations' ); ?></option>
            </select>
            <label for="mv_translations_video_url"><?php esc_html_e( 'Video URL', 'mv-translations' ); ?></label>
            <input type="url" name="mv_translations_video_url" id="mv_translations_video_url" value="<?php echo $results[1]['meta_value']; ?>" />
        </fieldset>
        <br />
        <input type="hidden" name="mv_translations_action" value="update">
        <input type="hidden" name="action" value="editpost">
        <input type="hidden" name="mv_translations_nonce" value="<?php echo wp_create_nonce( 'mv_translations_nonce' ); ?>">
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="submit" name="submit_form" value="<?php esc_attr_e( 'Submit', 'mv-translations' ); ?>" />
    </form>
    <br>
    <a href="<?php echo esc_url( home_url( '/submit-translation' ) ); ?>"><?php esc_html_e( 'Back to translations list', 'mv-translations' ); ?></a>
</div>
<?php endif; ?>
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>