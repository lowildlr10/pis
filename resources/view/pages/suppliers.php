<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='system_libraries.php' style='color: rgb(225, 239, 243);'>System Libraries</a>/".
		         "<a href='suppliers.php' style='color: #98ffe8;'>Suppliers</a>");

	$searchInput = "";

	if (isset($_REQUEST['txtSearch'])) {
		$searchInput = trim($_REQUEST['txtSearch']);
		unset($_REQUEST['txtSearch']);
	}

	if (isset($_REQUEST['result'])) {
		$result = "Successfully saved.";
		unset($_REQUEST['result']);
	}

	if (isset($_POST['delCheck'])) {
		$result = "";
		$ok = '';
		$nope = '';
		$withNope = 0;
		$delete = 0;
		
		while(list(,$val) = each($_POST['delCheck'])){
			$qryWith = $conn->query("SELECT bidderID FROM tblbids_quotations WHERE bidderID='".$val."' LIMIT 1");
			if(mysqli_num_rows($qryWith) < 1){
				$conn->query("DELETE FROM tblbidders WHERE bidderID='".$val."'");
				if(mysqli_affected_rows($conn) != -1){				
					$ok .= $val.' ';
				}
				$delete = 1;
			}else{
				$withNope = 1;
				$nope .= $val.' ';
					
			}
			if($delete){
				$result = 'Selected item(s) has been deleted successfully. - ('.$ok.')';
			}
			if($withNope){
				$result .= "<br />Selected item(s) cannot be deleted since bidder(s) is in used in past abstract forms. - (".$nope.")";
			}
		}
	}

?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="system_libraries.php">&lt;&lt;Back</a>
			</div>
		</div>

		<div class="col-md-5" style="padding: 0px;"></div>
		<div class="col-md-4" style="padding: 0px;">
            <?php
            if (!isset($_SESSION['log_staff'])) {
            ?>

			<div class="btn-group btn-group-justified">
				<a class="btn btn-primary operation" href="suppliers_op.php">Add Bidders</a>
    			<a class="btn btn-default operation" href="javascript: ifCheck();">Delete Bidders</a>
			</div>

            <?php
            }
            ?>
		</div>
	</div>
</div>

<?php
if (isset($result)) {
	echo '<div class="msg">'.$result.'</div>';
	unset($result);
}
?>

<?php
if (!isset($_SESSION['showPerPage'])) {
	$_SESSION['showPerPage'] = 25;
}

if (isset($_POST['txtPerPage'])) {
	$_SESSION['showPerPage'] = $_POST['txtPerPage'];
}

$perPage = $_SESSION['showPerPage'];

if (isset($_REQUEST['limit'])) {
	$accessPage = $_REQUEST['limit'];
	$startlimit = $accessPage * $perPage - $perPage;
	$limit = $startlimit.",".$perPage;	
} else {
	$limit = "0,".$perPage;
	$accessPage = 1;					
}
	
$_SESSION['curPage'] = $accessPage;	

?>

