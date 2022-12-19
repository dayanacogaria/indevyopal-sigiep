<?php
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
?>
    <title>Existencias - Inventario</title>
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
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: -20px">
                <h2 align="center" class="tituloform">Existencias de Inventario</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                        <div class="form-group">
                            <?php
                            $ein= "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' ', UPPER(pi.nombre)) AS codele
                                   FROM            gf_detalle_movimiento dm
                                   LEFT JOIN       gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE           pi.compania = $compania
                                   ORDER BY        pi.id_unico ASC";
                            $rsEin = $mysqli->query($ein);
                            ?>
                            <label for="Ein" class="col-sm-5 control-label">
                                <strong style="color:#03C1FB;">*</strong>Elemento Inicial:
                            </label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltEin" id="sltEin" class="form-control select" title="Seleccione Elemento inicial" style="height: 30px" required>
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
                            $efn = "SELECT DISTINCT dm.planmovimiento, CONCAT (pi.codi,' ', UPPER(pi.nombre)) AS codele
                                   FROM      gf_detalle_movimiento dm
                                   LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                   WHERE     pi.compania = $compania
                                   ORDER BY pi.id_unico DESC";
                            $rsEfn = $mysqli->query($efn);
                            ?>
                            <label for="Efn" class="col-sm-5 control-label">
                                <strong style="color:#03C1FB;">*</strong>Elemento Final:
                            </label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltEfn" id="sltEfn" class="form-control select" title="Seleccione Elemento final" style="height: 30px" required>
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
                            <label for="fechafin" type = "date" class="col-sm-5 control-label">
                                <strong class="obligado">*</strong>Fecha Final:
                            </label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input class="form-control" type="text" name="fechaini" id="fechaini" placeholder="FechaFinal">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="Efn" class="col-sm-5 col-md-5 col-lg-5 control-label">Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="" class="radio-inline"><input type="radio" name="optArchivo" id="" onclick="reportePdf()" required>PDF</label>
                                <label for="" class="radio-inline"><input type="radio" name="optArchivo" id="" onclick="reporteExcel()">EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Efn" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-primary"><span class="fa fa-send"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php' ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-notify.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        $(".select2, #sltEfn, #sltEin").select2();

        function reporteExcel(){
           $('form').attr('action', 'informes/generar_INF_EXIS_INVENTARIO_EXCEL.php');
        }

        function reportePdf(){
            $('form').attr('action', 'informes/generar_INF_EXISTENCIAS_INVENTARIO.php');
        }
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
        });
    </script>
</body>
</html>