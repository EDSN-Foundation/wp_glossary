<?php
/**
 * Name: WPCTN THUMBNAIL - This plugin allows you to use custom thumbnails in your posts.  
 * Description: This plugin allows you to use custom thumbnails in your posts. The propose is to use a different image from the default one, wich is already used on themes posts. 
 * Version: 1.0
 * Author: Willian Ganzert Lopes
 * Author URI: https://github.com/willianganzert
 * Plugin based on:
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Main WPCTN_Thumbnail Class
 *
 * @class WPCTN_Thumbnail
 */
final class WPCTN_Thumbnail {

	/**
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * @var WPCTN_Thumbnail The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Ensures only one instance of this class is loaded or can be loaded
	 *
	 * @see WPG_Thumbnail()
	 * @return WPCTN_Thumbnail instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WPG_Thumbnail Constructor
	 * @access public
	 */
	public function __construct() {

		// Define constants
		$this->define_constants();
		
		// Include required files
		$this->includes();

		// Hooks
		$this->init_hooks();
	}


	/**
	 * Define WPG Constants
	 */
	private function define_constants() {
		define( 'WPCTN_PLUGIN_FILE', __FILE__ );
		define( 'WPCTN_VERSION', $this->version );
		define( 'WPCTN_3_PLUGIN_PATH', '/includes/thumbnail');//If it is not the main plugin
		define( 'WPCTN_PLUGIN_URL', $this->plugin_url() );
		define( 'WPCTN_PLUGIN_PATH', $this->plugin_path() );
		define( 'WPCTN_TEXT_DOMAIN', 'WPCTN_thumbnail' );
	}
	
	/**
	 * Include required core files
	 */
	private function includes() {
 		include_once('class-wpctn-custom-thumbnail-shortcode.php');
 		include_once('class-wpctn-custom-thumbnail-metabox.php');	
 		include_once('class-wpctn-custom-thumbnail-rest-operations.php' );	
		if( is_admin() ) {
			
		} else {
		}
	}
	
	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}
	
	/**
	 * Load Localisation files
	 */
	public function load_plugin_textdomain() {
	    load_plugin_textdomain(WPCTN_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}
	
	/**
	 * Load taxonomy metabox if it is necessary 
	 */
	public function load_thumbnail_metabox() {
	    $load_metabox = TRUE;
	    if($load_metabox){
	        new WPCTN_Custom_Thumbnail_MetaBox();
	    }
	}
	/**
	 * Load taxonomy metabox if it is necessary
	 */
	public function load_thumbnail_shortcode() {
	    $load_shortcode = TRUE;
	    if($load_shortcode){
	        new WPCTN_CustomThumbnailShortcode();
	    }
	}
	
	

	/**
	 * Get the plugin url
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	
	/**
	 * Get the plugin path
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	/**
	 * Get the plugin relative path
	 *
	 * @return string
	 */
	public function main_relative_path() {
	    return untrailingslashit(defined('WPCTN_3_PLUGIN_PATH')?WPCTN_3_PLUGIN_PATH:'');
	}

	/**
	 * Init Cards when WordPress Initialises
	 */
	public function init() {
		$this->load_plugin_textdomain();
		$this->load_thumbnail_metabox();
		$this->load_thumbnail_shortcode();
		do_action( 'WPCTN_Thumbnail_init' );
	}

}

/**
 * Returns the main instance of WP_Glossary to prevent the need to use globals
 *
 * @return WP_Glossary
 */

function WPG_Thumbnail() {
    return WPCTN_Thumbnail::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpctn_thumbnail'] = WPG_Thumbnail();