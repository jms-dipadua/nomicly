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
/* if (isset($_POST['new_topic'])) {
	nomicly_new_topic();
	}
*/

?>

		<div id="primary">
			<div id="content" role="main">
			
			
	<?php
	// check for login to present idea form or reg/login links
	if ( is_user_logged_in() ) { ?>
		<form method ="post" action ="#">
		<!-- should make this a drop down to select 'idea' or 'topic' -->
		<h2>Create A New Idea</h2>
		<input type="text" name="new_idea" value="" />
		<!-- maybe include a dropdown of all the topics too? -->
		<input type="submit" name="create" value="Create" />
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
			<?php 	// END IF USER_LOGGED_IN()
				}   ?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>