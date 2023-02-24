<?php
session_start();
global $db;
include('env.php');
echo $_GET['arr_sort'];
$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("set names utf8");
$sort= 'id';
foreach($_SESSION['array_struct'] as $key => $value){
    if($key == ($_GET['arr_sort'] -1)){
        $sort = $value['Field'];
        break;
    }
}
echo $sort;
$_SESSION['pos_id'] = $sort;
$_SESSION['page']=1;
header("Location: index.php");