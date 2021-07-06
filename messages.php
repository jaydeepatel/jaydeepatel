<?php
/* Template Name: Messages */

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
$path = explode('wp-content', __DIR__);
include_once($path[0] . 'wp-load.php');
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

//include_once('/var/www/funtimeflirt/wp-load.php');
//global $current_user;

//$user_id = $current_user->ID;

define('DBNAME', 'dating_db');

/** MySQL database username */
define('DBUSER', 'root');

/** MySQL database password */
define('DBPASSWORD', '@Bs4zcvgD#zQooTGbA@');

/** MySQL hostname */
define('DBHOST', 'localhost');

/** WP prefix */
define('PREFIX', 'ft_');

/** WP prefix */
define('ABSPATH', '/var/www/funtimeflirt/');

global $wpdb;
$dsp_instant_chat_table  = table_name('dsp_instant_chat_table');

$user_id                 = get_current_user_id();

$user_meta = get_userdata($user_id);
if (in_array('operator', $user_meta->roles, true)) {
    wp_redirect('https://funtimeflirt.com/wp-admin');
    exit;
}

//echo "user_id=".$user_id;
$current_user = get_user_details($user_id);
//print_r($current_user);

$user_list = load_recent_chats($user_id, true, 1);

if ($_GET['id']) {
    $chat_user    = get_user_details($_GET['id']);
    $chat_user_id =  $chat_user['ID'];
} else if (is_array($user_list) && count($user_list) > 0 && empty($_GET['id'])) {
    $chat_user    = get_user_details($user_list[0]['receiver_id']);
    $chat_user_id = $user_list[0]['receiver_id'];
}


$ip = $_SERVER['REMOTE_ADDR'];
//$result = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/json'));
/*$result = json_decode(file_get_contents('https://www.iplocate.io/api/lookup/'.$ip));
$aus_state = array('South Wales','Queensland','South Australia','Tasmania','Victoria','Western Australia');
$random_state=array_rand($aus_state,1);
if($result->subdivision != NULL && $result->subdivision != '' && in_array($result->subdivision,$aus_state) ){ 
	if(!isset($_SESSION['location']) || $_SESSION['location'] == '' || $_SESSION['location'] == NULL){
		$_SESSION['location'] = $result->subdivision; 
        $updated = update_user_meta( $user_id, 'user_state', $_SESSION['location'] );
		update_user_meta( $user_id, 'user_ip', $ip );
	}
}else{
	if(!isset($_SESSION['location']) || $_SESSION['location'] == '' || $_SESSION['location'] == NULL){
		//$_SESSION['location'] = $aus_state[$random_state]; 
		$_SESSION['location'] = $result->subdivision;
        $updated = update_user_meta( $user_id, 'user_state', $_SESSION['location'] );
		update_user_meta( $user_id, 'user_ip', $ip );
	}
}*/
update_user_meta($user_id, 'last_login', date('Y-m-d h:i:s'));
$result2 = json_decode(file_get_contents('https://www.iplocate.io/api/lookup/' . $ip));
if (metadata_exists('user', $user_id, 'user_state')) {
    update_user_meta($user_id, 'user_state', $result2->subdivision);
} else {
    add_user_meta($user_id, 'user_state', $result2->subdivision);
}

if (metadata_exists('user', $user_id, 'user_ip')) {
    update_user_meta($user_id, 'user_ip', $ip);
} else {
    add_user_meta($user_id, 'user_ip', $ip);
}
//echo "ok";
//wp_localize_script('wpdating-instant-chat', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

//enqueue_styles();
//enqueue_scripts();
if ($user_id) {
    $new_count = $wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "notifications_logs WHERE `member_id` = $user_id AND `is_seen` = 0 ORDER BY ID DESC");
    //$notification = $wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."notifications_logs WHERE `member_id` = $user_id ORDER BY ID DESC LIMIT 5");

    $notification_count = $wpdb->get_var("SELECT *  FROM " . $wpdb->prefix . "notifications_logs WHERE `member_id` = " . $user_id . " AND `is_seen` = '0' ORDER BY ID DESC");

    if ($notification_count > 0) {
        $notification = $wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "notifications_logs WHERE `member_id` = " . $user_id . " AND `is_seen` = '0' ORDER BY ID DESC");
    } else {
        $notification = $wpdb->get_results("SELECT *  FROM " . $wpdb->prefix . "notifications_logs WHERE `member_id` = " . $user_id . " ORDER BY ID DESC LIMIT 5");
    }
}


function table_name($name)
{
    global $wpdb;
    $table_name = [
        'dsp_users'                       => $wpdb->prefix . DSP_USERS_TABLE,
        'dsp_user_privacy_table'          => $wpdb->prefix . DSP_USER_PRIVACY_TABLE,
        'dsp_language_table'              => $wpdb->prefix . DSP_LANGUAGE_TABLE,
        'dsp_instant_chat_table'          => $wpdb->prefix . 'dsp_instant_chat',
        'dsp_user_table'                  => $wpdb->prefix . DSP_USERS_TABLE,
        'dsp_my_friends_table'            => $wpdb->prefix . DSP_MY_FRIENDS_TABLE,
        'dsp_user_profiles_table'         => $wpdb->prefix . DSP_USER_PROFILES_TABLE,
        'dsp_options_table'               => $wpdb->prefix . 'options',
        'dsp_instant_chat_details_table'  => $wpdb->prefix . 'instant_chat_details',
        'dsp_instant_chat_photos'         => $wpdb->prefix . 'dsp_instant_chat_photos',
        'dsp_user_online_table'           => $wpdb->prefix . DSP_USER_ONLINE_TABLE,
        'dsp_members_photos_table'        => $wpdb->prefix . DSP_MEMBERS_PHOTOS_TABLE,

    ];

    return $table_name[$name];
}

function connect_database()
{

    $servername = DBHOST;
    $username = DBUSER;
    $password = DBPASSWORD;
    $dbname = DBNAME;

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}



