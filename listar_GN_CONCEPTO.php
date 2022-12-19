<?php
#01/06/2017 --- Nestor B --- se modifico  la consulta que trae la informacion.
require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';

  $sql = "SELECT        c.id_unico,
                        c.codigo,
                        c.descripcion,
                        c.tipofondo,
                        tf.id_unico,
                        tf.nombre,
                        c.compania,
                        t.id_unico,
                        t.razonsocial,
                        c.unidadmedida,
                        um.id_unico,
                        um.nombre,
                        c.clase,
                        cl.id_unico,
                        cl.nombre,
                        c.codigocgr,
                        cc.id_unico,
                        cc.nombre,
                        c.tipoentidadcredito,
                        ter.id_unico,
                        ter.razonsocial,
                        c.conceptorel,
                        cr.id_unico,
                        cr.codigo,
                        c.codigodian,
                        cd.id_unico,
                        cd.nombre,
                        CONCAT(cr.codigo,' (',cr.descripcion,')'),
                         c.tipo_interfaz,
                         c.acum_ibc,
                         c.aplica_liquidacion_final,
                         c.ibr, 
                         c.liquida_retroactivo,
                         c.equivalente_NE,
                         tnn.nombre,
                         c.equivalante_sui,
                         c.equivalente_personal_cos 
                FROM gn_concepto c	 
                LEFT JOIN 	gn_tipo_afiliacion tf            ON c.tipofondo          = tf.id_unico
                LEFT JOIN 	gn_unidad_medida_con um     ON c.unidadmedida       = um.id_unico
                LEFT JOIN 	gn_clase_concepto cl        ON c.clase              = cl.id_unico
                LEFT JOIN  	gn_codigo_cgr cc            ON c.codigocgr          = cc.id_unico
                LEFT JOIN       gf_tercero ter              ON c.tipoentidadcredito = ter.id_unico
                LEFT JOIN       gf_tercero t                ON c.compania            = t.id_unico
                LEFT JOIN 	gn_concepto cr              ON c.conceptorel        = cr.id_unico
                LEFT JOIN  	gn_codigo_dian cd           ON c.codigodian         = cd.id_unico
                LEFT JOIN gn_tipo_novedad_nomina tnn  ON tnn.id_unico=c.tipo_novedad_nomina";
  
    $resultado = $mysqli->query($sql);
