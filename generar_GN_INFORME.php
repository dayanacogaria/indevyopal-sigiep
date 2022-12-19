<?php
#####################################################################################################################################
#                                                                                   Modificaciones
######################################################################################################################################
#27/07/2017 | Erica González | Encabezados de los informes
######################################################################################################################################
	require_once('Conexion/conexion.php');
  require_once 'head_listar.php';

  $queryInforme = "";
  $resultado = "";
  $num_col = 0;
  $id = 0;
  $tipoInf = 0;

  if(!empty($_SESSION['consulta_gi'])) {
    $miarray = $_SESSION['columnas_gi'];

    $array_para_recibir_via_url = stripslashes($miarray);
    $columnas_gi = unserialize($array_para_recibir_via_url);
    $num_col = count($columnas_gi);

    $queryInforme = $_SESSION['consulta_gi'];
    $resultado = $mysqli->query($queryInforme);

    $id = $_SESSION['id_gi'];
    $tipoInf = $_SESSION['tipoInf_gi'];
  }

	$sqlInforme = "SELECT id, nombre
  FROM gn_tipo_informe
  ORDER BY nombre ASC";
	$informe = $mysqli->query($sqlInforme);
 #********************************************************************************************************************************************
 #Datos encabezado
 #Código Compañia
$codigoc ="";
$cc="SELECT codigo_dane FROM gf_tercero WHERE id_unico =".$_SESSION['compania'];
$cc = $mysqli->query($cc);
if(mysqli_num_rows($cc)>0){
    $cc = mysqli_fetch_row($cc);
    if($cc[0]==""){
        $codigoc = "";
    } else {
    $codigoc = $cc[0];
    }
}
#Nombre del informe a mostrar en encabezado
$nI="";
$nI = "SELECT nombre_informe FROM gn_informe WHERE id =".$id;
$nI = $mysqli->query($nI);
if(mysqli_num_rows($nI)>0){
    $nI = mysqli_fetch_row($nI);
    $nI = $nI[0];
} else {
    $nI="";
}
#Fecha Meses Encabezado
$fechaEncabezado ="";
if(isset($_SESSION['fechaI']) && isset($_SESSION['fechaF'])){
   $fi= $_SESSION['fechaI'];
   $fecha_divI = explode("-", $fi);
   $mesI = $fecha_divI[1];
   $ff= $_SESSION['fechaF'];
   $fecha_div = explode("-", $ff);
   $mesF = $fecha_div[1];
  if($tipoInf==6){
    $fechaEncabezado ='1'.$mesF.$mesF;
  } else {
    $fechaEncabezado ='1'.$mesI.$mesF;
  }
}
#Año
$parmanno = $_SESSION['anno'];
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0];
#********************************************************************************************************************************************
?>
	<title>Generar Informe</title>
  <style type="text/css">
    .area{ height: auto !important;}
    /*Esto permite que el texto contenido dentro del div
    no se salga de las medidas del mismo.*/
    .acotado{white-space: nowrap;}
    table.dataTable thead th,table.dataTable thead td {padding: 1px 18px;font-size: 10px;}
    table.dataTable tbody td,table.dataTable tbody td{padding: 1px;}
    .dataTables_wrapper .ui-toolbar{padding: 2px;font-size: 10px;}
    .control-label{font-size: 12px;}
    .itemListado{margin-left: 5px;margin-top: 5px;width: 150px;cursor: pointer;}
    #listado {width: 150px;height: 80px;overflow: auto;background-color: white;}
    .cursor{cursor: pointer;}
		/*Copia de los estilos basicoas de selección para select*/
    select:hover{outline: thin dotted;outline: 5px auto -webkit-focus-ring-color;outline-offset: -2px;}
    select:focus {
      border-color: #66afe9;
      outline: 0;
      -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
      box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
    }

	#WindowLoad{
	  position:fixed;
	  top:0px;
	  left:0px;
	  z-index:3200;
	  filter:alpha(opacity=80);
	  -moz-opacity:80;
	  opacity:0.80;
	  background:#FFF;
	}
	</style>
  <script>
    <?php if(!empty($_SESSION['consulta_gi'])) { ?>
      $(document).ready(function(){
        $("#btnModificar,#btnGenerar").attr('disabled',false);
        $("#btnGuardar").attr('disabled',true);
      });
    <?php }else{ ?>
      //cuando se inicie el formulario el boton modificar este inhabilitado
      $(document).ready(function(){
        $("#btnModificar,#btnGenerar").prop('disabled', true);
        $("#btnGuardar").attr('disabled',false);
      });
    <?php } ?>
  </script>
