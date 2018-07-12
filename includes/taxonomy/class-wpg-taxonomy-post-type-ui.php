<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register users' custom taxonomies.
 
 *
 * @internal
 */
function wpg_create_custom_taxonomies() {
    $taxes = get_option( WPG_Taxonomy_Data::FIELD_OPTION );
    
    if ( empty( $taxes ) ) {
        return;
    }    
    if ( is_array( $taxes ) ) {
        foreach ( $taxes as $tax ) {
            wpg_register_taxonomy( $tax );
        }
    }
}
add_action( 'init', 'wpg_create_custom_taxonomies', 9 );  // Leave on standard init for legacy purposes.

/**
 * Helper function to register the actual taxonomy.
 
 *
 * @internal
 *
 * @param array $taxonomy Taxonomy array to register. Optional.
 * @return null Result of register_taxonomy.
 */
function wpg_register_taxonomy( $taxonomy = array() ) {

	$labels = array(
		'name'               => $taxonomy['label'],
		'singular_name'      => $taxonomy['singular_label'],
	);

	$description = '';
	if ( ! empty( $taxonomy['description'] ) ) {
		$description = $taxonomy['description'];
	}

	$preserved = wpg_get_preserved_keys( 'taxonomies' );
	foreach ( $taxonomy['labels'] as $key => $label ) {

		if ( ! empty( $label ) ) {
			$labels[ $key ] = $label;
		} elseif ( empty( $label ) && in_array( $key, $preserved ) ) {
			$labels[ $key ] = wpg_get_preserved_label( 'taxonomies', $key, $taxonomy['label'], $taxonomy['singular_label'] );
		}
	}

	$rewrite = get_disp_boolean( $taxonomy['rewrite'] );
	if ( false !== get_disp_boolean( $taxonomy['rewrite'] ) ) {
		$rewrite = array();
		$rewrite['slug'] = ( ! empty( $taxonomy['rewrite_slug'] ) ) ? $taxonomy['rewrite_slug'] : $taxonomy['name'];
		$rewrite['with_front'] = true;
		if ( isset( $taxonomy['rewrite_withfront'] ) ) {
			$rewrite['with_front'] = ( 'false' === disp_boolean( $taxonomy['rewrite_withfront'] ) ) ? false : true;
		}
		$rewrite['hierarchical'] = false;
		if ( isset( $taxonomy['rewrite_hierarchical'] ) ) {
			$rewrite['hierarchical'] = ( 'true' === disp_boolean( $taxonomy['rewrite_hierarchical'] ) ) ? true : false;
		}
	}

	if ( in_array( $taxonomy['query_var'], array( 'true', 'false', '0', '1' ) ) ) {
		$taxonomy['query_var'] = get_disp_boolean( $taxonomy['query_var'] );
	}
	if ( true === $taxonomy['query_var'] && ! empty( $taxonomy['query_var_slug'] ) ) {
		$taxonomy['query_var'] = $taxonomy['query_var_slug'];
	}

	$public = ( ! empty( $taxonomy['public'] ) && false === get_disp_boolean( $taxonomy['public'] ) ) ? false : true;

	$show_admin_column = ( ! empty( $taxonomy['show_admin_column'] ) && false !== get_disp_boolean( $taxonomy['show_admin_column'] ) ) ? true : false;

	$show_in_menu = ( ! empty( $taxonomy['show_in_menu'] ) && false !== get_disp_boolean( $taxonomy['show_in_menu'] ) ) ? true : false;

	if ( empty( $taxonomy['show_in_menu'] ) ) {
		$show_in_menu = get_disp_boolean( $taxonomy['show_ui'] );
	}

	$show_in_nav_menus = ( ! empty( $taxonomy['show_in_nav_menus'] ) && false !== get_disp_boolean( $taxonomy['show_in_nav_menus'] ) ) ? true : false;
	if ( empty( $taxonomy['show_in_nav_menus'] ) ) {
		$show_in_nav_menus = $public;
	}

	$show_in_rest = ( ! empty( $taxonomy['show_in_rest'] ) && false !== get_disp_boolean( $taxonomy['show_in_rest'] ) ) ? true : false;

	$show_in_quick_edit = ( ! empty( $taxonomy['show_in_quick_edit'] ) && false !== get_disp_boolean( $taxonomy['show_in_quick_edit'] ) ) ? true : false;

	$rest_base = null;
	if ( ! empty( $taxonomy['rest_base'] ) ) {
		$rest_base = $taxonomy['rest_base'];
	}

	$args = array(
		'labels'             => $labels,
		'label'              => $taxonomy['label'],
		'description'        => $description,
		'public'             => $public,
		'hierarchical'       => get_disp_boolean( $taxonomy['hierarchical'] ),
		'show_ui'            => get_disp_boolean( $taxonomy['show_ui'] ),
		'show_in_menu'       => $show_in_menu,
		'show_in_nav_menus'  => $show_in_nav_menus,
		'query_var'          => $taxonomy['query_var'],
		'rewrite'            => $rewrite,
		'show_admin_column'  => $show_admin_column,
		'show_in_rest'       => $show_in_rest,
		'rest_base'          => $rest_base,
		'show_in_quick_edit' => $show_in_quick_edit,
	);

	$object_type = ( ! empty( $taxonomy['object_types'] ) ) ? $taxonomy['object_types'] : '';

	/**
	 * Filters the arguments used for a taxonomy right before registering.
	 
	 * @since 1.3.0 Added original passed in values array
	 *
	 * @param array  $args     Array of arguments to use for registering taxonomy.
	 * @param string $value    Taxonomy slug to be registered.
	 * @param array  $taxonomy Original passed in values for taxonomy.
	 */
	$args = apply_filters( 'cptui_pre_register_taxonomy', $args, $taxonomy['name'], $taxonomy );

	return register_taxonomy( $taxonomy['name'], wpg_glossary_get_slug(), $args );
}

