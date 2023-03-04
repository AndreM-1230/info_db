<?php
session_start();
	global $db;
include('env.php');
include ('functions.php');
$db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("set names utf8");


var_dump($_POST);
$data_type =[
    [
        'Целые числа',
        'Дробные числа',
        'Строки',
        ],[
        'INTEGER NOT NULL',
        'FLOAT NOT NULL',
        'VARCHAR(30) NOT NULL'
    ]
];
$table_name = $_POST['tablename'];
$table_struct = 'id INT(11) NOT NULL AUTO_INCREMENT, ';
foreach($_POST as $key => $value){
    if(is_int($key)){
        $cell_key = '#' . $key;
        foreach($data_type[0] as $data_key => $data_val){
            if(strcmp($data_val, $_POST[$cell_key]) == 0){
                $cell_key = $data_type[1][$data_key];
                break;
            }
        }
        $table_struct .=''. $value . ' ' . $cell_key . ', ';
    }
}
$table_struct .= 'PRIMARY KEY (id)';
echo $table_name . PHP_EOL;
echo $table_struct;
print_r($table_struct);

$sql = "CREATE TABLE $table_name ( $table_struct )";
//$db->exec($sql);

if($db -> query($sql))
{
    header("Location: index.php");
}
	else
    {
        echo "Ошибка" . $db->error;
    }