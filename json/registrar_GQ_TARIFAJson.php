<?php
#############Archivo by David Padilla 23/05/2019#######################
require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$msj = '';
$action = 0;
if (!empty($_POST['action'])){
    $action = $_POST['action'];
}else {
    $action = $_GET['action'];
}
switch ($action) {
    case 1: #Insert
        $nombre     = $_POST['txtNombre'];
        $valor      = $_POST['txtvalor'];
        $unidadm    = $_POST['sltunidadm'];
        $intervalo  = $_POST['txtintervalo'];
        $desviacion = $_POST['txtdesviacion'];
        $tarifaaso  = $_POST['slttarifaaso'];
        $tipov      = $_POST['slttipov'];
        if (empty($tarifaaso)){
            $tarifaaso = "NULL";
        }
        $sqlinsert  = "INSERT INTO gq_fraccion VALUES (NULL, '$nombre', $valor, '$unidadm', $desviacion, $intervalo, $tarifaaso, $tipov)";
        $resultado = $mysqli->query($sqlinsert);
        break;
    case 2: #Update
        $id         = $_POST['id'];
        $nombre     = $_POST['txtNombre'];
        $valor      = $_POST['txtvalor'];
        $unidadm    = $_POST['sltunidadm'];
        $intervalo  = $_POST['txtintervalo'];
        $desviacion = $_POST['txtdesviacion'];
        $tarifaaso  = $_POST['slttarifaaso'];
        $tipov      = $_POST['slttipov'];
        if (empty($tarifaaso)){
            $tarifaaso = "NULL";
        }
        $sqlupdate  = "
        UPDATE gq_fraccion SET
        nombre          = '$nombre', 
        valor           = $valor, 
        unidad_medida   = '$unidadm', 
        desviacion      = $desviacion, 
        intervalo       = $intervalo, 
        tarifa_asociada = $tarifaaso, 
        tipo_vehiculo   = $tipov
        WHERE id_unico  = $id";
        $resultado = $mysqli->query($sqlupdate);
        break;
    case 3: #Delete
        $id         = $_GET['id'];
        $sqlinsert  = "DELETE FROM gq_fraccion WHERE md5(id_unico) = '$id'";
        $resultado = $mysqli->query($sqlinsert);   
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
<div class="modal fade" id="mdlinfo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p id="pinfo"></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.js"></script>
<?php
    if($action == 1){ # Insert
        if ($resultado==true){
            $msj = "Informaci&oacuten guardada correctamente.";
        }else {
            $msj = "No se ha podido guardar la informaci&oacuten.";
        }
    }else if ($action == 2) { #Update
        if ($resultado==true){
            $msj = "Informaci&oacuten modificada correctamente.";
        }else {
            $msj = "No se ha podido modificar la informaci&oacuten.";
        }
    }else if ($action == 3) { #Delete
        if ($resultado==true){
            $msj = "Informaci&oacuten eliminada correctamente.";
        }else {
            $msj = "No se ha podido eliminar la informaci&oacuten.";
        }
    }
?>
<script type="text/javascript">
    let response = "<?php echo $msj ?>";
    $("#pinfo").html(response);
    $("#mdlinfo").modal('show');
    $("#ver1").click(function(){
        $("#mdlinfo").modal('hide');
        window.location='../LISTAR_GQ_TARIFA.php';
    });
</script>