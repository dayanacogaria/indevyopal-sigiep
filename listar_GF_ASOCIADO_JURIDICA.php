<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#24/07/2018 |Erica G. | Correo Electrónico 
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$compania = $_SESSION['compania'];
$queryTerceroComp ="SELECT 
  t.Id_Unico , 
  t.RazonSocial , 
  t.NumeroIdentificacion , 
  t.DigitoVerficacion , 
  ti.Nombre, 
  s.nombre, 
  t.RepresentanteLegal, 
  ci.Nombre, 
  tr.Nombre, 
  t.Contacto , 
  te.Nombre, 
  tipen.Nombre, 
  z.Nombre, 
  t.email, 
  d.nombre 
  FROM gf_tercero t
  LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
  LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
  LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
  LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
  LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
  LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico
  LEFT JOIN gf_perfil_tercero pt ON t.Id_Unico = pt.Tercero 
  LEFT JOIN gf_departamento d ON ci.departamento = d.id_unico 
  LEFT JOIN  gf_zona z ON t.Zona = z.Id_Unico
  WHERE pt.Perfil = 8 AND t.compania = $compania";
$resultado = $mysqli->query($queryTerceroComp);
?>
<title>Listar Asociado Jurídica</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top:-20px">
                <h2 class="titulolista" align="center">
                    Asociado Jurídica
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
                                  <td class="cabeza"><strong>Sucursal</strong></td>
                                  <td class="cabeza"><strong>Razón Social</strong></td>
                                  <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                  <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                  <td class="cabeza"><strong>Representante Legal</strong></td>
                                  <td class="cabeza"><strong>Ciudad Identificación</strong></td>
                                  <td class="cabeza"><strong>Contacto</strong></td>
                                  <td class="cabeza"><strong>Zona</strong></td>
                                  <td class="cabeza"><strong>Corrreo Electrónico</strong></td>
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
                                  <th>Representante Legal</th>
                                  <th>Ciudad Identificación</th>
                                  <th>Contacto</th>
                                  <th>Zona</th> 
                                  <th>Corrreo Electrónico</th> 
                                </tr>
                            </thead>
                            <tbody>
                            <?php while($row = mysqli_fetch_row($resultado)){ ?>
                                <tr>
                                    <td class="oculto"><?php echo $row[0]?></td>
                                    <td class="campos">
                                        <a  href="#" onclick="javascript:eliminarAsociadoJur(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="modificar_GF_ASOCIADO_JURIDICA.php?id_asociadoJur=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[4]));?></td>
                                    <td class="campos"><?php echo ($row[2].' - '.$row[3])?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[5]))?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[1]))?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[8]))?></td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[10]))?></td>
                                    <td class="campos">
                                        <?php
                                        if (empty($row[6])) {
                                          echo "";
                                        }else{
                                            $queryRepLeg = "SELECT ter.Id_Unico, ter.NombreUno, ter.NombreDos, 
                                                 ter.ApellidoUno, ter.ApellidoDos, 
                                                 ter.numeroidentificacion 
                                                 FROM gf_tercero ter WHERE ter.Id_Unico = $row[6]"; 

                                            $repreLegal = $mysqli->query($queryRepLeg);  
                                            $rowRL = mysqli_fetch_row($repreLegal);
                                            echo ucwords(mb_strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4])).' - '.$rowRL[5];
                                        }

                                        ?>

                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[7].' - '.$row[14]))?></td>
                                    <td class="campos">
                                      <?php
                                      if (!empty($row[9])) {
                                        $queryRepLeg = "SELECT ter.Id_Unico, ter.NombreUno, ter.NombreDos, 
                                                 ter.ApellidoUno, ter.ApellidoDos, 
                                                 ter.numeroidentificacion 
                                                 FROM gf_tercero ter WHERE ter.Id_Unico = $row[9]"; 

                                            $repreLegal = $mysqli->query($queryRepLeg);  
                                            $rowRL = mysqli_fetch_row($repreLegal);
                                            echo ucwords(mb_strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4])).' - '.$rowRL[5];
                                      } ?>
                                    </td>
                                    <td class="campos"><?php echo ucwords(mb_strtolower($row[12]))?></td>  
                                    <td class="campos"><?php echo  ($row[13])?></td>  
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
                                <a href="registrar_GF_ASOCIADO_JURIDICA.php" class="btn btn-primary btnNuevoLista" Style="box-shadow: 0px 2px 5px 1px gray;color: #fff;border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px;">Registrar Nuevo</a>
                            </div> 
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Divs de clase Modal para las ventanillas de eliminar. -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
              <p>¿Desea eliminar el registro seleccionado de Asociado Jurídica?</p>
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
<!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
      function eliminarAsociadoJur(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              jsShowWindowLoad('Eliminando Información...');
              var form_data = {action:3, perfil:8, id:id}
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