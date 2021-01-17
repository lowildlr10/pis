<?php

function generateIAR($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
    $poNo = $_REQUEST["poNo"];
    $tableData = [];
    $orsID = "";
    $supplier = "";
    $iarNo = "";
    $poDate = $poNo;
    $RequisitioningDept = "";
    $iarDate = "";
    $invoiceNo = "";
    $invoiceDate = "";
    $sign1 = "";
    $sign2 = "";
    
    $qrySupplier = $conn->query("SELECT bid.company_name, ors.id 
                                 FROM tblors AS ors
                                 INNER JOIN tblbidders AS bid 
                                 ON bid.bidderID = ors.payee 
                                 WHERE ors.poNo = '" . $poNo . "'")
                                 or die(mysqli_error($conn));

    $qryDepartment = $conn->query("SELECT sec.section 
                                   FROM tblpr AS pr 
                                   INNER JOIN tblemp_accounts AS emp 
                                   ON emp.empID = pr.requestBy 
                                   INNER JOIN tblsections AS sec 
                                   ON sec.sectionID = emp.sectionID 
                                   WHERE pr.prID = '" . $pid . "'");

    $qryItems = $conn->query("SELECT unitIssue, quantity, itemDescription, amount 
                              FROM tblpo_jo_items  
                              WHERE prID = '" . $pid . "' 
                              AND poNo ='" . $poNo . "' 
                              AND excluded = 'n'") 
                              or die(mysqli_error($conn));

    if (mysqli_num_rows($qrySupplier)) {
        $data = $qrySupplier->fetch_object();
        $supplier = $data->company_name;
        $orsID = $data->id;
    }

    if (mysqli_num_rows($qryDepartment)) {
        $data = $qryDepartment->fetch_object();
        $RequisitioningDept = $data->section;
    }

    $qryIAR = $conn->query("SELECT iarNo, iarDate, invoiceNo, invoiceDate, inspectedBy, signatorySupply 
                            FROM tbliar AS iar 
                            WHERE orsID = '" . $orsID . "'")
                            or die(mysqli_error($conn));

    if (mysqli_num_rows($qryIAR)) {
        $data = $qryIAR->fetch_object();
        $iarNo = $data->iarNo;
        $iarDate = $data->iarDate;
        $invoiceNo = $data->invoiceNo;
        $invoiceDate = $data->invoiceDate;
        $sign1 = $data->inspectedBy;
        $sign2 = $data->signatorySupply;
    }

    $qrySignatories = $conn->query("SELECT signatoryID, name 
                                    FROM tblsignatories")
                                    or die(mysqli_error($conn));

    if (mysqli_num_rows($qrySignatories)) {
        while ($data = $qrySignatories->fetch_object()) {
            if ($sign1 == $data->signatoryID) {
                $sign1 = $data->name;
            }

            if ($sign2 == $data->signatoryID) {
                $sign2 = $data->name;
            }
        }
    }

    if (mysqli_num_rows($qryItems)) {
        while ($data = $qryItems->fetch_object()) {
            $stockNo = "";
            $description = $data->itemDescription;
            $unit = $data->unitIssue;
            $quantity = $data->quantity;
            //$description = iconv('UTF-8', 'windows-1252//IGNORE', $description);
            $tableData[] = [$stockNo, $description, $unit, $quantity];
        }
    }

    $contentPageWidth = $pageWidth - 20;
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C'],
            'widths' => [(30/$contentPageWidth) * 100, 
                         (90/$contentPageWidth) * 100, 
                         (32/$contentPageWidth) * 100, 
                         (40/$contentPageWidth) * 100],
            'font-styles' => ['B', 'B', 'B', 'B'],
            'type' => 'row-title',
            'data' => [["Stock/<br>Property No.", "Description", "Unit", "Quantity"]]
        ], [
            'aligns' => ['C', 'L', 'C', 'C'],
            'widths' => [(30/$contentPageWidth) * 100, 
                         (90/$contentPageWidth) * 100, 
                         (32/$contentPageWidth) * 100, 
                         (40/$contentPageWidth) * 100],
            'font-styles' => ['', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ], [
            'aligns' => ['C', 'L', 'C', 'C'],
            'widths' => [(30/$contentPageWidth) * 100, 
                         (90/$contentPageWidth) * 100, 
                         (32/$contentPageWidth) * 100, 
                         (40/$contentPageWidth) * 100],
            'font-styles' => ['', '', '', ''],
            'type' => 'other',
            'data' => [['', '', '', '']]
        ]
    ];

    $data1 = [
        [
            'aligns' => ['C', 'C'],
            'widths' => [(97/$contentPageWidth) * 100, 
                         (95/$contentPageWidth) * 100],
            'font-styles' => ['', ''],
            'type' => 'other',
            'data' => [["INSPECTION", "ACCEPTANCE"]]
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F09');
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

    //Add a page
    //This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    //Content
    //--Title
    $pdf->SetFont('Times', 'B', 14 + ($increaseFontSize * 14));
    $pdf->Cell(0, "5", "INSPECTION AND ACCEPTANCE REPORT", "", "", "C");
    $pdf->Ln();

    $pdf->Ln(5);

    $pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell(0, "5", "Fund Cluster : 01", "", "", "L");
    $pdf->Ln();

    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY();
    $x1 = $xCoor;
    $y1 = $yCoor;
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("28", "7", "\tSupplier: \n ", "0", "L");
    $pdf->SetXY($xCoor + 28, $yCoor);
    $pdf->MultiCell("92", "7", $supplier, "0", "L");
    $pdf->SetXY($xCoor + 120, $yCoor);
    $pdf->MultiCell("24", "7", "IAR No.: \n ", "0", "L");
    $pdf->SetXY($xCoor + 144, $yCoor);
    $pdf->MultiCell("48", "7", $iarNo, "0", "L");
    $pdf->ln();

    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY();
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("28", "7", "\tPO No./Date : ", "0", "L");
    $pdf->SetXY($xCoor + 28, $yCoor);
    $pdf->MultiCell("92", "7", $poDate, "0", "L");
    $pdf->SetXY($xCoor + 120, $yCoor);
    $pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("24", "7", "Date : ", "0", "L");
    $pdf->SetXY($xCoor + 144, $yCoor);
    $pdf->SetFont('Times', '', 11);
    $pdf->MultiCell("48", "7", $iarDate, "0", "L");
    //$pdf->ln();

    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY();
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("50", "7", "\tRequisitioning Office/Dept. : ", "0", "L");
    $pdf->SetXY($xCoor + 50, $yCoor);
    $pdf->MultiCell("70", "7", $RequisitioningDept, "0", "L");
    $pdf->SetXY($xCoor + 120, $yCoor);
    $pdf->MultiCell("24", "7", "Invoice No. : ", "0", "L");
    $pdf->SetXY($xCoor + 144, $yCoor);
    $pdf->MultiCell("48", "7", $invoiceNo, "0", "L");
    //$pdf->ln(0);

    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY();
    $x1_1 = $xCoor;
    $y1_1 = $yCoor;
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("120", "7", "\tResponsibility Center Code : 19 001 03000 14", "0", "L");
    $pdf->SetXY($xCoor + 120, $yCoor);
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell("24", "7", "Date : ", "0", "L");
    $pdf->SetXY($xCoor + 144, $yCoor);
    $pdf->MultiCell("48", "7", $invoiceDate, "0", "L");
    //$pdf->ln();

    $pdf->Rect($x1, $y1, 120, $y1 - 18);
    $pdf->Rect($x1 + 120, $y1, 72, $y1 - 18);

    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY() - 2;
    $pdf->SetXY($xCoor, $yCoor);

    //----Table data
    $pdf->htmlTable($data);
    $pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
    $pdf->htmlTable($data1);

    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->Cell("97", "7", "Date Inspected : ______________________________", "TLR");
    $pdf->Cell("95", "7", "Date Received : ____________________________", "TLR");
    $pdf->ln();

    $pdf->Cell("97", "5", "", "LR");
    $pdf->Cell("95", "5", "PO/JO #: ", "LR");
    $pdf->ln();

    $pdf->Cell("2", "7", "", "LR");
    $pdf->Cell("8", "7", "", "TBLR");
    $pdf->Cell("87", "7", "\tInspected, verified and found in order as to", "LR");
    $pdf->Cell("95", "7", "", "LR");
    $pdf->ln();

    $pdf->Cell("2", "7", "", "L");
    $pdf->Cell("8", "7", "", "");
    $pdf->Cell("87", "7", "\tquantity and specifications", "R");
    $pdf->Cell("7", "7", "", "LR");
    $pdf->Cell("8", "7", "", "TBLR");
    $pdf->Cell("80", "7", "\tComplete", "LR");
    $pdf->ln();

    $pdf->Cell("97", "5", "", "LR");
    $pdf->Cell("95", "5", "", "LR");
    $pdf->ln();

    $pdf->Cell("2", "7", "", "LR");
    $pdf->Cell("8", "7", "", "TBLR");
    $pdf->Cell("87", "7", "\tNot in conformity as to quantity and ", "R");
    $pdf->Cell("7", "7", "", "LR");
    $pdf->Cell("8", "7", "", "TBLR");
    $pdf->Cell("80", "7", "\tPartial  (pls. specify quantity)", "LR");
    $pdf->ln();

    $pdf->Cell("2", "7", "", "L");
    $pdf->Cell("8", "7", "", "");
    $pdf->Cell("87", "7", "\tspecifications", "R");
    $pdf->Cell("95", "7", "", "LR");
    $pdf->ln();

    $pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
    $pdf->Cell("97", "3", "", "LR", "", "C");
    $pdf->Cell("95", "3", "", "LR", "", "C");
    $pdf->ln();

    $pdf->SetFont('Times','B', 12 + ($increaseFontSize * 12));
    $pdf->Cell("97", "5", strtoupper($sign1), "LR", "", "C");
    $pdf->Cell("95", "5", strtoupper($sign2), "LR", "", "C");
    $pdf->ln();

    $pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
    $pdf->Cell("97", "8", "Inspection Officer/Inspection Committee", "BLR", "", "C");
    $pdf->Cell("95", "8", "Supply and/or Property Custodian", "BLR", "", "C");
    $pdf->ln();

    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->Cell("97", "8", "Remarks/Recommendation: ", "LR", "", "L");
    $pdf->Cell("95", "8", "", "LR");
    $pdf->ln();

    $pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
    $pdf->Cell("97", "5", "___________________________________________ ", "LR", "", "L");
    $pdf->Cell("95", "5", "", "LR");
    $pdf->ln();

    $pdf->Cell("97", "6", "", "BLR");
    $pdf->Cell("95", "6", "", "BLR");
    $pdf->ln();

	//Set document information
    $title = 'iar_' . $poNo;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Inspection and Acceptance Report');
    $pdf->SetKeywords('IAR, Inspection, Acceptance, Report', 'Inspection and Acceptance Report');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}
?>