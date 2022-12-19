<?php
#############################################################################
#       ******************     Modificaciones       ******************      #
#############################################################################
#26/03/2018 |Erica G. | ARCHIVO CREADO
#############################################################################
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
$con = new ConexionPDO();        
require './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

?>
<html>
    <head>
        <title>Configuración Interfaz Comercio</title>
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
                    <h2 align="center" class="tituloform" style="margin-top:-3px">Configuración Interfaz Comercio</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                            <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="vigencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Vigencia:</label>
                                <select name="vigencia" id="vigencia" class="select2_single form-control" title="Seleccione Vigencia" style="height: auto " required>
                                    <?php 
                                    if(empty($_GET['vg'])) { 
                                        echo '<option value="">Vigencia</option>';
                                        $vg = $con->Listar("SELECT id_unico, nombre, valor FROM gf_vigencias_interfaz_comercio WHERE parametrizacionanno = $anno");
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].' - '.$vg[$i][2].'</option>'; 
                                        }
                                    } else {
                                        $vg1 = $con->Listar("SELECT id_unico, nombre, valor FROM gf_vigencias_interfaz_comercio WHERE id_unico =".$_GET['vg']);
                                        echo '<option value="'.$vg1[0][0].'">'.$vg1[0][1].' - '.$vg1[0][2].'</option>';
                                        $vg = $con->Listar("SELECT id_unico, nombre, valor FROM gf_vigencias_interfaz_comercio WHERE parametrizacionanno = $anno AND id_unico !=".$_GET['vg']);
                                        for ($i = 0; $i < count($vg); $i++) {
                                           echo '<option value="'.$vg[$i][0].'">'.$vg[$i][1].' - '.$vg[$i][2].'</option>'; 
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
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="30px" align="center"></td>
                                        <td><strong>Concepto Comercio</strong></td>
                                        <td><strong>Concepto Financiero</strong></td>
                                        <td><strong>Porcentaje</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th>Concepto Comercio</th>
                                        <th>Concepto Financiero</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($_GET['vg'])) { 
                                        $vigencia = $_GET['vg'];
                                        #**** Conceptos ****#
                                        $conc = $con->Listar("SELECT * FROM gc_concepto_comercial 
                                            WHERE (tipo_ope=2 OR tipo_ope=3) AND clase = 1 ORDER BY codigo ASC ");
                                        # Ciclo De Concepto Comercio
                                        for ($j = 0; $j < count($conc); $j++) {
                                            #Buscar Si Hay Configuración Guardada
                                            $cf = $con->Listar("SELECT cf.id_unico, 
                                                    cf.concepto_comercio, cf.porcentaje, 
                                                    UPPER(rb.codi_presupuesto), LOWER(rb.nombre), 
                                                    LOWER(f.nombre) , (c.nombre)
                                                    FROM gf_configuracion_comercio cf 
                                                    LEFT JOIN gf_vigencias_interfaz_comercio vg ON cf.vigencia = vg.id_unico 
                                                    LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
                                                    LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico 
                                                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                                    LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
                                                    LEFT JOIN gf_fuente f On rf.fuente = f.id_unico 
                                                    WHERE vg.parametrizacionanno = $anno AND cf.vigencia = $vigencia 
                                                    AND cf.concepto_comercio = ".$conc[$j][0]);
                                            if(count($cf)>0){
                                                for ($z = 0; $z < count($cf); $z++) {
                                                    echo '<tr>';
                                                    echo '<form name="form'.$cf[$z][0].'" id="form'.$cf[$z][0].'" method="POST" action="javascript:modificar()">';
                                                    echo '<td style="display: none;">'.$conc[$j][0].'</td>';
                                                    echo '<td>'; 
                                                    echo '<a  onclick="javascript:eliminar('.$cf[$z][0].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                                    echo '<a  href="#" onclick="javascript:open_modal_r('.$cf[$z][0].')"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>';
                                                    echo '</td>';
                                                    echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                                    echo '<td>'.$cf[$z][6].' - '.$cf[$z][3].' - '.ucwords($cf[$z][4]).' - '.ucwords($cf[$z][5]).'</td>'; 
                                                    echo '<td>'.$cf[$z][2].'%</td>'; 
                                                    echo '</form>';
                                                    echo '</tr>';
                                                }
                                                #****** Buscar Si El Porcentaje ==100*****#
                                                $p = $con->Listar("SELECT SUM(cf.porcentaje) FROM gf_configuracion_comercio cf 
                                                        LEFT JOIN gf_vigencias_interfaz_comercio vg ON cf.vigencia = vg.id_unico 
                                                        WHERE vg.parametrizacionanno = $anno AND cf.vigencia = $vigencia 
                                                        AND cf.concepto_comercio = ".$conc[$j][0]);
                                                $por = $p[0][0];
                                                if($por < 100){
                                                    $max = 100 -$por;
                                                    echo '<tr>';
                                                    echo '<form name="form'.$conc[$j][0].'" id="form'.$conc[$j][0].'" method="POST" action="javascript:guardar('.$conc[$j][0].')">';
                                                    echo '<td style="display: none;"><input type ="hidden" name="vigencia" id="vigencia" value ="'.$vigencia.'">';
                                                    echo '<input type ="hidden" name="concepto_comercio" id="concepto_comercio" value ="'.$conc[$j][0].'"></td>';
                                                    echo '<td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>';
                                                    echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                                    $cfv = $con->Listar("SELECT cr.id_unico , 
                                                        rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                                                        FROM gf_concepto_rubro cr 
                                                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                                        LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                                                        LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                                                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                                    WHERE c.parametrizacionanno = $anno AND rf.id_unico IS NOT NULL 
                                                    AND (rb.tipoclase = 6 OR rb.tipoclase = 8)");
                                                    echo '<td>';
                                                    echo '<select name="concepto'.$conc[$j][0].'" id="concepto'.$conc[$j][0].'" class="select2_single form-control" required>';
                                                    echo '<option value ="">Concepto Financiero</option>';
                                                    for($z =0; $z < count($cfv); $z++){
                                                        echo '<option value ="'.$cfv[$z][0].','.$cfv[$z][1].'">'.ucwords($cfv[$z][2]).' - '.$cfv[$z][3].' '.ucwords($cfv[$z][4]).' - '.ucwords($cfv[$z][5]).'</option>';
                                                    }
                                                    echo '</select>';
                                                    echo '</td>'; 
                                                    echo '<td><input type="text" name="porcentaje'.$conc[$j][0].'" id="porcentaje'.$conc[$j][0].'" class="form_control" value="'.$max.'" style="width:30px" onkeypress="return validarNum1('.$conc[$j][0].')" required>%';
                                                    echo '<input type="hidden" name="max'.$conc[$j][0].'" id="max'.$conc[$j][0].'" value="'.$max.'" ></td>'; 
                                                    
                                                    echo '</form>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                $max = 100;
                                                echo '<tr>';
                                                echo '<form name="form'.$conc[$j][0].'" id="form'.$conc[$j][0].'" method="POST" action="javascript:guardar('.$conc[$j][0].')">';
                                                echo '<td style="display: none;"><input type ="hidden" name="vigencia" id="vigencia" value ="'.$vigencia.'">';
                                                echo '<input type ="hidden" name="concepto_comercio" id="concepto_comercio" value ="'.$conc[$j][0].'"></td>';
                                                echo '<td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>';
                                                echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                                $cfv = $con->Listar("SELECT cr.id_unico , 
                                                    rf.id_unico, LOWER(c.nombre), rb.codi_presupuesto, LOWER(rb.nombre), LOWER(f.nombre) 
                                                    FROM gf_concepto_rubro cr 
                                                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                                    LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro 
                                                    LEFT JOIN gf_rubro_pptal rb On rf.rubro = rb.id_unico 
                                                    LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                                WHERE c.parametrizacionanno = $anno AND rf.id_unico IS NOT NULL 
                                                AND (rb.tipoclase = 6 OR rb.tipoclase = 8)");
                                                echo '<td>';
                                                echo '<select name="concepto'.$conc[$j][0].'" id="concepto'.$conc[$j][0].'" class="select2_single form-control" required>';
                                                echo '<option value ="">Concepto Financiero</option>';
                                                for($z =0; $z < count($cfv); $z++){
                                                    echo '<option value ="'.$cfv[$z][0].','.$cfv[$z][1].'">'.ucwords($cfv[$z][2]).' - '.$cfv[$z][3].' '.ucwords($cfv[$z][4]).' - '.ucwords($cfv[$z][5]).'</option>';
                                                }
                                                echo '</select>';
                                                echo '</td>'; 
                                                echo '<td><input type="text" name="porcentaje'.$conc[$j][0].'" id="porcentaje'.$conc[$j][0].'" class="form_control" value="'.$max.'" style="width:30px" onkeypress="return validarNum1('.$conc[$j][0].')" required>%';
                                                echo '<input type="hidden" name="max'.$conc[$j][0].'" id="max'.$conc[$j][0].'" value="'.$max.'" ></td>'; 
                                                echo '</form>';
                                                echo '</tr>';
                                            }
                                        }
                                    } ?>
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
            $("#vigencia").change(function(){
                var vigencia = $("#vigencia").val();
                if(vigencia!=""){
                    document.location ='GF_CONFIGURACION_COMERCIO.php?vg='+vigencia;
                }
            })
        </script>
        <!----**Funcion Validar Porcentaje**---->
        <script>
            function validarNum1(id){
            event = event || window.event;
            var charCode = event.keyCode || event.which;
            var first = (charCode <= 57 && charCode >= 48);
            var numero = $("#porcentaje"+id).val();
            var char = parseFloat(String.fromCharCode(charCode));
            var num = parseFloat(numero+char);
            var com = parseFloat($("#max"+id).val());
            var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
            var dec = match[0].length;
            if(dec<=3){
                if(num <= com){
                    if (charCode ==46){
                        var element = event.srcElement || event.target;
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
                        return false;
                    }
                }
            } else { 
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
                url: "jsonPptal/gf_interfaz_ComercioJson.php?action=4",
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
                    var form_data ={id:id,action:5}
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_interfaz_ComercioJson.php",
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
            function open_modal_r(id) {  

               var form_data={                            
                  id:id 
                };
                 $.ajax({
                    type: 'POST',
                    url: "GF_CONFIGURACION_COMERCIO_MODAL.php#mdlModificar",
                    data:form_data,
                    success: function (data) { 
                        $("#mdlModificar").html(data);
                        $(".movi").modal("show");
                    }
                }).error(function(data,textStatus,jqXHR){
                    alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
                })            
            } 
        </script>
        <script>
            $("#Aceptar").click(function(){
               document.location.reload();
            });
        </script> 
        <?php require_once './GF_CONFIGURACION_COMERCIO_MODAL.php'; ?>
    </body>
</html>

