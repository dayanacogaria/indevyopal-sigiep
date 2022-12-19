<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 

$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
 ?>
<title>Seguimiento Ingresos Presupuesto Contabilidad</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #tipoI-error, #tipoF-error, #fechaI-error, #fechaF-error  {
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
    rules: {
        sltmes: {
          required: true
        },
        sltcni: {
          required: true
        },
        sltAnnio: {
          required: true
        }
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

<script>
        $(function(){
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaI").datepicker({changeMonth: true,}).val();
        $("#fechaF").datepicker({changeMonth: true}).val();
        
        
});
</script>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Seguimiento Ingresos Presupuesto Contabilidad</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Inicial:</label>
                            <select  name="tipoI" id="tipoI" class="select2_single form-control" title= "Seleccione Tipo Comprobante inicial" style="height: 30px" required>
                                <?php 
                                $tci= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_comprobante_cnt cn 
                                LEFT JOIN gf_tipo_comprobante t ON cn.tipocomprobante = t.id_unico  
                                WHERE t.compania = $compania AND cn.parametrizacionanno = $anno
                                    AND t.clasecontable = 9 
                                    ORDER BY t.id_unico ASC";
                                $rsTci = $mysqli->query($tci);
                                while ($filaTci = mysqli_fetch_row($rsTci)) { ?>
                                    <option value="<?php echo $filaTci[0];?>"><?php echo $filaTci[1].' - '.ucwords(mb_strtolower($filaTci[2]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipoF" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Comprobante Final:</label>
                            <select name="tipoF" class="select2_single form-control" id="tipoF" title="Seleccione Tipo Comprobante final" style="height: 30px"  required >
                                <?php 
                                $tcf= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_comprobante_cnt cn 
                                LEFT JOIN gf_tipo_comprobante t ON cn.tipocomprobante = t.id_unico  
                                WHERE t.compania = $compania AND cn.parametrizacionanno = $anno
                                    AND t.clasecontable = 9 
                                    ORDER BY t.id_unico DESC";
                                $rsTcf = $mysqli->query($tcf);
                                while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                <option value="<?php echo $filaTcf[0];?>"><?php echo ($filaTcf[1].' - '. ucwords(mb_strtolower($filaTcf[2]))); ?></option>
                                <?php } ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                             <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                             <input class="form-control" type="text" name="fechaI" id="fechaI"  value="<?php echo date("d/m/Y");?>" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                             <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                             <input class="form-control" type="text" name="fechaF" id="fechaF"  value="<?php echo date("d/m/Y");?>" required>
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>             
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
    function reporteExcel(){
       $('form').attr('action', 'informes/INF_SEGUIMIENTO_PC.php');
    }
    </script>
</div>
<?php require_once 'footer.php' ?>  
</body>
</html>