eUndac.Views.Paragraphs = Backbone.View.extend({
	initialize : function(options){
		//templates
		template_paragraph = swig.compile($('#template_paragraphs').html());
		this.render(options.data_render);
		this.$where_render = options.where_render;
	},

	render : function(data_render){
		var self = this;
		var html_paragraph = template_paragraph(data_render);
		$('#js_main_spinner').addClass('fadeOut');
		setTimeout(function() {
			$('#js_main_spinner').removeClass('fadeUp fadeOut');
			self.$where_render.html(html_paragraph);
		}, 300);
	}
});