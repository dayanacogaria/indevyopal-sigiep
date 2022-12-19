<?php
################################################################################################################
#                                           MODIFICACIONES
################################################################################################################
#27/08/2018 |Erica G. |Fuente
#20/10/2017 |Erica G. |Archivo Creado 
################################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';
$anno = $_SESSION['anno'];
?>
<title>Libro Auxiliar Presupuestañ</title>
</head>
<body>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>

    <style>
        label  #sltctaf-error, #fechaini-error, #fechafin-error, #sltctai-error , #fuente-error{
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
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {

                    $(element)
                            .closest("form")
                            .find("label[for='" + element.attr("id") + "']")
                            .append(error);
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

    <style>
        .form-control {font-size: 12px;}

    </style>

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


            $("#fechaini").datepicker({changeMonth: true, }).val();
            $("#fechafin").datepicker({changeMonth: true}).val();


        });
    </script>
    <!-- contenedor principal -->  
    <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Libro Auxiliar Presupuestal - Gastos</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <?php
                            $cuentaI = "SELECT id_unico, CONCAT_WS(' - ', codi_presupuesto, nombre), codi_presupuesto  
                                        FROM gf_rubro_pptal 
                                        WHERE tipoclase=7 AND parametrizacionanno = $anno ORDER BY codi_presupuesto ASC";
                            $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Inicial:</label>
                                <select name="sltctai" id="sltctai"  style="height: auto" class="select2_single form-control" title="Seleccione Código Inicial" required="required">
                                    <option value="">Código Inicial</option>
                                    <?php
                                    while ($filactai = mysqli_fetch_row($rsctai)) {
                                        ?>
                                        <option value="<?php echo $filactai[2]; ?>"><?php echo ucwords(mb_strtolower($filactai[1])); ?></option>                                
                                        <?php
                                    }
                                    ?>                                    
                                </select>
                            </div>            
                            <?php
                            $cuentaF = "SELECT id_unico, CONCAT_WS(' - ', codi_presupuesto, nombre), codi_presupuesto  
                                        FROM gf_rubro_pptal 
                                        WHERE tipoclase=7 AND parametrizacionanno = $anno ORDER BY codi_presupuesto DESC";
                            $rsctaf = $mysqli->query($cuentaF);
                            ?>
                            <div class="form-group" style="margin-top: 0px">
                                <label for="sltctaf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Final:</label>
                                <select name="sltctaf" id="sltctaf" style="height: auto" class=" select2_single form-control" title="Seleccione Código Final" required="required">
                                    <option value="">Código Final</option>
                                     <?php
                                    while ($filactaf = mysqli_fetch_row($rsctaf)) {
                                        ?>
                                        <option value="<?php echo $filactaf[2]; ?>"><?php echo ucwords(mb_strtolower($filactaf[1])); ?></option>                                
                                        <?php
                                    }
                                    ?>                                    
                                </select>
                            </div>   
                            <?php
                            $fuente = "SELECT id_unico,nombre  
                                        FROM gf_fuente 
                                        WHERE parametrizacionanno = $anno ORDER BY id_unico ASC";
                            $rowff = $mysqli->query($fuente);
                            ?>
                            <div class="form-group" style="margin-top: 0px">
                                <label for="fuente" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Fuente:</label>
                                <select name="fuente" id="fuente" style="height: auto" class=" select2_single form-control" title="Seleccione Fuente">
                                    <option value=""> Fuente </option>
                                   <?php
                                    while ($rowf = mysqli_fetch_row($rowff)) {
                                        ?>
                                        <option value="<?php echo $rowf[0]; ?>"><?php echo ucwords(mb_strtolower($rowf[1])); ?></option>                                
                                        <?php
                                    }
                                    ?> 
                                                                      
                                </select>
                            </div> 
                            
                            <div class="form-group" style="margin-top: -0px;">
                                <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                                <input class="form-control" type="text" name="fechafin" id="fechafin" placeholder="Fecha Final" required="required" title="Seleccione Fecha Final">
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
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true
                });
            });
        </script>
        <!-- Llamado al pie de pagina -->
        <?php require_once 'footer.php' ?>  
        <script>
            function reporteExcel() {
                $('form').attr('action', 'informes/inf_libro_auxiliar_pptal.php?c=2');
            }

        </script>
        <script>
            function reportePdf() {
                $('form').attr('action', 'informes/inf_libro_auxiliar_pptal.php?c=1');
            }
        </script>
    </div>
</body>
</html>