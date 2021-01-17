<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/functions.php");

if (isset($_SESSION['log_admin'])) {
	$searchInput = "";
	$itemCount = 0;

	if (isset($_POST['txtSearch'])) {
		$searchInput = trim($_POST['txtSearch']);
		unset($_SESSION['txtSearch']);
	}

	if (isset($_POST['delCheck'])) {
		while (list(,$val) = each($_POST['delCheck'])) {
			parse_str($val);

			if ($_POST['hdAction'] == 'delete') {
				//delete
				
				$qryCheck = $conn->query("SELECT prID 
										  FROM tblpr 
										  WHERE requestBy='".$eid."' LIMIT 1");

				if (mysqli_num_rows($qryCheck) <= 0) {
					$conn->query("DELETE FROM tblemp_accounts
								  WHERE empID='".$eid."'");
					$result = "User has been deleted.";
				} else {
					$result = "Can't delete user. User have purchase request transactions.";
				}
			} else {
				//update
				if ($block == 'n') {
					$conn->query("UPDATE tblemp_accounts 
								  SET blocked='y'
								  WHERE empID='".$eid."'");
				} else {
					$conn->query("UPDATE tblemp_accounts 
								  SET blocked='n' 
								  WHERE empID='".$eid."'");
				}
			}
		}
	}

	start_layout("DOST-CAR Procurement System", "User Accounts", "");

	echo '
	<div id="action">
		<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
			<div class="col-md-3" style="padding: 0px;"> 
				<div class="btn-group btn-group-justified">
					<a class="btn btn-danger operation-back" href="system_libraries.php">&lt;&lt;Back</a>
				</div>
			</div>

			<div class="col-md-7" style="padding: 0px;"></div>
			
			<div class="col-md-2" style="padding: 0px;">
				<div class="btn-group btn-group-justified">
					<a class="btn btn-primary operation" href="users_op.php">Add User</a>
				</div>
			</div>
		</div>
	</div>';

	if (isset($result)) {
		echo '<div class="msg">' . $result . '</div>';
	}

	echo '<div id="table-container" class="col-xs-12 col-md-12" style="overflow: auto; padding: 0px;">
	<form name="frmUsers" method="post">
		<table class="table table-hover" cellpadding="4" cellspacing="0" id="tblStyle" align="center" width="85%">
			<tr>
				<tr>
			  	  	<th>
				 	  	<div class="col-xs-12 col-md-12" style="padding: 0px;">
					  		<div class="col-md-3 col-xs-12" style="padding: 0px;"> 
								<strong><label>#System Users</label></strong>
							</div>
							<div class="col-md-6 col-xs-12" style="padding: 0px;">
								&nbsp
							</div>
							<div class="col-md-3 col-xs-12" style="padding: 0px;">
								<form method="POST" name="frmSearch" action="#" class="form-inline">
									<div class="form-group" style="text-align: left; width: 100%;">
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
			</tr>
			<tr>
				<td>
					<table class="table table-hover" cellpadding="4" cellspacing="0" id="tblLists" align="center" width="97%">
						<tr>
							<th width="10%">
								<input type="hidden" name="hdAction">
							</th>
							<th align="left" style="padding-left: 20px;" width="60%">
								Name
							</th>
							<th width="15%">
								Last Login
							</th>
							<th width="5%">
								Blocked
							</th>
							<th>
							</th>
							<th>
							</th>
							<th>
							</th>
						</tr>';

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

	//display list
	$qry = "SELECT COUNT(empID) totalBut 
			FROM tblemp_accounts 
			WHERE empID <> '".$_SESSION['log_admin']."' ";
	$qryUsers = "SELECT CONCAT(lastname, ', ',firstname, ' ', left(middlename, 1),'.') name, 
						empID, username,empID, user_type, last_login, blocked 
				 FROM tblemp_accounts 
				 WHERE empID <> '". $_SESSION['log_admin'] . "' ";

	if (!empty($searchInput)) {
		echo '<label> Searched For: "' . $searchInput . '" </label> 
			  <a class="btn btn-danger btn-sm" 
			  	 style="padding: 0px 4px 0px 4px;
    					border-radius: 25px; 
    					margin-left: 3px;
    					margin-bottom: 2px;"
    		     href="system_users.php">
    		     Clear
    		   </a><br><br>';

		$qry = $qry . " AND (firstname LIKE '%$searchInput%' 
							 OR lastname LIKE '%$searchInput%' 
							 OR middlename LIKE '%$searchInput%' 
							 OR position LIKE '%$searchInput%') 
							 ORDER BY lastname ASC LIMIT $limit";
		$qryUsers = $qryUsers . " AND (firstname LIKE '%$searchInput%' 
								 	   OR lastname LIKE '%$searchInput%' 
								 	   OR middlename LIKE '%$searchInput%' 
								 	   OR position LIKE '%$searchInput%') 
								 	   ORDER BY lastname ASC LIMIT $limit";
	} else {
		$qryUsers = $qryUsers . " ORDER BY lastname ASC LIMIT $limit";
	}

	if ($users = $conn->query($qryUsers)) {
		while ($data = $users->fetch_object()) {
			echo '
				<tr id="row_0">
					<td>
						<input type="checkbox" name="delCheck[]" value="eid='.$data->empID.'&block='.$data->blocked.'" id="user_'.$data->empID.'">
					</td>
					<td align="left" style="padding-left: 20px;">'.
						$data->name.'&nbsp;['.$data->user_type.']
					</td>
					<td>'.
						$data->last_login .'
					</td>	
					<td>'.
						$data->blocked.'
					</td>
					<td>
						<a href="users_op.php?edit='.$data->empID.'">
							<img class="img-button" src="../../assets/images/edit.png" alt="edit user">
						</a>
					</td>
					<td>
						<a href="javascript: checkDelete(\''.$data->name.'\',\'user_'.$data->empID.'\',\'block\');">
							<img class="img-button" src="../../assets/images/block.png" alt="edit user">
						</a>
					</td>
					<td>
						<a href="javascript: checkDelete(\''.$data->name.'\',\'user_'.$data->empID.'\',\'delete\');">
							<img class="img-button" src="../../assets/images/delete.png" alt="edit user">
						</a>
					</td>
				</tr>';
		}
	}

	while ($itemCount < $perPage) {
		echo '<tr id=row_0><td colspan="7"></td></tr>';
		$itemCount++;
	}
	
	echo '
					</table>
				</td>
			</tr>';
	display_pages($conn, $qry, 7, $accessPage, $perPage);

	echo '
		</table>
	</form></div>';

	end_layout("system_users");
} else {
	header("Location:  " . $dir . "index.php");
}
?>