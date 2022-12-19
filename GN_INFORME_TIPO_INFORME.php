<?php
#####################################################################################################################################
#                                                                                   Modificaciones
######################################################################################################################################
#27/07/2017 | Erica González | Nombre de informe para los encabezados
######################################################################################################################################
require_once('Conexion/conexion.php');
require_once('head_listar.php');

$id_tipo_informe = " ";
if (isset($_GET["id"])) {
  $id_tipo_informe = (($_GET["id"]));
  $queryInf = "SELECT id, nombre
    FROM gn_tipo_informe
    WHERE md5(id) = '$id_tipo_informe'";
}

$resultado = $mysqli->query($queryInf);
$row = mysqli_fetch_row($resultado);

$idTipoInforme = $row[0];
$nombreTipoInforme = $row[1];

$queryInforme = "SELECT i.id, i.nombre, ci.nombre , ci.id_unico , i.nombre_informe 
FROM gn_informe i LEFT JOIN gn_clase_informe ci ON i.clase_informe = ci.id_unico  
WHERE tipo_informe = $idTipoInforme";
$resultado = $mysqli->query($queryInforme);
?>
  <title>Informe</title>
  <link href="css/select/select2.min.css" rel="stylesheet">
  <style type="text/css">
    .acotado { white-space: normal; }
    table.dataTable thead tr th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px;white-space: nowrap}
    .dataTables_wrapper .ui-toolbar{padding:2px}
  </style>
