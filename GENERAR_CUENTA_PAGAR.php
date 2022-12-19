<?php
require_once('Conexion/conexion.php'); 
require_once('jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
$nm         = reservasva();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$numero     = "";
$fecha      = "";
$fechaVen   = "";
$descripcion = "";
$num_anno   = anno($_SESSION['anno']);
if (!empty($_GET['cxp'])) {
    $iddis = $_GET['cxp'];
    $dis = "SELECT id_unico FROM gf_comprobante_pptal WHERE md5(id_unico) ='$iddis'";
    $dis = $mysqli->query($dis);
    $dis = mysqli_fetch_row($dis);
    $dis = $dis[0];
    $_SESSION['id_comp_pptal_CP'] = $dis;
    $_SESSION['nuevo_CP'] = 1;
}
if (!empty($_SESSION['id_comp_pptal_CP'])) {
    $queryGen = "SELECT detComP.id_unico, rub.nombre, detComP.valor, 
         rubFue.id_unico, fue.nombre, rub.codi_presupuesto , cc.nombre, 
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
                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                con.nombre 
         FROM gf_detalle_comprobante_pptal detComP
         left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
         left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
         left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
         left join gf_concepto con on con.id_unico = conRub.concepto 
         left join gf_fuente fue on fue.id_unico = rubFue.fuente 
         LEFT JOIN gf_centro_costo cc ON detComP.centro_costo = cc.id_unico 
         LEFT JOIN 
                gf_tercero t ON detComP.tercero= t.id_unico
         where detComP.comprobantepptal = " . $_SESSION['id_comp_pptal_CP'];
    $resultado = $mysqli->query($queryGen);
    $numRegistro = mysqli_num_rows($resultado);
    $queryCompro = "SELECT
               comp.id_unico,
               comp.numero,
               comp.fecha,
               comp.descripcion,
               comp.fechavencimiento,
               comp.tipocomprobante,
               tipCom.codigo,
               tipCom.nombre,
               comp.tercero,
               cc.id_unico, 
               cc.nombre, 
               comp.numerocontrato, 
               p.id_unico, 
               LOWER(p.nombre) , 
               t.id_unico, 
               t.numeroidentificacion ,
               CONCAT_WS(' ', t.razonsocial, t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
             FROM
               gf_comprobante_pptal comp
             LEFT JOIN
               gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico
             LEFT JOIN 
               gf_clase_contrato cc ON comp.clasecontrato = cc.id_unico 
             LEFT JOIN 
                gf_proyecto p ON comp.proyecto = p.id_unico 
             LEFT JOIN 
                gf_tercero t ON comp.tercero = t.id_unico 
             WHERE
               comp.id_unico = " . $_SESSION['id_comp_pptal_CP'];

    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $tipocomprobante = $rowComp[5];
    $terceroComp = $rowComp[8];
    
    $numeroContrato = $rowComp[11];
    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    $fecha = $dia . "/" . $mes . "/" . $anio;
    $fechaDP = $dia . "-" . $mes . "-" . $anio;
    ;
    //Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT id_unico, numero     
         FROM gf_comprobante_pptal 
         WHERE tipocomprobante = 6 
         AND estado = 1 
         AND id_unico != '" . $_SESSION['id_comp_pptal_CP'] . "' 
         ORDER BY numetipocomprobantero";
    $numeroSoli = $mysqli->query($queryNumSol);
}


$queryTipComPtal = "SELECT id_unico, codigo, nombre        
       FROM gf_tipo_comprobante_pptal 
       WHERE clasepptal = 16 
       AND tipooperacion = 1  AND vigencia_actual = 1 
       AND compania = $compania 
       ORDER BY codigo";
$tipoComPtal = $mysqli->query($queryTipComPtal);

//Consulta para listado de Número Solicitud. // WHERE tipocomprobante = 6 era clase 14

$querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
       FROM gf_comprobante_pptal  comp 
       LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
       WHERE comp.parametrizacionanno = $anno AND tipcomp.clasepptal = 15
       AND comp.estado = 3
       OR comp.estado = 4
       ORDER BY comp.numero";

$SolAprob = $mysqli->query($querySolAprob);

//Consulta para el listado de concepto de la tabla gf_tipo_comprobante.

if (!empty($_SESSION['id_comp_pptal_CP'])) {
    $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
       FROM gf_tercero ter 
       LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
       WHERE ter.compania = $compania 
       GROUP BY ter.id_unico LIMIT 20";
} else {
$queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
       FROM gf_tercero ter 
       LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
       WHERE ter.compania = $compania 
       GROUP BY ter.id_unico LIMIT 20";
}
$tercero = $mysqli->query($queryTercero);

// Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
$natural = array(2, 3, 5, 7, 10);
$juridica = array(1, 4, 6, 8, 9);

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

#*¨ Proyecto 

?>
<title>Generar Cuenta Por Pagar</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/chosen.min.css"/>
<script src="js/jquery-ui.js"></script> 
<script src="js/chosen.jquery.js"></script> 
<script src="js/chosen.jquery.min.js"></script> 
<style type="text/css">
    .area
    { 
        height: auto !important;  
    }  
    /*Esto permite que el texto contenido dentro del div
    no se salga de las medidas del mismo.*/
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
        font-size: 12px;
    }
    .itemListado
    {
        margin-left: 5px;
        margin-top: 5px;
        width: 150px;
        cursor: pointer;
    }
    #listado 
    {
        width: 250px;
        height: 120px;
        overflow: auto;
        background-color: white;
    }
</style>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<script type="text/javascript">
    $(document).ready(function ()
    {

        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;

        if (dia < 10)
        {
            dia = "0" + dia;
        }

        if (mes < 10)
        {
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
            changeYear: true,
            yearSuffix: '',
            yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
            maxDate: '31/12/<?php echo $num_anno?>',
            minDate: '01/01/<?php echo $num_anno?>'
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
<?php if (empty($_SESSION['nuevo_CP'])) {
    if (!empty($_SESSION['id_comp_pptal_CP'])) {
        ?>
                var fechaI = '<?php echo date("d/m/Y", strtotime($fechaDP)); ?>';
                $("#fecha").datepicker({changeMonth: true}).val(fechaI);

    <?php } else { ?>
                $("#fecha").datepicker({changeMonth: true}).val();
    <?php }
} else { ?>

            var fechaI = '<?php echo date("d/m/Y", strtotime($fechaDP)); ?>';
            $("#fecha").datepicker({changeMonth: true}).val(fechaI);
<?php } ?>
        $("#fechaAct").val(fecAct);

    });

</script>

</head>
<body >
    
    <input type="hidden" name="moviEscogidos" id="moviEscogidos" >
    <input type="hidden" id="id_comp_pptal_CP" value="<?php echo $_SESSION['id_comp_pptal_CP']; ?>">
    <input type="hidden" id="idComPtal" value="<?php echo $_SESSION['id_comp_pptal_CP']; ?>">
    <input type="hidden" id="fechaAct">
    <input type="hidden" id="tipoDeComp" value="<?php echo $tipocomprobante; ?>">
    <input type="hidden" id="compEgreso">
    <input type="hidden" id="compContable">
    <input type="hidden" id="numRegistros" value="<?php echo $numRegistro ?>">    
    <input type="hidden" id="terceroComp" value="<?php echo $terceroComp ?>">
    <input type="hidden" name="tipocomp" id="tipocomp" value="<?php echo $tipocomp; ?>">
    <script>
        $(document).ready(function ()
        {
            
                if($("#id_comp_pptal_CP").val()!="" && $("#compContable").val()==0 ) {
                /*Buscar Movimientos de Almacen */
                var form_data ={action:2, tercero : $("#terceroComp").val()}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_cuenta_pagarJson.php",
                    data: form_data,
                    success: function(response)
                    {
                        if(response>0){
                            $("#movAlmacen").css("display","block");
                        } else {
                            $("#movAlmacen").css("display","none");
                        }
                    }
                }); 
            }
        });

    </script>
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <!-- Localización de los botones de información a la derecha. -->
            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Generar Cuenta Por Pagar</h2>
                <div class="col-sm-12">
                    <!--   estaba 10 -->
                    <div class="client-form contenedorForma col-sm-12"  style="padding: 0px;">
                        <!-- Formulario de comprobante PPTAL -->
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  onsubmit="return validar();"  action="json/registrar_GENERAR_CUENTA_PAGARJson.php">
                            <input type="hidden" value="obligacion" name="expedir">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                            </p>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;">
                                <!-- Primera Fila -->
                                <div class="col-sm-3" align="left">
                                    <!-- Tercero -->
                                    <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero">
                                    <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                    <select name="tercero" id="tercero" class="form-control input-sm select2_single" title="Seleccione un tipo de comprobante" style="width:180px;" >
                                        <?php
                                        $seleccionado = '';
                                        if (!empty($_SESSION['id_comp_pptal_CP']) && !empty($rowComp[14])) {
                                           echo '<option value="'.$rowComp[14].'">'.$rowComp[16].' - '.$rowComp[15].'</option>';
                                        } else {
                                            echo '<option value="">Tercero</option>';
                                        }
                                        while ($rowTerc = mysqli_fetch_row($tercero)) {
                                            if (in_array($rowTerc[7], $natural)) {                ?>
                                                <option value="<?php echo $rowTerc[0]; ?>">
                                                    <?php echo ucwords(mb_strtolower($rowTerc[1])) . ' ' . ucwords(mb_strtolower($rowTerc[2])) . ' ' . ucwords(mb_strtolower($rowTerc[3])) . ' ' . ucwords(mb_strtolower($rowTerc[4])) . ' ' . $rowTerc[6];?>
                                                </option>
                                            <?php } elseif (in_array($rowTerc[7], $juridica)) { ?>
                                                <option value="<?php echo $rowTerc[0]; ?>" >
                                                <?php echo ucwords(mb_strtolower($rowTerc[5])) . ' ' . $rowTerc[6]; ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!-- Fin Tercero -->
                                <div class="col-sm-3" align="left">
                                    <!-- Registro Presupuestal -->
                                    <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Registro Presupuestal:</label><br>
                                    <select name="solicitudAprobada" id="solicitudAprobada" class="select2_single form-control input-sm" title="Registro presupuestal" style="width:180px; height: 38px" >
                                        
                                        <?php if (empty($_SESSION['nuevo_CP']) && !empty($_SESSION['id_comp_pptal_CP'])) {
                                            echo '<option value="' . $id . '" >' . $numero . ' ' . $fecha . ' ' . ucwords(mb_strtolower($descripcion)) . '</option>';
                                            ?>
                                        <?php
                                        } else { ?>
                                        <option value="">Registro Presupuestal</option>
                                            <?php if (!empty($_SESSION['nuevo_CP']) && !empty($_SESSION['id_comp_pptal_CP'])) {

                                                if (!empty($terceroComp)) {
                                                    $queryComp = "SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
                                                                                 FROM gf_comprobante_pptal com
                                                                                 left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
                                                                                 WHERE tipoCom.clasepptal = 20 and com.tercero =  $terceroComp and com.parametrizacionanno= $anno";

                                                    $comprobanteP = $mysqli->query($queryComp);
                                                    while ($row = mysqli_fetch_row($comprobanteP)) {
                                                        $queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
                                                                             FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
                                                                             WHERE comP.id_unico = detComp.comprobantepptal 
                                                                             AND comP.id_unico = " . $row[0];

                                                        $saldDispo = 0;
                                                        $totalSaldDispo = 0;
                                                        $detCompro = $mysqli->query($queryDetCompro);
                                                        while ($rowDetComp = mysqli_fetch_row($detCompro)) {
                                                            $rowDetComp[1];
                                                            $queryDetAfetc = "SELECT valor   
                                                                                         FROM gf_detalle_comprobante_pptal   
                                                                                         WHERE comprobanteafectado = " . $rowDetComp[0];
                                                            $detAfect = $mysqli->query($queryDetAfetc);
                                                            $totalAfec = 0;
                                                            while ($rowDetAf = mysqli_fetch_row($detAfect)) {
                                                                $totalAfec += $rowDetAf[0];
                                                            }

                                                            $saldDispo = $rowDetComp[1] - $totalAfec;
                                                            $totalSaldDispo += $saldDispo;
                                                        }
                                                        $saldo = $totalSaldDispo;

                                                        if ($saldo > 0) {
                                                            $fecha_div = explode("-", $row[2]);
                                                            $anio = $fecha_div[0];
                                                            $mes = $fecha_div[1];
                                                            $dia = $fecha_div[2];
                                                            $fecha = $dia . "/" . $mes . "/" . $anio;

                                                            echo '<option value="' . $row[0] . '">' . $row[1] . ' ' . $fecha . ' ' . ucwords(mb_strtolower($row[3])) . ' $' . number_format($saldo, 2, '.', ',') . '</option>';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <?php
                                        //
                                        ?>
                                    </select>
                                </div>
                                <!-- Fin Solicitud aprobada -->
                                <div class="col-sm-3" align="left">
                                    <!-- Tipo Comprobante -->
                                    <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br>
                                    <select name="tipoComprobante" id="tipoComprobante" class="select2_single form-control input-sm" title="Tipo Comprobante" style="width:180px; height: 38px" required>
                                        <option value="" >Tipo Comprobante</option>
                                            <?php
                                            while ($rowTC = mysqli_fetch_row($tipoComPtal)) {
                                                ?>
                                            <option value="<?php echo $rowTC[0]; ?>"> <?php echo $rowTC[1] . ' - ' . ucwords(mb_strtolower($rowTC[2])) ?></option>
                                            <?php }
                                            ?>
                                    </select>
                                </div>
                                <!-- Fin Solicitud aprobada -->
                                <div class="col-sm-3" align="left" >
                                    <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Número Comprobante:</label><br>
                                    <input class="input-sm" onkeypress="return txtValida(event, 'num')" type="text" name="numReg" id="numReg" class="form-control" style="width:150px; height: 38px; margin-top: 0px; margin-bottom: 0px;" title="Número Comprobante" maxlength="50" 
                                           placeholder="Número Comprobante" readonly="readonly" value="<?php if (!empty($_SESSION['nuevo_CP'])) {
                                            echo $numero;
                                        } ?>"> <!---->
                                </div>
                                    <?php
                                    if (!empty($_SESSION['id_comp_pptal_CP']) /* && !empty($_SESSION['nuevo_CP']) */) {
                                        ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            var idComPtal = $("#idComPtal").val();
                                            var tipoDeComp = $("#tipoDeComp").val();
                                            var nombTercero = $("#nombTercero").val();
                                            $('#tipoComprobante > option[value="' + tipoDeComp + '"]').attr('selected', 'selected');
                                            $('#solicitudAprobada > option[value="' + idComPtal + '"]').attr('selected', 'selected');
                                            //$("#tipoComprobante").prop("disabled", true); //Deshabilitado
                                            $("#buscarReg").val("");
                                        });
                                    </script>
                                        <?php
                                    } else {
                                        ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#tipoComprobante").prop("disabled", false); //Deshabilitado}+}
                                            $("#tipoComprobante").val("");
                                            $('#solicitudAprobada').val("");
                                            $("#buscarReg").val("");

                                        });
                                    </script>
                                    <?php
                                        }
                                        ?>
                            </div>
                            <!-- Fin de la primera fila -->
                            <!-- Segunda fila -->
                            <div class="form-group form-inline col-sm-12" style="margin-top: -10px; margin-left: 0px; margin-bottom: 0px;">
                                <!-- Segunda Fila -->
                                <div class="col-sm-3" align="left">
                                    <label for="proyecto" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Proyecto:</label><br>
                                    <select name="proyecto" id="proyecto" class="select2_single form-control input-sm" title="Proyecto" style="width:180px; height: 38px" required="required">
                                        <?php if (!empty($rowComp[12])) { 
                                            echo '<option value="'.$rowComp[12].'">'.ucwords($rowComp[13]).'</option>';
                                            $rowpr1 = "SELECT id_unico, nombre FROM gf_proyecto WHERE id_unico != '$rowComp[12]' AND compania = $compania ORDER BY nombre ASC ";
                                            $rowpr1 = $mysqli->query($rowpr1);
                                        } else {
                                            $rowpr1 = "SELECT id_unico, LOWER(nombre) FROM gf_proyecto WHERE nombre ='Varios' AND compania = $compania  ORDER BY nombre ASC ";
                                            $rowpr1 = $mysqli->query($rowpr1);
                                            if(mysqli_num_rows($rowpr1)>0){
                                                $rowprv = mysqli_fetch_row($rowpr1);
                                                echo '<option value="'.$rowprv[0].'">'.ucwords($rowprv[1]).'</option>';
                                            } else {
                                                echo '<option value="">Proyecto</option>';
                                            }
                                            $rowpr1 = "SELECT id_unico, LOWER(nombre) FROM gf_proyecto WHERE compania = $compania  ORDER BY nombre ASC ";
                                            $rowpr1 = $mysqli->query($rowpr1);
                                        }
                                        while ($rowpr = mysqli_fetch_row($rowpr1)) {
                                            echo '<option value="'.$rowpr[0].'">'.ucwords($rowpr[1]).'</option>';
                                        } ?>
                                    </select>
                                </div>
                                
                                
                                <div class="col-sm-3" align="left">
                                    <!-- Tipo Contrato-->
                                    <label for="claseContrato" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Clase Contrato:</label><br>
                                    <select name="claseContrato" id="claseContrato" class="select2_single form-control input-sm" title="Clase Contrato" style="width:180px; height: 38px">
                                        <?php if (!empty($rowComp[9])) { ?>
                                            <option value="<?php echo $rowComp[9] ?>"><?php echo ucwords(mb_strtolower($rowComp[10])) ?></option>
                                            <?php
                                            $clasecon = "SELECT id_unico, nombre FROM gf_clase_contrato WHERE id_unico != '$rowComp[9]' ORDER BY nombre ASC ";
                                            $clasecon = $mysqli->query($clasecon);
                                            while ($rowcc = mysqli_fetch_row($clasecon)) {
                                                ?>
                                                <option value="<?php echo $rowcc[0] ?>"><?php echo ucwords(mb_strtolower($rowcc[1])) ?></option>
                                            <?php }
                                        } else { ?>
                                            <option value>Clase Contrato</option>
                                            <?php
                                            $clasecon = "SELECT id_unico, nombre FROM gf_clase_contrato ORDER BY nombre ASC ";
                                            $clasecon = $mysqli->query($clasecon);
                                            while ($rowcc = mysqli_fetch_row($clasecon)) {
                                                ?>
                                                <option value="<?php echo $rowcc[0] ?>"><?php echo ucwords(mb_strtolower($rowcc[1])) ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <!--  Numero -->
                                    <label class="control-label"><strong style="color:#03C1FB;"></strong>Número Contrato:</label> <br/>
                                    <?php if (!empty($rowComp[11])) { ?>
                                        <input class="form-control input-sm" type="text" name="numeroContrato" id="numeroContrato" style="width:180px; height: 38px" title="Ingrese Número Contrato" placeholder="Número Contrato" value="<?php echo $rowComp[11] ?>" >
                                    <?php } else { ?>
                                        <input class="form-control input-sm" type="text" name="numeroContrato" id="numeroContrato" style="width:180px; height: 38px" title="Ingrese Número Contrato" placeholder="Número Contrato">
                                    <?php } ?>
                                </div>
                                <div class="col-sm-3" align="left" >
                                    <label class="control-label"><strong style="color:#03C1FB;"></strong>Buscar Cuenta Por Pagar:</label> <br/>
                                    <select class="select2_single form-control" name="buscarReg" id="buscarReg" style="width:200px">
                                        <option value="">Cuenta por pagar</option>
                                    <?php
                                    $reg = "SELECT
                                         cp.id_unico,
                                         cp.numero,
                                         cp.fecha,
                                         tcp.codigo,
                                         IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                             OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                           (tr.razonsocial),
                                           CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                                         tr.numeroidentificacion
                                         FROM
                                         gf_comprobante_pptal cp
                                         LEFT JOIN
                                         gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                         LEFT JOIN
                                         gf_tercero tr ON cp.tercero = tr.id_unico 
                                         WHERE tcp.clasepptal = 16 AND tcp.tipooperacion=1  AND cp.parametrizacionanno = $anno 
                                         AND tcp.vigencia_actual ='1' ORDER BY cp.numero DESC";
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
                                            <option value="<?php echo $row1[0] ?>"><?php echo $row1[1] . ' ' . mb_strtoupper($row1[3]) . ' ' . $f . ' ' . ucwords(mb_strtolower($row1[4])) . ' ' . $row1[5] . $v ?>
                                            <?php } ?>
                                    </select>
                                    <input type="hidden" id="seleccionar">
                                </div>
                                <br/>
                            </div>
                            <div class="col-sm-12" style="margin-top: 0px; margin-bottom: 5px;">
                                <!--  Tercera fila -->
                                <div class="col-sm-3" align="left" style="margin-top:-20px">
                                    <!--  Fecha -->
                                    <label class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>
                                    <?php if (empty($_SESSION['nuevo_CP'])) { ?>
                                        <input required="required" class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:180px; height: 38px" 
                                               title="Ingrese la fecha" placeholder="Fecha" readonly="true">
                                           <?php } else { ?>
                                        <input required="required" class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:180px; height: 38px" 
                                               title="Ingrese la fecha" placeholder="Fecha"  readonly="true">
                                           <?php } ?>
                                </div>
                                <div class="col-sm-3" align="left" style="margin-top:-20px">
                                    <!--  DESCRIPCION -->
                                    <label class="control-label"><strong style="color:#03C1FB;"></strong>Descripción:</label> <br/>
                                    <textarea class="form-control input-sm" type="text" name="descripcion" id="descripcion" style="width:180px; height: 60px; margin-top:0px" title="Ingrese Descripción" placeholder="Descripción"  value="" ><?php echo (($descripcion)) ?></textarea>
                                </div>
                                
                                <div class="col-sm-1" style="margin-top: 0px;margin-left: 0px">
                                    <!-- Botón nuevo -->
                                    <button type="button" id="btnNuevo" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Nuevo" >
                                        <li class="glyphicon glyphicon-plus"></li>
                                    </button>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px;margin-left: -30px">
                                    <!-- Botón Guardar -->
                                    <button type="submit" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                    </button>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón Modifcar -->
                                    <button type="button" id="btnModificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-pencil"></li>
                                    </button>
                                    <script>
                                        $("#btnModificar").click(function(){
                                            var idpptal =$("#id_comp_pptal_CP").val();
                                            var idcnt = $("#compContable").val();
                                            var desc = $("#descripcion").val();
                                            var clasec = $("#claseContrato").val();
                                            var fecha = $("#fecha").val();
                                            var numc = $("#numeroContrato").val();
                                            var tercero =$("#tercero").val();
                                            var proyecto =$("#proyecto").val();
                                            var form_data={ action:1, pptal :idpptal, idcnt :idcnt, desc:desc, clasec:clasec, fecha:fecha, numc:numc, tercero:tercero, proyecto:proyecto};
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_cuenta_pagarJson.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                console.log(response);
                                                $("#mensajemodal").html(response);
                                                $("#modalmensajes").modal("show");
                                                $("#btnmodmsj").click(function(){
                                                    document.location.reload();
                                                })
                                            }
                                            });

                                        })
                                    </script>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón siguiente -->
                                    <button type="button" id="btnEnviar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Siguiente" >
                                        <li class="glyphicon glyphicon-arrow-right"></li>
                                    </button>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón imprimir -->
                                    <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                        <li class="fa fa-file-pdf-o"></li>
                                    </button>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón imprimir -->
                                    <button type="button" id="btnImprimirExcel" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                        <li class="fa fa-file-excel-o"></li>
                                    </button>
                                </div>
                                <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#btnImprimirExcel").click(function ()
                                    {
                                        
                                        var id_comp = $("#id_comp_pptal_CP").val();

                                        var form_data = {estruc: 16, id_comp: id_comp}
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_aplicar_retenciones.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response);
                                                if (response != 0)
                                                {
                                                    window.open('informesPptal/inf_Comp_Cuent_PagarExcel.php');
                                                } else
                                                {
                                                    $("#mdlErrNoCnt").modal('show');
                                                }
                                            }//Fin succes.
                                        }); //Fi
                                    });
                                });
                            </script>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón ver formulario registro_COMPROBANTE_CNT.php -->
                                    <button type="button" id="btnVerCnt" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Ver Comprobante Contable">
                                        <li class="glyphicon glyphicon-eye-open"></li>
                                    </button>
                                </div>
                                <div class="col-sm-1" style="margin-top: 0px; margin-left: -30px">
                                    <!-- Botón Eliminar -->
                                    <button type="button" id="btnEliminarPtal" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Eliminar el Comprobante Contable">
                                        <li class="glyphicon glyphicon-remove"></li>
                                    </button>
                                </div>
                                <div class="col-sm-2" style="margin-top: 8px; margin-left: 15px" >
                                    <button type="button" id="btnNuevoRegPre" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Agregar Registro Presupuestal">
                                        <li class="glyphicon glyphicon-plus"></li>
                                        Registro Presupuestal
                                    </button>
                                </div>
                                
                                <!-------------------ELIMINAR EN CASCADA-------------------------------->
                                    <div class="col-sm-1" style="margin-top: -13px;margin-left: 10px">
                                        <button type="button" id="btnEliCas" disabled="true" class="btn btn-primary sombra" style="background: #f60303; color: #fff; border-color: #f60303; ;margin:0 auto; margin-top: 20px; width: 70%" title="Eliminar Comprobantes en Cascada" >
                                            <img src="img/eliminar.png" style="width: 20px" >
                                        </button>                       
                                    </div>
                                    
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
                                                        var form_data = { estruc: 12, id:+$("#id_comp_pptal_CP").val() };
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
                                    <!-------------------BOTON ALMACEN-------------------------------->
                                    <div class="col-sm-1"  id="movAlmacen" name="movAlmacen" style="display:none; margin-left:-10px">
                                        <a id="btnAlmacen" class="btn btn-primary shadow col-sm-1 col-lg-1 glyphicon glyphicon-tags" style="width: 100%" onclick="open_modal_r()"></a>
                                    </div>
                                    <!-------------------FIN BOTON ALMACEN-------------------------------->
                                    <?php 
                                    if($nm>0){ 
                                    if (empty($_SESSION['nuevo_CP']) && empty($_SESSION['id_comp_pptal_CP'])) { ?>
                                    <div id="vigencia"  style="display:block">
                                    <?php }  elseif (!empty($_SESSION['nuevo_CP']) && !empty($_SESSION['id_comp_pptal_CP'])) { 
                                            #* Contar Detalles 
                                            $dt = detallesnumpptal($_SESSION['id_comp_pptal_CP']);
                                            if($dt>0){ ?>
                                            <div id="vigencia"  style="display:none">
                                            <?php } else { ?>        
                                            <div id="vigencia"   style="display:block">
                                                
                                            <?php } ?>    
                                           
                                    <?php } else { ?>
                                    <div id="vigencia"  style="display:none">
                                    <?php } } else { ?>    
                                    <div id="vigencia"   style="display:none">
                                    <?php } ?>    
                                        <br/>
                                        <label class="control-label"  align="left" style="width: 60px; margin-top: -0px"><strong style="color:#03C1FB;">*</strong>Vigencia Anterior:</label> 
                                        <input type="checkbox" id="vigencia" value="vigencia"/><br/>
                                        <input type="hidden" name="vigenciaa" id="vigenciaa">
                                        <script>
                                            $(document).on('change', 'input[type="checkbox"]', function (e) {
                                                if (this.id == "vigencia") {
                                                    var ter = $("#tercero").val();
                                                    if (this.checked){
                                                        if(ter==""){
                                                            $("#vigenciaa").val('0');
                                                            $("#vigencia input[type=checkbox]").prop('checked', false);
                                                            $("#mensaje").html('Seleccione Tercero');
                                                            $("#myModalError").modal("show");
                                                             $("#btnErrorModal").click(function(){
                                                                 $("#myModalError").modal("hide");
                                                            })
                                                            
                                                        } else {
                                                            $("#vigenciaa").val('1');
                                                           var opcion = '<option value="" >Registro</option>';
                                                            var form_data = {action:6, tercero: +$("#tercero").val()};
                                                            $.ajax({
                                                                type: "POST",
                                                                url: "jsonPptal/gf_cuenta_pagarJson.php",
                                                                data: form_data,
                                                                success: function (response)
                                                                {
                                                                    console.log(response);
                                                                    if (response == "" || response == 0)
                                                                    {
                                                                        var noHay = '<option value="N" >No hay Registros</option>';
                                                                        $("#solicitudAprobada").html(noHay).focus();
                                                                    } else
                                                                    {
                                                                        opcion += response;
                                                                        $("#solicitudAprobada").html(opcion).focus();
                                                                    }
                                                                }//Fin succes.
                                                            });
                                                        }
                                                    } else {
                                                        $("#vigenciaa").val('0');
                                                        if(ter==""){
                                                        } else {
                                                            var opcion = '<option value="" >Registro</option>';
                                                            var form_data = {id_tercero: +$("#tercero").val(), clase: 20};
                                                            $.ajax({
                                                                type: "POST",
                                                                url: "estructura_tercero_cuenta_pagar.php",
                                                                data: form_data,
                                                                success: function (response)
                                                                {
                                                                    console.log(response+'tercero');
                                                                    if (response == "" || response == 0)
                                                                    {
                                                                        var numReg = $('#numRegistros').val();
                                                                        if (numReg > 0) {
                                                                            var terCom = $("#terceroComp").val();
                                                                            var terSel = $("#tercero").val();
                                                                            var compCont = $("#compContable").val();



                                                                        }
                                                                        var noHay = '<option value="N" >No Hay Registro Presupuestal</option>';
                                                                        $("#solicitudAprobada").html(noHay).focus();

                                                                    } else
                                                                    {
                                                                        var numReg = $('#numRegistros').val();
                                                                        if (numReg > 0) {
                                                                            var terCom = $("#terceroComp").val();
                                                                            var terSel = $("#tercero").val();
                                                                            var compCont = $("#compContable").val();


                                                                        }
                                                                        opcion += response;
                                                                        $("#solicitudAprobada").html(opcion).focus();
                                                                    }
                                                                }//Fin succes.
                                            }); //Fin ajax.

                                                        }
                                                    }
                                                }
                                            });
                                        </script>  
                                        <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                  <div id="forma-modal" class="modal-header">
                                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                                  </div>
                                                  <div class="modal-body" style="margin-top: 8px">
                                                      <labe id="mensaje" name="mensaje" style="font-weight:light"></labe>
                                                  </div>
                                                  <div id="forma-modal" class="modal-footer">
                                                    <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                    Aceptar
                                                    </button>
                                                  </div>
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
                                                    <button type="button" id="btnmodmsjcan" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $("#eliminartodo").click(function(){
                                            var form_data ={estruc:13, id:+$("#id_comp_pptal_CP").val() };
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
                                                                var form_data ={estruc:14, id:+$("#id_comp_pptal_CP").val() };
                                                                $.ajax({
                                                                    type: "POST",
                                                                    url: "jsonPptal/consultas.php",
                                                                    data: form_data,
                                                                    success: function(response)
                                                                    {
                                                                        jsRemoveWindowLoad();
                                                                        console.log(response);
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
                                    
                                    <!-------------------FIN ELIMINAR EN CASCADA-------------------------------->
                                    
                            </div>
                            <!-- Fin tercera fila -->
                            <!--TRAER UN NUMERO CUANDO ESTA VACIO-->
                            <script>
                                $("#tipoComprobante").change(function ()
                                {

                                <?php if (empty($_SESSION['nuevo_CP'])) { ?>
                                        var registro = $("#solicitudAprobada").val();
                                        var tipo = $("#tipoComprobante").val();
                                        var form_data = {action: 4, tipo: tipo}
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_comprobantesJson.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                response = JSON.parse(response);
                                                console.log(response+'AAAAA');

                                                $("#numReg").val(response);
                                                $("#fecha").val("");
                                            }
                                        });

                                    <?php } ?>

                                })

                            </script>  
                            <script>
                                $(document).ready(function ()
                                {
                                    $("#fecha").change(function ()
                                    {
                                        //VALIDAR SI YA TUVO CIERRE LA FECHA
                                        var fecha = $("#fecha").val();
                                        var form_data = {case: 4, fecha: fecha};

                                        $.ajax({
                                            type: "POST",
                                            url: "jsonSistema/consultas.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response);
                                                if (response == 1) {
                                                    $("#periodoC").modal('show');
                                                } else {

                                                    fecha1();
                                                }
                                            }
                                        });
                                    });
                                });
                            </script>
                            <script>
                                function fecha1() { 
                                    var id_com = $("#id_comp_pptal_CP").val();
                                    <?php if (!empty($_SESSION['nuevo_CP']) && !empty($_SESSION['id_comp_pptal_CP'])) { ?>
                                        var comp = $("#id_comp_pptal_CP").val();
                                        var fecha = $("#fecha").val();
                                        var num = $('#numReg').val();
                                        var form_data = {estruc: 7, comp: comp, fecha: fecha, num: num};
                                        $.ajax({
                                            type: "POST",
                                            url: "consultasBasicas/validarFechas.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response);
                                                if (response == 2)
                                                {
                                                    $("#myModalAlertErrFec").modal('show');
                                                    $("#AceptErrFec").click(function ()
                                                    {
                                                        var fechaA = $("#fechaA").val();
                                                        $("#fecha").val('');
                                                        $("#AceptErrFec").modal('hide');
                                                    });

                                                } 
                                            }
                                        });
                                   <?php } else {  ?>
                                        var tipComPal = $("#tipoComprobante").val();
                                    if (tipComPal == '') {
                                        $("#mdlSelTCF").modal('show');
                                        $("#ErrormdlSelTCF").click(function ()
                                        {
                                            $("#fecha").val("");
                                            $("#mdlSelTCF").modal('hide');
                                        });
                                    } else {
                                        var comp = $("#solicitudAprobada").val();
                                        var fecha = $("#fecha").val();
                                        var num = $("#numReg").val();
                                        var form_data = {estruc: 6, tipComPal: tipComPal, comp: comp, fecha: fecha, num: num};

                                        $.ajax({
                                            type: "POST",
                                            url: "consultasBasicas/validarFechas.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response);
                                                if (response == 1)
                                                {
                                                    $("#myModalAlertErrFec").modal('show');
                                                    $("#AceptErrFec").click(function ()
                                                    {
                                                        $("#fecha").val("");
                                                        $("#myModalAlertErrFec").modal('hide');
                                                    });

                                                }
                                            }
                                        });
                                    }
                                   <?php  } ?>
                                    
                                }
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
                            <script>
                                $("#periodoCA").click(function () {
                                    $("#fecha").val("").focus();
                                })
                            </script>
                            <div class="modal fade" id="mdlSelTCF" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <p>Seleccione Tipo Comprobante </p>
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="ErrormdlSelTCF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <p>La fecha es inválida. Verifique nuevamente.</p>
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                Aceptar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#btnImprimir").click(function ()
                                    {
                                        
                                        var id_comp = $("#id_comp_pptal_CP").val();

                                        var form_data = {estruc: 16, id_comp: id_comp}
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_aplicar_retenciones.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response);
                                                window.open('informesPptal/inf_Comp_Cuent_Pagar.php');
                                                /*
                                                if (response != 0)
                                                {
                                                    window.open('informesPptal/inf_Comp_Cuent_Pagar.php');
                                                } else
                                                {
                                                    $("#mdlErrNoCnt").modal('show');
                                                }*/
                                            }//Fin succes.
                                        }); //Fi
                                    });
                                });
                            </script>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#btnVerCnt").click(function ()
                                    {
                                        var id_comp = $("#id_comp_pptal_CP").val();

                                        var form_data = {estruc: 16, id_comp: id_comp}
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_aplicar_retenciones.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                if (response != 0)
                                                {
                                                    document.location = 'registro_COMPROBANTE_CNT.php'; //Dejar esta siempre.
                                                    //window.open('registro_COMPROBANTE_CNT.php'); // Usar para probar.
                                                } else
                                                {
                                                    $("#mdlErrNoCnt").modal('show');
                                                }
                                            }//Fin succes.
                                        }); //Fi
                                    });
                                });
                            </script>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#btnEliminarPtal").click(function ()
                                    {
                                        var id_comp = $("#id_comp_pptal_CP").val();

                                        var form_data = {estruc: 1, id_com: id_comp}
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_modificar_eliminar_pptal.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                response = parseInt(response);
                                                if (response == 0)
                                                {
                                                    $("#mdlDeseaElimPtal").modal('show');
                                                } else
                                                {
                                                    $("#mdlNoPuedeElim").modal('show');
                                                }

                                            }//Fin succes.
                                        }); //Fi

                                    });
                                });
                            </script>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#btnNuevoRegPre").click(function ()
                                    {
                                        jsShowWindowLoad('Agregando Registro...');
                                        $("#btnNuevoRegPre").prop("disabled", true);
                                        if ($("#solicitudAprobada").val() != "")
                                        {
                                            var id_comp = $("#solicitudAprobada").val();
                                            var id_comp_act = $("#id_comp_pptal_CP").val();
                                            var fecha = $("#fecha").val();
                                            var tercero = $("#tercero").val();
                                            var proyecto = $("#proyecto").val();
                                            if (id_comp != id_comp_act)
                                            {
                                                if($("#vigenciaa").val()==1){
                                                    var form_data = {action: 7, id_comp: id_comp, fecha: fecha, tercero: tercero, proyecto:proyecto}
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "jsonPptal/gf_cuenta_pagarJson.php",
                                                        data: form_data,
                                                        success: function (response)
                                                        {
                                                            jsRemoveWindowLoad();
                                                            console.log(response);
                                                            if (response == 1)
                                                            {
                                                                $("#mdlExitoRetenAnadida").modal('show');
                                                            } else if (response == 0)
                                                            {
                                                                $("#mdlErrNoAnadeRetenc").modal('show');
                                                            } else if (response == 2)
                                                            {
                                                                $("#mdlFechaMayor").modal('show');
                                                            }
                                                        }//Fin succes.
                                                    });
                                                } else {
                                                    var form_data = {estruc: 20, id_comp: id_comp, fecha: fecha, tercero: tercero}
                                                    $.ajax({
                                                    type: "POST",
                                                    url: "estructura_aplicar_retenciones.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        jsRemoveWindowLoad();
                                                        console.log(response);
                                                        if (response == 1)
                                                        {
                                                            $("#mdlExitoRetenAnadida").modal('show');
                                                        } else if (response == 0)
                                                        {
                                                            $("#mdlErrNoAnadeRetenc").modal('show');
                                                        } else if (response == 2)
                                                        {
                                                            $("#mdlFechaMayor").modal('show');
                                                        }
                                                    }//Fin succes.
                                                }); 
                                                }
                                            } else
                                            {
                                                jsRemoveWindowLoad();
                                                $("#mdlNoDebenSerIguales").modal('show');
                                            }
                                        } else
                                        {
                                            jsRemoveWindowLoad();
                                            $("#mdlNoRetenSelec").modal('show');
                                        }

                                    });
                                });
                            </script>
                            <script type="text/javascript">
                                // Al dar click fuera del input buscar se limpia el input y se oculta el div de resultados.
                                $(document).ready(function () {

                                    $(document).click(function (e) {
                                        if (e.target.id != 'buscarReg')
                                            //$('#buscarReg').val('');
                                            $('#listado').fadeOut();
                                    });

                                });

                            </script>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#buscarReg").change(function (event)
                                    {
                                        console.log('lllego');
                                        if (($("#buscarReg").val() != "") && ($("#buscarReg").val() != 0))
                                        {

                                            traerNum();

                                        }
                                    });
                                });

                            </script>
                            <script type="text/javascript">
                                function traerNum()
                                {

                                    var form_data = {sesion: 'id_comp_pptal_CP', nuevo: 'nuevo_CP', numero: $("#buscarReg").val(), valN: 1}
                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_seleccionar_pptal.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            if (response == 1)
                                            {
                                                document.location='GENERAR_CUENTA_PAGAR.php';
                                            }

                                        }
                                    }); //Fi
                                }

                            </script>
                            <script type="text/javascript">// Evalúa que la fecha inicial no sea inferior a la fecha inicial del comprobante predecesor.
                                $(document).ready(function () {
                                    $("#solicitudAprobada").click(function ()
                                    {
                                        var tercero = $("#tercero").val();
                                        if (tercero == 0 || tercero == '')
                                        {
                                            $("#mdlAlertTercero").modal('show');
                                        }
                                    });
                                });
                            </script>
                            <!-- Script para cargar datos en el combo select Rubro a partir del lo que se seleccione en el combo select Concepto. -->
                            
                            <script type="text/javascript"></script>
                            <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.
                                $(document).ready(function ()
                                {
                                    $("#tercero").change(function ()
                                    {                                  

                                    <?php if (empty($_SESSION['nuevo_CP']) && empty($_SESSION['id_comp_pptal_CP'])) { ?>
                                        var opcion = '<option value="" >Registro Presupuestal</option>';

                                        if (($("#tercero").val() == "") || ($("#tercero").val() == 0))
                                        {
                                            $("#solicitudAprobada").html(opcion);
                                        } else
                                        {
                                            var form_data = {id_tercero: +$("#tercero").val(), clase: 20};
                                            $.ajax({
                                                type: "POST",
                                                url: "estructura_tercero_cuenta_pagar.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'tercero');
                                                    if (response == "" || response == 0)
                                                    {
                                                        var numReg = $('#numRegistros').val();
                                                        if (numReg > 0) {
                                                            var terCom = $("#terceroComp").val();
                                                            var terSel = $("#tercero").val();
                                                            var compCont = $("#compContable").val();



                                                        }
                                                        var noHay = '<option value="N" >No hay registro presupuestal</option>';
                                                        $("#solicitudAprobada").html(noHay).focus();

                                                    } else
                                                    {
                                                        var numReg = $('#numRegistros').val();
                                                        if (numReg > 0) {
                                                            var terCom = $("#terceroComp").val();
                                                            var terSel = $("#tercero").val();
                                                            var compCont = $("#compContable").val();


                                                        }
                                                        opcion += response;
                                                        $("#solicitudAprobada").html(opcion).focus();
                                                    }
                                                }//Fin succes.
                                            }); //Fin ajax.

                                        } //Cierre else.            
                                                    
                                                    
                                    <?php } else { ?>
                                        
                                       
                                        var opcion = '<option value="" >Registro Presupuestal</option>';

                                        if (($("#tercero").val() == "") || ($("#tercero").val() == 0))
                                        {
                                            $("#solicitudAprobada").html(opcion);
                                        } else
                                        {
                                            var form_data = {id_tercero: +$("#tercero").val(), clase: 20};
                                            $.ajax({
                                                type: "POST",
                                                url: "estructura_tercero_cuenta_pagar.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'aaa');
                                                    if (response == "" || response == 0)
                                                    {
                                                        var numReg = $('#numRegistros').val();
                                                        if (numReg > 0) {
                                                            var terCom = $("#terceroComp").val();
                                                            var terSel = $("#tercero").val();
                                                            var compCont = $("#compContable").val();



                                                        }
                                                        var noHay = '<option value="N" >No hay registro presupuestal</option>';
                                                        $("#solicitudAprobada").html(noHay).focus();

                                                    } else
                                                    {
                                                        var numReg = $('#numRegistros').val();
                                                        if (numReg > 0) {
                                                            var terCom = $("#terceroComp").val();
                                                            var terSel = $("#tercero").val();
                                                            var compCont = $("#compContable").val();


                                                        }
                                                        opcion += response;
                                                        $("#solicitudAprobada").html(opcion).focus();
                                                    }
                                                }//Fin succes.
                                            }); //Fin ajax.

                                        } //Cierre else.
                                        
                                            
                                    <?php } ?>   
                                        
                                    
                                    });//Cierre change.
                                });//Cierre Ready.

                            </script> <!-- Código JS para asignación -->
                            <!-- El número de solicitud seleccionado -->
                            <input name="numero" type="hidden" value="<?php echo $numero; ?>">
                            <input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->
                            <input type="hidden" name="MM_insert" >
                        </form>
                        <?php
                        if (empty($_SESSION['nuevo_CP'])) {
                            ?>
                            <!-- Al seleccionar un número de solcitud, cargará  --> 
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#solicitudAprobada").change(function ()
                                    {
                                        if (($("#solicitudAprobada").val() == "") || ($("#solicitudAprobada").val() == 0))
                                        {
                                            var form_data = {estruc: 1, sesion: 'id_comp_pptal_CP', nuevo: 'nuevo_CP'}; //Estructura Uno 
                                            $.ajax({
                                                type: "POST",
                                                url: "estructura_sesiones.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    document.location.reload();
                                                }//Fin succes.
                                            }); //Fin ajax.
                                        } else if ($("#solicitudAprobada").val() != "N")
                                        {
                                            if($("#vigenciaa").val()==1){
                                                
                                            } else {
                                                var form_data = {estruc: 2, id_comp: +$("#solicitudAprobada").val(), sesion: 'id_comp_pptal_CP', nuevo: 'nuevo_CP'}; //Estructura Dos 
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_sesiones.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        document.location.reload();
                                                    }//Fin succes.
                                                }); //Fin ajax.
                                            }
                                        } //Cierre else.              
                                    });//Cierre change.

                                });//Cierre Ready.

                            </script> <!-- Fin de recargar la página al seleccionar Solicitud nueva -->
                                <?php
                            }
                            ?>
                        <script type="text/javascript">
                            $(document).ready(function ()
                            {
                                $("#btnNuevo").click(function ()
                                {
                                    var form_data = {estruc: 1, sesion: 'id_comp_pptal_CP', nuevo: 'nuevo_CP'}; //Estructura Uno 
                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_sesiones.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            document.location.reload();
                                        }//Fin succes.
                                    }); //Fin ajax.
                                });
                            });


                        </script>
                    </div>
                    <!-- Cierra clase client-form contenedorForma -->
                </div>
                <!-- Cierra col-sm-10 -->
                <?php
                if (!empty($_SESSION['id_comp_pptal_CP'])) {
                    ?>
                    <script type="text/javascript">
                        $("#btnGuardar").prop("disabled", false);
                        $("#btnEliCas").prop("disabled", true);
                        $("#btnModificar").prop("disabled", true);

                    </script>
                        <?php
                    } else {
                    }
                    if (!empty($_SESSION['nuevo_CP'])) {
                        ?>
                    <script type="text/javascript">
                        $("#btnGuardar").prop("disabled", true);
                        $("#btnEliCas").prop("disabled", false);
                        $("#btnModificar").prop("disabled", false);
                        $("#btnNuevoRegPre").prop("disabled", false);
                    </script>
                        <?php
                    } else {
                        ?>
                    <script type="text/javascript">
                        $("#tipocomprobante").prop("disabled", false);
                        $("#btnVerCnt").prop("disabled", true);
                        $("#btnModificar").prop("disabled", true);
                        $("#btnNuevoRegPre").prop("disabled", true);
                        /**/$("#btnImprimir").prop("disabled", true);
                        $("#btnImprimirExcel").prop("disabled", true);
                        
                        $("#btnEliminarPtal").prop("disabled", true);
                        $("#btnEnviar").prop("disabled", true);
                    </script>
                        <?php
                    }
                    ?>
                <!-- select2 -->
                <script type="text/javascript" src="js/select2.js"></script>
                <script>
                    $(document).ready(function () {
                        $(".select2_single").select2({
                            allowClear:true
                        }); 


                        $(".chosen-select").chosen({max_selected_options: 0});
                        $(".chosen-container").bind('keypress',function(e) {
                            let term = e.target.value;
                             let form_data4 = {action: 8, term: term};
                        
                             $.ajax({
                                 type:"POST",
                                 url:"jsonPptal/gf_tercerosJson.php",
                                 data:form_data4,
                                 success: function(data){
                                     let option = '<option value=""> - </option>';
                                     //console.log(data);
                                      option = data+option;
                                      $("#tercero").html(option);
                                      $("#tercero").trigger("chosen:updated");
                                     
                                 }
                             }); 
                             e.target.value=term;

                         });

                        $('.select2-input').on("keyup", function(event) {
                              var ter=$("#tercero").val();
                              var num=$("#numReg").val();
                              console.log(ter+'ss'+num);
                            if (ter=="" && num=="") {
                                let term = event.currentTarget.value;
                                 let form_data4 = {action: 8, term: term};

                            $.ajax({
                                type:"POST",
                                url:"jsonPptal/gf_tercerosJson.php",
                                data:form_data4,
                                success: function(data){
                                    let option = '<option value=""> - </option>';
                                    //console.log(data);
                                     console.log('PASOF');
                                     option = option+data;
                                    $("#tercero").html(option);

                                }
                            }); 
                        }else if(ter!="" && num!=""){
                            let term = event.currentTarget.value;
                                 let form_data4 = {action: 8, term: term};

                            $.ajax({
                                type:"POST",
                                url:"jsonPptal/gf_tercerosJson.php",
                                data:form_data4,
                                success: function(data){
                                    let option = '<option value=""> - </option>';
                                    //console.log(data);
                                     console.log('PASOF');
                                     option = option+data;
                                    $("#tercero").html(option);

                                }
                            }); 

                        }
                        });
                           $('.select2-close').on("keypress", function(event) {
    
                              console.log('SIUU');
                           });
                    });

                </script>

                <script>
                    function llenar() {
                        var tercero = document.getElementById('tercero').value;
                        document.getElementById('terceroB').value = tercero;
                    }
                </script>
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <!-- Listado de registros -->
                <div class="table-responsive contTabla col-sm-12" style="margin-top: 5px;">
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Concepto</strong></td>
                                    <td class="cabeza"><strong>Rubro Presupuestal</strong></td>
                                    <td class="cabeza"><strong>Fuente</strong></td>                                    
                                    <?php if($tipocomp==2){
                                        echo '<td class="cabeza"><strong>Centro Costo</strong></td>';
                                    } ?>
                                    <td class="cabeza"><strong>Tercero</strong></td>
                                    <td class="cabeza"><strong>Valor Aprobado</strong></td>
                                    <td class="cabeza"><strong>Cuenta Débito</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto</th>
                                    <th>Rubro Presupuestal</th>
                                    <th>Fuente</th>
                                    <?php if($tipocomp==2){
                                        echo '<th>Centro Costo</th>';
                                    } ?>
                                    <th>Tercero</th>
                                    <th>Valor Aprobado</th>
                                    <th>Cuenta Débito</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($_SESSION['id_comp_pptal_CP']) && ($resultado == true)) {
                                $valorTotal = 0;
                                $debitoCP = array();

                                while ($row = mysqli_fetch_row($resultado)) {
                                    $valorTotal += $row[2];
                                    $valorPpTl = $row[2];
                                    ?>
                                        <tr>
                                            <td class="oculto"><?php echo $row[0] ?>
                                                <input  id="id_det_com<?php echo $row[0]; ?>" type="hidden" value="<?php echo $row[0]; ?>" >
                                            </td>
                                            <td class="campos" >
                                        <?php
                                        if (!empty($_SESSION['id_comp_pptal_CP']) && !empty($_SESSION['nuevo_CP'])) {
                                            ##BUSCAR FECHA COMPROBANTE 
                                            $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = " . $_SESSION['id_comp_pptal_CP'];
                                            $fc = $mysqli->query($fc);
                                            $fc = mysqli_fetch_row($fc);
                                            $fc = $fc[0];
                                            ##DIVIDIR FECHA
                                            $fecha_div = explode("-", $fc);
                                            $anio = $fecha_div[0];
                                            $mes = $fecha_div[1];
                                            $dia = $fecha_div[2];

                                            ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
                                            $ci = "SELECT
                                cp.id_unico
                                FROM
                                gs_cierre_periodo cp
                                LEFT JOIN
                                gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
                                LEFT JOIN
                                gf_mes m ON cp.mes = m.id_unico
                                WHERE
                                pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 AND cp.anno = $anno";
                                            $ci = $mysqli->query($ci);
                                            if (mysqli_num_rows($ci) > 0) {
                                                ?>

                                                        <?php
                                                    } else {
                                                        $afecd = afect($row[0]);
                                                        if ($afecd == 1) {
                                                            
                                                        } else {
                                                            ?> 

                                                            <!-- Botones modificar y eliminar -->
                                                            <a class="eliminar"  href="#<?php echo $row[0]; ?>" onclick="javascript:eliminarDetComp(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                            </a>
                                                            <a class="modificar"  href="#<?php echo $row[0]; ?>" onclick="javascript:modificarDetComp(<?php echo $row[0]; ?>);" ><i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                            </a>
                                                        <?php }
                                                    }
                                                } ?>

                                            </td>
                                            <td class="campos" align="left">
                                                <div class="acotado">
                                                    <?php echo ucwords(mb_strtolower($row[10])); ?>
                                                </div>
                                            </td>
                                            <td class="campos" align="left" >
                                                <!-- Rubro presupuestal -->
                                                <div class="acotado">
                                                    <?php echo (ucwords(mb_strtolower($row[5] . ' - ' . $row[1]))); ?>
                                                </div>
                                            </td>
                                            <td class="campos" align="left">
                                                <div class="acotado">
                                                    <?php echo ucwords(mb_strtolower($row[4])); ?>
                                                </div>
                                            </td>
                                            <?php if($tipocomp==2){
                                                echo '<td class="campos" align="left"><div class="acotado">';
                                                echo ucwords(mb_strtolower($row[6]));
                                                echo '</div></td>';
                                                
                                            
                                            } ?>
                                            <td class="campos" align="left"><div class="acotado">
                                                <?php echo ucwords(mb_strtolower($row[8])).' - '.$row[9];?>
                                            </div></td>
                                            <td class="campos" align="right" style="padding: 0px">
                                                <!-- Valor aprobado -->
                                                <input type="hidden" id="valOcul<?php echo $row[0]; ?>"  value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>">
                                                <div id="divVal<?php echo $row[0]; ?>" class="divValor" style="margin-right: 10px;">
                                                    <?php
                                                    echo number_format($row[2], 2, '.', ',');
                                                    ?>
                                                </div>
                                                <!-- Modificar los valores -->
                                                <table id="tab<?php echo $row[0]; ?>" style="padding: 0px; background-color: transparent; background:transparent; margin: 0px;">
                                                    <tr>
                                                        <td style="padding: 0px;">
                                                            <input type="text" name="valorMod" id="valorMod<?php echo $row[0]; ?>" maxlength="50" style="margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valorMod<?php echo $row[0]; ?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0]; ?>');" value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>" required>
                                                        </td>
                                                        <td style="padding: 3px;">
                                                            <!-- Botón guardar lo modificado. -->
                                                            <a href="#<?php echo $row[0]; ?>" onclick="javascript:verificarValor('<?php echo $row[0]; ?>', '<?php echo $row[3]; ?>');" >
                                                                <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                                            </a> 
                                                        </td>
                                                        <td style="padding: 3px;">
                                                            <!-- Botón cancelar modificación -->
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
                                            <!-- Fin celda Valor aprobado -->
                                            <td class="campos" align="right" >
                                                <!-- Saldo por pagar -->
                                                <div class="anchoT"></div>
                                                <?php
                                                $queryCuenDeb = "SELECT cuen.id_unico, cuen.codi_cuenta, cuen.nombre, cc.codi_cuenta, cc.nombre , conRubCun.id_unico
                                                                         FROM gf_cuenta cuen 
                                                                         LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                                                         LEFT JOIN gf_concepto_rubro conRub ON conRub.id_unico = conRubCun.concepto_rubro 
                                                                         LEFT JOIN gf_rubro_pptal rub ON rub.id_unico = conRub.rubro 
                                                                         LEFT JOIN gf_rubro_fuente rubFue ON rubFue.rubro = rub.id_unico 
                                                                         LEFT JOIN gf_cuenta cc ON conRubCun.cuenta_credito = cc.id_unico 
                                                                         WHERE rubFue.id_unico = $row[3]";

                                                $cuentaDeb = $mysqli->query($queryCuenDeb);
                                                $cuentaDebRow = $mysqli->query($queryCuenDeb);

                                                $rowCDPrimer = mysqli_fetch_row($cuentaDeb);
                                                $idCP = (int) $row[0];
                                                $idPrimerDebito = (int) $rowCDPrimer[5];
                                                $debitoCP[$idCP] = $idPrimerDebito;
                                                ?>
                                                <input type="hidden" id="cuenDebOc">
                                                <select name="cuenDeb" id="cuenDeb<?php echo $row[0]; ?>" onchange="javascript:cambiarVector(<?php echo $row[0]; ?>);" class="form-control input-sm" title="Seleccione una Cuenta Débito" style="width:150px;" required >
                                                    <!--  <option value=""> Cuenta Débito </option> -->
                                                <?php
                                                //echo $row[3];
                                                while ($rowCD = mysqli_fetch_row($cuentaDebRow)) {
                                                    echo '<option value="' . $rowCD[5] . '">' . $rowCD[1] . ' ' . $rowCD[2] . ' - ' . $rowCD[3] . ' ' . $rowCD[4] . '</option>';
                                                }
                                                ?>
                                                </select>
                                                <script type="text/javascript">
                                                    $(document).ready(function ()
                                                    {
                                                        var valorVal = $("#cuenDeb<?php echo $row[0]; ?>").val();


                                                        $("#cuenDeb").change(function () {
                                                            var cuenDeb = $("#cuenDeb").val();
                                                            $("#cuenDebOc").val(cuenDeb);
                                                        });

                                                    });
                                                </script>
                                            </td>
                                            <!-- Saldo por pagar -->
                                        </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                            </tbody>
                        </table>
                            <?php
                            if (!empty($debitoCP)) {
                                $debitoCP_serial = serialize($debitoCP);
                                $_SESSION['debitoCP'] = $debitoCP_serial;
                            }
                            ?>
                        <script type="text/javascript"> //Modifica vector de débito
                            function cambiarVector(id, cuentaC)
                            {
                                var idCuenDeb = 'cuenDeb' + id;
                                var valor = $("#" + idCuenDeb).val();
                                var ctaC = cuentaC
                                var form_data = {estruc: 12, id: id, valor: valor, ctaC: ctaC};
                                $.ajax({
                                    type: "POST",
                                    url: "estructura_aplicar_retenciones.php",
                                    data: form_data,
                                    success: function (response)
                                    {
                                        console.log(response);
                                    }
                                });
                            }

                        </script>
                        <div class="col-sm-12" style="margin-top:5px; padding: 0px;" >
                            <div class="valorT" style="font-size: 12px;" align="right">
                                <span style="margin-right: 10px;"> Valor Total Aprobado:  </span>
                                <label style="margin-right: 10px;">
                                    <?php
                                    if (!empty($valorTotal)) {
                                        echo number_format($valorTotal, 2, '.', ',');
                                    }
                                    ?>
                                </label>
                                <input type="hidden" id="valorTotal"> 
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $("#valorTotal").val('<?php if (!empty($valorTotal)) {
                                        echo $valorTotal;
                                    } ?>');
                                    });
                                </script>
                            </div>
                        </div>
                                    <?php
                                    if (!empty($_SESSION['nuevo_CP'])) {
                                        ?>
                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                   // cambiar();
                                });


                                function cambiar()
                                {
                                    var elemento = $(".anchoT");
                                    var posicion = elemento.position();
                                    $(".valorT").width(posicion.left);

                                }

                            </script>
                                <?php
                            }
                            ?>
                            <?php
                            if (!empty($_SESSION['nuevo_CP'])) {
                                ?>
                            <script type="text/javascript">
                                var idCompPtal = $("#idComPtal").val();

                                var form_data = {estruc: 11, idCompPtal: idCompPtal};
                                $.ajax({
                                    type: "POST",
                                    url: "estructura_aplicar_retenciones.php",
                                    data: form_data,
                                    success: function (response)
                                    {
                                        console.log('cnt');
                                        console.log(response);

                                        response = parseInt(response);
                                        $("#compContable").val(response);

                                        var form_data = {estruc: 1, id_com: idCompPtal};
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_modificar_eliminar_pptal.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                response = parseInt(response);
                                                $("#compEgreso").val(response);

                                                var compContable = $("#compContable").val();
                                                var compEgreso = $("#compEgreso").val();
                                                var numRegistros = $("#numRegistros").val();
                                                if (compContable == 0)
                                                {
                                                    if (numRegistros > 0) {
                                                        $("#btnEnviar").prop("disabled", false);
                                                    } else {
                                                        $("#btnEnviar").prop("disabled", true);
                                                    }
                                                    $("#btnVerCnt").prop("disabled", true);
                                                    $("#btnImprimir").prop("disabled", true);
                                                    $("#btnImprimirExcel").prop("disabled", true);
                                                    
                                                    $("#btnNuevoRegPre").prop("disabled", false);
                                                } else
                                                {
                                                    $("#btnEnviar").prop("disabled", true);
                                                    $("#btnVerCnt").prop("disabled", false);
                                                    $("#btnImprimir").prop("disabled", false);
                                                    $("#btnImprimirExcel").prop("disabled", false);
                                                    $("#btnNuevoRegPre").prop("disabled", true);
                                                }

                                                if (compEgreso != 0)
                                                {
                                                    $("#btnEliminarPtal").prop("disabled", true);
                                                    $("#btnModificar").prop("disabled", true);
                                                } else if (compContable != 0)
                                                {
                                                    $("#btnEliminarPtal").prop("disabled", false);
                                                    
                                                } else if (compContable == 0)
                                                {
                                                    $("#btnEliminarPtal").prop("disabled", true);
                                                    
                                                }

                                            }
                                        });

                                    }
                                });



                            </script>
                                <?php
                            }
                            ?>
                        <script type="text/javascript">
                            $(document).ready(function ()
                            {
                                $('#btnEnviar').click(function ()
                                {
                                    jsShowWindowLoad('Guardando...');
                                    if($("#moviEscogidos").val()==""){
                                        jsRemoveWindowLoad();
                                        enviar();
                                    } else {
                                        /****Validar Configuración****/
                                        var form_data ={action:5 , mova:$("#moviEscogidos").val()};
                                        $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_cuenta_pagarJson.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                jsRemoveWindowLoad();
                                                resultado = JSON.parse(response);
                                                var data = resultado["rs"];
                                                var msj = resultado["respuesta"];
                                                if(data>0){
                                                    $("#mensajemodal").html(msj);
                                                    $("#modalmensajes").modal("show");
                                                    $("#btnmodmsj").click(function(){
                                                        $("#modalmensajes").modal("hide");
                                                    }); 
                                                    $("#btnmodmsjcan").click(function(){
                                                        $("#modalmensajes").modal("hide");
                                                    })
                                                } else {
                                        
                                                    var mov =$("#moviEscogidos").val();
                                                    var idp =$("#id_comp_pptal_CP").val();
                                                    var form_data ={action:4 , mov:mov, idp:idp};
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "jsonPptal/gf_cuenta_pagarJson.php",
                                                        data: form_data,
                                                        success: function(response)
                                                        {
                                                            console.log(response);
                                                            if(response==1) {
                                                                $("#mensajemodal").html("El Valor De La Cuenta Y De Los Movimientos De Almacen Escogidos Son Diferentes, ¿Desea Continuar?");
                                                                $("#modalmensajes").modal("show");
                                                                $("#btnmodmsj").click(function(){
                                                                    enviar();
                                                                }); 
                                                                $("#btnmodmsjcan").click(function(){
                                                                    $("#modalmensajes").modal("hide");
                                                                })
                                                            } else {
                                                                if(response==2){
                                                                    $("#mensajemodal").html("El Valor De Los Movimientos Escogidos No Puede Ser Mayor Al De La Cuenta Por Pagar");
                                                                    $("#modalmensajes").modal("show");
                                                                    $("#btnmodmsj").click(function(){
                                                                        $("#modalmensajes").modal("hide");
                                                                    }); 
                                                                    $("#btnmodmsjcan").click(function(){
                                                                        $("#modalmensajes").modal("hide");
                                                                    })
                                                                } else {
                                                                    enviar();
                                                                }
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        })
                                    }
                                   
                                });
                            });

                        </script>
                        <script>
                              function enviar(){
                                  jsShowWindowLoad('Guardando...');
                                  //VALIDAR SI EL TIPO TIENE RETENCION //
                                   var form_data = {case: 24, tipo: $("#tipoComprobante").val()};
                                    $.ajax({ 
                                        type: "POST",
                                        url: "consultasBasicas/busquedas.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            jsRemoveWindowLoad();
                                            console.log(response);
                                            if (response == 1)
                                            {
                                                var valorTot = $("#valorTotal").val();
                                                var idCompPtal = $("#idComPtal").val();
                                                var movAlmacen = $("#moviEscogidos").val();
                                                var tercero = $("#tercero").val();
                                                var form_data = {estruc: 11, valorTot: valorTot, idCompPtal: idCompPtal};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_aplicar_retenciones.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        console.log(response);
                                                        if (response == 0)
                                                        {
                                                            document.location = 'gf_APLICAR_RETENCIONES.php?id=1&mova='+movAlmacen+'&tercero='+tercero; // Dejar

                                                        } else
                                                        {
                                                            $("#mdlErrYaCnt").modal('show');
                                                        }
                                                    }
                                                });
                                            } else
                                            {
                                                jsShowWindowLoad('Guardando...');
                                                var compRet = $("#idComPtal").val();
                                                var valorRet = 0;
                                                var retencionBas = 0;
                                                var form_data = {estruc: 21, compRet: compRet, valorRet: valorRet,
                                                    retencionBas: retencionBas, idform: 1, mova :$("#moviEscogidos").val()};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_aplicar_retenciones.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        jsRemoveWindowLoad();
                                                        console.log(response);
                                                        var numeroLetras = response.length;
                                                        response = response.substr(numeroLetras - 1, 1);

                                                        response = parseInt(response);
                                                        console.log(response);
                                                        if (response == 1)
                                                        {
                                                            $("#modCNTExito").modal('show');

                                                        } else if (response == 2)
                                                        {
                                                            $("#modNoTipComCNT").modal('show');
                                                        } else if (response == 0)
                                                        {
                                                            $("#modCNTError").modal('show');
                                                        }

                                                    }//Fin succes.
                                                });
                                            }
                                        }
                                    });
                              }
                        </script>
                    </div>
                    <!-- table-responsive contTabla -->
                </div>
                <!-- Cierra clase table-responsive contTabla  -->
            </div>
            <!-- Cierra clase col-sm-10 text-left -->
        </div>
                    
        <!-- Cierra clase row content -->
    </div>
    <!-- Cierra clase container-fluid text-center -->
    <div class="modal fade" id="modCNTExito" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Se ha guardado el Comprobante CNT.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntExi" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modCNTError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha guardado el Comprobante CNT.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modNoTipComCNT" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No hay un tipo de comprobante CNT configurado para este tipo de comprobante PPTAL.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCntErr_" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlFechaMayor" role="dialog" align="center"  data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La fecha del registro a agregar es mayor al registro guardado.Verifique Nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnmdlFechaMayor" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"> //Aquí
        $('#btnCntExi').click(function ()
        {
            document.location = 'registro_COMPROBANTE_CNT.php'; //Dejar esta siempre.

        });
    </script>
    <script>
        $("#btnmdlFechaMayor").click(function () {
            $("#btnNuevoRegPre").prop("disabled", false);
        })
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
    <!-- Fin Modales para eliminación -->
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
                    <p>El valor a modificar no puede ser superior al valor existente. Verifique nuevamente.</p>
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
                    <p>La fecha es menor a la del comprobante seleccionado. Verifique nuevamente.</p>
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
    <!-- Error de fecha --> 
    <div class="modal fade" id="mdlAlertComP" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe seleccionar primero un registro presupuestal.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAlertComP" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error de fecha --> 
    <div class="modal fade" id="mdlAlertTercero" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe seleccionar primero un tercero.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAlertTercero" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error de fecha --> 
    <div class="modal fade" id="mdlErrNoCnt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No tiene comprobante CNT para imprimir.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrNoCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error de fecha --> 
    <div class="modal fade" id="mdlErrYaCnt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Ya tiene comprobante CNT. No puede proseguir.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrYaCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlDeseaElimPtal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el comprobante contable de este comprobante seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAcepElimPptal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelElimPptal" class="btn"  style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error de fecha --> 
    <div class="modal fade" id="mdlNoPuedeElim" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No puede eliminar este comprobante ya que tiene comprobantes afectados.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrYaCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlCompEliminado" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCompEliminado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlCompNoEliminado" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnCompNoEliminado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlExitoRetenAnadida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Se ha agregado el registro con éxito.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnExitoRetenAnadida" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlErrNoAnadeRetenc" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo agregar el registro.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrNoAnadeRetenc" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#btnErrNoAnadeRetenc").click(function () {
            $("#btnNuevoRegPre").prop("disabled", false);
        })
    </script>
    <div class="modal fade" id="mdlNoRetenSelec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se ha seleccionado la retención.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoRetenSelec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlNoDebenSerIguales" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe seleccionar un registro.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnNoDebenSerIguales" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
