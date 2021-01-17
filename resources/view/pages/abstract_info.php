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
	$item = "";
	$prNo = "";

	if (isset($_GET['pr_no'])) {
		$prNo = $_GET['pr_no'];
	}

	if (isset($_GET['selected'])) {
		$prID = $_GET['selected'];
		$arBidder = array();
		$arLists = array();
		$groupNumber = array();
		$groupData = array();

		$qryGroupNumber = "SELECT groupNo, infoID FROM tblpr_info 
						   WHERE prID = '".$prID."' 
						   ORDER BY LENGTH(infoID), infoID ASC";		 
		$groupNo = $conn->query($qryGroupNumber);

		while ($grp = $groupNo->fetch_object()) {
			$groupNumber[] = $grp->groupNo;
			$groupData[] = array("grpNo" => $grp->groupNo, "infoID" => $grp->infoID);
		}

		$groupNumber = array_unique($groupNumber);
		$gCounter = 0;
		
		foreach ($groupNumber as $grpNo) {
			$qryPRItems = "SELECT tblpr.prID, tblpr_info.itemDescription,tblpr_info.quantity,tblpr_info.groupNo,
		    					  tblpr_info.unitIssue, tblpr_info.awardedRemarks, tblpr_info.infoID,
		    					  tblpr_info.awardedTo, tblbids_quotations.bidID, tblbids_quotations.remarks, 
		    					  tblbids_quotations.amount, tblbids_quotations.bidderID 
		    			   FROM tblpr 
		    			   INNER JOIN tblpr_info 
		    			   ON tblpr.prID = tblpr_info.prID 
		    			   Left Join tblbids_quotations 
		    			   ON tblpr_info.infoID = tblbids_quotations.infoID 
		    			   Left Join tblbidders 
		    			   ON tblbids_quotations.bidderID = tblbidders.bidderID 
		    			   WHERE tblpr.prID='".$prID."' 
		    			   AND tblpr_info.groupNo='".$grpNo."' 
		    			   ORDER BY LENGTH(tblpr_info.infoID), tblpr_info.infoID ASC, tblbidders.company_name ASC";
			$resQry = $conn->query($qryPRItems);

			foreach ($groupData as $grpData) {
				if ($grpData["grpNo"] == $grpNo) {
					$qryBidders = $conn->query("SELECT DISTINCT bq.bidderID, b.company_name 
												FROM tblbids_quotations bq 
												INNER JOIN tblbidders b 
												ON bq.bidderID = b.bidderID 
												WHERE bq.prID = '".$prID."' 
												AND infoID='" . $grpData["infoID"] . "'
												ORDER BY b.company_name ASC") 
												or die(mysqli_error($conn));
				}
			}

			if (mysqli_num_rows($qryBidders)) {
				while ($list = $qryBidders->fetch_object()) {
					$arBidder[] = "bid=".$list->bidderID."&name=".$list->company_name;
					$arLists[] = $list->bidderID;			
				}
			}
			
            
			echo '<table class="table table-bordered table-hover" cellpadding="4" cellspacing="1" 
						 id="tblInfo" width="97%" align="center">';	

			if (mysqli_num_rows($resQry)) {
				$gCounter++;

				echo '<tr>
					  	  <th colspan="' . (count($arBidder) + 3) .'" style="background-color: #286497; color: white;">
					  	  	  GROUP NUMBER ' . $gCounter . '
					  	  </th>
					  </tr>';
				echo '<tr>
					  	  <th width="5%">
					  	  	  Quantity
					  	  </th>
					  	  <th width="30%">
					  	  	  Item Description
					  	  </th>';

				if (isset($arBidder)) {
					if (count($arBidder) > 0) {
						foreach ($arBidder as $cname) {
							parse_str($cname);
							echo '<th>'.$name.'</th>';
						}

						echo '<th>Awarded to</th>';
					} else {
						echo '<th width="60%">No Suppliers</th>';
					}
				}

				echo '</tr>';
				$curItem = "";
				$arrItem = array();
				$arrBidQuotes = array();
				$ctr = 0;
				$fCount = 0;

				while ($data = $resQry->fetch_object()) {		
					$arrBidQuotes[$fCount] = $data->bidID;
					$fCount++;

					if ($curItem != $data->infoID) {
						$curItem = $data->infoID;
						$arrItem[$ctr] = $data->infoID;								
						$ctr++;
						echo '<tr id="row_0">';
						
						if ($data->unitIssue == 'J.O.' || strtolower($data->unitIssue) == 'job order' || 
								strtolower($data->unitIssue) == 'work order') {		
							echo '<td align="center">'.$data->unitIssue;
						} else {
							echo '<td align="center">'.$data->quantity.'&nbsp;'.$data->unitIssue;
						}

						echo '</td>';
						echo '<td align="left" style="padding-left: 30px; padding-right: 30px;">';
						echo (count_chars($item)>200)?''.substr($data->itemDescription,0,200).'...':$data->itemDescription;
						echo '</td>';								
					}

					if (isset($arBidder)) {
						if (count($arBidder) > 0) {

							if (!empty($data->remarks)) {
								echo '<td align="center"><b>'.number_format($data->amount, 2).'</b><p align="center">('.$data->remarks.')</p></td>';
							} else {
								echo '<td align="center"><b>'.number_format($data->amount, 2).'</b><p align="center"></p></td>';
							}
							
							if ($fCount == count($arBidder)) {

								echo '<td align="center"><strong>';	
								
								foreach ($arBidder as $cname) {
									parse_str($cname);

									echo $data->awardedTo==$bid?' '.$name.'':'';
								}

								if (!empty($data->awardedRemarks)) {
									echo '<br>(' . 
										 $data->awardedRemarks . 
										 ')</strong></td></tr>';
								} else {
									echo "</strong></td></tr>";
								}

								
								$fCount = 0;
							}
											
						} else {
							
							echo '<td><strong> -- N/A -- </strong></td></tr>';

						}

					}
				}//end while
			} else {
				echo '<tr>
						   <td>
						   <br>
						   <strong class="font-color-1"> ----- Print first the Canvass Form. ----- 
						   <br>
						   <a target="_parent" href="canvass.php?po_no=' . $prNo . '"> 
						   	  [Click here to create a canvass form.]
						   </a>
						   </strong><br><br>
						   </td>
					  </tr>';
			}

			$arBidder = [];
			$arLists = [];

			/*
			if (isset($arBidder)) {
				unset($arBidder);
			}

			if (isset($arLists)) {
				unset($arLists);
			}
			*/
		}
		
		?>

</table>

<?php
}
?>

</body>
</html>
