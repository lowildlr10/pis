<?php
	
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Purchase Request Details</title>
	<link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../assets/css/main.css">
</head>

<body style="background-color: #325e79;">
<?php
if (isset($_GET['selected'])) {
	$pid = $_GET['selected'];
	$qryPR_Info = "SELECT * 
				   FROM tblpr_info 
				   WHERE prID='".$pid."' 
				   ORDER BY LENGTH(infoID), infoID ASC";

	if ($resInfo = $conn->query($qryPR_Info)) {
		echo '<table class="table table-hover" id="tblInfo">';			
		echo '<tr><th width="10px">Item #</th>';
		echo '<th width="10px">QTY</th>';
		echo '<th width="40px">Unit of Issue</th>';
		echo '<th width="300px">Item Description</th>';
		echo '<th width="40px">Stock No.</th>';
		echo '<th width="90px">Estimated Unit Cost</th>';
		echo '<th width="90px">Estimated Cost</th>';
		echo '</tr>';
		$itemNo = 0;
		$total = 0;

		while ($data = $resInfo->fetch_object()) {
			$itemNo++;
			echo '<tr>';
			echo '<td>'.$itemNo.'</td>';
			echo '<td>'.$data->quantity.'</td>';
			echo '<td>'.$data->unitIssue.'</td>';
			echo '<td style="text-align: left; padding-left: 10px;">'.$data->itemDescription.'</td>';
			echo '<td>'.$data->stockNo.'</td>';
			echo '<td style="text-align:right; padding-left: 1em;">' . number_format($data->estimateUnitCost,2,'.',',') . '</td>';
			echo '<td style="text-align:right; padding-left: 1em;">' . number_format($data->estimateTotalCost,2,'.',',') . '</td>';
			echo '<tr>';
			$total += $data->estimateTotalCost; 
		}

		echo '<tr>
				 <td colspan="7" style="text-align:right; padding-left: 1em;">
				 	 <strong>
				 	 	 <font>Total:</font> ' . number_format($total,2) . 
				 	 '</strong>
				 </td>
			  </tr>';
		$qryRemarks = "SELECT remarks, cancelled 
					   FROM tblpr 
					   WHERE prID='".$pid."'";

		if ($remInfo = $conn->query($qryRemarks)) {
			$rem = $remInfo->fetch_object();

			if ($rem->remarks || $rem->cancelled) {
				echo '<tr><td colspan="7" align="center"><div class="msg">'.$rem->remarks.'';
				if (!empty($rem->cancelled)) {
					echo '<br />Cancelled: '.$rem->cancelled.'<br />';
				}
				echo '</div></td></tr>';
			}
		}
		
		echo '</table>';			
	}
}
?>
</body>
</html>
