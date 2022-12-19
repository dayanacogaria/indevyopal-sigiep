<?php
  require_once('../Conexion/conexion.php');
  session_start();
  $id = $mysqli->real_escape_string(''.$_POST['id'].'');
  $nombre  = $mysqli->real_escape_string(''.$_POST['nombre'].'');
  $identificador  = $mysqli->real_escape_string(''.$_POST['identificador'].'');
  $cadena1 = strtolower($nombre);
  //cargo en el array los textos a sustituir
  $exp_regular = array();
  $exp_regular[0] = '/á/';
  $exp_regular[1] = '/é/';
  $exp_regular[2] = '/í/';
  $exp_regular[3] = '/ó/';
  $exp_regular[4] = '/ú/';
  $exp_regular[4] = '/ñ/';
  //cargo en el array los textos que pondremos en la sustitucion
  $cadena_nueva = array();
  $cadena_nueva[0] = 'a';
  $cadena_nueva[1] = 'e';
  $cadena_nueva[2] = 'i';
  $cadena_nueva[3] = 'o';
  $cadena_nueva[4] = 'u';
  $cadena_nueva[4] = 'n';
 
  
  $queryUA="SELECT nombre, identificador FROM gg_tipo_proceso "
          . "WHERE id_unico = '$id'";
  $carA = $mysqli->query($queryUA);
  $numA=  mysqli_fetch_row($carA);
  $cadena2 = strtolower($numA[0]);
  
  $queryU="SELECT * FROM gg_tipo_proceso "
          . "WHERE LOWER(nombre) = '$nombre' AND identificador ='$identificador'";
  $car = $mysqli->query($queryU);
  $num=mysqli_num_rows($car);
   //Reemplazar caracteres en nombre para comparar anterior con el nuevo
 $nombreC1= preg_replace($exp_regular, $cadena_nueva, $cadena1);
 $nombreC2= preg_replace($exp_regular, $cadena_nueva, $cadena2);
 //Identificador
 $queryI="SELECT * FROM gg_tipo_proceso "
          . "WHERE identificador = $identificador";
  $carI = $mysqli->query($queryI);
  $numI=mysqli_num_rows($carI);
  
 //comparación para que guarde
   if($nombreC2==$nombreC1 && $numA[1] ==$identificador){
        
         $update = "UPDATE gg_tipo_proceso "
              . "SET nombre ='$nombre', identificador = $identificador  "
              . "WHERE id_unico = '$id'";
         $resul = $mysqli->query($update);
         
         $resultado =true;
         
  } else {
        if($num == 0)
        {
         if($numI==0 || $numA[1] ==$identificador){
            $update = "UPDATE gg_tipo_proceso "
              . "SET nombre ='$nombre', identificador = '$identificador' "
              . "WHERE id_unico = '$id'";
         $resul = $mysqli->query($update);
         
         $resultado =true;
         
         } else {
             $resultado= false ;
         }
        } else { 
             if($num > 0){
                 $resultado =false;
             }else {
                 $resultado= false;
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
                   <p><?php
                        if($num != 0){ 
                          echo "El registro ingresado ya existe.";
                        } else { 
                            if ($numI !=0){
                                echo 'El identificador ya ha sido asignado';
                            } else {
                               echo "No se ha podido guardar la informaci&oacuten."; 
                            }
                        }
                          
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
                window.location='../LISTAR_GG_TIPO_PROCESO.php';
              });
            </script>
        <?php }else{ ?>
        <script type="text/javascript">
          $("#myModal2").modal('show');
         $("#ver2").click(function(){
            $("#myModal2").modal('hide');
             window.location=window.history.back(-1);
          });
        </script>
    <?php }  ?>
</html>