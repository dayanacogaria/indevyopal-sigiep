<?php
################################################################################
#Creado: 25/01/2019 kAREN 
#
#
################################################################################
require_once '../Conexion/conexion.php';
require 'prima.php';
require '../Dias_Incapacidad.php';
require '../clase_nomina.php'; 

$compania = $_SESSION['compania'];
$anno = $_SESSION['anno'];

if(!empty($_REQUEST['id_emp'])){

    $empleado = $_REQUEST['id_emp'];
}

if(!empty($_REQUEST['id_per'])){

    $periodo = $_REQUEST['id_per'];
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
}

$e = $empleado ;
$I = $periodo;
  #$diasT     = ''.$mysqli->real_escape_string(''.$_POST['txtdiasT'].'').'';    

  $hoy = date('d-m-Y');
  $hoy = trim($hoy, '"');
  $fecha_div = explode("-", $hoy);
  $anio1 = $fecha_div[2];
  $mes1 = $fecha_div[1];
  $dia1 = $fecha_div[0];
  $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';

  $enero = '"'.$anio1.'-01-01'.'"';
  $noviembre = '"'.$anio1.'-11-30'.'"';

if($empleado == 2){
    $sql1 = "SELECT e.id_unico, c.salarioactual, e.codigointerno FROM gn_empleado e
                LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico
                LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                ORDER BY e.id_unico ASC"; 
}else{
    $sql1 = "SELECT e.id_unico, c.salarioactual,e.codigointerno FROM gn_empleado e 
                LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                WHERE e.id_unico = '$empleado' "; 

}

$resultado = $mysqli->query($sql1);


$PL = "SELECT  * FROM gn_parametros_liquidacion WHERE vigencia = $anno";
    $res1 = $mysqli->query($PL);
    $rowP = mysqli_fetch_row($res1);
        
        $pid = $rowP[0]; // id de los parametros
        $pvi = $rowP[1]; // vigencia 
        $psm = $rowP[2]; // salario minimo de la vigencia
        $pat = $rowP[3]; // auxilio de transporte de la vigencia 
        $ppa = $rowP[4]; // prima de alimentacion
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

$Fperiodo = "SELECT fechafin, fechainicio FROM gn_periodo WHERE id_unico= '$periodo'";
$Fecha = $mysqli->query($Fperiodo);
$FechaP = mysqli_fetch_row($Fecha);

