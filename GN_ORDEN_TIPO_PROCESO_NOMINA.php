<?php

require_once('Conexion/conexion.php'); 
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
$tipo = $_REQUEST['id'];
#**** Conceptos ****#
$conc = $con->Listar("SELECT DISTINCT c.id_unico, cl.nombre, c.codigo, c.descripcion, c.orden FROM gn_novedad n 
LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
LEFT JOIN gn_clase_concepto cl ON c.clase = cl.id_unico 
WHERE md5(p.tipoprocesonomina) = '$tipo' AND p.parametrizacionanno = $anno 
ORDER BY c.id_unico");

?>
<html>
    <head>
    <title>Configuración Orden Sábana</title>
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Configuración Orden Sábana</h2>    
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Clase</strong></td>
                                    <td><strong>Concepto</strong></td>
                                    <td><strong>Orden</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Clase</th>
                                    <th>Concepto</th>
                                    <th>Orden</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php 
                                    for ($i=0; $i < count($conc); $i++) { 
                                        echo '<tr>
                                            <td style="display: none;">'.$conc[$i][0].'</td>
                                            <td width="7%"></td>
                                            <td>'.utf8_encode($conc[$i][1]).'</td>
                                            <td>'.$conc[$i][2].' - '.utf8_encode($conc[$i][3]).'</td>
                                            <td><input type="text" id="orden'.$conc[$i][0].'" onchange="javascript:guardar( '.$conc[$i][0].')" value="'.$conc[$i][4].'"></td>
                                        </tr>';
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
        function guardar( id){
            let val = $("#orden"+id).val();
            console.log('Orden: '+val);
            $.ajax({
                type:"GET",
                url:"jsonNomina/gn_consultasJson.php?action=3&val="+val+"&id="+id,
                success: function (data) {
                    result = JSON.parse(data);
                    console.log(data);
                }

            });
        }
    </script>
</body>
</html>



