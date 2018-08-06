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
	    add_submenu_page( 'edit.php?post_type='.wpg_glossary_get_post_type(), __( 'WP Glossary - Settings', WPG_TEXT_DOMAIN ), __( 'Settings', WPG_TEXT_DOMAIN ), 'manage_options', 'wpg-settings', array( $this, 'add_settings' ) );
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
					
						<div class="wpg-tabs" style="visibility: hidden;">
							<ul>
								<?php
									foreach( $option_sections as $option_section ) {
										if( isset( $option_section->options ) && ! empty( $option_section->options ) ) {
											?><li><a href="#tab-<?php echo sanitize_title( $option_section->heading ); ?>"><?php echo $option_section->heading; ?></a></li><?php
										}
									}
								?>
							</ul>
							
							<?php
								foreach( $option_sections as $option_section ) {
									?><div id="tab-<?php echo sanitize_title( $option_section->heading ); ?>">
									
										<?php
											if( isset( $option_section->options ) && ! empty( $option_section->options ) ) {
												?><table class="form-table">
													<tbody>
														<?php								
															foreach( $option_section->options as $option ) {
						
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
																						<select name="' . esc_attr( $option['name'] ) . ( $option['type'] == 'multiselect' ? '[]' : '' ) . '" id="' . esc_attr( $option['name'] ) .'" ' . $custom_attributes . ' ' . ( $option['type'] == 'multiselect' ? 'multiple="multiple"' : '' ) . ' ' . ((isset($option['required']) && $option['required'])?'required':'') . '>';
													
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
																						<textarea name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="large-text" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" rows="5" cols="50" ' . ((isset($option['required']) && $option['required'])?'required':'') . '>'. esc_textarea( $value  ) .'</textarea>
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
																						<input type="text" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="wpg_cpick" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" value="' . esc_attr( $value ) . '" ' . ((isset($option['required']) && $option['required'])?'required':'') . ' />
																					';
																				break;
											
																				default:
																					echo '
																						<input type="' . esc_attr( $option['type'] ) . '" name="' . esc_attr( $option['name'] ) . '" id="' . esc_attr( $option['name'] ) .'" class="regular-text" ' . $custom_attributes . ' placeholder="' . esc_attr( $option['placeholder'] ) . '" value="' . esc_attr( $value ) . '" ' . ((isset($option['required']) && $option['required'])?'required':'') . ' />
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
	
	private function extract_data_post() {
	    if( empty( $_POST ) ) {
	        return false;
	    }
	    $update_options = array();
	    
	    if( isset( $_POST['action'] ) && $_POST['action'] == 'wpg_settings' ) {
	        
	        
	        
	        // Loop options and get values to save
	        $option_sections = self::get_settings();
	        
	        
	        if( ! empty( $option_sections ) ) {
	            foreach( $option_sections as $option_section ) {
	                
	                if( isset( $option_section->options ) && ! empty( $option_section->options ) ) {
	                    foreach( $option_section->options as $option ) {
	                        if( ! isset( $option->name ) || ! isset( $option->type  ) ) {
	                            continue;
	                        }
	                        
	                        // Get posted value
	                        if( strstr( $option->name, '[' ) ) {
	                            parse_str( $option->name, $option_name_array );
	                            $option_name	= current( array_keys( $option_name_array ) );
	                            $setting_name	= key( $option_name_array[ $option_name ] );
	                            $raw_value		= isset( $_POST[ $option_name ][ $setting_name ] ) ? wp_unslash( $_POST[ $option_name ][ $setting_name ] ) : null;
	                        } else {
	                            $option_name	= $option->name;
	                            $setting_name	= '';
	                            $raw_value		= isset( $_POST[ $option->name ] ) ? wp_unslash( $_POST[ $option->name ] ) : null;
	                        }
	                        
	                        // Format the value based on option type
	                        switch( $option->type) {
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
	        }
	    }
	    return $update_options;
	}
	
	private function validation_data($data){
	    if(wpg_glossary_get_post_type() != $data['wpg_glossary_slug']){
	        if(in_array($data['wpg_glossary_slug'], wp_and_plugins_reserved_terms())){
	            
	            add_filter('wpg_custom_error_message', 'wpt_slug_taxonomy_already_registered');
	            
	            return FALSE;
	        }
	    }
	    if(wpg_glossary_get_post_type() != $data['wpg_glossary_post_type']){
	        if(in_array($data['wpg_glossary_post_type'], wp_and_plugins_reserved_terms())){
	            
	            add_filter('wpg_custom_error_message', 'wpt_post_type_is_reserved');
	            
	            return FALSE;
	        }
	    }         
	    
	    return TRUE;	   
	}
	
	private function update_data($data){
	    foreach( $data as $name => $value ) {
	        update_option( $name, $value );
	    }
	}
	
	public function save_settings() {	    
	    $data = $this->extract_data_post();
	    if(empty($data)){
	        return;
	    }
	    
	    if($this->validation_data($data)){
	        $post_type_before = wpg_glossary_get_post_type();
	        $post_type_after = $data['wpg_glossary_post_type'];
	        $this->update_data($data);
	        add_action('admin_notices', 'wpg_update_success_admin_notice');
	        if($post_type_before != $post_type_after){	            
	            wp_redirect(str_replace('post_type=' . $post_type_before, 'post_type=' . $post_type_after, $_SERVER['REQUEST_URI']));
	        }
	    }
	    else{
	        add_action('admin_notices', "wpg_error_admin_notice");
	    }
	    
	}	
	
	/**
	 * Setting Options
	 */
	public static function get_settings() {		
		$option_sections = json_decode(file_get_contents(dirname(__FILE__) . './../../assets/Settings.json'));
		return apply_filters( 'wpg_settings', $option_sections );
	}
}

new WPG_Settings();

