<?php
/**
 * Core Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Glossary: Get Posts/Terms
 */
function wpg_glossary_terms() {
	$wpg_glossary_terms = array();
	
	$query_args = array(
		'post_type'			=> 'glossary',
		'posts_per_page'	=> -1,
		'orderby'			=> 'title',
		'order'				=> 'ASC'
	);
	$wpg_glossary_terms = get_posts( apply_filters( 'wpg_glossary_terms_query_args', $query_args ) );
	
	if( ! empty( $wpg_glossary_terms ) ) {
		global $post;
		foreach( $wpg_glossary_terms as $key=>$post ) {
			setup_postdata( $post );
			$glossary_tags = wp_get_post_terms( get_the_ID(), 'glossary_tag', array( 'fields' => 'names' ) );
			$wpg_glossary_terms[ $key ]->terms = $glossary_tags;
			
		}
		wp_reset_postdata();
	}
	
	return apply_filters( 'wpg_glossary_terms', $wpg_glossary_terms );
}

/**
 * Glossary: Get Title
 */
function wpg_glossary_get_title() {
	$wpg_glossary_title = get_option( 'wpg_glossary_title' );
	
	if( $wpg_glossary_title == '' ) {
		$wpg_glossary_title = __( 'Glossary', WPG_TEXT_DOMAIN );
	}
	
	return apply_filters( 'wpg_glossary_title', $wpg_glossary_title );
}

/**
 * Glossary: Get Slug
 */
function wpg_glossary_get_slug() {
	$wpg_glossary_slug = get_option( 'wpg_glossary_slug' );
	
	if( $wpg_glossary_slug == '' ) {
		$wpg_glossary_slug = 'glossary';
	}
	
	return apply_filters( 'wpg_glossary_slug', $wpg_glossary_slug );
}

/**
 * Glossary: Archive?
 */
function wpg_glossary_is_archive() {
	$wpg_glossary_archive = get_option( 'wpg_glossary_archive' );
	
	if( $wpg_glossary_archive == 'no' ) {
		$wpg_glossary_archive = false;
	} else {
		$wpg_glossary_archive = true;
	}
	
	return apply_filters( 'wpg_glossary_is_archive', $wpg_glossary_archive );
}

/**
 * Glossary: Get Page/Post ID
 */
function wpg_glossary_get_page_id() {
	$wpg_glossary_page_id = get_option( 'wpg_glossary_page_id' );
	return apply_filters( 'wpg_glossary_page_id', $wpg_glossary_page_id );
}

/**
 * Glossary: Animation?
 */
function wpg_glossary_is_animation() {
	$wpg_glossary_animation = get_option( 'wpg_glossary_animation' );
	
	if( $wpg_glossary_animation == 'no' ) {
		$wpg_glossary_animation = false;
	} else {
		$wpg_glossary_animation = true;
	}
	
	return apply_filters( 'wpg_glossary_is_animation', $wpg_glossary_animation );
}

/**
 * Glossary: Get Label - ALL
 */
function wpg_glossary_get_label_all() {
	$wpg_glossary_label_all = get_option( 'wpg_glossary_label_all' );
	
	if( $wpg_glossary_label_all == '' ) {
		$wpg_glossary_label_all = __( 'All', WPG_TEXT_DOMAIN );
	}
	
	return apply_filters( 'wpg_glossary_label_all', $wpg_glossary_label_all );
}

/**
 * Glossary: Is Disable Link?
 */
function wpg_glossary_is_disable_link() {
	$wpg_glossary_disable_link = get_option( 'wpg_glossary_disable_link' );
	
	if( $wpg_glossary_disable_link == 'yes' ) {
		$wpg_glossary_disable_link = true;
	} else {
		$wpg_glossary_disable_link = false;
	}
	
	return apply_filters( 'wpg_glossary_is_disable_link', $wpg_glossary_disable_link );
}

/**
 * Glossary: New Tab?
 */
