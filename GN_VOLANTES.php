<?php
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
require_once ('./Conexion/ConexionPDO.php');
@session_start();
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$rowe = $con->Listar("SELECT                      
    e.id_unico,
    CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos), 
    t.numeroidentificacion 
    FROM gn_empleado e
    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE t.compania = $compania");
$rowt = $con->Listar("SELECT id_unico, nombre FROM gn_tipo_proceso_nomina WHERE id_unico in (2,7,12,8)");
?>

<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltEmpleado-error, #sltPeriodo-error, #sltTipo-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
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
<script src="js/jquery-ui.js"></script>
<title>Generación Volantes</title> 
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Generación de Volantes de Pago</h2>
                <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/INF_VOLANTES.php" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="">
                            <label for="sltTipo" class="control-label col-sm-1" style="margin-top: 15px"><strong style="color:#03C1FB;">*</strong>Volante:</label>
                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo Volante" style="width: 180px;height: 30px; margin-top: 15px; margin-left: 15px;" class="select2_single form-control col-sm-1" required>
                                <option value="">Tipo De Volante</option>
                                <?php 
                                for ($t=0; $t <count($rowt) ; $t++) { 
                                    echo '<option value="'.$rowt[$t][0].'">'.ucwords(mb_strtolower($rowt[$t][1])).'</option>';
                                }
                                ?> 
                            </select>
                            <label for="sltEmpleado" class="control-label col-sm-1" style="margin-top: 15px"><strong style="color:#03C1FB;">*</strong>Empleado:</label>
                                <select name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 180px;height: 30px; margin-top: 15px; margin-left: 15px;" class="select2_single form-control col-sm-1" required>
                                    <?php 
                                    for ($i=0; $i <count($rowe) ; $i++) { 
                                        echo '<option value="'.$rowe[$i][0].'">'.ucwords(mb_strtolower($rowe[$i][1])).' - '.$rowe[$i][2].'</option>';
                                    }
                                    ?>                                                          
                            </select>                            
                            <label for="sltPeriodo" class="control-label col-sm-1" style="margin-top: 15px; margin-left: 60px;"><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                            <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 180px;height: 30px; margin-top: 15px; margin-left: 15px;" class="select2_single form-control col-sm-1" required>
                                <option value="">Periodo</option>
                            </select>
                            <button type="submit" class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;">Generar </button>
                        </div>
                    </form>
                </div>
            </div>                                    
        </div>
    <div>
    <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#sltEmpleado").select2();
        $("#sltPeriodo").select2();
        $("#sltTipo").select2();

        $("#sltTipo").change(function(){
            let tipo = $("#sltTipo").val();
            if(tipo==''){}else{
                var form_data={action: 4, tipo :tipo};
               var optionCF ="<option value=''>Periodo</option>";
               $.ajax({
                  type:'POST', 
                  url:'jsonNomina/gn_consultasJson.php',
                  data: form_data,
                  success: function(response){
                      optionCF =optionCF+response;
                      $("#sltPeriodo").html(optionCF).focus();     

                  }
               });
            }
        })
    </script>
</body>
</html>