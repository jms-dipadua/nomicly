/*
// File Purpose: 
// 		js/jquery to augment nomicly functionality 
*/

/* onload, get these functions ready */

jQuery(function() {
	if (jQuery('.home').length > 0) {
		determine_ideas_voted_on();
	}

});


/*
//  CREATE NEW IDEAS
//  a. validate (BUG) form -- cannot be empty + checks for swearing?
//  b. accept idea submission
//	c. return query for all new ideas created since idea was submitted
//  d. return new ideas (in order received) and append to top of pre-existing content
*/
jQuery(function() {
   jQuery('.idea_submit_button').click(function() {
	var idea = jQuery('#new_idea').val();
	var cat_id = jQuery('#category_id').val();
	var user_id = jQuery('#user_id').val();
		var ajaxurl = "http://www.jamesdipadua.com/experimental/nomicly/wp-admin/admin-ajax.php";
	
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
//  a. validate (BUG) form -- cannot be empty and cannot be the exact same as original
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
      			jQuery('#newly_modified_idea').html('<h1 class="entry-title">Modified idea:<br />'+modified_idea+'</h1>');      			      	
      		// then put new idea into the form for further modification
      		jQuery('#new_idea').val(modified_idea);
      		// reset the parent id to the newly created post id (for ancestry purposes)
      		jQuery('#post_parent').val(modified_idea_id);
      		//show the idea div that was previously hidden
      		jQuery('#newly_modified_idea').show();
   			}  // END response
		}); // END .ajax
					return false;
	}); // END .click
});  // END MODIFY EXISTING IDEAS 

/*
// DETERMINE WHICH IDEAS THE PERSON HAS VOTED ON
	// 1. for each element of "article" get the ID
	// 2. split the returned ID on '-'
	// 3. set the idea_id = element[1]
	// 	3-b. get the user_id (getting from functions.php instead)
	// 4. pass everything to php
	// 5. return a true or fales
	// 6-a. if true, get the stats and display them
	// 6-b. if false, append the vote buttons to the div (or put them in there however adria tells you to)
*/
function determine_ideas_voted_on() {
	jQuery('article').each(function() {
		var idea_data = jQuery(this).attr('id');
		var idea_array = idea_data.split('-');
		var idea = idea_array[1];
		var ajaxurl = "http://www.jamesdipadua.com/experimental/nomicly/wp-admin/admin-ajax.php";
		var div_stats = '#stats_'+idea;
		//check to see if div_stats has content
		if( !jQuery.trim( jQuery(div_stats).html() ).length ) {
	
		jQuery.ajax({
			url: ajaxurl, 
			type: "get",
			dataType:'json',
			data: {
	      		action:'determine_voter_idea_status',
	      		idea_id: idea,
      			  }, 
 		success:  function(response){
 			// determine case of response (either logged in or needs to)
 			if (response.voter_status_data == "NULL") {
  			// print voter buttons
  			// we'll get them to register in a soon-coming revision
  			// goal is to just turn on the query and start debugging that!
				jQuery(div_stats).append('<a href="#" id="agree_'+idea+'" class="idea-vote agree-with">Agree</a>  <a href="#" id="disagree_'+idea+'" class="idea-vote disagree-with">Disagree</a>');
				}
 			else {
 				// LOGGED IN USER
 				// HAS VOTED
 				if (response.voter_status_data.vote_status == 1) {
 				  // new AJAX call to get stats
 				  	jQuery.ajax({
						url: ajaxurl, 
						type: "get",
						dataType:'json',
						data: {
							action:'fetch_idea_consensus',
							idea_id: idea,
							  }, 
						success:  function(response2){
							// will need to get the person's actual vote still...
							var votes_yes = response2.consensus_data.votes_yes;
							var votes_no = response2.consensus_data.votes_no;
								if (response.voter_status_data.vote_type == 1) {
								var vote_type = 'Agreed';
								}
								else {
								var vote_type = 'Disagreed';
								}
							jQuery(div_stats).append('<p>Current Consensus<br /> Votes Yes: '+votes_yes+'   Votes No: '+votes_no+'   <br /> You '+vote_type+' with this idea.</p>');
						} // end RESPONSE - GET STATS
						}); // END -GET STATS AJAX
 				  // in future let them re-vote (change vote) 
 				  // for now to minimize abuse we'll just show it the one time
 				} // END HAS VOTED
				else if (response.voter_status_data.vote_status == 0) { // NOT VOTED
					  // print voter buttons
					jQuery(div_stats).append('<a href="#" id="agree_'+idea+'" class="idea-vote agree-with">Agree</a>  <a href="#" id="disagree_'+idea+'" class="idea-vote disagree-with">Disagree</a>');
						}// END NOT VOTED
 				} // END LOGGED IN USER
   			}  // END response
		}); // END .ajax
		}//end check for div_stats content
	});// END EACH
} // END DETERMINE IDEAS VOTED ON


