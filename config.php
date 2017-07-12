<?php

// Defining constant for the template directory.
define('TEMPLATE', __DIR__ . '/template/');

// Defining constant for the plugin directory.
define('USER_META_PLUGIN_URL', plugin_dir_url(__FILE__));

// Defining constant for the keys to be omitted.
define("EXEMPTED_LIST", serialize(array('session_tokens', 'mylogin_capabilities', 'managenav-menuscolumnshidden', 'metaboxhidden_nav-menus', 'mylogin_dashboard_quick_press_last_post_id')));

// Defining constant for the keys to be disabled 
define("DELETE_EXEMPTED_LIST", serialize(array('first_name', 'last_name','nickname')));

define("NONCE_LIFE_TIME", serialize('14400'));