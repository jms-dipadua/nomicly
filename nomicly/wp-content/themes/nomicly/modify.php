<?php
/**
 * Template Name: Modify Template
 * Description: This page is for modifying existing ideas.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 
if(isset($_POST['modify'])) {
	nomicly_modify_idea();
	}
?>

		<div id="primary">
			<div id="content" role="main">
				
	<?php
		if (is_user_logged_in()) {						
				//global $wpdb;	
				$logged_in = true;
				if(isset($_GET['idea'])) {
				$post_id = $_GET['idea'];
				}// END GET
				else {
				$logged_in = false;
				$message = "I'm sorry. The idea you were looking for could not be found. Maybe click back and try again?<br />";
				}

				$query_args = array(
					'p' => $post_id
					);
				query_posts( $query_args ); 
				while ( have_posts() ) : the_post(); ?>
				<?php the_title();?>
		<form method="post" action="#">
		<h2>Modify Existing Idea</h2>
		<textarea rows="2" cols="20" name="new_topic">
		<?php the_title();?>
		</textarea>
		<!-- maybe include a dropdown of all the topics too? -->
		<input type="hidden" name="cateogry_id" value="<?php get_the_category($post_id); ?>" />
		<input type="hidden" name="post_id" value="<?php the_ID(); ?>" />
		<input type="submit" name="modify_idea" value="Modify" />
		</form>
				<?	endwhile;
		}// END IS USER LOGGED IN
		else {
			echo 'Sorry. This page is for registered users only. Please <a href="../wp-login.php">Login</a> or <a href="../wp-login.php?action=register">Register</a> to modify ideas.<br />';			
			}
	?>


			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>