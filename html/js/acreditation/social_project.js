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
		// console.log(data_cronograma)
		var type_t = $(this).val(), date = new Date() ,date_year = date.getFullYear()
		for (var i = 0; i < data_cronograma.length; i++) {
				var anio = data_cronograma[i].f_ini.split('-')
				if (type_t == data_cronograma[i].type &&  parseInt(anio) == date_year ) {
					var option = $("<option value="+data_cronograma[i].id+">"+data_cronograma[i].f_ini+"</option>") 
				}else{
					option = $("<option>Sin registro</option>")
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
	success_modified:function($data){
		// console.log($data)
		project.cronogram
		$("#Modalproject").modal('show')
		$("#Modalproject").html($data)
		$("#btn_modified_project").click(project.uploadFiles_mod)
		$("#uid_openerp").val(project.config.open_uid)
		$("#escid_openerp").val(project.config.open_escid)
		$("#subid_openerp").val(project.config.open_subid)
		$("#type").click(project.cronogram)
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

	},
	prepareUpload:function(input){
		if (input.files && input.files[0]) {
            var reader = new FileReader();
            // reader.onload = function (e) {
            //     $('#previewImg').attr('src', e.target.result);
            // }
            reader.readAsDataURL(input.files[0]);
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