<?php
session_start();
require_once '../Conexion/conexion.php';
$tipo = $_POST['tipo'];
$sql = "SELECT DISTINCT fat.id_unico,fat.numero_factura,tpf.nombre,fat.fecha_factura FROM gp_factura fat
        LEFT JOIN  gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
        LEFT JOIN gp_detalle_factura dtf ON dtf.factura = fat.id_unico 
        WHERE tpf.id_unico = $tipo AND (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0
        ORDER BY fat.fecha_factura";
$result = $mysqli->query($sql);
$cantidad = $result->num_rows;
echo '<option value="">Factura</option>';
if($cantidad!=0){
    while($row = $result->fetch_row()){
        $sql4 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                    FROM gp_detalle_factura dtf
                    WHERE dtf.factura = $row[0]";
            $result4 = $mysqli->query($sql4);
            $row4 = mysqli_fetch_row($result4);
            $sql100 = "SELECT DISTINCT IFNULL($row4[0]-SUM(dtp.valor),$row4[0]) AS ULTIMO 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                    WHERE dtf.factura = $row[0]";
            $result100 = $mysqli->query($sql100);
            $row5= mysqli_fetch_row($result100);
            if($row5[0]==0){
            }else{
                echo '<option value="'.$row[0].'">'.ucwords(strtolower('Tipo Factura: '.$row[2].'    '.$row[1].'    -   Saldo:'.$row5[0].'     -   Fecha:'.$row[3])).'</option>';
            }        
    }
}else{
    echo '<option value="">Factura</option>';
    echo '<option value="">Factura</option>';
    $sqlFacturas= "SELECT DISTINCT fat.id_unico,fat.numero_factura,tpf.nombre,fat.fecha_factura FROM gp_factura fat
    LEFT JOIN  gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
    LEFT JOIN gp_detalle_factura dtf ON dtf.factura = fat.id_unico 
    WHERE (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0
    ORDER BY fat.fecha_factura";
    $resultFacturas = $mysqli->query($sqlFacturas);                                   
    $cantidad = $resultFacturas->num_rows;
    if($cantidad!=0){
        while($row = $resultFacturas->fetch_row()){
            $sql4 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                        FROM gp_detalle_factura dtf
                        WHERE dtf.factura = $row[0]";
            $result4 = $mysqli->query($sql4);
            $row4 = mysqli_fetch_row($result4);
            $sql100 = "SELECT DISTINCT IFNULL($row4[0]-SUM(dtp.valor),$row4[0]) AS ULTIMO 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                    WHERE dtf.factura = $row[0]";
            $result100 = $mysqli->query($sql100);
            $row5= mysqli_fetch_row($result100);
            if($row5[0]==0){
            }else{
                echo '<option value="'.$row[0].'">'.ucwords(strtolower('Tipo Factura: '.$row[2].'    '.$row[1].'    -   Saldo:'.$row5[0].'     -   Fecha:'.$row[3])).'</option>';
            }        
        }
    }
}
?>