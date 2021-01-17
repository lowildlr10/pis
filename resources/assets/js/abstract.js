$(function() {
	var who, what, absdate, prNo;
	var segmentList = [];
	var toggleSecondMember = "Yes";
	var toggleAlternateMember = "No";
	var infoData = [];
	var paperSize = "3";
	var fontScale = 0;
	var toggleModal = 0;

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

	function saveAbstract(toggle, _data, key) {
		$.post('db_operation.php', {
		    data: _data,
		    prID: who,
		    bidCount: _data[10], 
		    toggle: "save-abstract"
		}).done(function(data) {
			//console.log(data);
			//console.log(key);
			//console.log("Success");
		}).fail(function(xhr, status, error) {
			//console.log("Error");
			saveAbstract(toggle, _data);
		});
	}

	function proccessData(listGroup, toggle) {
		var withError = false;
		var abstractData = [];
		var abstractDate = $("#txtDate-val").val();
		withError = inputValidation(withError);

		if (!withError) {
			var increment = 0;

			$.each(segmentList, function(i, segment) {
				$(segment).find(".bidder-select").each(function() {
					var bidderCount = $(this).val();

					if (bidderCount > 0) {
						var bidderID = [];
						
						$(segment).find(".abstract-suppliers .group-bidders").each(function() {
							bidderID.push($(this).val());
						});

						$(segment).find(".group-segment").each(function() {
							var tempData = [];

							$(this).find(".abstractData").each(function() {
								var bidCount = 0;
								var infoID = "";
						    	var unitCost = [];
						    	var bidID = [];
						    	var totalCost = [];
						    	var txtRemarks = [];
						    	var txtSpecification = [];
						    	var txtSelection = [];
						    	var selAward = "";
						    	var txtAwardRemarks = "";

						    	$(this).find('td').each(function() {
						    		$(this).find(".itemID").each(function() {
						    			var _infoID = $(this).val().split("-");
						    			var infoIDLength = _infoID.length;

						    			if (infoIDLength == 2) {
						    				infoID = _infoID[1];
						    			} else if (infoIDLength == 3) {
						    				infoID = _infoID[1] + "-" + _infoID[2];
						    			}

							    		//infoID = $(this).val().split("-")[1];
							    	});

							    	$(this).find(".input-bid-id").each(function() {
							    		bidID.push($(this).val());
							    	});

							    	$(this).find(".input-unit-cost").each(function() {
							    		var tempCost = parseFloat($(this).val());
							    		unitCost.push(tempCost);
							    	});
							  
							    	$(this).find(".input-total-cost").each(function() {
							    		var tempTotal = parseFloat($(this).val());
							    		totalCost.push(tempTotal);
							    	});

							    	$(this).find(".text-remarks").each(function() {
							    		txtRemarks.push($(this).val());
							    	});

							    	$(this).find(".text-specification").each(function() {
							    		txtSpecification.push($(this).val());
							    	});

							    	$(this).find(".text-selection").each(function() {
							    		bidCount++;
							    		txtSelection.push($(this).val());
							    	});

							    	selAward = $(this).find(".select-award").val();
							    	txtAwardRemarks = $(this).find(".text-award-remarks").val();
						    	});

						    	tempData.push([ who,
									    		infoID,
									    		selAward,
									    		txtAwardRemarks,
									    		unitCost,
									    		totalCost,
									    		txtRemarks,
									    		txtSelection,
									    		bidderID,
									    		bidID,
									    		bidCount,
									    		abstractDate,
									    		txtSpecification ]);
							});

							abstractData.push(tempData);
					    });
					}
				});
			});

			$.ajaxSetup({async: false});

			$.each(abstractData, function(key1, grouped) {
				$.each(grouped, function(key2, _data) {
					saveAbstract(toggle, _data, key2);
				});
			});

			$.ajaxSetup({async: true});
			$("#div-add").removeClass('disable-div');

			//window.location = "abstract.php?po_no=" + prNo;
			//return false;

			alert("Abstract saved!");
		}
	}

	function getGroupNumber() {
		var listGroup = [];

		$.post('db_operation.php', {
		    prID: who,
		    toggle: "get-group"
		}).done(function(data) {
			var list = $.parseJSON(data);

			$.each(list, function(i, value) {
		    	listGroup.push(value);
			});
		}).fail(function(xhr, status, error) {
			getGroupNumber();
		});

		return listGroup;
	}

	function getBidderCount(element, toggle, grpNumber, index) {
		var bidderCount = 0;
		var _data = [grpNumber];

		$.post('db_operation.php', {
		    prID: who,
		    data: _data,
		    toggle: "get-bidder-count"
		}).done(function(data) {
			bidderCount = parseInt(data);
			getTableData(element, toggle, grpNumber, bidderCount);

			$("#bidder-select-" + index).val(bidderCount).change(function() {
				bidderCount = $(this).val();
				getTableData("#segment-" + index, "create", grpNumber, bidderCount);
			});
		}).fail(function(xhr, status, error) {
			getBidderCount(grpNumber);
		});
	}

	function getTableData(element, toggle, grpNumber, bidderCount) {
		//$(element).html("");
		$(element).html('<h4 class="font-color-2" style="margin-top: 5%;">Loading...</h4>');
		$(element).fadeOut('slow', function(){
			$(element).load("abstract_op.php", { 
						 	"prNo": prNo,
						 	"prID": who,
						 	"toggle": toggle,
						 	"groupNumber": grpNumber,
						 	"bidderCount": bidderCount
					}, function() {
						$(element).fadeIn('slow');

					    // <select> Suppliers
					    if (bidderCount > 0) {
					    	var tempBeforeValue = "";

					    	$(element).find(".group-bidders").on('click', function() {
						        // Store the current value on focus and on change
						        tempBeforeValue = $(this).val();
						    }).unbind("change").change(function() {
					    		var isUnique = true;
					    		var selectHTML = "<option value='0'></option>";
					    		var tempCurrent = $(this).attr('id');
					    		var tempCurrentValue = $(this);

					    		$(element).find(".group-bidders").each(function() {
					    			supplierID = $(this).find("option:selected").val();
					    			supplierName = $(this).find("option:selected").text();

					    			if (tempCurrent != $(this).attr('id')) {
					    				if (tempCurrentValue.val() == supplierID) {
					    					tempCurrentValue.val(tempBeforeValue);
					    					isUnique = false;
					    				}
					    			}

					    			selectHTML += '<option value="' + supplierID + '">' + supplierName + '</option>';
						    	});

					    		if (isUnique) {
					    			$(element).find(".select-award").html(selectHTML);
					    		} else {
					    			alert("The selected suppliers must be unique.");
					    		}
					    	});
					    }
					    
					    // Numeric and Decimal input only
					    $(".input-unit-cost").keydown(function(event) {
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

					    $(element).find(".abstractData").each(function() {
					    	var qntyValue = [];
					    	var unitCostID = [];
					    	var totalCostID = [];

					    	$(this).find('td').each(function() {
					    		$(this).find(".quantity-value").each(function() {
						    		qntyValue.push(parseInt($(this).html()));
						    	});


						    	$(this).find(".input-unit-cost").each(function() {
						    		var tempCost = "#" +  $(this).attr("id");
						    		unitCostID.push(tempCost.toString());
						    	});

						  
						    	$(this).find(".input-total-cost").each(function() {
						    		var tempTotal = "#" + $(this).attr("id");
						    		totalCostID.push(tempTotal.toString());
						    	});
						  
					    	});

					    	if (bidderCount > 0) {
					    		$.each(qntyValue, function(i, qty) {
					    			for (var x = 0; x < bidderCount; x++) {
					    				$(unitCostID[x]).keyup(function() {
											var unitCostValue = parseFloat($(this).val());
											var tempStr = $(this).attr("id");
											var indexNum = parseInt(tempStr[tempStr.length -1]) - 1;

											if (!unitCostValue) {
												unitCostValue = 0;
											}
											
											$(totalCostID[indexNum]).val(parseFloat((qty * unitCostValue).toFixed(2)));
										});
					    			}
						    	});
					    	}
					    });

					    $("#btn-save").unbind("click").click(function(e) {
					    	$("#div-add").addClass('disable-div');
					    	proccessData(listGroup, toggle);
					    });
					});
		});
	}

	function generateGroupSegment(listGroup, toggle) {
		var bidderCount = 0;
		var tempHTML = "";
		var selectOption = '<option value="0"> -- Click here to select the number of supplier -- </option>';

		segmentList = [];

		for (var optCount = 1; optCount <= 20; optCount++) {
			selectOption += '<option value="' + optCount + '"> Number of Supplier: ' + optCount + ' </option>';
		}

		$.each(listGroup, function(i, grpNumber) {
			var counter = i + 1;
			tempHTML += '<div class="main-segment col-md-12">' +
						'<div col-md-12" style="padding: 9px 0px 9px 0px; background: #005e7c; border-radius: 7px 7px 0px 0px;">' +
						'<div class="col-md-12">' +
						'<label class="font-color-2" style="float: left;">Group Number ' + counter + ': </label>' +
						'<select id="bidder-select-' + i + '" class="bidder-select font-color-1 form-control" style="font-weight: bold;">' + 
						selectOption + 
						'</select>' +
						'</div>' +
						'</div>' +
						'<div id="segment-' + i + '" class="group-segment" style="margin: 15px;"></div>' +
						'</div>';
		});

		$("#create-content").html(tempHTML);
		$(".main-segment").each(function(index) {
			segmentList.push($(this));

			$(this).find(".group-segment").each(function() {
				var grpNumber = listGroup[index];

				if (toggle == "create") {
					getTableData($(this), toggle, grpNumber, bidderCount);

					$("#bidder-select-" + index).change(function() {
						bidderCount = $(this).val();
						getTableData("#segment-" + index, toggle, grpNumber, bidderCount);
					});
				} else {
					getBidderCount($(this), toggle, grpNumber, index);
				}

			});
		});
	}

	function deleteAbstract(who) {
		$.post('db_operation.php', {
		    prID: who,
		    toggle: "delete-abstract"
		}).done(function(data) {
			alert("Success: Abstract deleted!");
			window.location = "abstract.php?po_no=" + prNo;
		}).fail(function(xhr, status, error) {
			alert("Error: Cannot delete abstract!");
		});
	}

	function submitPrint() {
		fontScale = $("#txtIncreaseSize").val();
		paperSize = $("#selPaperSize").val();

		$("#print").val(who);
		$("#what").val(what);
		$("#fdate").val(absdate);
		$("#qtn").val(prNo);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);

		$("#frmSize").submit();
	}

	function printPreview() {
		infoData = [];

		infoData.push($("#inp-chairman").val());
		infoData.push($("#inp-vice").val());
		infoData.push($("#inp-member1").val());
		infoData.push($("#inp-member2").val());
		infoData.push($("#inp-member3").val());
		infoData.push($("#inp-enduser").val());

		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&font-scale=" + fontScale + "&paper-size=" + paperSize +
											"&qtn=" + prNo + 
											"&inp-chairman=" + infoData[0] +
											"&inp-vice=" + infoData[1] + 
											"&inp-member1=" + infoData[2] + 
											"&inp-member2=" + infoData[3] +
											"&inp-member3=" + infoData[4] +
											"&inp-enduser=" + infoData[5] + 
											"&fdate=" + absdate);
			$('body').addClass('modal-open');
		}).on("hidden.bs.modal", function() {
			toggleAlternateMember = "No";
			toggleSecondMember = "Yes";
			paperSize = "3";
			fontScale = 0;
			toggleModal = 0;
			$("#print-content").attr("src", "");
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}

	function continueToPrint() {
		$("#inp-chairman").val($("#chairman").val());
		$("#inp-vice").val($("#vice").val());
		$("#inp-member1").val($("#member1").val());
		$("#inp-member2").val($("#member2").val());
		$("#inp-member3").val($("#member3").val());
		$("#inp-enduser").val($("#enduser").val());

		toggleModal = 1;
		$("#modal-print-1").modal("hide");
	}
	
	$("#btn-print").unbind("click").click(function() {
		submitPrint();
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&qtn=" + prNo + 
											"&font-scale=" + fontScale + "&paper-size=" + paperSize +
											"&inp-chairman=" + infoData[0] +
											"&inp-vice=" + infoData[1] + 
											"&inp-member1=" + infoData[2] + 
											"&inp-member2=" + infoData[3] +
											"&inp-member3=" + infoData[4] +
											"&inp-enduser=" + infoData[5] + 
											"&fdate=" + absdate); 
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + who + "&what=" + what + 
											"&qtn=" + prNo + 
											"&font-scale=" + fontScale + "&paper-size=" + paperSize +
											"&inp-chairman=" + infoData[0] +
											"&inp-vice=" + infoData[1] + 
											"&inp-member1=" + infoData[2] + 
											"&inp-member2=" + infoData[3] +
											"&inp-member3=" + infoData[4] +
											"&inp-enduser=" + infoData[5] + 
											"&fdate=" + absdate); 
	});

	$.fn.showPrintDialog = function(_who, _what, _absdate, _prNo) {
		who = _who;
		what = _what;
		absdate = _absdate;
		prNo = _prNo;

		$('.tooltip').remove();

		$("#modal-print-1").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("input[name=altmember]").unbind("change").on("change", function(){
				toggleAlternateMember = $(this).val();

				if (toggleAlternateMember == "Yes") {
					if (toggleSecondMember == "Yes") {
						$("#alternate").removeAttr("disabled");
					}
				} else if ("No") {
					$("#alternate").attr("disabled", "disabled");
				}
			});

			$("input[name=secondmember]").unbind("change").on("change", function(){
				toggleSecondMember = $(this).val();

				if (toggleSecondMember == "Yes") {
					$("#member2").removeAttr("disabled");
				} else if ("No") {
					$("#member2").attr("disabled", "disabled");
				}
			});

			$("#btn-ok").unbind("click").click(function() {
				continueToPrint();
			});
		}).on("hide.bs.modal", function() {
			if (toggleModal == 1) {
				printPreview();
			}
		}).on("hidden.bs.modal", function() {
			toggleAlternateMember = "No";
			toggleSecondMember = "Yes";
			toggleModal = 0;
		});
	}

	$.fn.showAbstractDialog = function(_who, _what, _prNo, toggle) {
		who = _who;
		what = _what;
		prNo = _prNo
		listGroup = getGroupNumber();

		$('.tooltip').remove();

		$("#modal-create-abstract").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#display-pr-no").html("PR Number: " + prNo);
			generateGroupSegment(listGroup, toggle);
			
		}).on("hidden.bs.modal", function() {
			$("#create-content").html('<br><div class="progress">' +
										  '<div class="progress-bar progress-bar-striped active" role="progressbar"' +
										  'aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">' +
										    'Loading...' +
										  '</div>' +
									   '</div>');
			window.location = "abstract.php?po_no=" + prNo;
			return false;
		});
	}

	$.fn.showDeleteDialog = function(_who, _what, _absdate) {
		who = _who;
		what = _what;
		absdate = _absdate;

		$('.tooltip').remove();

		if (confirm("Are you sure you want to delete this abstract?")) {
		    deleteAbstract(who);
		}
	}

	$.fn.ifCheck = function() {
		var flag = false;
		
		for (i = 0; i < document.frmPRPost.elements.length; i++) {
			if (document.frmPRPost.elements[i].checked == true) {
				flag = true;
			}	

			if (flag == true) {
				if (confirm("Are you sure you want to finalized all checked PR?")) {
					i = document.frmPRPost.elements.length;
					document.frmPRPost.submit();
				} else {
					i = document.frmPRPost.elements.length;
				}
			}	
		}
	}

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

	$("#txtDate").datetimepicker({
		viewMode: 'days',
		format: 'MM/DD/YYYY'
	});
});