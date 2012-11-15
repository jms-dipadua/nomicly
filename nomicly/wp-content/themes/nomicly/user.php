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

		<div id="primary">
			<div id="content" role="main">
			
			
	<?php
	// check for login to present idea form or reg/login links
	if ( is_user_logged_in() ) { ?>
		<form method ="post" action ="#">
		<!-- should make this a drop down to select 'idea' or 'topic' -->
		<h2>Create A New Idea</h2>
		<textarea rows="2" cols="20" name="new_idea" value="">
		</textarea>
		<!-- maybe include a dropdown of all the topics too? -->
		<input type="submit" name="create" value="Create" />
		</form>    <br />
		<form method ="post" action ="#">
		<!-- should make this a drop down to select 'idea' or 'topic' -->
		<h2>Create A New Topic</h2>
		Topic Name: <input type="text" name="new_topic_name" value="" /> <br />
		Topic Description: 		
		<textarea rows="2" cols="20" name="new_topic" value="">
		</textarea>
		<!-- maybe include a dropdown of all the topics too? -->
		<input type="submit" name="create_topic" value="New Topic" />
		</form>    
		<?php	} else {
	    echo '<h1><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to Create New Ideas</h1>';
			}
		?>	
				
			<?php 
			if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
				$query_args = array(
					'post_author' => $user_id,
					);
				query_posts( $query_args ); 
				global $wpdb;
					echo "<br /><h2>My Ideas</h2>";
				?>
			
			<?php while ( have_posts() ) : the_post();  ?>
				<h2> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h2>

				<?php the_content(); ?>
			<?php endwhile;	?>			
				<?php
				if (!have_posts()) {
				 		echo "Looks like you haven't created any ideas! <br /> 
				 		  That makes us a Sad Panda. :( <br />";
				 		} // END NO POSTS FOR USER	
					?>
					
				<?php
				///GET THE TOPICS FROM THIS UER
				// QUERY USER_TOPICS
				// GET THE TOPIC_IDS (ARRAY)
				// THEN FOR EACH ENTRY, GRAB THE CATEGORY AND DISPLAY IT...
				global $wpdb;
				$table_user_topics = $wpdb->prefix."user_topics";
				$topic_query_results = $wpdb->get_results("SELECT topic_id from $table_user_topics WHERE user_id = '$user_id'", 'ARRAY_N'); 
				// collapse the results for the next query
				$user_topics = $topic_query_results[0];
				$user_topics = implode(",", $user_topics);
				
				$args=array(
				  'orderby' => 'name',
				  'order' => 'ASC',
 			  	  'hide_empty' => 0,
 			  	  'include' => $user_topics
 			  	);
				$categories=get_categories($args);
				echo "<br /> <h2>My Topics:</h2>";
				foreach($categories as $category) { 
					echo '<p>Topic: <a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </p> '; }
					//echo '<p> Description:'. $category->description . '</p>';  
				?>
		
					
			<?php 	// END IF USER_LOGGED_IN()
				}   ?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>