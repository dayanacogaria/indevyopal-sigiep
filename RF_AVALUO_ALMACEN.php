<?php
require 'head_listar.php';
?>
    <title>Avaluo de Almacén</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <style>
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        .cabeza{
            white-space:nowrap;
            padding: 20px;
        }

        .campos{
            padding: 0px;
            font-size: 10px
        }

        .valorLabel{
            font-size: 10px;
        }

        .valorLabel:hover{
            cursor: pointer;
            color:#1155CC;
        }

        body{
            font-size: 10px;
        }

        .client-form input[type="text"]{
            width: 100%;
        }

        .client-form textarea{
            width: 100%;
            height: 60px;
            margin-top: 0px;
        }

        #form>.form-group{
            margin-bottom: 0px !important;
        }

        .btn {
            box-shadow: 0px 2px 5px 1px grey;
        }

        .salto{
            margin-bottom: 5px !important;
        }

        .inferior{
            margin-bottom: 5px !important;
        }

        thead, tbody { display: block; }

        tbody {
            height: 220px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        thead>tr>th, tbody>tr>td{
            width: 215px;
        }

        .scrollbar-primary::-webkit-scrollbar {
            width: 10px;
            background-color: transparent;
            border-right-color: #00577b;
            border-left-color: #00577b;
            border-top-color: #00577b;
            border-bottom-color: #00577b;
            border-width: 2px;
            border-radius: 10px;
            border-style: solid;
        }

        .scrollbar-primary::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 5px 6px rgba(0, 0, 0, 0.1);
            background-color: #00aae5;
        }

        textarea.form-control{
            height: 34px;
        }

        tr>td>input, tr>td>textarea{
            border:none !important;
            background-color: transparent !important;
            box-shadow: none !important;
        }

        tr>td>input:focus, tr>td>textarea:focus{
            background-color: #fff !important;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075) !important;
            -webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075) !important;
            border: 1px solid #ccc !important;
            border-color: #66afe9 !important;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6) !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-lg-10 col-md-10 col-sm-10 text-left inferior">
                <h2 align="center" style="margin-top:0px" class="tituloform">Avaluo de Almacén</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#">
                        <p align="center" class="parrafoO" style="margin-bottom:0.50em">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="sltTipoA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Avaluo:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoA" id="sltTipoA" class="select2 form-control" required="" title="Seleccione tipo de avaluo" placeholder="Tipo Avaluo">
                                    <option value="">Tipo Avaluo</option>
                                </select>
                            </div>
                            <label for="txtNumero" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Número:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtNumero" id="txtNumero" class="form-control" value="" required="required" placeholder="Número" title="Ingrese número de avaluo">
                            </div>
                            <label for="txtFecha" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="txtFecha" id="txtFecha" class="form-control" value="" required="required" placeholder="Fecha" title="Ingrese fecha de avaluo" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtObservaciones" class="col-sm-2 col-md-2 col-lg-2 control-label">Observaciones:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <textarea name="txtObservaciones" id="txtObservaciones" class="form-control" rows="3" placeholder="Revelaciones" title="Ingreres observaciones"></textarea>
                            </div>
                            <label for="sltBuscar" class="col-sm-2 col-md-2 col-lg-2 control-label">Buscar:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltBuscar" id="sltBuscar" class="select2 form-control" title="Seleccione avaluo" placeholder="Avaluo"></select>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <div class="col-sm-11 col-md-11 col-lg-11 salto">
                                <button type="button" id="btn-nuevo" onclick="javascript:nuevo()" class="btn btn-primary glyphicon glyphicon-plus nuevo"></button>
                                <button type="submit" id="btn-guardar" class="btn btn-primary glyphicon glyphicon-floppy-disk guardar"></button>
                                <button type="button" id="btn-imprimir" class="btn btn-primary glyphicon glyphicon-print imprimir"></button>
                                <button type="button" id="btn-modificar" class="btn btn-primary glyphicon glyphicon-pencil modificar"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 text-left salto2 table-responsive">
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>SERIE PRODUCTO</th>
                            <th>ELEMENTO</th>
                            <th>VALOR AVALUO</th>
                            <th>VIDA UTIL REMANENTE</th>
                            <th>REVELACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $html = "";
                        $sql = "SELECT    pro.id_unico, pre.valor, CONCAT_WS(' - ', pln.codi, pln.nombre) as elemento
                                FROM      gf_producto pro
                                LEFT JOIN gf_movimiento_producto     mpr ON mpr.producto          = pro.id_unico
                                LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                                LEFT JOIN gf_movimiento              mov ON dtm.movimiento        = mov.id_unico
                                LEFT JOIN gf_tipo_movimiento         tpm ON mov.tipomovimiento    = tpm.id_unico
                                LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                                LEFT JOIN gf_producto_especificacion pre ON pre.producto          = pro.id_unico
                                WHERE     (tpm.clase           = 2)
                                AND       (pre.fichainventario = 6)";
                        $res = $mysqli->query($sql);
                        $dta = $res->fetch_all(MYSQLI_NUM);
                        foreach ($dta as $row) {
                            $html .= "<tr id=\"$row[0]\" >";
                            $html .= "<td class='campos text-right'>$row[1]</td>";
                            $html .= "<td class='campos txt-left'>$row[2]</td>";
                            $html .= "<td class='campos'>";
                            $html .= "<input type=\"text\" name=\"txtAvaluo$row[0]\" id=\"txtAvaluo$row[0]\" class=\"form-control\" title=\"Ingrese valor del avaluo\">";
                            $html .= "</td>";
                            $html .= "<td class='campos'>";
                            $html .= "<input type=\"number\" name=\"txtVida$row[0]\" id=\"txtVida$row[0]\" class=\"form-control\" title=\"Ingrese valor de vida ultil\">";
                            $html .= "</td>";
                            $html .= "<td class='campos'>";
                            $html .= "<textarea name=\"txtRevelaciones$row[0]\" id=\"txtRevelaciones$row[0]\" class=\"form-control\" title=\"IngreseRevelaciones\"></textarea>";
                            $html .= "</td>";
                            $html .= "</tr>";
                        }
                        echo $html;
                        ?>
                    </tbody>
                </table>
            </div>
            <?php require_once 'footer.php'; ?>
        </div>
    </div>
    <script type="text/javascript" src="js/md5.js" ></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2" ).select2({
            placeholder: "Seleccione una opción",
            allowClear: true
        });

        $(document).ready(function(){
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
            $("#txtFecha").datepicker({changeMonth: true}).val();
        });
        /*
         * @param type{string} or {number} e
         * @returns {/\d/.test(String.fromCharCode(keynum))}
         */
        function justNumbers(e){
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
            return true;
            return /\d/.test(String.fromCharCode(keynum));
        }

        function nuevo(){
            window.location = 'RF_AVALUO_ALMACEN.php';
        }

        <?php
        $html = "";
        if(empty($_GET['avaluo'])){
            $html .= "\n\t\t$('#btn-nuevo, #btn-imprimir, #btn-modificar').attr('disabled',true);";
            $html .= "\n\t\t$('#btn-nuevo, #btn-imprimir, #btn-modificar').removeAttr('onclick');";
        }else{
            $html .= "\n\t\t$('#btn-guardar').attr('disabled',false);";
            $html .= "\n\t\t$('#btn-guardar').removeAttr('onclick');";
        }
        echo $html;
         ?>

        $("table>tbody>tr").on('click', function(e){
            $id = $(this).attr("id");
            $navaluo = '#txtAvaluo'+$id;
            $nvida   = '#txtVida'+$id;
            $nrevel  = '#txtRevelaciones'+$id;

            var vAvaluo = $($navaluo).val();
            var vVida   = $($nvida).val();
            var vRevel  = $($nrevel).val();

            if(vAvaluo.length > 0 && vVida.length > 0) {
                console.log("Guarda");
            }
        });
    </script>
</body>
</html>