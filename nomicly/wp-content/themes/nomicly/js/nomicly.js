jQuery(function() {

	jQuery('.vote-link').click(function() {
	
		var votedID = jQuery(this).attr('id');
		var setHidden = jQuery('#chosen_idea').attr('value', votedID);
		//alert( jQuery(setHidden).attr('value') ) ;
		jQuery('#compare-ideas-submit').click();
		return false;
	});

});