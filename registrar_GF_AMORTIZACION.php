<?php
require("./Conexion/ConexionPDO.php");
require './Conexion/conexion.php';
require_once("./jsonPptal/funcionesPptal.php");
require './head.php';
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$con = new ConexionPDO();
if (!empty($_GET['id'])){
    $id = $_GET['id'];
    $sqlamortizacion = $con->Listar("
SELECT DISTINCT DATE_FORMAT(c.fecha_inicial, '%d/%m/%Y'), c.id_unico, c.concepto, 
fecha_inicial, numero_meses, periodicidad, cuenta_debito, tipo_documento, numero_documento, observaciones, tercero, detallecomprobantepptal, 
cn.parametrizacionanno 
FROM gf_amortizacion c 
LEFT JOIN gf_concepto cn ON c.concepto = cn.id_unico 
WHERE md5(c.id_unico) = '$id'"
);
$fecha          = $sqlamortizacion[0][0];
$amortizacion   = $sqlamortizacion[0][1];
$numeromeses    = $sqlamortizacion[0][4];
$periodicidad   = $sqlamortizacion[0][5];
$cuenta         = $sqlamortizacion[0][6];
$observaciones  = $sqlamortizacion[0][9];
$tipodoc        = $sqlamortizacion[0][7];
$numerodoc      = $sqlamortizacion[0][8];
$tercero        = $sqlamortizacion[0][10];
$concepto       = $sqlamortizacion[0][2];
$detalle        = $sqlamortizacion[0][11];
$parametrizacion= $sqlamortizacion[0][12];
$sqcoutas = $con->Listar("
    SELECT COUNT(1)
    FROM gf_detalle_amortizacion
    WHERE amortizacion = $amortizacion AND detallecomprobante IS NOT NULL");
$detallecouta = $sqcoutas[0][0];


$sqltr = $con->Listar("
            SELECT pt.tercero, pt.valor, cp.nombre
            FROM gf_detalle_comprobante_pptal pt
            LEFT JOIN gf_concepto_rubro cr ON pt.conceptoRubro = cr.id_unico
            LEFT JOIN gf_concepto cp ON cr.concepto = cp.id_unico
            WHERE pt.id_unico = '$detalle'");
        $conceptodetalle = $sqltr[0][2];
        $valordetalle = "$".number_format($sqltr[0][1], 2, '.', ',');        
}else {
    if (!empty($_GET['concepto']) && !empty($_GET['detalle'])){
        $concepto = $_GET['concepto'];
        $detalle = $_GET['detalle'];
        $sqltr = $con->Listar("
            SELECT pt.tercero, pt.valor, cp.nombre
            FROM gf_detalle_comprobante_pptal pt
            LEFT JOIN gf_concepto_rubro cr ON pt.conceptoRubro = cr.id_unico
            LEFT JOIN gf_concepto cp ON cr.concepto = cp.id_unico
            WHERE md5(pt.id_unico) = '$detalle'");
        $tercero = $sqltr[0][0];
        $conceptodetalle = $sqltr[0][2];
        $valordetalle = "$".number_format($sqltr[0][1], 2, '.', ',');
    }else{
        $concepto = 0;
        $detalle = 0;
        $conceptodetalle = ".";
        $valordetalle = ".";
    }
$amortizacion = 0;
}
$id_aa = idannoanterior($anno);
if(empty($id_aa)){
    $id_aa = 0;
}

$numero_anno = anno($anno);
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/jquery.datetimepicker.css">
<link rel="stylesheet" href="css/desing.css">
<title>Registrar Amortización</title>
<style>
    #form>.form-group{
        margin-bottom: 5px !important;
    }
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
    .campos{padding: 0px;font-size: 10px}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 class="tituloform" align="center" style="margin-top: 0px;">Registrar Amortización </h2>
                <a href="GF_AMORTIZACIONES.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:96%; display:inline-block; margin-bottom: 10px; margin-right: -1px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $conceptodetalle ." ".$valordetalle ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_AMORTIZACIONJson.php">
                            <input type="hidden" value="obligacion" name="expedir">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                            </p>                            
                            <input type="hidden" name="txtamortizacion" id="txtamortizacion" value="<?php echo $amortizacion;?>">
                            <input type="hidden" name="txtconcepto" id="txtconcepto" value="<?php echo $concepto;?>">
                            <input type="hidden" name="txtdetallepptal" id="txtdetallepptal" value="<?php echo $detalle ;?>">
                            <input type="hidden" name="txtdetallecuota" id="txtdetallecuota" value="<?php echo $detallecouta ;?>">
                            <input type="hidden" name="action" id="action" value="0">
                            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;">
                                <div class="col-sm-3" align="left">                                    
                                    <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fecha Inicial:</label><br>
                                    <input required="required" class="form-control fecha" type="text" name="fecha" id="fecha" style="width:180px; height: 38px" title="Ingrese la fecha" placeholder="Fecha" readonly="true" value="<?php if (!empty($fecha)){echo $fecha;}?>">
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="lblNumeromesas" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Número Meses:</label><br>
                                    <input type="number" class="form-control input-sm" name="txtnumeromeses" id="txtnumeromeses" style="width:180px; height: 38px" title="Ingrese Número Meses" placeholder="Número Meses" required value="<?php echo $numeromeses; ?>">
                                </div>
                                <div class="col-sm-3" align="left">
                                    <!-- Tipo Comprobante -->
                                    <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Periodicidad:</label><br>
                                    <select name="sltperiodicidad" id="sltperiodicidad" class="select2_single form-control" title="Periodicidad" style="width:180px; height: 38px" required>
                                        <option value="" >Periodicidad</option>
                                            <?php
                                            $html = "";
                                            if (!empty($periodicidad)){
                                            $sqlperi = $con->Listar("SELECT * FROM gf_periodicidad WHERE id_unico = $periodicidad");
                                            $idd = $sqlperi[0][0];
                                            $nmperi = $sqlperi[0][1];
                                            echo "<option value='$idd' selected>$nmperi</option>";
                                                $sqper = "SELECT * FROM gf_periodicidad WHERE id_unico <> $periodicidad";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }else {
                                                $sqper = "SELECT * FROM gf_periodicidad";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }
                                                ?>
                                           
                                    </select>
                                </div>
                                <!-- Fin Solicitud aprobada -->
                                <div class="col-sm-3" align="left" >
                                    <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label><br>
                                    <select name="stlcuenta" id="stlcuenta" class="select2_single form-control" title="Cuenta Débito" style="width:180px; height: 38px" required>
                                        <option value="" >Cuenta Débito</option>
                                            <?php
                                            $html = "";
                                            if (!empty($cuenta)){
                                                $sqlcuen = $con->Listar("SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico = $cuenta AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta");
                                                $idd = $sqlcuen[0][0];
                                                $nm1 = $sqlcuen[0][1];
                                                $nm2 = $sqlcuen[0][2];
                                                echo "<option value='$idd' selected>$nm1 - $nm2</option>";
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND id_unico <> $cuenta AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                             }
                                            echo $html;
                                            }else{
                                                $sqper = "SELECT id_unico, codi_cuenta, nombre
                                                FROM gf_cuenta
                                                WHERE clasecuenta IN (7,17,18) AND parametrizacionanno = $anno
                                                AND (movimiento = 1 OR centrocosto = 1) 
                                                ORDER BY codi_cuenta";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";                                                
                                             }
                                            echo $html;
                                            }                                            
                                            ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 0px;">
                                <div class="col-sm-3" align="left">
                                    <label class="control-label"><strong style="color:#03C1FB;"></strong>Observaciones:</label> <br/>
                                    <textarea class="form-control input-sm" type="text" name="txtobservaciones" id="txtobservaciones" style="width:180px; height: 60px; margin-top:0px" title="Ingrese Observaciones" placeholder="Observaciones" ><?php if(!empty($observaciones)){echo $observaciones;} ?></textarea>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="claseContrato" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Tipo Documento:</label><br>
                                    <select name="stltipod" id="stltipod" class="select2_single form-control" title="Tipo Documento" style="width:180px; height: 38px" >
                                        <option value="" >Tipo Documento</option>
                                            <?php
                                            $html = "";
                                            if (!empty($tipodoc)){
                                                $sqlperi = $con->Listar("SELECT * FROM gf_tipo_documento WHERE id_unico = $tipodoc  AND compania = $compania");
                                                $idd = $sqlperi[0][0];
                                                $nmperi = ($sqlperi[0][1]);
                                                echo "<option value='$idd' selected>  $nmperi </option>";
                                                $sqper = "SELECT *
                                                FROM gf_tipo_documento
                                                WHERE id_unico <> $tipodoc AND compania = $compania
                                                ORDER BY nombre";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }else{
                                                $sqper = "SELECT *
                                                FROM gf_tipo_documento
                                                WHERE compania = $compania
                                                ORDER BY nombre";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1]</option>";
                                                }
                                                echo $html;
                                            }
                                            
                                            ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label for="lblNumerodocumento" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Número Documento:</label><br>
                                    <input type="number" class="form-control input-sm" name="txtnumerodocumento" id="txtnumerodocumento" style="width:180px; height: 38px" title="Ingrese Número Documento" placeholder="Número Documento" required value="<?php echo $numerodoc; ?>">
                                </div>
                                <div class="col-sm-3" align="left">
                                    <label class="control-label"><strong style="color:#03C1FB;"></strong>Tercero:</label> <br/>
                                    <select name="stltercero" id="stltercero" class="select2_single form-control" title="Tercero" style="width:180px; height: 38px" required>
                                        <option value="" >Tercero</option>
                                            <?php
                                            $html = "";
                                            if (!empty($tercero)){
                                                $sqlter = $con->Listar("SELECT id_unico,numeroidentificacion,
                                                        (
                                                            IF(
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                                            ter.razonsocial,
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                                            )
                                                        ) as tercero
                                                    FROM gf_tercero ter
                                                    WHERE ter.id_unico = $tercero AND ter.compania = $compania");
                                                $idd = $sqlter[0][0];
                                                $nmp1 = $sqlter[0][1];
                                                $nmp2 = $sqlter[0][2];
                                                echo "<option value='$idd' selected>$nmp1 -$nmp2</option>";
                                                $sqper = "SELECT id_unico,numeroidentificacion,
                                                        (
                                                            IF(
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                                            ter.razonsocial,
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                                            )
                                                        ) as tercero
                                                    FROM gf_tercero ter
                                                    WHERE ter.id_unico <> $tercero AND ter.compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                                }
                                            }else{
                                               $sqper = "SELECT id_unico,numeroidentificacion,
                                                        (
                                                            IF(
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                                            ter.razonsocial,
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                                            )
                                                        ) as tercero
                                                    FROM gf_tercero ter WHERE ter.compania = $compania";
                                                $resper = $mysqli->query($sqper);
                                                while ($row = mysqli_fetch_row($resper)) {
                                                    $html .= "<option value='$row[0]'>$row[1] - $row[2]</option>";
                                                } 
                                            }                                            
                                            echo $html; 
                                            ?>
                                    </select>
                                                                 
                                </div>
                                <br/>
                            </div>
                            <div class="col-sm-12" style="margin-top: 0px; margin-bottom: 5px;">
                                <div class="col-sm-2" align="left" style="margin-top:-20px">
                                   <label class="control-label"><strong style="color:#03C1FB;"></strong>Buscar Amortización:</label> <br/>
                                   <select class="select2_single form-control" name="stlamortizacion" id="stlamortizacion" style="width:200px" title="Buscar Amortización">
                                        <option value="">Amortización</option>
                                         <?php
                                            $sqper = "SELECT amz.id_unico, cnp.numero, DATE_FORMAT(cnp.fecha, '%d/%m/%Y'), tpp.codigo,con.nombre, ppt.valor,
                                                    (
                                                    IF(
                                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                                    ter.razonsocial,
                                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                                    )
                                                    ) as tercero
                                                    FROM gf_amortizacion amz
                                                    LEFT JOIN gf_detalle_comprobante_pptal ppt ON amz.detallecomprobantepptal = ppt.id_unico 
                                                    LEFT JOIN gf_comprobante_pptal cnp ON ppt.comprobantepptal = cnp.id_unico
                                                    LEFT JOIN gf_tipo_comprobante_pptal tpp ON cnp.tipocomprobante = tpp.id_unico
                                                    LEFT JOIN gf_concepto con ON ppt.conceptoRubro = con.id_unico
                                                    LEFT JOIN gf_tercero ter ON amz.tercero = ter.id_unico
                                                    WHERE cnp.parametrizacionanno = $anno";
                                            $resper = $mysqli->query($sqper);
                                            while ($row = mysqli_fetch_row($resper)) {
                                                ?>
                                        <option value="<?php echo md5($row[0]); ?>"> <?php echo $row[3]."  ".$row[1]." ".$row[2]." ".$row[4]." $".number_format($row[5], 2, '.', ',') ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12 text-right" style="width: 814px;">
                                    <button type="submit" id="btnNuevo" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                    </button>
                                    <button type="submit" id="btnmodificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar" >
                                        <li class="glyphicon glyphicon-pencil"></li>
                                    </button>
                                    <button type="button" id="btneliminar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Eliminar" >
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </button>
                            </div>
                            </div>
                    </form>
                </div>
            </div>
            <script>
                $("#btnmodificar").click(function(e){        
                    e.preventDefault();
                    let form2 = $("#form");
                    $("#action").val(1);
                    if(form2.valid()){
                        form2.attr('action', 'json/modificar_GF_AMORTIZACIONJson.php');
                        form2.submit();
                    }
                });
                
                $("#stlamortizacion").change(function(){      
                    let id = $(this).val();
                    window.location.href = "registrar_GF_AMORTIZACION.php?id="+id;
                });
                
                $("#btneliminar").click(function(e){
                    $("#mdlinfo").modal("show");;
                    $("#brnconfirmdel").click(function(){                        
                        $('#fecha').removeAttr("required");
                        $('#txtnumeromeses').removeAttr("required");
                        $('#sltperiodicidad').removeAttr("required");
                        $('#stlcuenta').removeAttr("required");
                        $('#stltipod').removeAttr("required");
                        $('#txtnumerodocumento').removeAttr("required");
                        $('#stltercero').removeAttr("required");                        
                       let form2 = $("#form");
                        $("#action").val(2);
                        form2.attr('action', 'json/modificar_GF_AMORTIZACIONJson.php');
                        form2.submit();                     
                    });  
                    $("#brncanceldel").click(function(){
                        $("#mdlinfo").modal("hide");;
                    });  
                });
                
            </script>

            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                <div class="table-responsive contTabla" >
                    <table id="tabla212" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                        <thead style="position: relative;overflow: auto;width: 100%;">
                            <tr>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Fecha</strong></td>
                                <td class="cabeza"><strong>N° Cuota</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Fecha</th>
                                <th class="cabeza">N° Cuota</th>
                                <th class="cabeza">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                        if (!empty($amortizacion)){
                            if($parametrizacion!= $anno){ 
                                $sqldetalles = "SELECT DATE_FORMAT(fecha_programada, '%d/%m/%Y'), numero_cuota, valor
                                    FROM gf_detalle_amortizacion
                                    WHERE amortizacion =  $amortizacion 
                                    AND YEAR(fecha_programada)='$numero_anno'";
                            } else {
                                $sqldetalles = "SELECT DATE_FORMAT(fecha_programada, '%d/%m/%Y'), numero_cuota, valor
                                    FROM gf_detalle_amortizacion
                                    WHERE amortizacion =  $amortizacion";
                            }
                            $resdetalles = $mysqli->query($sqldetalles);
                            while ($row = mysqli_fetch_row($resdetalles)) {
                                echo "<tr>";                                
                                echo "<td class='oculto'></td>";
                                echo "<td></td>";
                                echo "<td class='campos'>$row[0]</td>";
                                echo "<td class='campos'>$row[1]</td>";
                                $valor = number_format($row[2], 2, '.', ',');
                                echo "<td class='campos'>$valor</td>";
                                echo "</tr>";
                            } 
                        }                        
                        ?>   
                        </tbody>
                    </table>

                </div>
                
            </div>
            
        </div>        
    </div>
<div class="modal fade" id="mdlinfo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p id="pinfo">¿Está seguro que desea eliminar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">                
                <button type="button" id="brnconfirmdel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" id="brncanceldel" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </div>
    </div>
</div>
    <?php require_once 'footer.php'; ?>
    <script src="js/script_modal.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/php-date-formatter.min.js"></script>
    <script src="js/jquery.datetimepicker.js"></script>
    <script src="js/script_date.js"></script>
    <script src="js/script_table.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script src="js/script.js"></script>    
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
            //melta
            let amortizacion = $("#txtamortizacion").val();
            let concepto = $("#txtconcepto").val();
            let detalle = $("#txtdetallepptal").val();            
            if (amortizacion > 0){                
                $("#btnNuevo").prop("disabled", true);
                $("#btnmodificar").prop("disabled", false);
                $("#btneliminar").prop("disabled", false);
            }else if (concepto == 0 && detalle == 0 && amortizacion == 0){                                
                $("#btnNuevo").css("display","none");
                $("#btnmodificar").css("display","none");
                $("#btneliminar").css("display","none");
            }else if (concepto.length > 0 && detalle.length > 0){ 
                $("#btnNuevo").prop("disabled", false);
                $("#btnmodificar").prop("disabled", true);
                $("#btneliminar").prop("disabled", true);
            }
            
            let detallecomprobante = $("#txtdetallecuota").val();
            if(detallecomprobante > 0){
                $("#btnmodificar").css("display","none");
                $("#btneliminar").css("display","none");
            }
        });
        
        $(document).ready(function () {
        var i = 1;
        $('#tabla212 thead th').each(function () {
            if (i != 1) {
                var title = $(this).text();
                switch (i) {
                    case 2:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 3:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 4:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 5:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 6:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 6:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 7:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 8:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 9:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 10:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 11:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 12:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 13:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 14:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 15:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 16:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 17:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 18:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                    case 19:
                        $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                        break;
                }
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
        // DataTable
        var table = $('#tabla212').DataTable({
            "pageLength": 5,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
            },
            'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center'
                }]
        });
        var i = 0;
        table.columns().every(function () {
            var that = this;
            if (i != 0) {
                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                                .search(this.value)
                                .draw();
                    }
                });
                i = i + 1;
            } else {
                i = i + 1;
            }
        });
    });
    </script>
</body>
</html>
