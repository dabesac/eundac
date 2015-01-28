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
	}


	//Closure curriculum
	function curriculum() {
		var speciality    = '';
		var idGet         = '';
		var idJs          = '';
		var showErrorTime;

		var fadeMessageError;

		function chargeSchools(dataGet){
			$('#dataCurriculums').html('');
			if (dataGet) {
				$('#selectSchool')
					.html('<option value="">Cargando...</option>')
					.load('/default/global/listschools/id/' + dataGet);
			}else{
				$('#selectSchool').html('<option value="">Escuela</option><option value="">Primero seleccione alguna facultad ¬¬</option>');
			}
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
			}
		}

		function showCurBySpe(speciality){
			$(".curriculums").each(function(){
				currentLiText = $(this).text(),
		        showCurrentLi = currentLiText.indexOf(speciality) !== -1;
		        $(this).toggle(showCurrentLi);
		        if (showCurrentLi) {
		    		existData = 0;
		        }
		    });

			if (rol == 'RC') {
			    $('html body').animate({
					scrollTop : $('#selectFaculty').offset().top
				});
			}

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
			}

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
						}
					}
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
					}
				});

				//Mostar y esconder derive
				if (!$(div_derive).hasClass('active_derive')) {
					$(div_derive).addClass('active_derive');
				} else {
					$(div_derive).addClass('hide_derive');
					setTimeout(function(){$(div_derive).removeClass('active_derive hide_derive');}, 300);
				}

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
					}
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
								$(element).removeClass('active_confirm hide_confirm');
								$(element).parent().slideUp(150);
							}, 300);
						}
					}else{
						if (type != fromType) {
							if ($(element).hasClass('active_confirm')) {
								$(element).addClass('hide_confirm');
								setTimeout(function(){
									$(element).removeClass('active_confirm hide_confirm');
								}, 300);
							}
						}
					}
				});

				$('.btn_delete').removeClass('active_delete');
				$('.btn_derive').removeClass('active_a active_active active_temporary active_close');

				//mostrar Alert
				var classType = type + '_curriculum';
				var classBtn  = 'active_' + type;

				var confirm_alert = $('#curriculum_confirm' + goTo).find('.' + classType);

				$('#curriculum_confirm' + goTo).slideDown(150);
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
				var why_call_me = $(this).attr('whycallme');

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
				}
			});

			//Verificar si hay algun driveBtns abierto...
			$('.derive_from_draft').each(function(index, element){
				if ($(element).hasClass('active_derive')) {
					$(element).siblings('.btn_derive').removeClass('active_a');
					$(element).addClass('hide_derive');
					setTimeout(function(){$(element).removeClass('active_derive hide_derive');}, 300);
				}
			});
		}

		

		//tmre ya no se me ocurren nombres D:
		function iCallYou(why_call_me, idGet, idJs){
			$.ajax({
				url  : '/curricula/curricula/icallyou',
				data : {	id : idGet,
							why : why_call_me },
				dataType : 'json',
				success  : function(data){
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
							}

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

								$(div_to_change).siblings('.empty').slideUp('fast');

								//Agregar la que estaba activa a Temporales
								if ($(curriculumBefore).attr('state')) {
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
									enumerate(div_target);
								}
								enumerate(div_from_change);
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
						}
					}
				},
				error : function(){

				}
			});
		}

		function newCurriculum(){
			$('#formCurriculum').on('submit', function(e){
				e.preventDefault();

				var form        = $(this);
				var btnSubmit   = $(this).find('input[type=submit]');
				var msg_error   = $(this).siblings('.msg_side').children('.msg.warning');
				var msg_success = $(this).siblings('.msg_side').children('.msg.success');
				
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

							jukeboxMsg(msg_success, msg_error, 2000);

							clearTimeout(showErrorTime);
							showErrorTime = setTimeout(function(){
												$('#modalNewCur').modal('hide');
												btnSubmit
													.removeAttr('disabled')
													.val('Guardar');
											}, 2000);

							//Bajar si no hay borradores aun creados
							$('#curriculums' + speciality).find('.main_draft').slideDown('fast');
						}else if (data.success === 0) {
							fillErrors(msg_error, data.errors);
							jukeboxMsg(msg_error, msg_success, 7000);
						}
					},
					error : function(){
						jukeboxMsg(msg_error, msg_success, 7000);
						$(msg_error).html('<p>Ups! Parece que hubo un error, fijese que no haya otra currícula en el mismo año y periódo igual al que esta creando...</p>');
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
			//Para proximas acciones en esta vista de detalle de curricula agregar codigo aca
		}

		function view_editCurriculum(idGet, idJs){
			$('#formEditCurriculum').on('submit', function(e){
				e.preventDefault();

				var btnSubmit     = $(this).find('input[type=submit]');
				var nameCurricula = $(this).find('input[name=name]').val();
				var msg_error     = $(this).siblings('.msg_side').children('.msg.warning');
				var msg_success   = $(this).siblings('.msg_side').children('.msg.success');

				$(btnSubmit)
					.attr('disabled', 'disabled')
					.val('Guardando...');

				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/saveedit',
					data     : $(this).serialize(),
					dataType : 'json',
					success  : function(data){
						if (data.success === 1) {
							jukeboxMsg(msg_success, msg_error, 2000);

							//Cargar de nuevo los detalles
							$('#idDivDetailCurricula')
								.html('<img src="/img/spinner.gif" alt="Loading..." />')
								.load('/curricula/curricula/detailcurriculum/id/' + idGet, function(){
									view_detailCurriculum();
								});

							//Cargar de nuevo los datos del curso para las curriculas
							$('section.new_course').load('/curricula/curricula/newcourse/id/' + idGet, function(){
								addCourse();
							});
						}else if (data.success === 0) {
							fillErrors(msg_error, data.errors);
							jukeboxMsg(msg_error, msg_success, 7000);
						}else if (data.success === 2) {
							console.log('error de base de datos');
						}
						btnSubmit
							.removeAttr('disabled')
							.val('Guardar');
					},
					error : function(){
						console.log('error de base de datos');
						btnSubmit
							.removeAttr('disabled')
							.val('Guardar');
					}
				});
			});
		}

		function view_adminCourses(){
			$('section.new_course').load('/curricula/curricula/newcourse/id/' + idGet, function(){
				addCourse();
			});

			//Funciones para el Boton Agregar Curso
			$('#btn_add_course').on('click', function(){
				$('#id_error_course_side').slideUp('fast');
				if (!$('#id_add_new_course').hasClass('active')) {
					$(this)
						.addClass('btn_cancel')
						.html('Cancelar');

					$(this).parent().parent().addClass('header_active');

					$('#id_add_new_course').addClass('active');
				}else {
					$(this)
						.removeClass('btn_cancel')
						.html('Agregar Curso');

					$(this).parent().parent().removeClass('header_active');

					$('#id_add_new_course').addClass('inactive');

					setTimeout(function(){
						$('#id_add_new_course')
							.removeClass('active inactive');
					}, 300);
				}
			});

			//agregar funciones por curso
			var id_course_js;
			$('.all_course_data').each(function(index, element){
				id_course_js = $(element).attr('idactivatejs');
				actionsPerCourse(id_course_js);
			});

		}

		function addCourse(){
			$('#form_add_new_course').on('submit', function(e){
				e.preventDefault();

				var btn_cancel_total = $(this).parent().parent().siblings('header').find('a#btn_add_course');

				var btnSubmit   = $(this).find('input[type=submit]');
				var msg_error   = $(this).siblings('.msg_side').children('.msg.warning');
				var msg_success = $(this).siblings('.msg_side').children('.msg.success');

				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/savecourse',
					data     : $(this).serialize(),
					dataType : 'json',
					success  : function(data){
						console.log(data);
						if (data.success === 1) {
							console.log(data.semester);
							jukeboxMsg(msg_success, msg_error, 1950);

							setTimeout(function() {
								$(btn_cancel_total)
									.removeClass('btn_cancel')
									.html('Agregar Curso');

								$(btn_cancel_total).parent().parent().removeClass('header_active');

								$('#id_add_new_course').addClass('inactive');

								setTimeout(function(){
									$('#id_add_new_course')
										.removeClass('active inactive');
								}, 300);
							}, 2000);
						}else if (data.success === 0){
							fillErrors(msg_error, data.errors);
							jukeboxMsg(msg_error, msg_success, 7000);
						}
					},
					error : function(){
						console.log('Error al guardar el curso, mas fijo que sea en la base de datos...');
					}
				});
			});

			//Agregar prerequisitos.....
			var semester_x_pre;
			var prerequisites_side = $('#id_select_pre').parent().siblings('.prerequisites');
			var empty_p            = $(prerequisites_side).children('p');

			$('#semid').on('change', function(){
				semester_x_pre = parseInt($(this).val());
				var interruptor = 0;
				var semester_act;
				$('#id_select_pre option').each(function(index, element){
					semester_act = parseInt($(element).attr('semester'));
					if(semester_act < semester_x_pre) {
						if (interruptor == 0 && semester_act == (semester_x_pre - 1)) {
							$('#id_select_pre option[semester=0]').html('Agregue un prerequisito...');
							$('#id_select_pre').val($(element).val());
							interruptor = 1;
						}
						$(element).removeAttr('hidden');
					} else {
						$(element).attr('hidden', 'true');
					}
				});
				if (semester_x_pre == 1) {
					$('#id_select_pre option[semester=0]').html('No hay prerequisitos para el primer semestre...');
				}
			});

			var count_pres = 0;
			$('#id_select_pre + a').on('click', function(){
				var course_id  = $('#id_select_pre').val();

				//Que los cursos no sean iguales
				var same_course = false;
				$(prerequisites_side).children('article').each(function(){
					if (course_id == $(this).find('input').val()) {
						same_course = true;
					}
				});

				if (course_id && !same_course) {
					var course_name = $('#id_select_pre option:selected').html();
					var interruptor_fill = false;

					var number_pres = 1;
					$(prerequisites_side).children('article').each(function(index, element){
						number_pres++;
					});

					if (number_pres <= 3) {
						var prerequisite_html = '<article id="prerequisite'+count_pres+'">'+
													'<p>'+course_name+'</p>'+
													'<input type="hidden" name="pre_'+number_pres+'" value="'+course_id+'">'+
													'<a href="##"><span class="glyphicon glyphicon-remove"></span></a>'+
												'</article>';
						$(empty_p).addClass('inactive');
						$(prerequisites_side).append(prerequisite_html);
						actionsPerRequisite(count_pres);

						count_pres++;
					};
				}
			});
		}

		function actionsPerRequisite(prerequisite_id){
			var prerequisites_side = $('#id_select_pre').parent().siblings('.prerequisites');
			var self               = $(prerequisites_side).find('#prerequisite' + prerequisite_id);

			$(self).addClass('active');

			$(self).find('a').on('click', function(){
				$(self).addClass('inactive');
				setTimeout(function() {
					$(self)
						.removeClass('active inactive')	
						.remove();

						$(prerequisites_side).children('article').each(function(index, element){
							var number_pres = index + 1;
							$(element).find('input').attr('name', 'pre_' + number_pres);
						});
				}, 300);
			});
		}

		function actionsPerCourse(id){
			var course_btn_edit = $('#id_course_' + id).find('.btn_edit_course');
			var course_row      = $('#id_course_' + id).find('article.data_course');
			var course_article  = $('#id_course_' + id).find('article.edit_course');

			//Editar Curso
			$(course_btn_edit).on('click', function(){
				var idget = $(this).attr('code');

				if (!$(course_row).hasClass('course_clicked')) {
					$(course_article)
						.html('<img src="/img/spinner.gif" alt="Loading..." />')
						.load('/curricula/curricula/editcourse/id/'+idGet)
						.addClass('active');

					var course_active = $('section.semester').find('article.data_course.course_clicked');
					if (course_active) {
						$(course_active).find('.btn_edit_course').html('Editar');
						$(course_active).removeClass('course_clicked');

						$(course_active).siblings('.edit_course').addClass('inactive');
						setTimeout(function() {
							$(course_active).siblings('.edit_course').removeClass('active inactive');
						}, 300);
					}

					$(course_row).addClass('course_clicked');
					$(course_btn_edit).html('Cancelar');
				} else {
					$(course_row).removeClass('course_clicked');
					$(course_btn_edit).html('Editar');

					$(course_article).addClass('inactive');
					setTimeout(function() {
						$(course_article).removeClass('active inactive');
					}, 300);
				}

				/*if (!$(course_row).hasClass('course_clicked')) {
					$(course_article)
						.html('<img src="/img/spinner.gif" alt="Loading..." />')
						.load('/curricula/curricula/editcourse/id/'+idGet)
						.addClass('active');
					$(course_row).addClass('course_clicked');
					$(course_btn_edit).html('Cancelar');
				}*/

				/*var course_active = $('section.semester').find('article.data_course.course_clicked');
				if (course_active) {
					console.log('joder');
					$(course_active).find('.btn_edit_course').html('Editar');
					$(course_active).removeClass('course_clicked');

					/*$(course_active).addClass('inactive');
					setTimeout(function() {
						$(course_active).removeClass('active inactive');
					}, 300);
				}*/
			});

			//Form por curso
			/*$(course_form_edit).on('submit', function(e){
				e.preventDefault();
				console.log('no se envio');
				$.ajax({
					type     : 'post',
					url      : '/curricula/curricula/saveeditcourse',
					data     : $(this).serialize(),
					//dataType : 'json',
					success  : function(data){
						console.log(data);
					},
					error : function(){

					}
				});
			});*/
		}


		//Funciones Globales
		function jukeboxMsg(msg1, msg2, time){
			//si tiene otro mensaje activo
			if ($(msg2).hasClass('show')) {
				$(msg2).addClass('hide');
				setTimeout(function() {
					$(msg2).removeClass('show hide');
					$(msg1).addClass('show');
				}, 300);
			} else {
				$(msg1).addClass('show');
			}

			clearTimeout(fadeMessageError);
			fadeMessageError = setTimeout(function() {
				$(msg1).addClass('hide');
				setTimeout(function() {
					$(msg1).removeClass('show hide');
				}, 300);
			}, time);
		}

		function fillErrors(msg, errors) {
			$(msg).html('');
			errors.forEach(function(error){
				$(msg).append('<p><span class="glyphicon glyphicon-exclamation-sign"></span> ' + error + '</p>');
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
				}
			}

			if (empty_curriculums) {
				$(curriculums).children('.empty_message').slideDown('fast');

				//solo se escondera si es borrador
				if ($(curriculums).hasClass('div_draft')) {
					setTimeout(function(){
						$(curriculums).parent('.main').slideUp('fast');
						$(curriculums).children('.empty_message').slideUp('fast');
					}, 7000);
				}
			} else {
				$(curriculums).siblings('.empty').slideUp('fast');
				$(curriculums).children('.empty_message').slideUp('fast');
			}
		}

		return {
			chargeSchools     : chargeSchools,
			chargeCurriculums : chargeCurriculums
		};
	}
});