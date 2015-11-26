<?php

/*
  |--------------------------------------------------------------------------
  | Plugin Setup
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the plugin functionality.
  | Simply use shortcut to create pages shortcodes and much more.
  |
 */

$this->uninstall('AntiTorSettingsController@uninstall');

$this->page(array(
    'title' => 'Anti Tor',
    'parent' => 'options-general',
    'request.get' => 'AntiTorSettingsController@settings',
    'request.post' => 'AntiTorSettingsController@save'
));

$this->bind(['wp', 'login_head'], 'AntiTorController@detectTorUsers');
