<?php
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
require 'prima.php';
require '../Dias_Incapacidad.php';
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
$empleado   = $_REQUEST['id_emp'];  
$periodo    = $_REQUEST['id_per'];  

$rowp        = $con->Listar("SELECT fechainicio, fechafin, dias_nomina FROM  gn_periodo WHERE id_unico = $periodo");
$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        

$rta        = 0;
#* EMPLEADOS NORMALES

if(empty($empleado)|| $empleado==2){
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
       tc.categoria, 
       c.salarioactual,
       (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) ulmv, 
       (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico)) ulmve, 
       CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), et.tipo  
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico != 2 
    ORDER BY e.id_unico"); 
} else {
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
       tc.categoria, 
       c.salarioactual,
       (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) ulmv, 
       (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico)) ulmve,
       CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), et.tipo  
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico = $empleado 
    ORDER BY e.id_unico"); 
}
for ($i = 0; $i < count($rowe); $i++) {
    #* Parametros 

    $rowP = $con->Listar("SELECT id_unico, vigencia, salmin, auxt, primaA, tipo_prima_servicio, dias_prima_servicio , 
        tope_aux_transporte, auxt, talimentacion, primaA
        FROM gn_parametros_liquidacion WHERE vigencia = $anno AND tipo_empleado  = ".$rowe[$i][6]);

    $pid        = $rowP[0][0]; 
    $pvi        = $rowP[0][1]; 
    $psm        = $rowP[0][2]; 
    $pat        = $rowP[0][3]; 
    $ppa        = $rowP[0][4];

    $id_empleado = $rowe[$i][0];
    $fecha_ar    = $rowe[$i][3];
    $estado_ar   = $rowe[$i][4];
    $liquidar    = 1;
    
    if($estado_ar==2 && $fecha_ar < $fechaFin){
        $liquidar    = 0;
    } elseif($estado_ar==1 && $fecha_ar> $fechaFin) {
        $liquidar    = 0;
    }
    if($liquidar==1){
        #Elimina novedades periodo -dias sindicato
        $ld = "DELETE  n.* FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            WHERE n.periodo = $periodo AND n.empleado = $id_empleado AND n.aplicabilidad in(1,2,3) ";
        $resultado1 = $mysqli->query($ld);

        
        #*Asignación Básica
        $salario = $rowe[$i][2];
        $id_conceptos = id_concepto('001');
        $gn = guardarNovedad($salario, $id_empleado, $periodo, $id_conceptos);
        
        
        switch ($rowP[0][5]) {
            case 'Convencion':
                
                #*Dias Trabajados
                $rowdt = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('009','011','035','350','351','352','353','354','355','367','368','960') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                     AND p.tipoprocesonomina NOT IN (12)");

                $dias_t = $rowdt[0][0] + 30;
                if($periodo == 114 && $dias_t > 360){
                    $dias_t = 360; 
                }
                $id_conceptodt = id_concepto('038');
                guardarNovedad($dias_t, $id_empleado, $periodo, $id_conceptodt);
                
                            
                #* Auxilio Transporte 
                $aux_tr = 0;
                if( $rowP[0][8] > 0 ){
                    if( $salario < $rowP[0][7] ){
                        $aux_tr = $rowP[0][8] ;
                        $id_conceptoat = id_concepto('953');
                        guardarNovedad($aux_tr, $id_empleado, $periodo, $id_conceptoat);
                    }
                }
                #* Auxilio Alimentacion
                $aux_al = 0;
                if( $rowP[0][10] > 0 ){
                    if( $salario < $rowP[0][9] ){
                        $aux_al = $rowP[0][10] ;
                        $id_conceptoal = id_concepto('954');
                        guardarNovedad($aux_al , $id_empleado, $periodo, $id_conceptoal);
                    }
                }   
                
                #*Horas Extras ** 
                $h_ex = 0;
                $hex  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    LEFT JOIN gn_concepto cr ON cr.id_unico = c.conceptorel
                    LEFT JOIN gn_novedad nv ON nv.concepto = cr.id_unico AND nv.empleado =  $id_empleado AND nv.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND cr.clase = 9 AND n.valor IS NOT NULL 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' ");
                if(!empty($hex[0][0])){
                    $h_ex = ROUND($hex[0][0]/12);
                }
                if($h_ex>0){
                    $id_conceptohe = id_concepto('869');
                    guardarNovedad($h_ex , $id_empleado, $periodo, $id_conceptohe);
                }


                
                #*Prima vacaciones ** 
                $p_vac = 0;
                $row_pv  = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('175') 
                    AND p.fechainicio < '$fechaFin' 
                    AND n.periodo != $periodo 
                    ORDER BY p.fechainicio DESC 
                    LIMIT 1");
                if(!empty($row_pv[0][0])){
                    $p_vac = ROUND($row_pv[0][0]/12);
                }
                if($p_vac>0){
                    $id_conceptohe = id_concepto('804');
                    guardarNovedad($p_vac , $id_empleado, $periodo, $id_conceptohe);
                }

                #*Prima Antiguedad ** 
                $p_antg = 0;
                $row_antg  = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('150') 
                    AND p.fechainicio < '$fechaFin' 
                    AND n.periodo != $periodo 
                    ORDER BY p.fechainicio DESC 
                    LIMIT 1 ");
                if(!empty($row_antg[0][0])){
                    $p_antg = ROUND($row_antg[0][0]/12);
                }
                if($p_antg>0){
                    $id_conceptoantg = id_concepto('806');
                    guardarNovedad($p_antg , $id_empleado, $periodo, $id_conceptoantg);
                }

                #*Prima Semestral + RETROACTIVO ** 
                $tpsm  = 0;
                $p_sem = 0;
                $row_psm  = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('160') 
                    AND p.fechainicio < '$fechaFin' 
                    AND n.periodo != $periodo
                    ORDER BY p.fechainicio DESC 
                    LIMIT 1 ");
                if(!empty($row_psm[0][0])){
                    $p_sem = ROUND($row_psm[0][0]/12);
                }

                $v_retro = 0;
                $row_retro  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('1031') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin'");
                if(!empty($row_retro[0][0])){
                    $v_retro = ROUND($row_retro[0][0]/12);
                }
                $tpsm = $p_sem + $v_retro ; 

                if($tpsm>0){
                    $id_conceptopsm = id_concepto('1002');
                    guardarNovedad($tpsm , $id_empleado, $periodo, $id_conceptopsm);
                }

                #*Prima Navidad ** 
                $p_nav = 0;
                $row_pnav  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('158') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin'");
                if(!empty($row_pnav[0][0])){
                    $p_nav = ROUND($row_pnav[0][0]/12);
                }
                if($p_nav>0){
                    $id_conceptopnv = id_concepto('1011');
                    guardarNovedad($p_nav , $id_empleado, $periodo, $id_conceptopnv);
                }


              

                
                $prima = 0;
                
                if(!empty($rowP[0][6]) ){
                    $diascp = $rowP[0][6];
                    if( $dias_t <360 ){
                        $pp = ROUND(($diascp * $dias_t)/360);
                        $prima = ROUND((($salario + $aux_tr + $aux_al + $h_ex + $p_vac + $p_antg + $tpsm+ $p_nav) *$pp)/30);
                        $id_conceptodp = id_concepto('090');
                        guardarNovedad($pp , $id_empleado, $periodo, $id_conceptodp);
                    } else {
                        $prima = ROUND((($salario + $aux_tr + $aux_al + $h_ex + $p_vac + $p_antg + $tpsm+ $p_nav) *$diascp)/30);
                        $id_conceptodp = id_concepto('090');
                        guardarNovedad($diascp , $id_empleado, $periodo, $id_conceptodp);
                    }
                }
             
                #Prima
                $id_conceptops = id_concepto('160');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptops);
                
                #Devengos
                $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo
                    AND n.empleado = $id_empleado 
                    AND c.clase = 1 and c.unidadmedida = 1");
                
                if(empty($dv[0][0])){
                    $tdv = 0;
                } else {
                    $tdv = $dv[0][0];   
                }
                $id_conceptotd = id_concepto('097');
                guardarNovedad($tdv , $id_empleado, $periodo, $id_conceptotd);

                #Descuentos
                $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo
                    AND n.empleado = $id_empleado 
                    AND c.clase = 2 and c.unidadmedida = 1");
                if(empty($dv[0][0])){
                    $tds = 0;
                } else {
                    $tds = $dv[0][0];
                }
                $id_conceptods = id_concepto('140');
                guardarNovedad($tds , $id_empleado, $periodo, $id_conceptods);

                
                #Neto
                $np = $tdv -$tds;
                $id_conceptonp = id_concepto('144');
                $ge = guardarNovedad($np , $id_empleado, $periodo, $id_conceptonp);


                if(empty($ge)){
                    $rta +=1;
                }    
            break;
            
            case 'Libre Nombramiento':
                #*Dias Trabajados
                $rowdt = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND  c.codigo IN('009','011','035','350','351','352','353','354','355','367','368','960') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.tipoprocesonomina NOT IN (12)");

                $dias_t = $rowdt[0][0] + 30;
                if($periodo == 114 && $dias_t > 360){
                    $dias_t = 360; 
                }
                $id_conceptodt = id_concepto('038');
                guardarNovedad($dias_t, $id_empleado, $periodo, $id_conceptodt);
                
                     
                #* Auxilio Transporte 
                $aux_tr = 0;
                if($rowP[0][8] > 0){
                    if($salario < $rowP[0][7]){
                        $aux_tr = $rowP[0][8] ;
                        $id_conceptoat = id_concepto('953');
                        guardarNovedad($aux_tr, $id_empleado, $periodo, $id_conceptoat);
                    }
                }
                #* Auxilio Alimentacion
                $aux_al = 0;
                if($rowP[0][10] > 0){
                    if($salario < $rowP[0][9]){
                        $aux_al = $rowP[0][10] ;
                        $id_conceptoal = id_concepto('954');
                        guardarNovedad($aux_al , $id_empleado, $periodo, $id_conceptoal);
                    }
                }         

                
                #*Bonificacion SP
                $b_ser = 0;
                $bonf  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('161') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' ");
                if(!empty($bonf[0][0])>0){
                    $b_ser = ROUND($bonf[0][0]/12);
                }
                if($b_ser>0){
                    $id_conceptobs = id_concepto('988');
                    guardarNovedad($b_ser , $id_empleado, $periodo, $id_conceptobs);
                }       
                
                if(!empty($rowP[0][6])){
                    $diascp = $rowP[0][6];
                    if($dias_t <360){
                        $pp = ROUND(($diascp * $dias_t)/360);
                        $prima = ROUND((($salario + $aux_tr + $aux_al + $b_ser) *$pp)/30);
                        $id_conceptodp = id_concepto('090');
                        guardarNovedad($pp , $id_empleado, $periodo, $id_conceptodp);
                    } else {
                        $prima = ROUND((($salario + $aux_tr + $aux_al + $b_ser) *$diascp)/30);
                        $id_conceptodp = id_concepto('090');
                        guardarNovedad($diascp , $id_empleado, $periodo, $id_conceptodp);
                    }
                }
                
                $id_conceptops = id_concepto('160');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptops);
                
                #Devengos
                $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo
                    AND n.empleado = $id_empleado 
                    AND c.clase = 1 and c.unidadmedida = 1");
                if(empty($dv[0][0])){
                    $tdv = 0;
                } else {
                    $tdv = $dv[0][0];   
                }
                $id_conceptotd = id_concepto('097');
                guardarNovedad($tdv , $id_empleado, $periodo, $id_conceptotd);

                #Descuentos
                $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo
                    AND n.empleado = $id_empleado 
                    AND c.clase = 2 and c.unidadmedida = 1");
                if(empty($dv[0][0])){
                    $tds = 0;
                } else {
                    $tds = $dv[0][0];
                }
                $id_conceptods = id_concepto('140');
                guardarNovedad($tds , $id_empleado, $periodo, $id_conceptods);

                
                #Neto
                $np = $tdv -$tds;
                $id_conceptonp = id_concepto('144');
                $ge = guardarNovedad($np , $id_empleado, $periodo, $id_conceptonp);


                if(empty($ge)){
                    $rta +=1;
                }       
            break;
            case 'Privados':
                #Elimina novedades periodo -dias sindicato
                $ld = "DELETE  n.* FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo AND c.codigo != '280' AND n.empleado = $id_empleado ";
                $resultado1 = $mysqli->query($ld);
                
                #*Asignación Básica
                $salario = $rowe[$i][2];
                $id_conceptos =    ('001');
                $gn = guardarNovedad($salario, $id_empleado, $periodo, $id_conceptos);
                
                #*Dias Trabajados
                $dias_t = 0;
                if($fecha_ar< $fechaInicio){
                    $dias_t = 180;
                } else {
                    $date1  = new DateTime($fecha_ar);
                    $date2  = new DateTime($fechaFin);
                    $dias_t = $date1->diff($date2);
                    $dias_t = $dias_t->days;
                }
                if($dias_t>180){
                    $dias_t = 180;
                }
                
                $id_conceptodt = id_concepto('038');
                guardarNovedad($dias_t, $id_empleado, $periodo, $id_conceptodt);
                
                #*Dias Sindicato
                $dias_s = 0;
                $ds = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo AND c.codigo = '280' AND n.empleado = $id_empleado");
                if(count($ds)>0){
                    $dias_s += $ds[0][0];
                }
                $totaldias = 0;
                $totaldias = $dias_t + $dias_s;
                
                #*Auxilio Transporte *** valor paraámetros
                $aux_tr = 0;
                $auxt = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo = '080' 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    ORDER BY n.id_unico DESC 
                    LIMIT 1 ");
                if(count($aux_tr)>0){
                    $aux_tr = $auxt[0][0];
                }
                if($aux_tr>0){
                    $id_conceptoat = id_concepto('953');
                    guardarNovedad($aux_tr, $id_empleado, $periodo, $id_conceptoat);
                }
                
                #*Auxilio Alimentacion valor paraámetros
                $aux_al = 0;
                $auxa = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo = '079' 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    ORDER BY n.id_unico DESC 
                    LIMIT 1 ");
                if(count($auxa)>0){
                    $aux_al = $auxa[0][0];
                }
                if($aux_al>0){
                    $id_conceptoal = id_concepto('954');
                    guardarNovedad($aux_al , $id_empleado, $periodo, $id_conceptoal);
                }
                
                #*Horas Extras ** concepto horas extras ***ultumo año

                $h_ex = 0;
                $hex  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('050','051','052','053','054','055','056','057','058','059') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    ORDER BY n.id_unico DESC 
                    LIMIT 1 ");
                if(count($hex)>0){
                    ##PRIVADO
                    $h_ex = ($hex[0][0]/$totaldias)*30;
                    
                }
                if($h_ex>0){
                    $id_conceptohe = id_concepto('1008');
                    guardarNovedad($h_ex , $id_empleado, $periodo, $id_conceptohe);
                }
                
                #*Comision
                $v_com = 0;
                $coms  = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('1000') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    ORDER BY n.id_unico DESC ");
                if(count($coms)>0){
                    $v_com = ($coms[0][0]/$totaldias)*30;
                }
                if($v_com>0){
                    $id_conceptocom = id_concepto('1001');
                    guardarNovedad($v_com , $id_empleado, $periodo, $id_conceptocom);
                }
                
                #*Bonificacion SP
                $b_ser = 0;
                $bonf  = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.empleado = $id_empleado AND c.codigo IN('161') 
                    AND p.fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' 
                    AND p.fechafin BETWEEN '$fechaInicio' AND '$fechaFin' 
                    ORDER BY n.id_unico DESC 
                    LIMIT 1 ");
                if(count($bonf)>0){
                    $b_ser = ROUND($bonf[0][0]/12);
                }
                if($b_ser>0){
                    $id_conceptobs = id_concepto('956');
                    guardarNovedad($b_ser , $id_empleado, $periodo, $id_conceptobs);
                }        
                
                #prIVADP
                $prima = ROUND((($salario+$aux_tr+$aux_al+$h_ex+$b_ser+$v_com) * $totaldias)/360);

                
                $id_conceptops = id_concepto('160');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptops);
                
                $id_conceptotd = id_concepto('097');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptotd);
                
                $id_conceptonp = id_concepto('144');
                $ge = guardarNovedad($prima , $id_empleado, $periodo, $id_conceptonp);
                if(empty($ge)){
                    $rta +=1;
                }         
            break;
            case 'Pensionados':      
                #*Dias Trabajados
                $dias_t = 0;
                if($fecha_ar< $fechaInicio){
                    $dias_t = 180;
                } else {
                    $date1  = new DateTime($fecha_ar);
                    $date2  = new DateTime($fechaFin);
                    $dias_t = $date1->diff($date2);
                    $dias_t = $dias_t->days;
                }
                if($dias_t>180){
                    $dias_t = 180;
                }
                
                $id_conceptodt = id_concepto('038');
                guardarNovedad($dias_t, $id_empleado, $periodo, $id_conceptodt);
                
                #*Dias Sindicato
                $dias_s = 0;
                $ds = $con->Listar("SELECT n.valor FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE n.periodo = $periodo AND c.codigo = '280' AND n.empleado = $id_empleado");
                if(count($ds)>0){
                    $dias_s += $ds[0][0];
                }
                $totaldias = 0;
                $totaldias = $dias_t + $dias_s;
                
                
                $prima = ROUND((($salario) * $totaldias)/180);
                
                
                $id_conceptops = id_concepto('160');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptops);
                
                $id_conceptotd = id_concepto('097');
                guardarNovedad($prima , $id_empleado, $periodo, $id_conceptotd);
                
                $id_conceptonp = id_concepto('144');
                $ge = guardarNovedad($prima , $id_empleado, $periodo, $id_conceptonp);
                if(empty($ge)){
                    $rta +=1;
                }  
            break;
        }
    }
}

echo $rta;

function id_concepto($codigo){
    global $con;
    
    $c = $con->Listar("SELECT id_unico FROM gn_concepto WHERE codigo = '$codigo'");
    return $c[0][0];
}

function guardarNovedad($valor, $empleado, $periodo, $concepto){
    global $con;
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
    return $resp;
}
