<?php
require_once '../Conexion/conexion.php';
session_start();
$tercA= $_GET['tercA'];
$tercero=$_GET['tercero'];
$propietario=$_GET['propietario'];
$predio=$_GET['predio'];
if($_GET['porcentaje']==''){
    $porcentaje='NULL';
    $por=0;
} else {
    $porcentaje=$_GET['porcentaje'];
    $por=$porcentaje;
}
$porcentajeA = "SELECT porcentaje FROM gp_tercero_predio WHERE predio = '$predio' AND tercero='$tercA'";
$porcentajeA = $mysqli->query($porcentajeA);
if(mysqli_num_rows($porcentajeA)>0){
$porcentajeA = mysqli_fetch_row($porcentajeA);
$porcentajeA = $porcentajeA[0];
} else {
    $porcentajeA=0;
}


$porcentajeB = "SELECT SUM(porcentaje) FROM gp_tercero_predio WHERE predio = '$predio'";
$porcentajeB = $mysqli->query($porcentajeB);
$porcentajeB = mysqli_fetch_row($porcentajeB);
$porcentajeB = $porcentajeB[0];

$total = ($porcentajeB+$por)-$porcentajeA;
if($total>100){
    $result=false;
} else {
    

$sql = "UPDATE gp_tercero_predio SET tercero=$tercero,predio=$predio,propietario=$propietario, porcentaje=$porcentaje "
        . "WHERE tercero='$tercA' AND predio='$predio'";

$result = $mysqli->query($sql);
}
echo json_encode($result);
?>