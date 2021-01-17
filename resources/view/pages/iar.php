<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='iar.php' style='color: #98ffe8;'>Inspection & Acceptance Report</a>");
	
	$page = "iar";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
	$searchInput = "";

	$withX = 0;

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_POST['txtSearch']);
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
			<div class="col-md-4" style="padding: 0px;"></div>
		</div>
	</div>

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
		<table class="table" id="tblStyle">
		  	<tr>
			  	<th>
			  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
			  			<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#For Inspection & Acceptance Report</label></strong>
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
									<input id="txtSearch" class='form-control' type="text" 
										   name="txtSearch" placeholder="Enter a keyword first...">
								</div>
				 	  		</form>
						</div>
					</div>
			  	</th>
			</tr>
			<tr>
			    <td>
				<?php
					if ($withX == 1) {
						echo '<div class="msg">'.$result.'<br><br>Verify that it has been accepted or it has been inspected with the appropriate details.</div>';
					}

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
					$ctr = 0;

				    if (isset($_SESSION['log_admin'])) {
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr AS prs 
									 INNER JOIN tblemp_accounts AS emps 
									 ON prs.requestBy = emps.empID 
									 WHERE prs.prStatus 
									 IN ('for_po', 'for_inspection', 'for_payment',
				    					 'for_inventory', 'recorded', 'issued') ";
				    	$qryPOs = "SELECT prID, prNo, prDate, purpose 
					    		   FROM tblpr AS prs
								   INNER JOIN tblemp_accounts AS emps 
								   ON prs.requestBy = emps.empID 
					    		   WHERE prs.prStatus 
								   IN ('for_po', 'for_inspection', 'for_payment',
				    				   'for_inventory', 'recorded', 'issued') ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr AS prs
									 INNER JOIN tblemp_accounts AS emps 
									 ON prs.requestBy = emps.empID 
									 WHERE prs.prStatus 
									 IN ('for_po', 'for_inspection', 'for_payment',
				    					 'for_inventory', 'recorded', 'issued') 
									 AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
				    	$qryPOs = "SELECT prID, prNo, prDate, purpose 
					    		   FROM tblpr AS prs
								   INNER JOIN tblemp_accounts AS emps 
								   ON prs.requestBy = emps.empID 
					    		   WHERE prs.prStatus 
								   IN ('for_po', 'for_inspection', 'for_payment',
				    				   'for_inventory', 'recorded', 'issued') 
					    		   AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
					} else if (isset($_SESSION['log_staff'])) {
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr AS prs
									 INNER JOIN tblemp_accounts AS emps 
									 ON prs.requestBy = emps.empID 
									 WHERE prs.prStatus 
									 IN ('for_po', 'for_inspection', 'for_payment',
				    					 'for_inventory', 'recorded', 'issued') 
									 AND prs.requestBy = '". $_SESSION['log_empID']  ."' ";
				    	$qryPOs = "SELECT prID, prNo, prDate, purpose 
					    		   FROM tblpr AS prs
								   INNER JOIN tblemp_accounts AS emps 
								   ON prs.requestBy = emps.empID 
					    		   WHERE prs.prStatus 
								   IN ('for_po', 'for_inspection', 'for_payment',
				    				   'for_inventory', 'recorded', 'issued') 
					    		   AND prs.requestBy = '". $_SESSION['log_empID']  ."' ";
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
				    	$searchInput = substr($searchInput, 0, 7);

						echo '<label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
    									border-radius: 25px; 
    									margin-left: 3px;
    									margin-bottom: 2px;"
    						     href="iar.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (prs.prNo LIKE '%$searchInput%' 
												  OR prs.prDate LIKE '%$searchInput%' 
												  OR prs.purpose LIKE '%$searchInput%' 
												  OR emps.firstname LIKE '%$searchInput%'
												  OR emps.middlename LIKE '%$searchInput%'
												  OR emps.lastname LIKE '%$searchInput%') 
												  ORDER BY prs.prID DESC LIMIT $limit";
						$qryPOs = $qryPOs . " AND (prs.prNo LIKE '%$searchInput%' 
											  OR prs.prDate LIKE '%$searchInput%' 
											  OR prs.purpose LIKE '%$searchInput%' 
											  OR emps.firstname LIKE '%$searchInput%'
											  OR emps.middlename LIKE '%$searchInput%'
											  OR emps.lastname LIKE '%$searchInput%') 
										      ORDER BY prs.prID DESC LIMIT $limit";
					} else {
						$qryPOs = $qryPOs . " ORDER BY prs.prID DESC LIMIT $limit";
					}
					
					if ($resQry = $conn->query($qryPOs)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
							echo '<table class="table table-hover table-responsive" id="tblLists">';		
							echo '<tr>
									  <th width="1%"></th>
									  <th width="5%">&nbsp;</th>
									  <th align="left" style="padding-left: 10px;" width="10%">prNo</th>
									  <th width="10%">PR Date</th>
									  <th width="74%">Purpose</th>
								  </tr>';
							$ctr = $startlimit;

							while ($data = $resQry->fetch_object()) {
								$ctr++;
								$itemCount++;
								echo '<tr id="row_0" style="background-color: #fff; color: #235e7a;">';
								echo '<td>'.$ctr.'</td>';
								echo '<td>';				
								echo '<img class="img-button" src="../../assets/images/closed.gif" />';			
								echo '</td>';	
								echo '<td><strong>'.$data->prNo.'</strong></td>';
								echo '<td>'.$data->prDate.'</td>';
								echo '<td align="left">'.$data->purpose . '</td>';
								echo '</tr>';

								$qryIARs = $conn->query("SELECT iar.id, iar.toForm, iar.iarNo, iar.prID, 
																iar.orsID, po.poStatus, po.poNo 
													     FROM tbliar AS iar 
													     INNER JOIN tblors AS ors 
													     ON ors.id = iar.orsID 
													     INNER JOIN tblpo_jo AS po 
													     ON po.poNo = ors.poNo 
													     WHERE iar.prID = '" . $data->prID . "' 
													     ORDER BY iar.id DESC");
								
								if (mysqli_num_rows($qryIARs)) {
									echo '<tr>
											  <td colspan="5" style="border-right: 9px #006699 solid; border-left: 9px #006699 solid;">';
									echo '<table style="border: 1px solid #5bc0de;" class="table table-hover table-responsive">';
									echo '<tr>
											  <th width="5%">&nbsp;</th>
											  <th width="15%">PO/JO No</th>
											  <th width="75%">Awarded To</th>
											  <th></th>
											  <th></th>
											  <th></th>
										  </tr>';
									
									while ($pos = $qryIARs->fetch_object()) {
										$poNo = "";
										$companyName = "";
										$bidderID = "";

										$_qryPO =  $conn->query("SELECT ors.poNo, bs.bidderID, bs.company_name 
															     FROM tblors AS ors 
															     INNER JOIN tblbidders AS bs 
															     ON ors.payee = bs.bidderID 
															     WHERE ors.id = '" . $pos->orsID . "'");

										if (mysqli_num_rows($_qryPO)) {
											$_data = $_qryPO->fetch_object();
											$poNo = $_data->poNo;
											$awardedTo = $_data->company_name;
											$bidderID = $_data->bidderID;
										}

										if ($pos->poStatus != "pending" || $pos->poStatus != "approved" || 
											$pos->poStatus != "for_posting" || $pos->poStatus != "disapproved" || 
											$pos->poStatus != "cancelled" || $pos->poStatus != "for_canvass" ||
										    $pos->poStatus == "for_payment" || $pos->poStatus == "") {
											$fCount++;
											echo '<tr id="row_0" onclick="servOC('.$fCount.',\'abstract_insp.php\',\'\')">';
											echo '<td>
													   <input type="checkbox" 
													   		  value="iid='.$pos->id.'&pid='.$pos->prID.'&pn='.$poNo.
													   		  	    '&supplier='.$bidderID.'&fine='.$pos->toForm.
													   		  	    '" name="upCheck[]" id="signa_'.$bidderID.'">
												   </td>'."\n";
											echo '<td>'.$poNo.'</td>
												  <td style="padding-left: 20px; text-align: left;" 
												  	  id="name'.$fCount.'">
												  	  <img class="img-button" src="../../assets/images/down.png">
												  	  <strong> '.$awardedTo.'</strong>
												  </td>'."\n";
											
											echo '<td>
												      <a data-toggle="tooltip" data-placement="left" 
												      	 title="Click to print IAR for this Purchase/Job Order (PO/JO No: '. $poNo .')" 
												      	 href="javascript: $(this).showPrintDialog(\''.$poNo.'\',\'iar\',\''.$pos->prID.'\',\''.$pos->orsID.'\');" 
												      	 title="Print Preview"><img class="img-button" src="../../assets/images/print.png" 
												      	  alt="print">
												      </a>
												  </td>';

											echo '<td>';
											if (!isset($_SESSION['log_staff'])) {
												echo '<a data-toggle="tooltip" data-placement="left" 
													 	 title="Click to approve for DV (PO/JO No: '. $poNo .')" 
													 	 href="javascript: $(this).showFinalizeDialog(\''.$poNo.'\',\'signa_'.$bidderID.
													 	 '\',\'finalized\');" title="Finalized">
													 	   <img class="img-button" src="../../assets/images/create.png" alt="Final">
													 </a>';
											}
											echo '</td>';
											echo '<td>';

											if (!isset($_SESSION['log_staff'])) {
												if ($pos->poStatus == "for_inspection") {
													echo '<a data-toggle="tooltip" data-placement="left" 
														  	 title="Click to finalize items for inventory (PO/JO No: '. $poNo .')" 
														  	 href="javascript: $(this).saveDV(\''.$pos->prID.'\',\''.$pos->orsID.'\',\''.$pos->poNo.'\');" 
														  	 title="Finalized">
														  	   <img class="img-button" src="../../assets/images/approve.png" alt="Final">
														  </a>';
												} else {
													echo '<a data-toggle="tooltip" data-placement="left" title="Click to create DV (PO/JO No: '. $poNo .')" 
														 	  href="dv.php?po_no='. $pos->poNo .'" title="Finalized">
														 	   <img class="img-button" src="../../assets/images/closed.gif" alt="Final">
														  </a>';
												}
											}

											echo '</td>';
											
											echo '</tr>';
											echo '<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'">
													  <td colspan="6" class="pr-info">
													  	   <iframe id="ihif'.$fCount.'" frameborder="0" width="100%" 
													  	   	       src="abstract_insp.php?selected='.$pos->prID.'&bid='.
													  	   	       $bidderID.'">
													  	   </iframe>
													  </td>
												  </tr>';
										} else {
											echo '<tr id="row_0">
												<td colspan="5">
													----- No available data. ----- 
												</td>
											</tr>';
										}
									}

									echo '</table>';
									echo '</td></tr>';
								} else {
									echo '<tr style="background: #005e7c;">
											<td style="border-right: 9px #006699 solid; border-left: 9px #006699 solid;" colspan="5">
											<br><strong class="font-color-2"> ----- No available data. ' . 
											'----- </strong><br><br></td>
										  </tr>';
								}
							}

							while ($itemCount < $perPage) {
								echo "<tr id='row_0'><td colspan='5'></td></tr>";
								$itemCount++;
							}

							echo '</table><input type="hidden" value="final" name="hdAction"></form></div>';	
						} else {
							echo '
							<div align="center" style="color:#999999">
								<br>
								----- No available record for IAR. -----
								<br>
								<br>
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

 	<form id="frmSize" name="frmSize" method="post" action="../../../class_function/print_preview.php" 
 		  target="_self">
		<input name="print" type="hidden" id="print">
		<input name="what" type="hidden" id="what"> 
		<input name="poNo" type="hidden" id="poNo"> 
	</form>
       
<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/iar-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>