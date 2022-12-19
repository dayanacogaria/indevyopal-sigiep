<?php 
##########################################################################################
#               *** Modificaciones ** 
##########################################################################################
#30/01/2018 | Erica G. | Archivo Creado
##########################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$annio      = $_SESSION['anno'];
$compania   = $_SESSION['compania']; 
?>
<html>
    <head>
        <title>Informes SIA</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!--######VALIDACIONES#####-->
        <style>
            label #sltInforme-error, #sltExportar-error, #separador-error  {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
        }
        body{
            font-size: 12px;
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
            }
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>
        <style>
            .form-control {font-size: 12px;}
        </style>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                    <h2 align="center" class="tituloform">Informes SIA</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/INF_SIA.php" target=”_blank”>  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="sltInforme" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Informe</label>
                                <select required="required" name="sltInforme" id="sltInforme" class="form-control select2_single" title="Seleccione Tipo Informe">
                                    <option value="">Informe</option>              
                                    <option value="1">Movimiento De Bancos</option>
                                    <option value="2">Relación De Pagos</option>
                                    <option value="3">Informe De Almacén</option>
                                </select>
                            </div> 
                            <div class="form-group">
                                <label for="sltExportar" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Exportar A</label>
                                <select required="required" name="sltExportar" id="sltExportar" class="form-control select2_single" title="Seleccione Exportar" >
                                    <option value="">Exportar</option>              
                                    <option value="1">csv</option>
                                    <option value="2">txt</option>
                                    <option value="3">xls</option>
                                </select>
                            </div> 
                            <div class="form-group" id="sep" style="display:none">
                                <label for="separador" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Separado Por</label>
                                <select name="separador" id="separador" class="form-control select2_single" title="Seleccione Separador">
                                    <option value="">Separador</option>              
                                    <option value=",">,</option>
                                    <option value=";">;</option>
                                    <option value="tab">Tab</option>
                                </select>
                            </div> 
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: 0px; margin-bottom: 10px; margin-left: -100px;" >Generar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    <?php require_once 'footer.php' ?>  
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
    </script>
    <script>
        $("#sltExportar").change(function(){
            var tipo = $("#sltExportar").val();
            if(tipo == 3){
                $("#sep").css("display", "none");
                $("#separador").prop("required", false);
            } else {
                $("#sep").css("display", "block");
                $("#separador").prop("required", true);
            }
        })
    </script>
    
    </body>
</html>