function get_user_details($id)
{
    global $wpdb;
    $dsp_user_profiles_table        = table_name('dsp_user_profiles_table');
    $dsp_members_photos_table       = table_name('dsp_members_photos_table');;

    return $wpdb->get_row("SELECT user.*,profile.*,
                                (SELECT member_photo.picture FROM $dsp_members_photos_table AS member_photo 
                                WHERE member_photo.user_id = user.ID LIMIT 1) AS image_name
                                FROM $wpdb->users user
                                JOIN $dsp_user_profiles_table profile
                                 ON user.ID = profile.user_id
                                 WHERE user.ID = $id", ARRAY_A);
}




function enqueue_styles()
{
    error_log("1");


    wp_enqueue_style('wpdating-instant-chat', get_stylesheet_directory_uri() . 'public/css/wpdating-instant-chat-public.css', array(), '1.0', 'all');
    wp_enqueue_style('wpdating-instant-chat' . '1', get_stylesheet_directory_uri() . 'public/css/wpdating-image-modal.css', array(), '1.0', 'all');
    wp_enqueue_style('wpdating-instant-chat' . '2', get_stylesheet_directory_uri() . 'lib/emoji-picker/lib/css/emoji.css', array(), '1.0', 'all');
    wp_enqueue_style('wpdating-message-page', get_stylesheet_directory_uri() . 'public/css/wpdating-chat-page.css', array(), '1.0', 'all');
    wp_enqueue_style('wpdating-instant-chat' . '-toastr', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css', array(), '1.0', 'all');
}


function enqueue_scripts()
{
    error_log("2");
    require_once('/var/www/funtimeflirt/wp-load.php');

    global $wpdb;
    $dsp_user_profiles   = table_name('dsp_user_profiles_table');
    echo $dsp_user_profiles;
    $user_id             = get_current_user_id();
    echo $user_id;
    $chk_profile_created = $wpdb->get_var("SELECT COUNT(*) FROM $dsp_user_profiles WHERE status_id=1 and country_id!= 0 AND user_id = '$user_id'");

    //if ($user_id && $chk_profile_created > 0) {

    wp_enqueue_script('wpdating-instant-chat' . '2', get_stylesheet_directory_uri() . 'lib/emoji-picker/lib/js/config.js', array('jquery'), '1.0', true);
    wp_enqueue_script('wpdating-instant-chat' . '3', get_stylesheet_directory_uri() . 'lib/emoji-picker/lib/js/util.js', array('jquery'), '1.0', true);
    wp_enqueue_script('wpdating-instant-chat' . '4', get_stylesheet_directory_uri() . 'lib/emoji-picker/lib/js/jquery.emojiarea.js', array('jquery'), '1.0', true);
    wp_enqueue_script('wpdating-instant-chat' . '5', get_stylesheet_directory_uri() . 'lib/emoji-picker/lib/js/emoji-picker.js', array('jquery'), '1.0', true);
    wp_enqueue_script('wpdating-instant-chat' . '6', get_stylesheet_directory_uri() . 'public/js/wpdating-emoji-handler.js', array('jquery'), '1.0', true);
    //wp_localize_script('wpdating-instant-chat' . '6', 'object', array(
    //   'plugins_url' => plugin_dir_url(dirname(__FILE__))));
    wp_enqueue_script('wpdating-common-js1', get_stylesheet_directory_uri() . 'public/js/wpdating-common.js', array('jquery'), '1.0', true);
    wp_localize_script('wpdating-common-js1', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_enqueue_script('image-modal', get_stylesheet_directory_uri() . 'public/js/wpdating-image-modal.js', array('jquery'), '1.0', true);
    wp_enqueue_script('wpdating-instant-chat' . '-toastr', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array('jquery'), '1.0', true);

    /*if (((strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"))
                &&  (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet"))
                && strpos($_SERVER["REQUEST_URI"],'chat')) || strpos($_SERVER["REQUEST_URI"],'chat')){*/


    if (wp_script_is('wpdating-message-page1', 'enqueued')) {
        echo "test123";
    } else {
        echo "test";
        wp_enqueue_script('wpdating-message-page1', get_stylesheet_directory_uri() . 'public/js/wpdating-chat-page.js', array('jquery'), '1.0', true);
        wp_localize_script('wpdating-message-page1', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    /*  }else if (!(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"))
                &&  !(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet"))){*/

    wp_enqueue_script('wpdating-instant-chat7', get_stylesheet_directory_uri() . 'public/js/wpdating-instant-chat-public.js', array('jquery'), '1.0', true);
    wp_localize_script('wpdating-instant-chat7', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_script('wpdating-socket-js', get_stylesheet_directory_uri() . 'public/js/wpdating-socket-handler.js', array('jquery'), '1.0', true);

    // }else{
    wp_enqueue_script('mobile-socket-js', get_stylesheet_directory_uri() . 'public/js/mobile-socket-handler.js', array('jquery'), '1.0', true);
    //}
    //wp_localize_script('wpdating-instant-chat', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    // }

}
add_action('wp_enqueue_scripts', 'enqueue_styles');
add_action('wp_enqueue_scripts', 'enqueue_scripts');

function load_recent_chats($user_id, $page, $page_num, $show_friend_only = 'N', $search_key = null)
{
    error_log("3");
    global $wpdb;
    $dsp_instant_chat_details_table = table_name('dsp_instant_chat_details_table');
    $dsp_user_profiles_table        = table_name('dsp_user_profiles_table');
    $dsp_user_online_table          = table_name('dsp_user_online_table');
    $dsp_user_privacy_table         = table_name('dsp_user_privacy_table');
    $dsp_my_friends_table           = table_name('dsp_my_friends_table');
    $dsp_members_photos_table       = table_name('dsp_members_photos_table');;
    $dsp_instant_chat_table         = table_name('dsp_instant_chat_table');;

    $sql_query      = "SELECT chat_detail.sender_id,chat_detail.receiver_id,
                            user.ID, user.display_name,user.user_login, 
                            profile.make_private, profile.gender,
                            online.status,
                            (SELECT COUNT(*) FROM $dsp_instant_chat_table AS chat 
                             WHERE (chat.receiver_id = $user_id
                              AND chat.sender_id = user.ID
                              AND chat.message_read = 0)) AS unread_count,
                              (SELECT `created_at` FROM ft_dsp_instant_chat AS chat1 
                              WHERE chat1.receiver_id = $user_id AND chat1.sender_id = user.ID AND chat1.is_read = 0
                              ORDER BY chat1.created_at DESC LIMIT 1) AS created,
                              (SELECT member_photo.picture FROM $dsp_members_photos_table AS member_photo 
                             WHERE member_photo.user_id = user.ID LIMIT 1) AS image_name
                            FROM $dsp_instant_chat_details_table AS chat_detail
                            JOIN $wpdb->users AS user 
                            ON chat_detail.receiver_id = user.ID
                            JOIN $dsp_user_profiles_table AS profile
                            ON user.ID = profile.user_id
                            LEFT JOIN $dsp_user_online_table AS online
                            ON user.ID = online.user_id 
                            LEFT JOIN $dsp_my_friends_table AS friend
                            ON user.ID = friend.friend_uid
                            AND friend.user_id = {$user_id}
                            LEFT JOIN $dsp_user_privacy_table AS user_privacy
                            ON user_privacy.user_id = user.ID
                            WHERE";

    if ($show_friend_only == 'Y') {
        $sql_query .= " friend.approved_status = 'Y' AND";
    } else {
        $sql_query .= " (( user_privacy.with_instant_chat_permission = 'Y'
                                AND friend.approved_status = 'Y') OR 
                                ( user_privacy.with_instant_chat_permission = 'N' OR 
                                user_privacy.with_instant_chat_permission IS NULL )) AND";
    }
    if (!is_null($search_key)) {
        $sql_query .= " user.display_name LIKE '%$search_key%' AND";
    }

    //$sql_query .= " chat_detail.sender_id = $user_id ORDER BY chat_detail.updated_at DESC";
    $sql_query .= " chat_detail.sender_id = $user_id ORDER BY created DESC";

    //$offset         = ($page_num - 1) * 15;
    //$sql_query      .= " LIMIT {$offset},15";

    $user_list = $wpdb->get_results($sql_query, ARRAY_A);

    if ($wpdb->num_rows <= 0) {
        if (null != $search_key) {
            return "<div class='no-user-text'>No Users Found</div>";
        } else {
            return "<div class='no-user-text'>No Recent Chats</div>";
        }
    }

    if ($page) {
        return $user_list;
    }

    return prepare_recent_user_list_for_response($user_list);
}

function prepare_recent_user_list_for_response($user_id, $user_array, $search_key)
{
    $output = '';
    foreach ($user_array as $user) {
        $user_data = get_user_data($user['receiver_id'], $search_key);
        if (null == $user_data) {
            continue;
        }
        $data = get_latest_message($user['receiver_id'], $user['sender_id']);

        if ($user_id == $data['receiver_id'] && $data['message_read'] == 0) {
            $output .= "<div class=\"dsp-user-container\" onclick=\"open_chat_box(" . $user_data['id'] . ",'" . $user_data['display_name'] . "','" . $user_data['username'] . "'," . $data['message_id'] . "," . $data['thread_id'] . ")\">";
            $output .= "<div class=\"active\">";
        } else {
            $output .= "<div class=\"dsp-user-container\" onclick=\"open_chat_box(" . $user_data['id'] . ",'" . $user_data['display_name'] . "','" . $user_data['username'] . "')\">";
            $output .= "<div>";
        }

        $output .= "<img src=\"" . $user_data['image'] . "\" class=\"dsp-image-container\" >";
        if (strlen($user_data['display_name']) > 15) {
            $output .= substr($user_data['display_name'], 0, 13) . "...";
        } else {
            $output .= $user_data['display_name'];
        }

        if ($user_data['status'] == 'online') {
            $output .= "<span class=\"chat-online\"></span>";
        }

        $output .= " <p>" . $data['chat_text'] . "</p>";

        $output .= "</div></div>";
    }

    if ($output == '') {
        $output = "<div class='no-user-text'>No Users Found</div>";
    }

    return $output;
}

function get_user_data($id, $search_key)
{

    $dsp_user_table = PREFIX . 'users';
    $dsp_user_profiles_table = PREFIX . 'dsp_user_profiles';
    $sql_query      = "SELECT user.ID, user.display_name,user.user_login, profile.make_private, profile.gender FROM $dsp_user_table AS user
                            JOIN $dsp_user_profiles_table AS profile
                            ON user.ID = profile.user_id WHERE ID = $id";
    if (null != $search_key) {
        $sql_query .= " AND display_name LIKE '%$search_key%'";
    }
    $conn       = connect_database();
    $result     = $conn->query($sql_query);
    if ($result->num_rows <= 0) {
        return null;
    }
    $row = $result->fetch_assoc();
    $data                     = array();
    $data['id']               = $row['ID'];
    $data['display_name']     = $row['display_name'];
    $data['username']         = $row['user_login'];
    $data['gender']           = $row['gender'];
    if ($row['make_private'] == 'Y') {
        $data['image'] = get_image($row['ID'], true, $row['gender']);
    } else {
        $data['image'] = get_image($row['ID'], false, $row['gender']);
    }

    $data['status']    = check_online($id);

    return $data;
}

function check_online($id)
{
    $dsp_user_online_table = PREFIX . 'dsp_user_online';
    $sql_query      = "SELECT * FROM $dsp_user_online_table WHERE user_id = $id";

    $conn       = connect_database();
    $result     = $conn->query($sql_query);
    $status     = 'offline';
    if ($result->num_rows > 0) {
        $status = 'online';
    }

    return $status;
}

function get_image($user_id, $private, $status = 'M')
{
    $dsp_members_photos = PREFIX . 'dsp_members_photos';
    $conn       = connect_database();
    $result     = $conn->query("Select picture from $dsp_members_photos where user_id=$user_id");
    $site_url   = get_site_url();
    $image              = '';
    if ($private == true) {
        $image = $site_url . "/wp-content/plugins/dsp_dating/images/private-photo-pic.jpg";
    } else {
        if ($result->num_rows > 0) {
            $my_img    = $result->fetch_object();
            $image  =  $site_url . "/wp-content/uploads/dsp_media/user_photos/user_{$user_id}/thumbs/thumb_{$my_img->picture}";
        } else {
            if ($status == 'F') {
                $image = $site_url . "/wp-content/plugins/dsp_dating/images/female-generic.jpg";
            } else if ($status == 'C') {
                $image = $site_url . "/wp-content/plugins/dsp_dating/images/couples-generic.jpg";
            } else {
                $image = $site_url . "/wp-content/plugins/dsp_dating/images/male-generic.jpg";
            }
        }
    }
    return $image;
}

function get_image_using_name($user_id, $image_name, $private, $status = 'M')
{
    error_log("37");
    $image              = '';
    if ($private == 'Y') {
        $image = get_site_url() . "/wp-content/plugins/dsp_dating/images/private-photo-pic.jpg";
    } else {
        if (!empty($image_name)) {
            $image  =  get_site_url() . "/wp-content/uploads/dsp_media/user_photos/user_{$user_id}/thumbs/thumb_{$image_name}";
        } else {
            if ($status == 'F') {
                $image = get_site_url() . "/wp-content/plugins/dsp_dating/images/female-generic.jpg";
            } else if ($status == 'C') {
                $image = get_site_url() . "/wp-content/plugins/dsp_dating/images/couples-generic.jpg";
            } else {
                $image = get_site_url() . "/wp-content/plugins/dsp_dating/images/male-generic.jpg";
            }
        }
    }
    return $image;
}


function get_latest_message($receiver_id, $sender_id)
{
    error_log("31");
    $dsp_instant_chat_table = PREFIX . 'dsp_instant_chat';
    $sql_query      = "SELECT * FROM $dsp_instant_chat_table WHERE (receiver_id = $receiver_id AND sender_id = $sender_id) OR
                            (receiver_id = $sender_id AND sender_id = $receiver_id) ORDER BY created_at DESC LIMIT 1";
    $conn       = connect_database();
    $result     = $conn->query($sql_query);
    if ($result->num_rows <= 0) {
        return '';
    }

    $row                  = $result->fetch_assoc();
    //$new_time = date('H:i', strtotime($row['created_at']));
    $new_time = date('m/d/Y H:i:s', strtotime($row['created_at']));

    $data                 = array();
    $data['receiver_id']  = $row['receiver_id'];
    $data['message_id']   = $row['chat_id'];
    $data['message_read'] = $row['message_read'];
    $data['is_read'] = $row['is_read'];
    $data['thread_id']    = $row['thread_id'];
    $data['created_at']    = $new_time;
    if ($row['photo_id'] !=  null) {
        $data['chat_text'] = 'sent an image';
    } else {
        $data['chat_text']    = $row['chat_text'];
        if (strlen($data['chat_text']) > 30) {
            $data['chat_text'] = substr($data['chat_text'], 0, 30) . "...";
        }
    }
    return $data;
}

function get_latest_message_mobile($receiver_id, $sender_id)
{
    error_log("31");
    $dsp_instant_chat_table = PREFIX . 'dsp_instant_chat';
    $sql_query      = "SELECT * FROM $dsp_instant_chat_table WHERE (receiver_id = $receiver_id AND sender_id = $sender_id) OR
                            (receiver_id = $sender_id AND sender_id = $receiver_id) ORDER BY created_at DESC LIMIT 1";
    $conn       = connect_database();
    $result     = $conn->query($sql_query);
    if ($result->num_rows <= 0) {
        return '';
    }

    $row                  = $result->fetch_assoc();
    //$new_time = date('H:i', strtotime($row['created_at']));
    $new_time = date('m/d/Y H:i:s', strtotime($row['created_at']));

    $data                 = array();
    $data['receiver_id']  = $row['receiver_id'];
    $data['message_id']   = $row['chat_id'];
    $data['message_read'] = $row['message_read'];
    $data['is_read'] = $row['is_read'];
    $data['thread_id']    = $row['thread_id'];
    $data['created_at']    = $new_time;
    if ($row['photo_id'] !=  null) {
        $data['chat_text'] = 'sent an image';
    } else {
        $data['chat_text']    = $row['chat_text'];
        if (strlen($data['chat_text']) > 30) {
            $data['chat_text'] = substr($data['chat_text'], 0, 40) . "...";
        }
    }
    return $data;
}

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}
function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}
function get_chat_list_by_user_id($chat_user_id, $current_user_id, $page, $page_num)
{
    error_log("15");
    global $wpdb;
    $dsp_instant_chat_table        = table_name('dsp_instant_chat_table');
    $dsp_instant_chat_photos_table = table_name('dsp_instant_chat_photos');

    $offset         = ($page_num - 1) * 15;
    $sql_query      = "SELECT * FROM $dsp_instant_chat_table AS instant_chat
                            LEFT JOIN $dsp_instant_chat_photos_table AS chat_photo
                            ON instant_chat.photo_id = chat_photo.id
                            WHERE (instant_chat.receiver_id = $current_user_id 
                            AND instant_chat.sender_id = $chat_user_id) 
                            OR 
                            (instant_chat.receiver_id = $chat_user_id 
                            AND instant_chat.sender_id = $current_user_id) 
                            ORDER BY instant_chat.created_at DESC";

    $chat_list     = $wpdb->get_results($sql_query, ARRAY_A);

    $response      = '';
    if ($wpdb->num_rows > 0) {
        if (!$page) {
            for ($i = count($chat_list) - 1; $i >= 0; $i--) {
                if ($chat_list[$i]['sender_id'] == $current_user_id) {
                    $response .= "<div class='message-container message-container-right'>" .
                        get_message($chat_list[$i], false) . "</div>";
                } else {
                    $response .= "<div class='message-container message-container-left'>"  .
                        get_message($chat_list[$i], true) . "</div>";
                }
            }
        } else {
            for ($i = count($chat_list) - 1; $i >= 0; $i--) {
                if ($chat_list[$i]['sender_id'] == $current_user_id) {
                    $response .= "<li class='replies test'>" . get_message($chat_list[$i], false) . "</li>";
                } else {
                    $response .= "<li class='sent test'>" . get_message($chat_list[$i], true) . "</li>";
                }
            }
        }
    }

    return $response;
}

function get_message($chat, $receiver = false)
{
    error_log("16");
    global $wpdb;

    //$msg_time = date('H:i', strtotime($chat['created_at']));
    $msg_time = date('m/d/Y H:i:s', strtotime($chat['created_at']));

    //echo "<script type='text/javascript'>document.write(GetAddress(",$lat,",",$lng,"));</script>";
    //$new_msg_time = '<script>getLocaltime('.$msgs_time.')</script>';

    //$msg_time = $new_msg_time;

    if (!is_null($chat['photo_id'])) {
        if ($chat['photo_id'] == "1") {
            $chat_id = $chat['chat_id'];
            $private_image = $wpdb->get_var("SELECT `private_image` FROM `chatter_private_image_message` WHERE message_id = $chat_id");
            $image = $private_image;
        } else {
            $image = get_image_message_by_name($chat['sender_id'], $chat['receiver_id'], $chat['picture']);
        }
        if ($receiver) {
            $message = "<div class='message-image'>
                                <span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time1' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>
                                <img onclick=\"show_image_preview('" . $image . "')\" id='image-msg' src='" . $image . "' alt='no-image'>
                         </div>";
        } else {
            if ($chat['message_read'] == 1) {
                $message = "<div class='message-image'>
                    <span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time2' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>
                    <img onclick=\"show_image_preview('" . $image . "')\" onclick='show_image_preview()' id='image-msg' src='" . $image . "' alt='no-image'>
                    </div>";
            } else {
                $message = "<div class='message-image'>
                    <span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time3' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>
                   <img onclick=\"show_image_preview('" . $image . "')\" id='image-msg' src='" . $image . "' alt='no-image'>
                    </div>";
            }
        }
    } else {
        $len = strlen($chat['chat_text']);
        $f = substr($chat['chat_text'], 0, 1);
        $l = substr($chat['chat_text'], $len - 1, 1);
        //$emo = clean($chat['chat_text']);
        $emo = str_replace(':', '', $chat['chat_text']);
        $count_str = substr_count($chat['chat_text'], ":");


        if ($chat['message_read'] == 1) {
            if (($f === $l) && ($f === ':')) {
                $message = "<div class='message-text emoji " . $emo . "' data-id='" . $chat['chat_text'] . "'  data-ids='" . $emo . "'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time4' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            } elseif (($count_str > 1)) {
                $output = array();
                preg_match_all('/:(.*?)\:/i', $chat['chat_text'], $output);
                //$output = get_string_between($chat['chat_text'], ':', ':');
                $cnt = count($output[1]);
                $output2 = $output[1];
                $emos = "";
                $emos_id = "";
                for ($i = 0; $i < $cnt; $i++) {
                    //if($i%2 != 0) {
                    $emos .= $output2[$i];
                    $emos_id .= ":" . $output2[$i] . ":";
                    //}
                }
                //$message = print_r($output2, TRUE);
                $message = "<div class='message-text emoji " . $emos . "' data-id='" . $emos_id . "'  data-ids='" . $emos . "' data-msg='" . $chat['chat_text'] . "'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time5' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            } else {
                $message = "<div class='message-text'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            }
        } else {
            if (($f === $l) && ($f === ':')) {
                $message = "<div class='message-text emoji " . $emo . "' data-id='" . $chat['chat_text'] . "'  data-ids='" . $emo . "'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time6' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            } elseif (($count_str > 1)) {
                $output = array();
                preg_match_all('/:(.*?)\:/i', $chat['chat_text'], $output);
                //$output = get_string_between($chat['chat_text'], ':', ':');
                $cnt = count($output[1]);
                $output2 = $output[1];
                $emos = "";
                $emos_id = "";
                for ($i = 0; $i < $cnt; $i++) {
                    //if($i%2 != 0) {
                    $emos .= $output2[$i];
                    $emos_id .= ":" . $output2[$i] . ":";
                    //}
                }
                //$message = print_r($output2, TRUE);
                $message = "<div class='message-text emoji " . $emos . "' data-id='" . $emos_id . "'  data-ids='" . $emos . "' data-msg='" . $chat['chat_text'] . "'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;display:none;' class='msg-time msg-time7' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            } else {
                $message = "<div class='message-text'><span style='float: left;padding-bottom: 10px;padding-right: 5px;font-size: 11px;width: 100%;' class='msg-time msg-time8' data-msgtime='" . $msg_time . "'>" . $msg_time . "</span>" . $chat['chat_text'] . "</div>";
            }
        }
        /*if ($receiver){
            $message = "<div class='message-text' title='".date('Y-m-d H:i A', strtotime($chat['created_at'])) . "'>".$chat['chat_text']."</div>";
        }else{
            if ($chat['message_read'] == 1){
                $message = "<div class='message-text' title='".date('Y-m-d H:i A', strtotime($chat['created_at'])) . "\nseen'>".$chat['chat_text']."</div>";
            }else{
                $message = "<div class='message-text' title='".date('Y-m-d H:i A', strtotime($chat['created_at'])) . "\ndelivered'>".$chat['chat_text']."</div>";
            }
        }*/
    }

    return $message;
}

function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function clean($string)
{
    $string = str_replace(':', '', $string);
    return $string;
}

function get_image_message_by_name($sender, $receiver, $image_name)
{
    error_log("35");
    return get_site_url() . '/wp-content/uploads/dsp_media/instant_chat_photos/chat_' . $sender .  '_' . $receiver . '/' . $image_name;
}

add_action("wp_ajax_page_chat_box_by_user_id", "page_chat_box_by_user_id");
add_action("wp_ajax_nopriv_page_chat_box_by_user_id", "page_chat_box_by_user_id");
function page_chat_box_by_user_id()
{
    error_log("13");
    if ((isset($_GET['chat_user_id'])) && ($_GET['chat_user_id'] != "") && ($_GET['chat_user_id'] != "NaN")) {
        $user  = get_user_details($_GET['chat_user_id']);
        $image = get_image_using_name($user['ID'], $user['image_name'], ($user['make_private'] == 'Y'), $user['gender']);

        $uid = $user['ID'];
        $response = " <div class='contact-profile'>";
        //$response .="   <a href='". site_url()."/profile/?id=".$uid."'>
        $response .= "                          <img src='" .  $image . "' height='34px' />
                                    <p class='dsp-current-user-display-name'>" . $user['display_name'] . "</p>
                                    
                                </div>
                                <div class='messages dsp-chat-list dsp-chat-" .  $user['ID'] . "' >";
        $response .= "<ul>";
        if (isset($_GET['chat_user_id']) && isset($_GET['current_user_id'])) {
            $response .= get_chat_list_by_user_id($_GET['chat_user_id'], $_GET['current_user_id'], true, 1);
        }

        if (isset($_GET['data_message']) && $_GET['data_message'] == 2) {
            $response .= "<li>
                                    <a class='btn btn-danger' href= '" . get_site_url() . "/members/setting/upgrade_account' type='button' style='width: 100%;margin-top: 10px; font-size: 13px;color: white;'>
                                    <i class='fa fa-shopping-cart' style='color: white;font-size: 15px;'></i>Not enough credits. Please Purchase.
                                    </a>
                                </li>";
        }
        $response .= "</ul>";
    } else {
        $response = " <div class='contact-profile'></div>";
    }
    die($response);
}

/*add_action( "wp_ajax_get_available_credits", "get_available_credits");
add_action( "wp_ajax_nopriv_get_available_credits", "get_available_credits");
function get_available_credits($user_id){
    global $wpdb;
    $credits = $wpdb->get_var("SELECT `no_of_credits` FROM `ft_dsp_credits_usage` WHERE user_id = '$user_id'");
    return $credits;
}

add_action( "wp_ajax_get_available_messages", "get_available_messages");
add_action( "wp_ajax_nopriv_get_available_messages", "get_available_messages");
function get_available_messages($user_id){
    global $wpdb;
    $available_msgs = $wpdb->get_var("SELECT `no_of_emails` FROM `ft_dsp_credits_usage` WHERE user_id = '$user_id'");
    return $available_msgs;
}*/

function check_read_message($sender_id, $receiver_id)
{
    global $wpdb;
    $ck_read = $wpdb->get_var("SELECT COUNT(*) FROM `ft_dsp_instant_chat` WHERE receiver_id= '$receiver_id' AND sender_id = '$sender_id'");

    if ($ck_read > 0) {
        return "1";
    } else {
        return "0";
    }
}

function check_like_profile($user_id, $chatter_id)
{
    global $wpdb;
    $ck_read = $wpdb->get_var("SELECT COUNT(*) FROM `ft_member_like` WHERE user_id= '$user_id' AND like_to = '$chatter_id'");

    if ($ck_read > 0) {
        return "1";
    } else {
        return "0";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Fun Time Flirt - Chat</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="https://funtimeflirt.com/wp-content/uploads/2021/03/favicon.ico" type="image/x-icon">
    <link rel="icon" href="https://funtimeflirt.com/wp-content/uploads/2021/03/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri() ?>/css/plugin.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri() ?>/css/common.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri() ?>/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri() ?>/public/css/wpdating-instant-chat-public.css">
    <!--<link rel="stylesheet" id="wpdating-instant-chat-css" href="https://funtimeflirt.com/wp-content/plugins/wpdating-instant-chat/public/css/wpdating-instant-chat-public.css" type="text/css" media="all">-->
    <link rel="stylesheet" id="wpdating-instant-chat1-css" href="https://funtimeflirt.com/wp-content/plugins/wpdating-instant-chat/public/css/wpdating-image-modal.css" type="text/css" media="all">
    <link rel="stylesheet" id="wpdating-instant-chat2-css" href="https://funtimeflirt.com/wp-content/plugins/wpdating-instant-chat/lib/emoji-picker/lib/css/emoji.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri() ?>/public/css/wpdating-chat-page.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/css/seprate-style.css" />
    <link type="text/css" rel="stylesheet" href="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/popup-style.css"/>
    <link rel="stylesheet" type="text/css" href="https://funtimeflirt.com/wp-content/themes/WPDATING/members/css/responsive.css" />
    <link rel="stylesheet" type="text/css" href="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/css/main-responsive.css" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-196602109-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-196602109-1');
    </script>
</head>

<body>

    <?php //if(isset($chat_user_id) && isset($current_user['ID'])){ 
    ?>
    <div class="notification-columns popup_slide">
        <div class="col_wrapper">
            <ul id="notifications_listing">
                <?php
                if (!empty($notification)) {
                    foreach ($notification as $notifications) {
                        $fname = get_user_meta($notifications->user_id, 'nickname', true);
                        $seconds_ago = (time() - strtotime($notifications->createdon));
                        if ($seconds_ago >= 31536000) {
                            $time_ago = intval($seconds_ago / 31536000) . " years ago";
                        } elseif ($seconds_ago >= 2419200) {
                            $time_ago = intval($seconds_ago / 2419200) . " months ago";
                        } elseif ($seconds_ago >= 86400) {
                            $time_ago = intval($seconds_ago / 86400) . " days ago";
                        } elseif ($seconds_ago >= 3600) {
                            $time_ago = intval($seconds_ago / 3600) . " hours ago";
                        } elseif ($seconds_ago >= 60) {
                            $time_ago = intval($seconds_ago / 60) . " minutes ago";
                        } else {
                            $time_ago = $seconds_ago . " seconds ago";
                        }
                ?>
                        <?php if ($notifications->is_click == '0') { ?>
                            <li id="<?php echo $notifications->ID; ?>" class="notification-list" style="cursor:pointer;">

                                <div class="padding_cover fix_include">
                                    <div class="img_box">
                                        <img class="img-fluid" src="<?php echo esc_url(get_avatar_url($notifications->user_id)); ?>">
                                    </div>
                                    <?php
                                    if ($notifications->notification_type == 1) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/profile?id=' . $notifications->user_id . '" style="cursor:pointer;"><strong>' . $fname . '</strong> Liked your profile <span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 2) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/chat?id=' . $notifications->user_id . '" style="cursor:pointer;"> Message from <strong>' . $fname . '</strong><span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 3) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/profile?id=' . $notifications->user_id . '" style="cursor:pointer;"><strong>' . $fname . '</strong> view your profile <span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 4) {
                                        echo '<div class="user_info"><strong>' . $fname . '</strong> profile matches with you <span class="timing">' . $time_ago . '</span></div>';
                                    }
                                    ?>
                                </div>
                            </li>
                        <?php
                        }
                    }

                    foreach ($notification as $notifications) {
                        $fname = get_user_meta($notifications->user_id, 'nickname', true);
                        $seconds_ago = (time() - strtotime($notifications->createdon));
                        if ($seconds_ago >= 31536000) {
                            $time_ago = intval($seconds_ago / 31536000) . " years ago";
                        } elseif ($seconds_ago >= 2419200) {
                            $time_ago = intval($seconds_ago / 2419200) . " months ago";
                        } elseif ($seconds_ago >= 86400) {
                            $time_ago = intval($seconds_ago / 86400) . " days ago";
                        } elseif ($seconds_ago >= 3600) {
                            $time_ago = intval($seconds_ago / 3600) . " hours ago";
                        } elseif ($seconds_ago >= 60) {
                            $time_ago = intval($seconds_ago / 60) . " minutes ago";
                        } else {
                            $time_ago = $seconds_ago . " seconds ago";
                        }
                        ?>
                        <?php if ($notifications->is_click == '1') { ?>
                            <li id="<?php echo $notifications->ID; ?>" class="notification-list" style="background-color:#fff;cursor:pointer;">

                                <div class="padding_cover fix_include">
                                    <div class="img_box">
                                        <img class="img-fluid" src="<?php echo esc_url(get_avatar_url($notifications->user_id)); ?>">
                                    </div>
                                    <?php
                                    if ($notifications->notification_type == 1) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/profile?id=' . $notifications->user_id . '" style="cursor:pointer;"><strong>' . $fname . '</strong> Liked your profile <span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 2) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/chat?id=' . $notifications->user_id . '" style="cursor:pointer;"> Message from <strong>' . $fname . '</strong><span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 3) {
                                        echo '<div class="user_info"><a data-url="' . get_site_url() . '/profile?id=' . $notifications->user_id . '" style="cursor:pointer;"><strong>' . $fname . '</strong> view your profile <span class="timing">' . $time_ago . '</span></a></div>';
                                    } else if ($notifications->notification_type == 4) {
                                        echo '<div class="user_info"><strong>' . $fname . '</strong> profile matches with you <span class="timing">' . $time_ago . '</span></div>';
                                    }
                                    ?>
                                </div>
                            </li>
                    <?php
                        }
                    }
                    echo '<li style="text-align: center;border: none;box-shadow: none;background: transparent;">
							<button type="button" class="btn btn-success" id="clear_all_notification">Clear All Notifications</button>
						</li>';
                } else { ?>
                    <li>
                        <div class="padding_cover fix_include">
                            <div class="user_info">
                                You have no any new notification.
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="row member_main_section dc_instant_chat_wrap message-pg" data-sticky-container>
        <!-- main section -->
        <div class="col-md-12 dc_content_header_wrap">
            <h2></h2>
        </div>


        <div id="frame">
            <div id="sidepanel" class="dsp-user-list-container">
                <div class="toggle-menu">
                    <button class="boost-button desktop-boost" data-toggle="modal" href="javascript:void(0)" data-target="#exampleModal" style="width: 98%;height: 50px;background: #ebaaf7;font-size: 28px;border-radius: 16px;">Boost <i class="fa fa-rocket" aria-hidden="true"></i></button>
                    <!-- <div class="title"><h2>Messages</h2></div> -->
                    <div class="tab_button_wrap">
                        <div class="tab1 active btn-msg-1">
                            <span>messages</span>
                            <span id="count-messages" style="display:none;"></span>
                        </div>
                        <div class="tab2 btn-msg-2">
                            <span>my chats</span>
                            <span id="count-mychats" style="display:none;"></span>
                        </div>
                    </div>
                </div>

                <!--            <div id="profile" class="dsp-current-user-details" >-->
                <!--                <div class="wrap">-->
                <!--                    --><?php
                                            //                    $image = get_image_using_name($current_user['ID'], $current_user['image_name'], false, $current_user['gender']);
                                            //                    
                                            ?>
                <!--                    <img id="profile-img" src="--><?php //echo $image; 
                                                                        ?>
                <!--" />-->
                <!--                    <p>--><?php //echo $current_user['display_name'] 
                                                ?>
                <!--</p>-->
                <!--                </div>-->
                <!--            </div>-->
                <div id="search" class="dsp-chat-user-search-input">
                    <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
                    <input type="text" name="dsp-chat-user-search" id="dsp-chat-user-search" placeholder="<?php echo language_code('DSP_CHAT_SEARCH'); ?>" onKeyUp="if(event.keyCode === 13) dsp_page_search_user()" />
                </div>
                <div id="contacts">
                    <ul>
                        <div class=" dsp-user-list">
                            <?php if (is_array($user_list) && count($user_list) > 0) { ?>
                                <?php
                                $count_read = 0;
                                foreach ($user_list as $user) {
                                ?>
                                    <li class="contact">

                                        <?php
                                        $ck_read = check_read_message($user_id, $user['receiver_id']);

                                        if ($ck_read == "0") { ?>
                                            <div class="dsp-user-row wrap <?php echo $user['receiver_id'] == $chat_user_id ? 'active' : ''; ?>
                                        <?php echo 'unread'; ?>" id="dsp-user-row-<?php echo $user['receiver_id']; ?>" onclick="dsp_open_chat_box(<?php echo $user['receiver_id']; ?>)">

                                            <?php } else { ?>
                                                <div class="dsp-user-row wrap <?php echo $user['receiver_id'] == $chat_user_id ? 'active' : ''; ?>
                                        <?php echo ''; ?>" id="dsp-user-row-<?php echo $user['receiver_id']; ?>" onclick="dsp_open_chat_box(<?php echo $user['receiver_id']; ?>)">

                                                <?php } ?>

                                                <!-- <a href="<?php //echo site_url()."/profile/?id=".$user['receiver_id']; 
                                                                ?>"> -->
                                                <img data-chatter="<?php echo $user['receiver_id']; ?>" src="<?php echo get_image_using_name($user['receiver_id'], $user['image_name'], ($user['make_private'] == 'Y'), $user['gender']); ?>" class="dsp-image-container profile-click">
                                                <!-- </a> -->
                                                <div class="meta">
                                                    <!--<a href="<? php // echo site_url()."/profile/?id=".$user['receiver_id']; 
                                                                    ?>"> -->
                                                    <label data-chatter="<?php echo $user['receiver_id']; ?>" class="meta_user_name profile-click" style="display: inline-block;cursor:pointer;">
                                                        <?php echo $user['display_name']; ?></label>
                                                    <!-- </a>-->
                                                    <?php $msg_time = get_latest_message($user['receiver_id'], $user_id)['created_at']; ?>
                                                    <span class="msg_time" data-msgtime="<?php echo $msg_time; ?>"><?php echo $msg_time; ?></span>
                                                    <?php /*if (isset($user['unread_count']) && $user['unread_count'] > 0){ */ ?>
                                                    <!--
                                                <span class='msg-unread'><?php /*echo $user['unread_count'] */ ?></span>
                                            --><?php /*} */ ?>
                                                    <?php if (isset($user['status']) && $user['status'] == 'Y') { ?>
                                                        <span class='online'></span>
                                                    <?php } ?>

                                                    <?php

                                                    $isMob = is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"));

                                                    if ($isMob) {
                                                        $latest_msg = get_latest_message_mobile($user['receiver_id'], $user_id)['chat_text'];
                                                    } else {
                                                        $latest_msg = get_latest_message($user['receiver_id'], $user_id)['chat_text'];
                                                    }


                                                    $is_read = get_latest_message($user['receiver_id'], $user_id)['is_read'];
                                                    $latest_receiver_id = get_latest_message($user['receiver_id'], $user_id)['receiver_id'];
                                                    // echo $latest_msg; 
                                                    ?>


                                                    <?php

                                                    $len = strlen($latest_msg);
                                                    $f = substr($latest_msg, 0, 1);
                                                    $l = substr($latest_msg, $len - 1, 1);
                                                    //$emo = clean($chat['chat_text']);
                                                    $emo = str_replace(':', '', $latest_msg);
                                                    $count_str = substr_count($latest_msg, ":");

                                                    if (($f === $l) && ($f === ':')) {
                                                        $message = "<p class='message-text emoji " . $emo . "' data-id='" . $latest_msg . "'  data-ids='" . $emo . "' >" . $latest_msg . "</p>";
                                                    } elseif (($count_str > 1)) {
                                                        $output = array();
                                                        preg_match_all('/:(.*?)\:/i', $latest_msg, $output);
                                                        //$output = get_string_between($chat['chat_text'], ':', ':');
                                                        $cnt = count($output[1]);
                                                        $output2 = $output[1];
                                                        $emos = "";
                                                        $emos_id = "";
                                                        for ($i = 0; $i < $cnt; $i++) {
                                                            //if($i%2 != 0) {
                                                            $emos .= $output2[$i];
                                                            $emos_id .= ":" . $output2[$i] . ":";
                                                            //}
                                                        }
                                                        //$message = print_r($output2, TRUE);
                                                        $message = "<div class='message-text emoji " . $emos . "' data-id='" . $emos_id . "'  data-ids='" . $emos . "' data-msg='" . $latest_msg . "' >" . $latest_msg . "</div>";
                                                    } else {
                                                        if ($is_read == '1') {
                                                            $message = "<p class='test1'>" . $latest_msg . "</p>";
                                                        } else {
                                                            if ($latest_receiver_id != $user['receiver_id']) {
                                                                $count_read = $count_read + 1;
                                                                $message = "<p style='font-weight:800'>" . $latest_msg . "</p>";
                                                            } else {
                                                                $message = "<p class='test'>" . $latest_msg . "</p>";
                                                            }
                                                        }
                                                    }
                                                    // $latest_msg = get_latest_message( $user['receiver_id'], $user_id)['chat_text'];
                                                    echo $message; ?>
                                                </div>
                                                </div>
                                    </li>
                            <?php }
                            } else if (is_string($user_list)) {
                                echo $user_list;
                            } ?>
                        </div>
                    </ul>
                </div>




            </div>
            <div class="dsp-chat-box content">
                <div class="dsp-chat-user-container" style=" height: 85%;padding-bottom: 40px;">
                    <div class="contact-profile">
                        <?php if (!empty($chat_user)) {
                            $image1 = get_image_using_name($chat_user_id, $chat_user['image_name'], ($chat_user['make_private'] == 'Y'), $chat_user['gender']);
                        ?>
                            <a href="<?php echo site_url() . "/profile/?id=" . $chat_user_id ?>"><img src="<?php echo $image1; ?>" />
                                <p><?php echo $chat_user['display_name'] ?></p>
                            </a>


                            <?php $ck_like = check_like_profile($user_id, $chat_user_id);
                            if ($ck_like != "0") { ?>
                                <i class="fa fa-heart" style="padding-left:10px;color:red;"></i>
                            <?php } ?>

                            <span class="left_msg" style="float:right;padding-right:10px;color:#fff;">Back</span>
                            <div class="left_arrow_msg" style="margin-right:0px;">

                                <svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' fill='currentColor' class='bi bi-arrow-left-short bi-arrow-left' viewBox='0 0 16 16'>
                                    <path fill-rule='evenodd' d='M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z' />
                                </svg>

                            </div>


                            <?php if (is_array($user_list) && count($user_list) > 0) { ?>
                                <?php foreach ($user_list as $user) {
                                    if ($user['receiver_id'] == $chat_user_id) {
                                ?>
                                        <?php
                                        if (isset($user['status']) && $user['status'] == 'Y') {
                                            echo "<span style='position: absolute;left: 20px;margin: 36px 0 0 -12px;width: 10px;height: 10px;border-radius: 50%;border: 2px solid #2c3e50;background-color: #5ecc72;'></span>";
                                        }
                                        ?>

                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

                        <?php } ?>

                    </div>
                    <div class="messages dsp-chat-list dsp-chat-<?php echo isset($chat_user_id) ? $chat_user_id : ''; ?>">
                        <ul>
                            <?php
                            /*echo "chat=".$chat_user_id;
                        echo "user_id_new=".$user_id;*/
                            $current_user = get_user_details($user_id);

                            if (isset($chat_user_id) && isset($user_id)) {

                                echo get_chat_list_by_user_id($chat_user_id, $user_id, true, 1);
                            }
                            ?>
                        </ul>


                        <div class="chat_box">
        <div class="chatcol">
            <h3 class="title">Don't Miss Out Chat Unlimited</h3>
            <div class="img_chat">
                <img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/x1.gif" alt="">
            </div>
            <button class="claim_now_btn">Claim Now</button>
        </div>
    </div>

                        <div class="verify_box">
        <div class="verify_content">
            <h3 class="title">Verify your email and get <a href="">100 Free Coins</a></h3>
            <div class="col_wrap">
                <p>
                    An Email Has Been Sent To <br>
                    <span>rakesh.patel@iblinfoetch.com</span><br> *Check Your Spam or Junk folder
                </p>
                <div class="img_wrap">
                    <img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/email-sending.svg" alt="">
                </div>
            </div>
            <button class="btn_verify">Verify</button>
        </div>
        <hr>
        <div class="verify_content">
            <p>
                Is the email you are using correct? If not...
            </p>
            <a href="#" style="color: #333;">Change Email Address</a>
        </div>
    </div>

                        <div class="welcome_box">
        <div class="welcom_content clearboth group">
            <div class="col_wrap">
                <h3 class="title">Welcome on board!</h3>
                <p>
                    we credited your account with <a href="">80 free coins!</a> In addition, to help you get noticed we have put a badge on your profile for 7 days to let other users know that you are new. <a href="">Have fun!</a>
                </p>
            </div>
            <div class="img_wrap">
                <img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/welcome-chat-msg-dark.svg" alt="">
            </div>
        </div>
    </div>
                    </div>
                </div>
                <div class="message-input dsp-send-message-container">
                    <div class='dsp-file-input'>
                        <input type='file' name='fileinput' id='fileinput' accept='image/x-png,image/gif,image/jpeg' onchange="dsp_read_url(this)" style="display: none">
                        <div>
                            <button id="close_file" onClick="remove_file()">×</button>
                            <img src='#'>
                        </div>
                    </div>
                    <div class="wrap">
                        <div class="chat-image-selector left-image">
                            <i class="attachment bg-icon" aria-hidden="true" onClick="jQuery('#fileinput').trigger('click')"></i>
                        </div>
                        <div class="center-chat" style="position: relative;">
                            <textarea class='dsp-instant-chat-message-box' id='dsp-instant-chat-message' onkeyup="if(event.keyCode === 13) dsp_send_message()" data-emojiable="true" placeholder="Type Here To Chat..."></textarea>
                        </div>
                        <div class="right-send" style="display: inline-block; width: 18.5%;">
                            <button onClick="dsp_send_message()" style="background: #B7EFAE !important;color:white;">
                                <!--<i class="fa fa-paper-plane" aria-hidden="true"></i>--> SEND
                            </button>
                        </div>
                        <?php $thread_id = get_latest_message($current_user['ID'], $chat_user_id)['thread_id']; ?>
                        <input type="hidden" id="chat-user-id" value="<?php echo isset($chat_user_id) ? $chat_user_id : ''; ?>">
                        <input type="hidden" id="user-list-page" value="1">
                        <input type="hidden" id="user-id" value="<?php echo $user_id ?>">
                        <input type="hidden" id="chat-list-page" value="1">
                        <input type="hidden" id="thread-id" value="<?php echo isset($thread_id) ? $thread_id : '' ?>">

                    </div>
                </div>
            </div>

            <aside class="side_piller right round-disable">
                <div class="col_wrapper">
                    <ul>
                        <li>
                            <div class="cover">
                                <a href="<?php echo get_site_url(); ?>/members">
                                    <i class="bi bi-geo-alt"></i>
                                    <span class="label mobile-label">near you</span>
                                </a>
                            </div>
                        </li>

                        <li class="active">
                            <div class="cover">
                                <?php

                                $count_chat = $wpdb->get_var("SELECT COUNT(DISTINCT(`sender_id`)) FROM `ft_dsp_instant_chat` WHERE receiver_id= '$user_id' AND is_read = '0'");

                                if (!empty($count_chat)) {
                                    echo '<span class="count_chat">' . $count_chat . '</span>';
                                } else {
                                    echo '<span class="count_chat" style="display:none;"></span>';
                                }

                                ?>
                                <a href="<?php echo get_site_url(); ?>/chat">
                                    <i class="bi bi-chat"></i>
                                    <span class="label mobile-label">messages</span>
                                </a>
                            </div>
                        </li>

                        <li>
                            <div class="cover">
                                <a href="<?php echo get_site_url(); ?>/my-profile">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="label mobile-label">my profile</span>
                                    <?php
                                    $credit_updated = $wpdb->get_results("SELECT * FROM `ft_bp_xprofile_field_updated` WHERE `user_id` = " . $user_id);
                                    if ($credit_updated[0]->height == 0 || $credit_updated[0]->body_type == 0 || $credit_updated[0]->hair_color == 0 || $credit_updated[0]->about_me == 0 || $credit_updated[0]->looking_for == 0) {
                                    ?>
                                        <span class="label">FREE CREDITS</span>
                                    <?php } ?>
                                </a>
                            </div>
                        </li>

                        <li class="notification">
                            <div class="cover">
                                <?php
                                if (!empty($new_count)) {
                                    echo '<span class="count_notification notification_count">' . count($new_count) . '</span>';

                                ?>
                                    <a>
                                        <i class="bi bi-bell-fill bell"></i>
                                        <span class="label mobile-label">Notification</span>
                                    </a>

                                <?php } else { ?>
                                    <span class="notification_count"></span>
                                    <a>
                                        <i class="bi bi-bell-fill"></i>
                                        <span class="label mobile-label">Notification</span>
                                    </a>
                                <?php } ?>
                            </div>
                        </li>

                        <li class="boost-button mobile-boost">
                            <div class="cover">
                                <a data-toggle="modal" data-target="#exampleModal" href="javascript:void(0)">
                                    <i class="fa fa-rocket" aria-hidden="true"></i>
                                    <span class="label mobile-label">Boost</span>
                                </a>
                            </div>
                        </li>

                    </ul>
                    <div class="footer_msg">
                        <p class="cred">Credits : <span id="credits"></span></p>
                        <!-- <p class="ava_msg">Available Messages : <span id="avail_msg"></span></p> -->
                    </div>
                </div>
                <!-- Button trigger modal -->
                <button id="credit-btn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#creditspopup">
                    <!-- <button id="credit-btn" type="button" class="btn btn-primary"> -->
                    get credit
                </button>
            </aside>

        </div>


        <style>
            body.instant-chat #creditspopup.modal {
                background-color: rgb(0 0 0 / 50%);
            }

            .msg-unread {
                display: none !important;
            }

            .emoji-wysiwyg-editor.dsp-instant-chat-message-box {
                font-size: 16px;
            }

            .message-text a {
                color: #940594;
            }

            #credit-btn {
                background-color: #ebaaf7 !important;
            }


            #count-messages,
            #count-mychats {
                position: relative;
                top: 0px;
                background: #ebaaf7;
                border-radius: 50%;
                font-size: 12px;
                width: 23px;
                height: 23px;
                padding: 5px;
                right: -10px;
                color: white;

            }

            .tab_button_wrap span {
                display: inline-block;
                verticle-align: middle;
            }

            .count_chat {
                position: relative;
                right: -34px;
                top: -9px;
                background: #ebaaf7;
                border-radius: 50%;
                padding: 0 5px;
                margin: 0 -9px;
            }

            #frame .content .messages ul li.sent .message-text {
                background: #B7EFAE !important;
                color: #333 !important;
            }

            .msg_time {
                display: none;
            }

            .msg-time {
                display: block !important;
            }

            i.emoji-picker-icon.emoji-picker.fa.fa-smile-o {
                display: none !important;
            }

            .replies .message-image {
                float: right;
            }

            .sent .message-image {
                float: left;
            }

            #creditspopup .modal-title {
                text-align: center;
                font-size: 16px;
                line-height: 1.4;
                font-weight: 700;
            }

            .credits_area_wrap ul {
                list-style: none;
                margin: 0px;
                padding: 0px;
            }

            .credit_box_wrap {
                padding: 10px;
                box-shadow: 0px 0px 10px rgb(0 0 0 / 50%);
                display: flex;
                border-radius: 10px;
                justify-content: space-between;
                height: 50px;
                border: 2px solid #fff;
                align-items: center;
                transition: all ease 500ms;
            }

            .credits_area_wrap ul li {
                transition: all ease 500ms;
                margin-bottom: 15px;
            }

            li:hover .credit_box_wrap {
                border: 2px solid #ebaaf7;
            }

            a {
                text-decoration: none;
            }


            h6.offer {
                font-size: 14px;
                line-height: 1;
                text-transform: uppercase;
                color: #ebaaf7;
                margin: 0px;
                display: block;
                font-weight: 700;
                margin-top: -15px;
            }


            .credit_box_wrap h5 {
                text-transform: uppercase;
                color: #000;
                font-size: 18px;
                margin: 0px;
                line-height: 1;
                font-weight: 700;
            }


            .credit_box_wrap h5 span {
                font-size: 13px;
                line-height: 1;
                font-weight: 600;
            }

            span.only_rs {
                display: block;
                text-align: right;
                color: #000000;
                font-size: 18px;
                line-height: 1;
                font-weight: 700;
                padding-bottom: 4px;
            }

            .credit_box_wrap strong {
                font-size: 14px;
                line-height: 1;
                display: block;
                color: #000;
                font-weight: 700;
            }

            .details_wrap {
                text-align: center;
                margin: 20px auto;
                font-weight: 500;
                color: #1f1f1f;
                max-width: 90%;
                font-size: 16px;
                line-height: 1.5;
            }

            .show#creditspopup {
                z-index: 9999;
                opacity: 1;
            }


            #creditspopup .modal-dialog {
                transform: translate(0px, -100px);
            }

            #creditspopup button.close {
                opacity: 1;
                color: #fff;
                margin: 0px;
                padding: 0px 6px 7px !important;
                background-color: rgb(0 0 0 / 30%);
                font-weight: 300;
                line-height: 1;
                right: 4px;
                top: 5px;
                height: 30px;
                width: 30px;
                font-size: 30px;
            }

            #creditspopup .modal-header {
                border-bottom: 0px;
            }



            @media only screen and (max-width: 640px) {
                /* #contacts>ul {
                    height: 160px;
                    overflow: scroll;
                } */

                .instant-chat #frame #sidepanel {
                    height: auto;
                }

                .count_notification {
                    top: -10px;
                }

            }

            .bell {
                display: block;
                font-size: 40px;
                color: #9e9e9e;
                -webkit-animation: ring 4s .7s ease-in-out infinite;
                -webkit-transform-origin: 50% 4px;
                -moz-animation: ring 4s .7s ease-in-out infinite;
                -moz-transform-origin: 50% 4px;
                animation: ring 4s .7s ease-in-out infinite;
                transform-origin: 50% 4px;
            }

            @-webkit-keyframes ring {
                0% {
                    -webkit-transform: rotateZ(0);
                }

                1% {
                    -webkit-transform: rotateZ(30deg);
                }

                3% {
                    -webkit-transform: rotateZ(-28deg);
                }

                5% {
                    -webkit-transform: rotateZ(34deg);
                }

                7% {
                    -webkit-transform: rotateZ(-32deg);
                }

                9% {
                    -webkit-transform: rotateZ(30deg);
                }

                11% {
                    -webkit-transform: rotateZ(-28deg);
                }

                13% {
                    -webkit-transform: rotateZ(26deg);
                }

                15% {
                    -webkit-transform: rotateZ(-24deg);
                }

                17% {
                    -webkit-transform: rotateZ(22deg);
                }

                19% {
                    -webkit-transform: rotateZ(-20deg);
                }

                21% {
                    -webkit-transform: rotateZ(18deg);
                }

                23% {
                    -webkit-transform: rotateZ(-16deg);
                }

                25% {
                    -webkit-transform: rotateZ(14deg);
                }

                27% {
                    -webkit-transform: rotateZ(-12deg);
                }

                29% {
                    -webkit-transform: rotateZ(10deg);
                }

                31% {
                    -webkit-transform: rotateZ(-8deg);
                }

                33% {
                    -webkit-transform: rotateZ(6deg);
                }

                35% {
                    -webkit-transform: rotateZ(-4deg);
                }

                37% {
                    -webkit-transform: rotateZ(2deg);
                }

                39% {
                    -webkit-transform: rotateZ(-1deg);
                }

                41% {
                    -webkit-transform: rotateZ(1deg);
                }

                43% {
                    -webkit-transform: rotateZ(0);
                }

                100% {
                    -webkit-transform: rotateZ(0);
                }
            }

            @-moz-keyframes ring {
                0% {
                    -moz-transform: rotate(0);
                }

                1% {
                    -moz-transform: rotate(30deg);
                }

                3% {
                    -moz-transform: rotate(-28deg);
                }

                5% {
                    -moz-transform: rotate(34deg);
                }

                7% {
                    -moz-transform: rotate(-32deg);
                }

                9% {
                    -moz-transform: rotate(30deg);
                }

                11% {
                    -moz-transform: rotate(-28deg);
                }

                13% {
                    -moz-transform: rotate(26deg);
                }

                15% {
                    -moz-transform: rotate(-24deg);
                }

                17% {
                    -moz-transform: rotate(22deg);
                }

                19% {
                    -moz-transform: rotate(-20deg);
                }

                21% {
                    -moz-transform: rotate(18deg);
                }

                23% {
                    -moz-transform: rotate(-16deg);
                }

                25% {
                    -moz-transform: rotate(14deg);
                }

                27% {
                    -moz-transform: rotate(-12deg);
                }

                29% {
                    -moz-transform: rotate(10deg);
                }

                31% {
                    -moz-transform: rotate(-8deg);
                }

                33% {
                    -moz-transform: rotate(6deg);
                }

                35% {
                    -moz-transform: rotate(-4deg);
                }

                37% {
                    -moz-transform: rotate(2deg);
                }

                39% {
                    -moz-transform: rotate(-1deg);
                }

                41% {
                    -moz-transform: rotate(1deg);
                }

                43% {
                    -moz-transform: rotate(0);
                }

                100% {
                    -moz-transform: rotate(0);
                }
            }

            @keyframes ring {
                0% {
                    transform: rotate(0);
                }

                1% {
                    transform: rotate(30deg);
                }

                3% {
                    transform: rotate(-28deg);
                }

                5% {
                    transform: rotate(34deg);
                }

                7% {
                    transform: rotate(-32deg);
                }

                9% {
                    transform: rotate(30deg);
                }

                11% {
                    transform: rotate(-28deg);
                }

                13% {
                    transform: rotate(26deg);
                }

                15% {
                    transform: rotate(-24deg);
                }

                17% {
                    transform: rotate(22deg);
                }

                19% {
                    transform: rotate(-20deg);
                }

                21% {
                    transform: rotate(18deg);
                }

                23% {
                    transform: rotate(-16deg);
                }

                25% {
                    transform: rotate(14deg);
                }

                27% {
                    transform: rotate(-12deg);
                }

                29% {
                    transform: rotate(10deg);
                }

                31% {
                    transform: rotate(-8deg);
                }

                33% {
                    transform: rotate(6deg);
                }

                35% {
                    transform: rotate(-4deg);
                }

                37% {
                    transform: rotate(2deg);
                }

                39% {
                    transform: rotate(-1deg);
                }

                41% {
                    transform: rotate(1deg);
                }

                43% {
                    transform: rotate(0);
                }

                100% {
                    transform: rotate(0);
                }
            }


            .modal-backdrop.show {
                display: none;
            }

            .modal {
                background-color: rgba(0, 0, 0, 0.5);
            }

            #exampleModal .btn-primary {
                color: #fff;
                background-color: #ebaaf7;
                border-color: #ebaaf7;
            }

            #exampleModal .close {
                color: #bbb;
            }

            #exampleModal .modal-body {
                text-align: center;
            }

            #exampleModal .modal-content {
                margin: auto;
                display: block;
                width: 86%;
                max-width: 700px;
            }

            #exampleModal .modal-body {
                position: relative;
                padding: 15px;
            }

            #exampleModal.show {
                z-index: 9999;
                opacity: 1;
            }

            #exampleModal .close {
                top: 0px;
                right: 0px;
            }

            .mobile-boost {
                display: none;
            }

            @media only screen and (max-width: 640px) {
                .mobile-boost {
                    display: block !important;
                }

                .desktop-boost {
                    display: none !important;
                }
            }
        </style>

        <!-- credits Modal -->
        <div class="modal fade" id="creditspopup" tabindex="-1" role="dialog" aria-labelledby="creditspopupLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="creditspopupLabel">Buy Chat Credits to message users in your area.</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_credit">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="credits_area_wrap">
                            <ul>


                                <?php
                                global $wpdb;
                                $sql_query      = "SELECT * FROM `ft_dsp_credits_plan` ORDER BY credits_plan_id";
                                //$conn       = connect_database();
                                $result     = $wpdb->get_results($sql_query);

                                function cutAfterDot($number, $afterDot = 2)
                                {
                                    $a = $number * pow(10, $afterDot);
                                    $b = floor($a);
                                    $c = pow(10, $afterDot);
                                    return $b / $c;
                                }

                                $ck_first_purchase = $wpdb->get_var("SELECT `first_credit_purchase` FROM `ft_dsp_credits_purchase_history` WHERE `user_id` = '$user_id' ORDER BY `credit_purchase_id` DESC LIMIT 1");

                                $currency = "$";
                                foreach ($result as $plan) {

                                    $planname = str_replace(' ', '', $plan->plan_name);
                                    $_SESSION[$planname] = rand(1000, 9999);
                                    $plan_name = base64_encode($_SESSION[$planname]);

                                    if ($plan->credits_plan_id == 1) {

                                        if ($ck_first_purchase == '0' || $ck_first_purchase == '') {
                                ?>

                                            <li class="offer">

                                                <a href="javascript:void(0)" class="credit-plans" data-planid="<?php echo $plan_name; ?>" data-amount="<?php echo $plan->amount; ?>" data-currency="<?php echo $currency; ?>" data-plan="<?php echo $plan->plan_name; ?>">
                                                    <div class="credit_box_wrap">
                                                        <h5><?php echo $plan->plan_name; ?></h5>
                                                        <h6 class="offer">limited offer, try now!</h6>
                                                        <span class="only_rs"><?php echo $currency; ?><?php echo $plan->amount; ?></span>
                                                    </div>
                                                </a>
                                            </li>

                                        <?php
                                        }
                                    } else {

                                        ?>

                                        <li>
                                            <a href="javascript:void(0)" class="credit-plans" data-planid="<?php echo $plan_name; ?>" data-amount="<?php echo $plan->amount; ?>" data-currency="<?php echo $currency; ?>" data-plan="<?php echo $plan->plan_name; ?>">
                                                <div class="credit_box_wrap">
                                                    <h5><?php echo $plan->plan_name; ?></h5>
                                                    <div class="right_rs_wrap">
                                                        <span class="only_rs"><?php echo $currency; ?><?php echo $plan->amount; ?></span>
                                                        <strong><?php echo $currency; ?><?php $a = $plan->amount / $plan->no_of_credits;
                                                                                        echo cutAfterDot($a, 2); ?>
                                                            / message</strong>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                <?php
                                    }
                                }
                                ?>

                            </ul>
                        </div>



                        <div class="details_wrap">
                            <p>We care about your private life and respect your privacy.
                                Any charges made on your credit card will appear under:
                                ‘vtsup.com*PWR Networ’</p>

                            <p>This is not subscription. <br>
                                Your credit card will not be re-billed</p>

                            <p><strong style="color:#000;">Need assistance? send us an email:</strong> <br>
                                <a href="mailto:billing@funtimeflirt.com" target="_blank">billing@funtimeflirt.com</a> <br>
                                By clicking "secure purchase" you agree with our <br> <a href="https://funtimeflirt.com/agreement" target="_blank">term & conditions</a>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- credits Modal -->


        <!-- The Modal -->
        <div id="myModal" class="modal">

            <!-- The Close Button -->
            <span class="close" onClick="close_modal()">&times;</span>

            <!-- Modal Content (The Image) -->
            <img class="modal-content" id="img01">

        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 700;font-size: 20px;text-align:center;">Super Boost!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Boost now to get seen by ALL the girls in your area!</p><br>
                        <button type="button" class="btn btn-primary" onClick="Boost_messages()">$4.99</button>
                    </div>
                </div>
            </div>
        </div>

        <footer></footer>

        <script>
            function show_image_preview(image) {
                jQuery('#myModal').css('display', 'block');
                jQuery('#img01').attr('src', image);
            }

            function close_modal() {
                jQuery('#myModal').css('display', 'none');
            }
        </script>




        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/public/js/wpdating-chat-page.js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/public/js/wpdating-common.js">
        </script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/public/js/wpdating-instant-chat-public.js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/lib/emoji-picker/lib/js/config.js" id="wpdating-instant-chat2-js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/lib/emoji-picker/lib/js/util.js" id="wpdating-instant-chat3-js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/lib/emoji-picker/lib/js/jquery.emojiarea.js" id="wpdating-instant-chat4-js"></script>
        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/lib/emoji-picker/lib/js/emoji-picker.js" id="wpdating-instant-chat5-js"></script>

        <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/public/js/wpdating-emoji-handler.js" id="wpdating-instant-chat6-js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js">
        </script>
        <script>
            jQuery('#credit-btn').click(function() {
                jQuery('#creditspopup').css('display', 'block');
                log_credit_button_click();
                // var userid = '<?php //echo $user_id; 
                                    ?>';
                // jQuery.ajax({
                //     type : "POST",
                //     dataType : "text",
                //     url : "/wp-admin/admin-ajax.php",
                //     data : {user_id:userid,action: "give_free_twenty_credits"},
                //     success: function(response) {
                //         alert(response);
                //         window.location.reload();    
                //     }
                // });

            });

            jQuery('#close_credit').click(function() {
                jQuery('#creditspopup').css('display', 'none');
            });
        </script>
        <script>
            jQuery("li.notification").click(function() {
                jQuery(".notification-columns").toggleClass("show_ntfy");
            });

            jQuery(".dsp-user-list-container").click(function() {
                jQuery(".notification-columns").removeClass("show_ntfy");
            });

            jQuery(".dsp-chat-box").click(function() {
                jQuery(".notification-columns").removeClass("show_ntfy");
            });


            jQuery('.credit-plans').click(function() {
                var amount = jQuery(this).data('amount');
                var currency = jQuery(this).data('currency');
                var plan = jQuery(this).data('plan');
                var plan_id = jQuery(this).data('planid');
                var site_url = window.location.href;

                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "<?php echo get_site_url(); ?>/wp-content/themes/WPDATING-child/rk_pay_credits.php",
                    data: {
                        'amount': amount,
                        'currency': currency,
                        'plan': plan,
                        'plan_id': plan_id,
                        'site_url': site_url
                    },
                    success: function(response) {
                        //if(response != ""){
                        //console.log(response.responseText);
                        log_attempt_time(amount);
                        window.location.href = response.responseText;
                        //}
                    },
                    error: function(response) {
                        //console.log(response.responseText);
                        log_attempt_time(amount);
                        window.location.href = response.responseText;
                    }
                });

            });


            jQuery('.notification').click(function() {
                //var userid = '<?php //echo $user_id; 
                                ?>';
                // jQuery.ajax({
                //     type: "POST",
                //     dataType: "json",
                //     url: "/wp-admin/admin-ajax.php",
                //     data: {
                //         userid: userid,
                //         action: "update_notifications_count"
                //     },
                //     success: function (response) {

                //     }
                // });
            });

            /*   setTimeout(function(){

                   jQuery.each($('.emoji'), function (i, checkbox) {
                       var a = $(this).data('ids');

                       var b = jQuery("."+a+"").data('id');
                       if(b){
                           jQuery(this).html(jQuery.emojiarea.createIcon(jQuery.emojiarea.icons[b]));;
                       }
                   });

               }, 100);*/

            /*       setTimeout(function(){

                    jQuery.each($('.emoji'), function (i, checkbox) {
                        var a = $(this).data('ids');

                        var b = jQuery("."+a+"").data('id');

                        var count = (b.match(/:/g) || []).length;
                        emoarray = b.split(':');
                        if(count > 2){
                            for(i=count;i>=1;i--){
                                if(i%2 != 0){
                                    var c = ":"+emoarray[i]+":";

                                    var d = jQuery.emojiarea.createIcon(jQuery.emojiarea.icons[c]);

                                    jQuery('img').attr('alt','');
                                    jQuery(this).html(function(index,text){
                                        return text.replace(c,d);
                                    });

                                }
                            }
                        }else{
                            jQuery(this).html('');
                            jQuery(this).append(jQuery.emojiarea.createIcon(jQuery.emojiarea.icons[b]));
                        }

                    });

                }, 100);*/


            $(document).ready(function() {

                get_msg_local_time();

                check_user_is_online();

                setInterval(() => {
                    check_user_is_online();
                }, 100000);

                var ask = getParameterByName('ask');
                var user_id = getParameterByName('id');
                var pvt_user = localStorage.getItem("pvt-user");
                var location_user = localStorage.getItem("location-user");
                var url = window.location.href;
                var ck_active = false;
                var userrow = 'dsp-user-row-' + user_id;
                // jQuery('.tab2').click();
                /*jQuery.each(jQuery('.dsp-user-row'), function (i, checkbox){

                    if(!jQuery(this).hasClass("unread")){
                        a = jQuery(this).attr('id');
                        jQuery("#"+a+"").click();
                        return false;
                    }

                });*/
                //var unread = 0;
                var ck_msg_unread = 0;
                var ck_my_chat_unread = 0;
                jQuery.each(jQuery('.dsp-user-row'), function(i, checkbox) {

                    if (jQuery(this).hasClass("unread")) {
                        a = jQuery(this).attr('id');
                        jQuery("#" + a + "").hide();
                        //console.log("new="+a);
                        if (jQuery('#' + a + ' p').hasClass("unread")) {
                            ck_msg_unread = ck_msg_unread + 1;
                        }
                    } else {
                        a = jQuery(this).attr('id');
                        jQuery("#" + a + "").show();
                        if (jQuery('#' + a + ' p').hasClass("unread")) {
                            ck_my_chat_unread = ck_my_chat_unread + 1;
                        }
                    }

                    if (a == userrow) {
                        ck_active = true;
                    }

                });

                if (ck_msg_unread > 0) {
                    jQuery('#count-messages').html(ck_msg_unread);
                } else {
                    jQuery('#count-messages').hide();
                }

                if (ck_my_chat_unread > 0) {
                    jQuery('#count-mychats').html(ck_my_chat_unread);
                } else {
                    jQuery('#count-mychats').hide();
                }

                if (ck_active) {
                    //console.log("tab1");
                    jQuery('.tab1').click();
                    if (jQuery('#' + userrow + '').hasClass("unread")) {
                        jQuery('.tab1').click();
                    } else {
                        jQuery('.tab2').click();
                    }
                } else {
                    //console.log("tab2");
                    jQuery('.tab2').click();
                }

                $('.btn-msg-1').on('click', function() {

                    if (!$('.tab1').hasClass('active')) {
                        var tab1_click = 0;
                    }

                    $('.tab2').removeClass('active');
                    $('.tab1').addClass('active');

                    //var unread1 = 0;

                    jQuery.each(jQuery('.dsp-user-row'), function(i, checkbox) {

                        if (!jQuery(this).hasClass("unread")) {
                            a = jQuery(this).attr('id');
                            jQuery("#" + a + "").hide();
                        } else {
                            a = jQuery(this).attr('id');
                            jQuery("#" + a + "").show();

                            if (tab1_click == 0) {
                                if (jQuery(window).width() > 640) {
                                    jQuery("#" + a + "").click();
                                    tab1_click = tab1_click + 1;
                                }
                            }
                            //  unread1 = unread1 + 1;
                        }

                    });

                });



                $('.btn-msg-2').on('click', function() {

                    //console.log("tab2 click");
                    if (!$('.tab2').hasClass('active')) {
                        var tab2_click = 0;
                    }

                    $('.tab1').removeClass('active');
                    $('.tab2').addClass('active');

                    //var unread2 = 0;
                    var tab2_click = 0;
                    jQuery.each(jQuery('.dsp-user-row'), function(i, checkbox) {

                        if (jQuery(this).hasClass("unread")) {
                            a = jQuery(this).attr('id');
                            jQuery("#" + a + "").hide();
                            //unread2 = unread2 + 1;
                        } else {
                            a = jQuery(this).attr('id');
                            jQuery("#" + a + "").show();

                            if (tab2_click == 0) {
                                if (jQuery(window).width() > 640) {
                                    jQuery("#" + a + "").click();
                                    tab2_click = tab2_click + 1;
                                }
                            }
                        }

                    });


                });



                function getParameterByName(name, url = window.location.href) {
                    name = name.replace(/[\[\]]/g, '\\$&');
                    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                        results = regex.exec(url);
                    if (!results) return null;
                    if (!results[2]) return '';
                    return decodeURIComponent(results[2].replace(/\+/g, ' '));
                }


                if ((ask == "pvtimg") && (pvt_user != user_id)) {
                    jQuery('#dsp-instant-chat-message').val("Hi there, can I get a private picture?");
                    jQuery('.dsp-instant-chat-message-box').html("Hi there, can I get a private picture?");
                    if (jQuery(window).width() > 640) {
                        jQuery('.dsp-instant-chat-message-box').css('font-weight', '900');
                        jQuery('.dsp-instant-chat-message-box').css('font-size', '22px');
                    } else {
                        jQuery('.dsp-instant-chat-message-box').css('font-weight', '700');
                        jQuery('.dsp-instant-chat-message-box').css('font-size', '14px');
                    }
                    localStorage.setItem("pvt-user", user_id);

                }

                if ((ask == "location") && (location_user != user_id)) {
                    jQuery('#dsp-instant-chat-message').val("Hi there! I noticed you didn't mention your city, can you tell me where you live?");
                    jQuery('.dsp-instant-chat-message-box').html("Hi there! I noticed you didn't mention your city, can you tell me where you live?");
                    if (jQuery(window).width() > 640) {
                        jQuery('.dsp-instant-chat-message-box').css('font-weight', '900');
                        jQuery('.dsp-instant-chat-message-box').css('font-size', '22px');
                    } else {
                        jQuery('.dsp-instant-chat-message-box').css('font-weight', '700');
                        jQuery('.dsp-instant-chat-message-box').css('font-size', '14px');
                    }
                    localStorage.setItem("location-user", user_id);
                }


                var sale_id = getParameterByName('saleID');
                var user_id = <?php echo $user_id; ?>;
                var payment_method = getParameterByName('paymentMethod');
                var amount = getParameterByName('priceAmount');

                if ((sale_id != null) && (payment_method != null) && (amount != null)) {
                    //console.log(sale_id);

                    let data_check = {
                        'saleID': sale_id
                    };
                    jQuery.get("<?php echo get_site_url(); ?>/wp-content/themes/WPDATING-child/check_pay_cred_status.php", data_check, function(res) {
                        if (res == "APPROVED") {

                            let data = {
                                'user_id': user_id,
                                'sale_id': sale_id,
                                'payment_method': payment_method,
                                'amount': amount,
                                'action': "check_payment_status",
                            };
                            jQuery.get('https://funtimeflirt.com/wp-admin/admin-ajax.php', data, function(response) {
                                if (response != "") {
                                    alert(response);
                                    window.location.href = "https://funtimeflirt.com/chat";
                                }

                            });

                        }

                    });


                    // jQuery.ajax({
                    //     type: "POST",
                    //     dataType: "json",
                    //     url: "/wp-admin/admin-ajax.php",
                    //     data: {
                    //         user_id: user_id,
                    //         sale_id: sale_id,
                    //         payment_method: payment_method,
                    //         amount: amount,
                    //         action: "check_payment_status"
                    //     },
                    //     success: function (response) {
                    //         console.log(response);
                    //     }
                    // });
                }

                function reset_form_element(e) {
                    e.wrap('<form>').parent('form').trigger('reset');
                    e.unwrap();
                }

            });
        </script>


        <script type="text/javascript">
            $('#contacts .dsp-user-list li').on('click', function() {
                $('.dsp-chat-box.content').addClass('showing');
                $('#sidepanel').addClass('hiding');

            });

            $(document).on('click', '.left_arrow_msg', function() {
                $('.dsp-chat-box.content').removeClass('showing');
                $('#sidepanel').removeClass('hiding');
            });

            $(document).on('click', '.left_msg', function() {
                $('.dsp-chat-box.content').removeClass('showing');
                $('#sidepanel').removeClass('hiding');
            });

            jQuery('.attachment, #close_file').click(function() {
                reset_form_element(jQuery('#fileinput'));
            });

            if (jQuery.trim(jQuery('.meta_user_name').html()) == "Matradee") {
                //console.log("testing");
                jQuery('.tab1').click();
            }

            function removeURLParameter(url, parameter) {
                //prefer to use l.search if you have a location/link object
                var urlparts = url.split('?');
                if (urlparts.length >= 2) {

                    var prefix = encodeURIComponent(parameter) + '=';
                    var pars = urlparts[1].split(/[&;]/g);

                    //reverse iteration as may be destructive
                    for (var i = pars.length; i-- > 0;) {
                        //idiom for string.startsWith
                        if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                            pars.splice(i, 1);
                        }
                    }

                    url = urlparts[0] + '?' + pars.join('&');
                    return url;
                } else {
                    return url;
                }
            }

            jQuery('.notification-list').click(function() {
                var notification_id = jQuery(this).attr('id');
                let data = {
                    'notification_id': notification_id,
                    'action': "update_notification_status",
                };
                jQuery.get('https://funtimeflirt.com/wp-admin/admin-ajax.php', data, function(response) {
                    //console.log("update notificatio");
                    jQuery(this).css('background-color', '#fff');
                    jQuery(this).css('cursor', 'pointer');
                });
            });

            jQuery('.notification-list .user_info a').click(function() {
                var url = jQuery(this).data('url');
                var notification_id = jQuery(this).attr('id');
                let data = {
                    'notification_id': notification_id,
                    'action': "update_notification_status",
                };
                jQuery.get('https://funtimeflirt.com/wp-admin/admin-ajax.php', data, function(response) {
                    //console.log("update notificatio new");
                    if (response !== '') {
                        jQuery(this).css('background-color', '#fff');
                        jQuery(this).css('cursor', 'pointer');
                        //console.log("url="+url);
                        window.location.href = url;
                    }

                });

            });


            jQuery('.profile-click').click(function() {

                if (jQuery(window).width() > 640) {
                    var chatter_id = jQuery(this).data('chatter');
                    dsp_open_chat_box(chatter_id);
                    //window.location.href = "<?php //echo site_url()."/profile/?id=" 
                                                ?>"+chatter_id;
                }

            });

            jQuery('#clear_all_notification').click(function() {
                var userid = '<?php echo $user_id; ?>';
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        userid: userid,
                        action: "clear_all_notification"
                    },
                    success: function(response) {
                        jQuery('#notifications_listing').html('<li><div class="padding_cover fix_include"><div class="user_info">You have no any new notification.</div></div></li>');
                    }
                });
            });

            function getLocaltime(mydate) {
                mydatestr = mydate + ' UTC';
                var theDate = new Date(mydatestr);
                var new_time = theDate.getHours() + ":" + (theDate.getMinutes() < 10 ? '0' : '') + theDate.getMinutes();
                return new_time;
            }

            window.addEventListener("beforeunload", function(e) {
                var userid = '<?php echo $user_id; ?>';
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        user_id: userid,
                        action: "offline_user_on_tab_close"
                    },
                    success: function(response) {

                    }
                });
                for (var i = 0; i < 500000000; i++) {}
                return undefined;

            });

            function check_user_is_online() {
                var userid = '<?php echo $user_id; ?>';
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        user_id: userid,
                        action: "check_user_online_status"
                    },
                    success: function(response) {

                    }
                });
            }

            function Boost_messages() {
                var userid = '<?php echo $user_id; ?>';
                /*jQuery.ajax({
                    type : "POST",
                    dataType : "text",
                    url : "boost_message.php",
                    data : {user_id:userid},
                    success: function(response) {
                    
                    }
                });*/

                var amount = 4.99;
                var currency = '$';
                var plan_id = 'boost';
                var plan = 'boost';
                var site_url = window.location.href;
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "<?php echo get_site_url(); ?>/wp-content/themes/WPDATING-child/boost_pay.php",
                    data: {
                        'amount': amount,
                        'currency': currency,
                        'plan': plan,
                        'plan_id': plan_id,
                        'site_url': site_url
                    },
                    success: function(response) {
                        //if(response != ""){
                        console.log(response.responseText);
                        log_attempt_time(amount);
                        window.location.href = response.responseText;
                        //}
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        log_attempt_time(amount);
                        window.location.href = response.responseText;
                    }
                });
            }

            function log_attempt_time(amount) {
                var userid = '<?php echo $user_id; ?>';
                var planamount = amount;
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        user_id: userid,
                        plan_amount: planamount,
                        action: "log_attempt_time"
                    },
                    success: function(response) {

                    }
                });
            }

            function log_credit_button_click() {
                var userid = '<?php echo $user_id; ?>';
                jQuery.ajax({
                    type: "POST",
                    dataType: "text",
                    url: "/wp-admin/admin-ajax.php",
                    data: {
                        user_id: userid,
                        action: "log_credit_button_click"
                    },
                    success: function(response) {

                    }
                });
            }
        </script>
        <?php //} 
        ?>
</body>

</html>