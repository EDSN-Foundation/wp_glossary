<?php
/**
 * Settings Panel
 *
 * @class WPG_Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPG_Settings
 */
class WPG_Settings {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
	
		// Add Settings Menu
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * Add Admin Sub Menu: Settings
	 */
	public function add_settings_menu() {
		add_submenu_page( 'edit.php?post_type=glossary', __( 'WP Glossary - Settings', WPG_TEXT_DOMAIN ), __( 'Settings', WPG_TEXT_DOMAIN ), 'manage_options', 'wpg-settings', array( $this, 'add_settings' ) );
	}
	
	/**
	 * Add Settings Menu Form
	 */
	public function add_settings() {
		$option_sections = self::get_settings();
		
		// Add settings form
		if( ! empty( $option_sections ) ) {
			?><div class="wrap">
				<div id="icon-tools" class="icon32"></div>
				<h2><?php _e( 'Settings', WPG_TEXT_DOMAIN ); ?></h2>
				
				<div class="wpg-settings-wrapper">
					<form method="post" action="">
					
						<div class="wpg-tabs">
							<ul>
								<?php
									foreach( $option_sections as $option_section ) {
										if( isset( $option_section['options'] ) && ! empty( $option_section['options'] ) ) {
											?><li><a href="#tab-<?php echo sanitize_title( $option_section['heading'] ); ?>"><?php echo $option_section['heading']; ?></a></li><?php
										}
									}
								?>
							</ul>
							
							<?php
								foreach( $option_sections as $option_section ) {
									?><div id="tab-<?php echo sanitize_title( $option_section['heading'] ); ?>">
									
										<?php
											if( isset( $option_section['options'] ) && ! empty( $option_section['options'] ) ) {
												?><table class="form-table">
													<tbody>
														<?php								
															foreach( $option_section['options'] as $option ) {
						
																// Default args
																$option = wp_parse_args( $option, array(
																	'type'				=> 'text',
																	'label'				=> '',
																	'desc'				=> '',
																	'placeholder'		=> '',
																	'opts'				=> array(),
																	'default'			=> '',
																	'custom_attributes'	=> array()
																) );
						
																// Option value
																$value = get_option( $option['name'] );
																if( $value === false ) {
																	$value = $option['default'];
																}
							
																// Custom attribute handling
																$custom_attributes = array();
							
																if( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
																	foreach( $option['custom_attributes'] as $attribute => $attribute_value ) {
																		$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
																	}
																}
							
																$custom_attributes = implode( ' ', $custom_attributes );
							
																// Option row
																?><tr class="field_<?php echo $option['type']; ?>">
																	<th scope="row"><label for="<?php echo $option['name']; ?>"><?php echo $option['label']; ?></label></th>
																	<td>
																		<?php
																			switch( $option['type'] ) {
											
																				case 'select':
																				case 'multiselect':
																					echo '
																						<select name="' . esc_attr( $option['name'] ) . ( $option['type'] == 'multiselect' ? '[]' : '' ) . '" id="' . esc_attr( $option['name'] ) .'" ' . $custom_attributes . ' ' . ( $option['type'] == 'multiselect' ? 'multiple="multiple"' : '' ) . '>';
													
																							if( isset( $option['opts'] ) && ! empty( $option['opts'] ) ) {
															
																								foreach( $option['opts'] as $key => $opt ) {
																
																									if( is_array( $value ) ) {
																										$selected = selected( in_array( $key, $value ), true, false );
																									} else {
																										$selected = selected( $value, $key, false );
																									}
																
																									echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_attr( $opt ) . '</option>';
																								}
																							}
														
																							echo '
																						</select>
																					';
																				break;
											
																				case 'textarea':
																					echo '
																						<textarea name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="large-text" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" rows="5" cols="50">'. esc_textarea( $value  ) .'</textarea>
																					';
																				break;
											
																				case 'checkbox':
																					echo '
																						<fieldset>
																							<legend class="screen-reader-text"><span>' . $option['label'] . '</span></legend>
														
																							<label for="' . $option['name'] . '">
																								<input type="checkbox" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" ' . $custom_attributes . ' value="1" '. checked( $value, 'yes', false ) .' /> 
																							</label>
																						</fieldset>
																					';
																				break;
													
																				case 'checkbox_group':
																					echo '
																						<fieldset>
																							<legend class="screen-reader-text"><span>' . $option['label'] . '</span></legend>';
														
																							if( isset( $option['opts'] ) && ! empty( $option['opts'] ) ) {
															
																								foreach( $option['opts'] as $key => $opt ) {
																
																									if( is_array( $value ) ) {
																										$checked = checked( in_array( $key, $value ), true, false );
																									} else {
																										$checked = checked( $value, $key, false );
																									}
																
																									echo '
																										<label for="' . esc_attr( $option['name'] . '_' . $key ) . '">
																											<input type="checkbox" name="' . esc_attr( $option['name'] ) . '[]" id="' . esc_attr( $option['name'] . '_' . $key ) .'" ' . $custom_attributes . ' value="' . esc_attr( $key ) . '" ' . $checked . ' /> ' . esc_attr( $opt ) . '

																										</label><br />
																									';
																								}
																							}
														
																							echo '
																						</fieldset>
																					';
																				break;
											
																				case 'radio':
																					echo '
																						<fieldset>
																							<legend class="screen-reader-text"><span>' . $option['label'] . '</span></legend>';
														
																							if( isset( $option['opts'] ) && ! empty( $option['opts'] ) ) {
															
																								foreach( $option['opts'] as $key => $opt ) {
																
																									echo '
																										<label for="' . esc_attr( $option['name'] . '_' . $key ) . '">
																											<input type="radio" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] . '_' . $key ) .'" ' . $custom_attributes . ' value="' . esc_attr( $key ) . '" ' . checked( $value, $key, false ) . ' /> ' . esc_attr( $opt ) . '

																										</label><br />
																									';
																								}
																							}
														
																							echo '
																						</fieldset>
																					';
																				break;
													
																				case 'colour':
																					echo '
																						<input type="text" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="wpg_cpick" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" value="' . esc_attr( $value ) . '" />
																					';
																				break;
											
																				default:
																					echo '
																						<input type="' . esc_attr( $option['type'] ) . '" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="regular-text" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" value="' . esc_attr( $value ) . '" />
																					';

																			}
										
																			if( ! empty( $option['desc'] ) ) {
																				echo '<span class="wpg-tooltip" title="'.esc_html($option['desc']).'">&nbsp;</span>';
													
																			}
																		?>
																	</td>
																</tr><?php
															}
														?>
													</tbody>
												</table><?php
											}
										?>
										
									</div><?php
								}
							?>
						</div>
						
