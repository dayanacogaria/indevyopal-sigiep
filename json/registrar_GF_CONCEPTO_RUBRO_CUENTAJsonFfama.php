<?php
  require_once('../Conexion/conexion.php');
  session_start();
########################################################################################################################################################
# Modificaciones
#
########################################################################################################################################################
# Fecha : 24/02/2017
# Hora  : 03:57 p.m
# Modificó : Jhon Numpaque
# Descripción : Se agrego campos cuenta iva, cuenta impoconsumo.
#
########################################################################################################################################################
  $id  = $mysqli->real_escape_string(''.$_POST['conceptoRubro'].'');
  $conceptoR  = '"'.$mysqli->real_escape_string(''.$_POST['conceptoRubro'].'').'"';
  $cuentaD  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaD'].'').'"';
  $cuentaC  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaC'].'').'"';
  $centroC  = '"'.$mysqli->real_escape_string(''.$_POST['centroC'].'').'"';
  $proyecto  = '"'.$mysqli->real_escape_string(''.$_POST['proyecto'].'').'"';
  if(empty($_POST['sltCuentaI'])){
      $cuentaIva="NULL";
  } else {
  $cuentaIva  = '"'.$mysqli->real_escape_string(''.$_POST['sltCuentaI'].'').'"';
  }
  
  if(empty($_POST['sltCuentaImpo'])){
        $cuentaImpo="NULL";
  } else {
        $cuentaImpo  = '"'.$mysqli->real_escape_string(''.$_POST['sltCuentaImpo'].'').'"';
  }
  
  if(empty($_POST['cuentaDebitoSeguro'])){
        $cuentaDebitoSeguro="NULL";
  } else {
        $cuentaDebitoSeguro  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaDebitoSeguro'].'').'"';
  }
 
 
   if(empty($_POST['cuentaCreditoSeguro'])){
        $cuentaCreditoSeguro="NULL";
  } else {
        $cuentaCreditoSeguro  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaCreditoSeguro'].'').'"';
  }
  
   if(empty($_POST['terceroSeguro'])){
        $terceroSeguro="NULL";
  } else {
        $terceroSeguro  = '"'.$mysqli->real_escape_string(''.$_POST['terceroSeguro'].'').'"';
  }
  
   if(empty($_POST['cuentaDebitoProvision'])){
        $cuentaDebitoProvision="NULL";
  } else {
        $cuentaDebitoProvision  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaDebitoProvision'].'').'"';
  }
  
   if(empty($_POST['cuentaCreditoProvision'])){
        $cuentaCreditoProvision="NULL";
  } else {
        $cuentaCreditoProvision  = '"'.$mysqli->real_escape_string(''.$_POST['cuentaCreditoProvision'].'').'"';
  }
  
  
  
  $queryU="SELECT * FROM gf_concepto_rubro_cuenta 
          WHERE concepto_rubro = $conceptoR 
          AND cuenta_debito = $cuentaD 
          AND cuenta_credito=$cuentaC 
          AND centrocosto = $centroC 
          AND proyecto = $proyecto 
          AND cuenta_iva = $cuentaIva 
          AND cuenta_impoconsumo = $cuentaImpo 
          AND cuenta_debito_seguro = $cuentaDebitoSeguro 
          AND cuenta_credito_seguro =$cuentaCreditoSeguro 
          AND tercero_seguro =$terceroSeguro 
          AND cuenta_debito_provision =$cuentaDebitoProvision 
          AND cuenta_credito_provision = $cuentaCreditoProvision ";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
   $insert = "INSERT INTO gf_concepto_rubro_cuenta (concepto_rubro, cuenta_debito, "
           . "cuenta_credito, centrocosto, proyecto,cuenta_iva,"
           . "cuenta_impoconsumo, cuenta_debito_seguro, "
           . "cuenta_credito_seguro, tercero_seguro, "
           . "cuenta_debito_provision, cuenta_credito_provision, cuenta_descuento  ) "
          . "VALUES($conceptoR, $cuentaD, $cuentaC, $centroC, $proyecto, $cuentaIva, "
           . "$cuentaImpo, $cuentaDebitoSeguro, $cuentaCreditoSeguro, "
           . "$terceroSeguro, $cuentaDebitoProvision, $cuentaCreditoProvision, $cuentaDes)";
  $resultado = $mysqli->query($insert);
   }
  else
  {
    $resultado = false;
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
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
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
    window.location='../GF_CONCEPTO_RUBRO_CUENTA.php?id=<?php echo md5($id);?>';
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