<?php

function generatePAR($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$tableData = [];
    $poNo = $_REQUEST['po-no'];
    $parNo = $_REQUEST['inv-class-no'];
    $recievedBy = $_REQUEST['recieved-by'];
    $multiple = $_REQUEST['multiple'];

    $filename = $parNo . ".pdf";
    $date = "";
    $supplier = "";
    $recievedFrom = "";

    $qryPO = $conn->query("SELECT po.poDate, bidder.company_name 
                           FROM tblpo_jo AS po  
                           INNER JOIN tblbidders AS bidder 
                           ON bidder.bidderID = po.awardedTo 
                           WHERE po.poNo = '" . $poNo . "'") 
                           or die(mysql_error($conn));

    if (mysqli_num_rows($qryPO)) {
        $data = $qryPO->fetch_object();
        $date = $data->poDate;
        $supplier = $data->company_name;
    }

    $qryInventoryItems = $conn->query("SELECT DISTINCT inv.propertyNo, issue.issueDate, issue.issuedBy, 
                                                       issue.quantity, issue.inventoryID, 
                                                       item.unitIssue, item.itemDescription,
                                                       item.amount 
                                       FROM tblinventory_items AS inv 
                                       INNER JOIN tblitem_issue AS issue 
                                       ON issue.inventoryID = inv.id 
                                       INNER JOIN tblpo_jo_items AS item 
                                       ON item.id = inv.poItemID 
                                       WHERE inv.inventoryClassNo = '" . $parNo . "' 
                                       AND issue.empID = '" . $recievedBy . "' 
                                       AND item.poNo = '" . $poNo . "'") 
                                       or die(mysql_error($conn));

    if (mysqli_num_rows($qryInventoryItems)) {
        while ($data = $qryInventoryItems->fetch_object()) {
            $quantity = $data->quantity;
            $unitIssue = $data->unitIssue;
            $unitCost = number_format($data->amount, 2);
            $itemDescription = $data->itemDescription;
            $acquiredDate = $data->issueDate;
            $propertyNo = $data->propertyNo;
            $issuedBy = $data->issuedBy;
            //$itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $itemDescription);

            $tableData[] = [$quantity, $unitIssue, $itemDescription, 
                            $propertyNo, $acquiredDate, $unitCost];
        }
    }

    $qrySignatory1 = $conn->query("SELECT name, position 
                                   FROM tblsignatories 
                                   WHERE signatoryID = '" . $issuedBy . "'") 
                                   or die(mysql_error($conn));
    $qrySignatory2 = $conn->query("SELECT concat(firstname, ' ', left(middlename,1) , '. ', lastname) name, position 
                                   FROM tblemp_accounts 
                                   WHERE empID = '" . $recievedBy . "'") 
                                   or die(mysql_error($conn));

    if (mysqli_num_rows($qrySignatory1)) {
        $data = $qrySignatory1->fetch_object();
        $issuedByName = strtoupper($data->name);
        $issuedByPosition = strtoupper($data->position);
    } else {
        $issuedByName = "";
        $issuedByPosition = "";
    }

    if (mysqli_num_rows($qrySignatory2)) {
        $data = $qrySignatory2->fetch_object();
        $recievedByName = strtoupper($data->name);
        $recievedByPosition = strtoupper($data->position);
    } else {
        $recievedByName = "";
        $recievedByPosition = "";
    }

    for ($i = 0; $i <= 3; $i++) { 
        $tableData[] = array('', '', '', '', '', '');
    }

    $multiplier = 100 / 90;
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [$multiplier * 10, $multiplier * 8, 
                         $multiplier * 37, $multiplier * 10, 
                         $multiplier * 10, $multiplier * 15],
            'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
            'type' => 'row-title',
            'data' => [["Quantity", "Unit", "Description", "Property Number", "Date Acquired", "Amount"]]
        ], [
            'aligns' => ['C', 'C', 'L', 'C', 'C', 'C'],
            'widths' => [$multiplier * 10, $multiplier * 8, 
                         $multiplier * 37, $multiplier * 10, 
                         $multiplier * 10, $multiplier * 15],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F10');
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
    $pdf->Cell(0, "5", "PROPERTY ACKNOWLEDGMENT RECEIPT", "", "", "C");
    $pdf->Ln(10);

    $pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.65, "5", "Fund Cluster : 01", "", "", "L");
    $pdf->Cell(0, "5", "PAR No : $parNo", "", "", "L");
    $pdf->Ln(10);

    //----Table data
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->htmlTable($data);

    $pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.2, "5", "Purchase Order", "L", "", "L");
    $pdf->Cell(0, "5", ": $poNo", "R", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.2, "5", "Date", "L", "", "L");
    $pdf->Cell(0, "5", ": $date", "R", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.2, "5", "Supplier", "L", "", "L");
    $pdf->Cell(0, "5", ": $supplier", "R", "", "L");
    $pdf->Ln();

    $pdf->Cell(0, "10", "", "LRB", "", "L");
    $pdf->Ln();

    $pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.45, "5", "Received by:", "LR", "", "L");
    $pdf->Cell(0, "5", "Issued By: ", "R", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
    $pdf->Cell(0, "5", "", "R", "", "L");
    $pdf->Ln();

    $pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.45, "5", $recievedByName, "LR", "", "C");
    $pdf->Cell(0, "5", $issuedByName, "R", "", "C");
    $pdf->Ln();

    $pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.45, "5", "Signature over Printed Name of End User", "LR", "", "C");
    $pdf->Cell(0, "5", "Signature over Printed Name of Supply and/or", "R", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "3", "", "LR", "", "L");
    $pdf->Cell(0, "3", "Property Custodian", "R", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
    $pdf->Cell(0, "5", "", "R", "", "C");
    $pdf->Ln();

    $pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.45, "5", $recievedByPosition, "LR", "", "C");
    $pdf->Cell(0, "5", $issuedByPosition, "R", "", "C");
    $pdf->Ln();

    $pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.45, "5", "Position/Office", "LR", "", "C");
    $pdf->Cell(0, "5", "Position/Office", "R", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
    $pdf->Cell(0, "5", "", "R", "", "L");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "5", "_________________________________", "LR", "", "C");
    $pdf->Cell(0, "5", "_________________________________", "R", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45, "5", "Date", "LRB", "", "C");
    $pdf->Cell(0, "5", "Date", "RB", "", "C");
    $pdf->Ln();

    //Set document information
    $title = strtolower($parNo);
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Inventory Custodian Slip');
    $pdf->SetKeywords('ICS, Inventory, Custodian, Slip, Inventory Custodian Slip');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}
?>