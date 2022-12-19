<?php
session_start();
require_once '../Conexion/conexion.php';
$rubro = $_REQUEST['rubro'];
$concepto = $_REQUEST['concepto'];
$es = "SELECT rubro FROM gf_rubro_fuente WHERE id_unico = $rubro";
$vall = $mysqli->query($es);
$valor=  mysqli_fetch_row($vall);
//echo '<option value="">Cuenta</option>';
$sql = "SELECT DISTINCT
                    cnt.id_unico cuenta,
                    cnt.codi_cuenta,
                    cnt.nombre
        FROM gf_concepto ct
        LEFT JOIN gf_concepto_rubro cnr ON cnr.concepto = ct.id_unico
        LEFT JOIN gf_rubro_fuente rbf ON rbf.rubro = cnr.rubro
        LEFT JOIN gf_rubro_pptal rb ON rbf.rubro = rb.id_unico
        LEFT JOIN gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro
        LEFT JOIN gf_cuenta cnt ON ctrb.cuenta_debito = cnt.id_unico
        WHERE rb.id_unico = $valor[0] AND ct.id_unico = $concepto";
$res = $mysqli->query($sql);
while($row = mysqli_fetch_row($res)){
    echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1].' '.$row[2])).'</option>';
}
?>