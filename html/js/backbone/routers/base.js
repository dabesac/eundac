eUndac.Routers.Base = Backbone.Router.extend({
	routes : {
		'' : 'index',
		'period' : 'period',
		'register/preregister' : 'preregister'
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

		var period_form = new eUndac.Views.Period_Form({template : $('#js_content_new').html(), 
														collection : collection_periods});
	},

	preregister : function(){
		//pagos
		var model_user_payment    = new eUndac.Models.UserPayment();
		var model_user_data = new eUndac.Models.UserData();
		var more_semester = null;
		var more_credits  = null;
		
		model_user_data.fetch({
			success : function(model, response){
				var model_user = model;
				var response_user = response;
				var success_pre = response.current_register.state;
				if (!success_pre || success_pre === 'B') {
					model_user_payment.fetch({
						success : function(model, response){
							var state_payment = response.current_payment.state;
							var pre_auxiliar = new eUndac.Views.PreregisterAuxiliar({   type : 'PA',
																						model : model, 
																						model_credits : model_user});
							pre_auxiliar.render();
							$('.js_auxiliar').html(pre_auxiliar.$el);

							// condiciones
							if (!success_pre || success_pre === 'B') {
								if (response_user.conditions) {
									var conditions = response_user.conditions;
									conditions.forEach(function(condition){
										if (condition.code == 'CA') {
											more_credits = condition.amount;
										} else if (condition.code == 'SA') {
											more_semester = condition.amount;
										}
									});
								}
							}

							// verificar pago
							if (state_payment !== 'EP' && state_payment !== 'LP' && state_payment !== 'OT') {
								//preregister
								var student_courses = new eUndac.Collections.PreregisterCourses({
																									model_user    : model_user,
																									success_pre   : success_pre,
																									more_semester : more_semester,
																									more_credits  : more_credits  });
							} else {
								var error_view = new eUndac.Views.TypeError({ model : model });
							}
						},
						error : function(){

						}
					});
				} else if (success_pre === 'I'){
					var student_courses = new eUndac.Collections.PreregisterCourses({
																						model_user    : model,
																						success_pre   : success_pre,
																						more_semester : more_semester,
																						more_credits  : more_credits  });

				}
			},
			error : function(){

			}
		});
		/*model_user_payment.fetch({
			success : function(model, response){
				var model_self = model;
				var state_payment = response.current_payment.state;

				model_user_data.fetch({
					success : function(model, response){
						var pre_auxiliar = new eUndac.Views.PreregisterAuxiliar({   model : model_self, 
																					model_credits : model});
						pre_auxiliar.render();
						$('.js_auxiliar').html(pre_auxiliar.$el);

						var success_pre = response.current_register.state;

						// condiciones
						var more_semester = null;
						var more_credits  = null;
						if (response.conditions && !success_pre) {
							var conditions = response.conditions;
							conditions.forEach(function(condition){
								if (condition.code == 'CA') {
									more_credits = condition.amount;
								} else if (condition.code == 'SA') {
									more_semester = condition.amount;
								}
							});
						}
						// verificar pago
						if (state_payment !== 'EP' && state_payment !== 'LP' && state_payment !== 'OT') {
							//preregister
							var student_courses = new eUndac.Collections.PreregisterCourses({
																								success_pre   : success_pre,
																								more_semester : more_semester,
																								more_credits  : more_credits  });
						} else {
							var error_view = new eUndac.Views.TypeError({ model : model_self });
						}
					},
					error : function(){

					}
				});
				
			},
			error : function(){

			}
		});*/
	}
});