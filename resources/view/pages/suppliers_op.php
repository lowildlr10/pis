<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {

	/*
	//add record
	if (isset($_POST['txtCompany']) && !empty($_POST['txtCompany'])) {
		$allowed_tags = "";

		while (list($key, $val) = each($_POST)) {
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}

		$company_name = trim($_POST['txtCompany']);
		$address = trim($_POST['txtAddress']);
		$contact_person = trim($_POST['txtContact']);
		$contact_no = trim($_POST['txtContactNo']);
		$oldname = $_POST['txtOldName'];
		$classID = $_POST['selClass'];
		$ok = 1;

		if ($oldname != $company_name) {
			$checkExists = $conn->query("SELECT bidderID 
										 FROM tblbidders 
										 WHERE company_name = '$company_name'");

			if (mysqli_num_rows($checkExists)) {
				$result = "Bidder already exists.";
				$ok = 0;
			}
		}

		if ($ok) {
			$table = new db_operation();
			$table->initialize('tblbidders');

			if (!isset($_POST['edit'])) {
				$action_taken = "Added";
				$conn->query("INSERT INTO tblbidders (company_name, address, contact_person, 
													  contact_no, classID)
						  	  VALUES ('$company_name', '$address', '$contact_person', 
						  	          '$contact_no', '$classID')");
			} else {
				$action_taken = "Updated";
				$query = "UPDATE tblbidders 
						  SET company_name = '" . $company_name . "', 
							  address = '" . $address . "', 
							  contact_person = '" . $contact_person . "', 
							  contact_no = '" . $contact_no . "', 
							  classID = '" . $classID . "' 
						  WHERE bidderID = " . $_POST['edit'];
				$conn->query($query);
			}

			if (mysqli_affected_rows($conn) != -1) {
				$result = "Bidder has been ".$action_taken.".";
			} else {
				$result = "Error occured. Bidder has not been ".$action_taken.".";
			}	
		}//end check if exists
	}//end add*/

	//update
	$bidderID = "";
	$fileDate = "";
	$mobileNumber = "";
	$establishedDate = "";
	$emailAddress = "";
	$urlAddress = "";
	$faxNo = "";
	$vatNo = "";
	$nameBank = "";
	$accountName = "";
	$accountNo = "";
	$natureBusiness = "";
	$natureBusinessOthers = "";
	$deliveryVehicleNo = "";
	$productLines = "";
	$creditAccomodation = "";
	$attachement = "";
	$attachmentOthers = "";
	$oldname = "";
	$address = "";
	$classID = "";
	$contact_person = "";
	$contact_no = "";
	
	$action = "Add Supplier";

	if (isset($_REQUEST['edit'])) {		
		$bidderID = $_REQUEST['edit'];
		$qryEdit = $conn->query("SELECT * 
								 FROM tblbidders 
								 WHERE bidderID='".$bidderID."'");

		if (mysqli_num_rows($qryEdit)) {
			$data = $qryEdit->fetch_object();
			$fileDate = $data->fileDate;
			$mobileNumber = $data->mobileNumber;
			$establishedDate = $data->establishedDate;
			$emailAddress = $data->emailAddress;
			$urlAddress = $data->urlAddress;
			$faxNo = $data->faxNo;
			$vatNo = $data->vatNo;
			$nameBank = $data->nameBank;
			$accountName = $data->accountName;
			$accountNo = $data->accountNo;
			$natureBusiness = $data->natureBusiness;
			$natureBusinessOthers = $data->natureBusinessOthers;
			$deliveryVehicleNo = $data->deliveryVehicleNo;
			$productLines = $data->productLines;
			$creditAccomodation = $data->creditAccomodation;
			$attachement = $data->attachement;
			$attachmentOthers = $data->attachmentOthers;
			$oldname = $data->company_name;
			$address = $data->address;
			$classID = $data->classID;
			$contact_person = $data->contact_person;
			$contact_no = $data->contact_no;
			$editThis = $_REQUEST['edit'];
			$action = "Update Supplier";
		}
	}
	//end update
	start_layout("DOST-CAR Procurement System: Bidders","" . $action . "","");
?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="suppliers.php?limit=<?php echo $_SESSION['curPage'] ?>">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-9" style="padding: 0px;"></div>
	</div>
</div>

<?php
	if(isset($result)){
		echo '<div class="msg well">'.$result.'</div>';
		unset($result);
	}
?>

<div class="panel panel-default" style="border: 2px solid #005e7c;">
	<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
		<label class="font-color-2"> Supplier Information </label>
	</div>
	<div class="panel-body">
		<form method="post" name="frmAdd" id="frmAdd">
			<div class="form-horizontal font-color-1">
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtDate">
					  		Date<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1 required" name="txtDate" type="text" 
		        				   id="txtDate" value="<?php echo $fileDate ?>">
		        			<input type="hidden" name="bidderID" id="bidderID" value="<?php echo $bidderID ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtCompany">
					  		Name of Company<font color="#FF0000"> *</font>
					  	</label>
					  	<input type="hidden" value="<?php echo $oldname ?>" name="txtOldName">
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1 required" name="txtCompany" 
		        				   type="text" id="txtCompany" value="<?php echo $oldname ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="selClass">
					  		Classification<font color="#FF0000"> *</font>
					  	</label>
					  	<input type="hidden" value="<?php echo $oldname ?>" name="txtOldName">
					  	<div class="col-md-5">
		        			<select class="form-control font-color-1 required" name="selClass" id="selClass">
						        <?php
									$qrySection = $conn->query("SELECT * FROM tblclassifications");
									echo '<option value="">&nbsp;</option>';
									while($data = $qrySection->fetch_object()){
										echo '<option value="'.$data->classID.'"';
										echo $classID==$data->classID?' selected="selected">':'>';
										echo $data->classification."</option>";
									}
								?>
					        </select>
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtAddress">
					  		Business Address<font color="#FF0000"> *</font>
					  	</label>
					  	<input type="hidden" value="<?php echo $oldname ?>" name="txtOldName">
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1 required" name="txtAddress" type="text" 
		        				   id="txtAddress" value="<?php echo $address ?>">
		        		</div>
					</div>
					


					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtEmail">
					  		Email Address
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtEmail" type="text" 
		        				   id="txtEmail" value="<?php echo $emailAddress ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtUrlAddress">
					  		URL Address
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtUrlAddress" type="text" 
		        				   id="txtUrlAddress" value="<?php echo $urlAddress ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtContact">
					  		Contact Person
					  	</label>
					  	<input type="hidden" value="<?php echo $oldname ?>" name="txtOldName">
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtContact" type="text" 
		        				   id="txtContact" value="<?php echo $contact_person ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtContactNo">
					  		Telephone Number/s
					  	</label>
					  	<input type="hidden" value="<?php echo $oldname ?>" name="txtOldName">
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtContactNo" type="text" 
		        				   id="txtContactNo" value="<?php echo $contact_no ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtFaxNumber">
					  		Fax Number
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtFaxNumber" type="text" 
		        				   id="txtFaxNumber" value="<?php echo $faxNo ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtMobileNo">
					  		Mobile Phone Number
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtMobileNo" type="text" 
		        				   id="txtMobileNo" value="<?php echo $mobileNumber ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtDateEstablished">
					  		Date Established
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtDateEstablished" type="text" 
		        				   id="txtDateEstablished" value="<?php echo $establishedDate ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtVatNumber">
					  		VAT Number
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtVatNumber" type="text" 
		        				   id="txtVatNumber" value="<?php echo $vatNo ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtNameBank">
					  		Name of Bank
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtNameBank" type="text" 
		        				   id="txtNameBank" value="<?php echo $nameBank ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtAccountName">
					  		Account Name
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtAccountName" type="text" 
		        				   id="txtAccountName" value="<?php echo $accountName ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtAccountNumber">
					  		Account Number
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtAccountNumber" type="text" 
		        				   id="txtAccountNumber" value="<?php echo $accountNo ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtNatureBusiness">
					  		Nature of Business
					  	</label>
					  	<div class="col-md-5">
					  		<select class="form-control font-color-1" id="txtNatureBusiness" name="txtNatureBusiness">
					  			<option> -- Select Nature of Business -- </option>
					  			<?php 
					  			$nature = array("Manufacturer", "Trading Firm", "Service Contractor", 
					  								 "Others: (pls. specify)");

					  			foreach ($nature as $value) {
					  				echo '<option value="' . $value . '"';
									echo $value == $natureBusiness ? ' selected="selected"':'';
									echo '>'.$value.'</option>';
					  			}

					  			?>
					  		</select>
		        		</div>
					</div>
					<div class="form-group" hidden="hidden" id="group-business">
					  	<label class="control-label col-md-4" for="txtNatureBusinessOthers">
					  		--- Others
					  	</label>
					  	<div class="col-md-5">
					  		<input type="text" name="txtNatureBusinessOthers" class="form-control font-color-1"
					  			   id="txtNatureBusinessOthers" value="<?php echo $natureBusinessOthers ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtNoDeliveryVehicles">
					  		No. of Delivery Vehicles
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtNoDeliveryVehicles" type="text" 
		        				   id="txtNoDeliveryVehicles" value="<?php echo $deliveryVehicleNo ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtProductLines">
					  		Product Lines
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtProductLines" type="text" 
		        				   id="txtProductLines" value="<?php echo $productLines ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtCreditAccomodation">
					  		Credit Accomodation to DOST-CAR
					  	</label>
					  	<div class="col-md-5">
					  		<select class="form-control font-color-1" name="txtCreditAccomodation" id="txtCreditAccomodation">
					  			<option> -- Select Credit Accomodation to DOST-CAR -- </option>
					  			<?php 
					  			$creditAccom = array("91-DAYS AND ABOVE", "90-DAYS", "60-DAYS", 
					  								 "30-DAYS", "90-DAYS AND BELOW");

					  			foreach ($creditAccom as $value1) {
					  				echo '<option value="' . $value1 . '"';
									echo $value1 == $creditAccomodation ? ' selected="selected"':'';
									echo '>'.$value1.'</option>';
					  			}

					  			?>
					  		</select>
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtAttachment">
					  		Attachment
					  	</label>
					  	<div class="col-md-5">
		        			<select class="form-control font-color-1" id="txtAttachment" name="txtAttachment">
					  			<option> -- Select Attachment -- </option>
					  			<?php 
					  			$attach = array("Latest Financial Statement", "DTI/SEC Registration", 
					  							"Valid and Current Mayor's Permit/Municipal License", 
					  							"VAT Registration Certificate", 
					  							"Articles of Incorporation, Partnership or Cooperation, Valid joint venture Agreement whichever is applicable", 
					  							"Certificate of PhilGEPS Registration", "Others, Specify");

					  			foreach ($attach as $value2) {
					  				echo '<option value="' . $value2 . '"';
									echo $value2 == $attachement ? ' selected="selected"':'';
									echo '>'.$value2.'</option>';
					  			}

					  			?>
					  		</select>
		        		</div>
					</div>
					<div class="form-group" hidden="hidden" id="group-attachment">
					  	<label class="control-label col-md-4" for="txtAttachmentOthers">
					  		--- Others
					  	</label>
					  	<div class="col-md-5">
					  		<input type="text" name="txtAttachmentOthers" class="form-control font-color-1"
					  			   id="txtAttachmentOthers" value="<?php echo $attachmentOthers ?>">
		        		</div>
					</div>
				</div>
				
				<div class="form-group"> 
				    <div class="col-md-12">
				      	<input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" 
				      		   onclick="return false;" value="Submit">
			        	<input type="reset" name="btnReset" id="btnCancel" class="btn btn-danger" value="Cancel" onClick="javascript: location.replace('suppliers.php');">
				        
				        <?php
							if (isset($editThis)) {
								echo '<input type="hidden" value="'.$editThis.'" name="edit">';
							}
						?>
				    </div>
				</div>
			</div>
		</form>
	</div>
</div>


<?php
	end_layout("suppliers");
} else {
	header("Location:  " . $dir . "index.php");
}
?>