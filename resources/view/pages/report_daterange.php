<?php

include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$what = "";

if (isset($_REQUEST["what"])) {
	$what = $_REQUEST["what"];
}

$qrySection = $conn->query("SELECT sectionID, section 
							FROM tblsections")
							or die(mysql_error($conn));
?>

<div class="row" style="margin-right: 0;">
	<div class="col-md-12">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="form-group font-color-1" style="text-align: left;">
				<?php 
				if ($what == "pmf") {
				?>
					<label for="sel-division">Select a Division:</label>
					<strong>
						<select class="form-control font-color-1" id="sel-division">
							<?php
							while ($data = $qrySection->fetch_object()) {
								echo '<option value="' . $data->sectionID . '">' .
									 $data->section . '</option>';
							}
							?>
						</select>
					</strong>
					<br>
					<label for="sel-report-menu">Select a Range of PR Date:</label>
					<strong>
						<div class="input-group">
							<input type="text" id="report-date-range" class="form-control font-color-1">
							<span class="input-group-btn">
						        <button id="btn-generate" class="btn btn-success" type="button">
						        	Generate
						        </button>
						   </span>
					   </div>
					</strong>
				<?php
				} else if ($what == "ios") {
				?>
					<label for="sel-classification">Select a Inventory Type:</label>
					<strong>
						<select class="form-control font-color-1" id="sel-classification">
							<option value="ris"> Requisition and Issue Slip (RIS) </option>
						</select>
					</strong>
					<br>
					<label for="sel-report-menu">Select a Range of Delivery Date:</label>
					<strong>
						<div class="input-group">
							<input type="text" id="report-date-range" class="form-control font-color-1">
							<span class="input-group-btn">
						        <button id="btn-generate" class="btn btn-success" type="button">
						        	Generate
						        </button>
						   </span>
					   </div>
					</strong>
				<?php
				} else if ($what == "pcppe") {
				?>
					<label for="sel-classification">Select a Inventory Type:</label>
					<strong>
						<select class="form-control font-color-1" id="sel-classification">
							<option value="par"> Property Acknowledgement Receipt (PAR) </option>
							<option value="ics"> Inventory Custodian Slip (ICS) </option>
						</select>
					</strong>
					<br>
					<label for="sel-category">Select a Category:</label>
					<strong>
						<select class="form-control font-color-1" id="sel-category">
							<option value="all"> -- All -- </option>
							<?php
								$qry = $conn->query("SELECT * 
												 	 FROM tblitem_categories") 
													 or die(mysql_error($conn));

								while ($list1 = $qry->fetch_object()) {
									echo '<option value="' . $list1->categoryID . '">' . $list1->category . '</option>';
								}
							?>
						</select>
					</strong>
					<br>
					<label for="sel-report-menu">Select a Range of Delivery Date:</label>
					<strong>
						<div class="input-group">
							<input type="text" id="report-date-range" class="form-control font-color-1">
							<span class="input-group-btn">
						        <button id="btn-generate" class="btn btn-success" type="button">
						        	Generate
						        </button>
						   </span>
					   </div>
					</strong>
				<?php
				}
				?>
			</div>
		</div>
		<div class="col-md-3"></div>
	</div>
</div>