<?php
session_start();
include('env.php');
include('functions.php');
// array_keys($_POST)[0] - id строки;
$id =  array_keys($_POST)[0];
$fixer = $_SESSION['account'][0]['id'];
sqltab("UPDATE task_list SET admin = $fixer, status = 2 WHERE id = $id");
header('Location:task.php');
