<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Seguimiento_Presupuesto_Contabilidad.xls");
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
$fechaI     = fechaC($_REQUEST['fechaI']);
$fechaF     = fechaC($_REQUEST['fechaF']);
$tipoI      = tc($_REQUEST['tipoI']);
$tipoF      = tc($_REQUEST['tipoF']);
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Seguimiento Ingresos Presupuesto Contabilidad</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="10" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>SEGUIMIENTO INGRESOS PRESUPUESTO CONTABILIDAD
        <?php echo '<br/>'.$tipoI.' A '.$tipoF.'<br/> ENTRE '.$_REQUEST['fechaI'].' AL '.$_REQUEST['fechaF']; ?>
        <br/>&nbsp;                 
        </strong> 
    </th>   
    <tr>
        <td align="center"><strong>FECHA</strong></td>
        <td align="center"><strong>TIPO</strong></td>
        <td align="center"><strong>NÚMERO</strong></td>
        <td align="center"><strong>CONCEPTO</strong></td>
        <td align="center"><strong>RUBRO</strong></td>
        <td align="center"><strong>FUENTE</strong></td>
        <td align="center"><strong>CUENTA</strong></td>
        <td align="center"><strong>VALOR CONTABLE</strong></td>
        <td align="center"><strong>VALOR PRESUPUESTAL</strong></td>
        <td align="center"><strong>DIFERENCIA</strong></td>
    </tr>
    <?php
    $rowc = $con->Listar("SELECT DISTINCT cn.id_unico, 
        DATE_FORMAT(cn.fecha, '%d/%m/%Y'), t.sigla, t.nombre, cn.numero 
        FROM gf_comprobante_cnt cn 
        LEFT JOIN gf_tipo_comprobante t ON cn.tipocomprobante = t.id_unico 
        WHERE cn.parametrizacionanno = $parmanno  
        AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'
        AND t.id_unico BETWEEN ".$_REQUEST['tipoI']." AND ".$_REQUEST['tipoF']." 
        AND t.clasecontable = 9 
        ORDER BY t.id_unico, cn.numero, cn.fecha");
    for ($i = 0; $i < count($rowc); $i++) {
        #* Buscar Detalles
        $rd = $con->Listar("SELECT cn.nombre, rb.codi_presupuesto, rb.nombre, f.nombre, 
            c.codi_cuenta, c.nombre, 
            IF(dp.valor>0, IF(dc.valor<0, dc.valor*-1, dc.valor), 
            IF(dp.valor<0, IF(dc.valor>0, dc.valor*-1, dc.valor),IF(dc.valor<0, dc.valor*-1, dc.valor))) as valor, 
            dp.valor , dp.id_unico , dp.comprobantepptal 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico 
        LEFT JOIN gf_concepto_rubro cr ON dp.conceptoRubro = cr.id_unico 
        LEFT JOIN gf_rubro_fuente rf ON dp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_fuente f oN rf.fuente = f.id_unico 
        LEFT JOIN gf_concepto cn ON cr.concepto = cn.id_unico
        WHERE dc.comprobante = ".$rowc[$i][0]." AND c.clasecuenta NOT IN (11,12)");
        $vp  = 0;
        $vc  = 0;
        $idsp = '0';
        $idcp = '0';
        for ($d = 0;$d < count($rd); $d++) {
            $idsp .= ','.$rd[$d][8];
            $idcp .= ','.$rd[$d][9];
            $dif   = $rd[$d][6]-$rd[$d][7];
            echo '<tr>
            <td>'.$rowc[$i][1].'</td>    
            <td>'.$rowc[$i][2].' - '.$rowc[$i][3].'</td>
            <td>'.$rowc[$i][4].'</td>
            <td>'.$rd[$d][0].'</td>
            <td>'.$rd[$d][1].' - '.$rd[$d][2].'</td>
            <td>'.$rd[$d][3].'</td>
            <td>'.$rd[$d][4].' - '.$rd[$d][5].'</td>
            <td>'. number_format($rd[$d][6],2,'.',',').'</td>
            <td>'. number_format($rd[$d][7],2,'.',',').'</td>
            <td>'. number_format($dif,2,'.',',').'</td>
            </tr>';
            $vc  += $rd[$d][6];
            $vp  += $rd[$d][7];
        }
        $rpp  = $con->Listar("SELECT cn.nombre, rb.codi_presupuesto, 
            rb.nombre, f.nombre, dp.valor , dp.id_unico, dp.comprobantepptal 
        FROM  gf_detalle_comprobante_pptal dp 
        LEFT JOIN gf_concepto_rubro cr ON dp.conceptoRubro = cr.id_unico 
        LEFT JOIN gf_rubro_fuente rf ON dp.rubrofuente = rf.id_unico 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_fuente f oN rf.fuente = f.id_unico 
        LEFT JOIN gf_concepto cn ON cr.concepto = cn.id_unico 
        WHERE dp.comprobantepptal IN($idcp) AND dp.id_unico NOT IN ($idsp)");
        for ($p = 0;$p < count($rpp); $p++) {
           
            $dif   = 0-$rpp[$p][4];
            echo '<tr>
            <td>'.$rowc[$i][1].'</td>    
            <td>'.$rowc[$i][2].' - '.$rowc[$i][3].'</td>
            <td>'.$rowc[$i][4].'</td>
            <td>'.$rpp[$p][0].'</td>
            <td>'.$rpp[$p][1].' - '.$rpp[$p][2].'</td>
            <td>'.$rpp[$p][3].'</td>
            <td></td>
            <td>'. number_format(0,2,'.',',').'</td>
            <td>'. number_format($rpp[$p][4],2,'.',',').'</td>
            <td>'. number_format($dif,2,'.',',').'</td>
            </tr>';
            $vc  += 0;
            $vp  += $rpp[$p][4];
        }
        $dt = $vc - $vp;
        echo '<tr>
        <td colspan="7"><strong><i>TOTAL '.$rowc[$i][2].' '.$rowc[$i][4].'</i></strong></td>    
        <td><strong><i>'. number_format($vc,2,'.',',').'</i></strong></td>
        <td><strong><i>'. number_format($vp,2,'.',',').'</i></strong></td>
        <td><strong><i>'. number_format($dt,2,'.',',').'</i></strong></td>
        </tr>';    
        
    }
    
    ?>
</table>
</body>
</html>
<?php
function tc($tipo){
    global $con;
    $rw = $con->Listar("SELECT CONCAT_WS(' - ', sigla, nombre) FROM gf_tipo_comprobante WHERE id_unico = $tipo");
    return $rw[0][0];
}    