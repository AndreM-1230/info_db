<?php
session_start();
include('env.php');
include('functions.php');
var_dump($_POST);
$id =  array_keys($_POST)[0];
sqltab("UPDATE task_list SET rating = $_POST[$id] WHERE id = $id");
header('Location:task.php');