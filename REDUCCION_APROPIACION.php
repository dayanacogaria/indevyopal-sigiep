<?php 
	require_once('Conexion/conexion.php');
  require_once 'head_listar.php'; 
require_once('./jsonSistema/funcionCierre.php');
  $compania = $_SESSION['compania'];
  $id = 0;
  $numero = "";
  $fecha = "";
  $fechaVen = "";
  $descripcion = "";
  $anno = $_SESSION['anno'];
  $id_comp_pptal_adic = 0;
  $id_tipo_c = "";

  if(!empty($_SESSION['idComPtalReduc']))
  {
    $id_comp_pptal_adic = $_SESSION['idComPtalReduc']; 

    $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['idComPtalReduc'];

    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $id_tipo_c = $rowComp[5];
    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
  
    $fecha = $dia."/".$mes."/".$anio;

    $fecha_div = explode("-", $fechaVen);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
  
    $fechaVen = $dia."/".$mes."/".$anio;

    // id_comprobante_pptal
  }

   $queryGen = "SELECT detCompP.id_unico, 
          CONCAT_WS(' - ', rubP.codi_presupuesto, rubP.nombre), fue.nombre, detCompP.valor
    from gf_detalle_comprobante_pptal detCompP
    left join gf_rubro_fuente rubFue on rubFue.id_unico = detCompP.rubrofuente
    left join gf_fuente fue on fue.id_unico = rubFue.fuente 
    left join gf_rubro_pptal rubP on rubP.id_unico = rubFue.rubro
    left join gf_comprobante_pptal compPtal on compPtal.id_unico = detCompP.comprobantepptal
    where compPtal.id_unico = $id_comp_pptal_adic 
    order by detCompP.id_unico desc"; 
  $resultado = $mysqli->query($queryGen);

  //Consulta para el listado de concepto de la tabla gf_rubro_pptal.
  $queryRub = "SELECT id_unico, CONCAT(codi_presupuesto, ' ',nombre) rubro 
    FROM gf_rubro_pptal WHERE movimiento = 1 AND parametrizacionanno = $anno 
    ORDER BY codi_presupuesto ASC";
  $rubro = $mysqli->query($queryRub);

  $queryFue = "SELECT id_unico, nombre    
    FROM gf_fuente where parametrizacionanno = $anno";
  $fuente = $mysqli->query($queryFue);
  
?>

<title>Reducción Apropiación</title>

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

  .contenedorForma2{
    /*border: 1px solid #020324; #E9E9E9*/
    border: 1px solid #E9E9E9;
    border-radius: 10px; 
    margin-left: 4px;
    margin-right: 4px;
  }

  .area
  { 
    height: auto !important;  
  }  

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
    width:150px;
    height:80px;
    overflow: auto;
    background-color: white;
  }
 body{
      font-size: 10px;
  }

</style>


<link rel="stylesheet" href="css/jquery-ui.css">
  <script src="js/jquery-ui.js"></script> 
  <link href="css/select/select2.min.css" rel="stylesheet">


<script type="text/javascript">

 $(document).ready(function()
  {

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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fecha").datepicker({changeMonth: true}).val();
    $("#fechaAct").val();
  });

</script>


</head>
<body onMouseMove="coordenadas(event);"> <!-- Llamado de la función coordenadas, quien muestra un mensaje al intentar dejar la página. -->

<!-- Campos ocultos que funcionan como variables globales para las funciones de JS. -->
<input type="hidden" id="balanceo">
<input type="hidden" id="id_com_ptal" value="<?php echo $id_comp_pptal_adic;?>">

<input type="hidden" id="id_com_pptal" value="<?php echo $id;?>">
<input type="hidden" id="fechaCompP" value="<?php echo $fecha;?>">
<input type="hidden" id="fechaVenCompP" value="<?php echo $fechaVen;?>">
<input type="hidden" id="fechaAct">


<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Reducción Apropiación</h2>


<div class="col-sm-10"> 


<!-- Disponibilidad presupuestal -->

<div class="client-form contenedorForma form-inline col-sm-12" style="margin-bottom: 8px; padding-bottom: 8px;" >

  <p align="center" class="parrafoO" style="margin-bottom: 5px">
    Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
  </p>

  <form action="javascript: guardarElComp();">


