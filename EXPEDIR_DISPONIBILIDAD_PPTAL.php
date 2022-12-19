<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('./jsonSistema/funcionCierre.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$num_anno   = anno($_SESSION['anno']);
$id = 0;
$numero = "";
$fecha = "";
$fechaVen = "";
$descripcion = "";
$numDet = 0;

if (!empty($_GET['dis'])) {
    $iddis = $_GET['dis'];
    $dis = "SELECT id_unico FROM gf_comprobante_pptal WHERE md5(id_unico) ='$iddis'";
    $dis = $mysqli->query($dis);
    $dis = mysqli_fetch_row($dis);
    $dis = $dis[0];
    $_SESSION['id_comp_pptal_ED'] = $dis;
    $_SESSION['nuevo_ED'] = 1;
}
if (!empty($_SESSION['id_comp_pptal_ED'])) {
    $queryGen = "SELECT
                detComP.id_unico,
                con.nombre,
                CONCAT_WS(' - ',rub.codi_presupuesto, rub.nombre),
                detComP.valor,
                rubFue.id_unico,
                fue.nombre, 
                cc.nombre , 
                t.id_unico, 
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
              FROM
                gf_detalle_comprobante_pptal detComP
              LEFT JOIN
                gf_rubro_fuente rubFue ON detComP.rubrofuente = rubFue.id_unico
              LEFT JOIN
                gf_rubro_pptal rub ON rubFue.rubro = rub.id_unico
              LEFT JOIN
                gf_concepto_rubro conRub ON conRub.id_unico = detComP.conceptorubro
              LEFT JOIN
                gf_concepto con ON con.id_unico = conRub.concepto
              LEFT JOIN
                gf_fuente fue ON fue.id_unico = rubFue.fuente 
              LEFT JOIN 
                gf_centro_costo cc ON detComP.centro_costo= cc.id_unico 
              LEFT JOIN 
                gf_tercero t ON detComP.tercero= t.id_unico 
              WHERE
                detComP.comprobantepptal = " . $_SESSION['id_comp_pptal_ED'];
    $resultado = $mysqli->query($queryGen);
    $numDet = $resultado->num_rows;
    $queryCompro = "SELECT
                    comp.id_unico,
                    comp.numero,
                    comp.fecha,
                    comp.descripcion,
                    comp.fechavencimiento,
                    comp.tipocomprobante,
                    tipCom.codigo,
                    tipCom.nombre, 
                    t.id_unico, 
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
								  
									 
									  
									  
										
										 
												 
									  
									  
										
											  
										  
									  
									  
									  
										
													
																				 
												  
																				   
                  FROM
                    gf_comprobante_pptal comp 
                  LEFT JOIN 
                    gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico 
                  LEFT JOIN 
                    gf_tercero t ON comp.tercero = t.id_unico 
							
																	  
                  WHERE
                     comp.id_unico =" . $_SESSION['id_comp_pptal_ED'];
    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);
    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $fecha_s = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
    $fecha = $dia . "/" . $mes . "/" . $anio;
    $fecha_div = explode("-", $fechaVen);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
    $fechaVen = $dia . "/" . $mes . "/" . $anio;
    #Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT
                    id_unico,
                    numero
                  FROM
                    gf_comprobante_pptal
                  WHERE
                    tipocomprobante = 6 AND estado = 1 
                    AND id_unico !='" . $_SESSION['id_comp_pptal_ED'] . "' 
                  ORDER BY numero";
    $numeroSoli = $mysqli->query($queryNumSol);
}
#Consulta para listado de Número Solicitud. // WHERE tipocomprobante = 6 
if (!empty($_SESSION['id_comp_pptal_ED']) && !empty($_SESSION['nuevo_ED'])) {
    $querySolAprob = "SELECT
                    cn.id_unico,
                    cn.numero,
                    cn.fecha,
                    cn.descripcion
                  FROM
                    gf_comprobante_pptal cn 
                  LEFT JOIN 
                    gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
                  WHERE
                    tc.clasepptal = 12 AND tc.tipooperacion = 1 AND fecha <='$fecha_s' 
                    AND cn.parametrizacionanno = $anno 
                  ORDER BY
                    cn.numero";
} else {
    $querySolAprob = "SELECT
                    cn.id_unico,
                    cn.numero,
                    cn.fecha,
                    cn.descripcion
                  FROM
                    gf_comprobante_pptal cn 
                  LEFT JOIN 
                    gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
                  WHERE
                    tc.clasepptal = 12 AND tc.tipooperacion = 1 
                    AND cn.parametrizacionanno = $anno 
                  ORDER BY
                    cn.numero";
}
$SolAprob = $mysqli->query($querySolAprob);
#Consulta para listado de Tipo Comprobante Pptal.
/**/ // Clase 14, Tipo de operación 1.
$queryTipComPtal = "SELECT
                    id_unico,
                    codigo,
                    nombre
                  FROM
                    gf_tipo_comprobante_pptal
                  WHERE
                    clasepptal = 14 AND tipooperacion = 1 and vigencia_actual = 1 AND compania = $compania 
                  ORDER BY
                    codigo";
$tipoComPtal = $mysqli->query($queryTipComPtal);
#Consulta para el listado de concepto de la tabla gf_concepto.
$queryCon = "SELECT
            id_unico,
            nombre
          FROM
            gf_concepto 
          WHERE parametrizacionanno = $anno";
$concepto = $mysqli->query($queryCon);

#Consulta para el listado de concepto de la tabla gf_rubro_pptal.
$queryRub = "SELECT
            id_unico,
            CONCAT(codi_presupuesto,
            ' ',
            nombre) rubro
          FROM
            gf_rubro_pptal
          WHERE
            movimiento = 1";
$rubro = $mysqli->query($queryRub);

$queryFue = "SELECT
            id_unico,
            nombre
          FROM
            gf_fuente";
$fuente = $mysqli->query($queryFue);

#Consulta para el listado de concepto de la tabla gf_concepto.
$queryCon = "SELECT
                id_unico,
                nombre
              FROM
                gf_concepto
              WHERE
                clase_concepto = 2 AND parametrizacionanno = $anno";
$concepto = $mysqli->query($queryCon);

$arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');

