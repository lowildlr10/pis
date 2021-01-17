<?php
$session_lifetime = 3600 * 24 * 2; // 2 days
session_set_cookie_params($session_lifetime);
session_start();
include_once("dbcon.php");

/*
$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'dbpis'
        AND ENGINE = 'MyISAM'";

$rs = $conn->query($sql);

while($row = $rs->fetch_array()) {
    $tbl = $row[0];
    $sql = "ALTER TABLE `$tbl` ENGINE=INNODB";
    $conn->query($sql);
}

$sql1 = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'dbpis'";

$rs1 = $conn->query($sql1);

while($row = $rs1->fetch_array()) {
    $tbl = $row[0];
    $sql1 = "ALTER TABLE `$tbl` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
    $conn->query($sql1);
}*/

$title = "SYSTEM LOGIN";
$page = "index.php";

//check user login
if (isset($_POST['txtUser']) || isset($_POST['txtPass'])) {
	$userLog = (get_magic_quotes_gpc()) ? $_POST['txtUser'] : addslashes($_POST['txtUser']);
	$userPass = (get_magic_quotes_gpc()) ? $_POST['txtPass'] : addslashes($_POST['txtPass']); 			
	
	//qry user login
	$qryCheckUser = "SELECT empID, concat(firstname,' ',left(middlename,1),'. ', lastname) 
					 AS name, position, user_type, blocked, sectionID, picture 
					 FROM tblemp_accounts 
					 WHERE username = '".$userLog."' 
					 AND password = '".md5($userPass)."'";

	$result = $conn->query($qryCheckUser, MYSQLI_STORE_RESULT);
	$data = $result->fetch_object();

	if (count($data) != 0) {
		if ($data->empID) {
			if ($data->blocked != 'y') {
				$logNow = date('Y-m-d H:i:s');
				$update = $conn->query("UPDATE tblemp_accounts 
					   					SET last_login='".$logNow."' 
					   					WHERE empID='".$data->empID."'")
								or die(mysqli_error($conn));

				$_SESSION['log_name'] = $data->name;
				$_SESSION['log_position'] = $data->position;
				$_SESSION['log_empID'] = $data->empID;
				$_SESSION['log_sectionID'] = $data->sectionID;
				$_SESSION['log_picture'] = $data->picture;

				switch ($data->user_type) {
					case 'admin':
						$_SESSION['log_admin'] = $data->empID;
						header("Location: resources/view/pages/access.php");
						break;
					case 'pstd':
						$_SESSION['log_pstd'] = $data->empID;
						header("Location: resources/view/pages/access.php");
						break;
					case 'staff':
						$_SESSION['log_staff'] = $data->empID;
						header("Location: resources/view/pages/access.php");
						break;
					case 'encoder':
						$_SESSION['log_encoder'] = $data->empID;
						header("Location: resources/view/pages/access.php");
						break;
				}					
			} else {
				$title .= ' : <font color="#fff">User is blocked from the system.</font>';
			}//end if blocked	
		}	
	} else {
		if (!isset($_POST['button2'])) {
			$title .= " : <font color=\"#CC6600\">Invalid Username / Password.</font>"; 
		}
	}//end if check exists
}

if (!isset($_SESSION['log_name'])) {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>DOST-CAR PIS SYSTEM</title>
	<link rel="shortcut icon" type="image/x-icon" href="/pis/favicon.png">
	<link rel="stylesheet" type="text/css" href="resources/assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="resources/assets/css/main.css">
</head>
<body onload="document.frmLogin.txtUser.focus();">
	<!--
	<div id="top" class="col-md-12">
		<div style="float: left; color: #e1eff3; padding: 18px; font-size: 21px;">
			<img src="resources/assets/images/dostlogo.png" width="80px">
			<strong>DOST-CAR PROCUREMENT SYSTEM</strong>
		</div>
	</div>
	!-->

	<nav id="top" class="navbar navbar-default navbar-static-top">
	    <div class="container" style="width: 80%;">
	        <div class="navbar-header">

	            <!-- Collapsed Hamburger 
	            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
	                <span class="sr-only">Toggle Navigation</span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>-->

    			<a class="navbar-brand" href="/pis">
    				<img src="resources/assets/images/pis_logo1.png" alt="DOST-CAR">
    			</a>
	            <!-- Branding Image 
	            <div id="nav-header-brand">
	                <a style="color: #d9edf7;" class="navbar-brand" href="/pis/resources/view/pages/access.php">
	                    <i>
	                        <img src="../../assets/images/dostlogo.png" alt="DOST-LOGO" width="40px">
	                    </i>
	                    <strong>
	                    PIS
	                    </strong>
	                </a>
	            </div>
	            -->
	        </div>
	    </div>
	</nav>

	<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <br>
            <br>
            <div id="login-panel" class="panel panel-default">
                <div id="login-head-panel" class="panel-heading">
                    <strong class="font-color-2">
                        <i class="fa fa-btn" style="display: inline;">
                            <img class="menu-image-undraggable" src="resources/assets/images/keyfold.png">
                        </i> 
                        <div style="display: inline;">
                        	<?php echo $title ?>
                        </div>
                    </strong>
                </div>
                <div class="panel-body">
                    <br>
                    <form class="form-horizontal" role="form" action="#" method="post" name="frmLogin">
                    	<!-- onsubmit="return $(this).checkInput();" !-->
                         <div class="form-group">
                            <label for="username" class="col-md-3 control-label font-color-2">USERNAME: </label>

                            <div class="col-md-8">
                                <input autocomplete="off" name="txtUser" id="txtUser" type="text" class="form-control font-color-1" name="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-3 control-label font-color-2">PASSWORD: </label>

                            <div class="col-md-8">
                                <input name="txtPass" id="txtPass" type="password" class="form-control font-color-1" name="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="btn-login" class="btn btn-primary font-color-2"> Login </button>
                                <input type="submit" name="button2" class="btn btn-default font-color-1" value="Reset">
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div id="login-foot-panel" class="panel-footer">
                	<div id="login-panel-text-down">
		                <center class="font-color-2">
		                    © Department of Science & Technology - Cordillera Administrative Region 
		                    All Rights Reserved 2010
		                </center>
		            </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
</body>

<?php 
} else {
	header("Location: resources/view/pages/access.php");
}
?>

<script type="text/javascript" src="resources/assets/js/jquery.js"></script>
<script type="text/javascript">
	$(function() {
		$("#btn-login").click(function() {
			$(this).checkInput();
		});

		$.fn.checkInput = function() {
			var txtUser = $("#txtUser").val().replace(/^\s+|\s+$/g, "").length;
			var txtPass = $("#txtPass").val().replace(/^\s+|\s+$/g, "").length;

			if (txtUser == 0 && txtPass == 0) {
				$("#txtUser").addClass("input-error-highlighter");
				$("#txtPass").addClass("input-error-highlighter");
				return false;
				alert("Please enter your username and password.");
			} else if (txtUser == 0 && txtPass > 0) {
				$("#txtUser").addClass("input-error-highlighter");
				alert("Please enter your username.");
			} else if (txtUser > 0 && txtPass == 0) {
				$("#txtPass").addClass("input-error-highlighter");
				alert("Please enter your password.");
			}
		}
	});
</script>

</html>