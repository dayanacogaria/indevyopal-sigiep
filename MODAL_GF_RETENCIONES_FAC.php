<?php 
################### MODIFICACIONES ###################
#14/07/2017 | ERICA G. | ARCHIVO CREADO
require_once 'Conexion/conexion.php';
@session_start();
$valorTotal=0;
$id=0;
$pptal=0;
if(!empty($_POST['valorTotal'])) {
$valorTotal = $_POST['valorTotal'];
}
if(!empty($_POST['id'])) {
   $id = $_POST['id']; 
}
if(!empty($_POST['pptal'])) {
    $pptal = $_POST['pptal'];
}


?>
<style>
    #tabla2 table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    #tabla2 table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}
    #btnCerrarModalMov1:hover {
        border: 1px solid #020324;         
    }
    
    #btnCerrarModalMov1{
        box-shadow: 1px 1px 1px 1px #424852;
    }
</style>
<style>
.cabeza{
    white-space:nowrap;
    padding: 20px;
}
.campos{
    padding:-20px;
}
</style> 
<div class="modal fade movi1" id="mdlModificarReteciones1" role="dialog" align="center" aria-labelledby="mdlModificarReteciones1" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:900px">
        <div class="modal-content">
            <input type="hidden" id="consecutivo"> <!-- Dejar hidden. -->
            <input type="hidden" id="idcnt" value="<?php echo $id;?>"> 
            <input type="hidden" id="idpptal" value="<?php echo $pptal;?>"> 
            <script type="text/javascript">
                $(document).ready(function()
                {
                  $("#consecutivo").val(0);
                });
              </script>
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24; padding: 3px;">Registrar Retenciones</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov1" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <input type="hidden" id="idPrevio1" value="">
                    <input type="hidden" id="idActual1" value="">
                    <div class="col-sm-12" style="margin-top: 10px;margin-left: 4px;margin-right: 4px">                                                
                        
                        <div class="table-responsive contTabla" >
                            <table id="tabla212" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" style="">
                                <thead style="position: relative;overflow: auto;width: 100%;">
                                    <tr>
                                        <td class="oculto">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Clase Retención</strong></td>
                                        <td class="cabeza"><strong>Tipo Retención</strong></td>
                                        <td class="cabeza"><strong>Aplicar Sobre</strong></td>
                                        <td class="cabeza"><strong>Valor Total</strong></td>
                                        <td class="cabeza"><strong>% IVA</strong></td>
                                        <td class="cabeza"><strong>Valor Base</strong></td>
                                        <td class="cabeza"><strong>Retención a Aplicar</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="oculto">Identificador</th>
                                        <th width="7%" class="cabeza"></th>
                                        <th class="cabeza">Clase Retención</th>
                                        <th class="cabeza">Tipo Retención</th>
                                        <th class="cabeza">Aplicar Sobre</th>
                                        <th class="cabeza">Valor Total</th>
                                        <th class="cabeza">% IVA</th>
                                        <th class="cabeza">Valor Base</th>
                                        <th class="cabeza">Retención a Aplicar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                  <td class="oculto">
                                  </td>
                                  <td class="campos" > 
                                    <a class="campos" id="modificarValoresRet0" style="cursor: pointer; display: none;" onclick="javascript: modificarValores(0);">
                                    <i title="Modificar" class="glyphicon glyphicon-edit">
                                    </i>
                                    </a>
                                  </td>
                                  <td class="campos" align="center">
                                    <!-- Clase Retención --> 
                                    <?php
                                      $claseRet = "SELECT nombre, id_unico
                                        FROM gf_clase_retencion 
                                        ORDER BY nombre ASC";
                                      $rscR = $mysqli->query($claseRet);
                                      ?> 
                                    <label></label>
                                    <select name="sltClaseRet0" id="sltClaseRet0" onchange="javascript: claseRet(0);" class="form-control input-sm" title="Seleccione Clase Retención" style="width: 94%;">
                                      <option value="" selected="selected">Clase Retención</option>
                                      <?php 
                                        while($filacR = mysqli_fetch_row($rscR)) 
                                        {
                                          echo '<option value="'.$filacR[1].'">'.ucwords(($filacR[0])).'</option>';
                                        }
                                        ?>   
                                    </select>
                                    <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.
                                        function claseRet(num)
                                        {
                                            var numeral = num;
                                            $("#modificarValoresRet"+num).css("display", "none"); 
                                            validarOculto(num);
                                            limpiarValores(numeral);

                                            var opcion = '<option value="">Tipo Retención</option>';
                                            if(($("#sltClaseRet"+numeral).val() != "") && ($("#sltClaseRet"+numeral).val() != 0))
                                            {  
                                              var id_clas_rt = $("#sltClaseRet"+numeral).val();
                                              var form_data = { action: 2, id_clase_ret: id_clas_rt };

                                              $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_recaudoFacJson.php",
                                                data: form_data,
                                                success: function(response)
                                                {
                                                  if(response != 0 && response != "") 
                                                  {
                                                    opcion += response;
                                                    $("#sltTipoRet"+numeral).html(opcion).focus();
                                                    validarTipoRetencion(numeral);
                                                  }            
                                                  else
                                                  {
                                                    opcion = '<option value="">No hay tipo retención</option>';;
                                                    $("#sltTipoRet"+numeral).html(opcion);
                                                    validarTipoRetencion(numeral);
                                                  }                  
                                                }//Fin succes.
                                              }); //Fin ajax.


                                            }
                                            else
                                            {
                                              $("#sltTipoRet"+numeral).html(opcion);
                                              validarTipoRetencion(numeral);
                                            }


                                        }

                                      </script> <!-- Código JS para asignación -->
                                  </td>
                                  <td class="campos" align="center" >
                                    <!-- Tipo Retención --> 
                                    <label></label>
                                    <select name="sltTipoRet0" id="sltTipoRet0" onclick="javascript: tipoRetencion(0);"  onchange="javascript: valTipoRetRep(0);" class="form-control input-sm" title="Seleccione Tipo Retención" style="width: 94%;">
                                      <option value="">Tipo Retención</option>
                                    </select>
                                    <script type="text/javascript"> //
                                        function tipoRetencion(num)
                                        {
                                            var numeral = num;

                                          if(($("#sltClaseRet"+numeral).val() == "") || ($("#sltClaseRet"+numeral).val() == 0))
                                          {
                                            $("#modErrorTipRet").modal('show');                
                                          }

                                        }
                                      </script> 
                                      <!-- Código JS para asignación -->
                                    <script type="text/javascript"> // Evento change del combo-select Tipo de retención.
                                      function valTipoRetRep(num)
                                      {
                                        var numeral = num;

                                        $("#modificarValoresRet"+num).css("display", "none"); 
                                        validarOculto(num);

                                        var consecutivo = $("#consecutivo").val();
                                        var valorTipRet = $("#sltTipoRet" + num).val();

                                        validarTipoRetencion(numeral);

                                        if(consecutivo != 0)
                                        {
                                          for(var i = 0; i <= consecutivo; i++)
                                          {
                                            if(i != num && valorTipRet == $("#sltTipoRet" + i).val())
                                            {
                                              $("#modTipRetRepetido").modal('show'); //Ya existe un tipo de retención igual a esta.
                                              $("#numeralError").val(num);
                                              validarTipoRetencion(numeral);
                                              break;
                                            }
                                          }                
                                        }

                                      }
                                    </script> <!-- Código JS para asignación -->
                                  </td>
                                  <td class="campos" align="center" style="padding: 0px">
                                    <!-- Aplicar Sobre -->
                                    <?php
                                      $aplicarS = "SELECT nombre, id_unico
                                                    FROM gf_tipo_base
                                                    WHERE nombre != ''
                                                    ORDER BY nombre ASC";
                                      $rsaS = $mysqli->query($aplicarS);
                                      ?>
                                    <label></label>
                                    <select name="sltAplicarS0" id="sltAplicarS0" onclick="javascript: validarTres(0);" onchange="javascript: aplicarSob(0);" class="form-control input-sm" title="Aplicar Sobre" style="width: 94%;">
                                      <option value="" selected="selected">Aplicar sobre</option>
                                      <?php 
                                        while ($filaaS = mysqli_fetch_row($rsaS)) 
                                        { 
                                          echo '<option value="'.$filaaS[1].'">'.ucwords(($filaaS[0])).'</option>'; 
                                        }
                                        ?>                                   
                                    </select>
                                    <script type="text/javascript">
                                        function validarTres(num)
                                        {
                                          $("#sltAplicarS"+num).change(function()
                                          {
                                            var tipoRete = $("#sltTipoRet"+num).val();
                                            var aplicar = $("#sltAplicarS"+num).val();
                                            var form_data = { action: 3, aplicar: aplicar, tipoRete: tipoRete };

                                            $.ajax({
                                              type: "POST",
                                              url: "jsonPptal/gf_recaudoFacJson.php",
                                              data: form_data,
                                              success: function(response)
                                              {
                                                response = response.trim();
                                                if(response != 0)
                                                { // 1 equivale a Sí, 2 equivale a No.
                                                  var valida = response.split("|");

                                                  if(valida[0] == 1 || valida[1] == 1)
                                                  {
                                                    $("#modificarValoresRet"+num).css("display", "block");
                                                  }
                                                  else
                                                  {
                                                    $("#modificarValoresRet"+num).css("display", "none");
                                                  }

                                                  $("#retencionAplicaM"+num).val(valida[0]);
                                                  $("#valorBaseM"+num).val(valida[1]);
                                                }
                                                else
                                                {
                                                  $("#retencionAplicaM").val("");
                                                  $("#valorBaseM").val("");
                                                }                     
                                              }//Fin succes.
                                            }); //Fin ajax.

                                          });

                                        }

                                      </script>
                                      <script type="text/javascript"> //
                                        function aplicarSob(num)
                                        {
                                          var numeral = num;

                                          $("#modificarValoresRet"+num).css("display", "none"); 
                                          validarOculto(num);

                                          if(($("#sltTipoRet"+numeral).val() != "") && ($("#sltTipoRet"+numeral).val() != 0))
                                          {
                                            if($("#porIVA"+numeral).val() == 0 || $("#porIVA"+numeral).val() == "")
                                            {
                                              $("#modErrIva").modal('show');
                                            }
                                            else if(($("#sltAplicarS"+numeral).val() != "") && ($("#sltAplicarS"+numeral).val() != 0))  //acá
                                            {  
                                              var tipoRete = $("#sltTipoRet"+numeral).val();
                                              var aplicar = $("#sltAplicarS"+numeral).val();
                                              var valor = $("#valorTotal"+numeral).text();
                                              valor = parseFloat(valor.replace(/\,/g,'')); 
                                              var iva = $("#porIVA"+numeral).val();
                                              var form_data = { action: 4, aplicar: aplicar, valor: valor, iva: iva, tipoRete: tipoRete };

                                              $.ajax({
                                                type: "POST",
                                                url: "jsonPptal/gf_recaudoFacJson.php",
                                                data: form_data,
                                                success: function(response)
                                                {
                                                  var valorBase = parseFloat(response).toFixed(2);
                                                  $("#valorBase"+numeral).html(valorBase);
                                                  $("#valorBaseOcul"+numeral).val(valorBase);
                                                  formatC('valorBaseOcul'+numeral);
                                                  $("#valorBase"+numeral).html($("#valorBaseOcul"+numeral).val());
                                                  retencionAplicar(numeral);
                                                }//Fin succes.
                                              }); //Fin ajax.
                                            }
                                            else
                                            {
                                              $("#valorBase"+numeral).html("");
                                              $("#valorBaseOcul"+numeral).val("");
                                              $("#retencionApl"+numeral).html("");
                                              $("#retencionAplOcul"+numeral).val("");
                                            }
                                          }
                                          else
                                          {
                                            $("#modErrorRet").modal('show');
                                          }
                                        }

                                      </script> <!-- Código JS para asignación -->
                                  </td>
                                  <!-- Fin celda Valor aprobado -->
                                  <td class="campos" align="right" >
                                    <!-- Valor Total -->
                                    <span class="valorTotal" id="valorTotal0"><?php echo number_format($valorTotal, 2, '.', ','); ?></span>
                                  </td>
                                  <!-- Saldo por pagar -->
                                  <td class="campos" align="center" >
                                    <!-- % IVA -->
                                    <?php 
                                      $porIVA = "SELECT valor 
                                                  FROM gs_parametros_basicos 
                                                  WHERE nombre ='porcentaje iva' ";
                                      $rsPI = $mysqli->query($porIVA);
                                      $filaPI = mysqli_fetch_row($rsPI);
                                      ?> 
                                    <label></label>
                                    <input type="number" step="1" min="0" max="100" value="<?php echo $filaPI[0];?>" name="porIVA0" id="porIVA0" onkeyup="javascript: porcIVA(0);" class="form-control input-sm" maxlength="100" title="" onkeypress="return txtValida(event,'dec', 'porIVA', '2')" onclick="javascript: porcIVA(0);" placeholder="% IVA" style="width: 94%;" >
                                    <input type="hidden" id="paramIVA0" value="<?php echo $filaPI[0];?>">
                                  </td>
                                  <!-- Saldo por pagar -->
                                  <td class="campos" align="right" >
                                    <!-- Valor Base -->
                                    <span id="valorBase0"></span>
                                    <input type="text" id="valorBaseNuevo0" name="valorBaseNuevo0" style="display: none; width: 90%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorBaseNuevo0', '2');" onkeyup="formatC('valorBaseNuevo0')">
                                    <input type="hidden" id="valorBaseM0" value="">
                                    <input type="hidden" id="valorBaseOcul0">
                                  </td>
                                  <td class="campos" align="right" >
                                    <!-- Retención a Aplicar -->
                                    <span id="retencionApl0"></span>
                                    <a class="campos" id="ok0" style="cursor: pointer; display: none; position: absolute;" onclick="javascript: aceptarMod(0);">
                                    <i title="Ok" class="glyphicon glyphicon-ok">
                                    </i>
                                    </a>
                                    <a class="campos" id="cancelar0" style="cursor: pointer; display: none; position: absolute; margin-left: 16px;" onclick="javascript: cancelarMod(0);">
                                    <i title="Cancelar" class="glyphicon glyphicon-remove">
                                    </i>
                                    </a>
                                    <input type="text" id="valorRetencionNuevo0" name="valorRetencionNuevo0" style="display: none; width: 75%;" maxlength="50" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorRetencionNuevo0', '2');" onkeyup="formatC('valorRetencionNuevo0')">
                                    <input type="hidden" id="retencionAplicaM0" value="">
                                    <input type="hidden" id="retencionAplOcul0">
                                  </td>
                                  <!--  Fin retención a Aplicar -->
                                </tr>
                              </tbody>
                            </table>
                            <div class="col-sm-12" align="right" style="margin-top: 10px;">
                              <button type="button" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px;" title="Generar Retención" >
                                <li class="glyphicon glyphicon-floppy-disk"></li>
                              </button>
                              <button type="button" id="btnNuevoRet" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px;" title="Generar Comprobante CNT" >
                                <li class="glyphicon glyphicon-plus"></li>
                              </button>
                              <!-- Nuevo--> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#btnGuardarComp').click(function()
    {
      $('#btnGuardarComp').prop("disabled", true);
    
      var numeral  = parseInt($("#consecutivo").val());
      var num = numeral;
      var pasa = 0;
      var error = 0;
     
        if(numeral == 0)
        {
          if($("#valorBaseOcul0").val() != 0 && $("#valorBaseOcul0").val() != "")
          {
            var valorRet = $("#retencionAplOcul0").val();
            valorRet = parseFloat(valorRet.replace(/\,/g,'')); 
    
            var retencionBas = $("#valorBaseOcul0").val();
            retencionBas = parseFloat(retencionBas.replace(/\,/g,'')); 
    
            var porcenRet = $("#porIVA0").val();
            var tipoRet = $("#sltTipoRet0").val(); 
            var cuentaDesRet = $("#cuenCre0").val();
    
            pasa = 1;
          }
          else
          {
            $("#modVacioError").modal('show'); // Mensaje: No se ha calculado la retención.
          }
        }
        else
        {
            var valorRet = new Array();
            var retencionBas = new Array();
            var porcenRet = new Array();
            var tipoRet = new Array();
            var cuentaDesRet = new Array();
    
             for(var i = 0; i <= num; i++)
            {
              numeral  = i;
              
              if($("#valorBaseOcul" + numeral).val() != 0 && $("#valorBaseOcul" + numeral).val() != "")
              {
                  
                var valorRetV = $("#retencionAplOcul"+numeral).val();
                valorRetV = parseFloat(valorRetV.replace(/\,/g,''));
                valorRetV = valorRetV.toString();
                valorRet[i] = valorRetV;
    
                var retencionBasV = $("#valorBaseOcul"+numeral).val();
                retencionBasV = parseFloat(retencionBasV.replace(/\,/g,'')); 
                retencionBasV = retencionBasV.toString();
                retencionBas[i] = retencionBasV;
    
                var porcenRetV = $("#porIVA"+numeral).val();
                porcenRet[i] = porcenRetV;
                
                var tipoRetV = $("#sltTipoRet"+numeral).val();
                tipoRet[i] = tipoRetV; 
    
                var cuentaDesRetV = $("#cuenCre"+numeral).val();
                cuentaDesRet[i] = cuentaDesRetV;
              }
              else
              {
                $("#numeralError").val(numeral);
                error = 1;
                break;
              }
            }
    
            if(error != 1)
            {
              pasa = 1;
    
              valorRet = serializeArr(valorRet);
    
              retencionBas = serializeArr(retencionBas);
    
              porcenRet = serializeArr(porcenRet);
    
              tipoRet = serializeArr(tipoRet);
    
              
    
            }
            else
            {
              $("#modVacioError").modal('show'); // Mensaje: No se ha calculado la retención.
            }
              
         }
         
         if(pasa != 0)
         {
             var cnt = $("#idcnt").val();
            var pptal =$("#idpptal").val();
            
    
            var form_data = { action: 6, valorRet: valorRet, retencionBas: retencionBas, 
                porcenRet: porcenRet, pptal: pptal, cnt: cnt, tipoRet: tipoRet, cuentaDesRet: cuentaDesRet, numReng: num };
    
            $.ajax({
              type: "POST",
              url: "jsonPptal/gf_recaudoFacJson.php",
              data: form_data,
              success: function(response)
              {
                console.log(response);
                response = parseInt(response);
                
                if(response == 1)
                {
                  $("#modGuarExitoRet").modal('show');
                  $("#btnGenerarCom").prop("disabled", false);
                  $(".ocultarSiGuarda").hide();
                }
                else if(response == 2)
                {
                  $("#modReptError").modal('show');
                }
                else if(response == 0)
                {
                  $("#modGuarError").modal('show');
                }
    
              }//Fin succes.
            }); //Fin ajax.
         }
    
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function()
    {
            
            $("#btnGuardarComp").prop("disabled", true);
            $("#btnNuevoRet").prop("disabled", true);
   
    });
    
  </script>  
  
  <script type="text/javascript">
    function validarTipoRetencion(id)
    {
      limpiarValores(id);
      if($("#sltTipoRet" + id).val() == "")
      {
        $("#btnGuardarComp").prop("disabled", true);
        $("#btnNuevoRet").prop("disabled", true);
      }
      else
      {
        $("#btnGuardarComp").prop("disabled", false);
        $("#btnNuevoRet").prop("disabled", false);
      }
      
    }
    
  </script>
  <script type="text/javascript">
    function porcIVA(num)
    {
        var numeral = num;
        $("#modificarValoresRet"+num).css("display", "none"); 
        validarOculto(num);
        
        var porIva = $("#porIVA"+numeral).val();
    
        if(porIva > 100)
        {
          $("#modErrorIva").modal('show');
          $("#porIVA"+numeral).attr('readonly', 'readonly');
        }
        else
        {
          $("#sltAplicarS"+numeral).val("").attr('selected', 'selected');
          $("#valorBase"+numeral).text("");
          $("#valorBaseOcul"+numeral).val("");
          $("#retencionApl"+numeral).text("");
          $("#retencionAplOcul"+numeral).val("");
        }
      }
    
  </script>
  <script type="text/javascript">
    function limpiarValores(numeral)
    {
      if($("#sltAplicarS"+numeral).val() != "")
      {
        $("#sltAplicarS"+numeral).val("").attr('selected', 'selected').focus();
        $("#valorBase"+numeral).text("");
        $("#valorBaseOcul"+numeral).val("");
        $("#retencionApl"+numeral).text("");
        $("#retencionAplOcul"+numeral).val("");
      }
      
    }
    
  </script>
  <script type="text/javascript">
    $(document).ready(function()
    {
      $("#btnNuevoRet").click(function()
      {
          
        var consecutivo = $("#consecutivo").val();
        consecutivo = parseInt(consecutivo);
        consecutivo += 1;
        
        $("#consecutivo").val(consecutivo);
            
        
        var valorTotal =<?php echo $valorTotal?>;
        var form_data = { action: 1,  valorTotal: valorTotal, consecutivo: consecutivo };
        $.ajax({
          type: "POST",
          url: "jsonPptal/gf_recaudoFacJson.php",
          data: form_data,
          success: function(data)
          {
              
            $("#tabla212 tbody").append(data);
          }//Fin succes.
        }); //Fin ajax
    
      });
    });
    
  </script>
  
 
  
  <script type="text/javascript">
    function retencionAplicar(numeral)
    {
      var idTipRet = $("#sltTipoRet"+numeral).val();
      var valorBas = $("#valorBase"+numeral).text();
      valorBas = parseFloat(valorBas.replace(/\,/g,'')); 
      var form_data = { action: 5, valorBas: valorBas, idTipRet: idTipRet };
    
      $.ajax({
        type: "POST",
        url: "jsonPptal/gf_recaudoFacJson.php",
        data: form_data,
        success: function(response)
        {
            
          var retApl = parseFloat(response).toFixed(2);
          $("#retencionApl"+numeral).html(retApl);
          $("#retencionAplOcul"+numeral).val(retApl);
          formatC('retencionAplOcul'+numeral);
          $("#retencionApl"+numeral).html($("#retencionAplOcul"+numeral).val());
        }//Fin succes.
      }); //Fin ajax.
    
    }
    
  </script>
  <script type="text/javascript"> 
    function modificarValores(num)
    {
      validarOculto(num);
      // 1 equivale a Sí, 2 equivale a No.
      var retencionAplicaM = $("#retencionAplicaM"+num).val();
      var valorBaseM = $("#valorBaseM"+num).val();
    
      if(valorBaseM == 1)
      {
        $("#valorBase"+num).css("display", "none");
        var valorBse = $("#valorBaseOcul"+num).val();
        $("#valorBaseNuevo"+num).val(valorBse);
        $("#valorBaseNuevo"+num).css("display", "block");
      }
      
      if(retencionAplicaM == 1)
      {
        $("#retencionApl"+num).css("display", "none");
        var valorRtencion = $("#retencionAplOcul"+num).val();
        $("#valorRetencionNuevo"+num).val(valorRtencion);
        $("#valorRetencionNuevo"+num).css("display", "block");
      }
    
      $("#ok"+num).css("display", "block");
      $("#cancelar"+num).css("display", "block");
      
      $("#visibleActual").val(num);
    }
    
  </script>
  <script type="text/javascript"> 
    function cancelarMod(num)
    {
      $("#visibleActual").val("");
      $("#valorBaseNuevo"+num).css("display", "none");
      $("#valorRetencionNuevo"+num).css("display", "none");
    
      $("#ok"+num).css("display", "none");
      $("#cancelar"+num).css("display", "none");
    
      $("#valorBase"+num).css("display", "block");
      $("#retencionApl"+num).css("display", "block");
    
    
    }
    
  </script>
  <script type="text/javascript"> 
    function aceptarMod(num)
    {
    
      if($('#valorBaseNuevo'+num).is(":visible") )
      {
        var valorBse = $("#valorBaseNuevo"+num).val();
        $("#valorBaseOcul"+num).val(valorBse);
        $("#valorBase"+num).text(valorBse);
        $("#valorBaseNuevo"+num).css("display", "none");
        $("#valorBase"+num).css("display", "block");
          var idTipRet = $("#sltTipoRet"+num).val();
          
          valorBse = parseFloat(valorBse.replace(/\,/g,'')); 
          var form_data = { action: 5, valorBas: valorBse, idTipRet: idTipRet };
    
          $.ajax({
            type: "POST",
            url: "jsonPptal/gf_recaudoFacJson.php",
            data: form_data,
            success: function(response)
            {
                var retApl = parseFloat(response).toFixed(2);
                console.log($('#valorRetencionNuevo'+num).is(":visible") );
                if($('#valorRetencionNuevo'+num).is(":visible") ){
                    if($("#valorRetencionNuevo"+num).val() == 0 || $("#valorRetencionNuevo"+num).val() == ''){
                        $("#retencionApl"+num).html(retApl);
                        $("#retencionAplOcul"+num).val(retApl);
                        formatC('retencionAplOcul'+num);
                        $("#retencionApl"+num).html($("#retencionAplOcul"+num).val());
                        $("#valorRetencionNuevo"+num).css("display", "none");
                        $("#retencionApl"+num).css("display", "block");
                    } else {
                        console.log('retnvalorBR= '+$("#valorRetencionNuevo"+num).val() );
                        var valorRtencion = $("#valorRetencionNuevo"+num).val();
                        $("#retencionAplOcul"+num).val(valorRtencion);
                        $("#retencionApl"+num).text(valorRtencion);
                        $("#valorRetencionNuevo"+num).css("display", "none");
                        $("#retencionApl"+num).css("display", "block");
                    }
                } else { 
                    $("#retencionApl"+num).html(retApl);
                    $("#retencionAplOcul"+num).val(retApl);
                    formatC('retencionAplOcul'+num);
                    $("#retencionApl"+num).html($("#retencionAplOcul"+num).val());
                    $("#valorRetencionNuevo"+num).css("display", "none");
                    $("#retencionApl"+num).css("display", "block");
                }
            }//Fin succes.
          });
        
       
      } 
      
      $("#visibleActual").val("");    
      $("#ok"+num).css("display", "none");
      $("#cancelar"+num).css("display", "none");
    
    }
    
  </script>
  <script type="text/javascript">
    function validarOculto(num)
    {
      var actual = $("#visibleActual").val()
      if(actual != "")
      {
    
        if( $('#ok'+actual).is(":visible") )
        {
          
          if(num != actual)
          {
            $("#valorBaseNuevo"+actual).css("display", "none");
            $("#valorRetencionNuevo"+actual).css("display", "none");
    
            $("#ok"+actual).css("display", "none");
            $("#cancelar"+actual).css("display", "none");
    
            $("#valorBase"+actual).css("display", "block");
            $("#retencionApl"+actual).css("display", "block");
    
          }
          else
          {
            $("#visibleActual").val("");
            $("#valorBaseNuevo"+num).css("display", "none");
            $("#valorRetencionNuevo"+num).css("display", "none");
    
            $("#ok"+num).css("display", "none");
            $("#cancelar"+num).css("display", "none");
    
            $("#valorBase"+num).css("display", "block");
            $("#retencionApl"+num).css("display", "block");
    
            $("#modificarValoresRet"+num).css("display", "none");
    
          }
    
        }
      }
    
    }
    
  </script>
  
  <!-- Divs de clase Modal para las ventanillas de eliminar. -->
  <div class="modal fade" id="modErrorIva" role="dialog" align="center" data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El valor del porcentaje del IVA no puede ser superior a 100%</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrorIva" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Divs de clase Modal para las ventanillas de eliminar. -->
  <div class="modal fade" id="modErrorRet" role="dialog" align="center" data-keyboard="false"  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Debe seleccionar un Tipo Retención</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrorRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Divs de clase Modal para las ventanillas de eliminar. -->
  <div class="modal fade" id="modErrorTipRet" role="dialog" align="center" data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Debe seleccionar una Clase Retención</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrorTipRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
  <div class="modal fade" id="modGuarExitoRet" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnGuarEx" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <script>
      $("#modGuarExitoRet").click(function(){
          $("#mdlModificarReteciones1").modal('hide');
          $(".modal-backdrop fade in").css('display','none');
          $(".modal-backdrop").css('display','none');
      })
  </script>
  <div class="modal fade" id="modGuarError" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnGuarErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modVacioError" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha calculado la retención.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnVacErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modErrIva" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El valor del IVA no es el adecuado o está vacío.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrIva" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modReptError" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Ya existe una retención para este comprobante con el tipo de retención seleccionado.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnErrRep" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modTipRetRepetido" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Ya existe un tipo de retención igual a esta.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnTipRetRepetido" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modNoCuentTipRet" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El tipo de retención seleccionado no tiene cuenta. Se ingresaron los datos pero no la retención.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnNoCuentTipRet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modTipoReteNoPermit" role="dialog" align="center"  data-keyboard="false" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Este tipo de retención no permite cambiar el valor base y la retención a aplicar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnTipoReteNoPermit" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $('#btnErrorIva').click(function()
    {
     
      var numeral  = parseInt($("#numeralError").val());
      
      $("#porIVA"+numeral).val($("#paramIVA"+numeral).val());
      $("#porIVA"+numeral).removeAttr('readonly');
    });
  </script>
  <script type="text/javascript">
    $('#btnErrIva').click(function()
    {
      var numeral  = parseInt($("#numeralError").val());
    
      $("#porIVA"+numeral).val($("#paramIVA"+numeral).val()).focus();
      $("#sltAplicarS"+numeral).val("").attr('selected', 'selected');
    });
  </script>
  <script type="text/javascript">
    $('#btnErrorRet').click(function()
    {
      var numeral  = parseInt($("#numeralError").val());
    
      $("#sltTipoRet"+numeral).focus();
      $("#sltAplicarS"+numeral).val("").attr('selected', 'selected');
    });
  </script>
  <script type="text/javascript">
    $('#btnErrorTipRet').click(function()
    {
      var numeral  = parseInt($("#numeralError").val());
    
      $("#sltClaseRet"+numeral).focus();
      $("#sltTipoRet"+numeral).val("").attr('selected', 'selected');
    });
  </script>
  <script type="text/javascript">
    $('#btnVacErr').click(function()
    {
      $('#btnGuardarComp').prop("disabled", false);
      var numeral  = parseInt($("#numeralError").val());
    
      $("#sltAplicarS"+numeral).val("").attr('selected', 'selected').focus();
    });
  </script>
  <script type="text/javascript"></script>
 
  <script type="text/javascript"> 
    function quitarRenglon(id)
    {
      $("#renglon" + id).remove();
      var consecutivo = $("#consecutivo").val();
      consecutivo -= 1;
      $("#consecutivo").val(consecutivo);
    }
  </script>
  <script type="text/javascript">
    function serializeArr(arr)
    {
      var res = 'a:'+arr.length+':{';
      console.log(arr);
      for(ni = 0; ni < arr.length; ni ++)
      {
      res += 'i:' + ni + ';s:' + arr[ni].length + ':"' + arr[ni] + '";';
      }
      res += '}';
       
      return res;
    }
    
  </script>
  <script type="text/javascript"> 
    $('#btnTipRetRepetido').click(function()
    {
      var num = $("#numeralError").val();
      $("#sltTipoRet" + num).val("");
      $("#numeralError").val("");
      
    });
  </script>
 

<script type="text/javascript" >
    $("#btnCerrarModalMov1").click(function(){
       $("#mdlModificarReteciones1").modal('hide');
       $(".modal-backdrop fade in").css('display','none');
       $(".modal-backdrop").css('display','none');
    });
    
    $("#mdlModificarReteciones1").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tabla212").DataTable();
            dataTable.columns.adjust().responsive.recalc();   
        }catch(err){}        
    });
</script>
<script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla212 thead th').each( function () {
        if(i != 1){ 
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
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 8:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 9:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 10:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 11:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 12:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 13:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 14:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 15:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 16:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 17:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 18:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 19:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
  
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tabla212').DataTable({
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
<script>
function justNumbers(e){   
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
        return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<?php if(!empty($_REQUEST['facturacion'])){?>
    <script>
        $("#btnGuarEx").click(function(){
            let factura = $("#id").val();
            let cnt     = $("#idcnt").val();
            var form_data = { action: 7, cnt: cnt, factura: factura };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_recaudoFacJson.php",
                data: form_data,
                success: function(response) {
                    console.log(response);
                   document.location.reload();
                }
            });
       })
    </script>
<?php } ?>



