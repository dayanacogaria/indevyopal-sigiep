<?php
require ('head.php');
require ('Conexion/conexion.php');
?>
    <title>Cargar Información</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <style>

            label #archivo-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
            }
            
        </style>
        <script>
        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                },
                rules: {
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
        </script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">    
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Almacen.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                        <div class="form-group" style="margin-top: -10px; ">
                            <input type="hidden" id="action" name="action" value="3">
                            <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                            <input required id="flDoc" name="flDoc" type="file" style="height: 35px;"  title="Seleccione un archivo">
                        </div>
                        <div class="form-group" style="margin-top: 10px; width: 74%;">
                           <label for="no" class="col-sm-5 control-label"></label>
                           <button type="submit" id="btnGuardar" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px; ">Cargar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
        </div>
    </div>
    <div class="modal fade" id="mdlresponse" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" id="forma-modal">
                    <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span></button>
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="height: 400px; overflow-y: auto;">
                        <div id="htmlprogress">
                            <ul class="tabs"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="forma-modal">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
    </div>     
    <script src="js/jquery-ui.js"></script>
    <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-notify.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        
        $("#btnGuardar").click( function( evt){            
            evt.preventDefault();
            let file = $("#flDoc").val();
            let num = file.length;
            let ext  = file.substring(num-4,num);
            let dependencias = 0;
            let depresponsable = 0;
            if (num > 0 && ext === "xlsx"){
                /***** Validar TERCERO *****/                
                jsShowWindowLoad('Validando Terceros...');
                $("#action").val(1);
                var formData = new FormData($("#form")[0]);
                $.ajax({
                    type: 'POST',
                    url: "./json/jsonCargarAlmacen.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+'G');
                        var resultado = JSON.parse(response);
                        var rta = resultado["rta"];
                        var data = resultado["html"];                                                
                        if (rta != 0) {                     
                            $('#htmlprogress ul li').remove();
                            $("#mdlresponse").modal("show");                            
                            $("#htmlprogress ul").append('<li><h5><strong>' + rta + '</strong></h5></li>');
                            $(data).each(function (i, v) {
                                $("#htmlprogress ul").append('<li><span>' + v + '</span></li>');
                            });
                            
                        }else {                            
                            /***** Validar ELEMENTO *****/                            
                            jsShowWindowLoad('Validando Elementos...');
                            $("#action").val(2);
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                               type: 'POST',
                                url: "./json/jsonCargarAlmacen.php",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response+'G');
                                    var resultado = JSON.parse(response);
                                    var rta = resultado["rta"];
                                    var data = resultado["html"];                        
                                    if (rta != 0) {                       
                                        $('#htmlprogress ul li').remove();
                                        $("#mdlresponse").modal("show");                                        
                                        $("#htmlprogress ul").append('<li><h5><strong>' + rta + '</strong></h5></li>');
                                        $(data).each(function (i, v) {
                                            $("#htmlprogress ul").append('<li><span>' + v + '</span></li>');
                                        });                                        
                                    }else{
                                        /***** Registrar DEPENDENCIA*****/        
                                        jsShowWindowLoad('Registrando Dependencias...');
                                        $("#action").val(3);
                                        var formData = new FormData($("#form")[0]);
                                        $.ajax({
                                            type: 'POST',
                                            url: "./json/jsonCargarAlmacen.php",
                                            data:formData,
                                            contentType: false,
                                            processData: false,
                                            success: function(response)
                                            {
                                                jsRemoveWindowLoad();
                                                console.log(response+'G');
                                                var resultado = JSON.parse(response);
                                                var rta = resultado["rta"];
                                                var data = resultado["html"];
                                                dependencias = resultado["dependencias"];
                                                if (rta != 0) {
                                                    $('#htmlprogress ul li').remove();
                                                    $("#mdlresponse").modal("show");
                                                    $("#htmlprogress ul").append('<li><h5><strong>' + rta + '</strong></h5></li>');
                                                    $(data).each(function (i, v) {
                                                        $("#htmlprogress ul").append('<li><span>' + v + '</span></li>');
                                                    });
                                                }else{
                                                    /***** Registrar DEPENDENCIA_RESPONSABLE*****/
                                                    jsShowWindowLoad('Registrando Dependencia_Responsable...');
                                                    $("#action").val(4);
                                                    var formData = new FormData($("#form")[0]);
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: "./json/jsonCargarAlmacen.php",
                                                        data:formData,
                                                        contentType: false,
                                                        processData: false,
                                                        success: function(response)
                                                        {
                                                            jsRemoveWindowLoad();
                                                            console.log(response+'DR');
                                                            var resultado = JSON.parse(response);
                                                            var rta = resultado["rta"];
                                                            var data = resultado["html"];
                                                            depresponsable = resultado["dep_res_insert"];                                                            
                                                            if (rta != 0) {
                                                                $('#htmlprogress ul li').remove();
                                                                $("#mdlresponse").modal("show");
                                                                $("#htmlprogress ul").append('<li><h5><strong>' + rta + '</strong></h5></li>');
                                                                $(data).each(function (i, v) {
                                                                    $("#htmlprogress ul").append('<li><span>' + v + '</span></li>');
                                                                });
                                                            }else{
                                                                /***** Registrar MOVIMIENTOS*****/
                                                                jsShowWindowLoad('Registrando Movimientos...');
                                                                $("#action").val(5);
                                                                var formData = new FormData($("#form")[0]);
                                                                $.ajax({
                                                                    type: 'POST',
                                                                    url: "./json/jsonCargarAlmacen.php",
                                                                    data:formData,
                                                                    contentType: false,
                                                                    processData: false,
                                                                    success: function(response)
                                                                    {
                                                                        jsRemoveWindowLoad();
                                                                        console.log(response+'Movimientos');
                                                                        var resultado = JSON.parse(response);
                                                                        var rta = resultado["rta"];
                                                                        var data = resultado["html"];
                                                                        var entradas = resultado["entradas"];
                                                                        var salidas = resultado["salidas"];
                                                                        var inserto = resultado["inserto"];
                                                                        $('#htmlprogress ul li').remove();
                                                                        $("#mdlresponse").modal("show");
                                                                        $("#htmlprogress ul").append('<li><h5><strong>' + inserto + '</strong></h5></li>');
                                                                        $("#htmlprogress ul").append('<li><span>Dependencias: ' + dependencias + '</span></li>');
                                                                        $("#htmlprogress ul").append('<li><span>D_Responsable: ' + depresponsable + '</span></li>');
                                                                        $("#htmlprogress ul").append('<li><span>Entradas: ' + entradas + '</span></li>');
                                                                        $("#htmlprogress ul").append('<li><span>Salidas: ' + salidas + '</span></li>');
                                                                        $(data).each(function (i, v) {
                                                                            if (v != 0){
                                                                                $("#htmlprogress ul").append('<li><span>' + v + '</span></li>');
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                                    
                            });
                        }
                    }
                });
            }  
        });
        
        $(".select2").select2();

        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione tipo de predio",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-6").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-6").addClass("has-error").removeClass('has-success');
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    $(element).parents(".col-lg-6").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-6").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-6").addClass('has-success').removeClass('has-error');
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
    </script>
</body>
</html>
