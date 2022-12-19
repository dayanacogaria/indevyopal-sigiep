<?php
  require_once('../Conexion/conexion.php');
  session_start();

    //Captura de datos e instrucción SQL para su modificación en la tabla gf_plan_inventario.
    $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
    $codigo = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';
    $nombre = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
    $movimiento = '"'.$mysqli->real_escape_string(''.$_POST['movimiento'].'').'"';
    $tipoInv = '"'.$mysqli->real_escape_string(''.$_POST['tipoInv'].'').'"';
    $undFact = '"'.$mysqli->real_escape_string(''.$_POST['undFact'].'').'"';
    $predecesor = '"'.$mysqli->real_escape_string(''.$_POST['predecesor'].'').'"';
    $tipoAct = '"'.$mysqli->real_escape_string(''.$_POST['tipoAct'].'').'"';
    //Se agregó elñ campo ficha el cual tiene la validación si viene vacio o nulo
    if (empty($_POST['sltFicha'])) {
        $ficha = 'NULL';
    }else{
        $ficha = '"'.$mysqli->real_escape_string(''.$_POST['sltFicha'].'').'"';
    }
    if($predecesor == '""'){
        //Modificación en la tabla gf_centro_costo sin predecesor.
        $updateSQL = "UPDATE gf_plan_inventario 
        SET codi = $codigo, nombre = $nombre, tienemovimiento = $movimiento, tipoinventario = $tipoInv, unidad = $undFact, tipoactivo = $tipoAct,ficha=$ficha   
        WHERE id_unico = $id";
      }
    else{
        //Modificación en la tabla gf_centro_costo con predecesor.
        $updateSQL = "UPDATE gf_plan_inventario 
        SET codi = $codigo, nombre = $nombre, tienemovimiento = $movimiento, tipoinventario = $tipoInv, unidad = $undFact, tipoactivo = $tipoAct, predecesor = $predecesor,ficha=$ficha  
        WHERE id_unico = $id";
    }
    $resultado = $mysqli->query($updateSQL);      
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
          <p>Información modificada correctamente.</p>
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
          <p>No se ha podido modificar la información.</p>
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

<?php if($resultado==true){
    if(!empty($_POST['planAso'])){
        if(!empty($_POST['sltPlanPadre'])){
            $planAso = $_POST['planAso'];
            $padre = '"'.$mysqli->real_escape_string(''.$_POST['sltPlanPadre'].'').'"';
            $sqlUpdateP="update gf_plan_inventario_asociado set plan_padre='$padre' where id_unico = '$planAso'";
            $result2=$mysqli->query($sqlUpdateP);
        }else{
            $planAso = $_POST['planAso'];            
            $sqlDelete="delete from gf_plan_inventario_asociado where id_unico = '$planAso'";
            $result1=$mysqli->query($sqlDelete);
        }        
    }else{
        if(!empty($_POST['sltPlanPadre'])){
            $planAso = $_POST['sltPlanPadre'];
            $padre = '"'.$mysqli->real_escape_string(''.$_POST['sltPlanPadre'].'').'"';
            $sqlinsertP="insert into gf_plan_inventario_asociado(plan_padre,plan_hijo) values ($padre,$id)";
            $result3=$mysqli->query($sqlinsertP);
        }
    }
    ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../GF_PLAN_INVENTARIO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>