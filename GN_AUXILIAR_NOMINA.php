<?php 
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

$rowpF = $con->Listar("SELECT p.id_unico, tp.nombre, p.codigointerno FROM gn_periodo p 
LEFT JOIN gn_tipo_proceso_nomina tp ON p.tipoprocesonomina = tp.id_unico 
WHERE p.parametrizacionanno = $anno ORDER BY p.id_unico DESC");

$rowcI = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, c.descripcion
FROM gn_novedad n 
LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
LEFT JOIN gn_periodo p on n.periodo = p.id_unico 
WHERE p.parametrizacionanno = $anno  
ORDER BY cast(c.id_unico as unsigned) ASC");

$rowcF = $con->Listar("SELECT DISTINCT c.id_unico, c.codigo, c.descripcion
FROM gn_novedad n 
LEFT JOIN gn_concepto c ON c.id_unico = n.concepto 
LEFT JOIN gn_periodo p on n.periodo = p.id_unico 
WHERE p.parametrizacionanno = $anno  
ORDER BY cast(c.id_unico as unsigned) DESC");

$rowcc = $con->Listar("SELECT DISTINCT id_unico, nombre  FROM gn_clase_concepto");
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
<title>Auxiliar Nómina</title>
</head>

<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliar Nómina</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/INF_AUXILIAR_NOMINA.php"  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <label for="empleado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Empleado:</label>
                            <select required="required"  name="empleado" id="empleado" class="select2_single form-control" title="Seleccione Empleado">
                                <option value="">Empleado</option>
                                <?php for ($i = 0; $i < count($rowe); $i++) {
                                    echo '<option value="'.$rowe[$i][0].'">'.$rowe[$i][1].' - '.$rowe[$i][2].'</option>';
                                } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="periodoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Periodo Inicial:</label>
                            <select required="required"  name="periodoI" id="periodoI" class="select2_single form-control" title="Seleccione Periodo Inicial">
                                <?php for ($i = 0; $i < count($rowpI); $i++) {
                                    echo '<option value="'.$rowpI[$i][0].'">'.$rowpI[$i][1].' - '.$rowpI[$i][2].'</option>';
                                } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="periodoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Periodo Final:</label>
                            <select required="required"  name="periodoF" id="periodoF" class="select2_single form-control" title="Seleccione Periodo Final">
                                <?php for ($i = 0; $i < count($rowpF); $i++) {
                                    echo '<option value="'.$rowpF[$i][0].'">'.$rowpF[$i][1].' - '.$rowpF[$i][2].'</option>';
                                } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <label for="buscar" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Buscar Conceptos Por:</label>
                            <select required="required"  name="buscar" id="buscar" class="select2_single form-control" title="Seleccione Buscar Conceptos Por:">
                                <option value="">Buscar Conceptos Por</option>                            
                                <option value="1">Todos</option>                            
                                <option value="2">Clase</option>                            
                            </select>
                        </div>
                        <div id="clase" style="display:none;">
                            <div class="form-group" style="margin-top: -10px">
                                <label for="tipoC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Concepto:</label>
                                <select   name="tipoC" id="tipoC" class="select2_single form-control" title="Seleccione Clase Concepto">
                                    <option value="">Clase Concepto</option>
                                    <?php 
                                    for ($i = 0; $i < count($rowcc); $i++) {
                                        echo '<option value="'.$rowcc[$i][0].'">'.$rowcc[$i][1].'</option>';
                                    } ?>  
                                </select>
                            </div>
                        </div>
                        <div id="conceptos" style="display:none">
                            <div class="form-group" style="margin-top: -10px">
                                <label for="conceptoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Inicial:</label>
                                <select required="required"  name="conceptoI" id="conceptoI" class="select2_single form-control" title="Seleccione Concepto Inicial">
                                    <option value="">Concepto Inicial</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="conceptoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Final:</label>
                                <select required="required"  name="conceptoF" id="conceptoF" class="select2_single form-control" title="Seleccione Concepto Final">
                                    <option value="">Concepto Final</option>
                                </select>
                            </div>
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
    $('form').attr('action', 'informes_nomina/INF_AUXILIAR_NOMINA.php?t=1');
}
function reporteExcel(){
    $('form').attr('action', 'informes_nomina/INF_AUXILIAR_NOMINA.php?t=2');
}
</script>
<script>
$("#buscar").change(function(){
    let buscar = $("#buscar").val();
    let optionI = '<option value="">Concepto Inicial</option>';
    $("#conceptoI").html(optionI);
    let optionF = '<option value="">Concepto Final</option>';
    $("#conceptoF").html(optionF);
    if(buscar==2){
        $("#clase").css('display', 'block');
        $("#conceptos").css('display', 'none');
        $("#tipoC").prop("required",true);
        
    } else {
        $("#clase").css('display', 'none');
        $("#tipoC").prop("required", false);
        cargarconceptos(1,0);
        $("#conceptos").css('display', 'block');
        
    }
    
})
$("#tipoC").change(function(){
    let tipoC = $("#tipoC").val();
    cargarconceptos(2,tipoC);
    $("#conceptos").css('display', 'block');
   
});
function cargarconceptos(tipo, clase){
    console.log(tipo,clase);
    if(tipo==1){
        let form_data = {action: 1, orden:'ASC'};
        $.ajax({
            type: "POST",
            url: "jsonNomina/gn_consultasJson.php",
            data: form_data,
            success: function (response)
            {
                let optionI = '<option value="">Concepto Inicial</option>';
                $("#conceptoI").html(optionI+response);
            }
        });
        let form_data2 = {action: 1, orden:'DESC'};
        $.ajax({
            type: "POST",
            url: "jsonNomina/gn_consultasJson.php",
            data: form_data2,
            success: function (response)
            {
                let optionF = '<option value="">Concepto Final</option>';
                $("#conceptoF").html(optionF+response);
            }
        });
    } else {
        let form_data3 = {action: 1, orden:'ASC', clase:clase};
        $.ajax({
            type: "POST",
            url: "jsonNomina/gn_consultasJson.php",
            data: form_data3,
            success: function (response)
            {
                let optionI = '<option value="">Concepto Inicial</option>';
                $("#conceptoI").html(optionI+response);
            }
        });
        let form_data4 = {action: 1, orden:'DESC',clase:clase};
        $.ajax({
            type: "POST",
            url: "jsonNomina/gn_consultasJson.php",
            data: form_data4,
            success: function (response)
            {
                let optionF = '<option value="">Concepto Final</option>';
                $("#conceptoF").html(optionF+response);
            }
        });
    }
}
</script>
</body>
</html>
<?php require_once 'footer.php'?>  