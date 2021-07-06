<?php
/* Template Name: My Profile */

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
$path = explode('wp-content', __DIR__);
include_once($path[0] . 'wp-load.php');
global $current_user;

$user_id = $current_user->ID;
$credit_count = $wpdb->get_results("SELECT * FROM `ft_dsp_credits` LIMIT 1");

$credit_updated = $wpdb->get_results("SELECT * FROM `ft_bp_xprofile_field_updated` WHERE `user_id` = " . $user_id);
/*$profile_id = $_REQUEST['id'];
$member_args = array(
    'include' => $profile_id
    // 'include' =>  my_custom_ids( 'IsMember', 'Yes' )
);*/
include_once('custome_header.php');
?>

<link
    type="text/css"
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css"/>
<link
    rel="stylesheet"
    id="wpdating-instant-chat1-css"
    href="https://funtimeflirt.com/wp-content/plugins/wpdating-instant-chat/public/css/wpdating-image-modal.css"
    type="text/css"
    media="all">


    <link type="text/css" rel="stylesheet" href="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/popup-style.css"/>
<link
<?php if (bp_has_profile('user_id=' . $user_id)) : ?>
<?php if (bp_has_members('type=alphabetical&include=' . $user_id)) : ?>
<?php while (bp_members()) : bp_the_member(); ?>

