<?php
#Histórico de Actualizaciones
# 11/02/2017   | 11:30 | Daniel N: Tras modificar regresa al listar que lo invocó (Listado General ó Listado de Entidad Afiliación).
# 22/05/2017   | 11:20 | Nestor B: Se agrego la validación de cual perfil tiene el tercero y si no posse un perfil de tipo entidad afiliacion inserteselo y se le agrego el campo de tipo entidad   
  require_once('../Conexion/conexion.php');
  session_start();

  //Captura de datos e instrucción SQL para su modificación en la tabla gf_tercero.
  #$digitVerif = '"'.$mysqli->real_escape_string(''.$_POST['digitVerif'].'').'"';
  $id       = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
  $tipoIden = '"'.$mysqli->real_escape_string(''.$_POST['tipoIdent'].'').'"';
  $noIdent  = '"'.$mysqli->real_escape_string(''.$_POST['noIdent'].'').'"';
  $sucursal = '"'.$mysqli->real_escape_string(''.$_POST['sucursal'].'').'"';
  $razoSoci = '"'.$mysqli->real_escape_string(''.$_POST['razoSoci'].'').'"';
  $tipoReg = '"'.$mysqli->real_escape_string(''.$_POST['tipoReg'].'').'"';
  $tipoEmp = '"'.$mysqli->real_escape_string(''.$_POST['tipoEmp'].'').'"';
  $tipoEnt = '"'.$mysqli->real_escape_string(''.$_POST['tipoEnt'].'').'"';
  
  $ciudad = '"'.$mysqli->real_escape_string(''.$_POST['ciudad'].'').'"';
  
  $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

  if(empty($_POST['contacto']) || $_POST['contacto']=='""' || $_POST['contacto']==0){
    $contacto = 'NULL';
  }else{
    $contacto = '"'.$mysqli->real_escape_string(''.$_POST['contacto'].'').'"';
  }
  
  if(empty($_POST['repreLegal']) || $_POST['repreLegal']=='""' || $_POST['repreLegal']==0){
    $repreLegal = 'NULL';
  }else{
    $repreLegal = '"'.$mysqli->real_escape_string(''.$_POST['repreLegal'].'').'"';
  }

  if(empty($_POST['zona']) || $_POST['zona']=='""' || $_POST['zona']==0){
    $zona = 'NULL';
  }else{
    $zona = '"'.$mysqli->real_escape_string(''.$_POST['zona'].'').'"';
  }
  if($codigo_afp=="" || empty($codigo_afp)){
    $codigo_afp = "NULL";
  }else{
    $codigo_afp = '"'.$mysqli->real_escape_string(''.$_POST['codigo_afp'].'').'"';
  }
   $updateSQL = "UPDATE gf_tercero 
          SET RazonSocial = $razoSoci, NumeroIdentificacion = $noIdent, TipoIdentificacion = $tipoIden, Sucursal = $sucursal, RepresentanteLegal = $repreLegal, CiudadIdentificacion = $ciudad, TipoRegimen = $tipoReg, Contacto =  $contacto, TipoEmpresa = $tipoEmp, tipoentidad = $tipoEnt, Zona = $zona, codigo_afp = $codigo_afp
          WHERE Id_Unico = $id";
  $resultado = $mysqli->query($updateSQL);  

  #Consulta si el tercero posee un perfil de tipo entidad afliacion
  $perfil = "SELECT * FROM gf_perfil_tercero WHERE tercero = $id AND perfil = 11";
  $perf = $mysqli->query($perfil);
  

  if( mysqli_num_rows($perf)>0){
  } else {
       $insertSQL = "INSERT INTO gf_perfil_tercero (Perfil, Tercero) VALUES(11  , $id)";
      $resultado = $mysqli->query($insertSQL);      
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
<!-- Divs de clase Modal para las ventanillas de confirmación de modificación de registro. -->
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
<!-- Script que redirige a la página inicial de Compañia. -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    //window.location='../listar_GF_TERCERO_ENTIDAD_AFILIACION.php';
      window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>