<?php
  require_once('../Conexion/conexion.php');
  session_start();
#SUBIDA DEL DOCUMENTO 
$documento = $_FILES['file'];
$nombre = $_FILES['file']['name'];
$directorio ='../documentos/proceso/';
$id = "SELECT MAX(id_unico) FROM gg_documento_proceso";
$id = $mysqli->query($id);
$id= mysqli_fetch_row($id);
$id = $id[0]+1;

if(!empty($_POST['obligatorio'])){
    $obligatorio  = $mysqli->real_escape_string(''.$_POST['obligatorio'].'');
    if($obligatorio=='1'){
        if(!empty($_POST['numero1'])){
          $numero  = $mysqli->real_escape_string(''.$_POST['numero1'].''); 
          $numeroB = '="'.$numero.'"';
        } else {
            $numero=NULL;
            $numeroB = 'IS NULL'; 
        }
    } else {  
        if(!empty($_POST['numero2'])){
            $numero  = $mysqli->real_escape_string(''.$_POST['numero2'].''); 
            $numeroB = '="'.$numero.'"';
        } else {
            $numero=NULL;
            $numeroB = 'IS NULL'; 
        }
    }
} else {
    if(!empty($_POST['numero1'])){
          $numero  = $mysqli->real_escape_string(''.$_POST['numero1'].''); 
          $numeroB = '="'.$numero.'"';
    } else {
            if(!empty($_POST['numero2'])){
                $numero  = $mysqli->real_escape_string(''.$_POST['numero2'].''); 
                $numeroB = '="'.$numero.'"';
            } else {
                $numero=NULL;
                $numeroB = 'IS NULL'; 
            }
    }
}
$documento  = $mysqli->real_escape_string(''.$_POST['documento'].'');
$proceso  = $mysqli->real_escape_string(''.$_POST['proceso'].'');
$nombre =$id.$nombre;
$ruta = 'documentos/proceso/'.$nombre;


$queryU="SELECT * FROM gg_documento_proceso "
          . "WHERE proceso='$proceso' "
          . "AND documento = '$documento' "
          . "AND numero_documento $numeroB OR numero_documento ='' "
          . "AND ruta = '$ruta' ";
 $car = $mysqli->query($queryU);
 $num=mysqli_num_rows($car);

  if($num == 0)
  {
    $insert = "INSERT INTO gg_documento_proceso (id_unico, proceso, documento, numero_documento, ruta) "
          . "VALUES('$id', '$proceso', '$documento', '$numero', '$ruta')";
    $resultado = $mysqli->query($insert);
    if ($resultado==true || $resultado=='1'){
       // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
        move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
    }
    
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
                <p><?php
                    if($num != 0) 
                      echo "El registro ingresado ya existe.";
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

<?php if($resultado==true){ ?>
    <script type="text/javascript">
      $("#myModal1").modal('show');
      $("#ver1").click(function(){
        $("#myModal1").modal('hide');
        window.location='../GG_DOCUMENTO_PROCESO.php?id=<?php echo md5($proceso)?>';
      });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
      $("#myModal2").modal('show');
      $("#ver2").click(function(){
        $("#myModal2").modal('hide');
         window.location='../GG_DOCUMENTO_PROCESO.php?id=<?php echo md5($proceso)?>';
      });
    </script>
<?php } ?>