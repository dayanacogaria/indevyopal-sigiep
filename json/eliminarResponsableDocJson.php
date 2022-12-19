<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Añadir campos orden y fechas
##################################################################################################################################################################

    require_once('../Conexion/conexion.php');
    session_start();

    //Captura de ID e instrucción SQL para su eliminación de la tabla gf_unidad_factor.  
   $id1 = $_GET['id'];
   
   $query = "DELETE FROM gf_responsable_documento WHERE id_unico = $id1";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>