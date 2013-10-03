$(document).ready(function() {
    
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();

    });

	/**********limit-just-numbers**********/
	var $strnotes = '';
    var $edition_notes = false;
	$(".tb-notes input[type='text']").keypress(function(e){

		var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
                if(e.which != 8){
                    var chr = String.fromCharCode( e.which );
                    $strnotes = '' + $strnotes + chr + '';
                    if($strnotes != ''){$nota = Number($strnotes);}
                    else{$nota='';}
                    if(($nota < 0 || $nota >20) && ($nota!=-3) && ($nota!='')){
                        $strnotes = '';
                        $(this).val('');
                        e.preventDefault();
                    }
                }
                
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);
                $tr.attr("edition",true); 
                
                $index = $(this).attr("index");
                $("#edit-note-"+ $index).addClass("glyphicon-edit");
                $("#save_notes").attr("disabled",false);
                objeto = {
                    "width":'50px',
                }
                $(".td-edit-note").css(objeto);
                if($edition_notes == false){
                    // InitializeTimer();
                    $edition_notes = true;
                }
                
            }

	});
	
    /****************************calculation average ********************************/

    $(".tb-notes input[type='text']").keyup(function(e){
        var td = $(this).parent();
        var tr = td.parent();
        $tr = $(tr);
        $tr.attr('edition',true);
        calculation_of_average($tr,$partial);
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
            $notes_prom[$.base64.encode('promedio2')] = $.base64.encode($.notes_prom[$.base64.encode('promedio2')]);
            $notes_prom[$.base64.encode('notafinal')] = $.base64.encode($.notes_prom[$.base64.encode('notafinal')]);

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
function save_notes(){
    
}
