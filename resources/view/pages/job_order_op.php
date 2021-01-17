<?php 

include_once("../../../config.php");
include_once($dir . "dbcon.php");

$poNo = "";
$poDate = "";
$pid = "";
$placeDel = "";
$dateDel = "";
$payTerm = "";
$amt = "";
$awardedTo = "";
$address = "";
$signatoryApp = "";
$signatoryFunds = "";
$editThis = "";
$signDept = "";
$excluded = "n";
$amountWord = "";

if (isset($_REQUEST['poNo']) && !empty($_REQUEST['poNo'])) {	
	$poNo = $_REQUEST['poNo'];
	$qryEdit = $conn->query("SELECT pd.*, bs.company_name, bs.address, pr.procurementMode  
							 FROM tblpo_jo pd 
							 INNER JOIN tblbidders bs 
							 ON pd.awardedTo = bs.bidderID 
							 INNER JOIN tblpr pr 
							 ON pd.prID=pr.prID 
							 WHERE poNo='".$poNo."'");
	
	if (mysqli_num_rows($qryEdit)) {
		$data = $qryEdit->fetch_object();
		$poDate = $data->poDate;
		$pid = $data->prID;
		$placeDel = $data->placeDelivery;
		$dateDel = $data->deliveryDate;
		$payTerm = $data->paymentTerm;
		$amt = $data->amountWords;
		$awardedTo = $data->company_name;
		$awardID = $data->awardedTo;
		$address = $data->address;
		$signatoryApp = $data->signatoryApp;
		$signatoryFunds = $data->signatoryFunds;
		$editThis = $_REQUEST['poNo'];
		$action = "Update J.O.";
	} else {
		$result="No Record for " . $_REQUEST['edit'];
	}
}

?>

<form id="frmAdd" action="../../../class_function/print_preview.php?what=jo" target="_blank" method="post" name="frmJO">
    <table class="table" style="border: 1px solid #000;" align="center">
	    <tr>
	      	<th colspan="4" align="center" style="background-color: #006699; color: #FFF;">
	      		JOB ORDER DETAILS
	      	</th>
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
     		<td width="15%">
     			<strong>JOB ORDER NO: </strong>
     		</td>
     		<td align="left">
     			<?php echo $poNo ?>
     		</td>
     		<td width="20%">
     			&nbsp;
     		</td>
     		<td>
     			&nbsp;
     		</td>
     	</tr>
     	<tr>
     		<td width="15%">
     			<strong>DATE: <font color="#FF0000">*</font></strong>
     		</td>
     		<td align="left">
                    <div class="form-group">
                        <div class='input-group date' id='txtJODate'>
                            <input type='text' class="form-control" name="txtJODate" value="<?php echo $poDate ?>">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
     		</td>
     		<td width="5%">
     			&nbsp;
     		</td>
     		<td>
     			&nbsp;
     		</td>
     	</tr>
     	<tr>
     		<td width="15%">
     			<strong>TO: </strong>
     		</td>
     		<td align="left">
     			<?php echo $awardedTo ?>
     		</td>
     		<td width="5%">
     			&nbsp;
     		</td>
     		<td>
     			&nbsp;
     		</td>
     	</tr>
     	<tr>
     		<td width="15%">
     			<strong>Address: </strong>
     		</td>
     		<td align="left">
     			<?php echo $address ?>
     		</td>
     		<td width="5%">
     			&nbsp;
     		</td>
     		<td>
     			&nbsp;
     		</td>
     	</tr>
     	<tr>
     		<td colspan="4" align="left">
     			<br>
     			Sir/Madam: <br><br>
     			In connection with the existing regulations, you are hereby authorized to undertake the indicated job/work below:
     			<br>
     		</td>
     	</tr>
     	<tr>
     		<td colspan="4" align="center">
        	<?php

            $isEdited = false;
            $qryItems = $conn->query("SELECT poNo, id 
                                      FROM tblpo_jo_items 
                                      WHERE poNo = '" . $_REQUEST['poNo'] . "' 
                                      AND unitIssue = 'J.O.' 
                                      ORDER BY id ASC")
                                      or die(mysqli_error($conn));

            if (!mysqli_num_rows($qryItems)) {
                 $qryPR_Info = "SELECT pri.infoID, pri.unitIssue, pri.quantity, pri.itemDescription, bq.amount, bq.remarks 
                                FROM tblbids_quotations AS bq 
                                INNER JOIN tblpr_info AS pri 
                                ON bq.infoID = pri.infoID 
                                WHERE pri.prID =  '".$pid."' 
                                AND pri.unitIssue = 'J.O.' 
                                AND bq.bidderID =  '".$awardID."' 
                                AND pri.awardedTo =  '".$awardID."' 
                                ORDER BY LENGTH(pri.infoID), pri.infoID ASC";
                 $isEdited = false;
            } else {
                 $qryPR_Info = "SELECT prID, infoID, unitIssue, quantity, itemDescription, amount, excluded 
                                FROM tblpo_jo_items 
                                WHERE poNo = '". $_REQUEST['poNo'] ."' 
                                AND unitIssue = 'J.O.' 
                                ORDER BY id";

                 $isEdited = true;
            }
	
			if ($resInfo = $conn->query($qryPR_Info)) {
                $itemNo = 0;
                $total = 0;
                $grandTotal = 0;

				echo '<table class="table table-hover" id="tblInn">';			
				echo '<tr><th width="">&nbsp;</th>';
                echo '<tr><th width="5%">&nbsp;</th><th width="15%">&nbsp;</th>';
				echo '<th width="50%">JOB/ WORK Description</th>';
				echo '<th width="30%">Amount</th>';
                echo '<th width="10%">Excluded This?</th>';
				echo '</tr>';

				while ($data = $resInfo->fetch_object()) {
					$itemNo++;
                    $excluded = "n";

					echo '<tr>';
                         echo '<td class="input-qnty">'. $data->quantity . '</td>';
					echo '<td>'. $data->unitIssue.'</td>';
					
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
					echo '<td><input class="input-cost form-control" type="text" value="'.$data->amount.'" disabled="disabled">
                              <input type="hidden" class="val-info-id" value="' . $data->infoID . '">
                          </td>';
					$total = $data->quantity * $data->amount; 
					$grandTotal+=$total;
                    echo '<td><select class="input-excluded form-control">';

                    if (isset($data->excluded)) {
                        $excluded = $data->excluded;
                    }

                    if ($excluded == "y") {
                        echo '<option value="y" selected="selected"> Yes </option>';
                    } else {
                        echo '<option value="y"> Yes </option>';
                    }

                    if ($excluded == "n") {
                        echo '<option value="n" selected="selected"> No </option>';
                    } else {
                        echo '<option value="n"> No </option>';
                    }
                    
                    echo '</select></td>';
					echo '</tr>';
				}

				echo '<tr><td colspan="3" style="text-align: center;"><b><em>TOTAL AMOUNT </em></b></td>';
				echo '<td colspan="2" style="text-align: center;"><b><input class="form-control" id="grand-total" type="text" value="'.$grandTotal.'" disabled="disabled"></b></td>';
				echo '</tr>';
				echo '</table>';			
			}

			?>
			</td>
     	</tr>
     	<tr>
     		<td colspan="4" align="left">
     			Completion/Delivery : within the specified date of delivery
     		</td>
     	</tr>
     	<tr>
     		<td align="left">
     			<b><em>Place of Delivery : <font color="#FF0000">*</em></b>
     		</td>
     		<td align="left">
     			<input class="form-control" name="txtPlaceDel" type="text" id="txtPlaceDel" value="<?php echo $placeDel ?>" size="40">
     		</td>
     		<td align="right">
     			
     		</td>
     		<td>
     			
     		</td>
     	</tr>
     	<tr>
     		<td align="left">
     			<b><em>Date of Delivery : <font color="#FF0000">*</em></b>
     		</td>
     		<td align="left">
     			<input class="form-control" name="txtDateDel" type="text" id="txtDateDel" value="<?php echo $dateDel ?>" size="40">
     		</td>
     		<td align="right">
     			<b><em>Payment Term : <font color="#FF0000">*</em></b>
     		</td>
     		<td>
     			<input class="form-control" name="txtPayTerm" type="text" id="txtPayTerm" value="<?php echo $payTerm ?>" size="40">
     		</td>
     	</tr>
     	<tr>
     		<td align="center" colspan="4">
     			<p>
     				This order is authorized by the DEPARTMENT OF SCIENCE AND TECHNOLOGY, Cordillera Administrative Region <br>
     				under DR. VICTOR B. MARIANO, Regional Director in the amount not to exceed <br><br><input id="amountWord" 
                    class="form-control" type="text" value="<?= $amt ?>" placeholder="Input grand total in words...."> <br>
     				(Php <?= number_format($grandTotal, 2) ?>). The cost of this WORK ORDER will be charged against DOST-CAR after work has been completed.
     			</p>
     		</td>
     	</tr>
     	<tr>
     		<td align="center" colspan="4">
     			<p><b><em>
     				In case of failure to make the full delivery within time specified above, a penalty of one-tenth (1/10) of one <br>
     				percent for everyday of delay shall be imposed.
     			</b></p></em>
     		</td>
     	</tr>
     	<tr>
     		<td align="center" colspan="4">
     			<p>
     				lease submit your bill together with the original of this JOB/WORK ORDER to expedite payment.
     			</b>
     		</td>
     	</tr>
     	<tr>
     		<td align="left">
     			<em>Very truly yours, </em>
     		</td>
     		<td align="left"></td>
     		<td align="right"></td>
     		<td></td>
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td align="left" colspan="2">
     			Requisitioning Office/Dept.:
     		</td>
     		<td align="left"></td>
     		<td align="left">APPROVED:</td>
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td align="left" colspan="2">
     			<select class="form-control" name="selReq" id="selReq">
	          		<option value="">&nbsp;</option>
			  		<?php
					$qryEmps = $conn->query("SELECT empID, concat(firstname,' ',left(middlename,1),'. ',lastname) 
											 AS name 
											 FROM tblemp_accounts
											 WHERE blocked='n'
											 ORDER BY name ASC");
					
					while ($data = $qryEmps->fetch_object()) {
						echo '<option value="'.$data->empID.'"';
						echo $data->empID == "B-0805" ? ' selected="selected"':'';
						echo $data->empID == $signDept ? ' selected="selected"':'';
						echo '>'.$data->name.'</option>';
					}
					?>
	            </select>
     		</td>
     		<td align="left"></td>
     		<td align="left">
     			<select class="form-control" name="selApp" id="selApp">
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
     		<td align="left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Authorized Signatory</td>
     		<td align="left"></td>
     		<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Authorized Signatory</td>
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td align="left" colspan="2">Funds Available:</td>
     		<td align="left"></td>
     		<td align="left"></td>
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td colspan="4">
     	</tr>
     	<tr>
     		<td align="left" colspan="2">
     			<select class="form-control" name="selFunds" id="selFunds">
					<option value="">&nbsp;</option>
					<?php
					$qryEmps = $conn->query("SELECT * FROM tblsignatories 
											 WHERE active='yes'") 
											 or die(mysqli_error($conn));

					while ($data = $qryEmps->fetch_object()) {
						echo '<option value="'.$data->signatoryID.'"';
						echo $data->signatoryID == 24 ? ' selected="selected"':'';
						echo $data->signatoryID == $signatoryFunds ? ' selected="selected"':'';
						echo '>'.$data->name.'</option>';
					}
					?>
		        </select>
     		</td>
     		<td align="left"></td>
     		<td align="left">
     		</td>
     	</tr>
     	<tr>
     		<td align="left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Authorized Signatory</td>
     		<td align="left"></td>
     		<td align="left"></td>
     	</tr>

     	<?php } ?>
	    
    </table>
</form>