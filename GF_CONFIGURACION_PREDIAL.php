<?php
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#15/02/2018| ERICA G. | ARCHIVO CREADO
######################################################################################################################################################################
require_once('Conexion/conexion.php'); 
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
#*** Listar Vigencias ***#
$vg = $con->Listar("SELECT id_unico, nombre FROM gf_vigencias_interfaz_predial WHERE parametrizacionanno = $anno");
#**** Conceptos ****#
$conc = $con->Listar("SELECT * FROM gr_concepto ORDER BY codigo ASC ");

?>
<html>
    <head>
    <title>Configuración Interfaz Predial</title>
   <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js">
    </script>
    <link href="css/select/select2.min.css" rel="stylesheet">    
    </head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Configuración Interfaz Predial</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Concepto</strong></td>
                                    <?php 
                                    for ($i = 0; $i < count($vg); $i++) {
                                      echo '<td><strong>'.$vg[$i][1].'</strong></td>';  
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto</th>
                                    <?php 
                                    for ($i = 0; $i < count($vg); $i++) {
                                      echo '<th>'.$vg[$i][1].'</th>';  
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php 
                                    # Ciclo De Concepto Predial
                                    for ($j = 0; $j < count($conc); $j++) {
                                        #Buscar Si Hay Configuración Guardada
                                        $cf = $con->Listar("SELECT cf.id_unico, 
                                                cf.concepto_predial 
                                                FROM gf_configuracion_predial cf 
                                                LEFT JOIN gf_vigencias_interfaz_predial vg ON cf.vigencia = vg.id_unico 
                                                LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
                                                LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico 
                                                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                                LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
                                                WHERE vg.parametrizacionanno = $anno 
                                                AND cf.concepto_predial = ".$conc[$j][0]);
                                        if(count($cf)>0){
                                            echo '<tr>';
                                            echo '<form name="form'.$cf[0][0].'" id="form'.$cf[0][0].'" method="POST" action="javascript:modificar()">';
                                            echo '<td style="display: none;">'.$cf[0][0].'</td>';
                                            echo '<td>'; 
                                            echo '<a  onclick="javascript:eliminar('.$cf[0][1].')"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>';
                                            echo '<a  href="#" onclick="javascript:open_modal_r('.$cf[0][1].')"><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>';
                                            echo '</td>';
                                            echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                            for ($i = 0; $i < count($vg); $i++) {
                                                $cfv = $con->Listar("SELECT cf.id_unico, 
                                                c.nombre, rb.codi_presupuesto 
                                                FROM gf_configuracion_predial cf 
                                                LEFT JOIN gf_vigencias_interfaz_predial vg ON cf.vigencia = vg.id_unico 
                                                LEFT JOIN gf_concepto_rubro cr ON cf.concepto_financiero = cr.id_unico 
                                                LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico 
                                                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                                LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
                                                WHERE vg.parametrizacionanno = $anno 
                                                AND cf.concepto_predial = ".$conc[$j][0]." AND cf.vigencia = ".$vg[$i][0]);
                                                  echo '<td>'.$cfv[0][1].' - '.$cfv[0][2].'</td>'; 
                                                } 
                                            echo '</form>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<form name="form'.$conc[$j][0].'" id="form'.$conc[$j][0].'" method="POST" action="javascript:guardar('.$conc[$j][0].')">';
                                            echo '<td style="display: none;"><input type ="hidden" name="concepto_predial" id="concepto_predial" value ="'.$conc[$j][0].'">'.'</td>';
                                            echo '<td><button type="submit"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></button></td>';
                                            echo '<td>'.$conc[$j][1].' - '.$conc[$j][2].'</td>'; 
                                            for ($i = 0; $i < count($vg); $i++) {
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
                                                echo '<select name="concepto'.$conc[$j][0].''.$vg[$i][0].'" id="concepto'.$conc[$j][0].''.$vg[$i][0].'" class="select2_single form-control" required>';
                                                echo '<option value ="">Concepto Financiero</option>';
                                                for($z =0; $z < count($cfv); $z++){
                                                    echo '<option value ="'.$cfv[$z][0].','.$cfv[$z][1].'">'.ucwords($cfv[$z][2]).' - '.$cfv[$z][3].' '.ucwords($cfv[$z][4]).' - '.ucwords($cfv[$z][5]).'</option>';
                                                }
                                                echo '</select>';
                                                echo '</td>'; 
                                            } 
                                            echo '</form>';
                                            echo '</tr>';
                                        }
                                    
                                    }?>
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
        $(document).ready(function() {
          $(".select2_single").select2({
            allowClear: true
          });
        });
        
    </script>
    <!----**Funcion Guardar Configuracion**---->
    <script>
        function guardar(id){
            var nam = 'form'+id;
            var formData = new FormData($("#"+nam)[0]);  
            
           $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_interfaz_PredialJson.php?action=4",
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
                    url: "jsonPptal/gf_interfaz_PredialJson.php",
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
                url: "GF_CONFIGURACION_PREDIAL_MODAL.php#mdlModificar",
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
    <?php require_once './GF_CONFIGURACION_PREDIAL_MODAL.php'; ?>
</body>
</html>



