<?php 
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
#******* Buscar Mes 12 Del Año ****#
$ms = $con->Listar("SELECT id_unico FROM gf_mes WHERE parametrizacionanno = $anno AND numero = 12");
$mes = $ms[0][0];
#**********Recepción Variables ****************#
$informe    = $_POST['sltInforme'];
$exportar   = $_POST['sltExportar'];
$separador  = $_POST['separador'];
if($separador == 'tab') {	
    $separador = "\t";		
}
#**********************************************#
switch ($informe){
    #**** Movimiento De Bancos *****# 
    case 1:
        
        $row = $con->Listar("SELECT DISTINCT 
            REPLACE(b.razonsocial,',',' ') as 'Banco',
            REPLACE(CONCAT_WS(' ',cb.numerocuenta,cb.descripcion),',',' '), 
            REPLACE(d.nombre,',',' ') as 'Denominación',
            REPLACE(rf.nombre,',',' ') as 'Fuente De Financiación' , 
            c.id_unico, c.naturaleza, c.codi_cuenta, 
            (if((pc.saldo_extracto) is null ,'0.00', (pc.saldo_extracto))) as 'Saldo Extracto Bancario' 
            FROM gf_cuenta_bancaria cb 
            LEFT JOIN gf_cuenta c on cb.cuenta = c.id_unico 
            LEFT JOIN gf_tercero b on cb.banco = b.id_unico  
            LEFT JOIN gf_tipo_destinacion d on cb.destinacion = d.id_unico 
            LEFT JOIN gf_partida_conciliatoria pc on pc.id_cuenta = c.id_unico and pc.mes = $mes 
            LEFT JOIN gf_recurso_financiero rf on cb.recursofinanciero = rf.id_unico 
            WHERE cb.parametrizacionanno = $anno and c.clasecuenta IN(11,12)
            ORDER BY c.codi_cuenta");
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            #Buscar Saldo Inicial A 1 Enero
            
            $cuenta = $row[$i][4];
            $natural = $row[$i][5];
            # ***** Saldo Inicial *** #
            $com = $con->Listar("SELECT SUM(dc.valor) 
                FROM
                  gf_detalle_comprobante dc
                LEFT JOIN
                  gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                LEFT JOIN
                  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                WHERE cc.id_unico = '5' 
                  AND dc.cuenta = '$cuenta' AND cp.parametrizacionanno =$anno ");
            $saldoInicial = $com[0][0];
            #******* Validar Valores Detalles ************#
            $det = $con->Listar("SELECT DISTINCT dc.id_unico, dc.valor, dc.comprobante
                    FROM
                      gf_detalle_comprobante dc
                    LEFT JOIN
                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                    LEFT JOIN
                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                    LEFT JOIN
                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                    WHERE cc.id_unico != '5' AND cc.id_unico !='20'  
                      AND dc.cuenta = '$cuenta' AND cp.parametrizacionanno =$anno ");
            $ingresos =0;
            $egresos  =0;
            $notasD   =0;
            $notasC   =0;
            if($natural==1){
                for ($j = 0; $j < count($det); $j++) {
                    #*******Buscar Si Comprobante Tiene Presupuesto ***#
                    $comprobante = $det[$j][2];
                    $valor       = $det[$j][1];
                    $pr = $con->Listar("SELECT DISTINCT dcp.id_unico FROM gf_comprobante_cnt cn 
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
                    LEFT JOIN gf_comprobante_pptal cp ON tcp.id_unico = cp.tipocomprobante AND cn.numero = cp.numero 
                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico 
                    LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                    WHERE cn.id_unico = $comprobante AND rb.tipoclase IN (6,7) AND dcp.id_unico IS NOT NULL");
                    #Si Hay registros, Tiene Parte Presupuestal
                    if(count($pr)>0){
                        if($valor>0){
                            $ingresos += $valor;
                        } else {
                            $egresos  +=$valor *-1;
                        }
                    }
                    #Si NO Hay registros, NO Tiene Parte Presupuestal
                    else {
                        if($valor>0){
                            $notasC   +=$valor *-1;
                        } else {                            
                            $notasD   += $valor;
                        }
                        
                    }
                }
            } elseif($natural==2) {
                for ($j = 0; $j < count($det); $j++) {
                    #*******Buscar Si Comprobante Tiene Presupuesto ***#
                    $comprobante = $det[$j][2];
                    $valor       = $det[$j][1];
                    $pr = $con->Listar("SELECT DISTINCT dcp.id_unico FROM gf_comprobante_cnt cn 
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
                    LEFT JOIN gf_comprobante_pptal cp ON tcp.id_unico = cp.tipocomprobante AND cn.numero = cp.numero 
                    LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico 
                    LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                    LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
                    WHERE cn.id_unico = $comprobante AND rb.tipoclase IN (6,7) AND dcp.id_unico IS NOT NULL");
                    #Si Hay registros, Tiene Parte Presupuestal
                    if(count($pr)>0){
                        if($valor>0){
                            $egresos  +=$valor ;
                            
                        } else {
                            $ingresos += $valor *-1;
                        }
                    }
                    #Si NO Hay registros, NO Tiene Parte Presupuestal
                    else {
                        if($valor>0){
                            $notasD   += $valor*-1;
                        } else {                            
                            $notasC   +=$valor ;                            
                        }
                        
                    }
                }
            }
            $saldoFinal = $saldoInicial + ($ingresos + $notasD) - ($egresos+$notasC);
            if($exportar==3){
               $html .='<tr>';
               $html .='<td>'.$row[$i][0].'</td>';
               $html .='<td>'.$row[$i][6].'</td>';
               $html .='<td>'.$row[$i][1].'</td>';
               $html .='<td>'.$row[$i][3].'</td>';
               $html .='<td>'. number_format($saldoInicial,2,'.',',').'</td>';
               $html .='<td>'. number_format($ingresos,2,'.',',').'</td>';
               $html .='<td>'. number_format($egresos,2,'.',',').'</td>';
               $html .='<td>'. number_format($notasD,2,'.',',').'</td>';
               $html .='<td>'. number_format($notasC,2,'.',',').'</td>';
               $html .='<td>'. number_format($saldoFinal,2,'.',',').'</td>';
               $html .='<td>'. number_format($row[$i][7],2,'.',',').'</td>';
               $html .='</tr>';
            } else{
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=$saldoInicial."$separador";
                $html .=$ingresos."$separador";
                $html .=$egresos."$separador";
                $html .=$notasD."$separador";
                $html .=$notasC."$separador";
                $html .=$saldoFinal."$separador";
                $html .=str_replace(',',' ',$row[$i][7]);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Movimientos_Bancos.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Movimientos_Bancos.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Movimientos_Bancos.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Movimientos Bancos</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>BANCO</strong></center></td>
                                <td><center><strong>N° DE CUENTA</strong></center></td>
                                <td><center><strong>DENOMINACION</strong></center></td>
                                <td><center><strong>FUENTE DE FINANCIACIÓN</strong></center></td>
                                <td><center><strong>SALDO INICIAL</strong></center></td>
                                <td><center><strong>INGRESOS</strong></center></td>
                                <td><center><strong>EGRESOS</strong></center></td>
                                <td><center><strong>NOTAS DÉBITO</strong></center></td>
                                <td><center><strong>NOTAS CREDITO</strong></center></td>
                                <td><center><strong>SALDO LIBROS</strong></center></td>
                                <td><center><strong>SALDO EXTRACTO</strong></center></td>

                            </tr>
                <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    #**** Relación De Pagos *****#
    case 2:
        $html="";
        
        
        $row = $con->Listar("SELECT DISTINCT 
            DATE_FORMAT(cp.fecha, '%Y/%m/%d') as 'Fecha Pago',
            CONCAT_WS('',h.id_destino,rb.codi_presupuesto) as 'Codigo Presupuestal',
            'Normal' as 'Clase De Pago', 
            REPLACE(CONCAT_WS(' ',tp.codigo,tp.nombre),',',' ') as 'Tipo De Pago',
            cp.numero as 'Numero Comprobante',  
            IF(CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) 
                 IS NULL OR CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos) = '',
                 (t.razonsocial),
                 CONCAT_WS(' ',
                 t.nombreuno,
                 t.nombredos,
                 t.apellidouno,
                 t.apellidodos)) AS 'Beneficiario',
                 t.numeroidentificacion as 'Cédula O NIT',
            cp.descripcion as 'Detalle Pago', 
            (SELECT SUM(if(dc1.valor<0, dc1.valor*-1, dc1.valor)) 
                    FROM gf_detalle_comprobante_pptal dc1 
                    WHERE cp.id_unico = dc1.comprobantepptal 
                    AND dc.rubrofuente = dc1.rubrofuente GROUP BY dc1.rubrofuente  ) as 'Valor Comprobante Pago',
             if((SELECT SUM(if(det.valor<0, det.valor*-1, det.valor)) 
                 FROM gf_detalle_comprobante det 
                 LEFT JOIN gf_cuenta cta ON det.cuenta = cta.id_unico 
                 WHERE det.comprobante = dccc.comprobante AND cta.clasecuenta=20 
                 AND det.detallecomprobantepptal IS NULL 
                ) IS NULL,'0.00',(SELECT SUM(if(det.valor<0, det.valor*-1, det.valor)) 
                 FROM gf_detalle_comprobante det 
                 LEFT JOIN gf_cuenta cta ON det.cuenta = cta.id_unico 
                 WHERE det.comprobante = dccc.comprobante AND cta.clasecuenta=20 
                 AND det.detallecomprobantepptal IS NULL 
                )) as 'Descuentos Seg. Social',

            if(tc.retencion=2,
                        if((SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = dccc.comprobante AND clasr.clase_sia=1
                ) IS NULL,'0.00',(SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = dccc.comprobante AND clasr.clase_sia=1
                )), 
                IF((SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = cn.id_unico AND clasr.clase_sia=1) IS NULL, '0.00',  
                 (SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = cn.id_unico AND clasr.clase_sia=1))
              ) as 'Descuentos Retenciones',
              if(tc.retencion=2,
                        if((SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = dccc.comprobante AND clasr.clase_sia=2
                ) IS NULL,'0.00',(SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = dccc.comprobante AND clasr.clase_sia=2
                )), 
                IF((SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = cn.id_unico AND clasr.clase_sia=2) IS NULL, '0.00',  
                 (SELECT SUM(if(ret.valorretencion<0, ret.valorretencion*-1, ret.valorretencion)) 
                 FROM gf_retencion ret 
                 LEFT JOIN gf_tipo_retencion tret ON ret.tiporetencion = tret.id_unico 
                 LEFT JOIN gf_clase_retencion clasr ON tret.claseretencion = clasr.id_unico
                 WHERE ret.comprobante = cn.id_unico AND clasr.clase_sia=2))
              ) as 'Otras Retenciones',

            (SELECT SUM(if(dcv.valor<0, dcv.valor*-1, dcv.valor)) 
                    FROM gf_detalle_comprobante dcv 
                    LEFT JOIN gf_cuenta cd ON dcv.cuenta = cd.id_unico 
                    WHERE dcv.comprobante = cn.id_unico AND (cd.clasecuenta = 11 OR cd.clasecuenta = 12)) as 'Neto Pagado',
            (SELECT GROUP_CONCAT(DISTINCT ' ',t.razonsocial) 
                    FROM gf_cuenta_bancaria cb 
                            LEFT JOIN gf_tercero t ON cb.banco = t.id_unico 
                    LEFT JOIN gf_cuenta ct ON ct.id_unico = cb.cuenta 
                    LEFT JOIN gf_detalle_comprobante dn ON dn.cuenta = ct.id_unico 
                    WHERE dn.comprobante = cn.id_unico)as 'Banco', 
            (SELECT GROUP_CONCAT(DISTINCT ' ',cb.numerocuenta) 
                    FROM gf_cuenta_bancaria cb 
                    LEFT JOIN gf_cuenta ct ON ct.id_unico = cb.cuenta 
                    LEFT JOIN gf_detalle_comprobante dn ON dn.cuenta = ct.id_unico 
                    WHERE dn.comprobante = cn.id_unico)as 'No. De Cuenta', 
            if(
                (SELECT GROUP_CONCAT(DISTINCT ' ',dcm.numero) 
                    FROM gf_detalle_comprobante_mov dcm 
                            LEFT JOIN gf_detalle_comprobante dnc ON dcm.comprobantecnt = dnc.id_unico 
                    WHERE dnc.comprobante = cn.id_unico) IS NULL,'ND',
            (SELECT GROUP_CONCAT(DISTINCT ' ',dcm.numero) 
                    FROM gf_detalle_comprobante_mov dcm 
                    LEFT JOIN gf_detalle_comprobante dnc ON dcm.comprobantecnt = dnc.id_unico 
                    WHERE dnc.comprobante = cn.id_unico))as 'No. De Cheque', 
            (SELECT SUM(dccp.valor) FROM gf_detalle_comprobante_pptal dccp 
                WHERE dccp.comprobantepptal = cp.id_unico) as 'Valor Comprobante', 
                cn.id_unico as 'Id Unico', 
                (SELECT COUNT(dccp.id_unico) FROM gf_detalle_comprobante_pptal dccp WHERE dccp.comprobantepptal = cp.id_unico) as 'Número'
        FROM  gf_detalle_comprobante_pptal dc 
        LEFT JOIN gn_homologaciones h on  h.id_origen = dc.rubrofuente and h.origen = 67
        LEFT JOIN gf_comprobante_pptal cp  ON cp.id_unico = dc.comprobantepptal 
        LEFT JOIN gf_tipo_comprobante_pptal tp ON tp.id_unico = cp.tipocomprobante 
        LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico 
        LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cp.numero = cn.numero 
        LEFT JOIN gf_rubro_fuente rf ON dc.rubrofuente = rf.id_unico 
        LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
        LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
                LEFT JOIN gf_detalle_comprobante_pptal dcpc ON dc.comprobanteafectado = dcpc.id_unico 
                LEFT JOIN gf_detalle_comprobante dccc ON dccc.detallecomprobantepptal = dcpc.id_unico        
        WHERE tp.clasepptal = 17 AND rb.tipovigencia = 1 AND cp.parametrizacionanno = $anno  
            AND rb.tipoclase = 7 
        GROUP BY dc.rubrofuente , cp.id_unico 
        ORDER by rb.codi_presupuesto, cp.tipocomprobante, cp.numero ASC");
       
        if(count($row)>0){
            $x=0;
            for ($i = 0; $i < count($row); $i++) {
                if($row[$i][18]==1){
                    $descuentoSeguridad     =$row[$i][9];
                    $descuentoRetenciones   =$row[$i][10];
                    $otrosDescuentos        =$row[$i][11];
                    $Neto                   =$row[$i][12];
                } else {
                    #*********** Calcular % ****************#
                    $descuentoSeguridad =0;
                    $descuentoRetenciones =0;
                    $otrosDescuentos =0;
                    $Neto =0;
                    $cantidadTotal = $row[$i][16];
                    #**** Hallar Porcentaje *****#
                    $porcentaje = ($row[$i][8]*100)/$cantidadTotal;
                    #* Descuentos SS 
                    if($row[$i][9] !=0){ 
                        $descuentoSeguridad = round((($row[$i][9]*$porcentaje)/100),2);
                        if($row[$i][12] > $cantidadTotal){
                            #Buscar Detalles Que Solo Esten Relacionados Presupuestalmente
                            $descuentoSeguridad = 0;
                        } else {
                            $descuentoSeguridad = round((($row[$i][9]*$porcentaje)/100),2);
                        }
                    }
                    
                    #* Descuentos Rt
                    if($row[$i][10] !=0){ 
                        $descuentoRetenciones = round((($row[$i][10]*$porcentaje)/100),2);
                    }
                    #* Descuentos Otros
                    if($row[$i][11] !=0){ 
                        $otrosDescuentos = round((($row[$i][11]*$porcentaje)/100),2);
                    }
                    #* Neto
                    if($row[$i][12] !=0){ 

//                        if($row[$i][12] > $cantidadTotal){
//                            #Buscar Detalles Que Solo Esten Relacionados Presupuestalmente
//                            $ccom = $row[$i][17];
//                            $sn = $con->Listar("SELECT SUM(valor*-1) FROM gf_detalle_comprobante where comprobante = $ccom AND valor<0 and (detalleafectado IS NOT NULL OR detallecomprobantepptal is not null)");
//                            $Ne = $sn[0][0];
//                            $Neto = round((($Ne*$porcentaje)/100),2);
//                        } else {
                            $Neto = round((($row[$i][12]*$porcentaje)/100),2);
//                        }
                    }
                #***************************************#
                }
                if($exportar==3){
                    ##***Imprimir los datos del egreso
                    $html .='<tr>';
                    #* Fecha Pago
                    $html .='<td align="left">'.$row[$i][0].'</td>';
                    #* Código 
                    $html .='<td align="left">'.$row[$i][1].'</td>';
                    #* Clase
                    $html .='<td align="left">'.mb_strtoupper($row[$i][2]).'</td>';
                    #* Tipo Pago
                    $html .='<td align="left">'.$row[$i][3].'</td>';
                    #* Numero Comprobante 
                    $html .='<td align="left">'.$row[$i][4].'</td>';
                    #* Beneficiario
                    $html .='<td align="left">'.$row[$i][5].'</td>';
                    #* Nit 
                    $html .='<td align="left">'.$row[$i][6].'</td>';
                    #* Detalle 
                    $html .='<td align="left">'.$row[$i][7].'</td>';
                    #* Valor Pago
                    $html .='<td align="left">'.$row[$i][8].'</td>';
                    #* Descuentos SS 
                    $html .='<td align="left">'.$descuentoSeguridad.'</td>';
                    #* Descuentos Rt
                    $html .='<td align="left">'.$descuentoRetenciones.'</td>';
                    #* Descuentos Otros
                    $html .='<td align="left">'.$otrosDescuentos.'</td>';
                    #* Neto
                    $html .='<td align="left">'.$Neto.'</td>';
                    #* Banco
                    $html .='<td align="left">'.$row[$i][13].'</td>';
                    #* N° Cuenta
                    $html .='<td align="left">'.$row[$i][14].'</td>';
                    #* Cheque
                    $html .='<td align="left">'.$row[$i][15].'</td>';
                    $html .='</tr>';
                } else{
                    ##***Imprimir los datos del egreso
                    $html .=str_replace(',',' ',$row[$i][0])."$separador";
                    #* Código 
                    $html .=str_replace(',',' ',$row[$i][1])."$separador";
                    #* Clase
                    $html .=str_replace(',',' ',mb_strtoupper($row[$i][2]))."$separador";
                    #* Tipo Pago
                    $html .=str_replace(',',' ',$row[$i][3])."$separador";
                    #* Numero Comprobante 
                    $html .=str_replace(',',' ',$row[$i][4])."$separador";
                    #* Beneficiario
                    $html .=str_replace(',',' ',$row[$i][5])."$separador";
                    #* Nit 
                    $html .=str_replace(',',' ',$row[$i][6])."$separador";
                    #* Detalle 
                    $html .=str_replace(',',' ',$row[$i][7])."$separador";
                    #* Valor Pago
                    $html .=$row[$i][8]."$separador";
                    #* Descuentos SS 
                    $html .=$descuentoSeguridad."$separador";
                    #* Descuentos Rt
                    $html .=$descuentoRetenciones."$separador";
                    #* Descuentos Otros
                    $html .=$otrosDescuentos."$separador";
                    #* Neto
                    $html .=$Neto."$separador";
                    #* Banco
                    $html .=str_replace(',',' ',$row[$i][13])."$separador";
                    #* N° Cuenta
                    $html .=str_replace(',',' ',$row[$i][14])."$separador";
                    #* Cheque
                    $html .=str_replace(',',' ',$row[$i][15]);
                    $html .= "\n";
                }
            }    
        }
        
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Relacion_Pagos.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Relacion_Pagos.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
               header("Content-Disposition: attachment; filename=Relacion_Pagos.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Relación De Pagos</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>FECHA DE PAGO</strong></center></td>
                                <td><center><strong>CÓDIGO PRESUPUESTAL</strong></center></td>
                                <td><center><strong>CLASE DE PAGO</strong></center></td>
                                <td><center><strong>TIPO DE PAGO</strong></center></td>
                                <td><center><strong>N° DE COMPORBANTE</strong></center></td>
                                <td><center><strong>BENEFICIARIO</strong></center></td>
                                <td><center><strong>CÉDULA O NIT</strong></center></td>
                                <td><center><strong>DETALLE DE PAGO</strong></center></td>
                                <td><center><strong>VALOR COMPROBANTE PAGO</strong></center></td>
                                <td><center><strong>DESCUENTOS SEGURIDAD SOCIAL</strong></center></td>
                                <td><center><strong>DESCUENTOS RETENCIONES</strong></center></td>
                                <td><center><strong>OTROS DESCUENTOS</strong></center></td>
                                <td><center><strong>NETO PAGADO</strong></center></td>
                                <td><center><strong>BANCO</strong></center></td>
                                <td><center><strong>N° DE CUENTA</strong></center></td>
                                <td><center><strong>N° DE CHEQUE</strong></center></td>
                                

                            </tr>
                <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php 
            break;
        }
        
    break;
    
    #*** Almacén ***#
    case 3:
        $row = $con->Listar("SELECT m.fecha, CONCAT(tm.sigla,' - ',tm.nombre), 
            SUM(dm.valor),  
            CONCAT(pi.codi,' - ',pi.nombre ), c.codi_cuenta 
            FROM gf_movimiento m 
            LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
            LEFT JOIN gf_tipo_movimiento tm ON tm.id_unico = m.tipomovimiento 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            LEFT JOIN gf_configuracion_almacen cf ON cf.plan_inventario = pi.id_unico 
                    AND cf.tipo_movimiento = tm.id_unico 
            LEFT JOIN gf_cuenta c ON cf.cuenta_debito = c.id_unico 
            WHERE tm.clase = 2 AND m.parametrizacionanno = $anno  
            GROUP BY pi.id_unico 
            ORDER BY m.fecha, pi.codi ASC ");
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            if($exportar==3){
               $html .='<tr>';
               $html .='<td>'.$row[$i][0].'</td>';
               $html .='<td>'.$row[$i][1].'</td>';
               $html .='<td>'.number_format($row[$i][2],2,'.',',').'</td>';
               $html .='<td>'.$row[$i][3].'</td>';
               $html .='<td>'.$row[$i][4].'</td>';
               $html .='</tr>';
            } else{
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=$row[$i][2]."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4]);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Movimientos_Almacen.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Movimientos_Almacen.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Movimientos_Almacen.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Movimientos Almacén</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>FECHA</strong></center></td>
                                <td><center><strong>CONCEPTO</strong></center></td>
                                <td><center><strong>VALOR</strong></center></td>
                                <td><center><strong>DETALLE</strong></center></td>
                                <td><center><strong>CÓDIGO CONTABLE</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;

}

