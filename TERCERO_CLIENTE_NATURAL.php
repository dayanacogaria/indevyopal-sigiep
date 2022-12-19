<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#16/08/2018 |Erica G. | Nombre Comercial
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$queryTercClieNat ="SELECT t.Id_Unico, ti.Nombre, t.NumeroIdentificacion, 
    t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, tr.Nombre, 
    t.email, t.nombre_comercial  
  FROM gf_tercero t 
  LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
  LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
  LEFT JOIN gf_perfil_tercero pt   ON t.Id_Unico = pt.Tercero  
  WHERE pt.Perfil = 3 AND t.compania = $compania";
$resultado = $mysqli->query($queryTercClieNat);
?>
<title>Listar Cliente Natural</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 align="center" class="titulolista">Cliente Natural</h2>
                <div class="table-responsive contTabla" >
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                    <td class="cabeza"><strong>Número Identificación</strong></td>
                                    <td class="cabeza"><strong>Primer Nombre</strong></td>
                                    <td class="cabeza"><strong>Segundo Nombre</strong></td>
                                    <td class="cabeza"><strong>Primer Apellido</strong></td>
                                    <td class="cabeza"><strong>Segundo Apellido</strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                    <td class="cabeza"><strong>Nombre Comercial</strong></td>
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
                                    <th>Nombre Comercial</th> 
                                    <th>Corrreo Electrónico</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                <tr>
                                    <td class="oculto"><?php echo $row[0] ?></td>
                                    <td class="campos">
                                        <a class href="#" onclick="javascript:eliminarClieNatu(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        <a href="modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat=<?php echo md5($row[0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[1])) ?></td>
                                    <td class="campos"><?php echo $row[2] ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[3])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[4])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[5])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[6])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[7])) ?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[9])) ?></td>
                                    <td class="campos"><?php echo $row[8]?></td>
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
                                <a href="registrar_TERCERO_CLIENTE_NATURAL.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Cliente Natural?</p>
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
                    <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" onclick="cerrar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarClieNatu(id)  {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:3, id:id}
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
        function cerrar(){
            document.location.reload();
        }
    </script>

</body>
</html>

