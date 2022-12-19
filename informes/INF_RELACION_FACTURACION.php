<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#01/08/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../jsonPptal/funcionesPptal.php");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Relacion_Facturacion.xls");
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN       gf_ciudad c ON ter.ciudadresidencia = c.id_unico 
LEFT JOIN       gf_ciudad c2 ON ter.ciudadidentificacion = c2.id_unico 
LEFT JOIN       gf_departamento d1 ON c.departamento = d1.id_unico 
LEFT JOIN       gf_departamento d2 ON c2.departamento = d2.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
$ciudadR     = $rowC[0][7];
$ciudadI     = $rowC[0][8];
$deptR       = $rowC[0][9];
$deptI       = $rowC[0][10];
#*******************************************************************************#

#   ************    Datos Recibe    ************    #
$fechaI     = fechaC($_POST['fechaI']);  
$fechaF     = fechaC($_POST['fechaF']);  
$t  =0;
#*******************************************************************************# 
if(!empty($_REQUEST['tipo'])){
    if($_REQUEST['tipo']==1){
        $t  =2;
    } elseif($_REQUEST['tipo']==2) {
        $t  =3;
    } else {
        $t =1;
    }
} else {
    $t=1;
}
if($t==1){
    
    $row = $con->Listar("SELECT 
        f.id_unico,
        DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
        UPPER(tf.prefijo), tf.nombre, 
        f.numero_factura, SUM(df.valor_total_ajustado), GROUP_CONCAT(df.id_unico )
    FROM gp_factura f 
    LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
    LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico
    WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
    GROUP BY f.id_unico 
    ORDER BY f.fecha_factura, f.numero_factura");


        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<title>Informe Relación Facturación</title>';
        echo '</head>';
        echo '<body>';
        echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
        echo '<th colspan="20" align="center"><strong>';
        echo '<br/>&nbsp;<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
        echo '<br/>&nbsp;<br/>Informe Relación Facturación';
        echo '<br/>&nbsp;</strong></th>';


        echo '<tr>';
        echo '<td><center><strong>FECHA FACTURA </strong></center></td>';
        echo '<td><center><strong>TIPO FACTURA </strong></center></td>';
        echo '<td><center><strong>NÚMERO FACTURA </strong></center></td>';
        echo '<td><center><strong>VALOR FACTURA</strong></center></td>';
        echo '<td><center><strong>FECHA RECAUDO</strong></center></td>';
        echo '<td><center><strong>TIPO RECAUDO</strong></center></td>';
        echo '<td><center><strong>NÚMERO RECAUDO</strong></center></td>';
        echo '<td><center><strong>VALOR RECAUDO</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';

        echo '</tr>';
        echo '<tbody>';
        $r =1;
        $total_facturas = 0;
        $total_recaudos = 0;
        $total_ing_cnt  = 0;
        $total_ing_pptal= 0;
        $total_ing_caus = 0;
        for ($i = 0; $i < count($row); $i++) {
            echo '<tr>';
            #***** Datos Factura *******#
            echo '<td>'.$row[$i][1].'</td>';
            echo '<td>'.$row[$i][2].' - '.$row[$i][3].'</td>';
            echo '<td>'.$row[$i][4].'</td>';
            echo '<td>'.number_format($row[$i][5],2,'.',',').'</td>';
            #***** Buscar Recaudo ***********#
            $df = $row[$i][6];
            $rowr = $con->Listar("SELECT 
                GROUP_CONCAT(DISTINCT p.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(p.fecha_pago, '%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT tp.nombre),
                GROUP_CONCAT(DISTINCT ' - ',p.numero_pago),
                (SUM(dp.valor)+SUM(dp.iva)+SUM(dp.impoconsumo)+SUM(dp.ajuste_peso)),
                GROUP_CONCAT(DISTINCT dp.detallecomprobante) 
            FROM gp_detalle_pago dp 
            LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
            LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico 
            WHERE dp.detalle_factura IN ($df)");
            echo '<td>'.$rowr[0][1].'</td>';
            echo '<td>'.$rowr[0][2].'</td>';
            echo '<td>'.$rowr[0][3].'</td>';
            echo '<td>'.number_format($rowr[0][4],2,'.',',').'</td>';
            #*****  Buscar Comprobante Ingreso Cnt ***********#
            $drc = $rowr[0][5];
            $rowcn = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ',cn.numero), 
                SUM(IF((dc.valor<0),valor*-1, valor)),
                GROUP_CONCAT(DISTINCT dc.detallecomprobantepptal) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.id_unico IN ($drc)");
            echo '<td>'.$rowcn[0][1].'</td>';
            echo '<td>'.$rowcn[0][2].'</td>';
            echo '<td>'.$rowcn[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante 
                WHERE comprobante IN (".$rowcn[0][0].") ");
            $vdoc_cnt =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_cnt,2,'.',',').'</td>';
            #*****  Buscar Comprobante Ingreso Pptal ***********#
            $drp = $rowcn[0][5];
            $rowdp = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT CONCAT_WS(' - ',UPPER(tc.codigo), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ',cn.numero), 
                SUM(dc.valor)
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.id_unico IN ($drp)");
            echo '<td>'.$rowdp[0][1].'</td>';
            echo '<td>'.$rowdp[0][2].'</td>';
            echo '<td>'.$rowdp[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante_pptal
                WHERE comprobantepptal IN (".$rowdp[0][0].") ");
            $vdoc_pptal =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_pptal,2,'.',',').'</td>';
            #*****  Buscar Comprobante Afectacion ***********#
            $rowca = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ',cn.numero), 
                (SUM(IF((dc.valor<0),valor*-1, valor))/2) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.detalleafectado IN($drc)");
            echo '<td>'.$rowca[0][1].'</td>';
            echo '<td>'.$rowca[0][2].'</td>';
            echo '<td>'.$rowca[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante
                WHERE comprobante IN (".$rowca[0][0].") ");
            $vdoc_csc =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_csc,2,'.',',').'</td>';
            echo '</tr>';
            $total_facturas += $row[$i][5];
            $total_recaudos += $rowr[0][4];
            $total_ing_cnt  += $vdoc_cnt;
            $total_ing_pptal+= $vdoc_pptal;
            $total_ing_caus += $vdoc_csc;
        }
        echo '<tr>';
        echo '<td colspan="3"><strong>TOTAL FACTURAS</strong></td>';
        echo '<td><strong>'.number_format($total_facturas,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL RECAUDOS</strong></td>';
        echo '<td><strong>'.number_format($total_recaudos,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CONTABLES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_cnt,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO PRESUPUESTALES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_pptal,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CAUSACIÓN</strong></td>';
        echo '<td><strong>'.number_format($total_ing_caus,2,'.',',').'</strong></td>';
        echo '<tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
}
#********** Informe Por Facturación *******************#
elseif($t==2){
    if(empty($_REQUEST['tipof'])){
        $row = $con->Listar("SELECT 
            f.id_unico,
            DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
            UPPER(tf.prefijo), tf.nombre, 
            f.numero_factura, SUM(df.valor_total_ajustado), GROUP_CONCAT(df.id_unico ), 
            GROUP_CONCAT(df.detallecomprobante)
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico
        WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
        GROUP BY f.id_unico 
        ORDER BY f.fecha_factura, f.numero_factura");
    } else {
        $row = $con->Listar("SELECT 
            f.id_unico,
            DATE_FORMAT(f.fecha_factura, '%d/%m/%Y'), 
            UPPER(tf.prefijo), tf.nombre, 
            f.numero_factura, SUM(df.valor_total_ajustado), GROUP_CONCAT(df.id_unico ), 
            GROUP_CONCAT(df.detallecomprobante) 
        FROM gp_factura f 
        LEFT JOIN gp_detalle_factura df ON df.factura = f.id_unico 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico
        WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' AND tf.id_unico = ".$_REQUEST['tipof']."
        GROUP BY f.id_unico 
        ORDER BY f.fecha_factura, f.numero_factura");
    }

        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<title>Informe Relación Facturación</title>';
        echo '</head>';
        echo '<body>';
        echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
        echo '<th colspan="16" align="center"><strong>';
        echo '<br/>&nbsp;<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
        echo '<br/>&nbsp;<br/>Informe Relación Facturación';
        echo '<br/>&nbsp;</strong></th>';


        echo '<tr>';
        echo '<td><center><strong>FECHA FACTURA </strong></center></td>';
        echo '<td><center><strong>TIPO FACTURA </strong></center></td>';
        echo '<td><center><strong>NÚMERO FACTURA </strong></center></td>';
        echo '<td><center><strong>VALOR FACTURA</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';

        echo '</tr>';
        echo '<tbody>';
        $r =1;
        $total_facturas = 0;
        $total_recaudos = 0;
        $total_ing_cnt  = 0;
        $total_ing_pptal= 0;
        $total_ing_caus = 0;
        for ($i = 0; $i < count($row); $i++) {
            echo '<tr>';
            #***** Datos Factura *******#
            echo '<td>'.$row[$i][1].'</td>';
            echo '<td>'.$row[$i][2].' - '.$row[$i][3].'</td>';
            echo '<td>'.$row[$i][4].'</td>';
            echo '<td>'.number_format($row[$i][5],2,'.',',').'</td>';
            #***** Buscar Parte Contable ***********#
            $drc = $row[$i][7];
            $rowcn = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                SUM(IF((dc.valor<0),valor*-1, valor)),
                GROUP_CONCAT(DISTINCT dc.detallecomprobantepptal) 
            FROM gp_factura f 
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
            LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND f.numero_factura = cn.numero 
            LEFT JOIN gf_detalle_comprobante dc ON dc.comprobante = cn.id_unico 
            WHERE f.id_unico =".$row[$i][0]);
            echo '<td>'.$rowcn[0][1].'</td>';
            echo '<td>'.$rowcn[0][2].'</td>';
            echo '<td>'.$rowcn[0][3].'</td>';
            #** Buscar Valor Documento ***#
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante 
                WHERE comprobante IN (".$rowcn[0][0].") ");
            $vdoc_cnt =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_cnt,2,'.',',').'</td>';
            #*****  Buscar Comprobante Ingreso Pptal ***********#
            $drp = $rowcn[0][5];
            $rowdp = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tcp.codigo), tcp.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                SUM(dc.valor)
            FROM gp_factura f 
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
            LEFT JOIN gf_comprobante_pptal cn ON cn.numero = f.numero_factura AND cn.tipocomprobante = tcp.id_unico
            LEFT JOIN gf_detalle_comprobante dc ON dc.comprobante = cn.id_unico 
            WHERE f.id_unico =".$row[$i][0]);
            echo '<td>'.$rowdp[0][1].'</td>';
            echo '<td>'.$rowdp[0][2].'</td>';
            echo '<td>'.$rowdp[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante_pptal
                WHERE comprobantepptal IN (".$rowdp[0][0].") ");
            $vdoc_pptal =$vdoc[0][0];
            echo '<td>'.number_format($vdoc_pptal,2,'.',',').'</td>';
            #*****  Buscar Comprobante Afectacion ***********#
            $rowca = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                (SUM(IF((dc.valor<0),valor*-1, valor))/2) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.detalleafectado IN($drc)");
            echo '<td>'.$rowca[0][1].'</td>';
            echo '<td>'.$rowca[0][2].'</td>';
            echo '<td>'.$rowca[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante
                WHERE comprobante IN (".$rowca[0][0].") ");
            $vdoc_csc =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_csc,2,'.',',').'</td>';
            echo '</tr>';
            $total_facturas += $row[$i][5];
            $total_ing_cnt  += $vdoc_cnt;
            $total_ing_pptal+= $vdoc_pptal;
            $total_ing_caus += $vdoc_csc;
        }
        echo '<tr>';
        echo '<td colspan="3"><strong>TOTAL FACTURAS</strong></td>';
        echo '<td><strong>'.number_format($total_facturas,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CONTABLES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_cnt,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO PRESUPUESTALES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_pptal,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CAUSACIÓN</strong></td>';
        echo '<td><strong>'.number_format($total_ing_caus,2,'.',',').'</strong></td>';
        echo '<tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
}elseif($t==3){
    if(empty($_REQUEST['tipoP'])){
        $row = $con->Listar("SELECT 
            f.id_unico,
            DATE_FORMAT(f.fecha_pago, '%d/%m/%Y'), 
            UPPER(tf.nombre), tf.nombre, 
            f.numero_pago, SUM(df.valor+df.iva+df.impoconsumo+df.ajuste_peso), GROUP_CONCAT(df.id_unico ), 
            GROUP_CONCAT(df.detallecomprobante) 
        FROM gp_pago f 
        LEFT JOIN gp_detalle_pago df ON df.pago = f.id_unico 
        LEFT JOIN gp_tipo_pago tf ON f.tipo_pago = tf.id_unico
        WHERE f.fecha_pago BETWEEN '$fechaI' AND '$fechaF' 
        GROUP BY f.id_unico 
        ORDER BY f.fecha_pago, f.numero_pago");
    } else {
        $row = $con->Listar("SELECT 
            f.id_unico,
            DATE_FORMAT(f.fecha_pago, '%d/%m/%Y'), 
            UPPER(tf.nombre), tf.nombre, 
            f.numero_pago, SUM(df.valor+df.iva+df.impoconsumo+df.ajuste_peso), GROUP_CONCAT(df.id_unico ), 
            GROUP_CONCAT(df.detallecomprobante) 
        FROM gp_pago f 
        LEFT JOIN gp_detalle_pago df ON df.pago = f.id_unico 
        LEFT JOIN gp_tipo_pago tf ON f.tipo_pago = tf.id_unico
        WHERE f.fecha_pago BETWEEN '$fechaI' AND '$fechaF' AND tf.id_unico = ".$_REQUEST['tipoP']."
        GROUP BY f.id_unico 
        ORDER BY f.fecha_pago, f.numero_pago");
    }

        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<title>Informe Relación Facturación</title>';
        echo '</head>';
        echo '<body>';
        echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
        echo '<th colspan="16" align="center"><strong>';
        echo '<br/>&nbsp;<br/>'.$razonsocial;
        echo '<br/>'.$nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer;
        echo '<br/>&nbsp;<br/>Informe Relación Facturación';
        echo '<br/>&nbsp;</strong></th>';


        echo '<tr>';
        echo '<td><center><strong>FECHA PAGO </strong></center></td>';
        echo '<td><center><strong>TIPO PAGO </strong></center></td>';
        echo '<td><center><strong>NÚMERO PAGO </strong></center></td>';
        echo '<td><center><strong>VALOR PAGO</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CONTABLE</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO PRESUPUESTAL</strong></center></td>';
        echo '<td><center><strong>FECHA COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>TIPO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>NÚMERO COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';
        echo '<td><center><strong>VALOR COMPROBANTE <br/>INGRESO CAUSACIÓN</strong></center></td>';

        echo '</tr>';
        echo '<tbody>';
        $r =1;
        $total_facturas = 0;
        $total_recaudos = 0;
        $total_ing_cnt  = 0;
        $total_ing_pptal= 0;
        $total_ing_caus = 0;
        for ($i = 0; $i < count($row); $i++) {
            echo '<tr>';
            #***** Datos Factura *******#
            echo '<td>'.$row[$i][1].'</td>';
            echo '<td>'.$row[$i][2].'</td>';
            echo '<td>'.$row[$i][4].'</td>';
            echo '<td>'.number_format($row[$i][5],2,'.',',').'</td>';
            #***** Buscar Parte Contable ***********#
            $drc = $row[$i][7];
            $rowcn = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                SUM(IF((dc.valor<0),valor*-1, valor)),
                GROUP_CONCAT(DISTINCT dc.detallecomprobantepptal) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.id_unico IN ($drc)");
            echo '<td>'.$rowcn[0][1].'</td>';
            echo '<td>'.$rowcn[0][2].'</td>';
            echo '<td>'.$rowcn[0][3].'</td>';
            #** Buscar Valor Documento ***#
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante 
                WHERE comprobante IN (".$rowcn[0][0].") ");
            $vdoc_cnt =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_cnt,2,'.',',').'</td>';
            #*****  Buscar Comprobante Ingreso Pptal ***********#
            $drp = $rowcn[0][5];
            $rowdp = $con->Listar("SELECT GROUP_CONCAT(cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tc.codigo), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                SUM(dc.valor)
            FROM gf_detalle_comprobante_pptal dc 
            LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.id_unico IN ($drp)");
            echo '<td>'.$rowdp[0][1].'</td>';
            echo '<td>'.$rowdp[0][2].'</td>';
            echo '<td>'.$rowdp[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante_pptal
                WHERE comprobantepptal IN (".$rowdp[0][0].") ");
            $vdoc_pptal =$vdoc[0][0];
            echo '<td>'.number_format($vdoc_pptal,2,'.',',').'</td>';
            
            #*****  Buscar Comprobante Afectacion ***********#
            $rowca = $con->Listar("SELECT GROUP_CONCAT(DISTINCT cn.id_unico),
                GROUP_CONCAT(DISTINCT DATE_FORMAT(cn.fecha,'%d/%m/%Y')), 
                GROUP_CONCAT(DISTINCT ' - ', CONCAT_WS(' - ',UPPER(tc.sigla), tc.nombre)),
                GROUP_CONCAT(DISTINCT ' - ', cn.numero), 
                (SUM(IF((dc.valor<0),valor*-1, valor))/2) 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
            WHERE dc.detalleafectado IN($drc)");
            echo '<td>'.$rowca[0][1].'</td>';
            echo '<td>'.$rowca[0][2].'</td>';
            echo '<td>'.$rowca[0][3].'</td>';
            $vdoc = $con->Listar("SELECT SUM(IF((valor<0),valor*-1, valor)) 
                FROM gf_detalle_comprobante
                WHERE comprobante IN (".$rowca[0][0].") ");
            $vdoc_csc =$vdoc[0][0]/2;
            echo '<td>'.number_format($vdoc_csc,2,'.',',').'</td>';
            echo '</tr>';
            $total_facturas += $row[$i][5];
            $total_ing_cnt  += $vdoc_cnt;
            $total_ing_pptal+= $vdoc_pptal;
            $total_ing_caus += $vdoc_csc;
        }
        echo '<tr>';
        echo '<td colspan="3"><strong>TOTAL PAGOS</strong></td>';
        echo '<td><strong>'.number_format($total_facturas,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CONTABLES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_cnt,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO PRESUPUESTALES</strong></td>';
        echo '<td><strong>'.number_format($total_ing_pptal,2,'.',',').'</strong></td>';
        echo '<td colspan="3"><strong>TOTAL COMPROBANTES INGRESO CAUSACIÓN</strong></td>';
        echo '<td><strong>'.number_format($total_ing_caus,2,'.',',').'</strong></td>';
        echo '<tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
}
