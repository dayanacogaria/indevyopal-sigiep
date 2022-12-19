<?php 
#############MODIFICACIONES##################
#07/06/2017 | ERICA G. | CUENTAS DE NÓMINA
#18/05/2017 | ERICA G. | VALIDACION DE FECHA Y DE CIERRE 
#17/05/2017 | ERICA G. | VALIDACION DE RETENCION POR EGRESO 
#17/04/2017 | ERICA G. | AGRANDO EL ESPACIO PARA BUSQUEDAS, BUSQUEDAS DESC
#22/03/2017 | ERICA G. | VALIDACIONES FECHA, ELIMINAR, DISEÑO
#08/03/2017 |ERICA G. | MODIFICACION EGRESO
#21/02/2017 || Erica G. ||Se arregló la modificacion tercero cuando estan vacios
#15/02/2017 || Erica G. ||Se arregló la consulta para agregar cuenta por pagar en egresos vacios
#14/02/2017 || Erica G. ||Se arregló la consulta para agregar cuenta por pagar al cambiar el tercero
#14/02/2017 || Ferney Pérez|| Se modificó la consulta queryGen.
#10/02/2017 || Erica G. ||Arreglo boton Agregar Cuenta por pagar
#03/02/2017 || Ferney Pérez || Se realizaron cambios para el botón modificar e imprimir*/
#31/01/2017 || Ferney Pérez
#28/01/2017 || Ferney Pérez
#27/01/2017 || ERICA G.
#############################################
  require_once('Conexion/conexion.php');
  require_once('estructura_apropiacion.php');
  require_once('estructura_saldo_obligacion.php');

  require_once 'head_listar.php'; 

  $numero = "";
  $fecha = "";
  $fechaVen = "";
  $descripcion = "";

  if(!empty($_SESSION['id_comp_pptal_GE']))
  {
      
    $queryGen = "SELECT detComP.id_unico, CONCAT(rub.codi_presupuesto,' - ',rub.nombre), detComP.valor, 
        rubFue.id_unico, fue.nombre      
      FROM gf_detalle_comprobante_pptal detComP
      left join gf_rubro_fuente rubFue on detComP.rubrofuente = rubFue.id_unico 
      left join gf_rubro_pptal rub on rubFue.rubro = rub.id_unico 
      left join gf_fuente fue on fue.id_unico = rubFue.fuente 
      where detComP.comprobantepptal = ".$_SESSION['id_comp_pptal_GE'];
    $resultado = $mysqli->query($queryGen);

 $queryCompro = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion, comp.fechavencimiento, comp.tipocomprobante, tipCom.codigo, tipCom.nombre, comp.tercero 
      FROM gf_comprobante_pptal comp, gf_tipo_comprobante_pptal tipCom
      WHERE comp.tipocomprobante = tipCom.id_unico 
      AND comp.id_unico = ".$_SESSION['id_comp_pptal_GE'];

    $comprobante = $mysqli->query($queryCompro);
    if(mysqli_num_rows($comprobante)>0){
    $rowComp = mysqli_fetch_row($comprobante);

    $id = $rowComp[0];
    
    $numero = $rowComp[1];
    $fecha = $rowComp[2];
    $descripcion = $rowComp[3];
    $fechaVen = $rowComp[4];
    $terceroComp = $rowComp[8];
    $_SESSION['terceroGuardado']= $rowComp[8];
    $fecha_div = explode("-", $fecha);
    $anio = $fecha_div[0];
    $mes = $fecha_div[1];
    $dia = $fecha_div[2];
  
    $fecha = $dia."/".$mes."/".$anio;

    //Consulta para listado de Número Solicitud diferente al actual.
    $queryNumSol = "SELECT id_unico, numero     
      FROM gf_comprobante_pptal 
      WHERE tipocomprobante = 6 
      AND estado = 1 
      AND id_unico != '".$_SESSION['id_comp_pptal_GE']."' 
      ORDER BY numero";
    $numeroSoli = $mysqli->query($queryNumSol);
    if(!empty($_SESSION['nuevo_GE'])) {
    $idComPtal = $_SESSION['id_comp_pptal_GE'];
    $ppptal = "SELECT
                cp.numero,
                tc.id_unico
              FROM
                gf_comprobante_pptal cp
              LEFT JOIN
                gf_tipo_comprobante tc ON cp.tipocomprobante = tc.comprobante_pptal
              WHERE
                cp.id_unico ='$idComPtal'";
$pptal=$mysqli->query($ppptal);
if(mysqli_num_rows($pptal)>0){
  #BUSCAR CNT CORRESPONDIENTE AL MISMO NUMERO Y AL TIPO #cnt ya hecho
   $pptal= mysqli_fetch_row($pptal);
   $sqlComP= "SELECT id_unico, numero, fecha, descripcion, numerocontrato, tercero, 
    clasecontrato, tipocomprobante, valorbase, valorbaseiva, valorneto, estado       
                            FROM gf_comprobante_cnt   
                            WHERE numero = $pptal[0] AND tipocomprobante=$pptal[1]";

$compPtal = $mysqli->query($sqlComP);
$rowCP = mysqli_fetch_row($compPtal);
$comprobateCnt = $rowCP[0];
}
       $_SESSION['cntEgreso']=$comprobateCnt;
       $_SESSION['idCompCnt']=$comprobateCnt;
    }
}


  }
  


  $queryTipComPtal = "SELECT id_unico, codigo, nombre       
    FROM gf_tipo_comprobante_pptal 
    WHERE clasepptal = 17
    AND tipooperacion = 1 
    AND vigencia_actual=1
    ORDER BY codigo";
  $tipoComPtal = $mysqli->query($queryTipComPtal);

  //Consulta para listado de Número Solicitud. // WHERE tipocomprobante = 6 era clase 14
   //SELECT comp.id_unico0, comp.numero1, comp.fecha2, comp.descripcion3 
  $querySolAprob = "SELECT comp.id_unico, comp.numero, comp.fecha, comp.descripcion       
    FROM gf_comprobante_pptal  comp 
    LEFT JOIN gf_tipo_comprobante_pptal tipcomp on tipcomp.id_unico = comp.tipocomprobante
    WHERE tipcomp.clasepptal = 15
    AND comp.estado = 3
    OR comp.estado = 4
    ORDER BY comp.numero";

  $SolAprob = $mysqli->query($querySolAprob);
  
   //Consulta para el listado de concepto de la tabla gf_tipo_comprobante.
  $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
    FROM gf_tercero ter 
    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
	GROUP BY ter.id_unico";
  $tercero = $mysqli->query($queryTercero); 

  // Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);
  
  $arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
  
 /* foreach ($arr_sesiones_presupuesto as $index => $value)
  {
    if($value != 'id_comp_pptal_GE')
    {
    	unset($_SESSION[$value]);
    }
  }*/

?>

<title>Generar Egreso</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 


<style type="text/css">
  .area
  { 
    height: auto !important;  
  }  

  /*Esto permite que el texto contenido dentro del div
  no se salga de las medidas del mismo.*/
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
    margin-left: 5px;
    margin-top: 5px;
    width: 150px;
    cursor: pointer;
  }

  #listado 
  {
    width: 250px; /* Para Erica*/
    height: 120px; /* Para Erica*/
    overflow: auto;
    background-color: white;
  }

</style>
 
 <!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 

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
    <?php if(empty($fecha)|| $fecha=='') { ?>
    $("#fecha").datepicker({changeMonth: true}).val();
    <?php }  else { ?>
    var fechaI = '<?php echo date("d/m/Y", strtotime($rowComp[2]));?>';
    $("#fecha").datepicker({changeMonth: true}).val(fechaI);
    <?php } ?>
    $("#fechaAct").val(fecAct);

  });

