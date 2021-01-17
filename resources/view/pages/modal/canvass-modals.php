<div class="modal fade" id="modal-print-1" role="dialog">
	<div class="modal-dialog modal-lg">
	 	<div class="modal-content">
	 	  	<div class="modal-header">
	 	  	  	<button type="button" class="close" data-dismiss="modal">&times;</button>
	 	  	  	<h4 class="modal-title">Print Details</h4>
	 	  	</div>
	 	  	<div class="modal-body">
				<div style="margin-top: 12px; text-align: left;">
					<label><strong>Canvass Date: </strong></label>
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
				<div style="margin-top: 20px; text-align: left;">
					<label><strong>Signatory: </strong></label>
					<select class="form-control" name="selApp" id="selApp">';
						<?php
					    	$signApp = "53";
							$qryEmps = $conn->query("SELECT * FROM tblsignatories 
													 WHERE active = 'yes' 
													 AND rfq = 'y' 
													 ORDER BY name ASC") or die(mysqli_error($conn));
							
							while ($data = $qryEmps->fetch_object()) {
								echo '<option value="'.$data->signatoryID.'"';
								echo $data->signatoryID == $signApp ? ' selected="selected"':'';
								echo '>'.$data->name.' [' . $data->position .']</option>';
							}
						?>
					</select>
				</div>

				<br>

	     	  	<div id="canvass-content" style="overflow: auto;">
	     	  		<h3 class="font-color-1">Loading...</h3>
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