#****************Consulta Tipo de Compañia*********####
$com = $_SESSION['compania'];
$tcom = "SELECT tipo_compania FROM gf_tercero WHERE id_unico = $com";
$tcom = $mysqli->query($tcom);
if (mysqli_num_rows($tcom) > 0) {
    $tcom = mysqli_fetch_row($tcom);
    $tipocomp = $tcom[0];
} else {
    $tipocomp = 0;
}
?>
<html>
    <head>
        <title>Expedir Disponibilidad Presupuestal</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <style type="text/css">
            .area
            { 
                height: auto !important;  
            } 

            .acotado
            {
                white-space: normal;
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
                font-size: 10px;
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
                width:250px;
                height:120px;
                overflow: auto;
                background-color: white;
            }

            body{
                font-size: 10px;
            }
        </style>
        <?php if (empty($_SESSION['id_comp_pptal_ED']) && empty($_SESSION['nuevo_ED'])) {
            ?>
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
                        yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
                        maxDate: '31/12/<?php echo $num_anno?>',
                        minDate: '01/01/<?php echo $num_anno?>'
                    };
                    $.datepicker.setDefaults($.datepicker.regional['es']);
                    $("#fecha").datepicker({changeMonth: true}).val();
                    $("#fechaAct").val();
                });

            </script>
        <?php } ?>

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
                    maxDate: '31/12/<?php echo $num_anno?>',
                    minDate: '01/01/<?php echo $num_anno?>'
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fecha").datepicker({changeMonth: true}).val();
                $("#fechaVen").datepicker({changeMonth: true}).val();
            });
        </script>
    </head>
    <body onresize="cambiar()">
        <input type="hidden" id="id_com_pptal" value="<?php echo $id; ?>">
        <input type="hidden" id="fechaCompP" value="<?php echo $fecha; ?>">
        <input type="hidden" id="fechaVenCompP" value="<?php echo $fechaVen; ?>">
        <input type="hidden" id="fechaAct">
        <input type="hidden" id="numDet" value="<?php echo $numDet; ?>">
        <div class="container-fluid text-center"  >
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >
                    <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Expedir Disponibilidad Presupuestal</h2>
                    <div class="col-sm-12">
                        <div class="client-form contenedorForma"  style=""> 
                            <form name="form" class="form-horizontal" method="POST" onsubmit="return valida();"  enctype="multipart/form-data" action="json/registrar_EXP_DIS_COMPROBANTE_PPTALJson.php">
                                <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera fila -->
                                    <input type="hidden" name="tipocomp" id="tipocomp" value="<?php echo $tipocomp; ?>">
                                    <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                        <?php if($tipocomp==2){ ?> 
                                        <input type="hidden" name="solicitudAprobada" id="solicitudAprobada">
                                        <label for="tercero" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Tercero:</label><br/>
                                        <select name="tercero" id="tercero" class="select2_single form-control " title="Tercero" style="width:180px;">
                                            <?php if (!empty($_SESSION['nuevo_ED'])) {
                                                echo '<option value="'.$rowComp[8].'">'. ucwords(mb_strtolower($rowComp[9])).' - '.$rowComp[10].'</option>';
                                                $idd =$rowComp[8];
                                            } else { 
                                                echo '<option value="2">Varios</option>';
                                                $idd =2;
                                            }
                                             $ter = $con->Listar("SELECT t.id_unico, 
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
                                               WHERE t.id_unico !=$idd  
                                               ORDER BY t.id_unico");
                                            for ($i = 0; $i < count($ter); $i++) {
                                                echo '<option value="'.$ter[$i][0].'">'. ucwords(mb_strtolower($ter[$i][1])).' - '.$ter[$i][2].'</option>';
                                            }
                                            ?>
                                        </select>
                                        <?php } else {  ?>
                                        <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Solicitud Aprobada:</label><br/>
                                        <select name="solicitudAprobada" id="solicitudAprobada" class="select2_single form-control " title="Número de solicitud aprobada" style="width:180px;">
                                            <?php $solSeleccionada = ''; ?>
                                            <option value="" <?php if (empty($_SESSION['id_comp_pptal_ED'])) {
                                                echo 'selected="selected"';
                                            } ?> >Solicitud Aprobada</option>
                                            <?php
                                            while ($rowSolAprob = mysqli_fetch_row($SolAprob)) {
                                                if ($id == $rowSolAprob[0])
                                                    $solSeleccionada = 'selected="selected"';
                                                else
                                                    $solSeleccionada = '';
                                                $queryDetCompro = "SELECT
                                                                        detComp.id_unico,
                                                                        detComp.valor
                                                                      FROM
                                                                        gf_detalle_comprobante_pptal detComp,
                                                                        gf_comprobante_pptal comP
                                                                      WHERE
                                                                        comP.id_unico = detComp.comprobantepptal 
                                                                        AND comP.id_unico =" . $rowSolAprob[0];
                                                $saldDispo = 0;
                                                $totalSaldDispo = 0;
                                                $detCompro = $mysqli->query($queryDetCompro);
                                                while ($rowDetComp = mysqli_fetch_row($detCompro)) {
                                                    $queryDetAfetc = "SELECT
                                                                        valor
                                                                      FROM
                                                                        gf_detalle_comprobante_pptal
                                                                      WHERE
                                                                        comprobanteafectado = " . $rowDetComp[0];
                                                    $detAfect = $mysqli->query($queryDetAfetc);
                                                    $totalAfec = 0;
                                                    while ($rowDetAf = mysqli_fetch_row($detAfect)) {
                                                        $totalAfec += $rowDetAf[0];
                                                    }
                                                    $saldDispo = $rowDetComp[1] - $totalAfec;
                                                    $totalSaldDispo += $saldDispo;
                                                }
                                                if ($totalSaldDispo > 0) {
                                                    $fecha_div = explode("-", $rowSolAprob[2]);
                                                    $anio = $fecha_div[0];
                                                    $mes = $fecha_div[1];
                                                    $dia = $fecha_div[2];
                                                    $fechaCom = $dia . "/" . $mes . "/" . $anio;
                                                    ?>
                                                    <option value="<?php echo $rowSolAprob[0]; ?>" <?php echo $solSeleccionada; ?> >
                                                        <?php echo $rowSolAprob[1] . ' ' . $fechaCom . ' ' . ucwords(mb_strtolower($rowSolAprob[3])) . ' $' . number_format($totalSaldDispo, 2, '.', ',') ?>
                                                    </option> 
                                                <?php }
                                            }
                                            ?>
                                        </select>
                                        <?php } ?>
                                    </div>
                                    <!-- Tipo de comprobante -->
                                    <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                        <label for="tipoComPtal" class="control-label " ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Pptal:</label><br/>
                                        <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm " title="Seleccione un tipo de comprobante" style="width:180px;" required>
                                            <?php
                                            if (!empty($_SESSION['nuevo_ED'])) {
                                                echo '<option value="' . $rowComp[5] . '" selected="selected" >' . $rowComp[6] . ' ' . ucwords(mb_strtolower($rowComp[7])) . '</option> ';
                                            } else {
                                                ?>
                                                <option value="" selected="selected" >Tipo Comprobante Presupuestal</option>                        
                                                <?php while ($rowTipComPtal = mysqli_fetch_row($tipoComPtal)) { ?>
                                                    <option value="<?php echo $rowTipComPtal[0]; ?>"><?php echo $rowTipComPtal[1] . ' ' . ucwords(mb_strtolower($rowTipComPtal[2])); ?></option> 
    <?php }
}
?>
                                        </select>
                                    </div>
                                    <!-- Número de disponibilidad -->
                                    <div class="col-sm-2" align="left" style="padding-left: 0px; margin-right: 10px;">
                                        <div style="width: 150px;">
                                            <label for="noDisponibilidad" class="control-label" style="">
                                                <strong style="color:#03C1FB;">*</strong>Número Disponibilidad:
                                            </label>
                                        </div>
                                        <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width: 150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly" value="<?php if (!empty($_SESSION['nuevo_ED'])) {
    echo $numero;
} ?>" required>
                                    </div>
                                    <div class="col-sm-3" style="margin-top: 0px;" > <!-- Buscar disponibilidad -->
                                        <label for="noDisponibilidad" class="control-label" style="margin-left:-60px"><right>Buscar Disponibilidad:</right></label>
                                        <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:250px">
                                            <option value="">Registro</option>
                                            <?php
                                            $reg = "SELECT
                                                    cp.id_unico,
                                                    cp.numero,
                                                    cp.fecha,
                                                    tcp.codigo
                                                    
                                                  FROM
                                                    gf_comprobante_pptal cp
                                                  LEFT JOIN
                                                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                                  LEFT JOIN
                                                    gf_tercero tr ON cp.tercero = tr.id_unico 
                                                  WHERE cp.parametrizacionanno = $anno AND tcp.clasepptal = 14 AND tcp.tipooperacion=1  AND tcp.vigencia_actual = 1 ORDER BY cp.numero DESC";
                                            $reg = $mysqli->query($reg);
                                            while ($row1 = mysqli_fetch_row($reg)) {
                                                $date = new DateTime($row1[2]);
                                                $f = $date->format('d/m/Y');
                                                $sqlValor = 'SELECT SUM(valor) 
                                                        FROM gf_detalle_comprobante_pptal 
                                                        WHERE comprobantepptal = ' . $row1[0];
                                                $valor = $mysqli->query($sqlValor);
                                                $rowV = mysqli_fetch_row($valor);
                                                $v = ' $' . number_format($rowV[0], 2, '.', ',');
                                                ?>
                                                <option value="<?php echo $row1[0] ?>"><?php echo $row1[1] . ' ' . mb_strtoupper($row1[3]) . ' ' . $f . ' ' . $v ?>
<?php } ?>
                                        </select>
                                    </div> 

                                </div> 
                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#tipoComPtal").change(function ()
                                        {
                                            if (($("#tipoComPtal").val() == "") || ($("#tipoComPtal").val() == 0))
                                            {
                                                $("#noDisponibilidad").val("");
                                                $("#descripcion").attr('readonly', 'readonly');
                                            } else {
                                                var form_data = {estruc: 3, id_tip_comp: +$("#tipoComPtal").val()};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_expedir_disponibilidad.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        
                                                        var numero = response.trim();
                                                        $("#noDisponibilidad").val(numero);
                                                        $("#descripcion").removeAttr('readonly');
                                                        $("#fecha").val("");
                                                        $("#fechaVen").val();

                                                    }//Fin succes.
                                                });
                                            }
                                        });//Cierre change.
                                    });
                                </script>
                                <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 4px;"> <!-- Segunda fila -->
                                    <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                        <label for="nombre" class=" control-label" style="margin-top: 0px;" >Descripción:</label>
                                        <textarea class="" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; width:250px; height: 50px; width:180px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="1000" placeholder="Descripción"   ><?php echo $descripcion; ?> </textarea> 
                                    </div>
                                    <div class="col-sm-2" align="left" style="padding-left: 0px;">
                                        <label for="fecha" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                        <?php if (empty($_SESSION['id_comp_pptal_ED'])) { ?>
                                        <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha; ?>" readonly="true" >
                                        <?php
                                        } else {
                                            #AFECTACION
                                            $af = "SELECT DISTINCT 
                                                COUNT(dcp.comprobantepptal)
                                              FROM
                                                gf_detalle_comprobante_pptal dcp
                                              LEFT JOIN
                                                gf_detalle_comprobante_pptal dcpa ON dcp.comprobanteafectado = dcpa.id_unico
                                              WHERE
                                                dcpa.comprobantepptal =" . $_SESSION['id_comp_pptal_ED'];
                                            $af = $mysqli->query($af);
                                            $af = mysqli_fetch_row($af);
                                            if ($af[0] > 0) {
                                                ?>
                                                <script>
                                                    $(document).ready(function ()
                                                    {
                                                        //$("#btnModificarComp").prop("disabled", true);
                                                        $("#btnEliminar").prop("disabled", true);
                                                        $("#agregarSolicitud").prop("disabled", true);
                                                    });
                                                </script>
                                                <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha; ?>">
    <?php } else { ?>
                                                <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha; ?>"  readonly="true">
    <?php }
} ?>
                                    </div>
                                    <div class="col-sm-2" align="left" style="padding-left: 0px;">
                                        <label for="fechaVen" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label>
<?php if (!empty($_SESSION['id_comp_pptal_ED'])) {
    if ($af[0] > 0) {
        ?>
                                                <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen; ?>"  readonly="true" disabled="true">
    <?php } else { ?>
                                                <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen; ?>"  readonly="true" >
                                                <script>
                                                    var fechaAs = $("#fecha").val();
                                                    $("#fechaVen").datepicker("destroy");
                                                    $("#fechaVen").datepicker({changeMonth: true, minDate: 
                                                            }).val();
                                                </script>
    <?php }
} else { ?>
                                            <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen; ?>"  readonly="true" >
                                            <script>
                                                var fechaAs = $("#fecha").val();
                                                $("#fechaVen").datepicker("destroy");
                                                $("#fechaVen").datepicker({changeMonth: true, minDate: fechaAs}).val();
                                            </script>
<?php } ?>
                                    </div>
                                    <div class="col-sm-1" align="left" style="padding-left: 0px;">
                                        <label for="mostrarEstado" class="control-label" >Estado:</label>
                                        <input class="input-sm " type="text" name="mostrarEstado" id="mostrarEstado" class="form-control" style="width:70px; margin-top: 0px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 
                                        <input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->
                                    </div>

                                    <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px">
                                        <a id="btnNuevoComp" class="btn btn-primary sombra" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a>
                                    </div>
                                    <div class="col-sm-1" style="margin-top: 15px; margin-left: -25px">
                                        <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Guardar" >
                                            <li class="glyphicon glyphicon-floppy-disk"></li>
                                        </button> <!--Guardar-->
                                    </div>
                                    <div class="col-sm-1" style="margin-top: 15px; margin-left: -25px">
                                        <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();">
                                            <img src="images/hb2.png" style="width: 14px; height: 14.28px;">
                                        </button> <!--Firma Dactilar-->
                                    </div>
                                    <div class="col-sm-1" style="margin-top: 15px; margin-left: -25px">
                                        <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                            <li class="glyphicon glyphicon glyphicon-print"></li>
                                        </button> <!--Imprimir-->
                                    </div>
