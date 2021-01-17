<?php

function generatePO($pid, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$tableData = [];
	$groupData = array();
	$groupNumber = array();
	$poNo = $_REQUEST['print'];
	$prID = $_REQUEST['prID'];

	$qtyItems = json_decode($_REQUEST['qtyItems']);
	$itemDescription = json_decode($_REQUEST['itemDesc']);
	$unitCost = json_decode($_REQUEST['unitCost']);
	$totalAmount = json_decode($_REQUEST['totalAmount']);

	$appName = "";
	$deptName = "";
	$appPosition = "";

	$itemNo = 0;
	$grandTotal = 0;

	$qryGroupNumber = "SELECT groupNo, infoID FROM tblpr_info 
					   WHERE prID='". $prID ."' 
					   ORDER BY LENGTH(infoID), infoID ASC";		 
	$groupNo = $conn->query($qryGroupNumber);

	while ($grp = $groupNo->fetch_object()) {
		$groupNumber[] = $grp->groupNo;
		$groupData[] = array("grpNo" => $grp->groupNo, "infoID" => $grp->infoID);
	}

	$groupNumber = array_unique($groupNumber);

    $total = 0;
    $height = 70;
    $qryItems = $conn->query("SELECT unitIssue, quantity, itemDescription, amount, totalAmount 
                              FROM tblpo_jo_items 
                              WHERE excluded = 'n' 
                              AND poNo = '" . $poNo . "' 
                              ORDER BY id ASC") 
                              or die(mysql_error($conn));

    while ($list = $qryItems->fetch_object()) {
        $itemNo++;

        if (!empty($list->totalAmount)) {
            $total = $list->totalAmount;
        } else {
            $total = $list->quantity * $list->amount;
        }
        
        $grandTotal += $total;
        //$list->itemDescription = iconv('UTF-8', 'windows-1252//IGNORE', $list->itemDescription);
        $tableData[] = [$itemNo, $list->unitIssue, 
                        $list->itemDescription, $list->quantity, 
                        number_format($list->amount, 2), 
                        number_format($total, 2)];
    }

    foreach ($groupNumber as $grpNo) {
        foreach ($groupData as $grpData) {
            if ($grpData["grpNo"] == $grpNo) {
                $qryEdit = $conn->query("SELECT pd.*, bs.company_name, bs.address, pr.procurementMode 
                                         FROM tblpo_jo pd 
                                         INNER JOIN tblbidders bs 
                                         ON pd.awardedTo=bs.bidderID 
                                         INNER JOIN tblpr pr 
                                         ON pd.prID=pr.prID 
                                         WHERE PONo='".$poNo."'");
                break;
            }
        }

        if (mysqli_num_rows($qryEdit)) {
            $data = $qryEdit->fetch_object();
            $poDate = $data->poDate;
            $pid = $data->prID;
            $supplier = $data->company_name;
            $mode = $data->procurementMode;
            $placeDel = $data->placeDelivery;
            $dateDel = $data->deliveryDate;
            $delTerm = $data->deliveryTerm;
            $payTerm = $data->paymentTerm;
            $amt = $data->amountWords;
            $award = $data->awardedTo;
            $address = $data->address;
            $signatoryApp = $data->signatoryApp;
            $signatoryDept = $data->signatoryDept;
            $signatoryFunds = $data->signatoryFunds;

            $qrySig1 = $conn->query("SELECT name, position 
                                     FROM tblsignatories 
                                     WHERE signatoryID='".$signatoryApp."'");

            if(mysqli_num_rows($qrySig1) > 0){
                $val = $qrySig1->fetch_object();
                $appName = $val->name;
                $appPosition = $val->position;
            }

            $qrySig1 = $conn->query("SELECT empID, concat(firstname,' ',left(middlename,1),'. ',lastname) 
                                     AS name 
                                     FROM tblemp_accounts emp 
                                     WHERE empID='".$signatoryDept."'");

            if(mysqli_num_rows($qrySig1) > 0){
                $val = $qrySig1->fetch_object();
                $deptName = $val->name;
                $deptPosition = $val->name;
            }
        }
    }

    $grandTotal = number_format($grandTotal, 2);
    $data = [
        [
            'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
            'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
            'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
            'type' => 'row-title',
            'data' => [["Stock/\nProperty No.",'Unit','Description',
                        'Quantity','Unit Cost','Amount']]
        ], [
            'aligns' => ['C', 'C', 'L', 'C', 'R', 'R'],
            'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'row-data',
            'data' => $tableData
        ], [
            'aligns' => ['L', 'L', 'L', 'L', 'L', 'R'],
            'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'other',
            'data' => [['', '', '', '', '', ''], 
                       ['', '', '', '', '', '']]
        ], [
            'col-span' => true,
            'col-span-key' => ['0-1', '2-4', '5'],
            'aligns' => ['L', 'L', 'L', 'L', 'L', 'R'],
            'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
            'font-styles' => ['', '', '', '', '', ''],
            'type' => 'other',
            'data' => [['(Total Amount in Words)', '', 
                        $amt, '', '', 
                        $grandTotal]]
        ]
    ];

    //-------------------------------------------------------------------------------------------

	$pageSize = [$pageWidth, $pageHeight];

    //Create new PDF document
    $pdf = new PDF('P', 'mm', $pageSize);
    $pdf->setDocCode('FM-FAS-PUR F08');
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
    $pdf->SetFont('Times','B', 14 + ($increaseFontSize * 14));
    $pdf->MultiCell(0, 5, "PURCHASE ORDER", '', 'C');

    $pdf->ln(3);

    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));

    $_x1 = $pdf->GetX();
    $_y1 = $pdf->GetY();

    $_x = $pdf->GetX();
    $_y = $pdf->GetY();

    $x1_1 = $_x;
    $x1_2 = $_x + $pageWidth * 0.548;
    $x1_3 = $_x + $pageWidth * 0.905;

    $pdf->MultiCell($pageWidth * 0.0857, '7', "Supplier: ", "TL");
    $pdf->SetXY($_x + $pageWidth * 0.0857, $_y);
    $pdf->SetFont('Times','B',11 + ($increaseFontSize * 11));
    $pdf->MultiCell(0, '6', $supplier, "T", "L");
    $_x2 = $pdf->GetX();
    $_y2 = $pdf->GetY();
    $pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->MultiCell($pageWidth * 0.1, '7', 'P.O. No. :', "TL");
    $pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
    $pdf->SetFont('Times','B',11 + ($increaseFontSize * 11));
    $pdf->MultiCell(0, '7', $poNo, "TR");

    $pdf->ln();

    $_x = $pdf->GetX();
    $_y = $pdf->GetY();

    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->MultiCell($pageWidth * 0.0857, '5', "Address: ", "");
    $pdf->SetXY($_x + $pageWidth * 0.0857, $_y);
    $pdf->MultiCell($pageWidth * 0.462, '5', $address, "", "L");
    $_yTemp = $pdf->GetY();
    $pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->MultiCell($pageWidth * 0.1, '5', 'Date  : ', "", "L");
    $pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
    $pdf->SetFont('Times','B',11 + ($increaseFontSize * 11));
    $pdf->MultiCell(0, '5', $poDate, "");

    $pdf->ln();

    $_x = $pdf->GetX();
    $_yTemp += 2;

    $x2_1 = $_x;
    $x2_2 = $_x + $pageWidth * 0.548;
    $x2_3 = $_x + $pageWidth * 0.905;

    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->SetXY($_x, $_yTemp);
    $pdf->MultiCell('115', '7', "TIN: ________________________________________________", "L");
    $pdf->SetXY($_x + $pageWidth * 0.548, $_yTemp);
    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->MultiCell('43', '7', 'Mode of Procurement:', "L", "L");
    $pdf->SetXY($_x + $pageWidth * 0.752, $_yTemp);
    $pdf->SetFont('Times','B',11 + ($increaseFontSize * 11));
    $pdf->MultiCell(0, '7', $mode, "R");

    $pdf->Line($_x1, $_y1, $x2_1, $_yTemp);
    $pdf->Line($x1_2, $_y2 - 5, $x2_2, $_yTemp);
    $pdf->Line($x1_3, $_y2 - 5, $x2_3, $_yTemp);

    $pdf->ln(0);

    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));
    $pdf->Cell(0, '7', "Gentlemen:", "RLT");
    $pdf->ln();
    $pdf->Cell(0, '11', "                Please furnish this Office the following articles" . 
                            " subject to the terms and conditions contained herein:", "RL");
    $pdf->ln();

    $remainingWidth = $pageWidth - 20;
    $items = [
        [
            'aligns' => ['L', 'L', 'L', 'L'],
            'widths' => [(($remainingWidth * 0.17) / $remainingWidth) * 100, 
                         (($remainingWidth * 0.435) / $remainingWidth) * 100,
                         (($remainingWidth * 0.15) / $remainingWidth) * 100, 
                         (($remainingWidth * 0.245) / $remainingWidth) * 100],
            'font-styles' => ['', 'B', '', 'B'],
            'type' => 'other',
            'data' => [['Place of Delivery: ', $placeDel, 'Delivery Term: ', $delTerm]]
        ], [
            'aligns' => ['L', 'L', 'L', 'L'],
            'widths' => [(($remainingWidth * 0.17) / $remainingWidth) * 100, 
                         (($remainingWidth * 0.435) / $remainingWidth) * 100,
                         (($remainingWidth * 0.15) / $remainingWidth) * 100, 
                         (($remainingWidth * 0.245) / $remainingWidth) * 100],
            'font-styles' => ['', '', '', ''],
            'type' => 'other',
            'data' => [['Date of Delivery: ', $dateDel, 'Payment Term: ', $payTerm]]
        ]
    ];

    $pdf->htmlTable($items);

    //----Table data
    $pdf->htmlTable($data);

    // Footer
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->MultiCell($pageWidth * 0.905,'5',
                    "\n\t\t\t\t\t In case of failure to make the full delivery within the time " . 
                    "specified above, a penalty of one-tenth (1/10) of one \n" . 
                    "percent for every day of delay shall be imposed on the undelivered item/s.",'LR','L');
    $pdf->Cell($pageWidth * 0.905,'5',' ','LR');

    $pdf->Ln();
    $pdf->Cell($pageWidth * 0.0524,'5',' ','L');
    $pdf->Cell($pageWidth * 0.229,'5','Conforme:','');
    $pdf->Cell($pageWidth * 0.6236,'5',"Very Truly Yours,",'R','','C');
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.905,'10',' ','LR');

    $pdf->Ln();

    $pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.0952,'5','','L');
    $pdf->Cell($pageWidth * 0.424,'5','___________________________________', '','','L');
    $pdf->Cell($pageWidth * 0.3858,'5',"". $appName ."",'R','','C');

    $pdf->Ln();

    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.0952,'5','','L');
    $pdf->Cell($pageWidth * 0.438,'5',"\t Signature over Printed Name of Supplier", '','','L');
    $pdf->SetFont('Times', 'BI', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.119,'5',"Regional Director",'','','C');
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.2528,'5',"or Authorized Representative",'R','','C');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.0952,'5','','L');
    $pdf->Cell($pageWidth * 0.5,'5',"\t\t\t\t ______________________________",'','','L');
    $pdf->Cell($pageWidth * 0.3098,'5',"",'R','','C');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.238,'5','','L');
    $pdf->Cell($pageWidth * 0.45238,'5',"Date",'','','L');
    $pdf->Cell($pageWidth * 0.21462,'5','','R');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.905,'5','','LR');

    $pdf->Ln();

    $pdf->SetFont('Times', 'BI', 10 + ($increaseFontSize * 10));
    $pdf->MultiCell($pageWidth * 0.905,'5', "Please provide your bank details (account name, account number, business name) to facilitate payment processing after delivery. Landbank is preferred.",'LRB', "L");
    
    $pdf->Cell($pageWidth * 0.45238,'5','','L');
    $pdf->Cell($pageWidth * 0.45238,'5','','LR');

    $pdf->Ln();

    $pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.45238,'5','Fund Cluster : 01','L','','L');
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.45238,'5',"ORS/BURS No. : _____________________________",'LR','','L');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45238,'5',"Funds Available : ____________________________",'L','','L');
    $pdf->Cell($pageWidth * 0.45238,'5',"Date of the ORS/BURS : _______________________",'LR','','L');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45238,'5','','L');
    $pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.0857,'5',"Amount : ",'L','','L');
    $pdf->SetFont('Times','U',11 + ($increaseFontSize * 11));

    if ($grandTotal) {
        $pdf->Cell($pageWidth * 0.366667,'5',"Php $grandTotal",'R','','L');
    } else {
        $pdf->Cell($pageWidth * 0.366667,'5',"Php 0.00",'R','','L');
    }

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45238,'5','','L');
    $pdf->Cell($pageWidth * 0.45238,'5','','LR');

    $pdf->Ln();

    $pdf->SetFont('Times','BU',11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.45238,'5',"".strtoupper($deptName)."",'L','','C');
    $pdf->Cell($pageWidth * 0.45238,'5','','LR');
    $pdf->SetFont('Times','',11 + ($increaseFontSize * 11));

    $pdf->Ln();

    $pdf->SetFont('Times','I',11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.45238,'5','Signature over Printed Name of Chief','LR','','C');
    $pdf->Cell($pageWidth * 0.45238,'5','','R','L');

    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.45238,'5','Accountant/Head of Accounting Division/Unit ','BL','','C');
    $pdf->Cell($pageWidth * 0.45238,'5','','BLR','L');

    $pdf->Ln();

    //Set document information
    $title = 'po_' . $poNo;
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