/**
 * Construct and output tab navigation.
 
 *
 * @param string $page Whether it's the CPT or Taxonomy page. Optional. Default "post_types".
 */
function taxonomy_tab_menu( $page = 'post_types' ) {

	/**
	 * Filters the tabs to render on a given page.
	 
	 *
	 * @param array  $value Array of tabs to render.
	 * @param string $page  Current page being displayed.
	 */
	$tabs = (array) apply_filters( 'wpg_get_taxonomy_tabs', array(), $page );

	if ( ! empty( $tabs['page_title'] ) ) {
		printf(
			'<h1>%s</h1><h2 class="nav-tab-wrapper">',
			$tabs['page_title']
		);
	}

	foreach ( $tabs['tabs'] as $tab ) {
		printf(
			'<a class="%s" href="%s" aria-selected="%s">%s</a>',
			implode( ' ', $tab['classes'] ),
			$tab['url'],
			$tab['aria-selected'],
			$tab['text']
		);
	}

	echo '</h2>';
}
/**
 * Register our tabs for the Taxonomy screen.
 
 *
 * @internal
 *
 * @param array  $tabs         Array of tabs to display. Optional.
 * @param string $current_page Current page being shown. Optional. Default empty string.
 * @return array Amended array of tabs to show.
 */
function wpg_taxonomy_tabs( $tabs = array(), $current_page = '' ) {
    
    if ( 'taxonomies' === $current_page ) {
        $taxonomies = get_taxonomy_data();
        $classes    = array( 'nav-tab' );
        $active_tab_class = array('nav-tab-active');
        
        $action = cptui_get_current_action();
        $tabs['page_title'] = get_admin_page_title();
        $tabs['tabs']       = array();
        // Start out with our basic "Add new" tab.
        $tabs['tabs']['add'] = array(
            'text'          => esc_html__( 'Add New Taxonomy', WPG_TEXT_DOMAIN ),
            'classes'       => array_merge($classes,empty( $action )?$active_tab_class:[]),
            'url'           => cptui_admin_url( 'edit.php?post_type=glossary&page=wpg_taxonomies' ),
            'aria-selected' => empty( $action )?'true':'false',
        );        
        if ( ! empty( $taxonomies ) ) {
            $tabs['tabs']['edit'] = array(
                'text'          => esc_html__( 'Edit Taxonomies', WPG_TEXT_DOMAIN ),
                'classes'       => array_merge($classes,!empty( $action ) && $action == WPG_Page_Action::EDITING?$active_tab_class:[]),
                'url'           => esc_url( add_query_arg( array( 'action' => WPG_Page_Action::EDITING ), cptui_admin_url( 'edit.php?post_type=glossary&page=wpg_taxonomies' ) ) ),
                'aria-selected' => ( ! empty( $action ) && $action == WPG_Page_Action::EDITING) ? 'true' : 'false'
            );
            
            $tabs['tabs']['view'] = array(
                'text'          => esc_html__( 'View Taxonomies', WPG_TEXT_DOMAIN ),
                'classes'       => array_merge($classes,!empty( $action ) && $action == WPG_Page_Action::LISTING?$active_tab_class:[]),
                'url'           => esc_url( add_query_arg( array( 'action' => WPG_Page_Action::LISTING ),cptui_admin_url( 'edit.php?post_type=glossary&page=wpg_taxonomies' )) ),
                'aria-selected' => ( ! empty( $action ) && $action == WPG_Page_Action::LISTING) ? 'true' : 'false'
            );

        }
    }
    
    return $tabs;
}

