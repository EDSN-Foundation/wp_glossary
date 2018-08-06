<?php
if (! defined('ABSPATH')) {
    // Exit if accessed directly.
    exit();
}
include_once 'pages/page-taxonomy-list.php'; 

class WPG_Taxonomy_Edit
{

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array(
            $this,
            'admin_enqueue_scripts'
        ));
        add_action('admin_menu', array(
            $this,
            'admin_menu'
        ));
        add_action('init', array(
            $this,
            'init'
        ), 8);
    }

    public function admin_enqueue_scripts()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        $current_screen = get_current_screen();
        
        wp_register_script('taxonomy-main-js', plugins_url("assets/js/wpg.taxonomy.main.js", __FILE__), array(
            'jquery',
            'postbox'
        ), WPG_VERSION, true);
        wp_enqueue_style('taxonomy-main-css', plugins_url("assets/css/wpg.taxonomy.main.css", __FILE__), array(), WPG_VERSION);
        
        if (! is_object($current_screen) || 'glossary_page_wpg_taxonomies' !== $current_screen->base) {
            return;
        }
        wp_enqueue_script('taxonomy-main-js');
        wp_localize_script('taxonomy-main-js', 'taxonomy_messages_data', array(
            'confirm' => esc_html__('Are you sure you want to delete this? Deleting will NOT remove created content.', WPG_TEXT_DOMAIN),
            'no_associated_type' => esc_html('Please select a post type to associate with.', WPG_TEXT_DOMAIN),
            'taxonomy_messages_data.choosing_taxonomy' => WPGT_FORCE_TAXONOMY_POST_TYPES ? false : true
        ));
    }

    public function admin_menu()
    {
        $parent_slug = 'edit.php?post_type=' . wpg_glossary_get_post_type();
        $capability = 'manage_options';
        add_submenu_page($parent_slug, __('Taxonomies', WPG_TEXT_DOMAIN), __('Taxonomies', WPG_TEXT_DOMAIN), $capability, 'wpg_taxonomies', array(
            $this,
            'build_taxonomies_page'
        ));
    }

    public function init()
    {
        $this->process_post();
    }
    
    public function converter_taxonomy_to_data(WPCT_Taxonomy $taxonomy)
    {
        
        $data = clone $taxonomy;
        
        
        $data->query_var  = $taxonomy->query_var !== FALSE;
        if($data->query_var && is_string($taxonomy->query_var)){
            $data->query_var_slug  = $taxonomy->query_var;
        }
        
        
        return $data;
    }
    public function converter_data_to_taxonomy($post,$labels,$related_post_types)
    {
        $custom_data = $post['custom_taxonomy_data'];
        
        $rewrite = filter_var($post['rewrite_data']['rewrite'], FILTER_VALIDATE_BOOLEAN);
        if(!$rewrite){
            $custom_data['rewrite'] = FALSE;
        }
        else {
            $custom_data['rewrite'] = array(
                'slug' => $post['rewrite_data']['slug'],
                'with_front' => filter_var($post['rewrite_data']['with_front'], FILTER_VALIDATE_BOOLEAN),
                'hierarchical' => filter_var($post['rewrite_data']['hierarchical'], FILTER_VALIDATE_BOOLEAN)
            );            
        }        
        
        
        $boolVariables = ['public','hierarchical','show_ui','show_in_menu','show_in_nav_menus','show_tagcloud','show_in_quick_edit',
            'show_admin_column','show_in_rest','rest_controller_class'];
        foreach ($custom_data as $key => $value){
            if(in_array($key,$boolVariables)){
                $custom_data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }
        
        
        
        $is_query_var = filter_var($post['temp_data']['query_var'], FILTER_VALIDATE_BOOLEAN);
        if(!$is_query_var){
            $custom_data['query_var'] = FALSE;
        }
        else{
            if(!empty($post['temp_data']['query_var_slug'])){
                $custom_data['query_var'] = trim($post['temp_data']['query_var_slug']);
            }
            else {
                $custom_data['query_var'] = TRUE;
            }
        }      
        
        $labels = wpg_get_default_labels($labels,$labels['singular_name'],$custom_data['label']);
        
        
        $custom_data['meta_box_cb'] = empty($custom_data['meta_box_cb'])?NULL:$custom_data['meta_box_cb'];
        
        return WPCT_Taxonomy::newInstance($custom_data,$labels,$related_post_types);
    }
    /**
     * Process taxonomy save and delete operations.
     */
    public function process_post()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        if (! is_admin()) {
            return;
        }
        
        if (! empty($_GET) && isset($_GET['page']) && 'wpg_taxonomies' !== $_GET['page']) {
            return;
        }
        
        if (! empty($_POST)) {
            $result = '';
            if (isset($_POST['taxonomy_submit'])) {
                
                if (WPGT_FORCE_TAXONOMY_POST_TYPES) {
                    $taxonomies = $this->get_taxonomy_by_screen();
                    
                    $selected_taxonomy = $this->get_current_taxonomy_slug();
                    
                    if ($selected_taxonomy) {
                        if (array_key_exists($selected_taxonomy, $taxonomies)) {
                            $current = $taxonomies[$selected_taxonomy];
                        }
                    }                    
                    
                    $_POST['taxonomy_related_post_types'] = isset($current) ? $current->object_type : array(
                        wpg_glossary_get_post_type()
                    );
                }
                check_admin_referer('managing_taxonomy', 'managing_taxonomy_nonce_field');
                $result = $this->add_update_taxonomy($_POST);
            } elseif (isset($_POST['taxonomy_delete'])) {
                check_admin_referer('managing_taxonomy', 'managing_taxonomy_nonce_field');
                $result = delete_taxonomy($_POST);
                add_filter('taxonomy_delete_filter', '__return_true');
            }
            if ($result) {
                switch ($result){
                    case 'add_success':{
                        add_action('admin_notices', "wpg_add_success_admin_notice");
                        break;
                    }
                    case 'update_success':{
                        add_action('admin_notices', "wpg_update_success_admin_notice");
                        break;
                    }
                    case 'error':{
                        add_action('admin_notices', "wpg_error_admin_notice");
                        break;
                    }
                    case 'delete_success':{
                        add_action('admin_notices', "wpg_delete_success_admin_notice");
                        break;
                    }
                    case 'delete_fail':{
                        add_action('admin_notices', "wpg_delete_fail_admin_notice");
                        break;
                    }
                    default:{
                        
                    }
                }
                //add_action('admin_notices', "wpg_{$result}_admin_notice");
            }
        }
    }

    public function build_taxonomies_page()
    {
        
        
        $this->render();
    }
    private function get_taxonomy_by_screen($output = 'objects'){
        $screen = get_current_screen();
        $args = array(
            'object_type' => $screen->post_type
        );
        return get_taxonomy_data(TRUE,$args,$output);
    }
    private function render(){
        $taxonomies = $this->get_taxonomy_by_screen();
        
        
        $action = WPG_Page_Action::ADDING;        
        if (! empty($_GET) && ! empty($_GET['action']) && count($taxonomies) > 0) {
            $action = $_GET['action'];
        }        
        
        ?>

    	<div class="wrap wpg-managing">
    
    	<?php        
            // Create our tabs.
    	taxonomy_tab_menu(count($taxonomies));
            
            if ($action == WPG_Page_Action::LISTING) {
                return new WPG_Page_Taxonomy_List();
            }
            else{
                $this->render_editing($action,$taxonomies);
            }
        ?>
        </div>
        <?php
    }
    private function render_editing($action,$taxonomies){ 
        $ui_builder = new WPG_Taxonomy_UI_Builder();
        /**
         * Filters whether or not a taxonomy was deleted.
         *
         * @param bool $value
         *            Whether or not taxonomy deleted. Default false.
         */
        $taxonomy_deleted = apply_filters('taxonomy_delete_filter', false);
        if (WPG_Page_Action::EDITING == $action) {
            $selected_taxonomy = $this->get_current_taxonomy_slug($taxonomy_deleted);
            
            if ($selected_taxonomy) {
                if (array_key_exists($selected_taxonomy, $taxonomies)) {
                    $current = $this->converter_taxonomy_to_data($taxonomies[$selected_taxonomy]);
                }
            }
        }
        
        
        
        // Will only be set if we're already on the edit screen.
        if ($action == WPG_Page_Action::EDITING && ! empty($taxonomies)) {
            
            ?>
	<form class="top_margin" method="post">
		<label for="taxonomy"><?php esc_html_e( 'Select: ', WPG_TEXT_DOMAIN ); ?></label>
			<?php
            $this->build_taxonomies_html_select($taxonomies);
            
            /**
             * Filters the text value to use on the select taxonomy button.
             
             *       
             * @param string $value
             *            Text to use for the button.
             */
            ?>			
			<a
			href="<?php echo get_edit_taxonomy_link($this->get_current_taxonomy_slug()); ?>">
			<input value="Manage categories" type="button"
			class="button-secondary" id="manage_taxonomy" name="manage_taxonomy" />
		</a>

	</form>
	<?php }?>
	<form class="taxonomiesui" method="post">
		<div class="postbox-container">
			<div id="poststuff">
				<div class="wpg-section postbox">
					<button type="button" class="handlediv button-link"
						aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Basic settings', WPG_TEXT_DOMAIN ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle">
						<span><?php esc_html_e( 'Basic settings', WPG_TEXT_DOMAIN ); ?></span>
					</h2>
					<div class="inside">
						<div class="main">
							<table class="form-table wpg-table">
							<?php
        echo $ui_builder->get_tr_start() . $ui_builder->get_th_start();
        echo $ui_builder->get_label('name', esc_html__('Taxonomy Slug', WPG_TEXT_DOMAIN)) . $ui_builder->get_required_span();
        
        if (WPG_Page_Action::EDITING == $action) {
            echo '<p id="slugchanged" class="hidemessage">' . __('Slug has changed', 'custom_post_type_ui') . '</p>';
        }
        echo $ui_builder->get_th_end() . $ui_builder->get_td_start();
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'name',
            'textvalue' => (isset($current->name)) ? esc_attr($current->name) : '',
            'maxlength' => '32',
            'helptext' => esc_attr__('The taxonomy name/slug. Used for various queries for taxonomy content.', WPG_TEXT_DOMAIN),
            'required' => true,
            'placeholder' => false,
            'wrap' => false
        ));
        
        echo '<p class="wpg-slug-details">';
        esc_html_e('Slugs should only contain alphanumeric, latin characters. Underscores should be used in place of spaces. Set "Custom Rewrite Slug" field to make slug use dashes for URLs.', WPG_TEXT_DOMAIN);
        echo '</p>';
        
        if (WPG_Page_Action::EDITING == $action) {
            echo '<p>';
            esc_html_e('DO NOT EDIT the taxonomy slug unless also planning to migrate terms. Changing the slug registers a new taxonomy entry.', WPG_TEXT_DOMAIN);
            echo '</p>';
            
            echo '<div class="wpg-spacer">';
            echo $ui_builder->get_check_input(array(
                'checkvalue' => 'update_taxonomy',
                'checked' => 'false',
                'name' => 'update_taxonomy',
                'namearray' => 'update_taxonomy',
                'labeltext' => esc_html__('Migrate terms to newly renamed taxonomy?', WPG_TEXT_DOMAIN),
                'helptext' => '',
                'default' => false,
                'wrap' => false
            ));
            echo '</div>';
        }
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'label',
            'textvalue' => (isset($current->label)) ? esc_attr($current->label) : '',
            'aftertext' => esc_html__('(e.g. Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Plural Label', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Used for the taxonomy admin menu item.', WPG_TEXT_DOMAIN),
            'required' => true
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'singular_name',
            'textvalue' => (isset($current->labels->singular_name)) ? esc_attr($current->labels->singular_name) : '',
            'aftertext' => esc_html__('(e.g. Actor)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Singular Label', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Used when a singular label is needed.', WPG_TEXT_DOMAIN),
            'required' => true
        ));
        
        echo $ui_builder->get_td_end() . $ui_builder->get_tr_end();
        
        if (! WPGT_FORCE_TAXONOMY_POST_TYPES) {
            echo $ui_builder->get_tr_start() . $ui_builder->get_th_start() . esc_html__('Attach to Post Type', WPG_TEXT_DOMAIN) . $ui_builder->get_required_span();
            echo $ui_builder->get_p(esc_html__('Add support for available registered post types. At least one is required.', WPG_TEXT_DOMAIN));
            echo $ui_builder->get_th_end() . $ui_builder->get_td_start() . $ui_builder->get_fieldset_start();
            
            $args = array(
            'public' => true
            );
            
            // If they don't return an array, fall back to the original default. Don't need to check for empty, because empty array is default for $args param in get_post_types anyway.
            if (! is_array($args)) {
                $args = array(
                    'public' => true
                );
            }
            $output = 'objects'; // Or objects.
            
            $post_types = get_post_types($args, $output);
            
            foreach ($post_types as $post_type) {
                $core_label = (in_array($post_type->name, array(
                    'post',
                    'page',
                    'attachment'
                ))) ? esc_html__('(WP Core)', WPG_TEXT_DOMAIN) : '';
                echo $ui_builder->get_check_input(array(
                    'checkvalue' => $post_type->name,
                    'checked' => ((WPG_Page_Action::ADDING == $action)?wpg_glossary_get_post_type() == $post_type->name:
                    (! empty($current->object_type) && is_array($current->object_type) && in_array($post_type->name, $current->object_type))) ? 'true' : 'false',
                    'name' => $post_type->name,
                    'namearray' => 'taxonomy_related_post_types',
                    'textvalue' => $post_type->name,
                    'labeltext' => $post_type->label . ' ' . $core_label,
                    'wrap' => false
                ));
            }
            
            echo $ui_builder->get_fieldset_end() . $ui_builder->get_td_end() . $ui_builder->get_tr_end();
        }
        ?>
						</table>
							<p class="submit">
							<?php      
        wp_nonce_field('managing_taxonomy', 'managing_taxonomy_nonce_field');
        if (! empty($_GET) && ! empty($_GET['action']) && WPG_Page_Action::EDITING == $_GET['action']) {
            ?>			
								<input type="submit" class="button-primary"
									name="taxonomy_submit"
									value="<?php echo esc_attr__( 'Save Taxonomy', WPG_TEXT_DOMAIN );?>" />
								
								<input type="submit" class="button-secondary"
									name="taxonomy_delete" id="taxonomy_submit_delete"
									value="<?php echo esc_attr__( 'Delete Taxonomy', WPG_TEXT_DOMAIN ); ?>" />
							<?php } else { ?>
								<input type="submit" class="button-primary wpg-taxonomy-submit"
									name="taxonomy_submit"
									value="<?php echo esc_attr__( 'Add Taxonomy', WPG_TEXT_DOMAIN ); ?>" />
							<?php } ?>

							<?php if ( ! empty( $current ) ) { ?>
								<input type="hidden" name="original_taxonomy_name" id="original_taxonomy_name"
									value="<?php echo esc_attr( $current->name ); ?>" />
							<?php
        }?>
							<input type="hidden" name="taxonomy_check_operation" id="taxonomy_check_operation"
									value="<?php echo esc_attr( $action ); ?>" />
							</p>
						</div>
					</div>
				</div>
				<div class="wpg-section postbox">
					<button type="button" class="handlediv button-link"
						aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Additional labels', WPG_TEXT_DOMAIN ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle">
						<span><?php esc_html_e( 'Additional labels', WPG_TEXT_DOMAIN ); ?></span>
					</h2>
					<div class="inside">
						<div class="main">
							<table class="form-table wpg-table">

							<?php
        if (isset($current->description)) {
            $current->description = stripslashes_deep($current->description);
        }
        echo $ui_builder->get_textarea_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'description',
            'rows' => '4',
            'cols' => '40',
            'textvalue' => (isset($current->description)) ? esc_textarea($current->description) : '',
            'labeltext' => esc_html__('Description', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Describe what your taxonomy is used for.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'menu_name',
            'textvalue' => (isset($current->labels->menu_name)) ? esc_attr($current->labels->menu_name) : '',
            'aftertext' => esc_attr__('(e.g. Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Menu Name', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom admin menu name for your taxonomy.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'all_items',
            'textvalue' => (isset($current->labels->all_items)) ? esc_attr($current->labels->all_items) : '',
            'aftertext' => esc_attr__('(e.g. All Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('All Items', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Used as tab text when showing all terms for hierarchical taxonomy while editing post.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'edit_item',
            'textvalue' => (isset($current->labels->edit_item)) ? esc_attr($current->labels->edit_item) : '',
            'aftertext' => esc_attr__('(e.g. Edit Actor)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Edit Item', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Used at the top of the term editor screen for an existing taxonomy term.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'view_item',
            'textvalue' => (isset($current->labels->view_item)) ? esc_attr($current->labels->view_item) : '',
            'aftertext' => esc_attr__('(e.g. View Actor)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('View Item', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Used in the admin bar when viewing editor screen for an existing taxonomy term.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'update_item',
            'textvalue' => (isset($current->labels->update_item)) ? esc_attr($current->labels->update_item) : '',
            'aftertext' => esc_attr__('(e.g. Update Actor Name)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Update Item Name', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'add_new_item',
            'textvalue' => (isset($current->labels->add_new_item)) ? esc_attr($current->labels->add_new_item) : '',
            'aftertext' => esc_attr__('(e.g. Add New Actor)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Add New Item', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Used at the top of the term editor screen and button text for a new taxonomy term.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'new_item_name',
            'textvalue' => (isset($current->labels->new_item_name)) ? esc_attr($current->labels->new_item_name) : '',
            'aftertext' => esc_attr__('(e.g. New Actor Name)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('New Item Name', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'parent_item',
            'textvalue' => (isset($current->labels->parent_item)) ? esc_attr($current->labels->parent_item) : '',
            'aftertext' => esc_attr__('(e.g. Parent Actor)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Parent Item', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'parent_item_colon',
            'textvalue' => (isset($current->labels->parent_item_colon)) ? esc_attr($current->labels->parent_item_colon) : '',
            'aftertext' => esc_attr__('(e.g. Parent Actor:)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Parent Item Colon', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'search_items',
            'textvalue' => (isset($current->labels->search_items)) ? esc_attr($current->labels->search_items) : '',
            'aftertext' => esc_attr__('(e.g. Search Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Search Items', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'popular_items',
            'textvalue' => (isset($current->labels->popular_items)) ? esc_attr($current->labels->popular_items) : null,
            'aftertext' => esc_attr__('(e.g. Popular Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Popular Items', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'separate_items_with_commas',
            'textvalue' => (isset($current->labels->separate_items_with_commas)) ? esc_attr($current->labels->separate_items_with_commas) : null,
            'aftertext' => esc_attr__('(e.g. Separate Actors with commas)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Separate Items with Commas', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'add_or_remove_items',
            'textvalue' => (isset($current->labels->add_or_remove_items)) ? esc_attr($current->labels->add_or_remove_items) : null,
            'aftertext' => esc_attr__('(e.g. Add or remove Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Add or Remove Items', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'choose_from_most_used',
            'textvalue' => (isset($current->labels->choose_from_most_used)) ? esc_attr($current->labels->choose_from_most_used) : null,
            'aftertext' => esc_attr__('(e.g. Choose from the most used Actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Choose From Most Used', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'not_found',
            'textvalue' => (isset($current->labels->not_found)) ? esc_attr($current->labels->not_found) : null,
            'aftertext' => esc_attr__('(e.g. No Actors found)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Not found', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy label. Used in the admin menu for displaying taxonomies.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'no_terms',
            'textvalue' => (isset($current->labels->no_terms)) ? esc_attr($current->labels->no_terms) : null,
            'aftertext' => esc_html__('(e.g. No actors)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('No terms', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Used when indicating that there are no terms in the given taxonomy associated with an object.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'items_list_navigation',
            'textvalue' => (isset($current->labels->items_list_navigation)) ? esc_attr($current->labels->items_list_navigation) : null,
            'aftertext' => esc_html__('(e.g. Actors list navigation)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Items List Navigation', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Screen reader text for the pagination heading on the term listing screen.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'cpt_tax_labels',
            'name' => 'items_list',
            'textvalue' => (isset($current->labels->items_list)) ? esc_attr($current->labels->items_list) : null,
            'aftertext' => esc_html__('(e.g. Actors list)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Items List', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Screen reader text for the items list heading on the term listing screen.', WPG_TEXT_DOMAIN)
        ));
        ?>
						</table>
						</div>
					</div>
				</div>
				<div class="wpg-section postbox">
					<button type="button" class="handlediv button-link"
						aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Settings', WPG_TEXT_DOMAIN ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle">
						<span><?php esc_html_e( 'Settings', WPG_TEXT_DOMAIN ); ?></span>
					</h2>
					<div class="inside">
						<div class="main">
							<table class="form-table wpg-table">
<?php
                
//         echo $ui_builder->get_check_input(array(
//             'checkvalue' => 1,
//             'checked' => (! empty($current->public) && $current->public == 1),
//             'name' => '',
//             'namearray' => 'custom_taxonomy_data',
//             'textvalue' => $post_type->name,
//             'labeltext' => esc_html__('Public', WPG_TEXT_DOMAIN),
//             'aftertext' => esc_html__('(default: true) Whether or not the taxonomy should be publicly queryable.', WPG_TEXT_DOMAIN),
//             //'wrap' => false
//         ));
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'public',
            'labeltext' => esc_html__('Public', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether or not the taxonomy should be publicly queryable.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->public : NULL
        ),TRUE);
               
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'hierarchical',
            'labeltext' => esc_html__('Hierarchical', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether the taxonomy can have parent-child relationships.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->hierarchical : NULL
        ),TRUE);
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_ui',
            'labeltext' => esc_html__('Show UI', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether to generate a default UI for managing this custom taxonomy.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_ui : NULL
        ),TRUE);
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_in_menu',
            'labeltext' => esc_html__('Show in menu', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: false) Whether to show the taxonomy in the admin menu.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_in_menu : NULL
        ),FALSE);        

        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_in_nav_menus',
            'labeltext' => esc_html__('Show in nav menus', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether to make the taxonomy available for selection in navigation menus.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_in_nav_menus : NULL
        ),TRUE);
                
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'temp_data',
            'name' => 'query_var',
            'labeltext' => esc_html__('Query Var', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Sets the query_var key for this taxonomy.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->query_var : NULL
        ),TRUE);
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'temp_data',
            'name' => 'query_var_slug',
            'textvalue' => (isset($current->query_var_slug)) ? esc_attr($current->query_var_slug) : '',
            'aftertext' => esc_attr__('(default: taxonomy slug). Query var needs to be true to use.', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Custom Query Var String', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Sets a custom query_var slug for this taxonomy.', WPG_TEXT_DOMAIN)
        ));
                
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'rewrite_data',
            'name' => 'rewrite',
            'labeltext' => esc_html__('Rewrite', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether or not WordPress should use rewrites for this taxonomy.', WPG_TEXT_DOMAIN),
            'selected' => (isset($current) && isset($current->rewrite)?$current->rewrite != FALSE?TRUE:FALSE:TRUE)
        ),TRUE);
        
        //'slug'
        //'with_front'   => true,
        //'hierarchical' => false,
        //'ep_mask'      => EP_NONE,
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'rewrite_data',
            'name' => 'slug',
            'textvalue' => (isset($current->rewrite['slug'])) ? esc_attr($current->rewrite['slug']) : '',
            'aftertext' => esc_attr__('(default: taxonomy name)', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Custom Rewrite Slug', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('Custom taxonomy rewrite slug.', WPG_TEXT_DOMAIN)            
        ));
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'rewrite_data',
            'name' => 'with_front',
            'labeltext' => esc_html__('Rewrite With Front', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Should the permastruct be prepended with the front base.', WPG_TEXT_DOMAIN),
            'selected' => (isset($current) && isset($current->rewrite) && $current->rewrite !== false)? $current->rewrite['with_front'] : NULL
        ),TRUE);        
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'rewrite_data',
            'name' => 'hierarchical',
            'labeltext' => esc_html__('Rewrite Hierarchical', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: false) Should the permastruct allow hierarchical urls.', WPG_TEXT_DOMAIN),
            'selected' => (isset($current) && isset($current->rewrite) && $current->rewrite !== false)? $current->rewrite['hierarchical'] : NULL
        ),FALSE);
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_admin_column',
            'labeltext' => esc_html__('Show Admin Column', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: true) Whether to allow automatic creation of taxonomy columns on associated post-types.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_admin_column : NULL
        ),TRUE);
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'meta_box_cb',
            'textvalue' => (isset($current->meta_box_cb)) ? esc_attr($current->meta_box_cb) : '',
            'aftertext' => esc_attr__('(default: post_categories_meta_box). The callback function for the meta box display.', WPG_TEXT_DOMAIN),
            'labeltext' => esc_html__('Metabox function', WPG_TEXT_DOMAIN),
            'helptext' => esc_html__('The callback function for the meta box display.', WPG_TEXT_DOMAIN)
        ));
        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_in_rest',
            'labeltext' => esc_html__('Show in REST API', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: false) Whether to show this taxonomy data in the WP REST API.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_in_rest : NULL
        ),FALSE);
        
        echo $ui_builder->get_text_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'rest_base',
            'labeltext' => esc_html__('REST API base slug', WPG_TEXT_DOMAIN),
            'helptext' => esc_attr__('Slug to use in REST API URLs.', WPG_TEXT_DOMAIN),
            'textvalue' => (isset($current->rest_base)) ? esc_attr($current->rest_base) : ''
        ));        
        echo $ui_builder->get_boolean_select_input(array(
            'namearray' => 'custom_taxonomy_data',
            'name' => 'show_in_quick_edit',
            'labeltext' => esc_html__('Show in quick/bulk edit panel.', WPG_TEXT_DOMAIN),
            'aftertext' => esc_html__('(default: false) Whether to show the taxonomy in the quick/bulk edit panel.', WPG_TEXT_DOMAIN),
            'selected' => isset($current) ? $current->show_in_quick_edit : NULL
        ),TRUE);
        ?>
						</table>
						</div>
					</div>
				</div>
			<p class="submit">
				<?php
        
        wp_nonce_field('managing_taxonomy', 'managing_taxonomy_nonce_field');
        if (! empty($_GET) && ! empty($_GET['action']) && WPG_Page_Action::EDITING == $_GET['action']) {
            ?>
					<?php
            
            /**
             * Filters the text value to use on the button when editing.
             
             *       
             * @param string $value
             *            Text to use for the button.
             */
            ?>
					<input type="submit" class="button-primary wpg-taxonomy-submit"
						name="taxonomy_submit"
						value="<?php echo esc_attr__( 'Save Taxonomy', WPG_TEXT_DOMAIN ); ?>" />
					<?php
            
            /**
             * Filters the text value to use on the button when deleting.
             
             *       
             * @param string $value
             *            Text to use for the button.
             */
            ?>
					<input type="submit" class="button-secondary"
						name="taxonomy_delete" id="taxonomy_submit_delete"
						value="<?php echo esc_attr__( 'Delete Taxonomy', WPG_TEXT_DOMAIN ); ?>" />
				<?php } else { ?>
					<?php
            
            /**
             * Filters the text value to use on the button when adding.
             
             *       
             * @param string $value
             *            Text to use for the button.
             */
            ?>
					<input type="submit" class="button-primary" name="taxonomy_submit"
						value="<?php echo esc_attr__( 'Add Taxonomy', WPG_TEXT_DOMAIN ); ?>" />
				<?php } ?>

				<?php if ( ! empty( $current ) ) { ?>
					<input type="hidden" name="original_taxonomy_name" id="original_taxonomy_name"
						value="<?php echo $current->name; ?>" />
				<?php
        }
        
        // Used to check and see if we should prevent duplicate slugs. ?>
				<input type="hidden" name="taxonomy_check_operation" id="taxonomy_check_operation"
						value="<?php echo $action; ?>" />
				</p>
			</div>
		</div>
	</form>

