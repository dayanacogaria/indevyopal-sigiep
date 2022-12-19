<?php 
require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];

$action  = $_POST['action'];
$mdl = 1;
$resultado = false;
switch ($action){    
    case 1:
        //modificar
         $fecha  = $_POST['fecha'];
            $amortizacion  = $_POST['txtamortizacion'];
            $numeromeses  = $_POST['txtnumeromeses'];
            $periodicidad  = $_POST['sltperiodicidad'];
            $cuenta  = $_POST['stlcuenta'];
            $observaciones  = $_POST['txtobservaciones'];
            if (empty($observaciones)){
                $observaciones = '';
            }
            $tipodocumento  = $_POST['stltipod'];
            $numerodocumento  = $_POST['txtnumerodocumento'];
            $tercero  = $_POST['stltercero'];
            $concepto  = $_POST['txtconcepto'];
            $detallepptal  = $_POST['txtdetallepptal'];

           $ff = explode("/", $fecha);
           $xx = explode(" ", $ff[2]);
           $fechaI = trim($xx[0]) ."-$ff[1]-$ff[0]";

           $sqcoutas = $con->Listar("
               SELECT numero_meses, fecha_inicial
               FROM gf_amortizacion
               WHERE id_unico = $amortizacion ");
           $nmeses = $sqcoutas[0][0];
           $fechadb = $sqcoutas[0][1];

           $insertSQL = "UPDATE gf_amortizacion SET "
                   . "fecha_inicial = '$fechaI', "
                   . "concepto = $concepto, "
                   . "numero_meses = $numeromeses, "
                   . "periodicidad = $periodicidad, "
                   . "cuenta_debito = $cuenta, "
                   . "tipo_documento = $tipodocumento, "
                   . "numero_documento = $numerodocumento, "
                   . "observaciones = '$observaciones', "
                   . "tercero = $tercero, "
                   . "detallecomprobantepptal = $detallepptal "
                   . "WHERE id_unico = $amortizacion";
            $resultado = $mysqli->query($insertSQL);
            $id = md5($amortizacion);

           if ($nmeses != $numeromeses || $fechaI != $fechadb){    
               $deletedetalleSQL = "DELETE "
                       . "FROM gf_detalle_amortizacion "
                       . "WHERE amortizacion = $amortizacion";    
                   $resultadelete = $mysqli->query($deletedetalleSQL); 

                   $sqldet = $con->Listar("
                   SELECT valor
                   FROM gf_detalle_comprobante_pptal
                   WHERE id_unico = $detallepptal");
                   $valordtpptal =  $sqldet[0][0] / $numeromeses;
                   $fechacuota = $fechaI;
               for ($i = 1; $i<= $numeromeses; $i++){
                   $fecha_ = new DateTime($fechacuota);
                   $mes = 1;
                   $fecha_->modify('+'.$mes.' month');
                   $nuevaFecha = (string)$fecha_->format('Y-m-d');
                   $fechacuota = $nuevaFecha;    
                   $comprobante = 'NULL';
                   $detalle = 'NULL';
                   $insertdetalleSQL = "INSERT INTO gf_detalle_amortizacion (amortizacion, numero_cuota, valor, comprobante, detallecomprobante, fecha_programada) "
                      . "VALUES ($amortizacion, $i, $valordtpptal, $comprobante, $detalle, '$nuevaFecha')";
                   $resultadodt = $mysqli->query($insertdetalleSQL); 
               }
           }
    break;
    case 2:
        $mdl = 2;
        $amortizacion = $_POST['txtamortizacion'];
        $deletedetalleSQL = "DELETE "
                       . "FROM gf_detalle_amortizacion "
                       . "WHERE amortizacion = $amortizacion";    
        $r1 = $mysqli->query($deletedetalleSQL);               
        $deletemtzSQL = "DELETE "
                       . "FROM gf_amortizacion "
                       . "WHERE id_unico = $amortizacion";    
        $resultado = $mysqli->query($deletemtzSQL); 
        $id = $amortizacion;
    break;

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
                <p>Información modificada correctamente.</p>
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
                <p>No se ha podido modificar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdleliminar1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>La información se ha eliminado correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btneliminar1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdleliminar2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido eliminar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btneliminar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.js"></script>

<?php if($resultado==true && $mdl == 1){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../registrar_GF_AMORTIZACION.php?id=<?php echo $id; ?>';
      });
    </script>
<?php }else if($resultado==false && $mdl == 1){ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
        window.location='../registrar_GF_AMORTIZACION.php?id=<?php echo $id; ?>';
      });
    </script>
<?php }else if($resultado==true && $mdl == 2){ ?>
    <script type="text/javascript">
      $("#mdleliminar1").modal('show');
      $("#btneliminar1").click(function(){
        $("#vereliminar1").modal('hide');
        window.location='../GF_AMORTIZACIONES.php';
        
      });
    </script>
<?php }else if($resultado==false && $mdl == 2){ ?>
    <script type="text/javascript">
      $("#mdleliminar2").modal('show');
      $("#btneliminar2").click(function(){
        $("#mdleliminar2").modal('hide');
        window.location='../registrar_GF_AMORTIZACION.php?id=<?php echo $id; ?>';
      });
    </script>
<?php } ?>