<?php 
require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
 $fecha  = $_POST['fecha'];
 $numeromeses  = $_POST['txtnumeromeses'];
 $periodicidad  = $_POST['sltperiodicidad'];
 $cuenta  = $_POST['stlcuenta'];
 $observaciones  = $_POST['txtobservaciones'];
 if (empty($observaciones)){
     $observaciones = '';
 }
if(empty($_POST['stltipod'])){
    $tipodocumento  = 'NULL';
} else { 
    $tipodocumento  = $_POST['stltipod'];
} 
 $numerodocumento  = $_POST['txtnumerodocumento'];
 $tercero  = $_POST['stltercero'];
 $concepto  = $_POST['txtconcepto'];
 $detallepptal  = $_POST['txtdetallepptal'];
 
$ff = explode("/", $fecha);
$xx = explode(" ", $ff[2]);
$fechaI = trim($xx[0]) ."-$ff[1]-$ff[0]";
 $sqlcon = $con->Listar("
SELECT id_unico
FROM gf_concepto 
WHERE md5(id_unico) = '$concepto'
");
$idconcepto =  $sqlcon[0][0];
$sqldet = $con->Listar("
SELECT id_unico, valor
FROM gf_detalle_comprobante_pptal
WHERE md5(id_unico) = '$detallepptal'");
$iddetallepptal =  $sqldet[0][0];
$valordtpptal =  $sqldet[0][1] / $numeromeses;
echo $insertSQL = "INSERT INTO gf_amortizacion (fecha_inicial, concepto, numero_meses, periodicidad, cuenta_debito, tipo_documento, numero_documento, observaciones, tercero, detallecomprobantepptal) "
        . "VALUES ('$fechaI', $idconcepto, $numeromeses, $periodicidad, $cuenta, $tipodocumento, $numerodocumento, '$observaciones', $tercero, $iddetallepptal)";
 $resultado = $mysqli->query($insertSQL); 
if ($resultado){
     $sqlmtr = $con->Listar("
    SELECT MAX(id_unico)
    FROM gf_amortizacion");    
    $amortizacion =  md5($sqlmtr[0][0]);
    $id = $sqlmtr[0][0];
    $fechacuota = $fechaI;
    for ($i = 1; $i<= $numeromeses; $i++){
    $fecha_ = new DateTime($fechacuota);
    $mes = 1;
    $fecha_->modify('+'.$mes.' month');
    $nuevaFecha = (string)$fecha_->format('Y-m-d');
    $fechacuota = $nuevaFecha;    
    $comprobante = 'NULL';
    $detalle = 'NULL';
    $insertdetalleSQL = "INSERT INTO gf_detalle_amortizacion (amortizacion, numero_cuota, valor, fecha_programada) "
        . "VALUES ($id, $i, $valordtpptal, '$nuevaFecha')";
    $resultadodt = $mysqli->query($insertdetalleSQL); 
    }
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
</html>
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
<script src="../js/bootstrap.js"></script>

<?php if($resultado==true){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../registrar_GF_AMORTIZACION.php?id=<?php echo $amortizacion;?>';
      });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.location='../registrar_GF_AMORTIZACION.php';
      });
    </script>
<?php } ?>