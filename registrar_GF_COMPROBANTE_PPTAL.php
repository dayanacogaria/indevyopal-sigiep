<?php 
#################MODIFICACIONES###############################################################
#11/05/2017 |ERICA G. |VALIDACION FECHAS, CIERRE PERIODO MODIFICACION, GUARDADO DEL SALDO DISPONIBLE
#24/04/2017 | Erica G. | AÑADIO FORMATO TILDES Y BUSQUEDA
#Modificado 06/02/2017 15:22 Ferney Pérez
#Modificado 26/01/2017 9:32 Ferney Pérez
###############################################################################################
require_once('Conexion/conexion.php');
require_once('estructura_apropiacion.php');
require_once 'head_listar.php'; 
$anno = $_SESSION['anno'];
  if(!empty($_SESSION['id_comprobante_pptal']))
  {
    $queryGen = "SELECT detComP.id_unico, con.nombre, CONCAT(rub.codi_presupuesto,' - ',rub.nombre), detComP.valor, rubFue.id_unico , 
        detComP.saldo_disponible   
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      where detComP.comprobantepptal = ".$_SESSION['id_comprobante_pptal'];
    $resultado = $mysqli->query($queryGen);

    $queryCompro = "SELECT id_unico, numero, fecha, descripcion FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comprobante_pptal'];
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
  }

  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto
  WHERE clase_concepto = 2 AND parametrizacionanno = $anno";
  $concepto = $mysqli->query($queryCon);
  
  $arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
  
  foreach ($arr_sesiones_presupuesto as $index => $value)
  {
    if($value != 'id_comprobante_pptal')
    {
    	unset($_SESSION[$value]);
    }
  }

?>

<title>Registrar Solicitud Disponibilidad</title>

<!-- Librerías en línea para el calendario. Las flechas del calendario, siguiente y anterior, no se muestran si estas librerías están en local -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 

<script type="text/javascript">

 $(document).ready(function()
  {
    //Código para mostrar la fecha actual.
    var fecha = new Date();
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1;

    if(dia < 10)
    {
      dia = "0" + dia;
    }

    if(mes < 10)
    {
      mes = "0" + mes;
    }

    var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();

    //Código para mostrar los meses y los días en español.
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Anterior',
        nextText: 'Siguiente',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        changeYear:true,
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#calendario").datepicker({changeMonth: true}).val(); //Pone la fecha actual en el calendario por defecto.
    $("#fechaActual").val(fecAct);

  });

</script>

 <!-- Script para ocultar y mostrar según sea el caso los elementos del formulario. --> 
  <script type="text/javascript">
                
    $(document).ready(function()
    {
      $("#guardarComp").prop("disabled", true);
      $("#nuevoReg").click(function()
      {

        $("#calendario").css("display", "block");
        $("#fechaReg").css("display", "none");

        $("#descripcionReg").css("display", "none");
        $("#descripcion").css("display", "block");

        $("#codigoReg").css("display", "none");
        $("#codigo").css("display", "block");

        $("#guardarComp").prop("disabled", false);

        $("#nuevoReg").css("display", "none");
        $("#cancelarNuevo").css("display", "block");

        $("#siguiente").prop("disabled", true);
        $("#btnModificarComp").prop("disabled", true);

        var form_data = { is_ajax: 1, proc: 1 }; //Calcular el número de solicitud.
        $.ajax({
          type: "POST",
          url: "estructura_comprobante_pptal.php",
          data: form_data,
          success: function(response)
          { 
            response = response.trim();
            $('#codigo').val(response);
          }
        }); //Cierra Ajax
      
      }); //Fin de click.

       });//Cierre Ready.

</script>

 <!-- Script para ocultar y mostrar según sea el caso los elementos del formulario. --> 
  <script type="text/javascript">
      //cancelarNuevo
       $(document).ready(function()
    {

      $("#cancelarNuevo").click(function()
      {
        $("#calendario").css("display", "none");
        $("#fechaReg").css("display", "block");

        $("#descripcionReg").css("display", "block");
        $("#descripcion").css("display", "none");
        $("#descripcion").val("");

        $("#codigoReg").css("display", "block");
        $("#codigo").css("display", "none");

        $("#guardarComp").prop("disabled", true);

        $("#nuevoReg").css("display", "block");
        $("#cancelarNuevo").css("display", "none");

        var bloqSig = $("#bloquSigui").val();
        if(bloqSig == 0)
        {
          $("#siguiente").prop("disabled", false);
          $("#btnModificarComp").prop("disabled", false);
        }
      
      }); //Fin de click.

    });//Cierre Ready.

  </script>

