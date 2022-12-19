<?php

require_once './Conexion/conexion.php';
require_once './head_listar.php';
session_start();
$anno = $_SESSION['anno'];
 $sql = "SELECT         pl.id_unico,  pl.vigencia, pl.salmin, pl.auxt,
                        pl.primaA, pl.primaM, pl.asaludemple,
                        pl.asaludempre,pl.apensionemple,pl.apensionempre,
                        pl.fodosol,pl.excentoret,pl.acajacomp,
                        pl.asena,pl.aicbf,pl.aesap,pl.aministerio,
                        pl.valoruvt,pl.talimentacion,pl.talimendoc,
                        pa.id_unico, pa.anno,pl.porce_inca,
                        pl.excento,pl.rec_noc,pl.rec_dom,
                        pl.hext_do,pl.hext_ddf,pl.hext_no,
                        pl.hext_ndf,pl.redondeo,pl.saludsena,
                        gg.nombre,tp.nombre , te.id_unico, te.nombre , 
                        pl.hora_extra_no, pl.tope_aux_transporte, pl.dias_primav,pl.dias_bon_recreacion,
                        pl.limite_bon_servicios                        
                FROM gn_parametros_liquidacion pl
                LEFT JOIN gf_parametrizacion_anno pa ON pl.vigencia = pa.id_unico
                LEFT JOIN gn_grupo_gestion gg        ON pl.grupo_gestion = gg.id_unico
                LEFT JOIN gn_tipo_provision tp       ON pl.tipo_provision = tp.id_unico
                LEFT JOIN gn_tipo_empleado te ON pl.tipo_empleado = te.id_unico 
                WHERE pl.vigencia = $anno ";

    $resultado = $mysqli->query($sql);


    
