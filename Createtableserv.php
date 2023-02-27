<?php
	global $db;
include('env.php');
/*$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("set names utf8");
$keys= implode(",", array_keys($_POST['data']));

$addSlashes = function ($string){
    return "'". addslashes($string) . "'";
};*/
var_dump($_POST);
/*$values = implode(",", array_map( $addSlashes, $_POST['data']) );
$sql = "INSERT INTO $_POST[table] ($keys) VALUES ($values)";
if($db -> query($sql))
{
    header("Location: index.php");
}
	else
    {
        echo "Ошибка" . $db->error;
    }*/