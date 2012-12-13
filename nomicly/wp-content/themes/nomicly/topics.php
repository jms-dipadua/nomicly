<?php
/**
 * Template Name: Topics Template
 * Description: A Page Template for listing all categories and topics in Nomicly
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

// process topic posting 
 if (isset($_POST['create_topic'])) {
	create_new_topic();
	}

?>

<!--logged in sidebar-->
<?php if ( is_user_logged_in() ) { ?>
<div id="secondary" class="widget-area" role="complementary">	
		<div class="widget">
			<form method ="post" action ="#">
			<!-- should make this a drop down to select 'idea' or 'topic' -->
			<h3>Create A New Topic</h3><br />
			<p>Topic Name: <input type="text" name="new_topic_name" value="" /> </p>
			Topic Description: 
			<textarea rows="2" cols="20" name="new_topic" value="">
			</textarea>
			<input type="submit" name="create_topic" value="Add Topic" class="widget-button" />
			</form>  
		</div>  
	</div>
<?php } ?>
	
	
		<div id="primary" class="showcase">
			<div id="content" role="main">

			<div class="widget topic-list">
				<ul>
						<?php
							$args=array(
							  'orderby' => 'name',
							  'order' => 'ASC',
							  'hide_empty' => 0,
							  'exclude' => 1
							);
							$categories=get_categories($args);
							  foreach($categories as $category) { 
								echo '<li><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </li> '; }
								//echo '<p> Description:'. $category->description . '</p>';  
							?>
								</ul>
							</div>
						</div><!-- #content -->
					</div><!-- #primary -->

	<!--non logged in sidebar-->
	<?php if ( !is_user_logged_in() ) { ?>		
		<div id="secondary" class="widget-area" role="complementary">
			<div class="widget">	
			<?php	$wpurl = get_bloginfo ( 'wpurl' );  
				echo '<b><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Create Topics</b>'; ?>
			</div>
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
		</div><!--end secondary-->	
	<?php } ?>

	<!-- logged in navbox-->
	   <?php if ( is_user_logged_in() ) { ?>		
			<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>

<?php get_footer(); ?>