<!-- End .wrap -->
<?php
    }
    private function add_update_taxonomy($data = array())
    {
        global $wpg_taxonomy_edit;
        
        if (empty($data['custom_taxonomy_data']['name'])) {
            return wpg_admin_notices('error', '', false, esc_html__('Please provide a taxonomy name', WPG_TEXT_DOMAIN));
        }
        
        if (empty($data['taxonomy_related_post_types'])) {
            return wpg_admin_notices('error', '', false, esc_html__('Please provide a post type to attach to.', WPG_TEXT_DOMAIN));
        }
        
        if (! empty($data['original_taxonomy_name']) && $data['original_taxonomy_name'] != $data['custom_taxonomy_data']['name']) {
            if (! empty($data['update_taxonomy'])) {
                add_filter('convert_taxonomy_terms', '__return_true');
            }
        }
        if (false !== strpos($data['custom_taxonomy_data']['name'], '\'') || false !== strpos($data['custom_taxonomy_data']['name'], '\"') || false !== strpos($data['rewrite_data']['slug'], '\'') || false !== strpos($data['rewrite_data']['slug'], '\"')) {
            
            add_filter('wpg_custom_error_message', 'wpt_slug_has_quotes');
            return 'error';
        }        
        
        
        /*
         * custom_taxonomy_data
         * cpt_tax_labels
         * taxonomy_related_post_types
         */
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = sanitize_text_field($value);
            } else {
                array_map('sanitize_text_field', $data[$key]);
            }
        }
        
        
        
        
        
        
        
        
        $wpct_taxonomy = $this->converter_data_to_taxonomy(
            $data,
            $data['cpt_tax_labels'],
            $data['taxonomy_related_post_types']);
        
        
        
        $taxonomies = get_taxonomy_data();
        
        /**
         * Check if we already have a post type of that name.
         
         *
         * @param bool $value
         *            Assume we have no conflict by default.
         * @param string $value
         *            Post type slug being saved.
         * @param array $post_types
         *            Array of existing post types from WPG.
         */
        $slug_exists = apply_filters('wpg_taxonomy_slug_exists', false, $data['custom_taxonomy_data']['name'], $taxonomies);
        if (true === $slug_exists) {
            add_filter('wpg_custom_error_message', 'wpt_slug_taxonomy_already_registered');
            return 'error';
        }        
        
        $taxonomies[$data['custom_taxonomy_data']['name']] = $wpct_taxonomy;
        $success = update_option(WPG_Taxonomy_Data::FIELD_OPTION, $taxonomies);
        
        
        // Used to help flush rewrite rules on init.
        set_transient('wpt_flush_rewrite_rules', 'true', 5 * 60);
        
        if (isset($success)) {
            if (WPG_Page_Action::ADDING == $data['taxonomy_check_operation']) {
                return 'add_success';
            }
        }
        
        return 'update_success';
    }
    /**
     * Build a html select of custom taxonomies.
     *
     * @param array $taxonomies
     *            Array of taxonomies that are registered. Optional.
     */
    private function build_taxonomies_html_select($taxonomies = array())
    {
        $ui_builder = new WPG_Taxonomy_UI_Builder();
        
        if (! empty($taxonomies)) {
            $select = array();
            $options = array();
            
            foreach ($taxonomies as $tax) {
                $text = (! empty($tax->label)) ? $tax->label : $tax->name;
                array_push($options, array(
                    'attr' => $tax->name,
                    'text' => $text
                ));
            }
            
            $current = $this->get_current_taxonomy_slug();
            
            $select['selected'] = $current;
            echo $ui_builder->get_select_input(array(
                'namearray' => 'selected_taxonomy',
                'name' => 'taxonomy',
                'wrap' => false,
                'attr' => array(
                    'onchange' => 'this.form.submit()'
                ),
                'options' => $options,
                'selected' => $current
            ));
        }
    }
    
    /**
     * Get the selected taxonomy from the $_POST global.
     *
     * @param bool $taxonomy_deleted
     *            Whether or not a taxonomy was recently deleted. Optional. Default false.
     * @return bool|string False on no result, sanitized taxonomy slug if set.
     */
    private function get_current_taxonomy_slug($taxonomy_deleted = false)
    {
        $tax = false;
        
        if (! empty($_POST)) {
            if (isset($_POST['selected_taxonomy']['taxonomy'])) {
                $tax = sanitize_text_field($_POST['selected_taxonomy']['taxonomy']);
            } else if ($taxonomy_deleted) {
                $taxonomies = $this->get_taxonomy_by_screen();
                $tax = key($taxonomies);
            } else if (isset($_POST['custom_taxonomy_data']['name'])) {
                // Return the submitted value.
                if (! in_array($_POST['custom_taxonomy_data']['name'], wp_and_plugins_reserved_terms(), true)) {
                    $tax = sanitize_text_field($_POST['custom_taxonomy_data']['name']);
                } else {
                    // If the submit was failed
                    if (isset($_POST['original_taxonomy_name'])) {
                        // EDITING EXISTED- Return the original value since user tried to submit a reserved term.
                        $tax = sanitize_text_field($_POST['original_taxonomy_name']);
                    } else {
                        // NEW -Do nothing
                    }
                }
            }
        } else if (! empty($_GET) && isset($_GET['wpg_taxonomy'])) {
            $tax = sanitize_text_field($_GET['wpg_taxonomy']);
        } else {
            $taxonomies = $this->get_taxonomy_by_screen();
            if (! empty($taxonomies)) {
                // Will return the first array key.
                $tax = key($taxonomies);
            }
        }
        /**
         * Filters the current taxonomy to edit.
         *
         * @param string $tax
         *            Taxonomy slug.
         */
        return apply_filters('current_taxonomy', $tax);
    }
}
$wpg_taxonomy_edit = new WPG_Taxonomy_Edit();





