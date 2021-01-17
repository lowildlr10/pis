<?php

function generateAbstract($pid, $conn, $chairman, $viceChairman,
						  $member1, $member2, $alternateMember,
						  $regionalDirector, $endUser, $toggleAlternateMember,
						  $toggleSecondMember, $dir,
						  $increaseFontSize = 0, $pageHeight = 330, $pageWidth = 216) {
	$items = array();
	$groupData = array();
	$groupNumber = array();
	$pid = base64_decode($_REQUEST['print']);
	$qtn = "";
	$abstractDate = "";
	$qryGroupNumber = "SELECT groupNo, infoID 
					   FROM tblpr_info 
					   WHERE prID='".$pid."' 
					   ORDER BY LENGTH(infoID), infoID ASC";	
	$groupNo = $conn->query($qryGroupNumber);
	$qryDate = 	$conn->query("SELECT abstractDate 
							  FROM tblpr 
							  WHERE prID='".$pid."'")
							  or die(mysqli_error($conn));
	$_qryDate = $qryDate->fetch_object();
	$abstractDate = $_qryDate->abstractDate;

	if (isset($_REQUEST['qtn'])) {
		$qtn = $_REQUEST['qtn'];
	}

	while ($grp = $groupNo->fetch_object()) {
		$groupNumber[] = $grp->groupNo;
		$groupData[] = array("grpNo" => $grp->groupNo, "infoID" => $grp->infoID);
	}

	$groupNumber = array_unique($groupNumber);

	$columItemNo = $pageHeight * 0.03334;
	$columQNTY = $pageHeight * 0.036364;
	$columUNIT = $pageHeight * 0.045455;

	$pdf = new PDF('L','mm',array($pageHeight, $pageWidth));

	foreach ($groupNumber as $grpNo) {
		foreach ($groupData as $grpData) {
			if ($grpData["grpNo"] == $grpNo) {
				$_qryBidders = "SELECT DISTINCT  bq.infoID, bq.bidderID, b.company_name 
								FROM tblbids_quotations bq INNER JOIN tblbidders b 
								ON bq.bidderID=b.bidderID 
								WHERE prID='".$pid."' 
								AND infoID='".$grpData["infoID"]."' 
								ORDER BY b.company_name ASC";
				
				$_qryCountGroup = "SELECT prID, infoID
								  FROM tblbids_quotations
								  WHERE prID='" . $pid . "'
								  AND infoID='" . $grpData["infoID"] . "'";
				
				break;
			}
		}

		$qryBidders = $conn->query($_qryBidders);
		$qryCountGroup = $conn->query($_qryCountGroup);
		$bidderCount =  mysqli_num_rows($qryCountGroup);
		
		$totalWidthDisplay = $pageHeight * 0.945;
		$columParticulars = ($totalWidthDisplay) * 0.29;
		$columBidderAwarded = ($totalWidthDisplay * 0.59) / ($bidderCount + 1);
		$fontSize1 = 9;
		$fontSize2 = 10;

		if ($bidderCount >= 1 && $bidderCount <= 3) {
			$columParticulars = ($totalWidthDisplay) * 0.29;
			$columBidderAwarded = ($totalWidthDisplay * 0.59) / ($bidderCount + 1);
			$fontSize1 = 9;
			$fontSize2 = 10;
		} else if ($bidderCount >= 4 && $bidderCount <= 5) {
			$columParticulars = ($totalWidthDisplay) * 0.24;
			$columBidderAwarded = ($totalWidthDisplay * 0.64) / ($bidderCount + 1);
			
			if ($bidderCount == 4) {
				$fontSize1 = 9;
				$fontSize2 = 10;
			} else if ($bidderCount == 5) {
				$fontSize1 = 7;
				$fontSize2 = 8;
			}
			
		} else if ($bidderCount == 6) {
			$columParticulars = ($totalWidthDisplay) * 0.20;
			$columBidderAwarded = ($totalWidthDisplay * 0.68) / ($bidderCount + 1);
			$fontSize1 = 7;
			$fontSize2 = 8;
		} else if ($bidderCount > 6) {
			$columParticulars = ($totalWidthDisplay) * 0.20;
			$columBidderAwarded = ($totalWidthDisplay * 0.68) / ($bidderCount + 1);
			$fontSize1 = 6;
			$fontSize2 = 7;
		}
		
		$columnTitleWidth = array($columItemNo, 
								  $columQNTY, 
								  $columUNIT, 
								  $columParticulars, 
								  $columBidderAwarded); 	    // Column Widths for ITEM NO., QNTY, UNIT, PARTICULARS, BIDDERS, AWARDED TO
		$columnDataWidth = array($columnTitleWidth[0], 			// Column Widths for ITEM NO. (Data)
								 $columnTitleWidth[1], 			// Column Widths for QNTY (Data)
								 $columnTitleWidth[2],  		// Column Widths for UNIT (Data)
								 $columnTitleWidth[3] * 0.76, 	// Column Widths for Particulars (Right-side) (Data)
								 $columnTitleWidth[3] * 0.24, 	// Column Widths for ABC(UNIT) Particulars (Data)
								 $columnTitleWidth[4] / 2, 		// Column Widths for UNIT COST and TOTAL COST (Data)
								 $columnTitleWidth[4]);			// Column Widths for BIDDER and AWARDED TO (Data)

		
		$pdf->AliasNbPages();
		$pdf->AddPage('L');
		$xCoor = $pdf->GetX();
		$yCoor = $pdf->GetY();

		####################################################################################################################################
							  							      # DOCUMENT HEADER # 
		####################################################################################################################################

		$pdf->Cell($pageHeight * 0.8334,'4',"",'');
		$pdf->Cell($pageHeight * 0.1116,'1',"","TLR");
		$pdf->Ln();
		$pdf->SetFont('Arial','B', 9);
		$pdf->Cell($pageHeight * 0.8334,'4',"",'');
		$pdf->Cell($pageHeight * 0.1116,'5',"  FM-FAS-PUR F07","LR");
		$pdf->Ln();
		$pdf->SetFont('Arial','', 8);
		$pdf->Cell($pageHeight * 0.8334,'2',"",'');
		$pdf->Cell($pageHeight * 0.1116,'2',"  Revision 2","LR");
		$pdf->Ln();
		$pdf->SetFont('Arial','', 8);
		$pdf->Cell($pageHeight * 0.8334,'4',"",'');
		$pdf->Cell($pageHeight * 0.1116,'5','  08-06-18',"LR");
		$pdf->Ln();
		$pdf->Cell($pageHeight * 0.8334,'2',"",'');
		$pdf->Cell($pageHeight * 0.1116,'1','',"BLR");

		$pdf->Ln();

		$pdf->SetXY($xCoor, $yCoor);
		$pdf->Image($dir . '/resources/assets/images/dostlogo.jpg' ,$xCoor ,$yCoor+1,14,0,'JPEG');
		$pdf->setXY($xCoor + 15,$yCoor);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell($pageHeight * 0.15152,'4','Republic of the Philippines','');
		$pdf->Ln();
		$pdf->SetX($pdf->GetX() + 15);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell($pageHeight * 0.15152,'4','DEPARTMENT OF SCIENCE AND TECHNOLOGY','');
		$pdf->Ln();
		$pdf->SetX($pdf->GetX() + 15);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell($pageHeight * 0.15152,'4','Cordillera Administrative Region','');
		$pdf->Ln();
		$pdf->SetX($pdf->GetX() + 15);
		$pdf->Cell($pageHeight * 0.15152,'4','Km. 6, La Trinidad, Benguet','');
		$pdf->setY($yCoor+25);
		$pdf->SetFont('Arial','',10);

		####################################################################################################################################
							  							  # END OF DOCUMENT HEADER # 
		####################################################################################################################################


		####################################################################################################################################
							  								      # BODY # 
		####################################################################################################################################

		$pdf->Cell($pageHeight * 0.85, '5', "Date: ", "", "", "R");
        $pdf->Cell($pageHeight * 0.08, '5', $abstractDate, "B", "", "C");
		$pdf->Ln();

		// TABLE TITLE
		$pdf->SetFont('Arial','', 10 + ($increaseFontSize * 10));
		$pdf->Cell($pageHeight * 0.948, 5, 'ABSTRACT OF QUOTATION', "", "",'C');
		$pdf->Ln(10);
		
		########################### TABLE COLUMN TITLES ###########################

	#====================================================
	// 1ST ROW OF CELL/S

		$staticTitleTable = array();
		$staticTitleTable_Aligns = array();
		$staticFontStyle = array();
		$staticFontSize = array();
		$staticColumnWidth = array();

		$titleTable = array("ITEM NO.", "QTY", "UNIT", "P A R T I C U L A R S", "ABC\n (Unit Cost)");
		$fontStyle = array("", "", "", "", "");
		$fontSize = array($fontSize1 + ($increaseFontSize * $fontSize1), $fontSize2 + ($increaseFontSize * $fontSize2), 
						  $fontSize2 + ($increaseFontSize * $fontSize2), $fontSize2 + ($increaseFontSize * $fontSize2),
                          $fontSize2 + ($increaseFontSize * $fontSize2));
		$aligns = array('C', 'C', 'C', 'C', 'C', 'C');
		$columnWidths = array($columnDataWidth[0], $columnDataWidth[1], $columnDataWidth[2], 
                              $columnDataWidth[3], $columnDataWidth[4]);

		if (mysqli_num_rows($qryBidders)) {
			$bidderLists = array();

			while ($list = $qryBidders->fetch_object()) {
				$bidderLists[] = array($list->bidderID, $list->company_name);
				$titleTable[] = $list->company_name;
				$fontStyle[] = "";
				$fontSize[] = $fontSize2 + ($increaseFontSize * $fontSize2);
				$columnWidths[] = $columnTitleWidth[4];
				$aligns[] = "C";
			}
		}

		$titleTable[] = "AWARDED TO";
		$borderSettings[] = "LR";
		$fontStyle[] = "";
		$fontSize[] = $fontSize2 + ($increaseFontSize * $fontSize2);
		$columnWidths[] = $columnTitleWidth[4];
		$aligns[] = "C";

		$staticTitleTable = $titleTable;
		$staticTitleTable_Aligns = $aligns;
		$staticFontStyle = $fontStyle;
		$staticFontSize = $fontSize;
		$staticColumnWidth = $columnWidths;

		/*
		$pdf->SetWidths($staticColumnWidth);
		$pdf->SetAligns($staticTitleTable_Aligns);
		$pdf->CustomRow($staticTitleTable, 5, $staticFontStyle, $staticFontSize);
		*/

		$pdf->SetWidths($columnWidths);
		$pdf->SetAligns($aligns);
		$pdf->CustomRow($titleTable, 5, $fontStyle, $fontSize);

		unset($titleTable);
		unset($fontStyle);
		unset($fontSize);
		unset($columnWidths);
		unset($aligns);

	#====================================================
	// 2ND ROW OF CELL/S

		$titleTable = array("", "", "", "", "");
		$fontStyle = array("", "", "", "", "B");
		$fontSize = array($fontSize1 + ($increaseFontSize * $fontSize1), $fontSize2 + ($increaseFontSize * $fontSize2), 
						  $fontSize2 + ($increaseFontSize * $fontSize2), $fontSize2 + ($increaseFontSize * $fontSize2));
		$fontSize = array($fontSize1 + ($increaseFontSize * $fontSize1), $fontSize1 + ($increaseFontSize * $fontSize1), 
						  $fontSize1 + ($increaseFontSize * $fontSize1), $fontSize1 + ($increaseFontSize * $fontSize1), 
						  $fontSize1 + ($increaseFontSize * $fontSize1));
		$columnWidths = array($columnDataWidth[0], $columnDataWidth[1], $columnDataWidth[2], 
							  $columnDataWidth[3], $columnDataWidth[4]);
		$aligns = array('C', 'C', 'C', 'C', 'C');


		for ($i = 1; $i <= $bidderCount; $i++) { 
			$titleTable[] = "Unit Cost";
			$titleTable[] = "Total Cost";
			$columnWidths[] = $columnDataWidth[5];
			$columnWidths[] = $columnDataWidth[5];
			$aligns[] = "C";
			$aligns[] = "C";
			$fontStyle[] = "";
			$fontStyle[] = "";
			$fontSize[] = $fontSize1 + ($increaseFontSize * $fontSize1);
			$fontSize[] = $fontSize1 + ($increaseFontSize * $fontSize1);
		}

		$titleTable[] = "";
		$fontStyle[] = "";
		$fontSize[] = $fontSize2;
		$columnWidths[] = $columnDataWidth[6];
		$aligns[] = "C";

		$pdf->SetWidths($columnWidths);
		$pdf->SetAligns($aligns);
		$pdf->CustomRow2($titleTable, 5, $fontStyle, $fontSize);

		unset($titleTable);
		unset($fontStyle);
		unset($fontSize);
		unset($columnWidths);
		unset($aligns);

	#====================================================
	// DATA
		
		$pdf->SetFont('Arial', '', $fontSize2 + ($increaseFontSize * $fontSize2));
		$columnWidths = array($columnDataWidth[0], $columnDataWidth[1], $columnDataWidth[2], 
							  $columnDataWidth[3], $columnDataWidth[4]);
		$aligns = array('R', 'R', 'L', 'L', 'R');

		for ($j = 0; $j < ($bidderCount * 2); $j++) { 
			$columnWidths[] = $columnDataWidth[5];
			$aligns[] = "C";
		}

		$columnWidths[] = $columnDataWidth[6];
		$aligns[] = "C";

		$pdf->SetWidths($columnWidths);
		$pdf->SetAligns($aligns);

		$i = 0;
		$tempInfoNo = 0;
		$_columnCount = 5;
		$columnCount = 6 + ($bidderCount * 2);

		foreach ($groupData as $grpData) {
			if ($grpData["grpNo"] == $grpNo) {
				$qryPRItems = "SELECT info.itemDescription, info.quantity, 
									  info.unitIssue, info.awardedRemarks, 
									  info.groupNo, info.estimateUnitCost, 
									  info.infoID, info.awardedTo, 
									  qtn.bidID, info.estimateTotalCost, 
									  qtn.remarks, qtn.bidderID, 
									  qtn.amount, qtn.lamount 
							   FROM tblpr AS pr 
							   INNER JOIN tblpr_info AS info 
							   ON pr.prID = info.prID 
							   LEFT JOIN tblbids_quotations qtn 
							   ON info.infoID = qtn.infoID 
							   LEFT JOIN tblbidders bid 
							   ON qtn.bidderID = bid.bidderID 
							   WHERE pr.prID='".$pid."' 
							   AND info.quantity IS NOT NULL 
							   AND info.groupNo = '" . $grpNo . "' 
							   AND info.infoID = '" . $grpData["infoID"] . "'
							   AND qtn.amount IS NOT NULL 
							   
							   ORDER BY LENGTH(info.infoID), info.infoID ASC, bid.company_name ASC";
							   /* AND info.awardedTo <> 0 */

				$res = $conn->query($qryPRItems) or die(mysqli_error($conn));

				if ($res) {
				  	while ($list = $res->fetch_object()) {
						if ($tempInfoNo != $list->infoID) {
							$i++;
							$items[$i - 1] = array($i, 
									   	   		   $list->quantity, 
									   	   		   $list->unitIssue, 
									   	   		   $list->itemDescription, 
									   	   		   number_format($list->estimateUnitCost, 2));
							$tempInfoNo = $list->infoID;
						}

						if (!empty($list->remarks)) {
							$items[$i - 1][count($items[$i - 1])] = number_format($list->amount, 2) . 
								"\n(" . $list->remarks .")";
						} else {
							$items[$i - 1][count($items[$i - 1])] = number_format($list->amount, 2);
						}
						
						$items[$i - 1][count($items[$i - 1])] = number_format($list->lamount, 2);
						$_columnCount += 2;

						if ($_columnCount == $columnCount - 1) {
							if ($list->awardedTo != "" && !empty($list->awardedTo) && $list->awardedTo != "0") {
								$awardedToName = "";

								foreach ($bidderLists as $idBid) {
									if ($list->awardedTo == $idBid[0]) {
										$awardedToName = $idBid[1];
									}
								}

								if ($list->awardedRemarks != "") {
									$items[$i - 1][count($items[$i - 1])] = $awardedToName . "\n(" . $list->awardedRemarks . ")";
								} else {
									$items[$i - 1][count($items[$i - 1])] = $awardedToName;// . " " . $list->awardedRemarks;
								}
							} else {
								$items[$i - 1][count($items[$i - 1])] = $list->awardedRemarks;
							}

							$_columnCount = 5;
						}
					}
				}
			}
		}

		if (count($items) > 0){
			foreach ($items as $item) {
				foreach ($bidderLists as $bidderList) {
					$tempBidderID = explode("[", $item[count($item) - 1]);
					$tempBidderCount = count($tempBidderID);

					if ($tempBidderID[0] == $bidderList[0]) {
						if ($tempBidderCount == 1) {
							$item[count($item) - 1] = $bidderList[1];
						} else if ($tempBidderCount > 1) {
							$item[count($item) - 1] = $bidderList[1] . " \n($tempBidderID[1])";
						}
						
						break;
					} else if ($tempBidderID[0] == "0") {
						if ($tempBidderCount == 1) { 
							$item[count($item) - 1] = "";
						} else if ($tempBidderCount > 1) {
							$item[count($item) - 1] = $tempBidderID[1];
						}
					}
				}

				$pdf->Row($item);
			}
			
			$aligns[3] = "C";
			$pdf->SetAligns($aligns);

			for ($y = 0; $y < 2; $y++) { 

				if ($y == 0) {
					$items = array("", "", "", "****** Nothing Follows ******", "");
				} else {
					$items = array("", "", "", "", "");
				}

				for ($x = 0; $x < ($bidderCount * 2) + 1; $x++) { 
					$items[] = "";
				}

				$pdf->Row($items);
				//unset($items);
				$items = array();
			}

			$pdf->Ln(2.5); // LINE BREAK
			$pdf->SetFont('Arial', '', 9 + ($increaseFontSize * 9));
			//$pdf->SetTextColor(128, 128, 128);
			$pdf->Cell(0, 0, "We hereby certify that we have witnessed the opening of bids/quotations and that the prices/quotations contained herein are the true and correct.");
			$pdf->Ln(5); // LINE BREAK

			unset($titleTable);
			unset($fontStyle);
			unset($fontSize);
			unset($columnWidths);
			unset($aligns);

            $pdf->Cell($totalWidthDisplay * 0.948, 5, "Recommendation:", '', 0, 'L', 0);
            $pdf->Ln(10);

            $pdf->Cell($totalWidthDisplay * 0.035, 2, "", '', 0, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.92, 2, "", 'B', 1, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.033, 2, "", '', 0, 'L', 0);
            $pdf->Ln();

            $pdf->Cell($totalWidthDisplay * 0.035, 2, "", '', 0, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.92, 2, "", 'B', 1, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.033, 2, "", '', 0, 'L', 0);
            $pdf->Ln();

            $pdf->Cell($totalWidthDisplay * 0.035, 2, "", '', 0, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.92, 2, "", 'B', 1, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.033, 2, "", '', 0, 'L', 0);
            $pdf->Ln();

            $pdf->Cell($totalWidthDisplay * 0.035, 2, "", '', 0, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.92, 2, "", 'B', 1, 'L', 0);
            $pdf->Cell($totalWidthDisplay * 0.033, 2, "", '', 0, 'L', 0);

            $pdf->Ln(5); // LINE BREAK

		#====================================================
		// BIDS AND AWARDS COMMITTEE

			$pdf->SetFont('Arial', 'B', 10 + ($increaseFontSize * 10));
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(5); // LINE BREAK

			if ($toggleAlternateMember == "Yes") {
				$pdf->Cell($totalWidthDisplay * 0.85, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
				$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
				$pdf->Cell($totalWidthDisplay * 0.25, 5, "", '', 0, 'L', 0);
				$pdf->Ln(); // LINE BREAK
				$pdf->SetFont('Arial', 'B', 9 + ($increaseFontSize * 9));
				$pdf->Cell(0, 8, " ", '', 0, 'L', 0);
				$pdf->Ln(); // LINE BREAK

				$fontStyle = array("BU", "BU", "BU", "BU", "BU");
				$fontSize = array(9, 9, 9, 9, 9);
				$columWidth = $totalWidthDisplay / 5;
				$columnWidths = array($columWidth, $columWidth, $columWidth, $columWidth, $columWidth);
				$aligns = array('C', 'C', 'C', 'C', 'C');
				$items = array($chairman, $viceChairman, $member1, $member2, $alternateMember);
				
				$pdf->SetWidths($columnWidths);
				$pdf->SetAligns($aligns);
				$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
				unset($items);
				unset($fontStyle);

				$fontStyle = array("", "", "", "", "");
				$items = array("Chairperson", "Vice Chairperson", "Member", "Member", "Alternate Member");
				$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
				unset($items);
				unset($fontStyle);
				unset($fontSize);
			} else {
				if ($toggleSecondMember == "Yes") {
					$pdf->Cell($totalWidthDisplay * 0.83, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
					$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
					$pdf->Cell($totalWidthDisplay * 0.32, 5, "", '', 0, 'L', 0);
					$pdf->Ln(); // LINE BREAK
					$pdf->SetFont('Arial', 'B', 9 + ($increaseFontSize * 9));
					$pdf->Cell(0, 8, " ", '', 0, 'L', 0);
					$pdf->Ln(); // LINE BREAK

					$fontStyle = array("BU", "BU", "BU", "BU");
					$fontSize = array(9, 9, 9, 9);
					$columWidth = $totalWidthDisplay / 4;
					$columnWidths = array($columWidth, $columWidth, $columWidth, $columWidth);
					$aligns = array('C', 'C', 'C', 'C');
					$items = array($chairman, $viceChairman, $member1, $member2);
					
					$pdf->SetWidths($columnWidths);
					$pdf->SetAligns($aligns);
					$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
					unset($items);
					unset($fontStyle);

					$fontStyle = array("", "", "", "");
					$items = array("Chairperson", "Vice Chairperson", "Member", "Member");
					$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
					//unset($items);
					$items = array();
					unset($fontStyle);
					unset($fontSize);
				} else if ($toggleSecondMember == "No") {
					$pdf->Cell($totalWidthDisplay * 0.83, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
					$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
					$pdf->Cell($totalWidthDisplay * 0.32, 5, "", '', 0, 'L', 0);
					$pdf->Ln(); // LINE BREAK
					$pdf->SetFont('Arial', 'B', 9 + ($increaseFontSize * 9));
					$pdf->Cell(0, 8, " ", '', 0, 'L', 0);
					$pdf->Ln(); // LINE BREAK

					$fontStyle = array("BU", "BU", "BU");
					$fontSize = array(9, 9, 9, 9);
					$columWidth = $totalWidthDisplay / 3;
					$columnWidths = array($columWidth, $columWidth, $columWidth);
					$aligns = array('C', 'C', 'C');
					$items = array($chairman, $viceChairman, $member1);
					
					$pdf->SetWidths($columnWidths);
					$pdf->SetAligns($aligns);
					$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
					unset($items);
					unset($fontStyle);

					$fontStyle = array("", "", "");
					$items = array("Chairperson", "Vice Chairperson", "Member");
					$pdf->CustomRow2($items, 5, $fontStyle, $fontSize, $toggleBorders = 0);
					//unset($items);
					$items = array();
					unset($fontStyle);
					unset($fontSize);
				}
				
			}
			
			$pdf->Ln(12); // LINE BREAK
			$pdf->SetFont('Arial', 'BU', 9 + ($increaseFontSize * 9));
			$pdf->Cell($pageHeight * 0.242, 5, $endUser, '', 0, 'C', 0);
			$pdf->SetFont('Arial', '', 9 + ($increaseFontSize * 9));
			$pdf->Ln(); // LINE BREAK
			$pdf->Cell($pageHeight * 0.242, 5, "END-USER", '', 0, 'C', 0); 

		#====================================================
		// END OF BODY
		}
	}
	
	$pdf->SetTitle('abstract_' . $qtn . '.pdf');

	if (!isset($_REQUEST['preview'])) {
		$pdf->Output('abstract_' . $qtn . '.pdf', 'D');
	} else {
		$pdf->Output('abstract_' . $qtn . '.pdf', 'I');
	}
}

?>