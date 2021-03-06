<?php
function med_picture_upload($med_id,$medFile,$med_prefix,$not1,$med_type,$meta_target,$delete_target) {
	global $wpdb; 
		include( ABSPATH . 'wp-admin/includes/image.php' );
		if (!empty($medFile) && $medFile != NULL) {
		$new_med_file = $med_prefix.$medFile["name"];
		$med_upload = wp_upload_dir();
		$med_path_1 = $med_upload['baseurl'] . "/medWeb-".$med_id."/" . $new_med_file;
		$med_path_2 = $med_upload['basedir']."/medWeb-".$med_id."/";
		$med_target = $med_path_2.$new_med_file;
		function insert_med_pic($var1,$var2,$var3,$var4,$var5,$var6) {
		if(@is_array(getimagesize($var3))){
			if ($var1 !== "post2") { if ($var1 === "comment2" || $var1 === "post1") { wp_delete_attachment($var6, true); }
			$med_file_type = wp_check_filetype( basename($var2), null );
			$med_image = array('post_title' => preg_replace( '/\.[^.]+$/', '', basename( $var2 ) ),
			'post_mime_type' => $med_file_type['type'],
			'post_content' => '',
			'guid' => $var2,
			'post_status' => 'inherit');
			$getPicID = wp_insert_attachment($med_image, $var3, $var4);
			$med_attach = wp_generate_attachment_metadata( $getPicID, $var3 );
			wp_update_attachment_metadata( $getPicID, $var3 );
			if ($var1 === "comment1") { add_comment_meta($var5,"comment_pic",$var2); }
			else if ($var1 === "comment2") { update_comment_meta($var5,"comment_pic",$var2); } }
			if ($var1 === "post1" || $var1 === "post2" || $var1 === "post3") { set_post_thumbnail($var4,$getPicID); }
			} else { unlink($var3); }
		}
		if ($med_type === "comment") {
			if (!file_exists($med_path_2)) { $create_med_dir = mkdir($med_path_2, 0755); }
			$get_med_meta = get_comment_meta($meta_target,"comment_pic", true);
			$oldID = $wpdb->get_var("SELECT ID FROM wp_posts WHERE guid = '".$get_med_meta."'");
			if (!empty($not1) && $not1 === 1) { if(!file_exists($med_target)){ move_uploaded_file($medFile["tmp_name"], $med_target);
				insert_med_pic("comment1",$med_path_1,$med_target,$med_id,$meta_target,$oldID); } else { add_comment_meta($meta_target,"comment_pic",""); } }
			if($not1 === 2) { move_uploaded_file($medFile["tmp_name"], $med_target); insert_med_pic("comment2",$med_path_1,$med_target,$med_id,$meta_target,$oldID); }
			}
		else if($med_type === "post") {
			if(!file_exists($med_target)){
			if (!file_exists($med_path_2)) { $create_med_dir = mkdir($med_path_2, 0755); }
			move_uploaded_file($medFile["tmp_name"], $med_target);
			if (!empty($not1) && $not1 !== "") {
			$old_details = get_post($not1); $old_guid = $old_details->guid; $old1 = str_replace( $med_upload['baseurl'], $med_upload['basedir'], $old_guid ); $new2 = $med_upload['url'] . "/" . $new_med_file;
			$oldID = $wpdb->get_var("SELECT ID FROM wp_posts WHERE guid = ".$med_target);
			if ( $old1 !== $new2) { insert_med_pic("post1",$med_path_1,$med_target,$med_id,"",$oldID); } else { insert_med_pic("post2",$med_path_1,$med_target,$med_id,"",$oldID); } }
			else { insert_med_pic("post3",$med_path_1,$med_target,$med_id,"",""); } }
			}
		} else if ($delete_target !== "") {
		if ($med_type === "comment") { wp_delete_attachment($delete_target, true); delete_comment_meta($meta_target,'comment_pic'); }
		else if($med_type === "post") { delete_post_thumbnail($med_id); } }
	}
			