<div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Uno -->

  <div class="col-sm-4" align="left" style="padding-left: 0px;">

    <label for="tipoComPtal" class="control-label " >
      <strong style="color:#03C1FB;">*</strong>
      Tipo Comprobante Pptal:
    </label><br/>

    <?php 
      $queryTipComPtal = "SELECT id_unico, codigo, nombre       
          FROM gf_tipo_comprobante_pptal 
          WHERE clasepptal = 13 
          AND tipooperacion = 3 
          AND compania = $compania 
          ORDER BY codigo";
      $tipoComPtal = $mysqli->query($queryTipComPtal);
    ?>

    <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width: 180px;" required>
      <option value="">Tipo Comprobante</option>
      <?php 
        while($rowTipComPtal = mysqli_fetch_row($tipoComPtal))
        {
          echo '<option value="'.$rowTipComPtal[0].'">'.$rowTipComPtal[1].' '.ucwords(mb_strtolower($rowTipComPtal[2])).'</option>';
        }
      ?>
    </select>
  

  </div>

  

  <!-- Número de disponibilidad -->
  <div class="col-sm-2" align="left" style="padding-left: 0px;">
    <div style="width: 150px;">
      <label for="noDisponibilidad" class="control-label" style="">
        <strong style="color:#03C1FB;">*</strong>Número Disponibilidad:
      </label>
    </div>

    <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width: 150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly" value="<?php echo $numero;?>" required>

  </div>
    <div class="col-sm-3" style="margin-left: 60px;" > <!-- Buscar disponibilidad -->
        <label for="noDisponibilidad" class="control-label" style=""><right>Buscar Reducción:</right></label>
        <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:250px">
            <option value="">Reducción</option>
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
                  WHERE cp.parametrizacionanno = $anno AND tcp.clasepptal = 13 AND tcp.tipooperacion=3  AND tcp.vigencia_actual = 1 ORDER BY cp.numero DESC";
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


  <script type="text/javascript">
      $(document).ready(function ()
        {
            $("#buscarDisp").change(function ()
            {
                if (($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0))
                {
                    traerNum();
                }
            });
        });

      </script>

      <script type="text/javascript"> //Aquí $_SESSION['idComPtalReduc']
       function traerNum()
          { 

            if(($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0) )
            {
              var form_data = { sesion: 'idComPtalReduc',  numero: $("#buscarDisp").val()};
              $.ajax({
                type: "POST",
                url: "estructura_seleccionar_pptal.php",
                data: form_data,
                success: function(response)
                {
                 console.log(response);
                  if(response == 1)
                  {
                    document.location.reload();
                  }
                                               
                }//Fin succes.
              }); //Fi
            } 
        }

      </script>
  
</div> <!-- Fin Fila Uno -->

