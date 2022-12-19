<?php 
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
session_start();
$id = $_GET['id'];
$con = new ConexionPDO();
#** Numero Y Tipo Cnt Detalle Pptal **#
$dt = $con->Listar("SELECT cn.id_unico FROM gf_detalle_comprobante_pptal dp 
LEFT JOIN gf_comprobante_pptal cp On dp.comprobantepptal = cp.id_unico 
LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
LEFT JOIN gf_tipo_comprobante tcc ON tc.id_unico = tcc.comprobante_pptal 
LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tcc.id_unico AND cn.numero = cp.numero 
WHERE dp.id_unico = $id");
if(count($dt)>0){
    $id_cnt = $dt[0][0];
} else {
    $id_cnt ="";
}

#** Buscar Detalle Afectado ***#
$ca = $con->Listar("SELECT comprobanteafectado FROM gf_detalle_comprobante_pptal WHERE id_unico = $id");
if(count($ca)>0){
    $cnt = $con->Listar("SELECT dc.id_unico 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico         
        WHERE dc.detallecomprobantepptal = ".$ca[0][0]." AND tc.clasecontable = 14");
    if(count($cnt)>0){
        if(empty($id_cnt)){
            $resultado = true;
        }else {
        $deleteSQL = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_cnt AND id_unico = ".$cnt[0][0];
        $resultado = $mysqli->query($deleteSQL);
        }
        
        if($resultado == true){
        $deleteSQL = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        $resultado = $mysqli->query($deleteSQL);
        } else {
            $resultado = false;
        }
    } else {
        $deleteSQL = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        $resultado = $mysqli->query($deleteSQL);
    }
} else {

$deleteSQL = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
$resultado = $mysqli->query($deleteSQL);
}
  echo json_encode($resultado);
?>