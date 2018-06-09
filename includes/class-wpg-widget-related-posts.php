<?php
/**
 * Glossary Widget - Related Posts
 *
 * @class WPG_Widget_Related_Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPG_Widget_Related_Posts Class
 */
class WPG_Widget_Related_Posts extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		$this->widget_cssclass    = 'wpg_widget wpg_widget_related_posts';
		$this->widget_description = __( 'Displays a list of post links on the glossary term details page where the glossary term is actually found. Nice for internal linking.', WPG_TEXT_DOMAIN );
		$this->widget_id          = 'wpg_widget_related_posts';
		$this->widget_name        = __( 'WP Glossary - Related Posts', WPG_TEXT_DOMAIN );
		
		$widget_ops = array(
			'classname'		=> $this->widget_cssclass,
			'description'	=> $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
	}

	/**
	 * Function: widget
	 */
	public function widget( $args, $instance ) {
	
		if( ! is_singular( 'glossary' ) ) {
			return;
		}
	
		if( empty( $instance['post_types'] ) ) {
			return;
		}
		
		global $post;
		
		$instance['number_of_posts'] = (int) $instance['number_of_posts'];
		if( $instance['number_of_posts'] < 1 ) {
			$instance['number_of_posts'] = -1;
		}
		
		add_filter( 'posts_where', array( $this, 'where_content_filter' ), 10, 2 );
		query_posts( array(
			'posts_per_page'	=> $instance['number_of_posts'],
			'post_type'			=> $instance['post_types'],
			'orderby'			=> 'title',
			'order'				=> 'ASC',
			'wpg_term'			=> $post->post_title
		) );
		remove_filter( 'posts_where', array( $this, 'where_content_filter' ), 10, 2 );
		
		if( ! have_posts() ) {
			return;
		}
			
		echo $args['before_widget'];
		
		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		?><ul><?php
			while( have_posts() ) : the_post();
				?><li><a href="<?php the_permalink(); ?>" alt="<?php the_title(); ?>"><?php the_title(); ?></a></li><?php
			endwhile;		
			wp_reset_query();		
		?></ul><?php
		
		echo $args['after_widget'];
	}
	
	/**
	 * Function: update
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( isset( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}
		
		if ( isset( $new_instance['number_of_posts'] ) ) {
			$instance['number_of_posts'] = sanitize_text_field( $new_instance['number_of_posts'] );
		}
		
		$instance['post_types'] = $new_instance['post_types'];

		return $instance;
	}
	
	/**
	 * Function: form
	 */
	public function form( $instance ) {
		
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Related Posts', WPG_TEXT_DOMAIN );
		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', WPG_TEXT_DOMAIN ); ?></label>
			
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p><?php
		
		$number_of_posts = ! empty( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;
		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php _e( 'Number of Posts', WPG_TEXT_DOMAIN ); ?></label>
			
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" type="number	" value="<?php echo esc_attr( $number_of_posts ); ?>" />
		</p><?php
		
		$post_types = ! empty( $instance['post_types'] ) ? $instance['post_types'] : '';
		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_types' ) ); ?>"><?php _e( 'Post Types', WPG_TEXT_DOMAIN ); ?></label><br />
			
			<?php
				$wpg_post_types = wpg_get_post_types();
				if( ! empty( $wpg_post_types ) ) {
					foreach( $wpg_post_types as $key => $opt ) {

						$checked = '';
						if( ! empty( $post_types ) ) {
							$checked = checked( in_array( $key, $post_types ), true, false );
						}

						?><input type="checkbox" name="<?php echo $this->get_field_name( 'post_types' ) . '[]'; ?>" id="<?php echo esc_attr( $this->get_field_id( 'post_types' ) )  . '_' . $key; ?>" value="<?php echo esc_attr( $key ); ?>" <?php echo $checked; ?> /> 
						
						<label for="<?php echo esc_attr( $this->get_field_id( 'post_types' ) )  . '_' . $key; ?>"><?php echo esc_attr( $opt ); ?></label><br /><?php
					}						
				}
			?>
		</p><?php
	}
	
	/**
	 * query_posts Filter By Post Content
	 */
	function where_content_filter( $where, &$wp_query ) {
		global $wpdb;
		
		if ( $wpg_term = $wp_query->get( 'wpg_term' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_content REGEXP \'[[:<:]]' . esc_sql( $wpdb->esc_like( $wpg_term ) ) . '[[:>:]]\'';
		}
		
		return $where;
	}
}

/**
 * Register Widget
 */
function wpg_widget_related_posts() {
	register_widget( 'WPG_Widget_Related_Posts' );
}
add_action( 'widgets_init', 'wpg_widget_related_posts' );
