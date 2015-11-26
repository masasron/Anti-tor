<?php

/*
  Plugin Name: AntiTor
  Plugin URI: http://ronmasas.com/antitor
  Description: Protect your website from Tor users, using this plugin you will be able to fully detect and restrict any Tor activity on your website.
  Version: 2.0
  Author: Ron Masas
  License: GPLv2 or later
 */

// Make sure Shortcut is active
if (!class_exists('Shortcut'))
{
    return;
}

/**
 * @ver array
 */
$files = array(
    'controllers/*.php',
    'app.php'
);

Shortcut::make(__FILE__)->requireAll($files);
