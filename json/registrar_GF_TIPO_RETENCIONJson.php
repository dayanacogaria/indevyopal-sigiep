<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $parm         = $_SESSION['anno'];
  $comp         = $_SESSION['compania'];
  $nombre       = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $descripcion  = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  $porcentajeB  = '"'.$mysqli->real_escape_string(''.$_POST['porcentajeB'].'').'"';
  $limiteI      = '"'.$mysqli->real_escape_string(''.$_POST['limiteI'].'').'"';
  $porcentajeA  = '"'.$mysqli->real_escape_string(''.$_POST['porcentajeA'].'').'"';
  $valorA       = '"'.$mysqli->real_escape_string(''.$_POST['valorA'].'').'"';
  $factorR      = '"'.$mysqli->real_escape_string(''.$_POST['factorR'].'').'"';
  $retencion    = '"'.$mysqli->real_escape_string(''.$_POST['retencion'].'').'"';
  $baseR        = '"'.$mysqli->real_escape_string(''.$_POST['baseR'].'').'"';
  $ley          = '"'.$mysqli->real_escape_string(''.$_POST['ley'].'').'"';
  $clase        = '"'.$mysqli->real_escape_string(''.$_POST['clase'].'').'"';
  $factor       = '"'.$mysqli->real_escape_string(''.$_POST['factor'].'').'"';
  $base         = '"'.$mysqli->real_escape_string(''.$_POST['base'].'').'"';
  $cuenta       = '"'.$mysqli->real_escape_string(''.$_POST['cuenta'].'').'"';
  $conceptoP    = $mysqli->real_escape_string(''.$_POST['conceptoP'].'');
  
  if(empty($_POST['sltcc'])){
    $cuentacc = 'NULL';
  }else{
    $cuentacc =$_POST['sltcc'];
  }
  if(empty($_POST['sltHomC'])){
    $concepto_Hom = 'NULL';
  }else{
    $concepto_Hom =$_POST['sltHomC'];
  }
  if(empty($_POST['codE'])){
    $codE = NULL;
  }else{
    $codE =$_POST['codE'];
  }
   if(empty($conceptoP)){
    $conceptoP = NULL;
  }else{
    $conceptoP =$conceptoP;
  }
     $insert = "INSERT INTO gf_tipo_retencion 
           (nombre, porcentajebase, 
           limiteinferior,porcentajeaplicar, 
           valoraplicar, factorredondeo, 
           descripcion, modificarretencion, 
           factoraplicacion,tipobase, 
           modificarbase,ley1450,
           claseretencion, cuenta, 
           parametrizacionanno, compania, 
           concepto_ingreso_hom, cod_exogena, cuenta_credito,concepto_pago) 
           VALUES($nombre, $porcentajeB, 
           $limiteI, $porcentajeA, 
           $valorA, $factorR, 
           $descripcion, $retencion, 
           $factor,$base, 
           $baseR, $ley, 
           $clase, $cuenta, 
           $parm, $comp, $concepto_Hom, '$codE', $cuentacc,'$conceptoP');";
    $resultado = $mysqli->query($insert);
  
  
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
</html>
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
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p><?php
                    if($num != 0) 
                      echo "El registro ingresado ya existe.";
                    else
                      echo "No se ha podido guardar la informaci&oacuten.";
                  ?>
                  </p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php if($resultado==true){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../LISTAR_GF_TIPO_RETENCION.php';
      });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location=window.history.back(-1);
      });
    </script>
<?php } ?>