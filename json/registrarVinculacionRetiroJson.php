<?php
    #03/03/2017 --- Nestor B --- se agregaron validaciones si vienen vacías las fechas
    #15/03/2017 --- Nestor B --- se modificó la validación para cuando la causa del retiro, la vinculacin retiro o el tipo de vinculación son vacíos
    #11/07/2017 --- Nestor B --- se agregó la validacion del estado 
    #04/09/2017 --- Nestor B --- se agregó la consulta el campo vinculacionretiro con el id de la vinculacion cuando se valla a registrar un retiro
    require_once '../Conexion/conexion.php';
    session_start();

    $empleado          = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';

    # valida si la fecha del acto es vacía
    if($mysqli->real_escape_string(''.$_POST['sltFechaA'].'')=="" ){
      $fechaacto="null";

    }else{
    $fec1 = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaA'].'').'"';
    $fecha1 = trim($fec1, '"');
    $fecha_div = explode("/", $fecha1);
    $anio1 = $fecha_div[2];
    $mes1 = $fecha_div[1];
    $dia1 = $fecha_div[0];
    $fechaacto = '"'.$anio1.'-'.$mes1.'-'.$dia1.'"';  
    } 

    #valida si la fecha es vac ia
    if(empty($_POST['sltFecha'])||$mysqli->real_escape_string(''.$_POST['sltFecha'].'')==""){
      $fecha="null";
    }else{
    $fec2 = '"'.$mysqli->real_escape_string(''.$_POST['sltFecha'].'').'"';
    $fecha2 = trim($fec2, '"');
    $fecha_div = explode("/", $fecha2);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fecha = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"';
    }

    if($mysqli->real_escape_string(''.$_POST['txtNumeroA'].'')=="")
        $numeroacto = "null";
    else
    $numeroacto        = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroA'].'').'"';


    if(empty($_POST['sltTipo']))
        $tipovinculacion = "null";
    else
        $tipovinculacion   = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

    if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')=="")
        $estado = "null";
    else
        $estado            = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';

    if(empty($_POST['sltCausa']))
        $causaretiro = "null";
    else
        $causaretiro       = '"'.$mysqli->real_escape_string(''.$_POST['sltCausa'].'').'"';

    if(empty($_POST['sltVinculacion']) )
        $vinculacionretiro = "null";
    else
        $vinculacionretiro = '"'.$mysqli->real_escape_string(''.$_POST['sltVinculacion'].'').'"';
if($estado == 1){

        $sql = "INSERT INTO gn_vinculacion_retiro(empleado,numeroacto,fechaacto,fecha,tipovinculacion,estado,causaretiro,vinculacionretiro) VALUES 
            ($empleado,$numeroacto,$fechaacto,$fecha,$tipovinculacion,$estado,$causaretiro,$vinculacionretiro)";
        $resultado = $mysqli->query($sql);
    }else{
        $sql = "INSERT INTO gn_vinculacion_retiro(empleado,numeroacto,fechaacto,fecha,tipovinculacion,estado,causaretiro,vinculacionretiro) VALUES 
            ($empleado,$numeroacto,$fechaacto,$fecha,$tipovinculacion,$estado,$causaretiro,$vinculacionretiro)";
        $resultado = $mysqli->query($sql);

        $sql2 = "UPDATE gn_vinculacion_retiro SET vinculacionretiro = $vinculacionretiro WHERE id_unico = $vinculacionretiro";
        $resultado = $mysqli->query($sql2);
        
        $per = "SELECT fechainicio, fechafin FROM gn_periodo WHERE fechainicio <= '$fecha' AND fechafin >= '$fecha' ";
        $peri = $mysqli->query($per);
        $nperi = mysqli_num_rows($peri);
        
        /*if($nperi > 0){
            
            $perio = mysqli_fetch_row($peri);
            
            $dias = (strtotime($perio[0])-strtotime($fecha))/86400;
            $dias = abs($dias);
            $dias = floor($dias);
            
            $sql1 = "";
        }
        /*$insertSQL = "UPDATE gn_vinculacion_retiro SET estado = 2 WHERE id_unico = $vinculacionretiro";
        $resultado = $mysqli->query($insertSQL);*/
    }    
    /*if(empty($_POST['txtId']))
    {
        $las = "SELECT MAX(id_unico) FROM gn_vinculacion_retiro";
        $resultado = $mysqli->query($las);
        $rw = mysqli_fetch_row($resultado);
        $id = $rw[0];
    }else{
        $id = '"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';    
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
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
        window.location='../registrar_GN_VINCULACION_RETIRO.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
      //window.history.go(-1);
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
?>