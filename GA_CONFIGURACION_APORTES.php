<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
require_once('./jsonPptal/funcionesPptal.php');
$compania   =$_SESSION['compania'];
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$tl = 0;
$t = '';
if($_REQUEST['t']==1){
    $t  = 'Ingresos';
    $t1 = 1;
}else{
    $t = 'Giros';
    $t1 = 2;
}
$row = $con->Listar("SELECT cc.id_unico, UPPER(c.sigla), LOWER(c.nombre), 
LOWER(cf.nombre), UPPER(rbc.codi_presupuesto), LOWER(rbc.nombre), 
UPPER(rb.codi_presupuesto), LOWER(rb.nombre), LOWER(f.nombre)  
FROM  ga_configuracion_concepto cc
LEFT JOIN ga_concepto c ON cc.concepto_aporte = c.id_unico 
LEFT JOIN gf_concepto_rubro cr ON cc.concepto_rubro = cr.id_unico 
LEFT JOIN gf_rubro_pptal rbc ON cr.rubro = rbc.id_unico 
LEFT JOIN gf_concepto cf ON cr.concepto = cf.id_unico 
LEFT JOIN gf_rubro_fuente rf ON cc.rubro_fuente = rf.id_unico 
LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico 
LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
WHERE cc.tipo = $t1 AND cc.parametrizacionanno = $anno ");
?>
<head>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <title>Configuración de Aportes</title>
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
    <style>
    label#conceptoA-error, #conceptoF-error,#rubroF-error{
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
    
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top: -15px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 0px; margin-right: 4px; margin-left: 4px;">Configuración Concepto Aporte - <?php echo $t;?></h2>
                <div style="border: 4px solid #020324;margin-top: 10px; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                         <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                            <input type="hidden" name="tipo" id="tipo" value="<?php echo $t1;?>">
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                <label for="conceptoA" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Aporte:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
                                <select name="conceptoA" id="conceptoA" class="form-control select2" title="Seleccione Concepto Aporte" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Concepto Aporte</option>';
                                        $tr = $con->Listar("SELECT id_unico, sigla, 
                                            LOWER(nombre) FROM ga_concepto 
                                            WHERE compania = $compania AND 
                                            id_unico NOT IN (SELECT concepto_aporte FROM ga_configuracion_concepto WHERE tipo = $t1 )");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][1].' - '.ucwords($tr[$i][2]).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                <label for="conceptoF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Concepto Financiero:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                <select name="conceptoF" id="conceptoF" class="form-control select2" title="Seleccione Concepto Financiero" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Concepto Financiero</option>';
                                        $tr = $con->Listar("SELECT cr.id_unico, LOWER(c.nombre), UPPER(rb.codi_presupuesto), LOWER(rb.nombre) 
                                            FROM gf_concepto_rubro cr 
                                            LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                            LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
                                            WHERE c.clase_concepto =$t1 AND c.parametrizacionanno =$anno ");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords($tr[$i][1]).' - '.$tr[$i][2].' '.ucwords($tr[$i][3]).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="rubroF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Rubro Fuente:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
                                <select name="rubroF" id="rubroF" class="form-control select2" title="Seleccione Rubro Fuente" style="height: auto " required="required">
                                    <option value="">Rubro Fuente</option>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:-20px">
                                <button type="submit" style="margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Concepto Aporte</strong></td>
                                    <td><strong>Concepto Financiero</strong></td>
                                    <td><strong>Rubro Presupuestal</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto Aporte</th>
                                    <th>Concepto Financiero</th>
                                    <th>Rubro Presupuestal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>

                                <tr>
                                    <td style="display: none;"></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td><?php echo $row[$i][1].' - '.ucwords(mb_strtolower($row[$i][2])); ?></td>  
                                    <td><?php echo 'Concepto: '.ucwords(mb_strtolower($row[$i][3])).' - Rubro: '.$row[$i][4].' - '.ucwords(mb_strtolower($row[$i][5])); ?></td>
                                    <td><?php echo 'Rubro: '.$row[$i][6].' - '.ucwords(mb_strtolower($row[$i][7])).' - Fuente: '.ucwords(mb_strtolower($row[$i][8])); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    
    
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
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              var form_data = {action:2,id:id};
              $.ajax({
                  type:"POST",
                  url:"jsonPptal/ga_control_cooperativo.php",
                  data: form_data,
                  success: function (data) {
                    console.log(data);
                    result = JSON.parse(data);
                    if (result == true) {
                        $("#mensaje").html("Información Eliminada Correctamente");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                    } else { 
                        $("#mensaje").html("No se ha podido eliminar la información");
                        $("#modalMensajes").modal("show");
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        })
                    }
                }
            });
        });
    }
    </script>
    <script>
        $("#conceptoF").change(function(){
            $("#rubroF").val("");
            var concepto = $("#conceptoF").val();
            if(concepto ==""){
            } else {
                var form_data= {action: 8, concepto:concepto};
                var opcion = '<option value="" >Rubro Fuente</option>';
                $.ajax({
                    type:"POST",
                    url: "jsonPptal/gn_nomina_financieraJson.php",
                    data: form_data,
                    success: function(response){
                        console.log(response );
                        opcion +=response;
                        $("#rubroF").html(opcion).focus();
                    }
               })
            }
        })
    </script> 
    <script>
        function guardar(){
            jsShowWindowLoad('Guardando');
            var formData = new FormData($("#form")[0]);  
            $.ajax({
                type: 'POST',
                url: "jsonPptal/ga_control_cooperativo.php?action=1",
                data:formData,
                contentType: false,
                processData: false,         
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);
                    if(response ==true){
                      $("#mensaje").html("Información Guardada Correctamente");
                      $("#modalMensajes").modal("show");
                      $("#Aceptar").click(function(){
                          document.location.reload();
                      })
                    } else {
                      $("#mensaje").html("No Se Ha Podido Guardar La Información");
                      $("#modalMensajes").modal("show");
                      $("#Aceptar").click(function(){
                          $("#mdlMensajes").modal("hide");
                      })
                    }
                }//Fin succes.
            }); 
        }
    </script>
    <script type="text/javascript"> 
        $("#conceptoA").select2();
        $("#conceptoF").select2();
        $("#rubroF").select2();
    </script>
</body>
</html>





