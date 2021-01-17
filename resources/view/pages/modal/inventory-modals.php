<!-- Modal -->
<div class="modal fade" id="modal-print-1" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 80%;">
    
     	<!-- Modal content-->
     	<div class="modal-content">
     	  	<div class="modal-header">
     	  	  	<button type="button" class="close" data-dismiss="modal">&times;</button>
     	  	  	<h4 class="modal-title">Print Details 
                    <strong>
                        <button id="btn-reload" class="btn btn-default btn-sm">
                            <span class="glyphicon glyphicon-refresh"></span>
                        </button>
                    </strong>
                </h4>
     	  	</div>
     	  	<div class="modal-body">
     	  		<div id="print-content-1" style="overflow: auto;">
     	  			<h3 class="font-color-1">Loading...</h3>
     	  		</div>
     	  	</div>
     	  	<div class="modal-footer">
     	  		<button id="btn-ok" type="button" class="btn btn-primary">
     	  			Save & Continue...
	 	  			<span class="glyphicon glyphicon-arrow-right"></span>
     	  		</button>
     	  	  	<button id="btn-exit" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     	  	</div>
     	</div>
      
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-print-2" role="dialog">
    <div class="modal-dialog modal-sm" style="min-width: 20%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Print Document</h4>
            </div>
            <div class="modal-body">
                <div id="print-content-2" style="overflow: auto;">
                    <!--
                    <h3 class="font-color-1">Loading...</h3>
                    -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-print-3" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 80%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Manual Adding of Inventory Item/s</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 font-color-1"  style="text-align: left;">
                        <label>Inventory Classification</label>
                        <select id="sel-classification" class="form-control font-color-1">
                            <option value="" selected="selected"> -- Please select first a classification -- </option>
                            <option value="par"> Property Acknowledgement Receipt (PAR) </option>
                            <option value="ris"> Requisition and Issue Slip (RIS) </option>
                            <option value="ics"> Inventory Custodian Slip (ICS) </option>
                        </select>
                    </div>
                </div>
                <hr>
                <div id="print-content-3" style="overflow: auto;">
                    <!--
                    <h3 class="font-color-1">Loading...</h3>
                    -->
                </div>
             </div>
            <div class="modal-footer">
                <button id="btn-add" type="button" class="btn btn-primary">
                    <span class="glyphicon glyphicon-floppy-disk"></span>
                    Save
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>