function add_new_medpost($newStatus, $newTopic, $newLocation, $countUArray) {
	global $userID, $groupID, $nameDisplay, $getMessage, $getMeta;
	$topic_data = array('comment_post_ID' => $groupID, 'comment_author' => $nameDisplay, 'comment_content' => $newTopic, 'comment_type' => '', 'comment_parent' => 0, 'user_id' => $userID);
	$new_comment = wp_new_comment($topic_data);
	add_comment_meta($new_comment,'patient_status',$newStatus);
	add_comment_meta($new_comment,'patient_location',$newLocation);
	$getMessage = $getMessage + 1;
	$getMeta['forum_count'][0] = $getMessage;
	if ($countUArray === 'none') { update_user_meta($userID, 'forum_count', $getMessage); }
	if ($countUArray !== 'none') { $assembleMKey = "delegate".$countUArray[1]."_post_count"; 
	$getCrntThisCount = intval($getMeta[$assembleMKey][0]);
	$getCrntThisCount = $getCrntThisCount + 1;
	update_user_meta( $countUArray[0], $assembleMKey, $getCrntThisCount); }
	return $new_comment; }
function edit_medpost($editComment, $editComStatus, $editComLoc, $editComContent) {
	$editComment = intval($editComment);
	$updateThisCom = wp_update_comment(array('comment_ID' => $editComment, 'comment_approved' => 1, 'comment_content' => $editComContent));
	update_comment_meta($editComment,'patient_status',$editComStatus);
	update_comment_meta($editComment,'patient_location',$editComLoc); }
	
function del_med_status($comID,$countU2Array) {
	global $getMessage, $userID, $wpdb;
	$findComDelImg = get_comment_meta($comID,"comment_pic",true);
	if ($findComDelImg !== "") { 
	$oldID = $wpdb->get_var("SELECT ID FROM wp_posts WHERE guid = '".$findComDelImg."'");
	wp_delete_attachment($oldID, true); }
	$getMessage = $getMessage - 1;
	$getMeta['forum_count'][0] = $getMessage;
	if ($countU2Array === 'none') { update_user_meta($userID, 'forum_count', $getMessage); }
	if ($countU2Array !== 'none') { $assembleMKey = "delegate".$countU2Array[1]."_post_count"; 
	$getCrntThisCount = intval($getMeta[$assembleMKey][0]);
	$getCrntThisCount = $getCrntThisCount - 1;
	update_user_meta( $countU2Array[0], $assembleMKey, $getCrntThisCount); }
	wp_delete_comment($comID, true); }
	
function manage_med_pic_graphic($comGID,$SPG1,$SPG2) {
	$SPG1 = intval($SPG1); $SPG2 = intval($SPG2); 
		if ($SPG1 === 0 && $SPG2 === 1) { add_comment_meta($comGID,"pic_graphic",1); } 
		else if ($SPG1 === 1) { update_comment_meta($comGID,"pic_graphic",$SPG2);} }
	
