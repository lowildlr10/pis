$(function() {
	var poData = [];
	var joData = [];
	var poNo, what, prID;
	var amountWord = "";
	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;
	var toggleOpen = 1;

	// For what = po_jo
	var po = false;
	var jo = false;

	function submitPrintPO() {
		$("#print").val(poNo);
		$("#what").val("po");
		$("#prID").val(prID);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);

		$("#frmPrintPO").submit();
	}

	function submitPrintJO() {
		$("#jo-print").val(poNo);
		$("#jo-what").val("jo");
		$("#jo-prID").val(prID);
		$("#jo-font-scale").val(fontScale);
		$("#jo-paper-size").val(paperSize);
		$("#amount-word").val(amountWord);

		$("#frmPrintJO").submit();
	}

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			if (toggleModal == 1) {
				var qtyItems = $("#qtyItems").val();
				var itemDesc = $("#itemDesc").val();
				var unitCost = $("#unitCost").val();
				var totalAmount = $("#totalAmount").val();
				
				$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
												"preview=true&print=" + poNo + "&what=po" + 
												"&prID=" + prID + 
												"&qtyItems=" + qtyItems + 
												"&itemDesc=" + encodeURIComponent(itemDesc) +
												"&unitCost=" + unitCost +
												"&totalAmount=" + totalAmount +
												"&amount-word=" + amountWord);
			} else if (toggleModal == 2) {
				var workDesc = $("#workDesc").val();

				$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
												"preview=true&print=" + poNo + "&what=jo" + 
												"&prID=" + prID + "&amount-word=" + amountWord +
												"&workDesc=" + encodeURIComponent(workDesc));
			}

			if (what == "po_jo") {
				if (toggleModal == 1) {
					$("#btn-exit").html('Continue to JO... <span class="glyphicon glyphicon-arrow-right"></span>');
				}
			}
			
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "1";
			fontScale = 0;
			$("#print-content").attr("src", "");
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);

			if (what != "po_jo") {
				toggleModal = 0;
			} else if (what == "po_jo") {
				if (toggleModal == 1) {
					jo = true;
					po = false;
					toggleModal = 2;
					toggleOpen = "0";
					$("#btn-exit").html('Cancel');
					$("#modal-print-1").modal("show");
					$("#print-content-1").load("job_order_op.php", 
								{ "poNo": poNo,
								  "prID": prID },
								function() {
									if (po) {
										po = false;
									}

									$("#txtJODate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY',
										sideBySide: true,
										widgetPositioning: {
									        horizontal: 'left',
									        vertical: 'bottom'
									    }
									});
								});
				} else if (toggleModal == 2) {
					po = false;
					jo = false;
					toggleModal = 0;
					toggleOpen = 1;
				}
			}

			/*
			if (!po && jo && (what == "po_jo") && (toggleModal == 1)) {
				$("#modal-print-1").modal("show");
				$("#btn-exit").html('Cancel');
				$("#print-content-1").load( "job_order_op.php", 
								{ "poNo": poNo,
								  "prID": prID },
								function() {
									if (po) {
										po = false;
									}

									$("#txtJODate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY',
										sideBySide: true,
										widgetPositioning: {
									        horizontal: 'left',
									        vertical: 'bottom'
									    }
									});
								});
			}

			if (!po && jo && (what == "po_jo") && (toggleModal == 2)) {
				console.log("end");
				po = false;
				jo = false;
				toggleModal = 0;
				toggleOpen = 1;
			}*/
		});
	}

	function computeGrandTotal() {
		$(".input-qnty").keyup(function(event) {
			var tableRowData = $(event.currentTarget).closest("tr");
			var qnty = tableRowData.find('.input-qnty').val();
			var itemDescription = tableRowData.find('.input-description').val();
			var unitCost = tableRowData.find('.input-cost').val();
			var total = 0;
			var grandTotal = 0;

			if (!qnty) {
				qnty = 0;
			}

			total = parseFloat((qnty * unitCost).toFixed(2));
		    tableRowData.find('.input-total').val(total.toFixed(2));

		    $('.input-total').each(function(){
			    grandTotal += parseFloat($(this).val());
			})

			$("#grand-total").val(grandTotal.toFixed(2));
		});
	}

	function checkIntegerInput() {
		$(".input-qnty").keydown(function(event) {
			if (event.shiftKey == true) {
		        event.preventDefault();
		    }

		    if (!((event.keyCode >= 48 && event.keyCode <= 57) || 
		        (event.keyCode >= 96 && event.keyCode <= 105) || 
		         event.keyCode == 8 || event.keyCode == 37 ||
		         event.keyCode == 39 || event.keyCode == 46)) {
		    	
		    	event.preventDefault();
		    }
		});
	}

	function inputValidation(withError, printType) {
		var errorCount = 0;
		var poDate = 0;
		var deliveryTerm = 0;
		var totalAmountWords = 0;
		var elemPoDate = "";
		var elemDeliveryTerm = "";
		var elemTotalAmountWords = "";

		var placeDelivery = $("#txtPlaceDel").val().replace(/^\s+|\s+$/g, "").length;
		var dateDelivery = $("#txtDateDel").val().replace(/^\s+|\s+$/g, "").length;
		var paymentTerm = $("#txtPayTerm").val().replace(/^\s+|\s+$/g, "").length;
		var elemPlaceDelivery = "#txtPlaceDel";
		var elemDateDelivery = "#txtDateDel";
		var elemPaymentTerm = "#txtPayTerm";

		if (printType == "po") {
			poDate = $("#txtPODate input").val().replace(/^\s+|\s+$/g, "").length;
			deliveryTerm = $("#txtDelTerm").val().replace(/^\s+|\s+$/g, "").length;
			totalAmountWords = $("#txtAmnt").val().replace(/^\s+|\s+$/g, "").length;
			var elemPoDate = "#txtPODate";
			var elemDeliveryTerm = "#txtDelTerm";
			var elemTotalAmountWords = "#txtAmnt";

			$(".input-qnty").each(function() {
				var inputQnty = $(this).val().replace(/^\s+|\s+$/g, "").length;

				if (inputQnty == 0) {
					$(this).addClass("input-error-highlighter");
					errorCount++;
				} else {
					$(this).removeClass("input-error-highlighter");
				}
			});

			if (deliveryTerm == 0) {
				$(elemDeliveryTerm).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(elemDeliveryTerm).removeClass("input-error-highlighter");
			}

			if (totalAmountWords == 0) {
				$(elemTotalAmountWords).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(elemTotalAmountWords).removeClass("input-error-highlighter");
			}
		} else if (printType == "jo") {
			var amountWords = $("#amountWord").val().replace(/^\s+|\s+$/g, "").length;
			poDate = $("#txtJODate input").val().replace(/^\s+|\s+$/g, "").length;
		
			if (amountWords == 0) {
				$("#amountWord").addClass("input-error-highlighter")
				errorCount++;
			} else {
				$("#amountWord").removeClass("input-error-highlighter");
			}

			var elemPoDate = "#txtJODate";
		}
		
		$(".input-description").each(function() {
			var inputDescription = $(this).val().replace(/^\s+|\s+$/g, "").length;

			if (inputDescription == 0) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(this).removeClass("input-error-highlighter");
			}
		});

		if (poDate == 0) {
			$(elemPoDate).addClass("input-error-highlighter");
			errorCount++;
		} else {
			$(elemPoDate).removeClass("input-error-highlighter");
		}

		if (placeDelivery == 0) {
			$(elemPlaceDelivery).addClass("input-error-highlighter");
			errorCount++;
		} else {
			$(elemPlaceDelivery).removeClass("input-error-highlighter");
		}

		if (dateDelivery == 0) {
			$(elemDateDelivery).addClass("input-error-highlighter");
			errorCount++;
		} else {
			$(elemDateDelivery).removeClass("input-error-highlighter");
		}

		if (paymentTerm == 0) {
			$(elemPaymentTerm).addClass("input-error-highlighter")
			errorCount++;
		} else {
			$(elemPaymentTerm).removeClass("input-error-highlighter");
		}

		if (errorCount == 0) {
			withError = false;
		} else {
			withError = true;
		}

		return withError;
	}

	function savePO() {
		var withError = false;
		var poDate = $("#txtPODate input").val();
		var placeDelivery = $("#txtPlaceDel").val();
		var dateDelivery = $("#txtDateDel").val();
		var deliveryTerm = $("#txtDelTerm").val();
		var paymentTerm = $("#txtPayTerm").val();
		var totalAmountWords = $("#txtAmnt").val();
		var grandTotal = $("#grand-total").val();
		var trulyYours = $("#selApp").val();
		var accountantHead = $("#selReq").val();

		withError = inputValidation(withError, "po");

		if (!withError) {
			var qtyItems = [];
			var itemDesc = [];
			var unitCost = [];
			var totalAmount = [];
			var unitIssue = [];
			var infoID = [];
			var excluded = [];

			$(".input-qnty").each(function() {
				qtyItems.push($(this).val());
			});

			$(".input-description").each(function() {
				itemDesc.push($(this).val());
			});

			$(".input-cost").each(function() {
				unitCost.push($(this).val());
			});

			$(".input-total").each(function() {
				totalAmount.push($(this).val());
			});

			$(".unit-issue").each(function() {
				unitIssue.push($(this).text());
			});

			$(".val-info-id").each(function() {
				infoID.push($(this).val());
			});

			$(".input-excluded").each(function() {
				excluded.push($(this).val());
			});

			poData = [ poDate ,
					   placeDelivery ,
					   dateDelivery ,
					   deliveryTerm ,
					   paymentTerm ,
					   totalAmountWords ,
					   grandTotal ,
					   trulyYours ,
					   accountantHead ,
					   qtyItems , 
					   itemDesc , 
					   unitCost , 
					   unitIssue ,
					   prID,
					   infoID,
					   excluded,
					   totalAmount ];

			$.post('db_operation.php', {
			    data: poData,
			    prID: poNo,
			    toggle: "save-po"
			}).done(function(data) {
				alert("Saved to ORS/BURS.");

				if (what == "po_jo") {
					if (po) {
						toggleModal = 1;
						$("#modal-print-1").modal("hide");
						$("#qtyItems").val(JSON.stringify(qtyItems));
						$("#itemDesc").val(JSON.stringify(itemDesc));
						$("#unitCost").val(JSON.stringify(unitCost));
						$("#totalAmount").val(JSON.stringify(totalAmount));
						//submitPrintPO();
					}

					
					//if (jo) {
					//	$("#print-content-1").load( "job_order_op.php", 
					//		{ "poNo": poNo,
					//		  "prID": prID },
					//		function() {
					//			if (po) {
					//				po = false;
					//			}

					//			$("#txtJODate").datetimepicker({
					//				viewMode: 'days',
					//				format: 'MM/DD/YYYY',
					//				sideBySide: true,
					//				widgetPositioning: {
					//			        horizontal: 'left',
					//			        vertical: 'bottom'
					//			    }
					//			});
					//		});
					//}
				} else {
					toggleModal = 1;
					$("#modal-print-1").modal("hide");
					$("#qtyItems").val(JSON.stringify(qtyItems));
					$("#itemDesc").val(JSON.stringify(itemDesc));
					$("#unitCost").val(JSON.stringify(unitCost));
					$("#totalAmount").val(JSON.stringify(totalAmount));
					//submitPrintPO();
				}
			}).fail(function(xhr, status, error) {
				
			});
		}
	}

	function saveJO() {
		var withError = false;
		var poDate = $("#txtJODate input").val();
		var placeDelivery = $("#txtPlaceDel").val();
		var dateDelivery = $("#txtDateDel").val();
		var grandTotal = $("#grand-total").val();
		var paymentTerm = $("#txtPayTerm").val();
		var approvedBy = $("#selApp").val();
		var reqDept = $("#selReq").val();
		var accountantHead = $("#selFunds").val();
		var amountWord = $("#amountWord").val();

		withError = inputValidation(withError, "jo");

		if (!withError) {
			var workDesc = [];
			var qtyItems = [];
			var unitCost = [];
			var infoID = [];
			var excluded = [];

			$(".input-description").each(function() {
				workDesc.push($(this).val());
			});

			$(".input-qnty").each(function() {
				qtyItems.push($(this).text());
			});

			$(".input-cost").each(function() {
				unitCost.push($(this).val());
			});

			$(".val-info-id").each(function() {
				infoID.push($(this).val());
			});

			$(".input-excluded").each(function() {
				excluded.push($(this).val());
			});

			joData = [ poDate ,
					   placeDelivery ,
					   dateDelivery ,
					   grandTotal , 
					   paymentTerm ,
					   approvedBy ,
					   reqDept ,
					   accountantHead ,
					   workDesc , 
					   qtyItems , 
					   unitCost , 
					   prID ,
					   infoID,
					   excluded,
					   amountWord ];

			$.post('db_operation.php', {
			    data: joData,
			    prID: poNo,
			    toggle: "save-jo"
			}).done(function(data) {
				alert("Saved to ORS/BURS.");

				if (what == "po_jo") {
					if (jo) {
						$("#workDesc").val(JSON.stringify(workDesc));
						$("#modal-print-1").modal("hide");
						//submitPrintJO();
					}
				} else {
					toggleModal = 2;
					$("#workDesc").val(JSON.stringify(workDesc));
					$("#modal-print-1").modal("hide");
					//submitPrintJO();
				}
			}).fail(function(xhr, status, error) {

			});
		}
	}

	$("#btn-print").unbind("click").click(function() {
		if (toggleModal == 1) {
			submitPrintPO();
		} else if (toggleModal == 2) {
			submitPrintJO();
		}
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		if (toggleModal == 1) {
			var qtyItems = $("#qtyItems").val();
			var itemDesc = $("#itemDesc").val();
			var unitCost = $("#unitCost").val();
			var totalAmount = $("#totalAmount").val();

			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + poNo + "&what=po" + 
											"&prID=" + prID + 
											"&qtyItems=" + qtyItems + 
											"&itemDesc=" + encodeURIComponent(itemDesc) +
											"&unitCost=" + unitCost +
											"&totalAmount=" + totalAmount +
											"&font-scale=" + fontScale + "&paper-size=" + paperSize + 
											"&amount-word=" + amountWord);
		} else if (toggleModal == 2) {
			var workDesc = $("#workDesc").val();

			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + poNo + "&what=jo" + 
											"&prID=" + prID + 
											"&workDesc=" + encodeURIComponent(workDesc) +
											"&font-scale=" + fontScale + "&paper-size=" + paperSize + 
											"&amount-word=" + amountWord);
		}
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		if (toggleModal == 1) {
			var qtyItems = $("#qtyItems").val();
			var itemDesc = $("#itemDesc").val();
			var unitCost = $("#unitCost").val();
			var totalAmount = $("#totalAmount").val();

			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + poNo + "&what=po" + 
											"&prID=" + prID + 
											"&qtyItems=" + qtyItems + 
											"&itemDesc=" + encodeURIComponent(itemDesc) +
											"&unitCost=" + unitCost +
											"&totalAmount=" + totalAmount +
											"&font-scale=" + fontScale + "&paper-size=" + paperSize +
											"&amount-word=" + amountWord);
		} else if (toggleModal == 2) {
			var workDesc = $("#workDesc").val();

			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + poNo + "&what=jo" + 
											"&prID=" + prID + 
											"&workDesc=" + encodeURIComponent(workDesc) +
											"&font-scale=" + fontScale + "&paper-size=" + paperSize + 
											"&amount-word=" + amountWord);
		}
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

	$.fn.showPrintDialog = function(_poNo, _what, _prID) {
		poNo = _poNo;
		what = _what
		prID = _prID;
		po = false;
		jo = false;

		$('.tooltip').remove();

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			if (what == "po") {
				$("#print-content-1").load( "purchase_order_op.php", 
								   { "poNo": poNo,
								     "prID": prID },
								   function() {
									   checkIntegerInput();
									   computeGrandTotal();

									   $("#btn-ok").unbind("click").click(function() {
									   		if (what == "po") {
						    					savePO();
						    				} else if (what == "jo") {
						    					saveJO();
						    				}  else if (what == "po_jo") {
						    					if (jo) {
						    						saveJO();
						    					}

						    					if (po) {
						    						savePO();
						    					}
						    				}
									   });

									   $("#txtPODate").datetimepicker({
											viewMode: 'days',
											format: 'MM/DD/YYYY',
											sideBySide: true,
											widgetPositioning: {
									            horizontal: 'left',
									            vertical: 'bottom'
									        }
										});
								   });
			} else if (what == "jo") {
				$("#print-content-1").load( "job_order_op.php", 
								   { "poNo": poNo,
								     "prID": prID },
								   function() {
									   //checkIntegerInput();
									   //computeGrandTotal();
									   $("#btn-ok").unbind("click").click(function() {
									   		if (what == "po") {
						    					savePO();
						    				} else if (what == "jo") {
						    					saveJO();
						    				}  else if (what == "po_jo") {
						    					if (jo) {
						    						saveJO();
						    					}

						    					if (po) {
						    						savePO();
						    					}
						    				}
									   });

									   $("#txtJODate").datetimepicker({
											viewMode: 'days',
											format: 'MM/DD/YYYY',
											sideBySide: true,
											widgetPositioning: {
									            horizontal: 'left',
									            vertical: 'bottom'
									        }
										});
								   });
			} else if (what == "po_jo") {
				if (toggleOpen == 1) {
					po = true;
					jo = false;

					$("#print-content-1").load( "purchase_order_op.php", 
								   { "poNo": poNo,
								     "prID": prID },
								   function() {
									   checkIntegerInput();
									   computeGrandTotal();

									   $("#btn-ok").unbind("click").click(function() {
									   		if (what == "po") {
						    					savePO();
						    				} else if (what == "jo") {
						    					saveJO();
						    				}  else if (what == "po_jo") {
						    					if (jo) {
						    						saveJO();
						    					}

						    					if (po) {
						    						savePO();
						    					}
						    				}
									   });

									   $("#txtPODate").datetimepicker({
											viewMode: 'days',
											format: 'MM/DD/YYYY',
											sideBySide: true,
											widgetPositioning: {
									            horizontal: 'left',
									            vertical: 'bottom'
									        }
										});
								   });
				}	
			}
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1 || toggleModal == 2) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.checkItem = function(selected,who,action) {
		if(confirm("Are you sure you want to " + action + " '" + selected + "' from the lists?")) {
			document.getElementById(who).checked=1;
			document.frmPRPost.hdAction.value = action;
			document.frmPRPost.submit();
		}
	}

	$.fn.check_input = function(frm) {
		if (frm.txtPlaceDel.value=="" || frm.txtPlaceDel.value== " ") {
			alert("Place of delivery is required.");
			frm.txtPlaceDel.focus();
			return false;
		} else if (frm.txtDelTerm.value=="" || frm.txtDelTerm.value== " ") {
			alert("Delivery Term is required.");
			frm.txtDelTerm.focus();
			return false;
		} else if (frm.txtDateDel.value=="" || frm.txtDateDel.value== " ") {
			alert("Date of Delivery is required.");
			frm.txtDateDel.focus();
			return false;
		} else if (frm.txtPayTerm.value=="" || frm.txtPayTerm.value== " ") {
			alert("Payment of Delivery is required.");
			frm.txtPayTerm.focus();
			return false;
		} else if (frm.txtPODate.value=="" || frm.txtPODate.value== " ") {
			alert("PO Date is required.");
			frm.txtPODate.focus();
			return false;
		} else if (frm.txtAmnt.value=="" || frm.txtAmnt.value== " ") {
			alert("Amount in Words is required.");
			frm.txtAmnt.focus();
			return false;
		} else {
			return true;
		}
	}
});