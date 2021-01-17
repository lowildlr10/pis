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
$divisionID = "";

if (isset($_REQUEST["startDate"])) {
	$startDate = $_REQUEST["startDate"];
}

if (isset($_REQUEST["endDate"])) {
	$endDate = $_REQUEST["endDate"];
}

if (isset($_REQUEST["divisionID"])) {
	$divisionID = $_REQUEST["divisionID"];
}

$dates = array();
$current = strtotime($startDate);
$last = strtotime($endDate);

while ($current <= $last) {
	$dates[] = date('m/d/Y', $current );
	$current = strtotime('+1 day', $current );
}

foreach ($dates as $date) {
	$qryPR = $conn->query("SELECT pr.prNo, pr.prDate, pr.abstractApprovalDate, pr.prID, pr.prStatus 
						   FROM tblpr pr 
						   INNER JOIN tblemp_accounts emp 
						   ON emp.empID = pr.requestBy 
						   INNER JOIN tblsections sec 
						   ON sec.sectionID = emp.sectionID 
						   WHERE pr.prDate LIKE '%$date%' 
						   AND emp.sectionID = '" . $divisionID . "' 
						   AND pr.prStatus <> 'pending' 
						   AND pr.prStatus <> 'for_posting' 
						   ORDER BY LENGTH(pr.prDate), pr.prDate ASC") 
						   or die(mysql_error($conn));

	while ($data = $qryPR->fetch_object()) {
		$tempData1 = array();
		$tempData2 = array();

		$qryPO = $conn->query("SELECT po.poApprovalDate, bid.company_name, class.classification, 
									  po.deliveryDate, po.poNo 
							   FROM tblpo_jo po 
							   INNER JOIN tblbidders bid 
							   ON bid.bidderID = po.awardedTo 
							   INNER JOIN tblclassifications class 
							   ON class.classID = bid.classID 
							   WHERE po.poNo LIKE '%" . $data->prNo . "%' 
							   AND po.prID ='" . $data->prID . "'")
							   or die(mysql_error($conn));

		$qryIAR = $conn->query("SELECT iar.iarDate, iar.invoiceNo, sig.name 
								FROM tbliar iar 
								INNER JOIN tblsignatories sig 
								ON sig.signatoryID = iar.inspectedBy 
								WHERE iar.prID = '" . $data->prID . "' 
								AND iar.iarNo LIKE '%" . $data->prNo . "%'")
								or die(mysql_error($conn));

		while ($data2 = $qryPO->fetch_object() ) {
			$data2->deliveryDate = preg_replace('/[^0-9]/', '', $data2->deliveryDate);

			$tempData1[] = array($data2->poApprovalDate, $data2->company_name, 
								 $data2->classification, $data2->deliveryDate);
		}

		while ($data3 = $qryIAR->fetch_object() ) {
			$tempData2[] = array($data3->iarDate, $data3->invoiceNo, $data3->name);
		}

		$items[] = array($data->prNo, $data->prDate, 
						 $data->abstractApprovalDate, 
						 $tempData1, $tempData2);
	}
}

?>

<table class="table table-bordered table-responsive">
	<tr>
		<th style="text-align: center;"> MO./YEAR </th>
		<th style="text-align: center;"> PURCHASE REQUEST NO. </th>
		<th style="text-align: center;"> P.R. DATE</th>
		<th style="text-align: center;"> DATE OF APPROVAL OF ABSTRACT OF BIDS </th>
		<th style="text-align: center;"> DATE OF APPROVAL OF P.O. </th>
		<th style="text-align: center;"> SUPPLIER </th>
		<th style="text-align: center;"> PARTICULARS </th>
		<th style="text-align: center;"> DATE P.O. RECEIVED </th>
		<th style="text-align: center;"> DATE DELIVERED </th>
		<th style="text-align: center;"> INVOICE NO. </th>
		<th style="text-align: center;"> INSPECTED BY </th>
		<th style="text-align: center;"> REQUIRED NO. OF DAYS </th>
		<th style="text-align: center;"> ACTUAL NO. OF DAYS </th>
		<th style="text-align: center;"> DIFFERENCE </th>
		<th style="text-align: center;"> REMARKS </th>
		<th style="text-align: center;"> </th>
	</tr>

	<?php
		if (count($items) > 0) {
			foreach ($items as $key => $item) {
				$prDate = "";
				$current = strtotime($item[1]);
				$last = strtotime($item[1]);

				while ($current <= $last) {
					$prDate = date('m/Y', $current );
					$current = strtotime('+1 day', $current );
				}

				echo '<tr class="row_data" id="row-data-' . $key . '">';
				// MO./Year
				echo '<td>
						<input type="text" class="form-control font-color-1 txt-mo-year" 
							   value="' . $prDate . '" disabled="disabled">
					  </td>';
				// Purchase Request No.
				echo '<td>
						<input type="text" class="form-control font-color-1 txt-pr-no" 
							   value="' . $item[0] . '" disabled="disabled">
					  </td>';
				// P.R. Date
				echo '<td>
						<input type="text" class="form-control font-color-1 txt-pr-date" value="' . $item[1] . '">
					  </td>';
				// Date of Approval of Abstract of Bids
				echo '<td>
						<input type="text" class="form-control font-color-1 txt-date-abstract" value="' . $item[2] . '">
					  </td>';
				// Date of Approval of P.O.
				echo '<td>';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key1 => $valPO) {
						$valPO[0] = preg_replace('/\s+/', '', $valPO[0]);

						if (!empty($valPO[0]) && $valPO[0] != "") {
							//echo $valPO[0];
							echo '<input type="text" class="form-control font-color-1 txt-date-po" value="' . $valPO[0] . '">';

							if ($key1 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-date-po" value=""> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-date-po" value="">';
				}
					
				echo '</td>';
				// Supplier
				echo '<td style="text-align: left;">';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key2 => $valPO) {
						if (!empty($valPO[1])) {
							echo '<input type="text" class="form-control font-color-1 txt-supplier" value="' . $valPO[1] . '">';

							if ($key2 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-supplier" value=""> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-supplier" value="">';
				}

				echo '</td>';
				// Paticulars
				echo '<td style="text-align: left;">';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key3 => $valPO) {
						if (!empty($valPO[2])) {
							//echo $valPO[2];
							echo '<input type="text" class="form-control font-color-1 txt-particulars" value="' . $valPO[2] . '">';

							if ($key3 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-particulars" value=""> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-particulars" value="">';
				}

				echo '</td>';
				// Date P.O. Recieved
				echo '<td>';

				if (count($item[4]) > 0) {
					foreach ($item[4] as $key4 => $valIAR) {
						if (!empty($valIAR[0])) {
							//echo $valIAR[0];
							echo '<input type="text" class="form-control font-color-1 txt-date-po-recieved" 
										 value="" id="txt-date-po-recieved-' . $key . '-' . $key4 . '">';

							if ($key4 != count($item[4]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-date-po-recieved" value=""
									     id="txt-date-po-recieved-' . $key . '-' . $key4 . '"> <br>';
						}
					}
				} else {
					if (count($item[3]) > 0) {
						foreach ($item[3] as $_key1 => $valPO) {
							echo '<input type="text" class="form-control font-color-1 txt-date-po-recieved" value=""
										 id="txt-date-po-recieved-' . $key . '-' . $_key1 . '">';

							if ($_key1 != count($item[3]) - 1) {
								echo ", ";
							}
						}
					} else {
						echo '<input type="text" class="form-control font-color-1 txt-date-po-recieved" value=""
										 id="txt-date-po-recieved-' . $key . '-0">';
					}
				}

				echo '</td>';
				// Date Delivered
				echo '<td style="text-align: left;">';

				if (count($item[4]) > 0) {
					foreach ($item[4] as $key5 => $valIAR) {
						if (!empty($valIAR[0])) {
							//echo $valIAR[0];
							echo '<input type="text" class="form-control font-color-1 txt-date-delivered" 
										 value="' . $valIAR[0] . '" id="txt-date-delivered-' . $key . '-' . $key5 . '">';

							if ($key5 != count($item[4]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-date-delivered" 
										 value="" id="txt-date-delivered-' . $key . '-' . $key5 . '"> <br>';
						}
					}
				} else {
					if (count($item[3]) > 0) {
						foreach ($item[3] as $_key2 => $valPO) {
							echo '<input type="text" class="form-control font-color-1 txt-date-po-recieved" value=""
										 id="txt-date-delivered-' . $key . '-' . $_key2 . '">';

							if ($_key2 != count($item[3]) - 1) {
								echo ", ";
							}
						}
					} else {
						echo '<input type="text" class="form-control font-color-1 txt-date-delivered" 
								 value="" id="txt-date-delivered-' . $key . '-0">';
					}
				}

				echo '</td>';
				// Invoice No.
				echo '<td style="text-align: left;">';

				if (count($item[4]) > 0) {
					foreach ($item[4] as $key6 => $valIAR) {
						$valIAR[1] = preg_replace('/\s+/','',$valIAR[1]);

						if (!empty($valIAR[1]) || $valIAR[1] != "") {
							//echo $valIAR[1];
							echo '<input type="text" class="form-control font-color-1 txt-invoice-no" value="' .  $valIAR[1] . '">';
						} else {
							//echo "OR";
							echo '<input type="text" class="form-control font-color-1 txt-invoice-no" value="OR">';
						}

						if ($key6 != count($item[4]) - 1) {
							echo ", ";
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-invoice-no" value="">';
				}

				echo '</td>';
				// Inspected By
				echo '<td style="text-align: left;">';

				if (count($item[4]) > 0) {
					foreach ($item[4] as $key7 => $valIAR) {
						if (!empty($valIAR[2])) {
							//echo $valIAR[2];
							echo '<input type="text" class="form-control font-color-1 txt-inspected-by" value="' . $valIAR[2] . '">';

							if ($key7 != count($item[4]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-inspected-by" value=""> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-inspected-by" value=""> <br>';
				}

				echo '</td>';
				// Required No. of Days
				echo '<td style="text-align: left;">';

				if (count($item[4]) > 0) {
					foreach ($item[3] as $key8 => $valPO) {
						if (!empty($valPO[3])) {
							//echo $valPO[3];
							echo '<input type="text" class="form-control font-color-1 txt-required-days" 
										 value="' . $valPO[3] . '" id="txt-required-days-' . $key . '-' . $key8 . '">';

							if ($key8 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-required-days" 
										 value="" id="txt-required-days-' . $key . '-' . $key8 . '"> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-required-days" 
					      	     value="" id="txt-required-days-' . $key . '-0">';
				}

				echo '</td>';
				// Actual No. of Days
				echo '<td>';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key9 => $valPO) {
						if (!empty($valPO[2])) {
							//echo $valPO[2];
							echo '<input type="text" class="form-control font-color-1 txt-actual-days" 
								         value="" id="txt-actual-days-' . $key . '-' . $key9 . '">';

							if ($key9 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-actual-days" 
								         value="" id="txt-actual-days-' . $key . '-' . $key9 . '"> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-actual-days" 
								         value="" id="txt-actual-days-' . $key . '-0">';
				}

				echo '</td>';
				// Difference
				echo '<td>';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key10 => $valPO) {
						if (!empty($valPO[2])) {
							//echo $valPO[2];
							echo '<input type="text" class="form-control font-color-1 txt-difference" disabled="disabled"
									     value="" id="txt-difference-' . $key . '-' . $key10 . '">';

							if ($key10 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-difference" disabled="disabled"
									     value="" id="txt-difference-' . $key . '-' . $key10 . '">> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-difference" disabled="disabled"
									     value="" id="txt-difference-' . $key . '-0">';
				}

				echo '</td>';
				// Remarks
				echo '<td>';

				if (count($item[3]) > 0) {
					foreach ($item[3] as $key11 => $valPO) {
						if (!empty($valPO[2])) {
							//echo $valPO[2];
							echo '<input type="text" class="form-control font-color-1 txt-remarks" value="">';

							if ($key11 != count($item[3]) - 1) {
								echo ", ";
							}
						} else {
							echo '<input type="text" class="form-control font-color-1 txt-remarks" value=""> <br>';
						}
					}
				} else {
					echo '<input type="text" class="form-control font-color-1 txt-remarks" value="">';
				}

				echo '</td>';
				echo '<td>
					  <a  href="javascript: $(this).deleteItem(\'row-data-' . $key . '\');" title="Delete Item">
								<img class="img-button" src="../../assets/images/delete.png" alt="Delete">
					  </a>
					  </td>';
				echo '</tr>';
			}
		} else {
			echo '<td colspan="15"> -- No available data... --</td>';
		}
			
	?>
</table>