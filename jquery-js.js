/*Main js file*/
jQuery(".rate_this_author .showpop").click(function(){

jQuery(".reviews_button_container #light").show();
jQuery(".reviews_button_container #fade").show();
});
jQuery(".reviews_button_container .hidepop").click(function(){
jQuery(".reviews_button_container #light").hide();
jQuery(".reviews_button_container #fade").hide();
});


function rtauth_add_star_count(value)
{
  jQuery("#rating_simple3").attr("value",value);
  //alert(jQuery("#rating_simple3").val());
}
          
jQuery(function() {
       
jQuery("#rating_simple3").webwidget_rating_simple({
                    rating_star_length: '5',
                    rating_initial_value: '0',
                    rating_function_name: 'rtauth_add_star_count',//this is function name for click
                    directory: dynamicPath.pluginsUrl
                });
            });




var ajaxurl = dynamicPath.siteUrl+"/wp-admin/admin-ajax.php";
	    var options = { 
        target:        '.white_content #review_output',      // target element(s) to be updated with server response 
        beforeSubmit:  rtauth_showRequest,     // pre-submit callback 
        success:       rtauth_showResponse,    // post-submit callback 
        url:    ajaxurl                 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php     
    }; 

    // bind form using 'ajaxForm' 
   jQuery('#thumbnail_upload').ajaxForm(options); 
	
function rtauth_showRequest(formData, jqForm, options) 
{
	
	jQuery(".pop_up_form #visitor_email").removeClass('red-border');
	jQuery(".pop_up_form .webwidget_rating_simple").removeClass('red-border');
	jQuery(".pop_up_form #visitor_image_id").removeClass('red-border');
		var errStr = "";
		var email = jQuery("#visitor_email");
		if (email.val() == "") {errStr += dynamicPath.emptyEmail+"<br>"; jQuery(".pop_up_form #visitor_email").addClass('red-border');}	
		if(email.val()!="")
		{
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (!filter.test(email.val())){ errStr += dynamicPath.valid_email+"<br>"; jQuery(".pop_up_form #visitor_email").addClass('red-border');}
		}
		
		if(jQuery('#rating_simple3').val()==0)
		{
			errStr += dynamicPath.rate_author+"<br>"
			jQuery(".pop_up_form .webwidget_rating_simple").addClass('red-border');
		}
		if (errStr != "") {
			if (errStr != "") 
			{
				jQuery(".error_msg").html(errStr);
				return false;
			}
		}
		else
		{
			jQuery(".white_content .loading").show();
			jQuery("#submit_rating").prop('disabled', false);
			//console.log(formData);
		}

}
function rtauth_showResponse(responseText, statusText, xhr, $form)  {
//do extra stuff after submit
//console.log("reponse text1"+statusText);
if(statusText=="success")
{
	
	switch(responseText.trim())
	{
	case 'duplicate': 	jQuery(".error_msg").html(dynamicPath.duplicate_user+"<br>");	
						jQuery(".pop_up_form #visitor_email").addClass('red-border');
						break;
	
	case 'err-2':	jQuery(".error_msg").html(dynamicPath.invalid_image+"<br>");	
					jQuery(".pop_up_form #visitor_image_id").addClass('red-border');
					break;
	
	case 'err-1':   jQuery(".error_msg").html(dynamicPath.image_error+"<br>");
					jQuery(".pop_up_form #visitor_image_id").addClass('red-border');
					break;
					
	default:	jQuery(".pop_up_form").hide();
					//console.log("reponse text2"+responseText);
					
					//jQuery("#success_msg").fadeIn();
					jQuery(".white_content #review_output").fadeIn();
					
					
	}
	

	
	jQuery(".white_content .loading").hide();
	
	
}
}

/*Front end stars rating*/

jQuery.fn.stars = function() {
    return jQuery(this).each(function() {
        // Get the value
        var val = parseFloat(jQuery(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = jQuery('<span />').width(size);
        
		
		//run code for rating star when rtl
		if(dynamicPath.is_rtl)
		{
				// Replace the numerical value with stars
				jQuery(this).html($span.css("background-position", ""+size-64+"px 0"));	
			
		}
		else
		{
				jQuery(this).html($span);		
		}
		
    });
	
}

jQuery(function() {
    jQuery('.rating_stars span.stars').stars();
});

if(dynamicPath.is_author==1)
{
	jQuery("footer").css("clear","both");
}