<?php
if (!empty($_SESSION['id_comp_pptal_CP'])) {
    $sig = "SELECT DISTINCT
        dc.comprobante
      FROM
        gf_detalle_comprobante dc
      LEFT JOIN
        gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico
      LEFT JOIN
        gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
      LEFT JOIN
        gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
      LEFT JOIN
        gf_clase_contable cc ON tc.clasecontable = cc.id_unico
      WHERE cc.id_unico = 14 AND 
        dp.comprobantepptal = " . $_SESSION['id_comp_pptal_CP'];


    $sig = $mysqli->query($sig);
    $eg = mysqli_num_rows($sig);
}
//if($eg>0) { 
?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <?php require_once 'footer.php'; ?>
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
    <!-- Función para la eliminación del registro. -->
    <!-- Fin funciones eliminar -->
    <!-- Función para la modificación del registro. -->
    <script type="text/javascript">
        function eliminarDetComp(id)
        {
<?php if (empty($_SESSION['nuevo_CP'])) { ?>
                $("#mdlGenerarCXP").modal('show');
                $('#btnmdlGenerarCXP').click(function () {
                    $("#mdlGenerarCXP").modal('hide');
                });
<?php } else {
    if ($eg > 0) { ?>
                    $("#myModalYaEgE").modal('show');
                    $('#verYaEgE').click(function () {
                        $("#myModalYaEgE").modal('hide');
                        eliminarDetCompAc(id)
                    });
                    $('#verNoYaEgE').click(function () {
                        $("#myModalYaEgE").modal('hide');
                    });
    <?php } else { ?>
                    eliminarDetCompAc(id)

    <?php }
} ?>
        }

    </script>
    <script type="text/javascript">
        function eliminarDetCompAc(id)
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
            document.location = 'GENERAR_CUENTA_PAGAR.php';
        });

    </script>
    <script type="text/javascript">
        $('#ver2').click(function () {
            document.location = 'GENERAR_CUENTA_PAGAR.php';
        });

    </script>
    <script type="text/javascript">
        function modificarDetComp(id)
        {
<?php if (empty($_SESSION['nuevo_CP'])) { ?>
                $("#mdlGenerarCXP").modal('show');
                $('#btnmdlGenerarCXP').click(function () {
                    $("#mdlGenerarCXP").modal('hide');
                });
<?php } else {
    if ($eg > 0) { ?>
                    $("#myModalYaEg").modal('show');
                    $('#verYaEg').click(function () {
                        $("#myModalYaEg").modal('hide');
                        modificarDetCompAc(id);
                    });
                    $('#verNoYaEg').click(function () {
                        $("#myModalYaEg").modal('hide');
                    });
    <?php } else { ?>
                    modificarDetCompAc(id);

    <?php }
} ?>
        }

    </script>
    <div class="modal fade" id="myModalYaEg" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Comprobante Ya Tiene Egreso, ¿Desea Modificarlo?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verYaEg" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                    <button type="button" id="verNoYaEg" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalYaEgE" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Comprobante Ya Tiene Egreso, ¿Desea Eliminarlo?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verYaEgE" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                    <button type="button" id="verNoYaEgE" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modificarDetCompAc(id)
        {

            if (($("#idPrevio").val() != 0) || ($("#idPrevio").val() != ""))
            {
                var cambiarTab = 'tab' + $("#idPrevio").val();
                var cambiarDiv = 'divVal' + $("#idPrevio").val();
                var cambiarOcul = 'valOcul' + $("#idPrevio").val();
                var cambiarMod = 'valorMod' + $("#idPrevio").val();

                var cambiarDivTerc = 'divTerc' + $("#idPrevio").val();
                var cambiarTabTerc = 'tabTerc' + $("#idPrevio").val();
                var cambiarDivProy = 'divProy' + $("#idPrevio").val();
                var cambiarTabProy = 'tabProy' + $("#idPrevio").val();

                if ($("#" + cambiarTab).is(':visible'))
                {

                    $("#" + cambiarTab).css("display", "none");
                    $("#" + cambiarDiv).css("display", "block");
                    $("#" + cambiarMod).val($("#" + cambiarOcul).val());

                    $("#" + cambiarTabTerc).css("display", "none");
                    $("#" + cambiarDivTerc).css("display", "block");

                    $("#" + cambiarTabProy).css("display", "none");
                    $("#" + cambiarDivProy).css("display", "block");
                }
            }

            var idValor = 'valorMod' + id;
            var idModi = 'modif' + id;

            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;

            var idDivTerc = 'divTerc' + id;
            var idTablTerc = 'tabTerc' + id;

            var idDivProy = 'divProy' + id;
            var idTablProy = 'tabProy' + id;



            $("#" + idDiv).css("display", "none");
            $("#" + idTabl).css("display", "block");

            $("#" + idDivTerc).css("display", "none");
            $("#" + idTablTerc).css("display", "block");

            $("#" + idDivProy).css("display", "none");
            $("#" + idTablProy).css("display", "block");

            $("#idActual").val(id);

            if ($("#idPrevio").val() != id)
                $("#idPrevio").val(id);
        }

    </script>
    <div class="modal fade" id="mdlGenerarCXP" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe generar primero la Cuenta Por Pagar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnmdlGenerarCXP" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlDatosIn" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Datos Incompletos.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnmdlDatosIn" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function cancelarModificacion(id)
        {

            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;
            var idValorM = 'valorMod' + id;
            var idValOcul = 'valOcul' + id;

            var idDivTerc = 'divTerc' + id;
            var idTablTerc = 'tabTerc' + id;

            var idDivProy = 'divProy' + id;
            var idTablProy = 'tabProy' + id;


            $("#" + idDiv).css("display", "block");
            $("#" + idTabl).css("display", "none");

            $("#" + idDivTerc).css("display", "block");
            $("#" + idTablTerc).css("display", "none");

            $("#" + idDivProy).css("display", "block");
            $("#" + idTablProy).css("display", "none");

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

            var idCampoTerc = 'tercMod' + id;
            var idCampoProy = 'proyMod' + id;

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
            var resVal = 0;
            var idValMod = "valorMod" + id_txt;
            var idDetComp = "id_det_comp" + id_txt;
            var validar = $("#" + idValMod).val();
            var id_det_comp = $("#" + idDetComp).val();

            var id_ocul = "valOcul" + id_txt;
            var valOriginal = $("#" + id_ocul).val();

            validar = parseFloat(validar.replace(/\,/g, '')); //Elimina la coma que separa los miles.
            valOriginal = parseFloat(valOriginal.replace(/\,/g, ''));

            if ((isNaN(validar)) || (validar == 0) || (validar == ""))
            {
                $("#myModalAlertModInval").modal('show');
            } else if (valOriginal < validar)
            {
                $("#myModalAlertModSuperior").modal('show');
            } else
            {
                var form_data = {proc: 4, id_rubFue: id_rubFue, id_comp: id_det_comp, clase: 15};
                $.ajax({
                    type: "POST",
                    url: "estructura_comprobante_pptal.php",
                    data: form_data,
                    success: function (response)
                    {
                        resVal = parseFloat(response);
                        if (resVal < validar)
                        {
                            $("#myModalAlertMod").modal('show');
                        } else
                        {
                            guardarModificacion(id_txt);
                        }
                    } //Fin success.
                }); //Fin Ajax.
            } //Fin de If. 

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
        //Si se ingresan valores superiores a los valores para aprobar en alguna de las casillas 
        // de la lista para su modificación.
        $('#AceptValModSup').click(function ()
        {
            var id_mod = "valorMod" + $("#idActual").val();
            var id_ocul = "valOcul" + $("#idActual").val();
            $("#" + id_mod).val($("#" + id_ocul).val()).focus();
        });
    </script>
    <script type="text/javascript"></script>
    <script type="text/javascript">
        $('#AceptErrFecVen').click(function () {
            $("#fecha").focus();
        });

    </script>
    <script type="text/javascript">
        $('#AceptErrFec').click(function ()
        {
            var fechaActual = $("#fechaAct").val();
            $("#fecha").val(fechaActual).focus();
        });

    </script>
    <script type="text/javascript">
        $('#btnAlertComP').click(function ()
        {
            var fechaActual = $("#fechaAct").val();
            $("#fecha").val(fechaActual);
            $("#solicitudAprobada").focus();
        });

    </script>
    <script type="text/javascript">
        $('#btnAlertTercero').click(function ()
        {
            $("#tercero").focus();
        });

    </script>
    <script type="text/javascript"> // Evento click para la eliminación de comprobante.
        $('#btnAcepElimPptal').click(function ()
        {

            var id_comp = $("#id_comp_pptal_CP").val();
            console.log(id_comp);
            var form_data = {estruc: 18, id_comp: id_comp};
            $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                    response = parseInt(response);
                    if (response == 1)
                    {
                        $("#mdlCompEliminado").modal('show');
                    } else
                    {
                        $("#mdlCompNoEliminado").modal('show');
                    }
                }//Fin succes.
            }); //Fin ajax.

        });

    </script>
    <script type="text/javascript">
        $('#btnCompEliminado').click(function ()
        {
            document.location.reload(); //Hay que dejar esta línea.
        });

    </script>
    <script type="text/javascript">
        $('#btnExitoRetenAnadida').click(function ()
        {
            document.location.reload(); //Hay que dejar esta línea.
        });

    </script>
    <script type="text/javascript">
        $('#btnNoRetenSelec').click(function ()
        {
            $("#solicitudAprobada").focus();
        });

    </script>
    <script type="text/javascript">
        $('#btnNoDebenSerIguales').click(function ()
        {
            $("#solicitudAprobada").focus();
        });

    </script>
    <script>
        function validar() {
            var fecha = $("#fecha").val();
            var tercero = $("#tercero").val();
            var tipoComprobante = $("#tipoComprobante").val();
            var numReg = $("#numReg").val();
            if (fecha == "" || tercero == "" || tipoComprobante == "" || numReg == "") {
                $("#mdlDatosIn").modal("show");
                return false;
            } else {
                return true;
            }

        }
    </script>
    <!--CIERRE 
    ###BUSCAR EL CIERRE MAYOR
    --->
