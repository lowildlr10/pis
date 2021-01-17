<?php

include_once("session.php");
include_once("../layout/main_layout.php");
include_once($dir . "dbcon.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='system_libraries.php' style='color: #98ffe8;'>System Libraries</a>");	
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
			<?php
			if (isset($_SESSION['log_admin'])) {
			?>
				<div id="icon">
					<a href="item_classification.php">
						<img src="../../assets/images/item_classification.png" alt="items classifications">
						<br>
						<br>
						<span>Items Classifications</span>
					</a>
				</div>
				<div id="icon">
					<a href="unit_issue.php">
						<img src="../../assets/images/unit.png" alt="unit of issue">
						<br>
						<br>
						<span>Units Of Issue</span>
					</a>
				</div>
				<div id="icon">
					<a href="classification.php">
						<img src="../../assets/images/classification.png" alt="bidders classifications">
						<br>
						<br>
						<span>Supplier Classifications</span>
					</a>
				</div>
				<div id="icon">
					<a href="signatories.php">
						<img src="../../assets/images/signatories.png" width="75" height="75">
						<br>
						<br>
						<span>Signatories</span>
					</a>
				</div>
			 	<div id="icon">
					<a href="sections.php">
						<img src="../../assets/images/section.png" alt="sections">
						<br>
						<br>
						<span>Sections</span>
					</a>
				</div>  
			    <div id="icon">
					<a href="#">
						<img src="../../assets/images/cat.png" alt="supply categories">
						<br>
						<br>
				  		<span>Supply Categories</span>
				  	</a>
			  	</div>
			    <div id="icon">
					<a href="#">
						<img src="../../assets/images/subcat.png" alt="supply sub categories">
						<br>
						<br>
				  		<span>Supply Sub-categories</span>
				  	</a>
			  	</div>   
			  	<div id="icon">
				  	<a href="pr_corrections.php">
				  		<img src="../../assets/images/correctpr.png" alt="corrections">
				  		<br>
				  		<br>
				  		<span>Pr Corrections</span>
				  	</a>
				</div>
				<div id="icon">
					<a href="system_users.php">
						<img src="../../assets/images/users.png" alt="user accounts">
						<br>
						<br>
						<span>User Accounts</span>
					</a>
				</div>
			<?php
			} else if (isset($_SESSION['log_pstd'])) {
			?>

				<div id="icon">
					<a href="signatories.php">
						<img src="../../assets/images/signatories.png" width="75" height="75">
						<br>
						<br>
						<span>Signatories</span>
					</a>
				</div>

			<?php
			}
			?>

			<div id="icon">
				<a href="suppliers.php">
					<img src="../../assets/images/bidders.png" alt="bidders">
					<br>
					<br>
					<span>Suppliers</span>
				</a>
			</div>
		</div>
	</div>

<?php
	end_layout();
} else {
	header("Location:  " . $dir . "index.php");
}
?>