<?php

require_once '../Conexion/conexion.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$anno = $_SESSION['anno'];
$responsable = $_SESSION['usuario_tercero'];


$id = $_GET["id"];
  $sql = "DELETE FROM gn_concepto_base WHERE id_unico = $id";
  $resultado = $mysqli->query($sql);
  echo json_encode($resultado);
?>