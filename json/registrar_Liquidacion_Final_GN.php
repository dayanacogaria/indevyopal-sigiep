<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $compania = $_SESSION['compania'];
  $anno = $_SESSION['anno'];

  if(!empty($_REQUEST['id_emp'])){

        $empleado = $_REQUEST['id_emp'];
    }

    if(!empty($_REQUEST['id_per'])){

        $periodo = $_REQUEST['id_per'];
    }

    if(!empty($_POST['sltEmpleado'])){

        $empleado  = $_POST['sltEmpleado'];  
        $emp  = $_POST['sltEmpleado'];  
    }

    if(!empty($_POST['sltPeriodo'])){

        $periodo   = $_POST['sltPeriodo'];  
    }
    $fecha_retiro = $_POST['fechaR'];  
    $fcha_ret = explode('/', $fecha_retiro);
    $a_ret = $fcha_ret[2];
    $m_ret = $fcha_ret[1];
    $d_ret = $fcha_ret[0];
    $fecha_retiro = '"'.$a_ret.'-'.$m_ret.'-'.$d_ret.'"';
    $fc_retiro = $a_ret.'-'.$m_ret.'-'.$d_ret;

    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode('-', $hoy);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';

    //si el empleado es 2 osea varios se debe registrar el mimo procesos para todos 


    if(empty($empleado)|| $empleado==2){

      $DiaTRA = "SELECT dias_nomina,fechainicio,fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
      $dias = $mysqli->query($DiaTRA);
      $DTR = mysqli_fetch_row($dias);

        $sql_listar_emp = "SELECT DISTINCT    e.id_unico, 
                                        e.tercero, 
                                        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                                        tc.categoria, 
                                        c.id_unico, 
                                        c.nombre, 
                                        c.salarioactual,
                                        e.unidadejecutora,
                                        cr.valor 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                WHERE e.id_unico != 2 AND e.unidadejecutora !=4 AND vr.estado=1  AND vr.vinculacionretiro IS NULL 
                OR e.id_unico != 2 AND e.unidadejecutora !=4 AND vr.estado = 2 AND vr.fecha BETWEEN '$DTR[1]' AND '$DTR[2]' ORDER BY `e`.`id_unico` ASC"; 
        $res_emp = $mysqli->query($sql_listar_emp);    
        $n_emp = mysqli_num_rows($res_emp);
        while($row_emp = mysqli_fetch_row($res_emp)){
            $empleado=$row_emp[0];
            //1. Buscar y/o registrar vinculacion retiro
            //consulta que me trae el id_tercero, nombre , fecha del ultimo ingreso y el id de la ultima vinculacion  y el valor del auxilio de transporte
            $sql_vin = "SELECT  e.id_unico, 
                             e.tercero, 
                             CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) as tercero, 
                                c.salarioactual,
                             (SELECT ingreso.fecha FROM gn_empleado e_ing 
                              LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                              WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                              fecha_ingreso,
                              (SELECT ingreso.id_unico FROM gn_empleado e_ing 
                               LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico  
                               WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                               id_ingreso, 
                                
                               (SELECT auxt FROM gn_parametros_liquidacion WHERE vigencia = $anno) as vlor_auxilio,
                               (SELECT salmin FROM gn_parametros_liquidacion WHERE vigencia = $anno) as vlor_auxilio
                                

                        FROM gn_empleado e 
                        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                        LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                        WHERE e.id_unico = $empleado ";
            $res = $mysqli->query($sql_vin);
            $row_vin = mysqli_fetch_row($res);

            $id_tercero=$row_vin[1];
            $nom_tercero=$row_vin[2];
            $salario_tercero=$row_vin[3];
            $fecha_ingreso=$row_vin[4];
            $id_ingreso=$row_vin[5];
            $aux_trans=$row_vin[6];
            $vlr_salario_min=$row_vin[7];


            //con el id_ingreso buscamos si tiene o no  la vinculacion retiro
            $sql_vin_ret = "SELECT retiro.* FROM gn_empleado e_retiro 
                        LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_retiro.id_unico              
                        WHERE e_retiro.id_unico = $empleado and retiro.estado=2 and retiro.vinculacionretiro=$id_ingreso
                        order by retiro.fecha desc LIMIT 1 ";
            $res_ret = $mysqli->query($sql_vin_ret);
            $n_ret = mysqli_num_rows($res_ret);
            if($n_ret > 0){
            
              $row_ret = mysqli_fetch_row($res_ret);
              $id_retiro=$row_ret[0];
              $fecha_ret=$row_ret[3];
                        
            }else{
              $id_retiro=0;
              $fecha_ret=$fecha_retiro;
              //registrar la vinculacion retiro
              $sql_insert_retiro = "INSERT INTO gn_vinculacion_retiro(fechaacto,fecha,empleado,tipovinculacion,estado,causaretiro,
              vinculacionretiro) VALUES($fecha_ret,$fecha_ret,$empleado,NULL,2,3,$id_ingreso)";
              $resul_retiro = $mysqli->query($sql_insert_retiro);

              $sql_update = "UPDATE gn_vinculacion_retiro set vinculacionretiro = $id_ingreso where id_unico = $id_ingreso";
              $resul_retiro = $mysqli->query($sql_update);

              $sql_update = "UPDATE gn_empleado set estado = 2  where id_unico = $empleado";
              $resul_retiro = $mysqli->query($sql_update);

            }
           // echo '   empleado   '.$empleado.'  ingreso '.$fecha_ingreso.'  retiro  '.$fc_retiro;

         
            $fch_ing = explode('-', $fecha_ingreso);
            $a_ing = $fch_ing[0];
            $m_ing = $fch_ing[1];
            $d_ing = $fch_ing[2];
            
            $y_ing=$a_ing;
            $mont_ing=$m_ing;
            $cont_mes=0;
            while ($y_ing<=$a_ret){

               if($y_ing<$a_ret){
                    while($y_ing<$a_ret){
                      if($mont_ing<12){
                        $cont_mes++;  
                        $mont_ing++;
                        
                      }else{
                        $y_ing++;     
                      }      
                    }
                  }
            
                if($y_ing==$a_ret){
                  $cont_mes=$cont_mes+$m_ret;
              
                  $y_ing++;
                }     

            }
            $dias_ad=0;
            if($d_ret <30){
              $cont_mes--;
              $dias_ad=$d_ret;
            }    
          //echo '  meses '.$cont_mes;
         // $dias_trab = ($cont_mes*30)+$dias_ad;
          //echo '  dias '.$dias_trab;
          $dias_trab = (strtotime($fecha_ingreso)-strtotime($fc_retiro))/86400;
          $dias_trab = abs($dias_trab); 
          $dias_trab = floor($dias_trab);
          $dias_prima=$dias_trab;
          $dias_vac=$dias_trab;
          $dias_ces=$dias_trab;

          //registrar novedad dias trabajados 

          //1. Consultar los dias trabajados y pagos en la prima 
          /*
          $sql_dias_pr = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=2 and n.concepto=7  and n.fecha>='$fecha_ingreso' ";
            $res_pr = $mysqli->query($sql_dias_pr);
            $n_pr = mysqli_num_rows($res_pr);
            if($n_pr > 0)
            {
                 $row_pr = mysqli_fetch_row($res_pr);
                 $dias_prima= $dias_prima-$row_pr[0];
            }*/
            $sql_dias_pr = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empleado and p.tipoprocesonomina=2 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
            $res_pr = $mysqli->query($sql_dias_pr);
            $n_pr = mysqli_num_rows($res_pr);
            if($n_pr > 0)
            {
                $row_pr = mysqli_fetch_row($res_pr);
                $ffin_prima = explode('-', $row_pr[0]);
                if($ffin_prima[1]==12){
                    $ms_inipr=1;
                    $fini_prim= $a_ret.'-01-01';
                }else if($ffin_prima[1]==06){
                    $ms_inipr=7;
                    $fini_prim= $a_ret.'-07-01';
                }
                $cont=0;
                for($x=$ms_inipr;$x<=($m_ret-1);$x++){
                   $cont++;
                }
                $dias_prima=($cont*30)+$d_ret;
            }
            //2. Consultar los dias pagados de vacaciones 
            /*
            $sql_dias_vac = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=7 and n.concepto=7 and n.fecha>='$fecha_ingreso'";
            $res_vac = $mysqli->query($sql_dias_vac);
            $n_vac = mysqli_num_rows($res_vac);
            if($n_vac > 0)
            {
                 $row_vac = mysqli_fetch_row($res_vac);
                 $dias_vac= $dias_vac-$row_vac[0];
            }*/
            $sql_dias_vac = "SELECT fechafin from gn_vacaciones where fechaInicio >='$fecha_ingreso' and empleado = $empleado order by fechafin desc ";
            $res_v = $mysqli->query($sql_dias_vac);
            $n_vac = mysqli_num_rows($res_v);
            if($n_vac > 0)
            {
                $row_v = mysqli_fetch_row($res_v);
                $fult_vac = $row_v[0];                                         
            }else{
                $fult_vac = $fecha_ingreso;
            }
            if($fult_vac==$fecha_ingreso){
                $dias_vac= $dias_vac;
            }else{
                $dias_vac = (strtotime($fult_vac)-strtotime($fc_retiro))/86400;
                $dias_vac = abs($dias_vac); 
                $dias_vac = floor($dias_vac);
            }
            $dias_vac = ($dias_vac * 15)/360;
            $dias_vac = round($dias_vac);

            //3. Consultar los dias pagados de cesantias 
            /*
            $sql_dias_ces = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=11 and n.concepto=7 and n.fecha>='$fecha_ingreso'";
            $res_ces = $mysqli->query($sql_dias_ces);
            $n_ces = mysqli_num_rows($res_ces);
            if($n_ces > 0)
            {
                 $row_ces = mysqli_fetch_row($res_ces);
                 $dias_ces= $dias_ces-$row_ces[0];
            }*/
            $sql_dias_ces = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empl_sim and p.tipoprocesonomina=11 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
            $res_c = $mysqli->query($sql_dias_ces);
            $n_c = mysqli_num_rows($res_c);
            if($n_c > 0)
            {
                $row_c = mysqli_fetch_row($res_c);
                $ffin_ces = explode('-', $row_c[0]);
                if($ffin_ces[1]==12){
                    $fini_ces= ($ffin_ces[0]+1).'-01-01';
                }else {
                    $fini_ces= $ffin_ces[0].'-01-01';
                }
                $dias_ces = (strtotime($fini_ces)-strtotime($fc_retiro))/86400;
                $dias_ces = abs($dias_ces); 
                $dias_ces = floor($dias_ces);
                if($dias_ces>360){
                    $dias_ces = 360;
                }                                         
            }else{
              $dias_ces= $dias_ces;
            }

          /*Eliminacion de novedades de dias de prima de servicios, vacaciones, cesantias*/
          //prima de servicios
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=443";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          //vacaciones
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=444";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          //cesantias
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=445";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          /*Registro de novedades de dias de prima de servicio, vacaciones y cesantias*/
          //
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_prima,$hoy,$empleado,$periodo,443,4)";                  
          $resultado = $mysqli->query($sql_dias);

          //vacaciones
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_vac,$hoy,$empleado,$periodo,444,4)";                  
          $resultado = $mysqli->query($sql_dias);

          //cesantias
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_ces,$hoy,$empleado,$periodo,445,4)";                  
          $resultado = $mysqli->query($sql_dias);

        //conceptos para la liquidacion final
          //averiguar que otros conceptos entran como base de liquidacon para prima
          //PRIMA DE SERVICIOS
          $sql_vlhe_pr="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=439";
          $res_hepr = $mysqli->query($sql_vlhe_pr);    
          $n_he = mysqli_num_rows($res_hepr);
          $vl_promedio_pr=0;
          $vl_actual_pr=0;

            if($n_he > 0){
              while($row_he = mysqli_fetch_row($res_hepr)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_pr= $vl_actual_pr+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_pr= $vl_promedio_pr+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_pr= ($vl_promedio_pr/$dias_prima)*30;

            //VACACIONES
          $sql_vlhe_vac="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=440";
          $res_hevac = $mysqli->query($sql_vlhe_vac);    
          $n_vac = mysqli_num_rows($res_hevac);
          $vl_promedio_vac=0;
          $vl_actual_vac=0;

            if($n_vac > 0){
              while($row_he = mysqli_fetch_row($res_hevac)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_vac= $vl_actual_vac+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_vac= $vl_promedio_vac+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_vac= ($vl_promedio_vac/$dias_vac)*30;

            //cesantias
          $sql_vlhe_ces="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=441";
          $res_hec = $mysqli->query($sql_vlhe_ces);    
          $n_he = mysqli_num_rows($res_hec);
          $vl_promedio_ces=0;
          $vl_actual_ces=0;

            if($n_he > 0){
              while($row_he = mysqli_fetch_row($res_hec)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_ces= $vl_actual_ces+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_ces= $vl_promedio_ces+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_ces= ($vl_promedio_ces/$dias_ces)*30;


        $sql_concep = "SELECT * FROM gn_concepto WHERE aplica_liquidacion_final=1";
            $res_con = $mysqli->query($sql_concep);    
            $n_con = mysqli_num_rows($res_con);
            
            if($n_con > 0){

              $restriccion= $vlr_salario_min*2;
              if($salario_tercero<=$restriccion){
                  $salario_base= $salario_tercero+$aux_trans;
              }else{
                  $salario_base= $salario_tercero;
              }
                    
              $salario_base_pr=$salario_base+$vl_promedio_pr+$vl_actual_pr;
              $salario_base_vac=$salario_tercero+$vl_promedio_vac+$vl_actual_vac;
              $salario_base_ces=$salario_base+$vl_promedio_ces+$vl_actual_ces;

              $vlr_prima=($salario_base_pr*$dias_prima)/360;
              $vlr_vac_ret=($salario_base_vac*$dias_trab)/720;
              $vlr_cesantias_pg=($salario_base_ces*$dias_ces)/360;
              $vlr_int_cesantias=($vlr_cesantias_pg*0.12*$dias_ces)/360;

              $vlr_prima =  round($vlr_prima, 2, PHP_ROUND_HALF_UP);
              $vlr_vac_ret =  round($vlr_vac_ret, 2, PHP_ROUND_HALF_UP);
              $vlr_cesantias_pg =  round($vlr_cesantias_pg, 2, PHP_ROUND_HALF_UP);
              $vlr_int_cesantias =  round($vlr_int_cesantias, 2, PHP_ROUND_HALF_UP);

              while($row_con = mysqli_fetch_row($res_con)){

                  $cod_con=$row_con[1];
                  $id_con=$row_con[0];
                  if($cod_con=='C12'){
                    //eliminar si esa novedad esta 
                    $sql_elm_nov1="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov1);
                    //regstra nuevamente
                    $sql_C12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_prima,$hoy,$empleado,$periodo,$id_con,4)";
                  //  echo ' registro novedad 1 '.$sql_C12;
                    $resultado = $mysqli->query($sql_C12);
                  }
                  if($cod_con=='C13'){
                    $sql_elm_nov2="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov2);
                    //registra nuevamente
                    $sql_C13 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_vac_ret,$hoy,$empleado,$periodo,$id_con,4)";
                   // echo ' registro novedad 2 '.$sql_C13;
                    $resultado = $mysqli->query($sql_C13);
                  }
                  if($cod_con=='C14'){
                    $sql_elm_nov3="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov3);
                    //registra nuevamente
                    $sql_C14 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_cesantias_pg,$hoy,$empleado,$periodo,$id_con,4)";
                    //echo ' registro novedad 3 '.$sql_C14;
                    $resultado = $mysqli->query($sql_C14);
                  }
                  if($cod_con=='C15'){
                    $sql_elm_nov4="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov4);
                    //registra nuevamente
                    $sql_C15 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_int_cesantias,$hoy,$empleado,$periodo,$id_con,4)";
                   // echo ' registro novedad 4 '.$sql_C15;
                    $resultado = $mysqli->query($sql_C15);
                  }
              }
            
            }
            
        }

    }else{
      //1. Buscar y/o registrar vinculacion retiro
    //consulta que me trae el id_tercero, nombre , fecha del ultimo ingreso y el id de la ultima vinculacion  y el valor del auxilio de transporte
    $sql_vin = "SELECT  e.id_unico, 
                     e.tercero, 
                     CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) as tercero, 
                        c.salarioactual,
                     (SELECT ingreso.fecha FROM gn_empleado e_ing 
                      LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                      WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                      fecha_ingreso,
                      (SELECT ingreso.id_unico FROM gn_empleado e_ing 
                       LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico  
                       WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                       id_ingreso, 
                        
                       (SELECT auxt FROM gn_parametros_liquidacion WHERE vigencia = $anno) as vlor_auxilio,
                       (SELECT salmin FROM gn_parametros_liquidacion WHERE vigencia = $anno) as vlor_auxilio
                        

                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                WHERE e.id_unico = $empleado ";
    $res = $mysqli->query($sql_vin);
    $row_vin = mysqli_fetch_row($res);

    $id_tercero=$row_vin[1];
    $nom_tercero=$row_vin[2];
    $salario_tercero=$row_vin[3];
    $fecha_ingreso=$row_vin[4];
    $id_ingreso=$row_vin[5];
    $aux_trans=$row_vin[6];
    $vlr_salario_min=$row_vin[7];

//con el id_ingreso buscamos si tiene o no  la vinculacion retiro
$sql_vin_ret = "SELECT retiro.* FROM gn_empleado e_retiro 
                LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_retiro.id_unico              
                WHERE e_retiro.id_unico = $empleado and retiro.estado=2 and retiro.vinculacionretiro=$id_ingreso
                order by retiro.fecha desc LIMIT 1 ";
    $res_ret = $mysqli->query($sql_vin_ret);
    $n_ret = mysqli_num_rows($res_ret);
    if($n_ret > 0){
    
      $row_ret = mysqli_fetch_row($res_ret);
      $id_retiro=$row_ret[0];
      $fecha_ret=$row_ret[3];
                
    }else{
      $id_retiro=0;
      $fecha_ret=$fecha_retiro;
      //registrar la vinculacion retiro
      $sql_insert_retiro = "INSERT INTO gn_vinculacion_retiro(fechaacto,fecha,empleado,tipovinculacion,estado,causaretiro,
      vinculacionretiro) VALUES($fecha_ret,$fecha_ret,$empleado,NULL,2,3,$id_ingreso)";
      $resul_retiro = $mysqli->query($sql_insert_retiro);

      $sql_update = "UPDATE gn_vinculacion_retiro set vinculacionretiro = $id_ingreso where id_unico = $id_ingreso";
              $resul_retiro = $mysqli->query($sql_update);

      $sql_update = "UPDATE gn_empleado set estado = 2  where id_unico = $empleado";
              $resul_retiro = $mysqli->query($sql_update);

    }
    //echo '  ingreso '.$fecha_ingreso.'  retiro  '.$fc_retiro;

 
    $fch_ing = explode('-', $fecha_ingreso);
    $a_ing = $fch_ing[0];
    $m_ing = $fch_ing[1];
    $d_ing = $fch_ing[2];
    
    $y_ing=$a_ing;
    $mont_ing=$m_ing;
    $cont_mes=0;
    while ($y_ing<=$a_ret){

       if($y_ing<$a_ret){
            while($y_ing<$a_ret){
              if($mont_ing<12){
                $cont_mes++;  
                $mont_ing++;
                
              }else{
                $y_ing++;     
              }      
            }
          }
    
        if($y_ing==$a_ret){
          $cont_mes=$cont_mes+$m_ret;
      
          $y_ing++;
        }     

    }
    $dias_ad=0;
    if($d_ret <30){
      $cont_mes--;
      $dias_ad=$d_ret;
    }    
  //echo '  meses '.$cont_mes;
 // $dias_trab = ($cont_mes*30)+$dias_ad;
  //echo '  dias '.$dias_trab;
  $dias_trab = (strtotime($fecha_ingreso)-strtotime($fc_retiro))/86400;
  $dias_trab = abs($dias_trab); 
  $dias_trab = floor($dias_trab);
  $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=7";
          $resultado = $mysqli->query($sql_elm_dias_trab);
  $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_trab,$hoy,$empleado,$periodo,7,1)";                  
          $resultado = $mysqli->query($sql_dias);

  $dias_prima=$dias_trab;
  $dias_vac=$dias_trab;
  $dias_ces=$dias_trab;

