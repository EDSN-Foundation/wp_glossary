<?php
/**
 * Glossary Shortcode - List
 *
 * @class WPG_Shortcode_List
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPG_Shortcode_List Class
 */
class WPG_Shortcode_List {

	/**
	 * @var $order_keys
	 */
	public static $order_keys;

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		add_shortcode( 'wpg_list', array( __CLASS__, 'wpg_list' ) );
	}

	/**
	 * Widget Call Back Function
	 */
	public static function wpg_list( $args ) {
		
		ob_start();
		
		if( empty( $args ) ) {
			$args = array();
		}
		
		$item_blocks = array();
		
		if( ! isset( $args['title'] ) ) {
			$args['title'] = get_option( 'wpg_glossary_title' );
		}
		
		if( ! isset( $args['layout'] ) ) {
			$args['layout'] = get_option( 'wpg_glossary_layout' );
		}
		
		if( ! isset( $args['alphabet_set'] ) || ( isset( $args['alphabet_set'] ) && $args['alphabet_set'] == '' ) ) {
			$args['alphabet_set'] = get_option( 'wpg_glossary_alphabet_set' );
		}
		
		if( ! isset( $args['hide_empty'] ) ) {
			$args['hide_empty'] = get_option( 'wpg_glossary_hide_empty' );
		}

		if( ! isset( $args['hide_all'] ) ) {
			$args['hide_all'] = get_option( 'wpg_glossary_hide_all' );
		}
		
		if( ! isset( $args['hide_numeric'] ) ) {
			$args['hide_numeric'] = get_option( 'wpg_glossary_hide_numeric' );
		}
		
		if( ! isset( $args['template'] ) ) {
			$args['template'] = 'alphabet';
		}
		
		if( ! isset( $args['post_type'] ) || $args['post_type'] == '' ) {
		    $args['post_type'] = wpg_glossary_get_post_type();
		}
		
		if( ! isset( $args['taxonomy'] ) ) {
			$args['taxonomy'] = 'glossary_cat';
		}
		
		if( ! isset( $args['taxonomy_terms_to_include'] ) ) {
			$args['taxonomy_terms_to_include'] = false;
		}
		
		if( ! isset( $args['taxonomy_terms_to_exclude'] ) ) {
			$args['taxonomy_terms_to_exclude'] = false;
		}
		
		if( ! isset( $args['uncategorized_term_name'] ) ) {
			$args['uncategorized_term_name'] = '';
		}
		
		//Setting checkbox property
		$wpg_glossary_is_thumbnail_permited = get_option( 'wpg_glossary_thumbnail_permited' ) == 'yes';
		if($wpg_glossary_is_thumbnail_permited){
			//Setting checkbox property
			$wpg_glossary_is_thumbnail = get_option( 'wpg_glossary_thumbnail' ) == 'yes';
			//Overwrite by parameter
			if( isset($args['thumbnail'])){
				$args['thumbnail'] = $wpg_glossary_is_thumbnail = $args['thumbnail']=='no'?FALSE:TRUE;
			}
		}
		else{
			$wpg_glossary_is_thumbnail = FALSE;
		}
		
		
		
		$query_args = array(
			'post_type'			=> $args['post_type'],
			'posts_per_page'	=> -1,
			'orderby'			=> 'title',
			'order'				=> 'ASC'
		);

		if( $args['taxonomy']  !== false && $args['taxonomy'] != '' ) {
			
			$query_args['tax_query'] = array();
		
			if( $args['taxonomy_terms_to_include']  !== false ) {
				$args['taxonomy_terms_to_include'] = explode( ",", $args['taxonomy_terms_to_include'] );
			
				$query_args['tax_query'][] = array(
					'taxonomy'	=> $args['taxonomy'],
					'field'		=> 'term_id',
					'terms'		=> $args['taxonomy_terms_to_include']
				);
			}
		
			if( $args['taxonomy_terms_to_exclude']  !== false ) {
				$args['taxonomy_terms_to_exclude'] = explode( ",", $args['taxonomy_terms_to_exclude'] );
			
				$query_args['tax_query'][] = array(
					'taxonomy'	=> $args['taxonomy'],
					'field'		=> 'term_id',
					'terms'		=> $args['taxonomy_terms_to_exclude'],
					'operator'	=> 'NOT IN'
				);
			}
			
		}

		query_posts( apply_filters( 'wpg_list_query_args', $query_args ) );
		
		if( have_posts() ) :
		
			while( have_posts() ) : the_post();
			
				global $post;
			
				if( $args['template'] == 'category' ) {
					
					$terms = wp_get_post_terms( $post->ID, $args['taxonomy'], apply_filters( 'wpg_list_post_terms_query_args', array( 'parent' => 0 ) ) );
					
					if( ! empty( $terms ) ) {
					
						foreach( $terms as $term ) {
							$item_blocks[ $args['taxonomy'] . '_' . $term->term_id ][] = $post;
						
						}
					
					} else if( $args['uncategorized_term_name'] != '' ) {
					
						$item_blocks[ $args['taxonomy'] . '_0' ][] = $post;
					
					}
					
				} else {
				
					if( function_exists( 'substr' ) ) {
					
					    $first_alphabet = strtolower( substr( get_the_title(), 0, 1) );
						
					} else {
						
					    $first_alphabet = strtolower( substr( get_the_title(), 0, 1 ) );
						
					}
					
					if( $args['hide_numeric'] != 'yes' && is_numeric( $first_alphabet ) ) {
					
						$first_alphabet = '0-9';
					
					}
				
					if( ! empty( $first_alphabet ) ) {
				
						$item_blocks[ $first_alphabet ][] = $post;
					
					}
					
				}
				
			endwhile;
			
			ksort( $item_blocks );
			
		endif;
			
		wp_reset_query();
		
		if( ! empty( $item_blocks ) ) :
		
			$item_blocks = apply_filters( 'wpg_list_items', $item_blocks );
			
			$filters_arr = array();
			
			if( $args['template'] == 'category' ) {
							
				// Glossary Categories Array - Start
			
					$terms = get_terms( $args['taxonomy'], apply_filters( 'wpg_list_terms_query_args', array( 'parent' => 0, 'hide_empty' => false ) ) );
					
					if( ! empty( $terms ) ) {
					
						$filter_arr = array();
						
						foreach( $terms as $term ) {
						
							$filter_arr[ $args['taxonomy'] . '_' . $term->term_id ] = $term->name;
						
						}
						
						$filters_arr[] = $filter_arr;
						
					}
					
					if( $args['uncategorized_term_name'] != '' ) {
						
						$filters_arr[0][ $args['taxonomy'] . '_0' ] = $args['uncategorized_term_name'];
					
					}
					
				// Glossary Categories Array - End
			
			} else {
			
				// Glossary Alphabets Array - Start
				
					if( $args['alphabet_set'] != '' ) {
					
						$alphabet_set = $args['alphabet_set'];
					
						$alphabet_sets = explode( PHP_EOL, $alphabet_set );
						
						foreach( $alphabet_sets as $alphabet_set ) {
							
							$alphabet_set = explode( ",", $alphabet_set );
						
							$alphabet_set = array_map( 'trim', $alphabet_set );
							
							$alphabet_set = array_filter( $alphabet_set );
						
							$filters_arr[] = array_combine( array_values( array_map( 'strtolower', $alphabet_set ) ), $alphabet_set );
							
						}
						
					} else {
					
						$filters_arr[] = array_combine( array_map( 'strtolower', range( 'A','Z' ) ), range( 'A','Z' ) );
					
					}
					
					if( ! empty( $filters_arr ) ) {
						
						if( $args['hide_numeric'] != 'yes' ) {
				
							$filters_arr[0] = array( '0-9' => '0-9' ) + $filters_arr[0];
				
						}
					
					}
					
				// Glossary Alphabets Array - End
			
			}
			
			if( ! empty( $filters_arr ) && $args['hide_empty'] == 'yes' ) {
			
				foreach( $filters_arr as $filter_key => $filter_arr ) {
				
					foreach( $filter_arr as $filter_base => $filter_label ) {
		
						if( ! isset( $item_blocks[ $filter_base ] ) ) {
		
							unset( $filters_arr[ $filter_key ][ $filter_base ] );
		
						}
		
					}
				
				}
	
			}
			
			if( ! empty( $filters_arr ) ) {
						
				if( $args['hide_all'] != 'yes' ) {
					
					$wpg_glossary_label_all = wpg_glossary_get_label_all();
					
					$filters_arr[0] = array( 'all' => $wpg_glossary_label_all ) + $filters_arr[0];
		
				}
			
			}
			
			if( empty( $filters_arr ) ) {
			
				return;
			
			}
			
			// Sort Item Blocks
			
			self::$order_keys = self::get_order_keys( $item_blocks, $filters_arr );
			
			uksort( $item_blocks, array( __CLASS__, 'sort_item_blocks' ) );
			
			// Glossary Wrapper - Start
			
				echo apply_filters( 'wpg_list_wrapper_start', '<div class="wpg-list-wrapper wpg-list-wrapper-template-' . str_replace( "_", "-", $args['template'] ) . '">', $args['template'] );
				
					// Glossary Filter - Start
					
						echo apply_filters( 'wpg_list_filter_container_start', '<div class="wpg-list-filter-container">' );
						
							if( ! empty( $filters_arr ) ) {
							
								$active_filter = false;
				
								echo apply_filters( 'wpg_list_filter_start', '<div class="wpg-list-filter">' );
							
									foreach( $filters_arr as $filter_key => $filter_arr ) {
									
										if( ! empty( $filter_arr ) ) {
										
											echo apply_filters( 'wpg_list_filter_row_start', '<span class="wpg-list-filter-row">' );
			
												foreach( $filter_arr as $filter_base => $filter_label ) {
										
													// Filter Classes - Start
											
														$filter_class = '';
										
														if( $filter_base != 'all' && ! isset( $item_blocks[ $filter_base ] ) ) {
										
															$filter_class .= ' filter-disable';
										
														} else {
										
															$filter_class .= ' filter';
											
															if( ! $active_filter ) {
											
																$filter_class .= ' active';
											
																$active_filter = true;
											
															}
										
														}
												
													// Filter Classes - End
											
													// Data Filter Attr - Start
											
														$data_filter = '';
										
														if( $filter_base == 'all' ) {
										
															$data_filter = $filter_base;
										
														} else {
										
															$data_filter = '.wpg-filter-' . $filter_base;
												
														}
												
													// Data Filter Attr - End
				
													echo '<a class="' . $filter_class . '" data-filter="' . $data_filter . '">' . $filter_label . '</a>';
										
												}
												
											echo apply_filters( 'wpg_list_filter_row_end', '</span>' );
											
										}
										
									}
						
								echo apply_filters( 'wpg_list_filter_end', '</div>' );
					
							}
							
						echo apply_filters( 'wpg_list_filter_container_end', '</div>' );
					
					// Glossary Filter - End
					
					// Glossary List - Start
					
						echo apply_filters( 'wpg_list_start', '<div class="wpg-list wpg-list-template-' . str_replace( "_", "-", $args['layout'] ) . '">', $args['layout'] );
						
							foreach( $item_blocks as $filter_base => $items ) {
							
								// Glossary List Block - Start
								
									echo apply_filters( 'wpg_list_block_start', '<div class="wpg-list-block wpg-filter-'.$filter_base.' mix">', $filter_base );
									
										// Glossary List Block Heading - Start
								
											echo apply_filters( 'wpg_list_block_heading_start', '<h3 class="wpg-list-block-heading">' );
											
												$filter_base_label = ucfirst( $filter_base );
											
												if( ! empty( $filters_arr ) ) {
												
													foreach( $filters_arr as $filter_key => $filter_arr ) {
				
														if( isset( $filter_arr[ $filter_base ] ) ) {
														
															$filter_base_label = $filter_arr[ $filter_base ];
															
															break;
														
														}
														
													}
													
												}
											
												echo $filter_base_label;
								
											echo apply_filters( 'wpg_list_block_heading_end', '</h3>' );
							
										// Glossary List Block Heading - End
										
										// Glossary Block Items - Start
										
											echo apply_filters( 'wpg_list_items_start', '<ul class="wpg-list-items">' );
											
												$wpg_glossary_is_tooltip = wpg_glossary_is_tooltip();
												
												if( $wpg_glossary_is_tooltip ) {
													
													$wpg_glossary_is_tooltip_disable_on_index = wpg_glossary_is_tooltip_disable_on_index();
													
													if( $wpg_glossary_is_tooltip_disable_on_index ) {
														
														$wpg_glossary_is_tooltip = false;
														
													}
													
												}
																
												$wpg_glossary_is_disable_link = wpg_glossary_is_disable_link();
												
												$wpg_glossary_is_new_tab = wpg_glossary_is_new_tab();
												
												$wpg_glossary_is_tooltip_content_shortcode = wpg_glossary_is_tooltip_content_shortcode();
												
												$wpg_glossary_is_tooltip_content_read_more = wpg_glossary_is_tooltip_content_read_more();
											
												foreach( $items as $post ) {
												
													setup_postdata( $post );
											
													// Glossary Item - Start
				
														echo apply_filters( 'wpg_list_item_start', '<li class="wpg-list-item">' );
				
															// Glossary Item Title - Start
															
																$title = get_the_title();
															
																if( $wpg_glossary_is_disable_link ) {
																
																	$href = '';
																
																} else {
																	
																	$href = 'href="' . esc_url( get_permalink() ) . '"';
																	
																}
																
																if( $wpg_glossary_is_tooltip ) {																
																	$attr_title = wpg_glossary_get_tooltip_content( $wpg_glossary_is_tooltip_content_shortcode, $wpg_glossary_is_tooltip_content_read_more );																	
																} else {															
																	$attr_title = $title;																
																}


																$element_item_start = '<a class="wpg-list-item-title '.($wpg_glossary_is_tooltip?'wpg-tooltip':'').'" title="' . $attr_title . '" ' . $href . ' '. ( $wpg_glossary_is_new_tab ? 'target="_blank"' : '' ) .'>';
																$element_item_content = $title;
																$element_item_end = '</a>';
																if( $wpg_glossary_is_thumbnail ) {
																	$wp_glossary_img_id = get_post_meta( $post->ID, 'wp_glossary_custom_thumbnail', true );
																	$element_img = $wp_glossary_img_id?wp_get_attachment_image_src($wp_glossary_img_id , 'thumbnail' )[0]:NULL;
																	if(empty($element_img)){
																		$element_img = get_the_post_thumbnail_url(get_the_ID(),'post-thumbnail');
																		if(empty($element_img)){
																			$element_img = plugins_url( './assets/images/avatar.png', dirname(__FILE__) );
																		}
																	}
																	$element_item_content = '<div>
																					<div class="avatar">
																						<img src="'.esc_url($element_img).'" style="height:70px;width:65px">
																					</div>
																					<div class="avatar_content">'.$title.'</div>
																				</div>';
																}
																
																echo apply_filters( 'wpg_list_item_title_link_start', $element_item_start , get_permalink(), $attr_title, $wpg_glossary_is_tooltip );
																echo apply_filters( 'wpg_list_item_title_link_content', $element_item_content , get_permalink(), $attr_title, $wpg_glossary_is_tooltip );																
																echo apply_filters( 'wpg_list_item_title_link_end', $element_item_end , get_permalink(), $attr_title, $wpg_glossary_is_tooltip);
														
															// Glossary Item Title - End
					
														echo apply_filters( 'wpg_list_item_end', '</li>' );
				
													// Glossary Item - End
													
												}
												
												wp_reset_postdata();
										
											echo apply_filters( 'wpg_list_items_end', '</ul>' );
											
										// Glossary Block Items - End
										
									echo apply_filters( 'wpg_list_block_end', '</div>' );
																			
								// Glossary List Block - End
							
							}
						
						echo apply_filters( 'wpg_list_end', '</div>' );
				
						echo '<div class="wpg-clearfix"></div>';
					
					// Glossary List - End
				
				echo apply_filters( 'wpg_list_wrapper_end', '</div>' );
		
			// Glossary Wrapper - End
		
		endif;
		
		return ob_get_clean();
		
	}
	
	/**
	 * Sort Item Blocks Array by Custom Order
	 */
	public static function sort_item_blocks( $a, $b ) {
		$pos_a = array_search( $a, self::$order_keys );
		$pos_b = array_search( $b, self::$order_keys );

		if( $pos_a === false ) {
			return 1;
		}
			
		if( $pos_b === false ) {
			return -1;
		}

		return $pos_a - $pos_b;
	}
	
	/**
	 * Get Order Keys to Sort Item Blocks
	 */
	public static function get_order_keys( $item_blocks, $filters_arr ) {
		$item_block_keys = array_keys( $item_blocks );		
		$order_keys = call_User_Func_Array( 'array_Merge', $filters_arr );
		$order_keys = array_keys( $order_keys );		
		$item_block_missing_keys = array_diff( $item_block_keys, $order_keys );		
		$order_keys = array_merge( $order_keys, $item_block_missing_keys );
		return $order_keys;
	}
}

new WPG_Shortcode_List();