</script>

</head>
<body>
<!-- <body onresize="cambiar()"> -->

  <input type="hidden" id="id_comp_pptal_GE" value="<?php echo $_SESSION['id_comp_pptal_GE'];?>">
  <input type="hidden" id="idComPtal" value="<?php echo $_SESSION['id_comp_pptal_GE'];?>">
  <input type="hidden" id="respuesta" value="<?php if(empty($_SESSION['nuevo_GE'])){  
      if(!empty($_SESSION['id_comp_pptal_GE'])){
        $rt = "SELECT numero, tipocomprobante FROM gf_comprobante_pptal WHERE id_unico=".$_SESSION['id_comp_pptal_GE'];  
        $rt =$mysqli->query($rt);
        $rt = mysqli_fetch_row($rt);
        $r="SELECT
            cn.id_unico 
          FROM
            gf_comprobante_cnt cn
          LEFT JOIN
            gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
          WHERE
            numero = $rt[0] AND tc.comprobante_pptal = $rt[1]";
        $r = $mysqli->query($r);
        if( mysqli_num_rows($r)>0){
            $r = mysqli_fetch_row($r);
            echo $r[0];
        }
      } 
      
  } else { echo  $_SESSION['cntEgreso']; }?>">
  <input type="hidden" id="ultimoCnt">
  <input type="hidden" id="fechaAct">
<script type="text/javascript">
  $(document).ready(function()
  { 
     
      
    <?php if (empty($_SESSION['comprobanteGenerado'])) { ?>
            
            $("#btnImprimir").prop("disabled", true);
            $("#btnEnviar").prop("disabled", true);
            
            $("#btnEliminarReg").prop("disabled", true);
        
    <?php } else  { ?>
        var idComPtal=$("#idComPtal").val();
        var form_data = { estruc: 7, idComPtal: idComPtal }; //Estructura Uno 
      $.ajax({
        type: "POST",
        url: "estructura_aplicar_retenciones.php",
        data: form_data,
        success: function(response)
        {
          $("#respuesta").val(response);      
          
        }
    });
    <?php } ?>    
    
  });

  
</script>


<div class="container-fluid text-center"  >
  <div class="row content">
  <?php require_once 'menu.php'; ?>

   <!-- Localización de los botones de información a la derecha. -->
    <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 

      <h2 align="center" class="tituloform col-sm-12" style="margin-top: -5px; margin-bottom: 2px;" >Generar Egreso</h2>


<div class="col-sm-12"><!--   estaba 10 -->
  <div class="client-form contenedorForma col-sm-12"  style="padding: 0px;"> 
    
    <!-- Formulario de comprobante PPTAL -->
    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  action="json/registrar_GENERAR_EGRESOJsonL.php"> 
    <!-- <form name="form" class="form-horizontal" method="POST" onsubmit="return valida();" enctype="multipart/form-data"  >  -->

      <input type="hidden" value="obligacion" name="expedir">

      <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
        Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
      </p>

       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera Fila -->

       <div class="col-sm-2" align="left"> <!-- Tercero -->
           <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero" >
            <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
            <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width:150px; height: 30px" required onchange="llenar();">
              <option value="" <?php if(empty($_SESSION['id_comp_pptal_GE'])){ echo 'selected="selected"'; } ?> >Tercero</option>   
              <?php 
                $seleccionado = '';
                while($rowTerc = mysqli_fetch_row($tercero))
                {
                  if(!empty($_SESSION['id_comp_pptal_GE']) && ($terceroComp == $rowTerc[0]))
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
              <option value="<?php echo $rowTerc[0];?>" <?php echo $seleccionado.''?> >
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
                <?php echo ucwords(mb_strtolower($rowTerc[5])).' '.$rowTerc[6]; ?>
              </option> 
              <?php
                  }
                }
              ?>
            </select>
          </div> <!-- Fin Tercero -->
          
   <div class="col-sm-2" align="left"> <!-- Registro Presupuestal -->
            <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Cuenta por Pagar:</label><br>
            <select name="solicitudAprobada" id="solicitudAprobada" class="select2_single form-control input-sm" title="Cuenta por Pagar" style="width:150px; height: 30px" >
              <option value="" >Cuenta por Pagar</option>
                <?php                            
                if((!empty($_SESSION['terceroGuardado'])))
                { ?>
                 
              
              <?php
                    $id_tercero = $_SESSION['terceroGuardado'];
                    $queryComp="SELECT  com.id_unico, com.numero, com.fecha, com.descripcion
  		FROM gf_comprobante_pptal com
 		left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
  		WHERE tipoCom.clasepptal = 16 and com.tercero =  $id_tercero";
                    $comprobanteP = $mysqli->query($queryComp);
	while ($row = mysqli_fetch_row($comprobanteP))
	{
		$queryDetCompro = "SELECT detComp.id_unico, detComp.valor   
            FROM gf_detalle_comprobante_pptal detComp, gf_comprobante_pptal comP 
            WHERE comP.id_unico = detComp.comprobantepptal 
            AND comP.id_unico = ".$row[0];

        $saldDispo = 0;
        $totalSaldDispo = 0;
        $detCompro = $mysqli->query($queryDetCompro);
        while($rowDetComp = mysqli_fetch_row($detCompro))
        {
        	$rowDetComp[1];
        	$queryDetAfetc = "SELECT valor   
          		FROM gf_detalle_comprobante_pptal   
          		WHERE comprobanteafectado = ".$rowDetComp[0];
          	$detAfect = $mysqli->query($queryDetAfetc);
          	$totalAfec = 0;
          	while($rowDetAf = mysqli_fetch_row($detAfect))
          	{
            	$totalAfec += $rowDetAf[0];
          	}
            
        	$saldDispo = $rowDetComp[1] - $totalAfec;
        	$totalSaldDispo += $saldDispo;
    	}
    	$saldo = $totalSaldDispo;
		
		if($saldo > 0)
		{ 
			$fecha_div = explode("-", $row[2]);
		  $anio = $fecha_div[0];
		  $mes = $fecha_div[1];
		  $dia = $fecha_div[2];
		  $fecha = $dia."/".$mes."/".$anio; 

			echo '<option value="'.$row[0].'">'.$row[1].' '.$fecha.' '.ucwords(mb_strtolower($row[3])).' $'.number_format($saldo, 2, '.', ',').'</option>';
		}
                }
                }
              ?>
             
            </select>  
          </div><!-- Fin Solicitud aprobada -->

          <div class="col-sm-2" align="left" > <!-- Tipo Comprobante -->
            <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br>
            <select name="tipoComprobante" id="tipoComprobante" class="form-control input-sm" title="Tipo Comprobante" style="width:150px; height: 30px" required>
                <?php echo $_SESSION['id_comp_pptal_GE'];
                if(!empty($_SESSION['nuevo_GE']) && !empty($_SESSION['id_comp_pptal_GE'])){
                    
                    $tc ="SELECT tc.id_unico, tc.codigo, tc.nombre FROM gf_comprobante_pptal cp "
                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                            . "WHERE cp.id_unico =".$_SESSION['id_comp_pptal_GE'];
                    $tc = $mysqli->query($tc);
                    if(mysqli_num_rows($tc)>0) {
                    $tc = mysqli_fetch_row($tc);
                    ?>
                <option value="<?php echo $tc[0]?>"><?php echo mb_strtoupper($tc[1]).' - '.ucwords(mb_strtolower($tc[2]));?></option>
                <?php  }  else { ?>
                  <option value="" >Tipo Comprobante</option>
             <?php
                while($rowTC = mysqli_fetch_row($tipoComPtal))
                {
                  echo '<option value="'.$rowTC[0].'" > '.mb_strtoupper($rowTC[1]).' - '.ucwords(mb_strtolower($rowTC[2])).' </option>';
                }
             ?>  
                    
                <?php } } else {  ?>
             <option value="" >Tipo Comprobante</option>
             <?php
                while($rowTC = mysqli_fetch_row($tipoComPtal))
                {
                  echo '<option value="'.$rowTC[0].'" > '.mb_strtoupper($rowTC[1]).' - '.ucwords(mb_strtolower($rowTC[2])).' </option>';
                }
             ?><?php } ?>
            </select>  
          </div><!-- Fin Solicitud aprobada -->

    <!--    <div class="col-sm-2" align="left">  Fecha 
        </div> -->


        <?php 
  if(!empty($_SESSION['id_comp_pptal_GE']) && !empty($_SESSION['nuevo_GE']))
  {
 ?>
<script type="text/javascript">
  $(document).ready(function(){
    var idComPtal = $("#id_comp_pptal_GE").val();

    var form_data = { estruc: 7, idComPtal: idComPtal }; //Estructura Uno 
      $.ajax({
        type: "POST",
        url: "estructura_aplicar_retenciones.php",
        data: form_data,
        success: function(response)
        {
           // console.log(response);
          response = parseInt(response);
         // $("#respuesta").val(response);                     
        }//Fin succes.
      }); //Fin ajax.
  });
  
</script>

<?php 
  }
 ?>


        <?php 
  if(!empty($_SESSION['id_comp_pptal_GE']) && empty($_SESSION['nuevo_GE']))
  {
 ?>
<script type="text/javascript">
  $(document).ready(function(){
    var idComPtal = $("#id_comp_pptal_GE").val();

    var form_data = { estruc: 19, idComPtal: idComPtal }; //Estructura Uno 
      $.ajax({
        type: "POST",
        url: "estructura_aplicar_retenciones.php",
        data: form_data,
        success: function(response)
        {
          response = parseInt(response);
          //$("#respuesta").val(response);                     
        }//Fin succes.
      }); //Fin ajax.
  });
  
</script>

<?php 
  }
 ?>
