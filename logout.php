<?php
session_start();
while(list($key) = each($_SESSION)){
	session_unset($_SESSION[''.$key.'']);
}

session_destroy();
header("Location: index.php");
?>