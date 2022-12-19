<?php
##################################################################################################################################################################
# Modificaciones
##################################################################################################################################################################
# 27/07/2017   |ERICA G. | No modificaba
##################################################################################################################################################################
session_start();
//Archivos adjuntos
require_once '../Conexion/conexion.php';
//Capturamos el id
$id=$_POST['id'];
//Formateamos la fecha
$fechaT = ''.$mysqli->real_escape_string(''.$_POST['fecha'].'').'';
$valorF = explode("/",$fechaT);
$fecha =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';
//captura de cuenta banco
$banco = $_POST['banco'];
//Tercero 
$tercero = $_POST['tercero'];
//Consulta para obtener los comprobantes cnty pptal
$sqlC = "SELECT     
                    dtc.comprobante,
                    dtp.comprobantepptal
        FROM        gp_detalle_pago dp
        LEFT JOIN   gf_detalle_comprobante dtc          ON  dtc.id_unico = dp.detallecomprobante
        LEFT JOIN   gf_detalle_comprobante_pptal dtp    ON  dtp.id_unico = dtc.detallecomprobantepptal
        WHERE       dp.pago = $id";
$resultC = $mysqli->query($sqlC);
$rowC = $resultC->fetch_row();
$cantidad = mysqli_num_rows($resultC);
//Validamos que no este vacio
if($cantidad > 0) {
	//Consulta de actualización de comprobante cnt
	$sqlCnt = "UPDATE gf_comprobante_cnt SET fecha = $fecha, tercero= $tercero WHERE id_unico = $rowC[0]";
	$resultCnt = $mysqli->query($sqlCnt);
	//Consulta de actualiación de comprobante pptal
	$sqlPptal = "UPDATE gf_comprobante_pptal SET fecha = $fecha, tercero = $tercero, responsable = $tercero WHERE id_unico = $rowC[1]";
	$resultPptal = $mysqli->query($sqlPptal);
}
//Consulta de actualización
$sql = "UPDATE gp_pago SET fecha_pago=$fecha,banco=$banco,responsable=$tercero WHERE id_unico=$id";
$result = $mysqli->query($sql);
echo json_encode($result);
?>

