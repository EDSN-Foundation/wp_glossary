<?php
/**
 * Custom Rest Operations
 *
 * @class WPG_Rest_Operations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * WPG_Rest_Operations Class
 */
class WPG_Rest_Operations {
    
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
        
        // Init based hooks
        add_action( 'rest_api_init', array( __CLASS__, 'create_api_posts_meta_field' ));
    }
    
    
    public function create_api_posts_meta_field() {
    
        // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
        register_rest_field( 'post', 'post-meta-fields', array(
            'get_callback'    => 'get_post_meta_for_api',
            'schema'          => null,
        )
            );
    }

    public function get_post_meta_for_api( $object ) {
        //get the id of the post object array
        $post_id = $object['id'];
        
        //return the post meta
        return get_post_meta( $post_id );
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
            'object_type' => [wpg_glossary_get_post_type()]
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

new WPG_Rest_Operations();