/**
 * Delete a custom taxonomy from the array of taxonomies.
 *
 * @param array $data
 *            The $_POST values. Optional.
 * @return bool|string False on failure, string on success.
 */
function delete_taxonomy($data = array())
{
    if (is_string($data) && taxonomy_exists($data)) {
        $data = array(
            'custom_taxonomy_data' => array(
                'name' => $data
            )
        );
    }
    
    // Check if they selected one to delete.
    if (empty($data['custom_taxonomy_data']['name'])) {
        return wpg_admin_notices('error', '', false, esc_html__('Please provide a taxonomy to delete', WPG_TEXT_DOMAIN));
    }
    
    $taxonomies = get_taxonomy_data();
    
    if (array_key_exists(strtolower($data['custom_taxonomy_data']['name']), $taxonomies)) {
        
        unset($taxonomies[$data['custom_taxonomy_data']['name']]);
        
        $success = update_option(WPG_Taxonomy_Data::FIELD_OPTION, $taxonomies);
        
    }
    
    // Used to help flush rewrite rules on init.
    set_transient('wpt_flush_rewrite_rules', 'true', 5 * 60);
    
    if (isset($success)) {
        return 'delete_success';
    }
    return 'delete_fail';
}

/**
 * Add to or update data options regards taxonomy.
 
 *       
 * @internal
 *
 * @param array $data
 *            Array of taxonomy data to update. Optional.
 * @return bool|string False on failure, string on success.
 */


