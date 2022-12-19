<?php
#03/03/2017 --- Nestor B --- se agregó una condición de la validación de la fecha de aprobación
#13/06/2017 --- Nestor B --- se agregó la validación cuando los días de incapacidad no sobrepasan los días restantes del periodo
#25-08-2017 --- Nestor B --- se agregó la validación de los tipo de incapacidad para que inserte los ibc respectivos y los dias de la incapcidad y ls dias trabajados 

require_once '../Conexion/conexion.php';
require '../Dias_Incapacidad.php';
@session_start();

$empleado         = ''.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tiponovedad = "null";
else
    $tiponovedad      = ''.$mysqli->real_escape_string(''.$_POST['txtidTip'].'').'';

if(empty($_POST['sltAccidente']))
    $accidente = "null";
else
    $accidente           = '"'.$mysqli->real_escape_string(''.$_POST['sltAccidente'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroI'].'')=="")
    $numeroinc = "null";
else
    $numeroinc = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroI'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroA'].'')=="")
    $numeroaprobacion = "null";
else
    $numeroaprobacion = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroD'].'')=="")
    $numerodias = "null";
else
    $numerodias       = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'';

if($mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'')=="")
    $diagnostico = "null";
else
    $diagnostico      = '"'.$mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltConcepto'].'')==""){
    $concepto = "null";
}else{
    $concepto = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
}
#valida si la fecha inicial viene vacia
if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')=="")
    $fechainicio = "null";
else
{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechainicio = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';    
    $fecI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
}
if($mysqli->real_escape_string(''.$_POST['sltFechaF'].'')=="")
    $fechaf = "null";
else
{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechaf = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';    
}
#valida si la fehca de aprobación viene vacía 
if(empty($_POST['sltFechaA'])||$mysqli->real_escape_string(''.$_POST['sltFechaA'].'')=="")
    $fechaaprobacion = "null";
else
{
    $fec2 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
    $fecha2 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha2);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fechaaprobacion = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';    
}
$hoy = date('d-m-Y');
$hoy = trim($hoy, '"');
$fecha_divi = explode("-", $hoy);
$anio = $fecha_divi[2];
$mes = $fecha_divi[1];
$dia = $fecha_divi[0];
$hoy = '"'.$anio.'-'.$mes.'-'.$dia.'"';


$numD = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'';

$diasFinal = DiasFecha($fecI, $numD);



#valida que exista una periodo que no se encuentre cerrado cuya fecha inicial sea menor a la fecha de inicio de la incapacidad
 $Fper = "SELECT id_unico, fechainicio, fechafin, liquidado FROM gn_periodo where fechainicio <= $fechainicio AND fechafin >= $fechainicio  AND liquidado !=1 AND id_unico !=1 and tipoprocesonomina = 1";
$fecp = $mysqli->query($Fper);
$nfecp = mysqli_num_rows($fecp);

