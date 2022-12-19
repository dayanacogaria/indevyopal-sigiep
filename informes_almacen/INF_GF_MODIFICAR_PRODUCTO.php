<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Listado_Productos.xls");
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
@session_start();
ini_set('max_execution_time', 0);
ob_start();
$con = new ConexionPDO();
$parmanno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$annionom   = anno($parmanno);
$calendario = CAL_GREGORIAN;
$anno       = $annionom; 
$fecha      = fechaC($_REQUEST['txtFechaFinal']);

#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];

$rowf = $con->Listar("SELECT DISTINCT ef.id_unico, ef.nombre FROM gf_movimiento m 
LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento 
LEFT JOIN gf_producto p ON mp.producto = p.id_unico
LEFT JOIN gf_producto_especificacion pe ON p.id_unico = pe.producto 
LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico
LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
WHERE m.compania = $compania  and ef.id_unico IS NOT NULL
ORDER BY ef.id_unico ");
$html = '';
$nl = count($rowf)+5;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listado de Productos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="<?php echo $nl;?>" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>LISTADO DE PRODUCTOS Y CARACTERÍSTICAS   
        <br/>&nbsp;                 
        </strong> 
    </th>  
    <?php 
    # BUSCAR NOMBRE TIPO
    $html .= '<tr>';
    $html .= '<td><strong>Nombre Activo</strong></td>';
    $html .= '<td><strong>Descripción</strong></td>';
    $html .= '<td><strong>Valor</strong></td>';
    $html .= '<td><strong>Vida Útil</strong></td>';
    $html .= '<td><strong>Fecha Adquisición</strong></td>';
    for ($i = 0; $i < count($rowf); $i++) {
        $html .= '<td><strong>'.$rowf[$i][1].'</strong></td>';
    }
    $html .= '</tr>';
    $row = $con->Listar("SELECT DISTINCT  p.id_unico, 
        CONCAT_WS(' ',pi.codi, pi.nombre),p.descripcion,p.valor, p.vida_util_remanente, DATE_FORMAT(p.fecha_adquisicion,'%d/%m/%Y') , 
        pi.ficha 
     FROM gf_movimiento m 
     LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
     LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento 
     LEFT JOIN gf_producto p ON mp.producto = p.id_unico
     LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
     WHERE m.compania = $compania  and p.id_unico IS NOT NULL
     ORDER BY p.id_unico");
    for ($r = 0; $r < count($row); $r++) {
        $html .= '<tr>';
        $html .= '<td>'.$row[$r][1].'</td>';
        $html .= '<td>'.$row[$r][2].'</td>';
        $html .= '<td>'.number_format($row[$r][3],2,',','.').'</td>';
        $html .= '<td>'.$row[$r][4].'</td>';  
        $html .= '<td>'.$row[$r][5].'</td>';
        for ($i = 0; $i < count($rowf); $i++) {
            $dp = $con->Listar("SELECT DISTINCT  pe.id_unico, pe.valor,  fi.autogenerado, fi.id_unico
                 FROM gf_producto_especificacion pe
                 LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico 
                 WHERE pe.producto = ".$row[$r][0]." 
                 AND fi.elementoficha=  ".$rowf[$i][0]);
            if(empty($dp[0][0])){
                $html .= '<td></td>';
            } else { 
                $html .= '<td>'.$dp[0][1].'</td>';
            }
        }
        $html .= '</tr>';
    }
    echo $html;
    ?>
</table>
</body>
</html>