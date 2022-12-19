<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Seguimiento_Placa.xls");
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

$elemento       = $_REQUEST['elemento'];
$productoI      = $_REQUEST['productoI'];
$productoF      = $_REQUEST['productoF'];
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

# BUSCAR NOMBRE ELEMENTO
    
$el = $con->Listar("SELECT codi, nombre 
    FROM gf_plan_inventario WHERE id_unico = $elemento");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Seguimiento Placas</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="21" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>SEGUIMIENTO DE PLACAS
        <br/>ELEMENTO <?= $el[0][0].' - '.$el[0][1]?>
        <br/>&nbsp;                 
        </strong> 
    </th>   
    <?php 
    $html = "";
    $html .= '<tr>';
    $html .= '<td rowspan="2" align="center"><strong>PLACA</strong></td>';
    $html .= '<td rowspan="2" align="center"><strong>VALOR</strong></td>';
    $html .= '<td rowspan="2" align="center"><strong>ESPECIFICACIONES</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>ENTRADA</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>SALIDA</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>TRASLADO</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>REINTEGRO</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>REQUISICIÓN</strong></td>';
    $html .= '<td colspan="3" align="center"><strong>BAJA</strong></td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '<td align="center"><strong>TIPO</strong></td>';
    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
    $html .= '<td align="center"><strong>FECHA</strong></td>';
    $html .= '</tr>';
    #* Buscar Elementos 
    $rowd = $con->Listar("SELECT DISTINCT p.id_unico  
        FROM gf_producto_especificacion pe 
        LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
        LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
        LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
        LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
        LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
        WHERE m.compania = $compania AND fi.elementoficha = 6  
        AND pe.valor !=''  AND dm.planmovimiento = $elemento   
        AND CAST(pe.valor AS UNSIGNED) BETWEEN '$productoI' AND '$productoF'
        GROUP BY pe.valor 
        ORDER BY CAST(pe.valor AS UNSIGNED)  ASC ");
    for ($i = 0; $i < count($rowd); $i++) {            
        #CONSULTAS MOVIMIENTOS
        $dt1e = detalles(2, $rowd[$i][0]);
        $dt1s = detalles(3, $rowd[$i][0]);
        $dt1t = detalles(5, $rowd[$i][0]);
        $dt1n = detalles(6, $rowd[$i][0]);
        $dt1r = detalles(4, $rowd[$i][0]);
        $dtbj = detalles(7, $rowd[$i][0]);
        if(!empty($dt1e[0][0]) || !empty($dt1s[0][0]) || !empty($dt1t[0][0]) 
            || !empty($dt1r[0][0]) || !empty($dt1n[0][0]) || !empty($dtbj[0][0]) ){
            #Datos Producto 
            $da = $dt1e[0][8];
            $dp = datosp($rowd[$i][0]);
            $html .= '<tr>';
            $html .= '<td>'.$dt1e[0][10].'</td>';
            $html .= '<td>'.number_format($dt1e[0][3],2,'.',',').'</td>';
            $html .= '<td>';
            for ($p = 0; $p < count($dp); $p++) {
                $html.=$dp[$p][0].': '.$dp[$p][1].'<br/>';
            }
            $html .= '</td>';
            $html .= '<td>'.$dt1e[0][4].' - '.$dt1e[0][5].'</td>';
            $html .= '<td>'.$dt1e[0][6].'</td>';
            $html .= '<td>'.$dt1e[0][7].'</td>';            
            $html .= '<td>'.$dt1s[0][4].' - '.$dt1s[0][5].'</td>';
            $html .= '<td>'.$dt1s[0][6].'</td>';
            $html .= '<td>'.$dt1s[0][7].'</td>';              
            $html .= '<td>'.$dt1t[0][4].' - '.$dt1t[0][5].'</td>';
            $html .= '<td>'.$dt1t[0][6].'</td>';
            $html .= '<td>'.$dt1t[0][7].'</td>';     
            $html .= '<td>'.$dt1n[0][4].' - '.$dt1n[0][5].'</td>';
            $html .= '<td>'.$dt1n[0][6].'</td>';
            $html .= '<td>'.$dt1n[0][7].'</td>';             
            $html .= '<td>'.$dt1r[0][4].' - '.$dt1r[0][5].'</td>';
            $html .= '<td>'.$dt1r[0][6].'</td>';
            $html .= '<td>'.$dt1r[0][7].'</td>'; 
            $html .= '<td>'.$dtbj[0][4].' - '.$dtbj[0][5].'</td>';
            $html .= '<td>'.$dtbj[0][6].'</td>';
            $html .= '<td>'.$dtbj[0][7].'</td>'; 
            $html .= '</tr>';
        }
    }
    echo $html;
 

function detalles($clase, $producto ){
    global $con;
    global $compania;
    global $fecha;
    
    $row = $con ->Listar("SELECT DISTINCT dm.id_unico, pi.codi, pi.nombre, 
    dm.valor, tm.sigla, tm.nombre, m.numero, DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
    dm.detalleasociado , mp.producto, pe.valor 
    FROM gf_movimiento_producto mp 
    LEFT JOIN gf_detalle_movimiento dm ON dm.id_unico = mp.detallemovimiento
    LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
    LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
    LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
    LEFT JOIN gf_producto p ON mp.producto = p.id_unico 
    LEFT JOIN gf_producto_especificacion pe ON p.id_unico = pe.producto AND pe.fichainventario = 6 
    WHERE m.compania = $compania AND tm.clase =$clase 
        AND p.id_unico = $producto");
    return $row;
}

function datosp($id_producto){
    global $con;
    $row = $con->Listar("SELECT ef.nombre, pe.valor FROM gf_producto_especificacion pe 
    LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico 
    LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico
    WHERE pe.producto = $id_producto AND pe.fichainventario !=6");
    
    return $row;
}
?>

    