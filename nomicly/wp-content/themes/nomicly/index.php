<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 */

get_header(); 

// process new idea posting 
 if (isset($_POST['create'])) {
	nomicly_new_idea();
	}
?>

		<div id="primary">
			<div id="content" role="main">
	<?php if ( have_posts() ) : ?>

				<?php twentyeleven_content_nav( 'nav-above' ); ?>
		<?php
	// check for login to present idea form or reg/login links
	if ( is_user_logged_in() ) { ?>
		<form method ="post" action ="#">
		<h2>Create A New Idea</h2>
		<textarea rows="2" cols="20" name="new_idea" value="">
		</textarea>
		<input type="submit" name="create" value="Create" />
		</form>    
		<?php	} else {
			$wpurl = get_bloginfo ( 'wpurl' );  
		    echo '<h1><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Create New Ideas</h1>';
			}
		?>		
		
				<?php /* Start the Loop */ ?>
		
				<?php while ( have_posts() ) : the_post(); ?>
				<!-- APPEND THE MODIFY FUNCTIONALITY IF USER IS LOGGED IN --> 
				
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