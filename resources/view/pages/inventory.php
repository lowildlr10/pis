<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
	start_layout("DOST-CAR Procurement System","<a href='inventory.php' style='color: #98ffe8;'>Inventory</a>");

	$page = "inventory";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
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
					<a class="btn btn-danger operation-back" href="access.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-6" style="padding: 0px;"></div>
			<div class="col-md-3" style="padding: 0px;">
				<div class="btn-group btn-group-justified">
					<a id="btn-add-inventory" class="btn btn-primary operation">ADD ITEM/S</a>
				</div>
			</div>
		</div>
	</div>

	<?php
		if(isset($prResult)){
			echo '<div class="msg">'.$prResult.'</div>';
			unset($prResult);
		}
	?>

	<form id="frmSize" name="frmSize" method="post" action="../../../class_function/print_preview.php" target="_self">
		<input name="print" type="hidden" id="print">
		<input name="what" type="hidden" id="what">
		<input name="inv-class-no" type="hidden" id="inv-class-no">
        <input name="po-no" type="hidden" id="po-no">
        <input name="recieved-by" type="hidden" id="recieved-by">
        <input name="multiple" type="hidden" id="multiple">
		<input name="font-scale" type="hidden" id="font-scale">
		<input name="paper-size" type="hidden" id="paper-size">
	</form>

	<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">

		<table class="table table-hover table-responsive" id="tblStyle">
			<tr>
			  	<th>
			  		<div class="col-xs-12 col-md-12" style="padding: 0px;">
				  		<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#List of Items for Inventory</label></strong>
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
						$countQry = "SELECT COUNT(DISTINCT inv.inventoryClassNo) AS totalBut 
									 FROM tblinventory_items AS inv 
								     INNER JOIN tblpo_jo_items AS po 
								     ON po.id = inv.poItemID 
								     INNER JOIN tblpr AS pr 
								     ON pr.prID = inv.prID
								     INNER JOIN tblemp_accounts AS emp
								     ON emp.empID = pr.requestBy ";
				    	$qryINV = "SELECT inv.prID, inv.poItemID, inv.propertyNo, inv.inventoryClass, 
				    			   	   	  inv.itemStatus, pr.requestBy, po.poNo, inv.inventoryClassNo, 
				    			   	   	  GROUP_CONCAT(po.itemDescription SEPARATOR ';;') AS itemDescription, 
				    			   	   	  GROUP_CONCAT(po.quantity SEPARATOR ';;') AS quantity, 
				    			   	   	  GROUP_CONCAT(inv.id SEPARATOR ';;') AS id, 
				    			   	   	  concat(lastname,', ',firstname,' ',left(middlename,1),'.') AS name 
				    			   FROM tblinventory_items AS inv 
								   INNER JOIN tblpo_jo_items AS po 
								   ON po.id = inv.poItemID 
								   INNER JOIN tblpr AS pr 
								   ON pr.prID = inv.prID
								   INNER JOIN tblemp_accounts AS emp
								   ON emp.empID = pr.requestBy ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(DISTINCT inv.inventoryClassNo) AS totalBut 
									 FROM tblinventory_items AS inv 
								     INNER JOIN tblpo_jo_items AS po 
								     ON po.id = inv.poItemID 
								     INNER JOIN tblpr AS pr 
								     ON pr.prID = inv.prID
								     INNER JOIN tblemp_accounts AS emp
								     ON emp.empID = pr.requestBy 
				    			     WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
				    	$qryINV = "SELECT inv.prID, inv.poItemID, inv.propertyNo, inv.inventoryClass, 
				    			   	   	  inv.itemStatus, pr.requestBy, po.poNo, inv.inventoryClassNo, 
				    			   	   	  GROUP_CONCAT(po.itemDescription SEPARATOR ';;') AS itemDescription, 
				    			   	   	  GROUP_CONCAT(po.quantity SEPARATOR ';;') AS quantity, 
				    			   	   	  GROUP_CONCAT(inv.id SEPARATOR ';;') AS id, 
				    			   	   	  concat(lastname,', ',firstname,' ',left(middlename,1),'.') AS name 
				    			   FROM tblinventory_items AS inv 
								   INNER JOIN tblpo_jo_items AS po 
								   ON po.id = inv.poItemID 
								   INNER JOIN tblpr AS pr 
								   ON pr.prID = inv.prID
								   INNER JOIN tblemp_accounts AS emp
								   ON emp.empID = pr.requestBy 
				    			   WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
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
    						     href="inventory.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (po.poNo LIKE '%$searchInput%' 
												  OR emp.firstname LIKE '%$searchInput%' 
												  OR emp.middlename LIKE '%$searchInput%' 
												  OR emp.lastname LIKE '%$searchInput%' 
												  OR inv.propertyNo LIKE '%$searchInput%' 
												  OR po.itemDescription LIKE '%$searchInput%' 
												  OR inv.itemStatus LIKE '%$searchInput%' 
												  OR inv.inventoryClass LIKE '%$searchInput%') 
												  OR inv.inventoryClassNo = '$searchInput' 
												  GROUP BY(inv.inventoryClassNo) 
												  ORDER BY po.id DESC LIMIT $limit";
						$qryINV = $qryINV . " AND (po.poNo LIKE '%$searchInput%' 
											  OR emp.firstname LIKE '%$searchInput%' 
											  OR emp.middlename LIKE '%$searchInput%' 
											  OR emp.lastname LIKE '%$searchInput%' 
											  OR inv.propertyNo LIKE '%$searchInput%' 
											  OR po.itemDescription LIKE '%$searchInput%' 
											  OR inv.itemStatus LIKE '%$searchInput%' 
											  OR inv.inventoryClass LIKE '%$searchInput%') 
											  OR inv.inventoryClassNo = '$searchInput' 
											  GROUP BY(inv.inventoryClassNo) 
											  ORDER BY po.id DESC LIMIT $limit";
					} else {
						$qryINV = $qryINV . " GROUP BY(inv.inventoryClassNo) 
											  ORDER BY po.id DESC LIMIT $limit";
					}

					if ($resQry = $conn->query($qryINV)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
							echo '<table class="table table-hover" id="tblLists" align="center">';		
							echo '<tr>
									  <th width="1%"></th>
									  <th width="15%">Inventory No.</th>
									  <th width="60%">Item Description</th>
									  <th width="15%">Requested By</th>
									  <th width="5%">Classification & Status</th>
									  <th></th>
									  <th></th>
								  </tr>';
							$ctr = $startlimit;

							while ($data = $resQry->fetch_object()) {
								$itemCount++;
								$ctr++;
								$inventoryID = explode(';;', $data->id);
								$itemDescription = explode(';;', $data->itemDescription);
								$qntyArray = explode(';;', $data->quantity);
								$available = explode(';;', $data->quantity);

								echo '<tr id=row_0>';
								echo '<td>'.$ctr.'</td>';
								echo '<td><strong>'.$data->inventoryClassNo.'</strong></td>';
								echo '<td><div style="overflow: auto;"><strong><ul style="text-align:  left;">';

									foreach ($itemDescription as $descKey => $itemDesc) {
										$qryItemIssue = $conn->query("SELECT quantity 
																	  FROM tblitem_issue 
																	  WHERE inventoryID = '" . $inventoryID[$descKey] . "'") 
																	  or die(mysql_error($conn));

										if (mysqli_num_rows($qryItemIssue)) {
											while ($list1 = $qryItemIssue->fetch_object()) {
												$available[$descKey] -= $list1->quantity;
											}
										}						
										
										if ($available[$descKey] <= "0") {
											$issueFontColor = "#d9534f";

											echo '<li>' . $itemDesc . '<span><strong><a style="color: ' . $issueFontColor . ';"' . 
												 'class="issue-link"> [' . $available[$descKey] . '/' . $qntyArray[$descKey] . '] 
												 (Out of Stock)</a></strong></span></li>';
										} else {
											$issueFontColor = "#5cb85c";

											echo '<li>' . $itemDesc . '<span><strong><a style="color: ' . $issueFontColor . ';"' . 
												 'href="javascript: $(this).printDialog(\'' . $inventoryID[$descKey] . '\',\'' . $data->inventoryClass . '\',\'' . 
										  												 $data->prID . '\',\'' . $data->poNo . '\',\'' . 
										  												 'new' . '\',\'' . '0' . '\',\'' . $data->inventoryClassNo . '\',\'' . 
										  												 'n' . '\');"' .
												 ';" class="issue-link"> [' . $available[$descKey] . '/' . $qntyArray[$descKey] . '] 
												 (Click to issue <span class="glyphicon glyphicon-send"></span>)</a></strong></span></li>';
										}

											
									}

								echo '</ul></strong></div>';
								echo '<br>';
								echo '<a href="javascript: $(this).printDialog(\'' . $data->id . '\',\'' . $data->inventoryClass . '\',\'' . 
									  												 $data->prID . '\',\'' . $data->poNo . '\',\'' . 
									  												 'new' . '\',\'' . '0' . '\',\'' . $data->inventoryClassNo . '\',\'' . 
									  												 'y' . '\');" 
									  	 class="btn btn-default btn-sm btn-block" 
									  	 style="color: ' . $issueFontColor . '; border: 2px #006699 solid;">
									  	 <span class="font-color-1 glyphicon glyphicon-folder-open"></span>' . 
									  	'<strong class="font-color-1"> Click to Issue All</strong></span>
									  </a>';
								echo '</td>';
								/*
								if ($data->itemStatus == "recorded") {
									if ($available == "0") {
										echo '<td align="left">' . 
												$data->itemDescription . 
												'<br><br><a class="btn btn-default btn-sm btn-block" style="color: ' . $issueFontColor . ';">' . 
												'<strong>[Quantity = ' . $data->quantity . ', Available = ' . $available .
											 ']</strong></a></td>';
									} else {
										echo '<td align="left">' . 
												$data->itemDescription . 
												'<br><br><a href="javascript: $(this).printDialog(\'' . $data->id . '\',\'' . $data->inventoryClass . 
											   		'\',\'' . $data->prID . '\',\'' . $data->poNo . '\',\'' . 'new' . '\',\'' . '0' . '\');" class="btn btn-default btn-sm btn-block" 
											   		style="color: ' . $issueFontColor . '; border: 2px #006699 solid;"><span class="font-color-1 glyphicon glyphicon-folder-open"></span>' . 
												'<strong class="font-color-1"> Click to Issue</strong> <strong><span id="txt-issue-' . $ctr . '">' . 
												'[Quantity = ' . $data->quantity . ', Available = ' . $available .
											 ']</strong></span></a></td>';
									}
								} else if ($data->itemStatus == "issued") {
									echo '<td align="left">' . 
											$data->itemDescription . 
											'<br><br><a class="btn btn-default btn-sm btn-block" style="color: ' . $issueFontColor . ';">' . 
											'<strong>[Quantity = ' . $data->quantity . ', Available = ' . $available .
										 ']</strong></a></td>';
								}*/

								echo '<td>'. $data->name. '</td>';
								echo '<td><strong>' . strtoupper($data->inventoryClass) . ' - ' . strtoupper($data->itemStatus). '</strong></td>';

								echo '<td>
										   <a data-toggle="tooltip" data-placement="left" 
										   	  title="Click to print Inventory for this Purchase/Job Order (PO/JO No: '. 
										   		$data->poNo .')" href="javascript: $(this).listIssued(\'' . $data->id . '\', \'' . $data->inventoryClassNo . '\');" title="Print Preview">
											    <img class="img-button" src="../../assets/images/print.png"" alt="print">
										   </a>
									  </td>';

								echo '<td>';

								if ($data->itemStatus == "recorded") {
									echo '<a data-toggle="tooltip" data-placement="left" title="Click to issue the item for this Purchase/Job Order (PO/JO No: '. 
										   		$data->poNo .')" href="javascript: $(this).issueItem(\''.$data->inventoryClassNo.'\', \''.$data->poNo.'\', \''.$data->itemStatus.'\');" 
										   		title="Issue Item">
												<img class="img-button" src="../../assets/images/approve.png"" alt="Issue Item">
										  </a>';  
								} else if ($data->itemStatus == "issued") {
									echo '<a>
											  <img class="img-button" src="../../assets/images/closed.gif"" alt="Issue Item">
										  </a>';  
								}

								echo '</td>';

								echo '</tr>';
							}

							while ($itemCount < $perPage) {
								echo "<tr id=row_0><td colspan='7'></td></tr>";
								$itemCount++;
							}

							echo '</table>
								  <input type="hidden" value="final" name="hdAction" /></form></div>';
						} else {
							echo '<div align="center" style="color:#999999">
									  <br>----- No available record for Inventory -----<br><br>
								  </div>';
						}
					}


























					/*
					if (isset($_SESSION['log_admin'])) {
						$countQry = "SELECT COUNT(inv.id) totalBut 
									 FROM tblinventory_items inv 
								     INNER JOIN tblpo_jo_items po 
								     ON po.id = inv.poItemID 
								     INNER JOIN tblpr pr 
								     ON pr.prID = inv.prID
								     INNER JOIN tblemp_accounts emp
								     ON emp.empID = pr.requestBy 
								      ";
				    	$qryINV = "SELECT inv.prID, inv.poItemID, inv.propertyNo, inv.inventoryClass, inv.id, 
				    			   	   	  inv.itemStatus, pr.requestBy, po.poNo, po.itemDescription, po.quantity, 
				    			   	   	  concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, 
				    			   	   	  inv.id, inv.inventoryClassNo 
				    			   FROM tblinventory_items inv 
								   INNER JOIN tblpo_jo_items po 
								   ON po.id = inv.poItemID 
								   INNER JOIN tblpr pr 
								   ON pr.prID = inv.prID
								   INNER JOIN tblemp_accounts emp
								   ON emp.empID = pr.requestBy ";
					} else if (isset($_SESSION['log_pstd'])) {
						$countQry = "SELECT COUNT(inv.id) totalBut 
									 FROM tblinventory_items inv 
								     INNER JOIN tblpo_jo_items po 
								     ON po.id = inv.poItemID 
								     INNER JOIN tblpr pr 
								     ON pr.prID = inv.prID
								     INNER JOIN tblemp_accounts emp
								     ON emp.empID = pr.requestBy 
				    			     WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
				    	$qryINV = "SELECT inv.prID, inv.poItemID, inv.propertyNo, inv.inventoryClass, inv.id, 
				    			   	   	  inv.itemStatus, pr.requestBy, po.poNo, po.itemDescription, po.quantity, 
				    			   	   	  concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, 
				    			   	   	  inv.id, inv.inventoryClassNo  
				    			   FROM tblinventory_items inv 
								   INNER JOIN tblpo_jo_items po 
								   ON po.id = inv.poItemID 
								   INNER JOIN tblpr pr 
								   ON pr.prID = inv.prID
								   INNER JOIN tblemp_accounts emp
								   ON emp.empID = pr.requestBy 
				    			   WHERE emp.sectionID = '" . $_SESSION['log_sectionID'] . "' ";
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
    						     href="inventory.php">
    						     Clear
    						   </a><br><br>';

						$countQry = $countQry . " AND (po.poNo LIKE '%$searchInput%' 
												  OR emp.firstname LIKE '%$searchInput%' 
												  OR emp.middlename LIKE '%$searchInput%' 
												  OR emp.lastname LIKE '%$searchInput%' 
												  OR inv.propertyNo LIKE '%$searchInput%' 
												  OR po.itemDescription LIKE '%$searchInput%' 
												  OR inv.itemStatus LIKE '%$searchInput%' 
												  OR inv.inventoryClass LIKE '%$searchInput%') 
												  ORDER BY po.id DESC LIMIT $limit";
						$qryINV = $qryINV . " AND (po.poNo LIKE '%$searchInput%' 
											  OR emp.firstname LIKE '%$searchInput%' 
											  OR emp.middlename LIKE '%$searchInput%' 
											  OR emp.lastname LIKE '%$searchInput%' 
											  OR inv.propertyNo LIKE '%$searchInput%' 
											  OR po.itemDescription LIKE '%$searchInput%' 
											  OR inv.itemStatus LIKE '%$searchInput%' 
											  OR inv.inventoryClass LIKE '%$searchInput%') 
											  ORDER BY po.id DESC LIMIT $limit";
					} else {
						$qryINV = $qryINV . " ORDER BY po.id DESC LIMIT $limit";
					}

					if ($resQry = $conn->query($qryINV)) {
						if (mysqli_num_rows($resQry)) {
							echo '<div class="table-container-1"><form name="frmPRPost" method="post">';
							echo '<table class="table table-hover" id="tblLists" align="center">';		
							echo '<tr>
									  <th width="1%"></th>
									  <th width="10%">Inventory No.</th>
									  <th width="20%">Stock/Property No.</th>
									  <th width="45%">Item Description</th>
									  <th width="15%">Requested By</th>
									  <th width="5%">Classification & Status</th>
									  <th></th>
									  <th></th>
								  </tr>';
							$ctr = $startlimit;

							while ($data = $resQry->fetch_object()) {
								$available = $data->quantity;
								$itemCount++;
								$ctr++;

								$qryItemIssue = $conn->query("SELECT quantity 
															  FROM tblitem_issue 
															  WHERE inventoryID = '" . $data->id . "'") 
															  or die(mysql_error($conn));

								if (mysqli_num_rows($qryItemIssue)) {
									while ($list1 = $qryItemIssue->fetch_object()) {
										$available -= $list1->quantity;
									}
								}						
								
								if ($available <= 0) {
									$issueFontColor = "#d9534f";
								} else {
									$issueFontColor = "#5cb85c";
								}

								echo '<tr id=row_0>';
								echo '<td>'.$ctr.'</td>';
								echo '<td>'.$data->inventoryClassNo.'</td>';
								echo '<td>'.$data->propertyNo.'</td>';

								if ($data->itemStatus == "recorded") {
									if ($available == "0") {
										echo '<td align="left">' . 
												$data->itemDescription . 
												'<br><br><a class="btn btn-default btn-sm btn-block" style="color: ' . $issueFontColor . ';">' . 
												'<strong>[Quantity = ' . $data->quantity . ', Available = ' . $available .
											 ']</strong></a></td>';
									} else {
										echo '<td align="left">' . 
												$data->itemDescription . 
												'<br><br><a href="javascript: $(this).printDialog(\'' . $data->id . '\',\'' . $data->inventoryClass . 
											   		'\',\'' . $data->prID . '\',\'' . $data->poNo . '\',\'' . 'new' . '\',\'' . '0' . '\');" class="btn btn-default btn-sm btn-block" 
											   		style="color: ' . $issueFontColor . '; border: 2px #006699 solid;"><span class="font-color-1 glyphicon glyphicon-folder-open"></span>' . 
												'<strong class="font-color-1"> Click to Issue</strong> <strong><span id="txt-issue-' . $ctr . '">' . 
												'[Quantity = ' . $data->quantity . ', Available = ' . $available .
											 ']</strong></span></a></td>';
									}
								} else if ($data->itemStatus == "issued") {
									echo '<td align="left">' . 
											$data->itemDescription . 
											'<br><br><a class="btn btn-default btn-sm btn-block" style="color: ' . $issueFontColor . ';">' . 
											'<strong>[Quantity = ' . $data->quantity . ', Available = ' . $available .
										 ']</strong></a></td>';
								}

								echo '<td>'. $data->name. '</td>';
								echo '<td><strong>' . strtoupper($data->inventoryClass) . ' - ' . strtoupper($data->itemStatus). '</strong></td>';

								echo '<td>
										   <a data-toggle="tooltip" data-placement="left" title="Click to print Inventory for this Purchase/Job Order (PO/JO No: '. 
										   		$data->poNo .')" href="javascript: $(this).listIssued(\'' . $data->id . '\');" title="Print Preview">
											    <img class="img-button" src="../../assets/images/print.png"" alt="print">
										   </a>
									  </td>';

								echo '<td>';

								if ($data->itemStatus == "recorded") {
									echo '<a data-toggle="tooltip" data-placement="left" title="Click to issue the item for this Purchase/Job Order (PO/JO No: '. 
										   		$data->poNo .')" href="javascript: $(this).issueItem(\''.$data->id.'\', \''.$data->poNo.'\', \''.$data->itemStatus.'\');" 
										   		title="Issue Item">
												<img class="img-button" src="../../assets/images/approve.png"" alt="Issue Item">
										  </a>';  
								} else if ($data->itemStatus == "issued") {
									echo '<a>
											  <img class="img-button" src="../../assets/images/closed.gif"" alt="Issue Item">
										  </a>';  
								}

								echo '</td>';

								echo '</tr>';
							}

							while ($itemCount < $perPage) {
								echo "<tr id=row_0><td colspan='8'></td></tr>";
								$itemCount++;
							}

							echo '</table>
								  <input type="hidden" value="final" name="hdAction" /></form></div>';
						} else {
							echo '<div align="center" style="color:#999999">
									  <br>----- No available record for Inventory -----<br><br>
								  </div>';
						}
					}*/
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
	include_once("modal/inventory-modals.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}