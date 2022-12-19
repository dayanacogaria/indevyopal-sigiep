<?php
#Llamamos a la clase conexion
require_once ('./Conexion/conexion.php');
#definimos la sesion
session_start();
#Definmos la variable $id con elv alor enviado
$id = $_POST["id"];
#Validamos si la variable tiene algún valor
if ($id == 0) {
    #Consulta 
    $sql= "SELECT DISTINCT S.id_unico,S.nombre FROM gf_sector S LEFT JOIN gf_rubro_pptal PP ON PP.sector= S.id_unico";
    #Cargamos el resultado de la consutla
    $rs = $mysqli->query($sql);
    #Imprimimos la primera fila
    echo '<option value="" selected="selected">Sector</option>';
    #Imprimimos el resutlado de $rs en un ciclo
    while ($row = mysqli_fetch_row($rs)) {
        echo '<option value="'.$row[0].'">'.ucwords(utf8_encode(strtolower($row[1]))).'</option>';
    }
}else{
    #Consulta
    $sql= "SELECT DISTINCT S.id_unico,S.nombre FROM gf_sector S
LEFT JOIN gf_rubro_pptal PP ON S.id_unico = PP.id_unico
WHERE PP.id_unico ='$id'";
    #Cargamos el resultado de la consutla
    $rs = $mysqli->query($sql);
    #Cargamos el valor o valores retornados a $row para imprimirlos
    $row = mysqli_fetch_row($rs);
    echo '<option value="'.$row[0].'" selected="selected">'.ucwords(utf8_encode(strtolower($row[1]))).'</option>';
    $sql1= "SELECT DISTINCT S.id_unico,S.nombre FROM gf_sector S
LEFT JOIN gf_rubro_pptal PP ON S.id_unico != PP.id_unico
WHERE S.id_unico !='$row[0]'";
        $res = $mysqli->query($sql1);
        while($fila = mysqli_fetch_row($res)){
            echo '<option value="'.$fila[0].'" >'.ucwords(utf8_encode(strtolower($fila[1]))).'</option>';
        }
}
?>