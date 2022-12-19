<?php 
#################MODIFICACIONES#########################
#05/05/2017 | ERICA G. | VERIFICAR SI LA APROPIACION NO TIENE MOVIMIENTOS 
#27/02/2017 | Erica G. | Diseño, tíldes
########################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head_listar.php'; 
$parametroAnno = $_SESSION['anno'];
$con = new ConexionPDO();
$sqlAnno = 'SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = '.$parametroAnno;
$paramAnno = $mysqli->query($sqlAnno);
$rowPA = mysqli_fetch_row($paramAnno);
$anioAct = "'";
$anioAct .= $rowPA[0];
$anioAct .= "000001'";
$N = 0;
#******* Buscar Tipo Comprobante Por Compañia **********#
$compania = $_SESSION['compania'];
$tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 13 AND tipooperacion = 1");
$tc = $tc[0][0];

$querySql = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = $anioAct AND tipocomprobante = $tc AND parametrizacionanno = $parametroAnno";
$queryComprobante = $mysqli->query($querySql);
$row = mysqli_fetch_row($queryComprobante);
$id_comprobante_pptal = $row[0];
  
$queryGen = "SELECT detCompP.id_unico, rubP.nombre, fue.nombre, detCompP.valor, rubP.codi_presupuesto, 
  fue.id_unico , rubFue.id_unico 
from gf_detalle_comprobante_pptal detCompP
left join gf_rubro_fuente rubFue on rubFue.id_unico = detCompP.rubrofuente
left join gf_fuente fue on fue.id_unico = rubFue.fuente 
left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
left join gf_comprobante_pptal compPtal on compPtal.id_unico = detCompP.comprobantepptal
where compPtal.tipocomprobante = $tc 
and compPtal.id_unico = '$id_comprobante_pptal'
order by detCompP.id_unico desc"; //Tipo comprobante 8 de apropiación inicial.
$resultado = $mysqli->query($queryGen);

//Consulta para el listado de concepto de la tabla gf_rubro_pptal.
$queryRub = "SELECT id_unico, CONCAT(codi_presupuesto, ' - ',nombre) rubro 
FROM gf_rubro_pptal WHERE movimiento = 1 AND parametrizacionanno = $parametroAnno 
ORDER BY codi_presupuesto ASC";
$rubro = $mysqli->query($queryRub);

$queryFue = "SELECT id_unico, nombre    
FROM gf_fuente WHERE  parametrizacionanno = $parametroAnno  ";
$fuente = $mysqli->query($queryFue);
  
?>

<title>Registrar Apropiación Inicial</title>

<script type="text/javascript">

  $(document).ready(function()
  {
    //Función que ejecuta consulta para verificar si las fuentes se encuentran balanceadas o no.
    var id_com_ptal = $("#id_com_ptal").val();

    var form_data = { id: id_com_ptal};
    $.ajax({
      type: "POST",
      url: "estructura_balance_apropiacion.php",
      data: form_data,
      success: function(response)
      {
        document.getElementById("balanceo").value = response;
      }
    });

  });

 </script>

<script type="text/javascript">
  //Evento mouseover sobre el menú para avisar al usuario en caso de que las fuentes estén desbalanceadas.
  $(document).ready(function()
  {
    $("#accordion").mouseover(function()
    {
      var balanceo = document.getElementById("balanceo").value;
      if(balanceo == 1)
      {
      $("#modDesBal").modal('show');
      $("#btnDesBal").focus();
    }
    });
  });
</script>

<script type="text/javascript">
  //Esta función muestra un mensaje modal al usuario al intentar dejar al página. Al detectar la poscición del cursor acercarse a cero, el borde superior de la página, muestra el mensaje diciendo que las fuentes están desbalanceadas en caso en que lo estén.
  function coordenadas(event) 
  {
    var y = event.clientY;

    var balanceo = document.getElementById("balanceo").value;
    if(balanceo == 1)
    {
      if(y >= 0 && y <= 20 )
      {
        $("#modDesBal").modal('show');
        $("#btnDesBal").focus();
      }
    }
  }
</script>