if($nfecp > 0){

    $fp = mysqli_fetch_row($fecp);

    #calcula cuantos dias transcurren desde la fecha de inicio de la incapacidad hasta la fecha final del periodo
    //echo $fecI.'FI<br/>';
    //echo $fp[2].'FFP<br/>';
    $dias = (strtotime($fecI)-strtotime($fp[2]))/86400;
    $dias = abs($dias);
    $dias = floor($dias);
    $dias = $dias + 2;
    /*
    echo $numD.'<br/>';
    echo $dias.'<br/>';
    var_dump($numD > $dias);*/
    #valida si el numero de dias de la incapacidad es mayor al numero de dias transcurridos desde el inicio de la incapacidad hasta la fecha final del periodo
    if($numD > $dias){
    
        $DiasFal  = $numD - $dias;
        $TotalD  = $numD - $DiasFal;
    
        #inserta la incapacidad
        $sql = "INSERT INTO gn_incapacidad (empleado,tiponovedad,numeroinc,fechainicio,numerodias,numeroaprobacion,fechaaprobacion,diagnostico,concepto,accidente,fechafinal) VALUES 
        ($empleado,$tiponovedad,$numeroinc,$fechainicio,$numerodias,$numeroaprobacion,$fechaaprobacion,$diagnostico,$concepto,$accidente,$fechaf)";
        $resultado = $mysqli->query($sql);
        
        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
        $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($TotalD,$fechainicio,$empleado,$fp[0],$concepto,4)";
        $resultado = $mysqli->query($sql1);
    
        $fechaini = $fecI;  
        $fp[0]=$fp[0]+1;

        $sal = "SELECT ca.salarioactual FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                    WHERE e.id_unico = '$empleado'";
        $sala = $mysqli->query($sal);
        $salemp = mysqli_fetch_row($sala);
        
        if($tiponovedad == 1){

            $IBCLMA = ($saLemp[0] / 30) * $TotalD;
            $IBCLAM = round($IBCLMA /1000);
            $IBCLMA = $IBCLMA * 1000;

             $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLMA,$fechainicio,$empleado,$fp[0],417,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$TotalD;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $TotalD;
                #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }


        }elseif($tiponovedad == 2){

            $IBCLR = ($salemp[0] / 30)* $TotalD;
            $IBCLR = round($IBCLR /1000);
            $IBCLR = $IBCLR * 1000;

            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLR,$fechainicio,$empleado,$fp[0],420,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$TotalD;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $TotalD;
                #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }

        }elseif($tiponovedad == 4){

           
            $IBCIGE = ($salemp[0] / 30) * $TotalD;        
            $IBCIGE = round($IBCIGE/1000);
            $IBCIGE = $IBCIGE * 1000;
            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIGE,$fechainicio,$empleado,$fp[0],418,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$TotalD;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $TotalD;
                #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }
            
        }else{

           
            $IBCIRL = ($salemp[0] / 30) * $TotalD;        
            $IBCIRL = round($IBCIRL/1000);
            $IBCIRL = $IBCIRL * 1000;
             $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIRL,$fechainicio,$empleado,$fp[0],419,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$TotalD;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $TotalD;
                #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }
        }
        #minetras que los dias de incapacidad faltantes sean mayor a 0 
        while($DiasFal >0){
        
          
            #separa por año, mes y dia la fecha
            $fechapos = explode("-", $fechaini);
            $anio2 = $fechapos[0];
            $mes2 = $fechapos[1];
            $dia2 = $fechapos[2];
        
            if($mes2 == 12){
                $mes2 = 01;
                $anio2 = $anio2 +1;
            }else{
           
                $val = 1;
                #$fechaini = date('Y-m-j');
                $fechaini = strtotime ( $val.'month' , strtotime ( $fechaini ) ) ;
                $fechaini = date ( 'Y-m-j' , $fechaini );
            
            }
        
        
            $fechapost = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';
            # consulta los periodos que coincidan la fecha final de la incapacidad que no esten cerrados
            $sql1 = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE  id_unico = $fp[0]";
	
            $res1 = $mysqli->query($sql1);
            $nres = mysqli_num_rows($res1);
       
            if($nres >=1){
            
                $fp[0]=$fp[0]+1;
                $dmes = mysqli_fetch_row($res1);
                $diasm = (strtotime($dmes[2])-strtotime($dmes[3]))/86400;
                $diasm = abs($diasm);
                $diasm = floor($diasm);
                $diasm = $diasm + 1;
            
            
                if($diasm > $DiasFal){
                
                    #inserta la novedad en el periodo posterior con los dias de la incapacidad faltantes
                    $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DiasFal,$fechainicio,$empleado,$dmes[0],$concepto,4)";
                    $resultado = $mysqli->query($sql2);
                    
                    if($tiponovedad == 1){

                        $IBCLMA = ($saLemp[0] / 30) * $DiasFal;
                        $IBCLAM = round($IBCLMA /1000);
                        $IBCLMA = $IBCLMA * 1000;

                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLMA,$fechainicio,$empleado,$fp[0],417,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$DiasFal;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $DiasFal;
                
                            #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }


                    }elseif($tiponovedad == 2){

                        $IBCLR = ($salemp[0] / 30)* $DiasFal;
                        $IBCLR = round($IBCLR /1000);
                        $IBCLR = $IBCLR * 1000;

                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLR,$fechainicio,$empleado,$fp[0],420,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$DiasFal;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $DiasFal;
                
                            #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }

                    }elseif($tiponovedad == 4){

           
                        $IBCIGE = ($salemp[0] / 30) * $DiasFal;        
                        $IBCIGE = round($IBCIGE/1000);
                        $IBCIGE = $IBCIGE * 1000;
                    
                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIGE,$fechainicio,$empleado,$fp[0],418,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$DiasFal;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $DiasFal;
                
                            #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }
            
                    }else{

           
                        $IBCIRL = ($salemp[0] / 30) * $DiasFal;        
                        $IBCIRL = round($IBCIRL/1000);
                        $IBCIRL = $IBCIRL * 1000;
                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIRL,$fechainicio,$empleado,$fp[0],419,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$DiasFal;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $DiasFal;
                
                            #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }
                    }

                    $DiasFal = 0;
                }else{
                
                    $DiasFal = $DiasFal - $diasm;
                
                    #inserta la novedad en el periodo posterior con los dias de la incapacidad faltantes
                    $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($diasm,$fechainicio,$empleado,$dmes[0],$concepto,4)";
                    $resultado = $mysqli->query($sql2);
                    
                    if($tiponovedad == 1){

                        $IBCLMA = ($saLemp[0] / 30) * $diasm;
                        $IBCLAM = round($IBCLMA /1000);
                        $IBCLMA = $IBCLMA * 1000;

                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLMA,$fechainicio,$empleado,$fp[0],417,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$diasm;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $diasm;
                
                            #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }


                    }elseif($tiponovedad == 2){

                        $IBCLR = ($salemp[0] / 30)* $diasm;
                        $IBCLR = round($IBCLR /1000);
                        $IBCLR = $IBCLR * 1000;

                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLR,$fechainicio,$empleado,$fp[0],420,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$diasm;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $diasm;
                
                            #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }

                    }elseif($tiponovedad == 4){

           
                        $IBCIGE = ($salemp[0] / 30) * $diasm;        
                        $IBCIGE = round($IBCIGE/1000);
                        $IBCIGE = $IBCIGE * 1000;
                    
                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIGE,$fechainicio,$empleado,$fp[0],418,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$diasm;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $diasm;
                
                            #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }
            
                    }else{

           
                        $IBCIRL = ($salemp[0] / 30) * $diasm;        
                        $IBCIRL = round($IBCIRL/1000);
                        $IBCIRL = $IBCIRL * 1000;
                        $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIRL,$fechainicio,$empleado,$fp[0],419,4)";
                        $resultado = $mysqli->query($nov);

                        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
                        $Tdi = $mysqli->query($diT);
                        $nTdi = mysqli_num_rows($Tdi);

                        if($nTdi > 0){

                            $TraD = mysqli_fetch_row($Tdi);
                            $DiasTr = $TraD[1]-$diasm;

                            $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
                        }else{

                            $DLab = 30 - $diasm;
                
                            #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                            $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                            $resultado = $mysqli->query($sql2); 
                        }
                    }
                }
         
            }else{
             
                $calendario = CAL_GREGORIAN;
            
                $fechaposte = explode("-", $fechaini);
                $anio3 = $fechaposte[0];
                $mes3 = $fechaposte[1];
                $dia3 = $fechaposte[2];
            
                $diaF = cal_days_in_month($calendario, $mes3, $anio3); 
            
                $fp[0]=$fp[0]-1;
                #consulta cual es el periodo en que se registro la novedad
                $perant = "SELECT id_unico, fechainicio, fechafin FROM gn_periodo WHERE id_unico = $fp[0]";
                $peant = $mysqli->query($perant);
                $peran = mysqli_fetch_row($peant);
            
                #calacula los dias del periodo 
                $diasp = (strtotime($peran[1])-strtotime($peran[2]))/86400;
                $diasp = abs($diasp);
                $diasp = floor($diasp);
                $diasp = $diasp + 1;
            
                #valida el numero de dias del periodo
                if($diasp >=28){
                
                    $diai = '01';
                    $fechainiciop = $anio3.'-'.$mes3.'-'.$diai;
                    $fechafinp = $anio3.'-'.$mes3.'-'.$diaF;
                    $mesnom = int($fechapost[1]);
                    $nombremes = ['no','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                    $inserperiodo = "INSERT INTO gn_periodo(codigointerno,fechainicio,fechafin,acumulable,estado,parametrizacionanno,tipoprocesonomina,liquidado)"
                        . "VALUES('null','$fechainiciop','$fechafinp','null',2,1,1,0)";
                    $resultado = $mysqli->query($inserperiodo);
                    $fp[0]=$fp[0]+1;
                }else{
               
                    #divide la fecha para saber cual fue el ultimo dia del periodo 
                    $fechaposter = explode("-", $peran[2]);
                    $anio4 = $fechaposter[0];
                    $mes4 = $fechaposter[1];
                    $dia4 = $fechaposter[2];
                
                    $diaFi = cal_days_in_month($calendario, $mes4, $anio4);
                
                    #valida si la fecha final del periodo es el ultimo dia del mes
                    if($diaFi = $dia4){
                    
                        $diai = '01';
                        $val = 1;
                
                        #$fechaini = date('Y-m-j');
                        $fechaposter = strtotime ( $val.'month' , strtotime ( $fechaposter ) ) ;
                        $fechaposter = date ( 'Y-m-j' , $fechaposter );
                    
                        #separa la fecha por año, mes y dia
                        $fechaposteri = explode("-", $fechaposter);
                        $anio5 = $fechaposteri[0];
                        $mes5 = $fechaposteri[1];
                        $dia5 = $fechaposteri[2];
                    
                        #reordena la fecha
                        $fechainiperio = $anio5.'-'.$mes5.'-'.$diai;
                    
                        #funcion que le suma dias a la fecha
                        $fechafinperio = DiasFecha($fechainiperio, 15); 
                    
                        #consulta a la base de datos para insertar el periodo
                        $inserperiodo = "INSERT INTO gn_periodo(codigointerno,fechainicio,fechafin,acumulable,estado,parametrizacionanno,tipoprocesonomina,liquidado)"
                            . "VALUES('null','$fechainiperio','$fechafinperio','null',2,1,1,0)";
                        $resultado = $mysqli->query($inserperiodo);
                    
                        #aumenta en uno el id del periodo
                        $fp[0]=$fp[0]+1;
                
                    
                    }else{
                    
                        #funcion que le suma dias a la fecha 
                        $fechainiperio = DiasFecha($peran[2], 1);
                    
                        #funcion que retorna el ultimo dia del mes dependiendo del año y del mes
                        $diaFip = cal_days_in_month($calendario, $mes4, $anio4);
                    
                        #reordena la fehca final del periodo a insertar
                        $fechafinperio = $anio4.'-'.$mes4.'-'.$diaFip;
                    
                        #consulta para insertar el periodo
                        $inserperiodo = "INSERT INTO gn_periodo(codigointerno,fechainicio,fechafin,acumulable,estado,parametrizacionanno,tipoprocesonomina,liquidado)"
                            . "VALUES('null','$fechainiperio','$fechafinperio','null',2,1,1,0)";
                        $resultado = $mysqli->query($inserperiodo);
                    
                        #aumenta en uno el id del periodo
                        $fp[0]=$fp[0]+1;
                    }
                
                
                
                } 
            }
        
        
        }
    }else{
        
        $sal = "SELECT ca.salarioactual FROM gn_empleado e LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                    WHERE e.id_unico = '$empleado'";
        $sala = $mysqli->query($sal);
        $salemp = mysqli_fetch_row($sala);
        
        if($tiponovedad == 1){

            $IBCLMA = ($saLemp[0] / 30) * $numerodias;
            $IBCLAM = round($IBCLMA /1000);
            $IBCLMA = $IBCLMA * 1000;

            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLMA,$fechainicio,$empleado,$fp[0],417,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$numerodias;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $numerodias;
                #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }


        }elseif($tiponovedad == 2){

            $IBCLR = ($salemp[0] / 30)* $numerodias;
            $IBCLR = round($IBCLR /1000);
            $IBCLR = $IBCLR * 1000;

            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCLR,$fechainicio,$empleado,$fp[0],420,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$numerodias;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $numerodias;
                #inserta la novedad de los dias trabajados en el periodo actual menos los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }

        }elseif($tiponovedad == 4){

           
            $IBCIGE = ($salemp[0] / 30) * $numerodias;        
            $IBCIGE = round($IBCIGE/1000);
            $IBCIGE = $IBCIGE * 1000;
            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIGE,$fechainicio,$empleado,$fp[0],418,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$numerodias;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $numerodias;
                #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }
            
        }else{

           
            $IBCIRL = ($salemp[0] / 30) * $numerodias;        
            $IBCIRL = round($IBCIRL/1000);
            $IBCIRL = $IBCIRL * 1000;
            $nov = "INSERT INTO gn_novedad (valor,fecha,empleado,periodo,concepto,aplicabilidad)VALUES ($IBCIRL,$fechainicio,$empleado,$fp[0],419,4)";
            $resultado = $mysqli->query($nov);

            $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
            $Tdi = $mysqli->query($diT);
            $nTdi = mysqli_num_rows($Tdi);

            if($nTdi > 0){

                $TraD = mysqli_fetch_row($Tdi);
                $DiasTr = $TraD[1]-$numerodias;

                $act = "UPDATE gn_novedad SET valor = '$DiasTr' WHERE id_unico = '$DiasTr'";
            }else{

                $DLab = 30 - $numerodias;
                #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
                $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DLab,$fechainicio,$empleado,$fp[0],7,4)";
                $resultado = $mysqli->query($sql2); 
            }
        }
        
        #inserta la incapacidad
        $sql = "INSERT INTO gn_incapacidad (empleado,tiponovedad,accidente,numeroinc,fechainicio,numerodias,numeroaprobacion,fechaaprobacion,diagnostico,concepto,fechafinal) VALUES 
        ($empleado,$tiponovedad,$accidente,$numeroinc,$fechainicio,$numerodias,$numeroaprobacion,$fechaaprobacion,$diagnostico,$concepto,$fechaf)";
        $resultado = $mysqli->query($sql);
        
        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
        $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($numD,$fechainicio,$empleado,$fp[0],$concepto,4)";
        $resultado = $mysqli->query($sql1);

        #consulta los dias laborados del empleado en el periodo 
        $diT = "SELECT id_unico,valor FROM gn_novedad WHERE empleado ='$empleado' AND periodo ='$fp[0]' AND concepto = '7'";
        $Tdi = $mysqli->query($diT);
        $nTdi = mysqli_fetch_row($Tdi);

        #calcula el IBC de los dias trabajados del empleado en el periodo      
        $IBC = ($salemp[0] / 30) * $nTdi[1];        
        $IBC = round($IBC/1000);
        $IBC = $IBC * 1000;

        #inserta la novedad de los dias trabajados en el periodo actual con los dias de la incapacidad transcurridos
        $sql3 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($IBC,$fechainicio,$empleado,$fp[0],421,4)";
        $resultado = $mysqli->query($sql3);
        
    }
    
    $x = 0;
}else{
    
    $resultado = 0;
    $x = 1;
    
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
            <?php if($x == 0){ ?>
                        <p>No se ha podido guardar la información.</p>
            <?php }else{ ?>
                        <p>No se ha podido guardar la información, debido a que el periodo en el que intenta registrar la novedad se encuentra cerrado</p>
            <?php } ?>            
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
      <!--Modal para informar al usuario que no se ha podido registrar la incapacidad porque los dias de la misma sobrepasan los dias faltantes del periodo y se necesita crear uno -->
  <div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información, debido a que los día de la incapacidad sobrepasan a los días faltantes del periodo. 
              Por favor cree otro periodo para poder registrar la incapacidad.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
 
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->

<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
        window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
      //window.history.go(-1);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
         window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
  });
</script>
<?php } 
?>