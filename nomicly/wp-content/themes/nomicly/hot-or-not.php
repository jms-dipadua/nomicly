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
if (isset($_POST['vote'])) {
	$pair_id = nomicly_record_vote();
	$pair_stats = get_hot_not_stats($pair_id);
}
?>

		<div id="primary" class="showcase">
			<div id="content" role="main">
			<?php
				if (isset($_POST['vote'])) {
				echo "Statistics for the Last Pair:<br />";
				print_r ($pair_stats);
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
				<article id="<?php the_ID(); ?>">
					<header class="entry-header">
				<h2 class="entry-title"><?php the_title(); ?></h2>
					</header><!-- .entry-header -->
					
				<input type="hidden" name ="<?php echo "$nomicly_int"; ?>" value="<?php the_ID(); ?>" />
				<a href="" id="<?php the_ID(); ?>" class="vote-link">Vote</a>
				</article>					

				<?php 
				$nomicly_int++;
				endwhile;
				?>
				<input type="hidden" name="chosen_idea" id="chosen_idea" value="" />
				<input id="compare-ideas-submit" type="submit" name="vote" value="Vote" style="display:none" />					
				</form>
		<?php 
		} else {
	    echo '<h1><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to Create New Ideas</h1>';
			}
		?>				
			
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>