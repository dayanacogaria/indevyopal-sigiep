<?php
require("./Conexion/ConexionPDO.php");
require './Conexion/conexion.php';
require './head.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con = new ConexionPDO();
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Listado Facturación</title>
<style>
    #form>.form-group{
        margin-bottom: 5px !important;
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
    .campos{padding: 0px;font-size: 10px}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 class="tituloform" align="center" style="margin-top: 0px;">Listado Facturación</h2>
                <h5 id="forma-titulo3a" align="center" style="width:99%; display:inline-block; margin-bottom: 10px; margin-right: -1px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px">.</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/INFORME_FACTURACION.php" target="_blank" >
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="lbltipoinforme" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Tipo Factura:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="stltipoinf" id="stltipoinf" class="select2_single form-control" title="Tipo Informe" style="width:299px; height: 38px" required>
                                        <option value="" >Tipo Factura</option>
                                        <?php
                                    $html = "";
                                    $sqlCn = "SELECT * FROM gp_tipo_factura WHERE id_unico IN (2,3)";
                                    $resc = $mysqli->query($sqlCn);
                                    while ($row2 = mysqli_fetch_row($resc)) {
                                        echo '<option value="' . $row2[0] . '">' . $row2[1] . '</option>';
                                    }

                                    echo $html;
                                    ?>
                                    </select>
                            </div>
                        </div> 
                        <div id="dataform" style="display: none;">                        
                            <div class="form-group" id="divFechaInicial">
                                <label for="fechaInicial" type = "date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <input class="form-control" type="text" name="fechaInicial" id="fechaInicial"  value="<?php echo date("Y-m-d");?>" required autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group" id="divFechaFinal" style="margin-top: -10px;">
                                <label for="fechaFinal" type = "date" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <input class="form-control" type="text" name="fechaFinal" id="fechaFinal" required autocomplete="off">
                                </div>
                            </div>     
                            <div class="form-group" style="display: block;">
                                <label for="stlfacI" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado"></strong>Factura Inicial:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <select name="stlfacI" id="stlfacI" class="select2_single form-control" title="Seleccione Factura Inicial" style="width:299px; height: 38px">
                                            <option value="" >Factura Inicial</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="form-group" style="display: block;">
                                    <label for="stlfacF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado"></strong>Factura Final:</label>
                                    <div class="col-sm-5 col-md-5 col-lg-5">
                                        <select name="stlfacF" id="stlfacF" class="select2_single form-control" title="Seleccione Factura Final" style="width:299px; height: 38px">
                                                <option value="" >Factura Final</option>
                                        </select>
                                    </div>
                            </div> 
                            <div id="chmp" class="form-group" style="display: none; padding-bottom: 15px;">   
                                <label for="chkform" class="control-label col-sm-5 col-md-5 col-lg-5">Formato:<strong class="obligado"></strong></label>                      
                               <div class="col-sm-5 col-md-5 col-lg-5" style="padding-top: 6px;">
                                   <input name="chkform" id="chkform" type="checkbox" value="1" checked="checked">
                               </div>                         
                            </div>            
                            <div class="form-group">
                                    <label for="optTipoPDF" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado"></strong></label>
                                    <div class="col-sm-5 col-md-5 col-lg-5" style="margin-top: 10px;">
                                        <button  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-left: 0px;" type="submit"><i class="fa fa-file-pdf-o" aria-hidden="true" ></i></button>
                                    </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    </div>    
    <?php require_once 'footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script>    
    <script>        
        $("#stltipoinf").change(function (e) {
            $("#stlfacI").empty();
            $("#stlfacI").append('<option value="0" selected>Factura Inicial</option>');
            $("#stlfacF").empty();
            $("#stlfacF").append('<option value="0" selected>Factura Final</option>');
            $("#dataform").css('display','block');
            if($("#stltipoinf").val()==2){
                $("#chmp").css('display','block');
            } else {
                $("#chmp").css('display','none');
            }
        });

        $("#chkform").change(function (e) {
            if($("#chkform").prop('checked')){
                $("#chkform").val(1);
            } else {
                $("#chkform").val(0);
            }
        });

        $("#fechaFinal").change(function (e) {
            let stltipoinf = $("#stltipoinf").val();
            let tipofac = stltipoinf;
            stltipoinf = 0;
            let fechaInicial = $("#fechaInicial").val();
            let fechaFinal = $(this).val();
            var form_data = {
                stltipoinf: stltipoinf,
                tipofac: tipofac,
                fechaInicial: fechaInicial,
                fechaFinal: fechaFinal,
            };            
            $.ajax({
                type: 'POST',
                url: "informes/INFORME_FACTURACION.php",
                data: form_data,
                success: function (data) {
                    console.log(data);
                    $("#stlfacI").empty();
                    $("#stlfacI").append('<option value="0" selected>Factura Inicial</option>');
                    $("#stlfacF").empty();
                    $("#stlfacF").append('<option value="0" selected>Factura Final</option>');
                    let datos = JSON.parse(data);
                    let datosI = datos['datosI'];
                    let datosF = datos['datosF'];
                    $(datosI).each(function (i, v) { // indice, valor
                        $("#stlfacI").append('<option value=' + v[0] + '>' + v[1] +' - '+ v[2] + ' ' + v[4] + ' '+ v[5] + '</option>');
                    });
                    $(datosF).each(function (i, v) { // indice, valor
                        $("#stlfacF").append('<option value=' + v[0] + '>' + v[1] +' - '+ v[2] + ' ' + v[4] + ' '+ v[5] + '</option>');
                    });
                }
            });
        });
        $("#fechaInicial").change(function (e) {
            let stltipoinf = $("#stltipoinf").val();
            let tipofac = stltipoinf;
            stltipoinf = 0;
            let fechaInicial = $(this).val();;
            let fechaFinal =  $("#fechaFinal").val();
            var form_data = {
                stltipoinf: stltipoinf,
                tipofac: tipofac,
                fechaInicial: fechaInicial,
                fechaFinal: fechaFinal,
            };            
            $.ajax({
                type: 'POST',
                url: "informes/INFORME_FACTURACION.php",
                data: form_data,
                success: function (data) {
                    console.log(data);
                    $("#stlfacI").empty();
                    $("#stlfacI").append('<option value="0" selected>Factura Inicial</option>');
                    $("#stlfacF").empty();
                    $("#stlfacF").append('<option value="0" selected>Factura Final</option>');
                    let datos = JSON.parse(data);
                    let datosI = datos['datosI'];
                    let datosF = datos['datosF'];
                    $(datosI).each(function (i, v) { // indice, valor
                        $("#stlfacI").append('<option value=' + v[0] + '>' + v[1] +' - '+ v[2] + ' ' + v[4] + ' '+ v[5] + '</option>');
                    });
                    $(datosF).each(function (i, v) { // indice, valor
                        $("#stlfacF").append('<option value=' + v[0] + '>' + v[1] +' - '+ v[2] + ' ' + v[4] + ' '+ v[5] + '</option>');
                    });
                }
            });
        });
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
        });

        $(function(){
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            $("#fechaInicial").datepicker({changeMonth: true,}).val(fecAct);
            $("#fechaFinal").datepicker({changeMonth: true}).val(fecAct);
        });
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });            
        });             
        
    </script>
</body>
</html>
