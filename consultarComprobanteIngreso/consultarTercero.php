<?php
#MODIFICADO 30/01/2017 ERICA G.
#Modificacion 09-02-2017 Jhon Numpaque | Inclusión de consulta por tercero recibido
session_start();
require_once '../Conexion/conexion.php';
$cuenta = $_POST['cuenta'];
if(!empty($_POST['ter'])){
    $tercero = $_POST['ter'];
    $sql = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion          
        WHERE ter.id_unico = $tercero
    ";
    $result = $mysqli->query($sql);
    $row=  mysqli_fetch_row($result);
    echo '<option value="'.$row[1].'">'.ucwords(strtolower($row[0])).'</option>';
    $sql1 = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion          
        WHERE ter.id_unico != $tercero
    ";
    $result1 = $mysqli->query($sql1);
    while ($row1=  mysqli_fetch_row($result1)){
        echo '<option value="'.$row1[1].'">'.ucwords(strtolower($row1[0])).'</option>';
    }
}else{    
    $sql = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
        ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
        LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion          
    ";
    $result = $mysqli->query($sql);
    while ($row=  mysqli_fetch_row($result)){
        echo '<option value="'.$row[1].'">'.ucwords(strtolower($row[0])).'</option>';
    }    
}

?>