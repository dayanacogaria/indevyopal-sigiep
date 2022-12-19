<?php
    require_once '../Conexion/conexion.php';
    require '../clase_nomina.php'; 
    session_start();
    
    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];
    

    $proceso = 2;
    
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
    /*
    $periodo = 13;
    $empleado = 2;   
    */ 
    $hoy = date('d-m-Y');
    $hoy = trim($hoy, '"');
    $fecha_div = explode("-", $hoy);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $hoy = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';

    $enero = '"'.$anio1.'-01-01'.'"';
    $noviembre = '"'.$anio1.'-11-30'.'"';
    /*
    $AcumPrima = acumular_e($empleado, $proceso, 12,109);
    $AcumBonif = acumular_e($empleado, $proceso, 12,111);
    
    $x = $AcumBonif[111]/12;
    $y = $AcumPrima[109]/12;
    */
    if($empleado == 2){
        $sql1 = "SELECT e.id_unico, c.salarioactual FROM gn_empleado e
                LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico
                LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                WHERE e.id_unico != 2 AND vr.estado=1 AND vr.vinculacionretiro IS NULL 
                ORDER BY `e`.`id_unico` ASC";

    }else{
        $sql1 = "SELECT e.id_unico, c.salarioactual FROM gn_empleado e 
                LEFT JOIN gn_tercero_categoria tc ON tc.empleado = e.id_unico 
                LEFT JOIN gn_categoria c ON tc.categoria = c.id_unico 
                WHERE e.id_unico = '$empleado'";

    }
    //echo $sql1;
    
    $res1 = $mysqli->query($sql1);
    
    $sql2 = "SELECT primaA, talimentacion FROM gn_parametros_liquidacion WHERE vigencia = '$anno'";
    $res2 = $mysqli->query($sql2);
    $Pquid = mysqli_fetch_row($res2);

    while($EMP = mysqli_fetch_row($res1)){

         $DiasT = "SELECT SUM(n.valor) FROM gn_novedad n
        LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
        LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico
        WHERE n.concepto = '7' AND n.empleado = '$EMP[0]' AND p.tipoprocesonomina = '1' AND p.parametrizacionanno = '$anno' ";

        $DTrabj = $mysqli->query($DiasT);
        $DT = mysqli_fetch_row($DTrabj);

        $DTT = $DT[0] + 30;
      
        #valida si el salario del empleado es menor al tope del subsidio de alimentacion     
        if($EMP[1] < $Pquid[1] ){
            $SALM = $Pquid[0];    
        }else{
            $SALM = 0;
        }


        //valida si los dias trabajados del empleado son iguales a 360 dias
        if($DTT == 360){
          #Consulta el valor de la prima de vacaciones del empleado en el año
          $PVAC = "SELECT n.valor FROM gn_novedad n WHERE concepto = '118' AND empleado = '$EMP[0]' AND fecha BETWEEN $enero AND $noviembre ";
          $PrimaV = $mysqli->query($PVAC);
          $NPV = mysqli_num_rows($PrimaV);

          #valida si el empleado tiene prima de vacaciones el año
          if($NPV > 0){
            $PV = mysqli_fetch_row($PrimaV);
            $PV1 = $PV[0]/12;
          }else{
            $PV1 = primaVac($EMP[0]);
          }

          #Consulta el valor de la bonificacion por servicios prestados del empleado en el año
          $BonSEP = "SELECT n.valor FROM gn_novedad n WHERE concepto = '111' AND empleado = '$EMP[0]' AND fecha BETWEEN $enero AND $noviembre";
          $BSPre = $mysqli->query($BonSEP);
          $NBSP = mysqli_num_rows($BSPre);
          
          #valida si el empleado posee bonificaciones por servicios prestados en el año
          if($NBSP > 0){
           # echo "hola1"."<br/>";
            $BSP = mysqli_fetch_row($BSPre);
            $BSP1 = $BSP[0]/12;
          }else{
            #echo "hola"."<br/>";
            $BSP1 = bon_serv($EMP[0]);
          }
          #Consulta el valor de la prima servicios del empleado en el año
          $PrimaServ = "SELECT n.valor FROM gn_novedad n WHERE concepto = '109' AND empleado = '$EMP[0]' AND fecha BETWEEN $enero AND $noviembre";
          $PServ = $mysqli->query($PrimaServ);
          $NPServ = mysqli_num_rows($PServ);

          #valida si el empleado posee prima de servicios  en el año
          if($NPServ > 0){
            $PS = mysqli_fetch_row($PServ);
            $PS1 = $PS[0]/12;
          }else{
            
            $PS1 = prima_serv($EMP[0]);
          }

        }else{
            $PV1  = 0;
            $BSP1 = 0;
            $PS1  = 0; 

        }         
         #"empleado ".$EMP[0]."    salario ".$EMP[1]."    dias ".$DTT."    primaV ".$PV1."    primaSe ".$PS1."    bon ".$BSP1;
          $Base = ($EMP[1] + $SALM + $PV1 + $BSP1 + $PS1);
          $TPNAV = (($Base * $DTT)/360);
          $TPNAV = round($TPNAV/10)*10;

          $sql3 = "DELETE FROM gn_novedad WHERE empleado = '$EMP[0]' AND periodo = '$periodo'";
          $res3 = $mysqli->query($sql3);

          $Insert = "INSERT INTO gn_novedad(valor, fecha ,empleado, periodo, concepto,aplicabilidad )VALUES($TPNAV, $hoy, $EMP[0], $periodo, 108,4 ),($PS1,$hoy,$EMP[0],$periodo,421,4),
                      ($BSP1,$hoy,$EMP[0],$periodo,422,4),($PV1,$hoy,$EMP[0],$periodo,423,4),($SALM,$hoy,$EMP[0],$periodo,424,4)";
          $res3 = $mysqli->query($Insert);

  
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
                  . "($m[1],$hoy,$EMP[0],$periodo,74,4),($p[1],$hoy,$EMP[0],$periodo,98,4),($Np,$hoy,$EMP[0],$periodo,102,4),($DTT,$hoy,$EMP[0],$periodo,7,4)";
          $res3=$mysqli->query($tt);
      }
    
    $v=1;
    $s=1; 

$res3 = true;
   
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

    </body>

<!--Modal para informar al usuario que se ha registrado-->
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php if($res3== true ){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');      
                    window.location = '../Liquidar_GN_PRIMA_NAV.php?vol=<?php echo $v ?>&emp=<?php echo md5($e) ?>&per=<?php echo md5($I) ?>';
                });
            </script>
<?php }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    $("#myModal2").modal('hide');      
                    window.history.go(-1);
                });
            </script>
<?php } 

?>