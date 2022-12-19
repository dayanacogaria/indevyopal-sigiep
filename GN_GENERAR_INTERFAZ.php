<?php
##############################################################################################################################
#                                MODIFICACIONES
##############################################################################################################################                                                                                                           
#25/07/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once('Conexion/conexion.php');
require_once 'head_listar.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Interfaz Nómina</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="js/md5.pack.js"></script>
<script type="text/javascript">

    $(document).ready(function ()
    {
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
        $("#fechaDis").datepicker({changeMonth: true}).val();
        $("#fechaReg").datepicker({changeMonth: true}).val();
        $("#fechaCxp").datepicker({changeMonth: true}).val();

    });
</script>

<style type="text/css">
    .area
    { 
        height: auto !important;  
    }  

    table.dataTable thead th,table.dataTable thead td
    {
        padding: 1px 18px;
        font-size: 10px;
    }

    table.dataTable tbody td,table.dataTable tbody td
    {
        padding: 1px;
    }
    .dataTables_wrapper .ui-toolbar
    {
        padding: 2px;
        font-size: 10px;
    }

    .control-label
    {
        font-size: 12px;
    }

    .itemListado
    {
        margin-left:5px;
        margin-top:5px;
        width:150px;
        cursor:pointer;
    }

    #listado 
    {
        width:150px;
        height:80px;
        overflow: auto;
        background-color: white;
    }
