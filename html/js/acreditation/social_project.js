var project= {
	init:function(){
		$("#add_project").click(project.new)
	},	
	new:function(e){
		$.get('/acreditacion/socialprojection/newproject',function($data){
			$("#Modalproject").html($data)
		})
	}
}
$(document).ready(project.init);