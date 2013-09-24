$(document).ready(function() {

	/**********limit-just-numbers**********/
	var $strnota = '';
	$(".tb-notes input[type='text']").keypress(function(e){

		var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
                if(e.which != 8){
                    var chr = String.fromCharCode( e.which );
                    $strnota = '' + $strnota + chr + '';
                    if($strnota != ''){$nota = Number($strnota);}
                    else{$nota='';}
                    if(($nota < 0 || $nota >20) && ($nota!=-3) && ($nota!='')){
                        $strnota = '';
                        $(this).val('');
                        e.preventDefault();
                    }
                }
                
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);
                $tr.attr("edicion",true); 
                
                $indice = $(this).attr("indice");
                $("#comprobacion_" + $indice).attr("src","/img/cuaderno.png");
                $("#btnguardar").attr("disabled",false);
                
                if($grillaedicion == false){
                    InitializeTimer();
                    $grillaedicion = true;
                }
                
            }

	});

	/********************************************/
});



