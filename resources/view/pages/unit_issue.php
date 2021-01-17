<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
	$searchInput = "";
	$prResult = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	if (isset($_POST['delCheck'])) {
		$ok = false;

		while (list(,$val) = each($_POST['delCheck'])) {
			$qryCheckSig = $conn->query("SELECT prID 
										 FROM tblunit_issue 
										 WHERE unit = '".$val."' LIMIT 1");
			
			if (!mysqli_num_rows($qryCheckSig)) {
				$conn->query("DELETE FROM tblunit_issue 
							  WHERE id = '".$val."'") 
							  or die(mysql_error($conn));
				$ok = true;
			}
		}

		if ($ok) {
			header("Location: unit_issue.php?deleted=1");
		}
	}

	start_layout("DOST-CAR Procurement System","Items Unit of Issue");
?>

	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="system_libraries.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-5" style="padding: 0px;"></div>
			
			<div class="col-md-4" style="padding: 0px;">
				<div class="btn-group btn-group-justified">
					<a class="btn btn-primary operation" href="unit_issue_op.php">Add Unit</a>
					<a class="btn btn-default operation" href="javascript: ifCheck();">Delete Unit</a>
				</div>
			</div>
		</div>
	</div>

<?php
	if(isset($_REQUEST['deleted'])){
		echo '<div class="msg well">Unit successfully deleted!</div>';
	}

	if (!isset($_SESSION['showPerPage'])) {
		$_SESSION['showPerPage'] = 30;
	}

	if (isset($_POST['txtPerPage'])) {
		$_SESSION['showPerPage'] = $_POST['txtPerPage'];
	}

	$perPage = $_SESSION['showPerPage'];
	if (isset($_REQUEST['limit'])) {
		$accessPage = $_REQUEST['limit'];
		$startlimit = $accessPage * $perPage - $perPage;
		$limit = $startlimit.",".$perPage;	
	} else {
		$limit = "0,".$perPage;
		$accessPage = 1;					
	}
	$_SESSION['curPage'] = $accessPage;	
	
	//display list
	$qry = "SELECT COUNT(id) totalBut 
			FROM tblunit_issue";
	$qrySign = $conn->query("SELECT * 
							 FROM tblunit_issue 
							 ORDER BY unitName ASC LIMIT $limit");
?>

<?php
	echo '<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">';
	echo '<form name="frmSign" method="POST" action="#">';
	echo '<table class="table" id="tblStyle">
		  <tr>
	 	  	<th>
		 	  	<div class="col-xs-12 col-md-12" style="padding: 0px;">
			  		<div class="col-md-3" style="padding: 0px;"> 
						<strong><label>#Unit of Issue</label></strong>
					</div>
					<div class="col-md-6" style="padding: 0px;">
						&nbsp
					</div>
					<div class="col-md-3" style="padding: 0px;">
						<form method="POST" name="frmSearch" action="#" class="form-inline">
							<div class="form-group" style="text-align: left; width: 100%;">
								<label for="txtSearch">
									<strong class="font-color-2">Search: (Click Enter to Search)</strong> 
								</label>
								<input id="txtSearch" class="form-control" type="search" name="txtSearch" placeholder="Enter a keyword first...">
							</div>
				  		</form>
					</div>
				</div>
	 	  	</th>
	 	</tr>
		  <tr><td>';
	echo '<div class="table-container-1">
			<table class="table" id="tblLists">';

	echo '<tr>
		  	  <th width="5%">
		  	  	  <input type="checkbox" value="" name="chAll" onclick="checkAll();">
		  	  </th>
		  	  <th width="90%" style="padding-left: 20px; text-align: left;">
		  	  	  Unit
		  	  </th>
		  	  <th></th>
		  	  <th></th>
		  </tr>';

	if (mysqli_num_rows($qrySign)) {
		while ($data = $qrySign->fetch_object()) {
			echo '
			<tr id="row_0">
				<td>
					<input type="checkbox" name="delCheck[]" value="'.$data->id.'" 
						   id="signa_'.$data->id.'">
				</td>
				<td align="left" style="padding-left: 20px;">'.
					$data->unitName.
				'</td>
				<td>
					<a href="unit_issue_op.php?edit='.$data->id.'" title="Edit unit">
						<img class="img-button" src="../../assets/images/edit.png" alt="Edit">
					</a>
				</td>
				<td>
					<a href="javascript: checkDelete(\''.$data->unitName.'\',\'signa_'.$data->id.'\');" 
					   title="Delete unit">
					   <img class="img-button" src="../../assets/images/delete.png" alt="Delete">
					</a>
				</td>
			</tr>';
		}
	} else {
		echo '<tr><td colspan="4"> -- No Available Data -- </td></tr>';
	}

	echo '</table></div>';
	echo '</td></tr>';
	display_pages($conn,$qry,1,$accessPage,$perPage);
	echo '</table>';
	echo '</form></div>';
?>

<script type="text/javascript">
	function ifCheck() {
		for (i = 0; i < document.frmSign.elements.length; i++) {
			if (document.frmSign.elements[i].checked == true) {
				flag = true;
			}

			if (flag == true) {
				document.frmSign.submit();
			}
		}
	}

	function checkAll() {
		for (i = 0; i < document.frmSign.elements.length; i++){
			if (document.frmSign.chAll.checked == true) {			
				document.frmSign.elements[i].checked = 1;
			} else {
				document.frmSign.elements[i].checked = 0;
			}
		}	
	}

	function checkDelete(school,who) {
		if(confirm("Are you sure you want to remove '"+school+"' from the lists?")){
			document.getElementById(who).checked=1;
			document.frmSign.submit();
		}
	}
</script>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>