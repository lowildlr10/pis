<?php

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
	$prID = $_GET['selected'];
	$bID = $_GET['bid'];
	$fCount = 0;
	$arBidder = array();
	$arLists = array();
	$groupData = array();
	$groupNumber = array();
	$qryGroupNumber = "SELECT groupNo, infoID FROM tblpr_info 
					   WHERE prID='".$prID."' 
					   ORDER BY LENGTH(infoID), infoID ASC";		 
	$groupNo = $conn->query($qryGroupNumber);

	while ($grp = $groupNo->fetch_object()) {
		$groupNumber[] = $grp->groupNo;
		$groupData[] = array("grpNo" => $grp->groupNo, "infoID" => $grp->infoID);
	}

	$groupNumber = array_unique($groupNumber);

	foreach ($groupNumber as $grpNo) {
		$curItem = "";
		$arrItem = array();
		$arrBidQuotes = array();
		$ctr = 0;
		$withJO = 0;

		foreach ($groupData as $grpData) {
			$arBidder = array();

			if ($grpData["grpNo"] == $grpNo) {
				$qryPRItems = "SELECT tblpr_info.itemDescription,tblpr_info.quantity,tblpr_info.unitIssue, 
			    					  tblpr_info.groupNo, tblpr_info.infoID,tblpr_info.awardedTo, 
			    					  tblbids_quotations.bidID, 
			    					  tblbids_quotations.remarks, tblbids_quotations.amount, 
			    					  tblbids_quotations.bidderID 
			    		       FROM tblpr 
			    		       Inner Join tblpr_info 
			    		       ON tblpr.prID = tblpr_info.prID 
			    		       Left Join tblbids_quotations 
			    		       ON tblpr_info.infoID = tblbids_quotations.infoID 
			    		       Left Join tblbidders 
			    		       ON tblbids_quotations.bidderID = tblbidders.bidderID 
			    		       WHERE tblpr.prID='".$prID."' 
			    		       AND tblpr_info.awardedTo='".$bID."' 
			    		       AND tblpr_info.groupNo='".$grpNo."' 
			    		       ORDER BY LENGTH(tblpr_info.infoID), tblpr_info.infoID ASC, tblbidders.company_name ASC";
			    $qryBidders = $conn->query("SELECT DISTINCT  bq.bidderID, b.company_name 
								FROM tblbids_quotations bq 
								INNER JOIN tblbidders b 
								ON bq.bidderID=b.bidderID 
								WHERE prID='". $prID ."' 
								AND infoID='" . $grpData["infoID"] . "'
								ORDER BY b.company_name ASC") 
							or die(mysqli_error($conn));
			    break;
			}
		}

		if (mysqli_num_rows($qryBidders)) {
			$i = 0;

			while ($list = $qryBidders->fetch_object()) {
				$arBidder[$i] = "bid=".$list->bidderID."&name=".$list->company_name;
				$arLists[$i] = $list->bidderID;
				$i++; 			
			}
		}

		$resQry = $conn->query($qryPRItems);

		if (mysqli_num_rows($resQry)) {
			echo '
			<table class="table table-bordered table-hover" id="tblInfo" align="center">
				<tr>
					<th width="3%">
						Quantity
					</th>
					<th width="37%">
						Item Description
					</th>';		

			if (count($arBidder)) {
				foreach ($arBidder as $cname) {
					parse_str($cname);
					echo '
					<th>'.
						$name.'
					</th>';
				}

				echo '
					<th width="20%">
						Awarded to
					</th>';
			} else {
				echo '
					<th width="60%">
						No Suppliers
					</th>';
			}

			echo '
				</tr>';

			while ($data = $resQry->fetch_object()) {		
				$arrBidQuotes[$fCount] = $data->bidID;
				$fCount++;

				if ($curItem != $data->infoID) {
					$curItem = $data->infoID;
					$arrItem[$ctr] = $data->infoID;								
					$ctr++;

					$bg = '';
					echo '<tr id=row_0>';

					if ($data->unitIssue == 'J.O.' || strtolower($data->unitIssue) == 'job order' || 
						strtolower($data->unitIssue) == 'work order') {		
						echo '<td '.$bg.'>'.$data->unitIssue;
						$withJO = 1;
					} else {
						echo '<td '.$bg.'>'.$data->quantity.'&nbsp;'.$data->unitIssue;
					}

					echo '</td>';
					echo '<td align="left" '.$bg.'>';
					$item = $data->itemDescription;
					echo (count_chars($item)>200)?''.substr($data->itemDescription,0,200).'...':$data->itemDescription;
					echo '</td>';								
				}

				if (count($arBidder)) {				
					echo '
						<td align="center" '.$bg.'>
							<b>'.number_format($data->amount,'2','.',',').'</b>
							<p align="center">'.$data->remarks.'</p>
						</td>';				
				} else {
					echo '<td '.$bg.'>&nbsp;</td></tr>';
				}

				if ($fCount == count($arBidder)) {
					echo '<td '.$bg.'><strong>';

					foreach ($arBidder as $cname) {
						parse_str($cname);

						if (count_chars($name) > 15) {
							$name = substr($name,0,15)."...";
						}
						echo $data->awardedTo==$bid?' '.$name.'':'';
					}
					//echo $arBidder1($data->awardedTo);
					echo '</strong></td>';
					echo '</tr>';
					$fCount = 0;
				}
			}//end while
		}
	  	
  echo '
	  		</td>
	  	</tr>';
	}
}
?>

 	</table>

</body>
</html>
