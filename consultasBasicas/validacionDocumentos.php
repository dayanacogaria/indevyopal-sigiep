<?php
require_once '../Conexion/conexion.php';
session_start();
if (isset($_FILES["file"]))
{
    
    $file = $_FILES["file"];
    $name = $_FILES["file"]["name"];
    $tipo = $_FILES['file']['type'];
    $tamano = $_FILES['file']['size'];
    $ext = pathinfo($name, PATHINFO_EXTENSION);

        if (($name == !NULL) && ($tamano <= 2000000)) 
        {
           $mensaje='Correcto!!';
           $valor=1;

        } else {
            if($name == !NULL) { 
                $mensaje= "Documento no válido(Tamaño Max:2MB)"; 
                $valor=2;
                

            }else { 
                $mensaje='Seleccione Documento'; 
                $valor=3;

            }
}
} else {
    $mensaje='Seleccione Documento'; 
                $valor=3;
}
  
$datos = array("valor"=>$valor,"mensaje"=>$mensaje);

  echo json_encode($datos);
?>