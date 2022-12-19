<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Añadir campos orden y fechas
##################################################################################################################################################################

require_once '../Conexion/conexion.php';
require_once '../jsonPptal/funcionesPptal.php';
session_start();
$Tercero=$_GET['t'];
$TipoDocumento=$_GET['tDoc'];
$TipoResponsable=$_GET['tRes'];
$TipoRelacion=$_GET['tRel'];
$id=$_GET['id'];

$TerceroAnterior=$_GET['ta'];
$TipoResponsabeAnterior=$_GET['tResA'];
$TipoRelacionAnterior=$_GET['tRelA'];
$orden  = ($_GET['orden']);
  $fechaI  =($_GET['fechaI']);
  $fechaI =fechaC( $fechaI);
  if(empty(($_GET['fechaF']))){
      $fechaF = 'NULL';
  } else {
      $fechaF =fechaC( $_GET['fechaF']);
      $fechaF = "'".$fechaF."'";
  }

  $sql = "UPDATE gf_responsable_documento "
         . "SET tercero=$Tercero,"
         . "tipodocumento=$TipoDocumento,"
         . "tiporesponsable=$TipoResponsable, "
         . "tipo_relacion = $TipoRelacion, "
            . "orden = $orden, "
            . "fecha_inicio = '$fechaI',"
            . "fecha_fin = $fechaF  "
         . "WHERE id_unico = $id";

    $result = $mysqli->query($sql);


echo json_encode($result);
?>