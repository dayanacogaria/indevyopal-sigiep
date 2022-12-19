<?php 
  require_once('Conexion/conexion.php');
  require_once('estructura_apropiacion.php');
  //require_once('estructura_tercero_comprobante_cnt.php');
  session_start();

  require_once 'head_listar.php'; 

  $numero = "";
  $fecha = "";
  $fechaVen = "";
  $descripcion = "";

//$_SESSION['id_comp_pptal_ED'] = "";
  //$_SESSION['id_comp_pptal_ED'] = "";
  //$_SESSION['id_comp_pptal_ED'] = 42;

   //$_SESSION['id_comp_pptal_ED'] = 44; //Acabo de registrar

  if(!empty($_SESSION['id_comp_pptal_EO']))
  {
    // SELECT detComP.id_unico0, rub.nombre1, detComP.valor2, rubFue.id_unico3, fue.nombre4, proy.nombre5, detComP.tercero6, detComp.proyecto7   
    /*$queryGen = "SELECT detComP.id_unico, rub.nombre, detComP.valor, rubFue.id_unico, fue.nombre, proy.nombre, detComP.tercero, detComP.proyecto      
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_concepto_rubro conRub on rub.id_unico = conRub.rubro
      left join gf_concepto con on con.id_unico = conRub.concepto 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      left join gf_tercero terc on terc.id_unico = detComP.tercero 
      left join gf_proyecto proy on proy.id_unico = detComP.proyecto
      where detComP.comprobantepptal = ".$_SESSION['id_comp_pptal_EO'];
    $resultado = $mysqli->query($queryGen);*/
      $querygen = "SELECT 	dcp.id_unico,
	dcp.descripcion,
        dcp.valor,
        dcp.comprobantepptal,
        cp.id_unico,
        cp.numero,
        dcp.rubrofuente,
        rf.id_unico,
        rf.rubro,
        rp.id_unico,
        rp.nombre,
        dcp.tercero,
        tr.id_unico,
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos,
        tr.tipoidentificacion,
        ti.id_unico,
        ti.nombre,
        dcp.proyecto,
        pr.id_unico,
        pr.nombre,
        dcp.comprobanteafectado
        FROM 	gf_detalle_comprobante_pptal dcp
        LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico
        LEFT JOIN gf_tercero tr ON dcp.tercero = tr.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON tr.tipoidentificacion = ti.id_unico
        LEFT JOIN gf_proyecto pr ON dcp.proyecto = pr.id_unico
        WHERE dcp.comprobantepptal = 6 AND dcp.comprobantepptal = 7
        where detComP.comprobantepptal = ".$_SESSION['id_comp_pptal_EO'];
        $resultado = $mysqli->query($queryGen);


    $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['id_comp_pptal_EO'];

    $comprobante = $mysqli->query($queryCompro);
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];

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

    //Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT id_unico, numero     
      FROM gf_comprobante_pptal 
      WHERE tipocomprobante = 6 
      AND estado = 1 
      AND id_unico != '".$_SESSION['id_comp_pptal_EO']."' 
      ORDER BY numero";
    $numeroSoli = $mysqli->query($queryNumSol);

  }


  //Consulta para listado de Tipo Comprobante Pptal.
  $queryTipComPtal = "SELECT id_unico, codigo, nombre       
  FROM gf_tipo_comprobante_pptal 
  WHERE tipooperacion = 1
  ORDER BY codigo";
  $tipoComPtal = $mysqli->query($queryTipComPtal);

  //Consulta para listado de Número Solicitud. // WHERE tipocomprobante = 6 era clase 14
   //SELECT comp.id_unico0, comp.numero1, comp.fecha2, comp.descripcion3 
   /* $querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
    FROM gf_comprobante_pptal  comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
    WHERE tipcomp.clasepptal = 15
    AND comp.estado = 3
    OR comp.estado = 4
    ORDER BY comp.numero";*/


    $querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
    FROM gf_comprobante_pptal  comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
    
    ORDER BY comp.numero";
    

  $SolAprob = $mysqli->query($querySolAprob);


  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto";
  $concepto = $mysqli->query($queryCon);

   //Consulta para el listado de concepto de la tabla gf_rubro_pptal.
  $queryRub = "SELECT id_unico, CONCAT(codi_presupuesto, ' ',nombre) rubro 
    FROM gf_rubro_pptal WHERE movimiento = 1";
  $rubro = $mysqli->query($queryRub);

  $queryFue = "SELECT id_unico, nombre    
    FROM gf_fuente";
  $fuente = $mysqli->query($queryFue);

  //Consulta para el listado de concepto de la tabla gf_concepto.
  $queryCon = "SELECT id_unico, nombre    
  FROM gf_concepto";
  $concepto = $mysqli->query($queryCon);

  //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
  $queryClaCont = "SELECT id_unico, nombre    
  FROM gf_clase_contrato";
  $clasecont = $mysqli->query($queryClaCont);

  //Consulta para el listado de concepto de la tabla gf_proyecto.
  /* $queryProyecto = "SELECT id_unico, nombre    
  FROM gf_proyecto
  WHERE id_unico != $row[7]"; */
  //$proyecto = $mysqli->query($queryProyecto);

   //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
   $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
  FROM gf_tercero ter 
  LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico ";
  $tercero = $mysqli->query($queryTercero); 


  // Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);


