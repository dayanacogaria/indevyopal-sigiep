<?php
session_start();
require_once '../Conexion/conexion.php';
#Capturamos los valores enviados
$ficha = $_POST['ficha'];               
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['valor'].'').'';  
#Consulta en la que creamos un array similar al que se encuentra en paginación
$sqlprofichas = "SELECT prdes.producto
                FROM gf_producto_especificacion prdes 
                LEFT JOIN gf_ficha_inventario fin ON fin.id_unico = prdes.fichainventario 
                left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
                WHERE fin.ficha = $ficha
                GROUP BY prdes.producto
                ORDER BY prdes.producto,elm.id_unico ASC";
$resultprofichas =$mysqli->query($sqlprofichas);
while($filas = $resultprofichas->fetch_row()){
    $productos[]=$filas[0];
}
#agregamos un campo vacio para que el conteo de las posiciones sea similar
array_unshift($productos, "");
#Si la función de validación retorna verdadero entonces el valor que se envio fue una fecha
#pero si retorna falso el valor enviado no fue una fecha sino un string como "Negro" o "1"
#"Negro" hace referencia a color
if (validateDate($fechaT)==TRUE){
    $valorF = explode("/",$fechaT);
    $valor =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';             
}else{
    $valor = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';
}
$sqlproficha = "SELECT prdes.producto
                FROM gf_producto_especificacion prdes 
                LEFT JOIN gf_ficha_inventario fin ON fin.id_unico = prdes.fichainventario 
                left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
                WHERE fin.ficha = $ficha AND prdes.valor=$valor
                GROUP BY prdes.producto
                ORDER BY prdes.producto,elm.id_unico ASC";
$resultproficha =$mysqli->query($sqlproficha);
$pos = mysqli_num_rows($resultproficha);
$fila = $resultproficha->fetch_row();
#Buscamos la id del producto del array para tomar la posición
$posicion =  array_search($fila[0], $productos);
#Imprimimos la posición
echo json_encode($posicion);
//Función para validar que el valor enviado sea una fecha
function validatedate($date){
    $d = DateTime::createFromFormat('d/m/Y', $date);
    return $d && $d->format('d/m/Y') === $date;
}
?>

