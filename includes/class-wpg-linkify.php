<?php
/**
 * Linkify Content
 *
 * @class WPG_Linkify
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPG_Linkify {

	/**
	 * @vars
	 */
	public $is_active = '';
	public $is_linkify_tags = '';
	public $exclude_html_tags = '';
	public $is_disable_link = '';
	public $is_new_tab = '';
	public $linkify_sections = '';
	public $linkify_post_types = '';
	public $is_on_front_page = '';
	public $term_limit = '';
	public $is_term_limit_for_full_page = '';
	public $is_case_sensitive = '';
	public $is_tooltip = '';
	public $is_tooltip_content_shortcode = '';
	public $is_tooltip_content_read_more = '';
	public $glossary_terms = '';
	public $glossary_term_titles = '';
	public $replaced_terms = '';

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		// Setup Linkify Vars
		add_action( 'wp', array( $this, 'setup_vars' ) );
		
		// Initiate Linkify
		add_action( 'wp', array( $this, 'init_linkify' ) );
	}
	
	/**
	 * Setup Linkify Vars
	 */
	public function setup_vars() {
		
		$this->is_active = wpg_glossary_is_linkify();
		if( ! $this->is_active ) {
			return;
		}
		
		$this->is_on_front_page = wpg_glossary_is_linkify_on_front_page();
		if( is_front_page() && ! $this->is_on_front_page ) {
			return;
		}
		
		$this->linkify_sections = wpg_glossary_get_linkify_sections();
		if( empty( $this->linkify_sections ) ) {
			return;
		}
		
		$this->is_case_sensitive = wpg_glossary_is_linkify_case_sensitive();
		$this->is_linkify_tags = wpg_glossary_is_linkify_tags();
		
		$this->exclude_html_tags = wpg_glossary_get_linkify_exclude_html_tags();
		if( $this->exclude_html_tags != '' ) {
			$this->exclude_html_tags = explode( ',', $this->exclude_html_tags );
			foreach( $this->exclude_html_tags as $key => $html_tag ) {
				$html_tag = trim( $html_tag );
				if( $html_tag != '' ) {
					$this->exclude_html_tags[ $key ] = '[not(ancestor::'.$html_tag.')]';
				}
			}
			
			$this->exclude_html_tags = implode( '', $this->exclude_html_tags );
		}
		
		$this->glossary_terms = wpg_glossary_terms();
		if( empty( $this->glossary_terms ) ) {
			return;
		}
		
		usort( $this->glossary_terms, array( $this, 'sort_glossary_terms' ) );
		$this->format_glossary_terms();		
		if( empty( $this->glossary_terms ) ) {
			return;
		}
		
		$this->glossary_term_titles = array_keys( $this->glossary_terms );
		
		$this->is_tooltip = wpg_glossary_is_tooltip();
		$this->is_tooltip_content_shortcode = wpg_glossary_is_tooltip_content_shortcode();
		$this->is_tooltip_content_read_more = wpg_glossary_is_tooltip_content_read_more();
		$this->is_new_tab = wpg_glossary_is_linkify_new_tab();
		$this->is_disable_link = wpg_glossary_is_linkify_disable_link();
		$this->linkify_post_types = wpg_glossary_get_linkify_post_types();
		$this->term_limit = wpg_glossary_get_linkify_term_limit();
		$this->is_term_limit_for_full_page = wpg_glossary_is_linkify_limit_for_full_page();
	}
	
	/**
	 * Sort Glossary Terms Array: Fixed where a term contains another term
	 * 
	 */
	public function sort_glossary_terms( $term1, $term2 ) {
		return strlen( $term2->post_title ) - strlen( $term1->post_title );
	}
	
	/**
	 * Format Glossary Terms Array
	 */
	public function format_glossary_terms() {
		$wpg_glossary_terms = array();
		
		global $post;
		foreach( $this->glossary_terms as $glossary_term ) {
			
			if( $post->ID === $glossary_term->ID ) {
				continue;
			}
			
			$wpg_glossary_terms_key = array();
			
			// Term Title
			$wpg_glossary_terms_key[] = $this->format_glossary_term_string( $glossary_term->post_title );
			
			// Term Tags
			if( $this->is_linkify_tags && ! empty( $glossary_term->terms ) ) {
				foreach( $glossary_term->terms as $key => $term ) {
					$wpg_glossary_terms_key[] = $this->format_glossary_term_string( $term );
				}
			}
			
			$wpg_glossary_terms_key = implode( "|", $wpg_glossary_terms_key );
			
			if( ! isset( $wpg_glossary_terms[ $wpg_glossary_terms_key ] ) ) {			
				$wpg_glossary_terms[ $wpg_glossary_terms_key ] = $glossary_term;			
			}
		}
		
		$this->glossary_terms = $wpg_glossary_terms;
	}
	
	/**
	 * Format Linkify String
	 */
	public function format_glossary_term_string( $string='' ) {
		
		if( $string == '' ) {
			return;
		}
		
		$string = str_replace( '&#039;', "’", preg_quote( htmlspecialchars( trim( $string ), ENT_QUOTES, 'UTF-8' ), '/' ) );
		
		if ( ! $this->is_case_sensitive ) {
		    $string = strtolower( $string );
		}
		
		return $string;
	}

	/**
	 * Init Linkify
	 */
	public function init_linkify() {
		
		// Check if Linkify is enabled or not
		if( ! $this->is_active ) {
			return;
		}
		
		if( empty( $this->linkify_sections ) ) {
			return;
		}
		
		if( empty( $this->glossary_terms ) ) {
			return;
		}
		
		// Linkify Full Description
		if( in_array( 'post_content', $this->linkify_sections ) ) {
			add_filter( 'the_content', array( $this, 'linkify_content' ), 13, 2 );
		}
		
		// Linkify Short Description
		if( in_array( 'post_excerpt', $this->linkify_sections ) ) {
			add_filter( 'the_excerpt', array( $this, 'linkify_excerpt' ), 13, 2 );
		}
		
		// Linkify Categories / Terms Description
		if( in_array( 'term_content', $this->linkify_sections ) ) {
			add_filter( 'term_description', array( $this, 'linkify_term_content' ), 13, 2 );
		}
		
		// Linkify Widget
		if( in_array( 'widget', $this->linkify_sections ) ) {
			add_filter( 'widget_text', array( $this, 'linkify_widget' ), 13, 2 );
		}
		
		// Linkify Comment
		if( in_array( 'comment', $this->linkify_sections ) ) {
			add_filter( 'get_comment_text', array( $this, 'linkify_comment' ), 13, 2 );
			add_filter( 'get_comment_excerpt', array( $this, 'linkify_comment' ), 13, 2 );
		}
	}
	
	/**
	 * Linkify Full Description
	 */
	public function linkify_content( $content ) {
		global $post;
		
		if( empty( $this->linkify_post_types ) || ( isset( $post->post_type ) && ! in_array( $post->post_type, $this->linkify_post_types ) ) ) {
			return $content;
		}
		
		return $this->filter_text( $content );
	}
	
	/**
	 * Linkify Short Description
	 */
	public function linkify_excerpt( $content ) {
		global $post;
		
		if( empty( $this->linkify_post_types ) || ( isset( $post->post_type ) && ! in_array( $post->post_type, $this->linkify_post_types ) ) ) {
			return $content;
		}
	
		return $this->filter_text( $content );
	}
	
	/**
	 * Linkify Categories / Terms Description
	 */
	public function linkify_term_content( $content ) {
	
		if( ! is_category() && ! is_tax() ) {
			return $content;
		}
		
		if( empty( $this->linkify_post_types ) ) {
			return $content;
		}
		
		$queried_object = get_queried_object();
		
		if( empty( $queried_object ) || ! isset( $queried_object->term_id ) ) {
			return $content;
		}
		
		$taxonomy = get_taxonomy( $queried_object->taxonomy );
		
		$common_post_types = array_intersect( $taxonomy->object_type, $this->linkify_post_types );
		if( empty( $common_post_types ) ) {
			return $content;
		}
		
		return $this->filter_text( $content );
	}
	
	/**
	 * Linkify Widget
	 */
	public function linkify_widget( $content ) {
		return $this->filter_text( $content );
	}
	
	/**
	 * Linkify Comment
	 */
	public function linkify_comment( $content, $comment ) {
		$post_type = get_post_type( $comment->comment_post_ID );
		
		if( empty( $this->linkify_post_types ) || ( isset( $post_type ) && ! in_array( $post_type, $this->linkify_post_types ) ) ) {
			return $content;
		}
		
		return $this->filter_text( $content );
	}
	
	/**
	 * Linkify Text
	 */
	public function filter_text( $content ) {
		
		if( empty( $content ) ) {
			return;
		}
		
		// Empty replaced terms everytime a nex content block is there
		if( ! $this->is_term_limit_for_full_page ) {
			$this->replaced_terms = '';
		}
		
		// Search and Replace glossary terms from content
		$is_space_separated		 = TRUE;
		$glossary_term_titles_chunk = array_chunk( $this->glossary_term_titles, 150, TRUE );
		
		foreach( $glossary_term_titles_chunk as $glossary_term_titles ) {
			$glossary_term_titles	 = '/' . ( ( $is_space_separated ) ? '(?<=\P{L}|^)(?<!(\p{N}))' : '') . '(?!(<|&lt;))(' . ( ! $this->is_case_sensitive ? '(?i)' : '' ) . implode( '|', $glossary_term_titles ) . ')(?!(>|&gt;))' . ( ( $is_space_separated ) ? '(?=\P{L}|$)(?!(\p{N}))' : '') . '/u';
			
			// Parse Dom Nodes
			$dom = new DOMDocument();
			
			libxml_use_internal_errors( true );
			
			if ( ! $dom->loadHtml( mb_convert_encoding( $content, 'HTML-ENTITIES', "UTF-8" ) ) ) {
				libxml_clear_errors();
			}
			
			$xpath = new DOMXPath( $dom );
			
			$query = "//text()[not(ancestor::script)][not(ancestor::style)][not(ancestor::a)]{$this->exclude_html_tags}";
			
			foreach ( $xpath->query( $query ) as $node ) {
				$replaced = preg_replace_callback( $glossary_term_titles, array( $this, 'preg_replace_matches' ), htmlspecialchars( $node->wholeText, ENT_COMPAT ) );
				if ( ! empty( $replaced ) ) {
					$new_node				= $dom->createDocumentFragment();
					$replaced_shortcodes	= strip_shortcodes( $replaced );
					$result					= $new_node->appendXML( '<![CDATA[' . $replaced_shortcodes . ']]>' );

					if ( $result !== false ) {
						$node->parentNode->replaceChild( $new_node, $node );
					}
				}
			}
			
			// Trim body tag to get original content
			$body_node = $xpath->query( '//body' )->item( 0 );

			if ( $body_node !== NULL ) {
				$new_dom = new DOMDocument();
				$new_dom->appendChild( $new_dom->importNode( $body_node, TRUE ) );

				$intermal_html	= $new_dom->saveHTML();
				$content		= substr( trim( $intermal_html ), 6, (strlen( $intermal_html ) - 14 ));
				
				// Fix auto lost self-closing ( bug in DOMDocument->saveHtml() ) - caused a conflict with NextGen
				$content		= preg_replace( '#(<img[^>]*[^/])>#Ui', '$1/>', $content );
			}
		}
		
		return trim( $content );
	}
	
	/**
	 * Replace Matching Terms
	 */
	public function preg_replace_matches( $match ) {
		if ( ! empty( $match[0] ) ) {
			$title = htmlspecialchars_decode( $match[0], ENT_COMPAT );
			$glossary_term = array();
			
			if ( ! empty( $this->glossary_terms ) ) {
				$title_index = $this->format_glossary_term_string( $title );
				
				// First - look for exact keys
				if ( array_key_exists( $title_index, $this->glossary_terms ) ) {
					$glossary_term = $this->glossary_terms[ $title_index ];
				} else {
					// If not found - try the tags
					foreach ( $this->glossary_terms as $key => $value ) {
						if ( strstr( $key, '|' ) && strstr( $key, $title_index ) ) {
							$glossary_term_tags = explode( '|', $key );
							if ( in_array( $title_index, $glossary_term_tags ) ) {
								$glossary_term = $value;
								break;
							}
						}
					}
				}
			}
			
			if( ! empty( $glossary_term ) ) {
				
				// Check Limit per Term
				if( $this->term_limit > 0 ) {
					$this->replaced_terms[ $glossary_term->ID ] = ( isset( $this->replaced_terms[ $glossary_term->ID ] ) && $this->replaced_terms[ $glossary_term->ID ] > 0 ) ? ( $this->replaced_terms[ $glossary_term->ID ] + 1 ) : 1;
					
					if( $this->replaced_terms[ $glossary_term->ID ] > $this->term_limit ) {
						return $title;
					}
				}
				
				global $post;
				$post = $glossary_term;
				setup_postdata( $post );
				
				$title_place_holder = '##TITLE_GOES_HERE##';
				
				if( $this->is_disable_link ) {
					$href = '';
				} else {
					$href = 'href="' . esc_url( get_permalink() ) . '"';
				}
								
				if( $this->is_tooltip ) {
					$attr_title = wpg_glossary_get_tooltip_content( $this->is_tooltip_content_shortcode, $this->is_tooltip_content_read_more );
					
					$new_text = '<a class="wpg-linkify wpg-tooltip" title="' . $attr_title . '" ' . $href . ' '. ( $this->is_new_tab ? 'target="_blank"' : '' ) .'>' . $title_place_holder . '</a>';
				} else {
					$new_text = '<a class="wpg-linkify" ' . $href . ' '. ( $this->is_new_tab ? 'target="_blank"' : '' ) .'>' . $title_place_holder . '</a>';
				}
				
				wp_reset_postdata();
				
				$new_text = str_replace( $title_place_holder, $title, $new_text );
				return $new_text;
			}
		}
	}
}

new WPG_Linkify();
