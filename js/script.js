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

function create_string(data){
    //ДОБАВЛЕНИЕ ТАБЛИЦЫ В БД
    //ДОБАВЛЕНИЕ ПОЛЕЙ
    let $tbl = $(data).closest('tr');
    let $tbl_id = Number($tbl.attr('id')) + 1;
    $tbl.after("<tr id='" + $tbl_id + "'>" +
        "<td>" +
            "<input id='id' value='Название'/>" +
            "<br>" +
            "<select name='' form='' class='form-control' class='form-control'>" +
                "<option name='' form='' value='Целые числа' >Целые числа</option>" +
                "<option name='' form='' value='Дробные числа' title='Дробные числа' >Дробные числа</option>" +
                "<option name='' form='' value='Строки' title='Строки' >Строки</option>" +
            "</select>" +
            "</td><td>" +
            "<div class='btn-group' role='group' aria-label='Basic example'>" +
                "<button id='0' type='button' class='btn btn-outline-success'  onclick='up_string(this)'>Вверх</button>" +
                "<button id='1' type='button' class='btn btn-outline-success'  onclick='down_string(this)'>Вниз</button>" +
                "<button id='3' type='button' class='btn btn-outline-danger'  onclick='delete_string(this)'>Удалить</button>" +
            "</div>"+
            "</td>" +
        "</tr>");
    /*$(data).addClass('disabled');
    $(data).attr('hidden',true);*/
    /*$(data).remove();*/
}
function up_string(data){
    let $tbl = $(data).closest('tr');
    let $tbl2 = $(data.closest('tr')).prev();
    console.log($tbl2);
    $tbl.replaceWith($tbl2);
    //parent.removeChild($tbl);
}
function down_string(data){

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