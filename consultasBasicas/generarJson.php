<?php
session_start();
require_once '../Conexion/conexion.php';
#Declaración de arrays
#En total son tres arrays incluyendo el envio $_GET el cual por si solo es un array
$filas="";
$datos="";
//Declaración de la variable $ficha con el valor enviado por get
$ficha=$_GET['ficha'];
$descripcion=$_GET['txtDescripcion'];
$cantidad = $_GET['cantidad'];
$valor = $_GET['valor'];
$movimiento = $_GET['movimiento'];
#Eliminamos del array $_GET los valores que conocemos pero que no son necesarios
unset($_GET['chkTodos']);
unset($_GET['btnGuardarProducto']);
unset($_GET['ficha']);
unset($_GET['cantidad']);
unset($_GET[NULL]);
unset($_GET['txtDescripcion']);
unset($_GET['valor']);
unset($_GET['movimiento']);
unset($_GET['_']);
#En este while desplegamos la información del get
while (list($clave,$valor) = each($_GET)) {           
    $filas.='"'.$valor.'",';           
}
#Aqui quitamos una ,
$filas = substr($filas,0, strlen($filas) - 1);
#Al valor resultante le agregamos []
$filas='["",'.$filas.']';
#Hacemos una consulta para concer que campos son autoincrementables
#y aumentarlos
$sql = "select                 
        elm.nombre,        
        fin.autogenerado
from gf_ficha_inventario fin 
left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
WHERE fin.ficha = $ficha ORDER BY elm.id_unico";
$result = $mysqli->query($sql);
while ($campo = mysqli_fetch_row($result)){
    
}
#En este while generamos los valores dependiendo de la cantidad
for($index=1;$index<=$cantidad;$index++){
    $datos.=$filas.",";
}
#Le quitamos una coma
$datos = substr($datos,0, strlen($datos) - 1);
#Imprimimos los valores resultantes en un json
echo '{"data":['.$datos.']}';
?>