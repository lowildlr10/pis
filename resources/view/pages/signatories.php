<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
	$searchInput = "";
	$prResult = "";
	$startlimit = 0;

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	/*

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
	}*/

	if (isset($_POST['delCheck'])) {
		if($_POST['hdAction'] == 'delete'){
			while(list(,$val) = each($_POST['delCheck'])){		
				$qryCheckSig = $conn->query("SELECT prID 
											 FROM tblpr 
											 WHERE signatory='".$val."' 
											 LIMIT 1");

				if (!mysqli_num_rows($qryCheckSig)) {
					$conn->query("DELETE FROM tblsignatories 
								  WHERE signatoryID='".$val."'");
				}
			}//end while
		}else{
			$conn->query("UPDATE tblsignatories 
						  SET active='no'");

			while (list(,$val) = each($_POST['delCheck'])) {		
				$conn->query("UPDATE tblsignatories SET active='yes' 
							  WHERE signatoryID='".$val."'");
			}		
		}

	}

	start_layout("DOST-CAR Procurement System", "<a href='system_libraries.php' style='color: rgb(225, 239, 243);'>System Libraries</a>/
												 <a href='signatories.php' style='color: #98ffe8;'>Signatories</a>");
?>

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
				<a class="btn btn-primary operation" href="signatories_op.php">Add Signatory</a>
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
	$qry = "SELECT COUNT(signatoryID) totalBut 
		    FROM tblsignatories";
	$qrySign = $conn->query("SELECT * 
							 FROM tblsignatories 
							 ORDER BY name 
							 ASC LIMIT $limit");
?>


	<form name="frmSign" method="post">';
	<table class="table" id="tblStyle">
		<tr>
		  	<th>
		 	  	<div class="col-xs-12 col-md-12" style="padding: 0px;">
			  		<div class="col-md-3" style="padding: 0px;"> 
						<strong><label>#Signatories</label></strong>
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
			<td>
				<div class="table-container-1">
					<table class="table" id="tblLists">
						<?php
						if (mysqli_num_rows($qrySign)) {
						?>

						<tr>
							<th width="5">
							<!--<input type="checkbox" value="" name="chAll" onclick="checkAll();" />-->
							</th>
							<th width="40%">Signatory</th>
							<th width="20%">Position</th>
							<th width="25%">Function</th>
							<th width="15">Active</th>
							<th></th>
							<th></th>
						</tr>';

						<?php
							$ctr = $startlimit;

							if (mysqli_num_rows($qrySign)) {
								while ($data = $qrySign->fetch_object()) {
									$ctr++;
									$function = "";

									if ($data->p_req == 'y') {
										$function .= "PR ";
									}

									if ($data->rfq == 'y') {
										$function .= "RFQ ";
									}

									if ($data->abs == 'y') {
										$function .= "Abstract ";
									}

									if ($data->ors == 'y') {
										$function .= "PO ";
									}

									if ($data->iar == 'y') {
										$function .= "IAR ";
									}


									if ($data->dv == 'y') {
										$function .= "DV ";
									}


									if ($data->ris == 'y') {
										$function .= "RIS ";
									}

									if ($data->par == 'y') {
										$function .= "PAR ";
									}

									if ($data->ics == 'y') {
										$function .= "ICS";
									}

									echo '<tr id="row_0">';
									echo '<td>' . $ctr . '</td>';
									echo '<td style="text-align: left; vertical-align: middle; border-right: 2px #006699 solid;">' . $data->name . '</td>';
									echo '<td style="vertical-align: middle; border-right: 2px #006699 solid;">' . $data->position . '</td>';
									echo '<td>' . $function . '</td>';
									echo '<td><strong>' . strtoupper($data->active) . '</strong</td>';
									echo '<td>
											<a href="signatories_op.php?edit='.$data->signatoryID.'" title="Edit Signatory">
										  	<img class="img-button" src="../../assets/images/edit.png" alt="Edit">
										  	</a>
										  </td>';
									echo '<td>
											<a href="javascript: checkDelete(\''.$data->signatoryID.'\',\'signa_'.$data->signatoryID.'\');" 
										  	title="Delete unit">
										  	<img class="img-button" src="../../assets/images/delete.png" alt="Delete">
										  	</a>
										  </td>';
									echo '</tr>';
								}
							}
						?>

						<?php
						} else {
						?>

						<?php
							echo '<div align="center" style="color:#999999"><br />----- No available record for posting. -----<br /><br /></div>';
						}
						?>	
					</table>
				</div>
			</td>
		</tr>

		<input type="hidden" name="hdAction" value="" /></td></tr>

		<?php
			display_pages($conn,$qry,1,$accessPage,$perPage);
		?>

		</table>
	</form>


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
			document.frmSign.hdAction.value="delete";
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