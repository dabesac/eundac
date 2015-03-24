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

		var collection_periods = new eUndac.Collections.Admin_Periods({ id : null });
		years.on('add', function(model){

			header_interaction = new eUndac.Views.Interaction_Header({model:model, collection : collection_periods});
		});

		var period_form = new eUndac.Views.Period_Form({template : $('#js_content_new').html(), collection : collection_periods});
	}
});