<style type="text/css">
  
  table.dataTable thead th,table.dataTable thead td
  {
    padding: 1px 18px;
    font-size: 10px;
  }

  table.dataTable tbody td,table.dataTable tbody td
  {
    padding: 1px;
  }
  .dataTables_wrapper .ui-toolbar
  {
    padding: 2px;
    font-size: 10px;
  }

  .control-label
  {
    font-size: 12px;
  }

</style>


<link rel="stylesheet" href="css/jquery-ui.css">
  <script src="js/jquery-ui.js"></script> 
  <link href="css/select/select2.min.css" rel="stylesheet">


</head>
<body onMouseMove="coordenadas(event);"> <!-- Llamado de la función coordenadas, quien muestra un mensaje al intentar dejar la página. -->

<!-- Campos ocultos que funcionan como variables globales para las funciones de JS. -->
<input type="hidden" id="balanceo">
<input type="hidden" id="id_com_ptal" value="<?php echo $id_comprobante_pptal;?>">

<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Registrar Apropiación Inicial</h2>


<div class="col-sm-10"> 

              <div class="client-form contenedorForma form-inline" >
              <!-- Formulario de detalle comprobante pptal -->
              <form name="formConRub" id="orm" class="form-inline" method="POST"  enctype="multipart/form-data" onsubmit="return validarValor()" action="json/registrar_GF_APROPIACION_INICIALJson.php">

                <p align="center" class="parrafoO" style="margin-bottom: 5px">
                  Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                </p>
              <div class="row" style="margin-top: -5px;" > 

                <!-- Combo-box Rubro -->
                <div id="divRubro" class="form-group form-inline col-sm-3" style="margin-top: 5px; margin-left: 5px;" align="left">
                  <label for="rubro" class=" control-label"><strong class="obligado">*</strong>Rubro:</label><br/>

                  <input type="hidden" id="rubroOcul" name="rubroOcul">
                  <select name="rubro" id="rubro" onchange="llenar();" class=" form-control input-sm select2_single" title="Seleccione el rubro" style="width:150px;">
                    <option value="" selected="selected" >Rubro</option>
                  <?php
                    while($rowRub = mysqli_fetch_row($rubro))
                    {
                  ?>
                    <option value="<?php echo $rowRub[0]; ?>"><?php echo ucwords(mb_strtolower($rowRub[1])); ?></option>
                  <?php 
                    }
                  ?>
                  </select> 

                </div>

                <script src="js/select/select2.full.js"></script>

                <script>
                  $(document).ready(function() 
                  {
                    $(".select2_single").select2(
                    {
                        allowClear: true
                    });
                  });
                </script>

                <script>
                  $(document).ready(function() 
                  {
                    llenar();
                  });
                </script>
              
                <script>
                  function llenar()
                  {
                    var rubro = document.getElementById('rubro').value;
                    document.getElementById('rubroOcul').value= rubro;
                  }
                </script>




                <!-- Combo-box Fuente -->
                <div class="form-group form-inline col-sm-3" style="margin-top: 5px;" align="left">
                  <label for="fuente" class="control-label"><strong class="obligado">*</strong>Fuente:</label><br/>
                  <select name="fuente" id="fuente" class="form-control input-sm select2_single" title="Seleccione la fuente" style="width:150px;" required>
                    <option value="" selected="selected" >Fuente</option>
                <?php
                    while($rowFue = mysqli_fetch_row($fuente))
                    {
                  ?>
                    <option value="<?php echo $rowFue[0]; ?>"><?php echo ucwords(mb_strtolower($rowFue[0].' - '.$rowFue[1])); ?></option>
                  <?php 
                    }
                  ?>
                  </select> 

                </div>


                <script type="text/javascript">
                  //Evento Change sobre el la lista Combo-box Fuente. Compara los datos seleccionados en Rubro y Fuente para determinar si ya se encuentran en la base de datos, ya que estos son llave única en la tabla gf_rubro_fuente.
                   $(document).ready(function()
                    {  
                    $("#fuente").change(function()
                    {
                      if(($("#rubro").val() == "")||($("#rubro").val() == 0))
                      {
                        $("#myRubroVacio").modal('show');
                      }
                      else
                      {
                        if(($("#fuente").val() != "")||($("#fuente").val() != 0))
                        {
                          var form_data = { id_fuente:+$("#fuente").val(), id_rubro:+$("#rubro").val()  };
                          $.ajax({
                            type: "POST",
                            url: "estructura_consulta_rubro_fuente.php",
                            data: form_data,
                            success: function(response)
                            {                          
                              if(response == 1)
                              {
                                $("#myRubFueRepetido").modal('show');
                              }
                                
                            }//Fin succes.
                          }); //Fin ajax.

                        } //Cierre if "#fuente".
                      } //Cierre else "#rubro".
                                   
                    });//Cierre change.
                 });//Cierre Ready.

                </script>

                <!-- Caja texto Valor -->
              <div class="col-sm-3" >
                <div class="form-group" style="margin-top: 5px; margin-left: 0px; " align="left">
                  <label for="valor" class="control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label><br/>
                  <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>
                 

                  <input type="hidden" value="">
                </div>
              </div> 

               <!-- Botón guardar -->

                <div class="col-sm-1 " >
                  <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                <input type="hidden" name="MM_insert" >
              </div>

                </div> <!-- Cierra clase row -->
              </form>
            </div>  <!-- cierra clase client-form contenedorForma -->
      
      <input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">

      </div>