/**
 * Convert taxonomies.
 
 *       
 * @internal
 *
 * @param string $original_slug
 *            Original taxonomy slug. Optional. Default empty string.
 * @param string $new_slug
 *            New taxonomy slug. Optional. Default empty string.
 */
function convert_taxonomy_terms($original_slug = '', $new_slug = '')
{
    //TODO Validar 
    global $wpdb;
    
    $args = array(
        'taxonomy' => $original_slug,
        'hide_empty' => false,
        'fields' => 'ids'
    );
    
    $term_ids = get_terms($args);
    
    if (is_int($term_ids)) {
        $term_ids = (array) $term_ids;
    }
    
    if (is_array($term_ids) && ! empty($term_ids)) {
        $term_ids = implode(',', $term_ids);
        
        $query = "UPDATE `{$wpdb->term_taxonomy}` SET `taxonomy` = %s WHERE `taxonomy` = %s AND `term_id` IN ( {$term_ids} )";
        
        $wpdb->query($wpdb->prepare($query, $new_slug, $original_slug));
    }
    delete_taxonomy($original_slug);
}

/**
 * Checks if we are trying to register an already registered taxonomy slug.
 
 *       
 * @param bool $slug_exists
 *            Whether or not the post type slug exists. Optional. Default false.
 * @param string $taxonomy_slug
 *            The post type slug being saved. Optional. Default empty string.
 * @param array $taxonomies
 *            Array of wpg-registered post types. Optional.
 *            
 * @return bool
 */
