<?php

// Defining constant for the template directory.
define('UMM_TEMPLATE', __DIR__ . '/template/');

// Defining constant for the plugin directory.
define('UMM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Defining constant for the keys to be omitted.
define("UMM_EXEMPTED_LIST", serialize(array('session_tokens', 'mylogin_capabilities', 'managenav-menuscolumnshidden', 'metaboxhidden_nav-menus', 'mylogin_dashboard_quick_press_last_post_id')));

// Defining constant for the keys to be disabled 
define("UMM_DELETE_EXEMPTED_LIST", serialize(array('first_name', 'last_name', 'nickname')));

define("UMM_NONCE_LIFE_TIME", serialize('14400'));

// Defining page name of the plugin.
define("UMM_PAGE_NAME", serialize('users_page_user-meta-management'));

// Defining plugin prefix constant
define("UMM_PLUGIN_PREFIX", 'UMM');

// Defining success message
define("UMM_SUCCESS_MESSAGE", 'You have updated meta information successfully');

// Defining alert update message
define("UMM_ALERT_UPDATE", 'There is nothing to update');

// Defining alert delete message
define("UMM_ALERT_DELETE", 'There is nothing to delete');

// Defining default empty value
define("UMM_EMPTY_VALUE", '--');