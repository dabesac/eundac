eUndac.Views.Interaction_Header = Backbone.View.extend({
	tagName   : 'header',

	className : 'content__header  content__header--interaction',

	events : {
		'change .js_select'    : 'changeSelect',
		'click .js_button_new' : 'new',
	},

	initialize : function(){
		this.template = swig.compile($('#template_header_period').html());
		this.render().afterRender();

	},

	render : function(){
		var html = this.template(this.model.toJSON());
		$('.js_content_main').prepend(this.$el.html(html));
		return this;
	},

	afterRender : function(){
		var year = this.model.toJSON().current;
		$('.js_select').val(year);
		this.loadData(year);
	},

	changeSelect : function(ev){
		var $select = $(ev.target);
		this.loadData($select.val());
	},

	new : function(){
		changeContent($('.js_content_main'), $('.js_content_new'));
	},

	loadData : function(year){
		this.collection.id = year;
		$('.js_form-period-new').find('input[name=year]').val(year);

		chargeContent($('.js_content_body'), this.collection);
	}
});