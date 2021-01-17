<?php

function generatePropertyLabel($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 53.27, $pageWidth = 103.76125) {
    $poNo = $_REQUEST['po-no'];
    $inventoryClassNo = $_REQUEST['inv-class-no'];
    $recievedBy = $_REQUEST['recieved-by'];
    $serialNo = "";

    $qryInventoryItems = $conn->query("SELECT DISTINCT inv.propertyNo, issue.issueDate, issue.issuedBy, 
                                                       issue.quantity, issue.inventoryID, 
                                                       item.unitIssue, item.itemDescription,
                                                       item.amount, issue.serialNo 
                                       FROM tblinventory_items AS inv 
                                       INNER JOIN tblitem_issue AS issue 
                                       ON issue.inventoryID = inv.id 
                                       INNER JOIN tblpo_jo_items AS item 
                                       ON item.id = inv.poItemID 
                                       WHERE issue.inventoryID = '" . $pid . "' 
                                       AND issue.empID = '" . $recievedBy . "' 
                                       AND item.poNo = '" . $poNo . "'") 
                                       or die(mysql_error($conn));

    if (mysqli_num_rows($qryInventoryItems)) {
        while ($data = $qryInventoryItems->fetch_object()) {
            $serialNo = $data->serialNo;
            //$itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $data->itemDescription);

            if (!empty($serialNo)) {
                $itemDescription = substr($data->itemDescription, 0, 178) . "... \n[S/N: " . $serialNo . "]";
            } else {
                $itemDescription = substr($data->itemDescription, 0, 185) . "...";
            }
            
            $acquiredDate = $data->issueDate;
            $propertyNo = $data->propertyNo;
            $issuedBy = $data->issuedBy;
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

    if (empty($propertyNo)) {
        $propertyNo = 'N/A';
    }

    $multiplier = 85 / 85;
    $data1 = [
        [
            'aligns' => ['L', 'L'],
            'widths' => [$multiplier * 18, 
                         $multiplier * 67],
            'font-styles' => ['', ''],
            'type' => 'row-data',
            'data' => [[' Property No.:', $propertyNo]]
        ]
    ];
    $data2 = [
        [
            'aligns' => ['L', 'L'],
            'widths' => [$multiplier * 18, 
                         $multiplier * 67],
            'font-styles' => ['', ''],
            'type' => 'row-data',
            'data' => [[' Description:', $itemDescription]]
        ]
    ];
    $data3 = [
        [
            'aligns' => ['L', 'L', 'L', 'L'],
            'widths' => [$multiplier * 18, 
                         $multiplier * 21, 
                         $multiplier * 12, 
                         $multiplier * 34],
            'font-styles' => ['', 'B', '', 'B'],
            'type' => 'row-data',
            'data' => [[' Date Acquired:', $acquiredDate, 'Issued To:', $recievedByName]]
        ]
    ];
    $data4 = [
        [
            'aligns' => ['L', 'L'],
            'widths' => [$multiplier * 18, 
                         $multiplier * 67],
            'font-styles' => ['', 'B'],
            'type' => 'row-data',
            'data' => [[' Certified By:', $issuedByName],
                       [' Verified By:', '______________________________________________________']]
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('L', 'mm', $pageSize);
    $pdf->setHeaderLR(false, false);

    //set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    /*
    //Set margins
    $pdf->SetMargins(10, 35, 10);
    $pdf->SetHeaderMargin(10);*/
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);


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
    $img_file = $dir . '/resources/assets/images/label.png';
    $pdf->Image($img_file, 0, 0, $pageWidth, $pageHeight, 'PNG', '', '', false, 300, '', false, false, 0);

    $pdf->ln(13);

     //----Table data
    $pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
    $pdf->htmlTable($data1);
    $pdf->SetFont('Times', '', 7 + ($increaseFontSize * 7));
    $pdf->htmlTable($data2);
    $pdf->htmlTable($data3);
    $pdf->htmlTable($data4);

    // Barcode
    if ($propertyNo != 'N/A') {
        $pdf->setXY(89.3, 157);

        $type = 'code128';
        //$type = 'code39'; 
        $black = '000000'; // color in hexa 

        $code = $propertyNo; // barcode, of course ;)

        $pdf->StartTransform();
        $pdf->Rotate(90);
        $barcodeStyle = ['position' => 'S', 
                         'align' => 'C', 
                         'stretch' => true, 
                         'fitwidth' => false, 
                         'cellfitalign' => '', 
                         'border' => true, 
                         'hpadding' => 2, 
                         'vpadding' => 3, 
                         'fgcolor' => array(0, 0, 0), 
                         'bgcolor' => false, 
                         'text' => false, 
                         'font' => 'helvetica', 
                         'fontsize' => 8, 
                         'stretchtext' => 4];
        $pdf->write1DBarcode($code, 'C93', '', '', $pageHeight, 15, 0.4, $barcodeStyle, 'M');
        $pdf->StopTransform();
    } else {
        $pdf->setXY(89.3, 190);
        $pdf->SetFont('helvetica', '', 13);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->Cell($pageHeight, 15, '-- No Barcode --', 1, '', 'C');
        $pdf->StopTransform();
    }

    //Set document information
    $title = strtolower('label-' . $inventoryClassNo);
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