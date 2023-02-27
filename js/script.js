function buttoned(data){
        //РЕДАКТИРОВАНИЕ
        var $tbl = $(data).closest('tr');
        if(data.value == 'Редактировать'){
            $tbl.find('input:not(:first)').attr('disabled', false);
            $tbl.find('select').attr('disabled', false);
			$(data).removeClass('btn-warning');
			$(data).addClass('btn-success');
			$(data).val('Сохранить');
			$(data).attr('type', 'submit');
			setTimeout( () => {
				$(data).attr('form', $(data).prev().attr('form') );
			}, 0);
            $(data).attr('onclick',null)
		}
	}

    function selsize() {
        var sel=document.getElementById('selsize').selectedIndex;
        location.href = './contentsize.php?selsize='+ sel;
        console.log(sel);
    }


    function tablesort() {
        let arr_sort=document.getElementById('tablesort').selectedIndex;
        location.href = './arrSort.php?arr_sort='+ arr_sort;
        console.log(arr_sort);
    }

function create_string(data) {
    //ДОБАВЛЕНИЕ ТАБЛИЦЫ В БД
    //ДОБАВЛЕНИЕ ПОЛЕЙ
    let $tbl = $(data).closest('tr');
    $tbl.attr('id', $tbl.attr('id').replace('#', ''));
    let id = $tbl.attr('id');
    let $next_id = Number($tbl.attr('id')) + 1;
    $tbl.after("<tr id='#" + $next_id + "'>" +
        "<td>" +
        "<input name='"+ id +"' form='idf' value='Название'/>" +
        "<br>" +
        "<select name='#"+ id +"' form='idf' class='form-control' class='form-control'>" +
        "<option name='0' form='idf' value='Целые числа' >Целые числа</option>" +
        "<option name='1' form='idf' value='Дробные числа' title='Дробные числа' >Дробные числа</option>" +
        "<option name='2' form='idf' value='Строки' title='Строки' >Строки</option>" +
        "</select>" +
        "</td><td>" +
        "<div class='btn-group' role='group' aria-label='Basic example'>" +
        "<button id='00' type='button' class='btn btn-outline-success'  onclick='up_string(this)' disabled >Вверх</button>" +
        "<button id='01' type='button' class='btn btn-outline-success'  onclick='down_string(this)' disabled>Вниз</button>" +
        "<button id='02' type='button' class='btn btn-outline-success' onclick='create_string(this)'>Добавить</button>" +
        "</div>" +
        "</td>" +
        "</tr>");

    $(data).attr("onclick", null);
    if (Number($tbl.attr('id')) !== 0) {
        document.getElementById(id).getElementsByTagName('button')[0].disabled = false;
        document.getElementById(id).getElementsByTagName('button')[1].disabled = false;
        $(data).attr('onclick', 'delete_string(this)');
        $(data).text('Удалить');
        $(data).removeClass('btn-outline-success');
        $(data).addClass('btn-outline-danger');
    } else {
        $(data).addClass('disabled');
        /*$(data).remove();*/
    }
}
function up_string(data){
    if(($(data).closest('tr').prev().attr('id') !== '0') &&
        ($(data).closest('tr').attr('id').includes('#') === false)){
        let elem1 = document.getElementById($(data).closest('tr').prev().attr('id'));
        let elem2 = document.getElementById($(data).closest('tr').attr('id'));
        document.getElementById($(data).closest('tr').prev().attr('id')).replaceWith(elem2);
        elem2.after(elem1);

    }
}
function down_string(data){
    if(($(data).closest('tr').next().attr('id').includes('#') === false) ||
        ($(data).closest('tr').attr('id').includes('#') === true)){
        let elem1 = document.getElementById($(data).closest('tr').attr('id'));
        let elem2 = document.getElementById($(data).closest('tr').next().attr('id'));
        document.getElementById($(data).closest('tr').attr('id')).replaceWith(elem2);
        elem2.after(elem1);

    }
}
function delete_string(data){
    let $tbl = $(data).closest('tr');
    $tbl.remove();
}
    async function time_btn(){
        //ВРЕМЯ
        let gettime = await fetch("./gettime.php",{
            method:'GET'
        })
            .then((data) =>{
                return data})
            .then((resp) =>
                {return resp.text()})
            .then(resBody => {
                /*console.log(resBody)*/
                return resBody;
            });
        $('#time_text').attr('value', gettime);
        $('#time_text').attr('style', 'visibility : visible; border:none; text-align: center');
    }

    setInterval( async ()=>{
        time_btn()
    }, 1000);

    document.addEventListener("DOMContentLoaded", time_btn());