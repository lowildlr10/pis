$(function() {
	var poNo, what, who, orsID;
	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
									 "preview=true&print=" + who + "&what=" + what + "&poNo=" + poNo +
									 "&font-scale=" + fontScale + "&paper-size=" + paperSize);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "1";
			fontScale = 0;
			toggleModal = 0;
			$("#print-content").attr("src", "");
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}

	function inputValidation(withError) {
		var errorCount = 0;
		//input-unit-cost

		$(".required").each(function() {
			var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

			if (inputField == 0) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(this).removeClass("input-error-highlighter");
			}
		});

		if (errorCount == 0) {
			withError = false;
		} else {
			withError = true;
		}

		return withError;
	}


	function submitPrint() {
		$("#print").val(who);
		$("#what").val(what);
		$("#poNo").val(poNo);

		$("#frmSize").submit();
	}

	function saveIAR() {
		var iarDate = $("#txtIarDate").val();
		var invoiceNo = $("#txtInvoiceNo").val();
		var invoiceDate = $("#txtInvoiceDate").val();
		var inspectedBy = $("#sel-insp-by").val();
		var supplySign = $("#sel-supply").val();

		iarData = [ iarDate,
					invoiceNo,
					invoiceDate,
					inspectedBy,
					supplySign ];

		$.post('db_operation.php', {
		    data: iarData,
		    prID: orsID,
		    toggle: "update-iar"
		}).done(function(data) {
			//submitPrint();
			toggleModal = 1;
			$("#modal-print-1").modal("hide");
		}).fail(function(xhr, status, error) {
			saveIAR();
		});
	}

	function finalizeIAR(prID, poNo, finalizedData) {
		$.post('db_operation.php', {
		    data: finalizedData,
		    prID: prID,
		    poNo: poNo,
		    toggle: "finalize-iar"
		}).done(function(data) {

		}).fail(function(xhr, status, error) {
			finalizeIAR(prID, finalizedData);
		});
	}

	function proccessFinalizeData() {
		var poNo = $("#input-po-no").val();
		var finalizedData = [];
		var prID = $("#input-pr-id").val();
		var itemID = [];
		var infoID = [];
		var classification = [];
		var itemClass = [];
		var documentNo = [];
		var quantity = [];
		var itemNo = [];
		var groupNo = [];

		$(".input-item-id").each(function() {
			itemID.push($(this).val());
		});

		$(".input-info-id").each(function() {
			infoID.push($(this).val());
		});

		$(".input-classification").each(function() {
			classification.push($(this).val());
		});

		$(".input-item-class").each(function() {
			itemClass.push($(this).val());
		});

		$(".input-document-no").each(function() {
			documentNo.push($(this).val());
		});

		$(".input-item-no").each(function() {
			itemNo.push($(this).val());
		});

		$(".input-quantity").each(function() {
			quantity.push($(this).val());
		});

		$(".input-group").each(function() {
			groupNo.push($(this).val());
		});

		$.ajaxSetup({async: false});

		$.each(itemID, function(i, _itemID) {
			finalizedData = [_itemID, infoID[i], classification[i], 
							 documentNo[i], itemNo[i], itemClass[i],
							 quantity[i], groupNo[i]];

			finalizeIAR(prID, poNo, finalizedData);
		});

		$.ajaxSetup({async: true});

		alert("IAR successfully finalized!");
		window.location = "inventory.php?po_no=" + poNo;
		return false;
	}

	$("#btn-ok").unbind("click").click(function() {
		saveIAR();
	});

	$("#btn-print").unbind("click").click(function() {
		saveIAR();
	});

	$("#row_0 a").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$("#row_0 input[type='checkbox']").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + who + "&what=" + what + "&poNo=" + poNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + who + "&what=" + what + "&poNo=" + poNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$.fn.showPrintDialog = function(_poNo, _what, _prID, _orsID) {
		poNo = _poNo;
		what = _what;
		who = _prID;
		orsID = _orsID;

		$('.tooltip').remove();

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content-1").load("iar_op.php", { 
								  "orsID": orsID,
								  "poNo": poNo
								}, function() {
									$("#iarDate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY'
									});

									$("#invoiceDate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY'
									});
								});
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			toggleModal = 0;
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.showFinalizeDialog = function(_poNo, _what, _prID) {
		poNo = _poNo;
		what = _what;
		who = _prID;

		$('.tooltip').remove();

		$("#modal-finalize").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content-2").load("iar_finalize_op.php", { 
								  "orsID": orsID,
								  "poNo": poNo
								}, function() {
									$("#iarDate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY'
									});

									$("#invoiceDate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY'
									});

									$("#btn-finalize").unbind("click").click(function() {
										var withError = inputValidation(false);

										if (!withError) {
											proccessFinalizeData();
										}
									});
								});
		}).on("hidden.bs.modal", function() {
			$("#print-content-2").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	function approveForDV(dvData, _poNo) {
		$.post('db_operation.php', {
			prID: _poNo,
		    data: dvData,
		    toggle: "to-dv"
		}).done(function(data) {
			window.location = "dv.php?po_no=" + _poNo;
			return false;
		}).fail(function(xhr, status, error) {
			approveForDV(dvData, _poNo);
		});
	}

	$.fn.saveDV = function(_prID, _orsID, _poNo) {
		if(confirm("Approve this PO/JO No. " + _poNo + " for disbursement?")) {
			var dvData = [_prID, _orsID];

			approveForDV(dvData, _poNo);
		}
	}
});