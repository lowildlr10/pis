<?php

function generatePMF($startDate, $endDate, $conn, $dir, $increaseFontSize = 0, $pageHeight = 330, $pageWidth = 216) {
	$items = array();
	$filename = "FM-FAS-PUR F17 [ $startDate - $endDate ].pdf";

	$qryPMF = $conn->query("SELECT * 
							FROM tbltemp_procurement_monitoring 
							ORDER BY LENGTH(prDate), prDate ASC") 
							or die(mysql_error($conn));

	while ($data = $qryPMF->fetch_object()) {
		$poApprovalDates = explode(";", $data->poApprovalDate);
		$supplier = explode(";", $data->supplier);
		$particulars = explode(";", $data->particulars);
		$poRecievedDate = explode(";", $data->poRecievedDate);
		$deliveredDate = explode(";", $data->deliveredDate);
		$invoiceNo = explode(";", $data->invoiceNo);
		$inspectedBy = explode(";", $data->inspectedBy);
		$requiredDays = explode(";", $data->requiredDays);
		$actualDays = explode(";", $data->actualDays);
		$differences = explode(";", $data->difference);
		$remarks = explode(";", $data->remarks);

		$str1 = "";
		$str2 = "";
		$str3 = "";
		$str4 = "";
		$str5 = "";
		$str6 = "";
		$str7 = "";
		$str8 = "";
		$str9 = "";
		$str10 = "";
		$str11 = "";

		if (count($poApprovalDates) > 1) {
			if (count($poApprovalDates) == 2) {
				$str1 .= $poApprovalDates[0];
			} else {
				foreach ($poApprovalDates as $key1 => $val1) {
					if ($key1 < (count($poApprovalDates) - 2)) {
						$str1 .= $val1 . ", ";
					} else if ((count($poApprovalDates) - 2) == $key1) {
						$str1 .= "and " . $val1;
					}
					
				}
			}	
		} else {
			$str1 = "";
		}	

		if (count($supplier) > 1) {
			if (count($supplier) == 2) {
				$str2 .= $supplier[0];
			} else {
				foreach ($supplier as $key2 => $val2) {
					if ($key2 < (count($supplier) - 2)) {
						$str2 .= $val2 . ", ";
					} else if ((count($supplier) - 2) == $key2) {
						$str2 .= "and " . $val2;
					}
					
				}
			}	
		} else {
			$str2 = "";
		}

		if (count($particulars) > 1) {
			if (count($particulars) == 2) {
				$str3 .= $particulars[0];
			} else {
				foreach ($particulars as $key3 => $val3) {
					if ($key3 < (count($particulars) - 2)) {
						$str3 .= $val3 . ", ";
					} else if ((count($particulars) - 2) == $key3) {
						$str3 .= "and " . $val3;
					}
					
				}
			}	
		} else {
			$str3 = "";
		}

		if (count($poRecievedDate) > 0) {
			if (count($poRecievedDate) == 1) {
				$str4 .= $poRecievedDate[0];
			} else {
				foreach ($poRecievedDate as $key4 => $val4) {
					if ($key4 < (count($poRecievedDate) - 2)) {
						$str4 .= $val4 . ", ";
					} else if ((count($poRecievedDate) - 2) == $key4) {
						$str4 .= "and " . $val4;
					}
					
				}
			}	
		} else {
			$str4 = "";
		}

		if (count($deliveredDate) > 0) {
			if (count($deliveredDate) == 1) {
				$str5 .= $deliveredDate[0];
			} else {
				foreach ($deliveredDate as $key5 => $val5) {
					if ($key5 < (count($deliveredDate) - 2)) {
						$str5 .= $val5 . ", ";
					} else if ((count($deliveredDate) - 2) == $key5) {
						$str5 .= "and " . $val5;
					}
					
				}
			}	
		} else {
			$str5 = "";
		}

		if (count($invoiceNo) > 1) {
			if (count($invoiceNo) == 2) {
				$str6 .= $invoiceNo[0];
			} else {
				foreach ($invoiceNo as $key6 => $val6) {
					if ($key6 < (count($invoiceNo) - 2)) {
						$str6 .= $val6 . ", ";
					} else if ((count($invoiceNo) - 2) == $key6) {
						$str6 .= "and " . $val6;
					}
					
				}
			}	
		} else {
			$str6 = "";
		}

		if (count($inspectedBy) > 1) {
			if (count($inspectedBy) == 2) {
				$str7 .= $inspectedBy[0];
			} else {
				foreach ($inspectedBy as $key7 => $val7) {
					if ($key7 < (count($inspectedBy) - 2)) {
						$str7 .= $val7 . ", ";
					} else if ((count($inspectedBy) - 2) == $key7) {
						$str7 .= "and " . $val7;
					}
					
				}
			}	
		} else {
			$str7 = "";
		}

		if (count($requiredDays) > 1) {
			if (count($requiredDays) == 2) {
				$str8 .= $requiredDays[0] . " day/s";
			} else {
				foreach ($requiredDays as $key8 => $val8) {
					if ($key8 < (count($requiredDays) - 2)) {
						$str8 .= "(" . $val8 . "), ";
					} else if ((count($requiredDays) - 2) == $key8) {
						$str8 .= "and (" . $val8 . ") day/s";
					}
					
				}
			}	
		} else {
			$str8 = "";
		}

		if (count($actualDays) > 1) {
			if (count($actualDays) == 2) {
				$str9 .= $actualDays[0] . " day/s";
			} else {
				foreach ($actualDays as $key9 => $val9) {
					if ($key9 < (count($actualDays) - 2)) {
						$str9 .= "(" . $val9 . "), ";
					} else if ((count($actualDays) - 2) == $key9) {
						$str9 .= "and (" . $val9 . ") day/s";
					}
					
				}
			}	
		} else {
			$str9 = "";
		}

		if (count($differences) > 1) {
			if (count($differences) == 2) {
				$str10 .= $differences[0] . " day/s";
			} else {
				foreach ($differences as $key10 => $val10) {
					if ($key10 < (count($differences) - 2)) {
						$str10 .= "(" . $val10 . "), ";
					} else if ((count($differences) - 2) == $key10) {
						$str10 .= "and (" . $val10 . ") day/s";
					}
					
				}
			}	
		} else {
			$str10 = "";
		}

		if (count($remarks) > 1) {
			if (count($remarks) == 2) {
				$str11 .= $remarks[0];
			} else {
				foreach ($remarks as $key11 => $val11) {
					if ($key11 < (count($remarks) - 2)) {
						$str11 .= "(" . $val11 . "), ";
					} else if ((count($remarks) - 2) == $key11) {
						$str11 .= "and (" . $val11 . ")";
					}
					
				}
			}	
		} else {
			$str11 = "";
		}

		$items[] = array($data->moYear, $data->prNo, $data->prDate, $data->abstractApprovalDate, 
					     $str1, $str2, $str3, $str4, $str5, $str6, $str7, $str8, $str9, $str10, $str11);
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
	$pdf->Cell($pageHeight * 0.12,'5',"  FM-FAS-PUR F17","LR");
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
	$pdf->Cell($pageHeight * 0.93, "5", "PROCUREMENT MONITORING FORM", "", "", "C");
	$pdf->Ln(10);

	$pdf->SetFont('Arial', 'B', 7 + ($increaseFontSize * 7));
	$pdf->SetAligns(array("C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C", "C"));
	$pdf->SetWidths(array($pageHeight * 0.045, $pageHeight * 0.07, $pageHeight * 0.053, $pageHeight * 0.085,
						  $pageHeight * 0.08, $pageHeight * 0.07, $pageHeight * 0.08, $pageHeight * 0.053,
						  $pageHeight * 0.053, $pageHeight * 0.05, $pageHeight * 0.06, $pageHeight * 0.060,
						  $pageHeight * 0.062, $pageHeight * 0.062, $pageHeight * 0.05));
	$pdf->Row(array("MO. / YEAR", "PURCHASE REQUEST NO.", "P.R. DATE", 
					"DATE OF APPROVAL OF ABSTRACT OF BIDS", "DATE OF APPROVAL OF P.O.", 
					"SUPPLIER", "PARTICULARS", "DATE P.O. RECEIVED", 
					"DATE DELIVERED", "INVOICE NO.", "INSPECTED BY", 
					"REQUIRED NO. OF DAYS", "ACTUAL NO. OF DAYS", "DIFFERENCE", "REMARKS"));

	$pdf->SetFont('Arial', '', 7 + ($increaseFontSize * 7));
	$pdf->SetAligns(array("C", "C", "C", "C", "C", "L", "L", "C", "C", "C", "L", "C", "C", "C", "L"));

	foreach ($items as $item) {
		$pdf->Row($item);
	}

	$pdf->Ln(5);

	$pdf->Cell($pageHeight * 0.12, "5", "", "", "", "C");
	$pdf->Cell($pageHeight * 0.38, "5", "Prepared By:", "", "", "L");
	$pdf->Cell($pageHeight * 0.433, "5", "Noted by:", "", "", "L");
	$pdf->Ln();

	$pdf->Cell($pageHeight * 0.12, "5", "", "", "", "C");
	$pdf->Cell($pageHeight * 0.38, "5", "_________________________________________", "", "", "L");
	$pdf->Cell($pageHeight * 0.38, "5", "_________________________________________", "", "", "C");
	$pdf->Cell($pageHeight * 0.053, "5", "", "", "", "C");
	$pdf->Ln();

	$pdf->Cell($pageHeight * 0.12, "5", "", "", "", "C");
	$pdf->Cell($pageHeight * 0.38, "5", "Property and Supply Officer", "", "", "L");
	$pdf->Cell($pageHeight * 0.38, "5", "Chief, Administrative Officer", "", "", "C");
	$pdf->Cell($pageHeight * 0.053, "5", "", "", "", "C");

	$pdf->SetTitle($filename);

	if (!isset($_REQUEST['preview'])) {
		$pdf->Output($filename, 'D');
	} else {
		$pdf->Output($filename, 'I');
	}
}
?>