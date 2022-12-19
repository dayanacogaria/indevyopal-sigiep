<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#06/04/2018 | Erica G. | Pasar Saldos Si Todas Las cuentas no estan configuradas
#01/02/2018 | Erica G. | Archivo Creado
##########################################################################################
require_once './Conexion/ConexionPDO.php';
require_once './head_listar.php';
require_once './jsonPptal/funcionesPptal.php';
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$nanno = anno($anno);
$nanno2 = $nanno+1;
$cann2 = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = $nanno2 AND compania = ".$_SESSION['compania']);
if(count($cann2)>0){
    $anno2 = $cann2[0][0];
} else {
    $anno2 = 0;
}
?>
<html>
    <head>
        <title>Pasar Saldos Año Siguiente</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            label #sltTipo-error {
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
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                    <h2 align="center" class="tituloform">Preparar Saldos</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:enviar()" >  
                            
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="sltTipo" class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Tipo</label>
                                <select required="required" name="sltTipo" id="sltTipo" class="form-control select2_single" title="Seleccione Tipo Informe">
                                    <option value="">Tipo</option>              
                                    <option value="1">Cuentas Iguales</option>
                                    <option value="2">Cuentas Homologadas</option>
                                </select>
                            </div> 
                            <div align="center">
                                <button type="submit" id="enviar" name="enviar" class="btn btn-primary sombra" style="margin-top: 0px; margin-bottom: 10px; margin-left: -100px;" >Generar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                    <?php 
                    $row = $con->Listar("SELECT dc.id_unico,
                            c.codi_cuenta, LOWER(c.nombre), 
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
                            t.apellidodos)) AS NOMBRE,
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                            t.numeroidentificacion, 
                            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                            LOWER(cc.nombre), 
                            LOWER(p.nombre), 
                            c.naturaleza,
                            dc.valor 
                        FROM 
                            gf_detalle_comprobante dc 
                        LEFT JOIN 
                            gf_cuenta c ON dc.cuenta = c.id_unico 
                        LEFT JOIN 
                            gf_tercero t ON dc.tercero = t.id_unico 
                        LEFT JOIN 
                            gf_centro_costo cc ON dc.centrocosto = cc.id_unico 
                        LEFT JOIN 
                            gf_proyecto p ON dc.proyecto = p.id_unico 
                        LEFT JOIN 
                            gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                        LEFT JOIN 
                            gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                        WHERE cn.parametrizacionanno = $anno2 AND tc.clasecontable = 5 ");
                    if(count($row)>0) {
                    ?>
                    <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                        <h2 align="center" class="tituloform" style="margin-top:-3px"><?php echo 'Saldos Iniciales Año '.$nanno2?></h2>
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td class="cabeza" style="display: none;">Identificador</td>
                                        <td class="cabeza" width="30px"></td>
                                        <td class="cabeza"><strong>Cuenta</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Centro Costo</strong></td>
                                        <td class="cabeza"><strong>Proyecto</strong></td>
                                        <td class="cabeza"><strong>Valor Débito</strong></td>
                                        <td class="cabeza"><strong>Valor Crédito</strong></td>                                    
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Cuenta</th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Centro Costo</th>
                                        <th class="cabeza">Proyecto</th>
                                        <th class="cabeza">Valor Débito</th>
                                        <th class="cabeza">Valor Crédito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sumd =0;
                                    $sumc =0;
                                        for ($i = 0; $i < count($row); $i++) {
                                            echo '<tr>';
                                            echo '<td class="campos" style="display: none;">Identificador</td>';
                                            echo '<td class="cabeza" width="30px"></td>';
                                            echo '<td>'.$row[$i][1].' - '. ucwords($row[$i][2]).'</td>';
                                            echo '<td>'.ucwords(mb_strtolower($row[$i][3])).' - '. $row[$i][4].'</td>';
                                            echo '<td>'. ucwords($row[$i][5]).'</td>';
                                            echo '<td>'. ucwords($row[$i][6]).'</td>';
                                            $debito     = 0;
                                            $credito    = 0;
                                            if($row[$i][7]==1){
                                                if($row[$i][8]>0){
                                                    $debito = $row[$i][8];
                                                } else {
                                                    $credito = $row[$i][8]*-1;
                                                }
                                            } elseif($row[$i][7]==2){
                                                if($row[$i][8]<0){
                                                    $debito = $row[$i][8]*-1;
                                                } else {
                                                    $credito = $row[$i][8];
                                                }
                                            }
                                            echo '<td>'. number_format($debito, 2, '.',',').'</td>';
                                            echo '<td>'. number_format($credito, 2, '.',',').'</td>';
                                            echo '</tr>';
                                            $sumd +=$debito;
                                            $sumc +=$credito;  
                                        }
                                    ?>
                                    
                                </tbody>
                            </table>
                            <div class="col-sm-offset-8  col-sm-6 text-left">
                                <div class="col-sm-2">
                                    <div class="form-group" style="margin-top:5px" align="left">                                    
                                        <label class="control-label">
                                            <strong>Totales</strong>
                                        </label>                                
                                    </div>
                                </div>                        
                                <div class="col-sm-2 text-right" style="margin-top:10px;" align="left">
                                    <label class="control-label" title="Suma débito"><?php echo number_format($sumd, 2, '.', ',') ?></label>
                                    
                                </div>                        
                                <div class="col-sm-2  col-sm-offset-1" style="margin-top:10px;" align="left">
                                    <label class="control-label" title="Suma crédito"><?php echo number_format($sumc, 2, '.', ',') ?></label>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>    
            </div>
        </div>
    <?php require_once 'footer.php' ?>  
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script>
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
    </script>
    <div class="modal fade" id="myModalMsj" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje2" name="mensaje2" style="font-weight:normal"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="BtnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="myModalMsj2" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje22" name="mensaje22" style="font-weight:normal"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="BtnAceptar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje" style="font-weight:normal"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
              <button type="button" id="cancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
          </div>
        </div>
    </div>
    <script>
        function enviar(){
            jsShowWindowLoad('Validando...');
            var form_data = { action:1 };
            $.ajax({
              type: "POST",
              url: "jsonPptal/gf_pasar_saldosJson.php",
              data: form_data,
              success: function(response)
              { 
                  jsRemoveWindowLoad();
                  var anno2 =response; 
                  if(response ==0){
                      $("#mensaje2").html("Año Siguiente No Ha Sido Creado");
                      $("#myModalMsj").modal("show");
                      $("#BtnAceptar").click(function(){
                          $("#myModalMsj").modal("hide");
                      })
                  } else {
                      jsShowWindowLoad('Validando...');
                        var form_data = { action:2, anno2 :anno2 };
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/gf_pasar_saldosJson.php",
                            data: form_data,
                            success: function(response)
                            {
                                jsRemoveWindowLoad();
                                console.log(response);
                                if(response>0){
                                    $("#mensaje").html("Comprobante De Saldos Iniciales, Ya Ha Sido Creado.<br/> ¿Desea Reemplazarlo?");
                                    $("#myModal").modal("show");
                                    $("#aceptar").click(function(){
                                        generar(anno2);
                                    })
                                    $("#cancelar").click(function(){
                                        $("#myModal").modal("hide");
                                    })
                                } else {
                                    generar(anno2);
                                }
                            }
                        })
                  }
              }
            });
        }
        function generar(anno2){
            jsShowWindowLoad('Validando...');
            var tipo = $("#sltTipo").val();
            var form_data = { action:3, anno2 :anno2, tipo:tipo };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_pasar_saldosJson.php",
                data: form_data,
                success: function(response)
                {
                    jsRemoveWindowLoad();
                    console.log('Cuentas '+response);
                    resultado = JSON.parse(response);
                    var msj = resultado["msj"];
                    var rta = resultado["rta"];
                    //rta = 0;
                    if(rta =='NA'){
                        $("#mensaje2").html("No Se Encontraron Movimientos En Este Año");
                        $("#myModalMsj").modal("show");
                        $("#BtnAceptar").click(function(){
                            document.location.reload();
                        })
                        $("#cancelar").click(function(){
                            $("#myModal").modal("hide");
                        })
                    } else {
                        if(rta==0){
                            jsShowWindowLoad('Guardando...');
                            //Guardar Interfaz
                            var form_data = { action:5, anno2 :anno2, tipo:tipo };
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_pasar_saldosJson.php",
                                data: form_data,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log('Guardar');
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje2").html("Saldos Guardados Correctamente");
                                        $("#myModalMsj").modal("show");
                                        $("#BtnAceptar").click(function(){
                                            document.location.reload();
                                        })
                                    } else {
                                        $("#mensaje2").html("No Se Han Podido Guardar Saldos");
                                        $("#myModalMsj").modal("show");
                                        $("#BtnAceptar").click(function(){
                                            document.location.reload();
                                        })

                                    }

                                }
                            })
                        } else {
                            jsShowWindowLoad('Validando...');
                            var form_data = { action:4, anno2 :anno2, tipo:tipo };
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_pasar_saldosJson.php",
                                data: form_data,
                                success: function(response)
                                {
                                     jsRemoveWindowLoad();
                                    console.log('Link'+response);
                                    $("#mensaje22").html("Las Cuentas Que No Se Encontraron: <br/><a href='"+response+"' download>Descargar</a>");
                                    $("#myModalMsj2").modal("show");
                                    $("#BtnAceptar2").click(function(){
                                        $("#myModalMsj2").modal("hide");
                                        $("#mensaje").html("¿Desea Generar Saldos Iniciales Con Cuentas Encontradas? <br/>Recuerde Que No Quedará Completo");
                                        $("#myModal").modal("show");
                                        $("#aceptar").click(function(){
                                            jsShowWindowLoad('Guardando...');
                                            //Guardar Interfaz
                                            var form_data = { action:5, anno2 :anno2, tipo:tipo };
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_pasar_saldosJson.php",
                                                data: form_data,
                                                success: function(response)
                                                {
                                                    jsRemoveWindowLoad();
                                                    console.log('Guardar');
                                                    console.log(response);
                                                    if(response==1){
                                                        $("#mensaje2").html("Saldos Guardados Correctamente");
                                                        $("#myModalMsj").modal("show");
                                                        $("#BtnAceptar").click(function(){
                                                            document.location.reload();
                                                        })
                                                    } else {
                                                        $("#mensaje2").html("No Se Han Podido Guardar Saldos");
                                                        $("#myModalMsj").modal("show");
                                                        $("#BtnAceptar").click(function(){
                                                            document.location.reload();
                                                        })

                                                    }

                                                }
                                            })
                                        })
                                        $("#cancelar").click(function(){
                                            $("#myModal").modal("hide");
                                        })
                                    })
                                }
                            })
                        }
                    }  
                }
            })
            
        }
    </script>
    
    </body>
</html>