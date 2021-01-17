<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='obligation_request.php' style='color: #98ffe8;'>Obligation Request and Status</a>");
	
	$page = "ors";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
	$searchInput = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	if (isset($_POST['upCheck'])) {
		while (list(,$val) = each($_POST['upCheck'])) {
			parse_str($val);

			if ($status == "for_po" && !empty($serialNo)) {
				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '7'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblpo_jo 
							  SET poStatus = '" . $prStatus . "' 
							  WHERE poNo='".$pno."'");

				$prResult = "PO/JO has been obligated.";
				echo '<script>window.location.href = "purchase_job_order.php?result=1' . 
				 			   '&po_no=' . substr($pno, 0, -2)	. '";</script>';
			} else {
				$prResult = "Input a ORS serial number first.";
			}
		}
	}
?>
	
	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="access.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-5" style="padding: 0px;"></div>
			<div class="col-md-4" style="padding: 0px;">
				<!--
				<div class="btn-group btn-group-justified">
					<a class="btn btn-primary operation" href="javascript: $(this).createDialog()">Create Custom ORS</a>
					<a class="btn btn-default operation" href="dv.php">Create DV>></a>
				</div>
				!-->
			</div>
		</div>
	</div>

	<?php
		if(isset($prResult)){
			echo '<div class="msg well">'.$prResult.'</div>';
			unset($prResult);
		}
	?>

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
		<table class="table table-hover table-responsive" id="tblStyle">
			<tr>
			  	<th>
			  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
				  		<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#List for Obligation Requests</label></strong>
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
					$fCount = 0;
					$ctr = 0;

					if (isset($_SESSION['log_admin'])) {
						$countQry = "SELECT COUNT(id) totalBut 
									 FROM tblors AS ors 
									 INNER JOIN tblpr AS pr 
									 ON ors.prID = pr.prID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, 
				    					   ors.payee, ors.uacsObjectCode, ors.particulars, 
				    					   ors.amount, ors.prID, po.poStatus, ors.serialNo 
				    				FROM tblors AS ors 
									INNER JOIN tblpr AS pr 
									ON ors.prID = pr.prID
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(id) totalBut 
									 FROM tblors AS ors 
									 INNER JOIN tblpr AS pr 
									 ON ors.prID = pr.prID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo 
									 INNER JOIN tblemp_accounts emp 
									 ON emp.empID = pr.requestBy 
									 WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, 
				    					   ors.payee, ors.uacsObjectCode, ors.particulars, 
				    					   ors.amount, ors.prID, po.poStatus, ors.serialNo 
				    				FROM tblors AS ors 
									INNER JOIN tblpr AS pr 
									ON ors.prID = pr.prID
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo 
									INNER JOIN tblemp_accounts emp 
									ON emp.empID = pr.requestBy 
									WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
					} else if (isset($_SESSION['log_staff'])) {
						$countQry = "SELECT COUNT(id) totalBut 
									 FROM tblors AS ors 
									 INNER JOIN tblpr AS pr 
									 ON ors.prID = pr.prID 
									 INNER JOIN tblpo_jo po 
									 ON po.poNo = ors.poNo 
									 INNER JOIN tblemp_accounts emp 
									 ON emp.empID = pr.requestBy 
									 WHERE pr.requestBy = '" . $_SESSION['log_empID'] . "' ";
				    	$qryOBRs = "SELECT ors.id, ors.orsNo, ors.poNo, ors.address, 
				    					   ors.payee, ors.uacsObjectCode, ors.particulars, 
				    					   ors.amount, ors.prID, po.poStatus, ors.serialNo 
				    				FROM tblors AS ors 
									INNER JOIN tblpr AS pr 
									ON ors.prID = pr.prID
									INNER JOIN tblpo_jo po 
									ON po.poNo = ors.poNo 
									INNER JOIN tblemp_accounts emp 
									ON emp.empID = pr.requestBy 
				    				WHERE pr.requestBy = '" . $_SESSION['log_empID'] . "' ";
					}

					if (isset($_GET["po_no"])) {
						$searchInput = $_GET["po_no"];
						unset($_GET["po_no"]);
					}

					if (!empty($searchInput)) {
						echo '<label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
    									border-radius: 25px; 
    									margin-left: 3px;
    									margin-bottom: 2px;"
    						     href="obligation_request.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (ors.poNo LIKE '%$searchInput%' 
												  OR ors.poNo LIKE '%$searchInput%' 
												  OR ors.payee LIKE '%$searchInput%' 
												  OR ors.address LIKE '%$searchInput%' 
												  OR ors.particulars LIKE '%$searchInput%') 
												  ORDER BY ors.id DESC LIMIT $limit";
						$qryOBRs = $qryOBRs . " AND (ors.orsNo LIKE '%$searchInput%' 
												OR ors.poNo LIKE '%$searchInput%' 
												OR ors.payee LIKE '%$searchInput%' 
												OR ors.address LIKE '%$searchInput%' 
												OR ors.particulars LIKE '%$searchInput%') 
												ORDER BY ors.id DESC LIMIT $limit";
					} else {
						$qryOBRs = $qryOBRs . " ORDER BY ors.id DESC LIMIT $limit";
					}

					if ($resQry = $conn->query($qryOBRs)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
							echo '<table class="table table-hover" id="tblLists" align="center">';	
							/*<th width="9%">ORS No.</th>*/	
							echo '<tr>
									  <th width="3%">
									  </th>
									  <th align="left" style="padding-left: 10px;" width="10%">PO No.</th>
									  <th width="75%">Particulars</th>
									  <th width="10%">Amount</th>
									  <th></th>
									  <th></th>
									  <th></th>
								  </tr>';
							$ctr = $startlimit;

							while ($data = $resQry->fetch_object()) {
								$itemCount++;
								$ctr++;
								echo '<tr id=row_0>';
								echo '<td>'.$ctr.'</td>';
								/*
								echo '<td>';				
								echo $data->orsNo;			
								echo '</td>';*/	

								if (!empty($data->poNo)) {
									echo '<td>'.$data->poNo.' 
											<input type="hidden" value="pno=' . $data->poNo . 
														'&pid=' . $data->prID .
														'&status=' . $data->poStatus . 
														'&serialNo=' . $data->serialNo . 
														'" name="upCheck[]" id="input-' . $itemCount . '">
										  </td>';
								} else {
									echo '<td> -- Custom ORS -- </td>';
								}
								
								echo '<td id="particulars-'. $data->id .'" align="left">'.$data->particulars.'</td>';
								echo '<td>'.number_format($data->amount,2,'.',',').'</td>';

								if (empty($data->poNo)) {
									echo '<td><a data-toggle="tooltip" data-placement="left" title="Click to delete ORS for this Purchase/Job Order (PO/JO No: '. $data->poNo .')"  href="javascript: $(this).deleteDialog(\''.$data->id.'\');" 
											 title="Delete ORS"><img class="img-button" src="../../assets/images/delete.png" alt="delete"></a></td>';
								} else {
									echo '<td>
											<button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
											   <img class="img-button" src="../../assets/images/delete.png" alt="delete">
											</button>
										  </td>';
								} 

								echo '<td><a data-toggle="tooltip" data-placement="left" title="Click to print the ORS form for this Purchase/Job Order (PO/JO No: '. $data->poNo .')" href="javascript: $(this).printDialog(\''.$data->id.'\',\'ors\',\''.$data->orsNo.
																				  '\',\''.$data->poNo.'\',\''.$data->prID.'\',\'input-'.$itemCount.'\');" 
										     title="Print Preview"><img class="img-button" src="../../assets/images/print.png" alt="print"></a></td>';

								echo '<td>';

								if (!isset($_SESSION['log_staff'])) {
									if ($data->poStatus == "for_po") {
										echo '<a data-toggle="tooltip" data-placement="left" 
											     title="Click to Obligate this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
											     href="javascript: $(this).checkItem(\''. str_replace("'" , "", $data->poNo) .'\',\'input-' . 
											  					     $itemCount .'\',\'obligate\');"
											     title="Create DV"><img class="img-button" src="../../assets/images/approve.png" alt="obligate">
											  </a>';
									} else if ($data->poStatus == "obligated") {
										/*
										echo '<button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
											     <img class="img-button" src="../../assets/images/pis.gif" alt="delete">
											  </button>';*/
										echo '<a data-toggle="tooltip" data-placement="left" 
											     title="Click to approve for inspection for this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
											     href="javascript: $(this).saveIAR(\''. str_replace("'" , "", $data->poNo) .'\',\'' . 
											  					     $data->prID .'\',\'' . $data->id . '\');" 
											     title="Create IAR"><img class="img-button" src="../../assets/images/pis.gif" alt="create iar">
											  </a>';
									}  else if ($data->poStatus == "for_delivery") {
										echo '<a data-toggle="tooltip" data-placement="left" 
											     title="Click to approve for inspection for this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
											     href="javascript: $(this).saveIAR(\''. str_replace("'" , "", $data->poNo) .'\',\'' . 
											  					     $data->prID .'\',\'' . $data->id . '\');" 
											     title="Create IAR"><img class="img-button" src="../../assets/images/pis.gif" alt="create iar">
											  </a>';
									} else {
										echo '<a data-toggle="tooltip" data-placement="left" title="Click to create IAR for this Purchase/Job Order (PO/JO No: '. $data->poNo .')" 
											  	href="iar.php?&po_no=' . $data->poNo . '">
											  	<img class="img-button" src="../../assets/images/closed.gif" alt="Final">
											  </a>';
									}
								}
								
								echo '</td>';
								
							}

							while ($itemCount < $perPage) {
								echo "<tr id=row_0><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
								$itemCount++;
							}

							echo '</table><input type="hidden" value="final" name="hdAction"></form></div>';	
						} else {
							echo '<div align="center" style="color:#999999">
									  <br>----- No available record for ORS. -----<br><br>
								  </div>';
						}
					}
				?>    
				</td>
			</tr>

			<?php
				display_pages($conn, $countQry, 1, $accessPage, $perPage);
			?>
		</table>
	</div>

	<form id="frmSize" name="frmSize" method="post" action="../../../class_function/print_preview.php" target="_self">
		<input name="print" type="hidden" id="print">
		<input name="what" type="hidden" id="what">
		<input name="orsNo" type="hidden" id="orsNo"> 
		<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">
	</form>

<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/ors-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>