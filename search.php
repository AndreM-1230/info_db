<?php
	session_start();
	global $db;
	include('env.php');
	$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("set names utf8");
    if($_POST['data'] == NULL){
    	header("Location: index.php");
    }
    $where=[];
    foreach ($_POST['data'] as $key => $value){
    	if($value != NULL)
    	{	
    		$where[] = $key."='".$value."'";
    	}
    }
    //$sql="SELECT * FROM $_SESSION[active_table] WHERE $where";
    $_SESSION['where'] = ' WHERE '. implode(" AND ", $where);
    header("Location: index.php");
    
    //print_r($where);
?>