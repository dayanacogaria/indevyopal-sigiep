<?php
@session_start();
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Por_Cuenta.xls");
$usuario    = $_SESSION['usuario'];
$fechaActual= date('d/m/Y');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$nanno      = anno($anno);
$calendario = CAL_GREGORIAN;
$m2         = $_REQUEST['periodoF'];
$cuenta     = $_REQUEST['cuenta'];
$diaF       = cal_days_in_month($calendario, $m2, $nanno); 
$fechaFinal = $anno.'-'.$mesI.'-'.$diaF;
$fechac     = $diaF.'/'.$mesI.'/'.$anno;
$meses      = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$month2     = $meses[(int)$m2];
$fechaF     = $nanno.'-'.$m2.'-'.$diaF;
#***********************Datos Compañia***********************#
$compania   = $_SESSION['compania'];
$rowC       = $con->Listar("SELECT 
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
<title>Informe Auxiliar Por Cuenta</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <th colspan="4" align="center"><strong>
            <br/>&nbsp;AUXILIAR POR CUENTA 
            <br/>&nbsp;
            <?php  echo 'CUENTA : '.$cuenta.'<br/>&nbsp;';
            echo 'MES FINAL: '.$month2;?>
            <br/>&nbsp;
            </strong>
    </th>
    <?php
    if($_REQUEST['tipoa']==1){
        echo '<tr>';
        echo '<td><strong>COMPAÑÍA</strong></td>';
        echo '<td><strong>NÚMERO IDENTIFICACIÓN</strong></td>';
        echo '<td><strong>NOMBRE</strong></td>';
        echo '<td><strong>SALDO</strong></td>';
        echo '</tr>';
        $rowc =$con->Listar("SELECT 
        DISTINCT t.id_unico , t.razonsocial, t.numeroidentificacion 
        FROM  gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero  
        LEFT JOIN gf_parametrizacion_anno pa ON t.id_unico = pa.compania 
        WHERE  t.id_unico != $compania  AND pt.perfil = 1 AND pa.id_unico IS NOT NULL ");
        for ($c = 0; $c < count($rowc); $c++) {
            $tercero_compania = $rowc[$c][0];
            #* Cuentas 
            $row = $con->Listar("SELECT DISTINCT tr.id_unico , 
                tr.numeroidentificacion, IF(CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos)) AS NOMBRE
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
                LEFT JOIN gf_tercero tr ON dc.tercero = tr.id_unico 
                WHERE pa.compania = $tercero_compania AND pa.anno = '$nanno' 
                AND pa.id_unico = c.parametrizacionanno 
                AND c.codi_cuenta LIKE '$cuenta%' AND cn.fecha <='$fechaF'");
            if(count($row)>0){
                for ($i = 0; $i < count($row); $i++) {
                    $idt = $row[$i][0];
                    $rowd = $con->Listar("SELECT DISTINCT dc.valor, dc.id_unico 
                        FROM gf_detalle_comprobante dc 
                        LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                        LEFT JOIN gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
                        LEFT JOIN gf_tercero t ON dc.tercero = t.id_unico 
                        WHERE pa.compania = $tercero_compania AND pa.anno = '$nanno' 
                        AND pa.id_unico = c.parametrizacionanno 
                        AND t.id_unico = $idt 
                        AND c.codi_cuenta LIKE '$cuenta%' AND cn.fecha <='$fechaF'");
                    $saldo = 0;
                    for ($d = 0; $d < count($rowd); $d++) {
                        $saldo += $rowd[$d][0];
                    }
                    echo '<td>'.$rowc[$c][1].' - '.$rowc[$c][2].'</td>';
                    echo '<td>'.$row[$i][1].'</td>';
                    echo '<td>'.$row[$i][2].'</td>';
                    echo '<td>'. number_format($saldo, 2, '.', ',').'</td>';
                    echo '</tr>';
                
                }
                
                
            }
        }
    } else {
        echo '<tr>';
        echo '<td colspan ="3"><strong>COMPAÑÍA</strong></td>';
        echo '<td><strong>SALDO</strong></td>';
        echo '</tr>';
        $rowc =$con->Listar("SELECT 
        DISTINCT t.id_unico , t.razonsocial, t.numeroidentificacion 
        FROM  gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero  
        LEFT JOIN gf_parametrizacion_anno pa ON t.id_unico = pa.compania 
        WHERE  t.id_unico != $compania  AND pt.perfil = 1 AND pa.id_unico IS NOT NULL ");
        for ($c = 0; $c < count($rowc); $c++) {
            $tercero_compania = $rowc[$c][0];
            $rowd = $con->Listar("SELECT DISTINCT dc.valor, dc.id_unico 
                FROM gf_detalle_comprobante dc 
                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                LEFT JOIN gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
                LEFT JOIN gf_tercero t ON dc.tercero = t.id_unico 
                WHERE pa.compania = $tercero_compania AND pa.anno = '$nanno' 
                AND pa.id_unico = c.parametrizacionanno  
                AND c.codi_cuenta LIKE '$cuenta%' AND cn.fecha <='$fechaF'");
            $saldo = 0;
            for ($d = 0; $d < count($rowd); $d++) {
                $saldo += $rowd[$d][0];
            }
            echo '<td colspan="3">'.$rowc[$c][1].' - '.$rowc[$c][2].'</td>';
            echo '<td>'. number_format($saldo, 2, '.', ',').'</td>';
            echo '</tr>';
        }
    } 
    
    ?>
</table>
</body>
</html>