<?php if (bp_get_member_user_id() == $user_id) : ?>
<div id="members-area" class="account-page" data-cnt="all-data-fields">
    <div class="container">
        <div class="header-block">
            <div
                class="background"
                data-my-profile-background=""
                style="background-image: url('<?php echo get_template_directory_uri(); ?>/members/images/bg/texas.jpg');">
                <div class="background2"></div>
            </div>
            <div class="header-information-wrapper">
                <div class="header-information">
                    <div class="left uploadProfile">
                        <input
                            id="uploadProfile"
                            data-link="upload-profile"
                            name="UploadProfile"
                            type="file"
                            style="display:none;">

                        <?php /*?>$profile_id = get_user_meta( $user_id, 'ft_user_avatar', true );
                            if($profile_id == ''){ ?>
                        <img
                            src="https://funtimeflirt.com/wp-content/uploads/profile_photos/default_avtar_four_credits.png"
                            width="50"
                            height="50"
                            alt="john john"
                            class="avatar avatar-50 wp-user-avatar wp-user-avatar-50 alignnone photo">
                    <?php
							}else{
								bp_member_avatar(); 
							}<?php */ ?>

                        <?php bp_member_avatar(); ?>
                    </div>
                    <div class="right">
                        <div class="username">
                            <?php if (is_numeric(bp_get_member_profile_data('field=Name')) && strlen(bp_get_member_profile_data('field=Name')) == 6) { ?>
                            <input
                                class="rmv_username"
                                style="width: 80%;"
                                id="my-username"
                                type="text"
                                value="<?php bp_member_profile_data('field=Name'); ?>">
                            <i
                                class="fa fa-save rmv_username"
                                id="save-username"
                                aria-hidden="true"
                                style="display:none;cursor: pointer;margin-left: 10px;color: white;position: relative;top: 10px;"></i>
                            <?php } ?>
                            <span id="username-txt">
                                <?php bp_member_profile_data('field=Name'); ?></span>
                            <?php if (is_numeric(bp_get_member_profile_data('field=Name')) && strlen(bp_get_member_profile_data('field=Name')) == 6) { ?>
                            <i
                                class="fa fa-edit rmv_username"
                                id="edit-username"
                                aria-hidden="true"
                                style="cursor: pointer;margin-left: 10px;color: white;position: relative;"></i>
                            <span
                                class="four_credit_note"
                                id="username-credit-note"
                                style="padding-top: 0px;padding-bottom: 0px;color: black;text-shadow: none;">
                                <div class="icon_wrap">
                                    <img
                                        src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/images/imgs/coin.png">
                                </div>
                                Change your username for 1 FREE CREDIT.
                            </span>
                            <div
                                class="alert-success success_username_msg"
                                role="alert"
                                style="display:none;text-shadow: none;">
                                Username save successfully.
                            </div>
                            <?php } ?>
                        </div>
                        <div class="age"><?php bp_member_profile_data('field=Age'); ?>
                            <span data-geo="region"><?php //bp_member_profile_data('field=State'); 
                                                                                                                                ?></span></div>
                        <div class="living-place">
                            <?php
                                            $ip = $_SERVER['REMOTE_ADDR'];
                                            $result2 = json_decode(file_get_contents('https://www.iplocate.io/api/lookup/' . $ip));
                                            update_user_meta($user_id, 'user_state', $result2->subdivision);
                                            update_user_meta($user_id, 'user_ip', $ip);
                                            /*$ip = $_SERVER['REMOTE_ADDR']; 
								$result = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/json'));
								$aus_state = array('South Wales','Queensland','South Australia','Tasmania','Victoria','Western Australia');
								$random_state=array_rand($aus_state,1);*/
                                            ?>
                            <input
                                class="living-place-input"
                                type="text"
                                autocomplete="off"
                                message="City"
                                name="city"
                                id="city"
                                data-field="city"
                                placeholder="Enter a city"
                                readonly="readonly"
                                value="<?php //if($result->region != NULL && $result->region != '' && in_array($result->region,$aus_state) ){ echo $result->region; }else{ echo $aus_state[$random_state]; } echo $_SESSION['location'];
                                                                                                                                                                                                                echo $result2->subdivision; ?>">
                            <div data-cnt="searchCity" style="display:none;" class="location-dropdown">
                                <div class="location-container">
                                    <ul class="location-suggest" data-result="searchCity"></ul>
                                </div>
                            </div>
                        </div>
                        <!--<a class="button" data-action="save" data-hold="saveProfile"
                        id="save_city"><i class="far fa-save"></i></a>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content clearfix">
            <div data-msg="success" class="success">
                <div class="alert alert-success success_profile_photo" role="alert">
                    Your profile picture under review.
                </div>

                <div class="alert alert-success success_city_msg" role="alert">
                    Profile save successfully.
                </div>
            </div>
            <div data-msg="error" class="error"></div>
            <div class="left-side">
                <!---photos -->
                <div class="content-block photo-block">
                    <div class="content-header clearfix">
                        <h2 class="content-title">My photos:</h2>
                        <?php /*?><?php
								if($credit_updated[0]->profile_photo == 0){
							?>
                        <span class="four_credit_note" id="profile-photo-credit-note">
                            <div class="icon_wrap">
                                <img
                                    src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/images/imgs/coin.png"/>
                            </div>
                            Add a Photo and get
                            <?php echo $credit_count[0]->profile_photo_credit; ?>
                            FREE CREDITS.</span>
                        <?php } ?><?php */ ?>
                    </div>
                    <div class="my-pictures clearfix" data-link="attachPictures">
                        <div class="my-picture-wrapper">
                            <div class="my-picture upload">
                                <div class="upload-photo">

                                    <input
                                        id="uploadImage"
                                        data-link="upload-input"
                                        name="photoUpload-file"
                                        type="file">
                                    <label for="uploadImage" class="upload-image-label" data-link="upload-button">
                                        <i class="fas fa-plus"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                                        $additional_photo = get_user_meta($user_id, 'additional_photos', true);
                                        $additional_photo2 = explode(",", $additional_photo);
                                        $additional_photo = array_slice($additional_photo2, -3, 3, true);
                                        if ($additional_photo) {
                                            foreach ($additional_photo as $photos) {
                                                if ($photos) {
                                        ?>
                        <div class="popup-gallery">
                            <a
                                href="https://funtimeflirt.com/wp-content/uploads/profile_photos/<?php echo $photos; ?>"
                                class="image">
                                <img
                                    class="profile_photos"
                                    src="https://funtimeflirt.com/wp-content/uploads/profile_photos/<?php echo $photos; ?>"></a>
                        </div>

                        <?php }
                                            }
                                        } ?>
                    </div>
                </div>
                <div class="content-block personal-information-block">
                    <div class="content-header clearfix">
                        <h2 class="content-title">Personal information:</h2>
                    </div>
                    <!-- gender -->
                    <?php
                                    if ($credit_updated[0]->height == 0 || $credit_updated[0]->body_type == 0 || $credit_updated[0]->hair_color == 0) {
                                    ?>
                    <span class="four_credit_note" id="profile-detail-credit-note">
                        <div class="icon_wrap">
                            <img
                                src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/images/imgs/coin.png"/>
                        </div>
                        Enter your details for
                        <?php echo $credit_count[0]->profile_detail_credit; ?>
                        FREE CREDIT.
                    </span>
                    <?php } ?>

                    <div class="personal-information title">Gender:</div>
                    <div class="personal-information select-option">
                        <select class="style-two" data-field="gender" required="" id="Gender">
                            <option
                                value="Male"
                                <?php if (bp_get_member_profile_data('field=Gender') == 'Male') { echo 'selected'; } ?>>Man</option>
                            <option
                                value="Female"
                                <?php if (bp_get_member_profile_data('field=Gender') == 'Female') { echo 'selected'; } ?>>Woman</option>
                        </select>
                    </div>
                    <!-- date of birth -->
                    <div class="personal-information title">Date of birth:</div>
                    <div class="personal-information">
                        <input
                            data-field="dateOfBirth"
                            name="birthdateField"
                            type="date"
                            required="required"
                            value="<?php bp_member_profile_data('field=Birth Date'); ?>"
                            id="BirthDate">
                    </div>
                    <div class="personal-information title">Height:</div>
                    <div class="personal-information select-option">
                        <select
                            class="style-two"
                            name="length"
                            id="Height"
                            data-field="lengthChoice"
                            data-required="false">
                            <option value="">
                                -- Please select --</option>
                            <option
                                value='Shorter than 4&#39; 7"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'Shorter than 4\' 7"') { echo 'selected'; } ?>>Shorter than 4' 7"</option>
                            <option
                                value='From 4&#39; 7" to 4&#39; 11"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 4\' 7" to 4\' 11"') { echo 'selected'; } ?>>From 4' 7" to 4' 11"</option>
                            <option
                                value='From 4&#39; 11" to 5&#39; 3"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 4\' 11" to 5\' 3"') { echo 'selected'; } ?>>From 4' 11" to 5' 3"</option>
                            <option
                                value='From 5&#39; 3" to 5&#39; 7"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 5\' 3" to 5\' 7"') { echo 'selected'; } ?>>From 5' 3" to 5' 7"</option>
                            <option
                                value='From 5&#39; 7" to 5&#39; 11"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 5\' 7" to 5\' 11"') { echo 'selected'; } ?>>From 5' 7" to 5' 11"</option>
                            <option
                                value='From 5&#39; 11" to 6&#39; 3"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 5\' 11" to 6\' 3"') { echo 'selected'; } ?>>From 5' 11" to 6' 3"</option>
                            <option
                                value='From 6&#39; 3" to 6&#39; 7"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'From 6\' 3" to 6\' 7"') { echo 'selected'; } ?>>From 6' 3" to 6' 7"</option>
                            <option
                                value='Taller than 6&#39; 7"'
                                <?php if (bp_get_member_profile_data('field=Height') == 'Taller than 6\' 7"') { echo 'selected'; } ?>>Taller than 6' 7"</option>
                        </select>
                    </div>
                    <div class="personal-information title">Body type:</div>
                    <div class="personal-information select-option">
                        <select
                            name="body_type"
                            id="body_type"
                            data-field="build"
                            data-required="false">
                            <option value="">
                                -- Please select --</option>
                            <option
                                value="Athletic"
                                <?php if (bp_get_member_profile_data('field=Body Type') == 'Athletic') { echo 'selected'; } ?>>Athletic</option>
                            <option
                                value="Average"
                                <?php if (bp_get_member_profile_data('field=Body Type') == 'Average') { echo 'selected'; } ?>>Average</option>
                            <option
                                value="Slender"
                                <?php if (bp_get_member_profile_data('field=Body Type') == 'Slender') { echo 'selected'; } ?>>Slender</option>
                            <option
                                value="A little extra"
                                <?php if (bp_get_member_profile_data('field=Body Type') == 'A little extra') { echo 'selected'; } ?>>A little extra</option>
                            <option
                                value="Heavyset"
                                <?php if (bp_get_member_profile_data('field=Body Type') == 'Heavyset') { echo 'selected'; } ?>>Heavyset</option>
                        </select>
                    </div>
                    <div class="personal-information title">Marital status:</div>
                    <div class="personal-information select-option">
                        <select
                            id="marital_status"
                            name="marital_status"
                            data-field="civilStatus"
                            data-required="false">
                            <option value="">
                                -- Please select --</option>
                            <option
                                value="Divorced"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'Divorced') { echo 'selected'; } ?>>Divorced</option>
                            <option
                                value="Married"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'Married') { echo 'selected'; } ?>>Married</option>
                            <option
                                value="In a Relationship"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'In a Relationship') { echo 'selected'; } ?>>In a Relationship</option>

                            <option
                                value="Living together"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'Living together') { echo 'selected'; } ?>>Living together</option>
                            <option
                                value="Single"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'Single') { echo 'selected'; } ?>>Single</option>
                            <option
                                value="Widowed"
                                <?php if (bp_get_member_profile_data('field=Relationship status') == 'Widowed') { echo 'selected'; } ?>>Widowed</option>
                        </select>
                    </div>
                    <div class="personal-information title">Hair color:</div>
                    <div class="personal-information select-option">
                        <select
                            id="hair_color"
                            name="hair_color"
                            data-field="hairColor"
                            data-required="false">
                            <option value="">
                                -- Please select --</option>
                            <option
                                value="blond"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Blond') { echo 'selected'; } ?>>Blond</option>
                            <option
                                value="Dark blond"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Dark blond') { echo 'selected'; } ?>>Dark blond</option>
                            <option
                                value="Brown"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Brown') { echo 'selected'; } ?>>Brown</option>
                            <option
                                value="Black"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Black') { echo 'selected'; } ?>>Black</option>
                            <option
                                value="Bald"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Bald') { echo 'selected'; } ?>>Bald</option>
                            <option
                                value="Red"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Red') { echo 'selected'; } ?>>Red</option>
                            <option
                                value="Gray"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Gray') { echo 'selected'; } ?>>Gray</option>
                            <option
                                value="Other"
                                <?php if (bp_get_member_profile_data('field=Hair Color') == 'Other') { echo 'selected'; } ?>>Other</option>
                        </select>
                    </div>
                    <?php /*?>
                    <div class="personal-information title">Eye color:</div>
                    <div class="personal-information select-option">
                        <select
                            id="eye_color"
                            name="eye_color"
                            data-field="eyeColor"
                            data-required="false">
                            <option value="">
                                -- Please select --</option>
                            <option
                                value="Black"
                                <?php if(bp_get_member_profile_data('field=Eye Color') == 'Black'){ echo 'selected'; } ?>>Black</option>
                            <option
                                value="Blue"
                                <?php if(bp_get_member_profile_data('field=Eye Color') == 'Blue'){ echo 'selected'; } ?>>Blue</option>
                            <option
                                value="Brown"
                                <?php if(bp_get_member_profile_data('field=Eye Color') == 'Brown'){ echo 'selected'; } ?>>Brown</option>
                            <option
                                value="Gray"
                                <?php if(bp_get_member_profile_data('field=Eye Color') == 'Gray'){ echo 'selected'; } ?>>Gray</option>
                            <option
                                value="Green"
                                <?php if(bp_get_member_profile_data('field=Eye Color') == 'Green'){ echo 'selected'; } ?>>Green</option>
                        </select>
                    </div><?php */ ?>
                    <a class="button" data-action="save" data-hold="saveProfile" id="save_profile">
                        Save
                    </a>
                    <div class="alert alert-success success_msg" role="alert">Profile save successfully.</div>

                </div>
            </div>
            <div class="right-side">
                <!---about me -->
                <div class="content-block about-me-block" data-my-profile="about-me">
                    <div class="content-title">
                        <h2>About me:</h2>
                    </div>
                    <div class="char-counter">
                        <span data-counter="about-me" class="current-count" id="current-count">0</span>
                        <span>
                            / 700 characters</span>
                    </div>
                    <div class="form-field textarea">
                        <textarea
                            maxlength="700"
                            data-field="aboutMe"
                            id="aboutme"
                            name="aboutme"
                            placeholder="Write a couple of lines about yourself"
                            onkeyup="countChar(this)"><?php bp_member_profile_data('field=About me'); ?></textarea>
                        <a class="button" data-action="save" data-hold="saveProfile" id="save_about">
                            Save
                        </a>
                    </div>
                    <?php
                                    if ($credit_updated[0]->about_me == 0) {
                                    ?>
                    <span class="four_credit_note" id="about-me-credit-note">
                        <div class="icon_wrap">
                            <img
                                src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/images/imgs/coin.png"/>
                        </div>
                        Complete the About Me section and get
                        <?php echo $credit_count[0]->profile_about_me_credit; ?>
                        FREE CREDIT.
                    </span>
                    <?php } ?>
                    <div class="alert alert-success success_about_msg" role="alert">
                        Profile save successfully.
                    </div>
                </div>
                <!---about you -->
                <div class="content-block looking-for-block" data-my-profile="about-you">
                    <div class="content-title">
                        <h2>Looking for:</h2>
                    </div>
                    <div class="char-counter">
                        <span data-counter="about-you" class="current-count" id="current-count-Looking">0</span>
                        <span>
                            / 700 characters</span>
                    </div>
                    <div class="form-field textarea">
                        <textarea
                            data-field="aboutYou"
                            id="lookingfor"
                            name="aboutme"
                            data-required="true"
                            placeholder="Let members know what you are looking for"
                            maxlength="700"
                            onkeyup="countCharLooking(this)"><?php bp_member_profile_data('field=looking for'); ?></textarea>
                        <a class="button" data-action="save" data-hold="saveProfile" id="save_looking">
                            Save
                        </a>
                    </div>
                    <?php
                                    if ($credit_updated[0]->looking_for == 0) {
                                    ?>
                    <span class="four_credit_note" id="looking-for-credit-note">
                        <div class="icon_wrap">
                            <img
                                src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/members/images/imgs/coin.png"/>
                        </div>
                        Complete the Looking For section and get
                        <?php echo $credit_count[0]->profile_looking_for_credit; ?>
                        FREE CREDIT.
                    </span>
                    <?php } ?>
                    <div class="alert alert-success success_looking_msg" role="alert">
                        Profile save successfully.
                    </div>
                </div>
                <!---interests -->
                <div class="content-block redirects">
                    <div class="button-inverted" data-redirect="#">
                        <a href="<?php echo get_site_url(); ?>/setting">Settings</a>
                    </div>
                    <div class="button-inverted" data-redirect="#">
                        <a href="<?php echo get_site_url(); ?>/profile/?id=<?php echo $user_id; ?>">
                            View my profile page</a>
                    </div>
                </div>
            </div>
        </div>
        <p class="error" data-msg="error"></p>
        <p class="success" data-msg="success"></p>
    </div>
