<?php
include ('head_listar.php');
include ('Conexion/conexion.php');
include ('funciones/funciones_mov.php');
include_once ("modelAlmacen/salida.php");
$idMov = 0; $tipoMovimiento = ""; $numeroMovimiento = ""; $fecha=date('d/m/Y'); $centroCosto = ""; $proyecto = ""; $dependencia = ""; $responsable = "";
$estado = ""; $descripcion = ''; $observaciones = ''; $id=0; $id=0; $porcIva = '0'; $tercero = 0; $sumC = 0;$sumV = 0; $sumIva = 0; $item=1; $totalmov=0; $iva = ""; $idasoc = "";
$idaso = ""; $tipoAsociado = ""; $id_asoc = ""; $estdo = ""; $sltDocSoporte = ""; $numDocSoporte = "";
$compania = $_SESSION['compania'];
$param = $_SESSION['anno'];
if(!empty($_GET['movimiento'])){
    $idMov = $_GET['movimiento'];
    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia, mv.tercero, mv.estado, mv.descripcion, mv.observaciones, mv.id_unico, mv.porcivaglobal, mv.tipo_doc_sop, mv.numero_doc_sop
            FROM gf_movimiento mv
            WHERE   md5(id_unico)='$idMov'";
    $result = $mysqli->query($sql);
    $rowPrimaria = mysqli_fetch_row($result);
    #Variables de cargado de datos
    $tipoMovimiento = $rowPrimaria[0]; $numeroMovimiento = $rowPrimaria[1]; $fecha = $rowPrimaria[2]; $centroCosto = $rowPrimaria[3]; $proyecto = $rowPrimaria[4];
    $dependencia = $rowPrimaria[5]; $responsable = $rowPrimaria[6]; $estado = $rowPrimaria[7]; $descripcion = $rowPrimaria[8]; $observaciones = $rowPrimaria[9];
    $id = $rowPrimaria[10]; $porcIva = $rowPrimaria[11]; $sltDocSoporte = $rowPrimaria[12]; $numDocSoporte = $rowPrimaria[13];
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

if(empty($estdo)) {
    $sqlE = "SELECT id_unico,nombre FROM gf_estado_movimiento WHERE id_unico = 2";
    $resultE = $mysqli->query($sqlE);
    $rowE = mysqli_fetch_row($resultE);
    $estdo = $rowE[1];
}

if(!empty($_GET['asociado'])) {
    $id_asoc = $_GET['asociado'];
    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia, mv.tercero, mv.rubropptal, mv.estado, mv.plazoentrega,
                    mv.unidadentrega, mv.lugarentrega, mv.descripcion, mv.observaciones, mv.id_unico, mv.porcivaglobal, mv.tercero2, mv.tipo_doc_sop, mv.numero_doc_sop
            FROM    gf_movimiento mv
            WHERE   md5(id_unico)='$id_asoc'";
    $result = $mysqli->query($sql);
    $rowA = mysqli_fetch_row($result);
    $tipoAsociado = $rowA[0]; $fecha = $rowA[2]; $centroCosto = $rowA[3]; $proyecto = $rowA[4]; $dependencia = $rowA[5]; $responsable = $rowA[6]; $rubroPresupuestal = $rowA[7];
    $estado = $rowA[8]; $PlazoEntrega = $rowA[9]; $unidadPlazo = $rowA[10]; $lugarEntrega = $rowA[11]; $descripcion = $rowA[12]; $observaciones = $rowA[13]; $idaso = $rowA[14];
    $id_aso = $rowA[14]; $iva = $rowA[15]; $porcIva = $rowA[15]; $tercero = $rowA[16]; $sltDocSoporte = $rowA[17]; $numDocSoporte = $rowA[18];
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

/**
 * get_id_pro
 *
 * Función para obtener el id del producto al que se relaciona el detalle asociado enviado
 *
 * @author Alexander Numpaque
 * @package Movimiento
 * @param int $id_detalle Id de detalle asociado
 * @return int $producto Id del producto al que se relaciona el detalle
 */
function get_id_pro ($id_detalle) {
    include ('Conexion/conexion.php');
    $producto = 0;
    $sql = "SELECT producto FROM gf_movimiento_producto WHERE detallemovimiento = $id_detalle";
    $result = $mysqli->query($sql);
    if($result == true && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_row($result);
        $producto = $row[0];
    }
    return $producto;
}

$sq_ = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 10";
$rs_ = $mysqli->query($sq_);
$dc_ = $rs_->fetch_array();

$salida = new salida();
 ?>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript" src="js/md5.js" ></script>
    <script type="text/javascript">
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
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFecha").datepicker({changeMonth: true}).val();
        });

        function justNumbers(e){
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
            return true;
            return /\d/.test(String.fromCharCode(keynum));
        }

        $(document).ready(function() {
            var i= 1;
            $('#tablaMovimiento thead th').each( function () {
                if(i >= 1){
                    var title = $(this).text();
                    switch (i){
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            // DataTable
            var table = $('#tablaMovimiento').DataTable({
                "autoFill": true,
                "scrollX": true,
                "pageLength": 5,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });

            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0){
                    $('input', this.header()).on('keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
        });

        $("#btnCerrarModalMov").click(function(){
            document.location.reload();
        });

        function cargarfecha(campo){
            var valor = campo;
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
            //$("#"+valor).datepicker({changeMonth: true}).val();
            $("#"+valor).datepicker({changeYear: true}).val();
            //alert(valor);
        }

        $().ready(function() {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                    //var o = element.attr("id");
                    //document.getElementById(o).style.border = "solid #0000FF";
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
    </script>
    <title>Salida de Almacén</title>
    <style>
        .campos{
            padding: 0px;
            font-size: 10px
        }
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        .shadow {box-shadow: 1px 1px 1px 1px gray;color:#fff; border-color:#1075C1;}
        .form_detalle {
            box-shadow: inset 1px 2px 1px 2px darkgray;
            border-radius: 5px;
        }
        .campoD {
            border-radius: 4px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            line-height: 1.42857143;
            color: #555;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        }
        label #sltTipoAsociado-error, #sltNumeroA-error, #sltTipoMov-error, #txtNumeroMovimiento-error, #txtFecha-error, #sltCentroCosto-error, #sltProyecto-error, #sltDependencia-error,
        #sltResponsable-error, #sltTercero-error, #sltUPE-error, #txtPlazoE-error, #sltLE-error, #txtIva-error, #txtDescripcion-error, #txtObservacion-error, #sltRubroP-error, #txtAsociado-error,
        #sltTipoMovimiento-error {
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;
        }

        body{
            font-size: 10px
        }

        .values {
            cursor:pointer;
        }

        .client-form input[type="number"]{
            width: 100%;
        }
    </style>
</head>
<body onload="clean_inputs()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-lg-10 col-sm-10 text-left">
                <h2 align="center" style="margin-top: 0px" class="tituloform">Salida Almacén</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="controller/controllerGFSalidaAlmacen.php?action=insert" style="margin-bottom: -28px">
                        <div class="form-inline form-group">
                            <p align="center" class="parrafoO" style="margin-bottom: 5px">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>
                            <label class="col-lg-1 col-sm-1 control-label" for="sltTipoAsociado">Tipo Asociado:</label>
                            <select name="sltTipoAsociado" id="sltTipoAsociado" title="Seleccione tipo de asociado" style="width:10%;" class="col-lg-1 col-sm-1 form-control select2">
                                <?php
                                if (!empty($tipoAsociado)) {
                                    $sql1 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) FROM gf_tipo_movimiento WHERE id_unico = $tipoAsociado AND  clase IN(2,6) AND compania = $compania";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="'.$fila1[0].'">'."$fila1[2] ".ucwords(mb_strtolower($fila1[1])).'</option>';
                                    $sql2 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) FROM gf_tipo_movimiento WHERE id_unico != $tipoAsociado AND  clase IN(2,6) AND compania = $compania";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="'.$fila2[0].'">'."$fila2[2] ".ucwords(mb_strtolower($fila2[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Asociado</option>";
                                    $sql3 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) FROM gf_tipo_movimiento WHERE  clase IN(2,6) AND compania = $compania";
                                    $result3 = $mysqli->query($sql3);
                                    while ($fila3 = mysqli_fetch_row($result3)) {
                                        echo '<option value="'.$fila3[0].'">'."$fila3[2] ".ucwords(mb_strtolower($fila3[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label class="col-lg-1 col-sm-1 control-label" for="sltNumeroA">Nro Asociado:</label>
                            <select name="sltNumeroA" id="sltNumeroA" title="Seleccione número de asociado" style="width: 10.2%" class="col-lg-1 col-sm-1 form-control select2">
                                <?php
                                $html = "";
                                if(!empty($id_asoc)) {
                                    $sql_a = "SELECT DISTINCT mv.id_unico, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), tmv.nombre, tmv.sigla
                                              FROM            gf_movimiento mv
                                              LEFT JOIN       gf_detalle_movimiento dtm       ON  dtm.movimiento      = mv.id_unico
                                              LEFT JOIN       gf_tipo_movimiento tmv          ON  mv.tipomovimiento   = tmv.id_unico
                                              WHERE           mv.id_unico = $idaso";
                                    $res_a = $mysqli->query($sql_a);
                                    $fila  = mysqli_num_rows($res_a);
                                    $row_a = $res_a->fetch_all(MYSQLI_NUM);
                                    if ($fila != 0) {
                                        foreach ($row_a as $row) {
                                            $xxa  = array(); $totalD = 0; $totalX = 0;
                                            $sql_ = "SELECT id_unico, (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE movimiento = $row[0]";
                                            $res_ = $mysqli->query($sql_);
                                            $dat_ = $res_->fetch_all(MYSQLI_NUM);
                                            foreach ($dat_ as $row_) {
                                                $xxa[] = $row_[0]; $totalD += $row_[1];
                                            }

                                            for ($i = 0; $i < count($xxa); $i++) {
                                                $sqlr = "SELECT (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $xxa[$i]";
                                                $resr = $mysqli->query($sqlr);
                                                $dtar = $resr->fetch_all(MYSQLI_NUM);
                                                foreach ($dtar as $rowr) {
                                                    $totalX += $rowr[0];
                                                }
                                            }

                                            $suVl =  $totalD - $totalX;
                                            $html .= "<option value=\"$row[0]\">$row[4] $row[1] $row[2] $".number_format($suVl)."</option>";
                                        }

                                    } else {
                                        $html .= '<option value="">Nro Asociado</option>';
                                    }
                                }else{
                                    $html .= '<option value="">Nro Asociado</option>';
                                }
                                echo $html;
                                ?>
                            </select>
                            <label for="sltTipoMovimiento" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tipo Movimiento:</label>
                            <select   name="sltTipoMovimiento" id="sltTipoMovimiento" title="Seleccione tipo de movimiento" style="width:10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($tipoMovimiento)) {
                                    $sql1 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico WHERE tm.id_unico=$tipoMovimiento";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="'.$fila1[0].'">'."$fila1[2] ".ucwords(mb_strtolower($fila1[1])) . '</option>';
                                    $sql2 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
                                                WHERE cl.claseaso=3 AND id_unico != $tipoMovimiento AND compania = $compania";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="'.$fila2[0].'">'."$fila2[2] ".ucwords(mb_strtolower($fila2[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Movimiento</option>";
                                    $sql3 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico WHERE tm.clase = 3 AND compania = $compania";
                                    $result3 = $mysqli->query($sql3);
                                    while ($fila3 = mysqli_fetch_row($result3)) {
                                        echo '<option value="'.$fila3[0].'">'."$fila3[2] ".ucwords(mb_strtolower($fila3[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtNumeroMovimiento" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nro Movimiento:</label>
                            <input type="text" name="txtNumeroMovimiento" id="txtNumeroMovimiento" maxlength="50" style="width:10%" title="Número de movimiento" class="col-lg-1 col-sm-1 form-control" placeholder="N° movimiento"  value="<?php echo $numeroMovimiento; ?>" required readonly/>
                            <label for="txtFecha" class="col-sm-1 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <input type="text" name="txtFecha" id="txtFecha" title="Ingrese la fecha" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Fecha" required readonly value="<?php echo $fecha; ?>" onchange="max_date_type($('#sltTipoMovimiento').val(),this.value)">
                        </div>
                        <div class="form-group" style="margin-top:-15px">
                            <label for="sltCentroCosto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Centro Costo:</label>
                            <select  name="sltCentroCosto" id="sltCentroCosto" class="col-lg-1 col-sm-1 form-control select2" style="width:10%" title="Seleccione centro costo" required>
                                <?php
                                if (!empty($centroCosto)) {
                                    $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $centroCosto AND parametrizacionanno = $param ";
                                    $result4 = $mysqli->query($sql4);
                                    $fila4 = mysqli_fetch_row($result4);
                                    echo '<option value="'.$fila4[0].'">'.ucwords(mb_strtolower($fila4[1])).'</option>';
                                    $sql5 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $centroCosto AND parametrizacionanno = $param";
                                    $result5 = $mysqli->query($sql5);
                                    while ($fila5 = mysqli_fetch_row($result5)) {
                                        echo '<option value="'.$fila5[0].'">'.ucwords(mb_strtolower($fila5[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Centro Costo</option>";
                                    $sql6 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE  parametrizacionanno = $param";
                                    $result6 = $mysqli->query($sql6);
                                    while ($fila6 = mysqli_fetch_row($result6)) {
                                        echo '<option value="'.$fila6[0].'">'.ucwords(mb_strtolower($fila6[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltProyecto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Proyecto:</label>
                            <select name="sltProyecto" id="sltProyecto" style="width:10.2%" class="col-lg-1 col-sm-1 form-control select2" title="Seleccione proyecto" required>
                                <?php
                                if (!empty($proyecto)) {
                                    $sql7    = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto "
                                            . "WHERE id_unico = $proyecto AND compania = $compania";
                                    $sql8 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto "
                                            . "WHERE id_unico != $proyecto AND compania = $compania";
                                } else {
                                    $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto 
                                        WHERE nombre = 'varios' AND compania = $compania";
                                    $sql8 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto "
                                            . "WHERE nombre != 'varios' AND compania = $compania";
                                }
                                $result7 = $mysqli->query($sql7);
                                $fila7 = mysqli_fetch_row($result7);
                                echo '<option value="'.$fila7[0].'">'.ucwords(mb_strtolower($fila7[1])).'</option>';                                
                                $result8 = $mysqli->query($sql8);
                                while ($fila8 = mysqli_fetch_row($result8)) {
                                    echo '<option value="'.$fila8[0].'">'.ucwords(mb_strtolower($fila8[1])).'</option>';
                                }
                                ?>
                            </select>
                            <label for="sltDependencia" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Dependen<br/>cia:</label>
                            <select name="sltDependencia" id="sltDependencia" class="col-lg-1 col-sm-1 form-control select2" style="width:10%;" title="Seleccione dependecia " required>
                                <?php
                                if (!empty($dependencia)) {
                                    $sql9 = "SELECT DISTINCT id_unico,concat(sigla,' ', nombre) FROM gf_dependencia WHERE id_unico = $dependencia AND tipodependencia != 1 AND compania = $compania";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1])).'</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,concat(sigla,' ', nombre) FROM gf_dependencia WHERE id_unico != $dependencia AND tipodependencia != 1 AND compania = $compania";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="'.$fila10[0].'">'.ucwords(mb_strtolower($fila10[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Dependencia</option>";
                                    $sql11 = "SELECT DISTINCT id_unico,concat(sigla,' ', nombre) FROM gf_dependencia WHERE compania = $compania AND tipodependencia != 1";
                                    $result11 = $mysqli->query($sql11);
                                    while ($fila11 = mysqli_fetch_row($result11)) {
                                        echo '<option value="'.$fila11[0].'">'.ucwords(mb_strtolower($fila11[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltResponsable" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Respon<br/>sable:</label>
                            <select name="sltResponsable" id="sltResponsable"  title="Seleccione responsable"  style="width:10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($responsable)) {
                                    $sql12 = "SELECT    IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                                        OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,  (ter.razonsocial),
                                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                                        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_dependencia_responsable dpr
                                              LEFT JOIN gf_tercero ter              ON dpr.responsable = ter.id_unico
                                              LEFT JOIN gf_tipo_identificacion ti   ON ti.id_unico = ter.tipoidentificacion
                                              WHERE     ter.id_unico = $responsable AND ter.compania = $compania";
                                    $result12 = $mysqli->query($sql12);
                                    $fila12 = mysqli_fetch_row($result12);
                                    echo '<option value="' . $fila12[1] . '">' . ucwords(strtolower($fila12[0])) . '</option>';
                                    $sql5 = "SELECT DISTINCT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                                              OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                                              CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                                              ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_dependencia_responsable dpr
                                             LEFT JOIN        gf_tercero ter                 ON dpr.responsable = ter.id_unico
                                             LEFT JOIN        gf_tipo_identificacion ti      ON ti.id_unico = ter.tipoidentificacion
                                             LEFT JOIN        gf_dependencia_responsable dtr ON dtr.responsable = ter.id_unico
                                             WHERE            ter.id_unico != $responsable   AND ter.compania = $compania AND dtr.dependencia = $dependencia";
                                    $result5 = $mysqli->query($sql5);
                                    while ($fila5 = mysqli_fetch_row($result5)) {
                                        echo '<option value="' . $fila5[1] . '">' . ucwords(strtolower($fila5[0])) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Responsable</option>';
                                }
                                ?>
                            </select>
                            <label for="txtEstado" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Estado :</label>
                            <input type="text" name="txtEstado" id="txtEstado" title="Estado" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Estado" required value="<?php echo $estdo; ?>">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="txtDescripcion" class="col-lg-1 col-sm-1 control-label">Descrip<br/>ción:</label>
                            <textarea name="txtDescripcion" id="txtDescripcion" title="Ingrese descripción" style="margin-top: 0px;width: 28.2%;height: 50px" class="col-lg-1 col-sm-1 form-control" placeholder="Descripción" rows="5" cols="1000"><?php echo $descripcion; ?></textarea>
                            <label for="txtObservacion" class="col-lg-1 col-sm-1 control-label">Observa<br/>ciones:</label>
                            <textarea name="txtObservacion" id="txtObservacion" title="Ingrese observaciones" style="margin-top: 0px;width: 28.5%;height: 50px" class="col-lg-1 col-sm-1 form-control" placeholder="Observaciones" rows="5" cols="1000"><?php echo $observaciones; ?></textarea>
                            <label for="sltBuscar" class="col-lg-1 col-sm-1 control-label">Buscar Movimiento:</label>
                            <select name="sltBuscar" id="sltBuscar" title="Seleccione para buscar movimiento" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2 buscar">
                                <?php
                                echo "<option value=\"\">Buscar Movimiento</option>";
                                ECHO $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                                    IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                                        FROM        gf_movimiento mov
                                        LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                                        LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                                        WHERE       tpm.clase               =  3
                                        AND         mov.compania            = $compania
                                        AND         mov.parametrizacionanno = $param order by mov.numero desc";
                                $result = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($result)) {
                                    $valorDetalle = 0;
                                    $sql_r1 = "SELECT (dtm.valor + dtm.iva) * dtm.cantidad FROM gf_detalle_movimiento dtm WHERE dtm.movimiento = $row[0]";
                                    $res_r1 = $mysqli->query($sql_r1);
                                    $dta_r1 = $res_r1->fetch_all(MYSQLI_NUM);
                                    foreach ($dta_r1 as $row_r) {
                                        $valorDetalle += $row_r[0];
                                    }
                                    echo "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($valorDetalle,2,',','.')."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-11 col-lg-1 col-lg-offset-11" style="margin-top: -170px">
                                <a id="btnNuevo" title="Ingresar nuevo" class="btn btn-primary shadow glyphicon glyphicon-plus text-center nuevo" onclick="javascript:nuevo()" style="margin-bottom: 5px"></a>
                                <button type="submit" id="btnGuardar" title="Guardar movimiento" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk guardar" style="margin-bottom: 5px"></button>
                                <a id="btnImprimir" title="Imprimir" class="btn btn-primary shadow glyphicon glyphicon glyphicon-print imprimir" onclick="open_report(<?php echo $id ?>)" style="margin-bottom: 5px"></a>
                                <a id="btnModificar" title="Modificar movimiento" class="btn btn-primary shadow glyphicon glyphicon-pencil modificar" onclick="modify_data()"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            if(!empty($_GET['movimiento'])) {
                echo "\n\t<script>";
                echo "$(\"#btnGuardar\").attr('disabled',true);";
                echo "$(\"#btnGuardar\").removeAttr('onclick');";
                echo " \n\t</script>";
            }else{
                echo "\n\t<script>";
                echo "$(\"#btnNuevo, #btnModificar, #btnImprimir, #btnGuardarDetalle\").attr('disabled',true);";
                echo "$(\"#btnNuevo, #btnModificar, #btnImprimir, #btnGuardarDetalle\").removeAttr('onclick');";
                echo " \n\t</script>";
            }

            if(!empty($_GET['asociado'])) {
                echo "\n\t<script>";
                echo "$(\"#btnNuevo\").attr('disabled',false);";
                echo "$(\"#btnNuevo\").click(function(){nuevo();})";
                echo " \n\t</script>";
            }
            ?>
            <div class="col-lg-10 col-md-10 col-sm-10 text-left" style="margin-top: 5px; margin-bottom: -20px">
                <div class="client-form">
                    <form name="form-detalle" id="form-detalle" class="form-horizontal" method="POST" enctype="multipart/form-data" action="access.php?controller=salida&action=guardar_detalle">
                        <input type="hidden" name="id_mov" value="<?php echo $idMov ?>">
                        <div class="form-group">
                            <label class="control-label col-sm-1 col-md-1 col-lg-1 text-left" for=""><strong class="obligado">*</strong>Elemento:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltElemento" id="sltElemento" class="form-control select2" required="" title="Selecciona un elemento del inventario">
                                    <?php
                                    $html  = "";
                                    $html .= "<option value=\"\">Elemento</option>";
                                    $dta_e = $salida->obtnerElementosInventario();
                                    foreach ($dta_e as $row_e) {
                                        $xe = $salida->obtnerCantidadProductosPlan($row_e[0]);
                                        $xs = $salida->obtnerCantidadProductosPlanSalida($row_e[0]);
                                        $xx = $xe - $xs;
                                        if($xx > 0){
                                            $html .= "<option value=\"$row_e[0]\">$row_e[1]</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1 text-left" for=""><strong class="obligado">*</strong>Cantidad:</label>
                            <div class="col-sm-1 col-md-1 col-lg-1">
                                <input type="number" name="txtCantI" id="txtCantI" value="" required="" class="form-control" placeholder="Cant." title="Ingrese la cantidad del producto">
                                <input type="hidden" name="txtCnt" id="txtCnt" value="">
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1 text-left" for=""><strong class="obligado">*</strong>Valor U:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="number" name="txtValorU" id="txtValorU" value="" required="" class="form-control" placeholder="Valor U." title="Ingrese el valor unitario del producto" step="0.0000000000000000001">
                            </div>
                            <label class="control-label col-sm-1 col-md-1 col-lg-1 text-left" for=""><strong class="obligado">*</strong>Valor Total:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="number" name="txtValorT" id="txtValorT" required="" class="form-control" value="" readonly placeholder="Valor Total" title="Valor total">
                            </div>
                            <button id="btn-detalle" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </div>
                    </form>
                </div>
            </div>
            <input type="hidden" id="idPrevio" value="">
            <input type="hidden" id="idActual" value="">
            <div class="col-lg-10 col-sm-10 text-left" style="margin-top: 10px">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table id="tablaMovimiento" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza"><strong>Item</strong></td>
                                    <td class="cabeza"><strong>Plan Inventario</strong></td>
                                    <td class="cabeza"><strong>Cantidad</strong></td>
                                    <td class="cabeza"><strong>Valor Aproximado</strong></td>
                                    <td class="cabeza"><strong>Iva</strong></td>
                                    <td class="cabeza"><strong>Valor Total</strong></td>
                                    <td style="width:10px"></td>
                                </tr>
                                <tr>
                                    <th width="7%"  class="cabeza"></th>
                                    <th class="cabeza"><strong>Item</strong></th>
                                    <th class="cabeza"><strong>Plan Inventario</strong></th>
                                    <th class="cabeza"><strong>Cantidad</strong></th>
                                    <th class="cabeza"><strong>ValorAproximado</strong></th>
                                    <th class="cabeza"><strong>Iva</strong></th>
                                    <th class="cabeza"><strong>Valor Total</strong></th>
                                    <th class="cabeza" style="width: 10px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $item=1; $totalmov=0;
                                if(!empty($idMov) && empty($_GET['asociado'])) {
                                    $det = "SELECT    dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva, pl.codi,
                                                      pl.ficha, dtm.detalleasociado, pl.tipoinventario
                                            FROM      gf_detalle_movimiento dtm
                                            LEFT JOIN gf_movimiento mv      ON dtm.movimiento = mv.id_unico
                                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                            WHERE     md5(mv.id_unico)='$idMov'";
                                    $resultado2 = $mysqli->query($det);
                                    while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                                        <tr>
                                            <td>
                                                <a href="#<?php echo $row2[0];?>" class="glyphicon glyphicon-trash eliminar" title="Eliminar" onclick="javascript:delete_detail(<?php echo $row2[0];?>);"></a>
                                                <a href="#<?php echo $row2[0];?>" class="glyphicon glyphicon-edit modificar" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row2[0]; ?>);return calcular(<?php echo $row2[0]; ?>)"></a>
                                            </td>
                                            <td class="text-left campos" width="7%">
                                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblItem'.$row2[0].'">'.$item++.'</label>'; ?>
                                            </td>
                                            <td class="text-left campos">
                                                <?php  echo '<label  class="valorLabel" style="font-weight:normal" id="lblCodigoE'.$row2[0].'">'.$row2[7].' - '.ucwords(mb_strtolower($row2[5])).'</label>'; ?>
                                                <select class="col-sm-12 campoD" name="sltPlanInventario<?php echo $row2[0] ?>" id="sltPlanInventario<?php echo $row2[0] ?>" title="Seleccione elemento de plan inventario" style="display:none;padding:2px">
                                                    <?php
                                                    
                                                     echo '<option value="'.$row2[4].'">'.$row2[7].' - '.ucwords(mb_strtolower($row2[5])).'</option>';
                                                     $sqlPL = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento=2 AND id_unico!=$row2[4] AND compania = $compania ";
                                                     $resultPL = $mysqli->query($sqlPL);
                                                     while ($filaPL = mysqli_fetch_row($resultPL)){
                                                         echo '<option value="'.$filaPL[0].'">'.$filaPL[2].' - '.$filaPL[1].'</option>';
                                                     }
                                                    ?>
                                                </select>
                                            </td>
                                            <td class="text-right campos">
                                                <?php
                                                $sumC+=$row2[2];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row2[0].'">'.$row2[2].'</label>';
                                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" onkeyup="validar_cant('.$row2[0].')" name="txtcantidad'.$row2[0].'" id="txtcantidad'.$row2[0].'" value="'.$row2[2].'" />';
                                                echo '<input type="hidden" name="txtCantX'.$row2[0].'" id="txtCantX'.$row2[0].'" value="'.$row2[2].'">';
                                                ?>
                                            </td>
                                            <td class="text-right campos">
                                                <?php
                                                $sumV+=$row2[3];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="ValorT'.$row2[0].'">'.number_format($row2[3],2,',','.').'</label>';
                                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtvalor'.$row2[0].'" id="txtvalor'.$row2[0].'" value="'.$row2[3].'" />';
                                                ?>
                                            </td>
                                            <td class="text-right campos">
                                                <?php
                                                $sumIva +=$row2[6];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row2[0].'">'.number_format($row2[6],2,',','.').'</label>';
                                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="number" name="txtiva'.$row2[0].'" id="txtiva'.$row2[0].'" value="'.$row2[6].'" readonly/>';
                                                ?>
                                            </td>
                                            <td style="height:10px;font-size:10px" class="text-right campos">
                                                <?php
                                                if($row2[2] == '0') {
                                                    $mov = 0;
                                                    $total = 0;
                                                    $totalmov += 0;
                                                }else{
                                                    $total = ($row2[3] + $row2[6]) * $row2[2];
                                                    $totalmov+=$total;
                                                }
                                                echo '<label class="valorLabel" style="font-weight:normal" id="lblValorTotal'.$row2[0].'">'.number_format($total, 2, ',', '.').'</label>';
                                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-9 campoD text-left"  type="number" name="txttotal'.$row2[0].'" id="txttotal'.$row2[0].'" value="'.$total.'" readonly/>';
                                                 ?>
                                                <div >
                                                    <table id="tab<?php echo $row2[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-sm-1">
                                                        <tbody>
                                                            <tr style="background-color:transparent;">
                                                                <td style="background-color:transparent;">
                                                                    <a  href="#<?php echo $row2[0];?>" title="Guardar" id="guardar<?php echo $row2[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row2[0]; ?>)">
                                                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                                                    </a>
                                                                </td>
                                                                <td style="background-color:transparent;">
                                                                    <a href="#<?php echo $row2[0];?>" title="Cancelar" id="cancelar<?php echo $row2[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row2[0];?>)" >
                                                                        <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                            <td class="campos text-center" style="width: 10px">
                                                <?php
                                                if($row2[10] == 2) {
                                                    $producto = get_id_pro($row2[9]);
                                                    echo '<a id="plan'.$row2[0].'" onclick="return abrirModalSalida('.$row2[8].','.$row2[0].','.$row2[2].','.$row2[9].','.$producto.')" href="javascript:void(0)" title="Ficha Inventario" data-backdrop="static" "data-keyboard="false" data-toggle="modal"><i class="glyphicon glyphicon-blackboard"></i></a>';
                                                }else{
                                                    echo '<i class="glyphicon glyphicon-blackboard ficha" title="Ficha Inventario"></li>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                <?php }
                                }

                                $html = "";
                                if(!empty($_GET['asociado'])){
                                    $sq_ = "SELECT    dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, CONCAT_WS(' - ',pl.codi, UPPER(pl.nombre)), dtm.iva, pl.ficha
                                            FROM      gf_detalle_movimiento dtm
                                            LEFT JOIN gf_movimiento mv      ON dtm.movimiento = mv.id_unico
                                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                            WHERE     md5(mv.id_unico) = '$id_asoc'";
                                    $re_ = $mysqli->query($sq_);
                                    $dt_ = $re_->fetch_all(MYSQLI_NUM);
                                    foreach ($dt_ as $ro_) {
                                        $xc = 0;
                                        $sq_c = "SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $ro_[0]";
                                        $re_c = $mysqli->query($sq_c);
                                        $ro_c = $re_c->fetch_all(MYSQLI_NUM);
                                        foreach($ro_c as $r){
                                            $xc += $r[0];
                                        }
                                        $xxx = $ro_[2] - $xc;
                                        if( $xxx > 0){
                                            $sumV     += $ro_[3];
                                            $sumIva   += $ro_[6];
                                            $totalmov += ($ro_[3] + $ro_[6]) * $xxx;
                                            $html .= "<tr>";
                                            $html .= "<td width='7%'></td>";
                                            $html .= "<td class='text-left campos' width='7%'>$item</td>";
                                            $html .= "<td class='campos text-left'>$ro_[5]</td>";
                                            $html .= "<td class='campos text-right'>$xxx</td>";
                                            $html .= "<td class='campos text-right'>".number_format($ro_[3], 2, ',', '.')."</td>";
                                            $html .= "<td class='campos text-right'>".number_format($ro_[6], 2, ',', '.')."</td>";
                                            $html .= "<td class='campos text-right'>".number_format(($ro_[3] + $ro_[6]) * $ro_[2], 2, ',', '.')."</td>";
                                            $html .= "<td class='campos text-right'></td>";
                                            $html .= "</tr>";
                                        }
                                    }
                                }
                                echo $html;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 col-sm-10 text-right">
                <label for="" class="control-label col-lg-1 col-sm-1 col-sm-offset-4">Totales :</label>
                <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Valor Aproximado"><?php echo "$".number_format($sumV,2,',','.'); ?></label>
                <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Iva"><?php echo "$".number_format($sumIva,2,',','.') ?></label>
                <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Valor Total"><?php echo "$".number_format($totalmov,2,',','.')?></label>
            </div>
        </div>
        <?php
        if(!empty($id_aso)) {
            echo "<script>$(\".eliminar\").css('display','none');$(\".modificar\").css('display','none');$(\".ficha\").css('display','none');</script>";
        }
        ?>
        <script src="js/bootstrap.min.js"></script>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Salida de Almacén?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales de modificado -->
        <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalTipo" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione tipo de movimiento.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnD" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalGuardar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información guardada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnGuardado" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal para informar al usuario que no se ha poodido registrar la informacion-->
        <div class="modal fade" id="ModalNoGuardar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido guardar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(empty($porcIva)){
        $porcIva = 0;
    }?>
    <?php require_once 'footer.php' ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript">
        $(".select2").select2();
        $("#sltPlanInv").select2();

        function redondeo(valor, decimales){
            var flt = parseFloat(valor);
            var res = Math.round(flt * Math.pow(10, decimales)) / Math.pow(10, decimales);
            return res;
        }
        /**
         * Cuando el combo cambie su valor, redireccionara y traera los datos del asociado
         */
        $("#sltNumeroA").change(function () {
            var form_data = {
                mov:11,
                id:+$("#sltNumeroA").val()
            };
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    window.location = data;
                }
            }).error(function(data, textStatus, jqXHR) {
                console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
            });
        });

        /**
         * Cargue de consecutivo de numero asociado
         */
        $("#sltTipoMovimiento").change(function () {
            var form_data = {
                mov:2,
                tipo:$("#sltTipoMovimiento").val()
            };
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#txtNumeroMovimiento").val(data);
                }
            }).error(function(data, textStatus, jqXHR) {
                console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
            });
        });

        /**
         *Cargue de asociado
         */
        $("#sltTipoAsociado").change(function(){
            var form_data = {
                mov:9,
                tipo:+$("#sltTipoAsociado").val()
            };
            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consulta_mov.php",
                data:form_data,
                success: function (data) {
                    $("#sltNumeroA").html(data).fadeIn();
                    $("#sltNumeroA").css('display','none');
                }
            });
        });

        /**
         * Cargue de responsable por dependencia
         */
        $("#sltDependencia").change(function(){
            var form_data={
                existente:5,
                dependencia:$("#sltDependencia").val()
            };

            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consultarNumeros.php",
                data:form_data,
                success: function (data) {
                    $("#sltResponsable").html(data).fadeIn();
                    $("#sltResponsable").css('display','none');
                }
            });
        });

        $("#sltBuscar").change(function(e) {
            if($("#sltBuscar").val().length > 0){
                id = $("#sltBuscar").val();
                window.location = "registrar_GR_SALIDA_ALMACEN.php?movimiento="+md5(id);
            }
        });

        function nuevo() {
            window.location = 'registrar_GR_SALIDA_ALMACEN.php';
        }

        function modify_data() {
            var form_data = {
                action: 'modify',
                txtFecha:$("#txtFecha").val(),
                txtDescripcion:$("#txtDescripcion").val(),
                txtObservacion:$("#txtObservacion").val(),
                sltResponsable: $("#sltResponsable").val(),
                sltProyecto: $("#sltProyecto").val(),
                id:<?php echo $id; ?>,
                txtIva:<?php echo $porcIva ?>
            };
            var result = '';
            $.ajax({
                type:'POST',
                url: "controller/controllerGFSalidaAlmacen.php",
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#mdlModificado").modal('show');
                    }else{
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }

        function modificar(id){
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                //Labels
                var lblCantidadE = 'lblCantidad'+$("#idPrevio").val();
                var ValorTE = 'ValorT'+$("#idPrevio").val();
                var lblIvaE = 'lblIva'+$("#idPrevio").val();
                var lblValorTotalE = 'lblValorTotal'+$("#idPrevio").val();

                //Campos para cancelar y guardar cambios
                var guardarC = 'guardar'+$("#idPrevio").val();
                var cancelarC = 'cancelar'+$("#idPrevio").val();
                var tablaC = 'tab'+$("#idPrevio").val();

                //Campos ocultos
                var txtCantidadE = 'txtcantidad'+$("#idPrevio").val();
                var txtValorE = 'txtvalor'+$("#idPrevio").val();
                var txtIvaE = 'txtiva'+$("#idPrevio").val();
                var txtValorTE = 'txttotal'+$("#idPrevio").val();
                //Se mustran los labels
                $("#"+lblCantidadE).css('display','block');
                $("#"+ValorTE).css('display','block');
                $("#"+lblIvaE).css('display','block');
                $("#"+lblValorTotalE).css('display','block');

                //Se ocultan los campos
                $("#"+txtCantidadE).css('display','none');
                $("#"+txtValorE).css('display','none');
                $("#"+txtIvaE).css('display','none');
                $("#"+txtValorTE).css('display','none');

                //se mantienen ocultos
                $("#"+guardarC).css('display','none');
                $("#"+cancelarC).css('display','none');
                $("#"+tablaC).css('display','none');
            }
            //Labels
            var lblCantidad  = 'lblCantidad'+id;
            var ValorT  = 'ValorT'+id;
            var lblIva  = 'lblIva'+id;
            var lblValorTotal  = 'lblValorTotal'+id;

            //campos
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;
            var txtValorT = 'txttotal'+id;

            //campos para cancelar y guardar cambios
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;

            //Se ocultan los labels
            $("#"+lblCantidad).css('display','none');
            $("#"+ValorT).css('display','none');
            $("#"+lblIva).css('display','none');
            $("#"+lblValorTotal).css('display','none');

            //Se muestran los campos ocultos
            $("#"+txtCantidad).css('display','block');
            $("#"+txtValor).css('display','block');
            $("#"+txtIva).css('display','block');
            $("#"+txtValorT).css('display','block');

            //Se muestran los campos
            $("#"+guardar).css('display','block');
            $("#"+cancelar).css('display','block');
            $("#"+tabla).css('display','block');

            //Carga de la id actual
            $("#idActual").val(id);

            //carga del campo oculto con la id anterior
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);
            }
        }

        function cancelar(id){
            //labels
            var lblCantidad  = 'lblCantidad'+id;
            var ValorT  = 'ValorT'+id;
            var lblIva  = 'lblIva'+id;
            var lblValorTotal  = 'lblValorTotal'+id;
            //campos
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;
            var txtValorT = 'txttotal'+id;
            //campos para cancelar y guardar cambios
            var guardar = 'guardar'+id;
            var cancelar = 'cancelar'+id;
            var tabla = 'tab'+id;
            //se muestran los labels
            $("#"+lblCantidad).css('display','block');
            $("#"+ValorT).css('display','block');
            $("#"+lblIva).css('display','block');
            $("#"+lblValorTotal).css('display','block');
            //Se ocultan los campos
            $("#"+txtCantidad).css('display','none');
            $("#"+txtValor).css('display','none');
            $("#"+txtIva).css('display','none');
            $("#"+txtValorT).css('display','none');
            //Se ocultan los campos
            $("#"+guardar).css('display','none');
            $("#"+cancelar).css('display','none');
            $("#"+tabla).css('display','none');
        }

        function guardarCambios(id){
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;

            var form_data = {
                action:'modify_detail',
                txtCantidad:+$("#"+txtCantidad).val(),
                txtValor:+$("#"+txtValor).val(),
                txtValorIva:+$("#"+txtIva).val(),
                id_unico:id
            };
            var result = '';

            $.ajax({
                type:'POST',
                url:'controller/controllerGFMovimiento.php',
                data:form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#mdlEliminado").modal('show');
                    }else{
                        $("#mdlNoeliminado").modal('show');
                    }
                }
            });
        }

        function delete_detail (id_unico) {
            var form_data = {
                action:'delete_detail',
                id:id_unico
            };

            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $.ajax({
                    type:'POST',
                    url:'controller/controllerGFMovimiento.php',
                    data:form_data,
                    success: function (data) {
                        console.log(data);
                        result = JSON.parse(data);
                        if(result == true) {
                            $("#mdlEliminado").modal('show');
                        }else{
                            $("#mdlNoeliminado").modal('show');
                        }
                    }
                });
            });
        }

        function calcular(id){
            var txtCantidad = 'txtcantidad'+id;
            var txtValor    = 'txtvalor'+id;
            var txtIva      = 'txtiva'+id;
            var txtValorT   = 'txttotal'+id;

            $("#"+txtValor).keyup(function(){
                var cantidad = parseFloat($("#"+txtCantidad).val());
                if(cantidad == 0 || cantidad == "" || cantidad.length == 0 || cantidad == null){
                    cantidad = 1;
                }else{
                    cantidad = parseFloat($("#"+txtCantidad).val());
                }
                valor    = parseFloat($("#"+txtValor).val());
                iva      = parseFloat(<?php echo $porcIva; ?>);
                totalIva = redondeo((valor*iva)/100, <?php echo $dc_[0] ?>);
                total    = (valor + totalIva) * cantidad;

                $("#"+txtIva).val(totalIva);
                $("#"+txtValorT).val(total);
            });

            $("#"+txtCantidad).keyup(function(){
                var cantidad = parseFloat($("#"+txtCantidad).val());
                if(cantidad == 0 || cantidad == "" || cantidad.length == 0 || cantidad == null){
                    cantidad = 1;
                }else{
                    cantidad = parseFloat($("#"+txtCantidad).val());
                }
                valor    = parseFloat($("#"+txtValor).val());
                iva      = parseFloat(<?php echo $porcIva; ?>);
                totalIva = redondeo((valor * iva) / 100, <?php echo $dc_[0] ?>);
                total    = (valor + totalIva) * cantidad;

                $("#"+txtIva).val(totalIva);
                $("#"+txtValorT).val(total);
            });
        }

        function reload_page() {
            window.location.reload();
        }

        $("#btnD").click(function(){
            $("#sltTipoMovimiento").focus();
        });


        $("#btnCerrarModalMov").click(function(){
            document.location.reload();
        });

        function abrirModalSalida(ficha,detalle,cantidad,asociado, producto){
            var form_data = {
                ficha:ficha,
                detalle:detalle,
                cantidad:cantidad,
                asociado:asociado,
                producto:producto
            };

            $.ajax({
                type: 'POST',
                url: "modalSalidaProducto.php#modalSalidaProducto",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalSalidaProducto").html(data);
                    $('.salida').modal({backdrop: 'static', keyboard: false,show:true});
                }
            });
        }

        function calcule_values(id) {
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;
            var txtTotal = 'txttotal'+id;

            var cantidad = parseFloat($("#"+txtCantidad).val());
            var valor = parseFloat($("#"+txtValor).val());
            var iva = parseFloat(<?php echo $porcIva; ?>);

            if (cantidad === 0 || cantidad === "" || cantidad.length === 0 || cantidad === null) {
                cantidad = 0;
            }else {
                cantidad = parseFloat($("#"+txtCantidad).val());
            }

            var totalIva = redondeo((valor * iva) / 100, <?php echo $dc_[0] ?>);
            var total = (valor + totalIva) * cantidad;
            $("#"+txtIva).val(totalIva);
            $("#"+txtTotal).val(total);
        }

        function clean_inputs (){
            $("#sltPlanInv").select2('val','');
            $("#txtCantidad").val("");
            $("#txtValor").val("");
            $("#txtValorIva").val("");
            $("#txtValorTotal").val("");
        }

        /**
         * max_date_type
         *
         * Función para validar que la fecha no sea menor a la fecha anterior
         */
        function max_date_type (type, date) {
            var form_data = {
                newDate:date,
                type:type,
                mov:4
            };
            var result = '';
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    result = JSON.parse(data);
                    if(result == true) {
                        $("#mdlfecha").modal('show');
                        $("#txtFecha").val("");
                    }
                }
            });
        }

        /**
         * open_report
         * @param int mov Id de movimiento
         */
        function open_report(mov) {
            if(!isNaN(mov)) {
                window.open('informes_almacen/inf_salida_almacen.php?mov='+md5(mov));
            }
        }

        $("#sltElemento").change(function(e){
            var elemento = $(this).val();
            $.getJSON("access.php?controller=salida&action=obtnerCantidadPlan&sltElemento="+elemento, function(data, textStatus, jqXHR){
                $("#txtCantI").val(data);
                $("#txtCnt").val(data);
                if(data == 0){
                    $("#btn-detalle").attr('disabled', true);
                }else{
                    $("#btn-detalle").attr('disabled', false);
                }
            });

            $.getJSON("access.php?controller=salida&action=obnterValorU&sltElemento="+elemento, function(data, textStatus, jqXHR){
                $("#txtValorU").val(data);
            });
        });

        $("#txtValorU, #txtValorT, #txtCantI").on('keyup',function(e){
            var cantidad = parseFloat($("#txtCantI").val());
            var valorP   = parseFloat($("#txtValorU").val());
            $("#txtValorT").val(valorP * cantidad);
        });

        $("#txtCantI").on('keyup', function(e) {
            $cantT = parseFloat($("#txtCnt").val());
            $cantX = $(this).val();
            if($cantX != 0 || $cantX != ""){
                if($cantX > $cantT){
                    $("#btn-detalle").attr('disabled', true);
                }else{
                    $("#btn-detalle").attr('disabled', false);
                }
            }else{
                $("#btn-detalle").attr('disabled', true);
            }
        });

        <?php
        $html = "";
        if(!empty($_GET['asociado'])){
            $html .= "\n$('#form-detalle').css('display', 'none');";
        }

        if(empty($_GET['movimiento'])){
            $html .= "\n$('#form-detalle').css('display', 'none');";
        }
        echo $html;
       ?>

       function validar_cant($x){
            $cant = parseFloat($('#txtCantX'+$x).val());
            $cantx  = parseFloat($('#txtcantidad'+$x).val());

            if($cantx != "0" || $cantx != "" || !isNaN($cantx)){
                if($cantx > $cant){
                    $("#guardar"+$x).css('display', 'none');
                }else{
                    $("#guardar"+$x).css('display', 'block');
                }
            }else{
                $("#guardar"+$x).css('display', 'none');
            }
       }
    </script>
    <?php require_once 'modalSalidaProducto.php'; ?>
</body>
</html>