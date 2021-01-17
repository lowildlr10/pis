<?php

session_start();
include_once("../config.php");
require('pdf_generator.php');
require('../dbcon.php');
include 'preview/pr_purchase_request.php';
include 'preview/pr_canvass.php';
include 'preview/pr_abstract.php';
include 'preview/pr_purchase_order.php';
include 'preview/pr_job_order.php';
include 'preview/pr_obligation_request.php';
include 'preview/pr_budget_utilization_request.php';
include 'preview/pr_inspection_acceptance.php';
include 'preview/pr_disbursement_voucher.php';
include 'preview/pr_property_acknowledgement_receipt.php';
include 'preview/pr_requisition_issue_slip.php';
include 'preview/pr_inventory_custodian_slip.php';
include 'preview/pr_property_label.php';
//include 'preview/pr_procurement_monitoring_form.php';
//include 'preview/pr_inventory_supply.php';
//include 'preview/pr_physical_count_property.php';

$togglePreview = false;

if (isset($_SESSION['uU_Log']) || !isset($_SESSION['emp_Log'])) {
	$togglePreview = true;
} else if (!isset($_SESSION['uU_Log']) || isset($_SESSION['emp_Log'])) {
	$togglePreview = true;
} else {
	$togglePreview = false;
}

if ($togglePreview && (isset($_REQUEST['print']) || 
	isset($_REQUEST['startDate']) || isset($_REQUEST['endDate']) )) {
	$increaseFontSize = 0;
	$paperSize = "1";
	$pageHeight = 297;
	$pageWidth = 210;

	if (isset($_REQUEST['print'])) {
		$pid = $_REQUEST['print'];
	}

	if (isset($_REQUEST['startDate'])) {
		$startDate = $_REQUEST['startDate'];
	}

	if (isset($_REQUEST['endDate'])) {
		$endDate = $_REQUEST['endDate'];
	}

	if (isset($_REQUEST['what'])) {
		$document = $_REQUEST['what'];
	}

	if (isset($_REQUEST['font-scale'])) {
		$increaseFontSize = $_REQUEST['font-scale'] / 100;
	}

	if (isset($_REQUEST['paper-size'])) {
		$paperSize = $_REQUEST['paper-size'];

		switch ($paperSize) {
			case '1':
				$pageHeight = 297;
				$pageWidth = 210;
				break;
			case '2':
				$pageHeight = 279;
				$pageWidth = 216;
				break;
			case '3':
				$pageHeight = 330;
				$pageWidth = 216;
				break;
			default:
				
				break;
		}
	}

	switch($document) {
		case "pr": // Purchase Request Document
			generatePR($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "canvass": // Request for Quotation Document
			generateCanvass($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "abstract": // Abstract of Bids and Quotation Document
			$chairman = $_REQUEST["inp-chairman"];
			$viceChairman = $_REQUEST["inp-vice"];
			$member1 = $_REQUEST["inp-member1"];
			$member2 = $_REQUEST["inp-member2"];
			$member3 = $_REQUEST["inp-member3"];
			$endUser = $_REQUEST["inp-enduser"];

			generateAbstract($pid, $conn, $chairman, $viceChairman,
							 $member1, $member2, $member3, $endUser, $dir, 
							 $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "po": // Purchase Order Document
			generatePO($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "jo": // Job Order Document
			generateJO($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "ors": // Obligation Request and Status Document
			generateORS($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
        case "burs": // Obligation Request and Status Document
            generateBURS($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
            break;
        case "iar": // Inspection and Acceptance Report Document
            generateIAR($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
            break;
		case "dv": // Disbursement Voucher Document
			generateDV($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "par": // Requisition and Issue Slip Document
			generatePAR($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "ris": // Requisition and Issue Slip Document
			generateRIS($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
		case "ics": // Requisition and Issue Slip Document
			generateICS($pid, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
			break;
        case "label":
            generatePropertyLabel($pid, $conn, $dir, $increaseFontSize);
            break;
		case "pmf":
			//generatePMF($startDate, $endDate, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
            echo "This module is under re-development.";
			break;
		case "ios":
			//generateIOS($startDate, $endDate, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
            echo "This module is under re-development.";
			break;
		case "pcppe":
			//generatePCPPE($startDate, $endDate, $conn, $dir, $increaseFontSize, $pageHeight, $pageWidth);
            echo "This module is under re-development.";
			break;
		default:
			exit();
			break;
	}
} else {
	header("Location:  ../index.php");
}

?>