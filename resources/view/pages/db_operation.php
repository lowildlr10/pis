<?php
	include_once("../../../config.php");
	include_once($dir . "dbcon.php");

	$data = array();
	$prID = "";
	$toggle = "";

	#====================================================
	// initialize POST data

	if (isset($_REQUEST["prID"])) {
		$prID = $_REQUEST["prID"];
	}

	if (isset($_REQUEST["data"])) {
		$data = $_REQUEST["data"];
	}

	if (isset($_REQUEST["toggle"])) {
		$toggle = $_REQUEST["toggle"];
	}

	#====================================================
	// generate the database value/s

	switch ($toggle) {
		case 'file-upload':
			$result = "";
			$prNo = $_POST['prNo'];
			$formType = $_POST['form-type'];
			$uploadDirectory = $dir . "uploads/" . $formType . "/" . $prNo . "/";
			$counter = 0;

			if (is_dir($uploadDirectory) == false ) {
			    mkdir($uploadDirectory, 0700);
			}

			foreach ($_FILES as $file) {
				if (!$file['error']) {
					$counter++;

					$path = $file['name'];
					$ext = pathinfo($path, PATHINFO_EXTENSION);
					$fileName = $formType . "-" . $prNo . "-" . $counter . "." . $ext;
					$validFile = true;

					while (file_exists($uploadDirectory . $formType . "-" . $prNo . "-" . $counter . "." . $ext)) {
						$counter++;
						$fileName = $formType . "-" . $prNo . "-" . $counter . "." . $ext;
					}

					if($file['size'] > (20000000)) {
						$validFile = false;
						$result = 'Oops!  Your file\'s size is to large.';
					}

					//if the file has passed the test
					if($validFile) {
						//move it to where we want it to be
						$isUploaded = move_uploaded_file($file['tmp_name'], $uploadDirectory . $fileName);

						if (!$isUploaded) {
							$result = 'There is an error uploading your e-signature.';
						}
					}
				} else {
					$result = 'There is an error uploading your e-signature.';
				}
			}

			echo $result;
			break;
		case 'delete-file':
			$uploadDirectory = $dir . "uploads/canvass/" . $prID . "/" . $data;
			
			if (!unlink($uploadDirectory)) {
			  	echo ("Error deleting $data!");
			} else {
			  	echo ("The file $data successfully deleted!");
			}

			break;
		case 'exclude-update':
			foreach ($data as $count => $val) {
				foreach ($val as $_value) {
					$value = explode("-", $_value);
					$valCount = count($value);

					if ($count == 0) {
						/*
						if ($valCount == 2) {
							$conn->query("UPDATE tblpr_info 
										  SET exclude = '".$value[1]."' 
										  WHERE infoID = '" .$value[0]. "'")
										or die(mysqli_error($conn));
						} else if ($valCount == 3) {
							$conn->query("UPDATE tblpr_info 
										  SET exclude = '".$value[2]."' 
										  WHERE infoID = '". $value[0] . "-" . $value[1] . "'")
										or die(mysqli_error($conn));
						}*/
						
					} else if ($count == 1) {
						if ($valCount == 2) {
							$conn->query("UPDATE tblpr_info 
										  SET groupNo = '".$value[1]."' 
										  WHERE infoID = '".$value[0]."'")
										or die(mysqli_error($conn));
						} else if ($valCount == 3) {
							$conn->query("UPDATE tblpr_info 
										  SET groupNo = '".$value[2]."' 
										  WHERE infoID = '". $value[0] . "-" . $value[1] ."'")
										or die(mysqli_error($conn));
						}
					}
				}
			}

			print_r($_POST['fileCanvass']);

			break;
		case 'get-group':
			$_data = array();
			$qryGroup = $conn->query("SELECT groupNo 
									  FROM tblpr_info 
									  WHERE prID = '" . $prID . "'") 
								      or die(mysqli_error($conn));

			while ($list = $qryGroup->fetch_object()) {
				$_data[] = $list->groupNo;
			}

			$_data = array_unique($_data);

			foreach ($_data as $value) {
				$data[] = $value;
			}

			echo json_encode($data);

			break;
		case 'get-bidder-count':
			$bidderCount = 0;
			$infoID = "";

			$qryPRInfo = $conn->query("SELECT infoID  
					    			   FROM tblpr_info  
					    			   WHERE prID = '" . $prID . "' 
					    			   AND groupNo = '" . $data[0] . "'")
									   or die(mysqli_error($conn));

			if (mysqli_num_rows($qryPRInfo)) {
				$item = $qryPRInfo->fetch_object();
				$infoID = $item->infoID;
			}
									 
			$qryItems = $conn->query("SELECT infoID, prID 
					    			  FROM tblbids_quotations 
					    			  WHERE prID = '" . $prID . "' 
					    			  AND infoID = '" . $infoID . "'")
									  or die(mysqli_error($conn));

			$bidderCount = mysqli_num_rows($qryItems);
			echo $bidderCount;
			break;
		case 'save-abstract':
			$bidData = array();
			$arrayTest = array();
			$abstractDate = $data[11];
			$infoID = $data[1];
			$oldBidCount = $_POST["bidCount"];

			$oldAwardedTo = "";
			$poNo = "";
			$orsID = "";
			$prStatus = "";

			$placeDelivery = "DOST-CAR";
			$deliveryDate = "Within 15 days of receipt of this purchase order.";
			$deliveryTerm = "Complete";
			$paymentTerm = "After Inspection and Acceptance";
			$forApproval = 'n';
			$arrPOS = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
							'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			$allowed_tags = "";

			$qryPO = $conn->query("SELECT poNo, awardedTo 
								   FROM tblpo_jo  
								   WHERE prID = '" . $prID . "'")
								   or die(mysql_error($conn));
			$qryPR = $conn->query("SELECT prNo 
								   FROM tblpr 
								   WHERE prID = '" . $prID . "'") 
								   or die(mysql_error($conn));
			$qryInfo = $conn->query("SELECT bidID 
								     FROM tblbids_quotations 
								     WHERE infoID = '". $infoID . "'") 
								     or die(mysql_error($conn));

			$qryGetStatus = $conn->query("SELECT prStatus 
										  FROM tblpr 
										  WHERE prID = '" . $prID . "'")
										  or die(mysql_error($conn));

			$qryGetOldAward = $conn->query("SELECT awardedTo 
									        FROM tblpr_info 
									        WHERE infoID = '". $infoID . "' 
									        AND prID = '" . $prID . "'")
											or die(mysql_error($conn));

			$conn->query("UPDATE tblpr
						  SET abstractDate = '" . $abstractDate . "' 
						  WHERE prID = '" . $prID . "'")
						  or die(mysql_error($conn));

			if (mysqli_num_rows($qryPR)) {
				$pr = $qryPR->fetch_object();
				$prNo = $pr->prNo;
			}

			if (mysqli_num_rows($qryGetStatus)) {
				$item = $qryGetStatus->fetch_object();
				$prStatus = $item->prStatus;
			}

			if (mysqli_num_rows($qryGetOldAward)) {
				$list = $qryGetOldAward->fetch_object();
				$oldAwardedTo = $list->awardedTo;
			}
				
			$qryGetPoNo = $conn->query("SELECT poNo 
										FROM tblpo_jo  
										WHERE awardedTo = '" .  $oldAwardedTo . "' 
										AND prID = '" . $prID . "'")
										or die(mysql_error($conn));

			if (mysqli_num_rows($qryGetPoNo)) {
				$list2 = $qryGetPoNo->fetch_object();
				$poNo = $list2->poNo;
			}
			
			foreach ($data[8] as $key => $supplierID) {
				$data[3] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[3], 
							$allowed_tags)) : strip_tags($data[3], $allowed_tags);
				$data[6][$key] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[6][$key], 
								  $allowed_tags)) : strip_tags($data[6][$key], $allowed_tags);

				if (mysqli_num_rows($qryInfo)) {
					// UPDATE BIDDER ABSTRACT
					if ($prStatus <> "pending" && $prStatus && "for_canvass" && $prStatus <> "approved") {
						if ($key == 0) {
							$hasSupplier = false;
							
							while ($list = $qryPO->fetch_object()) {
								if ($list->awardedTo == $data[2]) {
									$hasSupplier = true;
								}
							}

							if (!$hasSupplier && $data[2] != "0" && $data[2] != "") {
								$countPO = mysqli_num_rows($qryPO);
								$newPONumber = $prNo . "-" . $arrPOS[$countPO];

								$conn->query("INSERT IGNORE INTO tblpo_jo (poNo, prID, awardedTo, 
										  		  			 		   placeDelivery, deliveryDate, 
										  		  			 		   deliveryTerm, paymentTerm, forApproval) 
										  	  VALUES ('" . $newPONumber . "', '" . $prID . "', '" . $data[2] . "', 
										  	  		  '" . $placeDelivery . "', '" . $deliveryDate . "', 
										  	  		  '" . $deliveryTerm . "', '" . $paymentTerm . "', '" . $forApproval . "')")
										  	  or die(mysqli_error($conn));
							}

							$conn->query("UPDATE tblpr_info 
									 	  SET awardedTo = '" . $data[2] . "', 
									 	  	  awardedRemarks = '" . $data[3] . "'
									 	  WHERE infoID = '". $infoID . "'")
										  or die(mysqli_error($conn));

                            $conn->query("DELETE FROM tblpo_jo_items 
                                          WHERE poNo = '" . $poNo . "'")
                                          or die(mysqli_error($conn));
						}
					} else {
						if ($key == 0) {
							$conn->query("UPDATE tblpr_info 
									 	  SET awardedTo = '" . $data[2] . "', 
									 	  	  awardedRemarks = '" . $data[3] . "'
									 	  WHERE infoID = '". $infoID . "'")
										  or die(mysqli_error($conn));
						}
					}

					if (mysqli_num_rows($qryInfo) == (int)$oldBidCount) {
						$conn->query("UPDATE tblbids_quotations 
								      SET bidderID = '" . $supplierID . "',
								      	  amount = '" . $data[4][$key] . "', 
								      	  lamount = '" . $data[5][$key] . "',
								      	  remarks = '" . $data[6][$key] . "',
                                          specification = '" . $data[12][$key] . "',
								      	  selection  = '" . $data[7][$key] . "' 
								      WHERE bidID = '" . $data[9][$key] . "'") 
								      or die(mysql_error($conn));
					} else {
						if ($key == 0) {
							$conn->query("DELETE FROM tblbids_quotations 
									      WHERE infoID = '" . $infoID . "'")
										  or die(mysqli_error($conn));
						}

						$conn->query("INSERT IGNORE INTO tblbids_quotations (bidderID, infoID, prID, amount, 
							  									             lamount, remarks, specification, selection)
								  	  VALUES ('" . $supplierID . "', '" . $infoID . "', '" . $prID . "',
								  	  		  '" . $data[4][$key] . "', '" . $data[5][$key] . "', 
								  	  		  '" . $data[6][$key] . "', 
                                              '" . $data[12][$key] . "',
								  	  		  '" . $data[7][$key] . "')")
								  	  or die(mysqli_error($conn));
					}
				} else {
					// INSERT BIDDER ABSTRACT
					$conn->query("INSERT IGNORE INTO tblbids_quotations (bidderID, infoID, prID, amount, 
								  							         	 lamount, remarks, specification, selection)
								  VALUES ('" . $supplierID . "', '" . $infoID . "', '" . $prID . "',
								  		  '" . $data[4][$key] . "', '" . $data[5][$key] . "', 
								  		  '" . $data[6][$key] . "', 
                                          '" . $data[12][$key] . "', 
								  		  '" . $data[7][$key] . "')")
								  or die(mysqli_error($conn));

					if ($key == 0) {
						$conn->query("UPDATE tblpr_info 
								 	  SET awardedTo = '" . $data[2] . "', 
								 	  	  awardedRemarks = '" . $data[3] . "'
								 	  WHERE infoID = '". $infoID . "'")
									  or die(mysqli_error($conn));

						if ($prStatus <> "pending" && $prStatus && "for_canvass" && $prStatus <> "approved") {
							$hasSupplier = false;
							
							while ($list = $qryPO->fetch_object()) {
								if ($list->awardedTo == $data[2]) {
									$hasSupplier = true;
									break;
								}
							}

							if (!$hasSupplier && $data[2] != "0" && $data[2] != "") {
								$countPO = mysqli_num_rows($qryPO);
								$newPONumber = $prNo . "-" . $arrPOS[$countPO];


								$conn->query("INSERT IGNORE INTO tblpo_jo (poNo, prID, awardedTo, 
										  		  			 		   placeDelivery, deliveryDate, 
										  		  			 		   deliveryTerm, paymentTerm, forApproval) 
										  	  VALUES ('" . $newPONumber . "', '" . $prID . "', '" . $data[2] . "', 
										  	  		  '" . $placeDelivery . "', '" . $deliveryDate . "', 
										  	  		  '" . $deliveryTerm . "', '" . $paymentTerm . "', '" . $forApproval . "')")
										  	  or die(mysqli_error($conn));
							}
						}
					}
				}
			}

			break;
		case 'delete-abstract':
			$conn->query("UPDATE tblpr_info 
						  SET awardedTo = 'NULL', 
						  	  awardedRemarks = '' 
						  WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("UPDATE tblpr 
						  SET prStatus = 'for_canvass' 
						  WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblbids_quotations 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblpo_jo 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblpo_jo_items 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblors 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tbliar 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tblinventory_items 
					      WHERE prID = '". $prID ."'")
						  or die(mysqli_error($conn));

			break;
		case 'save-po':
			$payee = "";
			$address = "";
			$particulars = "...";
			$amount = $data[6];

			$qryPO = $conn->query("SELECT po.awardedTo, bid.address  
								   FROM tblpo_jo po 
								   INNER JOIN tblbidders bid 
								   ON bid.bidderID = po.awardedTo 
								   WHERE po.poNo = '". $prID . "' 
								   AND po.prID = '" . $data[13] . "'") 
								   or die(mysql_error($conn));

			$qryORS = $conn->query("SELECT id 
								    FROM tblors 
								    WHERE poNo = '". $prID . "' 
								    AND prID = '" . $data[13] . "'") 
									or die(mysqli_error($conn));

			if (mysqli_num_rows($qryPO)) {
				$list1 = $qryPO->fetch_object();
				$payee = $list1->awardedTo;
				$address = $list1->address;
			}

			if (!mysqli_num_rows($qryORS)) {
				$conn->query("INSERT IGNORE INTO tblors (prID, poNo, particulars, 
							  		  			 		 payee, address, amount) 
							  VALUES ('" . $data[13] . "', '" . $prID . "', '" . $particulars . "', 
							  		  '" . $payee . "', '" . $address . "', '" . $amount . "')")
							  or die(mysqli_error($conn));
			} else {
				$conn->query("UPDATE tblors 
							  SET payee = '" . $payee . "', 
							  	  address = '" . $address . "', 
							  	  amount = '" . $amount . "'  
							  WHERE poNo = '". $prID . "'")
							  or die(mysqli_error($conn));
			}

			if (!empty($data[0]) && !empty($data[7]) && !empty($data[6]) && !empty($data[5])) {
				$forApproval = 'y';
			} else {
				$forApproval = 'n';
			}

			$conn->query("UPDATE tblpo_jo 
						  SET poDate = '" . $data[0] . "', 
						  	  placeDelivery = '" . $data[1] . "', 
						  	  deliveryDate = '" . $data[2] . "', 
						  	  deliveryTerm = '" . $data[3] . "', 
						  	  paymentTerm = '" . $data[4] . "', 
						  	  amountWords = '" . $data[5] . "', 
						  	  totalAmount = '" . $data[6] . "', 
						  	  forApproval = '" . $forApproval . "', 
						  	  signatoryApp = '" . $data[7] . "',  
						  	  signatoryDept = '" . $data[8] . "'
						  WHERE poNo = '". $prID . "'")
						  or die(mysqli_error($conn));
	
			$qryItems = $conn->query("SELECT poNo, id 
									  FROM tblpo_jo_items 
									  WHERE poNo = '" . $prID . "' 
									  ORDER BY id ASC")
									  or die(mysqli_error($conn));

			if (!mysqli_num_rows($qryItems)) {
				for ($i = 0; $i < count($data[9]); $i++) { 
					$allowed_tags = "";
					$itemDescription = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[10][$i], 
										$allowed_tags)) : strip_tags($data[10][$i], $allowed_tags);

					$conn->query("INSERT IGNORE INTO tblpo_jo_items (poNo, quantity, itemDescription, amount, 
                                                     unitIssue, prID, infoID, excluded, totalAmount) 
								  VALUES ('" . $prID ."', '" . $data[9][$i] ."', '" . $itemDescription . 
								  		 "', '" . $data[11][$i] ."', '" . $data[12][$i] . "', '" . $data[13] ."', '" . $data[14][$i] . 
								  		 "', '" . $data[15][$i] . "', '" . $data[16][$i] . "')")
								  or die(mysqli_error($conn));	
				}

				/*
				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '8'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblpo_jo 
							  SET poStatus = '" . $prStatus . "' 
							  WHERE poNo='".$prID."'");*/
			} else {
				$i = 0;

				while ($items = $qryItems->fetch_object()) {
					$allowed_tags = "";
					$itemDescription = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[10][$i], 
										$allowed_tags)) : strip_tags($data[10][$i], $allowed_tags);

					$conn->query("UPDATE tblpo_jo_items 
								  SET quantity = '" . $data[9][$i] . "', 
								  	  itemDescription = '" . $itemDescription . "', 
								  	  amount = '" . $data[11][$i] . "' ,
								  	  unitIssue = '" . $data[12][$i] . "', 
								  	  excluded = '" . $data[15][$i] . "',
                                      totalAmount = '" . $data[16][$i] . "' 
								  WHERE id = '". $items->id . "'")
								  or die(mysqli_error($conn));	

					$i++;
				}
			}

			break;
		case 'save-jo':
			$payee = "";
			$address = "";
			$particulars = "...";
			$amount = $data[3];

			$qryPO = $conn->query("SELECT po.awardedTo, bid.address  
								   FROM tblpo_jo po 
								   INNER JOIN tblbidders bid 
								   ON bid.bidderID = po.awardedTo 
								   WHERE po.poNo = '". $prID . "' 
								   AND po.prID = '" . $data[11] . "'") 
								   or die(mysql_error($conn));

			$qryORS = $conn->query("SELECT id 
								    FROM tblors 
								    WHERE poNo = '". $prID . "' 
								    AND prID = '" . $data[11] . "'") 
									or die(mysqli_error($conn));

			if (mysqli_num_rows($qryPO)) {
				$list1 = $qryPO->fetch_object();
				$payee = $list1->awardedTo;
				$address = $list1->address;
			}

			if (!mysqli_num_rows($qryORS)) {
				$conn->query("INSERT IGNORE INTO tblors (prID, poNo, particulars, 
							  		  			 		   payee, address, amount) 
							  VALUES ('" . $data[11] . "', '" . $prID . "', '" . $particulars . "', 
							  		  '" . $payee . "', '" . $address . "', '" . $amount . "')")
							  or die(mysqli_error($conn));
			} else {
				$conn->query("UPDATE tblors 
							  SET payee = '" . $payee . "', 
							  	  address = '" . $address . "', 
							  	  amount = '" . $amount . "'  
							  WHERE poNo = '". $prID . "'")
							  or die(mysqli_error($conn));
			}


			if (!empty($data[0]) && !empty($data[7]) && !empty($data[6]) && !empty($data[5])) {
				$forApproval = 'y';
			} else {
				$forApproval = 'n';
			}

			$conn->query("UPDATE tblpo_jo 
						  SET poDate = '" . $data[0] . "', 
						  	  placeDelivery = '" . $data[1] . "', 
						  	  deliveryDate = '" . $data[2] . "', 
						  	  totalAmount = '" . $data[3] . "', 
						  	  paymentTerm = '" . $data[4] . "', 
                              forApproval = '" . $forApproval . "', 
                              amountWords = '" . $data[14] . "', 
						  	  forApproval = '" . $forApproval . "', 
						  	  signatoryApp = '" . $data[5] . "', 
						  	  signatoryDept = '" . $data[6] . "', 
						  	  signatoryFunds = '" . $data[7] . "' 
						  WHERE poNo = '". $prID . "'")
						  or die(mysqli_error($conn));

			$qryItems = $conn->query("SELECT poNo, id 
									  FROM tblpo_jo_items 
									  WHERE poNo = '" . $prID . "' 
									  ORDER BY id ASC")
									  or die(mysqli_error($conn));
			
			if (!mysqli_num_rows($qryItems)) {
				for ($i = 0; $i < count($data[8]); $i++) { 
					$allowed_tags = "";
					$itemDescription = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[8][$i], 
										$allowed_tags)) : strip_tags($data[10][$i], $allowed_tags);

					$conn->query("INSERT IGNORE INTO tblpo_jo_items (poNo, itemDescription, unitIssue, quantity, amount, prID, infoID, excluded) 
								  VALUES ('" . $prID ."', '" . $itemDescription ."', 'J.O.', 
								  		  '" . $data[9][$i] ."', '". $data[10][$i] ."', '". $data[11] . "', '" . $data[12][$i] . 
								  		  "', '" . $data[13][$i] . "')")
								  or die(mysqli_error($conn));
				}

				/*
				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '8'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblpo_jo 
							  SET poStatus = '" . $prStatus . "' 
							  WHERE poNo='".$prID."'");*/
			} else {
				$i = 0;

				while ($items = $qryItems->fetch_object()) {
					$allowed_tags = "";
					$itemDescription = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[8][$i], 
										$allowed_tags)) : strip_tags($data[10][$i], $allowed_tags);
					
					$conn->query("UPDATE tblpo_jo_items 
								  	 SET itemDescription = '" . $itemDescription . "', 
								  	  	 unitIssue = 'J.O.', 
								  	  	 quantity = '" . $data[9][$i] . "', 
								  	  	 amount = '" . $data[10][$i] . "', 
								  	  	 excluded = '" . $data[13][$i] . "' 
								  WHERE id = '". $items->id . "'")
								  or die(mysqli_error($conn));	

					$i++;
				}
			}

			break;
		case 'save-ors':
			$poNo = "";

			if (count($data) == 14) {
				$allowed_tags = "";
				$data[3] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[3], 
							$allowed_tags)) : strip_tags($data[3], $allowed_tags);
				$data[4] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[4], 
							$allowed_tags)) : strip_tags($data[4], $allowed_tags);
				$data[5] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[5], 
							$allowed_tags)) : strip_tags($data[5], $allowed_tags);
				$data[11] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[11], 
							$allowed_tags)) : strip_tags($data[11], $allowed_tags);
				$data[12] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[12], 
							$allowed_tags)) : strip_tags($data[12], $allowed_tags);
				
				$conn->query("UPDATE tblors 
						  	  SET orsNo = '" . $data[0] . "', 
						  	  	  payee = '" . $data[1] . "', 
						  	  	  office = '" . $data[2] . "', 
						  	  	  address = '" . $data[3] . "', 
						  	  	  particulars = '" . $data[4] . "', 
						  	  	  uacsObjectCode = '" . $data[5] . "', 
						  	  	  signatoryReq = '" . $data[6] . "', 
						  	  	  signatoryBudget = '" . $data[7] . "', 
						  	  	  signatoryReqDate = '" . $data[8] . "', 
						  	  	  signatoryBudgetDate = '" . $data[9] . "',
						  	  	  serialNo = '" . $data[11] . "', 
						  	  	  orsDate = '" . $data[12] . "',
                                  documentType = '" . $data[13] . "' 
						  	  WHERE id = '". $prID . "'") 
						  	  or die(mysqli_error($conn));

				$qryDV = $conn->query("SELECT * 
									   FROM tbldv 
							  		   WHERE orsID = '" . $prID . "'");

				/*
				if (!mysqli_num_rows($qryDV)) {
					$conn->query("INSERT IGNORE INTO tbldv (prID, orsID, particulars)
								  VALUES ('" . $data[10] ."', '" . $prID ."', '...')")
								  or die(mysqli_error($conn));
				}

				$qryORS = $conn->query("SELECT poNo 
							            FROM tblors 
							            WHERE id = '" . $prID . "'")
							            or die(mysqli_error($conn));
				if (mysqli_num_rows($qryORS)) {
					$list = $qryORS->fetch_object();
					$poNo = $list->poNo;
				}

				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '10'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblpo_jo 
							  SET poStatus = '" . $prStatus . "' 
							  WHERE poNo='".$poNo."'")
							  or die(mysqli_error($conn));*/
			} else if (count($data) == 15) {
				/*
				$conn->query("INSERT IGNORE  INTO tblors (orsNo, payee, office, address, particulars, uacsObjectCode, amount, 
													     signatoryReq, signatoryBudget, signatoryReqDate, signatoryBudgetDate,
													     empID, sectionID)
							  VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "', '" . $data[3] . "', '" . $data[4] . "', 
							  	      '" . $data[5] . "', '" . $data[6] . "', '" . $data[7] . "', '" . $data[8] . "', '" . $data[9] . "', 
							  	      '" . $data[10] . "', '" . $data[11] . "', '" . $data[12] . "')")
							  or die(mysqli_error($conn));*/
			}

			break;
		case 'delete-ors':
			$conn->query("DELETE FROM tblors 
						  WHERE id = '" . $prID . "'")
				   		  or die(mysqli_error($conn));
			$conn->query("DELETE FROM tbldv 
						  WHERE orsID = '" . $prID . "'")
				   		  or die(mysqli_error($conn));
			break;
		case 'show-address':
			$address = "";
			$qrySuppliers = $conn->query("SELECT address 
									   	  FROM tblbidders 
									   	  WHERE bidderID='" . $data . "'") 
								   		  or die(mysqli_error($conn));

			if (mysqli_num_rows($qrySuppliers)) {
				$data = $qrySuppliers->fetch_object();
				$address = $data->address;
			}

			echo $address;
			break;
		case 'to-iar':
			$qryIAR = $conn->query("SELECT id 
								    FROM tbliar 
								    WHERE iarNo = '". $data[2] . "' 
								    AND prID = '" . $data[0] . "'") 
									or die(mysqli_error($conn));

			if (!mysqli_num_rows($qryIAR)) {
				$conn->query("INSERT IGNORE INTO tbliar (prID, orsID, iarNo) 
							  VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "')")
							  or die(mysqli_error($conn));
			}

			// Get and set PR Status
			$qryStatus = $conn->query("SELECT statusName 
									   FROM tblpr_status 
									   WHERE id = '9'") 
									   or die(mysqli_error($conn));
			$_prStatus = $qryStatus->fetch_object();
			$prStatus = $_prStatus->statusName;

			$conn->query("UPDATE tblpo_jo 
						  SET poStatus = '" . $prStatus . "' 
						  WHERE poNo='".$prID."'")
						  or die(mysqli_error($conn));
			break;
		case 'update-iar':
			$conn->query("UPDATE tbliar 
						  SET iarDate = '" . $data[0] . "', 
						  	  invoiceNo = '" . $data[1] . "', 
						  	  invoiceDate = '" . $data[2] . "', 
						  	  inspectedBy = '" . $data[3] . "', 
						  	  signatorySupply = '" . $data[4] . "' 
						  WHERE orsID = '". $prID . "'") 
						  or die(mysqli_error($conn));
			break;
		case 'finalize-iar':
			$inventoryClassNo = strtoupper($data[2]) . "-" . $_REQUEST['poNo'] . "-" . $data[7];
			$qryIAR = $conn->query("SELECT poItemID 
						            FROM tblinventory_items 
						            WHERE poItemID = '" . $data[0] . "' 
						            AND prID = '" . $prID . "'")
						            or die(mysqli_error($conn));
						            
			if (mysqli_num_rows($qryIAR)) {
				$conn->query("UPDATE tblinventory_items 
						  	  SET inventoryClass = '" . $data[2] . "', 
						  	  	  propertyNo = '" . $data[3] . "', 
						  	  	  itemClassification = '" . $data[5] . "', 
						  	  	  quantity = '" . $data[6] . "', 
						  	  	  inventoryClassNo = '" . $inventoryClassNo . "', 
						  	  	  groupNo = '" . $data[7] . "' 
						  	  WHERE poItemID = '". $data[0] . " ' 
						  	  AND prID = '" . $prID . "'") 
						  	  or die(mysqli_error($conn));
			} else {
				//$inventoryClassNo = strtoupper($data[2]) . "-" . $_REQUEST['poNo'] . "-" . $data[4];
				//$inventoryClassNo = strtoupper($data[2]) . "-" . $_REQUEST['poNo'];
				$date = date("m/d/Y");

				$conn->query("INSERT IGNORE INTO tblinventory_items (prID, poItemID, infoID, inventoryClass, 
																	 propertyNo, inventoryClassNo, createdAt, itemClassification, 
																	 quantity, groupNo) 
							  VALUES ('" . $prID . "', '" . $data[0] . "', '" . $data[1] . 
							  		  "', '" . $data[2] . "', '" . $data[3] . "', '" . $inventoryClassNo . 
							  		  "', '" . $date ."', '" . $data[5] . "', '" . $data[6] . 
							  		  "', '" . $data[7] . "')")
							  or die(mysqli_error($conn));

				/*
				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '12'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				if ($_REQUEST['poNo']) {
					$conn->query("UPDATE tblpo_jo 
							  	  SET poStatus = '" . $prStatus . "' 
							  	  WHERE poNo='". $_REQUEST['poNo'] ."'")
							  	  or die(mysqli_error($conn));
				}*/
			}

			break;
		case 'to-dv':
			$particulars = "...";
			$qryDV = $conn->query("SELECT id 
								    FROM tbldv 
								    WHERE orsID = '". $data[1] . "' 
								    AND prID = '" . $data[0] . "'") 
									or die(mysqli_error($conn));

			if (!mysqli_num_rows($qryDV)) {
				$conn->query("INSERT IGNORE INTO tbldv (prID, orsID, particulars) 
							  VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $particulars . "')")
							  or die(mysqli_error($conn));
			}

			// Get and set PR Status
			$qryStatus = $conn->query("SELECT statusName 
									   FROM tblpr_status 
									   WHERE id = '10'") 
									   or die(mysqli_error($conn));
			$_prStatus = $qryStatus->fetch_object();
			$prStatus = $_prStatus->statusName;

			$conn->query("UPDATE tblpo_jo 
						  SET poStatus = '" . $prStatus . "' 
						  WHERE poNo='".$prID."'")
						  or die(mysqli_error($conn));

			break;
		case 'save-dv':
			$data[1] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[1], 
							$allowed_tags)) : strip_tags($data[1], $allowed_tags);
			
			$data[0] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[0], 
							$allowed_tags)) : strip_tags($data[0], $allowed_tags);
			$conn->query("UPDATE tbldv 
						  SET particulars = '" . $data[0] . "', 
						  	  paymentMode = '" . $data[1] . "', 
						  	  dvNo = '" . $data[4] . "', 
						  	  dvDate = '" . $data[5] . "' 
						  WHERE orsID = '". $prID . "'") 
						  or die(mysqli_error($conn));

			/*
			$qryIAR = $conn->query("SELECT prID 
									FROM tbliar 
									WHERE orsID = '" . $prID . "'") 
								   	or die(mysqli_error($conn));

			if (!mysqli_num_rows($qryIAR)) {
				$iarNo = "IAR-" . $data[3];

				$conn->query("INSERT IGNORE INTO tbliar (prID, orsID, iarNo) 
							  VALUES ('" . $data[2] . "', '" . $prID . "', '" . $iarNo . "')")
							  or die(mysqli_error($conn));

				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '11'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblpo_jo 
							  SET poStatus = '" . $prStatus . "' 
							  WHERE poNo='".$data[3]."'")
							  or die(mysqli_error($conn));
			}

			$qryORS = $conn->query("SELECT poNo 
						            FROM tblors 
						            WHERE id = '" . $prID . "'")
						            or die(mysqli_error($conn));
			if (mysqli_num_rows($qryORS)) {
				$list = $qryORS->fetch_object();
				$poNo = $list->poNo;
			}*/

			break;
		case 'to-payment':
			// Get and set PR Status
			$qryStatus = $conn->query("SELECT statusName 
									   FROM tblpr_status 
									   WHERE id = '11'") 
									   or die(mysqli_error($conn));
			$_prStatus = $qryStatus->fetch_object();
			$prStatus = $_prStatus->statusName;

			$conn->query("UPDATE tblpo_jo 
						  SET poStatus = '" . $prStatus . "' 
						  WHERE poNo='".$data[1]."'")
						  or die(mysqli_error($conn));

			break;
		case 'save-inventory':
			$allowed_tags = "";
			$inventoryClassNo = $_REQUEST['inventoryClassNo'];
			$classification = $_REQUEST['classification'];
            $printType = $_REQUEST['savingType'];
            $multiple = $_REQUEST['multiple'];
			$date = date("m/d/Y");
			$quantity = 0;
			$empID = 0;
			$oldEmpID = 0;
			$available = 0;
            $propertyNo = "";
            $issueRemarks = "";
			$approvedBy = 0; 
			$issuedBy = 0; 
			$recievedBy = 0;
			$serialNo = "";

            $qryPO = $conn->query("SELECT po.quantity 
								   FROM tblpo_jo_items po 
								   INNER JOIN tblinventory_items inv 
								   ON inv.poItemID = po.id 
								   WHERE inv.id = '" . $prID . "'") 
								   or die(mysql_error($conn));
			$qryItemIssue = $conn->query("SELECT quantity 
										  FROM tblitem_issue 
										  WHERE inventoryID = '" . $prID . "'") 
										  or die(mysql_error($conn));

			if ($classification == "ris") {
				$empID = $data[6];
				$oldEmpID = $data[7];
				$quantity = $data[2];
				$issueRemarks = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[3], 
								$allowed_tags)) : strip_tags($data[3], $allowed_tags);;
				$approvedBy = $data[4]; 
				$issuedBy = $data[5]; 
				$recievedBy = $data[6];
				$inventoryClassNo = $data[0];

				$conn->query("UPDATE tblinventory_items 
							  SET inventoryClassNo = '" . $data[0] . "', 
							  	  stockAvailable = '" . $data[1] . "' 
							  WHERE id = '". $prID . " '")
							  or die(mysql_error($conn));
			} else if ($classification == "par") {
				$empID = $data[4];
				$oldEmpID = $data[6];
				$quantity = $data[5];
				$date = $data[2];
                $propertyNo = $data[1];
				$acquiredDate = $data[2]; 
				$recievedBy = $data[4];
				$issuedBy = $data[3];
				$inventoryClassNo = $data[0];
				$serialNo = $data[8];

                $conn->query("UPDATE tblinventory_items 
							  SET inventoryClassNo = '" . $data[0] . "', 
							  	  propertyNo = '" . $data[1] . "' 
							  WHERE id = '". $prID . " '")
							  or die(mysql_error($conn));
			} else if ($classification == "ics") {
				$empID = $data[5];
				$oldEmpID = $data[7];
				$quantity = $data[6];
				$date = $data[2];
                $propertyNo = $data[1];
				$acquiredDate = $data[2];
				$recievedBy = $data[5];  
				$issuedBy = $data[4]; 
				$inventoryClassNo = $data[0];
                $serialNo = $data[9];

                $conn->query("UPDATE tblinventory_items 
							  SET inventoryClassNo = '" . $data[0] . "',  
							  	  propertyNo = '" . $data[1] . "', 
							  	  estimatedUsefulLife = '" . $data[3] . "'  
							  WHERE id = '". $prID . " '")
							  or die(mysql_error($conn));
			}

			if (mysqli_num_rows($qryPO)) {
				$list1 = $qryPO->fetch_object();
				$available = $list1->quantity;
			}	

			if (mysqli_num_rows($qryItemIssue)) {
				while ($list2 = $qryItemIssue->fetch_object()) {
					$available -= $list2->quantity;
				}
			}
			
			//if ($classification == "par" || $classification == "ics") {
            if ($printType == "old") {
                $qryIssue = $conn->query("SELECT quantity 
                                          FROM tblitem_issue 
                                          WHERE inventoryID = '" . $prID . "' 
                                          AND empID = '" . $oldEmpID . "'") 
                                          or die(mysql_error($conn));
            } else if ($printType == "new") {
                $qryIssue = $conn->query("SELECT quantity 
                                          FROM tblitem_issue 
                                          WHERE inventoryID = '" . $prID . "' 
                                          AND empID = '" . $empID . "'") 
                                          or die(mysql_error($conn));
            }
			
			if (mysqli_num_rows($qryIssue)) {
				$list3 = $qryIssue->fetch_object();

				if ($quantity <= ($available + $list3->quantity)) {
					$conn->query("UPDATE tblitem_issue 
								  SET quantity = '" . $quantity . "', 
								  	  issueDate = '" . $date . "',
                                      serialNo = '" . $serialNo . "',
                                      approvedBy = '" . $approvedBy . "',
								  	  issuedBy = '" . $issuedBy . "',
								  	  issueRemarks = '" . $issueRemarks . "',
								  	  empID = '" . $empID . "' 
								  WHERE inventoryID = '". $prID . "' 
								  AND empID = '" . $oldEmpID . "'")
								  or die(mysql_error($conn));
					echo "1";
				} else {
					echo "0";
				}
			} else {
				if ($available > 0) {
					if ($quantity <= $available) {
						$conn->query("INSERT IGNORE INTO tblitem_issue (inventoryID, empID, quantity, 
																		issueDate, approvedBy, 
																		issuedBy, issueRemarks, serialNo) 
									  VALUES ('" . $prID . "', '" . $empID . "', '" . $quantity . 
									  		  "', '" . $date . "', '" . $approvedBy . 
									  		  "', '" . $issuedBy . "', '" . $issueRemarks .  
									  		  "', '" . $serialNo . "')")
									  or die(mysqli_error($conn));

						echo "1";
					} else {
						echo "0";
					}
				} else {
					echo "0";
				}
			}
			//}

			break;
		case 'issue-item':
			$qry = $conn->query("SELECT inventoryClassNo 
								 FROM tblinventory_items 
								 WHERE propertyNo IS NOT NULL 
								 AND inventoryClassNo IS NOT NULL 
								 AND recievedBy <> '0' 
								 AND issuedBy <> '0' 
								 AND inventoryClassNo = '" . $prID . "'") 
								 or die(mysql_error($conn));

			if (mysqli_num_rows($qry)) {
				// Get and set PR Status
				$qryStatus = $conn->query("SELECT statusName 
										   FROM tblpr_status 
										   WHERE id = '13'") 
										   or die(mysqli_error($conn));
				$_prStatus = $qryStatus->fetch_object();
				$prStatus = $_prStatus->statusName;

				$conn->query("UPDATE tblinventory_items 
						  	  SET itemStatus = '" . $prStatus . "'
						  	  WHERE inventoryClassNo='". $prID ."'")
						  	  or die(mysqli_error($conn));
				echo "1";
			} else {
				echo "0";
			}

			break;
		case 'truncate-table':
			$tableName = "";

			if (isset($_REQUEST["table"])) {
				$tableName = $_REQUEST["table"];
			}

			$conn->query("TRUNCATE TABLE " . $tableName) 
						  or die(mysqli_error($conn));

			break;
		case 'save-report':
			 $reportType = "";

			if (isset($_REQUEST["what"])) {
				$reportType = $_REQUEST["what"];
			}

			if ($reportType == "pmf") {
				$allowed_tags = "";

				for ($i = 4; $i <= 14; $i++) { 
					$data[$i] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[$i], 
									$allowed_tags)) : strip_tags($data[$i], $allowed_tags);
				}

				$conn->query("INSERT IGNORE INTO tbltemp_procurement_monitoring 
									  (moYear, 
									   prNo, 
									   prDate, 
									   abstractApprovalDate, 
									   poApprovalDate, 
									   supplier, 
									   particulars,
									   poRecievedDate,
									   deliveredDate,
									   invoiceNo,
									   inspectedBy,
									   requiredDays,
									   actualDays,
									   difference,
									   remarks) 
						       VALUES ('" . $data[0] . "', 
						       		   '" . $data[1] . "', 
						       		   '" . $data[2] . "', 
						       		   '" . $data[3] . "', 
						       		   '" . $data[4] . "', 
						       		   '" . $data[5] . "', 
						       		   '" . $data[6] . "', 
						       		   '" . $data[7] . "', 
						       		   '" . $data[8] . "', 
						       		   '" . $data[9] . "', 
						       		   '" . $data[10] . "',
						       		   '" . $data[11] . "',
						       		   '" . $data[12] . "',
						       		   '" . $data[13] . "', 
						       		   '" . $data[14] . "')")
						       or die(mysqli_error($conn));
			} else if ($reportType == "ios" || $reportType == "pcppe") {
				$conn->query("INSERT IGNORE INTO tbltemp_inventory_supply 
									  (propertyNo, 
									   description, 
									   inventoryClassNo, 
									   unitIssue, 
									   unitValue, 
									   quantity, 
									   onHandCount,
									   quantityShortage,
									   valueShortage,
									   remarks) 
						       VALUES ('" . $data[0] . "', 
						       		   '" . $data[1] . "', 
						       		   '" . $data[2] . "', 
						       		   '" . $data[3] . "', 
						       		   '" . $data[4] . "', 
						       		   '" . $data[5] . "', 
						       		   '" . $data[6] . "', 
						       		   '" . $data[7] . "', 
						       		   '" . $data[8] . "', 
						       		   '" . $data[9] . "')")
						       or die(mysqli_error($conn));
			}
			
			break;
			case 'update-supplier':
				$allowed_tags = "";
				$qrySupplier = $conn->query("SELECT bidderID 
											 FROM tblbidders 
											 WHERE bidderID = '" . $prID . "'") 
											 or die(mysql_error($conn));

				for ($i = 0; $i < count($data); $i++) { 
					$data[$i] = (!get_magic_quotes_gpc()) ? addslashes(strip_tags($data[$i], 
							$allowed_tags)) : strip_tags($data[$i], $allowed_tags);
				}

				if (mysqli_num_rows($qrySupplier)) {
					$conn->query("UPDATE tblbidders 
							  	  SET fileDate = '" . $data[0] . "', 
							  	  	  company_name = '" . $data[1] . "', 
							  	  	  classID = '" . $data[2] . "', 
							  	  	  address = '" . $data[3] . "', 
								  	  emailAddress = '" . $data[4] . "', 
								  	  urlAddress = '" . $data[5] . "', 
								  	  contact_person = '" . $data[6] . "',
								  	  contact_no = '" . $data[7] . "', 
								  	  faxNo = '" . $data[8] . "', 
								  	  mobileNumber = '" . $data[9] . "', 
								  	  establishedDate = '" . $data[10] . "',
								  	  vatNo = '" . $data[11] . "', 
								  	  nameBank = '" . $data[12] . "', 
								  	  accountName = '" . $data[13] . "', 
								  	  accountNo = '" . $data[14] . "', 
								  	  natureBusiness = '" . $data[15] . "', 
								  	  natureBusinessOthers = '" . $data[16] . "', 
								  	  deliveryVehicleNo = '" . $data[17] . "', 
								  	  productLines = '" . $data[18] . "',
								  	  creditAccomodation = '" . $data[19] . "', 
								  	  attachement = '" . $data[20] . "', 
								  	  attachmentOthers = '" . $data[21] . "' 
							  	  WHERE bidderID = '" . $prID . "'")
								  or die(mysql_error($conn));
				} else {
					$conn->query("INSERT IGNORE INTO tblbidders 
								  		  (fileDate, company_name, classID, address, 
								  		   emailAddress, urlAddress, contact_person,
								  		   contact_no, faxNo, mobileNumber, establishedDate,
								  		   vatNo, nameBank, accountName, accountNo, natureBusiness,
								  		   natureBusinessOthers, deliveryVehicleNo, productLines,
								  		   creditAccomodation, attachement, attachmentOthers) 
						       	  VALUES ('" . $data[0] . "', 
						       	  		  '" . $data[1] . "', 
						       	  		  '" . $data[2] . "', 
						       	  		  '" . $data[3] . "', 
						       	  		  '" . $data[4] . "', 
						       	  		  '" . $data[5] . "', 
						       	  		  '" . $data[6] . "', 
						       	  		  '" . $data[7] . "', 
						       	  		  '" . $data[8] . "', 
						       	  		  '" . $data[9] . "', 
						       	  		  '" . $data[10] . "', 
						       	  		  '" . $data[11] . "', 
						       	  		  '" . $data[12] . "', 
						       	  		  '" . $data[13] . "', 
						       	  		  '" . $data[14] . "', 
						       	  		  '" . $data[15] . "', 
						       	  		  '" . $data[16] . "', 
						       	  		  '" . $data[17] . "', 
						       	  		  '" . $data[18] . "', 
						       	  		  '" . $data[19] . "', 
						       	  		  '" . $data[20] . "', 
						       	  		  '" . $data[21] . "')")
						       	  or die(mysqli_error($conn));
				}

			break;
		default:
			break;
	}

	#====================================================
?>