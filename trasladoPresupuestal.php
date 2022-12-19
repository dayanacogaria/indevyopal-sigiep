<?php 
require_once('Conexion/conexion.php');
require_once 'head_listar.php'; 
require_once('./jsonSistema/funcionCierre.php');
require_once('./jsonPptal/funcionesPptal.php');
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$id = 0;
$numero = "";
$tipocomprobante = "";
$fecha = "";
$fechaVen = "";
$descripcion = "";

$id_comp_pptal_tras = 0;
$num_anno   = anno($_SESSION['anno']);
if(!empty($_GET['idComPtalTras']))
{
    
$id_comp_pptal_tras = $_GET['idComPtalTras']; 
$id = $id_comp_pptal_tras;
$queryCompro = 'SELECT numero, tipocomprobante, descripcion, fecha, fechavencimiento 
  FROM gf_comprobante_pptal 
  WHERE (id_unico) = \''.$id_comp_pptal_tras.'\'';

$comprobante = $mysqli->query($queryCompro);
$rowComp = mysqli_fetch_row($comprobante);

$numero = $rowComp[0];
$tipocomprobante = $rowComp[1];
$descripcion = $rowComp[2];
$fecha = $rowComp[3];
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

}

$queryGen = "SELECT detCompP.id_unico, CONCAT(rubP.codi_presupuesto,' - ',rubP.nombre), detCompP.valor, detCompP.rubrofuente 
    FROM gf_detalle_comprobante_pptal detCompP
    LEFT JOIN gf_rubro_fuente rubFue ON rubFue.id_unico = detCompP.rubrofuente           
    LEFT JOIN gf_rubro_pptal rubP ON rubP.id_unico = rubFue.rubro
    WHERE (detCompP.comprobantepptal) = $id_comp_pptal_tras 
    ORDER BY detCompP.id_unico ASC";
$resultado = $mysqli->query($queryGen);
?>
<title>Traslado Presupuestal</title>
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

  .contenedorForma2
  {
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
    width:250px;
    height:120px;
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
        yearSuffix: '',
        yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
        maxDate: '31/12/<?php echo $num_anno?>',
        minDate: '01/01/<?php echo $num_anno?>'
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fecha").datepicker({changeMonth: true,}).val();
    $("#fechaAct").val(fecAct);
  });
  </script>
