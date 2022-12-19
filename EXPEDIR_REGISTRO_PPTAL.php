<?php
require_once('Conexion/conexion.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once 'head_listar.php';
require_once('./jsonSistema/funcionCierre.php');
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$numero = "";
$fecha = "";
$fechaVen = "";
$descripcion = "";
$numContrato = "";
$idClaseCon = "";
$claseCon = "";
$terceroComp = "";
$numDet = 0;
$id = 0;
$num_anno   = anno($_SESSION['anno']);
if (!empty($_GET['reg'])) {
    $iddis = $_GET['reg'];
    $dis = "SELECT id_unico FROM gf_comprobante_pptal WHERE md5(id_unico) ='$iddis'";
    $dis = $mysqli->query($dis);
    $dis = mysqli_fetch_row($dis);
    $dis = $dis[0];
    $_SESSION['id_comp_pptal_ER'] = $dis;
    $_SESSION['nuevo_ER'] = 1;
}
if (!empty($_SESSION['id_comp_pptal_ER'])) {
    if (!empty($_SESSION['id_comp_pptal_ER_Detalle'])) {
        $detalleComprobante = $_SESSION['id_comp_pptal_ER_Detalle'];
    } else {
        $detalleComprobante = $_SESSION['id_comp_pptal_ER'];
    }

    $queryGen = "SELECT detComP.id_unico, CONCAT(rub.codi_presupuesto,' - ',rub.nombre), detComP.valor, rubFue.id_unico, fue.nombre, proy.nombre, detComP.tercero, detComP.proyecto      
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro  
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      left join gf_tercero terc on terc.id_unico = detComP.tercero 
      left join gf_proyecto proy on proy.id_unico = detComP.proyecto
      where detComP.comprobantepptal = " . $detalleComprobante;
    $resultado = $mysqli->query($queryGen);

    $numDet = $resultado->num_rows; //Número de filas que retorna la consulta.

    $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.numerocontrato, comp.clasecontrato , cla.nombre, comp.tercero    
      FROM gf_comprobante_pptal comp
      LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico
      LEFT JOIN gf_clase_contrato cla ON comp.clasecontrato = cla.id_unico
      WHERE  comp.id_unico = " . $_SESSION['id_comp_pptal_ER'];

    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $numContrato = $rowComp[8];
    $fechaComprobante = $rowComp[2];
    if (!empty($rowComp[9])) {
        $idClaseCon = $rowComp[9];
        $claseCon = $rowComp[10];
    }

    $terceroComp = $rowComp[11];


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
}

//Consulta para listado de Tipo Comprobante Pptal.
$queryTipComPtal = "SELECT id_unico, codigo, nombre       
  FROM gf_tipo_comprobante_pptal 
  WHERE tipooperacion = 1
  AND clasepptal = 15 AND vigencia_actual = 1 AND compania = $compania 
  ORDER BY codigo";
$tipoComPtal = $mysqli->query($queryTipComPtal);

//Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
$queryClaCont = "SELECT id_unico, nombre    
  FROM gf_clase_contrato";
$clasecont = $mysqli->query($queryClaCont);

//Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
// Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
$natural = array(2, 3, 5, 7, 10);
$juridica = array(1, 4, 6, 8, 9);


$arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
?>




<?php
if (!empty($_SESSION['nuevo_ER']) && !empty($_SESSION['agregar_ER'])) {
    if (($_SESSION['nuevo_ER'] == "") && ($_SESSION['agregar_ER'] == 3)) {
        ?>
        <script type="text/javascript">
            $(document).ready(function ()
            {
                $("#btnGuardarComp").prop("disabled", false);
                $("#btnEliCas").prop("disabled", true);
            });
        </script>
        <?php
    }
}
?>

<title>Expedir Registro Presupuestal</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>

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
            changeYear: true,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
            maxDate: '31/12/<?php echo $num_anno?>',
            minDate: '01/01/<?php echo $num_anno?>'
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();
        $("#fechaVen").datepicker({changeMonth: true}).val();

    });

</script>


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
        width:250px;
        height:120px;
        overflow: auto;
        background-color: white;
    }
    body{
        font-size: 10px;
    }
</style>


<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<!--<script src="lib/jquery.js"></script> -->

</head>
<body onresize="cambiar()">
    <!-- 
    Modificado:
      Ferney Pérez Cano
      27-01-2017 11:35 -->

    <input type="hidden" id="id_comp_pptal_ER" value="<?php echo $_SESSION['id_comp_pptal_ER']; ?>" > 
    <input type="hidden" id="fechaCompP" value="<?php echo $fecha; ?>">
    <input type="hidden" id="fechaVenCompP" value="<?php echo $fechaVen; ?>">
    <input type="hidden" id="fechaActu" >
    <input type="hidden" id="numDet" value="<?php echo $numDet; ?>">

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
            $("#fechaActu").val(fecAct);

        });

    </script>

    <div class="container-fluid text-center"  >
        <div class="row content">
