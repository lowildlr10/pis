$(function() {
	var currentDate = moment();
	var what, divisionID = "1", classification = "par", categoryID = "1";
	var startDate = currentDate.format('MM/DD/YYYY').toString();
	var endDate = currentDate.format('MM/DD/YYYY').toString();
	var paperSize = "3";
	var fontScale = 0;
	var toggleModal = 0;

	function initialTruncate() {
		var table = "tbltemp_procurement_monitoring";

		$.post('db_operation.php', {
			table: table,
		    toggle: "truncate-table"
		}).done(function(data) {
			table = "tbltemp_inventory_supply";

			$.post('db_operation.php', {
				table: table,
			    toggle: "truncate-table"
			}).done(function(data) {

			}).fail(function(xhr, status, error) {
				initialTruncate();
			});
		}).fail(function(xhr, status, error) {
			initialTruncate();
		});
	}

	function inputValidation(withError) {
		var errorCount = 0;

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

	function printPreview() {
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#selPaperSize").val(paperSize);
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										    "preview=true&startDate=" + startDate + "&endDate=" + endDate + 
										    "&what=" + what + "&font-scale=" + fontScale + 
										    "&paper-size=" + paperSize + "&class=" + classification +
										    "&categoryID=" + categoryID);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			paperSize = "3";
			fontScale = 0;
			toggleModal = 0;
			divisionID = "1";
			categoryID = "1"
			classification = "par";
			startDate = currentDate.format('MM/DD/YYYY').toString();
			endDate = currentDate.format('MM/DD/YYYY').toString();
			$("#toggle-display").val("0");
			$("#print-content").attr("src", "");
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}

	function saveReport(reportData) {
		$.post('db_operation.php', {
		    data: reportData,
		    toggle: "save-report",
		    what: what
		}).done(function(data) {
			console.log(data);
		}).fail(function(xhr, status, error) {
			saveReport(reportData);
		});
	}

	function proccessReportData() {
		var reportData = [];

		if (what == "pmf") {
			table = "tbltemp_procurement_monitoring";
		} else if (what == "ios" || what == "pcppe") {
			table = "tbltemp_inventory_supply";
		}

		$.post('db_operation.php', {
			table: table,
		    toggle: "truncate-table"
		}).done(function(data) {
			if (what == "pmf") {
				$(".row_data").each(function(index) {
					var moYear = $(this).find(".txt-mo-year").val();
					var prNo = $(this).find(".txt-pr-no").val();
					var prDate = $(this).find(".txt-pr-date").val();
					var abstractApprovalDate = $(this).find(".txt-date-abstract").val();
					var poApprovalDate = "", supplier = "", particulars = "", poRecievedDate = "",
						deliveredDate = "", invoiceNo = "", inspectedBy = "", requiredDays = "",
						actualDays = "", difference = "", remarks = "";

					$(this).find(".txt-date-po").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							poApprovalDate += $(this).val();
							poApprovalDate += ";";
						} else {
							poApprovalDate += "";
						}
					});

					$(this).find(".txt-supplier").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							supplier += $(this).val();
							supplier += ";";
						} else {
							supplier += "";
						}
					});

					$(this).find(".txt-particulars").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							particulars += $(this).val();
							particulars += ";";
						} else {
							particulars += "";
						}
					});

					$(this).find(".txt-date-po-recieved").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							poRecievedDate += $(this).val();
							poRecievedDate += ";";
						} else {
							poRecievedDate += "";
						}
					});

					$(this).find(".txt-date-delivered").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							deliveredDate += $(this).val();
							deliveredDate += ";";
						} else {
							deliveredDate += "";
						}
					});

					$(this).find(".txt-invoice-no").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							invoiceNo += $(this).val();
							invoiceNo += ";";
						} else {
							invoiceNo += "";
						}
					});

					$(this).find(".txt-inspected-by").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							inspectedBy += $(this).val();
							inspectedBy += ";";
						} else {
							inspectedBy += "";
						}
					});

					$(this).find(".txt-required-days").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							requiredDays += $(this).val();
							requiredDays += ";";
						} else {
							requiredDays += "";
						}
					});

					$(this).find(".txt-actual-days").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							actualDays += $(this).val();
							actualDays += ";";
						} else {
							actualDays += "";
						}
					});

					$(this).find(".txt-difference").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							difference += $(this).val();
							difference += ";";
						} else {
							difference += "";
						}
					});

					$(this).find(".txt-remarks").each(function() {
						var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

						if (inputField != 0) {
							remarks += $(this).val();
							remarks += ";";
						} else {
							remarks += "";
						}
					});

					reportData = [moYear, prNo, prDate, abstractApprovalDate, poApprovalDate,
								  supplier, particulars, poRecievedDate, deliveredDate, invoiceNo, 
								  inspectedBy, requiredDays, actualDays, difference, remarks];

					saveReport(reportData);
				});
			} else if (what == "ios" || what == "pcppe") {
				$(".txt-article").each(function(key) {
					var documentNo = $(this).val();
					var description = $("#txt-description-" + key).val();
					var itemNo = $("#txt-item-no-" + key).val();
					var unitIssue = $("#txt-unit-name-" + key).val();
					var unitValue = $("#txt-unit-value-" + key).val();
					var quantity = $("#txt-quantity-" + key).val();
					var onHandCount = $("#txt-per-count-" + key).val();
					var quantityShortage = $("#txt-qty-shortage-average-" + key).val();
					var valueShortage = $("#txt-value-shortage-average-" + key).val();
					var remarks = $("#txt-remarks-" + key).val();

					reportData = [documentNo, description, itemNo, unitIssue, 
								  unitValue, quantity, onHandCount, quantityShortage,
								  valueShortage, remarks];
					
					saveReport(reportData);
				});
			}

			toggleModal = 1;
			$("#modal-print-1").modal("hide");
		}).fail(function(xhr, status, error) {
			proccessReportData();
		});
	}

	function printReport() {
		fontScale = $("#txtIncreaseSize").val();
		paperSize = $("#selPaperSize").val();

		$("#startDate").val(startDate);
		$("#endDate").val(endDate);
		$("#divisionID").val(divisionID);
		$("#categoryID").val(categoryID);
		$("#what").val(what);
		$("#class").val(classification);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);

		$("#frmSize").submit();
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

	function initializeInput() {
		if (what == "pmf") {
			$(".row_data").find(".txt-pr-date").each(function() {
				$(this).daterangepicker({
				    "singleDatePicker": true,
				    "showDropdowns": true,
				    "minDate": "1/1/2011",
					"maxDate": currentDate.format('MM/DD/YYYY').toString(),
					autoUpdateInput: false,
				    locale: {
				        cancelLabel: 'Clear'
				    }
				}, function(start, end, label) {
					
				}).on('apply.daterangepicker', function(ev, picker) {
				    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
				}).on('cancel.daterangepicker', function(ev, picker) {
				    $(this).val('');
				});
			});

			$(".row_data").find(".txt-date-abstract").each(function() {
				$(this).daterangepicker({
				    "singleDatePicker": true,
				    "showDropdowns": true,
				    "minDate": "1/1/2011",
					"maxDate": currentDate.format('MM/DD/YYYY').toString(),
					autoUpdateInput: false,
				    locale: {
				        cancelLabel: 'Clear'
				    }
				}, function(start, end, label) {
					
				}).on('apply.daterangepicker', function(ev, picker) {
				    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
				}).on('cancel.daterangepicker', function(ev, picker) {
				    $(this).val('');
				});
			});

			$(".row_data").find(".txt-date-po").each(function() {
				$(this).daterangepicker({
				    "singleDatePicker": true,
				    "showDropdowns": true,
				    "minDate": "1/1/2011",
					"maxDate": currentDate.format('MM/DD/YYYY').toString(),
					autoUpdateInput: false,
				    locale: {
				        cancelLabel: 'Clear'
				    }
				}, function(start, end, label) {
					
				}).on('apply.daterangepicker', function(ev, picker) {
				    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
				}).on('cancel.daterangepicker', function(ev, picker) {
				    $(this).val('');
				});
			});

			$(".row_data").find(".txt-date-po-recieved").each(function() {
				$(this).daterangepicker({
				    "singleDatePicker": true,
				    "showDropdowns": true,
				    "minDate": "1/1/2011",
					"maxDate": currentDate.format('MM/DD/YYYY').toString(),
					autoUpdateInput: false,
				    locale: {
				        cancelLabel: 'Clear'
				    }
				}, function(start, end, label) {
					
				}).on('apply.daterangepicker', function(ev, picker) {
				    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
				}).on('cancel.daterangepicker', function(ev, picker) {
				    $(this).val('');
				});
			});

			$(".row_data").find(".txt-date-delivered").each(function() {
				$(this).daterangepicker({
				    "singleDatePicker": true,
				    "showDropdowns": true,
				    "minDate": "1/1/2011",
					"maxDate": currentDate.format('MM/DD/YYYY').toString(),
					autoUpdateInput: false,
				    locale: {
				        cancelLabel: 'Clear'
				    }
				}, function(start, end, label) {
					
				}).on('apply.daterangepicker', function(ev, picker) {
				    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
				}).on('cancel.daterangepicker', function(ev, picker) {
				    $(this).val('');
				});
			});

			$(".row_data").each(function(key) {
				$(this).find(".txt-required-days").each(function(key1) {
					var elementDifference = $("#txt-difference-" + key + "-" + key1);
					var elementActualDays = $("#txt-actual-days-" + key + "-" + key1);
					var elementRequiredDays = $("#txt-required-days-" + key + "-" + key1);
					
					setNumericOnly(elementActualDays);
					setNumericOnly(elementActualDays);

					$(this).keyup(function() {
						var actualDays = parseInt(elementActualDays.val());
						var requiredDays = parseInt(elementRequiredDays.val());
						var difference = requiredDays - actualDays;

						elementDifference.val(difference);
					});

					elementActualDays.keyup(function() {
						var actualDays = parseInt(elementActualDays.val());
						var requiredDays = parseInt(elementRequiredDays.val());
						var difference = requiredDays - actualDays;
						
						if (!difference || difference == "NaN") {
							difference = "";
						}

						elementDifference.val(difference);
					});
				});

				$(this).find(".txt-date-po-recieved").each(function(key2) {
					var elementRecievedPO = $("#txt-date-po-recieved-" + key + "-" + key2);
					var elementDateDelivered = $("#txt-date-delivered-" + key + "-" + key2);
					var elementActualDays = $("#txt-actual-days-" + key + "-" + key2);
					var elementDifference = $("#txt-difference-" + key + "-" + key2);
					var elementRequiredDays = $("#txt-required-days-" + key + "-" + key2);

					elementRecievedPO.on('apply.daterangepicker', function(ev, picker) {
						var fromDate = picker.startDate.format('MM/DD/YYYY'),
					  		toDate = elementDateDelivered.val().substring(0, 10),
							from = moment(fromDate.toString(), 'MM/DD/YYYY'), 
							to = moment(toDate.toString(), 'MM/DD/YYYY'), 
							duration = to.diff(from, 'days');

						if (!duration) {
							duration = "";
						}

						elementActualDays.val(duration);

						var actualDays = parseInt(elementActualDays.val()),
							requiredDays = parseInt(elementRequiredDays.val()),
							difference = requiredDays - actualDays;
						
						if (!difference || difference == "NaN") {
							difference = "";
						}

						elementDifference.val(difference);
					})

					elementDateDelivered.on('apply.daterangepicker', function(ev, picker) {
						var toDate = picker.startDate.format('MM/DD/YYYY'),
					  		fromDate = elementRecievedPO.val().substring(0, 10),
							from = moment(fromDate.toString(), 'MM/DD/YYYY'),
							to = moment(toDate.toString(), 'MM/DD/YYYY'),
							duration = to.diff(from, 'days');

						if (!duration) {
							duration = "";
						}

						elementActualDays.val(duration);

						var actualDays = parseInt(elementActualDays.val()),
							requiredDays = parseInt(elementRequiredDays.val()),
							difference = requiredDays - actualDays;
						
						

						if (!difference || difference == "NaN") {
							difference = "";
						}
						
						elementDifference.val(difference);
					})
				});
			});
		} else if (what == "ios" || what == "pcppe") {
			$(".txt-quantity").each(function(key) {
				setNumericOnly($(this));
				setNumericOnly($("#txt-per-count-" + key));
				setNumericOnly($("#txt-unit-value-" + key));
				setNumericOnly($("#txt-qty-shortage-average-" + key));
				setNumericOnly($("#txt-value-shortage-average-" + key));

				$(this).keyup(function() {
					var quantity = parseInt($(this).val());
					var perCount = parseInt($("#txt-per-count-" + key).val());
					var unitValue = parseFloat($("#txt-unit-value-" + key).val()).toFixed(2);
					var quantityShortage = $("#txt-qty-shortage-average-" + key);
					var valueShortage = $("#txt-value-shortage-average-" + key);

					var qtyShortage = quantity - perCount;
					var valShortage = qtyShortage * unitValue;

					if (!qtyShortage || qtyShortage == "NaN") {
						qtyShortage = "0";
					}

					if (!valShortage || valShortage == "NaN") {
						valShortage = "0.00";
					} else {
						valShortage = valShortage.toFixed(2);
					}

					quantityShortage.val(qtyShortage);
					valueShortage.val(valShortage);
				});
			});

			$(".txt-per-count").each(function(key) {
				$(this).keyup(function() {
					var quantity = parseInt($("#txt-quantity-" + key).val());
					var perCount = parseInt($(this).val());
					var unitValue = parseFloat($("#txt-unit-value-" + key).val()).toFixed(2);
					var quantityShortage = $("#txt-qty-shortage-average-" + key);
					var valueShortage = $("#txt-value-shortage-average-" + key);

					var qtyShortage = quantity - perCount;
					var valShortage = qtyShortage * unitValue;

					if (!qtyShortage || qtyShortage == "NaN") {
						qtyShortage = "0";
					}

					if (!valShortage || valShortage == "NaN") {
						valShortage = "0.00";
					} else {
						valShortage = valShortage.toFixed(2);
					}

					quantityShortage.val(qtyShortage);
					valueShortage.val(valShortage);
				});
			});
		}
	}

	function initialize() {
		var phpFile = "report_daterange.php";
		classification = "par";
		divisionID = "1";
		categoryID = "1";

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content-1").load(phpFile, { 
								  "what": what
								}, function() {
									var phpFile2 = "";
									startDate = currentDate.format('MM/DD/YYYY').toString();
									endDate = currentDate.format('MM/DD/YYYY').toString();

									$('#report-date-range').daterangepicker({
										"autoApply": false,
									    "alwaysShowCalendars": true,
									    "minDate": "1/1/2011",
									    "maxDate": currentDate.format('MM/DD/YYYY').toString()
									}, function(start, end, label) {
									  	startDate = start.format('MM/DD/YYYY');
									  	endDate = end.format('MM/DD/YYYY');
									});

									if (what == "pmf") {
										phpFile2 = "report_pmf_op.php";

										$("#sel-division").change(function() {
											divisionID = $(this).val();
										});
									} else if (what == "ios") {
										phpFile2 = "report_ios_op.php";

										$("#sel-classification").change(function() {
											classification = $(this).val();
										});
									} else if (what == "pcppe") {
										phpFile2 = "report_pcppe_op.php";

										$("#sel-classification").change(function() {
											classification = $(this).val();
										});

										$("#sel-category").change(function() {
											categoryID = $(this).val();
										});
									}

									$("#btn-generate").unbind("click").click(function() {
										if (what == "pmf") {
											divisionID = $("#sel-division").val();
										} else if (what == "ios") {
											divisionID = $("#sel-division").val();
											classification = $("#sel-classification").val();
										} else if (what == "pcppe") {
											classification = $("#sel-classification").val();
											categoryID = $("#sel-category").val();
										}

										$("#print-content-2").removeAttr("hidden", "hidden")
															 .html('<h3 class="font-color-1">Loading...</h3>')
															 .fadeOut("slow", function() {
															 	$(this).load(phpFile2, { 
																		  "startDate": startDate, 
																		  "endDate": endDate,
																		  "divisionID": divisionID, 
																		  "classification": classification, 
																		  "category": categoryID
																		}, function() {
																			$(this).fadeIn("slow");
																			$("#toggle-display").val("1");
																			initializeInput();
																		});
															 })
															 
									});

									$("#btn-ok").unbind('click').click(function() {
										var withError = inputValidation(false);
										var toggleTableDisplay = $("#toggle-display").val();

										if (toggleTableDisplay == "1") {
											if (!withError) {
												proccessReportData();
											}
										} else {
											alert("Error: Generate first the data table.");
										}
										
									});
									
									$("#btn-print").unbind('click').click(function() {
										printReport();
									});
								});
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			paperSize = "3";
			fontScale = 0;
			toggleModal = 0;
			startDate = currentDate.format('MM/DD/YYYY').toString();
			endDate = currentDate.format('MM/DD/YYYY').toString();
			$("#toggle-display").val("0");
			$("#print-content-1").html('<h3 class="font-color-1">Loading...</h3>');
			$("#print-content-2").attr("hidden", "hidden").html('<h3 class="font-color-1">Loading...</h3>');
		});
	}

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&startDate=" + startDate + "&endDate=" + endDate + 
										"&what=" + what + "&font-scale=" + fontScale + 
										"&paper-size=" + paperSize + "&class=" + classification +
										"&categoryID=" + categoryID);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&startDate=" + startDate + "&endDate=" + endDate + 
										"&what=" + what + "&font-scale=" + fontScale + 
										"&paper-size=" + paperSize + "&class=" + classification +
										"&categoryID=" + categoryID);
	});

	$("#btn-continue").click(function() {
		var withError = inputValidation(false);

		if (!withError) {
			what = $("#sel-report-menu").val();

			initialize();
		}
	});

	initialTruncate();

	$.fn.deleteItem = function(element) {
		if (confirm("Delete this item?")) {
			$("#" + element).remove();
		}
	}
});