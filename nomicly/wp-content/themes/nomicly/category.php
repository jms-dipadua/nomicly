<?php
/**
 * The template for displaying Category Archive pages.
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
?>

		<section id="primary">
			<div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<!--<h1 class="page-title"><?php
						printf( '<span>' . single_cat_title( '', false ) . '</span>' );
					?></h1>-->

					<?php
						$category_description = category_description();
						if ( ! empty( $category_description ) )
							echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
					?>
				</header>
				<?php twentyeleven_content_nav( 'nav-above' ); ?>

<?php
/* THIS IS WHERE YOU'LL PUT THE TOPIC-RESPONSE FORM 
// WILL NEED TO GET THE CATEGORY DATA
// THEN POPULATE THE FORM
// AND OF COURSE, CHECK FOR LOGIN
*/
	// check for login to present idea form or reg/login links
	if ( is_user_logged_in() ) { 
		//get category information
		global $post;
		$category = get_the_category($post->ID);
	//print_r($categories);
			$int = 0;
			foreach ($category as $categories) {
				$category_id[$int] = $category[$int] -> term_taxonomy_id;
				$int++;
				} // END FOREACH
				// may be just a corner-case bug
				// but WP was appending the main category to every post
				// didn't want that SO
				// before writing the form, check the category
				// if the first entry is = 1 (main category0-
				// then unset that variable 
			if ($category_id[0] = 1) {
				unset($category_id[0]);
				}
			$category_id = implode (",", $category_id);		
			
		echo 
		'<form method ="post" action ="#">
		<h2>Create A New Idea</h2>
		<textarea rows="2" cols="20" name="new_idea" value="">
		</textarea>		
		<input type="hidden" name="category_id" value="'.$category_id.'" />
		<input type="submit" name="create" value="Create" />
		</form>'; 
	} else {
	    echo '<h1><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to Create New Ideas</h1>';
			}
		?>		


				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
						
					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );
					?>

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
		</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
