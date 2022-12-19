<?php
header("Content-Type: text/html;charset=utf-8");

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require'../jsonPptal/funcionesPptal.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$nanno  = anno($anno);
$con    = new ConexionPDO();
$usuario = $_SESSION['usuario'];
IF(!empty($_REQUEST['mesI'])){
   $mesI =$_REQUEST['mesI'];
} else {
   $mesI ='01'; 
}
$mesF =$_REQUEST['mesF'];

$fechaI = $nanno.'-'.$mesI.'-01';
$diaf   = diaf($mesF,$nanno);
$fechaF =$nanno.'-'.$mesF.'-'.$diaf;
switch ($_REQUEST['t']){
    case 1:
        header("Content-Disposition: attachment; filename=Saldo_Costos.xls");
    $rowt = $con->Listar("SELECT DISTINCT 
			pa.id_unico, 
            t.razonsocial, 
            t.numeroidentificacion 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON pa.compania = t.id_unico 
        WHERE 
            pa.anno ='$nanno' 
            AND (tc.consolidado is null OR tc.consolidado = 2)  
        ORDER BY t.id_unico ASC");?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Cuentas 7</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="2" align="center"><strong>
                <br/>&nbsp;SALDO EN COSTOS 
                <br/>&nbsp;
                </strong>
        </th>
        <tr>
            <td><strong>COMPAÑIA</strong></td>
            <td><strong>SALDO</strong></td>
        </tr>
        <?php for ($i = 0; $i < count($rowt); $i++) {
            $id_par = $rowt[$i][0];
            #** Buscar movimientos de las cuentas 7 
            $row =$con->Listar("SELECT DISTINCT 
                    cn.id_unico,
                    cn.numero,
                    tc.sigla,
                    tc.nombre,
                    date_format(cn.fecha,'%d/%m/%Y'),
                    (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0 AND c1.codi_cuenta LIKE '7%') AS debito1,
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 AND c1.codi_cuenta LIKE '7%') AS credito2,
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0 AND c1.codi_cuenta LIKE '7%') AS credito, 
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0 AND c1.codi_cuenta LIKE '7%') AS debito2, 
                    t.razonsocial, 
                    t.numeroidentificacion, 
                    cn.id_unico 
                FROM
                    gf_comprobante_cnt cn 
                LEFT JOIN
                    gf_tipo_comprobante tc
                ON
                    cn.tipocomprobante = tc.id_unico  
                LEFT JOIN 
                gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
                LEFT JOIN 
                    gf_tercero t ON pa.compania = t.id_unico 
                WHERE 
                    cn.parametrizacionanno ='$id_par' 
                    AND tc.clasecontable != 5 
                    AND (tc.consolidado is null OR tc.consolidado = 2) 
                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'
                ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ");
            $debito  = 0;
            $credito = 0;
            for ($j = 0; $j < count($row); $j++) {
                $debito1    = $row[$j][5];
                $debitoN    = $row[$j][8]*-1;
                $credito1   = $row[$j][7];
                $creditoN   = $row[$j][6]*-1;
                $debito     += $debito1+$debitoN;
                $credito    += $credito1+$creditoN;
            }
            $diferencia = ROUND(($debito -$credito),2);
            if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                echo '<tr>';
                echo '<td>'.$rowt[$i][1].' - '.$rowt[$i][2].'</td>';
                echo '<td>'.number_format($diferencia,2,'.',',').'</td>';
                echo '</tr>';
            }
        }?>
    </table>
    </body>
    </html>
