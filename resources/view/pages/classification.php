<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
if(isset($_POST['delCheck'])){
	while(list(,$val) = each($_POST['delCheck'])){
		parse_str($val);
		$qryCheckSig = $conn->query("SELECT bidderID FROM tblbidders WHERE classID='".$cid."' LIMIT 1");
		if(!mysqli_num_rows($qryCheckSig)){
			$conn->query("DELETE FROM tblclassifications WHERE classID='".$cid."'");
		}else{
			$cannot .= "<br />- ".$class;
		}
		if(isset($cannot)){
			$result = "Cannot delete the following... (classification is in used.)".$cannot."";
		}
	}
}
start_layout("DOST-CAR Procurement System","Suppliers Classification");
?>
<script language="javascript" type="text/javascript">{
function ifCheck(){
	for (i=0; i<document.frmSign.elements.length;i++)
		if (document.frmSign.elements[i].checked == true)
			flag = true;
		if (flag == true)
			if(confirm("Are you sure you want to delete all checked?")){
				document.frmSign.submit();
			}
	}
}
function checkAll(){
	for (i=0; i<document.frmSign.elements.length;i++){
		if(document.frmSign.chAll.checked == true){			
			document.frmSign.elements[i].checked=1;
		}else{
			document.frmSign.elements[i].checked=0;
		}
	}	
}
function checkDelete(school,who){
	if(confirm("Are you sure you want to remove '"+school+"' from the lists?")){
		document.getElementById(who).checked=1;
		document.frmSign.submit();
	}
}
</script>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="system_libraries.php">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-6" style="padding: 0px;"></div>
		
		<div class="col-md-3" style="padding: 0px;">
			<div class="btn-group btn-group-justified">
				<a class="btn btn-primary operation" href="classification_op.php" class="operation">Add Classification</a>
    			<a class="btn btn-default operation" href="javascript: ifCheck();" class="operation">Delete Classification</a>
			</div>
		</div>
	</div>
</div>

<?php
if(isset($result)){
	echo '<div class="msg">'.$result.'</div>';
}
?>
<?php
if(!isset($_SESSION['showPerPage'])){
	$_SESSION['showPerPage'] = 25;
}
if(isset($_POST['txtPerPage'])){
	$_SESSION['showPerPage'] = $_POST['txtPerPage'];
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
	
	//display list
	$qry = "SELECT COUNT(classID) totalBut FROM tblclassifications";
	$qrySign = $conn->query("SELECT * FROM tblclassifications ORDER BY classification ASC LIMIT $limit");
	echo '<form name="frmSign" method="post">';
	echo '<table class="table" cellpadding="4" cellspacing="0" id="tblStyle" align="center" width="85%">
		  <tr><th>Bidders Classification</th></tr>
		  <tr><td>';
	echo '<table class="table table-hover" cellpadding="4" cellspacing="0" id="tblLists" align="center" width="97%">';
	if(mysqli_num_rows($qrySign)){
			echo '<tr><th width="5%"><input type="checkbox" value="" name="chAll" onclick="checkAll();" /></th><th style="padding-left: 20px; text-align: left;">Classification</th><th width="5%">Edit</th><th width="5%">Delete</th></tr>';
		while($data=$qrySign->fetch_object()){
			echo '
			<tr id="row_0">
			<td><input type="checkbox" name="delCheck[]" value="class='.$data->classification.'&cid='.$data->classID.'" id="signa_'.$data->classID.'" /></td>
			<td align="left" style="padding-left: 20px;">'.$data->classification.'</td>
			<td><a href="classification_op.php?edit='.$data->classID.'" title="Edit classification"><img class="img-button" src="../../assets/images/edit.png" alt="Edit" /></a></td>
			<td><a href="javascript: checkDelete(\''.$data->classification.'\',\'signa_'.$data->classID.'\');" title="Delete classification"><img class="img-button" src="../../assets/images/delete.png" alt="Delete" /></a></td>
			</tr>';
		}
	}			
	echo '</table>';
	echo '</td></tr>';
	display_pages($conn,$qry,1,$accessPage,$perPage);
	echo '</table>';
	echo '</form>';
?>
<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>