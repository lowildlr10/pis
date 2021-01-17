<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='dv.php' style='color: #98ffe8;'>Disbursement Voucher</a>");	
	
	$page = "dv";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
	$searchInput = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	if (isset($_REQUEST['result'])) {
		$prResult = 'Cannot confirm to payment yet. Put first a DV Number.';
		unset($_REQUEST['result']);
	}

?>

	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="access.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-3" style="padding: 0px;"></div>
			<div class="col-md-6" style="padding: 0px;"></div>
		</div>
	</div>

	<?php
		if(isset($prResult)){
			echo '<div class="msg well">'.$prResult.'</div>';
			unset($prResult);
		}
	?>

	<form id="frmSize" name="frmSize" method="post" action="../../../class_function/print_preview.php" target="_self">
		<input name="print" type="hidden" id="print">
		<input name="what" type="hidden" id="what">
		<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">
	</form>

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">

		<table class="table table-hover table-responsive" id="tblStyle">
			<tr>
			  	<th>
			  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
				  		<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#List for Disbursement Vouchers</label></strong>
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
									<input id="txtSearch" class='form-control' type="text" name="txtSearch" placeholder="Enter a keyword first...">
								</div>
					  		</form>
						</div>
					</div>
			  	</th>
			</tr>

		  	<tr>
			    <td>
				<?php
				    
				    if(!isset($_SESSION['showPerPage'])){
						$_SESSION['showPerPage'] = 30;
					}

					if(isset($_POST['txtPerPage'])){
						$_SESSION['showPerPage'] = $_POST['txtPerPage'];
						unset($_REQUEST['limit']);
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
					$ctr = 0;

					if (isset($_SESSION['log_admin'])) {
						$countQry = "SELECT COUNT(dv.id) totalBut 
									 FROM tblors ors 
									 INNER JOIN tbldv dv 
									 ON ors.id = dv.orsID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, ors.payee, ors.uacsObjectCode, 
				    				       dv.orsID, dv.particulars, ors.amount, dv.prID, po.poStatus, dv.dvNo 
				    				FROM tblors ors 
									INNER JOIN tbldv dv 
									ON ors.id = dv.orsID 
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(dv.id) totalBut 
									 FROM tblors ors 
									 INNER JOIN tbldv dv 
									 ON ors.id = dv.orsID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo 
									 INNER JOIN tblpr pr 
									 ON pr.prID = ors.prID 
									 INNER JOIN tblemp_accounts emp 
									 ON emp.empID = pr.requestBy 
									 WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, ors.payee, ors.uacsObjectCode, 
				    				       dv.orsID, dv.particulars, ors.amount, dv.prID, po.poStatus, dv.dvNo  
				    				FROM tblors ors 
									INNER JOIN tbldv dv 
									ON ors.id = dv.orsID 
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo 
									INNER JOIN tblpr pr 
									ON pr.prID = ors.prID 
									INNER JOIN tblemp_accounts emp 
									ON emp.empID = pr.requestBy 
				    				WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
					} else if (isset($_SESSION['log_staff'])) {
						$countQry = "SELECT COUNT(dv.id) totalBut 
									 FROM tblors ors 
									 INNER JOIN tbldv dv 
									 ON ors.id = dv.orsID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo 
									 INNER JOIN tblpr pr 
									 ON pr.prID = ors.prID 
									 INNER JOIN tblemp_accounts emp 
									 ON emp.empID = pr.requestBy 
									 WHERE empID = '" . $_SESSION['log_empID'] . "' ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, ors.payee, ors.uacsObjectCode, 
				    				       dv.orsID, dv.particulars, ors.amount, dv.prID, po.poStatus, dv.dvNo  
				    				FROM tblors ors 
									INNER JOIN tbldv dv 
									ON ors.id = dv.orsID 
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo 
									INNER JOIN tblpr pr 
									ON pr.prID = ors.prID 
									INNER JOIN tblemp_accounts emp 
									ON emp.empID = pr.requestBy 
				    				WHERE empID = '" . $_SESSION['log_empID'] . "' ";
					}

					if (isset($_GET["po_no"])) {
						$searchInput = $_GET["po_no"];
						unset($_GET["po_no"]);
					} else {
						if (isset($_GET["orsID"])) {
							$searchInput = $_GET["orsID"];
							unset($_GET["orsID"]);
						}
					}
					
					if (!empty($searchInput)) {
						echo '<label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
    									border-radius: 25px; 
    									margin-left: 3px;
    									margin-bottom: 2px;"
    						     href="dv.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (ors.orsNo LIKE '%$searchInput%' 
												  OR dv.orsID LIKE '%$searchInput%' 
												  OR ors.poNo LIKE '%$searchInput%' 
												  OR ors.payee LIKE '%$searchInput%' 
												  OR ors.address LIKE '%$searchInput%' 
												  OR dv.particulars LIKE '%$searchInput%') 
												  ORDER BY dv.id DESC LIMIT $limit";
						$qryOBRs = $qryOBRs . " AND (ors.orsNo LIKE '%$searchInput%' 
											    OR dv.orsID LIKE '%$searchInput%' 
												OR ors.poNo LIKE '%$searchInput%' 
												OR ors.payee LIKE '%$searchInput%' 
												OR ors.address LIKE '%$searchInput%' 
												OR dv.particulars LIKE '%$searchInput%') 
												ORDER BY dv.id DESC LIMIT $limit";
					} else {
						$qryOBRs = $qryOBRs . " ORDER BY dv.id DESC LIMIT $limit";
					}

					if ($resQry = $conn->query($qryOBRs)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
							echo '<table class="table table-hover" id="tblLists" align="center">';		
							echo '<tr>
									  <th width="1%"></th>
									  <th align="left" style="padding-left: 10px;" width="10%">PO No.</th>
									  <th width="70%">Particulars</th>
									  <th width="10%">Amount</th>
									  <th></th>
									  <th></th>
								  </tr>';
							$ctr = $startlimit;

							while ($data = $resQry->fetch_object()) {
								$itemCount++;
								$ctr++;
								echo '<tr id="row_0">';
								echo '<td>'.$ctr.'</td>';

								if (!empty($data->poNo)) {
									echo '<td>'.$data->poNo.'</td>';
								} else {
									echo '<td> -- Custom DV -- </td>';
								}
								
								echo '<td id="particulars-'. $data->id .'" align="left">'.$data->particulars.'</td>';
								echo '<td>'.number_format($data->amount,2,'.',',').'</td>';
								echo '<td>
										   <a data-toggle="tooltip" data-placement="left" 
										   		title="Click to print DV for this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
										   		href="javascript: $(this).printDialog(\''.$data->id.'\',\'dv\',\''.$data->prID.'\',\''.$data->poNo.'\',\'signa_'.$itemCount.'\');" 
										   		title="Print Preview">
											    <img class="img-button" src="../../assets/images/print.png"" alt="print">
										   </a>
										   <input type="text" value="' . $data->dvNo . '" id="signa_'.$itemCount.'"
										   		  name="upCheck" hidden="hidden">
									  </td>';

								echo '<td>';

								if (!isset($_SESSION['log_staff'])) {
									if ($data->poStatus == "for_disbursement") {
										echo '<a data-toggle="tooltip" data-placement="left" 
											  		title="Click to approve to payment this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
											  		href="javascript: $(this).saveToPayment(\''.$data->prID.'\',\''.$data->poNo.'\',\'signa_'.$itemCount.'\');" 
											  		title="to payment">
											      <img class="img-button" src="../../assets/images/approve.png"" alt="create iar">
											  </a>';
									} else {
										echo '<a data-toggle="tooltip" data-placement="left" 
											  		title="To Payment" 
											  		href="#" 
											  		title="Create DV">
											      <img class="img-button" src="../../assets/images/closed.gif"" alt="to payment">
											  </a>';
									}
								}
								
								echo '</td>';
								echo '</tr>';
							}

							while ($itemCount < $perPage) {
								echo "<tr id='row_0'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
								$itemCount++;
							}

							echo '</table>
								  <input type="hidden" value="final" name="hdAction" /></form></div>';
						} else {
							echo '<div align="center" style="color:#999999">
									  <br>----- No available record for ORS or print an ORS first. -----<br><br>
								  </div>';
						}
					}
				?>    
				</td>

				<?php
				display_pages($conn,$countQry,1,$accessPage,$perPage);
				?>
		  	</tr>
		</table>
	</div> 
<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/dv-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>