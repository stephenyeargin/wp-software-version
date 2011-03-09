<?php
/*
Plugin Name: Software Versions
Version: 1.0
Plugin URI: http://stephenyeargin.com/blog/tag/plugins/
Description: Versions of server software returned in post or page content if <code>[version:{software}]</code> or <code>[version&nbsp;type='{software}']</code> tag (2.5.1 or later) is included. Available software: <code>php, mysql, wordpress</code>.
Author: Stephen Yeargin
Author URI: http://stephenyeargin.com
*/

/*
 * Get Software Version
 *
 * Get current versions of your server software
 *
 * @param   string  Software to check
 * @return  string  Version number
 */
function get_software_version($type) {
    $ver = '???';
    switch($type) {
        // MySQL
        case 'mysql':
            global $wpdb;
            $ver = $wpdb->get_var("SELECT VERSION() AS version;");
            break;

        // PHP
        case 'php':
            $ver = phpversion();
            break;

        // WordPress
        case 'wordpress':
            $ver = get_bloginfo('version');
            break;
    }
    return $ver;
}

// Use old method if not a 2.5 blog
if (!function_exists('shortcode_atts')) {

    /*
     * Software Version
     *
     * Filter given text for software version tag, return if requested
     *
     * @param   string  Text of entry
     * @return  string  Filtered text
     */
    function software_version($text) {
        $text = str_replace(
               array('[version:wordpress]', '[version:mysql]', '[version:php]'),
               array(get_software_version('wordpress'), get_software_version('mysql'), get_software_version('php')),
               $text);
        
        return $text;
    }
    
    add_filter('the_content', 'software_version');

// Let's use the new shortcodes
} else {
    
    /*
     * Software Version
     *
     * Use short tags to encode software version
     *
     * @param   array   Attributes passed to shortcode
     * @return  string  Filtered text
     */
    function software_version($atts) {
        extract(shortcode_atts(array(
            'type' => 'wordpress'
        ), $atts));
        
        return get_software_version($type);
    }
    
    add_shortcode('version', 'software_version');
    
}
        