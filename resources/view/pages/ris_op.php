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

$risNo = "";
$division = "";
$office = "";
$approvedBy = "";
$issuedBy = "";
$recievedBy = "";

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
    $risNo = $inventoryClassNo;
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

$qryPO = $conn->query("SELECT po.poDate, bidder.company_name, 
                              ors.office, pr.requestBy, pr.purpose,
                              sec.section 
                       FROM tblpo_jo AS po  
                       INNER JOIN tblbidders AS bidder 
                       ON bidder.bidderID = po.awardedTo 
                       INNER JOIN tblors AS ors 
                       ON ors.prID = po.prID 
                       INNER JOIN tblpr AS pr 
                       ON pr.prID = po.prID 
                       INNER JOIN tblemp_accounts emp 
                       ON emp.empID = pr.requestBy 
                       INNER JOIN tblsections sec 
                       ON sec.sectionID = emp.sectionID 
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
    $division = $data->section;
    $office = $data->office;
}

if ($type == "old") {
    $qryInventoryItems = $conn->query("SELECT DISTINCT inv.propertyNo, inv.stockAvailable, issue.issueDate, 
                                                       issue.inventoryID, issue.issuedBy, issue.approvedBy, 
                                                       issue.quantity AS issueQuantity, 
                                                       issue.issueRemarks, item.unitIssue, item.itemDescription, 
                                                       item.amount, item.quantity, inv.id 
                                       FROM tblinventory_items AS inv 
                                       INNER JOIN tblitem_issue AS issue 
                                       ON issue.inventoryID = inv.id 
                                       JOIN tblpo_jo_items AS item 
                                       ON item.id = inv.poItemID 
                                       WHERE inv.inventoryClassNo = '" . $risNo . "' 
                                       AND issue.empID = '" . $empID . "'") 
                                       or die(mysql_error($conn));
} else if ($type == "new") {
    if ($multiple == "n") {
        $qryInventoryItems = $conn->query("SELECT inv.propertyNo, po.unitIssue, po.itemDescription,  
                                                  inv.id, po.quantity, inv.stockAvailable 
                                           FROM tblinventory_items inv 
                                           INNER JOIN tblpo_jo_items po
                                           ON inv.poItemID = po.id 
                                           WHERE inv.id = '" . $inventoryID . "' 
                                           ORDER BY inv.poItemID ASC")
                                           or die(mysql_error($conn));
    } else if ($multiple == "y") {
        $qryInventoryItems = $conn->query("SELECT inv.propertyNo, po.unitIssue, po.itemDescription,  
                                                  inv.id, po.quantity, inv.stockAvailable 
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
        <th colspan="9">REQUISITION AND ISSUE SLIP</th>
    </tr>
    <tr>
        <td colspan="9">
            <div class="row">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-1 col-xs-1">
                        <label for="txtDivision">
                            <strong>Division: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input class="form-control font-color-1" name="txtDivision" 
                               type="text" id="txtDivision" value="<?php echo $division ?>"
                               disabled="disabled">
                    </div>
                    <div class="col-md-1 col-xs-1">
                        <label for="txtRisNo">
                            <strong>RIS No: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input class="form-control font-color-1" name="txtRisNo" 
                               type="text" id="txtRisNo" value="<?php echo $risNo ?>">
                    </div>
                </div>

                <div class="col-md-12 col-xs-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-1 col-xs-1">
                        <label for="txtOffice">
                            <strong>Office: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <input class="form-control font-color-1" name="txtOffice" 
                        	   type="text" id="txtOffice" value="<?php echo $office ?>"
                               disabled="disabled">
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <table class="table table-bordered table-hover">
                <tr>
                    <th colspan="4" width="55%" style="text-align: center; border-left: 2px #005e7c solid;"> Requisistion </td>
                    <th colspan="2" width="15%" style="text-align: center;"> Stock Available </td>
                    <th colspan="2" width="28%" style="text-align: center; border-right: 2px #005e7c solid;"> Issue </td>
                    <th colspan="1"></td>
                </tr>
                <tr>
                	<td width="10%" style="border-left: 2px #005e7c solid;
                						   border-bottom: 1px #005e7c solid;">
                		<strong> Stock No. </strong>
                	</td>
                	<td width="5%" style="border-bottom: 1px #005e7c solid;"><strong> Unit </strong></td>
                	<td width="35%" style="border-bottom: 1px #005e7c solid;">
                		<strong> Description </strong>
                	</td>
                	<td width="5%" style="border-right: 2px #005e7c solid;
                						  border-bottom: 1px #005e7c solid;">
                		<strong> Quantity </strong>
                	</td>
                	<td width="7.5%" style="border-bottom: 1px #005e7c solid;">
                		<strong> Yes </strong>
                	</td>
                	<td width="7.5%" style="border-right: 2px #005e7c solid;
                							border-bottom: 1px #005e7c solid;">
                		<strong> No </strong></td>
                	<td width="10%" style="border-bottom: 1px #005e7c solid;">
                		<strong> Quantity </strong>
                	</td>
                	<td width="18%" style="border-right: 2px #005e7c solid;
                						   border-bottom: 1px #005e7c solid;">
                		<strong> Remarks </strong>
                	</td>
                    <td width="2%"></td>
                </tr>

                <?php
                    
                if (mysqli_num_rows($qryInventoryItems)) {
                    $cnt = 0;
                    $countItem = 0;

                	while ($data = $qryInventoryItems->fetch_object()) {
                        $quantity = 0;
                        $issueRemarks = "";

                        if ($type == "old") {
                            $quantity = $data->issueQuantity;
                            $issueRemarks = $data->issueRemarks;
                            $recievedBy = $empID;
                            $issuedBy = $data->issuedBy;
                            $approvedBy = $data->approvedBy;
                            $issueDate = $data->issueDate;
                        }

                		echo '<tr id="row_0" class="row-'. ++$countItem . '">';
			            echo '<td width="10%" style="border-left: 2px #005e7c solid;">
			                		<strong class="font-color-1">' . $data->propertyNo . '</strong>
			                  </td>';
			            echo '<td width="5%" class="font-color-1">
			                		' . $data->unitIssue . '
			                  </td>';
			            echo '<td class="font-color-1" width="35%">
			                  	  <textarea rows="5" class="form-control font-color-1" disabled="disabled">' . 
                                       $data->itemDescription . 
                                  '</textarea>
			                  </td>';
			            echo '<td width="5%" style="border-right: 2px #005e7c solid;">' . 
                                   $data->quantity . '
			                  </td>';
			            echo '<td width="7.5%">
			                  	  <input type="radio" class="input-stock" name="check-stock-available-'. $cnt . 
                                      '" value="yes" checked="checked">
			                  </td>';
                        echo '<td width="7.5%" style="border-right: 2px #005e7c solid;">
			                		<input type="radio" class="input-stock" name="check-stock-available-'. $cnt . 
                                        '" value="no">
                              </td>';
			            echo '<td width="10%">
                                  <input class="input-id" type="hidden" value="' . $data->id . '">
			                  	  <input type="number" class="input-quantity form-control font-color-1 required"
			                  		     value="' . $quantity . '" min="0">
			                  </td>';
			            echo '<td width="20%" style="border-right: 2px #005e7c solid;">
			                  	  <textarea rows="5" class="input-remarks form-control font-color-1" placeholder="...">' .
                                      $issueRemarks . 
                                  '</textarea>
			                  </td>';
                        echo '  <td style="text-align:center;">
                                    <a href="javascript: $(this).deleteItem(\'row-' . $countItem. '\')" title="Delete Item">
                                        <img class="img-button" src="../../assets/images/delete.png" />
                                    </a>
                                </td></tr>';

                        $cnt++;
                	}
                }  else {
                    echo '<tr id="row_0" class="row-0">';
                    echo '<td width="10%" style="border-left: 2px #005e7c solid;">
                                <strong class="font-color-1">' . $data->propertyNo . '</strong>
                          </td>';
                    echo '<td width="5%" class="font-color-1">
                                ' . $data->unitIssue . '
                          </td>';
                    echo '<td class="font-color-1" width="35%">
                              <textarea rows="5" class="form-control font-color-1" disabled="disabled">' . 
                                   $data->itemDescription . 
                              '</textarea>
                          </td>';
                    echo '<td width="5%" style="border-right: 2px #005e7c solid;">' . 
                               $data->quantity . '
                          </td>';
                    echo '<td width="7.5%">
                              <input type="radio" class="input-stock" name="check-stock-available-'. $cnt . 
                                  '" value="yes" checked="checked">
                          </td>';
                    echo '<td width="7.5%" style="border-right: 2px #005e7c solid;">
                                <input type="radio" class="input-stock" name="check-stock-available-'. $cnt . 
                                    '" value="no">
                          </td>';
                    echo '<td width="10%">
                              <input class="input-id" type="hidden" value="' . $data->id . '">
                              <input type="number" class="input-quantity form-control font-color-1 required"
                                     value="' . $quantity . '" min="0">
                          </td>';
                    echo '<td width="20%" style="border-right: 2px #005e7c solid;">
                              <textarea rows="5" class="input-remarks form-control font-color-1" placeholder="...">' .
                                  $issueRemarks . 
                              '</textarea>
                          </td>';
                    echo '  <td style="text-align:center;">
                                <a href="javascript: $(this).deleteItem(\'row-' . $countItem. '\')" title="Delete Item">
                                    <img class="img-button" src="../../assets/images/delete.png" />
                                </a>
                            </td></tr>';
                    echo '<tr>
                              <td colspan="9" align="center">
                                  <button id="btn-add-item" class="btn btn-default btn-block"
                                     style="padding: 16px; border: 4px #4577b4 dashed;color: #4577b4;background-color: #fff;font-weight: bold;">
                                      <span class="glyphicon glyphicon-plus"></span>
                                      Add Item
                                  </button>
                              </td>
                          </tr>';


                    /*
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
                          </tr>';*/
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
                        <label for="txtDivision">
                            <strong>Approved By: </strong>
                        </label>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <select id="sel-approved-by" class="form-control required">
                            <option value=""> -- SELECT APPROVED BY -- </option>
                            <?php
                                $qrySignatory = $conn->query("SELECT signatoryID, name, position 
                                                              FROM tblsignatories")
                                                              or die(mysql_error($conn));

                                if (mysqli_num_rows($qrySignatory)) {
                                    while ($data = $qrySignatory->fetch_object()) {
                                        echo '<option value="' . $data->signatoryID . '"';
                                        echo $data->signatoryID == 21 ? ' selected="selected"':'';
                                        echo $data->signatoryID == $approvedBy ? ' selected="selected"':'';
                                        echo '> ' . $data->name . ' [' . $data->position . '] </option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-1 col-xs-1">
                        <label for="txtOffice">
                            <strong>Issued By: </strong>
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
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12 col-xs-12">
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

                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
</table>