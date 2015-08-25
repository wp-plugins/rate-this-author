<?php 
/*=============================================

Function to display average rating of an author

==============================================*/


function rtauth_front_rating()
{
	
	global $wpdb;
	global $rtauth_table_name;
	
	$screen = get_current_screen();
	if($screen->base=="user-edit")
	{
		$author_id = $_GET['user_id'];
	}
	elseif($screen->base=="profile")
	{
		global $current_user;
		$author_id = $current_user->ID;
	}
	else
	{
		$author_id = get_the_author_meta("ID");
	}

if(get_userdata( $author_id )->roles[0]=="author" || get_userdata( $author_id )->roles[0]=="administrator")
{
	
	$stars_stats =$wpdb->get_results('SELECT sum( rating_stars ) as total_stars ,count( rating_stars ) as count_users FROM '.$rtauth_table_name.' where author_id='.$author_id.' and approved_status=1');
	if($stars_stats[0]->total_stars==0 || $stars_stats[0]->count_users==0)
	{
		$mean_of_stars = 0;
	}
	else
	{
		$mean_of_stars = $stars_stats[0]->total_stars / $stars_stats[0]->count_users;
	}
	
	
?>
	<div class="author_rating">
		<div class="rating_label"><label>Overall Rating: </label></div> 
		<div class="rating_stars">
			<span class="stars"><?php echo $mean_of_stars; ?></span>
		</div>
	
	
	</div>
<!--Google Rich Snippet for Author-->	
<div class="visuallyhidden" itemscope itemtype="http://schema.org/Review">
  <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
    <span itemprop="ratingValue"><?php echo $mean_of_stars; ?></span>
  </span> 

  <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Person">
    <a itemprop="sameAs" href="<?php echo the_author_meta('user_url'); ?>"><span itemprop="name"><?php the_author() ?></span></a>
  </div>
    
  <span itemprop="author" itemscope itemtype="http://schema.org/Person">
    <span itemprop="name">Visitors</span>
  </span>	
</div>
<!--Google Rich Snippet for Author-->	
	
<?php 

}
}

//add_shortcode( 'show_rating_stars', 'rtauth_front_rating' );


add_action( 'show_user_profile', 'rtauth_front_rating' );
add_action( 'edit_user_profile', 'rtauth_front_rating' );

/*===================================================

Function displaying list of users who reviewed author

====================================================*/

function rtauth_testimonial_func() {
	$author_id = get_the_author_meta('ID');
	global $wpdb;
	global $rtauth_table_name;
	$results = $wpdb->get_results( 'SELECT * FROM '.$rtauth_table_name.' where author_id='.$author_id.' and approved_status = 1 order by id DESC');
	$html = '';
	if(empty($results)){
		
		$html .= '<h3>No Reviews Found.</h3>';
	}else{
	
	$html.="<h2>Reviews by various users:-</h2>";
		foreach($results as $row)
	{
	
	$html .= '<div class="testimonial_loop">';
	$html .= '<div class="testimonial_left">';
		if($row->image!=0)
		{
			$uploads = wp_upload_dir();
			$link= esc_url( $uploads['baseurl'].'/'.get_post_meta( $row->image, '_wp_attached_file' , true )); 
			$user_img = '<img src="'.$link.'" width="50" height="50" alt="'.$row->name.'"  />';	
		}
		else
		{
			$reg_user = get_userdatabylogin($row->name);
			//var_dump($reg_user);
			if($reg_user->ID){
			$user_img = get_avatar( $reg_user->ID , 70 );
			}else{
			$link = plugins_url('images/dummy.jpg',dirname(__FILE__));
			$user_img = '<img src="'.$link.'" width="70" alt="'.$row->name.'"  />';	
			 }
			//$link = plugins_url('images/dummy.jpg',dirname(__FILE__));
		}
		$html .= $user_img;
	$html .= '</div>';
	$html .= '<div class="testimonial_right">';
		$html .= '<div class="testimonial_meta">';
			$html .= '<div class="end_user_name">';
				$html .= '<span>'.$row->name.'</span>, ';
				$html .= '<span style="font-size: small;">'.$row->city.'</span>';
				//$html .= '<span>'.$row->user_email.'</span>';
			$html .= '</div>';
			$type_of_review= '<span class="stars">'.$row->rating_stars.'</span>';
			$html .= '<div class="end_user_stars"><div class="rating_stars">'.$type_of_review.'</div></div>';
			$html .= '<div style="clear:both;"></div>';
		$html .= '</div>';
		$html .= '<p class="testimonial_comnt">'.$row->comments.'</p>';
	$html .= '</div></div>';	
	$html .= '<div style="clear:both;"></div>';	
	$html .= '<div class="stripe-line"></div>';
	}
	}
	echo $html;
}


/*=================================================================

Function will display rating button, average rating stars 
and list of users who reviewed Author, by default, at the 
end of author page(No backend settings required. It is autonomoous)

===================================================================*/

function rtauth_default_rating_view()
{
if(is_author())
{
	
?>
	<div class='default_rating_container'>
		<div class='default_rating_view'>
		
			<div class="reviews_button_container">
				<?php echo rtauth_front_rating();?>
				<?php echo rtauth_popup_form();?>
			</div>
			
			<div class="reviews_list_container">
				<?php echo rtauth_testimonial_func() ?>
			</div>
		
		</div>
		
	</div>
<?php
	
}
}

add_action('get_footer','rtauth_default_rating_view');

?>
