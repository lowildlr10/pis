<?php

function generateBURS($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$orsNo = $_REQUEST["orsNo"];
    $statObligations = [];

    $qryResult = $conn->query("SELECT poNo, orsNo, serialNo, orsDate, payee, office, address, particulars, 
                                      uacsObjectCode, amount, sig1.name 
                               as sign1Name, sig1.position 
                               as sign1Pos, sig2.name 
                               as sign2Name, sig2.position 
                               as sign2Pos, signatoryReqDate, signatoryBudgetDate 
                               FROM tblors 
                               LEFT JOIN tblsignatories as sig1 
                               ON signatoryReq = sig1.signatoryID 
                               LEFT JOIN tblsignatories sig2 
                               ON signatoryBudget = sig2.signatoryID WHERE id='".$pid."'") or die(mysqli_error($conn));

    if (mysqli_num_rows($qryResult)) {
        $data = $qryResult->fetch_object();
        $effectiveDate = "";
        $poNo = $data->poNo;
        $orsNo = $data->orsNo;
        $serialNo = $data->serialNo;
        $orsDate = $data->orsDate;
        $_payee = $data->payee;
        $office = $data->office;
        $address = $data->address;
        $particulars = $data->particulars;
        $acntCode = $data->uacsObjectCode;
        $amt = $data->amount;
        $sign1 = $data->sign1Name;
        $sign2 = $data->sign2Name;
        $position1 = $data->sign1Pos;
        $position2 = $data->sign2Pos;
        $sDate1 = $data->signatoryReqDate;
        $sDate2 = $data->signatoryBudgetDate;
    }

    if (!empty($poNo)) {
        $qrySuppliers = $conn->query("SELECT company_name 
                                      FROM tblbidders 
                                      WHERE bidderID='" . $_payee . "'") 
                               or die(mysqli_error($conn));

        if (mysqli_num_rows($qrySuppliers)) {
            $data = $qrySuppliers->fetch_object();
            $payee = $data->company_name;
        }
    } else {
        $qrySuppliers = $conn->query("SELECT * 
                                      FROM tblemp_accounts 
                                      WHERE empID='" . $_payee . "'") 
                               or die(mysqli_error($conn));

        if (mysqli_num_rows($qrySuppliers)) {
            $data = $qrySuppliers->fetch_object();
            if (!empty($data->middlename)) {
                $payee = $data->firstname . " " . $payee = $data->middlename[0] . ". " . $payee = $data->lastname;
            } else {
                $payee = $data->firstname . " " . $payee = $data->lastname;
            }
            
        }
    }

    $multiplier = 100 / 91.427;
    $itemAmount = number_format($amt, 2);
    //$particulars = iconv('UTF-8', 'windows-1252//IGNORE', $particulars);
    $tableData[] = ["19 001 03000 14", $particulars, "3-Regional <br>Office<br> 
    A.III.c.1 <br>
    A.III.b.1 <br>
    A.III.c.2 <br>", $acntCode, "<br><br>"];

    $headData = [
        [
            'aligns' => ['C', 'L'],
            'widths' => [16.19 * $multiplier, 75.24 * $multiplier],
            'font-styles' => ['', 'B'],
            'type' => 'other',
            'data' => [["Payee", $payee],
                       ["Office", $office],
                       ["Address", $address]]
        ]
    ];
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C', 'C'],
            'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier, 
                         14.29 * $multiplier, 19.40 * $multiplier],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => [["Responsibility<br>Center", 'Particulars', 'MFO/PAP', 'UACS Object Code', 'Amount']]
        ], [
            'aligns' => ['C', 'L', 'L', 'C', 'R'],
            'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier, 
                         14.29 * $multiplier, 19.40 * $multiplier],
            'font-styles' => ['', '', '', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ], [
            'aligns' => ['C', 'C', 'C', 'C', 'R'],
            'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier, 
                         14.29 * $multiplier, 19.40 * $multiplier],
            'font-styles' => ['', 'B', '', '', 'B'],
            'type' => 'other',
            'data' => [['', 'Total', '', '', $itemAmount]]
        ]
    ];

    for ($i = 1; $i <= 6 ; $i++) {
        $obligationValue = "";
        

        if ($i == 1) {
            $obligationValue = number_format($amt, 2);
        } else {
            $obligationValue = "";
        }

        $statObligations[] = ['', '', '', $obligationValue, '', '', '', ''];
    }

    $dataFooter = [
        [
            'col-span' => true,
            'col-span-key' => ['0', '1-7'],
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier],
            'font-styles' => ['', '', '', '', '', '', '', ''],
            'type' => 'other',
            'data' => [['', '', '', '', '', '', '', '']]
        ], [
            'col-span' => true,
            'col-span-key' => ['0', '1-7'],
            'aligns' => ['L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier],
            'font-styles' => ['B', 'B', '', '', '', '', '', ''],
            'type' => 'other',
            'data' => [['C.', 'STATUS OF UTILIZATION', '', '', '', '', '', '']]
        ], [
            'col-span' => true,
            'col-span-key' => ['0-2', '3-7'],
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier],
            'font-styles' => ['B', '', '', 'B', '', '', '', ''],
            'type' => 'other',
            'data' => [['Reference', '', '', 'Amount', '', '', '', '']]
        ], [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier],
            'font-styles' => ['', '', '', '', '', '', '', ''],
            'type' => 'other',
            'data' => [["Date", "Particulars", "BURS/JEV/RCI/RADAI/RTRAI No.", "Utilization <br><br>(a)", 
                        "Payable <br><br>(b)", "Payment <br><br>(c)", "Not Yet Due <br><br>(a-b)", 
                        "Due and Demandable <br>(b-c)"]]
        ], [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier, 
                         10.952 * $multiplier, 10.952 * $multiplier],
            'font-styles' => ['', '', '', 'B', '', '', '', ''],
            'type' => 'other',
            'data' => $statObligations
        ]
    ];

    //-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-BUD F04');
    $pdf->setDocRevision('Revision 1');
    $pdf->setRevDate('02-28-18');
    $pdf->setHeaderLR(false, true);

    //set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //Set margins
    $pdf->SetMargins(10, 30, 10);
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
    // Header with Logo
    $pdf->SetFont('helvetica', 'B', 14.5 + ($increaseFontSize * 14.5));
    $pdf->Cell($pageWidth * 0.5714, '8', "BUDGET UTILIZATION REQUEST AND STATUS", "TLR", 0, "C");
    $pdf->Cell(0, '8', "", "TR");
    $pdf->Ln();

    $xCoor = $pdf->getX();
    $yCoor = $pdf->getY();

    $pdf->Image($dir . '/resources/assets/images/dostlogo.jpg', $xCoor + 4, $yCoor, 16, 0, 'JPEG');
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.10476,'4','','L');
    $pdf->Cell($pageWidth * 0.466667,'4','Republic of the Philippines','R');
    $pdf->SetFont('helvetica','IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '4', "Serial No. \t\t\t\t\t\t\t\t\t: " . $serialNo, "R");
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.10476,'4','','L');
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.466667, '4', 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', 'R');
    $pdf->Cell(0, '4', "", "R");
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 9 + ($increaseFontSize * 9));
    $pdf->Cell($pageWidth * 0.10476,'4','','L');
    $pdf->Cell($pageWidth * 0.466667,'4','Cordillera Administrative Region','R');
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '4', "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t: " . $orsDate, "R");
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 9 + ($increaseFontSize * 9));
    $pdf->Cell($pageWidth * 0.10476,'4','','L');
    $pdf->Cell($pageWidth * 0.466667,'4','Km. 6, La Trinidad, Benguet','R');
    $pdf->Cell(0, '4', "", "R");
    $pdf->Ln();

    $pdf->SetFont('helvetica','IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.57143,'6', "Entity Name",'LRB', 0, "C");
    $pdf->SetFont('helvetica','IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, '6', "Fund Cluster \t\t\t\t: ____________________", "RB");
    $pdf->Ln();


    $pdf->SetFont('helvetica','', 9 + ($increaseFontSize * 9));
    //----Heead data
    $pdf->htmlTable($headData);

    //----Table data
    $pdf->htmlTable($data);

    $pdf->Cell($pageWidth * 0.0952,'7','A.','LRB');
    $pdf->Cell($pageWidth * 0.3667,'7','','R');
    $pdf->Cell($pageWidth * 0.1095,'7','B.','RB');
    $pdf->Cell(0,'7','','R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.0762,'5','','L');
    $pdf->Cell($pageWidth * 0.08095,'5','Certified:','');
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.30476,'5','Charges to appropriation/alloment ','');
    $pdf->Cell($pageWidth * 0.090476,'5','','L');
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.08095,'5','Certified:','');
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0,'5','Allotment available and obligated','R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.0762,'5','','L');
    $pdf->Cell($pageWidth * 0.3857,'5','necessary, lawful and under my direct supervision;','');
    $pdf->Cell($pageWidth * 0.090476,'5','','L');
    $pdf->Cell(0,'5','for the purpose/adjustment necessary as','R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.0762,'5','','L');
    $pdf->Cell($pageWidth * 0.3857,'5','and supporting documents valid, proper and legal.','');
    $pdf->Cell($pageWidth * 0.090476,'5','','L');
    $pdf->Cell(0,'5','indicated above.','R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.4619,'7','','RL');
    $pdf->Cell(0,'7','','R');
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.4619,'8',"Signature \t\t\t\t\t\t\t:       ______________________________",'LR');
    $pdf->Cell(0,'8',"Signature \t\t\t\t\t\t\t:      ______________________________",'R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.4619,'2',"",'LR');
    $pdf->Cell(0,'2',"",'R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.12857,'5',"Printed Name : ",'L');
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.28095,'5', $sign1,'B');
    $pdf->Cell($pageWidth * 0.05238,'5'," ",'R');
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.12857,'5',"Printed Name : ",'');
    $pdf->SetFont('helvetica','B', 10);
    $pdf->Cell($pageWidth * 0.28095,'5', $sign2,'B');
    $pdf->Cell(0,'5',"",'R');
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.4619,'4',"",'LR');
    $pdf->Cell(0,'4',"",'R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.12857,'5',"Position \t\t\t\t\t\t\t\t\t:   ",'L');
    $pdf->Cell($pageWidth * 0.28095,'5',$position1,'B');
    $pdf->Cell($pageWidth * 0.05238,'5'," ",'R');
    $pdf->Cell($pageWidth * 0.12857,'5',"Position \t\t\t\t\t\t\t\t\t:   ",'');
    $pdf->Cell($pageWidth * 0.28095,'5',$position2,'B');
    $pdf->Cell(0,'5',"",'R');
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.4619,'5',"\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, Requesting Office/Authorized",'LR');
    $pdf->Cell(0,'5',"\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, Budget Division/Unit/Authorized",'R');
    $pdf->Ln();
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.4619,'3',"\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative",'LR');
    $pdf->Cell(0,'3',"\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative",'R');
    $pdf->Ln();

    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.4619,'3',"",'LR');
    $pdf->Cell(0,'3',"",'R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.12857,'5',"Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ",'L');
    $pdf->Cell($pageWidth * 0.28095,'5', $sDate1,'B');
    $pdf->Cell($pageWidth * 0.05238,'5'," ",'R');
    $pdf->Cell($pageWidth * 0.12857,'5',"Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ",'');
    $pdf->Cell($pageWidth * 0.28095,'5', $sDate2,'B');
    $pdf->Cell(0,'5',"",'R');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.4619,'3',"",'LRB');
    $pdf->Cell(0,'3',"",'RB');
    $pdf->Ln();

    //----Footer data
    $pdf->htmlTable($dataFooter);

    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.5857, '10', "Date Received:", "", "L");
    $pdf->Cell(0, '10', "Date Released:", "", "L");

    //Set document information
    $title = 'burs_' . $pid;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Obligation Request and Status');
    $pdf->SetKeywords('ORS, Obligation, Request, Status, Obligation Request and Status');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}

?>