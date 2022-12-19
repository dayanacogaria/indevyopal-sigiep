<?php 
#######################################################################################################
#       **********************      Modificaciones      **********************
#######################################################################################################
#16/06/2018 |Erica G.|Archivo Creado 
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$comp   = $_SESSION['compania'];
#*** Consultas Tipo Comprobante Dependiendo ***#
$titulo = "";
$row    = "";
$tipo   = "";
if(!empty($_GET['t'])){
    $tipo =$_GET['t'];
    switch ($_GET['t']){
        # ** Disponibilidades ** #
        case 1:
            $titulo = "Disponibilidad Presupuestal";
            $row    = $con->Listar("SELECT
                    id_unico,UPPER(codigo),LOWER(nombre)
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 14 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $comp 
                ORDER BY codigo"); 
        break;
        # ** Registros ** #  
        case 2:
            $titulo = "Registro Presupuestal";
            $row    = $con->Listar("SELECT
                    id_unico,UPPER(codigo),LOWER(nombre)
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 15 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $comp 
                ORDER BY codigo"); 
        break;
        # ** Cuentas X Pagar ** #
        case 3:
            $titulo = "Cuenta Por Pagar";
            $row    = $con->Listar("SELECT
                    id_unico,UPPER(codigo),LOWER(nombre)
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 16 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $comp 
                ORDER BY codigo"); 
        break;
        # ** Egresos ** #
        case 4:
            $titulo = "Egreso";
            $row    = $con->Listar("SELECT
                    id_unico,UPPER(codigo),LOWER(nombre)
                FROM
                    gf_tipo_comprobante_pptal
                WHERE
                    clasepptal = 17 AND tipooperacion = 1 
                    AND vigencia_actual = 1 AND compania = $comp 
                ORDER BY codigo"); 
        break;
    }
}


?>
<title>Listar Formatos</title>
<head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #tipoComprobante-error, #fechaI-error, #fechaF-error{
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
<script>
    $(function () {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaI").datepicker({changeMonth: true, }).val();
        $("#fechaF").datepicker({changeMonth: true}).val();
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
                <h2 align="center" class="tituloform">Imprimir Formatos De <?php echo $titulo?></h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informesPptal/INF_LISTADO_FORMATOS.php" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo;?>">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipoComprobante" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Tipo Comprobante :</label>
                            <select name="tipoComprobante" id="tipoComprobante" class="select2_single form-control" title="Seleccione Tipo Comprobante" style="height: auto " required>
                                <option value="">Tipo Comprobante </option>
                                <?php 
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.$row[$i][1].' - '.ucwords($row[$i][2]).'</option>';
                                }
                                ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -0px;">
                            <label for="fechaI" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input class="form-control" type="text" name="fechaI" id="fechaI" placeholder="Fecha Inicial" required="required" title="Seleccione Fecha Inicial" readonly="true">
                        </div>
                        <div class="form-group" style="margin-top: -0px;">
                            <label for="fechaF"  class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input class="form-control" type="text" name="fechaF" id="fechaF" placeholder="Fecha Final" required="required" title="Seleccione Fecha Final" readonly="true">
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button type="submit" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Imprimir</button>              
                        </div>
                        <br/>
                        <br/>
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
        $("#fechaI").change(function(){
            
           var lockDate = new Date($("#fechaI").datepicker('getDate'));
           $('#fechaF').datepicker('option','minDate',lockDate);
        })
        
    </script>
    <?php require_once 'footer.php' ?>  
</body>
</html>