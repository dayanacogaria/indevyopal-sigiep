<?php
#############################################################################################
#       ***************************     Modificaciones      ***************************     #
#############################################################################################
#19/04/2018 | Erica G.  | Parametrizacion
# Modificado por        : Jhon Numpaque
# Fecha de modificación : 19/03/2017
# Descripción           : Se indento y orga 
###############################################################################################################################################################
# Modificado Ferney Pérez Cano // 21/02/2017. Modificada la función llenarTab().
###############################################################################################################################################################

  require_once('Conexion/conexion.php');
  require_once('head_listar.php');
?>
  <style type="text/css">
    .area{height: auto !important;}  
    /*Esto permite que el texto contenido dentro del div
    no se salga de las medidas del mismo.*/
    .acotado{white-space: normal;}
    table.dataTable thead th,table.dataTable thead td{padding: 1px 18px;font-size: 12px;}
    table.dataTable tbody td,table.dataTable tbody td{padding: 1px;}
    .dataTables_wrapper .ui-toolbar{padding: 2px;font-size: 12px;}
    .control-label{font-size: 12px;}
    .itemListado{margin-left: 5px;margin-top: 5px;width: 150px;cursor: pointer;}
    #listado {width: 150px;height: 80px;overflow: auto;background-color: white;}
    .cabeza{white-space:nowrap;padding: 20px;}
    .campos{padding:-20px;}
    table.dataTable thead tr th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px;white-space: nowrap}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    body{font-size: 12px}
  </style>
  <title>Configuración Homologación</title>  
  <link rel="stylesheet" href="css/jquery-ui.css">
  <script src="js/jquery-ui.js"></script> 
  <link href="css/select/select2.min.css" rel="stylesheet">
  <script type="text/javascript">
    // Este código construye la cabecera de la tabla tablaBase.
    $(document).ready(function() {
      var i= 0;
      $('#tablaBase thead th').each( function () {
        if(i >= 0){ 
          var title = $(this).text();
          switch (i){
          case 2:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 3:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 4:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 5:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;

          }
          i = i+1;
      }else{
        i = i+1;
      }
    } );
    // DataTable
    var table = $('#tablaBase').DataTable({
      "autoFill": true,
      "scrollX": true,
      "pageLength": 5,
      "language": {
        "lengthMenu": "Mostrar _MENU_ registros",
        "zeroRecords": "No Existen Registros...",
        "info": "Página _PAGE_ de _PAGES_ ",
        "infoEmpty": "No existen datos",
        "infoFiltered": "(Filtrado de _MAX_ registros)",
        "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
      },
      'columnDefs': [{
        'targets': 0,
        'searchable':false,
        'orderable':false,
        'className': 'dt-body-center'
      }]
    });
    var i = 0;
    table.columns().every( function () {
      var that = this;
      if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
              that
                  .search( this.value )
                  .draw();
          }
        } );
          i = i+1;
        }else{
          i = i+1;
        }
      } );
    } );
  </script>  
