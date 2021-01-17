<?php

include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {

	$prNo = "";
	$prID = "";
	$toggle = "";
	$groupNumber = 0;

	$itemCtr = 0;
	$groupCounter = 0;
	$curItem = "";
	$bidderCount = array();
	$_bidderCount = array();
	$arrayBidder = array();

	if (isset($_REQUEST['prID'])) {
		$prID = $_REQUEST['prID'];
	}

	if (isset($_REQUEST['prNo'])) {
		$prNo = $_REQUEST['prNo'];
	}

	if (isset($_REQUEST['toggle'])) {
		$toggle = $_REQUEST['toggle'];
	}

	if (isset($_REQUEST['groupNumber'])) {
		$groupNumber = (int)$_REQUEST['groupNumber'];
	}

	if (isset($_REQUEST['bidderCount'])) {
		$bidderCount = $_REQUEST['bidderCount'];
	}

?>
	<div id="table-container" class="col-md-12" style="overflow: auto;
													   padding: 0px;
													   margin-bottom: 30px;
													   border-bottom: 3px #005e7c solid;">
		<table class="table table-hover" id="tblStyle" class="tblDialog">

		  	<?php

		  		$qryBidders = "SELECT *
							   FROM tblbidders
							   ORDER BY company_name ASC";
		  		$qryItems = "SELECT groupNo FROM tblpr_info 
							 WHERE prID = '".$prID."' 
							 ORDER BY LENGTH(infoID), infoID ASC";
				$group = $conn->query($qryItems);

				if ($bidQry = $conn->query($qryBidders)) {
					if (mysqli_num_rows($bidQry)) {
						while ($bidders = $bidQry->fetch_object()) {
							$arrayBidder[] = array("bidderID" => $bidders->bidderID, "name" => $bidders->company_name);
						}
					}
				}

		  	?>

				<tr style="font-size: 10px;">
					<td>

					<?php
					   	$tempResQry = $conn->query("SELECT tblpr.prID, tblpr_info.groupNo, tblpr_info.estimateUnitCost,
					   									   tblpr_info.quantity, tblpr_info.itemDescription, 
							    					   	   tblpr_info.awardedRemarks, tblpr_info.infoID, 
							    					   	   tblpr_info.awardedTo, tblbids_quotations.bidID, 
							    					   	   tblbids_quotations.remarks ,tblbids_quotations.selection, 
							    					   	   tblbids_quotations.amount, tblbids_quotations.bidderID, 
							    					   	   tblbids_quotations.lamount, tblbids_quotations.specification  
							    			    FROM tblpr Inner JOIN tblpr_info 
							    			    ON tblpr.prID = tblpr_info.prID 
							    			    LEFT JOIN tblbids_quotations 
							    			    ON tblpr_info.infoID = tblbids_quotations.infoID 
							    			    LEFT JOIN tblbidders 
							    			    ON tblbids_quotations.bidderID = tblbidders.bidderID 
							    			    WHERE tblpr.prID='".$prID."' AND tblpr_info.groupNo='".$groupNumber."'
							    			    ORDER BY LENGTH(tblpr_info.infoID), tblpr_info.infoID ASC, tblbidders.company_name ASC")
					   							or die(mysqli_error($conn));

					   	$tempBidderList = array();
					   	$tempWinner = array();
					   	$tempUnitCost = array();
					   	$tempTotalCost = array();
					   	$tempRemarks = array();
					   	$tempSelection = array();
					   	$tempBidIDs = array();

					   	if (mysqli_num_rows($tempResQry)) {
					   		$itemCounter = 0;
					   		$itemCounter2 = 0;

					   		while ($_list = $tempResQry->fetch_object()) {
					   			$tempBidderList[] = $_list->bidderID;

					   			if ($itemCounter2 == $bidderCount) {
					   				$itemCounter2 = 0;
					   				$itemCounter++;
					   			}

					   			$tempBidIDs[$itemCounter][$itemCounter2] = $_list->bidID;
					   			$tempUnitCost[$itemCounter][$itemCounter2] = $_list->amount;
					   			$tempTotalCost[$itemCounter][$itemCounter2] = $_list->lamount;
					   			$tempRemarks[$itemCounter][$itemCounter2] = $_list->remarks;
                                $tempSpecification[$itemCounter][$itemCounter2] = $_list->specification;
					   			$tempSelection[$itemCounter][$itemCounter2] = $_list->selection;

					   			$itemCounter2++;
					   		}
					   	}

					   	$resQry = $conn->query("SELECT tblpr.prID, tblpr_info.groupNo, tblpr_info.estimateUnitCost, 
					   								   tblpr_info.quantity, tblpr_info.itemDescription, 
							    					   tblpr_info.awardedRemarks, tblpr_info.infoID, 
							    					   tblpr_info.awardedTo, tblbids_quotations.bidID, 
							    					   tblbids_quotations.remarks ,tblbids_quotations.selection, 
							    					   tblbids_quotations.amount, tblbids_quotations.bidderID, 
							    					   tblbids_quotations.lamount, tblbids_quotations.specification 
							    			    FROM tblpr Inner JOIN tblpr_info 
							    			    ON tblpr.prID = tblpr_info.prID 
							    			    LEFT JOIN tblbids_quotations 
							    			    ON tblpr_info.infoID = tblbids_quotations.infoID 
							    			    LEFT JOIN tblbidders 
							    			    ON tblbids_quotations.bidderID = tblbidders.bidderID 
							    			    WHERE tblpr.prID='".$prID."' 
							    			    AND tblpr_info.groupNo='".$groupNumber."'
							    			    ORDER BY LENGTH(tblpr_info.infoID), tblpr_info.infoID ASC, tblbidders.company_name ASC")
					   							or die(mysqli_error($conn));

						if (mysqli_num_rows($resQry)) {
							echo '<table class="table table-hover" id="tblLists">';
						    echo '<tr class="abstract-suppliers" style="font-size: 12px;">
						    		  <th hidden align="center" style="min-width: 44px;">Item ID</th>
						    		  <th align="center" style="min-width: 150px;">
						    		  	  Item Description
						    		  </th>
						    		  <th align="center" style="min-width: 64px;">
						        	  	  QNTY
						        	  </th>
						        	  <th align="center" style="min-width: 64px;">
						        	  	  ABC (UNIT)
						        	  </th>';

						    if (isset($bidderCount)) {
						    	if ($bidderCount > 0) {
						        	for ($bidderCounter = 1; $bidderCounter <= $bidderCount; $bidderCounter++) { 
						        		
						        		echo '
						        		<th style="min-width: 300px;">
						        			<div style="align-content: center;" id="bid-' . $groupNumber  . '-' . $bidderCounter . '">
						        			 	<select class="group-bidders form-control font-color-1" 
						        			 			id="sel-' . $groupNumber . '-' . $bidderCounter . '">';

						        		foreach ($arrayBidder as $keyBid => $bidder) {
						        			if ($toggle == "create") {
						        				if ($keyBid == ($bidderCounter - 1)) {
							        				echo '	<option value="' . $bidder["bidderID"] . '" style="margin-top: 10px;" selected>' .
							        							$bidder["name"] .
							        						'</option>';
							        			} else {
							        				echo '	<option value="' . $bidder["bidderID"] . '" style="margin-top: 10px;">' .
							        							$bidder["name"] .
							        						'</option>';
							        			}
						        			} else if ($toggle == "edit") {
						        				if ($tempBidderList[$bidderCounter - 1] == $bidder["bidderID"]) {
							        				echo '	<option value="' . $bidder["bidderID"] . '" style="margin-top: 10px;" selected>' .
							        							$bidder["name"] .
							        						'</option>';

							        				$tempWinner[] = $bidder["bidderID"] . "+" . $bidder["name"];
							        			} else {
							        				echo '	<option value="' . $bidder["bidderID"] . '" style="margin-top: 10px;">' .
							        							$bidder["name"] .
							        						'</option>';
							        			}
						        			}
						        		}

						        		echo '
						        				</select>
						        			</div>
						        		</th>';
						        	}

						        	echo '<th align="left" style="min-width: 300px;">Awarded To</th>';
						        }
						    }

						    echo  '</tr>';

							while ($data = $resQry->fetch_object()) {
								if ($curItem != $data->infoID) {
									$curItem = $data->infoID;
									$itemCtr++;

									echo '
									<tr id="row_0" class="abstractData">';
									
									echo '
										<td hidden align="center" style="padding-left: 10px; border-bottom: 1px solid #ccc;">
											<input type="hidden" class="itemID" value="' . $groupCounter . '-' . $curItem  . '">
										</td>
										<td align="left" style="padding-left: 10px; border-bottom: 1px solid #ccc;
											border-left: 1px solid #ccc;">
											 <b>' .
											 	$data->itemDescription . 
										'	 </b>
										</td>';
									echo '
										<td style="text-align:center; padding-left: 1em; border-bottom: 1px solid #ccc;
											border-left: 1px solid #ccc;">
											<b class="quantity-value" id="' . 'qnty-' . $curItem . '">' . 
												$data->quantity . 
										'	</b>
										</td>
										<td style="text-align:center; padding-left: 1em; border-bottom: 1px solid #ccc;
											border-left: 1px solid #ccc;">
											<b>' . 
												number_format($data->estimateUnitCost,2,'.',',') . 
										'	</b>
										</td>';
						            
						            if (isset($bidderCount)) {
						            	if ($bidderCount > 0) {
						                	for ($i = 1; $i <= $bidderCount; $i++) { 
						        				echo '
						        			<td align="left" style="border-bottom: 1px solid #ccc; border-left: 1px solid #ccc;">
						        				<div style="align-content: center; margin-left: 35px; margin-top: 10px; margin-right: 35px;"
						        					id="bid-' . $curItem  . '-' . $i . '">';  

						        				if ($toggle == "create") {
						        					echo '	<div>
						        							   <input type="hidden" class="input-bid-id" value="-">
						        							</div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">
								        					   	    <font color="#990000">* </font>
								        					   	    Unit Cost: 
								        					   </label>
								        					   <input class="required font-color-1 input-unit-cost form-control" id="unitCost-' . $curItem . '-'  . $i . '" 
								        					   		  type="text" placeholder="Enter a value">
								        					</div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">
								        					   		Total Cost: 
								        					   </label>
								        					   <input class="font-color-1 input-total-cost form-control" id="totalCost-' . $curItem . '-' . $i . '" 
								        					   		  type="text" placeholder="Total Cost Value">
								        					</div>
                                                            <div style="margin-top: 10px;">
                                                               <label style="font-weight: bold;">Specification: </label>
                                                               <br>
                                                               <textarea class="text-specification font-color-1 form-control" id="txtSpecification-' . $curItem . '-' . $i . ' 
                                                                         cols="20" style=" width: 100%; height: 50px;"></textarea>
                                                            </div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">Remarks: </label>
								        					   <br>
								        					   <textarea class="text-remarks font-color-1 form-control" id="txtRemarks-' . $curItem . '-' . $i . ' 
								        					   		     cols="20" style=" width: 100%; height: 50px;"></textarea>
								        					</div>
								        					<div style="margin-top: 10px; display: none;">
								        					   <label style="font-weight: bold;">Item Selection (if any): </label>
								        					   <br>
								        					   <textarea class="text-selection font-color-1 form-control" id="txtSelection-' . $curItem . '-' . $i . ' 
								        					      	     cols="20" style=" width: 100%; height: 50px;"></textarea>
								        					</div>
								        					<br>
								        				</div>
								        			</td>';
						        				} else {
						        					echo '	<div>
						        							   <input type="hidden" class="input-bid-id" value="' . $tempBidIDs[$itemCtr - 1][$i - 1] . '">
						        							</div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">
								        					   	    <font color="#990000">* </font>
								        					   	    Unit Cost: 
								        					   </label>
								        					   <input class="required font-color-1 input-unit-cost form-control" id="unitCost-' . $curItem . '-'  . $i . '" 
								        					   		  type="text" placeholder="Enter a value" value="' . $tempUnitCost[$itemCtr - 1][$i - 1] . '">
								        					</div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">
								        					   		Total Cost: 
								        					   </label>
								        					   <input class="font-color-1 input-total-cost form-control" id="totalCost-' . $curItem . '-' . $i . '" 
								        					   		  type="text" placeholder="Total Cost Value" value="' . $tempTotalCost[$itemCtr - 1][$i - 1] . '">
								        					</div>
								        					<div style="margin-top: 10px;">
								        					   <label style="font-weight: bold;">Remarks: </label>
								        					   <br>
								        					   <textarea class="text-remarks font-color-1 form-control" id="txtRemarks-' . $curItem . '-' . $i . ' 
								        					   		     cols="20" style=" width: 100%; height: 50px;">' . $tempRemarks[$itemCtr - 1][$i - 1] . '</textarea>
								        					</div>
                                                            <div style="margin-top: 10px;">
                                                               <label style="font-weight: bold;">Specification: </label>
                                                               <br>
                                                               <textarea class="text-specification font-color-1 form-control" id="txtSpecification-' . $curItem . '-' . $i . ' 
                                                                         cols="20" style=" width: 100%; height: 50px;">' . $tempSpecification[$itemCtr - 1][$i - 1] . '</textarea>
                                                            </div>
								        					<div style="margin-top: 10px; display: none;">
								        					   <label style="font-weight: bold;">Item Selection (if any): </label>
								        					   <br>
								        					   <textarea class="text-selection font-color-1 form-control" id="txtSelection-' . $curItem . '-' . $i . ' 
								        					      	     cols="20" style=" width: 100%; height: 50px;">' . $tempSelection[$itemCtr - 1][$i - 1] . '</textarea>
								        					</div>
								        					<br>
								        				</div>
								        			</td>';
						        				}
						        			}

						        			echo '
						        			<td align="left" style="border-bottom: 1px solid #ccc; border-left: 1px solid #ccc;">
						        				<div class="grp-' . $groupNumber . '" style="margin-left: 35px; margin-top: 10px; margin-right: 35px;">
						        					<label style="font-weight: bold;">
						        						<font color="#990000">* </font>
						        						Select a Supplier: 
						        						<br>
						        					</label>
						        					<select class="select-award font-color-1 form-control" id="selAward-' . $groupNumber . "-" . $curItem .
						        						 '" style="width: 100%; height: 35px; font-size: 13px;">';

						        				if ($toggle == "create") {
						        					echo '<option value="0" selected="selected"></option>';

							        				for ($z = 0; $z < $bidderCount; $z++) {
							        					if ($arrayBidder[$z]["bidderID"] == $data->awardedTo) {
							        						echo '
								        					<option value="' . $arrayBidder[$z]["bidderID"] . '" selected="selected">' .
								        						 $arrayBidder[$z]["name"] .
								        					'</option>';
							        					} else {
							        						echo '
								        					<option value="' . $arrayBidder[$z]["bidderID"] . '">' .
								        						 $arrayBidder[$z]["name"] .
								        					'</option>';
							        					}
							        				}
							        			} else {
							        				echo '<option value="0" selected="selected"></option>';

							        				foreach ($tempWinner as $value) {
							        					$arrayWinner = explode("+",  $value);

							        					if ($arrayWinner[0] == $data->awardedTo) {
								        					echo '<option value="' . $arrayWinner[0] . '" selected="selected">' . $arrayWinner[1] . '</option>';
								        				} else {
								        					echo '<option value="' . $arrayWinner[0] . '">' . $arrayWinner[1] . '</option>';
								        				}
							        				}
							        			}

						        			echo '
						        					</select>
						        					<div style="margin-top: 10px;">
						        						<label style="font-weight: bold;">Remarks: </label>
						        						<br>
						        						<textarea class="text-award-remarks font-color-1 form-control" id="txtRemarks-' . $groupNumber . "-" . $curItem . '" 
						        							cols="20" style=" width: 100%; height: 50px;">' . $data->awardedRemarks . '</textarea>
						        					</div>
						        				</div>
						        			</td>';
						        		}
						            }
						            
						            echo '
						            </tr>';
								}

								$groupCounter++;
							}//end while

							echo '</table>';
						} else {
							echo '
							<div align="center" style="color:#d9534f; font-size: 18px;">
								<br>
								----- Generate first a canvass form. -----
								<br>
								<a target="_parent" href="canvass.php?po_no=' . $prNo . '"> 
							   	  [Click here to create a canvass form.]
							    </a>
								<br>
								<br>
							</div>';
						}
					?>
					</td>
				</tr>
		</table>
	</div>
<?php
}
?>