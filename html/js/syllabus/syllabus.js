var perid= $.base64.encode($("#syl_perid_hidd").val())
var subid= $.base64.encode($("#syl_subid_hidd").val())
var turno= $.base64.encode($("#syl_turno_hidd").val())
var courseid= $.base64.encode($("#syl_courseid_hidd").val())
var curid= $.base64.encode($("#syl_curid_hidd").val())
var escid= $.base64.encode($("#syl_escid_hidd").val())
var $type_rate 
var send_units = false, alert= $('<span style="font-size:20px;" class="glyphicon" ></span>')
var $params = {};
var $unit 
var send = false
var cont_alert = $('<div style="display:none;" class="alert fade in">')
var bnt_close_alert= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
// cont_alert.append(bnt_close_alert)

var syllabus = {
	init:function(){
		// $('#campo').load("/syllabus/syllabus/viewfrm/"+url)
		$('#campo').html("</br></br><center><img src='/img/spinner.gif'> Cargando...</center>")
		$("#campo").load("/syllabus/syllabus/viewfrm/perid/"+perid+"/subid/"+subid+"/turno/"+turno+"/courseid/"+courseid+"/curid/"+curid+"/escid/"+escid,syllabus.created_units)
		$("#save_sylabus").click(syllabus.save_content_syllabus)
		$("#close_sylabus").click(syllabus.close_sylabus)
		$("#Modalpreviewsyllabus").click(syllabus.preview_syllbus)
	},
	created_units:function(){
	    $('textarea#sumilla,textarea#methodology,textarea#media,textarea#evaluation,textarea#sources,textarea#competency,textarea#capacity').ckeditor();
		$("#units").css({'width':'50px','float':'left'})
		$("#units").keyup(syllabus.modified_cant_units)
		$type_rate = $.trim($.base64.decode($("#type_hidden").val()))
		$("#crearunidad").click(syllabus.load_modal_units)

		$params[$.base64.encode('subid')]=$.trim($("#subid_hidden").val())
		$params[$.base64.encode('perid')]=$.trim($("#perid_hidden").val())
		$params[$.base64.encode('escid')]=$.trim($("#escid_hidden").val())
		$params[$.base64.encode('curid')]=$.trim($("#curid_hidden").val())
		$params[$.base64.encode('courseid')]=$.trim($("#courseid_hidden").val())
		$params[$.base64.encode('turno')]=$.trim($("#turno_hidden").val());
		$params[$.base64.encode('type_rate')]=$.trim($.base64.encode($type_rate))
		$params[$.base64.encode('unit')]=$.trim($.base64.encode('1'))

	},
	modified_cant_units:function(){ 
		$(this).after(alert)
		if($(this).val() < 6 && $(this).val() != ''){
			send_units = true 
			class_alert="glyphicon-ok-circle text-primary"
	    	alert.removeClass('glyphicon-remove-circle text-danger')
			$("#crearunidad").removeClass('disabled')
			$("#save_sylabus").removeClass('disabled')
			
		}else{
			send_units = false
	    	alert.removeClass('glyphicon-ok-circle text-primary')
			class_alert="glyphicon-remove-circle text-danger"
			$("#crearunidad").addClass('disabled')
			$("#save_sylabus").addClass('disabled')
			// $("#undac-menu").after($('<div class="alert alert-danger">').text("jsdfdibfdibfduibouou"))
		}
    	alert.addClass(class_alert)
	},
	load_modal_units:function(){
		if (send_units) {
			syllabus.save_content_syllabus_unit($params)
		}else{
			$.get('/syllabus/syllabus/units',$params,syllabus.modal_content)
		}
	},
	modal_content:function($responds){
		$("#myModalSyllabus").html($responds)
		var $unit = parseInt($.base64.decode($("#unit_hidden").val()))

		if ($unit > 1) {
			$("#footer_syllabus").append('<button type="button" class="btn btn-default pull-left" id="btn_back_unit"><span class="glyphicon glyphicon-arrow-left"></span> Anterior</button>');
		}
		syllabus.load_unit_content($unit)
		$("#btn_next_unit").click(syllabus.next_units)
		$("#btn_back_unit").click(syllabus.back_units)
	},
	next_units:function(){
		var $unit = parseInt($.base64.decode($("#unit_hidden").val()))
		$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit+1))
		syllabus.update_unit()
	},
	back_units:function(){
		var $unit = parseInt($.base64.decode($("#unit_hidden").val()))
		$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit-1))
		syllabus.update_unit()
	},

	update_unit:function(){
		var url="/syllabus/syllabus/units";
		$.ajax({
			url: url,
			type: 'POST',
			data:$("#frmSyllabusunits").serialize(),
			success:syllabus.updated_united,
			error : function($error){
				console.log($error)
					// alert("error al Guardar");
			}
		})
	},
	updated_united:function($data){
		if ($data.status) {
			syllabus.load_modal_units()
		}else{
			var $unit = parseInt($.base64.decode($("#unit_hidden").val()))
			syllabus.modal_content($data)
		}
	},
	save_unit:function(){
		var url="/syllabus/syllabus/units";
		$.ajax({
			url: url,
			type: 'POST',
			data:$("#frmSyllabusunits").serialize(),
			success:syllabus.save_unit_send,
			error : function($error){
				console.log($error)
					// alert("error al Guardar");
			}
		})
	},
	save_unit_send:function($data){
		if ($data.status) {
			send = false
			if ($type_rate == 'O') {
				syllabus.add_units_content_object()
		    }
		    if ($type_rate == 'C') {
				syllabus.add_units_content_competition()
		    }
		}else
		{
			console.log("Error al Insertar Uinidad")
		}
	},
	load_unit_content:function($unit){
		syllabus.config ={
			$content_O:'',
			$content_C:'',
			$param:{
				escid:$.trim($.base64.decode($("#escid_hidden").val())),
				subid:$.trim($.base64.decode($("#subid_hidden").val())),
				curid:$.trim($.base64.decode($("#curid_hidden").val())),
				courseid:$.trim($.base64.decode($("#courseid_hidden").val())),
				turno:$.trim($.base64.decode($("#turno_hidden").val())),
				perid:$.trim($.base64.decode($("#perid_hidden").val())),
				unit:$unit,
				type_rate:$.trim($type_rate)
			}
		}
		$params[$.base64.encode('unit')]=$.trim($.base64.encode($unit))
		$('#load_content_unit').html("</br></br><center><img src='/img/spinner.gif'> Cargando...</center>")
		$.get("/syllabus/syllabus/content/",$params,syllabus.content_units_init)
	},
	content_units_init:function($responds){
		$("#load_content_unit").html($responds)
		$("#week,#session").css({'width':'67px'})

		var $value_1 = $("#name").val()
		var $value_2 = $("#objetive").val()

		if ($value_1 !='' && $value_2 !='') {
			$("#add_units_content").attr('disabled',false)
		}
		$("#name, #objetive").keyup(function(){
			if ($(this).val() !='') {
				send = true
				$("#add_units_content").attr('disabled', false)
			}else{
				send = false
				$("#add_units_content").attr('disabled', true)
			}
		})

	    	$("#add_units_content").bind('click',syllabus.save_content_unit)
	    
		$(".delete-unit-content").bind('click',syllabus.deleted_content_unit)
		$(".edit-unit-content").bind('click',syllabus.active_tr_edit)
		$(".save-edit-unit-content").bind('click',syllabus.save_edit_content_unit)
	},
	save_content_unit:function(){
		if (send) {
			console.log("true")
			syllabus.save_unit()
		}else{
			if ($type_rate == 'O') {
				syllabus.add_units_content_object()
		    }

		    if ($type_rate == 'C') {
				syllabus.add_units_content_competition()
		    }
		}
	},
	add_units_content_competition:function(){
		syllabus.config.$data_new = syllabus.config.$param
		var tr = $("#form_units_content")
		syllabus.config.$data_new.week =tr.children('td:nth-child(1)').children('select').val()
		syllabus.config.$data_new.session = tr.children('td:nth-child(2)').children("select").val()
		syllabus.config.$data_new.com_conceptual = tr.children('td:nth-child(3)').children("input[type=text]").val()
		syllabus.config.$data_new.com_procedimental = tr.children('td:nth-child(4)').children("input[type=text]").val()
		syllabus.config.$data_new.com_actitudinal = tr.children('td:nth-child(5)').children("input[type=text]").val()
		syllabus.config.$data_new.com_indicators = tr.children('td:nth-child(6)').children("input[type=text]").val()
		syllabus.config.$data_new.com_instruments = tr.children('td:nth-child(7)').children("input[type=text]").val()

		syllabus.save_add_content_unit(syllabus.config.$data_new)
	},
	add_units_content_object:function(){
		syllabus.config.$data_new = syllabus.config.$param
		var tr = $("#form_units_content")
		syllabus.config.$data_new.week =tr.children('td:nth-child(1)').children('select').val()
		syllabus.config.$data_new.session = tr.children('td:nth-child(2)').children("select").val()
		syllabus.config.$data_new.obj_content = tr.children('td:nth-child(3)').children("input[type=text]").val()
		syllabus.config.$data_new.obj_strategy = tr.children('td:nth-child(4)').children("input[type=text]").val()
		syllabus.config.$data_new.com_indicators = tr.children('td:nth-child(5)').children("input[type=text]").val()
		syllabus.config.$data_new.com_instruments = tr.children('td:nth-child(6)').children("input[type=text]").val()

		syllabus.save_add_content_unit(syllabus.config.$data_new)
	},
	save_add_content_unit:function($data){
		$.ajax({
			url:"/syllabus/syllabus/addcontent",
			data:$data,
			type:'POST',
			success:syllabus.success_save_content,
			error:function($error){
				console.log($error)
				// alert("Error al Guardar")
			}
		})
	},
	success_save_content:function($responds){
		$("#content_syllabus_unit div.modal-header").append(cont_alert)
		if ($responds.status) {
			syllabus.table_contnt_add_row(syllabus.config.$data_new)
		}else{	
			if ($responds.error==1) {
				console.log("Se la session existe")
				// cont_alert.addClass("alert-danger").append(
				// 	bnt_close_alert,
				// 	$('<strong>Ocurrio un error !</strong>'),
				// 	$('<span>').text(' la sesión selecionda ya Existe.')
				// ).show()
			}
		}
	},
	table_contnt_add_row:function($data){
		if ($type_rate == 'O') {
			$("#table_units_content tbody").append(
				$("<tr class='td-edit-content' data-week='"+$data.week+"' data-session='"+$data.session+"'>").append(
					$("<td>").text($data.week),
					$("<td>").text($data.session),
					$("<td>").text($data.obj_content),
					$("<td>").text($data.obj_strategy),
					$("<td>").text($data.com_indicators),
					$("<td>").text($data.com_instruments),
					$("<td>").addClass('text-warning').append($('<span  class="glyphicon glyphicon-pencil edit-unit-content"></span>')),
					$("<td>").addClass('text-primary').append($('<span class="delete-unit-content glyphicon glyphicon-trash"></span>'))
				)
			)
			$("#obj_content").val('')
			$("#obj_strategy").val('')
			$("#com_indicators").val('')
			$("#com_instruments").val('')
		}
		if ($type_rate == "C") {
			$("#table_units_content tbody").append(
				$("<tr class='td-edit-content' data-week='"+$data.week+"' data-session='"+$data.session+"'>").append(
					$("<td>").text($data.week),
					$("<td>").text($data.session),
					$("<td>").text($data.com_conceptual),
					$("<td>").text($data.com_procedimental),
					$("<td>").text($data.com_actitudinal),
					$("<td>").text($data.com_indicators),
					$("<td>").text($data.com_instruments),
					$("<td>").addClass('text-warning').append($('<span class="glyphicon glyphicon-pencil edit-unit-content"></span>')),
					$("<td>").addClass('text-primary').append($('<span class="delete-unit-content glyphicon glyphicon-trash"></span>'))
				)
			)
			$("#com_conceptual").val('')
			$("#com_procedimental").val('')
			$("#com_actitudinal").val('')
			$("#com_indicators").val('')
			$("#com_instruments").val('')
		}
		$(".delete-unit-content").bind('click',syllabus.deleted_content_unit)
		$(".edit-unit-content").bind('click',syllabus.active_tr_edit)
		$(".save-edit-unit-content").bind('click',syllabus.save_edit_content_unit)

		// $("#table_units_content tbody tr").bind('click',syllabus.active_tr_edit)
	},
	active_tr_edit:function(){
		var tr = $(this).parent().parent()
		var td_week =tr.children('td:nth-child(1)')
		var td_session =tr.children('td:nth-child(2)')
		if($type_rate == 'C'){
			var td_conceptual = tr.children('td:nth-child(3)')
			var td_procedimental = tr.children('td:nth-child(4)')
			var td_actitudinal = tr.children('td:nth-child(5)')
			var td_indicators = tr.children('td:nth-child(6)')
			var td_instruments = tr.children('td:nth-child(7)')
			var td_btn = tr.children('td:nth-child(8)')

			td_conceptual.html('<input class="form-control" type="text" value= "'+td_conceptual.text()+'"/>')
			td_procedimental.html('<input class="form-control" type="text" value= "'+td_procedimental.text()+'"/>')
			td_actitudinal.html('<input class="form-control" type="text" value= "'+td_actitudinal.text()+'"/>')
			td_indicators.html('<input class="form-control" type="text" value= "'+td_indicators.text()+'"/>')
			td_instruments.html('<input class="form-control" type="text" value= "'+td_instruments.text()+'"/>')
			td_btn.html('<span class="glyphicon glyphicon-floppy-disk save-edit-unit-content"></span>')
		}
		if ($type_rate == 'O') {
			var td_content = tr.children('td:nth-child(3)')
			var td_strategy = tr.children('td:nth-child(4)')
			var td_indicators = tr.children('td:nth-child(5)')
			var td_instruments = tr.children('td:nth-child(6)')
			var td_btn = tr.children('td:nth-child(7)')

			td_content.html('<input class="form-control" type="text" value="'+td_content.text()+'"/>')
			td_strategy.html('<input class="form-control" type="text" value="'+td_strategy.text()+'"/>')
			td_indicators.html('<input class="form-control" type="text" value="'+td_indicators.text()+'"/>')
			td_instruments.html('<input class="form-control" type="text" value="'+td_instruments.text()+'"/>')
			td_btn.html('<span class="glyphicon glyphicon-floppy-disk save-edit-unit-content"></span>')

		}
		$(".delete-unit-content").bind('click',syllabus.deleted_content_unit)
		$(".edit-unit-content").bind('click',syllabus.active_tr_edit)
		$(".save-edit-unit-content").bind('click',syllabus.save_edit_content_unit)
	},
	save_edit_content_unit:function(){
		var tr =$(this).parent().parent()
		var $data = tr.data()
		syllabus.config.$element = tr
		syllabus.config.$edit_data = syllabus.config.$param
		syllabus.config.$edit_data.week =$data.week
		syllabus.config.$edit_data.session = $data.session
			if($type_rate == 'C'){
			// var td_conceptual = tr.children('td:nth-child(3)')
			// var td_procedimental = tr.children('td:nth-child(4)')
			// var td_actitudinal = tr.children('td:nth-child(5)')
			// var td_indicators = tr.children('td:nth-child(6)')
			// var td_instruments = tr.children('td:nth-child(7)')
			syllabus.config.$edit_data.com_conceptual = tr.children('td:nth-child(3)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_procedimental = tr.children('td:nth-child(4)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_actitudinal = tr.children('td:nth-child(5)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_indicators = tr.children('td:nth-child(6)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_instruments = tr.children('td:nth-child(7)').children("input[type=text]").val()
			// td_conceptual.html(td_conceptual.children('input[type=text]').val())
			// td_procedimental.html(td_procedimental.children('input[type=text]').val())
			// td_actitudinal.html(td_actitudinal.children('input[type=text]').val())
			// td_indicators.html(td_indicators.children('input[type=text]').val())
			// td_instruments.html(td_instruments.children('input[type=text]').val())
		}
		if ($type_rate == 'O') {
			syllabus.config.$edit_data.obj_content = tr.children('td:nth-child(3)').children("input[type=text]").val()
			syllabus.config.$edit_data.obj_strategy = tr.children('td:nth-child(4)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_indicators = tr.children('td:nth-child(5)').children("input[type=text]").val()
			syllabus.config.$edit_data.com_instruments = tr.children('td:nth-child(6)').children("input[type=text]").val()
			// var td_content = tr.children('td:nth-child(3)')
			// var td_strategy = tr.children('td:nth-child(4)')
			// var td_indicators = tr.children('td:nth-child(5)')
			// var td_instruments = tr.children('td:nth-child(6)')
			// var td_btn = tr.children('td:nth-child(7)')

			// td_content.html('<input class="form-control" type="text" value="'+td_content.text()+'"/>')
			// td_strategy.html('<input class="form-control" type="text" value="'+td_strategy.text()+'"/>')
			// td_indicators.html('<input class="form-control" type="text" value="'+td_indicators.text()+'"/>')
			// td_instruments.html('<input class="form-control" type="text" value="'+td_instruments.text()+'"/>')
			// td_btn.html('<span class="glyphicon glyphicon-floppy-disk save-edit-unit-content"></span>')

		}
		syllabus.send_save_edit_content_unit(syllabus.config.$edit_data)
	},
	send_save_edit_content_unit:function($data){
		// console.log($data)
		$.ajax({
			url:"/syllabus/syllabus/modifycontent",
			type: 'POST',
			data:$data,
			success:syllabus.success_save_edit_content_unit,
			error:function($error){
				console.log($error)
			}
		})
	},
	success_save_edit_content_unit:function($responds){
		if ($responds.status) {
			syllabus.disabled_row_edit()
		}else{
			console.log("error al modidicar cobteniso")
		}
	},
	disabled_row_edit:function(){
		var tr = syllabus.config.$element
		// console.log(tr)
		if ($type_rate == "C") {
			var td_conceptual = tr.children('td:nth-child(3)')
			var td_procedimental = tr.children('td:nth-child(4)')
			var td_actitudinal = tr.children('td:nth-child(5)')
			var td_indicators = tr.children('td:nth-child(6)')
			var td_instruments = tr.children('td:nth-child(7)')
			var td_btn = tr.children('td:nth-child(8)')
			td_conceptual.html(syllabus.config.$edit_data.com_conceptual)
			td_procedimental.html(syllabus.config.$edit_data.com_procedimental)
			td_actitudinal.html(syllabus.config.$edit_data.com_actitudinal)
			td_indicators.html(syllabus.config.$edit_data.com_indicators)
			td_instruments.html(syllabus.config.$edit_data.com_instruments)
			td_btn.html('<span class="glyphicon glyphicon-pencil edit-unit-content"></span>') 
		}
		if ($type_rate == "O") {
			var td_content = tr.children('td:nth-child(3)')
			var td_strategy = tr.children('td:nth-child(4)')
			var td_indicators = tr.children('td:nth-child(5)')
			var td_instruments = tr.children('td:nth-child(6)')
			var td_btn = tr.children('td:nth-child(7)')

			td_content.html(syllabus.config.$edit_data.obj_content)
			td_strategy.html(syllabus.config.$edit_data.obj_strategy)
			td_indicators.html(syllabus.config.$edit_data.com_indicators)
			td_instruments.html(syllabus.config.$edit_data.com_instruments)
			td_btn.html('<span class="glyphicon glyphicon-pencil edit-unit-content"></span>') 
		}
		$(".delete-unit-content").bind('click',syllabus.deleted_content_unit)
		$(".edit-unit-content").bind('click',syllabus.active_tr_edit)
		$(".save-edit-unit-content").bind('click',syllabus.save_edit_content_unit)
	},
	deleted_content_unit:function(){
		var $td = $(this).parent()
		var $tr = $td.parent()
		var $data = $tr.data()
		$deleted_param = {}

		$deleted_param[$.base64.encode('escid')]=$.base64.encode($.trim(syllabus.config.$param.escid)),
		$deleted_param[$.base64.encode('subid')]=$.base64.encode($.trim(syllabus.config.$param.subid)),
		$deleted_param[$.base64.encode('curid')]=$.base64.encode($.trim(syllabus.config.$param.curid)),
		$deleted_param[$.base64.encode('courseid')]=$.base64.encode($.trim(syllabus.config.$param.courseid)),
		$deleted_param[$.base64.encode('turno')]=$.base64.encode($.trim(syllabus.config.$param.turno)),
		$deleted_param[$.base64.encode('perid')]=$.base64.encode($.trim(syllabus.config.$param.perid)),
		$deleted_param[$.base64.encode('unit')]=$.base64.encode($.trim(syllabus.config.$param.unit)),
		$deleted_param[$.base64.encode('week')]=$.base64.encode($.trim($data.week)),
		$deleted_param[$.base64.encode('session')]=$.base64.encode($.trim($data.session))

		syllabus.config.$element_remove = $tr
		$.ajax({
			url:"/syllabus/syllabus/deletecontent/",
			data:$deleted_param,
			success:syllabus.success_delete_row,
			error:function($error){
				console.log($error)
				// alert("Error al Eliminar")
			}
		})
	},
	success_delete_row:function($responds){
		if ($responds.status) {
			syllabus.config.$element_remove.remove()
		}else{
			console.log("Error al Eliminar Contenido")
		}
	},
	default_week_session:function(){
		$long_tr = $('#table_units_content tbody tr').length
		count = 0
		if ($long_tr > '0') {
			$('#table_units_content tbody tr').each(function(){
				data = $(this).data()
				tr_week = data.week
				tr_session = data.session+1
			})
			$('#week> option[value="'+tr_week+'"]').attr('selected', 'selected')
			$('#session> option[value="'+tr_session+'"]').attr('selected', 'selected')
		}
	},
	save_content_syllabus_unit:function($params){
		var url="/syllabus/syllabus/savemodified"
		$.ajax({
			url: url,
			type: 'POST',
			data:$("#frmSyllabus").serialize(),
			success: function ($data){
				if($data.status){
					send_units = false
					$.get('/syllabus/syllabus/units',$params,syllabus.modal_content)
				}
			},
			error : function($error){
				console.log($error)
					// alert("error al Guardar");
			}
		})
	},
	save_content_syllabus:function(){
		$("#loading_overlay").show()
		$(".loading_message").show()
		var url="/syllabus/syllabus/savemodified"
		$.ajax({
			url: url,
			type: 'POST',
			data:$("#frmSyllabus").serialize(),
			success: function ($data){
				send_units = false
				$("#loading_overlay").hide()
				$(".loading_message").hide()
			},
			error : function($error){
				console.log($error)
					// alert("error al Guardar")
			}
		})
	},
	close_sylabus:function(){
		//$("#accept_close_syllabus").modal('show')
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
	},
	preview_syllbus :function(){
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
}
$(document).ready(syllabus.init)
