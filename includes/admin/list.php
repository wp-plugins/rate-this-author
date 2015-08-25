<?php
/*====================================

Function to show Review lists in Admin

=====================================*/

function rtauth_list_reviews()
{

function rtauth_get_user_role() {
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	return $user_role;
}

?>
<?php 
global $current_user;

if ( $current_user->roles[0]=="administrator") {?>
<div class="filter_author_container">
<div class="filter_label">Filter by Author's Name</div>
<?php 
$args  = array(
// search only for Authors role
//'role' => 'administrator',
// order results by display_name
'orderby' => 'display_name'
);
// Create the WP_User_Query object
$wp_user_query = new WP_User_Query($args);
// Get the results
$authors = $wp_user_query->get_results();
// Check for results
//print_r($authors);
if (!empty($authors))
{
	
    echo '<div class="filter_author"><form method="get" action=""><select name="author_id">';
    // loop trough each author
	echo '<option value="">All</option>';
   
	foreach ($authors as $author)
    {
	  if($author->roles[0]=="author" || $author->roles[0]=="administrator")	
	  {
        // get all the user's data
        $author_info = get_userdata($author->ID);
		if($_GET['author_id']==$author->ID)
		{
			$selected = 'selected="selected"';
		}
		else
		{
			$selected='';
		}
    	
		
	
	    echo '<option '.$selected.' value="'.$author->ID.'">'.$author_info->first_name.' '.$author_info->last_name.'</option>';
      }
	
	}
	
    echo '</select>';
	echo '<input type="hidden" name="page" value="rate-this-author-identifier">';
	echo '<input type="submit" value="Filter" /></form></div>';
} 

?>
</div>
<?php } ?>

<div class="review_list_heading"><h1>Accept/Reject Reviews:-</h1></div>


<?php

$num_rec_per_page=10;
if (isset($_GET["cpage"])) { $page  = $_GET["cpage"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 
	if(isset($_GET['author_id']) && $_GET['author_id']!="")
	{
		$where = 'where author_id='.$_GET['author_id'].'';
	}
	else
	{
		$where='';
	}
	if(rtauth_get_user_role()=='administrator')
	{
		global $rtauth_table_name;
		global $wpdb;
		$results = $wpdb->get_results( 'SELECT * FROM '.$rtauth_table_name.' '.$where.' order by id DESC LIMIT '.$start_from.', '.$num_rec_per_page.' ');	
		
	}
	
	if(rtauth_get_user_role()=='author')
	{
		global $rtauth_table_name;
		global $wpdb;
		$results = $wpdb->get_results( 'SELECT * FROM '.$rtauth_table_name.' where author_id='.get_current_user_id().' order by id DESC LIMIT '.$start_from.', '.$num_rec_per_page.' ');	
		
	}
	
	echo '<table border="0" class="review_list_container" cellpadding="0" cellspacing="0">';
	echo '<thead><tr>';
	echo '<th>Id</th> <th>Image</th> <th>Email</th> <th>Name</th> <th>City</th> <th>Comments</th> <th>Rating</th> <th>Author</th> <th>Status</th>';
	echo '</tr></thead>';
	echo '<tbody>';
	foreach($results as $row)
	{
	
	?>
	
	<tr>
	<td class="item"><?php echo $row->id; ?></td>
	<td class="item"><?php 
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
		$user_img = get_avatar( $reg_user->ID , 50 );
		}else{
		$link = plugins_url('../images/dummy.jpg',dirname(__FILE__));
		$user_img = '<img src="'.$link.'" width="50" height="50" alt="'.$row->name.'"  />';	
		 }
	}
	?>
	<?php echo $user_img; ?>
	</td>
	<td class="item"><?php echo $row->user_email; ?></td>
	<td class="item"><?php echo $row->name; ?></td>
	<td class="item"><?php echo $row->city; ?></td>
	<td class="item"><?php echo $row->comments; ?></td>
	<td class="item"><?php echo $row->rating_stars; ?></td>
	<td class="item"><?php echo get_the_author_meta( 'display_name',$row->author_id); ?></td>
	<td class="item status">
	<?php  
      if($row->approved_status==0)
	  {
	  	echo "<span class='to_be_approve' id='".$row->id."'>Approve</span>";
	  }
	  else
	  {
	  	echo "<span class='approved'>Approved</span>";
	  }
	  echo "<span class='to_remove' id='id_".$row->id."' >Remove</span>";
	
	 ?> </td>
	 
	
	
	</tr>
	
	
	
	
	
	<?php
	}
	
	echo '</tbody></table>';

	if(isset($_GET['author_id']) && $_GET['author_id']!="")
	{
		$where = 'where author_id='.$_GET['author_id'].'';
	}
	else
	{
		$where='';
	}
	if(rtauth_get_user_role()=='administrator')
	{
		global $rtauth_table_name;
		global $wpdb;
		$rs_results = $wpdb->get_results( 'SELECT count(*) as record FROM '.$rtauth_table_name.' '.$where.' order by id DESC');	
		
	}
	
	if(rtauth_get_user_role()=='author')
	{
		global $rtauth_table_name;
		global $wpdb;
		$rs_results = $wpdb->get_results( 'SELECT count(*) as record FROM '.$rtauth_table_name.' where author_id='.get_current_user_id().' order by id DESC');	
		
	}
	$total_records = $rs_results[0]->record;  //count number of records
	//echo $total_records;
$total_pages = ceil($total_records / $num_rec_per_page); 
if (isset($_GET["cpage"])) { $page  = $_GET["cpage"];
	if($page == 1){
		
		echo "<span style='padding: 3px 8px;margin: 2px;background: #ccc;color: #fff;text-decoration: none;border-radius: 3px;'>".'1'."</span> "; // Goto 1st page  
	}else{
		$prev_page = $total_pages-1;
		echo "<a href='?page=rate-this-author-identifier&cpage=".$prev_page."' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".'<<'."</a> "; // Goto first page 
		echo "<a href='?page=rate-this-author-identifier&cpage=1' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".'1'."</a> "; // Goto 1st page  
	}
 }
 else{
	 
	 echo "<span style='padding: 3px 8px;margin: 2px;background: #ccc;color: #fff;text-decoration: none;border-radius: 3px;'>".'1'."</span> "; // Goto 1st page 
 }
//echo "<a href='?page=rate-this-author-identifier&cpage=1' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".'1'."</a> "; // Goto 1st page  

for ($i=2; $i<=$total_pages; $i++) { 
			if (isset($_GET["cpage"])) { $page  = $_GET["cpage"];
				if($page == $i){
					echo "<span style='padding: 3px 8px;margin: 2px;background: #ccc;color: #fff;text-decoration: none;border-radius: 3px;'>".$i."</span> "; // Goto 1st page  
				}else{
					echo "<a href='?page=rate-this-author-identifier&cpage=".$i."' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".$i."</a> ";   
				}
			 }
			 else{
            echo "<a href='?page=rate-this-author-identifier&cpage=".$i."' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".$i."</a> "; 
			 }
} 
if (isset($_GET["cpage"])) { $page  = $_GET["cpage"];
				if($page == $total_pages){
					 //echo "<span style='padding: 3px 8px;margin: 2px;background: #ccc;color: #fff;text-decoration: none;border-radius: 3px;'>".$total_pages."</span> "; // Goto last page 
				}else{
					echo "<a href='?page=rate-this-author-identifier&cpage=".$total_pages."' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".'>>'."</a> "; // Goto last page 
				}
			 }
			 else{
				 
				 echo "<a href='?page=rate-this-author-identifier&cpage=".$total_pages."' style='padding: 3px 8px;margin: 2px;background: rgb(96, 197, 174);color: #fff;text-decoration: none;border-radius: 3px;'>".'>>'."</a> "; // Goto last page 
			 }

}

/*=========================================

Function to update review status in backend

==========================================*/

function rtauth_update_approval_status()
{
global $wpdb;
global $rtauth_table_name;
$wpdb->update($rtauth_table_name,array('approved_status'=>1),array('id'=>$_POST['id']));

return true;
}

add_action( 'wp_ajax_rtauth_update_approval_status', 'rtauth_update_approval_status');
add_action( 'wp_ajax_nopriv_rtauth_update_approval_status', 'rtauth_update_approval_status');


/*=========================================

Function to remove review in backend

==========================================*/

function rtauth_remove_review()
{
global $wpdb;
global $rtauth_table_name;
$wpdb->delete( $rtauth_table_name, array( 'id' => $_POST['id'] ) );


return true;

}

add_action( 'wp_ajax_rtauth_remove_review', 'rtauth_remove_review');
add_action( 'wp_ajax_nopriv_rtauth_remove_review', 'rtauth_remove_review');
?>