?>
    <title>Listar Concepto</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Concepto</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>              
                                        <td class="cabeza"><strong>Código</strong></td>
                                        <td class="cabeza"><strong>Descripción</strong></td>
                                        <td class="cabeza"><strong>Tipo Afiliación</strong></td>
                                        <td class="cabeza"><strong>Unidad Medida</strong></td>
                                        <td class="cabeza"><strong>Clase Concepto</strong></td>
                                        <td class="cabeza"><strong>Código CGR</strong></td>
                                        <td class="cabeza"><strong>Entidad Financiera</strong></td>
                                        <td class="cabeza"><strong>Código DIAN</strong></td>
                                        <td class="cabeza"><strong>Concepto Relacionado</strong></td>
                                        <td class="cabeza"><strong>Tipo Interfáz</strong></td>
                                        <td class="cabeza"><strong>Acumulable IBC</strong></td>
                                        <td class="cabeza"><strong>Aplica Liquidación Final</strong></td>
                                        <td class="cabeza"><strong>Acumulable IBR</strong></td>
                                        <td class="cabeza"><strong>Liquida Retroactivo</strong></td>
                                        <td class="cabeza"><strong>Equivalente Nómina Electrónica</strong></td>
                                        <td class="cabeza"><strong>Tipo Novedad Nómina Electrónica</strong></td>
                                        <td class="cabeza"><strong>Equivalente SUI</strong></td>
                                         <td class="cabeza"><strong>Equivalente Personal Costos</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>            
                                        <th class="cabeza">Código</th>
                                        <th class="cabeza">Descripción</th>
                                        <th class="cabeza">Tipo Afiliación</th>
                                        <th class="cabeza">Unidad Medida</th>
                                        <th class="cabeza">Clase Concepto</th>
                                        <th class="cabeza">Código CGR</th>
                                        <th class="cabeza">Entidad Financiera</th>
                                        <th class="cabeza">Código DIAN</th>
                                        <th class="cabeza">Concepto Relacionado</th>
                                        <th class="cabeza">Tipo Interfáz</th>
                                        <th class="cabeza">Acumulable IBC</th>
                                        <th class="cabeza">Aplica Liquidación Final</th>
                                        <th class="cabeza">Acumulable IBR</th>
                                        <th class="cabeza">Liquida Retroactivo</th>
                                        <th class="cabeza">Equivalente Nómina Electrónica</th>
                                        <th class="cabeza">Tipo Novedad Nómina Electrónica</th>
                                        <th class="cabeza">Equivalente SUI</th>
                                         <th class="cabeza">Equivalente Personal Costos</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        $cid      = $row[0];
                                        $ccod     = $row[1];
                                        $cdesc    = $row[2];
                                        $ctip     = $row[3];
                                        $tfid     = $row[4];
                                        $tfnom    = $row[5];
                                        $cter     = $row[6];
                                        $tid1     = $row[7];
                                        $ter1     = $row[8];
                                        $cum      = $row[9];
                                        $umid     = $row[10];
                                        $umnom    = $row[11];
                                        $ccla     = $row[12];
                                        $clid     = $row[13];
                                        $clnom    = $row[14];
                                        $ccodc    = $row[15];
                                        $codcid   = $row[16];
                                        $codcnom  = $row[17];
                                        $cte      = $row[18];
                                        $tid2     = $row[19];        
                                        $ter2     = $row[20];
                                        $ccr      = $row[21];
                                        $crid     = $row[22];
                                        $crcod    = $row[23];
                                        $ccodd    = $row[24];
                                        $coddid   = $row[25];
                                        $coddnom  = $row[26];
                                        $pred     = $row[27];
                                        $nominaE  = $row[33];
                                        $tipoNE   = $row[34];
                                        $equivalenteSUI  = $row[35];
                                        $equivalentePer  = $row[36];
                                        ?>
                                    <tr>
                                        <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos">
                                            <a class="campos" href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a class="campos" href="modificar_GN_CONCEPTO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                                     
                                        <td class="campos"><?php echo $ccod?></td>                
                                        <td class="campos"><?php echo $cdesc?></td>                
                                        <td class="campos"><?php echo $tfnom?></td>                
                                        <td class="campos"><?php echo $umnom?></td>                
                                        <td class="campos"><?php echo $clnom?></td>                
                                        <td class="campos"><?php echo $codcnom?></td>                
                                        <td class="campos"><?php echo $ter2?></td>                
                                        <td class="campos"><?php echo $coddnom?></td>                
                                        <td class="campos"><?php echo $pred?></td> 
                                        <td class="campos"><?php if($row[28]==1){echo 'Detallada';} elseif($row[28]==2) {echo 'Acumulada';}?></td> 
                                        <td class="campos"><?php if($row[29]==1){echo 'Si';} else {echo 'No';}?></td> 
                                        <td class="campos"><?php if($row[30]==1){echo 'Si';} else {echo 'No';}?></td>                
                                        <td class="campos"><?php if($row[31]==1){echo 'Si';} else {echo 'No';}?></td>                
                                        <td class="campos"><?php if($row[32]==1){echo 'Si';} else {echo 'No';}?></td>  
                                        <td class="campos"><?php echo $nominaE?></td>   
                                        <td class="campos"><?php echo $tipoNE?></td>
                                        <td class="campos"><?php echo $equivalenteSUI?></td>  
                                        <td class="campos"><?php echo $equivalentePer?></td>                          
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_CONCEPTO.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado de Concepto?</p>
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
                  url:"json/eliminarConceptoJson.php?id="+id,
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
        document.location = 'listar_GN_CONCEPTO.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_CONCEPTO.php';
      });    
  </script>
    </body>
</html>