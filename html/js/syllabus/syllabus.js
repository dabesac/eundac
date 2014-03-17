$(document).ready(function() {

		$("#save_sylabus").click(function(){
			save_syllabus();
		});

		var $params = {};
		$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val());
		$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val());
		$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val());
		$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val());
		$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val());
		$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
		$params[$.base64.encode('type_rate')]=$.trim($.base64.encode($type_rate));

		$("#close_sylabus").click(function(){
			$.ajax({
				url: $("#frmSyllabus").attr('action'),
				type: 'POST',
				data:$("#frmSyllabus").serialize(),
				success: function ($data){
					$("#campo").html($data);
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
	var url="/syllabus/syllabus/savemodified";
	$.ajax({
		url: url,
		type: 'POST',
		data:$("#frmSyllabus").serialize(),
		success: function ($data){
			
		},
		error : function($error){
				alert("error al Guardar");
		}
	});
}
