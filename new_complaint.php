<?php
session_start();
include('env.php');
include('functions.php');
$user = $_SESSION['account'][0]['id'];
sqltab("INSERT INTO task_list
    (complaint_text, status, user)
    VALUES ('$_POST[text]', '1' , '$user')");
header('Location:task.php');
