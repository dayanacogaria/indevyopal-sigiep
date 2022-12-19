<?PHP require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
@session_start();
ini_set('max_execution_time', 0);
ob_start();
$con = new ConexionPDO();
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Costos.xls");
$parmanno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$annionom   = anno($parmanno);
$calendario = CAL_GREGORIAN;
$anno       = $annionom; 
$mesI       = $mysqli->real_escape_string(''.$_POST['mesI'].'');
$mesF       = $mysqli->real_escape_string(''.$_POST['mesF'].'');
$diaF       = cal_days_in_month($calendario, $mesF, $anno); 
$fechaInicial= $anno.'-'.$mesI.'-01';
$fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
$centro_c   = $mysqli->real_escape_string(''.$_POST['centro_c'].'');

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Auxiliar Costos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="4" align="center"><strong>
        <br/>&nbsp;
        <br/><?php echo $razonsocial ?>
        <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
        <br/>&nbsp;
        <br/>AUXILIAR COSTOS  
        <br/>&nbsp;                 
        </strong>
    </th>
    <tr></tr> 
    <?php 
    $row = $con->Listar("SELECT DISTINCT 
        c.id_unico, c.codi_cuenta, c.nombre 
    FROM gf_detalle_comprobante dc 
    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
    WHERE cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
    AND dc.centrocosto = $centro_c AND c.codi_cuenta LIKE '7%' 
    AND cn.parametrizacionanno = $parmanno 
    AND dc.valor != 0
    ORDER BY c.codi_cuenta ASC ");
    for ($i = 0; $i < count($row); $i++) {
        echo '<tr>
        <td colspan="4"><strong><i><br/>&nbsp;'.$row[$i][1].' - '.$row[$i][2].'<br/>&nbsp;</i></strong></td>
        </tr>';
        echo '<tr>
        <td><strong>Tipo Movimiento</strong></td>
        <td><strong>Número</strong></td>
        <td><strong>Fecha</strong></td>
        <td><strong>Valor</strong></td>
        </tr>';
        $id_cuenta = $row[$i][0];
        
        $rowd = $con->Listar("SELECT DISTINCT 
            dc.id_unico, tc.sigla, tc.nombre, 
            cn.numero, DATE_FORMAT(cn.fecha, '%d/%m/%Y'), IF(dc.valor<0, dc.valor*-1, dc.valor) 
        FROM gf_detalle_comprobante dc 
        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
        WHERE cn.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' 
        AND dc.centrocosto = $centro_c 
        AND dc.cuenta = $id_cuenta AND dc.valor != 0 
        AND cn.parametrizacionanno = $parmanno ");
        for ($j = 0; $j < count($rowd); $j++) {
            echo '<tr>
            <td>'.$rowd[$j][1].' - '.$rowd[$j][2].'</td>
            <td>'.$rowd[$j][3].'</td>
            <td>'.$rowd[$j][4].'</td>
            <td>'.number_format($rowd[$j][5], 2, '.', ',').'</td>
            </tr>';
        }
    }
    
    ?>
</table>
</body>
</html>