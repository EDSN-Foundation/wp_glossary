<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class WPCTN_Custom_Thumbnail_MetaBox
 *
 * Used to automatically validation, and saving.
 *
 */
class WPCTN_Custom_Thumbnail_MetaBox {
	/**
	 * Initialization of class.
	 */
	public function __construct() {
		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
		    // Add the meta boxes
		    add_action( 'add_meta_boxes', array( __CLASS__, 'register_metaboxes' ) );
		    // Enqueue optionals styles & js
		    add_action( 'admin_enqueue_scripts', function ( $hook ) {
		        wp_enqueue_script( 'wpg_thumb_meta_scripts', WPCTN_PLUGIN_URL . '/assets/js/custom.thumbnail.scripts.js' );
		    } );
			// Use standard hooks
			add_action( 'save_post', array( __CLASS__, 'save_metaboxes' ) );
		}
	}
	
	/**
	 * REGISTER YOUR METABOXES
	 *
	 * http://codex.wordpress.org/Function_Reference/add_meta_box
	 */
	//TODO - Study for replace this template for the WP function call _wp_post_thumbnail_html in /wp-admin/includes/post.php
	public static function register_metaboxes() {
	    //Adding Thumbnail Metabox just for "glossary" types.
	    if( get_post_type() == "glossary"){
	        add_meta_box(
	            'wpg-thumbnail-metabox-id',  // HTML slug for box id
	            __( 'Custom Thumbnail', WPCTN_TEXT_DOMAIN ), // Visible title
	            function ( $post, $args ) { // Anonymous function to include metabox template
	                include WPCTN_PLUGIN_PATH . '/templates/custom-thumbnail-metabox-template.php';
	            },
	            "glossary",         // The slug of the post type you want to add this meta box to.
	            'side',             // Context. Where on the screen should this show up? Options: 'normal', 'advanced', or 'side'
	            'default'           // Priority. Options: 'high', 'core', 'default' or 'low'
	            );
	    }
	    // Loop through the post types and add meta box to each
	    
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