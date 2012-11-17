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


		<div id="primary" class="showcase">
			<div id="content" role="main">
<?php
	if ( is_user_logged_in() ) { ?>
		<form method ="post" action ="#">
		<!-- should make this a drop down to select 'idea' or 'topic' -->
		<h2>Create A New Topic</h2>
		Topic Name: <input type="text" name="new_topic_name" value="" /> <br />
		Topic Description: 
		<textarea rows="2" cols="20" name="new_topic" value="">
		</textarea>
		<input type="hidden" name="cateogry_id" value="<?php get_the_category($post_id); ?>" />
		<!-- maybe include a dropdown of all the topics too? -->
		<input type="submit" name="create_topic" value="New Topic" />
		</form>    
		<?php	} else {
			$wpurl = get_bloginfo ( 'wpurl' );  
		    echo '<h1><a href="'.$wpurl.'/wp-login.php?action=register">Register</a> or <a href="'.$wpurl.'/wp-login.php">Login</a> to Create New Ideas</h1>';
			}
?>
			<?php
				$args=array(
				  'orderby' => 'name',
				  'order' => 'ASC',
 			  	  'hide_empty' => 0,
 			  	  'exclude' => 1
 			  	);
				$categories=get_categories($args);
				  foreach($categories as $category) { 
					echo '<p>Topic: <a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View %s" ), $category->name ) . '" ' . '>' . $category->description.'</a> </p> '; }
					//echo '<p> Description:'. $category->description . '</p>';  
				?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>