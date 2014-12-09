var $indicefa = -1;
	var $notas = {};
	//var $td = null;
	var $tr = null;
	var $strnota = '';
	var $grillaedicion = false;
$(document).ready(function() {
	    $(".notas input[type='text']").focusout(function(e){
        	$strnota = '';
    	});

    	$(".notas input[type='text']").click(function(){
            $(this).removeClass('error-input');
    	});

   //  	$(".notas .select_receipt").change(function(){

			// $(".select_receipt").each(function(){
			// 	alert($(this).val());
			// 	if ($(this).val() !="") {

			// 		$(".td_select").removeClass('has-error');
			// 	};		
			// });

   //  	});


		$(".notas input[type='text']").keypress(function(e){
            
            var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
                if(e.which != 8){
                    var chr = String.fromCharCode( e.which );
                    $strnota = '' + $strnota + chr + '';
                    if($strnota != ''){$nota = Number($strnota);}
                    else{$nota='';}
                    if(($nota < 0 || $nota >20) && ($nota!=-3) && ($nota!='')){
                        $strnota = '';
                        $(this).val('');
                        e.preventDefault();
                    }
                }
                
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);

                $tr.attr("edicion",true); 
                
                $index = $(this).attr("index");

                $("#edit_nota_" + $index).addClass("glyphicon-edit");

                $("#save_notas").attr("disabled",false);
            }
    	});

    	$("#save_notas").click(function(){
    		$indicefa = -2;
            var $envioserver = false;
            $("#tbnotas tr").each(function(){
                $indicefa = parseInt($indicefa)  + 1;
                $tr = $(this);
                $action = $tr.attr('edicion');
                if($action == "true") {

    	          	if ($envioserver == false ) {
                		$envioserver = true;	
                	}

                	$notas ={};
                	$notas[$.base64.encode('courseid')]= $.base64.encode($.trim($tr.attr("courseid")));
			    	$notas[$.base64.encode('curid')]   = $.base64.encode($.trim($tr.attr("curid")));
			    	$notas[$.base64.encode('turno')]    = $.base64.encode($.trim($tr.attr("turno")));
			    	$notas[$.base64.encode('perid')]     = $.base64.encode($.trim($tr.attr("perid")));
			    	$notas[$.base64.encode('escid')]     = $.base64.encode($.trim($tr.attr("escid")));
			    	$notas[$.base64.encode('subid')]     = $.base64.encode($.trim($tr.attr("subid")));
			    	$notas[$.base64.encode('regid')]     = $.base64.encode($.trim($tr.attr("regid")));
			    	$notas[$.base64.encode('uid')]        = $.base64.encode($.trim($tr.attr("uid")));
			    	$notas[$.base64.encode('pid')]        = $.base64.encode($.trim($tr.attr("pid")));

			    	$informacion = false;
			    	$($tr[0].children).each(function () {
			    		if (this.firstElementChild != null) {
			    			if (this.firstElementChild.nodeName == "INPUT" || this.firstElementChild.nodeName =='SELECT') {

			    				$input= $(this.firstElementChild);
                    			$name = $input.attr("name");
                    			$valor = $input.val();

                    			if($valor != '')
                    			{
                    				$informacion = true;
                    				$notas[$.base64.encode($name)] = $.base64.encode($.trim($valor));
                    			}
                    			else
                    			{
                    				if ($name == 'receipt') 
                					{
                						$(".td_select_"+$indicefa).addClass('has-error');
                					};
                    				if ($name == 'notafinal') 
                					{
                						$($input).addClass('error-input');
                					};
                    			}
			    			}
			    		}
			    	});

			    	if($informacion){
			    		savefile();
			    	}
   	
                }

            });

            if($envioserver==true){
	            $("#save_notas").attr("disabled",true);
            }
    	});
		
		$("#closed_record").click(function (){
			
			var $record = {};

			// alert($("#recordcourseid").val());
			$record[$.base64.encode('courseid')] = $.base64.encode($("#record-courseid").val());
			$record[$.base64.encode('turno')] = $.base64.encode($("#record-turno").val());
			$record[$.base64.encode('curid')] = $.base64.encode($("#record-curid").val());
			$record[$.base64.encode('escid')] = $.base64.encode($("#record-escid").val());
			$record[$.base64.encode('subid')] = $.base64.encode($("#record-subid").val());
			$record[$.base64.encode('perid')] = $.base64.encode($("#record-perid").val());
			
			var result = '';

			for( var prop in $record){
                result += '' + prop + '/' + $record[prop]  + '/';
			}
            result = result.substring(0, result.length-1);
            var $url = '/register/deferred/closerecord/' + result;

    	    $.ajax({
		        url:$url,
		        success:function($data){

	                if($data.status == true){
	                    //Pintar el exito
	                    window.location.href = window.location.href;	
	                    $("#edit_nota_" + $temporaindex).addClass('glyphicon-ok-circle');
	                    $subject.css("color", "none");
	                }else{
	                    
                			$("#tbnotas tr").each(function(){
			                $indicefa = parseInt($indicefa)  + 1;
			                $tr = $(this);
			                $action = $tr.attr('edicion');
			                if($action == "false"){

					    	$informacion = false;
						    	$($tr[0].children).each(function () {
						    		if (this.firstElementChild != null) {
						    			if (this.firstElementChild.nodeName == "INPUT" || this.firstElementChild.nodeName =='SELECT') {

							   				$input= $(this.firstElementChild);
			                    			$name = $input.attr("name");
			                    			$valor = $input.val();
			                    			if($valor != "")
			                    			{

			                    			}
			                    			else
			                    			{
			                    				if ($name == 'receipt') 
			                					{
			                						$(".td_select_"+$indicefa).addClass('has-error');
			                					};
			                    				if ($name == 'notafinal') 
			                					{
			                						$($input).addClass('error-input');
			                					};

			                    			}
			                    			
						    			}
						    		}
						    	});
			                }

			            });
	                }
		                // $('#modalloader').modal('hide');
		        },
		        error:function($data){
		        	alert("error");
		        }
	   		});

		});

	});
	function not_present (obj) {
		var $icon = $(obj);
		var $subject = $icon.parent();
		var $subject = $subject.parent();
		var $action = $(obj).attr('action');
		
		var $index =$icon.attr("index");

		if($action == "0")
		{
			$icon.removeClass('glyphicon-eye-open');
			$icon.addClass('glyphicon-eye-close');
			$subject.css('color','#FC4141');
        	$icon.attr("action","1");
		}
		else{

			$icon.removeClass('glyphicon-eye-close');
			$icon.addClass('glyphicon-eye-open');
			$subject.css('color','#000');
        	$icon.attr("action","0");
		}

		$notas = {};
    	$notas[$.base64.encode('courseid')]        = $.base64.encode($.trim($subject.attr("courseid")));
    	$notas[$.base64.encode('curid')]        = $.base64.encode($.trim($subject.attr("curid")));
    	$notas[$.base64.encode('turno')]        = $.base64.encode($.trim($subject.attr("turno")));
    	$notas[$.base64.encode('perid')]        = $.base64.encode($.trim($subject.attr("perid")));
    	$notas[$.base64.encode('escid')]        = $.base64.encode($.trim($subject.attr("escid")));
    	$notas[$.base64.encode('subid')]        = $.base64.encode($.trim($subject.attr("subid")));
    	$notas[$.base64.encode('regid')]        = $.base64.encode($.trim($subject.attr("regid")));
    	$notas[$.base64.encode('uid')]        = $.base64.encode($.trim($subject.attr("uid")));
    	$notas[$.base64.encode('pid')]        = $.base64.encode($.trim($subject.attr("pid")));
    	

		$($subject[0].children).each(function (){
			if (this.firstElementChild != null) {
				if (this.firstElementChild.nodeName == "INPUT" || this.firstElementChild.nodeName =='SELECT') {

					$input= $(this.firstElementChild);
                    $name = $input.attr("name");

                    if ($action == "0") {
                    	$input.attr('disabled',true);
                        $($input).css("color", "#FC4141");
                        $notas[$.base64.encode("receipt")] = $.base64.encode("");
                        $notas[$.base64.encode("notafinal")] = $.base64.encode("-2");
                        if ($name == "notafinal") {
                        	$input.val("NP");	
                        }
                    }
                    else
                    {	
                        $notas[$.base64.encode("receipt")] = $.base64.encode("");
                    	$notas[$.base64.encode("notafinal")]=$.base64.encode("");
                    	$input.css("color","#000");
                    	$input.attr('disabled',false);
                    	$input.val('');
                    }
				}
			}
		});
		

	    result = "";
	    for (var prop in $notas ) {
	        result += '' + prop + '/' + $notas[prop]  + '/'; 
	    }
	    result = result.substring(0, result.length-1);
	    var $url = '/register/deferred/loadnotas/' + result;
	    // $('#modalloader').modal('show');
	    $temporaindex = $index;

	    $.ajax({
	        url:$url,
	        success:function($data){
	                if($data.status == true){
	                    //Pintar el exito
	                    $("#edit_nota_" + $temporaindex).addClass('glyphicon-ok-circle');
	                    $subject.css("color", "none");
	                }else{
	                    //Pintar Fracaso
	                    $("#edit_nota_" + $temporaindex).attr("src","");
	                    $subject.css("color", "#FC4141");
	                }
	                // $('#modalloader').modal('hide');
	        },
	        error:function($data){
	        	$subject.css("color", "#FC4141");
                $("#edit_nota_" + $index).attr("src","");
	        }
	    });
		$notas = {};
    	return true;
	}

	function savefile(){
		var result = '' ;
        var resultdecode = '';
        var $numnotas   =  0;
        var $suma = 0

        for (var prop in $notas ) {
            resultdecode += '' + $.base64.decode(prop) + '/' + $.base64.decode($notas[prop])  + '/'; 
        }

    
        
        if($notas[$.base64.encode('receipt')]){ 
        	$notas[$.base64.encode('receipt')]=$notas[$.base64.encode('receipt')];
        }
        if($notas[$.base64.encode('notafinal')]){ 
        	$notas[$.base64.encode('notafinal')]=$notas[$.base64.encode('notafinal')];
        }
  		
        for (var prop in $notas ) {
            result += '' + prop + '/' + $notas[prop]  + '/'; 
        }
      	result = result.substring(0, result.length-1);
	    var $url = '/register/deferred/loadnotas/' + result;

        if (resultdecode.toLowerCase().indexOf("nota") >= 0){
	    	$temporaindex = $indicefa;
        	$.ajax({
	        url:$url,
            async:false,
	        success:function($data){
	                if($data.status == true){
	                    //Pintar el exito
	                    $("#edit_nota_" + $temporaindex).addClass('glyphicon-ok-circle');
	                    $tr.css("color", "none");

	                }else{
	                    //Pintar Fracaso
	                    $("#edit_nota_" + $temporaindex).attr("src","");
	                    $tr.css("color", "#FC4141");
	                }
                    $tr.attr("edicion",false); 

	                // $('#modalloader').modal('hide');
	        },
	        error:function($data){
	        	$tr.css("color", "#FC4141");
                $("#edit_nota_" + $temporaindex).attr("src","");
                $tr.attr("edicion",false); 
	        }
	    	});
        }

        $notas = {};
    	return true;
	}
