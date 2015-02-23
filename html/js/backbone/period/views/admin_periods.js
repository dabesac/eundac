eUndac.Views.Admin_Periods = Backbone.View.extend({
	tagName : 'article',

	className : 'item-table',

	events : {
		'click .js_button-more-detail'    : 'moreDetail',
		'click .js_button-back'           : 'backToNoDetail',
		'click .js_button-edit'           : 'editPeriod',
		'click .js_button-back_detail'    : 'backToDetail',
		'click .js_button-delete'         : 'toggleDeleteCape',
		'click .js_button-delete-cancel'  : 'toggleDeleteCape',
		'click .js_button-delete-confirm' : 'confirmDelete',
		'submit .js_form-edit'            : 'submitEditForm' 
	},

	initialize : function(){
		var self = this;
		this.model.bind('change', function(){
			//update
			if (this.model.changed.success) {
				setTimeout(function() {
					if (self.model.changed.hard_render) {
							self.render().afterRender();
							self.model.set({hard_render : false});
					} else {
							self.renderUpdate().afterRender();
					}
					self.model.set({success : false});
				}, 2000);
			}
		}, this);

		
		//template para vista de periodo, detalle y edicion...
		this.template = swig.compile($('#template_period').html());
		template_period_detail = swig.compile($('#template_period-detail').html());
		template_period_edit   = swig.compile($('#template_period-edit').html());

		//Templates para mensajes para edicion
		this.template_msg_error   = swig.compile($('#template_msg_error').html());
		this.template_msg_success = swig.compile($('#template_msg_success').html());
		this.template_msg_danger  = swig.compile($('#template_msg_danger_update').html());

		this.render().afterRender();
	},

	render : function(){
		var data = this.model.toJSON();
		var html = this.$el.html(this.template(data));

		if (data.state === 'A') {
			$('.js_group_active').append(html);
			$('.js_group_active').find('p.empty').fadeOut('fast');
		} else if (data.state === 'T') {
			$('.js_group_temporary').append(html);
			$('.js_group_temporary').find('p.empty').fadeOut('fast');
		} else if (data.state === 'C') {
			$('.js_group_close').append(html);
			$('.js_group_close').find('p.empty').fadeOut('fast');
		}

		return this;
	},

	renderUpdate : function(){
		var data = this.model.toJSON();
		this.$el.html(this.template(data));
		return this;
	},

	afterRender : function(){
		//render de detalle
		var data = this.model.toJSON();
		var html_period_detail = template_period_detail(data);
		this.$el.find('.js_interactive_sections').append(html_period_detail);

		//render de edicion
		var html_period_edit = template_period_edit(data);
		this.$el.find('.js_interactive_sections').append(html_period_edit);
		$('input[js-type=date').datepicker();


		//elementos de la vista
		this.$content_no_detail = this.$el.find('.js_section-no-detail');
		this.$content_detail    = this.$el.find('.js_section-detail');
		this.$content_edit      = this.$el.find('.js_section-edit');
		this.$delete_cape       = this.$el.find('.js_delete-cape');

		//form
		this.$form          = this.$el.find('.js_form-edit');
		this.$messages_site = this.$el.find('.js_form_messages');
	},

	moreDetail : function(){
		toggleContent(this.$content_no_detail, this.$content_detail);
	},

	editPeriod : function(){
		toggleContent(this.$content_detail, this.$content_edit);
	},

	backToDetail : function(){
		toggleContent(this.$content_edit, this.$content_detail);	
	},

	backToNoDetail : function(){
		toggleContent(this.$content_detail, this.$content_no_detail);	
	},

	submitEditForm : function(e){
		e.preventDefault();

		var periodData = this.$form.serializeJSON();
		this.model.set(periodData, {wait : true});

		update(this.$form, this);
		this.searchForEmpty();
	},

	searchForEmpty : function(){
		setTimeout(function() {
			if ($('.js_group_active').find('article').length === 0) {
				$('.js_group_active').find('p.empty').fadeIn('fast');
			}
			if ($('.js_group_temporary').find('article').length === 0) {
				$('.js_group_temporary').find('p.empty').fadeIn('fast');
			}
			if ($('.js_group_close').find('article').length === 0) {
				$('.js_group_close').find('p.empty').fadeIn('fast');
			}
		}, 2200);
	},

	toggleDeleteCape : function(){
		toggleDelete(this.$delete_cape);
	},

	confirmDelete : function(e){
		var $btn_delete = $(e.target);
		
		deleteItem($btn_delete, this);
		this.searchForEmpty();
	}
});