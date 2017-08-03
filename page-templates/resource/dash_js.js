jQuery(document).ready(function( $ ) {
		$(".app-nav-btn").on("click", function() {
		var checkTabClass = $(this).attr("class");
		checkTabClass = checkTabClass.split(" ");
		var checkLength = checkTabClass.length;
		if (checkLength == 1) {
		var getTabID = $(this).attr("id");
		var newTabID = getTabID.split("-");
		newTabID = newTabID[1];
		var oldTabID = $(".active-tab").attr("id");
		var oldTab = oldTabID.split("-");
		oldTab = oldTab[1];
		$("#appNav-"+oldTab).removeClass("active-tab");
		$("#tab"+oldTab).stop().fadeOut(300);
		setTimeout(function() {	$("#appNav-"+newTabID).addClass("active-tab"); $("#tab"+newTabID).stop().fadeIn(300) }, 300); }
		});
	var stop1 = 0; var getFData = 0;
		$("#regOption").on("click", function() {
		if (stop1 == 0) { getFData = $("input[name=selectSelfOr]:checked").val(); stop1 = 1; }
		if (getFData == 1) {
			$("#formInstruct").stop().fadeOut(500);
			setTimeout(function() { $("#patient-reg").stop().fadeIn(500); }, 500); }
		else if (getFData == 2) {
			$("#question1Reg").stop().fadeOut(500);
			setTimeout(function() { $("#question2Reg").stop().fadeIn(500); }, 500); 
			getFData = 1; }
		});
		
		$(".openEdit").on("click",function() { var editParent = $(this).parent().parent(); var noShow1 = editParent.find(".commentInner"); var showEdit1 = editParent.find(".editComForm"); noShow1.hide(); showEdit1.show(); });
		$(".closeEdit").on("click",function() {	var editParent = $(this).parent().parent(); var noShow2 = editParent.find(".commentInner"); var showEdit2 = editParent.find(".editComForm"); noShow2.show(); showEdit2.hide(); });
						
		$(".picUpload").change(function () {
		var reader = new FileReader();
		reader.readAsDataURL(this.files[0]);
		reader.onload = function (e) {  $("#picPreview img").attr('src', e.target.result); $("#picPreview img").attr('srcset', ''); }
		});
		
		$("#add-commentPic").change(function () {
		$("#prevNewPostImg").html("<img id='newComPic' style='width:100%' />");
		var reader2 = new FileReader();
			if (this.files[0] != undefined) {
			reader2.readAsDataURL(this.files[0]);
			reader2.onload = function (ev) { $("#prevNewPostImg").css({"display":"initial"}); $("#newComPic").attr('src', ev.target.result); var nHeight = $("#statusBox").height(); $("#sBox2 textarea").css({"height":nHeight}); }
			$("#removeAddPicX").css({"display":"initial"});
			} else { $("#prevNewPostImg").css({"height":"0"}); $("#sBox2 textarea").css({"height":"10.2vh"}); $("#removeAddPicX").css({"display":"none"}); }
		});
		
		$(".edit-commentPic").change(function () {
		var buttonDiv = $(this).parent();
		var prevEdit = buttonDiv.parent().find('.prevEditPostImg');
		var vClass = prevEdit.attr("class");
		vClass = vClass.split(" ");
		vClass = vClass[1];
		var reader3 = new FileReader();
			if (this.files[0] != undefined) {
			reader3.readAsDataURL(this.files[0]); 
			reader3.onload = function (ev) {
			if (vClass == "noImage") {
			prevEdit.removeClass("noImage").addClass("hasImage").html("<img class='editComPic' style='width:100%' />"); prevEdit.find('img').attr('src',''); 
			buttonDiv.find('.picFormID').val(0); 
			buttonDiv.find(".setAsGSpan").css({"display":"initial"}); buttonDiv.find(".setAsGraphic").css({"display":"initial"}); buttonDiv.find(".delTxt").css({"display":"initial"}); buttonDiv.find(".patientDelPic").css({"display":"initial"});
			buttonDiv.find(".replacePicTxt").empty(); buttonDiv.find(".replacePicTxt").text("Replace Picture: "); }
			var thisImage = prevEdit.parent().find("img");
			thisImage.attr('src', ev.target.result);
				}
			}
		});
		
		$("#removeAddPicX").mousedown(function() { 
		$(this).parent().find("#prevNewPostImg").empty();
		$(this).parent().find("#add-commentPic").val("");
		$("#removeAddPicX").css({"display":"none"});
		$("#prevNewPostImg").css({"display":"none"});
		$("#add-details").css({"height":"11.8vh"});
		});
				
		$(".patientDelPic").mousedown(function() { 
		var prevEdit = $(this).parent().parent().find('.prevEditPostImg');
		var buttonDiv = $(this).parent();
		var vClass = prevEdit.attr("class");
		vClass = vClass.split(" ");
		vClass = vClass[1];
		if (vClass == "hasImage") {	prevEdit.removeClass("hasImage").addClass("noImage").empty(); prevEdit.find('img').attr('src',''); 
		buttonDiv.find('.picFormID').val(1); 
		buttonDiv.find(".setAsGSpan").css({"display":"none"}); buttonDiv.find(".setAsGraphic").css({"display":"none"}); buttonDiv.find(".delTxt").css({"display":"none"}); buttonDiv.find(".patientDelPic").css({"display":"none"}); 
		buttonDiv.find(".replacePicTxt").empty(); buttonDiv.find(".replacePicTxt").text("Add Picture: "); buttonDiv.find(".edit-commentPic").empty();
			}
		});
		
		$(".ePicHover").hover(function() { $(".delPPic").fadeIn(300); }, function() { $(".delPPic").fadeOut(300); });
		
		$("#sortBySelect").change(function () {
			var getSelectVal = $(this).val();
			var setV1 = ""; var setV2 = "";
			if (getSelectVal == "all-post") { var setV1 = "initial"; var setV2 = "initial"; }
			else if (getSelectVal == "user-post") { var setV1 = "initial"; var setV2 = "none"; }
			else if (getSelectVal == "not-user") { var setV1 = "none"; var setV2 = "initial"; }
			$(".user-post").css({"display":setV1});
			$(".not-user").css({"display":setV2});
		});
		
		$("#enable-donate").change(function() {
			var cValue = $(this).val();
			if (cValue == 1) { $(".url-holder").css({"display":"initial","height":"auto"}); }
			else if (cValue == 0) { $(".url-holder").css({"display":"none","height":"0"}); $(".url-holder").empty(); }
		});
	});