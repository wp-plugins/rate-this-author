<?php
/* **** Author Widget **** */

function rtauth_widgets_init() {
register_widget('Author_Rating_Widget');
}
add_action('widgets_init', 'rtauth_widgets_init');

/*
 * Top Author Rating widget
 */
class Author_Rating_Widget extends WP_Widget {
    private $fields = array(
        'title'         => 'Title (optional)',
    );
    
    function __construct() {
        $widget_ops = array('classname' => 'author_widget', 'description' => __('Use this widget to add a top rated authors listing', 'roots'));

        $this->WP_Widget('author_rating_Widget', __('Top Rated Authors', 'roots'), $widget_ops);
        $this->alt_option_name = 'author_rating_Widget';
    }
    
    function widget($args, $instance) {
        $cache = wp_cache_get('author_rating_Widget', 'widget');

        if (!is_array($cache)) {
          $cache = array();
        }

        if (!isset($args['widget_id'])) {
          $args['widget_id'] = null;
        }

        if (isset($cache[$args['widget_id']])) {
          echo $cache[$args['widget_id']];
          return;
        }

        ob_start();
        extract($args, EXTR_SKIP);

        $title = apply_filters('widget_title', empty($instance['title']) ? false : $instance['title'], $instance, $this->id_base);

        foreach($this->fields as $name => $label) {
          if (!isset($instance[$name])) { $instance[$name] = ''; }
        }

        echo $before_widget;
	echo '<div class="rating_widget">';
	echo '<h3 class="widget-title">', $title, '</h3>';	
		global $wpdb;
		global $rtauth_table_name;
$result = $wpdb->get_results('SELECT sum( rating_stars ) as total_stars ,count( rating_stars ) as count_users FROM '.$rtauth_table_name.' where  approved_status=1');
		// WP_User_Query arguments
		$args = array (
			'role'           => 'Author',
		);

		// The User Query
		$user_query = new WP_User_Query( $args );

		// The User Loop
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$stars_stats =$wpdb->get_results('SELECT sum( rating_stars ) as total_stars ,count( rating_stars ) as count_users FROM '.$rtauth_table_name.' where author_id='.$user->ID.' and approved_status=1');
				if($stars_stats[0]->total_stars==0 || $stars_stats[0]->count_users==0)
				{
					$mean_of_stars = 0;
				}
				else
				{
					$mean_of_stars = $stars_stats[0]->total_stars / $stars_stats[0]->count_users;
				}
				
				$keyid = $user->ID;
				$star_arr[$keyid] = $mean_of_stars;
				
			?>
			<?php } 
			}else{	echo 'Not Found'; }
			arsort($star_arr);
			$i = 1;
			foreach($star_arr as $usrid => $star_val){
			if($i <= 5){
			$i++;
			$user_info = get_userdata($usrid);
			$username = $user_info->user_login;
			?>
				<div class="author_rating_loop">
				<div class="auth_img"><?php echo get_avatar( $usrid , 50 ); ?></div>
				<div class="rating_stars">
					<p><?php echo $username; ?></p>
					<span class="stars"><?php echo $star_val; ?></span>
				</div>
				</div>
			<?php } else{
				break;
				}
			}
			unset($star_arr);
			
		echo '</div>';
        echo $after_widget;
		echo '<div style="clear:both;"></div>';

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('author_rating_Widget', $cache, 'widget');
    }

    function form($instance) {
        foreach($this->fields as $name => $label) {
          ${$name} = isset($instance[$name]) ? esc_attr($instance[$name]) : '';
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id($name)); ?>"><?php _e("{$label}:", 'roots'); ?></label>
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name)); ?>" type="text" value="<?php echo ${$name}; ?>">
        </p>
        <?php
        }
    }
    
    function flush_widget_cache() {
        wp_cache_delete('author_rating_Widget', 'widget');
    }
}

