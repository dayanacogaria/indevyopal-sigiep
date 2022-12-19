<?php
#
require_once '../Conexion/conexion.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$anno = $_SESSION['anno'];
$responsable = $_SESSION['usuario_tercero'];

$cod_apts=''.$mysqli->real_escape_string(''.$_GET["id_ap"].'').'';
$id_tercero=''.$mysqli->real_escape_string(''.$_GET["id_ter"].'').'';
$id_perfil=''.$mysqli->real_escape_string(''.$_GET["id_perfil"].'').'';
$id_paren=''.$mysqli->real_escape_string(''.$_GET["id_parent"].'').'';
$princ=''.$mysqli->real_escape_string(''.$_GET["principal"].'').'';
$id_tercero_rel=''.$mysqli->real_escape_string(''.$_GET["tercero_rel"].'').'';
$cod_ap = explode(",", $cod_apts);


for ($i=0;$i<count($cod_ap);$i++) {
    $apt= $cod_ap[$i];
    
    $sql_existe="SELECT * from gph_espacio_habitable_tercero where id_espacio_habitable='$apt' 
                    and id_tercero='$id_tercero'";
    $resultado = $mysqli->query($sql_existe);
    $exist = mysqli_fetch_row($resultado); 
    if(empty($apt) || empty($id_tercero)){
        
    }else{
        if(empty($exit[0])){
              //registrarlo
          if($id_paren==''){
            if($id_tercero_rel==''){
              $sql_insrt_espacio = "INSERT INTO gph_espacio_habitable_tercero(id_espacio_habitable,
            id_tercero, id_perfil, id_parentesco,principal, tercero_asociado) VALUES ('$apt','$id_tercero','$id_perfil',NULL,'$princ',NULL)";      
            }else{
              $sql_insrt_espacio = "INSERT INTO gph_espacio_habitable_tercero(id_espacio_habitable,
            id_tercero, id_perfil, id_parentesco,principal,tercero_asociado) VALUES ('$apt','$id_tercero','$id_perfil',NULL,'$princ','$id_tercero_rel')";  
            }
                
          }else{
            if($id_tercero_rel==''){
              $sql_insrt_espacio = "INSERT INTO gph_espacio_habitable_tercero(id_espacio_habitable,
              id_tercero, id_perfil, id_parentesco,principal,tercero_asociado) VALUES ('$apt','$id_tercero','$id_perfil','$id_paren','$princ',NULL)";   
            }else{
              $sql_insrt_espacio = "INSERT INTO gph_espacio_habitable_tercero(id_espacio_habitable,
              id_tercero, id_perfil, id_parentesco,principal,tercero_asociado) VALUES ('$apt','$id_tercero','$id_perfil','$id_paren','$princ','$id_tercero_rel')"; 
            }
               
          }
        
        $resultado = $mysqli->query($sql_insrt_espacio);
        }else{
            $sql_update_espacio = "update gph_espacio_habitable_tercero set id_perfil = '$id_perfil', id_parentesco ='$id_paren' where id_espacio_habitable='$apt' and id_tercero='$id_tercero'";
            $resultado = $mysqli->query($sql_update_espacio); 
        }
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
    
    window.location='../listar_GPH_ESPACIO_HABITABLE_TERCERO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
