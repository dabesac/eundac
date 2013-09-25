$(document).ready(function() {
    
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();
        
        

    });

	/**********limit-just-numbers**********/
	var $strnotes = '';
    var $edition_notes = false;
	$(".tb-notes input[type='text']").keypress(function(e){

		var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)){
                e.preventDefault();
            }else{
                if(e.which != 8){
                    var chr = String.fromCharCode( e.which );
                    $strnotes = '' + $strnotes + chr + '';
                    if($strnotes != ''){$nota = Number($strnotes);}
                    else{$nota='';}
                    if(($nota < 0 || $nota >20) && ($nota!=-3) && ($nota!='')){
                        $strnotes = '';
                        $(this).val('');
                        e.preventDefault();
                    }
                }
                
                var td = $(this).parent();
                var tr  = td.parent();
                $tr = $(tr);
                $tr.attr("edition",true); 
                
                $index = $(this).attr("index");
                $("#edit-note-"+ $index).addClass("glyphicon-edit");
                $("#save_notes").attr("disabled",false);
                objeto = {
                    "width":'50px',
                }
                $(".td-edit-note").css(objeto);
                if($edition_notes == false){
                    InitializeTimer();
                    $edition_notes = true;
                }
                
            }

	});
	
    /****************************--save in notes in 1.5 minutes ********************************/


});



