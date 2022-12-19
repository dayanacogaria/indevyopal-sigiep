<?php 
require_once('../Conexion/ConexionPDO.php');
require_once('../Conexion/conexion.php');
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$response = 0;
$action  = $_POST['action'];
if ($action == 1 || $action == 2){    
    $cuentatraslado = $_POST['sltcttraslado'];
    $centrocosto    = $_POST['sltcosto'];
    $cuentadebito   = $_POST['sltctdebito'];
    $costodebito    = $_POST['sltcostodebito'];
    $cuentacredito  = $_POST['sltctcredito'];
    $costocredito   = $_POST['sltcostocredito'];
}
switch ($action){    
    case 1:        
        $insertSQL = "INSERT INTO gf_configuracion_traslado (cuenta_traslado, centro_costo, cuenta_debito, centro_costo_debito, cuenta_credito, centro_costo_credito) "
        . "VALUES ($cuentatraslado, $centrocosto, $cuentadebito, $costodebito, $cuentacredito, $costocredito)";
        $resultado = $mysqli->query($insertSQL);
        $resultado = true;
        if ($resultado){            
            $sqlcuenta = $con->Listar("
            SELECT MAX(id_unico)
            FROM gf_configuracion_traslado");    
            $response =  md5($sqlcuenta[0][0]);
        } 
    break;
    case 2:
        $cuenta = $_POST['idcuenta'];
        $updateSQL = "UPDATE gf_configuracion_traslado SET "
                   . "cuenta_traslado = $cuentatraslado, "
                   . "centro_costo = $centrocosto, "
                   . "cuenta_debito = $cuentadebito, "
                   . "centro_costo_debito = $costodebito, "
                   . "cuenta_credito = $cuentacredito, "
                   . "centro_costo_credito = $costocredito "
                   . "WHERE id_unico = $cuenta";
            $resultado = $mysqli->query($updateSQL);
            $response = md5($cuenta);
    break;
    case 3:
        $cuenta = $_POST['idcuenta']; 
        $deletemtzSQL = "DELETE "
                       . "FROM gf_configuracion_traslado "
                       . "WHERE id_unico = $cuenta";    
        $resultado = $mysqli->query($deletemtzSQL); 
        if ($resultado){
            $response = 1;
        }else {
            $response = md5($cuenta);
        }        
    break;
}
echo $response;
?>