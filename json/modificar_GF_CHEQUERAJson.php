<?php
  require_once('../Conexion/conexion.php');
  $numero  = $mysqli->real_escape_string(''.$_POST['numero'].'');
  $numI  = $mysqli->real_escape_string(''.$_POST['numeroI'].'');
  $numF  = $mysqli->real_escape_string(''.$_POST['numeroF'].'');
  $estado  =$mysqli->real_escape_string(''.$_POST['estado'].'');
  $cuentaB  = $mysqli->real_escape_string(''.$_POST['cuenta'].'');
  $id  = $mysqli->real_escape_string(''.$_POST['id'].'');
  $error='';
  if($numI >= $numF){
      $error = '10';
  }else {
  $queryA="SELECT numerochequera, cuentabancaria FROM gf_chequera WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryA);
  $numA=mysqli_fetch_row($carA);
  
  $queryU="SELECT * FROM gf_chequera WHERE numerochequera = $numero AND cuentabancaria = $cuentaB";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);

  if($numA[0]==$numero &&$numA[1]==$cuentaB ){
  
        $insertSQL = "UPDATE gf_chequera SET numerochequera = $numero, "
                    . "numeroinicial = $numI, numerofinal =  $numF, "
                    . "estadochequera = $estado, cuentabancaria=$cuentaB WHERE id_unico = $id";
        $resultado = $mysqli->query($insertSQL); 
    } else {
        if($num == 0)
	  { 
            $insertSQL = "UPDATE gf_chequera SET numerochequera = $numero, "
                    . "numeroinicial = $numI, numerofinal =  $numF, "
                    . "estadochequera = $estado, cuentabancaria=$cuentaB WHERE id_unico = $id";
            $resultado = $mysqli->query($insertSQL); 
	  } else {
	  	if($num>0){
	  		$resultado=3;
	  		
	  	} else {
	  	$resultado = false;
	  }
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
          <p>No se ha podido modificar la información</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
          <p>El registro ingresado ya existe</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
          <p>El número final debe ser mayor al número inicial
            </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
  <?php if ($error =='10'){ ?>
  <script type="text/javascript">
  $("#myModal4").modal('show');
  $("#ver4").click(function(){
    $("#myModal4").modal('hide');
     window.location=window.history.back(-2);
  });
</script>
  <?php } else { ?>
<?php if ($resultado == 1) { ?>
    <script>
        $("#myModal1").modal('show');
        $("#ver1").click(function(){

          $("#myModal1").modal('hide');
          document.location = '../LISTAR_GF_CHEQUERA.php';

        });
    </script>
    <?php } else { 
        if($resultado == 3){ ?>
    <script>
        $("#myModal3").modal('show');
        $("#ver3").click(function(){

          $("#myModal3").modal('hide');
          window.location=window.history.back(-2);
        });
    </script>
    <?php } else { ?>
    <script>

            $("#myModal2").modal('show');
            $("#ver2").click(function(){
              
              $("#myModal2").modal('hide');
              window.location=window.history.back(-2);
              
            });
    </script>
    <?php } }  }?>
