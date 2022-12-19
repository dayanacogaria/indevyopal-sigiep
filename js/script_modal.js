function eliminar(ruta){
    var result = '';
    $("#mdlConfirmarDel").modal("show");
    $("#btnDel").click(function(){
        $("#mdlConfirmarDel").modal("hide");
        $.get(ruta, function(data){
            result = JSON.parse(data);
            if(result == true){
                $("#mdlAceptarDel").modal("show");
            }else{
                $("#mdlNoConf").modal("show");
            }
        });
    });
}

$("#btnAcepts").click(function(){
    location.reload();
});