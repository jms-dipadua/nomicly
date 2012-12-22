<?php
/**
 * Template Name: Profile Template
 * Description: A page for showing user information
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// Enqueue scripts
get_header(); 

// process new idea posting 
 if (isset($_POST['create'])) {
	nomicly_new_idea();
	}

// process topic posting 
 if (isset($_POST['create_topic'])) {
	create_new_topic();
	}

?>



<?php if ( is_user_logged_in() ) { ?>
	<div class="full-width">
		<!--profile box-->
				<div class="profile-sidebar-box media" style="padding: 2%">
				<div class="img">
					<?php global $current_user;
						  get_currentuserinfo();
						  
						  echo get_avatar( $current_user->user_email, $size = '100' );?>
					</div>
					<div class="bd">	  
						<?php  echo '<h3 class="entry-title">' . $current_user->user_login . '</h3>'; ?>
						<p class="sidebar-stats-ideas"><b>Ideas:</b> <span><?php $user_id = get_current_user_id(); $post_count = count_user_posts($user_id); echo "$post_count";?></span></p>
						<p class="sidebar-stats-topics"><b>Topics:</b> <span><?php echo count_user_topics($user_id);?></span></p>
						<p class="sidebar-stats-votes"><b>Votes Available:</b><span></span></p>
						<!--<p><b>Reputation:</b> awesome</p>-->
					</div>
				</div>
	
	</div>		
	
<?php } ?>		


<!--logged in sidebar-->

			
	<div id="secondary" class="widget-area" role="complementary">	
		<!--new idea box-->	
		<?php
			// check for login to present idea form or reg/login links
			if ( is_user_logged_in() ) { ?>
			
				<div class="widget new-idea-sidebox">	
					<?php $user_id = get_current_user_id(); ?>
						<form method ="post" action ="#">
						<h3>Create A New Idea</h3>
						<textarea rows="2" cols="20" name="new_idea" id="new_idea" value=""></textarea>
						<span class="topic-select">Topic:<?php wp_dropdown_categories(); ?></span>
						<input type="hidden" name="user_id" id="user_id" value="<?php echo "$user_id"; ?>" />
						<input type="hidden" name="category_id" id="category_id" value="" />
						<div><input type="submit" name="create" class="idea_submit_button" value="Create" /></div>
					</form>    
				</div>
			<!--new topic box -->
			<div class="widget new-idea-sidebox">	
				<form method ="post" action ="#">
					<!-- should make this a drop down to select 'idea' or 'topic' -->
					<h3>Create A New Topic</h3>
					<input type="hidden" name="new_topic_name" value="" id="new-topic-name"/> 
					Topic Description: 		
					<textarea rows="2" cols="20" name="new_topic" value="" id="new-topic-description"></textarea>
					<!-- maybe include a dropdown of all the topics too? -->
					<input type="submit" name="create_topic" value="New Topic" class="widget-button" id="new-topic-submit" />
				</form> 
			</div>
			<!--my topics box-->
			<!-- 
			<div class="widget">
				<?php
				/*
					// BUG AS OF V0.1:
						// -- RETURNS ALL TOPICS RATHER THAN USER SPECIFIC
						// -- REMOVING FOR NOW
					echo "<h3>My Topics</h3>";
					get_user_topics($user_id);
					*/
				?>
			</div> -->
			<!--end topic box-->
			<?php	} else {
	    		echo '<div class="widget"><h3><a href="../wp-login.php?action=register">Register</a> or <a href="../wp-login.php">Login</a> to Create New Ideas</h3></div>';
			}
			?>
	</div>
<!--end logged in sidebar-->


		<div id="primary">
			<div id="content" role="main" class="user_feed">
			
			<?php 
			/* GUTS OF USER PROFILE PAGE
			// verify user is logged in
			// then get ideas and topics from that person
			// note that voting and sharing come from the_content();
			// if we don't have anything from this person, we prompt them for ideas
			*/
			if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			// only get posts from this author and exclude the user profile page
			$query_args = array(
					'author' => $user_id,
					'exclude' => 59,
					'paged' => get_query_var('paged')
					);
				query_posts( $query_args ); 
				global $wpdb;
					echo "<h2 class='user-hd entry-title'>My Ideas</h2><div id='fresh-idea'></div><div id='idea-holder'></div>";	
							
				 while ( have_posts() ) : the_post();  ?>
				<article class="hentry media post" id="post-<?php the_ID(); ?>">
						<div class="img idea-stats">
							<p class="idea-stats-positive" id="positive_votes_<?php the_ID(); ?>"><b>Positive votes:</b><span></span></p>
							<p class="idea-stats-negative" id="negative_votes_<?php the_ID(); ?>"><b>Negative votes:</b><span></span></p>
							<!--<p><b>Consensus:</b> awesome</p>
							<p><b>Influenced ideas:</b> 2</p>
							<p><b>Shares:</b> 5</p>-->
						</div>
						
						<span class="pub-date"><?php the_time('m/d/y'); ?></span>
						<div class="bd"><h3 class="entry-title"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h3>
							<?php the_content(); ?>
						</div>
						<!-- ADDS MODIFY IDEA TO THE CONTENT -->
							<? if (is_user_logged_in()) { ?>
				 			<?php $wpurl = get_bloginfo ( 'wpurl' );  ?>
							<a href="<?php echo "$wpurl";?>/modify/?idea=<?php the_ID(); ?>" class="modify-link widget-button">Modify Idea</a>
					<?php }//END IF LOGGED IN  ?>
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
									<div class="ancestry-toggle-div">
										<p class="ancestry-links" id="ancestry-links_<?php the_ID(); ?>">
											<span class="ancestry-label"></span><a class="ancestry-link"></a>
										</p>
									</div>
						</footer>
				</article>
				
			<?php endwhile;	?>			
			<?php twentyeleven_content_nav( 'nav-below' ); ?>
				<?php
				if (!have_posts()) {
				 		echo "<p style='padding:10px'>Looks like you haven't created any ideas! <br /> 
				 		  That makes us a Sad Panda. :( </p>";
				 		} // END NO POSTS FOR USER	
					?>
					
			<?php 	// END IF USER_LOGGED_IN()
				}   ?>
			
			</div><!-- #content -->
		</div><!-- #primary -->
		
		<!--logged in navbox-->
		<?php if ( is_user_logged_in() ) { ?>		
			<div class="secondary widget-area" role="complementary">	
			<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) ?><!--sidebar nav in showcase widget area-->
			</div><!--end secondary-->	
		<?php } ?>

<?php get_footer(); ?>