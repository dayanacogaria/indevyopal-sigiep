<?php
    require_once '../Conexion/conexion.php';
    require '../funciones/funciones_formulador.php';
    session_start();

    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];

    /*if(!empty($_REQUEST['id_emp'])){

        $empleado = $_REQUEST['id_emp'];
    }

    if(!empty($_REQUEST['id_per'])){

        $periodo = $_REQUEST['id_per'];
    }

    if(!empty($_POST['sltEmpleado'])){

        $empleado  = $_POST['sltEmpleado'];  
    }

    if(!empty($_POST['sltPeriodo'])){

        $periodo   = $_POST['sltPeriodo'];  
    }*/
    
    $periodo = 9;
    $empleado = 2;
    #$diasT     = ''.$mysqli->real_escape_string(''.$_POST['txtdiasT'].'').'';    
    $DiaTRA = "SELECT dias_nomina,fechainicio,fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
    $dias = $mysqli->query($DiaTRA);
    $DTR = mysqli_fetch_row($dias);
    
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
    
    $sql2 = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $anno";
    $res = $mysqli->query($sql2);
    $rowP = mysqli_fetch_row($res);

    $pid = $rowP[0]; // id de los parametros
    $pvi = $rowP[1]; // vigencia 
    $psm = $rowP[2]; // salario minimo de la vigencia
    $pat = $rowP[3]; // auxilio de transporte de la vigencia 
    $ppa = $rowP[4]; // prima de alimentracion
    $ppm = $rowP[5]; // prima de movilidad
    $pse = $rowP[6]; // aporte salud empleado
    $psp = $rowP[7]; // aporte salud empresa
    $ppe = $rowP[8]; // aporte pension empleado
    $ppp = $rowP[9]; // aporte pension empresa
    $pfs = $rowP[10]; // aporte fondo de solidaridad
    $per = $rowP[11]; // Encento de retencion
    $pcc = $rowP[12];// aporte caja de compensacion
    $psen = $rowP[13];// aporte SENA
    $pic = $rowP[14];// aporte ICBF
    $pes = $rowP[15];// aporte ESAP
    $pmi = $rowP[16];// aporte ministrerio
    $puv = $rowP[17];// valor UVT
    $pta = $rowP[18];// total alimetnacion
    $pad = $rowP[19];// total alimentacion docente
    
    $sql1  = "SELECT * FROM gn_novedad WHERE aplicabilidad= 1 AND empleado=2 OR  aplicabilidad= 1 AND empleado IS NULL ORDER BY id_unico"; //consulta para saber que novedades son para siempre para todos los empleados
    $res1  = $mysqli->query($sql1);
    $z     = mysqli_num_rows($res1);

    $sql2 = "SELECT * FROM gn_novedad WHERE aplicabilidad= 2 AND empleado=2  OR aplicabilidad= 2 AND empleado IS NULL"; //consulta para saber que novedades son para el periodo para todos los empleados
    $res2  = $mysqli->query($sql2);
    $y     = mysqli_num_rows($res2);

    
    if(empty($empleado)|| $empleado==2){
    
        $retiro = "SELECT fechainicio, fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
        $retEmp = $mysqli->query($retiro);
        $retE = mysqli_fetch_row($retEmp);


        $sql3 = "SELECT DISTINCT    e.id_unico, 
                                        e.tercero, 
                                        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                                        tc.categoria, 
                                        c.id_unico, 
                                        c.nombre, 
                                        c.salarioactual 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                WHERE e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                OR e.id_unico != 2 AND vr.estado = 2 AND vr.fecha BETWEEN $DTR[1] AND $DTR[2] ORDER BY `e`.`id_unico` ASC"; 

    }else{
        $sql3 = "SELECT  e.id_unico, 
                        e.tercero, 
                        CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                        tc.categoria, 
                        c.id_unico, 
                        c.nombre, 
                        c.salarioactual

                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                WHERE e.id_unico = $empleado"; 

    }
    
    $resulta = $mysqli->query($sql3);
    
    while($EMP = mysqli_fetch_row($resulta)){
        
        $sql4 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND aplicabilidad = 1 "; //Borra las novedades del empleado en el periodo de apli 1
        $resultado = $mysqli->query($sql4);
        
        $sql5 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND aplicabilidad = 3 "; //Borra las novedades del empleado en el periodo de apli 3
        $resultado = $mysqli->query($sql5);
        
        $sql6 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND aplicabilidad = 2 "; //Borra las novedades del empleado en el periodo de apli 2
        $resultado = $mysqli->query($sql6);
        
        #consulta si el empleado posee uno o varios créditos
        $sql7 = "SELECT valorcuota, valorcredito, numerocuotas, concepto FROM gn_credito WHERE empleado = '$EMP[0]'";
        $res7 = $mysqli->query($sql7);
        $nres7 = mysqli_num_rows($res7);
        
        #Valida si empleado posse uno o mas creditos
        if($nres7 > 0){
            
            while($CRE = mysqli_fetch_row($res7)){
                
                #suma el valor de los pagos realizados del crédito
                $sql8 = "SELECT SUM(valor) FROM gn_novedad WHERE concepto = '$CRE[3]' AND empleado = '$EMP[0]' ";
                $res8 = $mysqli->query($sql8);
                $pago = mysqli_fetch_row($res8);
                
                #Valida si el valor total del crédito es mayor que la suma de los pagos realizados
                if($CRE[1] > $pago[0]){
                    
                    #inserta el valor de la couta de crédito en la tabla gn_novedad
                    $cuota = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($CRE[0],$hoy,$EMP[0],$periodo,$CRE[3],3)";
                    $resultado = $mysqli->query($cuota);
                }
            }
        }
        
        #consulta si el empleado posee vacaciones en fechas dentro de la fecha inicio y la fecha fin del periodo
        $sql9 = "SELECT fechainiciodisfrute, fechafindisfrute, dias_hab FROM gn_vacaciones WHERE empleado = '$EMP[0]' AND fechainiciodisfrute >= '$DTR[1]' "
                . "AND fechafindisfrute <= '$DTR[2]'";
        $res9 = $mysqli->query($sql9);
        $nres9 = mysqli_num_rows($res9);
        
        $IBCVAC = 0;
        
        #Valida si el empleado posee vacaciones
        if($nres9 > 0){
            
            $DV = mysqli_fetch_row($res9);
            
            #inserta la novedad de días de vacaciones del empleado en el periodo
            $dvac = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($DV[2],$hoy,$EMP[0],$periodo,27,3)";
            $resutlado = $mysqli->query($dvac);
           
            #consulta si el empleado posee una novedad de días trabajados en el periodo
            $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
            $diasT = $mysqli->query($dias_trab);
            $ndias = mysqli_num_rows($diasT);
            
            #valida si el empleado posee la novedad de días trabajados en el periodo
            if($ndias > 0){
                
                $DT = mysqli_fetch_row($diasT);
                $TDT = $DT[0]- $DV[2];
                
                $sql10 = "UPDATE gn_novedad SET valor = '$TDT' WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 7 ";
                $resultado = $mysqli->query($sql10);
            }else{
                $TDT = $DTR[0] - $DV[2];
                $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TDT,$hoy,$EMP[0],$periodo,7,1)";
                $resultado = $mysqli->query($sql10);
            }
            
            #consulta la ecuacion del concepto de ibc de vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '416'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array($EMP[0],$periodo,"$EMP[0],$periodo");
            $IBCVAC = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCVAC,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del empleado en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '396'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $saludVACE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludVACE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del empleado en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '397'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $pensionVACE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionVACE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '398'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $saludVACP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludVACP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '399'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $pensionVACP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionVACP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '401'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $icbfVAC = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfVAC,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '402'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $esapVACE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapVACE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '403'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $minedVACE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedVACE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '404'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $senaVACE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaVACE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte a la caja de compensacion por parte del patrono en vacaciones
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '405'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $ccfVAC = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfVAC,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
        }//// FINALIZA LA VALIDACIONES DE VACACIONES
        
        #consulta si el empleado posee incapacidad general en fechas dentro de la fecha inicio y la fecha fin del periodo
        $sql12 = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '138'";
        $res12 = $mysqli->query($sql12);
        $nres12 = mysqli_num_rows($res12);
        
        $IBCIGE = 0;
        
        #Valida si el empleado posee incapacidad general
        if($nres12 > 0){
            
            $DIG = mysqli_fetch_row($res12);
            
            #consulta si el empleado posee una novedad de días trabajados en el periodo
            $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
            $diasT = $mysqli->query($dias_trab);
            $ndias = mysqli_num_rows($diasT);
            
            #valida si el empleado posee la novedad de días trabajados en el periodo
            if($ndias > 0){
                
                $DT = mysqli_fetch_row($diasT);
                $TDT = $DT[0]- $DIG[0];
                
                $sql10 = "UPDATE gn_novedad SET valor = '$TDT' WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 7 ";
                $resultado = $mysqli->query($sql10);
            }else{
                $TDT = $DTR[0] - $DIG[0];
                $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TDT,$hoy,$EMP[0],$periodo,7,1)";
                $resultado = $mysqli->query($sql10);
            }
            
            #consulta la ecuacion del concepto de ibc de incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '418'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array($EMP[0],$periodo,"$EMP[0],$periodo");
            $IBCIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del empleado en  incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '376'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $saludIGEE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludIGEE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del empleado en incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '377'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $pensionIGEE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionIGEE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del patrono en incapacidaad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '378'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $saludIGEP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludIGEP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del patrono en incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '379'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $pensionIGEP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionIGEP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '381'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $icbfIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '382'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $esapIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en incapacidad genral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '383'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $minedIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en incapacidad genral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '384'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $senaIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte a la caja de compensacion por parte del patrono en incapacidad general
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '385'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $ccfIGE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfIGE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
        }/// FINALIZA LA VALIDACION DE INCAPACIDA GENERAL
        
        #consulta si el empleado posee incapacidad laboral en fechas dentro de la fecha inicio y la fecha fin del periodo
        $sql13 = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '142'";
        $res13 = $mysqli->query($sql13);
        $nres13 = mysqli_num_rows($res13);
        
        $IBCIRL = 0;
        
        #Valida si el empleado posee incapacidad laboral
        if($nres12 > 0){
            
            $DIL = mysqli_fetch_row($res12);
            
            #consulta si el empleado posee una novedad de días trabajados en el periodo
            $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
            $diasT = $mysqli->query($dias_trab);
            $ndias = mysqli_num_rows($diasT);
            
            #valida si el empleado posee la novedad de días trabajados en el periodo
            if($ndias > 0){
                
                $DT = mysqli_fetch_row($diasT);
                $TDT = $DT[0]- $DIL[0];
                
                $sql10 = "UPDATE gn_novedad SET valor = '$TDT' WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 7 ";
                $resultado = $mysqli->query($sql10);
            }else{
                $TDT = $DTR[0] - $DIL[2];
                $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TDT,$hoy,$EMP[0],$periodo,7,1)";
                $resultado = $mysqli->query($sql10);
            }
            
            #consulta la ecuacion del concepto de ibc de incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '419'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array($EMP[0],$periodo,"$EMP[0],$periodo");
            $IBCIRL = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del empleado en  incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '386'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $saludIRLE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludIRLE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del empleado en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '387'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $pensionIRLE = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionIRLE,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de salud por parte del patrono en incapacidaad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '388'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $saludIRLP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludIRLP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de pension por parte del patrono en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '389'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
            $pensionIRLP = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionIRLP,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en incapacidad laboral 
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '391'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $icbfIRL = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '392'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $esapIRL = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '393'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $minedIRL = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '394'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $senaIRL = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte a la caja de compensacion por parte del patrono en incapacidad laboral
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '385'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);
            
            $a = array("$EMP[0],$periodo",$anno);
            $ccfIRl = evalute_expression($F[0], $a);
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfIRL,$hoy,$EMP[0],$periodo,$F[1],3)";
            $resultado = $mysqli->query($sql11);
            
        }/// FINALIZA LA VALIDACION DE INCAPACIDA LABORAL
        
        #consulta si el empleado posee una novedad de días trabajados en el periodo
        $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
        $diasT = $mysqli->query($dias_trab);
        $ndias = mysqli_num_rows($diasT);
            
        #valida si el empleado posee la novedad de días trabajados en el periodo
        if($ndias > 0){
                
            $DT = mysqli_fetch_row($diasT);
        
        }else{
            
            $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($DTR[0],$hoy,$EMP[0],$periodo,7,1)";
            $resultado = $mysqli->query($sql10);
        }
        
        #consulta la ecuacion del concepto de ibc 
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '78'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array($EMP[0],"$EMP[0],$periodo","$EMP[0],$periodo",$periodo);
        $IBC = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBC,$hoy,$EMP[0],$periodo,$F[1],3)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del concepto de sueldo 
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '2'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array($EMP[0],"$EMP[0],$periodo",$periodo);
        $sueldo = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($sueldo,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de salud por parte del empleado de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '366'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $saludNME = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludNME,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de pension por parte del empleado de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '367'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $pensionNME = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionNME,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de salud por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '368'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
        $saludNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de pension por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '369'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno,$anno,"$EMP[0],$periodo");
        $pensionNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de arl por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '370'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$EMP[0]);
        $arlNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($arlNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de icbf por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '371'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $icbfNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de esap por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '372'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $esapNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de sena por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '373'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $senaNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de ministerio de educ por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '371'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $minedNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '375'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo",$anno);
        $ccfNMP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfNMP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        ////CALCULO DE VALORES TOTALES//////////
        
        $TIBC = $IBC + $IBCVAC + $IBCIGE + $IBCIRL;
        
        if($TIBC >= ($psm * 4)){
            
            if($IBCVAC > 0){
                
                #consulta la ecuacion del aporte al fondo de solidaridad pensional en vacaciones
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '428'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSVAC1 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSVAC1,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                 #consulta la ecuacion del aporte al fondo de solidaridad pensional en vacaciones
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '429'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSVAC2 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSVAC2,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                $FSVAC = round(($FSVAC1 + $FSVAC2) /10)*10;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSVAC,$hoy,$EMP[0],$periodo,430,1)";
                $resultado = $mysqli->query($sql11);
            }
            
            if($IBCIGE > 0){
                
                #consulta la ecuacion del aporte al fondo de solidaridad pensional en incapacidad general
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '422'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSIGE1 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIGE1,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                 #consulta la ecuacion del aporte al fondo de solidaridad pensional en incapacidad genral
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '423'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSIGE2 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIGE2,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                $FSIGE = round(($FSIGE1 + $FSIGE2) /10)*10;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIGE,$hoy,$EMP[0],$periodo,424,1)";
                $resultado = $mysqli->query($sql11);
            }
            
            if($IBCIRL > 0){
                
                #consulta la ecuacion del aporte al fondo de solidaridad pensional en incapacidad laboral
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '425'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSIRL1 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIRL1,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                 #consulta la ecuacion del aporte al fondo de solidaridad pensional en incapacidad laboral
                $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '426'";
                $fo = $mysqli->query($for);
                $F = mysqli_fetch_row($fo);

                $a = array("$EMP[0],$periodo",$anno);
                $FSIRL2 = evalute_expression($F[0], $a);

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIRL2,$hoy,$EMP[0],$periodo,$F[1],1)";
                $resultado = $mysqli->query($sql11);
                
                $FSIRL = round(($FSIRL1 + $FSIRL2) /10)*10;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSIRL,$hoy,$EMP[0],$periodo,427,1)";
                $resultado = $mysqli->query($sql11);
            }
            
                
            #consulta la ecuacion del aporte al fondo de solidaridad pensional de los días trabajados
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '361'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);

            $a = array("$EMP[0],$periodo",$anno);
            $FSNM1 = evalute_expression($F[0], $a);

            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSNM1,$hoy,$EMP[0],$periodo,$F[1],1)";
            $resultado = $mysqli->query($sql11);
                
            #consulta la ecuacion del aporte al fondo de solidaridad pensional de los días trabajados 
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '362'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);

            $a = array("$EMP[0],$periodo",$anno);
            $FSNM2 = evalute_expression($F[0], $a);

            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSNM2,$hoy,$EMP[0],$periodo,$F[1],1)";
            $resultado = $mysqli->query($sql11);
                
            $FSNM = round(($FSNM1 + $FSNM2) /10)*10;
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSNM,$hoy,$EMP[0],$periodo,421,1)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del aporte al fondo de solidaridad pensional 
            $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '81'";
            $fo = $mysqli->query($for);
            $F = mysqli_fetch_row($fo);

            $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
            $TFS = evalute_expression($F[0], $a);

            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TFS,$hoy,$EMP[0],$periodo,$F[1],1)";
            $resultado = $mysqli->query($sql11);
        }
        
        #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del empleado
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '80'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
        $saludE = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludE,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion de la suma total del aporte al fondo de pension por parte del empleado
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '84'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
        $pensionE = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionE,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del patrono
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '82'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
        $saludP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion de la suma total del aporte al fondo de pensión por parte del patrono
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '83'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
        $pensionP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion de la suma total del aporte al fondo de pensión por parte del patrono
        $for = "SELECT ecuacion,concepto FROM gn_formula_concepto WHERE concepto = '83'";
        $fo = $mysqli->query($for);
        $F = mysqli_fetch_row($fo);
          
        $a = array("$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo","$EMP[0],$periodo");
        $pensionP = evalute_expression($F[0], $a);
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionP,$hoy,$EMP[0],$periodo,$F[1],1)";
        $resultado = $mysqli->query($sql11);
        
        
        $sql = "SELECT * FROM gn_novedad WHERE aplicabilidad= 3 AND periodo = 1 AND empleado = '$EMP[0]'"; //consulta para saber que novedades son para siempre para un empleado
        $res  = $mysqli->query($sql);
        $w     = mysqli_num_rows($res);
        
        
    }
        
    
?>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/md5.pack.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" >
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    </head>
    <body>
    </body>

<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
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
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
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
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($resultado == 1 ){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');      
                    //window.location='../registrar_GN_VACACIONES.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
                    window.history.go(-1);
                });
            </script>
<?php }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                $("#myModal2").modal('hide');      
                    //window.location='../registrar_GN_ACCIDENTE.php?id=<?php echo md5($id);?>';
                    window.history.go(-1);
                });
            </script>
<?php } ?>