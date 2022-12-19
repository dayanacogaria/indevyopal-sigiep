<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$parametroAnno = $_SESSION['anno'];
$idAnteriorComprobante = '"' . $mysqli->real_escape_string('' . $_POST['codigo'] . '') . '"';

$tc2 = $con->Listar("SELECT numero FROM gf_comprobante_pptal WHERE id_unico =  $idAnteriorComprobante");
$numero = $tc2[0][0];
$fecha = fechaC($_POST['fecha']);
if (empty($_POST['descripcion'])) {
    $descripcion = 'NULL';
} else {
    $descripcion = '"' . $mysqli->real_escape_string('' . $_POST['descripcion'] . '').'"';
}
$estado     = '"' . $mysqli->real_escape_string('' . $_POST['estado'] . '') . '"';
$fechaVen   = fechaSum($fecha);

$queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania";
$vario      = $mysqli->query($queryVario);
$row        = mysqli_fetch_row($vario);
$tercero    = $row[0];
$responsable= $row[0];

$tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 12 AND tipooperacion = 1");
$tc = $tc[0][0];
$tipocomprobante = $tc; //Tipo de comprobante 7, APROBACION A SOLICITUD DE CDP.

#****** Buscar Si existe aprobación *******#
$rowa = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE tipocomprobante = $tipocomprobante AND numero = $numero");
if(count($rowa)>0){
   $idNuevoComprobante = $rowa[0][0]; 
   $updateSQL = "UPDATE gf_comprobante_pptal  
      SET fecha = '$fecha',   fechavencimiento = '$fechaVen', 
          descripcion = $descripcion 
      WHERE id_unico = $idNuevoComprobante";
    $resultadoUpdate = $mysqli->query($updateSQL);
} else {
     $insertSQL = "INSERT INTO gf_comprobante_pptal 
        (numero, fecha, fechavencimiento, parametrizacionanno, 
        tipocomprobante, tercero, estado, responsable, descripcion) 
    VALUES('$numero', '$fecha', '$fechaVen', $parametroAnno, 
        $tipocomprobante, $tercero, $estado, $responsable, $descripcion)";
    $resultado = $mysqli->query($insertSQL);
    $queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipocomprobante AND numero = $numero";
    $ultimComp = $mysqli->query($queryUltComp);
    $rowUC = mysqli_fetch_row($ultimComp);
    $idNuevoComprobante = $rowUC[0];
}


if (!empty($idNuevoComprobante)) {   

    $queryAntiguoDetallPttal = "SELECT detComP.descripcion, detComP.valor, detComP.rubrofuente, detComP.tercero, detComP.proyecto, detComP.id_unico, detComP.conceptorubro  
      FROM gf_detalle_comprobante_pptal detComP
      where detComP.comprobantepptal = $idAnteriorComprobante";
    $resultado = $mysqli->query($queryAntiguoDetallPttal);

    $comprobantepptal = $idNuevoComprobante;

    while ($row = mysqli_fetch_row($resultado)) {

        $valor = $row[1];
        $rubro = $row[2];
        $tercero = $row[3];
        $proyecto = $row[4];
        $idAfectado = $row[5];
        $conceptorubro = $row[6];

        $campo = "";
        $variable = "";
        if (($descripcion != '""') && ($descripcion != NULL)) {
            $campo = "descripcion,";
            $variable = "$descripcion,";
        }

        $insertSQL = "INSERT INTO gf_detalle_comprobante_pptal ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, comprobanteafectado, conceptorubro) VALUES ($variable $valor, $comprobantepptal, $rubro, $tercero, $proyecto, $idAfectado, $conceptorubro)";
        $resultadoInsert = $mysqli->query($insertSQL);
    }

    $updateSQL = "UPDATE gf_comprobante_pptal  
      SET estado = $estado     
      WHERE id_unico = $idAnteriorComprobante";
    $resultadoUpdate = $mysqli->query($updateSQL);

    $_SESSION['id_compr_pptal'] = $comprobantepptal;
    $_SESSION['nuevo_pptal'] = 1;
}
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/md5.pack.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    </head>
    <body>
    </body>
</html>
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">

                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
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
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<!-- Script que redirige a la página inicial de aprobar solicitud. -->
<?php if ($resultado == true) { ?>
    <script type="text/javascript">
        $("#myModal1").modal('show');
        $("#ver1").click(function () {
            $("#myModal1").modal('hide');
            window.location = '../APROBAR_COMPROBANTE_PPTAL.php';
        });
    </script>
<?php } else { ?>
    <script type="text/javascript">
        $("#myModal2").modal('show');
    </script>
<?php } ?>