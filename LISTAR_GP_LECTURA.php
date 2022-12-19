<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once('./jsonServicios/funcionesServicios.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
?>
<head>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <title>Listar Lectura</title>
    <style>
        label #periodo-error, #sectorI-error, #sectorF-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;

        }
    </style>
    <script>
        $().ready(function() {
          var validator = $("#form").validate({
                ignore: "",

            errorPlacement: function(error, element) {

              $( element )
                .closest( "form" )
                  .find( "label[for='" + element.attr( "id" ) + "']" )
                    .append( error );
            },
            rules: {
                sltmes: {
                  required: true
                },
                sltcni: {
                  required: true
                },
                sltAnnio: {
                  required: true
                }
             }
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top: -15px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Lectura</h2>
                <?php if(empty($_GET['p'])) { ?>                
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:buscar()" >  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 10px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <label for="periodo" class="col-sm-12 control-label"><strong class="obligado">*</strong>Periodo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                <select name="periodo" id="periodo" class="form-control select2" title="Seleccione Periodo" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Periodo</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, 
                                            DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
                                            DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
                                            FROM gp_periodo p 
                                            WHERE anno = $anno ORDER BY fecha_inicial DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'  '.$tr[$i][2].' - '.$tr[$i][3].'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <label for="sectorI" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector Inicial:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
                                <select name="sectorI" id="sectorI" class="form-control select2" title="Seleccione Sector Inicial" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Inicial</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_sector 
                                            ORDER BY codigo ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <label for="sectorF" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector Final:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
                                <select name="sectorF" id="sectorF" class="form-control select2" title="Seleccione Sector Final" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector Final</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_sector 
                                            ORDER BY codigo DESC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title=Buscar"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                    <script>
                        function buscar(){
                            document.location ='LISTAR_GP_LECTURA.php?p='+$("#periodo").val()+'&s1='+$("#sectorI").val()+'&s2='+$("#sectorF").val();
                        }
                    </script>
                </div>
                <?php } else { 
                    $id_sectorI = $_REQUEST['s1'];
                    $id_sectorF = $_REQUEST['s2'];
                    $perioodo   = $_REQUEST['p'];
                    $id_periodo    = $_REQUEST['p'];
                    $p = $con->Listar("SELECT DISTINCT id_unico, 
                        nombre, 
                        DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
                        DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
                        FROM gp_periodo p 
                        WHERE id_unico=".$_REQUEST['p']);
                    $periodo =ucwords(mb_strtolower($p[0][1])).'  '.$p[0][2].' - '.$p[0][3];
                    $s1 = $con->Listar("SELECT DISTINCT id_unico, 
                        nombre, codigo
                        FROM gp_sector 
                        WHERE id_unico =".$_REQUEST['s1']);
                    $sectorInicial = $s1[0][2].' - '.ucwords(mb_strtolower($s1[0][1]));
                    $s = $con->Listar("SELECT DISTINCT id_unico, 
                        nombre, codigo
                        FROM gp_sector 
                        WHERE id_unico =".$_REQUEST['s2']);
                    $sectorFinal = $s[0][2].' - '.ucwords(mb_strtolower($s[0][1]));
                    ?>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 10px">
                            <input type="hidden" name="periodo" id="periodo" value="<?php echo $p[0][0];?>">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="">
                                <label for="periodo" class="col-sm-12 control-label"><strong class="obligado">*</strong>Periodo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="">
                                <label for="periodo" class="col-sm-12 control-label" style="font-weight: normal"><?php echo $periodo;?></label>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <label for="sectorI" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector Inicial:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                <label for="sectorI" class="col-sm-12 control-label" style="font-weight: normal"><?php echo $sectorInicial;?></label>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  20px;">
                                <label for="sectorI" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector Final:</label>
                            </div>
                            <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                <label for="sectorI" class="col-sm-12 control-label" style="font-weight: normal"><?php echo $sectorFinal;?></label>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  10px;">
                                <a href="LISTAR_GP_LECTURA.php" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Buscar Lecturas"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  -20px;">
                                <a href="informes_servicios/INF_LECTURA.php?s=<?php echo $id_sectorI.'&p='.$id_periodo.'&sf='.$id_sectorF?>" target="_blank" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Informe Lecturas"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  -20px;">
                                <a href="GP_LECTURA.php" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Guardar Lecturas"><i class="glyphicon glyphicon-saved" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Código Sistema</strong></td>
                                    <td><strong>Sector</strong></td>
                                    <td><strong>Código Ruta</strong></td>
                                    <td><strong>Dirección</strong></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Lectura Anterior</strong></td>
                                    <td><strong>Lectura Actual</strong></td>
                                    <td><strong>Valor</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Código Sistema</th>
                                    <th>Sector</th>
                                    <th>Código Ruta</th>
                                    <th>Dirección</th>
                                    <th>Tercero</th>
                                    <th>Lectura Anterior</th>
                                    <th>Lectura Actual</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <?php 
                            $row = $con->Listar("SELECT uvms.id_unico, p.codigo_catastral, 
                                 p.direccion , 
                                IF(CONCAT_WS(' ',
                                     t.nombreuno,
                                     t.nombredos,
                                     t.apellidouno,
                                     t.apellidodos) 
                                     IS NULL OR CONCAT_WS(' ',
                                     t.nombreuno,
                                     t.nombredos,
                                     t.apellidouno,
                                     t.apellidodos) = '',
                                     (t.razonsocial),
                                     CONCAT_WS(' ',
                                     t.nombreuno,
                                     t.nombredos,
                                     t.apellidouno,
                                     t.apellidodos)) AS NOMBRE, 
                                     s.codigo, s.nombre, uv.codigo_ruta 
                                FROM gp_unidad_vivienda_medidor_servicio uvms 
                                LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                                LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                                WHERE uvs.estado_servicio = 1 AND m.estado_medidor != 3 
                                AND uv.sector BETWEEN $id_sectorI AND $id_sectorF  
                                AND p.estado
                                ORDER BY cast(s.codigo as unsigned),cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC");
                            $periodo = $_REQUEST['p'];
                            $periodoa =periodoA ($periodo);
                             for ($i = 0; $i < count($row); $i++) {
                                $id_uvms = $row[$i][0];
                                #*** Buscar Lectura Anterior ***#
                                $la = $con->Listar("SELECT valor, IF(LENGTH(valor)>3, SUBSTRING(valor, -3), valor) FROM gp_lectura 
                                    WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $periodoa");
                                if(empty($la[0][0])){
                                    $la = 0;
                                    $lam = 0;
                                } else {
                                    $lam = $la[0][1];
                                    $la = $la[0][0];
                                }
                                $lac  ="";
                                $lact = $con->Listar("SELECT valor, IF(LENGTH(valor)>3, SUBSTRING(valor, -3), valor) FROM gp_lectura 
                                    WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $periodo");
                                $lacm = 0;
                                if(count($lact)>0) {
                                    $lac = $lact[0][0];
                                    $lacm = $lact[0][1];
                                }
                                if($lac==""){
                                    $lectura  = "";
                                } else {
                                    $lectura  = $lac -$la;
                                    if($lectura ==($la*-1)){
                                        $lectura = 0;
                                    } 
                                }
                                IF(!empty($lectura)){
                                    echo '<tr>';
                                    echo '<td style="display: none;">';
                                    echo '<input type="hidden" name="uvms'.$i.'" id="uvms'.$i.'" value="'.$id_uvms.'">';
                                    echo '</td>';
                                    echo '<td width="7%">';
                                    echo '<label name="mensaje'.$i.'" id="mensaje'.$i.'" style="color:#bd081c"></label>';
                                    echo '</td>';
                                    echo '<td>'.$row[$i][1].'</td>';
                                    echo '<td>'.$row[$i][4].' - '.ucwords(mb_strtolower($row[$i][5])).'</td>';
                                    echo '<td>'.$row[$i][6].'</td>';
                                    echo '<td>'.$row[$i][2].'</td>';
                                    echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
                                    echo '<td style="text-align:center">'.$lam.'</td>';
                                    echo '<td style="text-align:center">'.$lacm.'</td>';
                                    echo '<td style="text-align:center"><label name="cantidad'.$i.'" id="cantidad'.$i.'">'.$lectura.'</label></td>';
                                    echo '</tr>';
                                }
                                 
                             }
                            ?>
                        </table>
                    </div>
                </div>
                <?php }  ?>
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
                    <p>¿Desea eliminar el registro seleccionado de lectura?</p>
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
    <div class="modal fade" id="myModalTipoLectura" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content" style="width: 500px;">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Registrar Lectura</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group"  align="center">
                        <select style="font-size:15px;height: 40px;" name="lectura" id="lectura" class="form-control" title="Registrar lectura" required>
                            <option >Registrar Lectura</option>
                            <option value="1">Lectura individual</option>
                            <option value="2">Lectura masiva</option>
                            <option value="3">Lectura por archivo plano</option>
                        </select>
                    </div>                           
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="men" class="btn" onclick="enviarLectura()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>	
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>	
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#periodo").select2();
        $("#sectorI").select2();
        $("#sectorF").select2();
    </script>

    <!-- Función para la eliminación del registro. -->
    <script type="text/javascript">
          function eliminar(id)
          {
             var result = '';
             $("#myModal").modal('show');
             $("#ver").click(function(){
                  $("#mymodal").modal('hide');
                  $.ajax({
                      type:"GET",
                      url:"json/eliminar_GP_LECTURAJson.php?id="+id,
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
        function modalTipoL(){
           $("#myModalTipoLectura").modal('show');
        }
    </script>
    <script type="text/javascript">
      function enviarLectura() {
          var lectura = document.getElementById('lectura').value;
          var periodo = $("#periodo").val();
          switch (lectura){
              case ('1'):
                  document.location = 'GP_LECTURA_INDIVIDUAL.php?p='+periodo;
              break;
              case ('2'):
                  document.location = 'GP_LECTURA_MASIVA.php?p='+periodo;
              break;
              case ('3'):
                  document.location = 'GP_LECTURA_ARCHIVO_PLANO.php?p='+periodo;
              break;
              default:
                  $("#myModalTipoLectura").modal('hide');
              break;
          }
      }    
    </script>
</body>
</html>