</div>

<?php endif; ?>
<?php endwhile; ?>

<?php endif; ?>
<?php endif; ?>
</main>

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#reminderpopup">
1
</button>
<div
class="modal fade"
id="reminderpopup"
tabindex="-1"
role="dialog"
aria-labelledby="reminderpopupLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="r-header">
            <h5 class="title">Reminder</h5>

            <div class="email_img"><img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/email-sending.svg"/></div>

            <div class="verify_box">
                <h6>Lorem ipsum dolor sit amet,
                    <br>
                    consectetur adipisicing elit.</h6>

                <ul>
                    <li>
                        <div class="inner_cover">
                            <div class="icon"><img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/shield-dark.svg"/></div>
                            <div class="text_box">Lorem ipsum</div>
                        </div>
                    </li>

                    <li>
                        <div class="inner_cover">
                            <div class="icon"><img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/contact-dark.svg"/></div>
                            <div class="text_box">Lorem ipsum</div>
                        </div>
                    </li>

                    <li>
                        <div class="inner_cover">
                            <div class="icon"><img src="https://funtimeflirt.com/wp-content/themes/WPDATING-child/popup-assets/noticed-dark.svg"/></div>
                            <div class="text_box">Lorem ipsum</div>
                        </div>
                    </li>
                </ul>

            </div>

