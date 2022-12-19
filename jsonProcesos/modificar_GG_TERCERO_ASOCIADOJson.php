<?php
require_once('../Conexion/conexion.php');
  session_start();
$id  = $mysqli->real_escape_string(''.$_POST['idMod'].'');
$proceso  = $mysqli->real_escape_string(''.$_POST['procesoMod'].'');
$tercero = $mysqli->real_escape_string(''.$_POST['terceroMod'].'');
$porcentaje = $mysqli->real_escape_string(''.$_POST['porcentajeMod'].'');

#DATOS ANTERIORES
$datosAnteriores="SELECT tercero, porcentaje_participacion, proceso FROM gg_persona_proceso WHERE id_unico ='$id'";
$datosAnteriores = $mysqli->query($datosAnteriores);
$datosA = mysqli_fetch_row($datosAnteriores);

$queryU="SELECT * FROM gg_persona_proceso "
              . "WHERE tercero = '$tercero' "
              . "AND proceso='$proceso' ";
$car = $mysqli->query($queryU);
$num=mysqli_num_rows($car);

if($datosA[0] ==$tercero && $datosA[2] ==$proceso) {
    $sql = "UPDATE gg_persona_proceso "
         . "SET tercero=$tercero,"
         . "porcentaje_participacion=$porcentaje "
         . "WHERE id_unico='$id' ";

    $result = $mysqli->query($sql);
} else {

         if($num == 0)
        {
         $sql="UPDATE gg_persona_proceso "
         . "SET tercero=$tercero,"
         . "porcentaje_participacion=$porcentaje "
         . "WHERE id_unico='$id' ";

        $result = $mysqli->query($sql);  
        }
        else
        {
            if($num >0) {
                $result = false;
            }
        }
}

echo json_encode($result);
?>