<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Class WPCTN_CustomThumbnailShortcode
 *
 * Used to create a shortcode for Custom Thumbnails
 * 
 * The `img_size` parameter accepts either an array or a string. The supported string
 * values are 'thumb' or 'thumbnail' for the given thumbnail size or defaults at
 * 128 width and 96 height in pixels. Also supported for the string value is
 * 'medium', 'medium_large' and 'full'. The 'full' isn't actually supported, but any value other
 * than the supported will result in the content_width size or 500 if that is
 * not set.
 * 
 * Configuration
 *		Shrotcode
 *			wpctn_thumbnail
 * 		Parameters 
 * 			title:String - e.g.:"Anything"
 *          img_size:String - e.g.: "thumbnail"|"medium"|"medium_large"|"full"
 * 			style:String - e.g.:"width:75px;height:80px"
 *
 * E.g.:
 * 	SHORTCODE - [wpctn_thumbnail title="Anything" style="width:75px;height:80px" img_size="thumbnail"]
 *              [wpctn_thumbnail title="Anything" style="width:500px;height:500px" img_size="full"]
 *  GENERATED - <img src="..." alt="Anything" style="width:75px;height:80px" />';
 *              <img src="..." alt="Anything" style="width:500px;height:500px" />';
 */
class WPCTN_CustomThumbnailShortcode {

	/**
	 * Initialization of class.
	 */
	public function __construct() {
		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
			add_shortcode( 'wpctn_thumbnail', array( __CLASS__, 'wpctn_thumbnail_shortcode' ) );
		}		
	}
		
	/**
	 * Widget Call Back Function
	 * 
	 * 
	 */
	public static function wpctn_thumbnail_shortcode( $args ) {
		global $post;
		$img_size = 'thumbnail';
		if(isset($args['img_size'])){
		    $args['img_size'] = trim($args['img_size']);
		    if(!empty($args['img_size'])){
		        $img_size = $args['img_size'];
		    }
		}
		// See if there's a media id already saved as post meta
		$wp_glossary_img_id = get_post_meta( $post->ID, 'wp_glossary_custom_thumbnail', true );
		// Get the image src
		$wp_glossary_img_src = wp_get_attachment_image_src( $wp_glossary_img_id,  $img_size);
		// For convenience, see if the array is valid
		$has_img = is_array( $wp_glossary_img_src );
		
		$img_container = '';
		if ( $has_img ){
			$img_container .= '<img src="'.$wp_glossary_img_src[0].'" alt="'.$args['title'].'" style="'.$args['style'].'" />';				
		}
		
		return $img_container;
		
	}
}