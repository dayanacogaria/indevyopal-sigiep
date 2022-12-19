<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once ('Conexion/conexion.php');
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$sql = "
SELECT 
    T.id_unico,
    TI.id_unico,
    TI.nombre,
    T.numeroidentificacion,
    T.nombreuno,
    T.nombredos,
    T.apellidouno,
    T.apellidodos,
    TR.id_unico,
    TR.nombre, 
    T.email 
FROM gf_tercero T
LEFT JOIN gf_tipo_identificacion TI ON T.tipoidentificacion = TI.id_unico
LEFT JOIN gf_tipo_regimen TR ON T.tiporegimen = TR.id_unico
LEFT JOIN gf_perfil_tercero PT ON T.id_unico = PT.tercero
WHERE PT.perfil = 5 AND T.compania = $compania";    
$listar = $mysqli->query($sql);
?>      
<title>Listar Proveedor Natural</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px;">
                <h2 class="titulolista" align="center">
                    Proveedor Natural
                </h2>
                <div class="table-responsive contTabla">
                    <div class="table-responsive contTabla">
                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                    <td class="cabeza"><strong>Número Identificación</strong></td>
                                    <td class="cabeza"><strong>Primer Nombre</strong></td>
                                    <td class="cabeza"><strong>Segundo Nombre</strong></td>
                                    <td class="cabeza"><strong>Primer Apellido</strong></td>
                                    <td class="cabeza"><strong>Segundo Apellido </strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                    <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Identificación</th>
                                    <th>Número Identificación</th>
                                    <th>Primer Nombre</th>
                                    <th>Segundo Nombre</th>
                                    <th>Primer Apellido</th>
                                    <th>Segundo Apellido</th>
                                    <th>Tipo Régimen</th>
                                    <th>Corrreo Electrónico</th> 
                                </tr>                                    
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($listar)) { ?>
                                <tr>
                                    <td class="oculto"><?php echo $row[0]; ?></td>
                                    <td class="campos">
                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0]; ?>);">
                                            <li title="Eliminar" class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="EDITAR_TERCERO_PROVEEDOR_NATURAL_2.php?id=<?php echo md5($row[0]); ?>">
                                            <li title="Modificar" class="glyphicon glyphicon-edit"></li>
                                        </a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[2])); ?></td>
                                    <td class="campos"><?php echo ucwords(($row[3])); ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[4])); ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[5])); ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[6])); ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[7])); ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[9])); ?></td>
                                    <td class="campos"><?php echo $row[10]; ?></td>
                                </tr> 
                                <?php } ?>                                    
                            </tbody>
                        </table>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="left">
                                <button onclick="javascript:abrirMTerceroMenu()" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:4px;">Buscar Terceros</button>
                            </div> 
                        </div>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="right">
                                <a href="TerceroProveedorNatural2.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
                            </div> 
                        </div> 
                    </div>                        
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
                    <p>¿Desea eliminar el registro seleccionado de Proveedor Natural?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" onclick="cerrar()" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal3" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Se eliminó solo el perfil ya que el tercero tiene movimientos</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminar(id){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:5, id:id}
                $.ajax({
                  type:"POST",
                  url:"jsonPptal/gf_tercerosJson.php",
                  data: form_data,
                  success: function (data) {
                        jsRemoveWindowLoad();
                        console.log(data);
                        result = JSON.parse(data);
                        if(result==1){
                            $("#myModal1").modal('show');
                        } else if(result==2){ 
                            $("#myModal3").modal('show');
                        } else {
                             $("#myModal2").modal('show');
                        }
                  }
                });
            });
        }
    </script>
    <script type="text/javascript">
        function cerrar() {
            document.location.reload();
        }
    </script>
</body>
</html>