<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-8" style="margin-top: 10px;">
          <div class="table-responsive contTabla" >
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Rubro</strong></td>
                <td class="cabeza"><strong>Fuente</strong></td>
                <td class="cabeza"><strong>Valor</strong></td>
               
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Rubro</th>
                <th>Fuente</th>
                <th>Valor</th>
                                
              </tr>

            </thead>
            <tbody>
              
              <?php
                if($resultado == true)
                {
                  while($row = mysqli_fetch_row($resultado))
                  {
                ?>
               <tr>
                <td class="oculto"><?php echo $row[0]?></td>
                <td class="campos">
                  <?php 
                  #######VERIFICAR SI EL RUBRO TIENE MOVIMIENTO####
                  $rf=$row[6];
                  $busrf = "SELECT * FROM gf_detalle_comprobante_pptal dc 
                        LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
                        LEFT JOIN gf_tipo_comprobante_pptal tc ON cn.tipocomprobante = tc.id_unico 
                        where dc.rubrofuente = $rf and dc.id_unico !=$row[0] 
                            AND tc.clasepptal !=13 and tc.tipooperacion != 1";
                  $busrf =$mysqli->query($busrf);
                  if(mysqli_num_rows($busrf)>0) { } else { 
                  ?>  
                  <a class href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a class href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" ><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                  <?php } ?>
                </td>
                <td class="campos" align="left"><?php echo ucwords(mb_strtolower($row[4].' - '.$row[1]));?> </td>
                <td class="campos" align="left"><?php echo ucwords(mb_strtolower($row[5].' - '.$row[2]));?></td>
                <td class="campos" align="right">

                  <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($row[3], 2, '.', ','); ?>">

                  <div id="divVal<?php echo $row[0];?>" >
                    <?php  
                      echo number_format($row[3], 2, '.', ',');
                    ?>
                  </div>
                    <!-- Modificar los valores -->
                          <table id="tab<?php echo $row[0];?>" style="padding: 0px;  margin-top: -10px; margin-bottom: -10px;" >
                            <tr>
                              <td>
                                <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" class="fo9rm-control in9put-sm" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>')" value="<?php echo number_format($row[3], 2, '.', ','); ?>" required>
                              </td>
                              <td>
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>');" >
                                  <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                </a>
                              </td>
                              <td>
                                <a href="#<?php echo $row[0];?>" onclick="javascript:cancelarModificacion(<?php echo $row[0];?>);" >
                                  <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                </a>
                              </td>
                            </tr>
                          </table>

                    <script type="text/javascript">
                       var id = "<?php echo $row[0];?>";  
                       var idValorM = 'valorMod'+id;
                       var idTab = 'tab'+id;

                       $("#"+idTab).css("display", "none");

                    </script>

                </td>
                  
              </tr>
          <?php 
                }
              }
          ?>


            </tbody>
          </table>

        </div>
       
      </div> <!-- Cierra clase table-responsive contTabla  -->

<!-- Botones de consulta -->
    <div class="col-sm-2" >
                <table class="tablaC table-condensed" style="margin-left: -30px" >
                    <thead>
                        <th>
                            <h2 class="titulo" align="center">Consultas</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas">
                                    <a href="#">
                                        BALANCE POR <br>FUENTES
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas">
                                    <a href="#"> 
                                        APROPIACIÓN POR RUBRO
                                    </a>
                                </div>
                            </td>
                        </tr> 
                    </tbody>
                </table>
      </div>
      
    </div>
   
    </div> <!-- Cierra clase col-sm-10 text-left -->
    
  </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->

<!-- Divs de clase Modal para las ventanillas de eliminar. -->
<div class="modal fade" id="myModal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Detalle Solicitud?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
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

  <div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
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
<!-- Modales para eliminación -->

<!-- Divs de clase Modal para las ventanillas de modificar. -->
  <!-- Mensaje de modificación exitosa. -->
  <div class="modal fade" id="ModificacionConfirmada" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarConf" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<!-- Error al modificar el valor al ser superior al saldo-->
  <div class="modal fade" id="myModalAlertMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>


  <!-- Mensaje dato a modificar no es válido. -->
  <div class="modal fade" id="ModificacionNoValida" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>El dato a modificar no es válido.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarNoVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

 <!-- Mensaje de fallo en la modificación. -->
  <div class="modal fade" id="ModificacionFallida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido modificar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnModificarFall" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!-- Modales para modificación -->


<!-- Modal de alerta. El valor ingresado no es numérico.  -->
<div class="modal fade" id="myModalAlert" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de alerta. No se a seleccionado en el concepto.  -->
<div class="modal fade" id="myModalAlert2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione un concepto válido.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptCon" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
  <div class="modal fade" id="myModalAlertModInval" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValModInval" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error, el rubro y la fuente ya existen, no pueden seleccionarse.  --> 
  <div class="modal fade" id="myRubFueRepetido" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No es posible seleccionar este conjunto de rubro y fuente, esta opción ya existe. Verifique de nuevo.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnNoRubFue" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error, el rubro no ha sido seleccionado.  --> 
  <div class="modal fade" id="myRubroVacio" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe seleccionar primero un rubro.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnRubVac" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="modDesBal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No puede abandonar este formulario ya que las fuentes no están balanceadas. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnDesBal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="mdlFaltaRubro" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No puede abandonar este formulario ya que las fuentes no están balanceadas. Verifique nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnFaltaRubro" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>



  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<?php require_once 'footer.php'; ?>

<script type="text/javascript">
  $('#AceptVal').click(function(){ 
    $("#valor").val('').focus();
  });
</script>

<script type="text/javascript">
  $('#AceptCon').click(function(){ 
    $("#valor").val('');
    $("#concepto").focus();
  });
</script>


<!-- Función para la eliminación del registro. -->
<script type="text/javascript">
      function eliminarDetComp(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_APROPIACION_INICIALJson.php?id="+id,
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
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'registrar_GF_APROPIACION_INICIAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'registrar_GF_APROPIACION_INICIAL.php';
      });
    
  </script>
