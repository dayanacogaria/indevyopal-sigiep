<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#11/04/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$annio      = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
$con        = new ConexionPDO();
$titulo     ="";
$t          = $_GET['t'];
if($t==1){
    $titulo = "Informe Comparendos Recaudados";
    $t2     = "Recaudo";
} elseif($t==2){
    $titulo = "Informe Comparendos Sin Recaudar";
    $t2     = "Comparendo";
}elseif($t==3){
    $titulo = "Informe Comparendos Repetidos";
    $t2     = "Comparendo";
}

?>
<html>
    <head>
        <title><?php echo $titulo;?></title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #sucursal-error, #fechaini-error, #fechafin-error {
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

                $(".cancel").click(function () {
                    validator.resetForm();
                });
            });
        </script>
        <script>
            $(function () {
                var fecha = new Date();
                var dia = fecha.getDate();
                var mes = fecha.getMonth() + 1;
                if (dia < 10) {
                    dia = "0" + dia;
                }
                if (mes < 10) {
                    mes = "0" + mes;
                }
                var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
                $("#fechaini").datepicker({changeMonth: true, }).val(fecAct);
                $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
            });
        </script>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
                    <h2 align="center" class="tituloform"><?php echo $titulo;?></h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                            <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                            <div class="form-group">
                                <input type="hidden" name="tipo" value="<?php echo $t?>" id="tipo">
                                <div class="form-group" style="margin-top: -10px; ">
                                    <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong> Sucursal:</label>
                                    <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" style="height: auto " required>
                                        <?php 
                                        #Consulta Sucursal
                                        $row = $con->Listar("SELECT id_unico, LOWER(nombre) FROM gf_sucursal");
                                        echo '<option value="">Sucursal</option>';
                                        for ($i = 0; $i < count($row); $i++) {
                                            echo '<option value = "'.$row[$i][0].'">'. ucwords($row[$i][1]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px;">
                                    <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial <?php echo $t2;?>:</label>
                                    <input class="form-control" type="text" name="fechaini" id="fechaini"  value="" required title="Seleccione Fecha Inicial <?php echo $t2;?>">
                                </div>
                                <div class="form-group" style="margin-top: -10px;">
                                    <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final <?php echo $t2;?>:</label>
                                    <input class="form-control" type="text" name="fechafin" id="fechafin"  value="" required title="Seleccione Fecha Final <?php echo $t2;?>">
                                </div>
                                <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                    <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                                    <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true
                });
            });
        </script>
        <script>
            function reporteExcel() {
                $('form').attr('action', 'informes/INF_COMPARENDOS.php?ex=excel');
            }

            function reportePdf() {
                $('form').attr('action', 'informes/INF_COMPARENDOS.php?ex=pdf');
            }
        </script>
        <?php require_once 'footer.php' ?>  
    </body>
</html>