<script type="text/javascript">

  $(document).ready(function()
  {
    //Función que ejecuta consulta para verificar si el comprobante
    var id= $("#id_com_ptal_tras").val();
    console.log(id);
    if(id==""){
        response =0;
    } else {
    var form_data = { estruc:9, id: id};
    $.ajax({
      type: "POST",
      url: "jsonPptal/consultas.php",
      data: form_data,
      success: function(response)
      {
          console.log(response);
          if(response==1){
              $("#btnNuevoComp").attr('disabled','disabled');
              $("#buscarReg").attr('disabled','disabled');
              
          }
        document.getElementById("balanceo").value = response;
      }
    });
    }

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
      $("#btnNuevo").attr('disabled','disabled');
      $("#sltBuscar").attr('disabled','disabled');
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
    var balanceo = $("#balanceo").val();
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


</head>
<body onMouseMove="coordenadas(event);">        
    <input type="hidden" id="balanceo" value="0">
    <input type="hidden" id="id_com_ptal_tras" value="<?php echo $id_comp_pptal_tras;?>" ><input type="hidden" id="tipocomprobante" value="<?php echo $tipocomprobante;?>">
    <input type="hidden" id="fechaCompP" value="<?php echo $fecha;?>">
    <input type="hidden" id="fechaVenCompP" value="<?php echo $fechaVen;?>">
    <input type="hidden" id="fechaAct">
    <input type="hidden" id="txtValidarValorRubr">
    <div class="container-fluid text-center"  >
        <div class="row content">
            <?php require_once 'menu.php'; ?> 
            <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" >
                <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Traslado Presupuestal</h2>
                <div class="col-sm-12"> 
                    <div class="client-form contenedorForma form-inline col-sm-12">
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">
                        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                        </p>
                        <form action="javascript: guardarElComp();">
                            <input type="hidden" name="id" id="id" value="<?php echo $id;?>">
                            <div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Uno -->
                                <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                    <label for="tipoComPtal" class="control-label " ><strong style="color:#03C1FB;">*</strong>
                                    Tipo Comprobante Pptal:</label><br/>
                                    <?php 
                                      $queryTipComPtal = "SELECT id_unico, UPPER(codigo), nombre       
                                          FROM gf_tipo_comprobante_pptal 
                                          WHERE clasepptal = 13 
                                          AND tipooperacion = 4 AND compania = $compania 
                                          ORDER BY codigo";
                                      $tipoComPtal = $mysqli->query($queryTipComPtal);
                                    ?>
                                    <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width: 180px;" required>
                                        <?php if (empty($_GET['idComPtalTras'])) { ?>
                                        <option value="">Tipo Comprobante</option>
                                        <?php 
                                          while($rowTipComPtal = mysqli_fetch_row($tipoComPtal))
                                          {
                                            echo '<option value="'.$rowTipComPtal[0].'">'.$rowTipComPtal[1].' '.ucwords(mb_strtolower($rowTipComPtal[2])).'</option>';
                                          }
                                        ?>
                                        <?php }  else {  
                                            $queryTipComPtal = "SELECT id_unico, UPPER(codigo), nombre       
                                            FROM gf_tipo_comprobante_pptal 
                                            WHERE id_unico = $tipocomprobante 
                                            ORDER BY codigo";
                                          $tipoComPtal = $mysqli->query($queryTipComPtal);
                                          $tp = mysqli_fetch_row($tipoComPtal);
                                            echo '<option value="'.$tp[0].'">'.$tp[1].' '.ucwords(mb_strtolower($tp[2])).'</option>';
                                            } ?>
                                    </select>
                                </div>
                                <!-- Número de traslado -->
                                <div class="col-sm-2" align="left" style="padding-left: 0px;">
                                    <div style="width: 150px;">
                                      <label for="noTraslado" class="control-label" style="">
                                        <strong style="color:#03C1FB;">*</strong>Número Traslado:
                                      </label>
                                    </div>
                                    <input class="input-sm" type="text" name="noTraslado" id="noTraslado" class="form-control" style="width: 150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly" value="<?php echo $numero;?>" required>
                                </div>
                                <div class="col-sm-6"  style="margin-left:30px" > <!-- Buscar disponibilidad -->
                                    <label for="noDisponibilidad" class="control-label"><right>Buscar Traslado:</right></label>
                                    <select class="select2_single form-control" name="buscarReg" id="buscarReg" style="width:350px">
                                        <option value="">Traslado</option>
                                        <?php $reg = "SELECT
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
                                           WHERE tcp.clasepptal = 13 AND tcp.tipooperacion=4 AND cp.parametrizacionanno = $anno ORDER BY cp.numero DESC";
                                        $reg = $mysqli->query($reg); 
                                        while ($row1 = mysqli_fetch_row($reg)) { 
                                            $date= new DateTime($row1[2]);
                                            $f= $date->format('d/m/Y');
                                             $sqlValor = 'SELECT SUM(valor) 
                                                    FROM gf_detalle_comprobante_pptal 
                                                    WHERE valor>0 AND comprobantepptal = '.$row1[0];
                                            $valor = $mysqli->query($sqlValor);
                                            $rowV = mysqli_fetch_row($valor);
                                            $v=' $'.number_format($rowV[0], 2, '.', ','); ?>
                                            <option value="<?php echo $row1[0]?>"><?php echo $row1[1].' '. mb_strtoupper($row1[3]).' '.$f.' '.$v?>
                                        <?php }?>
                                    </select>
                                    <input type="hidden" id="seleccionar">
                                </div>
                                <script type="text/javascript">
                                      $(document).ready(function() // Evento keyup para buscar los números de traslado
                                      {
                                        $("#buscarReg").change(function()
                                        { 
                                          if(($("#buscarReg").val() != "") && ($("#buscarReg").val() != 0))
                                          {
                                              var numero = $("#buscarReg").val();
                                              document.location = 'trasladoPresupuestal.php?idComPtalTras='+numero;


                                          }
                                          else
                                          {
                                            $("#listado").css("display", "none");
                                            $("#seleccionar").val("");
                                          }
                                        });
                                      });

                                </script>
                            </div> <!-- Fin Fila Uno -->
                            <div class="col-sm-12" align="left" style="padding-left: 0px;"> <!-- Fila Dos -->
                                <div class="col-sm-3" align="left" style="padding-left: 0px;">
                                    <label for="nombre" class=" control-label" style="margin-top: 0px;" >Descripción:</label>
                                    <textarea class="" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; width:250px; height: 50px; width:180px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return txtValida(event,'num_car')" ><?php echo $descripcion;?></textarea> 
                                </div>
                                <div class="col-sm-2" align="left" style="padding-left: 0px;">
                                    <label for="fecha" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                    <input class=" input-sm" type="text" name="fecha" id="fecha" class="form-control" style="width:150px;" title="Ingrese la fecha" placeholder="Fecha" value="<?php echo $fecha;?>" readonly="readonly" >
                                </div>
                                <div class="col-sm-2" align="left" style="padding-left: 0px;">
                                    <label for="fechaVen" class=" control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label>
                                    <input class=" input-sm" type="text" name="fechaVen" id="fechaVen" class="form-control" style="width:150px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value="<?php echo $fechaVen;?>"  readonly="readonly" required>  <!--  -->
                                </div>
                                <script type="text/javascript">
                                        $("#fecha").change(function()
                                        {
                                          var tipo = $("#tipoComPtal").val();
                                          if(tipo==""){
                                              $("#seleccioneTipo").modal('show');
                                          } else {
                                          var fecha = $("#fecha").val();
                                            var form_data = { case: 4, fecha:fecha};
                                            $.ajax({
                                              type: "POST",
                                              url: "jsonSistema/consultas.php",
                                              data: form_data,
                                              success: function(response)
                                              { 
                                                  if(response ==1){
                                                      $("#periodoC").modal('show');
                                                      $("#fecha").val("").focus();

                                                  } else {
                                                      fecha1();
                                                  }
                                              }
                                            });   
                                            
                                            }  
                                        });
                                        
                                    </script>
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
                                    <div class="modal fade" id="seleccioneTipo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div id="forma-modal" class="modal-header">
                                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                                </div>
                                                <div class="modal-body" style="margin-top: 8px">
                                                    <p>Seleccione Tipo de Comprobante </p>
                                                </div>
                                                <div id="forma-modal" class="modal-footer">
                                                    <button type="button" id="btnSeleccioneTipo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                                    Aceptar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                            $("#btnSeleccioneTipo").click(function(){
                                                $("#fecha").val("");
                                                $("#fechaVen").val("");
                                            })
                                    </script>
                                    <script type="text/javascript">
                                        function fecha1()
                                        { 
                                            var tipComPal = $("#tipoComPtal").val();
                                            var fecha = $("#fecha").val();
                                            var num = $("#noTraslado").val();
                                            var id = $("#id").val();
                                            console.log(num);
                                            if(id =="" || id =='0'){
                                                var form_data = { estruc: 25, tipComPal: tipComPal, fecha: fecha, num:num};
                                            } else {
                                                var form_data = { estruc: 26, tipComPal: tipComPal, fecha: fecha, num:num};
                                            }
                                            
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/validarFechas.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                
                                              console.log(response);
                                              if(response == 1)
                                              {
                                                $("#myModalAlertErrFec").modal('show');
                                              }
                                              else
                                              {
                                                  
                                                response = response.replace(' ',"");
                                                response= $.trim( response );
                                            
                                               
                                                $("#fechaVen").val(response);
                                                var fechaAs = $("#fecha").val();
                                                $( "#fechaVen" ).datepicker( "destroy" );
                                                $( "#fechaVen" ).datepicker({ changeMonth: true, minDate: fechaAs}).val(response);
                                                
                                              }
                                            }
                                          }); 
                                        }
                                    </script>
                                   <!-- Botón Nuevo --> 
                                <div class="col-sm-1" style="margin-top: 15px;">
                                    <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a>
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="submit" id="btnGuardarElComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-floppy-disk"></li>
                                    </button> <!--Guardar-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="button" id="btnModificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1;  margin: 0 auto;" title="Guardar" >
                                        <li class="glyphicon glyphicon-pencil"></li>
                                    </button> <!--Guardar-->
                                </div>
                                <script>
                                        $("#btnModificar").click(function(){
                                            var id = $("#id").val();
                                            var fecha= $("#fecha").val();
                                            var fechaV = $("#fechaVen").val();
                                            var descripcion = $("#descripcion").val();
                                            if(fecha =="" || fechaV ==""){
                                                $("#myModalAlertErrFec").modal('show');
                                            } else {
                                               var form_data={action:1,id:id, fecha:fecha, fechaV: fechaV, descripcion:descripcion};
                                                  $.ajax({
                                                    type: "POST",
                                                    url: "jsonPptal/gf_traslado_pptalJson.php",
                                                    data: form_data,
                                                    success: function(response)
                                                    { 
                                                        console.log(response);
                                                      if(response ==1){
                                                          $("#ModificacionConfirmada").modal("show");
                                                      } else {
                                                          $("#ModificacionFallida").modal("show");
                                                      }
                                                    }//Fin succes.
                                                  }); 
                                            }
                                        })
                                </script>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();">
                                      <img src="images/hb2.png" style="width: 14px; height: 14.28px;">
                                    </button> <!--Firma Dactilar-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="button" id="btnImprimirPdf" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                      <i class="fa fa-file-pdf-o"></i>
                                    </button> <!--Imprimir-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="button" id="btnImprimirExcel" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Imprimir">
                                      <i class="fa fa-file-excel-o"></i>
                                    </button> <!--Imprimir-->
                                </div>
                                <div class="col-sm-1" style="margin-top: 15px; margin-left: -35px">
                                    <button type="button" id="btnmdlmov" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" onclick="javascript:abrirdetalleMov(<?php echo $id ?>);" title="Agregrar">
                                      <i class="glyphicon glyphicon-upload"></i>
                                    </button> <!--New btn-->
                                </div>
                                <div id="response"></div>
                                <script type="text/javascript">
                                    $(document).ready(function()
                                    {
                                      $("#btnImprimirPdf").click(function(){
                                        window.open('estructuraPptal/inf_Traslado.php?id=<?php echo $_GET['idComPtalTras']?>');
                                      });
                                      $("#btnImprimirExcel").click(function(){
                                        window.open('estructuraPptal/inf_Traslado_Excel.php?id=<?php echo $_GET['idComPtalTras']?>');
                                      });
                                    });
                                </script>
                                

                                <script type="text/javascript"> 
                                $(document).ready(function()
                                {  
                                  $("#tipoComPtal").change(function()
                                  {
                                    if(($("#tipoComPtal").val() == "")||($("#tipoComPtal").val() == 0))
                                    { 
                                      $("#noTraslado").val("");
                                      $("#descripcion").attr('readonly','readonly');
                                      $("#descripcion").val("");

                                    
                                      $("#fecha").val();
                                      $("#fechaVen").val("");

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
                                          $("#noTraslado").val(numero);
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
                                // Guarda en gf_comprobate_presupuestal 
                                function guardarElComp()
                                {
                                  if($("#fechaVen").val() != "")
                                  {
                                    var numero  = $("#noTraslado").val(); 
                                    var fecha  = $("#fecha").val(); 
                                    var fechaVen  = $("#fechaVen").val();
                                    var descripcion = $("#descripcion").val();
                                    var estado = $("#estado").val();
                                    var tipocomprobante = $("#tipoComPtal").val(); 

                                    var form_data = { estruc: 1, numero: numero, fecha: fecha, fechaVen: fechaVen, descripcion: descripcion, estado: estado, tipocomprobante: tipocomprobante };
                                    $.ajax({
                                      type: "POST",
                                      url: "estructuraPptal/estructuraPptal.php",
                                      data: form_data,
                                      success: function(response)
                                      {      
                                          console.log(response);
                                        response = response.trim();
                                        if(response != 0)
                                        {
                                            $("#mdlExitoElComp").modal('show');
                                           $("#btnExitoElComp").click(function(){
                                                document.location = 'trasladoPresupuestal.php?idComPtalTras='+response;
                                            })
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
                                  document.location = 'trasladoPresupuestal.php';
                                });
                              });
                            </script>
                        </div> <!-- Fin Fila Tres -->
                    </form>
                    </div> <!-- Fin de la disponibilidad  -->
                    <!-- Apropiación inicial -->
                    <div class="client-form contenedorForma2 form-inline col-sm-12" >
                    <!-- Formulario de detalle comprobante pptal  -->
                    <form action="javascript: guardarDetalleTraslado();">
              <div class="row" style="margin-top: 0px;" >  

                <!-- Combo-box Rubro -->
                 <div id="divRubro" class="form-group form-inline col-sm-3" style="margin-top: 5px; margin-left: 0px;" align="left">
                  <label for="rubro" class=" control-label"><strong class="obligado">*</strong>Rubro:</label><br/>

                <?php

                  $queryRub = 'SELECT id_unico, CONCAT(codi_presupuesto, \' \',nombre) rubro 
                    FROM gf_rubro_pptal WHERE movimiento = 1 AND tipoclase=7  and parametrizacionanno = '.$anno.'
                    ORDER BY codi_presupuesto ASC';
                  $rubro = $mysqli->query($queryRub);

                ?>
                  
                  <input type="hidden" id="rubroFuente" name="rubroFuente">
                  <input type="hidden" id="rubroOculto" name="rubroOculto">

                  <select name="rubro" id="rubro" onchange="asignaRubro();" class=" form-control input-sm select2_single" title="Seleccione el rubro" style="width:150px;">
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

                  <script type="text/javascript">
                    
                    function asignaRubro()
                    {
                      if($("#rubro").val() != 0 && $("#rubro").val() != '')
                      {
                        var rubro = $("#rubro").val();
                        $("#rubroOculto").val(rubro);
                      }
                      else
                      {
                        $("#rubroOculto").val(0);
                      } 
                    }

                  </script>

                </div>

                <div id="divfuente" class="col-sm-3" style="margin-top: 5px; margin-left: -5px;" align="left">
                  <label for="fuente" class=" control-label"><strong class="obligado">*</strong>Fuente:</label><br/>
                
                  <input type="hidden" id="fuenteOculta" name="fuenteOculta">
                  <select name="fuente" id="fuente" onchange="asignaFuente();" class="form-control input-sm" title="Seleccione la fuente" style="width:150px;">
                   
                  </select> 

                </div>

                  <script type="text/javascript">
                    
                    function asignaFuente()
                    {
                        if($("#fuente").val() != 0 && $("#fuente").val() != '')
                        {
                          var fuente = $("#fuente").val();
                          $("#fuenteOculta").val(fuente);

                          verificarRubroFuente();
                        }
                        else
                        {
                          $("#fuenteOculta").val(0);
                          $("#contraCredito").prop("disabled", false);
                         } 
                    }

                  </script>
                  <script>
                                $("#rubro").change(function(){
                                   var form_data = { estruc: 5, rubro:$("#rubro").val() }
                                   $.ajax({
                                    type: "POST",
                                    url: "jsonPptal/consultas.php",
                                    data: form_data,
                                    success: function(response)
                                    { //console.log(response);
                                        $("#fuente").html(response).focus();
                                        $("#fuente").select2({
                                            allowClear:true
                                        });
                                        verificarRubroFuente();
                                    }
                                  }); 
                                });
                            </script>
                  <script type="text/javascript">

                    function verificarRubroFuente()
                    {
                      var rubro = $("#rubroOculto").val();
                      var fuente = $("#fuente").val();

                      var form_data = { estruc: 5, rubro: rubro, fuente: fuente };
                      $.ajax({
                        type: "POST",
                        url: "estructuraPptal/estructuraPptal.php",
                        data: form_data,
                        success: function(response)
                        {
                          response = parseInt(response);
                          $("#rubroFuente").val(response);
                          if(response != 0)
                          {
                            $("#contraCredito").prop("disabled", false);
                          }
                          else // Cero.
                          {
                            $("#contraCredito").prop("disabled", true); // Si no existe el rubro-fuente desactiva contracrédito.
                            $("#contraCredito").val("");
                          }
                        }
                      });
                    }
                    
                  </script>


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


                <!-- Combo-box Fuente -->
                <div class="form-group form-inline col-sm-3" style="margin-top: 5px;margin-left: -5px;" align="left">


                   <label for="credito" class="control-label"><strong style="color:#03C1FB;">*</strong>Crédito:</label><br/>
                  <input type="text" name="credito" id="credito" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Crédito" onkeypress="return txtValida(event,'dec', 'credito', '2');" title="Ingrese el crédito" onkeyup="formatC('credito');" required>

                </div>


                <!-- Caja texto Valor -->
              <div class="col-sm-2" >

                <div class="form-group" style="margin-top: 5px; margin-left: -5px; " align="left">
                  <label for="contraCredito" class="control-label"><strong style="color:#03C1FB;">*</strong>Contracrédito:</label><br/>
                  <input type="text" name="contraCredito" id="contraCredito" class="form-control input-sm" maxlength="50" style="width:150px;" placeholder="Contracrédito" onkeypress="return txtValida(event,'dec', 'contraCredito', '2');" title="Ingrese el contracrédito" onkeyup="formatC('contraCredito');" required>

                <script type="text/javascript">
                  
                  $(document).ready(function()
                  {
                    $("#credito").keyup(function(event)
                    {
                      var x = event.which || event.keyCode;
                      if(x >= 48 && x <= 57 || x >= 96 && x <= 105)
                      {
                        if($("#rubro").val() != "")
                        {
                          if($("#contraCredito").val() != 0 || $("#contraCredito").val() == "")
                          {
                            $("#contraCredito").val(0);
                          }
                        }
                        else
                        {
                          $("#credito").val("");
                          $("#contraCredito").val("");

                          $("#myRubroVacio").modal('show');
                        }
                      }
                    });
                  });

                </script>

                <script type="text/javascript">
                  
                  $(document).ready(function()
                  {
                    $("#contraCredito").keyup(function(event)
                    {
                      var x = event.which || event.keyCode;
                      if(x >= 48 && x <= 57 || x >= 96 && x <= 105)
                      {
                        if($("#rubro").val() != "")
                        {
                          if($("#credito").val() != 0 || $("#credito").val() == "")
                          {
                            $("#credito").val(0);
                          }

                          var valor = $("#contraCredito").val();
                          valor = parseFloat(valor.replace(/\,/g,''));
                          
                          if(!isNaN(valor))
                          {
                            validarValorRubr();
                          }
                        }
                        else
                        {
                          $("#credito").val("");
                          $("#contraCredito").val("");

                          $("#myRubroVacio").modal('show');
                        }
                      }
                    });
                  });

                </script>

              <!-- Evalúa que el valor ingresado no sea superior al saldo del Rubro. -->
              <script type="text/javascript">

                function validarValorRubr()
                {
                  
                  var a;
                  var valor = $("#contraCredito").val();
                  valor = parseFloat(valor.replace(/\,/g,''));

                  var rubFue = $("#rubroFuente").val();
                  
                  var form_data = { proc: 3, id_rubFue: rubFue};
                  $.ajax({
                    type: "POST",
                    url: "estructura_comprobante_pptal.php",
                    data: form_data,
                    success: function(response)
                    {
                        console.log(response);
                      var respVal = 0;
                      respVal = parseFloat(response);

                      if(respVal < valor)
                      {
                        $("#txtValidarValorRubr").val(1);
                        $("#myModalAlertSaldo").modal("show");
                        
                      }
                      else
                      {
                        $("#txtValidarValorRubr").val(0);
                      }
                    }
                  });
                }
       
      </script>


                  <input type="hidden" value="">
                  
                </div>
                  
              </div> 
<div class="col-sm-1 " style="margin-top:-2px" >
                    <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto; margin-top: 20px;" title="Guardar" >
                      <li class="glyphicon glyphicon-floppy-disk"></li>
                    </button> <!--Guardar-->
                  <input type="hidden" name="MM_insert" >
                </div>
                </div> <!-- Cierra clase row -->
                

              </form>
            </div>  <!-- cierra clase client-form contenedorForma -->
      
      <input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">

      </div>

      


<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-12" style="margin-top: 10px;">

 
          <div class="table-responsive contTabla" >
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Rubro</strong></td>
                <td class="cabeza"><strong>Crédito</strong></td>
                <td class="cabeza"><strong>Contracrédito</strong></td>
               
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Rubro</th>
                <th>Crédito</th>
                <th>Contracrédito</th>
                                
              </tr>

            </thead>
            <tbody>
              
              <?php
                $totalCredito =0;
                $totalContra =0;
                if($resultado == true)
                {
                  while($row = mysqli_fetch_row($resultado))
                  {
                ?>
               <tr class="ocultarEsto">
                <td class="oculto"><?php echo $row[0]?></td>
                <td class="campos">

                  <a class="eliminar" href="#<?php echo $row[0];?>" onclick="javascript:verificarValorEliminar('<?php echo $row[0];?>', '<?php echo $row[3];?>','<?php echo $row[2]?>');" >
                    <i title="Eliminar" class="glyphicon glyphicon-trash">
                    </i>
                  </a>

                  <a class="modificar" href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" >
                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                  </a>

                </td>
                <td class="campos" align="left">
                  <div class="acotado">
                    <?php echo ucwords(mb_strtolower($row[1]));?>
                  </div>
                  
                </td>

                <td class="campos" align="right">
                  <?php 
                    $credito = 0;
                    $contraCredito = 0;
                    if($row[2] < 0)
                    {
                       $contraCredito = $row[2] * -1;
                    }
                    else
                    {
                      $credito = $row[2];
                    }
                  ?>

                    <input type="hidden" id="valCredOcul<?php echo $row[0];?>"  value="<?php echo number_format($credito, 2, '.', ','); ?>">

                    <input type="text" name="creditoMod" id="creditoMod<?php echo $row[0];?>" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'creditoMod<?php echo $row[0];?>', '2');" onkeyup="formatC('creditoMod<?php echo $row[0];?>')" value="<?php echo number_format($credito, 2, '.', ','); ?>" required>

                    <div id="divCredVal<?php echo $row[0];?>" >
                      <?php
                        echo number_format($credito, 2, '.', ',');
                      ?>
                    </div>
                </td>

                <td class="campos" align="right">

                  <input type="hidden" id="valOcul<?php echo $row[0];?>"  value="<?php echo number_format($contraCredito, 2, '.', ','); ?>">

                  <div id="divVal<?php echo $row[0];?>" >
                    <?php  
                      echo number_format($contraCredito, 2, '.', ',');
                    ?>
                  </div>
                    <!-- Modificar los valores -->
                          <table id="tab<?php echo $row[0];?>" style="padding: 0px;  margin-top: -10px; margin-bottom: -10px;" >
                            <tr>
                              <td>
                                <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" maxlength="50" style="width:150px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>')" value="<?php echo number_format($contraCredito, 2, '.', ','); ?>" required>
                              </td>
                              <td>
                                <a href="#<?php echo $row[0];?>" onclick="javascript:verificarValor('<?php echo $row[0];?>', '<?php echo $row[3];?>');" >
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
                      $("#creditoMod"+id).css("display", "none");

                    </script>

                </td>
                  
              </tr>
          <?php 
                $totalCredito +=$credito;
                $totalContra  +=$contraCredito;
                }
              }
          ?>


            </tbody>
          </table>
                    <style>
                        .valores:hover{
                            cursor: pointer;
                            color:#1155CC;
                        }
                    </style>
                    <div class="col-sm-offset-6  col-sm-6 text-left">
                        <div class="col-sm-2">
                            <div class="form-group" style="margin-top:5px;margin-bottom:-10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales:</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-3 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if (($totalCredito) === NULL) { ?>
                                 <label class="control-label valores" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label valores" title="Suma débito"><?php echo number_format($totalCredito, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-5 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                            <?php 
                            if ($totalContra === NULL) { ?>
                                <label class="control-label valores" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label valores" title="Suma crédito"><?php echo number_format($totalContra, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>        
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
  <div class="modal fade" id="myModalAlertEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El registro no se puede eliminar.</p>
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

<!-- Modal de alerta. El valor ingresado no es numérico.  -->
<div class="modal fade" id="myModalAlertDetalle" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es un registro inválido. Verifique nuevamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnAceptValDet" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>



<!-- Error al modificar, los valores ingresados no son correctos, pueden ser letras. --> 
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


  <!-- Error de fecha --> 
  <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Fecha Inválida. Verifique nuevamente.</p>
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

   <!-- Exito al guardar el comprobante --> 
  <div class="modal fade" id="mdlExitoElDetComp" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnExitoElDetComp" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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

  <!-- Modal de alerta. El valor a ingresar en el formulario de concepto es mayor que el saldo.  -->
<div class="modal fade" id="myModalAlertSaldo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ingresado es superior al saldo disponible.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="AceptValSaldo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modDesBal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
                <div id="forma-modal" class="modal-header">		          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                        <p>No puede abandonar este formulario ya que no está balanceado. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnDesBal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
        </div>
    </div>
</div>


  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<?php require_once 'footer.php'; ?>

<script type="text/javascript">
  $('#AceptVal').click(function()
  { 
    $("#credito").val("");
    $("#contraCredito").val("");
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
        document.location.reload();
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
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

      var cambiarCredito = 'creditoMod'+$("#idPrevio").val();
      var ocultarCredito = 'divCredVal'+$("#idPrevio").val();

      if($("#"+cambiarTab).is(':visible'))
      {
            
        $("#"+cambiarTab).css("display", "none");
        $("#"+cambiarDiv).css("display", "block");
        $("#"+cambiarMod).val($("#"+cambiarOcul).val());

        $("#"+cambiarCredito).css("display", "none");
        $("#"+ocultarCredito).css("display", "block");

      }

    }
       
    var idValor = 'valorMod'+id;
    var idDiv = 'divVal'+id;
    var idModi = 'modif'+id;
    var idTabl = 'tab'+id;

    var cambiarCredito = 'creditoMod'+id;
    var ocultarCredito = 'divCredVal'+id;

    $("#"+idDiv).css("display", "none");
    $("#"+idTabl).css("display", "block");

    $("#"+ocultarCredito).css("display", "none");
    $("#"+cambiarCredito).css("display", "block");

    asigna(id);

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

    var cambiarCredito = 'creditoMod'+id;
    var ocultarCredito = 'divCredVal'+id;

    $("#"+idDiv).css("display", "block");
    $("#"+idTabl).css("display", "none");
    $("#"+idValorM).val($("#"+idValOcul).val());

    $("#"+ocultarCredito).css("display", "block");
    $("#"+cambiarCredito).css("display", "none");
    $("#"+cambiarCredito).val($("#valCredOcul"+id).val());

  }

</script>


<script type="text/javascript">
      function guardarModificacion(id) 
      {

        var idDiv = 'divVal'+id;
        var idTabl = 'tab'+id;
        var idCampoValor = 'valorMod'+id;
        var idValOcul = 'valOcul'+id;

        var credito = $("#creditoMod"+id).val();
        credito = credito.replace(/\,/g,'');

        var valor = $("#"+idCampoValor).val();
        valor = valor.replace(/\,/g,'');
        if(valor != 0)
        {
          valor = valor * -1;
        }
       
        var form_data = { estruc: 3, id_val: id, valor: valor, credito: credito};
        $.ajax({
          type: "POST",
          url: "estructuraPptal/estructuraPptal.php",
          data: form_data,
          success: function(response)
          {
              
            if(response == 1)
            {
              $("#ModificacionConfirmada").modal('show');
            }
            else if(response == 0)
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
      validar = parseFloat(validar);

      var credito = $("#creditoMod"+id_txt).val();
      credito = credito.replace(/\,/g,''); //Error.
      credito = parseFloat(credito);
     
      if((isNaN(validar)) || (isNaN(credito)))
      {
        $("#myModalAlertModInval").modal('show');
      }
      else
      {
        if((credito != 0 && contraCredito == 0) || (credito == 0 && contraCredito != 0))
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
        else
        {
          $("#myModalAlertDetalle").modal('show');
        }  
      }
          
    }

  </script>
  <!---Verficar el valor para eliminar--->
  <!-- Evalúa que el valor no sea superior al saldo en modificar valor-->
  <script type="text/javascript">

    function verificarValorEliminar(id_txt,id_rubFue, valor)
    { 
      
      var validar = valor;
      validar = validar.replace(/\,/g,'');
      validar = parseFloat(validar);

  
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
                $("#myModalAlertEliminar").modal('show');
              }
              else
              {
                eliminarDetComp(id_txt);
              }
            } //Fin success.
          }); //Fin Ajax.
          
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

        $("#select2-rubro-container").css("box-shadow", "0 2px 10px rgba(213,233,249,1)"); 
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

           $("#select2-rubro-container").css("box-shadow", ""); 
           $("#select2-rubro-container").css("border", "")
        }
        });
          
        

        $('#select2-rubro-container').click(function(event)
        {
          event.stopPropagation();
        });
      });

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
      
      $("#fecha").val("");
      $("#fechaVen").val("");
    });
    
  </script>

