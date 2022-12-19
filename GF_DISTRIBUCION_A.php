<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];

$rowc = $con->Listar("SELECT cp.id_unico, cn.id_unico, 
        UPPER(tp.codigo), LOWER(tp.nombre), 
        cp.numero, DATE_FORMAT(cp.fecha, '%d/%m/%Y') 
    FROM gf_comprobante_pptal cp 
    LEFT JOIN gf_tipo_comprobante_pptal tp ON cp.tipocomprobante = tp.id_unico
    LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico 
    LEFT JOIN gf_comprobante_cnt cn ON tc.id_unico = cn.tipocomprobante AND cp.numero = cn.numero 
    WHERE tp.clasepptal = 16 AND tp.tipooperacion = 1 AND cn.id_unico IS NOT NULL 
    AND cp.parametrizacionanno =$anno ");
?>
<html>
    <head>
        <title>Distribución Costos</title>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script> 
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="js/md5.pack.js"></script>
    </head>
    <body> 
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 align="center" class="tituloform" style="margin-top:-3px"> Distribución Costos</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed"  class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Tipo Comprobante</strong></td>
                                        <td><strong>Número</strong></td>
                                        <td><strong>Fecha</strong></td>
                                        <td><strong>Ver</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Tipo Comprobante</th>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($i = 0; $i < count($rowc); $i++) {
                                        #** Buscar si ya tiene distribución
                                        $bd = $con->Listar("SELECT * FROM gf_distribucion_costos WHERE pptal =". $rowc[$i][0]." AND cnt =".$rowc[$i][1]);
                                        echo '<tr>';
                                        echo '<td style="display: none;">'.$rowc[$i][0].'</td>';
                                        echo '<td>'; 
                                        if(!empty($bd[0][0])>0){
                                            echo '<a  onclick="javascript:ver('.$rowc[$i][0].','.$rowc[$i][1].')"><i title="ver" class="glyphicon glyphicon-list-alt"></i></a>';
                                            echo '<a  onclick="javascript:generarO('.$rowc[$i][0].','.$rowc[$i][1].')"><i title="Generar De Nuevo" class="glyphicon glyphicon-repeat"></i></a>';
                                        } else {
                                            echo '<a  onclick="javascript:generar('.$rowc[$i][0].','.$rowc[$i][1].')"><i title="Generar" class="glyphicon glyphicon-repeat"></i></a>';
                                        }
                                        echo '</td>';
                                        echo '<td>'.$rowc[$i][2].' - '.ucwords($rowc[$i][3]).'</td>'; 
                                        echo '<td>'.$rowc[$i][4].'</td>'; 
                                        echo '<td>'.$rowc[$i][5].'</td>'; 
                                        echo '<td><a href="GENERAR_CUENTA_PAGAR.php?cxp='.md5($rowc[$i][0]).'" target="_blank" ><i class="glyphicon glyphicon-eye-open"></i></a></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>       
                        </div>            
                    </div> 
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
                        <div id="txt"></div>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalMensajes2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensaje2" name="mensaje2"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="Aceptar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="cancelar2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $("#Aceptar").click(function(){
               document.location.reload();
            });
        </script>
        <script>
            function generar(id, idcnt){
                jsShowWindowLoad('Validando Configuración...')
                var form_data ={id:id,action:3}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        let resultado = JSON.parse(response);
                        let rta = resultado["rta"];
                        let msj = resultado["html"];
                        if(rta>0){
                            $("#mensaje2").html(msj+'<br/>¿Desea Continuar con la distribución?');  
                            $("#modalMensajes2").modal('show'); 
                            $("#Aceptar2").click(function(){
                                tipoc(id,idcnt);
                            });
                            $("#cancelar2").click(function(){
                                $("#modalMensajes2").modal('hide'); 
                            })
                        } else {
                            tipoc(id,idcnt);
                        }
                    }
                });
            }
            function tipoc(id,idcnt){
                jsShowWindowLoad('Validando Configuración...')
                var form_data ={id:id,action:6}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        if(response>0){
                            var form_data ={id:id,idcnt:idcnt,action:7}
                            $.ajax({
                                type: "POST",
                                url: "jsonPptal/gf_distribucion_costosJson.php",
                                data: form_data,
                                success: function(response)
                                { 
                                    $("#mensaje2").html(response+'<br/>¿Desea Continuar con la distribución?');  
                                    $("#modalMensajes2").modal('show'); 
                                    $("#Aceptar2").click(function(){
                                        guardar23(id,idcnt);
                                    });
                                    $("#cancelar2").click(function(){
                                        $("#modalMensajes2").modal('hide'); 
                                    })
                                }
                            })

                        } else {
                           guardar(id,idcnt);
                        }
                    }
                });

            }
            function guardar(id,idcnt){
                jsShowWindowLoad('Generando Distribución...')
                var form_data ={id:id,idcnt:idcnt,action:4}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        jsRemoveWindowLoad();
                        console.log(response);
                        if(response>0){
                            $("#mensaje").html('Distribución generada correctamente');  
                            $("#modalMensajes").modal('show'); 
                            $("#Aceptar").click(function(){
                                document.location.reload();
                            });
                        } else {
                            $("#mensaje").html('No se ha podido guardar distribución');  
                            $("#modalMensajes").modal('show'); 
                            $("#Aceptar").click(function(){
                                document.location.reload();
                            });
                        }
                    } 
                })
            }
            function guardar23(id,idcnt){
                
                //jsShowWindowLoad('Generando Distribución...');
                var numd1 = $("#datos2").val();
                var numd2 = $("#datos1").val();
                var valort = 0;
                let tr =0;
                for (var i = 0; i < numd1; i++) {
                    var valort = 0;
                    for (var j = 0; j < numd2; j++) {
                        var porc = parseFloat($("#porcentaje"+i+j).val());  
                        valort   = parseFloat(valort+porc);
                    }
                    if(valort != 100){
                        tr +=1;
                    }
                }
                if(tr>0){
                    $("#mensaje").html("La sumatoria de los porcentajes no es el 100%<br/>No se puede continuar con la distribución");
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        document.location.reload();
                    });
                } else {
                   guardar2(id,idcnt);
                }
                
            }
            function guardar2(id,idcnt){
                
                jsShowWindowLoad('Generando Distribución...');
                var numd1 = $("#datos2").val();
                var numd2 = $("#datos1").val();
                for (var i = 0; i < numd1; i++) {
                    for (var j = 0; j < numd2; j++) {
                        var nam = 'formg'+i+j;
                        var formData = new FormData($("#"+nam)[0]);  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_distribucion_costosJson.php?action=8&id="+id+"&idcnt="+idcnt+"&pc="+i+j,
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            { 
                                console.log(response);
                                $("#mensaje").html('Distribución generada correctamente');  
                                $("#modalMensajes").modal('show'); 
                                $("#Aceptar").click(function(){
                                    document.location.reload();
                                });
                            }
                        });
                    }
                }
                jsRemoveWindowLoad();
            }
            function ver(id, idcnt){
                var form_data ={id:id,idcnt:idcnt,action:5}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                        $("#txt").html(response);  
                        $("#modalMensajes").modal('show'); 
                        $("#Aceptar").click(function(){
                            document.location.reload();
                        });
                    }
                })
            }
           function cambio(id, id2){
               let valoro = parseFloat($("#valoro"+id+id2).val()); 
               let total  = parseFloat($("#total"+id).val());
               console.log(valoro);
               console.log(total);
               let valora = parseFloat(total+valoro);         
               let valorp = $("#porcentaje"+id+id2).val();
               console.log(valora);
               console.log(valorp);
               if(valora-valorp>0){
                   $("#lbltotal"+id).val(valora-valorp);
                   $("#total"+id).val(valora);      
               } else {
                   $("#porcentaje"+id+id2).val('')
               }
               
           }
           function generarO(id, idcnt){
           		jsShowWindowLoad('Eliminando Información');
           		var form_data ={id:id,idcnt:idcnt,action:16}
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/gf_distribucion_costosJson.php",
                    data: form_data,
                    success: function(response)
                    { 
                    	jsRemoveWindowLoad();
                    	//generar(id, idcnt);
                    }
                })

           }
        </script>
    </body>
</html>
</html>

