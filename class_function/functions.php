<?php

function display_pages($dbcon, $sql,$colCount,$accessPage,$perPage,$misc=""){
	$arrayPerPage = array("30", "50", "100", "250", "500");
	$countRes = $dbcon->query($sql);
	$next = 0;
	$sub = $perPage - 1;
	$total = $countRes->fetch_object();
	$totalRec = $total->totalBut; 
		
	if ($totalRec) {
		$butCount = $totalRec / $perPage + 1;
		echo '<tr><td class="pages" colspan = "'.$colCount.'" align="left">';		
				  
		if (isset($total)) {
		 	$dis1 = $accessPage * $perPage - $sub;
			$dis2 = $accessPage * $perPage;
			
			if ($dis2 > $totalRec) {
				$dis2 = $totalRec;
			}

			echo '<div class="col-xs-12 col-sm-12 col-md-12" style="float:left; padding: 0; margin: 0;">
					  <label>Displaying: '.$dis1.'-'.$dis2.' of '.$totalRec.'</label>
				  </div>';

			echo '<div class="col-xs-12 col-sm-12 col-md-12" style="float:left; padding: 0;>
				  <div class="col-xs-3 col-sm-3 col-md-3">
					  <form name="frmPerPage" method="post" class="form-inline">
					  	  <div class="form-group">
						  <label for="txtPerPage" class="control-label">Display Per Page: </label>
						  <select id="txtPerPage" name="txtPerPage" class="form-control font-color-1" onchange="this.form.submit();">';

			foreach ($arrayPerPage as $key => $value) {
				echo '<option value="'.$value.'"';
				echo $value == $perPage ? ' selected="selected"':'';
				echo '>'.$value.' </option>';
			}		  	

			echo '</select></div></form></div></div>';

			echo '<div class="col-xs-12 col-sm-12 col-md-12" style="float:left; padding: 0; margin: 0;">
					  <center><ul class="pagination" style="margin: 10px;">';
		}
		
		if (isset($butCount) && $butCount >= 2) {

			if ($accessPage != 1) {
				$prev = $accessPage - 1;
				echo '<li><a href="'.$_SERVER['PHP_SELF'].'?limit='.$prev.''.$misc.'">&laquo; previous</a>&nbsp;</li>';
			}

			for ($i = 1; $i < $butCount; $i++){ 
				if ($i != $accessPage) {
					echo '<li><a href = "'.$_SERVER['PHP_SELF'].'?limit='.$i.''.$misc.'">'.$i.'</a></li>';
				} else {
					echo '<li class="active"><a href="#">'.$i.'</a></li>';
					$next = $i+1;
				}
			}

			if ($next < $butCount) {
				echo '<li><a href="'.$_SERVER['PHP_SELF'].'?limit='.$next.''.$misc.'">next &raquo;</a></li>';
			}
		}

		echo '</ul></center></div>';
		echo '</div></td></tr>';
	}
}

?>