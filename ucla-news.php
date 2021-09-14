<?php 
/**
 * Plugin Name: UCLA News
 * Description: Plugin to handle the news component of the website
 * Author: Yasmine Khadija Talby
 * Version: 0.1.0
 */

// Prevent public user to directly access this file
if (!defined('ABSPATH')) {
	exit;
}

//set the constant NEWS_DOMAIN as ucla-news
define( 'NEWS_DOMAIN', 'ucla-news');

function ucla_news(){

	//set UI labels for CPT
	$labels= array(
		'name'=>__('News','NEWS_DOMAIN'),
		'singular_name'=>__('New','NEWS_DOMAIN'),
		'add_new'=>__('Add New','NEWS_DOMAIN'),
		'edit_item'=>__('Edit New','NEWS_DOMAIN'),
		'view_item'=>__('View New','NEWS_DOMAIN'),
		'view_items'=>__('View News','NEWS_DOMAIN'),
		'search_items'=>__('Search News','NEWS_DOMAIN'),
		'not_found'=>__('No News found.','NEWS_DOMAIN'),
		'not_found_in_trash'=>__('No News found in trash.','NEWS_DOMAIN'),
		'all_items'=>__('All News','NEWS_DOMAIN'),
		'menu_name'=>__('News','NEWS_DOMAIN'),
	);

	//set options for CPT
	$args = array(
		'description'=>__('UCLA Departemental News','NEWS_DOMAIN'),
		'labels'=>$labels,
		'supports'=> array('title', 'editor', 'author','custom-fields', 'thumbnail'), //define core features the post type supports
		'taxonomies'=> array('news-type'), //taxonomy identifiers that will be registered for the post type
		'hierarchical'=>false,
		'public'=> true ,//allow us to publish
		'show_in_rest' => false,//block editor?
		'has_archive'=> true,
		'menu_icon' => 'dashicons-admin-site' //set the menu icon
	);
	//register publication CPT
	register_post_type('news', $args);

}

//Hook into the 'init' action
add_action('init','ucla_news',0); 

/*CUSTOM FIELDS*/
function add_post_meta_boxes_news(){
	 add_meta_box("metadata-news", "News Details", "metabox_news", "news", "side", "low");
}
add_action('admin_init','add_post_meta_boxes_news');

function save_details_news(){
	global $post;
	if (defined('DOING_AUTOSAVE')&& DOING_AUTOSAVE){return;}
	update_post_meta($post->ID, "summary_news",sanitize_text_field($_POST["summary_news"]));
}
add_action('save_post','save_details_news');

function metabox_news() {
        global $post;
	$custom = get_post_custom($post->ID);//retrieve post meta fields based on post ID
	$field_data_summary_news = $custom["summary_news"][0]; //grab data from "summary_news"
	echo "<input type=\"text\" name=\"summary_news\" value=\"".$field_data_summary_news."\" placeholder=\"Descriptive Summary\">";}

/*TAXONOMIES*/

add_action('init', 'news_taxonomy',0);

function news_taxonomy(){
	//Labels for the GUI
	
	$labels = array (
		'name'=>__('News Type', 'NEWS_DOMAIN'),
		'singular_name' =>__('News Type', 'NEWS_DOMAIN'),
		'search_items' =>  __( 'Search News Types', 'NEWS_DOMAIN'),
	);
	register_taxonomy('news_type','news',array(
		'hierarchical' => true, //category taxonomy
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'news-type'),
    ));
}
/*TEMPLATE*/
  
function news_template($template){
	global $post;
	if ('news' === $post->post_type){
		return plugin_dir_path( __FILE__ ) . 'single-news.php';
	}
	return $template;
}
add_filter('single_template','news_template');
