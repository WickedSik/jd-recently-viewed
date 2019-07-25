<?php

/**
 * Plugin Name: JD's Recently Viewed Pages
 * Description: Shows the recently viewed pages in a convenient widget, no style included!
 * Version: 1.0
 * Requires PHP: 5.6
 * Author: Jurriën Dokter
 * License: copyleft
 */

// block direct access
defined('ABSPATH') or die('');

require_once(__DIR__ . '/lv-repo.php');
require_once(__DIR__ . '/widget.php');

// functionality
// 1. no need for scripts
// 2. read page / post views on page load
// 3. store / get recently viewed pages / posts from cookie
//    - cookie is required to keep it centered around the visitor
//    - no personal information is saved, only functionality, so GDPR-proof
// 4. output as a widget
// 5. output as a shortcode (nice to have)

function jrv_load_widget() {
    register_widget('jrv_widget');
}
add_action('widgets_init', 'jrv_load_widget');

add_action('init', function() {
    if(!is_admin()) {
        $actual_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $post_id = url_to_postid($actual_url);
        
        jrv_lastviewed_repo::addCurrentPage($post_id);
    }
});