<?php if (!empty($_SESSION['id_comp_pptal_ED'])) { ?>
                                        <script type="text/javascript">
                                            $(document).ready(function ()
                                            {
                                                $("#btnImprimir").click(function () {
                                                    window.open('informesPptal/inf_Exp_Dis_Pptal.php');
                                                });
                                            });
                                        </script>
<?php } ?>
                                </div> <!-- Fin segunda fila -->

                                <div class="form-group form-inline col-sm-12" style="margin-top: -35px;"> <!-- Tercera fila -->
																	
									
                                    <div class="col-sm-8"></div>
																																								 
																																													
																					  
																																							  
																   
													 
																						 
														
											 
																							  
																 
																
																
																  
																   
																			 
																
																
																  
																		
																	
																  
																
																
																  
																			  
																										 
																			
																											
																 
																										 
																  
																	 
																				  
																																						   
											 
											  
												 
										  
																
													  
																	
											  
                                    <div class="col-sm-1" style="margin-top: -5px; margin-left: -5px;"> 
                                        <button type="button" id="siguiente" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Siguiente" >
                                            <li class="glyphicon glyphicon-arrow-right"></li>
                                        </button> <!--  Siguiente -->
                                    </div>
                                    <div class="col-sm-1" style="margin-top: -5px; margin-left: -25px;"> 
                                        <button type="button" id="btnModificarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Modificar Comprobante Presupuestal" >
                                            <li class="glyphicon glyphicon-pencil"></li>
                                        </button> <!--  modificar -->
                                    </div>
                                    <div class="col-sm-1" style="margin-top: -5px; margin-left: -25px;"> 
                                        <button type="button" id="btnEliminar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Eliminar Comprobante Presupuestal" >
                                            <li class="glyphicon glyphicon-remove"></li>
                                        </button> <!--  Eliminar -->
                                    </div> 
                                    <div class="col-sm-1" style="margin-top: -5px; margin-left: -25px;"> 
                                        <button type="button" id="btnListadoA" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; ;margin:0 auto; margin-top: 20px;" title="Listado de comprobantes que afectaron al comprobante" >
                                            <li class="glyphicon glyphicon-th-list"></li>
                                        </button>                       
                                    </div>
                                    <?php if($tipocomp ==2) {  ?>
                                    <div class="col-sm-1" style="margin-top: -25px; margin-left: -35px;"> 
                                        <button type="button" id="btnEliCas" disabled="true" class="btn btn-primary sombra" style="background: #f60303; color: #fff; border-color: #f60303; ;margin:0 auto; margin-top: 20px; width: 70%" title="Eliminar Comprobantes en Cascada" >
                                            <img src="img/eliminar.png"  style="width: 100%" >
                                        </button>                       
                                    </div>
                                    <?php } else { ?>
                                    <div class="col-sm-1" style="margin-top: -42px; margin-left: -25px;"> 
                                        <button type="button" id="agregarSolicitud" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; ;margin:0 auto; margin-top: 20px;" title="Agregar Solicitud" >
                                            <li class="glyphicon glyphicon-pushpin"></li>
                                        </button>                       
                                    </div>
                                    <script>
                                        $("#agregarSolicitud").click(function(){
                                            var fecha = $("#fecha").val();
                                            var form_data = {case: 4, fecha: fecha};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonSistema/consultas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'cierre');
                                                    if (response == 1) {
                                                        $("#periodoC").modal('show');

                                                    } else {
                                                        if($("#solicitudAprobada").val()==""){
                                                            $("#mensajemodal").html('Seleccionar Solicitud');
                                                            $("#modalmensajes").modal("show");
                                                            $("#btnmodmsj").click(function(){
                                                                 $("#modalmensajes").modal("hide");
                                                            });
                                                        } else {
                                                            var form_data = { estruc: 12, 
                                                                disponibilidad:$("#solicitudAprobada").val(),
                                                                fecha : $("#fecha").val(),
                                                                comprobante :$("#id_com_pptal").val(),

                                                            };
                                                            $.ajax({
                                                              type: "POST",
                                                              url: "consultasBasicas/AgregarDisponibilidad.php",
                                                              data: form_data,
                                                              success: function(response)
                                                              { 
                                                                  console.log(response+'agregar');
                                                                  if(response=='true'){
                                                                    $("#mensajemodal").html('Información Agregada Correctamente');
                                                                    $("#modalmensajes").modal("show");
                                                                    $("#btnmodmsj").click(function(){
                                                                        document.location.reload();
                                                                    });
                                                                  } else {
                                                                    $("#mensajemodal").html('No Se Ha Podido Agregar La Información');
                                                                    $("#modalmensajes").modal("show");
                                                                    $("#btnmodmsj").click(function(){
                                                                        document.location.reload();
                                                                    });
                                                                  }
                                                              }//Fin succes.
                                                            });
                                                        }
                                                    }
                                                }
                                            })
                                        })
                                    </script>
                                    <div class="col-sm-1" style="margin-top: -15px; margin-left: -25px;"> 
                                        <button type="button" id="btnEliCas" disabled="true" class="btn btn-primary sombra" style="background: #f60303; color: #fff; border-color: #f60303; ;margin:0 auto; margin-top: 20px; width: 70%" title="Eliminar Comprobantes en Cascada" >
                                            <img src="img/eliminar.png" style="width: 100%" >
                                        </button>                       
                                    </div>
                                    <?php } ?>
                                    <!-------------------ELIMINAR EN CASCADA-------------------------------->
                                    <script>
                                        $("#btnEliCas").click(function(){
                                            var fecha = $("#fecha").val();
                                            var form_data = {case: 4, fecha: fecha};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonSistema/consultas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'cierre');
                                                    if (response == 1) {
                                                        $("#periodoC").modal('show');
                                                    } else {
                                                        var form_data = { estruc: 12, id:+$("#id_com_pptal").val() };
                                                        $.ajax({
                                                          type: "POST",
                                                          url: "jsonPptal/consultas.php",
                                                          data: form_data,
                                                          success: function(response)
                                                          { 
                                                              $("#mensajemodaleliminar").html(response);
                                                              $("#modaleliminartodo").modal("show");
                                                          }//Fin succes.
                                                        });
                                                    }
                                                }
                                            })
                                        })
                                    </script>
                                    <div class="modal fade" id="modaleliminartodo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div id="forma-modal" class="modal-header">
                                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                                </div>
                                                <div class="modal-body" style="margin-top: 8px">
                                                    <label  style="font-weight: normal" id="mensajemodaleliminar" name="mensajemodaleliminar"></label>
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
                                    <div class="modal fade" id="modalmensajes" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div id="forma-modal" class="modal-header">
                                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                                </div>
                                                <div class="modal-body" style="margin-top: 8px">
                                                    <label  style="font-weight: normal" id="mensajemodal" name="mensajemodal"></label>
                                                </div>
                                                <div id="forma-modal" class="modal-footer">
                                                    <button type="button" id="btnmodmsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                        Aceptar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $("#eliminartodo").click(function(){
                                            var form_data ={estruc:13, id:+$("#id_com_pptal").val() };
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/consultas.php",
                                                data: form_data,
                                                success: function(response)
                                                {
                                                    if(response==1){
                                                        $("#mensajemodal").html('Periodo está cerrado.Verifique Nuevamente');
                                                        $("#modalmensajes").modal("show");
                                                    }else {
                                                        if(response==2){
                                                            $("#mensajemodal").html('Los detalles de algún comprobante estan conciliados, Verifique Nuevamente');
                                                            $("#modalmensajes").modal("show");
                                                        } else {
                                                            if(response==0){
                                                                jsShowWindowLoad('Eliminando..');
                                                                var form_data ={estruc:14, id:+$("#id_com_pptal").val() };
                                                                $.ajax({
                                                                    type: "POST",
                                                                    url: "jsonPptal/consultas.php",
                                                                    data: form_data,
                                                                    success: function(response)
                                                                    {
                                                                        console.log(response);
                                                                        jsRemoveWindowLoad();
                                                                        if(response==1){
                                                                            $("#mensajemodal").html('Información Eliminada Correctamente');
                                                                            $("#modalmensajes").modal("show");
                                                                            $("#btnmodmsj").click(function(){
                                                                                document.location.reload();
                                                                            });
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
                                        })
                                        $("#canEliTodo").click(function(){
                                           $("#modaleliminartodo").modal("hide"); 
                                        })
                                    </script>
                                    
                                    
                                    
                                    
                                    
                                        
                                    <!-- Habilitación del botón -->
<?php if (!empty($_SESSION['id_comp_pptal_ED'])) {
    $ss = $_SESSION['id_comp_pptal_ED'];
    ?>
                                        <script>
                                            $("#btnListadoA").attr('disabled', false);
                                            $("#btnListadoA").click(function () {
                                                window.open('informesPptal/inf_listado_afet_com.php?idPptal=<?php echo md5($ss) ?>&env=DISPPTL');
                                            });
                                        </script>
<?php } else { ?>
                                        <script>
                                            $("#btnListadoA").attr('disabled', true);
                                        </script>
<?php } ?>          
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#btnEliminar").click(function ()
                                            {
                                                var fecha = $("#fecha").val();
                                                var form_data = {case: 4, fecha: fecha};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "jsonSistema/consultas.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        console.log(response+'cierre');
                                                        if (response == 1) {
                                                            $("#periodoC").modal('show');


                                                        } else {
                                                            var idComP = $("#id_com_pptal").val();
                                                            var form_data = {estruc: 1, id_com: idComP};
                                                            $.ajax({
                                                                type: "POST",
                                                                url: "estructura_modificar_eliminar_pptal.php",
                                                                data: form_data,
                                                                success: function (response)
                                                                {
                                                                    console.log(response);
                                                                    if (response == 0)
                                                                    {
                                                                        $("#mdlDeseaEliminar").modal('show');
                                                                    } else
                                                                    {
                                                                        $("#mdlNoEliminar").modal('show');
                                                                    }
                                                                }// Fin success.
                                                            });
                                                        }
                                                    }
                                                });

                                                
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#btnModificarComp").click(function ()
                                            {

                                                modificarCompPptal();


                                            });
                                        });
                                    </script>
                                </div>
                               

                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#siguiente").click(function ()
                                        {
                                            var idComP = $("#id_com_pptal").val();
                                            var form_data = {estruc: 1, id_com: idComP};
                                            $.ajax({
                                                type: "POST",
                                                url: "estructura_modificar_eliminar_pptal.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    
                                                    if (response == 0)
                                                    {
                                                        siguiente();
                                                    } else
                                                    {
                                                        $("#siguiente").prop("disabled", true);
                                                        $(".eliminar").css('display', 'none');
                                                        $(".modificar").css('display', 'none');
                                                        $("#mdlYaHayAfec").modal('show');
                                                    }
                                                }// Fin success.
                                            });
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    function siguiente()
                                    {
                                        var tipocom = $("#tipocomp").val();
                                        if (tipocom != 2) {
                                            var idComP = $("#id_com_pptal").val();
                                            var form_data = {sesion: 'id_comp_pptal_ER', numero: idComP, nuevo: 'nuevo_ER', valN: 2};
                                            $.ajax({
                                                type: "POST",
                                                url: "estructura_seleccionar_pptal.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
                                                }
                                            });
                                        } else {
                                            jsShowWindowLoad('Guardando...');
                                            var form_data = {action:2, tipo:$("#tipoComPtal").val()};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_privadaJson.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    jsRemoveWindowLoad();
                                                    var resultado = JSON.parse(response);
                                                    var rta = resultado["rta"];
                                                    var msj = resultado["html"];
                                                    if(rta==1){
                                                        jsShowWindowLoad('Guardando...');
                                                        var idComP = $("#id_com_pptal").val();
                                                        var form_data = {action:1, id:idComP };
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "jsonPptal/gf_privadaJson.php",
                                                            data: form_data,
                                                            success: function (response)
                                                            {
                                                                jsRemoveWindowLoad();
                                                                console.log(response);
        //                                                        console.log($.trim(response)=='Error');
                                                                if($.trim(response) =='Error'){
                                                                    $("#noguardado").modal('show');
                                                                } else {
                                                                    var idComP = response;
                                                                    var form_data = { sesion: 'id_comp_pptal_CP', numero: idComP,  valN: 2};
                                                                    $.ajax({
                                                                      type: "POST",
                                                                      url: "estructura_seleccionar_pptal.php",
                                                                      data: form_data,
                                                                      success: function(data)
                                                                      {
                                                                        document.location = 'GENERAR_CUENTA_PAGAR.php';
                                                                      }// Fin success.
                                                                    });// Fin Ajax;
                                                                }
                                                            }
                                                        });
                                                    } else {

                                                        $("#mensaje").html(msj);
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnAceptar").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                        });
                                                        $("#btnCancelar").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                        })
                                                    }
                                                }
                                            })    
                                        }
                                    }
                                </script>
                                
                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#buscarDisp").change(function ()
                                        {
                                            if (($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0))
                                            {
                                                traerNum();
                                            }
                                        });
                                    });
                                </script>
                                <script type="text/javascript">
                                    function traerNum()
                                    {
                                        var form_data = {sesion: 'id_comp_pptal_ED', nuevo: 'nuevo_ED', numero: $("#buscarDisp").val(), valN: 1};
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_seleccionar_pptal.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                if (response == 1)
                                                {
                                                    document.location.reload();
                                                }
                                            }
                                        });
                                    }
                                </script>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $(document).click(function (e) {
                                            if (e.target.id != 'buscarDisp')
                                                $('#buscarDisp').val('');
                                            $('#listado').fadeOut();
                                        });
                                    });
                                </script>
                                <div class="form-group form-inline" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;">
                                    <input name="numero" type="hidden" value="<?php echo $numero; ?>">
                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            $("#solicitudAprobada").change(function ()
                                            {
                                                <?php if (empty($_SESSION['nuevo_ED'])) { ?>
                                                    if (($("#solicitudAprobada").val() == "") || ($("#solicitudAprobada").val() == 0))
                                                    {
                                                    var form_data = {estruc: 1};
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "estructura_expedir_disponibilidad.php",
                                                        data: form_data,
                                                        success: function (response)
                                                        {
                                                            document.location.reload();
                                                        }//Fin succes.
                                                    }); //Fin ajax.
                                                } else {
                                                    var form_data = {estruc: 2, id_comp: +$("#solicitudAprobada").val()};
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "estructura_expedir_disponibilidad.php",
                                                        data: form_data,
                                                        success: function (response)
                                                        {
                                                            document.location.reload();
                                                        }//Fin succes.
                                                    });
                                                }
                                                <?php } ?>
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        $("#fecha").change(function ()
                                        {
                                            var fecha = $("#fecha").val();
                                            var form_data = {case: 4, fecha: fecha};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonSistema/consultas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'cierre');
                                                    if (response == 1) {
                                                        $("#fecha").val("").focus();
                                                        $("#periodoC").modal('show');
                                                    } else {
                                                        fecha1();
                                                    }
                                                }
                                            });


                                        });
                                    </script>
                                    <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div id="forma-modal" class="modal-header">
                                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                                </div>
                                                <div class="modal-body" style="margin-top: 8px">
                                                    <p>Periodo ya ha sido cerrado</p>
                                                </div>
                                                <div id="forma-modal" class="modal-footer">
                                                    <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                        Aceptar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalTerceroDis" role="dialog" align="center" >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">          
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <label class="form_label"><strong style="color:#03C1FB;">*</strong>Tercero: </label>
                                                <select name="terceroModal" id="terceroModal" class="select2_single form-control input-sm" title="Tercero" style="width:250px;">
                                                    <option value="">Tercero</option>
                                               <?php 
                                                   $ter = "SELECT t.id_unico, IF(CONCAT_WS(' ',
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
                                                       FROM gf_tercero t WHERE compania = $com 
                                                       ORDER BY t.id_unico ";
                                                $tr = $mysqli->query($ter);
                                                if(mysqli_num_rows($tr)>0){
                                                    while ($row2 = mysqli_fetch_row($tr)) {
                                                       echo '<option value ="'.$row2[0].'">'.$row2[1].' - '.$row2[2].'</option>';
                                                    }
                                                }
                                               ?>
                                                </select>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="btnmodalTerceroDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                                                <button type="button" id="btnmodalTerceroDisC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <script>
                                        function asignarfecha() {
                                            var tipComPal = $("#tipoComPtal").val();
                                            var form_data = {estruc: 2, tipComPal: tipComPal};
                                            $.ajax({
                                                type: "POST",
                                                url: "consultasBasicas/validarFechas.php",
                                                data: form_data,
                                                success: function (response)
                                                {

                                                    response = response.replace(" ", "");
                                                    $("#fecha").datepicker("destroy");
                                                    $("#fecha").datepicker({changeMonth: true, minDate: response}).val(response);

                                                }
                                            });

                                        }
                                    </script>
                                    <script type="text/javascript">
                                        function fecha1()
                                        {
                                            var tipComPal = $("#tipoComPtal").val();
                                            var fecha = $("#fecha").val();
                                            var num = $("#noDisponibilidad").val();
                                            var sol = $("#solicitudAprobada").val();
                                            var tipoc = $("#tipocomp").val();
                                            <?php if (empty($_SESSION['nuevo_ED'])) { ?>
                                                console.log(sol == "");
                                                if (sol == "") {
                                                    var form_data = {estruc: 7, tipComPal: tipComPal, fecha: fecha, num: num, tipoc:tipoc};
                                                } else {
                                                    console.log('as');
                                                    var form_data = {estruc: 8, tipComPal: tipComPal, fecha: fecha, num: num, solicitud: sol,tipoc:tipoc};
                                                }
                                            <?php } else { ?>
                                                var form_data = {estruc: 9, tipComPal: tipComPal, fecha: fecha, num: num, solicitud: sol,tipoc:tipoc};
                                            <?php } ?>
                                                console.log(form_data);
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/validarFechas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response);
                                                    if (response == 1){
                                                        $("#myModalAlertErrFec").modal('show');
                                                    } else{

                                                        response = response.replace(' ', "");
                                                        response = $.trim(response);
                                                        $("#fechaVen").val(response);
                                                        var fechaAs = $("#fecha").val();
                                                        $("#fechaVen").datepicker("destroy");
                                                        $("#fechaVen").datepicker({changeMonth: true, minDate: fechaAs}).val(response);
                                                        $("#fechaVen").val(response);
                                                        
 
                                                    }
                                                }
                                            });
                                        }
                                    </script>
