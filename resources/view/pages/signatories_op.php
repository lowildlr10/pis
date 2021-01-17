<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
	//add record
	if ((isset($_POST['txtName']) && !empty($_POST['txtName'])) ||
		(isset($_POST['txtPosition']) && !empty($_POST['txtPosition']))) {

		$allowed_tags = "";

		while (list($key, $val) = each($_POST)) {
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}

		$name = trim(strtoupper($_POST['txtName']));
		$position = trim($_POST['txtPosition']);
		$signType = $_POST['selType'];
		$active = $_POST['selActive'];
		$p_req = "n";
		$rfq = "n";
		$abs = "n";
		$ors = "n";
		$iar = "n";
		$dv = "n";
		$ris = "n";
		$par = "n";
		$ics = "n";

		if (isset($_POST['chk-pr'])) {
			$p_req = 'y';
		}

		if (isset($_POST['chk-rfq'])) {
			$rfq = 'y';
		}

		if (isset($_POST['chk-abs'])) {
			$abs = 'y';
		}

		if (isset($_POST['chk-ors'])) {
			$ors = 'y';
		}

		if (isset($_POST['chk-iar'])) {
			$iar = 'y';
		}

		if (isset($_POST['chk-dv'])) {
			$dv = 'y';
		}

		if (isset($_POST['chk-ris'])) {
			$ris = 'y';
		}

		if (isset($_POST['chk-par'])) {
			$par = 'y';
		}

		if (isset($_POST['chk-ics'])) {
			$ics = 'y';
		}
		
		$table = new db_operation();
		$table -> initialize('tblsignatories');

		if (!isset($_POST['edit'])) {
			$action_taken = "Added";
			$table->insert(compact('name', 'position', 'signType' , 'active',
								   'p_req', 'rfq', 'abs', 'ors', 'iar', 'dv', 'ris', 'par', 'ics'), $conn);	
			$sID = mysqli_insert_id($conn);			
		} else {
			$action_taken = "Updated";

			if (!empty($absOrder)) {
				$conn->query("UPDATE tblsignatories 
							  SET absOrder = 0 
							  WHERE absOrder = '".$absOrder."'");
			}

			$table->update(compact('name', 'position', 'signType' , 'active',
								   'p_req', 'rfq', 'abs', 'ors', 'iar', 'dv', 'ris', 'par', 'ics'),
						   "signatoryID=".$_POST['edit']."", $conn);	

			$sID = $_POST['edit'];
		}

		if (mysqli_affected_rows($conn) != -1) {
			$result = "Signatory has been ".$action_taken.".";
			//$ok = 1;
		} else {
			$result = "Error occured. Signatory has not been ".$action_taken.".";
			//$ok = 0;
		}

	}//end add

	//update
	$name = "";
	$position = "";
	$signatoryType = "";
	$active = "yes";
	$p_req = "n";
	$rfq = "n";
	$abs = "n";
	$ors = "n";
	$iar = "n";
	$dv = "n";
	$ris = "n";
	$par = "n";
	$ics = "n";
	$action = "Add Signatory";

	if (isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])) {		
		$qryEdit = $conn->query("SELECT * FROM tblsignatories WHERE signatoryID='".$_REQUEST['edit']."'");
		
		if (mysqli_num_rows($qryEdit)) {
			$data = $qryEdit->fetch_object();
			$name = $data->name;
			$position = $data->position;
			$signatoryType = $data->signType;
			$active = $data->active;
			$p_req = $data->p_req;
			$rfq = $data->rfq;
			$abs = $data->abs;
			$ors = $data->ors;
			$iar = $data->iar;
			$dv = $data->dv;
			$ris = $data->ris;
			$par = $data->par;
			$ics = $data->ics;
			$editThis = $_REQUEST['edit'];
			$action = "Update Signatory";
		} else {
			$result = "No Record for ".$_REQUEST['edit'];
		}
	}

	start_layout("DOST-CAR Procurement System","Signatories");
?>

<?php
	if (isset($result)) {
		echo '<div class="msg well">' . $result . '</div>';
	}
?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="signatories.php?limit=<?php echo $_SESSION['curPage'] ?>">
					&lt;&lt;Back
				</a>
			</div>
		</div>

		<div class="col-md-6" style="padding: 0px;"></div>
		<div class="col-md-3" style="padding: 0px;"></div>
	</div>
</div>
	
