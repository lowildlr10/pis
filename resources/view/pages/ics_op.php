<?php
	
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$inventoryID = "";
$poNo = "";
$prID = "";
$empID = "";
$inventoryClassNo = "";
$multiple = "";
$type = "";

$parNo = "";
$supplier = "";
$issuedBy = "";
$recievedBy = "";
$issueDate = "";
$serialNo = "";

$itemNo = 0;

if ($_POST["inventoryID"]) {
	$inventoryID = $_POST["inventoryID"];
	unset($_POST["inventoryID"]);
}

if ($_POST["poNo"]) {
	$poNo = $_POST["poNo"];
	unset($_POST["poNo"]);
}

if ($_POST["prID"]) {
	$prID = $_POST["prID"];
	unset($_POST["prID"]);
}

if ($_POST["empID"]) {
    $empID = $_POST["empID"];
    unset($_POST["empID"]);
}

if ($_POST["inventoryClassNo"]) {
    $inventoryClassNo = $_POST["inventoryClassNo"];
    $icsNo = $inventoryClassNo;
    unset($_POST["inventoryClassNo"]);
}

if ($_POST["multiple"]) {
    $multiple = $_POST["multiple"];
    unset($_POST["multiple"]);
}

if ($_POST["type"]) {
    $type = $_POST["type"];
    unset($_POST["type"]);
}

