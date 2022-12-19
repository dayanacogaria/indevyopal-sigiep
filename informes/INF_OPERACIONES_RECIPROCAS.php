<?php
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require './../jsonPptal/funcionesPptal.php';               
ini_set('max_execution_time', 0);
session_start(); 
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$num_anno   = anno($_SESSION['anno']);
#******* Buscar Mes 12 Del Año ****#
$ms = $con->Listar("SELECT id_unico FROM gf_mes WHERE parametrizacionanno = $anno AND numero = 12");
$mes = $ms[0][0];
#**********Recepción Variables ****************#
$informe    = $_POST['sltInforme'];
$exportar   = $_POST['sltExportar'];
$separador  = $_POST['separador'];
$mesI       = $_POST['mesI'];
$mesF       = $_POST['mesF'];

$fechaI = $num_anno.'-'.$mesI.'-01';
$diaf   = diaf($mesF,$num_anno);
$fechaF =$num_anno.'-'.$mesF.'-'.$diaf;
generarBalanceGeneral($num_anno, $anno, $fechaI, $fechaF);
if($separador == 'tab') {	
    $separador = "\t";		
}
#**********************************************#
$row = $con->Listar("SELECT 
    IF(length(tm.numero_cuenta)=6, 
    CONCAT_WS('.', (SUBSTRING(tm.numero_cuenta, 1, 1)),(SUBSTRING(tm.numero_cuenta, 2, 1)),(SUBSTRING(tm.numero_cuenta, 3,2)),(SUBSTRING(tm.numero_cuenta, 5,2))),
    IF(length(tm.numero_cuenta)=4, 
            CONCAT_WS('.', (SUBSTRING(tm.numero_cuenta, 1, 1)),(SUBSTRING(tm.numero_cuenta, 2, 1)),(SUBSTRING(tm.numero_cuenta, 3,2))),
    IF(length(tm.numero_cuenta)=2, 
            CONCAT_WS('.', (SUBSTRING(tm.numero_cuenta, 1, 1)),(SUBSTRING(tm.numero_cuenta, 2, 1))),(SUBSTRING(tm.numero_cuenta, 1))
    ))) as cuenta,  
   nuevo_saldo as saldo_corriente, 
   nuevo_saldo as saldo_no_corriente, 
   length(tm.numero_cuenta), c.tercero_reciproca,  tr.codigo_dane, 
   c.id_unico , c.tipocuentacgn 
FROM temporal_balance$anno tm 
LEFT JOIN gf_cuenta c ON tm.id_cuenta = c.id_unico 
LEFT JOIN gf_tercero tr ON c.tercero_reciproca = tr.id_unico 
WHERE length(tm.numero_cuenta)=6  AND tm.nuevo_saldo IS NOT NULL 
ORDER BY tm.numero_cuenta");   
$html ="";
for ($i = 0; $i < count($row); $i++) {            
    $id_cuenta = $row[$i][6];
    if($exportar==3){
        IF(empty($row[$i][4])){
            #Buscar terceros comprobante
            $buscarc = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gf_cuenta WHERE predecesor = $id_cuenta or id_unico = $id_cuenta");
            $id_cuentas = $buscarc[0][0];
            $rowtr = $con->Listar("SELECT  DISTINCT  dc.tercero, tr.codigo_dane 
            FROM  gf_detalle_comprobante dc   
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico
            LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico  = dc.comprobante
            LEFT JOIN gf_tercero tr  ON tr.id_unico = dc.tercero                        
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
            LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
            WHERE c.id_unico    IN ($id_cuentas)  
            AND  cn.parametrizacionanno =$anno AND cc.id_unico !='20' 
            AND dc.tercero != $compania 
            ORDER BY tr.numeroidentificacion ASC");
            for ($t = 0; $t < count($rowtr); $t++) {
                $id_tercero = $rowtr[$t][0];
                if(!empty($rowtr[$t][1])){
                    #SI FECHA INICIAL =01 DE ENERO
                    $fechaPrimera = $num_anno . '-01-01';
                    if ($fechaI == $fechaPrimera) {
                        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                        $fechaMax = $num_anno . '-12-31';
                        $com = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaMax' 
                                      AND cc.id_unico = '5' 
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero ");
                        if (!empty($com[0][0])) {
                            $saldo = $com[0][0];
                        } else {
                            $saldo = 0;
                        }

                        #DEBITOS
                        $deb = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE valor>0 AND 
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                      AND cc.id_unico != '5' AND cc.id_unico != '20'  
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero");
                        if (!empty($deb[0][0])) {
                            $debito = $deb[0][0];
                        } else {
                            $debito = 0;
                        }

                        #CREDITOS
                        $cr = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE valor<0 AND 
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                      AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero");
                        if (!empty($cr[0][0])) {
                            $credito =$cr[0][0];
                        } else {
                            $credito = 0;
                        }

                #SI FECHA INICIAL !=01 DE ENERO
                    } else {
                        #TRAE EL SALDO INICIAL
                        $sInicial = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaI' 
                                AND dc.cuenta IN ($id_cuentas) 
                                AND cp.parametrizacionanno =$anno 
                                AND dc.tercero = $id_tercero AND cc.id_unico !='20'");
                        if (!empty($sInicial[0][0])) {
                            $saldo =$sInicial[0][0];
                        } else {
                            $saldo = 0;
                        }
                        #DEBITOS
                        $deb = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor>0 AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                    AND dc.cuenta IN ($id_cuentas) 
                                    AND cp.parametrizacionanno =$anno 
                                    AND dc.tercero = $id_terceroAND cc.id_unico !='20'");
                        if (!empty($deb[0][0])) {
                            $debito = $deb[0][0];
                        } else {
                            $debito = 0;
                        }
                        #CREDITOS
                        $cr = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor<0  
                                AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                AND dc.cuenta IN ($id_cuentas) 
                                AND cp.parametrizacionanno =$anno 
                                AND dc.tercero = $id_tercero AND cc.id_unico !='20'");
                        if (!empty($cr[0][0])) {
                            $credito =$cr[0][0];
                        } else {
                            $credito = 0;
                        }
                    }
                    #SI LA NATURALEZA ES DEBITO
                    if ($rowtr[$t][2] == '1') {
                        if ($credito < 0) {
                            $credito = (float) substr($credito, '1');
                        }
                        $saldoNuevo = $saldo + $debito - $credito;
                        #SI LA NATURALEZA ES CREDITO
                    } else {
                        if ($credito < 0) {
                            $credito = (float) substr($credito, '1');
                        }
                        $saldoNuevo = $saldo - $credito + $debito;
                    }
                    if($saldoNuevo !=0){
                    $html .='<tr>';
                    $html .='<td>D</td>';
                    $html .='<td>'.$row[$i][0].'</td>';
                    $html .='<td>'.$rowtr[$t][1].'</td>';
                    if($row[$i][7]==6){
                        $html .='<td>0</td>';
                        $html .='<td>'.$saldoNuevo.'</td>';
                    } else {
                        $html .='<td>'.$saldoNuevo.'</td>';
                        $html .='<td>0</td>';                        
                    }
                    $html .='</tr>';
                }
                }
            }
        } else {
            $html .='<tr>';
            $html .='<td>D</td>';
            $html .='<td>'.$row[$i][0].'</td>';
            $html .='<td>'.$row[$i][5].'</td>';
            if($row[$i][7]==6){
                $html .='<td>0</td>';
                $html .='<td>'.$row[$i][2].'</td>';
            } else {
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>0</td>';
            }
            $html .='</tr>';
        }        
    } else{
                
        IF(empty($row[$i][4])){
            #Buscar terceros comprobante
            $buscarc = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gf_cuenta WHERE predecesor = $id_cuenta or id_unico = $id_cuenta");
            $id_cuentas = $buscarc[0][0];
            $rowtr = $con->Listar("SELECT  DISTINCT  dc.tercero, tr.codigo_dane 
            FROM  gf_detalle_comprobante dc   
            LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico
            LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico  = dc.comprobante
            LEFT JOIN gf_tercero tr  ON tr.id_unico = dc.tercero                        
            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
            LEFT JOIN gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
            WHERE c.id_unico    IN ($id_cuentas)  
            AND  cn.parametrizacionanno =$anno AND cc.id_unico !='20' 
            AND dc.tercero != $compania      
            ORDER BY tr.numeroidentificacion ASC");
            for ($t = 0; $t < count($rowtr); $t++) {
                $id_tercero = $rowtr[$t][0];
                if(!empty($rowtr[$t][1])){
                    #SI FECHA INICIAL =01 DE ENERO
                    $fechaPrimera = $num_anno . '-01-01';
                    if ($fechaI == $fechaPrimera) {
                        #CONSULTA EL SALDO DE LA CUENTA COMPROBANTE CLASE 5-SALDOS INICIALES
                        $fechaMax = $num_anno . '-12-31';
                        $com = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaMax' 
                                      AND cc.id_unico = '5' 
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero ");
                        if (!empty($com[0][0])) {
                            $saldo = $com[0][0];
                        } else {
                            $saldo = 0;
                        }

                        #DEBITOS
                        $deb = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE valor>0 AND 
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                      AND cc.id_unico != '5' AND cc.id_unico != '20'  
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero");
                        if (!empty($deb[0][0])) {
                            $debito = $deb[0][0];
                        } else {
                            $debito = 0;
                        }

                        #CREDITOS
                        $cr = $con->Listar("SELECT SUM(valor)
                                    FROM
                                      gf_detalle_comprobante dc
                                    LEFT JOIN
                                      gf_comprobante_cnt cp ON dc.comprobante = cp.id_unico
                                    LEFT JOIN
                                      gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico
                                    LEFT JOIN
                                      gf_clase_contable cc ON tc.clasecontable = cc.id_unico
                                    WHERE valor<0 AND 
                                      cp.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                      AND cc.id_unico != '5' AND cc.id_unico != '20' 
                                      AND dc.cuenta IN ($id_cuentas) 
                                      AND cp.parametrizacionanno =$anno 
                                      AND dc.tercero = $id_tercero");
                        if (!empty($cr[0][0])) {
                            $credito =$cr[0][0];
                        } else {
                            $credito = 0;
                        }

                #SI FECHA INICIAL !=01 DE ENERO
                    } else {
                        #TRAE EL SALDO INICIAL
                        $sInicial = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE cn.fecha >='$fechaPrimera' AND cn.fecha <'$fechaI' 
                                AND dc.cuenta IN ($id_cuentas) 
                                AND cp.parametrizacionanno =$anno 
                                AND dc.tercero = $id_tercero AND cc.id_unico !='20'");
                        if (!empty($sInicial[0][0])) {
                            $saldo =$sInicial[0][0];
                        } else {
                            $saldo = 0;
                        }
                        #DEBITOS
                        $deb = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor>0 AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                    AND dc.cuenta IN ($id_cuentas) 
                                    AND cp.parametrizacionanno =$anno 
                                    AND dc.tercero = $id_terceroAND cc.id_unico !='20'");
                        if (!empty($deb[0][0])) {
                            $debito = $deb[0][0];
                        } else {
                            $debito = 0;
                        }
                        #CREDITOS
                        $cr = $con->Listar("SELECT SUM(dc.valor) 
                                FROM 
                                    gf_detalle_comprobante dc 
                                LEFT JOIN 
                                    gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                                LEFT JOIN
                                    gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                                LEFT JOIN
                                    gf_clase_contable cc ON tc.clasecontable = cc.id_unico 
                                WHERE dc.valor<0  
                                AND cn.fecha BETWEEN '$fechaI' AND '$fechaF' 
                                AND dc.cuenta IN ($id_cuentas) 
                                AND cp.parametrizacionanno =$anno 
                                AND dc.tercero = $id_tercero AND cc.id_unico !='20'");
                        if (!empty($cr[0][0])) {
                            $credito =$cr[0][0];
                        } else {
                            $credito = 0;
                        }
                    }
                    #SI LA NATURALEZA ES DEBITO
                    if ($rowtr[$t][2] == '1') {
                        if ($credito < 0) {
                            $credito = (float) substr($credito, '1');
                        }
                        $saldoNuevo = $saldo + $debito - $credito;
                        #SI LA NATURALEZA ES CREDITO
                    } else {
                        if ($credito < 0) {
                            $credito = (float) substr($credito, '1');
                        }
                        $saldoNuevo = $saldo - $credito + $debito;
                    }
                    if($saldoNuevo !=0){
                    $html .=str_replace(',',' ','D')."$separador";
                    $html .=str_replace(',',' ',$row[$i][0])."$separador";
                    $html .=str_replace(',',' ',$rowtr[$t][1])."$separador";
                    if($row[$i][7]==6){
                        $html .=str_replace(',',' ',0)."$separador";
                        $html .=str_replace(',',' ',$saldoNuevo);
                    } else {
                        $html .=str_replace(',',' ',$saldoNuevo)."$separador";
                        $html .=str_replace(',',' ',0);
                        
                    }
                    $html .= "\n";
                }
                }
            }
        } else {   
            $html .=str_replace(',',' ','D')."$separador";
            $html .=str_replace(',',' ',$row[$i][0])."$separador";
            $html .=str_replace(',',' ',$row[$i][5])."$separador";
            if($row[$i][7]==6){
                $html .=str_replace(',',' ',0)."$separador";
                $html .=str_replace(',',' ',$row[$i][1]);
            } else {
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',0);
            }
            $html .= "\n";
        }
        
    }

}
switch ($exportar){
    #*** csv ***#
    case 1:
        header("Content-Disposition: attachment; filename=Operaciones_Reciprocas.csv");
        ini_set('max_execution_time', 0);
        echo $html;
    break;
    #*** txt ***#   
    case 2:
        header("Content-Disposition: attachment; filename=Operaciones_Reciprocas.txt");
        ini_set('max_execution_time', 0);
        echo $html;
    break;
    #*** xls ***#
    case 3:
        header("Content-Disposition: attachment; filename=Operaciones_Reciprocas.xls");
        ini_set('max_execution_time', 0);
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Operaciones Reciprocas</title>
            </head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><center><strong>D</strong></center></td>
                        <td><center><strong>CUENTA</strong></center></td>
                        <td><center><strong>TERCERO</strong></center></td>
                        <td><center><strong>SALDO CORRIENTE</strong></center></td>
                        <td><center><strong>SALDO NO CORRIENTE</strong></center></td>
                    </tr>
                    <?php echo $html; ?>
                </table>
            </body>
        </html>
        <?php             
    break;
}
