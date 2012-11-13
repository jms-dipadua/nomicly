<?php
/**
 * Template Name: Users Template
 * Description: A Page Template for show a user's profile on Nomicly
 *
 * We are creating two queries to fetch the proper posts and a custom widget for the sidebar.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

// Enqueue showcase script for the slider
//wp_enqueue_script( 'twentyeleven-showcase', get_template_directory_uri() . '/js/showcase.js', array( 'jquery' ), '2011-04-28' );

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

				<?php twentyeleven_content_nav( 'nav-above' ); ?>
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
				/* MODIFY the Loop */ 
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$query_args = array(
					'post_author' => $user_id,
					'order'    => 'ASC'
					);
				query_posts( $query_args );
				/* START the Loop */ 
				 while ( have_posts() ) : the_post(); 
				 if (!have_posts()) {
				 	echo "Looks like you haven't created any ideas! <br /> 
				 		  That makes us a Sad Panda. :( <br />";
				 	} // END NO POSTS FOR USER				 
				 } // END IF IS_USER
			else {
				echo "We can't find your ideas until you Login or Register...<br />";
			}
				
				?>

					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

				<?php twentyeleven_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>