<style type="text/css">
  .area
  { 
    height: auto !important;  
  }  

  .acotado
  {
    white-space: normal;
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
  <link rel="stylesheet" href="css/select2.css">
   <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>

</head>
<body>

<input id="id_com_ptal" value="<?php echo $_SESSION['id_comprobante_pptal'];?>" type="hidden">
<input id="fechaActual" type="hidden">
<input type="hidden" id="bloquSigui">

</div>

<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

   <!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Registrar Solicitud Disponibilidad</h2>


<div class="col-sm-10">
    <div class="client-form contenedorForma"  style=""> 

      <!-- Formulario de comprobante PPTAL -->
      <form name="comproPptal" id="comproPptal" class="form-horizontal" method="POST" onsubmit="asignaFecha();"  enctype="multipart/form-data" action="json/registrar_GF_COMPROBANTE_PPTALJson.php">

        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
          Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
        </p>
        
        <!-- Primer fila: Número de solicitud y fecha -->
        <div class="form-group form-inline" style="margin-top: 5px; margin-left: 5px;">

          <label for="codigo" class="col-sm-2 control-label" align="left"><strong style="color:#03C1FB;">*</strong>Número Solicitud:</label>

          <input class="col-sm-3 input-sm" type="text" name="codigo" id="codigo" class="form-control" maxlength="50" style="width:180px;" placeholder="Número solicitud" readonly="readonly" required>

          <input class="col-sm-3 input-sm" type="text" name="codigoReg" id="codigoReg" class="form-control" maxlength="50" style="width:180px;" placeholder="Número solicitud" readonly="readonly" value="<?php if(!empty($_SESSION['id_comprobante_pptal'])){ echo $numero;} ?>" required>
          <input  type="hidden" name="codigoRegO" id="codigoRegO" value="<?php if(!empty($_SESSION['id_comprobante_pptal'])){ echo $numero;} ?>" >
          


          <label class="col-sm-1 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>

          <input class="col-sm-1 input-sm" type="text" name="calendario" id="calendario" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" readonly="readonly" required>

          <input class="col-sm-1 input-sm" type="text" name="fechaReg" id="fechaReg" class="form-control" style="width:100px;" title="Fecha" readonly="readonly" value="<?php if(!empty($_SESSION['id_comprobante_pptal'])){ echo $fecha;} ?>" required>

          <input type="hidden" name="fecha" id="fecha">

         

          <div class="col-sm-1" align="center">

            <a id="nuevoReg" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo">
              <li class="glyphicon glyphicon-plus"></li>
            </a> <!-- Nuevo -->

         <a id="cancelarNuevo" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Cancelar nuevo"><li class="glyphicon glyphicon-remove"></li></a> <!-- Nuevo -->

          </div>

          <div class="col-sm-1" > <!-- Botón submit -->
            <button type="submit" id="guardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Guardar" >
              <li class="glyphicon glyphicon-floppy-disk"></li>
            </button> <!--  Guardar -->
          </div>
          <div class="col-sm-1" style="margin-top: -20px; margin-left: -8px;"> 
                <button type="button" id="btnModificarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Modificar Comprobante Presupuestal" >
                  <li class="glyphicon glyphicon-pencil"></li>
                </button> <!--  modificar -->
            </div>
          <script type="text/javascript">
                  $(document).ready(function()
                  {
                    $("#btnModificarComp").click(function(){
                        var id = $("#id_com_ptal").val();
                        var fecha = $("#fechaReg").val();
                        var desc = $("#descripcionReg").val();
                        if(fecha=='' || fecha =='00/00/0000'){
                            $("#mdlAlertErrFec").modal('show');
                        } else {
                            var form_data = { proc: 6, fecha: fecha, id:id, desc:desc };
                            $.ajax({
                            type: "POST",
                            url: "estructura_comprobante_pptal.php",
                            data: form_data,
                            success: function(response)
                            {
                                console.log(response);
                                if(response == 1){
                                    $("#ModificacionConfirmada").modal('show');
                                } else {
                                    $("#ModificacionFallida").modal('show');
                                    
                                }
                            }
                            });
                        }
                        });
                  });
                </script>
          <div class="col-sm-1" style="margin-top: 0px; margin-left: 0px">
              <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                <li class="glyphicon glyphicon glyphicon-print"></li>
              </button> <!--Imprimir-->
            </div>
            <?php if(!empty($_SESSION['id_comprobante_pptal'])) { 
                $_SESSION['id_comp_pptal_ED']=$_SESSION['id_comprobante_pptal']; ?>
                <script type="text/javascript">
                  $(document).ready(function()
                  {
                    $("#btnImprimir").click(function(){
                      window.open('informesPptal/inf_Solicitud.php');
                    });
                  });
                </script>
            <?php   } else { ?>
                <script type="text/javascript">
                  $(document).ready(function()
                  {
                    $("#btnImprimir").prop("disabled",true);
                  });
                </script>
            <?php } ?>  
           <div class="col-sm-1" > <!-- Botón submit -->
            <button type="button" id="siguiente" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Siguiente" >
              <li class="glyphicon glyphicon-arrow-right"></li>
            </button> <!--  Guardar -->
          </div>

        </div> <!-- cierra inline -->
        
          <script>
                $("#calendario").change(function(){
                    //VALIDAR SI YA TUVO CIERRE LA FECHA
                    var fecha = $("#calendario").val();
                    var form_data = { case: 4, fecha: fecha };

                    $.ajax({
                    type: "POST",
                    url: "jsonSistema/consultas.php",
                    data: form_data,
                    success: function(response)
                    {
                        console.log(response);
                        if(response == 1){
                            $("#periodoC").modal('show');
                        } else {

                          fecha1();
                        }
                    }
                  }); 
                })
            </script>
            <script type="text/javascript">
          function fecha1(){
             
            var fecha = $("#calendario").val();
              var form_data = { estruc: 13, fecha: fecha, tipComPal: 6 };
              $.ajax({
                type: "POST",
                url: "consultasBasicas/validarFechas.php",
                data: form_data,
                success: function(response)
                {
                  if(response != 1)
                  {
                    $("#mdlAlertErrFec").modal('show');
                  }
                }// Fin success.
              });// Fin Ajax; 

        } 
            
          </script>
          <script>
                $("#fechaReg").change(function(){
                    //VALIDAR SI YA TUVO CIERRE LA FECHA
                    var fecha = $("#fechaReg").val();
                    var form_data = { case: 4, fecha: fecha };

                    $.ajax({
                    type: "POST",
                    url: "jsonSistema/consultas.php",
                    data: form_data,
                    success: function(response)
                    {
                        console.log(response);
                        if(response == 1){
                            $("#periodoC").modal('show');
                        } else {

                          fechaValidar();
                        }
                    }
                  }); 
                })
            </script>
            <script type="text/javascript">
          function fechaValidar(){
             
            var fecha = $("#fechaReg").val();
            var id = $("#id_com_ptal").val();
            var num = $("#codigoRegO").val();
              var form_data = { estruc: 14, fecha: fecha, tipComPal: 6 ,id:id, num :num};
              $.ajax({
                type: "POST",
                url: "consultasBasicas/validarFechas.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                  if(response != 1)
                  {
                    $("#mdlAlertErrFec").modal('show');
                  }
                }// Fin success.
              });// Fin Ajax; 

        } 
            
          </script>


        <?php 
          if(empty($_SESSION['id_comprobante_pptal']))
          {
        ?>

          <script type="text/javascript">
            $(document).ready(function()
            {
              $("#siguiente").prop("disabled", true);
              $("#btnModificarComp").prop("disabled", true);
            });
          </script>
        <?php 
          }
        ?>

 

