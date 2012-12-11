<?php
/**
 * Template Name: Users Template
 * Description: A page for showing user information
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 
// process new idea posting 
 if (isset($_POST['create'])) {
	nomicly_new_idea();
	}

// process topic posting (LATER)
if (isset($_POST['create_topic'])) {
	create_new_topic();
	}

?>

<!--logged in sidebar-->

			
	<div id="secondary" class="widget-area" role="complementary">	
		<!--new idea box-->	
		<?php
			// check for login to present idea form or reg/login links
			if ( is_user_logged_in() ) { ?>
			
				<!--profile box-->
				<div class="profile-sidebar-box widget media">
				<div class="img">
					<?php global $current_user;
						  get_currentuserinfo();
						  
						  echo get_avatar( $current_user->user_email, $size = '42' );?>
					</div>
					<div class="bd">	  
						<?php  echo '<h4><a href="">' . $current_user->user_login . '</h4></a>'; ?>
						<p><b>Ideas:</b> 400</p>
						<p><b>Topics:</b> 30</p>
						<p><b>Reputation:</b> awesome</p>
					</div>
				</div>
			
				<div class="widget new-idea-sidebox">	
					<?php $user_id = get_current_user_id(); ?>
						<form method ="post" action ="#">
						<h3>Create A New Idea</h3>
						<textarea rows="2" cols="20" name="new_idea" id="new_idea" value=""></textarea>
						<input type="hidden" name="user_id" id="user_id" value="<?php echo "$user_id"; ?>" />
						<div><input type="submit" name="create" class="idea_submit_button" value="Create" /></div>
					</form>    
				</div>
			<!--new topic box -->
			<div class="widget new-idea-sidebox">	
				<form method ="post" action ="#">
					<!-- should make this a drop down to select 'idea' or 'topic' -->
					<h3>Create A New Topic</h3>
					Topic Name: <input type="text" name="new_topic_name" value="" /> <br />
					Topic Description: 		
					<textarea rows="2" cols="20" name="new_topic" value="">
					</textarea>
					<!-- maybe include a dropdown of all the topics too? -->
					<input type="submit" name="create_topic" value="New Topic" class="widget-button" />
				</form> 
			</div>
			<?php	} else {
	    		echo '<div class="widget"><h3><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to Create New Ideas</h3></div>';
			}
			?>
	</div>
<!--end logged in sidebar-->


		<div id="primary">
			<div id="content" role="main">
			
			<div id="the_feed">
				
			<?php 
			/* GUTS OF USER PROFILE PAGE
			// verify user is logged in
			// then get ideas and topics from that person
			// note that voting and sharing come from the_content();
			// if we don't have anything from this person, we prompt them for ideas
			*/
			if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			// only get posts from this author and exclude the user profile page
			$query_args = array(
					'author' => $user_id,
					'exclude' => 59
					);
				query_posts( $query_args ); 
				global $wpdb;
					echo "<h2 class='entry-title'>My Ideas</h2>";			
				 while ( have_posts() ) : the_post();  ?>
				<article class="hentry media">
						<div class="img idea-stats">
							<p><b>Positive votes:</b> 400</p>
							<p><b>Negative votes:</b> 30</p>
							<p><b>Consensus:</b> awesome</p>
							<p><b>Influenced ideas:</b> 2</p>
							<p><b>Shares:</b> 5</p>
						</div>
						<div class="bd"><h3 class="entry-title"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h3>
							<?php the_content(); ?>
							<!-- ADDS MODIFY IDEA TO THE CONTENT -->
							<?php $wpurl = get_bloginfo ( 'wpurl' );  ?>
							<a href="<?php echo "$wpurl";?>/modify/?idea=<?php the_ID(); ?>" class="widget-button modify-link">Modify Idea</a>
							
						</div>
				</article>
			<?php endwhile;	?>			
				<?php
				if (!have_posts()) {
				 		echo "Looks like you haven't created any ideas! <br /> 
				 		  That makes us a Sad Panda. :( <br />";
				 		} // END NO POSTS FOR USER	
					?>
					
				<?php
				echo "<h2 class='entry-title'>My Topics</h2>";
				///GET THE TOPICS FROM THIS UER
				// QUERY USER_TOPICS
				// GET THE TOPIC_IDS (ARRAY)
				// THEN FOR EACH ENTRY, GRAB THE CATEGORY AND DISPLAY IT...
				global $wpdb;
				$table_user_topics = $wpdb->prefix."user_topics";
				$topic_query_results = $wpdb->get_results("SELECT topic_id from $table_user_topics WHERE user_id = '$user_id'", 'ARRAY_N'); 
				// collapse the results for the next query
			if (!empty($topic_query_results)) {
				$user_topics = $topic_query_results[0];
				$user_topics = implode(",", $user_topics);
				$args=array(
				  'orderby' => 'name',
				  'order' => 'ASC',
 			  	  'hide_empty' => 0,
 			  	  'include' => $user_topics
 			  	);
				$categories=get_categories($args);

				foreach($categories as $category) { 
					echo '<p>Topic: <a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </p> '; }
					//echo '<p> Description:'. $category->description . '</p>';  

					}// END USER TOPIC DISPLAY
				else {
					echo "You haven't created any topics. <br /> 
				 		  See how Nomicly can help solve problems by creating a discussion topic.";
				 	}// END NO TOPICS BY USER
				?>
		
					
			<?php 	// END IF USER_LOGGED_IN()
				}   ?>
				</div>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>