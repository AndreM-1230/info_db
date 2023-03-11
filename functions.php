<?php

    function sql_connect(): void
    {
        global $db_info, $db_acc ;
        try {
            $db_info = new PDO("mysql:host=".$_ENV['db_connection']['host'].";
            dbname=".$_ENV['db_connection']['db'],
                $_ENV['db_connection']['username'],
                $_ENV['db_connection']['password']);
            $db_acc = new PDO("mysql:host=".$_ENV['account_connection']['host'].";
            dbname=".$_ENV['account_connection']['db'],
                $_ENV['account_connection']['username'],
                $_ENV['account_connection']['password']);
            $db_info->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db_acc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db_info->exec("set names utf8");
            $db_acc->exec("set names utf8");
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

function sql_length($sql) :int
{
    global $db_acc;
    $res = $db_acc->query($sql);
    return $res->fetchColumn();
}

    function sqlacc ($sql): array
    {
        global $db_acc;
        $arr = array();
        try {
            $sth = $db_acc->prepare($sql);
            $sth->execute();
            $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e){
            var_dump($e);
        }
        return ($arr);
    }


    function sqltab ($sql): array
    {
        global $db_info;
        $arr = array();
        try {
            $sth = $db_info->prepare($sql);
            $sth->execute();
            $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e){
            var_dump($e);
        }
        if($_SESSION['active_table'] == NULL){
            $_SESSION['active_table'] = $arr[0][0];
            echo $arr[0][0];
        }
        return ($arr);
    }
    function sqlupd ($sql): int
    {
        global $db_info;
        $insert_id = 0;
        try {
            $sth = $db_info->prepare($sql);
            $sth->execute();
            $insert_id = $db_info->lastInsertId();
        } catch(Exception $e) {
            ExceptionCatcher($e);
        }
        return $insert_id;
    }
    sql_connect(); // соединение с базой.
    // Условия по GET-запросам

function get_task_list_data(): array
{
    if(!isset($_SESSION['complaints_type']))
        $_SESSION['complaints_type'] = 1;
    if(!isset($_SESSION['selsize']))
        $_SESSION['selsize']=30;
    $firstrow=$_SESSION['selsize']*($_SESSION['page']-1);
    if(!isset($_SESSION['complaints_type']))
        $_SESSION['complaints_type']=1;
    if($_SESSION['complaints_type'] == 2){
        $user = $_SESSION['account'][0]['id'];
        $_SESSION['array_count']= sql_length("SELECT COUNT(*) FROM task_list WHERE user = $user");
        $task_arr=sqlacc("SELECT * FROM task_list WHERE user = $user ORDER BY id DESC LIMIT $firstrow, $_SESSION[selsize]");
    }
    else{
        $_SESSION['array_count']= sql_length("SELECT COUNT(*) FROM task_list");
        $task_arr=sqlacc("SELECT * FROM task_list ORDER BY id DESC LIMIT $firstrow, $_SESSION[selsize]");
    }
    return $task_arr;
}

function complaint_list():string
{
    if($_SESSION['account'][0]['user_stat'] != 0) {
        switch ($_SESSION['complaints_type']) {
            case 1:
                $btn_all = 'btn-primary disabled';
                $btn_user = 'btn-outline-primary';
                break;
            case 2:
                $btn_all = 'btn-outline-primary';
                $btn_user = 'btn-primary disabled';
                break;
            default:
                $btn_all = '';
                $btn_user = '';
                break;
        }
    }
    $complaints_arr=get_task_list_data();
    $return ="<div class='page-header' id='banner'>
                        <div class='row'>
                            <div class='col-lg-12'>";
    if($_SESSION['account'][0]['user_stat'] != 0){
        $return .=" <div class='btn-group' role='group' aria-label='Basic outlined example'>
                    <input class='btn $btn_all'
                     type='submit'
                      id='com_sel'
                       aria-current='page'
                        value='Показать все'
                         onclick='com_sel(1)'/>
                    <input class='btn $btn_user'
                     type='submit'
                      id='com_sel'
                       aria-current='page'
                        value='Показать мои'
                         onclick='com_sel(2)'/>
                    <input class='btn btn-outline-primary'
                     type='button'
                      data-bs-toggle='modal'
                      name='create_complaint'
                       data-bs-target='#my_personal_form' value='Создать жалобу'/>
                  </div>";
    }else{
        $return .=" <h1>Жалобы?</h1>";
    }

    $return .="</div>
                        </div>
                  </div>
                  <table class='table
                         table-hover
                         table-borderless
                         align-middle col-lg-12' data-tblname='tbl' style='text-align:center;'><tbody>
            <tr class='table-dark'>
                <td class='col-lg-1'>#</td>
                <td class='col-lg-3'>Содержание:</td>
                <td class='col-lg-1'>Статус:</td>
                <td class='col-lg-1'>Пользователь:</td>";
    if($_SESSION['account'][0]['user_stat'] == 0){
        $return .="<td class='col-lg-1'>Фиксер:</td>";
    }
    else{
        $return .="<td class='col-lg-1'>Оценка ответа:</td>";
    }
    $return .="<td class='col-lg-3'>Ответ:</td></tr>";
    foreach($complaints_arr as $value){
        $user = sqlacc("SELECT login FROM users WHERE id = $value[user]");
        if(!isset($value['admin'])){
            $fixer = sqlacc("SELECT login FROM users WHERE id = $value[admin]");
        }
        $status = sqlacc("SELECT status_name FROM status WHERE id = $value[status]");

        $return .="<tr>
                <td>". $value['id'] ."</td>";
        $return .="<td style='max-width: 45ch !important;'>";
        if(strlen($value['complaint_text']) > 30){
            $accordionID = 'acc' . $value['id'] . 'id';
            $data_accordionID = '#' . $accordionID;

            $collapse_accordion = 'coll' . $value['id'] . 'collapse';
            $data_collapse = '#' . $collapse_accordion;

            $heading_accordion = 'head' . $value['id'] . 'heading';

            $data_length =mb_substr($value['complaint_text'], 0, 30);

            $return .= accordion($accordionID,
                $data_accordionID,
                $collapse_accordion,
                $data_collapse,
                $heading_accordion,
                $data_length,
                $value['complaint_text']);
        }
        else{
            $return .=$value['complaint_text'];
        }
        $return .="</td>";

        switch ($status[0]['status_name']){
            case 'Ожидает':
                $return .="<td class='table-light'>" .  $status[0]['status_name'] ." </td>";
                break;
            case 'В работе':
                $return .="<td class='table-info'>" .  $status[0]['status_name'] ." </td>";
                break;
            case 'Выполнено':
                $return .="<td class='table-success'>" .  $status[0]['status_name'] ." </td>";
                break;
        }
        $return .="<td>" .  $user[0]['login'] ." </td>";
        if($_SESSION['account'][0]['user_stat'] == 0){
            if(!isset($fixer[0]['login'])){
                $return .="<td><form action='take_task.php' method='post'>
                                    <input type='submit'
                                    name='$value[id]'
                                    class='btn btn-outline-warning'
                                    value='Взять'/>
                                </form></td>";
            }
            else{
                $return .="<td>" .  $fixer[0]['login'] ." </td>";
            }
        }
        elseif($value['fix_comment'] != NULL){
            $return .="<td>";

            if(!isset($value['rating'])){
                if($_SESSION['account'][0]['user_stat'] == 1 and
                    $user[0]['login'] == $_SESSION['account'][0]['login'])
                {
                    $rating = null;
                    $return .= 'Оцените ответ: ' . rating($rating);
                }
            }
            else{
                $return .="$value[rating]";
            }

            $return .= "</td>";
        }

        $return .="<td style='max-width: 45ch !important;'>";


        if($value['fix_comment'] != NULL){
            if(strlen($value['fix_comment']) > 30){
                $accordionID = 'facc' . $value['id'] . 'id';
                $data_accordionID = '#' . $accordionID;

                $collapse_accordion = 'fcoll' . $value['id'] . 'collapse';
                $data_collapse = '#' . $collapse_accordion;

                $heading_accordion = 'fhead' . $value['id'] . 'heading';

                $data_length =mb_substr($value['fix_comment'], 0, 30);

                $return .= accordion($accordionID,
                    $data_accordionID,
                    $collapse_accordion,
                    $data_collapse,
                    $heading_accordion,
                    $data_length,
                    $value['fix_comment']);
            }
            else{
                $return .=$value['fix_comment'] . "</br>";
            }

            if(isset($value['rating']) and $_SESSION['account'][0]['user_stat'] == 0){
                $return .="Рейтинг ответа : $value[rating]";
            }

        }
        else{
            if(isset($fixer[0]['login']) and $fixer[0]['login'] == $_SESSION['account'][0]['login']){
                $return .="<input type='button'
                                    id='$value[id]'
                                    name='$value[id]'
                                    class='btn btn-outline-warning'
                                    onclick='comment_form(this)'
                                    value='Ответить'/>";
            }

        }
        $return .="</td>";
        $return .="</tr>";
    }
    $return .="</tbody></table>";
    $return .= Pages($_SESSION['array_count'],$_SESSION['selsize'],$_SESSION['page']);
    return $return;
}

function accordion($accordionID,
                   $data_accordionID,
                   $collapse_accordion,
                   $data_collapse,
                   $heading_accordion,
                   $data_length,
                   $text):string
{
    return "<div class='accordion accordion-flush' id='$accordionID' style='max-width: 45ch !important;' >
                        <div class='accordion-item'>
                          <h2 class='accordion-header' id='$heading_accordion'>
                            <button class='accordion-button'
                             type='button' 
                             style='background-color: transparent;'
                             data-bs-toggle='collapse' 
                             data-bs-target='$data_collapse' 
                             aria-controls='$collapse_accordion'>
                              $data_length ...
                            </button>
                          </h2>
                          <div id='$collapse_accordion'
                          class='accordion-collapse collapse'
                          aria-labelledby='$heading_accordion'
                          data-bs-parent='$data_accordionID'
                          style=''>
                            <div class='accordion-body' style='text-align:left;'>$text</div>
                          </div>
                        </div>
                    </div>";
}



function rating($id):string
{   $idf = $id . 'fr';
    return  "<form action='set_rating.php' method='post' id='$idf'></form>
                <div class='star-rating'>
                <div class='star-rating__wrap'>
                <input class='star-rating__input' form='$idf' id='$idf-5' type='submit' name=$id value='5'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-5' title='5'></label>
                <input class='star-rating__input' form='$idf' id='$idf-4' type='submit' name=$id value='4'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-4' title='4'></label>
                <input class='star-rating__input' form='$idf' id='$idf-3' type='submit' name=$id value='3'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-3' title='3'></label>
                <input class='star-rating__input' form='$idf' id='$idf-2' type='submit' name=$id value='2'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-2' title='2'></label>
                <input class='star-rating__input' form='$idf' id='$idf-1' type='submit' name=$id value='1'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-1' title='1'></label>
                </div>
                </div>";
}





class MySqlTypeToHtmlType
        {
            private $htmlInputTypes = [
                    'text' => [
                        'varchar',
                        'char',
                        'text'
                    ],
                    'number' => [
                        'int',
                        'float',
                        'double',
                        'numeric'
                    ]
                ];

            private $MysqlTypeNeedle;

            public function __construct($MysqlTypeNeedle)
            {
                $this->MysqlTypeNeedle=$MysqlTypeNeedle;
            }

            public function ToHtmlType()
            {
                 foreach ($this->htmlInputTypes as $HtmlType => $types_array) {
                    foreach ($types_array as $MysqlType) {
                        if( strpos($this->MysqlTypeNeedle, $MysqlType) !== false ){
                            return $HtmlType;
                        }
                    }
                }
            }

            public function length(): string
            {
                return (preg_replace('/[^0-9]/', '', $this->MysqlTypeNeedle) ?: '');
            }

            public function sqltype()
            {
                foreach ($this->htmlInputTypes as $types_array) {
                    foreach ($types_array as $MysqlType) {
                        if( strpos($this->MysqlTypeNeedle, $MysqlType) !== false ){
                            return $MysqlType;
                        }
                    }
                }
            }
        }

        function getTableButtons($sql_tables, $active): string
        {
            //ВЫБОР ТАБЛИЦЫ
            $return = "
                <div style='text-align: center; margin-bottom: 10px'>
                    <input id='time_text' placeholder='Disabled input' disabled readonly type='text' style='visibility: hidden'>
                </div>
                <div style='text-align: center; margin-bottom: 10px'>
                    <table style='margin: auto'>
                        <tbody>
                            <tr>";
            foreach($sql_tables as $button){
                if($active == $button){
                    $return .= "
                        <td>
                            <form action='./choose.php' method='post'>
                                <input type='submit' name='tbl' class='btn btn-lg btn-primary' value='$button'>
                            </form>
                        </td>";
                } else {
                    $return .= "
                        <td>
                            <form action='./choose.php' method='post'>
                                <input type='submit' name='tbl' class='btn btn-lg btn-default' value='$button'>
                            </form>
                        </td>";
                }
            }
            $return .= "<td>
                            <form action='./createtable.php' method='post'>
                                <input type='submit' name='tbl' class='btn btn-lg btn-outline-success' value='Создать'>
                            </form>
                        </td>
                            </tr>
                        </tbody>
                    </table>
                </div>";
            return $return;
        }

        function CreateTable(): string
        {
            return "<form
                        method='post'
                        action='./Createtableserv.php'
                        id='idf'></form>
                <h3>Имя таблицы: <input name='tablename' form='idf' value=''/></h3>

                <table class='table table-striped' style='text-align:center;'><tbody>
                    
                    <h3>Поля таблицы:</h3>
                    <tr id='#0'>
                        <td>
                            <h3>id</h3>
                        </td>
                        <td>
                        <div class='btn-group' role='group' aria-label='Basic example'>
                            <button type='button' id='00' class='btn btn-outline-success' disabled>Вверх</button>
                            <button type='button' id='01' class='btn btn-outline-success' disabled>Вниз</button>
                            <button type='button' id='02' class='btn btn-outline-success' onclick='create_string(this)'>Добавить</button>
                        </div>
                        </td>
                    </tr>
                </tbody></table>
                <div><input type='submit' name='tbl' form='idf' class='btn btn-lg btn-outline-success' value='Создать'  /></div>";
        }

        function getHeader($array_struct): string
        {
            $return = "<tr>";
            foreach ($array_struct as $value) {
                $return .= "<th style='text-align:center'>" . $value['Field'] . "</th>";
            }
            $return .= "
                <th colspan='2' style='text-align:center'>Действие</th>
            </tr>";
            return $return;
        }

        function getDependency($table, $Field): array
        {
            $env = $_ENV['db_connection']['db'];
            return sqltab("
                SELECT
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME,
                    CONSTRAINT_NAME,
                    TABLE_NAME,
                    COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                    TABLE_SCHEMA = '$env' AND
                    TABLE_NAME='$table' AND
                    COLUMN_NAME='$Field'
            ");
        }

        function addingToTable($array_struct, $active): string
        {
            $return = "
                <tr>
                    <td>
                        <form
                            method='post'
                            action='./fetch_data.php'
                            id='idf'></form>
                        <input class='form-control' id='id' form='idf' name='id' disabled>
                        <input id='table' form='idf' name='table' value='$active' hidden/>
                    </td>";

            foreach ($array_struct as $value) {
                if($value['Field'] != 'id'){
                    $inputAttributes = new MySqlTypeToHtmlType( $value['Type'] );
                    $step = "";
                    $type=$inputAttributes->ToHtmlType();
                    $maxl=$inputAttributes->length();
                    if(
                        $inputAttributes->sqltype() == 'float' ||
                        $inputAttributes->sqltype() == 'double'
                    ){
                        $step = " step='0.001' ";
                    }
                    else if(
                        $inputAttributes->sqltype() == 'int'
                    ){
                        $step = "max='2147483648'";
                    }
                    else if(
                        $type == 'text'
                    ){
                        $step = "pattern='[A-Za-zА-Яа-яЁё0-9\s]{1,$maxl}' title='A-Z a-z А-Я а-я 0-9'";
                    }

                    if( $value['Key']=='MUL' ){
                        $dependence = getDependency($active, $value['Field']);
                        $ST = $dependence[0]['REFERENCED_TABLE_NAME'];
                        $secondary_table = sqltab("SELECT * FROM $ST ORDER BY id DESC");
                        $return .=  "
                            <td>
                                <select name='data[$value[Field]]' form='idf' class='form-control'>";
                        foreach ($secondary_table as $val) {
                            if($value == $val['id']){
                                $return .= "<option name='data[$value[Field]]' form='idf' value='$val[id]' title='$val[title]' selected>".  $val['id']  ."</option>";
                            }
                            else{
                                $return .= "<option name='data[$value[Field]]' form='idf' value='$val[id]'>".  $val['title']  ."</option>";
                            }
                        }
                        $return .=  "
                                </select>
                            </td>";
                    } else {
                        $return .=  "
                            <td>
                                <input
                                    class='form-control'
                                    type='$type'
                                    $step
                                    id='$value[Field]'
                                    form='idf'
                                    name='data[$value[Field]]'
                                    value='1'
                                    maxlength='$maxl'
                                    required>
                            </td>";
                    }
                }
            }
            $return .= "
                <td colspan='2'>
                    <input style='width: 100% !important;' type='submit' id='add1' form='idf' class='btn btn-outline-success' value='Добавить'>
                </td>
                </tr>";
            return $return;
        }

        function arrSort ($array_struct): string
        {
            //СОРТИРОВКА
            $return ="<tr><td><form
                            method='post'
                            action='./arrSort.php'
                            id='idsortform' ></form><select class='form-select' id='tablesort' onchange='tablesort()'>
            <option>Сортировка</option>";
            foreach($array_struct as $value){
                $return .="<option> {$value['Field']} </option>";

                //$return .= $value['Field'];
            }
            $return .="</select></td></tr>";

            return $return;
        }

        function getRows($row, $array_struct, $active, $form_id): string
        {
            //echo $rov . PHP_EOL . $array_struct . PHP_EOL . $active . PHP_EOL;
            //print_r($array_struct);
            $return = '';
            $flag = 0;
            foreach ($row as $Field => $value) {
                foreach ($array_struct as $value1) {
                    if ($value1['Field'] == $Field)
                    {
                        if($value1['Key']=='MUL')
                        {
                            $flag=1;
                        }
                        $inputAttributes = new MySqlTypeToHtmlType( $value1['Type'] );
                        break;
                    }
                }
                $step = "";
                $type=$inputAttributes->ToHtmlType();
                $maxl=$inputAttributes->length();
                if(
                    $inputAttributes->sqltype() == 'float' ||
                    $inputAttributes->sqltype() == 'double'
                ){
                    $step = " step='0.001' ";
                } else if(
                    $inputAttributes->sqltype() == 'int'
                ){
                    $step = "max='2147483648'";
                } else if(
                    $type == 'text'
                ){
                    $step = "pattern='[A-Za-zА-Яа-яёЁ0-9\s]{1,$maxl}' title='A-Z a-z А-Я а-я 0-9'";
                }
                if($flag==1){
                    $dependence = getDependency($active, $Field);//return $Field;
                    $ST = $dependence[0]['REFERENCED_TABLE_NAME'];
                    //$return .= $dependence[0][TABLE_NAME];
                    //selectpicker
                    $secondary_table = sqltab("SELECT * FROM $ST ORDER BY id DESC");
                    $return .= "<tr><td><select name='data[$Field]' form='$form_id' class='form-control' disabled>";
                    foreach ($secondary_table as $val) {
                        if($value == $val['id']){
                            $title=$val['title'];
                            $return .= "<option name='data[$Field]' form='$form_id' value='$val[id]' title='$val[title]' selected >".$title."</option>";
                        }
                        else{
                            $return .= "<option name='data[$Field]' form='$form_id' value='$val[id]' >{$val['title']}</option>";
                        }
                    }

                    $return .= "</select></td>";
                } else {
                    $return .= "
                        <td>
                            <input
                            name='data[$Field]'
                            type='$type'
                            $step
                            form='$form_id'
                            class='$Field
                            form-control'
                            value='$value'
                            maxlength='$maxl'
                            disabled/>
                        </td>";
                }
            }
            return $return;
        }

        function buttonChange($active, $row_id, $form_id): string
        {
            return "<td>
                    <form action='./edit.php' method='post' id='$form_id'></form>
                    <input form='$form_id' name='table' value='$active' hidden/>
                    <input name='id' form ='$form_id' value='$row_id' hidden/>
                    <input
                        style='width: 100% !important;'
                        type='button'
                        class='btn btn-outline-warning'
                        value='Редактировать'
                        onclick='buttoned(this)'/>
                </td>";
        }

        function buttonDelete($row_id): string
        {
            return "<td>
                    <form action='./delete.php' method='post'>
                        <input type='hidden' name='id' value='$row_id'>
                        <input style='width: 100% !important;' type='submit' class='btn btn-outline-danger' value='Удалить'>
                    </form>
                </td></tr>";
        }

        function getSearchBar($array_struct, $active): string
        {
            $return = "
                <tr>
                    ";

            foreach ($array_struct as $value) {

                    $inputAttributes = new MySqlTypeToHtmlType( $value['Type'] );
                    $step = "";
                    $type=$inputAttributes->ToHtmlType();
                    $maxl=$inputAttributes->length();
                    if(
                        $inputAttributes->sqltype() == 'float' ||
                        $inputAttributes->sqltype() == 'double'
                    ){
                        $step = " step='0.001' ";
                    }
                    else if(
                        $inputAttributes->sqltype() == 'int'
                    ){
                        $step = "max='2147483648'";
                    }
                    else if(
                        $type == 'text'
                    ){
                        $step = "pattern='[A-Za-zА-Яа-яЁё0-9\s]{1,$maxl}' title='A-Z a-z А-Я а-я 0-9'";
                    }

                    if( $value['Key']=='MUL' ){
                        $dependence = getDependency($active, $value['Field']);
                        $ST = $dependence[0]['REFERENCED_TABLE_NAME'];
                        $secondary_table = sqltab("SELECT * FROM $ST ORDER BY id DESC");
                        $return .=  "
                            <td>
                                <select name='data[$value[Field]]' form='searchf' class='form-control'>
                                    <option name='data[$value[Field]]' form='searchf' value='$value[id]'></option>";
                        foreach ($secondary_table as $val) {
                            $return .= "<option name='data[$value[Field]]' form='searchf' value='$val[id]'>{$val['title']}</option>";
                        }
                        $return .=  "
                                </select>
                            </td>";
                    } else {
                        $return .=  "
                            <td>
                                <input
                                    class='form-control'
                                    type='$type'
                                    $step
                                    id='$value[Field]'
                                    form='searchf'
                                    name='data[$value[Field]]'
                                    maxlength='$maxl'>
                            </td>";

                    }

            }
            $return .= "
                <td colspan='2'>
                <form
                                method='post'
                                action='./search.php'
                                id='searchf'></form>
                    <input style='width: 100% !important;' type='submit' id='add1' form='searchf' class='btn btn-outline-primary' value='Поиск'>
                </td>
                </tr>";
            return $return;

        }

        function getTable($sql_select, $array_struct, $active): string
        {
            $return  = "<table class='table table-striped' data-tblname='tbl' style='text-align:center;'><tbody>";
            $return .= arrSort($array_struct);
            $return  .= getHeader($array_struct);
            $return  .= getSearchBar($array_struct, $active);
            //ДОБАВЛЕНИЕ
            $return  .= addingToTable($array_struct, $active);
            $maxrows = 0;

            foreach ($sql_select as $i => $row){
                $form_id = 'form'.$i;
                $return .= "<tr>";
                $return .= getRows($row, $array_struct, $active, $form_id);
                $return .= buttonChange($active, $row['id'], $form_id);
                $return .= buttonDelete($row['id']);
                $return .= "</tr>";
                $maxrows++;
                if($maxrows == $_SESSION['selsize']){
                    break;
                }
            }
            $return .= "</tbody></table>";
            return $return;
        }
