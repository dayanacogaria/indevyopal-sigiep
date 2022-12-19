<?php 
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#03/05/2018 | Erica G. | Formato 1001 Acumulado o Detallado
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania']; 
?>
<html>
    <head>
        <title>Informes Exógenas</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #formato-error, #sltExportar-error, #separador-error, #informe-error  {
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
                    <h2 align="center" class="tituloform">Informes Exógenas</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/INF_EXOGENAS.php" target=”_blank”>  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="anno" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Año</label>
                                <select required="required" name="anno" id="anno" class="form-control select2_single" title="Seleccione Año">
                                    <option value="">Año</option>              
                                    <?php 
                                    $row = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE compania =$compania ORDER BY anno DESC");
                                    for ($i = 0; $i < count($row); $i++) {
                                        echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
                                    }
                                    ?>
                                </select>
                            </div> 
                            <div class="form-group">
                                <label for="formato" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Formato</label>
                                <select required="required" name="formato" id="formato" class="form-control select2_single" title="Seleccione Formato">
                                    <option value="">Formato</option>              
                                </select>
                            </div> 
                            <div class="form-group" id="det" style="display:none">
                                <label for="informe" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Informe</label>
                                <select name="informe" id="informe" class="form-control select2_single" title="Seleccione Informe">
                                    <option value="">Informe</option>              
                                    <option value="1">Acumulado</option>
                                    <option value="2">Detallado</option>
                                </select>
                            </div>
                            <div class="form-group" id="exp">
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
        $("#formato").change(function(){
            var formato = $("#formato").val();
            if(formato != ""){
                var form_data={action: 11, id :formato};
                $.ajax({
                   type:'POST', 
                   url:'jsonPptal/gf_exogenasJson.php',
                   data: form_data,
                   success: function(response){
                        console.log(response == 1001);
                        if(response == 1001){
                            $("#det").css("display", "block");
                            $("#informe").prop("required", true);
                        } else {
                            $("#det").css("display", "none");
                            $("#informe").prop("required", false);

                        }     
                   }
                });
            }
        })
        $("#informe").change(function(){
            var informe = $("#informe").val();
            var formato = $("#formato").val();
            if(formato != ""){
                var form_data={action: 11, id :formato};
                $.ajax({
                   type:'POST', 
                   url:'jsonPptal/gf_exogenasJson.php',
                   data: form_data,
                   success: function(response){
                        console.log(response == 1001);
                        if(response == 1001){
                            if(informe==2){
                                $("#sltExportar").val("3");
                                $("#exp").css("display", "none");
                                $("#sltExportar").prop("required", false);
                            } else {
                                $("#sltExportar").val("");
                                $("#exp").css("display", "block");
                                $("#sltExportar").prop("required", true);
                            }
                        } else {
                            $("#exp").css("display", "block");
                            $("#sltExportar").prop("required", true);
                        }     
                   }
                });
            }
        })
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
    <script>
        $("#anno").change(function(){
            var anno = $("#anno").val();
            var form_data={action: 10, anno :anno};
            var optionMI ="<option value=''>Formato</option>";
            $.ajax({
               type:'POST', 
               url:'jsonPptal/gf_exogenasJson.php',
               data: form_data,
               success: function(response){
                   //console.log(response);
                   optionMI =optionMI+response;
                   //console.log(optionMI);
                   $("#formato").html(optionMI).focus();              
               }
            });
        })
    </script>
    
    </body>
</html>
