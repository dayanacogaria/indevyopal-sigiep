<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#20/02/2018| ERICA G. | ARCHIVO CREADO
######################################################################################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once('./head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nannov = anno($anno);
$anno2 = $nannov-1;
$an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'");
$ida2  = $an2[0][0];



if(empty($_GET['id'])){
    $titulo = "Registrar Homologación";
    $titulo2 =".";
    #*** Listar Cuentas Año ***#
    $vg = $con->Listar("SELECT c.id_unico, c.codi_cuenta, 
            LOWER(c.nombre) 
            FROM gf_cuenta c 
            WHERE c.parametrizacionanno = $anno AND (c.movimiento =1 OR c.auxiliartercero = 1) 
            AND c.equivalente_va IS NULL 
            ORDER BY c.codi_cuenta ASC");
    #*** Listar Cuentas Año Anterior ***#
    $vga = $con->Listar("SELECT c.id_unico, c.codi_cuenta, 
            LOWER(c.nombre) 
            FROM gf_cuenta c 
            WHERE c.parametrizacionanno = $ida2 AND (c.movimiento =1 OR c.auxiliartercero = 1) 
            AND c.codi_cuenta NOT IN
            (SELECT ca.equivalente_va FROM gf_cuenta ca 
            WHERE ca.parametrizacionanno = $anno 
            AND ca.equivalente_va IS NOT NULL )
            ORDER BY c.codi_cuenta ASC");
} else {
    $titulo = "Modificar Homologación";
    $dt = $con ->Listar("SELECT c.id_unico, c.codi_cuenta, LOWER(c.nombre),
                ca.id_unico, ca.codi_cuenta, LOWER(ca.nombre) 
                FROM gf_cuenta c 
                LEFT JOIN gf_cuenta ca ON ca.codi_cuenta = c.equivalente_va AND ca.parametrizacionanno=".$ida2." 
                WHERE md5(c.id_unico)='".$_GET['id']."'");
    $titulo2 ='Cuenta: '.$dt[0][1].' - '.ucwords($dt[0][2]);
    #*** Listar Cuentas Año ***#
    $vg = $con->Listar("SELECT c.id_unico, c.codi_cuenta, 
            LOWER(c.nombre) 
            FROM gf_cuenta c 
            WHERE c.parametrizacionanno = $anno AND (c.movimiento =1 OR c.auxiliartercero = 1) 
            AND c.equivalente_va IS NULL AND c.id_unico != ".$dt[0][0]."
            ORDER BY c.codi_cuenta ASC");
    #*** Listar Cuentas Año Anterior ***#
    $vga = $con->Listar("SELECT c.id_unico, c.codi_cuenta, 
            LOWER(c.nombre) 
            FROM gf_cuenta c 
            WHERE c.parametrizacionanno = $ida2 
            AND (c.movimiento =1 OR c.auxiliartercero = 1) 
            AND c.id_unico != ".$dt[0][3]."
            AND c.codi_cuenta NOT IN
            (SELECT ca.equivalente_va FROM gf_cuenta ca 
            WHERE ca.parametrizacionanno = $anno  
            AND ca.equivalente_va IS NOT NULL )
            ORDER BY c.codi_cuenta ASC");
}
?>

<html>
    <head>
    <title>Homologación Plan Contable Vigencia Anterior</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>

    <style>
        label #cuenta-error, #cuentava-error {
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;

    }
    body{
        font-size: 12px;
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
        },
      });

      $(".cancel").click(function() {
        validator.resetForm();
      });
    });
    </script>

    </head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px"><?php echo $titulo?></h2>    
                <a href="LISTAR_GF_HOMOLOGACION_CUENTAS_VA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo $titulo2;?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <?php if(empty($_GET['id'])) { ?>
                          <!--Ingresa la información-->
                          <div class="form-group">
                            <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta:</label>
                            <select name="cuenta" id="cuenta" class="select2_single form-control input-sm" title="Seleccione Cuenta" style="width:350px; ">
                                <option value="">Cuenta</option>
                                <?php 
                                for ($i = 0; $i < count($vg); $i++) {
                                    echo '<option value ="'.$vg[$i][0].'">'.$vg[$i][1].' - '.ucwords($vg[$i][2]).'</option>';
                                }
                                ?>
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="cuentava" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Vigencia Anterior:</label>
                            <select name="cuentava" id="cuentava" class="select2_single form-control input-sm" title="Seleccione Cuenta Vigencia Anterior" style="width:350px; ">
                                <option value="">Cuenta Vigencia Anterior</option>
                                <?php 
                                for ($i = 0; $i < count($vga); $i++) {
                                    $sl = $con->Listar("SELECT * FROM gf_cuenta WHERE parametrizacionanno = $anno AND equivalente_va =".$vga[$i][1]);
                                    echo '<option value ="'.$vga[$i][1].'">'.$vga[$i][1].' - '.ucwords($vga[$i][2]).'</option>';
                                   
                                }
                                ?>
                            </select>
                          </div>
                        <?php } else { ?>
                           <!--Ingresa la información-->
                          <div class="form-group">
                            <label for="cuenta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta:</label>
                            <select name="cuenta" id="cuenta" class="select2_single form-control input-sm" title="Seleccione Cuenta" style="width:350px; ">
                                
                                <?php 
                                echo '<option value="'.$dt[0][0].'">'.$dt[0][1].' - '.ucwords($dt[0][2]).'</option>';
                                for ($i = 0; $i < count($vg); $i++) {
                                    echo '<option value ="'.$vg[$i][0].'">'.$vg[$i][1].' - '.ucwords($vg[$i][2]).'</option>';
                                }
                                ?>
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="cuentava" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Vigencia Anterior:</label>
                            <select name="cuentava" id="cuentava" class="select2_single form-control input-sm" title="Seleccione Cuenta Vigencia Anterior" style="width:350px; ">
                                <?php 
                                echo '<option value="'.$dt[0][4].'">'.$dt[0][4].' - '.ucwords($dt[0][5]).'</option>';
                                for ($i = 0; $i < count($vga); $i++) {
                                    $sl = $con->Listar("SELECT * FROM gf_cuenta WHERE parametrizacionanno = $anno AND equivalente_va =".$vga[$i][1]);
                                    echo '<option value ="'.$vga[$i][1].'">'.$vga[$i][1].' - '.ucwords($vga[$i][2]).'</option>';
                                   
                                }
                                ?>
                            </select>
                          </div>
                        <?php } ?>  
                          <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                          </div>
                          <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
            </div> 
        <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
        
    </script>
    <!----**Funcion Guardar Configuracion**---->
    <script>
        function registrar(){
            var formData = new FormData($("#form")[0]);  
            
           $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_cuentaJson.php?action=3",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                console.log(response);
                if(response==true){
                    $("#mensaje").html('Información Guardada Correctamente');  
                    $("#modalMensajes").modal('show'); 
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal('hide'); 
                        window.location="LISTAR_GF_HOMOLOGACION_CUENTAS_VA.php";
                    })
                    
                } else {
                    $("#mensaje").html('No Se Ha Podido Guardar La Información');  
                    $("#modalMensajes").modal('show'); 
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal('hide'); 
                    })
                }
            }
           })
        }                                                                                                                                                                                                    
    </script>                                                                                                                                                                                                       
</body>
</html>