<script>
$("#tipoComprobante").change(function()
{
    <?php if(empty($_SESSION['comprobanteGenerado'])|| $_SESSION['comprobanteGenerado']=='') { ?>
    
    var tipo = $("#tipoComprobante").val();
    var respuesta = $("#respuesta").val();
    var cuenta=$("#solicitudAprobada").val();
    
    
    var form_data = { case: 17, tipocomprobante: tipo }; //Estructura Uno 
      $.ajax({
        type: "POST",
        url: "consultasBasicas/busquedas.php",
        data: form_data,
        success: function(response)
        {
           response=JSON.parse(response);
          response = parseInt(response);
          $("#numEgreso").val(response);  
          $("#btnGuardar").prop("disabled", false);
          $("#fecha").val("");  
        }
    });
    <?php }  else { ?>
    <?php } ?>
});


</script>
<div class="col-sm-2" align="left"><!--  Fecha -->
          <label class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>
          <input class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:100px; height: 30px" title="Ingrese la fecha" placeholder="Fecha" value="" readonly="readonly" >
        </div>

          <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botón guardar -->

               <button type="button" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!-- glyphicon glyphicon-floppy-disk Guardar --> 

        </div> <!-- Fin Botones nuevo -->

       

        <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botón siguiente -->

              <button type="button" id="btnEnviar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Siguiente" ><li class="glyphicon glyphicon-arrow-right"></li></button> <!-- glyphicon glyphicon-floppy-disk Guardar--> 

        </div> <!-- Fin Botones nuevo -->
        <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botón ver formulario registro_COMPROBANTE_CNT.php -->
                            <button type="button" id="btnVerCnt" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Ver Comprobante Contable">
                              <li class="glyphicon glyphicon-eye-open"></li>
                            </button> 
                          </div>
        <script type="text/javascript">
                          $(document).ready(function()
                          {
                            $("#btnVerCnt").click(function()
                            {
                              var id_comp = $("#id_comp_pptal_GE").val();

                              var form_data = { estruc: 16, id_comp: id_comp}
                              $.ajax({
                                type: "POST",
                                url: "estructura_aplicar_retenciones.php",
                                data: form_data,
                                success: function(response)
                                {
                                  if(response != 0)
                                  {
                                    document.location = 'registro_COMPROBANTE_EGRESO.php'; //Dejar esta siempre.
                                    //window.open('registro_COMPROBANTE_CNT.php'); // Usar para probar.
                                  }
                                  else
                                  {
                                    $("#mdlErrNoCnt").modal('show');
                                  }
                                }//Fin succes.
                              }); //Fi
                            });
                          });
                        </script>

 <div class="col-sm-1" style="margin-top: 22px;"> <!-- Botón nuevo -->

              <button type="button" id="btnNuevo" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Nuevo" ><li class="glyphicon glyphicon-plus"></li></button> <!-- glyphicon glyphicon-floppy-disk Guardar--> 

        </div> <!-- Fin Botones nuevo -->

       </div> <!-- Fin de la primera fila --> 


       <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left:2px; margin-bottom: 0px;"> <!-- Segunda Fila -->

        

           <div class="col-sm-3" align="left" >
            <label for="numEgreso" class="control-label" style="">No. Egreso:</label><br>
            <input class="input-sm" type="text" name="numEgreso" id="numEgreso" class="form-control" style="width:150px; margin-top: 0px; margin-bottom: 0px; height: 30px" title="Número de comprobante de Egreso" placeholder="Número Egreso" readonly="readonly" value="<?php if(!empty($_SESSION['nuevo_GE'])){ echo $numero;}?>"> <!---->
          </div>


        <div class="col-sm-4" style="margin-top: -2px; margin-left:-90px" > <!-- Buscar disponibilidad -->
            <label for="numEgreso" class="control-label" style="">Buscar Registros:</label><br>
            
            <select class="select2_single form-control" name="buscarReg" id="buscarReg" style="width:450px; height: 30px">
                <option value="">Registro</option>
                <?php $reg = "SELECT
                            cp.id_unico,
                            cp.numero,
                            cp.fecha,
                            tcp.codigo,
                            IF(
                              CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
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
                          WHERE tcp.clasepptal = 17 ORDER BY cp.numero DESC";
                    $reg = $mysqli->query($reg); 
                    while ($row1 = mysqli_fetch_row($reg)) { 
                        $date= new DateTime($row1[2]);
                        $f= $date->format('d/m/Y');
                         $sqlValor = 'SELECT SUM(valor) 
                                FROM gf_detalle_comprobante_pptal 
                                WHERE comprobantepptal = '.$row1[0];
                        $valor = $mysqli->query($sqlValor);
                        $rowV = mysqli_fetch_row($valor);
                        $v=' $'.number_format($rowV[0], 2, '.', ',');
                        ?>
                <option value="<?php echo $row1[0]?>"><?php echo $row1[1].' '. mb_strtoupper($row1[3]).' '.$f.' '.ucwords(mb_strtolower($row1[4])).' '.$row1[5].$v?>
                    <?php }?>
            </select>
              
          </div>
           <div class="col-sm-2" align="left"><!--  Fecha -->
           </div>

          

            <script type="text/javascript">

        $(document).ready(function()
        {
          $("#buscarReg").change(function()
          { 
           traerNum(); //Para Erica. Se añade esta línea para que al hacer clik se recarge con el comprobante egreso a buscar.
           
            });

          
          });

      </script>


<!-- Para Erica. Se añade desde la línea 454 hasta la línea 466 esta parte para que al dar click fuera del input de búsqueda se vacie este y desaparezca el listado  -->
<script type="text/javascript">
// Al dar click fuera del input buscar se limpia el input y se oculta el div de resultados.
        $(document).ready(function(){
 
          $(document).click(function(e){
            if(e.target.id!='buscarReg')
              $('#buscarReg').val('');
              $('#listado').fadeOut();
            });
 
        });
      </script>


      <!-- Para Erica. Se elimina document para dejar la función traerNum. Las línas comentadas se deben eliminar. -->
      <script type="text/javascript"> //Aquí $_SESSION['idComPtalAdic']
        // $(document).ready(function()
        // {
        //   $("#traerNum").click(function()
          function traerNum() //Añadido <---------- Ojo.
          { 

            // if(($("#buscarReg").val() != "") && ($("#buscarReg").val() != 0) && ($("#seleccionar").val() != "") && ($("#seleccionar").val() != 0))
            // { 

              // Ojo El número de valN cambia a tres.
              var form_data = { sesion: 'id_comp_pptal_GE', nuevo: 'nuevo_GE',  numero: $("#buscarReg").val(), valN: 3}
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

        //   });
        // });

      </script>


	  
	  
     <!--  Imprimir.-->
<div class="col-sm-1" style="margin-top: 5px;margin-left: 0px;">

        <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Imprimir Egreso" >
          <li class="glyphicon glyphicon-print"></li>
        </button> 

        <script type="text/javascript">// Evalúa que la fecha no sea inferior a la fecha inicial del comprobante seleccioando.
          $(document).ready(function(){
            $("#btnImprimir").click(function()
            {
              var idCompCnt = $("#respuesta").val();
              idCompCnt = parseInt(idCompCnt);

                var form_data = { estruc: 1, idCompCnt: idCompCnt};
                $.ajax({
                  type: "POST",
                  url: "imprimir_egreso.php",
                  data: form_data,
                  success: function(response)
                  {
                    window.open('informesPptal/inf_Comp_Egreso.php');  
                  }//Fin succes.
                }); //Fin ajax.
            }); 
          });
        </script> 
      </div>

      <div class="col-sm-1" style="margin-top: 5px;margin-left: 0px;">
        
        <button type="button" id="btnEliminarReg" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Eliminar" >
          <li class="glyphicon glyphicon-remove"></li>
        </button> 
         

      </div>
      <!--AGREGAR CUENTA POR PAGAR-->
      
     <script>
     $('#tercero').on('change',function()
        {
            <?php if(!empty($_SESSION['terceroGuardado'])) { 
              if (mysqli_num_rows($resultado)==''){ ?>
                  
              document.getElementById('agregarC').style.display = 'inline-block'; 
              <?php } else { ?>
            var ter = document.getElementById("tercero").value;
            var ter2 = <?php echo $_SESSION['terceroGuardado'];?>;
           
          if(ter == ter2){
              document.getElementById('agregarC').style.display = 'inline-block'; 
              
          } else {
              document.getElementById('agregarC').style.display = 'none'; 
            
          }
            <?php } }?>
        });
       </script>
       <?php 
       if(empty($_SESSION['comprobanteGenerado']) )
        { ?>
        <script>
         $(document).ready(function()
        {
         
              document.getElementById('agregarC').style.display = 'none'; 
         
      });
      </script>
       
       <?php   } else {  ?>
      
      <script>
         $(document).ready(function()
        {
          var ter = document.getElementById("tercero").value;
          var ter2 = <?php echo $_SESSION['terceroGuardado'];?>;
           
          if(ter == ter2){
              document.getElementById('agregarC').style.display = 'inline-block'; 
              
          } else {
              document.getElementById('agregarC').style.display = 'none'; 
          }
      });
      </script>
       <?php } ?>           
      <input type="hidden" id="comprobantepptalgeneradoA" name="comprobantepptalgeneradoA" value="<?php echo $_SESSION['comprobanteGenerado']?>">
      <input type="hidden" id="comprobanteCNT" name="comprobanteCNT" value="<?php echo $_SESSION['idCompCnt']?>">
      <div class="col-sm-1" id="agregarC" name="agregarC" style="margin-top: 5px; margin-left: 0px; display: inline-block" >

        <button type="button" id="btnAgregarCuentaPorPagar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Agregar Cuenta Por Pagar" >
          <li class="glyphicon glyphicon-plus"></li>Cuenta Por Pagar
        </button> 

      </div>
       
<br/>	   
<br/>	   
      <script type="text/javascript">
        $(document).ready(function()
        {
          $("#btnAgregarCuentaPorPagar").click(function()
          {
              var comprobanteAnterior = $("#comprobantepptalgeneradoA").val();
            var tipocomprobante = $("#tipoComprobante").val();
            var fecha = $("#fecha").val();
            var cuentaxpagar = $("#solicitudAprobada").val();
            var comprobante_pptal = $("#idComPtal").val();
            var comprobante_cnt = $("#comprobanteCNT").val();
            var tercero = $("#tercero").val();
            //VERIFICAR SI TIENE RETENCION O NO
             var form_data = { case: 24, tipo: $("#tipoComprobante").val()};
            $.ajax({
              type: "POST",
              url: "consultasBasicas/busquedas.php",
              data: form_data,
              success: function(response)
              {
                console.log(response);
                if(response == 1)
                {
                    
                    if(comprobanteAnterior == '' || comprobanteAnterior == 0){
                        $("#mdlNoHay").modal('show');
                        $("#btnNoHay").click(function()
                        {
                          $("#mdlNoHay").modal('hide');

                        });
                    } else {
                        if(tipocomprobante == '' || tipocomprobante == 0 || cuentaxpagar=="")
                        {
                          $("#mdlTipComErr").modal('show');
                        }
                        else
                        {
                            var form_data = { case:1, cuentaxpagar:cuentaxpagar  }; 
                            $.ajax({
                              type: "POST",
                              url: "consultasBasicas/agregar_Cuenta_Pagar.php",
                              data: form_data,
                              success: function(response)
                              {
                                  var Result= JSON.parse(response);
                                  if(Result ==2){
                                      $("#mdlNoCnt").modal('show');
                                  } else {
                                      var form_data = { case:3, fecha: fecha, cuentaxpagar:cuentaxpagar, 
                                          comprobante: comprobanteAnterior, tercero:tercero }; 
                                      $.ajax({
                                        type: "POST",
                                        url: "consultasBasicas/agregar_Cuenta_Pagar.php",
                                        data: form_data,
                                        success: function(response)
                                        {
                                            console.log(response);
                                          if(response != 0)
                                          {

                                            $("#mdlGuarExt").modal('show');
                                          }
                                          else
                                          {
                                            $("#mdlGuarErr").modal('show');
                                          }

                                        }//Fin succes.
                                      })
                                  }
                              }
                          });  
                        }
                    }
                    
                    
                } else{
            
            if(comprobanteAnterior == '' || comprobanteAnterior == 0){
                $("#mdlNoHay").modal('show');
                $("#btnNoHay").click(function()
                    {
                      $("#mdlNoHay").modal('hide');

                    });
            } else {
                
            
            if(tipocomprobante == '' || tipocomprobante == 0 || cuentaxpagar=="")
            {
              $("#mdlTipComErr").modal('show');
              
            }
            else
            {
                var form_data = { case:1, cuentaxpagar:cuentaxpagar  }; 
                  $.ajax({
                    type: "POST",
                    url: "consultasBasicas/agregar_Cuenta_Pagar.php",
                    data: form_data,
                    success: function(response)
                    {
                        
                        var Result= JSON.parse(response);
                        if(Result ==2){
                            $("#mdlNoCnt").modal('show');
                        } else {
                            
                            var form_data = { case:2, fecha: fecha, cuentaxpagar:cuentaxpagar, 
                                comprobante: comprobanteAnterior, tercero:tercero }; 
                            $.ajax({
                              type: "POST",
                              url: "consultasBasicas/agregar_Cuenta_Pagar.php",
                              data: form_data,
                              success: function(response)
                              {
                                  console.log(response);
                                if(response != 0)
                                {
                                  
                                  $("#mdlGuarExt").modal('show');
                                }
                                else
                                {
                                  $("#mdlGuarErr").modal('show');
                                }

                              }//Fin succes.
                            })

                            
                        }
                    }
                });
            }
            }
        }
    }
        });
          }); //Fin Change.
      
        });

      </script>
      <div class="modal fade" id="mdlNoHay" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
              <p>No hay egreso generado para agregar cuenta por pagar.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="btnNoHay" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
              Aceptar
              </button>
            </div>
          </div>
        </div>
      </div>
      <!--FIN AGREGAR CUENTA POR PAGAR-->

      </div>

      <script type="text/javascript">
        
        $(document).ready(function()
        {
          $("#btnEliminarReg").click(function()
          {
            $("#mdlDeseaEliminar").modal('show');
        
          }); //Fin Change.
        });

      </script>


<script type="text/javascript">// Evalúa que la fecha no sea inferior a la fecha inicial del comprobante seleccioando.
  
    $("#fecha").change(function()
    {
         //VALIDAR SI YA TUVO CIERRE LA FECHA
        var fecha = $("#fecha").val();
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
    <script>
        $("#periodoCA").click(function(){
            $("#fecha").val("").focus();
        })
    </script>
<script>
       function fecha1(){
      var tipo = $("#tipoComprobante").val();
      var fecha =$("#fecha").val();
      var comp = $("#solicitudAprobada").val();
      
      var num = $("#numEgreso").val();
      
      if(tipo==''||tipo==''){
        $("#mdlSelTCF").modal('show');
        $("#ErrormdlSelTCF").click(function(){
            $("#fecha").val("");
            $("#fechaVen").val("");
            $("#mdlSelTCF").modal('hide');
            
        })
          
      } else{
        
         var form_data = { estruc: 9, tipComPal: tipo, fecha:fecha, comp:comp, num:$("#numEgreso").val()};
        
        $.ajax({
            type: "POST",
            url: "consultasBasicas/validarFechas.php",
            data: form_data,
            success: function(response)
            {
                console.log(response);
                if(response == 1)
                {
                    $("#myModalAlertErrFec").modal('show');
                    $("#AceptErrFec").click(function()
                    {
                        $("#fecha").val("");
                        $("#myModalAlertErrFec").modal('hide');
                    });

                }
            }
        });
      }
    }
</script> <!-- Fin fecha -->
<div class="modal fade" id="mdlSelTCF" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Seleccione Tipo Comprobante</p>
        </div>
        <div id="forma-modal" class="modal-footer">
            <button type="button" id="ErrormdlSelTCF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#btnGuardar").click(function()
    {
      var respuesta = $("#respuesta").val();
      respuesta = parseInt(respuesta);
      var tipocomprobante = $("#tipoComprobante").val();
      var fecha = $("#fecha").val();
      var id_comprobante_pptal = $("#solicitudAprobada").val();
      
      var numero = $("#numEgreso").val();
      var tercero = $("#tercero").val();
      if(tipocomprobante == '' || tipocomprobante == 0 )
      {
        $("#mdlTipComErr").modal('show');
      } else {
        if(tercero == '' || tercero == 0)
        {
          $("#mdlTerr").modal('show');
        } 
        else
        { 
            if(fecha =='' || fecha =="") {
                $("#mdlFec").modal('show');
            }  else {
            if(id_comprobante_pptal == '' || id_comprobante_pptal == 0 || id_comprobante_pptal=='N')
            {
            $("#myModalSinCuenta").modal('show');
            $("#btnSinCuenta").click(function()
                {
                    
                    //VERIFICAR SI TIENE RETENCION O NO
                     var form_data = { case: 24, tipo: $("#tipoComprobante").val()};
                    $.ajax({
                      type: "POST",
                      url: "consultasBasicas/busquedas.php",
                      data: form_data,
                      success: function(response)
                      {
                        
                        if(response == 1)
                        {
                            var form_data = { estruc: 24, tercero:tercero, id_comp_cnt: respuesta, tipocomprobante: tipocomprobante, fecha: fecha, id_comprobante_pptal: id_comprobante_pptal, numero: numero }; 
                            $.ajax({
                              type: "POST",
                              url: "estructura_aplicar_retenciones.php",
                              data: form_data,
                              success: function(response)
                              {
                                  console.log(response);
                                if(response != 0)
                                {
                                    console.log(response);
                                  response = parseInt(response);
                                  $("#ultimoCnt").val(response);
                                  $("#mdlGuarExt").modal('show');
                                }
                                else
                                {
                                  $("#mdlGuarErr").modal('show');
                                }

                              }//Fin succes.
                            }); 
                        } else{
                           var form_data = { estruc: 8, tercero:tercero, id_comp_cnt: respuesta, tipocomprobante: tipocomprobante, fecha: fecha, id_comprobante_pptal: id_comprobante_pptal, numero: numero }; 
                            $.ajax({
                              type: "POST",
                              url: "estructura_aplicar_retenciones.php",
                              data: form_data,
                              success: function(response)
                              {
                                 
                                if(response != 0)
                                {
                                    
                                  response = parseInt(response);
                                  $("#ultimoCnt").val(response);
                                  $("#mdlGuarExt").modal('show');
                                }
                                else
                                {
                                  $("#mdlGuarErr").modal('show');
                                }

                              }//Fin succes.
                            });  
                        }
                       }
                   });
                    
                   
                });
            
            } 
            else 
            {
        
                if(respuesta != 0 && respuesta != "" && !isNaN(respuesta))
                {
                  //VERIFICAR SI TIENE RETENCION O NO
                     var form_data = { case: 24, tipo: $("#tipoComprobante").val()};
                    $.ajax({
                      type: "POST",
                      url: "consultasBasicas/busquedas.php",
                      data: form_data,
                      success: function(response)
                      {
                        console.log(response);
                        if(response == 1)
                        {
                            
                           var form_data = { estruc: 24, tercero:tercero, id_comp_cnt: respuesta, tipocomprobante: tipocomprobante, fecha: fecha, id_comprobante_pptal: id_comprobante_pptal, numero: numero }; 
                            $.ajax({
                              type: "POST",
                              url: "estructura_aplicar_retenciones.php",
                              data: form_data,
                              success: function(response)
                              {
                                  console.log(response);
                                if(response != 0)
                                {
                                    console.log(response);
                                  response = parseInt(response);
                                  $("#ultimoCnt").val(response);
                                  $("#mdlGuarExt").modal('show');
                                }
                                else
                                {
                                  $("#mdlGuarErr").modal('show');
                                }

                              }//Fin succes.
                            }); 
                            
                            
                            
                        } else{
                            var form_data = { estruc: 8, tercero:tercero, id_comp_cnt: respuesta, tipocomprobante: tipocomprobante, fecha: fecha, id_comprobante_pptal: id_comprobante_pptal, numero: numero }; 
                            $.ajax({
                              type: "POST",
                              url: "estructura_aplicar_retenciones.php",
                              data: form_data,
                              success: function(response)
                              {
                                  console.log(response);
                                if(response != 0)
                                {

                                  response = parseInt(response);
                                  $("#ultimoCnt").val(response);
                                  $("#mdlGuarExt").modal('show');
                                }
                                else
                                {
                                  $("#mdlGuarErr").modal('show');
                                }

                              }//Fin succes.
                            }); //Fin ajax. 
                        }
                       }
                   });
                   
                   
                 
                }
                else
                {
                  $("#mdlNoCnt").modal('show');
                }
    }
    }
      }
    }
    
    });

  });
 
</script>
<div class="modal fade" id="myModalSinCuenta" role="dialog" align="center" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea guardar Egreso sin cuenta por pagar?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnSinCuenta" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  $(document).ready(function()
  {
    $('#btnEnviar').click(function()
    {
      var tercero = $("#tercero").val();
      var idCompCnt = $("#respuesta").val();
      idCompCnt = parseInt(idCompCnt);
            var form_data = { case: 24, tipo: $("#tipoComprobante").val()};
            $.ajax({
              type: "POST",
              url: "consultasBasicas/busquedas.php",
              data: form_data,
              success: function(response)
              {
                console.log(response);
                if(response == 1)
                { 
                    
                    var valorTot = $("#valorTotal").val();
                    
                    var idCompPtal = $("#id_comp_pptal_GE").val();

                    var form_data = { estruc: 11, valorTot: valorTot, idCompPtal: idCompPtal};
                    $.ajax({
                      type: "POST",
                      url: "estructura_aplicar_retenciones.php",
                      data: form_data,
                      success: function(response)
                      {
                        console.log(response);
                        if(response == 0)
                        {
                          document.location = 'gf_APLICAR_RETENCIONES.php?id=2'; // Dejar
                          
                        }
                        else
                        {
                          $("#mdlErrYaCnt").modal('show');
                        }
                      }
                    });
                
                } else {
                var form_data = { estruc: 7, idpptalE :$("#id_comp_pptal_GE").val() }; 
                $.ajax({
                  type: "POST",
                  url: "estructura_modificar_eliminar_pptal.php",
                  data: form_data,
                  success: function(response)
                  {
                      console.log(response);
                      if(response ==1){
                       document.location = 'registro_COMPROBANTE_EGRESO.php';  //Dejar
                        }
                        else {
                            
                        }
                    //window.open('registro_COMPROBANTE_EGRESO.php');   //Probar con esta                
                    }//Fin succes.
                      }); //
                    }
                      }
                  });
    });
    
  });
