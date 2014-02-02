$(document).ready(function(){

	$(".turno-fill-col").change(function(){
		$index = $(this).attr('index');
		$checked_all = $(this)

		$(this).each(function(){
			if ($(this).prop('checked')) {
				$(this).checked = true;
			}else{
				$(this).checked = false;
			}
		});
		var $indexcefa	=	-2;

		$("#table_list_course_dist tr").each(function(){
			$indexcefa = parseInt($indexcefa)-1;
			$tr = $(this);
			$($tr[0].children).each(function(){
				if (this.firstElementChild != null) {
					if (this.firstElementChild.nodeName == "INPUT") {
						$checkbox = $(this.firstElementChild);
						if ($index == $checkbox.attr('index')) {
							if (!$checkbox.prop('checked')) {
								$checkbox.prop('checked',true);
							}else{
								$checkbox.prop('checked',false);
							}
						};
					};
				};
			});
		});
		
	});

	$(".edit-type-rate").click(function(){
		var $td = $(this).parent();
		var $tr = $td.parent();
		if ( $(this).val() != '') {
			_save($(this).val(),$tr);
		}
	});

	$("#edit-type-rate-all").click(function(){
		var $value = $(this).val();
		var $indexcefa	=	-2;

		if ($value != "") {
			$("#courses_distribution_list tr").each(function(){
				$indexcefa = parseInt($indexcefa)-1;
				$tr = $(this);
				$send = false;
				$($tr[0].children).each(function(){
					if(this.firstElementChild != null){
						if (this.firstElementChild.nodeName == "SELECT") {
                            $element = $(this.firstElementChild);
                            $element.val($value);
                            $send = true;
						}
					}
				});

				if ($send) {
					_save($value,$tr);
				};
			});
		}
	});

});

function _save($value,$parent){


	var $params = {};
	$params[$.base64.encode('curid')]	= $.base64.encode($.trim($parent.attr('curid')));	 
	$params[$.base64.encode('courseid')]	= $.base64.encode($.trim($parent.attr('courseid')));	 
	$params[$.base64.encode('turno')]	= $.base64.encode($.trim($parent.attr('turno')));	 
	$params[$.base64.encode('escid')]	= $.base64.encode($.trim($parent.attr('escid')));	 
	$params[$.base64.encode('subid')]	= $.base64.encode($.trim($parent.attr('subid')));	 
	$params[$.base64.encode('distid')]	= $.base64.encode($.trim($parent.attr('distid')));	 
	$params[$.base64.encode('perid')]	= $.base64.encode($.trim($parent.attr('perid')));	 
	$params[$.base64.encode('type_rate')]	= $.base64.encode($.trim($value));	 

	result =	'';
	for (var prop in $params ) {
	        result += '' + prop + '/' + $params[prop]  + '/'; 
	    }
	result = result.substring(0, result.length-1);
	var url	=	"/distribution/distribution/modifytypecourse/"+result;
	$.ajax({
		url : url,
		async:false,
		success: function($data){
			if ($data.status == true ) {

			};
		},
		error: function ($error){
			alert("!Vaya¡ Ocurrio un Error al Cambiar Tipo de Curso");
		}
	});
	$params = {};
	return true;		  
}