<div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Dos -->

 <div class="col-sm-3" align="left" style="padding-left: 0px;">
            <label for="nombre" class=" control-label" style="margin-top: 0px;" >Descripción:</label>
            <textarea class="" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; width:250px; height: 50px; width:180px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return txtValida(event,'num_car')" ><?php echo $descripcion;?></textarea> 
          </div>

           <div class="col-sm-2" align="left" style="padding-left: 0px;">
            <label for="fecha" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
            <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha;?>" readonly="readonly" >
          </div>

        <div class="col-sm-2" align="left" style="padding-left: 0px;">
            <label for="fechaVen" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label>
            <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen;?>"  readonly="readonly" required>  <!--  -->
          </div>

           <!-- Estado -->
          <div class="col-sm-1" align="left" style="padding-left: 0px;">
            <label for="mostrarEstado" class="control-label" >Estado:</label>
            <input class="input-sm " type="text" name="mostrarEstado" id="mostrarEstado" class="form-control" style="width:70px; margin-top: 0px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 
            <input type="hidden" value="3" name="estado" id="estado"> <!-- Estado 3, generada -->
          </div>

           <!-- Botón Nuevo --> 
          <div class="col-sm-1" style="margin-top: 15px;">
            <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a>
          </div>

          <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px">
              <button type="submit" id="btnGuardarElComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Guardar" >
                <li class="glyphicon glyphicon-floppy-disk"></li>
              </button> <!--Guardar-->
          </div>
           
           <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px;">
              <button type="button" id="btnModificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Modificar" >
                <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
              </button> <!--Modificar-->
          </div>
           
          <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px">
            <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();">
              <img src="images/hb2.png" style="width: 14px; height: 14.28px;">
            </button> <!--Firma Dactilar-->
          </div>

          <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px">
            <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
              <li class="glyphicon glyphicon glyphicon-print"></li>
            </button> <!--Imprimir-->
          </div>
          <div class="col-sm-1" style="margin-top: 15px; margin-left: -20px; width: 0px;">
            <button type="button" id="btnmdlmov" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" onclick="javascript:abrirdetalleMov(<?php echo $id ?>);" title="Agregrar">
              <li class="glyphicon glyphicon-upload"></li>
            </button> <!--New btn-->
          </div>          
           <script type="text/javascript">
                $("#btnModificar").click(function(){
                    var fecha = $("#fecha").val();
                    var form_data = {case: 4, fecha: fecha};
                    $.ajax({
                        type: "POST",
                        url: "jsonSistema/consultas.php",
                        data: form_data,
                        success: function (response)
                        {
                            console.log(response+'cierre');
                            if (response == 1) {
                                $("#periodoC").modal('show');


                            } else {
                                if(($("#fechaVen").val() != "" && $("#fechaVen").val() != "00/00/0000") && ( $("#fecha").val() != "" && $("#fecha").val() != "00/00/0000") ) {
                                    var fecha  = $("#fecha").val(); 
                                    var fechaVen  = $("#fechaVen").val();
                                    var descripcion = $("#descripcion").val();
                                    var comprobante = $("#id_com_ptal").val(); 
                                    var form_data = {  estruc: 4,fecha: fecha, fechaVen: fechaVen, 
                                        descripcion: descripcion, comprobante: comprobante};
                                    $.ajax({
                                        type: "POST",
                                        url: "estructura_adicion_reduccion.php",
                                        data: form_data,
                                        success: function(response){      
                                            console.log(response);
                                            if(response == 1){

                                                $("#ModificacionConfirmada").modal('show');
                                            } else {
                                               $("#ModificacionFallida").modal('show');

                                            }
                                        }
                                    }); 
                                } else {
                                    $("#mdlErrorFechVen").modal('show');
                                }
                            }
                        }
                    })
                    
                })
            </script>
          <div id="response"></div>
           <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnImprimir").click(function(){
                window.open('informesPptal/inf_Reduc_Aprop.php');
              });
            });
          </script>


            <script type="text/javascript">
    //La fecha.
      $("#fecha").change(function()
      {       
        var fecha = $("#fecha").val();
        var form_data = {case: 4, fecha: fecha};
        $.ajax({
            type: "POST",
            url: "jsonSistema/consultas.php",
            data: form_data,
            success: function (response)
            {
                console.log(response+'cierre');
                if (response == 1) {
                    $("#periodoC").modal('show');
                    $("#fecha").val(fechaAct);
                    $("#fechaVen").val("");

                } else {
                    if($("#noDisponibilidad").val() != 0)
                    {
                    <?php if(empty($_SESSION['idComPtalReduc'])){ ?> 
                        fecha1();
                    <?php }else { ?>
                        fecha2();
                    <?php } ?>
                    }
                    else
                    {
                      var fechaAct = $("#fechaAct").val();
                      $("#fecha").val();
                      $("#fechaVen").val("");
                    }
                }
            }
            })
      }); //Fin Change.

    </script>

    <script type="text/javascript">
  //melta2
  function fecha1(){
    var tipComPal = $("#tipoComPtal").val();
    var fecha = $("#fecha").val();
    var form_data = { estruc: 22, tipComPal: tipComPal, fecha: fecha };
    $.ajax({
      type: "POST",
      url: "estructura_expedir_disponibilidad.php",
      data: form_data,
      success: function(response)
      {
        if(response == 1)
        {
          $("#myModalAlertErrFec").modal('show');
        }
        else
        {
          //response = response.replace(" ","");
          response = response.trim();
          $("#fechaVen").val(response);
        }

      }//Fin succes.
    }); //Fin ajax.        
  }
  function fecha2(){
    var tipComPal = $("#tipoComPtal").val();
    var fecha = $("#fecha").val();
    var numero = $("#noDisponibilidad").val();
    var form_data = { estruc: 1, tipComPal: tipComPal, fecha: fecha, numero: numero};
    $.ajax({
      type: "POST",
      url: "json/jsonFechaComprobante.php",
      data: form_data,
      success: function(response)
      {
          console.log(response+'FechaC2')
        if(response == 1)
        {
          $("#pinfo").html("La fecha es menor a la del comprobante anterior. Verifique nuevamente.");
          $("#myModalAlertErrFec").modal('show');
        }else if (response == 2){
            $("#pinfo").html("La fecha es mayor a la del siguiente comprobante. Verifique nuevamente.");
            $("#myModalAlertErrFec").modal('show');
        }
        else {    
          response = response.trim();
          $("#fechaVen").val(response);
        }
      }//Fin succes.
    }); //Fin ajax.        
  }
