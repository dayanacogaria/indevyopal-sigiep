<?php
#
require_once '../Conexion/conexion.php';
require_once '../funciones/funcionLiquidador.php';
session_start();
$anno = $_SESSION['anno'];
$responsable = $_SESSION['usuario_tercero'];

$tipo=''.$mysqli->real_escape_string(''.$_GET["tipo"].'').'';
$id_espacio=''.$mysqli->real_escape_string(''.$_GET["id_espacio"].'').'';
if($tipo=='1'){
  $placa=''.$mysqli->real_escape_string(''.$_GET["placa"].'').'';
  $marca=''.$mysqli->real_escape_string(''.$_GET["marca"].'').'';
  $color=''.$mysqli->real_escape_string(''.$_GET["color"].'').'';  
  //registrar si es vehiculo
  $sql_insrt_vs = "INSERT INTO gph_vehiculo (placa,marca,color) VALUES ('$placa',
            '$marca','$color')";
  $resultado = $mysqli->query($sql_insrt_vs);
        //buscar vehiculo
  $sql_existe="SELECT * from gph_vehiculo where placa='$placa' ";
  $resultado = $mysqli->query($sql_existe);
  $exist = mysqli_fetch_row($resultado); 

//registrar espaccio habitable propiedad
  $sql_insrt_ehp = "INSERT INTO gph_espacio_habitable_propiedad_relacionada (id_tipo_relacion,id_vehiculo,id_mascota,id_espacio_habitable) VALUES ('$tipo',
            '$exist[0]',NULL,'$id_espacio')";
  $resultado = $mysqli->query($sql_insrt_ehp);
        //buscar espacio habitable propiiedad
  $sql_existe="SELECT * from gph_espacio_habitable_propiedad_relacionada order by id_unico desc limit 0,1 ";
  $resultado = $mysqli->query($sql_existe);
  $exist = mysqli_fetch_row($resultado); 

}else if($tipo=='2'){
  $especie=''.$mysqli->real_escape_string(''.$_GET["especie"].'').'';
  $raza=''.$mysqli->real_escape_string(''.$_GET["raza"].'').'';  

  //registrar si es mascota
  $sql_insrt_vs = "INSERT INTO gph_mascota (especie,raza) VALUES ('$especie',
            '$raza')";
  $resultado = $mysqli->query($sql_insrt_vs);
        //buscar visitante
  $sql_existe="SELECT * from gph_mascota order by id_unico desc limit 0,1";
  $resultado = $mysqli->query($sql_existe);
  $exist = mysqli_fetch_row($resultado); 

  //registrar espaccio habitable propiedad
  $sql_insrt_ehp = "INSERT INTO gph_espacio_habitable_propiedad_relacionada (id_tipo_relacion,id_vehiculo,id_mascota,id_espacio_habitable) VALUES ('$tipo',
            NULL,'$exist[0]','$id_espacio')";
  $resultado = $mysqli->query($sql_insrt_ehp);
        //buscar espacio habitable propiiedad
  $sql_existe="SELECT * from gph_espacio_habitable_propiedad_relacionada order by id_unico desc limit 0,1 ";
  $resultado = $mysqli->query($sql_existe);
  $exist = mysqli_fetch_row($resultado); 

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
    
    window.location='../listar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>
