<?php
/*
*	Plugin Name: Priority for SearchWP
*	Plugin URI:  http://wordpress.org/plugins/ht-voting-sort
*	Description: Plugin allows to define important articles that should be listed first in search results by certain search keywords.
*	Author: Sergiy Dzysyak
*	Version: 0.1
*	Author URI: http://erlycoder.com/
*	Text Domain: priority-for-searchwp
*/

defined( 'ABSPATH' ) || exit;

if( !class_exists('Priority_for_SearchWP') ){
	class Priority_for_SearchWP {
	    /**
	     * Plugin constructor. Registers actions and hooks.
	     */
		function __construct(){
			add_action( 'init', array($this, 'init'));
			
			register_activation_hook( __FILE__, [$this, 'plugin_install']);
			
			if(is_admin()){
				add_action('add_meta_boxes', array( $this, 'search_wp_sort_box'));
				add_action('save_post', array( $this, 'search_wp_sort_save_postdata'));
			}
			
			add_filter( 'searchwp_query_orderby', array( $this, 'searchwp_query_orderby'), 10, 2 );
		}
		
		/**
		 * Method modifies ORDER BY part of the query to place posts with predefined keywords to be listed first.
		 * If several articles have the same keywords defined, than such articles are ordered by relevance. 
		 * 
		 * @param unknown $sql Original query
		 * @param unknown $engine Search engine 
		 * @return string Function returns ORDER BY part of the SQL request.
		 */
		function searchwp_query_orderby( $sql, $engine ){
			global $wpdb;
			
			$sql = "SELECT post_id AS ID FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'search_wp_keywords' AND meta_value LIKE %s";
			$sql = $wpdb->prepare($sql, $_REQUEST['s']);
			$rows = $wpdb->get_results($sql);
			
			$ids = array();
			foreach ( $rows as $row ){
			   $ids[] = $row->ID;
			}
			
			if(!empty($ids)){
				return "ORDER BY {$wpdb->prefix}posts.ID IN (".implode(", ", $ids).") DESC, finalweight DESC";
			}else{
				return "ORDER BY finalweight DESC";
			}
		}
		
		/**
		 * Define metabox for the admin interface.
		 */
		function search_wp_sort_box(){
		    // By default enabling metabox for all types of posts. Byt one can define an array of types.  
			$screens = get_post_types();
			
			// Check for Gutenberg function
			if ( !function_exists( 'register_block_type' ) )
			foreach ($screens as $screen) {
				add_meta_box(
				    'search_wp_sort_box_id',     // Unique ID
				    'Priority for SearchWP',  // Box title
				    array($this, 'search_wp_sort_box_html'),  // Content callback, must be of type callable
				    $screen,                   // Post type
				    'normal', 
				    'high',
				    array(
						'__back_compat_meta_box' => false,
					)
				);	
			}
		}
		
		/**
		 * Metabox html code.
		 */
		function search_wp_sort_box_html(){
			$keys = get_post_custom_values('search_wp_keywords', $_REQUEST['post']);
		
			?>
    <label for="search_wp_keywords">Search keywords CSV</label>
    <textarea name="search_wp_keywords" id="search_wp_keywords" style="width: 100%;" placeholder="Example: apple, orange color, fruits and veggies"><?php echo implode(", ", $keys); ?></textarea>
        
    <?php
		}
		
		/**
		 * Process save_post event and save metabox data to post meta value.
		 * 
		 * @param unknown $post_id
		 */
		function search_wp_sort_save_postdata($post_id){
		    if (array_key_exists('search_wp_keywords', $_POST)) {
				delete_post_meta($post_id, 'search_wp_keywords');
			
			
    			$keys = explode(',', $_POST['search_wp_keywords']);
    			
    			foreach($keys as $key) if(!empty(trim($key))){
    				add_post_meta(
    					$post_id,
    					'search_wp_keywords',
    					trim($key),
    					false
    				);
    			}
			}
		}
		
		/**
		 * Load translations, scripts and register blocks.
		 */
		public function init(){
			if ( function_exists( 'load_plugin_textdomain' ) ) load_plugin_textdomain( 'priority-for-searchwp', false, basename( __DIR__ ) . '/languages' );
			if ( function_exists( 'register_post_meta' ) ) register_post_meta('post', 'search_wp_keywords', array('type' => 'string', 'single'	=> false, 'show_in_rest' => true));
			if ( function_exists( 'wp_register_script' ) ) wp_register_script('priority-for-searchwp',	plugins_url( '/js/plugin.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-edit-post', 'wp-data', 'wp-editor' ), filemtime( plugin_dir_path( __FILE__ ) . '/js/plugin.js' ));

			if ( function_exists( 'register_block_type' ) ) register_block_type( 'priority-for-searchwp/priority-for-searchwp', array('editor_script' => 'priority-for-searchwp',) );
			if ( function_exists( 'wp_set_script_translations' ) ) wp_set_script_translations( 'priority-for-searchwp', 'priority-for-searchwp', plugin_dir_path( __FILE__ ) . 'languages' );
		}
		
		/**
		 * Plugin install routines. Check for dependencies.
		 * 
		 * This plugin requires SearchWP plugin.
		 */
		public function plugin_install() {
		/*
			if ( ! is_plugin_active( 'searchwp/searchwp.php' ) and current_user_can( 'activate_plugins' ) ) {
				// Stop activation redirect and show error
				wp_die('Sorry, but this plugin requires the Search WP Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
			}
			*/
		}
	}
	
	$priority_for_searchwp_init = new Priority_for_SearchWP();
}
		
?>