?>

<title>Generar Cuenta por Pagar</title>




<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

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
    $("#fecha").datepicker({changeMonth: true}).val(fecAct);
    //$("#fechaVen").datepicker({changeMonth: true}).val(fecAct);

  });

/*});
});*/

</script>


<style type="text/css">
  .area
  { 
    height: auto !important;  
  }  

  .contenedorConRub
  {
    border: 4px solid #020324; 
    border-radius: 10px; 
    margin-left: 4px;
    margin-right: 4px;
    margin-top: -20px;
  }

  .acotado
  {
    white-space: normal;
  }

</style>
 


</head>
<body>


<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

<!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 
    <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Generar Cuenta por Pagar</h2>

    <div class="col-sm-10"><!--   estaba 10 -->
    <div class="client-form contenedorForma col-sm-12"  style=""> <!-- No tenía col-sm-12 -->
    
    <!-- Formulario de comprobante PPTAL -->
    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" onsubmit="return valida();" action="json/registrar_EXP_REG_COMPROBANTE_PPTALJson.php">

      <input type="hidden" value="obligacion" name="expedir">

      <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
      </p>


       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 5px; margin-bottom: -5px;"> <!-- Primera Fila -->
                     <div class="col-sm-4" align="left"> <!-- Tercero -->
            <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
            <select name="tercero" id="tercero" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width:250px;" required>

              <option value="" selected="selected" >Tercero</option>   

              <?php 

                while($rowTerc = mysqli_fetch_row($tercero))
                {
                  if(in_array($rowTerc[7], $natural))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>">
                <?php 
                  echo ucwords(strtolower($rowTerc[1])).' '.ucwords(strtolower($rowTerc[2])).' '.ucwords(strtolower($rowTerc[3])).' '.ucwords(strtolower($rowTerc[4])).' '.$rowTerc[6];
                ?>
              </option> 
              <?php
                  }
                  elseif (in_array($rowTerc[7], $juridica))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>"><?php echo ucwords(strtolower($rowTerc[5])).' '.$rowTerc[6];?></option> 
              <?php
                  }
               
                }
              ?>
            </select>
             <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

             $(document).ready(function()
             {  
                $("#tercero").change(function()
                {
                 

                  if(($("#tercero").val() == "")||($("#tercero").val() == 0))
                  { 
                    var opcion = '<option value="" >Registro Presupuestal</option>';
                    $("#solicitudAprobada").html(opcion);
                  }
                  else
                  {
                    var form_data = { id_tercero:+$("#tercero").val() };
                    $.ajax({
                      type: "POST",
                      url: "estructura_tercero_comprobante_cnt.php",
                      data: form_data,
                      success: function(response)
                      {
                        if(response == "" || response == 0)
                        {
                          var noHay = '<option value="" >No hay registro presupuestal</option>';
                          $("#solicitudAprobada").html(noHay);
                        }
                        else
                        {
                          $("#solicitudAprobada").html(response);
                        }
                        
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asig -->
          </div> <!-- Fin Tercero -->
          
          <div class="col-sm-4" align="left"> <!-- Registro Presupuestal -->
            <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Registro Presupuestal:</label><br>
            <select name="solicitudAprobada" id="solicitudAprobada" class="form-control input-sm" title="Número de solicitud" style="width:250px;">
              <option value="" >Registro Presupuestal</option>
              <?php 
               //SELECT comp.id_unico0, comp.numero1, comp.fecha2, comp.descripcion3 
                while($rowSolAprob = mysqli_fetch_row($SolAprob))
                {
                  $fecha_div = explode("-", $rowSolAprob[2]);
                  $anio = $fecha_div[0];
                  $mes = $fecha_div[1];
                  $dia = $fecha_div[2];
                  
                  $fecha = $dia."/".$mes."/".$anio;


              ?>
              <option value="<?php echo $rowSolAprob[0];?>"><?php echo $rowSolAprob[1].' '.$fecha.' '.ucwords(strtolower($rowSolAprob[3]));?></option> 
              <?php 
                }
              ?>
            </select>  
          </div><!-- Fin Solicitud aprobada -->
          
        <div class="col-sm-4" align="left"><!-- Tipo de comprobante -->
      
            <label for="tipoComPtal" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br/>
            <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width:250px;" required>
            <?php 

              if(!empty($_SESSION['id_comp_pptal_EO']))
              {
                echo '<option value="'.$rowComp[5].'" selected="selected" >'.$rowComp[6].' '.ucwords(strtolower($rowComp[7])).'</option> ';
              }
              else
              {
            ?>
              <option value="" selected="selected" >Tipo Comprobante Presupuestal</option>                        
              <?php 
                while($rowTipComPtal = mysqli_fetch_row($tipoComPtal))
                {
              ?>
              <option value="<?php echo $rowTipComPtal[0];?>"><?php echo $rowTipComPtal[1].' '.ucwords(strtolower($rowTipComPtal[2]));?></option> 
              <?php 
                }
               } 
              ?>
            </select>
          </div> <!-- Fin Tipo de comprobante -->


       </div> <!-- Fin de la primera fila -->
       <!-- Listado de registros -->
       
       
       

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
                        //document.location.reload();                             
                        var numero = parseInt(response);
                        $("#noDisponibilidad").val(numero);
                      }//Fin succes.
                    }); //Fin ajax.

                  } //Cierre else.
                                
                });//Cierre change.
             });//Cierre Ready.

          </script> <!-- Código JS para asignar un nuevo código de comprobante. -->

        </div><!-- Fin Segunda fila -->
        
          <!-- El número de solicitud seleccionado -->
          <input name="numero" type="hidden" value="<?php echo $numero; ?>">      

        <input type="hidden" name="MM_insert" >
        
                       
      </form>