//registrar novedad dias trabajados 

          //1. Consultar los dias trabajados y pagos en la prima 
          /*
          $sql_dias_pr = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=2 and concepto=7 ";
            $res_pr = $mysqli->query($sql_dias_pr);
            $n_pr = mysqli_num_rows($res_pr);
            if($n_pr > 0)
            {
                 $row_pr = mysqli_fetch_row($res_pr);
                 $dias_prima= $dias_prima-$row_pr[0];
            }*/
            $sql_dias_pr = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empleado and p.tipoprocesonomina=2 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
            $res_pr = $mysqli->query($sql_dias_pr);
            $n_pr = mysqli_num_rows($res_pr);
            if($n_pr > 0)
            {
                $row_pr = mysqli_fetch_row($res_pr);
                $ffin_prima = explode('-', $row_pr[0]);
                if($ffin_prima[1]==12){
                    $ms_inipr=1;
                    $fini_prim= $a_ret.'-01-01';
                }else if($ffin_prima[1]==06){
                    $ms_inipr=7;
                    $fini_prim= $a_ret.'-07-01';
                }
                $cont=0;
                for($x=$ms_inipr;$x<=($m_ret-1);$x++){
                   $cont++;
                }
                $dias_prima=($cont*30)+$d_ret;
            }
            //2. Consultar los dias pagados de vacaciones 
            /*
            $sql_dias_vac = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=7 and concepto=7 ";
            $res_vac = $mysqli->query($sql_dias_vac);
            $n_vac = mysqli_num_rows($res_vac);
            if($n_vac > 0)
            {
                 $row_vac = mysqli_fetch_row($res_vac);
                 $dias_vac= $dias_vac-$row_vac[0];
            }*/
            $sql_dias_vac = "SELECT fechafin from gn_vacaciones where fechaInicio >='$fecha_ingreso' and empleado = $empleado order by fechafin desc ";
            $res_v = $mysqli->query($sql_dias_vac);
            $n_vac = mysqli_num_rows($res_v);
            if($n_vac > 0)
            {
                $row_v = mysqli_fetch_row($res_v);
                $fult_vac = $row_v[0];                                         
            }else{
                $fult_vac = $fecha_ingreso;
            }
            if($fult_vac==$fecha_ingreso){
                $dias_vac= $dias_vac;
            }else{
                $dias_vac = (strtotime($fult_vac)-strtotime($fc_retiro))/86400;
                $dias_vac = abs($dias_vac); 
                $dias_vac = floor($dias_vac);
            }
            $dias_vac = ($dias_vac * 15)/360;
            $dias_vac = round($dias_vac);
            //3. Consultar los dias pagados de vacaciones 
            /*
            $sql_dias_ces = "SELECT sum(n.valor) vlr FROM gn_novedad n
                          left join gn_periodo p on p.id_unico= n.periodo
                          where empleado=$empleado and p.tipoprocesonomina=11 and concepto=7 ";
            $res_ces = $mysqli->query($sql_dias_ces);
            $n_ces = mysqli_num_rows($res_ces);
            if($n_ces > 0)
            {
                 $row_ces = mysqli_fetch_row($res_ces);
                 $dias_ces= $dias_ces-$row_ces[0];
            }
            */
            $sql_dias_ces = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empl_sim and p.tipoprocesonomina=11 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
            $res_c = $mysqli->query($sql_dias_ces);
            $n_c = mysqli_num_rows($res_c);
            if($n_c > 0)
            {
                $row_c = mysqli_fetch_row($res_c);
                $ffin_ces = explode('-', $row_c[0]);
                if($ffin_ces[1]==12){
                    $fini_ces= ($ffin_ces[0]+1).'-01-01';
                }else {
                    $fini_ces= $ffin_ces[0].'-01-01';
                }
                $dias_ces = (strtotime($fini_ces)-strtotime($fc_retiro))/86400;
                $dias_ces = abs($dias_ces); 
                $dias_ces = floor($dias_ces);
                if($dias_ces>360){
                    $dias_ces = 360;
                }                                         
            }else{
              $dias_ces= $dias_ces;
            }

          /*Eliminacion de novedades de dias de prima de servicios, vacaciones, cesantias*/
          //prima de servicios
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=443";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          //vacaciones
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=444";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          //cesantias
          $sql_elm_dias_trab="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=445";
          $resultado = $mysqli->query($sql_elm_dias_trab);

          /*Registro de novedades de dias de prima de servicio, vacaciones y cesantias*/
          //
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_prima,$hoy,$empleado,$periodo,443,4)";                  
          $resultado = $mysqli->query($sql_dias);

          //vacaciones
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_vac,$hoy,$empleado,$periodo,444,4)";                  
          $resultado = $mysqli->query($sql_dias);

          //cesantias
          $sql_dias = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_ces,$hoy,$empleado,$periodo,445,4)";                  
          $resultado = $mysqli->query($sql_dias);

