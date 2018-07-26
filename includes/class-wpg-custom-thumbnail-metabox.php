<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class WPG_CustomThumbnailMetaBox
 *
 * Used to automatically validation, and saving.
 *
 */
class WPG_CustomThumbnailMetaBox {
	/**
	 * Initialization of class.
	 */
	public function __construct() {
		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
			// Use standard hooks
			add_action( 'save_post', array( __CLASS__, 'save_metaboxes' ) );
		}
	}
		
	/**
	 * Automagically save every registered setting/field
	 */
	public static function save_metaboxes( $post_id ) {
        if ( self:: verify_save('wp_glossary_img_nonce', 'wpg-thumbnail-meta-box', $post_id ) ) {
            $img_ID = isset( $_POST[ 'custom-img-id' ] ) ?  $_POST[ 'custom-img-id' ] : '';             
            // Update the meta field in the database.
            update_post_meta( $post_id, 'wp_glossary_custom_thumbnail', $img_ID );    
        }
        else{
            echo "NOTHUMBNAIL";
        }
        
	}
	/**
	 * Performs meta box save validation
	 *
	 * @param $nonce_name
	 * @param $none_action
	 * @param $post_id
	 *
	 * @return bool
	 */
	public static function verify_save( $nonce_name, $none_action, $post_id ) {
		// VALIDATE NONCE & AUTOSAVE
		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return false;
		}
		if ( ! wp_verify_nonce( $_POST[ $nonce_name ], $none_action ) ) {
			return false;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		// VALIDATE PERMISSIONS
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return false;
		}
		// EVERYTHING CHECKS OUT
		return true;
	}
}
new WPG_CustomThumbnailMetaBox();