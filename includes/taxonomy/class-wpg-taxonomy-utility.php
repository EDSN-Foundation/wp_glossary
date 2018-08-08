<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return boolean status depending on passed in value.
 **
 * @param mixed $bool_text text to compare to typical boolean values.
 * @return bool Which bool value the passed in value was.
 */
function get_disp_boolean( $bool_text ) {
	$bool_text = (string) $bool_text;
	if ( empty( $bool_text ) || '0' === $bool_text || 'false' === $bool_text ) {
		return false;
	}

	return true;
}

/**
 * Return string versions of boolean values.
 **
 * @param string $bool_text String boolean value.
 * @return string standardized boolean text.
 */
function disp_boolean( $bool_text ) {
	$bool_text = (string) $bool_text;
	if ( empty( $bool_text ) || '0' === $bool_text || 'false' === $bool_text ) {
		return 'false';
	}

	return 'true';
}

/**
 * Conditionally flushes rewrite rules
 **/
function wpt_flush_rewrite_rules() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	/*
	 * Wise men say that you should not do flush_rewrite_rules on init or admin_init. Due to the nature of our plugin
	 * and how new post types or taxonomies can suddenly be introduced, we need to...potentially. For this,
	 * we rely on a short lived transient. Only 5 minutes life span. If it exists, we do a soft flush before
	 * deleting the transient to prevent subsequent flushes. The only times the transient gets created, is if
	 * post types or taxonomies are created, updated, deleted, or imported. Any other time and this condition
	 * should not be met.
	 */
	if ( 'true' === ( $flush_it = get_transient( 'wpt_flush_rewrite_rules' ) ) ) {
		flush_rewrite_rules( false );
		// So we only run this once.
		delete_transient( 'wpt_flush_rewrite_rules' );
	}
}
add_action( 'admin_init', 'wpt_flush_rewrite_rules' );

/**
 * Return the current action on GET context.
 **
 * @return string Current action
 */
function get_current_action() {
	$current_action = '';
	if ( ! empty( $_GET ) && isset( $_GET['action'] ) ) {
		$current_action .= esc_textarea( $_GET['action'] );
	}

	return $current_action;
}
/**
 * Return the appropriate admin URL depending on our context.
 **
 * @param string $path URL path.
 * @return string
 */
function wpct_admin_url( $path ) {
	if ( is_multisite() && is_network_admin() ) {
		return network_admin_url( $path );
	}

	return admin_url( $path );
}


function fixing_taxonomy($taxonomy){
    $new_taxonomy = WPCT_Taxonomy::newInstanceFromJSOM(wp_json_encode($taxonomy));
    
    
    return  $new_taxonomy;
}

/**
 * Return the names or objects of the taxonomies and custom taxonomies which are registered for the requested object or object type, such as
 * a post object or post type name.
 *
 *
 * @param array|string|WP_Post $object Name of the type of taxonomy object, or an object (row from posts)
 * @param string               $output Optional. The type of output to return in the array. Accepts either
 *                                     taxonomy 'names' or 'objects'. Default 'names'.
 * @return array The names of all taxonomy of $object_type.
 */
function get_taxonomy_data($custom_taxonomies_only = TRUE, $object = array(), $output = 'objects') {
    $taxonomies= array();
    $NO_filter = empty($object);    
    if($custom_taxonomies_only){
        
        $data_taxonomies = get_option( WPG_Taxonomy_Data::FIELD_OPTION, array());
                
        $object = (array) $object;
        foreach ( (array) $data_taxonomies as $tax_name => $tax_obj ) {
            if ($NO_filter || array_intersect($object, (array) $tax_obj->object_type) ) {
                if ('names' == $output)
                    $taxonomies[] = $tax_name;
                else {
                    $tax_obj = fixing_taxonomy($tax_obj);
                    $taxonomies[$tax_name] = new WPCT_Taxonomy($tax_name, $tax_obj->object_type, $tax_obj);
                }
            }
        }
                        
    }
    else{
        if($NO_filter){
            $taxonomies = array_merge($taxonomies,get_taxonomies( array(), $output ));
        }
        else {
            $taxonomies = array_merge($taxonomies,get_object_taxonomies( $object, $output ));
        }        
    }
    return $taxonomies;
}


