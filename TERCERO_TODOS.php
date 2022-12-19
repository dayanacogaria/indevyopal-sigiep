<?php 
##############MODIFICACIONES #########################
#23/03/2017 | ERICA G. | MODIFICACION CONSULTA

#Historial de Actualizaciones  (ctl+F con la fecha para acceso rápido)
#     08/02/2017 - Daniel N: Creación del archivo, trae todos los campos de gf_tercero pero visualiza los del informe Listado.
 
require_once './head_listar.php';
require_once ('./Conexion/conexion.php');

$compania = $_SESSION['compania'];
  $sql = "SELECT IF
  (CONCAT_WS(
      ' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) IS NULL 
   OR CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) = '',
    (tr.razonsocial),
    CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos)) AS NOMBRE,
  tr.id_unico,
  tr.numeroidentificacion,
  tr.digitoverficacion,
  ti.nombre, 
  CONCAT_WS(' ', trc.nombreuno, trc.nombredos, trc.apellidouno, trc.apellidodos) as contactos , 
  CONCAT_WS(' ', trr.nombreuno, trr.nombredos, trr.apellidouno, trr.apellidodos) as representante, 
  tr.cargo, 
  te.nombre, 
  tre.nombre,
  ten.nombre,
  z.nombre, 
  p.id_unico, 
  p.nombre 
FROM
  gf_tercero tr
LEFT JOIN
  gf_tipo_identificacion ti ON ti.id_unico = tr.tipoidentificacion
LEFT JOIN 
  gf_tercero trc ON trc.id_unico = tr.contacto
LEFT JOIN 
  gf_tercero trr ON trr.id_unico = tr.representantelegal
LEFT JOIN 
  gf_tipo_empresa te ON te.id_unico = tr.tipoempresa 
LEFT JOIN 
  gf_tipo_entidad ten ON tr.tipoentidad = ten.id_unico 
LEFT JOIN 
  gf_tipo_regimen tre ON tr.tiporegimen = tre.id_unico 
LEFT JOIN 
  gf_zona z ON tr.zona = z.id_unico 
LEFT JOIN 
  gf_perfil_tercero pt ON pt.tercero = tr.id_unico 
LEFT JOIN 
  gf_perfil p ON pt.perfil = p.id_unico 
WHERE tr.compania =  $compania"      ;
    $resultado = $mysqli->query($sql);

?>
    <title>Listado General de Terceros</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Terceros</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="10%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Perfil</strong></td>
                                        <td class="cabeza"><strong>Tipo Identificación</strong></td>
                                        <td class="cabeza"><strong>Identificación</strong></td>
                                        <td class="cabeza"><strong>Razón Social / Nombre</strong></td>
                                        <td class="cabeza"><strong>Contacto</strong></td>
                                        <td class="cabeza"><strong>Representante Legal</strong></td>
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Tipo Empresa</strong></td>
                                        <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                        <td class="cabeza"><strong>Tipo Régimen</strong></td>
                                        <td class="cabeza"><strong>Zona</strong></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="10%"></th>                                        
                                        <th class="cabeza">Perfil</th>
                                        <th class="cabeza">Tipo Identificación</th>
                                        <th class="cabeza">Identificación</th>
                                        <th class="cabeza">Razón Social / Nombre</th>
                                        <th class="cabeza">Contacto</th>
                                        <th class="cabeza">Representante Legal</th>
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Tipo Empresa</th>
                                        <th class="cabeza">Tipo Entidad</th>
                                        <th class="cabeza">Tipo Régimen</th>
                                        <th class="cabeza">Zona</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) 
                                    {
                                    #Igualación de perfil tercero a variable
                                    $pert = $row[12];
                                    $arg = 0;
                                    #Selección de formulario 
                                    switch ($row[12])
                                    {
                                        case 1:
                                            $arg = 'modificar_TERCERO_COMPANIA.php?id_ter_comp=';
                                        break;
                                        case 2:
                                            $arg = 'EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=';
                                        break;
                                        case 3:
                                            $arg = 'modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat=';
                                        break;
                                        case 4:
                                            $arg = 'modificar_TERCERO_CLIENTE_JURIDICA.php?id_ter_clie_jur=';
                                        break;
                                        case 5:
                                            $arg = 'EDITAR_TERCERO_PROVEEDOR_NATURAL_2.php?id=';
                                        break;
                                        case 6:
                                            $arg = 'EDITAR_TERCERO_PROVEEDOR_JURIDICA_2.php?id=';
                                        break;
                                        case 7:
                                            $arg = 'modificar_GF_ASOCIADO_NATURAL.php?id_asoNat=';
                                        break;
                                        case 8:
                                            $arg = 'modificar_GF_ASOCIADO_JURIDICA.php?id_asociadoJur=';
                                        break;
                                        case 9:
                                            $arg = 'modificar_GF_BANCO_JURIDICA.php?id_bancoJur=';
                                        break;
                                        case 10:
                                            $arg = 'modificar_TERCERO_CONTACTO_NATURAL.php?id_ter_cont_nat=';
                                        break;
                                        case 11:
                                            $arg = 'modificar_GF_TERCERO_ENTIDAD_AFILIACION.php?id=';
                                        break;
                                        case 12:
                                            $arg = 'modificar_GF_TERCERO_ENTIDAD_FINANCIERA.php?id=';
                                        break;
                                    }
                                    $var = "Modificar".$row[12];
                                    ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[1]?></td>
                                        <td>      
                                             <?php if($row[2]=='900849655' || $row[2]=='9999999999'){} else {?>
                                            <a class="campos" href="<?php echo $arg.md5($row[1])?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                             <?php } ?>
                                        </td>                                        
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[13]));?></td>                
                                        <td class="campos"><?php echo $row[4]?></td>                
                                        <td class="campos"><?php if(!empty($row[3])){ echo $row[2].' - '.$row[3]; }
                                                    else { echo $row[2]; }?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[0]));?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[5]));?></td>                
                                        <td class="campos"><?php ucwords(mb_strtolower($row[6]));?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[7]))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[8]))?></td> 
                                        
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[10]))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[9]))?></td>                
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[11]))?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>                            
                            <div align="right">
                                <a href="#"onclick="return abrirMTerceroMenu()" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Ver Terceros por Perfil</a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tercero?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTerceroTodosJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'TERCERO_TODOS.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'TERCERO_TODOS.php';
      });    
  </script>
    </body>
</html>