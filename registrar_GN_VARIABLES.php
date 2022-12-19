<?php 
	require_once('Conexion/conexion.php');
  require_once 'head_listar.php';
  $queryInforme = ""; 
  $resultado = "";
  $num_col = 0;
  $id = 0;
  $catFor = 0;

  if(!empty($_SESSION['consulta_gi'])) {
    $miarray = $_SESSION['columnas_gi'];
    $array_para_recibir_via_url = stripslashes($miarray);
    $columnas_gi = unserialize($array_para_recibir_via_url);
    $num_col = count($columnas_gi);
    $queryInforme = $_SESSION['consulta_gi'];
    $resultado = $mysqli->query($queryInforme);    
    $id = $_SESSION['id_gi'];
    $catFor = $_SESSION['catFor_gi'];
  }
	$sqlCatFor = "SELECT id_unico, nombre 
  FROM gn_categoria_formula   
  ORDER BY nombre ASC";
	$cateForm = $mysqli->query($sqlCatFor);
?>
	 <title>Configuración de Variables</title>
   <style type="text/css">
    .area { height: auto !important; }  
    /*Esto permite que el texto contenido dentro del div
    no se salga de las medidas del mismo.*/
    .acotado { white-space: normal; }
    table.dataTable thead th,table.dataTable thead td {padding: 1px 18px;font-size: 10px;}
    table.dataTable tbody td,table.dataTable tbody td {padding: 1px;}
    .dataTables_wrapper .ui-toolbar{padding: 2px;font-size: 10px;}
    .control-label{font-size: 12px;}
    .itemListado{margin-left: 5px;margin-top: 5px;width: 150px;cursor: pointer;}
    #listado {width: 150px;height: 80px;overflow: auto;background-color: white;}
  </style>
