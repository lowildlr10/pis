<?php

include_once("session.php");
include_once("../../../config.php");
include_once($dir . "dbcon.php");
include_once($dir . "class_function/functions.php");

	$prNo = "";
	$prID = "";
	$itemCtr = 0;
	$curItem = "";
	$toggle = "";
	$bidderCount = array();
	$_bidderCount = array();
	$arrayBidder = array();
	$groupNumber = array();

	if (isset($_REQUEST['pid'])) {
		$prID = $_REQUEST['pid'];
	} else {
		header("Location: " . $dir . "index.php");
	}

	if (isset($_REQUEST['prno'])) {
		$prNo = $_REQUEST['prno'];
	}

	if (isset($_REQUEST['toggle'])) {
		$toggle = $_REQUEST['toggle'];
	}

	if (isset($_REQUEST['bidderCount'])) {
		$_bidderCount = $_REQUEST['bidderCount'];

		foreach ($_bidderCount as $val) {
			$tempCount = explode("-", $val);

			$bidderCount[] = $tempCount[1];
		}
	}

	/*
	if (isset($_REQUEST['bidderCount'])) {
		$bidderCount = $_REQUEST['bidderCount'];
	}*/

?>
	
	<table class="table table-hover" width="100%" border="0" align="center" cellspacing="1" cellpadding="4" id="tblStyle">
	  	<tr>
	    	<th>PR No.: <?php echo $prNo ?></th>
	  	</tr>
	  	
	  	<?php
		if (isset($result)) {
			echo '<tr><td><div class="msg">'.$result.'</div></td></tr>';
		}
		?>

	  	<tr>
	    	<td>

			<?php
			    $qryPRItems = "SELECT pr.prID, info.groupNo, info.estimateUnitCost,info.quantity, 
			    					  info.itemDescription, info.infoID, info.unitIssue 
			    			   FROM tblpr pr 
			    			   INNER JOIN tblpr_info info 
			    			   ON pr.prID = info.prID 
			    			   WHERE pr.prID = '" . $prID . "' 
			    			   ORDER BY info.infoID ASC";

				if ($resQry = $conn->query($qryPRItems)) {
					if (mysqli_num_rows($resQry)) {
						echo '';
						echo '<table class="table table-hover" cellpadding="4" cellspacing="0" id="tblLists" align="center" width="97%" align="center">';
				        echo '<tr>
				        		  <th width="15%">
				        		  	  Group No.
				        		  </th>
				        		  <th width="5%">
				        		  	  Quantity
				        		  </th>
				        		  <th width="15%">
				        		  	  Unit
				        		  </th>
				        		  <th width="45%">
				        		  	  Item Description
				        		  </th>
				        		  <th width="20%">
				        		  	  Approved Budget
				        		  </th>
				        	  </tr>';

						while ($data = $resQry->fetch_object()) {
							if ($curItem != $data->infoID) {
								$curItem = $data->infoID;
								$itemCtr++;

								echo '<tr id=row_0>';

								echo '<td align="center">
										  <select class="group-option form-control" style="width: 80%;">';

								for ($gCount = 0; $gCount <= 20; $gCount++) { 
									echo '	   <option value="' . $curItem . '-'. $gCount .'"';
									echo 			$data->groupNo == $gCount?' selected="selected"':'';
									echo '	   >'. $gCount .'</option>';
								}

								echo '	  </select>
									  </td>';
				                echo '<td align="center">'.$data->quantity.'</td>';
				                echo '<td align="center">'.$data->unitIssue.'</td>';
								echo '<td align="center" style="padding-left: 10px; border-bottom: 1px dashed #ccc">' . $data->itemDescription . '</td>';
				                echo '<td style="text-align:center; padding-left: 1em;">' . number_format($data->estimateUnitCost,2,'.',',') . '</td>
				                	 <tr>';
							}
							
						}//end while

				 		echo '</table>';
					} else {
						echo '
						<div align="center" style="color:#999999">
							<br>
							----- No available record for posting. -----
							<br>
							<br>
						</div>';
					}
				}
			?>
			</td>
	  	</tr>
	</table>

	<div class="form-group well" style="text-align: left; background-color: #e1eff3;">
		<form id="frm-save-file" enctype="multipart/form-data" method="POST">
			<label>Upload Other Canvass Form (Optional):</label>
			<div class="input-group">
                <label class="input-group-btn">
                    <span class="btn btn-primary">
                        Browse&hellip; <input type="file" style="display: none;" 
                        					  name="file[]" id="file-canvass" 
					    					  accept=".xls,.xlsx,.doc,.docx,.pdf" multiple>
                    </span>
                </label>
                <input id="text-file" type="text" class="form-control" readonly>
            </div>
            <span class="help-block">
                Add by selecting one or more files.
            </span>

		    <div style="margin-top: 15px;
					    display: flex;
					    overflow: auto;">
		    	<?php

		    	if (is_dir($dir . 'uploads/canvass/' . $prNo) == false ) {
				    mkdir($dir . 'uploads/canvass/' . $prNo, 0700);
				}

		    	$listDir = scandir($dir . 'uploads/canvass/' . $prNo);

		    	if ($listDir) {
		    		foreach ($listDir as $key => $file) {
			    		if ($file != ".." && $file != ".") {
			    			echo '<div id="del-' . $key . '"><a class="btn btn-default btn-sm" href="../../../uploads/canvass/' . 
			    				$prNo . '/' . $file . '" target="_blank" style="width: 7em;
																			    border-radius: 1em;
																			    margin: 3px;
																			    border: 2px #005e7c solid;">
			    				<div onclick="javascript: $(this).deleteFile(\'' . $key . 
			    							  '\',\'' . $prNo . '\',\'' . $file . '\'); return false;"
			    					 style="text-align: right; color: #dc3545;">
				    			    <span style="border: 2px #dc3545 solid; border-radius: 4px;" 
				    			    	  class="glyphicon glyphicon-remove"
				    			    	  rel="tooltip" 
				    			    	  title="Delete \'' . $file . '\'">
				    			    </span>
								</div>
			    				<span class="glyphicon glyphicon-file"></span>' . 
			    				$file . 
			    			'</a></div>';
			    		}
			    	}
		    	}

		    	?>
		    </div>
		</form>
	</div>

	<script type="text/javascript">
		// We can attach the `fileselect` event to all file inputs on the page
		$(document).on('change', ':file', function() {
		  	var input = $(this),
		  	    numFiles = input.get(0).files ? input.get(0).files.length : 1,
		  	    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		  	input.trigger('fileselect', [numFiles, label]);
		});

		// We can watch for our custom `fileselect` event like this
		$(document).ready( function() {
		    $(':file').on('fileselect', function(event, numFiles, label) {
		
		        var input = $(this).parents('.input-group').find(':text'),
		            log = numFiles > 1 ? numFiles + ' files selected' : label;
		
		        if( input.length ) {
		            input.val(log);
		        } else {
		            if( log ) alert(log);
		        }
		
		    });
		});
	</script>