$(document).ready(function(){


	var $unit = parseInt($.base64.decode($("#unit_hidden").val()));

	if ($unit > 1) {
		$("#footer_syllabus").append('<button type="button" class="btn btn-default pull-left" id="btn_back_unit"><span class="glyphicon glyphicon-arrow-left"></span> Anterior</button>');
	}

	var $params = {};
	$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
	$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
	$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
	$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
	$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
	$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
	$params[$.base64.encode('type_rate')]=$.trim($("#type_hidden").val());
	$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit));

	result = "";
	    for (var prop in $params ) {
	        result += '' + prop + '/' + $params[prop]  + '/'; 
	    }
	    
	var $url_temp = "/syllabus/syllabus/content/"+result;
	$.get($url_temp,function($respons){
		$("#load_content_unit").html($respons);
	});

	$("#btn_next_unit").click(function(){
		var $data = $(this).data();
		$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit+1));
		result = "";
	    for (var prop in $params ) {
	        result += '' + prop + '/' + $params[prop]  + '/'; 
	    }
		if ($data.status==1) {
			update_unit(result);
			var $url = "/syllabus/syllabus/units/"+result;
			$.get($url,function($respons){
				$("#myModalSyllabus").html($respons);
			});
		}
		else{
			save_unit(result);
		}
			
	});

	$("#btn_back_unit").click(function(){
		$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit-1));
		result = "";
	    for (var prop in $params ) {
	        result += '' + prop + '/' + $params[prop]  + '/'; 
	    }
	    var $url = "/syllabus/syllabus/units/"+result;
		$.get($url,function($respons){
			$("#myModalSyllabus").html($respons);
		});
		update_unit(result);
	});

});

function save_unit(result){
	var url="/syllabus/syllabus/units";
	$.ajax({
		url: url,
		type: 'POST',
		data:$("#frmSyllabusunits").serialize(),
		success: function ($data){
				if ($data.status == true) {
					var $url = "/syllabus/syllabus/units/"+result;
					$.get($url,function($respons){
						$("#myModalSyllabus").html($respons);
					});

				}else{
					$("#myModalSyllabus").html($data);
				}
		},
		error : function($error){
				alert("error al Guardar");
		}
	});
}
function update_unit(result){
	var url="/syllabus/syllabus/units";
	$.ajax({
		url: url,
		type: 'POST',
		data:$("#frmSyllabusunits").serialize(),
		success: function ($data){
				
		},
		error : function($error){
				alert("error al Guardar");
		}
	});
}
