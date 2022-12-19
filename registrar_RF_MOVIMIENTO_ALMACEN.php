<?php
/**
 * Movimiento de Almacen
 * Created by Alexander.
 * User: Alexander
 * Date: 27/05/2017
 * Time: 8:25 AM
 */
include ('head_listar.php');
include ('Conexion/conexion.php');
include ('funciones/funciones_mov.php');
$compania = $_SESSION['compania'];
$param = $_SESSION['anno'];
/**
 * Inicializamos las variables en 0 o vacio
 */
$idMov = ""; $tipoMovimiento = ""; $numeroMovimiento = ""; $fecha=date('d/m/Y'); $centroCosto = ""; $proyecto = ""; $dependencia = ""; $responsable = "";
$rubroPresupuestal = ""; $PlazoEntrega = ""; $estado = 0; $unidadPlazo = 0; $lugarEntrega = "";$id = "0"; $iva = ""; $descripcion = ""; $observaciones = ""; $sumC = 0;
$sumV = 0; $sumIva = 0;$tipoAsociado = "";$tipo_aso = "";$id_asoc = ""; $id_aso = ""; $sumC = 0;$sumV = 0;$sumIva = 0; $porcIva = ""; $estdo = ""; $tercero = "";
$idasoc = ""; $idaso = ""; $tipoAsociado = ""; $id_asoc = ""; $tipoDocSoporte = ""; $numDocSoporte = "";
/** @var movimiento $_GET */
if(!empty($_GET['movimiento'])){
    $idMov = $_GET['movimiento'];
    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia, mv.tercero, mv.rubropptal, mv.estado, mv.plazoentrega, mv.unidadentrega, mv.lugarentrega, mv.descripcion, mv.observaciones, mv.id_unico, mv.tercero2, mv.porcivaglobal, mv.tercero2, mv.tipo_doc_sop, mv.numero_doc_sop 
            FROM    gf_movimiento mv 
            WHERE   md5(id_unico)='$idMov'";
    $result = $mysqli->query($sql);
    $rowPrimaria = mysqli_fetch_row($result);
    /**
     * Llenamos las variables con los valores retornados por la consulta
     */
    $tipoMovimiento = $rowPrimaria[0]; $numeroMovimiento = $rowPrimaria[1]; $fecha = $rowPrimaria[2]; $centroCosto = $rowPrimaria[3]; $proyecto = $rowPrimaria[4];
    $dependencia = $rowPrimaria[5]; $responsable = $rowPrimaria[6]; $rubroPresupuestal = $rowPrimaria[7]; $estado = $rowPrimaria[8]; $PlazoEntrega = $rowPrimaria[9];
    $unidadPlazo = $rowPrimaria[10]; $lugarEntrega = $rowPrimaria[11]; $descripcion = $rowPrimaria[12]; $observaciones = $rowPrimaria[13]; $id = $rowPrimaria[14];
    $tercero = $rowPrimaria[15]; $iva = $rowPrimaria[16]; $porcIva = $rowPrimaria[16]; $tercero = $rowPrimaria[17]; $tipoDocSoporte = $rowPrimaria[18];
    $numDocSoporte = $rowPrimaria[19];
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $est = mysqli_fetch_row($resEstado);
    $estdo = $est[0];
}

