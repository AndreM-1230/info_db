<?php
session_start();
if($_SESSION['account'] == null){
    include('../env.php');
    include('../functions.php');
    $login = $_POST['login'];
    $password = $_POST['password'];
    $user = sqlacc("SELECT * FROM users
             WHERE 
            (login = '$_POST[login]') and
            (password = '$_POST[password]')");
    var_dump($_POST);
    var_dump($user);
    $_SESSION['account'] = $user;
}
else{
    session_unset();
}
header('Location: ../index.php');
