<?php
require_once('../Conexion/conexion.php');
require '../ExcelR/Classes/PHPExcel/IOFactory.php';              
session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Bogota');
$anno = $_SESSION['anno'];
$periodo=$mysqli->real_escape_string(''.$_POST['sltPeriodo'].'');
$documento  = $_FILES['file'];
$name       = $_FILES['file']['name'];
$ext        = pathinfo($name, PATHINFO_EXTENSION);
$directorio ='../documentos/lecturas/';
$nombre     = $name;
$nombreArchivo= pathinfo($name, PATHINFO_FILENAME);
$subir      = move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre);
$ruta   = $directorio.$nombre;

$inputFileType = PHPExcel_IOFactory::identify($ruta);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($ruta);
$sheet = $objPHPExcel->getSheet(0); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();

for ($row = 1; $row <= $highestRow; $row++){ 
     $id_unico= $sheet->getCell("A".$row)->getValue();
     $nombre= $sheet->getCell("B".$row)->getValue();
     $codigo_a= $sheet->getCell("C".$row)->getValue();
     $nombre_a= $sheet->getCell("D".$row)->getValue();    

     $sql="INSERT INTO cuipo_detalle_sectorial (id_unico ,nombre,parametrizacionanno ,
     codigo_a,nombre_a) VALUES 
     ('$id_unico','$nombre',$anno,'$codigo_a','$nombre_a')";
      $insert = $mysqli->query($sql);
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
          <p>Información del archivo cargada correctamente.</p>
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
          <p>No se ha podido cargar la información del archivo.</p>
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
<?php if($insert==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
    window.location='../cargar_EXCEL_RESUMEN_ASISTENCIA.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
    window.location='../cargar_EXCEL_RESUMEN_ASISTENCIA.php';
  });
</script>
<?php } 
?>