<?php
    break;
    case 2:
        header("Content-Disposition: attachment; filename=Cuentas_Por_Cobrar.xls");
        $rowt = $con->Listar("SELECT DISTINCT 
			pa.id_unico, 
            t.razonsocial, 
            t.numeroidentificacion 
        FROM
            gf_comprobante_cnt cn 
        LEFT JOIN
            gf_tipo_comprobante tc
        ON
            cn.tipocomprobante = tc.id_unico  
        LEFT JOIN 
            gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        LEFT JOIN 
            gf_tercero t ON pa.compania = t.id_unico 
        WHERE 
            pa.anno ='$nanno' 
            AND (tc.consolidado is null OR tc.consolidado = 2)  
        ORDER BY t.id_unico ASC");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Cuentas 13</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="2" align="center"><strong>
                <br/>&nbsp;CUENTAS POR COBRAR CON SALDO
                <br/>&nbsp;
                </strong>
        </th>
        <tr>
            <td><strong>COMPAÑIA</strong></td>
            <td><strong>SALDO</strong></td>
        </tr>
        <?php for ($i = 0; $i < count($rowt); $i++) {
            $id_par = $rowt[$i][0];
            #** Buscar movimientos de las cuentas 13 
            $row =$con->Listar("SELECT DISTINCT 
                    cn.id_unico,
                    cn.numero,
                    tc.sigla,
                    tc.nombre,
                    date_format(cn.fecha,'%d/%m/%Y'),
                    (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0 AND c1.codi_cuenta LIKE '13%') AS debito1,
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 AND c1.codi_cuenta LIKE '13%') AS credito2,
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0 AND c1.codi_cuenta LIKE '13%') AS credito, 
                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0 AND c1.codi_cuenta LIKE '13%') AS debito2, 
                    t.razonsocial, 
                    t.numeroidentificacion, 
                    cn.id_unico 
                FROM
                    gf_comprobante_cnt cn 
                LEFT JOIN
                    gf_tipo_comprobante tc
                ON
                    cn.tipocomprobante = tc.id_unico  
                LEFT JOIN 
                gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
                LEFT JOIN 
                    gf_tercero t ON pa.compania = t.id_unico 
                WHERE 
                    cn.parametrizacionanno ='$id_par' 
                    AND tc.clasecontable != 5 
                    AND (tc.consolidado is null OR tc.consolidado = 2) 
                    AND cn.fecha BETWEEN '$fechaI' AND '$fechaF'
                ORDER BY t.id_unico, tc.id_unico, cn.fecha ASC ");
            $debito  = 0;
            $credito = 0;
            for ($j = 0; $j < count($row); $j++) {
                $debito1    = $row[$j][5];
                $debitoN    = $row[$j][8]*-1;
                $credito1   = $row[$j][7];
                $creditoN   = $row[$j][6]*-1;
                $debito     += $debito1+$debitoN;
                $credito    += $credito1+$creditoN;
            }
            $diferencia = ROUND(($debito -$credito),2);
            if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                echo '<tr>';
                echo '<td>'.$rowt[$i][1].' - '.$rowt[$i][2].'</td>';
                echo '<td>'.number_format($diferencia,2,'.',',').'</td>';
                echo '</tr>';
            }
        } 
        ?>
    </table>
    </body>
    </html>
    <?PHP 
    break;
    #** Informe Bancos 
    case 3:
        $n_ident = $_REQUEST['ni'];
        header("Content-Disposition: attachment; filename=Informe_Reciprocas.xls");
        $row = $con->Listar("SELECT  t.razonsocial, t.numeroidentificacion, 
            dc.cuenta, c.codi_cuenta, c.nombre, tr.razonsocial,
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=1 AND  dc1.valor>0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable = 5 ) as debitosi1, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=2 AND  dc1.valor<0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable = 5 ) as debitosi2, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=1 AND  dc1.valor<0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable = 5 ) as creditossi1, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=2 AND  dc1.valor>0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable = 5 ) as creditossi2,
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=1 AND  dc1.valor>0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable != 5 
                AND cn1.fecha BETWEEN '$fechaI' AND '$fechaF') as debito1, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=2 AND  dc1.valor<0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable != 5 
                AND cn1.fecha BETWEEN '$fechaI' AND '$fechaF') as debito2, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE c1.naturaleza=1 AND  dc1.valor<0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable != 5 
                AND cn1.fecha BETWEEN '$fechaI' AND '$fechaF') as credito1, 
            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 
                LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                LEFT JOIN gf_comprobante_cnt cn1 ON dc1.comprobante = cn1.id_unico 
                LEFT JOIN gf_tipo_comprobante tc1 ON cn1.tipocomprobante = tc1.id_unico 
                WHERE  c1.naturaleza=2 AND  dc1.valor>0 AND dc1.cuenta = dc.cuenta 
                AND tc1.clasecontable != 5 
                AND cn1.fecha BETWEEN '$fechaI' AND '$fechaF' ) as credito2 

            FROM
                gf_detalle_comprobante dc 
            LEFT JOIN 
                gf_cuenta c on dc.cuenta = c.id_unico        
            LEFT JOIN 
                gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
            LEFT JOIN 
                gf_tercero t ON pa.compania = t.id_unico 

            LEFT JOIN 
                gf_cuenta_bancaria cb ON cb.cuenta = c.id_unico 
            LEFT JOIN 
                gf_tercero tr ON cb.banco = tr.id_unico 
            WHERE 
                pa.anno ='$nanno' 
                AND tr.numeroidentificacion like '%$n_ident%' 
                GROUP by c.id_unico 
        ORDER BY t.razonsocial, t.numeroidentificacion, c.codi_cuenta ASC");
        
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Recíprocas</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="8" align="center"><strong>
        <br/>&nbsp;INFORME RECIPROCAS
        <br/>&nbsp;
        </strong>
        </th>
        <tr>
        <td><strong>COMPAÑIA</strong></td>
        <td><strong>NÚMERO IDENTIFICACIÓN</strong></td>
        <td><strong>CUENTA</strong></td>
        <td><strong>NOMBRE</strong></td>
        <td><strong>SALDO INICIAL</strong></td>
        <td><strong>DÉBITO</strong></td>
        <td><strong>CRÉDITO</strong></td>
        <td><strong>SALDO FINAL</strong></td>
        </tr>';
        for ($i = 0; $i < count($row); $i++) {
            $saldoI     =($row[$i][6]+$row[$i][7]*-1)-($row[$i][8]*-1+$row[$i][9]);
            $debitos    =($row[$i][10]+$row[$i][11]*-1);
            $creditos   =($row[$i][12]*-1+$row[$i][13]);
            $saldoF     =$saldoI+($debitos-$creditos);
            echo '<tr>
            <td>'.$row[$i][0].'</td>
            <td>'.$row[$i][1].'</td>
            <td>'.$row[$i][3].'</td>
            <td>'.$row[$i][4].'</td>
            <td>'. number_format($saldoI,2,'.',',').'</td> 
            <td>'. number_format($debitos,2,'.',',').'</td> 
            <td>'. number_format($creditos,2,'.',',').'</td> 
            <td>'. number_format($saldoF,2,'.',',').'</td> 
            </tr>';
        }
        echo '</table>
        </body>
        </html>
        ';
    break;
    # Cuentas Inactivas con saldo
    case 4:
        header("Content-Disposition: attachment; filename=Cuentas_Inactivas con saldo.xls");
        $row = $con->Listar("SELECT DISTINCT t.razonsocial, t.numeroidentificacion, c.codi_cuenta, c.nombre, 
            (SELECT IF(SUM(dc1.valor) != '',(IF(SUM(dc1.valor)<0,ROUND(SUM(dc1.valor),2)*-1,ROUND(SUM(dc1.valor),2))), 0) 
                    FROM gf_detalle_comprobante dc1 
                WHERE c.id_unico = dc1.cuenta  AND if(c.naturaleza=1, dc1.valor>0, dc1.valor<0) 
            ) AS VALORDEBITO, 
            (SELECT IF(SUM(dc1.valor) != '',(IF(SUM(dc1.valor)<0,ROUND(SUM(dc1.valor),2)*-1,ROUND(SUM(dc1.valor),2))), 0) 
                    FROM gf_detalle_comprobante dc1 
                WHERE c.id_unico = dc1.cuenta  AND if(c.naturaleza=2, dc1.valor>0, dc1.valor<0) 
            ) AS VALORCREDITO, 
             IF(c.naturaleza =1 && SUM(dc.valor)>0, SUM(dc.valor), IF(c.naturaleza =2 && SUM(dc.valor)<0, SUM(dc.valor)*-1, 0)) as SALDODEBITO,
             IF(c.naturaleza =2 && SUM(dc.valor)>0, SUM(dc.valor), IF(c.naturaleza =1 && SUM(dc.valor)<0, SUM(dc.valor)*-1, 0)) as SCREDITO 
            FROM gf_detalle_comprobante dc
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
            LEFT JOIN gf_parametrizacion_anno pa ON c.parametrizacionanno = pa.id_unico 
            LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            WHERE pa.anno = '$nanno' AND c.activa = 2  AND cn.fecha <='".$fechaF."' AND pa.compania != $compania 
            GROUP BY c.id_unico  
            HAVING SUM(dc.valor)!=0 
            ORDER BY t.numeroidentificacion, c.codi_cuenta ASC");
        
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informe Cuentas Inactivas Con Saldo</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="6" align="center"><strong>
                <br/>&nbsp;CUENTAS INACTIVAS CON SALDO
                <br/>&nbsp;
                </strong>
        </th>
        <tr>
            <td><strong>COMPAÑIA</strong></td>
            <td><strong>CUENTA</strong></td>
            <td><strong>MOVIMIENTO DÉBITO</strong></td>
            <td><strong>MOVIMIENTO CRÉDITO</strong></td>
            <td><strong>SALDO DÉBITO</strong></td>
            <td><strong>SALDO CRÉDITO</strong></td>
        </tr>
        <?php
            $html ='';
            for ($i = 0; $i < count($row); $i++) {
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].' - '.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].' - '.$row[$i][3].'</td>';
                $html .='<td>'.number_format($row[$i][4], 2,'.', ',' ).'</td>';
                $html .='<td>'.number_format($row[$i][5], 2,'.', ',' ).'</td>';
                $html .='<td>'.number_format($row[$i][6], 2,'.', ',' ).'</td>';
                $html .='<td>'.number_format($row[$i][7], 2,'.', ',' ).'</td>';
                $html .='</tr>';
            }      
            echo $html;
        ?>
    </table>
    </body>
    </html>
    <?PHP 
    break;
} ?>