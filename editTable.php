<?php
    session_start();
    $sql_tables = [];
    foreach (sqltab("SHOW TABLES") as $table) {
        $key = array_keys($table)[0];
        $sql_tables[] = $table[$key];
    }

    echo getTableButtons($sql_tables, $_SESSION['active_table']);

    if(!isset($_SESSION['selsize'])){
        $_SESSION['selsize'] = 30;
    }

    $pagenumber=1;
    $firstrow=$_SESSION['selsize']*($_SESSION['page']-1);

    $sql_select = sqltab("SELECT * FROM $_SESSION[active_table] $_SESSION[where] ORDER BY $_SESSION[pos_id] DESC LIMIT $firstrow, $_SESSION[selsize]");

    echo getTable($sql_select, $_SESSION['array_struct'], $_SESSION['active_table']);
    echo Pages($_SESSION['array_count'], $_SESSION['selsize'], $_SESSION['page']);
