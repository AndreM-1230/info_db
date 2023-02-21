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
    var arrsort=document.getElementById('tablesort').selectedIndex;
    location.href = './arrSort.php?arrsort='+ arrsort;
    console.log(arrsort);
    }

function createtable(data){
    //ДОБАВЛЕНИЕ ТАБЛИЦЫ В БД
    //ДОБАВЛЕНИЕ ПОЛЕЙ
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
                console.log(resBody)
                return resBody;
            });
        $('#time_text').attr('value', gettime);
        $('#time_text').attr('style', 'visibility : visible; border:none; text-align: center');
    }

    setInterval( async ()=>{
        time_btn()
    }, 1000);

    document.addEventListener("DOMContentLoaded", time_btn());