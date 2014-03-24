    	
var  msg = $('<div class="progress" style="height:10px;"></div>');
var msg_t = $('<span>La Contraseña no Considen</span>');

var  empty = CryptoJS.MD5('');
var change = {
	onReady : function() {
    	$('#valida_password').click(change.valid_pass);
    	$("#password_change").keyup(change.verify_fortress);
    	$("#password_verify").keyup(change.verify_equal);
    },
    valid_pass : function(){
    	pass_actual = CryptoJS.MD5($("#password_actual").val());
    	pass_change = CryptoJS.MD5($("#password_change").val());
    	pass_verify = CryptoJS.MD5($("#password_verify").val());
		if (typeof(pass_change) == typeof(pass_verify)) {
			// data = {};
            console.log(pass_change + " "+ pass_verify);
            alert("csscsssd");
		}else{
            console.log(pass_change + " "+ pass_verify);
            alert("diferfsvvsdv");
        }
    },
    verify_fortress: function(){
    	$(this).after(msg);
    	password = $(this).val().length;
    	if (password < 6) {
    		password * 10;
    		message = '<div class="progress-bar progress-bar-danger" style="width:'+password+'%"><span class="sr-only">30% Complete (danger)</span></div>';
    	}else{
    		if (password < 10) {
    				password *15;
		    		message = '<div class="progress-bar progress-bar-warning" style="width: '+password+'%"><span class="sr-only">20% Complete (warning)</span></div>';
    		}else{
    				password * 20 ;
		    		message = '<div class="progress-bar progress-bar-success" style="width: '+password+'%"><span class="sr-only">35% Complete (success)</span></div>';
    		}
    	}
    	msg.html(message)
    },
    verify_equal: function(){
        $(this).after(msg_t);
    	password_change = $("#password_change").val();
    	equal = $(this).val();
        if (password_change == equal) {
            msg_t=('La Contraseña Considen')
        }else{
            msg_t('La Contraseña no Considen')
        }
        msg_1.html(msg_t)
    	// if (typeof(equal) != typeof(empty) && typeof(password_change) != typeof(empty)) {
    	// 	console.log("erebvjdbd");
    	// }else{
    	// 	console.log("eror");
    	// }
    }
};
$(document).ready(change.onReady);
