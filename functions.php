<?php

    function sql_connect(): void
    {
        global $db;
        try {
            $db = new PDO("mysql:host=".$_ENV['db_connection']['host'].";dbname=".$_ENV['db_connection']['db'], $_ENV['db_connection']['username'], $_ENV['db_connection']['password']);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("set names utf8");
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    function sqltab ($sql): array
    {
        global $db;
        $arr = array();
        try {
            $sth = $db->prepare($sql);
            //print_r($sth);
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
        global $db;
        $insert_id = 0;
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
            $insert_id = $db->lastInsertId();
        } catch(Exception $e) {
            ExceptionCatcher($e);
        }
        return $insert_id;
    }
    sql_connect(); // соединение с базой.
    // Условия по GET-запросам



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
            return "<h3>Имя таблицы: <input id='tablename' value=''/></h3>

                <table class='table table-striped' style='text-align:center;'><tbody>
                    <form
                        method='post'
                        action='./Cratetableserv.php'
                        id='idf'></form>
                    <h3>Поля таблицы:</h3>
                    <tr id='0'>
                        <td>
                            <h3>id</h3>
                        </td>
                        <td>
                        <div class='btn-group' role='group' aria-label='Basic example'>
                            <button type='button' id='0' class='btn btn-outline-success' disabled>Вверх</button>
                            <button type='button' id='1' class='btn btn-outline-success' disabled>Вниз</button>
                            <button type='button' id='2' class='btn btn-outline-success' onclick='create_string(this)'>Добавить</button>
                        </div>
                        </td>
                    </tr>
                </tbody></table>";
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

        function Pages($array_count, $selsize, $pages): string
        {
            $return = '';
            $page_size = [30,60,90];
            $return .= "   <form action='./pageselect.php' method='post' id='page_id'></form>
                        <nav class='navbar col-lg-6'>

                            <select class='form-select form-select-sm w-25' id='selsize' onchange='selsize()'>";
                                //ВЫБОР КОЛИЧЕСТВА ($page_size) ОТОБРАЖАЕМЫХ СТРОК ТАБЛИЦЫ
                            foreach ($page_size as $value) {
                                if($value == $selsize){
                                    $return .= "<option selected>". $value ."</option>";
                                }
                                else{
                                    $return .= "<option>". $value ."</option>";
                                }
                            }
            $return .= " </select>
                <ul class='pagination'>";
                for($i=0;$i<($array_count/$selsize);$i++){
                    //$array_count - КОЛИЧЕСТВО СТРОК В ТАБЛИЦЕ
                    //$selsize - КОЛИЧЕСТВО ОТОБРАЖАЕМЫХ СТРОК
                    //$pages - ТЕКУЩАЯ СТРАНИЦА
                    $page=$i+1;
                    if($i<=2){
                    if($page==$pages){
                        $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                    else{
                        $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                    }
                    else if($i+4>=$pages && $i-2<=$pages){
                        if($i+4==$pages && $pages !=7){$return .= "...";}
                        if($page==$pages){
                            $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                        else{
                            $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                        if($i-2==$pages && $i+4 < $array_count/$selsize){$return .= "...";}
                    }
                    else if($i+3>=$array_count/$selsize){
                        if($page==$pages){
                        $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                    else{
                        $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
                    }
                }
                $return .= "</ul>
            </nav>
        ";
        return $return;
        }