<?php 
  require_once('Conexion/conexion.php');
  require_once('Conexion/ConexionPDO.php');
    require_once('./jsonPptal/funcionesPptal.php');
  require_once './head.php';
  $con = new ConexionPDO();
  $compania = $_SESSION['compania'];
  $panno    = $_SESSION['anno'];
  $anno     = anno($panno);
?>
<html>
    <head>
        <title>Copiar Tablas</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script src="dist/jquery.validate.js"></script>
        <script src="js/md5.pack.js"></script>
        <style>

            label #tabla-error,#compania-error , #tabla1-error,#compania1-error, #anno-error {
                display: block;
                color: #bd081c;
                font-weight: bold;
                font-style: italic;
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
                rules: {
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
         $().ready(function() {
            var validator = $("#form2").validate({
                ignore: "",
                errorPlacement: function(error, element) {
                    $( element )
                        .closest( "form2" )
                        .find( "label[for='" + element.attr( "id" ) + "']" )
                        .append( error );
                },
                rules: {
                }
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
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Copiar Tablas</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form">
                        <?php if($_GET['t']==1) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardar()" >  
                            <input type="hidden" name="action" id="action" value="1">
                            <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <br/>
                            <h4 id="forma-titulo1" align="left" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;"><i><strong>Copiar Tablas A Companías</strong></i></h4>
                            <br/>
                            <br/>
                            <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="tabla" class="col-sm-12 control-label"><strong class="obligado">*</strong>Tabla:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tabla" id="tabla" class="form-control select2" title="Seleccione Tabla" style="height: auto;" required="required" >
                                        <option value="">Tabla</option>
                                        <option value="3">Centro Costo</option>
                                        <option value="10">Dependencia</option>
                                        <option value="11">Dependencia Responsable</option>
                                        <option value="4">Mes</option>
                                        <option value="6">Plan Contable</option>
                                        <option value="8">Plan Inventario</option>
                                        <option value="7">Plan Presupuestal</option>                                     
                                        <option value="5">Terceros</option>
                                        <option value="1">Tipo Comprobantes</option>
                                        <option value="9">Tipo Movimiento</option>
                                        <option value="2">Tipo Retención</option>
                                        <option value="12">Fuentes</option>
                                        <option value="13">Tipo Documento</option>
                                        <option value="14">Cuentas Específicas</option>
                                        <option value="15">Configuración Distribución Centros de Costos</option>         
                                        <option value="16">Configuración Traslado de Costos</option>   
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="compania" class="col-sm-12 control-label"><strong class="obligado">*</strong>Compañia:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="compania" id="compania" class="form-control select2" title="Seleccione Compañia" style="height: auto;" required="required">
                                        <option value="">Compañia</option>
                                        <option value="0">Todas</option>
                                        <?php 
                                        $row = $con->Listar("SELECT 
                                            DISTINCT t.id_unico, CONCAT_WS(' - ',  
                                            IF(CONCAT_WS(' ',
                                             t.nombreuno,
                                             t.nombredos,
                                             t.apellidouno,
                                             t.apellidodos) 
                                             IS NULL OR CONCAT_WS(' ',
                                             t.nombreuno,
                                             t.nombredos,
                                             t.apellidouno,
                                             t.apellidodos) = '',
                                             (t.razonsocial),
                                             CONCAT_WS(' ',
                                             t.nombreuno,
                                             t.nombredos,
                                             t.apellidouno,
                                             t.apellidodos)), 
                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                 t.numeroidentificacion, 
                                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) ) 
                                            FROM  gf_tercero t 
                                            LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero  
                                            WHERE t.id_unico != $compania AND pt.perfil = 1 ");

                                        for ($i = 0; $i < count($row); $i++) {
                                            echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                        }    
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:20px">
                                    <button type="submit"  class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Copiar">
                                        <li class="glyphicon glyphicon-duplicate"></li>
                                    </button>
                                </div>
                                <br/>
                                <br/>
                                <div id="divcuenta" class="form-group form-inline" style="display: none;margin-top: 5px; margin-left: 30px">
                                    <div class="form-group form-inline  col-md-6 col-lg-6" text-aling="left">
                                        <label for="codi_cuenta" class="col-sm-12 control-label"><strong class="obligado"></strong>Código Cuenta:</label>
                                    </div>
                                    <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                        <input name="codi_cuenta" id="codi_cuenta" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php }  else { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:guardar()" >  
                            <input type="hidden" name="action" id="action" value="2">
                            <input type="hidden" name="igual" id="igual" value="0">
                            <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <br/>
                            <h4 id="forma-titulo1" align="left" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;"><i><strong>Copiar Tablas A Año</strong></i></h4>
                            <br/>
                            <br/>
                            <div class="form-group form-inline" style="margin-top: -5px; margin-left: 0px">
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="tabla" class="col-sm-12 control-label"><strong class="obligado">*</strong>Tabla:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="tabla" id="tabla" class="form-control select2" title="Seleccione Tabla" style="height: auto;" required="required" >
                                        <option value="">Tabla</option>
                                        <option value="1">Centro Costo</option>
                                        <option value="6">Conceptos</option>                                     
                                        <option value="5">Fuentes</option>
                                        <option value="2">Mes</option>                                        
                                        <option value="7">Tipo Retención</option> 
                                        <option value="3">Plan Contable</option>
                                        <option value="4">Plan Presupuestal</option>                                     
                                        <option value="8">Configuración Concepto Rubro Cuenta</option>                                     
                                        <option value="9">Bancos</option>         
                                        <option value="10">Configuración Distribución Centros de Costos</option>         
                                        <option value="11">Configuración Traslado de Costos</option>         
                                        <option value="12">Configuración Depreciación</option>         
                                        <option value="13">Configuración Amortizaciones</option>         
                                        <option value="14">Protocolos Informes</option>         
                                        <option value="15">Configuración Informes</option>         
                                        <option value="16">Exógenas</option>       
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" text-aling="left">
                                    <label for="anno" class="col-sm-12 control-label"><strong class="obligado">*</strong>Año:</label>
                                </div>
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:10px">
                                    <select name="anno" id="anno" class="form-control select2" title="Seleccione Año" style="height: auto;" required="required">
                                        <?php 
                                        $row = $con->Listar("SELECT  DISTINCT id_unico, anno 
                                            FROM gf_parametrizacion_anno 
                                            WHERE id_unico != $panno AND compania = $compania AND anno>($anno)");
                                        if(count($row)>0){
                                            for ($i = 0; $i < count($row); $i++) {
                                                echo '<option value="'.$row[$i][0].'">'.$row[$i][1].'</option>';
                                            } 
                                        }
                                        else {
                                            echo '<option value="">Año</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:20px">
                                    <button type="submit"  class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Copiar">
                                        <li class="glyphicon glyphicon-duplicate"></li>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
                    </div>    
                </div>   
            </div>
        </div>
      <?php require_once 'footer.php'; ?>
      <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/md5.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            $(".select2").select2({
                allowClear:true
            });
        </script>
        <script>
            function guardar(){
                if($("#action").val()==2){
                    if($("#tabla").val()==7 || $("#tabla").val()==9){
                        $("#mensaje1").html('¿Desea copiar la misma configuración de cuentas contables?');
                        $("#mdlMensajes1").modal("show");
                        $("#btnAceptar").click(function(){
                            $("#igual").val('1');
                            guardar1();
                        })
                        $("#btnCancelar").click(function(){
                            $("#igual").val('2');
                            guardar1();
                        })
                    } else {
                        guardar1();
                    }
                } else {
                    guardar1();
                }
            }
            function guardar1(){
                //alert('sss');
                jsShowWindowLoad('Copiando Información...');
                var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_copiarTablasJson.php",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response+' - G');
                        let resultado = JSON.parse(response);
                        let data      = resultado["d"];
                        let html      = resultado["rta"];
                        if(data==0){
                            $("#mensaje").html('No Se Ha Podido Copiar Información, Verifique si hay datos para copiar o si ya se copiaron todos los datos.');
                            $("#mdlMensaje").modal("show");
                        } else {
                            $("#mensaje").html(data+' Registros Copiados Correctamente <br/>'+html);
                            $("#mdlMensaje").modal("show");
                        }
                        $("#btnOk").click(function(){
                            document.location.reload();
                        })

                    }
                });
            }
            $("#tabla").change(function(){
                
                if($("#tabla").val()==14){
                    console.log($("#tabla").val()==14);
                    $("#divcuenta").css('display', 'inline-block');
                } else {
                    $("#divcuenta").css('display', 'none');
                }
            })
        </script>
        <div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnOk" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div> 
        <div class="modal fade" id="mdlMensajes1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje1" name="mensaje1" style="font-weight: normal"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>   
    </body>
</html>

