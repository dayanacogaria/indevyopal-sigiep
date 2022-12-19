<?php
session_start();
require_once '../Conexion/conexion.php';
$saldo = $_POST['saldo'];
$anno = date('Y');
switch ($saldo) {
    case 1:
        $cuenta = $_POST['cuenta'];
        $fechaI = $_POST['fecha'];
        $valorFechaI = explode("/", $fechaI);
        $fechaInical = $valorFechaI[2].'-'.$valorFechaI[1].'-'.$valorFechaI[0];
        $sql1 = "SELECT DISTINCT SUM(dtc.valor) FROM gf_cuenta ct LEFT JOIN gf_detalle_comprobante dtc ON dtc.cuenta = ct.id_unico LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico LEFT JOIN gf_tipo_comprobante tpc ON cnt.tipocomprobante = tpc.id_unico WHERE cnt.fecha BETWEEN '".$anno.'-01-01'."' AND ('$fechaInical') AND ct.codi_cuenta = '$cuenta'";
        $result1 = $mysqli->query($sql1);
        $SaldoI = mysqli_fetch_row($result1);
        echo number_format($SaldoI[0],2,'.',',');
        break;
    case 2:
        $cuenta2 = $_POST['cuenta'];
        $fechaF = $_POST['fechaFin'];
        $valorFechaI = explode("/", $fechaF);
        $fechaFin = $valorFechaI[2].'-'.$valorFechaI[1].'-'.$valorFechaI[0];
        $sql2 = "SELECT DISTINCT SUM(dtc.valor) FROM gf_cuenta ct LEFT JOIN gf_detalle_comprobante dtc ON dtc.cuenta = ct.id_unico LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico LEFT JOIN gf_tipo_comprobante tpc ON cnt.tipocomprobante = tpc.id_unico WHERE cnt.fecha BETWEEN '".$anno.'-01-01'."' AND ('$fechaFin') AND ct.codi_cuenta = '$cuenta2'";
        $result2 = $mysqli->query($sql2);
        $SaldoFin = mysqli_fetch_row($result2);
        echo number_format($SaldoFin[0],2,'.',',');
        break;
    case 3:
        unset($_SESSION['data']);
        #Consulta para generar un array el cual se forma por la consulta de las requisiciones,
        #este array será el detalle guardado en la tabla de movimientos,
        #Este data pertenece a orden de compra
        $i = 0;
        $requisicion1 = array();
        $values = $_POST['values'];
        $sqlIva = "SELECT valor FROM gs_parametros_basicos WHERE id_unico=2";
        $resultIva = $mysqli->query($sqlIva);
        $iva = mysqli_fetch_row($resultIva);
        $sql1 = "SELECT 
                    dtm.id_unico, 
                    pl.id_unico,
                    CONCAT(pl.codi,' - ',pl.nombre) as planI,                                       
                    SUM(dtm.cantidad) as cantidad,
                    (dtm.valor) AS valor                                          
        FROM gf_detalle_movimiento dtm
        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
        WHERE mv.id_unico IN ($values)
        GROUP BY pl.id_unico";
        $result1 = $mysqli->query($sql1);
        while ($row = mysqli_fetch_row($result1)){
            $total1 = $row[3]*$row[4];
            $valorIva = ($total1*$iva[0])/100;
            $valorTotal = $total1+$valorIva;
            $requisicion1[] = array("id_planI"=>$row[1],"nombrePlan"=>$row[2],"cantidad"=>$row[3],"valor"=>$row[4],"iva"=>$valorIva,"valorTotal"=>$valorTotal);            
            $i++;
        }        
        $_SESSION['data'] = $requisicion1;        
        break;  
    case 4:
        unset($_SESSION['data']);
        #Consulta para generar un array el cual se forma por la consulta de las requisiciones,
        #este array será el detalle guardado en la tabla de movimientos de manera individual
        $i = 0;
        $requisicion = array();
        $values = $_POST['values'];
        $sqlIva = "SELECT valor FROM gs_parametros_basicos WHERE id_unico=2";
        $resultIva = $mysqli->query($sqlIva);
        $iva = mysqli_fetch_row($resultIva);
        $sql = "SELECT 
                    dtm.id_unico, 
                    pl.id_unico,
                    CONCAT(pl.codi,' - ',pl.nombre) as planI,                                       
                    SUM(dtm.cantidad) as cantidad,
                    (dtm.valor),
                    (dtm.valor)*cantidad as total                                         
        FROM gf_detalle_movimiento dtm
        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
        WHERE mv.id_unico IN ($values)
        GROUP BY pl.id_unico";
        $result = $mysqli->query($sql);
        while ($row = mysqli_fetch_row($result)){
            $valorIva = ($row[4]*$iva[0])/100;
            $valorTotal = $row[5]+$valorIva;
            $requisicion[] = array("id_planI"=>$row[1],"nombrePlan"=>$row[2],"cantidad"=>$row[3],"valor"=>$row[4],"iva"=>$valorIva,"valorTotal"=>$valorTotal);            
            $i++;
        }        
        $_SESSION['data'] = $requisicion;
        break;
    case 5:
        unset($_SESSION['data']);
        #consulta para armado de data de insertado para salida de almacen
        $i = 0;
        $requisicion = array();
        $values = $_POST['values'];
        $sqlIva = "SELECT valor FROM gs_parametros_basicos WHERE id_unico=2";
        $resultIva = $mysqli->query($sqlIva);
        $iva = mysqli_fetch_row($resultIva);
        $sql = "SELECT 
                    dtm.id_unico, 
                    pl.id_unico,
                    CONCAT(pl.codi,' - ',pl.nombre) as planI,                                       
                    (dtm.cantidad) as cantidad,
                    (dtm.valor),
                    (dtm.valor)*cantidad as total                                        
        FROM gf_detalle_movimiento dtm
        LEFT JOIN gf_movimiento mv ON dtm.movimiento = mv.id_unico
        LEFT JOIN gf_plan_inventario pl ON dtm.planmovimiento = pl.id_unico
        WHERE mv.id_unico IN ($values)
        GROUP BY pl.id_unico";
        $result = $mysqli->query($sql);
        while ($row = mysqli_fetch_row($result)){
            $valorIva = ($row[4]*$iva[0])/100;
            $valorTotal = $row[5]+$valorIva;
            $requisicion[] = array("id_planI"=>$row[1],"nombrePlan"=>$row[2],"cantidad"=>$row[3],"valor"=>$row[4],"iva"=>$valorIva,"valorTotal"=>$valorTotal,"idDetalle"=>$row[0]);            
            $i++;
        }        
        $_SESSION['data'] = $requisicion;
        break;
}
?>

