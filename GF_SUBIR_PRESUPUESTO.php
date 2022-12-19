<?php 
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
?>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #file-error {
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
      });

      $(".cancel").click(function() {
        validator.resetForm();
      });
    });
    </script>
    <title>Subir Presupuesto Inicial</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Subir Presupuesto Inicial</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Archivo .xsl - .xlsx <a href="documentos/formatos/Formato_Presupuesto.xlsx" target="_blank"><i class="fa fa-file-excel-o"></i></a></p>
                        <div class="form-group" style="margin-top: -10px; ">
                            <input type="hidden" id="action" name="action" value="1">
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
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
 <script src="js/select/select2.full.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>  
    <script>
        function guardar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Comprobando..');
            $("#action").val(2);
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_subirMovPptalJson.php",
                data:formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    //Si Ya Hay Comprobante Apropiación Y movimientos
                    jsRemoveWindowLoad();
                    if(data==2){
                        $("#mensaje").html('No se Puede Realizar Cargue de Archivo. <br/>Ya Existen Movimientos.');
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                        $("#btnCancelar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                    } else {
                        if(data==1){
                            $("#mensaje").html('Ya existe Presupuesto Inicial. \n\
                            <br/><strong>¿Desea Subir de Nuevo el Presupuesto?</strong>');
                            $("#mdlMensajes").modal("show");
                            $("#btnAceptar").click(function(){
                                subira();
                            });
                            $("#btnCancelar").click(function(){
                                $("#mdlMensajes").modal("hide");
                            });
                        } else {
                            if(data==3){
                                $("#mensaje").html('No se Puede Realizar Cargue de Archivo. <br/>No existe tipo de comprobante presupuestal para realizar apropiación inicial.');
                                $("#mdlMensajes").modal("show");
                                $("#btnAceptar").click(function(){
                                    $("#mdlMensajes").modal("hide");
                                });
                                $("#btnCancelar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                            } else {
                                subira();
                            }
                        }
                    }
                }
            }); 
        }
        function subira(){
            //Subir
            jsShowWindowLoad('Guardando..');
            $("#action").val(4);
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_subirMovPptalJson.php",
                data:formData,
                contentType: false,
                processData: false,
                success: function (data) { 
                    console.log(data);
                    jsRemoveWindowLoad();
                    var resultado = JSON.parse(data);
                    var cfg = resultado["cfg"];
                    var apg = resultado["apg"];
                    if(apg>0){
                        $("#mensaje").html('Información Guardada Correctamente.<br/>'+apg+' Apropiaciones Registradas');
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            document.location='registrar_GF_APROPIACION_INICIAL.php';
                        });
                        $("#btnCancelar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                    } else {
                        $("#mensaje").html('No se ha podido guardar información');
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            document.location='registrar_GF_APROPIACION_INICIAL.php';
                        });
                        $("#btnCancelar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        });
                    }
                    
                }
            })
        }
    </script>
    <?php require_once 'footer.php';?>
</body>
</html>