</head>
<body>
  <input type="hidden" id="idDetalleInforme" value="0" > <!-- Aquí almacena el id del registro que se va a modificar o eliminar seleccionado de la tabla tablaBase. -->  
  <input type="hidden" id="arranca" value="0">
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once ('menu.php'); ?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" class="col-sm-12" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Configuración Homologación</h2>
        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript: guardar();">
          <div style="" class="client-form col-sm-12 contenedorForma">
            <p align="center" style="margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
              <div class="form-group col-sm-12 form-inline" style="margin-top:-5px;margin-bottom:-5px">                                  
                <label for="tipoInforme" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Tipo Informe:</label>                    
                <?php 
                $sqlTipoInforme = "SELECT id, nombre 
                FROM gn_tipo_informe  
                ORDER BY nombre ASC";
                $tipoInforme = $mysqli->query($sqlTipoInforme);
                ?>
                <select name="tipoInforme" id="tipoInforme" class="form-control input-sm col-sm-1" title="Informe" style="width: 150px;" required>
                  <option value="">Tipo Informe</option>
                  <?php 
                  while($rowTI = mysqli_fetch_row($tipoInforme)){
                    echo '<option value="'.$rowTI[0].'">'.ucwords(mb_strtolower($rowTI[1])).'</option>';
                  }
                  ?>
                </select>                      
                <script type="text/javascript">                    
                  $(document).ready(function(){
                    $("#tipoInforme").change(function(){
                      var origen = '<option value="">Informe</option>';
                      if($("#tipoInforme").val() != ""){
                        var tipoInf = $("#tipoInforme").val();
                        var form_data = { estruc: 8, tipoInf: tipoInf };  
                        $.ajax({
                          type: "POST",
                          url: "estructura_gestor_informes.php",
                          data: form_data,
                          success: function(response){
                            response = response.trim();
                            if(response != ""){
                              origen += response;
                              $("#informe").html(origen).fadeIn();
                              $("#informe").focus();
                            }else{
                              origen = '<option value="">No hay Informe</option>';
                              $("#informe").html(origen).fadeIn();
                            }              
                          }//Fin succes.
                        }); //Fin ajax.
                      }else{
                        $("#informe").html(origen).fadeIn();
                      }
                    });
                  });
                </script>                  
                <label for="informe" class="control-label col-sm-1"><strong style="color:#03C1FB;">*</strong>Informe:</label>
                <select name="informe" id="informe" onchange="habilita();" class="form-control input-sm col-sm-1" title="Informe" style="width: 150px;" required>
                <option value="">Informe</option>
                </select>                                                         
                <label for="periodicidad" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Periodicidad:</label>
                <?php 
                $sqlPeriodicidad = "SELECT id, nombre 
                FROM gn_periodicidad   
                ORDER BY nombre ASC";
                $periodicidad = $mysqli->query($sqlPeriodicidad);
                ?>                  
                <select name="periodicidad" id="periodicidad" onchange="habilita();" class="form-control input-sm col-sm-1" title="Informe" style="width: 150px;" required>
                  <option value="">Periodicidad</option>
                  <?php 
                  while($rowP = mysqli_fetch_row($periodicidad)){
                    echo '<option value="'.$rowP[0].'">'.ucwords(mb_strtolower($rowP[1])).'</option>';
                  }
                  ?>
                </select>                   
                <div class="col-sm-1 form-inline form-group">
                  <div class="col-sm-1" style="width: 45px">
                    <button type="submit" id="btnGuardarForm" class="btn btn-primary sombra habilita" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top:0px;width: 40px"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                  </div>
                  <div class="col-sm-1" style="width: 45px">
                    <button type="button" id="btnModificarForm" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top:0px;width: 40px;display:none"><li class="glyphicon glyphicon-edit"></li></button>
                  </div>                  
                  <div class="col-sm-1" style="margin-top: -44px;margin-left: 45px;">
                    <button type="button" id="btnCancelarMod" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top:0px;width: 40px;display:none"><li class="glyphicon glyphicon-remove"></li></button>
                  </div>                  
                </div>
              </div>
            </div>
          <div class="form-group col-sm-12 text-left" style="margin-top: 5px;">
            <div class="col-sm-6"> <!-- Inicio tabla y columna Inicio -->
              <div class="col-sm-12" style="border: #E9E9E9 solid 1px;border-radius: 5px;box-shadow:  1px 2px 2px 2px darkgray;">                  
                <div class="form-group">
                  <label class="control-label col-sm-1" style="font-size: 14px; font-weight: bold;">Origen</label>
                </div>
                <div class="form-group" style="margin-top: -15px">
                  <label for="tabOrig" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Tabla:</label>                    
                  <?php 
                  $sqlTablaOrigen = "SHOW FULL TABLES";
                  $tablaOrigen = $mysqli->query($sqlTablaOrigen);
                  ?>
                  <input type="hidden" id="tabOrigOcul">
                  <div id="divTabOrg">
                    <select name="tabOrig" id="tabOrig" onchange="javascript: consultar('Orig','Origen');" class="form-control  habilita input-sm col-sm-1 select2_single" title="Tabla Origen" style="width: 377.25px" required>
                      <option value="">Tabla</option>
                      <?php 
                      while($rowTO = mysqli_fetch_row($tablaOrigen)) {
                        echo '<option value="'.$rowTO[0].'">'.ucwords(mb_strtolower($rowTO[0])).'</option>';
                      }
                      ?>
                    </select> 
                  </div>                    
                </div>
                <div class="form-group" style="margin-top: -10px">                    
                  <label for="colOrg" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Columna:</label>                    
                  <select name="colOrg" id="colOrg" class="form-control input-sm habilita col-sm-1" title="Columna Origen" style="width: 377.25px" required>
                    <option value="">Columna</option>
                  </select>                    
                </div>
                <div class="form-group" style="margin-top: -10px;margin-bottom: 2px">                    
                  <label for="consultaOrigen" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Consulta Origen:</label>
                  <textarea class="col-sm-9 habilita" name="consultaOrigen" id="consultaOrigen" spellcheck="false" placeholder="Consulta Origen" style="padding: 2px; font-size: 12px; height: 50px;border-radius: 5px"  maxlength="9000"></textarea>
                  <div class="col-sm-1"> 
                    <table>
                      <tr>
                        <td>
                          <a class="glyphicon glyphicon-ok habilita" onclick="javascript: generaCons('Origen');" style="display:inline-block;margin-left:0px; font-size:100%; vertical-align: middle; text-decoration: none; cursor: pointer;" title="Generar Consulta Origen"></a>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <a class="glyphicon glyphicon-remove habilita" onclick="javascript: limpiar('Origen');" style="display:inline-block;margin-left:0px; font-size:100%; vertical-align: middle; text-decoration: none; cursor: pointer;" title="Borrar"></a>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
            </div>
          </div> <!-- Cierra Tabla y Columna Origen -->
          <div class="col-sm-6 form-group text-left" style="border: #E9E9E9 solid 1px;border-radius: 5px;box-shadow:  1px 2px 2px 2px darkgray;left: 25px"> <!-- Inicio tabla y Columna Derecha Destino -->              
            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label col-sm-1" style="font-size: 14px; font-weight: bold;">Destino</label>
              </div>
              <div class="form-group" style="margin-top: -15px">
                <label for="tabDes" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Tabla:</label>                  
                <?php
                $sqlTablaDestino = "SHOW FULL TABLES";
                $tablaDestino = $mysqli->query($sqlTablaDestino);
                ?>
                <input type="hidden" id="tabDesOcul">
                <select name="tabDes" id="tabDes" onchange="javascript: consultar('Des','Destino');" class="form-control  habilita input-sm col-sm-1 select2_single" title="Tabla Destino" style="width: 377.25px" required>
                  <option value="">Tabla</option>
                  <?php 
                  while($rowTD = mysqli_fetch_row($tablaDestino)){
                    echo '<option value="'.$rowTD[0].'">'.ucwords(mb_strtolower($rowTD[0])).'</option>';
                  }
                  ?>
                </select> 
              </div>
              <div class="form-group" style="margin-top: -10px">
                <label for="colDes" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Columna:</label>                
                <select name="colDes" id="colDes" class="form-control  habilita input-sm col-sm-1" title="Columna Destino" style="width: 377.25px" required>
                  <option value="">Columna</option>
                </select>
              </div>
              <div  class="form-group" style="margin-top: -10px;margin-bottom: 2px">                    
                <label for="consultaDestino" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Consulta Destino:</label>
                  <textarea class="col-sm-9 habilita" name="consultaDestino" id="consultaDestino" spellcheck="false" placeholder="Consulta Destino" style="padding: 2px; font-size: 12px; height: 50px;border-radius: 5px"  maxlength="9000"></textarea>
                  <div class="col-sm-1">
                    <table>
                      <tr>
                        <td>
                          <a class="glyphicon glyphicon-ok habilita" onclick="javascript: generaCons('Destino');" style="display:inline-block;margin-left:0px; font-size:100%; vertical-align: middle; text-decoration: none;  cursor: pointer;" title="Generar Consulta Destino"></a>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <a class="glyphicon glyphicon-remove habilita" onclick="javascript: limpiar('Destino');" style="display:inline-block;margin-left:0px; font-size:100%; vertical-align: middle; text-decoration: none;  cursor: pointer;" title="Borrar"></a>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>              
          </div> <!-- Fin tabla y Columna Derecha Destino -->
          <input type="hidden" name="MM_insert" >
        </form>
      </div>
      <!--  </div>   Termina -->
      <input type="hidden" id="posicion"> <!-- Posición del cursor en el textarea. -->
      <input type="hidden" id="sombra"> <!-- Sombra original del textarea. -->
      <input type="hidden" id="borde"> <!-- Borde original del textarea. -->
      <input type="hidden" id="errorMysql"> <!-- Error del la consulta SQL -->
      <input type="hidden" id="inpLugar"> <!-- Error del la consulta SQL -->
      <input type="hidden" id="habilitado"> <!-- Verifica si el contenido está habilitado para edición -->
      <input type="hidden" id="idTablaHomol"> <!-- El id a modificar en la tabla  -->
      <input type="hidden" id="elemento" value=""> <!-- Id del elemento vacio que impide modificar -->
      <input type="hidden" id="nombreElemento" value=""> <!-- Nombre del elemento vacio que impide modificar -->
      <script type="text/javascript">
        $('#btnCancelarMod').click(function() {
          var opcion = '<option value="">Columna</option>';
          $('#tipoInforme').prop('disabled', false);
          $('#informe').prop('disabled', false);
          $('#btnModificarForm').css("display", "none");
          $('#btnCancelarMod').css("display", "none");
          $('#btnGuardarForm').css("display", "block");
          $('#tabOrig').val("");
          $('#select2-tabOrig-container').text("Tabla");
          $('#tabOrigOcul').val("");
          $("#colOrg").html(opcion).fadeIn();
          $('#consultaOrigen').val("");
          $('#tabDes').val("");
          $('#select2-tabDes-container').text("Tabla");
          $('#tabDesOcul').val("");
          $("#colDes").html(opcion).fadeIn();
          $('#consultaDestino').val("");
          $('#periodicidad').val('');
          $("#idTablaHomol").val("");
          $('#tablaBase').css("display", "block");
          //Inhabilitamos los campos de origen
          $("#tabOrig").attr('disabled',false);
          $("#colOrg").attr('disabled',false);
        });        
        $('#btnModificarForm').click(function() {
          if($('#tabOrigOcul').val() == "") {
            $("#elemento").val("tabOrig");
            $("#nombreElemento").val("Tabla Origen");
          } else if($("#colOrg").val() == "") {
            $("#elemento").val("colOrg");
            $("#nombreElemento").val("Columna Origen");
          } else if($('#consultaOrigen').val() == "") {
            $("#elemento").val("consultaOrigen");
            $("#nombreElemento").val("Consulta origen");
          } else if($('#tabDesOcul').val() == "") {
            $("#elemento").val("tabDes");
            $("#nombreElemento").val("Tabla Destino");
          } else if($("#colDes").val() == "") {
            $("#elemento").val("colDes");
            $("#nombreElemento").val("Columna Destino");
          } else if($("#consultaDestino").val() == "") {
            $("#elemento").val("consultaDestino");
            $("#nombreElemento").val("Consulta Destino");
          } else if($("#periodicidad").val() == "") {
            $("#elemento").val("periodicidad");
            $("#nombreElemento").val("Periodicidad");
          }
          if($("#elemento").val() != "") {
            var elem = $("#nombreElemento").val();
            $("#spnElemento").text(elem);
            $("#mdlFaltaModif").modal('show');
          }else{
            modificando();
          }
        });        
        function modificando(){
          var idTablaHomol = $('#idTablaHomol').val();
          var tabOrig  = $('#tabOrigOcul').val();
          var colOrg  = $('#colOrg').val();
          var tabDes  = $('#tabDesOcul').val();
          var colDes  = $('#colDes').val();
          var informe  = $('#informe').val();
          var periodicidad  = $('#periodicidad').val();
          var select_table_origen  = $('#consultaOrigen').val();
          var select_table_destino  = $('#consultaDestino').val();
          var informe = $("#informe").val();
          var form_data = { estruc: 15, idTablaHomol :idTablaHomol, tabOrig: tabOrig, colOrg: colOrg, tabDes: tabDes, colDes: colDes, informe: informe, periodicidad: periodicidad, select_table_origen: select_table_origen, select_table_destino: select_table_destino };  
          $.ajax({
          type: "POST",
          url: "estructura_gestor_informes.php",
          data: form_data,
          success: function(response) {
            if(response == 1) {
              $('#tablaBase').css("display", "block"); 
              llenarTab(); // Aquí.
              $("#mdlModificarExito").modal('show');
              $("#tabOrig").attr('disabled',false);
              $("#colOrg").attr('disabled',false);
            } else if(response == 2) {
              $("#mdlErrorModificar").modal('show');
            } else if(response == 3){
              $("#mdlErrorCombina").modal('show');
            }
          }//Fin succes.
          }); //Fin ajax.
        }
        function limpiar(lugar) {
          var aLimpiar = "consulta" + lugar;
          $("#"+aLimpiar).val("");
        }          
        $(document).ready(function() {
          $("#consultaOrigen").blur(function() {
            limpTexArea('Origen');
          });
        });          
        $(document).ready(function() {
          $("#consultaDestino").blur(function() {
            limpTexArea('Destino');
          });
        });          
        $(document).ready(function() {
          $("#consultaOrigen").keypress(function() {
            if($("#sombra").val() != "") {
              limpTexArea('Origen');
            }
          });
        });        
        $(document).ready(function() {
          $("#consultaDestino").keypress(function() {
            if($("#sombra").val() != "") {
              limpTexArea('Destino');
            }
          });
        });
        function generaCons(lugar) {
          if(!$("#consulta"+lugar).is(':disabled')) {
            if($("#consulta"+lugar).val() != "") {
              $("#inpLugar").val(lugar);
              var consulta = $("#consulta"+lugar).val();
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
              if(no_sen == 1) {
                $("#posicion").val(posicion);
                $("#mdlErrorCon").modal('show');            
              } else {
                console.log(consulta);
                var form_data = { estruc: 11, consulta: consulta };  
                $.ajax({
                  type: "POST",
                  url: "estructura_gestor_informes.php",
                  data: form_data,
                  success: function(response) {
                      console.log(response);
                    if(response == 1) {
                      llamarModal(lugar)
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
          }
        }
        function llamarModal(lugar) {
          var consulta = $("#consulta"+lugar).val();
          var form_data = {                           
            consulta : consulta
          };
          $.ajax({
            type: 'POST',
            url: "modal_tabla_consulta.php#mdlTablaConsulta",
            data:form_data,
            success: function (data) { 
              $("#mdlTablaConsulta").html(data);
              $(".mov").modal('show');
            }
          });
        }
        function consultar(tabla, consulta) {
          var laTabla = $("#tab"+tabla).val();
          var oculto = "tab" + tabla + "Ocul";
          $("#"+oculto).val(laTabla);
          if($("#tab"+tabla).val() != "") {
            var form_data = { estruc: 10, laTabla: laTabla };  
            $.ajax({
            type: "POST",
            url: "estructura_gestor_informes.php",
            data: form_data,
            success: function(response) {
              response = response.trim();
              $("#consulta"+consulta).val(response);                           
            }//Fin succes.
            }); //Fin ajax.
          } else {
            $("#consulta"+consulta).val("");
          }
        }      
        $("#tabOrig").change(function() {
          var origen = '<option value="">Columna</option>';
          if($("#tabOrig").val() != "") {
            var tabla = $("#tabOrig").val();
            var form_data = { estruc: 1, tabla: tabla };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
              data: form_data,
              success: function(response) {
                origen += response;
                $("#colOrg").html(origen).fadeIn();
                $("#colOrg").focus();                             
              }//Fin succes.
            }); //Fin ajax. 
          } else {
            $("#colOrg").html(origen).fadeIn();
          }
        });                
        $("#tabDes").change(function() {
          var origen = '<option value="">Columna</option>';
          if($("#tabDes").val() != "") {
            var tabla = $("#tabDes").val();
            var form_data = { estruc: 1, tabla: tabla };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
              data: form_data,
              success: function(response) {
                origen += response;
                $("#colDes").html(origen).fadeIn();
                $("#colDes").focus();                           
              }//Fin succes.
            }); //Fin ajax.
          } else {
            $("#colDes").html(origen).fadeIn();
          }
        });
        $(document).ready(function() {
          habilita();
        });
        function habilita() {
          if($("#informe").val() != "" && $("#periodicidad").val() != "") {
            $(".habilita").prop("disabled", false);
            $('#habilitado').val(1);
          } else {
            $(".habilita").prop("disabled", true);
            $('#habilitado').val(2);
          }
        }
      </script>
      <div class="form-group col-sm-10" style="margin-top: -20px;margin-bottom: -15px">
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tablaBase" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td style="display: none;">Identificador</td> 
                  <td width="30px" align="center"></td> 
                  <td><strong>Tabla Origen</strong></td>
                  <td><strong>Columna Origen</strong></td>
                  <td><strong>Tabla Destino</strong></td>
                  <td><strong>Columna Destino</strong></td>
                  <td><strong>Periodicidad</strong></td>
                </tr>
                <tr>
                  <th style="display: none;">Identificador</th> 
                  <th width="7%"></th>
                  <th>Tabla Origen</th>
                  <th>Columna Origen</th>
                  <th>Tabla Destino</th>
                  <th>Columna Destino</th>
                  <th>Periodicidad</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>    
          </div>
        </div> <!-- table-responsive -->
      </div>
    </div> <!-- col-sm-10 text-left -->
  </div> <!-- row content -->
</div> <!-- container-fluid text-center -->
<script type="text/javascript">
  $(document).ready(function() {
    $("#informe").change(function() {
      llenarTab();                    
    });
  });
  // Crea dinámicamente la tabla tablaBase con los datos pertenicientes al informe seleccionado en el combo-select informe.
  function llenarTab() {
    var id;
    var informe;
    if($("#informe").val() != "") {
      informe = $("#informe").val();
    } else {
      informe = 0;
    }
    /*
    Nótese que en el apartado columns hay una columna que dice defaultContent. En esta columna se definen los links para eliminar y modificar 
    los registros de la tabla. En el evento onclick de cada etiqueta a hay una función dentro de un setTimeOut a cien milisegundos. Esto se 
    debe a un error que no se pudo corregir: Al seleccionar el registro en la función tomarIdClickTr() no toma el en todos los casos el 
    id de la data retornada por la consulta. Para solucionarlo, se toma el id al hacer click, el id va a un input oculto (idDetalleInforme) y posteriormente 
    se ejecuta la función que está dentro del setTimeOut().
    */
    var form_data = { estruc: 9, informe: informe };  
    var idll = new Array();
    $("#tablaBase").dataTable().fnDestroy(); 
    var table = $("#tablaBase").DataTable({
      "autoFill": true,
      "scrollX": true,
      "pageLength": 5,
      "language": {
        "lengthMenu": "Mostrar _MENU_ registros",
        "zeroRecords": "No Existen Registros...",
        "info": "Página _PAGE_ de _PAGES_ ",
        "infoEmpty": "No existen datos",
        "infoFiltered": "(Filtrado de _MAX_ registros)",
        "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
        },
      'columnDefs': [{
        'targets': 0,
        'searchable':false,
        'orderable':false
      }],
      "ajax": {
        "method": "POST",
        "url": "estructura_gestor_informes.php",
        "data": form_data,
        "cache": false
      },
      rowId: 'id',
      "columns":[
        {"data": "id", "bVisible": false},
        {"defaultContent": "<a class=\"campos\" onclick=\"setTimeout(eliminarTablaHomologable, 100);\" style=\"cursor: pointer\" ><i title=\"Eliminar\" class=\"glyphicon glyphicon-trash\"></i></a><a class=\"campos\" onclick=\"setTimeout(aModificar, 100);\" style=\"cursor: pointer\" > <i title=\"Modificar\" class=\"glyphicon glyphicon-edit\" ></i></a>"},
        {"data": "tabla_origen"},
        {"data": "columna_origen"},
        {"data": "tabla_destino"},
        {"data": "columna_destino"},
        {"data": "nombre"}
      ]
    });
    var i = 0;
    table.columns().every( function () {
      var that = this;
      if(i!=0){
        $( 'input', this.header() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
              .search( this.value )
              .draw();
          }
        } );
      i = i+1;
      } else {
        i = i+1;
      }
    });
    tomarIdClickTr(); // Acá asigna el id del registro seleccionado en la tabla tablaBase.
  }
  // Toma el id del registro seleccionado en evento click del tr de la tabla.
  function tomarIdClickTr() {
    var table = $('#tablaBase').DataTable(); 
    $('#tablaBase').on( 'click', 'tr', function () {
      var id = table.row( this ).id();
      id = parseInt(id);
      if(!isNaN(id)) {
        $("#idDetalleInforme").val(id);
      }
    });
  }
  // Guardar en la tabla gn_tabla_homologable.
  function guardar() {
    var tabOrig  = $('#tabOrig').val();
    var colOrg  = $('#colOrg').val();
    var tabDes  = $('#tabDes').val();
    var colDes  = $('#colDes').val();
    var informe  = $('#informe').val();
    var periodicidad  = $('#periodicidad').val();
    var select_table_origen  = $('#consultaOrigen').val();
    var select_table_destino  = $('#consultaDestino').val();
    var tipoInforme = $('#tipoInforme').val();
    var informe = $("#informe").val();
    var form_data = { estruc: 12, tipoInforme:tipoInforme, tabOrig: tabOrig, colOrg: colOrg, tabDes: tabDes, colDes: colDes, informe: informe, periodicidad: periodicidad, select_table_origen: select_table_origen, select_table_destino: select_table_destino };  
    $.ajax({
      type: "POST",
      url: "estructura_gestor_informes.php",
      data: form_data,
      success: function(response) {
         console.log(response);
        if(response == 1) {
          llenarTab();
          $("#mdlGuardarExito").modal('show');
        } else if(response == 2) {
          $("#mdlErrorGuardar").modal('show');
        } else if(response == 3) {
          $("#mdlErrorCombina").modal('show');
        }
      }//Fin succes.
    }); //Fin ajax.
  }
