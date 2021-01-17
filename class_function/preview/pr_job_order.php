<?php

function generateJO($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
    $tableData = [];
    $prID = $_REQUEST['prID'];
    $amountWord = $_REQUEST['amount-word'];
    $workDesc = json_decode($_REQUEST['workDesc']);
    $poNo = $_REQUEST['print'];
    $qryEdit = $conn->query("SELECT pd.*, bs.company_name, bs.address, pr.procurementMode 
                            FROM tblpo_jo pd 
                            INNER JOIN tblbidders bs 
                            ON pd.awardedTo=bs.bidderID 
                            INNER JOIN tblpr pr 
                            ON pd.PRID=pr.PRID 
                            WHERE PONo='".$poNo."'");
    if (mysqli_num_rows($qryEdit)) {
        $data = $qryEdit->fetch_object();
        $poDate = $data->poDate;
        $supplier = $data->company_name;
        $dateDel = $data->deliveryDate;
        $placeDel = $data->placeDelivery;
        $address = $data->address;
        $award = $data->awardedTo;
        $signApp = $data->signatoryApp;
        $signDept = $data->signatoryDept;
        $signFunds = $data->signatoryFunds;
        $amountWord = $data->amountWords;

        $qrySig1 = $conn->query("SELECT name, position 
                                 FROM tblsignatories 
                                 WHERE signatoryID = '".$signApp."'");

        if (mysqli_num_rows($qrySig1) != -1){
            $val = $qrySig1->fetch_object();
            $appName = $val->name;
            $appPosition = $val->position;
        }

        $qrySig1 = $conn->query("SELECT empID, concat(firstname,' ',left(middlename,1),'. ',lastname) 
                                 AS name, position 
                                 FROM tblemp_accounts emp 
                                 WHERE empID='".$signDept."'");

        if (mysqli_num_rows($qrySig1) != -1) {
            $val = $qrySig1->fetch_object();
            $deptName = $val->name;
            $deptPosition = $val->position;
        }

        $qrySig1 = $conn->query("SELECT name, position 
                                 FROM tblsignatories 
                                 WHERE signatoryID='".$signFunds."'");

        if (mysqli_num_rows($qrySig1) != -1) {
            $val = $qrySig1->fetch_object();
            $fundsName = $val->name;
            $fundsPosition = $val->position;
        }
    }

    $itemNo = 0;
    $total = 0;
    $height = 70;
    $grandTotal = 0;

    $qryItems = $conn->query("SELECT unitIssue, quantity, itemDescription, amount 
                              FROM tblpo_jo_items 
                              WHERE excluded = 'n' 
                              AND poNo = '" . $poNo . "' 
                              AND unitIssue = 'J.O.' 
                              ORDER BY id ASC") 
                              or die(mysql_error($conn));

    while ($list = $qryItems->fetch_object()) {
        $itemNo++;

        $total = $list->quantity * $list->amount;
        $grandTotal += $total;
        $joUnit = $list->quantity . " " . $list->unitIssue;
        //$list->itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $list->itemDescription);
        $tableData[] = [$joUnit, 
                        $list->itemDescription, 
                        number_format($total, 2)];
    }

    $grandTotal = number_format($grandTotal, 2);
    $contentWidth = $pageWidth - 20;
    $data = [
        [
            'aligns' => ['C','C','C'],
            'widths' => [16.5, 54.2, 29.3],
            'font-styles' => ['B', 'B', 'B'],
            'type' => 'row-title',
            'data' => [['', 'JOB/WORK DESCRIPTION', 'Amount']]
        ], [
            'aligns' => ['C', 'L', 'R'],
            'widths' => [16.5, 54.2, 29.3],
            'font-styles' => ['', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ], [
            'aligns' => ['C', 'C', 'C'],
            'widths' => [16.5, 54.2, 29.3],
            'font-styles' => ['', 'B', ''],
            'type' => 'other',
            'data' => [['', '****** Nothing Follows ******', ''], 
                       ['', '', '']]
        ], [
            'col-span' => true,
            'col-span-key' => ['0-1', '2'],
            'aligns' => ['C', 'C', 'R'],
            'widths' => [16.5, 54.2, 29.3],
            'font-styles' => ['BI', 'BI', 'B'],
            'type' => 'other',
            'data' => [['TOTAL AMOUNT', '', $grandTotal]]
        ]
    ];

	//-------------------------------------------------------------------------------------------

    $pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F15');
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
    $pdf->Cell(0, '8', "JOB / WORK ORDER", 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.18,'5',"JOB ORDER NO:", 0, 'L');
    $pdf->SetFont('Times','B', 11 + ($increaseFontSize * 11));
    $pdf->Cell(0,'5', $poNo, 0,'L');

    $pdf->Ln();

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.18, '5', "DATE:", 0, 'L');
    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->Cell(0, '5', $poDate, 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.18, '5', "TO:", 0, 'L');
    $pdf->SetFont('Times','B', 11 + ($increaseFontSize * 11));
    $pdf->Cell(0, '5', $supplier, 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.18, '5', "ADDRESS:", 0, 'L');
    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell(0, '5', $address, 0, 'L');

    $pdf->Ln(5);

    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));

    $pdf->MultiCell($contentWidth
        ,'5',"Sir/Madam:\n\nIn connection with the existing regulations," . 
        " you are hereby authorized to undertake the indicated job/work below:", 
        0, 'L');

    $pdf->Ln(5);

    //----Table data
    $pdf->htmlTable($data);

    $pdf->Ln(5);

    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.18,'5',"Completion/Delivery : within the specified date of delivery", 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Times','IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.16,'5', "Place of Delivery:", 0, 'L');
    $pdf->SetFont('Times','B', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.84,'5', "DOST-CAR ", 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Times','IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.16,'5', "Date of Delivery:", 0, 'L');
    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.16,'5', $dateDel, 0, 'L');
    $pdf->SetFont('Times','IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.40,'5', "Payment Term:", 0, 0, 'R');
    $pdf->SetFont('Times','UB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($contentWidth * 0.29,'5', "After inspection and acceptance", 0, 0, 'R');

    $pdf->Ln(10);

    $pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell($contentWidth + 1,'5',
               "This order is authorized by the DEPARTMENT OF SCIENCE AND TECHNOLOGY, Cordillera Administrative Region " .
               "under DR. NANCY A. BANTOG, Regional Director in the amount not to exceed $amountWord " .
               "(Php " . $grandTotal . "). The cost of this WORK ORDER will be charged against DOST-CAR after work has been completed.",
               0, 'C');

    $pdf->Ln(5);

    $pdf->SetFont('Arial','IB', 10 + ($increaseFontSize * 10));
    $pdf->MultiCell($contentWidth + 1,'5',
               " In case of failure to make the full delivery within time specified above, " .
               "a penalty of one-tenth (1/10) of one percent for everyday of delay shall be imposed.",
               0, 'C');

    $pdf->Ln(10);

    $pdf->SetFont('Arial','', 10 + ($increaseFontSize * 10));
    $pdf->MultiCell($contentWidth + 1,'5',
               "Please submit your bill together with the original of this JOB/WORK ORDER to expedite payment.",
               0, 'C');

    $pdf->Ln(5);

    $pdf->SetFont('Times','I', 10 + ($increaseFontSize * 10));
    $pdf->MultiCell($contentWidth,'5', "Very truly yours,", 0, 'L');

    $pdf->Ln(5);

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.5,'5', "Requisitioning Office/Dept.:", 0, 'L');
    $pdf->Cell($contentWidth * 0.5,'5', "APPROVED:", 0, 'L');

    $pdf->Ln(13);

    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.55,'5', strtoupper($deptName), 0, 'L');
    $pdf->Cell($contentWidth * 0.45,'5', strtoupper($appName), 0, 0, 'L');

    $pdf->Ln();
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.55,'5', "Authorized Signatory", 0, 'L');
    $pdf->Cell($contentWidth * 0.45,'5', "Authorized Signatory", 0, 0, 'L');

    $pdf->Ln(13);

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth,'5', "Funds Available:", 0, 'L');

    $pdf->Ln(13);

    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth,'5', strtoupper($fundsName), 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth,'5', "             Authorized Signatory", 0, 'L');

    $pdf->Ln(13);

    $pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth,'5', "JOB/WORK ORDER RECEIVED BY:", 0, 'L');

    $pdf->Ln(8);

    $pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($contentWidth * 0.35,'5', "", "B", 'L');



    //Set document information
    $title = 'jo_' . $poNo;
    $pdf->SetCreator('PFMS');
    $pdf->SetAuthor('DOST-CAR');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Job Order');
    $pdf->SetKeywords('JO, Job, Order, Job Order');

    if (!isset($_REQUEST['preview'])) {
        $pdf->Output($title . '.pdf', 'D');
    } else {
        $pdf->Output($title . '.pdf', 'I');
    }
}
?>