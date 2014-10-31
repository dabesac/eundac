$(function(){
	var dataGet = '';

	$('#selectFaculty').on('change', function(){
		dataGet = $(this).val();
		if (dataGet) {
			$('#selectSchool')
				.html('<option value="">Cargando...</option>')
				.load('/default/global/listschools/id/' + dataGet);
		}else{
			$('#selectSchool').html('<option value="">Escuela</option><option value="">Primero seleccione alguna facultad ¬¬</option>');
		};
	});

	$('#selectSchool').on('change', function(){
		dataGet = $(this).val();
		console.log(dataGet);
		$('#dataCurriculums').load('/curricula/curricula/listcurriculums/id/' + dataGet);
		
	});
});