add_filter( 'wpg_get_taxonomy_tabs', 'wpg_taxonomy_tabs', 10, 2 );

/**
 * Return a notice based on conditions.
 
 *
 * @param string $action       The type of action that occurred. Optional. Default empty string.
 * @param string $object_type  Whether it's from a post type or taxonomy. Optional. Default empty string.
 * @param bool   $success      Whether the action succeeded or not. Optional. Default true.
 * @param string $custom       Custom message if necessary. Optional. Default empty string.
 * @return bool|string false on no message, else HTML div with our notice message.
 */
function wpg_admin_notices( $action = '', $object_type = '', $success = true, $custom = '' ) {

	$class = array();
	$class[] = ( $success ) ? 'updated' : 'error';
	$class[] = 'notice is-dismissible';
	$object_type = esc_attr( $object_type );

	$messagewrapstart = '<div id="message" class="' . implode( ' ', $class ) . '"><p>';
	$message = '';

	$messagewrapend = '</p></div>';

	if ( 'add' == $action ) {
		if ( $success ) {
			$message .= sprintf( __( '%s has been successfully added', WPG_TEXT_DOMAIN ), $object_type );
		} else {
			$message .= sprintf( __( '%s has failed to be added', WPG_TEXT_DOMAIN ), $object_type );
		}
	} elseif ( 'update' == $action ) {
		if ( $success ) {
			$message .= sprintf( __( '%s has been successfully updated', WPG_TEXT_DOMAIN ), $object_type );
		} else {
			$message .= sprintf( __( '%s has failed to be updated', WPG_TEXT_DOMAIN ), $object_type );
		}
	} elseif ( 'delete' == $action ) {
		if ( $success ) {
			$message .= sprintf( __( '%s has been successfully deleted', WPG_TEXT_DOMAIN ), $object_type );
		} else {
			$message .= sprintf( __( '%s has failed to be deleted', WPG_TEXT_DOMAIN ), $object_type );
		}
	} elseif ( 'import' == $action ) {
		if ( $success ) {
			$message .= sprintf( __( '%s has been successfully imported', WPG_TEXT_DOMAIN ), $object_type );
		} else {
			$message .= sprintf( __( '%s has failed to be imported', WPG_TEXT_DOMAIN ), $object_type );
		}
	} elseif ( 'error' == $action ) {
		if ( ! empty( $custom ) ) {
			$message = $custom;
		}
	}

	if ( $message ) {

		/**
		 * Filters the custom admin notice for CPTUI.
		 
		 *
		 * @param string $value            Complete HTML output for notice.
		 * @param string $action           Action whose message is being generated.
		 * @param string $message          The message to be displayed.
		 * @param string $messagewrapstart Beginning wrap HTML.
		 * @param string $messagewrapend   Ending wrap HTML.
		 */
		return apply_filters( 'cptui_admin_notice', $messagewrapstart . $message . $messagewrapend, $action, $message, $messagewrapstart, $messagewrapend );
	}

	return false;
}