function wpg_glossary_is_new_tab() {
	$wpg_glossary_new_tab = get_option( 'wpg_glossary_new_tab' );
	
	if( $wpg_glossary_new_tab == 'yes' ) {
		$wpg_glossary_new_tab = true;
	} else {
		$wpg_glossary_new_tab = false;
	}
	
	return apply_filters( 'wpg_glossary_is_new_tab', $wpg_glossary_new_tab );
}

/**
 * Glossary: Is Back Link?
 */
function wpg_glossary_is_back_link() {
	$wpg_glossary_back_link = get_option( 'wpg_glossary_back_link' );
	
	if( $wpg_glossary_back_link == 'no' ) {
		$wpg_glossary_back_link = false;
	} else {
		$wpg_glossary_back_link = true;
	}
	
	return apply_filters( 'wpg_glossary_is_back_link', $wpg_glossary_back_link );
}

/**
 * Glossary: Get Label - Back Link
 */
function wpg_glossary_get_label_back_link() {
	$wpg_glossary_label_back_link = get_option( 'wpg_glossary_label_back_link' );
	
	if( $wpg_glossary_label_back_link == '' ) {
		$wpg_glossary_label_back_link = __( 'Back to Glossary Index Page', WPG_TEXT_DOMAIN );
	}
	
	return apply_filters( 'wpg_glossary_label_back_link', $wpg_glossary_label_back_link );
}

/**
 * Tooltip: Is Active?
 */
