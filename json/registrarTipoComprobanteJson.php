<?php
require_once('../Conexion/conexion.php');
session_start();
$anno = $_SESSION['anno'];
#***** Recepcion Variables ********#
$sigla      = '"'.$mysqli->real_escape_string(''.$_POST['sigla'].'').'"';
$nombre     = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
$reten      = $mysqli->real_escape_string(''.$_POST['reten'].'');
$inter      = '"'.$mysqli->real_escape_string(''.$_POST['inter'].'').'"';
$nif        = '"'.$mysqli->real_escape_string(''.$_POST['nif'].'').'"';
$claseC     = $mysqli->real_escape_string(''.$_POST['claseC'].'');      
$compania   = $_SESSION['compania'];
$predial    = $mysqli->real_escape_string(''.$_POST['predial'].'');      
$comercio   = $mysqli->real_escape_string(''.$_POST['comercio'].'');   
$reteica    = $mysqli->real_escape_string(''.$_POST['reteica'].'');      
$amortizacion= $mysqli->real_escape_string(''.$_POST['amortizacion'].'');      
$traslado   = $mysqli->real_escape_string(''.$_POST['traslado'].'');      
$aportes    = $mysqli->real_escape_string(''.$_POST['aportes'].'');      
if(!empty($_POST['formato'])){
    $formato    = '"'.$mysqli->real_escape_string(''.$_POST['formato'].'').'"';
}else{
    $formato    = 'NULL';
}
if(!empty($_POST['sltTipoC'])){
    $tipoCP     = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoC'].'').'"';
}else{
    $tipoCP     = 'NULL';
}
if(!empty($_POST['sltTipoCompH'])){
    $tcomH      = '"'.$mysqli->real_escape_string(''.$_POST['sltTipoCompH'].'').'"';
}else{
    $tcomH      = 'NULL';
}
#******* Indicativos Error *********#
$guardar  = 1;#1 - Si, "2- No
$m        = 0;#Error
#   ***** Validar Sigla No Exista ****#
$bs = "SELECT * FROM gf_tipo_comprobante WHERE sigla = $sigla AND compania = $compania";
$bs = $mysqli->query($bs);
if(mysqli_num_rows($bs)>0){
   $guardar = 2;
   $m = 4;
}
if($reten=="1") {
    # ***** BUSCAR LA CLASE QUE TIENE RETENCION ***** #
    $cr="SELECT tc.clasecontable  
          FROM gf_tipo_comprobante tc 
          WHERE tc.retencion = '1' AND compania = $compania
          GROUP BY tc.clasecontable, tc.retencion";
    $cr= $mysqli->query($cr);
    if(mysqli_num_rows($cr)>0){
        $cr = mysqli_fetch_row($cr) ;
        $claser=$cr[0];
        if($claseC==$claser){
        } else {
            $m        = 1;
            $guardar  = 2;
        }
    }
 }

 # ***** Buscar Si Existe Algun Comprobante Que Ya Sea De Pedial
 if($predial ==1){
     $ip ="SELECT * FROM  gf_tipo_comprobante WHERE interfaz_predial= '1' AND compania = $compania";
     $ip = $mysqli->query($ip);
     if(mysqli_num_rows($ip)>0){
         $m        = 2;
         $guardar  = 2;
     } 
 }
 #* Comercio
 if($comercio ==1){
     echo $ip = "SELECT * FROM gf_tipo_comprobante WHERE interfaz_comercio= '1' AND compania = $compania";
     $ip = $mysqli->query($ip);
     if(mysqli_num_rows($ip)>0){
         $m        = 3;
         $guardar  = 2;
     } 
 }
 #Reteica
if($reteica ==1){
    $ip ="SELECT  *  FROM gf_tipo_comprobante WHERE interfaz_reteica= '1' AND compania = $compania";
    $ip = $mysqli->query($ip);
    if(mysqli_num_rows($ip)>0){
        $m        = 5;
        $guardar  = 2;
    }
}
 #Amortizacion
if($amortizacion ==1){
    $ip ="SELECT  *  FROM gf_tipo_comprobante WHERE amortizacion= '1' AND compania = $compania";
    $ip = $mysqli->query($ip);
    if(mysqli_num_rows($ip)>0){
        $m        = 6;
        $guardar  = 2;
    }
}

 #Traslado
if($traslado ==1){
    $ip ="SELECT  *  FROM gf_tipo_comprobante WHERE traslado= '1' AND compania = $compania";
    $ip = $mysqli->query($ip);
    if(mysqli_num_rows($ip)>0){
        $m        = 7;
        $guardar  = 2;
    }
}

 #aportes
if($aportes ==1){
    $ip ="SELECT  *  FROM gf_tipo_comprobante WHERE interfaz_aportes= '1' AND compania = $compania";
    $ip = $mysqli->query($ip);
    if(mysqli_num_rows($ip)>0){
        $m        = 8;
        $guardar  = 2;
    }
}
if($guardar==1){
    $sql = "INSERT INTO gf_tipo_comprobante (sigla,nombre, retencion, 
    interface, niif, clasecontable,tipodocumento,
    compania,comprobante_pptal,tipo_comp_hom, interfaz_predial, 
    interfaz_comercio, interfaz_reteica, amortizacion, traslado, interfaz_aportes) 
    VALUES ($sigla,$nombre,  $reten, $inter, $nif, 
    $claseC, $formato,$compania,$tipoCP,$tcomH,$predial, $comercio, 
            $reteica, $amortizacion, $traslado, $aportes)";      
    $resultado = $mysqli->query($sql);  
} else {
    $resultado= false;
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
            <?php if ($m==1) {
                echo '<p>Ya existe una clase con retención que es diferente a la escogida. Por favor verifique</p>';
            } elseif($m==2) { 
                echo '<p>Ya existe una comprobante para Interfáz de Predial</p>';
            } elseif($m==3) { 
                echo '<p>Ya existe una comprobante para Interfáz de Comercio</p>';
            } elseif($m==4) { 
                echo '<p>Ya existe un comprobante con la misma sigla</p>';
            } elseif($m==5) { 
                echo '<p>Ya existe una comprobante para Interfáz de Reteica</p>';
            } elseif($m==6) { 
                echo '<p>Ya existe una comprobante para Interfáz de Amortización</p>';
            }elseif($m==7) { 
                echo '<p>Ya existe una comprobante para Interfáz de Traslados</p>';
            }elseif($m==8) { 
                echo '<p>Ya existe una comprobante para Interfáz de Aportes</p>';
            } else { 
                echo '<p>No se ha podido guardar la información.</p>';
            } ?>
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
    window.location='../listar_GF_TIPO_COMPROBANTE.php';
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
<?php } ?>