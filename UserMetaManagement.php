<?php

/* *
 * Plugin Name: User Meta Management
 * Plugin URI: http://osmosyssoftware.github.io/user-meta-management/
 * Description: This plugin is used to manage the users meta information.
 * Version: 0.1.0
 * Author: Osmosys Software Solutions
 * Author URI: http://osmosys.asia
 * License: GPLv2
 */
require_once( __DIR__ . '/config.php');

class UserMetaManagement {

    public function __construct() {
        $this->styleRegisterer();
        $this->scriptRegisterer();
        add_shortcode('show_meta_form', array($this, 'showMetaForm')); // Shortcode to view the form.
        add_shortcode('specific_metakey', array($this, 'showAllUsersOfSpecificMetaKey')); // Shortcode to show the users of specific meta key combinations.
        add_action('wp_ajax_meta_search', array($this, 'metaSearch')); // Action to search the meta key value combinations.
        add_action('wp_ajax_get_user_meta_details', array($this, 'getUserMetaDetails'));  // Action to show  user meta information to the admin.
        add_action('wp_ajax_get_users', array($this, 'getUsers'));  // Action to show  user meta information to the admin.
        add_action('wp_ajax_update_user_meta_data', array($this, 'updateUserMetaDetails')); // Action to update the user meta information.
        add_action('wp_ajax_add_user_meta_data', array($this, 'adduserMeta')); // Action to add the user meta information.
        add_action('wp_ajax_delete_user_meta', array($this, 'deleteUserMetaDetails')); // Action to delete the user meta information.
        add_action('wp_ajax_nopriv_meta_search', array($this, 'metaSearch')); // Action to search the user meta information.
        add_action('wp_ajax_nopriv_get_user_meta_details', array($this, 'getUserMetaDetails')); // Action to get the user meta details.
        add_action('wp_ajax_nopriv_get_users', array($this, 'getUsers')); // Action to get the user meta details.
        add_action('wp_ajax_nopriv_update_user_meta_data', array($this, 'updateUserMetaDetails')); //  Action to update the user meta details.
        add_action('wp_ajax_nopriv_add_user_meta_data', array($this, 'adduserMeta')); //  Action to update the user meta details.
        add_action('wp_ajax_nopriv_delete_user_meta', array($this, 'deleteUserMetaDetails'));  // Action to delete the user meta details.
        add_action('admin_menu', array($this, 'addUserPage')); // Action to add the user page to user section in the admin dashboard.
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'addActionLink')); // Filter to add the settings option to the plugin.
        add_filter('nonce_life', array($this, 'nonceLifeTime'));
