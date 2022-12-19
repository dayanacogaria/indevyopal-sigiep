<?php 
require'../Conexion/ConexionPDO.php';
require'../Conexion/conexion.php';
require './../jsonPptal/funcionesPptal.php';               
require './../jsonServicios/funcionesServicios.php';       
ini_set('max_execution_time', 0);
session_start(); 
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$num_anno   = anno($_SESSION['anno']);
#******* Buscar Mes 12 Del Año ****#
$ms = $con->Listar("SELECT id_unico FROM gf_mes WHERE parametrizacionanno = $anno AND numero = 12");
$mes = $ms[0][0];
#**********Recepción Variables ****************#
$informe    = $_POST['sltInforme'];
$exportar   = $_POST['sltExportar'];
$separador  = $_POST['separador'];
$periodoI   = $_POST['periodoI'];
$periodoF   = $_POST['periodoF'];
if($separador == 'tab') {	
    $separador = "\t";		
}
#**********************************************#
switch ($informe){
    #**** IGAC ACUEDUCTO *****# 
    case 1:
        
        $row = $con->Listar("SELECT '0000' as CODIGO, 
            '0000' as NUM_CONTRA, 
            d.rss as DEPTO, 
            c.rss as MUNICIPIO, 
            '99' as ZONA_IGAC, 
            '99' as SEC_IGAC, 
            '9999' as MAZ_IGAC, 
            '9999' as NUMPRE_IGAC,
            '0' as CONPRE_IGAC, 
            p.direccion as DIRECCION, 
            f.numero_factura as NUM_FAC, 
            DATE_FORMAT(f.fecha_factura, '%d-%m-%Y') as FECHA_FAC, 
            DATE_FORMAT(pr.fecha_inicial, '%d-%m-%Y') as FECHA_INI, 
            30 as DIAS_COB, 
            es.codigo as CLA_USO, 
            0 as UNIDM_RES, 
            0 as UNIDM_NRES, 
            if(uv.hogar=1,1, 0) as HOGAR_COMOSUS, 
            if(m.estado_medidor = 2, 1,2), 
            1 as DETER_USO, 
            l.valor as VLA, 
            pr.id_unico, 
            uvms.id_unico, 
            f.id_unico, 
            uvs.id_unico, 
            f.fecha_factura, 
            l.cantidad_facturada  
            FROM 
              gp_factura f 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv  ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
            LEFT JOIN gf_ciudad c ON p.ciudad = c.id_unico 
            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
            LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
            LEFT JOIN gp_estrato es ON uv.estrato = es.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = uvms.id_unico AND l.periodo = pr.id_unico 
            WHERE f.parametrizacionanno = $anno AND (f.periodo BETWEEN  $periodoI AND $periodoF) 
            ORDER BY f.id_unico");
        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            $l_actual    = $row[$i][20];
            $id_periodo  = $row[$i][21];
            $uvms        = $row[$i][22];
            $id_factura  = $row[$i][23];
            $uvs         = $row[$i][24];
            $fecha_factura= $row[$i][25];
            $cantidad_facturada= $row[$i][26];
            $periodoa    = periodoA($id_periodo);
            $periodo_sg  = periodoS($id_periodo);
             #********* Buscar Unidad_v con otros medidores ********#
            $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                    WHERE unidad_vivienda_servicio = $uvs");
            $ids_uv = $ids_uv[0][0];
            if(empty($cantidad_facturada)){
                #*** Buscar Lectura Anterior ***#
                $la = $con->Listar("SELECT valor FROM gp_lectura 
                    WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
                if(empty($la[0][0])){
                    $l_anterior = 0;
                } else {
                    $l_anterior = $la[0][0];
                }
                $consumo = $l_actual - $l_anterior;
            } else {
                $consumo= $cantidad_facturada;
            }
            
            #** Cargo fijo 
            $vcf = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 1");
            $cargo_fijo = $vcf[0][0];
            #** Consumo Total
            $vct = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 2");
            $consumo_valor = $vct[0][0];
            #* v/mt3
            if($consumo_valor>0){
                $valor_m3 = $consumo_valor /$consumo;
            }else {$valor_m3 = 0;}
            $var_consumo = $consumo;
            #** Cargo Básico
            if($consumo<16){
                $cargo_basico = $valor_m3 * $var_consumo; 
                $var_consumo -= $var_consumo; 
            } else {
                $cargo_basico = $valor_m3 * 16;
                $var_consumo -= 16;
            }            
            #** Cargo Comple
            if($var_consumo <= 0){
                $cargo_comple = 0; 
            } elseif($var_consumo<=14) {
                $cargo_comple = $valor_m3 * $var_consumo;
                $var_consumo -= $var_consumo;
            } else {
                $cargo_comple = $valor_m3 * 14;
                $var_consumo-=14;
            }
            #** Cargo Sun
            if($var_consumo<=0){
                $cargo_Sun = 0; 
            } else {
                $cargo_Sun = $valor_m3 * $var_consumo;
                $var_consumo-=$var_consumo;
            }
            #** CMT
            $cmt =0;
            #** Valor MC
            $valor_m3 = $valor_m3;
            #** Valor Consumo
            $consumo_valor = $consumo_valor;
            #** Valor Subsido
            $vcs = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 4");
            $subsidio = $vcs[0][0]*-1;
            #** Valor Contri
            $vcc = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0) 
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 3");
            $contribucion= $vcc[0][0];
            if($cargo_basico+$cargo_fijo+$cargo_comple>0){
                $porc = Round(($subsidio*100)/($cargo_basico+$cargo_fijo+$cargo_comple));
                $pm   = round($porc,0)/100;
            } else {
                $pm   = 0;
            }
            #** factor SuCONCF
            $fact_subsidio = -$pm;
            #** factor SuCONCO
            $fact_contri = 0;
            #** Conexion
            $conexion =0;
            #** Reconexion
            $vrx = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0) 
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 51");            
            $reconexion = $vrx[0][0];
            #** Reinstalacion
            $reinstalacion = 0;
            #** Suspensión
            $suspension = 0;
            #* Ca_ corte
            $corte = 0;
            #** Pag An
            $pago_anticipado = 0;
            #** Dias mora 
            $dias           = 0; 
            $deuda_anterior = 0;
            #Deuda Anterior     
            $da = $con->Listar("SELECT 
                    GROUP_CONCAT(df.id_unico), 
                    df.factura, 
                    f.fecha_factura, 
                    f.periodo 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                AND f.periodo <= $periodoa 
                GROUP BY df.factura  
                ORDER BY f.fecha_factura DESC");
            if(count($da)>0){
                for ($f = 0; $f < count($da); $f++) {
                    #*** Buscar Recaudo ***#
                    $id_df      = $da[$f][0];
                    $id_fc      = $da[$f][1];
                    $fecha_fc   = $da[$f][2];
                    $id_pant    = $da[$f][3];
                    $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.id_unico=$id_fc");
                    $valor_f    = $dav[0][0];
                    $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE p.fecha_pago <'$fecha_factura' AND dp.detalle_factura IN ($id_df)");
                    if(count(($rc))>0 && !empty($rc[0][0])){
                        $recaudo = $rc[0][0];
                    }else {
                        $recaudo = 0;
                    }
                    $deuda_anterior += $valor_f -$recaudo;
                    if(($valor_f -$recaudo)>0){
                        $dias = diasmora($id_pant , $id_periodo);
                    }
                }
            }
            #** Valor Mora
            $deuda_anterior = $deuda_anterior*0.37;       
            
            #** Intereses Mora
            $vcm = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 6");
            $int_mora = $vcm[0][0];
            #** Otros
            $otros = 0;
            #** Refacturacion
            $refac = 0;
            #** N° Refactur
            $nr    = 0;
            #** Total Fac
            $total_f = ($cargo_fijo+$cargo_basico+$cargo_comple+$cargo_Sun)-$subsidio+$contribucion+$int_mora+$reconexion;
            #** Pago Mes  
            $recaudo =0;   
            #** Buscar Fecha De La siguiente factura 
            if(empty($periodo_sg)){
                $fsf = $con->Listar("SELECT p.fecha_cierre
                FROM gp_periodo p
                WHERE p.id_unico =$id_periodo");
            } else {
                $fsf = $con->Listar("SELECT f.fecha_factura 
                FROM gp_factura f 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                    AND f.periodo = $periodo_sg");
            }
            if(empty($fsf[0][0])){
                $fecha_f = $fecha_factura;
            } else {
                $fecha_f = $fsf[0][0];
            }
            $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico) 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_periodo");
            if(count($da)>0){
                $id_df      = $da[0][0];
                #*** Buscar Recaudo ***#
                $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    WHERE c.tipo_concepto = 2 
                        AND dp.detalle_factura IN ($id_df) 
                        AND p.fecha_pago BETWEEN '$fecha_factura' AND '$fecha_f'");
                if(count(($rc))>0 && !empty($rc[0][0])){
                    $recaudo = $rc[0][0];
                }
            }
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                $html .='<td>'.$row[$i][9].'</td>';
                $html .='<td>'.$row[$i][10].'</td>';
                $html .='<td>'.$row[$i][11].'</td>';
                $html .='<td>'.$row[$i][12].'</td>';
                $html .='<td>'.$row[$i][13].'</td>';
                $html .='<td>'.$row[$i][14].'</td>';
                $html .='<td>'.$row[$i][15].'</td>';
                $html .='<td>'.$row[$i][16].'</td>';
                $html .='<td>'.$row[$i][17].'</td>';
                $html .='<td>'.$row[$i][18].'</td>';
                $html .='<td>'.$row[$i][19].'</td>';
                $html .='<td>'.$l_anterior.'</td>';
                $html .='<td>'.$l_actual.'</td>';
                $html .='<td>'.$consumo.'</td>';
                $html .='<td>'.$cargo_fijo.'</td>';
                $html .='<td>'.$cargo_basico.'</td>';
                $html .='<td>'.$cargo_comple.'</td>';
                $html .='<td>'.$cargo_Sun.'</td>';
                $html .='<td>'.$cmt.'</td>';
                $html .='<td>'.$valor_m3.'</td>';
                $html .='<td>'.$consumo_valor.'</td>';
                $html .='<td>'.$subsidio.'</td>';
                $html .='<td>'.$contribucion.'</td>';
                $html .='<td>'.$fact_subsidio.'</td>';
                $html .='<td>'.$fact_contri.'</td>';
                $html .='<td>'.$conexion.'</td>';
                $html .='<td>'.$reconexion.'</td>';
                $html .='<td>'.$reinstalacion.'</td>';
                $html .='<td>'.$suspension.'</td>';
                $html .='<td>'.$corte.'</td>';
                $html .='<td>'.$pago_anticipado.'</td>';
                $html .='<td>'.$dias.'</td>';
                $html .='<td>'.$deuda_anterior.'</td>';
                $html .='<td>'.$int_mora.'</td>';
                $html .='<td>'.$otros.'</td>';
                $html .='<td>'.$refac.'</td>';
                $html .='<td>'.$nr.'</td>';
                $html .='<td>'.$total_f.'</td>';
                $html .='<td>'.$recaudo.'</td>';
                $html .='</tr>';
            } else{
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',$row[$i][9])."$separador";
                $html .=str_replace(',',' ',$row[$i][10])."$separador";
                $html .=str_replace(',',' ',$row[$i][11])."$separador";
                $html .=str_replace(',',' ',$row[$i][12])."$separador";
                $html .=str_replace(',',' ',$row[$i][13])."$separador";
                $html .=str_replace(',',' ',$row[$i][14])."$separador";
                $html .=str_replace(',',' ',$row[$i][15])."$separador";
                $html .=str_replace(',',' ',$row[$i][16])."$separador";
                $html .=str_replace(',',' ',$row[$i][17])."$separador";
                $html .=str_replace(',',' ',$row[$i][18])."$separador";
                $html .=str_replace(',',' ',$row[$i][19])."$separador";
                $html .=str_replace(',',' ',$l_anterior)."$separador";
                $html .=str_replace(',',' ',$l_actual)."$separador";
                $html .=str_replace(',',' ',$consumo)."$separador";
                $html .=str_replace(',',' ',$cargo_fijo)."$separador";
                $html .=str_replace(',',' ',$cargo_basico)."$separador";
                $html .=str_replace(',',' ',$cargo_comple)."$separador";
                $html .=str_replace(',',' ',$cargo_Sun)."$separador";
                $html .=str_replace(',',' ',$cmt)."$separador";
                $html .=str_replace(',',' ',$valor_m3)."$separador";
                $html .=str_replace(',',' ',$consumo_valor)."$separador";
                $html .=str_replace(',',' ',$subsidio)."$separador";
                $html .=str_replace(',',' ',$contribucion)."$separador";
                $html .=str_replace(',',' ',$fact_subsidio)."$separador";
                $html .=str_replace(',',' ',$fact_contri)."$separador";
                $html .=str_replace(',',' ',$conexion)."$separador";
                $html .=str_replace(',',' ',$reconexion)."$separador";
                $html .=str_replace(',',' ',$reinstalacion)."$separador";
                $html .=str_replace(',',' ',$suspension)."$separador";
                $html .=str_replace(',',' ',$corte)."$separador";
                $html .=str_replace(',',' ',$pago_anticipado)."$separador";
                $html .=str_replace(',',' ',$dias)."$separador";
                $html .=str_replace(',',' ',$deuda_anterior)."$separador";
                $html .=str_replace(',',' ',$int_mora)."$separador";
                $html .=str_replace(',',' ',$otros)."$separador";
                $html .=str_replace(',',' ',$refac)."$separador";
                $html .=str_replace(',',' ',$nr)."$separador";
                $html .=str_replace(',',' ',$total_f)."$separador";
                $html .=str_replace(',',' ',$recaudo);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Igac_Acueducto.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Igac_Acueducto.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Igac_Acueducto.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>IGAC ACUEDUCTO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>CODIGO</strong></center></td>
                                <td><center><strong>NUM_CONTRA</strong></center></td>
                                <td><center><strong>DEPTO</strong></center></td>
                                <td><center><strong>MUNICIPIO</strong></center></td>
                                <td><center><strong>ZONA_IGAC</strong></center></td>
                                <td><center><strong>SEC_IGAC</strong></center></td>
                                <td><center><strong>MAZ_IGAC</strong></center></td>
                                <td><center><strong>NUMPRE_IGAC</strong></center></td>
                                <td><center><strong>CONPRE_IGAC</strong></center></td>
                                <td><center><strong>DIRECCION</strong></center></td>
                                <td><center><strong>NUM_FACT</strong></center></td>
                                <td><center><strong>FECHA_FAC</strong></center></td>
                                <td><center><strong>FECHA_INI</strong></center></td>
                                <td><center><strong>DIAS_COB</strong></center></td>
                                <td><center><strong>CLA_USO</strong></center></td>
                                <td><center><strong>UNIDM_RES</strong></center></td>
                                <td><center><strong>UNIDM_NRES</strong></center></td>
                                <td><center><strong>HOGAR_COMOSUS</strong></center></td>
                                <td><center><strong>ESTADO_MEDI</strong></center></td>
                                <td><center><strong>DETER_USO</strong></center></td>
                                <td><center><strong>LEC_ANT</strong></center></td>
                                <td><center><strong>LEC_ACT</strong></center></td>
                                <td><center><strong>CONSUMO</strong></center></td>
                                <td><center><strong>CARGO_FIJO</strong></center></td>
                                <td><center><strong>CAR_BASICO</strong></center></td>
                                <td><center><strong>CAR_COMPLE</strong></center></td>
                                <td><center><strong>CAR_SUN</strong></center></td>
                                <td><center><strong>CMT</strong></center></td>
                                <td><center><strong>VAL_MCUBICO</strong></center></td>
                                <td><center><strong>VAL_CONSUMO</strong></center></td>
                                <td><center><strong>VALOR_SUBSI</strong></center></td>
                                <td><center><strong>VAL_CONTRI</strong></center></td>
                                <td><center><strong>FAC_SUBOCONCF</strong></center></td>
                                <td><center><strong>FAC_SUBOCONCO</strong></center></td>
                                <td><center><strong>CONEXION</strong></center></td>
                                <td><center><strong>RECONEXION</strong></center></td>
                                <td><center><strong>REINSTALACION</strong></center></td>
                                <td><center><strong>SUSPENSION</strong></center></td>
                                <td><center><strong>CORTE</strong></center></td>
                                <td><center><strong>PAG_ANTI</strong></center></td>
                                <td><center><strong>DIAS_MORA</strong></center></td>
                                <td><center><strong>VALOR_MORA</strong></center></td>
                                <td><center><strong>INTERES_MORA</strong></center></td>
                                <td><center><strong>OTROS</strong></center></td>
                                <td><center><strong>CAUSAL_REFA</strong></center></td>
                                <td><center><strong>NUMFAC_REFA</strong></center></td>
                                <td><center><strong>TOT_FAC</strong></center></td>
                                <td><center><strong>PAGO_MES</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    #**** IGAC ALCANTARILLADO *****# 
    case 2:
        
        $row = $con->Listar("SELECT '0000' as CODIGO, 
            '0000' as NUM_CONTRA, 
            d.rss as DEPTO, 
            c.rss as MUNICIPIO, 
            '99' as ZONA_IGAC, 
            '99' as SEC_IGAC, 
            '9999' as MAZ_IGAC, 
            '9999' as NUMPRE_IGAC,
            '0' as CONPRE_IGAC, 
            p.direccion as DIRECCION, 
            f.numero_factura as NUM_FAC, 
            DATE_FORMAT(f.fecha_factura, '%d-%m-%Y') as FECHA_FAC, 
            DATE_FORMAT(pr.fecha_inicial, '%d-%m-%Y') as FECHA_INI, 
            30 as DIAS_COB, 
            es.codigo as CLA_USO, 
            0 as UNIDM_RES, 
            0 as UNIDM_NRES, 
            if(uv.hogar=1,1, 0) as HOGAR_COMOSUS, 
            if(m.estado_medidor = 2, 1,2), 
            1 as DETER_USO, 
            l.valor as VLA, 
            pr.id_unico, 
            uvms.id_unico, 
            f.id_unico, 
            uvs.id_unico, 
            f.fecha_factura, 
            l.cantidad_facturada 
            FROM 
              gp_factura f 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv  ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
            LEFT JOIN gf_ciudad c ON p.ciudad = c.id_unico 
            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
            LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
            LEFT JOIN gp_estrato es ON uv.estrato = es.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = uvms.id_unico AND l.periodo = pr.id_unico 
            WHERE f.parametrizacionanno = $anno AND (f.periodo BETWEEN  $periodoI AND $periodoF)  
            ORDER BY f.id_unico");
        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            $l_actual    = $row[$i][20];
            $id_periodo  = $row[$i][21];
            $uvms        = $row[$i][22];
            $id_factura  = $row[$i][23];
            $uvs         = $row[$i][24];
            $fecha_factura= $row[$i][25];
            $cantidad_facturada= $row[$i][26];
            $periodoa    = periodoA($id_periodo);
            $periodo_sg  = periodoS($id_periodo);
             #********* Buscar Unidad_v con otros medidores ********#
            $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                    WHERE unidad_vivienda_servicio = $uvs");
            $ids_uv = $ids_uv[0][0];
            if(empty($cantidad_facturada)){
                #*** Buscar Lectura Anterior ***#
                $la = $con->Listar("SELECT valor FROM gp_lectura 
                    WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
                if(empty($la[0][0])){
                    $l_anterior = 0;
                } else {
                    $l_anterior = $la[0][0];
                }
                $consumo = $l_actual - $l_anterior;
            } else {
                $consumo =  $cantidad_facturada;
            }
            #** Cargo fijo 
            $vcf = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 12");
            $cargo_fijo = $vcf[0][0];
            #** Consumo Total
            $vct = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 13");
            $consumo_valor = $vct[0][0];
            #* v/mt3
            if($consumo_valor>0){
                $valor_m3 = $consumo_valor /$consumo;
            }else {
                $valor_m3 = 0;
            }
            $var_consumo = $consumo;
            #** Cargo Básico
            if($consumo<16){
                $cargo_basico = $valor_m3 * $var_consumo; 
                $var_consumo -= $var_consumo; 
            } else {
                $cargo_basico = $valor_m3 * 16;
                $var_consumo -= 16;
            }            
            #** Cargo Comple
            if($var_consumo <= 0){
                $cargo_comple = 0; 
            } elseif($var_consumo<=14) {
                $cargo_comple = $valor_m3 * $var_consumo;
                $var_consumo -= $var_consumo;
            } else {
                $cargo_comple = $valor_m3 * 14;
                $var_consumo-=14;
            }
            #** Cargo Sun
            if($var_consumo<=0){
                $cargo_Sun = 0; 
            } else {
                $cargo_Sun = $valor_m3 * $var_consumo;
                $var_consumo-=$var_consumo;
            }
            #** CMT
            $cmt =0;
            #** Valor MC
            $valor_m3 = $valor_m3;
            #** Valor Consumo
            $consumo_valor = $consumo_valor;
            #** Valor Subsido
            $vcs = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 14");
            $subsidio = $vcs[0][0]*-1;
            #** Valor Contri
            $vcc = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0) 
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 23");
            $contribucion= $vcc[0][0];
            if($cargo_basico+$cargo_fijo+$cargo_comple>0){
                $porc = Round(($subsidio*100)/($cargo_basico+$cargo_fijo+$cargo_comple));
                $pm   = round($porc,0)/100;
            } else {
                $pm   = 0;
            }
            
            #** factor SuCONCF
            $fact_subsidio = -$pm;
            #** factor SuCONCO
            $fact_contri = 0;
            #** Conexion
            $conexion =0;
            #** Reconexion
            $vrx = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0) 
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 51");            
            $reconexion = $vrx[0][0];
            #** Reinstalacion
            $reinstalacion = 0;
            #** Suspensión
            $suspension = 0;
            #* Ca_ corte
            $corte = 0;
            #** Pag An
            $pago_anticipado = 0;
            #** Dias mora 
            $dias           = 0; 
            $deuda_anterior = 0;
            #Deuda Anterior     
            $da = $con->Listar("SELECT 
                    GROUP_CONCAT(df.id_unico), 
                    df.factura, 
                    f.fecha_factura, 
                    f.periodo 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                AND f.periodo <= $periodoa 
                GROUP BY df.factura  
                ORDER BY f.fecha_factura DESC");
            if(count($da)>0){
                for ($f = 0; $f < count($da); $f++) {
                    #*** Buscar Recaudo ***#
                    $id_df      = $da[$f][0];
                    $id_fc      = $da[$f][1];
                    $fecha_fc   = $da[$f][2];
                    $id_pant    = $da[$f][3];
                    $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.id_unico=$id_fc");
                    $valor_f    = $dav[0][0];
                    $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE p.fecha_pago <'$fecha_factura' AND dp.detalle_factura IN ($id_df)");
                    if(count(($rc))>0 && !empty($rc[0][0])){
                        $recaudo = $rc[0][0];
                    }else {
                        $recaudo = 0;
                    }
                    $deuda_anterior += $valor_f -$recaudo;
                    if(($valor_f -$recaudo)>0){
                        $dias = diasmora($id_pant , $id_periodo);
                    }
                }
            }
            #** Valor Mora
            $deuda_anterior = $deuda_anterior*0.21;       
            
            #** Intereses Mora
            $vcm = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 16");
            $int_mora = $vcm[0][0];
            #** Otros
            $otros = 0;
            #** Refacturacion
            $refac = 0;
            #** N° Refactur
            $nr    = 0;
            #** Total Fac
            $total_f = ($cargo_fijo+$cargo_basico+$cargo_comple+$cargo_Sun)-$subsidio+$contribucion+$int_mora+$reconexion;
            #** Pago Mes  
            $recaudo =0;   
            #** Buscar Fecha De La siguiente factura 
            if(empty($periodo_sg)){
                $fsf = $con->Listar("SELECT p.fecha_cierre
                FROM gp_periodo p
                WHERE p.id_unico =$id_periodo");
            } else {
                $fsf = $con->Listar("SELECT f.fecha_factura 
                FROM gp_factura f 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                    AND f.periodo = $periodo_sg");
            }
            if(empty($fsf[0][0])){
                $fecha_f = $fecha_factura;
            } else {
                $fecha_f = $fsf[0][0];
            }
            $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico) 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_periodo");
            if(count($da)>0){
                $id_df      = $da[0][0];
                #*** Buscar Recaudo ***#
                $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    WHERE c.tipo_concepto = 4 
                        AND dp.detalle_factura IN ($id_df) 
                        AND p.fecha_pago BETWEEN '$fecha_factura' AND '$fecha_f'");
                if(count(($rc))>0 && !empty($rc[0][0])){
                    $recaudo = $rc[0][0];
                }
            }
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                $html .='<td>'.$row[$i][9].'</td>';
                $html .='<td>'.$row[$i][10].'</td>';
                $html .='<td>'.$row[$i][11].'</td>';
                $html .='<td>'.$row[$i][12].'</td>';
                $html .='<td>'.$row[$i][13].'</td>';
                $html .='<td>'.$row[$i][14].'</td>';
                $html .='<td>'.$row[$i][15].'</td>';
                $html .='<td>'.$row[$i][16].'</td>';
                $html .='<td>'.$row[$i][17].'</td>';
                $html .='<td>0</td>';
                $html .='<td>1</td>';
                $html .='<td>'.$cargo_fijo.'</td>';
                $html .='<td>'.$cargo_basico.'</td>';
                $html .='<td>'.$cargo_comple.'</td>';
                $html .='<td>'.$cargo_Sun.'</td>';
                $html .='<td>'.$cmt.'</td>';
                $html .='<td>'.$valor_m3.'</td>';
                $html .='<td>'.$consumo_valor.'</td>';
                $html .='<td>'.$subsidio.'</td>';
                $html .='<td>'.$contribucion.'</td>';
                $html .='<td>'.$fact_subsidio.'</td>';
                $html .='<td>'.$fact_subsidio.'</td>';
                $html .='<td>'.$conexion.'</td>';
                $html .='<td>'.$pago_anticipado.'</td>';
                $html .='<td>'.$dias.'</td>';
                $html .='<td>'.$deuda_anterior.'</td>';
                $html .='<td>'.$int_mora.'</td>';
                $html .='<td>'.$otros.'</td>';
                $html .='<td>'.$refac.'</td>';
                $html .='<td>'.$nr.'</td>';
                $html .='<td>'.$total_f.'</td>';
                $html .='<td>'.$recaudo.'</td>';
                $html .='</tr>';
            } else{
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',$row[$i][9])."$separador";
                $html .=str_replace(',',' ',$row[$i][10])."$separador";
                $html .=str_replace(',',' ',$row[$i][11])."$separador";
                $html .=str_replace(',',' ',$row[$i][12])."$separador";
                $html .=str_replace(',',' ',$row[$i][13])."$separador";
                $html .=str_replace(',',' ',$row[$i][14])."$separador";
                $html .=str_replace(',',' ',$row[$i][15])."$separador";
                $html .=str_replace(',',' ',$row[$i][16])."$separador";
                $html .=str_replace(',',' ',$row[$i][17])."$separador";
                $html .='0'."$separador";
                $html .='1'."$separador";
                $html .=str_replace(',',' ',$cargo_fijo)."$separador";
                $html .=str_replace(',',' ',$cargo_basico)."$separador";
                $html .=str_replace(',',' ',$cargo_comple)."$separador";
                $html .=str_replace(',',' ',$cargo_Sun)."$separador";
                $html .=str_replace(',',' ',$cmt)."$separador";
                $html .=str_replace(',',' ',$valor_m3)."$separador";
                $html .=str_replace(',',' ',$consumo_valor)."$separador";
                $html .=str_replace(',',' ',$subsidio)."$separador";
                $html .=str_replace(',',' ',$contribucion)."$separador";
                $html .=str_replace(',',' ',$fact_subsidio)."$separador";
                $html .=str_replace(',',' ',$fact_subsidio)."$separador";
                $html .=str_replace(',',' ',$conexion)."$separador";
                $html .=str_replace(',',' ',$pago_anticipado)."$separador";
                $html .=str_replace(',',' ',$dias)."$separador";
                $html .=str_replace(',',' ',$deuda_anterior)."$separador";
                $html .=str_replace(',',' ',$int_mora)."$separador";
                $html .=str_replace(',',' ',$otros)."$separador";
                $html .=str_replace(',',' ',$refac)."$separador";
                $html .=str_replace(',',' ',$nr)."$separador";
                $html .=str_replace(',',' ',$total_f)."$separador";
                $html .=str_replace(',',' ',$recaudo);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Igac_Alcantarillado.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Igac_Alcantarillado.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Igac_Alcantarillado.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>IGAC ALCANTARILLADO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>CODIGO</strong></center></td>
                                <td><center><strong>NUM_CONTRA</strong></center></td>
                                <td><center><strong>DEPTO</strong></center></td>
                                <td><center><strong>MUNICIPIO</strong></center></td>
                                <td><center><strong>ZONA_IGAC</strong></center></td>
                                <td><center><strong>SEC_IGAC</strong></center></td>
                                <td><center><strong>MAZ_IGAC</strong></center></td>
                                <td><center><strong>NUMPRE_IGAC</strong></center></td>
                                <td><center><strong>CONPRE_IGAC</strong></center></td>
                                <td><center><strong>DIRECCION</strong></center></td>
                                <td><center><strong>NUM_FACT</strong></center></td>
                                <td><center><strong>FECHA_FAC</strong></center></td>
                                <td><center><strong>FECHA_INI</strong></center></td>
                                <td><center><strong>DIAS_COB</strong></center></td>
                                <td><center><strong>CLA_USO</strong></center></td>
                                <td><center><strong>UNIDM_RES</strong></center></td>
                                <td><center><strong>UNIDM_NRES</strong></center></td>
                                <td><center><strong>HOGAR_COMOSUS</strong></center></td>
                                <td><center><strong>FAC_AFORO</strong></center></td>
                                <td><center><strong>USU_CARACT</strong></center></td>
                                <td><center><strong>CARGO_FIJO</strong></center></td>
                                <td><center><strong>VERT_BAS</strong></center></td>
                                <td><center><strong>VERT_COM</strong></center></td>
                                <td><center><strong>VERT_SUN</strong></center></td>
                                <td><center><strong>CMT</strong></center></td>
                                <td><center><strong>M3_VERTI</strong></center></td>
                                <td><center><strong>FACT_VERT</strong></center></td>
                                <td><center><strong>VALOR_SUBSI</strong></center></td>
                                <td><center><strong>VAL_CONTRI</strong></center></td>
                                <td><center><strong>FAC_SUBOCONCF</strong></center></td>
                                <td><center><strong>FAC_SUBOCONCO</strong></center></td>
                                <td><center><strong>CONEXION</strong></center></td>
                                <td><center><strong>PAG_ANTI</strong></center></td>
                                <td><center><strong>DIAS_MORA</strong></center></td>
                                <td><center><strong>VALOR_MORA</strong></center></td>
                                <td><center><strong>INTERES_MORA</strong></center></td>
                                <td><center><strong>OTROS</strong></center></td>
                                <td><center><strong>CAUSAL_REFA</strong></center></td>
                                <td><center><strong>NUMFAC_REFA</strong></center></td>
                                <td><center><strong>TOT_FAC</strong></center></td>
                                <td><center><strong>PAGO_MES</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    #**** IGAC ASEO *****# 
    case 3:
        
        $row = $con->Listar("SELECT CONCAT_WS('',s.codigo,uv.codigo_interno) as CODIGO, 
            d.rss as DEPTO, 
            c.rss as MUNICIPIO, 
            '99' as ZONA_IGAC, 
            '99' as SEC_IGAC, 
            '9999' as MAZ_IGAC, 
            '9999' as NUMPRE_IGAC,
            '0' as CONPRE_IGAC, 
            DATE_FORMAT(f.fecha_factura, '%d-%m-%Y') as FECHA_FAC, 
            DATE_FORMAT(pr.fecha_inicial, '%d-%m-%Y') as FECHA_INI, 
            f.numero_factura as NUM_FAC, 
            ub.codigo_ubicacion as UBICACION, 
            ub.codigo_uso as CLA_USO, 
            es.codigo as CCLA_USO, 
            tp.codigo as COD_PRODUC, 
            if(uv.hogar=1,1, 0) as HOGAR_COMOSUS, 
            p.direccion as DIRECCION, 
            l.valor, 
            pr.id_unico, 
            uvms.id_unico, 
            f.id_unico, 
            uvs.id_unico, 
            f.fecha_factura, 
            l.cantidad_facturada 
            FROM 
              gp_factura f 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON f.unidad_vivienda_servicio = uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
            LEFT JOIN gp_unidad_vivienda uv  ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
            LEFT JOIN gf_ciudad c ON p.ciudad = c.id_unico 
            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
            LEFT JOIN gp_periodo pr ON f.periodo = pr.id_unico 
            LEFT JOIN gp_estrato es ON uv.estrato = es.id_unico 
            LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
            LEFT JOIN gp_lectura l ON l.unidad_vivienda_medidor_servicio = uvms.id_unico AND l.periodo = pr.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            LEFT JOIN gp_ubicacion ub ON uv.ubicacion = ub.id_unico 
            LEFT JOIN gp_tipo_productor tp ON uv.tipo_productor = tp.id_unico 
            WHERE f.parametrizacionanno = $anno AND (f.periodo BETWEEN  $periodoI AND $periodoF)  
            ORDER BY f.id_unico limit 10");
        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            $l_actual       = $row[$i][17];
            $id_periodo     = $row[$i][18];
            $uvms           = $row[$i][19];
            $id_factura     = $row[$i][20];
            $uvs            = $row[$i][21];
            $fecha_factura  = $row[$i][22];
            $cantidad_facturada = $row[$i][23];
            $periodoa       = periodoA($id_periodo);
            $periodo_sg     = periodoS($id_periodo);
            #** Refacturacion
            $refac          = 0;
            #** N° Refactura
            $nr             = 0;
            #********* Buscar Unidad_v con otros medidores ********#
            $ids_uv = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_unidad_vivienda_medidor_servicio 
                    WHERE unidad_vivienda_servicio = $uvs");
            $ids_uv = $ids_uv[0][0];
            
            #** Cargo fijo 
            $vcf = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 8");
            $cargo_fijo = $vcf[0][0];
            
            #** Valor Subsido
            $vcs = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 9");
            $subsidio = $vcs[0][0]*-1;
            
            if($cargo_fijo>0){
                $porc = Round(($subsidio*100)/($cargo_fijo));
                $pm   = round($porc,0)/100;
            } else {
                $pm   = 0;
            }
            #** Dias mora 
            $dias           = 0; 
            $deuda_anterior = 0;
            #Deuda Anterior     
            $da = $con->Listar("SELECT 
                    GROUP_CONCAT(df.id_unico), 
                    df.factura, 
                    f.fecha_factura, 
                    f.periodo 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                AND f.periodo <= $periodoa 
                GROUP BY df.factura  
                ORDER BY f.fecha_factura DESC");
            if(count($da)>0){
                for ($f = 0; $f < count($da); $f++) {
                    #*** Buscar Recaudo ***#
                    $id_df      = $da[$f][0];
                    $id_fc      = $da[$f][1];
                    $fecha_fc   = $da[$f][2];
                    $id_pant    = $da[$f][3];
                    $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
                    FROM gp_detalle_factura df 
                    LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                    WHERE f.id_unico=$id_fc");
                    $valor_f    = $dav[0][0];
                    $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                        LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                        WHERE p.fecha_pago <'$fecha_factura' AND dp.detalle_factura IN ($id_df)");
                    if(count(($rc))>0 && !empty($rc[0][0])){
                        $recaudo = $rc[0][0];
                    }else {
                        $recaudo = 0;
                    }
                    $deuda_anterior += $valor_f -$recaudo;
                    if(($valor_f -$recaudo)>0){
                        $dias = diasmora($id_pant , $id_periodo);
                    }
                }
            }
            #** Valor Mora
            $deuda_anterior = $deuda_anterior*0.42;       
            
            #** Intereses Mora
            $vcm = $con->Listar("SELECT IF(SUM(valor_total_ajustado)!='', SUM(valor_total_ajustado),0)
                FROM gp_detalle_factura df 
                WHERE factura = $id_factura AND concepto_tarifa = 11");
            $int_mora = $vcm[0][0];
            #** Total Fac
            $total_f = ($cargo_fijo-$subsidio+$int_mora);
            #** Pago Mes  
            $recaudo =0;   
            #** Buscar Fecha De La siguiente factura 
            if(empty($periodo_sg)){
                $fsf = $con->Listar("SELECT p.fecha_cierre
                FROM gp_periodo p
                WHERE p.id_unico =$id_periodo");
            } else {
                $fsf = $con->Listar("SELECT f.fecha_factura 
                FROM gp_factura f 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) 
                    AND f.periodo = $periodo_sg");
            }
            if(empty($fsf[0][0])){
                $fecha_f = $fecha_factura;
            } else {
                $fecha_f = $fsf[0][0];
            }
            $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico) 
                FROM gp_detalle_factura df 
                LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $id_periodo");
            if(count($da)>0){
                $id_df      = $da[0][0];
                #*** Buscar Recaudo ***#
                $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                    LEFT JOIN gp_pago p ON dp.pago = p.id_unico
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                    WHERE c.tipo_concepto = 3  
                        AND dp.detalle_factura IN ($id_df) 
                        AND p.fecha_pago BETWEEN '$fecha_factura' AND '$fecha_f'");
                if(count(($rc))>0 && !empty($rc[0][0])){
                    $recaudo = $rc[0][0];
                }
            }
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                $html .='<td>26951</td>';
                $html .='<td>'.$row[$i][9].'</td>';
                $html .='<td>'.$row[$i][10].'</td>';
                $html .='<td></td>';
                $html .='<td>'.$refac.'</td>';
                $html .='<td>'.$nr.'</td>';
                $html .='<td>'.$row[$i][11].'</td>';
                $html .='<td>'.$row[$i][12].'</td>';                
                $html .='<td>'.$row[$i][13].'</td>';
                $html .='<td>'.$row[$i][14].'</td>';
                $html .='<td>'.$row[$i][15].'</td>';
                $html .='<td>30</td>';
                $html .='<td>1</td>';
                $html .='<td>0</td>';
                $html .='<td>'.$subsidio.'</td>';
                $html .='<td>'.$refac.'</td>';
                $html .='<td>'.$deuda_anterior.'</td>';
                $html .='<td>'.$int_mora.'</td>';
                $html .='<td>0</td>';
                $html .='<td>'.$total_f.'</td>';
                $html .='<td>'.$recaudo.'</td>';
                $html .='<td>3</td>';
                $html .='<td>7</td>';
                $html .='<td>'.$cargo_fijo.'</td>';
                $html .='<td>'.$row[$i][16].'</td>';
                $html .='<td>5</td>';                
                $html .='<td>'.$pm.'</td>';
                $html .='<td>0</td>';
                $html .='<td>0</td>';                
                $html .='</tr>';
            } else{
                
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',26951)."$separador";
                $html .=str_replace(',',' ',$row[$i][9])."$separador";
                $html .=str_replace(',',' ',$row[$i][10])."$separador";
                $html .=' '."$separador";
                $html .=str_replace(',',' ',$refac)."$separador";
                $html .=str_replace(',',' ',$nr)."$separador";
                $html .=str_replace(',',' ',$row[$i][11])."$separador";
                $html .=str_replace(',',' ',$row[$i][12])."$separador";                
                $html .=str_replace(',',' ',$row[$i][13])."$separador";
                $html .=str_replace(',',' ',$row[$i][14])."$separador";
                $html .=str_replace(',',' ',$row[$i][15])."$separador";
                $html .=str_replace(',',' ',30)."$separador";
                $html .=str_replace(',',' ',1)."$separador";
                $html .=str_replace(',',' ',0)."$separador";
                $html .=str_replace(',',' ',$subsidio)."$separador";
                $html .=str_replace(',',' ',$refac)."$separador";
                $html .=str_replace(',',' ',$deuda_anterior)."$separador";
                $html .=str_replace(',',' ',$int_mora)."$separador";
                $html .=str_replace(',',' ',0)."$separador";
                $html .=str_replace(',',' ',$total_f)."$separador";
                $html .=str_replace(',',' ',$recaudo)."$separador";
                $html .=str_replace(',',' ',3)."$separador";
                $html .=str_replace(',',' ',7)."$separador";
                $html .=str_replace(',',' ',$cargo_fijo)."$separador";
                $html .=str_replace(',',' ',$row[$i][16])."$separador";
                $html .=str_replace(',',' ',5)."$separador";                
                $html .=str_replace(',',' ',$pm)."$separador";
                $html .=str_replace(',',' ',0)."$separador";
                $html .=str_replace(',',' ',0);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Igac_Aseo.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Igac_Aseo.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Igac_Aseo.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>IGAC ASEO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>CODIGO</strong></center></td>
                                <td><center><strong>NUM_CONTRA</strong></center></td>
                                <td><center><strong>DEPTO</strong></center></td>
                                <td><center><strong>MUNICIPIO</strong></center></td>
                                <td><center><strong>ZONA_IGAC</strong></center></td>
                                <td><center><strong>SEC_IGAC</strong></center></td>
                                <td><center><strong>MAZ_IGAC</strong></center></td>
                                <td><center><strong>NUMPRE_IGAC</strong></center></td>
                                <td><center><strong>CONPRE_IGAC</strong></center></td>
                                <td><center><strong>FECHA_FAC</strong></center></td>
                                <td><center><strong>NUAP</strong></center></td>
                                <td><center><strong>FECHA_INI</strong></center></td>
                                <td><center><strong>NUM_FACT</strong></center></td>       
                                <td><center><strong>MULTI</strong></center></td>                                
                                <td><center><strong>CAUSAL_REFA</strong></center></td>
                                <td><center><strong>FAC_REFA</strong></center></td>
                                <td><center><strong>UBICACION</strong></center></td>
                                <td><center><strong>CLASEUSO</strong></center></td>
                                <td><center><strong>CLASEUSO</strong></center></td>
                                <td><center><strong>COD_PRO</strong></center></td>
                                <td><center><strong>H_COMUN</strong></center></td>
                                <td><center><strong>DIAS_COBRADOS</strong></center></td>
                                <td><center><strong>AFORO</strong></center></td>
                                <td><center><strong>PRODUCCION</strong></center></td>
                                <td><center><strong>SUBSIDIO</strong></center></td>
                                <td><center><strong>NUMFAC_REFA</strong></center></td>
                                <td><center><strong>DEUDA</strong></center></td>
                                <td><center><strong>INTERESES</strong></center></td>
                                <td><center><strong>SALDO_USUARIO</strong></center></td>
                                <td><center><strong>TOTAL_FAC</strong></center></td>
                                <td><center><strong>RECAUDO</strong></center></td>
                                <td><center><strong>FRECUENRT</strong></center></td>
                                <td><center><strong>FRECUENBL</strong></center></td>
                                <td><center><strong>TARIFA</strong></center></td>
                                <td><center><strong>DIRECCION</strong></center></td>
                                <td><center><strong>CONJUNTA</strong></center></td>
                                <td><center><strong>FACTORS</strong></center></td>
                                <td><center><strong>DESCUENTO</strong></center></td>
                                <td><center><strong>KILOADI</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;

    #**** TARIFAS ACUEDUCTO *****# 
    case 4:
        
        $row = $con->Listar("");
        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                          
                $html .='</tr>';
            } else{
                
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',0);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>TARIFAS ACUEDUCTO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>DANE</strong></center></td>
                                <td><center><strong>UBICACION</strong></center></td>
                                <td><center><strong>MES</strong></center></td>
                                <td><center><strong>CLASE_USO</strong></center></td>
                                <td><center><strong>FECHA_APLICACION</strong></center></td>
                                <td><center><strong>INDICE_ACTUALIZACION</strong></center></td>
                                <td><center><strong>CARGO_FIJO</strong></center></td>
                                <td><center><strong>BASICO</strong></center></td>
                                <td><center><strong>COMPLEMENTARIO</strong></center></td>
                                <td><center><strong>SUNTUARIO</strong></center></td>
                                <td><center><strong>CMT</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    
    #**** TARIFAS ALCANTARILLADO *****# 
    case 5:        
        $row = $con->Listar("");        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {            
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                          
                $html .='</tr>';
            } else{
                
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',0);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>TARIFAS ACUEDUCTO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>DANE</strong></center></td>
                                <td><center><strong>UBICACION</strong></center></td>
                                <td><center><strong>MES</strong></center></td>
                                <td><center><strong>CLASE_USO</strong></center></td>
                                <td><center><strong>FECHA_APLICACION</strong></center></td>
                                <td><center><strong>INDICE_ACTUALIZACION</strong></center></td>
                                <td><center><strong>CARGO_FIJO</strong></center></td>
                                <td><center><strong>BASICO</strong></center></td>
                                <td><center><strong>COMPLEMENTARIO</strong></center></td>
                                <td><center><strong>SUNTUARIO</strong></center></td>
                                <td><center><strong>CMT</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    #**** TARIFAS ASEO *****# 
    case 6:        
        $row = $con->Listar("");        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {            
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                          
                $html .='</tr>';
            } else{
                
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',0);
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=Tarifas_Acueducto.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>TARIFAS ACUEDUCTO</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>DANE</strong></center></td>
                                <td><center><strong>UBICACION</strong></center></td>
                                <td><center><strong>MES</strong></center></td>
                                <td><center><strong>CLASE_USO</strong></center></td>
                                <td><center><strong>FECHA_APLICACION</strong></center></td>
                                <td><center><strong>INDICE_ACTUALIZACION</strong></center></td>
                                <td><center><strong>CARGO_FIJO</strong></center></td>
                                <td><center><strong>BASICO</strong></center></td>
                                <td><center><strong>COMPLEMENTARIO</strong></center></td>
                                <td><center><strong>SUNTUARIO</strong></center></td>
                                <td><center><strong>CMT</strong></center></td>
                            </tr>
                            <?php echo $html; ?>
                        </table>
                    </body>
                </html>
                <?php             
            break;
        }
    break;
    #**** PQR *****# 
    case 7:
        
        $row = $con->Listar("SELECT d.rss AS DANE_DPTO, 
                c.rss as DANE_MUN, 
                00 as DANE_ZONA, 
                pqr.id_unico as RADICADO_RECIBIDO,
                DATE_FORMAT(pqr.fecha_hora, '%d-%m-%Y')as FECHA_RADICADO, 
                tt.codigo as TIPO_TRAMITE, 
                ca.codigo as CAUSAL, 
                dc.codigo as DETALLE_CAUSAL, 
                CONCAT_WS('',s.codigo,uv.codigo_interno) as CODIGO, 
                f.numero_factura as FACTURA, 
                pqr.id_unico 
            FROM gpqr_pqr pqr 
            LEFT JOIN gf_tercero t ON  pqr.compania = t.id_unico 
            LEFT JOIN gf_ciudad c ON t.ciudadidentificacion = c.id_unico 
            LEFT JOIN gf_departamento d ON c.departamento = d.id_unico
            LEFT JOIN gpqr_tipo_tramite tt ON pqr.tipo_tramite = tt.id_unico 
            LEFT JOIN gpqr_detalle_causal dc ON pqr.detalle_causal = dc.id_unico 
            LEFT JOIN gpqr_causal ca ON dc.causal = ca.id_unico 
            LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON pqr.id_unidad_vivienda= uvms.id_unico 
            LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.id_unico = uvms.unidad_vivienda_servicio 
            LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
            LEFT JOIN gp_factura f ON pqr.id_factura = f.id_unico 
            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
            WHERE YEAR(pqr.fecha_hora) ='$num_anno'");
        
        $html ="";
        for ($i = 0; $i < count($row); $i++) {
            $id_pqr = $row[$i][10];
            $dr = $con->Listar("SELECT des.codigo , 
                DATE_FORMAT(dc.fecha, '%d-%m-%Y') 
                FROM gpqr_detalle_pqr dc 
                LEFT JOIN gpqr_clase c ON dc.id_clase = c.id_unico 
                LEFT JOIN gpqr_descripcion des ON dc.id_descripcion = des.id_unico 
                LEFT JOIN gpqr_clase_descripcion cl ON des.id_clase_descripcion = cl.id_unico 
                WHERE c.indicador_cierre = 1 AND cl.id_unico = 2 AND dc.pqr = $id_pqr");
            $tipo_res = $dr[0][0];
            $fecha    = $dr[0][1];
            if($exportar==3){
                $html .='<tr>';
                $html .='<td>'.$row[$i][0].'</td>';
                $html .='<td>'.$row[$i][1].'</td>';
                $html .='<td>'.$row[$i][2].'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$row[$i][4].'</td>';
                $html .='<td>'.$row[$i][5].'</td>';
                $html .='<td>'.$row[$i][6].'</td>';
                $html .='<td>'.$row[$i][7].'</td>';
                $html .='<td>'.$row[$i][8].'</td>';
                $html .='<td>'.$row[$i][9].'</td>';
                $html .='<td>'.$tipo_res.'</td>';
                $html .='<td>'.$fecha.'</td>';
                $html .='<td>'.$row[$i][3].'</td>';
                $html .='<td>'.$fecha.'</td>';
                $html .='<td>1</td>';
                $html .='<td> </td>';
                $html .='</tr>';
            } else{
                $html .=str_replace(',',' ',$row[$i][0])."$separador";
                $html .=str_replace(',',' ',$row[$i][1])."$separador";
                $html .=str_replace(',',' ',$row[$i][2])."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$row[$i][4])."$separador";
                $html .=str_replace(',',' ',$row[$i][5])."$separador";
                $html .=str_replace(',',' ',$row[$i][6])."$separador";
                $html .=str_replace(',',' ',$row[$i][7])."$separador";
                $html .=str_replace(',',' ',$row[$i][8])."$separador";
                $html .=str_replace(',',' ',$row[$i][9])."$separador";
                $html .=str_replace(',',' ',$tipo_res)."$separador";
                $html .=str_replace(',',' ',$fecha)."$separador";
                $html .=str_replace(',',' ',$row[$i][3])."$separador";
                $html .=str_replace(',',' ',$fecha)."$separador";
                $html .='1'."$separador";
                $html .=' ';
                $html .= "\n";
            }
                
        }
        switch ($exportar){
            #*** csv ***#
            case 1:
                header("Content-Disposition: attachment; filename=PQR.csv");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** txt ***#   
            case 2:
                header("Content-Disposition: attachment; filename=PQR.txt");
                ini_set('max_execution_time', 0);
                echo $html;
            break;
            #*** xls ***#
            case 3:
                header("Content-Disposition: attachment; filename=PQR.xls");
                ini_set('max_execution_time', 0);
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>PQR</title>
                    </head>
                    <body>
                        <table width="100%" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><center><strong>DANE_DPTO</strong></center></td>
                                <td><center><strong>DANE_MUN</strong></center></td>
                                <td><center><strong>DANE_ZONA</strong></center></td>
                                <td><center><strong>RADICADO_RECIBIDO</strong></center></td>
                                <td><center><strong>FECHA_RADICADO</strong></center></td>
                                <td><center><strong>TIPO_TRAMITE</strong></center></td>
                                <td><center><strong>CAUSAL</strong></center></td>
                                <td><center><strong>DETALLE_CAUSAL</strong></center></td>
                                <td><center><strong>NUMERO_CUENTA</strong></center></td>
                                <td><center><strong>IDENTIFICADOR_O_NUMERO_FAC</strong></center></td>
                                <td><center><strong>TIPO_RESPUESTA</strong></center></td>
                                <td><center><strong>FECHA_RESPUESTA</strong></center></td>
                                <td><center><strong>RADICADO_RESPUESTA</strong></center></td>
                                <td><center><strong>FECHA_NOTIFICACION</strong></center></td>
                                <td><center><strong>TIPO_NOTIFICACION</strong></center></td>
                                <td><center><strong>FECHA_TRASLADO</strong></center></td>
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

