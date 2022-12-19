<?php
require_once('../Conexion/conexion.php');
session_start();
$id  = $mysqli->real_escape_string(''.$_POST['id'].'');
$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
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
$nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);  
$costea  = '"'.$mysqli->real_escape_string(''.$_POST['costea'].'').'"';
$clase   = '"'.$mysqli->real_escape_string(''.$_POST['clase'].'').'"';
$elemento = '"'.$mysqli->real_escape_string(''.$_POST['elemento'].'').'"';
$persona  = '"'.$mysqli->real_escape_string(''.$_POST['persona'].'').'"';
$formato  = '"'.$mysqli->real_escape_string(''.$_POST['formato'].'').'"';
$sigla  = '"'.$mysqli->real_escape_string(''.$_POST['txtSigla'].'').'"';
$queryUA = "SELECT nombre, costea, clase, tipoelemento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento "
      . "WHERE id_unico = '$id'";
$carA = $mysqli->query($queryUA);
$numA =  mysqli_fetch_row($carA);  
$queryU="SELECT nombre, costea, clase, tipo_documento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento 
      WHERE LOWER(nombre) = $nombreC 
      AND costea = $costea 
      AND clase=$clase 
      AND tipoelemento = $elemento 
      AND tipopersona = $persona 
      AND tipo_documento = $formato
      AND sigla = $sigla";
$car = $mysqli->query($queryU);
$num = mysqli_num_rows($car);
if('"'.strtolower($numA[0]).'"'==$nombreC && '"'.$numA[1].'"'==$costea 
    && '"'.$numA[2].'"' == $clase && '"'.$numA[3].'"' == $elemento && '"'.$numA[4].'"' == $persona
    && '"'.$numA[5].'"' == $formato && '"'.$numA[6].'"' == $sigla){
    $resultado = '1';
} else {
    if($num == 0) {         
        $update = "UPDATE gf_tipo_movimiento "
              . "SET nombre =$nombre, "
              . "costea = $costea, "
              . "clase =$clase, "
              . "tipoelemento =$elemento, "
              . "tipopersona =$persona, "
              . "tipo_documento = $formato, "
              . "sigla = $sigla"
              . "WHERE id_unico = $id";
        $resul = $mysqli->query($update);         
        $resultado ='1';
    } else {
        if($num > 0){
            $resultado ='3';
        } else {
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
                    <p>No se ha podido modificar la información.</p>
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
                    <p>El registro ingresado ya existe.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="../js/menu.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <script src="../js/bootstrap.min.js"></script>
    <?php if($resultado =='3') { ?>
        <script type="text/javascript">
            $("#myModal3").modal('show');
            $("#ver3").click(function(){
                $("#myModal3").modal('hide');
                window.location='../LISTAR_GF_TIPO_MOVIMIENTO.php';
            });
        </script>
    <?php } else { ?>
        <?php if($resultado=='1' || $resultado ==1){ ?>
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');
                    window.location='../LISTAR_GF_TIPO_MOVIMIENTO.php';
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
    <?php } } ?>
</html>