<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
	<form name="frmSign" method="post" action="#">
		<table class="table table-responsive" id="tblStyle">
			<tr>
				<th>
				  	<div class="col-xs-12 col-md-12" style="padding: 0px;">
						<div class="col-md-3" style="padding: 0px;"> 
							<strong><label>#List of Suppliers</label></strong>
						</div>
						<div class="col-md-6" style="padding: 0px;">
							&nbsp
						</div>
						<div class="col-md-3" style="padding: 0px;">
							<form method="POST" name="frmSearch" action="#" class="form-inline">
								<div class="form-group" style="text-align: left;">
									<label for="txtSearch">
										<strong class="font-color-2">Search: (Click Enter to Search)</strong> 
									</label>
									<input id="txtSearch" class="form-control" type="text" name="txtSearch" placeholder="Enter a keyword first...">
								</div>
					  		</form>
						</div>
					</div>
			  	</th>
			</tr>
			<tr>
				<td>
					<table class="table table-responsive" id="tblLists">
						<?php

						//display list
						$qry = "SELECT COUNT(bidderID) totalBut ";
						$_qrySign = "SELECT bids.*, class.classification 
									 FROM tblbidders bids 
									 INNER JOIN tblclassifications class 
									 ON bids.classID=class.classID ";

						if (!empty($searchInput)) {
							echo '<label> Searched For: "' . $searchInput . '" </label> 
								   <a class="btn btn-danger btn-sm" 
								    style="padding: 0px 4px 0px 4px;
						    	   		border-radius: 25px; 
						    	   		margin-left: 3px;
						    	   		margin-bottom: 2px;"
						    	     href="suppliers.php">
						    	     Clear
						    	   </a><br><br>';

							$qry = $qry . " FROM tblbidders 
											WHERE (company_name LIKE '%$searchInput%' 
											OR contact_person LIKE '%$searchInput%' 
											OR contact_no LIKE '%$searchInput%') 
											ORDER BY company_name ASC LIMIT $limit";
							$_qrySign = $_qrySign . " WHERE (bids.company_name LIKE '%$searchInput%' 
													  OR bids.contact_person LIKE '%$searchInput%' 
													  OR bids.contact_no LIKE '%$searchInput%') 
													  ORDER BY classID ASC, company_name ASC LIMIT $limit";
						} else {
							$qry = $qry . "FROM tblbidders";
							$_qrySign = $_qrySign . "ORDER BY classID ASC, company_name ASC LIMIT $limit";
						}

						$qrySign = $conn->query($_qrySign);

						if(mysqli_num_rows($qrySign)){
							echo '<tr><th width="5%"><input type="checkbox" value="" name="chAll" onclick="checkAll();" /></th><th width="25%" style="padding-left: 20px; text-align: left;">Company Name</th><th width="65%">Address</th><th>Contact</th><th></th><th></th></tr>';
							$passClass = "";

							while ($data = $qrySign->fetch_object()) {
								if ($passClass != $data->classID) {
									echo '<tr><td colspan="6" id="group">'. strtoupper($data->classification) .'</td></tr>';
									$passClass = $data->classID;
								}

								$allowed_tags = "";
								$companyName = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data->company_name, 
											    $allowed_tags)) : strip_tags($data->company_name, $allowed_tags);

                                echo '<tr id="row_0">';
								echo '
								<td><input type="checkbox" name="delCheck[]" value="'.$data->bidderID.'" id="signa_'.$data->bidderID.'" /></td>
								<td align="left" style="padding-left: 20px;">'.$data->company_name.'</td>
								<td align="left">'.$data->address.'</td>
								<td align="left">'.$data->contact_person.'&nbsp;-&nbsp;'.$data->contact_no.'</td>';

                                if (!isset($_SESSION['log_staff'])) {
                                    echo '
                                    <td><a href="suppliers_op.php?edit='.$data->bidderID.'" title="Edit Bidder"><img class="img-button" src="../../assets/images/edit.png" alt="Edit" /></a></td>
                                    <td><a href="javascript: checkDelete(\''.$companyName.'\',\'signa_'.$data->bidderID.'\');" title="Delete Bidder"><img class="img-button" src="../../assets/images/delete.png" alt="Delete" /></a></td>';
                                } else {
                                    echo '<td><button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled"><img class="img-button" src="../../assets/images/edit.png" alt="Edit"></button></td>';
                                    echo '<td><button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled"><img class="img-button" src="../../assets/images/delete.png" alt="Delete"></button></td>';
                                }

                                echo '</tr>';
							}
						}			
						echo '</table>';
						echo '</td></tr>';
						display_pages($conn,$qry,6,$accessPage,$perPage);
						echo '</table>';
						echo '</form>';

						?>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>

<script language="javascript" type="text/javascript">{
	function ifCheck(){
		for (i=0; i<document.frmSign.elements.length;i++)
			if (document.frmSign.elements[i].checked == true)
				flag = true;
			if (flag == true)
				if(confirm("Are you sure you want to delete all checked?")){
					document.frmSign.submit();
				}
		}
	}

	function checkAll(){
		for (i=0; i<document.frmSign.elements.length;i++){
			if(document.frmSign.chAll.checked == true){			
				document.frmSign.elements[i].checked=1;
			}else{
				document.frmSign.elements[i].checked=0;
			}
		}	
	}

	function checkDelete(school,who){
		if (confirm("Are you sure you want to remove '"+ school +"' from the lists?")){
			document.getElementById(who).checked = 1;
			document.frmSign.submit();
		}
	}
</script>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>