<?php
/**
 * Plugin Name: WP Glossary - Encyclopedia / Lexicon / Knowledge Base / Wiki / Dictionary
 * Description: The "WP Glossary" plugin helps you to create your own glossary of terms for Encyclopedia / Lexicon / Knowledge Base / Wiki / Dictionary in your website. This plugin works based on a custom post type and so you have a full editor at your disposal.
 * Version: 1.0
 * WP version: 4.7.4
 * Author: The WP Instinct Team
 * Author URI: http://wpinstinct.com/
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Glossary' ) ) :

/**
 * Main WP_Glossary Class
 *
 * @class WP_Glossary
 */
final class WP_Glossary {

	/**
	 * @var string
	 */
	public $version = '2.2';

	/**
	 * @var WP_Glossary The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Glossary Instance
	 *
	 * Ensures only one instance of WP_Glossary is loaded or can be loaded
	 *
	 * @see WP_Glossary()
	 * @return WP_Glossary instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WP_Glossary Constructor
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
		define( 'WPG_PLUGIN_FILE', __FILE__ );
		define( 'WPG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'WPG_VERSION', $this->version );
		define( 'WPG_PLUGIN_URL', $this->plugin_url() );
		define( 'WPG_PLUGIN_PATH', $this->plugin_path() );
		define( 'WPG_TEXT_DOMAIN', 'wp_glossary' );
	}
	
	/**
	 * Include required core files
	 */
	private function includes() {
		include_once( 'includes/wpg-core-functions.php' );
		include_once( 'includes/class-wpg-install.php' );
		include_once( 'includes/class-wpg-post-types.php' );
		include_once( 'includes/class-wpg-shortcode-list.php' );
		include_once( 'includes/class-wpg-widget-related-posts.php' );
		include_once( 'includes/thumbnail/wpctn-thumbnail-plugin.php' );
		include_once( 'includes/taxonomy/wpg-taxonomy-plugin.php');		
		
		if( is_admin() ) {
			include_once( 'includes/admin/class-wpg-admin-scripts.php' );
			include_once( 'includes/admin/class-wpg-settings.php' );
			include_once( 'includes/admin/class-wpg-user-guide.php' );
		} else {
			include_once( 'includes/class-wpg-linkify.php' );
			include_once( 'includes/class-wpg-frontend-scripts.php' );
		}
	}
	
	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'WPG_Install', 'install' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}
	
	/**
	 * Load Localisation files
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp_glossary', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
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
	 * Init Cards when WordPress Initialises
	 */
	public function init() {
		$this->load_plugin_textdomain();
		do_action( 'wp_glossary_init' );
	}

}

endif;

/**
 * Returns the main instance of WP_Glossary to prevent the need to use globals
 *
 * @return WP_Glossary
 */

function WP_Glossary() {
	return WP_Glossary::instance();
}

// Global for backwards compatibility.
$GLOBALS['wp_glossary'] = WP_Glossary();
