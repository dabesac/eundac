$(document).ready(function(){
	var $send = false;

	var $value_1 = $("#name").val();
	var $value_2 = $("#objetive").val();
	if ($value_1 !='' && $value_2 !='') {
		$("#add_units_content").attr('disabled',false);
	}

	$("#name, #objetive").keypress(function(){
		$send = true;
		if ($(this).val() !='') {
			$("#add_units_content").attr('disabled', false)
		}else{
			$("#add_units_content").attr('disabled', true)
		}
	});


	$("#name, #objetive").keyup(function(){
		$send = true;
		if ($(this).val() !='') {
			$("#add_units_content").attr('disabled', false)
		}else{
			$("#add_units_content").attr('disabled', true)
		}
	});

    $("#add_units_content").click(function(){
		var $week = $("#week").val();
		var $session = $("#session").val();
		var $new_row = false;
		var $count = 0;
		var $long_tr = $('#table_units_content tbody tr').length;

		if ($long_tr > '0') {
			$('#table_units_content tbody tr').each(function(){
				var $data = $(this).data();
				var $tr_week = $data.week;
				var $tr_session = $data.session;
				if ($session != $tr_session) {
					$new_row = true;
				}else{
					$count ++;
				}
			});
			if ($new_row && $count == 0) {
				if ($send) {
					addRow_and_units($week,$session);
				}else{
					addNewRow($week,$session);
				}
			}else{

			}

		}else{
			if ($send) {
				addRow_and_units($week,$session);
			}else{
				addNewRow($week,$session);
			}
		}
		
    });

    $("#table_units_content tbody tr").click(function(){
    	$(this).off('click');
    	if ($type_rate == 'O') {
    		$value_1 = $(this).find('td').eq(2).text();
    		$value_2 = $(this).find('td').eq(3).text();
    		$value_3 = $(this).find('td').eq(4).text();
    		$value_4 = $(this).find('td').eq(5).text();
    		$(this).find('td').eq(2).text('');
    		$(this).find('td').eq(3).text('');
    		$(this).find('td').eq(4).text('');
    		$(this).find('td').eq(5).text('');

    		$(this).find('td').eq(2).append('<input type="text" class="form-control" value="'+$value_1+'" name="obj_content" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(3).append('<input type="text" class="form-control" value="'+$value_2+'" name="obj_strategy" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(4).append('<input type="text" class="form-control" value="'+$value_3+'" name="com_indicators" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(5).append('<input type="text" class="form-control" value="'+$value_4+'" name="com_instruments" placeholder="Ingrese Contenido"> ');
	    	$obj = $(this).find('td').eq(6).find('span').show();
    	}
    	if ($type_rate == 'C') {
    		$value_1 = $(this).find('td').eq(2).text();
    		$value_2 = $(this).find('td').eq(3).text();
    		$value_3 = $(this).find('td').eq(4).text();
    		$value_4 = $(this).find('td').eq(5).text();
    		$value_5 = $(this).find('td').eq(6).text();
    		$(this).find('td').eq(2).text('')
    		$(this).find('td').eq(3).text('')
    		$(this).find('td').eq(4).text('')
    		$(this).find('td').eq(5).text('')
    		$(this).find('td').eq(6).text('')
    		$(this).find('td').eq(2).append('<input type="text" class="form-control" value="'+$value_1+'" name="com_conceptual" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(3).append('<input type="text" class="form-control" value="'+$value_2+'" name="com_procedimental" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(4).append('<input type="text" class="form-control" value="'+$value_3+'" name="com_actitudinal" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(5).append('<input type="text" class="form-control" value="'+$value_4+'" name="com_indicators" placeholder="Ingrese Contenido"> ');
	    	$(this).find('td').eq(6).append('<input type="text" class="form-control" value="'+$value_4+'" name="com_instruments" placeholder="Ingrese Contenido"> ');

	    	$obj = $(this).find('td').eq(7).find('span').show();
    	}

    	// var $tr = $(this).parent();
    	// var $data = $tr.data();
    	// console.log($data);
    });
	
	$(".save-edit-unit-content").click(function(){
		var $td = $(this).parent();
		var $tr = $td.parent();
		var $data = $tr.data();

		$($tr[0].children).each(function(){
			if(this.firstElementChild != null){
	            if(this.firstElementChild.nodeName == 'INPUT'){
	            	$input  = $(this.firstElementChild);
                    $name = $input.attr("name");
                    $data[$name]=$input.val();
	            }
	        }
		});

		$data['subid']=$.trim($.base64.decode($("#subid_hidden").val()));
		$data['perid']=$.trim($.base64.decode($("#perid_hidden").val()));
		$data['escid']=$.trim($.base64.decode($("#escid_hidden").val()));
		$data['curid']=$.trim($.base64.decode($("#curid_hidden").val()));
		$data['courseid']=$.trim($.base64.decode($("#courseid_hidden").val()));
		$data['turno']=$.trim($.base64.decode($("#turno_hidden").val()));
		$data['unit']=$.trim($.base64.decode($("#unit_hidden").val()));
		$data['type_rate']=$.trim($.base64.decode($("#type_hidden").val()));

		$.ajax({
			url:"/syllabus/syllabus/modifycontent",
			type: 'POST',
			data:$data,
			success: function($respons){
				if ($respons.status==true) {
					load_contet();
				}
			},
			error:function(){
				alert("Error al Modificar")
			}
		});

	});
	
	$(".delete-unit-content").click(function(){
		var $td = $(this).parent();
		var $tr = $td.parent();
		var $data = $tr.data();
		var $params = {};

		$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
		$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
		$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
		$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
		$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
		$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
		$params[$.base64.encode('unit')]=$.trim($("#unit_hidden").val());
		$params[$.base64.encode('week')]=$.trim($.base64.encode($data.week));
		$params[$.base64.encode('session')]=$.trim($.base64.encode($data.session));

		$.ajax({
			url:"/syllabus/syllabus/deletecontent/",
			data:$params,
			success: function($respons){
				if ($respons.status==true) {
					load_contet();
				}
			},
			error:function(){
				alert("Error al Eliminar")
			}
		});
	});
	
});

function addNewRow($week,$session){
	$.ajax({
		url:"/syllabus/syllabus/addcontent",
		data:$("#form_units_content").serialize(),
		type:'POST',
		success:function($respons){

			if ($respons.status == true) {
				load_contet();
			}
		},
		error:function($error){
			alert("Error al Guardar");
		}
	});
}	
function addRow_and_units($week,$session){
	var url="/syllabus/syllabus/units";
	$.ajax({
		url: url,
		type: 'POST',
		data:$("#frmSyllabusunits").serialize(),
		success: function ($data){
				addNewRow();
		},
		error : function($error){
				alert("error al Guardar");
		}
	});
}
function load_contet(){
	var $params = {};
	$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
	$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
	$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
	$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
	$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
	$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
	$params[$.base64.encode('unit')]=$.trim($("#unit_hidden").val());
	$params[$.base64.encode('type_rate')]=$.trim($("#type_hidden").val());

	result = "";
	    for (var prop in $params ) {
	        result += '' + prop + '/' + $params[prop]  + '/'; 
	    }
	var $url_temp = "/syllabus/syllabus/content/"+result;
	$.get($url_temp,function($respons){
		$("#load_content_unit").html($respons);
	});
}
