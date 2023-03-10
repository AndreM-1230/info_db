<?php
    session_start();
	global $db_info;
	include('env.php');
	$db_info = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
    $db_info->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_info->exec("set names utf8");
 	$keys= implode(",", array_keys($_POST['data']));
 	
 	$addSlashes = function ($string){
 		return "'". addslashes($string) . "'";
 	};
	$values = implode(",", array_map( $addSlashes, $_POST['data']) );
	$sql = "INSERT INTO $_POST[table] ($keys) VALUES ($values)";
    header("Location: index.php");