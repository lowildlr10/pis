<?php
    
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$supplier = "";
$poNo = "";
$requisitioningOffice = "";
$iarNo = "";
$iarDate = "";
$invoiceNo = "";
$invoiceDate = "";
$sign1 = "";
$sign2 = "";

if (isset($_POST["poNo"]) && !empty($_POST["poNo"])) {
    $poNo = $_POST["poNo"];
    $orsID = "";

    if (isset($_POST["orsID"])) {
        $orsID = $_POST["orsID"];
    }

    $qryEdit = $conn->query("SELECT bid.company_name, po.poNo, sec.section, iar.iarNo,
                                    iar.iarDate, iar.invoiceNo, iar.invoiceDate, 
                                    iar.inspectedBy, iar.signatorySupply 
                             FROM tblpo_jo AS po 
                             INNER JOIN tblpo_jo_items AS item 
                             ON po.PRID = item.prID 
                             INNER JOIN tbliar AS iar 
                             ON iar.prID = item.prID 
                             INNER JOIN tblors AS ors 
                             ON ors.prID = item.prID 
                             INNER JOIN tblpr AS pr 
                             ON pr.prID = ors.prID 
                             INNER JOIN tblemp_accounts AS emp 
                             ON emp.empID = pr.requestBy  
                             INNER JOIN tblsections AS sec 
                             ON sec.sectionID = emp.sectionID 
                             INNER JOIN tblbidders AS bid 
                             ON po.awardedTo = bid.bidderID 
                             WHERE po.poNo = '". $poNo ."' 
                             AND iar.orsID = '" . $orsID . "'")
                             or die(mysqli_error($conn));

    if (mysqli_num_rows($qryEdit)) {
        $data = $qryEdit->fetch_object();
        $supplier = $data->company_name;
        $poNo = $data->poNo;
        $requisitioningOffice = $data->section;
        $iarNo = $data->iarNo;
        $iarDate = $data->iarDate;
        $invoiceNo = $data->invoiceNo;
        $invoiceDate = $data->invoiceDate;
        $sign1 = $data->inspectedBy;
        $sign2 = $data->signatorySupply;
    }
}

?>

<table id="tblStyle" class="table table-responsive">
    <tr>
        <th colspan="4">INSPECTION AND ACCEPTANCE REPORT</th>
    </tr>
    <tr>
        <td colspan="2">
            <div class="row">
                <div class="col-md-12" style="padding: 5px; padding-bottom: 15px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtSupplier">
                            <strong>Supplier: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtSupplier" type="text" id="txtSupplier" 
                               value="<?php echo $supplier ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtpoNo">
                            <strong>PO No./Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtpoNo" type="text" id="txtpoNo" 
                               value="<?php echo $poNo ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtReqOffice">
                            <strong>Requisitioning Office/Dept.: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtReqOffice" type="text" id="txtReqOffice" 
                               value="<?php echo $requisitioningOffice ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtAddress">
                            <strong>Responsibility Center Code: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" type="text"
                               value="19 001 03000 14" disabled="disabled">
                    </div>
                </div>
            </div>
        </td>
        <td colspan="2">
            <div class="row">
                <div class="col-md-12" style="padding: 5px; padding-bottom: 15px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtIARNo">
                            <strong>IAR No.: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtIARNo" type="text" id="txtIARNo" 
                               value="<?php echo $iarNo ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtIarDate">
                            <strong>Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <div class='input-group date' id="iarDate">
                                <input type='text' class="form-control" id="txtIarDate" name="txtIarDate" value="<?php echo $iarDate ?>">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtInvoiceNo">
                            <strong>Invoice No.: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control font-color-1" name="txtInvoiceNo" type="text" id="txtInvoiceNo" 
                               value="<?php echo $invoiceNo ?>">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-3" style="text-align: left;">
                        <label for="txtInvoiceDate">
                            <strong>Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <div class='input-group date' id="invoiceDate">
                                <input type='text' class="form-control" id="txtInvoiceDate" name="txtInvoiceDate" value="<?php echo $invoiceDate ?>">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="center">
            <div class="table-container-1">
                <table class="table table-hover table-responsive" id="tblLists">
                    <?php

                    $resQry = $conn->query("SELECT unitIssue, quantity, itemDescription 
                                            FROM tblpo_jo_items 
                                            WHERE poNo = '". $poNo ."' 
                                            AND excluded = 'n'")
                                            or die(mysqli_error($conn));

                    if (mysqli_num_rows($resQry)) {
                        echo '<tr>
                                  <th style="text-align: center;" width="10%">Stock/Property No.</th>
                                  <th style="text-align: center;" width="60%">Description</th>
                                  <th style="text-align: center;" width="15%">Unit</th>
                                  <th style="text-align: center;" width="15%">Quantity</th>
                              </tr>';

                        while ($list = $resQry->fetch_object()) {
                            echo '<tr id="row_0" style="background-color: #fff; color: #235e7a;">';
                            echo '<td></td>';
                            echo '<td>' . $list->itemDescription . '</td>';
                            echo '<td>' . $list->unitIssue . '</td>';
                            echo '<td>' . $list->quantity . '</td></tr>';
                        }
                    }

                    ?>

                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="form-group">
                <label for="sel-insp-by">Inspection Officer/Inspection Committee</label>
                <select name="sel-insp-by" id="sel-insp-by" class="form-control">
                    <?php
                        $qryEmps = $conn->query("SELECT * FROM tblsignatories 
                                                     WHERE active = 'yes' 
                                                     AND iar = 'y' 
                                                     ORDER BY name ASC") 
                                                 or die(mysqli_error($conn));

                        while($data = $qryEmps->fetch_object()){
                            echo '<option value="'.$data->signatoryID.'"';
                            echo $data->signatoryID == $sign1 ? ' selected="selected"':'';
                            echo '>'.$data->name.' ['.$data->position.']</option>';
                        }
                    ?>
                </select>
            </div>
        </td>
        <td colspan="2">
            <div class="form-group">
                <label for="sel-supply">Supply and/or Property Custodian</label>
                <select name="sel-supply" id="sel-supply" class="form-control">
                    <?php
                        $qryEmps = $conn->query("SELECT * FROM tblsignatories 
                                                     WHERE active = 'yes' 
                                                     AND iar = 'y' 
                                                     ORDER BY name ASC") 
                                                 or die(mysqli_error($conn));

                        while($data = $qryEmps->fetch_object()){
                            if ($data->signatoryID == "53") {
                                echo '<option value="'.$data->signatoryID.'"';
                                echo $data->signatoryID == $sign1 ? ' selected="selected"':'';
                                echo '>'.$data->name.' ['.$data->position.']</option>';
                            }
                        }
                    ?>
                </select>
            </div>
        </td>
    </tr>
</table>

<?php
    unset($_POST["poNo"]);
    unset($_POST["orsID"]);
?>