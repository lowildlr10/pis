<div class="modal fade" id="modal-print-1" role="dialog">
	<div class="modal-dialog modal-md">

	 	<!-- Modal content-->
	 	<div class="modal-content">
	 	  	<div class="modal-header">
	 	  	  	<button type="button" class="close" data-dismiss="modal">&times;</button>
	 	  	  	<h4 class="modal-title">Print Details</h4>
	 	  	</div>
	 	  	<div class="modal-body">
	     	  	<div align="left">
					<div style="margin-top: 12px;">
						<label>Select Chairperson:</label>
						<select class="form-control" name="chairman" id="chairman">
                            <option value="">--Blank--</option>
						<?php	 
							$qryEmps = $conn->query("SELECT * FROM tblsignatories 
													 WHERE active = 'yes' 
													 AND abs = 'y' 
													 AND signType = 'chairman'
													 OR signType = 'approval'
													 ORDER BY name ASC") or die(mysqli_error($conn));
							
							while ($data = $qryEmps->fetch_object()) {
								echo '<option value="'.$data->name.'"';
								echo '>'.$data->name.' [' . $data->position .']</option>';
							}
						?>	
						</select>
					</div>
					<div style="margin-top: 12px;">
						<label>Select Vice Chairperson:</label>
						<select class="form-control" name="vice" id="vice">
                            <option value="">--Blank--</option>
						<?php	
							$qryEmps = $qryEmps = $conn->query("SELECT * FROM tblsignatories 
													 WHERE active = 'yes' 
													 AND abs = 'y' 
													 AND signType = 'vice-chairman'
													 ORDER BY name ASC") or die(mysqli_error($conn));
							
							while ($data = $qryEmps->fetch_object()) {
								echo '<option value="'.$data->name.'"';
								echo '>'.$data->name.' [' . $data->position .']</option>';
							}
						?>	
						</select>
					</div>
					<div style="margin-top: 12px;">
						<label>Select 1st Member:</label>
						<select class="form-control" name="member1" id="member1">
                            <option value="">--Blank--</option>
						<?php	
							$qryEmps = $qryEmps = $conn->query("SELECT * FROM tblsignatories 
													 WHERE active = 'yes' 
													 AND abs = 'y' 
													 AND signType = 'member'
													 ORDER BY name ASC") or die(mysqli_error($conn));
							
							while ($data = $qryEmps->fetch_object()) {
								echo '<option value="'.$data->name.'"';
								echo '>'.$data->name.' [' . $data->position .']</option>';
							}
						?>	
						</select>
					</div>
					<div style="margin-top: 12px;">
						<label>Select 2nd Member:</label>
						<select class="form-control" name="member2" id="member2">
                            <option value="">--Blank--</option>
						<?php	
							$qryEmps = $qryEmps = $conn->query("SELECT * FROM tblsignatories 
													 WHERE active = 'yes' 
													 AND abs = 'y' 
													 AND signType = 'member'
													 ORDER BY name ASC") or die(mysqli_error($conn));
							
							while ($data = $qryEmps->fetch_object()) {
								echo '<option value="'.$data->name.'"';
								echo '>'.$data->name.' [' . $data->position .']</option>';
							}
						?>	
						</select>
					</div>
                    <div style="margin-top: 12px;">
                        <label>Select 3nd Member:</label>
                        <select class="form-control" name="member3" id="member3">
                            <option value="">--Blank--</option>
                        <?php   
                            $qryEmps = $qryEmps = $conn->query("SELECT * FROM tblsignatories 
                                                     WHERE active = 'yes' 
                                                     AND abs = 'y' 
                                                     AND signType = 'member'
                                                     ORDER BY name ASC") or die(mysqli_error($conn));
                            
                            while ($data = $qryEmps->fetch_object()) {
                                echo '<option value="'.$data->name.'"';
                                echo '>'.$data->name.' [' . $data->position .']</option>';
                            }
                        ?>  
                        </select>
                    </div>
					<div style="margin-top: 12px;">
						<label>Select End User:</label>
						<select class="form-control" name="enduser" id="enduser">
                            <option value="">--Blank--</option>
							<?php	
								$qryEmps = $conn->query("SELECT empID, concat(firstname,' ', left(middlename, 1),'. ',lastname) 
														 AS name, position 
														 FROM tblemp_accounts 
														 ORDER BY name ASC") or die(mysqli_error($conn));
								
								while ($data = $qryEmps->fetch_object()) {
									if ($data->empID == $_SESSION['log_empID']) {
										echo '<option value="'. strtoupper($data->name) .'" selected="selected"> ' .
										strtoupper($data->name) . ' </option>';
									} else {
										echo '<option value="'. strtoupper($data->name) .'"> ' .
										strtoupper($data->name) . ' </option>';
									}
								}
							?>
						</select>
					</div>
				</div>
			</div>
     	  	<div class="modal-footer">
     	  		<button id="btn-ok" type="button" class="btn btn-primary">
     	  			Save & Continue...
     	  			<span class="glyphicon glyphicon-arrow-right"></span>
     	  		</button>
     	  	  	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     	  	</div>
     	</div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-create-abstract" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90%;">
    
     	<!-- Modal content-->
     	<div class="modal-content">
     	  	<div class="modal-header">
     	  	  	<button type="button" class="close" data-dismiss="modal">&times;</button>
     	  	  	<h4 class="modal-title">Create Abstract</h4>
     	  	</div>
     	  	<div class="modal-body" id="div-add">
     	  		<h4>
     	  			<div id="display-pr-no" class="font-color-1"></div>
     	  		</h4>
     	  		<div style="text-align: left;">
	     	  		<label><strong>Abstract Date: </strong></label>
					<div class="form-group">
					    <div class="input-group date" id="txtDate">
					        <input type="text" class="form-control" name="txtDate" id="txtDate-val" 
					        	   value="<?php echo date('m/d/Y') ?>">
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
     	  		<div id="create-content" style="overflow: auto;
     	  										border: 3px #005e7c solid; 
     	  										border-radius: 10px; 
     	  										padding: 5px;
     	  										background: #005e7c;">
     	  			<br>
     	  			<div class="progress">
					  	<div class="progress-bar progress-bar-striped active" role="progressbar" 
							 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"> 
							    Loading... 
					  	</div> 
					</div>
     	  		</div>
     	  		<div class="form-group well" style="text-align: left; margin-top: 20px; background-color: #e1eff3;">
     	  			<label>Uploaded Custom Canvass Form/s:</label>
	     	  		<div style="margin-top: 15px;
							    display: flex;
							    overflow: auto;">
						<?php

						if (is_dir($dir . 'uploads/canvass/' . $_prNo) == false ) {
						    mkdir($dir . 'uploads/canvass/' . $_prNo, 0700);
						}

						$listDir = scandir($dir . 'uploads/canvass/' . $_prNo);

						if ($listDir) {
							foreach ($listDir as $file) {
					    		if ($file != ".." && $file != ".") {
					    			echo '<a class="btn btn-default btn-sm" href="../../../uploads/canvass/' . 
					    				$_prNo . '/' . $file . '" target="_blank" style="width: 7em;
																					     border-radius: 1em;
																					     margin: 3px;
																					     border: 2px #005e7c solid;">
					    				<span class="glyphicon glyphicon-file"></span>' . 
					    				$file . 
					    			'</a>';
					    		}
					    	}
						}
						
						?>
					</div>
				</div>
     	  	</div>
     	  	<div class="modal-footer">
     	  		<button id="btn-save" type="button" class="btn btn-primary">
     	  			<span class="glyphicon glyphicon-save"></span>
     	  			Save
     	  		</button>
     	  	  	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     	  	</div>
     	</div>
      
    </div>
</div>