</head>
<body>
  <input type="hidden" id="idVariable" value="<?php echo $id;?>">
  <input type="hidden" id="idCatFor" value="<?php echo $catFor;?>">
  <input type="hidden" id="queryInforme" value="<?php echo $queryInforme;?>">
  <div class="container-fluid text-center">
    <div class="row content">   
    <?php require_once 'menu.php';?>
    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 0px; margin-right: 4px; margin-left: 4px;">Configuración de Variables</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form col-sm-12">
        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificarConsulta();">
          <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <div class="form-group">              
              <label for="catFor" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Categoría Fórmula:</label>
              <select name="catFor" id="catFor" class="form-control col-sm-1" title="Categoría Fórmula" style="width: 30.1%;" required>                
                <?php 
                echo "<option value=\"\">Categoría Fórmula</option>";
                while($row = mysqli_fetch_row($cateForm)) {
                  echo '<option value="'.$row[0].'">'.utf8_encode(ucwords(strtolower($row[1]))).'</option>';
                }
                ?>
              </select>                         
              <!-- <label for="nombre" class=" control-label"> -->
              <label for="variable" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Variables:</label>
              <select name="variable" id="variable" class="form-control col-sm-1" title="Variable" style="width: 30.3%;" required>
                <option value="">Variable</option>
              </select>                                
            </div> <!-- -->           
            <div class="form-group" style="margin-top: -15px;margin-bottom: 0px">
              <label for="consulta" class="control-label col-sm-2">Consulta:</label>
              <textarea class="resaltar col-sm-1 form-control" name="consulta" id="consulta" placeholder="Consulta" style="width:72%; height: 100px;margin-top: 0px"  maxlength="1000"><?php echo $queryInforme; ?></textarea>            
              <div class="col-sm-1">                  
                <button type="button" id="btnEjecutar" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;margin-bottom: 20px"><li class="glyphicon glyphicon-play"></li></button>                                
                <button type="submit" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><li class="glyphicon glyphicon-floppy-disk"></li></button>                  
                </div>
              </div>
            </div>          
            <input type="hidden" name="MM_insert" >
        </form>
      </div>
      <script type="text/javascript">          
        $("#catFor").change(function() {
          $("#consulta").val("");
          var opcion = '<option value="">Variable</option>';
          if($("#catFor").val() == "") {
            $("#variable").html(opcion).fadeIn();
          } else {
            var catFor = $("#catFor").val();
            var form_data = { estruc: 5, catFor: catFor };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_categorias.php",
              data: form_data,
              success: function(response) {
                response = response.trim();
                if(response != "") {
                  opcion += response;
                  $("#variable").html(opcion).fadeIn().focus();
                } else {
                  opcion = '<option value="">No hay Variables</option>';
                  $("#variable").html(opcion).fadeIn();
                }  
              }//Fin succes.
            }); //Fin ajax.
          }
        });
                          
        $("#variable").change(function() { 
          $("#consulta").val("");
          if($("#variable").val() != "") {                  
            var variable = $("#variable").val();
            var form_data = { estruc: 6, variable: variable };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_categorias.php",
              data: form_data,
              success: function(response) {
                response = response.trim();
                if(response != "") {
                  $("#consulta").val(response).focus();
                } else {
                  $("#consulta").attr("placeholder", "Este informe no tiene consulta.").focus();
                }  
              }//Fin succes.
            }); //Fin ajax.
          }
        });
        
        $("#catFor").on("change", function() {
          placeConsulta();
        });
        $("#variable").on("change", function() {
          placeConsulta();
        });
        $("#consulta").on("keypress", function() {
          placeConsulta();
        });
        
        function placeConsulta() {
          if($("#consulta").attr("placeholder") != "Consulta") {
            $("#consulta").attr("placeholder", "Consulta");
          }
        }          
      </script>
      <input type="hidden" id="posicion"> <!-- Posición del cursor en el textarea. -->
      <input type="hidden" id="sombra"> <!-- Sombra original del textarea. -->
      <input type="hidden" id="borde"> <!-- Borde original del textarea. -->
      <input type="hidden" id="errorMysql" > <!-- Error del la consulta SQL -->
      <script type="text/javascript">
        $("#btnEjecutar").click(function() {
          if($("#consulta").val() != "") {
            var consulta = $("#consulta").val();
            consulta =  consulta.toLowerCase();
            var no_sentencias =['delete', 'update', 'insert', 'create', 'alter', 'drop', 'truncate', 'mysql', 'show', 'databases', 'replace', 'optimize', 'grant', 'revoke', 'flush', 'explain', ' tables', 'lock', 'set', 'start', 'analyze', 'check', 'handler', 'kill', 'load ', 'reset '];
              //Quedan pendientes: USE nombreBaseDatos, DESCRIBE nombreTabla, DO expresión.
            var no_sen = 0;
            for(var i in no_sentencias) {
              if(consulta.indexOf(no_sentencias[i])!=-1) { 
                var posicion = consulta.indexOf(no_sentencias[i]);
                no_sen = 1;
                break;
              }
            }

            if (no_sen == 1) {
              $("#posicion").val(posicion);
              $("#mdlErrorCon").modal('show');            
            } else {
              var id = $("#variable").val();
              var catFor = $("#catFor").val();
              var form_data = { estruc: 2, id: id, catFor: catFor, consulta: consulta };  
              $.ajax({
                type: "POST",
                url: "estructura_gestor_categorias.php",
                data: form_data,
                success: function(response) {
                  if(response == 1) {
                    document.location.reload();
                  } else if(response == 0) {
                    $("#mdlSinDatos").modal('show');
                  } else {
                    $("#errorMysql").val(response);
                    asignar();
                    $("#mdlErrorMysql").modal('show');
                  }
                }//Fin succes.
              }); //Fin ajax.
            }              
          }
        });
      
        function ponCursorEnPos(pos) {
          laCaja = document.getElementById('consulta');
          //método IE 
          if(typeof document.selection != 'undefined' && document.selection) {         
            var tex=laCaja.value; 
            laCaja.value='';  
            laCaja.focus(); 
            var str = document.selection.createRange();  
            laCaja.value=tex; 
            str.move("character", pos);  
            str.moveEnd("character", 0);  
            str.select(); 
          }  else if(typeof laCaja.selectionStart != 'undefined') /*método estándar */{                    
            laCaja.setSelectionRange(pos,pos);  
          } 
        }
            
        $("#consulta").blur(function() {
          limpTexArea();
        });      
      
        $("#consulta").keypress(function() {
          if($("#sombra").val() != "") {
            limpTexArea();
          }              
        });          
      
        function modificarConsulta() {
          var id = $("#variable").val();
          var consulta = $("#consulta").val();
          var form_data = { estruc: 7, id: id, consulta: consulta };  
          $.ajax({
            type: "POST",
            url: "estructura_gestor_categorias.php",
            data: form_data,
            success: function(response) {
              if(response == 1) {
                $("#mdlModConExito").modal('show');
              } else {
                $("#mdlModConError").modal('show');
              }                           
            }//Fin succes.
          }); //Fin ajax.
        }
      </script>
      <div class="form-group col-sm-10" style="margin-top: 5px;">
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td style="display: none;">Identificador</td>
                  <td width="30px" align="center"></td>
                  <?php  
                  for ($i = 0; $i < $num_col; $i++)  { 
                      echo '<td><strong>'.ucwords($columnas_gi[$i]).'</strong></td>';
                  } ?>                
                </tr>
                <tr>
                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <?php 
                  for ($i = 0; $i < $num_col; $i++)  { 
                    echo '<th>'.ucwords($columnas_gi[$i]).'</th>';
                  }
                  ?>
                </tr>
              </thead>
              <tbody>            
                <?php
                if(!empty($resultado)) {
                  while($row = mysqli_fetch_row($resultado)) { ?>
                  <tr>
                    <td style="display: none;"> </td>
                    <td></td> 
                    <?php 
                    for ($i = 0; $i < $num_col; $i++)  { 
                      echo '<td><div class="acotado">'.ucwords(strtolower($row[$i])).'</div></td>';
                    } ?>                
                </tr>
                <?php }
               } ?>
              </tbody>
            </table>     
          </div>            
        </div>
      </div>
    </div>
  </div>
