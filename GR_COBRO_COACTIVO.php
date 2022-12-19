<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #predioI-error, #predioF-error {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;

    }
</style>

<script>
    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",
            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            },
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>

<title>Cobro Coactivo</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Generar Oficio Cobro Coactivo</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:generar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <input type="hidden" id="predioI" name="predioI" required title="Ingrese Código Catastral Inicial" />
                            <label for="predioI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Catastral Inicial:</label>
                            <input type="text" name="predioIn" id="predioIn" class="form-control"  title="Ingrese el Código Catastral Inicial"  placeholder="Código Catastral Inicial" required style="display: inline; width: 250px">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <input type="hidden" id="predioF" name="predioF" required title="Ingrese Código Catastral Final" />
                            <label for="predioF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Catastral Final:</label>
                            <input type="text" name="predioFi" id="predioFi" class="form-control"  title="Ingrese el Código Catastral Final"  placeholder="Código Catastral Final" required style="display: inline; width: 250px">
                        </div>
                        <script>
                            $("#predioIn").keyup(function () {
                                $("#predioIn").autocomplete({
                                    source: "consultasBasicas/autoCompletadoPredio.php?action=1",
                                    minlength: 2,
                                    select: function (event, ui) {
                                        var referencia = ui.item;
                                        var ref = referencia.value;
                                        var form_data = {
                                            action: 3,
                                            valor: ref,
                                        }
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/autoCompletadoPredio.php?action=3",
                                            data:form_data,
                                            success: function(response)
                                            { 
                                                if(response !=''){
                                                    $("#predioI").val(response);
                                                }
                                            }
                                        })
                                     }
                                });
                            });
                            $("#predioFi").keyup(function () {
                                $("#predioFi").autocomplete({
                                    source: "consultasBasicas/autoCompletadoPredio.php?action=2",
                                    minlength: 2,
                                    select: function (event, ui) {
                                        var referencia = ui.item;
                                        var ref = referencia.value;
                                        var form_data = {
                                            action: 3,
                                            valor: ref,
                                        }
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/autoCompletadoPredio.php?action=3",
                                            data:form_data,
                                            success: function(response)
                                            { 
                                                if(response !=''){
                                                    $("#predioF").val(response);
                                                }
                                            }
                                        })
                                    }
                                });
                            });
                        </script>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Generar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function generar(){
            jsShowWindowLoad('Generando Informe...');
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "consultasBasicas/autoCompletadoPredio.php?action=4",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                {
                    jsRemoveWindowLoad();
                    if(response>0){
                        window.location='informes/Inf_Word/Inf_Notificacion_Cobro.php?predioI='+$("#predioI").val()+'&predioF='+$("#predioI").val();
                    } else {
                      $("#mensaje").html('No Se Encontraton Predios Con Adeudo');
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            $("#modalMensajes").modal("hide");
                        }) 
                    }
                }
            })
        }
    </script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

