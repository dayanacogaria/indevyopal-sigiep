<?php 
header("Content-Type: text/html;charset=utf-8");
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$compania = $_SESSION['compania'];
$anno     = $_SESSION['anno'];
$con      = new ConexionPDO();
$rowe = $con->Listar("SELECT DISTINCT e.id_unico, IF(CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) = '',
        (tr.razonsocial),
        CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
    FROM gn_novedad n 
    LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero tr ON tr.id_unico = e.tercero 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
    WHERE p.parametrizacionanno = $anno  
    ORDER BY e.id_unico ASC");

$rowpI = $con->Listar("SELECT p.id_unico, tp.nombre, p.codigointerno FROM gn_periodo p 
LEFT JOIN gn_tipo_proceso_nomina tp ON p.tipoprocesonomina = tp.id_unico 
WHERE p.parametrizacionanno = $anno ORDER BY p.id_unico ASC");


$rowcI = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, c.descripcion
FROM gn_novedad n 
LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
LEFT JOIN gn_periodo p on n.periodo = p.id_unico 
WHERE p.parametrizacionanno = $anno  
ORDER BY cast(c.id_unico as unsigned) ASC"); 
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #empleado-error, #periodoI-error, #periodoF-error, #conceptoI-error, #conceptoF-error , #buscar-error, #tipoC-error{
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
<title>Informe por Concepto Detallado</title>
</head>

<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Informe por Concepto Detallado</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/INF_AUXILIAR_NOMINA.php"  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        
                        <div class="form-group" style="margin-top: -10px">
                            <label for="periodoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                            <select required="required"  name="periodoI" id="periodoI" class="select2_single form-control" title="Seleccione Periodo">
                                <?php for ($i = 0; $i < count($rowpI); $i++) {
                                    echo '<option value="'.$rowpI[$i][0].'">'.$rowpI[$i][1].' - '.$rowpI[$i][2].'</option>';
                                } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="retencion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Informe de Retención:</label>
                            <input type="radio" name="retencion" id="retencion" value="1" onclick="javaScript:cambiar(1)">Sí
                            <input type="radio" name="retencion" id="retencion" value="2" checked="checked" onclick="javaScript:cambiar(2)">No
                        </div> 

                        <div id="conceptos" >
                            <div class="form-group" style="margin-top: -10px">
                                <label for="conceptoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                                <select required="required"  name="conceptoI" id="conceptoI" class="select2_single form-control" title="Seleccione Concepto">
                                    <option value="">Concepto</option>
                                    <?php for ($i = 0; $i < count($rowcI); $i++) {
	                                    echo '<option value="'.$rowcI[$i][0].'">'.$rowcI[$i][1].' - '.utf8_encode($rowcI[$i][2]).'</option>';
	                                } ?> 
                                </select>
                            </div>
                        </div>  
                         
                         
                        <div class="form-group" style="margin-top: -10px" id="divacumulado">
                            <label for="acumulado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Acumulado por Banco:</label>
                            <input type="radio" name="acumulado" id="acumulado" value="1">Sí
                            <input type="radio" name="acumulado" id="acumulado" value="2" checked="checked">No
                        </div>                        
                        <div class="form-group text-center" style="margin-top:20px;">
                            <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                            <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                                <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes_nomina/INF_CONCEPTO_DETALLADO.php?t=1');
}
function reporteExcel(){
    $('form').attr('action', 'informes_nomina/INF_CONCEPTO_DETALLADO.php?t=2');
}
function cambiar(tipo){
    if(tipo==1){
        $("#divacumulado").css('display','none');
        $("#conceptos").css('display','none');
        $("#conceptoI").attr('required', 'false');
        $("#conceptoI").removeClass("required");
        $('#conceptoI').removeAttr("required");
        $('#conceptoI').prop("required", false);
    } else {
        $("#divacumulado").css('display','block');
        $("#conceptos").css('display','block');
        $("#conceptoI").attr('required', 'true');
        $('#conceptoI').prop("required", true);
    }
}
</script>

</body>
</html>
<?php require_once 'footer.php'?>  