function retrieve_med_status() {
	global $groupID, $userID, $userLevel, $wpdb, $optionPic, $optionGraphic; $commentVar = array('post_id' => $groupID, 'orderby' => 'comment_date', 'order' => 'ASC'); $collectComments = "";
		$returnComments = get_comments($commentVar); $cleanUser = intval($userID);
			foreach ($returnComments as $oneComment) { $editPicForm1 = ""; $cleanCompare = intval($oneComment->user_id);
			$commentPic = get_comment_meta($oneComment->comment_ID, 'comment_pic', true);
			if (!empty($commentPic) && $commentPic !== "") { $list_pic_id = $wpdb->get_var("SELECT ID FROM wp_posts WHERE guid = '".$commentPic."'"); $list_pic_id = '<input type="hidden" name="picDeleteID" value="'.$list_pic_id.'"/>'; $h1A = "hasImage"; 
			$dumpImgNw = '<img class="editComPic" src="'.$commentPic.'" style="width:100%" />'; $displayRmv = ""; $aPID = 2; $aPTxt = "Replace"; }
			else { $list_pic_id = ""; $h1A = "noImage"; $dumpImgNw = ""; $displayRmv = "display: none;"; $aPID = 1; $aPTxt = "Add"; }
			if ($optionGraphic === 1) { $insert_graphic_box = ""; $seeIfGraphic = get_comment_meta($oneComment->comment_ID, 'pic_graphic', true); $seeIfGraphic = intval($seeIfGraphic);
			if ($seeIfGraphic === 1) { $checkGraphUpdate = 1; $setMarkChk = "checked"; } else if ($seeIfGraphic !== 1) { $checkGraphUpdate = 0; $setMarkChk = ""; }
				$insert_graphic_box = '<span class="setAsGSpan" style="'.$displayRmv.'">Graphic Image: </span><input class="setAsGraphic" name="setAsGraphic" type="checkbox" value="1" style="'.$displayRmv.'" '.$setMarkChk.'/><input type="hidden" name="picGraphicID" value="'.$checkGraphUpdate.'" />'; } else { $insert_graphic_box = '<input type="hidden" name="picGraphicID" value="0"/>'; }
			if ($optionPic === 1) { $editPicForm1 = '<div class="prevEditPostImg '.$h1A.'">'.$dumpImgNw.'</div><div class="editCommentPic" style="margin-top:0.5%; clear:both;">'.$list_pic_id.'<input class="addPicID" type="hidden" name="addPicID" value="'.$aPID.'"/><input class="picFormID" type="hidden" name="picFormID" value="0"/><span class="replacePicTxt">'.$aPTxt.' Picture: </span><input name="edit-commentPic" class="edit-commentPic" style="margin-bottom:0.5%;" type="file" /><span class="delTxt" style="'.$displayRmv.'">Remove Picture: </span><button type="button" class="patientDelPic" name="patientDelPic" style="float:left; margin-bottom:0.5%; clear:right; '.$displayRmv.'">Delete</button>'.$insert_graphic_box.'</div>'; }
			$editPicForm1.= '<input class="editPComment" type="submit" name="editPComment" value="Save Edits" /></form>';
			$optBoxDet = ""; $belongPost = "";
			$commentLoc = get_comment_meta($oneComment->comment_ID, 'patient_location', true); $commentStatus = get_comment_meta($oneComment->comment_ID, 'patient_status', true);
			if ($userLevel === 1 || $cleanUser === $cleanCompare) { if ($cleanUser === $cleanCompare) { $belongPost = "user-post"; } else { $belongPost = "not-user"; }
				$optBoxDet = '<div class="openEdit">+ Edit Post</div></div><div class="editComForm" style="display:none;"><form method="POST" class="editComment" name="editComment" enctype="multipart/form-data" style="clear:both;"><input type="hidden" name="commentID" value="'.$oneComment->comment_ID.'"/><div class="editCField"><span class="eCommentLabel">Location:</span><input class="editCommentData" name="editCLoc" value="'.$commentLoc.'"/></div><div class="editCField"><span class="eCommentLabel">Condition:</span><input class="editCommentData" name="editCStatus" value="'.$commentStatus.'"/></div><div class="editCField"><textarea class="editCommentText" name="editCText" col="8">'.$oneComment->comment_content.'</textarea></div>'.$editPicForm1.'<div class="closeEdit">+ Close Edit</div></div><form method="POST" class="rmfComment"><input type="hidden" name="commentID" value="'.$oneComment->comment_ID.'"/><input class="deleteNowComment" type="submit" name="deleteNowComment" value="Delete Post" /></form></div>'; }
			else { $belongPost = "not-user"; $optBoxDet = '</div></div>'; }
			$serverTime = $oneComment->comment_date;
			if (!empty($serverTime) && $serverTime !== 0 && $serverTime !== "") {
			$splitDateTime = explode(" ",$serverTime); $splitDate = $splitDateTime[0]; $splitTime = $splitDateTime[1];
			$splitDate = date("F d, Y", strtotime($splitDate)); $splitTime = explode(":",$splitTime); $cHour1 = intval($splitTime[0]); $timeOfDay = "am";
			if ($cHour1 >= 12) { $cHour1 = $cHour1 - 12; $timeOfDay = "pm"; }
			else if ($cHour1 === 0) { $cHour1 = 12; }
			$fTime = $cHour1.":".$splitTime[1].$timeOfDay; } else { $fTime = "Unknown"; }
			$collectComments.= '<div class="mainComment '.$belongPost.'"><div class="commentHeadR"><div class="nameComment">'.$oneComment->comment_author.'</div><div class="commentDate">Posted on '.$splitDate." - ".$fTime.'</div></div><div class="commentInner"><div class="commentLoc"><b>Location:</b> '.$commentLoc.'</div><div class="commentStatus"><b>Condition:</b> '.$commentStatus.'</div><div class="commentContent">'.$oneComment->comment_content.'</div>'.$optBoxDet;
			} return $collectComments; }
			
