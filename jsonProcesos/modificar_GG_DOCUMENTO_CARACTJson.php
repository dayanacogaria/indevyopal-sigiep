<?php
require_once '../Conexion/conexion.php';
session_start();
$id=$_GET['id'];
$documento=$_GET['documento'];
$caracteristica=$_GET['caracteristica'];

$queryU="SELECT * FROM gg_confi_caracteristica "
              . "WHERE documento = '$documento' "
              . "AND caracteristica='$caracteristica' ";
$car = $mysqli->query($queryU);
$num=mysqli_num_rows($car);

$queryUA="SELECT documento, caracteristica FROM gg_confi_caracteristica "
              . "WHERE id_unico = '$id'";
$carA = $mysqli->query($queryUA);
$numA=  mysqli_fetch_row($carA);

if($numA[0] ==$documento && $numA[1] ==$caracteristica) {
    $sql = "UPDATE gg_confi_caracteristica "
         . "SET documento=$documento,"
         . "caracteristica=$caracteristica "
         . "WHERE id_unico='$id' ";

    $result = $mysqli->query($sql);
} else {
        

         if($num == 0)
        {
         $sql = "UPDATE gg_confi_caracteristica "
         . "SET documento=$documento,"
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