<div class="email_sent_box">
<span>Lorem ipsum</span>
<div class="myemail">rakesh.patel@iblinfotech.com</div>
<span>*Please check your span or junk folder</span>
</div>

<div class="r-footer">
<p>Lorem ipsum dolor sit correct?</p>
<a href="javascript:void(0)"><span>change Email Address</span></a>
</div>


        </div>

    </div>
</div>
</div>

<!-- credits Modal -->
<div
class="modal fade"
id="creditspopup"
tabindex="-1"
role="dialog"
aria-labelledby="creditspopupLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="creditspopupLabel">Buy Chat Credits to message users in your area.</h5>
            <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                id="close_credit">
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
                        <a
                            href="javascript:void(0)"
                            class="credit-plans"
                            data-planid="<?php echo $plan_name; ?>"
                            data-amount="<?php echo $plan->amount; ?>"
                            data-currency="<?php echo $currency; ?>"
                            data-plan="<?php echo $plan->plan_name; ?>">
                            <div class="credit_box_wrap">
                                <h5><?php echo $plan->plan_name; ?></h5>
                                <h6 class="offer">limited offer, try now!</h6>
                                <span class="only_rs"><?php echo $currency; ?><?php echo $plan->amount; ?>
                                </span>
                            </div>
                        </a>
                    </li>

                <?php

                                }
                            } else {

                                ?>

                    <li>
                        <a
                            href="javascript:void(0)"
                            class="credit-plans"
                            data-planid="<?php echo $plan_name; ?>"
                            data-amount="<?php echo $plan->amount; ?>"
                            data-currency="<?php echo $currency; ?>"
                            data-plan="<?php echo $plan->plan_name; ?>">
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
                <p>We care about your private life and respect your privacy. Any charges made on
                    your credit card will appear under: ‘vtsup.com*PWR Networ’</p>
                <p>This is not subscription.
                    <br>
                    Your credit card will not be re-billed</p>
                <p>
                    <strong style="color:#000;">Need assistance? send us an email:</strong>
                    <br>
                    <a href="mailto:billing@funtimeflirt.com" target="_blank">billing@funtimeflirt.com</a>
                    <br>
                    By clicking "secure purchase" you agree with our
                    <br>
                    <a href="https://funtimeflirt.com/agreement" target="_blank">term & conditions</a>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
<!-- credits Modal -->

<style>
@import url('https://fonts.googleapis.com/css2?family=Lato&display=swap');

/*img.avatar.avatar-50.wp-user-avatar{width: 125px;border-radius: 50%;border: 4px solid #fff;cursor:pointer;}*/
img.avatar.avatar-50.wp-user-avatar {
    width: 185px;
    border-radius: 0;
    border: 4px solid #fff;
    cursor: pointer;
}

.uploadProfile img.avatar.avatar-50.wp-user-avatar {
    height: 185px;
}

#credit-btn {
    display: block;
    margin: 20px auto;
    position: absolute;
    left: 50%;
    transform: translate(-50%, 0px);
    bottom: 3px;
    height: 45px;
    width: 120px;
    font-size: 16px;
    line-height: 1;
    text-transform: capitalize;
    letter-spacing: 0.5px;
    font-weight: 800;
    background-color: #ebaaf7 !important;
    border: none;
}

