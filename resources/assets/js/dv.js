$(function() {
	var id, what, prID, poNo;
	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;
	var element;

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
									 "preview=true&print=" + id + "&what=" + what + "&poNo=" + poNo +
									 "&font-scale=" + fontScale + "&paper-size=" + paperSize);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "1";
			fontScale = 0;
			date = "";
			toggleModal = 0;
			$("#print-content").attr("src", "");
			$("#dv-content").html('<h3 class="font-color-1">Loading...</h3>');
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}

	function inputValidation1(withError, _element) {
		var errorCount = 0;
		//input-unit-cost

		_element.each(function() {
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

	function printDV() {
		$("#print").val(id);
		$("#what").val(what);
		$("#frmSize").submit();
		document.frmSize.print.value = id;
	}

	function saveDV() {
		var dvNo = $("#txtDvNo").val();
		var dvDate = $("#txtDvDate input").val();
		var particulars = $("#txtParticulars").val();
		var mds = $("input[name='check-mds']:checked").val();
		var commercial = $("input[name='check-commercial']:checked").val();
		var ada = $("input[name='check-ada']:checked").val();
		var others = $("input[name='check-others']:checked").val();

		if (!mds) {
			mds = "0";
		}

		if (!commercial) {
			commercial = "0";
		}

		if (!ada) {
			ada = "0";
		}

		if (!others) {
			others = "0";
		}

		paymentMode = mds + "-" + commercial + "-" + ada + "-" + others;

		dvData = [ particulars,
				   paymentMode, 
				   prID, poNo,
				   dvNo, dvDate ];

		element.val(dvNo);

		$.post('db_operation.php', {
		    data: dvData,
		    prID: id,
		    toggle: "save-dv"
		}).done(function(data) {
			$("#particulars-" + id).html(particulars);
			//printDV();
			toggleModal = 1;
			$("#modal-print-dv").modal("hide");
		}).fail(function(xhr, status, error) {
			saveDV();
		});
	}

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + id + "&what=" + what + "&poNo=" + poNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + id + "&what=" + what + "&poNo=" + poNo +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#btn-ok").unbind("click").click(function() {
		var withError = inputValidation(false);

		if (!withError) {
			saveDV();
		}
	});

	$("#btn-print").unbind("click").click(function() {
		printDV();
	});

	$.fn.checkItem = function(selected, who, action) {
		if(confirm("Are you sure you want to "+action+" '"+selected+"' from the lists?")){
			document.getElementById(who).checked=1;
			document.frmPRPost.hdAction.value=action;
			document.frmPRPost.submit();
		}
	}

	$.fn.printDialog = function(who, _what, _prID, _poNo, _element) {
		id = who;
		what = _what;
		prID = _prID;
		poNo = _poNo;
		element = $("#" + _element);

		$('.tooltip').remove();

		$("#modal-print-dv").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#dv-content").load("dv_op.php", { 
								  "orsID": id
								}, function() {
									$("#txtDvDate").datetimepicker({
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
			$("#dv-content").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	function approveToPayment(paymentData, _poNo) {
		$.post('db_operation.php', {
		    data: paymentData,
		    toggle: "to-payment"
		}).done(function(data) {
			window.location = "dv.php?po_no=" + _poNo;
			return false;
		}).fail(function(xhr, status, error) {
			approveToPayment(paymentData, _poNo);
		});
	}

	$.fn.saveToPayment = function(_prID, _poNo, _element) {
		var dvNo = $("#" + _element).val();
		var withError = inputValidation1(false, $("#" + _element));

		if (!withError) {
			if(confirm("Approve this PO/JO No. " + _poNo + " to payment?")) {
				var paymentData = [_prID, _poNo];

				approveToPayment(paymentData, _poNo);
			}
		} else {
			window.location = "dv.php?result=1&po_no=" + _poNo;
			return false;
		}
		
	}
});