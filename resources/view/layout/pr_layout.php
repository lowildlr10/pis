<?php

session_start();
include_once("../../../layout/module_layout.php");	
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$startlimit = 0;
$fCount = 0;

if (!isset($_SESSION['uU_Log'])) {
	header("Location:  " . $dir . "index.php");
} else {
	start_layout("DOST-CAR Procurement System","Purchase Request");	

	if (isset($_GET['result'])) {
		switch ($_GET['result']) {
			case 1:
				$prResult = "New purchase request has been added.";	
				break;
			case 2:
				$prResult = "Purchase request has been updated.";			
				break;
			case 3:
				$prResult = "Error encountered: Purchase Requests has not been processed successfully.";				
				break;
			case 4:
				$prResult = "Request has been saved except no PRNo.";
				break;
			case 5:
				$prResult = "Request has been saved except PRNo has not been added. Duplicate PRNo.";
				break;
		}
	}

	//processed check items
	if (isset($_POST['itemCheck'])) {
		$action = $_POST['operation'];
		$prProc_ok = "";
		$prProc_no = "";

		while (list(,$val) = each($_POST['itemCheck'])) {
			parse_str($val);

			switch ($action){
				case "delete":
					//remove item
					$qry = $conn->query("DELETE FROM tblpr_info 
										 WHERE prID='".$pid."'");
					$qry = $conn->query("DELETE FROM tblpr 
										 WHERE prID='".$pid."'");
					break;
				case "finalized":
					//finalized item for approval			
					$qryCheckItem = $conn->query("SELECT infoID 
												  FROM tblpr_info 
												  WHERE prID='".$pid."' LIMIT 1");

					if (mysqli_num_rows($qryCheckItem)) {
						$req_status = "pending";
						$tblAccess = new db_operation;
						$tblAccess->initialize("tblpr");
						$tblAccess->update(compact('req_status'),"prID='".$pid."'",$conn);	
						/* auto create prno
						$qryCheckPR = $conn->query("SELECT PRNo,PRDate FROM tblpr WHERE prID='".$pid."'");
						$data = $qryCheckPR->fetch_object();
						if(empty($data->PRNo)){
							$pur=explode('/',$data->PRDate);
							$pr =$pur[0]."".$pur[1]."".substr($pur[2],2,2)."".$pid; 
							//$finDate = date("mdy",time())."".$pid;
							$conn->query("UPDATE tblpr SET PRNo='".$pr."' WHERE prID='".$pid."'");
						}*/
					} else {
						$prProc_no .= "'".$pr."',";
					}		
					break;
				case "cancel":
					$req_status = "cancelled";
					$cancelled = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($_POST['reason'], $allowed_tags)) : 
								  strip_tags($_POST['canReason'], $allowed_tags);
					$cancelled.="<br /> Date Cancelled: ".date("m/d/y g:i a");

					if (trim($cancelled)) {
						$tblAccess = new db_operation;
						$tblAccess->initialize("tblpr");
						$tblAccess->update(compact('req_status','cancelled'),"prID='".$pid."'",$conn);
					}
					break;
				case "approved":
					//finalized item for approval
					$req_status = "approved";
					//if(!empty($pr)){
						$tblAccess = new db_operation;
						$tblAccess->initialize("tblpr");
						$tblAccess->update(compact('req_status'),"prID='".$pid."'",$conn);
					/*}else{
						echo '<script>alert("Empty PRNO not allowed.");</script>';
					}*/
					break;
				case "disapproved":
					//finalized item for approval
					$req_status = "disapproved";
					$tblAccess = new db_operation;
					$tblAccess->initialize("tblpr");
					$tblAccess->update(compact('req_status'),"prID='".$pid."'",$conn);
					break;
				default:
					break;
			}//end switch

			if (mysqli_affected_rows($conn) != -1) {
				$prProc_ok .= "'".$pr."',";
			} else {
				$prProc_no .= "'".$pr."',";
			}
		}//end while

		$prProc_ok = substr($prProc_ok,0,-1);

		switch ($action) {
			case "delete":
				$prResult = "Purchase Request ".$prProc_ok." has been deleted.";

				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been deleted.";
				}
				break;
			case "finalized":
				$prResult = "Purchase Request ".$prProc_ok." has been finalized.";

				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been finalized. PR may not have item on it or PRNo is not set.";
				}
				break;
			case "cancel":
				$prResult = "Purchase Request ".$prProc_ok." has been cancelled.";
				
				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been cancelled.";
				}
				break;
		}
	}//end process check

