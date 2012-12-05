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
 			// upon request success
 			// present the statistics to the user
 			// NOTE: need to create special ids/classes 
 			// 	to identify the "voting results" 
 			// 	that way it's easy after the user 'gets new ideas'
 			//	to drop the stats from the page (i.e. "reset the voting booth")
      					jQuery('.content_for_0').append('<span class="idea_1_consensus">'+response.idea_1_consensus_percentage+'</span>');
      					jQuery('.content_for_1').append('<span class="idea_2_consensus">'+response.idea_2_consensus_percentage+'</span>');
      					

      					jQuery('.content_for_1').append("<p class='total_votes'>Out of a total of "+response.total_votes+' votes</p>');
      					
      					jQuery('#content').append('<p class="next_ideas">'+response.get_next_ideas+'</p>');
      					
      					jQuery('.content_for_0 .vote-link, .content_for_1 .vote-link').hide();
      					//todo:  instead of hiding this, remove it and then recreate it.  that would be more fool-proof
      					
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
 			// upon request success
 			// swap out all old content with new content
 			// i.e. reset the voting booth
      			//alert(response.idea_1_data.ID);
      			var id1 = response.idea_1_data.ID;
      			var title1 = response.idea_1_data.post_title;
      			var id2 = response.idea_2_data.ID;
      			var title2 = response.idea_2_data.post_title;
      			
      			jQuery('.content_for_0').attr('id', id1);
      			jQuery('.content_for_0 .vote-link').attr('id', id1);
      			jQuery('.content_for_0 .entry-title').text(title1);
      			jQuery('.content_for_0 #idea0').val(id1);
      			
      			jQuery('.content_for_1').attr('id', id2);
      			jQuery('.content_for_1 .vote-link').attr('id', id2);
      			jQuery('.content_for_1 .entry-title').text(title2);
      			jQuery('.content_for_1 #idea1').val(id2);
      			
      			jQuery('.idea_1_consensus, .idea_2_consensus, .total_votes, .next_ideas').remove();
      			
      			jQuery('.content_for_0 .vote-link, .content_for_1 .vote-link ').show();
      			
   			}
});// END POST
		return false;
	}); // END VOTE-LINK CLICKED
});  // END GET NEW IDEAS

/*
//  CREATE NEW IDEAS
//  a. validate (?) form
//  b. accept idea submission
//	c. return query for all new ideas created since idea was submitted
//  d. return new ideas (in order received) and append to top of pre-existing content
*/

jQuery(function() {
   jQuery('.idea_submit_button').click(function() {
	var idea = jQuery('#new_idea').val();
	var cat_id = jQuery('#category_id').val();
	var user_id = jQuery('#user_id').val();
	var ajaxurl = "../nomicly/wp-admin/admin-ajax.php";
	
		jQuery.ajax({
			url: ajaxurl, 
			type: "POST",
			dataType:'json',
			data: {
	      		action:'create_new_idea',
	      		new_idea: idea,
      			category_id: cat_id,
      			user_id: user_id
      			  }, 
 		success:  function(response){
 			// upon request success
 			// append new idea to top of existing ideas
 				// later you can also pole for other new ideas and append those too
      			//alert(response.idea_1_data.ID);
			// returns full array of the new post
			// so you have title, id, link, cat, etc
				// TELL USER WE HAVE NEW IDEA
				jQuery('#new_idea').val('Saving New Idea...');
      			var theURL = response.new_idea_data.guid;
      			// PRESENT THE NEW IDEA
      			jQuery('#fresh-idea').load(theURL +" #article_content");
      			      	
      		// then empty the form for a new idea
      		jQuery('#new_idea').val('');
   			}  // END response
		}); // END .ajax
					return false;
	}); // END .click
});  // END CREATE (AND RETURN) NEW IDEAS

/*
//  MODIFY EXISTING IDEAS
//  a. validate (?) form
//  b. accept idea submission
//	c. return the modified idea
//  d. append to bottom of content
*/

jQuery(function() {
   jQuery('.submit_modifed_idea').click(function() {
	var idea = jQuery('#new_idea').val();
	var cat_id = jQuery('#category_id').val();
	var parent_id = jQuery('#post_parent').val();
	var user_id = jQuery('#user_id').val();
	var ajaxurl = "../../nomicly/wp-admin/admin-ajax.php";
	
		jQuery.ajax({
			url: ajaxurl, 
			type: "POST",
			dataType:'json',
			data: {
	      		action:'modify_existing_idea',
	      		new_idea: idea,
      			category_id: cat_id,
      			post_parent: parent_id,
      			user_id: user_id
      			  }, 
 		success:  function(response){
      			var modified_idea = response.new_idea_data.post_title;
      			var modified_idea_id = response.new_idea_data.ID;
      			// PRESENT THE NEW IDEA
      			jQuery('#newly_modified_idea').html('<h1 class="entry-title">'+modified_idea+'</h1>');      			      	
      		// then put new idea into the form for further modification
      		jQuery('#new_idea').val(modified_idea);
      		// reset the parent id to the newly created post id (for ancestry purposes)
      		jQuery('#post_parent').val(modified_idea_id);
   			}  // END response
		}); // END .ajax
					return false;
	}); // END .click
});  // END CREATE (AND RETURN) NEW IDEAS




/*
// NEW IDEA POLLING
// 	a. counter that periodically (60 s) queries for new posts
// 	b. if more, present "get more ideas"
//	c. query for new ideas since last poll (time set at page load?)
// 	d. return new ideas and append to top of content
*/