<?php require_once 'menu.php'; ?>

            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 

                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Expedir Registro Presupuestal</h2>


                <div class="col-sm-12">
                    <div class="client-form contenedorForma col-sm-12"  style=""> 

                        <!-- Formulario de comprobante PPTAL -->
                        <form name="form" id="form" class="form-horizontal" method="POST" onsubmit="return valida();"  enctype="multipart/form-data" action="json/registrar_EXP_REG_COMPROBANTE_PPTALJson.php">

                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                            </p>

                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: -2px; margin-bottom: 0px;"> <!-- Primera Fila -->


                                <div class="col-sm-2" align="left" style="padding-left: 0px;"> <!-- Tercero -->
                                    <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero">
                                    <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                    <select onchange="llenar();" name="tercero" id="tercero" class="select2_single form-control" title="Seleccione un tercero" style="width:170px;" required>
                                        <?php
                                        if (empty($_SESSION['nuevo_ER'])) {
                                            $queryTerceroV = "SELECT IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                                                    OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                                                  (tr.razonsocial),
                                                                  CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
                                                                  tr.numeroidentificacion, tr.id_unico , tr.digitoverficacion  
                                                            FROM gf_tercero tr WHERE numeroidentificacion = 9999999999 AND compania = $compania 
                                                            GROUP BY tr.id_unico";
                                            $terceroV = $mysqli->query($queryTerceroV);
                                            $terceroV = mysqli_fetch_row($terceroV);
                                            if (empty($terceroV[3])) {
                                                ?>
                                                <option value="<?php echo $terceroV[2] ?>"><?php echo ucwords(mb_strtolower($terceroV[0])) . ' ' . $terceroV[1]; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $terceroV[2] ?>"><?php echo ucwords(mb_strtolower($terceroV[0])) . ' ' . $terceroV[1] . ' - ' . $terceroV[3]; ?></option>
                                            <?php } ?>

                                            <?php
                                            $queryTercero = "SELECT IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                                    OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                                  (tr.razonsocial),
                                                  CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
                                                  tr.numeroidentificacion, tr.id_unico, tr.digitoverficacion   
                                            FROM gf_tercero tr WHERE numeroidentificacion != 9999999999 AND compania = $compania
                                            GROUP BY tr.id_unico LIMIT 20";
                                            $tercero1 = $mysqli->query($queryTercero);

                                            while ($tercero = mysqli_fetch_row($tercero1)) {
                                                if (empty($tercero[3])) {
                                                    ?>
                                                    <option value="<?php echo $tercero[2] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1]; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $tercero[2] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1] . '-' . $tercero[3]; ?></option>
                                                <?php }
                                            } ?>
                                            <script>
                                                var opcion = '<option value="" >Disponibilidad</option>';

                                                if (($("#tercero").val() == "") || ($("#tercero").val() == 0))
                                                {
                                                    $("#solicitudAprobada").html(opcion);
                                                } else
                                                {
                                                    var form_data = {id_tercero: +$("#tercero").val(), clase: 14};
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "estructura_tercero_comprobante_pptal.php",
                                                        data: form_data,
                                                        success: function (response)
                                                        {
                                                            if (response == "" || response == 0)
                                                            {
                                                                var noHay = '<option value="N" >No hay disponibilidad</option>';
                                                                $("#solicitudAprobada").html(noHay).focus();
                                                            } else
                                                            {
                                                                opcion += response;
                                                                $("#solicitudAprobada").html(opcion).focus();
                                                            }

                                                        }//Fin succes.
                                                    }); //Fin ajax.

                                                } //Ci
                                            </script>     
                                            <?php
                                            } else {
                                                $queryTerceroV = "SELECT IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                                                        OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                                                      (tr.razonsocial),
                                                                      CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
                                                                      tr.numeroidentificacion, tr.id_unico , tr.digitoverficacion  
                                                                FROM gf_tercero tr WHERE id_unico =$terceroComp 
                                                                GROUP BY tr.id_unico";
                                                $terceroV = $mysqli->query($queryTerceroV);
                                                $terceroV = mysqli_fetch_row($terceroV);
                                                if (empty($terceroV[3])) {
                                                    ?>
                                                <option value="<?php echo $terceroV[2] ?>"><?php echo ucwords(mb_strtolower($terceroV[0])) . ' ' . $terceroV[1]; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $terceroV[2] ?>"><?php echo ucwords(mb_strtolower($terceroV[0])) . ' ' . $terceroV[1] . ' - ' . $terceroV[3]; ?></option>
                                            <?php } ?>

                                            <?php
                                            $queryTercero = "SELECT IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                                    OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                                  (tr.razonsocial),
                                                  CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
                                                  tr.numeroidentificacion, tr.id_unico, tr.digitoverficacion   
                                            FROM gf_tercero tr WHERE id_unico !=$terceroComp AND  compania = $compania
                                                
                                            GROUP BY tr.id_unico LIMIT 20 ";
                                            $tercero1 = $mysqli->query($queryTercero);

                                            while ($tercero = mysqli_fetch_row($tercero1)) {
                                                if (empty($tercero[3])) {
                                                    ?>
                                                    <option value="<?php echo $tercero[2] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1]; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $tercero[2] ?>"><?php echo ucwords(mb_strtolower($tercero[0])) . ' ' . $tercero[1] . '-' . $tercero[3]; ?></option>
                                                <?php }
                                            }
                                        } ?>

                                    </select>
                                </div> <!-- Fin Tercero -->


                                <div class="col-sm-3" align="left" style="padding-left: 30px;"> <!-- Disponibilidad -->
                                    <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Disponibilidad:</label><br>
                                    <select name="solicitudAprobada" id="solicitudAprobada" class="select2_single form-control input-sm" title="Número de Disponibilidad" style="width:170px;">
                                        <?php
                                        if (!empty($_SESSION['id_comp_pptal_ER'])) {
    $idD = $_SESSION['id_comp_pptal_ER'];
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
                      WHERE cp.id_unico =$idD AND cp.parametrizacionanno = $anno ORDER BY cp.numero, tcp.codigo ASC";

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
                                        <?php } else { ?>
                                            <option value="" >Disponibilidad</option>
                                        <?php } ?>
                                    </select>  
                                </div><!-- Fin disponibilidad -->
                                <?php if (!empty($_SESSION['id_comp_pptal_ER'])) { ?>
                                <input type="hidden" name="idComPtal" id="idComPtal" value="<?php echo $_SESSION['id_comp_pptal_ER']; ?>" >
                                <?php } else { ?>
                                <input type="hidden" name="idComPtal" id="idComPtal" value="" >
                                <?php } ?>

                                <div class="col-sm-3" align="left" style="padding-left: 0px;"><!-- Tipo de comprobante -->
                                    <label for="tipoComPtal" class="control-label" style="margin-left: 0px;" >
                                        <strong style="color:#03C1FB;">*</strong>
                                        Tipo Comprobante Pptal:
                                    </label><br/>
                                    <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width:170px;" <?php if (!empty($_SESSION['id_comp_pptal_ER'])) {
                                                echo 'autofocus=""';
                                            } ?> required>
                                        <?php
                                        if (!empty($_SESSION['nuevo_ER']) || !empty($_SESSION['agregar_ER'])) {
                                            echo '<option value="' . $rowComp[5] . '" selected="selected" >' . $rowComp[6] . ' ' . ucwords(mb_strtolower($rowComp[7])) . '</option> ';
                                        } else {
                                            ?>
                                            <option value="" selected="selected" >Tipo Comprobante Presupuestal</option>                        
                                            <?php
                                            while ($rowTipComPtal = mysqli_fetch_row($tipoComPtal)) {
                                                ?>
                                                <option value="<?php echo $rowTipComPtal[0]; ?>"><?php echo $rowTipComPtal[1] . ' ' . ucwords(mb_strtolower($rowTipComPtal[2])); ?></option> 
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div> <!-- Fin Tipo de comprobante -->

                                <div class="col-sm-3" style="margin-left: -32px;" > <!-- Buscar disponibilidad -->
                                    <label for="reg" class="control-label" style="margin-left:-60px"><right>Buscar Registro:</right></label>
                                    <select class="select2_single form-control" name="buscarReg" id="buscarReg" style="width:250px">
                                        <option value="">Registro</option>
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
                                          WHERE tcp.clasepptal = 15 AND tcp.tipooperacion=1 AND cp.parametrizacionanno = $anno ORDER BY cp.numero DESC";
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

                                <script type="text/javascript">

                                    $(document).ready(function ()
                                    {
                                        $("#buscarReg").change(function ()
                                        {
                                            if (($("#buscarReg").val() != "") && ($("#buscarReg").val() != 0))
                                            {
                                                traerNum();

                                            } else
                                            {
                                                $("#listado").css("display", "none");
                                                $("#seleccionar").val("");
                                            }
                                        });
                                    });

                                </script>

                                <script type="text/javascript">

                                    function traerNum()
                                    {
                                        var form_data = {estruc: 24, numero: $("#buscarReg").val()};
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_expedir_disponibilidad.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                document.location.reload();
                                            }//Fin succes.
                                        }); //Fi
                                    }

                                </script>

                                <script type="text/javascript">
                                    // Al dar click fuera del input buscar se limpia el input y se oculta el div de resultados.
                                    $(document).ready(function () {

                                        $(document).click(function (e) {
                                            if (e.target.id != 'buscarReg')
                                                $('#buscarReg').val('');
                                            $('#listado').fadeOut();
                                        });

                                    });

                                </script>



                            </div> <!-- Fin de la primera fila -->


                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: -2px; margin-bottom: 0px;"> <!-- Primera Fila -->

                                <div class="col-sm-2" align="left" style="padding-left: 0px;"><!-- Número de disponibilidad -->
                                    <label for="noDisponibilidad" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Número Registro:</label><br/>
                                    <input type="hidden" name="numeroDis" id="numeroDis" value="<?php if (!empty($_SESSION['nuevo_ER']) || !empty($_SESSION['agregar_ER'])) {
                                            echo $numero;
                                        } ?>">
                                    <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de registro" placeholder="Número Registro"  readonly="readonly" value="<?php if (!empty($_SESSION['nuevo_ER']) || !empty($_SESSION['agregar_ER'])) {
                                            echo $numero;
                                        } ?>" required>
                                </div>


                                <div class="col-sm-3" align="left" style="padding-left: 30px;"> <!-- Clase de contrato -->
                                    <label for="claseCont" class="control-label" ><strong style="color:#03C1FB;">*</strong>Clase Contrato:</label><br>
                                    <select name="claseCont" id="claseCont" class="form-control input-sm" title="Seleccione una clase de contrato" style="width:170px;" required>

<?php $claConSelectd = ''; ?> 
                                        <option value="" <?php if (empty($idClaseCon)) {
    echo 'selected="selected"';
} ?>>
                                            Clase Contrato
                                        </option>   

                                        <?php
                                        while ($rowClaCon = mysqli_fetch_row($clasecont)) {
                                            if (!empty($idClaseCon) && $idClaseCon == $rowClaCon[0])
                                                $claConSelectd = 'selected="selected"';
                                            else
                                                $claConSelectd = '';

                                            echo '<option value="' . $rowClaCon[0] . '" ' . $claConSelectd . '>' . ucwords(mb_strtolower($rowClaCon[1])) . '</option>';
                                            ?>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>  <!-- Fin Clase de contrato -->


                                <div class="col-sm-2" align="left" style="padding-left: 0px;">  <!-- Número de contrato -->
                                    <label for="noContrato" class="control-label" ><strong style="color:#03C1FB;">*</strong>No. Contrato:</label><br>
                                    <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:160px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event, 'num_car')" value="<?php echo $numContrato ?>" required>
                                </div> <!-- Fin Número de contrato -->

                                <div class="col-sm-1" style="margin-top: 15px;margin-left: 25px ">
                                    <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a> <!-- Nuevo -->
                                </div>

                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -24px"> <!-- Botón guardar -->
                                    <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                                </div> <!-- Fin Botones nuevo y guardar -->

                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -24px">
                                    <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();">
                                        <img src="images/hb2.png" style="width: 14px; height: 17.28px">
                                    </button> <!--Firma Dactilar-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -24px" ><!--Imprimir-->
                                    <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-left: 0px;" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></button> 
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px;margin-left: -24px">
                                    <a class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; ;width:  40px;margin:0 auto" id="btnListadoA" title="Listado de comprobantes que afectaron al comprobante"><li class="glyphicon glyphicon-th-list"></li></a>
                                </div>


                                    <!-------------------ELIMINAR EN CASCADA-------------------------------->
                                    <div class="col-sm-1" style="margin-top: -5px;margin-left: -30px">
                                        <button type="button" id="btnEliCas" disabled="true" class="btn btn-primary sombra" style="background: #f60303; color: #fff; border-color: #f60303; ;margin:0 auto; margin-top: 20px; width: 70%" title="Eliminar Comprobantes en Cascada" >
                                            <img src="img/eliminar.png" style="width: 100%" >
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
                                                        var form_data = { estruc: 12, id:+$("#id_comp_pptal_ER").val() };
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
                                            var form_data ={estruc:13, id:+$("#id_comp_pptal_ER").val() };
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
                                                                var form_data ={estruc:14, id:+$("#id_comp_pptal_ER").val() };
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

