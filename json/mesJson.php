<?php
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
######################################################################################################
require_once '../Conexion/conexion.php';
session_start();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];

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

if($accion==1){    
        

        if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')== "" || empty($_POST['sltEstado'])){
             $estado = "null";
        }else{
            $estado   = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
        }
        
        if($mysqli->real_escape_string(''.$_POST['txtmes'].'')=="" || empty($_POST['txtmes'])){
            $mes = "null";
        }else{
            $mes      = '"'.$mysqli->real_escape_string(''.$_POST['txtmes'].'').'"';
        }
        
        if($mysqli->real_escape_string(''.$_POST['txtnmes'].'')=="" || empty($_POST['txtnmes'])){
            $nmes = "null";
        }else{
            $nmes     = $_POST['txtnmes'];
        }
     
        $sql2 = "SELECT * FROM gf_mes    WHERE parametrizacionanno = $anno && numero = $nmes";
        
        $a = $mysqli ->query($sql2);
        $row = mysqli_num_rows($a);
        
        if($row > 0 ){
            $resultado = 3;
        } else{
          
            if($nmes >=1 && $nmes <=12){
          
                $nmes     = '"'.$mysqli->real_escape_string(''.$_POST['txtnmes'].'').'"';

                $sql = "INSERT INTO gf_mes(mes,estadomes,parametrizacionanno,compania,numero) VALUES ($mes,$estado,$anno,$compania,$nmes) ";
                $resultado = $mysqli->query($sql);
                
            }else{
          
                $resultado = 2;
            }
        }
       
      if($resultado == 1){
            $res1 = 0;
            $res  = 1;
            $z=0;
        }elseif($resultado == 2){
          
            $res1 = 0;
            $res  = 2;
            $z=1;
        }elseif($resultado == 3){
          
            $res1 = 0;
            $res  = 2;
            $z=2;
        }    
      
}      
 elseif ($accion == 2) {
         
        
        if($mysqli->real_escape_string(''.$_POST['sltEstado'].'')== "" || empty($_POST['sltEstado'])){
            $estado = "null";
        }else{
             $estado   = '"'.$mysqli->real_escape_string(''.$_POST['sltEstado'].'').'"';
        }
        
        if($mysqli->real_escape_string(''.$_POST['txtmes'].'')=="" || empty($_POST['txtmes'])){
            $mes = "null";
        }else{
            $mes      = '"'.$mysqli->real_escape_string(''.$_POST['txtmes'].'').'"';
        }
        
        if($mysqli->real_escape_string(''.$_POST['txtnmes'].'')=="" || empty($_POST['txtnmes'])){
           $nmes = "null";
        }else{
            $nmes     = ''.$mysqli->real_escape_string(''.$_POST['txtnmes'].'').'';
        }
        
        $sql2 = "SELECT * FROM gf_mes  WHERE parametrizacionanno = $anno && numero = $nmes AND id_unico !=$id";
        $a = $mysqli ->query($sql2);
        $row = mysqli_num_rows($a);
        if($row > 0 ){
            $resultado1 = 3;
        } else{
          
            if($nmes >=1 && $nmes <=12){

                $sql1 = "UPDATE  gf_mes SET mes=$mes, estadomes=$estado,  numero=$nmes WHERE id_unico = $id";
                $resultado1 = $mysqli->query($sql1);
                IF($resultado1==true){
                    $resultado1 = 1;
                } else {
                    $resultado1 = 3;
                }
            }else{

                $resultado1 = 2;
            }
        }
        if($resultado1 == 1){
            $res1 = 1;
            $res  = 0;
            $z=0;
        }elseif($resultado1 == 2){
            $res1 = 2;
            $res  = 0;
            $z=1;
        }elseif($resultado1 == 3){
          
            $res1 = 2;
            $res  = 0;
            $z=2;
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
                     <?php if($z == 1){ ?>
                                <p>No se pudo guardar la información. El rango del número de mes es de 01 hasta 12.</p>
                     <?php } elseif ($z==2) { ?>
                                <p>No se pudo guardar la información. El mes ya se encuentra registrado para este año.</p> 
                      <?php }else{ ?>
                                <p>No se pudo guardar la información.</p>
                      <?php } ?>
                </div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>
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
                    <?php if($z == 1){ ?>
                                <p>No se pudo modificar la información. El rango del número de mes es de 01 hasta 12.</p>
                     <?php } elseif ($z==2) { ?>
                                <p>No se pudo modificar la información. El mes ya se encuentra registrado para este año.</p> 
                      <?php }else{ ?>
                               <p>No se pudo modificar la información.</p>
                      <?php } ?>
              		
        	</div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver4" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="myModal5" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            	<div id="forma-modal" class="modal-header">
              		<h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            	</div>
            	<div class="modal-body" style="margin-top: 8px">
              		<p>No se pudo guardar la información. El rango para el número del mes es de 01 hasta 12.</p>
        	</div>
        	<div id="forma-modal" class="modal-footer">
              		<button type="button" id="ver5" class="btn" style="" data-dismiss="modal" >Aceptar</button>
            	</div>
          </div>
        </div>
    </div>
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<?php if($res ==1){ ?>
<script type="text/javascript">
            $("#myModal1").modal('show');
            $("#ver1").click(function(){
            $("#myModal1").modal('hide');      
            window.location='../listar_GF_MES.php';
            //window.history.go(-1);
            });
      
            </script>
<?php }if($res == 2){ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
        
      window.history.go(-1);
  });
</script>
<?php } 
?>
<?php if($res1 == 1){ ?>
<script type="text/javascript">
            $("#myModal3").modal('show');
            $("#ver3").click(function(){
            $("#myModal3").modal('hide');      
            window.location='../listar_GF_MES.php';
            //window.history.go(-1);
            });
</script>
<?php }if($res1 == 2){ ?>
<script type="text/javascript">
  $("#myModal4").modal('show');
    $("#ver4").click(function(){
    $("#myModal4").modal('hide');      
        //window.location='../registrar_GN_ACCIDENTE.php?id=<?php echo md5($id);?>';
      window.history.go(-1);
  });
</script>
<?php } 
?>
