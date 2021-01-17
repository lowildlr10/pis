<?php

include_once("session.php");
include_once("../layout/main_layout.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='purchase.php' style='color: #98ffe8;'>Purchase Request</a>");
?>

<div id="action">
	<div class="col-xs-12 col-md-12" style="padding: 0px; margin-bottom: 2px">
		<div class="col-md-3" style="padding: 0px;"> 
			<div class="btn-group btn-group-justified">
				<a class="btn btn-danger operation-back" href="access.php">&lt;&lt;Back</a>
			</div>
		</div>
		<div class="col-md-3" style="padding: 0px;"></div>
		<div class="col-md-6" style="padding: 0px;"></div>
	</div>
</div>

<div align="center">
	<div id="menu">
		<div id="icon">
			<a href="purchase_op.php?loc=purchase">
				<img src="../../assets/images/add.png" alt="ARE">
				<br>
				<br>
				<span>Create Purchase Request</span>
			</a>
		</div>
		<div id="icon">
			<a href="purchase_for_posting.php">
				<img src="../../assets/images/are.png" alt="ARE">
				<br>
				<br>
				<span>For Approval</span>
			</a>
		</div>
		<div id="icon">
			<a href="purchase_made.php">
				<img src="../../assets/images/custodian.png" alt="Custodian Slip">
				<br>
				<br>
				<span>Approved Purchase Request</span>
			</a>
		</div> 
	</div>
</div>

<?php
	end_layout();
} else {
	header("Location:  /pis/index.php");
}
?>