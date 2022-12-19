<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania = $_SESSION['compania'];

@$proyec = $_REQUEST['pro'];
@$idCP = $_REQUEST['idCP'];

$claseP = "SELECT cp.nombre FROM gy_clase_pregunta cp WHERE md5(cp.id_unico) = '$idCP'";
$clasP = $mysqli->query($claseP);
$CP = mysqli_fetch_row($clasP);

$pregunt = "SELECT id_unico, nombre FROM gy_pregunta WHERE md5(id_clase_pregunta) = '$idCP' AND compania = '$compania'";
$pregu = $mysqli->query($pregunt);

$nresp = mysqli_num_rows($pregu); 

$id_proyecto = "SELECT id_unico, titulo FROM gy_proyecto WHERE md5(id_unico) = '$proyec'";
$id_proy = $mysqli->query($id_proyecto);
$id_pr = mysqli_fetch_row($id_proy);

$registrar = "SELECT rp.id_unico, rp.respuesta FROM gy_respuesta_pregunta rp 
            LEFT JOIN gy_pregunta p ON rp.id_pregunta = p.id_unico
            LEFT JOIN gy_clase_pregunta cp ON p.id_clase_pregunta = cp.id_unico
            WHERE md5(rp.id_proyecto) = '$proyec' AND md5(cp.id_unico) = '$idCP'  AND rp.compania = '$compania' ";
$exire = $mysqli->query($registrar);
$nexis = mysqli_num_rows($exire);
?>
<title><?php echo $CP[0] ?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src=”js/jquery.min.js” type=”text/javascript”> </script>
<script src=”js/jquery.fixedtableheader.min.js” type=”text/javascript”> </script>
<style>
    label #nombre-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
    }
    body{
        font-size: 12px;
    }
</style>
<style>
    .scrollTabla{
        
        height: 300px;
        overflow-y: scroll;
    }