<?php
if (!empty($_SESSION['id_comp_pptal_CP']) && !empty($_SESSION['nuevo_CP'])) {
    ##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = " . $_SESSION['id_comp_pptal_CP'];
    $fc = $mysqli->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci = "SELECT
    cp.id_unico
    FROM
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 AND cp.anno = $anno";
    $ci = $mysqli->query($ci);
    if (mysqli_num_rows($ci) > 0) {
        ?>
            <script>
                $(document).ready(function ()
                {
                    $("#btnEliminarPtal").prop("disabled", true);
                    $("#btnNuevoRegPre").prop("disabled", true);
                    $("#fecha").prop("disabled", true);

                });
            </script>
    <?php } else {
        if ($eg > 0) {
            ?>
                <script>
                    $(".eliminar").css('display', 'none');
                    $(".modificar").css('display', 'none');
                </script>   
        <?php }
    }
} ?>
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

<script>
        function open_modal_r() {

            var form_data={action:3, id : $("#tercero").val(), movimientos: $("#moviEscogidos").val()}
            $.ajax({
                type : 'post',
                cache: false,
                url : 'jsonPptal/gf_cuenta_pagarJson.php', 
                data : form_data, 
                success : function(data){
                    console.log(data);
                    $('#consulta').html(data);
                    table()
                    
                }
            })
            
        }
    </script>
