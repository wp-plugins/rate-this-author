<?php

/*=========================

Function to show pop up form

=========================*/
function rtauth_popup_form()
{
?>
<div class="rate_this_author"><a href ="javascript:void(0)" class="showpop">Want to "Rate this author" ?</a></div>

<div id="light" class="white_content mCustomScrollbar">
<a href ="javascript:void(0)" class="hidepop textright">Close</a>
	<div class="pop_up_form" style="clear: both;padding: 16px;padding-right: 1px;">
	<div class="form_title"><div class="author_name">Rate '<?php the_author() ?>' here</div><div class="stripe-line" style="float: left;width: 100%"></div><div style="clear:both;"></div></div>
	<div class="error_msg"></div>
		<form id="thumbnail_upload" method="post" style="margin-left: 1%;margin-top:20px;" action="#" enctype="multipart/form-data">
		<?php if( !is_user_logged_in() ) {  ?>
		<div class="popup_field_container">
			<div class="popup_label"><label>Name:</label></div> 
			<div class="popup_field"><input type="text" name="visitor_name" id="visitor_name" /></div>
		</div>
		
		<div class="popup_field_container">
			<div class="popup_label"><label>Email:</label></div> 
			<div class="popup_field"><input type="text" name="visitor_email" id="visitor_email" /></div>
		</div>
		
		<div class="popup_field_container">
			<div class="popup_label"><label>City:</label></div> 
			<div class="popup_field"><input type="text" name="visitor_city" id="visitor_city" /></div>
		</div>
		
		<div class="popup_field_container">
			<div class="popup_label"><label>Profile Image:</label></div> 
			<div class="popup_field"><input type="file" name="visitor_image" id="visitor_image_id" /></div>
		</div>
		<?php } else{
		global $current_user;
      		get_currentuserinfo();		
		 ?>
			<input type="hidden" name="visitor_name" id="visitor_name" value="<?php echo $current_user->user_login; ?>" />
			<input type="hidden" name="visitor_email" id="visitor_email" value="<?php echo $current_user->user_email; ?>" />
		<?php } ?>
		<div class="popup_field_container">
			<div class="popup_label"><label>Rate this Author</label></div> 
			<div class="popup_field"><input name="rating_simple3" value="" id="rating_simple3" type="hidden"></div>
		</div>
		
		<div class="popup_field_container">
			<div class="popup_label"><label>Comments:</label></div> 
			<div class="popup_field"><textarea name="visitor_comments" style="width:80%;" id="visitor_comments" /></textarea></div>
		</div>

		<div class="popup_field_container">
			<input type="hidden" id="author_id" name="author_id" value="<?php echo get_user_by( 'slug', get_query_var( 'author_name' ) )->ID;?>" />
			<input type="hidden" name="action" id="action" value="rtauth_submit_author_rating">
			<input type="submit" id="submit-ajax" name="submit-ajax"  />
		</div>
	
		</form>	
		
	</div>
  <div class="loading"><img src="<?php echo plugins_url('images/loading.gif',dirname(__FILE__) )?>" /></div>
  
  <div id="review_output"></div>
</div>
<div id="fade" class="black_overlay"></div>



<?php
}

//add_shortcode( 'popup_form_code', 'rtauth_popup_form' );


/*========================================

Function to save pop up form details in db

==========================================*/

function rtauth_submit_author_rating()
{

	global $rtauth_table_name;
	global $wpdb;
	$count_emails = $wpdb->get_results( 'SELECT count(user_email) as email FROM '.$rtauth_table_name.' where user_email="'.$_POST['visitor_email'].'" and author_id="'.$_POST['author_id'].'"');	
if($count_emails[0]->email==0)	
{		
	$validextensions = array("jpeg", "jpg", "png");
	$temporary = explode(".", $_FILES['visitor_image']["name"]);
	$file_extension = end($temporary);
	if (((($_FILES['visitor_image']["type"] == "image/png") || ($_FILES['visitor_image']["type"] == "image/jpg") || ($_FILES['visitor_image']["type"] == "image/jpeg")
	) && in_array($file_extension, $validextensions)) || $_FILES['visitor_image']=="") 
	{
		if ($_FILES['visitor_image']["error"] > 0)
		{
			echo "Return Code: " . $_FILES['visitor_image']["error"] . "<br/><br/>";
			echo "err-1";
			die();
			
		}
		else
		{
		
		 if($_FILES['visitor_image']!="")
		 {
			//require the needed files
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');
			//then loop over the files that were sent and store them using  media_handle_upload();
			
			$attachement_id = media_handle_upload( 'visitor_image', 0 );
		 }
		 
		global $wpdb;
		global $rtauth_table_name;
		$wpdb->insert(
			$rtauth_table_name,
			array(
				'author_id' => $_POST['author_id'],
				'user_email' => $_POST['visitor_email'],
				'name'=>$_POST['visitor_name'],
				'city'=>$_POST['visitor_city'],
				'image'=>$attachement_id,
				'comments'=>$_POST['visitor_comments'],
				'rating_stars'=>$_POST['rating_simple3'],
				'approved_status'=>0
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d'
			)
		);
	
		$output="<h3>Thanks for rating, ".get_the_author_meta( 'display_name',$_POST['author_id'])."</h3>";
		$output.="<h2>".get_option('success_msg')."</h2>";
		echo $output;
		die();
		return true;
		}
	
	}
	
	else
	{
		
		echo "err-2";
		die();
	
	}

}

else
{

	echo "duplicate";
	die();
}
}

add_action( 'wp_ajax_rtauth_submit_author_rating', 'rtauth_submit_author_rating');
add_action( 'wp_ajax_nopriv_rtauth_submit_author_rating', 'rtauth_submit_author_rating');


?>