</style>    
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",

    errorPlacement: function(error, element) {

      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    .form-control {font-size: 12px;}
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $CP[0] ?></h2>
                <a href="modificar_GY_PROYECTO.php?id=<?php echo $proyec ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo 'Proyecto: '.$id_pr[1] ?></h5>
                <!--<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">-->
                    <?php 
                        if($nexis < 1) { 
                    ?>
                            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                                <input type="hidden" id="txtidproyecto" name="txtidproyecto" value="<?php echo $id_pr[0] ?>">
                                <input type="hidden" id="txtnump" name="txtnump" value="<?php echo $nresp ?>">
                                <div class="scrollTabla">
                                <div class="col-sm-12 col-md-12 col-lg-12  text-left" style="margin-top: 3%">
                                    <div class="table-responsive" style="margin-top:-10px;">
                                        <div class="table-responsive">
                                            <table  class="table table-bordered" id="header-fixed1"  cellspacing="0" width="100%">
                                               
                                                <tbody>
                                                    <?php
                                                        $i = 1;
                                                        
                                                        $Y = 1;
                                                        $tipo_pre = "SELECT id_unico , nombre 
                                                                    FROM gy_tipo_pregunta 
                                                                    WHERE  md5(id_clase_pregunta) = '$idCP' AND compania = '$compania' ";
                                                        
                                                        $restipo = $mysqli->query($tipo_pre);
                                                        
                                                        while($TP = mysqli_fetch_row($restipo)){
                                                            $preguntas = "SELECT id_unico, nombre FROM gy_pregunta WHERE md5(id_clase_pregunta) = '$idCP' AND id_tipo_pregunta = '$TP[0]' AND compania = '$compania'";
                                                            $pregun = $mysqli->query($preguntas);
                                                    ?>
                                                    		<tr>
                                                            	<th colspan="2" style="text-align: center"><?php echo $Y.'. '.$TP[1] ?></th>
                                                          	</tr>
                                                            
                                                    <?php
                                                           $X = 1;
                                                            
                                                            while($PRE = mysqli_fetch_row($pregun)){
                                                                $id_pre = "idpregunta".$i;
                                                                $txtRes = "txtresp".$i;
                                                                $i = $i + 1;
                                                    ?>
                                                                <tr>
                                                                    <td style="width:50%;">
                                                                        <input type="hidden" id="<?php echo $id_pre?>" name="<?php echo $id_pre?>" value="<?php echo $PRE[0] ?>"> 
                                                                        <?php  echo $Y.'.'.$X.' '.$PRE[1]; ?>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" id="<?php echo $txtRes?>" name="<?php echo $txtRes?>" style="width: 100%">
                                                                    </td>
                                                                </tr>
                                                    <?php
                                                                $X++;
                                                            }
                                                            
                                                             $Y++;  
                                                        }
                                                        
                                                    ?>    
                                                        
                                                    
                                                </tbody>
                                            </table>
                                        </div>    
                                    </div>  
                                </div> 
                                </div>  
                        
                                <div class=" col-sm-12" style="margin-top: 20px; margin-left: 87%">
                                    <label for="no" class="col-sm-12 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" title="GUARDAR" style=" "><i class="glyphicon glyphicon-floppy-disk"></i></button>
                                </div>
                            </form>
                            <script>
                                function registrar(){
                                    //var P = $("#txtP").val();
                                    jsShowWindowLoad('Guardando Datos ...');
                                    var formData = new FormData($("#form")[0]);
                                    $.ajax({
                                        type: 'POST',
                                        url: "jsonProyecto/gy_respuesta_preguntaJson.php?action=2",
                                        data:formData,
                                        contentType: false,
                                        processData: false,
                                        success: function(response)
                                        {
                                            jsRemoveWindowLoad();
                                            console.log(response);
                                            if(response==1){
                                                $("#mensaje").html('Información Guardada Correctamente');
                                                $("#modalMensajes").modal("show");
                                                $("#Aceptar").click(function(){
                                                    location.reload();
                                                    //$document.location='modificar_GY_PROYECTO.php?id=<?php echo  $proyec ?>';

                                                })
                                            } else {
                                                $("#mensaje").html('No Se Ha Podido Guardar Información');
                                                $("#modalMensajes").modal("show");
                                                $("#Aceptar").click(function(){
                                                    $("#modalMensajes").modal("hide");
                                                })

                                            }
                                        }
                                    });
                                }
                            </script>
                    <?php
                        }else { 
                    ?>
                            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                                <input type="hidden" id="txtidproyecto" name="txtidproyecto" value="<?php echo $id_pr[0] ?>">
                                <input type="hidden" id="txtnump" name="txtnump" value="<?php echo $nresp ?>">
                                <div class="scrollTabla">
                                <div class="col-sm-12 col-md-12 col-lg-12  text-left" style="margin-top: 3%">
                                    <div class="table-responsive" style="margin-top:-10px;">
                                        <div class="table-responsive">
                                            <table  class="table table-bordered"  cellspacing="0" width="100%">
                                                
                                                <tbody>
                                                    <?php
                                                        $i = 1;
                                                        $X = 1;
                                                        $Y = 1;
                                                        $tipo_pre = "SELECT id_unico , nombre 
                                                                    FROM gy_tipo_pregunta 
                                                                    WHERE  md5(id_clase_pregunta) = '$idCP' AND compania = '$compania' ";
                                                        
                                                        $restipo = $mysqli->query($tipo_pre);
                                                        
                                                        while($TP = mysqli_fetch_row($restipo)){
                                                            $preguntas = "SELECT id_unico, nombre FROM gy_pregunta WHERE md5(id_clase_pregunta) = '$idCP' AND id_tipo_pregunta = '$TP[0]' AND compania = '$compania'";
                                                            $pregun = $mysqli->query($preguntas);
                                                    ?>
                                                            <tr>
                                                            	<th colspan="2" style="text-align: center"><?php echo $Y.'. '.$TP[1] ?></th>
                                                          	</tr>
                                                            
                                                    <?php
                                                            while($PRE = mysqli_fetch_row($pregun)){
                                                                $id_pre = "idpregunta".$i;
                                                                $txtRes = "txtresp".$i;
                                                                $i = $i + 1;
                                                    ?>
                                                                <tr>
                                                                    <td style="width:50%;">
                                                                        <input type="hidden" id="<?php echo $id_pre?>" name="<?php echo $id_pre?>" value="<?php echo $PRE[0] ?>"> 
                                                                        <?php  echo $X.'. '.$PRE[1]; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php 
                                                                           $txtresp = "SELECT respuesta FROM gy_respuesta_pregunta WHERE id_pregunta = '$PRE[0]' AND id_proyecto = '$id_pr[0]'";
                                                                            $resp = $mysqli->query($txtresp);
                                                                            $re = mysqli_fetch_row($resp);
                                                                        ?>
                                                                        <input type="text" id="<?php echo $txtRes?>" name="<?php echo $txtRes?>" style="width: 100%" value="<?php echo $re[0] ?>">
                                                                    </td>
                                                                </tr>
                                                    <?php
                                                                $X++;
                                                            }
                                                            $Y++;
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>    
                                    </div>  
                                </div> 
                                </div>  
                        
                                <div class=" col-sm-12" style="margin-top: 20px; margin-left: 77%; ">
                                    <label for="no" class="col-sm-12 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra" style=" " title="ACTIALIZAR" ><i class="glyphicon glyphicon-refresh"></i></button>
                                    <button type="button" class="btn btn-primary sombra"  title="IMPRIMIR" onclick="javascript:imprimir();" ><i class="glyphicon glyphicon-print" ></i></button>
                                </div>
                            </form>
                    <script>
                       
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_respuesta_preguntaJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             location.reload();
                                            //document.location='modificar_GY_PROYECTO.php?id=<?php echo  $proyec ?>';
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
                    <script>
                        function imprimir(){
                            window.open('informesProyecto/generar_INF_GY_RESPUESTAS.php?idCP=<?php echo $idCP ?>&idPR=<?php echo $proyec ?>');
                        }
                    </script>
                    <?php } ?>
                <!--</div>-->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea Eliminar El Registro Seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
</body>
</html>



