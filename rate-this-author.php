<?php /*

Plugin Name: Rate this Author
Description: A very simple and lightweight Plugin for rating authors by visitors.
Version: 1.1
Author: Tech9logy Creators 
Author URI: http://www.tech9logy.com/

*/

/*  Copyright 2015-2016 Tech9logy Creators (email: hello@tech9logy.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/** Database */
require_once(dirname (__FILE__) . '/includes/memory-schema.php');


/** Main Hooks */


add_action( 'admin_menu', 'rtauth_author_plugin_menu' );

function rtauth_author_plugin_menu() 
{
	add_menu_page( 'Rate this Author Options', 'Rate This Author', 'edit_posts', 'rate-this-author-identifier', 'rtauth_rate_this_author_options' );
	add_submenu_page( 'rate-this-author-identifier','Rate this Author Options', 'Settings', 'edit_posts', 'rate-this-author-settings', 'rtauth_rate_this_author_settings' );
}
add_action( 'admin_init', 'rtauth_register_mysettings' );
function rtauth_register_mysettings() {
register_setting( 'rtauth-settings-group', 'empty_email' );
register_setting( 'rtauth-settings-group', 'valid_email' );
register_setting( 'rtauth-settings-group', 'duplicate_user' );
register_setting( 'rtauth-settings-group', 'invalid_image' );
register_setting( 'rtauth-settings-group', 'image_error' );
register_setting( 'rtauth-settings-group', 'rate_author' );
register_setting( 'rtauth-settings-group', 'success_msg' );

}
if(!get_option( 'empty_email' )){
add_option( 'empty_email', 'Email id is required', '', 'yes' );
}
if(!get_option( 'valid_email' )){
add_option( 'valid_email', 'Email id is invalid', '', 'yes' );
}
if(!get_option( 'rate_author' )){
add_option( 'rate_author', 'Please rate this author', '', 'yes' );
}
if(!get_option( 'duplicate_user' )){
add_option( 'duplicate_user', 'You already seem to have reviewed this author by same email id', '', 'yes' );
}
if(!get_option( 'invalid_image' )){
add_option( 'invalid_image', 'The image type you are trying to upload is not valid', '', 'yes' );
}
if(!get_option( 'image_error' )){
add_option( 'image_error', 'There is some error while uploading Image, Please refresh and try again', '', 'yes' );
}
if(!get_option( 'success_msg' )){
add_option( 'success_msg', 'Your review has been sent for approval', '', 'yes' );
}

function rtauth_rate_this_author_options() {


		global $current_user;

	if ( !current_user_can( 'edit_posts' ) )  {


		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		
	}
	else
	{
		if($current_user->roles[0]=="administrator" || $current_user->roles[0]=="author" )
		{	
			echo rtauth_list_reviews();
		}
		else
		{
		
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
	}

}

function rtauth_rate_this_author_settings() {


	global $current_user;
		

	if ( $current_user->roles[0]!="administrator"  )  {


		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		
		
	}
	else
	{
		
			echo rtauth_setting_form();
	
	}

}

/*Get Pop up Form */	
require_once(dirname (__FILE__) . '/includes/form.php');

/* Get settings */
require_once(dirname (__FILE__) . '/includes/setting_form.php');

/*Get Rating Stars on frontend */	
require_once(dirname (__FILE__) . '/includes/front_rating.php');

/*Get Admin Listing */	
require_once(dirname (__FILE__) . '/includes/admin/list.php');

/*Get Author Widget */	
require_once(dirname (__FILE__) . '/includes/widget.php');

//Attach Style sheet and js
function rtauth_place_ui() {

 if ( is_rtl() ) 
  {	
	wp_enqueue_style( 'rtauth_main', plugins_url('main_rtl.css',__FILE__), array(), false, false );
  }
 else
  {
 	wp_enqueue_style( 'rtauth_main', plugins_url('main.css',__FILE__), array(), false, false );
  } 
  	wp_enqueue_style( 'rtauth-googleFonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,700,300', array(), false, false );
	wp_enqueue_style( 'rtauth_mScroll-css', plugins_url('mScroll/jquery.mCustomScrollbar.css',__FILE__), array(), false, false );
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-form',array('jquery'),false,true );
	wp_enqueue_script( 'rtauth_jquery-js', plugins_url('jquery-js.js',__FILE__), array(), false, true );
	wp_enqueue_script( 'rtauth_rating_simple', plugins_url('rating_simple.js',__FILE__), array(), false, true );
	wp_enqueue_script( 'rtauth_mScroll-js', plugins_url('mScroll/jquery.mCustomScrollbar.concat.min.js',__FILE__), array(), false, true );
	wp_localize_script('rtauth_jquery-js', 'dynamicPath', array(
    'pluginsUrl' => plugins_url('images',__FILE__),
	'siteUrl'=>site_url(),
	'emptyEmail'=>get_option('empty_email'),
	'valid_email'=>get_option('valid_email'),
	'rate_author'=>get_option('rate_author'),
	'duplicate_user'=>get_option('duplicate_user'),
	'invalid_image'=>get_option('invalid_image'),
	'image_error'=>get_option('image_error'),
	'is_author'=>is_author(),
	'is_rtl'=>is_rtl()
));
	wp_localize_script('rtauth_mScroll-js', 'url', array(
    'mousewheelJsUrl' => plugins_url('mScroll/jquery.mousewheel.min.js',__FILE__)
	));
}

add_action( 'wp_enqueue_scripts', 'rtauth_place_ui' );


//Attach Style sheet and js for admin

add_action( 'admin_enqueue_scripts', 'rtauth_load_admin_styles' );
	function rtauth_load_admin_styles() 
	{
		$screen = get_current_screen();
		
		if((isset($_GET['page']) && $_GET['page']=="rate-this-author-identifier") || isset($_GET['user_id']) || $screen->base=="profile")
		{	
		
		  if ( is_rtl() ) 
		    {
			   wp_enqueue_style( 'rtauth_admin__rtl_css',  plugins_url('includes/admin/admin_rtl.css',__FILE__), false, '1.0.0' );
			}
			else
			{	
			  wp_enqueue_style( 'rtauth_admin_css',  plugins_url('includes/admin/admin.css',__FILE__), false, '1.0.0' );
		 	}
			wp_enqueue_script( 'rtauth_admin', plugins_url('includes/admin/admin.js',__FILE__), array(), false, true );
		}
	}  


?>