//averiguar que otros conceptos entran como base de liquidacon para prima
          //PRIMA DE SERVICIOS
          $sql_vlhe_pr="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=439";
          $res_hepr = $mysqli->query($sql_vlhe_pr);    
          $n_he = mysqli_num_rows($res_hepr);
          $vl_promedio_pr=0;
          $vl_actual_pr=0;

            if($n_he > 0){
              while($row_he = mysqli_fetch_row($res_hepr)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_pr= $vl_actual_pr+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_pr= $vl_promedio_pr+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_pr= ($vl_promedio_pr/$dias_prima)*30;

            //VACACIONES
          $sql_vlhe_vac="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=440";
          $res_hevac = $mysqli->query($sql_vlhe_vac);    
          $n_vac = mysqli_num_rows($res_hevac);
          $vl_promedio_vac=0;
          $vl_actual_vac=0;

            if($n_vac > 0){
              while($row_he = mysqli_fetch_row($res_hevac)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_vac= $vl_actual_vac+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_vac= $vl_promedio_vac+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_vac= ($vl_promedio_vac/$dias_vac)*30;

            //cesantias
          $sql_vlhe_ces="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=441";
          $res_hec = $mysqli->query($sql_vlhe_ces);    
          $n_he = mysqli_num_rows($res_hec);
          $vl_promedio_ces=0;
          $vl_actual_ces=0;

            if($n_he > 0){
              while($row_he = mysqli_fetch_row($res_hec)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_ces= $vl_actual_ces+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empleado  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_ces= $vl_promedio_ces+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_ces= ($vl_promedio_ces/$dias_ces)*30;


//conceptos para la liquidacion final

$sql_concep = "SELECT * FROM gn_concepto WHERE aplica_liquidacion_final=1";
    $res_con = $mysqli->query($sql_concep);    
    $n_con = mysqli_num_rows($res_con);
    
    if($n_con > 0){

      $restriccion= $vlr_salario_min*2;
      if($salario_tercero<=$restriccion){
          $salario_base= $salario_tercero+$aux_trans;
      }else{
          $salario_base= $salario_tercero;
      }
          $salario_base_pr=$salario_base+$vl_promedio_pr+$vl_actual_pr;
          $salario_base_vac=$salario_tercero+$vl_promedio_vac+$vl_actual_vac;
          $salario_base_ces=$salario_base+$vl_promedio_ces+$vl_actual_ces;

          $vlr_prima=($salario_base_pr*$dias_prima)/360;
          $vlr_vac_ret=($salario_base_vac*$dias_trab)/720;
          $vlr_cesantias_pg=($salario_base_ces*$dias_ces)/360;
          $vlr_int_cesantias=($vlr_cesantias_pg*0.12*$dias_ces)/360;

          $vlr_prima =  round($vlr_prima, 2, PHP_ROUND_HALF_UP);
          $vlr_vac_ret =  round($vlr_vac_ret, 2, PHP_ROUND_HALF_UP);
          $vlr_cesantias_pg =  round($vlr_cesantias_pg, 2, PHP_ROUND_HALF_UP);
          $vlr_int_cesantias =  round($vlr_int_cesantias, 2, PHP_ROUND_HALF_UP);


      while($row_con = mysqli_fetch_row($res_con)){

          $cod_con=$row_con[1];
          $id_con=$row_con[0];
           if($cod_con=='C12'){
                    //eliminar si esa novedad esta 
                    $sql_elm_nov1="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov1);
                    //regstra nuevamente
                    $sql_C12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_prima,$hoy,$empleado,$periodo,$id_con,4)";
                  //  echo ' registro novedad 1 '.$sql_C12;
                    $resultado = $mysqli->query($sql_C12);
                  }
                  if($cod_con=='C13'){
                    $sql_elm_nov2="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov2);
                    //registra nuevamente
                    $sql_C13 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_vac_ret,$hoy,$empleado,$periodo,$id_con,4)";
                   // echo ' registro novedad 2 '.$sql_C13;
                    $resultado = $mysqli->query($sql_C13);
                  }
                  if($cod_con=='C14'){
                    $sql_elm_nov3="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov3);
                    //registra nuevamente
                    $sql_C14 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_cesantias_pg,$hoy,$empleado,$periodo,$id_con,4)";
                    //echo ' registro novedad 3 '.$sql_C14;
                    $resultado = $mysqli->query($sql_C14);
                  }
                  if($cod_con=='C15'){
                    $sql_elm_nov4="DELETE FROM gn_novedad where empleado=$empleado and periodo=$periodo and concepto=$id_con";
                    $resultado = $mysqli->query($sql_elm_nov4);
                    //registra nuevamente
                    $sql_C15 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_int_cesantias,$hoy,$empleado,$periodo,$id_con,4)";
                   // echo ' registro novedad 4 '.$sql_C15;
                    $resultado = $mysqli->query($sql_C15);
                  }
      }
    
    }
    }
    


  
    

?>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.js"></script>
<!-- Script que redirige a la página inicial de Tipo Elemento. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../liquidar_GN_LIQUIDACION_FINAL.php?idP=<?php echo $periodo?>';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>