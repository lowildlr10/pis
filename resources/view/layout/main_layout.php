<?php

//$dir = $_SERVER['DOCUMENT_ROOT'] . "/pis/";

include_once("../../../config.php");	
$imgDir = $dir . "resources/assets/images";
$jsDir = $dir . "resources/assets/js";
$cssDir = $dir . "resources/assets/css";

function start_layout($page_title, $content_title){
	$pictureFile = "";

	if (isset($_SESSION['log_picture'])) {
		if (empty($_SESSION['log_picture'])) {
			$pictureFile = "default.jpg";
		} else {
			$pictureFile = $_SESSION['log_picture'];
		}
	} else {
		$pictureFile = "default.jpg";
	}
?>
	
	<!DOCTYPE html>
	<html lang="en" style="height: 100%;">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $page_title ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="/pis/favicon.png">
		<link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="../../assets/css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="../../assets/css/daterangepicker.css">
		<link rel="stylesheet" href="../../assets/css/main.css">
	</head>
	<body id="main" style="height: 100%;">
		<nav id="top" class="navbar navbar-default navbar-fixed-top">
		    <div class="container" style="width: 80%;">
		        <div class="navbar-header">

		            <!-- Collapsed Hamburger -->
		            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
		                <span class="sr-only">Toggle Navigation</span>
		                <span class="icon-bar"></span>
		                <span class="icon-bar"></span>
		                <span class="icon-bar"></span>
		            </button>

        			<a class="navbar-brand" href="/pis/resources/view/pages/access.php">
        				<img src="../../assets/images/pis_logo1.png" alt="DOST-CAR">
        			</a>
		        </div>

		        <div class="collapse navbar-collapse" id="app-navbar-collapse">
		            <!-- Left Side Of Navbar -->
		            <!--  !-->

		            <!-- Right Side Of Navbar -->
		            <ul class="nav navbar-nav navbar-right">
		                <!-- Authentication Links -->
		                <input id="access-level" type="hidden" value="{{ Auth::user()->access_level }}">
		                <input id="emp-id" type="hidden" value="{{ Auth::user()->empID }}">
		                <input id="division-id" type="hidden" value="{{ Auth::user()->divisionID }}">
		                <li class="dropdown">
		                    <a id="nav-user" href="#" class="dropdown-toggle btn btn-info btn-sm" data-toggle="dropdown" role="button" 
		                       aria-expanded="false">
			                    <div style="float: left;">
			                    	<img class="img-circle" src="<?php echo "/pis/resources/assets/images/users/" . $pictureFile ?>" 
			                    		 alt="User" width="40">
			                    </div>
			                    <div style="padding: 0 0 0 56px; text-align: left;">
			                    	<strong class="font-color-2">
										<?php echo strtoupper($_SESSION['log_name']) ?>
										<span class="caret"></span>
									</strong>
									<br>
									<font class="font-color-2">
										<?php echo strtoupper($_SESSION['log_position']) ?>
									</font>
			                    </div>
		                    </a>

		                    <ul class="dropdown-menu" role="menu">
		                        <li>
		                            <a href="eaccount_op.php">
		                                <i class="glyphicon glyphicon-user"></i>
		                                Profile
		                            </a>
		                        </li>
		                        <li>
		                            <a href="../../../logout.php">
		                                <i class="glyphicon glyphicon-log-out"></i>
		                                Logout
		                            </a>
		                        </li>
		                    </ul>
		                </li>
		            </ul>
		        </div>
		    </div>
		</nav>

		<div id="mainBody">
		    <div class="container custom-container">
		    	<div class="row">
		    		<div id="conArea" class="col-sm-12 col-md-12 panel panel-default">
					  	<div class="panel-heading col-md-12" style="background-color: #005e7c; border-bottom: 2px;">
					  		<div id="title" class="col-md-8">
					  			<label id="location-label">
					  				<?php
					  				if ($content_title != "Home") {
					  				?>
					  					<a href="access.php" style="color: rgb(225, 239, 243);">Home</a>/<?php echo $content_title ?>
					  				<?php
					  				} else {
					  				?>
					  					<?php echo $content_title ?>
					  				<?php
					  				}
					  				?>
					  			</label>
					  		</div>
					  		<div id="title" class="col-md-4">
					  			<label>Today is <?php echo date("l : F d, Y") ?></label>
					  			<label id="time-display">[ <?php echo date("g:i:s a") ?> ]</label>
					  		</div>

					  	</div>
					  	<div id="panel-body-1" class="panel-body custom-panel-body">
<?php
}
	
function end_layout($jsFile = ""){
?> 							
						</div>
					</div>
				</div>
			</div>
		</div>

		<nav class="navbar navbar-default navbar-fixed-bottom" style="background-color: #005e7c; border-top: 6px outset #337ab7;">
			<div class="custom-nav-bottom">
			    <center class="font-color-2">
			        © Department of Science & Technology - Cordillera Administrative Region 
			        All Rights Reserved 2010
			    </center>
			</div>
		</nav>
		
		<script type="text/javascript" src="../../assets/js/jquery.js"></script>
		<script type="text/javascript" src="../../assets/js/moment.min.js"></script>
		<script type="text/javascript" src="../../assets/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../../assets/js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="../../assets/js/functions.js"></script>
		<script type="text/javascript" src="../../assets/js/calendar.js"></script>
		<script type="text/javascript" src="../../assets/js/daterangepicker.js"></script>
		<script type="text/javascript">
			$(function() {
				setInterval(function() { 
					var dt = moment().format('h:mm:ss a');
					$("#time-display").text("[ " + dt + " ]");
				}, 500);

				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>
	<?php
		if ($jsFile != "") {
			echo '<script type="text/javascript" src="../../assets/js/' . $jsFile . '.js"></script>';
		}
	?>

	</body>
	</html>

<?php
}
?>