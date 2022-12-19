<?php 
  require_once('Conexion/conexion.php');
  require_once 'head.php';
  require_once ('nombreBaseDatos.php');
?>
  <title>Cargar Archivo</title>
  <link rel="stylesheet" href="css/jquery-ui.css">
  <script src="js/jquery-ui.js"></script> 
  <link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>
  <div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-top:0px;margin-right: 4px; margin-left: 4px;">Cargar Archivo</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">
          <form name="form" method="post" class="form-horizontal"  enctype="multipart/form-data" action="json/registrar_cargar_archivoJson.php"> 
          <p align="center" style="margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>          
          <div class="form-group" style="margin-top: -5px;">
            <label for="lblArchivo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Archivo:</label>
            <input type="file" name="archivo" id="archivo" class="form-control" style="max-width: 300px;min-width: 100px" title="Ingrese el nombre" required> 
          </div>
          <div class="form-group" style="margin-top: -15px;">
            <label for="separador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Separador:</label>              
            <input type="text" name="separador" id="separador" class="form-control input-sm" title="Ingrese el caracter de separador" maxlength="20" placeholder="Separador"  required>
            <script type="text/javascript">                
                $("#separador").keyup(function() {
                  if($("#separador").val() != "") {
                    if($("#archivo").val() == "") {
                      $("#mdlErrorSelArch3").modal('show');
                    }
                  }
                });                
            </script>                
          </div>
          <div class="form-group" style="margin-top: -15px;">
            <label for="filaIni" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fila Inicial:</label>              
            <input type="text" name="filaIni" id="filaIni" class="form-control input-sm" value="1" title="Ingrese el número de fila inicial" placeholder="Fila Inicial" onkeypress="return txtValida(event, 'num')" required />              
            <script type="text/javascript">
              $("#filaIni").keyup(function() {
                if($("#filaIni").val() != "") {
                  var fila = $("#filaIni").val();
                  fila = parseInt(fila);
                  $("#filaIni").val(fila);
                  $("#nomFila").val("filaIni");
                  validarFila(fila);
                }
              });
            </script>
          </div>
          <div class="form-group" style="margin-top: -15px;">
            <label for="filaFin" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fila Final:</label>
            <input type="text" name="filaFin" id="filaFin" class="form-control input-sm" title="Ingrese el núemro de fila final" placeholder="Fila Final" onkeypress="return txtValida(event, 'num')" required>
            <script type="text/javascript">
              $("#filaFin").keyup(function() {
                if($("#filaFin").val() != "") { 
                  var fila = $("#filaFin").val();
                  fila = parseInt(fila);
                  $("#filaFin").val(fila);
                  if($("#filaIni").val() != "" && $("#filaIni").val() != 0) {
                    if($("#filaIni").val() <= $("#filaFin").val()) {
                      $("#nomFila").val("filaFin");
                      validarFila(fila);
                    } else {
                      $("#mdlErrorNumMayor").modal('show');
                    }
                  } else {
                    $("#mdlErrorValIni").modal('show');
                  }
                }
              });
            </script>
          </div>
          <div class="form-group" style="margin-top: -15px;">
            <label for="tabla" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tabla Seleccionada:</label>              
            <input type="hidden" id="tablaOculta">
            <select name="tabla" id="tabla" onchange="llenar();" class="form-control input-sm col-sm-1 select2_single" title="Seleccione una tabla a insertar" style="max-width:300px;min-width: 100px" required>              
              <?php
                if(!empty($_GET['table'])){
                  $table = $_GET['table'];
                  echo "<option value=".$table.">".ucwords(strtolower($table))."</option>";
                  echo $sqlColDestino = "SELECT table_name 
                  FROM INFORMATION_SCHEMA.TABLES 
                  WHERE table_name != '$table'";
                  $colDestino = $mysqli->query($sqlColDestino);
                  while($rowCD = mysqli_fetch_row($colDestino)) {
                    echo '<option value="'.$rowCD[0].'">'.ucwords(strtolower($rowCD[0])).'</option>';
                  }                  
                }else{
                  echo "<option value=\"\" selected=\"selected\">Tabla Seleccionada</option>";
                 echo  $sqlColDestino = "SELECT table_name 
                    FROM INFORMATION_SCHEMA.TABLES";
                  $colDestino = $mysqli->query($sqlColDestino);
                  while($rowCD = mysqli_fetch_row($colDestino)) {
                    echo '<option value="'.$rowCD[0].'">'.ucwords(strtolower($rowCD[0])).'</option>';
                  }                  
                }   
              ?> 
            </select>              
            <!-- select2 -->
            <script src="js/select/select2.full.js"></script>
            <script>                
              $(".select2_single").select2({
                allowClear: true
              });            

              $(document).ready(function() {llenar();});

              function llenar() {
                var tabla = document.getElementById('tabla').value;
                document.getElementById('tablaOculta').value= tabla;
              }

              $("#tabla").change(function() {
                if($("#archivo").val() != "" && $("#separador").val() != "") {
                  var archivo = $("#archivo").prop('files')[0];
                  var tabla = $("#tabla").val();
                  var separador = $("#separador").val();
                  
                  if(separador == "tab" || separador == "TAB") {
                    separador = "\t";
                  }
                  
                  var filaIni = $("#filaIni").val();
                  filaIni --;
                  var filaFin = $("#filaFin").val();
                  filaFin --;

                  var form_data = new FormData();
                  form_data.append('estruc', 4);
                  form_data.append('archivo', archivo);
                  form_data.append('tabla', tabla);
                  form_data.append('separador', separador);
                  form_data.append('filaIni', filaIni);
                  form_data.append('filaFin', filaFin);
                  $.ajax({
                    url: "estructura_genera_tablas.php",
                    dataType: 'text',
                    cache: false,
                    contentType: false, 
                    processData: false, 
                    data: form_data,
                    type: "POST",
                    success: function(response) {
                      var res = parseInt(response);
                      if(res == 1) {
                        $("#mdlErrorNumCampTabla").modal('show');
                      } else if(res == 2) {
                        $("#mdlErrorNoSeparador").modal('show');
                      } else if(res == 3) {
                        $("#mdlErrorTipoCampo").modal('show');
                      }
                    }//Fin succes.
                  }); //Fin ajax. 
                } else {
                  $("#mdlErrorSelArch2").modal('show');
                }
              });              
            </script>
          </div>          
          <div class="form-group" style="margin-top: 10px;margin-bottom: 0px">
            <label for="no" class="col-sm-5 control-label"></label>
            <button type="submit" class="btn btn-primary sombra" id="btnGuardar" style=" margin-left: 0px;">Guardar</button>
          </div>
          <div class="col-sm-2 col-sm-offset-9 text-center" style="box-shadow: inset 2px 1px 2px 2px darkgray;border-radius: 5px;margin-top: -250px">
            <p class="title text-primary" style="margin-top: 5px"><strong>Separador</strong></p>
            <div class="form-group" style="margin-top: -5px;margin-bottom: 0px">
              <label class="col-sm-5">Tabulador:</label><span class="col-sm-1">TAB,tab</span>              
            </div>            
          </div>                   
        </form>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="nomFila">