</script>


<?php 
  if(empty($_SESSION['idComPtalReduc']))
  {

?>

<script type="text/javascript">
  $(document).ready(function()
  {
    var fechaAct = $("#fechaAct").val();
    $("#fecha").val(fechaAct);

    if($("#noDisponibilidad").val() != "")
    {
      $("#descripcion").attr('readonly','readonly');
    }
    
  });
</script>

<?php 
  }
?>

  <script type="text/javascript"> ////$("#descripcion").removeAttr('readonly');

        $(document).ready(function()
        {  
          $("#tipoComPtal").change(function()
          {
            if(($("#tipoComPtal").val() == "")||($("#tipoComPtal").val() == 0))
            { 
              $("#noDisponibilidad").val("");
              $("#descripcion").attr('readonly','readonly');
              $("#descripcion").val("");
            }
            else
            {
              var form_data = { estruc: 3, id_tip_comp:+$("#tipoComPtal").val() };
              $.ajax({
                type: "POST",
                url: "estructura_expedir_disponibilidad.php",
                data: form_data,
                success: function(response)
                {             
                  var numero = response.trim();
                  $("#noDisponibilidad").val(numero);
                  $("#descripcion").removeAttr('readonly');
                }//Fin succes.
              }); //Fin ajax.

              $("#descripcion").removeAttr('readonly');

            } //Cierre else.
                                
          });//Cierre change.
        });//Cierre Ready.

    </script>

</div> <!-- Fin Fila dos -->


<div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Tres -->



    <script type="text/javascript">
        
        function guardarElComp()
        {
          if($("#fechaVen").val() != "")
          {
            var numero  = $("#noDisponibilidad").val(); 
            var fecha  = $("#fecha").val(); 
            var fechaVen  = $("#fechaVen").val();
            var descripcion = $("#descripcion").val();
            var estado = $("#estado").val();
            var tipocomprobante = $("#tipoComPtal").val(); 

                var form_data = { estruc: 1, numero: numero, fecha: fecha, fechaVen: fechaVen, descripcion: descripcion, estado: estado, tipocomprobante: tipocomprobante, sesion: 'idComPtalReduc' };
                $.ajax({
                  type: "POST",
                  url: "estructura_adicion_reduccion.php",
                  data: form_data,
                  success: function(response)
                  {                          
                    if(response == 1)
                    {
                      $("#mdlExitoElComp").modal('show');
                    }
                    else
                    {
                      $("#mdlErrorElComp").modal('show');
                    }
                  }//Fin succes.
                }); //Fin ajax.
          }
          else
          {
            $("#mdlErrorFechVen").modal('show');
          }
          
        }

    </script>


    <script type="text/javascript">
      
      $(document).ready(function()
      {

        $("#btnNuevoComp").click(function()
        {
          $("#noDisponibilidad").val(""); 
          $("#fecha").val(""); 
          $("#fechaVen").val("");
          $("#descripcion").val("");
          $("#tipoComPtal").val(""); 

                var form_data = { estruc: 2, sesion: 'idComPtalReduc' };
                $.ajax({
                  type: "POST",
                  url: "estructura_adicion_reduccion.php",
                  data: form_data,
                  success: function(response)
                  {                          
                    document.location.reload();
                  }//Fin succes.
                }); //Fin ajax.
        });

      });

    </script>

    


</div> <!-- Fin Fila Tres -->

</form>

