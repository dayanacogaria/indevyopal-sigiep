<?php require_once('../Conexion/conexion.php');
session_start();
 
  $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
  $codigo  = '"'.$mysqli->real_escape_string(''.$_POST['codigo'].'').'"';
  $tipoR  = '"'.$mysqli->real_escape_string(''.$_POST['tipoR'].'').'"';
  $compania = $_SESSION['compania'];
  $param = $_SESSION['anno'];

  //Consultar si existe el código en recurso financiero.
  $num = 0;
  $queryU="SELECT codi FROM gf_recurso_financiero WHERE codi = $codigo";
  $cod = $mysqli->query($queryU);
  $num=mysqli_num_rows($cod);

  if($num == 0)//Si no existe el código, se realizará la inserción de datos en la tabla gf_recurso_financiero.
  {
    //si el campo Tipo recurso financiero esta vacio permita guardar le resto de datos
        if ($tipoR == '""' ) {
          
           $insertSQL = "INSERT INTO gf_recurso_financiero  (Nombre, Codi,  ParametrizacionAnno, compania) VALUES ($nombre, $codigo,  $param, $compania)";
          $resultado = $mysqli->query($insertSQL); 

        }else{

          $insertSQL = "INSERT INTO gf_recurso_financiero  (Nombre, Codi, TipoRecursoFinanciero, ParametrizacionAnno, compania) VALUES ($nombre, $codigo, $tipoR, $param, $compania)";
          $resultado = $mysqli->query($insertSQL);

        }
  }
  else//Si no se encuentra, retronará false en el resultado.
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
          <p>No se ha podido guardar la información.</p>
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
      window.history.go(-2);
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