<?php
session_start();
include('env.php');
include('functions.php');
include('forms.php');
include('pages/pages.php');
//define("_INC", 1);
ini_set("memory_limit","6000M");
ini_set('mysql.connect_timeout', 7200); // таймаут соединения с БД (сек.)
ini_set('max_execution_time', 7200);    // таймаут php-скрипта
ini_set('display_errors','ON');
//error_reporting('E_ALL');
if(!isset($_SESSION['account']))
    $_SESSION['account'] = null;
if(!isset($_SESSION['active_table']))
    $_SESSION['active_table'] = null;
if($_SESSION['pos_id'] == NULL)
    $_SESSION['pos_id'] = 'id';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информационная база данных производства</title>

    <link href="./images/db_logo.ico" type="image/x-icon"                          rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/rating.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <script src="./js/jquery.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
    <script type="text/javascript" src="pages/pages.js"></script>
    <script type="text/javascript">


    </script>
</head>
<body>
<div class="navbar navbar-expand-lg navbar-default bg-dark text-white">
    <!--    navbar-fixed-top-->
    <div class="container">
        <div class="navbar-header">
            <a href="index.php">
                <img id="pnglogo"
                     src="./images/db_logo.png"
                     width="64"
                     height="64"
                     alt="Информационная база данных производства"/></a>
        </div>

        <div class="navbar-header">
            <a href="index.php" class="navbar-brand text-light">БД</a>
        </div>

        <div class="navbar-collapse collapse" style="color: #badbcc !important;" id="navbar-main">

            <ul class="nav navbar-nav navbar-right">
                <?php
                if($_SESSION['account'] == null){
                    echo '<li><input type="button"
                                class="btn btn-primary"
                                 data-bs-toggle="modal"
                                 name="sign"
                                  data-bs-target="#my_personal_form" value="Войти"/></li>';
                }
                else{
                    echo '<li><a class="navbar-brand text-light">Привет, '.$_SESSION["account"][0]["login"].'!</a></li>
                          <li><a href="account/check_sign.php" class="navbar-brand text-light">Выйти</a></li>';
                }
                ?>
                <li><a href="#" class="navbar-brand text-light">Личный кабинет</a></li>
                <li><a href="task.php" class="navbar-brand text-light">Задачи</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container">
    <div class="page-header" id="banner">
        <div class="row">
            <div class="col-lg-12">
                <h1>Информационная база данных производства</h1>
            </div>
        </div>
    </div>
    <?php
    if($_SESSION['account'] != null)
        include('editTable.php');
    ?>

    <div class="clearfix"></div>
</div>
</body>
</html>