<form method="POST" name="frmAdd" id="frmAdd" onSubmit="javascript: check_input(this);">
	<div class="panel panel-default" style="border: 2px solid #005e7c;">
		<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
			<label class="font-color-2"> Signatory </label>
		</div>
		<div class="panel-body">
			<div class="form-horizontal font-color-1">
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtName">
					  		Full Name<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtName" 
		        				   type="text" id="txtName" value="<?php echo $name ?>">
		        		</div>
					</div>
				</div>
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtPosition">
					  		Position<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtPosition" 
		        				   type="text" id="txtPosition" value="<?php echo $position ?>">
		        		</div>
					</div>
				</div>
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4">
					  		Function<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5" class="table-responsive">
					  		<table class="table table-hover table-bordered">
					  			<tr>
					  				<th width="11.11%"><label for="chk-pr">PR</label></th>
					  				<th width="11.11%"><label for="chk-rfq">RFQ</label></th>
					  				<th width="11.11%"><label for="chk-abs">Abstract</label></th>
					  				<th width="11.11%"><label for="chk-ors">ORS/BURS</label></th>
					  				<th width="11.11%"><label for="chk-iar">IAR</label></th>
					  				<th width="11.11%"><label for="chk-dv">DV</label></th>
					  				<th width="11.11%"><label for="chk-ris">RIS</label></th>
					  				<th width="11.11%"><label for="chk-par">PAR</label></th>
					  				<th width="11.11%"><label for="chk-ics">ICS</label></th>
					  			</tr>
					  			<tr>
					  				<td>
					  					<input id="chk-pr" name="chk-pr" type="checkbox"
					  						   <?php echo $p_req=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-rfq" name="chk-rfq" type="checkbox" 
					  						   <?php echo $rfq=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-abs" name="chk-abs" type="checkbox" 
					  						   <?php echo $abs=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-ors" name="chk-ors" type="checkbox" 
					  						   <?php echo $ors=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-iar" name="chk-iar" type="checkbox" 
					  						   <?php echo $iar=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-dv" name="chk-dv" type="checkbox" 
					  						   <?php echo $dv=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-ris" name="chk-ris" type="checkbox" 
					  						   <?php echo $ris=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-par" name="chk-par" type="checkbox" 
					  						   <?php echo $par=='y'?' checked="checked"':'' ?>>
					  				</td>
					  				<td>
					  					<input id="chk-ics" name="chk-ics" type="checkbox" 
					  						   <?php echo $ics=='y'?' checked="checked"':'' ?>>
					  				</td>
					  			</tr>
					  		</table>
		        		</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-6" style="text-align: left;">
						<strong>FOR ABSTRACT OF BIDS & QUOTATIONS</strong>
					</div>
					<div class="col-md-3"></div>
				</div>
				<br>
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="selType">
					  		Signature for
					  	</label>
					  	<div class="col-md-5">
					  		<strong>
					  			<select name="selType" id="selType" class="form-control font-color-1">
						          	<option value="approval"<?php echo $signatoryType=='approval'?' selected="selected"':'' ?>>
						          		Approval
						          	</option>
						          	<option value="chairman"<?php echo $signatoryType=='chairman'?' selected="selected"':'' ?>>
						          		Chairman
						          	</option>
						          	<option value="vice-chairman"<?php echo $signatoryType=='vice-chairman'?' selected="selected"':'' ?>>
						          		Vice Chairman
						          	</option>
						          	<option value="member"<?php echo $signatoryType=='member'?' selected="selected"':'' ?>>
						          		Member
						          	</option>
						        </select>
					  		</strong>
		        		</div>
					</div>
				</div>
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="selActive">
					  		Active
					  	</label>
					  	<div class="col-md-5">
					  		<strong>
					  			<select name="selActive" id="selActive" class="form-control font-color-1">
						          	<option value="yes"<?php echo $active=='yes'?' selected="selected"':'' ?>>
						          		Yes
						          	</option>
						          	<option value="no"<?php echo $active=='no'?' selected="selected"':'' ?>>
						          		No
						          	</option>
						        </select>
					  		</strong>
		        		</div>
					</div>
				</div>
				
				<div class="form-group"> 
				    <div class="col-md-12">
				      	<input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="Submit">
			        	<input type="reset" name="btnReset" id="btnCancel" class="btn btn-danger" 
			        		   value="Cancel" onClick="javascript: location.replace('signatories.php');">
				        
				        <?php
						if (isset($editThis))
							echo '<input type="hidden" value="'.$editThis.'" name="edit" />';
						?>
				    </div>
				 </div>
			</div>
		</div>
	</div>
</form>

<script language="javascript">
	function check_input(frm) {
		alert();
		if(frm.txtPos.value=="" || frm.Abrv.value == " "){
			alert("Please enter an abbreviaton for the school.");
			frm.txtPos.focus();
			return false;
		} else if (frm.txtSigna.value=="" || frm.txtSigna.value== " "){
			alert("Please enter the name of the school.");
			frm.txtSigna.focus();
			return false;
		} else {
			return true;
		}
	}

	function checkIt(evt) {
		var charCode = (evt.which) ? evt.which : event.keyCode;
		
		if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
		//alert("Please make sure entries are numbers only.")
			return false
		}
		return true
	}	
</script>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>