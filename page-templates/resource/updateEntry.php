<?php
if ($getEntry === 1) { 
	if ($optionPic === 1) { $showPicOptions = '<div id="prevNewPostImg" style="width:25%;"></div><br><button id="removeAddPicX" type="button" style="float:left; margin-right:0.5%; display:none; clear:left;">Remove Picture</button><input name="add-commentPic" id="add-commentPic" type="file" style="clear:right;"/>'; } else { $showPicOptions = ""; }
	if ( isset($_POST['status-submit'] ) ) {
		$newAStatus = sanitize_text_field($_POST['add-status']); $newALoc = sanitize_text_field($_POST['add-location']); $newATopic = sanitize_text_field($_POST['add-details']); 
		if ($userLevel === 1) { $newCommentAdd = add_new_medpost($newAStatus,$newATopic,$newALoc,'none'); }
		else { $newCommentAdd = add_new_medpost($newAStatus,$newATopic,$newALoc,array($parentID,$childID)); }
		if ($optionPic === 1) { med_picture_upload($groupID,$_FILES["add-commentPic"],"comment-".$newCommentAdd."-",1,"comment",$newCommentAdd,""); 
		if ($optionGraphic === 1) { $setAsPicG = intval($_POST['setAsGraphic']); $setAddGType = intval($_POST['picGraphicID']); 
		manage_med_pic_graphic($newCommentAdd,$_POST['picGraphicID'],$_POST['setAsGraphic']); } }
		unset($_POST); }
		
	if ( isset($_POST['deleteNowComment'] ) ) {
		$crntDelComID = $_POST['commentID'];
		if ($userLevel === 1) { del_med_status($crntDelComID,'none'); }	else { del_med_status($crntDelComID,array($parentID,$childID)); }
		unset($_POST); }
	
		if ( isset($_POST['editPComment'] ) ) {
		$ECID = intval($_POST['commentID']);
		$editAComStat = sanitize_text_field($_POST['editCStatus']); $editAComLoc = sanitize_text_field($_POST['editCLoc']); $editAComCont = sanitize_text_field($_POST['editCText']); 
		edit_medpost($ECID, $editAComStat, $editAComLoc, $editAComCont);
		if ($optionPic === 1) {
		$DELCLASS = intval($_POST['picFormID']);
		$CSALT = intval($_POST['addPicID']);
		if ($DELCLASS === 1) { if ($CSALT === 2) { $RCP = $_POST['picDeleteID']; } else if ($CSALT === 1) { $RCP = ""; } $_FILES['edit-commentPic'] = ""; } else { $RCP = ""; }
		med_picture_upload($groupID,$_FILES['edit-commentPic'],"comment-".$ECID."-",$CSALT,"comment",$ECID,$RCP);
			if ($optionGraphic === 1) { $setAsPicG = intval($_POST['setAsGraphic']); $setAddGType = intval($_POST['picGraphicID']); manage_med_pic_graphic($ECID,$_POST['picGraphicID'],$_POST['setAsGraphic']); }
			unset($_POST); } }
	
	if (isset($_POST['patientDelPic'])) { $delRmvTrgt = $_POST['picDeleteID']; $scrubGroup = intval($groupID); med_picture_upload($scrubGroup,"","medWeb-".$groupID."-".$userID."-","","post","",$delRmvTrgt); unset($_POST); }
	$outputComments = "";
	if ($getMessage !== 0 || $delegatePostCnt !== 0) { $outputComments = retrieve_med_status(); $commentStyle = ""; $openStyle = ''; }
	else { $openStyle = ""; $commentStyle = 'style="min-height:56vh;"'; }
	}
?>