//        add_action('admin_menu', array($this,'userMetaManagerSettingsPage'));
    }

    // Function to register all the styles.
    public function styleRegisterer() {
        wp_register_style('user-meta-bootstrap-css', USER_META_PLUGIN_URL . 'css/bootstrap.min.css');
        wp_register_style('user-meta-datatable-css', USER_META_PLUGIN_URL . 'css/datatable-bootstrap.css');
        wp_register_style('user-meta-style-css', USER_META_PLUGIN_URL . 'css/style.css');
        wp_register_style('user-meta-select-css', USER_META_PLUGIN_URL . 'css/bootstrap-select.min.css');
        wp_register_style('user-meta-chosen-css', USER_META_PLUGIN_URL . 'css/chosen.min.css');
    }

    //  Function to register all the scripts.
    public function scriptRegisterer() {
        wp_register_script('user-meta-bootstrap-js', USER_META_PLUGIN_URL . 'js/bootstrap.min.js');
        wp_register_script("user-meta-jquery-js", USER_META_PLUGIN_URL . 'js/jquery.js');
        wp_register_script("user-meta-datatable-js", USER_META_PLUGIN_URL . 'js/datatables-min.js');
        wp_register_script("user-meta-script", USER_META_PLUGIN_URL . 'js/script.js');
        wp_register_script("user-meta-notify", USER_META_PLUGIN_URL . 'js/notify.min.js');
        wp_register_script("user-meta-select-js", USER_META_PLUGIN_URL . 'js/bootstrap-select.min.js');
        wp_register_script("user-meta-chosen-js", USER_META_PLUGIN_URL . 'js/chosen.jquery.min.js');
    }

    // Function to enqueue all the registered scripts and styles.
    public function enquerer() {
        wp_enqueue_style('user-meta-bootstrap-css');
        wp_enqueue_style('user-meta-datatable-css');
        wp_enqueue_style('user-meta-style-css');
        wp_enqueue_style('user-meta-select-css');
        wp_enqueue_style('user-meta-chosen-css');
        wp_enqueue_script('user-meta-jquery-js');
        wp_enqueue_script('user-meta-bootstrap-js');
        wp_enqueue_script('user-meta-datatable-js');
        wp_enqueue_script('user-meta-select-js');
        wp_enqueue_script('user-meta-chosen-js');
        wp_enqueue_script('user-meta-script');
        wp_enqueue_script('user-meta-notify');
        wp_localize_script('user-meta-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('user-meta-management')));
    }

    // Function to parse the template.
    public function parseTemplate( $file, $inputData ) {
        ob_start();
        include ($file);
        return ob_get_clean();
    }

    // Function to show the meta form.
    public function showMetaForm() {
        return ($this->parseTemplate(TEMPLATE . '/meta-form.php', null));
    }

    /* Function to show the all users of specific metakey value equals.
     *
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
     * 9) The result will be returned to the client side.
     *
     */

    public function showAllUsersOfSpecificMetaKey( $metakey, $metavalue ) {
        $result = wp_get_current_user();
        $userData = ($result->allcaps);
        $userId = array();
        $key = substr($metakey, -1);
        $mkey = substr($metakey, 0, -1);
        if ($key == '%') {
            global $wpdb;
            $statement = "SELECT meta_key  FROM wp_usermeta WHERE meta_key like '%" . $mkey . "%'; ";
            $results = $wpdb->get_results($statement, ARRAY_N);
            for ($i = 0; $i < count($results); $i++) {
                $users = get_users(array(
                    'meta_key' => $results[0][$i]
                ));
                for ($j = 0; $j < count($users); $j++) {
                    $id = (array) ($users[$j]->data);
                    if (!in_array($id['ID'], $userId)) {
                        $userId[] = $id['ID'];
                    }
                }
            }
        } else {
            $users = get_users(array(
                'meta_key' => $metakey,
                'meta_value' => $metavalue,
                'meta_compare' => '=',
            ));
        }
        if ($userData['administrator']) {
            if (count($users)) {
                $user = array();
                for ($j = 0; $j < count($users); $j++) {
                    $id = (array) ($users[$j]->data);
                    $userDetails = (array) get_userdata($id['ID']);
                    $userMetaInformation = get_user_meta($id['ID']);
                    $userDetailsEmail = (array) get_userdata($id['ID'])->data;
                    $userRoles = implode(', ', get_userdata($id['ID'])->roles);
                    $userId = $userDetails['ID'] == " " ? '------------' : $userDetails['ID'];
                    $userFirstName = ($userMetaInformation['first_name'][0] == '' ? '   -----------  ' : $userMetaInformation['first_name'][0]);
                    $userLastName = ($userMetaInformation['last_name'][0] == '' ? '   ------------  ' : $userMetaInformation['last_name'][0]);
                    $userEmail = ($userDetailsEmail['user_email'] == '' ? ' ---------------------------- ' : $userDetailsEmail['user_email']);
                    $userInformation = array('id' => $userId, 'firstName' => $userFirstName, 'lastName' => $userLastName, 'email' => $userEmail, 'role' => $userRoles);
    
                array_push($user, $userInformation);
                }
            } else {
                $user = null;
            }return ($this->parseTemplate(TEMPLATE . '/users-same-meta-information.php', $user));
        } else {
            echo '<h2>You are not authorised to view this page.</h2>';
        }
    }

    /*
     * Function to search the meta key and value combinations.
     * 1) From the client side the meta key and value  will be received through the ajax call.
     * 2) We'll pass the meta key and value to the showAllUsersOfSpecificMetaKey function which process the data passed and return the result.
     * 3) The result of users who have the same meta key and value matched is echoed.
     *
     */

    public function metaSearch() {
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            $metaKey = filter_input(INPUT_POST, 'metaKey');
            $metaValue = filter_input(INPUT_POST, 'metaValue');
            echo($this->showAllUsersOfSpecificMetaKey($metaKey, $metaValue));
            die();
        }
    }

    /*
     * Function to get the user meta details.
     * 1) The function gets user Id from the client side through the ajax call;.
     * 2) The user is passed through the wordpress funtion get_user_meta which gives all the meta keys and values list.
     * 3) We'll keep track of the unwanted keys and the remaining will be displayed to the user.
     *
     */

    public function getUserMetaDetails() {
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            $userId = filter_input(INPUT_POST, 'userId');
            $excemptedList = unserialize(EXCEMPTED_LIST);
            $metaList = get_user_meta($userId);
            $metaListKeys = array_keys($metaList);
            for ($i = 0; $i < count($metaListKeys); $i++) {
                if (!in_array($metaListKeys[$i], $excemptedList)) {
                    $key = $metaListKeys[$i];
                    $value = ($metaList[$key][0] == '' ? ' ----------- ' : $metaList[$key][0]);
                    $userMetaDetailsResults[$key] = $value;
                }
            }

            echo($this->parseTemplate(TEMPLATE . '/user-meta-information.php', $userMetaDetailsResults));
            die();
        }
    }

    /*  .
     * 1) Receives the userId and meta information to be updated from the client side through ajax call..
     * 2) The meta information to be updated consists of array of meta keys and values.
     * 3) First the list of keys are passed into the variable.
     * 4) Then by passing the userid, meta key and meta value to the  update_user_meta function of wordpress
     *      the  meta information will be updated.
     * 5) The update_user_meta function also take care off adding the new meta information.
     *
     */

    public function updateUserMetaDetails() {
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            $meta = $_POST['userMetaData'];
            $userId = filter_input(INPUT_POST, 'userId');
            $keys = array_keys($meta);
            for ($i = 0; $i < count($keys); $i++) {
                $update = update_user_meta($userId, $keys[$i], $meta[$keys[$i]]);
            }
            if ($update) {
                echo json_encode(array('success' => 'You have updated meta information successfully'));
            } else {
                echo json_encode(array('error' => 'There is nothing to update'));
            }
            die();
        }
    }

    /* Function to delete the user meta information.
     * 1) This function receives the userId and meta information  to be deleted from the client side through ajax call..
     * 2) The meta information to be deleted consists of array of meta keys and values.
     * 3) First the list of keys are passed into the variable.
     * 4) Then by passing the userid, meta key and meta value to the  delete_user_meta function of wordpress
     *     the  meta information will be deleted.
     *
     */

    public function deleteUserMetaDetails() {
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            $metaData = $_POST['userMetaData'];
            $userId = $_POST['userId'];
            $keys = array_keys($metaData);
            for ($i = 0; $i < count($keys); $i++) {
                $delete = delete_user_meta($userId, $keys[$i], $metaData[$keys[$i]]);
            } if ($delete) {
                echo json_encode(array('success' => 'You have deleted meta information successfully'));
            } else {
                echo json_encode(array('error' => 'There is nothing to delete'));
            }
            die();
        }
    }

    // Function to create a user page in the user secion in the user seciton.
    public function addUserPage() {
        add_users_page('User Meta Management', 'User Meta Management', 'manage_options', 'user-meta-management', array($this, 'userMetaManagement'));
    }

    public function userMetaManagement() {
        $this->enquerer();
        echo($this->showMetaForm());
        echo($this->showAllUsersOfSpecificMetaKey(null, null));
    }

    public function addActionLink( $links ) {
        $mylinks = array(
            '<a href="' . admin_url('users.php?page=user-meta-settings') . '">Settings</a>',
        );
        return array_merge($links, $mylinks);
    }

    public function nonceLifeTime( $time ) {
        return 60 * 60 * 4;
    }

    // Function to add the user meta data to the selected users.
    public function adduserMeta() {
        global $wpdb;
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            $users=($_POST['users']);
            $metaKey = $_POST['metaKey'];
            $metaValue = filter_input(INPUT_POST, 'metaValue');
            $tableName = $wpdb->prefix . 'users';
//            $statement = 'SELECT ID FROM ' . $tableName;
//            $result = $wpdb->get_results($statement, ARRAY_A);
            for ($i = 0; $i < count($users); $i++) {
                $userId = $users[$i];
                $update = update_user_meta($userId, $metaKey, $metaValue);
            }
            if ($update) {
                echo json_encode(array('success' => 'You have updated meta information successfully'));
            } else {
                echo json_encode(array('error' => 'There is nothing to update'));
            }
            die();
        }
    }

    // Function to get all the users.
    public function getUsers() {
        check_ajax_referer('user-meta-management', 'security', TRUE);
        if (current_user_can('manage_options')) {
            global $wpdb;
            $user = array();
            $tableName = $wpdb->prefix . 'users';
            $statement = 'SELECT ID FROM ' . $tableName;
            $result = $wpdb->get_results($statement, ARRAY_A);
            for ($i = 0; $i < count($result); $i++) {
                $userId = $result[$i]['ID'];
                $userMetaInformation = get_user_meta($userId);
                $userNickName = ($userMetaInformation['nickname'][0] == '' ? '   ------------  ' : $userMetaInformation['nickname'][0]);
                $user[$userId] = $userNickName;
            }
            echo json_encode($user);
            die();
        }
    }
    
    // Adding the Settings page for the user meta manager plugin.
//    public function userMetaManagerSettingsPage(){
//        add_menu_page('Meta Settings', 'Meta Settings', 'administrator', 'user-meta-settings', array($this,'pluginDisplaySettings'));
//        
//    }
//    
//    public function pluginDisplaySettings(){
//         $this->enquerer();
//        echo($this->parseTemplate(TEMPLATE .'/settings.php', NULL));
//    }

}

    

$meta = new UserMetaManagement();
