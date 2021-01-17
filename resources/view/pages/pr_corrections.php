<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
start_layout("DOST-CAR Procurement System","Purchase Request");	
if(isset($_GET['result'])){
	switch($_GET['result']){
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
if(isset($_POST['itemCheck'])){
	$action = $_POST['operation'];
	$prProc_ok = "";
	$prProc_no = "";
	while(list(,$val) = each($_POST['itemCheck'])){
		parse_str($val);
		switch ($action){
		case "delete":
			//remove item
			$qry = $conn->query("DELETE FROM tblpr_info WHERE prID='".$pid."'");
			$qry = $conn->query("DELETE FROM tblpr WHERE prID='".$pid."'");
		break;
		case "finalized":
			//finalized item for approval			
			$qryCheckItem = $conn->query("SELECT infoID FROM tblpr_info WHERE prID='".$pid."' LIMIT 1");
			if(mysqli_num_rows($qryCheckItem)){
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
			}else{
				$prProc_no .= "'".$pr."',";
			}		
		break;
		case "cancel":
			$req_status = "cancelled";
			$cancelled = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($_POST['reason'],$allowed_tags)) : strip_tags($_POST['canReason'],$allowed_tags);
			$cancelled.="<br /> Date Cancelled: ".date("m/d/y g:i a");
			if(trim($cancelled)){
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
		if(mysqli_affected_rows($conn) != -1){
			$prProc_ok .= "'".$pr."',";
		}else{
			$prProc_no .= "'".$pr."',";
		}
	}//end while
	$prProc_ok = substr($prProc_ok,0,-1);
	switch($action){
	case "delete":
		$prResult = "Purchase Request ".$prProc_ok." has been deleted.";
		if(!empty($prProc_no)){
			$prProc_no = substr($prProc_no,0,-1);
			$prResult = "Error: Purchase Request ".$prProc_no." has not been deleted.";
		}
	break;
	case "finalized":
		$prResult = "Purchase Request ".$prProc_ok." has been finalized.";
		if(!empty($prProc_no)){
			$prProc_no = substr($prProc_no,0,-1);
			$prResult = "Error: Purchase Request ".$prProc_no." has not been finalized. PR may not have item on it or PRNo is not set.";
		}
	break;
	case "cancel":
		$prResult = "Purchase Request ".$prProc_ok." has been cancelled.";
		if(!empty($prProc_no)){
			$prProc_no = substr($prProc_no,0,-1);
			$prResult = "Error: Purchase Request ".$prProc_no." has not been cancelled.";
		}
	break;
	}
}//end process check
if(isset($_POST['hdPRcopy']) && ! empty($_POST['hdPRcopy'])){
	
	//copy purchase items
/*	$copyPID = $_POST['hdPRcopy'];
	$qryPRitems = $conn->query("SELECT
`tblpr`.`prID`,`tblpr`.`requestBy`, `tblpr`.`signatory`,
`tblpr_info`.`quantity`,
`tblpr_info`.`unitIssue`,
`tblpr_info`.`itemDesc`,
`tblpr_info`.`stockNo`,
`tblpr_info`.`estUnitCost`,
`tblpr_info`.`estTotCost`,
`tblpr`.`purpose`
FROM
`tblpr`
Inner Join `tblpr_info` ON `tblpr`.`prID` = `tblpr_info`.`prID`
WHERE `tblpr`.`prID` = '".$copyPID."'");
	if(mysqli_num_rows($qryPRitems)){
		while($items = $qryPRitems->fetch_object()){
			
		}
	}else{
		$prResult = "No items to be copied from the selected PR";
	}
*/
}
?>
<script language="javascript" type="text/javascript">
function ifCheck(action){
	var flag = false;
	frm = document.frmPRPost;
	for (i=0; i<frm.elements.length;i++){
		if (frm.elements[i].checked == true){
			flag = true;
			i=frm.elements.length;			
		}	
	}
	if(flag==false){
		alert("No item selected. Please select an item to be processed.");;
	}else{
		if(confirm("Are you sure you want to "+action+" selected item(s) from the lists?")){
			frm.operation.value=action;
			document.frmPRPost.submit();
		}
	}
}
function ifCheck2(action){
	var flag = false;
	frm = document.frmPRs;
	for (i=0; i<frm.elements.length;i++){
		if (frm.elements[i].checked == true){
			flag = true;
			i=frm.elements.length;			
		}	
	}
	if(flag==false){
		alert("No item selected. Please select an item to be processed.");;
	}else{
		//if(confirm("Are you sure you want to "+action+" selected item(s) from the lists?")){
			frm.operation.value=action;
			document.frmPRs.submit();
		//}
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
				document.frmPRPost.operation.value=action;
				document.frmPRPost.submit();
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
			document.frmPRPost.elements[i].checked=1;
		}else{
			document.frmPRPost.elements[i].checked=0;
		}
	}	
}
function ifempty(){
	if(document.frmOperation.txtSearch.value=="" || document.frmOperation.txtSearch.value==" "){
		document.frmOperation.txtSearch.value = 'scholarname...';
	}
}

</script>

<script language="javascript" src="../jscript/functions.js"></script>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="system_libraries.php">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-4" style="padding: 0px;"></div>
		
		<div class="col-md-5" style="padding: 0px;">
			<div class="btn-group btn-group-justified">
				<a class="btn btn-primary operation" href="javascript: ifCheck2('approved')" class="">Approve</a> 
				<a class="btn btn-default operation" href="javascript: ifCheck2('disapproved')" class="">Disapprove</a>
			</div>
		</div>
	</div>
</div>

<?php
if(isset($prResult)){
	echo '<br /><br /><br />';
	echo '<div class="msg">'.$prResult.'</div>';
}
?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 12px">
		<div class="col-md-3" style="padding: 0px;"> 
			<?php
				$show = 'all';

				if(isset($_REQUEST['selFilter'])){
					$show = $_REQUEST['selFilter'];
				}

				//reset limit to 1
				if(isset($_POST['selFilter'])){
					unset($_REQUEST['limit']);
				}
			?>

			<form method="post" name="frmFilter" action="#" class="form-inline">
				<div class="form-group" style="text-align: left;">
					<label for="selFilter">
						<strong class="font-color-2">Filter Display:</strong> 
					</label>
				 	
				    <select class="form-control" id="selFilter" name="selFilter" onchange="this.form.submit();">
				    	<option value="all"<?php echo $show=='all'?' selected="selected"':'' ?>>All</option>
				        <option value="pending"<?php echo $show=='pending'?' selected="selected"':'' ?>>For Approval</option>
				        <option value="approved"<?php echo $show=='approved'?' selected="selected"':'' ?>>Approved Requests</option>
				        <option value="for_po"<?php echo $show=='for_po'?' selected="selected"':'' ?>>For PO</option>
				        <option value="for_inspection"<?php echo $show=='for_inspection'?' selected="selected"':'' ?>>For Inspection</option>
				        <option value="for_inv"<?php echo $show=='for_inv'?' selected="selected"':'' ?>>For Inventory</option>
				        <option value="disapproved"<?php echo $show=='disapproved'?' selected="selected"':'' ?>>Disapproved Requests</option>
				        <option value="cancelled"<?php echo $show=='cancelled'?' selected="selected"':'' ?>>Cancelled Requests</option>
				        <option value="closed"<?php echo $show=='closed'?' selected="selected"':'' ?>>Closed Requests</option>
				    </select>
				</div>
			</form>
		</div>
		<div class="col-md-3" style="padding: 0px;"></div>
		<div class="col-md-6" style="padding: 0px;"></div>
	</div>
</div>


<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
	<table class="table table-hover table-responsive" id="tblStyle"> 
  <tr>
    <th>Purchase Requests Made</th>
  </tr>
  <tr>
    <td><?php
    if(!isset($_SESSION['showPerPage'])){
	$_SESSION['showPerPage'] = 30;
}
if(isset($_POST['txtPerPage'])){
	$_SESSION['showPerPage'] = $_POST['txtPerPage'];
	unset($_REQUEST['limit']);
}
$perPage = $_SESSION['showPerPage'];
	if(isset($_REQUEST['limit'])){
		$accessPage = $_REQUEST['limit'];
		$startlimit = $accessPage * $perPage - $perPage;
		$limit = $startlimit.",".$perPage;	
	}else{
		$limit = "0,".$perPage;
		$accessPage = 1;					
	}
	$_SESSION['curPage'] = $accessPage;	
	
	if($show == 'all'){
	$countQry = "SELECT COUNT(prID) totalBut FROM tblpr WHERE req_status <> 'for_posting'";
	$qryForPosting = "SELECT prID, PRNo, purpose, PRDate, req_status, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name FROM tblpr prs INNER JOIN tblemp_accounts emps ON prs.requestBy = emps.empID WHERE req_status <> 'for_posting' ORDER BY PRNo DESC LIMIT $limit";
	}else{
		$countQry = "SELECT COUNT(prID) totalBut FROM tblpr WHERE req_status = '".$show."'";
		$qryForPosting = "SELECT prID, PRNo, purpose, PRDate, req_status, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name FROM tblpr prs INNER JOIN tblemp_accounts emps ON prs.requestBy = emps.empID WHERE req_status = '".$show."' ORDER BY PRNo DESC LIMIT ".$limit."";
	}
	if($resQry = $conn->query($qryForPosting)){
		if(mysqli_num_rows($resQry)){
		echo '<form method="post" name="frmPRs" action="">';
		echo '<table class="table" cellpadding="4" cellspacing="0" id="tblLists" align="center" width="97%" align="center">';
		echo '<tr><th width="2%"><input type="hidden" value="'.$show.'" name="selFilter" /><input type="hidden" name="operation" /></th><th width="5%">&nbsp;</th><th align="left" style="padding-left: 10px;" width="10%">PRNo</th><th width="10%">PR Date</th><th width="40%">Purpose</th><th width="15%">Requested By</th><th width="5%">Status</th><th width="5%">Edit</th></tr>';
		$ctr=0;
		$fCount = 0;
		while($data = $resQry->fetch_object()){
			$fCount++;
			$ctr++;
			echo '<tr id=row_0>';
			echo '<td><font color="#999999">'.$ctr.'</font></td>';
			echo '<td>';
			switch ($data->req_status){
			case 'pending':
			echo '<input type="checkbox" name="itemCheck[]" value="pid='.$data->prID.'&pr='.$data->PRNo.'" id="pr_'.$data->prID.'" />';
			break;
			case 'approved':
			echo '<img class="img-button" src="../../assets/images/approved.png" />';
			break;
			case 'disapproved':
			echo '<img class="img-button" src="../../assets/images/disap.gif" />';
			break;
			case 'cancelled':
			echo '<img  class="img-button" src="../../assets/images/cancel.gif" />';	
			break;
			default:
			echo '<img src="../../assets/images/approved.gif" />';
			break;
			}
			echo '</td>';	
			echo '<td>';
			if(!empty($data->PRNo)){
				echo $data->PRNo;
			}else{
				echo '<a href="purchase_op.php?stat='.$data->req_status.'&edit='.$data->prID.'"><img class="img-button" src="../../assets/images/edit.png" alt="Edit" /></a>';
			}
			echo '</td>';
			echo '<td>'.$data->PRDate.'</td>';
			echo '<td align="left" onclick="servOC('.$fCount.',\'pr_info.php\',\'\')" style="padding-left: 20px;" id="name'.$fCount.'"><img class="img-button" src="../../assets/images/down.png"> '.$data->purpose.'</td>';
			echo '<td>'.$data->name.'</td>';
			echo '<td>';
			switch ($data->req_status){
			case 'pending':
			echo 'For Approval';
			break;
			case 'approved':
			echo 'Approved';
			break;
			case 'disapproved':
			echo 'Disapproved';
			break;
			case 'cancelled':
			echo 'Cancelled';	
			break;
			case 'for_po':
			echo 'For PO';	
			break;
			case 'for_inspection':
			echo 'For Inspection';	
			break;
			case 'for_inv':
			echo 'For Inventory';	
			break;
			case 'closed':
			echo 'Closed';	
			break;
			default:
			echo '';
			break;
			}
			echo '</td>';
			echo '<td>';
			if($data->req_status != 'for_inv'){
				echo '<a href="predit_op.php?edit='.$data->prID.'" title="Edit"><img class="img-button" src="../../assets/images/edit.png" alt="edit" /></a>';
			}
			echo '</td>';
			echo '</tr>';
			echo '<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'"><td colspan="8" class="pr-info"><iframe id="ihif'.$fCount.'" frameborder="0" width="100%" src="pr_info.php?selected='.$data->prID.'"></iframe></td></tr>';
		}
		
		echo '</table><input type="hidden" name="hdPRcopy" value="" /></form>';	
		}else{
			echo '<div align="center" style="color:#999999"><br />----- No available record. -----<br /><br /></div>';
		}
	}
	?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
 <?php
 display_pages($conn,$countQry,9,$accessPage,$perPage,"&selFilter=".$show."");
 ?> 
</table>
</div>
<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>