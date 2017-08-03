<?php
if ($getEntry === 1) { 
	if ( isset($_POST['patient-delete'] ) ) {
		if ($getDelegate !== 0) { for($q = 1; $q <= $getDelegate; $q++) { $delDelegate = 'delegate_id'.$q; $getDelegateID = $getMeta[$delDelegate][0]; wp_delete_user($getDelegateID); delete_user_meta($userID,$delDelegate); } 
		update_user_meta($userID,'delegate_count',0); $getDelegate = 0; $getMeta['delegate_count'][0] = 0; }
		if ($getMessage !== 0) {
			$delCommentDel = array('post_id' => $groupID, 'orderby' => 'comment_date');
			$deleteDCom = get_comments($delCommentDel);
			foreach ($deleteDCom as $delCom) { wp_delete_comment($delCom->comment_ID, true); }
			update_user_meta($userID,'forum_count',0); 
			$getMessage = 0; $getMeta['forum_count'][0] = 0; 
			$delegatePostCnt = 0; }
		$removePostPic = get_post_thumbnail_id($groupID);
		delete_post_thumbnail($groupID);
		wp_delete_attachment($removePostPic, true);
		wp_delete_post($groupID, true);
		update_user_meta($userID,'create_group',0); update_user_meta($userID,'group_id',0);
		update_user_meta($userID,'enable_donate',0); update_user_meta($userID,'paypal_url',0); update_user_meta($userID,'enable_pic',0); update_user_meta($userID,'enable_graphic',0);
		$groupDetails = ""; $postDetails = ""; $optionDonate = 0; $optionDonateURL = 0; $optionPic = 0; $optionGraphic = 0;
		$getMeta = get_user_meta($userID);
		$groupID = 0; $getMeta['group_id'][0] = 0;
		$getEntry = 0; $getMeta['create_group'][0] = 0;
		$getMeta['enable_donate'][0] = 0; $getMeta['paypal_url'][0] = 0; $getMeta['enable_pic'][0] = 0; $getMeta['enable_graphic'][0] = 0;
		unset($_POST); }
	if ( isset($_POST['patient-edit'] ) ) {
		$changeArrV = array(0,0,0,0,0);
		$editKeyArr = array("patient_firstname","patient_lastname","patient_zip","patient_city","patient_status","patient_desc");
		$edit_fname = sanitize_text_field( $_POST['edit-fname'] );
		if ($edit_fname !== $groupDetails["patient_firstname"][0]) { $changeArrV[0] = $edit_fname; }
		$edit_lname = sanitize_text_field( $_POST['edit-lname'] );
		if ($edit_lname !== $groupDetails["patient_lastname"][0]) { $changeArrV[1] = $edit_lname; }
		$edit_zip = sanitize_text_field( $_POST['edit-zip'] );
		if ($edit_zip !== $groupDetails["patient_zip"][0]) { $changeArrV[2] = $edit_zip; }
		$edit_city = sanitize_text_field( $_POST['edit-city'] );
		if ($edit_city !== $groupDetails["patient_city"][0]) { $changeArrV[3] = $edit_city; }
		$edit_status = $_POST['edit-status'];
		if ($edit_status !== $groupDetails["patient_status"][0]) { $changeArrV[4] = $edit_status; }
		$edit_desc = sanitize_text_field( $_POST['edit-desc'] );
		if ($edit_desc !== $groupDetails["patient_desc"][0]) { $changeArrV[5] = $edit_desc; }
		if ($changeArrV[0] !== 0 || $changeArrV[1] !== 0) { $newPName = $edit_fname." ".$edit_lname;
		$edit_name = array('ID' => $groupID, 'post_title' => $newPName, 'post_content' => $edit_status); $hasEdit = wp_update_post( $edit_name ); }
		if(!empty($_FILES["edit-patientPic"])) { if ( has_post_thumbnail($groupID) ) { $oldID = get_post_thumbnail_id($groupID); } else { $oldID = ""; }	
		med_picture_upload($groupID,$_FILES["edit-patientPic"],"medWeb-".$groupID."-".$userID."-",$oldID,"post","",""); } 
			foreach ($changeArrV as $key => $changeArr) { if ($changeArr !== 0) { if($key !== 5) { $e_key = $editKeyArr[$key];  update_post_meta($groupID, $e_key, $changeArr); } else { $upPatient = array('ID' => $groupID, 'post_content' => $changeArr); $checkNE = wp_update_post($upPatient); } } }
		$postDetails = get_post($groupID); $groupDetails = get_post_meta($groupID);
		unset($_POST); }
		$dSwitch = 0;
		if ( isset($_POST['delegate-submit'] ) ) { $dStringTkn = "delegate"; $dSwitch = 1; }
		else if ( isset($_POST['eDelegate-submit']) ) { $dStringTkn = "eDelegate"; $dSwitch = 2; }
			if ($dSwitch !== 0) { $tagDel1 = 1;
			if ($delegate_fname !== "") { $delegate_fname = sanitize_text_field( $_POST[$dStringTkn.'-fname'] ); } else { $tagDel1 = 0; }
			if ($delegate_lname !== "") { $delegate_lname = sanitize_text_field( $_POST[$dStringTkn.'-lname'] ); } else { $tagDel1 = 0; }
				if ($tagDel1 === 1) { $delegate_cname = $delegate_fname.$delegate_lname; $delegateIdentifier = $userID.$getDelegate; $delegate_user = $delegate_cname.$delegateIdentifier; $delegate_display = $delegate_fname." ".$delegate_lname; $delegate_nice = strtolower($delegate_cname).$delegateIdentifier; }
				else { $delegate_cname = ""; $delegateIdentifier = ""; $delegate_user = ""; $delegate_display = ""; $delegate_nice = ""; }
				$delegate_pass = sanitize_text_field( $_POST[$dStringTkn.'-password'] ); $delegate_mail = sanitize_text_field( $_POST[$dStringTkn.'-email'] ); 
				$delOptFormID = $_POST['delTypeID']; $delOptFormID = explode("-",$delOptFormID); $delOptFormID = intval($delOptFormID[1]);
			if ($dSwitch === 1 && !email_exists($delegate_mail) && !username_exists($delegate_user)) {
				$delegate_data = array('user_login' => $delegate_user, 'user_email' => $delegate_mail, 'user_pass' => $delegate_pass, 'first_name' => $delegate_fname, 'last_name' => $delegate_lname, 'role' => 'subscriber', 'wp_user_level' => '0');
				$delegate = wp_insert_user( $delegate_data ); 
					if ($delegate !== 0 && $delegate !== "WP_ERROR") { $getDelegate = $getDelegate + 1; update_user_meta( $userID, 'delegate_count', $getDelegate); add_user_meta($delegate, 'delegate_id', $delOptFormID);add_user_meta( $delegate, 'group_id', $groupID); add_user_meta($delegate, 'parent_id', $userID); add_user_meta($delegate, 'forum_count', 0); add_user_meta( $userID, 'delegate_id'.$delOptFormID, $delegate); add_user_meta( $userID, 'delegate'.$delOptFormID.'_post_count', 0);  } 
					$email_to = $delegate_mail;
					$clientRequest = "New Delegate Account Created";
					$headers = 'From: account@charlottesmedweb.com
					Reply-To: account@charlottesmedweb.com';
					$clientMsg = "User Name (Login): ".$delegate_user."
					Email Address: ".$delegate_mail."
					Password: ".$delegate_pass."
					First Name: ".$delegate_fname."
					Last Name: ".$delegate_lname."
					Instructions: You have been registered as a representative on CharlottesMedWeb.com. This means that you are authorized to speak on behalf of the ill patient.
					Please visit CharlottesMedWeb.com and log in to post a status update for the patient."
					
					
					;
					@mail($email_to, $clientRequest, $clientMsg, $headers); 
					$getMeta['delegate_count'][0] = $getDelegate; 
					$getMeta['delegate_id'.$getDelegate][0] = $delegate; }
			else if ($dSwitch === 0 && !email_exists($delegate_mail) && !username_exists($delegate_user)) { $callDelMeta = $getMeta['delegate_id'.$delOptFormID][0]; 
				$dumpDel = get_user_by("ID",$callDelMeta); $dumpDelMeta = get_user_meta($callDelMeta);
				$updateDelTrgt = array(0 => array('wp_usermeta','first_name'), 1 => array('wp_usermeta','last_name'), 2 => array('wp_users','user_login'), 3 => array('wp_users','user_nicename'), 4 => array('wp_users','display_name'), 5 => array('wp_users','user_pass'), 6 => array('wp_users','user_email'), 7 => array('wp_usermeta','nickname'));
				$updateEditDel = array($dumpDelMeta['first_name'][0],$dumpDelMeta['last_name'][0],$dumpDel->user_login,$dumpDel->user_nicename,$dumpDel->display_name,NULL,$dumpDel->user_email,$dumpDel->user_login); 
				$runDelLoop = array($delegate_fname,$delegate_lname,$delegate_user,$delegate_nice,$delegate_display,$delegate_pass,$delegate_mail,$delegate_user);
				for ($r = 0; $r <= 7; $r++) { $newEDValue = $runDelLoop[$r]; $oldEDValue = $updateEditDel[$r];
					if ($newEDValue !== "" && $newEDValue !== $oldEDValue) { 
					$unpackDelTrgt = $updateDelTrgt[$r]; $UPDT1 = $unpackDelTrgt[0]; $UPDT2 = $unpackDelTrgt[1];
						if ($UPDT2 === "user_login") { $wpdb->update($wpdb->users, array('user_login' => $newEDValue), array('ID' => $callDelMeta)); }
						else if ($oldEDValue === NULL) { $newEDValue = wp_hash_password($newEDValue); $wpdb->update($wpdb->users, array('user_pass' => $newEDValue), array('ID' => $callDelMeta)); }
						else { if ($UPDT1 === "wp_users") { wp_update_user(array('ID' => $callDelMeta, $UPDT2 => $newEDValue)); }
						else if ($UPDT1 === "wp_usermeta") { update_user_meta($callDelMeta,$UPDT2,$newEDValue); } }
						}
					}
			} else { echo "Error: Duplicate Account Or Incomplete Form Detected"; } unset($_POST); }
				
	if( isset($_POST['submitDelRmv']) ) { 
	$getRepTrgt = $_POST['delTypeID'];
	$getRepTrgt = explode("-",$getRepTrgt);
	$delegate_del_id = intval($getRepTrgt[1]);
	$delegate_del_id = str_replace("-","",$delegate_del_id);
	$find_delete_trgt = intval($getRepTrgt[0]);
	$find_delegate_comments = 0;
		if ($delegatePostCnt !== 0) { $find_delegate_comments = intval($getMeta['delegate'.$delegate_del_id.'_post_count'][0]); 
			if ($find_delegate_comments !== 0) {
				$remove_delegate_var = array('post_id' => $groupID, 'user_id' => $find_delete_trgt, 'orderby' => 'comment_date');
				$remove_allD_comments = get_comments($remove_delegate_var);
				$cntComDRmv = 0;
				foreach($remove_allD_comments as $rmv_all) { 
				$crnt_comment_meta = get_comment_meta($rmv_all->comment_ID,'comment_pic',true);
				if (!empty($crnt_comment_meta)) { $rmv_pic_id = $wpdb->get_var("SELECT ID FROM wp_posts WHERE guid = '".$crnt_comment_meta."'"); wp_delete_attachment($rmv_pic_id, true); }
				wp_delete_comment($rmv_all->comment_ID,true); $cntComDRmv++;
				} } } wp_delete_user($find_delete_trgt); 
	unset($getMeta['delegate_id'.$delegate_del_id]);
	$originalCount = $getDelegate; $getDelegate = $getDelegate - 1; $getMeta['delegate_count'][0] = $getDelegate;
	$delegatePostCnt = $delegatePostCnt - $cntComDRmv; $getMessage = $getMessage - $cntComDRmv; $getMeta['forum_count'][0] = $getMessage;
	delete_user_meta($userID,'delegate_id'.$delegate_del_id); update_user_meta($userID,'delegate_count',$getDelegate);  update_user_meta($userID,'forum_count',$getMessage); 
	if ($delegate_del_id < $originalCount) { $newDelAdd = 'delegate_id'.$originalCount; $NewFPsts = "delegate".$originalCount."_post_count"; $oldDel2ID = get_user_meta($userID,$newDelAdd,true); $oldForumC = get_user_meta($userID,$NewFPsts,true); update_user_meta($userID,'delegate_id'.$delegate_del_id,$oldDel2ID); update_user_meta($oldDel2ID,'delegate_id',$delegate_del_id); update_user_meta($userID,"delegate".$delegate_del_id."_post_count",$oldForumC); 
	delete_user_meta($userID, $NewFPsts); delete_user_meta($userID, $newDelAdd); }
	$getMeta = get_user_meta($userID); $outputComments = retrieve_med_status();
	unset($_POST); }
	}