<!-- Fin funciones eliminar -->


<!-- Función para la modificación del registro. -->
<script type="text/javascript">

  function modificarDetComp(id)
  {

    if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != ""))
    {
      var cambiarTab = 'tab'+$("#idPrevio").val();
      var cambiarDiv = 'divVal'+$("#idPrevio").val();
      var cambiarOcul = 'valOcul'+$("#idPrevio").val();
      var cambiarMod = 'valorMod'+$("#idPrevio").val();

      if($("#"+cambiarTab).is(':visible'))
      {
            
        $("#"+cambiarTab).css("display", "none");
        $("#"+cambiarDiv).css("display", "block");
        $("#"+cambiarMod).val($("#"+cambiarOcul).val());


      }

    }
       
    var idValor = 'valorMod'+id;
    var idDiv = 'divVal'+id;
    var idModi = 'modif'+id;
    var idTabl = 'tab'+id;

    $("#"+idDiv).css("display", "none");
    $("#"+idTabl).css("display", "block");

    $("#idActual").val(id);

    if($("#idPrevio").val() != id)
      $("#idPrevio").val(id);

     
  }

</script>



<script type="text/javascript">
      function cancelarModificacion(id) 
      {

        var idDiv = 'divVal'+id;
        var idTabl = 'tab'+id;
        var idValorM = 'valorMod'+id;
        var idValOcul = 'valOcul'+id;

        $("#"+idDiv).css("display", "block");
        $("#"+idTabl).css("display", "none");
        $("#"+idValorM).val($("#"+idValOcul).val());

   }
  </script>



