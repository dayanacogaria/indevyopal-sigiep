<?php
require_once '../Conexion/conexion.php';
require '../funciones/funciones_formulador.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonNomina/funcionesNomina.php'; 
$con = new ConexionPDO();
session_start();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
if(!empty($_REQUEST['id_emp'])){ $empleado = $_REQUEST['id_emp']; }

if(!empty($_REQUEST['id_per'])){$periodo = $_REQUEST['id_per'];}

if(!empty($_POST['sltEmpleado'])){ $empleado  = $_POST['sltEmpleado'];  }

if(!empty($_POST['sltPeriodo'])){ $periodo   = $_POST['sltPeriodo'];  }


$DiaTRA = "SELECT dias_nomina,fechainicio,fechafin, month(fechainicio), year(fechainicio), day(fechafin), periodo_retro FROM gn_periodo WHERE id_unico = '$periodo'";
$dias   = $mysqli->query($DiaTRA);
$DTR    = mysqli_fetch_row($dias);
$FF     = explode("-",$DTR[2]);
$DP     = $FF[2];

$hoy    = date('d-m-Y');
$hoy    = trim($hoy, '"');
$fecha_div = explode("-", $hoy);
$anio1  = $fecha_div[2];
$mes1   = $fecha_div[1];
$dia1   = $fecha_div[0];
$hoy    = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';


$fechaInicio    = $DTR[1];
$fechaFin       = $DTR[2];
$diasPeriodo    = $DTR[0];


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
        c.salarioactual,
        e.unidadejecutora,
        cr.valor,
        e.cesantias , e.grupogestion, et.tipo  
    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico 
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado 
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria 
    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
    LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico != 2 AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<= '$retE[1]'  ORDER BY vr.fechaacto DESC LIMIT 1)=1) or  ((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto BETWEEN '$retE[0]' AND '$retE[1]' ORDER BY vr.fechaacto DESC LIMIT 1)=2 )) ORDER BY `e`.`id_unico` DESC"; 

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
        e.cesantias,e.grupogestion, et.tipo 

    FROM gn_empleado e 
    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
    LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
    LEFT JOIN gn_empleado_tipo et ON et.empleado =e.id_unico 
    WHERE e.id_unico = $empleado"; 

}

