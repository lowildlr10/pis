function check_input(frm){
	if (frm.txtFname.value=="" || frm.txtFname.value== " "){
		alert("Please enter the user firstname.");
		frm.txtFname.focus();
		return false;
	} else if(frm.txtMname.value=="" || frm.txtMname.value== " "){
		alert("Please enter the user middlename.");
		frm.txtMname.focus();
		return false;
	} else if(frm.txtLname.value=="" || frm.txtLname.value== " "){
		alert("Please enter the user lastname.");
		frm.txtLname.focus();
		return false;
	} else if(frm.selSection.value==""){
		alert("Please enter the user section.");
		frm.selSection.focus();
		return false;
	} else if(frm.txtPosition.value=="" || frm.txtPosition.value== " "){
		alert("Please enter the user position.");
		frm.txtPosition.focus();
		return false;
	} else if(frm.txtPass.value != frm.txtPass2.value){
		alert("Password does not match.");
		frm.txtPass.focus();
		return false;
	} else{
		alert("User info has been updated.");
		return true;
	}
}