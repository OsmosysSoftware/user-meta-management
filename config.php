<?php

// Defining constant for the template directory.
define('TEMPLATE', __DIR__ . '/template/');

// Defining constant for the plugin directory.
define('USER_META_PLUGIN_URL', plugin_dir_url(__FILE__));

// Defining constant for the keys to be omitted.
define("EXCEMPTED_LIST", serialize(array('session_tokens', 'mylogin_capabilities', 'managenav-menuscolumnshidden', 'metaboxhidden_nav-menus', 'mylogin_dashboard_quick_press_last_post_id')));

