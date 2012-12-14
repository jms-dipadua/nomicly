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
						
						<p><b>Ideas:</b> 
						<?php 	$user_id = get_current_user_id();
						$post_count = count_user_posts($user_id);
						echo "$post_count"; ?>
						</p>
						<p><b>Topics:</b> 30</p>
					</div>
				</div>
			<!--new idea box-->	
				<div class="widget new-idea-sidebox">	
					<?php $user_id = get_current_user_id(); ?>
						<form method ="post" action ="#">
						<h3>Create A New Idea</h3>
						<textarea rows="2" cols="20" name="new_idea" id="new_idea" value=""></textarea>
						<input type="hidden" name="user_id" id="user_id" value="<?php echo "$user_id"; ?>" />
						<div><input type="submit" name="create" class="idea_submit_button" value="Create" /></div>
					</form>    
				</div>
			<!--recent ideas box-->
				<div class="widget recent-ideas-sidebox">	
					<h3>My Recent Ideas</h3>
					<ul>
					<?php
					  $number_recents_posts = 5;//Enter number of recent of posts you want to display
					 $args=array('numberposts' => $number_recents_posts,'post_status'=>'publish');
				
					  $recent_posts = wp_get_recent_posts( $args );
					  foreach($recent_posts as $post){
						echo '<li><a href="' . get_permalink($post["ID"]) . '" title="Look '.$post["post_title"].'" >' .   $post["post_title"].'</a> </li> ';
					  } ?>
					</ul>
					<p><a href="<?php bloginfo( 'wpurl' ); ?>/user-profile/" class="widget-button">View all</a></p>
				</div>

			</div><!--end secondary-->
		<?php } ?>	
	<!--end logged in sidebar-->


<!-- primary content-->
		<div id="primary">
			<div id="content" role="main" class="the_feed">

	<?php if ( have_posts() ) : ?>

				<?php /* Start the Loop */ ?>
	
		<div id="fresh-idea"></div>
				<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
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
		</div><!-- #primary -->
	
	<!--non loggedin sidebar-->	
		<?php if ( !is_user_logged_in() ) { ?>		
		<div id="secondary" class="widget-area" role="complementary">	
			<div class="widget">	
				<h3>The Short Summary</h3>
				<p>Nomicly is a place to create ideas that solve real world problems and to build consensus around the best ideas.<p>
			</div>
			<div class="widget">	
				<h3>Create Ideas</h3>
				<p>You can create an idea about anything you'd like.</p>
				
				<p>If you see an idea from someone else that triggers an idea of your own, you can modify the original idea. </p>
				
				<p>To create an idea to address a specific topic or solve a problem, first go to the Topics page where all ideas are organized by Topic. </p>
			</div>
				
			<div class="widget">	
				<h3>Build Consensus</h3>
				<p>Nomicly helps identify good ideas (and the bad ones) with your help.</p>
				
				<p>For each idea, you can either Thumbs Up or Thumbs Down it, helping identify the best ideas and solutions to the toughest problems. </p>
			</div>
			<div class="widget">	
				<h3>Want to make Nomicly better?</h3>
				
				<p>Please suggest improvements for Nomicly here. It's Nomicily for Nomicly.</p>
			</div>
			<div class="widget">	
			<?php	$wpurl = get_bloginfo ( 'wpurl' );  
				echo '<b><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Participate</b>'; ?>
			</div>
				<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
	
			</div><!--end secondary-->	
		<?php } ?>

		<!--logged in navbox-->
		<?php if ( is_user_logged_in() ) { ?>		
			<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>


<!--<?php //get_sidebar(); ?>-->
<?php get_footer(); ?>