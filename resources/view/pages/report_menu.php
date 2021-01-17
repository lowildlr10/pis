<?php

include_once("session.php");
include_once("../layout/main_layout.php");

if (isset($_SESSION['log_admin']) || isset($_SESSION['log_pstd']) ||
	isset($_SESSION['log_staff']) || isset($_SESSION['log_encoder'])) {
	start_layout("DOST-CAR Procurement System",
				 "<a href='report_menu.php' style='color: #98ffe8;'>Report</a>");
	
	$page = "report";
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

<div class="panel panel-default" style="border: 2px solid #005e7c;">
	<div class="panel-heading col-md-12" style="background-color: #005e7c; margin-bottom: 15px;">
		<label class="font-color-2"> REPORT GENERATION </label>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div id="report-menu">
						<div class="form-group font-color-2" style="text-align: left;">
							<label for="sel-report-menu">Generate Reports For:</label>
							<strong>
								<select id="sel-report-menu" class="form-control font-color-1 required">
									<option value=""> -- Select a report to generate -- </option>
									<option value="pmf"> PROCUREMENT MONITORING FORM [FM-FAS-PUR F17] </option>
									<option value="ios"> INVENTORY ON SUPPLY [FM-FAS-PUR F20] </option>
									<option value="pcppe"> PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT [FM-FAS-PUR F25] </option>
								</select>
							</strong>
						</div>
						<button id="btn-continue" class="btn btn-info btn-block font-color-1">
							<strong>
								Continue
								<span class="glyphicon glyphicon-fullscreen"></span>
							</strong>
						</button>
					</div>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</div>
</div>

<form id="frmSize" name="frmSize" method="POST" action="../../../class_function/print_preview.php" target="_self">
	<input name="startDate" type="hidden" id="startDate">
	<input name="endDate" type="hidden" id="endDate">
	<input name="divisionID" type="hidden" id="divisionID">
	<input name="categoryID" type="hidden" id="categoryID">
	<input name="what" type="hidden" id="what">
	<input name="class" type="hidden" id="class">
	<input name="font-scale" type="hidden" id="font-scale">
	<input name="paper-size" type="hidden" id="paper-size">
</form>

<?php
	include_once("modal/print-preview-modal.php");
	include_once("modal/report-modals.php");
	end_layout($page);
} else {
	header("Location:  /pis/index.php");
}
?>