$(function() {
	var who, what, prNo, sig;
	var bidderCount = [];
	var abstractData = [];
 	var itemID = [];
 	var groupNumber = [];
 	var date = "";
 	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;

	function submitPrint() {
		fontScale = $("#txtIncreaseSize").val();
		paperSize = $("#selPaperSize").val();

		$("#print").val(who);
		$("#what").val(what);
		$("#qtn").val(prNo);
		$("#sig").val(sig);
		$("#inputDate").val(date);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);
		$("#frmSize").submit();
	}

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
									 "preview=true&print=" + who + "&what=" + what + "&qtn=" + prNo +
									 "&font-scale=" + fontScale + "&paper-size=" + paperSize + "&inputDate=" + date +
									 "&sig=" + sig);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "1";
			fontScale = 0;
			date = "";
			toggleModal = 0;
			$("#print-content").attr("src", "");
			$("#canvass-content").html('<h3 class="font-color-1">Loading...</h3>');
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}

	function updateDB(excludeData, groupData) {
		var _data = [excludeData, groupData];
		var postData = new FormData();

		$.each($('#file-canvass')[0].files, function(i, file) {
	        postData.append('file-'+i, file);
	    });

	    postData.append('toggle', 'file-upload');
	    postData.append('prNo', prNo);
	    postData.append('form-type', 'canvass');

		$.ajax({
	        url: 'db_operation.php',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	console.log(result);
	        },
	        error: function(xhr, result, errorThrown){
	            console.log('Request failed.');
	        }
        });

		$.post('db_operation.php', {
			prID: who,
		    data: _data,
		    toggle: "exclude-update"
		}).done(function(data) {
			toggleModal = 1;
			$("#modal-print-1").modal("hide");
		}).fail(function(xhr, status, error) {

		});
	}

	function continueToPrint() {
		var excludeData = [];
		var groupData = [];

		$.post('db_operation.php', {
		    prID: who,
		    toggle: "delete-abstract"
		}).done(function(data) {
			$('select.exclude-option').find('option:selected').each(function() {
			    excludeData.push($(this).val());
			});

			$('select.group-option').find('option:selected').each(function() {
			    groupData.push($(this).val());
			});

			updateDB(excludeData, groupData);
		}).fail(function(xhr, status, error) {

		});
	}

	$("#txtDate").datetimepicker({
		viewMode: 'days',
		format: 'MM/DD/YYYY'
	});

	$("#btn-print").unbind("click").click(function() {
		submitPrint();
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + who + "&what=" + what + "&qtn=" + prNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize + "&inputDate=" + date +
										"&sig=" + sig);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + who + "&what=" + what + "&qtn=" + prNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize + "&inputDate=" + date +
										"&sig=" + sig);
	});

	$("#row_0").find("a").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$.fn.showPrintDialog = function(_who, _what, _qtn, _prNo) {
		who = _who;
		what = _what;
		prNo = _prNo

		$('.tooltip').remove();

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#canvass-content").load("canvass_items.php", {
									 "pid": who,
								     "prno":  prNo,
								  	 "toggle": "exclude-settings"
			});

			$("#btn-ok").unbind("click").click(function(e) {
				date = $("#txtDate-val").val();
				sig = $("#selApp").val();
				e.preventDefault();
				continueToPrint();
			});
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			toggleModal = 0;
			$("#canvass-content").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.deleteFile = function(key, prNo, fileName) {
		$.post('db_operation.php', {
		    prID: prNo,
		    data: fileName,
		    toggle: "delete-file"
		}).done(function(data) {
			alert(data);
			$("#del-" + key).fadeOut("slow");
		}).fail(function(xhr, status, error) {

		});
	}

	//====================================================
	// Old

	function removeBidder(who,cname) {
		if (confirm("Are you sure you want to remove " + cname + " as a bidder of this PR?")) {
			document.frmDel.delBidder.value = who;
			document.frmDel.submit();
		}
	}

	function checkIt(evt) {
		var charCode = (evt.which) ? evt.which : event.keyCode;

		if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
		//alert("Please make sure entries are numbers only.")
			return false
		}
		return true
	}
});