<?php if (empty($_SESSION['nuevo_ED'])) { ?>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $("#fecha").click(function () {
                                                    if ($("#noDisponibilidad").val() == "" || $("#noDisponibilidad").val() == "")
                                                    {
                                                        $("#myModalAlertTipCom").modal('show');
                                                    }
                                                });
                                            });
                                        </script>
<?php } ?>
                                </div>
                                <input type="hidden" name="MM_insert" >
                            </form>
                        </div> <!-- Cierra clase client-form contenedorForma -->
                    </div>
                    <div class="col-sm-12" > 
                        <div class="client-form form-inline" class="col-sm-10" >
                            <form name="formConRub" id="orm" class="form-inline" method="POST"  enctype="multipart/form-data" onsubmit="return validarValor()" action="json/registrar_EXP_DIS_DETALLE_COMPROBANTEJson.php">
                                <div class="row" style="margin-top: 5px; margin-bottom: 0px; margin-left: 0px;" > 
                                <?php if($tipocomp==2){ ?>
                                    <div class="col-sm-2 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="concepto" class="control-label" ><strong class="obligado">*</strong>Concepto:</label>
                                        </div>
                                        <br/>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <select name="concepto" id="concepto" class="select2_single form-control" title="Ingrese el concepto" style="width:150px; height: 25px" required>
                                                <option value="" selected="selected" >Concepto</option>
                                                <?php while ($rowCon = mysqli_fetch_row($concepto)) { ?>
                                                    <option value="<?php echo $rowCon[0]; ?>"><?php echo ucwords(mb_strtolower($rowCon[1])); ?></option>
                                                <?php } ?>
                                            </select>             
                                        </div>
                                    </div>
                                    <div class="col-sm-2 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="rubro" class="control-label" style="margin-top: 0px;"><strong class="obligado">*</strong>Rubro:</label>
                                        </div><br/>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <select name="rubro" id="rubro" class=" form-control" title="Seleccione el Rubro" style="width:150px; height: 36px" required>
                                                <option value="" selected="selected" >Rubro</option>
                                            </select>             
                                        </div>
                                    </div>
                                    <div class="col-sm-2 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="centroC" class="control-label" ><strong class="obligado">*</strong>Centro Costo:</label>
                                        </div><br/>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                        <select name="centroC" id="centroC" class="select2_single form-control" title="Seleccione el Centro Costo" style="width:150px; height: 25px" required>
                                            <?php 
                                            #** Buscar Centro Costo Varios **#
                                            $cv =$con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $anno AND nombre ='Varios'");
                                            if(count($cv)>0){
                                                echo '<option value="'.$cv[0][0].'">'. ucwords(mb_strtolower($cv[0][1])).'</option>';
                                            } else {
                                                echo '<option value="" >Centro Costo</option>';
                                            }
                                            #** Buscar Centro Costo **#
                                            $cv =$con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $anno AND nombre !='Varios'");
                                            if(count($cv)>0){
                                                for ($i = 0; $i < count($cv); $i++) {
                                                    echo '<option value="'.$cv[$i][0].'">'. ucwords(mb_strtolower($cv[$i][1])).'</option>';
                                                }                                                
                                            }
                                            ?>
                                        </select>  
                                        </div>
                                    </div> 
                                    <div class="col-sm-2 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="tercerod" class="control-label" ><strong class="obligado">*</strong>Tercero:</label>
                                        </div><br/>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                        <select name="tercerod" id="tercerod" class="select2_single form-control" title="Seleccione el Tercero" style="width:150px; height: 25px" required>
                                            <?php if (!empty($_SESSION['nuevo_ED'])) {
                                                echo '<option value="'. $rowComp[8].'">'. ucwords(mb_strtolower($rowComp[9])).' - '.$rowComp[10].'</option>';
                                                $idd =$rowComp[8];
                                                $ter = $con->Listar("SELECT t.id_unico, 
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
                                               WHERE t.id_unico !=$idd  
                                               ORDER BY t.id_unico");
                                            for ($i = 0; $i < count($ter); $i++) {
                                                echo '<option value="'.$ter[$i][0].'">'. ucwords(mb_strtolower($ter[$i][1])).' - '.$ter[$i][2].'</option>';
                                            }
                                            } else {
                                                echo '<option value="">Tercero</option>';
                                            }?>
                                        </select>  
                                        </div>
                                    </div> 
                                    <div class="col-sm-2 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="valor" class="control-label" ><strong class="obligado">*</strong>Valor:</label>
                                        </div>
                                        <br/>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:130px; height: 35px" placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>  
                                        </div>
                                    </div> 
																	 
																									   
																								  
																																			 
											  
											 
																								  
																																															 
																									   
																										
																																						
														  
																  
											  
										  
																									   
																								  
																																							   
												   
																								  
																																										 
																									
																  
											  
										  
																									   
																								  
																																				
												   
																								  
																																															  
												  
																			  
																																					  
															 
																																	 
													
																							   
											 
																	   
																																					   
															 
																					 
																																		   
																								 
											 
											  
												   
											  
										   
																									   
																								  
																																	   
											  
											 
																								  
																																																																													 
											  
										   
                                <?php } else { ?>
                                    <div class="col-sm-4 form-group form-inline" style="padding: 0px;">
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <label for="concepto" class="control-label" ><strong class="obligado">*</strong>Concepto:</label>
                                        </div>
                                        <div class="form-group form-inline" style="padding: 0px;">
                                            <select name="concepto" id="concepto" class="select2_single form-control" title="Ingrese el concepto" style="width:200px; height: 25px" required>
                                                <option value="" selected="selected" >Concepto</option>
                                                <?php while ($rowCon = mysqli_fetch_row($concepto)) { ?>
                                                    <option value="<?php echo $rowCon[0]; ?>"><?php echo ucwords(mb_strtolower($rowCon[1])); ?></option>
                                                <?php } ?>
                                            </select>             
                                        </div>
                                    </div>
                                    <div class="form-group form-inline col-sm-4"  style="padding: 0px;">
                                        <label for="rubro" class="control-label"><strong class="obligado">*</strong>Rubro:</label>
                                        <select name="rubro" id="rubro" class=" form-control input-sm" title="Seleccione el rubro" style="width:180px; height: 38px" required>
                                            <option value="" selected="selected" >Rubro</option>
                                        </select>            
                                    </div>
									
                                    <!-- Caja texto Valor -->
                                    <div class="form-group form-inline col-sm-3"  style="padding: 0px;">
                                        <table>
                                            <tr>
                                                <td  style="padding: 3px;">
                                                    <label for="valor" class="control-label">
                                                        <strong class="obligado">*</strong>Valor:
                                                    </label>
                                                </td>
                                                <td>
                                                    <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:150px; height: 38px" placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Evalúa que el valor no sea superior al saldo -->
                                       
                                    </div> 
                                <?php } ?>
                                    <input type="hidden" id="rubroFuente" name="rubroFuente">
                                    <input type="hidden" id="conceptoRubro" name="conceptoRubro">            
                                    <!-- Botón guardar -->
                                    <div class="col-sm-1 " align="left" >
                                        <button type="submit" onfocus="validaSaldo();" onmouseover="validaSaldo();" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-bottom: 0px; margin-left: -10px;" title="Guardar"><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                        <input type="hidden" name="MM_insert" >
                                    </div>
                                </div> <!-- Cierra clase row -->
                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#concepto").change(function ()
                                        {
                                            var opcion = '<option val="">Rubro</option>';
                                            if (($("#concepto").val() == "") || ($("#concepto").val() == 0))
                                            {

                                                $('#rubro').html(opcion).fadeIn();
                                            } else
                                            {
                                                var form_data = {proc: 2, id_con: +$("#concepto").val()};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_comprobante_pptal.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        if (response != 0)
                                                        {
                                                            opcion += response;
                                                            $('#rubro').html(opcion).fadeIn();
                                                            $('#rubro').focus();
                                                        } else
                                                        {
                                                            opcion = '<option val="">No hay rubro</option>';
                                                            $('#rubro').html(opcion).fadeIn();
                                                        }
                                                    }
                                                });
                                                asignaRubro();
                                            }

                                        });
                                    });
                                    </script>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#rubro").change(function ()
                                            {
                                                asignaRubro();
                                            });
                                        });
                                    </script>
                                    <script type="text/javascript">
                                        function asignaRubro()
                                        {
                                            if ($("#rubro").val() != 0 && $("#rubro").val() != '')
                                            {
                                                var rubro = $("#rubro").val();
                                                var rubroFuente_conceptoRubro = rubro.split("/");
                                                $("#rubroFuente").val(rubroFuente_conceptoRubro[0]);
                                                $("#conceptoRubro").val(rubroFuente_conceptoRubro[1]);
                                            } else
                                            {
                                                $("#rubroFuente").val(0);
                                                $("#conceptoRubro").val(0);
                                            }
                                        }
                                    </script>
                                     <script type="text/javascript">
                                            $(document).ready(function ()
                                            {
                                                $("#valor").keyup(function ()
                                                {
                                                    var tipocom = $("#tipocomp").val();
                                                    if (tipocom != 2) {
                                                        var valor = $("#valor").val();
                                                        valor = parseFloat(valor.replace(/\,/g, ''));
                                                        if ($("#rubroFuente").val() == "") {
                                                            $("#myModalAlert3").modal('show');
                                                        } else if ($("#concepto").val() == "") {
                                                            $("#myModalAlert2").modal('show');
                                                        } else {
                                                            //Validar Saldo A La Fecha Y Saldo Actual
                                                            var form_data = {estruc: 18, id_rubFue: +$("#rubroFuente").val(), fecha:$("#fechaCompP").val()};
                                                            $.ajax({
                                                                type: "POST",
                                                                url: "jsonPptal/consultas.php",
                                                                data: form_data,
                                                                success: function (response)
                                                                {
                                                                    console.log(response);
                                                                    var resVal = 0;
                                                                    respVal = parseFloat(response);
                                                                    if (respVal < valor)
                                                                    {
                                                                        $("#myModalAlert").modal('show');
                                                                    }
                                                                    console.log(response);
                                                                    
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
                            </form>
                        </div>  <!-- cierra clase client-form contenedorForma -->
                    </div>
                    <input type="hidden" id="valSal">


                    <script type="text/javascript">

                        $(document).ready(function ()
                        {
                            $('#btnNuevoComp').click(function () {
                                var form_data = {estruc: 1};
                                $.ajax({
                                    type: "POST",
                                    url: "estructura_expedir_disponibilidad.php",
                                    data: form_data,
                                    success: function (response)
                                    {
                                         document.location = 'EXPEDIR_DISPONIBILIDAD_PPTAL.php';
                                    }//Fin succes.
                                }); //Fin ajax.

                            });
                        });

                    </script>

                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">
                    <!-- Listado de registros -->
                    <div class="table-responsive contTabla col-sm-12" style="margin-top: -5px;">
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%"></td>
                                        <td class="cabeza"><strong>Concepto</strong></td>
                                        <td class="cabeza"><strong>Rubro</strong></td>
                                        <td class="cabeza"><strong>Fuente</strong></td>
                                        <?php if($tipocomp==2){ 
                                            echo '<td class="cabeza"><strong>Centro Costo</strong></td>';
                                            echo '<td class="cabeza"><strong>Tercero</strong></td>';
																   
																									  
                                        }?>
                                        <td class="cabeza"><strong>Valor</strong></td>
                                        <td class="cabeza"><strong>Documentos</strong></td>

                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Nombre</th>
                                        <th>Rubro</th>
                                        <th>Fuente</th>
                                        <?php if($tipocomp==2){ 
                                            echo '<th>Centro Costo</th>';
                                            echo '<th>Tercero</th>';
																   
																	  
                                        }?>
                                        <th>Valor</th>
                                        <th>Documentos</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($_SESSION['id_comp_pptal_ED']) && ($resultado == true)) {
                                        $valorTotal = 0;
                                        $saldoRegis = 0;

                                        while ($row = mysqli_fetch_row($resultado)) {

                                            if (!empty($_SESSION['nuevo_ED'])) {
                                                $valorPpTl = $row[3];
                                            } else {
                                                $saldDisp = 0;
                                                $totalAfec = 0;
                                                $queryDetAfe = "SELECT valor   
                                                        FROM gf_detalle_comprobante_pptal   
                                                        WHERE comprobanteafectado = " . $row[0];
                                                $detAfec = $mysqli->query($queryDetAfe);
                                                $totalAfe = 0;
                                                while ($rowDtAf = mysqli_fetch_row($detAfec)) {
                                                    $totalAfec += $rowDtAf[0];
                                                }

                                                $saldDisp = $row[3] - $totalAfec;
                                                $valorPpTl = $saldDisp;
                                            }?>
                                            <tr>
                                                <td class="oculto"><?php echo $row[0] ?></td>
                                                <td class="campos">
                                                    <div class="modElim">
                                                    <?php
                                                    if (!empty($_SESSION['nuevo_ED'])) {
                                                        $cierre = cierre($_SESSION['id_comp_pptal_ED']);
                                                        if ($cierre == 0) {
                                                            $afecd = afect($row[0]);
                                                            if ($afecd == 1) {
                                                            } else {?>   
                                                               <a class="eliminar" href="#<?php echo $row[0]; ?>" 
                                                                    <?php if (!empty($_SESSION['nuevo_ED'])) {
                                                                        echo 'onclick="javascript:eliminarDetComp(' . $row[0] . ')"';
                                                                    }?>>
                                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                                    </a>
                                                                    <a class="modificar" href="#<?php echo $row[0]; ?>"  
                                                                    <?php
                                                                    if (!empty($_SESSION['nuevo_ED'])) {
                                                                        echo 'onclick="javascript:modificarDetComp(' . $row[0] . ')"';
                                                                    }
                                                                    ?>
                                                                       >
                                                                        <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                                    </a>
                                                                <?php }
                                                            }
                                                        } ?>   
                                                    </div>
                                                </td>
                                                <td class="campos" align="left">
                                                    <div class="acotado"> 
                                                       <?php echo ucwords(mb_strtolower($row[1])); ?>
                                                    </div>
                                                </td>
                                                <td class="campos" align="left">
                                                    <div class="acotado">
                                                        <?php echo ucwords(mb_strtolower($row[2])); ?>
                                                    </div>

                                                </td>
                                                <td class="campos" align="left">
                                                    <div class="acotado">
                                                        <?php echo ucwords(mb_strtolower($row[5])); ?>
                                                    </div>
                                                </td>
                                                <?php if($tipocomp==2){ 
                                                    echo '<td class="campos" align="left">';
                                                    echo '<div class="acotado">';
                                                    echo ucwords(mb_strtolower($row[6]));
                                                    echo '</div></td>';
                                                    echo '<td class="campos" align="left">';
                                                    echo '<div class="acotado">';
                                                    echo ucwords(mb_strtolower($row[8])).' - '.$row[9];
                                                    echo '</div></td>';
                                                    
																		   
																						 
																				 
																						 
																	   
                                                }?>

                                                <td class="campos" align="right">
                                                    <div class="alienaTexto"></div>

                                                    <input type="hidden" id="valOcul<?php echo $row[0]; ?>"  value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>">

                                                    <div id="divVal<?php echo $row[0]; ?>" >
                                                        <?php
                                                        echo number_format($valorPpTl, 2, '.', ',');
                                                        $valorTotal += $valorPpTl;
                                                        ?>
                                                    </div>
                                                    <!-- Modificar los valores -->
                                                    <table align="right" id="tab<?php echo $row[0]; ?>" style="padding: 0px;  margin-top: 2px; margin-bottom: 2px;" >
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="valorMod" id="valorMod<?php echo $row[0]; ?>" maxlength="50" style="width:100px;" placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valorMod<?php echo $row[0]; ?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0]; ?>');" value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>" required>
                                                            </td>
                                                            <td>
                                                                <a href="#<?php echo $row[0]; ?>" onclick="javascript:verificarValor('<?php echo $row[0]; ?>', '<?php echo $row[4]; ?>');" >
                                                                    <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="#<?php echo $row[0]; ?>" onclick="javascript:cancelarModificacion(<?php echo $row[0]; ?>);" >
                                                                    <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <script type="text/javascript">
                                                        var id = "<?php echo $row[0]; ?>";
                                                        var idValorM = 'valorMod' + id;
                                                        var idTab = 'tab' + id;

                                                        $("#" + idTab).css("display", "none");

                                                    </script>

                                                </td>
                                                <td class="campos text-center" >
                                                    <div class="alienaSaldo"></div>
                                                    <a id="btnDetalleMovimiento" style="Cursor: pointer;" onclick="javascript:abrirdetalleMov(<?php echo $row[0] ?>,<?php echo $valorPpTl ?>);" title="Documentos"><i class="glyphicon glyphicon-file"></i></a>
                                                </td>

                                            </tr>
                                            <?php
                                            //}
                                        }
                                    }
                                    ?>


                                </tbody>
                            </table>

                        </div>

                        <div class="col-sm-12" style="font-size: 12px; height: 30px; position: relative; padding: 0px; margin-top: 5px;">

                            <div class="texto" style="" align="right">
                                <?php
                                if (!empty($valorTotal)) {
                                    ?>      
                                    <span style="font-weight: bold;">
                                        Valor Total:
                                    </span> 
                                    <?php
                                }
                                ?>

                            </div>

                            <div class="valor" align="right" style="margin-right:170px; margin-top: -12px">
                                <span style="">
                                    <?php
                                    if (!empty($valorTotal)) {
                                        echo number_format($valorTotal, 2, '.', ',');
                                    }
                                    ?>
                                </span>
                            </div>

                        </div>


                        <?php
                        if ($numDet != 0) {
                            ?>

                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    cambiar();
                                });
                            </script>

                            <script type="text/javascript">
                                function cambiar()
                                {

                                    var elementoTexto = $(".alienaTexto");
                                    var posicionTexto = elementoTexto.position();
                                    $(".texto").width(posicionTexto.left);

                                    var elementoValor = $(".alienaValor");
                                    var posicionValor = elementoValor.position();
                                    $(".valor").width(posicionValor.left);

                                    var elementoSaldoR = $(".alienaSaldo");
                                    var posicionSaldoR = elementoSaldoR.position();
                                    $(".saldoR").width(posicionSaldoR.left);

                                }
                            </script>

    <?php
}
?>
                    </div> <!-- Cierra clase table-responsive contTabla  -->

                </div>

            </div> <!-- Cierra clase col-sm-10 text-left -->

        </div> <!-- Cierra clase row content -->
    </div> <!-- Cierra clase container-fluid text-center -->
    <script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>


    <script>
            $(".select2_single").select2();
    </script>
    <!-- Divs de clase Modal para las ventanillas de eliminar. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Detalle Solicitud?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
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

    <div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales para eliminación -->


    <!-- Divs de clase Modal para las ventanillas de modificar. -->

    <!-- Mensaje de modificación exitosa. -->
    <div class="modal fade" id="ModificacionConfirmada" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificarConf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error al modificar el valor al ser superior al saldo-->
    <div class="modal fade" id="myModalAlertMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor ingresado es superior al saldo disponible.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptValMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Mensaje dato a modificar no es válido. -->
    <div class="modal fade" id="ModificacionNoValida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El dato a modificar no es válido.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificarNoVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de fallo en la modificación. -->
    <div class="modal fade" id="ModificacionFallida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificarFall" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales para modificación -->

    <!-- Modal de alerta. El valor es mayor que el saldo.  -->
    <div class="modal fade" id="myModalAlert" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor ingresado es superior al saldo disponible.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de alerta. No se a seleccionado en el concepto.  -->
    <div class="modal fade" id="myModalAlert2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un concepto válido.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptCon" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de alerta. No se a seleccionado en el concepto.  -->
    <div class="modal fade" id="myModalAlert3" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un rubro válido.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptCon3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
    <div class="modal fade" id="myModalAlertModInval" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptValModInval" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
    <div class="modal fade" id="myModalAlertModSuperior" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor a modificar no puede ser superior al valor existente para aprobar. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptValModSup" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error de fecha --> 
    <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Fecha Inválida. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error de fecha de vencimiento vacía --> 
    <div class="modal fade" id="ModalAlertFecVen" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La fecha de vencimiento está vacía. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptErrFecVen" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de alerta. Los valores ingresados no son numéricos.  -->
    <div class="modal fade" id="myModalInvalido" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptInval" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de alerta. Los valores ingresados no son numéricos.  -->
    <div class="modal fade" id="myModalAlertTipCom" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe selecccionar primero un Tipo Comprobante Presupuestal.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptTipCom" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error generar el registro primero --> 
    <div class="modal fade" id="ModalAlertNoMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe generar primero la Disponibilidad Presupuestal.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptNoMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modHuella" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Firma Dactilar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">

                    <img src="images/lectorhuella2.png" style="width: 500px; height: 300px"/><br/>
                    <a href="LISTAR_TERCERO_EMPLEADO_NATURAL2.php">Registrar Huella</a>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGuarHuella" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                    <button type="button" id="btnCancelHuella" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- No se puede eliminar el comprobante --> 
    <div class="modal fade" id="mdlNoEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se puede eliminar este comprobante ya que tiene afectaciones.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mdlModificadoComExito" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificadoComExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mdlModificadoComError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificadoComError" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
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
    <div class="modal fade" id="mdlNoModificarPtal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No es posible modificar esta comprobante ya que tiene afectaciones.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModificadoComError" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlYaHayAfec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este comprobante ya tiene afectación.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnYaHayAfec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlDeseaEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar detalles del comprobante seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAcepEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="javascript:eliminardetalles()">Aceptar</button>
                    <button type="button" id="btnCancelEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlExitEliminarComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Los detalles del comprobante han sido eliminados satisfactoriamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnExitEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlErrorEliminarComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El comprobante no fue eliminado.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
       function eliminardetalles()
            {
                console.log('entro');
                var idComP = $("#id_com_pptal").val();
                var numDet = $("#numDet").val();
                var sesion = 'id_comp_pptal_ED|nuevo_ED';

                var form_data = {estruc: 4, id_com: idComP, sesion: sesion, numDet: numDet};
                $.ajax({
                    type: "POST",
                    url: "estructura_modificar_eliminar_pptal.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        if (response == 1)
                        {
                            $("#mdlExitEliminarComp").modal('show');
                        } else
                        {
                            $("#mdlErrorEliminarComp").modal('show');
                        }
                    }// Fin success.
                });// Fin Ajax;

            }
    </script>

    <script type="text/javascript">
        $('#btnExitEliminarComp').click(function ()
        {
            document.location.reload(); //Hay que dejar. Quitar al probar.
        });
    </script>



    <script>
        function firma() {

            $("#modHuella").modal('show');
        }
    </script>


    <script type="text/javascript">
        $('#AceptVal').click(function () {
            $("#valor").val('').focus();
        });
    </script>

    <script type="text/javascript">
        $('#AceptCon').click(function () {
            $("#valor").val('');
            $("#concepto").focus();
        });
    </script>


    <script type="text/javascript">
        $('#AceptCon3').click(function () {
            $("#valor").val('');
            $("#rubro").focus();
        });
    </script>

    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
        function eliminarDetComp(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminar_GF_DETALLE_COMPROBANTE_PPTALJson.php?id=" + id,
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
    </script>
    <script type="text/javascript">
        function modal()
        {
            $("#myModal").modal('show');
        }
    </script>

    <script type="text/javascript">

        $('#ver1').click(function () {
            document.location = 'EXPEDIR_DISPONIBILIDAD_PPTAL.php';
        });

    </script>

    <script type="text/javascript">

        $('#ver2').click(function () {
            document.location = 'EXPEDIR_DISPONIBILIDAD_PPTAL.php';
        });

    </script>


    <!-- Fin funciones eliminar -->

    <!-- Función para la modificación del registro. -->
    <script type="text/javascript">

        function modificarDetComp(id)
        {
            if (($("#idPrevio").val() != 0) || ($("#idPrevio").val() != ""))
            {
                var cambiarTab = 'tab' + $("#idPrevio").val();
                var cambiarDiv = 'divVal' + $("#idPrevio").val();
                var cambiarOcul = 'valOcul' + $("#idPrevio").val();
                var cambiarMod = 'valorMod' + $("#idPrevio").val();

                if ($("#" + cambiarTab).is(':visible'))
                {

                    $("#" + cambiarTab).css("display", "none");
                    $("#" + cambiarDiv).css("display", "block");
                    $("#" + cambiarMod).val($("#" + cambiarOcul).val());
                }

            }

            var idValor = 'valorMod' + id;
            var idDiv = 'divVal' + id;
            var idModi = 'modif' + id;
            var idTabl = 'tab' + id;

            $("#" + idDiv).css("display", "none");
            $("#" + idTabl).css("display", "block");

            $("#idActual").val(id);

            if ($("#idPrevio").val() != id)
                $("#idPrevio").val(id);


        }

    </script>



    <script type="text/javascript">
        function cancelarModificacion(id) //modificarDetComp(id)
        {

            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;
            var idValorM = 'valorMod' + id;
            var idValOcul = 'valOcul' + id;

            $("#" + idDiv).css("display", "block");
            $("#" + idTabl).css("display", "none");
            $("#" + idValorM).val($("#" + idValOcul).val());

        }
    </script>



    <script type="text/javascript">
        function guardarModificacion(id) //modificarDetComp(id)
        {

            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;
            var idCampoValor = 'valorMod' + id;
            var idValOcul = 'valOcul' + id;

            var valor = $("#" + idCampoValor).val();
            valor = valor.replace(/\,/g, ''); //Elimina la coma que separa los miles.


            if (($("#" + idCampoValor).val() == "") || ($("#" + idCampoValor).val() == 0))
            {
                $("#ModificacionNoValida").modal('show');
                $("#" + idCampoValor).val($("#" + idValOcul).val());
            } else
            {
                var form_data = {id_val: id, valor: valor};
                $.ajax({
                    type: "POST",
                    url: "json/modificar_GF_DETALLE_COMPROBANTE_PPTALJson.php",
                    data: form_data,
                    success: function (response)
                    {
                        if (response != 0)
                        {
                            $("#ModificacionConfirmada").modal('show');
                            afuera();
                        } else
                        {
                            $("#ModificacionFallida").modal('show');
                        }

                    }
                });
            }

        }
    </script>

    <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
    <script type="text/javascript">

        function verificarValor(id_txt, id_rubFue)
        {
            var tipocom = $("#tipocomp").val();
            if (tipocom != 2) {
            var resVal = 0;
            var idValMod = "valorMod" + id_txt;
            var validar = $("#" + idValMod).val();

            var id_ocul = "valOcul" + id_txt;
            var valOriginal = $("#" + id_ocul).val();

            validar = parseFloat(validar.replace(/\,/g, '')); //Elimina la coma que separa los miles.
            valOriginal = parseFloat(valOriginal.replace(/\,/g, ''));

            if ((isNaN(validar)) || (validar == 0) || (validar == ""))
            {
                $("#myModalAlertModInval").modal('show');
            } else
            {
                //Validar Saldo A La Fecha Y Saldo Actual
                var form_data = {estruc: 18, id_rubFue: id_rubFue, fecha:$("#fechaCompP").val()};
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/consultas.php",
                    data: form_data,
                    success: function (response)
                    {
                        var resVal = 0;
                        resVal = parseFloat(response)+valOriginal;
                        console.log(valOriginal);
                        console.log(response);
                        if (resVal < validar)
                        {
                            $("#myModalAlertMod").modal('show');
                        } else
                        {
                            guardarModificacion(id_txt);
                        }

                    }
                });
            } //Fin de If. 
            } else {
                guardarModificacion(id_txt);
            }

        }

    </script>

    <script type="text/javascript">
        function valida()
        {
            if ($("#fechaVen").val() == "")
            {
                $("#ModalAlertFecVen").modal('show');
                return false;
            }

            return true;

        }
    </script>


    <script type="text/javascript">
        function modal()
        {
            $("#Modificacion").modal('show');
        }
    </script>

    <script type="text/javascript">

        $('#btnModificarConf').click(function ()
        {
            document.location.reload();
        });

    </script>

    <script type="text/javascript">

        function afuera() //modificarDetComp(id)
        {

            $(document).click(function ()
            {
                document.location.reload();
            });
        }

    </script>

    <script type="text/javascript">
        //Si se ingresan valores diferentes a los numéricos en alguna de las casillas 
        // de la lista para su modificación.
        $('#AceptValModInval').click(function ()
        {
            var id_mod = "valorMod" + $("#idActual").val();
            var id_ocul = "valOcul" + $("#idActual").val();
            $("#" + id_mod).val($("#" + id_ocul).val()).focus();
        });
    </script>

    <script type="text/javascript">
        //Si se ingresan valores superiores a los valores para aprobar en alguna de las casiilas 
        // de la lista para su modificación.
        $('#AceptValModSup').click(function ()
        {
            var id_mod = "valorMod" + $("#idActual").val();
            var id_ocul = "valOcul" + $("#idActual").val();
            $("#" + id_mod).val($("#" + id_ocul).val()).focus();
        });
    </script>

    <script type="text/javascript">
        //Si se ingresan valores superiores a los valores para aprobar en alguna de las casiilas 
        // de la lista para su modificación.
        $('#AceptValMod').click(function ()
        {
            var id_mod = "valorMod" + $("#idActual").val();
            var id_ocul = "valOcul" + $("#idActual").val();
            $("#" + id_mod).val($("#" + id_ocul).val()).focus();
        });
    </script>

    <script type="text/javascript">
        //Si se ingresan valores superiores a los valores para aprobar en alguna de las casillas 
        // de la lista para su modificación.
        $('#AceptTipCom').click(function ()
        {
            $("#tipoComPtal").focus();
        });
    </script>

    <script type="text/javascript">

        $('#AceptErrFec').click(function () {


            $("#fecha").val("");
            $("#fechaVen").val("");


        });

    </script>


    <!-- Fin funciones modificar -->

    <script type="text/javascript">

        $('#AceptErrFecVen').click(function () {
            $("#fecha").focus();
        });

    </script>

    <script type="text/javascript">
        //Aceptar el valor es inválido.
        $('#AceptInval').click(function () {
            $("#valor").val('').focus();
        });
    </script>

    <!-- Validar el campo valor del formulario concepto al guardar el dato. Esto es si se pega (copy/paste) un valor
    en el campo sin digitarlo -->
    <script type="text/javascript">
        function validarValor()
        {
            var tipocom = $("#tipocomp").val();
            if (tipocom != 2) {

                var valor = $("#valor").val();
                var rubro = $("#rubroFuente").val();
                var valSal = $("#valSal").val();
                valor = valor.replace(/\,/g, '');
                if ((isNaN(valor)) || (valor == 0) || (valor == ""))
                {
                    $("#myModalInvalido").modal('show');
                    return false;
                } else if (valSal != 2)
                {
                    $("#myModalAlert").modal('show');
                    return false;
                } else if (valSal == 2)
                {
                    return true;
                }

            }
        } //Fin función validarValor

    </script>

<?php
if (empty($_SESSION['nuevo_ED'])) {
    ?>
        <script type="text/javascript">

            $('.modElim').click(function ()
            {
                $("#ModalAlertNoMod").modal('show');

            });

        </script>
<?php } ?>

    <!-- Validar el campo valor del formulario concepto al guardar el dato. Esto es si se pega (copy/paste) un valor
    en el campo sin digitarlo -->
    <script type="text/javascript">

        function validaSaldo()
        {
            var fecha = $("#fecha").val();
            var form_data = {case: 4, fecha: fecha};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response+'cierre');
                    if (response == 1) {
                        $("#periodoC").modal('show');
                        $("#btnGuardar").prop("disabled", true);
                        
                    } else {
                        var tipocom = $("#tipocomp").val();
                        if (tipocom != 2) {
                            if (($("#rubroFuente").val() != 0) || ($("#rubroFuente").val() != ""))
                            {
                                var valor = $("#valor").val();
                                valor = parseFloat(valor.replace(/\,/g, ''));
                                var form_data = {proc: 3, id_rubFue: +$("#rubroFuente").val()}; //
                                $.ajax({
                                    type: "POST",
                                    url: "estructura_comprobante_pptal.php",
                                    data: form_data,
                                    success: function (response)
                                    {
                                        var res;
                                        res = parseFloat(response);
                                        if (res >= valor)
                                        {
                                            $("#valSal").val(2);
                                        } else
                                        {
                                            $("#valSal").val(1);
                                        }
                                    }
                                }); //Cierra ajax
                            }
                        }
                    }
                }
            })
        }
    </script>


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
    </script>
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
                    <button type="button" id="btnG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <script type="text/javascript">

        $("#btnG").click(function () {
            document.location.reload();
        });
        $("#btnG2").click(function () {
            document.location.reload();

        });

    </script>
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
    <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">          
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMensaje" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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

        function modificarCompPptal()
        {
            var idComP = $("#id_com_pptal").val();
            var descripcion = $("#descripcion").val();
            var fecha = $("#fecha").val();
            var fechaVen = $("#fechaVen").val();
            var tercero = $("#tercero").val();
													  
            var form_data = {estruc: 2, id_com: idComP, tercero:tercero,descripcion: descripcion, fecha: fecha, fechaVen: fechaVen};
            $.ajax({
                type: "POST",
                url: "estructura_modificar_eliminar_pptal.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                    if (response == 1)
                    {
                        $("#mdlModificadoComExito").modal('show');
                    } else
                    {
                        $("#mdlModificadoComError").modal('show');
                    }
                }// Fin success.
            });// Fin Ajax;


        }

    </script>




    <script type="text/javascript">
        $('#btnModificadoComExito').click(function ()
        {
            document.location.reload(); //Hay que dejar. Quitar al probar.
        });
    </script>
