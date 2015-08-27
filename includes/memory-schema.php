<?php
/* table installation */

global $wpdb;
$rtauth_table_name = $wpdb->prefix."rate_this_author";

global $rtauth_table_version;

$rtauth_table_version = "1.0";



function rtauth_table_install() {

   global $wpdb;

   global $rtauth_table_version;

   $rtauth_table_name = $wpdb->prefix."rate_this_author";


   $sql = "CREATE TABLE ".$rtauth_table_name." (id int(11) NOT NULL AUTO_INCREMENT, author_id int(11) NOT NULL, user_email varchar(255) NOT NULL, name varchar(255) NOT NULL, city varchar(255) NOT NULL, image int(11) NOT NULL, comments text NOT NULL, rating_stars int(1) NOT NULL, approved_status int(1) NOT NULL, UNIQUE KEY id (id));";


   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

   dbDelta( $sql );

 

   add_option( "rtauth_table_version", $rtauth_table_version );

}

rtauth_table_install();




?>