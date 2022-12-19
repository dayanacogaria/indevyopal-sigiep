<?php 
  require_once('Conexion/conexion.php');
  require_once 'head.php';
?>
  <script type="text/javascript" src="js/reservadas_mysql.js"></script>
  <title>Generar Tabla</title>
  <style type="text/css" media="screen">
    body{font-size: 14px}
  </style>
</head>
<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Generar Tabla</h2>
        <div class="client-form contenedorForma">
          <form name="form" class="form-horizontal"  enctype="multipart/form-data" action="javascript:generaTable();">
            <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%; ">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <div class="form-group form-inline">                
              <label for="nombre" class="control-label col-sm-3"><strong class="obligado">*</strong>Nombre Tabla:</label>                 
              <input type="text" name="nombre" id="nombre" class="form-control  col-sm-1" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car_raya_baja');" placeholder="Nombre Tabla" style="width: 200px" required>
              <label for="noCampos" class="col-sm-2 control-label"><strong class="obligado">*</strong>Número Campos:</label>              
              <input type="text" name="noCampos" style="width: 110px;" id="noCampos" class="form-control col-sm-1" maxlength="2" title="Ingrese el número de campos" onkeypress="return txtValida(event, 'num');" placeholder="Número Campos" title="Número de campos" required>          
              <div class="col-sm-1">
                <button type="submit" id="btnTable" class="btn btn-primary sombra" style=" margin-top: 0px;" title="Continuar"><span class="glyphicon glyphicon-arrow-right"></span></button>
              </div>
            </div>
          </form>
        </div> <!-- Cierra clase client-form col-sm-12 -->
        <input type="hidden" id="autInc">
        <input type="hidden" id="numCampos">
        <input type="hidden" id="indicador">
        <div  class="laTabla col-sm-12" style="display: none; margin-top: 5px;">
          <div class="col-sm-10"></div>
          <div class="col-sm-2" align="left">
            <button type="button" id="btnGenera" class="btn btn-primary sombra" style="margin: 0px;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
          </div>
        </div>
        <script type="text/javascript">          
          $("#autInc").val(0); //Se asigna el valor 0 al Hidden autInc
          $("#btnTable").prop("disabled", false); //Se mantiene activo el botón                  
          //Por medio de la función key press se inhabilita el botón
          $("#noCampos").keypress(function() {
            if($("#btnTable").prop("disabled") == true) {
              $("#btnTable").prop("disabled", false);
            }            
          });
          //Cuendo el botón click es precionado
          $("#btnGenera").click(function() {
            if($("#nombre").val() != "" && $("#nombre").val() != 0) {
              var nombre = $("#nombre").val();
              var form_data = { estruc: 2, nombre: nombre};  
              $.ajax({
                type: "POST",
                url: "estructura_genera_tablas.php",
                data: form_data,
                success: function(response) {
                  var res = parseInt(response);
                  if(res == 2) {
                    verificarCampos();
                  }  else {
                    $("#nombreTabla").text(nombre);
                    $("#mdlExisteTabla").modal('show');
                  }
                }//Fin succes.
              }); //Fin ajax. 
            } else {
              $("#mdlTablaVacia").modal('show');
            }
          });

          function verificarCampos() {
            var err = 0;
            var n = parseInt($("#numCampos").val());
            var res = 0;
            var campo = "";
            for(i = 0; i < n; i++) {
              ind = i + 1;
              if($("#campo"+ind).val() == "" || $("#campo"+ind).val() == 0) {
                err = 1;
                break;
              }
              if(($("#longitud"+ind).val() == "" || $("#longitud"+ind).val() == 0) && $("#tipo"+ind).val() == 2) {
                err = 2;
                break;
              }
              campo = $("#campo"+ind).val();
              campo = campo.trim();
              campo = campo.toLowerCase();
              res = verificarReservadas(campo);
              if(res == 1) {
                err = 3;
                break;
              }
            }
            $("#indicador").val(ind);
            if(err == 0) {
              crearTabla();
            } else if(err == 1) {
              $("#mdlFaltaNombre").modal('show'); //Mensaje falta nombre
            } else if(err == 2) {
              $("#mdlFaltaLongitud").modal('show'); //Mensaje falta longitud
            } else if(err == 3) {
              $("#campoRes").text($("#campo"+ind).val());
              $("#mdlErrReservada").modal('show'); //Mensaje falta longitud
            }
          }

          function crearTabla() {
            var nombre = $("#nombre").val();
            var n = parseInt($("#numCampos").val());
            var ind = 0; 
            var fila = new Array();
            var completo = new Array();
            var campo = "";              
            for(var i = 0; i < n; i++) {
              ind = i + 1;
              campo = $("#campo"+ind).val();
              campo = campo.toLowerCase();
              fila[0] = campo;
              fila[1] = $("#tipo"+ind).val();
              fila[2] = $("#longitud"+ind).val();

              if($("#nulo"+ind).is(':checked')) {
                fila[3] = '2'; //Sí.
              } else {
                fila[3] = '1'; //No.
              }

              if($("#unico"+ind).is(':checked')) {
                fila[4] = '2'; //Sí.
              } else {
                fila[4] = '1'; //No.
              }
              completo[i] = serializeArr(fila);
            }
            cadCompleto = serializeArr(completo);
            var form_data = { estruc: 1, completo: cadCompleto, n: n, nombre: nombre};  
            $.ajax({
              type: "POST",
              url: "estructura_genera_tablas.php",
              data: form_data,
              success: function(response) {
                /*console.log(response);*/
                var res = response;
                if(res == 2) {
                  $("#mdlExtCrearTabla").modal('show'); //Tabla creada con éxito.
                } else if(res == 1) {
                  $("#mdlErrCrearTabla").modal('show'); //La tabla no pudo ser creada.
                } else {
                  $("#mdlErrConsCrearTabla").modal('show'); //Error en la consulta al crear la tabla.
                  $("#errorCreaTabla").text(response);
                } 
              }//Fin succes.
            }); //Fin ajax. 
          }
        </script>
        <div class="col-sm-12 laTabla" style="display: none; margin-top: 10px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;"> 
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;"> 
              <table id="tabla" class="col-sm-12 table table-condensed table-hover table-striped table-bordered text-left" class="display" cellspacing="0" width="100%">  
                <thead> 
                  <tr> 
                    <td style="display: none;">Identificador</td> 
                    <td width="30px" align="center"></td> 
                    <td><strong>Nombre</strong></td> 
                    <td><strong>Tipo</strong></td> 
                    <td><strong>Longitud</strong></td> 
                    <td><strong>Nulo</strong></td> 
                    <!-- <td><strong>Índice</strong></td>  
                    <td><strong>Auto Incremento</strong></td> -->
                    <td><strong>Único</strong></td> 
                  </tr> 
                </thead> 
                <tbody> 
                </tbody> 
              </table> 
            </div> 
          </div> 
        </div>      
      </div> <!-- Cierra clase col-sm-10 text-left -->
    </div> <!-- Cierra clase row content -->
  </div> <!-- Cierra clase container-fluid text-center -->
  <div class="modal fade" id="mdlPrimary" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Ya existe un campo autoincremental.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnPrimary" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlNoNulo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Este campo no puede ser nulo.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnNoNulo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlExisteTabla" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La tabla <span id="nombreTabla"></span> ya existe en la base de datos. Verifique de nuevo</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnExisteTabla" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlFaltaNombre" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El campo nombre para este campo está vacío o es inválido. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnFaltaNombre" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlFaltaLongitud" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El valor de este campo no puede ser vacío. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnFaltaLongitud" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlErrReservada" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El nombre "<span id="campoRes"></span>" no puede ser usado por ser una palabra clave reservada de MySQL. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrReservada" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlTablaVacia" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El nombre de la tabla no es válido. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnTablaVacia" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlExtCrearTabla" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La tabla fue creada exitosamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnExtCrearTabla" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlErrCrearTabla" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La tabla no pudo ser creada.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrCrearTabla" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mdlErrConsCrearTabla" role="dialog" align="center" data-keyboard="false" data-backdrop="static"> <!-- No hay nombre -->
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>La tabla no pudo ser creada. <span id="errorCreaTabla"></span></p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrConsCrearTabla" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <?php require_once 'footer.php'; ?>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
  	function generaTable() {
      if($("#nombre").val() != "" && $("#noCampos").val() != "" && $("#noCampos").val() != 0) {
        $("#btnTable").prop("disabled", true);
        $(".nuvFila").remove();
        $(".laTabla").css("display", "block");
        var n = parseInt($("#noCampos").val());
        $("#numCampos").val(n);
        var tabla = '';
        var ind = 0;
        for(var i = 0; i < n; i++) {
          ind = i + 1;
          tabla += '<tr class="nuvFila"> \
            <td style="display: none;"></td> \
            <td class="campos"></td> \
            <td class="campos"> \
              <input type="text" id="campo' + ind + '" style="width: 150px; margin-right: 10px;"  class="form-control input-sm" title="Columna" onkeypress="return txtValida(event, \'car_raya_baja\');" placeholder="Columna" required> \
            </td> \
            <td class="campos" style="padding-right: 10px;"> \
              <select id="tipo' + ind + '"  style="width: 100px;"  class="form-control input-sm" title="Tipo"> \
                <option value="1" selected="selected">Int</option> \
                <option value="2">Varchar</option> \
              </select> \
            </td> \
            <td class="campos"> \
              <input type="text" id="longitud' + ind + '" style="width: 80px;"  class="form-control input-sm" title="Columna" onkeypress="return txtValida(event, \'num\');" placeholder="Longitud" required> \
            </td> \
            <td class="campos"> \
              <input id="nulo' + ind + '" type="checkbox" onclick="javascript:validaNulo(' + ind + ');"> \
            </td> \
            <td class="campos"> \
              <input id="unico' + ind + '" type="checkbox" onclick="javascript:validaUnico(' + ind + ');"> \
            </td> \
          </tr> ';
        }
        $("#tabla tbody").append(tabla);
      }
    }

    function validaNulo(ind) {
      if($("#unico"+ind).is(':checked')) {
        $('#nulo'+ind).prop('checked', false); 
      }
    }

    function validaUnico(ind) {
      if($("#unico"+ind).is(':checked')) {
        $('#nulo'+ind).prop('checked', false); 
      }
    }

    function serializeArr(arr) {
      var res = 'a:'+arr.length+':{';
      for(ni = 0; ni < arr.length; ni ++) {
        res += 'i:' + ni + ';s:' + arr[ni].length + ':"' + arr[ni] + '";';
      }
      res += '}';       
      return res;
    }

    function verificarReservadas(campo) {
      var res = 2;
      for(no = 0; no < verMysql.length; no ++) {
        if(campo == verMysql[no]) {
          res = 1;
        }
      }       
      return res;
    }

    $('#btnExisteTabla').click(function() {
      $("#nombre").focus();
    });
  
    $('#btnFaltaLongitud').click(function() {
      var ind = $("#indicador").val();
      $("#longitud"+ind).focus();
      $("#indicador").val(0);
    });
  
    $('#btnErrReservada').click(function() {
      var ind = $("#indicador").val();
      $("#campo"+ind).focus();
      $("#indicador").val(0);
    });
  
    $('#btnFaltaNombre').click(function() {
      var ind = $("#indicador").val();
      $("#campo"+ind).focus();
      $("#indicador").val(0);
    });
  
    $('#btnTablaVacia').click(function() {
      $("#nombre").focus();
    });
  
    $('#btnExtCrearTabla').click(function() {
      document.location = 'listar_tabla.php';
    });
  </script>
</body>
</html>

