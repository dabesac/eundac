eUndac.Collections.PreregisterCourses = Backbone.Collection.extend({
	initialize : function(options){
		var self = this;
		this.model_data_user = options.model_user;

		var success_pre = options.success_pre;
		if (!success_pre || success_pre === 'B') {
			// semesters
			var semester_render = 3;
			if (options.more_semester) {
				semester_render = semester_render + options.more_semester; 
			}
			var more_credits = false;
			if (options.more_credits) {
				more_credits = options.more_credits;
			}
		}

		this.fetch({
			success : function(models, response){
				if (!success_pre || success_pre === 'B') {
					//verificar si existe curso con condicion...
					var courses_with_condition = self.where({ condition : true });

					// tipo de render segun condicional
					var result = self.verifyCondition(courses_with_condition);

					var type_render_course = 'A';
					var type_render_paragraph = 'N';

					if (result.conditional && !result.conditional_allow){
						type_render_course = 'I';
						type_render_paragraph = 'C';
					}

					//render de parrafo
					var data_render = {	type : type_render_paragraph,
										conditional_amount : result.conditional_amount };
					var view_paragraph = new eUndac.Views.Paragraphs({ 	data_render : data_render, 
																		where_render : $('#js_paragraph') });

					//render de cursos
					var semesters = [];
					var semester_each = '-';
					var c_semesters = 0;
					models.every(function(model){
						// cantidad de semestres
						var semester_turn_each_self = model.toJSON().semester + model.toJSON().turn;
						var semester_each_self = model.toJSON().semester;

						// Semestre y turno para establecer creditos
						var exist = false;
						semesters.forEach(function(semester){
							if (semester === semester_turn_each_self) {
								exist = true;
							}
						});
						if (!exist) {
							semesters.push(semester_turn_each_self);
						}

						// Renderear cursos en la cantidad de semestres establecidos
						if (semester_each_self !== semester_each) {
							semester_each = semester_each_self;
							c_semesters++;
							
							if (c_semesters > semester_render)
								return false;
						}

						// render
						view_course = new eUndac.Views.Course({ model : model, 
																typeRender : type_render_course,
																where_render : $('#js_main-data') });

						return true;
					});

					// si no es condicional
					self.putCredits(result.conditional_credits, semesters, more_credits);

					//boton para validar
					if (type_render_course === 'A') {
						self.buttonPreregister(courses_with_condition);
					}
				} else if (success_pre === 'I') {
					// Renderear cursos que llevara
					var courses_carry = [];
					var semester_assign = models.models[0].toJSON().semester_roman_assign;
					models.forEach(function(course){
						courses_carry.push(course);
					});
					self.renderPreSuccess(courses_carry, semester_assign);
				} else if (success_pre === 'M') {
					var courses_carry_s = [];
					var semester_assign_s = models.models[0].toJSON().semester_roman_assign;
					models.forEach(function(course){
						courses_carry_s.push(course);
					});
					self.renderRegisterSuccess(courses_carry_s, semester_assign_s);
				}
			},
			error : function(){
				var error_view = new eUndac.Views.TypeError();
			}
		});

		// listen updates
		this.on('change', function(model){
			var course_code = model.toJSON().code;
			var course_turn = model.toJSON().turn;
			var same_course = this.where({code : course_code});
			same_course.forEach(function(course){
				if (course.toJSON().code === course_code && course.toJSON().turn !== course_turn) {
					if (course.toJSON().carry) {
						course.set({carry : false}, {silent : true});
					}
				}
			});
		});
	},

	url : '/rest/preregister',
	model : eUndac.Models.Preregister,

	verifyCondition : function(courses_with_condition){
		var result = {	conditional : false,
						conditional_allow : false,
					  	conditional_credits : 0,
					  	conditional_amount : 0 };

		var course_each = '-';
		courses_with_condition.forEach(function(course){
			if (course_each !== course.toJSON().code) {
				course_each = course.toJSON().code;
				result.conditional_allow = false;
				result.conditional = true;
				result.conditional_amount++;

				condition_allow = course.toJSON().condition_allow;
				result.conditional_credits = result.conditional_credits + course.toJSON().credits;
				if (condition_allow) {
					result.conditional_allow = true;
				}
			}
		});
		return result;
	},

	putCredits : function(conditional_credits, semesters, more_credits){
		var credits_max = 11;
		var semester_max = null;
		if (conditional_credits === 0) {
			semesters.forEach(function(semester){
				if ($('#js_credit' + semester).data().credits >= credits_max) {
					credits_max = $('#js_credit' + semester).data().credits;
					semester_max = $('#js_credit' + semester).data().semester_roman;
				}
			});
			credits_max++;
			$('#js_credits_current').data('credits', 0);
		} else {
			semesters.forEach(function(semester){
				$('#js_credit' + semester).data('credits', 10);
			});

			$('#js_credits_current').data('credits', 0);
			/*$('#js_credits_current')
				.html(conditional_credits)
				.data('credits', conditional_credits);*/
		}

		if (more_credits) {
			credits_max = credits_max + more_credits;
		}

		$('#js_period-assign')
			.data('credits', credits_max)
			.data('semester', semester_max)
			.html(credits_max);
	},

	buttonPreregister : function(courses_with_condition){
		var self = this;
		var courses_carry;
		var $button_submit  = $('#js_button_submit');
		var $button_back    = $('#js_confirm--button-back');
		var $button_confirm = $('#js_confirm--button-confirm');
		
		setTimeout(function() {
			$button_submit.addClass('fadeUp');
		}, 300);

		$button_submit.on('click', function(){
			// verify carry his conditionals course
			var conditional_carries = true;
			if (courses_with_condition.length > 0) {
				var conditional_carry;
				var course_code;
				var courses_same;
				var many_conditionals = 0;
				var many_conditionals_carry = 0;
				courses_with_condition.forEach(function(course){
					course_code_self = course.toJSON().code;
					if (course_code != course_code_self) {
						many_conditionals++;
						conditional_carry = true;
						course_code = course_code_self;
						if (!course.toJSON().carry) {
							conditional_carry = false;
							courses_same = self.where({code : course.toJSON().code});
							if (courses_same.length > 0) {
								courses_same.forEach(function(course_same){
									if (course.toJSON().turn !== course_same.toJSON().turn && course_same.toJSON().carry) {
										conditional_carry = true;
									}
								});
							}
						}
						if (conditional_carry) {
							many_conditionals_carry++;
						}
					}
				});

				if (many_conditionals !== many_conditionals_carry) {
					conditional_carries = false;
				}
			}

			if (conditional_carries) {
				courses_carry = self.where({carry : true});
				if (courses_carry.length > 0) {
					self.renderConfirmation(courses_carry);
				} else {
					console.log('Elija al menos un curso');
				}
			} else {
				$('#js_paragraph-charge-conditional').addClass('active');
			}

		});

		$button_back.on('click', this.backFromConfirmation);
		$button_confirm.on('click', function(){
			self.confirmSubmit(courses_carry);
		});

		
	},

	renderConfirmation : function(courses_carry){
		$('#js_content-pick-course').addClass('inactive');
		setTimeout(function() {
			$('#js_content-pick-course').removeClass('active inactive');
			$('#js_content-confirm-pick').addClass('active');
		}, 300);

		//render course y sacar datos
		var many_courses = 0;
		var semester_assign = $('#js_period-assign').data().semester;
		var credits_assign = $('#js_period-assign').data().credits;
		var semester_roman_assign = $('#js_period-assign').data().semester_roman;
		courses_carry.forEach(function(course){
			course.set({semester_assign : semester_assign,
						credits_assign  : credits_assign }, {silent : true});
			view_course = new eUndac.Views.Course({ model : course, 
													typeRender : 'I',
													where_render : $('#js_main-data-confirm') });
			many_courses++;
		});

		var data_render = {	type : 'CO',
							many_courses : many_courses,
							semester_assign : semester_roman_assign, };
		var view_paragraph = new eUndac.Views.Paragraphs({  data_render : data_render,
															where_render : $('#js_paragraph-confirm') });
	},

	backFromConfirmation : function(){
		// back to pick courses
		$('#js_content-confirm-pick').addClass('inactive');
		setTimeout(function() {
			$('#js_main-data-confirm').html('');
			$('#js_content-confirm-pick').removeClass('active inactive');
			$('#js_content-pick-course').addClass('active');
		}, 300);
	},

	confirmSubmit : function(courses_carry){
		$('#js_confirm--button-back, #js_confirm--button-confirm').attr('disabled', true);
		$('#js_paragraph-charge').addClass('active');

		var self = this;
		var many_courses = 0;
		var many_courses_success = 0;
		var semester_assign = $('#js_period-assign').data().semester_roman;
		courses_carry.forEach(function(course){
			many_courses++;
			course.save(null, {
				success : function(model, response){
					if (response.success === 1) {
						many_courses_success++;
						if (many_courses === many_courses_success) {
							self.renderPreSuccess(courses_carry, semester_assign);
						}
					} else if (response.success === 0) {
						console.log('error al guardar');
					}
				},
				error : function(){
					console.log('error al guardar en la db...');
				}
			});
		});

		/*var model_preregister = new eUndac.Models.Preregister();
		model_preregister.save(courses_json, {
			success : function(model, response){
				console.log(model, response);
			},
			error : function(){
				console.log('Horror');
			}
		});*/
	},

	renderPreSuccess : function(courses_carry, semester_roman_assign){
		var $button_delete  = $('#js_button_delete_pre');

		$('#js_content-confirm-pick').addClass('inactive');
		setTimeout(function() {
			$('#js_content-confirm-pick').removeClass('active inactive');
			$('#js_preregister-success').addClass('active');
		}, 300);

		// render success
		var many_courses = 0;
		courses_carry.forEach(function(course){
			view_course = new eUndac.Views.Course({ model : course, 
													typeRender : 'I',
													where_render : $('#js_success-main-data') });
			many_courses++;
		});

		var data_render = {	type : 'PS',
							many_courses : many_courses,
							semester_assign : semester_roman_assign, };
		var view_paragraph = new eUndac.Views.Paragraphs({  data_render : data_render,
															where_render : $('#js_success-paragraph') });

		var pre_auxiliar = new eUndac.Views.PreregisterAuxiliar({ type : 'PR' });
		pre_auxiliar.render();
		$('.js_auxiliar').html(pre_auxiliar.$el);

		var preregister_delete = new eUndac.Views.PreregisterDelete({ 	model : this.model_data_user,
																		model_course : courses_carry[0] });

		$button_delete.on('click', function(){
			$('#js_msg-confirm-delete').addClass('active');
		});
	},

	renderRegisterSuccess : function(courses_carry, semester_roman_assign){
		$('#js_register-success').addClass('active');

		// render success
		var many_courses = 0;
		courses_carry.forEach(function(course){
			view_course = new eUndac.Views.Course({ model : course, 
													typeRender : 'I',
													where_render : $('#js_reg_success-main-data') });
			many_courses++;
		});

		var data_render = {	type : 'RS',
							many_courses : many_courses,
							semester_assign : semester_roman_assign, };

		var view_paragraph = new eUndac.Views.Paragraphs({  data_render : data_render,
															where_render : $('#js_reg_success-paragraph') });
	}
});