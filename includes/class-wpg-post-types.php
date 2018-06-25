<?php
/**
 * Custom Post Types
 *
 * @class WPG_Post_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPG_Post_Types Class
 */
class WPG_Post_Types {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
	
		// Init based hooks
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		
		// Admin Init based hooks
		add_action( 'admin_init', array( __CLASS__, 'disable_tags_auto_suggestion' ) );
		
		// Custom Meta Boxes
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ) );
	}

	/**
	 * Register Post Types
	 *
	 * Post Type: glossary
	 */
	public static function register_post_types() {
			
		// Post Type: glossary
		$wpg_glossary_title = wpg_glossary_get_title();
		
		$labels = apply_filters( 'wpg_post_type_glossary_labels', array(
			'name'					=> _x( $wpg_glossary_title, 'post type general name', WPG_TEXT_DOMAIN ),
			'singular_name'			=> _x( $wpg_glossary_title, 'post type singular name', WPG_TEXT_DOMAIN ),
			'menu_name'				=> _x( $wpg_glossary_title, 'admin menu', WPG_TEXT_DOMAIN ),
			'name_admin_bar'		=> _x( $wpg_glossary_title, 'add new on admin bar', WPG_TEXT_DOMAIN ),
		    'add_new'				=> _x( 'Add New Term', wpg_glossary_get_post_type(), WPG_TEXT_DOMAIN ),
			'add_new_item'			=> __( 'Add New Term', WPG_TEXT_DOMAIN ),
			'new_item'				=> __( 'New Term', WPG_TEXT_DOMAIN ),
			'edit_item'				=> __( 'Edit Term', WPG_TEXT_DOMAIN ),
			'view_item'				=> __( 'View Term', WPG_TEXT_DOMAIN ),
			'all_items'				=> __( 'All Terms', WPG_TEXT_DOMAIN ),
			'search_items'			=> __( 'Search Terms', WPG_TEXT_DOMAIN ),
			'parent_item_colon'		=> __( 'Parent Terms:', WPG_TEXT_DOMAIN ),
			'not_found'				=> __( 'No terms found.', WPG_TEXT_DOMAIN ),
			'not_found_in_trash'	=> __( 'No terms found in Trash.', WPG_TEXT_DOMAIN )
		) );
		
		$args = apply_filters( 'wpg_post_type_glossary_args', array(
			'labels'				=> $labels,
			'description'			=> __( 'Description.', WPG_TEXT_DOMAIN ),
			'menu_icon'				=> 'dashicons-editor-spellcheck',
			'capability_type'		=> 'post',
			'rewrite'				=> array( 'slug' => wpg_glossary_get_slug() ),
			'public'				=> true,
			'publicly_queryable'	=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_in_menu'			=> true,
			'query_var'				=> true,
			'has_archive'			=> wpg_glossary_is_archive(),
			'hierarchical'			=> false,
			'menu_position'			=> 58,
			'supports'				=> array( 'title', 'excerpt', 'editor', 'thumbnail', 'author', 'comments' )
		) );

		register_post_type( wpg_glossary_get_post_type(), $args );
		
		// Taxonomy: glossary_cat
		$labels = apply_filters( 'wpg_taxonomy_glossary_cat_labels', array(
			'name'						=> _x( 'Categories', 'taxonomy general name', WPG_TEXT_DOMAIN ),
			'singular_name'				=> _x( 'Category', 'taxonomy singular name', WPG_TEXT_DOMAIN ),
			'search_items'				=> __( 'Search Glossary Categories', WPG_TEXT_DOMAIN ),
			'popular_items'				=> __( 'Popular Glossary Categories', WPG_TEXT_DOMAIN ),
			'all_items'					=> __( 'All Glossary Categories', WPG_TEXT_DOMAIN ),
			'parent_item'				=> null,
			'parent_item_colon'			=> null,
			'edit_item'					=> __( 'Edit Glossary Category', WPG_TEXT_DOMAIN ),
			'update_item'				=> __( 'Update Glossary Category', WPG_TEXT_DOMAIN ),
			'add_new_item'				=> __( 'Add New Glossary Category', WPG_TEXT_DOMAIN ),
			'new_item_name'				=> __( 'New Glossary Category Name', WPG_TEXT_DOMAIN ),
			'separate_items_with_commas'=> __( 'Separate glossary categories with commas', WPG_TEXT_DOMAIN ),
			'add_or_remove_items'		=> __( 'Add or remove glossary categories', WPG_TEXT_DOMAIN ),
			'choose_from_most_used'		=> __( 'Choose from the most used glossary categories', WPG_TEXT_DOMAIN ),
			'not_found'					=> __( 'No glossary categories found.', WPG_TEXT_DOMAIN ),
			'menu_name'					=> __( 'Categories', WPG_TEXT_DOMAIN ),
		) );

		$args = apply_filters( 'wpg_taxonomy_glossary_cat_args', array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_in_menu'			=> true,
			'show_in_nav_menus'		=> false,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'glossary_cat' ),
		) );

		register_taxonomy( 'glossary_cat', wpg_glossary_get_post_type(), $args );
		
		// Taxonomy: glossary_tag
		$labels = apply_filters( 'wpg_taxonomy_glossary_tag_labels', array(
			'name'						=> _x( 'Tags', 'taxonomy general name', WPG_TEXT_DOMAIN ),
			'singular_name'				=> _x( 'Tag', 'taxonomy singular name', WPG_TEXT_DOMAIN ),
			'search_items'				=> __( 'Search Glossary Tags', WPG_TEXT_DOMAIN ),
			'popular_items'				=> __( 'Popular Glossary Tags', WPG_TEXT_DOMAIN ),
			'all_items'					=> __( 'All Glossary Tags', WPG_TEXT_DOMAIN ),
			'parent_item'				=> null,
			'parent_item_colon'			=> null,
			'edit_item'					=> __( 'Edit Glossary Tag', WPG_TEXT_DOMAIN ),
			'update_item'				=> __( 'Update Glossary Tag', WPG_TEXT_DOMAIN ),
			'add_new_item'				=> __( 'Add New Glossary Tag', WPG_TEXT_DOMAIN ),
			'new_item_name'				=> __( 'New Glossary Tag Name', WPG_TEXT_DOMAIN ),
			'separate_items_with_commas'=> __( 'Separate glossary tags with commas', WPG_TEXT_DOMAIN ),
			'add_or_remove_items'		=> __( 'Add or remove glossary tags', WPG_TEXT_DOMAIN ),
			'choose_from_most_used'		=> NULL,
			'not_found'					=> __( 'No glossary tags found.', WPG_TEXT_DOMAIN ),
			'menu_name'					=> __( 'Tags', WPG_TEXT_DOMAIN ),
		) );

		$args = apply_filters( 'wpg_taxonomy_glossary_tag_args', array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_in_menu'			=> true,
			'show_in_nav_menus'		=> false,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'glossary_tag' ),
		) );

		register_taxonomy( 'glossary_tag', wpg_glossary_get_post_type(), $args );
		
		// Update permalinks when changing $wpg_glossary_slug
		flush_rewrite_rules();
	}
	
	/**
	 * Disable Auto Suggestion for Glossary Tags
	 *
	 * Taxonomy: glossary_tag
	 */
	public static function disable_tags_auto_suggestion() {
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if( isset( $_GET['action'] ) && $_GET['action'] == 'ajax-tag-search' && isset( $_GET['tax'] ) && $_GET['tax'] == 'glossary_tag' ) {
				die;
			}
		}
	}
	
	/**
	 * Add Custom Meta Boxes
	 */
	public static function add_meta_boxes() {
	    add_meta_box( 'meta-box-glossary-attributes', __( 'Custom Attributes', WPG_TEXT_DOMAIN ), array( __CLASS__, 'meta_box_glossary_attributes' ), wpg_glossary_get_post_type(), 'normal', 'high' );
	}
	
	/**
	 * Custom Meta Box Callback - Glossary Custom Attributes
	 */
	public static function meta_box_glossary_attributes( $post ) {
		wp_nonce_field( 'wpg_meta_box', 'wpg_meta_box_nonce' );
		$args = array(
			'public'   => true,
			'_builtin' => false,
			'hierarchical' => true,
		    'object_type' => [wpg_glossary_get_slug()]
		  ); 
		  $output = 'names'; // or objects
		  $operator = 'and'; // 'and' or 'or'
		  $taxonomies = get_taxonomies( $args, $output, $operator ); 
		  if ( $taxonomies ) {
			foreach ( $taxonomies  as $taxonomy ) {
			  //echo '<p>' . $taxonomy . '</p>';
			}
		  }
		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="custom_post_title"><?php _e( 'Post Title', WPG_TEXT_DOMAIN ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="custom_post_title" name="custom_post_title" value="<?php echo esc_attr( get_post_meta( $post->ID, 'custom_post_title', true ) ); ?>" />
						<p class="description"><?php _e( 'This option allows you to use custom post title for current glossary term. This option works with glossary details page and tooltip only.', WPG_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="custom_post_permalink"><?php _e( 'Custom URL', WPG_TEXT_DOMAIN ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="custom_post_permalink" name="custom_post_permalink" value="<?php echo esc_attr( get_post_meta( $post->ID, 'custom_post_permalink', true ) ); ?>" />
						<p class="description"><?php _e( 'This option allows you to use external URL for current glossary term. This option is usefull when you want user to redirect on wikipedia or some other dictionary URL for this particular term rather than having complete description on your website.', WPG_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
	
	/**
	 * Save Custom Meta Boxes
	 */
	public static function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['wpg_meta_box_nonce'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['wpg_meta_box_nonce'], 'wpg_meta_box' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if( isset( $_POST['custom_post_title'] ) ) {
			update_post_meta( $post_id, 'custom_post_title', sanitize_text_field( $_POST['custom_post_title'] ) );
		}
		
		if( isset( $_POST['custom_post_permalink'] ) ) {
			update_post_meta( $post_id, 'custom_post_permalink', sanitize_text_field( $_POST['custom_post_permalink'] ) );
		}
	}
}

new WPG_Post_Types();
