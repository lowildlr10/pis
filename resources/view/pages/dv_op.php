<?php
	
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$empID = "";
$dvDate = "";
$dvNo = "";
$_payee = "";
$address = "";
$_paymentMode = "";
$particulars = "";
$amount = "";
$signatory = "";
$isChecked = "0";

if (isset($_POST["orsID"]) && !empty($_POST["orsID"])) {
	$orsID = $_POST["orsID"];

    $qryEdit = $conn->query("SELECT ors.poNo, pr.requestBy, ors.payee, ors.address, dv.paymentMode, 
    								dv.particulars, dv.dvNo, dv.dvDate, ors.amount, ors.signatoryReq 
                            FROM tblors ors 
							INNER JOIN tbldv dv 
							ON ors.id = dv.orsID 
                            INNER JOIN tblpr pr 
                            ON pr.prID = dv.prID 
                            WHERE dv.orsID = '". $orsID ."' 
							OR ors.id = '" . $orsID . "'")
                            or die(mysqli_error($conn));

    if ($qryEdit) {
        $data = $qryEdit->fetch_object();
        $empID = $data->requestBy;
        $dvNo = $data->dvNo;
        $dvDate = $data->dvDate;
        $poNo = $data->poNo;
        $_payee = $data->payee;
        $address = $data->address;
        $_paymentMode = $data->paymentMode;
        $particulars = $data->particulars;
        $amount = number_format($data->amount, 2);
        $signatory = $data->signatoryReq;
    }

    $paymentMode = explode("-", $_paymentMode);
    $qryEmp = $conn->query("SELECT firstname, middlename, lastname  
							FROM tblemp_accounts 
							WHERE empID = '" . $_payee . "'");
	$qrySuppliers = $conn->query("SELECT company_name 
								  FROM tblbidders 
								  WHERE bidderID = '" . $_payee . "'");

	if (!empty($poNo)) {
		if (mysqli_num_rows($qrySuppliers)) {
			$data = $qrySuppliers->fetch_object();
			$payee = $data->company_name;
		}
	} else {
		if (mysqli_num_rows($qryEmp)) {
			$data = $qryEmp->fetch_object();
			if (!empty($data->middlename)) {
				$payee = $data->firstname . " " . $data->middlename[0] . ". " . $data->lastname;
			} else {
				$payee = $data->firstname . " " . $data->lastname;
			}
		}
	}
}

?>

<table id="tblStyle" class="table">
    <tr>
        <th colspan="4">DISBURSEMENT VOUCHER</th>
    </tr>
    <tr>
        <td colspan="4">
            <div class="row">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12"></div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtDvNo">
                            <strong>DV No: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtDvNo" type="text" 
                               id="txtDvNo" value="<?php echo $dvNo ?>">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtDvDate">
                            <strong>Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <div class='input-group date' id='txtDvDate'>
                                <input type='text' class="form-control" name="txtDvDate" 
                                       value="<?php echo $dvDate ?>">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label>
                            <strong>Mode of Payment: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">

                    	<?php
                    		$strCheck = '';

                    		if ($paymentMode[0] == "1") {
                    			$strCheck = 'checked="checked"';
                    		}
                    	?>

                        <label class="checkbox-inline">
					      	<input value="1" type="checkbox" name="check-mds"
                                   style="margin: -5px -27px;" <?php echo $strCheck ?>> 
                            <label> MDS Check</label>
					    </label>

					    <?php
                    		$strCheck = '';

                    		if ($paymentMode[1] == "1") {
                    			$strCheck = 'checked="checked"';
                    		}
                    	?>

					    <label class="checkbox-inline">
					      	<input value="1" type="checkbox" name="check-commercial" 
                                   style="margin: -5px -27px;" <?php echo $strCheck ?>>
                            <label> Commercial Check</label>
					    </label>

					    <?php
                    		$strCheck = '';

                    		if ($paymentMode[2] == "1") {
                    			$strCheck = 'checked="checked"';
                    		}
                    	?>

					    <label class="checkbox-inline">
					      	<input value="1" type="checkbox" name="check-ada" 
                                   style="margin: -5px -27px;" <?php echo $strCheck ?>> 
                            <label> ADA</label>
					    </label>

					    <?php
                    		$strCheck = '';

                    		if ($paymentMode[3] == "1") {
                    			$strCheck = 'checked="checked"';
                    		}
                    	?>

					    <label class="checkbox-inline">
					      	<input value="1" type="checkbox" name="check-others" 
                                   style="margin: -5px -27px;" <?php echo $strCheck ?>> 
                            <label> Others (Please Specify)</label>
					    </label>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px; padding-bottom: 15px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtPayee">
                            <strong>Payee: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtPayee" type="text" id="txtPayee" 
                        	   value="<?php echo $payee ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtAddress">
                            <strong>Address: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtAddress" type="text" id="txtAddress" 
                        	   value="<?php echo $address ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <table class="table table-bordered table-hover">
                <tr>
                    <th width="45%" style="text-align: center;">Particulars <font color="#FF0000">*</font></td>
                    <th width="15%"  style="text-align: center;">Responsibility Center</td>
                    <th width="15%"  style="text-align: center;">MFO/PAP</td>
                    <th width="25%"  style="text-align: center;">Amount</td>
                </tr>
                <tr>
                    
                    <td>
                        <textarea class="required form-control font-color-1" name="txtParticulars" id="txtParticulars" 
                            cols="30" rows="5" style="resize: none;"><?php echo $particulars ?></textarea>
                    </td>
                    <td>
                    	<textarea class="form-control font-color-1" name="" id="" 
                            cols="30" rows="5" style="resize: none;" disabled="disabled"></textarea>
                    </td>
                    <td>
                    	<br>
                    	a. A.III.b.1 <br>
                        b. A.III.c.1 <br>
                        c. A.III.c.2 <br>
                    </td>
                    <td>   
                        <input class="form-control font-color-1" name="txtAmount" type="text" id="txtAmount" 
                                value="<?php echo $amount ?>" disabled="disabled">
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="4">
            <div class="row">
                <div class="col-md-12" style="text-align: left;">
                	<p>
	                    <strong> A. </strong>
	                    Certified:  Expenses/Cash Advance necessary,  lawful and  incurred under my direct supervision.<br><br>
	                    <label for="selSignatory">Printed Name, Designation and Signature of Supervisor</label>
	                    <select name="selSignatory" id="selSignatory" class="form-control" disabled="disabled">
	                        <?php
	                            $qryEmps = $conn->query("SELECT * FROM tblsignatories 
                                                     WHERE active = 'yes' 
                                                     AND dv = 'y' 
                                                     ORDER BY name ASC") 
                                                 or die(mysqli_error($conn));

	                            while($data = $qryEmps->fetch_object()){
                                    echo '<option value="'.$data->signatoryID.'"';
                                    echo $data->signatoryID == $signatory ? ' selected="selected"':'';
                                    echo '>'.$data->name.' ['.$data->position.']</option>';
	                            }
	                        ?>
	                    </select>
                    </p>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
</table>

<?php
    unset($_POST["orsID"]);
?>