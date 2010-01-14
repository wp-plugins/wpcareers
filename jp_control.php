<?php

/**
Description: Wordpress wpCareers
Plugin Name: wpcareers
Plugin URI: http://www.forgani.com/root/wordpress-careers-plugin/
Description: wpCareers wordpress plugin
Author: Mohammad forgani
Version: 1.0-a
Author URI: http://www.forgani.com

Copyright 2010 Mohammad Forgani 

Demo:  http://www.wpcareers.com

*/

/**
Changes:

Jan 09, 2010
    - Released v1.0
    - All administrator pages finished.

*/ 



global $table_prefix, $wpdb;
if (!$table_prefix){$table_prefix = $wpdb->prefix;}



/**
 * Constants
 */
define('VERSION', '1.0-a');

define('JP_PLUGIN_DIR', ABSPATH .  'wp-content/plugins/wpcareers');
define('JP_PLUGIN_URL', plugins_url('wpcareers'));


/**
 * @brief Autoload files.
 */
require_once(dirname(__FILE__) . '/include/jp_functions.php');
require_once(dirname(__FILE__) . '/include/jp_captcha.php');
require_once(dirname(__FILE__) . '/include/jp_GADlink.php');
require_once(dirname(__FILE__) . '/admin/jp_admin_posts.php');
require_once(dirname(__FILE__) . '/admin/jp_admin.php');
require_once(dirname(__FILE__) . '/admin/jp_admin_setup.php');
require_once(dirname(__FILE__) . '/admin/jp_admin_structure.php');
require_once(dirname(__FILE__) . '/admin/jp_admin_utilities.php');
require_once(dirname(__FILE__) . '/jp_post_job.php');
require_once(dirname(__FILE__) . '/jp_post_resume.php');
require_once(dirname(__FILE__) . '/jp_list_job.php');
require_once(dirname(__FILE__) . '/jp_list_resume.php');
require_once(dirname(__FILE__) . '/jp_list_category.php');
require_once(dirname(__FILE__) . '/jp_list_resume.php');
require_once(dirname(__FILE__) . '/jp_search.php');
require_once(dirname(__FILE__) . '/jp_main.php');
require_once(dirname(__FILE__) . '/jp_view_job.php');
require_once(dirname(__FILE__) . '/jp_view_resume.php');

/**
 * get_language() - Get HTTP header accept languages
*/
$locale = get_locale();
list ($lng, $loc) = explode('_', $locale);
$languageFile = JP_PLUGIN_DIR . '/language/lang_'. $lng . '.php';
if (file_exists($languageFile)) {
   require_once($languageFile);
} else {
   require_once(JP_PLUGIN_DIR . '/language/lang_en.php');
}



/**
 * Initialize the plugin
*/

add_action('plugins_loaded', create_function('$a', 'global $wpcareers; $wpcareers = new WP_careers();'));
add_filter('the_content', 'wpcareers_page_handle_content');
add_filter('the_title', 'wpcareers_page_handle_title');
add_filter('wp_list_pages', 'wpcareers_page_handle_titlechange');
add_filter('single_post_title', 'wpcareers_page_handle_pagetitle');
add_filter('query_vars', 'wpcareers_query_vars');


/**
 * Assigns each respective variable.
 */
date_default_timezone_set('UTC'); // php5.1
if (!isset($_GET)) $_GET = $HTTP_GET_VARS;
if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
if (!isset($_SERVER)) $_SERVER = $HTTP_SERVER_VARS;
if (!isset($_COOKIE)) $_COOKIE = $HTTP_COOKIE_VARS;

$wpca_settings = get_option('wpcareers');
if (isset($_REQUEST["wpcareers_action"])){
   $_SERVER["REQUEST_URI"] = dirname(dirname($_SERVER["PHP_SELF"]))."/".$wpca_settings['slug']."/";
   $_SERVER["REQUEST_URI"] = stripslashes($_SERVER["REQUEST_URI"]);
}

?>