<style>
    #tabla2 table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    #tabla2 table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    #btnCerrarModalMov1:hover {
        border: 1px solid #020324;         
    }
    
    #btnCerrarModalMov1{
        box-shadow: 1px 1px 1px 1px #424852;
    }
</style>
<style>
.cabeza{
    white-space:nowrap;
    padding: 20px;
}
.campos{
    padding:-20px;
}
</style> 
<div class="modal fade movi" id="mdlMovAlmacen" role="dialog" align="center" aria-labelledby="mdlMovAlmacen" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Movimientos Almacén</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="idActual1" value="">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        
                        <div class="table-responsive contTabla" id="consulta" name="consulta">
                            
                        </div>                                                                        
                    </div>                    
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="btnMov" class="btn" onclick="movimientos()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>
    function movimientos() {
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
            $("#moviEscogidos").val(checkboxValues);
        }
    }
</script>
 <script type="text/javascript" >
  $("#mdlMovAlmacen").draggable({
      handle: ".modal-header"
  });
</script>
<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       $("#mdlMovAlmacen").modal("hide");
    });
    
    $("#mdlMovAlmacen").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tabla21").DataTable();
            dataTable.columns.adjust().responsive.recalc();   
        }catch(err){}        
    });
</script>
<script type="text/javascript">
  function table(){
      $("#mdlMovAlmacen").modal("show");
     var i= 1;
    $('#tabla21 thead th').each( function () {
        if(i != 1){ 
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
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 8:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 9:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 10:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 11:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 12:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 13:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 14:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 15:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 16:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 17:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 18:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 19:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
  
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tabla21').DataTable({
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
      }else{
        i = i+1;
      }
    } );
    
}
</script>
<script>

 </script> 
</body>
</html>