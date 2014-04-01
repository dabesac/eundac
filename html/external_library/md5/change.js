    	
var  msg = $('<div id="control_foortres" class="progress" style="height:10px;"></div>');
var msg_1 = $('<span>La Contraseña no Considen</span>') ;

var change = {
	onReady : function() {
    	$('#valida_password').click(change.valid_pass);
    	$("#password_change").keyup(change.verify_fortress);
    	$("#password_verify").keyup(change.verify_equal);
        $("#li_change").click(change.enabled);
    },
    valid_pass : function(){
    	var pass_actual = ($("#password_actual").val());
    	var pass_change = ($("#password_change").val());
    	var pass_verify = ($("#password_verify").val());
		if (
                (typeof(pass_change) == typeof(pass_verify)) &&
                (pass_change != '') && (pass_verify != '' ) && (pass_actual != '' )
            ) {
                $post_data = {}
                $post_data['pass_actual']=(pass_actual)
                $post_data['pass_change']=(pass_change)
                $post_data['pass_verify']=(pass_verify)
                $.ajax({
                    url:"/passwodorchange",
                    type:'POST',
                    data:$post_data,
                    success: function($respons){
                        if ($respons.status == true) {
                            $("#password_actual").val('')
                            $("#password_change").val('')
                            $("#password_verify").val('')
                            $("#control_foortres").hide()
                            msg_1.html('Contraseña Cambiada')
                        }
                        if($respons.status == false && $respons.error == 0) {
                            $parent  = $("#password_actual").parent()
                            $parent.addClass('has-error')
                            $parent.tooltip('show')
                            $("#password_actual").val('')
                            $("#password_change").val('')
                            $("#password_verify").val('')
                            $("#control_foortres").hide()
                            // msg_1.hide()
                        }
                    },
                    error: function($error){
                        alert("SFSFSFs");
                    }
                });
                return false;
		}else{
            $("#form-change").children(change.addClass_error)
        }
        // return false;
    },
    verify_fortress: function(){
    	$(this).after(msg);
    	password = $(this).val().length;
    	if (password < 6) {
            width = 50 - (password*5);
    		message = '<div class="progress-bar progress-bar-danger" style="width:'+ width +'%"><span class="sr-only">30% Complete (danger)</span></div>';
    	}else{
    		if (password < 10) {
		    		message = '<div class="progress-bar progress-bar-warning" style="width: '+password * 3+'%"><span class="sr-only">20% Complete (warning)</span></div>';
    		}else{
		    		message = '<div class="progress-bar progress-bar-success" style="width: '+password * 5+'%"><span class="sr-only">35% Complete (success)</span></div>';
    		}
    	}
    	msg.html(message)
    },
    verify_equal: function(){
        $(this).after(msg_1);
    	password_change = $("#password_change").val();
    	equal = $(this).val();
        if (password_change == equal) {
            msg_t=('Las Contraseña Coinciden')
        }else{
            msg_t=('Las Contraseña no coinciden')
        }
        msg_1.html(msg_t)
    	// if (typeof(equal) != typeof(empty) && typeof(password_change) != typeof(empty)) {
    	// 	console.log("erebvjdbd");
    	// }else{
    	// 	console.log("eror");
    	// }
    },
    enabled:function(){
        $("#form-change").addClass('activePassword')
    },
    addClass_error:function(){
    }
};
$(document).ready(change.onReady);
