<?php 
##################################################################################################
#********************************** Modificaciones ¨*********************************************#
##################################################################################################
#23/01/2019 | Creación
##################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
$t          = '';
$tipo       = 0;
if($_GET['t']==1){
    $row    = $con->Listar("SELECT id_unico, CONCAT_WS(' ', prefijo, nombre) "
            . "FROM gp_tipo_factura WHERE compania = $compania");
    $t      = 'Informe Facturación Por Concepto';
    $tipo   = 1;
} elseif($_GET['t']==2) {
    $row    = $con->Listar("SELECT id_unico,  nombre "
            . "FROM gp_tipo_pago WHERE compania = $compania");
    $t      = 'Informe Recaudo Por Concepto';
    $tipo   = 2;
}elseif($_GET['t']==3) {
    $row    = $con->Listar("SELECT id_unico, CONCAT_WS(' ', prefijo, nombre) "
            . "FROM gp_tipo_factura WHERE compania = $compania");
    $t      = 'Informe Cartera Por Concepto';
    $tipo   = 3;
}
?>
<title><?php echo $t;?></title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label  #fechaI-error,#fechaF-error, #tipoF-error,  #terceroI-error,  #terceroF-error  {
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
        $("#fechaF").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform"><?php echo $t;?></h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <input type="hidden" name="ti" id="ti" value="<?php echo $tipo?>">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo:</label>
                            <select name="tipo" id="tipo"  style="height: auto" class="select2_single form-control" title="Seleccione Tipo" required="required">
                                <?php 
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
                                }
                                ?>                                    
                            </select>
                        </div>  
                        <div class="form-group" style="margin-top: -5px;" id="divFechaInicial">
                             <label for="fechaI" type = "date" autocomplete="off" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                             <input autocomplete="off" class="form-control" title="Seleccione Fecha Inicial" type="text" name="fechaI" id="fechaI" placeholder="Fecha Inicial" required="required">
                        </div>
                        <div class="form-group" style="margin-top: -15px;" id="divFechaFinal">
                             <label for="fechaF" type = "date" autocomplete="off" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                             <input autocomplete="off" class="form-control" title="Seleccione Fecha Final" type="text" name="fechaF" id="fechaF" placeholder="Fecha Final " required="required">
                        </div>
                        <div class="form-group" style="margin-top: -15px; display: none" id="divTercero">
                             <label for="tercero" type = "date" autocomplete="off" class="col-sm-5 control-label"><strong class="obligado">*</strong>Acumulado Por Tercero:</label>
                             <input type="radio" title="Acumulado Por Tercero"  name="tercero" id="tercero" value="1">Sí
                             <input type="radio" title="Acumulado Por Tercero"  name="tercero" id="tercero" value="2" checked="checked">No
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
    <?php require_once 'footer.php'?>  
    <script>
        function reporteExcel(){
            $('form').attr('action', 'informes/INF_FACTURAS.php');
        }
    </script>
    <?php if($_GET['t']==3){?>
    <script>
        $(document).ready(function() {
            $("#divFechaInicial").css('display','none');
            $("#divFechaFinal").css('margin-top','10px');
            $("#fechaI").attr('required', false);
            $("#divTercero").css('display','block');
        })
    </script>
    <?php } ?>
</div>
</body>
</html>