</style>
</head>
<body>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 
            <!-- Localización de los botones de información a la derecha. -->
            <div class="col-sm-10" style="margin-left: -10px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Interfaz Nómina-Financiera</h2>
                <div class="col-sm-12">
                    <input type="hidden" name="id_dis" id="id_dis">
                    <input type="hidden" name="id_reg" id="id_reg">
                    <input type="hidden" name="id_cxp" id="id_cxp">
                    <input type="hidden" name="id_cxpc" id="id_cxpc">
                    <div class="client-form contenedorForma"  style=""> 
                        <!------Validación Búsqueda---->
                        <?php if (empty($_GET['periodo']) && empty($_GET['gg'])) { ?>
                            <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                            <!--*Formulario para cuando no hay registro de periodo y grupo de Gestión*-->
                            <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                    <div class="col-sm-4" align="left">  
                                        <label for="periodo" class="control-label" ><strong style="color:#03C1FB;">*</strong>Periodo:</label><br>
                                        <select name="periodo" id="periodo" class="select2_single form-control input-sm" title="Seleccione un Periodo" style="width:250px; " required>
                                            <option value="">Periodo</option>
                                            <?php
                                            $periodo = "SELECT DISTINCT p.id_unico, LOWER(p.codigointerno), 
                                                DATE_FORMAT(p.fechainicio,'%d/%m/%Y'), DATE_FORMAT(p.fechafin,'%d/%m/%Y'), 
                                                LOWER(tp.nombre) 
                                                FROM gn_periodo p 
                                                LEFT JOIN gn_tipo_proceso_nomina tp ON tp.id_unico = p.tipoprocesonomina 
                                                LEFT JOIN gn_financiera_nomina fn ON fn.periodo = p.id_unico 
                                                LEFT JOIN gf_comprobante_pptal cp ON fn.disponibilidad = cp.id_unico 
                                                WHERE ((p.parametrizacionanno =$anno AND p.id_unico !=1) 
                                                  OR (p.parametrizacionanno <$anno AND p.id_unico !=1 AND p.liquidado != 1) 
                                                  OR (cp.parametrizacionanno = $anno))
                                                ORDER BY p.fechainicio ASC";
                                            $periodo = $mysqli->query($periodo);
                                            while ($rowP = mysqli_fetch_row($periodo)) {
                                                ?>
                                                <option value="<?php echo $rowP[0] ?>"><?php echo ucwords($rowP[1]) . '  ' . $rowP[2] . ' - ', $rowP[3] . '  ' . ucwords($rowP[4]); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="grupog" class="control-label" ><strong style="color:#03C1FB;">*</strong>Grupo Gestión:</label><br>
                                        <select name="grupog" id="grupog" class="form-control input-sm" title="Grupo Gestión:" style="width:250px; " required>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="tercero" class="control-label" ><strong style="color:#03C1FB;"></strong>Tercero:</label><br>
                                        <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione Tercero" style="width:250px; " required>
                                            <option value="">Tercero</option>
                                            <?php
                                            $tr = "SELECT tr.id_unico, IF(CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos) 
                                                    IS NULL OR CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos) = '',
                                                    (tr.razonsocial),
                                                    CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
                                                   FROM gf_tercero tr 
                                                   LEFT JOIN gf_perfil_tercero  pt ON tr.id_unico = pt.tercero 
                                                   WHERE pt.perfil in (1,2)
                                                   ORDER BY NOMBRE ASC ";
                                            $tr = $mysqli->query($tr);
                                            ?>
                                            <?php
                                            while ($rowT = mysqli_fetch_row($tr)) {
                                                if (empty($rowT[3])) {
                                                    $numeroI = $rowT[2];
                                                } else {
                                                    $numeroI = $rowT[2] . '-' . $rowT[3];
                                                    ;
                                                }
                                                ?>
                                                <option value="<?php echo $rowT[0] ?>"><?php echo ucwords(mb_strtolower($rowT[1])) . ' - ' . $numeroI;
                                            } ?></option>;
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="dis" class="control-label" ><strong style="color:#03C1FB;"></strong>Disponibilidad:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Disponibilidad:</label><br>
                                        <select name="tipoDis" id="tipoDis" class="form-control input-sm" title="Seleccione Tipo Disponibilidad" style="width:200px; " required>
                                            <option value="">Tipo Disponibilidad</option>
                                            <?php
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 14 
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) {
                                                ?>
                                                <option value="<?php echo $row2[0] ?>"><?php echo mb_strtoupper($row2[1]) . ' - ' . ucwords(mb_strtolower($row2[2])); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numdis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numdis" id="numdis" class="form-control input-sm" title="Número Disponibilidad" style="width:250px; " required readonly="true"/>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="fechaDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaDis" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Disponibilidad" style="width:250px; " required readonly="true" />
                                    </div>
                                </div>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="reg" class="control-label" ><strong style="color:#03C1FB;"></strong>Registro:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Registro:</label><br>
                                        <select name="tipoReg" id="tipoReg" class="form-control input-sm" title="Seleccione Tipo Registro" style="width:200px; " required>
                                            <option value="">Tipo Registro</option>
                                            <?php
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 15
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) {
                                                ?>
                                                <option value="<?php echo $row2[0] ?>"><?php echo mb_strtoupper($row2[1]) . ' - ' . ucwords(mb_strtolower($row2[2])); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numReg" id="numReg" class="form-control input-sm" title="Número Registro" style="width:250px; " required readonly="true"/>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="fechaReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaReg" id="fechaReg" class="form-control input-sm" title="Seleccione Fecha Registro" style="width:250px; " required  readonly="true"/>
                                    </div>
                                </div>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="Cxp" class="control-label" ><strong style="color:#03C1FB;"></strong>Cuenta Por Pagar:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Por Pagar:</label><br>
                                        <select name="tipoCxp" id="tipoCxp" class="form-control input-sm" title="Seleccione Tipo Cuenta Por Pagar" style="width:200px; " required>
                                            <option value="">Tipo Cuenta Por Pagar</option>
                                            <?php
                                            $tipo = "SELECT id_unico, codigo, nombre 
                                                FROM gf_tipo_comprobante_pptal 
                                                WHERE clasepptal = 16 
                                                AND tipooperacion = 1 AND vigencia_actual =1 AND compania = $compania 
                                                ORDER BY codigo ASC";
                                            $tipo = $mysqli->query($tipo);
                                            while ($row2 = mysqli_fetch_row($tipo)) {
                                                ?>
                                                <option value="<?php echo $row2[0] ?>"><?php echo mb_strtoupper($row2[1]) . ' - ' . ucwords(mb_strtolower($row2[2])); ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numCxp" id="numCxp" class="form-control input-sm" title="Número Cuenta Por Pagar" style="width:250px; " required readonly="true"/>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="fechaCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaCxp" id="fechaCxp" class="form-control input-sm" title="Seleccione Fecha Cuenta Por Pagar" style="width:250px; " required  readonly/>
                                    </div>
                                </div>
                                <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;" align="right">
                                    <div class="col-sm-12 " align="right"  style="margin-top: -20px; margin-left: -20px;">
                                        <button type="button" onclick="guardar()"  id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                        <input type="hidden" name="MM_insert" >
                                    </div>
                                </div>
                                
                                <input type="hidden" name="MM_insert" >
                            </form>
                        <?php } else { ?>
                            <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                            <!--*Formulario para cuando hay registro de periodo y grupo de Gestión*-->
                            <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                            <?php
                            ####************Buscamos Periodo,Grupo de Gestion y comprobante***********##########
                            $periodo = $_GET['periodo'];
                            $gg = $_GET['gg'];
                            $sc = "SELECT fn.id_unico, fn.disponibilidad, fn.registro, fn.cuenta_pagar, 
                                    fn.tercero, p.id_unico, LOWER(p.codigointerno), 
                                    DATE_FORMAT(p.fechainicio,'%d/%m/%Y'), 
                                    DATE_FORMAT(p.fechafin,'%d/%m/%Y'),
                                    gg.id_unico, LOWER(gg.nombre),
                                    dis.numero, DATE_FORMAT(dis.fecha, '%d/%m/%Y'), tdis.id_unico, CONCAT(UPPER(tdis.codigo),' - ', LOWER(tdis.nombre)) , 
                                    reg.numero, DATE_FORMAT(reg.fecha, '%d/%m/%Y'), treg.id_unico, CONCAT(UPPER(treg.codigo),' - ', LOWER(treg.nombre)) ,
                                    cxp.numero, DATE_FORMAT(cxp.fecha, '%d/%m/%Y'), tcxp.id_unico, CONCAT(UPPER(tcxp.codigo),' - ', LOWER(tcxp.nombre)) , 
                                    tr.id_unico , 
                                    IF(CONCAT_WS(' ',
                                         tr.nombreuno,
                                         tr.nombredos,
                                         tr.apellidouno,
                                         tr.apellidodos) 
                                         IS NULL OR CONCAT_WS(' ',
                                         tr.nombreuno,
                                         tr.nombredos,
                                         tr.apellidouno,
                                         tr.apellidodos) = '',
                                         (tr.razonsocial),
                                         CONCAT_WS(' ',
                                         tr.nombreuno,
                                         tr.nombredos,
                                         tr.apellidouno,
                                         tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion, 
                                         LOWER(tp.nombre), ccnxp.id_unico  
                                    FROM gn_financiera_nomina fn 
                                    LEFT JOIN gn_periodo p ON fn.periodo = p.id_unico
                                    LEFT JOIN gn_grupo_gestion gg ON gg.id_unico = fn.grupo_gestion 
                                    LEFT JOIN gf_comprobante_pptal dis ON fn.disponibilidad = dis.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal tdis ON dis.tipocomprobante = tdis.id_unico 
                                    LEFT JOIN gf_comprobante_pptal reg ON fn.registro = reg.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal treg ON reg.tipocomprobante = treg.id_unico 
                                    LEFT JOIN gf_comprobante_pptal cxp ON fn.cuenta_pagar = cxp.id_unico 
                                    LEFT JOIN gf_tipo_comprobante_pptal tcxp ON cxp.tipocomprobante = tcxp.id_unico 
                                    LEFT JOIN gf_tercero tr ON tr.id_unico = fn.tercero 
                                    LEFT JOIN gn_tipo_proceso_nomina  tp ON tp.id_unico = p.tipoprocesonomina   
                                    LEFT JOIN gf_tipo_comprobante tcx ON tcxp.id_unico = tcx.comprobante_pptal
                                    LEFT JOIN gf_comprobante_cnt ccnxp ON tcx.id_unico = ccnxp.tipocomprobante AND cxp.numero = ccnxp.numero 
                                    WHERE md5(fn.periodo)='$periodo' 
                                    AND md5(fn.grupo_gestion)='$gg'";
                            $sc = $mysqli->query($sc);
                            $row = mysqli_fetch_row($sc);
                            echo '<script>$(document).ready(function ()
                                 {
                                 asignarids('.$row[1].','.$row[2].','.$row[3].','.$row[28].');
                                 })</script>';
                            #*** Verificar si la cuenta por pagar tiene egreso **#
                            $cxp = $row[3];
                            $sqle = "SELECT DISTINCT cp.* 
                                    FROM 
                                   gf_detalle_comprobante_pptal dpa
                                 LEFT JOIN
                                   gf_detalle_comprobante_pptal dp ON dpa.id_unico = dp.comprobanteafectado 
                                 LEFT JOIN 
                                   gf_comprobante_pptal cp ON dp.comprobantepptal = cp.id_unico 
                                 WHERE
                                   dpa.comprobantepptal =$cxp AND dp.comprobanteafectado is not null";
                            $sqle = $mysqli->query($sqle);
                            $eg   = mysqli_num_rows($sqle);
                            ?>
                            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:interfaz()">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px;"> 
                                    <div class="col-sm-4" align="left">  
                                        <input type="hidden" name="id" id="id" value="<?php echo $row[0] ?>">
                                        <label for="periodo" class="control-label" ><strong style="color:#03C1FB;">*</strong>Periodo:</label><br>
                                        <select name="periodo" id="periodo" class="select2_single form-control input-sm" title="Seleccione un Periodo" style="width:250px; " disabled="true">
                                            <option value="<?php echo $row[5] ?>"><?php echo ucwords($row[6]) . ' ' . $row[7] . ' - ' . $row[8] . '  ' . ucwords($row[27]) ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left">  
                                        <label for="grupog" class="control-label" ><strong style="color:#03C1FB;">*</strong>Grupo Gestión:</label><br>
                                        <select name="grupog" id="grupog" class="select2_single form-control input-sm" title="Grupo Gestión:" style="width:250px; " disabled="true">
                                            <option value="<?php echo $row[9] ?>"><?php echo ucwords($row[10]) ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3" align="left">  
                                        <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                        <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione Tercero" style="width:250px; " disabled="true">
                                            <option value="<?php echo $row[23] ?>"><?php echo ucwords($row[24]) . ' - ' . $row[25] . ' ' . $row[26] ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1" align="left">  
                                        <a href="GN_GENERAR_INTERFAZ.php" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px" title="Nuevo"><i class="glyphicon glyphicon-plus"></i></a>
                                    </div>
                                </div>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="dis" class="control-label" ><strong style="color:#03C1FB;"></strong>Disponibilidad:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Disponibilidad:</label><br>
                                        <select name="tipoDis" id="tipoDis" class="form-control input-sm" title="Seleccione Tipo Disponibilidad" style="width:200px; " required>
                                            <option value="<?php echo $row[13] ?>"><?php echo ucwords($row[14]) ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numdis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numdis" id="numdis" class="form-control input-sm" title="Número Disponibilidad" style="width:250px; " required readonly="true" value="<?php echo $row[11] ?>"/>
                                    </div>
                                    <div class="col-sm-3" align="left">  
                                        <label for="fechaDis" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaDis" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Disponibilidad" style="width:250px; " disabled="true" value="<?php echo $row[12] ?>" />
                                    </div>
                                    <div class="col-sm-1" align="left">  
                                        <a href="EXPEDIR_DISPONIBILIDAD_PPTAL.php?dis=<?php echo md5($row[1]) ?>" target="_blank" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                    </div>
                                </div>

                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="reg" class="control-label" ><strong style="color:#03C1FB;"></strong>Registro:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Registro:</label><br>
                                        <select name="tipoReg" id="tipoReg" class="form-control input-sm" title="Seleccione Tipo Registro" style="width:200px; " required>
                                            <option value="<?php echo $row[17] ?>"><?php echo ucwords($row[18]) ?></option>

                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numReg" id="numReg" class="form-control input-sm" title="Número Registro" style="width:250px; " required readonly="true" disabled="true" value="<?php echo $row[15] ?>"/>
                                    </div>
                                    <div class="col-sm-3" align="left">  
                                        <label for="fechaReg" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaReg" id="fechaDis" class="form-control input-sm" title="Seleccione Fecha Registro" style="width:250px; " disabled="true" value="<?php echo $row[16] ?>" />
                                    </div>
                                    <div class="col-sm-1" align="left">  
                                        <a href="EXPEDIR_REGISTRO_PPTAL.php?reg=<?php echo md5($row[2]) ?>" target="_blank" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                    </div>
                                </div>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-1" align="left">  
                                        <label for="Cxp" class="control-label" ><strong style="color:#03C1FB;"></strong>Cuenta Por Pagar:</label><br>
                                    </div>
                                    <div class="col-sm-3" align="left" style="margin-left:20px">  
                                        <label for="tipoCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Cuenta Por Pagar:</label><br>
                                        <select name="tipoCxp" id="tipoCxp" class="form-control input-sm" title="Seleccione Tipo Cuenta Por Pagar" style="width:200px; " required>
                                            <option value="<?php echo $row[21] ?>"><?php echo ucwords($row[22]) ?></option>

                                        </select>
                                    </div>
                                    <div class="col-sm-4" align="left" style="margin-left:-20px">  
                                        <label for="numCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Número:</label><br>
                                        <input name="numCxp" id="numCxp" class="form-control input-sm" title="Número Cuenta Por Pagar" style="width:250px; " required readonly="true" disabled="true" value="<?php echo $row[19] ?>"/>
                                    </div>
                                    <div class="col-sm-3" align="left">  
                                        <label for="fechaCxp" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha:</label><br>
                                        <input name="fechaCxp" id="fechaCxp" class="form-control input-sm" title="Seleccione Fecha Cuenta Por Pagar" style="width:250px; " disabled="true" value="<?php echo $row[20] ?>" />
                                    </div>
                                    <div class="col-sm-1" align="left">  
                                        <a href="GENERAR_CUENTA_PAGAR.php?cxp=<?php echo md5($row[3]) ?>" target="_blank"  class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px"><i class="glyphicon glyphicon-eye-open"></i></a>
                                    </div>
                                </div>
                                <?php if($eg==0) { ?>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px;"> 
                                    <div class="col-sm-12" align="right">  
                                        <button type="submit" class="btn sombra btn-primary" style="margin-left:10px; margin-top: 20px" title="Generar Interfáz De Nuevo"> Generar Interfáz de Nuevo <i class="glyphicon glyphicon-repeat"></i></button>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="form-group form-inline" style="margin-top: -15px; margin-left: 5px;" align="right">
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>

                            <!--********************************************************************************************************************************************************************************************************************************************************************************************************************************************-->
                    <?php } ?>   
                    </div>
                </div>
            </div>
        </div> <!-- Cierra clase col-sm-10 text-left -->
    </div> <!-- Cierra clase row content -->

    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function ()
        {
            $(".select2_single").select2(
                    {
                        allowClear: true
                    });
        });
    </script>
    
    <script>
        $("#periodo").change(function () {
            var periodo = $("#periodo").val();
            if (periodo == "") {

            } else {
                var opcion = '<option value="" >Grupo Gestión</option>';
                var form_data = {action: 4, periodo: periodo}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gn_nomina_financieraJson.php",
                    data: form_data,
                    success: function (response)
                    {
                        opcion += response;
                        $("#grupog").html(opcion).focus();
                        $("#grupog").select2({
                            allowClear: true
                        });
                    }
                })
            }
        })
        $("#grupog").change(function () {
            if ($("#periodo").val() == "") {

            } else {
                var form_data = {action: 10, periodo: $("#periodo").val(), gg: $("#grupog").val()}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gn_nomina_financieraJson.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        if (response != 0) {
                            document.location = response;
                        }
                    }
                })
            }
        })
        $("#tipoDis").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#tipoDis").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                var form_data = {estruc: 2, id_tip_comp: +$("#tipoDis").val()};
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/consultas.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        var numero = response.trim();
                        $("#numdis").val(numero);
                        $("#fechaDis").val("");
                    }
                });
            }
        });
        $("#fechaDis").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#fechaDis").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                if ($("#tipoDis").val() == "") {
                    $("#fechaDis").val("").focus();
                    $("#mensaje").html("Seleccione Tipo Comprobante");
                    $("#modalMensaje").modal("show");
                } else {
                    var fecha = $("#fechaDis").val();
                    var form_data = {case: 4, fecha: fecha};
                    $.ajax({
                        type: "POST",
                        url: "jsonSistema/consultas.php",
                        data: form_data,
                        success: function (response)
                        {
                            if (response == 1) {
                                $("#mensaje").html("Periodo ya ha sido cerrado, escoja nuevamente la fecha");
                                $("#modalMensaje").modal("show");
                                $("#fechaDis").val("").focus();

                            } else {
                                fechaDis();
                            }
                        }
                    });
                }
            }

        });
        function fechaDis()
        {
            var tipComPal = $("#tipoDis").val();
            var fecha = $("#fechaDis").val();
            var num = $("#numdis").val();
            var form_data = {estruc: 7, tipComPal: tipComPal, fecha: fecha, num: num};
            $.ajax({
                type: "POST",
                url: "jsonPptal/validarFechas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                    if (response == 1)
                    {
                        $("#fechaDis").val("");
                        $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                        $("#modalMensaje").modal("show");

                    } else
                    {
                        var fechaReg = $("#fechaReg").val();
                        if (fechaReg == "") {

                        } else {
                            var fechaR = fechaReg.split('/').reverse().join('/');
                            var fechaD = fecha.split('/').reverse().join('/');
                            if (fechaR < fechaD) {
                                $("#fechaReg").val("");
                                $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                                $("#modalMensaje").modal("show");
                            } else {

                            }
                        }
                    }
                }
            });
        }
        $("#tipoReg").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#tipoReg").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                var form_data = {estruc: 2, id_tip_comp: +$("#tipoReg").val()};
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/consultas.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        var numero = response.trim();
                        $("#numReg").val(numero);
                        $("#fechaReg").val("");
                    }
                });
            }
        });
        $("#fechaReg").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#fechaReg").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                if ($("#tipoReg").val() == "") {
                    $("#fechaReg").val("");
                    $("#mensaje").html("Seleccione Tipo Comprobante");
                    $("#modalMensaje").modal("show");
                } else {
                    var fecha = $("#fechaReg").val();
                    var form_data = {case: 4, fecha: fecha};
                    $.ajax({
                        type: "POST",
                        url: "jsonSistema/consultas.php",
                        data: form_data,
                        success: function (response)
                        {
                            if (response == 1) {
                                $("#mensaje").html("Periodo ya ha sido cerrado, escoja nuevamente la fecha");
                                $("#modalMensaje").modal("show");
                                $("#fechaReg").val("").focus();

                            } else {
                                fechaReg("");
                            }
                        }
                    });
                }
            }

        });
        function fechaReg()
        {
            var tipComPal = $("#tipoReg").val();
            var fecha = $("#fechaReg").val();
            var num = $("#numReg").val();
            var form_data = {estruc: 10, tipComPal: tipComPal, fecha: fecha, num: num};
            $.ajax({
                type: "POST",
                url: "jsonPptal/validarFechas.php",
                data: form_data,
                success: function (response)
                {

                    if (response == 1)
                    {
                        $("#fechaReg").val("");
                        $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                        $("#modalMensaje").modal("show");

                    } else
                    {
                        var fechaDis = $("#fechaDis").val();
                        if (fechaDis == "") {
                            $("#fechaReg").val("");
                            $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                            $("#modalMensaje").modal("show");
                        } else {
                            var fechaD = fechaDis.split('/').reverse().join('/');
                            var fechaR = fecha.split('/').reverse().join('/');
                            if (fechaR < fechaD) {
                                $("#fechaReg").val("");
                                $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                                $("#modalMensaje").modal("show");
                            } else {
                                var fechacxp = $("#fechaCxp").val();
                                if (fechacxp == "") {
                                } else {
                                    var fechaC = fechacxp.split('/').reverse().join('/');
                                    if (fechaR > fechaC) {
                                        $("#fechaReg").val("");
                                        $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                                        $("#modalMensaje").modal("show");
                                    } else {
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }
        $("#tipoCxp").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#tipoCxp").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                var form_data = {estruc: 2, id_tip_comp: +$("#tipoCxp").val()};
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/consultas.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        var numero = response.trim();
                        $("#numCxp").val(numero);
                        $("#fechaCxp").val("");
                    }//Fin succes.
                });
            }
        });
        $("#fechaCxp").change(function ()
        {
            if ($("#periodo").val() == "" || $("#grupog").val == "") {
                $("#fechaCxp").val("");
                $("#mensaje").html("Seleccione periodo y grupo de gestión");
                $("#modalMensaje").modal("show");
            } else {
                if ($("#tipoCxp").val() == "") {
                    $("#fechaCxp").val("");
                    $("#mensaje").html("Seleccione Tipo Comprobante");
                    $("#modalMensaje").modal("show");
                } else {
                    var fecha = $("#fechaCxp").val();
                    var form_data = {case: 4, fecha: fecha};
                    $.ajax({
                        type: "POST",
                        url: "jsonSistema/consultas.php",
                        data: form_data,
                        success: function (response)
                        {
                            if (response == 1) {
                                $("#mensaje").html("Periodo ya ha sido cerrado, escoja nuevamente la fecha");
                                $("#modalMensaje").modal("show");
                                $("#fechaCxp").val("").focus();

                            } else {
                                fechaCxp();
                            }
                        }
                    });
                }
            }

        });
        function fechaCxp()
        {
            var tipComPal = $("#tipoCxp").val();
            var fecha = $("#fechaCxp").val();
            var num = $("#numCxp").val();
            var form_data = {estruc: 14, tipComPal: tipComPal, fecha: fecha, num: num};
            $.ajax({
                type: "POST",
                url: "jsonPptal/validarFechas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                    if (response == 1)
                    {
                        $("#fechaCxp").val("");
                        $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                        $("#modalMensaje").modal("show");

                    } else
                    {
                        var fechaReg = $("#fechaReg").val();
                        if (fechaReg == "") {
                            $("#fechaCxp").val("");
                            $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                            $("#modalMensaje").modal("show");
                        } else {
                            var fechaR = fechaReg.split('/').reverse().join('/');
                            var fechaC = fecha.split('/').reverse().join('/');
                            if (fechaC < fechaR) {
                                $("#fechaCxp").val("");
                                $("#mensaje").html("Fecha Inválida. Verifique nuevamente");
                                $("#modalMensaje").modal("show");
                            } else {

                            }
                        }

                    }
                }
            });
        }
    </script>
    <script>
    function guardar() {
        var periodo = $("#periodo").val();
        var gg      = $("#grupog").val();
        var ter     = $("#tercero").val();
        var tipodis = $("#tipoDis").val();
        var numdis  = $("#numdis").val();
        var fechadis= $("#fechaDis").val();
        var tiporeg = $("#tipoReg").val();
        var numreg  = $("#numReg").val();
        var fechareg= $("#fechaReg").val();
        var tipocxp = $("#tipoCxp").val();
        var numcxp  = $("#numCxp").val();
        var fechacxp= $("#fechaCxp").val();

        if (periodo == "" || gg == "" || tipodis == "" || numdis == "" || fechadis == "" || tiporeg == "" || numreg == "" || fechareg == "" || tipocxp == "" || numcxp == "" || fechacxp == "") {
            $("#mensaje").html("Datos incompletos. Verifique nuevamente");
            $("#modalMensaje").modal("show");
        } else {
            jsShowWindowLoad('Validando Datos...');
            //****Validar que los conceptos esten homologados****//
            var form_data = {action: 5, gg: gg, periodo: periodo};
            $.ajax({
                type: "POST",
                url: "jsonPptal/gn_nomina_financieraJson.php",
                data: form_data,
                success: function (response)
                {
                    jsRemoveWindowLoad();
                    console.log('Homologacion' + response);
                    var resultado = JSON.parse(response);
                    if (resultado != "") {
                        var texto = "";
                        for (i = 0; i < resultado.length; i++) {
                            texto += resultado[i];
                            texto += '<br/>';
                        }
                        document.getElementById("conceptos").innerHTML = texto;
                        $('#modalConceptos').modal('show');
                    } else {
                        jsShowWindowLoad('Validando Saldo...');
                        //****Validar saldo de los rubros****//
                        var form_data = {action: 9, gg: gg, periodo: periodo, fecha: fechadis};
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/gn_nomina_financieraJson.php",
                            data: form_data,
                            success: function (response)
                            {
                                jsRemoveWindowLoad();
                                console.log('Saldo' + response);
                                var resultado = JSON.parse(response);
                                var rta = resultado["rta"];
                                var msj = resultado["msj"];
                                if (rta > 0) {
                                    document.getElementById("rubros").innerHTML = msj;
                                    $('#modalRubros').modal('show');
                                } else {
                                    jsShowWindowLoad('Guardando Comprobantes..');
                                    /*******Guardar**********/
                                    var form_data ={action:12, periodo:periodo,gg:gg,ter:ter,
                                    tipodis:tipodis,numdis:numdis,fechadis:fechadis,
                                    tiporeg:tiporeg,numreg:numreg,fechareg:fechareg,
                                    tipocxp:tipocxp,numcxp:numcxp,fechacxp:fechacxp};
                                    $.ajax({
                                           type: "POST",
                                           url: "jsonPptal/gn_nomina_financieraJson.php",
                                           data: form_data,
                                           success: function(response)
                                            {
                                                console.log(response);
                                                jsRemoveWindowLoad();
                                                var resultado = JSON.parse(response);
                                                var rta     = resultado["rta"];
                                                var iddis   = resultado["iddis"];
                                                var idreg   = resultado["idreg"];
                                                var idcxp   = resultado["idcxp"];
                                                var idcxpcnt= resultado["idcxpcnt"];
                                                
                                                if(rta==0){
                                                    asignarids(iddis,idreg,idcxp,idcxpcnt);
                                                    guardarDetalles(periodo,gg,ter,fechadis,iddis,idreg,idcxp,idcxpcnt);
                                                } else {
                                                    $("#mensaje").html("No se han podido guardar los comprobantes");
                                                    $("#modalMensaje").modal("show");
                                                }
                                            }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
    }
    </script>
    <script>
    function guardarDetalles(periodo,gg,ter,fechadis,iddis,idreg,idcxp,idcxpcnt){
        jsShowWindowLoad('Validando Datos...');
        //****Validar que los conceptos esten homologados****//
        var form_data = {action: 5, gg: gg, periodo: periodo};
        $.ajax({
            type: "POST",
            url: "jsonPptal/gn_nomina_financieraJson.php",
            data: form_data,
            success: function (response)
            {
                jsRemoveWindowLoad();
                console.log('Homologacion' + response);
                var resultado = JSON.parse(response);
                if (resultado != "") {
                    var texto = "";
                    for (i = 0; i < resultado.length; i++) {
                        texto += resultado[i];
                        texto += '<br/>';
                    }
                    document.getElementById("conceptos").innerHTML = texto;
                    $('#modalConceptos').modal('show');
                } else {
                    jsShowWindowLoad('Validando Saldo...');
                    //****Validar saldo de los rubros****//
                    var form_data = {action: 9, gg: gg, periodo: periodo, fecha: fechadis};
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gn_nomina_financieraJson.php",
                        data: form_data,
                        success: function (response)
                        {
                            jsRemoveWindowLoad();
                            console.log('Saldo' + response);
                            var resultado = JSON.parse(response);
                            var rta = resultado["rta"];
                            var msj = resultado["msj"];
                            if (rta > 0) {
                                document.getElementById("rubros").innerHTML = msj;
                                $('#modalRubros').modal('show');
                            } else {
                                jsShowWindowLoad('Guardando Comprobantes..');
                                var form_data ={action:13, periodo:periodo,gg:gg,ter:ter,
                                    fechadis:fechadis,iddis:iddis,idreg:idreg,idcxp:idcxp,
                                    idcxpcnt:idcxpcnt, fechaCxp:$("#fechaCxp").val()};
                                $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/gn_nomina_financieraJson.php",
                                    data: form_data,
                                    success: function(response)
                                    {

                                        jsRemoveWindowLoad();
                                        console.log(response);
                                         var resultado = JSON.parse(response);
                                         var rta     = resultado["rta"];
                                         var msj     = resultado["html"];
                                         if(rta==1){
                                            $("#guardo").modal("show");
                                            $("#btnGuardo").click(function(){ 
                                                document.location = msj;
                                            }) 
                                         } else {
                                            $("#mensaje").html("No se han podido guardar los detalles");
                                            $("#modalMensaje").modal("show");
                                        }
                                    }
                                });
                            }
                        }
                    })
                }
            }
        })
    }
    </script>
    <script>
        function interfaz(){
            //Validar Si alguna de las fechas tiene cierre 
            //Disponibilidad
            var fecha = $("#fechaDis").val();
            console.log(fecha+'cierre');
            var form_data = {case: 4, fecha: fecha};
            $.ajax({
               type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response) {
                    console.log(response+'cierre');
                    if (response == 1) {
                        $("#mensaje").html("Periodo está cerrado.Verifique Nuevamente");
                        $("#modalMensaje").modal("show");
                    } else {
                        //Registro
                        var fecha = $("#fechaReg").val();
                        var form_data = {case: 4, fecha: fecha};
                        $.ajax({
                            type: "POST",
                            url: "jsonSistema/consultas.php",
                            data: form_data,
                            success: function (response)
                            {
                                console.log(response+'cierre');
                                if (response == 1) {
                                    $("#mensaje").html("Periodo está cerrado.Verifique Nuevamente");
                                    $("#modalMensaje").modal("show");
                                } else {
                                    //Cuenta Por Pagar
                                    var fecha = $("#fechaCxp").val();
                                    var form_data = {case: 4, fecha: fecha};
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonSistema/consultas.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            console.log(response+'cierre');
                                            if (response == 1) {
                                                $("#mensaje").html("Periodo está cerrado.Verifique Nuevamente");
                                                $("#modalMensaje").modal("show");
                                            } else {
                                                $("#modaleliminartodo").modal("show"); 
                                                $("#eliminartodo").click(function(){
                                                    var id = $("#id_dis").val();
                                                    jsShowWindowLoad('Comprobando...');                
                                                    var form_data ={estruc:13, id:id };
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "jsonPptal/consultas.php",
                                                        data: form_data,
                                                        success: function(response)
                                                        {
                                                            jsRemoveWindowLoad();
                                                            if(response==1){
                                                                $("#mensaje").html("Periodo está cerrado.Verifique Nuevamente");
                                                                $("#modalMensaje").modal("show");
                                                            }else {
                                                                if(response==2){
                                                                    $("#mensaje").html("Los detalles de algún comprobante estan conciliados, Verifique Nuevamente");
                                                                    $("#modalMensaje").modal("show");
                                                                } else {
                                                                    if(response==0){
                                                                        jsShowWindowLoad('Eliminando..');
                                                                        var form_data ={estruc:14, id:id };
                                                                        $.ajax({
                                                                            type: "POST",
                                                                            url: "jsonPptal/consultas.php",
                                                                            data: form_data,
                                                                            success: function(response)
                                                                            {
                                                                                console.log(response);
                                                                                jsRemoveWindowLoad();
                                                                                if(response==1){
                                                                                    var periodo = $("#periodo").val();
                                                                                    var gg      = $("#grupog").val();
                                                                                    var ter     = $("#tercero").val();
                                                                                    var fechadis= $("#fechaDis").val();
                                                                                    var iddis   = $("#id_dis").val();
                                                                                    var idreg   = $("#id_reg").val();
                                                                                    var idcxp   = $("#id_cxp").val();
                                                                                    var idcxpcnt= '';
                                                                                    guardarDetalles(periodo,gg,ter,fechadis,iddis,idreg,idcxp,idcxpcnt);
                                                                                } else {
                                                                                    $("#mensajemodal").html('No Se Ha Podido Eliminar La Información');
                                                                                    $("#modalmensajes").modal("show");
                                                                                }
                                                                            }
                                                                        });
                                                                    } else {
                                                                        $("#mensajemodal").html('Error de validación');
                                                                        $("#modalmensajes").modal("show");
                                                                    }
                                                                }
                                                            }
                                                        }//Fin succes.
                                                    }); 

                                                $("#canEliTodo").click(function(){
                                                   $("#modaleliminartodo").modal("hide"); 
                                                }) 
                                            })
                                            }
                                        }
                                    })
                                }
                            }
                        })
                    }
                } 
            });
        }
    </script>
<script>
    function asignarids (iddis,idreg,idcxp,idcxpcnt){
        $("#id_dis").val(iddis);
        $("#id_reg").val(idreg);
        $("#id_cxp").val(idcxp);
        $("#id_cxpc").val(idcxpcnt);
    }
</script>   
<div class="modal fade" id="guardo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Interfáz Generada Correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnGuardo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalMensaje" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modaleliminartodo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label  style="font-weight: normal" id="mensajemodaleliminar" name="mensajemodaleliminar">¿Desea generar interfáz de nuevo?</label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="eliminartodo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
                <button type="button" id="canEliTodo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalConceptos" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">

                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p><strong><i>CONCEPTOS POR HOMOLOGAR:</i></strong></p><br/>
                <p id="conceptos" align="left" ></p> 

            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnGuardado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRubros" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">

                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p><strong><i>RUBROS SIN SALDO:</i></strong></p><br/>
                <p id="rubros" align="left" ></p> 

            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnRubros" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
</body>
</html>