</head>
<body>
  <input type="hidden" id="idNombre" value="<?php echo $id;?>">
  <input type="hidden" id="idTipoInf" value="<?php echo $tipoInf;?>">
  <input type="hidden" id="queryInforme" value="<?php echo $queryInforme;?>">
  <input type="hidden" id="fechaEncabezado" value="<?php echo $fechaEncabezado?>">
  <input type="hidden" id="codigodane" value="<?php echo $codigoc;?>">
  <input type="hidden" id="nomInforme" value="<?php echo $nI;?>">
  <input type="hidden" id="fechaEncabezado" value="<?php echo $fechaEncabezado;?>">
  <input type="hidden" id="anno" value="<?php echo $anno;?>">
  
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once 'menu.php';?>
      <div class="col-sm-10 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 0px; margin-right: 4px; margin-left: 4px;width: 100%">Generar Informe</h2>
        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: 5px;" class="client-form col-sm-12">
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificarConsulta();">
            <p align="center" style="margin-bottom: 8px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <div class="form-group">
              <label for="tipoInf" class=" control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Tipo Informe:</label>
              <select name="tipoInf" id="tipoInf" class="form-control col-sm-1 select2 cursor" title="Tipo Informe" style="width: 20%;" required>
                <option value="">Tipo Informe</option>
                <?php
                  while($row = mysqli_fetch_row($informe)) {
                    echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
                  }
                ?>
              </select>
              <label for="nombre" class=" control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Informe:</label>
              <select name="nombre" id="nombre" class="form-control col-sm-1 select2 cursor" title="Informe" style="width: 20%;" required>
                <option value="">Informe</option>
              </select>
              <div class="col-sm-1">
                  <button type="button" id="btnEjecutar" name="btnEjecutar" title="Ejecutar" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><span class="glyphicon glyphicon-play"></span></button>
              </div>
						</div>
						<div class="form-group" style="margin-top:-15px">
              <label class="control-label col-sm-2">Generar a :</label>
              <select class="col-sm-1 cursor" style="width: 20%" id="sltTipoArchivo" onchange="separador()" name="sltTipoArchivo" class="form-control" title="Tipo de archivo a generar">
                <option value="">Generar a..</option>
                <option value="1">.csv</option>
                <option value="2">.xls</option>
                <option value="3">.txt</option>
                <!--<option value="4">.xml</option>-->
              </select>
							<label class="control-label col-sm-2">Separador :</label>
              <select class="col-sm-1 cursor" style="width: 20%" id="sltSeparador" name="sltSeparador" class="form-control" title="Separador de filas cuando el tipo de archivo a generar es csv o txt">
                <option value="">Separador..</option>
                <option value=",">,</option>
                <option value=";">;</option>
                <option value="tab">tabulador</option>
              </select>
              <input type="hidden" name="consulta" id="consulta">
              <div class="col-sm-1">
                <button type="button" id="btnGenerar" name="btnGenerar" class="btn btn-primary" title="Generar" onclick="return archivo()" style="width: 40px;box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><span class="glyphicon glyphicon-save-file"></span></button>
              </div>
          	</div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>
        <script type="text/javascript">
          $("#tipoInf").change(function() {
            $("#consulta").val("");
            var opcion = '<option value="">Informe</option>';
            if($("#tipoInf").val() == "") {
              $("#nombre").html(opcion).fadeIn();
            } else {
              var tipoInf = $("#tipoInf").val();
              var form_data = { estruc: 5, tipoInf: tipoInf };
              $.ajax({
                type: "POST",
                url: "estructura_gestor_informes.php",
                data: form_data,
                success: function(response) {
                  if(response != "") {
                    opcion += response;
                    $("#nombre").html(opcion).fadeIn().focus();
                  } else {
                    opcion = '<option value="">No hay Informe</option>';
                    $("#nombre").html(opcion).fadeIn();
                  }
                }//Fin succes.
              }); //Fin ajax.
            }
          });

        $("#nombre").change(function(){
          $("#consulta").val("");
          if($("#nombre").val() != ""){
            var nombre = $("#nombre").val();
            var form_data = { estruc: 6, nombre: nombre };
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
              data: form_data,
              success: function(response) {
                response = response.trim();
                if(response != "") {
                  $("#consulta").val(response).focus();
                } else {
                  $("#consulta").attr("placeholder", "Este informe no tiene consulta.").focus();
                }
                $("#btnModificar,#btnGenerar").prop('disabled', false);
                $("#btnGuardar").attr('disabled',true);
              }//Fin succes.
            }); //Fin ajax.
          }
        });

        $("#tipoInf").on("change", function() {
          placeConsulta();
        });

        $("#nombre").on("change", function() {
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
              consulta =  consulta;
              var no_sentencias =['delete', 'update', 'insert',  'alter',  'truncate', 'mysql', 'show', 'databases',  'optimize', 'grant', 'revoke', 'flush', 'explain', ' tables', 'lock', 'set', 'start', 'analyze', 'check', 'handler', 'kill', 'load ', 'reset '];
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
                var informe = $("#nombre").val();
                var form_data = { session: 1, informe: informe };
                $.ajax({
                  type: "POST",
                  url: "consultasBasicas/estructura_generar_consultas_informes.php",
                  data: form_data,
                  success: function(data,textStatus,jqXHR) {
                    periodicidad = parseInt(data);
										if(!isNaN(periodicidad)) {
											open_window_p(periodicidad,informe);
										}else{
											open_window_p(1,informe);
										}
                  }
              }).error(function(data,textError,jqXHR){
								console.log('Data : '+data+', textError: '+textError+', jqXHR :'+jqXHR);		//Imprimimos por consola
								alert('Data : '+data+', textError: '+textError+', jqXHR :'+jqXHR);					//Alertamos
							});
              }
            }
          });

					function open_window_p(id_p,informe){
						var form_data = {id_p:id_p,informe:informe};
						$.ajax({
							type:'POST',
							url:'modal_periodicidad.php#modalPeriodo',
							data:form_data,
							success: function(data,textStatus,jqXHR){
								$("#modalPeriodo").html(data);
								$(".periodo").modal("show");
							}
						}).error(function (data,textError,jqXHR){
							alert('Error :' + textError);
						});
					}
			</script>
      <script>
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
            }
            //método estándar
            else if(typeof laCaja.selectionStart != 'undefined')   {
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
            var id = $("#nombre").val();
            var consulta = $("#consulta").val();
            var form_data = { estruc: 7, id: id, consulta: consulta };
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
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
      </div>
      <div class="col-sm-10 table-responsive text-left" style="margin-top: 5px;">
        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>
              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <?php
                  for ($i = 0; $i < $num_col; $i++) {
                    echo '<td><strong>'.ucwords($columnas_gi[$i]).'</strong></td>';
                  }
                ?>
              </tr>
              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <?php
                  for ($i = 0; $i < $num_col; $i++) {
                    echo '<th>'.ucwords($columnas_gi[$i]).'</th>';
                  }
                ?>
              </tr>
            </thead>
            <tbody>
              <?php
                if(!empty($resultado)) {
                while($row = mysqli_fetch_row($resultado)) {
              ?>
               <tr>
                <td style="display: none;"> </td>
                <td> </td>
                <?php
                  for ($i = 0; $i < $num_col; $i++)  {
                    echo '<td><div class="acotado">'.(ucwords(mb_strtolower($row[$i]))).'</div></td>';
                  }
                ?>
              </tr>
              <?php
               }
             }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php  require_once 'footer.php';?>
<?php
if(!empty($_SESSION['consulta_gi'])) {
?>
  <script type="text/javascript">
    $(document).ready(function() {
      var idTipoInf = $("#idTipoInf").val();
      var idNombre = $("#idNombre").val();
      var consulta = $("#queryInforme").val();
      $('#tipoInf > option[value="' + idTipoInf + '"]').attr('selected', 'selected');
      $("#consulta").val(consulta);
      $("#btnModificar,#btnGenerar").prop('disabled', false);//Inhabilitamos los botones de modificar y generar
      $("#btnGuardar").attr('disabled',true);//Habilitamos el botón de guardado
      $("#sltSeparador").attr('disabled', true);//Inhabilitamos el campo separador
      var opcion = '<option value="">Informe</option>';
      var tipoInf = $("#tipoInf").val();
      var form_data = { estruc: 5, tipoInf: tipoInf };
      $.ajax({
        type: "POST",
        url: "estructura_gestor_informes.php",
        data: form_data,
        success: function(response) {
          opcion += response;
          $("#nombre").html(opcion).fadeIn();
          $('#nombre > option[value="' + idNombre + '"]').attr('selected', 'selected');
        }//Fin succes.
      }); //Fin ajax.
    });
  </script>
<?php
}
?>
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
        <button type="button" id="btnSinDatos" class="btn" style="color: #000; margin-top: 2px" onclick="cerrar_modal_i()" data-dismiss="modal">Aceptar</button>
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
        <button type="button" id="btnModConExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
<!--- MODALES DE MODIFICADO  -->
<div class="modal fade" id="mdlModifcado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información modificada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlNoMod" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido modificar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnNoMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<!-- modal de archivo creado -->
<div class="modal fade" id="modalArchivo" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -25px">
          <button type="button" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
        </div>
      </div>
      <div class="modal-body" style="margin-top: 5px">
        <label for="" class="control-label col-sm-12 text_center">El archivo ha sido creado correctamente</label>
        <a href="" id="path_file" name="path_file" download="" class="text-left">Descargar el archivo</a>
      </div>
      <div id="forma-modal" class="modal-footer">
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript" src="js/select2.js"></script>
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
  //Función para modificar
  function modificarInforme(){
    //Variable de envio de ajax
    var form_data = {
      id:$("#nombre").val(),
      sltTable:$("#consulta").val()
    }
    //envio ajax
    var result = '';
    $.ajax({
      type:'POST',
      url:'json/modificar_GN_INFORMEJson.php',
      data:form_data,
      success: function(data){
        result = JSON.parse(data);
        if (result === true) {
          $("#mdlModifcado").modal('show');
        }else{
          $("#mdlNoMod").modal('show');
        }
      }
    });
  }
  //función para crear archivo
  function archivo(){
    //Captura del valor del combo sltTipoArchivo
    var tipo = parseInt($("#sltTipoArchivo").val());
    if(!isNaN(tipo)){//Validación para verificar que tipo no sea nulo
      //Captura del valor del campo de consulta
      var consulta = $("#consulta").val();
      var informe = $("#nombre option:selected").text();
      var tinforme = $("#nombre").val();
      var codigod = $("#codigodane").val();
      var separador = $("#sltSeparador").val();
      var nomMostrar = $("#nomInforme").val();
      var fechaEncabezado = $("#fechaEncabezado").val();
      var anno = $("#anno").val();
      
      //Variable de envio como data del ajax
      var form_data = {
        tipoArchivo:tipo,
        consulta:consulta,
        nombreI:informe,
        separador:separador, 
        informeid:tinforme,
        codigodane:codigod,
        nomMostrar:nomMostrar,
        fechaEncabezado:fechaEncabezado,
        anno:anno
      };
       console.log(form_data);
      //Envio ajax
      $.ajax({
        type:'POST',
        url:'consultasBasicas/exportarArchivos.php',
        data:form_data,
        success: function(data,textStatus,jqXHR){
            console.log(data);
          if(data.indexOf(";")!== 0){
            var dato = data.split(";");
            var path = dato[1];
            $("#path_file").attr('href',path.substring(3));
            $("#modalArchivo").modal('show');
          }
        },error: function(data,textStatus,textError){
          alert('Error :' + textError);
        }
      });
    }
  }
  //Función para activar el campo separador
  function separador(){
    //Capturamos el valor del combo separador
    var tipo = parseInt($("#sltTipoArchivo").val());
    if(!isNaN(tipo)) {
      if (tipo==1 || tipo == 3){
        $("#sltSeparador").attr('disabled', false);
        $("#sltSeparador").focus();
      }else{
        $("#sltSeparador").attr('disabled', true);
      }
    }
  }

	function cerrar_modal_i(){
		$("#mdlSinDatos").modal('hide');
		window.location.reload();
	}
</script>
<?php
  if(!empty($_SESSION['consulta_gi'])) {
    unset($_SESSION['consulta_gi']);
    unset($_SESSION['columnas_gi']);
  }
	//Agregamos el modal de periodicidad
	require ('modal_periodicidad.php');
 ?>
</body>
</html>
