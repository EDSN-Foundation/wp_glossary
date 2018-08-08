<?php
/**
 * Enqueue required styles/scripts files
 *
 * @class WPG_Frontend_Scripts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPG_Frontend_Scripts {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_head', array( __CLASS__, 'inline_styles' ) );
	}

	/**
	 * Register Frontend Scripts
	 */
	public static function register_scripts() {
		
		// Tooltipster Style
		wp_register_style( 'wpg-tooltipster-style', WPG_PLUGIN_URL . '/assets/css/tooltipster/tooltipster.css' );
		wp_register_style( 'wpg-tooltipster-light-style', WPG_PLUGIN_URL . '/assets/css/tooltipster/themes/tooltipster-light.css' );
		wp_register_style( 'wpg-tooltipster-noir-style', WPG_PLUGIN_URL . '/assets/css/tooltipster/themes/tooltipster-noir.css' );
		wp_register_style( 'wpg-tooltipster-punk-style', WPG_PLUGIN_URL . '/assets/css/tooltipster/themes/tooltipster-punk.css' );
		wp_register_style( 'wpg-tooltipster-shadow-style', WPG_PLUGIN_URL . '/assets/css/tooltipster/themes/tooltipster-shadow.css' );
		
		// Main Style
		wp_register_style( 'wpg-main-style', WPG_PLUGIN_URL . '/assets/css/style.css' );
		
		// Mixitup Script
		wp_register_script( 'wpg-mixitup-script', WPG_PLUGIN_URL . '/assets/js/mixitup.min.js', array( 'jquery' ) );
		
		// Tooltipster Script
		wp_register_script( 'wpg-tooltipster-script', WPG_PLUGIN_URL . '/assets/js/jquery.tooltipster.min.js', array( 'jquery' ) );
		
		// Main Script
		wp_register_script( 'wpg-main-script', WPG_PLUGIN_URL . '/assets/js/scripts.js', array( 'jquery' ) );
		
		// Attach Params to Main Script
		$wpg_js_params						= apply_filters( 'wpg_glossary_main_script_params', array(
			'animation'						=> wpg_glossary_is_animation(),
			'is_tooltip'					=> wpg_glossary_is_tooltip(),
			'tooltip_theme'					=> wpg_glossary_get_tooltip_theme(),
			'tooltip_animation'				=> wpg_glossary_get_tooltip_animation(),
			'tooltip_position'				=> wpg_glossary_get_tooltip_position(),
			'tooltip_is_arrow'				=> wpg_glossary_is_tooltip_arrow(),
			'tooltip_min_width'				=> wpg_glossary_get_tooltip_min_width(),
			'tooltip_max_width'				=> wpg_glossary_get_tooltip_max_width(),
			'tooltip_speed'					=> wpg_glossary_get_tooltip_speed(),
			'tooltip_delay'					=> wpg_glossary_get_tooltip_delay(),
			'tooltip_is_touch_devices'		=> wpg_glossary_is_tooltip_touch_devices(),
		) );
		wp_localize_script( 'wpg-main-script', 'wpg', $wpg_js_params );
	}
	
	/**
	 * Queue Frontend Scripts
	 */
	public static function load_scripts() {
		wp_enqueue_style( 'wpg-main-style' );
		
		wp_enqueue_script( 'wpg-mixitup-script' );
		
		// Tooltipster Styles/Scripts
		$wpg_glossary_is_tooltip = wpg_glossary_is_tooltip();
		if( $wpg_glossary_is_tooltip ) {
			wp_enqueue_style( 'wpg-tooltipster-style' );
			
			$wpg_glossary_tooltip_theme = wpg_glossary_get_tooltip_theme();
			if( wp_style_is( 'wpg-tooltipster-' . $wpg_glossary_tooltip_theme . '-style', 'registered' ) ) {
				wp_enqueue_style( 'wpg-tooltipster-' . $wpg_glossary_tooltip_theme . '-style' );
			}
			
			wp_enqueue_script( 'wpg-tooltipster-script' );
		}
		
		wp_enqueue_script( 'wpg-main-script' );
	}
	
	/**
	 * Inline frontend styles
	 */
	public static function inline_styles() {
		$wpg_inline_style = '';
		
		// Filter Colour / Size
		$wpg_glossary_list_filter_font_colour = get_option( 'wpg_glossary_list_filter_font_colour' );
		$wpg_glossary_list_filter_font_size = get_option( 'wpg_glossary_list_filter_font_size' );
		
		if( $wpg_glossary_list_filter_font_colour != '' || $wpg_glossary_list_filter_font_size != '' ) {
			$wpg_inline_style .= '
				.wpg-list-filter a {
					' . ( ( $wpg_glossary_list_filter_font_colour != '' ) ? 'color:' . $wpg_glossary_list_filter_font_colour : '' ) . ';
					' . ( ( $wpg_glossary_list_filter_font_size != '' ) ? 'font-size:' . $wpg_glossary_list_filter_font_size . 'px' : '' ) . ';
				}
			';
		}
		
		$wpg_glossary_list_filter_active_font_colour = get_option( 'wpg_glossary_list_filter_active_font_colour' );
		if( $wpg_glossary_list_filter_active_font_colour != '' ) {
			$wpg_inline_style .= '
				.wpg-list-filter a.active, .wpg-list-filter a:hover, .wpg-list-filter a:focus, .wpg-list-filter a:active  {
					' . ( ( $wpg_glossary_list_filter_active_font_colour != '' ) ? 'color:' . $wpg_glossary_list_filter_active_font_colour : '' ) . ';
				}
			';
		}
		
		// Heading Colour / BG Colour / Size
		$wpg_glossary_list_heading_bg_colour = get_option( 'wpg_glossary_list_heading_bg_colour' );
		$wpg_glossary_list_heading_font_colour = get_option( 'wpg_glossary_list_heading_font_colour' );
		$wpg_glossary_list_heading_font_size = get_option( 'wpg_glossary_list_heading_font_size' );
		
		if( $wpg_glossary_list_heading_bg_colour != '' || $wpg_glossary_list_heading_font_colour != '' || $wpg_glossary_list_heading_font_size != '' ) {
			$wpg_inline_style .= '
				.wpg-list-block h3 {
					' . ( ( $wpg_glossary_list_heading_bg_colour != '' ) ? 'background-color:' . $wpg_glossary_list_heading_bg_colour : '' ) . ';
					' . ( ( $wpg_glossary_list_heading_font_colour != '' ) ? 'color:' . $wpg_glossary_list_heading_font_colour : '' ) . ';
					' . ( ( $wpg_glossary_list_heading_font_size != '' ) ? 'font-size:' . $wpg_glossary_list_heading_font_size . 'px' : '' ) . ';
				}
			';
		}
		
		// Terms Colour / Size
		$wpg_glossary_list_terms_font_colour = get_option( 'wpg_glossary_list_terms_font_colour' );
		$wpg_glossary_list_terms_font_size = get_option( 'wpg_glossary_list_terms_font_size' );
		
		if( $wpg_glossary_list_terms_font_colour != '' || $wpg_glossary_list_terms_font_size != '' ) {
			$wpg_inline_style .= '
				.wpg-list-item a {
					' . ( ( $wpg_glossary_list_terms_font_colour != '' ) ? 'color:' . $wpg_glossary_list_terms_font_colour : '' ) . ';
					' . ( ( $wpg_glossary_list_terms_font_size != '' ) ? 'font-size:' . $wpg_glossary_list_terms_font_size . 'px' : '' ) . ';
				}
			';
		}
		
		$wpg_glossary_list_terms_active_font_colour = get_option( 'wpg_glossary_list_terms_active_font_colour' );
		if( $wpg_glossary_list_terms_active_font_colour != '' ) {
			$wpg_inline_style .= '
				.wpg-list-item a:hover, .wpg-list-item a:focus, .wpg-list-item a:active  {
					' . ( ( $wpg_glossary_list_terms_active_font_colour != '' ) ? 'color:' . $wpg_glossary_list_terms_active_font_colour : '' ) . ';
				}
			';
		}
		
		// Tooltip Colour / Size
		$wpg_glossary_tooltip_bg_colour = get_option( 'wpg_glossary_tooltip_bg_colour' );
		$wpg_glossary_tooltip_border_colour = get_option( 'wpg_glossary_tooltip_border_colour' );
		
		if( $wpg_glossary_tooltip_bg_colour != '' || $wpg_glossary_tooltip_border_colour != '' ) {
			$wpg_inline_style .= '
				.tooltipster-base {
					' . ( ( $wpg_glossary_tooltip_bg_colour != '' ) ? 'background-color:' . $wpg_glossary_tooltip_bg_colour : '' ) . ';
					' . ( ( $wpg_glossary_tooltip_border_colour != '' ) ? 'border-color:' . $wpg_glossary_tooltip_border_colour : '' ) . ';
				}
			';
		}
		
		$wpg_glossary_tooltip_heading_colour = get_option( 'wpg_glossary_tooltip_heading_colour' );
		if( $wpg_glossary_tooltip_heading_colour != '' ) {
			$wpg_inline_style .= '
				.tooltipster-base .wpg-tooltip-title {
					' . ( ( $wpg_glossary_tooltip_heading_colour != '' ) ? 'color:' . $wpg_glossary_tooltip_heading_colour : '' ) . ';
				}
			';
		}
		
		$wpg_glossary_tooltip_content_colour = get_option( 'wpg_glossary_tooltip_content_colour' );
		if( $wpg_glossary_tooltip_content_colour != '' ) {
			$wpg_inline_style .= '
				.tooltipster-base .wpg-tooltip-content, .tooltipster-base .wpg-tooltip-content p {
					' . ( ( $wpg_glossary_tooltip_content_colour != '' ) ? 'color:' . $wpg_glossary_tooltip_content_colour : '' ) . ';
				}
			';
		}
		
		$wpg_glossary_tooltip_link_colour = get_option( 'wpg_glossary_tooltip_link_colour' );
		if( $wpg_glossary_tooltip_link_colour != '' ) {
			$wpg_inline_style .= '
				.tooltipster-base a {
					' . ( ( $wpg_glossary_tooltip_link_colour != '' ) ? 'color:' . $wpg_glossary_tooltip_link_colour : '' ) . ';
				}
			';
		}
		
		// Print Final CSS
		if( $wpg_inline_style != '' ) {
			echo '<style type="text/css">' . $wpg_inline_style . '</style>';
		}
	}
}

new WPG_Frontend_Scripts();