/**
 * Secondary admin notices function for use with admin_notices hook.
 *
 * Constructs admin notice HTML.
 **
 * @param string $message Message to use in admin notice. Optional. Default empty string.
 * @param bool   $success Whether or not a success. Optional. Default true.
 * @return mixed|void
 */
function wpg_admin_notices_helper( $message = '', $success = true ) {

	$class       = array();
	$class[]     = ( $success ) ? 'updated' : 'error';
	$class[]     = 'notice is-dismissible';

	$messagewrapstart = '<div id="message" class="' . implode( ' ', $class ) . '"><p>';

	$messagewrapend = '</p></div>';

	$action = '';

	return $messagewrapstart . $message . $messagewrapend;
}

/**
 * Get taxonomy name from $_POST global, if available.
 **
 * @internal
 *
 * @return string
 */
function get_taxonomy_name_from_post_global() {	

	if ( isset( $_POST['custom_taxonomy_data']['name'] ) ) {
		return sanitize_text_field( $_POST['custom_taxonomy_data']['name'] );
	}

	return esc_html__( 'Object', WPG_TEXT_DOMAIN );
}

/**
 * Successful add callback.
 **/
function wpg_add_success_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has been successfully added', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		true
	);
}

/**
 * Fail to add callback.
 **/
function wpg_add_fail_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has failed to be added', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		false
	);
}

/**
 * Successful update callback.
 **/
function wpg_update_success_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has been successfully updated', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		true
	);
}

/**
 * Fail to update callback.
 **/
function wpg_update_fail_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has failed to be updated', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		false
	);
}

/**
 * Successful delete callback.
 **/
function wpg_delete_success_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has been successfully deleted', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		true
	);
}

/**
 * Fail to delete callback.
 **/
function wpg_delete_fail_admin_notice() {
	echo wpg_admin_notices_helper(
		sprintf(
			esc_html__( '%s has failed to be deleted', WPG_TEXT_DOMAIN ),
			get_taxonomy_name_from_post_global()
		),
		false
	);
}

/**
 * Success to import callback.
 **/
function wpg_import_success_admin_notice() {
	echo wpg_admin_notices_helper(
		esc_html__( 'Successfully imported data.', WPG_TEXT_DOMAIN )
	);
}

/**
 * Failure to import callback.
 **/
function wpg_import_fail_admin_notice() {
	echo wpg_admin_notices_helper(
		esc_html__( 'Invalid data provided', WPG_TEXT_DOMAIN ),
		false
	);
}


/**
 * Returns error message for if trying to register existing taxonomy.
 **
 * @return string
 */
function wpt_slug_taxonomy_already_registered() {
	return sprintf(
		esc_html__( 'Please choose a different taxonomy name. %s is already registered.', WPG_TEXT_DOMAIN ),
		get_taxonomy_name_from_post_global()
	);
}

/**
 * Returns error message for if trying to register existing post-type.
 **
 * @return string
 */
function wpt_post_type_is_reserved() {
    return sprintf(
        esc_html__( 'Please choose a different post-type name. The selected one is reserved for WordPress Only.', WPG_TEXT_DOMAIN ));
}

/**
 * Returns error message for if trying to use quotes in slugs or rewrite slugs.
 **
 * @return string
 */
function wpt_slug_has_quotes() {
	return sprintf(
		esc_html__( 'Please do not use quotes in post type/taxonomy names or rewrite slugs', WPG_TEXT_DOMAIN ),
		get_taxonomy_name_from_post_global()
	);
}

/**
 * Error admin notice.
 **/
function wpg_error_admin_notice() {
	echo wpg_admin_notices_helper(
		apply_filters( 'wpg_custom_error_message', '' ),
		false
	);
}
/**
 * gets the current post type in the WordPress Admin
 */
