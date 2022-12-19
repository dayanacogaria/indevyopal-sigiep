<?php
    require_once('Conexion/conexion.php');
     session_start();

//Tipoclase 6, ingresos en la tabla tipo clase
//Tipoclase 7, gastos en la tabla tipo clase
 /**/
$id = $_REQUEST['id'];    

$ingresos = 0;
$gastos = 0; 

$querySQL = "SELECT det.valor, tipclap.id_unico
from gf_detalle_comprobante_pptal det
left join gf_comprobante_pptal com on  com.id_unico = det.comprobantepptal 
left join gf_rubro_fuente rubf on rubf.id_unico = det.rubrofuente 
left join gf_fuente fue on fue.id_unico = rubf.fuente 
left join gf_tipo_comprobante_pptal tipc on tipc.id_unico = com.tipocomprobante 
left JOIN gf_rubro_pptal rub on rub.id_unico = rubf.rubro 
left join gf_tipo_clase_pptal tipclap on tipclap.id_unico = rub.tipoclase 
where (com.id_unico = $id 
and rub.tipoclase = 7) or (com.id_unico = $id 
and rub.tipoclase = 6)";

$resultado = $mysqli->query($querySQL);

while($row = mysqli_fetch_row($resultado))
{
    if($row[1] == 6)
        $ingresos += $row[0];
    elseif($row[1] == 7)
        $gastos += $row[0];
}

/**/
if($ingresos == $gastos)
    echo 2; // Las fuentes están balanceadas.
else
    echo 1; //Las fuentes no están balanceadas.

?>
