<?php

/*
Plugin Name: AntiTor
Plugin URI: http://ronmasas.com/antitor
Description: Protect your website from Tor users, using this plugin you will be able to fully detect and restrict any Tor activity on your website.
Version: 1.0.0
Author: Ron Masas
Author URI: http://ronmasas.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	die('Hi there!  I\'m just a plugin, not much I can do when called directly.');
}

// Generate random filename for the tor ip list
if ( !get_option("at_filename") ) {
	update_option("at_filename", sha1(time()) . '.list' );
}

// Uninstall hook
register_uninstall_hook(__FILE__,"uninstall_antitor");

// Defines
define('ANTITOR_VERSION', '1.0.0');
define('ANTITOR_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('ANTITOR_LAST_UPDATE',get_option("at_last_updated"));
define('ANTITOR_BLOCKED_COUNT',get_option("at_block_count"));
define('ANTITOR_LIST_FILENAME',get_option("at_filename"));
define('ANTITOR_USER_IP', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

//Actions
add_action('wp', 'antitor_wp'); // Runs before template loads
add_action('login_head','antitor_wp'); // Runs in login head tag
add_action('admin_menu', 'antitor_menu');
add_action('admin_enqueue_scripts', 'antitor_scripts');


function uninstall_antitor() {
	echo "Uninstall!";
	// Options array
	$options = array(
		"at_block",
		"at_filename",
		"at_block_count",
		"at_last_updated",
		"at_message"
	);
	// For each option in array
	foreach( $options as $option ) {
		// Check if set
		if ( get_option($option) ) {
			// Delete option
			delete_option($option);
		}
	}
}

function antitor_wp() {
	// Check if file exists
	if ( !file_exists(dirname(__FILE__) . '/' . ANTITOR_LIST_FILENAME) ){
		// File was not found, update list
		update_tor_list();
	} else {
		if ( !ANTITOR_LAST_UPDATE ){
			// Option was not set, update the list
			update_tor_list();
		} else {
			// Check that 1 hour has passt from the last update
			if ( ANTITOR_LAST_UPDATE + 3599 < time() ) {
				update_tor_list(); // Update the list
			}
		}
	}
	$BLOCK = get_option("at_block");
	// Convert lines of file to array
	$IPS = @file(dirname(__FILE__) . '/' . ANTITOR_LIST_FILENAME, FILE_IGNORE_NEW_LINES);
	// Validate conversion
	if ( is_array($IPS) ) {
		// Check if current ip is in list
		if( in_array(ANTITOR_USER_IP, $IPS) ) {
			// TOR WAS DETECTED
			if ( $BLOCK == true ) { // Check if need to block
				if ( !ANTITOR_BLOCKED_COUNT ) {
					update_option("at_block_count", 1); // Init the block count
				} else {
					update_option("at_block_count", ANTITOR_BLOCKED_COUNT + 1 ); // Add 1 to count
				}
				// TOR USER WAS BLOCKED
				die( stripslashes( get_option("at_message") ) );
			}
		}
	}
}

function update_tor_list() {
	// Get Tor ip list
	$list = @file_get_contents( "http://localhoster.org/torlist/" );
	if ( !strstr($list,'can only') ) { // Validate response
		// Save new ips list
		@file_put_contents(dirname(__FILE__) . '/' . ANTITOR_LIST_FILENAME, $list);
		// Update timestamp
		update_option("at_last_updated",time());
	}
}

function antitor_menu() {
	/* Add antitor menu item to the admin menu */
	$adminPage = add_menu_page('Anti Tor', 'Anti Tor', 'manage_options', 'anti-tor', 'antitor_panel' );
	/* Add help tab */
	add_action('load-'.$adminPage, 'help_tab');
}

function antitor_scripts() {
	/* Register */
	wp_register_script('at-panel-js', plugins_url('panel.js', __FILE__));
	/* Enqueue */ 
	wp_enqueue_script('at-panel-js');
	wp_enqueue_style('at-panel-css',plugins_url('panel.css', __FILE__));
}

function help_tab () {
	// Add help tab to the plugin panel page
    $screen = get_current_screen();
    $screen->add_help_tab( array(
        'id'	=> 'antitor_help_tab',// ID
        'title'	=> __('Protect Your Website.'), // TAB TITLE
        'content'	=> '<p>' . __( 'Tor is a great tool, but the anonymity it offers also attracts a lot of bad people.<br />Using this plugin you will be able to fully detect & restrict any Tor activity on your website.<br />If you have any questions or feedback you can contact me at <a href="mailto:ronmasas@gmail.com">ronmasas@gmail.com</a>' ) . '</p>', // CONTENT
    ) );
}

/* Get "time ago" from timestamp */
function time_elapsed_string( $ptime ) {
    $etime = time() - $ptime;
    if ( $etime < 1 ){
        return '0 seconds';
    }
    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );
    foreach ( $a as $secs => $str ){
        $d = $etime / $secs;
        if ( $d >= 1 ) {
            $r = round( $d );
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}

function antitor_panel() {
	/* Anti Tor Panel */
	require_once "panel.php";
}

?>
