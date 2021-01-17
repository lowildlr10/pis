<?php

function generateCanvass($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$groupNumber = array();
	$qtn = "";
	$sig = "";

	$date = date_create($_REQUEST['inputDate']);
	$date = date_add($date, date_interval_create_from_date_string("7 days"));
	//$deadlineDate = $date->format('Y-m-d');
	$deadlineDate = "";

	if (isset($_REQUEST['qtn'])) {
		$qtn = $_REQUEST['qtn'];
	}

	if (isset($_REQUEST['sig'])) {
		$sig = $_REQUEST['sig'];
	}

	parse_str($_REQUEST['print']);

	$qryInfo = "SELECT name, position 
				FROM tblsignatories 
				WHERE signatoryID =  '" . $sig ."'";

	$canvassDate = $_REQUEST['inputDate'];

	$conn->query("UPDATE tblpr 
				  SET canvassDate = '".$canvassDate."' 
				  WHERE prID='". $pid ."'");

	$qryItems = "SELECT groupNo FROM tblpr_info 
				 WHERE prID = '".$pid."' 
				 ORDER BY LENGTH(infoID), infoID ASC";
	$group = $conn->query($qryItems);

	while ($grp = $group->fetch_object()) {
		$groupNumber[] = $grp->groupNo;
	}

	$groupNumber = array_unique($groupNumber);

	if ($infoRes = $conn->query($qryInfo)) {
		$info = $infoRes->fetch_object();
		$signBy = $info->name;
		$signPos = $info->position;
	}

	if (isset($_REQUEST['cdate'])) {
		if (!empty($_REQUEST['cdate'])) {
			$printDate = $_REQUEST['cdate'];
		} else {
			$printDate = '';//time();
			//$conn->query("UPDATE tblpr SET canvassDate='".$printDate."' WHERE prID='".$pid."'");
		}
	}

    //-------------------------------------------------------------------------------------------

	$pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F06');
    $pdf->setDocRevision('Revision 1');
    $pdf->setRevDate('02-28-18');

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
        $tableData = [];
        $i = 0;
        $items = array();
        $itemNo = 0;
        $qryItems = "SELECT * FROM tblpr_info 
                     WHERE prID = '".$pid."' 
                     ORDER BY LENGTH(infoID), infoID ASC";
        $res = $conn->query($qryItems);

        while ($list = $res->fetch_object()) {
            if ($list->quantity != 0 && $list->groupNo == $grpNo) {
                $itemNo++;
                //$itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $list->itemDescription);
                $tableData[] = [$itemNo, 
                                $list->quantity, 
                                $list->unitIssue, 
                                $list->itemDescription, 
                                number_format($list->estimateUnitCost, 2),
                                ""];
            }
        }

        if (isset($_REQUEST['selApp']) && !empty($_REQUEST['selApp'])) {
            parse_str($_REQUEST['selApp']);
        }

        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [7.14, 6.19, 7.62, 49.95, 14.55, 14.55],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-title',
                'data' => [['ITEM NO.', 'QTY', 'UNIT', 'ARTICLES/PARTICULARS', 
                            'Approved Budget for the Contract', 'UNIT PRICE']]],
            [
                'aligns' => ['C','C','C','C','R','R'],
                'widths' => [7.14, 6.19, 7.62, 49.95, 14.55, 14.55],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData],
            [
                'aligns' => ['C','C','C','C','R','R'],
                'widths' => [7.14, 6.19, 7.62, 49.95, 14.55, 14.55],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'other',
                'data' => [['', '', '', '', '', '']]
            ]
        ];

        //-------------------------------------------------------------------------------------------

        //Add a page
        //This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        //--Title
        $pdf->Cell($pageWidth * 0.452, 5, 'QTN. NO: '.$qtn, 0, 0, 'L');
        $pdf->Cell(0, 5, 'Date: '.$_REQUEST['inputDate'], 0, 0, 'R');
        $pdf->Ln();
        $pdf->MultiCell(0, '5', 'REQUEST FOR BIDS/QUOTATION', 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10  + ($increaseFontSize * 10));
        $pdf->Cell(0, 5, 'Sir/Madam:');
        $pdf->Ln();
        $pdf->MultiCell(0, 5,
                        "       This is a request for quotation on items enumerated hereunder.".
                        " If you are interested to and in a position to furnish the same, we shall be ".
                        "glad to have your best prices, terms and conditions of delivery.",
                        0, 'L');
        $pdf->Ln(5);

        //----Table data
        $pdf->htmlTable($data);

        //--Footer
        $pdf->Cell($pageWidth * 0.5, 5,"                         Terms of Delivery:");
        $pdf->Cell($pageWidth * 0.405, 5,"             Terms of Payment:");
        $pdf->Ln();
        $pdf->Cell($pageWidth * 0.5, 5,"                          _______ Pick-up");
        $pdf->Cell($pageWidth * 0.405, 5,"             _______ After Inspection & Acceptance");
        $pdf->Ln();
        $pdf->Cell($pageWidth * 0.5, 5,"                          _______On-site Delivery");
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(0, 5,"Deadline for Submission: " . $deadlineDate);
        $pdf->Ln(8);

        $pdf->Cell($pageWidth * 0.57,  5, "Very truly yours,");
        $pdf->Cell($pageWidth * 0.19,  5, "Prices quoted above are");
        $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
        $pdf->Cell(0,  5, "valid until_______ ", 0);
        $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
        $pdf->Ln();

        $pdf->Cell($pageWidth * 0.5, 5, '');
        $pdf->Cell($pageWidth * 0.405, 5, "               Certified Correct:", 0, 0, 'L');
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10  + ($increaseFontSize * 10));
        $pdf->Cell(0, 5,"DEPARTMENT OF SCIENCE AND TECHNOLOGY", 0, 'B', 'L');
        $pdf->Ln(8);

        $pdf->Cell($pageWidth * 0.5, 5," ");
        $pdf->Cell(0, 5,"      ________________________________", 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.5, 5, " ");
        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.405, 5, "    Name of Firm/Company and Address:", 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.5, 5, " ");
        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.405, 5, " ");
        $pdf->Ln();
        $pdf->Cell($pageWidth * 0.5, 5, " ");
        $pdf->Cell($pageWidth * 0.405, 5, " ");
        $pdf->Ln();

        $pdf->SetFont('helvetica', 'B', 11 + ($increaseFontSize * 11));
        $pdf->Cell($pageWidth * 0.405, 5, "".$signBy."",'B','','C');
        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.5, 5, "");
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageWidth * 0.405, 5, "Property & Supply Officer/ PSTD ", 0, 0, 'C');
        $pdf->Cell($pageWidth * 0.1645, 5, "",'','','C');
        $pdf->Cell(0, 5, "Signature over Printed Name of Authorized", 'T', 0, 'L');
        $pdf->Ln();
        $pdf->Cell('83', 5, " ", 0, 0, 'C');
        //$pdf->Cell($pageWidth * 0.452, 5," Representative",0,'','L');
        $pdf->Ln();


        $pdf->Cell(0, 5, "IMPORTANT:", 0);
        $pdf->Ln();
        $pdf->Cell($pageWidth * 0.75, 5, '          Prices should be in ink and clearly quoted. '.
                                           'When offering substitute/equivalent, specify brand. ', 0);
        $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
        $pdf->Cell(0, 5, 'Submit quotations');
        $pdf->Ln();
        $pdf->Cell(0, 5, 'in a sealed envelope.');
        $pdf->Ln();
        $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
        $pdf->MultiCell(0, 5, "\n          DOST-CAR office reserves the right to reject any or all bids, ".
                              "to waive any defect therein and accept the offer most ".
                              "advantageous to the DOST-CAR Office.", 0, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'IB', 10 + ($increaseFontSize * 10));
        $pdf->Cell(0, 5, "Canvassed by:_______________________", 0, 0, 'L');
    }
	
    //Set document information
    $title = 'rfq_' . $qtn;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Request for Quotation');
    $pdf->SetKeywords('RFQ, Quotation, Request, Request for Quotation');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}
?>