$FF = explode("-", $FechaP[0]);
while($rowO = mysqli_fetch_row($resultado)){
    

    $sql2 = "DELETE FROM gn_novedad WHERE  periodo = '$periodo' and empleado='$rowO[0]'";
    $resultado1 = $mysqli->query($sql2);


    $salmin = "SELECT salmin, vigencia FROM gn_parametros_liquidacion WHERE vigencia = '$anno'";
    $salM = $mysqli->query($salmin);
    $slm = mysqli_fetch_row($salM);
        
    //$VI = $slm[1] - 1;
    $VI = $slm[1];

    $AuxiAli = "SELECT primaA, talimentacion, auxt FROM gn_parametros_liquidacion WHERE vigencia = '$VI'";
    $AuxAl = $mysqli->query($AuxiAli);
    $AuxA = mysqli_fetch_row($AuxAl);

            $fini=$FF[0].'-01-01';
            $ffin=$FF[0].'-12-30';
        
    /*CALCULAR LOS DIAS TRABAJADOS */
            //1. sacar la ultima vinculacion del empleado

            $sql_ing="SELECT fecha FROM gn_vinculacion_retiro v where v.empleado='$rowO[0]' and v.estado=1 ORDER by fecha desc LIMIT 1";
            $res_ing = $mysqli->query($sql_ing);
            $f_ing = mysqli_fetch_row($res_ing);

            //2. Sacar la fecha de salida en caso que no este retirado
            $sql_ret="SELECT fecha FROM gn_vinculacion_retiro v where v.empleado='$rowO[0]' and v.estado=2  and v.fecha> '$f_ing[0]' ORDER by fecha desc LIMIT 1";
            $res_ret = $mysqli->query($sql_ret);
            $f_ret = mysqli_fetch_row($res_ret);
            $fec_r = mysqli_num_rows($res_ret);
            if($fec_r>0){

                if($f_ret[0]<$ffin){
                    $fecha_final_cal=$f_ret[0];
                }else{
                    $fecha_final_cal=$ffin;
                }
            }else{
              $fecha_final_cal=$ffin;
            }
             
            if($f_ing[0]<$fini){
                    $fecha_inicio_cal=$fini;
            }else{
                    $fecha_inicio_cal=$f_ing[0];
            }
            //3. Calcular los dias trabajados
           
            $fini_cal = explode("-", $fecha_inicio_cal);
            $mes_ini=$fini_cal[1];
            $ffin_cal = explode("-", $fecha_final_cal);
            $mes_fin=$ffin_cal[1]-1;
            $cont=0;
            
            for($x=$mes_ini;$x<=$mes_fin;$x++){
                $cont++;
            }
            
            $dias_trab=($cont*30)-($fini_cal[2]-1)+$ffin_cal[2];

            //4. Conceptos las cesantias 
            //Salario y Auxilio de transporte
             $Sal = "SELECT c.salarioactual FROM gn_categoria c 
                LEFT JOIN gn_tercero_categoria tc ON tc.categoria = c.id_unico 
                LEFT JOIN gn_empleado e ON tc.empleado = e.id_unico 
                WHERE e.id_unico = '$rowO[0]' ";
        
            $SA = $mysqli->query($Sal);
            $S = mysqli_fetch_row($SA);
            if($S[0] >= ($psm * 2)){
              $axu_tra = 0;
            }else{
                $axu_tra = $pat;
            }
            $salario_base=$S[0]+$axu_tra;
            //conceptos configurados
          $sql_vlhe_pr="SELECT cb.* FROM gn_concepto_base cb
                        LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                        where c.clase=1 and cb.id_concepto_aplica=113";
          $res_hepr = $mysqli->query($sql_vlhe_pr);    
          $n_he = mysqli_num_rows($res_hepr);
          $vl_promedio_cs=0;
          $vl_actual_cs=0;

            if($n_he > 0){
              while($row_he = mysqli_fetch_row($res_hepr)){
                  $id_tipo_b=$row_he[3];
                  $id_concepto=$row_he[1];
                  if($id_tipo_b==1){
                    //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n 
                        left join gn_periodo p on p.id_unico=n.periodo
                        where n.concepto=$id_concepto and p.fechainicio>='$fecha_inicio_cal' and p.fechafin<='$fecha_final_cal' and n.empleado='$rowO[0]' order by n.fecha desc LIMIT 1 ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_actual_cs= $vl_actual_cs+$row_act[1];
                     }

                  }else if($id_tipo_b==2){
                    //si el tipo de base es promedio sumamos todos los valores pagados 
                    $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n 
                        left join gn_periodo p on p.id_unico=n.periodo
                        where n.concepto=$id_concepto and p.fechainicio>='$fecha_inicio_cal' and p.fechafin<='$fecha_final_cal' and n.empleado=$rowO[0]  ";
                     $res_vlact = $mysqli->query($sql_vl_act_con);   
                     $n_vl = mysqli_num_rows($res_vlact);
                     if($n_vl > 0)
                     {
                        $row_act = mysqli_fetch_row($res_vlact);
                        $vl_promedio_cs= $vl_promedio_cs+$row_act[0];
                     }
                  }
              }
            }
            $vl_promedio_cs= ($vl_promedio_cs/$dias_trab)*30;
            $salario_base_cs=$salario_base+$vl_promedio_cs+$vl_actual_cs;

            $vlr_cesantias = ($salario_base_cs * $dias_trab) / 360;
            $vlr_cesantias = round($vlr_cesantias/10)*10;

            $vlr_int_cesantias = ($vlr_cesantias * $dias_trab * 0.12)/360;
            $vlr_int_cesantias = round($vlr_int_cesantias/10)*10;            
            
            $sql3 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($dias_trab,$hoy,$rowO[0],$periodo,7,1)";
            $resultado1 = $mysqli->query($sql3);
            
            $sql4 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($vlr_cesantias,$hoy,$rowO[0],$periodo,113,1)";
            $resultado1 = $mysqli->query($sql4);

            $sql5 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($S[0],$hoy,$rowO[0],$periodo,1,1)";
            $resultado1 = $mysqli->query($sql5);

            $sql6 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($vl_promedio_cs,$hoy,$rowO[0],$periodo,446,1)";
            $resultado1 = $mysqli->query($sql6);

            $sql7 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($vl_actual_cs,$hoy,$rowO[0],$periodo,447,1)";
            $resultado1 = $mysqli->query($sql7);

            $sql8 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($vlr_int_cesantias,$hoy,$rowO[0],$periodo,436,1)";
            $resultado1 = $mysqli->query($sql8);

            $tdev = "SELECT n.id_unico,
                        sum( n.valor) as total, 
                        n.empleado, 
                        n.periodo, 
                        n.concepto, 
                        c.id_unico, 
                        c.clase 
                    FROM gn_novedad n 
                    LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                    WHERE c.clase = 1 AND n.concepto != 7 AND n.empleado = $rowO[0] AND n.periodo = $periodo";

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
                 WHERE c.clase = 2 AND n.concepto != 7 AND n.empleado = $rowO[0] AND n.periodo = $periodo";

        $s = $mysqli->query($tde);
        $p = mysqli_fetch_row($s);
        
        if(empty($p[1]) || $p[1] == ""){
            
            $p[1] = 0;            
        }
        
        $Np = $m[1] - $p[1];
       
        $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES 
            ($m[1],$hoy,$rowO[0],$periodo,74,1),($p[1],$hoy,$rowO[0],$periodo,98,1),($Np,$hoy,$rowO[0],$periodo,102,1)";
        $resultado1=$mysqli->query($tt);


    $v = 1; 


}
$json=2;

if($json !=1){


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
<?php 

if($resultado1==true ){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');      
                    window.location = '../liquidar_GN_CESANTIAS.php?vol=<?php echo $v ?>&emp=<?php echo md5($e) ?>&per=<?php echo md5($I) ?>';
                    
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
<?php } 

}else{
  echo json_encode($resultado1);
}?>