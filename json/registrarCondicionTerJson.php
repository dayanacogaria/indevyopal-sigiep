<?php
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su inserción en la tabla gf_perfil_tercero.
  $perfil  = $mysqli->real_escape_string(''.$_POST['perfil'].''); 
  $tercero = $mysqli->real_escape_string(''.$_POST['tercero'].'');

 $query = "SELECT td.nombre, pf.obligatorio FROM  gf_perfil_condicion pf  LEFT JOIN gf_condicion c ON pf.condicion= c.id_unico LEFT JOIN gf_tipo_dato td ON c.tipodato = td.id_unico WHERE pf.id_unico='$perfil'";
 $queryu = $mysqli->query($query);
 $row = mysqli_fetch_row($queryu);
 $tipod= $row[0];
 $obl= $row[1];

 switch (true){
          case ($tipod=='Alfabetico') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valoraO'].'').'"';
          break;
          case ($tipod=='Alfabetico') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valoraN'].'').'"';
          break;
          case ($tipod=='Alfanumerico') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valoranO'].'').'"';
          break;
          case ($tipod=='Alfanumerico') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valoranN'].'').'"';
          break;
          case ($tipod=='Texto abierto') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valortaO'].'').'"';
          break;
          case ($tipod=='Texto abierto') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valortaN'].'').'"';
          break;
          case ($tipod=='Numerico') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valornO'].'').'"';
          break;
          case ($tipod=='Numerico') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valornN'].'').'"';
          break;
          case ($tipod=='Booleano') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valorbO'].'').'"';
          break;
          case ($tipod=='Booleano') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valorbN'].'').'"';
          break;
          case ($tipod=='Fecha') &&($obl=='0'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valorfO'].'').'"';
          break;
          case ($tipod=='Fecha') &&($obl=='1'):
          $valor  = '"'.$mysqli->real_escape_string(''.$_POST['valorfN'].'').'"';
          break;
          default:
          $valor = 'NULL';
          break;
        } 
  if($valor=='' || $valor==" " ||$valor ==NULL){
    $valor = 'NULL';
  }

  $queryU="SELECT perfilcondicion FROM gf_condicion_tercero WHERE perfilcondicion = $perfil AND tercero = $tercero";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($num == 0)
  {
    $insertSQL = "INSERT INTO gf_condicion_tercero (perfilcondicion, tercero, valor) VALUES($perfil, $tercero, $valor)";
    $resultado = $mysqli->query($insertSQL);
   }
  else
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
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Informaci&oacuten guardada correctamente.</p>
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
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
         <p><?php
              if($num != 0) 
                echo "La Condici&oacuten Tercero ingresado ya existe.";
              else
                echo "No se ha podido guardar la informaci&oacuten.";
            ?>
        </p>
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
  
<!-- Script que redirige a la página inicial -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../GF_CONDICION_TERCERO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.location='../GF_CONDICION_TERCERO.php';
  });
</script>
<?php } ?>