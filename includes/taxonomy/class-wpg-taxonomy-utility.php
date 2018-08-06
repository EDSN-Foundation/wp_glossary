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
 * Return the current action being done within CPTUI context.
 **
 * @return string Current action being done by CPTUI
 */
function cptui_get_current_action() {
	$current_action = '';
	if ( ! empty( $_GET ) && isset( $_GET['action'] ) ) {
		$current_action .= esc_textarea( $_GET['action'] );
	}

	return $current_action;
}

/**
 * Return an array of all taxonomy slugs from Custom Post Type UI.
 **
 * @return array CPTUI taxonomy slugs.
 */
function cptui_get_taxonomy_slugs() {
	$taxonomies = get_option( WPG_Taxonomy_Data::FIELD_OPTION );
	if ( ! empty( $taxonomies ) ) {
		return array_keys( $taxonomies );
	}
	return array();
}

/**
 * Return the appropriate admin URL depending on our context.
 **
 * @param string $path URL path.
 * @return string
 */
function cptui_admin_url( $path ) {
	if ( is_multisite() && is_network_admin() ) {
		return network_admin_url( $path );
	}

	return admin_url( $path );
}

/**
 * Construct action tag for `<form>` tag.
 **
 * @param object|string $ui CPTUI Admin UI instance. Optional. Default empty string.
 * @return string
 */
function cptui_get_post_form_action( $ui = '' ) {
	/**
	 * Filters the string to be used in an `action=""` attribute.
	 **/
	return apply_filters( 'cptui_post_form_action', '', $ui );
}

/**
 * Display action tag for `<form>` tag.
 **
 * @param object $ui CPTUI Admin UI instance.
 */
