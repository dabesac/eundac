$(document).ready(function() {
	var $strnotes = '';
    var $edition_notes = false;
    $(".data-notes-input").jStepper({minValue:0, maxValue:20, minLength:2});
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();
        if( scroll >= 146){
            $("#header-info").addClass('data-header-info');
            $("#header-info").addClass('col-md-10');
        }
        else{
            
            $("#header-info").removeClass('data-header-info');
            $("#header-info").removeClass('col-md-10');
        }
    });

    // $(".tb-notes input[type='text']").focusout(function(e){
    //         $strnotes = '';
    //         alert();
    // });
    
    $(".tb-notes .data-notes-input").click(function(){
            $(this).removeClass('notes-error-input');
    });
    
    /**********limit-just-numbers**********/
    // var $notes = {};
	$(".tb-notes .data-notes-input").keypress(function(e){

		var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);
                $tr.attr("edition",true); 
                
                $index = $(this).attr("index");
                $("#edit-note-"+ $index).attr("src","/img/full_page.png");;
                $("#save_notes").attr("disabled",false);
                objeto = {
                    "width":'50px',
                }
                $(".td-edit-note").css(objeto);
                if($edition_notes == false){
                    InitializeTimer();
                    $edition_notes = true;
                }
                
            }

	});
	
    /**************************** average ********************************/
    $(".tb-notes .data-notes-input").keyup(function(e){
        var td = $(this).parent();
        var tr = td.parent();
        $index = $(this).attr("index");

        $tr = $(tr);
        calculation_of_average($tr,$partial);
        $("#save_notes").attr('disabled',false);
        $tr.attr('edition',true);
        $("#edit-note-"+ $index).attr("src","/img/full_page.png");
        $("#num-edit-note-"+$index).remove();
    });

    /***************************** save notes ********************************/
    $("#save_notes").click(function(e){
        $index_tmp = -2;
        var $send_server = false;

        $("#table-notes tr").each(function(){
            $index_tmp = parseInt($index_tmp)+1;
            $tr = $(this);
            $action = $tr.attr('edition');
            if($action == 'true'){
                if ($send_server == false) {
                    $("#data-server").modal('show');
                    $send_server =   true;
                }

                $notes = {};

                $notes[$.base64.encode('curid')] = $.base64.encode($.trim($($tr).attr('curid')));
                $notes[$.base64.encode('escid')] = $.base64.encode($.trim($($tr).attr('escid')));
                $notes[$.base64.encode('courseid')] = $.base64.encode($.trim($($tr).attr('courseid')));
                $notes[$.base64.encode('perid')] = $.base64.encode($.trim($($tr).attr('perid')));
                $notes[$.base64.encode('turno')] = $.base64.encode($.trim($($tr).attr('turno')));
                $notes[$.base64.encode('eid')] = $.base64.encode($.trim($($tr).attr('eid')));
                $notes[$.base64.encode('oid')] = $.base64.encode($.trim($($tr).attr('oid')));
                $notes[$.base64.encode('subid')] = $.base64.encode($.trim($($tr).attr('subid')));
                $notes[$.base64.encode('regid')] = $.base64.encode($.trim($($tr).attr('regid')));
                $notes[$.base64.encode('uid')] = $.base64.encode($.trim($($tr).attr('uid')));
                $notes[$.base64.encode('pid')] = $.base64.encode($.trim($($tr).attr('pid')));

                $information = false;

                $($tr[0].children).each(function(){
                    if (this.firstElementChild != null) {
                        if (this.firstElementChild.nodeName == 'INPUT') {
                            $input_text = $(this.firstElementChild);
                            $name = $input_text.attr('name');
                            $value = $input_text.val();
                            if($value != ''){
                                $information = true;
                                $notes[$.base64.encode($name)]=$.base64.encode($.trim($value));
                            }
                        }
                    }
                });

                if ($information) {
                    save_notes();
                }
            }
        }); 

            
        if ($send_server == true) {
            // alert("dad");
            $('#data-server').modal('hide');
            $("#save_notes").attr('disabled',true);
            $edition_notes = false;
        }
    }   );
    
    $("#closure-record").click(function(){
        
        var $record = {};
        var  result='';
        $record[$.base64.encode('curid')] = $.base64.encode($.trim($("#curid").val()));         
        $record[$.base64.encode('escid')] = $.base64.encode($.trim($("#escid").val()));         
        $record[$.base64.encode('courseid')] = $.base64.encode($.trim($("#courseid").val()));         
        $record[$.base64.encode('perid')] = $.base64.encode($.trim($("#perid").val()));         
        $record[$.base64.encode('turno')] = $.base64.encode($.trim($("#turno").val()));         
        $record[$.base64.encode('eid')] = $.base64.encode($.trim($("#eid").val()));         
        $record[$.base64.encode('oid')] = $.base64.encode($.trim($("#oid").val()));         
        $record[$.base64.encode('subid')] = $.base64.encode($.trim($("#subid").val()));         
        $record[$.base64.encode('partial')] = $.base64.encode($.trim($partial));         

        for(var prop in $record){
            result += ''+ prop + '/' + $record[prop] + '/';
        }
        var $url = '/docente/fillnotes/closuretarget/' + result;

        $.ajax({
            url:$url,
            success: function($data){1
                if ($data.status == true) {
                    window.location.href = window.location.href; 
                }else{
                    if ($data.closure == false) {
                        var url_assist = '/assistance/student/index/' +result;
                        $("#cont-alerts").html("<div class='alert alert-danger col-md-12'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Alerta!</strong> Estimado docente cierre de Asistencia <a href='"+url_assist+"' class='btn btn-warning'><span class='glyphicon glyphicon-calendar'></span> Ir Asistencia</a></div>");
                    }

                    if(
                        ($data.info.nota1_i && parseInt($data.info.num_reg) > parseInt($data.info.nota1_i)) && 
                        ($data.info.nota1_i && parseInt($data.info.nota1_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(3)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota2_i && parseInt($data.info.num_reg) > parseInt($data.info.nota2_i)) && 
                        ($data.info.nota2_i && parseInt($data.info.nota2_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(4)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota3_i && parseInt($data.info.num_reg) > parseInt($data.info.nota3_i)) && 
                        ($data.info.nota3_i && parseInt($data.info.nota3_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(5)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota4_i && parseInt($data.info.num_reg) > parseInt($data.info.nota4_i)) && 
                        ($data.info.nota4_i && parseInt($data.info.nota4_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(6)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota5_i && parseInt($data.info.num_reg) > parseInt($data.info.nota5_i)) && 
                        ($data.info.nota5_i && parseInt($data.info.nota5_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(7)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota6_i && parseInt($data.info.num_reg) > parseInt($data.info.nota6_i)) && 
                        ($data.info.nota6_i && parseInt($data.info.nota6_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(8)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota7_i && parseInt($data.info.num_reg) > parseInt($data.info.nota7_i)) && 
                        ($data.info.nota7_i && parseInt($data.info.nota7_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(9)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota8_i && parseInt($data.info.num_reg) > parseInt($data.info.nota8_i)) && 
                        ($data.info.nota8_i && parseInt($data.info.nota8_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(10)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        ($data.info.nota9_i && parseInt($data.info.num_reg) > parseInt($data.info.nota9_i)) && 
                        ($data.info.nota9_i && parseInt($data.info.nota9_i) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(11)[0].children[0])
                            average = $($this.find('td').eq(12)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }

                    /***************************partial 2*************/
                    if(
                        (parseInt($partial)==2 && $data.info.nota1_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota1_ii)) && 
                        ($data.info.nota1_ii && parseInt($data.info.nota1_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(4)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota2_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota2_ii)) && 
                        ($data.info.nota2_ii && parseInt($data.info.nota2_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(5)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota3_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota3_ii)) && 
                        ($data.info.nota3_ii && parseInt($data.info.nota3_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(6)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota4_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota4_ii)) && 
                        ($data.info.nota4_ii && parseInt($data.info.nota4_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(7)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota5_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota5_ii)) && 
                        ($data.info.nota5_ii && parseInt($data.info.nota5_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(8)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota6_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota6_ii)) && 
                        ($data.info.nota6_ii && parseInt($data.info.nota6_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(9)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota7_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota7_ii)) && 
                        ($data.info.nota7_ii && parseInt($data.info.nota7_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(10)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                    if(
                        (parseInt($partial)==2 && $data.info.nota8_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota8_ii)) && 
                        ($data.info.nota8_ii && parseInt($data.info.nota8_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(11)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                     if(
                        (parseInt($partial)==2 && $data.info.nota9_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota9_ii)) && 
                        ($data.info.nota9_ii && parseInt($data.info.nota9_ii) > 0)
                    ){
                        $("#table-notes tbody tr").each(function () {
                            var $this = $(this)
                            obj = $($this.find('td').eq(12)[0].children[0])
                            average = $($this.find('td').eq(13)[0].children[0])
                            if(obj.val()==''&& average.val !='RET'){
                                obj.addClass('notes-error-input');
                            }
                        });
                    }
                }
            },
            error: function($data){
                alert("error");
            }
        });


    });



});

/*=======================function calc average===========================*/
function calculation_of_average($trcell,$partial_temp){
    var $addition = 0;
    var $num_notes = 0;
    var $average = 0;
    var $notes_prom = {};

    $data = false;
    $($trcell[0].children).each(function(){
        if (this.firstElementChild != null) {
            if (this.firstElementChild.nodeName == 'INPUT') {
                $input_text = $(this.firstElementChild);
                $name = $input_text.attr('name');
                $value =$input_text.val();
                // alert($value);
                if ($value != '') {
                    $data = true;
                    $notes_prom[$.base64.encode($name)]=$.base64.encode($.trim($value));
                }
            }
        }
    });

    if ($partial_temp == 1) {
        if($notes_prom[$.base64.encode('nota1_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota1_i')])); $num_notes += 1;}
        if($notes_prom[$.base64.encode('nota2_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota2_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota3_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota3_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota4_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota4_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota5_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota5_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota6_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota6_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota7_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota7_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota8_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota8_i')]));$num_notes += 1;}
        if($notes_prom[$.base64.encode('nota9_i')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota9_i')]));$num_notes += 1;}

        $average = Math.floor(floatval($addition/$num_notes));
        // alert($average);
        $notes_prom[$.base64.encode('promedio1')] = $average;
        $notes_prom[$.base64.encode('promedio1')] = $.base64.encode($notes_prom[$.base64.encode('promedio1')]);


    }else{
        if ($partial_temp == 2) {
            if($notes_prom[$.base64.encode('nota1_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota1_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota2_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota2_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota3_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota3_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota4_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota4_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota5_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota5_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota6_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota6_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota7_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota7_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota8_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota8_ii')]));$num_notes += 1;}
            if($notes_prom[$.base64.encode('nota9_ii')]){$addition += intval($.base64.decode($notes_prom[$.base64.encode('nota9_ii')]));$num_notes += 1;}

            $average = Math.floor(floatval($addition/$num_notes));
            $notes_prom[$.base64.encode('promedio2')]= $average;

            $before = intval($.base64.decode($notes_prom[$.base64.encode('promedio1')])) + $average;

            $notes_prom[$.base64.encode('notafinal')] = roundNumber($before/2,0);
            $notes_prom[$.base64.encode('promedio2')] = $.base64.encode($notes_prom[$.base64.encode('promedio2')]);
            $notes_prom[$.base64.encode('notafinal')] = $.base64.encode($notes_prom[$.base64.encode('notafinal')]);

        }
    }

    $($trcell[0].children).each(function(){
        if (this.firstElementChild != null) {
            if (this.firstElementChild.nodeName == 'INPUT') {
                $input_text = $(this.firstElementChild);
                $name = $input_text.attr('name');
                if ($name == 'promedio1') {
                    // alert($.base64.decode($notes_prom[$.base64.encode('promedio1')]));
                    $input_text.val($.base64.decode($notes_prom[$.base64.encode('promedio1')]));
                }
                if ($name == 'promedio2') {
                    $input_text.val($.base64.decode($notes_prom[$.base64.encode('promedio2')]));
                }
                if ($name == 'notafinal') {
                    $input_text.val($.base64.decode($notes_prom[$.base64.encode('notafinal')]));
                }
            }
        }

    });
}
/****************timer save notess********************/
var sequence;
var timerId = null;
var timerRunning = false;
var delay = 3000

function InitializeTimer(){
    sequence = 30
    StopTheClock()
    StartTheTimer()
}
function StopTheClock()
{
    if(timerRunning)
        clearTimeout(timerId)
    timerRunning = false
}

function StartTheTimer()
{
    if(timerRunning)
        clearTimeout(timerId)
    timerRunning = false
    if (sequence==0)
    {
        StopTheClock()
        $("#save_notes").click();
        
    }
    else
    {
        self.status = sequence
        sequence = sequence - 1
        timerRunning = true
        timerId = self.setTimeout("StartTheTimer()", delay)
    }
}

/*=====================convert in val integer====================================*/
function intval(mixed_var,base){
    var tmp;
    var type = typeof(mixed_var);
     if (type === 'boolean') {
        return +mixed_var;
    } else if (type === 'string') {
        tmp = parseInt(mixed_var, base || 10);
        return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;    } else if (type === 'number' && isFinite(mixed_var)) {
        return mixed_var | 0;
    } else {
        return 0;
    }
}
/**====================convert average fload================================**/
function floatval (mixed_var) {
    return (parseFloat(mixed_var) || 0);
}
/*==================round of average===================**/
function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
    return result;
}
/**========================save notes in server =================================**/
function save_notes(){
    var result = '';
    var result_decode = '';
    var $addition = 0;
    var $num_notes = 0;
    var $average = 0;

    for (var prop in $notes){
        result_decode += ''+$.base64.decode(prop)+'/'+$.base64.decode($notes[prop]) + '/';
    }

    if ($partial == 1) {
        if($notes[$.base64.encode('nota1_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota1_i')])); $num_notes += 1;}
        if($notes[$.base64.encode('nota2_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota2_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota3_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota3_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota4_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota4_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota5_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota5_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota6_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota6_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota7_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota7_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota8_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota8_i')]));$num_notes += 1;}
        if($notes[$.base64.encode('nota9_i')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota9_i')]));$num_notes += 1;}

        $average = Math.floor(floatval($addition/$num_notes));
        // alert($average);
        $notes[$.base64.encode('promedio1')] = $average;
        $notes[$.base64.encode('promedio1')] = $.base64.encode($notes[$.base64.encode('promedio1')]);


    }else{
        if ($partial == 2) {
            if($notes[$.base64.encode('nota1_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota1_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota2_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota2_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota3_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota3_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota4_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota4_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota5_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota5_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota6_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota6_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota7_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota7_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota8_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota8_ii')]));$num_notes += 1;}
            if($notes[$.base64.encode('nota9_ii')]){$addition += intval($.base64.decode($notes[$.base64.encode('nota9_ii')]));$num_notes += 1;}

            $average = Math.floor(floatval($addition/$num_notes));
            $notes[$.base64.encode('promedio2')]= $average;
            $before = intval($.base64.decode($notes[$.base64.encode('promedio1')])) + $average;

            $notes[$.base64.encode('notafinal')] = roundNumber($before/2,0);
            $notes[$.base64.encode('promedio2')] = $.base64.encode($notes[$.base64.encode('promedio2')]);
            $notes[$.base64.encode('notafinal')] = $.base64.encode($notes[$.base64.encode('notafinal')]);

        }
    }

    $($tr[0].children).each(function(){
        if (this.firstElementChild != null) {
            if (this.firstElementChild.nodeName == 'INPUT') {
                $input_text = $(this.firstElementChild);
                $name = $input_text.attr('name');
                if ($name == 'promedio1') {
                    // alert($.base64.decode($notes_prom[$.base64.encode('promedio1')]));
                    $input_text.val($.base64.decode($notes[$.base64.encode('promedio1')]));
                }
                if ($name == 'promedio2') {
                    $input_text.val($.base64.decode($notes[$.base64.encode('promedio2')]));
                }
                if ($name == 'notafinal') {
                    $input_text.val($.base64.decode($notes[$.base64.encode('notafinal')]));
                }
            }
        }

    });

    for (var prop in $notes){
        result += '' + prop + '/' + $notes[prop] + '/';
    }
    result = result.substring(0,result.length-1);
    result = result + '/' + $.base64.encode('partial') + '/' + $.base64.encode($partial);
    var $url = '/docente/fillnotes/savetargetnotes/' + result;
    /**************************************************************/
    if(result_decode.toLowerCase().indexOf("nota") >= 0){
        $indexTmp = $index_tmp;
        $.ajax({
            url:$url,
            async:false,
            success:function($data){
                
                if ($data.status==true) {
                    $("#edit-note-" + $indexTmp).removeClass();
                    $("#edit-note-" + $indexTmp).attr('src','/img/accept_page.png');
                    $tr.css('color','none');
                }else{
                    $("#edit-note-" + $indexTmp).removeClass();
                    $("#edit-note-" + $indexTmp).attr('src','/img/accept_page.png');
                    $tr.css("color", "#FC4141");
                }
                $tr.attr("edition",false); 
            },
            error:function($data){
                $tr.css("color", "#FC4141");
                $("#edit-note-" + $indexTmp).attr('src','/img/delete_page.png');
                $tr.attr("edition",false); 

            }
        });
    }

    $notes = {};
    return true;
}
/********************==========end save============*********************************/