<?php
if (!empty($_SESSION['nuevo_ED'])) {
    ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                var idComP = $("#id_com_pptal").val();
                var form_data = {estruc: 1, id_com: idComP};
                $.ajax({
                    type: "POST",
                    url: "estructura_modificar_eliminar_pptal.php",
                    data: form_data,
                    success: function (response)
                    {
                        if (response == 0)
                        {
                            <?php
                            $cierre = cierre($_SESSION['id_comp_pptal_ED']);
                            if ($cierre == 1) {
                                ?>
                                $("#solicitudAprobada").prop("disabled", true);
                                $("#tipoComPtal").prop("disabled", true);
                                $("#noDisponibilidad").prop("disabled", true);
                                $("#descripcion").prop("disabled", true);
                                $("#fecha").prop("disabled", true);
                                $("#fechaVen").prop("disabled", true);
                                $("#siguiente").prop("disabled", true);
                                $("#btnGuardarComp").prop("disabled", true);
                                $("#btnEliCas").prop("disabled", true);
                                $("#agregarSolicitud").prop("disabled", true);
                                $("#btnEliminar").prop("disabled", true);
                                $("#concepto").prop("disabled", true);
                                $("#rubro").prop("disabled", true);
                                $("#valor").prop("disabled", true);
                                $("#btnGuardar").prop("disabled", true);

    <?php } else { ?>
                                $("#siguiente").prop("disabled", false);
    <?php } ?>
                        } else
                        {
                            $("#siguiente").prop("disabled", true);
                        }
                    }// Fin success.
                });
            });
        </script>
