<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#29/09/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
?>
<html>
    <head>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #file-error,#banco-error, #tipo_recaudo-error {
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
        <title> Recaudo Efecty </title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Recaudo Efecty</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Recaudo_Efecty.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="tipo_recaudo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Pago:</label>
                                <select name="tipo_recaudo" id="tipo_recaudo" class="select2_single form-control" title="Seleccione Tipo Pago" style="height: auto " required>
                                    <?php 
                                    #Consulta Tipo Recaudo
                                    $row = $con->Listar("SELECT id_unico, 
                                        LOWER(nombre) FROM gp_tipo_pago ");
                                    echo '<option value="">Tipo Pago</option>';
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<option value = "'.$row[$i][0].'">'.ucwords($row[$i][1]).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="banco" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Banco:</label>
                                <select name="banco" id="banco" class="select2_single form-control" title="Seleccione Banco" style="height: auto " required>
                                    <?php 
                                    #Consulta Banco
                                    $row = $con->Listar("SELECT id_unico, numerocuenta,LOWER(descripcion) FROM gf_cuenta_bancaria 
                                        WHERE parametrizacionanno = $anno");
                                    echo '<option value="">Banco</option>';
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<option value = "'.$row[$i][0].'">'. $row[$i][1].' - '.ucwords($row[$i][2]).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px; ">
                                <label for="file" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Seleccione Archivo:</label>
                                <input required id="file" name="file" type="file" style="height: 35px;"  title="Seleccione un archivo">
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" width: 100px; margin-top: -10px; margin-bottom: 10px;">Cargar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>      
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalRequerido" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">

                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un archivo.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnRequerido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/select/select2.full.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function () {
              $(".select2_single").select2({
                  allowClear: true
              });
          });
        </script>
        <script>
            function guardar() {
                var formData = new FormData($("#form")[0]);
                jsShowWindowLoad('Comprobando Datos ...');
                $.ajax({
                    type: 'POST',
                    url: "jsonServicios/gp_facturacionServiciosJson.php?action=5",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        jsRemoveWindowLoad();
                        resultado = JSON.parse(data);
                        var rta = resultado["rta"];
                        var html = resultado["html"];
                        if(rta>0){
                            $("#mensaje").html(html);
                            $("#modalMensaje").modal("show");
                            $("#btnAceptal").click(function(){
                                $("#modalMensaje").modal("hide");
                            });
                        } else {
                            var formData = new FormData($("#form")[0]);
                            jsShowWindowLoad('Guardando Datos ...');
                            $.ajax({
                                type: 'POST',
                                url: "jsonServicios/gp_facturacionServiciosJson.php?action=6",
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    jsRemoveWindowLoad();
                                    console.log(data);
                                    resultado = JSON.parse(data);
                                    var rta = resultado["rta"];
                                    var html = resultado["html"];
                                    if(rta>0){
                                        $("#mensaje").html(rta+' Registros Guardados Correctamente');
                                        $("#modalMensaje").modal("show");
                                        $("#btnAceptal").click(function(){
                                            $("#modalMensaje").modal("hide");
                                        });
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Registrar La Información');
                                        $("#modalMensaje").modal("show");
                                        $("#btnAceptal").click(function(){
                                            $("#modalMensaje").modal("hide");
                                            document.location.reload();
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
            }
        </script>
        <?php require_once 'footer.php'; ?>
    </body>
</html>



