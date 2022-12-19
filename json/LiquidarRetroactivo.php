<?php
    require_once '../Conexion/conexion.php';
    require '../funciones/funciones_formulador.php';
    session_start();

    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];

    if(!empty($_REQUEST['id_emp'])){

        $empleado = $_REQUEST['id_emp'];
    }

    if(!empty($_REQUEST['id_per'])){

        $periodo = $_REQUEST['id_per'];
    }

    if(!empty($_REQUEST['txtMeses'])){

        $meses = $_REQUEST['txtMeses'];
    }

    if(!empty($_POST['sltEmpleado'])){

        $empleado  = $_POST['sltEmpleado'];  
    }

    if(!empty($_POST['sltPeriodo'])){

        $periodo   = $_POST['sltPeriodo'];  
    }

    if(!empty($_POST['txtMeses'])){

        $meses = $_POST['txtMeses'];
    }

$Mper[0] = $periodo;
$PRetro[0] = $periodo;

    /*
    $periodo = 9;
    $empleado = 2;
    */
    #$diasT     = ''.$mysqli->real_escape_string(''.$_POST['txtdiasT'].'').'';    
    $DiaTRA = "SELECT dias_nomina,fechainicio,fechafin FROM gn_periodo WHERE id_unico = '$periodo'";
    $dias = $mysqli->query($DiaTRA);
    $DTR = mysqli_fetch_row($dias);

    $FF = explode("-",$DTR[2]);
    $DP = $FF[2];
    
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

    $pid        = $rowP[0]; // id de los parametros
    $pvi        = $rowP[1]; // vigencia 
    $psm        = $rowP[2]; // salario minimo de la vigencia
    $pat        = $rowP[3]; // auxilio de transporte de la vigencia 
    $ppa        = $rowP[4]; // prima de alimentacion
    $ppm        = $rowP[5]; // prima de movilidad
    $pse        = $rowP[6]; // aporte salud empleado
    $psp        = $rowP[7]; // aporte salud empresa
    $ppe        = $rowP[8]; // aporte pension empleado
    $ppp        = $rowP[9]; // aporte pension empresa
    $pfs        = $rowP[10]; // aporte fondo de solidaridad
    $per        = $rowP[11]; // Encento de retencion
    $pcc        = $rowP[12];// aporte caja de compensacion
    $psen       = $rowP[13];// aporte SENA
    $pic        = $rowP[14];// aporte ICBF
    $pes        = $rowP[15];// aporte ESAP
    $pmi        = $rowP[16];// aporte ministrerio
    $puv        = $rowP[17];// valor UVT
    $pta        = $rowP[18];// total alimentacion
    $pad        = $rowP[19];// total alimentacion docente
    $pie        = $rowP[20];// porcentaje de incapacidad
    $exc        = $rowP[21];// excento de parafiscales
    $recno      = $rowP[22];//recargo nocturno
    $recdom     = $rowP[23];// recargo dominical
    $hextdo     = $rowP[24];//hora extra diurna ordinaria
    $hextddf    = $rowP[25];// hora extra diurna dominical
    $hextno     = $rowP[26];//hora extra nocturna ordinaria
    $hextndf    = $rowP[27];//hora extra nocturna dominical
    $redondeo   = $rowP[28];//factor de redondeo de los descuentos y devengos
    $Tprovi     = $rowP[29];//tipo de entidad: pública o privada
    $PE = 12;
    
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


        echo $sql3 = "SELECT DISTINCT    e.id_unico, 
                                        e.tercero, 
                                        CONCAT_WS(' ', t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                                        tc.categoria, 
                                        c.id_unico, 
                                        c.nombre, 
                                        c.salarioactual,
                                        e.unidadejecutora,
                                        cr.valor,
                                        c.salarioanterior 
                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                WHERE e.id_unico != 2 AND e.unidadejecutora !=4 AND vr.estado=1  AND vr.vinculacionretiro IS NULL 
                ORDER BY `e`.`id_unico` ASC"; 

    }else{
        $sql3 = "SELECT  e.id_unico, 
                        e.tercero, 
                        CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ), 
                        tc.categoria, 
                        c.id_unico, 
                        c.nombre, 
                        c.salarioactual,
                        e.unidadejecutora,
                        cr.valor,
                        c.salarioanterior

                FROM gn_empleado e 
                LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                WHERE e.id_unico = $empleado"; 

    }
    
    $resulta = $mysqli->query($sql3);

    #consulta si existe un periodo con las mismas fechas con proceso de nomina mensual 
    echo $PANTER = "SELECT id_unico FROM gn_periodo WHERE fechainicio <= '$DTR[1]' AND fechafin >= $DTR[2] AND tipoprocesonomina = '1' AND parametrizacionanno = '$anno'";
    $PerAnt = $mysqli->query($PANTER);
    $PA = mysqli_fetch_row($PerAnt);

    $NPerio = "SELECT * FROM gn_periodo 
                    WHERE id_unico = '$periodo'
                    ORDER BY id_unico DESC";
                    
        $perNu = $mysqli->query($NPerio);
        $nper = mysqli_num_rows($perNu);
    
    #recorre todos los periodos encontrados
    //while($PRetro = mysqli_fetch_row($perNu)){
        #cuenta cuantos periodos existen de nomina mensual del periodo hacia atras
        /*$NPerio = "SELECT DISTINCT id_unico,codigointerno,fechainicio,fechafin,dias_nomina FROM gn_periodo 
                    WHERE id_unico <= '$PA[0]' AND tipoprocesonomina = '1' AND parametrizacionanno = '$anno'
                    ORDER BY id_unico DESC";*/

        
        #inserta los periodos encontrados con las mismas fechas y el mismo codigo interno pero con proceso retroactivo
        if($nper > 1){
            /*$sqlPer = "INSERT INTO gn_periodo(codigointerno,fechainicio,fechafin,acumulable,parametrizacionanno,tipoprocesonomina,liquidado,dias_nomina)
                                VALUE('$PRetro[1]','$PRetro[2]','$PRetro[3]',2,$anno,12,0,$DTR[0])";

            $resultado = $mysqli->query($sqlPer);*/
        }
        

        /*$Mperiodo  = "SELECT MAX(id_unico) FROM gn_periodo WHERE tipoprocesonomina = '12'" ;
        $MaP = $mysqli->query($Mperiodo);
        $Mper = mysqli_fetch_row($MaP);*/

        $Mper[0] = $periodo;

        while($EMP = mysqli_fetch_row($resulta)){
           
            $sql4 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = ' $Mper[0]' AND aplicabilidad = 1 "; //Borra las novedades del empleado en el periodo de apli 1
            $resultado = $mysqli->query($sql4);
            
            $sql5 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = ' $Mper[0]' AND aplicabilidad = 3 "; //Borra las novedades del empleado en el periodo de apli 3
            $resultado = $mysqli->query($sql5);
            
            $sql6 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = ' $Mper[0]' AND aplicabilidad = 2 "; //Borra las novedades del empleado en el periodo de apli 2
            $resultado = $mysqli->query($sql6);
            
            //consulta para saber que novedades son al periodo para todos los  empleados
            $sql21 = "SELECT * FROM gn_novedad WHERE aplicabilidad= 2 AND periodo = '$Mper[0]' AND empleado = 2 "; 
            $res21  = $mysqli->query($sql21);
            $z     = mysqli_num_rows($res21); 
            
            if($z > 0){
                
                while($APLI2 = mysqli_fetch_row($res21)){
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($APLI2[1],$hoy,$EMP[0], $Mper[0],$APLI2[5],3)";
                    $resultado = $mysqli->query($sql11);
                }
            }

            #consulta si el empleado es pensionado
            $pensionado = "SELECT empleado FROM gn_pensionado WHERE empleado = '$EMP[0]'";
            $pensio = $mysqli->query($pensionado);
            $npens = mysqli_fetch_row($pensio);

            if(empty($npens[0]) || $npens[0] == " "){
                $pens = 0;
            }else{
                $pens = 1;
            }

            #valida si el salario de la categoria varia entre el salario actual y el salario anterior
            if($EMP[6] != $EMP[9]){

                $salEmp = "SELECT c.salarioactual FROM gn_categoria c
                LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico
                LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico
                WHERE e.id_unico = '$EMP[0]'";

                $SEM = $mysqli->query($salEmp);
                $SE = mysqli_fetch_row($SEM); 
                
                #consulta si el empleado posee una novedad de días trabajados en el periodo
                $dias_trab = "SELECT SUM(n.valor) FROM gn_novedad n 
                            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico  
                            WHERE n.empleado = '$EMP[0]' AND n.concepto = '7' AND p.parametrizacionanno = '$anno' AND p.tipoprocesonomina = '1'";
                $diasT = $mysqli->query($dias_trab);
                $ndias = mysqli_num_rows($diasT);

                #valida si el empleado posee la novedad de días trabajados en el periodo
                if($ndias > 0){
                        
                    $DT = mysqli_fetch_row($diasT);
                
                }else{

                    if($EMP[7] == 1 || $EMP[7] == 2){
                        if($SE[0] < 1){
                            $DTE = 0;
                        }else{
                            $DTE = $DTR[0];
                        }
                    }else{
                        $DTE = $DTR[0];
                    }
                    
                    #$sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($DTE,$hoy,$EMP[0],$periodo,7,1)";
                    #$resultado = $mysqli->query($sql10);

                    #dias de nomina
                    $DT[0] = $DTR[0];
                }

                #consulta la ecuacion del concepto de ibc 
                $aumIBC = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado = '$EMP[0]' AND n.periodo = ' $Mper[0]' AND c.acum_ibc = '1' ";

                $AIBC = $mysqli->query($aumIBC);
                $nacum = mysqli_fetch_row($AIBC);

                if(empty($nacum[0]) || $nacum[0] == ""  ){
                $nacum[0] = 0;
                }

                //$IBC = ($SE[0] - $EMP[9]) * $DT[0]/$DTR[0];
                $IBC = ($SE[0] - $EMP[9]) * $meses;
                $IBC = $IBC + $nacum[0];

                if($EMP[7] == 1 || $EMP[7] == 2){
        	
                    if($SE[0] == 0){
        
                       $IBC = $psm; 
        
                    }
                }

                $sueldo = ($SE[0] - $EMP[9]);
                $sueldo = $sueldo + $nacum[0];

                if($EMP[7] == 1 || $EMP[7] == 2){
        	
                    if($SE[0] == 0){
        
                       $IBC = $psm; 
        
                    }
                }
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBC,$hoy,$EMP[0], $Mper[0],78,3)";
                $resultado = $mysqli->query($sql11);
                echo "<br/>";
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($sueldo,$hoy,$EMP[0], $Mper[0],2,3)";
                $resultado = $mysqli->query($sql11);

                #consulta la ecuacion del concepto de subsidio de alimentación
                if($ppa > 0){
                    if($SE[0] < $pta){
                        #valida si el empleado es pensionado
                        if($pens != 1){
                            
                            if($EMP[7] == 1 || $EMP[7] == 2 ){
                            
                            }else{
                                $AuxA = ($ppa * $DT[0])/$DTR[0];
                                $AuxA = round($AuxA / 10) * 10;
                                
                                $AAliAnt = "SELECT valor FROM  gn_novedad WHERE concepto = '65' AND empleado = '$EMP[0]' AND periodo = '$PRetro[0]'";
                                $AuAl = $mysqli->query($AAliAnt);
                                $AA = mysqli_fetch_row($AuAl);

                                $RAA = $AuxA - $AA[0];
                                
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RAA,$hoy,$EMP[0],$Mper[0],65,1)";
                                $resultado = $mysqli->query($sql11);
                            }
                            
                        }
                        
                    }
                }

                if($EMP[7] == 1 || $EMP[7] == 2 ){
            
                    $saludNME   = 0;
                    $pensionNME = 0;
                    
                }else{
                    #valida si el empleado es pensionado
                    if($pens == 1){
                        $saludNME = ($IBC  * $PE) / 100;
                        $saludNME = ceil($saludNME /100) * 100;   
                        $pensionNME = 0; 
                    }else{
                        $saludNME = ($IBC  * $pse) / 100;
                        $saludNME = round($saludNME /$redondeo) * $redondeo;    
        
                        #consulta la ecuacion del aporte al fondo de pension por parte del empleado de los días trabajados
                        $pensionNME = ($IBC  * $ppe) / 100;
                        $pensionNME = round($saludNME /$redondeo) * $redondeo;
                        
                        $PenEANT = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '367'";
                        $PEAnt = $mysqli->query($PenEANT);
                        $PEA  = mysqli_fetch_row($PEAnt);

                        $RPE = $pensionNME - $PEA[0];
                        #echo "Empleado: ".$EMP[0]." pen Act: ".$pensionNME." pen Ant : ".$PEA[0]." tot: ".$RPE;
                        #echo "<br/>";
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RPE,$hoy,$EMP[0],$Mper[0],367,1)";
                        $resultado = $mysqli->query($sql11);
                    }
                    
                    $SalEANT = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '366'";
                    $SEAnt = $mysqli->query($SalEANT);
                    $SEA  = mysqli_fetch_row($SEAnt);

                    $RSEm = $saludNME - $SEA[0];
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RSEm,$hoy,$EMP[0],$Mper[0],366,1)";
                    $resultado = $mysqli->query($sql11);
                }

                #Valida si la empresa esta excenta de pagar aporte a salud
                if($exc == 2 ){
                    #valida si el empleado es pensionado
                    if($pens != 1){
                        if($EMP[7] != 1){
                            #consulta la ecuacion del aporte al fondo de salud por parte del patrono de los días trabajados
                            $saludEMPLE = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$Mper[0]' AND concepto = 366 ";
                            $salEP = $mysqli->query($saludEMPLE);
                            $SENM = mysqli_fetch_row($salEP);
        
                            $salT = $pse + $psp;
                            $saludNMP = ($IBC  * $salT) / 100;
                            $saludNMP = ceil($saludNMP /100) * 100;
                            
                            
                            
                            $saludNMP = $saludNMP - $saludNME;

                            $SalPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '368'";
                            $SPAnt = $mysqli->query($SalPANT);
                            $SPA = mysqli_fetch_row($SPAnt);

                            $RSPa = $saludNMP - $SPA[0]; 
                            
                            #echo "Empleado: ".$EMP[0]." sal p Act: ".$saludNMP." sal p Ant : ".$SPA[0]." tot: ".$RSPa;
                            #echo "<br/>";
                            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RSPa,$hoy,$EMP[0],$Mper[0],368,1)";
                            $resultado = $mysqli->query($sql11);
                        }
                        
                    }else{
                        $saludNMP = 0;
                        $RSPa = 0;
                    }
                    
                }else{
                    $saludNMP = 0;
                    $RSPa = 0;
                }

                
                if($EMP[7] == 1){
                    if($SE[0] > 0){
                        #consulta la ecuacion del aporte al fondo de salud por parte del patrono de los días trabajados
                        $salT = $pse + $psp;
                        $saludNMP = ($IBC  * $salT) / 100;
                        $saludNMP = ceil($saludNMP /100) * 100;
                            
                        $TsaludNMP = $saludNMP;

                        $SalPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '368'";
                        $SPAnt = $mysqli->query($SalPANT);
                        $SPA = mysqli_fetch_row($SPAnt);

                        $RSPa = $TsaludNMP - $SPA[0];
            
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RSPa,$hoy,$EMP[0],$Mper[0],368,1)";
                        $resultado = $mysqli->query($sql11);
                    }else{
                        $RSPa = 0;
                    }
                }

                if($EMP[7] == 1 || $EMP[7] == 2){
                    $pensionNMP = 0;
                    $RPP = 0;
                    
                }else{
                    #valida si el empleado es pesionado
                    if($pens != 1){
                        $penT = $ppp + $ppe; 
                        $pensionNMP = ($IBC  * $penT) / 100;
                        $pensionNMP = ceil($pensionNMP /100) * 100;
                        
                        $pensionNMP = $pensionNMP - $pensionNME;

                        $PENPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '369'";
                        $PPAnt = $mysqli->query($PENPANT);
                        $PPA = mysqli_fetch_row($PPAnt);

                        $RPP = $pensionNMP - $PPA[0];
                        #echo "empl: ".$EMP[0]." pens P: ".$pensionNMP."  pen EMPLE: ".$PPA[0];
                        #echo "<br/>";
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RPP,$hoy,$EMP[0],$Mper[0],369,1)";
                        $resultado = $mysqli->query($sql11);
                    }else{
                        $pensionNMP = 0;
                        $RPP = 0;
                    }
                }

                if($pens !=1){
                    #consulta la ecuacion del aporte al fondo de arl por parte del patrono de los días trabajados
                    $arlNMP = ($IBC * $EMP[8]) / 100;
                    $arlNMP = ceil($arlNMP / 10) * 10;
                    $AR = intval($arlNMP);
                    $dec6 = substr($AR,-2); 
                    $dec6 = intval($dec6);
                   
                    if($dec6 != 0){
        
                        $arlNMP = ceil($arlNMP / 100)*100;
                    }

                    $ARLPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '363'";
                    $ArlAnt = $mysqli->query($ARLPANT);
                    $Arl = mysqli_fetch_row($ArlAnt);

                    $RARL = $arlNMP - $Arl[0];
                    #echo "ARL ACT: ".$arlNMP."  ARL ANT: ".$Arl[0]." ToARÑ: ".$RARL;
                    #echo "<br/>";
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RARL,$hoy,$EMP[0],$Mper[0],363,1)";
                    $resultado = $mysqli->query($sql11);
                    
                    if($exc == 2){
        
                        if($EMP[7] == 1 || $EMP[7] == 2){
        
                            $minedNMP   = 0;
                            $sena       = 0;
                            $esapNMP    = 0;
                            $icbfNMP    = 0; 
                            
                        }else{
                            #consulta la ecuacion del aporte al fondo de icbf por parte del patrono de los días trabajados
                            $icbfNMP = ($IBC * $pic) / 100;
                            $icbfNMP = ceil($icbfNMP/10)*10;
                            $IC = intval($icbfNMP);
                            $dec5 = substr($IC,-2);
                            $dec5 = intval($dec5);
                            if($dec5 != 0){
        
                                $icbfNMP  = ceil($icbfNMP / 100)*100;
                            }

                            $IcbfPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '371'";
                            $IcbfAnt = $mysqli->query($IcbfPANT);
                            $ICBF = mysqli_fetch_row($IcbfAnt);
                            
                            $RICBF = $icbfNMP - $ICBF[0];

                            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfNMP,$hoy,$EMP[0],$Mper[0],371,1)";
                            $resultado = $mysqli->query($sql11);
                            
                            if($pes > 0){
                                
        
                                #consulta la ecuacion del aporte al fondo de esap por parte del patrono de los días trabajados
                                $esapNMP = ($IBC * $pes) /100;
                                $esapNMP = ceil($esapNMP/10)*10;
                                $ESAP = intval($esapNMP);
                                $dec4 = substr($ESAP,-2);
                                $dec4 = intval($dec4);
                                if($dec4 != 0){
        
                                    $esapNMP  = ceil($esapNMP / 100)*100;    
                                }

                                $ESAPPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '372'";
                                $EsapAnt = $mysqli->query($ESAPPANT);
                                $ESAP = mysqli_fetch_row($EsapAnt);
                                
                                $RESAP = $esapNMP - $ESAP[0];
                                
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RESAP,$hoy,$EMP[0],$Mper[0],372,1)";
                                $resultado = $mysqli->query($sql11);
                             
                            }else{
                                $RESAP = 0;
                            }
        
                            #consulta la ecuacion del aporte al fondo de sena por parte del patrono de los días trabajados
                            $senaNMP = ($IBC * $psen) / 100;
                            $senaNMP = ceil($senaNMP/10)*10;
        
                            $SE = intval($senaNMP);
                            $dec2 = substr($SE,-2);
                            $dec2 = intval($dec2);
                            if($dec2 != 0){
        
                                $senaNMP  = ceil($senaNMP / 100)*100;
                                
                            }
                            
                            $SENAPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '373'";
                            $senaAnt = $mysqli->query($SENAPANT);
                            $Sena = mysqli_fetch_row($senaAnt);

                            $RSENA = $senaNMP - $Sena[0];

                            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RSENA,$hoy,$EMP[0],$Mper[0],373,1)";
                            $resultado = $mysqli->query($sql11);
        
                            if($pmi > 0){
                                #consulta la ecuacion del aporte al fondo de ministerio de educ por parte del patrono de los días trabajados
                                $minedNMP = ($IBC * $pmi) / 100;
                                $minedNMP = ceil($minedNMP/10)*10;
                                $MIN = intval($minedNMP);
                                $dec3= substr($MIN,-2);
                                $dec3 = intval($dec3);
                                if($dec3 != 0){
        
                                    $minedNMP = ceil($minedNMP / 100)*100;    
                                } 
                                
                                $MINPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '374'";
                                $minAnt = $mysqli->query($MINPANT);
                                $Min = mysqli_fetch_row($minAnt);

                                $RMIN = $minedNMP - $Min[0];
                                #echo " MIN Act: ".$minedNMP." min Ant: ".$MIN[0]." Base: ".$IBC;
                                #echo "<br/>";
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RMIN,$hoy,$EMP[0],$periodo,374,1)";
                                $resultado = $mysqli->query($sql11);
                            }else{
                                $RMIN = 0;
                            }      
                        }
                        
                    }else{
                        $minedNMP   = 0;
                        $sena       = 0;
                        $esapNMP    = 0;
                        $icbfNMP    = 0;       
                        $RMIN       = 0;
                        $RESAP      = 0;
                        $RICBF      = 0;
                        $RSENA      = 0;
                        
                    }
        
                    if($EMP[7] == 1 || $EMP[7] == 2){
                        if($SE[0] > 0){
                            #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
                            $ccfNMP = ($IBC * $pcc) / 100;
                            $ccfNMP = ceil($ccfNMP/100)*100;
                            
                            $CCFPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '375'";
                            $CCfAnt = $mysqli->query($CCFPANT);
                            $CCF = mysqli_fetch_row($CCFAnt);

                            $RCCF = $ccfNMP - $CCF[0];

                            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RCCF,$hoy,$EMP[0],$periodo,375,1)";
                            $resultado = $mysqli->query($sql11);
                            
                        }else{
                            $ccfNMP = 0;
                            $RCCF = 0;
                        }
                        
        
                    }else{
                        #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
                        $ccfNMP = ($IBC * $pcc) / 100;
                        $ccfNMP = ceil($ccfNMP/100)*100;

                        $CCFPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '375'";
                        $CCFAnt = $mysqli->query($CCFPANT);
                        $CCF = mysqli_fetch_row($CCFAnt);

                        $RCCF = $ccfNMP - $CCF[0];
        
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RCCF,$hoy,$EMP[0],$periodo,375,1)";
                        $resultado = $mysqli->query($sql11);
                        
                    }
                    
                }else{
                    $ccfNMP     = 0;
                    $minedNMP   = 0;
                    $sena       = 0;
                    $esapNMP    = 0;
                    $icbfNMP    = 0;
                    $arlNMP     = 0;
                    $RMIN       = 0;
                    $RESAP      = 0;
                    $RICBF      = 0;
                    $RSENA      = 0;
                    $RCCF       = 0;
                    $RARL       = 0;
                }

                #echo "EMP : ".$EMP[0]." sal: ".$EMP[6];
                if($EMP[6] >= ($psm * 4)){
                   
                    $pfs1 = $pfs / 2;
                    $FSNM1 = ($IBC * $pfs1) / 100;
                    $FSNM1 = ceil($FSNM1 / 100) * 100;
                

                    $FS1PANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '361'";
                    $FS1Ant = $mysqli->query($FS1PANT);
                    $FS1 = mysqli_fetch_row($FS1Ant);
                    if(empty($FS1[0]) || $FS1[0] == ""){
                        $FS1[0];
                    }
                    $RFS1 = $FSNM1 - $FS1[0];

                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RFS1,$hoy,$EMP[0],$periodo,361,1),($RFS1,$hoy,$EMP[0],$periodo,362,1);";
                    $resultado = $mysqli->query($sql11);
                    
                    #consulta la ecuacion del aporte al fondo de solidaridad pensional 
                    $TFS = $FSNM1 + $FSNM1;
                     echo "total: ".$TFS." f1: ".$FSNM1." fT: ".$FS1[0];
                    $FSPANT = "SELECT valor FROM  gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$PRetro[0]' AND concepto = '81'";
                    $FSAnt = $mysqli->query($FSPANT);
                    $FS = mysqli_fetch_row($FSAnt);
                    if(empty($FS[0]) || $FS[0]== ""){
                        $FS[0] = 0;
                    }
                    $RFS = $TFS - $FS[0];

                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($RFS,$hoy,$EMP[0],$periodo,81,1)";
                    $resultado = $mysqli->query($sql11);

                }

                #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del empleado
                $saludE = $RSEm;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludE,$hoy,$EMP[0],$periodo,80,1)";
                $resultado = $mysqli->query($sql11);

                #consulta la ecuacion de la suma total del aporte al fondo de pension por parte del empleado
                $pensionE = $RPE;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionE,$hoy,$EMP[0],$periodo,84,1)";
                $resultado = $mysqli->query($sql11);
                
                #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del patrono
                $saludP = $RSPa ;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludP,$hoy,$EMP[0],$periodo,82,1)";
                $resultado = $mysqli->query($sql11);
                
                #consulta la ecuacion de la suma total del aporte al fondo de pensión por parte del patrono
                $pensionP = $RPP;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionP,$hoy,$EMP[0],$periodo,83,1)";
                $resultado = $mysqli->query($sql11);
                
                #valida si la empresa está excenta de parafiscales 
                if($exc != 1){
                    
                    #consulta la ecuacion de la suma total del aporte al fondo escuela de adminstración pública por parte del patrono
                    if($pes > 0){
                        $esaP = $RESAP; 
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esaP,$hoy,$EMP[0],$periodo,259,1)";
                        $resultado = $mysqli->query($sql11);
                    }
                    
                    #consulta la ecuacion de la suma total del aporte al fondo de caja de compensación familiar  por parte del patrono
                    $ccfP = $RCCF ;
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfP,$hoy,$EMP[0],$periodo,256,1)";
                    $resultado = $mysqli->query($sql11);
                    
                    #consulta la ecuacion de la suma total del aporte al fondo de sena  por parte del patrono
                    $senaP = $RSENA;
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaP,$hoy,$EMP[0],$periodo,257,1)";
                    $resultado = $mysqli->query($sql11);
                    
                    #consulta la ecuacion de la suma total del aporte al fondo de ministerio de educación por parte del patrono
                    if($pmi > 0){
                        $mineducP = $RMIN;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($mineducP,$hoy,$EMP[0],$periodo,258,1)";
                        $resultado = $mysqli->query($sql11);
                    }
                
                    #consulta la ecuacion de la suma total del aporte al fondo de instituto colombiano de bienestar familiar  por parte del patrono
                    $icbfP = $RICBF;
                
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfP,$hoy,$EMP[0],$periodo,260,1)";
                    $resultado = $mysqli->query($sql11);
                    
                }        
                
                $tdev = "SELECT n.id_unico,"
                        . "    sum( n.valor) as total, "
                        . "     n.empleado, "
                        . "     n.periodo, "
                        . "     n.concepto, "
                        . "     c.id_unico, "
                        . "     c.clase "
                        . "     FROM gn_novedad n "
                        . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico " 
                        . "     WHERE c.clase = 1 AND n.concepto != 7 AND n.empleado = $EMP[0] AND n.periodo = $periodo";

                $c = $mysqli->query($tdev);
                $m = mysqli_fetch_row($c);
            
            
                $tde = "SELECT n.id_unico,"
                    . "     sum( n.valor) as total, "
                    . "     n.empleado, "
                    . "     n.periodo, "
                    . "     n.concepto, "
                    . "     c.id_unico, "
                    . "     c.clase "
                    . "     FROM gn_novedad n "
                    . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
                    . "     WHERE c.clase = 2 AND n.concepto != 7 AND n.empleado = $EMP[0] AND n.periodo = $periodo";

                $s = $mysqli->query($tde);
                $p = mysqli_fetch_row($s);
                
                if(empty($p[1]) || $p[1] == ""){
                    
                    $p[1] = 0;            
                }
                
                $Np = $m[1] - $p[1];
            
                $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES "
                    . "($m[1],$hoy,$EMP[0],$periodo,74,1),($p[1],$hoy,$EMP[0],$periodo,98,1),($Np,$hoy,$EMP[0],$periodo,102,1)";
                $resultado=$mysqli->query($tt);
            }
        }
        $resulta = $mysqli->query($sql3);  
    //}
    /*
    while($EMP = mysqli_fetch_row($resulta)){

        if($EMP[6] != $EMP[9]){
            

            /*
                $gg = "SELECT id_unico, fecha, empleado, estado FROM gn_vinculacion_retiro WHERE fecha BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 2";
                $fff =$mysqli->query($gg);
                $kjs = mysqli_num_rows($fff);
                        
                if($kjs > 0) {  
                    $b="SELECT * FROM gn_novedad WHERE concepto = 7 AND empleado = $EMP[0] AND periodo = $periodo";
                    $bb = $mysqli->query($b);
                    $dtb= mysqli_num_rows($bb);
                    $det = mysqli_fetch_row($bb);
                
                    if($dtb < 1){
                        
                        #Valida si existe algun empleado que haya ingresado dentro del periodo
                        $q = "SELECT id_unico, fecha, empleado, estado FROM gn_vinculacion_retiro WHERE fecha BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 1";
                        $rt =$mysqli->query($q);
                        $qq = mysqli_num_rows($rt); 
                        
                        # Valida si existe algun empleado que se haya retirado dentro del periodo
                        $g = "SELECT id_unico, fecha, empleado, estado FROM gn_vinculacion_retiro WHERE fecha BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 2";
                        $ff =$mysqli->query($g);
                        $kj = mysqli_num_rows($ff);
                    
                        #Valida si existe algún empleado que haya ingresado y se haya retirado dentro del periodo de liquidación
                        if($qq >= 1 && $kj >= 1){
                            $yu = mysqli_fetch_row($rt);
                            $os = mysqli_fetch_row($ff);
                            $dias = (strtotime($yu[1])-strtotime($os[1]))/86400;
                            $dias = abs($dias);
                            $dias = floor($dias);
                            $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($dias,$hoy,$yu[2],$periodo,7,1)";
                            $resultado1 = $mysqli->query($sql18);
                
                        }else{
                                
                            if($qq >= 1){
                                
                                while($yu = mysqli_fetch_row($rt)){
                
                                    $dias = (strtotime($DTR[1])-strtotime($yu[1]))/86400;
                                    $dias = abs($dias);
                                    $dias = floor($dias);
                                    $diasT = 30 - $dias;
                                    $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($diasT,$hoy,$yu[2],$periodo,7,1)";
                                    $resultado1 = $mysqli->query($sql18);
                                }
                            }
            
                            if($kj >= 1){
                
                                while($os = mysqli_fetch_row($ff)){
                                    $dias = (strtotime($DTR[1])-strtotime($os[1]))/86400;
                                    $dias = abs($dias);
                                    $dias = floor($dias);
                    
                                    $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($dias,$hoy,$os[2],$periodo,7,1)";
                                    $resultado1 = $mysqli->query($sql18);

                                }
                            }
                        }
                    }
                }
            
        }
        
        
        #inserta el salario actual del empleado
        
            
        
        
       

        #inserta el salario actual del empleado para el periodo
        $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($SE[0],$hoy,$EMP[0],$periodo,1,1)";
        $resultado = $mysqli->query($sql10);

        #consulta si el empleado posee horas extras 
        $sql12 = "SELECT numerohoras, concepto FROM gn_horas_extras WHERE empleado = '$EMP[0]' AND fecha BETWEEN '$DTR[1]' AND '$DTR[2]' ";
        $result1 = $mysqli->query($sql12);
        $nres = mysqli_num_rows($result1);

        #valida si el empleado posee horas extras
        if($nres > 0){

            while($res = mysqli_fetch_row($result1)){
                if($res[1] == 425){
                    $T = (($SE[0] / 240) *  $recno) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,431,3),($res[0],$hoy,$EMP[0],$periodo,425,3);";
                    $resultado = $mysqli->query($sql12);
                }elseif($res[1] == 426){

                    $T = (($SE[0] / 240) * $recdom) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,432,3),($res[0],$hoy,$EMP[0],$periodo,426,3);";
                    $resultado = $mysqli->query($sql12);
                }elseif($res[1] == 427){
                    $T = (($SE[0] / 240) * $hextdo) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,41,3),($res[0],$hoy,$EMP[0],$periodo,427,3);";
                    $resultado = $mysqli->query($sql12);
                }elseif($res[1] == 428){
                    $T = (($SE[0] / 240) * $hextno) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,42,3),($res[0],$hoy,$EMP[0],$periodo,428,3);";
                    $resultado = $mysqli->query($sql12);
                }elseif($res[1] == 429){
                    $T = (($SE[0] / 240) * $hextddf) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,43,3),($res[0],$hoy,$EMP[0],$periodo,429,3);";
                    $resultado = $mysqli->query($sql12);
                }elseif($res[1] == 430){
                    $T = (($SE[0] / 240) * $hextndf) * $res[0];
                    $T = round($T/10)*10;
                    #inserta el valor de las horas y el valor en dinero en la tabla novedad
                    $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($T,$hoy,$EMP[0],$periodo,44,3),($res[0],$hoy,$EMP[0],$periodo,430,3);";
                    $resultado = $mysqli->query($sql12);
                }
            }

        }

        
        #echo "emlp: ".$EMP[0]." sal: ".$SE[0]." salM: ".$psm." cat: ".$EMP[7];
        #echo "<br/>";/
        
            
        
        
        #consulta la ecuacion del concepto de sueldo 
        #$sueldo = ($SE[0] * $DT[0]) / $DTR[0];
        #$sueldo = round($sueldo / 10) * 10;
        
        #$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($sueldo,$hoy,$EMP[0],$periodo,2,1)";
        #$resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del concepto de subsidio de alimentación
        if($ppa > 0){
            if($SE[0] < $pta){
                #valida si el empleado es pensionado
                if($pens != 1){
                    
                    if($EMP[7] == 1 || $EMP[7] == 2 ){
                       
                    }else{
                         $AuxA = ($ppa * $DT[0])/$DTR[0];
                         $AuxA = round($AuxA / 10) * 10;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($AuxA,$hoy,$EMP[0],$periodo,65,1)";
                        $resultado = $mysqli->query($sql11);
                    }
                    
                }
                
            }
        }
        
        #consulta la ecuacion del concepto de auxilio de Transporte
        if($pat > 0){
            if($SE[0] <= ($psm * 2)){
                #valida si el empleado es pensionado
                if($pens != 1){
                    if($EMP[7] == 1 || $EMP[7] == 2){
                        
                    }else{
                        $AuxT = ($pat * $DT[0])/$DTR[0];
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($AuxT,$hoy,$EMP[0],$periodo,66,1)";
                        $resultado = $mysqli->query($sql11);
                    }
                    
                }
                
            }
        }
        
        
        
        
        

        

        

        
        
        
        
        
            
        ////CALCULO DE VALORES TOTALES//////////
        
        
        
     

        

        
        
        
    }*/
       
    
?>

<html>
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