/**
 * Return array of keys needing preserved.
 
 *
 * @param string $type Type to return. Either 'post_types' or 'taxonomies'. Optional. Default empty string.
 * @return array Array of keys needing preservered for the requested type.
 */
function wpg_get_preserved_keys( $type = '' ) {

	$preserved_labels = array(
		'post_types' => array(
			'add_new_item',
			'edit_item',
			'new_item',
			'view_item',
			'all_items',
			'search_items',
			'not_found',
			'not_found_in_trash',
		),
		'taxonomies' => array(
			'search_items',
			'popular_items',
			'all_items',
			'parent_item',
			'parent_item_colon',
			'edit_item',
			'update_item',
			'add_new_item',
			'new_item_name',
			'separate_items_with_commas',
			'add_or_remove_items',
			'choose_from_most_used',
		),
	);
	return ( ! empty( $type ) ) ? $preserved_labels[ $type ] : array();
}

/**
 * Return label for the requested type and label key. 
 *
 * @param string $type Type to return. Either 'post_types' or 'taxonomies'. Optional. Default empty string.
 * @param string $key Requested label key. Optional. Default empty string.
 * @param string $plural Plural verbiage for the requested label and type. Optional. Default empty string.
 * @param string $singular Singular verbiage for the requested label and type. Optional. Default empty string.
 * @return string Internationalized default label.
 */
function wpg_get_preserved_label( $type = '', $key = '', $plural = '', $singular = '' ) {

	$preserved_labels = array(
		'post_types' => array(
			'add_new_item'       => sprintf( __( 'Add new %s', WPG_TEXT_DOMAIN ), $singular ),
			'edit_item'          => sprintf( __( 'Edit %s', WPG_TEXT_DOMAIN ), $singular ),
			'new_item'           => sprintf( __( 'New %s', WPG_TEXT_DOMAIN ), $singular ),
			'view_item'          => sprintf( __( 'View %s', WPG_TEXT_DOMAIN ), $singular ),
			'all_items'          => sprintf( __( 'All %s', WPG_TEXT_DOMAIN ), $plural ),
			'search_items'       => sprintf( __( 'Search %s', WPG_TEXT_DOMAIN ), $plural ),
			'not_found'          => sprintf( __( 'No %s found.', WPG_TEXT_DOMAIN ), $plural ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash.', WPG_TEXT_DOMAIN ), $plural ),
		),
		'taxonomies' => array(
			'search_items'               => sprintf( __( 'Search %s', WPG_TEXT_DOMAIN ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s', WPG_TEXT_DOMAIN ), $plural ),
			'all_items'                  => sprintf( __( 'All %s', WPG_TEXT_DOMAIN ), $plural ),
			'parent_item'                => sprintf( __( 'Parent %s', WPG_TEXT_DOMAIN ), $singular ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', WPG_TEXT_DOMAIN ), $singular ),
			'edit_item'                  => sprintf( __( 'Edit %s', WPG_TEXT_DOMAIN ), $singular ),
			'update_item'                => sprintf( __( 'Update %s', WPG_TEXT_DOMAIN ), $singular ),
			'add_new_item'               => sprintf( __( 'Add new %s', WPG_TEXT_DOMAIN ), $singular ),
			'new_item_name'              => sprintf( __( 'New %s name', WPG_TEXT_DOMAIN ), $singular ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', WPG_TEXT_DOMAIN ), $plural ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', WPG_TEXT_DOMAIN ), $plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', WPG_TEXT_DOMAIN ), $plural ),
		),
	);

	return $preserved_labels[ $type ][ $key ];
}
