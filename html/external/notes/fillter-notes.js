$(document).ready(function() {
	$("#anho").change(function(){
		$("#anho option:selected").each(function(){
			var anio = $(this).val();
				$.get("/admin/correctnotes/listperiod",{anio:anio},function(data){
				$("#perid").html(data);	
			});
		});
	});

	$("#search-courses").click(function(){
		var $perid = $("#perid").val(), 
			$escid=$("#escid").val();
		var paramets ={
			perid:$perid,
			escid:$escid,
			};
		if ($perid != '') {
			url = $(this).attr('href')
			$.get(url,paramets,function($data){
				$("#result-search").html($data);
				$("#search-period").removeClass("has-error");

			});
		}else{
			$("#search-period").addClass("has-error");
		}
	});
})