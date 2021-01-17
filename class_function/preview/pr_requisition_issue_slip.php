<?php

function generateRIS($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
    $tableData = [];
    $poNo = $_REQUEST['po-no'];
    $risNo = $_REQUEST['inv-class-no'];
    $recievedBy = $_REQUEST['recieved-by'];
    $multiple = $_REQUEST['multiple'];

    $filename = $risNo . ".pdf";
    $date = "";
    $supplier = "";
    $office = "";
    $requestBy = "";
    $approvedBy = "";
    $issuedBy = "";
    $division = "";
    $office = "";

    $qryPO = $conn->query("SELECT po.poDate, bidder.company_name, 
                                  ors.office, pr.requestBy, pr.purpose,
                                  sec.section 
                           FROM tblpo_jo AS po  
                           INNER JOIN tblbidders AS bidder 
                           ON bidder.bidderID = po.awardedTo 
                           INNER JOIN tblors AS ors 
                           ON ors.prID = po.prID 
                           INNER JOIN tblpr AS pr 
                           ON pr.prID = po.prID 
                           INNER JOIN tblemp_accounts emp 
                           ON emp.empID = pr.requestBy 
                           INNER JOIN tblsections sec 
                           ON sec.sectionID = emp.sectionID 
                           WHERE po.poNo = '" . $poNo . "'") 
                           or die(mysql_error($conn));

    if (mysqli_num_rows($qryPO)) {
        $data = $qryPO->fetch_object();
        $date = $data->poDate;
        $supplier = $data->company_name;
        $requestBy = $data->requestBy;
        $division = $data->section;
        $office = $data->office;
        $purpose = $data->purpose;
    }

    $qryInventoryItems = $conn->query("SELECT DISTINCT inv.propertyNo, inv.stockAvailable, issue.issueDate, 
                                                       issue.inventoryID, issue.issuedBy, issue.approvedBy, 
                                                       issue.quantity AS issueQuantity, 
                                                       issue.issueRemarks, item.unitIssue, item.itemDescription, 
                                                       item.amount, item.quantity 
                                       FROM tblinventory_items AS inv 
                                       INNER JOIN tblitem_issue AS issue 
                                       ON issue.inventoryID = inv.id 
                                       JOIN tblpo_jo_items AS item 
                                       ON item.id = inv.poItemID 
                                       WHERE inv.inventoryClassNo = '" . $risNo . "' 
                                       AND issue.empID = '" . $recievedBy . "'") 
                                       or die(mysql_error($conn));

    if (mysqli_num_rows($qryInventoryItems)) {
        while ($data = $qryInventoryItems->fetch_object()) {
            $stockNo = $data->propertyNo;
            $unitIssue = $data->unitIssue;
            $itemDescription = $data->itemDescription;
            $quantity = $data->quantity;
            $stockAvailable = $data->stockAvailable;
            $issueQuantity = $data->issueQuantity;
            $issueRemarks = $data->issueRemarks;
            $approvedBy = $data->approvedBy;
            $issuedBy = $data->issuedBy;
            //$itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $itemDescription);

            if ($stockAvailable == "yes") {
                $tableData[] = array($stockNo, $unitIssue, $itemDescription, 
                                 $quantity, "x", "", $issueQuantity,
                                 $issueRemarks);
            } else if ($stockAvailable == "No") {
                $tableData[] = array($stockNo, $unitIssue, $itemDescription, 
                                 $quantity, "", "x", $issueQuantity,
                                 $issueRemarks);
            }
        }
    }

    $qrySignatory1 = $conn->query("SELECT concat(firstname, ' ', left(middlename,1) , '. ', lastname) name, position 
                                   FROM tblemp_accounts 
                                   WHERE empID = '" . $requestBy . "'") 
                                   or die(mysql_error($conn));
    $qrySignatory2 = $conn->query("SELECT name, position 
                                   FROM tblsignatories 
                                   WHERE signatoryID = '" . $approvedBy . "'") 
                                   or die(mysql_error($conn));
    $qrySignatory3 = $conn->query("SELECT name, position 
                                   FROM tblsignatories 
                                   WHERE signatoryID = '" . $issuedBy . "'") 
                                   or die(mysql_error($conn));
    $qrySignatory4 = $conn->query("SELECT concat(firstname, ' ', left(middlename,1) , '. ', lastname) name, position 
                                   FROM tblemp_accounts 
                                   WHERE empID = '" . $recievedBy . "'") 
                                   or die(mysql_error($conn));

    if (mysqli_num_rows($qrySignatory1)) {
        $data = $qrySignatory1->fetch_object();
        $requestByName = strtoupper($data->name);
        $requestByPosition = strtoupper($data->position);
    } else {
        $requestByName = "";
        $requestByPosition = "";
    }

    if (mysqli_num_rows($qrySignatory2)) {
        $data = $qrySignatory2->fetch_object();
        $approvedByName = strtoupper($data->name);
        $approvedByPosition = strtoupper($data->position);
    } else {
        $approvedByName = "";
        $approvedByPosition = "";
    }

    if (mysqli_num_rows($qrySignatory3)) {
        $data = $qrySignatory3->fetch_object();
        $issuedByName = strtoupper($data->name);
        $issuedByPosition = strtoupper($data->position);
    } else {
        $issuedByName = "";
        $issuedByPosition = "";
    }

    if (mysqli_num_rows($qrySignatory4)) {
        $data = $qrySignatory4->fetch_object();
        $recievedByName = strtoupper($data->name);
        $recievedByPosition = strtoupper($data->position);
    } else {
        $recievedByName = "";
        $recievedByPosition = "";
    }

    for ($i = 0; $i <= 3; $i++) { 
        $tableData[] = ['', '', '', '', '', '', '', ''];
    }

    $multiplier = 100 / 90;
    $data = [
        [
            'aligns' => ['C', 'C', 'C'],
            'widths' => [$multiplier * 48, $multiplier * 14,
                         $multiplier * 28],
            'font-styles' => ['B', 'B', 'B'],
            'type' => 'row-data',
            'data' => [['<br>Requisition', '<br>Stock Available?', '<br>Issue']]
        ], [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [$multiplier * 10, $multiplier * 6, 
                         $multiplier * 23, $multiplier * 9, 
                         $multiplier * 7, $multiplier * 7,
                         $multiplier * 9, $multiplier * 19],
            'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'],
            'type' => 'row-data',
            'data' => [['Stock No.', 'Unit', 'Description', 
                        'Quantity', 'Yes', 'No', 
                        'Quantity', 'Remarks']]
        ], [
            'aligns' => ['C', 'C', 'L', 'C', 'C', 'C', 'C', 'L'],
            'widths' => [$multiplier * 10, $multiplier * 6, 
                         $multiplier * 23, $multiplier * 9, 
                         $multiplier * 7, $multiplier * 7,
                         $multiplier * 9, $multiplier * 19],
            'font-styles' => ['', '', '', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ]
    ];
    $footerData = [
        [
            'aligns' => ['L', 'C', 'C', 'C', 'C'],
            'widths' => [$multiplier * 16, $multiplier * 18.5,
                         $multiplier * 18.5, $multiplier * 18.5,
                         $multiplier * 18.5],
            'font-styles' => ['', '', '', '', ''],
            'type' => 'row-data',
            'data' => [['Signature:<br><br> ', '', '', '', ''],
                       ['Printed Name:', $requestByName, $approvedByName, 
                        $issuedByName, $recievedByName],
                       ['Designations:', $requestByPosition, $approvedByPosition, 
                        $issuedByPosition, $recievedByPosition],
                       ['Date:', '', '', '', '']]
        ]
    ];

	//-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F11');
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
    $pdf->Cell(0, "5", "REQUISITION AND ISSUE SLIP", "", "", "C");
    $pdf->Ln(10);

    $pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "5", "Fund Cluster : 01", "", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.55, "2", "", "TLR", "", "L");
    $pdf->Cell(0, "2", "", "TR", "", "L");
    $pdf->Ln();

    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.55, "7", "Division : $division", "LR", "", "L");
    $pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "7", "Responsibility Center Code : 19 001 03000 14", "R", "", "L");
    $pdf->Ln();

    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.55, "7", "Office : $office", "LR", "", "L");
    $pdf->Cell(0, "7", "RIS No : $risNo", "R", "", "L");
    $pdf->Ln();

    //----Table data
    $pdf->htmlTable($data);

    $pdf->Cell(0, 5, "Purpose: $purpose", 1, "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.16, "7", "", "LR", "", "L");
    $pdf->Cell($pageWidth * 0.185, "7", "Requested by:", "RB", "", "L");
    $pdf->Cell($pageWidth * 0.185, "7", "Approved by:", "RB", "", "L");
    $pdf->Cell($pageWidth * 0.185, "7", "Issued by:", "RB", "", "L");
    $pdf->Cell(0, "7", "Received by:", "RB", "", "L");
    $pdf->Ln();

    //----Footer data
    $pdf->htmlTable($footerData);

    //Set document information
    $title = strtolower($risNo);
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Requisition and Issue Slip');
    $pdf->SetKeywords('RIS, Requisition, Issue, Slip, Requisition and Issue Slip');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}