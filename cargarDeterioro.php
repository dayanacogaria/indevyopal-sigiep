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
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0px 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }

        #Carga{
            background-color: #FFF !important;
            position:fixed;
            top:0px;
            left:0px;
            z-index:3200;
            filter:alpha(opacity=80);
            -moz-opacity:80;
            opacity:0.80;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Cargar Archivo Almacén</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/jsonDeterioro.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="flDoc" class="control-label col-sm-3 col-md-3 col-lg-3"><strong class="obligado">*</strong>Archivo:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input type="file" name="flDoc" id="flDoc" class="form-control" placeholder="Archivo" title="Seleccione un archivo de excel para leer" accept=".xls,.xlsx" required="">
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-right">
                                <button type="submit" id="btnGuardar" class="btn btn-primary glyphicon glyphicon-cloud-upload"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
        </div>
    </div>
    <script src="js/jquery-ui.js"></script>
    <script src="js/plugins/datepicker/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-notify.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        $(".select2").select2();

        // $("#btnGuardar").click(function(e){
        //     var excel = document.getElementById("flDoc"); //Capturamos el objeto file
        //     var path_ = excel.value;                      //Obtenemos la ruta del objeto
        //     if(path_.length > 0){                         //Validamos que la ruta no este vacia
        //         var ext_  = /(.xls|.xlsx)$/i;             //Extenciones validas que se pueden subir
        //         //Validamos que las extenciones anteriormente escritas se encuentren dentro de la ruta
        //         //si son diferente el valor del campo sera vacio, se incluye la clase de error de bootstrap.
        //         //De lo contrario se agrega la clase de permitido y se envia el archivo por ajax. Por último
        //         //se coloca un div con un loader mostrando el proceso de cargue de información de lectura del
        //         //archivo.
        //         if(!ext_.exec(path_)){
        //             excel.value = '';
        //             $(excel).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
        //         }else{
        //             $(excel).parents(".col-lg-6").addClass('has-success').removeClass('has-error');

        //             height = 50;

        //             var file  = excel.files[0];
        //             var data = new FormData();
        //             data.append("flDoc",file);

        //             var alto  = 0;
        //             var ancho = 0;
        //             if (window.innerWidth == undefined) ancho = window.screen.width;
        //             else ancho = window.innerWidth;
        //             if (window.innerHeight == undefined) alto = window.screen.height;
        //             else alto = window.innerHeight;
        //             var altof = alto/2;
        //             var html = "";
        //             html  += "<div style=\"text-align:center;height:"+alto+"px;\">";
        //             html  += "<div style='color:#FFFFFF;margin-top:"+altof+"px; font-size:20px;font-weight:bold;color:#1075C1'>";
        //             html  += "<label id=\"mensaje\">Iniciando Lectura</label>";
        //             html  += "</div>";
        //             html  += "<img src='img/loading.gif'/>";
        //             html  += "</div>";
        //             div    = document.createElement("div");
        //             div.id = "Carga";
        //             div.style.width  = ancho+"px";
        //             div.style.height = alto+"px";
        //             $("body").append(div);

        //             $("#Carga").html(html);

        //             input    = document.createElement("input");
        //             input.id = "texto";
        //             $("#texto").focus();
        //             $("#texto").hide()

        //             $.ajax({
        //                 url:"json/jsonCargarAlmacen.php",
        //                 type:"POST",
        //                 data:data,
        //                 processData:false,
        //                 contentType:false,
        //                 success:function(data){
        //                     if(data == 100){
        //                         window.location.reload();
        //                     }
        //                 }
        //             })
        //             .done(function() {
        //                 console.log("success");
        //             })
        //             .fail(function( jqXHR, textStatus, errorThrown ) {
        //                 if (jqXHR.status === 0) {
        //                     alert('Not connect: Verify Network.');
        //                 } else if (jqXHR.status == 404) {
        //                     alert('Requested page not found [404]');
        //                 } else if (jqXHR.status == 500) {
        //                     alert('Internal Server Error [500].');
        //                 } else if (textStatus === 'parsererror') {
        //                     alert('Requested JSON parse failed.');
        //                 } else if (textStatus === 'timeout') {
        //                     alert('Time out error.');
        //                 } else if (textStatus === 'abort') {
        //                     alert('Ajax request aborted.');
        //                 } else {
        //                     alert('Uncaught Error: ' + jqXHR.responseText);
        //                 }
        //             })
        //             .always(function() {
        //                 console.log("complete");
        //             });
        //         }
        //     }else{
        //         excel.value = '';
        //         $(excel).parents(".col-lg-6").addClass("has-error").removeClass('has-success');
        //     }
        // });

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