?>
     <title>Listar Parámetros Liquidación</title>
    </head>
     <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Parámetros Liquidación</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>              
                                        <td class="cabeza"><strong>Tipo Empleado</strong></td>
                                        <td class="cabeza"><strong>Tipo Provisión</strong></td>
                                        <td class="cabeza"><strong>Salario Mínimo</strong></td>
                                        <td class="cabeza"><strong>Auxilio de Transporte</strong></td>
                                        <td class="cabeza"><strong>Prima Alimentación</strong></td>
                                        <td class="cabeza"><strong>Prima Movilización</strong></td>
                                        <td class="cabeza"><strong>Aporte Salud <br> Empleado (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte Salud <br> Empresa (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte Pesión <br> Empleado (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte Pensión <br> Empresa (%)</strong></td>
                                        <td class="cabeza"><strong>Fondo <br> Solidaridad (%)</strong></td>
                                        <td class="cabeza"><strong>Exento <br> Retención</strong></td>
                                        <td class="cabeza"><strong>Aporte Caja <br> Compensación(%)</strong></td>
                                        <td class="cabeza"><strong>Aporte SENA (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte ICBF (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte ESAP (%)</strong></td>
                                        <td class="cabeza"><strong>Aporte <br> Ministerio (%)</strong></td>
                                        <td class="cabeza"><strong>Valor UVT</strong></td>
                                        <td class="cabeza"><strong>Tope <br>Alimentación</strong></td>
                                        <td class="cabeza"><strong>Tope <br> Alimentación Docente</strong></td>
                                        <td class="cabeza"><strong>Tope <br> Auxilio Transporte</strong></td>
                                        <td class="cabeza"><strong>Incapacidad <br>EPS (%)</strong></td>
                                        <td class="cabeza"><strong>Recargo Noc (%)</strong></td>
                                        <td class="cabeza"><strong>Recargo D.F. (%)</strong></td>
                                        <td class="cabeza"><strong>Horas Extras D.O (%)</strong></td>
                                        <td class="cabeza"><strong>Horas Extras D.D.F (%)</strong></td>
                                        <td class="cabeza"><strong>Horas Extras N.O (%)</strong></td>
                                        <td class="cabeza"><strong>Horas Extras N.D.F (%)</strong></td>
                                        <td class="cabeza"><strong>Horas Extras <br/>Noct. Ordinarias (%)</strong></td>
                                        <td class="cabeza"><strong>Redondeo</strong></td>
                                        <td class="cabeza"><strong>Salud Sena</strong></td>
                                        <td class="cabeza"><strong>Exento<br>Parafiscales</strong></td>
                                        <td class="cabeza"><strong>Días Prima<br>Vacaciones</strong></td>
                                        <td class="cabeza"><strong>Días Bonificación<br>Recreación</strong></td>
                                        <td class="cabeza"><strong>Limite Bonificación<br>Servicios</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>            
                                        <th class="cabeza">Tipo Empleado</th>
                                        <th class="cabeza">Tipo Provisión</th>
                                        <th class="cabeza">Salario Mínimo</th>
                                        <th class="cabeza">Auxilio Transporte</th>
                                        <th class="cabeza">Prima Alimentación</th>
                                        <th class="cabeza">Prima Movilización</th>
                                        <th class="cabeza">Aporte Salud <br> Empleado (%)</th>
                                        <th class="cabeza">Aporte Salud <br> Empresa (%)</th>
                                        <th class="cabeza">Aporte Pensión <br> Empleado (%)</th>
                                        <th class="cabeza">Aporte Pensión <br> Empresa (%)</th>
                                        <th class="cabeza">Fondo <br> Solidaridad (%)</th>
                                        <th class="cabeza">Exento<br>Retención (%)</th>
                                        <th class="cabeza">Aporte Caja <br> Compensación (%)</th>
                                        <th class="cabeza">Aporte SENA (%)</th>
                                        <th class="cabeza">Aporte ICBF (%)</th>
                                        <th class="cabeza">Aporte ESAP (%)</th>
                                        <th class="cabeza">Aporte <br> Ministerio (%)</th>
                                        <th class="cabeza">Valor UVT</th>
                                        <th class="cabeza">Tope <br>Alimentación</th>
                                        <th class="cabeza">Tope <br> Alimentación Docente</th>
                                        <th class="cabeza">Tope <br> Auxilio Transporte</th>
                                        <th class="cabeza">Incapacidad <br>EPS (%)</th>                                       
                                        <th class="cabeza">Recargo Noc. (%)</th>
                                        <th class="cabeza">Recargo D.F. (%)</th>
                                        <th class="cabeza">Horas Extras D.O (%)</th>
                                        <th class="cabeza">Horas Extras D.D.F (%)</th>
                                        <th class="cabeza">Horas Extras N.O (%)</th>
                                        <th class="cabeza">Horas Extras N.D.F (%)</th>
                                        <th class="cabeza">Horas Extras <br/>Noct. Ordinarias (%)</th>
                                        <th class="cabeza">Redondeo</th>
                                        <th class="cabeza">Salud Sena</th>
                                        <th class="cabeza">Exento<br>Parafiscales</th>
                                        <th class="cabeza">Dias Prima<br>Vacaciones</th>
                                        <th class="cabeza">Días Bonificación<br>Recreación</th>
                                        <th class="cabeza">Limite Bonificación<br>Servicios</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            
                                            $plid      = $row[0];
                                            $plvi      = $row[1];
                                            $plsm      = $row[2];
                                            $plat      = $row[3];
                                            $plpa      = $row[4];
                                            $plpm      = $row[5];
                                            $plsepl    = $row[6];
                                            $plsepr    = $row[7];
                                            $plpepl    = $row[8];
                                            $plpepr    = $row[9];
                                            $plfsol    = $row[10];
                                            $plexre    = $row[11];
                                            $plcacom   = $row[12];
                                            $plsena    = $row[13];
                                            $plicbf    = $row[14];
                                            $plesap    = $row[15];
                                            $plmini    = $row[16];
                                            $pluvt     = $row[17];
                                            $plali     = $row[18];
                                            $plalid    = $row[19];
                                            $vigen     = $row[21];
                                            $inca      = $row[22];
                                            $excen     = $row[23];
                                            $recno     = $row[24];
                                            $recdom    = $row[25];
                                            $hextdo    = $row[26];
                                            $hextddf   = $row[27];
                                            $hextno    = $row[28];
                                            $hextndf   = $row[29];
                                            $redondeo  = $row[30];
                                            $saludsena = $row[31];
                                            $grupog    = $row[35];
                                            $provis    = $row[33];
                                            $heno      = $row[36]; 
                                            $tauxt     = $row[37]; 
                                            $diaspv    = $row[38]; 
                                            $diasbr    = $row[39]; 
                                            $limitbon  = $row[40]; 

                                        ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td >
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_PARAMETRO_LIQUIDACION.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                       
                                        <td class="campos"><?php echo $grupog?></td> 
                                        <td class="campos"><?php echo $provis?></td>                
                                        <td class="campos"><?php echo number_format(($plsm),2,'.',',')?></td>                
                                        <td class="campos"><?php echo number_format(($plat),2,'.',',')?></td>                
                                        <td class="campos"><?php echo number_format(($plpa),2,'.',',')?></td>                
                                        <td class="campos"><?php echo number_format(($plpm),2,'.',',')?></td>
                                        <td class="campos"><?php echo $plsepl?></td>                
                                        <td class="campos"><?php echo $plsepr?></td>                
                                        <td class="campos"><?php echo $plpepl?></td>                
                                        <td class="campos"><?php echo $plpepr?></td>                
                                        <td class="campos"><?php echo $plfsol?></td>
                                        <td class="campos"><?php echo $plexre?></td>                
                                        <td class="campos"><?php echo $plcacom?></td>                
                                        <td class="campos"><?php echo $plsena?></td>                
                                        <td class="campos"><?php echo $plicbf?></td>                
                                        <td class="campos"><?php echo $plesap?></td>
                                        <td class="campos"><?php echo $plmini?></td>
                                        <td class="campos"><?php echo number_format(($pluvt),2,'.',',')?></td>
                                        <td class="campos"><?php echo number_format(($plali),2,'.',',')?></td>

                                        <td class="campos"><?php echo number_format(($plalid),2,'.',',')?></td>
                                        <td class="campos"><?php echo number_format(($tauxt),2,'.',',')?></td>
                                        <td class="campos"><?php echo $inca?></td>                                       
                                        <td class="campos"><?php echo $recno    ?></td>
                                        <td class="campos"><?php echo $recdom   ?></td>
                                        <td class="campos"><?php echo $hextdo   ?></td>
                                        <td class="campos"><?php echo $hextddf  ?></td>
                                        <td class="campos"><?php echo $hextno   ?></td>
                                        <td class="campos"><?php echo $hextndf  ?></td>
                                        <td class="campos"><?php echo $heno  ?></td>
                                        <td class="campos"><?php echo $redondeo ?></td>
                                        <td class="campos"><?php if ($saludsena == '1'){echo "Si";}else{echo "No";}?></td>
                                        <td class="campos"><?php if ($excen == '1'){echo "Si";}else{echo "No";}?></td>
                                        <td class="campos"><?php echo $diaspv ?></td>
                                        <td class="campos"><?php echo $diasbr ?></td>
                                        <td class="campos"><?php echo  number_format(($limitbon),2,'.',',') ?></td>
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                            <div align="right">
                                <a href="registrar_GN_PARAMETRO_LIQUIDACION.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a>
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
          <p>¿Desea eliminar el registro seleccionado?</p>
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
                  url:"json/eliminarParametroLiquidacionJson.php?id="+id,
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
        document.location = 'listar_GN_PARAMETRO_LIQUIDACION.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GN_PARAMETRO_LIQUIDACION.php';
      });    
  </script>
    </body>
</html>