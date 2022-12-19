<?php
require_once('../Conexion/conexion.php');
require '../ExcelR/Classes/PHPExcel/IOFactory.php';              
session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Bogota');

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

for ($row = 3; $row <= $highestRow; $row++){ 
     $numerocedula= $sheet->getCell("A".$row)->getValue();
     $nombres= $sheet->getCell("B".$row)->getValue();
     $apellidos= $sheet->getCell("C".$row)->getValue();
     $departamento= $sheet->getCell("D".$row)->getValue();    
     $empresa= $sheet->getCell("E".$row)->getValue(); 
     $centrocosto= $sheet->getCell("F".$row)->getValue(); 
     $fecha= $sheet->getCell("G".$row)->getValue();
     $asistenciadiaria= $sheet->getCell("H".$row)->getValue();  
     $permisos= $sheet->getCell("I".$row)->getValue(); 
     $horario= $sheet->getCell("J".$row)->getValue();
     $marcacionentrada= $sheet->getCell("K".$row)->getValue(); 
     $marcacionsalida= $sheet->getCell("L".$row)->getValue(); 
     $duraciontrabajo= $sheet->getCell("M".$row)->getValue(); 
     $totalhoras= $sheet->getCell("N".$row)->getValue(); 
     $horaentrada= $sheet->getCell("O".$row)->getValue(); 
     $temperaturaentrada= $sheet->getCell("P".$row)->getValue();
     $horasalida= $sheet->getCell("Q".$row)->getValue();
     $temperaturasalida= $sheet->getCell("R".$row)->getValue(); 
     $totaldehoras= $sheet->getCell("S".$row)->getValue();   
     $horastrabajadas= $sheet->getCell("T".$row)->getValue();   
     $tiemporealtrabajado= $sheet->getCell("U".$row)->getValue(); 
     $noprogramado= $sheet->getCell("V".$row)->getValue();   
     $descanso= $sheet->getCell("W".$row)->getValue();
     $llegadastarde= $sheet->getCell("X".$row)->getValue();
     $salidatemprana= $sheet->getCell("Y".$row)->getValue(); 
     $ausente= $sheet->getCell("Z".$row)->getValue(); 
     $totalpermiso= $sheet->getCell("AA".$row)->getValue(); 
     $or_di_na= $sheet->getCell("AB".$row)->getValue();  
     $h_d_f= $sheet->getCell("AC".$row)->getValue();  
     $extrasdiurnasordinarias= $sheet->getCell("AD".$row)->getValue(); 
     $extrasnocturnas= $sheet->getCell("AE".$row)->getValue(); 
     $extrasfestivasdiurnas= $sheet->getCell("AF".$row)->getValue();    
     $festivasnocturnas= $sheet->getCell("AG".$row)->getValue(); 
     $extrasfestivasnocturnas= $sheet->getCell("AH".$row)->getValue(); 
     $descansocompensatorio= $sheet->getCell("AI".$row)->getValue();     
     


     $sql="INSERT INTO gn_empleado_asistencia (numerodocumento,nombres,apellidos,
     departamento,empresa,centro_costo,fecha,asistencia_diaria,
     permisos,horario,marcacion_entrada,marcacion_salida,duracion_trabajo,
     total_horas,hora_entrada,temperatura_entrada,hora_salida,temperatura_salida,
     total_de_horas,horas_trabajadas,tiempo_real_trabajado,no_programado,descanso,
     llegadas_tarde,salida_temprana,ausente,total_permiso,or_di_na,h_d_f,extras_diurnas_ordinarias,
     extras_nocturnas,extras_festivas_diurnas,festivas_nocturnas,extras_festivas_nocturnas,
     descanso_compensatorio,periodo ) VALUES 
     ('$numerocedula','$nombres','$apellidos','$departamento','$empresa','$centrocosto','$fecha','$asistenciadiaria',
     '$permisos','$horario','$marcacionentrada','$marcacionsalida','$duraciontrabajo','$totalhoras','$horaentrada',
     '$temperaturaentrada','$horasalida','$temperaturasalida','$totaldehoras','$horastrabajadas','$tiemporealtrabajado',
     '$noprogramado','$descanso','$llegadastarde','$salidatemprana','$ausente','$totalpermiso','$or_di_na','$h_d_f',
     '$extrasdiurnasordinarias','$extrasnocturnas','$extrasfestivasdiurnas','$festivasnocturnas',
     '$extrasfestivasnocturnas','$descansocompensatorio','$periodo')";
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