function get_current_post_type()
{
    global $post, $get, $typenow, $current_screen;
    
    // we have a post so we can just get the post type from that
    if ($post && $post->post_type)
        return $post->post_type;
    
    // check the global $typenow - set in admin.php
    elseif ($typenow)
        return $typenow;
    
    // check the global $current_screen object - set in sceen.php
    elseif ($current_screen && $current_screen->post_type)
        return $current_screen->post_type;
    
    // lastly check the post_type querystring
    elseif (isset($_REQUEST['post_type']))
        return sanitize_key($_REQUEST['post_type']);
    
    
    // we do not know the post type!
    return null;
}
/**
 * Retrieves the URL for editing a given taxonomy.
 *
 * @param string $taxonomy    Taxonomy. Defaults to the taxonomy of the term identified
 *                            by `$term_id`.
 * @param string $object_type Optional. The object type. Used to highlight the proper post type
 *                            menu on the linked page. Defaults to the first object_type associated
 *                            with the taxonomy.
 * @return string|null The edit taxonomy link URL for the given term, or null on failure.
 */
function get_edit_taxonomy_link( $taxonomy = '', $object_type = '' ) {
    $tax = get_taxonomy( $taxonomy);
    if ( ! $tax || ! current_user_can( 'edit_categories') ) {
        return;
    }
    
    $args = array(
        'taxonomy' => $taxonomy
    );
    
    if ( $object_type ) {
        $args['post_type'] = $object_type;
    } elseif ( ! empty( $tax->object_type ) ) {
        $args['post_type'] = reset( $tax->object_type );
    }
    
    if ( $tax->show_ui ) {
        $location = add_query_arg( $args, admin_url( 'edit-tags.php' ) );
    } else {
        $location = '';
    }
    
    /**
     * Filters the edit link for a taxonomy.
     **
     * @param string $location    The edit link.
     * @param string $taxonomy    Taxonomy name.
     * @param string $object_type The object type (eg. the post type).
     */
    return apply_filters( 'get_edit_taxonomy_link', $location, $taxonomy, $object_type );
}

/**
 * Return an array of names that users should not or can not use for taxonomy names.
 *
 * See <a href="https://codex.wordpress.org/Reserved_Terms">https://codex.wordpress.org/Reserved_Terms</a>
 *
 * @return array $value Array of names that are recommended against.
 */
function wp_and_plugins_reserved_terms() {
    
    $reserved = array(
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'customize_messenger_channel',
        'customized',
        'cpage',
        'day',
        'debug',
        'error',
        'exact',
        'feed',
        'fields',
        'hour',
        'include',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nonce',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_tag',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'theme',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year',
        'output',
    );
    
    /**
     * Filters the list of reserved terms.
     * 3rd party plugin authors could use this to prevent duplicate terms.
     *
     *
     * @param array $value Array of post type slugs to forbid.
     */
    $custom_reserved = apply_filters( 'plugins_reserved_terms', array() );
    
    if ( is_string( $custom_reserved ) && ! empty( $custom_reserved ) ) {
        $reserved[] = $custom_reserved;
    } else if ( is_array( $custom_reserved ) && ! empty( $custom_reserved ) ) {
        foreach ( $custom_reserved as $slug ) {
            $reserved[] = $slug;
        }
    }
    
    return $reserved;
}
function dump($id,$arg1,$arg2=NULL,$arg3=NULL){
    echo "<pre style='margin-left:200px;'>".$id."<br>
            <table>";
    echo "<tr>";
    echo "<td>";
    var_dump($arg1);
    echo "</td>";
    echo "</tr>";
    if($arg2 !=NULL){
        echo "<tr>";
        echo "<td>";
        var_dump($arg2);
        echo "</td>";
        echo "</tr>";
    }
    if($arg3 !=NULL){
        echo "<tr>";
        echo "<td>";
        var_dump($arg3);
        echo "</td>";
        echo "</tr>";
    }
    echo "</table></pre>";
}

