<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#25/07/2018 |Erica G. | Correo Electrónico - Arreglar Código
#24/08/2017 |Erica G. | Tarjeta Profesional
#######################################################################################################
require_once ('Conexion/conexion.php');
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$sql = "
SELECT
    T.Id_Unico ID_T ,
    T.NumeroIdentificacion NUMIDENT_T ,
    T.NombreUno NOMU_T ,
    T.NombreDos NOMD_T ,
    T.ApellidoUno APEU_T ,
    T.ApellidoDos APED_T , 
    TI.Id_Unico ID_TI ,
    TI.Nombre TIPO ,
    REG.Id_Unico ID_REG ,
    REG.Nombre NOM_REG ,
    Z.Id_Unico ID_Z ,
    Z.Nombre NOM_Z , 
    T.tarjeta_profesional TPROF , 
    T.email as email 
FROM gf_tercero T 
LEFT JOIN gf_tipo_identificacion TI ON T.TipoIdentificacion = TI.Id_Unico 
LEFT JOIN gf_tipo_regimen REG ON T.TipoRegimen = REG.Id_Unico 
LEFT JOIN gf_zona Z ON T.Zona = Z.Id_Unico
LEFT JOIN gf_perfil_tercero PT ON PT.tercero = T.id_unico
WHERE PT.perfil = 2 AND T.compania = $compania";
$terceros = $mysqli->query($sql);
 ?>
<title>Listar Empleado Natural</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px;">
 	        <h2 align="center" class="titulolista">
                    Empleado Natural
 		</h2>
        	<div class="table-responsive contTabla">
                    <div class="table-responsive contTabla">
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
                                    <td class="cabeza"><strong>Zona</strong></td>
                                    <td class="cabeza"><strong>Tarjeta Profesional</strong></td>
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
                                    <th>Zona</th>
                                    <th>Tarjeta Profesional</th>
                                    <th>Corrreo Electrónico</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    while ( $row = mysqli_fetch_assoc($terceros)) { ?>
                                    <tr>
                                        <td class="oculto"><?php echo $row[0]?></td>
                                        <td class="campos">
                                            <a href="#" onclick="javascript:eliminarContrato(<?php echo $row['ID_T'];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=<?php echo md5($row['ID_T']);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['TIPO']));?></td>
                                        <td class="campos"><?php echo $row['NUMIDENT_T'];?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMU_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMD_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['APEU_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['APED_T']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOM_REG']));?></td>
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row['NOM_Z']));?></td>
                                        <td class="campos"><?php echo $row['TPROF'];?></td>
                                        <td class="campos"><?php echo $row['email'];?></td>
                                    </tr>
                                <?php  }?>
                            </tbody>
                        </table>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="left">
                                <button onclick="javascript:abrirMTerceroMenu()" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:4px;">Buscar Terceros</button>
                            </div> 
                        </div>
                        <div class="form-group form-inline col-sm-6" style="">
                            <div align="right">
                                <a href="TerceroEmpleadoNatural2.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
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
              <p>¿Desea eliminar el registro seleccionado de Tercero Empleado Natural?</p>
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
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminarContrato(id) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                jsShowWindowLoad('Eliminando Información...');
                var form_data = {action:3, perfil:2, id:id}
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