function wpg_glossary_is_tooltip() {
	$wpg_glossary_activate_tooltip = get_option( 'wpg_glossary_activate_tooltip' );
	
	if( $wpg_glossary_activate_tooltip == 'no' ) {
		$wpg_glossary_activate_tooltip = false;
	} else {
		$wpg_glossary_activate_tooltip = true;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip', $wpg_glossary_activate_tooltip );
}

/**
 * Tooltip: Is Disable on Index Page?
 */
function wpg_glossary_is_tooltip_disable_on_index() {
	$wpg_glossary_disable_tooltip_on_index = get_option( 'wpg_glossary_disable_tooltip_on_index' );
	
	if( $wpg_glossary_disable_tooltip_on_index == 'yes' ) {
		$wpg_glossary_disable_tooltip_on_index = true;
	} else {
		$wpg_glossary_disable_tooltip_on_index = false;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_disable_on_index', $wpg_glossary_disable_tooltip_on_index );
}

/**
 * Tooltip: Get Theme
 */
function wpg_glossary_get_tooltip_theme() {
	$wpg_glossary_tooltip_theme = get_option( 'wpg_glossary_tooltip_theme' );
	
	if( $wpg_glossary_tooltip_theme == '' ) {
		$wpg_glossary_tooltip_theme = 'default';
	}
	
	return apply_filters( 'wpg_glossary_tooltip_theme', $wpg_glossary_tooltip_theme );
}

/**
 * Tooltip: Append Title before Content?
 */
function wpg_glossary_is_tooltip_title() {
	$wpg_glossary_activate_tooltip_title = get_option( 'wpg_glossary_activate_tooltip_title' );
	
	if( $wpg_glossary_activate_tooltip_title == 'no' ) {
		$wpg_glossary_activate_tooltip_title = false;
	} else {
		$wpg_glossary_activate_tooltip_title = true;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_title', $wpg_glossary_activate_tooltip_title );
}

/**
 * Tooltip: Title Format before Content?
 */
function wpg_glossary_tooltip_title_format() {
	$wpg_glossary_tooltip_title_format = get_option( 'wpg_glossary_tooltip_title_format' );
	
	if( $wpg_glossary_tooltip_title_format == '' ) {
		$wpg_glossary_tooltip_title_format = '{TITLE}';
	}
	
	return apply_filters( 'wpg_glossary_tooltip_title_format', $wpg_glossary_tooltip_title_format );
}

/**
 * Tooltip: Get Content Type
 */
function wpg_glossary_get_tooltip_content_type() {
	$wpg_glossary_tooltip_content_type = get_option( 'wpg_glossary_tooltip_content_type' );
	
	if( $wpg_glossary_tooltip_content_type == '' ) {
		$wpg_glossary_tooltip_content_type = 'excerpt';
	}
	
	return apply_filters( 'wpg_glossary_tooltip_content_type', $wpg_glossary_tooltip_content_type );
}

/**
 * Tooltip: Get Content Length
 */
function wpg_glossary_get_tooltip_content_length() {
	$wpg_glossary_tooltip_content_length = get_option( 'wpg_glossary_tooltip_content_length' );
	
	if( $wpg_glossary_tooltip_content_length == '' ) {
		$wpg_glossary_tooltip_content_length = 0;
	}
	
	return apply_filters( 'wpg_glossary_tooltip_content_length', $wpg_glossary_tooltip_content_length );
}

/**
 * Tooltip: Is Content Filter
 */
function wpg_glossary_is_tooltip_content_shortcode() {
	$wpg_glossary_tooltip_content_shortcode = get_option( 'wpg_glossary_tooltip_content_shortcode' );
	
	if( $wpg_glossary_tooltip_content_shortcode == 'no' ) {
		$wpg_glossary_tooltip_content_shortcode = false;
	} else {
		$wpg_glossary_tooltip_content_shortcode = true;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_content_shortcode', $wpg_glossary_tooltip_content_shortcode );
}

/**
 * Tooltip: Is Read More Link
 */
function wpg_glossary_is_tooltip_content_read_more() {
	$wpg_glossary_tooltip_content_read_more = get_option( 'wpg_glossary_tooltip_content_read_more' );
	
	if( $wpg_glossary_tooltip_content_read_more == 'yes' ) {
		$wpg_glossary_tooltip_content_read_more = true;
	} else {
		$wpg_glossary_tooltip_content_read_more = false;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_content_read_more', $wpg_glossary_tooltip_content_read_more );
}

/**
 * Tooltip: Get Label - Read More
 */
function wpg_glossary_get_label_tooltip_content_read_more() {
	$wpg_glossary_label_tooltip_content_read_more = get_option( 'wpg_glossary_label_tooltip_content_read_more' );
	
	if( $wpg_glossary_label_tooltip_content_read_more == '' ) {
		$wpg_glossary_label_tooltip_content_read_more = __( 'Read More', WPG_TEXT_DOMAIN );
	}
	
	return apply_filters( 'wpg_glossary_label_tooltip_content_read_more', $wpg_glossary_label_tooltip_content_read_more );
}

/**
 * Tooltip: Get Animation Type
 */
function wpg_glossary_get_tooltip_animation() {
	$wpg_glossary_tooltip_animation = get_option( 'wpg_glossary_tooltip_animation' );
	
	if( $wpg_glossary_tooltip_animation == '' ) {
		$wpg_glossary_tooltip_animation = 'fade';
	}
	
	return apply_filters( 'wpg_glossary_tooltip_animation', $wpg_glossary_tooltip_animation );
}

/**
 * Tooltip: Get Position
 */
function wpg_glossary_get_tooltip_position() {
	$wpg_glossary_tooltip_position = get_option( 'wpg_glossary_tooltip_position' );
	
	if( $wpg_glossary_tooltip_position == '' ) {
		$wpg_glossary_tooltip_position = 'top';
	}
	
	return apply_filters( 'wpg_glossary_tooltip_position', $wpg_glossary_tooltip_position );
}

/**
 * Tooltip: Is Bubble Arrow?
 */
function wpg_glossary_is_tooltip_arrow() {
	$wpg_glossary_activate_tooltip_arrow = get_option( 'wpg_glossary_activate_tooltip_arrow' );
	
	if( $wpg_glossary_activate_tooltip_arrow == 'no' ) {
		$wpg_glossary_activate_tooltip_arrow = false;
	} else {
		$wpg_glossary_activate_tooltip_arrow = true;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_arrow', $wpg_glossary_activate_tooltip_arrow );
}

/**
 * Tooltip: Get Min Width
 */
function wpg_glossary_get_tooltip_min_width() {
	$wpg_glossary_tooltip_min_width = get_option( 'wpg_glossary_tooltip_min_width' );
	
	if( $wpg_glossary_tooltip_min_width == '' ) {
		$wpg_glossary_tooltip_min_width = 250;
	}
	
	return apply_filters( 'wpg_glossary_tooltip_min_width', $wpg_glossary_tooltip_min_width );
}

/**
 * Tooltip: Get Max Width
 */
function wpg_glossary_get_tooltip_max_width() {
	$wpg_glossary_tooltip_max_width = get_option( 'wpg_glossary_tooltip_max_width' );
	
	if( $wpg_glossary_tooltip_max_width == '' ) {
		$wpg_glossary_tooltip_max_width = 500;
	}
	
	return apply_filters( 'wpg_glossary_tooltip_max_width', $wpg_glossary_tooltip_max_width );
}

/**
 * Tooltip: Get Speed
 */
function wpg_glossary_get_tooltip_speed() {
	$wpg_glossary_tooltip_speed = get_option( 'wpg_glossary_tooltip_speed' );
	
	if( $wpg_glossary_tooltip_speed == '' ) {
		$wpg_glossary_tooltip_speed = 350;
	}
	
	return apply_filters( 'wpg_glossary_tooltip_speed', $wpg_glossary_tooltip_speed );
}

/**
 * Tooltip: Get Delay
 */
function wpg_glossary_get_tooltip_delay() {
	$wpg_glossary_tooltip_delay = get_option( 'wpg_glossary_tooltip_delay' );
	
	if( $wpg_glossary_tooltip_delay == '' ) {
		$wpg_glossary_tooltip_delay = 200;
	}
	
	return apply_filters( 'wpg_glossary_tooltip_delay', $wpg_glossary_tooltip_delay );
}

/**
 * Tooltip: Is Touch Device Support?
 */
function wpg_glossary_is_tooltip_touch_devices() {
	$wpg_glossary_activate_touch_devices = get_option( 'wpg_glossary_activate_touch_devices' );
	
	if( $wpg_glossary_activate_touch_devices == 'no' ) {
		$wpg_glossary_activate_touch_devices = false;
	} else {
		$wpg_glossary_activate_touch_devices = true;
	}
	
	return apply_filters( 'wpg_glossary_is_tooltip_touch_devices', $wpg_glossary_activate_touch_devices );
}

/**
 * Tooltip: Get Content
 */
function wpg_glossary_get_tooltip_content( $wpg_glossary_is_tooltip_content_shortcode=false, $wpg_glossary_is_tooltip_content_read_more=false ) {
	
	$wpg_glossary_tooltip_content = '';
	
	// Append Title
	$wpg_glossary_is_tooltip_title = wpg_glossary_is_tooltip_title();
	if( $wpg_glossary_is_tooltip_title ) {
		
		$wpg_glossary_tooltip_title_format = wpg_glossary_tooltip_title_format();
		if( $wpg_glossary_tooltip_title_format != '' ) {
			$wpg_glossary_tooltip_title = $wpg_glossary_tooltip_title_format;
			
			if( strstr( $wpg_glossary_tooltip_title, "{TITLE}" ) ) {
				$title = apply_filters( 'wpg_tooltip_term_title_start', '<span class="wpg-tooltip-term-title">' ) . get_the_title() . apply_filters( 'wpg_tooltip_term_title_end', '</span>' );
				
				$wpg_glossary_tooltip_title = str_replace( "{TITLE}", $title, $wpg_glossary_tooltip_title );
			}
			
			$wpg_glossary_tooltip_content .= apply_filters( 'wpg_tooltip_title_start', '<h3 class="wpg-tooltip-title">' ) . $wpg_glossary_tooltip_title . apply_filters( 'wpg_tooltip_title_end', '</h3>' );
		}
	}
	
	global $post;
	
	// Execute Required Filters On Content
	$wpg_glossary_get_tooltip_content_type = wpg_glossary_get_tooltip_content_type();
	
	if( $wpg_glossary_get_tooltip_content_type == 'content' ) {
		$content = $post->post_content;
		
		$content = apply_filters( 'wpg_tooltip_content', $content, $wpg_glossary_is_tooltip_content_shortcode, $wpg_glossary_is_tooltip_content_read_more );
	} else {
		$content = $post->post_excerpt;
		if( empty( $content ) ) {
			$content = $post->post_content;
		}
		
		$content = apply_filters( 'wpg_tooltip_excerpt', $content, $wpg_glossary_is_tooltip_content_shortcode, $wpg_glossary_is_tooltip_content_read_more );
	}
	
	// Limit Number Of Content Words
	$wpg_glossary_get_tooltip_content_length = wpg_glossary_get_tooltip_content_length();
	if( $wpg_glossary_get_tooltip_content_length > 0 ) {
		$content = wp_trim_words( $content, $wpg_glossary_get_tooltip_content_length, '' );
	}
	
	// Read More Link
	if( $wpg_glossary_is_tooltip_content_read_more ) {
		$content .= apply_filters( 'wpg_tooltip_content_read_more_wrapper_start', '<p class="wpg-read-more"><a href="'. get_permalink( $post->ID ) .'">', get_permalink( $post->ID ) );
		
			$content .= wpg_glossary_get_label_tooltip_content_read_more();
		
		$content .= apply_filters( 'wpg_tooltip_content_read_more_wrapper_end', '</a><p>' );
	}
	
	// Wrap The Content
	$wpg_glossary_tooltip_content .= apply_filters( 'wpg_tooltip_content_start', '<div class="wpg-tooltip-content">' ) . $content . apply_filters( 'wpg_tooltip_content_end', '</div>' );
	
	return apply_filters( 'wpg_glossary_tooltip_content', esc_html( $wpg_glossary_tooltip_content ) );
}

/**
 * Linkify: Is Active?
 */
function wpg_glossary_is_linkify() {
	$wpg_glossary_activate_linkify = get_option( 'wpg_glossary_activate_linkify' );
	
	if( $wpg_glossary_activate_linkify == 'no' ) {
		$wpg_glossary_activate_linkify = false;
	} else if( wpg_glossary_is_bp_page() ) {
		$wpg_glossary_activate_linkify = false;
	} else {
		$wpg_glossary_activate_linkify = true;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify', $wpg_glossary_activate_linkify );
}

/**
 * Linkify: Get HTML Tags to Exclude
 */
function wpg_glossary_get_linkify_exclude_html_tags() {
	$wpg_glossary_linkify_exclude_html_tags = get_option( 'wpg_glossary_linkify_exclude_html_tags' );
	
	return apply_filters( 'wpg_glossary_linkify_exclude_html_tags', $wpg_glossary_linkify_exclude_html_tags );
}

/**
 * Linkify: Tags?
 */
function wpg_glossary_is_linkify_tags() {
	$wpg_glossary_linkify_tags = get_option( 'wpg_glossary_linkify_tags' );
	
	if( $wpg_glossary_linkify_tags == 'no' ) {
		$wpg_glossary_linkify_tags = false;
	} else {
		$wpg_glossary_linkify_tags = true;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_tags', $wpg_glossary_linkify_tags );
}

/**
 * Linkify: Is Disable Link?
 */
function wpg_glossary_is_linkify_disable_link() {
	$wpg_glossary_linkify_disable_link = get_option( 'wpg_glossary_linkify_disable_link' );
	
	if( $wpg_glossary_linkify_disable_link == 'yes' ) {
		$wpg_glossary_linkify_disable_link = true;
	} else {
		$wpg_glossary_linkify_disable_link = false;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_disable_link', $wpg_glossary_linkify_disable_link );
}

/**
 * Linkify: New Tab?
 */
function wpg_glossary_is_linkify_new_tab() {
	$wpg_glossary_linkify_new_tab = get_option( 'wpg_glossary_linkify_new_tab' );
	
	if( $wpg_glossary_linkify_new_tab == 'yes' ) {
		$wpg_glossary_linkify_new_tab = true;
	} else {
		$wpg_glossary_linkify_new_tab = false;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_new_tab', $wpg_glossary_linkify_new_tab );
}

/**
 * Linkify: Get Zones
 */
function wpg_glossary_get_linkify_sections() {
	$wpg_glossary_linkify_sections = get_option( 'wpg_glossary_linkify_sections' );
	
	return apply_filters( 'wpg_glossary_linkify_sections', $wpg_glossary_linkify_sections );
}

/**
 * Linkify: Get Post Types
 */
function wpg_glossary_get_linkify_post_types() {
	$wpg_glossary_linkify_post_types = get_option( 'wpg_glossary_linkify_post_types' );
	
	return apply_filters( 'wpg_glossary_linkify_post_types', $wpg_glossary_linkify_post_types );
}

/**
 * Linkify: Is on Front Page?
 */
function wpg_glossary_is_linkify_on_front_page() {
	$wpg_glossary_linkify_on_front_page = get_option( 'wpg_glossary_linkify_on_front_page' );
	
	if( $wpg_glossary_linkify_on_front_page == 'yes' ) {
		$wpg_glossary_linkify_on_front_page = true;
	} else {
		$wpg_glossary_linkify_on_front_page = false;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_on_front_page', $wpg_glossary_linkify_on_front_page );
}

/**
 * Linkify: Get Limit per Term
 */
function wpg_glossary_get_linkify_term_limit() {
	$wpg_glossary_linkify_term_limit = get_option( 'wpg_glossary_linkify_term_limit' );
	
	if( $wpg_glossary_linkify_term_limit == '' || $wpg_glossary_linkify_term_limit == 0 ) {
		$wpg_glossary_linkify_term_limit = -1;
	}
	
	return apply_filters( 'wpg_glossary_linkify_term_limit', $wpg_glossary_linkify_term_limit );
}

/**
 * Linkify: Is Limit for Full Page Content?
 */
function wpg_glossary_is_linkify_limit_for_full_page() {
	$wpg_glossary_linkify_limit_for_full_page = get_option( 'wpg_glossary_linkify_limit_for_full_page' );
	
	if( $wpg_glossary_linkify_limit_for_full_page == 'yes' ) {
		$wpg_glossary_linkify_limit_for_full_page = true;
	} else {
		$wpg_glossary_linkify_limit_for_full_page = false;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_limit_for_full_page', $wpg_glossary_linkify_limit_for_full_page );
}

/**
 * Linkify: Is Case Sensitive?
 */
function wpg_glossary_is_linkify_case_sensitive() {
	$wpg_glossary_linkify_case_sensitive = get_option( 'wpg_glossary_linkify_case_sensitive' );
	
	if( $wpg_glossary_linkify_case_sensitive == 'yes' ) {
		$wpg_glossary_linkify_case_sensitive = true;
	} else {
		$wpg_glossary_linkify_case_sensitive = false;
	}
	
	return apply_filters( 'wpg_glossary_is_linkify_case_sensitive', $wpg_glossary_linkify_case_sensitive );
}

/**
 * BuddyPress: Is BuddyPress Page?
 */
function wpg_glossary_is_bp_page() {
	$wpg_glossary_is_bp_page = false;
	
	if( function_exists( 'bp_is_members_component' ) ) {
		if( bp_is_members_component() || bp_is_user() ) {
			$wpg_glossary_is_bp_page = true;
		}
	}
	
	return apply_filters( 'wpg_glossary_is_bp_page', $wpg_glossary_is_bp_page );
}

/**
 * Glossary Term Custom Title
 */
function wpg_glossary_term_title( $post_id='', $title='' ) {
	global $post;
	
	$custom_post_title = esc_attr( get_post_meta( $post_id, 'custom_post_title', true ) );
	if( $custom_post_title != '' ) {
		$title = $custom_post_title;
	}
	
	return $title;
}

/**
 * Glossary Term Custom Permalink
 */
function wpg_glossary_term_permalink( $post_id='', $permalink='' ) {
	global $post;
	
	$custom_post_permalink = esc_attr( get_post_meta( $post_id, 'custom_post_permalink', true ) );
	if( $custom_post_permalink != '' ) {
		$permalink = $custom_post_permalink;
	}
	
	return $permalink;
}

/**
 * Get Post Types
 */
function wpg_get_post_types() {
	$wpg_post_types = array();
		
	$post_types = get_post_types( apply_filters( 'wpg_glossary_post_types_args', array( 'public' => true ) ), 'objects' );
	if( ! empty( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			$wpg_post_types[ $post_type->name ] = $post_type->labels->name;
		}
	}
	
	return $wpg_post_types;
}

/**
 * Hook: Filter Tooltip Content
 */
function wpg_glossary_tooltip_content_filter( $content, $wpg_glossary_is_tooltip_content_shortcode=false, $wpg_glossary_is_tooltip_content_read_more=false ) {
	// Insert Auto Paragraphs
	$content = wpautop( $content );
	
	// Execute Shortcodes
	if( $wpg_glossary_is_tooltip_content_shortcode ) {
		$content = do_shortcode( $content );
	}
	
	return $content;
}
add_filter( 'wpg_tooltip_content', 'wpg_glossary_tooltip_content_filter', 10, 3 );

/**
 * Hook: Filter Tooltip Excerpt
 */
function wpg_glossary_tooltip_excerpt_filter( $content, $wpg_glossary_is_tooltip_content_shortcode=false, $wpg_glossary_is_tooltip_content_read_more=false ) {
	// Insert Auto Paragraphs
	$content = wpautop( $content );
	
	// <!--more--> Tag Support
	$content_chunks = get_extended ( $content );
	if( ! empty( $content_chunks ) ) {
		$content = $content_chunks['main'];
	}
	
	// Execute Shortcodes
	if( $wpg_glossary_is_tooltip_content_shortcode ) {
		$content = do_shortcode( $content );
	}
	
	return $content;
}
add_filter( 'wpg_tooltip_excerpt', 'wpg_glossary_tooltip_excerpt_filter', 10, 3 );

/**
 * Hook: Override Glossary Term Title
 */
function wpg_glossary_term_title_filter( $title, $post_id='' ) {
	
	if( ! is_admin () ) {
		$post = get_post( $post_id );
		
		if( get_post_type( $post ) == 'glossary' ) {
			$title = wpg_glossary_term_title( $post_id, $title );
		}
	}
	
	return $title;
}
add_filter( 'the_title', 'wpg_glossary_term_title_filter', 10, 2 );

/**
 * Hook: Override Glossary Term Permalink
 */
function wpg_glossary_term_permalink_filter( $permalink, $post ) {
	
	if( ! is_admin () ) {
		if( get_post_type( $post ) == 'glossary' ) {
			$permalink = wpg_glossary_term_permalink( $post->ID, $permalink );
		}
	}
	
	return $permalink;
}
add_filter( 'post_link', 'wpg_glossary_term_permalink_filter', 10, 2 );
add_filter( 'post_type_link', 'wpg_glossary_term_permalink_filter', 10, 2 );

/**
 * Hook: Add Back Link On Glossary Details Page
 */
function wpg_glossary_after_post_content( $content ) {
	
	if( ! is_admin () ) {
		global $post;
		$post_id = $post->ID;
		
		if ( is_singular( 'glossary' ) && in_the_loop() && $post_id == get_the_ID() ) {
			$wpg_glossary_is_back_link = wpg_glossary_is_back_link();
			if( $wpg_glossary_is_back_link ) {
				$wpg_glossary_page_id = wpg_glossary_get_page_id();
				if( $wpg_glossary_page_id > 0 ) {
					$content .= apply_filters( 'wpg_glossary_post_back_link_wrapper_start', '<p class="wpg-back-link"><a href="'. get_permalink( $wpg_glossary_page_id ) .'">', get_permalink( $wpg_glossary_page_id ) );
					
						$content .= wpg_glossary_get_label_back_link();
					
					$content .= apply_filters( 'wpg_glossary_post_back_link_wrapper_end', '</a><p>' );
				}
			}
		}
	}
	
	return $content;
}
add_filter( 'the_content', 'wpg_glossary_after_post_content', 15 );

/**
 * Hook: Terms Sort Order on Glossary Archive Page
 */
function wpg_glossary_terms_order( $query ) {
	if( ! is_admin() && $query->is_main_query() ) {
		if( $query->is_post_type_archive( 'glossary' ) || is_tax( 'glossary_cat' ) || is_tax( 'glossary_tag' ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}
}
add_filter( 'pre_get_posts', 'wpg_glossary_terms_order' );
