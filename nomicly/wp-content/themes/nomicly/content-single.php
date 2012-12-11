<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// Modify Idea enhancement from line 23 to 26
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div id="article_content" class="post-<?php the_ID(); ?>">

	<header class="entry-header">
	
		<div class="media">
				<div class="feed-gravatar img"> <? echo get_avatar( get_the_author_meta('user_email'), $size = '48'); ?><a href="<?php bloginfo( 'wpurl' ); ?>/user-profile/"><?php  the_author(); ?></a> </div>
				<div class="bd">
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				</div>
			</div>
	</header><!-- .entry-header -->

	<div class="entry-content">
	<!-- ADDS MODIFY IDEA TO THE CONTENT -->
	<? if (is_user_logged_in()) { ?>
	<?php $wpurl = get_bloginfo ( 'wpurl' );  ?>
			<a href="<?php echo "$wpurl";?>/modify/?idea=<?php the_ID(); ?>" class="widget-button modify-link">Modify Idea</a>
	<?php }//END IF LOGGED IN  ?>

		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	</div> <!-- #article_content --> 
	<footer class="entry-meta">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
			if ( '' != $tag_list ) {
				$utility_text = __( 'This idea addresses %1$s and tagged %2$s.', 'twentyeleven' );
			} elseif ( '' != $categories_list ) {
				$utility_text = __( 'This idea addresses %1$s.', 'twentyeleven' );
			} 

			printf(
				$utility_text,
				$categories_list,
				$tag_list,
				esc_url( get_permalink() ),
				the_title_attribute( 'echo=0' ),
				get_the_author(),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) )
			);
		?>
		
		<?php if ( get_the_author_meta( 'description' ) && ( ! function_exists( 'is_multi_author' ) || is_multi_author() ) ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries ?>
		<div id="author-info">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 68 ) ); ?>
			</div><!-- #author-avatar -->
			<div id="author-description">
				<h2><?php printf( __( 'About %s', 'twentyeleven' ), get_the_author() ); ?></h2>
				<?php the_author_meta( 'description' ); ?>
				<div id="author-link">
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyeleven' ), get_the_author() ); ?>
					</a>
				</div><!-- #author-link	-->
			</div><!-- #author-description -->
		</div><!-- #author-info -->
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
