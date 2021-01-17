<?php
class db_operation{
	var $tbl;
	var $tbl_col;
	var $tbl_val;

	function initialize($db_table){
		$this->tbl = $db_table;
	}
	function insert($atts,$con){
		if(is_array($atts)){
			while(list($col, $val) = each($atts)){
				if($val==""){
					continue;
				}
				$this->tbl_col .= $col . ",";
				if(is_int($val) || is_double($val)){
					$this->tbl_val .= $val . ",";
				}else{
					$this->tbl_val .= "'".$val."',";
			}
			$query = "INSERT INTO ".$this->tbl." (".$this->tbl_col.") VALUES (".$this->tbl_val.")";
			$query = str_replace(",)",")",$query); 
		}//end while	
		$con->query($query) or die(mysqli_error($con));
		}else{
			echo "Insert attributes not allowed.";
		}
	}
	function update($atts,$where,$con){
		$str="";
		if(is_array($atts)){
			while(list($col, $val) = each($atts)){
				//if($val == ""){
					//continue;
				//}
				if(is_int($val) || is_double($val)){
					$str .= "$col=$val,";
				}elseif($val=="NULL" || $val=="null"){
					$str .= "$col=NULL,";
				}else{
					$str .= "$col='$val',";
				}
			}
			$str = substr($str,0,-1);
			$query = "UPDATE ".$this->tbl." SET ".$str."";
			if(!empty($where)){
				$query .= " WHERE ".$where."";
			}
			$con->query($query) or die (mysqli_error($con));
		}else{
			echo "Update attributes not allowed.";
		}
	}
}
?>