<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/functions.php");
include_once($dir . "class_function/class_dbop.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='purchase_job_order.php' style='color: #98ffe8;'>Purchase and Job Order</a>");

	$page = "po_job_order";
	$searchInput = "";
	$itemCount = 0;

	if (isset($_REQUEST['txtSearch'])) {
		$searchInput = trim($_REQUEST['txtSearch']);
		unset($_REQUEST['txtSearch']);
	}

	if (isset($_REQUEST['result'])) {
		$prResult = 'PO has been obligated.';
		unset($_REQUEST['result']);
	}

	if (isset($_POST['upCheck'])) {
		while (list(,$val) = each($_POST['upCheck'])) {
			parse_str($val);

			$payee = "";
			$address = "";
			$particulars = "...";

			$qrySuppliers = $conn->query("SELECT bidderID, company_name, address 
									   	  FROM tblbidders 
									   	  WHERE bidderID='" . $awarded . "'") 
								   or die(mysqli_error($conn));
			$qryPO = $conn->query("SELECT forApproval, totalAmount 
								   FROM tblpo_jo 
								   WHERE poNo='" . $pno . "'") 
							or die(mysqli_error($conn));
			$qryRequestedBy = $conn->query("SELECT empID, sectionID 
								   			FROM tblemp_accounts 
								   			WHERE empID='" . $requestedBy . "'") 
							  or die(mysqli_error($conn));

			if (mysqli_num_rows($qrySuppliers)) {
				$data = $qrySuppliers->fetch_object();
				$payee = $awarded;
				$address = $data->address;
			}

			if (mysqli_num_rows($qryPO)) {
				$data = $qryPO->fetch_object();
				$final = $data->forApproval;
				$tamount = $data->totalAmount;
			}

			if (mysqli_num_rows($qryRequestedBy)) {
				$data = $qryRequestedBy->fetch_object();
				$empID = $data->empID;
				$sectionID = $data->sectionID;
			}

			if ($final != 'y') {
				$prResult = "Cannot approve PO/JO No. " . $pno . 
							", check if the following information is supplied:" . 
							" PO Date, Amount in words and Signatory for funds " .
							"available and requisitioning department and be sure to obligate this item.";
				$searchInput = substr($pno, 0, -2);
			} else {
				if ($status == "obligated") {
					// Get and set PR Status
					$qryStatus = $conn->query("SELECT statusName 
											   FROM tblpr_status 
											   WHERE id = '8'") 
											   or die(mysqli_error($conn));
					$_prStatus = $qryStatus->fetch_object();
					$poStatus = $_prStatus->statusName;
					$poApprovalDate = date("m/d/Y");

					$conn->query("UPDATE tblpo_jo 
								  SET approved = 'yes', 
								  	  poApprovalDate = '" . $poApprovalDate . "', 
								  	  poStatus = '" . $poStatus . "'  
								  WHERE poNo='".$pno."'");
						
					if (mysqli_affected_rows($conn) != -1) {
						$prResult = 'PO has been approved for delivery.';
						$searchInput = substr($pno, 0, -2);
					} else {
						$prResult = "Error finalizing PO/JO.";
						$searchInput = substr($pno, 0, -2);
					}
				} else {
					$prResult = "You should obligate this PO/JO first.";
					$searchInput = substr($pno, 0, -2);
				}
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

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
		<table class="table" id="tblStyle">
			<tr>
			  	<th>
			  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
			  			<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#Approved Requests for PO and JO</label></strong>
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
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr prs
									 INNER JOIN tblemp_accounts emps 
									 ON prs.requestBy = emps.empID 
								     WHERE prStatus 
				    				 IN ('for_po','for_inspection', 'for_confirmation',
				    					 'for_obligation', 'for_disbursement', 'for_payment',
				    					 'for_inventory', 'recorded', 'issued') ";
					    $qryPOs = "SELECT requestBy, prID, prNo, purpose, prDate, concat(lastname,', ',firstname,' ',
						    					 left(middlename,1),'.') name, prStatus 
						    			  FROM tblpr prs 
						    			  INNER JOIN tblemp_accounts emps 
						    			  ON prs.requestBy = emps.empID 
						    			  WHERE prStatus 
					    				  IN ('for_po','for_inspection', 'for_confirmation',
					    					  'for_obligation', 'for_disbursement', 'for_payment',
					    					  'for_inventory', 'recorded', 'issued') ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr prs
									 INNER JOIN tblemp_accounts emps 
									 ON prs.requestBy = emps.empID 
								     WHERE prStatus 
				    				 IN ('for_po','for_inspection', 'for_confirmation',
				    					 'for_obligation', 'for_disbursement', 'for_payment',
				    					 'for_inventory', 'recorded', 'issued') 
								     AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
					    $qryPOs = "SELECT requestBy, prID, prNo, purpose, prDate, concat(lastname,', ',firstname,' ',
			    								 left(middlename,1),'.') name, prStatus 
						    			  FROM tblpr prs 
						    			  INNER JOIN tblemp_accounts emps 
						    			  ON prs.requestBy = emps.empID 
						    			  WHERE prStatus 
					    				  IN ('for_po','for_inspection', 'for_confirmation',
					    					  'for_obligation', 'for_disbursement', 'for_payment',
					    					  'for_inventory', 'recorded', 'issued') 
								    	  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
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
    						     href="purchase_job_order.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (prNo LIKE '%$searchInput%' 
												  OR prDate LIKE '%$searchInput%' 
												  OR purpose LIKE '%$searchInput%' 
												  OR emps.firstname LIKE '%$searchInput%'
												  OR emps.middlename LIKE '%$searchInput%'
												  OR emps.lastname LIKE '%$searchInput%') 
												  ORDER BY prID DESC LIMIT $limit";
						$qryPOs = $qryPOs . " AND (prNo LIKE '%$searchInput%' 
												 	 OR prDate LIKE '%$searchInput%' 
												 	 OR purpose LIKE '%$searchInput%' 
													 OR emps.firstname LIKE '%$searchInput%' 
													 OR emps.middlename LIKE '%$searchInput%' 
												 	 OR emps.lastname LIKE '%$searchInput%') 
												 	 ORDER BY prID DESC LIMIT $limit";
					} else {
						$qryPOs = $qryPOs . " ORDER BY prID DESC LIMIT $limit";
					}
					
					if ($resQry = $conn->query($qryPOs)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1">
							<form name="frmPRPost" method="post">
								<table class="table table-hover" id="tblLists">
									<tr>
										<th width="1%"></th>
										<th width="5%">&nbsp;</th>
										<th align="left" style="padding-left: 10px;" width="10%">prNo</th>
										<th width="10%">PR Date</th>
										<th width="74%">Purpose</th>
									</tr>';

							while ($data = $resQry->fetch_object()) {
								$ctr++;
								$itemCount++;

								echo '
									<tr id="row_0" style="background-color: #fff; color: #235e7a;">
										<td>
											<font color="#999999">'.$ctr.'</font>
										</td>
										<td>
											<img class="img-button" src="../../assets/images/closed.gif">
										</td>
										<td>
											<strong>'.$data->prNo.'</strong>
										</td>
										<td>' . 
											$data->prDate . '
										</td>
										<td align="left">' . 
											$data->purpose . '
										</td>
									</tr>';			
								
								$qryPOs = $conn->query("SELECT pd.poNo, pd.prID, pd.approved, pd.forApproval, bs.bidderID, bs.company_name, pd.awardedTo, pd.totalAmount, pd.poStatus 
														FROM tblpo_jo pd 
														INNER JOIN tblbidders bs 
														ON pd.awardedTo = bs.bidderID 
														WHERE prID='".$data->prID."' 
														ORDER BY LENGTH(poNo), poNo ASC");

								if (mysqli_num_rows($qryPOs)) {
									echo '
									<tr><td colspan="5" style="border-right: 9px #006699 solid; border-left: 9px #006699 solid;">
										<table style="border: 1px solid #5bc0de;" class="table table-hover" border=0 cellpadding=4 cellspacing=1 width="90%" align="center">
											<tr>
												<th width="5%">
													&nbsp;
												</th>
												<th width="15%">
													PO/JO No
												</th>
												<th width="75%">
													Awarded To
												</th>
												<th>
												</th>
												<th>
												</th>
												<th>
												</th>
											</tr>';
									
									while ($pos = $qryPOs->fetch_object()) {
										$_printType = array();
										$printType = "po";
										$qryUnitIssue = "SELECT unitIssue
														 FROM tblpr_info
														 WHERE prID = '" .$pos->prID. "'
														 AND awardedTo = '" . $pos->awardedTo . "'";

										$tempItemCount = mysqli_num_rows($qryPOs);

										if ($_qryUnitIssue = $conn->query($qryUnitIssue)) {
							      			while ($unitType = $_qryUnitIssue->fetch_object()) {
							      				if ($unitType->unitIssue == "J.O.") {
							      					$_printType[] = "jo";
							      				} else {
							      					$_printType[] = "po";
							      				}
							      			}
							      		}

							      		if (count(array_unique($_printType)) == 1) {
							      			if (in_array('po', $_printType, true)) {
							      				$printType = "po";
											}

											if (in_array('jo', $_printType, true)) {
												$printType = "jo";
											}
							      		} else if (count(array_unique($_printType)) > 1) {
							      			$printType = "po_jo";
							      		}

										$fCount++;
										echo '
											<tr id="row_0" onclick="servOC('.$fCount.',\'abstract_insp.php\',\'\')">';

										if ($pos->approved == 'yes') {
											echo '
												<td></td>';
										} else {
											echo '
												<td>
													<input type="checkbox" value="pno=' . $pos->poNo . 
														'&pid=' . $pos->prID .
														'&fine=' . $pos->forApproval . 
														'&awarded=' . $pos->awardedTo . 
														'&requestedBy=' . $data->requestBy . 
														'&status=' . $pos->poStatus . 
														'" name="upCheck[]" id="signa_'.$pos->bidderID.'">
												</td>'."\n";
										}

										echo '
												<td>'.
													$pos->poNo.'
												</td>
												<td 
													style="padding-left: 20px; text-align: left;" id="name'.$fCount.'">
													<img class="img-button" src="../../assets/images/down.png">
													<strong> '.$pos->company_name.'</strong>
												</td>' . "\n" . '
												<td>
													<a data-toggle="tooltip" data-placement="left" title="Click to print the Purchase/Job Order form (PO/JO No: '. $pos->poNo .')"  href="javascript: $(this).showPrintDialog(\''.$pos->poNo.'\',\''. $printType .
														'\',\''.$pos->prID.'\');" title="Print Preview">
														<img class="img-button" src="../../assets/images/print.png" alt="print">
													</a>
												</td>';
										
										echo '
												<td>
													<a data-toggle="tooltip" data-placement="left" title="Click to create and Obligate ORS for this Purchase/Job Order (PO/JO No: '. $pos->poNo .')" 
														href="obligation_request.php?po_no='. $pos->poNo .'">
														<img class="img-button" src="../../assets/images/create.png" alt="Create ORS">
													</a>
												</td>';

										if ($pos->approved == 'yes') {
											echo '
												<td>
													<a data-toggle="tooltip" data-placement="left" title="Click to create IAR for this Purchase/Job Order (PO/JO No: '. $pos->poNo .')" 
														href="obligation_request.php?po_no=' . $pos->poNo . '">
														<img class="img-button" src="../../assets/images/closed.gif" alt="Final">
													</a>
												</td>';
										} else {
											echo '
												<td>
													<a data-toggle="tooltip" data-placement="left" title="Click to approve this Purchase/Job Order (PO/JO No: '. $pos->poNo .')" 
														href="javascript: $(this).checkItem(\''. str_replace("'" , "", $pos->company_name) .'\',\'signa_'. 
														     $pos->bidderID.'\',\'approve\');">
														<img class="img-button" src="../../assets/images/approve.png" alt="Final">
													</a>
												</td>';
										}

										echo '
											</tr>
											<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'">
												<td colspan="6" class="pr-info">
													<iframe id="ihif'.$fCount.'" frameborder="0" width="100%" 
														src="abstract_insp.php?selected='.$data->prID.'&bid='.$pos->bidderID.
															'&po='.$pos->poNo.'">
													</iframe>
												</td>
											</tr>';
									}

									echo '
										</table>
									</td>
								</tr>';
								}
							}

							while ($itemCount < $perPage) {
								echo '<tr id="row_0"><td colspan="5"></td></tr>';
								$itemCount++;
							}

							echo '
								</table>
								<input type="hidden" value="final" name="hdAction">
								<input type="hidden" value="final" name="hdAction1">
							</form></div>';	
						}else{
							echo '
							<div align="center" style="color:#999999">
								<br>
								----- No available record for PO. -----
								<br>
								<br>
							</div>';
						}
					}

					?>    
				</td>
		  	</tr>
			<?php
				display_pages($conn,$countQry,1,$accessPage,$perPage);
			?>
		</table>
	</div>

	<form id="frmPrintPO" name="frmPrintPO" method="POST" action="../../../class_function/print_preview.php" target="_self">
		<input name="print" type="hidden" id="print">
	  	<input name="what" type="hidden" id="what"> 
	  	<input name="prID" type="hidden" id="prID">
	  	<input name="qtyItems" type="hidden" id="qtyItems">
	  	<input name="itemDesc" type="hidden" id="itemDesc"> 
	  	<input name="unitCost" type="hidden" id="unitCost"> 
	  	<input name="totalAmount" type="hidden" id="totalAmount">
	  	<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">
	</form>
	<form id="frmPrintJO" name="frmPrintJO" method="POST" action="../../../class_function/print_preview.php" target="_self">
		<input name="print" type="hidden" id="jo-print">
	  	<input name="what" type="hidden" id="jo-what"> 
	  	<input name="prID" type="hidden" id="jo-prID">
	  	<input name="workDesc" type="hidden" id="workDesc">
	  	<input name="font-scale" type="hidden" id="jo-font-scale">
		<input name="paper-size" type="hidden" id="jo-paper-size">
		<input name="amount-word" type="hidden" id="amount-word">
	</form>

<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/po-jo-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>