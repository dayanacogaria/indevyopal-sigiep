<?php 
  require_once 'head_listar.php'; 
  require_once('Conexion/conexion.php');
  require_once('estructura_apropiacion.php');
  require_once('estructura_saldo_obligacion.php');
  
  $numero = "";
  $fecha = "";
  $fechaVen = "";
  $descripcion = "";
  $numContrato = "";
  $idClaseCon = "";
  $claseCon = "";
  $terceroComp = "";
 $anno = $_SESSION['anno'];
 $compania = $_SESSION['compania'];
  if(!empty($_SESSION['id_comp_pptal_RP']))
  {
    $queryGen = "SELECT detComP.id_unico, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre, proy.nombre, detComP.tercero, detComP.proyecto      
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on conRub.id_unico = detComP.conceptorubro 
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      left join gf_tercero terc on terc.id_unico = detComP.tercero 
      left join gf_proyecto proy on proy.id_unico = detComP.proyecto
      where detComP.comprobantepptal = ".$_SESSION['id_comp_pptal_RP'];
    $resultado = $mysqli->query($queryGen);

    $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.numerocontrato, comp.clasecontrato , cla.nombre, comp.tercero    
      FROM gf_comprobante_pptal comp
      LEFT JOIN gf_tipo_comprobante_pptal tipCom ON comp.tipocomprobante = tipCom.id_unico
      LEFT JOIN gf_clase_contrato cla ON comp.clasecontrato = cla.id_unico
      WHERE  comp.id_unico = ".$_SESSION['id_comp_pptal_RP'];

    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $numContrato = $rowComp[8];

    if(!empty($rowComp[9]))
    {
      $idClaseCon = $rowComp[9];
      $claseCon = $rowComp[10];
    }
    
    $terceroComp = $rowComp[11];


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

  }


  $queryTipComPtal = "SELECT id_unico, codigo, nombre       
    FROM gf_tipo_comprobante_pptal 
    WHERE clasepptal = 17
    AND tipooperacion = 1 and compania = $compania 
    ORDER BY codigo";
  $tipoComPtal = $mysqli->query($queryTipComPtal);

  $querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
    FROM gf_comprobante_pptal  comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
    WHERE tipcomp.clasepptal = 15
    AND comp.estado = 3
    OR comp.estado = 4
    ORDER BY comp.numero";

  $SolAprob = $mysqli->query($querySolAprob);


  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto WHERE parametrizacionanno = $anno";
  $concepto = $mysqli->query($queryCon);

   //Consulta para el listado de concepto de la tabla gf_rubro_pptal.
  $queryRub = "SELECT id_unico, CONCAT(codi_presupuesto, ' ',nombre) rubro 
    FROM gf_rubro_pptal WHERE movimiento = 1 AND parametrizacionanno = $anno";
  $rubro = $mysqli->query($queryRub);

  $queryFue = "SELECT id_unico, nombre    
    FROM gf_fuente WHERE parametrizacionanno = $anno";
  $fuente = $mysqli->query($queryFue);

  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto WHERE parametrizacionanno = $anno";
  $concepto = $mysqli->query($queryCon);

  //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
  $queryClaCont = "SELECT id_unico, nombre    
  FROM gf_clase_contrato";
  $clasecont = $mysqli->query($queryClaCont);

  //Consulta para el listado de concepto de la tabla gf_proyecto.
   //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
  $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
    FROM gf_tercero ter 
    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico WHERE ter.compania = $compania";
  $tercero = $mysqli->query($queryTercero); 


  // Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);


?>

<title>Giro Presupuestal</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<?php 
  if(empty($_SESSION['nuevo_RP']) )
  {
 ?>

<script type="text/javascript">

 $(document).ready(function()
  {

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

  });

</script>
<?php 
  }
 ?>

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


</style>
 
 <!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

</head>
<body>

<input  id="id_com" type="hidden" value="<?php echo $_SESSION['id_comp_pptal_RP'];?>" >
<input type="hidden" id="fechaCompP" value="<?php echo $fecha;?>">
<input type="hidden" id="fechaVenCompP" value="<?php echo $fechaVen;?>">
<input type="hidden" id="fechaActu">

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
    $("#fechaActu").val(fecAct);

  });

  </script>