else if ($getEntry === 0) {
	$startStyle = "style='min-height:50vh;'";
	if ( isset($_POST['patient-submit'] ) ) {
		$patient_fname = sanitize_text_field( $_POST['patient-fname'] );
		$patient_lname = sanitize_text_field( $_POST['patient-lname'] );
		$patient_name = $patient_fname." ".$patient_lname;
		$patient_zip = sanitize_text_field( $_POST['patient-zip'] );
		$patient_city = sanitize_text_field( $_POST['patient-city'] );
		$patient_status = $_POST['patient-status'];
		$patient_desc = sanitize_text_field( $_POST['patient-desc'] );
		$patient_content = "Patient Name: ".$patient_name;
		$patient_post = array('post_title' => wp_strip_all_tags($patient_name), 'post_content' => $patient_desc, 'post_status' => 'publish', 'post_author' => $userID, 'post_category' => array('Patients'), 'post_type' => 'patient-pages');
		$new_groupID = wp_insert_post($patient_post);
		if ($new_groupID !== 0 && $new_groupID !== "WP_ERROR") { med_picture_upload($new_groupID,$_FILES["patientPic"],"medWeb-".$new_groupID."-".$userID."-","","post","","");
		update_user_meta( $userID, 'create_group', 1);
		update_user_meta( $userID, 'group_id', $new_groupID);
		add_post_meta($new_groupID, "patient_firstname", $patient_fname);
		add_post_meta($new_groupID, "patient_lastname", $patient_lname);
		add_post_meta($new_groupID, "patient_zip", $patient_zip);
		add_post_meta($new_groupID, "patient_city", $patient_city);
		$getEntry = 1; $groupID = $new_groupID; $groupDetails = get_post_meta($groupID); $postDetails = get_post($groupID); }
		unset($_POST);
	}
}
?>
