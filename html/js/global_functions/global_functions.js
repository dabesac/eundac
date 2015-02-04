function global(){
	function tabs(ul, tabs){
		var id_tab;
		$(ul).find('a').on('click', function(){
			id_tab = $(this).attr('going-to');
			$(ul).find('a.active').removeClass('active');
			$(this).addClass('active');
			
			$(tabs).find('.tab_data.active').removeClass('active');
			$('#tab_'+id_tab).addClass('active');
		});
	}
	return {
		tabs : tabs
	}
}