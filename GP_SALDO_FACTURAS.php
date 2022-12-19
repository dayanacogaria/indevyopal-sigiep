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
?>
<title>Facturas Saldo</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label  #fecha-error, #tipoF-error,  #terceroI-error,  #terceroF-error  {
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
        $("#fecha").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Facturas Con Saldo</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -10px">
                            <label for="tipoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Factura:</label>
                            <select name="tipoF" id="tipoF"  style="height: auto" class="select2_single form-control" title="Seleccione Tipo Factura" required="required">
                                <option value="">Tipo Factura</option>
                                <?php 
                                $row = $con->Listar("SELECT * FROM gp_tipo_factura WHERE compania = $compania");
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.$row[$i][2].' - '.ucwords($row[$i][1]).'</option>';
                                }
                                ?>                                    
                            </select>
                        </div>  
                        <div class="form-group" style="margin-top: -5px;">
                             <label for="fecha" type = "date" autocomplete="off" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Corte Factura:</label>
                             <input autocomplete="off" class="form-control" title="Seleccione Fecha de Corte" type="text" name="fecha" id="fecha" placeholder="Fecha Corte Factura" required="required">
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label for="terceroI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero Inicial:</label>
                            <select name="terceroI" id="terceroI"  style="height: auto" class="select2_single form-control" title="Seleccione Tercero Inicial" required="required">
                                <option value="">Tercero Inicial</option>
                                <?php 
                                $row = $con->Listar("SELECT DISTINCT t.id_unico,  
                                        IF(CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) = '',
                                        (t.razonsocial),
                                        CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos)) AS NOMBRE, 
                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                        t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                    FROM gp_factura f 
                                    LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                                    WHERE t.compania = $compania ORDER BY id_unico ASC ");
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</option>';
                                }
                                ?>                                    
                            </select>
                        </div>  
                        <div class="form-group" style="margin-top: -5px">
                            <label for="terceroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero Final:</label>
                            <select name="terceroF" id="terceroF"  style="height: auto" class="select2_single form-control" title="Seleccione Tercero Final" required="required">
                                <option value="">Tercero Final</option>
                                <?php 
                                $row = $con->Listar("SELECT DISTINCT  t.id_unico,  
                                        IF(CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) = '',
                                        (t.razonsocial),
                                        CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos)) AS NOMBRE, 
                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                        t.numeroidentificacion, 
                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                    FROM gp_factura f 
                                    LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                                    WHERE t.compania = $compania ORDER BY id_unico DESC ");
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).' - '.$row[$i][2].'</option>';
                                }
                                ?>                                    
                            </select>
                        </div>  
                        <div class="form-group" style="margin-top: -5px;">
                             <label for="num_fac"class="col-sm-5 control-label"><strong class="obligado"></strong>Mínimo de facturas con deuda:</label>
                             <input class="form-control" title="" type="text" name="num_fac" id="num_fac" placeholder="Mínimo de facturas con deuda">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="acumulado"class="col-sm-5 control-label"><strong class="obligado"></strong>Acumulado Por Tercero:</label>
                             <input class="form-control" title="" type="checkbox" name="acumulado" id="acumulado" placeholder="Detallado" style="margin-left: -70px;width: 20px;margin-top: 0px;">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                             <label for="acumuladov"class="col-sm-5 control-label"><strong class="obligado"></strong>Acumulado Por Vivienda:</label>
                             <input class="form-control" title="" type="checkbox" name="acumuladov" id="acumuladov" placeholder="Acumulado Por Vivienda" style="margin-left: -70px;width: 20px;margin-top: 0px;">
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
            if( $('#acumulado').prop('checked') ) {
                $('form').attr('action', 'informes_servicios/INF_SALDO_FACTURAS.php?t=2&a=1');
            } else {
                if( $('#acumuladov').prop('checked') ) {
                    $('form').attr('action', 'informes_servicios/INF_SALDO_FACTURAS.php?t=2&a=3');
                } else {
                    $('form').attr('action', 'informes_servicios/INF_SALDO_FACTURAS.php?t=2&a=2');
                }
            }
            //
        }
        $('#acumulado').change(function(){
            if( $('#acumulado').prop('checked') ) {
                $('#acumuladov').prop('checked',false);
            }
        });
        $('#acumuladov').change(function(){
            if( $('#acumuladov').prop('checked') ) {
                $('#acumulado').prop('checked',false);
            }
        })
    </script>
    <script>
    function reportePdf(){
        $('form').attr('action', 'informes/INF_SALDO_FACTURAS.php?t=1');
    }
    </script>
</div>
</body>
</html>