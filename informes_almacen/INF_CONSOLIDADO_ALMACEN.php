<?php
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
@session_start();
ini_set('max_execution_time', 0);
ob_start();
$con = new ConexionPDO();
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Consolidado_Almacen.xls");
$parmanno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$anno       = anno($parmanno);
$calendario = CAL_GREGORIAN;
$mesI       = $mysqli->real_escape_string(''.$_POST['periodoF'].'');
$diaF       = cal_days_in_month($calendario, $mesI, $anno); 
$fechaFinal = $anno.'-'.$mesI.'-'.$diaF;
$fechac     = $diaF.'/'.$mesI.'/'.$anno;    

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
$tipo        = $_REQUEST['Tipo_Informe'];
$html        = "";
switch ($tipo){
    #Consolidado Almacén
    case 1:
        $titulo = 'Consolidado Cuenta Activo Acumulado';
        $n      = 5;
        $ta     = 0;
        $tc     = 0;
        $td     = 0;
        $html  .= '<tr><td colspan="5"><strong><center><i>ELEMENTOS CON CUENTA</i></center></strong></td></tr>';
        $html  .= '<tr>';
        $html  .= '<td><strong>CÓDIGO CUENTA</strong></td>';
        $html  .= '<td><strong>NOMBRE</strong></td>';
        $html  .= '<td><strong>VALOR ALMACÉN</strong></td>';
        $html  .= '<td><strong>VALOR CONTABLE</strong></td>';
        $html  .= '<td><strong>DIFERENCIA</strong></td>';
        $html  .= '</tr>';
        $row = $con->Listar("SELECT DISTINCT pe.valor
            FROM gf_producto_especificacion pe 
            LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
            LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
            LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
            AND pi.tipoinventario = 2 
            AND m.fecha <='$fechaFinal' 
            AND pe.valor!='' 
            ORDER BY pe.valor  ASC");
        for ($i = 0; $i < count($row); $i++) {
            $codigo = $row[$i][0];
            
            $bsv = $con->Listar("SELECT DISTINCT dm.id_unico, dm.valor, dm.cantidad 
            FROM gf_producto_especificacion pe 
            LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
            LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
            LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
            AND m.fecha <='$fechaFinal' 
            AND pe.valor = '$codigo' 
            AND pi.tipoinventario = 2 
            ORDER BY pe.valor  ASC"); 
            $tve = 0;
            for ($v = 0; $v < count($bsv); $v++) {
                $tve +=$bsv[$v][1]*$bsv[$v][2] ;
            }
            $vc     =$con->Listar("SELECT IF(c.codi_cuenta !='',c.codi_cuenta, $codigo), c.nombre,SUM(dm.valor) FROM gf_cuenta c 
                LEFT JOIN gf_detalle_comprobante dm ON c.id_unico = dm.cuenta
                LEFT JOIN gf_comprobante_cnt cn ON dm.comprobante = cn.id_unico 
                LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
                WHERE c.codi_cuenta = '$codigo' AND pa.compania = $compania  AND pa.anno = '$anno'
                AND cn.parametrizacionanno = pa.id_unico 
                AND cn.fecha <='$fechaFinal'");
            $diferencia = $tve-$vc[0][2];
            $html .='<tr>';
            $html .='<td>'.$vc[0][0].'</td>';
            $html .='<td>'.$vc[0][1].'</td>';
            $html .='<td>'.number_format($tve,2,'.',',').'</td>';
            $html .='<td>'.number_format($vc[0][2],2,'.',',').'</td>';
            $html .='<td>'.number_format($diferencia,2,'.',',').'</td>';
            $html .='</tr>';
            $ta   += $tve;
            $tc   += $vc[0][2];
            $td   += $diferencia;
        }
        $html .='<tr>';
        $html .='<td colspan="2"><strong>TOTALES</strong></td>';
        $html .='<td><strong>'.number_format($ta,2,'.',',').'</strong></td>';
        $html .='<td><strong>'.number_format($tc,2,'.',',').'</strong></td>';
        $html .='<td><strong>'.number_format($td,2,'.',',').'</strong></td>';
        $html .='</tr>';
        #******************************************************************************#
        $ta     = 0;
        $tc     = 0;
        $td     = 0;
        $html  .= '<tr><td colspan="5"><strong><center><i>ELEMENTOS SIN CUENTA</i></center></strong></td></tr>';
        $html  .= '<tr>';
        $html  .= '<td colspan="2"><strong>CÓDIGO ELEMENTO</strong></td>';
        $html  .= '<td colspan="2"><strong>NOMBRE</strong></td>';
        $html  .= '<td><strong>VALOR ALMACÉN</strong></td>';
        $html  .= '</tr>';
        $row = $con->Listar("SELECT DISTINCT  pi.codi, pi.nombre, 
            sum(dm.valor)
            FROM  gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            WHERE m.compania = $compania AND tm.clase = 3 
            AND m.fecha <='$fechaFinal' AND pi.tipoinventario = 2 
            AND (dm.id_unico NOT IN (SELECT detallemovimiento FROM gf_movimiento_producto) 
                OR (SELECT COUNT(pe.id_unico)FROM gf_producto_especificacion pe 
                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                LEFT JOIN gf_movimiento_producto mp ON 	p.id_unico = mp.producto 
                WHERE mp.detallemovimiento = dm.id_unico AND pe.fichainventario = 10 AND pe.valor !='')<=0) 
            GROUP BY pi.id_unico
            ORDER BY pi.codi  ASC");
        for ($i = 0; $i < count($row); $i++) {
            $html .='<tr>';
            $html .='<td colspan="2">'.$row[$i][0].'</td>';
            $html .='<td colspan="2">'.$row[$i][1].'</td>';
            $html .='<td>'.number_format($row[$i][2],2,'.',',').'</td>';
            $html .='</tr>';
            $ta   += $row[$i][2];
        }
        $html .='<tr>';
        $html .='<td colspan="4"><strong>TOTALES</strong></td>';
        $html .='<td><strong>'.number_format($ta,2,'.',',').'</strong></td>';
        $html .='</tr>';
        
        
    break;
    case 2:
        $titulo = 'Consolidado Cuenta Activo Detallado';
        $n      = 7;
        $html  .= '<tr>';
        $html  .= '<td><strong>CÓDIGO ELEMENTO</strong></td>';
        $html  .= '<td><strong>NOMBRE ELEMENTO</strong></td>';
        $html  .= '<td><strong>CÓDIGO CUENTA</strong></td>';
        $html  .= '<td><strong>NOMBRE</strong></td>';
        $html  .= '<td><strong>CANTIDAD </strong></td>';
        $html  .= '<td><strong>VALOR </strong></td>';
        $html  .= '<td><strong>VALOR TOTAL</strong></td>';
        $html  .= '</tr>';
        $whr   ='';
        if(!empty($_REQUEST['cuentaI'])){
            $whr   .=" AND pe.valor>='".$_REQUEST['cuentaI']."'";
        }
        if(!empty($_REQUEST['cuentaF'])){
            $whr   .=" AND pe.valor<='".$_REQUEST['cuentaF']."'";
        }
        $row = $con->Listar("SELECT DISTINCT pe.valor 
            FROM gf_producto_especificacion pe 
            LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
            LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
            LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
            LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
            AND pi.tipoinventario = 2 
            AND m.fecha <='$fechaFinal' $whr
            GROUP BY pe.valor 
            ORDER BY pe.valor  ASC");
        for ($i = 0; $i < count($row); $i++) {
            $tvu    = 0;
            $tc     = 0;
            $tt     = 0;
            $codigo = $row[$i][0];
            $rowdet  =$con->Listar("SELECT DISTINCT dm.id_unico, pi.codi, pi.nombre, 
                c.codi_cuenta, c.nombre, pe.valor, dm.cantidad, dm.valor
                FROM gf_producto_especificacion pe 
                LEFT JOIN gf_producto p ON pe.producto = p.id_unico 
                LEFT JOIN gf_movimiento_producto mp ON p.id_unico = mp.producto 
                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                LEFT JOIN gf_ficha_inventario fi ON fi.id_unico = pe.fichainventario 
                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                LEFT JOIN gf_cuenta c ON pe.valor = c.codi_cuenta
                LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico AND pa.compania = $compania and pa.anno ='$anno'
                WHERE m.compania = $compania AND tm.clase = 3 AND fi.elementoficha = 10  
                AND pa.compania = $compania and pa.anno ='$anno'
                AND pi.tipoinventario = 2     
                AND m.fecha <='$fechaFinal' 
                AND pe.valor = '$codigo'  
                ORDER BY pi.codi ASC");
            for ($j = 0; $j < count($rowdet); $j++) {
                $vtot = $rowdet[$j][6]*$rowdet[$j][7];
                $html .='<tr>';
                $html .='<td>'.$rowdet[$j][1].'</td>';
                $html .='<td>'.$rowdet[$j][2].'</td>';
                $html .='<td>'.$rowdet[$j][3].'</td>';
                $html .='<td>'.$rowdet[$j][4].'</td>';
                $html .='<td>'.number_format($rowdet[$j][6],2,'.',',').'</td>';
                $html .='<td>'.number_format($rowdet[$j][7],2,'.',',').'</td>';
                $html .='<td>'.number_format($vtot,2,'.',',').'</td>';
                $html .='</tr>';
                $tvu  += $rowdet[$j][6];
                $tc   += $rowdet[$j][7];
                $tt   += $vtot;
            }
            $html .='<tr>';
            $html .='<td colspan="4"><strong>TOTALES ALMACÉN</strong></td>';
            $html .='<td><strong>'.number_format($tvu,2,'.',',').'</strong></td>';
            $html .='<td><strong>'.number_format($tc,2,'.',',').'</strong></td>';
            $html .='<td><strong>'.number_format($tt,2,'.',',').'</strong></td>';
            $html .='</tr>';
            
            $vc     =$con->Listar("SELECT c.codi_cuenta, c.nombre,SUM(dm.valor) FROM gf_cuenta c 
                LEFT JOIN gf_detalle_comprobante dm ON c.id_unico = dm.cuenta
                LEFT JOIN gf_comprobante_cnt cn ON dm.comprobante = cn.id_unico 
                LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
                WHERE c.codi_cuenta = '$codigo' AND pa.compania = $compania  AND pa.anno = '$anno'
                AND cn.parametrizacionanno = pa.id_unico 
                AND cn.fecha <='$fechaFinal'");
            $dif = $tt-$vc[0][2];
            $html .='<tr>';
            $html .='<td colspan="6"><strong>TOTALES CONTABILIDAD</strong></td>';
            $html .='<td><strong>'.number_format($vc[0][2],2,'.',',').'</strong></td>';
            $html .='</tr>';
            $html .='<tr>';
            $html .='<td colspan="6"><strong>DIFERENCIA</strong></td>';
            $html .='<td><strong>'.number_format($dif,2,'.',',').'</strong></td>';
            $html .='</tr>';
        }
    break;
    
    #* Depreciación
    case 3:
        $titulo = 'Consolidado Depreciación';
        $n      = 6;
        $html  .= '<tr>';
        $html  .= '<td><strong>CÓDIGO CUENTA</strong></td>';
        $html  .= '<td><strong>NOMBRE</strong></td>';
        $html  .= '<td><strong>DEPRECIACIÓN MES</strong></td>';
        $html  .= '<td><strong>VALOR DEPRECIACIÓN ACUMULADA </strong></td>';
        $html  .= '<td><strong>VALOR CONTABLE</strong></td>';
        $html  .= '<td><strong>DIFERENCIA</strong></td>';
        $html  .= '</tr>';
        $row = $con->Listar("SELECT DISTINCT  c.id_unico, c.codi_cuenta, c.nombre, 
            GROUP_CONCAT(pi.id_unico) 
            FROM gf_configuracion_almacen ca 
            LEFT JOIN gf_plan_inventario pi ON ca.plan_inventario = pi.id_unico 
            LEFT JOIN gf_cuenta c ON ca.cuenta_credito = c.id_unico 
            WHERE ca.parametrizacion_anno =$parmanno AND ca.tipo_movimiento IS NULL 
            GROUP BY c.id_unico 
            ORDER BY c.codi_cuenta");
        $tvu    = 0;
        $tc     = 0;
        $tt     = 0;
        $td     = 0;
        for ($i = 0; $i < count($row); $i++) {
            $codigos  = $row[$i][3];
            $id_cuenta= $row[$i][0];
            $rowdet  = $con->Listar("SELECT GROUP_CONCAT(DISTINCT mp.producto) 
                FROM gf_movimiento_producto mp 
                LEFT JOIN gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
                WHERE m.compania = $compania AND tm.clase = 3 AND pi.tipoinventario = 2 
                AND m.fecha <='$fechaFinal' 
                AND dm.planmovimiento IN ($codigos)");
            if(empty($rowdet[0][0])){} else {
                $vc     =$con->Listar("SELECT c.codi_cuenta, c.nombre,SUM(dm.valor) 
                    FROM gf_cuenta c 
                    LEFT JOIN gf_detalle_comprobante dm ON c.id_unico = dm.cuenta
                    LEFT JOIN gf_comprobante_cnt cn ON dm.comprobante = cn.id_unico 
                    WHERE c.id_unico = $id_cuenta 
                    AND cn.fecha <='$fechaFinal'");

                $v_dem = $con->Listar("SELECT SUM(valor) FROM ga_depreciacion 
                    WHERE fecha_dep =  '$fechaFinal'  AND producto IN(".$rowdet[0][0].")");
                $v_dea = $con->Listar("SELECT SUM(valor) FROM ga_depreciacion 
                    WHERE fecha_dep <=  '$fechaFinal'  AND producto IN(".$rowdet[0][0].")");
                $dif = $v_dea[0][0]+$vc[0][2];
                $html .='<tr>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.number_format($v_dem[0][0],2,'.',',').'</td>';
                $html .='<td>'.number_format($v_dea[0][0],2,'.',',').'</td>';
                $html .='<td>'.number_format($vc[0][2],2,'.',',').'</td>';
                $html .='<td>'.number_format($dif,2,'.',',').'</td>';
                $html .='</tr>';
                $tvu  += $v_dem[0][0];
                $tc   += $v_dea[0][0];
                $tt   += $vc[0][2];
                $td   += $dif;
            }
        }
        $html .='<tr>';
        $html .='<td colspan="2"><strong>TOTALES </strong></td>';
        $html .='<td><strong>'.number_format($tvu,2,'.',',').'</strong></td>';
        $html .='<td><strong>'.number_format($tc,2,'.',',').'</strong></td>';
        $html .='<td><strong>'.number_format($tt,2,'.',',').'</strong></td>';
        $html .='<td><strong>'.number_format($td,2,'.',',').'</strong></td>';
        $html .='</tr>';
    break;

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Consolidado Almacén</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="<?php echo $n;?>" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/><?php echo $titulo ?>
        <br/>FECHA CORTE <?php echo $fechac ?>
        <br/>&nbsp;                 
        </strong> 
    </th>
    <?php echo $html;?>

</table>
</body>
</html>