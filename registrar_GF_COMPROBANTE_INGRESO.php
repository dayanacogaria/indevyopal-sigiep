<?php
require_once './Conexion/conexion.php';
require_once './head_listar.php';
require_once('./jsonSistema/funcionCierre.php');
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Comprobante Ingreso</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript">
$(function(){
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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fecha").datepicker({changeMonth: true}).val();
    });
</script>
</head>
<script type="text/javascript">
    $(document).ready(function ()
    {
        var id = $("#id").val();
        var form_data = {case: 21, id: id};
        $.ajax({
            type: "POST",
            url: "consultasBasicas/busquedas.php",
            data: form_data,
            success: function (response)
            {
                console.log(response);
                if (response == 1) {
                    $("#btnNuevo").attr('disabled', 'disabled');
                    $("#sltBuscar").attr('disabled', 'disabled');
                }
                document.getElementById("balanceo").value = response;
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function ()
    {
        $("#accordion").mouseover(function ()
        {
            var balanceo = document.getElementById("balanceo").value;
            if (balanceo == 1)
            {
                $("#btnNuevo").attr('disabled', 'disabled');
                $("#sltBuscar").attr('disabled', 'disabled');
                $("#modDesBal").modal('show');
                $("#btnDesBal").focus();
            }
        });
    });
</script>
<script type="text/javascript">
    function coordenadas(event)
    {
        var y = event.clientY;
        var balanceo = document.getElementById("balanceo").value;
        if (balanceo == 1)
        {
            $("#btnNuevo").attr('disabled', 'disabled');
            $("#sltBuscar").attr('disabled', 'disabled');
            if (y >= 0 && y <= 20)
            {
                $("#modDesBal").modal('show');
                $("#btnDesBal").focus();
            }
        }
    }
</script>
<body onload="return limpiar_campos()" onMouseMove="coordenadas(event);">    
    <input type="hidden" id="balanceo" >
    <?php if (!empty($_SESSION['idComprobanteI'])) { 
        echo '<input type="hidden" id="id" value="'.$_SESSION['idComprobanteI'].'">';
    } 
    $pptal          = 0;
    $idP            = 0;
    $tipoComprobante= 0;
    $fecha          = '';
    $numero         = 0;
    $tercero        = 0;
    $descripcion    = '';
    $estado         = 0;
    $claseContrato  = '';
    $numerocontrato = '';
    $num            = '';
    $idTer          = 0;
    $idComprobanteI = 0;
    if (!empty($_SESSION['idComprobanteI'])) {
        if (!empty($_SESSION['idPptal'])) {
            $pptal = $_SESSION['idPptal'];
        }
        $idComprobanteI = $_SESSION['idComprobanteI'];
        $sql = "SELECT  cn.id_unico, cn.tipocomprobante, cn.fecha, cn.numero, cn.tercero, cn.descripcion, cn.estado,
                    cn.clasecontrato, cn.numerocontrato
            FROM    gf_comprobante_cnt cn
            WHERE   cn.id_unico = $idComprobanteI";
        $result         = $mysqli->query($sql);
        $cn             = mysqli_fetch_row($result);
        $idP            = $cn[0];
        $tipoComprobante= $cn[1];
        $fecha          = $cn[2];
        $numero         = $cn[3];
        $tercero        = $cn[4];
        $idTer          = $cn[4];
        $descripcion    = $cn[5];
        $estado         = $cn[6];
        $claseContrato  = $cn[7];
        $numerocontrato = $cn[8];
        if (!empty($estado)) {
            $sqlE   = "SELECT nombre FROM gf_estado_comprobante_cnt WHERE id_unico = $estado";
            $est    = $mysqli->query($sqlE);
            if (mysqli_num_rows($est) > 0) {
                $dd = mysqli_num_rows($est);
                if ($dd > 0) {
                    $estdo = mysqli_fetch_row($est);
                }
            }
        }
    }
    ?>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-8" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Comprobante de Ingreso</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarComprobanteIngreso.php" style="margin-bottom:-10px">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">
                            <input type="hidden" name="idcnt" id="idcnt" value="<?php echo $idComprobanteI ?>">
                            <label for="fecha" class="col-sm-2 control-label"> <strong class="obligado">*</strong>Fecha:</label>
                            <input class="col-sm-2 input-sm" value="<?php if (!empty($fecha)) {
                                $valorF = (String) $fecha;
                                $fechaS = explode("-", $valorF);
                                echo $fechaS[2] . '/' . $fechaS[1] . '/' . $fechaS[0];
                            } else {
                                echo date('d/m/Y');
                            } ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:30px;" title="Ingrese la fecha" placeholder="Fecha" required>
                            <script type="text/javascript">
                                $("#fecha").change(function () {
                                    var fecha = $("#fecha").val();
                                    var form_data = {case: 4, fecha: fecha};
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonSistema/consultas.php",
                                        data: form_data,
                                        success: function (response) {
                                            if (response == 1) {
                                                $("#periodoC").modal('show');
                                                $("#fecha").val("").focus();
                                            } else {
                                                fecha();
                                            }
                                        }
                                    });
                                });
                            </script>
                            <label class="control-label col-sm-2"><strong class="obligado">*</strong>Tipo Comprobante:</label>
                            <select class="form-control input-sm col-sm-2" name="sltTipoM" id="sltTipoM" title="Seleccione tipo comprobante" style="width:100px;height:30px;cursor: pointer;" required>                                    
                                <?php
                                if (!empty($tipoComprobante)) {
                                    $sql = "SELECT ti.id_unico,ti.nombre, ti.sigla 
                                        FROM gf_tipo_comprobante ti 
                                        WHERE ti.id_unico = $tipoComprobante";
                                    $rs = $mysqli->query($sql);
                                    $tc = mysqli_fetch_row($rs);
                                    echo '<option value="' . $tc[0] . '">' . mb_strtoupper($tc[2]) . ' - ' . ucwords(mb_strtolower($tc[1])) . '</option>';
                                } else {
                                    $sql = "SELECT 
                                        ti.id_unico,
                                        ti.nombre, ti.sigla 
                                    FROM gf_tipo_comprobante ti 
                                    LEFT JOIN gf_clase_contable cc ON ti.clasecontable = cc.id_unico
                                    WHERE ti.clasecontable = 9 AND compania = $compania
                                    ORDER BY nombre DESC";
                                    $result = $mysqli->query($sql);
                                    echo '<option value="">Tipo Comprobante</option>';
                                    while ($tm = mysqli_fetch_row($result)) {
                                        echo '<option value="' . $tm[0] . '">' . mb_strtoupper($tm[2]) . ' - ' . ucwords(mb_strtolower($tm[1])) . '</option>';
                                    }
                                }
                                ?>
                            </select>                                
                            <label class="control-label col-sm-2"><strong class="obligado">*</strong>Nro Comprobante:</label>
                            <input class="form-control input-sm col-sm-2" name="txtNumero" id="txtNumero" type="text" title="Número comprobante" placeholder="Nro Comprobante" style="width:180px;height:30px;" value="<?php if (!empty($numero)) { echo $numero;} else { echo '';} ?>" required>
                        </div><br/>
                        <div class="form-group form-inline" style="margin-top:-25px;margin-left: 5px;" >                                
                            <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Tercero:</label>
                            <select class="form-control col-sm-1 input-sm select2" name="sltTercero" id="sltTercero" title="Seleccione tercero" style="width: 345px;height:30px;font-size: 10px;margin-top:-5px" required>
                                <?php
                                if (!empty($tercero)) {
                                    $sql18 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, 
                                        CONCAT(ter.numeroidentificacion) AS 'TipoD' 
                                        FROM gf_tercero ter
                                        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            WHERE ter.id_unico=$tercero ORDER BY NOMBRE ASC";
                                    $rs18 = $mysqli->query($sql18);
                                    $row18 = mysqli_fetch_row($rs18);
                                    echo '<option value="' . $row18[1] . '">' . ucwords(mb_strtolower($row18[0] . ' - ' . $row18[2])) . '</option>';
                                    $sql195 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, 
                                            CONCAT(ter.numeroidentificacion) AS 'TipoD' 
                                            FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            WHERE ter.id_unico!=$tercero AND ter.compania = $compania ORDER BY NOMBRE ASC LIMIT 20";
                                    $rs195 = $mysqli->query($sql195);
                                    while ($row195 = mysqli_fetch_row($rs195)) {
                                        echo '<option value="' . $row195[1] . '">' . ucwords(mb_strtolower($row195[0] . ' - ' . $row195[2])) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Tercero</option>';
                                    $sql191 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                            (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, 
                                            CONCAT(ter.numeroidentificacion) AS 'TipoD' 
                                            FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                                            WHERE ter.compania = $compania 
                                            ORDER BY NOMBRE ASC LIMIT 20 
                                            ";
                                    $rs191 = $mysqli->query($sql191);
                                    while ($row191 = mysqli_fetch_row($rs191)) {
                                        echo '<option value="' . $row191[1] . '">' . ucwords(mb_strtolower($row191[0] . ' - ' . $row191[2])) . '</option>';
                                    }
                                }
                                ?>                                    
                            </select>
                            <label class="col-sm-2 control-label" ><strong class="obligado">*</strong>Banco:</label>
                            <select class="form-control col-sm-1 input-sm select2" name="sltBanco" id="sltBanco" title="Seleccione banco" style="width:180px;height:30px;cursor: pointer;margin-top:-5px" required="">
                                <?php
                                if (!empty($_GET['banco'])) {
                                    $banco = $_GET['banco'];
                                    $sql = "SELECT  ctb.id_unico,CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')')
                                                    FROM gf_cuenta_bancaria ctb
                                                    LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                                                    LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE md5(ctb.id_unico)='$banco' AND ctb.parametrizacionanno = $anno AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta";
                                    $rs = $mysqli->query($sql);
                                    $row = mysqli_fetch_row($rs);
                                    echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                                    $sql1 = "SELECT  ctb.id_unico,CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')')
                                                FROM gf_cuenta_bancaria ctb
                                                LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                                                LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE md5(ctb.id_unico)!='$banco' AND ctbt.tercero ='" . $_SESSION['compania'] . "' AND ctb.parametrizacionanno = $anno AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta";
                                    $rs1 = $mysqli->query($sql1);
                                    while ($row1 = mysqli_fetch_row($rs1)) {
                                        echo '<option value="' . $row1[0] . '">' . ucwords(mb_strtolower($row1[1])) . '</option>';
                                    }
                                } else {
                                    ?>
                                    <option value="">Banco</option>
                                    <?php
                                    $sql = "SELECT  ctb.id_unico,CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')')
                                                FROM gf_cuenta_bancaria ctb
                                                LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                                                LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                                            WHERE ctbt.tercero ='" . $_SESSION['compania'] . "' AND ctb.parametrizacionanno = $anno AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta";
                                    $rs = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($rs)) {
                                        echo '<option value="' . $row[0] . '">' . ucwords(mb_strtolower($row[1])) . '</option>';
                                    }
                                }
                                ?>                                                                     
                            </select>                                
                        </div><br/>
                        <div class="form-group form-inline" style="margin-top:-15px;margin-left: 5px;">                                 
                            <label class="col-sm-2 control-label" for="txtDescripcion">Descripción:</label>
                            <textarea class="col-sm-2" style="margin-top:-1px;height:30px;width:345px;margin-top:-5px" class="area" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción"  ><?php if (!empty($descripcion)) {echo $descripcion; } else { echo '';} ?></textarea>                                
                            <label class="col-sm-2 control-label">Estado:</label>
                            <?php
                            $sql = "SELECT id_unico,nombre FROM gf_estado_comprobante_cnt WHERE id_unico = 1";
                            $result = $mysqli->query($sql);
                            $row = mysqli_fetch_row($result);
                            ?>
                            <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="width:180px;height:30px;margin-top:-5px" value="<?php if (!empty($estado)) {echo $estdo[0];} else {echo ucwords(mb_strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                        </div><br/>
                        <div class="form-group form-inline" style="margin-top:-25px;margin-left: 5px;">
                            <label class="col-sm-2 control-label">Tipo Contrato:</label>
                            <select class="col-sm-2 input-sm" name="sltClaseContrato" id="sltClaseContrato" class="form-control" style="width:100px;height:30px;cursor: pointer;margin-top:-5px" title="Seleccion tipo contrato">
                            <?php
                            if (!empty($claseContrato)) {
                                $sql = "SELECT id_unico,nombre FROM gf_clase_contrato WHERE id_unico = $claseContrato";
                                $result = $mysqli->query($sql);
                                $fila = mysqli_fetch_row($result);
                                echo '<option value="' . $fila[0] . '">' . ucwords(mb_strtolower($fila[1])) . '</option>';

                                $sql1 = "SELECT id_unico,nombre FROM gf_clase_contrato WHERE id_unico != $fila[0] GROUP BY id_unico";
                                $result1 = $mysqli->query($sql1);
                                while ($fila1 = mysqli_fetch_row($result1)) {
                                    echo '<option value="' . $fila1[0] . '">' . ucwords(mb_strtolower($fila1[1])) . '</option>';
                                }
                            } else {
                                echo '<option value="">Tipo Contrato</option>';
                                $sqlCCC = "SELECT id_unico,nombre FROM gf_clase_contrato";
                                $r = $mysqli->query($sqlCCC);
                                while ($x = mysqli_fetch_row($r)) {
                                    echo '<option value="' . $x[0] . '">' . ucwords(mb_strtolower($x[1])) . '</option>';
                                }
                            }
                            ?>                                                                      
                            </select>
                            <label class="col-sm-2 control-label">N° Contrato:</label>
                            <input class="col-sm-2 input-sm" type="text" name="txtNumeroCT" id="txtNumeroCT" class="form-control" style="width: 100px;height:30px;margin-top:-5px" title="Ingrese número de contrato" placeholder="N° Contrato" value="<?php echo $numerocontrato ?>"/>
                            <div class="form-group form-inline col-sm-4" style="margin-top:-0px; margin-left:50px">                                    
                                <a id="btnNuevo" onclick="javascript:nuevo()" class="btn shadow btn-primary" style="width: 40px" title="Ingresar nuevo comprobante"><li class="glyphicon glyphicon-plus"></li></a>                                    
                                <input type="hidden" name="id" id="id" value="<?php echo $idP; ?>" />                                    
                                <button type="submit" id="btnGuardar" class="btn shadow btn-primary" title="Guardar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                <a class="btn shadow btn-primary" id="btnImprimir" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#btnImprimir").click(function () {
                                            window.open('informes/inf_com_ing.php?idcom=<?php echo md5($idComprobanteI); ?>&idppt=<?php echo md5($pptal); ?>');
                                        });
                                    });
                                </script>                                                                                                                     
                                <button id="btnModificar" onclick="modificarComprobante()" class="btn shadow btn-primary" style="width: 40px" title="Modificar Comprobante"><li class="glyphicon glyphicon-pencil"></li></button>                                    
                                <script>
                                    $(document).ready(function () {
                                        $("#btnImprimir").click(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                id:<?php echo $idComprobanteI ?>
                                            };
                                            var result = '';
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/impreso.php",
                                                data: form_data,
                                                success: function (data) {
                                                    result = JSON.parse(data);
                                                    if (result == true) {
                                                        window.location.reload();
                                                    }
                                                }
                                            });
                                        });
                                    });
                                    function cancelarM() {
                                        var form_data = {
                                            session: 7,
                                            numero: $("#txtNumero").val()
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/vaciarSessiones.php",
                                            data: form_data,
                                            success: function (data) {
                                                window.location = 'registrar_GF_COMPROBANTE_INGRESO.php';
                                            }
                                        });
                                    }
                                    $("#sltTipoM").change(function () {
                                        var form_data = {
                                            id_tip_comp: $("#sltTipoM").val(),
                                            estruc: 24
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "jsonPptal/consultas.php",
                                            data: form_data,
                                            success: function (data) {
                                                var result = JSON.parse(data);
                                                var data = parseInt(result);
                                                $("#txtNumero").val(data);
                                            }
                                        });
                                    });
                                    function nuevo() {
                                        var form_data = {
                                            is_ajax: 1,
                                            tipo: $("#sltTipoM").val(),
                                            nuevos: 5
                                        };
                                        var form_data = {
                                            session: 7,
                                            numero: $("#txtNumero").val()
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/vaciarSessiones.php",
                                            data: form_data,
                                            success: function (data) {
                                                window.location = 'registrar_GF_COMPROBANTE_INGRESO.php';
                                            }
                                        });
                                    }
                                    function cancelarN() {
                                        $("#btnGuardar").attr('disabled', true);
                                        $("#txtNumero").val("");
                                        var form_data = {
                                            session: 7,
                                            numero: $("#txtNumero").val()
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasBasicas/vaciarSessiones.php",
                                            data: form_data,
                                            success: function (data) {
                                                window.location = 'registrar_GF_COMPROBANTE_INGRESO.php';
                                            }
                                        });
                                    }
                                </script>    
                                <button id="btnEliminar"  class="btn shadow btn-primary" style="width: 40px" title="Eliminar Comprobante"><li class="glyphicon glyphicon-remove"></li></button>                                    
                                <!------- Eliminar -------->
                                <script>
                                    $("#btnEliminar").click(function () {
                                        $("#myModalElimCom").modal("show");
                                        $("#EliminarCom").click(function () {
                                            var form_data = {action: 21, id: $("#idcnt").val()};
                                            $.ajax({
                                                type: 'POST',
                                                url: 'jsonPptal/comprobantesIngresoJson.php',
                                                data: form_data,
                                                success: function (response) {
                                                    console.log(response + 'Eliminar');
                                                    var resultado = JSON.parse(response);
                                                    var rta = resultado["rta"];
                                                    var mensaje = resultado["msj"];
                                                    if (rta == 1) {
                                                        $("#mensaje").html(mensaje);
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnAceptar").click(function () {
                                                            document.location.reload();
                                                        });
                                                    } else {
                                                        $("#mensaje").html("Información Eliminada Correctamente");
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnAceptar").click(function () {
                                                            document.location.reload();
                                                        });
                                                    }
                                                }
                                            });
                                        })
                                    })
                                </script>
                                <div class="modal fade" id="myModalElimCom" role="dialog" align="center" >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <p>¿Desea eliminar El Comprobante De Ingreso?</p>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="EliminarCom" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                                <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <?php if (!empty($_SESSION['idComprobanteI'])) { ?>
                                    <script>
                                        document.getElementById("btnGuardar").disabled = true;
                                        document.getElementById("btnEliminar").disabled = false;
                                        $("#btnImprimir").attr('disabled', false);
                                    </script>
                                    <?php } else { ?>
                                    <script>
                                        document.getElementById("btnGuardar").disabled = false;
                                        document.getElementById("btnEliminar").disabled = true;
                                        $("#btnImprimir").attr('disabled', true);
                                    </script>
                                    <?php } ?>   
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top:-10px; margin-left:10px">
                            <label for="sltBuscar" class="col-sm-2 control-label">Buscar Comprobante:</label>            
                            <div class="form-group form-inline" style="margin-left:-3px">                                  
                                <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Comprobante" class="form-control col-sm-1 select2" style="width: 130px;">
                                    <option value="">Tipo Comprobante</option>
                                    <?php $sqlTC = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE clasecontable=9 AND niif !=1 AND compania = $compania ";
                                    $m = $mysqli->query($sqlTC);
                                    while ($resc = mysqli_fetch_row($m)) {
                                        echo '<option value="' . $resc[0] . '">' . mb_strtoupper($resc[1]) . ' - ' . ucwords(mb_strtolower($resc[2])) . '</option>';
                                    } ?>
                                </select>                               
                            </div>
                            <div class="form-group form-inline" style="margin-left:20px">    
                                <select name="sltBuscar" id="sltBuscar" class="form-control col-sm-1 select2" style="width:230px;" title="Seleccione para consultar comprobante">
                                    <?php echo "<option value=''>Buscar Comprobante</option>"; ?>
                                </select> 
                            </div>
                        </div>
                    </form>
                    <script>
                        $("#sltTipoBuscar").change(function () {
                            var form_data = {
                                estruc: 25,
                                tipo: $("#sltTipoBuscar").val(),
                            }
                            var option = '<option value="">Buscar Comprobante</option>';
                            $.ajax({
                                type: 'POST',
                                url: 'jsonPptal/consultas.php',
                                data: form_data,
                                success: function (data) {
                                    var option = option + data;
                                    $("#sltBuscar").html(option);
                                }
                            });
                        })
                    </script>
                    <script type="text/javascript" charset="utf-8">
                        $("#sltBuscar").change(function () {
                            var form_data = {
                                comprobante: $("#sltBuscar").val(),
                                existente: 4
                            };
                            $.ajax({
                                type: 'POST',
                                url: 'consultasBasicas/consultarNumeros.php',
                                data: form_data,
                                success: function (data) {
                                    window.location.reload();
                                }
                            });
                        });
                    </script> 
                </div>
            </div>
            <div class="col-sm-10 text-center " style="margin-top:5px;" align="">                    
                <div class="client-form" style="" class="col-sm-12">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleComprobanteIngreso.php" style="margin-top:-15px">
                        <div class="col-sm-2" style="margin-right:-20px;">
                            <div class="form-group" style="margin-top: 5px;"  align="left">                                    
                                <label class="control-label"><strong class="obligado">*</strong>Concepto:</label>
                                <select name="sltConcepto" id="sltConcepto" class="form-control col-sm-1 select2" style="width:150px;height:30px;" title="Seleccione concepto" required="">
                                    <option value="">Concepto</option>
                                    <?php
                                    $sql = "SELECT id_unico,nombre FROM gf_concepto WHERE clase_concepto=1 AND parametrizacionanno = $anno ORDER BY nombre DESC";
                                    $result = $mysqli->query($sql);
                                    while ($row = mysqli_fetch_row($result)) {
                                        echo '<option value="' . $row[0] . '">' . ucwords(mb_strtolower($row[1])) . '</option>';
                                    } ?>
                                </select>
                                <input type="hidden" class="hidden" name="txtConceptoR" id="txtConceptoR"/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:20px;">
                            <div class="form-group" style="margin-top: 5px;"  align="left">                                   
                                <label class="control-label"><strong class="obligado">*</strong>Rubro Fuente:</label>
                                <select name="sltRubroFuente" id="sltRubroFuente" class="form-control" style="width:100px;height:30px;padding:2px" title="Seleccione tercero" required="">
                                    <option value="">Rubro Fuente</option>                                        
                                    <script>
                                        $("#sltConcepto").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                concepto: $("#sltConcepto").val(),
                                                valor: 1
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/conceptoRubro.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltRubroFuente").html(data).fadeIn();
                                                    cargarCuenta($("#sltRubroFuente").val());
                                                }
                                            });
                                        });
                                        $("#sltConcepto").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                concepto: $("#sltConcepto").val(),
                                                valor: 2
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/conceptoRubro.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#txtConceptoR").val(data);
                                                }
                                            });
                                        });
                                    </script>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:20px;">
                            <div class="form-group" style="margin-top: 5px;"  align="left">                                    
                                <label class="control-label"><strong class="obligado">*</strong>Cuenta:</label>
                                <select name="sltcuenta" id="sltcuenta" class="form-control" style="width:100px;height:30px;padding:2px" title="Seleccione cuenta" required="">
                                    <option value="">Cuenta</option>
                                    <script>
                                        function cargarCuenta(rubro) {
                                            var form_data = {
                                                is_ajax: 1,
                                                rubro: rubro,
                                                concepto: $("#sltConcepto").val(),
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/cuenta.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltcuenta").html(data).fadeIn();
                                                    consultarCentro($("#sltcuenta").val());
                                                    consultarTercero($("#sltcuenta").val());
                                                    consultarProyecto($("#sltcuenta").val());
                                                    cargarTercero($("#sltcuenta").val());
                                                    cargarCent($("#sltcuenta").val());
                                                    cargarPro($("#sltcuenta").val());
                                                }
                                            });
                                        }
                                        $("#sltRubroFuente").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                rubro: $("#sltRubroFuente").val(),
                                                concepto: $("#sltConcepto").val()
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/cuenta.php",
                                                data: form_data,
                                                success: function (data) {
                                                    console.log(data + 'Cuenta')
                                                    $("#sltcuenta").html(data).fadeIn();
                                                }
                                            });
                                        });
                                        function consultarCentro(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: cuenta
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/centroCosto.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltcentroc").html(data).fadeIn();
                                                }
                                            });
                                        }

                                        $("#sltcuenta").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: $("#sltcuenta").val()
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/centroCosto.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltcentroc").html(data).fadeIn();
                                                }
                                            });
                                        });

                                        function consultarProyecto(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: cuenta
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/proyecto.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltproyecto").html(data).fadeIn();
                                                }
                                            });
                                        }

                                        $("#sltcuenta").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: $("#sltcuenta").val()
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/proyecto.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#sltproyecto").html(data).fadeIn();
                                                }
                                            });
                                        });

                                        function consultarTercero(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: cuenta,
                                                ter:<?php echo $idTer; ?>
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/consultarTercero.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#slttercero").html(data).fadeIn();
                                                    $("#slttercero").css('display', 'none');
                                                }
                                            });
                                        }

                                        $("#sltcuenta").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                cuenta: $("#sltcuenta").val()
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultarComprobanteIngreso/consultarTercero.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $("#slttercero").html(data).fadeIn();
                                                    $("#slttercero").css('display', 'none');
                                                }
                                            });
                                        });

                                        function cargarTercero(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                data: cuenta
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "consultasDetalleComprobante/consultarTercero.php",
                                                data: form_data,
                                                success: function (data) {
                                                    var tercero = document.getElementById('slttercero');
                                                    if (data == 1) {
                                                        tercero.disabled = false;
                                                    } else if (data == 2) {
                                                        $("#slttercero").prop('disabled', true);
                                                    }
                                                }
                                            });
                                        }
                                        $(document).ready(function () {
                                            var padre = 0;
                                            $("#slttercero").prop('disabled', true);
                                            $("#sltcuenta").change(function () {
                                                if ($("#sltcuenta").val() == "" || $("#sltcuenta").val() == 0) {
                                                    padre = 0;
                                                    $("#slttercero").prop('disabled', true);
                                                } else {
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax: 1,
                                                    data: +padre
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "consultasDetalleComprobante/consultarTercero.php",
                                                    data: form_data,
                                                    success: function (data) {
                                                        var tercero = document.getElementById('slttercero');
                                                        if (data == 1) {
                                                            tercero.disabled = false;
                                                        } else if (data == 2) {
                                                            $("#slttercero").prop('disabled', true);
                                                        }
                                                    }
                                                });
                                            });
                                        });

                                        function cargarPro(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                data: cuenta
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "consultasDetalleComprobante/consultaProyecto.php",
                                                data: form_data,
                                                success: function (data) {
                                                    var centro = document.getElementById('sltproyecto');
                                                    if (data == 1) {
                                                        centro.disabled = false;
                                                    } else if (data == 2) {
                                                        $("#sltproyecto").prop('disabled', true);
                                                    }
                                                }
                                            });
                                        }
                                        $(document).ready(function () {
                                            var padre = 0;
                                            $("#sltproyecto").prop('disabled', true);
                                            $("#sltcuenta").change(function () {
                                                if ($("#sltcuenta").val() == "" || $("#sltcuenta").val() == 0) {
                                                    padre = 0;
                                                    $("#sltproyecto").prop('disabled', true);
                                                } else {
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax: 1,
                                                    data: +padre
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "consultasDetalleComprobante/consultaProyecto.php",
                                                    data: form_data,
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltproyecto');
                                                        if (data == 1) {
                                                            centro.disabled = false;
                                                        } else if (data == 2) {
                                                            $("#sltproyecto").prop('disabled', true);
                                                        }
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        function cargarCent(cuenta) {
                                            var form_data = {
                                                is_ajax: 1,
                                                data: cuenta
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "consultasDetalleComprobante/consultarCentroC.php",
                                                data: form_data,
                                                success: function (data) {
                                                    var centro = document.getElementById('sltcentroc');
                                                    if (data == 1) {
                                                        centro.disabled = false;
                                                    } else if (data == 2) {
                                                        $("#sltcentroc").prop('disabled', true);
                                                    }
                                                }
                                            });
                                        }
                                        $(document).ready(function () {
                                            var padre = 0;
                                            $("#sltcentroc").prop('disabled', true);
                                            //focus
                                            $("#sltcuenta").change(function () {
                                                if ($("#sltcuenta").val() == "" || $("#sltcuenta").val() == 0) {
                                                    padre = 0;
                                                    $("#sltcentroc").prop('disabled', true);
                                                } else {
                                                    padre = $("#sltcuenta").val();
                                                }

                                                var form_data = {
                                                    is_ajax: 1,
                                                    data: +padre
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "consultasDetalleComprobante/consultarCentroC.php",
                                                    data: form_data,
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltcentroc');
                                                        if (data == 1) {
                                                            centro.disabled = false;
                                                        } else if (data == 2) {
                                                            $("#sltcentroc").prop('disabled', true);
                                                        }
                                                    }
                                                });
                                            });
                                        });
                                    </script>    
                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            var padre = 0;
                                            $("#sltcentroc").prop('disabled', true);
                                            //focus
                                            $("#sltcuenta").change(function () {
                                                if ($("#sltcuenta").val() == "" || $("#sltcuenta").val() == 0) {
                                                    padre = 0;
                                                    $("#sltcentroc").prop('disabled', true);
                                                } else {
                                                    padre = $("#sltcuenta").val();
                                                }

                                                var form_data = {
                                                    is_ajax: 1,
                                                    data: +padre
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "consultasDetalleComprobante/consultarCentroC.php",
                                                    data: form_data,
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltcentroc');
                                                        if (data == 1) {
                                                            centro.disabled = false;
                                                        } else if (data == 2) {
                                                            $("#sltcentroc").prop('disabled', true);
                                                        }
                                                    }
                                                });
                                            });
                                        });
                                    </script> 
                                </select>
                            </div>                               
                        </div>    
                        <div class="col-sm-1" style="margin-right:20px;">
                            <div class="form-group" style="margin-top:5px;"  align="left">
                                    <?php
                                    $sql = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                    (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                            ter.id_unico, 
                                            CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' 
                                            FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                                            WHERE ter.compania = $compania 
                                            ORDER BY NOMBRE ASC LIMIT 20 
                                            ";
                                    $rs = $mysqli->query($sql);
                                    ?>
                                <label class="control-label"><strong class="obligado"></strong>Tercero:</label>
                                <select name="slttercero" id="slttercero" class="form-control" style="width:100px;height:30px;padding:2px" title="Seleccione tercero">
                                <?php
                                if (!empty($_SESSION['idComprobanteI'])) {
                                    if (!empty($idTer)) {
                                        $sql = "SELECT  t.id_unico, 
                                                        IF(CONCAT_WS(' ',
                                                        t.nombreuno,
                                                        t.nombredos,
                                                        t.apellidouno,
                                                        t.apellidodos) 
                                                        IS NULL OR CONCAT_WS(' ',
                                                        t.nombreuno,
                                                        t.nombredos,
                                                        t.apellidouno,
                                                        t.apellidodos) = '',
                                                        (t.razonsocial),
                                                        CONCAT_WS(' ',
                                                        t.nombreuno,
                                                        t.nombredos,
                                                        t.apellidouno,
                                                        t.apellidodos)) AS NOMBRE,
                                                        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                        t.numeroidentificacion, 
                                                        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                                        FROM gf_tercero t 
                                                        WHERE t.id_unico = $idTer";
                                        $rs = $mysqli->query($sql);
                                        $rs = mysqli_fetch_row($rs);
                                        echo '<option value ="' . $rs[0] . '">' . ucwords(mb_strtolower($rs[1])) . ' - ' . $rs[2] . '</option>';
                                    }
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:20px;">
                            <div class="form-group" style="margin-top: 5px;"  align="left">
                                <?php
                                $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE nombre = 'varios' AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                $a = $mysqli->query($sqlCC);
                                $filaC = mysqli_fetch_row($a);
                                $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                $r = $mysqli->query($sqlCT);
                                ?>
                                <label class="control-label"><strong class="obligado"></strong>Centro Costo:</label>
                                <select name="sltcentroc" id="sltcentroc" class="form-control" style="width:100px;height:30px;padding:2px" title="Seleccione centro costo" >
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:20px;">
                            <div class="form-group" style="margin-top: 5px;"  align="left">
                                <?php
                                $sqlP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'";
                                $d = $mysqli->query($sqlP);
                                $filaP = mysqli_fetch_row($d);
                                $sqlPY = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $filaP[0]";
                                $X = $mysqli->query($sqlPY);
                                ?>
                                <label class="control-label"><strong class="obligado"></strong>Proyecto:</label>
                                <select name="sltproyecto" id="sltproyecto" class="form-control" style="width:100px;height:30px;padding:2px" title="Seleccione proyecto" >
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <script type="text/javascript">
                                function justNumbers(e) {
                                    var keynum = window.event ? window.event.keyCode : e.which;
                                    if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                                        return true;
                                    return /\d/.test(String.fromCharCode(keynum));
                                }
                            </script>
                            <div class="form-group" style="margin-top:5px;" align="left">
                                <label class="control-label"><strong class="obligado">*</strong>Valor:</label>
                                <input type="text" name="txtValor" onkeypress="return justNumbers(event);" id="txtValor" minlength="1" maxlength="50" class="form-control" style="height:30px;padding:2px;width:100px" required=""/>
                            </div>
                        </div>                            
                        <div class="col-sm-1 form-group" style="margin-top:32px;margin-left:-40px">
                            <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                                                
                        </div>                            
                    </form>                        
                </div>                    
            </div>
            <div class="col-sm-8" style="margin-top:-20px">
                <?php
                if (!empty($idComprobanteI)) {
                    $numero = $idComprobanteI;
                    $result = "";
                    $sql = "SELECT DISTINCT dtc.id_unico, ct.id_unico, ct.nombre, rb.id_unico rubro, rb.codi_presupuesto, rb.nombre,
                            cnt.id_unico cuenta, cnt.codi_cuenta, cnt.nombre, cnt.naturaleza, dtc.valor, pr.id_unico proyecto,
                            pr.nombre, ctr.id_unico centroc, ctr.nombre, dtc.tercero, pptal.id_unico, ft.nombre, pptal.id_unico,
                            dtc.conciliado
                    FROM            gf_detalle_comprobante dtc
                    LEFT JOIN       gf_detalle_comprobante_pptal pptal ON dtc.detallecomprobantepptal = pptal.id_unico
                    LEFT JOIN       gf_concepto_rubro cnr ON pptal.conceptoRubro = cnr.id_unico
                    LEFT JOIN       gf_concepto ct ON cnr.concepto = ct.id_unico
                    LEFT JOIN       gf_rubro_fuente rbf ON rbf.id_unico = pptal.rubrofuente
                    LEFT JOIN       gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
                    LEFT JOIN       gf_fuente ft ON rbf.fuente = ft.id_unico
                    LEFT JOIN       gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro
                    LEFT JOIN       gf_cuenta cnt ON dtc.cuenta = cnt.id_unico
                    LEFT JOIN       gf_proyecto pr ON dtc.proyecto = pr.id_unico
                    LEFT JOIN       gf_centro_costo ctr ON dtc.centrocosto = ctr.id_unico
                    LEFT JOIN       gf_tercero ter ON dtc.tercero = ter.id_unico
                    WHERE           dtc.comprobante = $idComprobanteI ";
                $result = $mysqli->query($sql); }
                ?>
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                    <?php $sumar = 0;
                    $sumaT = 0; 
                    if (!empty($_SESSION['idComprobanteI'])) {
                        $dn = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = " . $_SESSION['idComprobanteI'];
                        $dn = $mysqli->query($dn);
                        $dn = mysqli_num_rows($dn);
                        if ($dn <= 10) { ?>
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>    
                                    <tr>
                                        <td class="oculto" >Identificador</td>                                                                        
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Concepto</strong></td>
                                        <td class="cabeza"><strong>Rubro Fuente</strong></td>
                                        <td class="cabeza"><strong>Cuenta</strong></td>
                                        <td class="cabeza"><strong>Débito</strong></td>
                                        <td class="cabeza"><strong>Crédito</strong></td>                                    
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Documentos</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>                                    
                                        <th width="7%" class="cabeza"></th>
                                        <th class="cabeza">Concepto</th>
                                        <th class="cabeza">Rubro Fuente</th>
                                        <th class="cabeza">Cuenta</th>
                                        <th class="cabeza">Débito</th>
                                        <th class="cabeza">Crédito</th>                                    
                                        <th class="cabeza">Centro Costo</th>
                                        <th class="cabeza">Proyecto</th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Documentos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_row($result)) { ?>
                                    <tr>
                                        <td class="oculto"><?php echo $row[0]; ?></td>
                                        <td class="campos">  
                                        <?php if (!empty($idComprobanteI)) {
                                            $cierre = cierrecnt($idComprobanteI);
                                            if ($cierre == 0) {
                                                if ($row[19] == '1') {
                                                } else {
                                                    if (!empty($row[16])) { ?>
                                                        <a href="#<?php echo $row[0]; ?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>,<?php echo $row[16]; ?>)" title="Eliminar">
                                                            <li class="glyphicon glyphicon-trash"></li>
                                                        </a>
                                                        <?php } else { ?>
                                                        <a href="#<?php echo $row[0]; ?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>, 0)" title="Eliminar">
                                                            <li class="glyphicon glyphicon-trash"></li>
                                                        </a>
                                                        <?php } 
                                                        if (empty($row[1])) { ?>
                                                        <a  href="#<?php echo $row[0] ?>" onclick="javascript:show_inputs(<?php echo $row[0] ?>)"><li class="glyphicon glyphicon-edit"></li></a>
                                                        <?php } else { ?>                                                                                
                                                        <a href="#<?php echo $row[0]; ?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);
                                                            javascript:cargarT(<?php echo $row[0]; ?>);
                                                            javascript:cargarT2(<?php echo $row[0]; ?>);
                                                            javascript:cargarCentro(<?php echo $row[0]; ?>);
                                                            javascript:cargarCentro2(<?php echo $row[0]; ?>);
                                                            javascript:cargarProyecto(<?php echo $row[0]; ?>);
                                                            javascript:cargarProyecto2(<?php echo $row[0]; ?>);
                                                            javascript:rubroFuente(<?php echo $row[0]; ?>);
                                                            javascript:rubroCuenta(<?php echo $row[0]; ?>);
                                                            return select(<?php echo $row[0]; ?>)">
                                                            <li class="glyphicon glyphicon-edit"></li>
                                                        </a>                                            
                                                <?php } } } } ?>
                                            </td>
                                            <td class="campos text-left">
                                                <?php echo '<label class="valorLabel col-sm-12" style="font-weight:normal" id="concepto' . $row[0] . '">' . ucwords(mb_strtolower($row[2])) . '</label>'; ?>                                        
                                            </td>
                                            <td class="campos text-left">
                                                    <?php
                                                    if (!empty($row[3])) {
                                                        $sqlRB = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                                                    FROM gf_concepto_rubro cr 
                                                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                                                    LEFT JOIN gf_rubro_pptal rb ON rft.rubro = rb.id_unico
                                                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
                                                    WHERE rb.id_unico =  $row[3] AND rb.id_unico IS NOT NULL";
                                                        $conR = $mysqli->query($sqlRB);
                                                        $tiene = mysqli_num_rows($conR);
                                                        $rubrofuente = mysqli_fetch_row($conR);
                                                        echo '<label class="valorLabel" style="font-weight:normal" title="' . $rubrofuente[2] . ' - ' . $row[17] . '" id="rubroFuente' . $row[0] . '">' . $rubrofuente[1] . ' - ' . $row[5] . ' - ' . $row[17] . '</label>';
                                                        ?>                                        
                                                    <select style="display: none;padding:2px;width: 100%" class="col-sm-12 campoD" id="sltrubroFte<?php echo $row[0]; ?>">
                                                        <option value="<?php echo $rubrofuente[4] ?>"><?php echo $rubrofuente[1] . ' - ' . $row[5] . ' - ' . $row[17] ?></option>
                                                    <?php
                                                    $sqlRB = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                                                    FROM gf_concepto_rubro cr 
                                                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                                                    LEFT JOIN gf_rubro_pptal rb ON rft.rubro = rb.id_unico
                                                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico
                                                    WHERE cr.concepto != $row[3] AND rb.id_unico IS NOT NULL AND rb.parametrizacionanno = $anno ";
                                                    $conR = $mysqli->query($sqlRB);
                                                    while ($rfuente = mysqli_fetch_row($conR)) {
                                                        echo '<option value="' . $rfuente[4] . '">' . $rfuente[1] . '   ' . $rfuente[2] . ' - ' . $rfuente[3] . '</option>';
                                                    }
                                                } else {
                                                    ?>
                                                        <select style="display: none;padding:2px;width: 100%" class="col-sm-12 campoD" id="sltrubroFte<?php echo $row[0]; ?>">
                                                    <?php
                                                    echo "<option value=' '></option>";
                                                    $sqlRB = "SELECT DISTINCT rb.id_unico,rb.codi_presupuesto,rb.nombre,ft.nombre,rft.id_unico
                                                    FROM gf_concepto_rubro cr 
                                                    LEFT JOIN gf_rubro_fuente rft ON cr.rubro = rft.rubro
                                                    LEFT JOIN gf_rubro_pptal rb ON rft.rubro = rb.id_unico
                                                    LEFT JOIN  gf_fuente ft ON rft.fuente = ft.id_unico 
                                                    WHERE rb.parametrizacionanno = $anno 
                                                    ";
                                                    $conR = $mysqli->query($sqlRB);
                                                    while ($rfuente = mysqli_fetch_row($conR)) {
                                                        echo '<option value="' . $rfuente[4] . '">' . $rfuente[1] . '   ' . $rfuente[2] . ' - ' . $rfuente[3] . '</option>';
                                                    }
                                                }
                                                ?>
                                                </select>
                                            </td>
                                            <td class="campos text-left">
                                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta' . $row[0] . '">' . (ucwords(mb_strtolower($row[7] . ' - ' . $row[8]))) . '</label>'; ?>                                  
                                                <select style="display: none;padding:2px" class="col-sm-12 campoD" id="sltC<?php echo $row[0]; ?>">
                                                    <option value="<?php echo $row[6]; ?>"><?php echo $row[7] . '-' . $row[8]; ?></option>
                                                <?php
                                                $sqlcuenta = "SELECT DISTINCT id_unico,codi_cuenta,nombre FROM gf_cuenta WHERE id_unico != $row[6] and parametrizacionanno = $anno";
                                                $rscuenta = $mysqli->query($sqlcuenta);
                                                while ($s = mysqli_fetch_row($rscuenta)) {
                                                    echo '<option value="' . $s[0] . '">' . $s[1] . ' - ' . $s[2] . '</option>';
                                                }
                                                ?>                                              
                                                </select>
                                            </td>
                                            <td class="campos text-right">
                                                <?php if ($row[9] == 1) {
                                                    if ($row[10] >= 0) {
                                                        $sumar += $row[10];
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="debitoP' . $row[0] . '">' . number_format($row[10], 2, '.', ',') . '</label>';
                                                        echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtDebito' . $row[0] . '" id="txtDebito' . $row[0] . '" value="' . $row[10] . '" />';
                                                    } else {
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="debitoP' . $row[0] . '">0</label>';
                                                        echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" align="center" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito' . $row[0] . '"  id="txtDebito' . $row[0] . '" value="0"/>';
                                                    }
                                                } else if ($row[9] == 2) {
                                                    if ($row[10] <= 0) {
                                                        $x = (float) substr($row[10], '1');
                                                        $sumar += $x;
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="debitoP' . $row[0] . '">' . number_format($x, 2, '.', ',') . '</label>';
                                                        echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" type="text" name="txtDebito' . $row[0] . '" id="txtDebito' . $row[0] . '" value="' . $x . '" />';
                                                    } else {
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="debitoP' . $row[0] . '">0</label>';
                                                        echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" type="text" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito' . $row[0] . '"  id="txtDebito' . $row[0] . '" value="0"/>';
                                                    }
                                                } ?>
                                            </td>
                                            <td class="campos text-right">
                                                <?php
                                                if ($row[9] == 2) {
                                                    if ($row[10] >= 0) {
                                                        $sumaT += $row[10];
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP' . $row[0] . '">' . number_format($row[10], 2, '.', ',') . '</label>';
                                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtCredito' . $row[0] . '" id="txtCredito' . $row[0] . '" value="' . $row[10] . '" />';
                                                    } else {
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP' . $row[0] . '">0</label>';
                                                        echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  name="txtCredito' . $row[0] . '"  id="txtCredito' . $row[0] . '" value="0"/>';
                                                    }
                                                } else if ($row[9] == 1) {
                                                    if ($row[10] <= 0) {
                                                        $x = (float) substr($row[10], '1');
                                                        $sumaT += $x;
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP' . $row[0] . '">' . number_format($x, 2, '.', ',') . '</label>';
                                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD"  type="text" name="txtCredito' . $row[0] . '" id="txtCredito' . $row[0] . '" value="' . $x . '" />';
                                                    } else {
                                                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP' . $row[0] . '">0</label>';
                                                        echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" class="col-sm-12 text-left campoD" style="display:none;padding:2px;height:19px" name="txtCredito' . $row[0] . '" id="txtCredito' . $row[0] . '" value="0"/>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="campos text-left">                                        
                                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC' . $row[0] . '">' . (ucwords(mb_strtolower($row[14]))) . '</label>'; ?>
                                                <select id="sltcentroC<?php echo $row[0]; ?>" style="display: none;padding:2px;height:19px" class="col-sm-12 campoD">
                                                    <option value="<?php echo $row[13]; ?>"><?php echo $row[14]; ?></option>
                                                    <?php $sqlCCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[13]' AND parametrizacionanno = $anno";
                                                    $g = $mysqli->query($sqlCCT);
                                                    while ($f = mysqli_fetch_row($g)) {
                                                        echo '<option value="' . $f[0] . '">' . $f[1] . '</option>';
                                                    } ?> 
                                                </select>
                                            </td>
                                            <td class="campos text-left">
                                                <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto' . $row[0] . '">' . (ucwords(mb_strtolower($row[12]))) . '</label>'; ?>
                                                <select style="display: none;padding:2px;height:19px" class="col-sm-12 campoD" id="sltProyecto<?php echo $row[0]; ?>">
                                                    <option value="<?php echo $row[11]; ?>"><?php echo $row[12]; ?></option>
                                                        <?php
                                                        $sqlCP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[12]";
                                                        $rsP = $mysqli->query($sqlCP);
                                                        while ($y = mysqli_fetch_row($rsP)) {
                                                            echo '<option value="' . $y[0] . '">' . $y[1] . '</option>';
                                                        }
                                                        ?>
                                                </select>
                                            </td>
                                            <td class="campos">
                                                <?php
                                                $ter = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
                                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                                    ter.id_unico, CONCAT(ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                    WHERE ter.id_unico = $row[15] ORDER BY NOMBRE ASC LIMIT 20 ";
                                                $tercero = $mysqli->query($ter);
                                                $per = mysqli_fetch_row($tercero);
                                                echo '<label style="font-weight:normal" class="valorLabel" title="' . $per[2] . '" id="tercero' . $row[0] . '">' . (ucwords(mb_strtolower($per[0]))) . '</label>';
                                                ?>
                                                <select id="sltTercero<?php echo $row[0]; ?>" style="display: none;padding: 2px" class="col-sm-12 campoD" onclick="cargarT(<?=$row[0];?>)">
                                                    <option value="<?php echo $row[15] ?>"><?php echo (ucwords(mb_strtolower($per[0] . '    ' . $per[2]))) ?></option>
                                                    <?php
                                                    $sqlTR = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
                                                                                        CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                                                        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                                                                            ter.id_unico, CONCAT(ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                                                            WHERE ter.id_unico != $row[15] AND ter.compania = $compania ORDER BY NOMBRE ASC LIMIT 20 ";
                                                    $resulta = $mysqli->query($sqlTR);
                                                    while ($e = mysqli_fetch_row($resulta)) {
                                                        echo '<option value="' . $e[1] . '">' . ucwords(mb_strtolower($e[0] . ' - ' . $e[2])) . '</option>';
                                                    } ?>
                                                </select>
                                            </td>
                                            <td class="campos text-center">                                        
                                                <div class="col-sm-1">
                                                    <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                        <tbody>
                                                            <tr style="background-color:transparent;">
                                                                <td style="background-color:transparent;">
                                                                    <a  href="#<?php echo $row[0]; ?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                        <li class="glyphicon glyphicon-floppy-disk" style="margin-left: -19px;"></li>
                                                                    </a>
                                                                    <a  href="#<?php echo $row[0]; ?>" title="Guardar" id="guardarX<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:update_bank(<?php echo $row[0]; ?>)">
                                                                        <li class="glyphicon glyphicon-floppy-disk" style="margin-left: -19px;"></li>
                                                                    </a>
                                                                </td>
                                                                <td style="background-color:transparent;">
                                                                    <a href="#<?php echo $row[0]; ?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0]; ?>)" >
                                                                        <i title="Cancelar" class="glyphicon glyphicon-remove" style="margin-left: -10px;" ></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php if (!empty($row[16])) { ?>
                                                    <a href="javascript:void(0)" onclick="abrirdetalleMov(<?php echo $row[16]; ?>,<?php echo $row[10]; ?>)" data-toggle="modal" class="col-sm-6"><li class="glyphicon glyphicon-file"></li></a>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0)" onclick="abrirdetalleMov1(<?php echo $row[0]; ?>,<?php echo $row[10]; ?>)" data-toggle="modal" class="col-sm-6"><li class="glyphicon glyphicon-file"></li></a>
                                                <?php } ?>                                        
                                            </td>
                                        </tr>
                                        <?php } ?>
                                </tbody>
                            </table>
                            <script>
                                function select(id) {
                                    var rf = 'sltrubroFte' + id;
                                    $(".select2_single, #" + rf).select2();
                                    var ct = 'sltC' + id;
                                    $(".select2_single, #" + ct).select2();
                                    var ter = 'sltTercero' + id;
                                    $(".select2_single, #" + ter).select2();
                                }
                            </script>
                        </div>
                    <?PHP }
                } ?>
                <script type="text/javascript" >
                    function abrirdetalleMov(id, valor) {
                        var form_data = {
                            id: id,
                            valor: valor
                        };
                        $.ajax({
                            type: 'POST',
                            url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                            data: form_data,
                            success: function (data) {
                                $("#mdlDetalleMovimiento").html(data);
                                $(".mov").modal('show');
                            }
                        });
                    }
                    function abrirdetalleMov1(id, valor) {
                        var form_data = {
                            id: id,
                            valor: valor
                        };
                        $.ajax({
                            type: 'POST',
                            url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                            data: form_data,
                            success: function (data) {
                                $("#mdlDetalleMovimiento").html(data);
                                $(".mov").modal('show');
                            }
                        });
                    }
                </script>
                <?php
                $sumar = 0;
                $sumaT = 0;
                $valorD = 0;
                $valorC = 0;
                $sql = "SELECT DISTINCT cnt.naturaleza,
                    dtc.valor , 
                    dtc.id_unico 
                  FROM
                    gf_detalle_comprobante dtc 
                  LEFT JOIN
                    gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                  WHERE           dtc.comprobante = $idComprobanteI ";
                $result = $mysqli->query($sql);
                while ($row = mysqli_fetch_row($result)) {
                    if ($row[0] == 1) {
                        if ($row[1] >= 0) {
                            $sumar += $row[1];
                        }
                    } else if ($row[0] == 2) {
                        if ($row[1] <= 0) {
                            $x = (float) substr($row[1], '1');
                            $sumar += $x;
                        }
                    }

                    if ($row[0] == 2) {
                        if ($row[1] >= 0) {
                            $sumaT += $row[1];
                        }
                    } else if ($row[0] == 1) {
                        if ($row[1] <= 0) {
                            $x = (float) substr($row[1], '1');
                            $sumaT += $x;
                        }
                    }
                }
                $valorD = $sumar;
                $valorC = $sumaT;
                #Diferencia
                $diferencia = $valorC - $valorD;
                $w = 0;
                if ($diferencia < 0) {
                    $w = substr($diferencia, 1);
                } else {
                    $w = $diferencia;
                }
                ?>
                <style>
                    .valores:hover{
                        cursor: pointer;
                        color:#1155CC;
                    }
                </style>
                <div class="container">

                </div>
                <div class="col-sm-offset-4  col-sm-5 text-left">
                    <div class="col-sm-2">
                        <div class="form-group" style="margin-top:5px;margin-bottom:-10px" align="left">                                    
                            <label class="control-label">
                                <strong>Totales:</strong>
                            </label>                                
                        </div>
                    </div>                        
                    <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                        <?php if (($valorD) === NULL) { 
                            echo '<label class="control-label valores" title="Suma débito">0</label>';
                        } else {
                            echo '<label class="control-label valores" title="Suma débito">'.number_format($valorD, 2, '.', ',').'</label>';
                        } ?>
                    </div>                        
                    <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                        <?php if ($valorC === NULL) { 
                            echo '<label class="control-label valores" title="Suma crédito">0</label>';
                        } else {
                            echo '<label class="control-label valores" title="Suma crédito">'.number_format($valorC, 2, '.', ',').'</label>';
    
                        } ?>
                    </div>
                    <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                        <?php if ($diferencia === 0) { 
                            echo '<label class="control-label text-right valores" title="Diferencia">0.00</label>';
                        } else {
                            echo '<label class="control-label text-right valores" title="Diferencia">'.number_format($diferencia, 2, '.', ',').'</label>';
                        } ?>                                  
                    </div>                          
                </div>
                <div class="col-sm-offset-11 col-sm-1 text-left" style="margin-top:-18px;">
                    <button type="button" class="btn btn-primary" id="btnGuardarM" onclick="generate_causa(<?php echo $idP . ',' . $pptal; ?>)" style="font-size:10px;padding:5px;margin-left:-30px">Registrar Pago</button>
                </div>
                <script>
                    $("#btnGuardarM").click(function () {
                        if ($("#sltBanco").val() == "") {
                            $("#mdlBanco").modal('show');
                        } else {
                            var form_data = {
                                banco: $("#sltBanco").val(),
                                fecha: $("#fecha").val(),
                                descripcion: $("#txtDescripcion").val(),
                                valor:<?php echo $w; ?>,
                                valorEjecucion: '0',
                                comprobante: $("#id").val(),
                                tercero: $("#slttercero").val(),
                                proyecto: $("#sltproyecto").val(),
                                centro: $("#sltcentroc").val()
                            };
                            var result = '';

                            $.ajax({
                                type: 'POST',
                                url: "consultarComprobanteIngreso/GuardarBanco.php",
                                data: form_data,
                                success: function (data) {
                                    result = JSON.parse(data);
                                    console.log(data);
                                    if (result == true) {
                                        $("#guardado").modal('show');
                                    } else {
                                        $("#noguardado").modal('show');
                                    }
                                }
                            });
                        }
                    });
                </script>
            </div>
            <div class="col-sm-2" style="margin-top:-347px;">
                <table class="tablaC table-condensed text-center" align="center">
                    <thead>
                        <tr>
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>                                    
                        </tr>
                    </thead>
                    <tbody>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GF_CUENTA_P.php">CUENTA</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>                                      
                                <a href="registrar_TERCERO_CLIENTE_NATURAL.php" class="btn btn-primary btnInfo">CLIENTE JURIDICA</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_CENTRO_COSTO.php">CENTRO COSTO</a>
                            </td>
                        </tr>                               
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GF_PROYECTO.php">PROYECTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a id="btn_pptal" class="btn btn-primary btnInfo" onclick="open_modal_pptal_ing(<?php echo $pptal ?>)">COMPROBANTE PPTAL<br/>INGRESO</a>
                            </td>
                        </tr>                              
                        <?php
                        if (empty($pptal)) {
                            echo "\n\t<script>";
                            echo "\n\t\t$('#btn_pptal').attr('disabled',true);";
                            echo "\n\t</script>";
                        }
                        if (!empty($idP)) {
                            $sqlTH = "SELECT      tp.tipo_comp_hom 
                                            FROM        gf_comprobante_cnt cnt 
                                            LEFT JOIN   gf_tipo_comprobante tp 
                                            ON          cnt.tipocomprobante = tp.id_unico 
                                            WHERE       cnt.id_unico        = $idP";
                            $resultTH = $mysqli->query($sqlTH);
                            $c = mysqli_num_rows($resultTH);
                            if ($c > 0) {
                                $tipo_hom = mysqli_fetch_row($resultTH);
                                if (!empty($tipo_hom[0])) {
                                    $sqlRC = "  SELECT      cnt1.id_unico as 'crn',
                                                              dtp.comprobantepptal as 'pptal'
                                                  FROM        gf_detalle_comprobante dtc                                                 
                                                  LEFT JOIN   gf_detalle_comprobante dtc1       ON dtc1.detalleafectado         = dtc.id_unico
                                                  LEFT JOIN   gf_comprobante_cnt cnt1           ON dtc1.comprobante             = cnt1.id_unico
                                                  LEFT JOIN   gf_detalle_comprobante_pptal dtp  ON dtc1.detallecomprobantepptal = dtp.id_unico
                                                  WHERE       dtc.comprobante = $idP
                                                  AND         cnt1.tipocomprobante = $tipo_hom[0]";
                                    $resultRc = $mysqli->query($sqlRC);
                                    $cantidad = mysqli_num_rows($resultRc);
                                    if ($cantidad > 0) {
                                        $id_crn = $resultRc->fetch_row();
                                        if (!empty($id_crn[0])) {
                                            echo "<tr>";
                                            echo "<td>";
                                            echo "<a class=\"btn btn-primary btnInfo\" id=\"btnCausar\" onclick=\"javascript:abrirModalCausa(" . $id_crn[0] . ")\" >COMPROBANTE<br/>CAUSACIÓN CNT</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        if (!empty($id_crn[1])) {
                                            echo "<tr>";
                                            echo "<td>";
                                            echo "<a class=\"btn btn-primary btnInfo\" id=\"btnCausar\" onclick=\"javascript:abrirModalCausaPptal(" . $id_crn[1] . ")\" >COMPROBANTE<br/>CAUSACIÓN PPTAL</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo "<a class=\"btn btn-primary btnInfo\" disabled>COMPROBANTE<br/>CAUSACIÓN CNT</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>";
                                        echo "<a class=\"btn btn-primary btnInfo\" disabled>COMPROBANTE<br/>CAUSACIÓN PPTAL</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                        echo print_btn_c(0, $idP, $pptal);
                                    }
                                }
                            } else {
                                echo "<tr>";
                                echo "<td>";
                                echo "<a class=\"btn btn-primary btnInfo\" disabled>COMPROBANTE<br/>CAUSACIÓN CNT</a>";
                                echo "</td>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<td>";
                                echo "<a class=\"btn btn-primary btnInfo\" disabled>COMPROBANTE<br/>CAUSACIÓN PPTAL</a>";
                                echo "</td>";
                                echo "</tr>";
                                echo print_btn_c(0, $idP, $pptal);
                            }
                        }

                        function print_btn_c($status, $idCnt = NULL, $idPptal = NULL) {
                            $html = "";
                            $html .= "\n\t<tr>";
                            $html .= "\n\t\t<td>";
                            if ($status === 0) {
                                $html .= "<button type=\"button\" class=\"btn btn-primary btnInfo\" id=\"btnGC\" onclick=\"generate_causa($idCnt, $idPptal)\">GENERAR<br/>CAUSACIÓN</button>";
                            } else {
                                $html .= "<a class=\"btn btn-primary btnInfo\" disabled>GENERAR CAUSACIÓN</a>";
                            }
                            $html .= "\n\t\t</td>";
                            $html .= "\n\t</tr>";
                            return $html;
                        }

                        ########################CONTAR LOD DETALLES###################
                        if (!empty($_SESSION['idComprobanteI'])) {
                            $dn = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = " . $_SESSION['idComprobanteI'];
                            $dn = $mysqli->query($dn);
                            $dn = mysqli_num_rows($dn);
                            if ($dn > 10) {
                                ?>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="GF_DETALLES_COMPROBANTE_INGRESO.php?id=<?php echo md5($_SESSION['idComprobanteI']) ?>">VER DETALLES</a>
                                    </td>
                                </tr>
                            <?php }
                        } ?>   
                    </tbody>
                </table>
            </div>                
        </div>
    </div>
<?php require_once './footer.php'; ?>
    <!-- Modal de carga de datos -->
    <div class="modal fade" id="mdlBanco" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No hay un banco seleccionado</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un tipo de comprobante.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales de guardado -->
    <div class="modal fade" id="guardado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG" class="btn" onclick="reload()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noguardado" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Moidales de eliminado -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Detalle Ingreso?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
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
    <div class="modal fade" id="infoM" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="noModifico" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
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
    <?php
    require_once 'modalComprobanteCausacion_Ingreso.php';
    require_once 'modal_pptal_ing.php';
    ?>      
    <script src="js/bootstrap.min.js"></script>
    <script>
        function generate_causa(id, idpptal) {
            var result = '';
            $.ajax({
                type: "POST",
                url: "jsonPptal/comprobantesIngresoJson.php",
                data: {
                    action: 22,
                    id_cnt: id,
                    id_pptal: idpptal
                },
                success: function (data, textStatus, jqXHR) {
                    console.log('Causacion' + data)
                    result = JSON.parse(data);
                    if (result == true) {
                        $("#guardado").modal('show');
                    } else {
                        $("#noguardado").modal('show');
                    }
                }
            }).error(function (data, textStatus, error) {
                alert('Data:' + data + ', Estado:' + textStatus + '- Error:' + error);
            });
        }

        $("#modal_pptal_ing").on('shown.bs.modal', function () {
            try {
                var dataTable = $("#tablaDetalle_P").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            } catch (err) {
            }
        });

        $("#modalComprobanteP").on('shown.bs.modal', function () {
            try {
                var dataTable = $("#tablaDetalleP").DataTable();
                dataTable.columns.adjust().responsive.recalc();
            } catch (err) {
            }
        });
        function modificarComprobante() {
                            var tipoComprobante = $("#sltTipoM").val();
                            var fecha = $("#fecha").val();
                            var numero = $("#txtNumero").val();
                            var tercero = $("#sltTercero").val();
                            var claseContrato = $("#sltClaseContrato").val();
                            var numeroContrato = $("#txtNumeroCT").val();
                            var descripcion = $("#txtDescripcion").val();
                            var id = $("#id").val();

                            var form_data = {
                                is_ajax: 1,
                                id: id,
                                fecha: fecha,
                                tipoComprobante: tipoComprobante,
                                numero: numero,
                                tercero: tercero,
                                descripcion: descripcion,
                                claseContrato: claseContrato,
                                numeroContrato: numeroContrato
                            };

                            var result = ' ';
                            $.ajax({
                                type: 'POST',
                                url: "json/modificarComprobanteIngreso.php",
                                data: form_data,
                                success: function (data) {
                                    result = JSON.parse(data);
                                    if (result == true) {
                                        $("#infoM").modal('show');
                                    } else {
                                        $("#noModifico").modal('show');
                                    }
                                }
                            });
                        }
    </script>                       
    <script type="text/javascript">
        function eliminar(id, pptal) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminarDetalleComprobanteIngreso.php?id=" + id + "&pptal=" + pptal,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result == true)
                            $("#myModal1").modal('show');
                        else
                            $("#myModal2").modal('show');
                    }
                });
            });
        }
        function cargarT2(id) {

            var padre = 0;
            $("#sltC" + id).change(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;

                } else {
                    padre = $("#sltC" + id).val();
                }

                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };

                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultarTercero.php",
                    data: form_data,
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero' + id);
                        if (data == 1) {
                            tercero.disabled = false;
                        } else if (data == 2) {

                        }
                    }
                });
            });
        }
        function cargarT(id) {

            var padre = 0;
            $("#sltC" + id).append(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;

                } else {
                    padre = $("#sltC" + id).val();
                }

                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };

                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultarTercero.php",
                    data: form_data,
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero' + id);
                        if (data == 1) {
                            tercero.disabled = false;
                        } else if (data == 2) {

                        }
                    }
                });
            });
        }

        function cargarCentro(id) {
            $("#sltcentroC" + id).prop('disabled', true);
            var padre = 0;
            $("#sltC" + id).append(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;
                    $("#sltcentroC" + id).prop('disabled', true);
                } else {
                    padre = $("#sltC" + id).val();
                }
                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };
                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultarCentroC.php",
                    data: form_data,
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC' + id);
                        if (data == 1) {
                            centro.disabled = false;
                        } else if (data == 2) {
                            centro.disabled = true;
                        }
                    }
                });
            });
        }

        function cargarCentro2(id) {
            $("#sltcentroC" + id).prop('disabled', true);
            var padre = 0;
            $("#sltC" + id).append(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;
                    $("#sltcentroC" + id).prop('disabled', true);
                } else {
                    padre = $("#sltC" + id).val();
                }
                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };
                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultarCentroC.php",
                    data: form_data,
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC' + id);
                        if (data == 1) {
                            centro.disabled = false;
                        } else if (data == 2) {
                            centro.disabled = true;
                        }
                    }
                });
            });
        }

        function cargarProyecto(id) {
            var padre = 0;
            $("#sltProyecto" + id).prop('disabled', true);
            $("#sltC" + id).append(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;
                    $("#sltProyecto" + id).prop('disabled', true);
                } else {
                    padre = $("#sltC" + id).val();
                }
                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };
                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultaProyecto.php",
                    data: form_data,
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto' + id);
                        if (data == 1) {
                            proyecto.disabled = false;
                        } else if (data == 2) {
                            $("#sltProyecto" + id).prop('disabled', true);
                        }
                    }
                });
            });
        }
        function cargarProyecto2(id) {
            var padre = 0;
            $("#sltProyecto" + id).prop('disabled', true);
            $("#sltC" + id).change(function () {
                if ($("#sltC" + id).val() == "" || $("#sltC" + id).val() == 0) {
                    padre = 0;
                    $("#sltProyecto" + id).prop('disabled', true);
                } else {
                    padre = $("#sltC" + id).val();
                }
                var form_data = {
                    is_ajax: 1,
                    data: +padre
                };
                $.ajax({
                    type: "POST",
                    url: "consultasDetalleComprobante/consultaProyecto.php",
                    data: form_data,
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto' + id);
                        if (data == 1) {
                            proyecto.disabled = false;
                        } else if (data == 2) {
                            $("#sltProyecto" + id).prop('disabled', true);
                        }
                    }
                });
            });
        }

        function rubroFuente(id) {
            $("#sltconcepto" + id).change(function () {
                var form_data = {
                    is_ajax: 1,
                    concepto: $("#sltconcepto" + id).val()
                };
                $.ajax({
                    type: 'POST',
                    url: "consultarComprobanteIngreso/conceptoRubro.php",
                    data: form_data,
                    success: function (data) {
                        $("#sltrubroFte" + id).html(data).fadeIn();
                    }
                });
            });
        }

        function rubroCuenta(id) {
            $("#sltrubroFte" + id).change(function () {
                var form_data = {
                    is_ajax: 1,
                    rubro: $("#sltrubroFte" + id).val()
                };
                $.ajax({
                    type: 'POST',
                    url: "consultarComprobanteIngreso/cuenta.php",
                    data: form_data,
                    success: function (data) {
                        $("#sltC" + id).html(data).fadeIn();
                    }
                });
            });
        }
        function modificar(id) {
            if (($("#idPrevio").val() != 0) || ($("#idPrevio").val() != "")) {
                var sltcuentaC = 'sltC' + $("#idPrevio").val();
                var lblCuentaC = 'cuenta' + $("#idPrevio").val();
                var sltTerceroC = 'sltTercero' + $("#idPrevio").val();
                var lblTerceroC = 'tercero' + $("#idPrevio").val();
                var sltCentroCC = 'sltcentroC' + $("#idPrevio").val();
                var lblCentroCC = 'centroC' + $("#idPrevio").val();
                var sltProyectoC = 'sltProyecto' + $("#idPrevio").val();
                var lblProyectoC = 'proyecto' + $("#idPrevio").val();
                var txtDebitoC = 'txtDebito' + $("#idPrevio").val();
                var lblDebitoC = 'debitoP' + $("#idPrevio").val();
                var txtCreditoC = 'txtCredito' + $("#idPrevio").val();
                var lblCreditoC = 'creditoP' + $("#idPrevio").val();
                var guardarC = 'guardar' + $("#idPrevio").val();
                var cancelarC = 'cancelar' + $("#idPrevio").val();
                var tablaC = 'tab' + $("#idPrevio").val();
                var lblRubroFuenteC = 'rubroFuente' + $("#idPrevio").val();
                var sltRubroFuenteC = 'sltrubroFte' + $("#idPrevio").val();

                $("#" + sltcuentaC).css('display', 'none');
                $("#" + lblCuentaC).css('display', 'block');
                $("#" + sltTerceroC).css('display', 'none');
                $("#" + lblTerceroC).css('display', 'block');
                $("#" + sltCentroCC).css('display', 'none');
                $("#" + lblCentroCC).css('display', 'block');
                $("#" + sltProyectoC).css('display', 'none');
                $("#" + lblProyectoC).css('display', 'block');
                $("#" + txtDebitoC).css('display', 'none');
                $("#" + lblDebitoC).css('display', 'block');
                $("#" + txtCreditoC).css('display', 'none');
                $("#" + lblCreditoC).css('display', 'block');
                $("#" + guardarC).css('display', 'none');
                $("#" + cancelarC).css('display', 'none');
                $("#" + tablaC).css('display', 'none');
                $("#" + lblRubroFuenteC).css('display', 'block');
                $("#" + sltRubroFuenteC).css('display', 'none');
            }

            var sltcuenta = 'sltC' + id;
            var lblCuenta = 'cuenta' + id;
            var sltTercero = 'sltTercero' + id;
            var lblTercero = 'tercero' + id;
            var sltCentroC = 'sltcentroC' + id;
            var lblCentroC = 'centroC' + id;
            var sltProyecto = 'sltProyecto' + id;
            var lblProyecto = 'proyecto' + id;
            var txtDebito = 'txtDebito' + id;
            var lblDebito = 'debitoP' + id;
            var txtCredito = 'txtCredito' + id;
            var lblCredito = 'creditoP' + id;
            var guardar = 'guardar' + id;
            var cancelar = 'cancelar' + id;
            var tabla = 'tab' + id;
            var sltRubroFuente = 'sltrubroFte' + id;
            var lblRubroFuente = 'rubroFuente' + id;

            $("#" + sltcuenta).css('display', 'block');
            $("#" + lblCuenta).css('display', 'none');
            $("#" + sltTercero).css('display', 'block');
            $("#" + lblTercero).css('display', 'none');
            $("#" + sltCentroC).css('display', 'block');
            $("#" + lblCentroC).css('display', 'none');
            $("#" + sltProyecto).css('display', 'block');
            $("#" + lblProyecto).css('display', 'none');
            $("#" + txtDebito).css('display', 'block');
            $("#" + lblDebito).css('display', 'none');
            $("#" + txtCredito).css('display', 'block');
            $("#" + lblCredito).css('display', 'none');
            $("#" + guardar).css('display', 'block');
            $("#" + cancelar).css('display', 'block');
            $("#" + tabla).css('display', 'block');
            $("#" + sltRubroFuente).css('display', 'block');
            $("#" + lblRubroFuente).css('display', 'none');
            $("#idActual").val(id);
            if ($("#idPrevio").val() != id) {
                $("#idPrevio").val(id);
            }
        }

        function cancelar(id) {
            var rf = 's2id_sltrubroFte' + id;
            $("#" + rf).css('display', 'none');
            var ct = 's2id_sltC' + id;
            $("#" + ct).css('display', 'none');
            var ter = 's2id_sltTercero' + id;
            $("#" + ter).css('display', 'none');

            var sltcuenta = 'sltC' + id;
            var lblCuenta = 'cuenta' + id;
            var sltTercero = 'sltTercero' + id;
            var lblTercero = 'tercero' + id;
            var sltCentroC = 'sltcentroC' + id;
            var lblCentroC = 'centroC' + id;
            var sltProyecto = 'sltProyecto' + id;
            var lblProyecto = 'proyecto' + id;
            var txtDebito = 'txtDebito' + id;
            var lblDebito = 'debitoP' + id;
            var txtCredito = 'txtCredito' + id;
            var lblCredito = 'creditoP' + id;
            var guardar = 'guardar' + id;
            var cancelar = 'cancelar' + id;
            var tabla = 'tab' + id;
            var sltRubroFuente = 'sltrubroFte' + id;
            var lblRubroFuente = 'rubroFuente' + id;

            $("#" + sltcuenta).css('display', 'none');
            $("#" + lblCuenta).css('display', 'block');
            $("#" + sltTercero).css('display', 'none');
            $("#" + lblTercero).css('display', 'block');
            $("#" + sltCentroC).css('display', 'none');
            $("#" + lblCentroC).css('display', 'block');
            $("#" + sltProyecto).css('display', 'none');
            $("#" + lblProyecto).css('display', 'block');
            $("#" + txtDebito).css('display', 'none');
            $("#" + lblDebito).css('display', 'block');
            $("#" + txtCredito).css('display', 'none');
            $("#" + lblCredito).css('display', 'block');
            $("#" + guardar).css('display', 'none');
            $("#" + cancelar).css('display', 'none');
            $("#" + tabla).css('display', 'none');
            $("#" + lblRubroFuente).css('display', 'block');
            $("#" + sltRubroFuente).css('display', 'none');
        }

        function guardarCambios(id) {
            var sltcuenta = 'sltC' + id;
            var sltTercero = 'sltTercero' + id;
            var sltCentroC = 'sltcentroC' + id;
            var sltProyecto = 'sltProyecto' + id;
            var txtDebito = 'txtDebito' + id;
            var txtCredito = 'txtCredito' + id;
            var sltConcepto = 'sltconcepto' + id;
            var sltRubroFuente = 'sltrubroFte' + id;

            var form_data = {
                is_ajax: 1,
                id: +id,
                cuenta: $("#" + sltcuenta).val(),
                tercero: $("#" + sltTercero).val(),
                centroC: $("#" + sltCentroC).val(),
                proyecto: $("#" + sltProyecto).val(),
                debito: $("#" + txtDebito).val(),
                credito: $("#" + txtCredito).val(),
                concepto: $("#" + sltConcepto).val(),
                rubroFuente: $("#" + sltRubroFuente).val()
            };
            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarDetalleComprobanteIngreso.php",
                data: form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if (result == true) {
                        $("#infoM").modal('show');
                    } else {
                        $("#noModifico").modal('show');
                    }
                }
            });
        }

        function limpiar_campos() {
            $("#sltConcepto").prop('selectedIndex', 0);
            $("#sltRubroFuente").prop('selectedIndex', 0);
            $("#sltcuenta").prop('selectedIndex', 0);
            $("#txtValor").val('');
        }
        ;

        function show_inputs(id) {
            if (($("#idPrevio").val() != 0) || ($("#idPrevio").val() != "")) {
                var txtDebitoC = 'txtDebito' + $("#idPrevio").val();
                var lblDebitoC = 'debitoP' + $("#idPrevio").val();
                var txtCreditoC = 'txtCredito' + $("#idPrevio").val();
                var lblCreditoC = 'creditoP' + $("#idPrevio").val();
                var guardarC = 'guardarX' + $("#idPrevio").val();
                var cancelarC = 'cancelar' + $("#idPrevio").val();
                var tablaC = 'tab' + $("#idPrevio").val();

                $("#" + txtDebitoC).css('display', 'none');
                $("#" + lblDebitoC).css('display', 'block');
                $("#" + txtCreditoC).css('display', 'none');
                $("#" + lblCreditoC).css('display', 'block');
                $("#" + guardarC).css('display', 'none');
                $("#" + cancelarC).css('display', 'none');
                $("#" + tablaC).css('display', 'none');
            }

            var txtDebito = 'txtDebito' + id;
            var lblDebito = 'debitoP' + id;
            var txtCredito = 'txtCredito' + id;
            var lblCredito = 'creditoP' + id;
            var guardar = 'guardarX' + id;
            var cancelar = 'cancelar' + id;
            var tabla = 'tab' + id;

            $("#" + txtDebito).css('display', 'block');
            $("#" + lblDebito).css('display', 'none');
            $("#" + txtCredito).css('display', 'block');
            $("#" + lblCredito).css('display', 'none');
            $("#" + guardar).css('display', 'block');
            $("#" + cancelar).css('display', 'block');
            $("#" + tabla).css('display', 'block');

            $("#idActual").val(id);
            if ($("#idPrevio").val() != id) {
                $("#idPrevio").val(id);
            }
        }
        //Función para actualizar banco
        function update_bank(id) {
            var debito = $("#txtDebito" + id).val();    //valor en debito
            var credito = $("#txtCredito" + id).val();  //valor en credito
            var form_data = {
                existente: 46,
                debito: debito,
                credito: credito,
                id: id
            };
            var result = '';
            //envio ajax
            $.ajax({
                type: 'POST',
                url: 'consultasBasicas/consultarNumeros.php',
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    result = JSON.parse(data);
                    if (result === true) {
                        $("#infoM").modal('show');
                    } else {
                        $("#noModifico").modal('show');
                    }
                }, error: function (data, textStatus, jqXHR) {
                    alert('Error : ' + data + ' ,' + textStatus + ' ,' + jqXHR);
                }
            });
        }
        //Función para generar causación
        function causacion() {
            //Array de envio
            var form_data = {
                existente: 47,
                comprobante:<?php echo $idP; ?>
            };
            //Envio ajax
            $.ajax({
                url: 'consultasBasicas/consultarNumeros.php',
                type: 'POST',
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                }, error: function (data, textStatus, jqXHR) {
                    alert('Error : D' + data + ', status :' + textStatus + ', jqXHR : ' + jqXHR);
                }
            });
        }
        //Función para abrir modal de causación @id_com {id de comprobante de reconocimiento}
        function abrirModalCausa(id_com) {
            //Array de envio
            var form_data = {
                com: id_com
            };
            //Envio ajax
            $.ajax({
                url: 'modalComprobanteCausacion_Ingreso.php#modalCausacion',
                type: 'POST',
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalCausacion").html(data);
                    $(".causa").modal('show');
                }, error: function (data, textStatus, jqXHR) {
                    alert('Error : D' + data + ', status :' + textStatus + ', jqXHR : ' + jqXHR);
                }
            });
        }
        //Funcion abrir modal de causación
        function abrirModalCausaPptal(id_pptal) {
            //Vector de envio con mi variable
            var form_data = {
                idP: id_pptal
            };
            $.ajax({
                type: 'POST',
                url: "modalConsultaComprobanteP.php",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalComprobanteP").html(data);
                    $(".comprobantep").modal('show');
                }
            });
        }
    </script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();
        $("#slttercero").select2();
        $("#sltBanco").select2();
        $("#sltConcepto").select2();
        $('#btnModifico').click(function () {
            window.location.reload();
        });
        $('#btnNoModifico').click(function () {
            window.location.reload();
        });
        $('#ver1').click(function () {
            window.location.reload();
        });
        $('#ver2').click(function () {
            window.location.reload();
        });
        $('#btnG').click(function () {
            window.location.reload();
        });
        $('#btnG2').click(function () {
            window.location.reload();
        });
        $("#btnMdlEliminadoP").click(function () {
            window.location.reload();
        });
        function reload() {
            window.location.reload();
        }

        $("#modalCausacion").on('shown.bs.modal', function () {
            var dataTable = $("#tablaDetalleC").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        });

        function open_modal_pptal_ing(id_pptal) {
            var form_data = {
                id_pptal: id_pptal
            };
            $.ajax({
                type: "POST",
                url: "modal_pptal_ing.php#modal_pptal_ing",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modal_pptal_ing").html(data);
                    $(".pptal_ing").modal('show');
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                alert('XHR :' + jqXHR + ' - textStatus :' + textStatus + ' - errorThrown :' + errorThrown);
            });
        }
    </script>           
