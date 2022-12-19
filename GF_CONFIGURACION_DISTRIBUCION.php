<?php
########################################################################################
#       ***************    Modificaciones *************** #
########################################################################################
#04/04/2019 | Creado
########################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Configuración Distribución Costos</title>
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Distribución Costos</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Concepto:</label>
                                <select name="concepto" id="concepto" class="select2_single form-control" title="Seleccione concepto" style="height: auto " required>
                                    <?php 
                                    if(empty($_GET['c'])) { 
                                        echo '<option value="">Concepto</option>';
                                        $vg = $con->Listar("SELECT id_unico, nombre 
                                            FROM gf_concepto 
                                            WHERE parametrizacionanno = $anno 
                                                AND clase_concepto = 2");
                                        
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                        }
                                    } else {
                                        $vg1 = $con->Listar("SELECT id_unico, nombre FROM gf_concepto WHERE id_unico =".$_GET['c']);
                                        echo '<option value="'.$vg1[0][0].'">'.$vg1[0][1].'</option>';
                                        $vg = $con->Listar("SELECT id_unico, nombre 
                                            FROM gf_concepto 
                                            WHERE parametrizacionanno = $anno 
                                                AND clase_concepto = 2 
                                                AND id_unico !=".$_GET['c']);
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].'</option>'; 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <br/>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed"  class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Centro De Costo</strong></td>
                                        <td><strong>Cuenta</strong></td>
                                        <td><strong>Porcentaje</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Centro De Costo</th>
                                        <th>Cuenta</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $por    = 100;
                                    $totala = 0;
                                    if(!empty($_GET['c'])) { 
                                        $concepto = $_GET['c'];
                                        #**** Centros de costo ****#
                                        $conc = $con->Listar("SELECT id_unico, UPPER(sigla), nombre  
                                            FROM gf_centro_costo  
                                            WHERE movimiento = 1 AND parametrizacionanno = $anno 
                                                ORDER BY sigla ASC ");
                                        #****** Buscar Si El Porcentaje ==100*****#
                                        $p      = $con->Listar("SELECT SUM(cf.porcentaje) FROM gf_configuracion_distribucion cf 
                                                WHERE cf.concepto = $concepto");
                                        $por    -= $p[0][0];
                                        $totala += $p[0][0];
                                        # Ciclo Centros de costo
                                        for ($j = 0; $j < count($conc); $j++) {
                                            #Buscar Si Hay Configuración Guardada
                                            $cf = $con->Listar("SELECT cd.id_unico, 
                                                cc.id_unico, LOWER(cc.nombre), UPPER(cc.sigla), 
                                                c.id_unico, c.codi_cuenta, LOWER(c.nombre), 
                                                cd.porcentaje 
                                                FROM gf_configuracion_distribucion cd 
                                                LEFT JOIN gf_centro_costo cc ON cd.centro_costo = cc.id_unico 
                                                LEFT JOIN gf_cuenta c ON cd.cuenta = c.id_unico 
                                                WHERE cd.centro_costo =".$conc[$j][0]." 
                                                AND cd.concepto = $concepto");
                                            
                                            if($cf!=NULL){
                                                for ($z = 0; $z < count($cf); $z++) {
                                                    echo '<tr>';
                                                    echo '<form name="form'.$cf[$z][0].'" id="form'.$cf[$z][0].'" method="POST" action="javascript:modificar()">';
                                                    echo '<td style="display: none;">'.$conc[$j][0].'</td>';
                                                    echo '<td>'; 
                                                    echo '<a  onclick="javascript:eliminar('.$cf[$z][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                                    echo '</td>';
                                                    echo '<td>'.$conc[$j][1].' - '.ucwords($conc[$j][2]).'</td>'; 
                                                    echo '<td>'.$cf[$z][5].' - '.ucwords($cf[$z][6]).'</td>'; 
                                                    echo '<td>'.number_format($cf[$z][7],2,',','.').'%</td>'; 
                                                    echo '</form>'; 
                                                    echo '</tr>';
                                                }
                                            } else {
                                                $max = $por;
                                                echo '<tr>';
                                                echo '<form name="form'.$conc[$j][0].'" id="form'.$conc[$j][0].'" method="POST" action="javascript:guardar('.$conc[$j][0].')">';
                                                echo '<td style="display: none;"><input type ="hidden" name="concepto" id="concepto" value ="'.$concepto.'">';
                                                echo '<input type ="hidden" name="centro_costo" id="centro_costo" value ="'.$conc[$j][0].'"></td>';
                                                echo '<td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>';
                                                echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                                $cfv = $con->Listar("SELECT c.id_unico, 
                                                    c.codi_cuenta, LOWER(c.nombre) 
                                                    FROM gf_cuenta c 
                                                    WHERE c.parametrizacionanno = $anno 
                                                    AND c.centrocosto = 1 
                                                    AND (c.clasecuenta = 7 OR c.clasecuenta  = 17 OR c.clasecuenta  = 18)");
                                                echo '<td>';
                                                echo '<select name="cuenta'.$conc[$j][0].'" id="cuenta'.$conc[$j][0].'" class="select2_single form-control" required>';
                                                echo '<option value ="">Cuenta</option>';
                                                for($z =0; $z < count($cfv); $z++){
                                                    echo '<option value ="'.$cfv[$z][0].'">'.ucwords($cfv[$z][1]).' - '.ucwords($cfv[$z][2]).'</option>';
                                                }
                                                echo '</select>';
                                                echo '</td>'; 
                                                echo '<td><input type="number" step="0.01" name="porcentaje'.$conc[$j][0].'" id="porcentaje'.$conc[$j][0].'" class="form_control" value="0" style="width:40px" onkeyup="return validarNum1('.$conc[$j][0].')" required>%';
                                                echo '<input type="hidden" name="max'.$conc[$j][0].'" id="max'.$conc[$j][0].'" value="'.$max.'">';
                                                echo '<label name="msjep'.$conc[$j][0].'" id="msjep'.$conc[$j][0].'"></label>';
                                                echo '</form>';
                                                echo '</tr>';
                                            }
                                        }
                                    } ?>
                                </tbody>
                            </table>       
                        </div>            
                    </div>   
                    <?php 
                    echo '<div class="col-sm-10">';
                    echo '<label>Total Asignado: '.$totala.'%</label><br/>';
                    echo '<label>Porcentaje Por asignar: '.(100 - $totala).'%</label>';
                    echo '</div>';
                    echo '<div class="col-sm-2" style="margin-top: 5px;text-align: right;">';
                    echo '<button id="imprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Informe Configuración">
                                <li class="glyphicon glyphicon-print"></li>
                            </button>';
                    echo '</div>';
                    
                    ?>
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
        <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <label id="mensajeEliminar" name="mensaje"></label>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    allowClear: true,
                });
            });
        </script> 
        <script>
            $("#concepto").change(function(){
                var concepto = $("#concepto").val();
                if(concepto!=""){
                    document.location ='GF_CONFIGURACION_DISTRIBUCION.php?c='+concepto;
                }
            })
        </script>
        <!----**Funcion Validar Porcentaje**---->
        <script>
            function validarNum1(id){
                
            event = event || window.event;
            let charCode = event.keyCode || event.which;
            let first = (charCode <= 57 && charCode >= 48);
            let numero = $("#porcentaje"+id).val();
            console.log(numero);
            let char = parseFloat(String.fromCharCode(charCode));
            let num = parseFloat(numero+char);
            let com = parseFloat($("#max"+id).val());
            console.log(num);
            console.log(com);
            let match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            let dec = match[0].length;
            if(dec<=3){
                console.log(num <= com);
                if(num <= com){
                    if (charCode ==46){
                        let element = event.srcElement || event.target;
                        if(element.value.indexOf('.') == -1){
                        return (charCode =46);
                        }else{
                           return first; 
                        }
                        } else {
                        return first;
                    }
                } else {
                    if(num <=com){
                        return first;
                    }else{
                        let numero = $("#porcentaje"+id).val('');
                        return false;
                    }
                }
            } else { 
                let numero = $("#porcentaje"+id).val('');
                return false;
            }
        }
        </script> 
        <!----**Funcion Guardar Configuracion**---->
        <script>
            function guardar(id){
                var nam = 'form'+id;
                var formData = new FormData($("#"+nam)[0]);  
                $.ajax({
                    type: 'POST',
                    url: "jsonPptal/gf_distribucion_costosJson.php?action=1",
                    data:formData,
                    contentType: false,
                    processData: false,
                    success: function(response)
                    { 
                        console.log(response);
                        if(response==0){
                            $("#mensaje").html('Información Guardada Correctamente');  
                            $("#modalMensajes").modal('show'); 

                        } else {
                            $("#mensaje").html('No Se Ha Podido Guardar La Información');  
                            $("#modalMensajes").modal('show'); 
                        }
                    }
                })
            }                                                                                                                                                                                                    
        </script>
        <!----**Funcion Eliminar Configuración**---->
        <script>
            function eliminar(id){
                $("#mensajeEliminar").html('¿Desea Eliminar El Registro De Configuración?');  
                $("#modalEliminar").modal('show'); 
                $("#btnAceptar").click(function(){
                    $("#modalEliminar").modal('hide');  
                    var form_data ={id:id,action:2}
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_distribucion_costosJson.php",
                        data: form_data,
                        success: function(response)
                        { 
                            console.log(response);
                            if(response==0){
                                $("#mensaje").html('Información Eliminada Correctamente');  
                                $("#modalMensajes").modal('show'); 

                            } else {
                                $("#mensaje").html('No Se Ha Podido Eliminar La Información');  
                                $("#modalMensajes").modal('show'); 
                            }
                        }
                    });
                });
                $("#btnAceptar").click(function(){
                   $("#modalEliminar").modal('hide');  
                });
            }                                                                                                                                                                                                 
        </script>
        <script>
            $("#Aceptar").click(function(){
               document.location.reload();
            });
        </script>
        <script>
            $("#imprimir").click(function(){
                window.open('informes/INF_CONFIGURACION_DISTRIBUCION.php');
            })
        </script>
    </body>
</html>
</html>

