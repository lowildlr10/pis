<?php

function generateDV($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$qryOBRs = $conn->query("SELECT ors.poNo, ors.id, .ors.orsNo, ors.poNo, ors.address, 
                                    ors.payee, ors.signatoryReq, dv.particulars, dv.paymentMode, 
                                    ors.amount, dv.dvNo, dv.dvDate 
                            FROM tblors ors 
                            INNER JOIN tbldv dv
                            ON ors.id = dv.orsID
                            WHERE dv.orsID = '". $pid ."' 
                            OR ors.id = '" . $pid . "'");

    if (mysqli_num_rows($qryOBRs)) {
        $data = $qryOBRs->fetch_object();
        $poNo = $data->poNo;
        $dvNo = $data->dvNo;
        $dvDate = $data->dvDate;
        $_paymentMode = $data->paymentMode;
        $_payee = $data->payee;
        $address = $data->address;
        $particulars = $data->particulars;
        $amount = number_format($data->amount, 2);
        $_signatory = $data->signatoryReq;
        $sign1 = "SHELA LORRAINE T. COSALAN";
        $sign2 = "Nancy A. Bantog";
    }

    $qryEmp = $conn->query("SELECT firstname, middlename, lastname  
                            FROM tblemp_accounts 
                            WHERE empID = '" . $_payee . "'");
    $qrySuppliers = $conn->query("SELECT company_name 
                                  FROM tblbidders 
                                  WHERE bidderID = '" . $_payee . "'");
    $qrySignatory = $conn->query("SELECT name, position 
                            FROM tblsignatories 
                            WHERE signatoryID = '" . $_signatory . "'");

    if (!empty($poNo)) {
        if (mysqli_num_rows($qrySuppliers)) {
            $data = $qrySuppliers->fetch_object();
            $payee = $data->company_name;
        }
    } else {
        if (mysqli_num_rows($qryEmp)) {
            $data = $qryEmp->fetch_object();
            if (!empty($data->middlename)) {
                $payee = $data->firstname . " " . $data->middlename[0] . ". " . $data->lastname;
            } else {
                $payee = $data->firstname . " " . $data->lastname;
            }
        }
    }
    

    if (mysqli_num_rows($qrySignatory)) {
        $data = $qrySignatory->fetch_object();
        $signatory = $data->name;
    }

    $multiplier = 100 / 91.4296;

    //$particulars = iconv('UTF-8', 'windows-1252//IGNORE', $particulars);
    $tableData[] = [$particulars, "", "a. A.III.b.1<br>b. A.III.c.1<br>c. A.III.c.2", ""];

    $headData = [
        [
            'aligns' => ['L', 'L', 'L', 'L'],
            'widths' => [10.4762 * $multiplier, 38.095 * $multiplier,
                         26.19 * $multiplier, 16.6684 * $multiplier],
            'font-styles' => ['B', 'B', '', ''],
            'type' => 'other',
            'data' => [['Payee', $payee, 
                        'TIN/Employee No.:<br>_____________________________', 
                        'ORS/BURS No.:<br>__________________']]
        ], [
            'col-span' => true,
            'col-span-key' => ['0', '1-3'],
            'aligns' => ['L', 'L', 'L', 'L'],
            'widths' => [10.4762 * $multiplier, 80.9534 * $multiplier, '', ''],
            'font-styles' => ['B', 'B', 'B', 'B'],
            'type' => 'other',
            'data' => [["Address", $address]]
        ]
    ];
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C'],
            'widths' => [46.667 * $multiplier, 10.952 * $multiplier, 
                         17.143 * $multiplier, 16.667 * $multiplier],
            'font-styles' => ['', '', '', ''],
            'type' => 'row-data',
            'data' => [["Particulars", 'Responsibility Center', 'MFO/PAP', 'Amount']]
        ], [
            'aligns' => ['L', 'C', 'C', 'R'],
            'widths' => [46.667 * $multiplier, 10.952 * $multiplier, 
                         17.143 * $multiplier, 16.667 * $multiplier],
            'font-styles' => ['', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ], [
            'aligns' => ['C', 'C', 'C', 'R'],
            'widths' => [46.667 * $multiplier, 10.952 * $multiplier, 
                         17.143 * $multiplier, 16.667 * $multiplier],
            'font-styles' => ['B', 'B', 'B', 'B'],
            'type' => 'other',
            'data' => [["Amount Due", "", "", $amount]]
        ]
    ];

    $items = [
        [
            'aligns' => ['C', 'C', 'C', 'C'],
            'widths' => [47.15 * $multiplier, 17.34 * $multiplier,
                         13.95 * $multiplier, 12.9896 * $multiplier],
            'font-styles' => ['', '', '', ''],
            'type' => 'other',
            'data' => [['Account Title', 'UACS Code', 'Debit', 'Credit']]
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-BUD F12');
    $pdf->setDocRevision('Revision 1');
    $pdf->setRevDate('02-28-18');
    $pdf->setHeaderLR(false, true);

    //set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //Set margins
    $pdf->SetMargins(10, 24, 10);
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
    $xCoor = $pdf->GetX();
    $yCoor = $pdf->GetY();

    // Header with Logo
    $pdf->Cell($pageWidth * 0.74762, '1', "", "TL", 0, "C");
    $pdf->Cell(0, '1', "", "TR");
    $pdf->Ln();

    $xCoor = $pdf->getX();
    $yCoor = $pdf->getY();

    $pdf->Image($dir . '/resources/assets/images/dostlogo.jpg', $xCoor + 16, $yCoor, 16, 0, 'JPEG');
    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell($pageWidth * 0.74762,'5','Republic of the Philippines', "L", "", "C");
    $pdf->Cell($pageWidth * 0, '5', "", "R");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'5','DEPARTMENT OF SCIENCE AND TECHNOLOGY', "L", "", "C");
    $pdf->Cell($pageWidth * 0, '5', "", "R");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'3','Cordillera Administrative Region', "L", "", "C");
    $pdf->Cell($pageWidth * 0, '3', "", "R");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'4','', "BL", "", "C");
    $pdf->Cell($pageWidth * 0, '4', "", "BR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'5','', "L", "", "C");
    $pdf->Cell($pageWidth * 0, '5', "Fund Cluster : 01", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'5', "DISBURSEMENT VOUCHER", "L", "", "C");
    $pdf->Cell($pageWidth * 0, '5', "Date : " . $dvDate, "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.74762,'5', "", "BL", "", "C");
    $pdf->Cell($pageWidth * 0, '5', "DV No. : " . $dvNo, "BLR");
    $pdf->Ln();

    $x = $pdf->getX();
    $y = $pdf->getY();

    $pdf->MultiCell($pageWidth * 0.10476, "3.8", "\nMode of \nPayment\n   ", "1");
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));

    $pdf->SetXY($x + $pageWidth * 0.10476, $y);
    $pdf->SetFont('ZapfDingbats','', 15 + ($increaseFontSize * 15));

    // Fill checkbox with check symbol
    $paymentMode = explode("-", $_paymentMode);

    if ($paymentMode[0] != "0") {
        $pdf->Text($x + $pageWidth * 0.13333, $y + 7, 3);
    }

    if ($paymentMode[1] != "0") {
        $pdf->Text($x + $pageWidth * 0.28095, $y + 7, 3);
    }

    if ($paymentMode[2] != "0") {
        $pdf->Text($x + $pageWidth * 0.49524, $y + 7, 3);
    }

    if ($paymentMode[3] != "0") {
        $pdf->Text($x + $pageWidth * 0.6, $y + 7, 3);
    }

    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 15));
    $pdf->Rect($x + $pageWidth * 0.12857, $y + 3, 5, 5);
    $pdf->Rect($x + $pageWidth * 0.27619, $y + 3, 5, 5);
    $pdf->Rect($x + $pageWidth * 0.49048, $y + 3, 5, 5);
    $pdf->Rect($x + $pageWidth * 0.59524, $y + 3, 5, 5);
    $pdf->MultiCell(0, "3.8", "\n \t\t\t\t\t\t\t\t\t  MDS Check \t\t\t\t\t\t\t\t\t\t\t\t\t   Commercial Check" . 
                                  "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    ADA" . 
                                  "\t\t\t\t\t\t\t\t\t\t\t\t\t      Others (Please specify) 
                                   \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                  "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                  "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                  "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                  "_____________________\n\n", "1");

    //----Table data
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->htmlTable($headData);
    $pdf->htmlTable($data);

    //--A
    $pdf->Cell($pageWidth * 0.02391, "5", "A.", "1");
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "5", "Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.", "R");
    $pdf->Ln();

    $pdf->Cell(0, "5", "", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.2619, "5", "", "L");
    $pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.390476, "5", strtoupper($signatory), "", "", "C");
    $pdf->Cell(0, "5", "", "R");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.2619, "5", "", "BL");
    $pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.390476, "5", "Printed Name, Designation and Signature of Supervisor", "B", "", "C");
    $pdf->Cell(0, "5", "", "BR");
    $pdf->Ln();

    //--B
    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.02381, "5", "B.", "1");
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "5", "Accounting Entry:", "R");
    $pdf->Ln();

    $pdf->htmlTable($items);

    $pdf->Cell($pageWidth * 0.466667, "6", "", "LR");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x + 2, $y + 2, 4, 4);

    $pdf->Cell($pageWidth * 0.171429, "6", "", "LR");
    $pdf->Cell($pageWidth * 0.1381, "6", "", "1");
    $pdf->Cell(0, "6", "", "1");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.466667, "6", "", "LR");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x + 2, $y + 2, 4, 4);

    $pdf->Cell($pageWidth * 0.171429, "6", "", "LR");
    $pdf->Cell($pageWidth * 0.1381, "6", "", "1");
    $pdf->Cell(0, "6", "", "1");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.466667, "6", "", "LR");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x + 2, $y + 2, 4, 4);

    $pdf->Cell($pageWidth * 0.171429, "6", "", "LR");
    $pdf->Cell($pageWidth * 0.1381, "6", "", "1");
    $pdf->Cell(0, "6", "", "1");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.466667, "6", "", "LR");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x + 2, $y + 2, 4, 4);

    $pdf->Cell($pageWidth * 0.171429, "6", "", "LR");
    $pdf->Cell($pageWidth * 0.1381, "6", "", "1");
    $pdf->Cell(0, "6", "", "1");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.466667, "6", "", "LR");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x + 2, $y + 2, 4, 4);

    $pdf->Cell($pageWidth * 0.171429, "6", "", "LR");
    $pdf->Cell($pageWidth * 0.1381, "6", "", "1");
    $pdf->Cell(0, "6", "", "1");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.466667, "2", "", "BLR");
    $pdf->Cell($pageWidth * 0.171429, "2", "", "BLR");
    $pdf->Cell($pageWidth * 0.1381, "2", "", "1");
    $pdf->Cell(0, "2", "", "1");
    $pdf->Ln();

    //--C & D
    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.02381, "5", "C.", "1");
    $pdf->Cell($pageWidth * 0.4429, "5", "Certified:", "1");
    $pdf->Cell($pageWidth * 0.02381, "5", "D.", "1");
    $pdf->Cell(0, "5", "Approved for Payment", "1");
    $pdf->Ln();

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.02381, "6", "", "L");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x, $y + 1, 8, 4);

    $pdf->Cell($pageWidth * 0.0381, "6", "", "");
    $pdf->Cell($pageWidth * 0.40476, "6", " Cash available", "R");
    $pdf->Cell(0, "6", "", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.02381, "6", "", "L");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x, $y + 1, 8, 4);

    $pdf->Cell($pageWidth * 0.0381, "6", "", "");
    $pdf->Cell($pageWidth * 0.40476, "6", " Subject to Authority to Debit Account (when applicable)", "R");
    $pdf->Cell(0, "6", "", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.02381, "6", "", "L");
    $x = $pdf->getX();
    $y = $pdf->getY();
    $pdf->Rect($x, $y + 1, 8, 4);

    $pdf->Cell($pageWidth * 0.0381, "6", "", "");
    $pdf->Cell($pageWidth * 0.40476, "6", " Supporting documents complete and amount claimed", "R");
    $pdf->Cell(0, "6", "", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.02381, "4", "", "BL");
    $pdf->Cell($pageWidth * 0.0381, "4", "", "B");
    $pdf->Cell($pageWidth * 0.40476, "4", "  proper", "BR");
    $pdf->Cell(0, "4", "", "BLR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "10", "Signature", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.3619, "10", "", "BLR");
    $pdf->Cell($pageWidth * 0.10476, "10", "Signature", "BLR", "", "C");
    $pdf->Cell(0, "10", "", "BLR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "5", "Printed Name", "BLR", "", "C");
    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.3619, "5", strtoupper($sign1), "BLR", "", "C");
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.10476, "5", "Printed Name", "BLR", "", "C");
    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "5", strtoupper($sign2), "BLR", "", "C");
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "10", "", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.3619, "10", "Head, Accounting Unit/Authorized Representative", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.10476, "10", "", "BLR", "", "C");
    $pdf->Cell(0, "10", "Agency Head/Authorized Representative", "BLR", "", "C");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "5", "Date", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.3619, "5", "", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.10476, "5", "Date", "BLR", "", "C");
    $pdf->Cell(0, "5", "", "BLR", "", "C");
    $pdf->Ln();

    //--E
    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.02381, "5", "E.", "1");
    $pdf->Cell($pageWidth * 0.7143, "5", "Receipt of Payment", "1");
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, "5", "JEV  No.", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "5", "Check/   ADA ", "LR", "", "C");
    $pdf->Cell($pageWidth * 0.17619, "5", "", "LR");
    $pdf->Cell($pageWidth * 0.11429, "5", "", "LR");
    $pdf->Cell($pageWidth * 0.10476, "5", "Date:", "LR");
    $pdf->Cell($pageWidth * 0.2381, "5", "Bank Name & Account Number:", "LR");
    $pdf->Cell(0, "5", "", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "5", "No. :", "BLR", "", "C");
    $pdf->Cell($pageWidth * 0.17619, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.11429, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.10476, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.2381, "5", "", "BLR");
    $pdf->Cell(0, "5", "", "BLR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476, "10", "Signature:", "LR", "", "C");
    $pdf->Cell($pageWidth * 0.17619, "5", "", "LR");
    $pdf->Cell($pageWidth * 0.11429, "5", "", "LR");
    $pdf->Cell($pageWidth * 0.10476, "5", "Date:", "LR");
    $pdf->Cell($pageWidth * 0.2381, "5", "Printed Name:", "LR");
    $pdf->Cell(0, "5", "Date", "LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.28095, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.11429, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.10476, "5", "", "BLR");
    $pdf->Cell($pageWidth * 0.2381, "5", "", "BLR");
    $pdf->Cell(0, "5", "", " LR");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.7381, "5", "Official Receipt No. & Date/Other Documents", "1");
    $pdf->Cell(0, "5", "", "BLR");
    $pdf->Ln();

    //Set document information
    $title = 'dv_' . $pid;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Purchase Order');
    $pdf->SetKeywords('PO, Purchase, Order, Purchase Order');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}

?>