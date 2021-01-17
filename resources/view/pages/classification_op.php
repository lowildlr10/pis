<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
	//add record
	if(isset($_POST['txtSigna']) && !empty($_POST['txtSigna'])){
		$allowed_tags = "";
		while(list($key, $val) = each($_POST)){
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}
		$classification = trim($_POST['txtSigna']);
		$checkExists = $conn->query("SELECT classID FROM tblclassifications WHERE classification='$classification'");
		if(mysqli_num_rows($checkExists)){
			$result = "Classification already exists.";
		}else{
		$table = new db_operation();
		$table -> initialize('tblclassifications');
			if(!isset($_POST['edit'])){
				$action_taken = "Added";
				$table -> insert(compact('classification'),$conn);				
			}else{
				$action_taken = "Updated";
				$table -> update(compact('classification'),"classID=".$_POST['edit']."",$conn);
			}
			if(mysqli_affected_rows($conn) != -1){
					$result = "Classification has been ".$action_taken.".";
				}else{
					$result = "Error occured. Classification has not been ".$action_taken.".";
				}	
		}//end check if exists
	}//end add
	//update
		$classification="";
		$action="Add Classification";
		if(isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])){		
		$qryEdit = $conn->query("SELECT * FROM tblclassifications WHERE classID='".$_REQUEST['edit']."'");
		if(mysqli_num_rows($qryEdit)){
			$data = $qryEdit->fetch_object();
			$classification = $data->classification;
			$editThis = $_REQUEST['edit'];
			$action = "Update Classification";
		}else{
			$result="No Record for ".$_REQUEST['edit'];
		}
	}
	//end update
start_layout("DOST-CAR Procurement System: Bidders Classification","".$action."","");
?>
<script language="javascript">
	function check_input(frm){
		if(frm.txtSigna.value=="" || frm.txtSigna.value== " "){
			alert("Classification is required.");
			frm.txtSigna.focus();
			return false;
		}else{
			return true;
		}
	}	
</script>
<br />
  <div align="left" style="padding-left: 10px;"><a href="classification.php?limit=<?php echo $_SESSION['curPage'] ?>"  class="operation" >&lt;&lt;Back</a></div>
<form method="post" name="frmAdd" id="frmAdd" onSubmit="return check_input(this);">
  <table border="0" cellspacing="4" cellpadding="4" id="tblStyle" width="50%" align="center">
     <tr>
       <th colspan="2"> Bidder Classification</th>
     </tr>
	  <?php if(isset($result)){ ?>
      <tr><td colspan="2"><div class="msg"><?php echo $result ?></div></td></tr>
      <?php } ?>
      <tr>
        <td width="40%" align="right">Classification :</td>
        <td width="60%" align="left"><input name="txtSigna" type="text" id="txtSigna" size="40" value="<?php echo $classification ?>" /></td>
    </tr>
      
      <tr>
        <td align="right">&nbsp;</td>
        <td align="left"><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit">
        <input type="reset" name="btnReset" id="btnCancel" value="Cancel" onClick="javascript: location.replace('classification.php');">
        <?php
		if(isset($editThis))
			echo '<input type="hidden" value="'.$editThis.'" name="edit" />';
		?>        </td>
      </tr>
    </table>
</form>
<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>