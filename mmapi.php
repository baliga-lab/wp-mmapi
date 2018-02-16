<?php
/**
 * @package mmapi
 * @version 1.01
 */
/*
Plugin Name: wp-mmapi
Plugin URI: https://github.com/baliga-lab/wp-mmapi
Description: A plugin that pulls in information from a mmapi service
Author: Wei-ju Wu
Version: 1.0
Author URI: http://www.systemsbiology.org
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**********************************************************************
 * Settings Section
 * Users provide and store information about the web service and
 * structure of their web site here
 **********************************************************************/

function mmapi_settings_init() {

    // This is the General section
    add_settings_section(
        "general_section",
        "MMAPI",
        "general_section_cb",
        'general'  // general, writing, reading, discussion, media, privacy, permalink
    );
    add_settings_field('source_url', 'Data Source URL', 'source_url_field_cb', 'general',
                       'general_section');
    add_settings_field('mmapi_slug', 'MMAPI Slug', 'slug_field_cb', 'general',
                       'general_section');

    register_setting('general', 'source_url');
    register_setting('general', 'mmapi_slug');
}

function general_section_cb()
{
    echo "<p>General settings for the MMAPI Plugin</p>";
}

function source_url_field_cb()
{
    $url = get_option('source_url', '');
    echo "<input type=\"text\" name=\"source_url\" value=\"" . $url . "\">";
}

function slug_field_cb()
{
    $slug = get_option('mmapi_slug', 'mmapi');
    echo "<input type=\"text\" name=\"mmapi_slug\" value=\"" . $slug . "\">";
}

/**********************************************************************
 * Plugin Section
 **********************************************************************/

require_once('short_codes.php');
require_once('ajax_source.php');

/*
 * Custom variables that are supposed to be used must be made
 * available explicitly through the filter mechanism.
 */
function add_query_vars_filter($vars) {
    $vars[] = "bicluster";
    $vars[] = "regulator";
    $vars[] = "mutation";
    $vars[] = "search_term";
    return $vars;
}

function mmapi_init()
{
    // add all javascript and style files that are used by our plugin
    wp_enqueue_style('datatables', plugin_dir_url(__FILE__) . 'css/jquery.dataTables.min.css');
    wp_enqueue_style('wp-mmapi', plugin_dir_url(__FILE__) . 'css/wp-mmapi.css');
    wp_enqueue_style('qtip', plugin_dir_url(__FILE__) . 'css/jquery.qtip.min.css', null, false, false);

    wp_enqueue_script('d3', plugin_dir_url(__FILE__) . 'js/d3.min.js', array('jquery'));
    wp_enqueue_script('datatables', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js', array('jquery'));
    wp_enqueue_script('qtip', plugin_dir_url(__FILE__) . 'js/jquery.qtip.min.js', array('jquery'), false, true);
    wp_enqueue_script('highcharts', plugin_dir_url(__FILE__) . 'js/highcharts.js', array('jquery'));

    mmapi_add_shortcodes();
    mmapi_datatables_source_init();
    add_filter('query_vars', 'add_query_vars_filter');
}

function search_mmapi()
{
    $search_term = $_POST['search_term'];
    error_log("search_mmapi(): " . $search_term);
    $page = get_page_by_path('search-results');
    wp_safe_redirect(get_permalink($page->ID) . "?search_term=" . $search_term);
    exit;
}

add_action('admin_init', 'mmapi_settings_init');
add_action('init', 'mmapi_init');
add_action('admin_post_nopriv_search_mmapi', 'search_mmapi');
add_action('admin_post_search_mmapi', 'search_mmapi');

?>
