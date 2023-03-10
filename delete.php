<?php
session_start();
$_SESSION["newsession"]='';

	if(isset($_POST["id"]))
	{	
		global $db_info;
		include('env.php');
		$db_info = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
        $db_info->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_info->exec("set names utf8");
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