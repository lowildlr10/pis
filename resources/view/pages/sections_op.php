<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
	//add record
	if(isset($_POST['txtSection']) && !empty($_POST['txtSection'])){
		$allowed_tags = "";
		while(list($key, $val) = each($_POST)){
			$_POST["".$key.""] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($val,$allowed_tags)) : strip_tags($val,$allowed_tags);
		}
		$section = trim($_POST['txtSection']);
		$section_code = trim($_POST['txtSectionCode']);
		$checkExists = $conn->query("SELECT sectionID FROM tblsections WHERE section='".$section."' OR section_code ='".$section_code."'");
		if(mysqli_num_rows($checkExists)){
			$result = "Section already exists.";
		}else{
		$table = new db_operation();
		$table -> initialize('tblsections');
			if(!isset($_POST['edit'])){
				$action_taken = "Added";
				$table -> insert(compact('section','section_code'),$conn);				
			}else{
				$action_taken = "Updated";
				$table -> update(compact('section','section_code'),"sectionID=".$_POST['edit']."",$conn);
			}
			if(mysqli_affected_rows($conn) != -1){
					$result = "Section has been ".$action_taken.".";
				}else{
					$result = "Error occured. section has not been ".$action_taken.".";
				}	
		}//end check if exists
	}//end add
	//update
		$section="";
		$action="Add section";
		$scode = "";
		if(isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])){		
		$qryEdit = $conn->query("SELECT * FROM tblsections WHERE sectionID='".$_REQUEST['edit']."'");
		if(mysqli_num_rows($qryEdit)){
			$data = $qryEdit->fetch_object();
			$section = $data->section;
			$scode = $data->section_code;
			$editThis = $_REQUEST['edit'];
			$action = "Update section";
		}else{
			$result="No Record for ".$_REQUEST['edit'];
		}
	}
	//end update
start_layout("DOST-CAR Procurement System: Item section","".$action."","");
?>
<script language="javascript">
	function check_input(frm){
		if(frm.txtSection.value=="" || frm.txtSection.value == " "){
			alert("Section is required.");
			frm.txtSection.focus();
			return false;
		}else if(frm.txtSectionCode.value=="" || frm.txtSectionCode.value== " "){
			alert("Setion Code is required.");
			frm.txtSectionCode.focus();
			return false;
		}else{
			return true;
		}
	}	
</script>
<br />
  <div align="left" style="padding-left: 10px;"><a href="sections.php?limit=<?php echo $_SESSION['curPage'] ?>"  class="operation" >&lt;&lt;Back</a></div>
<form method="post" name="frmAdd" id="frmAdd" onSubmit="return check_input(this);">
  <table border="0" cellspacing="4" cellpadding="4" id="tblStyle" width="50%" align="center">
     <tr>
       <th colspan="2"> Item Section</th>
     </tr>
	  <?php if(isset($result)){ ?>
      <tr><td colspan="2"><div class="msg"><?php echo $result ?></div></td></tr>
      <?php } ?>
      <tr>
        <td width="40%" align="right">Section:</td>
        <td width="60%" align="left"><input name="txtSection" type="text" id="txtSection" size="40" value="<?php echo $section ?>" /></td>
    </tr>
      
      <tr>
        <td align="right">Section Code:</td>
        <td align="left"><input name="txtSectionCode" type="text" id="txtSectionCode" size="40" value="<?php echo $scode ?>" /></td>
      </tr>
      <tr>
        <td align="right">&nbsp;</td>
        <td align="left"><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit">
        <input type="reset" name="btnReset" id="btnCancel" value="Cancel" onClick="javascript: location.replace('sections.php');">
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