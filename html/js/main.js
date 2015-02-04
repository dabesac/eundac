$(function(){
	$('#main_search_form').on('submit', function(e){
		e.preventDefault();
		var datasearch = $(this).find('input[type=text]').val();
		if (datasearch) {
			location.href = '/default/global/search/data/' + datasearch;
		}
	});

	var global_function = global();
	global_function.tabs($('ul.tabs'), $('ul.tabs').siblings('.tabs_data'));
});