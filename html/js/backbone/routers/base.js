eUndac.Routers.Base = Backbone.Router.extend({
	routes : {
		'' : 'index',
		'period' : 'period'
	},
	initialize : function(){
		Backbone.history.start({
			root      : '/',
			pushState : true
		});
	},
	
	index : function(){
		console.log('estoy en mi index');
	},

	period : function(){
		var years = new eUndac.Collections.Years();
		years.fetch();
		years.on('add', function(model){
			header_interaction = new eUndac.Views.Interaction_Header({model:model});
		});
		
		var period_form = new eUndac.Views.Period_Form({template : $('#js_content_new').html()});
	}
});