<?php } ?>   
        
<?php
if (!empty($_SESSION['id_comp_pptal_ED'])) {
    ?>
                        <script type="text/javascript">

                            $("#btnGuardarComp").prop("disabled", false);
                            $("#btnEliCas").prop("disabled", true);
                            $("#btnGuardar").prop("disabled", false);
                            $("#btnImprimir").prop("disabled", false);

                            //$("#descripcion").val("<?php //echo $descripcion; ?>");
                            //$("#descripcion").attr('readonly','readonly');
                            $("#descripcion").removeAttr('readonly');

                        </script>
    <?php
} else {
    ?>
                        <script type="text/javascript">

                            $("#btnGuardarComp").prop("disabled", true);
                            $("#btnEliCas").prop("disabled", false);
                            $("#btnGuardar").prop("disabled", true);
                            $("#btnImprimir").prop("disabled", true);

                            $("#descripcion").val("");
                            //$("#descripcion").removeAttr('readonly');
                            $("#descripcion").attr('readonly', 'readonly');

                        </script>
    <?php
}

if (!empty($_SESSION['nuevo_ED'])) {
    ?>
                        <script type="text/javascript">

                            $("#btnGuardarComp").prop("disabled", true);
                            $("#btnEliCas").prop("disabled", false);
                            $("#agregarSolicitud").prop("disabled", false);
                            $("#btnEliminar").prop("disabled", false);
                            $("#btnModificarComp").prop("disabled", false);
                        </script>
    <?php
}

