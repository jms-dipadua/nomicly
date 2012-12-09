<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 */

get_header(); 

// process new idea posting 
/* if (isset($_POST['create'])) {
	nomicly_new_idea();
	}
	*/
?>

		<div id="secondary" class="widget-area" role="complementary">		
		<?php if ( is_user_logged_in() ) { ?>
		
			<div class="profile-sidebar-box">
				<?php global $current_user;
					  get_currentuserinfo();
				
					  echo '<h3>' . $current_user->user_login . '</h3>';
					  
   					  echo get_avatar( $current_user->user_email, $size = '42' ); 

				?>
			</div>
			
			<?php $user_id = get_current_user_id(); ?>
				<form method ="post" action ="#">
				<h3>Create A New Idea</h3>
				<textarea rows="2" cols="20" name="new_idea" id="new_idea" value=""></textarea>
				<input type="hidden" name="user_id" id="user_id" value="<?php echo "$user_id"; ?>" />
				<input type="submit" name="create" class="idea_submit_button" value="Create" />
			</form>    
			
		<?php } 
			else { ?>	
			<h3>The Short Summary</h3>
			<p>Nomicly is a place to create ideas that solve real world problems and to build consensus around the best ideas.<p>
			
			<h3>Create Ideas</h3>
			<p>You can create an idea about anything you'd like.</p>
			
			<p>If you see an idea from someone else that triggers an idea of your own, you can modify the original idea. </p>
			
			<p>To create an idea to address a specific topic or solve a problem, first go to the Topics page where all ideas are organized by Topic. </p>
			
			<h3>Build Consensus</h3>
			<p>Nomicly helps identify good ideas (and the bad ones) with your help.</p>
			
			<p>For each idea, you can either Thumbs Up or Thumbs Down it, helping identify the best ideas and solutions to the toughest problems. </p>
			
			<h3>Want to make Nomicly better?</h3>
			
			<p>Please suggest improvements for Nomicly here. It's Nomicily for Nomicly.</p>
			
		<?php	$wpurl = get_bloginfo ( 'wpurl' );  
		    echo '<p><b><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Participate</b></p>'; ?>
			
			
			<?php } ?>
		</div>

		<div id="primary">
			<div id="content" role="main">

	<?php if ( have_posts() ) : ?>

				<?php twentyeleven_content_nav( 'nav-above' ); ?>
		<?php
	// check for login to present idea form or reg/login links
	if ( !is_user_logged_in() ) { 
			$wpurl = get_bloginfo ( 'wpurl' );  
		    echo '<h1><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Create New Ideas</h1>';
			}
		?>		
		
				<?php /* Start the Loop */ ?>
		<div id="the_feed">
		<div id="fresh-idea"></div>
				<?php while ( have_posts() ) : the_post(); ?>
				
				<?php get_template_part( 'content', get_post_format() ); ?>
			

				<?php endwhile; ?>

				<?php twentyeleven_content_nav( 'nav-below' ); ?>

			<?php else : ?>
		</div>

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
		</div><!-- #primary -->
		


<!--<?php //get_sidebar(); ?>-->
<?php get_footer(); ?>