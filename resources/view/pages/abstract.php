<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

$result = "";
$startlimit = 0;
$fCount = 0;
$itemCount = 0;

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
	start_layout("DOST-CAR Procurement System", 
				 "<a href='abstract.php' style='color: #98ffe8;'>Abstract of Bids and Quotations</a>");

	$page = "abstract";
	$searchBy = "";
	$searchInput = "";
	$_prNo = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	//========================================================================================================

	if (isset($_POST['finCheck'])) {
		foreach ($_POST['finCheck'] as $count => $final) {
			parse_str($final);

			$prID = $pid;
			
			if (!empty($prNo)) {
				$qryWinners = $conn->query("SELECT DISTINCT awardedTo 
											FROM tblpr_info 
											WHERE prID='". $prID ."' 
											AND awardedTo IS NOT NULL 
											AND awardedTo <> 0");
				$winCnt = mysqli_num_rows($qryWinners);
				$placeDelivery = "DOST-CAR";
				$deliveryDate = "Within 15 days of receipt of this purchase order.";
				$deliveryTerm = "Complete";
				$paymentTerm = "After Inspection and Acceptance";
				$forApproval = 'n';
				$ok = false;
				$cntWin = 0;
				$arrPOS = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
								'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

				if ($winCnt >= 1) {
					while ($win = $qryWinners->fetch_object()) {												
						$poNo = $prNo."-".$arrPOS[$cntWin];
						$awardedTo = $win->awardedTo;

						$conn->query("DELETE FROM tblpo_jo 
							  		  WHERE poNo='".$poNo."' AND prID='". $prID."'");	
						
						if (!empty($awardedTo)) {
							$conn->query("INSERT IGNORE INTO tblpo_jo (poNo, prID, awardedTo, 
										  		  			 		   placeDelivery, deliveryDate, 
										  		  			 		   deliveryTerm, paymentTerm, forApproval) 
										  VALUES ('" . $poNo . "', '" . $prID . "', '" . $awardedTo . "', 
										  		  '" . $placeDelivery . "', '" . $deliveryDate . "', 
										  		  '" . $deliveryTerm . "', '" . $paymentTerm . "', '" . $forApproval . "')")
										  or die(mysqli_error($conn));
							$ok = true;
						}

						$cntWin++;
					}
				}

				if ($ok) {
					// Get and set PR Status
					$qryStatus = $conn->query("SELECT statusName 
											   FROM tblpr_status 
											   WHERE id = '6'") 
											   or die(mysqli_error($conn));
					$_prStatus = $qryStatus->fetch_object();
					$prStatus = $_prStatus->statusName;
					$abstractApprovalDate = date("m/d/Y");

					$conn->query("UPDATE tblpr 
								  SET prStatus = '" . $prStatus . "',
								  	  abstractApprovalDate = '" . $abstractApprovalDate . "' 
								  WHERE prID='".$prID."'");
				} else {
					$result.="- ".$prNo." not yet awarded.<br>";
				}
			} else {
				echo "No PR Number. <br>";
			}
		}

		unset($_POST['finCheck']);
	}

	//========================================================================================================
?>

	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="access.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-7" style="padding: 0px;"></div>
			<div class="col-md-2" style="padding: 0px;">
				<div class="btn-group btn-group-justified">
					<a class="btn btn-primary operation" href="javascript: void(0);" 
						onclick="$(this).ifCheck();">	Approve Selected
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php
		if ($result) {
			echo '<div class="msg">Can\'t finalized: <br />'.$result.'</div>';
		}
	?>
	
	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
		<table class="table table-hover table-responsive" id="tblStyle">
			<tr>
			  	<th>
		 	  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
				  		<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#Approved Requests</label></strong>
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

				if (!isset($_SESSION['showPerPage'])) {
					$_SESSION['showPerPage'] = 30;
				}

				if (isset($_POST['txtPerPage'])) {
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
				if (isset($_SESSION['log_admin'])) {
					$countQry = "SELECT COUNT(prID) totalBut 
								 FROM tblpr prs
								 INNER JOIN tblemp_accounts emps 
								 ON prs.requestBy = emps.empID 
							     WHERE prStatus <> 'pending' 
								 AND prStatus <> 'for_posting'";
				    $qryForPosting = "SELECT prID, prNo, purpose, prDate, abstractDate, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, prStatus 
				    				  FROM tblpr prs INNER JOIN tblemp_accounts emps 
				    				  ON prs.requestBy = emps.empID 
				    				  WHERE prStatus <> 'pending' 
								 	  AND prStatus <> 'for_posting'";
				} else if (isset($_SESSION['log_pstd'])) {
					$countQry = "SELECT COUNT(prID) totalBut 
								 FROM tblpr prs
								 INNER JOIN tblemp_accounts emps 
								 ON prs.requestBy = emps.empID 
							     WHERE prStatus <> 'pending' 
								 AND prStatus <> 'for_posting' 
							     AND emps.sectionID = '". $_SESSION['log_sectionID']  ."'";
				    $qryForPosting = "SELECT prID, prNo, purpose, prDate, abstractDate, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, prStatus 
				    				  FROM tblpr prs INNER JOIN tblemp_accounts emps 
				    				  ON prs.requestBy = emps.empID 
				    				  WHERE prStatus <> 'pending' 
								 	  AND prStatus <> 'for_posting' 
				    				  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."'";
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
    						     href="abstract.php">
    						     Clear
    						   </a><br><br>';

					$countQry = $countQry . " AND (prNo LIKE '%$searchInput%' 
											  OR prDate LIKE '%$searchInput%' 
											  OR purpose LIKE '%$searchInput%' 
											  OR emps.firstname LIKE '%$searchInput%'
											  OR emps.middlename LIKE '%$searchInput%'
											  OR emps.lastname LIKE '%$searchInput%') 
											  ORDER BY prID DESC LIMIT $limit";
					$qryForPosting = $qryForPosting . " AND (prNo LIKE '%$searchInput%' 
											 		    OR prDate LIKE '%$searchInput%' 
											 		    OR purpose LIKE '%$searchInput%' 
													    OR emps.firstname LIKE '%$searchInput%'
													    OR emps.middlename LIKE '%$searchInput%'
											 		    OR emps.lastname LIKE '%$searchInput%') 
											 		    ORDER BY prID DESC LIMIT $limit";
				} else {
					$qryForPosting = $qryForPosting . " ORDER BY prID DESC LIMIT $limit";
				}
				
				if ($resQry = $conn->query($qryForPosting)) {
					if (mysqli_num_rows($resQry)) {
						$fCount = $startlimit;
						echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
						echo '<table class="table table-hover table-responsive" id="tblLists">';		
						echo '<tr>
								  <th></th>
								  <th width="5%">Check for Approve</th>
								  <th align="left" style="padding-left: 10px;" width="10%">
								  	  PR No
								  </th>
								  <th width="10%">
								  	  PR Date
								  </th>
								  <th width="60%">
								  	  Purpose
								  </th>
								  <th>
								  </th>
								  <th>
								  </th>
								  <th>
								  </th>
								  <th>
								  </th>
							 </tr>';
						
						while ($data = $resQry->fetch_object()) {
							$fCount++;
							$itemCount++;
							$_prNo = $data->prNo;

							echo '<tr id="row_0" onclick="servOC('.$fCount.',\'pr_info.php\',\'\')">';
							echo '<td>'.$fCount.'</td>';
							echo '<td>';
							
							if ($data->prStatus != 'for_canvass' && $data->prStatus != 'approved') {
								echo '<div style="padding: 7px 4px 7px 4px;">';			
								echo '<img style="margin: 3px;" class="img-button" src="../../assets/images/closed.gif"><br>';
								echo '</div>';
							} else {
								echo '<input data-toggle="tooltip" data-placement="right" title="Check then click APPROVE SELECTED button to approve this purchase request for PO/JO (PR No: '. $data->prNo .')" type="checkbox" name="finCheck[]" 
											 value="pid='.$data->prID.'&prNo='.$data->prNo.'" id="pr_'.$data->prID.'">';
							}

							echo '</td>';	
							echo '<td>';

							if (!empty($data->prNo)) {
								echo $data->prNo;
							} else {
								echo '<a data-toggle="tooltip" data-placement="left" title="Click to edit the abstract for this Purchase Request (PR No: '. $data->prNo .')" 
										  href="purchase_op.php?loc=abstract&stat=approved&edit='.$data->prID.'">
									  	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit">
									  </a>';
							}

							echo '</td>';
							echo '<td>'.$data->prDate.'</td>';
							echo '<td align="left" 
									  style="padding-left: 20px;" id="name'.$fCount.'">
									  <img class="img-button" src="../../assets/images/down.png"> '.
									  $data->purpose.
								 '</td>';
							
							if ($data->prStatus != 'for_canvass' && $data->prStatus != 'approved') {
								echo '<td>
									    <a data-toggle="tooltip" data-placement="left" title="Click to edit the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showAbstractDialog(\'' . $data->prID. 
												'\',\'abstract\',\'' . $data->prNo . '\',\'' . "edit" . '\');" title="Edit">
									    	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit">
									    </a>
									  </td>';
								//echo '<td>--</td>';
								echo '<td>
										  	  <a data-toggle="tooltip" data-placement="left" title="Click to delete the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showDeleteDialog(\''.$data->prID.'\',\'abstract\',\''.
										  	  	  $data->abstractDate.'\');" title="Delete Abstract">
										  	  	  <img class="img-button" src="../../assets/images/delete.png" alt="print">
										  	  </a>
									  	  </td>';
								echo '<td>
									  	  <a data-toggle="tooltip" data-placement="left" title="Click to print the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showPrintDialog(\''.base64_encode($data->prID).'\',\'abstract\',\''.
									  	  	  $data->abstractDate.'\',\''.$data->prNo.'\');" title="Print Preview">
									  	  	  <img class="img-button" src="../../assets/images/print.png" alt="print">
									  	  </a>
									  </td>';
								echo '
									  <td>
									  	  <a data-toggle="tooltip" data-placement="left" title="Click to create the PO/JO for this Purchase Request (PR No: '. $data->prNo .')" href="purchase_job_order.php?po_no='. $data->prNo .'" 
									  		  title="Create PO">
									  		  <img class="img-button" src="../../assets/images/create.png" alt="Create PO">
									  	  </a>
									  </td>';
							} else {
								$bidderCount = 0;
								$bidResult = $conn->query('SELECT * FROM tblbids_quotations 
											   			   WHERE prID = "' . $data->prID . '"');
								$bidderCount = mysqli_num_rows($bidResult);

								if ($bidderCount > 0) {
									echo '<td>
										    <a data-toggle="tooltip" data-placement="left" title="Click to edit the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showAbstractDialog(\'' . $data->prID. 
													'\',\'abstract\',\'' . $data->prNo . '\',\'' . "edit" . '\');" title="Edit">
										    	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit">
										    </a>
										  </td>';
									echo '<td>
										  	  <a data-toggle="tooltip" data-placement="left" title="Click to delete the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showDeleteDialog(\''.$data->prID.'\',\'abstract\',\''.
										  	  	  base64_encode($data->abstractDate).'\');" title="Delete Abstract">
										  	  	  <img class="img-button" src="../../assets/images/delete.png" alt="print">
										  	  </a>
									  	  </td>';
									echo '<td>
										  	  <a data-toggle="tooltip" data-placement="left" title="Click to print the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showPrintDialog(\''.base64_encode($data->prID).
										  	  	  '\',\'abstract\',\''.base64_encode($data->abstractDate).'\',\''.$data->prNo.'\');" title="Print Preview">
										  	  	  <img class="img-button" src="../../assets/images/print.png" alt="print">
										  	  </a>
									  	  </td>';
									echo '<td>
											  <button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
											     <img class="img-button" src="../../assets/images/create.png" alt="delete">
											  </button>
										  </td>';
								} else {
									echo '
										<td>
											<a data-toggle="tooltip" data-placement="left" title="Click to create the abstract for this Purchase Request (PR No: '. $data->prNo .')" href="javascript:void(0);" onclick="$(this).showAbstractDialog(\'' . $data->prID. 
												'\',\'abstract\',\'' . $data->prNo . '\',\'' . "create" . '\');" 
												title="Make Abstract">
												<img class="img-button" src="../../assets/images/create.png" alt="abstract">
											</a>
										</td>';
									echo '<td>
											  <button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
											     <img class="img-button" src="../../assets/images/delete.png" alt="delete">
											  </button>
										  </td>';
									echo '<td>
										  	  <button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
										         <img class="img-button" src="../../assets/images/print.png" alt="delete">
										      </button>
										  </td>';
									echo '<td>
										      <button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
											     <img class="img-button" src="../../assets/images/create.png" alt="delete">
											  </button>
										  </td>';
								}			
							}

							echo '</tr>';
							echo '<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'">
								     <td colspan="9" class="pr-info">
								      	 <iframe id="ihif'.$fCount.'" frameborder="0" width="100%" src="abstract_info.php?selected='.$data->prID.'&pr_no='.$data->prNo.'"></iframe>
								     </td>
								  </tr>';
						}

						while ($itemCount < $perPage) {
							echo "<tr id=row_0><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
							$itemCount++;
						}

						echo '</table></form></div>';	
					} else {
						echo '<div align="center" style="color:#999999"><br>----- No available record to be abstract. -----<br><br></div>';
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
		<input name="fdate" type="hidden" id="fdate">
		<input name="what" type="hidden" id="what">
		<input name="qtn" type="hidden" id="qtn"> 
		<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">

		<input name="inp-chairman" type="hidden" id="inp-chairman">
		<input name="inp-vice" type="hidden" id="inp-vice">
		<input name="inp-member1" type="hidden" id="inp-member1">
		<input name="inp-member2" type="hidden" id="inp-member2">
        <input name="inp-member3" type="hidden" id="inp-member3">
		<input name="inp-enduser" type="hidden" id="inp-enduser">
	</form>

<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/abstract-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>