div#creditspopup {
    font-family: 'Lato', sans-serif;
}

#creditspopup .modal-title {
    text-align: center;
    font-size: 16px;
    line-height: 1.4;
    font-weight: 700;
    margin: 0 auto;
}

.credits_area_wrap ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.credit_box_wrap {
    padding: 10px;
    box-shadow: 0 0 10px rgb(0 0 0 / 50%);
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
    margin: 0;
    display: block;
    font-weight: 700;
    margin-top: -15px;
}

.credit_box_wrap h5 {
    text-transform: uppercase;
    color: #000;
    font-size: 18px;
    margin: 0;
    line-height: 1;
    font-weight: 600;
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
    font-family: 'Lato';
}

.details_wrap p {
    padding: 5px 0;
    margin: 0 0 10px;
}

.details_wrap a {
    color: #607890;
}

.show#creditspopup {
    z-index: 9999;
    opacity: 1;
    background-color: rgb(0 0 0 / 50%);
}

#creditspopup .modal-dialog {
    transform: translate(0px, -100px);
}

#creditspopup button.close {
    opacity: 1;
    color: #fff;
    margin: 0;
    padding: 0 0 0 !important;
    background-color: rgb(0 0 0 / 30%);
    font-weight: 300;
    line-height: 1;
    right: 4px;
    top: 5px;
    height: 30px;
    width: 30px;
    font-size: 30px;
    border-radius: 5px;
}

