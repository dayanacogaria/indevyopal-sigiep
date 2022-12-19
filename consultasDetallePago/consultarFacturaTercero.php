<?php
session_start();
require_once '../Conexion/conexion.php';
$funcion = $_POST['funcion'];
switch ($funcion) {
    case 1:       
        $tercero = $_POST['tercero'];
        if(!empty($tercero)){
        echo '<option value="">Factura</option>';
        $sql = "SELECT DISTINCT fat.id_unico,fat.numero_factura,tpf.nombre,fat.fecha_factura FROM gp_factura fat
                LEFT JOIN gp_detalle_factura dtf ON dtf.factura = fat.id_unico
                LEFT JOIN gp_detalle_pago dtp ON dtf.id_unico = dtp.detalle_factura
                LEFT JOIN gp_tipo_factura tpf ON fat.tipofactura = tpf.id_unico
                WHERE fat.tercero=$tercero AND (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0";
        $result = $mysqli->query($sql);
        while($fila = mysqli_fetch_row($result)){
            $factura = $fila[0];
            $sql4 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                    FROM gp_detalle_factura dtf
                    WHERE dtf.factura = $factura";
            $result4 = $mysqli->query($sql4);
            $row4 = mysqli_fetch_row($result4);
            $sql100 = "SELECT DISTINCT IFNULL($row4[0]-SUM(dtp.valor),$row4[0]) AS ULTIMO 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                    WHERE dtf.factura = $factura";
            $result100 = $mysqli->query($sql100);
            $row5= mysqli_fetch_row($result100);
            if($row5[0]==0){                
            }else{
                echo '<option value="'.$fila[0].'">'.'Tipo Factura: '.ucwords(strtolower($fila[2].' '.$fila[1].' - Saldo:'.$row5[0].' - Fecha:'.$fila[3])).'</option>';
            }
        }
        }else{
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
        break;

    case 2:
        $tipo = $_POST['tipo'];        
        $tercero = $_POST['tercero'];
        $sql2 = "SELECT ter.id_unico                                                     
                FROM  gf_tercero ter
                WHERE IF(CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)='' OR ter.razonsocial='',ter.razonsocial,CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos))  = '$tercero'";
        $result2 = $mysqli->query($sql2);
        $fila2 = $result2->fetch_row();
        $tipo = $_POST['tipo'];
        echo '<option value="">Factura</option>';
        $sql3 = "SELECT DISTINCT fat.id_unico,fat.numero_factura  FROM gp_factura fat
                LEFT JOIN gp_detalle_factura dtf ON dtf.factura = fat.id_unico
                WHERE fat.tercero=$fila2[0] AND fat.tipofactura=$tipo AND (SELECT SUM(dtf.valor_total_ajustado) FROM gp_detalle_factura dft WHERE dtf.factura = fat.id_unico)>0";
        $result3 = $mysqli->query($sql3);
        while($fila3=$result3->fetch_row()){
            $factura = $fila3[0];
            $sql41 = "SELECT DISTINCT SUM(dtf.valor_total_ajustado) AS ULTIMO 
                    FROM gp_detalle_factura dtf
                    WHERE dtf.factura = $fila3[0]";
            $result41 = $mysqli->query($sql41);
            $row41 = mysqli_fetch_row($result41);
            $sql1001 = "SELECT DISTINCT IFNULL($row41[0]-SUM(dtp.valor),$row41[0]) AS ULTIMO 
                    FROM gp_detalle_pago dtp
                    LEFT JOIN gp_detalle_factura dtf ON dtp.detalle_factura = dtf.id_unico
                    WHERE dtf.factura = $fila3[0]";
            $result1001 = $mysqli->query($sql1001);
            $row5 = mysqli_fetch_row($result1001);
            if($row5[0]==0){
            }else{
                echo '<option value="'.$fila3[0].'">'.$fila3[1].' - Saldo:'.$row5[0].'</option>';
            }
        }                        
        break;
}
?>