<?php
if (empty($_SESSION['nuevo_ER'])) {
    ?>
                                <script type="text/javascript">

                                    $(document).ready(function ()
                                    {
                                        $("#siguiente").prop("disabled", true);
                                    });
                                </script>

    <?php
}
?>


                            <?php
                            if (!empty($_SESSION['nuevo_ER'])) {
                                ?>
                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        var idComP = $("#id_comp_pptal_ER").val();
                                        var form_data = {estruc: 1, id_com: idComP};
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_modificar_eliminar_pptal.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                if (response == 0)
                                                {
    <?php $cierre = cierre($_SESSION['id_comp_pptal_ER']);
    if ($cierre == 1) {
        ?>

                                                        $("#siguiente").prop("disabled", true);
                                                        $("#btnAgregarDis").prop("disabled", true);
    <?php } else { ?>
                                                        $("#siguiente").prop("disabled", false);
                                                        $("#btnAgregarDis").prop("disabled", false);
    <?php } ?>
                                                } else
                                                {
                                                    $("#siguiente").prop("disabled", true);
                                                    $("#btnAgregarDis").prop("disabled", true);
                                                }
                                            }// Fin success.
                                        });// Fin Ajax;

                                    });
                                </script>

    <?php
}
?>

                            <script type="text/javascript">
                                $(document).ready(function ()
                                {
                                    $("#siguiente").click(function ()
                                    {
                                        var idComP = $("#id_comp_pptal_ER").val();

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
                                                    $("#mdlYaHayAfec").modal('show');
                                                }
                                            }// Fin success.
                                        });// Fin Ajax;

                                    });

                                });
                            </script>

                            <script type="text/javascript">
                                function siguiente()
                                {
                                    var idComP = $("#id_comp_pptal_ER").val();
                                    var form_data = {sesion: 'id_comp_pptal_CP', numero: idComP, nuevo: 'nuevo_CP', valN: 2};
                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_seleccionar_pptal.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            document.location = 'GENERAR_CUENTA_PAGAR.php'; // Dejar
                                            //window.open('GENERAR_CUENTA_PAGAR.php'); // Comentar. Esto se usa solo para pruebas.
                                        }// Fin success.
                                    });// Fin Ajax;

                                }

                            </script>



                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 5px;">  <!-- Segunda fila -->

                                <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

                                    $(document).ready(function ()
                                    {
                                        $("#tercero").change(function ()
                                        {
                                            var opcion = '<option value="" >Disponibilidad</option>';

                                            if (($("#tercero").val() == "") || ($("#tercero").val() == 0))
                                            {
                                                $("#solicitudAprobada").html(opcion);
                                            } else
                                            {
                                                var form_data = {id_tercero: +$("#tercero").val(), clase: 14};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_tercero_comprobante_pptal.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        if (response == "" || response == 0)
                                                        {
                                                            var noHay = '<option value="N" >No hay disponibilidad</option>';
                                                            $("#solicitudAprobada").html(noHay).focus();
                                                        } else
                                                        {
                                                            opcion += response;
                                                            $("#solicitudAprobada").html(opcion).focus();
                                                        }

                                                    }//Fin succes.
                                                }); //Fin ajax.

                                            } //Cierre else.

                                        });//Cierre change.
                                    });//Cierre Ready.

                                </script> <!-- Código JS para asignación -->

