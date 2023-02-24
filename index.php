<?php
session_start();
include('env.php');
include('functions.php');
//define("_INC", 1);
ini_set("memory_limit","6000M");
ini_set('mysql.connect_timeout', 7200); // таймаут соединения с БД (сек.)
ini_set('max_execution_time', 7200);    // таймаут php-скрипта
ini_set('display_errors','ON');
//error_reporting('E_ALL');

if($_SESSION['pos_id'] == NULL){
    $_SESSION['pos_id'] = 'id';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информационная база данных производства</title>

    <link href="./images/db_logo.ico" type="image/x-icon"                          rel="icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <script src="./js/jquery.min.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script type="text/javascript">
        /*$(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        });*/

        function tablesort() {
    let arr_sort=document.getElementById('tablesort').selectedIndex;
    location.href = './arrSort.php?arr_sort='+ arr_sort;
    console.log(arr_sort);
    }
    </script>
</head>
<body>
<div class="navbar navbar-expand-lg navbar-default bg-dark text-white">
    <!--    navbar-fixed-top-->
    <div class="container">
        <div class="navbar-header">
            <a href="">
                <img id="pnglogo"
                     src="./images/db_logo.png"
                     width="64"
                     height="64"
                     alt="Информационная база данных производства"/></a>
        </div>

        <div class="navbar-header">
            <a href="" class="navbar-brand">Заказы предприятия</a>
        </div>

        <div class="navbar-collapse collapse" style="color: #badbcc !important;" id="navbar-main">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#"
                       class="dropdown-toggle"
                       data-toggle="dropdown"
                       role="button"
                       aria-expanded="false">
                        <span class="glyphicon glyphicon-th-list">
                        </span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#">...</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">...</a>
                        </li>
                        <li>
                            <a href="#">...</a>
                        </li>
                        <li>
                            <a href="#">...</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">...</a>
                        </li>
                        <li>
                            <a href="#">...</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="#" class="link-light">Справочный запрос</a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle"
                       data-toggle="dropdown"
                       href="#"
                       id="manufacture">
                        <span class="glyphicon glyphicon-tasks"> Производство
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu"
                        aria-labelledby="manufacture">
                        <li><a href="#">...</a></li>
                        <li><a href="#">...</a></li>
                        <li><a href="#">...</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle"
                       data-toggle="dropdown"
                       href="#"
                       id="manufacture">
                        <span class="glyphicon glyphicon-list-alt"> НКЦ
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu"
                        aria-labelledby="manufacture">
                        <li><a href="#">...</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Личный кабинет</a></li>
                <li><a href="#">Справка</a></li>
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
    /*if($_GET["action"] == ""){
        $_GET["action"] = "index";
    }
    if($_GET["action"] = "index") {*/
        include('editTable.php');
    /*}*/
    ?>

    <div class="clearfix"></div>
    <!--
    <div class="jumbotron" style="min-height: 300px; background-color: transparent;">
        &nbsp;
    </div>
    <div class="row">
        <div class="alert alert-dismissible alert-success col-lg-offset-1 col-lg-6">
        </div>
    </div>
    -->
</div>
</body>
</html>