$qryPO = $conn->query("SELECT po.poDate, bidder.company_name 
                       FROM tblpo_jo AS po  
                       INNER JOIN tblbidders AS bidder 
                       ON bidder.bidderID = po.awardedTo 
                       WHERE po.poNo = '" . $poNo . "'") 
                       or die(mysql_error($conn));
$qryUnitIssue = $conn->query("SELECT unitName 
                              FROM tblunit_issue 
                              ORDER BY unitName ASC") 
                              or die(mysql_error($conn));

if (mysqli_num_rows($qryPO)) {
    $data = $qryPO->fetch_object();
    $date = $data->poDate;
    $supplier = $data->company_name;
}

if ($type == "old") {
    $qryInventoryItems = $conn->query("SELECT DISTINCT inv.propertyNo, issue.issueDate, issue.issuedBy, 
                                                       inv.estimatedUsefulLife, issue.quantity, 
                                                       inv.inventoryClassNo, item.unitIssue, 
                                                       item.itemDescription, item.amount, inv.id, issue.serialNo 
                                       FROM tblinventory_items AS inv 
                                       INNER JOIN tblitem_issue AS issue 
                                       ON issue.inventoryID = inv.id 
                                       INNER JOIN tblpo_jo_items AS item 
                                       ON item.id = inv.poItemID 
                                       WHERE inv.inventoryClassNo = '" . $inventoryClassNo . "' 
                                       AND issue.empID = '" . $empID . "' 
                                       AND item.poNo = '" . $poNo . "'") 
                                       or die(mysql_error($conn));
} else if ($type == "new") {
    if ($multiple == "n") {
        $qryInventoryItems = $conn->query("SELECT inv.propertyNo, po.unitIssue, po.itemDescription,  
                                                  inv.id, po.amount, inv.estimatedUsefulLife 
                                           FROM tblinventory_items inv 
                                           INNER JOIN tblpo_jo_items po
                                           ON inv.poItemID = po.id 
                                           WHERE inv.id = '" . $inventoryID . "' 
                                           ORDER BY inv.poItemID ASC")
                                           or die(mysql_error($conn));
    } else if ($multiple == "y") {
        $qryInventoryItems = $conn->query("SELECT inv.propertyNo, po.unitIssue, po.itemDescription,  
                                                  inv.id, po.amount, inv.estimatedUsefulLife 
                                           FROM tblinventory_items inv 
                                           INNER JOIN tblpo_jo_items po
                                           ON inv.poItemID = po.id 
                                           WHERE inv.inventoryClassNo = '" . $inventoryClassNo . "' 
                                           ORDER BY inv.poItemID ASC")
                                           or die(mysql_error($conn));
    }
}

?>
	
<table id="tblStyle" class="table">
    <tr>
        <th colspan="9">INVENTORY CUSTODIAN SLIP</th>
    </tr>
    <tr>
        <td colspan="9">
            <div class="row">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-1 col-xs-1">
                        <label for="txtRisNo">
                            <strong>ICS No: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input class="form-control font-color-1" name="txtIcsNo" 
                               type="text" id="txticsNo" value="<?php echo $icsNo ?>">
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <table id="tbl-items" class="table table-bordered table-hover">
                <tr>
                    <th width="5%" style="text-align: center;""> Quantity </td>
                    <th width="7%" style="text-align: center;"> Unit </td>
                    <th width="8%" style="text-align: center;"> Unit Cost </td>
                    <th width="8%" style="text-align: center;"> Total Cost </td>
                    <th width="27%" style="text-align: center;"> Description </td>
                    <th width="15%" style="text-align: center;"> Date Acquired </td>
                    <th width="13%" style="text-align: center;"> Inventory Item Number </td>
                    <th width="15%" style="text-align: center;"> Estimated Useful Life </td>
                    <th width="2%" style="text-align: center;">  </td>
                </tr>

                <?php

                if (mysqli_num_rows($qryInventoryItems)) {
                    $countItem = 0;

                	while ($data = $qryInventoryItems->fetch_object()) {
                        $quantity = 0;

                        if ($type == "old") {
                            $quantity = $data->quantity;
                            $recievedBy = $empID;
                            $issuedBy = $data->issuedBy;
                            $issueDate = $data->issueDate;
                            $serialNo = $data->serialNo;
                        }

                		echo '<tr id="row_0" class="row-'. ++$countItem . '">';
                		echo '<td>
                                <input class="input-id" type="hidden" value="' . $data->id . '">
                                <input class="input-quantity form-control required" type="number" 
                                       value="' . $quantity . '" min="0"></td>';
                		echo '<td>' . $data->unitIssue . '</td>';
                        echo '<td>
                                <input class="input-cost form-control required" type="text" 
                                       value="' . number_format($data->amount, 2) . '" disabled="disabled"></td>';
                        echo '<td>
                                <input class="input-total form-control required" type="text" 
                                       value="' .  number_format($data->amount * $quantity, 2) . '" disabled="disabled"></td>';

                        if ($type == "old") {
                		    echo '<td style="text-align: left;"> &raquo; ' . $data->itemDescription . '...<br><br>' . 
                                      ' &raquo; <strong>
                                           <label>S/N: </label></strong>
                                           <input type="text" value="' . $serialNo . '"
                                                  class="form-control input-serial" placeholder="Serial number..."><br>' .
                                      ' &raquo; <span style="text-align: left;">
                                                <a onclick="$(this).printLabelDialog(\'' . $data->id . '\')"
                                                      class="btn btn-info btn-sm">
                                              <span class="glyphicon glyphicon-barcode"></span>
                                              Print Property Label
                                       </a></span>'.
                                  '</td>';
                        } else if ($type == "new") {
                            echo '<td style="text-align: left;"> &raquo; ' . $data->itemDescription . '...<br><br>' . 
                                 ' &raquo; <strong>
                                      <label>S/N: </label></strong>
                                      <input type="text" value="' . $serialNo . '"
                                             class="form-control input-serial" placeholder="Serial number...">' .
                                 '</td>';
                        }

                		echo '<td>' . 
                			 '<div class="form-group">
				                <div class="input-group date divDate">
				                    <input type="text" class="input-date form-control required" 
				                    	   value="' . $issueDate . '">
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
				             </div>' .
                		 	 '</td>';
                		echo '<td>
                				<input class="input-property-no form-control" type="text" 
                					   value="' . $data->propertyNo . '"">
                			  </td>';
                		echo '<td>
                				<input class="input-life form-control" type="text" 
                					   value="' . $data->estimatedUsefulLife . '"">
                			  </td>';
                        echo '<td style="text-align:center;">
                                  <a href="javascript: $(this).deleteItem(\'row-' . $countItem. '\')" title="Delete Item">
                                      <img class="img-button" src="../../assets/images/delete.png" />
                                  </a>
                              </td>';
                		echo '</tr>';
                	}
                } else {
                    echo '<tr id="row_0" class="row-0">';
                    echo '<td>
                            <input class="input-id" type="hidden" value="">
                            <input class="input-quantity form-control required" type="number" 
                                   value="0" min="0"></td>';
                    echo '<td><select class="sel-unit form-control required">';

                    if (mysqli_num_rows($qryUnitIssue)) {
                        while ($data = $qryUnitIssue->fetch_object()) {
                            echo '<option value="' . $data->unitName . '">' . $data->unitName . '</option>';
                        }
                    }

                    echo '</select></td>';
                    echo '<td><input class="input-unit-cost form-control required" type="number"></td>';
                    echo '<td><input class="input-total-cost form-control required" type="number"></td>';
                    echo '<td style="text-align: left;"> 
                              <textarea class="txt-description form-control required" placeholder="Item description..." rows="4"></textarea>
                          </td>';
                    echo '<td>' . 
                         '<div class="form-group">
                            <div class="divDate input-group date">
                                <input type="text" class="input-date form-control required" name="" 
                                       value="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                         </div>' .
                         '</td>';
                    echo '<td>
                            <input class="input-property-no form-control" type="text">
                          </td>';
                    echo '<td><input class="input-life form-control required" type="text"></td>';
                    echo '<td style="text-align:center;">
                              <a href="javascript: $(this).deleteItem(\'row-0\')" title="Delete Item">
                                  <img class="img-button" src="../../assets/images/delete.png" />
                              </a>
                          </td>';
                    echo '</tr>';
                    echo '<tr>
                              <td colspan="9" align="center">
                                  <button id="btn-add-item" class="btn btn-default btn-block"
                                     style="padding: 16px; border: 4px #4577b4 dashed;color: #4577b4;background-color: #fff;font-weight: bold;">
                                      <span class="glyphicon glyphicon-plus"></span>
                                      Add Item
                                  </button>
                              </td>
                          </tr>';
                }

                ?>
            </table>
        </td>
    </tr>
    <tr>
    	<td colspan="9">
    		<div class="row">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-1 col-xs-1">
                        <label>
                            <strong>PO No: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input type="text" class="form-control" disabled="disabled" 
                        	   value="<?php echo $poNo ?>">
                    </div>

                    <div class="col-md-1 col-xs-1">
                        <label>
                            <strong>Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <div class="form-group">
                           <div class="divDate input-group date">
                               <input type="text" class="input-date-2 form-control" 
                                      value="<?php echo $date ?>">
                               <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                               </span>
                           </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-1 col-xs-1">
                        <label>
                            <strong>Supplier: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input type="text" class="form-control" disabled="disabled" 
                        	   value="<?php echo $supplier ?>">
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
    	</td>
    </tr>
    <tr>
    	<td colspan="9">
            <div class="row">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-1 col-xs-1">
                        <label for="txtOffice">
                            <strong>Recieved From: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <select id="sel-issued-by" class="form-control required">
                            <option value=""> -- SELECT ISSUED BY -- </option>
                            <?php
                                $qrySignatory = $conn->query("SELECT signatoryID, name, position 
                                                              FROM tblsignatories 
                                                              ORDER BY name ASC")
                                                              or die(mysql_error($conn));

                                if (mysqli_num_rows($qrySignatory)) {
                                    while ($data = $qrySignatory->fetch_object()) {
                                        echo '<option value="' . $data->signatoryID . '"';
                                        echo $data->signatoryID == 53 ? ' selected="selected"':'';
                                        echo $data->signatoryID == $issuedBy ? ' selected="selected"':'';
                                        echo '> ' . $data->name . ' [' . $data->position . '] </option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-1 col-xs-1">
                        <label for="txtRecieveBy">
                            <strong>Recieved By: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <select id="sel-recieved-by" class="form-control required">
                            <option value=""> -- SELECT RECIEVED BY -- </option>
                            <?php
                                $qrySignatory = $conn->query("SELECT empID, position, 
                                                                     concat(firstname, ' ', left(middlename,1) , ' ', lastname) name 
                                                              FROM tblemp_accounts 
                                                              ORDER BY name ASC")
                                                              or die(mysql_error($conn));

                                if (mysqli_num_rows($qrySignatory)) {
                                    while ($data = $qrySignatory->fetch_object()) {
                                        echo '<option value="' . $data->empID . '"';
                                        echo $data->empID == $recievedBy ? ' selected="selected"':'';
                                        echo '> ' . strtoupper($data->name) . ' [' . 
                                                    strtoupper($data->position) . 
                                             '] </option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;">
                    <input name="txtItemCount" type="hidden" id="txtItemCount" 
                           value="<?= $itemNo ?>">
                </div>
            </div>
        </td>
    </tr>
</table>

