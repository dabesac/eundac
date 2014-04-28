var new_project= {
	init:function(){
		$("#save_project").click(new_project.save_project)
	},	
	save_project:function(){
		$.ajax({
			url: "/acreditacion/socialprojection/newproject",
			type:"post",
			data:$("#form_new_project").serialize(),
			success:function($data){
				alert($data)
				$("#Modalproject").html($data)
			},
			erro:function($erro){
				alert($erro)
			}
		});
	}
}
$(document).ready(new_project.init);