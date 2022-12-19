<?php
require_once './Conexion/ConexionPDO.php';
require_once './head_listar.php';
$con = new ConexionPDO();
$annio =$_SESSION['anno'];
?>
<title>Configuración Cierre Contable</title>
<link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require_once './menu.php'; ?>
            <div class="col-sm-8 text-center" style="margin-top:-22px;">
                <h2 class="tituloform" align="center">Configuración Cierre Contable</h2>
                <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCierreContable.php" style="margin-bottom:-10px">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>                        
                        <div class="form-group form-inline " style="margin-left:2px">                              
                            <div class="form-group form-inline col-sm-4">                              
                                <label class="col-sm-3 control-label">
                                    <strong class="obligado">*</strong>Grupo A Cerrar:
                                </label>
                                <select class="select2_single form-control input-sm col-sm-2" name="sltcuentaCerrar" id="sltcuentaCerrar" title="Seleccione cuenta a cerrar" style="width:150px;height:30%" required>
                                    <option value="">Grupo A Cerrar</option>
                                    <?php
                                    $row = $con->Listar("SELECT id_unico,CONCAT(codi_cuenta,' - ',LOWER(nombre))
                                            FROM gf_cuenta 
                                            WHERE LENGTH(codi_cuenta)=1 AND codi_cuenta>3 
                                            AND parametrizacionanno = $annio 
                                            ORDER BY codi_cuenta ASC");
                                    for($i=0; $i< count($row); $i++){ 
                                        echo '<option value="' . $row[$i][0] . '">' . ucwords($row[$i][1]) . '</option>';
                                    }
                                     $row = $con->Listar("SELECT DISTINCT c.id_unico,CONCAT(c.codi_cuenta,' - ',LOWER(c.nombre))
                                            FROM gf_configuracion_cierre_contable cc 
                                            LEFT JOIN gf_cuenta c ON cc.contracuenta = c.id_unico 
                                            WHERE cc.parametrizacionanno =  $annio AND cc.tipocondicion IS NULL 
                                            ORDER BY c.codi_cuenta ASC");
                                    for($i=0; $i< count($row); $i++){ 
                                        echo '<option value="' . $row[$i][0] . '">' . ucwords($row[$i][1]) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-4">                              
                                <label class="col-sm-3 control-label">
                                    <strong class="obligado">*</strong>Contra Cuenta:
                                </label>
                                <select class="select2_single form-control input-sm col-sm-2" name="sltcontraCuenta" id="sltcontraCuenta" title="Seleccione contra cuenta" style="width:150px;height:30%" required>
                                    <option value="">Contra Cuenta</option>
                                    <?php
                                    $sql1 = $con->Listar("SELECT id_unico,CONCAT(codi_cuenta,' - ',LOWER(nombre)) AS CUENTA
                                            FROM gf_cuenta
                                            WHERE (movimiento = 1 OR centrocosto =1 OR auxiliarproyecto = 1 OR auxiliartercero=1) AND parametrizacionanno = $annio ORDER BY codi_cuenta ASC ");
                                    for ($i = 0; $i < count($sql1); $i++) {
                                        echo '<option value="' . $sql1[$i][0] . '">' . ucwords($sql1[$i][1]) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-4">                              
                                <label class="col-sm-3 control-label">
                                    Tipo Condición:
                                </label>
                                <select class="select2_single form-control input-sm col-sm-2" name="sltTipoCondicion" id="sltTipoCondicion" title="Seleccione tipo condicion" style="width:150px;height:30%" >
                                    <option value="">Tipo Condición</option>
                                    <?php
                                    $sql2 = $con->Listar("SELECT id_unico,LOWER(nombre)
                                            FROM gf_tipo_condicion");
                                    for ($i = 0; $i < count($sql2); $i++) {
                                        echo '<option value="' . $sql2[$i][0] . '">' . ucwords($sql2[$i][1]) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline">                              
                                <div class="col-sm-1" style="margin-left:-60px; margin-top: 10px">                                    
                                    <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar cierre contable"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                    
                                </div>
                            </div>    
                        </div>
                    </form>
                </div>
            </div>
            <input type="hidden" id="idPrevio" value="">
            <input type="hidden" id="idActual" value="">  
            <div class="col-sm-8" style="margin-top:10px">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>    
                            <tr>
                                <td class="oculto"><strong>Identificador</strong></td>
                                <td class="cabeza" width="7%"></td>
                                <td class="cabeza"><strong>Cuenta Cerrar</strong></td>
                                <td class="cabeza"><strong>Contra Cuenta</strong></td>
                                <td class="cabeza"><strong>Tipo Condición</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th class="cabeza" width="7%"></th>
                                <th class="cabeza">Cuenta Cerrar</th>
                                <th class="cabeza">Contra Cuenta</th>
                                <th class="cabeza">Tipo Condición</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $row = $con->Listar("SELECT crr.id_unico,
                                                crr.tipocondicion,
                                                crr.cuentacerrar,
                                                crr.contracuenta,
                                                tpc.id_unico,
                                                tpc.nombre, 
                                                ctc.id_unico, CONCAT(ctc.codi_cuenta,' - ', LOWER(ctc.nombre )),
                                                conc.id_unico, CONCAT(conc.codi_cuenta,' - ', LOWER(conc.nombre ))
                                    FROM gf_configuracion_cierre_contable crr
                                    LEFT JOIN gf_tipo_condicion tpc ON tpc.id_unico = crr.tipocondicion 
                                    LEFT JOIN gf_cuenta ctc ON crr.cuentacerrar = ctc.id_unico 
                                    LEFT JOIN gf_cuenta conc ON crr.contracuenta = conc.id_unico 
                                    WHERE crr.parametrizacionanno = $annio");
                            for ($i = 0;$i < count($row);$i++) { ?>
                                <tr>
                                    <td class="oculto">
                                    <?php echo $row[$i][0]; ?>
                                    </td>
                                    <td class="campos">
                                        <a href="#<?php echo $row[$i][0]; ?>" onclick="javascript:eliminar(<?php echo $row[$i][0]; ?>)" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="#<?php echo $row[$i][0]; ?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[$i][0]; ?>);">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>
                                    </td>
                                    <td class="campos">
                                        <?php
                                        echo '<label class="valorLabel" style="font-weight:normal"  id="lblCerrarCuenta' . $row[$i][0] . '">' . ucwords(($row[$i][7])) . '</label>';
                                        ?>
                                        <div id="selectcuenta<?php echo $row[$i][0]; ?>" style="display:none">
                                        <select style="padding:2px;height:19px; width: 100%" class="select2_single campoD" id="sltcerrarcuenta<?php echo $row[$i][0]; ?>">
                                            <?php
                                            echo '<option value="' . $row[$i][6] . '">' . ucwords($row[$i][7]) . '</option>';
                                            $row6 = $con->Listar("SELECT id_unico,CONCAT(codi_cuenta,' - ',LOWER(nombre)) AS CUENTA
                                                            FROM gf_cuenta WHERE 
                                                            LENGTH(codi_cuenta)=1 AND codi_cuenta>3 
                                                            AND id_unico!=".$row[$i][2]."  
                                                            AND parametrizacionanno = $annio
                                                            ORDER BY codi_cuenta ASC");
                                            
                                            for($j=0;$j < count($row6); $j++){
                                                echo '<option value="' . $row6[$j][0] . '">' . ucwords($row6[$j][1]) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        </div>
                                    </td>
                                    <td class="campos">
                                        <?php
                                        
                                        echo '<label class="valorLabel" style="font-weight:normal"  id="lblContaCuenta' . $row[$i][0] . '">' . ucwords(($row[$i][9])) . '</label>';
                                        ?>
                                        <div id="selectcontracuenta<?php echo $row[$i][0]; ?>" style="display:none">
                                        <select style="padding:2px;height:19px;width: 100%" class="select2_single campoD" id="sltcontracuenta<?php echo $row[$i][0]; ?>">                                    
                                            <?php
                                            echo '<option value="' . $row[$i][8] . '">' . ucwords(($row[$i][9])) . '</option>';
                                            $row7 = $con->Listar("SELECT id_unico,CONCAT(codi_cuenta,'  -  ',LOWER(nombre)) FROM gf_cuenta "
                                                    . "WHERE id_unico!=".$row[$i][3] ." AND movimiento = 1 AND parametrizacionanno = $annio ORDER BY codi_cuenta ASC ");
                                            for($h=0; $h<count($row7); $h++){
                                                echo '<option value="' . $row7[$h][0] . '">' . ucwords(($row7[$h][1])) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        </div>
                                    </td>
                                    <td class="campos">
                                            <?php
                                            echo '<label class="valorLabel" style="font-weight:normal"  id="lblTipoCondición' . $row[$i][0] . '">' . ucwords(mb_strtolower($row[$i][5])) . '</label>';
                                            ?>
                                        <div id="selecttipo<?php echo $row[$i][0]; ?>" style="display:none">
                                        <select style="padding:2px;height:19px;width: 100%" class="select2_single campoD" id="slttipocondicion<?php echo $row[$i][0]; ?>">                                    
                                            <?php
                                            
                                            if (empty($row[$i][4])) {
                                                echo '<option value="">-</option>';
                                                $row8 = $con->Listar("SELECT id_unico,nombre FROM gf_tipo_condicion");
                                            } else {
                                                echo '<option value="' . $row[$i][4] . '">' . ucwords(mb_strtolower($row[$i][5])) . '</option>';
                                                echo '<option value="">-</option>';
                                                $row8 = $con->Listar("SELECT id_unico,nombre FROM gf_tipo_condicion WHERE id_unico!=".$row[$i][1]);
                                            }
                                            for($z=0; $z < count($row8) ; $z++) {
                                                echo '<option value="' . $row8[$z][0] . '">' . ucwords(mb_strtolower($row8[$z][1])) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        </div>
                                        <div >
                                            <table id="tab<?php echo $row[$i][0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[$i][0]; ?>" title="Guardar" id="guardar<?php echo $row[$i][0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[$i][0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[$i][0]; ?>" title="Cancelar" id="cancelar<?php echo $row[$i][0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[$i][0]; ?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-8 col-sm-1" style="margin-top:-120px;"  >
                <table class="tablaC table-condensed text-center" align="center">
                    <thead>
                        <tr>
                        <tr>                                        
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>
                        </tr>
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
                                <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                <a href="GF_TIPO_CONDICION.php" class="btn btn-primary btnInfo">TIPO CONDICIÓN</a>                                       
                            </td>
                        </tr>                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php require_once './footer.php'; ?>
    <!-- select2 -->
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
    </script>
    <script type="text/javascript" >
        function modificar(id) {
            if (($("#idPrevio").val() != 0) || ($("#idPrevio").val() != "")) {
                var lblCerrarCuentaC = 'lblCerrarCuenta' + $("#idPrevio").val();
                var lblContaCuentaC = 'lblContaCuenta' + $("#idPrevio").val();
                var lblTipoCondiciónC = 'lblTipoCondición' + $("#idPrevio").val();
                var selectcuenta = 'selectcuenta' + $("#idPrevio").val();
                var selectcontracuenta = 'selectcontracuenta' + $("#idPrevio").val();
                var guardarC = 'guardar' + $("#idPrevio").val();
                var cancelarC = 'cancelar' + $("#idPrevio").val();
                var tablaC = 'tab' + $("#idPrevio").val();
                var selecttipo = 'selecttipo' + $("#idPrevio").val();

                $("#" + lblCerrarCuentaC).css('display', 'block');
                $("#" + lblContaCuentaC).css('display', 'block');
                $("#" + lblTipoCondiciónC).css('display', 'block');
                $("#" + guardarC).css('display', 'none');
                $("#" + cancelarC).css('display', 'none');
                $("#" + tablaC).css('display', 'none');
                $("#" + selectcuenta).css('display', 'none');
                $("#" + selectcontracuenta).css('display', 'none');
                $("#" + selecttipo).css('display', 'none');
            }

            var lblCerrarCuenta = 'lblCerrarCuenta' + id;
            var lblContaCuenta = 'lblContaCuenta' + id;
            var lblTipoCondición = 'lblTipoCondición' + id;
            var guardar = 'guardar' + id;
            var cancelar = 'cancelar' + id;
            var tabla = 'tab' + id;
            var selectcuenta = 'selectcuenta' + id;
            var selectcontracuenta = 'selectcontracuenta' + id;
            var selecttipo = 'selecttipo' + id;

            $("#" + lblCerrarCuenta).css('display', 'none');
            $("#" + selectcuenta).css('display', 'block');
            $("#" + lblContaCuenta).css('display', 'none');
            $("#" + lblTipoCondición).css('display', 'none');
            $("#" + guardar).css('display', 'block');
            $("#" + cancelar).css('display', 'block');
            $("#" + tabla).css('display', 'block');
            $("#" + selectcontracuenta).css('display', 'block');
            $("#" + selecttipo).css('display', 'block');
            $("#idActual").val(id);
            if ($("#idPrevio").val() != id) {
                $("#idPrevio").val(id);
            }
        }

        function cancelar(id) {
            var lblCerrarCuenta = 'lblCerrarCuenta' + id;
            var lblContaCuenta = 'lblContaCuenta' + id;
            var lblTipoCondición = 'lblTipoCondición' + id;
            var guardar = 'guardar' + id;
            var cancelar = 'cancelar' + id;
            var tabla = 'tab' + id;
            var selectcuenta = 'selectcuenta' + id;
            var selectcontracuenta = 'selectcontracuenta' + id;
            var selecttipo = 'selecttipo' + id;

            $("#" + lblCerrarCuenta).css('display', 'block');
            $("#" + selectcuenta).css('display', 'none');
            $("#" + lblContaCuenta).css('display', 'block');
            $("#" + lblTipoCondición).css('display', 'block');
            $("#" + guardar).css('display', 'none');
            $("#" + cancelar).css('display', 'none');
            $("#" + tabla).css('display', 'block');
            $("#" + selectcontracuenta).css('display', 'none');
            $("#" + selecttipo).css('display', 'none');
            
            
        }

        function eliminar(id) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminarCierreContable.php?id=" + id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result == true)
                            $("#mdlEliminado").modal('show');
                        else
                            $("#mdlNoeliminado").modal('show');
                    }
                });
            });
        }

        function guardarCambios(id) {
            var sltCerrarCuenta = 'sltcerrarcuenta' + id;
            var sltContraCuenta = 'sltcontracuenta' + id;
            var sltTipoCondicion = 'slttipocondicion' + id;

            var form_data = {
                id: id,
                cuentaCerrar: $("#" + sltCerrarCuenta).val(),
                contraCuenta: $("#" + sltContraCuenta).val(),
                tipocondicion: $("#" + sltTipoCondicion).val()
            };
            var result = '';
            $.ajax({
                type: 'POST',
                url: "json/modificarConfiguracionCierre.php",
                data: form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if (result == true) {
                        $("#mdlModificado").modal('show');
                    } else {
                        $("#mdlNomodificado").modal('show');
                    }
                }
            });
        }
    </script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
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
    <div class="modal fade" id="mdlModificado" role="dialog" align="center" >
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
    <div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
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
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Configuración de cierre contable?</p>
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
    <div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
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
    <script type="text/javascript">
        $('#btnModifico').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#btnNoModifico').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#ver1').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#ver2').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#btnG').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript">
        $('#btnG2').click(function () {
            document.location.reload();
        });
    </script>
    <script type="text/javascript" >
        $("#tbmtipoF").click(function () {
            $("#slttipoFactura").focus();
        });
    </script>
</body>
</html>
