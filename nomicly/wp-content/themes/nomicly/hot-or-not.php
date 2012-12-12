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

get_header(); 
//if (isset($_POST['vote'])) {
//	$pair_id = nomicly_record_vote();
//	$pair_stats = get_hot_not_stats($pair_id);
//}
?>

		<div id="primary" class="showcase">
			<div id="content" role="main">

			<?php
				if (isset($_POST['process_hot_not_vote'])) {
				echo "Statistics for the Last Pair:<br />";
				echo "Idea 1 selected: ".$pair_stats['idea_1_consensus_percentage']."<br />";
				echo "Idea 2 selected: ".$pair_stats['idea_2_consensus_percentage']."<br />";
				}
			?>
	<?php
	// check for login to present idea form or reg/login links
	if ( is_user_logged_in() ) { ?>
			<form action="#" method="post" id="compare-ideas-form">
			<?php 
			query_posts( 'posts_per_page=2&orderby=rand' );
			$nomicly_int = 0;
			while ( have_posts() ) : the_post(); ?>	
				<article id="<?php the_ID(); ?>" class="<?php echo 'content_for_'.$nomicly_int;?>">
				
				<div class="media">
					<div class="img compare-vote-results"></div>
					<div class="bd"><h2 class="entry-title"><?php the_title(); ?></h2></div>
				</div>

					
				<input type="hidden" id="<?php echo 'idea'.$nomicly_int;?>" name ="<?php echo 'idea'.$nomicly_int; ?>" value="<?php the_ID(); ?>" />
				<a href="" id="<?php the_ID(); ?>" class="widget-button vote-link">This idea is better</a>
				</article>					

				<?php 
				$nomicly_int++;
				endwhile;
				?>
				<input type="hidden" name="chosen_idea" id="chosen_idea" value="" />
				<input id="compare-ideas-submit" type="submit" name="vote" value="Vote" style="display:none" />		
				
					<div class="compare-results-box widget"></div>			
				</form>
				
		<?php 
		} else {
	    echo '<div class="widget"><h3><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to play</h3></div>';
			}
		?>				
			
			</div><!-- #content -->
		</div><!-- #primary -->
		
		<!--logged in description box-->
		
		 	<div id="secondary" class="widget-area" role="complementary">
		 		<div class="widget">
		 			<h3>Compare Ideas</h3>
		 			<p>Compare ideas is a game where you get to decide which idea is better.  See what other people think as well.  Sometimes the ideas will be related but most often they will be silly comparisons.</p>
		 		</div>
		 		<!--nav box-->
				<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
		 	</div>
		

		



<?php get_footer(); ?>