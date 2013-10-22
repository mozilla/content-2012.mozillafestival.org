<?php
/*
Plugin Name: Recent Category Posts Widget
Plugin URI: http://www.Stephanis.info/
Description: Displays the most recent posts in the selected category in a simple list.
Author: E. George Stephanis
Author URI: http://www.Stephanis.info/
Version: 2.0
*/

class single_category_posts_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'single_category_posts_widget',
			'Single Category Posts Widget',
			array( 'description' => __( 'A widget to display the most recent posts from a single category' ) )
		);
	}

 	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Recent Posts in Category' );
		$cat = isset( $instance[ 'cat' ] ) ? intval( $instance[ 'cat' ] ) : 0;
		$qty = isset( $instance[ 'qty' ] ) ? intval( $instance[ 'qty' ] ) : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:' ); ?></label> 
			<?php wp_dropdown_categories( Array(
						'orderby'            => 'ID', 
						'order'              => 'ASC',
						'show_count'         => 1,
						'hide_empty'         => 0,
						'hide_if_empty'      => false,
						'echo'               => 1,
						'selected'           => $cat,
						'hierarchical'       => 1, 
						'name'               => $this->get_field_name( 'cat' ),
						'id'                 => $this->get_field_id( 'cat' ),
						'class'              => 'widefat',
						'taxonomy'           => 'category',
					) ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'qty' ); ?>"><?php _e( 'Quantity:' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'qty' ); ?>" name="<?php echo $this->get_field_name( 'qty' ); ?>" type="number" min="1" step="1" value="<?php echo $qty; ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['cat'] = intval( $new_instance['cat'] );
		$instance['qty'] = intval( $new_instance['qty'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$cat = $instance['cat'];
		$qty = (int) $instance['qty'];

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo self::get_cat_posts( $cat, $qty );
		echo $after_widget;
	}

	public function get_cat_posts( $cat, $qty ) {
		$posts = get_posts( Array(
			'category'		=>	$cat,
			'orderby'		=>	'date',
			'order'			=>	'DESC',
			'numberposts'	=>	$qty
		));

		$returnThis = '';
		if( count( $posts ) )
			$returnThis .= '<ul class="'.__CLASS__.'">'."\r\n";
		foreach( $posts as $post )
			$returnThis .= "\t".'<li><a href="'.get_permalink( $post->ID ).'">'.$post->post_title.'</a></li>'."\r\n";
		if( count( $posts ) )
			$returnThis .= '</ul>'."\r\n";
		return $returnThis;
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "single_category_posts_widget" );' ) );
