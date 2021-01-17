<?php
	
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$inventoryID = "";
$inventoryClass = "";
$inventoryClassNo = "";
$prID = "";
$poNo = "";

$picture = "";
$empName = "";
$quantity = 0;

if ($_POST["inventoryID"]) {
	$inventoryID = $_POST["inventoryID"];
	unset($_POST["inventoryID"]);
}

if ($_POST["inventoryClassNo"]) {
	$inventoryClassNo = $_POST["inventoryClassNo"];
	unset($_POST["inventoryClassNo"]);
}

$qry1 = $conn->query("SELECT DISTINCT emp.empID, emp.position, emp.picture, 
							 concat(emp.firstname, ' ', emp.lastname) name, inv.inventoryClass, 
							 po.prID, po.poNo 
					  FROM tblemp_accounts emp 
					  INNER JOIN tblitem_issue issue 
					  ON issue.empID = emp.empID 
					  INNER JOIN tblinventory_items inv 
					  ON inv.id = issue.inventoryID 
					  INNER JOIN tblpo_jo_items po 
					  ON po.id = inv.poItemID 
					  WHERE inv.inventoryClassNo = '" . $inventoryClassNo . "' 
					  ORDER BY name ASC") 
					  or die(mysql_error($conn));

if (mysqli_num_rows($qry1)) {
	echo '<h4 style="text-align: left;">Issued to:</h4>';
	echo '<form><div style="overflow-x: auto;
				      height: 37em;
				      border: 1px #4577b4 solid;
				      border-radius: 5px;">
				      <ol style="padding: 14px 12px 4px 24px;">';

	while ($data = $qry1->fetch_object()) {
		$picture = $data->picture;
		$empName = $data->name;
		//$quantity = $data->quantity;
		$inventoryClass = $data->inventoryClass;
		$prID = $data->prID;
		$poNo = $data->poNo;

		if (empty($picture)) {
			$picture = "default.jpg";
		}

		echo '<li></span> <a class="btn btn-default btn-block" style="text-align: left;" 
							 href="javascript: $(this).printDialog(\'' . $inventoryID . '\',\'' . $data->inventoryClass . 
										   		'\',\'' . $data->prID . '\',\'' . $data->poNo . '\',\'' . 'old' . '\',\'' . $data->empID . 
										   		'\', \'' . $inventoryClassNo . '\', \'' . 'y' . '\');">';
		echo '<input type="hidden" class="btn-id" value="' . $data->empID . '">';
		echo '<span><img src="../../../resources/assets/images/users/' . $picture . '" width="20px"></span> ';
		echo "<span class='font-color-1'><strong> $empName </strong></span><br><br>";
		//echo '<span style="color: #d9534f;"><strong> No. item\s acquired: ' . $quantity . ' </strong></span>';
		echo '</a></li>';
	}

	echo '</ol></div></form><br>';

	/*
    echo '<a class="btn btn-default btn-block" style="text-align: left;"
             href="javascript: $(this).printDialog(\'' . $inventoryID . '\',\'' . $inventoryClass . 
                                                    '\',\'' . $prID . '\',\'' . $poNo . '\',\'' . 'old' . '\',\'' . '0' . 
                                                    '\', \'' . $inventoryClassNo . '\', \'' . 'y' . '\');">';
    echo 'or <strong class="font-color-1">Print Manual Document</strong>';
    echo '</a>';
    */
} else {
	echo '<h4 style="text-align: left;">Issued to:</h4>';
	echo '<form><div style="overflow-x: auto;
				      height: 10em;
				      border: 1px #4577b4 solid;
				      border-radius: 5px;">';
	echo '<br><h4> Not yet issued. </h4></div></form><br>';
}

?>