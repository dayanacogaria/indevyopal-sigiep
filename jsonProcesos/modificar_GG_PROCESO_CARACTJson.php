<?php
require_once '../Conexion/conexion.php';
session_start();
$id=$_GET['id'];
$proceso=$_GET['proceso'];
$caracteristica=$_GET['caracteristica'];

$queryU="SELECT * FROM gg_confi_caracteristica "
              . "WHERE tipo_proceso = '$proceso' "
              . "AND caracteristica='$caracteristica' ";
$car = $mysqli->query($queryU);
$num=mysqli_num_rows($car);

$queryUA="SELECT tipo_proceso, caracteristica FROM gg_confi_caracteristica "
              . "WHERE id_unico = '$id'";
$carA = $mysqli->query($queryUA);
$numA=  mysqli_fetch_row($carA);

if($numA[0] ==$proceso && $numA[1] ==$caracteristica) {
    $sql = "UPDATE gg_confi_caracteristica "
         . "SET tipo_proceso=$proceso,"
         . "caracteristica=$caracteristica "
         . "WHERE id_unico='$id' ";

    $result = $mysqli->query($sql);
} else {
        

         if($num == 0)
        {
         $sql = "UPDATE gg_confi_caracteristica "
         . "SET tipo_proceso=$proceso,"
         . "caracteristica=$caracteristica "
         . "WHERE id_unico='$id' ";

        $result = $mysqli->query($sql);  
        }
        else
        {
            if($num >0) {
                $result = '3';
            } else {
                $result = false;
            }
        }
}

echo json_encode($result);
?>