if(!empty($_GET['valorI'])){
    $porcIva=$_GET['valorI'];
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
    $id_aso = $rowA[14]; $iva = $rowA[15]; $porcIva = $rowA[15]; $tercero = $rowA[16]; $tipoDocSoporte = $rowPrimaria[17]; $numDocSoporte = $rowPrimaria[18];
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
?>
    <title>Registro Movimiento Almacen</title>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
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
            $("#txtFecha").datepicker({changeMonth: true}).val();
        });

        $(document).ready(function() {
            var i= 1;
            $('#tablaM thead th').each( function () {
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
                        case 8:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            } );
            // DataTable
            var table = $('#tablaM').DataTable({
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
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    } );
                    i = i+1;
                } else {
                    i = i+1;
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
                    //var o = element.attr("id");
                    //document.getElementById(o).style.border = "solid #0000FF";
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
    </script>
    <style>
        .campos{
            padding: 0px;
            font-size: 10px
        }
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        body {font-family: "Arial";font-size: 10px;}
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
        label #sltTipoAsociado-error, #sltNumeroA-error, #sltTipoMov-error, #txtNumero-error, #txtFecha-error, #sltCentroCosto-error, #sltProyecto-error, #sltDependencia-error,
        #sltResponsable-error, #sltTercero-error, #sltUPE-error, #txtPlazoE-error, #sltLE-error, #txtIva-error, #txtDescripcion-error, #txtObservacion-error, #sltRubroP-error, #txtNumDocs-error, #sltDocSoporte-error, .error {
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;
        }

        .values {
            cursor:pointer;
        }
    </style>
</head>
<body onload="clean_inputs()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php include ('menu.php');?>
            <div class="col-lg-10 col-sm-10 text-left">
                <h2 class="tituloform text-center" style="margin-top: 0px">Registrar Movimiento Almacen</h2>
                <div style="margin-top:-7px; border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="controller/controllerGFMovimiento.php?action=insert" style="margin-bottom: -30px;">
                        <p align="center" class="parrafoO" style="margin-bottom:5px">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group">
                            <label for="sltTipoAsociado" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tipo Asociado :</label>
                            <select name="sltTipoAsociado" id="sltTipoAsociado" title="Seleccione tipo asociado" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($tipoAsociado)) {
                                    $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico = $tipoAsociado AND  clase=4 AND compania = $compania";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="'.$fila1[0].'">'.ucwords(mb_strtolower($fila1[1])).'</option>';
                                    $sql2 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico != $tipoAsociado AND  clase=4";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="' . $fila2[0] . '">' . ucwords(mb_strtolower($fila2[1])) . '</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Asociado</option>";
                                    $sql3 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE  clase=4 AND compania = $compania";
                                    $result3 = $mysqli->query($sql3);
                                    while ($fila3 = mysqli_fetch_row($result3)) {
                                        echo '<option value="' . $fila3[0] . '">' . $fila3[1] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltNumeroA" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nro Asociado:</label>
                            <select name="sltNumeroA" id="sltNumeroA" title="Seleccione número asociado" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if(!empty($id_asoc)) {
                                    $sqlAsociado = "SELECT DISTINCT mv.id_unico, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), tmv.nombre FROM        gf_movimiento mv
                                                    LEFT JOIN   gf_detalle_movimiento dtm       ON  dtm.movimiento      = mv.id_unico
                                                    LEFT JOIN   gf_tipo_movimiento tmv          ON  mv.tipomovimiento   = tmv.id_unico
                                                    WHERE       mv.id_unico=$idaso";
                                    $resultado = $mysqli->query($sqlAsociado);
                                    $fila = mysqli_num_rows($resultado);
                                    if ($fila != 0) {
                                        while ($row = mysqli_fetch_row($resultado)) {
                                            $sqlAM = "SELECT DISTINCT   (dtm.valor) as valor FROM gf_movimiento mv
                                                      LEFT JOIN         gf_detalle_movimiento dtm ON  dtm.movimiento = mv.id_unico
                                                      WHERE             mv.id_unico=$row[0]";
                                            $resultAM = $mysqli->query($sqlAM);
                                            $valAM = mysqli_fetch_row($resultAM);
                                            $sqlAF = "SELECT DISTINCT   IF(IFNULL((dtm.valor) - $valAM[0],$valAM[0])=0,$valAM[0],IFNULL((dtm.valor) - $valAM[0],$valAM[0])) as total
                                                      FROM                gf_movimiento mv
                                                      LEFT JOIN           gf_detalle_movimiento dtm   ON  dtm.movimiento = mv.id_unico
                                                      WHERE               mv.id_unico     =   $row[0]";
                                            $resultAF = $mysqli->query($sqlAF);
                                            $valAF = mysqli_fetch_row($resultAF);
                                            echo '<option value="' . $row[0] . '">' . ucwords(mb_strtolower('Nro:' . $row[1])) . ' -Saldo:' . number_format($valAF[0], 0, ',', '.') . ' -Fecha:' . $row[2] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">Nro Asociado</option>';
                                    }
                                }else{
                                    echo '<option value="">Nro Asociado</option>';
                                } ?>
                            </select>
                            <label for="sltTipoMov" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tipo Movimiento:</label>
                            <select name="sltTipoMov" id="sltTipoMov" title="Seleccione tipo movimiento" style="width: 10%;" class="col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($tipoMovimiento)) {
                                    $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico = $tipoMovimiento AND  clase IN (2,3) AND compania = $compania";
                                    $result1 = $mysqli->query($sql1);
                                    $fila1 = mysqli_fetch_row($result1);
                                    echo '<option value="' . $fila1[0] . '">' . ucwords(mb_strtolower($fila1[1])) . '</option>';
                                    $sql2 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE id_unico != $tipoMovimiento AND  clase IN (2,3) AND compania = $compania";
                                    $result2 = $mysqli->query($sql2);
                                    while ($fila2 = mysqli_fetch_row($result2)) {
                                        echo '<option value="' . $fila2[0] . '">' . ucwords(mb_strtolower($fila2[1])) . '</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Tipo Movimiento</option>";
                                    $sql1 = "SELECT DISTINCT id_unico,nombre FROM gf_tipo_movimiento WHERE  clase IN (2,3) AND compania = $compania";
                                    $result1 = $mysqli->query($sql1);
                                    while ($fila1 = mysqli_fetch_row($result1)) {
                                        echo '<option value="' . $fila1[0] . '">' . $fila1[1] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="txtNumero" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nro. Movimiento:</label>
                            <input type="text" name="txtNumero" id="txtNumero" title="Número de movimiento" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Número de movimiento" required readonly value="<?php  echo $numeroMovimiento; ?>">
                            <label for="txtFecha" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Fecha:</label>
                            <input type="text" name="txtFecha" id="txtFecha" title="Ingrese la fecha" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control" placeholder="Fecha" required readonly value="<?php echo $fecha; ?>" onchange="max_date_type($('#sltTipoMov').val(),this.value)">
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label for="sltCentroCosto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Centro Costo:</label>
                            <select name="sltCentroCosto" id="sltCentroCosto" title="Seleccione centro costo" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($centroCosto)) {
                                    $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $centroCosto";
                                    $result4 = $mysqli->query($sql4);
                                    $fila4 = mysqli_fetch_row($result4);
                                    echo '<option value="'.$fila4[0].'">'.ucwords(mb_strtolower($fila4[1])).'</option>';
                                    $sql5 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $centroCosto";
                                    $result5 = $mysqli->query($sql5);
                                    while ($fila5 = mysqli_fetch_row($result5)) {
                                        echo '<option value="'.$fila5[0].'">'.ucwords(mb_strtolower($fila5[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Centro Costo</option>";
                                    $sql6 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo";
                                    $result6 = $mysqli->query($sql6);
                                    while ($fila6 = mysqli_fetch_row($result6)) {
                                        echo '<option value="'.$fila6[0].'">'.ucwords(mb_strtolower($fila6[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltProyecto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Proyecto :</label>
                            <select name="sltProyecto" id="sltProyecto" title="Seleccione proyecto" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                                    echo "<option value=\"\">Proyecto</option>";
                                    $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto";
                                    $result9 = $mysqli->query($sql9);
                                    while ($fila9 = mysqli_fetch_row($result9)) {
                                        echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltDependencia" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Dependecia:</label>
                            <select name="sltDependencia" id="sltDependencia" title="Seleccione dependencia" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($dependencia)) {
                                    $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico = $dependencia";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1])).'</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE id_unico != $dependencia AND compania = $compania";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="'.$fila10[0].'">'.ucwords(mb_strtolower($fila10[1])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Dependencia</option>";
                                    $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia WHERE compania = $compania";
                                    $result11 = $mysqli->query($sql11);
                                    while ($fila11 = mysqli_fetch_row($result11)) {
                                        echo '<option value="'.$fila11[0].'">'.ucwords(mb_strtolower($fila11[1])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <label for="sltResponsable" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Responsable:</label>
                            <select name="sltResponsable" id="sltResponsable" title="Seleccione responsable" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                            <label for="sltRubroP" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Rubro Presupuestal:</label>
                            <select name="sltRubroP" id="sltRubroP" title="Seleccione rubro presupuestal" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control select2" required>
                                <?php
                                if (!empty($rubroPresupuestal)) {
                                    $sql9 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico = $rubroPresupuestal";
                                    $result9 = $mysqli->query($sql9);
                                    $fila9 = mysqli_fetch_row($result9);
                                    echo '<option value="'.$fila9[0].'">'.ucwords(mb_strtolower($fila9[1].' - '.$fila9[2])).'</option>';
                                    $sql10 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE id_unico != $rubroPresupuestal AND parametrizacionanno = $param";
                                    $result10 = $mysqli->query($sql10);
                                    while ($fila10 = mysqli_fetch_row($result10)) {
                                        echo '<option value="'.$fila10[0].'">'.ucwords(mb_strtolower($fila10[1].' - '.$fila10[2])).'</option>';
                                    }
                                } else {
                                    echo "<option value=\"\">Rubro Presuuestal</option>";
                                    $sql11 = "SELECT DISTINCT id_unico,codi_presupuesto,nombre FROM gf_rubro_pptal WHERE  parametrizacionanno = $param";
                                    $result11 = $mysqli->query($sql11);
                                    while ($fila11 = mysqli_fetch_row($result11)) {
                                        echo '<option value="'.$fila11[0].'">'.ucwords(mb_strtolower($fila11[1].' : '.$fila11[2])).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltTercero" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tercero :</label>
                            <select name="sltTercero" id="sltTercero" title="Seleccione tercero" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                                    echo '<option value=\"\">Tercero</option>';
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
                            <label for="sltUPE" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>U. Plazo Entrega:</label>
                            <select name="sltUPE" id="sltUPE" title="Seleccione unidad plazo entrega" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                            <label for="txtPlazoE" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Plazo de Entrega:</label>
                            <input type="number" name="txtPlazoE" id="txtPlazoE" title="Ingrese plazo de entrega" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Plazo Entrega" required value="<?php echo $PlazoEntrega; ?>">
                            <label for="sltLE" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Lugar Entrega:</label>
                            <select name="sltLE" id="sltLE" title="Seleccione lugar entrega" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                            <label for="txtIva" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>% Iva:</label>
                            <input type="number" name="txtIva" id="txtIva" title="Ingrese porcentaje de iva" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control" placeholder="% Iva" required value="<?php echo $iva; ?>">
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label for="sltDocSoporte" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tipo Doc. Soporte:</label>
                            <select name="sltDocSoporte" id="sltDocSoporte" title="Seleccione documento de soporte" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                            <label for="txtNumDocS" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nº Doc. Soporte:</label>
                            <input type="text" name="txtNumDocs" id="txtNumDocS" title="Ingrese número de documento soporte" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Nº Documento Soporte" required value="<?php echo $numDocSoporte ?>">
                            <label for="txtEstado" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Estado :</label>
                            <input type="text" name="txtEstado" id="txtEstado" title="Estado" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Estado" required value="<?php echo $estdo; ?>">
                            <label for="sltBuscar" class="col-lg-1 col-sm-1 control-label">Buscar Movimiento:</label>
                            <select name="sltBuscar" id="sltBuscar" title="Seleccione para buscar movimiento" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2 buscar">
                                <?php
                                echo "<option value=\"\">Buscar Movimiento</option>";
                                $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                                    IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                                        FROM        gf_movimiento mov
                                        LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                                        LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                                        WHERE       tpm.clase IN (2,3)
                                        AND         mov.compania = $compania 
                                        AND         mov.parametrizacionanno = $param";
                                $result = $mysqli->query($sql);
                                while ($row = mysqli_fetch_row($result)) {
                                    $sql_r = "SELECT SUM(dtm.valor+dtm.iva) FROM gf_detalle_movimiento dtm WHERE dtm.movimiento = $row[0]";
                                    $result_r = $mysqli->query($sql_r);
                                    $row_r = mysqli_fetch_row($result_r);
                                    echo "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($row_r[0],2,',','.')."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="txtDescripcion" class="col-lg-1 col-sm-1 control-label">Descripción:</label>
                            <textarea name="txtDescripcion" id="txtDescripcion" title="Ingrese descripción" style="margin-top: 0px;width: 28.2%;height: 40px" class="col-lg-1 col-sm-1 form-control" placeholder="Descripción" rows="5" cols="1000"><?php echo utf8_decode($descripcion); ?></textarea>
                            <label for="txtObservacion" class="col-lg-1 col-sm-1 control-label">Observaciones:</label>
                            <textarea name="txtObservacion" id="txtObservacion" title="Ingrese observaciones" style="margin-top: 0px;width: 28.5%;height: 40px" class="col-lg-1 col-sm-1 form-control" placeholder="Observaciones" rows="5" cols="1000"><?php echo $observaciones; ?></textarea>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 col-sm-push-10 col-lg-2 col-lg-push-10" style="margin-top: -110px;margin-bottom: 5px;">
                                <a id="btnNuevo" title="Ingresar nuevo" class="btn btn-primary shadow glyphicon glyphicon-plus text-center nuevo" onclick="javascript:nuevo()"></a>
                                <button type="submit" id="btnGuardar" title="Guardar movimiento" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk guardar"></button>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-lg-offset-10 col-sm-offset-10" style="margin-top: -70px">
                                <a id="btnImprimir" title="Imprimir" class="btn btn-primary shadow glyphicon glyphicon glyphicon-print imprimir"></a>
                                <a id="btnModificar" title="Modificar movimiento" class="btn btn-primary shadow glyphicon glyphicon-pencil modificar" onclick="modify_data()"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-10 col-sm-10 text-left" style="margin-top: 5px">
                <form name="form" id="formD" class="form-horizontal" method="POST" enctype="multipart/form-data" action="controller/controllerGFMovimiento.php?action=insert_detail">
                    <div class="form-group">
                        <input type="hidden" name="txtIdMov" id="txtIdMov" value="<?php echo $id; ?>" />
                        <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Plan Inventario:</label>
                        <select name="sltPlanInv" id="sltPlanInv" class="col-lg-1 col-sm-1 form-control" style="width:100px;height:26px;cursor: pointer" title="Seleccione elemento de plan inventario" required="">
                            <?php
                            echo '<option value="">Plan Inventario</option>';
                            $sql119 = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento=2 AND compania = $compania";
                            $result119 = $mysqli->query($sql119);
                            while ($fila119=  mysqli_fetch_row($result119)){
                                echo '<option value="'.$fila119[0].'">'.$fila119[2].' - '.$fila119[1].'</option>';
                            }
                            ?>
                        </select>
                        <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Cantidad:</label>
                        <input type="number" name="txtCantidad" class="form-control col-lg-1 col-sm-1" id="txtCantidad" title="Cantidad" onkeypress="return txtValida(event,'num');" maxlength="10" placeholder="Cantidad" style="padding:2px;width:80px;font-size: 10px;"/>
                        <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Valor:</label>
                        <input type="number" name="txtValor" id="txtValor" class="form-control col-sm-1" title="Valor aproximado" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Valor" style="padding:2px;width:110px;font-size: 10px;"/>
                        <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Iva:</label>
                        <input type="number" name="txtValorIva" id="txtValorIva" class="form-control col-lg-1 col-sm-1" title="Iva" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Iva" style="padding:2px;width:100px;font-size: 10px;" readonly/>
                        <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Total:</label>
                        <input type="number" name="txtValorTotal" id="txtValorTotal"  class="form-control col-lg-1 col-sm-1" title="Valor total" onkeypress="return txtValida(event,'num');"  maxlength="50" placeholder="Valor Total" style="padding:2px;width:100px;font-size: 10px;margin-right: 10px" required="" readonly/>
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
                echo "$(\"#btnNuevo, #btnModificar, #btnImprimir, #btnGuardarDetalle\").attr('disabled',true);";
                echo "$(\"#btnNuevo, #btnModificar, #btnImprimir, #btnGuardarDetalle\").removeAttr('onclick');";
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
                    <div class="table-responsive">
                        <table id="tablaM" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza"><strong>Item</strong></td>
                                    <td class="cabeza"><strong>Plan Inventario</strong></td>
                                    <td class="cabeza"><strong>Cantidad</strong></td>
                                    <td class="cabeza"><strong>Valor Aproximado</strong></td>
                                    <td class="cabeza"><strong>Iva</strong></td>
                                    <td class="cabeza"><strong>Valor Total</strong></td>
                                </tr>
                                <tr>
                                    <th width="7%"  class="cabeza"></th>
                                    <th class="cabeza"><strong>Item</strong></th>
                                    <th class="cabeza"><strong>Plan Inventario</strong></th>
                                    <th class="cabeza"><strong>Cantidad</strong></th>
                                    <th class="cabeza"><strong>ValorAproximado</strong></th>
                                    <th class="cabeza"><strong>Iva</strong></th>
                                    <th class="cabeza"><strong>Valor Total</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $item=1;
                                $sumC = 0;
                                $sumV = 0;
                                $sumIva = 0;
                                $totalmov = 0;
                                $sumIva = 0;
                                if(empty($idMov) && !empty($id_asoc)) {
                                    $idMov = $id_asoc;
                                }
                                $det = "SELECT  dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva, pl.codi
                                        FROM gf_detalle_movimiento dtm
                                        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                        WHERE md5(mv.id_unico)='$idMov'";
                                $resultado2 = $mysqli->query($det);
                                while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                                    <tr>
                                        <td class="campos">
                                            <a href="#<?php echo $row2[0];?>" title="Eliminar" class="eliminar" id="del" onclick="delete_detail(<?php echo $row2[0];?>);">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="#<?php echo $row2[0];?>" title="Modificar" class="modificar" id="mod" onclick="show_input(<?php echo $row2[0]; ?>);">
                                                <li class="glyphicon glyphicon-edit"></li>
                                            </a>
                                        </td>
                                        <td class="campos" class="text-left" width="7%">
                                            <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblItem'.$row2[0].'">'.$item++.'</label>'; ?>
                                        </td>
                                        <td class="campos" class="text-left">
                                            <?php
                                            echo '<label  class="valorLabel" style="font-weight:normal" id="lblCodigoE'.$row2[0].'">'.$row2[7].' - '.$row2[5].'</label>'; ?>
                                            <select class="col-lg-12 col-sm-12 campoD" name="sltPlanInventario<?php echo $row2[0] ?>" id="sltPlanInventario<?php echo $row2[0] ?>" title="Seleccione elemento de plan inventario" style="display:none;padding:2px">
                                                <?php
                                                echo '<option value="'.$row2[4].'">'.$row2[7].' - '.$row2[5].'</option>';
                                                $sqlPL = "SELECT id_unico,nombre,codi FROM gf_plan_inventario WHERE tienemovimiento=2 AND id_unico!=$row2[4]";
                                                $resultPL = $mysqli->query($sqlPL);
                                                while ($filaPL = mysqli_fetch_row($resultPL)){
                                                    echo '<option value="'.$filaPL[0].'">'.$filaPL[2].' - '.$filaPL[1].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="campos text-right">
                                            <?php
                                            $sumC+=$row2[2];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row2[0].'">'.$row2[2].'</label>';
                                            echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-lg-12 col-sm-12 campoD text-left" onkeyup=\'calcule_values('.$row2[0].')\' type="number" name="txtcantidad'.$row2[0].'" id="txtcantidad'.$row2[0].'" value="'.$row2[2].'" />';
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php
                                            $sumV+=$row2[3];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="ValorT'.$row2[0].'">'.number_format($row2[3],2,',','.').'</label>';
                                            echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-lg-12 col-sm-12 campoD text-left" onkeyup="calcule_values(\''.$row2[0].'\')" type="number" name="txtvalor'.$row2[0].'" id="txtvalor'.$row2[0].'" value="'.$row2[3].'" />';
                                            ?>
                                        </td>
                                        <td class="campos text-right">
                                            <?php
                                            $sumIva +=$row2[6];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row2[0].'">'.number_format($row2[6],2,',','.').'</label>';
                                            echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-lg-12 col-sm-12 campoD text-left"  type="number" name="txtiva'.$row2[0].'" id="txtiva'.$row2[0].'" value="'.$row2[6].'" readonly/>';
                                            ?>
                                        </td>
                                        <td class="campos text-right" style="height:10px;font-size:10px">
                                            <?php
                                            $mov=(($row2[2]*$row2[3]));
                                            $total = $mov+$row2[6];
                                            $totalmov+=$total;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="lblValorTotal'.$row2[0].'">'.number_format($total, 2, ',', '.').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;" class="col-lg-9 col-sm-9 campoD text-left"  type="number" name="txttotal'.$row2[0].'" id="txttotal'.$row2[0].'" value="'.$total.'" readonly/>';
                                            ?>
                                            <div >
                                                <table id="tab<?php echo $row2[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-lg-1 col-sm-1">
                                                    <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row2[0];?>" title="Guardar" id="guardar<?php echo $row2[0]; ?>" style="display: none;" onclick="save_values_detail(<?php echo $row2[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row2[0];?>" title="Cancelar" id="cancelar<?php echo $row2[0] ?>" style="display: none" onclick="cancel_modify(<?php echo $row2[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
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
                        <button type="button" id="btnNoModifico" class="btn" onclick="reload_page()" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
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
                        <button type="button" id="ver1" onclick="reload_page();" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
        <div class="modal fade" id="mdlfecha" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>La fecha ingresada es menor a la fecha del ultimo movimiento.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include ('footer.php'); ?>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        /**
         * Cargamos a la clase select2
         */
        $(".select2").select2();
        $("#sltPlanInv").select2();
        /**
         * Cuando el combo cambie su valor,  consulte y envie por ajax
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
         * Cuando el combo cambie su valor, redireccionara y traera los datos del asociado
         */
        $("#sltNumeroA").change(function () {
            var form_data = {
                mov:1,
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
         * Cuando se seleccione tipo de movimiento trae el valor maximo
         */
        $("#sltTipoMov").change(function () {
            var form_data = {
                mov:2,
                tipo:$("#sltTipoMov").val()
            };
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consulta_mov.php',
                data:form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#txtNumero").val(data);
                }
            }).error(function(data, textStatus, jqXHR) {
                console.log('data :'+data+', textStatus :'+textStatus+', jqXHR :'+jqXHR);
            });
        });

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

        $("#sltBuscar").change(function() {
            var form_data = {
                mov:3,
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
            window.location = 'registrar_RF_MOVIMIENTO_ALMACEN.php';
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
        /**
         * Función para recargar la pagina
         */
        function reload_page() {
            window.location.reload();
        }

        /**
         * Función para generar el valor total del iva y el valor total cuando se escribe en el campo valor
         * @type {number}
         */
        var valor = 0.00;
        var iva = 0.00;
        var totalIva = 0.00;
        var total = 0.00;
        $("#txtValor").keyup(function(){
            var cantidad = $("#txtCantidad").val();
            if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                cantidad = 1;
            }else{
                cantidad = parseFloat($("#txtCantidad").val());
            }
            valor = ($("#txtValor").val());
            iva = parseFloat(<?php echo $porcIva; ?>);
            total = cantidad*valor;
            totalIva = (total*iva)/100;
            $("#txtValorIva").val(totalIva.toFixed(2));
            total = total+totalIva;
            $("#txtValorTotal").val(total.toFixed(2));
        });

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
        /**
         * Función para mostrar los campos ocultos
         * @param {type} id
         */
        function show_input(id) {
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
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
            $("#idActual").val(id);
            //carga del campo oculto con la id anterior
            if($("#idPrevio").val() != id){
                $("#idPrevio").val(id);
            }
        }

        function calcule_values(id) {
            var txtCantidad = 'txtcantidad'+id;
            var txtValor = 'txtvalor'+id;
            var txtIva = 'txtiva'+id;
            var txtTotal = 'txttotal'+id;

            var cantidad = $("#"+txtCantidad).val();
            var valor = $("#"+txtValor).val();
            var iva = parseFloat(<?php echo $porcIva; ?>);

            if (cantidad === 0 || cantidad === "" || cantidad.length === 0 || cantidad === null) {
                cantidad = 0;
            }else {
                cantidad = parseFloat($("#"+txtCantidad).val());
            }

            var totalC = valor * cantidad;
            var totalIva = (totalC * iva) / 100;
            var total = totalC + totalIva;
            $("#"+txtIva).val(totalIva.toFixed(2));
            $("#"+txtTotal).val(total);
        }

        /**
         * Función para cancelar el proceso de actualización
         * @param id
         */
        function cancel_modify (id) {
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

        /**
         * Función para guardar los valores modificados en el detalle
         * @param id
         * @type {number}
         */
        function save_values_detail(id) {
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
         * Función para limpieza de campos
         */
        function clean_inputs (){
            $("#sltPlanInv").select2('val','');
            $("#txtCantidad").val("");
            $("#txtValor").val("");
            $("#txtValorIva").val("");
            $("#txtValorTotal").val("");
        }
    </script>
</body>
</html>
