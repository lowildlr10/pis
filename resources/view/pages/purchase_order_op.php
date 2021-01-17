<?php

include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

//update
$poDate = "";
$pid = "";
$supplier = "";
$pmode = "";
$placeDel = "";
$dateDel = "";
$delTerm = "";
$payTerm = "";
$amt = "";
$award = "";
$address = "";
$signatoryApp = "";
$signDept = "";
$signatoryFunds = "";
$excluded = "n";
$editThis = "";
$action = "";

if (isset($_REQUEST['poNo']) && !empty($_REQUEST['poNo'])) {	
	$poNo = $_REQUEST['poNo'];
	$qryEdit = $conn->query("SELECT pd.*, bs.company_name, bs.address, pr.procurementMode 
							 FROM tblpo_jo pd 
							 INNER JOIN tblbidders bs 
							 ON pd.awardedTo=bs.bidderID 
							 INNER JOIN tblpr pr 
							 ON pd.prID=pr.prID 
							 WHERE poNo='".$poNo."'");
	
	if (mysqli_num_rows($qryEdit)) {
		$data = $qryEdit->fetch_object();
		$poDate = $data->poDate;
		$pid = $data->prID;
		$supplier = $data->company_name;
		$pmode = $data->procurementMode;
		$placeDel = $data->placeDelivery;
		$dateDel = $data->deliveryDate;
		$delTerm = $data->deliveryTerm;
		$payTerm = $data->paymentTerm;
		$amt = $data->amountWords;
		$award = $data->awardedTo;
		$address = $data->address;
		$signatoryApp = $data->signatoryApp;
		$signatoryFunds = $data->signatoryFunds;
		$editThis = $_REQUEST['poNo'];
		$action = "Update P.O.";
	} else {
		$result="No Record for " . $_REQUEST['edit'];
	}
}

?>

<form method="post" name="frmAdd" id="frmAdd" onSubmit="return $(this).check_input(this);">
  	<table class="table table-responsive" style="border: 1px solid #000;" align="center">
     	<tr>
     	  	<th style="background-color: #006699; color: #FFF;" colspan="4">P.O. DETAILS</th>
     	</tr>
     	<tr>
     	  	<th colspan="4">&nbsp;</th>
     	</tr>

	  	<?php 

	  	if (isset($result)) { ?>
      		<tr>
      			<td colspan="4">
      				<div class="msg"><?php echo $result ?>
      					
      				</div>
      			</td>
      		</tr>
      	<?php } else { ?>

      	<tr>
      	  	<td align="right" width="20%">
      	  		Supplier: 
      	  	</td>
      	  	<td align="left" width="30%">
      	  		<b><?php echo $supplier ?></b>
      	  	</td>
      	  	<td align="left" width="20%">
      	  		P.O. No.:
      	  	</td>
      	  	<td align="left" width="30%">
      	  		<b><?php echo $poNo ?></b>
      	  	</td>
      	</tr>
      	<tr>
      	  	<td align="right">Address:</td>
      	  	<td align="left">
      	  		<b><?php echo $address ?></b></td>
      	  	<td align="left">P.O. Date: <font color="#FF0000">*</font></td>
      	  	<td align="left">
      	  		<div class="form-group">
			        <div class='input-group date' id='txtPODate'>
			            <input type='text' class="form-control" name="txtPODate" value="<?php echo $poDate ?>">
			            <span class="input-group-addon">
			                <span class="glyphicon glyphicon-calendar"></span>
			            </span>
			        </div>
			    </div>
      	  	</td>
      	</tr>
      	<tr>
	        <td align="right">&nbsp;</td>
	        <td align="left">&nbsp;</td>
	        <td align="left">Mode of Procurement:</td>
	        <td align="left">
	        	<?php
				echo $pmode;
			 	?>
			</td>
	    </tr>
	    <tr>
	        <td colspan="4" align="left">
	        	Gentlemen:
	        	<br>
	         	Please furnish this office the following articles subject too the term and conditions herein:
	         	<br>
	         </td>
      	</tr>
      	<tr>
	        <td align="right">
	        	Place of Delivery: <font color="#FF0000">*</font>
	        </td>
	        <td align="left">
	        	<input class="form-control" name="txtPlaceDel" type="text" id="txtPlaceDel" value="<?php echo $placeDel ?>" size="40">
	        </td>
	        <td align="left">
	        	Delivery Term: <font color="#FF0000">*</font>
	        </td>
	        <td align="left">
	        	<input class="form-control" name="txtDelTerm" type="text" id="txtDelTerm" value="<?php echo $delTerm ?>" size="40">
	        </td>
     	</tr>
      	<tr>
	        <td align="right">
	        	Date of Delivery : <font color="#FF0000">*</font>
	        </td>
	        <td align="left">
	        	<input class="form-control" name="txtDateDel" type="text" id="txtDateDel" value="<?php echo $dateDel ?>" size="40">
	        </td>
	        <td align="left">
	        	Payment Term:<font color="#FF0000"> *</font>
	        </td>
	        <td align="left">
	        	<input class="form-control" name="txtPayTerm" type="text" id="txtPayTerm" value="<?php echo $payTerm ?>" size="40">
	        </td>
    	</tr>
      	<tr>
        	<td colspan="4" align="center">
        	<?php

        	$isEdited = false;
        	$qryItems = $conn->query("SELECT poNo, id 
									  FROM tblpo_jo_items 
									  WHERE poNo = '" . $_REQUEST['poNo'] . "' 
									  AND unitIssue != 'J.O.' 
									  ORDER BY id ASC")
									  or die(mysqli_error($conn));
			
			if (!mysqli_num_rows($qryItems)) {
				$qryPR_Info = "SELECT pri.infoID, pri.unitIssue, pri.quantity, pri.itemDescription, bq.amount, bq.remarks 
							   FROM tblbids_quotations AS bq 
							   INNER JOIN tblpr_info AS pri 
							   ON bq.infoID = pri.infoID 
							   WHERE pri.prID =  '".$pid."' 
							   AND pri.unitIssue != 'J.O.' 
							   AND bq.bidderID = '".$award."' 
							   AND pri.awardedTo = '".$award."' 
							   ORDER BY LENGTH(pri.infoID), pri.infoID ASC";
				$isEdited = false;
			} else {
				$qryPR_Info = "SELECT prID, infoID, unitIssue, quantity, itemDescription, amount, excluded, totalAmount  
							   FROM tblpo_jo_items 
							   WHERE poNo = '". $_REQUEST['poNo'] ."' 
							   AND unitIssue != 'J.O.' 
							   ORDER BY id";

				$isEdited = true;
			}
	
			if ($resInfo = $conn->query($qryPR_Info)) {
				$itemNo = 0;
				$total = 0;
				$grandTotal = 0;
				
				echo '<table class="table table-responsive" width="100%" id="tblInn">';			
				echo '<tr><th width="30">Item #</th>';
				echo '<th width="40">QTY</th>';
				echo '<th width="80">Unit of Issue</th>';
				echo '<th>Item Description</th>';
				echo '<th width="200">Unit Cost</th>';
				echo '<th width="200">Amount</th>';
				echo '<th width="100">Exclude This?</th>';
				echo '</tr>';

				while ($data = $resInfo->fetch_object()) {
					$itemNo++;
					$excluded = "n";

					echo '<tr>';
					echo '<td>' . $itemNo . '<input type="hidden" class="val-info-id" value="' . $data->infoID . '"></td>';
					echo '<td><input style="width: 50px;" class="input-qnty form-control" type="number" value="'.$data->quantity.'"></td>';
					echo '<td class="unit-issue">'.$data->unitIssue.'</td>';
					
					if (!$isEdited) {
						echo '<td style="text-align: center; padding-left: 10px;">'.
							    '<textarea class="input-description form-control">'.
							        htmlentities($data->itemDescription). " ". htmlentities($data->remarks) .
							    '</textarea>';
					} else {
						echo '<td style="text-align: center; padding-left: 10px;">'.
							    '<textarea class="input-description form-control">'.
							        htmlentities($data->itemDescription) .
							    '</textarea>';
					}
					
					echo '</td>';
					echo '<td><input class="input-cost form-control" type="text" value="'.$data->amount.'"></td>';

                    if (!empty($data->totalAmount)) {
                        $total = $data->totalAmount; 
                    } else {
                        $total = $data->quantity * $data->amount; 
                    }
					
					$grandTotal+=$total;
					echo '<td><input class="input-total form-control" type="text" value="'.$total.'"></td>';
					echo '<td><select class="input-excluded form-control">';

					if (isset($data->excluded)) {
						$excluded = $data->excluded;
					}

					if ($excluded == "n") {
						echo '<option value="n" selected="selected"> No </option>';
					} else {
						echo '<option value="n"> No </option>';
					}

					if ($excluded == "y") {
						echo '<option value="y" selected="selected"> Yes </option>';
					} else {
						echo '<option value="y"> Yes </option>';
					}
					
					echo '</select></td>';
					echo '</tr>';
				}
				echo '<tr><td colspan="7" style="text-align: right; font-size: 17px; "><b>Grand Total: P &nbsp; <input id="grand-total" type="text" class="form-control" value="'.$grandTotal.'" style="width: 17%; float: right;display: inline;">
					</b></td></tr>';
				echo '</table>';			
			}

			?>
			</td>
      	</tr>
      	<tr>
	        <td colspan="4" align="right">
	        	Total Amount in Words:<font color="#FF0000"> *</font>&nbsp;
	        	<input class="form-control" id="txtAmnt" name="txtAmnt" type="text" value="<?php echo $amt ?>" size="80" style="width: 60%;">
	        </td>
	    </tr>
      
	    <tr>
	      	<td colspan="4" align="left">
	      		In case of failure to make the full delivery within time specified above, a penalty of one-tenth (1/10) of one percent for every delay shall be imposed.
	      	</td>
	    </tr>
	    <tr>
	      	<td align="right">&nbsp;</td>
	      	<td align="left">&nbsp;</td>
	      	<td align="left">&nbsp;</td>
	      	<td align="left">Very Truly Yours</td>
	    </tr>
     	<tr>
	        <td align="right">&nbsp;</td>
	        <td align="left">&nbsp;</td>
	        <td align="left">&nbsp;</td>
	        <td align="left">
	        	<select class="form-control" name="selApp" id="selApp" style="width:225px;">
	        	<?php
				$qryEmps = $conn->query("SELECT * FROM tblsignatories 
										 WHERE active='yes'") 
								  or die(mysqli_error($conn));
				
				while ($data = $qryEmps->fetch_object()) {
					echo '<option value="'.$data->signatoryID.'"';
					echo $data->signatoryID == 35 ? ' selected="selected"':'';
					echo $data->signatoryID == $signDept ? ' selected="selected"':'';
					echo '>'.$data->name.'</option>';
				}
				?>
	            </select>
	        </td>
      	</tr>
	    <tr>
	        <td align="right">&nbsp;</td>
	        <td align="left">Chief Accountant/ Head of Accounting Division/Unit:</td>
	        <td align="left">&nbsp;</td>
	    </tr>
      	<tr>
	        <td align="right">&nbsp;</td>
	        <td align="left">
	        	<select class="form-control" name="selReq" id="selReq" style="width:225px;">
	          		<option value="">&nbsp;</option>
			  		<?php
					$qryEmps = $conn->query("SELECT empID, concat(firstname,' ',left(middlename,1),'. ',lastname) 
											 AS name 
											 FROM tblemp_accounts
											 WHERE blocked='n'
											 ORDER BY name ASC");
					
					while ($data = $qryEmps->fetch_object()) {
						echo '<option value="'.$data->empID.'"';
						echo $data->empID == "C-1108" ? ' selected="selected"':'';
						echo $data->empID == $signDept ? ' selected="selected"':'';
						echo '>'.$data->name.'</option>';
					}
					?>
	            </select>
	        </td>
	        <td align="left">&nbsp;</td>
      	</tr>
      	<tr>
	        <td>&nbsp;</td>
	    </tr>
      	<?php } ?>
    </table>
</form>