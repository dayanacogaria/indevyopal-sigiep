<?php
    #06/03/2017 --- Nestor B --- se modificó la ruta del botón aceptar para que regrese a donde fue llamado
    #16/03/2017 --- Nestor B --- se agregaron las validaciones de las fechas para que no genere error cuando son vacías
    #25/05/2017 --- Nestor B --- se  agregó la validacion de que la fecha inicial sea consecutiva a la fecha final del periodo anterior
    #02/06/2017 --- Nestor B --- se agregó el campo de dias de nómina
    #06/06/2017 --- Nestor B --- se agregó el campo liquidado para saber cuales periodos ya se cerraron y no pueden volver  a liquidado
    #04/07/2017 --- Nestor B --- se modficó la validación de las fechas de los periodod para que permita registrar dependiendo del proceso de nómina
    #11/07/2017 --- Nestor B --- se agregó los dias de nomina del periodo
    require_once '../Conexion/conexion.php';
    require '../Dias_Incapacidad.php';
    session_start();

    $Ano                  = ''.$mysqli->real_escape_string(''.$_POST['Anno'   ].'').'';
    #$fechainicio         = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'    ].'').'"';
    #$fechafin            = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'    ].'').'"';
    $valor                = ''.$mysqli->real_escape_string(''.$_POST['txtvalor'].'').'';
    #$estado              =     $mysqli->real_escape_string(''.$_POST['sltEstado'    ].'');
    $tipo                 = ''.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'';
    #$parametrizacionanno  = '"'.$_SESSION['anno'].'"';
    #$diasnom              =''.$mysqli->real_escape_string(''.$_POST['dialiq'].'');

    if(empty($acumulable))
        $acum = "null";
    else
        $acum = $acumulable;    

    if(empty($estado))
        $est = "null";
    else
        $est = $estado;

    

    #valida si la fecha inicial es vacía
    if($mysqli->real_escape_string(''.$_POST['sltFechaI'].'')==""){
      $fechainicio='null';
    }else{
      $fec1      = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaI'].'').'"';
      $fecha1    = trim($fec1,'"');
      $fecha_div = explode("/", $fecha1);
      $anio1 = $fecha_div[2];
      $mes1 = $fecha_div[1];
      $dia1 = $fecha_div[0];  
      $fechainicio = ''.$anio1.'-'.$mes1.'-'.$dia1.'';  
     
    }

    if($mysqli->real_escape_string(''.$_POST['sltFechaF'].'')==""){
      $fechafin='null';
    }else{
      $fec2     = '"'.$mysqli->real_escape_string(''.$_POST['sltFechaF'].'').'"';
      $fecha2    = trim($fec2,'"');
      $fecha_div = explode("/", $fecha2);
      $anio1 = $fecha_div[2];
      $mes1 = $fecha_div[1];
      $dia1 = $fecha_div[0];  
      $fechafin = ''.$anio1.'-'.$mes1.'-'.$dia1.'';  
     
    }

    $sql = "INSERT INTO gc_int_desc(anno,valor,fecha_inicio,fecha_final,tipo)VALUES($Ano,$valor,'$fechainicio','$fechafin',$tipo)";
    $resultado = $mysqli->query($sql);

    $x=1;
        

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
          <p>Información guardada correctamente.</p>
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
          <?php if($x == 1){ ?>
          <p>No se ha podido guardar la información.</p>
          <?php }else{ ?>
          <p>No se ha podido el registrar el periodo, debido a que la fecha inicial no coincide con el consecutivo de la fecha final del periodo anterior.</p>
          <?php } ?>
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
          <p>No se ha podido el registrar el periodo, debido a que la fecha inicial no coincide con el consecutivo de la fecha final del periodo anterior.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->

<?php   if($resultado==true){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');
                    window.history.go(-2);
                });   
            </script>
<?php   }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    $("#myModal2").modal('hide');
                    window.history.go(-1);
                });
            </script>
<?php   } ?>