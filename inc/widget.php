<?php
class eeb_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'r-emergency-button',
			'description' => __( 'R Emergency Button widget', 'r-emergency-button' )
		);


		$control_ops = array(
			'id_base' => 'r-emergency-button'
		);

		parent::__construct( 'r-emergency-button', __( 'R Emergency Button', 'r-emergency-button' ), $widget_ops, $control_ops );
	}

	public static function register() {
		register_widget( 'eeb_widget' );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$content = $instance['content'];
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo '<div class="textwidget">' . do_shortcode( $content ) . '</div>';
		echo $after_widget;
	}

	function form( $instance ) {
		$defaults = array(
			'title'   => __( 'R Emergency Button', 'r-emergency-button' ),
			'text' => 'Emergency',
			'no_of_clicks' => '3',
			'content' => ''
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'text' ); ?>">Text</label>
				<input type="text" placeholder="Enter your Button Text" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo $instance['text']; ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'no_of_clicks' ); ?>">Number of Clicks</label>
				<input type="number" name="<?php echo $this->get_field_name( 'no_of_clicks' ); ?>" id="<?php echo $this->get_field_id( 'no_of_clicks' ); ?>" value="<?php echo $instance['no_of_clicks' ]; ?>" class="widefat" style="margin-top:10px"/>
			</p>
		<?php
	}

}

add_action( 'widgets_init', array( 'eeb_widget', 'register' ) );
