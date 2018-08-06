<?php
class WPG_Page_Taxonomy_List
{
    
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
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
    
    public function render(){
        ?>
<div class="wrap wpg-listings">
			<?php
			$taxonomies = $this->get_taxonomy_by_screen();
    
    if (! empty($taxonomies)) {
        $taxonomy_table_heads = array(
            __('Taxonomy', WPG_TEXT_DOMAIN),
            __('Settings', WPG_TEXT_DOMAIN),
            __('Post Types', WPG_TEXT_DOMAIN),
            __('Labels', WPG_TEXT_DOMAIN),
            __('Template Hierarchy', WPG_TEXT_DOMAIN)
        );
        ?>
				<table class="wp-list-table widefat taxonomy-listing">
		<thead>
			<tr>
						<?php
        foreach ($taxonomy_table_heads as $head) {
            echo '<th>' . esc_html($head) . '</th>';
        }
        ?>
					</tr>
		</thead>
		<tbody>
					<?php
        $counter = 1;
        foreach ($taxonomies as $taxonomy => $taxonomy_settings) {
            
            $rowclass = (0 === $counter % 2) ? '' : 'alternate';
            
            $strings = array();
            $object_types = array();
            foreach ($taxonomy_settings as $settings_key => $settings_value) {
                if ('labels' === $settings_key) {
                    continue;
                }
                
                if (is_string($settings_value)) {
                    $strings[$settings_key] = $settings_value;
                } else {
                    if ('object_type' === $settings_key) {
                        $object_types[$settings_key] = $settings_value;
                        
                        // In case they are not associated from the post type settings.
                        if (empty($object_types['object_type'])) {
                            $types = get_taxonomy($taxonomy);
                            $object_types['object_type'] = $types->object_type;
                        }
                    }
                }
            }
            ?>
							<tr class="<?php echo esc_attr( $rowclass ); ?>">
								<?php
            $edit_path = 'edit.php?post_type=' . wpg_glossary_get_post_type() . '&page=wpg_taxonomies&action=edit&wpg_taxonomy=' . $taxonomy;
            $taxonomy_link_url = (is_network_admin()) ? network_admin_url($edit_path) : admin_url($edit_path);
            ?>
								<td>
									<?php
            
printf('<a href="%s">%s</a>', esc_attr($taxonomy_link_url), sprintf(esc_html__('Edit %s', WPG_TEXT_DOMAIN), esc_html($taxonomy)));
            ?>
								</td>
				<td>
									<?php
            foreach ($strings as $key => $value) {
                printf('<strong>%s:</strong> ', esc_html($key));
                if (in_array($value, array(
                    '1',
                    '0'
                ))) {
                    echo esc_html(disp_boolean($value));
                } else {
                    echo (! empty($value)) ? esc_html($value) : '""';
                }
                echo '<br/>';
            }
            ?>
								</td>
				<td>
									<?php
            if (! empty($object_types['object_type'])) {
                foreach ($object_types['object_type'] as $type) {
                    echo esc_html($type) . '<br/>';
                }
            }
            ?>
								</td>
				<td>
									<?php
									
									foreach ($taxonomy_settings->labels as $key => $value) {
                    printf('%s: %s<br/>', esc_html($key), esc_html($value));
                }

            ?>
								</td>
				<td>
					<p>
						<strong><?php esc_html_e( 'Archives file name examples.', WPG_TEXT_DOMAIN ); ?></strong><br />
										taxonomy-<?php echo esc_html( $taxonomy ); ?>-term_slug.php *<br />
										taxonomy-<?php echo esc_html( $taxonomy ); ?>.php<br />
						taxonomy.php<br /> archive.php<br /> index.php
					</p>

					<p>
										<?php esc_html_e( '*Replace "term_slug" with the slug of the actual taxonomy term.', WPG_TEXT_DOMAIN ); ?>
									</p>
					<p><?php
            printf('<a href="https://developer.wordpress.org/themes/basics/template-hierarchy/">%s</a>', esc_html__('Template hierarchy Theme Handbook', WPG_TEXT_DOMAIN));
            ?></p>
				</td>
			</tr>

						<?php
            $counter ++;
        }
        ?>
					</tbody>
		<tfoot>
			<tr>
						<?php
        foreach ($taxonomy_table_heads as $head) {
            echo '<th>' . esc_html($head) . '</th>';
        }
        ?>
					</tr>
		</tfoot>
	</table>
			<?php
    } else {
        
        $ui_builder.get_p(
            sprintf(esc_html__('No taxonomies registered for display. Visit %s to get started.', WPG_TEXT_DOMAIN), sprintf('<a href="%s">%s</a>', esc_attr(admin_url('admin.php?page=cptui_manage_taxonomies')), esc_html__('Add/Edit Taxonomies', WPG_TEXT_DOMAIN))));        
    }
    ?>

		</div>
<?php
    }
}