$(function() {
	var id, what, prID, poNo ,type, empID, inventoryClassNo, multiple;
	var printType = "doc";
	var oldEmpID = 0;
	var paperSize = "1";
	var fontScale = 0;
	var toggleModal = 0;

	function printPreview() {
        if (toggleModal == 2) {
            $("#selPaperSize").attr("disabled", "disabled");
        }

		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			var pid = inventoryClassNo;

			if (printType == "doc") {
				pid = inventoryClassNo;
			} else if (printType == "label") {
				pid = id;
			}

            $("#print-content").attr("src", "../../../class_function/print_preview.php?" +
                                        	"preview=true&print=" + pid + "&what=" + what + 
                                        	"&inv-class-no=" + inventoryClassNo + "&po-no=" + poNo + 
                                        	"&recieved-by=" + empID + "&multiple=" + multiple +
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
            $("#selPaperSize").removeAttr("disabled");
		});
	}

	function printInventory() {
		fontScale = $("#txtIncreaseSize").val();
		paperSize = $("#selPaperSize").val();

		if (printType == "doc") {
			$("#print").val(inventoryClassNo);
		} else if (printType == "label") {
			$("#print").val(id);
		}
		
		$("#what").val(what);
		$("#inv-class-no").val(inventoryClassNo);
		$("#po-no").val(poNo);
		$("#recieved-by").val(empID);
        $("#multiple").val(multiple);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);

		$("#frmSize").submit();
	}

	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
			var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

			if (inputField == 0 ) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(".input-quantity").each(function() {
					if ($(this).val() == "0") {
			            $(this).addClass("input-error-highlighter");
			            errorCount++;
			        }
				});

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

	function savePAR(invID, parData) {
		$.post('db_operation.php', {
			prID: invID,
			inventoryClassNo: inventoryClassNo,
		    data: parData,
		    classification: what,
		    oldEmpID: oldEmpID,
            savingType: type,
            multiple: multiple,
		    toggle: "save-inventory"
		}).done(function(data) {
			if (data != 0) {
				//$("#modal-print-1").modal("hide");
			} else {
				alert("There are no available stock.");
			}
		}).fail(function(xhr, status, error) {
			savePAR(invID, parData);
		});
	}

	function saveRIS(invID, risData) {
		$.post('db_operation.php', {
			prID: invID,
			inventoryClassNo: inventoryClassNo,
		    data: risData,
		    classification: what,
		    oldEmpID: oldEmpID,
            savingType: type,
            multiple: multiple,
		    toggle: "save-inventory"
		}).done(function(data) {
			if (data != 0) {
				//$("#modal-print-1").modal("hide");
			} else {
				alert("There are no available stock.");
			}
		}).fail(function(xhr, status, error) {
			saveRIS(invID, risData)
		});
	}

	function saveICS(invID, icsData) {
		$.post('db_operation.php', {
			prID: invID,
			inventoryClassNo: inventoryClassNo,
		    data: icsData,
		    classification: what,
		    oldEmpID: oldEmpID,
            savingType: type,
            multiple: multiple,
		    toggle: "save-inventory"
		}).done(function(data) {
			if (data != 0) {
				//$("#modal-print-1").modal("hide");
			} else {
				alert("There are no available stock.");
			}
		}).fail(function(xhr, status, error) {
			saveICS(invID, icsData);
		});
	}

	function issueSaved() {
		if (type == "new") {
			alert("Stock/s issue saved!");
			window.location = "inventory.php?po_no=" + poNo;
			return false;
		} else if (type == "old") {
        	toggleModal = 1;
        	$("#modal-print-1").modal("hide");
        }
	}

	function proccessPARData() {
		var parData = [];
		var quantity = [];
		var propertyNo = [];
		var acquiredDate = [];
		var invID = [];
		var serialNo = [];
		var parNo = $("#txtParNo").val();
		var recievedBy = $("#sel-recieved-by").val();
		var issuedBy = $("#sel-issued-by").val();

		$(".input-id").each(function() {
			invID.push($(this).val());
		});

		$(".input-quantity").each(function() {
			quantity.push($(this).val());
		});

		$(".input-property-no").each(function() {
			propertyNo.push($(this).val());
		});

		$(".input-date").each(function() {
			acquiredDate.push($(this).val());
		});

		$(".input-serial").each(function() {
			serialNo.push($(this).val());
		});

		$.ajaxSetup({async: false});

		$.each(quantity, function(key, qnty) {
			parData = [parNo, propertyNo[key], acquiredDate[key],
			           issuedBy, recievedBy, quantity[key], 
			           oldEmpID, type, serialNo[key]];

			//console.log(parData);
			savePAR(invID[key], parData);
		});

		$.ajaxSetup({async: true});

		issueSaved();
	}

	function proccessICSData() {
		var icsData = [];
		var quantity = [];
		var acquiredDate = [];
		var inventoryNo = [];
		var estimatedLife = [];
		var invID = [];
		var serialNo = [];
		var icsNo = $("#txticsNo").val();
		var issuedBy = $("#sel-issued-by").val();
		var recievedBy = $("#sel-recieved-by").val();

		$(".input-id").each(function() {
			invID.push($(this).val());
		});

		$(".input-quantity").each(function() {
			quantity.push($(this).val());
		});

		$(".input-date").each(function() {
			acquiredDate.push($(this).val());
		});

		$(".input-property-no").each(function() {
			inventoryNo.push($(this).val());
		});

		$(".input-life").each(function() {
			estimatedLife.push($(this).val());
		});

		$(".input-serial").each(function() {
			serialNo.push($(this).val());
		});

		$.ajaxSetup({async: false});

		$.each(quantity, function(key, qnty) {
			icsData = [icsNo, inventoryNo[key], acquiredDate[key],
					   estimatedLife[key], issuedBy, recievedBy,
					   qnty, oldEmpID, type, serialNo[key]];

			//console.log(icsData);
			saveICS(invID[key], icsData);
		});

		$.ajaxSetup({async: true});

		issueSaved();
	}

	function proccessRISData() {
		var risData = [];
		var quantity = [];
		var remarks = [];
		var invID = [];
		var risNo = $("#txtRisNo").val();
		var approvedBy = $("#sel-approved-by").val();
		var issuedBy = $("#sel-issued-by").val();
		var recievedBy = $("#sel-recieved-by").val();
		var stockAvailable = "";

		$(".input-id").each(function() {
			invID.push($(this).val());
		});

		$(".input-quantity").each(function() {
			quantity.push($(this).val());
		});

		$(".input-remarks").each(function() {
			remarks.push($(this).val());
		});

		$.ajaxSetup({async: false});

		$.each(quantity, function(key, qnty) {
			stockAvailable = $("input[name=check-stock-available-" + key + "]:checked").val();
			risData = [risNo, stockAvailable, quantity[key], remarks[key], 
					   approvedBy, issuedBy, recievedBy, oldEmpID];

			//console.log(risData);
			saveRIS(invID[key], risData);
		});

		$.ajaxSetup({async: true});

		issueSaved();
	}

	function setNumericOnly(element) {
		$(element).keydown(function(event) {
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
	}

	function issueItem(inventoryClassNo, poNo) {
		$.post('db_operation.php', {
			prID: inventoryClassNo,
		    toggle: "issue-item"
		}).done(function(data) {
			//console.log(data);
			if (data == 0) {
				alert("Error: Print first a document.");
			}
		}).fail(function(xhr, status, error) {
			issueItem(inventoryClassNo);
		});

		window.location = "inventory.php?po_no=" + poNo;
		return false;
	}

	function generateTableRow() {
		var table = $("#tbl-items").get(0);
		var rowCount = table.rows.length - 1; 
		var row = table.insertRow(rowCount);
		var iteration = rowCount;
		var origCount = $('#txtItemCount').get(0).value;
		var conCount = eval(origCount) + 1;
	    var colCount = table.rows[0].cells.length;

		if (what == "par") {
            for (var i = 0; i < colCount; i++) {
		        var newcell = row.insertCell(i);
		        $(newcell).html($(table.rows[1].cells[i]).html());

				switch(i){
					case 0:
						$(newcell).find('input')
								  .attr('id', 'input-quantity-' + conCount)
								  .attr('name', 'input-quantity-' + conCount);
					break;
					case 1:
						$(newcell).find('select')
								  .attr('id', 'sel-unit-' + conCount)
								  .attr('name', 'sel-unit-' + conCount);
					break;
					case 2:
						$(newcell).find('textarea')
								  .attr('id', 'txt-description-' + conCount)
								  .attr('name', 'txt-description-' + conCount)
								  .attr('placeholder', 'Item description...')
								  .val('');
					break;
					case 3:
						$(newcell).find('input')
								  .attr('id', 'input-property-no-' + conCount)
								  .attr('name', 'input-property-no-' + conCount)
								  .val('');
					break;
					case 4:
						$(newcell).find('input')
								  .attr('id', 'input-date-' + conCount)
								  .attr('name', 'input-date-' + conCount)
								  .val('');
					break;
					case 5:
						$(newcell).find('input')
								  .attr('id', 'input-amount-' + conCount)
								  .attr('name', 'input-amount-' + conCount)
								  .val('');
					break;
					case 6:
						var rowID = 'row_' + conCount;
						var rowClass = 'row-' + conCount;
						$(row).attr('id', rowID)
							  .attr('class', rowClass);
						
						$(newcell).find('a')
								  .attr('id', 'btn-del-' + conCount)
								  .attr('name', 'btn-del-' + conCount)
								  .attr('href', 'javascript: $(this).deleteItem(\'' + rowClass + '\')');
					break;				
				}
		    }
		} else if (what == "ics") {
			for (var i = 0; i < colCount; i++) {
		        var newcell = row.insertCell(i);
		        $(newcell).html($(table.rows[1].cells[i]).html());

				switch(i){
					case 0:
						$(newcell).find('input')
								  .attr('id', 'input-quantity-' + conCount)
								  .attr('name', 'input-quantity-' + conCount);
					break;
					case 1:
						$(newcell).find('select')
								  .attr('id', 'sel-unit-' + conCount)
								  .attr('name', 'sel-unit-' + conCount);
					break;
					case 2:
						$(newcell).find('input')
								  .attr('id', 'input-unit-cost-' + conCount)
								  .attr('name', 'input-unit-cost-' + conCount);
					break;
					case 3:
						$(newcell).find('input')
								  .attr('id', 'input-total-cost-' + conCount)
								  .attr('name', 'input-total-cost-' + conCount);
					break;
					case 4:
						$(newcell).find('textarea')
								  .attr('id', 'txt-description-' + conCount)
								  .attr('name', 'txt-description-' + conCount)
								  .attr('placeholder', 'Item description...')
								  .val('');
					break;
					case 5:
						$(newcell).find('input')
								  .attr('id', 'input-date-' + conCount)
								  .attr('name', 'input-date-' + conCount)
								  .val('');
					break;
					case 6:
						$(newcell).find('input')
								  .attr('id', 'input-property-no-' + conCount)
								  .attr('name', 'input-property-no-' + conCount)
								  .val('');
					break;
					case 7:
						$(newcell).find('input')
								  .attr('id', 'input-life-' + conCount)
								  .attr('name', 'input-life-' + conCount)
								  .val('');
					break;
					case 8:
						var rowID = 'row_' + conCount;
						var rowClass = 'row-' + conCount;
						$(row).attr('id', rowID)
							  .attr('class', rowClass);
						
						$(newcell).find('a')
								  .attr('id', 'btn-del-' + conCount)
								  .attr('name', 'btn-del-' + conCount)
								  .attr('href', 'javascript: $(this).deleteItem(\'' + rowClass + '\')');
					break;				
				}
		    }
		} else if (what == "ris") {

		}

		//return rowString;

	    $("#txtItemCount").val(conCount);
	}

	function loadOptionPage(element, phpFile) {
		$.ajaxSetup({async: false});

		$(element).load(phpFile, { 
							  "inventoryID": id,
							  "poNo": poNo,
							  "prID": prID,
							  "empID": empID,
							  "inventoryClassNo": inventoryClassNo,
							  "multiple": multiple,
							  "type": type
							}, function() {
								$('body').addClass('modal-open');

								$(".divDate").datetimepicker({
									viewMode: 'days',
									format: 'MM/DD/YYYY'
								});

								if (what == "ris") {
									
								} else if (what == "par") {
									
								} else if (what == "ics") {
									setNumericOnly($("#input-quantity"));
									setNumericOnly($("#input-total"));
									setNumericOnly($("#input-cost"));

									$("#input-quantity").keyup(function() {
										var val1 = parseInt($(this).val());
										var val2 = parseFloat($("#input-cost").val()).toFixed(2);
										var product = val1 * val2;
										
										if (!product || product == "NaN") {
											product = 0.00;
										}

										$("#input-total").val(product.toFixed(2));
									});
								}

								$("#btn-ok").unbind('click').click(function() {
									printType = "doc";

									if (what == "ris") {
										var withError = inputValidation(false);

										if (!withError) {
											proccessRISData();			
										}
									} else if (what == "par") {
										var withError = inputValidation(false);

										if (!withError) {
											proccessPARData();
										}
									} else if (what == "ics") {
										var withError = inputValidation(false);

										if (!withError) {
											proccessICSData();
										}
									}
								});

								$("#btn-add-item").unbind('click').click(function() {
									generateTableRow();

									$(".divDate").datetimepicker({
										viewMode: 'days',
										format: 'MM/DD/YYYY'
									});
								});

								/*
                                 $("#btn-label").unbind('click').click(function() {
                                     if (what == "ris") {
                                         alert("For PAR and ICS only.");
                                     } else if (what == "par") {
                                         var withError = inputValidation(false);

                                         if (!withError) {
                                             proccessPARData();
                                             what = "label";
                                             toggleModal = 2;
                                         }
                                     } else if (what == "ics") {
                                         var withError = inputValidation(false);

                                         if (!withError) {
                                             proccessICSData();
                                             what = "label";
                                             toggleModal = 2;
                                         }
                                     }
                                 });*/

								$("#btn-print").unbind('click').click(function() {
									printInventory();
								});

                                 $("#btn-exit").unbind('click').click(function() {
                                     toggleModal = 0;
                                     $("#modal-print-1").modal("hide");
                                 });
							});

		$.ajaxSetup({async: true});
	}

	$("#txtIncreaseSize").change(function() {
		var pid = inventoryClassNo;
		fontScale = $(this).val();

		if (printType == "doc") {
			pid = inventoryClassNo;
		} else if (printType == "label") {
			pid = id;
		}

        $("#print-content").attr("src", "../../../class_function/print_preview.php?" +
                                    	"preview=true&print=" + pid + "&what=" + what + 
                                    	"&inv-class-no=" + inventoryClassNo + "&po-no=" + poNo + 
                                    	"&recieved-by=" + empID + "&multiple=" + multiple +
                                    	"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#selPaperSize").change(function() {
		var pid = inventoryClassNo;
		paperSize = $(this).val();

		if (printType == "doc") {
			pid = inventoryClassNo;
		} else if (printType == "label") {
			pid = id;
		}

        $("#print-content").attr("src", "../../../class_function/print_preview.php?" +
                                    	"preview=true&print=" + pid + "&what=" + what + 
                                    	"&inv-class-no=" + inventoryClassNo + "&po-no=" + poNo + 
                                    	"&recieved-by=" + empID + "&multiple=" + multiple +
                                    	"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#btn-add-inventory").unbind('click').click(function() {
		id = "";
		what = "";
		prID = "";
		poNo = "";
		type = "";
		empID = "";
		oldEmpID = "";
		inventoryClassNo = "";
		multiple = "";

		$("#modal-print-3").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#sel-classification").change(function() {
				what = $(this).val();
				var phpFile = what + "_op.php";
				var element = "#print-content-3";

				if (what == "par" || what == "ics" || what == "ris") {
					loadOptionPage(element, phpFile);
				} else {
					$("#print-content-3").html('<h3 class="font-color-1"></h3>');
				}
			});
		}).on("hidden.bs.modal", function() {
			$("#sel-classification").val("");
			$("#print-content-3").html('<h3 class="font-color-1"></h3>');
		});
	});
	
	$.fn.printDialog = function(_id, _what, _prID, _poNo, _type, _empID, _inventoryClassNo, _multiple) {
		id = _id;
		what = _what;
		prID = _prID;
		poNo = _poNo;
		type = _type;
		empID = _empID;
		oldEmpID = _empID;
		inventoryClassNo = _inventoryClassNo;
		multiple = _multiple;

		var phpFile = what + "_op.php";
		var element = "#print-content-1";

		$("#modal-print-2").modal("hide");
		$('.tooltip').remove();

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#btn-reload").unbind('click').click(function() {
				loadOptionPage(element, phpFile);
			});

			loadOptionPage(element, phpFile);
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1 || toggleModal == 2) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			toggleModal = 0;
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.printLabelDialog = function(_id) {
		id = _id;
		printType = "label";

		if (what == "ris") {
            alert("For PAR and ICS only.");
        } else if (what == "par") {
        	what = "label";
            toggleModal = 2;

            $("#modal-print-1").modal("hide");
        } else if (what == "ics") {
        	what = "label";
            toggleModal = 2;

            $("#modal-print-1").modal("hide");
        }
	}

	$.fn.listIssued = function(_id, _inventoryClassNo) {
		id = _id;
		inventoryClassNo = _inventoryClassNo;

		$('.tooltip').remove();

		$("#modal-print-2").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content-2").load("issued_staff_list_op.php", { 
								  "inventoryID": id,
								  "inventoryClassNo": inventoryClassNo
								}, function() {
									
								});
		}).on("hidden.bs.modal", function() {
			$("#print-content-2").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$.fn.issueItem = function(inventoryClassNo, poNo, itemStatus) {
		if (confirm("Change the status of this item to 'ISSUED'?")) {
		    issueItem(inventoryClassNo, poNo);
		}
	}

	$.fn.deleteItem = function(element) {
		if (confirm("Delete this item?")) {
			$('.' + element).remove();
		}
	}

});