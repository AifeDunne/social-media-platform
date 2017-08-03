<?php
/*
Template Name: Single Patient
Description: Patient Page
*/
$resource_path = get_template_directory_uri(); 
$resource_path.= "-child/page-templates";
$req_file_prefix = "page-templates/resource/";
$crnt_post_id = get_the_ID();
$pat_p = get_post($crnt_post_id,'ARRAY_A');
$get_uAuthor = intval($pat_p['post_author']);
$donate_yes = get_user_meta( $get_uAuthor, 'enable_donate', true); $donate_yes = intval($donate_yes);
$pic_yes = get_user_meta( $get_uAuthor, 'enable_pic', true); $pic_yes = intval($pic_yes);
$graphic_yes = get_user_meta( $get_uAuthor, 'enable_graphic', true); $graphic_yes = intval($graphic_yes);
$pay_url = get_user_meta( $get_uAuthor, 'paypal_url', true);
$get_pmeta = get_post_meta($crnt_post_id);
if ($pay_url !== 0 && $pay_url !== "0" && $donate_yes === 1) { $pat_purl = "<a id='donateBtn' href='http://www.paypal.me/".$pay_url."' target='_blank'>Donate</a>"; } else { $pat_purl = ""; }
$headerString = "<div id='patientHeader'>";
$pat_pic = get_the_post_thumbnail($crnt_post_id,'full');
$headerString.= "<div id='mainProfilePic'>".$pat_pic."</div>";
$headerString.=  "<div id='patientName'>".$pat_p['post_title']."</div><div id='patientHeadText'>".$pat_p['post_content']."</div>".$pat_purl."</div>";
$midHeader = $headerString."<div id='patientMidInner' style='width:70%; overflow-y:auto;'><div id='patientMidHeader'>Latest Updates</div><div id='commentArray'>";
$hasComments = intval($pat_p['comment_count']);
if ($hasComments > 0) {
	$userLogged = is_user_logged_in();
	$userID = get_current_user_id();
	$getMeta = get_user_meta($userID);
	$userLevel = intval($getMeta['wp_user_level'][0]);
	$editCheck = 0;
		if ($userLogged === true && $userLevel < 2) {
		$crntUserName = $getMeta['nickname'][0];
		$getFName = $getMeta['first_name'][0];
			$getLName = $getMeta['last_name'][0];
			$nameDisplay = "";
			if (!empty($getFName)) { $nameDisplay.= $getFName." "; }
			if (!empty($getLName)) { $nameDisplay.= $getLName; }
		if ($userLevel === 0) { $parentID = $getMeta['parent_id'][0]; $parentID = intval($parentID); $childID = $getMeta['delegate_id'][0]; $childID = intval($childID); $getMeta = get_user_meta($parentID); }
		$groupID = intval($getMeta['group_id'][0]);	$groupDetails = ""; $postDetails = "";
		if ($groupID !== 0 && $groupID === $crnt_post_id) { $groupDetails = get_post_meta($groupID); $postDetails = get_post($groupID); 
		$getPageLink = $postDetails->guid; $goToPatient = ''; $page2arrow = '<a href="/dashboard/" style="display:block; width:100%; text-align:right; text-decoration:none; color:#FFF; font-size:22px; font-weight:bold;">> See Full Dashboard</a>';
		$getMessage = intval($getMeta['forum_count'][0]); $optionPic = intval($getMeta["enable_pic"][0]); $optionGraphic = $pic_yes; $optionDonate = $donate_yes; $optionDonateURL = $pay_url; 
		$getDelegate = intval($getMeta['delegate_count'][0]); $delegatePostCnt = 0;
			if ($getDelegate !== 0) {
				if ($getDelegate === 1) { $delegatePostCnt = intval($getMeta['delegate1_post_count'][0]); }
				else if($getDelegate === 2) { $delegatePostCnt = intval($getMeta['delegate1_post_count'][0]) + intval($getMeta['delegate2_post_count'][0]); }  };
			$getEntry = $getMeta['create_group'][0]; $getEntry = intval($getEntry);
		require($req_file_prefix."medFunctions.php");
		require($req_file_prefix."updateEntry.php");
		if ($userLevel === 1) {	require($req_file_prefix."userAdmin.php"); $dashComponents = ""; if ($getEntry !== 0) { 
		$dashComponents = display_med_status($outputComments,$showPicOptions,$openStyle,$goToPatient,$page2arrow,2); } $midHeader.= $dashComponents; }
		else if ($userLevel === 0) { $commentComponents = display_med_status($outputComments,$showPicOptions,$openStyle,$goToPatient,$page2arrow,2); $midHeader.= $commentComponents.'</div></div></div>'; }
			} else { $editCheck = 1; }
				} else { $editCheck = 1; }
	if ($editCheck === 1) {
	$findComments = array('post_id' => $crnt_post_id, 'orderby' => 'comment_date', 'order' => 'ASC');
	$grabComments = get_comments($findComments);
	foreach ($grabComments as $comments) {
	$midHeader.= "<div class='commentBox'><div class='commentBoxInner'>";
	$splitDateTime = $comments->comment_date;
	$splitDateTime = explode(" ",$splitDateTime);
	$splitDate = $splitDateTime[0];	$splitTime = $splitDateTime[1];
	$splitDate = date("F d, Y", strtotime($splitDate));
	$splitTime = explode(":",$splitTime);
	$cHour1 = intval($splitTime[0]); $timeOfDay = "am";
	if ($cHour1 >= 12) { $cHour1 = $cHour1 - 12; $timeOfDay = "pm"; }
	else if ($cHour1 === 0) { $cHour1 = 12; }
	$fTime = $cHour1.":".$splitTime[1].$timeOfDay;
	$commentAuthor = $comments->user_id;
	$authorDetails = get_userdata($commentAuthor);
	$midHeader.= "<div class='commentSec1'>".get_avatar($commentAuthor)."</div><div class='commentSec2'><div class='commentSec2H1'>Condition:<span class='commentSec2H2'>".get_comment_meta($comments->comment_ID, 'patient_status', true)."</span></div><div class='commentSec2H1'>Location:<span class='commentSec2H2'>".get_comment_meta($comments->comment_ID, 'patient_location', true)."</span></div>
	<div class='updateDetails'>Posted by ".$authorDetails->display_name." on ".$splitDate." @ ".$fTime."</div>
	<div class='statusContent'>".$comments->comment_content."</div>";
	if ($pic_yes === 1) { $checkPostPagePic = get_comment_meta($comments->comment_ID, 'comment_pic', true);
		if ($graphic_yes === 1) { $checkPostGraphic = get_comment_meta($comments->comment_ID, 'pic_graphic', true); $checkPostGraphic = intval($checkPostGraphic);
			if ($checkPostGraphic === 1) { $pG1 = "<div class='gImgBlock'><div class='gImgTxt'>GRAPHIC IMAGE ATTACHED</div><button type='button' class='gImgBtn'>CLICK TO VIEW</button><div class='gImgHolder closedG'><hr style='clear:both;'><img src='"; $pG2 = "' style='height:100%; width:auto;'/></div></div>"; } else { $pG1 = "<div class='imgHolder'><img src='"; $pG2 = "' style='width:100%; height:auto;'/></div>"; }
	if ($checkPostPagePic !== "") { $midHeader.= $pG1.$checkPostPagePic.$pG2; } } }
	$midHeader.= "</div></div></div>"; } }
	} else { $midHeader.= "style='width:66%; padding-left:2%; padding-right:2%; background:#FFF; min-height:16vh;'><div id='patientMidHeader'>Latest Updates</div><div id='commentArray'><div id='noComments'>No posts have been added for this patient yet. Check back later.</div>"; }
get_header();
echo '</header>
<link rel="stylesheet" type="text/css" href="'.$resource_path.'/resource/patient_page.css">
<div class="entry-content">
	<div id="contentCenter">
	'.$midHeader."</div></div>";
echo '<script type="text/javascript" src="'.$resource_path.'/resource/dash_js.js"></script>';
?>
<script type="text/javascript">
jQuery(document).ready(function( $ ) {
$(".gImgBtn").mousedown(function() {
	var gHold = $(this).parent().find(".gImgHolder");
	var btnG = $(this).parent().find(".gImgBtn");
	var gStatus = gHold.attr("class");
	gStatus = gStatus.split(" ");
	gStatus = gStatus[1];
	if (gStatus == "closedG") { gHold.removeClass("closedG").addClass("openG").css({"display":"initial"}); btnG.empty(); btnG.text("CLICK TO CLOSE"); }
	else if (gStatus == "openG") { gHold.removeClass("openG").addClass("closedG").css({"display":"none"}); btnG.empty(); btnG.text("CLICK TO VIEW");  }
	});
});
</script>
<?php get_footer(); ?>