function display_med_status($loadComments,$picOptRun,$comStyle,$link2patient,$sideArrow2,$switchType) { $compileComments = "";
	if ($switchType === 1) { $compileComments.= '<div id="dashMDCol1">'.$link2patient.'<div id="statusBox"><form id="status-reg" method="post" enctype="multipart/form-data"><div id="sBox1">'.$picOptRun.'<div id="sBox1A"><label for="add-status" class="comment-label" style="clear:left;">Condition: </label><input type="text" id="add-status" name="add-status" class="new-comment" value=""/></div><div id="sBox1B"><label for="add-location" class="comment-label">Location: </label><input type="text" id="add-location" name="add-location" class="new-comment" value=""/></div><input id="status-submit" type="submit" name="status-submit" value="Add Patient Post"/></div><div id="sBox2"><label for="add-details" class="comment-label">Message: </label><textarea id="add-details" name="add-details" class="new-comment" col="8"></textarea></div></form></div></div><div id="dashMDCol2" '.$comStyle.'><div id="commentTopBar"><div id="sortByDash"><span>Show: </span><select id="sortBySelect"><option value="all-post">All Posts</option><option value="user-post">My Posts</option><option value="not-user">Everyone Elses</option></select></div>'.$sideArrow2.'</div>'; $compileComments.= $loadComments.'</div>'; }
	else { $compileComments.= $loadComments.'</div></div><div style="width:100%; padding-top:20px; padding-bottom:20px; margin-top:30px; background-color:#1e73be; overflow-y: auto;"><div style="position:relative; float:none; margin-left:auto; margin-right:auto; width:70%;"><div id="dashMDCol1"><div id="statusBox"><form id="status-reg" method="post" enctype="multipart/form-data"><div id="sBox1">'.$picOptRun.'<div id="sBox1A"><label for="add-status" class="comment-label" style="clear:left;">Condition: </label><input type="text" id="add-status" name="add-status" class="new-comment" value=""/></div><div id="sBox1B"><label for="add-location" class="comment-label">Location: </label><input type="text" id="add-location" name="add-location" class="new-comment" value=""/></div><input id="status-submit" type="submit" name="status-submit" value="Add Patient Post"/></div><div id="sBox2">'.$sideArrow2.'<label for="add-details" class="comment-label" style="display:inline !important; clear:none !important; margin-top:12px;">Message: </label><textarea id="add-details" name="add-details" class="new-comment" col="8" style="float:left !important; display:inline !important; width: 79.5% !important; margin-top:22px; margin-left:5px;"></textarea></div></form></div></div><div id="dashMDCol2" '.$comStyle.'</div>'; }
	return $compileComments;
}
?>
