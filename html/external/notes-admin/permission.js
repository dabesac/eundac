function f_edit_permission_one(obj){
    var $td = $(obj).parent();
    var $tr = $td.parent();
    var $index = $tr.attr('index');
    $tr.attr('edit','true');
    switch ($(obj).attr('permission')){
        case 'allow':
            $(obj).attr('permission','deny');
            $(obj).removeClass('glyphicon-ok-circle text-info');
            $(obj).addClass('glyphicon-remove-circle text-danger');
            $action = 0;
            $permission = "deny";
        break;
        case 'deny':
            $(obj).attr('permission','allow');
            $(obj).removeClass('glyphicon-remove-circle text-danger');
            $(obj).addClass('glyphicon-ok-circle text-info');
            $action = 0;
            $permission = "allow";

        break;
        case 'not':
            $(obj).attr('permission','allow');
            $(obj).attr('permission','deny');
            $(obj).removeClass('glyphicon-minus-sign text-success');
            $(obj).addClass('glyphicon-ok-circle text-info');
            $action = 1;
            $permission = "allow";

        break;
    }

    $params={};
    $params[$.base64.encode('mid')]=$.base64.encode($.trim($tr.attr('mid')));
    $params[$.base64.encode('reid')]=$.base64.encode($.trim($tr.attr('reid')));
    $params[$.base64.encode('rid')]=$.base64.encode($.trim($tr.attr('rid')));
    $params[$.base64.encode('action')]=$.base64.encode($.trim($action));
    $params[$.base64.encode('permission')]=$.base64.encode($.trim($permission));

    
    result = "";
    for (var prop in $params ) {
        result += '' + prop + '/' + $params[prop]  + '/'; 
    }
    result = result.substring(0, result.length-1);
    var $url = '/admin/acl/allowresource/' + result;
    $.ajax({
        url:$url,
        success:function($data){
            
        },
        error:function($data){
            //Pintar el error
        }
    });
    $params = {};
    return true;
};

$(document).ready(function() {

});