<?php 
	require_once('../Conexion/conexion.php');
  session_start();
   
   //Captura de ID y eliminación del registro en la tabla gf_centro_costo.
  $id = $_GET['id'];

   //Hacer la consulta en la tabla gf_centro_costo para descartar que el registro a eliminar exista en un predecesor.
   // $queryPred = "SELECT Id_Unico FROM gf_centro_costo WHERE Predecesor = $id";
   // $resultado = $mysqli->query($queryPred);
   // $num = $resultado->num_rows;
   
   //Si no existe el registro como predecesor se elimina.
   // if($num == 0)
   // {
    $deleteSQL = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
    $resultado = $mysqli->query($deleteSQL);
   // }
   // else //Si existe el registro como predecesor, no se realiza la eliminación y el resultado se hará false para que el usuario reciba el mensaje.
    //$resultado = false;

  echo json_encode($resultado);
?>