<?php
if (!empty($_SESSION['id_comp_pptal_ER'])) {
    ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
    <?php if (empty($_SESSION['id_comp_pptal_ER'])) { ?>
                                                var opcion = '<option value="" >Disponibilidad</option>';
                                                var form_data = {id_tercero: +$("#tercero").val(), clase: 14};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_tercero_comprobante_pptal.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        if (response == "" || response == 0)
                                                        {
                                                            var noHay = '<option value="N" >No hay disponibilidad</option>';
                                                            $("#solicitudAprobada").html(noHay);
                                                        } else
                                                        {
                                                            opcion += response;
                                                            $("#solicitudAprobada").html(opcion);
                                                            var id_comp_pptal_ER = $("#id_comp_pptal_ER").val();
                                                            $('#solicitudAprobada > option[value="' + id_comp_pptal_ER + '"]').attr('selected', 'selected');
                                                        }

                                                    }//Fin succes.
                                                }); //Fin ajax.
    <?php } ?>
                                        }); // Fin ready.
                                    </script>

    <?php
}
?>

                                <div class="col-sm-2" align="left" style="padding-left: 0px;"> <!-- Descripción -->
                                    <label for="nombre" class="control-label" style="" >Descripción:</label><br/>
                                    <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="1000" placeholder="Descripción"   ><?php echo $descripcion; ?></textarea> 
                                </div> <!-- Fin Descripción -->

                                <div class="col-sm-2" align="left" style="padding-left: 30px;"><!--  Fecha -->
                                    <label for="fecha" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>

                                <?php if (empty($_SESSION['nuevo_ER'])) { ?>
                                    <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:120px;" title="Ingrese la fecha" placeholder="Fecha" value="" required="required" readonly="true">
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
                                                        dcpa.comprobantepptal =" . $_SESSION['id_comp_pptal_ER'];
                                        $af = $mysqli->query($af);
                                        $af = mysqli_fetch_row($af);
                                        if ($af[0] > 0) {
                                            ?>
                                            <script>
                                                $(document).ready(function ()
                                                {
                                                    //$("#btnModificarComp").prop("disabled", true);
                                                    $("#btnEliminar").prop("disabled", true);
                                                });
                                            </script>
                                            <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:120px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha; ?>"  readonly="readonly" disabled="true">
                                            <?php } else { ?>
                                            <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:120px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha; ?>" required="required" readonly="readonly" >
                                        <?php }
                                    } ?>
                                </div>

                                <div class="col-sm-2" align="left" style="padding-left: 0px;"> <!-- Fecha Vencimiento  -->
                                    <label for="fechaVen" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha Vencimiento:</label> <br/>
                                    <?php if (empty($_SESSION['nuevo_ER'])) { ?>
                                        <input class="input-sm form-control" type="text" name="fechaVen" id="fechaVen" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value=""  required>  
                                    <?php } else { ?>
                                        <input class="input-sm form-control" type="text" name="fechaVen" id="fechaVen" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen; ?>"  required>  
                                        <script>
                                            $("#fechaVen").datepicker("destroy");
                                            $("#fechaVen").datepicker({changeMonth: true, minDate: $("#fecha").val()}).val($("#fechaVen").val());
                                        </script>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-1" align="left" >
                                    <label for="mostrarEstado" class="control-label" style="" >Estado:</label> <br/>
                                    <input class="input-sm form-control" type="text" name="mostrarEstado" id="mostrarEstado" style="width:100px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 
                                    <input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->
                                </div>
                                <div class="col-sm-1" style="margin-left: 23px"> 
                                    <button type="button" id="siguiente" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 15px;" title="Siguiente" >
                                        <li class="glyphicon glyphicon-arrow-right"></li>
                                    </button> <!--  Siguiente -->
                                </div>

                                <div class="col-sm-1" style="margin-top: -5px; margin-left: -24px;"> 
                                    <button type="button" id="btnModificarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Modificar Comprobante Presupuestal" >
                                        <li class="glyphicon glyphicon-pencil"></li>
                                    </button> <!--  modificar -->
                                </div>

                                <div class="col-sm-1" style="margin-top: -5px; margin-left: -24px;"> 
                                    <button type="button" id="btnEliminar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Eliminar Comprobante Presupuestal" >
                                        <li class="glyphicon glyphicon-remove"></li>
                                    </button> <!--  Eliminar -->
                                </div> 
                                <!--########### AGREGAR DISPONIBILIDAD A REGISTRO ###########-->
                                <div class="col-sm-1" style="margin-top: -5px; margin-left: -24px;"> 
                                    <button type="button" id="btnAgregarDis" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Agregar Disponibilidad" >
                                        <li class="glyphicon glyphicon-plus"></li>Disponibilidad
                                    </button> 
                                </div> 
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
<?php
if (!empty($_SESSION['id_comp_pptal_ER'])) {
    ?>
                                    <script type="text/javascript">
                                        $(document).ready(function ()
                                        {
                                            $("#btnImprimir").click(function () {
                                                window.open('informesPptal/inf_Exp_Reg_Ptal.php');
                                            });
                                        });
                                    </script>
    <?php
}
?>

<?php if (!empty($id)) { ?>
                                    <script>
                                        $("#btnListadoA").attr('disabled', false);
                                        $("#btnListadoA").click(function () {
                                            window.open('informesPptal/inf_listado_afet_com.php?idPptal=<?php echo md5($id) ?>&env=EXPREGPPTAL');
                                        });
                                    </script>
                                <?php } else { ?>
                                    <script>
                                        $("#btnListadoA").attr('disabled', true);
                                    </script>
<?php } ?>



                                </script>

                                <script type="text/javascript"> //Código JS para asignar un nuevo código de comprobante.

                                    $(document).ready(function ()
                                    {
                                        $("#tipoComPtal").change(function ()
                                        {

                                            if (($("#tipoComPtal").val() == "") || ($("#tipoComPtal").val() == 0))
                                            {
                                                $("#noDisponibilidad").val("");
                                            } else
                                            {
                                                var form_data = {estruc: 3, id_tip_comp: +$("#tipoComPtal").val()};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "estructura_expedir_disponibilidad.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        var numero = parseInt(response);
                                                        $("#noDisponibilidad").val(numero);
                                                        $("#fecha").val("");
                                                        $("#fechaVen").val("");
                                                    }//Fin succes.
                                                }); //Fin ajax.

                                            } //Cierre else.

                                        });//Cierre change.
                                    });//Cierre Ready.

                                </script> <!-- Código JS para asignar un nuevo código de comprobante. -->
                                <script>
                                    function asignarfecha() {
                                        var tipComPal = $("#tipoComPtal").val();
                                        var comp = $("#solicitudAprobada").val();
                                        var form_data = {estruc: 3, tipComPal: tipComPal, comp: comp};
                                        $.ajax({
                                            type: "POST",
                                            url: "consultasBasicas/validarFechas.php",
                                            data: form_data,
                                            success: function (response)
                                            {

                                                response = response.replace(" ", "");
                                                $("#fecha").datepicker("destroy");
                                                $("#fecha").datepicker({changeMonth: true, minDate: response}).val(response);
                                                fecha();
                                            }
                                        });

                                    }
                                </script>
                                <script type="text/javascript">
                                    function fecha()
                                    {
                                        var tipComPal = $("#tipoComPtal").val();
                                        var fecha = $("#fecha").val();
                                        var form_data = {estruc: 1, tipComPal: tipComPal, fecha: fecha};
                                        $.ajax({
                                            type: "POST",
                                            url: "consultasBasicas/validarFechas.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                console.log(response+'acaa');

                                                if (response == 1)
                                                {
                                                    $("#myModalAlertErrFec").modal('show');
                                                } else
                                                {
                                                    response = response.replace(" ", "");
                                                    $("#fechaVen").val(response);
                                                }
                                            }
                                        });
                                    }
                                </script>

                            </div><!-- Fin Segunda fila -->

                            <div class="form-group form-inline col-sm-12" style="margin-top: -35px; margin-left: 0px; margin-bottom: 5px;"> <!-- Tercera fila -->




                                <!--  Funcion  -->
                                <script>
                                    $(document).ready(function ()
                                    {
                                        $("#btnAgregarDis").click(function ()
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
                                                    //response=2;
                                                    if (response == 1) {
                                                        $("#periodoC").modal('show');


                                                    } else {
                                                        $("#mdlAgregarDis").modal('show');
                                                    }
                                                }
                                            })
                                        })
                                    })
                                </script>
                                <!--  Modal  -->
                                <div class="modal fade" id="mdlAgregarDis" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Disponibilidad</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <input type="hidden" name="comprobantepptal" id="comprobantepptal" value="<?php echo $_SESSION['id_comp_pptal_ER'] ?>">
                                                <label class="form_label"><strong style="color:#03C1FB;">*</strong>Disponibilidad: </label>
                                                <select name="disponibilidad" id="disponibilidad" class="select2_single form-control input-sm" title="Número de Disponibilidad" style="width:250px;">
                                                    <option value="" >Disponibilidad</option>
<?php
$querySolAprob = "SELECT
                            com.id_unico,
                            com.numero,
                            DATE_FORMAT(com.fecha, '%d/%m/%Y'),
                            com.descripcion,
                            tip.codigo, 
                            (SELECT
                                SUM(dcp.valor)
                            FROM
                                gf_detalle_comprobante_pptal dcp
                            WHERE
                                dcp.comprobantepptal = com.id_unico
                                ) AS valor
                        FROM
                            gf_comprobante_pptal com
                        LEFT JOIN gf_tipo_comprobante_pptal tip ON
                            tip.id_unico = com.tipocomprobante
                        WHERE
                            tip.clasepptal = 14 AND tip.tipooperacion = 1 
                            AND com.parametrizacionanno = $anno  AND com.fecha <='$fechaComprobante' 
                        ORDER BY
                            com.numero,
                            com.fecha ASC";
$SolAprob = $mysqli->query($querySolAprob);

