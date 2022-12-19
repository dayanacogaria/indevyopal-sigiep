
<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $cuenta = $mysqli->real_escape_string(''.$_POST['cuenta'].'');
  $tipo  =$mysqli->real_escape_string(''.$_POST['tipo'].'');
  $cuentaE = $mysqli->real_escape_string(''.$_POST['cuentaE'].'');
  $anno = $_SESSION['anno'];
  
 $insert = "INSERT INTO gf_equivalencia_puc "
         . "(cuenta, tipo_equivalencia, cuenta_equivalente, parametrizacion) "
         . "VALUES($cuenta, $tipo,$cuentaE, $anno)";
  $resultado = $mysqli->query($insert);
  
  echo json_encode($resultado);
?>

