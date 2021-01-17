$(function() {
	var currentDate = moment();
	var startDate = currentDate.format('MM/DD/YYYY').toString();
	var endDate = currentDate.format('MM/DD/YYYY').toString();

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

	function saveSuppliers(bidderID, proccessData, businessName) {
		$.post('db_operation.php', {
		    data: proccessData,
		    prID: bidderID,
		    toggle: "update-supplier"
		}).done(function(data) {
			window.location = "suppliers.php?result=1&txtSearch=" + businessName;
			return false;
		}).fail(function(xhr, status, error) {
			saveSuppliers(bidderID, proccessData);
		});
	}

	function proccessData() {
		var bidderID = $("#bidderID").val();
		var fileDate = $("#txtDate").val();
		var businessName = $("#txtCompany").val();
		var classID =  $("#selClass").val();
		var address =  $("#txtAddress").val();
		var emailAddress =  $("#txtEmail").val();
		var urlAddress =  $("#txtUrlAddress").val();
		var contactPerson =  $("#txtContact").val();
		var contactNo =  $("#txtContactNo").val();
		var faxNo =  $("#txtFaxNumber").val();
		var mobileNumber =  $("#txtMobileNo").val();
		var establishedDate =  $("#txtDateEstablished").val();
		var vatNo =  $("#txtVatNumber").val();
		var nameBank =  $("#txtNameBank").val();
		var accountName =  $("#txtAccountName").val();
		var accountNo =  $("#txtAccountNumber").val();
		var natureBusiness =  $("#txtNatureBusiness").val();
		var natureBusinessOthers =  $("#txtNatureBusinessOthers").val();
		var deliveryVehicleNo =  $("#txtNoDeliveryVehicles").val();
		var productLines =  $("#txtProductLines").val();
		var creditAccomodation =  $("#txtCreditAccomodation").val();
		var attachement =  $("#txtAttachment").val();
		var attachmentOthers =  $("#txtAttachmentOthers").val();
		var supplierData = [fileDate, businessName, classID,
							address, emailAddress, urlAddress, contactPerson,
							contactNo, faxNo, mobileNumber, establishedDate,
							vatNo, nameBank, accountName, accountNo, natureBusiness,
							natureBusinessOthers, deliveryVehicleNo, productLines,
							creditAccomodation, attachement, attachmentOthers];

		saveSuppliers(bidderID, supplierData, businessName);
	}

	$("#txtDate").daterangepicker({
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
	    $(this).val(picker.startDate.format('MM/DD/YYYY'));
	}).on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});

	$("#txtDateEstablished").daterangepicker({
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
	    $(this).val(picker.startDate.format('MM/DD/YYYY'));
	}).on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});

	$("#txtNatureBusiness").change(function() {
		if ($(this).val() == "Others: (pls. specify)") {
			$("#group-business").removeAttr("hidden");
			$("#txtNatureBusinessOthers").val("");
		} else {
			$("#group-business").attr("hidden", "hidden");
			$("#txtNatureBusinessOthers").val("");
		}
	});

	$("#txtAttachment").change(function() {
		if ($(this).val() == "Others, Specify") {
			$("#group-attachment").removeAttr("hidden");
			$("#txtAttachmentOthers").val("");
		} else {
			$("#group-attachment").attr("hidden", "hidden");
			$("#txtAttachmentOthers").val("");
		}
	});

	$("#btnSubmit").unbind("click").click(function() {
		var withError = inputValidation(false);

		if (!withError) {
			proccessData();
		}
	});

	if ($("#txtNatureBusiness").val() == "Others: (pls. specify)") {
		$("#group-business").removeAttr("hidden");
	}

	if ($("#txtAttachment").val() == "Others, Specify") {
		$("#group-attachment").removeAttr("hidden");
	}
});