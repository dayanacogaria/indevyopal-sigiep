  <?php
    require_once('../Conexion/conexion.php');
  session_start();
    
      $nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
      $tipoC  = '"'.$mysqli->real_escape_string(''.$_POST['tipoC'].'').'"';
      $claseA  = '"'.$mysqli->real_escape_string(''.$_POST['claseA'].'').'"';

   /////Provicional: Buscar un ID de perfil tercero con perfil 1 que es Compañia para insertarlo en parametrizacion anno.
    $queryComp="SELECT MAX(tercero) AS Id_Unico FROM gf_perfil_tercero WHERE Perfil=1 ";
    $comp = $mysqli->query( $queryComp);
    $row = mysqli_fetch_row($comp); 
    $compania = $row[0];
   ///////Provicional.

    /////Provicional: Buscar un ID de parametrización año para insertarlo en recurso financiero.
    $queryUlt="SELECT MAX(Id_Unico) AS Id_Unico FROM gf_parametrizacion_anno";
    $ult = $mysqli->query($queryUlt);
    $row=mysqli_fetch_row($ult);
    $param=$row[0];
    ///////Provicional.

    $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

      $variable = ", tipoclase=$tipoC, claseafectar=$claseA";

      if($claseA == '""' && $tipoC == '""')
        $variable = ", tipoclase=NULL, claseafectar=NULL";
      elseif($claseA == '""')
        $variable = ",tipoclase=$tipoC, claseafectar=NULL";
      elseif ($tipoC == '""') 
        $variable = ", claseafectar=$claseA, tipoclase=NULL";

      $sql = "UPDATE gf_clase_pptal  SET nombre=$nombre".$variable." WHERE Id_Unico = $id";
      $resultado = $mysqli->query($sql);
  
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

  <?php if($resultado==true){ ?>
  <script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function(){
      $("#myModal1").modal('hide');
      window.location='../listar_GF_CLASE_PPTAL.php';
    });
  </script>
  <?php }else{ ?>
  <script type="text/javascript">
    $("#myModal2").modal('show');
  </script>
  <?php } ?>