if (empty($_SESSION['nuevo_ED'])) {
    ?>
                        <script type="text/javascript">

                            $("#btnGuardar").prop("disabled", true);
                            $("#btnEliminar").prop("disabled", true);
                            $("#agregarSolicitud").prop("disabled", true);
                            $("#btnModificarComp").prop("disabled", true);
                        </script>
    <?php
}
?>
                         <?php if (!empty($_SESSION['nuevo_ED'])) { ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#btnGuardarComp").prop("disabled", true);
                                            $("#btnEliCas").prop("disabled", false);
                                            
                                        });
                                    </script>
                                <?php } else { ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#btnGuardarComp").prop("disabled", false);
                                            $("#siguiente").prop("disabled", true);
                                            $("#btnEliCas").prop("disabled", true);
                                        });
                                    </script>
                                <?php } ?>
<?php
if (!empty($_SESSION['nuevo_ED'])) {
    $cierre = cierre($_SESSION['id_comp_pptal_ED']);
    if ($cierre == 1) {
        ?>
            <script>
                $("#solicitudAprobada").prop("disabled", true);
                $("#tipoComPtal").prop("disabled", true);
                $("#noDisponibilidad").prop("disabled", true);
                $("#descripcion").prop("disabled", true);
                $("#fecha").prop("disabled", true);
                $("#fechaVen").prop("disabled", true);
                $("#siguiente").prop("disabled", true);
                $("#btnGuardarComp").prop("disabled", true);
                $("#btnEliCas").prop("disabled", true);
                $("#agregarSolicitud").prop("disabled", true);
                $("#btnEliminar").prop("disabled", true);
                $("#concepto").prop("disabled", true);
                $("#rubro").prop("disabled", true);
                $("#valor").prop("disabled", true);
                $("#btnGuardar").prop("disabled", true);
                $("#btnModificarComp").prop("disabled", true);
            </script>    
    <?php } else {
        ?>
    <?php }
}
?>    
<script>
function jsRemoveWindowLoad() {
    // eliminamos el div que bloquea pantalla
    $("#WindowLoad").remove(); 
}
 
