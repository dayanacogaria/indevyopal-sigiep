<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
require_once('./jsonPptal/funcionesPptal.php');
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
    <title>Lectura</title>
    <script type="text/javascript">
      $(document).ready(function () {
          var i = 1;
          $('#tablaO thead th').each(function () {
              if (i != 1) {
                  var title = $(this).text();
                  switch (i) {
                      case 3:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 4:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 5:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 6:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 6:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 7:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 8:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 9:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 10:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 11:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 12:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 13:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 14:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 15:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 16:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 17:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 18:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 19:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 20:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 21:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 22:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 23:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 24:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 25:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 26:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 27:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 28:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 29:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 30:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 31:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 32:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 33:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 34:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 35:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 36:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 37:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 38:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 39:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 40:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 41:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 42:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 43:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 44:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 45:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 46:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 47:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 48:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 49:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                      case 50:
                          $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                          break;
                  }
                  i = i + 1;
              } else {
                  i = i + 1;
              }
          });

          // DataTable
          var table = $('#tablaO').DataTable({
              "autoFill": true,
              "scrollX": true,
              "pageLength": 10,
              "language": {
                  "lengthMenu": "Mostrar _MENU_ registros",
                  "zeroRecords": "No Existen Registros...",
                  "info": "Página _PAGE_ de _PAGES_ ",
                  "infoEmpty": "No existen datos",
                  "infoFiltered": "(Filtrado de _MAX_ registros)",
                  "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
              },
              'columnDefs': [{
                      'targets': 0,
                      'searchable': false,
                      'orderable': false,
                      'className': 'dt-body-center'
                  }]
          });

          var i = 0;
          table.columns().every(function () {
              var that = this;
              if (i != 0) {
                  $('input', this.header()).on('keyup change', function () {
                      if (that.search() !== this.value) {
                          that
                                  .search(this.value)
                                  .draw();
                      }
                  });
                  i = i + 1;
              } else {
                  i = i + 1;
              }
          });
      });
    </script>
    <style>
    /*Modificación al diseño del la tabla Datatable*/
    table.dataTable thead th,table.dataTable thead td{padding:0px 18px;font-size:12px}
    table.dataTable tbody td,table.dataTable tbody td{padding:0px}
    .dataTables_wrapper .ui-toolbar{padding:2px}

