<?php
session_start();
require_once '../Conexion/conexion.php';
$texto = $_GET['term'];
$sql = "SELECT  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion WHERE  IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) LIKE '%$texto%' OR ter.numeroidentificacion LIKE '%$texto%'";
$result = $mysqli->query($sql);
if($result->num_rows > 0){
    while($fila =  mysqli_fetch_row($result)){
        $terceros[] = ucwords(strtolower($fila[0]));
    }    
    echo json_encode($terceros);
}
?>