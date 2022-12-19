<?php

require_once '../Conexion/conexion.php';
session_start();

$compania = $_SESSION['compania'];

if(!empty($_GET['action'])){
    $accion = $_GET['action'];
    
}else if(!empty($_POST['action'])){
    $accion =  $_POST['action'];
    
}
if(!empty($_GET['id'])){
    $id = $_GET['id'];
 
}elseif (!empty ($_POST['id'])) {
    $id = $_POST['id'];
  
}

if($accion==1){  //insertar   
        if($mysqli->real_escape_string(''.$_POST['indicador'].'')=="" || empty($_POST['indicador'])){
            $indicador = "null";
        }else{
            $indicador    = $_POST['indicador'];
        }
        if($mysqli->real_escape_string(''.$_POST['txtNombre'].'')=="" || empty($_POST['txtNombre'])){
            $nombre = "null";
        }else{
            $nombre      = $_POST['txtNombre'];
        }        
        if($mysqli->real_escape_string(''.$_POST['txtValor'].'')=="" || empty($_POST['txtValor'])){
            $valor = "null";
        }else{
            $valor     = $_POST['txtValor'];
        }
        $sql = "INSERT INTO gs_parametros_basicos(nombre,valor, indicador, compania) VALUES ('$nombre','$valor', '$indicador', $compania) ";
       $resultado = $mysqli->query($sql);      
}      
 elseif ($accion == 2) {  
        if($mysqli->real_escape_string(''.$_POST['indicador'].'')=="" || empty($_POST['indicador'])){
            $indicador = "null";
        }else{
            $indicador    = $_POST['indicador'];
        }

        if($mysqli->real_escape_string(''.$_POST['txtNombre'].'')=="" || empty($_POST['txtNombre'])){
            $nombre = "null";
        }else{
            $nombre      = $_POST['txtNombre'];
        }
        
        if($mysqli->real_escape_string(''.$_POST['txtValor'].'')=="" || empty($_POST['txtValor'])){
            $valor = "null";
        }else{
            $valor     = $_POST['txtValor'];
        }  
    $sql1 = "UPDATE  gs_parametros_basicos SET nombre='$nombre', valor='$valor', indicador = '$indicador' WHERE md5(id_unico)='$id'";
    $resultado1 = $mysqli->query($sql1);
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


    <!--Modales Registrar-->

    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
            	<div id="forma-modal" class="modal-header">
              		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            	</div>
        	<div class="modal-body" style="margin-top: 8px">
              		<p> Información guardada correctamente.</p>
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
                   <p>No se pudo guardar la información.</p>
              </div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>

    <!--Modales Modificar-->

    <div class="modal fade" id="myModal3" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            	<div id="forma-modal" class="modal-header">
              		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            	</div>
            	<div class="modal-body" style="margin-top: 8px">

              		<p>Información modificada correctamente.</p>
        	</div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver3" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>


    <div class="modal fade" id="myModal4" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            	<div id="forma-modal" class="modal-header">
              		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            	</div>
            	<div class="modal-body" style="margin-top: 8px">
                  <p>No se pudo modificar la información.</p>
        	</div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver4" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>
   
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>

<?php if(isset($resultado)==1){ //guardar ?>  

    <script type="text/javascript">

                $("#myModal1").modal('show');
                $("#ver1").click(function(){

                  $("#myModal1").modal('hide');      
                  window.location='../listar_GS_PARAMETROS_BASICOS.php';
                
                });
          
    </script>

<?php } if(isset($resultado1)==1){ //modificar ?>

    <script type="text/javascript">

          $("#myModal3").modal('show');
          $("#ver3").click(function(){

            $("#myModal3").modal('hide');      
            window.location='../listar_GS_PARAMETROS_BASICOS.php';
              
            
         });

    </script>
   
<?php } ?>