/*
// File Purpose: 
// 		js/jquery to augment nomicly functionality 
*/


/*
// this function controls the hot or not voting
// 1. identifies the link clicked on by the user
// 2. sets a hidden input value to the selected idea
// 3. submits the form for processing by server

// TO DO NEXT:
// a. rather than submit the page
// b. should submit the vote to the server
// c. indicate (to user) the vote was submitted successfully
// d. present the stats on the last idea
//	- with ideas in context
// e. allow user to dismiss the stats and get the next idea
*/


/*
// submit votes for hot or not
*/
jQuery(function() {
	jQuery('.vote-link').click(function() {
		var votedID = jQuery(this).attr('id');
		var setHidden = jQuery('#chosen_idea').attr('value', votedID);
		var idea1 = jQuery('#idea0').val();
		var idea2 = jQuery('#idea1').val();
		var ajaxurl = "../../nomicly/wp-admin/admin-ajax.php";
		//alert( jQuery(setHidden).attr('value') ) ;
//		jQuery('#compare-ideas-submit').click();
		jQuery.ajax({
			url: ajaxurl, 
			type: "POST",
			dataType:'json',
			data: 
			{
      		action:'process_hot_not_vote',
      		idea0: idea1,
      		idea1: idea2,
      		chosen_idea: votedID
		  	 }, 
 			success:  function(response){
      					//alert(response.get_next_ideas);
      					jQuery('.content_for_0').append(response.idea_1_consensus_percentage);
      					jQuery('.content_for_1').append(response.idea_2_consensus_percentage);
      					jQuery('.content_for_1').append("<br />Out of a total ").append(response.total_votes).append(" votes<br />");
      					jQuery('#content').append(response.get_next_ideas);
   						}
			});// END POST
			return false;
		}); // END VOTE-LINK CLICKED
		
});  // END submit vote 

/*
// GET NEW IDEAS
// (after voting on an idea in hot/not)
*/
jQuery(function() {
	jQuery('#primary').delegate('#get_next_ideas', 'click', function() {
		var ajaxurl = "../../nomicly/wp-admin/admin-ajax.php";
		jQuery.ajax({
			url: ajaxurl, 
			type: "POST",
			dataType:'json',
			data: {
	      		action:'get_next_ideas'
      	  	 }, 
 		success:  function(response){
      		alert(response);
   			}
});// END POST
		return false;
	}); // END VOTE-LINK CLICKED
});  // END MAIN JQUERY



