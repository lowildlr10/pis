$(function() {
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

	$.fn.checkIt = function(evt) {
		var charCode = (evt.which) ? evt.which : event.keyCode

		if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
		//alert("Please make sure entries are numbers only.")
			return false
		}

		return true
	}

	$.fn.computeCost = function(cnt, obj) {
		var objId;

	    if (obj != null) {
	      objId = obj;
	    } else {
	      objId = this.id;
	    }

		if (objId.search(/txtQty/i) == 0) {
			cnt = parseFloat(objId.replace('txtQty',' '), 10);		
		} else {
			cnt = parseFloat(objId.replace('txtEUC',' '), 10);
		}

		$('#txtEC' + cnt).val($('#txtEUC' + cnt).val() * $('#txtQty' + cnt).val());
	}

	$.fn.check_input = function(frm) {
		var withError = inputValidation(false);

		if (withError) {
			return false;
		}
	}

	$.fn.addRow = function(tableID) {
		var table = $(tableID).get(0);
		var rowCount = table.rows.length; 
		var row = table.insertRow(rowCount);
		var iteration = rowCount;
		var origCount = $('#txtItemCount').get(0).value;
		var conCount = eval(origCount) + 1;
	    var colCount = table.rows[0].cells.length;

	    for (var i = 0; i < colCount; i++) {
	        var newcell = row.insertCell(i);
	        $(newcell).html($(table.rows[1].cells[i]).html());
	        

			switch(i){
				case 0:
					$(newcell).find('input')
							  .attr('id', 'chk' + conCount)
							  .attr('name', 'chk' + conCount)
							  .attr('checked', false);
				break;
				case 1:
					$(newcell).find('input')
							  .attr('id', 'txtQty' + conCount)
							  .attr('name', 'txtQty' + conCount)
							  .attr('placeholder', '...')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "txtQty' + conCount + '")')
							  .val('');
				break;
				case 2:
					$(newcell).find('select')
							  .attr('id', 'selUnit' + conCount)
							  .attr('name', 'selUnit' + conCount)
							  .val('');
				break;
				case 3:
					$(newcell).find('textarea')
							  .attr('id', 'txtDesc' + conCount)
							  .attr('name', 'txtDesc' + conCount)
							  .attr('placeholder', 'Type here...')
							  .val('');
				break;
				case 4:
					$(newcell).find('input')
							  .attr('id', 'txtStockNo' + conCount)
							  .attr('name', 'txtStockNo' + conCount)
							  .attr('placeholder', '...')
							  .val('');
				break;
				case 5:
					$(newcell).find('input')
							  .attr('id', 'txtEUC' + conCount)
							  .attr('name', 'txtEUC' + conCount)
							  .attr('placeholder', '...')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "txtEUC' + conCount + '")')
							  .val('');
				break;
				case 6:
					
					$(newcell).find('input')
							  .attr('id', 'txtEC' + conCount)
							  .attr('name', 'txtEC' + conCount)
							  .attr('disabled', 'disabled')
							  .val('');
				break;
				case 7:
					$(newcell).find('a')
							  .attr('href', 'javascript: $(this).deleteRow(\'tblInn\',\'chk' + conCount + '\');');
				break;					
			}
	    }

	    $("#txtItemCount").val(conCount);
	}

	$.fn.deleteRow = function(tableID, who) {
		if (confirm('Are you sure you want to remove this item?')) {
			document.getElementById(who).checked = 1;

			try {
			    var table = document.getElementById(tableID);
			    var rowCount = table.rows.length;

				for (var i = 0; i<rowCount; i++) {
			        var row = table.rows[i];
			        var chkbox = row.cells[0].childNodes[0];

			        if (null != chkbox && true == chkbox.checked) {                   
						if (rowCount <= 2) {
							 alert("Cannot delete all the rows.");
							 document.getElementById(who).checked=0;
						} else {
							table.deleteRow(i);
			            }

						rowCount--;
			            i--;
			        }
			    }
		    } catch(e) {
		        alert(e);
		    }
		}
	}

	$("#txtPRDate").datetimepicker({
		viewMode: 'days',
		format: 'MM/DD/YYYY'
	});
});