/*
// VOTING - MAIN FEED (I.E. NON-HOT/HOT)
// 	1. get the id for the idea voted on (may need to split the article)
// 	2. send to process
//  3. determine whether the vote was cast (i.e. if they have a vote to use)
//	4-a. if vote processed, then display the stats
// 	4-b. if the vote was not processed (error or no votes avail) then display message
		// - this includes the prompt to register if the user is not logged in

//   DON'T FORGET TO APPEND THE STATS TO THE IDEA ONCE THE PERSON HAS VOTED . lol.
*/
jQuery(function() {
	jQuery('.vote-box').delegate('.idea-vote', 'click', function() {
		//set up all the data
		var vote_data = jQuery(this).attr('id');
		var vote_array = vote_data.split('_');
		var vote_choice = vote_array[0];
		var idea = vote_array[1];
		var ajaxurl = "http://www.jamesdipadua.com/experimental/nomicly/wp-admin/admin-ajax.php";
		var div_stats = '#stats_'+idea;
		if (vote_choice == "agree") {
			type = '1';
		}
		else {
			type = '0';
		}
		jQuery.ajax({
			url: ajaxurl, 
			type: "POST",
			dataType:'json',
			data: {
	      		action:'process_user_vote',
	      		idea_id: idea,
	      		vote_type: type
      			  }, 
			success:  function(response){
				// VOTE NOT COUNTED
				if (response.vote_response_data == "no-vote") {
					jQuery(div_stats).html(response.vote_message);
				}
				// SUCCESSFUL VOTE
				else {
					// remove the vote buttons 
					// append the consensus to the stats box
						var votes_yes = response.vote_response_data.votes_yes;
						var votes_no = response.vote_response_data.votes_no;
						jQuery(div_stats).html('<p>Vote Successful! <br /> Current Consensus<br /> Votes Yes: '+votes_yes+'    Votes No: '+votes_no+'</p>');
					}// END SUCCESSFUL VOTE
				}	// end RESPONSE 
 				  // in future let them re-vote (change vote) 
 				  // for now to minimize abuse we'll just show it the one time					
			}); // END .ajax		
	return false; // so nobody goes anywhere...
	});
}); // END VOTING (NON-HOT/NOT)

/*
// GET THE NUMBER OF VOTES A UERS HAS AVAILABLE (OR SHOULD THIS BE IN PHP ONLY?)
	// - seems like this is largely handled when a person tries to vote
	// - the argument *for* this function is to *display* the num votes to the user
		// -- and to update the num votes after a person votes
*/ 


/*
// NEW IDEA POLLING
// 	a. counter that periodically (60 s) queries for new posts
// 	b. if more, present "get more ideas"
//	c. query for new ideas since last poll (time set at page load?)
// 	d. return new ideas and append to top of content
*/


/*
// this function controls the hot or not voting
// 1. identifies the link clicked on by the user
// 2. sets a hidden input value to the selected idea
// 3. submits the form for processing by server
// 4. returns the current stats for that hot/not combination
// 5. user dismisses stats to get next idea pair

// TO DO NEXT:
// c. indicate (to user) the vote was submitted successfully (BUG here)
*/


/*
// PROCESS HOT OR NOT VOTES
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
      					jQuery('.content_for_0 .compare-vote-results').append('<span class="idea_1_consensus">'+response.idea_1_consensus_percentage+'</span>');
      					jQuery('.content_for_1 .compare-vote-results').append('<span class="idea_2_consensus">'+response.idea_2_consensus_percentage+'</span>');
      					

      					jQuery('.compare-results-box').append("<p class='total_votes'>Total Votes: "+response.total_votes+'</p>');
      					
      					jQuery('.compare-results-box').append('<p class="next_ideas">'+response.get_next_ideas+'</p>');
      					
      					jQuery('.compare-results-box').show();
      					jQuery('.compare-vote-results').show();
      					
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
			type: "get",
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

