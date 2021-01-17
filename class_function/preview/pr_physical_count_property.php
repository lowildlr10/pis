<?php

function generatePCPPE($startDate, $endDate, $conn, $dir, $increaseFontSize = 0, $pageHeight = 297, $pageWidth = 210) {
	$items = array();
	$classification = $_REQUEST["class"];
	$categoryID = $_REQUEST["categoryID"];
	$itemClassification = "";
	$qry = $conn->query("SELECT * 
					 	 FROM tblitem_categories") 
						 or die(mysql_error($conn));

	while ($list1 = $qry->fetch_object()) {
		if ($list1->categoryID == $categoryID) {
			$itemClassification = $list1->category;
			break;
		}
	}

	$filename = "$itemClassification FM-FAS-PUR F25 [ $startDate - $endDate ].pdf";

	$qryPMF = $conn->query("SELECT * 
							FROM tbltemp_inventory_supply 
							ORDER BY id ASC") 
							or die(mysql_error($conn));



	while ($data = $qryPMF->fetch_object()) {
        //$data->description = iconv('UTF-8', 'windows-1252//IGNORE', $data->description);
		$items[] = array($data->documentNo, $data->description, $data->itemNo, $data->unitIssue, 
					     $data->unitValue, $data->quantity, $data->onHandCount, $data->quantityShortage, 
					     $data->valueShortage, $data->remarks);
	}

	$pdf = new PDF('L', 'mm', array($pageWidth, $pageHeight)); // A4"
	$pdf->AliasNbPages();
	$pdf->AddPage('L');
	$xCoor = $pdf->GetX();
	$yCoor = $pdf->GetY();

	#################################################################################################
							  				  # DOCUMENT HEADER # 
	#################################################################################################

	$pdf->Cell($pageHeight * 0.81,'4',"",'');
	$pdf->Cell($pageHeight * 0.12,'1',"","TLR");
	$pdf->Ln();
	$pdf->SetFont('Arial','B', 9);
	$pdf->Cell($pageHeight * 0.81,'4',"",'');
	$pdf->Cell($pageHeight * 0.12,'5',"  FM-FAS-PUR F20","LR");
	$pdf->Ln();
	$pdf->SetFont('Arial','', 8);
	$pdf->Cell($pageHeight * 0.81,'2',"",'');
	$pdf->Cell($pageHeight * 0.12,'2',"  Revision 1","LR");
	$pdf->Ln();
	$pdf->SetFont('Arial','', 8);
	$pdf->Cell($pageHeight * 0.81,'4',"",'');
	$pdf->Cell($pageHeight * 0.12,'5','  02-28-18',"LR");
	$pdf->Ln();
	$pdf->Cell($pageHeight * 0.81,'2',"",'');
	$pdf->Cell($pageHeight * 0.12,'1','',"BLR");

	$pdf->Ln();

	$pdf->SetXY($xCoor, $yCoor);
	$pdf->Image($dir . '/resources/assets/images/dostlogo.jpg' ,$xCoor, $yCoor, 18, 0,'JPEG');
	$pdf->setXY($xCoor + 19, $yCoor + 1);
	$pdf->SetFont('Arial','',11);
	$pdf->Cell($pageHeight * 0.2380952,'4','Republic of the Philippines','');
	$pdf->Ln();
	$pdf->SetX($pdf->GetX() + 19);
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell($pageHeight * 0.2380952,'4','DEPARTMENT OF SCIENCE AND TECHNOLOGY','');
	$pdf->Ln();
	$pdf->SetX($pdf->GetX() + 19);
	$pdf->SetFont('Arial','',11);
	$pdf->Cell($pageHeight * 0.2380952,'4','Cordillera Administrative Region','');
	$pdf->Ln();
	$pdf->SetX($pdf->GetX() + 19);
	$pdf->Cell($pageHeight * 0.2380952,'4','Km. 6, La Trinidad, Benguet','');
	$pdf->setY($yCoor+25);
	$pdf->SetFont('Arial','',10);

	#################################################################################################
						  			   # END OF DOCUMENT HEADER # 
	#################################################################################################

	$pdf->Ln(5);

	$pdf->SetFont('Arial', 'B', 13 + ($increaseFontSize * 13));
	$pdf->Cell($pageHeight * 0.93, "5", "REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT", "", "", "C");
	$pdf->Ln();

	$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Cell($pageHeight * 0.23, "5", $itemClassification, "B", "", "C");
	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Ln();

	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Cell($pageHeight * 0.23, "5", "(Type of Property, Plant and Equipment)", "", "", "C");
	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Ln();


	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Cell($pageHeight * 0.04, "5", "As at ", "", "", "C");
	$pdf->Cell($pageHeight * 0.19, "5", date("Y"), "B", "", "C");
	$pdf->Cell($pageHeight * 0.35, "5", " ", "", "", "C");
	$pdf->Ln(10);

	$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
	$pdf->Cell($pageHeight * 0.13, "5", " For which ", "", "", "R");
	$pdf->Cell($pageHeight * 0.15, "5", " Nancy A. Bantog  ", "B", "", "C");
	$pdf->Cell($pageHeight * 0.01, "5", "  , ", "", "", "C");
	$pdf->Cell($pageHeight * 0.13, "5", " Regional Director ", "B", "", "C");
	$pdf->Cell($pageHeight * 0.01, "5", " ", "", "", "C");
	$pdf->Cell($pageHeight * 0.06, "5", " DOST-CAR ", "B", "", "C");
	$pdf->SetFont('Arial', '', 10 + ($increaseFontSize * 10));
	$pdf->Cell($pageHeight * 0.27, "5", " is accountable, having assumed such accountability on ", "", "", "C");
	$pdf->Cell($pageHeight * 0.13, "5", " Nov. 9, 2015 ", "B", "", "C");
	$pdf->Cell($pageHeight * 0.04, "5", " ", "", "", "L");
	$pdf->Ln();

	$pdf->SetFont('Arial', 'I', 10 + ($increaseFontSize * 10));
	$pdf->Cell($pageHeight * 0.13, "5", " ", "", "", "R");
	$pdf->Cell($pageHeight * 0.16, "5", " (name of accountable officer) ", "", "", "L");
	$pdf->Cell($pageHeight * 0.14, "5", " (Official Designation) ", "", "", "L");
	$pdf->Cell($pageHeight * 0.06, "5", " (Agency) ", "", "", "L");
	$pdf->Cell($pageHeight * 0.27, "5", "  ", "", "", "C");
	$pdf->Cell($pageHeight * 0.17, "5", " (Date of Assumption) ", "", "", "L");
	$pdf->Ln(10);

	$pdf->SetFont('Arial', 'B', 9 + ($increaseFontSize * 9));
	$pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C"));
	$pdf->SetWidths(array($pageHeight * 0.06, $pageHeight * 0.2, $pageHeight * 0.07, $pageHeight * 0.09,
						  $pageHeight * 0.08, $pageHeight * 0.1, $pageHeight * 0.08, $pageHeight * 0.16,
						  $pageHeight * 0.09));
	$pdf->Row(array("ARTICLE", "DESCRIPTION", "PROPERTY", "UNIT OF",
					"UNIT VALUE", "QUANTITY PER", "QUANTITY PER", "SHORTAGE / OVERAGE",
					"REMARKS"));

	$pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C", "C"));
	$pdf->SetWidths(array($pageHeight * 0.06, $pageHeight * 0.2, $pageHeight * 0.07, $pageHeight * 0.09,
						  $pageHeight * 0.08, $pageHeight * 0.1, $pageHeight * 0.08, $pageHeight * 0.08,
						  $pageHeight * 0.08, $pageHeight * 0.09));

	$pdf->Row(array("", "", "NUMBER", "MEASURE",
					"", "PROPERTY CARD", "PHYSICAL COUNT", "QUANTITY",
					"VALUE", ""));

	$pdf->SetFont('Arial', '', 9 + ($increaseFontSize * 9));
	$pdf->SetAligns(array("C", "L", "C", "C", "C", "C", "C", "C", "C", "L"));

	foreach ($items as $key => $item) {
		$pdf->Row($item);
	}

	$pdf->Ln(10);

	$pdf->Cell($pageHeight * 0.6, "5", "Prepared by:", "", "", "L");
	$pdf->Cell($pageHeight * 0.33, "5", "Approved by:", "", "", "L");
	$pdf->Ln(10);

	$pdf->Cell($pageHeight * 0.26, "5", "", "B", "", "L");
	$pdf->Cell($pageHeight * 0.34, "5", "", "", "", "L");
	$pdf->Cell($pageHeight * 0.16, "5", "", "B", "", "L");
	$pdf->Cell($pageHeight * 0.17, "5", "", "", "", "L");
	$pdf->Ln();

	$pdf->Cell($pageHeight * 0.26, "5", "Property and Supply Officer", "", "", "C");
	$pdf->Cell($pageHeight * 0.34, "5", "", "", "", "");
	$pdf->Cell($pageHeight * 0.16, "5", "Head", "", "", "C");
	$pdf->Cell($pageHeight * 0.17, "5", "", "", "", "L");
	$pdf->Ln();

	$pdf->SetTitle($filename);

	if (!isset($_REQUEST['preview'])) {
		
		$pdf->Output($filename, 'D');
	} else {
		$pdf->Output($filename, 'I');
	}
}
?>