<?php

function generateAbstract($pid, $conn, $chairman, $viceChairman,
                          $member1, $member2, $member3, $endUser, $dir,
						  $increaseFontSize = 0, $pageHeight = 330, $pageWidth = 216) {
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

    //-------------------------------------------------------------------------------------------

	$pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('L', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F07');
    $pdf->setDocRevision('Revision 2');
    $pdf->setRevDate('02-06-18');

    //set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //Set margins
    $pdf->SetMargins(10, 35, 10);
    $pdf->SetHeaderMargin(10);

    //Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    //Set default font subsetting mode
    $pdf->setFontSubsetting(true);

    //Content
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
        $qryBidders1 = $conn->query($_qryBidders);
        $qryCountGroup = $conn->query($_qryCountGroup);
        $bidderCount =  mysqli_num_rows($qryCountGroup);

        $totalWidthDisplay = $pageHeight - 20; //$pageHeight * 0.945;
        $totalWidth1 = $totalWidthDisplay * 0.83;
        $totalWidth2 = $totalWidthDisplay * 0.17;
        $bidderTotalWidth = $totalWidth1 * 0.71;

        if ($bidderCount != 0) {
            $bidderWidth = $bidderTotalWidth / $bidderCount;
            $bWidth = 58.92 / $bidderCount;
        } else {
            $bidderWidth = $bidderTotalWidth / 3;
            $bWidth = 58.92 / 3;
        }

        $columnWidths = [3.32, 3.32, 3.32, 10.8, 3.32];
        $aligns = ['R', 'R', 'L', 'L', 'R'];
        $fontStyles = ['', '', '', '', ''];

        for ($i = 1; $i <= $bidderCount; $i++) {
            $columnWidths[] = $bWidth * 0.25;
            $columnWidths[] = $bWidth * 0.25;
            $columnWidths[] = $bWidth * 0.5;
            $aligns[] = "C";
            $aligns[] = "C";
            $aligns[] = "L";
            $fontStyles[] = "";
            $fontStyles[] = "";
            $fontStyles[] = "";
        }

        $columnWidths[] = 17;
        $aligns[] = 'C';
        $fontStyles[] = "";

        $tableData = [];
        $i = 0;
        $tempInfoNo = 0;
        $_columnCount = 5;
        $columnCount = 6 + ($bidderCount * 3);

        if (mysqli_num_rows($qryBidders1)) {
            $bidderLists = array();

            while ($_list = $qryBidders1->fetch_object()) {
                $bidderLists[] = array($_list->bidderID, $_list->company_name);
            }
                
        }

        foreach ($groupData as $grpData) {
            if ($grpData["grpNo"] == $grpNo) {
                $qryPRItems = "SELECT info.itemDescription, info.quantity, 
                                      info.unitIssue, info.awardedRemarks, 
                                      info.groupNo, info.estimateUnitCost, 
                                      info.infoID, info.awardedTo, 
                                      qtn.bidID, info.estimateTotalCost, 
                                      qtn.remarks, qtn.bidderID, 
                                      qtn.amount, qtn.lamount, qtn.specification 
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

                $res = $conn->query($qryPRItems) or die(mysqli_error($conn));

                if ($res) {
                    while ($list = $res->fetch_object()) {
                        if ($tempInfoNo != $list->infoID) {
                            $i++;
                            //$list->itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $list->itemDescription);
                            $tableData[$i - 1] = array($i, 
                                                   $list->quantity, 
                                                   $list->unitIssue, 
                                                   $list->itemDescription, 
                                                   number_format($list->estimateUnitCost, 2));
                            $tempInfoNo = $list->infoID;
                        }

                        $tableData[$i - 1][count($tableData[$i - 1])] = number_format($list->amount, 2);
                        $tableData[$i - 1][count($tableData[$i - 1])] = number_format($list->lamount, 2);
                        
                        if (empty($list->specification)) {
                            $list->specification = "";
                        }

                        //$list->specification = iconv('UTF-8', 'windows-1252//IGNORE', $list->specification);

                        if (!empty($list->remarks)) {
                            $tableData[$i - 1][count($tableData[$i - 1])] = $list->specification . "\n" .
                                                                    '*Remarks: ' . $list->remarks;
                        } else {
                            $tableData[$i - 1][count($tableData[$i - 1])] = $list->specification;
                        }
                        
                        $_columnCount += 3;
                        
                        if ($_columnCount == $columnCount - 1) {
                            if ($list->awardedTo != "" && !empty($list->awardedTo) && $list->awardedTo != "0") {
                                $awardedToName = "";

                                foreach ($bidderLists as $idBid) {
                                    if ($list->awardedTo == $idBid[0]) {
                                        $awardedToName = $idBid[1];
                                    }
                                }

                                if ($list->awardedRemarks != "") {
                                    $tableData[$i - 1][count($tableData[$i - 1])] = $awardedToName . "\n(" . $list->awardedRemarks . ")";
                                } else {
                                    $tableData[$i - 1][count($tableData[$i - 1])] = $awardedToName;// . " " . $list->awardedRemarks;
                                }
                            } else {
                                $tableData[$i - 1][count($tableData[$i - 1])] = $list->awardedRemarks;
                            }

                            $_columnCount = 5;
                        }
                    }
                }
            }
        }

        /*
        if (count($tableData) > 0){
            foreach ($tableData as $item) {
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

                //$pdf->Row($item);
                //var_dump($item);
                $pdf->MultiCell(20, 5, $item[0]);
                $pdf->MultiCell(20, 5, $item[1]);
                $pdf->MultiCell(20, 5, $item[2]);
                $pdf->MultiCell(20, 5, $item[3]);
                $pdf->MultiCell(20, 5, $item[4]);
                $pdf->ln();
            }
        }*/

        $data = [
            [
                'aligns' => $aligns,
                'widths' => $columnWidths,
                'font-styles' => $fontStyles,
                'type' => 'row-data',
                'data' => $tableData
            ], [
                'aligns' => $aligns,
                'widths' => $columnWidths,
                'font-styles' => $fontStyles,
                'type' => 'other',
                'data' => [$fontStyles]
            ]
        ];

        //-------------------------------------------------------------------------------------------

        //Add a page
        //This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        //Content
        //--Title
        $pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageHeight * 0.948, 5, 'ABSTRACT OF QUOTATION', "", "",'C');
        $pdf->Ln(10);

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Row group
        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->MultiCell($totalWidth1 / 2, 10.5, "Purchase Request No.: $qtn \nPMO/End-User : $endUser", "LTB", "L", "");
        $pdf->SetXY($x + ($totalWidth1 / 2), $y);
        $pdf->MultiCell($totalWidth1 / 2, 10.5, "Date Prepared: $abstractDate " . 
                                             "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n" .
                                             "Mode of Procurement : ______________________     ", "RTB", "R", "");
        $pdf->SetXY($x + $totalWidth1, $y);
        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->MultiCell(0, 3.5, "based on the canvasses submitted,\n WE, the members of the " . 
                              "Bids and\n Awards Committee (BAC) ", "TR", "C", "");
        //$pdf->ln(-2);

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->Cell($bidderTotalWidth, 2, "BIDDER'S QUOTATION AND OFFER", 'RB', '', 'C');
        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->MultiCell(0, 3.5, "RECOMMEND the following", "R", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'ITEM', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'QTY', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'UNIT', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, 'P A R T I C U L A R S', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'ABC', 'R', '', 'C');

        for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) { 
            //$pdf->Cell($bidderWidth, 3.6, '', 'R', '', 'C');
            $pdf->Cell($bidderWidth, 3.6, '', 'R', '', 'C');
        }

        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->MultiCell(0, 3.5, "items to be AWARDED as", "R", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'NO.', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '(Unit', 'R', '', 'C');

        if (mysqli_num_rows($qryBidders)) {
            $bidderLists = array();

            while ($list = $qryBidders->fetch_object()) {
                $strLength = strlen($list->company_name);
                $bidderLists[] = array($list->bidderID, $list->company_name);

                if ($bidderCount == 3) {
                    if ($strLength > 30) {
                        $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 30) . 
                                   '...', 'RB', '', 'C');
                    } else {
                        $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                    }
                } else if ($bidderCount >= 4 && $bidderCount <= 5) {
                    if ($strLength > 20) {
                        $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 20) . 
                                   '...', 'RB', '', 'C');
                    } else {
                        $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                    }
                } else {
                    if ($strLength > 10) {
                        $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 10) . 
                                   '...', 'RB', '', 'C');
                    } else {
                        $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                    }
                } 
            }
                
        }

        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->MultiCell(0, 3.5, "follows:", "RB", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'LRB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'Cost)', 'RB', '', 'C');

        for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) { 
            $pdf->SetFont('helvetica', '', 7 + ($increaseFontSize * 7));
            $pdf->Cell($bidderWidth * 0.25, 3.6, 'Unit Cost', 'RB', '', 'C');
            $pdf->Cell($bidderWidth * 0.25, 3.6, 'Total Cost', 'RB', '', 'C');
            $pdf->SetFont('helvetica', 'BI', 7 + ($increaseFontSize * 7));
            $pdf->Cell($bidderWidth * 0.5, 3.6, 'Specification', 'RB', '', 'C');
        }

        $pdf->Cell(0, 3.6, '', 'RB', '', 'C');
        $pdf->Ln();

        //----Table data
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->htmlTable($data);

        $pdf->Ln(2.5);
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell(0, 0, "We hereby certify that we have witnessed the opening of bids/quotations and that the prices/quotations contained herein are the true and correct.");
        $pdf->Ln(5);

        // Recommendation
        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->Cell(0, 5, "Recommendation:", '', 0, 'L', 0);
        $pdf->Ln(5);

        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);

        
        // Bids and Committee awardee
        $pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10); // LINE BREAK

        $pdf->Cell(0, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Ln(); // LINE BREAK
        $pdf->SetFont('helvetica', 'B', 9 + ($increaseFontSize * 9));
        $pdf->Cell(0, 8, " ", '', 0, 'L', 0);
        $pdf->Ln(); // LINE BREAK

        $columWidth = ($totalWidthDisplay - 15) / 6;
        $columWidthSpace = 15 / 12;
        $items = [$chairman, $viceChairman, $member1, 
                  $member2, $member3, $endUser];

        $pdf->SetFont('helvetica', 'B', 9 + ($increaseFontSize * 9));

        foreach ($items as $absSig) {
            $pdf->Cell($columWidthSpace, 5);
            $pdf->Cell($columWidth, 5, $absSig, 'B', 0, 'C');
            $pdf->Cell($columWidthSpace, 5);
        }

        $items = ["Chairperson", "Vice Chairperson", "Member", 
                  "Member", "Member", "End-user"];

        $pdf->SetFont('helvetica', '', 9 + ($increaseFontSize * 9));
        $pdf->Ln(); // LINE BREAK

        foreach ($items as $title) {
            $pdf->Cell($columWidthSpace, 5);
            $pdf->Cell($columWidth, 5, $title, 0, 0, 'C');
            $pdf->Cell($columWidthSpace, 5);
        }

        $items = [];
    }

    //Set document information
    $title = 'abstract_' . $qtn;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Abstract of Quotation');
    $pdf->SetKeywords('Abstract, Quotation, Abstract of Quotation');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}

?>