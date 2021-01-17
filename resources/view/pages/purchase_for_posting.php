<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
		         "<a href='purchase.php' style='color: rgb(225, 239, 243);'>Purchase Request</a>/".
		         "<a href='purchase_for_posting.php' style='color: #98ffe8;'>For Approval</a>");	
	
	$page = "pr";
	$startlimit = 0;
	$fCount = 0;
	$itemCount = 0;
	$searchBy = "";
	$searchInput = "";

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	if (isset($_SESSION['arrBids'])) {
		unset($_SESSION['arrBids']);
	}

	if (isset($_POST['txtSearch'])) {
		$tempStr = str_replace(' ', '', $_POST['txtSearch']);
		$tempStr = strtolower($tempStr);
	}

	if (isset($_GET['result'])) {
		switch ($_GET['result']) {
			case 1:
				$prResult = "New purchase request has been added.";	
				break;
			case 2:
				$prResult = "Purchase request has been updated.";			
				break;
			case 3:
				$prResult = "Error encountered: Purchase Requests has not been processed successfully.";				
				break;
			case 4:
				$prResult = "Request has been saved except no prNo.";
				break;
			case 5:
				$prResult = "Request has been saved except prNo has not been added. Duplicate prNo.";
				break;
		}
	}

	//processed check items
	if (isset($_POST['itemCheck'])) {
		$action = $_POST['operation'];
		$prProc_ok = "";
		$prProc_no = "";

		while (list(,$val) = each($_POST['itemCheck'])) {
			parse_str($val);

			switch ($action){
				case "delete":
					//remove item
					$qry = $conn->query("DELETE FROM tblpr_info 
										 WHERE prID='".$pid."'");
					$qry = $conn->query("DELETE FROM tblpr 
										 WHERE prID='".$pid."'");
					break;
				case "finalized":
					//finalized item for approval			
					$qryCheckItem = $conn->query("SELECT infoID 
												  FROM tblpr_info 
												  WHERE prID='".$pid."' LIMIT 1");

					if (mysqli_num_rows($qryCheckItem)) {
						// Get and set PR Status
						$qryStatus = $conn->query("SELECT statusName 
												   FROM tblpr_status 
												   WHERE id = '5'") 
												   or die(mysqli_error($conn));
						$_prStatus = $qryStatus->fetch_object();
						$prStatus = $_prStatus->statusName;
						$prApprovalDate = date("m/d/Y");

						$tblAccess = new db_operation;
						$tblAccess->initialize("tblpr");
						$tblAccess->update(compact('prStatus', 'prApprovalDate'),"prID='".$pid."'",$conn);	
					} else {
						$prProc_no .= "'".$pr."',";
					}		
					break;
				case "cancel":
					$prStatus = "cancelled";
					$cancelled = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($_POST['reason'], $allowed_tags)) : 
								  strip_tags($_POST['canReason'], $allowed_tags);
					$cancelled.="<br /> Date Cancelled: ".date("m/d/y g:i a");

					if (trim($cancelled)) {
						$tblAccess = new db_operation;
						$tblAccess->initialize("tblpr");
						$tblAccess->update(compact('prStatus','cancelled'),"prID='".$pid."'",$conn);
					}
					break;
				case "approved":
					//finalized item for approval
					$qryStatus = $conn->query("SELECT statusName 
											   FROM tblpr_status 
											   WHERE id = '5'") 
											   or die(mysqli_error($conn));
					$_prStatus = $qryStatus->fetch_object();
					$prStatus = $_prStatus->statusName;
					$prApprovalDate = date("m/d/Y");

					$tblAccess = new db_operation;
					$tblAccess->initialize("tblpr");
					$tblAccess->update(compact('prStatus', 'prApprovalDate'),"prID='".$pid."'",$conn);

					break;
				case "disapproved":
					//finalized item for approval
					$prStatus = "disapproved";
					$tblAccess = new db_operation;
					$tblAccess->initialize("tblpr");
					$tblAccess->update(compact('prStatus'),"prID='".$pid."'",$conn);
					break;
				default:
					break;
			}//end switch

			if (mysqli_affected_rows($conn) != -1) {
				$prProc_ok .= "'".$pr."',";
			} else {
				$prProc_no .= "'".$pr."',";
			}
		}//end while

		$prProc_ok = substr($prProc_ok,0,-1);

		switch ($action) {
			case "delete":
				$prResult = "Purchase Request ".$prProc_ok." has been deleted.";

				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been deleted.";
				}
				break;
			case "finalized":
				$prResult = "Purchase Request ".$prProc_ok." has been finalized.";

				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been finalized. PR may not have item on it or prNo is not set.";
				}
				break;
			case "cancel":
				$prResult = "Purchase Request ".$prProc_ok." has been cancelled.";
				
				if (!empty($prProc_no)) {
					$prProc_no = substr($prProc_no,0,-1);
					$prResult = "Error: Purchase Request ".$prProc_no." has not been cancelled.";
				}
				break;
		}
	}//end process check

?>

<form id="frmSize" name="frmSize" method="post" action="../../../class_function/print_preview.php" target="_self">
	<input name="print" type="hidden" id="print">
	<input name="what" type="hidden" id="what">
	<input name="font-scale" type="hidden" id="font-scale">
	<input name="paper-size" type="hidden" id="paper-size">
</form>

<!-- !-->
<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="purchase.php">&lt;&lt;Back</a>
			</div>
		</div>

		<?php 
		if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
		?>	
		<div class="col-md-3" style="padding: 0px;"></div>
		<div class="col-md-6" style="padding: 0px;">
			<div class="btn-group btn-group-justified">
				<a class="btn btn-success operation" href="javascript: $(this).ifCheck('approved')">Approve Selected</a>
				<a class="btn btn-default" href="javascript: $(this).ifCheck('disapproved')">Disapprove Selected</a>
				<a class="btn btn-primary operation" href="purchase_op.php?loc=purchase_for_posting">NEW REQUEST</a>
			</div>
		</div>
		<?php
		} else {
		?>
		<div class="col-md-7" style="padding: 0px;"></div>
		<div class="col-md-2" style="padding: 0px;">
			<div class="btn-group btn-group-justified">
				<a class="btn btn-primary operation" href="purchase_op.php?loc=purchase_for_posting">NEW REQUEST</a>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>

<?php
	if(isset($prResult)){
		echo '<div class="msg well">'.$prResult.'</div>';
		unset($prResult);
	}
?>

<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
	<table class="table" id="tblStyle"><!-- Start Purchase Requests for Posting !-->
	 	<tr>
	 	  	<th>
		 	  	<div class="col-xs-12 col-md-12" style="padding: 0px;">
			  		<div class="col-md-3" style="padding: 0px;"> 
						<strong><label>#Purchase Requests for Posting</label></strong>
					</div>
					<div class="col-md-6" style="padding: 0px;">
						&nbsp
					</div>
					<div class="col-md-3" style="padding: 0px;">
						<form method="POST" name="frmSearch" action="#" class="form-inline">
							<div class="form-group" style="text-align: left; width: 100%;">
								<label for="txtSearch">
									<strong class="font-color-2">Search: (Click Enter to Search)</strong> 
								</label>
								<input id="txtSearch" class='form-control' type="search" name="txtSearch" placeholder="Enter a keyword first...">
							</div>
				  		</form>
					</div>
				</div>
	 	  	</th>
	 	</tr>
	 	<tr>
		  	<td>

			<?php
			
			if (!isset($_SESSION['showPerPage'])) {
				$_SESSION['showPerPage'] = 30;
			}

			if (isset($_POST['txtPerPage'])) {
				$_SESSION['showPerPage'] = $_POST['txtPerPage'];
				unset($_REQUEST['limit']);
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

			//$search_value = $_GET["search_query_purchase_txtbox"];

	 		if (isset($_SESSION['log_admin'])) {
				$countQry = "SELECT COUNT(prID) totalBut 
						     FROM tblpr prs 
						     INNER JOIN tblemp_accounts emps 
						     ON prs.requestBy = emps.empID 
						     WHERE prs.prStatus 
                             IN ('pending', 'for_posting') ";
				$qryForPosting = "SELECT prID, prNo, purpose, prDate, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, emps.empID 
							  	  FROM tblpr prs 
							  	  INNER JOIN tblemp_accounts emps 
							  	  ON prs.requestBy = emps.empID 
							  	  WHERE prs.prStatus 
                                  IN ('pending', 'for_posting') ";
			} else if (isset($_SESSION['log_pstd'])) {
				$countQry = "SELECT COUNT(prID) totalBut 
						     FROM tblpr prs 
						     INNER JOIN tblemp_accounts emps 
							 ON prs.requestBy = emps.empID 
						     WHERE prs.prStatus 
                             IN ('pending', 'for_posting') 
						     AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
				$qryForPosting = "SELECT prID, prNo, purpose, prDate, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, emps.empID 
							  	  FROM tblpr prs 
							  	  INNER JOIN tblemp_accounts emps 
							  	  ON prs.requestBy = emps.empID 
							  	  WHERE prs.prStatus 
                                  IN ('pending', 'for_posting') 
							  	  AND emps.sectionID = '". $_SESSION['log_sectionID']  ."' ";
			} else {
				$countQry = "SELECT COUNT(prID) totalBut 
						     FROM tblpr AS prs 
						     INNER JOIN tblemp_accounts AS emps 
						     ON prs.requestBy = emps.empID 
						     WHERE prs.prStatus 
							 IN ('pending', 'for_posting') 
						     AND emps.empID = '".$_SESSION['log_empID']."'";
				$qryForPosting = "SELECT prID, prNo, purpose, prDate, concat(lastname,', ',firstname,' ',left(middlename,1),'.') name, emps.empID 
							  	  FROM tblpr AS prs 
							  	  INNER JOIN tblemp_accounts AS emps 
							  	  ON prs.requestBy = emps.empID 
							  	  WHERE prs.prStatus 
							  	  IN ('pending', 'for_posting') 
							  	  AND emps.empID = '". $_SESSION['log_empID'] ."'";
			}

			if (!empty($searchInput)) {
				echo '<label> Searched For: "' . $searchInput . '" </label> 
							  <a class="btn btn-danger btn-sm" 
							  	 style="padding: 0px 4px 0px 4px;
    									border-radius: 25px; 
    									margin-left: 3px;
    									margin-bottom: 2px;"
    						     href="purchase_for_posting.php">
    						     Clear
    						   </a><br><br>';

				$countQry = $countQry . " AND (prNo LIKE '%$searchInput%' 
										  OR prDate LIKE '%$searchInput%' 
										  OR purpose LIKE '%$searchInput%' 
										  OR emps.firstname LIKE '%$searchInput%'
										  OR emps.middlename LIKE '%$searchInput%'
										  OR emps.lastname LIKE '%$searchInput%') 
										  ORDER BY prID DESC LIMIT $limit";
				$qryForPosting = $qryForPosting . " AND (prNo LIKE '%$searchInput%' 
										 		    OR prDate LIKE '%$searchInput%' 
										 		    OR purpose LIKE '%$searchInput%' 
												    OR emps.firstname LIKE '%$searchInput%'
												    OR emps.middlename LIKE '%$searchInput%'
										 		    OR emps.lastname LIKE '%$searchInput%') 
										 		    ORDER BY prID DESC LIMIT $limit";
			} else {
				$qryForPosting = $qryForPosting . " ORDER BY prID DESC LIMIT $limit";
			}

			if ($resQry = $conn->query($qryForPosting)) {
				if (mysqli_num_rows($resQry)) {
					$ctr = $startlimit;
					echo '<div class="table-container-1"><form method="post" name="frmPRPost" action="">';
					echo '<table class="table" id="tblLists">';		
					echo '<tr>
						  	  <th>
						  	  	  <input type="hidden" name="operation" />
						  	  </th>
						  	  <th width="1%">
						  	  	  <input type="checkbox" value="" name="chAll" onclick="$(this).checkAll();" />
						  	  </th>
						  	  <th width="10%">
						  	  	  prNo
						  	  </th>
						  	  <th width="10%">
						  	  	  PR Date
						  	  </th>
						  	  <th width="60%">
						  	  	  Purpose
						  	  </th>
						  	  <th width="15%" align="left" >
						  	  	  Requested By
						  	  </th>';

					if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
						echo  '<th>
						  	  	  
						  	  </th>';
					}

					echo	  '<th>
						  	  	  
						  	  </th>';

					echo  	  '<th>
						  	  	  
						  	  </th>';

					echo	  '<th>
						  	  	  
						  	  </th>
						  </tr>';

					while ($data = $resQry->fetch_object()) {
						$fCount++;
						$ctr++;
						$itemCount++;

						echo '<tr id=row_0 onclick="servOC('. $fCount . ',\'pr_info.php\',\'\')">';
						echo '<td><font color="#999999">'.$ctr.'</font></td>';
						echo '<td>
								 <input type="checkbox" name="itemCheck[]" value="pid='.$data->prID.'&pr='.$data->prNo.'" id="pr_'.$data->prID.'" />
							  </td>';	
						echo '<td>'.$data->prNo.'</td>';
						echo '<td>'.$data->prDate.'</td>';
						echo '<td align="left" style="padding-left: 20px;" id="name' . $fCount . '">
							  	  <img class="img-button" src="../../assets/images/down.png" /> ' . $data->purpose .
							 '</td>';
						echo '<td>'.$data->name.'</td>';			

						if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd'])) {
							echo '<td>';
							echo '<a data-toggle="tooltip" data-placement="left" title="Click to approve this Purchase Request (PR No: '. $data->prNo .')" href="javascript: $(this).checkItem1(\''.$fCount.'\',\'pr_'.$data->prID.'\',\'approved\');" title="Approved Item">
									 <img class="img-button" src="../../assets/images/approve.png" alt="Approved Request">
								  </a>';
							echo '</td>';
						}

						echo '<td>
								  <a data-toggle="tooltip" data-placement="left" title="Click to edit this Purchase Request (PR No: '. $data->prNo .')" href="purchase_op.php?loc=purchase_for_posting&edit='.$data->prID.'">
								  	  <img class="img-button" src="../../assets/images/edit.png" alt="Edit">
								  </a>
							  </td>';
		
						echo '<td>
								  <a data-toggle="tooltip" data-placement="left" title="Click to print this Purchase Request (PR No: '. $data->prNo .')" href="javascript: $(this).showPrintDialog(\''.$data->prID.'\',\'pr\');" title="Print Preview">
								  	  <img class="img-button" src="../../assets/images/print.png" alt="print">
								  </a>
							  </td>';
		
						echo '<td>';

						if ($_SESSION['log_empID'] == $data->empID) {
							echo '<a data-toggle="tooltip" data-placement="left" title="Click to delete this Purchase Request (PR No: '. $data->prNo .')" href="javascript: $(this).checkItem1(\''.$fCount.'\',\'pr_'.$data->prID.'\',\'delete\');" title="Delete Item">
								  	  <img class="img-button" src="../../assets/images/delete.png" alt="Delete">
								  </a>';
						} else {
							echo '<button style="padding: 0.2em; margin: 0px 0px 0px 0px;" class="btn btn-default" disabled="disabled">
								     <img class="img-button" src="../../assets/images/delete.png" alt="delete">
								  </button>';
						}

						echo '</td>';
						echo '</tr>';
						echo '<tr style="background: #fff; display: none;" id="ihtr'.$fCount.'">
							  	  <td colspan="10" class="pr-info">
							  	  	  <iframe id="ihif'.$fCount.'" frameborder="0" width="100%" src="pr_info.php?selected='.$data->prID.'">
							  	  	  </iframe>
							  	  </td>
							  </tr>';
					}

					while ($itemCount < $perPage) {
						echo "<tr id=row_0><td colspan='10'></td></tr>";
						$itemCount++;
					}

					echo '</table></form></div>';	
				} else {
					echo '<div align="center" style="color:#999999"><br />----- No available record for posting. -----<br /><br /></div>';
				}
			}
			?>   

			</td>
		</tr>

		<?php
			display_pages($conn, $countQry, 9, $accessPage, $perPage, "&selFilter="."all");
		?>
	</table> <!-- End Purchase Requests for Posting !-->
</div>

<?php
	include_once("modal/print-preview-modal.php");
	end_layout($page);
} else {
	header("Location:  " . $dir . "index.php");
}
?>