function cptui_post_form_action( $ui ) {
	echo cptui_get_post_form_action( $ui );
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
 * Checks if a post type is already registered.
 **
 * @param string       $slug Post type slug to check. Optional. Default empty string.
 * @param array|string $data Post type data being utilized. Optional.
 * @return mixed
 */
function cptui_get_post_type_exists( $slug = '', $data = array() ) {

	/**
	 * Filters the boolean value for if a post type exists for 3rd parties.
	 **
	 * @param string       $slug Post type slug to check.
	 * @param array|string $data Post type data being utilized.
	 */
	return apply_filters( 'cptui_get_post_type_exists', post_type_exists( $slug ), $data );
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

	/**
	 * Filters the custom admin notice for CPTUI.
	 **
	 * @param string $value            Complete HTML output for notice.
	 * @param string $action           Action whose message is being generated.
	 * @param string $message          The message to be displayed.
	 * @param string $messagewrapstart Beginning wrap HTML.
	 * @param string $messagewrapend   Ending wrap HTML.
	 */
	return apply_filters( 'cptui_admin_notice', $messagewrapstart . $message . $messagewrapend, $action, $message, $messagewrapstart, $messagewrapend );
}

/**
 * Grab post type or taxonomy slug from $_POST global, if available.
 **
 * @internal
 *
 * @return string
 */
function cptui_get_object_from_post_global() {
	if ( isset( $_POST['cpt_custom_post_type']['name'] ) ) {
		return sanitize_text_field( $_POST['cpt_custom_post_type']['name'] );
	}

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
			cptui_get_object_from_post_global()
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
			cptui_get_object_from_post_global()
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
			cptui_get_object_from_post_global()
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
			cptui_get_object_from_post_global()
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
			cptui_get_object_from_post_global()
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
			cptui_get_object_from_post_global()
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
 * Returns error message for if trying to register existing post type.
 **
 * @return string
 */
function cptui_slug_matches_post_type() {
	return sprintf(
		esc_html__( 'Please choose a different post type name. %s is already registered.', WPG_TEXT_DOMAIN ),
		cptui_get_object_from_post_global()
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
		cptui_get_object_from_post_global()
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
 * Returns error message for if trying to register post type with matching page slug.
 **
 * @return string
 */
function cptui_slug_matches_page() {
	return sprintf(
		esc_html__( 'Please choose a different post type name. %s matches an existing page slug, which can cause conflicts.', WPG_TEXT_DOMAIN ),
		cptui_get_object_from_post_global()
	);
}

/**
 * Returns error message for if trying to use quotes in slugs or rewrite slugs.
 **
 * @return string
 */
function wpt_slug_has_quotes() {
	return sprintf(
		esc_html__( 'Please do not use quotes in post type/taxonomy names or rewrite slugs', WPG_TEXT_DOMAIN ),
		cptui_get_object_from_post_global()
	);
}

/**
 * Returns error message for if trying to use a function that is not on system.
 **
 * @return string
 */
function wpt_taxonomy_metabox_function_doesnt_exists() {
    return sprintf(
        esc_html__( 'Please make sure that the Metabox function existis on system. (e.g."post_categories_meta_box" | "post_tags_meta_box")', WPG_TEXT_DOMAIN ),
        cptui_get_object_from_post_global()
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
 * Mark site as not a new CPTUI install upon update to 1.5.0
 **
 * @param object $wp_upgrader WP_Upgrader instance.
 * @param array  $extras      Extra information about performed upgrade.
 */
function cptui_not_new_install( $wp_upgrader, $extras ) {

	if ( ! is_a( $wp_upgrader, 'Plugin_Upgrader' ) ) {
		return;
	}

	if ( ! array_key_exists( 'plugins', $extras ) || ! is_array( $extras['plugins'] ) ) {
		return;
	}

	// Was CPTUI updated?
	if ( ! in_array( 'custom-post-type-ui/custom-post-type-ui.php', $extras['plugins'] ) ) {
		return;
	}

	// If we are already known as not new, return.
	if ( cptui_is_new_install() ) {
		return;
	}

	// We need to mark ourselves as not new.
	cptui_set_not_new_install();
}
add_action( 'upgrader_process_complete', 'cptui_not_new_install', 10, 2 );

/**
 * Check whether or not we're on a new install.
 **
 * @return bool
 */
function cptui_is_new_install() {
	$new_or_not = true;
	$saved = get_option( 'cptui_new_install', '' );

	if ( 'false' === $saved ) {
		$new_or_not = false;
	}

	/**
	 * Filters the new install status.
	 *
	 * Offers third parties the ability to override if they choose to.
	 **
	 * @param bool $new_or_not Whether or not site is a new install.
	 */
	return (bool) apply_filters( 'cptui_is_new_install',  $new_or_not );
}

/**
 * Set our activation status to not new.
 **/
function cptui_set_not_new_install() {
	update_option( 'cptui_new_install', 'false' );
}

/**
 * Returns saved values for single taxonomy from CPTUI settings.
 **
 * @param string $taxonomy Taxonomy to retrieve CPTUI object for.
 * @return string
 */
function cptui_get_cptui_taxonomy_object( $taxonomy = '' ) {
	$taxonomies = get_option( WPG_Taxonomy_Data::FIELD_OPTION );

	if ( array_key_exists( $taxonomy, $taxonomies ) ) {
		return $taxonomies[ $taxonomy ];
	}
	return '';
}


/**
 * Add missing post_format taxonomy support for CPTUI post types.
 *
 * Addresses bug wih previewing changes for published posts with post types that
 * have post-formats support.
 **
 * @param array $post_types Array of CPTUI post types.
 */
function cptui_published_post_format_fix( $post_types ) {
	foreach ( $post_types as $type ) {
		if ( in_array( 'post-formats', $type['supports'], true ) ) {
			add_post_type_support( $type['name'], 'post-formats' );
			register_taxonomy_for_object_type( 'post_format', $type['name'] );
		}
	}
}
add_action( 'cptui_post_register_post_types', 'cptui_published_post_format_fix' );


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