while ($row = mysqli_fetch_row($SolAprob)) {
    $valorRep = 0;
    ##############BUSCA LOS DETALLES##########
    $queryDetCompro = "SELECT
                                detComp.id_unico,
                                detComp.valor
                            FROM
                                gf_detalle_comprobante_pptal detComp,
                                gf_comprobante_pptal comP
                            WHERE
                                comP.id_unico = detComp.comprobantepptal AND comP.id_unico = " . $row[0];
    $saldDispo = 0;
    $totalSaldDispo = 0;
    $detCompro = $mysqli->query($queryDetCompro);
    $valorRep += $row[5];
    $saldo = 0;
    while ($rowDetComp = mysqli_fetch_row($detCompro)) {

        ########AFECTACIONES A DISPONBILIDAD#########
        $afec = "SELECT tc.tipooperacion, dc.valor, dc.id_unico FROM gf_detalle_comprobante_pptal dc 
                                        LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                                        LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                        WHERE dc.comprobanteafectado = $rowDetComp[0]";
        $afec = $mysqli->query($afec);
        while ($row2 = mysqli_fetch_row($afec)) {
            if ($row2[0] == 2) {
                $valorRep += $row2[1];
            } elseif ($row2[0] == 1) {


                $valorRep -= $row2[1];
                //var_dump($valorRep);
                ########AFECTACIONES A REGISTRO#########
                $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc 
                                                LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                                                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                                WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row2[2]";
                $afecR = $mysqli->query($afecR);
                if (mysqli_num_rows($afecR) > 0) {
                    while ($row2R = mysqli_fetch_row($afecR)) {
                        if ($row2R[0] == 2) {
                            $valorRep -= $row2R[1];
                        } elseif ($row2R[0] == 3) {
                            $valorRep += $row2R[1];
                        }
                    }
                }
            } elseif ($row2[0] == 3) {
                $valorRep -= $row2[1];
            }
        }
        var_dump($valorRep);
        $totalSaldDispo += $valorRep;
    }
    $saldo = $valorRep;

    if ($saldo > 0) {
        $tipo = mb_strtoupper($row[4]);
        $valor = '$' . number_format($saldo, 2, '.', ',');
        echo "<option value='$row[0]'>$row[1] $tipo $row[2] $row[3] $valor</option>";
    }
}
?>
                                                </select> 
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="guardarDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Agregar</button>
                                                <button type="button" id="cancelarDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  Fin Modal  -->
                                <!--  Funcion Botones Modal  -->
                                <script>
                                    $("#cancelarDis").click(function ()
                                    {
                                        $("#mdlAgregarDis").modal('hide');
                                    })
                                </script>
                                <script>
                                    $("#guardarDis").click(function ()
                                    {
                                        var idcomprobante = document.getElementById('comprobantepptal').value;
                                        var disponibilidad = document.getElementById('disponibilidad').value;

                                        if (idcomprobante == '') {
                                            document.location.reload();
                                        } else {
                                            if (disponibilidad == '') {
                                                $("#mdlDisError").modal('show');
                                                $("#ErrorguardarDis").click(function ()
                                                {
                                                    $("#mdlDisError").modal('hide');
                                                })
                                            } else {
                                                var form_data = {disponibilidad: disponibilidad, comprobante: idcomprobante};
                                                $.ajax({
                                                    type: "POST",
                                                    url: "consultasBasicas/AgregarDisponibilidad.php",
                                                    data: form_data,
                                                    success: function (response)
                                                    {
                                                        var result = JSON.parse(response)
                                                        console.log(result);
                                                        if (result == true) {
                                                            $("#mdlDisGuardo").modal('show');
                                                            $("#GuardoDispo").click(function ()
                                                            {
                                                                $("#mdlDisGuardo").modal('hide');
                                                                document.location.reload();
                                                            })
                                                        } else {
                                                            $("#mdlDisErrorGuardar").modal('show');
                                                            $("#NoGuardo").click(function ()
                                                            {
                                                                $("#mdlDisErrorGuardar").modal('hide');
                                                                document.location.reload();
                                                            })
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                </script>
                                <!--  Modal Disponibilidad No seleccionada  -->
                                <div class="modal fade" id="mdlDisError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <p>Seleccione Disponibilidad A Agregar</p>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="ErrorguardarDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  Fin Modal  -->
                                <!--  Modal Guardo Disponibilidad   -->
                                <div class="modal fade" id="mdlDisGuardo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <p>Disponibilidad Agregada Correctamente</p>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="GuardoDispo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  Fin Modal  -->
                                <!--  Modal Disponibilidad No seleccionada  -->
                                <div class="modal fade" id="mdlDisErrorGuardar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div id="forma-modal" class="modal-header">
                                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                            </div>
                                            <div class="modal-body" style="margin-top: 8px">
                                                <p>No se ha podido Agregar Disponibilidad</p>
                                            </div>
                                            <div id="forma-modal" class="modal-footer">
                                                <button type="button" id="NoGuardo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  Fin Modal  -->
                                <!--###########FIN AGREGAR DISPONIBILIDAD A REGISTRO #########-->

                                <script type="text/javascript">
                                    $(document).ready(function ()
                                    {
                                        $("#btnModificarComp").click(function ()
                                        {
                                            var idComP = $("#id_comp_pptal_ER").val();
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
                                                        modificarCompPptal(1);
                                                    } else
                                                    {
                                                        modificarCompPptal(2);
                                                    }
                                                }// Fin success.
                                            });// Fin Ajax;

                                        });

                                    });
                                </script>

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
                                                        var idComP = $("#id_comp_pptal_ER").val();
                                                        var form_data = {estruc: 1, id_com: idComP};
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "estructura_modificar_eliminar_pptal.php",
                                                            data: form_data,
                                                            success: function (response)
                                                            {
                                                                if (response == 0)
                                                                {
                                                                    $("#mdlDeseaEliminar").modal('show');
                                                                } else
                                                                {
                                                                    $("#mdlNoEliminar").modal('show');
                                                                }
                                                            }// Fin success.
                                                        });// Fin Ajax;
                                                    }
                                                }
                                            })

                                        });

                                    });
                                </script>

                            </div> <!-- Fin Tercera fila -->

                            <!-- El número de solicitud seleccionado -->
                            <input name="numero" type="hidden" value="<?php echo $numero; ?>">

                            <input type="hidden" name="MM_insert" >
                        </form>



                        <!-- Al seleccionar un número de solcitud, cargará  --> 
                        <script type="text/javascript">

                            $(document).ready(function ()
                            {
                                $("#solicitudAprobada").change(function ()
                                {
                                    if (($("#solicitudAprobada").val() == "") || ($("#solicitudAprobada").val() == 0))
                                    {
                                        var form_data = {estruc: 5}; //Estructura Uno 5
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_expedir_disponibilidad.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                document.location.reload();
                                            }//Fin succes.
                                        }); //Fin ajax.
                                    } else if ($("#noDisponibilidad").val() != "")
                                    {

                                        var id_comp_pptal_ER = $("#id_comp_pptal_ER").val();
                                        var id_comp = $("#solicitudAprobada").val();

                                        var form_data = {estruc: 23, id_comp: id_comp, id_comp_pptal_ER: id_comp_pptal_ER};
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_expedir_disponibilidad.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                document.location.reload();
                                            }//Fin succes.
                                        }); //Fin ajax.

                                    } else
                                    {
                                        var id_comp = $("#solicitudAprobada").val();
                                        var form_data = {estruc: 6, id_comp: id_comp}; //Estructura Dos 6
                                        $.ajax({
                                            type: "POST",
                                            url: "estructura_expedir_disponibilidad.php",
                                            data: form_data,
                                            success: function (response)
                                            {
                                                document.location.reload();
                                            }//Fin succes.
                                        }); //Fin ajax.

                                    } //Cierre else.              
                                });//Cierre change.

                            });//Cierre Ready.

                        </script> <!-- Fin de recargar la página al seleccionar Solicitud nueva -->


                        <script type="text/javascript">// Evalúa que la fecha inicial no sea inferior a la fecha inicial del comprobante predecesor.

                            $("#fecha").change(function ()
                            {
                                //**Valida que el periodo no esté cerrado**//
                                var fecha = $("#fecha").val();
                                var form_data = {case: 4, fecha: fecha};
                                $.ajax({
                                    type: "POST",
                                    url: "jsonSistema/consultas.php",
                                    data: form_data,
                                    success: function (response)
                                    {
                                        if (response == 1) {
                                            $("#periodoC").modal('show');
                                            $("#fecha").val("").focus();

                                        } else {
                                            fecha1();
                                        }
                                    }
                                });
                            });


                        </script> <!-- Fin fecha -->
                        <script>
                            function fecha1() {

                                var fecha = $("#fecha").val();
                                var fechaV = $("#fechaVen").val();
                                var idComPptal = $("#id_comp_pptal_ER").val();
                                var tipComPal = $("#tipoComPtal").val();
                                if (tipComPal == '') {
                                    $("#mdlSelTCF").modal('show');
                                    $("#ErrormdlSelTCF").click(function () {
                                        $("#fecha").val("");
                                        $("#fechaVen").val("");
                                        $("#mdlSelTCF").modal('hide');

                                    })
                                } else {


                                    var fecha = $("#fecha").val();
                                    var num = $("#noDisponibilidad").val();
                                    var dis = $("#solicitudAprobada").val();
                                    console.log(dis);
                                    <?php if (empty($_SESSION['nuevo_ER'])) { ?>
                                    var dis = $("#idComPtal").val();
                                        var form_data = {estruc: 10, idComPptal: idComPptal, tipComPal: tipComPal, fecha: fecha, num: num, dis:dis};
                                    <?php } else { ?>
                                        var form_data = {estruc: 12, idComPptal: idComPptal, tipComPal: tipComPal, fecha: fecha, num: num,dis:dis};
                                    <?php } ?>
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/validarFechas.php",
                                        data: form_data,
                                        success: function (response)
                                        {
                                            console.log(response+'a');
                                            if (response == 1)
                                            {
                                                $("#fechaVen").val("");
                                                $("#fecha").val("");
                                                $("#myModalAlertErrFec").modal('show');
                                            } else
                                            {
                                                response = response.replace(" ", "");
                                                response = $.trim(response);
                                                $("#fechaVen").datepicker("destroy");
                                                $("#fechaVen").datepicker({changeMonth: true, minDate: fecha}).val(response);
                                            }

                                        }
                                    });
                                }
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

                        <div class="modal fade" id="mdlSelTCF" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div id="forma-modal" class="modal-header">
                                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                    </div>
                                    <div class="modal-body" style="margin-top: 8px">
                                        <p>Seleccione Tipo Comprobante y Disponibilidad a Registrar</p>
                                    </div>
                                    <div id="forma-modal" class="modal-footer">
                                        <button type="button" id="ErrormdlSelTCF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- Cierra clase client-form contenedorForma -->
                </div> <!-- Cierra col-sm-10 -->


