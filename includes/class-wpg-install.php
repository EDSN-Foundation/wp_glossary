<?php
/**
 * Installation related functions and actions
 *
 * @class WPG_Install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPG_Install {

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct() {
		
		// Add quick links to plugin - Settings
		$plugin = plugin_basename( WPG_PLUGIN_FILE );
		add_filter( "plugin_action_links_$plugin", array( __CLASS__, 'quick_links' ) );
	}
	
	/**
	 * Add settings link on plugin page
	 */
	public static function quick_links( $links ) {
	    $settings_link = '<a href="edit.php?post_type=' . wpg_glossary_get_post_type() . '&page=wpg-settings">' . __( 'Settings', WPG_TEXT_DOMAIN ) . '</a>';
		array_unshift( $links, $settings_link ); 
		return $links; 
	}

	/**
	 * Install WPG
	 */
	public static function install() {
	
		// Create custom post-types/taxonomies
		WPG_Post_Types::register_post_types();
		flush_rewrite_rules();
		
		// Include settings so that we can run through defaults
		include_once( 'admin/class-wpg-settings.php' );

		$option_sections = WPG_Settings::get_settings();
		if( ! empty( $option_sections ) ) {
			foreach( $option_sections as $option_section ) {
				if( isset( $option_section->options ) && ! empty( $option_section->options ) ) {
+					foreach( $option_section->options as $option ) {
+						if( isset( $option->default ) && isset( $option->name ) ) {
+							$autoload = isset( $option->autoload ) ? (bool) $option->autoload : true;
+							add_option( $option->name, $option->default, '', ( $autoload ? 'yes' : 'no' ) );
 						}
 					}
 				}
			}
		}
		
	}
}

new WPG_Install();
