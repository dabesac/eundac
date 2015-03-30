$(function(){
	$('#main_search_form').on('submit', function(e){
		e.preventDefault();
		var datasearch = $(this).find('input[type=text]').val();
		if (datasearch) {
			location.href = '/default/global/search/data/' + datasearch;
		}
	});


	var global_function = global();
	global_function.toggleTabs($('ul.tabs'), $('ul.tabs').siblings('.tabs_data'));
	global_function.auxiliarVoh($('#js_auxiliar'));
	global_function.scrollOldAux();

	//Backbone FTW!!!
	eUndac.app = new eUndac.Routers.Base();

	//Funciones globales
	$('input[js-type=date]').datepicker();

	displayMainMenu();
});

function changeContent(el_1, el_2){
	el_1.addClass('inactive');
	setTimeout(function() {
		el_1.removeClass('active inactive');
		el_2.addClass('active');
	}, 300);
}

function chargeContent(el, model){
	el.children('img').addClass('fadeUp');
	el.children('.group').addClass('inactive');
	setTimeout(function() {
		el.children('.group').removeClass('active inactive');

		el.children('.group').find('.item-table').remove();
		el.children('.group').find('p.empty').slideDown('fast');
		charge();
	}, 300);

	function charge(){
		model.fetch({
			success : function(){
				el.children('img').addClass('fadeOut');
				setTimeout(function() {
					el.children('img').removeClass('fadeUp fadeOut');
					el.children('.group').addClass('active');
				}, 300);
			},
			error : function(){
				el.children('img').addClass('fadeOut');
				setTimeout(function() {
					el.children('img').removeClass('fadeUp fadeOut');
					el.children('.group').addClass('active');
				}, 300);
			}
		});
	}
}

function toggleDetail(button, content_1, content_2){
	if (!content_1.hasClass('active')) {
		button.html('Mas Detalles');
		content_2.addClass('inactive');
		setTimeout(function() {
			content_1.addClass('active');
			content_2.removeClass('active inactive');
		}, 300);
	} else {
		button.html('Menos Detalles');
		content_1.addClass('inactive');
		setTimeout(function() {
			content_2.addClass('active');
			content_1.removeClass('active inactive');
		}, 300);
	}
}

function toggleContent(content_1, content_2){
	content_1.addClass('inactive');
	setTimeout(function() {
		content_2.addClass('active');
		content_1.removeClass('active inactive');
	}, 300);
}

function toggleDelete(content){
	if (!content.hasClass('active')) {
		content.addClass('active');
	} else {
		content.addClass('inactive');
		setTimeout(function() {
			content.removeClass('active inactive');
		}, 300);
	}
}

function cleanForm(form){
	form.find('input[type=text]').val('');
}

function saveNew(form, model, collection, scope){
	var $messages_site = form.find('.js_form_messages');
	var $button_submit = form.find('input[type=submit]');

	$button_submit
		.val('Guardando...')
		.attr('disabled', true);


	model.save(null, {
		success : function(model, response){
			if (response.success) {
				//agregar id al modelo
				model.set({ idget : response.idget,
							success : false });

				//si se agrega un activo
				if (response.new_active){
					var last_active = collection.where({state : 'A'});
					var active = last_active[0];
					if (active) {
						active.set({state : 'T', state_before : 'A'});
						active.save();
					}
				}

				//Agregar el periodo si esta en el año indicado
				if (response.render)
					collection.add(model, {id : response.idget});


				// Limpiando el formulario y cambiando despues de 2 seg
				$messages_site.html(scope.template_msg_success());
				$button_submit
					.val('Guardado :)');

				setTimeout(function() {
					changeContent(scope.$content_new, scope.$content_main);
					cleanForm(form);
					$button_submit
						.removeAttr('disabled')
						.val('Guardar');
					$messages_site.html('');
				}, 2000);
			} else {
				$messages_site.html(scope.template_msg_error(response));
				$button_submit
					.removeAttr('disabled')
					.val('Guardar');
			}
		},
		error : function(){
			$button_submit
				.removeAttr('disabled')
				.val('Guardar');
			$messages_site.html(scope.template_msg_danger());
		}
	});
}

function update(form, scope) {
	var $messages_site = form.find('.js_form_messages');
	var $button_submit = form.find('input[type=submit]');
	$button_submit
		.val('Guardando...')
		.attr('disabled', true);

	scope.model.save(null, {
		success : function(model, response){
			if (response.success) {
				$button_submit
					.val('Guardado :)');

				$messages_site.html(scope.template_msg_success());

				setTimeout(function() {
					$('html body').animate({
						scrollTop : scope.$el.offset().top - 70
					}, 400);
				}, 1700);
				
			} else {
				$button_submit
					.val('Guardar')
					.removeAttr('disabled');

				$messages_site.html(scope.template_msg_error(response));
			}
		},
		error : function(){
			$button_submit
				.val('Guardar')
				.removeAttr('disabled');

			$messages_site.html(scope.template_msg_danger());
		}
	});
}

function deleteItem(button, scope){
	button
		.attr('disabled', true)
		.html('Eliminando...');
	scope.model.destroy({
		success : function(model, response){
			if (response.success) {
				button.html('Eliminado :)');

				setTimeout(function() {
					scope.$el.addClass('delete');
					setTimeout(function() {
						scope.$el.remove();
					}, 300);
				}, 1300);
			} else {
				button
					.removeAttr('disabled')
					.html('Si, estoy seguro');
			}
		},
		error : function(){
			button
				.removeAttr('disabled')
				.html('Si, estoy seguro');
		}
	});
}

// ------ G L O B A L --------
function displayMainMenu(){
	$('#js_main-btn-menu').on('click', function(){
		if (!$('#js_main-menu-ul').hasClass('active')) {
			$('#js_main-menu-ul').addClass('active');
		} else {
			$('#js_main-menu-ul').addClass('inactive');
			setTimeout(function() {
				$('#js_main-menu-ul').removeClass('active inactive');
			}, 300);

		}
	});
}

function global(){
	function toggleTabs(ul, tabs){
		var id_tab;
		$(ul).find('a').on('click', function(){
			id_tab = $(this).attr('going-to');
			$(ul).find('a.active').removeClass('active');
			$(this).addClass('active');
			
			$(tabs).find('.tab_data.active').removeClass('active');
			$('#tab_'+id_tab).addClass('active');
		});
	}
	function auxiliarVoh(aux){
		$(window).scroll(function(){
            if($(this).scrollTop() >= 110){
               aux.addClass('fixed');
            }
            else{
               aux.removeClass('fixed');
            }
        });
	}
	function scrollOldAux(){
		$(window).scroll(function(){
            if($(this).scrollTop() >= 120){
               $('.navigationButtonsSVCLayoutRight').addClass('navigationButtonsSVCFixed');
               $('.navigationButtonsSVCLayoutLeft').addClass('navigationButtonsSVCFixed');
            }
            else{
               $('.navigationButtonsSVCLayoutRight').removeClass('navigationButtonsSVCFixed');
               $('.navigationButtonsSVCLayoutLeft').removeClass('navigationButtonsSVCFixed');
            }
        });
	}
	return {
		toggleTabs   : toggleTabs,
		auxiliarVoh  : auxiliarVoh,
		scrollOldAux : scrollOldAux
	};
}