<script type="text/javascript">
    
    $('#AceptValSaldo').click(function()
    {
      $("#contraCredito").val("");
    });
    
  </script>

  <script type="text/javascript">
    
    $('#btnExitoElDetComp').click(function()
    {

      var idComPtalTras = $("#id_com_ptal_tras").val();
      document.location = 'trasladoPresupuestal.php?idComPtalTras='+idComPtalTras;

    });
    
  </script>


  <script type="text/javascript">

    function guardarDetalleTraslado()
    {
      validarValorRubr();

      var validaCC = $("#txtValidarValorRubr").val();
      if(validaCC == 0)
      {
          /*if($("#rubroFuente").val() != 0)
          {*/

            var credito = $("#credito").val();
            credito = parseFloat(credito.replace(/\,/g,''));

            var contraCredito = $("#contraCredito").val();
            contraCredito = parseFloat(contraCredito.replace(/\,/g,''));

            if((isNaN(credito)) || (isNaN(contraCredito)))
            {
              $("#myModalAlert").modal('show');
            }
            else
            {

              if((credito != 0 && contraCredito == 0) || (credito == 0 && contraCredito != 0))
              {

                if(contraCredito != 0)
                {
                  contraCredito = contraCredito * -1;
                }
                
                var idComp = $("#id_com_ptal_tras").val();
                var fuente = $("#fuente").val();
                var rubro = $("#rubro").val();

                var form_data = { estruc: 2, credito: credito, contraCredito: contraCredito, idComp: idComp, rubro: rubro, fuente: fuente };
                $.ajax({
                  type: "POST",
                  url: "estructuraPptal/estructuraPptal.php",
                  data: form_data,
                  success: function(response)
                  {
                      console.log(response);
                    response = parseInt(response);
                    if(response == 1)
                    {
                      $("#rubro").val("");
                      $('#select2-rubro-container').text("Rubro");
                      $("#rubroOculto").val(0);

                      $("#fuente").val("");
                      $('#select2-fuente-container').text("Fuente");
                      $("#fuenteOculta").val(0);

                      $("#credito").val("");
                      $("#contraCredito").val("");

                      $("#mdlExitoElDetComp").modal('show');
                    }
                    else if(response == 0)
                    {
                      $("#mdlErrorElComp").modal('show');
                    }

                  }//Fin succes.
                }); //Fin ajax.

              }
              else
              {
                $("#myModalAlert").modal('show');
              }
            }
        /*}
          else
          {
            $("#myRubroVacio").modal("show");
          }*/
      }
    }

  </script>


  <script type="text/javascript">
                  
    function asigna(id)
    {

      $("#creditoMod"+id).keyup(function(event)
      {
        var x = event.which || event.keyCode;
        if(x >= 48 && x <= 57 || x >= 96 && x <= 105)
        {
          if($("#valorMod"+id).val() != 0 || $("#valorMod"+id).val() == "")
          {
            $("#valorMod"+id).val(0);
          }
        }
      });

      $("#valorMod"+id).keyup(function(event)
      {
        var x = event.which || event.keyCode;
        if(x >= 48 && x <= 57 || x >= 96 && x <= 105)
        {
          if($("#creditoMod"+id).val() != 0 || $("#creditoMod"+id).val() == "")
          {
            $("#creditoMod"+id).val(0);
          }
        }
      });

    }

  </script>
