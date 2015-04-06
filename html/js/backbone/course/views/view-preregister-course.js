eUndac.Views.Course = Backbone.View.extend({
	tagName : 'article',
	className : 'item  item--blue',

	events : {
		'click .js_course_checkbox' : 'carryCourse'
	},

	initialize : function(options){
		//templates
		this.template = swig.compile($('#template_course').html());
		template_section = swig.compile($('#template_group_semester').html());

		this.course_data = this.model.toJSON();
		this.course_data.typeRender = options.typeRender;

		this.$where_render = options.where_render;

		this.loadData();
		
	},


	render : function(){
		var html_course = this.template(this.course_data);
		this.$el.html(html_course);
		this.$where_render.children('.js_semester' + this.course_data.semester).find('.js_content-semester').append(this.$el.html(html_course));
	},

	renderGroup : function(){
		var html_section = template_section(this.course_data);
		this.$where_render.append(html_section);

		this.render();
	},

	loadData : function(){
		var self = this;

		setTimeout(function() {
			$('.js_button-pre').addClass('fadeUp');

			//renderear de acuerdo al semestre
			var semester = self.model.toJSON().semester;
			if (self.$where_render.children('.js_semester' + semester).hasClass('active')) {
				self.render();
			} else {
				self.renderGroup();
			}
		}, 300);
	},

	carryCourse : function(e){
		e.preventDefault();
		var $btn_carry_course = $(e.currentTarget);
		var $checkbox_carry_course = $btn_carry_course.siblings('input');
		var data_course = $btn_carry_course.data();

		this.updateCredits($checkbox_carry_course, data_course);

	},

	updateCredits : function($check_box, data_course){
		var credits_current      = $('#js_credits_current').data().credits;
		var type_course          = data_course.type;
		var type_semester_course = data_course.type_semester;

		// Verify another turn
		var code = data_course.code;
		var turn = data_course.turn;
		$('[data-code=' + code + ']').each(function(index){
			if ($(this).data().turn !== turn) {
				if ($(this).siblings('input').prop('checked')) {
					var credits_other = $(this).data().credits;
					credits_current = credits_current - credits_other;
					$('#js_credits_current')
						.data('credits', credits_current)
						.html(credits_current);
					$(this).siblings('input').prop('checked', false);
				}
			}
		});

		// elective
		if (type_course === 'E') {
			$('[data-type_semester=' + type_semester_course + ']').each(function(index){
				if ($(this).data().code !== code) {
					if ($(this).siblings('input').prop('checked')) {
						var credits_other = $(this).data().credits;
						credits_current = credits_current - credits_other;
						$('#js_credits_current')
							.data('credits', credits_current)
							.html(credits_current);
						$(this).siblings('input').prop('checked', false);
					}
				}
			});
		}

		// Carry course
		var credits_assign = parseInt($('#js_period-assign').data().credits);
		var credits_course = data_course.credits;
		if (!$check_box.prop('checked')){
			credits_current = parseInt(credits_current) + parseInt(credits_course);
			if (credits_current <= credits_assign) {
				$('#js_credits_current')
					.data('credits', credits_current)
					.html(credits_current);

				$check_box.prop('checked', true);
				this.model.set({carry : true});
			}
		}else {
			credits_current = parseInt(credits_current) - parseInt(credits_course);
			$('#js_credits_current')
				.data('credits', credits_current)
				.html(credits_current);

			$check_box.prop('checked', false);
			this.model.set({carry : false});
		}

		// Higher pick for credits
		var exists_s_t = [];
		$('.js_course_checkbox').each(function(index){
			if ($(this).siblings('input').prop('checked')) {
				exists_s_t.push({ 	s_t : $(this).data().st,
									credits : $(this).data().credits,
									semester : $(this).data().semester,
									turn : $(this).data().turn,
									type : $(this).data().type });
			}
		});

		var s_t_self = '-';
		var s_t_close;
		var unique_s_t = [];
		var s_t_credits_assign = [];
		var higher_credits = 0;
		var c_s_t;
		var credits_s_t;
		exists_s_t.forEach(function(s_t){
			if (s_t.s_t !== s_t_self) {
				credits_s_t = 0;
				s_t_self = s_t.s_t;
				exists_s_t.forEach(function(s_t_again){
					if (s_t.s_t === s_t_again.s_t)
						credits_s_t = credits_s_t + parseInt(s_t_again.credits);
				});

				if (credits_s_t >= higher_credits)
					higher_credits = credits_s_t;

				unique_s_t.push({
					s_t            : s_t.s_t,
					semester       : s_t.semester,
					semester_roman : $('#js_credit' + s_t.s_t).data().semester_roman,
					turn           : s_t.turn,
					type           : s_t.type,
					credits_pick   : credits_s_t
				});
				
				if (s_t.type !== 'X')
					s_t_credits_assign.push({
						s_t          : s_t.s_t,
						semester     : s_t.semester,
						credits_pick : credits_s_t,
						credits      : parseInt($('#js_credit' + s_t.s_t).data().credits) + 1
					});
			}
		});

		// asignar semestre
		var less_semester = 100;
		unique_s_t.forEach(function(s_t){
			if (s_t.credits_pick >= higher_credits) {
				if (s_t.semester <= less_semester) {
					less_semester = s_t.semester;
					$('#js_period-assign')
						.data('semester', s_t.semester)
						.data('semester_roman', s_t.semester_roman);
				}
			}
		});

		// asignar creditos
		s_t_credits_assign.forEach(function(s_t){
			if (s_t.credits_pick >= higher_credits) {
				if (s_t.semester <= less_semester) {
					less_semester = s_t.semester;
					$('#js_period-assign')
						.data('credits', s_t.credits)
						.html(s_t.credits);
				}
			}
		});

		// If the pick course is more higher that current assign when it change
		if ($check_box.prop('checked')) {
			credits_current = $('#js_credits_current').data().credits;
			credits_assign = $('#js_period-assign').data().credits;
			if (credits_current > credits_assign) {
				credits_current = credits_current - credits_course;
				$('#js_credits_current')
					.data('credits', credits_current)
					.html(credits_current);
				$check_box.prop('checked', false);

				this.model.set({carry : false});
			}
		}
	},
});