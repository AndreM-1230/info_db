<?php
	global $db_info;
	include('env.php');
	$db_info = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
    $db_info->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_info->exec("set names utf8");
 	$str=[];
 	foreach ($_POST['data'] as $key => $value) {
 		$str[]=$key."='".$value."'";
 	}
 	$sql="UPDATE $_POST[table] SET ". implode(', ', $str) ." WHERE id=$_POST[id]";

     header("Location: index.php");

	
