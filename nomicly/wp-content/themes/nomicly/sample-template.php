<?php
/**
 * Template Name: Sample Template
 * Description: A Sample Page Template 
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
				
			
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/**
					 * inside loop is here
					 * 
					 */
					
				?>

				<?php endwhile; ?>			


			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>