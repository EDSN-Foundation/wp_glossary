<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPG_CustomThumbnail
 * 
 * Class for creating custom meta boxes. 
 */
class WPG_CustomThumbnail {

	/**
	 * Initialization of class.
	 */
	public function __construct() {
		require 'class-wpg-custom-thumbnail-metabox.php';
		require 'class-wpg-custom-thumbanil-shortcode.php';

		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
			// Add the meta boxes
			add_action( 'add_meta_boxes', array( __CLASS__, 'register_metaboxes' ) );
			// Enqueue optionals styles & js
			add_action( 'admin_enqueue_scripts', function ( $hook ) {
				wp_enqueue_script( 'wpg_thumb_meta_scripts', plugin_dir_url( __FILE__ ) . '../assets/js/custom.thumbnail.scripts.js' );			
			} );
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
				__( 'Custom Thumbnail', WPG_TEXT_DOMAIN ), // Visible title
				function ( $post, $args ) { // Anonymous function to include metabox template
					include plugin_dir_path( __FILE__ ).'../templates/custom-thumbnail-metabox-template.php';
				},
				"glossary",         // The slug of the post type you want to add this meta box to.
				'side',             // Context. Where on the screen should this show up? Options: 'normal', 'advanced', or 'side'
				'default'           // Priority. Options: 'high', 'core', 'default' or 'low'
			);
        }
		// Loop through the post types and add meta box to each
		
	}
	
}

new WPG_CustomThumbnail();