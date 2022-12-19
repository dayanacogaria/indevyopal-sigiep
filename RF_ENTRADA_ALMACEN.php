<?php
include ('head_listar.php');
include ('Conexion/conexion.php');
include ('funciones/funciones_mov.php');

$compania = $_SESSION['compania'];
$param    = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$idMov          = 0;  $tipoMovimiento    = ""; $numeroMovimiento = ""; $fecha   = date('d/m/Y'); $centroCosto = ""; $proyecto     = ""; $dependencia = "";
$responsable    = ""; $rubroPresupuestal = ""; $PlazoEntrega     = ""; $estado  = "";            $unidadPlazo = ""; $lugarEntrega = ""; $descripcion = '';
$observaciones  = ''; $id                = 0;  $porcIva          = '0';$tercero = 0;             $sumC        = 0;  $sumV         = 0;  $sumIva      = 0;
$item           = 1;  $totalmov          = 0;  $iva              = ""; $idasoc  = "";            $idaso       = ""; $tipoAsociado = ""; $id_asoc     = "";
$tipoDocSoporte = ""; $numDocSoporte     = "";
$iddMov =0;
if(!empty($_GET['movimiento'])){

    $idMov = $_GET['movimiento'];

    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia,
                    mv.tercero, mv.rubropptal, mv.estado, mv.plazoentrega,mv.unidadentrega, 
                    mv.lugarentrega, mv.descripcion,
                    mv.observaciones, mv.id_unico, mv.tercero2, porcivaglobal, 
                    mv.tipo_doc_sop, mv.numero_doc_sop, mv.id_unico 
            FROM    gf_movimiento mv
            WHERE   md5(id_unico) = '$idMov'";
    $result = $mysqli->query($sql);
    $rowPrimaria = mysqli_fetch_row($result);
    $iddMov =$rowPrimaria[19];
    $tipoMovimiento = $rowPrimaria[0];  $numeroMovimiento = $rowPrimaria[1];  $fecha          = $rowPrimaria[2];  $centroCosto       = $rowPrimaria[3];
    $proyecto       = $rowPrimaria[4];  $dependencia      = $rowPrimaria[5];  $responsable    = $rowPrimaria[6];  $rubroPresupuestal = $rowPrimaria[7];
    $estado         = $rowPrimaria[8];  $PlazoEntrega     = $rowPrimaria[9];  $unidadPlazo    = $rowPrimaria[10]; $lugarEntrega      = $rowPrimaria[11];
    $descripcion    = $rowPrimaria[12]; $observaciones    = $rowPrimaria[13]; $id             = $rowPrimaria[14]; $tercero           = $rowPrimaria[15];
    $porcIva        = $rowPrimaria[16]; $iva              = $rowPrimaria[16]; $tipoDocSoporte = $rowPrimaria[17]; $numDocSoporte     = $rowPrimaria[18];
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd  = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

if(!empty($_GET['asociado'])) {

    $id_asoc = $_GET['asociado'];

    $sql     = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia,
                        mv.tercero, mv.rubropptal, mv.estado, mv.plazoentrega, mv.unidadentrega, mv.lugarentrega, mv.descripcion,
                        mv.observaciones, mv.id_unico, mv.porcivaglobal, mv.tercero2, mv.tipo_doc_sop, mv.numero_doc_sop
                FROM    gf_movimiento mv
                WHERE   md5(id_unico)='$id_asoc'";
    $result = $mysqli->query($sql);
    $rowA   = mysqli_fetch_row($result);

    $tipoAsociado      = $rowA[0];  $fecha         = $rowA[2];  $centroCosto  = $rowA[3];  $proyecto    = $rowA[4];
    $rubroPresupuestal = $rowA[7];  $estado        = $rowA[8];  $PlazoEntrega = $rowA[9];  $unidadPlazo = $rowA[10]; $lugarEntrega = $rowA[11]; $descripcion = $rowA[12];
    $observaciones     = $rowA[13]; $idaso         = $rowA[14]; $id_aso       = $rowA[14]; $iva         = $rowA[15]; $porcIva      = $rowA[15]; $tercero     = $rowA[16];
    $tipoDocSoporte    = $rowA[17]; $numDocSoporte = $rowA[18];

    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd  = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

if(empty($estdo)) {
    $sqlE    = "SELECT id_unico,nombre FROM gf_estado_movimiento WHERE id_unico = 2";
    $resultE = $mysqli->query($sqlE);

    $rowE  = mysqli_fetch_row($resultE);
    $estdo = $rowE[1];
}
$sq_ = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 10";
$rs_ = $mysqli->query($sq_);
$dc_ = $rs_->fetch_array();
define("dec", $dc_['valor']);
?>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-float-label.css">
    <script src="dist/jquery.validate.js"></script>
    <script type="text/javascript" src="js/md5.js" ></script>
    <script type="text/javascript">
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
                if(i >= 1) {
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
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
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
    <title>Entrada de Almacén</title>
    <style>
        .campos{
            padding: 0px;
            font-size: 10px
        }

        table.dataTable thead th,
        table.dataTable thead td{
            padding:1px 18px;
            font-size:9px
        }

        table.dataTable tbody td,
        table.dataTable tbody td{
            padding:1px;
        }

        .dataTables_wrapper .ui-toolbar{
            padding:2px;
        }

        .shadow {
            box-shadow: 1px 1px 1px 1px gray;
            color:#fff;
            border-color:#1075C1;
        }

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

        label #sltTipoAsociado-error, #sltNumeroA-error, #sltTipoMovimiento-error, #txtNumeroMovimiento-error, #txtFecha-error, #sltCentroCosto-error, #sltProyecto-error, #sltDependencia-error,
        #sltResponsable-error, #sltTercero-error, #sltUPE-error, #txtPlazoE-error, #sltLE-error, #txtIva-error, #txtDescripcion-error, #txtObservacion-error, #sltRubroP-error, #txtAsociado-error,
        #txtNumDocS-error, #sltDocSoporte-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }

        body{
            font-size: 10px
        }

        .values {
            cursor:pointer;
        }
    </style>
</head>
<body onload="clean_inputs()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-lg-10 col-md-10 col-sm-10 text-left">
                <h2 align="center" style="margin-top:0px" class="tituloform">Entrada Almacén</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFEntradaAlmacen.php?action=insert" onsubmit="return validar()" style="margin-bottom: -30px">
                        <div class="form-group">
                            <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <label class="col-lg-1 col-md-1 col-sm-1 control-label" for="sltTipoAsociado">Tipo Asociado:</label>
                            <select name="sltTipoAsociado" id="sltTipoAsociado" title="Seleccione tipo de asociado" style="width:10%;" class="col-lg-1 col-md-1 col-sm-1 form-control select2">
                                <?php
                                if (!empty($tipoAsociado)) {
                                    $sql1 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                        FROM gf_tipo_movimiento WHERE id_unico = $tipoAsociado 
                                        AND  clase = 1 AND compania = $compania";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="'.$fila1[0].'">'.ucwords(mb_strtolower($fila1[1]))." ".$fila1[2].'</option>';
                                    $sql2 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                        FROM gf_tipo_movimiento WHERE id_unico != $tipoAsociado 
                                        AND  clase = 1 AND compania = $compania";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="'.$fila2[0].'">'.ucwords(mb_strtolower($fila2[1]))." ".$fila2[2].'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Asociado</option>";
                                    $sql3 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                        FROM gf_tipo_movimiento WHERE  clase = 1 AND compania = $compania";
                                    $result3 = $mysqli->query($sql3);
                                    while ($fila3 = mysqli_fetch_row($result3)) {
                                        echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1]))." ".$fila3[2].'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label class="col-lg-1 col-md-1 col-sm-1 control-label" for="sltNumeroA">Nro Asociado:</label>
                            <select name="sltNumeroA" id="sltNumeroA" title="Seleccione número de asociado" style="width: 10.2%" class="col-lg-1 col-md-1 col-sm-1 form-control select2">
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
                            <label for="sltTipoMovimiento" class="col-sm-1 col-md-1 control-label"><strong class="obligado">*</strong>Tipo Movimiento:</label>
                            <select   name="sltTipoMovimiento" id="sltTipoMovimiento" title="Seleccione tipo de movimiento" style="width:10%" class="col-lg-1 col-md-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($tipoMovimiento)) {
                                    $sql1 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) 
                                        FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico 
                                        WHERE tm.id_unico=$tipoMovimiento";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="'.$fila1[0].'">'.ucwords(mb_strtolower($fila1[1]))." ".$fila1[2] . '</option>';
                                    $sql2 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) 
                                        FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico
                                        WHERE tm.clase=2 AND cl.claseaso=1 AND id_unico != $tipoMovimiento 
                                        AND compania = $compania";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="'.$fila2[0].'">'.ucwords(mb_strtolower($fila2[1]))." ".$fila2[2].'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Movimiento</option>";
                                    $sql3 = "SELECT DISTINCT tm.id_unico, tm.nombre, UPPER(tm.sigla) 
                                        FROM gf_tipo_movimiento tm LEFT JOIN gf_clase cl ON tm.clase = cl.id_unico 
                                        WHERE tm.clase=2 AND cl.claseaso=1 AND compania = $compania";
                                    $result3 = $mysqli->query($sql3);
                                    while ($fila3 = mysqli_fetch_row($result3)) {
                                        echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1]))." ".$fila3[2].'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtNumeroMovimiento" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nro Movimiento:</label>
                            <input type="text" name="txtNumeroMovimiento" id="txtNumeroMovimiento" maxlength="50" style="width:10%" title="Número de movimiento" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="N° movimiento"  value="<?php echo $numeroMovimiento; ?>" required readonly/>
                            <label for="txtFecha" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <input type="text" name="txtFecha" id="txtFecha" title="Ingrese la fecha" style="width: 10.5%" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Fecha" required readonly value="<?php echo $fecha; ?>" onchange="max_date_type($('#sltTipoMovimiento').val(),this.value)">
                        </div>
                        <div class="form-group" style="margin-top:-15px">
                            <label for="sltCentroCosto" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Centro Costo:</label>
                            <select  name="sltCentroCosto" id="sltCentroCosto" class="col-lg-1 col-md-1 col-sm-1 form-control select2" style="width:10%" title="Seleccione centro costo" required>
                                <?php
                                if (!empty($centroCosto)) {
                                    $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo 
                                        WHERE id_unico = $centroCosto";
                                    $result4 = $mysqli->query($sql4);
                                    $fila4 = mysqli_fetch_row($result4);
                                    echo '<option value="'.$fila4[0].'">'.ucwords(mb_strtolower($fila4[1])).'</option>';
                                    $sql5 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo 
                                        WHERE id_unico != $centroCosto AND parametrizacionanno = $param";
                                    $result5 = $mysqli->query($sql5);
                                    while ($fila5 = mysqli_fetch_row($result5)) {
                                        echo '<option value="'.$fila5[0].'">'.ucwords(mb_strtolower($fila5[1])).'</option>';
                                    }
                                } else {
                                    $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo 
                                        WHERE nombre = 'varios' AND parametrizacionanno = $param";
                                    $result4 = $mysqli->query($sql4);
                                    $fila4 = mysqli_fetch_row($result4);
                                    echo '<option value="'.$fila4[0].'">'.ucwords(mb_strtolower($fila4[1])).'</option>';
                                    $sql6 = "SELECT DISTINCT id_unico,nombre 
                                        FROM gf_centro_costo WHERE nombre != 'varios' AND parametrizacionanno = $param";
                                    $result6 = $mysqli->query($sql6);
                                    while ($fila6 = mysqli_fetch_row($result6)) {
                                        echo '<option value="'.$fila6[0].'">'.ucwords(mb_strtolower($fila6[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltProyecto" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Proyecto:</label>
                            <select name="sltProyecto" id="sltProyecto" style="width:10.2%" class="col-lg-1 col-md-1 col-sm-1 form-control select2" title="Seleccione proyecto" required>
                                <?php
                                if (!empty($proyecto)) {
                                    $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico = $proyecto";
                                    $result7 = $mysqli->query($sql7);
                                    $fila7 = mysqli_fetch_row($result7);
                                    echo '<option value="'.$fila7[0].'">'.ucwords(mb_strtolower($fila7[1])).'</option>';
                                    $sql8 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $proyecto";
                                    $result8 = $mysqli->query($sql8);
                                    while ($fila8 = mysqli_fetch_row($result8)) {
                                        echo '<option value="'.$fila8[0].'">'.ucwords(mb_strtolower($fila8[1])).'</option>';
                                    }
                                } else {
                                    $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto 
                                        WHERE nombre = 'varios'";
                                    $result7 = $mysqli->query($sql7);
                                    $fila7 = mysqli_fetch_row($result7);
                                    echo '<option value="'.$fila7[0].'">'.ucwords(mb_strtolower($fila7[1])).'</option>';
                                    $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre != 'varios'";
                                    $result9 = $mysqli->query($sql9);
                                    while ($fila9 = mysqli_fetch_row($result9)) {
                                        echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltDependencia" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Dependen<br/>cia:</label>
                            <select name="sltDependencia" id="sltDependencia" class="col-lg-1 col-md-1 col-sm-1 form-control select2" style="width:10%;" title="Seleccione dependecia " required>
                                <?php
                                if (!empty($dependencia)) {
                                    $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico = $dependencia";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1])).'</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico != $dependencia AND compania = $compania AND tipodependencia = 1";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="'.$fila10[0].'">'.ucwords(mb_strtolower($fila10[1])).'</option>';
                                    }
                                } else {
                                    if(!empty($_GET['asociado'])){
                                        $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE compania = $compania AND tipodependencia = 1";
                                        $result11 = $mysqli->query($sql11);
                                        while ($fila11 = mysqli_fetch_row($result11)) {
                                            echo '<option value="'.$fila11[0].'">'.ucwords(mb_strtolower($fila11[1])).'</option>';
                                        }
                                    }else{
                                        echo "<option value=\"\">Dependencia</option>";
                                        $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE compania = $compania AND tipodependencia = 1";
                                        $result11 = $mysqli->query($sql11);
                                        while ($fila11 = mysqli_fetch_row($result11)) {
                                            echo '<option value="'.$fila11[0].'">'.ucwords(mb_strtolower($fila11[1])).'</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltResponsable" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Respon<br/>sable:</label>
                            <select name="sltResponsable" id="sltResponsable"  title="Seleccione responsable"  style="width:10%" class="col-lg-1 col-md-1 col-sm-1 form-control select2" required>
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
                            <label for="sltRubroP" class="col-lg-1 col-md-1 col-sm-1 control-label">Rubro Presupuestal:</label>
                            <select name="sltRubroP" id="sltRubroP" class="col-lg-1 col-md-1 col-sm-1 form-control select2" title="Seleccione rubro presupuestal" style="width:10.5%">
                                <?php
                                if (!empty($rubroPresupuestal)) {
                                    $sql9 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico = $rubroPresupuestal AND parametrizacionanno = $param";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="' . $fila9[0] . '">' . ucwords(mb_strtolower($fila9[1].'- '.$fila9[2])) . '</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico != $rubroPresupuestal AND parametrizacionanno = $param AND movimiento = 1 AND (tipoclase = 7 OR tipoclase = 8) ";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="' . $fila10[0] . '">' . ucwords(mb_strtolower($fila10[1] . '- ' . $fila10[2])) . '</option>';
                                    }
                                } else {
                                    echo "<option value=''>Rubro Presuuestal</option>";
                                    $sql11 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $param AND movimiento = 1 AND (tipoclase = 7 OR tipoclase = 8) ";
                                    $result11 = $mysqli->query($sql11);
                                    while ($fila11 = mysqli_fetch_row($result11)) {
                                        echo '<option value="' . $fila11[0] . '">' . ucwords(mb_strtolower($fila11[1] . ': ' . $fila11[2])) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top:-5px">
                            <label for="sltTercero" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tercero :</label>
                            <select name="sltTercero" id="sltTercero" title="Seleccione tercero" style="width: 10%" class="col-lg-1 col-md-1 col-sm-1 form-control select2" required>
                                <?php
                                if(!empty($tercero)){
                                    $sql18 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                            WHERE prt.perfil BETWEEN 5 AND 6 AND ter.id_unico=$tercero AND ter.compania = $compania";
                                    $rs18 = $mysqli->query($sql18);
                                    $row18 = mysqli_fetch_row($rs18);
                                    echo '<option value="'.$row18[1].'">'.ucwords(mb_strtolower($row18[0].PHP_EOL.$row18[2])).'</option>';
                                    $sql19 = "
                                        SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                            WHERE prt.perfil BETWEEN 5 AND 6 AND ter.id_unico!=$tercero AND ter.compania = $compania";
                                    $rs19 = $mysqli->query($sql19);
                                    while($row19 = mysqli_fetch_row($rs19)){
                                        echo '<option value="'.$row19[1].'">'.ucwords(mb_strtolower($row19[0].PHP_EOL.$row19[2])).'</option>';
                                    }
                                }else{
                                    echo '<option value="">Tercero</option>';
                                    $sql1 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',
                                            ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            LEFT JOIN gf_perfil_tercero prt ON ter.id_unico = prt.tercero
                                            WHERE prt.perfil BETWEEN 5 AND 6 AND ter.compania = $compania";
                                    $rs1 = $mysqli->query($sql1);
                                    while($row1 = mysqli_fetch_row($rs1)){
                                        echo '<option value="'.$row1[1].'">'.ucwords(mb_strtolower($row1[0].PHP_EOL.$row1[2])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltUPE" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>U. Plazo Entrega:</label>
                            <select name="sltUPE" id="sltUPE" title="Seleccione unidad plazo de entrega " style="width:10.2%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($unidadPlazo)) {
                                    $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega WHERE id_unico = $unidadPlazo";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="' . $fila9[0] . '">' . ucwords(mb_strtolower($fila9[1])) . '</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega WHERE id_unico!= $unidadPlazo";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="' . $fila10[0] . '">' . ucwords(mb_strtolower($fila10[1])) . '</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Unidad Plazo Entrega</option>";
                                    $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_unidad_plazo_entrega";
                                    $result11 = $mysqli->query($sql11);
                                    while ($fila11 = mysqli_fetch_row($result11)) {
                                        echo '<option value="' . $fila11[0] . '">' . ucwords(mb_strtolower($fila11[1])) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtPlazoE" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Plazo de Entrega:</label>
                            <input type="number" name="txtPlazoE" id="txtPlazoE" title="Ingrese plazo de entrega" style="width: 10%" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Plazo Entrega" required value="<?php echo $PlazoEntrega; ?>">
                            <label for="sltLE" class="col-lg-1 col-md-1 control-label"><strong class="obligado">*</strong>Lugar Entrega:</label>
                            <select name="sltLE" id="sltLE" title="Seleccione lugar entrega" style="width: 10%" class="col-lg-1 col-md-1 form-control select2" required>
                                <?php
                                if (!empty($lugarEntrega)) {
                                    $sql9 = "SELECT DISTINCT dpt.id_unico, dpt.nombre, cid.id_unico, cid.nombre FROM gf_ciudad cid LEFT JOIN gf_departamento dpt ON dpt.id_unico = cid.departamento WHERE cid.id_unico = $lugarEntrega";
                                    $result9 = $mysqli->query($sql9); $row9 = mysqli_fetch_row($result9);
                                    echo "<optgroup label=\"".ucwords(mb_strtolower($row9[1]))."\">";
                                    echo "<option value=\"".$row9[2]."\">".ucwords(mb_strtolower($row9[3]))."</option>";
                                    $sqlT = "SELECT cid.id_unico, cid.nombre FROM gf_ciudad cid WHERE cid.id_unico != $lugarEntrega AND cid.departamento = $row9[0]";
                                    $resultT = $mysqli->query($sqlT);
                                    while ($rowT = mysqli_fetch_row($resultT)) {
                                        echo "<option value=\"".$rowT[0]."\">".ucwords(mb_strtolower($rowT[1]))."</option>";
                                    }
                                    echo "</optgroup>";
                                    $sql10 = "SELECT DISTINCT dpt.id_unico, dpt.nombre FROM gf_departamento dpt LEFT JOIN gf_ciudad cid ON cid.departamento = dpt.id_unico WHERE cid.departamento IS NOT NULL AND cid.departamento != $row9[0] ORDER BY dpt.nombre ASC";
                                    $result10 = $mysqli->query($sql10);
                                    while ($row10 = mysqli_fetch_row($result10)) {
                                        echo "<optgroup label=\"".$row10[1]."\">";
                                        $sqlR = "SELECT id_unico, nombre FROM gf_ciudad WHERE departamento = $row10[0]";
                                        $resultR = $mysqli->query($sqlR);
                                        while ($rowR = mysqli_fetch_row($resultR)) {
                                            echo "<option value=\"".$rowR[0]."\">".ucwords(mb_strtolower($rowR[1]))."</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                } else {
                                    echo "<option value=\"\">Lugar Entrega</option>";
                                    $sql9 = "SELECT DISTINCT dpt.id_unico, dpt.nombre FROM gf_departamento dpt LEFT JOIN gf_ciudad cid ON cid.departamento = dpt.id_unico WHERE cid.departamento IS NOT NULL ORDER BY dpt.nombre ASC";
                                    $result9 = $mysqli->query($sql9);
                                    while ($row9 = mysqli_fetch_row($result9)) {
                                        echo "<optgroup label=\"".ucwords(mb_strtolower($row9[1]))."\">";
                                        $sqlR = "SELECT id_unico, nombre FROM gf_ciudad WHERE departamento = $row9[0]";
                                        $resultR = $mysqli->query($sqlR);
                                        while ($rowR = mysqli_fetch_row($resultR)) {
                                            echo "<option value=\"".$rowR[0]."\">".ucwords(mb_strtolower($rowR[1]))."</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtIva" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>% Iva:</label>
                            <input type="number" name="txtIva" id="txtIva" title="Ingrese porcentaje de iva" style="width: 10.5%" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="% Iva" required value="<?php if(empty($iva)){ echo 0;} else { echo $iva; }?>">
                        </div>
                        <div class="form-inline form-group" style="margin-top:-15px">
                            <label for="sltDocSoporte" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tipo Doc. Soporte:</label>
                            <select name="sltDocSoporte" id="sltDocSoporte" title="Seleccione documento de soporte" style="width: 10%" class="col-lg-1 col-md-1 col-sm-1 form-control select2" required>
                                <?php
                                if(empty($tipoDocSoporte)) {
                                    echo "<option value=\"\">Tipo Documento Soporte</option>";
                                    $sqlTD = "SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a ORDER BY nombre ASC";
                                    $resultTD = $mysqli->query($sqlTD);
                                    while($rowTD = mysqli_fetch_row($resultTD)) {
                                        echo "<option value=\"".$rowTD[0]."\">".ucwords(mb_strtolower($rowTD[1]))."</option>";
                                    }
                                }else{
                                    $sqlTD = "SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a WHERE id_unico = $tipoDocSoporte";
                                    $resultTD = $mysqli->query($sqlTD);
                                    $rowTD = mysqli_fetch_row($resultTD);
                                    echo "<option value=\"".$rowTD[0]."\">".ucwords(mb_strtolower($rowTD[1]))."</option>";
                                    $sqlT_D = "SELECT id_unico, nombre FROM gf_tipo_documento_soporte_a WHERE id_unico != $tipoDocSoporte";
                                    $resultT_D = $mysqli->query($sqlT_D);
                                    while($rowT_D = mysqli_fetch_row($resultT_D)) {
                                        echo "<option value=\"".$rowT_D[0]."\">".ucwords(mb_strtolower($rowT_D[1]))."</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtNumDocS" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nº Doc. Soporte:</label>
                            <input type="text" name="txtNumDocS" id="txtNumDocS" title="Ingrese número de documento soporte" style="width: 10.2%" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Nº Documento Soporte" required value="<?php echo $numDocSoporte ?>">
                            <label for="txtEstado" class="col-lg-1 col-md-1 col-sm-1 control-label"><strong class="obligado">*</strong>Estado :</label>
                            <input type="text" name="txtEstado" id="txtEstado" title="Estado" style="width: 10%" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Estado" required value="<?php echo $estdo; ?>">
                            <label for="sltBuscar" class="col-lg-1 col-md-1 col-sm-1 control-label">Buscar Movimiento:</label>
                            <select name="sltBuscar" id="sltBuscar" title="Seleccione para buscar movimiento" style="width: 10%" class="col-lg-1 col-md-1 col-sm-1 form-control select2 buscar">
                                <?php
                                echo "<option value=\"\">Buscar Movimiento</option>";
                                $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                                    IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                                        FROM        gf_movimiento mov
                                        LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                                        LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                                        WHERE       tpm.clase IN (2)
                                        AND         mov.compania = $compania
                                        AND         mov.parametrizacionanno = $param 
                                        ORDER BY    cast(mov.numero as unsigned) DESC";
                                $result = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($result)) {
                                    $valorDetalle = "";
                                    $sql_r = "SELECT (dtm.valor + dtm.iva) * dtm.cantidad FROM gf_detalle_movimiento dtm WHERE dtm.movimiento = $row[0]";
                                    $result_r = $mysqli->query($sql_r);
                                    while ($row_r = mysqli_fetch_row($result_r)) {
                                        $valorDetalle += $row_r[0];
                                    }
                                    echo "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($valorDetalle,2,',','.')."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-inline form-group" style="margin-top:-15px">
                            <label for="txtDescripcion" class="col-lg-1 col-md-1 col-sm-1 control-label">Descrip<br/>ción:</label>
                            <textarea name="txtDescripcion" id="txtDescripcion" title="Ingrese descripción" style="margin-top: 0px;width: 28.5%;height: 34px" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Descripción" rows="5" cols="1000"><?php echo $descripcion; ?></textarea>
                            <label for="txtObservacion" class="col-lg-1 col-md-1 col-sm-1 control-label">Observa<br/>ciones:</label>
                            <textarea name="txtObservacion" id="txtObservacion" title="Ingrese observaciones" style="margin-top: 0px;width: 28.5%;height: 34px" class="col-lg-1 col-md-1 col-sm-1 form-control" placeholder="Observaciones" rows="5" cols="1000"><?php echo $observaciones; ?></textarea>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 col-sm-push-10 col-lg-2 col-lg-push-10" style="margin-top: -100px;margin-bottom: 5px;">
                                <a id="btnNuevo" title="Ingresar nuevo" class="btn btn-primary shadow glyphicon glyphicon-plus text-center nuevo" onclick="javascript:nuevo()"></a>
                                <button type="submit"  id="btnGuardar" title="Guardar movimiento" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk guardar"></button>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-lg-offset-10 col-sm-offset-10" style="margin-top: -60px">
                                <a id="btnImprimir" title="Imprimir" class="btn btn-primary shadow glyphicon glyphicon glyphicon-print imprimir" onclick="open_report(<?php echo $id; ?>)"></a>
                                <a id="btnModificar" title="Modificar movimiento" class="btn btn-primary shadow glyphicon glyphicon-pencil modificar" onclick="modify_data()"></a>
                                <?php
                                if(!empty($_GET['movimiento'])){
                                #** Buscar Si El Movimiento Tiene detalles afectados **#
                                $sqlda = "SELECT COUNT(dma.id_unico) FROM gf_detalle_movimiento dm "
                                        . "LEFT JOIN gf_detalle_movimiento dma ON dm.id_unico = dma.detalleasociado "
                                        . "WHERE md5(dm.movimiento) = '".$_GET['movimiento']."'";
                                $sqlda = $mysqli->query($sqlda);
                                $sqlda = mysqli_fetch_row($sqlda);
                                if($sqlda[0]>0){}else{
                                ?>
                                <a id="btnOrden" title="Agregar Orden de Compra" class="btn btn-primary shadow glyphicon glyphicon-tags"></a>
                                <?PHP } }?>
                            </div>
                        </div>
                    </form>
                    <script>
                         $("#btnOrden").click(function(){
                             $("#modalAgregarO").modal("show");
                         })
                    </script>
                </div>
            </div>
            <div class="modal fade" id="modalAgregarO" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Agregar Orden</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <div class="form-group">
                            <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <label class="col-lg-2 col-md-2 col-sm-2 control-label" for="sltTipoAsociado">Tipo Asociado:</label>
                            <select name="sltTipoAsociado1" id="sltTipoAsociado1" class="select2 form-control input-sm" title="Número de Disponibilidad" style="width:250px;">
                                <?php
                                echo "<option value=\"\">Tipo Asociado</option>";
                                $sql3 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                    FROM gf_tipo_movimiento WHERE  clase = 1 AND compania = $compania";
                                $result3 = $mysqli->query($sql3);
                                while ($fila3 = mysqli_fetch_row($result3)) {
                                    echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1]))." ".$fila3[2].'</option>';
                                }
                                ?>
                            </select>
                            <br/>
                            <label class="col-lg-2 col-md-2 col-sm-2 control-label" for="sltTipoAsociado">Nro Asociado:</label>
                            <select name="sltNumeroAsociado1" id="sltNumeroAsociado1" class="select2 form-control input-sm" title="Nro Asociado" style="width:250px;">
                                <option value="">Nro Asociado</option>
                            </select>
                        </div>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnAgregarA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Agregar</button>
                            <button type="button" id="btnCancelarA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $("#sltTipoAsociado1").change(function(){
                    var form_data = {
                        mov:9,
                        tipo:+$("#sltTipoAsociado1").val()
                    };
                    $.ajax({
                        type: 'POST',
                        url: "consultasBasicas/consulta_mov.php",
                        data:form_data,
                        success: function (data) {
                            $("#sltNumeroAsociado1").html(data).fadeIn();
                            $("#sltNumeroAsociado1").css('display','none');
                        }
                    });
                })
                $("#btnAgregarA").click(function(){
                    if($("#sltTipoAsociado1").val()!=""){
                        if($("#sltNumeroAsociado1").val()!=""){
                            //** Validar Fechas **//
                            var form_data = { action:1, asociado:$("#sltNumeroAsociado1").val(), fecha:$("#txtFecha").val() };
                            $.ajax({
                              type: "POST",
                              url: "jsonAlmacen/agregarOrderJson.php",
                              data: form_data,
                              success: function(response)
                              { 
                                    if(response==0){
                                        jsShowWindowLoad('Guardando Información');
                                        //** Agregar Asociado **//
                                        var form_data = { action:2, 
                                            asociado:$("#sltNumeroAsociado1").val(), 
                                            idmovimiento: <?php echo $iddMov;?>};
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonAlmacen/agregarOrderJson.php",
                                            data: form_data,
                                            success: function(response)
                                            { 
                                                jsRemoveWindowLoad();
                                                console.log(response);
                                                if(response==1){
                                                    $("#txtmsjmdl").html('Orden Agregada Correctamente');
                                                    $("#mdlMensaje").modal("show");
                                                    $("#btnMsj").click(function(){
                                                       document.location.reload();
                                                    });
                                                }  else {
                                                    $("#txtmsjmdl").html('No Se Pudo Agregar Orden');
                                                    $("#mdlMensaje").modal("show");
                                                }
                                            }
                                        })
                                        
                                    } else {
                                        $("#txtmsjmdl").html('Fecha Incorrecta');
                                        $("#mdlMensaje").modal("show");
                                    }
                              }   
                            }); 
                        }
                    }
                })
            </script>
            <div class="col-lg-10 col-sm-10 text-left" style="margin-top:5px;">
                <form name="form" id="formD" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFMovimiento.php?action=insert_detail">
                    <div class="form-group">
                        <input type="hidden" name="txtIdMov" id="txtIdMov" value="<?php echo $id; ?>" />
                        <div class="col-sm-3 col-md-3 col-lg-2">
                            <span class="has-float-label">
                                <select name="sltPlanInv" id="sltPlanInv" class="form-control select2" style="height:26px;cursor: pointer" title="Seleccione elemento de plan inventario" required="">
                                    <?php
                                    echo '<option value="">Plan Inventario</option>';
                                    $sql119 = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento = 2 AND compania = $compania";
                                    $result119 = $mysqli->query($sql119);
                                    while ($fila119=  mysqli_fetch_row($result119)){
                                        echo '<option value="'.$fila119[0].'">'.$fila119[2].' - '.$fila119[1].'</option>';
                                    }
                                    ?>
                                </select>
                                <label for="sltPlanInv">Plan Inventario</label>
                            </span>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <span class="has-float-label">
                                <input type="text" name="txtCantidad" class="form-control" id="txtCantidad" title="Cantidad" onkeypress="return txtValida(event,'num');" maxlength="10" placeholder="Cantidad" style="padding:2px;font-size: 10px;"/>
                                <label for="txtCantidad">Cantidad</label>
                            </span>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <span class="has-float-label">
                                <input type="text" name="txtValor" id="txtValor" class="form-control col-lg-1 col-sm-1" title="Valor aproximado" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Valor" style="padding:2px;font-size: 10px;" required="" />
                                <label for="txtValor">Valor Unitario</label>
                            </span>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <span class="has-float-label">
                                <input type="text" name="txtValorIva" id="txtValorIva" class="form-control col-lg-1 col-sm-1" title="Iva" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Iva" style="padding:2px;font-size: 10px;" readonly required="" />
                                <label for="txtValorIva">Iva</label>
                            </span>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <span class="has-float-label">
                                <input type="text" name="txtAjuste" id="txtAjuste"  class="form-control col-lg-1 col-sm-1" title="Ajuste"  maxlength="50" placeholder="Ajuste" value="0" style="padding:2px;font-size: 10px;" required="" value="0"/>
                                <label for="txtAjuste">Ajuste</label>
                            </span>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <span class="has-float-label">
                                <input type="text" name="txtValorTotal" id="txtValorTotal"  class="form-control col-lg-1 col-sm-1" title="Valor total" onkeypress="return txtValida(event,'num');"  maxlength="50" placeholder="Valor Total" style="padding:2px;font-size: 10px;" required="" readonly/>
                                <label fot="txtValorTotal">Total</label>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary shadow guardar" id="btnGuardarDetalle"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                    </div>
                </form>
            </div>
            <?php
            if(!empty($_GET['movimiento'])) {
                echo "\n\t<script>";
                echo "$(\"#btnGuardar\").attr('disabled',true);";
                echo "$(\"#btnGuardar\").removeAttr('onclick');";
                echo " \n\t</script>";
            }else{
                echo "\n\t<script>";
                echo "$(\"#btnNuevo, #btnModificar,#btnOrden, #btnImprimir, #btnGuardarDetalle\").attr('disabled',true);";
                echo "$(\"#btnNuevo, #btnModificar,#btnOrden, #btnImprimir, #btnGuardarDetalle\").removeAttr('onclick');";
                echo " \n\t</script>";
            }

            //Cuando la variable asociado no esta vacia
            if(!empty($_GET['asociado'])) {
                echo "\n\t<script>";
                echo "$(\"#btnNuevo\").attr('disabled',false);";
                echo "$(\"#btnNuevo\").click(function(){nuevo();})";
                echo " \n\t</script>";
            }
            ?>
            <input type="hidden" id="idPrevio" value="">
            <input type="hidden" id="idActual" value="">
            <div class="col-lg-10 col-sm-10 text-left" style="margin-top: -10px">
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
                            if(!empty($idMov)){
                                $det = "SELECT  dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva, pl.codi, pl.ficha,
                                                dtm.ajuste
                                FROM gf_detalle_movimiento dtm
                                LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                WHERE md5(mv.id_unico)='$idMov'";
                                $resultado2 = $mysqli->query($det);
                                $html = "";
                                while ($row2 = mysqli_fetch_row($resultado2)) {
                                    $html .= "\n\t<tr>";
                                    $html .= "\n\t\t<td class='campos'>";
                                    $html .= "\n\t\t\t<a href=\"#".$row2[0]."\" title=\"Eliminar\" class=\"eliminar\" onclick=\"delete_detail(".$row2[0].");\"><i class=\"glyphicon glyphicon-trash\"></i></a>";
                                    $html .= "\n\t\t\t<a href=\"#".$row2[0]."\" title=\"Modificar\" id=\"mod\" class=\"modificar\" onclick=\"modificar(".$row2[0].");\"><li class=\"glyphicon glyphicon-edit\"></li></a>";
                                    $html .= "\n\t\t</td>";
                                    $html .= "\n\t\t<td class=\"text-left campos\" width=\"7%\">";
                                    $html .= "\n\t\t\t<label class=\"valorLabel\" style=\"font-weight:normal\" id=\"lblItem".$row2[0]."\">".$item++."</label></td>";
                                    $html .= "\n\t\t\t<td class=\"text-left campos\">";
                                    $html .= "\n\t\t\t\t<label  class=\"valorLabel\" style=\"font-weight:normal\" id=\"lblCodigoE".$row2[0]."\">".$row2[7]." - ".$row2[5]."</label>";
                                    $html .= "\n\t\t\t\t<select class=\"col-sm-12 campoD\" name=\"sltPlanInventario".$row2[0]."\" id=\"sltPlanInventario".$row2[0]."\" title=\"Seleccione elemento de plan inventario\" style=\"display:none;padding:2px\">";
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t\t<td class=\"text-right campos\">";
                                    $sumC+=$row2[2];
                                    $html .= "\n\t\t\t\t<label class=\"valorLabel\" style=\"font-weight:normal\" id=\"lblCantidad".$row2[0]."\">".$row2[2]."</label>";
                                    $html .= "\n\t\t\t\t<input maxlength=\"50\" onkeypress=\"return justNumbers(event)\" onkeyup=\"calcule_values(".$row2[0].")\" style=\"display:none;padding:2px;\" class=\"col-lg-12 col-sm-12 campoD text-right\" type=\"number\" name=\"txtcantidad".$row2[0]."\" id=\"txtcantidad".$row2[0]."\" value=\"".$row2[2]."\" />";
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t\t<td class=\"text-right campos\">";
                                    $sumV+=$row2[3];
                                    $html .= "\n\t\t\t\t<label class=\"valorLabel\" style=\"font-weight:normal\" id=\"ValorT".$row2[0]."\">".number_format($row2[3],2,',','.')."</label>";
                                    $html .= "\n\t\t\t\t<input maxlength=\"50\" onkeypress=\"return justNumbers(event);\" onkeyup=\"calcule_values(".$row2[0].")\" style=\"display:none;padding:2px;\" class=\"col-lg-12 col-sm-12 campoD text-left\"  type=\"number\" name=\"txtvalor".$row2[0]."\" id=\"txtvalor".$row2[0]."\" value=\"".$row2[3]."\" />";
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t\t<td class=\"text-right campos\">";
                                    $sumIva +=$row2[6];
                                    $html .= "\n\t\t\t\t<label class=\"valorLabel\" style=\"font-weight:normal\" id=\"lblIva".$row2[0]."\">".number_format($row2[6],2,'.',',')."</label>";
                                    $html .= "\n\t\t\t\t<input maxlength=\"50\" onkeypress=\"return justNumbers(event)\" style=\"display:none;padding:2px;\" class=\"col-lg-12 col-sm-12 campoD text-left\"  type=\"number\" name=\"txtiva".$row2[0]."\" id=\"txtiva".$row2[0]."\" value=\"".$row2[6]."\" readonly />";
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t\t<td style=\"height:10px;font-size:10px\"class=\"text-right campos\">";
                                    $total = ($row2[3]+$row2[6])*$row2[2];
                                    $totalmov+=$total;
                                    $html .= "\n\t\t\t\t<label class=\"valorLabel\" style=\"font-weight:normal\" id=\"lblValorTotal".$row2[0]."\">".number_format($total, 2, '.', ',')."</label>";
                                    $html .= "\n\t\t\t\t<div class=\"col-lg-10 col-md-10 col-sm-10\">";
                                    $html .= "\n\t\t\t\t\t<input maxlength=\"50\" onkeypress=\"return justNumbers(event)\" style=\"display:none;padding:2px;\" class=\"campoD text-left\"  type=\"number\" name=\"txttotal".$row2[0]."\" id=\"txttotal".$row2[0]."\" value=\"".$total."\" readonly />";
                                    $html .= "\n\t\t\t\t</div>";
                                    $html .= "\n\t\t\t\t<div class=\"col-lg-2 col-md-2 col-sm-2\">";
                                    $html .= "\n\t\t\t\t\t<table id=\"tab".$row2[0]."\" style=\"padding:0px;background-color:transparent;background:transparent;\">";
                                    $html .= "\n\t\t\t\t\t\t<tbody>";
                                    $html .= "\n\t\t\t\t\t\t\t<tr style=\"background-color:transparent;\">";
                                    $html .= "\n\t\t\t\t\t\t\t\t<td style=\"background-color:transparent;\">";
                                    $html .= "\n\t\t\t\t\t\t\t\t\t<a href=\"#".$row2[0]."\" title=\"Guardar\" id=\"guardar".$row2[0]."\" style=\"display: none;\" onclick=\"guardarCambios(".$row2[0].")\"><li class=\"glyphicon glyphicon-floppy-disk\"></li></a>";
                                    $html .= "\n\t\t\t\t\t\t\t\t</td>";
                                    $html .= "\n\t\t\t\t\t\t\t\t<td style=\"background-color:transparent;\">";
                                    $html .= "\n\t\t\t\t\t\t\t\t\t<a href=\"#".$row2[0]."\" title=\"Cancelar\" id=\"cancelar".$row2[0]."\" style=\"display: none\" onclick=\"cancelar(".$row2[0].")\" >";
                                    $html .= "\n\t\t\t\t\t\t\t\t\t\t<i title=\"Cancelar\" class=\"glyphicon glyphicon-remove\" ></i>";
                                    $html .= "\n\t\t\t\t\t\t\t\t\t</a>";
                                    $html .= "\n\t\t\t\t\t\t\t\t</td>";
                                    $html .= "\n\t\t\t\t\t\t\t</tr>";
                                    $html .= "\n\t\t\t\t\t\t</tbody>";
                                    $html .= "\n\t\t\t\t\t</table>";
                                    $html .= "\n\t\t\t\t</div>";
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t\t<td class=\"campos text-center\" style=\"width: 10px\">";
                                    $sq_ = "SELECT plan_hijo FROM gf_plan_inventario_asociado WHERE plan_padre = $row2[1]";
                                    $re_ = $mysqli->query($sq_);
                                    if($re_->num_rows > 0){
                                        $html .= "\n\t\t\t\t<a id=\"plan".$row2[0]."\" class=\"valorLabel ficha\" href=\"javascript:void(0)\" title=\"Ficha Inventario\" onclick=\"return registroDetalleHijos(".$row2[1].','.$row2[3].','.$id.','.$row2[2].','.$row2[0].")\" data-backdrop=\"static\" data-keyboard=\"false\" data-toggle=\"modal\"><i class=\"glyphicon glyphicon-blackboard\"></i></a>";
                                    }else{
                                        $sqD = "SELECT tipoinventario FROM gf_plan_inventario WHERE id_unico = $row2[1]";
                                        $reD = $mysqli->query($sqD);
                                        $roD = $reD->fetch_row();
                                        if($roD[0] == 2 OR $roD[0] == 4){
                                            $html .= "\n\t\t\t\t<a id=\"plan".$row2[0]."\" class=\"valorLabel ficha\" href=\"javascript:void(0)\" title=\"Ficha Inventario\" onclick=\"return abrirFichaI(".$row2[2].','.$row2[8].','.$row2[3].','.$row2[0].','.$id.")\" data-backdrop=\"static\" data-keyboard=\"false\" data-toggle=\"modal\"><i class=\"glyphicon glyphicon-blackboard\"></i></a>";
                                        }else{
                                            $html .= '<i class="glyphicon glyphicon-blackboard ficha" title="Ficha Inventario"></li>';
                                        }
                                    }
                                    $html .= "\n\t\t\t</td>";
                                    $html .= "\n\t\t</tr>";
                                }
                                echo $html;
                            }

                            if(!empty($_GET['asociado'])){
                                $html = "";
                                $aso  = $_GET['asociado'];
                                $sql_d = "SELECT  dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva , pl.codi
                                        FROM      gf_detalle_movimiento dtm
                                        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                        WHERE     md5(mv.id_unico) = '$aso'";
                                $res_d = $mysqli->query($sql_d);
                                $row_d = $res_d->fetch_all(MYSQLI_NUM);
                                foreach ($row_d as $rowa) {
                                    $xc = 0;
                                    $sq_c = "SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $rowa[0]";
                                    $re_c = $mysqli->query($sq_c);
                                    $ro_c = $re_c->fetch_all(MYSQLI_NUM);
                                    foreach($ro_c as $r){
                                        $xc += $r[0];
                                    }
                                    $xxx = $rowa[2] - $xc;
                                    if( $xxx > 0){
                                        $html .= "<tr>";
                                        $html .= "<td></td>";
                                        $html .= "<td class='text-center campos' width='2%'>".$item++."</td>";
                                        $html .= "<td class='campos text-right'>".ucwords(mb_strtolower($rowa[7]." ".$rowa[5]))."</td>";
                                        $html .= "<td class='campos text-right'>$xxx</td>";
                                        $html .= "<td class='campos text-right'>".number_format($rowa[3], 2, ',', '.')."</td>";
                                        $html .= "<td class='campos text-right'>".number_format($rowa[6], 2, ',', '.')."</td>";
                                        $vtt = ($rowa[3] + $rowa[6]) * $xxx;
                                        $html .= "<td class='campos text-right'>".number_format($vtt, 2, ',', '.')."</td>";
                                        $html .= "<td></td>";
                                        $html .= "</tr>";
                                        $sumV     += $rowa[3];
                                        $sumIva   += $rowa[6];
                                        $totalmov += $vtt;
                                    }
                                }
                                echo $html;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 text-right">
                <label for="" class="control-label col-lg-1 col-md-1 col-sm-1 text-right col-sm-offset-5 col-md-offset-5 col-lg-offset-5">Totales :</label>
                <label for="" class="control-label col-lg-2 col-md-2 col-sm-2 text-right values" title="Total de Valor Aproximado"><?php echo "$".number_format($sumV,2,',','.'); ?></label>
                <label for="" class="control-label col-lg-2 col-md-2 col-sm-2 text-right values" title="Total de Iva"><?php echo "$".number_format($sumIva,2,',','.') ?></label>
                <label for="" class="control-label col-lg-2 col-md-2 col-sm-2 text-right" title="Total de Valor Total"><?php echo "$".number_format($totalmov,2,',','.')?></label>
            </div>
        </div>
    </div>
    <?php
    if(!empty($idaso)) {
        echo "<script>$(\".eliminar\").css('display','none');$(\".modificar\").css('display','none');$(\".ficha\").css('display','none');</script>";
    }
    ?>
    <?php require_once 'footer.php' ?>
    <script src="js/bootstrap.min.js"></script>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
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
                    <button type="button" id="ver1" onclick="reload()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                    <button type="button" id="btnModifico" onclick="reload()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                    <button type="button" id="btnGuardado" onclick="reload()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
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
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();
        /**
         * Cuando el combo cambie su valor, redireccionara y traera los datos del asociado
         */
        $("#sltNumeroA").change(function () {
            var form_data = {
                mov:8,
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
        $("#sltDependencia").change(function(e){
            var dep = e.target.value;
            obtenerResponsableD(dep);
        });

        function obtenerResponsableD(dep){
            var form_data={
                existente:5,
                dependencia:dep
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
        }

        $("#sltBuscar").change(function() {
            var form_data = {
                mov:7,
                id:$("#sltBuscar").val()
            };

            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data) {
                    window.location = data;
                }
            });
        });

        function nuevo() {
            window.location = 'RF_ENTRADA_ALMACEN.php';
        }

        function modify_data() {
            var form_data = {
                action: 'modify',
                txtFecha:$("#txtFecha").val(),
                txtDescripcion:$("#txtDescripcion").val(),
                txtObservacion:$("#txtObservacion").val(),
                id:<?php echo $id; ?>,
                txtIva:$("#txtIva").val()
            };
            var result = '';
            $.ajax({
                type:'POST',
                url: "controller/controllerGFMovimiento.php",
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
                        $("#mdlModificado").modal('show');
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
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;
            var txtValorT = 'txttotal'+id;

            $("#"+txtValor).keyup(function(){
                var cantidad = $("#"+txtCantidad).val();
                if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                    cantidad = 1;
                }else{
                    cantidad = parseFloat($("#"+txtCantidad).val());
                }
                valor    = parseFloat($("#"+txtValor).val());
                iva      = parseFloat(<?php echo $porcIva; ?>);
                totalIva = redondeo((valor*iva)/100, <?php echo $dc_[0] ?>);
                total    = (parseFloat(valor) + parseFloat(totalIva)) * cantidad;
                $("#"+txtIva).val(totalIva);
                $("#"+txtValorT).val(total);
            });

            $("#"+txtCantidad).keyup(function(){
                var cantidad = $("#"+txtCantidad).val();
                if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                    cantidad = 1;
                }else{
                    cantidad = parseFloat($("#"+txtCantidad).val());
                }
                valor    = parseFloat($("#"+txtValor).val());
                iva      = parseFloat(<?php echo $porcIva; ?>);
                totalIva = redondeo((valor*iva)/100, <?php echo $dc_[0] ?>);
                total    = (parseFloat(valor) + parseFloat(totalIva))* cantidad;
                $("#"+txtIva).val(totalIva);
                $("#"+txtValorT).val(total);
            });
        }

        $("#btnD").click(function(){
            $("#sltTipoMovimiento").focus();
        });

        $('#btnG').click(function(){
            document.location.reload();
        });

        $('#btnG2').click(function(){
            document.location.reload();
        });

        function reload(){
            document.location.reload();
        };

        function calcule_values(id) {
            var txtCantidad = 'txtcantidad'+id;
            var txtValor    = 'txtvalor'+id;
            var txtIva      = 'txtiva'+id;
            var txtTotal    = 'txttotal'+id;

            var cantidad = parseFloat($("#"+txtCantidad).val());
            var valor    = parseFloat($("#"+txtValor).val());
            var iva      = parseFloat(<?php echo $porcIva; ?>);

            if (cantidad === 0 || cantidad === "" || cantidad.length === 0 || cantidad === null) {
                cantidad = 0;
            }else {
                cantidad = parseFloat($("#"+txtCantidad).val());
            }

            var totalIva = (parseFloat(valor) * parseFloat(iva)) / 100;
            var total    = (parseFloat(valor) + parseFloat(totalIva)) * cantidad;
            $("#"+txtIva).val(totalIva);
            $("#"+txtTotal).val(total);
        }

        function abrirFichaI(cantidad,ficha,valor,movimiento,idM){
            var form_data = {
                ficha:ficha,
                cantidad:cantidad,
                valor:valor,
                movimiento:movimiento,
                idM:idM
            };

            $.ajax({
                type: 'POST',
                url: "modalProducto.php#modalFichaInventario",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalFichaInventario").html(data);
                    $('.modalI').modal({backdrop: 'static', keyboard: false,show:true});
                }
            });
        }

        function abrirFichaIn(cantidad,ficha,valor,detalle,pos,nom){
            var form_data = {
                posicion:pos,
                ficha:ficha,
                cantidad:cantidad,
                valor:valor,
                detalle:detalle
            };

            $.ajax({
                type: 'POST',
                url: "consultasBasicas/paginacionFormularioProducto.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#formProducto").html(data);
                }
            });
        }

        function registroDetalleHijos(plan,valor,mov,cant,detalle){
            var form_data = {padre:plan,valor:valor,mov:mov,cant:cant,iva:<?php echo $porcIva ?>,detalle:detalle};
            $.ajax({
                type: 'POST',
                url: "modalV.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalRegistrarD").html(data);
                    $('.plan').modal({backdrop: 'static', keyboard: false,show:true});
                }
            });
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

        function clean_inputs (){
            $("#sltPlanInv").select2('val','');
            $("#txtCantidad").val("");
            $("#txtValor").val("");
            $("#txtValorIva").val("");
            $("#txtAjuste").val("");
            $("#txtValorTotal").val("");
        }

        function redondeo(valor, decimales){
            var flt = parseFloat(valor);
            var res = Math.round(flt * Math.pow(10, decimales)) / Math.pow(10, decimales);
            return res;
        }

        function ajuste_peso(ajuste){
            var valor = parseFloat($('#txtValor').val());
            var iva   = parseFloat($('#txtValorIva').val());
            var can   = parseFloat($('#txtCantidad').val());

            if(can == "" || isNaN(can)){
                can = 1;
            }

            var xxx = (valor * can) + iva;
            var zzz = redondeo(xxx, ajuste);
            $("#txtValorTotal").val(zzz);
        }

        var valor = 0.00;
        var iva = 0.00;
        var totalP = 0.00;
        var totalIva = 0.00;
        var total = 0.00;

        $("#txtValor").keyup(function(){
            var cantidad = $("#txtCantidad").val();
            if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                cantidad = 1;
            }else{
                cantidad = parseFloat($("#txtCantidad").val());
            }
            valor    = parseFloat($("#txtValor").val());
            iva      = parseFloat(<?php echo $porcIva; ?>);
            if(<?php echo $dc_[0] ?> != ' '){
                totalIva = redondeo((valor * iva)/100, <?php echo $dc_[0] ?>);
            }else{
                totalIva = ((valor * iva)/100);
                totalIva = totalIva.toFixed(2);
            }
            total    = (parseFloat(valor) + parseFloat(totalIva)) * cantidad;
            $("#txtValorIva").val(totalIva);
            $("#txtValorTotal").val(total);
        });

        $("#txtAjuste").blur(function(){
            var cantidad = $("#txtCantidad").val();
            var valor  = parseFloat($("#txtValor").val());
            var iva    = parseFloat($("#txtValorIva").val());
            var ajuste = parseFloat($(this).val());

            if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                cantidad = 1;
            }else{
                cantidad = parseFloat($("#txtCantidad").val());
            }

            var tc     = valor;
            var total  = (iva + ajuste);
            var vTotal = (tc + total);

            $("#txtValorIva").val(total);
            $("#txtValorTotal").val(vTotal);
        });

        /**
         * open_report
         * @param int mov Id de movimiento
         */
        function open_report(mov) {
            if(!isNaN(mov)) {
                window.open('informes_almacen/inf_entrada_almacen.php?mov=<?php echo md5($id)?>');
            }
        }

        $(document).ready(function(){
            var validator = $("#formD").validate({
                ignore: "",
                errorElement: "em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });
        <?php
        $html = "";
        if(!empty($_GET['asociado'])){
            $html .= "obtenerResponsableD($('#sltDependencia').val());";
        }
        echo $html;
        ?>
    </script>
    <?php
    require_once './modalProducto.php';
    require_once './modalV.php';
    ?>
</body>
<script> 
    function validar(){
        if($("#sltTipoAsociado").val()!=""){
            if($("#sltNumeroA").val()!=""){
                //** Validar Fechas **//
                var form_data = { action:1, asociado:$("#sltNumeroA").val(), fecha:$("#txtFecha").val() };
                $.ajax({
                  type: "POST",
                  url: "jsonAlmacen/agregarOrderJson.php",
                  data: form_data,
                  success: function(response)
                  { 
                        if(response==0){
                            return true;
                        } else {
                            return false;
                            $("#txtFecha").val('');
                            $("#txtmsjmdl").html('Fecha Incorrecta');
                            $("#mdlMensaje").modal("show");
                        }
                  }   
                }); 
            } else {
                $("#txtmsjmdl").html('Seleccione Asociado');
                $("#mdlMensaje").modal("show");
                return false;
            }
        } else {
            return true;
        }
    }
    
</script>
<div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="txtmsjmdl" name="txtmsjmdl" ></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</html>