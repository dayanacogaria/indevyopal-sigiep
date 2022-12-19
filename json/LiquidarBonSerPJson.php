<?php
################################################################################
#Creado: 10/07/2017 | Nestor B |
#
#Modificado : 11/07/2017 | Nestor B | se modifico la formula 
#
################################################################################
require_once '../Conexion/conexion.php';
require 'prima.php';
require '../Dias_Incapacidad.php';

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

$resultado = $mysqli->query($sql1);
$sql2 = "DELETE FROM gn_novedad WHERE  periodo = '$periodo'";
$resultado1 = $mysqli->query($sql2);

while($rowO = mysqli_fetch_row($resultado)){
    $salmin = "SELECT salmin, vigencia FROM gn_parametros_liquidacion WHERE vigencia = '$anno'";
    $salM = $mysqli->query($salmin);
    $slm = mysqli_fetch_row($salM);
    
    

    #consulta cuantos dias ha trabajado el empleado en el año 
    $DiasT = "SELECT SUM(n.valor) FROM gn_novedad n LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE n.empleado = '$rowO[0]' AND n.concepto = '7' AND p.parametrizacionanno = '$anno' AND p.tipoprocesonomina = '1'";
    $DTrabaj = $mysqli->query($DiasT);
    $DT = mysqli_fetch_row($DTrabaj);
    $dt = $DT[0] + 30;
    
    if($dt == 360){
        #consulta si al empleado se le ha cancelado la bonificacion
        $BOONSER = "SELECT n.* FROM gn_novedad n LEFT JOIN gn_periodo p ON n.periodo = p.id_unico WHERE n.empleado = '$rowO[0]' AND n.concepto  = '111' AND n.fecha BETWEEN $enero AND $noviembre";
        $BServP = $mysqli->query($BOONSER);
        $NBSP = mysqli_num_rows($BServP);

        if($NBSP < 1){
            if($rowO[1] <= ($slm[0] * 2)){
                $porc = 50;
            }else{
                $porc = 35;
            }
            
            $BON = ($rowO[1] * $porc)/100;
            $BON = round($BON / 10);
            $BON = $BON * 10;
            
            $sql3 = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES ($BON,$hoy,$rowO[0],$periodo,111,4)";
            $resultado1 = $mysqli->query($sql3);
            
            $tdev = "SELECT n.id_unico,"
                    . "    sum( n.valor) as total, "
                    . "     n.empleado, "
                    . "     n.periodo, "
                    . "     n.concepto, "
                    . "     c.id_unico, "
                    . "     c.clase "
                    . "     FROM gn_novedad n "
                    . "     LEFT JOIN gn_concepto c ON n.concepto = c.id_unico " 
                    . "     WHERE c.clase = 1  AND n.empleado = $rowO[0] AND n.periodo = $periodo ";
            
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
                    . "     WHERE c.clase = 2  AND n.empleado = $rowO[0] AND n.periodo = $periodo";
            
            $s = $mysqli->query($tde);
            $p = mysqli_fetch_row($s);
            if(empty($p[1]) || $p[1] == ""){
                $p[1] = 0;
            }
                    
            $Np = $m[1] - $p[1];
                        
            $tt = "INSERT INTO gn_novedad(valor,fecha,empleado,periodo,concepto,aplicabilidad) VALUES($m[1],$hoy,$rowO[0],$periodo,74,4),($p[1],$hoy,$rowO[0],$periodo,98,4),($Np,$hoy,$rowO[0],$periodo,102,4)";
            $resultado1=$mysqli->query($tt);
            
        }
    }
    
     
    
    $v = 1;    
}


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
<?php if($resultado1==true ){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');      
                    window.location = '../Liquidar_GN_BON_SERP.php?vol=<?php echo $v ?>&emp=<?php echo md5($e) ?>&per=<?php echo md5($I) ?>';
                    
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