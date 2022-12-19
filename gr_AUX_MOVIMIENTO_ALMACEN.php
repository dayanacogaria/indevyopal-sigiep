<?php
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
?>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/datapicker.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <style type="text/css" media="screen">
        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form select{
            width: 100%;
        }

        .btn{
            box-shadow: 0px 2px 5px 1px grey;
        }

        .client-form input[type="file"]{
            width: 100%
        }

        .dependencia, .responsable{
            display: none;
        }

        .client-form>.form-group{
            margin-bottom: 5px;
        }
    </style>
    <title>Auxiliares Movimiento - Almacén</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: -20px;">
                <h2 align="center" class="tituloform">Auxiliares Movimiento de Almacén</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <?php
                            $ein= "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' - ',pi.nombre) AS codele
                                   FROM      gf_detalle_movimiento dm
                                   LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE     pi.compania = $compania
                                   ORDER BY pi.id_unico ASC";
                            $rsEin = $mysqli->query($ein);
                            ?>
                            <label for="Ein" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Elemento Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltEin" id="sltEin" class="form-control" title="Seleccione Elemento inicial" style="height: 30px" required>
                                    <?php
                                    echo "<option value=''>Elemento Inicial</option>";
                                    while ($filaEin = mysqli_fetch_row($rsEin)){
                                        echo "<option value='$filaEin[0]'>$filaEin[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $efn= "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' - ',pi.nombre) AS codele
                                   FROM      gf_detalle_movimiento dm
                                   LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE     pi.compania = $compania
                                   ORDER BY pi.id_unico DESC";
                            $rsEfn = $mysqli->query($efn);
                            ?>
                            <label for="Efn" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Elemento Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltEfn" id="sltEfn" class="form-control" title="Seleccione Elemento final" style="height: 30px" required/>
                                    <?php
                                    echo "<option value=''>Elemento Final</option>";
                                    while ($filaEfn = mysqli_fetch_row($rsEfn)){
                                        echo "<option value='$filaEfn[0]'>$filaEfn[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $movI = "SELECT clase, nombre FROM gf_tipo_movimiento WHERE clase IN(2,3) AND compania = $compania order by clase ASC";
                            $rsmovi = $mysqli->query($movI);
                            ?>
                            <label for="Movi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento Inicial:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltmovi" id="sltmovi" style="height: auto" class="form-control" title="Seleccione Tipo movimiento inicial" required>
                                    <option value="">Movimiento Inicial</option>
                                        <?php
                                        while ($filamovi = mysqli_fetch_row($rsmovi)){?>
                                            <option value="<?php echo $filamovi[0];?>"><?php echo $filamovi[1];?></option>
                                        <?php
                                        }
                                        ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                            $movF = "SELECT clase, nombre FROM gf_tipo_movimiento WHERE clase IN(2,3) AND compania = $compania order by clase DESC";
                            $rsmovf = $mysqli->query($movF);
                            ?>
                            <label for="Movf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Movimiento Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltmovf" id="sltmovf" style="height: auto" class="form-control" title="Seleccione Tipo movimiento final" required>
                                    <?php
                                        echo "<option value=''>Movimiento Final</option>";
                                        while ($filamovf = mysqli_fetch_row($rsmovf)){
                                            echo "<option value='$filamovf[0]'>$filamovf[1]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="height: 33px;">
                            <label for="fechaini" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" type="text" name="fechafin" id="fechafin" placeholder="Fecha Final" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group">                            
                            <label for="tercero" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="optTer" class="radio-inline"><input type="radio" name="optTercero" id="optTercero" value="2" checked>Tercero</label>
                                <label for="optTer" class="radio-inline"><input type="radio" name="optTercero" id="optTercero" value="1">Responsable</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="optTipoArchivo" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Archivo</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="optPdf" class="radio-inline"><input type="radio" name="optTipoArchivo" id="optPdf" required>PDF</label>
                                <label for="optExl" class="radio-inline"><input type="radio" name="optTipoArchivo" id="optExl">EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fechaini" class="col-sm-5 control-label"><strong class="obligado"></strong></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button  class="btn btn-primary" title="Generar reporte PDF"><i class="fa fa-play" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_table.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script> 
    <script>
        $(".select2, #sltEfn, #sltEin, #sltmovi, #sltmovf, #sltercero").select2();

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
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#fechaini").datepicker({changeMonth: true}).val();
            $("#fechafin").datepicker({changeMonth: true}).val();
        });

        $("#optPdf").click(function () {
            if($("#optPdf").is(':checked')){
                $("form[name='form']").attr("action", "informes/generar_INF_AUX_MOV_ALMACEN.php");
            }
        });

        $("#optExl").click(function () {
            if($("#optExl").is(':checked')){
                $("form[name='form']").attr("action", "informes_almacen/generar_INF_AUX_MOV_ALMACEN_EXCEL.php");
            }
        });
    </script>
</body>
</html>