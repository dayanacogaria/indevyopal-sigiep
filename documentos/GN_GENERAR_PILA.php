<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#07/03/2019 | Erica G. | Archivo Creado
#04/04/2019 | Karen B. | Archivo modificado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];


?>
<title>Generar Pila</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #nombre-error, #nombre-error, #codigo-error {
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
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Planilla Pila</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/INF_PILA_NOM.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <!--<input type="hidden" name="sltInforme" id="sltInforme" value="1">-->
                             <?php
                                $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico WHERE p.id_unico!=1 AND p.parametrizacionanno =$anno";

                                $periodo = $mysqli->query($per);
                            ?>
                            <label for="periodo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                            <select name="periodo" id="periodo" class="select2_single form-control" title="Seleccione Periodo" required="required">
                                <option value="">Periodo</option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($periodo))
                                    {
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                ?> 
                            </select> 
                        </div>
                        <div class="form-group">
                                <label for="sltInforme" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Tipo Informe</label>
                                <select required="required" name="sltInforme" id="sltInforme" class="form-control select2_single" title="Seleccione Tipo" >
                                    <option value="">Tipo Informe</option>              
                                    <option value="1">Empleados</option>
                                    <option value="2">Pensionados</option>
                                    
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
                                    <option value="1">Sin Separador</option>
                                    <option value=",">,</option>
                                    <option value=";">;</option>
                                    <option value="tab">Tab</option>
                                </select>
                            </div> 
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Generar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <a href="documentos/pilafebrero.txt" id="path_file" name="path_file" download="" class="text-left">Descargar el archivo</a>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
   
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });  
    });
    </script>
    <script>
        function generar(){
            $("#modalEliminar").modal('show');
        }
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