</script>
 <div class="modal fade" id="mdlErrYaCnt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Ya tiene comprobante CNT. No puede proseguir.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnErrYaCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
          Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

        <!-- Script para cargar datos en el combo select Rubro a partir del lo que se seleccione en el combo select Concepto. -->
      <script type="text/javascript">

      </script>

   <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

             $(document).ready(function()
             {  
                $("#tercero").change(function()
                {
//                    
                 var opcion = '<option value="" >Cuenta por Pagar</option>';

                  if(($("#tercero").val() == "")||($("#tercero").val() == 0))
                  { 
                    $("#solicitudAprobada").html(opcion);
                  }
                  else
                  {
                    var form_data = { id_tercero:+$("#tercero").val(), clase: 16, tipoOp: 1, signo: '=' };
                    $.ajax({
                      type: "POST",
                      url: "estructura_tercero_cuenta_pagar.php",
                      data: form_data,
                      success: function(response)
                      {                          
                        if(response == "" || response == 0)
                        {
                          var noHay = '<option value="N" >No hay Cuenta por Pagar</option>';
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


<?php 
  if(!empty($_SESSION['id_comp_pptal_GE']) && empty($_SESSION['nuevo_GE']))
  {
?>
          <script type="text/javascript"> //Código JS para asignar un comprobante a partir de un tercero.

             $(document).ready(function()
             {  
                 var opcion = '<option value="" >Cuenta por Pagar</option>';
                    var id_comp_pptal_GE = $("#id_comp_pptal_GE").val();
                    var form_data = { id_tercero:+$("#tercero").val(), clase: 16, tipoOp: 1, signo: '=' };
                    $.ajax({
                      type: "POST",
                      url: "estructura_tercero_cuenta_pagar.php",
                      data: form_data,
                      success: function(response)
                      {                          
                        opcion += response;
                        $("#solicitudAprobada").html(opcion).focus();
                        $('#solicitudAprobada > option[value="' + id_comp_pptal_GE + '"]').attr('selected', 'selected');
                      }//Fin succes.
                    }); //Fin ajax.
             });//Cierre Ready.

          </script> <!-- Código JS para asignación -->

<?php 
  }
?>

          <!-- El número de solicitud seleccionado -->
          <input name="numero" type="hidden" value="<?php echo $numero; ?>">

<input type="hidden" value="3" name="estado"> <!-- Estado 3, generada -->

        <input type="hidden" name="MM_insert" >

      </form>

<!-- Al seleccionar un número de solcitud, cargará  --> 
<script type="text/javascript">

  $(document).ready(function()
  {  
        $("#solicitudAprobada").change(function() 
        {
          
          <?php if(!empty($_SESSION['nuevo_GE'])){?>
              $("#fecha").val("");
          <?php } else { ?>
          if(($("#solicitudAprobada").val() == "")||($("#solicitudAprobada").val() == 0))
          { 
            var form_data = { estruc: 1, sesion: 'id_comp_pptal_GE', nuevo: 'nuevo_GE' }; //Estructura Uno 
            $.ajax({
              type: "POST",
              url: "estructura_sesiones.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.
          }
          else if($("#solicitudAprobada").val() != "N")
          {
            var form_data = { estruc: 2, id_comp:+$("#solicitudAprobada").val(), sesion: 'id_comp_pptal_GE', nuevo: 'nuevo_GE'  }; //Estructura Dos 
            $.ajax({
              type: "POST",
              url: "estructura_sesiones.php",
              data: form_data,
              success: function(response)
              {
                document.location.reload();                             
              }//Fin succes.
            }); //Fin ajax.

          }
      <?php }?>//Cierre else.              
        });//Cierre change.
    
     });//Cierre Ready.

</script> <!-- Fin de recargar la página al seleccionar Solicitud nueva -->


<script type="text/javascript">
  $(document).ready(function()
  {  
    $("#btnNuevo").click(function() 
    {
      var form_data = { estruc: 1, sesion: 'id_comp_pptal_GE', nuevo: 'nuevo_GE' }; //Estructura Uno 
      $.ajax({
        type: "POST",
        url: "estructura_sesiones.php",
        data: form_data,
        success: function(response)
        {
          document.location.reload();                             
        }//Fin succes.
      }); //Fin ajax.
    });
  });


</script>
<div><br/></div>

  </div> <!-- Cierra clase client-form contenedorForma -->
</div> <!-- Cierra col-sm-10 -->


<?php 

  if(!empty($_SESSION['id_comp_pptal_GE']))
  {
?>
  <script type="text/javascript">

    //$("#btnEnviar").prop("disabled", true);
    $("#btnGuardar").prop("disabled", false);
    $("#btnImprimir").prop("disabled", true);
    
    
  </script>

<?php 
  }
  else
  {
?>

  <script type="text/javascript">

    $("#btnEnviar").prop("disabled", true);
    $("#btnGuardar").prop("disabled", true);
    $("#btnImprimir").prop("disabled", true);
    $
    ("#btnAgregarCuentaPorPagar").prop("disabled", true);
	 
  </script>
<?php
  }

  if(!empty($_SESSION['nuevo_GE']) && $_SESSION['nuevo_GE'] != 3)
  {
?>
 <script type="text/javascript">

     $("#btnGuardar").prop("disabled", true);
     
     $("#btnEnviar").prop("disabled", false);
     $("#btnImprimir").prop("disabled", false);
	
  </script>

<?php
  } elseif(!empty($_SESSION['nuevo_GE']) && $_SESSION['nuevo_GE'] == 3) 
  {

    ?>
 <script type="text/javascript">

     $("#btnGuardar").prop("disabled", true);
     $("#btnAgregarCuentaPorPagar").prop("disabled", false);
     $("#btnEnviar").prop("disabled", false);
     $("#btnImprimir").prop("disabled", false);  
	 

  </script>

<?php

  } 

?>
 


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


<input type="hidden" id="idPrevio" value="">
      <input type="hidden" id="idActual" value="">

<!-- Listado de registros -->
 <div class="table-responsive contTabla col-sm-12" style="margin-top: 5px;">
          <div class="table-responsive contTabla" >
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Fuente</strong></td>
                <td class="cabeza"><strong>Rubro Presupuestal</strong></td>
                <td class="cabeza"><strong>Valor Aprobado</strong></td>
                <td class="cabeza"><strong></strong>Cuenta Débito</td>
              </tr>

              <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Fuente</th>
                <th>Rubro Presupuestal</th>
                <th>Valor Aprobado</th>
                <th>Cuenta Débito</th>
              </tr>

            </thead>
            <tbody>
              
              <?php
                if(!empty($_SESSION['id_comp_pptal_GE']) && ($resultado == true))
                {
                  $valorTotal = 0;
                  $matriz  = array();
                  
                  while($row = mysqli_fetch_row($resultado))
                  { 
                      $valorTotal += $row[2] ;
                      $valorPpTl = $row[2];

                ?>
               <tr>

                <td class="oculto"><?php echo $row[0]?>
                  <input  id="id_det_com<?php echo $row[0];?>" type="hidden" value="<?php echo $row[0];?>" >
                </td>
                <td class="campos" > <!-- Botones modificar y eliminar -->
                    <?php 
                    if(!empty($_SESSION['id_comp_pptal_GE']) && !empty($_SESSION['nuevo_GE']))
                                {
                                ##BUSCAR FECHA COMPROBANTE 
                                $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comp_pptal_GE'];
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

                                <?php 
                                } else { ?> 
                                <a class href="#<?php echo $row[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                </a>
                                <a class href="#<?php echo $row[0];?>" onclick="javascript:modificarDetComp(<?php echo $row[0];?>);" ><i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                </a>
                                <?php } } ?> 

                </td>

                <td class="campos" align="left">
                  <div class="acotado">
                    <?php echo ucwords(mb_strtolower($row[4]));?>
                  </div>
                  
                </td>

                <td class="campos" align="left" > <!-- Rubro presupuestal -->
                  <div class="acotado">
                    <?php echo (ucwords(mb_strtolower($row[1])));?>
                  </div>
                </td>

                <td class="campos" align="right" style="padding: 0px"> <!-- Valor aprobado -->

                  

                  <div id="divVal<?php echo $row[0];?>" class="divValor" style="margin-right: 10px;">
                    <?php  
                      echo number_format($valorPpTl, 2, '.', ',');
                    ?>
                  </div>
               
                    <!-- Modificar los valores -->

                          <table id="tab<?php echo $row[0];?>" style="padding: 0px; background-color: transparent; background:transparent; margin: 0px;">
                            <tr>
                              <td style="padding: 0px;">

                              <input type="text" name="valorMod" id="valorMod<?php echo $row[0];?>" maxlength="50" style="margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row[0];?>');" value="<?php echo number_format($valorPpTl, 2, '.', ','); ?>" required>

                            </td>

                            
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

                       $("#"+idTab).css("display", "none");

                    </script>
          </td>
<td class="campos" align="right" style="padding: 0px"> <!-- Saldo por pagar -->
                      <?php 
                                $queryCuenDeb = "SELECT cuen.id_unico, cuen.codi_cuenta, cuen.nombre, cc.codi_cuenta, cc.nombre , conRubCun.id_unico
                                FROM gf_cuenta cuen 
                                LEFT JOIN gf_concepto_rubro_cuenta conRubCun ON conRubCun.cuenta_debito = cuen.id_unico 
                                LEFT JOIN gf_concepto_rubro conRub ON conRub.id_unico = conRubCun.concepto_rubro 
                                LEFT JOIN gf_rubro_pptal rub ON rub.id_unico = conRub.rubro 
                                LEFT JOIN gf_rubro_fuente rubFue ON rubFue.rubro = rub.id_unico 
                                LEFT JOIN gf_cuenta cc ON conRubCun.cuenta_credito = cc.id_unico 
                                WHERE rubFue.id_unico = $row[3]";

                                $cuentaDeb = $mysqli->query($queryCuenDeb);
                                $cuentaDebRow = $mysqli->query($queryCuenDeb);

                                $rowCDPrimer = mysqli_fetch_row($cuentaDeb);
                                $idCP = (int)$row[0];
                                $idPrimerDebito = (int)$rowCDPrimer[5];
                                $debitoCP[$idCP] = $idPrimerDebito;
                                ?>

                                <input type="hidden" id="cuenDebOc">

                                <select name="cuenDeb" id="cuenDeb<?php echo $row[0];?>" onchange="javascript:cambiarVector(<?php echo $row[0];?>);" class="form-control input-sm" title="Seleccione una Cuenta Débito" style="width:150px;" required >
                                <!--  <option value=""> Cuenta Débito </option> -->
                                <?php //echo $row[3];
                                while($rowCD = mysqli_fetch_row($cuentaDebRow))
                                {
                                echo '<option value="'.$rowCD[5].'">'.$rowCD[1].' '.$rowCD[2].' - '.$rowCD[3].' '.$rowCD[4].'</option>';
                                }
                                ?>
                                </select>

                                <script type="text/javascript">
                                $(document).ready(function()
                                {
                                var valorVal = $("#cuenDeb<?php echo $row[0];?>").val();


                                $("#cuenDeb").change(function(){
                                var cuenDeb = $("#cuenDeb").val();
                                $("#cuenDebOc").val(cuenDeb);
                                });

                                });
                                </script>

                </td> <!-- Saldo por pagar -->
                  
              </tr>
          <?php 
                  
                }
              
              }
          ?>

            </tbody>
          </table>

          <input type="hidden" id="matriz" value="">

          <div class="col-sm-12" style="margin-top:5px; padding: 0px;" > 

                <div class="valorT" style="font-size: 12px;" align="right">
                  <span style="margin-right: 10px;"> Valor Total Aprobado:  </span>
                    <label style="margin-right: 10px;">
                      <?php 
                        if(!empty($valorTotal))
                        {
                          echo number_format($valorTotal, 2, '.', ',');
                        }
                      ?>
                    </label>
                        <?php 
                        if(!empty($valorTotal))
                        { ?>
                          <input type="hidden" id="valorTotal" value="<?php echo $valorTotal; ?>"> 
                        <?php } ?>
                  
                    

            </div>

        </div> <!-- table-responsive contTabla -->
       
      </div> <!-- Cierra clase table-responsive contTabla  -->
      
    </div> <!-- Cierra clase col-sm-10 text-left -->
  </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->
<?php 
        if(!empty($debitoCP))
        {
        $debitoCP_serial = serialize($debitoCP);
        $_SESSION['debitoCP'] = $debitoCP_serial;
        }
        ?>

        <script type="text/javascript"> //Modifica vector de débito
        function cambiarVector(id, cuentaC)
        {
        var idCuenDeb = 'cuenDeb'+id;
        var valor = $("#"+idCuenDeb).val();
        var ctaC = cuentaC
        var form_data = { estruc: 12, id: id, valor: valor, ctaC:ctaC };
        $.ajax({
        type: "POST",
        url: "estructura_aplicar_retenciones.php",
        data: form_data,
        success: function(response)
        {
            console.log(response);
        }
        });
        }

        </script>
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
  <div class="modal fade" id="mdlGuarErr" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuarErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error de fecha de vencimiento vacía --> 
  <div class="modal fade" id="mdlGuarExt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuarExt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error de fecha de vencimiento vacía --> 
  <div class="modal fade" id="mdlNoCnt" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Este comprobante no cuenta con comprobante contable afectado.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnNoCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Error de fecha de vencimiento vacía --> 
  <div class="modal fade" id="mdlTipComErr" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No hay un tipo de comprobante seleccionado.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnTipComErr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
 <div class="modal fade" id="mdlTerr" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione Tercero.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnmdlTerr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione Fecha.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnmdlFecr" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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

<div class="modal fade" id="mdlDeseaEliminar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar los detalles del comprobante seleccionado?</p>
      </div>
      <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnAcepElim" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" id="btnCancelElim" class="btn"  style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

 <div class="modal fade" id="mdlCompEliminado" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnCompEliminado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="mdlCompNoEliminado" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnCompNoEliminado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


 <div class="modal fade" id="mdlModificadoComExito" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información modificada correctamente.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModificadoComExito" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
        Aceptar
        </button>
      </div>
    </div>
  </div>
</div>


  <div class="modal fade" id="mdlModificadoComError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido modificar la información.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModificadoComError" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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
          <?php if(empty($_SESSION['nuevo_GE'])) { ?>
              $("#mdlGenerarCXP").modal('show');
              $('#btnmdlGenerarCXP').click(function(){
                    $("#mdlGenerarCXP").modal('hide');
                  });
      <?php } else {  ?>
          eliminarDetCompA(id);
      <?php } ?>
      }
  </script>
  
<script type="text/javascript">
      function eliminarDetCompA(id)
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
        document.location = 'GENERAR_EGRESO.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'GENERAR_EGRESO.php';
      });
    
  </script>

<!-- Fin funciones eliminar -->
<!-- Función para la modificación del registro. -->
<script type="text/javascript">

  function modificarDetComp(id)
  {
    <?php if(empty($_SESSION['nuevo_GE'])|| $_SESSION['nuevo_GE']=='') { ?>
          $("#mdlGenerarCXP").modal('show');
              $('#btnmdlGenerarCXP').click(function(){
                    $("#mdlGenerarCXP").modal('hide');
                  });  
    <?php }  else { ?>
        modificarDetCompA(id)
    <?php } ?>
     
  }

</script>
<div class="modal fade" id="mdlGenerarCXP" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Debe generar primero el Egreso.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="btnmdlGenerarCXP" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">

  function modificarDetCompA(id)
  {
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
   console.log(valor);
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
    var idDetComp = "id_det_comp"+id_txt;
    var validar = $("#"+idValMod).val();
    var id_det_comp = $("#"+idDetComp).val();
    var id_ocul = "valOcul"+id_txt;
    var valOriginal = $("#"+id_ocul).val();

    validar = parseFloat(validar.replace(/\,/g,'')); //Elimina la coma que separa los miles.
    //valOriginal = parseFloat(valOriginal.replace(/\,/g,''));

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
      var form_data = { proc: 4, id_rubFue: id_rubFue, id_comp: id_det_comp , clase: 15};
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
  /* function valida()
  {
    if($("#fechaVen").val() == "")
    {
      $("#ModalAlertFecVen").modal('show');
      return false;
    }
    
    return true;

  } */
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
  //Si se ingresan valores superiores a los valores para aprobar en alguna de las casillas 
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
    <?php if(empty($fecha)|| $fecha=='') { ?>
    $("#fecha").datepicker({changeMonth: true}).val(fecAct);
    <?php }  else { ?>
    var fechaI = '<?php echo date("d/m/Y", strtotime($rowComp[2]));?>';
    $("#fecha").datepicker({changeMonth: true}).val(fechaI);
    <?php } ?>
    $("#fechaVen").val("");

  });
    
</script>

<script type="text/javascript">
  
  $('#AceptErrFecVen').click(function(){
    $("#fecha").focus();
  });

</script>

<script type="text/javascript">
  
  $('#btnTipComErr').click(function(){
     $("#tipoComprobante").focus();
  });

</script>

<script type="text/javascript">
  
  $('#AceptErrFecVen').click(function(){
    $("#fecha").focus();
  });

</script>

<script type="text/javascript">
  
  $('#btnGuarExt').click(function(){
    document.location.reload(); //Dejar siempre. Quitar para probar.
    $("#btnEnviar").prop("disabled", false);
    $("#btnGuardar").prop("disabled", true);
  });

</script>


<script type="text/javascript">
  
  $('#btnAcepElim').click(function()
  {
    
    var idpptal = $("#id_comp_pptal_GE").val();
    var idcnt = $("#respuesta").val();
    
    var form_data = { estruc: 6, idpptal: idpptal, idcnt:idcnt }; 
    $.ajax({
      type: "POST",
      url: "estructura_modificar_eliminar_pptal.php",
      data: form_data,
      success: function(response)
      {
          console.log(response);
        //document.location.reload();
        if(response == 1)
        {
          $("#mdlCompEliminado").modal('show');
        }
        else
        {
          $("#mdlCompNoEliminado").modal('show');
        }
      }//Fin succes.
    }); //Fin ajax.

  });

</script>

<script type="text/javascript">
  
  $('#btnCompEliminado').click(function()
  {
	document.location.reload();
  });

</script>

</script>

<script type="text/javascript">
  
  $('#btnModificadoComExito').click(function()
  {
    document.location.reload();
  });

</script>
<?php 
###VERIFICAR SI TIENE CNT ####
?>
 <!--CIERRE 
    ###BUSCAR EL CIERRE MAYOR
    --->
    <?php

    if(!empty($_SESSION['id_comp_pptal_GE']) && !empty($_SESSION['nuevo_GE']))
    {
    ##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = ".$_SESSION['id_comp_pptal_GE'];
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
        
        $("#btnEliminarReg").prop("disabled", true);
        $("#btnAgregarCuentaPorPagar").prop("disabled", true);
        $("#btnEliminarReg").prop("disabled", true);
        $("#btnEnviar").prop("disabled", true);
    
    });
    </script>
    <?php } else { ?>
<script>
  $(document).ready(function()
  {
     var valorTot = $("#valorTotal").val();
    var idCompPtal = $("#idComPtal").val();

    var form_data = { estruc: 11, valorTot: valorTot, idCompPtal: idCompPtal};
    $.ajax({
      type: "POST",
      url: "estructura_aplicar_retenciones.php",
      data: form_data,
      success: function(response)
      {
        console.log(response);
        if(response == 0)
        {
          
          $("#btnEnviar").prop("disabled", false);
          $("#btnVerCnt").prop("disabled", true);
          $("#btnImprimir").prop("disabled", true);
          $("#btnEliminarReg").prop("disabled", true);
        }
        else
        {
          
          $("#btnEnviar").prop("disabled", true);
          $("#btnVerCnt").prop("disabled", false);
          $("#btnImprimir").prop("disabled", false);
          $("#btnEliminarReg").prop("disabled", false);
        }
      }
    });
  })
</script>
<?php }} else { ?>
<script>
    $(document).ready(function()
    {
        
        $("#btnEliminarReg").prop("disabled", true);
        $("#btnAgregarCuentaPorPagar").prop("disabled", true);
        $("#btnEliminarReg").prop("disabled", true);
        $("#btnEnviar").prop("disabled", true);
        $("#btnVerCnt").prop("disabled", true);
    
    });
    </script>
<?php } ?>
</body>
</html>

