eUndac.Views.PreregisterAuxiliar = Backbone.View.extend({
	tagName : 'div',

	className : 'helper  helper--item  helper--item--two',

	events : {
		'click .js_item-informative' : 'informativeDetail',
	},

	initialize : function(options){
		this.template = swig.compile($('#template_auxiliar-one').html());

		this.type = options.type;
		this.data_payments = null;
		this.data_credits  = null;
		if (this.type === 'PA') {
			this.data_payments = this.model.toJSON();
			this.data_credits = options.model_credits.toJSON();
		}
	},

	render : function(){
		this.$el.html(this.template({ 	type : this.type,
										data_payments : this.data_payments,
										data_credits : this.data_credits }));
		if (this.type === 'PR') {
			this.$el.removeClass('helper--item  helper--item--two');
			this.$el.addClass('helper--button  button-large-right  fix-alone');
		}
	},

	renderPrint : function(){
		console.log('render');
	},

	informativeDetail : function(e){
		$e = $(e.currentTarget);
		if (!$e.hasClass('active')) {
			$e.addClass('active');
		} else {
			$e.addClass('inactive');
			setTimeout(function() {
				$e.removeClass('active inactive');
			}, 300);
		}
	}

});