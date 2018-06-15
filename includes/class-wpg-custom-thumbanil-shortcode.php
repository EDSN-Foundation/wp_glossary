<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Class WPG_CustomThumbnailShortcode
 *
 * Used to create a shortcode for Custom Thumbnails
 * 
 * Configuration
 *		Shrotcode
 *			wpg_thumbnail
 * 		Parameters 
 * 			title:string - e.g.:"Anything"
 * 			style:String - e.g.:"width:75px;height:80px"
 *
 * E.g.:
 * 	SHORTCODE - [wpg_thumbnail title="Anything" style="width:75px;height:80px"]
 *  GENERATED - <img src="..." alt="Anything" style="width:75px;height:80px" />';
 */
class WPG_CustomThumbnailShortcode {

	/**
	 * Initialization of class.
	 */
	public function __construct() {
		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
			add_shortcode( 'wpg_thumbnail', array( __CLASS__, 'wpg_thumbnail_shortcode' ) );
		}		
	}

	/**
	 * Widget Call Back Function
	 */
	public static function wpg_thumbnail_shortcode( $args ) {
		global $post;
		
		// See if there's a media id already saved as post meta
		$wp_glossary_img_id = get_post_meta( $post->ID, 'wp_glossary_custom_thumbnail', true );
		// Get the image src
		$wp_glossary_img_src = wp_get_attachment_image_src( $wp_glossary_img_id, 'full' );
		// For convenience, see if the array is valid
		$have_img = is_array( $wp_glossary_img_src );
		
		if ( $have_img ){
			$img_container .= '<img src="'.$wp_glossary_img_src[0].'" alt="'.$args['title'].'" style="'.$args['style'].'" />';				
		}
		
		return $img_container;
		
	}
}
new WPG_CustomThumbnailShortcode();