$resulta = $mysqli->query($sql3);
while($EMP = mysqli_fetch_row($resulta)){
    
    #¨***parametros **#
    $sql2 = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $anno and tipo_empleado  = ".$EMP[11];
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
    $pta        = $rowP[18];// tope alimentacion
    $pad        = $rowP[19];// tope alimentacion docente
    $pie        = $rowP[20];// porcentaje de incapacidad
    $exc        = $rowP[21];// excento de parafiscales
    $recno      = $rowP[22];//recargo nocturno
    $recdom     = $rowP[23];// recargo dominical
    $hextdo     = $rowP[24];//hora extra diurna ordinaria
    $hextddf    = $rowP[25];// hora extra diurna dominical
    $hextno     = $rowP[26];//hora extra nocturna ordinaria
    $hextndf    = $rowP[27];//hora extra nocturna dominical
    $redondeo   = $rowP[28];//factor de redondeo de los descuentos y devengos
    $Ssena      = $rowP[29];//Salud Sena
    $Tprovi     = $rowP[30];//Tipo Provision
    $gg         = $rowP[31];//Grupo Gestión
    $TipoEm     = $rowP[32];//Tipo Empleado
    $hexnoco    = $rowP[33];//Hora Extra Notctua Orn
    $TopeTrans  = $rowP[34];//Tope Auxilio T
    $diasPV     = $rowP[35];//Dias PV
    $aplicaB    = $rowP[36];//Aplica B

    $PE = 12;

    
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
    
    #*consulta para saber que novedades son para siempre para un empleado
    $sql = "SELECT * FROM gn_novedad WHERE aplicabilidad= 3 AND periodo = 1 AND empleado = '$EMP[0]'"; 
    $res  = $mysqli->query($sql);
    $w     = mysqli_num_rows($res);       
    if($w > 0){            
        while($APLI3 = mysqli_fetch_row($res)){                
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($APLI3[1],$hoy,$EMP[0],$periodo,$APLI3[5],3)";
            $resultado = $mysqli->query($sql11);                
        }
    }

    #*consulta para saber que novedades son para siempre para todos los empleados
    $sql20 = "SELECT * FROM gn_novedad WHERE aplicabilidad= 1 AND periodo = 1 AND empleado = 2 "; 
    $res20  = $mysqli->query($sql20);
    $x     = mysqli_num_rows($res20);
    if($x > 0){            
        while($APLI1 = mysqli_fetch_row($res20)){                
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($APLI1[1],$hoy,$EMP[0],$periodo,$APLI1[5],1)";
            $resultado = $mysqli->query($sql11);
        }
    }

    #*consulta para saber que novedades son al periodo para todos los  empleados
    $sql21 = "SELECT * FROM gn_novedad WHERE aplicabilidad= 2 AND periodo = '$periodo' AND empleado = 2 "; 
    $res21  = $mysqli->query($sql21);
    $z     = mysqli_num_rows($res21);
    if($z > 0){
        
        while($APLI2 = mysqli_fetch_row($res21)){
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($APLI2[1],$hoy,$EMP[0],$periodo,$APLI2[5],3)";
            $resultado = $mysqli->query($sql11);
        }
    }

    #*consulta si el empleado es pensionado
    $pensionado = "SELECT empleado FROM gn_pensionado WHERE empleado = '$EMP[0]'";
    $pensio = $mysqli->query($pensionado);
    $npens = mysqli_fetch_row($pensio);
    if(empty($npens[0]) || $npens[0] == " "){
        $pens = 0;
    }else{
        $pens = 1;
    }
 
    #* CONSULTA QUE EL EMPLEADO NO ESTE RETIRADO
    #* DIAS TRABAJADOS
    $gg = "SELECT id_unico, fechaacto, empleado, estado FROM gn_vinculacion_retiro WHERE fechaacto BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] ";
    $fff =$mysqli->query($gg);
    $kjs = mysqli_num_rows($fff); 
    if($kjs > 0) {  
        $b="SELECT * FROM gn_novedad WHERE concepto = 7 AND empleado = $EMP[0] AND periodo = $periodo";
        $bb = $mysqli->query($b);
        $dtb= mysqli_num_rows($bb);
        $det = mysqli_fetch_row($bb);
       
        if($dtb < 1){
            
            #Valida si existe algun empleado que haya ingresado dentro del periodo
            $q = "SELECT id_unico, fechaacto, empleado, estado FROM gn_vinculacion_retiro WHERE fechaacto BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 1 ";
            $rt =$mysqli->query($q);
            $qq = mysqli_num_rows($rt); 
            
            # Valida si existe algun empleado que se haya retirado dentro del periodo
            $g = "SELECT id_unico, fechaacto, empleado, estado FROM gn_vinculacion_retiro WHERE fechaacto BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 2 ";
            $ff =$mysqli->query($g);
            $kj = mysqli_num_rows($ff);
          
            #Valida si existe algún empleado que haya ingresado y se haya retirado dentro del periodo de liquidación
            if($qq >= 1 && $kj >= 1){
                $yu = mysqli_fetch_row($rt);
                $os = mysqli_fetch_row($ff);

                $fs     = new DateTime($os[1]);
                $fe     = new DateTime($yu[1]);
                

                if($DTR[5]<30){
                    $sum =30-$DTR[5];
                } else {
                    $sum = 1;
                }
                
                IF($fe > $fs){
                    $ffp    = new DateTime($DTR[1]);
                    $fid    = new DateTime($yu[1]);
                    $diff   = $fid ->diff($ffp);
                    $dias  =  $diff->days+$sum;
                } else {
                    $fid    = new DateTime($os[1]);
                    $ffp    = new DateTime($yu[1]);
                    //var_dump($fid);
                    //var_dump($ffp);
                    $diff   = $fid->diff($ffp);
                    $dias  =  $diff->days+$sum;
                }

                $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($dias,$hoy,$yu[2],$periodo,7,1)";
                $resultado1 = $mysqli->query($sql18);        
            }else{                        
                if($qq >= 1){                        
                    while($yu = mysqli_fetch_row($rt)){
                
                        $ffp    = new DateTime($DTR[2]);
                        $fid    = new DateTime($yu[1]);
                        
                        $diff   = $ffp->diff($fid);
                        $dias  =  $diff->days+1;
                        if($DTR[5]<30){
                            $dias+=2;
                        }

                        $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($dias,$hoy,$yu[2],$periodo,7,1)";
                        $resultado1 = $mysqli->query($sql18);
                    }
                }    
                if($kj >= 1){           
                    while($os = mysqli_fetch_row($ff)){
                        $ffp    = new DateTime($DTR[1]);
                        $fid    = new DateTime($os[1]);
                        $diff   = $fid ->diff($ffp);
                        $dias  =  $diff->days+1;


                        $sql18 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($dias,$hoy,$os[2],$periodo,7,1)";
                        $resultado1 = $mysqli->query($sql18);

                    }
                } 
            }
        }else{
             $g = "SELECT id_unico, fechaacto, empleado, estado FROM gn_vinculacion_retiro WHERE fechaacto BETWEEN '$DTR[1]' AND '$DTR[2]' AND empleado = $EMP[0] AND estado = 2";
            $ff =$mysqli->query($g);
            $kj = mysqli_num_rows($ff);              
            #Valida si existe algún empleado que haya ingresado y se haya retirado dentro del periodo de liquidación               
            while($os = mysqli_fetch_row($ff)){
                $dias = (strtotime($DTR[1])-strtotime($os[1]))/86400;
                $dias = abs($dias);
                $dias = floor($dias);
                $sql18 = "UPDATE gn_novedad set valor =$dias where empleado =$os[2] and periodo = $periodo  and concepto = 7";
                $resultado1 = $mysqli->query($sql18);

            }
        }
        //echo $sql18;
    }    

    
    #consulta si el empleado posee vacaciones en fechas dentro de la fecha inicio y la fecha fin del periodo
    $sql9 = "SELECT fechainiciodisfrute, fechafindisfrute, dias_hab FROM gn_vacaciones WHERE empleado = '$EMP[0]' AND 
    (fechainiciodisfrute BETWEEN '$DTR[1]' AND  '$DTR[2]' or fechafindisfrute BETWEEN '$DTR[1]' AND '$DTR[2]') ";
    $res9   = $mysqli->query($sql9);
    $nres9  = mysqli_num_rows($res9);
    $IBCVAC = 0;        
    #Valida si el empleado posee vacaciones
    if($nres9 > 0){            
        $DV = mysqli_fetch_row($res9);     
        $diasv = 0;
        
        if($DV[0] < $DTR[1] ){
            
            $ffd = new DateTime($DV[1]);
            $fip = new DateTime( $DTR[1]);
            $diff = $ffd ->diff($fip);
            $diasv =  $diff->days+1;

        } elseif($DV[1] > $DTR[2]) {            
            
            $ffp    = new DateTime($DTR[2]);
            $fid    = new DateTime( $DV[0]);
            $diff   = $ffp ->diff($fid);
            $diasv  =  $diff->days+1;
        } else {
            $diasv =$DV[2];
        }


        #inserta la novedad de días de vacaciones del empleado en el periodo
        $dvac = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($diasv,$hoy,$EMP[0],$periodo,27,3)";
        $resutlado = $mysqli->query($dvac);
       
        #consulta si el empleado posee una novedad de días trabajados en el periodo
        $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
        $diasT = $mysqli->query($dias_trab);
        $ndias = mysqli_num_rows($diasT);
        
        #valida si el empleado posee la novedad de días trabajados en el periodo
        if($ndias > 0){
            
            $DT = mysqli_fetch_row($diasT);
            $TDT = $DT[0]- $diasv; 
            
            $sql10 = "UPDATE gn_novedad SET valor = '$TDT' WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 7 ";
            $resultado = $mysqli->query($sql10);
        }else{
            $TDT = $DTR[0] - $diasv;
            $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TDT,$hoy,$EMP[0],$periodo,7,1)";
            $resultado = $mysqli->query($sql10);
        } 
        
        #inserta el salario actual del empleado
        $salEmp = "SELECT c.salarioactual FROM gn_categoria c
        LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico
        LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico
        WHERE e.id_unico = '$EMP[0]'";

        $SEM = $mysqli->query($salEmp);
        $SE = mysqli_fetch_row($SEM);

        #consulta la ecuacion del vaciones
        $IBCVAC =  ($SE[0] ) * $diasv/$DTR[0];
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCVAC,$hoy,$EMP[0],$periodo,416,3)";
        $resultado = $mysqli->query($sql11);

        #Inserta el salario de vaciones
        /*$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCVAC,$hoy,$EMP[0],$periodo,107,3)";
        $resultado = $mysqli->query($sql11);*/

        #$sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($DI,$hoy,$EMP[0],$periodo,138,3)";
        #$resultado = $mysqli->query($sql11);
        
        #ecuacion del concepto del aporte al fondo de salud por parte del empleado en  incapacidad general
        if($exc == 1){
            $saludVACE = ($IBCVAC * $pse) / 100;
            $saludVACE = ceil($saludVACE/100)*100;    
        }else{
            $saludVACE = ($IBCVAC * $pse) / 100;
            $saludVACE = ROUND($saludVACE); 
        } 
           
        #echo "salud vac: ".$saludVACE." emple: ".$EMP[0];
        #echo "<br/>";
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludVACE,$hoy,$EMP[0],$periodo,396,3)";
        $resultado = $mysqli->query($sql11);
    
        
        
        #consulta la ecuacion del concepto del aporte al fondo de pension por parte del empleado en incapacidad general
        $pensionVACE = ($IBCVAC * $ppe) / 100;
        $pensionVACE = ceil($pensionVACE/$redondeo)*$redondeo;
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionVACE,$hoy,$EMP[0],$periodo,397,3)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del concepto del aporte al fondo de salud por parte del patrono en incapacidaad general
        if($exc != 1){

            $TsalVAC = $psp + $pse;
            $saludVACP = ($IBCVAC * $TsalVAC) / 100;
            $saludVACP = ceil($saludVACP/100)*100; 
            $saludVACP = $saludVACP - $saludVACE;      
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludVACP,$hoy,$EMP[0],$periodo,398,3)";
            $resultado = $mysqli->query($sql11);
            
        }else{
            $saludVACP = 0;
        }
        
        #consulta la ecuacion del concepto del ap4orte al fondo de pension por parte del patrono en incapacidad general
        $TpensVAC = $ppp + $ppe;
        $pensionVACP = ($IBCVAC * $TpensVAC) / 100;
        $pensionVACP = ceil($pensionVACP/100)*100;
        $pensionVACP = $pensionVACP - $pensionVACE; 
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionVACP,$hoy,$EMP[0],$periodo,399,3)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del concepto del aporte a la caja de compensacion por parte del patrono en incapacidad general
        $ccfVAC = ($IBCVAC * $pcc) / 100;
        $ccfVAC = ceil($ccfVAC/100)*100;
            
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfVAC,$hoy,$EMP[0],$periodo,405,3)";
        $resultado = $mysqli->query($sql11);
        
        if($exc != 1){
            #consulta la ecuacion del concepto del aporte al fondo de icbf por parte del patrono en incapacidad general
            $icbfVAC = ($IBCVAC * $pic) / 100;
            $icbfVAC = ceil($icbfVAC/100)*100;
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfVAC,$hoy,$EMP[0],$periodo,401,3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de esap por parte del patrono en incapacidad general
            $esapVAC = ($IBCVAC * $pes) / 100;
            $esapVAC = ceil($esapVAC/100)/100;
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapVAC,$hoy,$EMP[0],$periodo,402,3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de ministerio educacion por parte del patrono en incapacidad genral
            $minedVAC = ($IBCVAC * $pmi) / 100;
            $minedVAC = ceil($minedVAC/100)/100;
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedVAC,$hoy,$EMP[0],$periodo,403,3)";
            $resultado = $mysqli->query($sql11);
            
            #consulta la ecuacion del concepto del aporte al fondo de sena por parte del patrono en incapacidad genral
            $senaVAC = ($IBCVAC * $psen) / 100;
            $senaVAC = ceil($senaVAC/100)*100;
            
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaVAC,$hoy,$EMP[0],$periodo,404,3)";
            $resultado = $mysqli->query($sql11);
            
            
        }else{
            $senaVAC        = 0;
            $icbfVAC        = 0;
            $ccfVAC         = 0;
            $esapVAC        = 0;
            $minedVAC       = 0;    
        }
        
    }else{
        $saludVACE      = 0;
        $saludVACP      = 0;
        $pensionVACE    = 0;
        $pensionVACP    = 0;
        $senaVAC        = 0;
        $icbfVAC        = 0;
        $ccfVAC         = 0;
        $esapVAC        = 0;
        $minedVAC       = 0;
    }
  
    
    #* SALARIO
    $salEmp = "SELECT c.salarioactual FROM gn_categoria c
    LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico
    LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico
    WHERE e.id_unico = '$EMP[0]'";

    $SEM = $mysqli->query($salEmp);
    $SE = mysqli_fetch_row($SEM); 
    $salarioactual = $SE[0];

    #Auxilio Transporte
    $AuxA1 = 0;
    if($ppa > 0){
        if($SE[0] < $pta){                
            $AuxA1 = ($ppa);
            $AuxA1 = ROUND($AuxA1);
        
        }
    }
    $AuxilioTransporte = $AuxA1;

    #INCAPACIDADES

    $diasI = incapacidades($EMP[0],$periodo);

  
              
    #consulta si el empleado posee una novedad de días trabajados en el periodo
    $dias_trab = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = '7'";
    $diasT = $mysqli->query($dias_trab);
    $ndias = mysqli_num_rows($diasT);
    
    #valida si el empleado posee la novedad de días trabajados en el periodo
    if($ndias > 0){            
        $DT     = mysqli_fetch_row($diasT);
        $TDT    = $DT[0]- $diasI;
        if($TDT<0){$TDT=0;}
        $sql10 = "UPDATE gn_novedad SET valor = '$TDT' WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 7 ";
        $resultado = $mysqli->query($sql10);
    }else{
        $TDT = $DTR[0] - $diasI;
        if($TDT<0){$TDT=0;}
        $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TDT,$hoy,$EMP[0],$periodo,7,1)";
        $resultado = $mysqli->query($sql10);
    }


    #inserta el salario actual del empleado para el periodo
    $sql10 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($SE[0],$hoy,$EMP[0],$periodo,1,1)";
    $resultado = $mysqli->query($sql10);


    #**+ HORAS EXTRAS
    $sql12="SELECT numerohoras, concepto FROM gn_horas_extras WHERE empleado ='$EMP[0]' AND fecha BETWEEN '$DTR[1]' AND '$DTR[2]' ";
    $result1 = $mysqli->query($sql12);
    $nres = mysqli_num_rows($result1);

    #valida si el empleado posee horas extras
    if($nres > 0){

        while($res = mysqli_fetch_row($result1)){
            $conceptov = '';
            $conceptoh = '';
            if($res[1] == 14){
                $parametro = $recno;
                $conceptov = 43;
                $conceptoh = 14;
            }elseif($res[1] == 426){
                $parametro = $recdom;
                $conceptov = 44;
                $conceptoh = 426;
            }elseif($res[1] == 12){
                $parametro = $hextdo;
                $conceptov = 41; 
                $conceptoh = 12;
            }elseif($res[1] == 13){
                $parametro = $hextno;
                $conceptov = 42;
                $conceptoh = 13;
            }elseif($res[1] == 17){
                $parametro = $hextddf;
                $conceptov = 46;
                $conceptoh = 17;
            }elseif($res[1] == 20){
                $parametro = $hextndf;
                $conceptov = 425;
                $conceptoh = 20;
            }elseif($res[1] == 16){
                $parametro = $hexnoco;
                $conceptov = 45;
                $conceptoh = 16;
            }elseif($res[1] == 15){
                $parametro = $recdom;
                $conceptov = 44;
                $conceptoh = 15;
            }
            if(!empty($conceptov)) { 
                $T = (($SE[0] / 240) * $parametro) * $res[0];
                $T = round($T);
                #inserta el valor de las horas y el valor en dinero en la tabla novedad
                //echo 'Parametro'.$parametro.'**** INSERT****';
                $sql12 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES
                ($T,$hoy,$EMP[0],$periodo,$conceptov,3),
                ($res[0],$hoy,$EMP[0],$periodo,$conceptoh,3)";
                $resultado = $mysqli->query($sql12);
            }
            

        }

    }
    #********#********#
    #* Auxilio Alimentación
    if($ppa > 0){
        if($SE[0] < $pta){
            #valida si el empleado es pensionado 
            if($pens != 1){
                $AuxA = ($ppa * $TDT)/$DTR[0];
                $AuxA = ROUND($AuxA) ;
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($AuxA,$hoy,$EMP[0],$periodo,65,1)";
                $resultado = $mysqli->query($sql11);
            }
        }
    }
    


    #consulta la ecuacion del concepto de ibc 
    $aumIBC = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                WHERE n.empleado = '$EMP[0]' AND n.periodo = '$periodo' AND c.acum_ibc = '1' ";
    $AIBC = $mysqli->query($aumIBC);
    $nacum = mysqli_fetch_row($AIBC);
    
    if(empty($nacum[0]) || $nacum[0] == ""  ){
        $nacum[0] = 0;
    }
    #consulta la ecuacion del concepto de ibcNM 
    $aumIBC2 = "SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                WHERE n.empleado = '$EMP[0]' 
                AND c.acum_nm = '1'
                AND p.fechafin BETWEEN '$DTR[1]' and '$DTR[2]'  ";
    $AIBC2 = $mysqli->query($aumIBC2);
    $nacum2 = mysqli_fetch_row($AIBC2);
    
    if(empty($nacum2[0]) || $nacum2[0] == ""  ){
        $nacum2[0] = 0;
    }
    
    $IBC = ($SE[0] ) * $TDT/$DTR[0];
    $IBC = $IBC + $nacum[0]+$nacum2[0];
    
    if($EMP[7] == 1 || $EMP[7] == 2){
        
        if($SE[0] == 0){

           $IBC = $psm; 

        }
    }       
    $IBC  = ROUND($IBC);


    if ($IBC>=($psm * 10)) {
        $exc = 1;
    }
    $IBCA = $IBC;
    #*******RETROACTIVO **********#
    if(!empty($DTR[6])){
        $vibcr = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
            LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
            LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
            WHERE n.periodo = ".$DTR[6]."
            AND n.empleado = ".$EMP[0]."
            AND p.parametrizacionanno = ".$anno." 
            AND (c.acum_ibc = 1  OR c.codigo ='002')");
        $vlribcr = $vibcr[0][0];

        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($vlribcr,$hoy,$EMP[0],$periodo,703,3)";
        $resultado = $mysqli->query($sql11);
        $IBCA += $vlribcr ;
    }


    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($IBCA,$hoy,$EMP[0],$periodo,78,3)";
    $resultado = $mysqli->query($sql11);
    

    


    #********#********#
    #* Sueldo
     $sueldo = ROUND(($SE[0] * $TDT) / $DTR[0]);
     $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($sueldo,$hoy,$EMP[0],$periodo,2,1)";
     $resultado = $mysqli->query($sql11);
    

    #********#********#
    #* Auxilio de Transporte
    if($pat > 0){
        if($SE[0] < $TopeTrans){
            #valida si el empleado es pensionado
            if($pens != 1){
                $AuxT = ROUND(($pat * $TDT)/$DTR[0]);
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($AuxT,$hoy,$EMP[0],$periodo,66,1)";
                $resultado = $mysqli->query($sql11);
                
            }
            
        }
    }
    #********#********#

    #valida si el empleado es pensionado
    if($pens == 1){
        $saludNME = ($IBCA  * $PE) / 100;
        $saludNME = ceil($saludNME /100) * 100;   
        $pensionNME = 0; 

    }else{

        if($exc == 1){
            $saludNME = ($IBCA  * $pse) / 100;
            $saludNME = ceil($saludNME /100) * 100;     
        }else{
            $saludNME = ($IBCA  * $pse) / 100;
            $saludNME = round($saludNME); 
        }
        
        $vsrts2 = 0;
        $saludNME2 = $saludNME;
        if(!empty($DTR[6])){
            $vsrts2 = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                WHERE n.periodo = ".$DTR[6]."
                AND n.empleado = ".$EMP[0]."
                AND n.concepto = 366");
            $vsrts2     = $vsrts2[0][0];
            
            $saludNME   =$saludNME  - $vsrts2;
        }            

        #consulta la ecuacion del aporte al fondo de pension por parte del empleado de los días trabajados
        $pensionNME = ($IBCA  * $ppe) / 100;
        $pensionNME = round($pensionNME);


        $vsrtp2 = 0;
        $pensionNME2 = $pensionNME;
        if(!empty($DTR[6])){
            
            $vsrtp2 = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                WHERE n.periodo = ".$DTR[6]."
                AND n.empleado = ".$EMP[0]."
                AND n.concepto = 367");
            $vsrtp2         = $vsrtp2[0][0];
            $pensionNME     = $pensionNME  - $vsrtp2;

        }    
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionNME2,$hoy,$EMP[0],$periodo,367,1)";
        $resultado = $mysqli->query($sql11);
    }
    

    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludNME2,$hoy,$EMP[0],$periodo,366,1)";
    $resultado = $mysqli->query($sql11);

    
    
    if($exc == 2 ){
        #valida si el empleado es pensionado
        if($pens != 1){
            if($EMP[7] != 1 AND $EMP[7] != 2){
                #consulta la ecuacion del aporte al fondo de salud por parte del patrono de los días trabajados
                $saludEMPLE = "SELECT valor FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo' AND concepto = 366 ";
                $salEP = $mysqli->query($saludEMPLE);
                $SENM = mysqli_fetch_row($salEP);

                $salT = $pse + $psp;
                $saludNMP = ($IBC  * $salT) / 100;
                $saludNMP = ceil($saludNMP /100) * 100;
                
                #*******RETROACTIVO **********#
                $saludNME2 = $saludNME;
                $vsrts = 0;
                if(!empty($DTR[6])){
                    $saludNMP = ($IBCA  * $salT) / 100;
                    $saludNMP = ceil($saludNMP /100) * 100;

                    $vsrts = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                        WHERE n.periodo = ".$DTR[6]."
                        AND n.empleado = ".$EMP[0]."
                        AND n.concepto = 366");
                    $vsrts = $vsrts[0][0];

                }            
                $saludNMP = $saludNMP - $saludNME - $vsrts;
                
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludNMP,$hoy,$EMP[0],$periodo,368,1)";
                $resultado = $mysqli->query($sql11);
                
            }
            
        }else{
            $saludNMP = 0;

        }
        
    }else{
        $saludNMP = 0;
    }

   
    if($EMP[7] == 1 || $EMP[7] == 2){
        
        
        if($SE[0] > 0){
           
            #consulta la ecuacion del aporte al fondo de salud por parte del patrono de los días trabajados
            $salT = $pse + $psp;
            $saludNMP = ($IBCA  * $salT) / 100;
            $saludNMP = ceil($saludNMP /100) * 100;
                
            $TsaludNMP = $saludNMP;

            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TsaludNMP,$hoy,$EMP[0],$periodo,368,1)";
            $resultado = $mysqli->query($sql11);    



        }else{

            $saludNMP = 0;

        }
        
        
    }
    

    
    
    if($EMP[7] == 1 || $EMP[7] == 2){
        $pensionNMP = 0;
        
    }else{
        #valida si el empleado es pesionado
        if($pens != 1){
            $penT = $ppp + $ppe; 
            $pensionNMP = ($IBC  * $penT) / 100;
            $pensionNMP = ceil($pensionNMP /100) * 100;
            
            $pensionNME2 = $pensionNME;
            $vsrt = 0;
            if(!empty($DTR[6])){
                $pensionNMP = ($IBCA  * $penT) / 100;
                $pensionNMP = ceil($pensionNMP /100) * 100;
                $pensionNME2 = $pensionNME;

                $vsrt = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
                    WHERE n.periodo = ".$DTR[6]."
                    AND n.empleado = ".$EMP[0]."
                    AND n.concepto = 367");
                $vsrt = $vsrt[0][0];

            }            
            $pensionNMP = $pensionNMP - $pensionNME -$vsrt;
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionNMP,$hoy,$EMP[0],$periodo,369,1)";
            $resultado = $mysqli->query($sql11);


        }else{
            $pensionNMP = 0;
        }
    }
    
    if($pens !=1){
        #consulta la ecuacion del aporte al fondo de arl por parte del patrono de los días trabajados
        $arlNMP = ($IBCA * $EMP[8]) / 100;
        $arlNMP = ceil($arlNMP / 10) * 10;
        $AR = intval($arlNMP);
        $dec6 = substr($AR,-2); 
        $dec6 = intval($dec6);
       
        if($dec6 != 0){

            $arlNMP = ceil($arlNMP / 100)*100;
        }
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($arlNMP,$hoy,$EMP[0],$periodo,363,1)";
        $resultado = $mysqli->query($sql11);
        
        if($exc == 2){

            if($EMP[7] == 1 || $EMP[7] == 2){

                $minedNMP   = 0;
                $senaNMP       = 0;
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
               
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfNMP,$hoy,$EMP[0],$periodo,371,1)";
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
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esapNMP,$hoy,$EMP[0],$periodo,372,1)";
                    $resultado = $mysqli->query($sql11);
                 
                }

                #consulta la ecuacion del aporte al fondo de sena por parte del patrono de los días trabajados
                $senaNMP = ($IBC * $psen) / 100;
                $senaNMP = ceil($senaNMP/10)*10;

                $SE = intval($senaNMP);
                $dec2 = substr($SE,-2);
                $dec2 = intval($dec2);
                if($dec2 != 0){

                    $senaNMP  = ceil($senaNMP / 100)*100;
                    #echo " por: ".$psen." sena: ".$senaNMP;
                    #echo "<br/>";
                }
               
                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaNMP,$hoy,$EMP[0],$periodo,373,1)";
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
                    
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($minedNMP,$hoy,$EMP[0],$periodo,374,1)";
                    $resultado = $mysqli->query($sql11);
                }       
            }
            
        }else{
            $minedNMP   = 0;
            $senaNMP    = 0;
            $esapNMP    = 0;
            $icbfNMP    = 0;       
        }

        if($EMP[7] == 1 || $EMP[7] == 2){
            if($SE[0] > 0){
                #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
                $ccfNMP = ($IBCA * $pcc) / 100;
                $ccfNMP = ceil($ccfNMP/100)*100;

                $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfNMP,$hoy,$EMP[0],$periodo,375,1)";
                $resultado = $mysqli->query($sql11);
                
            }else{
                $ccfNMP = 0;
            }
            

        }else{
            #consulta la ecuacion del aporte al fondo de ccf por parte del patrono de los días trabajados
            $ccfNMP = ($IBCA * $pcc) / 100;
            $ccfNMP = ceil($ccfNMP/100)*100;

            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfNMP,$hoy,$EMP[0],$periodo,375,1)";
            $resultado = $mysqli->query($sql11);
            
        }
        
    }else{
        $ccfNMP     = 0;
        $minedNMP   = 0;
        $senaNMP    = 0;
        $esapNMP    = 0;
        $icbfNMP    = 0;
        $arlNMP     = 0;
    }
    
    #valida que tipo de provision se debe ejecutar
    if($Tprovi = 1){
        #inserta las novedades de provision
        #$pens = 2;
        if($pens !=1){
            if($EMP[7] == 1 || $EMP[7] == 2){
                $PBS = 0; 
                $PPS = 0; 
                $PPV = 0; 
                $PVA = 0;  
                $PPN = 0; 
                $PIC = 0; 
                $PAC = 0;
            }else{
                $salarioAct = "SELECT c.salarioactual FROM gn_categoria c 
                            LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico 
                            LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico
                            WHERE e.id_unico = $EMP[0]";
                $SalAct = $mysqli->query($salarioAct);
                $SA = mysqli_fetch_row($SalAct);

                if($SA[0] <= ($psm * 2)){
                    $auxT = $pat;
                    $auxA = $ppa;
                    $por  = 50;
                }else{
                    $auxT = 0;
                    $auxA = 0;
                    $por  = 35;

                }
                
                $PBS = ((($SA[0] + $auxT + $auxA) * $por) / 100) / 12;
                $PBS = round($PBS / 1 ) * 1;
                $PPS = ($SA[0] + $PBS + $auxT + $auxA) / 12;
                $PPS = round($PPS / 1 ) * 1;
                $PPV = (($SA[0] / 2) + $PBS + $PPS + $auxT + $auxA) / 12;
                $PPV = round($PPV / 1 ) * 1;
                $PVA = (($SA[0] / 2) + $PBS + $PPS + $auxT + $auxA) / 12;
                $PVA = round($PVA / 1 ) * 1;
                $PPN = ($SA[0] + $PPV + $PBS + $PPS + $auxT + $auxA) / 12;
                $PPN = round($PPN / 1 ) * 1;
                

                if($EMP[9] != 2 ){
                    $PIC = (($SA[0] + $PBS + $PPS + $PPV + $PPN + $auxT + $auxA) * 1) / 100; 
                    $PIC = round($PIC / 1 ) * 1;
                }else{
                    $PIC = 0;
                }

                $PAC = ($SA[0] + $PBS + $PPS + $PPV + $PPN + $auxT + $auxA) / 12;
                $PAC = round($PAC / 1 ) * 1;
                
                $TOT = $PBS + $PPS + $PPV + $PVA + $PPN + $PIC + $PAC;
            }
        }
    }else{
        $PBS = 0; 
        $PPS = 0; 
        $PPV = 0; 
        $PVA = 0;  
        $PPN = 0; 
        $PIC = 0; 
        $PAC = 0;
         $Sal = "SELECT c.salarioactual FROM gn_categoria c 
            LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico 
            LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico 
            WHERE e.id_unico = '$EMP[0]' ";
    
        $SA = $mysqli->query($Sal);
        $S = mysqli_fetch_row($SA);
       
        if($S[0] >= ($psm * 2)){
          $axu_tra = 0;
        }else{
            $axu_tra = $pat;
        }
        $salario_base=$m[1];
        
        $PBS = 0; 
        //prima de servicios
        $PPS = $salario_base * 0.0833;
        $PPS = round($PPS / 1 ) * 1;
        $PPV = 0; 
        //vacaciones
        $PVA = $S[0] * 0.0417;
        $PVA = round($PVA / 1 ) * 1;
        $PPN = 0; 
        //cesantias
        $PAC = $salario_base * 0.0833;
        //intereses de cesantias
        $PIC = $PAC * 0.01;
        $PAC = round($PAC / 1 ) * 1;
        $PIC = round($PIC / 1 ) * 1;
    }
    
     //echo "<br/>";
            //echo "sal: ".$SA[0]." emple: ".$EMP[0]." pbs: ".$PBS." pps: ".$PPS." ppv: ".$PPV." pva: ".$PVA." ppn: ".$PPN." pic: ".$PIC." pac: ".$PAC ;
            //echo "<br/>";
    if($PBS > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PBS,$hoy,$EMP[0],$periodo,269,1)";
            $resultado = $mysqli->query($sql11);
    }

    if($PPS > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PPS,$hoy,$EMP[0],$periodo,268,1)";
            $resultado = $mysqli->query($sql11);
    }
    if($PPV > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PPV,$hoy,$EMP[0],$periodo,266,1)";
            $resultado = $mysqli->query($sql11);
    }

    if($PVA > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PVA,$hoy,$EMP[0],$periodo,265,1)";
            $resultado = $mysqli->query($sql11);
    }
    if($PPN > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PPN,$hoy,$EMP[0],$periodo,267,1)";
            $resultado = $mysqli->query($sql11);
    }
    if($PIC > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PIC,$hoy,$EMP[0],$periodo,264,1)";
            $resultado = $mysqli->query($sql11);
    }
    if($PAC > 0){
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($PAC,$hoy,$EMP[0],$periodo,263,1)";
            $resultado = $mysqli->query($sql11);
    }
    
    //echo "empleado: ".$EMP[0]." salario: ".$SE[0]." bonifica Ser: ".$PBS." prima ser: ".$PPS." prima vac: ".$PPV." vacaciones: ".$PVA." prima nav: ".$PPN." interes C: ".$PIC." auxilio C: ".$PAC." TOTAL: ".$TOT;
    //    echo "<br/>";
        
    ////CALCULO DE VALORES TOTALES//////////
    
    
    $TIBC = $IBCA + $IBCVAC ;//+ $IBCIGE + $IBCIRL  + $IBCLMA;
    
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
        if($TIBC >= ($psm * 4)){
            $pfs1 = $pfs / 2;
            $FSNM1 = ($TIBC * $pfs1) / 100;
            $FSNM1 = ceil($FSNM1 / 100) * 100;
        }
       
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($FSNM1,$hoy,$EMP[0],$periodo,361,1),($FSNM1,$hoy,$EMP[0],$periodo,362,1);";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion del aporte al fondo de solidaridad pensional 
        $TFS = $FSNM1 + $FSNM1;

        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($TFS,$hoy,$EMP[0],$periodo,81,1)";
        $resultado = $mysqli->query($sql11);
    }

    
    #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del empleado
    $saludE = $saludNME + $saludVACE;//+ $saludIRLE + $saludVACE + $saludIGEE + $saludLMAE ;
    //echo $saludNME.' - '.$saludIRLE.' - '.$saludVACE.' - '.$saludIGEE.' - '.$saludLMAE.'<br/>';
    
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludE,$hoy,$EMP[0],$periodo,80,1)";
    $resultado = $mysqli->query($sql11);
    
    #consulta la ecuacion de la suma total del aporte al fondo de pension por parte del empleado
    $pensionE = $pensionNME + $pensionVACE;//+$pensionIRLE + $pensionVACE + $pensionIGEE + $pensionLMAE;
    //echo $pensionNME.' - '.$pensionIRLE.' - '.$pensionVACE.' - '.$pensionIGEE.' - '.$pensionLMAE;
    
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionE,$hoy,$EMP[0],$periodo,84,1)";
    $resultado = $mysqli->query($sql11);
    
    #consulta la ecuacion de la suma total del aporte al fondo de salud por parte del patrono
    $saludP = $saludNMP + $saludVACP;//+ $saludIRLP + $saludVACP + $saludIGEP + $saludLMAP + $saludLMAP ;
    


    
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($saludP,$hoy,$EMP[0],$periodo,82,1)";
    $resultado = $mysqli->query($sql11);
    
    #consulta la ecuacion de la suma total del aporte al fondo de pensión por parte del patrono
    $pensionP = $pensionNMP + $pensionVACP;//+ $pensionIRLP + $pensionVACP + $pensionIGEP + $pensionLMAP; 
    
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($pensionP,$hoy,$EMP[0],$periodo,83,1)";
    $resultado = $mysqli->query($sql11);
    
    #consulta la ecuacion de la suma total del aporte al fondo de caja de compensación familiar  por parte del patrono
    $ccfP = $ccfNMP + $ccfVAC + $ccfIRL + $ccfIGE + $ccfVAC + $ccfLMA ;
        
    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($ccfP,$hoy,$EMP[0],$periodo,256,1)";
    $resultado = $mysqli->query($sql11);
    
    #valida si la empresa está excenta de parafiscales 
    if($exc != 1){
        
        #consulta la ecuacion de la suma total del aporte al fondo escuela de adminstración pública por parte del patrono
        if($pes > 0){
            $esaP = $esapNMP + $esapVACE;// + $esapIRL + $esapIGE + $esapVACE + $esapLMA; 
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($esaP,$hoy,$EMP[0],$periodo,259,1)";
            $resultado = $mysqli->query($sql11);
        }
        
        
        #consulta la ecuacion de la suma total del aporte al fondo de sena  por parte del patrono
        $senaP = $senaNMP + $senaVACE;//+ $senaIRL + $senaIGE + $senaVACE + $senaLMA;
        
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($senaP,$hoy,$EMP[0],$periodo,257,1)";
        $resultado = $mysqli->query($sql11);
        
        #consulta la ecuacion de la suma total del aporte al fondo de ministerio de educación por parte del patrono
        if($pmi > 0){
            $mineducP = $minedNMP + $minedVACE;//+ $minedIRL + $minedIGE + $minedVACE + $minedLMA;
            $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($mineducP,$hoy,$EMP[0],$periodo,258,1)";
            $resultado = $mysqli->query($sql11);
        }
    
        #consulta la ecuacion de la suma total del aporte al fondo de instituto colombiano de bienestar familiar  por parte del patrono
        $icbfP = $icbfNMP + $icbfVAC;// + $icbfIGE + $icbfIRL + $icbfVAC + $icbfLMA;
    
        $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($icbfP,$hoy,$EMP[0],$periodo,260,1)";
        $resultado = $mysqli->query($sql11);
        
    }        
    
    #* BONIFICACION 
    if($aplicaB==1){
        $mes = $DTR[3];
        $year = $DTR[4];
        $mesv = "SELECT fechaacto, estado,month(fechaacto),year(fechaacto) FROM `gn_vinculacion_retiro` where empleado = $EMP[0]  ORDER BY fechaacto DESC LIMIT 1";
        $mesv = $mysqli->query($mesv);
        $mesv = mysqli_fetch_row($mesv);

        if($mesv[1]==1){
            if($mesv[2]==$mes && $mesv[3]<$year){
                $bon =0;
                if($sueldo>1395158){
                    $bon =  ROUND(($SE[0]*35)/100);
                } else {
                    $bon =  ROUND(($SE[0]*50)/100);
                }
                if($bon>0){
                    $sql11 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES($bon,$hoy,$EMP[0],$periodo,111,1)";
                            $resultado = $mysqli->query($sql11);    
                }
            }
        }
    }


    #* RETENCION
    retencion($EMP[0],$periodo);

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