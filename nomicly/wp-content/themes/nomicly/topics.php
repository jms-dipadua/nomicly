<?php
/**
 * Template Name: Topics Template
 * Description: A Page Template for list of categories
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
/* if (isset($_POST['new_topic']) {
	nomicly_new_topic();
	}
*/
?>


		<div id="primary" class="showcase">
			<div id="content" role="main">
<?php
if ( is_user_logged_in() ) {
    //CREATE FORM LOGIC HERE
} else {
    echo '<h1><a href="wp-login.php?action=register">Register</a> or <a href="wp-login.php">Login</a> to Create New Ideas</h1>';
}
?>
				<?php
				$args=array(
				  'orderby' => 'name',
				  'order' => 'ASC'
				  );
				$categories=get_categories($args);
				  foreach($categories as $category) { 
					echo '<p>Topic: <a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </p> '; }
					//echo '<p> Description:'. $category->description . '</p>';  
				?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>