eUndac.Views.Period_Form = Backbone.View.extend({
	events : {
		'click .js_button_cancel' : 'cancel',
		'submit form' : 'submitForm'
	},

	initialize : function(options){
		this.template = options.template;
		this.template_msg_error   = swig.compile($('#template_msg_error').html());
		this.template_msg_success = swig.compile($('#template_msg_success').html());
		this.template_msg_danger  = swig.compile($('#template_msg_danger_new').html());

		//tags globales
		this.$content_new  = $('.js_content_new');
		this.$content_main = $('.js_content_main');
		this.render().afterRender();
	},

	render : function(){
		var html = this.$el.html(this.template);
		this.$content_new.html(html);

		return this;
	},

	afterRender : function(){
		this.$form         = $('.js_form-period-new');
	},

	cancel : function(){
		changeContent(this.$content_new, this.$content_main);
	},

	submitForm : function(e){
		e.preventDefault();

		var period_data    = this.$form.serializeJSON();

		var period_model = new eUndac.Models.Period(period_data);

		saveNew(this.$form, period_model, this.collection, this);
	}
});