#creditspopup .modal-header {
    border-bottom: 0;
}

.modal-content {
    margin: auto;
    display: block;
    width: 86%;
    max-width: 700px;
}

.modal-body {
    position: relative;
    padding: 15px;
}

@media only screen and (max-width: 1024px) {
    #credit-btn {
        bottom: -10px;
        height: 32px;
        width: 115px;
    }
}

@media only screen and (max-width: 480px) {
    .uploadProfile img.avatar.avatar-50.wp-user-avatar {
        height: 80px;
    }
}
</style>
<?php
include_once('custome_footer.php');
?>

<script
type="text/javascript"
src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script type="text/javascript">
$('.popup-gallery').magnificPopup({
    delegate: 'a',
    type: 'image',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
    }
});

jQuery('#edit-username').click(function () {
    jQuery('#my-username').show();
    jQuery('#save-username').show();
    jQuery('#username-txt').hide();
    jQuery(this).hide();
});

jQuery('#save-username').click(function () {

    var userid = '<?php echo $user_id; ?>';
    var my_username = jQuery("#my-username").val();
    // jQuery.ajax({     type: "POST",     dataType: "json",     url:
    // "/wp-admin/admin-ajax.php",     data: {         userid: userid, my_username:
    // my_username,         action: "update_user_name"     }, success:
    // function(response) {         if (response != '') { console.log("test1");
    // alert('Username already exist');         } else {
    // console.log("test2"); jQuery('.success_username_msg').show();
    // jQuery('.success_username_msg').fadeOut(9000); get_credit_details();
    // get_credit_count(); jQuery('#username-credit-note').hide();
    // jQuery('#username-txt').html(my_username); jQuery('#my-username').hide();
    // jQuery('#save-username').hide(); jQuery('#username-txt').show();
    // jQuery('#edit-username').show(); jQuery(this).hide();
    // jQuery('.rmv_username').remove(); jQuery('#username-credit-note').hide();
    // get_credit_details(); }     } });

    let data = {
        'userid': userid,
        'my_username': my_username,
        'action': "update_user_name"
    };
    jQuery.post(
        'https://funtimeflirt.com/wp-admin/admin-ajax.php',
        data,
        function (response) {
            res = jQuery.trim(response);
            if (res != "1") {
                console.log("test1");
                alert('Username already exist');
            } else {
                console.log("test2");
                jQuery('.success_username_msg').show();
                jQuery('.success_username_msg').fadeOut(9000);
                get_credit_details();
                //get_credit_count(); jQuery('#username-credit-note').hide();
                jQuery('#username-txt').html(my_username);
                jQuery('#my-username').hide();
                jQuery('#save-username').hide();
                jQuery('#username-txt').show();
                jQuery('#edit-username').show();
                jQuery(this).hide();
                jQuery('.rmv_username').remove();
                jQuery('#username-credit-note').hide();
                get_credit_details();
            }
        }
    );

});
</script>