</div> <!-- Fin de la disponibilidad  -->


        <!-- Apropiación inicial -->

              <div class="client-form contenedorForma2 form-inline col-sm-12" >
              <!-- Formulario de detalle comprobante pptal -->
              <form name="formConRub" id="orm" class="form-inline" method="POST"  enctype="multipart/form-data" onsubmit="return validarValor()" action="json/registrar_reduccion_apropiacionjson.php">

               <!--  <p align="center" class="parrafoO" style="margin-bottom: 5px">
                  Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                </p>-->
              <div class="row" style="margin-top: 0px;" >  

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
                    <option value="<?php echo $rowRub[0]; ?>"><?php echo ucwords(strtolower($rowRub[1])); ?></option>
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
                  <select name="fuente" id="fuente" class="form-control input-sm" title="Seleccione la fuente" style="width:150px;" required>
                    <option value="" selected="selected" >Fuente</option>
                <?php
                    while($rowFue = mysqli_fetch_row($fuente))
                    {
                  ?>
                    <option value="<?php echo $rowFue[0]; ?>"><?php echo ucwords(strtolower($rowFue[1])); ?></option>
                  <?php 
                    }
                  ?>
                  </select> 

                  <input type="hidden" name="id_rubro_fuente" id="id_rubro_fuente">

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
                          var form_data = { estruc: 3, id_fuente:+$("#fuente").val(), id_rubro:+$("#rubro").val()  };
                          $.ajax({
                            type: "POST",
                            url: "estructura_adicion_reduccion.php",
                            data: form_data,
                            success: function(response)
                            {                          
                              response = response.trim();
                              if(response == 0)
                              {
                                $("#myRubFueRepetido").modal('show');
                              }
                              else
                              {
                                $("#id_rubro_fuente").val(response);
                              }

                                
                            }//Fin succes.
                          }); //Fin ajax.

                        } //Cierre if "#fuente".
                        else
                        {
                          $("#id_rubro_fuente").val("");
                        }
                      } //Cierre else "#rubro".
                                   
                    });//Cierre change.
                 });//Cierre Ready.

                </script>

                  <script type="text/javascript">
                   $(document).ready(function()
                    {  
                      $("#rubro").change(function()
                      {
                        $("#id_rubro_fuente").val("");
                        $("#fuente").val("").focus();
                      });
                    });

                  </script>



                <!-- Caja texto Valor -->
              <div class="col-sm-3" >
                <div class="form-group" style="margin-top: 5px; margin-left: 0px; " align="left">
                  <label for="valor" class="control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label><br/>
                  <input type="text" name="valor" id="valor" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valor', '2');" title="Ingrese el valor" onkeyup="formatC('valor');" required>
                 

                  <input type="hidden" value="">
                </div>
              </div> 

               <!-- Evalúa que el valor no sea superior al saldo -->
                <script type="text/javascript">

                 $(document).ready(function()
                  {

                   $("#valor").keyup(function()
                    { 
                        
                      var valor = $("#valor").val();
                      valor = parseFloat(valor.replace(/\,/g,''));

                      if($("#rubro").val() == "")
                      { 
                        $("#myModalAlert3").modal('show');
                      }
                      else if($("#fuente").val() == "")
                      {
                        $("#myModalAlert2").modal('show');
                      }
                      else
                      {
                        var form_data = { proc: 3, id_rubFue: +$("#id_rubro_fuente").val()}; 
                        $.ajax({
                          type: "POST",
                          url: "estructura_comprobante_pptal.php",
                          data: form_data,
                          success: function(response)
                          {
                            var resVal = 0;
                            respVal = parseFloat(response);

                            if(respVal < valor)
                            {
                              $("#myModalAlert").modal('show');
                            }
                          }
                        });
                      }
                    
                    }); 
                  });

                </script>

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

      

      <!-- Botones de consulta -->
    <div class="col-sm-2" >
                <table class="tablaC table-condensed" style="margin-left: 0px" >
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
      </div> <!-- Fin de botones de consulta -->

<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-10" style="margin-top: 10px;">

 
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
                  $cierre = cierre($id_comp_pptal_adic);
                  if($cierre==0){ ?>
                  <a class href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);">
                    <i title="Eliminar" class="glyphicon glyphicon-trash">
                    </i>
                  </a>

                  <a class href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" >
                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                  </a>
                  <?php }  ?> 
                </td>
                <td class="campos" align="left">
                  <?php echo ucwords(strtolower($row[1]));?>
                </td>

                <td class="campos" align="left">
                  <?php echo ucwords(strtolower($row[2]));?>
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
          <p>¿Desea eliminar el registro seleccionado?</p>
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
        <p>Seleccione una fuente válida.</p>
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
        <p>No es posible seleccionar este conjunto de rubro y fuente, esta opción no existe. Verifique de nuevo.</p>
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

  <!-- Error de fecha --> 
  <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
          <p id="pinfo">La fecha es menor a la del comprobante anterior. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
  </div>


  <!-- Exito al guardar el comprobante --> 
  <div class="modal fade" id="mdlExitoElComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnExitoElComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
  </div>

   <!-- Error al guardar el comprobante --> 
  <div class="modal fade" id="mdlErrorElComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorElComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
  </div>

  <!-- Error al guardar el comprobante --> 
  <div class="modal fade" id="mdlErrorFechVen" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No hay fecha de vencimiento. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrorFechVen" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
  </div>

  <!-- Modal de alerta. No se a seleccionado en el concepto.  -->