<script type="text/javascript">
      function guardarModificacion(id) 
      {

        var idDiv = 'divVal'+id;
        var idTabl = 'tab'+id;
        var idCampoValor = 'valorMod'+id;
        var idValOcul = 'valOcul'+id;

        var valor = $("#"+idCampoValor).val();
        var valor = valor.replace(/\,/g,'');
       
        var form_data = { id_val: id, valor: valor};
        $.ajax({
          type: "POST",
          url: "json/modificar_GF_DETALLE_COMPROBANTE_PPTALJson.php",
          data: form_data,
          success: function(response)
          {
            if(response != 0)
            {
              $("#ModificacionConfirmada").modal('show');
            }
            else
            {
              $("#ModificacionFallida").modal('show');
            }
          }
        });
   }

  </script>

   <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
  <script type="text/javascript">
    function verificarValor(id_txt,id_rubFue)
    { 
      var idValMod = "valorMod"+id_txt;
      var validar = $("#"+idValMod).val();
      validar = validar.replace(/\,/g,'');

      if((isNaN(validar)) || (validar == 0) || (validar == ""))
      {
        $("#myModalAlertModInval").modal('show');
      }
      else
      {
        guardarModificacion(id_txt);
      }
    }

  </script>


  <script type="text/javascript">
      function modal()
      {
         $("#Modificacion").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#btnModificarConf').click(function()
      {
        document.location.reload();
      });
    
  </script>

  <script type="text/javascript">
    
      $('#btnModificarFall').click(function(){
      });
    
  </script>
<!-- Fin funciones modificar -->

  <script type="text/javascript">
    
      $('#btnNoRubFue').click(function()
      {
        $("#rubro").val("");
        $("#rubroOcul").val("");
        $("#select2-rubro-container").text("Rubro");

        $("#fuente").val("");

      });
    
  </script>

   <script type="text/javascript">
    
      $('#btnRubVac').click(function()
      {
        $("#fuente").val("");

        //document.getElementById('').focus();
        $("#select2-rubro-container").css("box-shadow", "0 2px 10px rgba(213,233,249,1)"); //Estaba 66,129,255. Ahora este rgb(213, 233, 249)
        $("#select2-rubro-container").css("border", "3px solid rgba(213,233,249,0.8)")
      });
    
  </script>


    <script type="text/javascript">
                  
      $(document).ready(function()
      {
       /* $("#").blur(function()
        {
          alert("gg");
          //$("span").text("Blur / Out of focus Established"); 
        }); */
      });
    </script>

    <script type="text/javascript">
      
      $(function()
      {
        $('body').click(function()
        {
          if($("#myRubroVacio").css('display') == 'none')
          {

           $("#select2-rubro-container").css("box-shadow", ""); //estaba 5px 240,248,255
           $("#select2-rubro-container").css("border", "")
          //alert('fff');
        }
        });
          
        

        $('#select2-rubro-container').click(function(event)
        {
          event.stopPropagation();
        });
      });

    </script>

  

<!-- Validar el campo valor al guardar el dato. -->
<script type="text/javascript">

  function validarValor() 
      {
        if($("#rubro").val() != "")
        {
          var valor = $("#valor").val();
          valor = valor.replace(/\,/g,'');

          if((isNaN(valor)) || (valor == 0 ) || (valor == ""))
          {
            $("#myModalAlert").modal('show');
            return false;
          }
          else
          {
            return true; 
          }
        }
        else
        {
          $("#mdlFaltaRubro").modal('show');
          return false;

        }
        


      }


</script>

 <script type="text/javascript">
    
      $('#AceptValModInval').click(function()
      {
        var id_mod = "valorMod"+$("#idActual").val();
        var id_ocul = "valOcul"+$("#idActual").val();
        $("#"+id_mod).val($("#"+id_ocul).val()).focus();
      });
    
  </script>

<script type="text/javascript">
    
      $('#btnDesBal').click(function()
      {
  $("#rubro").focus();
      });
    
  </script>

  <script type="text/javascript">
    
      $('#btnFaltaRubro').click(function()
      {
  $("#rubro").focus();
      });
    
  </script>

  


</body>
</html>