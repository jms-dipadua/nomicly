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


<!--logged in sidebar-->
	<?php if ( is_user_logged_in() ) { ?>
			<div id="secondary" class="widget-area" role="complementary">		
			
				<!--profile box-->
				<div class="profile-sidebar-box widget media">
				<div class="img">
					<?php 
						global $current_user;
						get_currentuserinfo();
						  
						  echo get_avatar( $current_user->user_email, $size = '42' );?>
					</div>
					<div class="bd">	  
						<h4><a href="<?php bloginfo( 'wpurl' ); ?>/user-profile/"><?php  echo $current_user->user_login ?></a></h4>
						
						<p class="sidebar-stats-ideas"><b>Ideas:</b> <span>
						<?php 	$user_id = get_current_user_id();
						$post_count = count_user_posts($user_id);
						echo "$post_count"; ?> </span>
						</p>
						<p class="sidebar-stats-topics"><b>Topics:</b> <span><?php echo count_user_topics($user_id);?></span></p>
						<p class="sidebar-stats-votes"><b>Votes Available:</b><span></span></p>
					</div>
				</div>
				<!--create topic-->
				<div class="widget">
					<?php
					//get category information
					$category = get_the_category();
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
						 if ($category_id[0] == 1) {
							unset($category_id[0]);
							}
						$category_id = implode (",", $category_id);		
						echo 
					'<form method ="post" action ="#">
					<h3>Create A New Idea</h3>
					<textarea rows="2" cols="20" name="new_idea" id="new_idea" value=""></textarea>		
					<input type="hidden" name="category_id" id="category_id" value="'.$category_id.'" />
					<input type="submit" name="create" class="idea_submit_button" value="Create" class="widget-button" />
					</form>';  ?>
				
				</div>

			</div><!--end secondary-->
		<?php } ?>	
	<!--end logged in sidebar-->

<!--start primary-->
		<section id="primary">
			<div id="content" role="main" class="the_feed">

<?php
/* THIS IS WHERE YOU'LL PUT THE TOPIC-RESPONSE FORM 
// WILL NEED TO GET THE CATEGORY DATA
// THEN POPULATE THE FORM
// AND OF COURSE, CHECK FOR LOGIN
*/
	$category_description = category_description();
	if ( ! empty( $category_description ) ) {
		echo apply_filters( 'category_archive_meta', '<div class="widget"><h2>' . $category_description . '</h2></div>' );
		} else {
	    	$wpurl = get_bloginfo ( 'wpurl' );  
		    echo '<div class="widget"><h2><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Create New Topics</h2></div>';

			}
			
?>
		
			<?php if ( have_posts() ) : ?>


				<?php /* Start the Loop */ ?>
				<div id="fresh-idea"></div>
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
			<div class="widget">
				
					<h1 class="entry-title"><?php _e( 'No Ideas Found', 'twentyeleven' ); ?></h1>

						<p>
					<?php 
						if ( is_user_logged_in() ) { 
						_e( 'Use the form above to help contribute a solution to this problem by providing the first idea!', 'twentyeleven' ); 
						}
					?>
						</p>
				
				</div>
			<?php endif; ?>
			
			</div><!-- #content -->
		</section><!-- #primary -->
		
		<!--nonlogged in sidebar-->
		<?php if ( !is_user_logged_in() ) { ?>		
			<div id="secondary" class="widget-area" role="complementary">	
				<div class="widget">	
				<?php	$wpurl = get_bloginfo ( 'wpurl' );  
					echo '<b><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Participate</b>'; ?>
				</div>
					
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>
		
		<!--logged in nav box-->
		 <?php if ( is_user_logged_in() ) { ?>		
			<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>

<!--<?php //get_sidebar(); ?>-->
<?php get_footer(); ?>
