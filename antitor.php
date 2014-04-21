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

// Uninstall hook
register_uninstall_hook(__FILE__,"uninstall_antitor");

// Defines
define('ANTITOR_VERSION', '1.0.0');
define('ANTITOR_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('ANTITOR_BLOCKED_COUNT',get_option("at_block_count"));

//Actions
add_action('wp', 'antitor_detect'); // Runs before template loads
add_action('login_head','antitor_detect'); // Runs in login head tag
add_action('admin_menu', 'antitor_menu');
add_action('admin_enqueue_scripts', 'antitor_scripts');


function uninstall_antitor() {
	// Options array
	$options = array(
		"at_block",
		"at_block_count",
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

/* Check if TOR exit node */
function is_tor_exit_point(){
	$URL = reverse_ipoctets( $_SERVER['REMOTE_ADDR'] ) . "." . $_SERVER['SERVER_PORT'] . "." . reverse_ipoctets( $_SERVER['SERVER_ADDR'] );
    // Check if host name is 127.0.0.2 = Tor exit node
    return ( gethostbyname( $URL . ".ip-port.exitlist.torproject.org" ) == "127.0.0.2" );
}

/* Reverse IP order */
function reverse_ipoctets( $input_ip ) {
    $ipoc = explode(".",$input_ip);
    return $ipoc[3] . "." . $ipoc[2] . "." . $ipoc[1] . "." . $ipoc[0];
}

function antitor_detect() {
	$BLOCK = get_option("at_block");
	if ( is_tor_exit_point() ) {
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

function antitor_menu() {
	/* Add antitor menu item to the admin menu */
	$adminPage = add_menu_page('Anti Tor', 'Anti Tor', 'manage_options', 'anti-tor', 'antitor_panel' );
	/* Add help tab */
	add_action('load-'.$adminPage, 'help_tab');
}

function antitor_scripts() {
	/* Register scripts */
	wp_register_script('at-panel-js', plugins_url('panel.js', __FILE__));
	/* Enqueue scripts */ 
	wp_enqueue_script('at-panel-js');
	/* Enqueue style */
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

function antitor_panel() {
	// Include the admin panel
	require_once "panel.php";
}

?>
