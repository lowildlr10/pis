<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) || isset($_SESSION['log_staff'])) {
	start_layout("DOST-CAR Procurement System",
		 		 "<a href='canvass.php' style='color: #98ffe8;'>Request for Bids and Quotation</a>");	
	
	$prNo = "";
	$startlimit = 0;
	$limit = 0;
	$fCount = 0;
	$itemCount = 0;
	$perPage = 30;
	$data = array();
	$searchBy = "";
	$searchInput = "";
	$page = "canvass";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
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

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
		<table class="table" id="tblStyle">
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
					#====================================================
					// Set the number of item/s to be displayed on table

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

					#====================================================
					// Get canvas data

					if (isset($_SESSION['log_admin'])) {
						$countQry = "SELECT COUNT(prID) totalBut 
									 FROM tblpr prs
									 INNER JOIN tblemp_accounts emps 
									 ON prs.requestBy = emps.empID 
								 	 WHERE prStatus <> 'pending' 
								 	 AND prStatus <> 'for_posting'";
						$qryForPosting = "SELECT prID, prNo, purpose, prDate, canvassDate, 
												 concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, 
												 prStatus 
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
						$qryForPosting = "SELECT prID, prNo, purpose, prDate, canvassDate, 
												 concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, 
												 prStatus 
										  FROM tblpr prs INNER JOIN tblemp_accounts emps 
										  ON prs.requestBy = emps.empID 
										  WHERE prStatus <> 'pending' 
								 	 	  AND prStatus <> 'for_posting'
										  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."'";
					} else {
                        $countQry = "SELECT COUNT(prID) totalBut 
                                     FROM tblpr prs
                                     INNER JOIN tblemp_accounts emps 
                                     ON prs.requestBy = emps.empID 
                                     WHERE prStatus <> 'pending' 
                                     AND prStatus <> 'for_posting' 
                                     AND emps.empID = '". $_SESSION['log_empID']  ."'";
                        $qryForPosting = "SELECT prID, prNo, purpose, prDate, canvassDate, 
                                                 concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, 
                                                 prStatus 
                                          FROM tblpr prs INNER JOIN tblemp_accounts emps 
                                          ON prs.requestBy = emps.empID 
                                          WHERE prStatus <> 'pending' 
                                          AND prStatus <> 'for_posting'
                                          AND emps.empID = '". $_SESSION['log_empID']  ."'";
                    }

					if (isset($_GET["po_no"])) {
						$searchInput = $_GET["po_no"];
						unset($_GET["po_no"]);
					}

					if (!empty($searchInput)) {
						echo '<div>
							  <label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
			    						border-radius: 25px; 
			    						margin-left: 3px;
			    						margin-bottom: 2px;"
			    			     href="canvass.php">
			    			     Clear
			    			   </a><br><br></div>';

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
							echo '
							<div class="table-container-1">
								<form method="post" name="frmPRPost" action="">
									<table class="table" id="tblLists">
										<tr>
											<th width="1%"></th>
											<th align="left" style="padding-left: 10px;" width="10%">
												PR No
											</th>
											<th width="10%">
												PR Date
											</th>
											<th width="55%">
												Purpose
											</th>
											<th width="15%">
												Requested By
											</th>
											<th></th>
											<th></th>
										</tr>';

										while ($res = $resQry->fetch_object()) {
											$data[] = array("prID" => $res->prID,
															"prNo" => $res->prNo,
															"prDate" => $res->prDate,
															"purpose" => $res->purpose,
															"name" => $res->name,
															"canvassDate" => $res->canvassDate,
															"prStatus" => $res->prStatus);
										}

										#====================================================
										// Display canvas data to table

										foreach ($data as $count => $item) {
											$itemCount++;
											$fCount = $count + 1;
											$ctr = $startlimit + $count + 1;

											echo '
											<tr id="row_0" onclick="servOC(' . $fCount . ',\'pr_info.php\',\'\')">
												<td>' . $ctr . '</td>
												<td>';

											if ($item["prNo"] != "") {
												echo $item["prNo"];
											} else {
												echo '--';
											}

											echo '
												</td>
												<td>' . $item["prDate"] . '</td>
												<td align="left" 
													style="padding-left: 20px;" id="name' . $fCount . '">
													<img class="img-button" src="../../assets/images/down.png"> ' . $item["purpose"] . 
												'</td>
												<td>' . $item["name"] . '</td>';

                                            if (isset($_SESSION['log_staff'])) {
											    echo '
												<td colspan="2">';
                                            } else {
                                                echo '
                                                <td>';
                                            }

                                            echo '
													<a data-toggle="tooltip" data-placement="left" title="Click to print the canvass form for this Purchase Request (PR No: '. $item["prNo"] .')" href="javascript:void(0);" onclick="$(this).showPrintDialog(\'' . $item["prID"]. 
														'\',\'canvass\',\'' . $item["prID"] . '\',\'' . $item["prNo"] . '\');" 
														title="Print Preview">
														<img class="img-button" src="../../assets/images/print.png" alt="print">
													</a>
											  	</td>';

                                            if (!isset($_SESSION['log_staff'])) {
                                                echo '
                                                <td>
                                                    <a data-toggle="tooltip" data-placement="left" title="Click to create an abstract for this Purchase Request (PR No: '. $item["prNo"] .')" href="abstract.php?po_no='. $item["prNo"] .'">
                                                        <img class="img-button" src="../../assets/images/create.png" alt="Create ORS">
                                                    </a>
                                                </td>';
                                            }

											echo '</tr>';

										  	echo '
										  	<tr style="background: #fff; display: none;" id="ihtr'. $fCount .'">
											  	<td colspan="7" class="pr-info">
											  		<iframe id="ihif'. $fCount . '" frameborder="0" width="100%" 
											  			src="pr_info.php?selected=' . $item["prID"] . '"></iframe>
											  	</td>
										  	</tr>';
										}

										while ($itemCount < $perPage) {
											echo "<tr id='row_0'><td colspan='7'></td>";
											$itemCount++;
										}

										#====================================================
							echo
								'	</table>
								</form>
							</div>';
						} else {
							echo '
								<div align="center" style="color:#999999">
									<br>
									----- No available record for posting. -----
									<br>
									<br>
								</div>';
						}
					}

					#====================================================

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
		<input name="qtn" type="hidden" id="qtn">
		<input name="sig" type="hidden" id="sig">
		<input name="inputDate" type="hidden" id="inputDate">
		<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">
	</form>

<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/canvass-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>
