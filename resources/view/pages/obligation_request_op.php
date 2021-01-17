<?php
	
include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/class_dbop.php");
include_once($dir . "class_function/functions.php");

$empID = "";
$poNo = "";
$ORSNo = "";
$serialNo = "";
$orsDate = "";
$payee = "";
$office = "";
$address = "";
$particulars = "";
$objectCode = "";
$amount = "";
$sign1 = "";
$sign2 = "";
$signDate1 = "";
$signDate2 = "";
$docType = "";

if (isset($_POST["poNo"]) && !empty($_POST["poNo"])) {
	$poNo = $_POST["poNo"];

    $qryEdit = $conn->query("SELECT empID, poNo, orsNo, orsDate, payee, office, address, particulars,
                                    uacsObjectCode, amount, signatoryReq, signatoryBudget, 
                                    signatoryReqDate, signatoryBudgetDate, serialNo, orsDate, documentType 
                            FROM tblors ors 
                            INNER JOIN tblpr pr 
                            ON pr.prID = ors.prID 
                            INNER JOIN tblemp_accounts emp 
                            ON pr.requestBy = emp.empID 
                            WHERE poNo='". $poNo ."'")
                            or die(mysqli_error($conn));

    if ($qryEdit) {
        $data = $qryEdit->fetch_object();
        $empID = $data->empID;
        $ORSNo = $data->orsNo;
        $serialNo = $data->serialNo;
        $orsDate = $data->orsDate;
        $payee = $data->payee;
        $address = $data->address;
        $particulars = $data->particulars;
        $objectCode = $data->uacsObjectCode;
        $amount = $data->amount;
        $sign1 = $data->signatoryReq;
        $sign2 = $data->signatoryBudget;
        $signDate1 = $data->signatoryReqDate;
        $signDate2 = $data->signatoryBudgetDate;
        $docType = $data->documentType;
    }
} else {
    if (isset($_POST["pid"])) {
        $pid = $_POST["pid"];

        $qryEdit = $conn->query("SELECT empID, poNo, orsNo, orsDate, payee, office, address, particulars,
                                        uacsObjectCode, amount, signatoryReq, signatoryBudget, 
                                        signatoryReqDate, signatoryBudgetDate, serialNo, orsDate, documentType  
                                FROM tblors ors
                                INNER JOIN tblpr pr 
                                ON pr.prID = ors.prID 
                                INNER JOIN tblemp_accounts emp 
                                ON pr.requestBy = emp.empID 
                                WHERE id='". $pid ."'")
                                or die(mysqli_error($conn));

        if ($qryEdit) {
            $data = $qryEdit->fetch_object();
            $empID = $data->empID;
            $ORSNo = $data->orsNo;
            $serialNo = $data->serialNo;
            $orsDate = $data->orsDate;
            $payee = $data->payee;
            $office = $data->office;
            $address = $data->address;
            $particulars = $data->particulars;
            $objectCode = $data->uacsObjectCode;
            $amount = $data->amount;
            $sign1 = $data->signatoryReq;
            $sign2 = $data->signatoryBudget;
            $signDate1 = $data->signatoryReqDate;
            $signDate2 = $data->signatoryBudgetDate;
            $docType = $data->documentType;
        }
    }
}

?>

<table id="tblStyle" class="table">
    <tr>
        <th colspan="4">OBLIGATION/BUDGET UTILIZATION REQUEST AND STATUS</th>
    </tr>
    <tr>
        <td colspan="4">
            <div class="row" style="text-align: right;">
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtDocType">
                            <strong>Document Type: </strong>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <select name="txtDocType" id="txtDocType" class="required form-control font-color-1">
                            <?php
                                echo '<option value="ors"';
                                echo $docType == "ors" ? ' selected="selected"':'';
                                echo '> Obligation Request and Status (ORS) </option>';

                                echo '<option value="burs"';
                                echo $docType == "burs" ? ' selected="selected"':'';
                                echo '> Utilization Budget Request and Status (BURS) </option>';
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtSerialNo">
                            <strong>Serial No: </strong>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input class="form-control font-color-1" name="txtSerialNo" 
                               type="text" id="txtSerialNo" value="<?php echo $serialNo ?>">
                    </div>
                </div>
                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtOrsDate">
                            <strong>Date: </strong>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <div class='input-group date' id='txtOrsDate'>
                                <input type='text' class="form-control" name="txtOrsDate" 
                                       value="<?php echo $orsDate ?>">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-md-12">
                    
                    <div class="col-md-2">
                        <label for="txtPayee">
                            <strong>ORS No.: </strong><font color="#FF0000">*</font>
                        </label>
                    </div>

                    <div class="col-md-10">
                        <input class="required form-control font-color-1" name="txtORS" type="text" id="txtORS" value="<?php echo $ORSNo ?>">
                    </div>
                  
                </div>
                !-->
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtPayee">
                            <strong>Payee: </strong><font color="#FF0000">*</font>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <select name="txtPayee" id="txtPayee" class="required form-control font-color-1" disabled="disabled">
                            <?php
                                if (empty($_POST["poNo"])) {
                                    if (isset($_SESSION['log_admin'])) {
                                        $qryEmp = $conn->query("SELECT * FROM tblemp_accounts") 
                                                  or die(mysqli_error($conn));
                                    } else if (isset($_SESSION['log_pstd'])) {
                                        $qryEmp = $conn->query("SELECT * FROM tblemp_accounts 
                                                                WHERE sectionID='" . $_SESSION['log_sectionID'] . "'") 
                                                  or die(mysqli_error($conn));
                                    } else if (isset($_SESSION['log_staff'])) {
                                        $qryEmp = $conn->query("SELECT * FROM tblemp_accounts 
                                                                WHERE empID='" . $_SESSION['log_empID'] . "'") 
                                                  or die(mysqli_error($conn));
                                    }
                                    
                                    $office = "DOST-CAR";
                                    $address = "BSU Compound, Km.6, La trinidad, Benguet";

                                    while ($data = $qryEmp->fetch_object()){
                                        echo '<option value="'.$data->empID.'"';
                                        echo $data->empID == $empID ? ' selected="selected"':'';
                                        echo '>' . $data->firstname . ' ' . $data->middlename . ' ' . $data->lastname .  '</option>';
                                    } 
                                } else {
                                    echo '<option value="">-- Select payee --</option>';

                                    $qrySuppliers = $conn->query("SELECT * FROM tblbidders ORDER BY company_name ASC") 
                                                                  or die(mysqli_error($conn));

                                    while ($data = $qrySuppliers->fetch_object()){
                                        echo '<option value="'.$data->bidderID.'"';
                                        echo $data->bidderID == $payee ? ' selected="selected"':'';
                                        echo '>' .$data->company_name. '</option>';
                                    } 
                                }

                                
                            ?>
                        </select>

                        <!--
                        <input class="required form-control font-color-1" name="txtPayee" type="text" id="txtPayee" value="<?php echo $payee ?>">
                        !-->
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtAddress">
                            <strong>Office: </strong>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input class="form-control font-color-1" name="txtOffice" type="text" id="txtOffice" value="<?php echo $office ?>">
                    </div>
                </div>

                <div class="col-md-12" style="padding: 5px;"></div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="txtAddress">
                            <strong>Address: </strong><font color="#FF0000">*</font>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input class="required form-control font-color-1" name="txtAddress" type="text" id="txtAddress" value="<?php echo $address ?>">
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <table class="table table-bordered table-hover">
                <tr>
                    <th style="text-align: center;">Responsibility Center</td>
                    <th style="text-align: center;">Particulars <font color="#FF0000">*</font></td>
                    <th style="text-align: center;">MFO/PAP</td>
                    <th style="text-align: center;">UACS Object Code</td>
                    <th style="text-align: center;">Amount <font color="#FF0000">*</font></td>
                </tr>
                <tr>
                    <td>19 001 03000 14 </td>
                    <td>
                        <textarea class="required form-control font-color-1" name="txtParticulars" id="txtParticulars" 
                            cols="30" rows="5" style="resize: none;"><?php echo $particulars ?></textarea>
                    </td>
                    <td>
                        3-Regional Office <br>
                        A.III.c.1 <br>
                        A.III.b.1 <br>
                        A.III.c.2 <br>
                    </td>
                    <td>
                        <input class="form-control font-color-1" name="txtAcntCode" type="text" id="txtAcntCode" value="<?php echo $objectCode ?>">
                    </td>
                    <td>   
                        <?php
                        if (isset($_POST["poNo"]) && !empty($_POST["poNo"]) || isset($_POST["pid"])) {
                        ?>
                            <input class="form-control font-color-1" name="txtAmount" type="text" id="txtAmount" 
                                value="<?php echo $amount ?>" disabled="disabled">
                        <?php
                        } else {
                        ?>
                            <input class="form-control font-color-1 required" name="txtAmount" type="text" id="txtAmount" 
                                value="<?php echo $amount ?>">
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <p style="text-align: left;">
                            <strong> A. </strong><br>
                            <strong>Certified:</strong>Charges to appropriation/alloment Certified: Allotment available and obligated
                            necessary, lawful and under my direct supervision; for the purpose/adjustment necessary as
                            and supporting documents valid, proper and legal.<br><br>
                            <label>Signature:</label> _________________________________________<br><br>
                            <label for="selCert1">Printed Name:</label>
                            <select name="selCert1" id="selCert1" class="form-control">
                                <?php
                                    $qryEmps = $conn->query("SELECT * FROM tblsignatories 
                                                             WHERE active = 'yes' 
                                                             AND ors = 'y' 
                                                             ORDER BY name ASC") or die(mysqli_error($conn));
                                    
                                    while ($data = $qryEmps->fetch_object()) {
                                        echo '<option value="'.$data->signatoryID.'"';
                                        echo $data->signatoryID == $sign1 ? ' selected="selected"':'';
                                        echo '>'.$data->name.' [' . $data->position .']</option>';
                                    }
                                ?>
                            </select>
                            <br><br>
                            <label for="txtCDate1">Date:</label>
                            <div class="form-group">
                                <div class='input-group date' id="txtDate1">
                                    <input type='text' class="form-control" id="txtCDate1" name="txtCDate1" value="<?php echo $signDate1 ?>">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="text-align: left;">
                            <strong> B. </strong><br>
                            <strong>Certified:</strong> Allotment available and obligated
                            necessary, lawful and under my direct supervision; for the purpose/adjustment necessary as
                            and supporting documents valid, proper and legal. indicated above.<br><br>
                            <label>Signature:</label> _________________________________________<br><br>
                            <label for="selCert2">Printed Name:</label>
                            <select name="selCert2" id="selCert2" class="form-control">
                                <?php
                                    $qryEmps = $conn->query("SELECT * FROM tblsignatories WHERE active='yes' ORDER BY name ASC") 
                                                            or die(mysqli_error($conn));

                                    while($data = $qryEmps->fetch_object()){
                                        if ($data->signatoryID == "9") {
                                            echo '<option value="'.$data->signatoryID.'"';
                                            echo $data->signatoryID == $sign2 ? ' selected="selected"':'';
                                            echo '>'.$data->name.' ['.$data->position.']</option>';
                                        }
                                    }
                                ?>
                            </select>
                            <br><br>
                            <label for="txtCDate2">Date:</label>
                            <div class="form-group">
                                <div class='input-group date' id="txtDate2">
                                    <input type='text' class="form-control" id="txtCDate2" name="txtCDate2" value="<?php echo $signDate2 ?>">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" name="userID" id="userID" value="<?php echo $_SESSION['log_empID'] ?>">
                            <input type="hidden" name="sectionID" id="sectionID" value="<?php echo $_SESSION['log_sectionID'] ?>">
                        </p>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<?php
    unset($_POST["poNo"]);
    unset($_POST["pid"]);
?>