<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

   <!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 

      <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Giro Presupuestal</h2>


<div class="col-sm-10"><!--   estaba 10 -->
  <div class="client-form contenedorForma col-sm-12"  style=""> <!-- No tenía col-sm-12 -->
    
    <!-- Formulario de comprobante PPTAL -->
    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" onsubmit="return valida();" action="json/registrar_PAG_COMPROBANTE_PPTALJson.php">


      <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
      </p>


       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera Fila -->

        <div class="col-sm-3" align="left"> <!-- Tercero -->
            <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero">
            <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
            <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width:170px;" required>
               <option value="" <?php if(empty($_SESSION['id_comp_pptal_RP'])){ echo 'selected="selected"'; } ?> >Tercero</option>   
              <?php 
                $seleccionado = '';
                while($rowTerc = mysqli_fetch_row($tercero))
                {
                  if(!empty($_SESSION['id_comp_pptal_RP']) && ($terceroComp == $rowTerc[0]))
                  {
                    $seleccionado = 'selected="selected"';
                  }
                  else
                  {
                    $seleccionado = '';
                  }

                  if(in_array($rowTerc[7], $natural))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>" <?php echo $seleccionado?> >
                <?php 
                  echo ucwords(mb_strtolower($rowTerc[1])).' '.ucwords(mb_strtolower($rowTerc[2])).' '.ucwords(mb_strtolower($rowTerc[3])).' '.ucwords(mb_strtolower($rowTerc[4])).' '.$rowTerc[6];
                ?>
              </option> 
              <?php
                  }
                  elseif (in_array($rowTerc[7], $juridica))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>" <?php echo $seleccionado?> >
                <?php echo ucwords(mb_strtolower($rowTerc[5])).' '.$rowTerc[6];?>
              </option> 
              <?php
                  }
               
                }
              ?>

           </select>
          </div> <!-- Fin Tercero -->



         <div class="col-sm-3" align="left"> <!-- Registro Presupuestal -->
            <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Registro Presupuestal:</label><br>
            <select name="solicitudAprobada" id="solicitudAprobada" class="form-control input-sm" title="Registro presupuestal" style="width:170px;">
              <?php 
                 if(!empty($_SESSION['id_comp_pptal_RP']))
                {
                  echo '<option value="'.$id.'">'.$numero.' '.$fecha.' '.ucwords(mb_strtolower($descripcion)).'</option>';
                }
               ?>
              <option value="" >Registro Presupuestal</option>
            </select>  
          </div><!-- Fin Solicitud aprobada -->


        <div class="col-sm-4" align="left"><!-- Tipo de comprobante -->
            <label for="tipoComPtal" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Pptal:</label><br/>
            <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width:170px;" required>
            <?php 

              if(!empty($_SESSION['nuevo_RP']))
              {
                echo '<option value="'.$rowComp[5].'" selected="selected" >'.$rowComp[6].' '.ucwords(mb_strtolower($rowComp[7])).'</option> ';
              }
              else
              {
            ?>
              <option value="" selected="selected" >Tipo Comprobante Presupuestal</option>                        
              <?php 
                while($rowTipComPtal = mysqli_fetch_row($tipoComPtal))
                {
              ?>
              <option value="<?php echo $rowTipComPtal[0];?>"><?php echo $rowTipComPtal[1].' '.ucwords(mb_strtolower($rowTipComPtal[2]));?></option> 
              <?php 
                }
               } 
              ?>
            </select>
          </div> <!-- Fin Tipo de comprobante -->

          <div class="col-sm-1" style="margin-top: 15px;">
            <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo">
              <li class="glyphicon glyphicon-plus"></li>
            </a> <!-- Nuevo -->
          </div>

          <div class="col-sm-1" style="margin-top: 15px;"> <!-- Botones nuevo y guardar -->
            <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
              <li class="glyphicon glyphicon-floppy-disk"></li>
            </button> <!--Guardar-->  
          </div> <!-- Fin Botones nuevo y guardar -->

         

       </div> <!-- Fin de la primera fila -->


        <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 0px;">  <!-- Segunda fila -->

          <div class="col-sm-3" align="left"><!-- Número de disponibilidad -->
            <label for="noDisponibilidad" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Número Comprobante:</label><br/>
            
            <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de comprobante" placeholder="Número Disponibilidad"  readonly="readonly" value="<?php if(!empty($_SESSION['nuevo_RP'])){echo $numero;} ?>" required>
          </div>

          <div class="col-sm-3" align="left"> <!-- Clase de contrato -->
            <label for="claseCont" class="control-label" ><strong style="color:#03C1FB;">*</strong>Clase Contrato:</label><br>
            <select name="claseCont" id="claseCont" class="form-control input-sm" title="Seleccione una clase de contrato" style="width:150px;" required>
              <?php  $claConSelectd = ''; ?> 
              <option value="" <?php if(empty($idClaseCon)){echo 'selected="selected"';}?> >
                Clase Contrato
              </option>   

              <?php 
                while($rowClaCon = mysqli_fetch_row($clasecont))
                {
                  if(!empty($idClaseCon) && $idClaseCon == $rowClaCon[0])
                    $claConSelectd = 'selected="selected"';
                  else
                    $claConSelectd = '';

                  echo '<option value="'. $rowClaCon[0].'" '.$claConSelectd.'>'.ucwords(mb_strtolower($rowClaCon[1])).'</option>'; 

              ?>
              
              <?php 
                }
              ?>

            </select>
          </div>  <!-- Fin Clase de contrato -->

           <div class="col-sm-2" align="left">  <!-- Número de contrato -->
            <label for="noContrato" class="control-label" ><strong style="color:#03C1FB;">*</strong>No. Contrato:</label><br>
            <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:100px;" title="Número de contrato" placeholder="Número Contrato" onkeypress="return txtValida(event,'num')" value="<?php echo $numContrato;?>" required>
          </div> <!-- Fin Número de contrato -->

            <!-- Estado -->
          <div class="col-sm-2" align="left">
            <label for="mostrarEstado" class="control-label" style="" >Estado:</label> <br/>
            <input class="input-sm form-control" type="text" name="mostrarEstado" id="mostrarEstado" style="width:100px;" title="El estado es Solicitada" value="Solicitada" readonly="readonly" > 
            <input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->
          </div>

         

          <div class="col-sm-1" style="margin-top: 15px;" ><!--Imprimir-->
            <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-left: 0px;" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></button> 
        </div>

         <?php 
             if(!empty($_SESSION['id_comp_pptal_RP']))
             {
           ?>
          <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnImprimir").click(function(){
                window.open('informesPptal/inf_Pag_Pptal.php');
              });
            });
          </script>
          <?php 
           }
           ?>
 
        <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

             $(document).ready(function()
             {  
                $("#tercero").change(function()
                {
                 var opcion = '<option value="" >Registro Presupuestal</option>';

                  if(($("#tercero").val() == "")||($("#tercero").val() == 0))
                  { 
                    $("#solicitudAprobada").html(opcion);
                  }
                  else
                  {
                    var form_data = { id_tercero:+$("#tercero").val(), clase: 16 };
                    $.ajax({
                      type: "POST",
                      url: "estructura_tercero_comprobante_pptal.php",
                      data: form_data,
                      success: function(response)
                      {                          
                        if(response == "" || response == 0)
                        {
                          var noHay = '<option value="N" >No hay registro presupuestal</option>';
                          $("#solicitudAprobada").html(noHay).focus();
                        }
                        else
                        {
                          opcion += response;
                          $("#solicitudAprobada").html(opcion).focus();
                        }
                        
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asignación -->

             <script type="text/javascript"> //Código JS para asignar un nuevo código de comprobante.

             $(document).ready(function()
             {  
                $("#tipoComPtal").change(function()
                {
                 

                  if(($("#tipoComPtal").val() == "")||($("#tipoComPtal").val() == 0))
                  { 
                    $("#noDisponibilidad").val("");
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
                        var numero = parseInt(response);
                        $("#noDisponibilidad").val(numero);
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asignar un nuevo código de comprobante. -->



        </div><!-- Fin Segunda fila -->


        <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 0px;"> <!-- Tercera línea fila -->


          <div class="col-sm-3" align="left"> <!-- Descripción -->
            <label for="nombre" class="control-label" style="" >Descripción:</label><br/>
            <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 5px; width:250px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return txtValida(event,'num_car')" ></textarea> <!-- readonly="readonly" -->
          </div> <!-- Fin Descripción -->


        <div class="col-sm-3" align="left"><!--  Fecha -->
          <label for="fecha" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>
          <input class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:100px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha;?>" readonly="readonly" >
        </div>

        <div class="col-sm-2" align="left"> <!-- Fecha Vencimiento  -->
          <label for="fechaVen" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label> <br/>
          <input class="input-sm form-control" type="text" name="fechaVen" id="fechaVen" style="width:100px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen;?>"  readonly="readonly" required>  
        </div>

        <div class="col-sm-3" style="margin-left: 10px;" > <!-- Buscar disponibilidad -->
        <label for="noDisponibilidad" class="control-label" style=""><right>Buscar Giro:</right></label>
        <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:250px">
            <option value="">Giro</option>
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
                  WHERE cp.parametrizacionanno = $anno AND  tcp.clasepptal = 17 AND tcp.tipooperacion=1  AND tcp.vigencia_actual = 1 ORDER BY cp.numero DESC";
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

      <script type="text/javascript"> //Aquí $_SESSION['id_comp_pptal_RP']
        function traerNum(){

            if(($("#buscarDisp").val() != "") && ($("#buscarDisp").val() != 0) && ($("#buscarDisp").val() != "") && ($("#seleccionar").val() != 0))
            {
              var form_data = { sesion: 'id_comp_pptal_RP', nuevo: 'nuevo_RP',  numero: $("#buscarDisp").val()};
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
                                               
                }//Fin succes.
              }); //Fi
            } 

          }

      </script>




        </div> <!-- Fin Tercera línea fila -->

          <!-- El número de solicitud seleccionado -->
          <input name="numero" type="hidden" value="<?php echo $numero; ?>">

        <input type="hidden" name="MM_insert" >
      </form>

        <?php 
  if(empty($_SESSION['id_comp_pptal_RP']))
  {

  ?>
  <script type="text/javascript">
    $(document).ready(function()
    {

      var fechaActual = $("#fechaActu").val();
      $("#fecha").val(fechaActual);
      
    });

  </script>

  <?php
  }
  ?>

        <?php 
  if(!empty($_SESSION['nuevo_RP']))
  {

  ?>
  <script type="text/javascript">
    $(document).ready(function()
    {
      $("#btnGuardarComp").prop("disabled", true);

    });

  </script>

  <?php
  }
  ?>

<!-- Al seleccionar un número de solcitud, cargará  --> 
<script type="text/javascript">

   $(document).ready(function()
     {  
        $("#solicitudAprobada").change(function() 
        {
          if(($("#solicitudAprobada").val() == "")||($("#solicitudAprobada").val() == 0))
          { 
            var form_data = { estruc: 9}; //Estructura Uno 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.
          }
          else if($("#solicitudAprobada").val() != "N")
          {
            var form_data = { estruc: 10, id_comp:+$("#solicitudAprobada").val() }; //Estructura Dos 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

          } //Cierre else.              
        });//Cierre change.

     });//Cierre Ready.

</script> <!-- Fin de recargar la página al seleccionar Solicitud nueva -->


<script type="text/javascript">// Evalúa que la fecha inicial no sea inferior a la fecha inicial del comprobante predecesor.

  $("#fecha").change(function()
  {
    var idComPptal = $("#id_com").val();
    var tipComPal = $("#tipoComPtal").val();
    var fecha = $("#fecha").val();

    var form_data = { estruc: 4, idComPptal: idComPptal, tipComPal: tipComPal, fecha: fecha };
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
          response = response.replace(" ","");
          $("#fechaVen").val(response);
        }

      }//Fin succes.
    }); //Fin ajax.

  }); //Fin Change.

</script> <!-- Fin fecha -->

        </div> <!-- Cierra clase client-form contenedorForma -->
</div> <!-- Cierra col-sm-10 -->



<?php 

  if(!empty($_SESSION['id_comp_pptal_RP']))
  {
    ?>
  <script type="text/javascript">

    $("#descripcion").val("<?php echo $descripcion;?>");
    $("#btnImprimir").prop("disabled", false);

    $("#txtSesion").val(2);

  </script>
<?php 
  }
  else
  {
?>
  <script type="text/javascript">
    $("#btnImprimir").prop("disabled", true);

    $("#descripcion").val("");

  </script>
<?php
  }
 ?>
 
<script type="text/javascript">
  
     $(document).ready(function()
     { 
      $('#btnNuevoComp').click(function(){
         var form_data = { estruc: 9}; //Estructura Uno 
            $.ajax({
              type: "POST",
              url: "estructura_expedir_disponibilidad.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

      });
    });
    
  </script>


  <!-- select2 -->
  <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
     
      
    });
  </script>

  <script>
  function llenar(){
      var tercero = document.getElementById('tercero').value;
      document.getElementById('terceroB').value= tercero;
  }
  </script>

   <?php  
                  if(empty($_SESSION['nuevo_ED']))
                  {
                ?>
                  <script type="text/javascript">
                   $(document).ready(function()
                  {

                   $("#fecha").click(function()
                    {
                      if($("#noDisponibilidad").val() == "" || $("#noDisponibilidad").val() == "")
                      {
                        $("#myModalAlertTipCom").modal('show');
                      }
                    });

                  });

                  </script>
                <?php 
                  }
                 ?>

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
                <td class="cabeza"><strong>Rubro</strong></td>
                <td class="cabeza"><strong>Fuente</strong></td>
                <td class="cabeza"><strong>Tercero</strong></td>
                <td class="cabeza"><strong>Proyecto</strong></td>
                <td class="cabeza"><strong>Saldo</strong></td>
                <td class="cabeza"><strong>Valor Afectado</strong></td>
               
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Rubro</th>
                <th>Fuente</th>
                <th>Tercero</th>
                <th>Proyecto</th>
                <th>Saldo</th>
                <th>Valor Afectado</th>
                <td></td>
                                
              </tr>

            </thead>
            <tbody>
              
              <?php
                if(!empty($_SESSION['id_comp_pptal_RP']) && ($resultado == true))
                {
                  while($row = mysqli_fetch_row($resultado))
                  { 
                     if(!empty($_SESSION['nuevo_RP']))
                    {
                      $valorPpTl = $row[2];
                    }
                    else
                    {
                      $saldDisp = 0;
                      $totalAfec = 0;
                      $queryDetAfe = "SELECT valor   
                      FROM gf_detalle_comprobante_pptal   
                      WHERE comprobanteafectado = ".$row[0];
                      $detAfec = $mysqli->query($queryDetAfe);
                      $totalAfe = 0;
                      while($rowDtAf = mysqli_fetch_row($detAfec))
                      {
                        $totalAfec += $rowDtAf[0];
                      }
                        
                      $saldDisp = $row[2] - $totalAfec;
                      $valorPpTl = $saldDisp;
                    }


                    if($valorPpTl > 0)
                    {


                ?>
               <tr>
                <td class="oculto"><?php echo $row[0]?></td>
                <td class="campos" style="width: 7%;"> <!-- Botones modificar y eliminar -->

                  <a class"" href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                  <a class"" href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" ><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>

                </td>

                <td class="campos" align="left" style="width: 10%;"> <!-- Rubro -->
                  <div class="acotado">
                    <?php echo ucwords(mb_strtolower($row[1]));?>
                  </div>
                </td>

                <td class="campos" align="left" style="width: 20%;"> <!-- Fuente -->
                  
                  <div id="txtFuente" class="acotado" style="width: 100%;">
                    <?php echo ucwords(mb_strtolower($row[4]));?>
                  </div>
                  
                </td>

                <td class="campos" align="left" style="width: 20%;" > <!-- S  Tercero -->

                  <div id="divTerc<?php echo $row[0];?>" class="acotado"  style="width: 100%;">

                    <?php

                      $queryTerc = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                        FROM gf_tercero ter 
                        LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                        WHERE ter.id_unico = '$row[6]'";
                      $terc = $mysqli->query($queryTerc);
                      $rowTer = mysqli_fetch_row($terc);

                      if(in_array($rowTer[7], $natural))
                          {
                            
                          echo ucwords(mb_strtolower($rowTer[1])).' '.ucwords(mb_strtolower($rowTer[2])).' '.ucwords(mb_strtolower($rowTer[3])).' '.ucwords(mb_strtolower($rowTer[4])).' '.$rowTer[6];
                        
                          }
                          elseif (in_array($rowTer[7], $juridica))
                          {
                            echo ucwords(mb_strtolower($rowTer[5])).' '.$rowTer[6]; 
                          }
                    ?>

                  </div>

                  <div id="tabTerc<?php echo $row[0];?>"> <!-- Select Tercero -->
                      <select id="tercMod<?php echo $row[0];?>" class="col-sm-12"  title="Seleccione un tercero" style=" margin-top: 0px;">

                        <option value="<?php echo $row[6];?>" selected="selected" > <!-- Primer select donde se muestra el tercero actual -->
                           <?php

                      $queryTercAct = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                        FROM gf_tercero ter 
                        LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                        WHERE ter.id_unico = '$row[6]'";
                      $tercAct = $mysqli->query($queryTercAct);
                      $rowTerAct = mysqli_fetch_row($tercAct);

                      if(in_array($rowTerAct[7], $natural))
                          {
                            
                          echo ucwords(mb_strtolower($rowTerAct[1])).' '.ucwords(mb_strtolower($rowTerAct[2])).' '.ucwords(mb_strtolower($rowTerAct[3])).' '.ucwords(mb_strtolower($rowTerAct[4])).' '.$rowTerAct[6];
                        
                          }
                          elseif (in_array($rowTerAct[7], $juridica))
                          {
                            echo ucwords(mb_strtolower($rowTerAct[5])).' '.$rowTerAct[6]; 
                          }

                    ?> 
                        </option>   

                      <?php 
                         //Consulta para el listado de concepto de la tabla gf_tercero.
                        $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
                              FROM gf_tercero ter 
                              LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
                              WHERE ter.id_unico != $row[6] AND ter.compania =$compania";
                        $tercero = $mysqli->query($queryTercero);
                        while($rowTerc = mysqli_fetch_row($tercero))
                        {
                          if(in_array($rowTerc[7], $natural))
                          {
                            ?>
                        <option value="<?php echo $rowTerc[0];?>">
                        <?php 
                          echo ucwords(mb_strtolower($rowTerc[1])).' '.ucwords(mb_strtolower($rowTerc[2])).' '.ucwords(mb_strtolower($rowTerc[3])).' '.ucwords(mb_strtolower($rowTerc[4])).' '.$rowTerc[6];
                        ?>
                        </option> 
                      <?php
                          }
                          elseif (in_array($rowTerc[7], $juridica))
                          {
                            ?>
                        <option value="<?php echo $rowTerc[0];?>"><?php echo ucwords(mb_strtolower($rowTerc[5])).' '.$rowTerc[6];?></option> 
                      <?php
                          }
                       
                        }
                      ?>
                    </select>

                  </div>

                </td>

                <td class="campos" align="left" style="width: 10%;"> <!-- S Proyecto -->

                  <div id="divProy<?php echo $row[0];?>" class="acotado">
                    <?php echo ucwords(mb_strtolower($row[5]));?>
                  </div>

                  <div id="tabProy<?php echo $row[0];?>">

                      
                      <select id="proyMod<?php echo $row[0];?>" class="col-sm-12" title="Seleccione un tercero" style="w9idth:80px; margin-top: 0px;">
                        <option value="<?php echo $row[7]?>" selected="selected">
                          <?php echo ucwords(mb_strtolower($row[5]));?>
                        </option>

                      <?php 
                         $queryProyecto = "SELECT id_unico, nombre    
                            FROM gf_proyecto
                            WHERE id_unico != $row[7]";

                        $proyecto = $mysqli->query($queryProyecto);
                        while($rowProy = mysqli_fetch_row($proyecto))
                        {
                      ?>
                          <option value="<?php echo $rowProy[0];?>"><?php echo $rowProy[1];?></option>
                      <?php 
                        }
                      ?>

                      </select>
                  </div>

                </td>

                <td class="campos" align="right" style="width: 10%;"> <!-- Valor afectado -->

                  <?php 
                    $saldoDisponible = valorRegistro($id, $row[3]) + modificacionRegistro($id,$row[3], 16) - afectacionRegistro($id, $row[0], 16);
                    echo number_format($saldoDisponible, 2, '.', ',');
                  ?>

                </td>

                <td class="campos" align="right" style="width: 12%; padding: 0px"> <!-- Valor -->

                  <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($valorPpTl/*$row[2]*/, 2, '.', ','); ?>">

                  <div id="divVal<?php echo $row[0];?>" style="margin-right: 10px;">
                    <?php  
                      echo number_format($valorPpTl/*$row[2]*/, 2, '.', ',');
                    ?>
                  </div>
                    <!-- Modificar los valores -->
                          <table id="tab<?php echo $row[0];?>" style="padding: 0px; background-color: transparent; background:transparent; margin: 0 auto; width: 98%; margin-top: 5px;">
                            <tr>
                              <td colspan="4" style="padding: 0px;">
                              <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" maxlength="50" style="margin-top: 0px; margin-bottom: 0px; width: 100%;" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>');" value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>" required>
                            </td>
                          </tr>
                            <tr>
                              <td></td>
                              <td></td>
                              <td style="padding: 3px;"> <!-- Botón guardar lo modificado. -->
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>','<?php echo $row[3];?>');" >
                                  <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                </a>
                              </td>
                              <td style="padding: 3px;"> <!-- Botón cancelar modificación -->
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

                       var idTabTerc = 'tabTerc'+id;
                       var idTabProy = 'tabProy'+id;

                       $("#"+idTab).css("display", "none");
                       $("#"+idTabTerc).css("display", "none");
                       $("#"+idTabProy).css("display", "none");

                    </script>


                </td>

                

                <td class="campos" style="width: 4%"> <!-- Botón ver afectaciones -->
                  <a class="" href="#<?php echo $row[0];?>" onclick=""><li class="glyphicon glyphicon-eye-open" title="Ver Afectaciones"></li></a>
                </td>
                  
              </tr>
          <?php 
                  }
                }
              }
          ?>


            </tbody>
          </table>

        </div> <!-- table-responsive contTabla -->
       
      </div> <!-- Cierra clase table-responsive contTabla  -->
      
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
<!-- Fin Modales para eliminación -->





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




<!-- Modal de alerta. El valor es mayor que el saldo.  -->
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

<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras || aqui se va a modificar: data-keyboard="false" data-backdrop="static" --> 
  <div class="modal fade" id="myModalAlertModSuperior" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor a modificar no puede ser superior al valor existente. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValModSup" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
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
        <p>La fecha es menor a la del comprobante anterior. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>


<!-- Error de fecha de vencimiento vacía --> 
  <div class="modal fade" id="ModalAlertFecVen" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La fecha de vencimiento está vacía. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptErrFecVen" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de alerta. Los valores ingresados no son numéricos.  -->
<div class="modal fade" id="myModalAlertTipCom" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Debe selecccionar primero un Tipo Comprobante Presupuestal.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptTipCom" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript" src="js/menu.js"></script>
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
        document.location = 'registrar_PAGO_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'registrar_PAGO_PPTAL.php';
      });
    
  </script>


<!-- Fin funciones eliminar -->
<!-- Función para la modificación del registro. -->
<script type="text/javascript">

  function modificarDetComp(id)
  {
    //console.log($("#idPrevio").val());
    if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != ""))
    {
      var cambiarTab = 'tab'+$("#idPrevio").val();
      var cambiarDiv = 'divVal'+$("#idPrevio").val();
      var cambiarOcul = 'valOcul'+$("#idPrevio").val();
      var cambiarMod = 'valorMod'+$("#idPrevio").val();

      var cambiarDivTerc = 'divTerc'+$("#idPrevio").val();
      var cambiarTabTerc = 'tabTerc'+$("#idPrevio").val();
      var cambiarDivProy = 'divProy'+$("#idPrevio").val();
      var cambiarTabProy = 'tabProy'+$("#idPrevio").val();

      if($("#"+cambiarTab).is(':visible'))
      {
            
        $("#"+cambiarTab).css("display", "none");
        $("#"+cambiarDiv).css("display", "block");
        $("#"+cambiarMod).val($("#"+cambiarOcul).val());

        $("#"+cambiarTabTerc).css("display", "none");
        $("#"+cambiarDivTerc).css("display", "block");

        $("#"+cambiarTabProy).css("display", "none");
        $("#"+cambiarDivProy).css("display", "block");


      }

    }
       
    var idValor = 'valorMod'+id;
    var idModi = 'modif'+id;

    var idDiv = 'divVal'+id;
    var idTabl = 'tab'+id;

    var idDivTerc = 'divTerc'+id;
    var idTablTerc = 'tabTerc'+id;

    var idDivProy = 'divProy'+id;
    var idTablProy = 'tabProy'+id;



    $("#"+idDiv).css("display", "none");
    $("#"+idTabl).css("display", "block");

    $("#"+idDivTerc).css("display", "none");
    $("#"+idTablTerc).css("display", "block");

    $("#"+idDivProy).css("display", "none");
    $("#"+idTablProy).css("display", "block");

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

    var idDivTerc = 'divTerc'+id;
    var idTablTerc = 'tabTerc'+id;

    var idDivProy = 'divProy'+id;
    var idTablProy = 'tabProy'+id;


    $("#"+idDiv).css("display", "block");
    $("#"+idTabl).css("display", "none");

    $("#"+idDivTerc).css("display", "block");
    $("#"+idTablTerc).css("display", "none");

    $("#"+idDivProy).css("display", "block");
    $("#"+idTablProy).css("display", "none");

    $("#"+idValorM).val($("#"+idValOcul).val());

  }
</script>



<script type="text/javascript">
  function guardarModificacion(id) //modificarDetComp(id)
  {
    var idDiv = 'divVal'+id;
    var idTabl = 'tab'+id;
    var idCampoValor = 'valorMod'+id;
    var idValOcul = 'valOcul'+id;

    var idCampoTerc = 'tercMod'+id;
    var idCampoProy = 'proyMod'+id;

    var valor = $("#"+idCampoValor).val();
    var tercero = $("#"+idCampoTerc).val();
    var proyecto = $("#"+idCampoProy).val();
        
    valor = valor.replace(/\,/g,''); //Elimina la coma que separa los miles.

    if( ($("#"+idCampoValor).val() == "") || ($("#"+idCampoValor).val() == 0))
    { 
      $("#ModificacionNoValida").modal('show');
      $("#"+idCampoValor).val($("#"+idValOcul).val());
    }
    else
    {
      var form_data = { id_val: id, valor: valor, tercero: tercero, proyecto: proyecto};
      $.ajax({
        type: "POST",
        url: "json/modificar_EXP_REG_DETALLE_COMPROBANTE_PPTALJson.php",
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
      var form_data = { proc: 4, id_rubFue: id_rubFue, id_comp: $("#id_com").val() , clase: 16};
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
  function valida()
  {
    if($("#fechaVen").val() == "")
    {
      $("#ModalAlertFecVen").modal('show');
      return false;
    }
    
    return true;

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
    
  $('#AceptErrFec').click(function()
  {

    var fechaCompP = $("#fechaCompP").val();
    var fechaVenCompP = $("#fechaVenCompP").val();

    $("#fecha").val(fechaCompP);
    $("#fechaVen").val(fechaVenCompP);

  });
    
</script>

<script type="text/javascript">
  
  $('#AceptErrFecVen').click(function(){
    $("#fecha").focus();
  });

</script>

<script type="text/javascript">
    //Si se ingresan valores superiores a los valores para aprobar en alguna de las casillas 
    // de la lista para su modificación.
      $('#AceptTipCom').click(function()
      {
        $("#tipoComPtal").focus();
      });
  </script>

</body>
</html>