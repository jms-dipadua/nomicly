<?php
/**
 * Template Name: Hot or Not Template
 * Description: A Page Template for comparing 2 random posts
 *
 * We are creating two queries to fetch the proper posts and a custom widget for the sidebar.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

// Enqueue showcase script for the slider
//wp_enqueue_script( 'twentyeleven-showcase', get_template_directory_uri() . '/js/showcase.js', array( 'jquery' ), '2011-04-28' );

get_header(); ?>

		<div id="primary" class="showcase">
			<div id="content" role="main">
				<form action="" method="GET">
				<?php 
				query_posts( 'posts_per_page=2&orderby=rand' );
				while ( have_posts() ) : the_post(); ?>

				
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'compare-ideas' ); ?>>
					<header class="entry-header">
						<h2 class="entry-title"><?php the_title(); ?></h2>
					</header><!-- .entry-header -->
					
					<input type="hidden" value="vote-<?php the_ID(); ?>" />
					<input type="submit" value="vote-<?php the_ID(); ?>" />
					</article><!-- #post-<?php the_ID(); ?> -->
								

				<?php endwhile; ?>
					
				</form>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>