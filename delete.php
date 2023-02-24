<?php
session_start();
$_SESSION["newsession"]='';

	if(isset($_POST["id"]))
	{	
		global $db;
		include('env.php');
		$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("set names utf8");
		$id=$_POST ['id'];
		$act_table=$_SESSION['active_table'];
		try
		{	
			$sql = "DELETE FROM $act_table WHERE id = '$id'";
		}
		catch(Exception $e) 
		{
			echo "Ошибка";
		}
        header("Location: index.php");
	}
	
?>