<!--aqui-->
<!-- Al seleccionar un número de solcitud, cargará  --> 
<script type="text/javascript">

   $(document).ready(function()
     {  
        $("#solicitudAprobada").change(function() 
        {
          if(($("#solicitudAprobada").val() == "")||($("#solicitudAprobada").val() == 0))
          { 
            var form_data = { estruc: 7}; //Estructura Uno 
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
          else
          {
            var form_data = { estruc: 8, id_comp:+$("#solicitudAprobada").val() }; //Estructura Dos 
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
    
    var form_data = { estruc: 4, id_tip_comp:+$("#tipoComPtal").val(), fecha: $("#fecha").val() };
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

      <!--</div>  cierra inline -->


        </div> <!-- Cierra clase client-form contenedorForma -->
</div> <!-- Cierra col-sm-10 -->

  

      <input type="hidden" id="txtSesion" > <!-- 2 hay sesión. 1 no hay sesión  -->

<?php 

  if(!empty($_SESSION['id_comp_pptal_EO']))
  {
    ?>
  <script type="text/javascript">

    $("#btnGuardarComp").prop("disabled", false);
    $("#btnGuardar").prop("disabled", false);

    $("#descripcion").val("<?php echo $descripcion;?>");
    $("#descripcion").attr('readonly','readonly');

    $("#fecha").prop("disabled", true);

    $("#btnGuardarComp").css("display", "none");
    $("#btnNuevoComp").css("display", "block");

    $("#txtSesion").val(2);

  </script>
<?php 
  }
  else
  {
?>
  <script type="text/javascript">

    $("#btnGuardarComp").prop("disabled", false);
    $("#btnGuardar").prop("disabled", true);

    $("#descripcion").val("");
    $("#descripcion").removeAttr('readonly');

    $("#fecha").prop("disabled", false);

    $("#btnGuardarComp").css("display", "block");
    $("#btnNuevoComp").css("display", "none");
    
    $("#txtSesion").val(1);

  </script>
<?php
  }
 ?>
 
<script type="text/javascript">
  
     $(document).ready(function()
     { 
      $('#btnNuevoComp').click(function(){
         var form_data = { estruc: 7}; //Estructura Uno 
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


<input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">


      
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
        document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'EXPEDIR_REGISTRO_PPTAL.php';
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

    validar = validar.replace(/\,/g,''); //Elimina la coma que separa los miles.
    valOriginal = valOriginal.replace(/\,/g,'');

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
          resVal = parseInt(response);        
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
    $("#fecha").val(fecAct);
    $("#fechaVen").val("");

  });
    
</script>

<script type="text/javascript">
  
  $('#AceptErrFecVen').click(function(){
    $("#fecha").focus();
  });

</script>

</body>
</html>