<?php 
#27/04/2018 |Erica G. |Busqueda
#Modificado 06/02/2017 15:22 Ferney Pérez
#Modificado 26/01/2017 6:52 Ferney Pérez
	require_once('Conexion/conexion.php');
  require_once('estructura_apropiacion.php');

  require_once 'head_listar.php'; 
  @session_start();
  require_once('Conexion/ConexionPDO.php'); 
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$anno = $_SESSION['anno'];
  $numero = "";
  $fecha = "";
  $descripcion = "";
    $tc = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE compania= $compania AND clasepptal = 11 and tipooperacion = 1");
  $tc = $tc[0][0];
  if(!empty($_SESSION['id_compr_pptal']))
  {
    $queryGen = "SELECT detComP.id_unico, con.nombre, rub.nombre, detComP.valor, rubFue.id_unico   
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      where detComP.comprobantepptal =".$_SESSION['id_compr_pptal'];
    $resultado = $mysqli->query($queryGen);


    $queryCompro = "SELECT id_unico, numero, fecha, descripcion FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_compr_pptal'];
    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];

    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
  
    $fecha = $dia."/".$mes."/".$anio;

    //Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT id_unico, numero     
      FROM gf_comprobante_pptal 
      WHERE tipocomprobante = $tc  
      AND estado = 1 
      AND id_unico != ".$_SESSION['id_compr_pptal']."
      ORDER BY numero";
    $numeroSoli = $mysqli->query($queryNumSol);

  }

  //Consulta para listado de Número Solicitud.
  $queryNumS = "SELECT id_unico, numero     
  FROM gf_comprobante_pptal 
  WHERE tipocomprobante = $tc  
  AND estado = 1 
  ORDER BY numero";
  $numeroSol = $mysqli->query($queryNumS);


  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto";
  $concepto = $mysqli->query($queryCon);
  
  $arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
  
  foreach ($arr_sesiones_presupuesto as $index => $value)
  {
    if($value != 'id_compr_pptal')
    {
    	unset($_SESSION[$value]);
    }
  }

?>

<title>Aprobar Solicitud</title>

<link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<style type="text/css">
  .area
  { 
    height: auto !important;  
  }  

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

   /*Esto permite que el texto contenido dentro del div
  no se salga de las medidas del mismo.*/
  .acotado
  {
    white-space: normal;
  }

  .itemListado
  {
    margin-left:5px;
    margin-top:5px;
    width:150px;
    cursor:pointer;
  }

  #listado 
  {
    width:250px;
    height:120px;
    overflow: auto;
    background-color: white;
  }

</style>
 
<script src="js/jquery-ui.js"></script> 
<script type="text/javascript">

    $(document).ready(function ()
    {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $("#fecha").datepicker({changeMonth: true}).val();
       
    });
</script>
</head>
<body>

  <input type="hidden" id="idComP" value="<?php echo $_SESSION['id_compr_pptal'];?>">

<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

   <!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Aprobar Solicitud</h2>


<div class="col-sm-10">
            <div class="client-form contenedorForma"  style=""> 
                            <!-- Formulario de comprobante PPTAL -->
                            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_APROBAR_COMPROBANTE_PPTALJson.php">

                              <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                              </p>
                              <!-- Primer fila: Núemro de solicitud y fecha -->
                              <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">

                                <label for="codigo" class="col-sm-2 control-label" align="left"><strong style="color:#03C1FB;">*</strong>Número Solicitud:</label>
                                 <select name="codigo" id="codigo" class="form-control input-sm col-sm-3" title="Seleccione un número de solicitud" style="width:180px;" required>
              <?php 
                if(!empty($_SESSION['id_compr_pptal']) && ($rowComp == true))
                {
                  echo '<option value="'.$id.'">'.$numero.'</option>';
                  echo '<option value="">Número Solicitud</option>';
                  while($rowNumSuli = mysqli_fetch_row($numeroSoli))
                  {
                    echo '<option value="'.$rowNumSuli[0].'">'.$rowNumSuli[1].'</option>';
                  }

                }
                else
                {
              ?>
                    <option value="" selected="selected">Número Solicitud</option>
              <?php
                  
                    while($rowNumS = mysqli_fetch_row($numeroSol))
                    {
              ?>
                    <option value="<?php echo $rowNumS[0]; ?>"><?php echo $rowNumS[1];?></option>
              <?php 
                    }

                 }   
              ?>
                  </select> 

                  
                  <input name="numero" type="hidden" value="<?php echo $numero; ?>">


