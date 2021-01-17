<?php
    
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$poNo = "";
$itemCounter = 0;

if (isset($_POST["poNo"]) && !empty($_POST["poNo"])) {
    $poNo = $_POST["poNo"];
    $orsID = "";

    if (isset($_POST["orsID"])) {
        $orsID = $_POST["orsID"];
    }
}

?>

<table id="tblStyle" class="table table-responsive">
    <tr>
        <th colspan="4">FINALIZE INSPECTION AND ACCEPTANCE REPORT</th>
    </tr>
    <tr>
        <td colspan="4" align="center">
            <div class="table-container-1">
                <table class="table table-hover table-responsive" id="tblLists">
                    <?php

                    $resQry = $conn->query("SELECT po.id, po.prID, po.infoID, po.unitIssue, po.quantity, 
                                                   po.itemDescription, inv.propertyNo, inv.inventoryClass,
                                                   po.amount, inv.itemClassification, inv.groupNo 
                                            FROM tblpo_jo_items po 
                                            LEFT JOIN tblinventory_items inv 
                                            ON inv.poItemID = po.id 
                                            WHERE po.poNo = '". $poNo ."' 
                                            AND excluded = 'n' 
                                            ORDER BY po.id ASC")
                                            or die(mysqli_error($conn));

                    if (mysqli_num_rows($resQry)) {
                        echo '<tr>
                                  <th style="text-align: center;" width="" hidden>Property/Stock No</th>
                                  <th style="text-align: center;" width="38%">Description</th>
                                  <th style="text-align: center;" width="10%">Unit</th>
                                  <th style="text-align: center;" width="10%">Unit Cost</th>
                                  <th style="text-align: center;" width="19%">Inventory Classification</th>
                                  <th style="text-align: center;" width="15%">Item Category</th>
                                  <th style="text-align: center;" width="8%">Group No</th>
                              </tr>';

                        while ($list = $resQry->fetch_object()) {
                            $itemCounter++;

                            echo '<input id="input-pr-id" type="hidden" value="' . $list->prID . '">';
                            echo '<input id="input-po-no" type="hidden" value="' . $poNo . '">';
                            echo '<input class="input-item-no" type="hidden" value="' . $itemCounter . '">';

                            echo '<tr id="row_0" style="background-color: #fff; color: #235e7a;">';
                            echo '<td hidden>
                                     <strong><input class="font-color-1 form-control input-document-no" type="text" placeholder="..." value="' . $list->propertyNo . '"></strong>
                                     <input class="input-item-id" type="hidden" value="' . $list->id . '">
                                     <input class="input-info-id" type="hidden" value="' . $list->infoID . '">
                                     <input class="input-quantity" type="hidden" value="' . $list->quantity . '">
                                </td>';
                            echo '<td>' . $list->itemDescription . '</td>';
                            echo '<td style="text-align: center;">' . $list->unitIssue . '</td>';
                            echo '<td style="text-align: center;">' . number_format($list->amount, 2) . '</td>';
                            echo '<td><strong><select class="form-control font-color-1 input-classification required">';
                            echo '<option value=""> -- Select Classification -- </option>';
                            
                            if (!empty($list->inventoryClass)) {
                                echo '<option value="par"';
                                echo $list->inventoryClass == "par" ? ' selected="selected"':'';
                                echo '> Property Acknowledgement Receipt (PAR) </option>';
                                echo '<option value="ris"';
                                echo $list->inventoryClass == "ris" ? ' selected="selected"':'';
                                echo '> Requisition and Issue Slip (RIS) </option>';
                                echo '<option value="ics"';
                                echo $list->inventoryClass == "ics" ? ' selected="selected"':'';
                                echo '> Inventory Custodian Slip (ICS) </option>';
                            } else {
                                echo '<option value="par"';
                                echo $list->amount > 15000 ? ' selected="selected"':'';
                                echo '> Property Acknowledgement Receipt (PAR) </option>';
                                echo '<option value="ris"';
                                echo $list->amount == 0 ? ' selected="selected"':'';
                                echo '> Requisition and Issue Slip (RIS) </option>';
                                echo '<option value="ics"';
                                echo $list->amount < 15000 ? ' selected="selected"':'';
                                echo '> Inventory Custodian Slip (ICS) </option>';
                            }

                            echo '</select></strong></td>';
                            echo '<td><strong><select class="form-control font-color-1 input-item-class">';
                            echo '<option value="0"> -- None -- </option>';
                            
                                $qryItemClassificaton = $conn->query("SELECT * 
                                                                      FROM tblitem_categories") 
                                                                      or die;

                                while ($list1 = $qryItemClassificaton->fetch_object()) {
                                    echo '<option value="' . $list1->categoryID . '"';
                                    echo $list->itemClassification == $list1->categoryID ? ' selected="selected"':'';
                                    echo '> ' . $list1->category . ' </option>';
                                }

                            echo '</select></strong></td>';
                            echo '<td><strong><select class="form-control font-color-1 input-group">';
                            echo '<option value="0"> 0 </option>';

                                for ($grpNo = 1; $grpNo <= 300 ; $grpNo++) { 
                                    echo '<option value="' . $grpNo . '"';
                                    echo $grpNo == $list->groupNo ? ' selected="selected"':'';
                                    echo '> ' . $grpNo . ' </option>';
                                }

                            echo '</select></strong></td>';
                            echo '</tr>';
                        }
                    }

                    ?>

                </table>
            </div>
        </td>
    </tr>
</table>

<?php
    unset($_POST["poNo"]);
    unset($_POST["orsID"]);
?>