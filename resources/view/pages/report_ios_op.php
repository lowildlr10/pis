<?php
    
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$items = array();
$itemCounter = 0;
$startDate = "";
$endDate = "";
$classification = "";

if (isset($_REQUEST["startDate"])) {
	$startDate = $_REQUEST["startDate"];
}

if (isset($_REQUEST["endDate"])) {
	$endDate = $_REQUEST["endDate"];
}

if (isset($_REQUEST["classification"])) {
	$classification = $_REQUEST["classification"];
}

$dates = array();
$current = strtotime($startDate);
$last = strtotime($endDate);

while ($current <= $last) {
	$dates[] = date('m/d/Y', $current );
	$current = strtotime('+1 day', $current );
}

foreach ($dates as $date) {
	$tempItem = array();

	$qryInventory = $conn->query("SELECT po.itemDescription, po.unitIssue, po.amount, 
										 inv.propertyNo, inv.inventoryClassNo, po.quantity, 
										 concat(firstname,' ',left(middlename,1),'. ', lastname) name 
								  FROM tblinventory_items inv 
								  INNER JOIN tblpo_jo_items po 
								  ON po.id = inv.poItemID 
								  INNER JOIN tblpr pr 
							  	  ON pr.prID = inv.prID 
								  INNER JOIN tblemp_accounts emps 
							  	  ON pr.requestBy = emps.empID 
								  WHERE inv.createdAt LIKE '%$date%' 
								  AND inv.inventoryClass = '" . $classification . "' 
								  AND po.excluded = 'n' 
								  ORDER BY LENGTH(inv.createdAt), inv.createdAt ASC") 
							 	  or die(mysql_error($conn));

	while ($data = $qryInventory->fetch_object()) {
		$items[] = $data;
	}
}
?>

<table class="table table-bordered table-responsive">
	<tr>
		<th style="text-align: center;"> ARTICLE </th>
		<th style="text-align: center;"> DESCRIPTION </th>
		<th style="text-align: center;"> STOCK NO. </th>
		<th style="text-align: center;"> UNIT OF MEASURE </th>
		<th style="text-align: center;"> UNIT VALUE </th>
		<th style="text-align: center;"> BALANCE PER CARD (QUANTITY) </th>
		<th style="text-align: center;"> ON HAND PER COUNT </th>
		<th style="text-align: center;"> QTY (SHORTAGE/AVERAGE) </th>
		<th style="text-align: center;"> VALUE (SHORTAGE/AVERAGE) </th>
		<th style="text-align: center;"> REMARKS </th>
		<th style="text-align: center;"> </th>
	</tr>

	<?php
		if (count($items) > 0) {
			foreach ($items as $key => $item) {
				echo '<tr class="row_data" id="row-data-' . $key . '">';
				echo '<td><input type="text" class="form-control font-color-1 txt-article" 
							   value="">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-description" 
							   value="' . $item->itemDescription . '" disabled="disabled" id="txt-description-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-item-no" 
							   value="' . $item->propertyNo . '" disabled="disabled" id="txt-item-no-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-unit-name" 
							   value="' . $item->unitIssue . '" disabled="disabled" id="txt-unit-name-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-unit-value" 
							   value="' . $item->amount . '" disabled="disabled" id="txt-unit-value-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-quantity" 
							   value="" id="txt-quantity-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-per-count" 
							   value="" id="txt-per-count-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-qty-shortage-average" 
							   value="" id="txt-qty-shortage-average-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-value-shortage-average" 
							   value="" id="txt-value-shortage-average-' . $key . '">
					  </td>';
				echo '<td><input type="text" class="form-control font-color-1 txt-remarks" 
							   value="' . $item->name . '" id="txt-remarks-' . $key . '">
					  </td>';
				echo '<td>
					  <a  href="javascript: $(this).deleteItem(\'row-data-' . $key . '\');" title="Delete Item">
								<img class="img-button" src="../../assets/images/delete.png" alt="Delete">
					  </a>
					  </td>';
				echo '</tr>';
			}
		} else {
			echo '<td colspan="11"> -- No available data... --</td>';
		}
	?>
</table>