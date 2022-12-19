<?php

#06/06/2017 --- Nestor B --- se agregó el campo concepto y se modificó el campo de periodo inicial 
#14/06/2017 --- Nestor B --- se agregó la validacion para que no registre el credito cuando el periodo inicial este cerrado
require_once '../Conexion/conexion.php';
session_start();

$empleado      = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$concepto      = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
$periodoinicial = '"'.$mysqli->real_escape_string(''.$_POST['sltPeriodo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltEntidad'].'')=="")
    $entidad       = "null";
else    
    $entidad       = '"'.$mysqli->real_escape_string(''.$_POST['sltEntidad'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tipoproceso = "null";
else
    $tipoproceso   = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroC'].'')=="")
    $numerocredito = "null";
else
    $numerocredito = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroC'].'').'"';

if($mysqli->real_escape_string(''.$_POST['Fecha'].'')=="")
    $fecha = "null";
else
{
    $fechaR = '"'.$mysqli->real_escape_string(''.$_POST['Fecha'].'').'"';
    $fecha2 = trim($fechaR, '"');
    $fecha_div = explode("/", $fecha2);
    $anio2 = $fecha_div[2];
    $mes2 = $fecha_div[1];
    $dia2 = $fecha_div[0];
    $fecha = '"'.$anio2.'-'.$mes2.'-'.$dia2.'"'; 
}



if($mysqli->real_escape_string(''.$_POST['txtValorCr'].'')=="")
    $valorcredito ="null";
else
    $valorcredito  = '"'.$mysqli->real_escape_string(''.$_POST['txtValorCr'].'').'"';
    $valorcredito = str_replace(',', '', $valorcredito);

if($mysqli->real_escape_string(''.$_POST['txtNCuotas'].'')=="")
    $numerocuotas = "null";
    
else
    $numerocuotas  = '"'.$mysqli->real_escape_string(''.$_POST['txtNCuotas'].'').'"';
    $numerocuotas = str_replace(',', '', $numerocuotas);

if($mysqli->real_escape_string(''.$_POST['txtValorCu'].'')=="")
    $valorcuota = "null";
else
    $valorcuota    = '"'.$mysqli->real_escape_string(''.$_POST['txtValorCu'].'').'"';
    $valorcuota = str_replace(',', '', $valorcuota);
    
$per = "SELECT id_unico, codigointerno FROM gn_periodo WHERE id_unico = $periodoinicial AND liquidado !=1";
$perr = $mysqli->query($per);
$nper = mysqli_num_rows($perr);

if($nper > 0){
    

    $sql = "INSERT INTO gn_credito(empleado,entidad,tipoproceso,numerocredito,fecha,periodoinicia,valorcredito,numerocuotas,valorcuota,concepto) VALUES ($empleado,$entidad,$tipoproceso,$numerocredito,$fecha,$periodoinicial,$valorcredito,$numerocuotas,$valorcuota,$concepto)";
    $resultado = $mysqli->query($sql);

    if(empty($_POST['txtId']))
    {
        $las = "SELECT MAX(id_unico) FROM gn_credito";
        $resultado = $mysqli->query($las);
        $rw = mysqli_fetch_row($resultado);
        $id = $rw[0];
    }else{
        $id = '"'.$mysqli->real_escape_string(''.$_POST['txtId'].'').'"';    
    }
    
    $x = 0;

    
}else{
    
    $x= 1;
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
                    <p>No se ha podido guardar la información, debido a que el periodo se encuentra cerrado</p>
            <?php } ?>
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
        window.location='../registrar_GN_CREDITO.php?idE=<?php echo md5($_POST['sltEmpleado'])?>';
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