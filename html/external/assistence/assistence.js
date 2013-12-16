function retired_assist (obj) {
		$("#toserver").modal('show');

		var $icon = $(obj);
		var $parent = $(obj).parent();
		var $parent = $parent.parent();
		var $action = $(obj).attr('action');

		var $index = $icon.attr('index');

		if($action == "0")
		{
			$icon.removeClass('glyphicon-eye-open');
			$icon.addClass('glyphicon-eye-close');
			$parent.css('color','#FC4141');
        	$icon.attr("action","1");
		}
		else{

			$icon.removeClass('glyphicon-eye-close');
			$icon.addClass('glyphicon-eye-open');
			$parent.css('color','#000');
        	$icon.attr("action","0");
		}

		$assist = {};
		$assist[$.base64.encode('courseid')]=$.base64.encode($.trim($parent.attr('courseid')));
		$assist[$.base64.encode('curid')]=$.base64.encode($.trim($parent.attr('curid')));
		$assist[$.base64.encode('turno')]=$.base64.encode($.trim($parent.attr('turno')));
		$assist[$.base64.encode('regid')]=$.base64.encode($.trim($parent.attr('regid')));
		$assist[$.base64.encode('uid')]=$.base64.encode($.trim($parent.attr('uid')));
		$assist[$.base64.encode('pid')]=$.base64.encode($.trim($parent.attr('pid')));
		$assist[$.base64.encode('perid')]=$.base64.encode($.trim($parent.attr('perid')));
		$assist[$.base64.encode('escid')]=$.base64.encode($.trim($parent.attr('escid')));
		$assist[$.base64.encode('subid')]=$.base64.encode($.trim($parent.attr('subid')));
		$assist[$.base64.encode('action')]=$.base64.encode($.trim($action));

		$($parent[0].children).each(function(){
	        if(this.firstElementChild != null){
	            if(this.firstElementChild.nodeName == 'SPAN'){
	                
                    $checkbox= $(this.firstElementChild);
                    $name = $checkbox.attr("name");
                    
                    if ($action == '0') {
                    	$checkbox.attr('disabled',true);
                    	$checkbox.removeClass();
                    	$checkbox.addClass('inputassist  glyphicon glyphicon-registration-mark');
                    	$checkbox.attr('assistance','');
                    }
                    else
                    {
                    	$checkbox.attr('disabled',false);
                    	$checkbox.removeClass();
                    	$checkbox.addClass('inputassist  glyphicon glyphicon-unchecked');
                    	$checkbox.attr('assistance','');
                    }
	            }
	        }
    	});

		result = "";
	    for (var prop in $assist ) {
	        result += '' + prop + '/' + $assist[prop]  + '/'; 
	    }
    	result = result.substring(0, result.length-1);
    	result = result + '/' + $.base64.encode('partial') + '/' + $.base64.encode($partial);
    	var $url = '/assistance/student/retiredstudent/' + result;
    	$.ajax({
	        url:$url,
	        success:function($data){
                if ($data.status == true) {
                	$(".modal-title").html("El Alumno fue Retirado");
                }
                else
                {
                	$(".modal-title").html("Error al Eliminar al Alumno");
                }

	        },
	        error:function($data){
	                //Pintar el error
                	$(".modal-title").html("Erro en el Servidor");
	                
	        }
	    });
    	$assist = {};
    	return true;
	}
	/*****************************asigner-asistencia***************************************/
	function assignet_assistance($obj){

		var $value = $($obj).attr('value');
		var $index = $($obj).attr('index');
		var $indexall = $($obj).attr('indexall');
		var $value_1 = $("#index-"+$indexall+"-"+$index).attr("assistance");

		/******removed class****/
		if ($value_1=="A"){
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-ok-circle");
			$("#index-"+$indexall+"-"+$index).removeClass("text-primary");
		}
		else
		{
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-unchecked");

		}
		if ($value_1=="F"){
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-remove-circle");
			$("#index-"+$indexall+"-"+$index).removeClass("text-danger");
		}else{
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-unchecked");

		}
		if ($value_1=="T"){
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-copyright-mark");
			$("#index-"+$indexall+"-"+$index).removeClass("text-success");
		}else{
			$("#index-"+$indexall+"-"+$index).removeClass("glyphicon-unchecked");
		}
		
		/*********add class*********/

		if ($value == "A"){
			$("#index-"+$indexall+"-"+$index).addClass("glyphicon-ok-circle");
			$("#index-"+$indexall+"-"+$index).addClass("text-primary");
			$("#index-"+$indexall+"-"+$index).attr("data-content","<h5><span class='assignet-assist glyphicon glyphicon-remove-circle text-danger' value='F' index='"+$index+"' indexall='"+$indexall+"'  onclick='assignet_assistance(this);'> Falto</span></h5><h5><span class='assignet-assist glyphicon glyphicon-copyright-mark text-success' value='T' index='"+$index+"' indexall='"+$indexall+"' onclick='assignet_assistance(this);'> Tarde</span></h5>");

		}
		if ($value == "F"){
			$("#index-"+$indexall+"-"+$index).addClass("glyphicon-remove-circle");
			$("#index-"+$indexall+"-"+$index).addClass("text-danger");
			$("#index-"+$indexall+"-"+$index).attr("data-content","<h5><span class='assignet-assist glyphicon glyphicon-ok-circle text-primary' value='A' index='"+$index+"' indexall='"+$indexall+"'  onclick='assignet_assistance(this);'> Asistio</span></h5><h5><span class='assignet-assist glyphicon glyphicon-copyright-mark text-success' value='T' index='"+$index+"' indexall='"+$indexall+"' onclick='assignet_assistance(this);'> Tarde</span></h5>");

		}
		if ($value == "T"){
			$("#index-"+$indexall+"-"+$index).addClass("glyphicon-copyright-mark");
			$("#index-"+$indexall+"-"+$index).addClass("text-success");
			$("#index-"+$indexall+"-"+$index).attr("data-content","<h5><span class='assignet-assist glyphicon glyphicon-ok-circle text-primary' value='A' index='"+$index+"' indexall='"+$indexall+"'  onclick='assignet_assistance(this);'> Asistio</span></h5><h5><span class='assignet-assist glyphicon glyphicon-remove-circle text-danger' value='F' index='"+$index+"' indexall='"+$indexall+"' onclick='assignet_assistance(this);'> Falto</span></h5>");

		}

		/**************assign value****************/
		$("#index-"+$indexall+"-"+$index).attr("assistance",$value);
		$("#index-tr-"+$index).attr('edition',true);
		$("#save_assistance").attr('disabled',false);
		$(".popover").remove();
	}
	/****************cierre de asistencia**************************/
	function closureassistance($objet){
		$("#toserver").modal('show');
		var result ='';
		$assist = {};

		$assist[$.base64.encode('courseid')] =$.base64.encode($.trim($($objet).attr('coursoid'))); 
		$assist[$.base64.encode('curid')] =$.base64.encode($.trim($($objet).attr('curid'))); 
		$assist[$.base64.encode('turno')] =$.base64.encode($.trim($($objet).attr('turno'))); 
		$assist[$.base64.encode('escid')] =$.base64.encode($.trim($($objet).attr('escid'))); 
		$assist[$.base64.encode('subid')] =$.base64.encode($.trim($($objet).attr('subid'))); 
		$assist[$.base64.encode('perid')] =$.base64.encode($.trim($($objet).attr('perid'))); 
		$assist[$.base64.encode('partial')] = $.base64.encode($partial); 

		for (var prop in $assist){
			result += '' + prop + '/' + $assist[prop] + '/';
		}
		var $url = '/assistance/student/closureassistance/' + result;
		$.ajax({
        	url:$url,
	        async:false,
	        success:function($data){
	        	if ($data.status == true) {
	        		$(".modal-title").html('Asistencia se Cerro Satisfactoriamente');
                     window.location.href=window.location.href;

	        	}else
	        	{
	        		$(".modal-title").html('Error al Cerrar Verifique llenado de Asistencia');
	        	}
	        },
	        error:function($data){
	                //Pintar el error
	                $(".modal-title").html('Error al Guardar En el Servidor');
	        }
        });

		// $assist = {};

	}
	/***************************guardar-archivo*****************************************/
	function savefile(){
		var result = '';
		var resuldecode  = '';

		for (var prop in $assist ) {
            result += '' + prop + '/' + $assist[prop]  + '/'; 
        }  

        result = result.substring(0, result.length-1);
        result = result + '/' + $.base64.encode('partial') + '/' + $.base64.encode($partial);
    	var $url = '/assistance/student/savefile/' + result;
    	$.ajax({
        	url:$url,
	        async:false,
	        success:function($data){

	                if ($data.status == true) {
	                	$("#save_assistance").attr('disabled',true);
	                }
	                else{

	                }
	                $tr.attr('edition',false);
	        },
	        error:function($data){
	                //Pintar el error
	                alert("error");
	                $tr.attr('edition',false);

	        }
        });

    	$assist = {};
    	return true;
	}

	$(document).ready(function() {

		$(window).scroll(function(){
	        var scroll = $(window).scrollTop();
	        if( scroll >= 146){
	            $("#header-info").addClass('data-header-info-assit');
	            $("#header-info").addClass('col-md-10');
	            $("#tb-header-info").addClass('scroll-header-info');
	            $(".text-sesion-assist").css({'padding-right':'22px'});
	        }
	        else{
	            
	            $("#header-info").removeClass('data-header-info-assit');
	            $("#header-info").removeClass('col-md-10');
	            $("#tb-header-info").removeClass('scroll-header-info');
	            
	        }
    	});


		//*********************seleccionar todo***********************///

		$(".student-assist .selectedall").click(function (){
			$(this).attr('checked',true);
			var $index_all	=	$(this).attr('index-all');
			var $indexcefa	=	-2;

			var $checkedall = $(this);

			$("#talassist tr").each(function (){
				$indexcefa = parseInt($indexcefa)-1;
				$tr = $(this);
				$action = $tr.attr('edition');

					$($tr[0].children).each(function (){

						if (this.firstElementChild != null) {
                            if(this.firstElementChild.nodeName == 'SPAN'){

                            	$checkbox = $(this.firstElementChild);
                            	$indexall = $checkbox.attr('indexall');
                            	$disabled = $checkbox.attr('disabled');
                            	$assistance = $checkbox.attr('assistance');
                            	// $checkbox.attr =
                            	if ($checkedall.is(':checked')) {
	                            	if ($indexall == $index_all && $disabled != 'disabled' ) {
										$tr.attr('edition',true);
										$checkbox.removeClass();
										$checkbox.addClass('inputassist glyphicon glyphicon-ok-circle text-primary');
	                            		$checkbox.attr('assistance','A');
	                            	}
                            	}
                            	else{
                            		if ($indexall == $index_all && $disabled != 'disabled' ) {
										$tr.attr('edition',true);
										$checkbox.removeClass();
										$checkbox.addClass('inputassist glyphicon glyphicon-unchecked');
	                            		$checkbox.attr('assistance','');
	                            	}
                            	}
							}
						}

					});
				
			});
			$("#save_assistance").attr('disabled',false);
		});

		/*********************evento-para-guardar***************************/
			$("#save_assistance").click(function(){
			$indexcefa = -2;
			var $to_send = false;

			$("#talassist tr").each(function (){
				$indexcefa = parseInt($indexcefa)-1;
				$tr = $(this);
				$action = $tr.attr('edition');

				if ($action == 'true') {

					if ($to_send == false) {
						$to_send = true;
						$("#toserver").modal('show');
					}

					$assist = {};
					$assist[$.base64.encode('courseid')]=$.base64.encode($.trim($tr.attr('courseid')));
					$assist[$.base64.encode('curid')]=$.base64.encode($.trim($tr.attr('curid')));
					$assist[$.base64.encode('turno')]=$.base64.encode($.trim($tr.attr('turno')));
					$assist[$.base64.encode('regid')]=$.base64.encode($.trim($tr.attr('regid')));
					$assist[$.base64.encode('uid')]=$.base64.encode($.trim($tr.attr('uid')));
					$assist[$.base64.encode('pid')]=$.base64.encode($.trim($tr.attr('pid')));
					$assist[$.base64.encode('perid')]=$.base64.encode($.trim($tr.attr('perid')));
					$assist[$.base64.encode('escid')]=$.base64.encode($.trim($tr.attr('escid')));
					$assist[$.base64.encode('subid')]=$.base64.encode($.trim($tr.attr('subid')));

					$information = false;
					$($tr[0].children).each(function (){

						if (this.firstElementChild != null) {
                            if(this.firstElementChild.nodeName == 'SPAN'){
                            	$checkbox = $(this.firstElementChild);
                            	$name = $checkbox.attr('name');
                            	$value = $checkbox.attr('assistance');

                            	if ($value != '') {
                            		$information = true;
                            		$assist[$.base64.encode($name)] = $.base64.encode($.trim($value));
                            	}
							}
						}

					});

					if ($information) {
						savefile();
					}
				}
			});

			if ($to_send == true) {
				$("#toserver").modal('hide');
				$("save_assistance").attr('disabled',true);
			}	
		});	

		$(".inputassist").popover();
		$(".div-tooltip").tooltip();

	});

	/*****************************/