var project= {
	init:function(){
		$("#add_project").click(project.new)
		$(".project-modified").click(project.modified)
		project.config = {
			open_uid:$("#uid_openerp_t").val(),
			open_escid:$("#escid_openerp_t").val(),
			open_subid:$("#subid_openerp_t").val(),
		}
	},
	cronogram:function(e){
		var type_t = $(this).val()
		project.option_cronogram(type_t)
	},
	option_cronogram:function(type_t){
		var option = "<option>Seleccione</option>",date = new Date() ,date_year = date.getFullYear()
		for (var i = 0; i < data_cronograma.length; i++) {
			// console.log(data_cronograma.length)
				var anio = data_cronograma[i].f_ini.split('-')
				if (type_t == data_cronograma[i].type &&  parseInt(anio[0]) == date_year && data_cronograma[i].state=='A') {
					option = option + "<option data-index="+i+" value="+data_cronograma[i].id+">"+data_cronograma[i].name+"</option>"
				}
		}
		$("#cronogram_proj").html(option)
	},
	new:function(e){
		$.get('/acreditacion/socialprojection/newproject',project.success_new)
	},
	modified:function(e){
		// console.log("ssfsf")
		id_project = $(this).attr('id_project')
		$.get('/acreditacion/socialprojection/modifiedproject',{id:id_project},project.success_modified)
	},
	option_default:function(){
		$("#cronogram_proj option[value="+ data_project.cronogram_id[0] +"]").attr('selected',true)
	},
	success_modified:function($data){
		$("#Modalproject").modal('show')
		$("#Modalproject").html($data)
		$("#btn_modified_project").click(project.uploadFiles_mod)
		$("#uid_openerp").val(project.config.open_uid)
		$("#escid_openerp").val(project.config.open_escid)
		$("#subid_openerp").val(project.config.open_subid)
		$("#type").click(project.cronogram)
		// project.cronogram()
		project.option_cronogram($("#type").val())
		project.option_default();
		// $(")
	},
	success_new:function($data){
		$("#Modalproject").html($data)
		$("#project").on('change',project.prepareUpload)
		$("#save_project").click(project.uploadFiles)
		$("#uid_openerp").val(project.config.open_uid)
		$("#escid_openerp").val(project.config.open_escid)
		$("#subid_openerp").val(project.config.open_subid)
		$("#type").click(project.cronogram)
		$("#cronogram_proj").on('change',project.contet_cronogram)
	},
	contet_cronogram:function(){
		var index = $(this).find('option:selected').data('index')
		var etiqueta	
		// $("#contet_conogram").append(
		// 		$("<label>").text(data_cronograma[index].f_ini)
		// 	)
	},
	prepareUpload:function(input){
		if (input.files && input.files[0]) {
            var reader = new FileReader();
            // reader.onload = function (e) {
            //     $('#previewImg').attr('src', e.target.result);
            // }
            reader.readAsDataURL(input.files[0])
        }
		// project.config.files = event.target.files
		// console.log(project.config.files)
	},
	uploadFiles:function(event){

		var $this = $('#form_new_project')

	    var data = new FormData($this[0])
	    // console.log(data)
	    project.save_upload(data)
	},
	uploadFiles_mod:function(event){
		var $this = $('#form_new_project')
	    var data = new FormData($this[0])
	    project.save_modified(data)
	},
	save_modified:function(data){
		// console.log("cssddsdsds")
		$.ajax({
			url: "/acreditacion/socialprojection/modifiedproject",
			type:'POST',
			data:data,
			processData: false,
			contentType: false, 
			success:function($data){
				if ($data.status == true) {
                    window.location.href = window.location.href; 
				}else{
					$("#Modalproject").html($data)
				}
			},
			error:function($error){
				console.log($error)
			}

		})
		return false
	},
	save_upload:function(data){
		$.ajax({
			url: "/acreditacion/socialprojection/newproject",
			type:'POST',
			data:data,
			processData: false,
			contentType: false, 
			success:function($data){
				if ($data.status == true) {
                    window.location.href = window.location.href; 
				}else{
					$("#Modalproject").html($data)
				}
			},
			error:function($error){
				console.log($error)
			}

		})
		return false
	}	
}
$(document).ready(project.init);