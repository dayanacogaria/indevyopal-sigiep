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
$rowt = $con->Listar("SELECT id_unico, nombre FROM gn_tipo_proceso_nomina WHERE id_unico in (7)");
?>

<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltTipo-error, #sltPeriodo-error, #sltTipo-error {
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
<title>Informe De Empleados</title> 
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Informe De Empleados</h2>
                <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltTipo" class="control-label col-sm-5" style="margin-top: 0px; "><strong class="obligado">*</strong>Tipo De Informe:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select  name="sltTipo" id="sltTipo" title="Seleccione Tipo De Informe" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="1">Empleados Activos</option>
                                    <option value="2">Todos Los Empleados</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="chkTipoA" class="control-label col-sm-5 col-md-5 col-lg-5">Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="" class="label-inradio"><input name="chkTipoA" type="radio" id="optPdf" checked="checked">PDF</label>
                                <label for="" class="label-inradio"><input name="chkTipoA" type="radio" id="optExcel" >EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button id="enviar"  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Generar</i></button>
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
        $("#sltTipo").select2();

         $("#enviar").click(function(){
            if($("#optPdf").is(':checked')){
                $("#form").attr("action", "informes_nomina/INF_EMPLEADOS.php?t=1");
            } else {
                if($("#optExcel").is(":checked")){
                    $("#form").attr("action", "informes_nomina/INF_EMPLEADOS.php?t=2");
                }
            }
        });
    </script>
</body>
</html>