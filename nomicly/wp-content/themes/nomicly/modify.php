<?php
/**
 * Template Name: Modify Template
 * Description: This page is for modifying existing ideas.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 
if(isset($_POST['modify_idea'])) {
	nomicly_modify_idea();
	}
?>

	<!--logged in sidebar, minus navbox which is after #primary-->
	<?php if ( is_user_logged_in() ) { ?>
	<div id="secondary" class="widget-area" role="complementary">		

			<!--related ideas box-->
				<div class="widget recent-ideas-sidebox">	
					<h3>Related Ideas</h3>
					<ul>
						<li><a href="">This is a related idea link</a></li>
						<li><a href="">This is a related idea link</a></li>
						<li><a href="">This is a related idea link</a></li>
					</ul>
					<p><a href="" class="widget-button">View all</a></p>
				</div>

			</div><!--end secondary-->
		<?php } ?>	
	<!--end logged in sidebar-->
		
		
		
		<div id="primary">
			<div id="content" role="main">
			
	<?php
		if (is_user_logged_in()) {						
				//global $wpdb;	
				$logged_in = true;
				$user_id = get_current_user_id();
			if (isset($_GET['idea'])) {
				$post_id = $_GET['idea'];
				$post_id = intval($post_id);
				$category = get_the_category($post_id);
				$int = 0;
				foreach ($category as $categories) {
					$category_id[$int] = $category[$int] -> term_taxonomy_id;
					$int++;
					} // END FOREACH
				$category_id = implode (",", $category_id);
			}// END GET
			else {
				$logged_in = false;
				$message = "Sorry. The idea you were looking for could not be found.<br />";
				}
				$query_args = array(
					'p' => $post_id
					);
				query_posts( $query_args ); 
				while ( have_posts() ) : the_post(); ?>
				<div class="widget">
					<h3> Original Idea</h3> <p><?php the_title();?></p>
					<?php

						$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );
						if ( $categories_list ):
					?>
					<p class="cat-links">
						<?php printf( __( '<span class="%1$s">Issues addressed:</span> %2$s', 'twentyeleven' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
						$show_sep = true; ?>
					</p>
					<?php endif; // End if categories ?>
			
					<form method="post" action="#">
						<h3>Modify Existing Idea</h3>
						<textarea rows="2" cols="20" name="new_idea" id ="new_idea" value=""><?php the_title();?></textarea>
						<!-- maybe include a dropdown of all the topics too? -->
						<input type="hidden" name="category_id" id = "category_id" value="<?php echo "$category_id"; ?>" />
						<input type="hidden" name="post_parent" id = "post_parent" value="<?php the_ID(); ?>" />
						<input type="hidden" name="user_id" id = "user_id" value="<?php echo "$user_id"; ?>" />
						<input type="submit" name="modify_idea" class ="widget-button submit_modifed_idea" value="Modify" />
					</form>
				</div>
				<?	endwhile;
		}// END IS USER LOGGED IN
		else {
		
			$wpurl = get_bloginfo ( 'wpurl' );  
			echo '<div class="widget">Sorry. This page is for registered users only. Please <a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to modify ideas.</div>';			
			}
	?>
			<!--newly modified idea is inserted here with ajax in nomicly.js-->
			<div id="newly_modified_idea" class="widget"></div>

			</div><!-- #content -->
		</div><!-- #primary -->
		
	<!--logged in nav box-->
		 <?php if ( is_user_logged_in() ) { ?>		
			<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>

<?php get_footer(); ?>