<?php

/* *
 * Plugin Name: User Meta Management
 * Plugin URI: http://osmosyssoftware.github.io/user-meta-management/
 * Description: This plugin is used to manage the users meta information.
 * Version: 0.0.1
 * Author: Osmosys Software Solutions
 * Author URI: http://osmosys.asia
 * License: MIT
 */

define('UMM_VERSION', '0.0.1');

require_once( __DIR__ . '/config.php');

class UMMUserMetaManagement {

    public function __construct() {	
	add_action('admin_enqueue_scripts', array($this, 'UMM_enquerer'));
	// Shortcode to view the form.
	add_shortcode('UMM_show_meta_form', array($this, 'UMM_showMetaForm'));
	// Shortcode to show the users of specific meta key combinations.
	add_shortcode('UMM_specific_metakey', array($this, 'UMM_showAllUsersOfSpecificMetaKey')); 
	// Action to search the meta key value combinations.
	add_action('wp_ajax_UMM_meta_search', array($this, 'UMM_metaSearch')); 
	// Action to show  user meta information to the admin.
	add_action('wp_ajax_UMM_get_user_meta_details', array($this, 'UMM_getUserMetaDetails'));  
	// Action to update the user meta information.
	add_action('wp_ajax_UMM_update_user_meta_data', array($this, 'UMM_updateUserMetaDetails')); 
	// Action to delete the user meta information.
	add_action('wp_ajax_UMM_delete_user_meta', array($this, 'UMM_deleteUserMetaDetails')); 
	// Action to add the user page to user section in the admin dashboard.
	add_action('admin_menu', array($this, 'UMM_addUserPage')); 
	// Filter to add the settings option to the plugin.
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'UMM_addActionLink')); 
	add_filter('nonce_life', array($this, 'UMM_nonceLifeTime'));
	
	// Filters
        add_filter('script_loader_src', array($this, 'UMM_refresh_browserCache'));
        add_filter('style_loader_src', array($this, 'UMM_refresh_browserCache'));
    }

    /**
     * Function to enqueue all the registered scripts and styles.
     * @param type $hook
     * @return type
     */
    public function UMM_enquerer($hook) {
	// Checking the page name, if it is not user meta management page, then scripts and styles will not be enqueued
	if( $hook !== unserialize(UMM_PAGE_NAME)) {
	    return;
	}
	
	wp_register_style(UMM_PLUGIN_PREFIX.'user-meta-font-awesome-css', UMM_PLUGIN_URL . '/css/font-awesome.min.css');
	wp_register_style(UMM_PLUGIN_PREFIX.'user-meta-datatable-css', UMM_PLUGIN_URL . '/css/datatable-bootstrap.css');
	wp_register_style(UMM_PLUGIN_PREFIX.'user-meta-style-css', UMM_PLUGIN_URL . '/css/style.css');

	wp_enqueue_style(UMM_PLUGIN_PREFIX.'user-meta-font-awesome-css');
	wp_enqueue_style(UMM_PLUGIN_PREFIX.'user-meta-datatable-css');
	wp_enqueue_style(UMM_PLUGIN_PREFIX.'user-meta-style-css');
	wp_enqueue_style ('wp-jquery-ui-dialog');

	wp_register_script(UMM_PLUGIN_PREFIX.'user-meta-datatable-js', UMM_PLUGIN_URL . '/js/datatables-min.js', array('jquery'), '', true);
	wp_register_script(UMM_PLUGIN_PREFIX.'user-meta-script', UMM_PLUGIN_URL . '/js/script.js', array('jquery','jquery-ui-core', 'jquery-ui-dialog'), '', true);
	wp_register_script(UMM_PLUGIN_PREFIX.'user-meta-notify', UMM_PLUGIN_URL . '/js/notify.min.js', array('jquery'), '', true);
	wp_enqueue_script(UMM_PLUGIN_PREFIX.'user-meta-datatable-js');
	wp_enqueue_script(UMM_PLUGIN_PREFIX.'user-meta-script');
	wp_enqueue_script(UMM_PLUGIN_PREFIX.'user-meta-notify');
	wp_localize_script(UMM_PLUGIN_PREFIX.'user-meta-script', 'UMMData', array('ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('user-meta-management')));
    }
    
    /**
     * Function to parse the template.
     * @param type $file
     * @param type $inputData
     * @return type
     */
    private function UMM_parseTemplate($file, $inputData) {
	ob_start();
	include ($file);
	return ob_get_clean();
    }
 
    /**
     * Function to show the meta form.
     * @return type
     */
    public function UMM_showMetaForm() {
	return ($this->UMM_parseTemplate(UMM_TEMPLATE . '/meta-form.php', null));
    }

    /**
     * Function to show the all users of specific metakey value equals.
     * 1) The function  accepts two parameters i.e. meta key and meta value.
     * 2) First the function get the current user details.
     * 3) If the current user is administrator then he has privilige to see the details.
     *     ohterwise the user wil be shown an error message.
     * 4) If the user is administrator then the details are passed to the template page
     * 5) In template page we'll make use of the wordpress function get_users which accepts the meta key
     *    and value and by making the  compare value  equal to (=) the function will return the list of users who
     *    have the passed meta key and value matched.
     * 6) By grabbing the user id from the result we'll get the user details by using get_userdata and get_user_meta function of wordpress.
     * 7) From the user details we'll take the userId,firstname,lastname and email address of the user.
     * 8) The function generates the list based on the count of number of users who have the meta key value combination matched.
     * 9) The result will be returned to the client side.     * 
     * @param type $metakey
     * @param type $metavalue
     * @return type
     */
    public function UMM_showAllUsersOfSpecificMetaKey($metakey, $metavalue) {
	$result = wp_get_current_user();
	$userData = ($result->allcaps);
	if ($userData['administrator']) {
	    $users = get_users(array(
		'meta_key' => $metakey,
		'meta_value' => $metavalue,
		'meta_compare' => '=',
	    ));
	    $user = array();
	    if (count($users)) {
		
		for ($j = 0; $j < count($users); $j++) {
		    $userInfo = (array) ($users[$j]->data);
		    $userDetails = (array) get_userdata($userInfo['ID']);
		    $userMetaInformation = get_user_meta($userInfo['ID']);
		    $userDetailsEmail = (array) get_userdata($userInfo['ID'])->data;
		    $userRoles = implode(', ', get_userdata($userInfo['ID'])->roles);
		    $userId = $userDetails['ID'] == "" ? '' : $userDetails['ID'];
		    $userFirstName = ($userMetaInformation['first_name'][0] == '' ? $this->UMM_defaultValue() : $userMetaInformation['first_name'][0]);
		    $userLastName = ($userMetaInformation['last_name'][0] == '' ? $this->UMM_defaultValue() : $userMetaInformation['last_name'][0]);
		    $userEmail = ($userDetailsEmail['user_email'] == '' ? $this->UMM_defaultValue() : $userDetailsEmail['user_email']);
		    $userInformation = array('id' => $userId, 'firstName' => $userFirstName, 'lastName' => $userLastName, 'email' => $userEmail, 'role' => $userRoles);
		    array_push($user, $userInformation);
		}
	    }
	    return ($this->UMM_parseTemplate(UMM_TEMPLATE . '/users-same-meta-information.php', $user));
	} else {
	    echo '<h2>You are not authorised to view this page.</h2>';
	}
    }

    /**
     * Function to search the meta key and value combinations.
     * 1) From the client side the meta key and value  will be received through the ajax call.
     * 2) We'll pass the meta key and value to the UMMShowAllUsersOfSpecificMetaKey function which process the data passed and return the result.
     * 3) The result of users who have the same meta key and value matched is echoed.
     */
    public function UMM_metaSearch() {
	check_ajax_referer('user-meta-management', 'security', TRUE);
	if (current_user_can('manage_options')) {
	    $metaKey = filter_input(INPUT_POST, 'metaKey');
	    $metaValue = filter_input(INPUT_POST, 'metaValue');
	    echo($this->UMM_showAllUsersOfSpecificMetaKey($metaKey, $metaValue));
	    die();
	}
    }

    /**
     * Function to get the user meta details.
     * 1) The function gets user Id from the client side through the ajax call;.
     * 2) The user is passed through the wordpress funtion get_user_meta which gives all the meta keys and values list.
     * 3) We'll keep track of the unwanted keys and the remaining will be displayed to the user.
     */    
    public function UMM_getUserMetaDetails() {
	check_ajax_referer('user-meta-management', 'security', TRUE);
	if (current_user_can('manage_options')) {
	    $userId = filter_input(INPUT_POST, 'userId');
	    $exemptedList = unserialize(UMM_EXEMPTED_LIST);
	    $metaList = get_user_meta($userId);
	    $metaListKeys = array_keys($metaList);
	    for ($i = 0; $i < count($metaListKeys); $i++) {
		if (!in_array($metaListKeys[$i], $exemptedList)) {
		    $key = $metaListKeys[$i];
		    $value = ($metaList[$key][0] == '' ? '' : $metaList[$key][0]);
		    $userMetaDetailsResults[$key] = $value;
		}
	    }

	    echo($this->UMM_parseTemplate(UMM_TEMPLATE . '/user-meta-information.php', $userMetaDetailsResults));
	    die();
	}
    }

    /**
     * 1) Receives the userId and meta information to be updated from the client side through ajax call..
     * 2) The meta information to be updated consists of array of meta keys and values.
     * 3) First the list of keys are passed into the variable.
     * 4) Then by passing the userid, meta key and meta value to the  update_user_meta function of wordpress
     *      the  meta information will be updated.
     * 5) The update_user_meta function also take care off adding the new meta information.
     */
    public function UMM_updateUserMetaDetails() {
	check_ajax_referer('user-meta-management', 'security', TRUE);
	if (current_user_can('manage_options')) {
	    $meta = filter_input(INPUT_POST, 'UMMData', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
              
              // Send error response if meta data is not set.
            if (!$meta) {
                echo json_encode(['error' => UMM_ALERT_UPDATE]);
                die();
            }

            $userId = filter_input(INPUT_POST, 'userId');
	    $keys = array_keys($meta);
	    for ($i = 0; $i < count($keys); $i++) {
		$update = update_user_meta($userId, $keys[$i], $meta[$keys[$i]]);
	    }
	    if ($update) {
		echo json_encode(array('success' => UMM_SUCCESS_MESSAGE));
	    } else {
		echo json_encode(array('error' => UMM_ALERT_UPDATE));
	    }
	    die();
	}
    }

    /** 
     * Function to delete the user meta information.
     * 1) This function receives the userId and meta information  to be deleted from the client side through ajax call..
     * 2) The meta information to be deleted consists of array of meta keys and values.
     * 3) First the list of keys are passed into the variable.
     * 4) Then by passing the userid, meta key and meta value to the  delete_user_meta function of wordpress
     *     the  meta information will be deleted.
     */
    public function UMM_deleteUserMetaDetails() {
	check_ajax_referer('user-meta-management', 'security', TRUE);
	if (current_user_can('manage_options')) {
	    $metaData = filter_input(INPUT_POST, 'UMMData', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
               
               // Send error response if meta data is not set.
            if (!$metaData) {
                echo json_encode(['error' => UMM_ALERT_DELETE]);
                die();
            }

            $userId = filter_input(INPUT_POST, 'userId');
	    $keys = array_keys($metaData);
	    for ($i = 0; $i < count($keys); $i++) {
		$delete = delete_user_meta($userId, $keys[$i], $metaData[$keys[$i]]);
	    } if ($delete) {
		echo json_encode(array('success' => UMM_SUCCESS_MESSAGE));
	    } else {
		echo json_encode(array('error' => UMM_ALERT_DELETE));
	    }
	    die();
	}
    }

    /**
     * Function to create a user page in the user secion in the user seciton.
     */
    public function UMM_addUserPage() {
	add_users_page('User Meta Management', 'User Meta Management', 'manage_options', 'user-meta-management', array($this, 'userMetaDataManagement'));
    }

    /**
     * Function to echo user meta data form and other specific meta keys and values.
     */
    public function userMetaDataManagement() {
	echo($this->UMM_showMetaForm());
	echo($this->UMM_showAllUsersOfSpecificMetaKey(null, null));
    }
 
    /**
     * Fixing the nonce life time.
     * @param type $time
     * @return type
     */
    public function UMM_nonceLifeTime($time) {
	$UMM_nonceLifeTime = unserialize(UMM_NONCE_LIFE_TIME);
	return $UMM_nonceLifeTime;
    }

    /**
     * Adding the settings options to the user meta management plugin.
     * @param type $links
     * @return type
     */
    public function UMM_addActionLink($links) {
	$mylinks = array(
	    '<a href="' . admin_url('users.php?page=user-meta-management') . '">Settings</a>',
	);
	return array_merge($links, $mylinks);
    }
    
    /**
     * Function to return '--' for empty values.
     * @return string
     */
    private function UMM_defaultValue() {
	return UMM_EMPTY_VALUE;
    }
    
    /**
     *
     * It is used to clear cache files when version is changed.
     * @param type $src
     * @return string
     */
    public function UMM_refresh_browserCache($src) {
	$version_str = '?ver=' . UMM_VERSION;
	// Put your regular expression here
	if (preg_match('/plugins\/[1-9]+.[a-zA-Z]+/', $src) || preg_match('/[myurl]+\/[style]+.css/', $src)) {
	    if (strpos($src, '?ver=')) {
		$search = substr($src, strpos($src, '?ver='));
		$src = str_replace($search, $version_str, $src);
	    } else {
		$src = $src . $version_str;
	    }
	}
	return $src;
    }
}

$meta = new UMMUserMetaManagement();
