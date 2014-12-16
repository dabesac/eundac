$(function(){
	var dataGet    = '';
	var curriculum = curriculum();

	//Rol
	var rol = $('#rol').val();

	if (rol == 'RC') {
		$('#selectFaculty').on('change', function(){
			dataGet = $(this).val();
			curriculum.chargeSchools(dataGet);
		});

		$('#selectSchool').on('change', function(){
			dataGet = $(this).val();
			curriculum.chargeCurriculums(dataGet);
		});
	}else if (rol == 'DR'){
		dataGet = $('#school').val();
		curriculum.chargeCurriculums(dataGet);
	};


	//Closure curriculum
	function curriculum(){
		var speciality    = '';
		var showErrorTime = '';
		var idGet         = '';
		var idJs          = '';


		function chargeSchools(dataGet){
			$('#dataCurriculums').html('');
			if (dataGet) {
				$('#selectSchool')
					.html('<option value="">Cargando...</option>')
					.load('/default/global/listschools/id/' + dataGet);
			}else{
				$('#selectSchool').html('<option value="">Escuela</option><option value="">Primero seleccione alguna facultad ¬¬</option>');
			};
		}

		function chargeCurriculums(dataGet){
			if (dataGet) {
				$('#dataCurriculums')
					.html('<br><br><center><img src="/img/spinner.gif" alt="Loading" /></center>')
					.load('/curricula/curricula/listcurriculums/id/' + dataGet, function(){
						speciality = $('#selectSpeciality').val();
						showCurBySpe(speciality);
						selectSpeciality();
					});
			}else{
				$('#dataCurriculums').html('');
			};
		}

		function showCurBySpe(speciality){
			$(".curriculums").each(function(){
				currentLiText = $(this).text(),
		        showCurrentLi = currentLiText.indexOf(speciality) !== -1;
		        $(this).toggle(showCurrentLi);
		        if (showCurrentLi) {
		    		existData = 0;
		        };
		    });

			if (rol == 'RC') {
			    $('html body').animate({
					scrollTop : $('#selectFaculty').offset().top
				});
			};

		}

		function selectSpeciality(){
			$('#selectSpeciality').on('change', function(){
				speciality = $(this).val();
				showCurBySpe(speciality);

				//cargar Formulario por especialidad
				$('#dataFormNewCur')
					.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
					.load('/curricula/curricula/newcurricula/id/' + speciality, function(){
						newCurriculum();
					});
			});

			//cargar Formulario cuando no tiene especialidades
			$('#dataFormNewCur')
				.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
				.load('/curricula/curricula/newcurricula/id/' + speciality, function(){
					newCurriculum();
				});

			var idActivateJs;
			$('.curriculum').each(function(i, element){
				idActivateJs = $(element).attr('idactivatejs');
				actionsPerCurriculum(idActivateJs);
			});
		}

		function actionsPerCurriculum(idActivate){
			//Botones por cada curricula
			var curriculumThis  = $('#curriculum' + idActivate);
			var btn_detail_cur  = $(curriculumThis).find('.modify').children('.btnDetailCur');
			var btn_derive_cur  = $(curriculumThis).find('.modify').children('.btn_derive');
			var btn_delete_cur  = $(curriculumThis).find('.modify').children('.btn_delete');
			var stateCurriculum = $(curriculumThis).attr('state');
			
			//quitarle el show curriculum si lo tuviera
			if ($(curriculumThis).hasClass('showCurriculum')) {
				$(curriculumThis).removeClass('showCurriculum');
			};

			//Detalle de Curricula
			$(btn_detail_cur).on('click', function(){
				cleanOfAlerts();

				idGet = $(this).attr('ide');
				idJs  = $(this).attr('idjs');
				$('#dataDetailCur')
					.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
					.load('/curricula/curricula/admincurriculum/state/' + stateCurriculum + '/id/' + idGet, function(){
						view_adminCurriculum(idGet, idJs);
					});
			});

			//Boton para derivar
			var goTo;
			var btn_dede_clicked;
			$(btn_derive_cur).on('click', function(){
				btn_dede_clicked = $(this);

				var div_derive = $(btn_dede_clicked).siblings('.derive_from_draft');
				goTo           = $(btn_dede_clicked).attr('goto');
				idGet          = $(btn_dede_clicked).attr('ide');
				idJs           = $(btn_dede_clicked).attr('idjs');

				$(btn_dede_clicked).toggleClass('active_a');

				//Esconder si hay otros derivados abiertos
				var fromTo = '';
				$('.derive_from_draft').each(function(index, element){
					fromTo = $(element).attr('fromto');
					if (goTo != fromTo) {
						if ($(element).hasClass('active_derive')) {
							$(element).siblings('.btn_derive').removeClass('active_a');
							$(element).addClass('hide_derive');
							setTimeout(function(){$(element).removeClass('active_derive hide_derive');}, 300);
						};
					};
				});

				//Esconder si hay un delete abierto
				$('.delete_curriculum').each(function(index, element){
					if ($(element).hasClass('active_confirm')) {
						$(element).addClass('hide_confirm');
						$('.btn_delete').removeClass('active_delete');
						setTimeout(function(){
							$(element).removeClass('active_confirm hide_confirm');
							$(element).parent().slideUp(150);
						}, 300);
					};
				});

				//Mostar y esconder derive
				if (!$(div_derive).hasClass('active_derive')) {
					$(div_derive).addClass('active_derive');
				} else {
					$(div_derive).addClass('hide_derive');
					setTimeout(function(){$(div_derive).removeClass('active_derive hide_derive');}, 300);
				};

			});

			//Boton delete para abrir alerta
			$(btn_delete_cur).on('click', function(){
				btn_dede_clicked = $(this);

				goTo               = $(btn_dede_clicked).attr('goto');
				idGet              = $(btn_dede_clicked).attr('ide');

				$('.btn_derive').removeClass('active_a active_active active_temporary active_close');
				$('.derive_from_draft').each(function(index, element){
					if ($(element).hasClass('active_derive')) {
						$(element).addClass('hide_derive');
						setTimeout(function(){
							$(element).removeClass('active_derive hide_derive');
						}, 300);
					};
				});
			});

			//Boton para abrir alertas
			$('#curriculum' + idActivate + ' .derive_from_draft a, #curriculum' + idActivate + ' .btn_delete').on('click', function(){
				//Esconder Derive Btns
				var btnClick   = $(this);
				var deriveBtns = $(btnClick).parent('.derive_from_draft');

				$(deriveBtns).addClass('hide_derive');
				setTimeout(function(){$(deriveBtns).removeClass('active_derive hide_derive');}, 300);
				

				//Verificar si hay algun confirm alert abierto
				var type = $(btnClick).attr('type');

				var fromType = '';
				var fromTo = '';
				$('.confirm_side').each(function(index, element){
					fromTo   = $(element).parent().attr('fromto');
					fromType = $(element).attr('type');
					if (goTo != fromTo) {
						if ($(element).hasClass('active_confirm')) {
							$(element).addClass('hide_confirm');
							setTimeout(function(){
								$(element).removeClass('active_confirm hide_confirm')
								$(element).parent().slideUp(150);
							}, 300);
						};
					}else{
						if (type != fromType) {
							if ($(element).hasClass('active_confirm')) {
								$(element).addClass('hide_confirm');
								setTimeout(function(){
									$(element).removeClass('active_confirm hide_confirm')
								}, 300);
							};
						}
					};
				});

				$('.btn_delete').removeClass('active_delete');
				$('.btn_derive').removeClass('active_a active_active active_temporary active_close');

				//mostrar Alert
				var classType = type + '_curriculum';
				var classBtn  = 'active_' + type;

				var confirm_alert = $('#curriculum_confirm' + goTo).find('.' + classType);

				$('#curriculum_confirm' + goTo).slideDown(150)
				$(confirm_alert).addClass('active_confirm');

				$(btn_dede_clicked)
					.removeClass('active_a active_active active_temporary active_close')
					.addClass(classBtn);
			});

			//Cancelar Confirm
			$('#curriculum_confirm' + idActivate + ' .confirm_side a:nth-child(3)').on('click', function(){
				cleanOfAlerts();
			});

			//Confirmar cambio de estado o eliminación
			$('#curriculum_confirm' + idActivate + ' .confirm_side a:nth-child(2)').on('click', function(){
				var why_call_me = $(this).attr('whycallme')

				$(this).html('<img src="/img/spinner.gif" alt="Loading..." />');

				iCallYou(why_call_me, idGet, goTo);
				
			});
		}

		function cleanOfAlerts(){
			//Verificar si hay algun confirm alert abierto
			$('.btn_derive').removeClass('active_a active_active active_temporary active_close');
			$('.btn_delete').removeClass('active_delete');
			$('.confirms_side .confirm_side').each(function(index, element){
				if ($(element).hasClass('active_confirm')) {
					$(element).addClass('hide_confirm');
					setTimeout(function(){
						$(element).removeClass('active_confirm hide_confirm');
						$(element).parent().slideUp(150);
					}, 300);
				};
			});

			//Verificar si hay algun driveBtns abierto...
			$('.derive_from_draft').each(function(index, element){
				if ($(element).hasClass('active_derive')) {
					$(element).siblings('.btn_derive').removeClass('active_a');
					$(element).addClass('hide_derive');
					setTimeout(function(){$(element).removeClass('active_derive hide_derive');}, 300);
				};
			});
		}

		function enumerate(curriculums){
			var empty_curriculums = true;
			$(curriculums).children('.curriculum').each(function(index, element){
				$(element).children('span:nth-child(1)').html(index + 1);
				empty_curriculums = false;
			});

			if ($(curriculums).hasClass('div_temporary')) {
				//Si esta Vacio cambiar
				if ($(curriculums).parent().hasClass('main_hide')) {
					$(curriculums).parent().slideDown('fast');
					$(curriculums).parent().siblings('.for_message').slideUp('fast');
				};
			};

			if (empty_curriculums) {
				$(curriculums).children('.empty_message').slideDown('fast');

				//solo se escondera si es borrador
				if ($(curriculums).hasClass('div_draft')) {
					setTimeout(function(){
						$(curriculums).parent('.main').slideUp('fast');
						$(curriculums).children('.empty_message').slideUp('fast');
					}, 7000);
				};
			} else {
				$(curriculums).siblings('.empty').slideUp('fast');
				$(curriculums).children('.empty_message').slideUp('fast');
			};
		}

		//tmre ya no se me ocurren nombres D:
		function iCallYou(why_call_me, idGet, idJs){
			$.ajax({
				url  : '/curricula/curricula/icallyou',
				data : {	id : idGet,
							why : why_call_me },
				dataType : 'json',
				success  : function(data){
					console.log(data);
					if (data.success === 1) {
						cleanOfAlerts();

						//Nombre y Años
						var dataCurriculum  = new Object();
						dataCurriculum.name = $('#curriculum' + idJs + ' span:nth-child(2)').html();
						dataCurriculum.year = $('#curriculum' + idJs + ' span:nth-child(3)').html();

						if (why_call_me == 'A') {
							var div_to_change   = $('#curriculums' + speciality).find('.div_active');
							var div_from_change = $('#curriculum' + idJs).parent();

							//Actual Activo
							var curriculumBefore = $(div_to_change).children('.curriculum');
							if ($(curriculumBefore).attr('state')) {
								var dataCurriculumChange   = new Object();
								dataCurriculumChange.name  = $(curriculumBefore).children('span:nth-child(2)').html();
								dataCurriculumChange.year  = $(curriculumBefore).children('span:nth-child(3)').html();
								dataCurriculumChange.idJs  = $(curriculumBefore).attr('idactivatejs');
								dataCurriculumChange.idGet = $(curriculumBefore).attr('idget');
							};

							$('#curriculum' + idJs).addClass('removeCurriculum');
							setTimeout(function(){
								$('#curriculum' + idJs + ', #curriculum_confirm' + idJs).remove();

								//Forma HORRIBLE de agregar div curricula activa
								var curriculumHtml = '<div id="curriculum'+ idJs +'" class="curriculum showCurriculum" idactivatejs="'+ idJs +'" idget="'+ idGet +'" state="A">'+
														'<span><span class="glyphicon glyphicon-ok"></span></span>'+
														'<span>'+ dataCurriculum.name +'</span>'+
														'<span>'+ dataCurriculum.year +'</span>'+
														'<div class="modify active_modify">'+
															'<a href="##">Ver Cursos</a>'+
															'<a href="##" ide="'+ idGet +'" idjs="'+ idJs +'" class="btnDetailCur" data-toggle="modal" data-target="#modalDetailCur" title="Mire los detalles de esta currícula...">Detalles</a>'+
														'</div>'+
													'</div>';
								$(div_to_change).html(curriculumHtml);
								actionsPerCurriculum(idJs);

								//Agregar la que estaba activa a Temporales
								curriculumHtml = '<div id="curriculum'+ dataCurriculumChange.idJs +'" class="curriculum showCurriculum" idactivatejs="'+ dataCurriculumChange.idJs +'" state="T">'+
													'<span><span class="glyphicon glyphicon-ok"></span></span>'+
													'<span>'+ dataCurriculumChange.name +'</span>'+
													'<span>'+ dataCurriculumChange.year +'</span>'+
													'<div class="modify temporary_modify">'+
														'<a href="##">Administrar Cursos</a>'+
														'<a href="##" ide="'+ dataCurriculumChange.idGet +'" idjs="'+ dataCurriculumChange.idJs +'" class="btnDetailCur" data-toggle="modal" data-target="#modalDetailCur" title="Mire los detalles de esta currícula...">Detalles</a>'+
														'<a href="##" class="btn_derive" ide="'+ dataCurriculumChange.idGet +'" goto="'+ dataCurriculumChange.idJs +'">Derivar</a>'+
														'<div class="derive_from_draft" fromto="'+ dataCurriculumChange.idJs +'">'+
															'<a href="##" type="active">Activar Curricula</a>'+
															'<a href="##" type="close">Cerrar Curricula</a>'+
														'</div>'+
													'</div>'+
												'</div>'+
												'<div id="curriculum_confirm'+ dataCurriculumChange.idJs +'" fromto="'+ dataCurriculumChange.idJs +'" class="confirms_side">'+
													'<div class="confirm_side delete_curriculum" type="delete">'+
														'<p>Confírme si desea eliminar esta currícula...</p>'+
														'<a href="##" whycallme="D">Si, quiero eliminarla</a>'+
														'<a href="##">Cancelar</a>'+
													'</div>'+
													'<div class="confirm_side active_curriculum" type="active">'+
														'<p>Confírme si desea activar esta currícula, si hay otra activa ésta la remplazara...</p>'+
														'<a href="##" whycallme="A">Si, quiero activarla</a>'+
														'<a href="##">Cancelar</a>'+
													'</div>'+
													'<div class="confirm_side temporary_curriculum" type="temporary">'+
														'<p>Confírme si desea mover esta currícula a temporales...</p>'+
														'<a href="##" whycallme="T">Si, quiero moverla a temporales</a>'+
														'<a href="##">Cancelar</a>'+
													'</div>'+
													'<div class="confirm_side close_curriculum" type="close">'+
														'<p>Confírme si desea cerrar esta currícula...</p>'+
														'<a href="##" whycallme="C">Si, quiero cerrarla</a>'+
														'<a href="##">Cancelar</a>'+
													'</div>'+
												'</div>';

								var div_target = $('#curriculums' + speciality).find('.div_temporary');

								$(div_target).prepend(curriculumHtml);
								actionsPerCurriculum(dataCurriculumChange.idJs);

								enumerate(div_from_change);
								enumerate(div_target);
							}, 300);
						}else if(why_call_me == 'T'){
							var div_to_change   = $('#curriculums' + speciality).find('.div_temporary');
							var div_from_change = $('#curriculum' + idJs).parent();

							$('#curriculum' + idJs).addClass('removeCurriculum');

							setTimeout(function(){
								$('#curriculum' + idJs + ', #curriculum_confirm' + idJs).remove();
								//Forma HORRIBLE de agregar div curricula activa
								var curriculumHtml = '<div id="curriculum'+ idJs +'" class="curriculum showCurriculum" idactivatejs="'+ idJs +'" state="T">'+
														'<span><span class="glyphicon glyphicon-ok"></span></span>'+
														'<span>'+ dataCurriculum.name +'</span>'+
														'<span>'+ dataCurriculum.year +'</span>'+
														'<div class="modify temporary_modify">'+
															'<a href="##">Administrar Cursos</a>'+
															'<a href="##" ide="'+ idGet +'" idjs="'+ idJs +'" class="btnDetailCur" data-toggle="modal" data-target="#modalDetailCur" title="Mire los detalles de esta currícula...">Detalles</a>'+
															'<a href="##" class="btn_derive" ide="'+ idGet +'" goto="'+ idJs +'">Derivar</a>'+
															'<div class="derive_from_draft" fromto="'+ idJs +'">'+
																'<a href="##" type="active">Activar Curricula</a>'+
																'<a href="##" type="close">Cerrar Curricula</a>'+
															'</div>'+
														'</div>'+
													'</div>'+
													'<div id="curriculum_confirm'+ idJs +'" fromto="'+ idJs +'" class="confirms_side">'+
														'<div class="confirm_side delete_curriculum" type="delete">'+
															'<p>Confírme si desea eliminar esta currícula...</p>'+
															'<a href="##" whycallme="D">Si, quiero eliminarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side active_curriculum" type="active">'+
															'<p>Confírme si desea activar esta currícula, si hay otra activa ésta la remplazara...</p>'+
															'<a href="##" whycallme="A">Si, quiero activarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side temporary_curriculum" type="temporary">'+
															'<p>Confírme si desea mover esta currícula a temporales...</p>'+
															'<a href="##" whycallme="T">Si, quiero moverla a temporales</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side close_curriculum" type="close">'+
															'<p>Confírme si desea cerrar esta currícula...</p>'+
															'<a href="##" whycallme="C">Si, quiero cerrarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
													'</div>';
								
								$(div_to_change).prepend(curriculumHtml);
								actionsPerCurriculum(idJs);

								enumerate(div_from_change);
								enumerate(div_to_change);
							}, 300);
						}else if(why_call_me == 'C'){
							var div_to_change   = $('#curriculums' + speciality).find('.div_close');
							var div_from_change = $('#curriculum' + idJs).parent();

							$('#curriculum' + idJs).addClass('removeCurriculum');


							setTimeout(function(){
								$('#curriculum' + idJs + ', #curriculum_confirm' + idJs).remove();
								//Forma HORRIBLE de agregar div curricula activa
								var curriculumHtml = '<div id="curriculum'+ idJs +'" class="curriculum" idactivatejs="'+ idJs +'" state="C">'+
														'<span>0</span>'+
														'<span>'+ dataCurriculum.name +'</span>'+
														'<span>'+ dataCurriculum.year +'</span>'+
														'<div class="modify close_modify">'+
															'<a href="##">Ver Cursos</a>'+
															'<a href="##" ide="'+ idGet +'" idjs="'+ idJs +'" class="btnDetailCur" data-toggle="modal" data-target="#modalDetailCur" title="Mire los detalles de esta currícula...">Detalles</a>'+
														'</div>'+
													'</div>';

								$(div_to_change).prepend(curriculumHtml);
								actionsPerCurriculum(idJs);

								enumerate(div_from_change);
								enumerate(div_to_change);
							}, 300);
						}else if(why_call_me == 'D'){
							var div_from_change = $('#curriculum' + idJs).parent();
							$('#curriculum' + idJs).addClass('removeCurriculum');
							setTimeout(function(){
								$('#curriculum' + idJs + ', #curriculum_confirm' + idJs).remove();
								enumerate(div_from_change);
							}, 300);
						};
					};
				},
				error : function(){

				}
			});
		}

		
		function newCurriculum(){
			$('#formCurriculum').on('submit', function(e){
				e.preventDefault();

				$('#idChargeSide').slideDown('fast');

				var form      = $(this);
				var btnSubmit = $(this).find('input[type=submit]');

				btnSubmit
					.attr('disabled', 'disabled')
					.val('Guardando...');

				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/savenew',
					data     : $(this).serialize(),
					dataType : 'json',
					success  : function(data){
						if (data.success === 1) {
							//forma HORRIBLE de agregar una nueva curricula en vivo :(
							var htmlNewCurriculum = '<div class="curriculum" id="curriculum'+ data.dataNew.idJs +'" state="B">' +
														'<span>RC</span>' +
														'<span><span class="nameChangeEdit">'+ data.dataNew.name + '</span> <small>'+ data.dataNew.curid +'</small></span>' +
														'<span>Año '+ data.dataNew.year +'</span>' +
														'<div class="modify draftModify">' +
															'<a href="##" title="Administre los cursos correspondientes a esta currícula...">Administrar Cursos</a>'+
															'<a href="##" ide="'+ data.dataNew.id +'" idjs="'+ data.dataNew.idJs +'" class="btnDetailCur" data-toggle="modal" data-target="#modalDetailCur" title="Mire los detalles de esta currícula...">Detalles</a>'+
															'<a href="##" class="btn_derive" ide="'+ data.dataNew.id +'" goto="'+ data.dataNew.idJs +'">Derivar</a>'+
															'<a href="##" class="btn_delete" ide="'+ data.dataNew.id +'" type="delete" goto="'+ data.dataNew.idJs +'">Eliminar</a>'+
															'<div class="derive_from_draft" fromto="'+ data.dataNew.idJs +'">'+
																'<a href="##" type="active">Activar Curricula</a>'+
																'<a href="##" type="temporary">Mover a Temporales</a>'+
																'<a href="##" type="close">Cerrar Curricula</a>'+
															'</div>'+
														'</div>' +
													'</div>'+
													'<div id="curriculum_confirm'+ data.dataNew.idJs +'" fromto="'+ data.dataNew.idJs +'" class="confirms_side">'+
														'<div class="confirm_side delete_curriculum" type="delete">'+
															'<p>Confírme si desea eliminar esta currícula...</p>'+
															'<a href="##" whycallme="D">Si, quiero eliminarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side active_curriculum" type="active">'+
															'<p>Confírme si desea activar esta currícula, si hay otra activa ésta la remplazara...</p>'+
															'<a href="##" whycallme="A">Si, quiero activarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side temporary_curriculum" type="temporary">'+
															'<p>Confírme si desea mover esta currícula a temporales...</p>'+
															'<a href="##" whycallme="T">Si, quiero moverla a temporales</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
														'<div class="confirm_side close_curriculum" type="close">'+
															'<p>Confírme si desea cerrar esta currícula...</p>'+
															'<a href="##" whycallme="C">Si, quiero cerrarla</a>'+
															'<a href="##">Cancelar</a>'+
														'</div>'+
													'</div>';

						
							var div_to_add = $('#curriculums' + speciality).find('.div_draft');
							$(div_to_add).prepend(htmlNewCurriculum);
							actionsPerCurriculum(data.dataNew.idJs);
							enumerate(div_to_add);
							

							$(form).find('.form-control').val('');
							//$(form).reset();

							$('#idErrorsSide, #idChargeSide').slideUp('fast');
							$('#idSuccessSide').slideDown('fast');

							btnSubmit.val('Guardado!');

							clearTimeout(showErrorTime);
							showErrorTime = setTimeout(function(){
												$('#idSuccessSide').slideUp('fast');
												$('#modalNewCur').modal('hide');
												btnSubmit
													.removeAttr('disabled')
													.val('Guardar');
											}, 2000);


							//Bajar si no hay borradores aun creados
							$('#curriculums' + speciality).find('.main_draft').slideDown('fast');
						}else if (data.success === 0) {
							$('#idChargeSide').slideUp('fast');
							$('#idErrorsSide .errors').html('');
							$('#idErrorsSide')
								.removeClass('errorBase')
								.addClass('errorData')
								.slideDown('fast');
							for (var i = 0; i <= data.errors.length - 1; i++) {
								$('#idErrorsSide .errors').append('<p>' + data.errors[i] + '</p>');
							};

							clearTimeout(showErrorTime);
							showErrorTime = setTimeout(function(){
												$('#idErrorsSide').slideUp('fast');
											}, 7000);
						};
					},
					error : function(){
						console.log('error de base de datos');
						$('#idChargeSide').slideUp('fast');
						$('#idErrorsSide')
							.removeClass('errorData')
							.addClass('errorBase')
							.slideDown('fast');

						$('#idErrorsSide .errors').html('<p>Ups! Parece que hubo un error, fijese que no haya otra currícula en el mismo año y periódo igual al que esta creando...</p>');

						clearTimeout(showErrorTime);
						showErrorTime = setTimeout(function(){
											$('#idErrorsSide').slideUp('fast');
										}, 7000);
					}
				});
				btnSubmit
					.removeAttr('disabled')
					.val('Guardar');
			});
		}

		function view_adminCurriculum(idGet, idCur){
			//Función de botones
			//Editar
			$('#detailCur_btnEditCur').on('click', function(){
				$('#idDivDetailCurricula, #idHeaderDetail').fadeOut('fast', function(){
					$('#idDivEditCurriculum, #idHeaderEdit').fadeIn('fast');
				});
			});

			$('#detailCur_btnAdminCourses').on('click', function(){
				$('#idDivDetailCurricula, #idHeaderDetail').fadeOut('fast', function(){
					$('#idDivAdminCourses, #idHeaderAdmin').fadeIn('fast');
				});
			});

			$('#detailCur_edit_btnBackToDetail').on('click', function(){
				$('#idDivEditCurriculum, #idHeaderEdit').fadeOut('fast', function(){
					$('#idDivDetailCurricula, #idHeaderDetail').fadeIn('fast');
				});
			});

			$('#detailCur_courses_btnBackToDetail').on('click', function(){
				$('#idDivAdminCourses, #idHeaderAdmin').fadeOut('fast', function(){
					$('#idDivDetailCurricula, #idHeaderDetail').fadeIn('fast');
				});
			});

			//Cargar Detalle de curricula
			$('#idDivDetailCurricula')
				.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
				.load('/curricula/curricula/detailcurriculum/id/' + idGet, function(){
					view_detailCurriculum();
				});

			//Cargar Formulario para editar curricula
			$('#idDivEditCurriculum')
				.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
				.load('/curricula/curricula/editcurriculum/id/' + idGet, function(){
					view_editCurriculum(idGet, idJs);
				});

			//Cargar administracion de Cursos
			$('#idDivAdminCourses')
				.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
				.load('/curricula/curricula/admincourses/id/' + idGet, function(){
					view_adminCourses(idGet, idJs);
				});
		}

		function view_detailCurriculum(){
			//Para proximas acciones en esta vista agregar codigo aca
		}

		function view_editCurriculum(idGet, idJs){
			$('#formEditCurriculum').on('submit', function(e){
				e.preventDefault();

				var btnSubmit     = $(this).find('input[type=submit]');
				var nameCurricula = $(this).find('input[name=name]').val();

				btnSubmit
					.attr('disabled', 'disabled')
					.val('Guardando...');

				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/saveedit',
					data     : $(this).serialize(),
					dataType : 'json',
					success  : function(data){
						if (data.success === 1) {
							$('#idDivDetailCurricula')
								.html('<br><br><br><center><img src="/img/spinner.gif" alt="Loading..." /></center>')
								.load('/curricula/curricula/detailcurriculum/id/' + idGet, function(){
									view_detailCurriculum();
								});

							$('#idDivEditCurriculum, #idHeaderEdit').fadeOut('fast', function(){
								$('#idDivDetailCurricula, #idHeaderDetail').fadeIn('fast');
							});

							$('#curriculum' + idJs).find('span.nameChangeEdit').html(nameCurricula);
						}else if (data.success === 0) {
							$('#idErrorsSide_Edit .errors').html('');
							$('#idErrorsSide_Edit').slideDown('fast');
							for (var i = 0; i <= data.errors.length - 1; i++) {
								$('#idErrorsSide_Edit .errors').append('<p>' + data.errors[i] + '</p>');
							};

							clearTimeout(showErrorTime);
							showErrorTime = setTimeout(function(){
												$('#idErrorsSide_Edit').slideUp('fast');
											}, 7000);
						}else if (data.success === 2) {
							console.log('error de base de datos');
						};
					},
					error : function(){
						console.log('error de base de datos');
					}
				});
				
				btnSubmit
					.removeAttr('disabled')
					.val('Guardar');
			});
		}

		function view_adminCourses(){
			$('section.new_course').load('/curricula/curricula/newcourse/id/' + idGet, function(){
				addCourse();
			});

			$('#btn_add_course').on('click', function(){
				if (!$('#id_add_new_course').hasClass('new_course_active')) {
					$(this)
						.addClass('btn_cancel')
						.html('Cancelar');
					$(this).parent().parent().addClass('header_active');
					$('#id_add_new_course')
						.removeClass('new_course_hide')
						.addClass('new_course_active');
				}else {
					$(this)
						.removeClass('btn_cancel')
						.html('Agregar Curso');
					$(this).parent().parent().removeClass('header_active');
					$('#id_add_new_course')
						.removeClass('new_course_active')
						.addClass('new_course_hide');

					setTimeout(function(){
						$('#id_add_new_course')
							.removeClass('new_course_hide')
					}, 300);
				};
			});

		}

		function addCourse(){
			$('#form_add_new_course').on('submit', function(e){
				e.preventDefault();

				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/savecourse',
					data     : $(this).serialize(),
					//dataType : 'json',
					success  : function(data){
						console.log(data);
					},
					error : function(){
						console.log('Error al guardar el curso, mas fijo que sea en la base de datos...')
					}
				})
			});
		}

		return {
			chargeSchools     : chargeSchools,
			chargeCurriculums : chargeCurriculums
		}
	}
});