<?php
if (!empty($_SESSION['id_comp_pptal_ER'])) {
    ?>
                    <script type="text/javascript">
                        $("#btnGuardar").prop("disabled", false);
                        $("#btnImprimir").prop("disabled", false);

                        //$("#descripcion").val("<?php //echo $descripcion; ?>");
                        $("#descripcion").removeAttr('readonly');

                        $("#btnEliminar").prop("disabled", true);
                        $("#btnAgregarDis").prop("disabled", true);
                        $("#btnModificarComp").prop("disabled", true);
                        //$("#btnImprimir").prop("disabled", true);

                    </script>
    <?php
} else {
    ?>
                    <script type="text/javascript">

                        $("#btnGuardar").prop("disabled", true);
                        $("#btnImprimir").prop("disabled", true);

                        $("#descripcion").val("");
                        $("#descripcion").removeAttr('readonly');

                        $("#btnEliminar").prop("disabled", true);
                        $("#btnAgregarDis").prop("disabled", true);
                        $("#btnModificarComp").prop("disabled", true);
                        $("#btnImprimir").prop("disabled", true);


                    </script>
    <?php
}
?>


                <?php
                if (!empty($_SESSION['nuevo_ER'])) {
                    ?>
                    <script type="text/javascript">

                        $("#btnGuardarComp").prop("disabled", true);
                        $("#btnEliCas").prop("disabled", false);
                        
                        //$("#descripcion").attr('readonly','readonly');
                        $("#descripcion").removeAttr('readonly');
                        $("#btnEliminar").prop("disabled", false);
                        $("#btnModificarComp").prop("disabled", false);

                    </script>
    <?php
} else {
    ?>
                    <script type="text/javascript">

                        $("#btnImprimir").prop("disabled", true);
                        $("#btnListadoA").prop("disabled", true);

                    </script>
                <?php } ?>

                <script type="text/javascript">

                    $(document).ready(function ()
                    {
                        $('#btnNuevoComp').click(function () {
                            var form_data = {estruc: 5}; //Estructura Uno 5
                            $.ajax({
                                type: "POST",
                                url: "estructura_expedir_disponibilidad.php",
                                data: form_data,
                                success: function (response)
                                {
                                    document.location.reload();
                                }//Fin succes.
                            }); //Fin ajax.

                        });
                    });

                </script>


                <script>

                    $(document).ready(function ()
                    {
                        llenar();
                    });

                </script>

                <script>

                    function llenar()
                    {
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
                                    <td class="cabeza"><strong>Rubro</strong></td>
                                    <td class="cabeza"><strong>Fuente</strong></td>
                                    <td class="cabeza"><strong>Tercero</strong></td>
                                    <td class="cabeza"><strong>Proyecto</strong></td>
                                    <td class="cabeza"><strong>Valor</strong></td>
                                    <td class="cabeza"><strong>Saldo Registro</strong></td>
                                    <td class="cabeza"><strong>Valor Afectado</strong></td>

                                </tr>

                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Rubro</th>
                                    <th>Fuente</th>
                                    <th>Tercero</th>
                                    <th>Proyecto</th>
                                    <th>Valor</th>
                                    <th>Saldo Registro</th>
                                    <th>Valor Afectado</th>
                                    <td></td>

                                </tr>

                            </thead>
                            <tbody>

<?php
if (!empty($_SESSION['id_comp_pptal_ER']) && ($resultado == true)) {
    $valorTotal = 0;
    $saldoRegis = 0;
    $valorAfec = 0;

    while ($row = mysqli_fetch_row($resultado)) {

        if (!empty($_SESSION['nuevo_ER'])) {
            $valorPpTl = $row[2];
        } else {
            $saldDisp = 0;
            $totalAfec = 0;
            $afec = "SELECT tc.tipooperacion, dc.valor, dc.id_unico FROM gf_detalle_comprobante_pptal dc 
                LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                WHERE dc.comprobanteafectado = $row[0]";
            $afec = $mysqli->query($afec);
            while ($row2 = mysqli_fetch_row($afec)) {
                if($row2[0]==2){
                    $valorRep +=$row2[1];
                } elseif($row2[0]==1) {
                       $valorRep -=$row2[1];
                        ########AFECTACIONES A REGISTRO#########
                        $afecR = "SELECT tc.tipooperacion, dc.valor FROM gf_detalle_comprobante_pptal dc 
                                LEFT JOIN gf_comprobante_pptal cp On dc.comprobantepptal = cp.id_unico 
                                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                                WHERE tc.tipooperacion !=1 AND dc.comprobanteafectado = $row2[2]";
                        $afecR = $mysqli->query($afecR);
                        if(mysqli_num_rows($afecR)>0){
                        while ($row2R = mysqli_fetch_row($afecR)) {
                            if($row2R[0]==2){
                                $valorRep -=$row2R[1];
                            } 
                            elseif($row2R[0]==3) {
                                $valorRep +=$row2R[1];
                            }
                        }
                        }
                }elseif($row2[0]==3) {
                    $valorRep-=$row2[1];
                }
            }
            $totalAfec += $valorRep;
            
            $totalSaldDispo = $row[2] - $totalAfec;
            $valorPpTl = $totalSaldDispo;
        }


        // if($valorPpTl > 0)
        // {
        ?>
                                        <tr>
                                            <td class="oculto"><?php echo $row[0]; ?>
                                                <input  id="id_det_com<?php echo $row[0]; ?>" type="hidden" value="<?php echo $row[0]; ?>" >
                                            </td>
                                            <td class="campos" style="width: 7%;"> <!-- Botones modificar y eliminar -->
                                        <?php
                                        if (!empty($_SESSION['nuevo_ER']) && !empty($_SESSION['id_comp_pptal_ER'])) {
                                            $cierre = cierre($_SESSION['id_comp_pptal_ER']);
                                            if ($cierre == 0) {
                                                $afecd = afect($row[0]);
                                                if ($afecd == 1) {
                                                    
                                                } else {
                                                    ?>
                                                            <div class="modElim">

                                                                <a class="eliminar"  href="#<?php echo $row[0]; ?>" 
                                                    <?php
                                                    if (!empty($_SESSION['nuevo_ER'])) {
                                                        echo 'onclick="javascript:eliminarDetComp(' . $row[0] . ')"';
                                                    }
                                                    ?>
                                                                   >
                                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                                </a>

                                                                <a class="modificar" href="#<?php echo $row[0]; ?>"  
                                                    <?php
                                                    if (!empty($_SESSION['nuevo_ER'])) {
                                                        echo 'onclick="javascript:modificarDetComp(' . $row[0] . ')"';
                                                    }
                                                    ?>
                                                                   >
                                                                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                                </a>

                                                            </div>
                <?php }
            }
        } ?>
                                        
                                            </td>
                                            <td class="campos" align="left" style="width: 10%;"> <!-- Rubro -->
                                                <div class="acotado">
                                                <?php echo ucwords(mb_strtolower($row[1])); ?>
                                                </div>
                                            </td>

                                            <td class="campos" align="left" style="width: 20%;"> <!-- Fuente -->

                                                <div id="txtFuente" class="acotado" style="width: 100%;">
                                                    <?php echo ucwords(mb_strtolower($row[4])); ?>
                                                </div>

                                            </td>

                                            <td class="campos" align="left" style="width: 20%;" > <!-- S  Tercero -->

                                                <div id="divTerc<?php echo $row[0]; ?>" class="acotado"  style="width: 100%;">

                                                    <?php
                                                    $queryTerc = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                                                        FROM gf_tercero ter 
                                                        LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                                                        WHERE ter.id_unico = '$row[6]'";
                                                    $terc = $mysqli->query($queryTerc);
                                                    $rowTer = mysqli_fetch_row($terc);

                                                    if (in_array($rowTer[7], $natural)) {

                                                        echo ucwords(mb_strtolower($rowTer[1])) . ' ' . ucwords(mb_strtolower($rowTer[2])) . ' ' . ucwords(mb_strtolower($rowTer[3])) . ' ' . ucwords(mb_strtolower($rowTer[4])) . ' ' . $rowTer[6];
                                                    } elseif (in_array($rowTer[7], $juridica)) {
                                                        echo ucwords(mb_strtolower($rowTer[5])) . ' ' . $rowTer[6];
                                                    }
                                                    ?>
                                                </div>

                                                <div id="tabTerc<?php echo $row[0]; ?>"> <!-- Select Tercero -->
                                                    <select id="tercMod<?php echo $row[0]; ?>" class="col-sm-12"  title="Seleccione un tercero" style=" margin-top: 0px;" onclick="cargarT(<?=$row[0];?>)">

                                                        <option value="<?php echo $row[6]; ?>" selected="selected" > <!-- Primer select donde se muestra el tercero actual -->
                                                    <?php
                                                    $queryTercAct = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                                                                    FROM gf_tercero ter 
                                                                    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                                                                    WHERE ter.id_unico = '$row[6]'";
                                                    $tercAct = $mysqli->query($queryTercAct);
                                                    $rowTerAct = mysqli_fetch_row($tercAct);

                                                    if (in_array($rowTerAct[7], $natural)) {

                                                        echo ucwords(mb_strtolower($rowTerAct[1])) . ' ' . ucwords(mb_strtolower($rowTerAct[2])) . ' ' . ucwords(mb_strtolower($rowTerAct[3])) . ' ' . ucwords(strtolower($rowTerAct[4])) . ' ' . $rowTerAct[6];
                                                    } elseif (in_array($rowTerAct[7], $juridica)) {
                                                        echo ucwords(mb_strtolower($rowTerAct[5])) . ' ' . $rowTerAct[6];
                                                    }
                                                    ?> 
                                                        </option>   

                                                    <?php
                                                    //Consulta para el listado de concepto de la tabla gf_tercero.
                                                    $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                              FROM gf_tercero ter 
                              LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                              WHERE ter.id_unico != $row[6] AND compania = $compania limit 20";
                                                    $tercero = $mysqli->query($queryTercero);
                                                    while ($rowTerc = mysqli_fetch_row($tercero)) {
                                                        if (in_array($rowTerc[7], $natural)) {
                                                            ?>
                                                  <option value="<?php echo $rowTerc[0]; ?>">
                                                    <?php
                                                    echo ucwords(mb_strtolower($rowTerc[1])) . ' ' . ucwords(mb_strtolower($rowTerc[2])) . ' ' . ucwords(mb_strtolower($rowTerc[3])) . ' ' . ucwords(mb_strtolower($rowTerc[4])) . ' ' . $rowTerc[6];
                                                    ?>
                                                                </option> 
                                                        <?php
                                                    } elseif (in_array($rowTerc[7], $juridica)) {
                                                        ?>
                                                                <option value="<?php echo $rowTerc[0]; ?>"><?php echo ucwords(mb_strtolower($rowTerc[5])) . ' ' . $rowTerc[6]; ?></option> 
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                    </select>

                                                </div>

                                            </td>

                                            <td class="campos" align="left" style="width: 10%;"> <!-- S Proyecto -->

                                                <div id="divProy<?php echo $row[0]; ?>" class="acotado">
                                                            <?php echo ucwords(mb_strtolower($row[5])); ?>
                                                </div>

                                                <div id="tabProy<?php echo $row[0]; ?>">


                                                    <select id="proyMod<?php echo $row[0]; ?>" class="col-sm-12" title="Seleccione un tercero" style="w9idth:80px; margin-top: 0px;">
                                                        <option value="<?php echo $row[7] ?>" selected="selected">
                                                        <?php echo ucwords(mb_strtolower($row[5])); ?>
                                                        </option>

                                                        <?php
                                                        $queryProyecto = "SELECT id_unico, nombre    
                            FROM gf_proyecto
                            WHERE id_unico != $row[7]";

                                                        $proyecto = $mysqli->query($queryProyecto);
                                                        while ($rowProy = mysqli_fetch_row($proyecto)) {
                                                            ?>
                                                            <option value="<?php echo $rowProy[0]; ?>"><?php echo $rowProy[1]; ?></option>
                                                                <?php
                                                            }
                                                            ?>

                                                    </select>
                                                </div>

                                            </td>

                                            <td class="campos" align="right" style="width: 10%; padding: 0px"> <!-- Valor -->

                                                <div class="alienaTexto"></div>

                                                <input type="hidden" id="valOcul<?php echo $row[0]; ?>"  value="<?php echo number_format($row[2]/* $valorPpTl */, 2, '.', ','); ?>">

                                                <div id="divVal<?php echo $row[0]; ?>" style="margin-right: 10px;">
                                                        <?php
                                                        echo number_format($row[2]/* $valorPpTl */, 2, '.', ',');
                                                        ?>
                                                </div>
                                                <!-- Modificar los valores -->

                                                <table align="right" id="tab<?php echo $row[0]; ?>" style="padding: 0px; background-color: transparent; background:transparent; margin: 5px;">
                                                    <tr>
                                                        <td colspan="4" style="padding: 0px;">
                                                            <input type="text" name="valorMod" id="valorMod<?php echo $row[0]; ?>" maxlength="50" style=" width: 80% " placeholder="Valor" onkeypress="return txtValida(event, 'dec', 'valorMod<?php echo $row[0]; ?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0]; ?>');" value="<?php echo number_format($row[2]/* $valorPpTl */, 2, '.', ','); ?>" required>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="padding: 3px;"> <!-- Botón guardar lo modificado. -->
                                                            <a href="#<?php echo $row[0]; ?>" onclick="javascript:verificarValor('<?php echo $row[0]; ?>', '<?php echo $row[3]; ?>');" >
                                                                <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                                            </a>
                                                        </td>
                                                        <td style="padding: 3px;">
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

                                                    var idTabTerc = 'tabTerc' + id;
                                                    var idTabProy = 'tabProy' + id;

                                                    $("#" + idTab).css("display", "none");
                                                    $("#" + idTabTerc).css("display", "none");
                                                    $("#" + idTabProy).css("display", "none");

                                                </script>

                                                <?php
                                                $valorTotal += $row[2];
                                                ?>

                                            </td>
                                            <td class="campos" align="right" style="width: 10%;"> <!-- Saldo Registro -->
                                                <div class="alienaValor"></div>
                                                <?php
                                                if (!empty($_SESSION['nuevo_ER'])) {
                                                    $saldoDisponible = afectacionRegistro($row[0], $row[3], 14);
                                                    if($saldoDisponible <0){$saldoDisponible = $saldoDisponible*-1;}
                                                    $sr = $row[2]-$saldoDisponible;
                                                    echo number_format($sr, 2, '.', ',');
                                                    $saldoRegis += $sr;
                                                } else {
                                                    echo '0.00';
                                                }
                                                ?>
                                            </td>
                                            <td class="campos" align="right" style="width: 10%;"> <!-- Valor afectado -->
                                                <div class="alienaSaldo"></div>
                                                    <?php
                                                    if (!empty($_SESSION['nuevo_ER'])) {
                                                        $saldoDisponible = afectacionRegistro($row[0], $row[3], 14); //valorRegistro($id, $row[3]) + modificacionRegistro($row[3], 14) - 
                                                        echo number_format($saldoDisponible, 2, '.', ',');
                                                        $valorAfec += $saldoDisponible;
                                                    } else {
                                                         echo '0.00';
                                                    }
                                                    ?>
                                            </td>
                                            <td class="campos text-center" style="width: 4%"> <!-- Botón ver afectaciones -->
                                                <div class="alienaAfectado"></div>
                                                <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $row[0] ?>,<?php echo $row[2] ?>);" title="Documentos"><i class="glyphicon glyphicon-file"></i></a>
                                            </td>

                                        </tr>
        <?php
        // }
    }
}
?>

                            </tbody>
                        </table>

                    </div> <!-- table-responsive contTabla -->

                    <div class="col-sm-12" style="font-size: 12px; height: 30px; position: relative; padding: 0px; margin-top: 5px;">

                        <div class="texto" style="position: absolute;" align="right">
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

                        <div class="valor" style="position: absolute;" align="right">
                            <span style="">
<?php
if (!empty($valorTotal)) {
    echo number_format($valorTotal, 2, '.', ',');
}
?>
                            </span>
                        </div>

                        <div class="saldoR" style="position: absolute;" align="right">
                            <span>
<?php
if (!empty($saldoRegis)) {
    echo number_format($saldoRegis, 2, '.', ',');
}
?>
                            </span>
                        </div>

                        <div class="valorAfec" style="position: absolute;" align="right">
                            <span>
                                <?php
                                if (!empty($valorAfec)) {
                                    echo number_format($valorAfec, 2, '.', ',');
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

                                var elementoAfectado = $(".alienaAfectado");
                                var posicionAfectado = elementoAfectado.position();
                                $(".valorAfec").width(posicionAfectado.left);

                            }

                        </script>

    <?php
}
?>

                </div> <!-- Cierra clase table-responsive contTabla  -->

            </div> <!-- Cierra clase col-sm-10 text-left -->
        </div> <!-- Cierra clase row content -->
    </div> <!-- Cierra clase container-fluid text-center -->

    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>

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

    <!-- Error generar el registro primero --> 
    <div class="modal fade" id="ModalAlertNoMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Debe generar primero el Registro Presupuestal.</p>
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
                    <p>¿Desea eliminar los detalles del comprobante seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAcepEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelEliminarComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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

    <div class="modal fade" id="mdlExitEliminarComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>El comprobante ha sido eliminado satisfactoriamente.</p>
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




    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
                        function firma() {

                            $("#modHuella").modal('show');
                        }
    </script>



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
            document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
        });

    </script>

    <script type="text/javascript">

        $('#ver2').click(function () {
            document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
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
        function guardarModificacion(id)
        {
            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;
            var idCampoValor = 'valorMod' + id;
            var idValOcul = 'valOcul' + id;

            var idCampoTerc = 'tercMod' + id;
            var idCampoProy = 'proyMod' + id;

            var valor = $("#" + idCampoValor).val();
            var tercero = $("#" + idCampoTerc).val();
            var proyecto = $("#" + idCampoProy).val();

            valor = valor.replace(/\,/g, ''); //Elimina la coma que separa los miles.

            if (($("#" + idCampoValor).val() == "") || ($("#" + idCampoValor).val() == 0))
            {
                $("#ModificacionNoValida").modal('show');
                $("#" + idCampoValor).val($("#" + idValOcul).val());
            } else
            {
                var form_data = {id_val: id, valor: valor, tercero: tercero, proyecto: proyecto};
                $.ajax({
                    type: "POST",
                    url: "json/modificar_EXP_REG_DETALLE_COMPROBANTE_PPTALJson.php",
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
                var form_data = {proc: 4, id_rubFue: id_rubFue, id_comp: id_det_comp, clase: 14};
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

<?php
if (empty($_SESSION['nuevo_ER'])) {
    ?>
        <script type="text/javascript">

            $('.modElim').click(function ()
            {
                $("#ModalAlertNoMod").modal('show');

            });

        </script>
<?php } ?>

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

        $('#AceptErrFec').click(function ()
        {

            var fechaCompP = $("#fechaCompP").val();
            var fechaVenCompP = $("#fechaVenCompP").val();

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
            $("#fecha").datepicker({changeMonth: true}).val("").focus();
            $("#fechaVen").val("");
        });

    </script>

    <script type="text/javascript">

        $('#AceptErrFecVen').click(function ()
        {
            $("#fecha").focus();
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
            document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
        });

    </script>

    <script type="text/javascript">

        $('#ver2').click(function () {
            document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
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
        function guardarModificacion(id)
        {
            var idDiv = 'divVal' + id;
            var idTabl = 'tab' + id;
            var idCampoValor = 'valorMod' + id;
            var idValOcul = 'valOcul' + id;

            var idCampoTerc = 'tercMod' + id;
            var idCampoProy = 'proyMod' + id;

            var valor = $("#" + idCampoValor).val();
            var tercero = $("#" + idCampoTerc).val();
            var proyecto = $("#" + idCampoProy).val();

            valor = valor.replace(/\,/g, ''); //Elimina la coma que separa los miles.

            if (($("#" + idCampoValor).val() == "") || ($("#" + idCampoValor).val() == 0))
            {
                $("#ModificacionNoValida").modal('show');
                $("#" + idCampoValor).val($("#" + idValOcul).val());
            } else
            {
                var form_data = {id_val: id, valor: valor, tercero: tercero, proyecto: proyecto};
                $.ajax({
                    type: "POST",
                    url: "json/modificar_EXP_REG_DETALLE_COMPROBANTE_PPTALJson.php",
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
                var form_data = {proc: 4, id_rubFue: id_rubFue, id_comp: id_det_comp, clase: 14};
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

<?php
if (empty($_SESSION['nuevo_ER'])) {
    ?>
        <script type="text/javascript">

            $('.modElim').click(function ()
            {
                $("#ModalAlertNoMod").modal('show');

            });

        </script>
<?php } ?>

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

        $('#AceptErrFec').click(function ()
        {

            var fechaCompP = $("#fechaCompP").val();
            var fechaVenCompP = $("#fechaVenCompP").val();

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
            $("#fecha").datepicker({changeMonth: true}).val("").focus();
            $("#fechaVen").val("");
        });

    </script>

    <script type="text/javascript">

        $('#AceptErrFecVen').click(function ()
        {
            $("#fecha").focus();
        });

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

        function modificarCompPptal(id)
        {
            var idComP = $("#id_comp_pptal_ER").val();
            var descripcion = $("#descripcion").val();
            var fechaVen = $("#fechaVen").val();
            var clase = $("#claseCont").val();
            var ncontrato = $("#noContrato").val();
            var fecha = $("#fecha").val();
            if (id == 1) {

                var tercero = $("#terceroB").val();
                var form_data = {estruc: 3, id_com: idComP, descripcion: descripcion, tercero: tercero, fechaVen: fechaVen, fecha: fecha, clase: clase, ncontrato: ncontrato};
            } else {

                var form_data = {estruc: 3, id_com: idComP, descripcion: descripcion, fecha: fecha, fechaVen: fechaVen, clase: clase, ncontrato: ncontrato};
            }
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


    <script type="text/javascript">
        $(document).ready(function ()
        {
            $("#btnAcepEliminarComp").click(function ()
            {
                var idComP = $("#id_comp_pptal_ER").val();
                var numDet = $("#numDet").val();
                var sesion = 'id_comp_pptal_ER|id_comp_pptal_ER_Detalle|nuevo_ER';

                var form_data = {estruc: 4, id_com: idComP, sesion: sesion, numDet: numDet};
                $.ajax({
                    type: "POST",
                    url: "estructura_modificar_eliminar_pptal.php",
                    data: form_data,
                    success: function (response)
                    {
                        if (response == 1)
                        {
                            $("#mdlExitEliminarComp").modal('show');
                        } else
                        {
                            $("#mdlErrorEliminarComp").modal('show');
                        }
                    }// Fin success.
                });// Fin Ajax;

            });

        });
    </script>

    <script type="text/javascript">
        $('#btnExitEliminarComp').click(function ()
        {
            document.location.reload(); //Hay que dejar. Quitar al probar.
        });
    </script>


<?php
if (!empty($_SESSION['nuevo_ER'])) {
    $cierre = cierre($_SESSION['id_comp_pptal_ER']);
    if ($cierre == 1) {
        ?>
            <script>
                $("#btnGuardarComp").prop("disabled", true);
                $("#btnEliCas").prop("disabled", false);
                $("#tercero").prop("disabled", true);
                $("#solicitudAprobada").prop("disabled", true);
                $("#tipoComPtal").prop("disabled", true);
                $("#noDisponibilidad").prop("disabled", true);
                $("#claseCont").prop("disabled", true);
                $("#noContrato").prop("disabled", true);
                $("#descripcion").prop("disabled", true);
                $("#fecha").prop("disabled", true);
                $("#fechaVen").prop("disabled", true);
                $("#siguiente").prop("disabled", true);
                $("#btnModificarComp").prop("disabled", true);
                $("#btnEliminar").prop("disabled", true);
                $("#btnAgregarDis").prop("disabled", true);
            </script>    
    <?php }
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
$('#s2id_autogen1_search').on("keydown", function(e) {
    let term = e.currentTarget.value;
    let form_data4 = {action: 8, term: term};
    console.log('tercero');
    $.ajax({
        type:"POST",
        url:"jsonPptal/gf_tercerosJson.php",
        data:form_data4,
        success: function(data){
            let option = '<option value=""> - </option>';
             option = option+data;
            $("#tercero").html(option);
                
        }
    }); 
});

function cargarT(id){
    $("#tercMod"+id).select2({ placeholder:"Tercero",allowClear: true });
    $('#s2id_autogen5_search').on("keydown", function(e) {
        let term = e.currentTarget.value;
        let form_data4 = {action: 8, term: term};
        console.log('tercero');
        $.ajax({
            type:"POST",
            url:"jsonPptal/gf_tercerosJson.php",
            data:form_data4,
            success: function(data){
                let option = '<option value=""> - </option>';
                 option = option+data;
                $("#tercMod"+id).html(option);
                    
            }
        }); 
    });
    
}
</script>
<?php require_once 'footer.php'; ?>
<?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>
</body>
</html>

