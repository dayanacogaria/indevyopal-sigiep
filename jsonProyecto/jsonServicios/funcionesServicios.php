<?php

function promedioLectura($id_pa, $consumo,$uvms,$la){
    global $con;
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
    if(empty($vfac[0][0])) { $valorf1 =0;} else {$valorf1 = $vfac[0][0];}

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
    if(empty($vfac[0][0])) { $valorf2 =0;} else {$valorf2 = $vfac[0][0];}
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
    if(empty($vfac[0][0])) { $valorf3 =0;} else {$valorf3 = $vfac[0][0];}
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
    if(empty($vfac[0][0])) { $valorf4 =0;} else {$valorf4 = $vfac[0][0];}
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
    if(empty($vfac[0][0])) { $valorf5 =0;} else {$valorf5 = $vfac[0][0];}
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
    if(empty($vfac[0][0])) { $valorf6 =0;} else {$valorf6 = $vfac[0][0];}

    #** Promedio **#
    $promedio = (($valor1+$valor2+$valor3+$valor4+$valor5+$valor6+$consumo)/7);
    $promedio = round($promedio,0);
    return $promedio;
}