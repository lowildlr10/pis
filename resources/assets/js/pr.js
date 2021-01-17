$(function() {
	var prID, what;
	var paperSize = "1";
	var fontScale = 0;

	function submitPrint() {
		fontScale = $("#txtIncreaseSize").val();
		paperSize = $("#selPaperSize").val();

		$("#print").val(prID);
		$("#what").val(what);
		$("#font-scale").val(fontScale);
		$("#paper-size").val(paperSize);
		$("#frmSize").submit();
	}

	$("#row_0").find("a").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$("#row_0").find("input[type='checkbox']").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$("#row_0 .btn").each(function(index, element) {
		$(this).click(function (ev) {
		    ev.stopPropagation();
		});
	});

	$("#btn-print").unbind("click").click(function() {
		submitPrint();
	});

	$("#txtIncreaseSize").change(function() {
		fontScale = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + prID + "&what=" + what +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$("#selPaperSize").change(function() {
		paperSize = $(this).val();

		$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
										"preview=true&print=" + prID + "&what=" + what +
										"&font-scale=" + fontScale + "&paper-size=" + paperSize);
	});

	$.fn.ifCheck = function(action) {
		var flag = false;
		frm = document.frmPRPost;

		for (i = 0; i < frm.elements.length; i++) {
			if (frm.elements[i].checked == true) {
				flag = true;
				i = frm.elements.length;			
			}
		}

		if (flag == false) {
			alert("No item selected. Please select an item to be processed.");;
		} else {
			if (confirm("Are you sure you want to "+action+" selected item(s) from the lists?")) {
				frm.operation.value = action;
				document.frmPRPost.submit();
			}
		}
	}

	$.fn.ifCheck2 = function(action) {
		var flag = false;
		frm = document.frmPRs;

		for (i = 0; i < frm.elements.length; i++) {
			if (frm.elements[i].checked == true){
				flag = true;
				i = frm.elements.length;			
			}	
		}

		if (flag == false) {
			alert("No item selected. Please select an item to be processed.");;
		} else {
			//if(confirm("Are you sure you want to "+action+" selected item(s) from the lists?")){
				frm.operation.value = action;
				document.frmPRs.submit();
			//}
		}
	}

	$.fn.checkItem1 = function(prNum,who,action) {
		if (confirm("Are you sure you want to " + action + " item no '" + prNum + "' from the lists?")) {
			document.getElementById(who).checked = 1;
			document.frmPRPost.operation.value = action;
			document.frmPRPost.submit();
		}
	}

	$.fn.checkAll = function() {
		for (i = 0; i < document.frmPRPost.elements.length; i++) {
			if (document.frmPRPost.chAll.checked == true) {			
				document.frmPRPost.elements[i].checked = 1;
			} else {
				document.frmPRPost.elements[i].checked = 0;
			}
		}	
	}

	$.fn.showPrintDialog = function(_prID, _what) {
		what = _what;
		prID = _prID;
		$('.tooltip').remove();
		
		$("#modal-print").modal({
			backdrop: 'static', 
			keyboard: false
		}).on("shown.bs.modal", function() {
			$("#print-content").attr("src", "../../../class_function/print_preview.php?" +
											"preview=true&print=" + prID + "&what=" + what); 
		}).on("hidden.bs.modal", function() {
			$("#print-content").attr("src", "");
			paperSize = "1";
			fontScale = 0;
			$("#txtIncreaseSize").val(fontScale);
			$("#selPaperSize").val(paperSize);
		});
	}
});