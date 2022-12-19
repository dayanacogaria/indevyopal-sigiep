<?php
##########################################################################################
#                           MODIFICACIONES
#29/06/2017 |ERICA G. | PARAMETRIZACION ANNO                          
##########################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
session_start();
$parmanno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$anno       = anno($parmanno);
$sqlter     = $con->Listar("SELECT * FROM gf_tercero WHERE  numeroidentificacion = 9999999999 AND compania = $compania");
$tercero    = $sqlter[0][0];
$sqlc       = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $parmanno");
$centroCosto= $sqlc[0][0];
$rowTC      = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE compania = $compania AND clasecontable = 5");  
$tipocomprobante = $rowTC[0][0];
$fecha = $anno.'/01/01';
$des = "'COMPROBANTE DE SALDOS INICIALES'";
  
$rowc = $con->Listar("SELECT cnt.id_unico FROM gf_comprobante_cnt cnt  
LEFT JOIN gf_tipo_comprobante tc ON cnt.tipocomprobante = tc.id_unico 
WHERE tc.clasecontable = 5 and parametrizacionanno = $parmanno");

if(count($rowc)>0){
    $comprobante = $rowc[0][0];
} else {
  $numero = $anno.'000001';
  $valorBase = 0;
  $valorBaseIva = 0; 
  $estdo = "1";  
  $sql = "INSERT INTO gf_comprobante_cnt (numero,fecha,descripcion,
      tipocomprobante,parametrizacionanno,tercero,estado,compania) 
    VALUES ($numero,'$fecha',$des,$tipocomprobante, $parmanno, $tercero, '1',$compania);";
  $result = $mysqli->query($sql);
  $sqlC = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante=$tipocomprobante AND parametrizacionanno =$parmanno ";
  $rsC = $mysqli->query($sqlC);
  $com = mysqli_fetch_row($rsC);
  $comprobante = $com[0];
}
  $cuenta = ''.$mysqli->real_escape_string(''.$_POST['sltcuenta'].'').'';
  $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $cuenta";
  $rs = $mysqli->query($sql);
  $nat = mysqli_fetch_row($rs);
  $natural = $nat[0];
  if (empty($_POST['txtValorD'])) {
      if ($_POST['txtValorC'] != '""') {
          if ($nat[0] == 1) {
              $valor ='"'.$mysqli->real_escape_string('-'.$_POST['txtValorC'].'').'"';
          }else{
              $valor ='"'.$mysqli->real_escape_string(''.$_POST['txtValorC'].'').'"';
          }
          
      }
  }
  if (empty($_POST['txtValorC'])) {
      if($_POST['txtValorD'] != '""'){
          if ($nat[0]==2) {
              $valor =  '"'.$mysqli->real_escape_string('-'.$_POST['txtValorD'].'').'"';
          }else{
              $valor =  '"'.$mysqli->real_escape_string(''.$_POST['txtValorD'].'').'"';
          }        
      }
  }
  if(empty($_POST['slttercero'])){
      $tercero = $tercero;
  }else{
      $tercero = ''.$mysqli->real_escape_string(''.$_POST['slttercero'].'').'';
  }
  if(empty($_POST['sltproyecto'])){
        $proyecto = '2147483647';
  }else{
      $proyecto = ''.$mysqli->real_escape_string(''.$_POST['sltproyecto'].'').'';
  }
  
  if(empty($_POST['sltcentroc'])){
     $centroCosto = $centroCosto;
  }else{
      $centroCosto = ''.$mysqli->real_escape_string(''.$_POST['sltcentroc'].'').'';
  }
  
  $sqlExiste = "SELECT cuenta,tercero,centrocosto,proyecto,COUNT(id_unico) 
    FROM gf_detalle_comprobante WHERE cuenta='$cuenta' AND tercero='$tercero' 
    AND centrocosto='$centroCosto' AND proyecto='$proyecto' AND comprobante=$comprobante";
  $resultado = $mysqli->query($sqlExiste);
  $fila= mysqli_fetch_row($resultado);

  if(empty($fila[4])){
      $sqli = "INSERT INTO gf_detalle_comprobante(fecha,descripcion,valor,
          valorejecucion,comprobante,cuenta,naturaleza,tercero,proyecto,centrocosto) 
        VALUES ('$fecha',$des,$valor,$valor,$comprobante,"
              . "$cuenta,$natural,$tercero,$proyecto,$centroCosto)";
    $res = $mysqli->query($sqli);
 } else {
     $res = false;
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
<!-- Modal para indicar que ya existe el elemento -->
<div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>La información ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
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
  <!--Modal para informar al usuario que no se ha podido registrar la informacion-->
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
<!--Links para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php 
if($fila[4]>=1){?>
<script type="text/javascript" >
$("#myModal3").modal('show');
$("#ver3").click(function(){
$("#myModal3").modal('hide');
    window.location = "../registrar_GF_SALDOS_INICIALES.php";
});
</script>
<?php    
}else{
    if($rs==true){         ?>
    <script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location = "../registrar_GF_SALDOS_INICIALES.php";
    });
    </script>
    <?php }else{ ?>
    <script type="text/javascript">
        window.history.go(-1);
    </script>
<?php } ?>
<?php
}
?>

