eUndac.Views.TypeError = Backbone.View.extend({
	initialize : function(options){
		//templates
		template_error_payment = swig.compile($('#template_error-payment').html());
		this.loadError();
		this.options = options;
	},

	render : function(){
		console.log(this.options);
		var html;
		if (this.model) {
			html = template_error_payment(this.model.toJSON());
		} else if (this.options) {
			html = template_error_payment({type : this.options.type});
		} else {
			html = template_error_payment();
		}
		
		$('#js_main-data').html(html);
	},

	loadError : function(){
		var self = this;

		$('#js_main_spinner').addClass('fadeOut');
		setTimeout(function() {
			$('#js_main_spinner').removeClass('fadeUp fadeOut');
			self.render();
		}, 300);
	}
});