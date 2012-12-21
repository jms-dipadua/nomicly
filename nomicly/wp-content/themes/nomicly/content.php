<?php
/**
 * The default template for displaying content
 // NOTE THIS PAGE HAS BEEN MODIFIED TO ACCOUNT FOR LOGGED-IN/LOGGED-OUT STATUS
 	//	1. modify ideas
 	//  2. vote status +/- vote buttons 
 	// CIRCA LINES 40
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( is_sticky() ) : ?>
				<hgroup>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<h3 class="entry-format"><?php _e( 'Featured', 'twentyeleven' ); ?></h3>
				</hgroup>

			<?php else : ?>
			
			<div class="media">
				<div class="feed-gravatar img"> 
				<? 
					echo get_avatar( get_the_author_meta('user_email'), $size = '48'); 
					$author_id=$post->post_author;
					
					?>

				<a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php the_author_meta('display_name'); ?></a>
				</div>
				<span class="pub-date"><?php the_time('m/d/y'); ?></span>
				<div class="bd">
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( 'post' == get_post_type() ) : ?>
		<!--	<div class="entry-meta">
				<?php twentyeleven_posted_on(); ?>
			</div>.entry-meta -->
			<?php endif; ?>


			<!--<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Reply', 'twentyeleven' ) . '</span>', _x( '1', 'comments number', 'twentyeleven' ), _x( '%', 'comments number', 'twentyeleven' ) ); ?>
			</div>-->
				<? if (is_user_logged_in()) { ?>
				 <?php $wpurl = get_bloginfo ( 'wpurl' );  ?>
			<a href="<?php echo "$wpurl";?>/modify/?idea=<?php the_ID(); ?>" class="modify-link widget-button">Modify Idea</a>
					<?php }//END IF LOGGED IN  ?>

			<div id='stats_<?php the_ID(); ?>' class="vote-box"> </div>

		<?php if ( is_search() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
		<?php else : ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<?php endif; ?>

		<footer class="entry-meta">
			<?php $show_sep = false; ?>
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
				if ( $categories_list ):
			?>
			<span class="cat-links">
				<?php printf( __( '<span class="%1$s">This idea addresses:</span> %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
				$show_sep = true; ?>
			</span>
			<br />
				<span class="ancestry-links">
				<a href="" id="ancestry_<?php the_ID(); ?>">&nbsp;</a>
				<a href="" id="progeny_<?php the_ID(); ?>">&nbsp;</a>
				</span>
			<?php endif; // End if categories ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
				if ( $tags_list ):
				if ( $show_sep ) : ?>
			<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
			<span class="tag-links">
				<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
				$show_sep = true; ?>
			</span>
			<?php endif; // End if $tags_list ?>
			<?php endif; // End if 'post' == get_post_type() ?>




		</footer><!-- #entry-meta -->
	</article><!-- #post-<?php the_ID(); ?> -->
