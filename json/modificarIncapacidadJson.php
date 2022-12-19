<?php
#13/03/2017 --- Nestor B --- se agregaron la validaciones de las fechas cuando son vacías 
#05/04/2017 --- Nestor B --- se modificó la forma en que recibe la fechas

  require_once('../Conexion/conexion.php');
  require '../Dias_Incapacidad.php';
session_start();

//obtiene los datos que se van a modificar

$empleado         = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$tiponovedad      = $mysqli->real_escape_string(''.$_POST['sltTipo'].'');
$estado           = $mysqli->real_escape_string(''.$_POST['sltEstado'].'');
$numeroinc        = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroI'].'').'"';
$concept      = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
$numerodias       = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'"';
$numeroaprobacion = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';
#$fechaaprobacion  = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
$diagnostico      = '"'.$mysqli->real_escape_string(''.$_POST['txtDiagnostico'].'').'"';
$id             = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$FI             = $mysqli->real_escape_string(''.$_POST['txtFechaI'].'');
$CON            = $mysqli->real_escape_string(''.$_POST['txtCon'].'');

#valida si la fecha inicial es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
    $fechainicio = "null";
    $FechaI = "null";
    
}else
{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechainicio = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
    $FechaI = ''.$anio1.'-'.$mes1.'-'.$dia1.'';  
}

#valida si la fecha de aprobación es vacía
if($mysqli->real_escape_string(''.$_POST['sltFechaA'].'')=="")
    $fechaaprobacion= "null";
else
{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio7 = $fecha_div[2];
    $mes7 = $fecha_div[1];
    $dia7 = $fecha_div[0];
    $fechaaprobacion = '"'.$anio7.'-'.$mes7.'-'.$dia7.'"';     
}
if($tiponovedad=="")
    $tipo = "null";
else
    $tipo = $tiponovedad;

if($estado=="")
    $est = "null";
else
    $est = $estado;



 $novedad = "SELECT n.id_unico, n.valor, n.concepto, n.empleado, n.periodo FROM   gn_novedad n LEFT JOIN gn_incapacidad i ON i.empleado = n.empleado WHERE i.empleado = $empleado AND i.fechainicio = '$FI' AND i.concepto = $CON ORDER BY n.periodo ASC";
$res = $mysqli->query($novedad);

$nov = mysqli_fetch_row($res);

 $perio = "SELECT id_unico, codigointerno, fechainicio, fechafin FROM gn_periodo WHERE id_unico = $nov[4] AND liquidado !=1";
$per = $mysqli->query($perio);
$nper = mysqli_num_rows($per);

if($nper >  0 ){
    
    $borrarnov = "DELETE FROM gn_novedad WHERE concepto = $CON and empleado = $empleado AND fecha = '$FI' ";
    $resultado = $mysqli->query($borrarnov);
    //modificar ne la base de datos
    $insertSQL = "UPDATE gn_incapacidad SET empleado=$empleado, tiponovedad=$tipo, estado=$est, numeroinc=$numeroinc, fechainicio=$fechainicio, numerodias=$numerodias, numeroaprobacion=$numeroaprobacion, fechaaprobacion=$fechaaprobacion, diagnostico=$diagnostico WHERE id_unico = $id";
    $resultado = $mysqli->query($insertSQL);
    
    $numD = ''.$mysqli->real_escape_string(''.$_POST['txtNumeroD'].'').'';
    $diasFinal = DiasFecha($FechaI, $numD);
    


    #valida que exista una periodo que no se encuentre cerrado cuya fecha inical sea menor a la fecha de inicio de la incapacidad
    $Fper = "SELECT id_unico, fechainicio, fechafin, liquidado FROM gn_periodo where fechainicio <= $fechainicio AND fechafin >= $fechainicio AND liquidado !=1 AND id_unico !=1";
    $fecp = $mysqli->query($Fper);
    $nfecp = mysqli_num_rows($fecp);
    $fp = mysqli_fetch_row($fecp);
    
    #calcula cuantos dias transcurren desde la fecha de inicio de la incapacidad hasta la fecha final del periodo
    $dias = (strtotime($FechaI)-strtotime($fp[2]))/86400;
    $dias = abs($dias);
    $dias = floor($dias);
    $dias = $dias + 1;
    
    #valida si el numero de dias de la incapacidad es mayor al numero de dias transcurridos desde el inicio de la incapacidad hasta la fecha final del periodo
    if($numD > $dias){
    
        $DiasFal  = $numD - $dias;
        $TotalD  = $numD - $DiasFal;
        
        $DiasFal  = $numD - $dias;
        $TotalD  = $numD - $DiasFal;
    
    
        
        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
        $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($TotalD,$fechainicio,$empleado,$fp[0],$concept,4)";
        $resultado = $mysqli->query($sql1);
    
        $fechaini = $FechaI;  
        $fp[0]=$fp[0]+1;
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
                    $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($DiasFal,$fechainicio,$empleado,$dmes[0],$concept,4)";
                    $resultado = $mysqli->query($sql2);
                
                    $DiasFal = 0;
                }else{
                
                    $DiasFal = $DiasFal - $diasm;
                
                    #inserta la novedad en el periodo posterior con los dias de la incapacidad faltantes
                    $sql2 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($diasm,$fechainicio,$empleado,$dmes[0],$concept,4)";
                    $resultado = $mysqli->query($sql2);
           
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
                    #$mesnom = int($fechapost[1]);
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
        
        
        #inserta la novedad en el periodo actual con los dias de la incapacidad transcurridos
       $sql1 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($numD,$fechainicio,$empleado,$fp[0],$concept,4)";
        $resultado = $mysqli->query($sql1);
        
        
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
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <?php if($x == 0){ ?>
          
            <p>No se ha podido modificar la información.</p>
            
          <?php }else{ ?>
            
             <p>No se ha podido modificar la información, debido a que el periodo en el que se registró la novedad se encuentra cerrado</p>
          <?php } ?>   
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GN_INCAPACIDAD.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
      $("#myModal2").modal('hide');
       //window.location='../registrar_GN_INCAPACIDAD.php?idE=<?php echo md5($_POST['sltEmpleado'])?>&tipo=<?php echo $tiponovedad ?>';
       window.history.go(-2);
  });
</script>
<?php } ?>