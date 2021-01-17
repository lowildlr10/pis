<?php

include_once("session.php");
include_once("../layout/main_layout.php");	
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {

	// Initial values
	$result = 0;
	$pidSelected = "";
	$itemNo = 1;
	$action = "Save Request";
	$disable = "";

	$prNo = "";
	$prDate = "";
	$canvassDate = "";
	$abstractDate = "";
	$abstractApprovalDate = "";
	$requestBy = "";
	$sectionID = "";
	$signatory = "";
	$purpose = "";
	$remarks = "";
	$procurementMode = "";

	// Get and set PR Status
	$qryStatus = $conn->query("SELECT statusName 
							   FROM tblpr_status 
							   WHERE id = '1'") 
							   or die(mysqli_error($conn));
	$_prStatus = $qryStatus->fetch_object();
	$prStatus = $_prStatus->statusName;
	
	// Get the units of issue names.
	$unitArray = array('');
	$qryUnits = $conn->query("SELECT unitName 
							  FROM tblunit_issue 
							  ORDER BY unitName ASC") 
							  or die(mysqli_error($conn));

	while ($opt = $qryUnits->fetch_object()) {
		$unitArray[] = $opt->unitName;
	}

	// Operation for adding and updating PR data.
	if (isset($_POST['btnSave'])) {		
		$allowed_tags = "";

		while (list($key, $val) = each($_POST)) {
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}

		$prDate = trim($_POST['txtPRDate']);
		$requestBy = trim($_POST['selReqBy']);
		$signatory = trim($_POST['selAppBy']);
		$sectionID = $_POST['selSection'];
		$purpose = trim($_POST['txtPurpose']);
		$remarks = trim($_POST['txtRemarks']);
		$procurementMode = trim($_POST['selProcMode']);
		$countItems = $_POST['txtItemCount'];
		
		$tblAccess = new db_operation(); 
		$tblAccess->initialize("tblpr");

		if (!isset($_POST['prUpdate'])) {
			//add request
			if (!empty($prNo)) {
				$qryCheck = $conn->query("SELECT prID 
										  FROM tblpr 
										  WHERE prNo = '".$prNo."'");
				
				if (mysqli_num_rows($qryCheck)) {
					$result = 5;				
				}
			} else {
				$result = 4;
			}

			// Auto Generate prNo
			$prSequenceQry = $conn->query("SELECT prNo, prID 
										   FROM tblpr
										   ORDER BY prID ASC");
		    $currentYearMonth = date('y') . date('m');
		    $prNumber = "";

		    while ($_prNumber = $prSequenceQry->fetch_object()) {
		    	if (substr($_prNumber->prNo, 0, 4) == $currentYearMonth) {
		    	 	$prNumber = $_prNumber->prNo;
		    	}
		    }

		    $prSequenceNumber = (int)substr($prNumber, 4) + 1;
		    $prNo = $currentYearMonth . str_pad($prSequenceNumber, 3, '0', STR_PAD_LEFT);

			$tblAccess->insert(compact('prNo', 'prDate', 'canvassDate', 'abstractDate', 
									   'abstractApprovalDate', 'requestBy', 'sectionID', 
									   'signatory', 'purpose', 'remarks', 'procurementMode', 'prStatus'), $conn); 

			if (mysqli_affected_rows($conn) != -1) {
				// No error on adding
				$pidSelected = mysqli_insert_id($conn);
				$result = 1;
			} else {
				// There is an error on adding
				$result = 3;
			}
				
		} else {
			//update request
			$pidSelected = $_POST['prUpdate'];

			$conn->query("DELETE FROM tblbids_quotations 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblpo_jo 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblpo_jo_items 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblors 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tbldv 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tbliar 
					      WHERE prID = '". $pidSelected ."'")
						  or die(mysqli_error($conn));

			if (!empty($prNo)) {
				$qryCheck = $conn->query("SELECT prID 
										  FROM tblpr 
										  WHERE prNo='".$prNo."' AND prID <> '".$pidSelected."'");

				if (mysqli_num_rows($qryCheck)) {
					//$prNo='';
					$result = 5;
				}
			}

			if (!isset($_POST['hdaddPR'])) {
				/*
				if (is_dir($dir . 'uploads/canvass/' . $prNo)) {
					$listDir = scandir($dir . 'uploads/canvass/' . $prNo);

					if ($listDir) {
						foreach ($listDir as $key => $file) {
							if ($file != ".." && $file != ".") {
								$uploadDirectory = $dir . "uploads/canvass/" . $prNo . "/" . $file;
								echo $uploadDirectory;
								chmod($uploadDirectory, 0777);
								unlink($uploadDirectory);
							}
						}
					}
				}*/

				$tblAccess->update(compact('prDate', 'canvassDate', 'abstractDate', 
									       'abstractApprovalDate', 'requestBy', 'sectionID', 
									       'signatory', 'purpose', 'remarks', 'procurementMode', 'prStatus'),
										   "prID='".$pidSelected."'",$conn); 
			}

			if (mysqli_affected_rows($conn) != -1) {
				// No error on updating
				if ($result != 5) {
					$result = 2;
				}
			} else {
				// There is an error on updating
				$result = 3;
			}
			
		}

		if (isset($_POST['prUpdate']) && !isset($_POST['hdaddPR'])) {
			$conn->query("DELETE FROM tblpr_info WHERE prID='".$pidSelected."'");
		}

		// Insert PR items
		if ($result != 3) {
			$prID = $pidSelected;

			$itemCounter = 0;

			while ($itemNo <= $countItems) {
				$itemCounter++;
				$tblAccess = new db_operation();
				$tblAccess->initialize("tblpr_info");

				if (isset($_POST["txtDesc".$itemNo.""])) {
					$quantity = trim($_POST["txtQty".$itemNo.""]);
					$itemDescription = trim($_POST["txtDesc".$itemNo.""]);
					$unitIssue = trim($_POST["selUnit".$itemNo.""]);
					$stockNo = trim($_POST["txtStockNo".$itemNo.""]);
					$estimateUnitCost = trim($_POST["txtEUC".$itemNo.""]);

					if ($estimateUnitCost > 0) {
						$estimateTotalCost = $quantity * $estimateUnitCost;
					} else {
						$estimateUnitCost = '';
						$estimateTotalCost = '';
					}

					$infoID = $prID . "-" . $itemCounter;
					$tblAccess->insert(compact('infoID', 'prID','quantity','unitIssue','itemDescription',
											   'stockNo','estimateUnitCost','estimateTotalCost'),$conn);
				}

				$itemNo++;
			}//end while
			if (mysqli_affected_rows($conn) != -1) {
				header("Location: purchase_for_posting.php?result=" . $result);
				exit();
			}
		}//end if result
	}//end btnsave

	if (isset($_GET['edit']) || isset($_GET['copypr'])) {	
		if (isset($_GET['copypr'])) {
			$title = "Add Purchase Request";
			$action = "Save Request";
			$pidSelected = substr($_GET['copypr'],strpos($_GET['copypr'],"_")+1);
		} else {
			$title = "Edit Purchase Request";
			$action = "Update Request";
			$pidSelected = $_GET['edit'];
		}

		$qryDetails = "SELECT * 
					   FROM tblpr 
					   WHERE prID = '".$pidSelected."'";
		$detResult = $conn->query($qryDetails);

		$data = $detResult->fetch_object();

		$prNo = $data->prNo;
		$prDate = $data->prDate;
		$requestBy = $data->requestBy;
		$sectionID = $data->sectionID;
		$signatory = $data->sectionID;
		$purpose = $data->purpose;
		$remarks = $data->remarks;
		$procurementMode = $data->procurementMode;
		$disable = '';

		if(isset($_GET['copypr'])){
			$prDate = "";
			$prNo = "";
			$remarks = "";
		}

		if(isset($_REQUEST['stat'])){
			$disable = ' disabled="disabled"';
		}		
	} else {
		$title = "Create New Purchase Request";
	}

	start_layout("DOST-CAR Procurement System",
		         "<a href='purchase.php' style='color: rgb(225, 239, 243);'>Purchase Request</a>/".
		         "<a href='purchase_op.php' style='color: #98ffe8;'>Create</a>");	
?>
	
	<div id="action">
		<?php
		$location = 'purchase.php';

		if (isset($_GET['loc'])) {
			$location = $_GET['loc'] . '.php';
		}
		
		?>

		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="<?php echo $location ?>">&lt;&lt;Back</a>
				</div>
			</div>
			<div class="col-md-3" style="padding: 0px;"></div>
			<div class="col-md-6" style="padding: 0px;"></div>
		</div>
	</div>

	<div style="clear:both; border: 5px solid #015c7b; border-radius: 9px; background-color: white; padding: 22px;     overflow: auto;">
		<form id="frmPR" name="frmPR" method="post" action="" onsubmit="return $(this).check_input(this)">
		    <table class="table table-responsive create-pr-table" width="95%" align="center">
			    <tr>
			      	<td class="caption">
			      		<font color="#990000">* </font>
			      		<strong>PR Date:</strong>
			      	</td>
			      	<td align="left">
			      	<!--
			      		<input class="form-control" readonly name="txtPRDate" 
			      		type="text" id="txtPRDate" value="<?php echo $prDate ?>" 
			      	!-->
			      		<div class="form-group">
			                <div class='input-group date' id='txtPRDate'>
			                    <input type='text' class="form-control required" name="txtPRDate" value="<?php echo $prDate ?>">
			                    <span class="input-group-addon">
			                        <span class="glyphicon glyphicon-calendar"></span>
			                    </span>
			                </div>
			            </div>
			      	</td>
			      	<td align="left"class="caption"><strong>Purchase Request No.:</strong></td>
			      	
			      	<?php
			      	if (empty($prNo)) {
			      	?>
			      		<td align="left"><input class="form-control" readonly name="txtprNo" type="text" id="txtprNo" 
			        		value="Auto Generated" >
			      	<?php
			      	} else {
			        ?>
			        	<td align="left"><input class="form-control" readonly name="txtprNo" type="text" id="txtprNo" 
			        		value="<?php echo $prNo ?>" >
			        <?php
			        }	
			        ?>

			        </td>
			    </tr>

			    <tr>
			        <td class="caption"><strong>Department: </strong></td>
			        <td align="left"><input type="text" class="form-control font-color-1" 
			        	value="DOST-CAR" disabled="disabled"></td>
			        <td width="28%" class="caption"><font color="#990000">* </font><strong>Procurement Mode:</strong></td>
		        	<td width="31%" align="left">
		        		<select class="form-control" name="selProcMode" id="selProcMode"
			        		<?php echo $disable ?>>

			        		<?php

			        		$qryProcMode = $conn->query("SELECT modeName 
														 FROM tblprocurement_mode
														 ORDER BY id ASC") 
														 or die(mysqli_error($conn));

							while ($proc = $qryProcMode->fetch_object()) {
			        		?>
					          	<option value="<?php echo $proc->modeName ?>"
					          		<?php echo $procurementMode == $proc->modeName ? ' selected="selected"':'';?>>
					          		<?php echo $proc->modeName ?>
					          	</option>
				        	<?php 
				        	}
				        	?>
			        	</select>
		        	</td>
			    </tr>
			    <tr>
			        <td class="caption"><strong>Section: </strong></td>
			        <td align="left">
			        	<select class="form-control" name="selSection" id="selSection">
			        		<?php
								$qrySec = $conn->query("SELECT * FROM tblsections 
														 ORDER BY sectionID ASC") 
														 or die(mysqli_error($conn));
								
								while ($dataSec = $qrySec->fetch_object()) {
									echo '<option value="'.$dataSec->sectionID.'"';
									echo $dataSec->sectionID == $sectionID ? ' selected="selected"':'';
									echo '>'.$dataSec->section.'</option>';
								}
							?>
			        	</select>
			        </td>
			        <td width="20%" class="caption"></td>
			        <td width="30%" align="left">
			        </td>
			    </tr>
			   	<tr>
			        <td colspan="4" style="height:2px;"></td>
			   	</tr>
			    <tr>
			      	<td colspan="4" class="caption" style="text-align:center; background-color: #005e7c; color: #fff; 
			      		border: 2px #235e7a solid;">
			      		<strong>ITEMS</strong>
			      	</td>
			    </tr>

			    <tr>
			        <td colspan="4" class="caption" style="background-color: #fff; 
			        									   border-left: 2px #235e7a solid;
   														   border-right: 2px #235e7a solid;
   														   border-bottom: 1px #fff solid;">
   						<div class="table-container-1" style="border: 0px #000 solid;     
   															  border-bottom-left-radius: 0px;
    														  border-bottom-right-radius: 0px;">
					        <table class="table table-responsive" align="center" id="tblInn" style="width: 100%; margin: 0;">
					          	<tr>
					            	<th width="5%">&nbsp;</th>
					            	<th width="7%"><font color="#990000">* </font>Qty</th>
					            	<th width="8%"><font color="#990000">* </font>Unit of Issue</th>
					            	<th width="45%"><font color="#990000">* </font>Item Description</th>
					            	<th width="7%">Stock No.</th>
					            	<th width="12%">Estimated Unit Cost</th>
					            	<th width="12%">Estimated Cost</th>
					            	<th width="4%">&nbsp;</th>
					          	</tr>

								<?php
								if (!empty($pidSelected)) {
									$qryPR_Info = "SELECT * FROM tblpr_info 
												   WHERE prID='".$pidSelected."' 
												   ORDER BY LENGTH(infoID), infoID ASC";

									if ($resInfo = $conn->query($qryPR_Info)) {
										$itemNo = 0;
										$x = 0;

										if (!isset($_REQUEST['stat'])) {
											while ($data = $resInfo->fetch_object()) {				
												$itemNo++;
												echo '<tr id="row_0">
								        	    <td><input type="checkbox" name="chk" id="chk' . $itemNo . '" disabled="disabled" /></td>
												<td scope="col"><input class="form-control required" name="txtQty'.$itemNo.'" type="number" id="txtQty'.
													$itemNo.'" size="5" value="'.$data->quantity.
													'" onKeyPress="return $(this).checkIt(event);" onchange="javascript: $(this).computeCost(' . $itemNo . ', \'txtQty'.$itemNo.'\')" title="Item Quantity" /></td>
								            	<td scope="col"><select class="form-control required" name="selUnit'.$itemNo.'" id="selUnit'.$itemNo.'">';
								              	while(list(,$val) = each($unitArray)){
													echo '<option value="'.$val.'"';
													echo $data->unitIssue == $val?' selected="selected"':'';
													echo '>'.$val.'</option>';
												}
												reset($unitArray);
												echo '
												</select>            </td>
								            	<td scope="col"><textarea class="form-control required" name="txtDesc'.$itemNo.'" id="txtDesc'.$itemNo.'" rows="2" cols="30" title="Item Description">'.$data->itemDescription.'</textarea></td>
								            	<td scope="col"><input class="form-control" name="txtStockNo'.$itemNo.'" type="text" id="txtStockNo'.$itemNo.'" value="'.$data->stockNo.'" size="5" onKeyPress="return $(this).checkIt(event)" /></td>
								     	        <td scope="col"><input class="form-control required" name="txtEUC'.$itemNo.'" type="text" id="txtEUC'.$itemNo.'" value="'.$data->estimateUnitCost.'" size="10" onKeyPress="return $(this).checkIt(event)" onChange="return $(this).computeCost(' . $itemNo . ', \'txtEUC'.$itemNo.'\')" /></td>
								            	<td scope="col"><input class="form-control" name="txtEC'.$itemNo.'" type="text" id="txtEC'.$itemNo.'" value="'.$data->estimateTotalCost.'" size="10" onKeyPress="return $(this).checkIt(event)" disabled="disabled" /> </td>
												<td style="text-align:center;"><a href="javascript: $(this).deleteRow(\'tblInn\',\'chk' . $itemNo . '\')" title="Delete Item"><img class="img-button" src="../../assets/images/delete.png" /></a></td>
								          		</tr>';
											}//end while
										} else {
											while($data = $resInfo->fetch_object()){				
												$itemNo++;
												echo '<tr id="row_0">
												<td><input type="checkbox" name="chk" id="chk' . $itemNo . '" /></td>
								        	    <td scope="col">'.$data->quantity.'</td>
								            	<td scope="col">';
												echo $data->unitIssue;
												echo '</td>
								            	<td style="text-align:left;">'.$data->itemDescription.'</td>
								            	<td>'.$data->stockNo.'</td>
								     	        <td  style="text-align:right;">'.$data->estimateUnitCost.'</td>
								            	<td style="text-align:right;">'.$data->estimateTotalCost.'</td>
												<td style="text-align:center;"><a href="javascript: $(this).deleteRow(\'tblInn\',\'chk' . $itemNo . '\')" title-"Delete Item"><img class="img-button" src="../../assets/images/delete.png" /></a></td>
								          		</tr>';
											}//end while
										}			
									}//end if
								} else {//isset pidSelected		
								?>
							        <tr id="row_0">
							            <td><input type="checkbox" name="chk" id="chk1"></td>
							            <td scope="col">
							            	<input class="form-control required" name="txtQty1" type="number" id="txtQty1" size="5" 
							            	onKeyPress="return $(this).checkIt(event)" onchange="javascript: $(this).computeCost(1,'txtQty1')" 
							            	title="Item Quantity" placeholder="...">
							            </td>
							            <td scope="col">
							            	<select class="form-control required" name="selUnit1" id="selUnit1">
								                <?php
												while(list(,$val) = each($unitArray)){
													echo '<option value="'.$val.'">'.$val.'</option>';;			
												}
												reset($unitArray);
												?>
								            </select>
							          	</td>
							            <td  scope="col">
							            	<textarea class="form-control required" name="txtDesc1" id="txtDesc1" 
							            			  rows="2" cols="30" title="Item Description" placeholder="Type here..."></textarea>
							            </td>
							            <td scope="col">
							            	<input class="form-control" name="txtStockNo1" type="text" id="txtStockNo1" 
							            		   size="5" onKeyPress="return $(this).checkIt(event)" placeholder="...">
							            </td>
							            <td scope="col">
							            	<input class="form-control required" name="txtEUC1" type="text" id="txtEUC1" 
							            		   size="10" onKeyPress="return $(this).checkIt(event)" placeholder="..."
							            		   onchange="javascript:$(this).computeCost(1,'txtEUC1')">
							            </td>
							            <td scope="col">
							            	<input class="form-control" name="txtEC1" type="text" id="txtEC1" 
							            		   size="10" onKeyPress="return $(this).checkIt(event)" disabled="disabled">
							            </td>
							            <td style="text-align:center;">
							            	<a href="javascript: $(this).deleteRow('tblInn','chk1')" title="Delete Item">
							            		<img class="img-button" src="../../assets/images/delete.png">
							            	</a>
							            </td>
							        </tr>
						        <?php
								}
								?>
							</table>
						</div>
			        </td>
			    </tr>

			    <?php
				if (!isset($_REQUEST['stat'])) {
				?>

			      	<tr>
			      	  	<td colspan="4" align="center" 
			      	  		style="padding: 0 8px 14px 8px; background-color: #fff; border-bottom: 2px #235e7a solid;
								   border-left: 2px #235e7a solid; border-right: 2px #235e7a solid;">
			      	  		<a class="btn btn-default btn-block" href="#" onClick="$(this).addRow('#tblInn'); return false;" 
			      	  		   style="padding: 16px; border: 4px #4577b4 dashed;color: #4577b4;background-color: #fff;font-weight: bold;">
			      	  			<span class="glyphicon glyphicon-plus"></span>
			      	  			Add Item
			      	  		</a>
			      	  		<!--
			      	  		<input class="btn btn-info form-control" type="button" name="button" id="button" 
			      	  			   value="Add Item" onClick="$(this).addRow('tblInn');">
			      	  		!-->
			      	  	</td>
			      	</tr>
			      	<tr>
			      		<td colspan="4"></td>
			      	</tr>

			      	<?php
				  	} else {
				  	?>
			      	<tr>
			      	  	<td colspan="4" align="center">
			      	  		<input type="hidden" name="hdaddPR" value="<?php echo $pidSelected  ?>">
			      	  	</td>
			      	</tr>

			    <?php
				}
				?>

			    <tr>
			      	<td class="caption">
			      		<strong>Remarks:</strong>
			      	</td>
			      	<td align="left">
			      		<textarea class="form-control" name="txtRemarks" id="txtRemarks" cols="25" rows="5"
			      			<?php echo $disable ?>><?php echo $remarks ?></textarea>
			      	</td>
			      	<td class="caption">
			      		<strong>
				      		<font color="#990000">
				      			* 
				      		</font>
				      		Purpose:
				      	</strong>
			      	</td>
			      	<td align="left">
			      		<textarea class="form-control required" name="txtPurpose" id="txtPurpose" cols="25" rows="5"
			      			<?php echo $disable ?>><?php echo $purpose ?></textarea>
			      	</td>
			    </tr>
		      	<tr>
		      		<td colspan="4">
				      	<table class="table table-responsive" border="0" cellpadding="4" cellspacing="1" align="center" width="75%">      
				      		<tr>
						        <td align="center"><font color="#990000">* </font>Request By:<br>
							        <br>
							        <select class="form-control" name="selReqBy" id="selReqBy"
							        	<?php echo $disable ?>>
							        	<?php
							        	if (isset($_SESSION['log_admin'])) {
											$qryEmps = $conn->query("SELECT empID, concat(lastname,', ',firstname,' ',left(middlename,1),'. ') 
																	 AS name, position 
																	 FROM tblemp_accounts 
																	 ORDER BY name ASC") 
																	 or die(mysqli_error($conn));
										} else {
											$qryEmps = $conn->query("SELECT empID, concat(lastname,', ',firstname,' ',left(middlename,1),'. ') 
																	 AS name, position 
																	 FROM tblemp_accounts 
																	 WHERE empID = '". $_SESSION['log_empID'] ."'
																	 ORDER BY name ASC") 
																	 or die(mysqli_error($conn));
										}
									
										while ($data = $qryEmps->fetch_object()) {
											echo '<option value="'.$data->empID.'"';
											if (empty($requestBy)) {
												echo $data->empID == $_SESSION['log_empID'] ? ' selected="selected"':'';
											} else {
												echo $data->empID == $requestBy ? ' selected="selected"':'';
											}
											echo '>'.$data->name.' [ '.$data->position.' ]</option>';
										}

										?>
							        </select>
							    </td>
							    <td width="5%"></td>
							    <td align="center"><font color="#990000">* </font>Approved By: <br>
							        <br>
							        <select class="form-control" name="selAppBy" id="selAppBy"
							        	<?php echo $disable ?>>
							            <?php
										$qryEmps = $conn->query("SELECT * FROM tblsignatories 
																 WHERE active = 'yes'
																 AND p_req = 'y' 
																 ORDER BY name ASC") 
																 or die(mysqli_error($conn));
										
										while ($data = $qryEmps->fetch_object()) {
                                            /*
											if (strpos(strtoupper($data->position), "DIRECTOR")) {
												echo '<option value="'.$data->signatoryID.'"';
												echo $data->signatoryID == 38 ? ' selected="selected"':'';
												echo $data->signatoryID == $signatory ? ' selected="selected"':'';
												echo '>'.$data->name.' [ '.$data->position.' ]</option>';
											} */

                                            echo '<option value="'.$data->signatoryID.'"';
                                             echo $data->signatoryID == 35 ? ' selected="selected"':'';
                                             echo $data->signatoryID == $signatory ? ' selected="selected"':'';
                                             echo '>'.$data->name.' [ '.$data->position.' ]</option>';
										}
										?>
							        </select>
						        </td>
				          	</tr>
				      	</table>
		      		</td>
		      	</tr>
		      	<tr>
		        	<td colspan="4">
		        		<div align="center">
			        		<br>

			        		<?php
							if (!empty($pidSelected) && !isset($_GET['copypr'])) {
								echo '<input type="hidden" name="prUpdate" value="'.$pidSelected.'">';
							}
							?>

			             	<input class="btn btn-primary" type="submit" name="btnSave" id="btnSave" 
			             		value="<?php echo $action ?>">
			                <input name="txtItemCount" type="hidden" id="txtItemCount" 
			                	value="<?php echo $itemNo ?>">
		          		</div>
		          	</td>
		      	</tr>
		    </table>
		</form>
	</div>

<?php
end_layout("purchase");
}//end if
?>