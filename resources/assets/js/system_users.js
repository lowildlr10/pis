function ifCheck(){
	for (i = 0; i < document.frmUsers.elements.length; i++){
		if (document.frmUsers.elements[i].checked == true) {
			flag = true;
		}

		if (flag == true){
			if (confirm("Are you sure you want to delete all checked?")) {
				document.frmUsers.submit();
			}
		}
	}
}

function checkAll(){
	for (i = 0; i < document.frmUsers.elements.length; i++) {
		if (document.frmUsers.chAll.checked == true) {			
			document.frmUsers.elements[i].checked=1;
		} else {
			document.frmUsers.elements[i].checked=0;
		}
	}	
}

function checkDelete(user, who, action){
	toDo = action;

	if (action == 'block') {
		toDo = "unblock / block";
	}

	if (confirm("Are you sure you want to "+toDo+" '"+user+"' from the lists?")) {
		document.frmUsers.hdAction.value = action;
		document.getElementById(who).checked=1;
		document.frmUsers.submit();
	}
}

function check_input(frm){
	if (frm.txtEID.value=="" || frm.txtEID.value == " ") {
		alert("Please enter employee ID.");
		frm.txtEID.focus();
		return false;
	}

	if (frm.txtFname.value=="" || frm.txtFname.value== " ") {
		alert("Please enter the user firstname.");
		frm.txtFname.focus();
		return false;
	} else if (frm.txtMname.value=="" || frm.txtMname.value== " ") {
		alert("Please enter the user middlename.");
		frm.txtMname.focus();
		return false;
	} else if (frm.txtLname.value=="" || frm.txtLname.value== " ") {
		alert("Please enter the user lastname.");
		frm.txtLname.focus();
		return false;
	} else if (frm.selSection.value=="") {
		alert("Please enter the user section.");
		frm.selSection.focus();
		return false;
	} else if (frm.txtPosition.value=="" || frm.txtPosition.value== " ") {
		alert("Please enter the user position.");
		frm.txtPosition.focus();
		return false;
	} else if (frm.selAccess.value=="") {
		alert("Please enter the user access level.");
		frm.selAccess.focus();
		return false;
	} else {
		return true;
	}
}	

function check_input(frm){
	if(frm.txtEID.value == "" || frm.txtEID.value == " ") {
		alert("Please enter employee ID.");
		frm.txtEID.focus();
		return false;
	} else if (frm.txtFname.value == "" || frm.txtFname.value == " ") {
		alert("Please enter the user firstname.");
		frm.txtFname.focus();
		return false;
	} else if (frm.txtMname.value == "" || frm.txtMname.value == " ") {
		alert("Please enter the user middlename.");
		frm.txtMname.focus();
		return false;
	} else if (frm.txtLname.value == "" || frm.txtLname.value == " ") {
		alert("Please enter the user lastname.");
		frm.txtLname.focus();
		return false;
	} else if (frm.selSection.value == ""){
		alert("Please enter the user section.");
		frm.selSection.focus();
		return false;
	} else if (frm.txtPosition.value == "" || frm.txtPosition.value == " ") {
		alert("Please enter the user position.");
		frm.txtPosition.focus();
		return false;
	} else if (frm.txtPass.value != "" || frm.txtPass.value != " " || 
			   frm.txtPass2.value != "" || frm.txtPass2.value!=" ") {
		if (frm.txtPass.value != frm.txtPass2.value) {
			alert("New password does not match.");
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}

}	
