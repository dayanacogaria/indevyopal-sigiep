<?php
#Aqui esta la forma en que se arma un json de forma dinamica
session_start();
require_once '../Conexion/conexion.php';
$i=0;
$tabla = "";
$codigo = $_GET['codigo'];
$fechaInicial = $_GET['fechaI'];
$valorFechaI = explode("/", $fechaInicial);
$fechaI = $valorFechaI[2].'-'.$valorFechaI[1].'-'.$valorFechaI[0];
$fechaFinal = $_GET['fechaF'];
$valorFechaFinal = explode("/", $fechaFinal);
$fechaF=$valorFechaFinal[2].'-'.$valorFechaFinal[1].'-'.$valorFechaFinal[0];
$sql = "SELECT DISTINCT tpc.nombre,cnt.numero,cnt.fecha,ct.naturaleza,dtc.valor,dtc.descripcion,cnt.tercero,dtc.centrocosto,dtc.proyecto
FROM gf_cuenta ct
LEFT JOIN gf_detalle_comprobante dtc ON dtc.cuenta = ct.id_unico
LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico
LEFT JOIN gf_tipo_comprobante tpc ON cnt.tipocomprobante = tpc.id_unico
WHERE ct.codi_cuenta = '$codigo' AND  cnt.fecha BETWEEN ('$fechaI') AND ('$fechaF')";
$result = $mysqli->query($sql);
$filas =$result->num_rows;
if($filas>=0){
    while($row=$result->fetch_row()){
        $valorFecha=  explode("-", $row[2]);
        $fecha = $valorFecha[2].'/'.$valorFecha[1].'/'.$valorFecha[0];
        #Captura del valor debito por su naturaleza
        $debtido = "";
        if($row[3]==1){
            if($row[4]>=0){
                $debtido=  number_format($row[4], 2, '.', ',');
            }else{
                $debtido = '0.00';
            }
        }else if($row[3]==2){
            if($row[4] <= 0){
                $x = (float) substr($row[4],'1');
                $debtido = number_format($x, 2,'.', ',');
            }else{
                $debtido = '0.00';
            }
        }
        #captura del valor crédito
        $credito = "";
        if ($row[3] == 2) {
            if($row[4] >= 0){
                $credito = number_format($row[4], 2, '.', ',');
            }else{
                $credito = '0.00';
            }
        }else if($row[3] == 1){
            if($row[4] <= 0){
                $y = (float) substr($row[4],'1');
                $credito = number_format($y, 2, '.', ',');
            }else{
                $credito = '0.00';
            }
        }
        $tercero = $row[6];
        $sqlTercero = "SELECT DISTINCT IF(CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos) IS NULL OR CONCAT(ter.nombreuno,' ', ter.nombredos, ' ', ter.apellidouno, ' ', ter.apellidodos)='',
                    (ter.razonsocial),CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)) AS 'NOMBRE', 
                    ter.id_unico, CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                    WHERE ter.id_unico=$tercero";
        $resultTercero = $mysqli->query($sqlTercero);
        $persona = mysqli_fetch_row($resultTercero);
        #centro de costo
        $sqlCentroCosto = "SELECT nombre FROM gf_centro_costo WHERE id_unico = $row[7]";
        $resultCentroCosto = $mysqli->query($sqlCentroCosto);
        $centroCosto = mysqli_fetch_row($resultCentroCosto);
        #proyecto
        $sqlProyecto = "SELECT nombre FROM gf_proyecto WHERE id_unico = $row[8]";
        $resultProyecto = $mysqli->query($sqlProyecto);
        $proyecto = mysqli_fetch_row($resultProyecto);
        #En el string $tabla armamos nuestro json el cual depende de los datos consultados
        $tabla.='{"Tipo":"'.ucwords(strtolower($row[0])).'","Número":"'.ucwords(strtolower($row[1])).'","Fecha":"'.$fecha.'","Valor Débito":"'.$debtido.'","Valor Crédito":"'.$credito.'","Descripción":"'.ucwords(strtolower($row[5])).'","Tercero":"'.ucwords(strtolower($persona[0])).'","Centro Costo":"'.ucwords(strtolower($centroCosto[0])).'","Proyecto":"'.ucwords(strtolower($proyecto[0])).'"},';
        $i++;        
    }
    #Aqui le hacemos un substring y le quitamos una , al valor impreso
    $tabla = substr($tabla,0, strlen($tabla) - 1);
    echo '{"data":['.$tabla.']}';
}else{
    echo '<tr><td class="text-center" colspan="12" class="text-center"><p>No Existen Registros...</p><td><tr/>';
}
?>