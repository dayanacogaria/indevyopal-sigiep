<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#21/09/2017 |Erica G. | Agregar Campo Tipo Compañia (1-Pública, 2- Privada)
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$queryTerceroComp = "SELECT 
    t.id_unico , 
    t.razonsocial , 
    t.numeroidentificacion , 
    t.digitoverficacion , 
    ti.nombre, 
    s.nombre, 
    t.representantelegal, 
    ci.nombre, 
    tr.nombre, 
    t.contacto , 
    te.nombre, 
    tipen.nombre, 
    t.codigo_dane , 
    t.tipo_compania, 
    IF(CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos) = '',
     (trl.razonsocial),
     CONCAT_WS(' ',
     trl.nombreuno,
     trl.nombredos,
     trl.apellidouno,
     trl.apellidodos)) AS repLeg, 
     IF(CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos) = '',
     (trc.razonsocial),
     CONCAT_WS(' ',
     trc.nombreuno,
     trc.nombredos,
     trc.apellidouno,
     trc.apellidodos)) AS contac, 
     t.email , d.nombre, 
     t.distribucion_costos 
  FROM gf_tercero t
  LEFT JOIN gf_tipo_identificacion ti on t.tipoidentificacion = ti.id_unico
  LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
  LEFT JOIN gf_tipo_regimen tr ON t.tiporegimen = tr.id_unico
  LEFT JOIN gf_tipo_empresa te ON t.tipoempresa = te.id_unico
  LEFT JOIN gf_tipo_entidad tipen ON t.tipoentidad = tipen.id_unico
  LEFT JOIN gf_ciudad ci ON t.ciudadidentificacion = ci.id_unico
  LEFT JOIN gf_tercero trl ON trl.id_unico = t.representantelegal 
  LEFT JOIN gf_tercero trc ON trc.id_unico = t.contacto 
  LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
  LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico 
  LEFT JOIN gf_departamento d ON ci.departamento = d.id_unico 
  WHERE p.id_unico = 1 AND t.compania = $compania";
$resultado = $mysqli->query($queryTerceroComp);
?>
<title>Listar Compañía</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 class="titulolista" align="center" >Compañía</h2>
                <div class="table-responsive" >
                    <div class="table-responsive" >
                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"</td>
                                    <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                    <td class="cabeza"><strong>Número Identificación</strong></td>
                                    <td class="cabeza"><strong>Sucursal</strong></td>
                                    <td class="cabeza"><strong>Razón Social</strong></td>
                                    <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                    <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                    <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                    <td class="cabeza"><strong>Representante Legal</strong></td>
                                    <td class="cabeza"><strong>Ciudad Identificación</strong></td>
                                    <td class="cabeza"><strong>Contacto</strong></td>
                                    <td class="cabeza"><strong>Código DANE</strong></td>
                                    <td class="cabeza"><strong>Tipo Compañia</strong></td>
                                    <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
                                    <td class="cabeza"><strong>Distribución de costos</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo Identificación</th>
                                    <th>Número Identificación</th>
                                    <th>Sucursal</th>
                                    <th>Razón Social</th>
                                    <th>Tipo Régimen</th>
                                    <th>Tipo Empresa</th>
                                    <th>Tipo Entidad</th>
                                    <th>Representante Legal</th>
                                    <th>Ciudad Identificación</th>
                                    <th>Contacto</th>               
                                    <th>Código DANE</th>               
                                    <th>Tipo Compañia</th>    
                                    <th>Corrreo Electrónico</th> 
                                    <th>Distribución de costos</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td class="oculto"><?php echo $row[0] ?></td>
                                        <td class="campos">
                                            <a href="#" onclick="javascript:eliminarTerComp(<?php echo $row[0]; ?>);"><li title="Eliminar" class="glyphicon glyphicon-trash"></li>
                                            </a>
                                            <a href="modificar_TERCERO_COMPANIA.php?id_ter_comp=<?php echo md5($row[0]); ?>"><li title="Modificar" class="glyphicon glyphicon-edit" ></li></a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[4])); ?></td>
                                        <td class="campos"><?php
                                            echo $row[2] . ' - ' . $row[3];
                                            ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[5])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[1])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[8])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[10])); ?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[11])); ?></td>
                                        <td class="campos">
                                            <?php if (!empty($row[6])) {
                                                echo ucwords(mb_strtolower($row[14]));
                                            } ?>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[7].' - '.$row[17])) ?></td>
                                        <td class="campos">
                                            <?php if (!empty($row[9])) {
                                                echo ucwords(mb_strtolower($row[15]));
                                            } ?>
                                        </td>
                                        <td class="campos">
                                            <?php if (!empty($row[12])) {
                                                echo mb_strtoupper($row[12]);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($row[13])) {
                                                if ($row[13] == '2') {
                                                    echo 'Privada';
                                                } else {
                                                    echo 'Pública';
                                                }
                                            } else {
                                                echo 'Pública';
                                            }?>
                                        </td>    
                                        <td class="campos"><?php echo $row[16]; ?></td>
                                        <td class="campos"><?php 
                                        if($row[18]==1){
                                            echo 'Sí'; 
                                        } else {echo 'No';};
                                        ?></td>
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
                                <a href="registrar_TERCERO_COMPANIA.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
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
                    <p>¿Desea eliminar el registro seleccionado de Compañia?</p>
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
                    <button type="button" onclick="cerrar()"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
        function eliminarTerComp(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:1, id:id}
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

