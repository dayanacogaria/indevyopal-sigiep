<?php
#
require_once '../Conexion/conexion.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$anno = $_SESSION['anno'];
$responsable = $_SESSION['usuario_tercero'];

$opcion=''.$mysqli->real_escape_string(''.$_GET["opcion"].'').'';

if($opcion=='R'){
  $cod_apts=''.$mysqli->real_escape_string(''.$_GET["id_ap"].'').'';
  $id_con=''.$mysqli->real_escape_string(''.$_GET["id_con"].'').'';
  $id_tipo=''.$mysqli->real_escape_string(''.$_GET["tipo"].'').'';
  $cod_ap = explode(",", $cod_apts);


  for ($i=0;$i<count($cod_ap);$i++) {
      $apt= $cod_ap[$i];
      
      $sql_existe="SELECT * from gn_concepto_base where id_concepto='$apt' 
                      and id_concepto_aplica='$id_con'";
      $resultado = $mysqli->query($sql_existe);
      $exist = mysqli_fetch_row($resultado); 
      if(empty($apt) || empty($id_con)){
          
      }else{
          if(empty($exit[0])){
                //registrarlo
            $sql_insrt_conb = "INSERT INTO gn_concepto_base(id_concepto,
            id_concepto_aplica, id_tipo_base) VALUES ('$apt','$id_con','$id_tipo')";                 
          
          $resultado = $mysqli->query($sql_insrt_conb);

          }else{
              $sql_update_espacio = "update gn_concepto_base set id_tipo_base = '$id_tipo' where id_concepto='$apt' and id_concepto_aplica='$id_con'";
              $resultado = $mysqli->query($sql_update_espacio); 
          }
      }
      
           
  }
}else if($opcion=='E'){
  $id = $_GET["id"];
  $sql = "DELETE FROM gn_concepto_base WHERE id_unico = $id";
  $resultado = $mysqli->query($sql);
  echo json_encode($resultado);
}
else if($opcion=='M'){

  $id_cb=''.$mysqli->real_escape_string(''.$_GET["id_cb"].'').'';
  $id_concepto=''.$mysqli->real_escape_string(''.$_GET["con"].'').'';
  $id_concepto_aplica=''.$mysqli->real_escape_string(''.$_GET["con_ap"].'').'';
  $id_tipo=''.$mysqli->real_escape_string(''.$_GET["tipo"].'').'';
  
      $sql_existe="SELECT * from gn_concepto_base where id_concepto='$id_concepto' 
                      and id_concepto_aplica='$id_concepto_aplica'";
      $resultado = $mysqli->query($sql_existe);
      $exist = mysqli_fetch_row($resultado); 
      if(empty($id_concepto) || empty($id_concepto_aplica)){
          
      }else{
          
          $sql_update_con = "update gn_concepto_base set id_tipo_base = '$id_tipo' where id_concepto='$id_concepto' and id_concepto_aplica='$id_concepto_aplica'";   
         
             
              $resultado = $mysqli->query($sql_update_con); 
          
      }
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

if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    
    window.location='../listar_GN_CONCEPTO_BASE.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