<div class="modal fade" id="myModalAlert3" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione un rubro válido.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptCon3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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
        document.location = 'ADICION_APROPIACION.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'ADICION_APROPIACION.php';
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

  <script type="text/javascript">
    
    $('#AceptErrFec').click(function()
    {
      var fechaAct = $("#fechaAct").val();
      $("#fecha").val(fechaAct);
      $("#fechaVen").val("");
    });
    
  </script>


  <script type="text/javascript">
    
    $('#btnExitoElComp').click(function()
    {

      document.location.reload();


    });
    
  </script>


<script type="text/javascript">
  $('#AceptCon').click(function(){ 
    $("#valor").val('');
    $("#fuente").focus();
  });
</script>


<script type="text/javascript">
  $('#AceptCon3').click(function(){ 
    $("#valor").val('');
    $("#rubro").focus();
  });
</script>
<script>                                    
    function abrirdetalleMov (id){
      var form_data = {
      id: id,
      valor: 0
      };
      $.ajax({
        type: 'POST',
        url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_3.php",
        data: form_data,
        success: function (data) {
          $('#response').html(data);
          $(".movi1").modal("show");
        }
      });
    }
</script> 
<div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Periodo ya ha sido cerrado</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
</body>
<?php 
####################VALIDACION CIERRE######################
if(($id_comp_pptal_adic!=0)){
    $cierre = cierre($id_comp_pptal_adic);
    if($cierre ==1){ ?> 
        <script>
            $("#btnGuardarElComp").prop("disabled", true); 
            $("#btnModificar").prop("disabled", true);
            $("#btnGuardarComp").prop("disabled", true);
            $("#rubro").prop("disabled", true);
            $("#fuente").prop("disabled", true);
            $("#valor").prop("disabled", true);
            
        </script>
<?php } else {
        if(empty($_SESSION['idComPtalReduc'])){?>
            <script type="text/javascript">
              $(document).ready(function()
              {
                $("#rubro").prop("disabled", true);
                $("#fuente").prop("disabled", true);
                $("#btnGuardarComp").prop("disabled", true);
                $("#valor").attr("readonly", "readonly");
                $("#btnImprimir").prop("disabled", true); //Deshabilitado
                $("#btnmdlmov").prop("disabled", true);
                $("#tipoComPtal").prop("disabled", false);
                $("#fecha").prop("disabled", false);
              });

            </script>
       <?php } else { ?>
        <script type="text/javascript">          
          $(document).ready(function()
          {
            $("#rubro").prop("disabled", false);
            $("#fuente").prop("disabled", false);
            $("#btnGuardarComp").prop("disabled", false);
            $("#valor").removeAttr("readonly");

            $('#tipoComPtal > option[value="<?php echo $id_tipo_c;?>"]').attr('selected', 'selected');
            $("#tipoComPtal").prop("disabled", true);
            //$("#fecha").prop("disabled", true);

            $("#btnGuardarElComp").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false); //Deshabilitado
          });

        </script>

      <?php } 
      } } else {  
        if(empty($_SESSION['idComPtalReduc'])) { ?>
            <script type="text/javascript">
              $(document).ready(function()
              {
                $("#rubro").prop("disabled", true);
                $("#fuente").prop("disabled", true);
                $("#btnGuardarComp").prop("disabled", true);
                $("#valor").attr("readonly", "readonly");
                $("#btnImprimir").prop("disabled", true); //Deshabilitado
                $("#btnmdlmov").prop("disabled", true);
                $("#tipoComPtal").prop("disabled", false);
                $("#fecha").prop("disabled", false);
              });

            </script>
       <?php } else { ?>
        <script type="text/javascript">          
          $(document).ready(function()
          {
            $("#rubro").prop("disabled", false);
            $("#fuente").prop("disabled", false);
            $("#btnGuardarComp").prop("disabled", false);
            $("#valor").removeAttr("readonly");

            $('#tipoComPtal > option[value="<?php echo $id_tipo_c;?>"]').attr('selected', 'selected');
            $("#tipoComPtal").prop("disabled", true);
            //$("#fecha").prop("disabled", true);

            $("#btnGuardarElComp").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false); //Deshabilitado
          });

        </script>

      <?php }
      }?> 
</html>