function jsShowWindowLoad(mensaje) {
    //eliminamos si existe un div ya bloqueando
    jsRemoveWindowLoad(); 
    //si no enviamos mensaje se pondra este por defecto
    if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
    //centrar imagen gif
    height = 20;//El div del titulo, para que se vea mas arriba (H)
    var ancho = 0;
    var alto = 0; 
    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
    if (window.innerWidth == undefined) ancho = window.screen.width;
    else ancho = window.innerWidth;
    if (window.innerHeight == undefined) alto = window.screen.height;
    else alto = window.innerHeight; 
    //operación necesaria para centrar el div que muestra el mensaje
    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
   //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
        //creamos el div que bloquea grande------------------------------------------
        div = document.createElement("div");
        div.id = "WindowLoad";
        div.style.width = ancho + "px";
        div.style.height = alto + "px";        
        $("body").append(div); 
        //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
        input = document.createElement("input");
        input.id = "focusInput";
        input.type = "text"; 
        //asignamos el div que bloquea
        $("#WindowLoad").append(input); 
        //asignamos el foco y ocultamos el input text
        $("#focusInput").focus();
        $("#focusInput").hide(); 
        //centramos el div del texto
        $("#WindowLoad").html(imgCentro);
 
}
</script>

<style>
#WindowLoad{
    position:fixed;
    top:0px;
    left:0px;
    z-index:3200;
    filter:alpha(opacity=80);
   -moz-opacity:80;
    opacity:0.80;
    background:#FFF;
}
</style>

    <?php require_once 'footer.php'; ?>
    <?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>

</body>
</html>