<?php

function promedioLectura($id_pa, $consumo,$uvms,$la){
    global $con;
    $cd=7;
    #*********************** Consumos Anteriores ***************************#
    #1
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan1 = $laa[0][0];
    $valor1 = $la -$laan1;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){
        if(empty($vfac[0][0])) { $valorf1 =0; } else {$valorf1 = $vfac[0][0];}
    } else {
        $valorf1 = 0;
        $cd      -= 1;
    }

    #2
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan2 = $laa[0][0];
    $valor2 = $laan1-$laan2;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){
        if(empty($vfac[0][0])) { $valorf2 =0;} else {$valorf2 = $vfac[0][0];}
    }else {
        $valorf2 = 0;
        $cd      -= 1;
    }
    #3
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan3 = $laa[0][0];
    $valor3 = $laan2 - $laan3;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){ 
        if(empty($vfac[0][0])) { $valorf3 =0;} else {$valorf3 = $vfac[0][0];}
    } else {
        $valorf3 = 0;
        $cd      -= 1;
    }
    #4
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan4 = $laa[0][0];
    $valor4 = $laan3 - $laan4;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){ 
        if(empty($vfac[0][0])) { $valorf4 =0;} else {$valorf4 = $vfac[0][0];}
    } else {
        $valorf4 = 0;
        $cd      -= 1;
    }    
        
    #5
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan5 = $laa[0][0];
    $valor5 = $laan4 - $laan4;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){ 
        if(empty($vfac[0][0])) { $valorf5 =0;} else {$valorf5 = $vfac[0][0];}
    } else {
        $valorf5 = 0;
        $cd      -= 1;
    }  
    #6
    $rowp = $con->Listar("SELECT DISTINCT pa.* 
            FROM gp_periodo p 
            LEFT JOIN gp_periodo pa ON pa.fecha_inicial < p.fecha_inicial 
            WHERE p.id_unico = ".$id_pa."  
            ORDER BY pa.fecha_inicial DESC ");
    $id_pa     = $rowp[0]['id_unico'];
    $laa= $con->Listar("SELECT valor FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $id_pa");
    $laan6 = $laa[0][0];
    $valor6 = $laan5 - $laan6;
    #Valor
    $vfac = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $id_pa");
    if(count($vfac)>0){ 
        if(empty($vfac[0][0])) { $valorf6 =0;} else {$valorf6 = $vfac[0][0];}
    }else {
        $valorf6 = 0;
        $cd      -= 1;
    }  

    #** Promedio **#
    $promedio = (($valor1+$valor2+$valor3+$valor4+$valor5+$valor6+$consumo)/$cd);
    $promedio = round($promedio,0);
    return $promedio;
}
function despejarformula($formula, $cantidad, $id_factura, $deuda_anterior, $estrato, $periodo, $uso, $concepto_id){
    @session_start();
    global $con;
    $panno = $_SESSION['anno'];
    #*** Buscar Si Formula tiene Conceptos ****#
    $formula = str_replace('&saldo anterior&', $deuda_anterior, $formula) ;
    if(strpos($formula, "&")){
        $regex = preg_match_all('/&([^&]*)&/',$formula,$exit);#Regex para obtener de los paramtros entre &&
	for ($i = 0; $i < $regex; $i++){
            $concepto = $exit[1][$i];
            #Buscar Si el concepto tiene factor base **#
            $fb = $con->Listar("SELECT cf.id_unico, f.valor FROM gp_concepto_factor cf 
                LEFT JOIN gp_factor_base f ON cf.factor = f.id_unico 
                WHERE f.parametrizacionanno = $panno AND cf.concepto = $concepto_id");
            
            $valor_factor = "";
            $factor       = 0;
            if(count($fb)>0){
                $factor         = 1;
                $valor_factor   = $fb[0][1];
            }
            if($factor ==1 && $cantidad > $valor_factor){
                #** Buscar id concepto ***#
                $id_c = $con->Listar("SELECT id_unico FROM gp_concepto WHERE nombre like '$concepto'");
                $ids = $id_c[0][0];
                $valor = valorconceptobase($ids,$valor_factor,$uso, $estrato,$periodo, $panno,$id_factura, $deuda_anterior);
            } else {  
                #** Buscar id concepto ***#
                $id_c = $con->Listar("SELECT GROUP_CONCAT(id_unico) FROM gp_concepto WHERE nombre like '$concepto'");
                $ids = $id_c[0][0];
                #*** Buscar Valor Concepto en factura ****#
                $vl = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura
                    WHERE factura =$id_factura AND concepto_tarifa IN ($ids)");

                if(empty($vl[0][0])){
                    $valor =buscarvalort($ids,$uso,$estrato,$periodo, $panno);
                } else {
                    $valor = $vl[0][0];        
                }       
            }
            $formula = str_replace('&'.$concepto.'&', $valor, $formula) ;
        }        
    }

    $formula = str_replace('?lectura?', $cantidad, $formula) ;
    
    return $formula;
}
function buscarformula0($id_concepto, $uso, $estrato){
    global $con;
    $bt =0;
    $df = 0;
    $formula_e = 0;
    #** Buscar Fórmula por estrato uy uso **#
    $frm = $con->Listar("SELECT * FROM gn_formula_concepto 
        WHERE concepto = $id_concepto 
        AND uso = $uso
        AND lectura_0 =1");
    if(count($frm)>0){
        if(!empty($frm[0]['ecuacion'])){
            $df = 1;
            $formula_e = $frm[0]['ecuacion'];
        } else {
            $bt =1;                         
        }
    } else { 
        #** Buscar Fórmula por estrato **#
        $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto AND estrato = $estrato AND lectura_0 =1");
        if(count($frm)>0){
            if(!empty($frm[0]['ecuacion'])){
                $df = 1;
                $formula_e = $frm[0]['ecuacion'];
            } else {
                $bt =1;                         
            }
        } else {
            $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto AND lectura_0 =1 AND estrato IS NULL AND uso IS NULL");
            if(count($frm)>0){
                if(!empty($frm[0]['ecuacion'])){
                    $df = 1;
                    $formula_e = $frm[0]['ecuacion'];
                } else {
                   $bt=1;                         
                }
            } else {
                $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto AND estrato = $estrato 
                    AND (lectura_0 != 1 OR lectura_0 IS NULL)");
                if(count($frm)>0){
                    if(!empty($frm[0]['ecuacion'])){
                            $df = 1;
                            $formula_e = $frm[0]['ecuacion'];
                    } else {
                        $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto");
                        if(count($frm)>0){
                            if(!empty($frm[0]['ecuacion'])){
                                $df = 1;
                                $formula_e = $frm[0]['ecuacion'];
                            } else {
                                $bt=1;                           
                            }
                        } else {
                            $bt=1;
                        } 
                    }
                } else {
                        $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto");
                        if(count($frm)>0){
                            if(!empty($frm[0]['ecuacion'])){
                                $df = 1;
                                $formula_e = $frm[0]['ecuacion'];
                            } else {
                                $bt=1;                           
                            }
                        } else {
                            $bt=1;
                        } 
                }
            } 
        } 
    }
    return array($bt, $df, $formula_e);
}

function buscarformula($id_concepto, $uso, $estrato){
    global $con;
    $bt =0;
    $df = 0;
    $formula_e = 0;
    #** Buscar Fórmula por  uso **#
    $frm = $con->Listar("SELECT * FROM gn_formula_concepto 
        WHERE concepto = $id_concepto 
        AND uso = $uso
        AND (lectura_0 != 1 OR lectura_0 IS NULL)");
    if(count($frm)>0){
        if(!empty($frm[0]['ecuacion'])){
            $df = 1;
            $formula_e = $frm[0]['ecuacion'];
        } else {
            $bt =1;                         
        }
    }  else { 
        #** Buscar Fórmula por estrato **#
        $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto "
                . "AND estrato = $estrato AND (lectura_0 != 1 OR lectura_0 IS NULL) AND uso IS NULL");
        if(count($frm)>0){
            if(!empty($frm[0]['ecuacion'])){
                $df = 1;
                $formula_e = $frm[0]['ecuacion'];
            } else {
                $bt=1;                          
            }
        } else {
            $frm = $con->Listar("SELECT * FROM gn_formula_concepto WHERE concepto = $id_concepto AND uso IS NULL");
            if(count($frm)>0){
                if(!empty($frm[0]['ecuacion'])){
                    $df = 1;
                    $formula_e = $frm[0]['ecuacion'];
                } else {
                    $bt=1;                            
                }
            } else {
                $bt=1;
            } 
        }
    }
    return array($bt, $df, $formula_e);
}

function buscarvalort($ids,$uso,$estrato,$periodo, $panno){
    global $con;
    $valor = 0;
    $tr = $con->Listar("SELECT t.valor 
        FROM gp_concepto_tarifa ct 
        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
        WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
        AND t.estrato = '$estrato' AND t.periodo= '$periodo' 
        AND ct.parametrizacionanno = $panno");

    if(count($tr)>0){
        $valor = $tr[0][0];
    } else {
        $tr = $con->Listar("SELECT t.valor 
        FROM gp_concepto_tarifa ct 
        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
        WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
        AND t.periodo= '$periodo' 
        AND ct.parametrizacionanno = $panno");        
        if(count($tr)>0){
            $valor = $tr[0][0];
        } else {
           $tr = $con->Listar("SELECT t.valor 
            FROM gp_concepto_tarifa ct 
            LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
            WHERE ct.concepto IN ($ids) AND t.uso = '$uso'
            AND t.estrato = '$estrato' 
            AND ct.parametrizacionanno = $panno "); 
            if(count($tr)>0){
                $valor = $tr[0][0];
            } else {
                $tr = $con->Listar("SELECT t.valor 
                FROM gp_concepto_tarifa ct 
                LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                WHERE ct.concepto IN ($ids) AND t.uso = '$uso' 
                AND ct.parametrizacionanno = $panno");
                if(count($tr)>0){
                    $valor = $tr[0][0];
                } else {
                    $tr = $con->Listar("SELECT t.valor 
                    FROM gp_concepto_tarifa ct 
                    LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                    WHERE ct.concepto IN ($ids) 
                    AND t.estrato = '$estrato' AND t.periodo= '$periodo' 
                    AND ct.parametrizacionanno = $panno");
                    if(count($tr)>0){
                        $valor = $tr[0][0];
                    } else {
                        $tr = $con->Listar("SELECT t.valor 
                        FROM gp_concepto_tarifa ct 
                        LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                        WHERE ct.concepto IN ($ids) AND t.periodo= '$periodo' 
                            AND ct.parametrizacionanno = $panno");
                        if(count($tr)>0){
                            $valor = $tr[0][0];
                        } else {
                            $tr = $con->Listar("SELECT t.valor 
                            FROM gp_concepto_tarifa ct 
                            LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                            WHERE ct.concepto IN ($ids) AND t.estrato = '$estrato' 
                                AND ct.parametrizacionanno = $panno");
                            if(count($tr)>0){
                                $valor = $tr[0][0];
                            } else {
                                $tr = $con->Listar("SELECT t.valor 
                                FROM gp_concepto_tarifa ct 
                                LEFT JOIN gp_tarifa t ON ct.tarifa =t.id_unico
                                WHERE ct.concepto IN ($ids) 
                                AND ct.parametrizacionanno = $panno AND t.uso IS NULL 
                                AND t.estrato IS NULL AND t.periodo IS NULL"); 
                                if(count($tr)>0){
                                    $valor = $tr[0][0];
                                } else {
                                    $valor =0;
                                }
                            }
                        }
                    }
                }
            }
        }                            
    }
    
    return $valor;
}

function valorconceptobase($ids,$valor_factor,$uso, $estrato,$periodo, $panno,$id_factura, $deuda_anterior){
    global $Cal;
    $bfm     = buscarformula($ids, $uso, $estrato);
    $bt      = $bfm[0];
    $df      = $bfm[1];
    $formula_e = $bfm[2];
    if($df == 1){
        $formula = despejarformula($formula_e, $valor_factor, $id_factura, $deuda_anterior, $estrato, $periodo, $uso, $ids);
        $valor = $Cal->calculate($formula); // 12
        $valor = round($valor,0);
    } elseif($bt==1){
        $valor = buscarvalort($ids,$uso,$estrato,$periodo, $panno);
    } else {
        $valor = 0;
    }
    
    return $valor;
}

function diasmora($periodo_anterior, $periodo_actual, $fecha ){
    global $con;
    $tr = $con->Listar("SELECT DATEDIFF('$fecha', p1.fecha_cierre)
        FROM gp_periodo p1
        WHERE p1.id_unico = $periodo_anterior");
    return $tr[0][0];
}

# ******* Función para buscar periodo siguiente ******* #
function periodoS ($periodo){
    global $con;
    $row = $con->Listar("SELECT DISTINCT pa.* FROM gp_periodo p 
        LEFT JOIN gp_periodo pa ON pa.fecha_inicial > p.fecha_inicial 
        WHERE p.id_unico = $periodo 
        ORDER BY pa.fecha_inicial ASC ");
    return $row[0][0];
}

function deudaActual($id_uvms){
    global $con; 
    $deuda_anterior = 0;
    // Buscar Unidades de esa vivienda 
    $uds = $con->Listar("SELECT GROUP_CONCAT(uvms.id_unico) 
        FROM gp_unidad_vivienda_medidor_servicio uvms 
        WHERE uvms.unidad_vivienda_servicio 
        IN ( SELECT uvmss.unidad_vivienda_servicio FROM gp_unidad_vivienda_medidor_servicio uvmss 
        WHERE uvmss.id_unico = $id_uvms)");
    #** Buscar Facturas con saldo de la unidad de vivienda  y realizarles pago**#
    $da = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), 
            SUM(df.valor_total_ajustado), f.id_unico 
        FROM gp_detalle_factura df 
        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
        WHERE f.unidad_vivienda_servicio IN(".$uds[0][0].")  
        GROUP BY f.id_unico ORDER BY f.fecha_factura ASC");
    if(count($da)>0){
        for ($d = 0; $d < count($da); $d++) {
            #*** Buscar Recaudo ***#
            $id_df      = $da[$d][0];
            $valor_f    = $da[$d][1];
            $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                WHERE dp.detalle_factura IN ($id_df)");
            if(count(($rc))<=0){
                $recaudo = 0;
            }elseif(empty ($rc[0][0])){
                $recaudo = 0;
            } else {
                $recaudo = $rc[0][0];
            }
            $deuda_anterior += $valor_f -$recaudo;
        }
    } 
    return $deuda_anterior;
}