						<input type="hidden" name="action" value="wpg_settings" />
						<?php submit_button(); ?>
					</form>
				</div>
			</div><?php
		}
	}
	
	/**
	 * Save Settings Menu Form
	 */
	public function save_settings() {
		if( empty( $_POST ) ) {
			return false;
		}
		
		if( isset( $_POST['action'] ) && $_POST['action'] == 'wpg_settings' ) {
		
			// Options to update will be stored here and saved later
			$update_options = array();
			
			// Loop options and get values to save
			$option_sections = self::get_settings();
			
			if( ! empty( $option_sections ) ) {
				foreach( $option_sections as $option_section ) {
				
					if( isset( $option_section['options'] ) && ! empty( $option_section['options'] ) ) {
						foreach( $option_section['options'] as $option ) {
							if( ! isset( $option['name'] ) || ! isset( $option['type'] ) ) {
								continue;
							}
					
							// Get posted value
							if( strstr( $option['name'], '[' ) ) {
								parse_str( $option['name'], $option_name_array );
								$option_name	= current( array_keys( $option_name_array ) );
								$setting_name	= key( $option_name_array[ $option_name ] );
								$raw_value		= isset( $_POST[ $option_name ][ $setting_name ] ) ? wp_unslash( $_POST[ $option_name ][ $setting_name ] ) : null;
							} else {
								$option_name	= $option['name'];
								$setting_name	= '';
								$raw_value		= isset( $_POST[ $option['name'] ] ) ? wp_unslash( $_POST[ $option['name'] ] ) : null;
							}
					
							// Format the value based on option type
							switch( $option['type'] ) {
								case 'checkbox' :
									$value = is_null( $raw_value ) ? 'no' : 'yes';
								break;
							
								case 'textarea' :
									$value = wp_kses_post( trim( $raw_value ) );
								break;
						
								default :
									$value = is_array( $raw_value ) ? array_map( 'sanitize_text_field', $raw_value ) : sanitize_text_field( $raw_value );
								break;
							}
					
							// Check if option is an array and handle that differently to single values.
							if( $option_name && $setting_name ) {
								if( ! isset( $update_options[ $option_name ] ) ) {
									$update_options[ $option_name ] = get_option( $option_name, array() );
								}
								if( ! is_array( $update_options[ $option_name ] ) ) {
									$update_options[ $option_name ] = array();
								}
								$update_options[ $option_name ][ $setting_name ] = $value;
							} else {
								$update_options[ $option_name ] = $value;
							}
						}
					}
					
				}
				
				// Save all options in our array
				foreach( $update_options as $name => $value ) {
					update_option( $name, $value );
				}

				return true;
			}
		}
	}
	
	/**
	 * Setting Options
	 */
	public static function get_settings() {
		$option_sections = array(
			'section_index'									=> array(
				'heading'									=> __( 'Glossary Index Page', WPG_TEXT_DOMAIN ),
				'options'									=> array(
					'wpg_glossary_title'					=> array(
						'name'								=> 'wpg_glossary_title',
						'label'								=> __( 'Glossary Title', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> 'Glossary',
						'desc'								=> __( 'This option allows you to change the glossary title wholeover the website.', WPG_TEXT_DOMAIN )
					),
			
					'wpg_glossary_slug'						=> array(
						'name'								=> 'wpg_glossary_slug',
						'label'								=> __( 'Glossary Slug', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> 'glossary',
						'desc'								=> __( 'This options allows you to choose the seo slug for the glossary terms permalink. By default, its value is <strong>glossary</strong> and you can change it with whatever you want. <br /><br /><strong>WARNING!</strong> If you already have same seo slug running for another post or page, this can interrupt/effect the behaviour of current plugin so make sure that you use a unique seo slug for this.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_archive'					=> array(
						'name'								=> 'wpg_glossary_archive',
						'label'								=> __( 'Glossary Archive', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( "In case you want to use same slug for Glossary Index Page which you have already used for <strong>Glossary Slug</strong>, WordPress doesn't allow it as glossary slug has already been reserved for glossary post type archive there. You can disable this option in order to use same slug for Glossary Index Page and Glossary Slug.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_page_id'					=> array(
						'name'								=> 'wpg_glossary_page_id',
						'label'								=> __( 'Glossary Page/Post ID', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'desc'								=> __( 'Enter the page/post ID you are using as Glossary Index Page by placing shortcode <strong>[wpg_list]</strong>.', WPG_TEXT_DOMAIN )
					),
			
					'wpg_glossary_layout'					=> array(
						'name'								=> 'wpg_glossary_layout',
						'label'								=> __( 'Glossary Page Layout', WPG_TEXT_DOMAIN ),
						'type'								=> 'select',
						'opts'								=> apply_filters( 'wpg_setting_layouts', array(
							'one_column'					=> __( '1 Column', WPG_TEXT_DOMAIN ),
							'two_column'					=> __( '2 Column', WPG_TEXT_DOMAIN ),
							'three_column'					=> __( '3 Column', WPG_TEXT_DOMAIN ),
							'four_column'					=> __( '4 Column', WPG_TEXT_DOMAIN ),
							'five_column'					=> __( '5 Column', WPG_TEXT_DOMAIN )
						) ),
						'default'							=> 'three_column',
						'desc'								=> __( 'Select the number of columns you want per row on Glossary Index Page. By default, it shows three columns per row but you can change it to any from one to five columns.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_alphabet_set'				=> array(
						'name'								=> 'wpg_glossary_alphabet_set',
						'label'								=> __( 'Alphabet Set', WPG_TEXT_DOMAIN ),
						'type'								=> 'textarea',
						'custom_attributes'					=> array( 'rows' => 5 ),
						'desc'								=> __( 'By default, plugin uses A to Z alphabet set for filter on Glossary Index Page. Suppose you want to: <br /><ul><li>change the order of the alphabets</li><li>show only few alphabets</li><li>show a different set of characters</li><li>show characters list for other language (eg: Hebrew, Greek )</li><li>show multiple rows rather than all the alphabets in single row or all English alphabets in first row and Hebrew in next row</ul>You can use this option for all these. Use comma (,) for multiple alphabets. For multiple rows, simply use new line (ENTER from keyboard). <br /><br /><strong>EG:</strong> <br />A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z <br />А,Б,В,Г,Д,Е,Ж,З,И,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Э,Ю,Я', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_animation'				=> array(
						'name'								=> 'wpg_glossary_animation',
						'label'								=> __( 'Glossary Animation', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'An option dictating whether or not animation should be enabled for the Glossary Index Page. If false, all operations will occur instantly and syncronously.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_hide_empty'				=> array(
						'name'								=> 'wpg_glossary_hide_empty',
						'label'								=> __( 'Hide Empty Alphabets', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( "In case, some of the alphabets from filter list, on Glossary Index Page, doesn't have any glossary term/phrase. These alphabets will show as disabled in the list. Selecting this option will remove these disabled alphabets from the list completely.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_hide_all'					=> array(
						'name'								=> 'wpg_glossary_hide_all',
						'label'								=> __( 'Hide "All" Filter', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Plugin, by default, shows <strong>ALL</strong> at the starting of the filter list on Glossary Index Page which shows all the glossary terms/phrases when clicked. For example, you click on alphabet <strong>A</strong> from the filter list, it will come up with all the glossary terms starting with <strong>A</strong>. Now you want to see back the whole list of terms. Clicking <strong>ALL</strong> does this for you. If you want to show it no more in the filter list, you can enable this option.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_label_all'				=> array(
						'name'								=> 'wpg_glossary_label_all',
						'label'								=> __( 'Label - All', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> 'All',
						'desc'								=> __( 'This option allows you to change the label from "All" with one you want.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_hide_numeric'				=> array(
						'name'								=> 'wpg_glossary_hide_numeric',
						'label'								=> __( 'Hide "0-9" Filter', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'This option works almost in the same way <strong>Hide "All" Filter</strong> works but for the glossary terms starting with number. Plugin shows  <strong>0-9</strong> just after <strong>All</strong> in the filter list on Glossary Index Page and you can remove it by enabling this option.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_disable_link'				=> array(
						'name'								=> 'wpg_glossary_disable_link',
						'label'								=> __( 'Disable Term Links', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( "Sometimes, you don't want one to see glossary terms details page by just clicking glossary term OR want one to click on <strong>Read More</strong> text from tooltip content before one redirects to glossary term details page. This option allows you to disable links on Glossary Index Page from glossary terms/phrases to term details page. This option will work only for Glossary Index Page and not for Linkified Terms. For Linkified Terms, please visit <strong>Glossary Linkify</strong> section from the settings. <br /><br /><strong>NOTE!</strong> Plugin still uses anchor tag for the glossary terms to show hand when one hover the terms. You can change it through CSS if don't want to show hand when taking mouse pointer over <strong>link disabled terms</strong>.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_new_tab'					=> array(
						'name'								=> 'wpg_glossary_new_tab',
						'label'								=> __( 'New Tab', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Select this option if you want to open a new tab when one clicks on glossary term.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_back_link'				=> array(
						'name'								=> 'wpg_glossary_back_link',
						'label'								=> __( 'Back Link', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Select this option if you want to add a back link at the bottom of term description on glossary term details page to Glossary Index Page. You can change the text <strong>Back to Glossary Index Page</strong> easily using filter <strong>wpg_glossary_post_back_link_text</strong> anywhere in your current theme or third party plugin.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_label_back_link'			=> array(
						'name'								=> 'wpg_glossary_label_back_link',
						'label'								=> __( 'Label - Back Link', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> 'Back to Glossary Index Page',
						'desc'								=> __( 'This option allows you to change the text from "Back to Glossary Index Page" with one you want.', WPG_TEXT_DOMAIN )
					),
				)
			),
			
			'section_linkify'								=> array(
				'heading'									=> __( 'Glossary Linkify', WPG_TEXT_DOMAIN ),
				'options'									=> array(
					'wpg_glossary_activate_linkify'			=> array(
						'name'								=> 'wpg_glossary_activate_linkify',
						'label'								=> __( 'Enable Linkify', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Enable this option will automatically hyperlink the glossary terms/phrases in content. Everytime glossary terms appear in your pages/posts, get automatically linked to the their detail pages. This way it enhances Search Engine Optimization by auto linking each highlighted phrase or term back to a dedicated term definition page.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_exclude_html_tags'	=> array(
						'name'								=> 'wpg_glossary_linkify_exclude_html_tags',
						'label'								=> __( 'HTML Tags to Exclude', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'desc'								=> __( 'By default, plugin excludes only anchor, style and script tags from linkified. You can use this option to exclude more HTML tags. <br /><br /><strong>EG:</strong> h1, h2, h3, h4, h5, h6, pre, object', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_tags'				=> array(
						'name'								=> 'wpg_glossary_linkify_tags',
						'label'								=> __( 'Linkify Tags', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Suppose you have a term and now you want its plurals/synonyms also be linkified with the same term. There is no way to check it programmatically if a phrase is a plural or synonym for existing glossary term. <br /><br />Plugin allows you to add multiple tags against each glossary term while adding/editing this particular term. That way you can add multiple plurals/synonyms for any term under these tags. Now enable this option and it will start linkifying plurals/synonyms along with glossary terms.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_disable_link'		=> array(
						'name'								=> 'wpg_glossary_linkify_disable_link',
						'label'								=> __( 'Disable Term Links', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( "Sometimes, you don't want one to see glossary terms details page by just clicking linkified glossary term OR want one to click on <strong>Read More</strong> text from tooltip content before one redirects to glossary term details page. This option allows you to disable links from linkified glossary terms/phrases to term details page.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_new_tab'			=> array(
						'name'								=> 'wpg_glossary_linkify_new_tab',
						'label'								=> __( 'New Tab', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Select this option if you want to open a new tab when one clicks on linkified glossary term.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_sections'			=> array(
						'name'								=> 'wpg_glossary_linkify_sections',
						'label'								=> __( 'Linkify Zones', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox_group',
						'opts'								=> apply_filters( 'wpg_setting_linkify_sections', array(
							'post_content'					=> __( 'Post/Page Full Description', WPG_TEXT_DOMAIN ),
							'post_excerpt'					=> __( 'Post/Page Short Description', WPG_TEXT_DOMAIN ),
							'term_content'					=> __( 'Categories/Terms Description', WPG_TEXT_DOMAIN ),							
							'widget'						=> __( 'Widgets', WPG_TEXT_DOMAIN ),
							'comment'						=> __( 'Comments', WPG_TEXT_DOMAIN )
						) ),
						'default'							=> array( 'post_content', 'widget' ),
						'desc'								=> __( "This option allows you to choose which content you want content to be linkified. Sometimes, you don't need comments or widgets text be linkified, this option allows us to disable linkify for those.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_post_types'		=> array(
						'name'								=> 'wpg_glossary_linkify_post_types',
						'label'								=> __( 'Post Types', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox_group',
						'opts'								=> apply_filters( 'wpg_setting_linkify_post_types', wpg_get_post_types() ),
						'default'							=> array( 'post' ),
						'desc'								=> __( 'Most of the times, you will require linkify to work with limited post types only and not with all. This option allows you to choose post types you want linkify to work with.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_on_front_page'	=> array(
						'name'								=> 'wpg_glossary_linkify_on_front_page',
						'label'								=> __( 'Front Page', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Enable this option will start linkifying glossary terms even on home page content as well.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_term_limit'		=> array(
						'name'								=> 'wpg_glossary_linkify_term_limit',
						'label'								=> __( 'Linkify Limit per Term', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'desc'								=> __( 'By default, plugin adds the links to all the occurrences of the glossary terms where few times, you need to linkify only first occurance of each glossary term.  This option allows you to limit maximum number of occurrences a single glossary term should be linkified for. Leave blank to linkify all the occurrences of each glossary term.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_limit_for_full_page'	=> array(
						'name'								=> 'wpg_glossary_linkify_limit_for_full_page',
						'label'								=> __( 'Linkify Limit for Full Page', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Above option <strong>Linkify Limit per Term</strong> limits occurrences of glossary terms per section ( eg: post content, comments, widgets ). This option allows you to expand this limit for full page content.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_linkify_case_sensitive'		=> array(
						'name'								=> 'wpg_glossary_linkify_case_sensitive',
						'label'								=> __( 'Case Sensitive', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Enable this option if you want glossary terms to be case-sensitive. <br /><br /><strong>EG:</strong> You have a glossary term titled <strong>Vehicles</strong>. Activate this option and it will linkify only phrase <strong>Vehicles</strong> and not <strong>vehicles</strong>. See the difference between capital and small case <strong>V</strong>.', WPG_TEXT_DOMAIN )
					)
				)
			),
			
			'section_tooltip'								=> array(
				'heading'									=> __( 'Glossary Tooltip', WPG_TEXT_DOMAIN ),
				'options'									=> array(
					'wpg_glossary_activate_tooltip'			=> array(
						'name'								=> 'wpg_glossary_activate_tooltip',
						'label'								=> __( 'Enable Tooltip', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Enable this option will start showing an info box with term definition when one hovers over the term. Plugin uses <strong>Tooltipster</strong> for this and you can find more documentation <a href="http://iamceege.github.io/tooltipster/" target="_blank"><strong>HERE</strong></a>.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_disable_tooltip_on_index'	=> array(
						'name'								=> 'wpg_glossary_disable_tooltip_on_index',
						'label'								=> __( 'Disable Tooltip on Index', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Enable this option will disable tooltip on Glossary Index Page even if you have enabled <strong>Enable Tooltip</strong> option.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_theme'			=> array(
						'name'								=> 'wpg_glossary_tooltip_theme',
						'label'								=> __( 'Tooltip Theme', WPG_TEXT_DOMAIN ),
						'type'								=> 'select',
						'opts'								=> apply_filters( 'wpg_setting_tooltip_themes', array(
							'default'						=> __( 'Default', WPG_TEXT_DOMAIN ),
							'light'							=> __( 'Light', WPG_TEXT_DOMAIN ),
							'punk'							=> __( 'Punk', WPG_TEXT_DOMAIN ),
							'noir'							=> __( 'Noir', WPG_TEXT_DOMAIN ),
							'shadow'						=> __( 'Shadow', WPG_TEXT_DOMAIN ),
						) ),
						'default'							=> 'default',
						'desc'								=> __( "Plugin tooltip comes with five different themes. You can try those to get one which fits best with your current theme. Still need some styling changes, you can do it through your current theme's style.css file if you are good with CSS.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_activate_tooltip_title'	=> array(
						'name'								=> 'wpg_glossary_activate_tooltip_title',
						'label'								=> __( 'Show Heading', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Enable this option if you want to add a heading before term description in tooltip content.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_title_format'		=> array(
						'name'								=> 'wpg_glossary_tooltip_title_format',
						'label'								=> __( 'Heading Format', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> '{TITLE}',
						'desc'								=> __( 'Enabled <strong>Show Heading</strong>, next you want to format the content for heading. This option allows you to do it. You can use <strong>{TITLE}</strong> to show hovered term title dynamically along with other static content. <br /><br /><strong>EG:</strong> This terms refers to: {TITLE}</strong>', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_content_type'		=> array(
						'name'								=> 'wpg_glossary_tooltip_content_type',
						'label'								=> __( 'Content Type', WPG_TEXT_DOMAIN ),
						'type'								=> 'select',
						'opts'								=> apply_filters( 'wpg_setting_tooltip_content_types', array(
							'excerpt'						=> __( 'Short Description ( Excerpt )', WPG_TEXT_DOMAIN ),
							'content'						=> __( 'Full Description', WPG_TEXT_DOMAIN ),
						) ),
						'default'							=> 'excerpt',
						'desc'								=> __( "This option allows you to choose either to show short description (excerpt) or full description in the tooltip. In case if a term doesn't have short description, it will be replaced by full description. Short description supports <strong><!--more--></strong> tag as well in the same way WordPress does for post excerpt. Follow this <a href='https://codex.wordpress.org/Customizing_the_Read_More' target='_blank'><strong>URL</strong></a> for more understanding of <strong>more</strong> tag. <br /><br /><strong>NOTE!</strong> You have to manually enter short description per term using the <strong>Excerpt</strong> field on the term add/edit screen.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_content_length'	=> array(
						'name'								=> 'wpg_glossary_tooltip_content_length',
						'label'								=> __( 'Content Length', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'default'							=> '',
						'desc'								=> __( 'Use this option if you want to limit the number of words in tooltip content. Leave it blank if you want to show all the words from <strong>Content Type</strong> option. <br /><br /><strong>WARNING!</strong> This option will remove/strip all the formatting from content including HTML tags.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_content_shortcode'=> array(
						'name'								=> 'wpg_glossary_tooltip_content_shortcode',
						'label'								=> __( 'Execute Shortcodes', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'You have shortcodes in glossary term content and want them to execute in the same way those execute on post/term details page. Enable this option will start executing all the shortcodes from glossary term content in the tooltip as well.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_content_read_more'=> array(
						'name'								=> 'wpg_glossary_tooltip_content_read_more',
						'label'								=> __( 'Read More', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'desc'								=> __( 'Enable this option will add a <strong>Read More</strong> link at the end of tooltip content which will redirect users to term details page. You can change the text <strong>Read More</strong> easily using filter <strong>wpg_tooltip_content_read_more_text</strong> anywhere in your current theme or third party plugin.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_label_tooltip_content_read_more'	=> array(
						'name'								=> 'wpg_glossary_label_tooltip_content_read_more',
						'label'								=> __( 'Label - Read More', WPG_TEXT_DOMAIN ),
						'type'								=> 'text',
						'default'							=> 'Read More',
						'desc'								=> __( 'This option allows you to change the text from "Read More" with one you want.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_animation'		=> array(
						'name'								=> 'wpg_glossary_tooltip_animation',
						'label'								=> __( 'Animation Type', WPG_TEXT_DOMAIN ),
						'type'								=> 'select',
						'opts'								=> apply_filters( 'wpg_setting_tooltip_animations', array(
							'fade'							=> __( 'Fade', WPG_TEXT_DOMAIN ),
							'grow'							=> __( 'Grow', WPG_TEXT_DOMAIN ),
							'swing'							=> __( 'Swing', WPG_TEXT_DOMAIN ),
							'slide'							=> __( 'Slide', WPG_TEXT_DOMAIN ),
							'fall'							=> __( 'Fall', WPG_TEXT_DOMAIN )
						) ),
						'default'							=> 'fade',
						'desc'								=> __( 'Plugin tooltip comes with five ready to use animations when one hovers term. You can play with those to get one which fits best with your current theme.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_position'			=> array(
						'name'								=> 'wpg_glossary_tooltip_position',
						'label'								=> __( 'Position', WPG_TEXT_DOMAIN ),
						'type'								=> 'select',
						'opts'								=> apply_filters( 'wpg_setting_tooltip_positions', array(
							'left'							=> __( 'Left', WPG_TEXT_DOMAIN ),
							'right'							=> __( 'Right', WPG_TEXT_DOMAIN ),
							'top'							=> __( 'Top', WPG_TEXT_DOMAIN ),
							'bottom'						=> __( 'Bottom', WPG_TEXT_DOMAIN ),
							'top-left'						=> __( 'Top Left', WPG_TEXT_DOMAIN ),
							'top-right'						=> __( 'Top Right', WPG_TEXT_DOMAIN ),
							'bottom-left'					=> __( 'Bottom Left', WPG_TEXT_DOMAIN ),
							'bottom-right'					=> __( 'Bottom Right', WPG_TEXT_DOMAIN )
						) ),
						'default'							=> 'top',
						'desc'								=> __( 'By default, tooltip shows just above the phrase when one hovers. In case if you want to change its position from top to somewhere else, you can use this option. This option allows you to choose one from Left, Right, Top, Bottom and few other combinations for tooltip position.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_activate_tooltip_arrow'	=> array(
						'name'								=> 'wpg_glossary_activate_tooltip_arrow',
						'label'								=> __( 'Bubble Arrow', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'Selecting this option will add add a <strong>speech bubble arrow</strong> to the tooltip. Default its active but you can uncheck the option to hide <strong>speech bubble arrow</strong>.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_min_width'		=> array(
						'name'								=> 'wpg_glossary_tooltip_min_width',
						'label'								=> __( 'Min Width', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'default'							=> '250',
						'desc'								=> __( 'This option allows you to change the minimum width of tooltip window from 250 to else.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_max_width'		=> array(
						'name'								=> 'wpg_glossary_tooltip_max_width',
						'label'								=> __( 'Max Width', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'default'							=> '500',
						'desc'								=> __( 'This option allows you to change the maximum width of tooltip window from 500 to else.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_speed'			=> array(
						'name'								=> 'wpg_glossary_tooltip_speed',
						'label'								=> __( 'Speed', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'default'							=> '350',
						'desc'								=> __( 'Set the speed of the animation, in milliseconds, for the tooltip. By default, its value is 350 but you can try few different values to see which one seems good with the website.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_delay'			=> array(
						'name'								=> 'wpg_glossary_tooltip_delay',
						'label'								=> __( 'Delay', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'default'							=> '200',
						'desc'								=> __( 'Set the delay how long it takes, in milliseconds, for the tooltip to start animating in. Upon mouse interaction, this is the delay before the tooltip starts its opening and closing animations. By default, its value is 200 but you can try few different values to see which one seems good with the website.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_activate_touch_devices'	=> array(
						'name'								=> 'wpg_glossary_activate_touch_devices',
						'label'								=> __( 'Touch Devices', WPG_TEXT_DOMAIN ),
						'type'								=> 'checkbox',
						'default'							=> 'yes',
						'desc'								=> __( 'This option allows you to choose whether to show tooltip on touch devices or not. Disable this option will not show the tooltips on pure-touch devices.', WPG_TEXT_DOMAIN )
					),
				)
			),
			
			'section_custom_css'							=> array(
				'heading'									=> __( 'Glossary Custom Styles', WPG_TEXT_DOMAIN ),
				'options'									=> array(
					'wpg_glossary_list_filter_font_colour'	=> array(
						'name'								=> 'wpg_glossary_list_filter_font_colour',
						'label'								=> __( 'Filter Font Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose colour using colour picker for filter list on Glossary Index Page. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_filter_active_font_colour'	=> array(
						'name'								=> 'wpg_glossary_list_filter_active_font_colour',
						'label'								=> __( 'Filter Active Font Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose colour using colour picker for active alphabet/category from filter list on Glossary Index Page. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_filter_font_size'	=> array(
						'name'								=> 'wpg_glossary_list_filter_font_size',
						'label'								=> __( 'Filter Font Size (px)', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'desc'								=> __( 'This option allows you to choose font size, in pixels, for filter list on Glossary Index Page.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_heading_bg_colour'	=> array(
						'name'								=> 'wpg_glossary_list_heading_bg_colour',
						'label'								=> __( 'Heading Background Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'default'							=> '#f4f4f4',
						'desc'								=> __( "This option allows you to choose background colour using colour picker for terms block heading on Glossary Index Page. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_heading_font_colour'	=> array(
						'name'								=> 'wpg_glossary_list_heading_font_colour',
						'label'								=> __( 'Heading Font Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'default'							=> '#777777',
						'desc'								=> __( "This option allows you to choose colour using colour picker for terms block heading on Glossary Index Page. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_heading_font_size'	=> array(
						'name'								=> 'wpg_glossary_list_heading_font_size',
						'label'								=> __( 'Heading Font Size (px)', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'desc'								=> __( 'This option allows you to choose font size, in pixels, for terms block heading on Glossary Index Page.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_terms_font_colour'	=> array(
						'name'								=> 'wpg_glossary_list_terms_font_colour',
						'label'								=> __( 'Terms Font Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose colour using colour picker for terms on Glossary Index Page. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_terms_active_font_colour'	=> array(
						'name'								=> 'wpg_glossary_list_terms_active_font_colour',
						'label'								=> __( 'Terms Hover Font Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose colour using colour picker for terms on Glossary Index Page when one hovers term. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_list_terms_font_size'		=> array(
						'name'								=> 'wpg_glossary_list_terms_font_size',
						'label'								=> __( 'Terms Font Size (px)', WPG_TEXT_DOMAIN ),
						'type'								=> 'number',
						'desc'								=> __( 'This option allows you to choose font size, in pixels, for terms on Glossary Index Page.', WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_bg_colour'		=> array(
						'name'								=> 'wpg_glossary_tooltip_bg_colour',
						'label'								=> __( 'Tooltip Background Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose background colour using colour picker for tooltip. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_border_colour'	=> array(
						'name'								=> 'wpg_glossary_tooltip_border_colour',
						'label'								=> __( 'Tooltip Border Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose border colour using colour picker for tooltip. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_heading_colour'	=> array(
						'name'								=> 'wpg_glossary_tooltip_heading_colour',
						'label'								=> __( 'Tooltip Heading Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose heading colour using colour picker for tooltip. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_content_colour'	=> array(
						'name'								=> 'wpg_glossary_tooltip_content_colour',
						'label'								=> __( 'Tooltip Content Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose content colour using colour picker for tooltip. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
					
					'wpg_glossary_tooltip_link_colour'	=> array(
						'name'								=> 'wpg_glossary_tooltip_link_colour',
						'label'								=> __( 'Tooltip Links Colour', WPG_TEXT_DOMAIN ),
						'type'								=> 'colour',
						'desc'								=> __( "This option allows you to choose link colour ( eg: anchor tag ) using colour picker for tooltip. Select <strong>Clear</strong> from colour picker if you want to use plugin's default style.", WPG_TEXT_DOMAIN )
					),
				)
			)
		);
		
		return apply_filters( 'wpg_settings', $option_sections );
	}
}

new WPG_Settings();
