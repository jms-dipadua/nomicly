<?php
/**
 * Template Name: Authors Template
 * Description: A Page Template for show a user's profile on Nomicly
 * The template for displaying Author Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
get_header(); ?>
		
	<div class="full-width">
		<!--profile box-->
				<div class="profile-sidebar-box media" style="padding: 2%">
				<div class="img">
					<?php global $current_user;
						  get_currentuserinfo();
						  
						  echo get_avatar( $current_user->user_email, $size = '100' );?>
					</div>
					<div class="bd">	  
						<?php
							global $post;
							$author_id=$post->post_author;
							$field=user_login; ?>
							<h3 class="entry-title"><?php the_author_meta( $field, $author_id ); ?> </h3>
							<p class="sidebar-stats-ideas"><b>Ideas:</b> <?php $post_count = count_user_posts($author_id); echo "$post_count";?></p>
							<p class="sidebar-stats-topics"><b>Topics:</b> <?php echo count_user_topics($author_id);?></p>
							
							
							<!--<p><b>Reputation:</b> awesome</p>-->
					</div>
				</div>
	
	</div>		

		<!--start content-->
		<section id="primary">
			<div id="content" role="main" class="user_feed">

			<?php if ( have_posts() ) : ?>

				<?php
					/* Queue the first post, that way we know
					 * what author we're dealing with (if that is the case).
					 *
					 * We reset this later so we can run the loop
					 * properly with a call to rewind_posts().
					 */
					the_post();
				?>

			<!--	<header class="page-header">
					<h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'twentyeleven' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
				</header>-->

				<?php
					/* Since we called the_post() above, we need to
					 * rewind the loop back to the beginning that way
					 * we can run the loop properly, in full.
					 */
					rewind_posts();
				?>

				<?php twentyeleven_content_nav( 'nav-above' ); ?>

				<?php
				// If a user has filled out their description, show a bio on their entries.
				if ( get_the_author_meta( 'description' ) ) : ?>
				<div id="author-info">
					<div id="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 60 ) ); ?>
					</div><!-- #author-avatar -->
					<div id="author-description">
						<h2><?php printf( __( 'About %s', 'twentyeleven' ), get_the_author() ); ?></h2>
						<?php the_author_meta( 'description' ); ?>
					</div><!-- #author-description	-->
				</div><!-- #author-info -->
				<?php endif; ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
				
					<article class="hentry media post" id="post-<?php the_ID(); ?>">
						<div class="img idea-stats">
							<p><b>Positive votes:</b> 400</p>
							<p><b>Negative votes:</b> 30</p>
							<p><b>Consensus:</b> awesome</p>
							<p><b>Influenced ideas:</b> 2</p>
							<p><b>Shares:</b> 5</p>
						</div>
						<?php the_date('m/d/y', '<span class="pub-date">', '</span>'); ?>
						<div class="bd"><h3 class="entry-title"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h3>
							<?php the_content(); ?>
							<!-- ADDS MODIFY IDEA TO THE CONTENT -->
							<?php $wpurl = get_bloginfo ( 'wpurl' );  ?>
							<a href="<?php echo "$wpurl";?>/modify/?idea=<?php the_ID(); ?>" class="widget-button modify-link">Modify Idea</a>
							
						</div>
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
						</footer>
				</article>


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
		
		<!--nav box-->
		<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
		</div><!--end secondary-->	



<?php get_footer(); ?>