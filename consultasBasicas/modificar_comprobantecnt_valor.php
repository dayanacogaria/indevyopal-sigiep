<?php
#########MODIFICACIONES ##############
#22/09/2017 || ERICA G || MODIFICAR CUENTA DETALLES
#13/02/2017 || 05:30 ERICA G. // Guardar Naturaleza de la cuenta en detalle
#10/02/2017 || 10:00 ERICA G. // MODIFICAR VALOR DETALLES
######################################

require_once('../Conexion/conexion.php');
session_start();
$proyecto =$_REQUEST['proyecto'];
$detcompobantecnt = $_POST['iddetalle'];
if(!empty($_POST['cuenta'])){
    $cuenta = $_POST['cuenta'];
} else {
    $cuenta1 = "SELECT cuenta, tercero FROM gf_detalle_comprobante WHERE id_unico =$detcompobantecnt";
    $cuenta1 = $mysqli->query($cuenta1);
    if(mysqli_num_rows($cuenta1)>0){
        $cuenta1 = mysqli_fetch_row($cuenta1);
        $cuenta=$cuenta1[0];
        $tercero =$cuenta1[1];
    }else {
        $cuenta='';
        $tercero ='';
    }
}

if(!empty($cuenta)){
#BUSCAR NATURALEZA
$sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
$rs = $mysqli->query($sql);
$nat = mysqli_fetch_row($rs);
$natural = $nat[0];

    if(empty($_POST['valorD'])) {
        if(empty($_POST['valorC'])) { 
            $rs=2;
        } else {
            if ($nat[0] == 1) {
                $valor ='"'.$mysqli->real_escape_string('-'.$_POST['valorC'].'').'"';
            }else{
                $valor ='"'.$mysqli->real_escape_string(''.$_POST['valorC'].'').'"';
            }
        }

    } else {
        if ($nat[0]==2) {
            $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['valorD'].'').'"';
        }else{
            $valor =  '"'.$mysqli->real_escape_string(''.$_POST['valorD'].'').'"';
        }   
    }

    if($valor=="" || $cuenta=="" ) {
        $rs=2;
    } else {
        
      if(empty($_POST['tercero'])){
          $tercero = $tercero;
      } else {
          $tercero = $_POST['tercero'];
      }
     #*******************************************************************************************************************************************
      #Buscar si el comprobante tiene retención
      $comprobante = $_SESSION['idCompCntV'];
      $queryretencion = "SELECT * FROM gf_retencion WHERE comprobante = $comprobante";
      $queryretencion = $mysqli->query($queryretencion);
      if(mysqli_num_rows($queryretencion)>0){
          #Buscar el valor y la cuenta del detalle
          $detalle = "SELECT cuenta, valor FROM gf_detalle_comprobante WHERE id_unico = $detcompobantecnt";
          $detalle = $mysqli->query($detalle);
          $detalle = mysqli_fetch_row($detalle);
          $cuentadetalle = $detalle[0];
          $valordetalle = $detalle[1];
          
          #Buscar la retencion que tiene esa cuenta y ese comprobante  valor
          $retencion = "SELECT r.id_unico "
                  . "FROM gf_retencion r "
                  . "LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico  "
                  . "WHERE r.comprobante = $comprobante AND tr.cuenta=$cuentadetalle  "
                  . "AND r.valorretencion = $valordetalle";
          $retencion = $mysqli->query($retencion);
          if(mysqli_num_rows($retencion)>0){
              $retencion = mysqli_fetch_row($retencion);
              $idretencion = $retencion[0];
              #*Actualiza la retencion 
              $updateRetencion = "UPDATE gf_retencion SET valorretencion = $valor WHERE id_unico = $idretencion";
              $updateRetencion = $mysqli->query($updateRetencion);
          }
          
          
      }
      $centro = $_REQUEST['centro'];
      
      $sqli = "UPDATE gf_detalle_comprobante SET valor=$valor, "
             . "naturaleza = $natural, tercero = $tercero, "
             . "cuenta = $cuenta , centrocosto = $centro, proyecto=$proyecto "
             . "WHERE id_unico = $detcompobantecnt";
    $rs = $mysqli->query($sqli);
    }
} else {
    $rs=2;
}
echo $rs;
?>

