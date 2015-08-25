<?php

/*======================================

Function to show settings form in admin

=======================================*/

function rtauth_setting_form()
{
?>
<h2>"Rate this author" Settings</h2>
		<form method="post" action="options.php">
		<?php settings_fields( 'rtauth-settings-group' ); ?>
		<?php do_settings_sections( 'rtauth-settings-group' ); ?>
		<table class="form-table">
        <tr valign="top">
        <th scope="row">Empty Email :</th>
        <td><input type="text" name="empty_email" value="<?php echo get_option('empty_email'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Valid Email :</th>
        <td><input type="text" name="valid_email" value="<?php echo get_option('valid_email'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Rate Author :</th>
        <td><input type="text" name="rate_author" value="<?php echo get_option('rate_author'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Duplicate User :</th>
        <td><input type="text" name="duplicate_user" value="<?php echo get_option('duplicate_user'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Invalid Image :</th>
        <td><input type="text" name="invalid_image" value="<?php echo get_option('invalid_image'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Image Error :</th>
        <td><input type="text" name="image_error" value="<?php echo get_option('image_error'); ?>" /></td>
        </tr>
		<tr valign="top">
        <th scope="row">Sucess Message :</th>
        <td><input type="text" name="success_msg" value="<?php echo get_option('success_msg'); ?>" /></td>
        </tr>
	</table>
    
    <?php submit_button(); ?>
	
		</form>	
<?php
}
?>