?>

<script type="text/javascript" src="../../../assets/js/functions.js"></script>
<script type="text/javascript">
	function ifCheck(action) {
		var flag = false;
		frm = document.frmPRPost;

		for (i = 0; i < frm.elements.length; i++){
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
	function ifCheck2(action) {
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
				frm.operation.value=action;
				document.frmPRs.submit();
			//}
		}
	}

	function checkItem1(prNum,who,action) {
		if(confirm("Are you sure you want to "+action+" item no '"+prNum+"' from the lists?")){
			document.getElementById(who).checked=1;
			document.frmPRPost.operation.value=action;
			document.frmPRPost.submit();
		}
	}
	function checkItem(prNum,who,action){
		var reason;
		if(confirm("Are you sure you want to "+action+" item no '"+prNum+"' from the lists?")){
			if(action != 'cancel'){
				if(action == 'copy'){
					window.location.href="purchase_op.php?copypr="+who+"";
					
				}else{
					document.getElementById(who).checked=1;
					document.frmPRs.operation.value=action;
					document.frmPRs.submit();
				}
			}else{
				reason = prompt("Reason for cancellation?");
				if(reason){
					document.getElementById(who).checked=1;
					document.frmPRs.operation.value=action;
					document.frmPRs.canReason.value=reason;
					document.frmPRs.submit();
				}
			}
		}
	}
	function checkAll(){
		for (i=0; i<document.frmPRPost.elements.length;i++){
			if(document.frmPRPost.chAll.checked == true){			
				document.frmPRPost.elements[i].checked = 1;
			}else{
				document.frmPRPost.elements[i].checked = 0;
			}
		}	
	}
	function ifempty(){
		if(document.frmOperation.txtSearch.value =="" || document.frmOperation.txtSearch.value == " "){
			document.frmOperation.txtSearch.value = 'scholarname...';
		}
	}
	function submitPrint(who, what){
		document.frmSize.print.value = who;
		document.frmSize.what.value = what;
		document.frmSize.submit();

		console.log(document.frmSize.print.value + " " + document.frmSize.what.value);
	}

	
</script>

<form id="frmSize" name="frmSize" method="post" action="../pr_preview.php" target="_blank">
	<div align="right" style="padding-right: 6em;" hidden>
		<table width="654" border="0" cellspacing="1" cellpadding="4" id="tblPrint">
		    <tr>
			    <th colspan="4" align="center">
			    	Print Options
			    </th>
			</tr>
			<tr>
				<td width="185" align="right">
					E-signature:
				</td>
				<td width="144">
					<input type="radio" name="rdEsign" id="radio" value="yes">
					yes
			  		<input name="rdEsign" type="radio" id="radio2" value="no" checked="checked">
					no
				</td>
			    <td width="144">
			    	Paper Size (Height 
			    	<br>
					default: 11.69 inch) :
				</td>
			    <td width="144">
			    	<input name="txtHeight" type="text" id="txtHeight">
			    </td>
			</tr>
		</table>

	  	<br>

	  	<input name="print" type="hidden" id="print">
	  	<input name="what" type="hidden" id="what">
	</div>
</form>

<div align="right" style="padding-right:6em;">
	<table border="0" cellspacing="1" cellpadding="4" id="tblPrint">
	    <tr>
	      	<th colspan="4" align="left">
	      		<span>Search by:</span>
	      	</th>
	    </tr>
	    <tr>
	    	<td>
	      		<select name="searchBy" id="searchBy">
					<option value="pr-number" selected="selected"> PR Number </option>
					<option value="pr-date"> PR Date </option>
					<option value="purpose"> Purpose </option>
					<option value="requested-by"> Requested By </option>
				</select>
				<input id="txtSearch" type="text" name="txtSearch" placeholder="Search" width="70%">
				<input id="btnSearch" type="submit" name="btnSearch" value="Search">
				<input id="btnClear" type="submit" name="btnClear" value="Clear">
	      	</td>
	    </tr>
	</table>
	<br>
</div>

<br>

<?php
	end_layout();
}//end if
?>