<?php
if(!empty($_GET['idComPtalTras']))
{ 
  $cierre = cierre($_GET['idComPtalTras']);
  if($cierre ==1){  ?>
  <script>
    $("#rubro").prop("disabled", true);
    $("#fuente").prop("disabled", true);
    $("#btnGuardarComp").prop("disabled", true);
    $("#btnModificar").prop("disabled", true);
    $("#btnGuardarElComp").prop("disabled", true);     
    $(".eliminar").css('display','none');
     $(".modificar").css('display','none');
  </script>    
<?php } else { ?>
    <script type="text/javascript">
            $("#btnGuardarElComp").prop("disabled", true);
            
        </script>
<?php }} else { ?>
    <script type="text/javascript">
        $("#rubro").prop("disabled", true);
            $("#fuente").prop("disabled", true);
            $("#btnGuardarComp").prop("disabled", true);
            $("#valor").attr("readonly", "readonly");
            $("#btnImprimirPdf").prop("disabled", true); //Deshabilitado
            $("#btnImprimirExcel").prop("disabled", true); //Deshabilitado
            $("#btnmdlmov").prop("disabled", true);
            $("#tipoComPtal").prop("disabled", false);
            $("#fecha").prop("disabled", false);
            $("#btnModificar").prop("disabled", true);
        </script>
<?php }?> 
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
</body>
</html>