<?php
/**
 * Template Name: Dashboard
 */
 
$userLogged = is_user_logged_in();
if ($userLogged === true) { 
$userID = get_current_user_id();
$getMeta = get_user_meta($userID);
$userLevel = intval($getMeta['wp_user_level'][0]);
if ($userLevel < 2) {
$crntUserName = $getMeta['nickname'][0];
$getFName = $getMeta['first_name'][0];
	$getLName = $getMeta['last_name'][0];
	$nameDisplay = "";
	if (!empty($getFName)) { $nameDisplay.= $getFName." "; }
	if (!empty($getLName)) { $nameDisplay.= $getLName; }
if ($userLevel === 0) { $parentID = $getMeta['parent_id'][0]; $parentID = intval($parentID); $childID = $getMeta['delegate_id'][0]; $childID = intval($childID); $getMeta = get_user_meta($parentID); }
else { if ( isset($_POST['options-submit'] ) ) {
	if (!isset($_POST['enable-donate'])) { $donateEdit = 0; } else { $donateEdit = intval($_POST['enable-donate']); }
		if ($donateEdit !== $optionDonate) { update_user_meta($userID, "enable_donate", $donateEdit); $optionDonate = $donateEdit; $getMeta["enable_donate"][0] = $donateEdit; }
	if (!isset($_POST['donate-url'])) { $donateURL = 0; } else { $donateURL = sanitize_text_field($_POST['donate-url']); }
		if ($donateURL !== $optionDonateURL) { update_user_meta($userID, "paypal_url", $donateURL); $optionDonateURL = $donateURL; $getMeta["paypal_url"][0] = $donateURL; }
	if (!isset($_POST['enable-pictures'])) { $picOptionEdit = 0; } else { $picOptionEdit = intval($_POST['enable-pictures']); }
		if ($picOptionEdit !== $optionPic) { update_user_meta($userID, "enable_pic", $picOptionEdit); $optionPic = $picOptionEdit; $getMeta["enable_pic"][0] = $picOptionEdit; }
	if (!isset($_POST['enable-graphic'])) { $graphicOptionEdit = 0; } else { $graphicOptionEdit = intval($_POST['enable-graphic']); }
	if ($graphicOptionEdit !== $optionGraphic) { update_user_meta($userID, "enable_graphic", $graphicOptionEdit); $optionGraphic = $graphicOptionEdit; $getMeta["enable_graphic"][0] = $graphicOptionEdit; }
	} }
$groupID = intval($getMeta['group_id'][0]); 
$resource_path = get_template_directory_uri();
$resource_path.= "-child/page-templates";
$groupDetails = ""; $postDetails = "";
if ($groupID !== 0) { $groupDetails = get_post_meta($groupID); $postDetails = get_post($groupID); $getPageLink = $postDetails->guid; $goToPatient = '<span id="patientPHead">Current Patient: <a href="'.$getPageLink.'" style="color:#012d90;">'.$groupDetails["patient_firstname"][0].' '.$groupDetails["patient_lastname"][0].'</a></span>'; $page2arrow = '<a href="'.$getPageLink.'" style="float:right; text-decoration:none;">> See Patient Page</a>'; };
$getMessage = intval($getMeta['forum_count'][0]);
$optionPic = intval($getMeta["enable_pic"][0]);
$optionGraphic = intval($getMeta["enable_graphic"][0]);
$optionDonate = intval($getMeta["enable_donate"][0]); 
$optionDonateURL = $getMeta["paypal_url"][0];
$getDelegate = intval($getMeta['delegate_count'][0]);
$delegatePostCnt = 0;
	if ($getDelegate !== 0) {
		if ($getDelegate === 1) { $delegatePostCnt = intval($getMeta['delegate1_post_count'][0]); }
		else if($getDelegate === 2) { $delegatePostCnt = intval($getMeta['delegate1_post_count'][0]) + intval($getMeta['delegate2_post_count'][0]); }  };
	$getEntry = $getMeta['create_group'][0];
	$getEntry = intval($getEntry);
require("resource/medFunctions.php");
require("resource/updateEntry.php");
get_header();
echo '<link rel="stylesheet" type="text/css" href="'.$resource_path.'/resource/dash_css.css">
</div></div></div><div id="appContent">';
	$showName = "<div id='dash-welcome'><h3>Welcome ";
	if (!empty($nameDisplay)) { $showName.= $nameDisplay.",</h3></div>"; }	else { $showName.= $crntUserName.",</h3></div>"; }
		$headerPartial = '<div id="app-nav">'.$showName;
		$headerMid1 = '</div><div id="tabWrapper" ';
		$headerMid2 = '><div id="tab0" class="app-tabs" ';
		$headerEnd = '>';
if ($userLevel === 1) {
	$startStyle = "";
	require("resource/userAdmin.php");
	$headerPartial.= '<div id="appNav-3" class="app-nav-btn">Options</div><div id="appNav-2" class="app-nav-btn">Reps</div><div id="appNav-1" class="app-nav-btn">Patient</div><div id="appNav-0" class="app-nav-btn active-tab">Overview</div>';
	$headerMid1.= $startStyle; $headerMid2.= $commentStyle; $headerFull = $headerPartial.$headerMid1.$headerMid2.$headerEnd;
	echo $headerFull; $dashComponents = "";
		if ($getEntry === 0) { echo '<div id="entry-status">You do not have a patient added to this account yet.</div>'; }
		else { $dashComponents.= display_med_status($outputComments,$showPicOptions,$openStyle,$goToPatient,$page2arrow,1); }
	echo $dashComponents.'</div><div id="tab1" class="app-tabs">';
		
	$tab2Str = '</div><div id="tab2" class="app-tabs">';
	if ($getEntry === 0) {
		if ($optionPic === 1) { $aDashF = "/resource/admin_dash.html"; } else { $aDashF = "/resource/admin_dash2.html"; }
		$loadAdmin = new DOMDocument();
		$loadAdmin->loadHTMLFile($resource_path.$aDashF);
		echo $loadAdmin->saveHTML();
		echo $tab2Str.'<div id="no-delegate">You cannot assign representatives without a patient added to the account.</div>'; } 
	else { $picPreview = ""; $addPFormX = "";
	if ($optionPic === 1) {
	if ( has_post_thumbnail($groupID) ) { $getPatientPic = get_the_post_thumbnail($groupID); $getPPicID = get_post_thumbnail_id($groupID); $picPreview = "<div id='picPreview' class='ePicHover'><form method='POST' class='delPPic ePicHover'><input type='hidden' name='picDeleteID' value='".$getPPicID."'/><input type='hidden' name='picFormID' value='post'/><input class='patientDelPic ePicHover' type='submit' name='patientDelPic' value='Remove Picture X'/></form>".$getPatientPic."</div>"; $patPicTxt = "Replace Picture: "; } else { $patPicTxt = "Add Picture: "; }
	$addPFormX = '<div class="ePatientFormD" style="margin-bottom:2%;"><span class="replacePicTxt" style="margin-right:5px;">'.$patPicTxt.'</span><input name="edit-patientPic" id="edit-patientPic" class="picUpload" type="file" /></div>'; }
	$editForm = '<h3 style="margin-left: 3%; font-size: 130%; font-weight: bold;">Edit The Patient Entry</h3>
		'.$picPreview.'
		<form id="edit-patientReg" method="post" enctype="multipart/form-data">
		'.$addPFormX.'
		<div class="ePatientFormD" style="width: 24.5%;"><label for="edit-fname" class="patientLabel">Patient First Name: </label><input class="ePatientFormI" type="text" name="edit-fname" value="'.$groupDetails["patient_firstname"][0].'" style="width: 100%;" maxlength="25"></div><div class="ePatientFormD" style="width: 24.5%;"><label for="edit-lname" class="patientLabel">Patient Last Name: </label><input class="ePatientFormI" type="text" name="edit-lname" value="'.$groupDetails["patient_lastname"][0].'" style="width: 100%;" maxlength="25"></div><div class="ePatientFormD" style="width: 24.5%;"><label for="edit-zip" class="patientLabel">Current Zipcode: </label><input class="ePatientFormI" type="text" name="edit-zip" value="'.$groupDetails["patient_zip"][0].'" style="width: 100%;" maxlength="10"></div><div class="ePatientFormD" style="width: 24.5%;"><label for="edit-city" class="patientLabel">Current City: </label><input class="ePatientFormI" type="text" name="edit-city" value="'.$groupDetails["patient_city"][0].'" style="width: 100%;" maxlength="70"></div><div class="ePatientFormD"><label for="edit-desc" class="patientLabel">Patient Page Description: </label><textarea class="ePatientFormI" type="text" name="edit-desc" rows="4" cols="50">'.$postDetails->post_content.'</textarea></div><input class="ePatientFormI" id="patient-edit" type="submit" name="patient-edit" value="Edit Patient" style="width:24.7%; height:4vh;"/><input class="ePatientFormI" id="patient-delete" type="submit" name="patient-delete" value="Delete Patient" style="width:24.7%; height:4vh;"/></form>'.$tab2Str;
	echo $editForm;
	if ($getDelegate === 0) { echo '<div id="delegate-status">You do not have any representatives added to this account yet.</div>'; $userBox = array('','',''); } else { $userBox = array('<div class="','FormD"><span>User Name: </span><input type="text" value="','" style="background:#d3d3d3; color:#8c8c8c;" readonly></div>'); }
	if ($getDelegate < 2) { $show_all_delegate = $getDelegate + 1; } else { $show_all_delegate = $getDelegate; } $allDelOutFin = ""; 
		for($k = 1; $k <= $show_all_delegate; $k++) { $dAllCnt++; $fullVarArr = "";
			if (array_key_exists('delegate_id'.$k,$getMeta)) { $dGarKey = 'delegate_id'.$k; $delLocID = $getMeta[$dGarKey][0]; $dUserInfo = get_user_by('ID',$delLocID); $dUserMetaFName = get_user_meta($delLocID,"first_name", true); $dUserMetaLName = get_user_meta($delLocID,"last_name", true);
			$delDestroyDel = "<form id='delDelegateRmv' name='delDelegateRmv' method='post'><input type='hidden' name='delTypeID' value='".$delLocID."-".$k."'/></form>";
			$delDestroyBtn = "<button name='submitDelRmv' type='submit' form='delDelegateRmv' value='Delete Delegate' style='float:right; height:4vh; width:45%; clear:right;'>Delete</button>";
			$fullVarArr = array("Save Changes","eDelegate",$dUserInfo->user_login,$dUserMetaFName,$dUserMetaLName,$dUserInfo->user_email,"","placeholder='Hidden' ","",$delDestroyBtn,$delDestroyDel); }
			else { $fullVarArr = array("Add","delegate","","","","","required","","<div class='warnDelForm'>Note: All Forms Required</div>","",""); }
	$allDelOutFin.= '<form id="'.$fullVarArr[1].'-reg" method="post"><h3>'.$fullVarArr[0].' Representative '.$k.'</h3><hr><input type="hidden" name="delTypeID" value="'.$delLocID.'-'.$k.'"/>'.$userBox[0].$fullVarArr[1].$userBox[1].$fullVarArr[2].$userBox[2].'<div class="'.$fullVarArr[1].'FormD"><label for="'.$fullVarArr[1].'-fname">First Name: </label><input class="'.$fullVarArr[1].'FormI" type="text" name="'.$fullVarArr[1].'-fname" value="'.$fullVarArr[3].'" '.$fullVarArr[6].'/></div><div class="'.$fullVarArr[1].'FormD"><label for="'.$fullVarArr[1].'-lname">Last Name: </label><input class="'.$fullVarArr[1].'FormI" type="text" name="'.$fullVarArr[1].'-lname" value="'.$fullVarArr[4].'" '.$fullVarArr[6].'/></div><div class="'.$fullVarArr[1].'FormD"><label for="'.$fullVarArr[1].'-password">Password: </label><input class="'.$fullVarArr[1].'FormI" type="password" name="'.$fullVarArr[1].'-password" value="" '.$fullVarArr[7].$fullVarArr[6].'/></div><div class="'.$fullVarArr[1].'FormD"><label for="'.$fullVarArr[1].'-email">Email: </label><input class="'.$fullVarArr[1].'FormI" type="text" name="'.$fullVarArr[1].'-email" value="'.$fullVarArr[5].'" '.$fullVarArr[6].'/></div>'.$fullVarArr[8].'<input class="delegateEnter'.$k.'" type="submit" name="'.$fullVarArr[1].'-submit" value="'.$fullVarArr[0].'"/>'.$fullVarArr[9].'</form>'.$fullVarArr[10];
		if ($k === 1) { $allDelOutFin.= '<div style="float:left; margin-top:1%; clear:both"></div>'; }
	} echo $allDelOutFin; }
	
	$opDonate = ""; $opPicE = ""; $opGraph = "";
	if ($optionDonate === 1) { $opDonate = "checked"; $dpShow = "initial"; $dhShow = "auto"; } else { $dpShow = "none"; $dhShow = "0"; }
	if ($optionPic === 1) { $opPicE = "checked"; }
	if ($optionGraphic === 1) { $opGraph = "checked"; }
	if ($optionDonateURL !== 0 && $optionDonateURL !== "0") { $showDonateURL = $optionDonateURL; $showPlaceH = ""; } else { $showDonateURL = ""; $showPlaceH = "YourNameHere"; }
	echo '</div><div id="tab3" class="app-tabs">
	<form class="option-form" method="post">
		<div class="option-box"><label class="option-label" for="enable-donate">Enable PayPal Donations: </label><input class="option-check" name="enable-donate" id="enable-donate" type="checkbox" value="1" '.$opDonate.'/></div>
		<br/>
		<div class="explanation-paragraph">
		<p style="clear:both;">Please enable this option if you would like to accept PayPal donations on your patient page. To get a free PayPal donation link, please visit <a href="https://www.paypal.me/">PayPal.me</a></p>
		</div>
		
		<div class="option-box url-holder" style="display:'.$dpShow.'; height:'.$dhShow.'; width:100%;"><label class="option-label" for="donate-url">Donations URL: http://www.paypal.me/</label><input name="donate-url" type="text" style="width:10%;" value="'.$showDonateURL.'" placeholder="'.$showPlaceH.'"/></div>
		<div class="option-box"><label class="option-label" for="enable-pictures">Enable Pictures: </label><input class="option-check" name="enable-pictures" type="checkbox" value="1" '.$opPicE.'/></div>
				<br/>
		<div class="explanation-paragraph">
		<p style="clear:both;">Please enable this option if you would like to enable the display of images on the patient status page.</a></p>
		</div>
		<div class="option-box"><label class="option-label" for="enable-graphic">Enable Graphic Images: </label><input class="option-check" name="enable-graphic" type="checkbox" value="1" '.$opGraph.'/></div>
						<br/>
		<div class="explanation-paragraph">
		<p style="clear:both;">Please enable this option if you would like to enable the display of graphic images on the patient status page. Images can be labeled as "graphic" and viewers will need to click a button in order to view the images.</a></p>
		</div>
		<span style="float:left; font-size:150%; line-height:150%; margin-top:1%; clear:left;">Report Issue: </span><a style="float:left; font-size:150%; line-height:150%; margin-top:1%; margin-left: 0.5%; clear:right;" href="http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'feedback/">Message Us</a>
		<input id="options-submit" type="submit" name="options-submit" value="Save Options"/>
	</form>
	</div></div>'; }
	
else if ($userLevel === 0) {
	$headerPartial.= '</div><div id="tab0" class="app-tabs">';
	$headerMid1.= $startStyle; $headerMid2.= $commentStyle; $headerFull = $headerPartial.$headerMid1.$headerMid2.$headerEnd;
	echo $headerFull;
	$commentComponents = display_med_status($outputComments,$showPicOptions,$openStyle,$goToPatient,$page2arrow,1);
	echo $commentComponents.'</div></div></div>'; }
		
echo '<script type="text/javascript" src="'.$resource_path.'/resource/dash_js.js"></script>';
get_footer(); } else { $getAdmin = get_site_url(); $getAdmin = $getAdmin."/wp-admin/"; wp_redirect($getAdmin); }
} else { $r_user = wp_login_url( get_permalink() ); wp_redirect($r_user); }
