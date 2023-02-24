<?php
//ВЫБОР ТАБЛИЦЫ
	session_start();
	$_SESSION['where'] = '';
	global $db;
	include('env.php');
	$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("set names utf8");
	$value=$_POST ['tbl'];
	$res = $db->prepare("SHOW COLUMNS FROM $value");
	$res->execute();
	$array=$res->fetchAll(PDO::FETCH_ASSOC);
	$sql="SELECT COUNT(*) FROM $value";
	$res = $db->query($sql);
	$count = $res->fetchColumn();
    $_SESSION['pos_id'] = 'id';
	$_SESSION['array_count']=$count;
	$_SESSION['active_table'] = $value;
	$_SESSION['array_struct']= $array;
	$_SESSION['page']=1;
	header("Location: index.php");