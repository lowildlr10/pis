<?php

include_once("session.php");
include_once( "../layout/main_layout.php" );	
include_once( $dir . "dbcon.php" );

$loginType = "";
$_SESSION['showPerPage'] = 30;

if (isset($_SESSION['log_admin'])) {
	$loginType = "admin";
}

if (isset($_SESSION['log_pstd'])) {
	$loginType = "pstd";
}

if (isset($_SESSION['log_staff'])) {
	$loginType = "staff";
}

if (isset($_SESSION['log_encoder'])) {
	$loginType = "encoder";
}

switch ($loginType) {
	case 'admin':
		start_layout("DOST-CAR Procurement System", "Home");	
		adminAccess();
		break;
	case 'pstd':
		start_layout("DOST-CAR Procurement System", "Home");	
		pstdAccess();
		break;
	case 'staff':
		start_layout("DOST-CAR Procurement System", "Home");	
		staffAccess();
		break;
	case 'encoder':
		start_layout("DOST-CAR Procurement System", "Home");	

		break;
	default:
		header("Location:  ". $dir . "index.php");
		break;
}
?>

<?php 
function adminAccess() {
?>
	<div id="menu">
		<div id="icon">
			<a href="purchase.php">
				<img src="../../assets/images/prs.png" alt="prs">
				<br>
				<br>
				<span>Purchase Requests</span>
			</a>
		</div>
		<div id="icon">
			<a href="canvass.php">
				<img src="../../assets/images/canvass.png" alt="canvass">
				<br>
				<br>
				<span>Request for Quotation</span>
			</a>
		</div>
		<div id="icon">
			<a href="abstract.php">
				<img src="../../assets/images/abstract.png" alt="abtract">
				<br>
				<br>
				<span>Abstract of Bids & Quotations</span>
			</a>
		</div>
		<div id="icon">
			<a href="purchase_job_order.php">
				<img src="../../assets/images/po.png" alt="purchase order">
				<br>
				<br>
				<span>Purchase & Job Order</span>
			</a>
		</div>
	    <div id="icon">
			<a href="obligation_request.php">
				<img src="../../assets/images/obr.png" alt="obligation request">
				<br>
				<br>
				<span>ORS and BURS</span>
			</a>
		</div>
		<div id="icon">
			<a href="iar.php">
				<img src="../../assets/images/inspect.png" alt="inspections">
				<br>
				<br>
				<span>Inspection & Acceptance Report</span>
			</a>
		</div>
		<div id="icon">
			<a href="dv.php">
				<img src="../../assets/images/dv.png" alt="disbursement voucher">
				<br>
				<br>
				<span>Disbursement Voucher</span>
			</a>
		</div>
	  	<div id="icon">
			<a href="inventory.php">
				<img src="../../assets/images/inventory.png" alt="inventory">
				<br>
				<br>
				<span>Inventory</span>
			</a>
		</div>
		<div id="icon">
			<a href="report_menu.php">
				<img src="../../assets/images/reports.gif" alt="help">
				<br>
				<br>
				<span>Reports</span>
			</a>
		</div>
	    <div id="icon">
			<a href="system_libraries.php">
				<img src="../../assets/images/settings.png" alt="system libraries">
				<br>
				<br>
				<span>System Libraries</span>
			</a>
	   	</div>
	</div>
<?php
}
?>

<?php 
function pstdAccess() {
?>
	<div id="menu">
		<div id="icon">
			<a href="purchase.php">
				<img src="../../assets/images/prs.png" alt="prs">
				<br>
				<br>
				<span>Purchase Requests</span>
			</a>
		</div>
		<div id="icon">
			<a href="canvass.php">
				<img src="../../assets/images/canvass.png" alt="canvass">
				<br>
				<br>
				<span>Request for Quotation</span>
			</a>
		</div>
		<div id="icon">
			<a href="abstract.php">
				<img src="../../assets/images/abstract.png" alt="abtract">
				<br>
				<br>
				<span>Abstract of Bids & Quotations</span>
			</a>
		</div>
		<div id="icon">
			<a href="purchase_job_order.php">
				<img src="../../assets/images/po.png" alt="purchase order">
				<br>
				<br>
				<span>Purchase & Job Order</span>
			</a>
		</div>
		<div id="icon">
			<a href="obligation_request.php">
				<img src="../../assets/images/obr.png" alt="obligation request">
				<br>
				<br>
				<span>ORS and BURS</span>
			</a>
		</div>
		<div id="icon">
			<a href="iar.php">
				<img src="../../assets/images/inspect.png" alt="inspections">
				<br>
				<br>
				<span>Inspection & Acceptance Report</span>
			</a>
		</div>
		<div id="icon">
			<a href="dv.php">
				<img src="../../assets/images/dv.png" alt="disbursement voucher">
				<br>
				<br>
				<span>Disbursement Voucher</span>
			</a>
		</div> 
	  	<div id="icon">
			<a href="inventory.php">
				<img src="../../assets/images/inventory.png" alt="inventory">
				<br>
				<br>
				<span>Inventory</span>
			</a>
		</div>
	    <div id="icon">
			<a href="system_libraries.php">
				<img src="../../assets/images/settings.png" alt="system libraries">
				<br>
				<br>
				<span>System Libraries</span>
			</a>
	   	</div>
	    <div id="icon">
			<a href="javascript: alert('Please contact the MIS.');">
				<img src="../../assets/images/help.png" alt="help">
				<br>
				<br>
				<span>Help</span>
			</a>
		</div> 
	</div>
<?php
}
?>

<?php 
function staffAccess() {
?>
	<div id="menu">
		<div id="icon">
			<a href="purchase.php">
				<img src="../../assets/images/prs.png" alt="prs">
				<br>
				<br>
				<span>Purchase Requests</span>
			</a>
		</div>
        <div id="icon">
            <a href="canvass.php">
                <img src="../../assets/images/canvass.png" alt="canvass">
                <br>
                <br>
                <span>Request for Quotation</span>
            </a>
        </div>
		<div id="icon">
			<a href="obligation_request.php">
				<img src="../../assets/images/obr.png" alt="obligation request">
				<br>
				<br>
				<span>ORS and BURS</span>
			</a>
		</div>
		<div id="icon">
			<a href="iar.php">
				<img src="../../assets/images/inspect.png" alt="inspections">
				<br>
				<br>
				<span>Inspection & Acceptance Report</span>
			</a>
		</div>
		<div id="icon">
			<a href="dv.php">
				<img src="../../assets/images/dv.png" alt="disbursement voucher">
				<br>
				<br>
				<span>Disbursement Voucher</span>
			</a>
		</div>
        <div id="icon">
            <a href="system_libraries.php">
                <img src="../../assets/images/settings.png" alt="system libraries">
                <br>
                <br>
                <span>System Libraries</span>
            </a>
        </div>
	    <div id="icon">
			<a href="javascript: alert('Please contact the MIS.');">
				<img src="../../assets/images/help.png" alt="help">
				<br>
				<br>
				<span>Help</span>
			</a>
		</div> 
	</div>
<?php
}
?>

<?php 
function encoderAccess() {
?>
	
<?php
}
?>

<?php
	end_layout();
?>