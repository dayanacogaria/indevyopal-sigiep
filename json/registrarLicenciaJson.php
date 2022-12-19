<?php
#03/03/2017 --- Nestor B --- se agregó una condición de la validación de la fecha de aprobación
#13/06/2017 --- Nestor B ---  se agregó la validación cuando los días de incapacidad no sobrepasan los días restantes del periodo

require_once '../Conexion/conexion.php';
require '../Dias_Incapacidad.php';
@session_start();

$empleado         = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tiponovedad = "null";
else
    $tiponovedad      = '"'.$mysqli->real_escape_string(''.$_POST['txtidTip'].'').'"';

/*if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')=="")
    $estado = "null";
else
    $estado           = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
*/
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
    $numerodias       = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'"';

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
}
if($mysqli->real_escape_string(''.$_POST['sltFechaF'].'')=="")
    $fechaf = "null";
else
{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fechaf = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';    
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

$fecI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';
$numD = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'';

$diasFinal = DiasFecha($fecI, $numD);
$id_p1 = "";
$fechainicio_ant = "";
$id_p1 = "";
#revisar si los periodos creados alcanzan para la Liccencia
$meses_lic = $numD/30;
$meses_lic = round($meses_lic)+1;
$reg = 0;

if(!empty($fechainicio)){

    $ms_fal= 12 - $mes1;    
    if($ms_fal<$meses_lic){
        /*veificar el año siguiente*/
        $ano_siguiente = $anio1 + 1;
        $sql_vig_sig = "SELECT * FROM gf_parametrizacion_anno WHERE anno = '$ano_siguiente'";
        $vig_sg = $mysqli->query($sql_vig_sig);
        $nvigs = mysqli_num_rows($vig_sg);
        if($nvigs>0){
             $reg = 1;
        }else{
            $reg = 0;
            $resultado = 'vigencia';
        }
        
    }else{
         $reg = 1;
    }

   
}else{
    $reg = 0;
    $resultado = 'fecha_inicio';
}
if($reg == 1){
    //$DiasFal = $numD;
        for($c=1;$c<=$meses_lic;$c++){
            

            if($c==1){
                #valida que exista una periodo que no se encuentre cerrado cuya fecha inical sea menor a la fecha de inicio de la incapacidad
                $Fper = "SELECT id_unico, fechainicio, fechafin, liquidado FROM gn_periodo where fechainicio <= $fechainicio AND fechafin >= $fechainicio  AND liquidado !=1 AND id_unico !=1 and tipoprocesonomina = 1";
                $fecp = $mysqli->query($Fper);
                $nfecp = mysqli_num_rows($fecp);
                if($nfecp>0){
                    $fp = mysqli_fetch_row($fecp);
                    
                    $fechainicio_ant = $fp[1];
                    #calcula cuantos dias transcurren desde la fecha de inicio de la incapacidad hasta la fecha final del periodo
                    $dias = (strtotime($fecI)-strtotime($fp[2]))/86400;
                    $dias = abs($dias);
                    $dias = floor($dias);
                    $dias = $dias + 1;
                    #valida si el numero de dias de la incapacidad es mayor al numero de dias transcurridos desde el inicio de la incapacidad hasta la fecha final del periodo
                    if($numD > $dias){
                        $DiasFal = $numD - $dias;

                        #inserta la incapacidad
                        $sql = "INSERT INTO gn_incapacidad (empleado,tiponovedad,numeroinc,fechainicio,numerodias,numeroaprobacion,fechaaprobacion,diagnostico,concepto,fechafinal) VALUES 
                        ($empleado,$tiponovedad,$numeroinc,$fechainicio,$numerodias,$numeroaprobacion,$fechaaprobacion,$diagnostico,$concepto,$fechaf)";
                        $resultado = $mysqli->query($sql);

                        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
                        $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($dias,$hoy,$empleado,$fp[0],$concepto,4)";
                        $resultado = $mysqli->query($sql1);


                    }else{

                        #inserta la incapacidad
                        $sql = "INSERT INTO gn_incapacidad (empleado,tiponovedad,numeroinc,fechainicio,numerodias,numeroaprobacion,fechaaprobacion,diagnostico,concepto,fechafinal) VALUES 
                        ($empleado,$tiponovedad,$numeroinc,$fechainicio,$numerodias,$numeroaprobacion,$fechaaprobacion,$diagnostico,$concepto,$fechaf)";
                        $resultado = $mysqli->query($sql);
                        
                        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
                        $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($numD,$hoy,$empleado,$fp[0],$concepto,4)";
                        $resultado = $mysqli->query($sql1);
                    }
                }else{
                    $resultado = 'vigencia_actual';
                    $c=$meses_lic+1;
                }
                
            }else{
                /*calcular siguiente periodo con fecha*/
                $calendario = CAL_GREGORIAN;
                
                $fecha_ant = explode("-", $fechainicio_ant);
                $an_ant = $fecha_ant[0];
                $mes_ant = $fecha_ant[1];
                $an_sig =$an_ant;
                $mes_sig = $mes_ant;

                if($mes_ant==12){
                    $mes_sig = '01';
                    $an_sig =$an_ant + 1;
                }else{
                    $mes_sig = $mes_ant + 1;
                    $an_sig =$an_ant;
                    if($mes_sig<10){
                        $mes_sig = '0'.$mes_sig;
                    }
                }
                $ult_dia_mes = cal_days_in_month($calendario, $mes_sig, $an_sig); 
                $diai = '01';
                $fechainiciop = $an_sig.'-'.$mes_sig.'-'.$diai;
                $fechafinp = $an_sig.'-'.$mes_sig.'-'.$ult_dia_mes;
                $fechainicio_ant = $fechafinp;
                setlocale(LC_ALL, 'es_ES');
                $monthNum  = $mes_sig;
                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                $monthName = strftime('%B', $dateObj->getTimestamp());
                $monthName = strtoupper($monthName);
                $monthName = $monthName.' '.$an_sig;

                $Fper = "SELECT id_unico, fechainicio, fechafin, liquidado FROM gn_periodo where fechainicio = '$fechainiciop' AND fechafin = '$fechafinp'  AND liquidado !=1 AND id_unico !=1 and tipoprocesonomina = 1 ";
                $fecp = $mysqli->query($Fper);
                $nfecp = mysqli_num_rows($fecp);
                if($nfecp>0){
                    $fp = mysqli_fetch_row($fecp);
                    if($DiasFal>=$ult_dia_mes){                                
                        $dias_nov = $ult_dia_mes;
                        $DiasFal = $DiasFal - $ult_dia_mes;                                
                    }else{
                       $dias_nov = $DiasFal;
                    }
                #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
                $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($dias_nov,$hoy,$empleado,$fp[0],$concepto,4)";
                $resultado = $mysqli->query($sql1);
                }else{

                    $sql_par = "SELECT * FROM gf_parametrizacion_anno WHERE anno = '$an_sig'";
                    $parm = $mysqli->query($sql_par);
                    $nparam = mysqli_num_rows($parm);
                    if($nparam>0){
                        $vig = mysqli_fetch_row($parm);
                        $fp[0] = $fp[0] + 1;

                        $inserperiodo = "INSERT INTO gn_periodo(id_unico,codigointerno,fechainicio,fechafin,acumulable,estado,parametrizacionanno,tipoprocesonomina,liquidado) VALUES('$fp[0]','$monthName','$fechainiciop','$fechafinp','null',2,$vig[0],1,0)";
                        $resultado = $mysqli->query($inserperiodo);

                        $Fper = "SELECT id_unico, fechainicio, fechafin, liquidado FROM gn_periodo where fechainicio = '$fechainiciop' AND fechafin = '$fechafinp' AND liquidado !=1 AND id_unico !=1 and tipoprocesonomina = 1";
                        $fecp = $mysqli->query($Fper);
                        $nfecp = mysqli_num_rows($fecp);
                        if($nfecp>0){
                            $fp = mysqli_fetch_row($fecp);
                            if($DiasFal>=$ult_dia_mes){                                
                                $dias_nov = $ult_dia_mes;
                                $DiasFal = $DiasFal - $ult_dia_mes;                                
                             }else{
                                $dias_nov = $DiasFal;
                             }
                             #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
                            echo $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($dias_nov,$hoy,$empleado,$fp[0],$concepto,4)";
                            $resultado = $mysqli->query($sql1);
                        }

                    }                        
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

  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModalFaltanMeses" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>No se ha podido guardar la información. Debido que la licencia pasa al año siguiente y por lo tanto debe estar registrado el año de la vigencia.</p>                       
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="verFaltanMeses" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModalFaltanMes" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>No se ha podido guardar la información. Debe crear el mes(es) de la Licencia</p>                       
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="verFaltanMes" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
<?php }else if($resultado == 'vigencia'){ ?>
<script type="text/javascript">
  $("#myModalFaltanMeses").modal('show');
    $("#verFaltanMeses").click(function(){
    $("#myModalFaltanMeses").modal('hide');      
         window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
  });
</script>
<?php }else if($resultado == 'vigencia_actual'){ ?>
<script type="text/javascript">
  $("#myModalFaltanMes").modal('show');
    $("#verFaltanMes").click(function(){
    $("#myModalFaltanMes").modal('hide');      
         window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
  });
</script>
<?php } else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
         window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
  });
</script>
<?php } 
?>