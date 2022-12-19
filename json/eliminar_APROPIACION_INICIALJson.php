<?php 
	require_once('../Conexion/conexion.php');
  session_start();
   
   //Captura de ID y eliminación del registro en la tabla gf_detalle_comprobante_pptal.
  $id = $_GET['id'];

  $querySQL = "SELECT rubrofuente FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
  $rubroFuente = $mysqli->query($querySQL);
  $row = mysqli_fetch_row($rubroFuente);
  $id_rubFue = $row[0];

  $deleteSQL = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
  $resultado = $mysqli->query($deleteSQL);

  $deleteSQL = "DELETE FROM gf_rubro_fuente WHERE id_unico = $id_rubFue";
  $resultado = $mysqli->query($deleteSQL);
  

  echo json_encode($resultado);
?>