<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
	//add record
	if(isset($_POST['txtSigna']) && !empty($_POST['txtSigna'])){
		$allowed_tags = "";

		while(list($key, $val) = each($_POST)){
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}

		$unitName = trim($_POST['txtSigna']);
		$checkExists = $conn->query("SELECT id 
									 FROM tblunit_issue 
									 WHERE unitName = '$unitName'");

		if (mysqli_num_rows($checkExists)) {
			$result = "Unit already exists.";
		} else {
			$table = new db_operation();
			$table -> initialize('tblunit_issue');

			if (!isset($_POST['edit'])) {
				$action_taken = "Added";
				$table->insert(compact('unitName'),$conn);				
			} else {
				$action_taken = "Updated";
				$table -> update(compact('unitName'),"id=".$_POST['edit']."",$conn);
			}

			if (mysqli_affected_rows($conn) != -1) {
				$result = "Unit name has been ".$action_taken.".";
			} else {
				$result = "Error occured. unit has not been ".$action_taken.".";
			}	
		}//end check if exists
	}//end add

	//update
	$unitName = "";
	$action = "Add unit";

	if (isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])) {		
		$qryEdit = $conn->query("SELECT * 
								 FROM tblunit_issue 
								 WHERE id='".$_REQUEST['edit']."'");
		
		if (mysqli_num_rows($qryEdit)) {
			$data = $qryEdit->fetch_object();
			$unitName = $data->unitName;
			$editThis = $_REQUEST['edit'];
			$action = "Update unit";
		} else {
			$result = "No Record for ".$_REQUEST['edit'];
		}
	}
	//end update

	start_layout("DOST-CAR Procurement System: Items Unit of Issue","Add Unit of Issue","");
?>	

	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="unit_issue.php?limit=<?php echo $_SESSION['curPage'] ?>">
						&lt;&lt;Back
					</a>
				</div>
			</div>

			<div class="col-md-6" style="padding: 0px;"></div>
			<div class="col-md-3" style="padding: 0px;"></div>
		</div>
	</div>

<?php
	if (isset($result)) {
		echo '<div class="msg well">' . $result . '</div>';
	}
?>
	
<form method="POST" name="frmAdd" id="frmAdd" onSubmit="javascript: check_input(this);">
	<div class="panel panel-default" style="border: 2px solid #005e7c;">
		<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
			<label class="font-color-2"> Unit </label>
		</div>
		<div class="panel-body">
			<div class="form-horizontal font-color-1">
				<div style="text-align: left;">
					<div class="form-group">
					  	<label class="control-label col-md-4" for="txtUnitName">
					  		Unit Name<font color="#FF0000"> *</font>
					  	</label>
					  	<div class="col-md-5">
		        			<input class="form-control font-color-1" name="txtSigna" 
		        				   type="text" id="txtSigna" value="<?php echo $unitName ?>">
		        		</div>
					</div>
				</div>
				
				<div class="form-group"> 
				    <div class="col-md-12">
				      	<input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="Submit">
			        	<input type="reset" name="btnReset" id="btnCancel" class="btn btn-danger" 
			        		   value="Cancel" onClick="javascript: location.replace('unit_issue.php');">
				        
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

<!--
<form method="POST" name="frmAdd" id="frmAdd" onSubmit="return check_input(this);">
	<table border="0" cellspacing="4" cellpadding="4" id="tblStyle" width="50%" align="center">
	    <tr>
	      	<th colspan="2"> Unit of Issue</th>
	    </tr>
		  	<?php if(isset($result)){ ?>
	    <tr>
	    	<td colspan="2">
	    		<div class="msg"><?php echo $result ?></div>
	    	</td>
	   	</tr>
	    	<?php } ?>
	    <tr>
	        <td width="40%" align="right">Unit of Issue :</td>
	        <td width="60%" align="left">
	        	<input name="txtSigna" type="text" id="txtSigna" size="40" 
	        		   value="<?php echo $unit ?>" class="form-control">
	        </td>
	    </tr>
	      
	    <tr>
	        <td align="right">&nbsp;</td>
	        <td align="left">
	        	<input type="submit" name="btnSubmit" id="btnSubmit" value="Submit">
	        	<input type="reset" name="btnReset" id="btnCancel" value="Cancel" 
	        		   onClick="javascript: location.replace('unit_issue.php');">
		        <?php
				if (isset($editThis))
					echo '<input type="hidden" value="'.$editThis.'" name="edit" />';
				?>
			</td>
	    </tr>
	</table>
</form>
!-->

<script type="text/javascript">
	function check_input(frm) {
		if (frm.txtPos.value == "" || frm.Abrv.value == " ") {
			alert("Please enter an abbreviaton for the school.");
			frm.txtPos.focus();
			return false;
		} else if (frm.txtSigna.value == "" || frm.txtSigna.value == " ") {
			alert("Please enter the name of the school.");
			frm.txtSigna.focus();
			return false;
		} else {
			return true;
		}
	}	
</script>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>