<script type="text/javascript">
  $(document).ready(function(){$("#nomFila").val("");});
</script>
<div class="modal fade" id="mdlErrorNumFila" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El número de filas del archivo (<span id="numFilas"></span> filas) es menor al número ingresado. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorNumFila" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorSelArch" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe seleccionar un archivo.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorSelArch" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorSelArch2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe seleccionar un archivo y/o asignar un valor a Separador.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorSelArch2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorSelArch3" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe seleccionar un archivo.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorSelArch3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorExt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Este tipo de archivo no es válido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorExt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorNumMayor" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El número de fila inicial es mayor al número de fila final. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorNumMayor" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorValIni" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El campo de fila inicial está vacío. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorValIni" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorNumCampTabla" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Los datos del archivo seleccionado no corresponden a la tabla seleccionada. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorValIni" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorNoSeparador" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El separador asignado no se encuentra dentro del rango de filas indicado. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorNoSeparador" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorTipoCampo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Los tipos de datos entre el archivo y la tabla no coinciden. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorTipoCampo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorSeparador" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Este separador no se encuentra en el archivo. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorSeparador" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>  
<?php require_once 'footer.php'; ?>
<script type="text/javascript">
  $("#archivo").change(function() {
    var ext = $("#archivo").val();
    ext = ext.substring(ext.length-3,ext.length);
    ext = ext.toLowerCase();
    if(ext != 'txt') {
      $("#mdlErrorExt").modal('show');
      $("#archivo").val("");
    }
  });

  function validarFila(fila) {
    if($("#archivo").val() != "") {
      var archivo = $("#archivo").prop('files')[0];
      var form_data = new FormData();
      form_data.append('estruc', 3);
      form_data.append('archivo', archivo);
      form_data.append('fila', fila);  
      $.ajax({
        url: "estructura_genera_tablas.php",
        dataType: 'text',
        cache: false,
        contentType: false, 
        processData: false, 
        data: form_data,
        type: "POST",
        success: function(response) {
          var numFilas = parseInt(response);
          if(fila > numFilas) {
            $("#numFilas").text(numFilas);
            var nomFila = $("#nomFila").val();
            $("#"+nomFila).attr('readonly', 'readonly')
            $("#mdlErrorNumFila").modal('show');
            $('#btnErrorNumFila').focus();
          }
        }//Fin succes.
      }); //Fin ajax. 
    } else {
      var nomFila = $("#nomFila").val();
      $("#"+nomFila).attr('readonly', 'readonly')
      $("#mdlErrorSelArch").modal('show');
    }
  }

  $('#btnErrorSelArch').click(function() {
    var nomFila = $("#nomFila").val();
    $("#"+nomFila).removeAttr('readonly')
    $("#"+nomFila).val("").focus();
    $("#nomFila").val("");

    $("#archivo").focus();
  });

  $('#btnErrorNumFila').click(function() {
    var nomFila = $("#nomFila").val();
    $("#"+nomFila).removeAttr('readonly')
    $("#"+nomFila).val("").focus();
    $("#nomFila").val("");
  });

  $('#btnErrorExt').click(function() {
    $("#archivo").focus();
  });

  $('#btnErrorNumMayor').click(function() {
    $("#filaIni").val(1);
    $("#filaFin").val("").focus();
  });

  $('#btnErrorValIni').click(function() {
    $("#filaIni").val(1);
    $("#filaFin").focus();
  });

  $('#btnErrorNoSeparador').click(function() {
    $("#separador").focus();
  });

  $('#btnErrorTipoCampo').click(function() {
    $("#tabla").focus();
  });

  $('#btnErrorSeparador').click(function() {
    $("#separador").val("").focus();
  });

  $('#btnErrorSelArch3').click(function() {
    $("#separador").val("");
    $("#archivo").focus();
  });
</script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
</body>
</html>

