<?php
@session_start();
@require ('Conexion/conexion.php');
?>
<div class="modal fade periodo" id="modalPeriodo" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Periodicidad</h4>
        <div class="col-sm-offset-12" style="margin-top:-30px;">
            <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;margin-left: -25px;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
        </div>
      </div>
      <div class="modal-body row form-horizontal" style="margin-top: 8px">
        <?php
        $periodo = "";                          //Iniciamos la variable en vacio para recibir el valor
        $id_informe = "";
        if(!empty($_POST['id_p'])) {            //Validamos que la variable no este vacia
          $periodo = $_POST['id_p'];            //Recibimos la variable
          $param = $_SESSION['anno'];           //Capturamos el id de la parametrizacionannio actual
          $id_informe = $_POST['informe'];      //Id informe
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          //Hacemos un switch como receptor de la variable para validar que consulta o proceso debe realizar
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          switch ($periodo) {
            case 1:
              /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
              // Imprimimos el html para generar mostrar los campos
              /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
              echo "<div class=\"form-inline form-group\">";
              echo "<label class=\"control-label col-sm-4 select\">Año:</label>";
              echo "<select name=\"sltAnno\" id=\"sltAnno\" style=\"width:40%;padding:2px;font-size:10px;height:30px\" class=\"col-sm-1 form-control select cursor\" title=\"Seleccione el tipo de comprobante\" >\n";
              $sqlP = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param";
              $resultP = $mysqli->query($sqlP);
              $rowP = mysqli_fetch_row($resultP);
              echo "<option value=\"$rowP[0]\">$rowP[0]</option>";
              $sqlP = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico != $param ORDER BY anno ASC";
              $resultP = $mysqli->query($sqlP);
              while ($rowP = mysqli_fetch_row($resultP)) {
                echo "<option value=\"$rowP[0]\">$rowP[0]</option>";
              }
              echo "</select>\n";
              echo "</div>";
              break;
            case 2:
              echo "<div class=\"form-inline form-group\">";
              echo "<label class=\"control-label col-sm-4 select cursor\">Mes Inicial:</label>";
              echo "<select name=\"sltMesInicial\" id=\"sltMesInicial\" style=\"width:40%;padding:2px;font-size:10px;height:30px\" class=\"col-sm-1 form-control\" title=\"Seleccione el tipo de comprobante\" >\n";
              echo "<option value>Mes Inicial</option>";
              $sqlP = "SELECT     m.numero,m.mes,pan.anno
                      FROM        gf_parametrizacion_anno pan
                      LEFT JOIN   gf_mes m ON pan.id_unico = m.parametrizacionanno
                      WHERE       pan.id_unico = $param ORDER BY m.numero ASC";
              $resultP = $mysqli->query($sqlP);
              while ($rowP = mysqli_fetch_row($resultP)) {
                echo "<option value=\"$rowP[0]/$rowP[2]\">".$rowP[1]."</option>";
              }
              echo "</select>\n";
              echo "</div>";
              echo "<div class=\"form-inline form-group\">";
              echo "<label class=\"control-label col-sm-4 select cursor\">Mes Final:</label>";
              echo "<select name=\"sltMesFinal\" id=\"sltMesFinal\" style=\"width:40%;padding:2px;font-size:10px;height:30px\" class=\"col-sm-1 form-control\" title=\"Seleccione el tipo de comprobante\" >\n";
              echo "<option value>Mes Final</option>";
              $sqlP = "SELECT     m.numero,m.mes,pan.anno
                      FROM        gf_parametrizacion_anno pan
                      LEFT JOIN   gf_mes m ON pan.id_unico = m.parametrizacionanno
                      WHERE       pan.id_unico = $param ORDER BY m.numero DESC";
              $resultP = $mysqli->query($sqlP);
              while ($rowP = mysqli_fetch_row($resultP)) {
                echo "<option value=\"$rowP[0]/$rowP[2]\">".$rowP[1]."</option>";
              }
              echo "</select>\n";
              echo "</div>";
              break;
            case 3:
              echo "<div class=\"form-inline form-group\">";
              echo "<label class=\"control-label col-sm-4 select cursor\">Trimestre</label>";
              echo "<select name=\"sltTrimestre\" id=\"sltTrimestre\" style=\"width:40%;padding:2px;font-size:10px;height:30px\" class=\"col-sm-1 form-control\" title=\"Seleccione el tipo de comprobante\" >\n";
              echo "<option value=\"1\">Primer Trimestre</option>";
              echo "<option value=\"2\">Segundo Trimestre</option>";
              echo "<option value=\"3\">Tercer Trimestre</option>";
              echo "<option value=\"4\">Cuarto Trimestre</option>";
              echo "</select>\n";
              echo "</div>";
              break;
          }
        }
         ?>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnPeriodicidad" class="btn" style="color: #000; margin-top: 2px" onclick="send_table('<?php echo $periodo ?>','<?php echo $id_informe ?>')">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  //Función para remover pagina gris
  function jsRemoveWindowLoad() {
    // eliminamos el div que bloquea pantalla
    $("#WindowLoad").remove();
  }
  //Función para cargar pagina gris y carga
  function jsShowWindowLoad(mensaje) {
    //eliminamos si existe un div ya bloqueando
    jsRemoveWindowLoad();
    //si no enviamos mensaje se pondra este por defecto
    if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor";
    //centrar imagen gif
    height = 20;//El div del titulo, para que se vea mas arriba (H)
    var ancho = 0;
    var alto = 0;
    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
    if (window.innerWidth == undefined) ancho = window.screen.width;
    else ancho = window.innerWidth;
    if (window.innerHeight == undefined) alto = window.screen.height;
    else alto = window.innerHeight;
    //operación necesaria para centrar el div que muestra el mensaje
    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar
    //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>";
    //creamos el div que bloquea grande------------------------------------------
    div = document.createElement("div");
    div.id = "WindowLoad";
    div.style.width = ancho + "px";
    div.style.height = alto + "px";
    $("body").append(div);
    //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
    input = document.createElement("input");
    input.id = "focusInput";
    input.type = "text";
    //asignamos el div que bloquea
    $("#WindowLoad").append(input);
    //asignamos el foco y ocultamos el input text
    $("#focusInput").focus();
    $("#focusInput").hide();
    //centramos el div del texto
    $("#WindowLoad").html(imgCentro);
  }
  //Función para enviar los valores
  function send_table(periodo,informe) {
    $("#modalPeriodo").modal('hide');
    jsShowWindowLoad("Cargando..");
    var periodo = periodo;
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Validamos por medio de la periodicidad el envio de la variable form_data
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    switch (periodo) {
      case '1':
        form_data = {
          session:2,
          periodo:periodo,
          informe:informe,
          anno:$("#sltAnno").val()
        };
        break;
      case '2':
        form_data = {
          session:3,
          perido:periodo,
          informe:informe,
          mesI:$("#sltMesInicial").val(),
          mesF:$("#sltMesFinal").val()
        };
        break;
      case '3':
          
        form_data = {
          session:4,
          perido:periodo,
          informe:informe,
          trimestre:$("#sltTrimestre").val()
        };
        break;
    }
    //Envio ajax
    $.ajax({
      type:'POST',
      url:'consultasBasicas/estructura_generar_consultas_informes.php',
      data:form_data,
      success: function(data,textStatus,jqXHR) {
        jsRemoveWindowLoad();  
        console.log('aca' +data);
        if(data.length != 0){
          jsRemoveWindowLoad();
          validate_consulta();
        }
      }
    }).error(function(data,textError,jqXHR){
      alert('Error :'+textError+', data:'+data);
    });
  }

  function validate_consulta() {
    var id = $("#nombre").val();
    var tipoInf = $("#tipoInf").val();
    //Validar Si El Informe Es Cuipo
    if(tipoInf==6){
        var form_data = { estruc: 16, id: id, tipoInf: tipoInf};
        $.ajax({
          type: "POST",
          url: "estructura_gestor_informes.php",
          data: form_data,
          success: function(response) {
            console.log('validateconsulta'+response);
          }//Fin succes.
        })
    }
    
    var id = $("#nombre").val();
    var tipoInf = $("#tipoInf").val();
    var consulta = $("#consulta").val();
    var form_data = { estruc: 2, id: id, tipoInf: tipoInf, consulta: consulta };
    $.ajax({
      type: "POST",
      url: "estructura_gestor_informes.php",
      data: form_data,
      success: function(response) {
        console.log('validateconsulta'+response);
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
    });
  }
</script>
