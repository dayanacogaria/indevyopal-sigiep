<?php
    require_once '../Conexion/conexion.php';
    require '../funciones/funciones_formulador.php';   
    session_start();
    
    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];
    
   
    $proceso1 = 2;
    $proceso2 = 4;
    if(!empty($_REQUEST['id_emp'])){

        $empleado = $_REQUEST['id_emp'];
    }

    if(!empty($_REQUEST['id_per'])){

        $periodo = $_REQUEST['id_per'];
        $periodoI = $_REQUEST['id_per'];
    }

    if(!empty($_REQUEST['fr'])){

        $json = $_REQUEST['fr'];
    }else{
        $json = '';
    }

    if(!empty($_POST['sltEmpleado'])){

        $empleado  = $_POST['sltEmpleado'];  
    }

    if(!empty($_POST['sltPeriodo'])){

        $periodo   = $_POST['sltPeriodo'];  
        $periodoI =$_POST['sltPeriodo'];  
    }
    
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
    

         # fechas de periodo inicial
        $sql_pin = "SELECT * from gn_periodo p where p.id_unico='$periodoI' ";
       // $pprin = $GLOBALS['mysqli']->query($sql_pin);
        $pprin = $mysqli->query($sql_pin);
        $reg_pprin = mysqli_fetch_row($pprin);
    
   
    
     $sql1 = "SELECT c.salarioactual, et.tipo FROM gn_categoria c 
LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico 
LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico 
LEFT JOIN gn_empleado_tipo et ON e.id_unico = et.empleado "
            . "WHERE e.id_unico = '$empleado'";

    
    $res1 = $mysqli->query($sql1);
    $result1 = mysqli_fetch_row($res1);
    $salario = $result1[0];

    $sql2 = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $anno AND tipo_empleado=".$result1[1];
    $resu = $mysqli->query($sql2);
    $rowP = mysqli_fetch_row($resu);

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
    $Tprovi     = $rowP[30];//tipo de entidad: pública o privada
    $diasbr     = $rowP[42];//tipo de entidad: pública o privada

    $sqlP = "SELECT  valor FROM gs_parametros_basicos WHERE nombre ='Nomina Empresa Servicios Publicos' AND compania=".$compania;
    $resuP = $mysqli->query($sqlP);
    $rowPr = mysqli_fetch_row($resuP);
        if ($rowPr[0]!='NO') {
            $AcumPrimaNav = acumular_eN($empleado, 8, 12,108,$periodo);
        }
        


        $AcumPrima = acumular_eN($empleado, $proceso1, 1,110,$periodo);
     
    
    $AcumBonif = acumular_eN($empleado, $proceso2, 12,111,$periodo);
    $AcumPrimaA = acumular_eN($empleado, 15, 12,484,$periodo);

    //echo $AcumPrimaNav.'PN - ';

    $x = $AcumBonif/12;
    $y = $AcumPrima/12;
    $z = $AcumPrimaNav/12;
    $gd = $AcumPrimaA/12;

     $sql2 = "SELECT primaA, talimentacion, dias_primav,auxt,tope_aux_transporte     FROM  gn_parametros_liquidacion WHERE vigencia = '$anno' AND tipo_empleado=".$result1[1];
    $res2 = $mysqli->query($sql2);
    $result2 = mysqli_fetch_row($res2);
    
    if($result1[0] > $result2[1]){
        
        $result2[0] = 0;
    }
    $aux_tr = 0;
    if( $result2[3] > 0 ){
        if( $salario < $result2[4]){
            $aux_tr = $result2[3] ;
        }
    }

    //echo "bonificacion: ".$x." prima ser: ".$y." salrio: ".$result1[0]." prima A: ".$result2[0];
    //echo $x.' - '.$y.' - '.$z.' - '.$gd.' - '.$result1[0].' - '.$result2[0];
    $SUMP = ((($x + $y + $z + $gd+ $result1[0] + $result2[0]+$aux_tr) *  $result2[2]) / 30);
    $SUMP = intval($SUMP);

    $SUMB = (( $result1[0]  * 2) / 30);
    $SUMB = intval($SUMB);
    

    /*Dias de disfrute vacaciones*/
    $sql_prd="SELECT fechainicio from gn_periodo where id_unico = '$periodo'";
    $res_prd = $mysqli->query($sql_prd);
    $result_prd = mysqli_fetch_row($res_prd);
     $sql_vac="SELECT fechainiciodisfrute,fechafindisfrute  FROM gn_vacaciones where empleado = $empleado AND fechainiciodisfrute >='$result_prd[0]'";
    $res_vac = $mysqli->query($sql_vac);
    $result_vac = mysqli_fetch_row($res_vac);

    $dias_vac = (strtotime($result_vac[1])-strtotime($result_vac[0]))/86400;
    $dias_vac = abs($dias_vac);
    $dias_vac = floor($dias_vac)+1;

    //$Salario_Base = ( $result1[0]+ $result2[0]+$result2[3]+$x + $y);
    $Salario_Base = ( $result1[0]+ $result2[0]+$x + $y);
    $Salario_Base = intval($Salario_Base);

    $val_bon_recre=($result1[0]/30)*$diasbr;

    //echo  $x.' - '.$y.' - '.$result1[0].' - '.$result2[0].' - '.$result2[3].'dv:'.$dias_vac;
    //echo 'base: '.($x + $y + $result1[0]+$result2[0]+$result2[3]);
    //echo 'base: '.($x + $y + $result1[0]+$result2[0]+$result2[3])*21;
    //echo 'base: '.(($x + $y + $result1[0]+$result2[0]+$result2[3])*21)/30;
    $vlr_vac_total = ((($x + $y + $result1[0]+$result2[0]+$result2[3])*$dias_vac)/30);
    //var_dump($vlr_vac_total);
    $vlr_vac_total = intval($vlr_vac_total);
    //var_dump($vlr_vac_total);
    
    $sql3 = "DELETE n.* FROM gn_novedad n 
    LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
    WHERE n.empleado = '$empleado' AND n.periodo = '$periodo' AND c.clase!=2";
    
    $res3 = $mysqli->query($sql3);
    
    #************FACTORES 
    #* X - bonificacion 
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($x,$hoy,$empleado,$periodo,319,4)";
    $resultado = $mysqli->query($sql11);
    #* Y - prima s 
     $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($y,$hoy,$empleado,$periodo,421,4)";
    $resultado = $mysqli->query($sql11);

    #* Z -Prima N 
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($z,$hoy,$empleado,$periodo,437,4)";
    $resultado = $mysqli->query($sql11);
    #* gd -Prima An
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($gd,$hoy,$empleado,$periodo,604,4)";
    $resultado = $mysqli->query($sql11);
    #* Aux Alim
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($result2[0],$hoy,$empleado,$periodo,317,4)";
    $resultado = $mysqli->query($sql11);
    
    #* Aux Transporte
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($result2[3],$hoy,$empleado,$periodo,316,4)";
    $resultado = $mysqli->query($sql11);
    #**************
    #* Bonificacion Recreacion
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($val_bon_recre,$hoy,$empleado,$periodo,105,4)";
    $resultado = $mysqli->query($sql11);

    # VERIFICAR QUE LOS PERIODOS DE VACACIONES ESTEN CREADO 
         # Dias vacaciones TOTALES
     $sql_vac = "SELECT fechainiciodisfrute, fechafindisfrute, dias_hab FROM gn_vacaciones WHERE empleado = '$empleado' AND fechainiciodisfrute >= '$reg_pprin[2]' ";
        $res_vac = $mysqli->query($sql_vac);
        //$res_vac = $GLOBALS['mysqli']->query($sql_vac);
        $nres_vac = mysqli_num_rows($res_vac);
        $registrar = false;
        if($nres_vac>0){
            $row = mysqli_fetch_row($res_vac);  
            $fcini = explode("-", $row[0]);  
            $fcfin = explode("-", $row[1]);  
            $ms_in  =$fcini[1];
            $ano_in = $fcini[0];
            $ms_fn  = $fcfin[1];
            $ms_recorrido = $ms_in;
            
            for($cn=1; $cn<=5; $cn++){

                if($ms_recorrido<=$ms_fn){
                    $finicio= $ano_in.'-'.$ms_recorrido.'-'.'01';
                    $diasp = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_in);
                    if($diasp==31){
                         $ffin = $ano_in.'-'.$ms_recorrido.'-30';    
                    }else{
                        $ffin = $ano_in.'-'.$ms_recorrido.'-'.$diasp;    
                    }
                    
                     $sql_per="SELECT * from gn_periodo p where p.fechainicio>='$finicio'  and p.fechafin = '$ffin'  and p.tipoprocesonomina=7 ";
                    $sp = $mysqli->query($sql_per);
                    //$sp = $GLOBALS['mysqli']->query($sql_per);
                    $nsp = mysqli_num_rows($sp);
                    
                    if($nsp>0){
                        $registrar = true;
                    }

                    $ms_recorrido++;
                } else {
                    if($ms_recorrido==12){
                        $finicio= $ano_in.'-'.$ms_recorrido.'-'.'01';
                        $diasp = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_in);
                        if($diasp==31){
                             $ffin = $ano_in.'-'.$ms_recorrido.'-30';    
                        }else{
                            $ffin = $ano_in.'-'.$ms_recorrido.'-'.$diasp;    
                        }
                        
                         $sql_per="SELECT * from gn_periodo p where p.fechainicio='$finicio'  and p.fechafin = '$ffin' and p.tipoprocesonomina=7 ";
                        $sp = $mysqli->query($sql_per);
                        //$sp = $GLOBALS['mysqli']->query($sql_per);
                        $nsp = mysqli_num_rows($sp);
                        if($nsp>0){
                            $registrar = true;
                        }
                        
                        $ms_recorrido = 1;
                        $ano_in=$ano_in+1;
                        
                        
                    }else{

                        $finicio= $ano_in.'-'.$ms_recorrido.'-'.'01';
                        if($finicio<=$row[1]){
                            $diasp = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_in);
                            if($diasp==31){
                                 $ffin = $ano_in.'-'.$ms_recorrido.'-30';    
                            }else{
                                $ffin = $ano_in.'-'.$ms_recorrido.'-'.$diasp;    
                            }
                            
                            $sql_per="SELECT * from gn_periodo p where p.fechainicio='$finicio'  and p.fechafin = '$ffin' and p.tipoprocesonomina=7 ";
                            $sp = $mysqli->query($sql_per);
                            //$sp = $GLOBALS['mysqli']->query($sql_per);
                            $nsp = mysqli_num_rows($sp);
                            if($nsp>0){
                                $registrar = true;
                            }
                        }
                        
                        $ms_recorrido++;
                    }
                }
            }
            
        }else{
            $registrar = false;
        }
        $acum_salud_empl=0;
        $acum_salud_pat=0;
        $acum_pension_emp=0;
        $acum_pension_pat=0;
        $acum_pension_fondo=0;
        $acum_ccf=0;
        if($registrar == 1){
         # Dias vacaciones TOTALES
        
             $sql_vac = "SELECT fechainiciodisfrute, fechafindisfrute, dias_hab FROM gn_vacaciones WHERE empleado = '$empleado' AND fechainiciodisfrute >= '$reg_pprin[2]' ";
            $res_vac = $mysqli->query($sql_vac);
            //$res_vac = $GLOBALS['mysqli']->query($sql_vac);
            $nres_vac = mysqli_num_rows($res_vac);
            if($nres_vac>0){
                $vac = mysqli_fetch_row($res_vac);    
                # contar los dias de disfrute vacaciones
                $fec_incio_vac = new DateTime($vac[0]);
                $fec_fin_vac = new DateTime($vac[1]);
                $diff = $fec_incio_vac->diff($fec_fin_vac);
                /*$dias_vac_totales=$diff->days;       
                $dias_vac_totales = $dias_vac_totales +1 ;*/
                $dias_vac_totales = $vac[2];

            }

            //valores de recreacion, prima de vacaciones, vacaciones
             $sql4 = "INSERT INTO gn_novedad (valor, fecha, empleado, periodo, concepto, aplicabilidad)VALUES($SUMP, $hoy, $empleado, $periodo, 118, 3)";
            $res4 = $mysqli->query($sql4);
            /*REGISTRAR LO PRINCIPAL*/
            //SALARIO VACACIONES TOTAL
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlr_vac_total,$hoy,$empleado,$periodoI,457,1)";
            $resultado = $mysqli->query($sql11);
            
            //$resultado = $GLOBALS['mysqli']->query($sql11);
            
            //DIAS TOTALES VACACIONES
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($dias_vac_totales,$hoy,$empleado,$periodoI,448,4)";
            $resultado = $mysqli->query($sql11);
            //$resultado = $GLOBALS['mysqli']->query($sql11);

            //sacar los meses de las fecha de disfrite inicio y fin 
            $f_inicio = explode("-", $vac[0]);
            $mes_incio=$f_inicio[1];
            $ano_incio=$f_inicio[0];
            $f_final = explode("-", $vac[1]);
            $mes_final=$f_final[1];
            $ms_recorrido=$f_inicio[1];

            $c=0;
            


            for($cn=1; $cn<=5; $cn++){
               // echo 'contador  '.$cn;
                #echo 'contador  '.$cn;
                #echo 'mes recorrido  '.$ms_recorrido.' mes inicio '.$mes_incio;
                if($ms_recorrido<=12){
                    if($ms_recorrido<=$mes_final){

                        $fec_incio_p = $ano_incio.'-'.$ms_recorrido.'-01';
                        if($ms_recorrido==$mes_incio){
                           #echo 'fecha incio '.$vac[0];
                            $fec_incio_dis = new DateTime($vac[0]);    
                            $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                            if($dias==31){
                                $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                            }else{
                                $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                            }
                            #echo 'fecha fin vaca  '.$vac[1].' fecha periodo '.$reg_pprin[3];
                            if($vac[1]>$reg_pprin[3]){
                                #echo 'fecha final '.$reg_pprin[3];
                                $fec_fin_dis = new DateTime($reg_pprin[3]);
                            }else{
                                #echo 'fecha final '.$vac[1];
                                $fec_fin_dis = new DateTime($vac[1]);
                            } 
                        }else{
                            #echo 'fecha incio2 '.$ano_incio.'-'.$ms_recorrido.'-01';
                             $fec_incio_dis = new DateTime($ano_incio.'-'.$ms_recorrido.'-01');    
                             $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                            if($dias==31){
                                $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                            }else{
                                $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                            }                       

                            if($vac[1]>$fech_fin_per){
                                $fec_fin_dis = new DateTime($fech_fin_per);
                            }else{
                                $fec_fin_dis = new DateTime($vac[1]);
                            }
                        }
                        
                          
                        $diff = $fec_incio_dis->diff($fec_fin_dis);
                        //$dias_vac_disf=$diff->days;
                        $dias_vac_disf = $vac[2];
                        $vlr_vac_dif= ($Salario_Base/30)*$dias_vac_disf;  
                        $vlr_vac_dif = intval($vlr_vac_dif);
                        if($ms_recorrido==$mes_final){
                            $cn=5;
                        }
                        $ms_recorrido++;
                        
                        $realiza_p=true;
                    }else{
                        
                        if($ms_recorrido == 12){

                             $fec_incio_p = $ano_incio.'-'.$ms_recorrido.'-01';
                             
                            if($ms_recorrido==$mes_incio){
                                
                                $fec_incio_dis = new DateTime($vac[0]);   

                                $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                                if($dias==31){
                                    $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                                }else{
                                    $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                                }
                                
                                if($vac[1]>$reg_pprin[3]){
                                    
                                    $fec_fin_dis = new DateTime($reg_pprin[3]);
                                }else{
                                   
                                    $fec_fin_dis = new DateTime($vac[1]);
                                } 
                                
                            }else{
                                 $fec_incio_dis = new DateTime($ano_incio.'-'.$ms_recorrido.'-01');    
                                 $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                                if($dias==31){
                                    $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                                }else{
                                    $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                                }                       

                                if($vac[1]>$fech_fin_per){
                                    $fec_fin_dis = new DateTime($fech_fin_per);
                                }else{
                                    $fec_fin_dis = new DateTime($vac[1]);
                                }
                            }
                                                  
                            $diff = $fec_incio_dis->diff($fec_fin_dis);
                            //$dias_vac_disf=$diff->days;
                            $dias_vac_disf = $vac[2];
                            $vlr_vac_dif= ($Salario_Base/30)*$dias_vac_disf;  
                            $vlr_vac_dif = intval($vlr_vac_dif);
                            $realiza_p=true;

                            $ms_recorrido=1;
                            $ano_incio = $ano_incio+1;
                        }else{

                            $fec_incio_p = $ano_incio.'-'.$ms_recorrido.'-01';
                            if($fec_incio_p<=$row[1]){
                                if($ms_recorrido==$mes_incio){
                                    $fec_incio_dis = new DateTime($vac[0]);    

                                    $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                                    if($dias==31){
                                        $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                                    }else{
                                        $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                                    }
                                    if($vac[1]>$reg_pprin[2]){
                                        $fec_fin_dis = new DateTime($reg_pprin[3]);
                                    }else{
                                        $fec_fin_dis = new DateTime($vac[1]);
                                    } 
                                }else{
                                     $fec_incio_dis = new DateTime($ano_incio.'-'.$ms_recorrido.'-01');    
                                     $dias = cal_days_in_month(CAL_GREGORIAN, $ms_recorrido,$ano_incio);
                                        if($dias==31){
                                            $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-30';    
                                        }else{
                                            $fech_fin_per = $ano_incio.'-'.$ms_recorrido.'-'.$dias;    
                                        }                       

                                        if($vac[1]>$fech_fin_per){
                                            $fec_fin_dis = new DateTime($fech_fin_per);
                                        }else{
                                            $fec_fin_dis = new DateTime($vac[1]);
                                        }
                                }
                                                  
                                $diff = $fec_incio_dis->diff($fec_fin_dis);
                                /*$dias_vac_disf=$diff->days;
                                $dias_vac_disf = $dias_vac_disf+1;
                                */
                                $dias_vac_disf = $vac[2];
                                $vlr_vac_dif= ($Salario_Base/30)*$dias_vac_disf;  
                                $vlr_vac_dif = intval($vlr_vac_dif);
                                $realiza_p=true;
                            }else{
                                $realiza_p=false;
                            }
                            $ms_recorrido++;
                        }
                        if($ms_recorrido==$mes_final){
                            $cn=5;
                        }
                    }
                }else{
                    $realiza_p=false;
                }
             //echo ' contador  '.$cn.' realiza  '.$realiza_p;
                if($realiza_p == 1){
                   // echo ' contador  '.$cn;
                   //echo ' entra proceso 2'.$realiza_p;
                    
                  //  echo '  fecha periodo '.$fec_incio_p.'  fecha final '.$fech_fin_per;
                   $sql_per2 = "SELECT * from gn_periodo p where p.fechainicio='$fec_incio_p'  and p.fechafin = '$fech_fin_per' and p.tipoprocesonomina=7";
                    
                    $sp2 = $mysqli->query($sql_per2);
                    //$sp = $GLOBALS['mysqli']->query($sql_per);
                    $nsp = mysqli_num_rows($sp2);
                    if($nsp>0){
                        $per = mysqli_fetch_row($sp2);    

                        
                        /***************************************************VACACIONES MES *********************************/
                        
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($Salario_Base,$hoy,$empleado,$per[0],1,1,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //DIAS DISFRUTE VACACIONES MES
                       $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($dias_vac_disf,$hoy,$empleado,$per[0],27,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);

                        //VALOR DISFRUTE VACACIONES MES
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($vlr_vac_dif,$hoy,$empleado,$per[0],107,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);
                        /************************************EMPLEADO****************************************/
                        //VALOR DESCUENTO SALUD VACACIONES MES
                        //echo '   acomulado '.$acum_salud_empl;

                        $desc_sal_emp_mes=($vlr_vac_dif * $pse)/100;    
                        $desc_sal_emp_mes = ceil($desc_sal_emp_mes/100)*100;   
                        //echo '   descuento  '.$desc_sal_emp_mes;
                        $acum_salud_empl = $acum_salud_empl + $desc_sal_emp_mes;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_sal_emp_mes,$hoy,$empleado,$per[0],449,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);

                        //VALOR DESCUENTO PENSION VACACIONES MES
                        $desc_pen_emp_mes=($vlr_vac_dif * $ppe)/100;    
                        $desc_pen_emp_mes = ceil($desc_pen_emp_mes/100)*100;    
                        $acum_pension_emp =   $acum_pension_emp + $desc_pen_emp_mes;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_pen_emp_mes,$hoy,$empleado,$per[0],450,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);

                        $desc_pen_fondo=($vlr_vac_dif * 0.5)/100;    
                        $desc_pen_fondo = ceil($desc_pen_fondo/100)*100;    
                        $acum_pension_fondo =   $acum_pension_fondo + $desc_pen_fondo;
                        //$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_pen_fondo,$hoy,$empleado,$per[0],361,4,$periodoI)";
                        ////$resultado = $mysqli->query($sql11);
                        //$acum_pension_fondo =   $acum_pension_fondo + $desc_pen_fondo;
                        //$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_pen_fondo,$hoy,$empleado,$per[0],362,4,$periodoI)";
                        //$resultado = $mysqli->query($sql11);

                        $total_fondo_sol_mes=$desc_pen_fondo+$desc_pen_fondo;
                        //$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($total_fondo_sol_mes,$hoy,$empleado,$per[0],488,4,$periodoI)";
                        //$resultado = $mysqli->query($sql11);


                        /**************************************PATRONO****************************************/
                        //VALOR DESCUENTO SALUD VACACIONES MES
                        
                         if($exc==1){
                            $desc_sal_pat_mes=0;
                        }else{
                            $desc_sal_pat_mes=($vlr_vac_dif * ($psp+$pse))/100;       
                            $desc_sal_pat_mes = $desc_sal_pat_mes - $desc_pen_emp_mes;
                            $desc_sal_pat_mes = ceil($desc_sal_pat_mes/100)*100;    
                        }

                        $acum_salud_pat = $acum_salud_pat + $desc_sal_pat_mes;    
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_sal_pat_mes,$hoy,$empleado,$per[0],451,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);

                        //VALOR DESCUENTO PENSION VACACIONES MES
                        $desc_pen_pat_mes=($vlr_vac_dif * ($ppp + $ppe))/100;     
                        $desc_pen_pat_mes = $desc_pen_pat_mes - $desc_pen_emp_mes;  
                        $desc_pen_pat_mes = ceil($desc_pen_pat_mes/100)*100;  
                        $acum_pension_pat =   $acum_pension_pat + $desc_pen_pat_mes;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_pen_pat_mes,$hoy,$empleado,$per[0],452,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);

                        //VALOR DESCUENTO CAJA DE CONMPENSACION VACACIONES MES
                        $desc_ccf_mes=($vlr_vac_dif * ($pcc))/100;     
                        $desc_ccf_mes = ceil($desc_ccf_mes/100)*100;  
                        $acum_ccf = $acum_ccf + $desc_ccf_mes;
                        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin)VALUES($desc_ccf_mes,$hoy,$empleado,$per[0],453,4,$periodoI)";
                        $resultado = $mysqli->query($sql11);
                        //$resultado = $GLOBALS['mysqli']->query($sql11);
                        //$res3=1;      

                        if($exc != 1){
                            #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en incapacidad general
                            $icbfVAC = ($vlr_vac_total * $pic) / 100;
                            $icbfVAC = ceil($icbfVAC/100)*100;
                            /*$af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                            $afl_prf = $mysqli->query($af_prf);
                            $naf_prf = mysqli_num_rows($afl_prf);
                            if($naf_prf>0){*/
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfVAC,$hoy,$empleado,$periodo,489,3)";
                                $resultado = $mysqli->query($sql11);
                            //}
                            
                            
                            #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en incapacidad general
                            $esapVAC = ($vlr_vac_total * $pes) / 100;
                            $esapVAC = ceil($esapVAC/100)/100;
                           /* $af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                            $afl_prf = $mysqli->query($af_prf);
                            $naf_prf = mysqli_num_rows($afl_prf);
                            if($naf_prf>0){*/
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapVAC,$hoy,$empleado,$periodo,490,3)";
                                $resultado = $mysqli->query($sql11);
                            //}
                            
                            
                            #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en incapacidad genral
                            $minedVAC = ($vlr_vac_total * $pmi) / 100;
                            $minedVAC = ceil($minedVAC/100)/100;
                            
                            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedVAC,$hoy,$empleado,$periodo,491,3)";
                            $resultado = $mysqli->query($sql11);
                            
                            #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en incapacidad genral
                            $senaVAC = ($vlr_vac_total * $psen) / 100;
                            $senaVAC = ceil($senaVAC/100)*100;
                           /* $af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                            $afl_prf = $mysqli->query($af_prf);
                            $naf_prf = mysqli_num_rows($afl_prf);
                            if($naf_prf>0){*/
                                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaVAC,$hoy,$empleado,$periodo,492,3)";
                                $resultado = $mysqli->query($sql11);
                            //}
                            
                            
                            
                        }else{
                            $senaVAC        = 0;
                            $icbfVAC        = 0;
                            $ccfVAC         = 0;
                            $esapVAC        = 0;
                            $minedVAC       = 0;    
                        }
                        $tdev = "SELECT n.id_unico,
                                   sum( n.valor) as total, 
                                   n.empleado, 
                                   n.periodo, 
                                   n.concepto, 
                                   c.id_unico, 
                                   c.clase 
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                                  WHERE c.clase = 1 AND n.concepto != 7 AND n.empleado = $empleado AND n.periodo = $per[0]";
                    
                        $c = $mysqli->query($tdev);
                        $m = mysqli_fetch_row($c);
                       
                       
                        $tde = "SELECT n.id_unico,
                                 sum( n.valor) as total, 
                                 n.empleado, 
                                 n.periodo, 
                                 n.concepto, 
                                 c.id_unico, 
                                 c.clase 
                                 FROM gn_novedad n 
                                 LEFT JOIN gn_concepto c ON n.concepto = c.id_unico                                     
                                 WHERE   c.clase = 2 AND n.empleado = $empleado AND n.periodo = $per[0]";

                        $s = $mysqli->query($tde);
                        $p = mysqli_fetch_row($s);
                        
                        if(empty($p[1]) || $p[1] == ""){
                            
                            $p[1] = 0;            
                        }
                        
                        $Np = $m[1] - $p[1];
                       
                        $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad,periodo_prin) VALUES 
                            ($m[1],$hoy,$empleado,$per[0],74,1,$periodoI),($p[1],$hoy,$empleado,$per[0],98,1,$periodoI),($Np,$hoy,$empleado,$per[0],102,1,$periodoI)";
                        $resultado=$mysqli->query($tt);                

                    }
                }
            }
            /*************************************************EMPLEADO VACACIONES TOTAL*********************************/

            //VALOR DESCUENTO SALUD 
            $desc_sal_emp_Total=$acum_salud_empl;   
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_sal_emp_Total,$hoy,$empleado,$periodoI,396,4)";
            $resultado = $mysqli->query($sql11);
           // $resultado = $GLOBALS['mysqli']->query($sql11);

            //VALOR DESCUENTO PENSION 
            $desc_pension_emp_Total=$acum_pension_emp;   
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_pension_emp_Total,$hoy,$empleado,$periodoI,397,4)";
            $resultado = $mysqli->query($sql11);

            $desc_pension_Fondo_Total=$acum_pension_fondo;   
            //$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_pension_Fondo_Total,$hoy,$empleado,$periodoI,81,4)";
            //$resultado = $mysqli->query($sql11);
            //$resultado = $GLOBALS['mysqli']->query($sql11);

            /*****************************************************PATRONO VACACIONES TOTAL*********************************/
            //VALOR DESCUENTO SALUD 
            if($exc==1){
                $desc_sal_pat_Total=0;
            }else{
                $desc_sal_pat_Total=$acum_salud_pat;    
            }
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_sal_pat_Total,$hoy,$empleado,$periodoI,398,4)";
            $resultado = $mysqli->query($sql11);
            //$resultado = $GLOBALS['mysqli']->query($sql11);

            //VALOR DESCUENTO PENSION 
            $desc_pension_pat_Total=$acum_pension_pat;   
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_pension_pat_Total,$hoy,$empleado,$periodoI,399,4)";
            $resultado = $mysqli->query($sql11);
            //$resultado = $GLOBALS['mysqli']->query($sql11);

            //VALOR DESCUENTO CAJA DE CONMPENSACION
            $desc_ccf_pat_Total=$acum_ccf;        
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($desc_ccf_pat_Total,$hoy,$empleado,$periodoI,256,4)";
            $resultado = $mysqli->query($sql11);
            //$resultado = $GLOBALS['mysqli']->query($sql11);

            if($exc != 1){
                #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en incapacidad general
                $icbfVAC = ($vlr_vac_total * $pic) / 100;
                $icbfVAC = ceil($icbfVAC/100)*100;
                /*$af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                $afl_prf = $mysqli->query($af_prf);
                $naf_prf = mysqli_num_rows($afl_prf);
                if($naf_prf>0){*/
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfVAC,$hoy,$empleado,$periodo,462,3)";
                    $resultado = $mysqli->query($sql11);
               // }
                
                
                #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en incapacidad general
                $esapVAC = ($vlr_vac_total * $pes) / 100;
                $esapVAC = ceil($esapVAC/100)/100;
                /*$af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                $afl_prf = $mysqli->query($af_prf);
                $naf_prf = mysqli_num_rows($afl_prf);
                if($naf_prf>0){*/
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapVAC,$hoy,$empleado,$periodo,463,3)";
                    $resultado = $mysqli->query($sql11);
               //}
                
                
                #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en incapacidad genral
                $minedVAC = ($vlr_vac_total * $pmi) / 100;
                $minedVAC = ceil($minedVAC/100)/100;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedVAC,$hoy,$empleado,$periodo,464,3)";
                $resultado = $mysqli->query($sql11);
                
                #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en incapacidad genral
                $senaVAC = ($vlr_vac_total * $psen) / 100;
                $senaVAC = ceil($senaVAC/100)*100;
                /*$af_prf="SELECT * FROM gn_afiliacion where empleado = $empleado and tipo = 5 and fecharetiro is null";
                $afl_prf = $mysqli->query($af_prf);
                $naf_prf = mysqli_num_rows($afl_prf);
                if($naf_prf>0){*/
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaVAC,$hoy,$empleado,$periodo,465,3)";
                    $resultado = $mysqli->query($sql11);
               // }
                
                
                
            }else{
                $senaVAC        = 0;
                $icbfVAC        = 0;
                $ccfVAC         = 0;
                $esapVAC        = 0;
                $minedVAC       = 0;    
            }




        }else{
            $res3=2;
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
            . "     WHERE c.clase = 1 AND n.concepto != 7 AND n.empleado = $empleado AND n.periodo = $periodo";

        $c = $mysqli->query($tdev);
        $m = mysqli_fetch_row($c);
     
        $tde = "SELECT n.id_unico,"
            . "    sum( n.valor) as total, "
            . "     n.empleado, "
            . "     n.periodo, "
            . "     n.concepto, "
            . "     c.id_unico, "
            . "     c.clase "
            . "     FROM gn_novedad n "
            . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico "
            . "     WHERE c.clase = 2 AND n.concepto != 7 AND n.empleado = $empleado AND n.periodo = $periodo";

        $s = $mysqli->query($tde);
        $p = mysqli_fetch_row($s);
        
        if(empty($p[1]) || $p[1] == ""){
            
            $p[1] = 0;            
        }  
   
        $Np = $m[1] - $p[1];
    
        $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES "
            . "($m[1],$hoy,$empleado,$periodo,454,1),($p[1],$hoy,$empleado,$periodo,455,1),($Np,$hoy,$empleado,$periodo,456,1)";
        $resultado1=$mysqli->query($tt);
$json=2;

  echo json_encode($res3);
?>