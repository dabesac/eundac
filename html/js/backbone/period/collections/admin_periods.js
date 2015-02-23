eUndac.Collections.Admin_Periods = Backbone.Collection.extend({
	initialize : function(options){
		if (options.id) {
			this.id = options.id;
		}
		this.on('add', function(model){
			//render de vistas
			view = new eUndac.Views.Admin_Periods({model : model});
		});

		this.on('change', function(model){
			if (model.changed.new_active) {
				var last_actives = this.where({ state : 'A'});

				//renderear el antiguo activo :v en temporales...
				last_actives.forEach(function(active){
					if (model.id != active.id) {
						active.set({state : 'T', state_before : 'A', new_active : false});
						active.save();
					}
				});
			}
		}, this);
	},

	url : function(){
		return this.id ? '/rest/period/id/'+this.id : '/rest/period';
	},

	model : eUndac.Models.Period
});