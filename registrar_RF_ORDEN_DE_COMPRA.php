<?php
#################################################################################
# ****************************** MODIFICACIONES ******************************* #
#################################################################################                           
#08/11/2018 |Erica G.  | Parametrizacion, compañia 
#################################################################################                           
include ('head_listar.php');
include ('Conexion/conexion.php');
include ('funciones/funciones_mov.php');
$compania = $_SESSION['compania'];
$param    = $_SESSION['anno'];
/**
 * Inicializamos las variables en 0 o vacio
 */
list(
    $id, $idMov, $tipoMovimiento, $numeroMovimiento, $fecha, $dependencia, $responsable, $centroCosto, $proyecto, $estado, $descripcion,
    $observaciones, $tercero, $rubroPresupuestal, $porcIva, $PlazoEntrega, $unidadPlazo, $lugarEntrega, $idR, $idReq, $iva, $idasoc, $idaso,
    $asociados, $tercero, $tipoDocSoporte, $numDocSoporte
) = array(0, '', '', '', date('d/m/Y'), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','', '', '', '', '', '');
$sumC = 0;$sumV = 0;$sumIva = 0;
if(!empty($_GET['movimiento'])){
    $idMov = $_GET['movimiento'];
    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia, mv.tercero,
                    mv.rubropptal, mv.estado, mv.plazoentrega, mv.unidadentrega, mv.lugarentrega, mv.descripcion, mv.observaciones, mv.id_unico, mv.porcivaglobal, mv.tercero2, mv.tipo_doc_sop, mv.numero_doc_sop
            FROM    gf_movimiento mv
            WHERE   md5(id_unico)='$idMov'";
    $result = $mysqli->query($sql);
    $rowPrimaria = mysqli_fetch_row($result);
    list($tipoMovimiento, $numeroMovimiento, $fecha, $centroCosto, $proyecto, $dependencia, $responsable, $rubroPresupuestal, $estado,
    $PlazoEntrega, $unidadPlazo, $lugarEntrega, $descripcion, $observaciones, $id, $iva, $porcIva, $tercero, $tipoDocSoporte, $numDocSoporte)
    = array($rowPrimaria[0], $rowPrimaria[1], $rowPrimaria[2], $rowPrimaria[3], $rowPrimaria[4], $rowPrimaria[5], $rowPrimaria[6],
        $rowPrimaria[7], $rowPrimaria[8], $rowPrimaria[9], $rowPrimaria[10], $rowPrimaria[11], $rowPrimaria[12], $rowPrimaria[13],
        $rowPrimaria[14], $rowPrimaria[15], $rowPrimaria[15], $rowPrimaria[16], $rowPrimaria[17], $rowPrimaria[18]);
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

if(!empty($_GET['asociado'])) {
    $idasoc = $_GET['asociado'];
    $sql = "SELECT  mv.tipomovimiento, mv.numero, DATE_FORMAT(mv.fecha,'%d/%m/%Y'), mv.centrocosto, mv.proyecto, mv.dependencia, mv.tercero,
                    mv.rubropptal, mv.estado, mv.plazoentrega,mv.unidadentrega, mv.lugarentrega, mv.descripcion, mv.observaciones, mv.id_unico, mv.porcivaglobal, mv.tercero2, mv.tipo_doc_sop, mv.numero_doc_sop
            FROM    gf_movimiento mv
            WHERE   md5(id_unico)='$idasoc'";
    $result = $mysqli->query($sql);
    $rowA = mysqli_fetch_row($result);
    $fecha = $rowA[2]; $centroCosto = $rowA[3]; $proyecto = $rowA[4]; $dependencia = $rowA[5]; $responsable = $rowA[6]; $rubroPresupuestal = $rowA[7]; $estado = $rowA[8]; $PlazoEntrega = $rowA[9]; $unidadPlazo = $rowA[10]; $lugarEntrega = $rowA[11]; $descripcion = $rowA[12]; $observaciones = $rowA[13]; $idaso = $rowA[14]; $iva = $rowA[15]; $porcIva = $rowA[15]; $tercero = $rowA[16]; $tipoDocSoporte = $rowA[17]; $numDocSoporte = $rowA[18];
    $sqlEstado = "SELECT nombre FROM gf_estado_movimiento WHERE id_unico = $estado";
    $resEstado = $mysqli->query($sqlEstado);
    $estd = mysqli_fetch_row($resEstado);
    $estdo = $estd[0];
}

if(!empty($_GET['req'])) {
    $asociados = $_GET['req'];
}

if(empty($estdo)) {
    $sqlE = "SELECT id_unico,nombre FROM gf_estado_movimiento WHERE nombre = 'Generado'";
    $resultE = $mysqli->query($sqlE);
    $rowE = mysqli_fetch_row($resultE);
    $estdo = $rowE[1];
}

$sq_ = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 10";
$rs_ = $mysqli->query($sq_);
$dc_ = $rs_->fetch_row();
?>
    <title>Orden de Compra Almacén</title>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
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

            var i= 1;
            $('#tablaRequisiciones thead th').each( function () {
                if(i != 0){
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:110%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });

            var tableR = $("#tablaRequisiciones").DataTable({
                "autoFill": true,
                "scrollX": true,
                "processing": true,
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
                    'orderable':false

                }]
            });

            var i = 0;
            tableR.columns().every( function () {
                var that = this;
                if(i!=0){
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
    <body onload="clean_inputs()">
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require 'menu.php'; ?>
                <div class="col-sm-10 col-lg-10 text-left">
                    <h2 align="center" style="margin-top: 0px" class="tituloform">Orden de Compra Almacén</h2>
                    <div style="margin-top:-7px; border: 4px solid #020324; border-radius: 10px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFOrdenCompraAlmacen.php?action=insert" style="margin-bottom: -30px;margin-left: 10px">
                            <p align="center" class="parrafoO" style="margin-bottom:5px">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>
                            <div class="form-group form-inline" style="margin-top: 5px;">
                                <label for="txtAsociado" class="col-sm-1 col-lg-1 control-label">Requisición: </label>
                                <a id="btnAsociado" class="btn btn-primary shadow col-sm-1 col-lg-1 glyphicon glyphicon-tags" style="width: 10%" onclick="open_modal_r()"></a>
                                <input type="hidden" name="txtAsociado" id="txtAsociado" title="Seleccione una ó mas requisiciones" value="<?php echo $asociados; ?>" >
                                <label for="sltTipoMov" class="col-sm-1 col-lg-1 control-label"><strong class="obligado">*</strong>Tipo Movimiento:</label>
                                <select name="sltTipoMov" id="sltTipoMov" title="Seleccione tipo movimiento" style="width: 10%;" class="col-sm-1 col-lg-1 form-control select2" required>
                                    <?php
                                    if (!empty($tipoMovimiento)) {
                                        $sql1 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                            FROM gf_tipo_movimiento WHERE id_unico = $tipoMovimiento AND clase=1";
                                        $result1 = $mysqli->query($sql1);
                                        $fila1 = mysqli_fetch_row($result1);
                                        echo '<option value="'.$fila1[0].'">'.ucwords(mb_strtolower($fila1[1]))." ".$fila1[2].'</option>';
                                        $sql2 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) 
                                            FROM gf_tipo_movimiento WHERE id_unico != $tipoMovimiento AND clase=1 AND compania =$compania";
                                        $result2 = $mysqli->query($result2);
                                        while ($fila2 = mysqli_fetch_row($result2)) {
                                            echo '<option value="'.$fila2[0].'">'.ucwords(mb_strtolower($fila2[1]))." ".$fila2[2].'</option>';
                                        }
                                    } else {
                                        echo "<option value=\"\">Tipo Movimiento</option>";
                                        $sql3 = "SELECT DISTINCT id_unico, nombre, UPPER(sigla) FROM gf_tipo_movimiento WHERE compania =$compania AND clase=1";
                                        $result3 = $mysqli->query($sql3);
                                        while ($fila3 = mysqli_fetch_row($result3)) {
                                            echo '<option value="' . $fila3[0] . '">' . $fila3[1]." ".$fila3[2] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="txtNumero" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Nro. Movimiento:</label>
                                <input type="text" name="txtNumero" id="txtNumero" title="Número de movimiento" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Número de movimiento" required readonly value="<?php  echo $numeroMovimiento; ?>">
                                <label for="fecha" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Fecha:</label>
                                <input type="text" name="txtFecha" id="txtFecha" title="Ingrese la fecha" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Fecha" required readonly value="<?php echo $fecha; ?>" onchange="max_date_type($('#sltTipoMov').val(),this.value)">
                                <label for="sltCentroCosto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Centro Costo:</label>
                                <select name="sltCentroCosto" id="sltCentroCosto" title="Seleccione centro costo" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control select2" required>
                                    <?php
                                    if (!empty($centroCosto)) {
                                        $sql4 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $centroCosto";
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
                                           WHERE nombre='varios' AND parametrizacionanno =$param";
                                        $result4 = $mysqli->query($sql4);
                                        $fila4 = mysqli_fetch_row($result4);
                                        echo '<option value="'.$fila4[0].'">'.ucwords(mb_strtolower($fila4[1])).'</option>';
                                        $sql6 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo 
                                            WHERE nombre != 'varios' 
                                            AND parametrizacionanno = $param";
                                        $result6 = $mysqli->query($sql6);
                                        while ($fila6 = mysqli_fetch_row($result6)) {
                                            echo '<option value="'.$fila6[0].'">'.ucwords(mb_strtolower($fila6[1])).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline" style="margin-top:-15px">
                                <label for="sltProyecto" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Proyecto :</label>
                                <select name="sltProyecto" id="sltProyecto" title="Seleccione proyecto" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2" required>
                                    <?php
                                    if (!empty($proyecto)) {
                                        $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico = $proyecto";
                                        $result7 = $mysqli->query($sql7);
                                        $fila7 = mysqli_fetch_row($result7);
                                        echo '<option value="' . $fila7[0] . '">' . ucwords(mb_strtolower($fila7[1])) . '</option>';
                                        $sql8 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $proyecto";
                                        $result8 = $mysqli->query($sql8);
                                        while ($fila8 = mysqli_fetch_row($result8)) {
                                            echo '<option value="' . $fila8[0] . '">' . ucwords(mb_strtolower($fila8[1])) . '</option>';
                                        }
                                    } else {
                                        $sql7 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto 
                                            WHERE nombre = 'varios'";
                                        $result7 = $mysqli->query($sql7);
                                        $fila7 = mysqli_fetch_row($result7);
                                        echo '<option value="' . $fila7[0] . '">' . ucwords(mb_strtolower($fila7[1])) . '</option>';
                                        $sql9 = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto  WHERE nombre != 'varios'";
                                        $result9 = $mysqli->query($sql9);
                                        while ($fila9 = mysqli_fetch_row($result9)) {
                                            echo '<option value="' . $fila9[0] . '">' . ucwords(mb_strtolower($fila9[1])) . '</option>';
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
                                        $sql10 = "SELECT DISTINCT id_unico,nombre 
                                            FROM gf_dependencia WHERE id_unico != $dependencia AND compania = $compania 
                                            #AND tipodependencia != 1";
                                        $result10 = $mysqli->query($sql10);
                                        while ($fila10 = mysqli_fetch_row($result10)) {
                                            echo '<option value="'.$fila10[0].'">'.ucwords(mb_strtolower($fila10[1])).'</option>';
                                        }
                                    } else {
                                        echo "<option value=\"\">Dependencia</option>";
                                        $sql11 = "SELECT DISTINCT id_unico,nombre FROM gf_dependencia 
                                            WHERE compania = $compania #AND tipodependencia != 1";
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
                                        echo '<option value="' . $fila12[1] . '">' . ucwords(mb_strtolower($fila12[0])) . '</option>';
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
                                            echo '<option value="' . $fila5[1] . '">' . ucwords(mb_strtolower($fila5[0])) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">Responsable</option>';
                                    }
                                    ?>
                                </select>
                                <label for="sltRubroP" class="col-lg-1 col-sm-1 control-label">Rubro Presupuestal:</label>
                                <select name="sltRubroP" id="sltRubroP" title="Seleccione rubro presupuestal" style="width: 10%" class="col-lg-1 col-sm-1 form-control select2">
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
                                <label for="txtIva" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>% Iva:</label>
                                <input type="number" name="txtIva" id="txtIva" title="Ingrese porcentaje de iva" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control" placeholder="% Iva" required value="<?php if(!empty($iva)){echo $iva;} else{ echo 0;} ?>">
                            </div>
                            <div class="form-group form-inline" style="margin-top:-15px">
                                <label for="txtEstado" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Estado :</label>
                                <input type="text" name="txtEstado" id="txtEstado" title="Estado" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Estado" required value="<?php echo $estdo; ?>">
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
                                <label for="sltTercero" class="col-lg-1 col-sm-1 control-label"><strong class="obligado">*</strong>Tercero :</label>
                                <select name="sltTercero" id="sltTercero" title="Seleccione tercero" style="width: 10.5%" class="col-lg-1 col-sm-1 form-control select2" required>
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
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
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
                                <input type="text" name="txtNumDocS" id="txtNumDocS" title="Ingrese número de documento soporte" style="width: 10%" class="col-lg-1 col-sm-1 form-control" placeholder="Nº Documento Soporte" required value="<?php echo $numDocSoporte ?>">
                                <label for="txtDescripcion" class="col-lg-1 col-sm-1 control-label">Descripción:</label>
                                <textarea name="txtDescripcion" id="txtDescripcion" title="Ingrese descripción" style="margin-top: 0px;width: 28.5%;height: 34px" class="col-lg-1 col-sm-1 form-control" placeholder="Descripción" rows="5" cols="1000"><?php echo ($descripcion); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label for="txtObservacion" class="col-sm-1 control-label">Observaciones:</label>
                                <textarea name="txtObservacion" id="txtObservacion" title="Ingrese observaciones" style="margin-top: 0px;width: 28.5%;height: 34px" class="col-lg-1 col-sm-1 form-control" placeholder="Observaciones" rows="5" cols="1000"><?php echo $observaciones; ?></textarea>
                                <label for="sltBuscar" class="col-lg-1 col-sm-1 control-label">Buscar Movimiento:</label>
                                <select name="sltBuscar" id="sltBuscar" title="Seleccione para buscar movimiento" style="width: 28.5%" class="col-lg-1 col-sm-1 form-control select2 buscar">
                                    <?php
                                    echo "<option value=\"\">Buscar Movimiento</option>";
                                    $sql = "SELECT      mov.id_unico,CONCAT(tpm.sigla,' ',mov.numero,' ',DATE_FORMAT(mov.fecha,'%d/%m/%Y')),
                                                        IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),
                                                            CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE'
                                            FROM        gf_movimiento mov
                                            LEFT JOIN   gf_tipo_movimiento tpm  ON mov.tipomovimiento = tpm.id_unico
                                            LEFT JOIN   gf_tercero ter          ON ter.id_unico = mov.tercero2
                                            WHERE       tpm.clase IN (1)
                                            AND         mov.parametrizacionanno = $param
                                            AND         mov.compania = $compania 
                                            ORDER BY cast(mov.numero as unsigned) DESC";
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
                            <div class="form-group">
                                <div class="col-sm-2 col-sm-push-10 col-lg-2 col-lg-push-10" style="margin-top: -100px;margin-bottom: 5px;">
                                    <a id="btnNuevo" title="Ingresar nuevo" class="btn btn-primary shadow glyphicon glyphicon-plus text-center nuevo" onclick="javascript:nuevo()"></a>
                                    <button type="submit" id="btnGuardar" title="Guardar movimiento" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk guardar"></button>
                                </div>
                                <div class="col-lg-2 col-sm-2 col-lg-offset-10 col-sm-offset-10" style="margin-top: -60px">
                                    <a id="btnImprimir" title="Imprimir" class="btn btn-primary shadow glyphicon glyphicon glyphicon-print imprimir" onclick="open_report(<?php echo $id; ?>)"></a>
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
                            <input type="number" name="txtValor" id="txtValor" class="form-control col-lg-1 col-sm-1" title="Valor aproximado" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Valor" style="padding:2px;width:110px;font-size: 10px;"/>
                            <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Iva:</label>
                            <input type="number" name="txtValorIva" id="txtValorIva" class="form-control col-lg-1 col-sm-1" title="Iva" onkeypress="return txtValida(event,'num');" maxlength="50" placeholder="Iva" style="padding:2px;width:100px;font-size: 10px;margin-right: 10px" readonly/>
                            <input type="text" name="txtAjuste" id="txtAjuste"  class="form-control col-lg-1 col-sm-1" title="Ajuste"  maxlength="50" placeholder="Ajuste" style="padding:2px;font-size: 10px;width: 80px" value="0"/>
                            <label class="control-label col-lg-1 col-sm-1"><strong class="obligado">*</strong>Total:</label>
                            <input type="number" name="txtValorTotal" id="txtValorTotal"  class="form-control col-lg-1 col-sm-1" title="Valor total" onkeypress="return txtValida(event,'num');"  maxlength="50" placeholder="Valor Total" style="padding:2px;width:100px;font-size: 10px;margin-right: 10px" required="" readonly/>
                            <button type="submit" class="btn btn-primary shadow guardar" id="btnGuardarDetalle"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                        </div>
                    </form>
                </div>
                <?php
                //Validamos que cuando la variable movimiento no esta vacia inhabilitamos el botón de guardado
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
                                if(!empty($idMov)){
                                    $det = "SELECT    dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva, pl.codi
                                            FROM      gf_detalle_movimiento dtm
                                            LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                            WHERE     md5(mv.id_unico)='$idMov'";
                                    $resultado2 = $mysqli->query($det);
                                    while ($row2 = mysqli_fetch_row($resultado2)) { ?>
                                        <tr>
                                            <td class="campos">
                                                <a href="#<?php echo $row2[0];?>" title="Eliminar" id="del" class="eliminar" onclick="delete_detail(<?php echo $row2[0];?>);">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                                <a href="#<?php echo $row2[0];?>" title="Modificar" id="mod" class="modificar" onclick="show_input(<?php echo $row2[0]; ?>);">
                                                    <li class="glyphicon glyphicon-edit"></li>
                                                </a>
                                            </td>
                                            <td class="campos text-left" width="7%">
                                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblItem'.$row2[0].'">'.$item++.'</label>'; ?>
                                            </td>
                                            <td class="campos" class="text-left">
                                                <?php
                                                echo '<label  class="valorLabel" style="font-weight:normal" id="lblCodigoE'.$row2[0].'">'.$row2[7].' - '.$row2[5].'</label>'; ?>
                                                <select class="col-sm-12 campoD" name="sltPlanInventario<?php echo $row2[0] ?>" id="sltPlanInventario<?php echo $row2[0] ?>" title="Seleccione elemento de plan inventario" style="display:none;padding:2px">
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
                                                echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-sm-12 campoD text-left" onkeyup=\'calcule_values('.$row2[0].')\'  type="number" name="txtcantidad'.$row2[0].'" id="txtcantidad'.$row2[0].'" value="'.$row2[2].'" />';
                                                ?>
                                            </td>
                                            <td class="campos text-right">
                                                <?php
                                                $sumV+=$row2[3];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="ValorT'.$row2[0].'">'.number_format($row2[3],2,',','.').'</label>';
                                                echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-sm-12 campoD text-left" onkeyup="calcule_values(\''.$row2[0].'\')" type="number" name="txtvalor'.$row2[0].'" id="txtvalor'.$row2[0].'" value="'.$row2[3].'" />';
                                                ?>
                                            </td>
                                            <td class="campos text-right">
                                                <?php
                                                $sumIva +=$row2[6];
                                                echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row2[0].'">'.number_format($row2[6],2,',','.').'</label>';
                                                echo '<input maxlength="50" onkeypress="txtValida(event,\'num\')" style="display:none;padding:2px;" class="col-sm-12 campoD text-left"  type="number" name="txtiva'.$row2[0].'" id="txtiva'.$row2[0].'" value="'.$row2[6].'" readonly/>';
                                                ?>
                                            </td>
                                            <td class="campos text-right" style="height:10px;font-size:10px">
                                                <?php
                                                $total = ($row2[3]+$row2[6]) *$row2[2];
                                                $totalmov+=$total;
                                                echo '<label class="valorLabel" style="font-weight:normal" id="lblValorTotal'.$row2[0].'">'.number_format($total, 2, ',', '.').'</label>';
                                                echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;" class="col-sm-9 campoD text-left"  type="number" name="txttotal'.$row2[0].'" id="txttotal'.$row2[0].'" value="'.$total.'" readonly/>';
                                                ?>
                                                <div >
                                                    <table id="tab<?php echo $row2[0] ?>" style="padding:0px;background-color:transparent;background:transparent;" class="col-sm-1">
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
                                    }

                                if(!empty($asociados)) {
                                    $html = "";
                                    $sql_d = "SELECT  dtm.id_unico, dtm.planmovimiento, dtm.cantidad, dtm.valor, pl.id_unico, pl.nombre, dtm.iva , pl.codi
                                            FROM      gf_detalle_movimiento dtm
                                            LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
                                            LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
                                            WHERE     mv.id_unico IN ($asociados)";
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
                                            $html .= "<td class='campos'>".ucwords(mb_strtolower($rowa[7]." ".$rowa[5]))."</td>";
                                            $html .= "<td class='campos'>$xxx</td>";
                                            $html .= "<td class='campos'>".number_format($rowa[3], 2, ',', '.')."</td>";
                                            $html .= "<td class='campos'>".number_format($rowa[6], 2, ',', '.')."</td>";
                                            $vtt = ($rowa[3] + $rowa[6]) * $xxx;
                                            $html .= "<td class='campos'>".number_format($vtt, 2, ',', '.')."</td>";
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
                </div>
                <div class="col-lg-10 col-sm-10 text-right">
                    <label for="" class="control-label col-lg-1 col-sm-1 col-lg-offset-4 col-sm-offset-4">Totales :</label>
                    <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Valor Aproximado"><?php echo "$".number_format($sumV,2,',','.'); ?></label>
                    <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Iva"><?php echo "$".number_format($sumIva,2,',','.') ?></label>
                    <label for="" class="control-label col-lg-2 col-sm-2 values" title="Total Valor Total"><?php echo "$".number_format($totalmov,2,',','.')?></label>
                </div>
            </div>
        </div>
        <?php
        if(!empty($idaso)) {
            echo "<script>$(\".eliminar\").css('display','none');$(\".modificar\").css('display','none');</script>";
        }
        ?>
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
        <div class="modal fade" id="modalRequisicones" role="dialog">
            <div class="modal-dialog" style="max-width:500px">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <button type="button" class="btn btn-xs close" aria-label="Close" style="color: #fff;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;" align="center">Requisiciones</h4>
                    </div>
                    <table id="tablaRequisiciones" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td class="cabeza"><strong>Tipo</strong></td>
                            <td class="cabeza"><strong>Número</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                            <td class="cabeza" width="1%"><strong></strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza">Tipo</th>
                            <th class="cabeza">Número</th>
                            <th class="cabeza">Cantidad</th>
                            <th class="cabeza">Valor</th>
                            <th class="cabeza" width="1%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sqlReq = "SELECT    mv.id_unico, tpm.sigla, mv.numero
                                   FROM      gf_movimiento mv
                                   LEFT JOIN gf_tipo_movimiento tpm ON tpm.id_unico = mv.tipomovimiento 
                                   WHERE (tpm.clase = 4) AND tpm.compania = $compania";
                        $resReq = $mysqli->query($sqlReq);
                        $datReq = $resReq->fetch_all(MYSQLI_NUM);
                        foreach ($datReq as $rowReq) {
                            list($sumValorD , $sumValorDT, $cantidad, $xxCant, $vvU)  = array(0, 0, 0, 0, 0); $xxx = array();
                            $sql_ = "SELECT id_unico, cantidad, (valor + iva) * cantidad  FROM gf_detalle_movimiento WHERE movimiento = $rowReq[0]";
                            $res_ = $mysqli->query($sql_);
                            $datO = $res_->fetch_all(MYSQLI_NUM);
                            foreach ($datO as $rowO) {
                                $sumValorDT += $rowO[2];
                                $xxCant     += $rowO[1];
                                $xxx[]      =  $rowO[0];
                            }

                            for ($i = 0; $i < count($xxx); $i++) {
                                $sq_  = "SELECT cantidad, (valor + iva) * cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $xxx[$i]";
                                $re_  = $mysqli->query($sq_);
                                $datE = $re_->fetch_all(MYSQLI_NUM);
                                foreach ($datE as $rowE) {
                                    $cantidad  += $rowE[0];
                                    $sumValorD += $rowE[1];
                                }
                            }

                            $sumD = $sumValorDT - $sumValorD;
                            $xxxC = $xxCant     - $cantidad;
                            $html = "";
                            if($sumD > 0) {
                                $html .= "\n\t<tr>";
                                $html .= "\n\t\t<td class=\"campos text-left\">$rowReq[1]</td>";
                                $html .= "\n\t\t<td class=\"campos text-right\">".$rowReq[2]."</td>";
                                $html .= "\n\t\t<td class=\"campos text-right\">".$xxxC."</td>";
                                $html .= "\n\t\t<td class=\"campos text-right\">".number_format($sumD,2,',','.')."</td>";
                                $html .= "\n\t\t<td class=\"campos text-right\" width=\"1%\"><input name=\"chkActivar[]\" id=\"chkActivar".$rowReq[0]."\" value=\"".$rowReq[0]."\" type=\"checkbox\"/></td>";
                                $html .= "\n\t</tr>";
                            }
                            echo $html;
                        }
                        ?>
                        </tbody>
                    </table>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnReq" class="btn" onclick="markeds()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'footer.php';?>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            $(".select2").select2();
            $("#sltPlanInv").select2();
            /**
             * Cuando el combo cambie su valor,  consulte y envie por ajax
             */
            $("#sltTipoAsociado").change(function(){
                var form_data = {
                    existente:10,
                    tipo:+$("#sltTipoAsociado").val()
                };
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/consultarNumeros.php",
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
                    mov:6,
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
                window.location = 'registrar_RF_ORDEN_DE_COMPRA.php';
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

            function redondeo(valor, decimales){
                var flt = parseFloat(valor);
                var res = Math.round(flt * Math.pow(10, decimales)) / Math.pow(10, decimales);
                return res;
            }
            /**
             * Función para generar el valor total del iva y el valor total cuando se escribe en el campo valor
             * @type {number}
             */

            $("#txtValor").keyup(function(){
                var cantidad = $("#txtCantidad").val();
                if(cantidad ===0 || cantidad ==="" || cantidad.length === 0 || cantidad===null){
                    cantidad = 1;
                }else{
                    cantidad = parseFloat($("#txtCantidad").val());
                }
                valor    = parseFloat($("#txtValor").val());
                iva      = parseFloat(<?php echo $porcIva; ?>)
                if(<?php echo $dc_[0] ?> != ' '){
                    totalIva = redondeo((valor * iva)/100, <?php echo $dc_[0] ?>);
                }else{
                    totalIva = ((valor * iva)/100);
                    totalIva = totalIva.toFixed(2);
                }
                total    = (parseFloat(valor) + parseFloat(totalIva)) * parseFloat(cantidad);
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

                var total  = iva + ajuste;
                var vTotal = (valor + total) * cantidad;

                $("#txtValorIva").val(total);
                $("#txtValorTotal").val(vTotal);
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

                var cantidad = parseFloat($("#"+txtCantidad).val());
                var valor = parseFloat($("#"+txtValor).val());
                var iva = parseFloat(<?php echo $porcIva; ?>);

                if (cantidad === 0 || cantidad === "" || cantidad.length === 0 || cantidad === null) {
                    cantidad = 0;
                }else {
                    cantidad = parseFloat($("#"+txtCantidad).val());
                }

                var totalIva = redondeo((valor * iva) / 100,<?php echo $dc_[0] ?>);
                var total = (totalIva + valor) * cantidad;
                $("#"+txtIva).val(totalIva);
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
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        }
                    }
                });
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
             * Función para limpieza de campos
             */
            function clean_inputs (){
                $("#sltPlanInv").select2('val','');
                $("#txtCantidad").val("");
                $("#txtValor").val("");
                $("#txtValorIva").val("");
                $("#txtValorTotal").val("");
            }

            function open_modal_r() {
                $("#modalRequisicones").modal('show');
            }

            $("#modalRequisicones").on('shown.bs.modal',function(){
                try{
                    var dataTable = $("#tablaRequisiciones").DataTable();
                    dataTable.columns.adjust().responsive.recalc();
                }catch(err){}
            });

            function markeds() {
                var i = 0;
                var checkboxValues = "";
                var asociado = "";
                $('input[name="chkActivar[]"]:checked').each(function() {
                    checkboxValues += $(this).val() + ",";
                    i = i +1;
                });
                if(checkboxValues.length > 0) {
                    checkboxValues = checkboxValues.substring(0, checkboxValues.length-1);  //Eliminamos la última coma del string
                    aso = checkboxValues.split(",");
                    if(i > 1) {
                        window.location = 'registrar_RF_ORDEN_DE_COMPRA.php?asociado='+md5(aso[0])+'&req='+checkboxValues;
                    } else {
                        window.location = 'registrar_RF_ORDEN_DE_COMPRA.php?asociado='+md5(checkboxValues)+'&req='+checkboxValues;
                    }
                }
            }

            /**
             * open_report
             * @param int mov Id de movimiento
             */
            function open_report(mov) {
                if(!isNaN(mov)) {
                    window.open('informes_almacen/inf_orden_compra_almacen.php?mov='+md5(mov));
                }
            }
        </script>
    </body>
</html>