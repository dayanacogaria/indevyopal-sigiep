<?php
require_once '../Conexion/conexion.php';
session_start();
#vamos a modificar la información
#Campos de la tabla producto valor y descripción
$valorU = '"'.$mysqli->real_escape_string(''.$_POST["txtValor"].'').'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST["txtDescripcion"].'').'"';
$producto = '"'.$mysqli->real_escape_string(''.$_POST["producto"].'').'"';
#actualizamos la tabla de producto
$sqlProducto = "update gf_producto set valor=$valorU,descripcion=$descripcion where id_unico = $producto";
$resultProducto =$mysqli->query($sqlProducto);
#vamos a modificar la tabla producto especificación
$sqlProductos = "select     fin.id_unico,
                            fin.elementoficha,
                            elm.id_unico,
                            elm.nombre,
                            elm.tipodato,
                            tpd.id_unico,
                            tpd.nombre,
                            fin.obligatorio,
                            fin.autogenerado,
                            prdes.valor,
                            prdes.id_unico
from gf_ficha_inventario fin 
left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
left join gf_producto_especificacion prdes on prdes.fichainventario = fin.id_unico
where prdes.producto = $producto ORDER BY elm.id_unico";
$resultProductos=$mysqli->query($sqlProductos);
while($campo=$resultProductos->fetch_row()){
    #Reemplazamos los campos vacios en el nombre del elemento
    $fila = str_replace(' ', '', $campo[3]);
    switch ($campo[4]){
        case 1:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 2:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 3:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 4:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 5:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 6:
            $fechaT = ''.$mysqli->real_escape_string(''.$_POST["$fila"].'').'';
            $valorF = explode("/",$fechaT);
            $valor =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';             
            break;        
    }      
    $sqlUpdate = "update gf_producto_especificacion set valor=$valor where id_unico = $campo[10]";
    $resultUpdate = $mysqli->query($sqlUpdate);
}
?>

