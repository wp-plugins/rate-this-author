//Approving review

jQuery(".review_list_container .to_be_approve").one("click", function(){

var id= jQuery(this).attr("id");

 jQuery(this).css("opacity","0.6");
// jQuery(this).removeClass("to_be_approve").text("Approved").addClass("approved").fadeIn();

 jQuery.ajax({         
						  url: ajaxurl,            
						  type: "POST",  
						 data: {           
						 action: "rtauth_update_approval_status",          
						  id:id
						  },                
						  success: function(data1) {      
						//  jQuery(".review_list_container .to_be_approve#"+id+"").hide();
						//  jQuery(".review_list_container .to_be_approve#"+id+"").parent().find(".approved").fadeIn();
						    var target = ".review_list_container .to_be_approve#"+id+"";
						    jQuery(target).hide();
 						    jQuery(target).removeClass("to_be_approve").text("Approved").addClass("approved").fadeIn();
						   
						//  console.log(data1);
						  },               
						  error: function(err) {       
							  
						    jQuery(".review_list_container .to_be_approve#"+id+"").css("opacity","1");            
						  }            
					});  

});


jQuery(".review_list_container .to_remove").click(function()
{
	
	var get_id = jQuery(this).attr("id").split('_');
	//console.log(get_id[1]);
	jQuery(this).parent().parent().fadeOut();
	jQuery.ajax({         
						  url: ajaxurl,            
						  type: "POST",  
						 data: {           
						 action: "rtauth_remove_review",          
						  id:get_id[1]
						  },                
						  success: function(data1) {      
							
							
						   
						 // console.log(data1);
						  },               
						  error: function(err) {       
							  
						   //console.log(err);
						   // jQuery(".review_list_container .to_be_approve#"+id+"").css("opacity","1");            
						  }            
					});  
	
});


/*Admin end stars rating*/

jQuery.fn.stars = function() {
    return jQuery(this).each(function() {
        // Get the value
        var val = parseFloat(jQuery(this).html());
        // Make sure that the value is in 0 - 5 range, multiply to get width
        var size = Math.max(0, (Math.min(5, val))) * 16;
        // Create stars holder
        var $span = jQuery('<span />').width(size);
        // Replace the numerical value with stars
        jQuery(this).html($span);
    });
}

jQuery(function() {
    jQuery('.rating_stars span.stars').stars();
});