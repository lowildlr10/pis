<?php

include_once("session.php");
include_once("../layout/main_layout.php");	
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='purchase.php' style='color: rgb(225, 239, 243);'>Purchase Request</a>/".
		         "<a href='purchase_made.php' style='color: #98ffe8;'>Approved</a>");	
	
	$page = "pr";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
	$searchBy = "";
	$searchInput = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="purchase.php">&lt;&lt;Back</a>
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

			<!--
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
			!-->
		</div>
		<div class="col-md-3" style="padding: 0px;"></div>
		<div class="col-md-6" style="padding: 0px;"></div>
	</div>
</div>

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
			  		<div class="col-md-3 col-xs-12" style="padding: 0px;"> 
						<strong><label>#Purchase Requests Made</label></strong>
					</div>
					<div class="col-md-6 col-xs-12" style="padding: 0px;">
						&nbsp
					</div>
					<div class="col-md-3 col-xs-12" style="padding: 0px;">
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

					$perPage = $_SESSION['showPerPage'];

					if (isset($_POST['txtPerPage'])) {
						$_SESSION['showPerPage'] = $_POST['txtPerPage'];
						unset($_REQUEST['limit']);
					}

					if (isset($_REQUEST['limit'])) {
						$accessPage = $_REQUEST['limit'];
						$startlimit = $accessPage * $perPage - $perPage;
						$limit = $startlimit.",".$perPage;	

					} else {
						$limit = "0,".$perPage;
						$accessPage = 1;			
					}

					$_SESSION['curPage'] = $accessPage;	

					if (isset($_SESSION['log_admin'])) {
						if ($show == 'all') {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
									     WHERE prStatus <> 'pending' 
									     AND prStatus <> 'for_posting'";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE prStatus <> 'pending' 
											  AND prStatus <> 'for_posting'";
						} else {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
									     WHERE prStatus = '".$show."'";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE prStatus = '".$show."'";
						}
					} else if (isset($_SESSION['log_pstd'])) {
						if ($show == 'all') {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
									     WHERE prStatus <> 'pending' 
									     AND prStatus <> 'for_posting' 
									     AND emps.sectionID = '". $_SESSION['log_sectionID']  ."'";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE prStatus <> 'pending' 
											  AND prStatus <> 'for_posting' 
											  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
						} else {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
									     WHERE prStatus = '".$show."'
									     AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE prStatus = '".$show."' 
											  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
						}
					} else {
						if ($show == 'all') {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
										 WHERE requestBy = '".$_SESSION['log_empID']."' 
										 AND prStatus <> 'pending' 
										 AND prStatus <> 'for_posting'";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE requestBy = '".$_SESSION['log_empID']."' 
											  AND prStatus <> 'pending' 
											  AND prStatus <> 'for_posting'";
						} else {
							$countQry = "SELECT COUNT(prID) totalBut 
									     FROM tblpr prs
									     INNER JOIN tblemp_accounts emps 
									     ON prs.requestBy = emps.empID 
										 WHERE requestBy = '".$_SESSION['log_empID']."' 
										 AND prStatus = '".$show."'";
							$qryForPosting = "SELECT prID, prNo, purpose, prDate, prStatus, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name 
											  FROM tblpr prs INNER JOIN tblemp_accounts emps 
											  ON prs.requestBy = emps.empID 
											  WHERE requestBy = '".$_SESSION['log_empID']."' AND prStatus = '".$show."'";
						}
					}

					if (!empty($searchInput)) {
						echo '<label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
    									border-radius: 25px; 
    									margin-left: 3px;
    									margin-bottom: 2px;"
    						     href="purchase_made.php">
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
							$ctr = $startlimit;
							echo '<div class="table-container-1"><form method="post" name="frmPRs" action="">';
							echo '<table class="table table-hover table-responsive" cellpadding="4" cellspacing="0" id="tblLists" align="center" width="97%" align="center">';
							echo '<tr>
									  <th>
									  	  <input type="hidden" value="'.$show.'" name="selFilter">
									  	  <input type="hidden" name="operation">
									  </th>
									  <th width="1%">
									  	  &nbsp;
									  </th>
									  <th align="left" style="padding-left: 10px;" width="10%">
									  	  prNo
									  </th>
									  <th width="10%">
									  	  PR Date
									  </th>
									  <th width="40%">
									      Purpose
									   </th>
									   <th width="15%">
									      Requested By
									   </th>
									   <th width="11%">
									   	  Status
									   </th>
									   <th >
								  	  	  
								  	  </th>
									   <th >
									      
									   </th>
									   <th >
									      
									   </th>
								 </tr>';

								 /*
								 <th width="5%">
									      Copy
									   </th>
								 */

							while ($data = $resQry->fetch_object()) {
								$itemCount++;
								$fCount++;
								$ctr++;
								echo '<tr id=row_0 onclick="servOC('.$fCount.',\'pr_info.php\',\'\')">';
								echo '<td>'.$ctr.'</td>';
								echo '<td>';

								switch ($data->prStatus) {
									case 'pending':
										echo '<input type="checkbox" name="itemCheck[]" value="pid='.$data->prID.'&pr='.$data->prNo.'" id="pr_'.$data->prID.'" />';
										break;
									case 'approved':
										echo '<img class="img-button" src="../../assets/images/approved.gif" />';
										break;
									case 'disapproved':
										echo '<img class="img-button" src="../../assets/images/disap.gif" />';
										break;
									case 'cancelled':
										echo '<img class="img-button" src="../../assets/images/cancel.gif" />';	
										break;
									default:
										echo '<img class="img-button" src="../../assets/images/approved.gif" />';
										break;
								}

								echo '</td>';	
								echo '<td>';

								if (!empty($data->prNo)) {
									echo $data->prNo;
								} else {
									echo '<a href="purchase_op.php?loc=purchase_made&stat='.$data->prStatus.'&edit='.$data->prID.'">
										  	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit">
										  </a>';
								}

								echo '</td>';
								echo '<td>'.$data->prDate.'</td>';
								echo '<td align="left" style="padding-left: 20px;" id="name'.$fCount.'">
									  	  <img class="img-button" src="../../assets/images/down.png"> '. $data->purpose.
									 '</td>';
								echo '<td style="border-left: 1px #c1bdbd solid; border-right: 1px #c1bdbd solid;">'.$data->name.'</td>';
								echo '<td style="text-align: left; border-right: 1px #c1bdbd solid;">';

								$qryPO = $conn->query("SELECT poStatus, poNo 
													   FROM tblpo_jo 
													   WHERE prID = '" . $data->prID . "' 
													   ORDER BY LENGTH(poNo), poNo ASC")
												or die($mysqli_error($conn));
								$prStatus = "";

								if (mysqli_num_rows($qryPO)) {
									while ($poData = $qryPO->fetch_object()) {
										switch ($poData->poStatus) {
											case 'pending':
												$prStatus = 'For Approval';
												break;
											case 'disapproved':
												$prStatus = 'Disapproved';
												break;
											case 'cancelled':
												$prStatus = 'Cancelled';	
												break;
											case 'closed':
												$prStatus = 'Closed';	
												break;
											case 'for_canvass':
												$prStatus = 'APPROVED FOR CANVASS';	
												break;
											case 'for_po':
												$prStatus = 'APPROVED FOR PO';	
												break;
											case 'obligated':
												$prStatus = 'OBLIGATED';	
												break;
											case 'for_delivery':
												$prStatus = 'CONFIRMED FOR DELIVERY';	
												break;
											case 'for_inspection':
												$prStatus = 'FOR INSPECTION';	
												break;
											case 'for_disbursement':
												$prStatus = 'INSPECTED';	
												break;
											case 'for_payment':
												$prStatus = 'FOR PAYMENT';	
												break;
											case 'for_inventory':
												$prStatus = 'FOR INVENTORY';	
												break;
											case 'recorded':
												$prStatus = 'RECORDED';	
												break;
											case 'issued':
												$prStatus = 'ISSUED';	
												break;
											// From old status
											case 'approved':
												$prStatus = 'APPROVED FOR CANVASS';	
												break;
											default:
												$prStatus = "APPROVED FOR PO";
												break;
										}

										echo '<strong>PO No: ' . $poData->poNo . ' </strong><br>';
										echo '(' . $prStatus . ')<br>';
									}
								} else {
									$prStatus = $data->prStatus;

									switch ($data->prStatus) {
										case 'pending':
											$prStatus = 'For Approval';
											break;
										case 'disapproved':
											$prStatus = 'Disapproved';
											break;
										case 'cancelled':
											$prStatus = 'Cancelled';	
											break;
										case 'closed':
											$prStatus = 'Closed';	
											break;
										case 'for_canvass':
											$prStatus = 'APPROVED FOR CANVASS';	
											break;
										case 'for_po':
											$prStatus = 'APPROVED FOR PO';	
											break;
										case 'for_inspection':
											$prStatus = 'FOR INSPECTION';	
											break;
										case 'for_confirmation':
											$prStatus = 'APPROVED FOR CONFIRMATION';	
											break;
										case 'for_obligation':
											$prStatus = 'CONFIRMATION FOR OBLIGATION';	
											break;
										case 'for_disbursement':
											$prStatus = 'FOR DISBURSEMENT';	
											break;
										case 'for_payment':
											$prStatus = 'FOR PAYMENT';	
											break;
										case 'for_inventory':
											$prStatus = 'FOR INVENTORY';	
											break;
										case 'recorded':
											$prStatus = 'RECORDED';	
											break;
										case 'issued':
											$prStatus = 'ISSUED';	
											break;
										// From old status
										case 'approved':
											$prStatus = 'APPROVED FOR CANVASS';	
											break;
										default:
											echo '';
											break;
									}

									echo $prStatus;
								}

								echo '</td>';
								echo '<td>
									    <a data-toggle="tooltip" data-placement="left" title="Click to edit this Purchase Request (PR No: '. $data->prNo .')" href="purchase_op.php?loc=purchase_made&edit='.$data->prID.'">
									    	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit" />
									    </a>
									  </td>';
								
								echo '<td>
										  <a data-toggle="tooltip" data-placement="left" title="Click to print this Purchase Request (PR No: '. $data->prNo .')" href="javascript: $(this).showPrintDialog(\''.$data->prID.'\',\'pr\');" title="Print Preview">
										  	  <img class="img-button" src="../../assets/images/print.png" alt="print">
										  </a>
									  </td>';
								echo '
									  <td>
									  	  <a data-toggle="tooltip" data-placement="left" title="Click to create canvass for this Purchase Request (PR No: '. $data->prNo .')" href="canvass.php?po_no='. $data->prNo .'">
									  	  	   <img class="img-button" src="../../assets/images/create.png" alt="Create Canvass">
									  	  </a>
									  </td>';
								echo '</tr>';
								echo '<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'">
									  	  <td colspan="10" class="pr-info">
									  	  	   <iframe id="ihif'.$fCount.'" frameborder="0" width="100%" src="pr_info.php?selected='.$data->prID.'"></iframe>
									  	  </td>
									  </tr>';
							}

							while ($itemCount < $perPage) {
								echo "<tr id=row_0><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
								$itemCount++;
							}
							
							echo '</table><input type="hidden" name="hdPRcopy" value="" /></form></div>';	
						} else {
							echo '<div align="center" style="color:#999999"><br />----- No available record. -----<br /><br /></div>';
						}
					}
					
				?>
			</td>
	  	</tr>
	  	<tr>
	    	<td>&nbsp;</td>
	  	</tr>

		<?php
	 		display_pages($conn, $countQry, 9, $accessPage, $perPage, "&selFilter=".$show."");
		?> 
	</table>
</div>

<?php
	include_once("modal/print-preview-modal.php");
	end_layout($page);
} else {
	header("Location: /pis/index.php");
}
?>