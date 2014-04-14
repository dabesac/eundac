$(document).ready(function() {

		$("#save_sylabus").click(save_syllabus);
		var $params = {};
		$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
		$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
		$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
		$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
		$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
		$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
		$params[$.base64.encode('type_rate')]=$.trim($.base64.encode($type_rate));

		$("#preview_syllbus").click(preview_syllbus);

		$("#close_sylabus").click(function(){
			$.ajax({
				url: "/syllabus/syllabus/closure",
				type: 'POST',
				data:$("#frmSyllabus").serialize(),
				success: function ($data){
					if ($data.status == true) {
                     window.location.href=window.location.href;
					}else{
						$("#error-syllabus").show()
					}
				},
				error: function($error){
					alert("Ocurrio un error al cerrar síllbus");
				}
			});
		});

		$("#crearunidad").click(function(){
			$params[$.base64.encode('unit')]=$.trim($.base64.encode('1'));
			if ($send_units) { save_syllabus();	}
			$.get('/syllabus/syllabus/units',$params,function($responds){
				// alert($responds);
				$("#myModalSyllabus").html($responds);
			});
		});

		$(".height").click(function(e){
		    var  $this= $(this).find('span');
		    if ($this.attr('class').match('glyphicon glyphicon-chevron-down')) {
		            $this.attr('class','glyphicon glyphicon-chevron-up');
		            e.preventDefault();
		        }
		    else $this.attr('class','glyphicon glyphicon-chevron-down');
		});

		(function($) {
		    $('textarea#sumilla,textarea#methodology,textarea#media,textarea#evaluation,textarea#sources,textarea#competency,textarea#capacity').ckeditor();
		})(jQuery);
	
});

function save_syllabus(){
	$("#loading_overlay").show();
	$(".loading_message").show();
	var url="/syllabus/syllabus/savemodified";
	$.ajax({
		url: url,
		type: 'POST',
		data:$("#frmSyllabus").serialize(),
		success: function ($data){
			$("#loading_overlay").hide();
			$(".loading_message").hide();
		},
		error : function($error){
				alert("error al Guardar");
		}
	});
}
function preview_syllbus(){
	var $params = {};
	$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
	$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
	$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
	$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
	$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
	$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
	$params[$.base64.encode('type_rate')]=$.trim($.base64.encode($type_rate));

	$.get('/syllabus/syllabus/view',$params,function($responds){
		$("#content_preview_syllbus").html($responds);
	})
}