</script>
<!-- select2 -->
<script src="js/select/select2.full.js"></script>
<script>
  $(document).ready(function() {
    $(".select2_single").select2({
      allowClear: true
    });
  });
  $(document).ready(function() {
    var tabOrigen = $("#tabOrig").val();
    $("#tabOrigOcul").val(tabOrigen);

    var tabDestino = $("#tabDes").val();
    $("#tabDesOcul").val(tabDestino);
  });
</script>
<div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>¿Desea eliminar el registro seleccionado de Configuración Base?</p>
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
<div class="modal fade" id="mdlGuardarExito" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuardarExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorGuardar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorGuardar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlModificarExito" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información modificada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModificarExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorModificar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorModificar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlErrorCombina" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La combinación tabla es invalida. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorCombina" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlFaltaModif" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El campo <span id="spnElemento"></span> se encunetra vacío. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnFaltaModif" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<?php require_once ('modal_tabla_consulta.php'); ?>
<?php require_once ('footer.php'); ?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
  function eliminarTablaHomologable() {
    var id = $("#idDetalleInforme").val()
    id = parseInt(id);
    if(!isNaN(id)) {
      $("#myModal").modal('show');
      $("#ver").click(function(){
      $("#mymodal").modal('hide');
      var form_data = { estruc: 13, id: id };  
      $.ajax({
        type: "POST",
        url: "estructura_gestor_informes.php",
        data: form_data,
        success: function(response) {
          if(response == 1) {
            //remover(id);
            llenarTab();
            $("#myModal1").modal('show');
          } else if(response == 2) 
              $("#myModal2").modal('show');
          }//Fin succes.
        }); //Fin ajax.
      });
    }
  }
  function aModificar() {
    var id = $("#idDetalleInforme").val();
    id = parseInt(id);
    if(!isNaN(id)) {
      if($('#habilitado').val() != 2) {
        var form_data = { estruc: 14, id: +id };  
        $.ajax({
        type: "POST",
        url: "estructura_gestor_informes.php",
        data: form_data,
        success: function(response) {
          $('#tipoInforme').prop('disabled', true);
          $('#informe').prop('disabled', true);
          $('#btnGuardarForm').css("display", "none");
          $('#btnModificarForm').css("display", "block");
          $('#btnCancelarMod').css("display", "block");
          var arrTabHom = response.split("|");
          var tabOrig = arrTabHom[0];
          tabOrig = tabOrig.trim();
          var colOrg = arrTabHom[1];
          var consultaOrigen = arrTabHom[2];
          var tabDes = arrTabHom[3];
          var colDes = arrTabHom[4];
          var consultaDestino = arrTabHom[5];
          var periodicidad = arrTabHom[6];
          periodicidad = parseInt(periodicidad);
          $('#tabOrig > option[value="' + tabOrig + '"]').attr('selected', 'selected');
          $('#select2-tabOrig-container').text(tabOrig);
          $('#tabOrigOcul').val(tabOrig);
          columnasOrigen(tabOrig, colOrg);
          $('#consultaOrigen').val(consultaOrigen);
          $('#tabDes > option[value="' + tabDes + '"]').attr('selected', 'selected');
          $('#select2-tabDes-container').text(tabDes);
          $('#tabDesOcul').val(tabDes);
          columnasDestino(tabDes, colDes)
          $('#consultaDestino').val(consultaDestino);
          $("#periodicidad").val(periodicidad);
          $("#idTablaHomol").val(id);
          $('#tablaBase').css("display", "none");  
          //Inhabilitamos los campos de origen
          $("#tabOrig").attr('disabled',true);
          $("#colOrg").attr('disabled',true);
          }//Fin succes.
        }); //Fin ajax.
      }
    }
  }
  function columnasOrigen(tabOrig, colOrg) {
    var origen = '<option value="">Columna</option>';
    var form_data = { estruc: 1, tabla: tabOrig };  
    $.ajax({
      type: "POST",
      url: "estructura_gestor_informes.php",
      data: form_data,
      success: function(response) {
        origen += response;
        $("#colOrg").html(origen).fadeIn();
        $('#colOrg > option[value="' + colOrg + '"]').attr('selected', 'selected');                      
      }//Fin succes.
    }); //Fin ajax.
  }
  function columnasDestino(tabDes, colDes) {
    var origen = '<option value="">Columna</option>';
    var form_data = { estruc: 1, tabla: tabDes };  
    $.ajax({
      type: "POST",
      url: "estructura_gestor_informes.php",
      data: form_data,
      success: function(response) {
      origen += response;
        $("#colDes").html(origen).fadeIn();
        $('#colDes > option[value="' + colDes + '"]').attr('selected', 'selected'); 
      }//Fin succes.
    }); //Fin ajax. 
  }
  function modal() {
    $("#myModal").modal('show');
  }
  function remover(id) {
    $('#tr'+id).remove();
  }
  $('#ver1').click(function(){});
  $('#ver2').click(function(){});
  $('#btnErrorCon').click(function() {
    var caja = $("#inpLugar").val();
    caja = caja.trim();
    var lugar = 'consulta' + caja;
    $('#'+lugar).focus();
    var posicion = $("#posicion").val();
    ponCursorEnPos(posicion);
    var sombra =  $("#"+lugar).css("box-shadow");
    var borde =  $("#"+lugar).css("border");
    $("#sombra").val(sombra);
    $("#borde").val(borde);
    $("#"+lugar).css("box-shadow", "0 0 2px rgba(255,0,0,1)"); //estaba 5px
    $("#"+lugar).css("border", "1px solid rgba(255,0,0,0.8)"); 
  });
  function ponCursorEnPos(pos) {
    var caja = $("#inpLugar").val();
    caja = caja.trim();
    var lugar = 'consulta' + caja;
    laCaja = document.getElementById(lugar);
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
      //método estándar
    }  else if(typeof laCaja.selectionStart != 'undefined') {                    
      laCaja.setSelectionRange(pos,pos);  
    } 
  }
  function limpTexArea(lugar) {
    var sombra = $("#sombra").val();
    var borde = $("#borde").val();
    $("#consulta"+lugar).css("box-shadow", sombra);
    $("#consulta"+lugar).css("border", borde);
    $("#sombra").val("");
    $("#borde").val("");
  }
  function asignar() {
    var error = $("#errorMysql").val();
    $("#errorMsql").text(error);
  }
  $('#btnErrorMysql').click(function() {
    var lugar = $("#inpLugar").val();
    $('#consulta'+lugar).focus();
  });
  $("#mdlTablaConsulta").on('shown.bs.modal',function(){
    var dataTable = $("#tablaCon").DataTable();
    dataTable.columns.adjust().responsive.recalc();
  });  
  $('#btnErrorMysql').click(function() {
    var lugar = $("#inpLugar").val();
    $('#consulta'+lugar).focus();
  });
  $('#btnFaltaModif').click(function() {
    var elem = $("#elemento").val();
    $('#'+elem).focus();
    $("#elemento").val("");
    $("#nombreElemento").val("");
  });
  $('#btnGuardarExito').click(function() {
    limpiarForm();
  });
  $('#btnModificarExito').click(function() {
    $('#tipoInforme').prop('disabled', false);
    $('#informe').prop('disabled', false);
    $('#btnModificarForm').css("display", "none");
    $('#btnCancelarMod').css("display", "none");
    $('#btnGuardarForm').css("display", "block");
    $("#idTablaHomol").val("");
    limpiarForm();
  });
  function limpiarForm() {
    var opcion = '<option value="">Columna</option>';
    $('#tabOrig').val("");
    $('#select2-tabOrig-container').text("Tabla");
    $('#tabOrigOcul').val("");
    $("#colOrg").html(opcion).fadeIn();
    $('#consultaOrigen').val("");
    $('#tabDes').val("");
    $('#select2-tabDes-container').text("Tabla");
    $('#tabDesOcul').val("");
    $("#colDes").html(opcion).fadeIn();
    $('#consultaDestino').val("");
  }
</script>
</body>
</html>