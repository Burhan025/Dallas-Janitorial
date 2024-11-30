<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles', 1000 );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'icomoon-fonts', get_stylesheet_directory_uri() . '/icomoon.css', array() );
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/style.css', array() );
	wp_enqueue_style( 'djs-jotforms', get_stylesheet_directory_uri() . '/css/djs-jotforms.css', array() );
	
}

// Removes Query Strings from scripts and styles
function remove_script_version( $src ){
  if ( strpos( $src, 'uploads/bb-plugin' ) !== false || strpos( $src, 'uploads/bb-theme' ) !== false ) {
    return $src;
  }
  else {
    $parts = explode( '?ver', $src );
    return $parts[0];
  }
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );

// Add Additional Image Sizes
add_image_size( 'news-thumb', 260, 150, false );
add_image_size( 'news-full', 800, 300, false );
add_image_size( 'sidebar-thumb', 200, 150, false );
add_image_size( 'blog-thumb', 388, 288, true );
add_image_size( 'mailchimp', 564, 9999, false );
add_image_size( 'amp', 600, 9999, false );
add_image_size( 'home-news', 340, 187, true );
add_image_size( 'subpage-header', 1440, 330, true );

// Gravity Forms confirmation anchor on all forms
add_filter( 'gform_confirmation_anchor', '__return_true' );

//Sets the number of revisions for all post types
add_filter( 'wp_revisions_to_keep', 'revisions_count', 10, 2 );
function revisions_count( $num, $post ) {
	$num = 3;
    return $num;
}

// Enable Featured Images in RSS Feed and apply Custom image size so it doesn't generate large images in emails
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'mailchimp', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
}
 
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

//Show custom widget in navigation

// Register sidebar/ widget
register_sidebar( array(
'id' => 'mobile-nav-widget',
'name' => __( 'Mobile Nav Widget', 'dallas-janitorial' ),
'description' => __( 'Custom Widget Area', 'childtheme' ),
'before_widget' => '<li class="mob-html">',
'after_widget' => '</li>',
) );

// add sidebar to menu
add_filter( 'wp_nav_menu_items','add_phone_mobile_menu', 10, 2 );
function add_phone_mobile_menu( $items, $args ) {

//	print_r($args->menu );

//	if ($args->menu == 'main-menu') {
		ob_start();
		dynamic_sidebar('mobile-nav-widget');
		$sidebar = ob_get_contents();
		ob_end_clean();
		$items .= $sidebar;
//	}
	return $items;

}

//****** AMP Customizations ******/

// Add Fav Icon to AMP Pages
add_action('amp_post_template_head','amp_favicon');
function amp_favicon() { ?>
	<link rel="icon" href="<?php echo get_site_icon_url(); ?>" />
<?php } 

// Add Banner below content of AMP Pages
add_action('ampforwp_after_post_content','amp_custom_banner_extension_insert_banner');
function amp_custom_banner_extension_insert_banner() { ?>
	<div class="amp-custom-banner-after-post">
		<h2>IF YOU HAVE ANY QUESTIONS, PLEASE DO NOT HESITATE TO CONTACT US</h2>
		<a class="ampforwp-comment-button" href="/contact-us">
			CONTACT US
		</a>
	</div>
<?php }

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'font-awesome' ); // FontAwesome 4
    wp_enqueue_style( 'font-awesome-5' ); // FontAwesome 5

    wp_dequeue_style( 'jquery-magnificpopup' );
    wp_dequeue_script( 'jquery-magnificpopup' );

    wp_dequeue_script( 'bootstrap' );
    //wp_dequeue_script( 'imagesloaded' );
    wp_dequeue_script( 'jquery-fitvids' );
    //wp_dequeue_script( 'jquery-throttle' );
    wp_dequeue_script( 'jquery-waypoints' );
}, 9999 );

/* Site Optimization - Removing several assets from Home page that we dont need */

// Remove Assets from HOME page only
function remove_home_assets() {
  if (is_front_page()) {
      
	  wp_dequeue_style('yoast-seo-adminbar');
	  wp_dequeue_style('addtoany');
	  wp_dequeue_style('font-awesome');
	  wp_dequeue_style('wpautoterms_css');
	  
	  wp_dequeue_script('wpautoterms_base');
	  wp_dequeue_script('addtoany-core');
	  wp_dequeue_script('addtoany-jquery');
	  
	  
  }
  
};
add_action( 'wp_enqueue_scripts', 'remove_home_assets', 9999 );


//Removing unused Default Wordpress Emoji Script - Performance Enhancer
function disable_emoji_dequeue_script() {
    wp_dequeue_script( 'emoji' );
}
add_action( 'wp_print_scripts', 'disable_emoji_dequeue_script', 100 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Removes Emoji Scripts 
add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_header', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}