<?php
require_once './modalConsultaComprobanteP.php';
?>      	
    <div class="modal fade" id="modDesBal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">		          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No puede abandonar este formulario ya que no está balanceado. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnDesBal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Periodo ya ha sido cerrado, escoja nuevamente la fecha</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php
if (!empty($_SESSION['idComprobanteI'])) {
    $cierre = cierrecnt($_SESSION['idComprobanteI']);
    if ($cierre == 1) {
        ?>
            <script>
                $("#fecha").prop("disabled", true);
                $("#sltTipoM").prop("disabled", true);
                $("#txtNumero").prop("disabled", true);
                $("#sltTercero").prop("disabled", true);
                $("#sltBanco").prop("disabled", true);
                $("#txtDescripcion").prop("disabled", true);
                $("#sltClaseContrato").prop("disabled", true);
                $("#txtNumeroCT").prop("disabled", true);
                $("#btnGuardar").prop("disabled", true);
                $("#txtEstado").prop("disabled", true);
                $("#btnModificar").prop("disabled", true);
                $("#btnGuardar").prop("disabled", true);
                $("#sltConcepto").prop("disabled", true);
                $("#sltRubroFuente").prop("disabled", true);
                $("#sltcuenta").prop("disabled", true);
                $("#slttercero").prop("disabled", true);
                $("#sltcentroc").prop("disabled", true);
                $("#sltproyecto").prop("disabled", true);
                $("#txtValor").prop("disabled", true);
                $("#btnGuardarDetalle").prop("disabled", true);
                $("#btnGuardarM").prop("disabled", true);
                $("#btnEliminar").prop("disabled", true);
                $("#btnGuardarM").attr("disabled", true);
                $("#btnGC").attr("disabled", true);
            </script>    
    <?php } else { ?>
            <script>
                $(document).ready(function () {
                    $("#btnModificar").attr('disabled', false);
                });
            </script>        
    <?php }
} ?>  
<script>
    
        

        $('#sltTercero').on('select2-open', function () {
            $('#s2id_autogen1_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};

                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        console.log(data);
                         option = option+data;
                        $("#sltTercero").html(option);

                    }
                }); 
            });
        });
        $('#slttercero').on('select2-open', function () {
            $('#s2id_autogen6_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};

                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        console.log(data);
                         option = option+data;
                        $("#slttercero").html(option);

                    }
                }); 
            });
        });

        
        function cargarT(id) {
            $("#sltTercero" + id).select2({placeholder: "Tercero", allowClear: true});
            $("#sltTercero" + id).on('select2-open', function () {
                $('.select2-input').keyup(function () {
                    var name = $(this).attr("id"); 
                    $('#'+name).on("keydown", function(e) {
                        let term = e.currentTarget.value;
                        let form_data4 = {action: 8, term: term};
                        console.log('tercero');
                        $.ajax({
                            type:"POST",
                            url:"jsonPptal/gf_tercerosJson.php",
                            data:form_data4,
                            success: function(data){
                                let option = '<option value=""> - </option>';
                                //console.log(data);
                                 option = option+data;
                                $("#sltTercero" + id).html(option);
                                    
                            }
                        }); 
                    }); 
                });
            });
        }
    </script>             
<?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>        
</body>
</html>