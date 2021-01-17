<?php

include_once("session.php");
$dir = $_SERVER['DOCUMENT_ROOT'] . "/pis/";
include_once("../layout/main_layout.php");	
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
//if not login
if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	//add record
	$empID = "";
	$firstname = "";
	$middlename = "";
	$lastname = "";
	$position = "";
	$username = "";
	$password = "";
	$user_type = "";
	$sectionID = "";
	
	if (isset($_POST['btnSubmit'])) {
		$allowed_tags = "";

		while (list($key, $val) = each($_POST)) {
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}

		$firstname = trim($_POST['txtFname']);
		$middlename = trim($_POST['txtMname']);
		$lastname = trim($_POST['txtLname']);
		$position = trim($_POST['txtPosition']);
		$sectionID = $_POST['selSection'];
		$ok = 1;

		if (!empty($_POST['txtUsername'])) {		
			$username = $_POST['txtUsername'];

			$checkUname = $conn->query("SELECT empID 
										FROM tblemp_accounts 
										WHERE username = '".$username."' 
										AND empID <> '".$_SESSION['log_empID']."' LIMIT 1");
			if(mysqli_num_rows($checkUname)<1){
				$tbl = new db_operation();
				$tbl -> initialize('tblemp_accounts');
				//$tbl -> update(compact('username'),"empID='".$_SESSION['log_empID']."'",$conn);				
				$tbl -> update(compact('sectionID','username','firstname','middlename','lastname','position'),"empID='".$_SESSION['log_empID']."'",$conn);
			}else{
				$result = "Error occured. Username is in used. Use different username.";
				$ok = 0;
			}
		}

		if (isset($_POST['txtPass']) && !empty($_POST['txtPass']) && $ok == 1) {		
			$password = md5($_POST['txtPass']);
			$tbl = new db_operation();
			$tbl -> initialize('tblemp_accounts');
			$tbl -> update(compact('password'),"empID='".$_SESSION['log_empID']."'",$conn);			
			
			if (mysqli_affected_rows($conn) != -1) {
				$result = "User info has been updated.";					
			} else {
				$result = "Error occured. Password has not been updated.";
			}				
		}//end 

		if ($ok) {
			$uploadDirectory = $dir . "uploads/e-signature/" . $_SESSION['log_empID'] . "/";

			if (!is_dir($uploadDirectory)) {
			    mkdir($uploadDirectory, 0700);
			}

			if (!$_FILES['fleSign']['error']) {
				$path = $_FILES['fleSign']['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$fileName = "esig-" . $_SESSION['log_empID'] . "." . $ext;
				$validFile = true;

				if ($_FILES['fleSign']['size'] > (8388608)) {
					$validFile = false;
					$errMsg = "Error: File image is too large.";
				}

				switch ($ext) {
					case 'gif':
						$validFile = true;
						break;
					case 'png':
						$validFile = true;
						break;
					case 'jpg':
						$validFile = true;
						break;
					default:
						$errMsg = "Invalid picture format! Choose only jpg, png, and gif.";
						$validFile = false;
						break;
				}

				//if the file has passed the test
				if($validFile) {
					$conn->query("UPDATE tblemp_accounts 
								  SET signature = '" . $fileName . "' 
								  WHERE empID = '" . $_SESSION['log_empID'] . "'") 
								  or die(mysql_error($conn));

					//move it to where we want it to be
					$isUploaded = move_uploaded_file($_FILES['fleSign']['tmp_name'], $uploadDirectory . $fileName);

					if (!$isUploaded) {
						$result = 'There is an error uploading your e-signature.';
					}
				}
			} else {
				$result = 'There is an error uploading your e-signature.';
			}
		}
	}

	//update
	if (isset($_SESSION['log_empID']) && !empty($_SESSION['log_empID'])) {
		$selected = $_SESSION['log_empID'];				
		$qryEdit = $conn->query("SELECT * FROM  tblemp_accounts WHERE empID='".$selected."'");

		if (mysqli_num_rows($qryEdit)) {
			$data = mysqli_fetch_object($qryEdit);
			$firstname = $data->firstname;
			$middlename = $data->middlename;
			$lastname = $data->lastname;
			$empID = $data->empID;
			$username = $data->username;
			$sectionID = $data->sectionID;
			$position = $data->position;
			$acid = $data->empID;
			$sign = $data->signature;
		} else {
			$result = "No Record for ".$_REQUEST['edit'];
		}
	}
	//end update

	start_layout("DOST-CAR Procurement System","My Account");
?>

<div id="action">
	<?php
	$location = 'access.php';

	if (isset($_POST['loc'])) {
		$location = $_POST['loc'] . '.php';
	}
	
	?>

	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="<?php echo $location ?>">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-6" style="padding: 0px;"></div>
		<div class="col-md-3" style="padding: 0px;"></div>
	</div>
</div>

<form method="post" enctype="multipart/form-data" name="frmAdd" id="frmAdd" 
	  onSubmit="return check_input(this);">
	<div class="panel panel-default" style="border: 2px solid #005e7c;">
		<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
			<label class="font-color-2"> User Information </label>
		</div>
		<div class="panel-body">
			<div class="form-horizontal font-color-1">
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtEID">
					  		Employee ID
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtEID" type="text" 
		        				   id="txtEID" value="<?php echo $empID ?>" disabled="disabled">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtFname">
					  		Firstname
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtFname" type="text" 
		        				   id="txtFname" value="<?php echo $firstname ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtMname">
					  		Middlename
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtMname" type="text" 
		        				   id="txtMname" value="<?php echo $middlename ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtLname">
					  		Lastname
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtLname" type="text" 
		        				   id="txtLname" value="<?php echo $lastname ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtPosition">
					  		Position
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtPosition" type="text" 
		        				   id="txtPosition" value="<?php echo $position ?>">
		        		</div>
					</div>
					<div class="form-group" hidden="hidden">
					  	<label class="control-label col-md-4" for="selSection">
					  		Division
					  	</label>
					  	<div class="col-md-5">
		        			<select class="form-control font-color-1" name="selSection" id="selSection" hidden="hidden">
					            <?php

								$qrySection = $conn->query("SELECT * FROM tblsections");
								echo '<option value="">&nbsp;</option>';

								while ($data = $qrySection->fetch_object()){
									if ($sectionID == $data->sectionID) {
										$sectionName = $data->section;
									}
									
									echo '<option value="'.$data->sectionID.'"';
									echo $sectionID==$data->sectionID?' selected="selected">':'>';
									echo $data->section."</option>";
								}

								?>
					        </select>
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="sel-Section">
					  		Division
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="sel-Section" type="text" 
		        				   id="sel-Section" disabled="disabled"
		        				   value="<?php if (isset($sectionName)) echo $sectionName ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtUsername">
					  		Username
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtUsername" type="text" 
		        				   id="txtUsername" value="<?php echo $username ?>">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtPass">
					  		New Password
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control" name="txtPass" type="password" id="txtPass">
		        		</div>
					</div>
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtPass2">
					  		Confirm Password
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control" name="txtPass2" type="password" id="txtPass2">
		        		</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="fleSign">
					  		E-Signature
					  	</label>
					  	<div class="col-md-5">
					  		<?php if (empty($sign)){ ?>
								<div class="input-group">
					                <label class="input-group-btn">
					                    <span class="btn btn-primary">
					                        Browse&hellip; <input type="file" style="display: none;" 
					                        					  name="fleSign" id="fleSign" 
										    					  accept=".jpg,.png,.gif">
					                    </span>
					                </label>
					                <input id="text-file" type="text" class="form-control" readonly>
					            </div>
				        	<?php } else { ?>
				        		<div class="input-group" style="display: none;">
					                <label class="input-group-btn">
					                    <span class="btn btn-primary">
					                        Browse&hellip; <input type="file" style="display: none;" 
					                        					  name="fleSign" id="fleSign" 
										    					  accept=".jpg,.png,.gif">
					                    </span>
					                </label>
					                <input id="text-file" type="text" class="form-control" readonly>
					            </div>

				        		<div class="col-md-4 col-xs-5" style="border: 2px #2b3f50 solid; border-radius: 1em;">
				        			<img src="<?php echo '../../../uploads/e-signature/' . $_SESSION['log_empID'] . '/' .$sign ?>" 
				        				 width="100%" height="100%" alt="signature">
				        		</div>
				        		<div class="col-md-1 col-xs-5"">
				        			<button class="btn btn-danger btn-sm" onclick="return false;">
				        				<span class="glyphicon glyphicon-remove"></span>
				        				Delete
				        			</button>
				        		</div>
				        	</div>
				        	<?php } ?>
				        </div>
					</div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-5">
	    					<?php 
	    					
	    					?>
	    				</div>
					</div>
				</div>
				<div class="form-group"> 
				    <div class="col-md-12" style="margin-top: 17px;">
				      	<input type="submit" name="btnSubmit" id="btnSubmit" 
				      		   class="btn btn-primary" value="Submit">
      	    			<input type="reset" name="btnReset" id="btnCancel" value="Cancel" 
      	    				   class="btn btn-danger" onClick="javascript: location.replace('access.php');"> 
				        
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
	end_layout("user");
} else {
	header("Location:  " . $dir . "index.php");
}
?>