</div>
<?php if(!empty($_SESSION['consulta_gi'])) { ?>
<script type="text/javascript">
  $(document).ready(function() {
    var idCatFor = $("#idCatFor").val();
    var idVariable = $("#idVariable").val();
    var consulta = $("#queryInforme").val();
    $('#catFor > option[value="' + idCatFor + '"]').attr('selected', 'selected');
    $("#consulta").val(consulta);
    var opcion = '<option value="">Variable</option>';
    var idCatFor = $("#idCatFor").val();
    var form_data = { estruc: 5, catFor: idCatFor };  
    $.ajax({
      type: "POST",
      url: "estructura_gestor_categorias.php",
      data: form_data,
      success: function(response) {
        opcion += response;
        $("#variable").html(opcion).fadeIn(); 
        $('#variable > option[value="' + idVariable + '"]').attr('selected', 'selected');
      }//Fin succes.
    }); //Fin ajax.
  });
</script>
<?php } ?>
<div class="modal fade" id="mdlErrorCon" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La consulta que desea ejecutar no está permitida. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorCon" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlSinDatos" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La consulta no arroja ningún resultado.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnSinDatos" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorMysql" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>
          La consulta arroja el siguiente error: <span id="errorMsql" style="font: bold 90% monospace;"></span>
          </br> Verifique nuevamente.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorMysql" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlModConExito" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">        
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModConExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload_page()">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlModConError" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">        
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModConError" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<?php  require_once 'footer.php'; ?>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">    
  $('#btnErrorCon').click(function() {
    $('#consulta').focus();
    var posicion = $("#posicion").val();
    ponCursorEnPos(posicion);
    var sombra =  $("#consulta").css("box-shadow");
    var borde =  $("#consulta").css("border");
    $("#sombra").val(sombra);
    $("#borde").val(borde);
    $("#consulta").css("box-shadow", "0 0 2px rgba(255,0,0,1)"); //estaba 5px
    $("#consulta").css("border", "1px solid rgba(255,0,0,0.8)");
  });      

  function limpTexArea() {
    var sombra = $("#sombra").val();
    var borde = $("#borde").val();
    $("#consulta").css("box-shadow", sombra);
    $("#consulta").css("border", borde);
    $("#sombra").val("");
    $("#borde").val("");
  }
  
  function asignar() {
    var error = $("#errorMysql").val();
    $("#errorMsql").text(error);
  }  
    
  $('#btnErrorMysql').click(function() {
    $('#consulta').focus();
  });  

  function reload_page() {
    window.location.reload();
  }  
</script>
</body>
</html>