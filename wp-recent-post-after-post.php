<?php
/*
Plugin Name: WP Post
Plugin URI: https://diegosanchez.info/wp-post-plugin/
Description: A WordPress plugin which can be enabled / disabled via the Plugins admin page & includes full details for identification.
Version: 1.0.0
Author: Diego Sanchez
Author URI: https://diegosanchez.info
License: GPLv2 
Text Domain: wprecentpostap
*/

// Doesn't loads directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_filter( 'the_content', 'wp_display_recent_post', 20 );

/**
 * Handles to add recent post after post
 * @package WP - Recent Post After Post
 * @since 1.0.0
*/

function wp_display_recent_post( $content ) {
	global $post;
    if ( is_single() && isset( $post ) && $post->post_type == 'post' ) {
    	ob_start();
    	$args = array(
			'numberposts' => 1,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'suppress_filters' => true
		);

    	$recent_posts = wp_get_recent_posts( $args, ARRAY_A ); ?>		
    	<div class="recent-post-wrapper">
    		<div class="image-wrapper">    		
			<?php if (has_post_thumbnail( $recent_posts[0]['ID'] ) ) {
			  	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $recent_posts[0]['ID'] ), 'thumbnail' ); ?>
  				<img src="<?php echo $image[0]; ?>" id="featured-image"/>
			<?php } ?>
			</div>
			<div class="content-wrapper">
				<?php
					$added_category = '';
					$categories = get_the_category( $recent_posts[0]['ID'] );
					if(!empty( $categories ) ) {
						foreach ( $categories as $category ) {
							if( $category == end( $categories ) ) {
								$added_category .= $category->name;	
							} else {
								$added_category .= $category->name.',';
							}
						}
					}					
				?>
				<div class="category"><?php echo $added_category; ?></div><span class="mobile-time"><?php echo '| '. human_time_diff(  mysql2date('U', $recent_posts[0]['post_date'], false ), current_time('timestamp') ) . ' ago'; ?></span>
				<div class="post-title"><?php echo $recent_posts[0]['post_title']; ?></div>
				<div class="post-information">
					<?php 
					$post_author = $recent_posts[0]['post_author'];
					$user_data = get_userdata( $post_author ); ?>
					<span class="author"><span class="by">By </span><span class="name"><?php echo $user_data->data->display_name; ?></span></span>
					<span class="time"><?php echo human_time_diff(  mysql2date('U', $recent_posts[0]['post_date'], false ), current_time('timestamp') ) . ' ago'; ?></span>
				</div>
			</div>
		</div>
    	<?php
    	$html = ob_get_clean();
    	$content = $content . $html;	
    }            

    //Returns content.
    return $content;
}

//Checks code front side.
add_action( 'wp_enqueue_scripts', 'wp_recent_include_css' );
function wp_recent_include_css() {
	global $post;
    if ( is_single() && isset( $post ) && $post->post_type == 'post' ) {
    	// add css for check code in public
		wp_register_style( 'wp-recent-post-style', plugin_dir_url( __FILE__ ) . 'css/wp-recent-post-style.css', array(), '1.0.0' );
		wp_enqueue_style( 'wp-recent-post-style' );
    }
}
