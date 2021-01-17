<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
	
if (isset($_SESSION['log_admin'])) {
	start_layout("DOST-CAR Procurement System","Add User","");
	//add record
	$signatureDir = $dir . "resources/assets/images/signatures/";
	$empID = "";
	$firstname = "";
	$middlename = "";
	$lastname = "";
	$position = "";
	$username = "";
	$password = "";
	$user_type = "";
	$sectionID = "";
	$signature = "";

	//update
	if (isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])) {
		$selected = $_REQUEST['edit'];

		if ($_REQUEST['edit'] != $empID && $empID != "") {
			$selected = $empID;
		}	

		$qryEdit = $conn->query("SELECT * FROM tblemp_accounts WHERE empID='".$selected."'");

		if (mysqli_num_rows($qryEdit)) {
			$data = mysqli_fetch_object($qryEdit);
			$firstname = $data->firstname;
			$middlename = $data->middlename;
			$lastname = $data->lastname;
			$empID = $data->empID;
			$user_type = $data->user_type;
			$sectionID = $data->sectionID;
			$position = $data->position;
			$signature = $data->signature;
		} else {
			$result = "No Record for ".$_REQUEST['edit'];
		}
	}

	if (isset($_POST['txtEID']) && isset($_POST['txtFname']) && isset($_POST['txtMname']) &&
		isset($_POST['txtLname']) && isset($_POST['txtPosition']) && isset($_POST['selAccess']) &&
		isset($_POST['selSection'])) {

		$empID = trim($_POST['txtEID']);
		$firstname = trim($_POST['txtFname']);
		$middlename = trim($_POST['txtMname']);
		$lastname = trim($_POST['txtLname']);
		$position = trim($_POST['txtPosition']);
		$user_type = $_POST['selAccess'];
		$sectionID = $_POST['selSection'];

		if ($_FILES['fleSign']['name']) {
			if(!$_FILES['fleSign']['error']) {
				$path = $_FILES['fleSign']['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);;
				$sigName =  strtolower(preg_replace('/\s+/', '', $firstname) . "_" . $lastname[0]) . "." . $ext;
				$newImageName = strtolower($_FILES['fleSign']['tmp_name']);
				$valid_file = true;

				if($_FILES['fleSign']['size'] > (8000000)) {
					$valid_file = false;
					$result = 'Oops!  Your file\'s size is to large.';
				}

				//if the file has passed the test
				if($valid_file) {
					//move it to where we want it to be
					$isUploaded = move_uploaded_file($_FILES['fleSign']['tmp_name'], $signatureDir . $sigName);
					$signature = $sigName;

					if (!$isUploaded) {
						$result = 'There is an error uploading your e-signature.';
					}
				}
			} else {
				$result = 'There is an error uploading your e-signature.';
			}
		}

		$allowed_tags = "";

		while (list($key, $val) = each($_POST)) {
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}
		
		$blocked='n';
		$ok = 1;
		$selected = "";

		if (isset($_POST['edit'])) {
			$selected = $_POST['edit'];
		}

		if ($selected != $empID && !isset($_REQUEST['edit'])) { 
			$checkExists = $conn->query("SELECT empID FROM tblemp_accounts WHERE empID='".$empID."'");
			
			if (mysqli_num_rows($checkExists)) {
				$result = "Employee ID already exists. Please enter a different Employee ID.";
				$ok = 0;
			}//end check if exists
		}

		if ($ok) {			
			if (!isset($_REQUEST['edit'])) {
				$username = strtolower(trim($_POST['txtLname']));
				$password = md5(strtolower(trim($_POST['txtLname'])));
				$action_taken = "Added";
				$table = new db_operation(); 
				$tbl = new db_operation();
				$tbl -> initialize('tblemp_accounts');
				$tbl -> insert(compact('empID','sectionID','firstname','middlename','lastname','position','username','password','user_type','blocked', 'signature'),$conn);				
			} else {
				$table = new db_operation();
				$table -> initialize('tblemp_accounts');
				$action_taken = "Updated";
				$table -> update(compact('empID','sectionID','firstname','middlename','lastname','position','user_type', 'signature'),"empID='".$empID."'",$conn);
				$tbl = new db_operation();
				$tbl -> initialize('tblemp_accounts');

				if (isset($_POST['chkRestore'])) {
					$username = strtolower(trim($_POST['txtLname']));
					$password = md5(strtolower(trim($_POST['txtLname'])));
					$tbl -> update(compact('username','password'),"empID='".$empID."'",$conn);
				}
			}

			if (mysqli_affected_rows($conn) != -1) {
				$result = "System user has been ".$action_taken.".";
				$ok = 1;					
			} else {
				$result = "Error occured. System user has not been ".$action_taken.". Try again or contact system administrator.";
				$ok = 0;
			}
		}
	}

?>

<script language="javascript">
	function check_input(frm) {
		if (frm.txtEID.value=="" || frm.txtEID.value == " ") {
			alert("Please enter employee ID.");
			frm.txtEID.focus();
			return false;
		} if(frm.txtFname.value=="" || frm.txtFname.value== " ") {
			alert("Please enter the user firstname.");
			frm.txtFname.focus();
			return false;
		} else if (frm.txtMname.value=="" || frm.txtMname.value== " ") {
			alert("Please enter the user middlename.");
			frm.txtMname.focus();
			return false;
		} else if (frm.txtLname.value=="" || frm.txtLname.value== " ") {
			alert("Please enter the user lastname.");
			frm.txtLname.focus();
			return false;
		} else if (frm.selSection.value=="") {
			alert("Please enter the user section.");
			frm.selSection.focus();
			return false;
		} else if (frm.txtPosition.value=="" || frm.txtPosition.value== " ") {
			alert("Please enter the user position.");
			frm.txtPosition.focus();
			return false;
		} else if (frm.selAccess.value=="") {
			alert("Please enter the user access level.");
			frm.selAccess.focus();
			return false;
		} else {
			return true;
		}
	}	
</script>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="system_users.php">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-6" style="padding: 0px;"></div>
		<div class="col-md-3" style="padding: 0px;"></div>
	</div>
</div>

<?php
	if(isset($result)){
		echo '<div class="msg well">'.$result.'</div>';
		unset($result);
	}
?>

<form method="POST" action="#" enctype="multipart/form-data" name="frmAdd" 
	  id="frmAdd" onSubmit="return check_input(this);">
	<div class="panel panel-default" style="border: 2px solid #005e7c;">
		<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
			<label class="font-color-2"> User Information </label>
		</div>
		<div class="panel-body">
			<div class="form-horizontal font-color-1">
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtEID">
					  		Employee ID<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtEID" type="text" id="txtEID" value="<?php echo $empID ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtFname">
					  		Firstname<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtFname" type="text" id="txtFname" value="<?php echo $firstname ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtMname">
					  		Middlename<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtMname" type="text" id="txtMname" value="<?php echo $middlename ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtLname">
					  		Lastname<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtLname" type="text" id="txtLname" value="<?php echo $lastname ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="selSection">
					  		Section<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<select class="form-control font-color-1" name="selSection" id="selSection">
						        <?php
									$qrySection = $conn->query("SELECT * FROM tblsections");
									echo '<option value="">&nbsp;</option>';

									while ($data = $qrySection->fetch_object()) {
										echo '<option value="'.$data->sectionID.'"';
										echo $sectionID == $data->sectionID?' selected="selected">':'>';
										echo $data->section."</option>";
									}
								?>
					        </select>
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtPosition">
					  		Position<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtPosition" type="text" id="txtPosition" value="<?php echo $position ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="selAccess">
					  		Access Level<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<select class="form-control font-color-1" name="selAccess" id="selAccess">
						        <option value="admin"<?php echo $user_type=='admin'?' selected="selected"':'' ?>>Supply Officer</option>
						        <option value="pstd"<?php echo $user_type=='pstd'?' selected="selected"':'' ?>>PSTD</option>
						        <option value="staff"<?php echo $user_type=='staff'?' selected="selected"':'' ?>>Staff</option>
						        <option value="encoder"<?php echo $user_type=='encoder'?' selected="selected"':'' ?>>Encoder</option>
					        </select>
		        		</div>
					</div>
					<div class="form-group">
		        		<label class="control-label col-md-4" for="fleSign">
					  		E-Signature
					  	</label>
					  	<div class="col-md-5">
					  		<div class="form-control">
					  			<input class="form-control-file" type="file" name="fleSign" id="fleSign"
					  			       accept=".jpg,.png,.gif">
	    						<div style="margin-top: 15px; margin-left: 33px; padding: 7px;">
	    							<?php 
	    							if (!empty($signature)) { 
	    								echo '<img src="../../assets/images/signatures/'.$signature.'" width="40%" alt="signature">';
	    							} 
	    							?>
	    						</div>
					  		</div>
		        		</div>
					</div>

					<?php
					if (isset($_REQUEST['edit'])) {
					?>
					<div class="form-group">
						<div class="col-md-4"></div>
					    <div class="col-md-5">
					      	<div class="checkbox">
					      	  	<label class="checkbox-inline">
					      	  		<input name="chkRestore" type="checkbox" id="chkRestore" value="yes"
					      	  			   style="margin-top: -6px;">
					      	  	  	<div style="margin-left: 8px;"> (Check to restore default password and submit form)</div>
					      	  	</label>
					      	</div>
					    </div>
					</div>
					<?php
					}
					?>
				</div>
				<div class="form-group"> 
				    <div class="col-md-12" style="margin-top: 17px;">
				      	<input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="Submit">
			        	<input type="reset" name="btnReset" id="btnCancel" class="btn btn-danger" value="Cancel" onClick="javascript: location.replace('system_users.php');">
				        
				        <?php
							if (isset($editThis)) {
								echo '<input type="hidden" value="'.$editThis.'" name="edit">';
							}
						?>
				    </div>
				 </div>
			</div>
		</div>
	</div>
</form>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>