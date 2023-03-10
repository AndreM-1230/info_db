<?php
//ВЫБОР СТРАНИЦЫ
    session_start();
    $_SESSION['page'] = number_format($_POST['page'], 0);
    header("Location: ../info_db/index.php");