<?php 
  if(!empty($_SESSION['id_comprobante_pptal']))
  {
?>

  <script type="text/javascript">
  $(document).ready(function()
  {
    var idComP = $("#id_com_ptal").val();

      var form_data = { estruc: 1, id_com: idComP };
      $.ajax({
        type: "POST",
        url: "estructura_modificar_eliminar_pptal.php",
        data: form_data,
        success: function(response)
        {
          response = parseInt(response);
          if(response == 0)
          {
            $("#siguiente").prop("disabled", false);
            $("#btnModificarComp").prop("disabled", false);
            
            $("#bloquSigui").val(0);
          }
          else
          {
            $("#siguiente").prop("disabled", true);
            $("#btnModificarComp").prop("disabled", true);
            $("#fechaReg").prop("disabled", true);
            $("#descripcionReg").prop("disabled", true);
            $("#bloquSigui").val(1);
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
      var idComP = $("#id_com_ptal").val();

      var form_data = { estruc: 1, id_com: idComP };
      $.ajax({
        type: "POST",
        url: "estructura_modificar_eliminar_pptal.php",
        data: form_data,
        success: function(response)
        {
          response = parseInt(response);
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
    var idComP = $("#id_com_ptal").val();
    var form_data = { sesion: 'id_compr_pptal', numero: idComP, nuevo: 'nuevo_pptal', valN: 2};
    $.ajax({
      type: "POST",
      url: "estructura_seleccionar_pptal.php",
      data: form_data,
      success: function(response)
      {
        document.location = 'APROBAR_COMPROBANTE_PPTAL.php'; // Dejar
        //window.open('APROBAR_COMPROBANTE_PPTAL.php');  // Comentar. Esto se usa solo para pruebas.
      }// Fin success.
    });// Fin Ajax;

  }

</script>

        <div class="form-group form-inline" style="margin-left: 5px; margin-top:25px">

          <label for="nombre" class="col-sm-2 control-label" style="margin-top: -20px;" >Descripción:</label>

          <textarea class="col-sm-3" style="margin-top: -20px; margin-bottom: -20px; width:250px; height: 50px; width:180px"  rows="2" name="descripcionReg" id="descripcionReg"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')"  ><?php if(!empty($_SESSION['id_comprobante_pptal'])){ echo $descripcion;} ?></textarea>

          <textarea class="col-sm-3" style="margin-top: -20px; margin-bottom: -20px; width:250px; height: 50px; width:180px" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ></textarea>

          <label for="mostrarEstado" class="col-sm-1 control-label" style="margin-top: -20px;" >Estado:</label>
          <input class="col-sm-1 input-sm" type="text" name="mostrarEstado" id="mostrarEstado" class="form-control" style="width:100px; margin-top: -20px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 

          <input type="hidden" value="1" name="estado"> <!-- Estado 1, solicitada. -->

<!--          <div class="col-sm-3" >  Buscar disponibilidad 
                <input class="input-sm" onkeypress="return txtValida(event,'num')" type="text" name="buscarDisp" id="buscarDisp" class="form-control" style="width:150px; margin-top: -20px; margin-bottom: 0px;" title="Buscar disponibilidad" maxlength="50" placeholder="Buscar Disponibilidad"> 
                <div id="listado" style="display: none; position: absolute; z-index: 100; margin-top: 0px;"></div>
                <input type="hidden" id="seleccionar">
          </div>-->
<div class="col-sm-3" style="margin-top: -35px;" > <!-- Buscar disponibilidad -->
                                        <label for="noDisponibilidad" class="control-label" style="margin-left:-60px"><right>Buscar Disponibilidad:</right></label>
                                        <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:280px">
                                            <option value="">Registro</option>
                                            <?php $reg = "SELECT
                                                    cp.id_unico,
                                                    cp.numero,
                                                    cp.fecha,
                                                    tcp.codigo,
                                                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                                                        OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                                                      (tr.razonsocial),
                                                      CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
                                                    tr.numeroidentificacion
                                                  FROM
                                                    gf_comprobante_pptal cp
                                                  LEFT JOIN
                                                    gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                                                  LEFT JOIN
                                                    gf_tercero tr ON cp.tercero = tr.id_unico 
                                                  WHERE tcp.clasepptal = 11 AND cp.parametrizacionanno = $anno ORDER BY cp.numero DESC";
                                            $reg = $mysqli->query($reg); 
                                            while ($row1 = mysqli_fetch_row($reg)) { 
                                                $date= new DateTime($row1[2]);
                                                $f= $date->format('d/m/Y');
                                                 $sqlValor = 'SELECT SUM(valor) 
                                                        FROM gf_detalle_comprobante_pptal 
                                                        WHERE comprobantepptal = '.$row1[0];
                                                $valor = $mysqli->query($sqlValor);
                                                $rowV = mysqli_fetch_row($valor);
                                                $v=' $'.number_format($rowV[0], 2, '.', ','); ?>
                                                <option value="<?php echo $row1[0]?>"><?php echo $row1[1].' '. mb_strtoupper($row1[3]).' '.$f.' '.ucwords(mb_strtolower($row1[4])).' '.$row1[5].$v?>
                                            <?php }?>
                                        </select>
                                    </div> 

        </div> <!-- Cierra inline -->

        <input type="hidden" name="MM_insert" >
      </form> <!-- Cierra Formulario de comprobante PPTAL -->

    </div> <!-- Cierra clase client-form contenedorForma -->
</div>

<!-- Script para cargar datos en el combo select Rubro a partir del lo que se seleccione en el combo select Concepto. -->
<script type="text/javascript">
    $(document).ready(function()
    {
      $("#buscarDisp").change(function()
      { 
        if(($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0))
        {
            traerNum();
        } 
      });
    });
</script>
<script type="text/javascript"> 
    function traerNum()
    {
      var form_data = { sesion: 'id_comprobante_pptal', nuevo: 'nuevo_ED',  numero: $("#buscarDisp").val(),  valN: 1};
      $.ajax({
        type: "POST",
        url: "estructura_seleccionar_pptal.php",
        data: form_data,
        success: function(response)
        {
          if(response == 1)
          {
            document.location.reload();
          }
        }
      });  
    } 
</script>      

      <script type="text/javascript">

        $(document).ready(function(){
 
          $(document).click(function(e){
            if(e.target.id!='buscarDisp')
              $('#buscarDisp').val('');
              $('#listado').fadeOut();
            });
 
        });

      </script>

  <?php // Se están viendo los datos del número de solicitud.
    if(!empty($_SESSION['id_comprobante_pptal']))
    {
  ?>
    <script type="text/javascript">
    
      $("#calendario").css("display", "none");
      $("#fechaReg").css("display", "block");

      $("#descripcionReg").css("display", "block");
      $("#descripcion").css("display", "none");

      $("#codigoReg").css("display", "block");
      $("#codigo").css("display", "none");
      $("#cancelarNuevo").css("display", "none");
                             
    </script>

  <?php
    }
    else //Se abre la página y se ingresará un nuevo registro.
    {
  ?>
    <script type="text/javascript">
    
      $("#calendario").css("display", "block");
      $("#fechaReg").css("display", "none");

      $("#descripcionReg").css("display", "none");
      $("#descripcion").css("display", "block");

      $("#codigoReg").css("display", "none");
      $("#codigo").css("display", "block");
      $("#cancelarNuevo").css("display", "none");

    </script>

  <?php
    }
  ?>


<div class="col-sfm-10"> 

  <div class="client-form" >
    
    <!-- Formulario de detalle comprobante pptal -->
    <form name="formConRub" id="formConRub" class="form-inline" method="POST" onsubmit="return validarValor();"  enctype="multipart/form-data" action="json/registrar_GF_DETALLE_COMPROBANTE_PPTALJson.php"> 

      <div class="container-fluid">
      <div class="row" style="margin-top: -5px;">

      <!-- Combo-box Concepto -->
      <div class="col-sm-3 form-group form-inline">
        <div class="form-group form-inline" style="margin-top: 5px; margin-right: 0px;"  align="left">
          <label for="concepto" class="control-label"><strong class="obligado">*</strong>Concepto:</label>
        </div>
        <div class="form-group form-inline" style="margin-top: 5px; margin-right: 0px;"  align="left">
          <select name="concepto" id="concepto" class="form-control input-sm select2_single" title="Ingrese el concepto" style="width:150px;" required>
            <option value="" selected="selected" >Concepto</option>
          <?php
            while($rowCon = mysqli_fetch_row($concepto))
            {
          ?>
            <option value="<?php echo $rowCon[0]; ?>"><?php echo ucwords(mb_strtolower($rowCon[1])); ?></option>
          <?php 
            }
          ?>
          </select> 
        </div>
      </div>

      <!-- Combo-box Rubro -->
      <div class="col-sm-3">
        <div class="form-group " style="margin-top: 5px; margin-left: 0px;" align="left">
          <label for="rubro" class="control-label"><strong class="obligado">*</strong>Rubro:</label>
          <select name="rubro" id="rubro" class="form-control input-sm" title="Ingrese el rubro" style="width:150px;" required>
            <option value="">Rubro</option>
          </select> 
        </div>
      </div>

      <input type="hidden" id="rubroFuente" name="rubroFuente">
      <input type="hidden" id="conceptoRubro" name="conceptoRubro"> 

      <!-- Script para cargar datos en el combo select Rubro a partir del lo que se seleccione en el combo select Concepto. -->
      <script type="text/javascript">

        $(document).ready(function()
        {
          $("#concepto").change(function()
          { 
            var opcion = '<option val="">Rubro</option>';
            if( ($("#concepto").val() == "") || ($("#concepto").val() == 0))
            { 
              $('#rubro').html(opcion).fadeIn();
            }
            else
            {
              var form_data = {proc: 2, id_con: +$("#concepto").val(), fecha :$("#fechaReg").val()};
              $.ajax({
                type: "POST",
                url: "estructura_comprobante_pptal.php",
                data: form_data,
                success: function(response)
                {
                  if(response != 0)
                  {
                    opcion += response;
                    $('#rubro').html(opcion).fadeIn().focus();
                  }
                  else
                  {
                    opcion = '<option val="">No hay rubro</option>';
                    $('#rubro').html(opcion).fadeIn();
                  }
                }
              });
            }
          });
        });

      </script>

    <script type="text/javascript">
      $(document).ready(function()
      {
        $("#rubro").change(function()
        {
          if($("#rubro").val() != 0 && $("#rubro").val() != '')
          {
            var rubro = $("#rubro").val();
            var rubroFuente_conceptoRubro = rubro.split("/");
            $("#rubroFuente").val(rubroFuente_conceptoRubro[0]);
            $("#conceptoRubro").val(rubroFuente_conceptoRubro[1]);
          }
          else
          {
            $("#rubroFuente").val(0);
            $("#conceptoRubro").val(0);
          }  
        });
      });

  </script>


      <!-- Caja texto Valor -->
      <div class="col-sm-3 form-group form-inline" style="margin-top: 5px;">
        <table>
          <tr>
            <td style="padding: 3px;">
              <label for="valor" class="control-label">
                <strong style="color:#03C1FB;">*</strong>
                Valor:
              </label>
            </td>
              
            <td>
              <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>

            </td>
          </tr>
        </table>
          
          <input type="hidden" value="">

      </div>

      <!-- Evalúa que el valor ingresado no sea superior al saldo del Rubro. -->
      <script type="text/javascript">

        $(document).ready(function()
        {

          $("#valor").keyup(function()
          { 
            var valor = $("#valor").val();
            valor = parseFloat(valor.replace(/\,/g,''));

            if(($("#rubroFuente").val() == "") || ($("#rubroFuente").val() == 0))
            { 
              $("#myModalAlert2").modal('show');
            }
            else
            {
                var rubFue = $("#rubroFuente").val()
                var form_data = {estruc: 18, id_rubFue: rubFue, fecha:$("#fechaReg").val()};
                $.ajax({
                    type: "POST",
                    url: "jsonPptal/consultas.php",
                    data: form_data,
                    success: function (response)
                    {
                        console.log(response);
                        var resVal = 0;
                        respVal = parseFloat(response);
                        if (respVal < valor)
                        {
                            $("#myModalAlert").modal('show');
                        }
                        console.log(response);

                    }
                });
            }
                    
          }); 
        });

      </script>


                <!-- Botón guardar -->

            <div class="col-sm-1" align="left">
              <br/> <!-- En los eventos onfocus y onmouseover se hace el llamado de la función valSal que se encarga de hacer el envío de ajax al archivo de php que es el encargado de hacer el calculo del saldo del rubro fuente.  -->

              <button type="submit" id="btnConRub" onfocus="validaSaldo();" onmouseover="validaSaldo();" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: -12px;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->

              <input type="hidden" name="MM_insert" >
            </div>

            <?php
            if(!empty($_SESSION['id_comprobante_pptal']))
            {
            ?>

            <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnConRub").prop("disabled", false);
              $("#concepto").prop("disabled", false);
              $("#rubro").prop("disabled", false);
              $("#valor").prop("disabled", false);
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
              $("#btnConRub").prop("disabled", true);
              $("#concepto").prop("disabled", true);
              $("#rubro").prop("disabled", true);
              $("#valor").prop("disabled", true);
            });
            </script>
            
            <?php
            }
            ?>

          </div> <!-- Cierra clase row -->
        </div> <!-- Cierra la clase container fluid-->
      </form>
    </div>  <!-- cierra clase client-form contenedorForma -->
      
    <input type="hidden" id="idPrevio" value="">
    <input type="hidden" id="idActual" value="">

  </div>

  <input type="hidden" id="valSal">

<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-10" style="margin-top: -10px;">
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
        if(!empty($_SESSION['id_comprobante_pptal']) && ($resultado == true))
        {
          while($row = mysqli_fetch_row($resultado))
          {
      ?>
        <tr class="ocultarFilas">
          <td class="oculto"><?php echo $row[0]?></td>
          <td class="campos">
            <?php  
            if(!empty($_SESSION['id_comprobante_pptal']))
            {
            ##BUSCAR FECHA COMPROBANTE 
            $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comprobante_pptal'];
            $fc = $mysqli->query($fc);
            $fc = mysqli_fetch_row($fc);
            $fc = $fc[0];
            ##DIVIDIR FECHA
            $fecha_div = explode("-", $fc);
            $anio = $fecha_div[0];
            $mes = $fecha_div[1];
            $dia = $fecha_div[2];

            ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
            $ci="SELECT
            cp.id_unico
            FROM
            gs_cierre_periodo cp
            LEFT JOIN
            gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
            LEFT JOIN
            gf_mes m ON cp.mes = m.id_unico
            WHERE
            pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
            $ci =$mysqli->query($ci);
            if(mysqli_num_rows($ci)>0){ } else { 
                
                $sqlBuscarAfec = 'SELECT COUNT(cps.id_unico) 
					FROM gf_comprobante_pptal cp
					LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico
					LEFT JOIN gf_detalle_comprobante_pptal dcps ON dcps.comprobanteafectado = dcp.id_unico
					LEFT JOIN gf_comprobante_pptal cps ON dcps.comprobantepptal = cps.id_unico
					WHERE cp.id_unico = '.$_SESSION['id_comprobante_pptal'];
    		$buscarAfectacion = $mysqli->query($sqlBuscarAfec);
			$rowBA = mysqli_fetch_row($buscarAfectacion);
                $num = (int)$rowBA[0];
                if($num>0) { } else {
                ?>
            <div class="bloquea">
              <a class href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i> </a>
              <a class href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" ><i title="Modificar" class="glyphicon glyphicon-edit"></i></a>
            </div>
            <?php } } } ?>
          </td>
          <td class="campos" align="left">
            <div class="acotado">
              <?php echo ucwords(mb_strtolower($row[1]));?>
            </div>
          </td>
          <td class="campos" align="left">
            <div class="acotado">
              <?php echo ucwords(mb_strtolower($row[2]));?>
            </div>
          </td>
          <td class="campos" align="right">

                  <!-- Campo oculto para tener el valor del detalle como referencia en caso de haber una falla al momento de editar el valor y poder restaurarlo. -->
                  <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($row[3], 2, '.', ','); ?>">

                  <!-- Muestra el valor del detalle -->
                  <div id="divVal<?php echo $row[0];?>" >
                    <?php  
                      echo number_format($row[3], 2, '.', ',');
                    ?>
                  </div>
                    <!-- Modificar los valores -->
                          <table id="tab<?php echo $row[0];?>" style="padding: 0px;  margin-top: -10px; margin-bottom: -10px;" >
                            <tr>
                              <td> <!-- Input para capturar el valor a modificar. -->
                                <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" class="fo9rm-control in9put-sm" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>');"  value="<?php echo number_format($row[3], 2, '.', ','); ?>" required>
                              </td>
                              <td>

                                 <!-- Botón modificar. -->
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>','<?php echo $row[4];?>');" >
                                  <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                </a>
                              </td>
                              <td>
                                <!-- Botón cancelar modificar. -->
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
                    <!-- Saldo disponible -->
                  <?php 
                      
                      echo number_format($row[5], 2, '.', ',');
                  ?>

                </td>
                  
              </tr>
          <?php 
                }
              }
          ?>


            </tbody>
    </table>

    <script type="text/javascript">
      $(document).ready(function()
      {
        $("#cancelarNuevo").click(function()
        {
          $("#btnConRub").prop("disabled", false);
          $(".bloquea").css("display", "block");

          $(".ocultarFilas").show();

          $("#concepto").prop("disabled", false);
          $("#rubro").prop("disabled", false);
          $("#valor").prop("disabled", false);

        });
        
        $("#nuevoReg").click(function()
        {
          $("#btnConRub").prop("disabled", true);
          $(".bloquea").css("display", "none");

          $("#buscarDisp").val("");
          $("#listado").css("display", "none");
          /**/
          var id = $("#idActual").val();
          var idDiv = 'divVal'+id;
          var idTabl = 'tab'+id;
          var idValorM = 'valorMod'+id;
          var idValOcul = 'valOcul'+id;

          $("#"+idDiv).css("display", "block");
          $("#"+idTabl).css("display", "none");
          $("#"+idValorM).val($("#"+idValOcul).val()); 

          $(".ocultarFilas").hide();

          $("#concepto").prop("disabled", true);
          $("#rubro").prop("disabled", true);
          $("#valor").prop("disabled", true);

        });
      }); 

    </script>

        </div>
       
      </div> <!-- Cierra clase table-responsive contTabla  -->
      
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

<!-- Error al modificar el valor al ser superior al saldo disponible en los cajas del listado. -->
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

 <!-- Mensaje de fallo en la modificación (Campos listados). -->
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


<!-- Modal de alerta. El valor a ingresar en el formulario de concepto es mayor que el saldo.  -->
<div class="modal fade" id="myModalAlert" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es superior al saldo disponible.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptVal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de alerta. Los valores ingresados no son numéricos.  -->
<div class="modal fade" id="myModalInvalido" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptInval" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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

<!-- Modal de alerta. No se a seleccionado en el concepto.  -->
<div class="modal fade" id="mdlAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La fecha inválida, verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnAlertErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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
//Aceptar el valor es inválido.
  $('#AceptInval').click(function(){ 
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
    
    $('#ver1').click(function()
    {
      document.location = 'registrar_GF_COMPROBANTE_PPTAL.php';
    });
    
  </script>

  <script type="text/javascript">
    
    $('#ver2').click(function()
    {
      document.location = 'registrar_GF_COMPROBANTE_PPTAL.php';
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
  //Validado el valor ingresado por el usuario se ejecutará la consulta Update.
      function guardarModificacion(id) 
      {

        var idDiv = 'divVal'+id;
        var idTabl = 'tab'+id;
        var idCampoValor = 'valorMod'+id;
        var idValOcul = 'valOcul'+id;

        var valor = $("#"+idCampoValor).val();
        valor = valor.replace(/\,/g,'');


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
              }
              else
              {
                $("#ModificacionFallida").modal('show');
              }
            } //Fin success
          }); // Fin Ajax
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
        validar =  parseFloat(validar.replace(/\,/g,'')); //Elimina la coma que separa los miles.

        if((isNaN(validar)) || (validar == 0) || (validar == ""))
        {
          $("#myModalAlertModInval").modal('show');
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

    } //Fin función.

  </script>


  <script type="text/javascript">
      function modal()
      {
         $("#Modificacion").modal('show');
      }
  </script>
  

  <script type="text/javascript">
  //Confirmada la modificación (campos listados) se recarga la página.
      $('#btnModificarConf').click(function()
      {
        document.location.reload();
      });
    
  </script>


  <script type="text/javascript">
    //Si se ingresa un valor equivocado en alguna de las casiilas 
    // de la lista para su modificación.
      $('#AceptValMod').click(function()
      {
          var id_mod = "valorMod"+$("#idActual").val();
          var id_ocul = "valOcul"+$("#idActual").val();
          $("#"+id_mod).val($("#"+id_ocul).val()).focus();
      });
  </script>

  <script type="text/javascript">
    //Si se ingresan valores diferentes a los numéricos en alguna de las casiilas 
    // de la lista para su modificación.
      $('#AceptValModInval').click(function()
      {
        var id_mod = "valorMod"+$("#idActual").val();
        var id_ocul = "valOcul"+$("#idActual").val();
        $("#"+id_mod).val($("#"+id_ocul).val()).focus();
      });
  </script>

<!-- Fin funciones modificar -->

<script type="text/javascript">
  //$(document).ready(function(){}) document
  function asignaFecha() 
  {
    $("#fecha").val($("#calendario").val());
  }
</script>

<!-- Validar el campo valor del formulario concepto al guardar el dato. Esto es si se pega (copy/paste) un valor
en el campo sin digitarlo -->
<script type="text/javascript">
  function validarValor() 
  {
    var valor = $("#valor").val();
    var rubro = parseInt($("#rubroFuente").val());
    var valSal = $("#valSal").val();
    valor = valor.replace(/\,/g,'');
    if((isNaN(valor)) || (valor == 0) || (valor == ""))
    {
      $("#myModalInvalido").modal('show');
      return false;
    }
    else if(valSal != 2)
    {
      $("#myModalAlert").modal('show');
      return false;
    }
    else if(valSal == 2)
    {
      return true;
    }
  } //Fin función validarValor

</script>

<!-- Validar el campo valor del formulario concepto al guardar el dato. Esto es si se pega (copy/paste) un valor
en el campo sin digitarlo -->
<script type="text/javascript">

  function validaSaldo()
  {
    if(($("#rubroFuente").val() != 0) || ($("#rubroFuente").val() != ""))
    {
      var valor = $("#valor").val();
      valor = parseFloat(valor.replace(/\,/g,''));
      var rubFue = parseInt($("#rubroFuente").val());
      var form_data = { proc: 3, id_rubFue: rubFue }; //
          $.ajax({
            type: "POST",
            url: "estructura_comprobante_pptal.php",
            data: form_data,
            success: function(response)
            { 
              var res;
              res = parseFloat(response);
              if(res >= valor) 
              {
                $("#valSal").val(2);
              }
              else
              {
                $("#valSal").val(1);
              }
            }
          }); //Cierra ajax
    }
  }

</script>
<script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>


    <script>
        $(".select2_single").select2();                    
    </script>
<script type="text/javascript">
      $('#btnAlertErrFec').click(function()
      {
        var fecAct = $("#fechaActual").val();
        $("#calendario").val("").focus();
        $("#fechaReg").val("").focus();
      });
  </script>

  
    <!--CIERRE 
    ###BUSCAR EL CIERRE MAYOR
    --->
    <?php

    if(!empty($_SESSION['id_comprobante_pptal']))
    {
    ##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comprobante_pptal'];
    $fc = $mysqli->query($fc);
    $fc = mysqli_fetch_row($fc);
    $fc = $fc[0];
    ##DIVIDIR FECHA
    $fecha_div = explode("-", $fc);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];

    ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
    $ci="SELECT
    cp.id_unico
    FROM
    gs_cierre_periodo cp
    LEFT JOIN
    gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
    LEFT JOIN
    gf_mes m ON cp.mes = m.id_unico
    WHERE
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
    $ci =$mysqli->query($ci);
    if(mysqli_num_rows($ci)>0){ ?>
    <script>
    $(document).ready(function()
    {
    $("#concepto").prop("disabled", true);
    $("#rubro").prop("disabled", true);
    $("#btnConRub").prop("disabled", true);
    $("#valor").prop("disabled", true);
    //$("#fecha").prop("disabled", true);
    $("#descripcionReg").prop("disabled", true);
    $("#btnModificar").prop("disabled", true);
    $("#siguiente").prop("disabled", true);
    $("#btnModificarComp").prop("disabled", true);
    
    });
    </script>
    <?php 
    } else { ?>
<script>
    $(document).ready(function()
    {
       $("#fecha").prop("disabled",false);
       $("#fechaVen").prop("disabled",false);
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Anterior',
        nextText: 'Siguiente',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        changeYear: true,
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        
        $("#fechaReg").datepicker({changeMonth: true}).val();
        
    })
</script>
    <?php } }
    
    ?>
 <!-- Modal de alerta. Periodo para la fecha ya ha sido cerrado.  -->
    <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Periodo ya ha sido cerrado, escoja nuevamente la fecha</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $('#periodoCA').click(function(){ 
        $("#calendario").val("").focus();
        $("#fechaReg").val("").focus();
        
    });
    </script>
</body>
</html>
  
</body>
</html>