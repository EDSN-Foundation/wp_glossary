<?php
/**
 * Name: WPG TAXONOMY - Plugin to create and edit custom taxonomies 
 * Description: Plugin to create and edit custom taxonomies.
 * Version: 1.0
 * Author: Willian Ganzert Lopes
 * Author URI: https://github.com/willianganzert
 * Plugin based on: Custom post type UI - https://wordpress.org/plugins/custom-post-type-ui/
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Main WP_Glossary Class
 *
 * @class WP_Glossary
 */
final class WPG_Taxonomy {

	/**
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * @var WPG_Taxonomy The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Ensures only one instance of this class is loaded or can be loaded
	 *
	 * @see WPG_Taxonomy()
	 * @return WPG_Taxonomy instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WPG_Taxonomy Constructor
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
		define( 'WPGT_PLUGIN_FILE', __FILE__ );
		define( 'WPGT_VERSION', $this->version );
		define( 'WPGT_3_PLUGIN_PATH', '/includes/taxonomy');//If it is not the main plugin
		define( 'WPGT_PLUGIN_URL', $this->plugin_url() );
		define( 'WPGT_PLUGIN_PATH', $this->plugin_path() );
		define( 'WPGT_TEXT_DOMAIN', 'wpg_taxonomy' );
		define( 'WPGT_FORCE_TAXONOMY_POST_TYPES', 0);
	}
	
	/**
	 * Include required core files
	 */
	private function includes() {
	    include_once('enumerations/class-wpg-enumeration-page-action.php');
	    include_once('enumerations/class-wpg-taxonomy-data.php');
	    include_once('class-wpct-taxonomy.php');
		include_once('class-wpg-taxonomy-metabox.php');
		include_once('class-wpg-taxonomy-edit.php');
		include_once('class-wpg-taxonomy-ui.php');
		include_once('class-wpg-taxonomy-post-type-ui.php');
		include_once('class-wpg-taxonomy-utility.php');
		include_once('class-wpg-taxonomy-listings.php');		
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
	    load_plugin_textdomain(WPGT_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}
	
	/**
	 * Load taxonomy metabox if it is necessary 
	 */
	public function load_taxonomy_metabox() {
	    $load_metabox = get_option( 'wpg_glossary_combine_taxonomy_boxes' ) == 'yes';
	    if($load_metabox){
	        new WPG_Taxonomy_Metabox(wpg_glossary_get_post_type());
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
	    return untrailingslashit(defined('WPGT_3_PLUGIN_PATH')?WPGT_3_PLUGIN_PATH:'');
	}

	/**
	 * Init Cards when WordPress Initialises
	 */
	public function init() {
		$this->load_plugin_textdomain();
		$this->load_taxonomy_metabox();
		do_action( 'wpg_taxonomy_init' );
	}

}

/**
 * Returns the main instance of WP_Glossary to prevent the need to use globals
 *
 * @return WP_Glossary
 */

function WPG_Taxonomy() {
    return WPG_Taxonomy::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpg_taxonomy'] = WPG_Taxonomy();