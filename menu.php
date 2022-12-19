<?php 
@session_start();
$panno_m = $_SESSION['anno'];
$nit_ut  = "SELECT t.numeroidentificacion, u.rol
FROM gs_usuario u 
LEFT JOIN gf_tercero t ON u.tercero = t.id_unico 
WHERE t.id_unico =".$_SESSION['usuario_tercero'];
$r = $mysqli->query($nit_ut);
$r = mysqli_fetch_row($r);
?>
<link href="css/custom.min_menu.css" rel="stylesheet">
<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<style type="text/css">
    body{
        font-size: 12px;
        font-family:sans-serif;
        height: 105%;
    }
</style>
<div  class="col-sm-2 sidenav text-left" style="background:#002952;overflow-x:hidden; overflow-y:scroll;padding-left:0px">
    <div style="margin-top: -30px;" class=""> 
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu" >
            <div class="menu_section">
                <ul class="nav side-menu" > 
                    <?PHP 
                    #MENU BD
                    require_once('Conexion/conexion.php');
                    require_once('Conexion/ConexionPDO.php');
                    $con = new ConexionPDO();
                    @session_start();
                    $imenud_user = $_SESSION['id_usuario'];
                    $compania    = $_SESSION['compania'];   
                    $rowmenu = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre 
                    FROM gs_menu m
                    LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                    LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                    LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                    WHERE u.id_unico = $imenud_user AND m.predecesor IS NULL 
                    AND m.estado = 1 AND mc.compania = $compania 
                    ORDER BY  cast(m.orden as unsigned)");
                    for ($imenu = 0; $imenu < count($rowmenu); $imenu++) {?>
                        <li>
                            <a href="#" style="padding-left: 0%;"><?php echo $rowmenu[$imenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu" style="padding-left: 10px;margin-top: -20px;">';
                            <?php   $rowmenu2 = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre, m.ruta, 
                            (SELECT COUNT(mh.id_unico) FROM gs_menu mh WHERE mh.predecesor = m.id_unico) as hjs 
                            FROM gs_menu m
                            LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                            LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                            LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                            WHERE u.id_unico = $imenud_user AND m.predecesor = ".$rowmenu[$imenu][0]."
                            AND m.estado = 1 AND mc.compania = $compania 
                            ORDER BY  cast(m.orden as unsigned)");
                            for ($jmenu = 0; $jmenu < count($rowmenu2); $jmenu++) { 
                                if($rowmenu2[$jmenu][3]>0){?>
                                    <li>
                                        <a <?php if($rowmenu2[$jmenu][1]=='PRESUPUESTO'){ ?> onclick="javaScript:buscarFechas(1);"<?php }
                                        elseif($rowmenu2[$jmenu][1]=='CONTABILIDAD'){ ?> onclick="javaScript:buscarFechas(2);"<?php } ?> ><?=$rowmenu2[$jmenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu" style="padding-left: 10px;"> 
                                            <?php   $rowmenu3 = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre, m.ruta, 
                                            (SELECT COUNT(mh.id_unico) FROM gs_menu mh WHERE mh.predecesor = m.id_unico) as hjs 
                                            FROM gs_menu m
                                            LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                                            LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                                            LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                                            WHERE u.id_unico = $imenud_user AND m.predecesor = ".$rowmenu2[$jmenu][0]."
                                            AND m.estado = 1 AND mc.compania = $compania 
                                            ORDER BY  cast(m.orden as unsigned)");
                                            for ($kmenu = 0; $kmenu < count($rowmenu3); $kmenu++) { 
                                                if($rowmenu3[$kmenu][3]>0){?>
                                                    <li>
                                                        <a><?php echo $rowmenu3[$kmenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                                                        <ul class="nav child_menu" style="padding-left: 10px"> 
                                                            <?php   $rowmenu4 = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre, m.ruta, 
                                                                (SELECT COUNT(mh.id_unico) FROM gs_menu mh WHERE mh.predecesor = m.id_unico) as hjs 
                                                                FROM gs_menu m
                                                                LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                                                                LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                                                                LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                                                                WHERE u.id_unico = $imenud_user AND m.predecesor = ".$rowmenu3[$kmenu][0]."
                                                                AND m.estado = 1 AND mc.compania = $compania 
                                                                ORDER BY  cast(m.orden as unsigned)");
                                                                for ($lmenu = 0; $lmenu < count($rowmenu4); $lmenu++) { 
                                                                    if($rowmenu4[$lmenu][3]>0){?>
                                                                        <li>
                                                                            <a><?php echo $rowmenu4[$lmenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                                                                            <ul class="nav child_menu" style="padding-left: 10px"> 
                                                                                <?php   $rowmenu5 = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre, m.ruta, 
                                                                                    (SELECT COUNT(mh.id_unico) FROM gs_menu mh WHERE mh.predecesor = m.id_unico) as hjs 
                                                                                    FROM gs_menu m
                                                                                    LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                                                                                    LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                                                                                    LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                                                                                    WHERE u.id_unico = $imenud_user AND m.predecesor = ".$rowmenu4[$lmenu][0]."
                                                                                    AND m.estado = 1 AND mc.compania = $compania 
                                                                                    ORDER BY  cast(m.orden as unsigned)");
                                                                                    for ($mmenu = 0; $mmenu < count($rowmenu5); $mmenu++) { 
                                                                                        if($rowmenu5[$mmenu][3]>0){?>
                                                                                            <li>
                                                                                                <a><?php echo $rowmenu5[$mmenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                                                                                                <ul class="nav child_menu" style="padding-left: 10px"> 
                                                                                                    <?php   $rowmenu6 = $con->Listar("SELECT DISTINCT m.id_unico, m.nombre, m.ruta, 
                                                                                                    (SELECT COUNT(mh.id_unico) FROM gs_menu mh WHERE mh.predecesor = m.id_unico) as hjs 
                                                                                                    FROM gs_menu m
                                                                                                    LEFT JOIN gs_privilegios_rol pr ON pr.menu = m.id_unico 
                                                                                                    LEFT JOIN gs_usuario u ON pr.rol = u.rol 
                                                                                                    LEFT JOIN gs_menu_compania mc ON mc.menu = m.id_unico 
                                                                                                    WHERE u.id_unico = $imenud_user AND m.predecesor = ".$rowmenu5[$mmenu][0]."
                                                                                                    AND m.estado = 1 AND mc.compania = $compania 
                                                                                                    ORDER BY  cast(m.orden as unsigned)");
                                                                                                    for ($nmenu = 0; $nmenu < count($rowmenu6); $nmenu++) { 
                                                                                                        if($rowmenu6[$nmenu][3]>0){?>
                                                                                                            <li>
                                                                                                                <a><?php echo $rowmenu6[$nmenu][1]; ?><span class="fa fa-chevron-down"></span></a>
                                                                                                                <ul class="nav child_menu" style="padding-left: 10px"> 

                                                                                                                </ul>
                                                                                                            </li>
                                                                                                        <?php } else { 
                                                                                                            if (strpos($rowmenu6[$nmenu][2], '()') !== false) {
                                                                                                                $tp = 'onclick="javascript:'.$rowmenu6[$nmenu][2].'"';
                                                                                                            } else {
                                                                                                                $tp = 'href="'.$rowmenu6[$nmenu][2].'"';
                                                                                                            }?>  
                                                                                                            <li>
                                                                                                                <a <?php echo $tp ?>><?php echo $rowmenu6[$nmenu][1]; ?></a>
                                                                                                            </li>
                                                                                                    <?php } } ?>
                                                                                                </ul>
                                                                                            </li>
                                                                                        <?php } else { 
                                                                                            if (strpos($rowmenu5[$mmenu][2], '()') !== false) {
                                                                                                $tp = 'onclick="javascript:'.$rowmenu5[$mmenu][2].'"';
                                                                                            } else {
                                                                                                $tp = 'href="'.$rowmenu5[$mmenu][2].'"';
                                                                                            }?>  
                                                                                            <li>
                                                                                                <a <?php echo $tp ?>><?php echo $rowmenu5[$mmenu][1]; ?></a>
                                                                                            </li>
                                                                                    <?php } } ?>
                                                                            </ul>
                                                                        </li>
                                                                    <?php } else { 
                                                                        if (strpos($rowmenu4[$lmenu][2], '()') !== false) {
                                                                            $tp = 'onclick="javascript:'.$rowmenu4[$lmenu][2].'"';
                                                                        } else {
                                                                            $tp = 'href="'.$rowmenu4[$lmenu][2].'"';
                                                                        }?>  
                                                                        <li>
                                                                            <a <?php echo $tp ?>><?php echo $rowmenu4[$lmenu][1]; ?></a>
                                                                        </li>
                                                                <?php } } ?>
                                                        </ul>
                                                    </li>
                                                <?php } else { 
                                                    if (strpos($rowmenu3[$kmenu][2], '()') !== false) {
                                                        $tp = 'onclick="javascript:'.$rowmenu3[$kmenu][2].'"';
                                                    } else {
                                                        $tp = 'href="'.$rowmenu3[$kmenu][2].'"';
                                                    }?>  
                                                    <li>
                                                        <a <?php echo $tp ?>><?php echo $rowmenu3[$kmenu][1]; ?></a>
                                                    </li>
                                            <?php } } ?>
                                        </ul>
                                    </li>
                                <?php } else { 
                                    if (strpos($rowmenu2[$jmenu][2], '()') !== false) {
                                        $tp = 'onclick="javascript:'.$rowmenu2[$jmenu][2].'"';
                                    } else {
                                        $tp = 'href="'.$rowmenu2[$jmenu][2].'"';
                                    }?>  
                                    <li>
                                        <a <?php echo $tp ?>><?php echo $rowmenu2[$jmenu][1]; ?></a>
                                    </li>
                            <?php } } ?>
                            </ul>
                        </li>
                    <?php }
                    if ($r[0]=='900849655'){
                    ?>
                    <li>
                        <a href="#" style="padding-left: 0%;">ADMINISTRACIÓN SIGIEP<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu" style="padding-left: 10px;">
                            <li>
                                <a href="GS_CONFIGURACION_CONTRATO.php">Configuración Contrato</a>
                            </li>
                            <li><a href="#" style="padding-left: 0%;">GUIAS SIGIEP <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a href="listar_GS_MODULOS.php">Crear Módulos</a></li>
                                    <li><a href="SUBIR_GS_GUIAS.php">Subir Guías</a></li>
                                    <li><a href="SUBIR_GS_VIDEOS.php">Subir Videos</a></li>
                                </ul>
                            </li>
                            <li><a href="#" style="padding-left: 0%;">FACTURACIÓN ELECTRÓNICA <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a href="GP_EDITAR_FACTURAS_E.php">Modificar Facturas Enviadas</a></li>
                                </ul>
                            </li>
                            <li><a href="#" style="padding-left: 0%;">CONFIGURACIÓN MENÚ<span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu" style="padding-left: 10px">
                                    <li><a href="registrar_GS_MENU.php">Registrar Menú</a></li>
                                    <li><a href="registrar_GS_MENU_COMPANIA.php">Relacionar Menú Compañía</a></li>
                                    <li><a href="registrar_GS_ROL_MENU.php">Relacionar Menú Rol</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
                <br/>
                <div style="margin-left:-20px">
                    <a onclick="salir()"  href="#" style="color:white; font-size: 14px;padding-left: 13%;">
                        <img src="Conexion/cerrar.png" style="width: 55px; height: 55px"/>
                        <i><strong>SALIDA SEGURA</strong></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODALES-->
<script src="js/bootstrap.min_menu.js"></script>
<script src="js/custom.min_menu.js"></script>
<script src="js/select/select2.full.js"></script>
    <script>
    $(document).ready(function() 
    {
        $('#perio').select2();
    });
    </script>
<script>
function abrirMTerceroMenu() {
    $("#terceroMenu").modal('show');
}
    function abrirVALMOV() {
        $("#validarMov").modal('show');
    }
</script>
<div class="modal fade" id="terceroMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Perfil Tercero</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="tercer" id="tercer" class="form-control" title="Tipo Identificación" required>
                        <option >Perfil Tercero</option>
                        <option value="AsociadoJ">Asocíado Jurídica</option>
                        <option value="AsociadoN">Asocíado Natural</option>
                        <option value="BancoJ">Banco Jurídica</option>
                        <option value="Compania">Compañia</option>
                        <option value="ClienteJ">Cliente Juridica</option>
                        <option value="ClienteN">Cliente Natural</option>
                        <option value="ContactoN">Contacto Natural</option>
                        <option value="EmpleadoN">Empleado Natural</option>
                        <option value="ProveeNat">Proveedor Natural</option>
                        <option value="ProveeJur">Proveedor Jurídica</option>
                        <option value="EntAfil">Entidad Afiliación</option>
                        <option value="EntFinan">Entidad Financiera</option>
                        <option value="Todos">Todos los perfiles</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="return terceroMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function terceroMenu() {
        var form = document.getElementById('tercer').value;
        window.location = "terceros.php?tercero=" + form;
    }
    function exportarConfiguracionMenu() {
        $("#modalConceptoMenu").modal('show');
    }
    function abrirExportarTercero(){
        $("#modalExportarTercero").modal('show');
    }
</script>
<div class="modal fade" id="modalConceptoMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informe Configuración Concepto</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportar" id="exportar" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="exportarConfiguracionCMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalExportarTercero" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informe Terceros</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarT" id="exportarT" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="exportarInfTercero()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="validarMov" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Exportar a</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="vali" id="vali" class="form-control" title="Tipo Identificación" required>
                        <option >Tipo Archivo</option>
                        <option value="1">PDF</option>
                        <option value="2">EXCEL</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="return validarMovimientos()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function exportarConfiguracionCMenu() {
        var exportar = document.getElementById('exportar').value;
        if (exportar == 1) {
            window.open("informes/INFORMES_PPTAL/generar_INF_CONFIGURACION_CONCEPTO.php");
        } else {
            window.open("informes/INFORMES_PPTAL/generar_INF_CONFIGURACION_CONCEPTOEXCEL.php");
        }
    }
    function exportarInfTercero(){
        var exportar = document.getElementById('exportarT').value;
        if (exportar == 1) {
            window.open("informes/generar_INF_TERCEROS.php?t=1");
        } else {
            window.open("informes/generar_INF_TERCEROS.php?t=2");
        }
    }
    function validarMovimientos() {
        var form = document.getElementById('vali').value;
        //window.location = "informes/INF_VALIDACION_MOVIMIENTOS_DIF.php?tipo="+form;
       window.open('informes/INF_VALIDACION_MOVIMIENTOS_DIF.php?tipo='+form, '_blank');
        //window.open("informes/INF_VALIDACION_MOVIMIENTOS_DIF.php?tipo="+form", "", ventana);
    }
</script>
<script>
    function exportarPlanPresupuestalMenu() {
        $("#modalPlanPptalMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalPlanPptalMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Plan Presupuestal</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarPlan" id="exportarPlan" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarPlanPptalMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarPlanPptalMenu() {
        var exportar = document.getElementById('exportarPlan').value;
        if (exportar == 1) {
            window.open("informes/generar_INF_PLAN_PPTAL.php?t=1");
        } else {
            window.open("informes/generar_INF_PLAN_PPTAL.php?t=2");
        }
    }
</script>
<script>
    function exportarApropiacionInicialMenu() {
        $("#modalApropInicialMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalApropInicialMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Apropiación Inicial</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarApropiacion" id="exportarApropiacion" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarApropiacionMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarApropiacionMenu() {
        var exportar = document.getElementById('exportarApropiacion').value;
        if (exportar == 1) {
            window.open("informes/generar_INF_APR_INICIALES.php?t=1");
        } else {
            window.open("informes/generar_INF_APR_INICIALES.php?t=2");
        }
    }
</script>
<script>
    function exportarFuentesMenu() {
        $("#modalFuentesMenu").modal('show');
    }
</script>
<div class="modal fade" id="modalFuentesMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Fuentes</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarFuentes" id="exportarFuentes" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="enviarFuentesMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function enviarFuentesMenu() {
        var exportar = document.getElementById('exportarFuentes').value;
        if (exportar == 1) {
            window.open("informes/generar_INF_FUENTES_RECURSO.php?t=1");
        } else {
            window.open("informes/generar_INF_FUENTES_RECURSO.php?t=2");
        }
    }
</script>
<script>
    function salir() {
        $("#mdlSalirMenu").modal('show');
    }

</script>
<div class="modal fade" id="mdlSalirMenu" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmación</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea salir de SIGIEP?.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnSalirCMenu" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
                <button type="button"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#btnSalirCMenu').click(function () {
        document.location = 'Conexion/cerrarSesiones.php';
    });
</script>
<script>
    $(document).ready(function ()
    {
        $("ul").on("click", "li", function () {
            var form_data = {case: 2};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                }
            });
        });
    });
</script>
<script>
    function plancuentasMenu() {
        $("#modalPlanCMenu").modal("show");
    }
</script>
<div class="modal fade" id="modalPlanCMenu" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="exportarplan" id="exportarplan" class="form-control" title="Exportar A" required>
                        <option >Exportar A:</option>
                        <option value="1">PDF</option>
                        <option value="2">Excel</option>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick="exportarPlanMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function exportarPlanMenu() {
        var tipo = $("#exportarplan").val();
        console.log(tipo);
        window.open("informes/generar_INF_PLAN_CUENTAS.php?id=" + tipo);
    }
</script>
<!--*************************Modales Nómina**************************************-->
<script>
    function abrirLN() {
        $("#LiqNom").modal('show');
    }
</script>
<div class="modal fade" id="LiqNom" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Novedad</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
                    <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                    <?php
                    $per = "SELECT e.id_unico, CONCAT( e.codigointerno,' - ',tp.nombre), tp.id_unico, e.tipoprocesonomina
                            FROM gn_periodo e
                            lEFT JOIN  gn_tipo_proceso_nomina tp ON e.tipoprocesonomina = tp.id_unico
                            WHERE e.liquidado != 1 AND e.id_unico !=1 AND e.parametrizacionanno = $panno_m";
                    $periodo = $mysqli->query($per);
                    ?>
                    <select style="display:inline-block;width:270px;height: 40px;" id="perio" name="perio" id="perio" class="form-control" title="Periodo" required>
                        <option >Periodo</option>

                        <?php
                        while ($rowmenuE = mysqli_fetch_row($periodo)) {
                            echo "<option value=" . $rowmenuE[0] . ">" . $rowmenuE[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button"  class="btn" onclick="return guardarNovedadMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function guardarNovedadMenu() {
        //var proces = document.getElementById('proce').value;
        var period = document.getElementById('perio').value;
        window.location = "registrar_GN_NOVEDAD.php?periodo=" + period;
    }
</script>
<script>
    function abrirINF() {
        $("#InFSab").modal('show');
    }
</script>
<div class="modal fade" id="InFSab" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Sabana de Nómina</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
                    <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                    <?php
                    $per = "SELECT e.id_unico, CONCAT( e.codigointerno,' - ',tp.nombre), tp.id_unico, e.tipoprocesonomina
                        FROM gn_periodo e
                        lEFT JOIN  gn_tipo_proceso_nomina tp ON e.tipoprocesonomina = tp.id_unico 
                        WHERE e.parametrizacionanno = $panno_m";
                    $periodo = $mysqli->query($per);
                    ?>
                    <select style="display:inline-block;width:270px;height: 40px;" id="peri" name="peri"  class="form-control" title="Periodo" required>
                        <option >Periodo</option>

                        <?php
                        while ($rowmenuE = mysqli_fetch_row($periodo)) {
                            echo "<option value=" . $rowmenuE[0] . ">" . $rowmenuE[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn" onclick=" consultarSabanaMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function consultarSabanaMenu() {
        var period = document.getElementById('peri').value;
        window.location = "informes/generar_INF_SABANA_NOMINA.php?periodo=" + period;
    }
</script>
<?php
$TipoN = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE tipo = 'LI'";
$TipN = $mysqli->query($TipoN);
?>
<script>
    function abrirInLi() {
        $("#IncapLic").modal('show');
    }
</script>
<div class="modal fade" id="IncapLic" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 500px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Incapacidad / Licencia</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="center">
                    <select style="font-size:15px;height: 40px;" name="inli" id="inli" class="form-control" title="Tipo Identificación" required>
                        <option >Seleccione una opción</option>
                        <?php
                        while ($TN = mysqli_fetch_row($TipN)) {

                            echo "<option value=" . $TN[0] . ">" . $TN[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="inclic" class="btn" onclick="return elegiropMenu()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button><button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function elegiropMenu() {
        var tipo = document.getElementById('inli').value;
        window.location = "GN_INCAPACIDAD.php?tipo=" + tipo;
    }
</script>
<script>
    function abrirCierreN() {
        $("#CierreNom").modal('show');
    }
</script>
<div class="modal fade" id="CierreNom" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Cierre de Nómina</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="form-group"  align="left">
                    <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Periodo:</label>
                    <?php
                    $per = "SELECT e.id_unico, CONCAT( e.codigointerno,' - ',tp.nombre), tp.id_unico, e.tipoprocesonomina
                        FROM gn_periodo e
                        lEFT JOIN  gn_tipo_proceso_nomina tp ON e.tipoprocesonomina = tp.id_unico
                        WHERE e.id_unico !=1 AND e.liquidado !=1 AND e.parametrizacionanno = $panno_m";
                    $periodo = $mysqli->query($per);
                    ?>
                    <select style="display:inline-block;width:270px;height: 40px;" id="period" name="period"  class="form-control" title="Periodo" required>
                        <option >Periodo</option>

                        <?php
                        while ($rowmenuE = mysqli_fetch_row($periodo)) {
                            echo "<option value=" . $rowmenuE[0] . ">" . $rowmenuE[1] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" class="btn"  onclick="javascript:cierreNomMenu();"style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function cierreNomMenu() {
        var id = document.getElementById('period').value;
        window.location = "json/cerrarNominaJson.php?id=" + id;

    }
</script>
<!-----------------------**************Industria Y Comercio***************-------------------------------->
<script>
    function abrirLD() {
        $("#LiqDec").modal('show');
    }
</script>

<script src="js/jquery-ui.js"></script>
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

        $("#sltFechaDec").datepicker({changeMonth: true, }).val();
        $("#sltFecha").datepicker({changeMonth: true, }).val();
    });
</script>
<div class="modal fade" id="LiqDec" role="dialog" align="center" >

    <div class="modal-dialog">
        <div class="modal-content" style="width: 450px;">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Declaración</h4>
            </div>
            <form method="POST" action="javascript:guardarDEC()">
                <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group"  align="left">
                        <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Tipo Periodo:</label>
<?php
$per = "SELECT id, nombre FROM gn_periodicidad";
$periodo = $mysqli->query($per);
?>
                        <select style="display:inline-block;width:270px;height: 40px;" id="peri1" name="peri1" class="form-control" title="Selecione el Tipo de Periodo" required>
                            <option value="">Tipo de Periodo</option>

<?php
#$_SESSION['vinteres'] = 0;
while ($rowmenuE = mysqli_fetch_row($periodo)) {
    echo "<option value=" . $rowmenuE[0] . ">" . $rowmenuE[1] . "</option>";
}
?>
                        </select>
                    </div>
                    <div class="form-group"  align="left">
                        <label style="display:inline-block; width:130px; font-size: 15px;"><strong style="color:#03C1FB; font-size: 20px;">*</strong>Tipo Declaración:</label>
<?php
$Tipodec = "SELECT id_unico, nombre FROM gc_tipo_declaracion ";
$TIDEC = $mysqli->query($Tipodec);
?>
                        <select style="display:inline-block;width:270px;height: 40px;" id="sltTipoD" name="sltTipoD" class="form-control" title="Selecione el Tipo de Declaración" required>
                            <option value="">Tipo Declaración</option>

<?php
while ($TD = mysqli_fetch_row($TIDEC)) {
    echo "<option value=" . $TD[0] . ">" . $TD[1] . "</option>";
}
?>
                        </select>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("#datepicker").datepicker();
                        });
                    </script>

<?php
$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anio1 = $fecha_div[2];
$mes1 = $fecha_div[1];
$dia1 = $fecha_div[0];
$hoy = '' . $dia1 . '/' . $mes1 . '/' . $anio1 . '';
?>
                    <div class="form-group"  align="left">
                        <label for="sltFechaDec" style="display:inline-block; width:130px; font-size: 15px;">Fecha:</label>
                        <input type="text" id="sltFechaDec" name="sltFechaDec" style="display:inline-block;width:270px;height: 40px;" class="form-control" value="<?php echo $hoy; ?>">
                    </div>
                </div>

                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn"  style="color: #000; margin-top: 2px"  title="Siguiente" ><li class="glyphicon glyphicon-forward"></li></button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function guardarDEC() {
            //var proces = document.getElementById('proce').value;

            var perid = document.getElementById('peri1').value;
            var TipoDE = document.getElementById('sltTipoD').value;
            var FecDEC = document.getElementById('sltFechaDec').value;
            //var pesas  = $("#pesas:checked").val();

            window.location = 'registrar_GC_DECLARACION.php?peri2=' + perid + '&TipoD=' + TipoDE + '&FD=' + FecDEC;
        }
    </script>
    <script>
            function buscarFechas(tipo){
                let form_data = {case: 10, tipo:tipo};
                $.ajax({
                    type: "POST",
                    url: "jsonSistema/consultas.php",
                    data: form_data,
                    success: function(response)
                    {
                        console.log(response+'CSF');
                        let resultado = JSON.parse(response);
                        let rta  = resultado["rta"];
                        let html = resultado["html"];
                        if (rta > 0) {
                            $("#fechas_modal").html(html);
                            $("#errorFechas").modal("show");
                        }
                    }
                  });
                
            }
        </script>
</div>

<?php require_once './gs_actualizacion.php'; ?>
