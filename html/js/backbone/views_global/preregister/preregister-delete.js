eUndac.Views.PreregisterDelete = Backbone.View.extend({
	tagName : 'section',

	className : 'content content--interactive',

	id : 'js_msg-confirm-delete',

	events : {
		'click .js_button_cancel_delete'  : 'cancelDelete',
		'click .js_button_confirm_delete' : 'confirmDelete'
	},

	initialize : function(options){
		this.template = swig.compile($('#template_preregister-delete').html());
		this.render();

		this.model_course = options.model_course;
	},
	
	render : function(){
		var data = {attemps : 5 - this.model.toJSON().current_register.count_delete };
		var html = this.$el.html(this.template(data));
		$('#js_success-delete').html(html);
	},

	cancelDelete : function(){
		$('#js_msg-confirm-delete').addClass('inactive');
		setTimeout(function() {
			$('#js_msg-confirm-delete').removeClass('active inactive');
		}, 300);
	},

	confirmDelete : function(e){
		$btn_delete_confirm = $(e.currentTarget);
		$btn_delete_confirm
			.attr('disabled', true)
			.html('Eliminando...');
		$btn_delete_confirm.siblings('.js_button_cancel_delete').attr('disabled', true);
		
		this.model_course.destroy({
			success : function(model, response){
				if (response.success) {
					location.reload();
				} else {
					$btn_delete_confirm
						.removeAttr('disabled')
						.html('Eliminar Prematricula');
				}
			},
			error : function(){
				$btn_delete_confirm
					.removeAttr('disabled')
					.html('Eliminar Prematricula');
			}
		});
	}
});