</head>
<body>
  <input type="hidden" id="id_tipo_informe" value="<?php echo $id_tipo_informe;?>">
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once ('menu.php'); ?>
      <div class="col-sm-10 text-left"> 
        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Informe</h2>
        <a href="modificar_GN_TIPO_INFORME.php?id_tipo_informe=<?php echo $id_tipo_informe;?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px" align="center"><?php echo ucwords(mb_strtolower($nombreTipoInforme)); ?></h5>
        <div class="client-form contenedorForma form-horizontal">
          <p align="center" style="margin-bottom: 0px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
          <div class="form-group form-inline" style="margin-top: 5px;margin-bottom: 0px">
          <label for="nombre" class="control-label col-sm-1"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control col-sm-1" maxlength="100" title="Ingrese el nombre" style="width: 200px;" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" required>
            <label for="nombreEncabezado" class="control-label col-sm-1"><strong style="color:#03C1FB;"></strong>Nombre Encabezado:</label>
            <input type="text" name="nombreEncabezado" id="nombreEncabezado" class="form-control col-sm-1" maxlength="100" title="Ingrese el nombre Encabezado" style="width: 200px;" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre Encabezado" >
            <div class="form-group form-inline">
                <?php $ci = "SELECT * FROM gn_clase_informe ORDER BY nombre ASC";
                $ci= $mysqli->query($ci);
                ?>
                <label for="nombre" class="control-label col-sm-3"><strong style="color:#03C1FB;">*</strong>Clase Informe:</label>
                <select id="clase" name="clase" class="select2_single form-control col-sm-5" required="required" style="width: 250px">
                    <option value="">Clase Informe</option>
                <?php while ($row1 = mysqli_fetch_row($ci)) { ?>
                    <option value="<?php echo $row1[0]?>"><?php echo ucwords(mb_strtolower($row1[1]))?></option>       
                        <?php }?>    
                </select>
                
            </div>
            <div class="form-group form-inline">
            <div class="col-sm-1" style="margin-top: 10px">
              <button type="button" id="btnGuardar" class="btn btn-primary sombra col-sm-1" style="width: 40px"> <li class="glyphicon glyphicon-floppy-disk"></li></button>
            </div>
            </div>
          </div>
        </div>
        <input type="hidden" id="idTipoInforme" value="<?php echo $idTipoInforme;?>">
        <script type="text/javascript">          
          $("#btnGuardar").click(function() {
            if($("#nombre").val() != "" && $("#nombre").val() != 0 && $("#clase").val() != "" && $("#clase").val() != 0) {
              var nombre = $("#nombre").val();
              var idTipoInforme = $("#idTipoInforme").val();
              var claseI =$("#clase").val();
               var nombreEncabezado = $("#nombreEncabezado").val();
              var form_data = { estruc: 3, nombre: nombre, idTipoInforme: idTipoInforme, clase:claseI ,nombreEncabezado:nombreEncabezado};
              $.ajax({
                type: "POST",
                url: "estructura_gestor_informes.php",
                data: form_data,
                success: function(response) {
                  if(response == 1) {
                    $("#mdlExitoGuar").modal('show');
                  } else {
                    $("#mdlErrorGuar").modal('show');
                  }
                }//Fin succes.
              }); //Fin ajax.
            } else {
              $("#mdlNombreVacio").modal('show');
            }
          });          
        </script>
        <div class="col-sm-12" style="margin-top: 10px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
              <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <td style="display: none;">Identificador</td>
                    <td width="30px" align="center"></td>
                    <td><strong>Nombre</strong></td>
                    <td><strong>Clase Informe</strong></td>
                    <td><strong>Nombre Encabezado</strong></td>
                  </tr>
                  <tr>
                    <th style="display: none;">Identificador</th>
                    <th width="7%"></th>
                    <th>Nombre</th>
                    <th>Clase Informe</th>
                    <th>Nombre Encabezado</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    while($row = mysqli_fetch_row($resultado)) {
                  ?>
                   <tr>
                    <td style="display: none;"><?php echo $row[0];?></td>
                    <td>
                      <a href="#<?php echo $row[0];?>" onclick="javascript:eliminarInforme(<?php echo $row[0];?>);">
                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                      </a>
                      <a href="#<?php echo $row[0];?>" onclick="javascript:modificarInforme(<?php echo $row[0].",'".ucwords(mb_strtolower(($row[1])))."',".$row[3].",'".$row[4]."'";?>);">
                        <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                      </a>
                    </td>
                    <td><?php echo ucwords(mb_strtolower($row[1]));?></td>
                    <td><?php echo ucwords(mb_strtolower($row[2]));?></td>
                    <td><?php echo $row[4];?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div> <!-- Cierra Clase table-responsive -->
        </div>
      </div> <!-- Cierra Clase col-sm-10 text-left -->
    </div>
  </div>
  <div class="modal fade" id="myModalUpdate" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <input type="hidden" name="idM" id="idM">
      <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
        </div>
        <div class="modal-body "  align="center">
          <div class="form-group" align="left">
            <label  style="margin-left:150px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
            <input style="display:inline-block; width:250px; font-size: 0.9em; height: 30px; padding: 5px;" type="text" name="nombreM" id="nombreM" title="Ingrese el nombre" class="form-control input-sm" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Nombre"  required>
          </div>
            <div class="form-group" align="left">
            <label  style="margin-left:83px; display:inline-block;"><strong style="color:#03C1FB;"></strong>Nombre Encabezado:</label>
            <input style="display:inline-block; width:250px; font-size: 0.9em; height: 30px; padding: 5px;" type="text" name="nombreEM" id="nombreEM" title="Ingrese el nombre encabezado" class="form-control input-sm" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Nombre"  >
          </div>
            <div class="form-group" align="left">
            <div class="form-group  form-inline" align="left">
                <?php $ci = "SELECT * FROM gn_clase_informe ORDER BY nombre ASC";
                $ci= $mysqli->query($ci);
                ?>
                <label for="nombre" style="margin-left:116px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Clase Informe:</label>
                <select id="claseMod" name="claseMod" class="select2_single form-control " style="width:250px; "  required="required" >
                <?php while ($row1 = mysqli_fetch_row($ci)) { ?>
                    <option value="<?php echo $row1[0]?>"><?php echo ucwords(mb_strtolower($row1[1]))?></option>       
                        <?php }?>    
                </select>
            </div>
            </div>
            
          <input type="hidden" id="id" name="id">
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" onclick="javascript:modificarItem()" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
          <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Informe?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
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
  <div class="modal fade" id="myModal2" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
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
  <div class="modal fade" id="mdlExitoGuar" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnExitoGuar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlErorGuar" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErorGuar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlNombreVacio" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El campo Nombre se encuentra vacío o su valor no es válido. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnNombreVacio" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlNombreVacioMod" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El campo Nombre se encuentra vacío o su valor no es válido. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnNombreVacioMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlExitoModf" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnExitoModf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlErorModf" role="dialog" align="center"   data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErorModf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <?php require_once ('footer.php'); ?>  
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    function eliminarInforme(id) {
       var result = '';
       $("#myModal").modal('show');
       $("#ver").click(function(){
            $("#mymodal").modal('hide');
            $.ajax({
                type:"GET",
                url:"json/eliminar_GN_INFORMEJson.php?id="+id,
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
  
    function modificarInforme(id, nombre, clase,nombreE) {
      document.getElementById('idM').value = id;
      document.getElementById('nombreM').value = nombre;
      document.getElementById('claseMod').value = clase;
      document.getElementById('nombreEM').value = nombreE;
      $("#myModalUpdate").modal('show');
    }
  
    function modificarItem() {
      var id = document.getElementById('idM').value;
      var nombre = document.getElementById('nombreM').value;
      var clase = document.getElementById('claseMod').value ;
      var nombreE = document.getElementById('nombreEM').value;
      if(nombre != "" && nombre != 0) {
        var form_data = { estruc: 4, id: id, nombre: nombre, clase:clase,nombreE:nombreE };
        $.ajax({
          type: "POST",
          url: "estructura_gestor_informes.php",
          data: form_data,
          success: function(response) {
              console.log(response);
            if(response == 1) {
              $("#myModalUpdate").modal('hide');
              $("#mdlExitoModf").modal('show');
            } else {
              $("#mdlErorModf").modal('show');
            }
          }//Fin succes.
        }); //Fin ajax.
      } else {
        $("#mdlNombreVacioMod").modal('show');
      }
    }

    function modal() {
      $("#myModal").modal('show');
    }
  
    $('#ver1').click(function() {
      var id = $("#id_tipo_informe").val();
      document.location = 'GN_INFORME_TIPO_INFORME.php?id=' + id;
    });

    $('#ver2').click(function() {
      var id = $("#id_tipo_informe").val();
      document.location = 'GN_INFORME_TIPO_INFORME.php?id=' + id;
    });

    $('#btnNombreVacio').click(function() {
      $("#nombre").focus();
    });
    
    $('#btnExitoGuar').click(function() {
      document.location.reload();
    });

    $('#btnExitoModf').click(function() {
      document.location.reload();
    });

    $('#btnNombreVacioMod').click(function() {
      $("#nombreM").focus();
    });
  </script>
  <script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>
