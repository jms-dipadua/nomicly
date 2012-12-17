<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'single' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->
		
		<!--navbox and related posts-->
			
			<div class="secondary widget-area" role="complementary">	
			<div class="widget related-posts-widget"><?php related_posts(); ?></div>
				<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
			
			
		

		
<?php get_footer(); ?>