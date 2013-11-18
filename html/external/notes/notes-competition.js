$(document).ready(function() {
	/***************load persentaje*************/
	var $edition_notes = false;
	var $strnotes = "";

    $(".data-text-note").jStepper({minValue:0, maxValue:20, minLength:2});

	if ( $ulr_persentage != ''){
		$("#modal-persentage").modal({
			keyboard: false,
		});
		$("#modal-persentage").modal('show');
	    $.get($ulr_persentage,function($data){
	        $("#modal-persentage").html($data);
			$("#close-modal-persentage").attr('disabled',true);
	    });
	}
	/*********************************************/

	/*************************sroll**********************/
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

	/*****************************************************/
	/***removed class error*****/
	$(".data-text-note").click(function(){
        $(this).removeClass('notes-error-input');
	});
    /**********limit-just-numbers**********/
    $("#tb-notes .data-text-note").keypress(function(e){
    	var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
               
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);
                $tr.attr("edition",true); 
                
                $index = $(this).attr("index");
                $("#img-notes-"+ $index).attr("src","/img/full_page.png");
                $("#save_notes_competition").attr("disabled",false);
                if($edition_notes == false){
                    InitializeTimer();
                    $edition_notes = true;
                }
                
            }


    });
	
	/**********************fill note ************************/
	$("#tb-notes .data-text-note").keyup(function(){
		var td = $(this).parent();
        var tr = td.parent();
        $index = $(this).attr("index");
        $tr = $(tr);
        calculation_of_average($tr,$partial,$units);
        $("#save_notes_competition").attr('disabled',false);
        $tr.attr('edition',true);
        $("#img-notes-"+ $index).attr("src","/img/full_page.png");
	});


	/*****************************save notes**********************************/
    $("#save_notes_competition").click(function(){
        $index_tmp = -2;
        var $send_server = false;
    	$("#tb-notes tr").each(function(){
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
            $("#save_notes_competition").attr('disabled',true);
            $edition_notes = false;
        }		
    });	

	/*****************************closure record********************************/
	$("#closure-record-competition").click(function(){
		
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
        var $url = '/docente/fillnotes/closurerecordcompetition/' + result;

        $.ajax({
        	url:$url,
        	success:function($data){

        		if ($data.status == true) {
                    window.location.href = window.location.href; 
        		}else{
        			
        			if ($data.closure == false) {
                        var url_assist = '/assistance/student/index/' +result;
                        $("#cont-alerts").html("<div class='alert alert-danger col-md-12'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Alerta!</strong> Estimado docente cierre de Asistencia <a href='"+url_assist+"' class='btn btn-warning'><span class='glyphicon glyphicon-calendar'></span> Ir Asistencia</a></div>");
                    }
        			
        			if (
        				(
                            ($data.info.nota1_i && parseInt($data.info.num_reg) > parseInt($data.info.nota1_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(3)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            ($data.info.nota2_i && parseInt($data.info.num_reg) > parseInt($data.info.nota2_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(4)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            ($data.info.nota3_i && parseInt($data.info.num_reg) > parseInt($data.info.nota3_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(5)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            ($data.info.nota6_i && parseInt($data.info.num_reg) > parseInt($data.info.nota6_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(7)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            ($data.info.nota7_i && parseInt($data.info.num_reg) > parseInt($data.info.nota7_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(8)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            ($data.info.nota8_i && parseInt($data.info.num_reg) > parseInt($data.info.nota8_i)) 
                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(9)[0].children[0])
                            average = $($(this).find('td').eq(10)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}

        			/****************partial 2*************/
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota1_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota1_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(5)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota2_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota2_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(6)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota3_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota3_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(7)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota6_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota6_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(9)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota7_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota7_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(10)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}
        			if (
        				(
                            (parseInt($partial) == 2 && $data.info.nota8_ii && parseInt($data.info.num_reg) > parseInt($data.info.nota8_ii))

                        )
        			) {
        				$("#tb-notes tbody tr").each(function () {
                            obj = $($(this).find('td').eq(11)[0].children[0])
                            average = $($(this).find('td').eq(8)[0].children[0])
                            if(obj.val() == ''  && average.val() != 'RET'){
                                obj.addClass("notes-error-input");
                            }
                        })
        			}

        		}
        	},
        	error:function($data){
        		alert("error");
        	}
        });


	});

});

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
        $("#save_notes_competition").click();
        
    }
    else
    {
        self.status = sequence
        sequence = sequence - 1
        timerRunning = true
        timerId = self.setTimeout("StartTheTimer()", delay)
    }
}

/**========================save notes in server =================================**/
function save_notes(){
	var result = '';
	var result_decode = '';

	for(var prop in $notes ){
		result_decode += '' + $.base64.decode(prop) + '/' + $.base64.decode($notes[prop]) + '/';
	}

	if (persetage_complte == true) {

		if ($partial == 1) {
			if (
				$notes[$.base64.encode('nota1_i')] && 
				$notes[$.base64.encode('nota2_i')] && 
				$notes[$.base64.encode('nota3_i')] 
				) {
				$notes[$.base64.encode('nota4_i')] = 
				Math.floor(
					( intval($.base64.decode($notes[$.base64.encode('nota1_i')]))*intval($apersentage['porc1_u1'])/100 ) + 
					( intval($.base64.decode($notes[$.base64.encode('nota2_i')]))*intval($apersentage['porc2_u1'])/100 ) +
					( intval($.base64.decode($notes[$.base64.encode('nota3_i')]))*intval($apersentage['porc3_u1'])/100 )
				);
			}
			else{
				$notes[$.base64.encode('nota4_i')] = '';
			}
			if (
				$notes[$.base64.encode('nota6_i')] && 
				$notes[$.base64.encode('nota7_i')] && 
				$notes[$.base64.encode('nota8_i')] 
				) {
				$notes[$.base64.encode('nota9_i')] = 
				Math.floor(
					( intval($.base64.decode($notes[$.base64.encode('nota6_i')]))*intval($apersentage['porc1_u2'])/100 ) + 
					( intval($.base64.decode($notes[$.base64.encode('nota7_i')]))*intval($apersentage['porc2_u2'])/100 ) +
					( intval($.base64.decode($notes[$.base64.encode('nota8_i')]))*intval($apersentage['porc3_u2'])/100 )
				);
			}
			else{
				$notes[$.base64.encode('nota9_i')] = '';
			}

			$notes[$.base64.encode('nota4_i')] = $.base64.encode($notes[$.base64.encode('nota4_i')]);
			$notes[$.base64.encode('nota9_i')] = $.base64.encode($notes[$.base64.encode('nota9_i')]);
		}else{
			
				if ($partial==2 && $units_t == 3) {
					if (
						$notes[$.base64.encode('nota1_ii')] && 
						$notes[$.base64.encode('nota2_ii')] && 
						$notes[$.base64.encode('nota3_ii')]
					) {
						$notes[$.base64.encode('nota4_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes[$.base64.encode('nota1_ii')]))*intval($apersentage('porc1_u3'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota2_ii')]))*intval($apersentage('porc2_u3'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota3_ii')]))*intval($apersentage('porc3_u3'))/100 ) 
						);
					}
					else
					{
						$notes[$.base64.encode('nota4_ii')] = '';
					}


					if(
						$notes[$.base64.encode('nota4_i')] &&
						$notes[$.base64.encode('nota9_i')] &&
						$notes[$.base64.encode('nota4_ii')]
						){

						$sum = floatval($.base64.encode($notes[$.base64.encode('nota4_i')])) +
								floatval($.base64.encode($notes[$.base64.encode('nota9_i')])) +
								floatval($.base64.encode($notes[$.base64.encode('nota4_ii')])) ;

						$notes[$.base64.encode('notafinal')] = roundNumber($sum/4,0);
						$notes[$.base64.encode('notafinal')] = $.base64.encode[$.base64.encode($notes[$.base64.encode('notafinal')])];
					}

				}
				else {
					if (
						$notes[$.base64.encode('nota1_ii')] && 
						$notes[$.base64.encode('nota2_ii')] && 
						$notes[$.base64.encode('nota3_ii')]
					) {
						$notes[$.base64.encode('nota4_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes[$.base64.encode('nota1_ii')]))*intval($apersentage('porc1_u3'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota2_ii')]))*intval($apersentage('porc2_u3'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota3_ii')]))*intval($apersentage('porc3_u3'))/100 ) 
						);
					}
					else
					{
						$notes[$.base64.encode('nota4_ii')] = '';
					}

					if (
						$notes[$.base64.encode('nota6_ii')] && 
						$notes[$.base64.encode('nota7_ii')] && 
						$notes[$.base64.encode('nota8_ii')]
					) {
						$notes[$.base64.encode('nota9_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes[$.base64.encode('nota6_ii')]))*intval($apersentage('porc1_u4'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota7_ii')]))*intval($apersentage('porc2_u4'))/100 ) +
							(intval($.base64.encode($notes[$.base64.encode('nota8_ii')]))*intval($apersentage('porc3_u4'))/100 ) 
						);
					}
					else
					{
						$notes[$.base64.encode('nota9_ii')] = '';
					}

					$notes[$.base64.encode('nota4_ii')] = $.base64.encode($notes[$.base64.encode('nota4_ii')]);
					$notes[$.base64.encode('nota9_ii')] = $.base64.encode($notes[$.base64.encode('nota9_ii')]);

					if(
						$notes[$.base64.encode('nota4_i')] &&
						$notes[$.base64.encode('nota9_i')] &&
						$notes[$.base64.encode('nota4_ii')] &&
						$notes[$.base64.encode('nota9_ii')] 
						){

						$sum = floatval($.base64.encode($notes[$.base64.encode('nota4_i')])) +
								floatval($.base64.encode($notes[$.base64.encode('nota9_i')])) +
								floatval($.base64.encode($notes[$.base64.encode('nota4_ii')])) +
								floatval($.base64.encode($notes[$.base64.encode('nota9_ii')])) ;

						$notes[$.base64.encode('notafinal')] = roundNumber($sum/4,0);
						$notes[$.base64.encode('notafinal')] = $.base64.encode[$.base64.encode($notes[$.base64.encode('notafinal')])];
					}

				}

		}

		$($tr[0].children).each(function(){
			if (this.firstElementChild != null ) {
				if (this.firstElementChild.nodeName == 'INPUT') {
					$input_text = $(this.firstElementChild);
	                $name = $input_text.attr('name');
					
					if ($name == "nota4_i") {
						if ($notes[$.base64.encode('nota4_i')]) {
							$input_text.val($.base64.decode($notes[$.base64.encode('nota4_i')]));
						}
					}
					if ($name == "nota9_i") {
						if ($notes[$.base64.encode('nota9_i')]) {
							$input_text.val($.base64.decode($notes[$.base64.encode('nota9_i')]));
						}
					}
					if ($name == "nota4_ii") {
						if ($notes[$.base64.encode('nota4_ii')]) {
							$input_text.val($.base64.decode($notes[$.base64.encode('nota4_ii')]));
						}
					}
					if ($name == "nota9_ii") {
						if ($notes[$.base64.encode('nota9_ii')]) {
							$input_text.val($.base64.decode($notes[$.base64.encode('nota9_ii')]));
						}
					} 
					if ($name == "notafinal") {
						if ($notes[$.base64.encode('notafinal')]) {
							$input_text.val($.base64.decode($notes[$.base64.encode('notafinal')]));
						}
					}               	
				}
			}
		});
	

		for(var prop in $notes){
			result += '' + prop + "/" + $notes[prop] + '/';
		}

		result = result.substring(0,result.length-1);
		result = result + "/" + $.base64.encode('partial') + "/" + $.base64.encode($partial);
		var $url = "/docente/fillnotes/savecompettition/" +result;
		if (result_decode.toLowerCase().indexOf('nota') >= 0) {
			$index_tmp1 = $index_tmp-1;
			$.ajax({
				url:$url,
				async:false,
				success:function($data){
					if ($data.status == true) {
						$("#img-notes-"+$index_tmp1).attr('src','/img/accept_page.png');
					}else{
						$("#img-notes-"+$index_tmp1).attr('src','/img/delete_page.png');

					}
	                $tr.attr("edition",false); 
				},
				error:function($data){
					$tr.css("color", "#FC4141");
	                $("#img-notes-"+$index_tmp1).attr('src','/img/delete_page.png');
	                $tr.attr("edition",false); 
				}
			});
		}
	}else
	{
		$("#modal-persentage").modal({
			keyboard: false,
		});
		$("#modal-persentage").modal('show');
	    $.get($ulr_persentage,function($data){
	        $("#modal-persentage").html($data);
			$("#close-modal-persentage").attr('disabled',true);
	    });
	}
	$notes = {};
	return true;
}

function calculation_of_average($tr_tmp,$partial_t,$units_t){
	var $notes_prom = {};

	$($tr_tmp[0].children).each(function(){
        if (this.firstElementChild != null) {
            if (this.firstElementChild.nodeName == 'INPUT') {
                $input_text = $(this.firstElementChild);
                $name = $input_text.attr('name');
                $value =$input_text.val();
                if ($value != '') {
                    $notes_prom[$.base64.encode($name)]=$.base64.encode($.trim($value));
                }
            }
        }
    });

	if (persetage_complte == true) {

		if ($partial_t == 1) {
			if (
				$notes_prom[$.base64.encode('nota1_i')] && 
				$notes_prom[$.base64.encode('nota2_i')] && 
				$notes_prom[$.base64.encode('nota3_i')] 
				) {
				$notes_prom[$.base64.encode('nota4_i')] = 
				Math.floor(
					( intval($.base64.decode($notes_prom[$.base64.encode('nota1_i')]))*intval($apersentage['porc1_u1'])/100 ) + 
					( intval($.base64.decode($notes_prom[$.base64.encode('nota2_i')]))*intval($apersentage['porc2_u1'])/100 ) +
					( intval($.base64.decode($notes_prom[$.base64.encode('nota3_i')]))*intval($apersentage['porc3_u1'])/100 )
				);
			}
			else{
				$notes_prom[$.base64.encode('nota4_i')] = '';
			}
			if (
				$notes_prom[$.base64.encode('nota6_i')] && 
				$notes_prom[$.base64.encode('nota7_i')] && 
				$notes_prom[$.base64.encode('nota8_i')] 
				) {
				$notes_prom[$.base64.encode('nota9_i')] = 
				Math.floor(
					( intval($.base64.decode($notes_prom[$.base64.encode('nota6_i')]))*intval($apersentage['porc1_u2'])/100 ) + 
					( intval($.base64.decode($notes_prom[$.base64.encode('nota7_i')]))*intval($apersentage['porc2_u2'])/100 ) +
					( intval($.base64.decode($notes_prom[$.base64.encode('nota8_i')]))*intval($apersentage['porc3_u2'])/100 )
				);
			}
			else{
				$notes_prom[$.base64.encode('nota9_i')] = '';
			}

			$notes_prom[$.base64.encode('nota4_i')] = $.base64.encode($notes_prom[$.base64.encode('nota4_i')]);
			$notes_prom[$.base64.encode('nota9_i')] = $.base64.encode($notes_prom[$.base64.encode('nota9_i')]);
		}else{
			
				if ($partial_t==2 && $units_t == 3) {
					if (
						$notes_prom[$.base64.encode('nota1_ii')] && 
						$notes_prom[$.base64.encode('nota2_ii')] && 
						$notes_prom[$.base64.encode('nota3_ii')]
					) {
						$notes_prom[$.base64.encode('nota4_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes_prom[$.base64.encode('nota1_ii')]))*intval($apersentage('porc1_u3'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota2_ii')]))*intval($apersentage('porc2_u3'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota3_ii')]))*intval($apersentage('porc3_u3'))/100 ) 
						);
					}
					else
					{
						$notes_prom[$.base64.encode('nota4_ii')] = '';
					}


					if(
						$notes_prom[$.base64.encode('nota4_i')] &&
						$notes_prom[$.base64.encode('nota9_i')] &&
						$notes_prom[$.base64.encode('nota4_ii')]
						){

						$sum = floatval($.base64.encode($notes_prom[$.base64.encode('nota4_i')])) +
								floatval($.base64.encode($notes_prom[$.base64.encode('nota9_i')])) +
								floatval($.base64.encode($notes_prom[$.base64.encode('nota4_ii')])) ;

						$notes_prom[$.base64.encode('notafinal')] = roundNumber($sum/4,0);
						$notes_prom[$.base64.encode('notafinal')] = $.base64.encode[$.base64.encode($notes_prom[$.base64.encode('notafinal')])];
					}

				}
				else {
					if (
						$notes_prom[$.base64.encode('nota1_ii')] && 
						$notes_prom[$.base64.encode('nota2_ii')] && 
						$notes_prom[$.base64.encode('nota3_ii')]
					) {
						$notes_prom[$.base64.encode('nota4_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes_prom[$.base64.encode('nota1_ii')]))*intval($apersentage('porc1_u3'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota2_ii')]))*intval($apersentage('porc2_u3'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota3_ii')]))*intval($apersentage('porc3_u3'))/100 ) 
						);
					}
					else
					{
						$notes_prom[$.base64.encode('nota4_ii')] = '';
					}

					if (
						$notes_prom[$.base64.encode('nota6_ii')] && 
						$notes_prom[$.base64.encode('nota7_ii')] && 
						$notes_prom[$.base64.encode('nota8_ii')]
					) {
						$notes_prom[$.base64.encode('nota9_ii')] = 
						Math.floor(
							(intval($.base64.encode($notes_prom[$.base64.encode('nota6_ii')]))*intval($apersentage('porc1_u4'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota7_ii')]))*intval($apersentage('porc2_u4'))/100 ) +
							(intval($.base64.encode($notes_prom[$.base64.encode('nota8_ii')]))*intval($apersentage('porc3_u4'))/100 ) 
						);
					}
					else
					{
						$notes_prom[$.base64.encode('nota9_ii')] = '';
					}

					$notes_prom[$.base64.encode('nota4_ii')] = $.base64.encode($notes_prom[$.base64.encode('nota4_ii')]);
					$notes_prom[$.base64.encode('nota9_ii')] = $.base64.encode($notes_prom[$.base64.encode('nota9_ii')]);

					if(
						$notes_prom[$.base64.encode('nota4_i')] &&
						$notes_prom[$.base64.encode('nota9_i')] &&
						$notes_prom[$.base64.encode('nota4_ii')] &&
						$notes_prom[$.base64.encode('nota9_ii')] 
						){

						$sum = floatval($.base64.encode($notes_prom[$.base64.encode('nota4_i')])) +
								floatval($.base64.encode($notes_prom[$.base64.encode('nota9_i')])) +
								floatval($.base64.encode($notes_prom[$.base64.encode('nota4_ii')])) +
								floatval($.base64.encode($notes_prom[$.base64.encode('nota9_ii')])) ;

						$notes_prom[$.base64.encode('notafinal')] = roundNumber($sum/4,0);
						$notes_prom[$.base64.encode('notafinal')] = $.base64.encode[$.base64.encode($notes_prom[$.base64.encode('notafinal')])];
					}

				}

		}

		$($tr_tmp[0].children).each(function(){
			if (this.firstElementChild != null ) {
				if (this.firstElementChild.nodeName == 'INPUT') {
					$input_text = $(this.firstElementChild);
	                $name = $input_text.attr('name');
					
					if ($name == "nota4_i") {
						if ($notes_prom[$.base64.encode('nota4_i')]) {
							$input_text.val($.base64.decode($notes_prom[$.base64.encode('nota4_i')]));
						}
					}
					if ($name == "nota9_i") {
						if ($notes_prom[$.base64.encode('nota9_i')]) {
							$input_text.val($.base64.decode($notes_prom[$.base64.encode('nota9_i')]));
						}
					}
					if ($name == "nota4_ii") {
						if ($notes_prom[$.base64.encode('nota4_ii')]) {
							$input_text.val($.base64.decode($notes_prom[$.base64.encode('nota4_ii')]));
						}
					}
					if ($name == "nota9_ii") {
						if ($notes_prom[$.base64.encode('nota9_ii')]) {
							$input_text.val($.base64.decode($notes_prom[$.base64.encode('nota9_ii')]));
						}
					} 
					if ($name == "notafinal") {
						if ($notes_prom[$.base64.encode('notafinal')]) {
							$input_text.val($.base64.decode($notes_prom[$.base64.encode('notafinal')]));
						}
					}               	
				}
			}
		});

	}else
	{
		$("#modal-persentage").modal({
			keyboard: false,
		});
		$("#modal-persentage").modal('show');
	    $.get($ulr_persentage,function($data){
	        $("#modal-persentage").html($data);
			$("#close-modal-persentage").attr('disabled',true);
	    });
	}

}

