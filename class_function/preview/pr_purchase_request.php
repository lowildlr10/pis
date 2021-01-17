<?php

function generatePR ($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
    $total = 0;
    $tableData = [];
    $office = "DOST-CAR";
    $signApp = "MARIA CELESTE R. DELA CRUZ";
    $signAppPostion = "PSO";
    $signCertified = "SHELA LORRAINE T. COSALAN";
    $signCertifiedPosition = "Chief Accountant";

    $qryPR = $conn->query("SELECT prNo, prDate, requestBy, signatory, purpose 
                           FROM tblpr
                           WHERE prID = '". $pid ."'");
    $qryPRInfo = $conn->query("SELECT estimateTotalCost, itemDescription, quantity, 
                                      estimateUnitCost, stockNo, unitIssue 
                               FROM tblpr_info
                               WHERE prID = '". $pid ."' 
                               ORDER BY LENGTH(infoID), infoID ASC");

    if (mysqli_num_rows($qryPR)) {
        $data = $qryPR->fetch_object();
        $prNo = $data->prNo;
        $prDate = $data->prDate;
        $purpose = $data->purpose;
        $_requestedBy = $data->requestBy;
        $_signatory = $data->signatory;
    }

    $qryEmp = $conn->query("SELECT firstname, middlename, lastname, position  
                            FROM tblemp_accounts 
                            WHERE empID = '" . $_requestedBy . "'");
    $qrySignatory = $conn->query("SELECT name, position 
                                  FROM tblsignatories 
                                  WHERE signatoryID = '" . $_signatory . "'");

    if (mysqli_num_rows($qryEmp)) {
        $data = $qryEmp->fetch_object();

        if (!empty($data->middlename)) {
            $requestedBy = $data->firstname . " " . $data->middlename[0] . ". " . $data->lastname;
        } else {
            $requestedBy = $data->firstname . " " . $data->lastname;
        }
        
        $reqPosition = $data->position;
    }

    if (mysqli_num_rows($qrySignatory)) {
        $data = $qrySignatory->fetch_object();
        $signatory = $data->name;
        $sigPosition = $data->position;
    }

    if (mysqli_num_rows($qryPRInfo)) {
        while ($list = $qryPRInfo->fetch_object()) {
            //$list->itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $list->itemDescription);
            $tableData[] = [$list->stockNo, 
                            $list->unitIssue, 
                            $list->itemDescription, 
                            $list->quantity, 
                            number_format($list->estimateUnitCost, 2), 
                            number_format($list->estimateTotalCost, 2)];

            $total += $list->estimateTotalCost;
        }
    }

    $total = number_format($total, 2);
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [14.8, 10, 32, 9, 17.1, 17.1],
            'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
            'type' => 'row-title',
            'data' => [['Stock/Property No.', 'Unit', 'Item Description',
                        'Quantity', 'Unit Cost', 'Total Cost']]],
        [
            'aligns' => ['C', 'C', 'L', 'C', 'R', 'R'],
            'widths' => [14.8, 10, 32, 9, 17.1, 17.1],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData],
        [
            'aligns' => ['C', 'C', 'C', 'C', 'L', 'R'],
            'widths' => [14.8, 10, 32, 9, 17.1, 17.1],
            'font-styles' => ['', '', '', '', 'B', 'B'],
            'type' => 'other',
            'data' => [['', '', '', '', '', ''], 
                       ['', '', '', '', 'Total', $total]]
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

	//Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F05');
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
    $pdf->Cell(0, 5, 'PURCHASE REQUEST', 0, 0, 'C');
    $pdf->Ln(10);

    //--Body
    $pdf->SetFont('Times', 'BI', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.114, '5', 'Fund Cluster:', 0, 0, 'L');
    $pdf->SetFont('Times', 'BIU', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '5', '01', '', '', 'L');
    $pdf->Ln(6);

    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.224, '6', 'Office/Section : ', 'TLR', '', 'L');
    $pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.371, '6', 'PR No.: ' . $prNo, 'TLR', 0, "L");
    $pdf->Cell(0, '6', 'Date: ' . $prDate, 'TLR', 0, 'L');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.224, 7, $office, 'BLR', 0, 'C');
    $pdf->Cell($pageWidth * 0.371, 7, 'Responsibility Center Code : 19 001 03000 14', 'B');
    $pdf->Cell(0, 7, '', 'BLR', 0, 'L');
    $pdf->Ln();

    //----Table data
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->htmlTable($data);
    $pdf->MultiCell(0, 5, 'Purpose: ' . $purpose, 'BLR', 'L');

    //--Table Footer
    $pdf->Cell($pageWidth * 0.135, '5', "", "TLR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "Requested by: ", "TLR", "", "L");
    $pdf->Cell(0, '5', "Approved by: ", "TLR", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "Signature : ", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "L");
    $pdf->Cell(0, '5', "", "LR", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "Printed Name : ", "LR", "", "L");
    $pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.378, '5', strtoupper($requestedBy), "LR", "", "C");
    $pdf->Cell(0, '5', strtoupper($signatory), "LR", "", "C");
    $pdf->Ln();

    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.135, '5', "Designation : ", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', $reqPosition, "LR", "", "C");
    $pdf->Cell(0, '5', $sigPosition, "LR", "", "C");
    $pdf->Ln();
    
    $pdf->Cell($pageWidth * 0.135, '6', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '6', "", "LR", "", "C");
    $pdf->Cell(0, '6', "", "LR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "L");
    $pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '5', "Within APP: ", "LR", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->Cell(0, '5', "", "LR", "", "C");
    $pdf->Ln();

    $pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->Cell(0, '5', strtoupper($signApp), "LR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->Cell(0, '5', $signAppPostion, "LR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '5', "Certified Funds Available: ", "LR", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->Cell(0, '5', "", "LR", "", "C");
    $pdf->Ln();

    $pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->Cell(0, '5', strtoupper($signCertified), "LR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "LR", "", "C");
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '5', $signCertifiedPosition, "LR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.135, '5', "", "BLR", "", "L");
    $pdf->Cell($pageWidth * 0.378, '5', "", "BLR", "", "C");
    $pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '5', "Reference:______________________________", "BLR", "", "L");
    $pdf->Ln();

    //Set document information
    $title = 'pr_' . $prNo;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Purchase Request');
    $pdf->SetKeywords('PR, Purchase, Request, Purchase Request');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}