function slugs_taxonomy_exists($slug_exists = false, $taxonomy_slug = '', $taxonomies = array())
{
    // If true, then we'll already have a conflict, let's not re-process.
    if (true === $slug_exists) {
        return $slug_exists;
    }
    
    // Check if taxonomy slug is already registered.
    if (array_key_exists(strtolower($taxonomy_slug), $taxonomies)) {
        return true;
    }
    
    // Check if we're registering a reserved post type slug.
    if (in_array($taxonomy_slug, wp_and_plugins_reserved_terms())) {
        return true;
    }
    
    // Check if other plugins have registered this same slug.
    $registered_taxonomies = get_post_types(array(
        '_builtin' => false,
        'public' => false
    ));
    if (in_array($taxonomy_slug, $registered_taxonomies)) {
        return true;
    }
    
    // If we're this far, it's false.
    return $slug_exists;
}
add_filter('wpg_taxonomy_slug_exists', 'slugs_taxonomy_exists', 10, 3);

/**
 * Handle the conversion of taxonomy terms.
 *
 * This function came to be because we needed to convert AFTER registration.
 
 */
function init_convert_taxonomy_terms()
{
    
    /**
     * Whether or not to convert taxonomy terms.
     
     *       
     * @param bool $value
     *            Whether or not to convert.
     */
    if (apply_filters('convert_taxonomy_terms', false)) {
        convert_taxonomy_terms(sanitize_text_field($_POST['original_taxonomy_name']), sanitize_text_field($_POST['custom_taxonomy_data']['name']));
    }
}
add_action('init', 'init_convert_taxonomy_terms');

/**
 * Handles slug_exist checks for cases of editing an existing taxonomy.
 
 *       
 * @param bool $slug_exists
 *            Current status for exist checks.
 * @param string $taxonomy_slug
 *            Taxonomy slug being processed.
 * @param array $taxonomies
 *            CPTUI taxonomies.
 * @return bool
 */
function updated_taxonomy_slug_exists($slug_exists, $taxonomy_slug = '', $taxonomies = array())
{
    if ((! empty($_POST['taxonomy_check_operation']) && WPG_Page_Action::EDITING == $_POST['taxonomy_check_operation']) && ! in_array($taxonomy_slug, wp_and_plugins_reserved_terms()) && (! empty($_POST['original_taxonomy_name']) && $taxonomy_slug === $_POST['original_taxonomy_name'])) {
        $slug_exists = false;
    }
    return $slug_exists;
}
add_filter('wpg_taxonomy_slug_exists', 'updated_taxonomy_slug_exists', 11, 3);