<!-- Al seleccionar un número de solcitud, cargará  -->
<script type="text/javascript">

   $(document).ready(function()
     {  
        $("#buscarDisp").change(function()
        {

          if(($("#buscarDisp").val() == "")||($("#buscarDisp").val() == 0))
          { 
            var form_data = { estruc: 1};
            $.ajax({
              type: "POST",
              url: "estructura_aprobar_solicitud.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.
          }
          else
          {

            var form_data = { estruc: 2, id_comp:+$("#buscarDisp").val() };
            $.ajax({
              type: "POST",
              url: "estructura_aprobar_solicitud.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

          } //Cierre else.
                        
        });//Cierre change.
     });//Cierre Ready.

</script>
        

                      <label for="fecha" class="col-sm-2 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                      <input class="col-sm-3 input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha;?>" readonly="readonly" required>


      <script type="text/javascript"> //Aquí $_SESSION['id_compr_pptal']
        
            function traerNum()
            {
              var form_data = { estruc: 3, id_comp:+$("#seleccionar").val() };
              $.ajax({
                type: "POST",
                url: "estructura_aprobar_solicitud.php",
                data: form_data,
                success: function(response)
                {
                  document.location.reload();                             
                }//Fin succes.
              }); //Fin ajax.

            } 

      </script>

      <script type="text/javascript">
// Al dar click fuera del input buscar se limpia el input y se oculta el div de resultados.
        $(document).ready(function(){
 
          $(document).click(function(e){
            if(e.target.id!='buscarSol')
              $('#buscarSol').val('');
              $('#listado').fadeOut();
            });
 
        });

      </script>
      <div class="col-sm-2" >
<select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:250px;">
                    <option value="">Buscar</option>
                    <?php
                     $reg = "SELECT
                            cp.id_unico,
                            cp.numero,
                            cp.fecha,
                            tcp.codigo

                          FROM
                            gf_comprobante_pptal cp
                          LEFT JOIN
                            gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                          LEFT JOIN
                            gf_tercero tr ON cp.tercero = tr.id_unico 
                          WHERE cp.parametrizacionanno = $anno AND tcp.clasepptal = 12 AND tcp.tipooperacion=1  AND tcp.vigencia_actual = 1 ORDER BY cp.numero DESC";
                    $reg = $mysqli->query($reg);
                    while ($row1 = mysqli_fetch_row($reg)) {
                        $date = new DateTime($row1[2]);
                        $f = $date->format('d/m/Y');
                        $sqlValor = 'SELECT SUM(valor) 
                                FROM gf_detalle_comprobante_pptal 
                                WHERE comprobantepptal = ' . $row1[0];
                        $valor = $mysqli->query($sqlValor);
                        $rowV = mysqli_fetch_row($valor);
                        $v = ' $' . number_format($rowV[0], 2, '.', ',');
                        ?>
                        <option value="<?php echo $row1[0] ?>"><?php echo $row1[1] . ' ' . mb_strtoupper($row1[3]) . ' ' . $f . ' ' . $v ?>
<?php } ?>
                </select>
                              </div>
                              </div> <!-- cierra inline -->

    

                              <div class="form-group form-inline" style="margin-left: 5px;">

                                <label for="nombre" class="col-sm-2 control-label" style="margin-top: -20px;" >Descripción:</label>
                                <textarea class="col-sm-3" style="margin-top: -20px; margin-bottom: -20px; width:250px; height: 50px; width:180px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción" readonly="readonly" onkeypress="return txtValida(event,'num_car')" ><?php echo ucwords(strtolower($descripcion)); ?></textarea> 
                              <!-- </div> -->

                              <label for="mostrarEstado" class="col-sm-2 control-label" style="margin-top: -20px;" >Estado:</label>
                                <input class="col-sm-1 input-sm" type="text" name="mostrarEstado" id="mostrarEstado" class="form-control" style="width:100px; margin-top: -20px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 

                                <input type="hidden" value="2" name="estado"> <!-- Estado 2, aprobada -->


                              <div class="col-sm-2" >

                                <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: -20px;" title="Aprobar" ><li class="glyphicon glyphicon-ok"></li></button> <!-- Aprobar -->

                              </div> 

                              <div class="col-sm-1" > 
                                <button type="button" id="siguiente" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: -20px;" title="Siguiente" >
                                  <li class="glyphicon glyphicon-arrow-right"></li>
                                </button> <!--  Siguiente -->
                              </div>

                              </div> <!-- Cierra inline -->

                              <input type="hidden" name="MM_insert" >
                            </form>

                          </div> <!-- Cierra clase client-form contenedorForma -->
</div>

<?php 
  if(!empty($_SESSION['nuevo_pptal']))
  {
 ?>
  <script type="text/javascript">

    $(document).ready(function()
    {
      $("#btnGuardarComp").prop("disabled", true);
      //$("#siguiente").prop("disabled", true);
    });
  </script>

 <?php
  }
  else
  {
 ?>
  <script type="text/javascript">

    $(document).ready(function()
    {
      $("#btnGuardarComp").prop("disabled", false);
      $("#siguiente").prop("disabled", true);
    });
  </script>

 <?php
  }
 ?>




<?php 
  if(!empty($_SESSION['nuevo_pptal']))
  {
 ?>

 <script type="text/javascript">
  $(document).ready(function()
  {
      var idComP = $("#idComP").val();
      var form_data = { estruc: 1, id_com: idComP };
      $.ajax({
        type: "POST",
        url: "estructura_modificar_eliminar_pptal.php",
        data: form_data,
        success: function(response)
        {
          if(response == 0)
          {
            $("#siguiente").prop("disabled", false);
          }
          else
          {
            $("#siguiente").prop("disabled", true);
          }
        }// Fin success.
      });// Fin Ajax;

  });
</script>



 <?php
  }
 ?>




<script type="text/javascript">
  $(document).ready(function()
  {
    $("#siguiente").click(function()
    {
      var idComP = $("#idComP").val();
      var form_data = { estruc: 1, id_com: idComP };
      $.ajax({
        type: "POST",
        url: "estructura_modificar_eliminar_pptal.php",
        data: form_data,
        success: function(response)
        {
          if(response == 0)
          {
            siguiente();
          }
          else
          {
            $("#mdlYaHayAfec").modal('show');
          }
        }// Fin success.
      });// Fin Ajax;

    });

  });
</script>

<script type="text/javascript">
   
  function siguiente()
  {
      var idComP = $("#idComP").val();
      var form_data = { sesion: 'id_comp_pptal_ED', numero: idComP, nuevo: 'nuevo_ED', valN: 2};
      $.ajax({
        type: "POST",
        url: "estructura_seleccionar_pptal.php",
        data: form_data,
        success: function(response)
        {
          document.location = 'EXPEDIR_DISPONIBILIDAD_PPTAL.php'; // Dejar
          //window.open('EXPEDIR_DISPONIBILIDAD_PPTAL.php'); // Comentar. Esto se usa solo para pruebas.
        }// Fin success.
      });// Fin Ajax;

  }
</script>

 

<input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">

<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-10" style="margin-top: 5px;">
          <div class="table-responsive contTabla" >
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Concepto</strong></td>
                <td class="cabeza"><strong>Rubro</strong></td>
                <td class="cabeza"><strong>Valor</strong></td>
                <td class="cabeza"><strong>Saldo Disponible</strong></td>
               
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Nombre</th>
                <th>Rubro</th>
                <th>Valor</th>
                <th>Saldo Disponible</th>
                                
              </tr>

            </thead>
            <tbody>
              
              <?php
                if(!empty($_SESSION['id_compr_pptal']) && ($resultado == true))
                {
                  while($row = mysqli_fetch_row($resultado))
                  {
                ?>
               <tr>
                <td class="oculto"><?php echo $row[0]?></td>
                <td class="campos">
                  <a class"" href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a class"" href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" ><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                </td>
                <td class="campos" align="left">
                  <div class="acotado">
                    <?php echo $row[1];?>
                  </div>
                </td>
                <td class="campos" align="left">
                  <div class="acotado">
                    <?php echo $row[2];?>
                  </div>
                </td>
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
                                <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" class="fo9rm-control in9put-sm" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>');" value="<?php echo number_format($row[3], 2, '.', ','); ?>" required>
                              </td>
                              <td>
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>','<?php echo $row[4];?>');" >
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
                <td class="campos" align="right">

                  <?php 
                      $saldoDisponible = apropiacion($row[4]) - disponibilidades($row[4]); 
                      echo number_format($saldoDisponible, 2, '.', ',');
                  ?>

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
      
 </div>
   
    </div> <!-- Cierra clase col-sm-10 text-left -->
    
  </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->



<!-- Divs de clase Modal para las ventanillas de eliminar. -->
<div class="modal fade" id="myModal" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
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

  <div class="modal fade" id="myModal2" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
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
        <p>El valor ingresado es superior al saldo disponible.</p>
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
  <div class="modal fade" id="ModificacionNoValida" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
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

<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
  <div class="modal fade" id="myModalAlertModSuperior" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor a modificar no puede ser superior al valor existente para aprobar. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValModSup" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mdlYaHayAfec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Este comprobante ya tiene afectación.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnYaHayAfec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
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
                  url:"json/eliminar_GF_DETALLE_COMPROBANTE_PPTALJson.php?id="+id,
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
        document.location = 'APROBAR_COMPROBANTE_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'APROBAR_COMPROBANTE_PPTAL.php';
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
        valor = valor.replace(/\,/g,''); //Elimina la coma que separa los miles.


                      if( ($("#"+idCampoValor).val() == "") || ($("#"+idCampoValor).val() == 0))
                      { 
                        $("#ModificacionNoValida").modal('show');
                        $("#"+idCampoValor).val($("#"+idValOcul).val());
                      }
                      else
                      {
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
                              afuera();
                            }
                            else
                            {
                              $("#ModificacionFallida").modal('show');
                            }
                              
                          }
                        });
                      }

   }
  </script>

   <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
                <script type="text/javascript">

                function verificarValor(id_txt,id_rubFue)
                {
                  var resVal = 0; 
                  var idValMod = "valorMod"+id_txt;
                  var validar = $("#"+idValMod).val();

                  var id_ocul = "valOcul"+id_txt;
                  var valOriginal = $("#"+id_ocul).val();

                  validar = parseFloat(validar.replace(/\,/g,'')); //Elimina la coma que separa los miles.
                  valOriginal = parseFloat(valOriginal.replace(/\,/g,''));

                  if((isNaN(validar)) || (validar == 0) || (validar == ""))
                  {
                    $("#myModalAlertModInval").modal('show');
                  }
                  else if(valOriginal < validar)
                  {
                    $("#myModalAlertModSuperior").modal('show');
                  }
                  else
                  {
                    
                    var form_data = { proc: 3, id_rubFue: id_rubFue};
                    $.ajax({
                      type: "POST",
                      url: "estructura_comprobante_pptal.php",
                      data: form_data,
                      success: function(response)
                      {         
                        resVal = parseFloat(response);        
                        if(resVal < validar)
                        {
                          $("#myModalAlertMod").modal('show');
                        }
                        else
                        {
                          guardarModificacion(id_txt);
                        }
                      } //Fin success.
                    }); //Fin Ajax.
                  } //Fin de If. 
                 

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
    //Si se ingresan valores diferentes a los numéricos en alguna de las casillas 
    // de la lista para su modificación.
      $('#AceptValModInval').click(function()
      {
        var id_mod = "valorMod"+$("#idActual").val();
        var id_ocul = "valOcul"+$("#idActual").val();
        $("#"+id_mod).val($("#"+id_ocul).val()).focus();
      });
  </script>

  <script type="text/javascript">
    //Si se ingresan valores superiores a los valores para aprobar en alguna de las casiilas 
    // de la lista para su modificación.
      $('#AceptValModSup').click(function()
      {
        var id_mod = "valorMod"+$("#idActual").val();
        var id_ocul = "valOcul"+$("#idActual").val();
        $("#"+id_mod).val($("#"+id_ocul).val()).focus();
      });
  </script>

  

  <script type="text/javascript">
    
      $('#btnModificarFall').click(function(){
      });
    
  </script>

  <script type="text/javascript">
    
      $('#btnModificarNoVal').click(function(){
      });
    
  </script>
<!-- Fin funciones modificar -->

<script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>


    <script>
           $(".select2_single").select2();
    </script>

</body>
</html>