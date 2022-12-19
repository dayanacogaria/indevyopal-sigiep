<?php 
#################################################################################################
#  ********************************** Modificaciones **********************************         #
#################################################################################################
#09/03/2018 |Erica G. | Quitar Ciudad
#06/03/2018 |Erica G. | ARCHIVO CREADO
#################################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con  = new ConexionPDO();

$cr = $con->Listar("SELECT DISTINCT cr.id_unico, cr.nombre FROM gf_clase_retencion cr ");
?>
<html>
    <head>
        <title>Informe Exógenas Municipal</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #sltAnnio-error, #departamento-error, #fechaI-error, #fechaF-error, #claseR-error  {
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
                    changeYear: true,
                    changeMonth: true,
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fechaI").datepicker().val();
                $("#fechaF").datepicker().val();
            });
        </script>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-left: 0px;margin-top: -20px"> 
                    <h2 align="center" class="tituloform">Informe Exógenas Municipal</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                            <div class="form-group">
                                
                                <div class="form-group" style="margin-top: -5px;">
                                     <label for="sltAnnio" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                     <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                        <option value="">Año</option>
                                        <?php 
                                        $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                        $rsannio = $mysqli->query($annio);
                                        while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                             <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                        <?php }?>                                    
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="claseR" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Retención :</label>
                                    <select  name="claseR" id="claseR" class="select2_single form-control" title="Seleccione Clase Retención " style="height: 30px" required>
                                            <option value="">Clase Retención</option>                              
                                            <?php  for ($i = 0; $i < count($cr); $i++) {
                                                echo '<option value="'.$cr[$i][0].'">'.ucwords($cr[$i][1]).'</option>';
                                            } ?>
                                    </select>
                                </div>
                                <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                                   <!-- <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              -->
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
            $(document).ready(function() {
              $(".select2_single").select2({
                allowClear: true
              });
            });
        </script>
        <script>
        function reporteExcel(){
            $('form').attr('action', 'informes/INF_EXOGENAS_MUNICIPAL_PRU.php?tipo=2');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/INF_EXOGENAS_MUNICIPAL.php?tipo=1');
        }
        </script>
        <?php require_once 'footer.php' ?>  
    </body>
</html>