<div class="modal fade" id="modal-print" role="dialog">
    <div class="modal-dialog modal-lg">
     	<!-- Modal content-->
     	<div class="modal-content">
     	  	<div class="modal-header">
     	  	  	<button type="button" class="close" data-dismiss="modal">&times;</button>
     	  	  	<h4 class="modal-title">Print Details</h4>
     	  	</div>
     	  	<div class="modal-body">
     	  		<div class="row">
     	  			<div class="col-md-12" style="overflow: auto;">
   	     	  			<div class="col-md-3">
   	     	  				<div class="form-group" >
   	     	  					<label for="txtIncreaseSize">Increase/Decrease Text Size (%): [Default = 0%]</label>
   	     	  					<input id="txtIncreaseSize" name="txtIncreaseSize" class="form-control" type="number" value="0">
   	     	  				</div>
   	     	  				<div class="form-group" >
   	     	  					<label for="selPaperSize">Select Paper Size:</label>
   	     	  					<select id="selPaperSize" class="form-control">
   	     	  						<option value="1">A4</option>
   	     	  						<option value="2">Short</option>
   	     	  						<option value="3" <?php echo $page == "abstract" ? ' selected="selected"':''; ?>>Long</option>
   	     	  					</select>
   	     	  				</div>
   	     	  			</div>
   	     	  			<div class="col-md-9" style="padding: 0;">
   	     	  				<iframe id="print-content" width="100%" height="600" frameborder="0" 
   	     	  						marginwidth="0" marginheight="0" 
   	     	  						style="border: 2px solid #4577b4; border-radius: 4px;">
                                
   			     	  	  	</iframe>
   	     	  			</div>
   	     	  		</div>
     	  		</div>
     	  	</div>
     	  	<div class="modal-footer">
     	  		<button id="btn-print" type="button" class="btn btn-primary">
                    <span class="glyphicon glyphicon-save-file"></span>
                    Download
                </button>
     	  	  	<button type="button" id="btn-exit" class="btn btn-default" data-dismiss="modal">Cancel</button>
     	  	</div>
     	</div>
    </div>
</div>