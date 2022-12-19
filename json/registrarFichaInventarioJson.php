<?php
require_once '../Conexion/conexion.php';
session_start();
$nombreFicha = '"'.$mysqli->real_escape_string(''.$_POST['sltFicha'].'').'"';
$obligatorio = '"'.$mysqli->real_escape_string(''.$_POST['optObligatorio'].'').'"';
$autogenerado = '"'.$mysqli->real_escape_string(''.$_POST['optAutoGenerado'].'').'"';
$elementoFicha = '"'.$mysqli->real_escape_string(''.$_POST['sltElementoFicha'].'').'"';
$sqlConsulta = "select ficha,elementoficha from gf_ficha_inventario where ficha=$nombreFicha AND elementoficha=$elementoFicha";
$resultado = $mysqli->query($sqlConsulta);
$filas = mysqli_num_rows($resultado);
try {
    if(($filas==0)){
        $sql = "INSERT INTO gf_ficha_inventario(ficha, obligatorio, autogenerado, elementoficha) VALUES($nombreFicha,$obligatorio,$autogenerado,$elementoFicha)";
        $result = $mysqli->query($sql);
    }
} catch (Exception $exc) {
}


?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
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
            <?php 
            if(($filas>0)){ 
                $result = '';
                ?>
                <p>Los datos a guardar ya existén en la base de datos</p>
            <?php    
            }else{ ?>
                <p>No se ha podido guardar la información.</p>
            <?php   
            }
            ?>           
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<?php 
try{    
    if($result==true){ ?>
        <script type="text/javascript">
            $("#myModal1").modal('show');
            $("#ver1").click(function(){
                $("#myModal1").modal('hide');
                window.history.back(1);
            });
        </script>
    <?php }else{ ?>
        <script type="text/javascript">
            $("#myModal2").modal('show');
            $("#ver2").click(function(){
                $("#myModal").modal('hide');
                window.history.back(1);
            }); 
        </script>
    <?php } 
} catch (Exception $err){}
?>



