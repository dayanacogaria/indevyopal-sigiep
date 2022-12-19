<?php
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
session_start();
$con            = new ConexionPDO();
$anno           = $_SESSION['anno'];
$compania       = $_SESSION['compania'];
$rubroFuente    = '"' . $mysqli->real_escape_string('' . $_POST['rubroFuente'] . '') . '"';
$conceptoRubro  = '"' . $mysqli->real_escape_string('' . $_POST['conceptoRubro'] . '') . '"';
$valor          = '"' . $mysqli->real_escape_string('' . $_POST['valor'] . '') . '"';
$valor          = str_replace(',', '', $valor);
$comprobantepptal = $_SESSION['id_comp_pptal_ED'];

$queryDesc      = "SELECT descripcion, fecha FROM gf_comprobante_pptal WHERE id_unico = " . $comprobantepptal;
$desc           = $mysqli->query($queryDesc);
$row            = mysqli_fetch_row($desc);
$descripcion    = '"' . $mysqli->real_escape_string('' . $row[0] . '') . '"';
$fecha          = $row[1];
$queryProy      = "SELECT id_unico FROM gf_proyecto WHERE nombre = 'VARIOS' AND compania = $compania";
$proyecto       = $mysqli->query($queryProy);
$row            = mysqli_fetch_row($proyecto);
$id_proyecto    = $row[0];
$campo = "";
$variable = "";
if (($descripcion != '""') && ($descripcion != NULL)) {
    $campo = "descripcion,";
    $variable = "$descripcion,";
}
$IDRubroFuente = $rubroFuente;
$saldoDisponible = apropiacionfecha($IDRubroFuente, $fecha) - disponibilidadesfecha($IDRubroFuente, $fecha);
if (empty($_POST['centroC'])) {
    $cv = $con->Listar("SELECT * FROM gf_centro_costo WHERE parametrizacionanno = $anno AND nombre ='Varios'");
    if (count($cv) > 0) {
        $cc = $cv[0][0];
    } else {
        $cc = 'NULL';
    }
} else {
    $cc = $_POST['centroC'];
}
if (empty($_POST['tercerod'])) {
    $queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999 AND compania = $compania";
    $vario = $mysqli->query($queryVario);
    $row = mysqli_fetch_row($vario);
    $tercero = $row[0];
} else {
    $tercero = $_POST['tercerod'];
}
$insertSQL = "INSERT INTO gf_detalle_comprobante_pptal 
         ($campo valor, comprobantepptal, rubrofuente, tercero, proyecto, 
          conceptorubro, saldo_disponible, centro_costo) 
    VALUES ($variable $valor, $comprobantepptal, $rubroFuente, $tercero, $id_proyecto, 
       $conceptoRubro,$saldoDisponible, $cc )";
$resultado = $mysqli->query($insertSQL);
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
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<?php if ($resultado == true) { ?>
    <script type="text/javascript">
        $("#myModal1").modal('show');
        $("#ver1").click(function () {
            $("#myModal1").modal('hide');
            window.location = '../EXPEDIR_DISPONIBILIDAD_PPTAL.php';
        });
    </script>
<?php } else { ?>
    <script type="text/javascript">
        $("#myModal2").modal('show');
        $("#ver2").click(function () {
            $("#myModal1").modal('hide');
            window.location = '../EXPEDIR_DISPONIBILIDAD_PPTAL.php';
        });
    </script>
<?php } ?>