</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top: -15px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Lecturas</h2>
                <?php if(empty($_GET['p']) && empty($_GET['s'])) { ?>                
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 50px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  50px;">
                                <label for="periodo" class="col-sm-12 control-label"><strong class="obligado">*</strong>Periodo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  50px;">
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
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  50px;">
                                <label for="sector" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  50px;">
                                <select name="sector" id="sector" class="form-control select2" title="Seleccione Sector" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector</option>';
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
                        </div>
                    </form>
                    <script>
                        $("#periodo").change(function(){
                            if($("#sector").val() !=""){
                                if($("#periodo").val()!=""){
                                    document.location ='GP_LECTURA.php?p='+$("#periodo").val()+'&s='+$("#sector").val();
                                }
                            }
                        })
                        $("#sector").change(function(){
                            if($("#sector").val() !=""){
                                if($("#periodo").val()!=""){
                                    document.location ='GP_LECTURA.php?p='+$("#periodo").val()+'&s='+$("#sector").val();
                                }
                            }
                        })
                    </script>
                </div>
                <?php }  else  { 
                    $id_sector     = $_REQUEST['s'];
                    $id_periodo    = $_REQUEST['p'];
                    $p = $con->Listar("SELECT DISTINCT id_unico, 
                        nombre, 
                        DATE_FORMAT(fecha_inicial, '%d/%m/%Y'),
                        DATE_FORMAT(fecha_final, '%d/%m/%Y')                                       
                        FROM gp_periodo p 
                        WHERE id_unico=".$_REQUEST['p']);
                    $periodo =ucwords(mb_strtolower($p[0][1])).'  '.$p[0][2].' - '.$p[0][3];
                    $s = $con->Listar("SELECT DISTINCT id_unico, 
                        nombre, codigo
                        FROM gp_sector 
                        WHERE id_unico =".$_REQUEST['s']);
                    $sector = $s[0][2].' - '.ucwords(mb_strtolower($s[0][1]));
                    ?>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 50px">
                            <input type="hidden" name="periodo" id="periodo" value="<?php echo $p[0][0];?>">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  50px;">
                                <label for="periodo" class="col-sm-12 control-label"><strong class="obligado">*</strong>Periodo:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  50px;">
                                <label for="periodo" class="col-sm-12 control-label" style="font-weight: normal"><?php echo $periodo;?></label>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  50px;">
                                <label for="sector" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  50px;">
                                <label for="periodo" class="col-sm-12 control-label" style="font-weight: normal"><?php echo $sector;?></label>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  10px;">
                                <a href="GP_LECTURA.php" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  -20px;">
                                <a href="informes_servicios/INF_LECTURA.php?s=<?php echo $id_sector.'&p='.$id_periodo?>" target="_blank" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Informe Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  -20px;">
                                <a href="LISTAR_GP_LECTURA.php" target="_blank" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title=Buscar"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tablaO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Código Sistema</strong></td>
                                    <td><strong>Código Ruta</strong></td>
                                    <td><strong>Dirección</strong></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Lectura Anterior</strong></td>
                                    <td><strong>Lectura Actual</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Medidor Dañado</strong></td>
                                    <td><strong>Vivienda Vacía</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Código Sistema</th>
                                    <th>Código Ruta</th>
                                    <th>Dirección</th>
                                    <th>Tercero</th>
                                    <th>Lectura Anterior</th>
                                    <th>Lectura Actual</th>
                                    <th>Valor</th>
                                    <th>Medidor Dañado</th>
                                    <th>Vivienda Vacía</th>
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
                                     t.apellidodos)) AS NOMBRE , 
                                     m.estado_medidor, m.id_unico, 
                                     uv.codigo_ruta, 
                                     uv.deshabilitado, 
                                     uv.id_unico 
                                FROM gp_unidad_vivienda_medidor_servicio uvms 
                                LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                                LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico 
                                LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico 
                                LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                                WHERE uvs.estado_servicio = 1 
                                AND uv.sector =".$_REQUEST['s']." 
                                AND p.estado != 3 AND m.estado_medidor != 3 
                                ORDER BY cast((replace(uv.codigo_ruta, '.','')) as unsigned) ASC ");
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
                                    $la  = $la[0][0];                                    
                                }
                                #*** Buscar Lectura Actual ***#
                                $lac  ="";
                                $lact = $con->Listar("SELECT valor,IF(LENGTH(valor)>3, SUBSTRING(valor, -3), valor) FROM gp_lectura 
                                    WHERE unidad_vivienda_medidor_servicio = $id_uvms AND periodo = $periodo");
                                
                                if(count($lact)>0) {
                                    $lac = $lact[0][0];
                                }
                                if($lac==""){
                                    $lectura  = "";
                                } else {
                                    $lectura  = $lac -$la;
                                    if($lectura ==($la*-1)){
                                        $lectura = 0;
                                    } 
                                }
                                $lacm = $lact[0][1];
                                echo '<tr>';
                                echo '<td style="display: none;">'; 
                                echo '<input type="hidden" name="uvms'.$i.'" id="uvms'.$i.'" value="'.$id_uvms.'">';
                                echo '</td>';
                                echo '<td width="7%">';
                                echo '<label name="mensaje'.$i.'" id="mensaje'.$i.'" style="color:#bd081c"></label>';
                                echo '</td>';
                                echo '<td>'.$row[$i][1].'</td>';
                                echo '<td>'.$row[$i][6].'</td>';
                                echo '<td>'.$row[$i][2].'</td>';
                                echo '<td>'.ucwords(mb_strtolower($row[$i][3])).'</td>';
                                echo '<td style="text-align:center">'.$lam.'</td>';
                                echo '<td style="text-align:center">';
                                #*** Buscar Si Ya Tiene Factura ****#
                                $fc = $con->Listar("SELECT * FROM gp_factura  
                                    WHERE unidad_vivienda_servicio = $id_uvms AND periodo = $periodo");
                                if(count($fc)>0){
                                    echo $lacm;
                                } else { 
                                    echo '<input name="valor'.$i.'" id="valor'.$i.'" value = "'.$lacm.'" '
                                        . 'class="col-sm-2 form-control" title="Seleccione Valor" required '
                                        . 'title="Ingrese el valor" style=" width: 80%; height:10px;"  '
                                        . 'onchange="valor('.$i.')" />';
                                }
                                echo '</td>';
                                echo '<td style="text-align:center"><label name="cantidad'.$i.'" id="cantidad'.$i.'">'.$lectura.'</label></td>';
                                echo '<td style="text-align:center">';
                                #** Estado Medidor **#
                                echo '<input type="hidden" name="id_medidor'.$i.'" id="id_medidor'.$i.'" value="'.$row[$i][5].'">';
                                if($row[$i][4]==1){
                                    echo '<input name="medidor'.$i.'" id="medidor'.$i.'" type="checkbox" onchange="cambiarV('.$i.')" checked>';
                                } else { 
                                    echo '<input name="medidor'.$i.'" id="medidor'.$i.'" type="checkbox" onchange="cambiarV('.$i.')">';
                                }
                                echo '</td>';
                                echo '<td style="text-align:center">';
                                #** Casa Vacía **#
                                echo '<input type="hidden" name="id_unidad_vivienda'.$i.'" id="id_unidad_vivienda'.$i.'" value="'.$row[$i][8].'">';
                                if($row[$i][7]==1){
                                    echo '<input name="casav'.$i.'" id="casav'.$i.'" type="checkbox" onchange="cambiarCV('.$i.')" checked>';
                                } else { 
                                    echo '<input name="casav'.$i.'" id="casav'.$i.'" type="checkbox" onchange="cambiarCV('.$i.')">';
                                }
                                echo '</td>';
                                echo '</tr>';
                                 
                             }
                            ?>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#periodo").select2();
        $("#sector").select2();
    </script>
    <script>
        function valor(i){
            var periodo = $("#periodo").val();
            var nuvms   = 'uvms'+i;
            var uvms    = $("#"+nuvms).val();
            var nvalor  = 'valor'+i;
            var valor   = $("#"+nvalor).val();
            var nmsj    = 'mensaje'+i;
            var ncan    = 'cantidad'+i;
            //** Guardar, Actualizar **//
            var form_data ={action:1,periodo:periodo, uvms:uvms, valor:valor}
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_LecturaJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==0){
                        $("#"+nmsj).html('Guardado');
                        //*** Verificar Cantidad ***//
                        var form_data ={action:2,periodo:periodo, uvms:uvms, valor:valor}
                        $.ajax({
                            type: "POST",
                            url: "jsonServicios/gp_LecturaJson.php",
                            data: form_data,
                            success: function(response)
                            {
                                $("#"+ncan).html(response);
                                if(response>0){                                    
                                    //** Verificar Promedio **//
                                    var form_data ={action:3,periodo:periodo, uvms:uvms, valor:valor}
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonServicios/gp_LecturaJson.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log('Promedio'+response);
                                            if(response>150){
                                                $("#"+nmsj).html('Valor excede el 50% del promedio');
                                            } else {
                                                
                                            }
                                        }
                                    })                                    
                                } else {
                                    $("#"+nmsj).html('Verificar Valor');
                                }
                            }
                        })
                    } else {
                        $("#"+nmsj).html('Error!!');
                    }
                }
            })
            
        }
        
        function cambiarV(i){
            var id_m    = 'id_medidor'+i;
            var id      = $("#"+id_m).val();
            var ncheck = 'medidor'+i;
            var nmsj    = 'mensaje'+i;
            if($("#"+ncheck).prop('checked')){
                var estado = 1;
            } else {
                var estado = 2;
            }
            var form_data ={action:7,estado :estado,medidor:id}
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_facturacionServiciosJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==0){
                        $("#"+nmsj).html('Estado Actualizado');
                        
                    } else {
                        $("#"+nmsj).html('Error');
                    }
                }
            })
        }
        
        function cambiarCV(i){
            var id_uv   = 'id_unidad_vivienda'+i;
            var id      = $("#"+id_uv).val();
            var ncheck  = 'casav'+i;
            var nmsj    = 'mensaje'+i;
            if($("#"+ncheck).prop('checked')){
                var estado = "1";
            } else {
                var estado = "2";
            }
            var form_data ={action:6,estado :estado,uv:id}
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_UnidadViviendaJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==0){
                        $("#"+nmsj).html('Estado Actualizado');
                        
                    } else {
                        $("#"+nmsj).html('Error');
                    }
                }
            })
        }
    </script>
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
</body>
</html>


