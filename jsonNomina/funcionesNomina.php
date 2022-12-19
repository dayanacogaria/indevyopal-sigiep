<?php
#Consultar Concepto Actual
function guardarActual($empleado,$concepto, $periodo ){
    global $con;
    global $diasTP;
    global $panno;
    switch ($concepto) {
        #Asignación Básica
        case '001':
            $salario = sueldo($empleado);
            $id_conceptos = id_concepto($concepto);
            $gn = guardarNovedad($salario, $empleado, $periodo, $id_conceptos);
        break;
        #* Sueldo 
        case '002':
            $salario = sueldo($empleado);
            if($diasTP<30){
                $salario = ROUND(($salario * $diasTP) /30);
            } 
            $id_conceptos = id_concepto($concepto);
            $gn = guardarNovedad($salario, $empleado, $periodo, $id_conceptos);
        break;
        case '009':
            $id_concepto = id_concepto($concepto);
            guardarNovedad($diasTP , $empleado, $periodo, $id_concepto);
        break;
        #Prima Alimentación
        case '079':
            $salario = sueldo($empleado);
            $aux_al  = 0;
            $rowax = $con->Listar("SELECT  pl.primaA, pl.talimentacion FROM gn_empleado e 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico
                LEFT JOIN gn_parametros_liquidacion pl ON et.tipo = pl.tipo_empleado and pl.vigencia = $panno  
                WHERE e.id_unico = $empleado");
            if($rowax[0][0] > 0){
                if($salario < $rowax[0][1] ){
                    $aux_al = $rowax[0][0] ;
                    if($diasTP<30){
                        $aux_al = ROUND(($aux_al * $diasTP) /30);
                    }
                    $id_conceptoal = id_concepto($concepto);
                    guardarNovedad($aux_al , $empleado, $periodo, $id_conceptoal);
                }
            } 
        break;
        # Factor Alimentación
        case '954':
        case '1005';
            $salario = sueldo($empleado);
            $aux_al  = 0;
            $rowax = $con->Listar("SELECT  pl.primaA, pl.talimentacion FROM gn_empleado e 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico
                LEFT JOIN gn_parametros_liquidacion pl ON et.tipo = pl.tipo_empleado and pl.vigencia = $panno  
                WHERE e.id_unico = $empleado");
            if($rowax[0][0] > 0){
                if($salario < $rowax[0][1] ){
                    $aux_al = $rowax[0][0] ;
                    $id_conceptoal = id_concepto($concepto);
                    guardarNovedad($aux_al , $empleado, $periodo, $id_conceptoal);
                }
            } 
        break;

        #Auxilio Transporte
        case '080':
            $salario = sueldo($empleado);
            $aux_at  = 0;
            $rowax = $con->Listar("SELECT pl.auxt, pl.tope_aux_transporte FROM gn_empleado e 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico
                LEFT JOIN gn_parametros_liquidacion pl ON et.tipo = pl.tipo_empleado and pl.vigencia = $panno 
                WHERE e.id_unico = $empleado");
            if($rowax[0][0] > 0){
                if($salario < $rowax[0][1] ){
                    $aux_at = $rowax[0][0] ;
                    if($diasTP<30){
                        $aux_at = ROUND(($aux_at * $diasTP) /30);
                    }
                    $id_conceptoal = id_concepto($concepto);
                    guardarNovedad($aux_at , $empleado, $periodo, $id_conceptoal);
                }
            } 
        break;
        #Factor Transporte
        case '953':
            $salario = sueldo($empleado);
            $aux_at  = 0;
            $rowax = $con->Listar("SELECT pl.auxt, pl.tope_aux_transporte FROM gn_empleado e 
                LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico
                LEFT JOIN gn_parametros_liquidacion pl ON et.tipo = pl.tipo_empleado and pl.vigencia = $panno 
                WHERE e.id_unico = $empleado");
            if($rowax[0][0] > 0){
                if($salario < $rowax[0][1] ){
                    $aux_at = $rowax[0][0] ;
                    $id_conceptoal = id_concepto($concepto);
                    guardarNovedad($aux_at , $empleado, $periodo, $id_conceptoal);
                }
            } 
        break;

        #FACTOR PRIMA ANTIGUEDAD
        case 'L007':
            $id_concepto = id_concepto($concepto);
            guardarNovedad('3.5' , $empleado, $periodo, $id_concepto);
        break;

        case 'L006':
            $id_concepto = id_concepto($concepto);
            guardarNovedad('3600' , $empleado, $periodo, $id_concepto);
        break;
    }
}
#Consultar Id Concepto con el código
function id_concepto($codigo){
    global $con;
    
    $c = $con->Listar("SELECT id_unico FROM gn_concepto WHERE codigo = '$codigo'");
    return $c[0][0];
}
#Consultar Códgo concepto con Id
function codigo_concepto($id_concepto){
    global $con;
    
    $c = $con->Listar("SELECT codigo FROM gn_concepto WHERE id_unico = '$id_concepto'");
    return $c[0][0];
}
#Guardar Novedad
function guardarNovedad($valor, $empleado, $periodo, $concepto){
    global $con;
    if($valor != 0){
        $sql_cons ="INSERT INTO `gn_novedad`
        ( `valor`, `fecha`, `empleado`, 
        `periodo`, `concepto`, `aplicabilidad`)
        VALUES (:valor, :fecha, :empleado, 
        :periodo, :concepto, :aplicabilidad)";
        $sql_dato = array(
            array(":valor",$valor),
            array(":fecha",date('Y-m-d')),
            array(":empleado",$empleado),
            array(":periodo",$periodo),
            array(":concepto",$concepto),
            array(":aplicabilidad",1),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);    
    } else {
        $resp ='';    
    }
    
    return $resp;
}
#Consultar Sueldo del empleado
function sueldo($empleado){
    global $con;
    $sa = $con->Listar(" SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual 
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
        WHERE e.id_unico = $empleado 
        ORDER BY e.id_unico");
    $salario = $sa[0][2];
    return $salario;
}
#Dias Totales Trabajados
function diasTPeriodo($empleado, $periodo){
    global $con;
    global $fechaIngreso ;
    global $fechaSalida;
    global $fechaInicio;
    global $fechaFin;
    global $diasPeriodo;
    $fing = new DateTime($fechaIngreso);
    $fsal = new DateTime($fechaSalida);
    $fin  = new DateTime($fechaInicio);
    $ffin = new DateTime($fechaFin);

    $diff           = $fsal ->diff($fing);
    $diasTotales    = $diff->days+1;
    if($fechaIngreso > $fechaInicio){
        if($fechaSalida<$fechaFin){
            $diff           = $fsal ->diff($fing);
            $diasTotales    = $diff->days+1;
        } else {
            $diff           = $ffin ->diff($fing);
            $diasTotales    = $diff->days+1;
        }
    } elseif($fechaSalida<$fechaFin){
        $diff           = $fsal ->diff($fin);
        $diasTotales    = $diff->days+1;
    } else {
        $diasTotales = $diasPeriodo;
    }
    return $diasTotales;
}

function diasTrabajados($fechaIngreso, $fechaSalida){
    $europeo = true;
    list($yy1, $mm1, $dd1) = explode('-', $fechaIngreso);
    list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
    if( $dd1==31) { $dd1 = 30; }
    if(!$europeo) {
        if( ($dd1==30) and ($dd2==31) ) {
          $dd2=30;
        } else {
          if( $dd2==31 ) {
            $dd2=30;
          }
        }
    }

    //check for invalid date
    if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
      ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
      ($yy1>$yy2) ) {
        return(-1);
        }
    if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
    if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }

    //Calc
    $yy = $yy2-$yy1;
    $mm = $mm2-$mm1;
    $dd = $dd2-$dd1;
    $diasTotales = (($yy*360)+($mm*30)+$dd+1 );
    return $diasTotales;
}
#Cálculo de Retenciones
function retencion($empleado, $periodo){
    global $con;
    #Fechas Periodo
    $fp = $con->Listar("SELECT DISTINCT p.fechainicio, p.fechafin, pa.uvt, p.periodo_retro  FROM gn_periodo p 
        LEFT JOIN gf_parametrizacion_anno pa oN p.parametrizacionanno = pa.id_unico WHERE p.id_unico = $periodo");
    $fechaI = $fp[0][0];
    $fechaF = $fp[0][1];
    #Valor UVT;
    $uvt = $fp[0][2];

    #Concepto R01
    $rowr1 = $con->Listar("SELECT COALESCE(SUM(n.valor), 0) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE c.ibr = 1 AND p.fechafin BETWEEN '$fechaI' and '$fechaF' 
        AND p.fechainicio BETWEEN '$fechaI' and '$fechaF' 
        AND n.empleado = $empleado AND c.clase = 1 ");
          
    $r01 = $rowr1[0][0];
    $r01 = ROUND($r01,0);
    $concepto = id_concepto('R01');
    guardarNovedad($r01, $empleado, $periodo, $concepto);

    #Concepto R02
    $rowr2 = $con->Listar("SELECT COALESCE(SUM(n.valor), 0) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE c.ibr = 1 AND p.fechafin BETWEEN '$fechaI' and '$fechaF' 
        AND p.fechainicio BETWEEN '$fechaI' and '$fechaF' 
        AND n.empleado = $empleado AND c.clase = 2 ");

    $r02 = $rowr2[0][0];
    $r02 = ROUND($r02,0);
    $concepto = id_concepto('R02');
    guardarNovedad($r02, $empleado, $periodo, $concepto);

    #Concepto R03
    $rowr3 = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE c.codigo = 'R03'
        AND n.empleado = $empleado AND n.periodo = $periodo ");
    $r03 = $rowr3[0][0];

    #Concepto R04
    if(!empty($r03)){
        $r04I = $r01 * $r03/100;
    } else {
        $r04I = 0;
    }
    if($r04I>(32*$uvt)){
        $r04 = 32*$uvt;
    } else {
        $r04 = $r04I;
    }
    $r04 = ROUND($r04,0);
    $concepto = id_concepto('R04');
    guardarNovedad($r04, $empleado, $periodo, $concepto);

    #Concepto R05
    $r05I = ($r01-$r02-$r04)*(25/100);  
    if($r05I>(240*$uvt)){
        $r05 = (240*$uvt);
    } else {
        $r05 = $r05I;
    }
    $r05 = ROUND($r05,0);
    $concepto = id_concepto('R05');
    guardarNovedad($r05, $empleado, $periodo, $concepto);    

    #Concepto 110
   

    $r110 = ($r01-$r02)-$r04-$r05;
    $r110 = ROUND($r110,0);
    $concepto = id_concepto('110');
    #Buscar concetpos del periodo 
    $vc1 = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE n.empleado = '$empleado' 
        AND c.codigo = '110'
        AND p.fechafin BETWEEN '$fechaI' and '$fechaF' 
        AND p.fechainicio BETWEEN '$fechaI' and '$fechaF' 
        AND p.tipoprocesonomina!=8");
    $r110g = $r110;
    if(!empty($vc1[0][0])){
        $r110g = $r110-$vc1[0][0];
    }
    guardarNovedad($r110g, $empleado, $periodo, $concepto);        

    #Concepto R06
    if ($uvt==0) {
        $r06=0;
    }else{
        $r06 = $r110/$uvt;
    }
 
    
    $r06 =  ROUND($r06,2);
    $concepto = id_concepto('R06');
    guardarNovedad($r06, $empleado, $periodo, $concepto); 

    #Buscar rango
    $rng = $con->Listar("SELECT DISTINCT id_unico, uvt_descontar, uvt_sumar, tarifa_marginal FROM gn_rango_retencion WHERE $r06 BETWEEN rango_inicial and rango_final");
    $rngSql = "SELECT DISTINCT id_unico, uvt_descontar, uvt_sumar, tarifa_marginal FROM gn_rango_retencion WHERE $r06 BETWEEN rango_inicial and rango_final";
    if(!empty($rng[0][0])){
        $r07 = (($r06 - $rng[0][1]) * ($rng[0][3]/100))+$rng[0][2];
        $r07 = ROUND($r07,2);
        $concepto = id_concepto('R07');
        guardarNovedad($r07, $empleado, $periodo, $concepto); 

        $r125 = ROUND($r07*$uvt, 0);
        $r125 = ROUND($r125/1000,0)*1000;
        $concepto = id_concepto('125');
        #Buscar concetpos del periodo 
        $vc2 = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            WHERE n.empleado = '$empleado' 
            AND c.codigo = '125'
            AND p.fechafin BETWEEN '$fechaI' and '$fechaF' 
            AND p.tipoprocesonomina!=8");
        $r125g = $r125;
        if(!empty($vc2[0][0])){
            $r125g = $r125-$vc2[0][0];
        }

        guardarNovedad($r125g, $empleado, $periodo, $concepto);  


    }
    return $r110.'-/-'.$uvt;

}
#Cálculo Incapacidades
function incapacidades($empleado, $periodo){
    global $con;
    global $fechaInicio;
    global $fechaFin;
    global $diasPeriodo;
    global $AuxilioTransporte;
    global $salarioactual;
    global $ppp;
    global $ppe;
    global $exc;
    global $pic;
    global $pcc;
    global $pse;
    global $psp;
    global $psen;
    global $pes;
    global $pmi;
    global $pfs ;

    $fin  = new DateTime($fechaInicio);
    $ffin = new DateTime($fechaFin);

    #Buscar Incapacidades
    $rowi = $con->Listar("SELECT id_unico, tiponovedad, numerodias, fechainicio, fechafinal 
        FROM gn_incapacidad  where empleado = $empleado AND fechainicio BETWEEN '$fechaInicio' AND '$fechaFin'");
    
    $totalIncapacidadE = 0;
    
    for ($i=0; $i <count($rowi) ; $i++) { 
        #Busca los días de la incapacidad
         #Buscar ´DIAS REALES DE INCAPACIDAD
        $rdias = $con->Listar("SELECT i.numerodias, tn.concepto FROM gn_incapacidad  i 
            LEFT JOIN gn_tipo_novedad tn ON i.tiponovedad = tn.id_unico
            where i.empleado = '$empleado' and i.tiponovedad = ".$rowi[$i][1]." and i.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' AND i.id_unico=".$rowi[$i][0]);
        $diasNovedad = 0;
        $diasIncapacidad = 0;
            
        if($diasPeriodo == $rdias[0][0]){
            if($diasPeriodo < 29){
                $diasNovedad = $diasPeriodo + 2;
                $diasIncapacidad = $rdias[0][0]+2;

            }elseif($diasPeriodo == 29){
                $diasNovedad = $diasPeriodo + 1;
                $diasIncapacidad = $rdias[0][0]+1;
            }elseif($diasPeriodo > 30){
                $diasNovedad = $diasPeriodo + 1;
                $diasIncapacidad = $rdias[0][0]+1;
            }else{
                $diasNovedad = $diasPeriodo ;
                $diasIncapacidad = $rdias[0][0];
            }
        } else {
            $diasNovedad = $rdias[0][0];
            $diasIncapacidad = $rdias[0][0];
        }

        $Y = $diasNovedad - $diasIncapacidad;
        if($Y <= 0){
            $V = $diasNovedad;   
        }else{
            $V = $diasIncapacidad;
        }


        $totaldias = $V;
        #Buscar los conceptos relacionados a la incapacidad 
        $rowc = $con->Listar("SELECT `id_unico`, `tipo_incapacidad`, `dias_incapacidad`, `porcentaje`, `dias`, `valor`, `ibc`, `aporte_pension_patrono`, `aporte_pension_empleado`, `aporte_salud_patrono`, `aporte_salud_empleado`, `caja_compensacion`, `sena`, `icbf`, `esap`, `ministerio_educacion`, `institutos_tecnicos`, `fondo_solidaridad`, `arl`  FROM `gn_concepto_incapacidad` WHERE tipo_incapacidad = ".$rowi[$i][1]." ORDER BY dias_incapacidad DESC");

        $fecha_inicio = $rowi[$i][3];
        $fecha_fin    = $rowi[$i][4];
        $dias_sum     = 0;
        //$dias_sum     = 0;
        for ($c=0; $c <count($rowc) ; $c++) { 
            if($totaldias > 0) {
                if(!empty($rowc[$c][2])){
                    if($totaldias >= $rowc[$c][2]){
                        $diasGuardar = $rowc[$c][2];
                        $totaldias  -= $rowc[$c][2];
                    } else {
                        $diasGuardar = $totaldias;
                        $totaldias  -= $totaldias;
                    }
                } else {
                    $diasGuardar = $totaldias;
                }
                $totalIncapacidadE += $diasGuardar;

                #NovedadDias
                guardarNovedad($diasGuardar, $empleado, $periodo, $rowc[$c][4]);

                IF($diasGuardar!= $diasIncapacidad){
                    $fecha_inicio = date("Y-m-d",strtotime($fecha_inicio."+".$dias_sum."days"));
                    $dias_sum    += $diasGuardar-1;
                    $fecha_fin    = date("Y-m-d",strtotime($fecha_inicio."+".$dias_sum."days"));
                    if($fecha_fin>$rowi[$i][4]){
                        $fecha_fin = $rowi[$i][4];
                    }
                }
                #Novedad Valor 
                $porcentaje = 100;
                if(!empty($rowc[$c][3])){
                    $porcentaje = $rowc[$c][3];
                }

                $vvt = round(($salarioactual +$AuxilioTransporte) * $diasGuardar/$diasPeriodo);
                $vvt = round($vvt*$porcentaje/100);
                guardarNovedad($vvt, $empleado, $periodo, $rowc[$c][5]);

                #IBC 
                $ibc = $vvt;
                guardarNovedad($ibc, $empleado, $periodo, $rowc[$c][6]);            

                #Pension Empleado
                $pensionEmpleado = ROUND(($ibc * $ppe) / 100);
                guardarNovedad($pensionEmpleado, $empleado, $periodo, $rowc[$c][8]);

                #Aporte Pension Patrono
                $porcentajePP= $ppp + $ppe;
                $pensionP    = ($ibc * $porcentajePP) / 100;
                $pensionP    = ROUND($pensionP);
                $pensionP    = $pensionP - $pensionEmpleado; 
                guardarNovedad($pensionP, $empleado, $periodo, $rowc[$c][7]);


                #Salud Empleado
                $saludEmpleado = ROUND(($ibc * $pse) / 100);  
                $saludEmpleado = ceil($saludEmpleado/100)*100;
                guardarNovedad($saludEmpleado, $empleado, $periodo, $rowc[$c][10]);             

                #Salud Patrono
                $saludPatrono = 0;
                if($exc != 1){
                    $porcentajeSP = $psp + $pse;
                    $saludPatrono = ($ibc * $porcentajeSP) / 100;
                    $saludPatrono = $saludPatrono - $saludEmpleado;  
                    guardarNovedad($saludPatrono, $empleado, $periodo, $rowc[$c][9]);                     
                }
                
                #Caja de Compensacion
                $cajac =0;
                if(!empty($rowc[$c][11])){
                    $cajac = ($ibc * $pcc) / 100;
                    $cajac = ceil($cajac/100)*100;
                    guardarNovedad($cajac, $empleado, $periodo, $rowc[$c][11]);
                }
                $sena = 0;
                $icbf = 0;
                $esap = 0;
                $mine = 0;
                $fondo = 0;
                if($exc != 1){                
                    
                    #Sena
                    if(!empty($rowc[$c][12])){
                        $sena = ($ibc * $psen) / 100;
                        $sena = ceil($sena/100)*100;
                        guardarNovedad($sena, $empleado, $periodo, $rowc[$c][12]);
                    }

                    #ICBF
                    if(!empty($rowc[$c][13])){
                        $icbf = ($ibc * $pic) / 100; 
                        $icbf = ceil($icbf/100)*100;
                        guardarNovedad($icbf, $empleado, $periodo, $rowc[$c][13]);
                    }
                    #ESAP
                    if(!empty($rowc[$c][14])){
                        $esap = ($ibc * $pes) / 100;
                        $esap = ceil($esap/100)/100;
                        guardarNovedad($esap, $empleado, $periodo, $rowc[$c][14]);
                    }

                    #MINEDUCACION
                    if(!empty($rowc[$c][15])){
                        $mine = ($ibc * $pmi) / 100;  
                        $mine = ceil($mine/100)/100;
                        guardarNovedad($mine, $empleado, $periodo, $rowc[$c][15]);
                    }

                    #IT
                    if(!empty($rowc[$c][16])){
                        //guardarNovedad($saludEmpleado, $empleado, $periodo, $rowc[$c][16]); 
                    }
                    #FONDO SOLIDARIDAD
                    if(!empty($rowc[$c][17])){
                        $fondo = ($ibc * $pfs) / 100;  
                        $fondo = ceil($fondo/100)/100;
                        guardarNovedad($fondo, $empleado, $periodo, $rowc[$c][17]);
                    }
                    #ARL
                    if(!empty($rowc[$c][18])){  
                        
                    }   
                    
                }

                $sql_cons ="INSERT INTO `gn_incapacidad_valor`
                ( `tipo_incapacidad`, `incapacidad`, `empleado`, `dias_incapacidad`, `valor`, `ibc`, `aporte_pension_patrono`, 
                    `aporte_pension_empleado`, `aporte_salud_patrono`, `aporte_salud_empleado`, `caja_compensacion`, `sena`, 
                    `icbf`, `esap`, `ministerio_educacion`, `institutos_tecnicos`, `fondo_solidaridad`, `arl`, `periodo`, `fecha_inicio`,`fecha_fin`)
                VALUES (:tipo_incapacidad, :incapacidad, :empleado, :dias_incapacidad, :valor, :ibc, :aporte_pension_patrono, 
                :aporte_pension_empleado, :aporte_salud_patrono, :aporte_salud_empleado, :caja_compensacion, :sena, 
                :icbf, :esap, :ministerio_educacion, :institutos_tecnicos, :fondo_solidaridad, :arl, :periodo, :fecha_inicio, :fecha_fin )";
                $sql_dato = array(
                    array(":tipo_incapacidad",$rowc[$c][1]),
                    array(":incapacidad",$rowi[$i][0]),
                    array(":empleado",$empleado),
                    array(":dias_incapacidad",$diasGuardar),
                    array(":valor",$vvt),
                    array(":ibc",$ibc),
                    array(":aporte_pension_patrono",$pensionP),
                    array(":aporte_pension_empleado",$pensionEmpleado),
                    array(":aporte_salud_patrono",$saludPatrono),
                    array(":aporte_salud_empleado",$saludEmpleado),
                    array(":caja_compensacion",$cajac),
                    array(":sena",$sena),
                    array(":icbf",$icbf),
                    array(":esap",$esap),
                    array(":ministerio_educacion",$mine),
                    array(":institutos_tecnicos",0),
                    array(":fondo_solidaridad",$fondo),
                    array(":arl",0),
                    array(":periodo",$periodo), 
                    array(":fecha_inicio",$fecha_inicio),
                    array(":fecha_fin",$fecha_fin), 
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato); 
            }
            
        }

    }
    return $totalIncapacidadE;
}
#Doceava Acumulado por concepto
function doceavaAcumulado($empleado,$concepto,$periodo, $fechaIngreso){
        
    global $con;
    global $fechaInicio;
    global $fechaFin; 
    $Total = 0;
    $sql = $con->Listar("SELECT    GROUP_CONCAT(nov.periodo) 
        FROM      gn_novedad nov
        LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
        LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo
        WHERE     nov.empleado          = $empleado 
        and nov.concepto = $concepto 
        AND (per.fechainicio BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin OR per.fechafin BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin) 
        ORDER BY  per.id_unico DESC");
    
    if(!empty($sql[0][0])) {
        $y = $sql[0][0];
        $sql_x = $con->Listar("SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
            LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
            LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
            WHERE     (nov.empleado          = $empleado)
            AND       (nov.periodo           IN ($y))
            AND       (nov.concepto          = $concepto)
            AND per.fechainicio >='$fechaIngreso'
            GROUP BY  nov.concepto");
        if(!empty($sql_x[0][1])){
            $Total = ROUND($sql_x[0][1]/12,0);    
        }
    }   
    
    
    return $Total;
}
#Ultimo valor liquidado por concepto
function ultimoValor($empleado,$concepto,$periodo, $fechaIngreso){
        
    global $con;
    global $fechaInicio;
    global $fechaFin;

    
    $sql = $con->Listar("SELECT    (nov.periodo), per.fechafin 
            FROM      gn_novedad nov
            LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
            LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo
            WHERE     nov.empleado          = $empleado 
            and nov.concepto = $concepto AND per.tipoprocesonomina != 12  
            ORDER BY per.fechainicio DESC
            limit 1");
    $Total = 0;
    if(!empty($sql[0][0])) {
        $y = $sql[0][0];
        $sql_x = $con->Listar("SELECT    con.id_unico, SUM(nov.valor) FROM gn_novedad nov
            LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
            LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
            WHERE     (nov.empleado          = $empleado)
            AND       (nov.periodo           IN ($y))
            AND       (nov.concepto          = $concepto)
            AND per.fechainicio >='$fechaIngreso' 
            GROUP BY  nov.concepto");
        
        if(!empty($sql_x[0][1])){
            $Total = ROUND($sql_x[0][1]/12,0);    
        }

        #BUSCAR PERIODO RETROACTIVO 
        $pretro = $con->Listar("SELECT SUM(nov.valor)
            FROM      gn_novedad nov
            LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
            LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo 
            WHERE     nov.empleado          = $empleado
            and nov.concepto = $concepto  AND per.tipoprocesonomina = 12 
            AND per.fechafin >'".$sql[0][1]."'            
            ORDER BY per.fechainicio DESC
            limit 1");
        if(!empty($pretro[0][0])){
            $Total += ROUND($pretro[0][0]/12,0);    
        }
    }   
    
    return $Total;
}
#Doceava horas extra
function doceavaHE($empleado,$periodo, $fechaIngreso){
    global $con;
    global $fechaInicio;
    global $fechaFin;


    $sql = $con->Listar("SELECT   GROUP_CONCAT(DISTINCT nov.periodo) , GROUP_CONCAT(DISTINCT c.id_unico)  
            FROM      gn_novedad nov
            LEFT JOIN gn_periodo per ON nov.periodo = per.id_unico 
            LEFT JOIN gn_periodo pera ON pera.id_unico = $periodo
            LEFT JOIN gn_concepto c ON nov.concepto = c.id_unico 
            LEFT JOIN gn_concepto cr ON c.conceptorel = cr.id_unico 
            WHERE     nov.empleado = $empleado 
            AND cr.clase= 9 
            AND (per.fechainicio BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin OR per.fechafin BETWEEN  (CONCAT(YEAR(pera.fechainicio)-1, '-', if(MONTH(pera.fechainicio)<10, CONCAT('0',MONTH(pera.fechainicio)),MONTH(pera.fechainicio) ), '-01') ) and pera.fechafin) 
            ORDER BY  per.id_unico DESC");
    $Total = 0;
    if(!empty($sql[0][0])) {
        $y = $sql[0][0];
        $concepto= $sql[0][1];
        $sql_x = $con->Listar("SELECT SUM(nov.valor) FROM gn_novedad nov
            LEFT JOIN gn_periodo per  ON nov.periodo  = per.id_unico
            LEFT JOIN gn_concepto con ON nov.concepto = con.id_unico
            WHERE     (nov.empleado          = $empleado)
            AND       (nov.periodo           IN ($y))
            AND       (nov.concepto          IN ($concepto))
            AND per.fechainicio >='$fechaIngreso' ");
        
        if(!empty($sql_x[0][0])){
            $Total = ROUND($sql_x[0][0]/12,0);    
        }
    }   
    return $Total;
}
#Valor concepto entre fechas
function valorConceptoFechas($empleado, $fechaI, $fechaF, $concepto){
    global $con;

    $sql = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE n.empleado = $empleado AND n.concepto = $concepto 
        AND p.fechainicio BETWEEN '".$fechaI."' AND '".$fechaF."'");
    $vlr = 0;
    if(!empty($sql[0][0])){
        $vlr = $sql[0][0];
    }
    return $vlr;
}

function valorConceptoFechasP($empleado, $fechaI, $fechaF, $concepto, $periodo) {
    global $con;

    $sql = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE n.empleado = $empleado AND n.concepto = $concepto 
        AND p.id_unico != $periodo 
        AND p.fechainicio BETWEEN '".$fechaI."' AND '".$fechaF."'");
    $vlr = 0;
    if(!empty($sql[0][0])){
        $vlr = $sql[0][0];
    }
    return $vlr;
}

#Valor Concepto Por Periodo
function valorConceptoPeriodo($empleado,$periodo, $concepto){
    global $con;

    $sql = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.empleado = $empleado AND c.codigo = '".$concepto."' 
        AND p.id_unico = $periodo ");
    $vlr = 0;
    if(!empty($sql[0][0])){
        $vlr = $sql[0][0];
    }
    return $vlr;

}
#Dias Pendientes por liquidar(Según tipo de proceoso)
function diasPendientes($tipo, $empleado, $fechaIngreso, $fechar, $fechaSalida){
    global $con;
    $calendario = CAL_GREGORIAN;
    if($tipo ==7){
        $upv = $con->Listar("SELECT DISTINCT v.fechafin FROM gn_vacaciones v 
            WHERE  v.empleado = $empleado  
            AND v.fechainicio >='".$fechaIngreso."'
            ORDER BY v.fechafin DESC");
    } else {
        $upv = $con->Listar("SELECT DISTINCT p.fechafin FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE p.tipoprocesonomina = $tipo  AND n.empleado = $empleado 
        AND p.fechainicio >='".$fechaIngreso."' 
        ORDER BY p.fechafin DESC");
    }
    if(empty($upv[0][0])){
        $upv = $con->Listar("SELECT DISTINCT p.fechafin FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_periodo pa ON p.periodo_retro = pa.id_unico 
        WHERE pa.tipoprocesonomina = $tipo  AND n.empleado = $empleado 
        AND p.fechainicio >='".$fechaIngreso."' 
        ORDER BY p.fechafin DESC");
    }
    $diasrv =0;
    if(!empty($upv[0][0])){
        $ffv = $upv[0][0];
        list($yy1, $mm1, $dd1) = explode('-', $ffv);
        list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
        

        $fechauv= new DateTime($ffv);
        $diffv  = $fechar ->diff($fechauv);
        $annos = $diffv->y;
        $meses = $diffv->m;    
        $dias  = $diffv->d;
        
        if($dd1>$dd2){
            $mesfn = $mm2-1;
            $diafn = cal_days_in_month($calendario, $mesfn, $yy2); 
            $diafnI= cal_days_in_month($calendario, $mm1, $yy2); 
            if($diafn==31){     
                if(($dd1!=30 && $dd1==31)){
                    //$dias -=1;
                }
            }elseif($diafn==28){
                $dias +=2;
            }
        }
        $diasrv += (((($annos*12)+$meses)*30)+$dias);
        
    } else {
        $ffv = $fechaIngreso;//sUMARLE 1 
        $europeo = true;
        list($yy1, $mm1, $dd1) = explode('-', $ffv);
        list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
        if( $dd1==31) { $dd1 = 30; }
        if(!$europeo) {
            if( ($dd1==30) and ($dd2==31) ) {
              $dd2=30;
            } else {
              if( $dd2==31 ) {
                $dd2=30;
              }
            }
        }

        //check for invalid date
        if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
          ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
          ($yy1>$yy2) ) {
            return(-1);
            }
        if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
        if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }

        //Calc
        $yy = $yy2-$yy1;
        $mm = $mm2-$mm1;
        $dd = $dd2-$dd1;
        $diasrv += (($yy*360)+($mm*30)+$dd+1 );

        
    }

    #Dias No trabajados 
    $id_cdnt = id_concepto('361');
    $diasnt  = valorConceptoFechas($empleado, $ffv, $fechaSalida,$id_cdnt);
    $diasrv -=$diasnt;
   

    return $diasrv;
}

#Dias Pendientes por liquidar(Según tipo de proceso != Periodo)
function diasPendientes2($tipo, $empleado, $fechaIngreso, $fechar, $fechaSalida, $periodo){
    global $con;
    $calendario = CAL_GREGORIAN;
    if($tipo ==7){
        $upv = $con->Listar("SELECT DISTINCT v.fechafin FROM gn_vacaciones v 
            WHERE  v.empleado = $empleado  
            AND v.fechainicio >='".$fechaIngreso."'
            ORDER BY v.fechafin DESC");
    } else {
        $upv = $con->Listar("SELECT DISTINCT p.fechafin FROM gn_novedad n 
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        WHERE p.tipoprocesonomina = $tipo  AND n.empleado = $empleado 
        AND p.fechainicio >='".$fechaIngreso."'  AND p.id_unico != $periodo 
        ORDER BY p.fechafin DESC");
    }
   
    $diasrv =0;
    if(!empty($upv[0][0])){
        $ffv = $upv[0][0];
        list($yy1, $mm1, $dd1) = explode('-', $ffv);
        list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
        

        $fechauv= new DateTime($ffv);
        $diffv  = $fechar ->diff($fechauv);
        $annos = $diffv->y;
        $meses = $diffv->m;    
        $dias  = $diffv->d;
        
        if($dd1>$dd2){
            $mesfn = $mm2-1;
            $diafn = cal_days_in_month($calendario, $mesfn, $yy2); 
            $diafnI= cal_days_in_month($calendario, $mm1, $yy2); 
            if($diafn==31){
     
                if(($dd1!=30 && $dd1==31)){
                    $dias -=1;
                }
            }elseif($diafn==28){
                $dias +=2;
            }
        } 
       
        $diasrv += (((($annos*12)+$meses)*30)+$dias);
    } else {
        $ffv = $fechaIngreso;//sUMARLE 1 
        $europeo = true;
        list($yy1, $mm1, $dd1) = explode('-', $ffv);
        list($yy2, $mm2, $dd2) = explode('-', $fechaSalida);
        if( $dd1==31) { $dd1 = 30; }
        if(!$europeo) {
            if( ($dd1==30) and ($dd2==31) ) {
              $dd2=30;
            } else {
              if( $dd2==31 ) {
                $dd2=30;
              }
            }
        }

        //check for invalid date
        if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
          ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
          ($yy1>$yy2) ) {
            return(-1);
            }
        if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
        if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }

        //Calc
        $yy = $yy2-$yy1;
        $mm = $mm2-$mm1;
        $dd = $dd2-$dd1;
        $diasrv += (($yy*360)+($mm*30)+$dd+1 );

        
    }
    #Dias No trabajados 
    $id_cdnt = id_concepto('361');
    $diasnt  = valorConceptoFechas($empleado, $ffv, $fechaSalida,$id_cdnt);
    $diasrv -=$diasnt;


    return $diasrv;
}