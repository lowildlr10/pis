$(function() {
	var who, what, orsNo, poNo, prID;
	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;
	var element = "";

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&orsNo=" + orsNo + "&font-scale=" + fontScale + "&paper-size=" + paperSize);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "1";
			fontScale = 0;
			toggleModal = 0;
			$("#print-content").attr("src", "");
			$("#ors-content").html('<h3 class="font-color-1">Loading...</h3>');
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

	function saveORS() {
		//var orsNo = $("#txtORS").val();
        what = $("#txtDocType").val();

		var orsNo = who;
		var serialNo = $("#txtSerialNo").val();
		var orsDate = $("#txtOrsDate input").val();
		var payee = $("#txtPayee").val();
		var office = $("#txtOffice").val();
		var address = $("#txtAddress").val();
		var particulars = $("#txtParticulars").val();
		var objectCode = $("#txtAcntCode").val();
		var signatory1 = $("#selCert1").val();
		var signatory2 = $("#selCert2").val();
		var signDate1 = $("#txtCDate1").val();
		var signDate2 = $("#txtCDate2").val();

		var inputArray = $("#" + element).val();

		$("#" + element).val(inputArray + serialNo);

		orsData = [ who,
					payee, 
					office, 
					address,
					particulars,
					objectCode,
					signatory1,
					signatory2,
					signDate1,
					signDate2, 
					prID,
					serialNo,
					orsDate,
                    what ];

		$.post('db_operation.php', {
		    data: orsData,
		    prID: who,
		    toggle: "save-ors"
		}).done(function(data) {
			$("#particulars-" + who).html(particulars);
			toggleModal = 1;
			$("#modal-print-ors").modal("hide");
		}).fail(function(xhr, status, error) {
			saveORS();
		});
	}

	/*
	function saveCustomORS() {
		//var orsNo = $("#txtORS").val();
		var orsNo = who;
		var payee = $("#txtPayee").val();
		var office = $("#txtOffice").val();
		var address = $("#txtAddress").val();
		var particulars = $("#txtParticulars").val();
		var objectCode = $("#txtAcntCode").val();
		var amount = $("#txtAmount").val();
		var signatory1 = $("#selCert1").val();
		var signatory2 = $("#selCert2").val();
		var signDate1 = $("#txtCDate1").val();
		var signDate2 = $("#txtCDate2").val();
		var userID = $("#userID").val();
		var sectionID = $("#sectionID").val();

		orsData = [ orsNo ,
					payee , 
					office , 
					address ,
					particulars ,
					objectCode ,
					amount ,
					signatory1 ,
					signatory2 ,
					signDate1,
					signDate2,
					userID,
					sectionID ];

		$.post('db_operation.php', {
		    data: orsData,
		    prID: who,
		    toggle: "save-ors"
		}).done(function(data) {
			//$("#info-title").html("SUCCESS!").css("color", "#3c763d;");
			//$("#info-msg").html("Abstract saved.").css("color", "#3c763d;");
			//infoDialog.dialog("open");
			window.location = "obligation_request.php";
			return false;
		}).fail(function(xhr, status, error) {
			saveCustomORS()
			//$("#info-title").html("ERROR!").css("color", "#a94442;");
			//$("#info-msg").html("Something went wrong while generating preview. " + 
			//					"Please try again.").css("color", "#a94442;");
			//infoDialog.dialog("open");
		});
	}*/

	function deleteCustomORS(id) {
		$.post('db_operation.php', {
			prID: id,
		    toggle: "delete-ors"
		}).done(function(data) {
			alert("(" + id + ") is deleted.");
			window.location = "obligation_request.php";
		}).fail(function(xhr, status, error) {
			deleteCustomORS(id);
		});
	}

	function printORS() {
		$("#print").val(who);
		$("#what").val(what);
		$("#orsNo").val(orsNo);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);
		$("#frmSize").submit();
	}

	function showSupplierAddress(bidderID) {
		$.post('db_operation.php', {
		    data: bidderID,
		    toggle: "show-address"
		}).done(function(data) {
			$("#txtAddress").val(data);
		}).fail(function(xhr, status, error) {
			showSupplierAddress(bidderID);
		});
	}

	$("#btn-print").unbind("click").click(function() {
		printORS();
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&orsNo=" + orsNo + "&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&orsNo=" + orsNo + "&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#btn-create").unbind("click").click(function() {
		var withError = inputValidation(false);

		if (!withError) {
			saveCustomORS();
		}
	});

	$.fn.checkItem = function(selected, who, action) {
		if (confirm("Are you sure you want to "+action+" '"+selected+"' from the lists?")) {
			document.getElementById(who).checked=1;
			document.frmPRPost.hdAction.value=action;
			document.frmPRPost.submit();
		}
	}

	$("#btn-ok").unbind("click").click(function() {
		var withError = inputValidation(false);

		if (!withError) {
			saveORS();
		}
	});

	$.fn.printDialog = function(_who, _what, _orsNo, _poNo, _prID, _element) {
		who = _who;
		what = _what;
		orsNo = _orsNo;
		poNo = _poNo;
		prID = _prID;
		element = _element;

		$('.tooltip').remove();

		$("#modal-print-ors").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content-1").load("obligation_request_op.php", { 
									"poNo": poNo,
									"pid": who
								}, function() {
									$(function() {
								        $("#txtDate1").datetimepicker({
								            viewMode: 'days',
								            format: 'MM/DD/YYYY'
								        });

								        $("#txtDate2").datetimepicker({
								            viewMode: 'days',
								            format: 'MM/DD/YYYY'
								        });

								        $("#txtPayee").change(function() {
								        	var bidderID = $(this).val();
											showSupplierAddress(bidderID);
										});

								        $("#txtAmount").keydown(function(event) {
											if (event.shiftKey == true) {
										        event.preventDefault();
										    }

										    if (!((event.keyCode >= 48 && event.keyCode <= 57) || 
										        (event.keyCode >= 96 && event.keyCode <= 105) || 
										         event.keyCode == 8 || event.keyCode == 37 ||
										         event.keyCode == 39 || event.keyCode == 46 || 
										         event.keyCode == 190)) {
										    	
										    	event.preventDefault();
										    }

										    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190) {
								        		event.preventDefault(); 
										    }
										});

										$("#txtOrsDate").datetimepicker({
											viewMode: 'days',
											format: 'MM/DD/YYYY'
										});
								    });
								});
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			toggleModal = 0;
			$("#ors-content").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.createDialog = function() {
		$('.tooltip').remove();

		$("#modal-create-ors").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#ors-content").load("obligation_request_op.php", { 
									"poNo": ""
								}, function() {
									$(function() {
										$("#txtPayee").removeAttr("disabled");

								        $("#txtDate1").datetimepicker({
								            viewMode: 'days',
								            format: 'MM/DD/YYYY'
								        });

								        $("#txtDate2").datetimepicker({
								            viewMode: 'days',
								            format: 'MM/DD/YYYY'
								        });

								        $("#txtPayee").change(function() {
											var bidderID = $(this).val();
											showSupplierAddress(bidderID);
										});

								        $("#txtAmount").keydown(function(event) {
											if (event.shiftKey == true) {
										        event.preventDefault();
										    }

										    if (!((event.keyCode >= 48 && event.keyCode <= 57) || 
										        (event.keyCode >= 96 && event.keyCode <= 105) || 
										         event.keyCode == 8 || event.keyCode == 37 ||
										         event.keyCode == 39 || event.keyCode == 46 || 
										         event.keyCode == 190)) {
										    	
										    	event.preventDefault();
										    }

										    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190) {
								        		event.preventDefault(); 
										    }
										});
								    });
								});
		}).on("hidden.bs.modal", function() {
			$("#ors-content").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.deleteDialog = function(id) {
		$("#modal-delete-ors").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#btn-delete").unbind("click").click(function() {
				deleteCustomORS(id);
			});
		});
	}

	$.fn.checkItem = function(selected,who,action) {
		if(confirm("Are you sure you want to " + action + " this PO/JO No.'" + selected + "'?")) {
			document.getElementById(who).checked = 1;
			document.frmPRPost.hdAction.value = action;
			document.frmPRPost.submit();
		}
	}

	function approveToIAR(iarData, _poNo) {
		$.post('db_operation.php', {
			prID: _poNo,
		    data: iarData,
		    toggle: "to-iar"
		}).done(function(data) {
			window.location = "iar.php?po_no=" + _poNo;
			return false;
		}).fail(function(xhr, status, error) {
			approveToIAR(iarData, _poNo);
		});
	}

	$.fn.saveIAR = function(_poNo, _prID, _orsID) {
		if(confirm("Approve this PO/JO No. " + _poNo + " for inspection?")) {
			var iarNo = "IAR-" + _poNo;
			var iarData = [_prID, _orsID, iarNo];

			approveToIAR(iarData, _poNo);
		}
	}
});