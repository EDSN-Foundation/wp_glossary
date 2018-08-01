<?php
/**
 * Taxonomy Metabox.
 *
 * @package   Taxonomy_Metabox
 * @author  David Cramer <david@CalderaWP.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer and CalderaWP LLC
 */

/**
 * Plugin class.
 * @package Taxonomy_Metabox
 * @author  David Cramer <david@CalderaWP.com>
 */
class WPG_Taxonomy_Metabox {
    /**
     * The slug for this plugin
     *
     * @since 0.0.1
     *
     * @var      string
     */
    private $METABOX_ID = 'taxonomy_metabox_wrapp';
    private $METABOX_SUFIX_ID = 'div';
    
	/**
	 * The slug for this plugin
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'taxonomy-metabox';

	/**
	 * Holds class isntance
	 *
	 * @since 0.0.1
	 *
	 * @var      object|WPG_Taxonomy_Metabox
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 */
	public function __construct() {

		// register metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );

	}


	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.1
	 *
	 * @return    object|WPG_Taxonomy_Metabox    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	

	/**
	 * Adds registered metaboxes to wordpress
	 *
	 * @since 0.0.1
	 *
	 */
	public function add_metaboxes() {

		$screen = get_current_screen();
		if( !is_object( $screen ) || $screen->base != 'post' ){
			return;
		}

		global $post;

		// argh!
		wp_deregister_script( 'pods-handlebars' );
		
		// enqueue the styles and scripts for metaboxes
		wp_enqueue_style('taxonomy_metabox-baldrick-modals', plugins_url('/assets/css/wpg.taxonomy.metabox.css', __FILE__));
		wp_enqueue_script('taxonomy_metabox-post-meta',  plugins_url('/assets/js/wpg.taxonomy.metabox.js', __FILE__), array( 'media-editor' ) , false, true );

		// get all taxonomies
		$taxonomies = get_object_taxonomies( $post );

		// only add if there are taxonomies.
		if( !empty( $taxonomies ) ){
			add_meta_box(
				$this->METABOX_ID,
				__('Taxonomies', $this->plugin_slug),
				array( $this, 'render_metabox' ),
				$screen->post_type,
				'normal',
				'high'
			);
		}

	}
	private function get_active_taxonomy_tab_taxonomy_slug(){
	    $taxonomies = get_taxonomy_data();
	    $hidden = get_hidden_meta_boxes(get_current_screen());
	    
	    foreach( $taxonomies as $taxonomy_slug=>$taxonomy ){
	        if(!in_array($taxonomy_slug . $this->METABOX_SUFIX_ID, $hidden)){
	            return $taxonomy_slug;
	        }
	    }
	    
	    
	    return '';
	}

	/**
	 * Renders Metaboxes
	 *
	 * @since 0.0.1
	 *
	 */
	public function render_metabox( $post, $metabox ) {
	    $args = array(
	        'object_type' => [wpg_glossary_get_slug()]
		  ); 
		//$taxonomies = get_taxonomy_data();
	    
	    $taxonomies = get_taxonomy_data();
	    $hidden = get_hidden_meta_boxes(get_current_screen());
		//echo '<pre>';var_dump($taxonomies);echo '</pre>';
		$has_tabs = false;
		if( count( $taxonomies ) > 1 ){
			$has_tabs = 'has-tabs';
			
		}

		$taxonomy_slug_active_tab = $this->get_active_taxonomy_tab_taxonomy_slug();
		
		echo '<div id="taxonomy-metabox-' . $metabox['id'] . '" class="taxonomy-metabox-wrapper ' . $has_tabs . '">';
		// check if there are multiu panels (tabs)
		if( !empty( $has_tabs ) ){
			
			// yup- tabs! make 'em
			echo '<span class="taxonomy-metabox-wrapper">';
			echo '<ul class="taxonomy-metabox-tab">';
			// taxonomies			
			foreach( $taxonomies as $taxonomy_slug=>$taxonomy ){
			    if( $taxonomy_slug == 'post_format'){
					continue;
				}
				$hidden_ = (in_array($taxonomy_slug . $this->METABOX_SUFIX_ID, $hidden));
				$class = '';
				if($taxonomy_slug_active_tab == $taxonomy_slug){
					$class = 'class="active"';					
				}
				echo '<li id="' . $metabox['id'] . '_tabselect_' . $taxonomy_slug .'" '. $class . ' style="'.($hidden_?'display: none;':'').'"><a href="#' . $metabox['id'] . '_tabselect_' . $taxonomy_slug . '">' . $taxonomy->label . '</a></li>';
			}
			echo '</ul>';
			echo '</span>';
		}

		// make tab bodies, even if just a single tab.
		$first_tab = TRUE;
		foreach( $taxonomies as $taxonomy_slug => $taxonomy ){
		    $hidden_ = (in_array($taxonomy_slug . $this->METABOX_SUFIX_ID, $hidden));			
		    $style = 'display:block;';
			if( $taxonomy_slug == 'post_format'){
				continue;
			}			
			if($hidden_){
			    $style = 'display:none;';
			}
			if($taxonomy_slug_active_tab != $taxonomy_slug){
			    $style .= 'position: absolute; visibility: hidden;';
			}
			echo '<div id="' . $metabox['id'] . '_tab_' . $taxonomy_slug . '" class="taxonomy-metabox-tab-body" style="'.$style.'">';
				if( !empty( $taxonomy->hierarchical ) ){
				    echo '<span data-pull="' . $taxonomy_slug . $this->METABOX_SUFIX_ID.'"></span>';
				}else{
					echo '<span data-pull="tagsdiv-' . $taxonomy_slug . '"></span>';
				}
			echo '</div>';
		}

		echo '</div>';
		
		?>
		<script>
    		//Creating a object to control the visibility of the custom taxonomies.
    		var taxonomies_tab = [<?php 
    		$firstVisible = TRUE;
    		$elements = "";
    		foreach( $taxonomies as $taxonomy_slug => $taxonomy ){
    		    $showed = !in_array($taxonomy_slug . $this->METABOX_SUFIX_ID, $hidden);
    		    $elements .= (strlen($elements)>0?',':'').'{"id":"'.$this->METABOX_ID.'_tab_'.$taxonomy_slug.'","idTab":"'.$this->METABOX_ID.'_tabselect_'.$taxonomy_slug.'", "taxonomySlug":"'.$taxonomy_slug.'", "show":' . ($showed?'true':'false') . ',"selected":'.($showed&&$firstVisible?'true':'false').'}';
    		    if($showed){
    		        $firstVisible=FALSE;
    		    }
    		}
    		echo $elements;
    		?>];
    		var idTaxonomyMetabox = '<?php echo $metabox['id']; ?>';
    		var METABOX_SUFIX_ID = '<?php echo $this->METABOX_SUFIX_ID;?>' 
    
    		// Initializing custom taxonomy metabox visibility control
    		jQuery(()=>customTaxonomy.init(idTaxonomyMetabox, taxonomies_tab, METABOX_SUFIX_ID));
		</script>
		<?php
	
	}


}















