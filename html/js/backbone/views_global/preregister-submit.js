eUndac.Views.PreregisterSubmit = Backbone.View.extend({
	tagName : 'a',

	className : 'button button--icon button--success',

	initialize : function(options){
		this.template = swig.compile($('#template_button-submit').html());
		this.render();
	},
	
	render : function(){
		var html = this.$el.html(this.template());
		$('#js_button_submit_side').html(html);

	}
});