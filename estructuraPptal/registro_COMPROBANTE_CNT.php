<?php
#######################MODIFICACIONES ############################
#10/04/2017 || ERICA G. || MODIFICACION CONSULTA TERCEROS, LISTAR
#22/02/2017 || Erica G. || INHABILITAR GUARDADO
#21/02/2017 || Erica G. || INHABILITAR BOTON SIGUIENTE CUANDO TIENE EGRESO
#21/02/2017 || Erica G. || INHABILITAR BOTON SIGUIENTE CUANDO TIENE EGRESO
#10/02/2017 || ERICA G. || MODIFICAR VALOR DETALLES
#03/02/2017 || ERICA G. || AGREGAR CUENTA CONTABLE CAMPOS DEBITO Y CREDITO
##################################################################
  require_once ('Conexion/conexion.php');
  require_once ('head_listar.php');

  if(!empty($_SESSION['idCompCntV']))
  {
     ######PPTAL DE LA CUENTA POR PAGAR###### 
    $idComprobante = $_SESSION['idCompCntV'];
    $sql1="SELECT DISTINCT MAX(dcpttal.comprobantepptal)
        FROM
          gf_detalle_comprobante DT
        LEFT JOIN 
          gf_detalle_comprobante_pptal dcpttal ON DT.detallecomprobantepptal = dcpttal.id_unico
        WHERE (DT.comprobante) = $idComprobante";
        $rs1 = $mysqli->query($sql1);
        $pptal = mysqli_fetch_row($rs1);
        $pptal = $pptal[0];
         $_SESSION['id_comp_pptal_GE']=$pptal;
        

###########BUSCAR SI TIENE EGRRESO DESHABILITAR SIGUIENTE###
    $sig ="SELECT DISTINCT
  dc.comprobante
FROM
  gf_detalle_comprobante dc
LEFT JOIN
  gf_detalle_comprobante_pptal dp ON dc.detallecomprobantepptal = dp.id_unico
LEFT JOIN
  gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
LEFT JOIN
  gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
LEFT JOIN
  gf_clase_contable cc ON tc.clasecontable = cc.id_unico
WHERE cc.id_unico = 14 AND 
  dp.comprobantepptal = ".$_SESSION['id_comp_pptal_GE'];

$sig = $mysqli->query($sig);
$eg= mysqli_num_rows($sig);
if($eg>0) { ?>
<script>
    $(document).ready(function(){
       // console.log('sdfs');
        $("#btnEnviar").prop("disabled", true);  
        $("#fecha").prop("disabled",true)
        $("#claseContrato").prop("disabled",true)
        $("#numeroContrato").prop("disabled",true)
    })
</script>
<input type="hidden" name="eg" id="eg" value="<?php echo $eg;?>">
<?php }
##############################################################
  $queryTerCnt = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil 
    FROM gf_tercero ter 
    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico
    LEFT JOIN gf_comprobante_cnt comCnt ON comCnt.tercero = ter.id_unico 
    WHERE comCnt.id_unico = $idComprobante";
  $terceroCnt = $mysqli->query($queryTerCnt);

  

  $queryTipoComp = "SELECT  nombre 
    FROM gf_tipo_comprobante tipCom 
    LEFT JOIN gf_comprobante_cnt comCnt ON tipCom.id_unico = comCnt.tipocomprobante
    WHERE comCnt.id_unico = $idComprobante";
  $tipoCompro = $mysqli->query($queryTipoComp);
  $rowTC = mysqli_fetch_row($tipoCompro);
  
  }

  $queryTercero = "SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
    FROM gf_tercero ter 
    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico";
  $tercero = $mysqli->query($queryTercero); 

  // Los tipos de perfiles que se encunetran en la tabla gf_tipo_perfil.
  $natural = array(2, 3, 5, 7, 10); 
  $juridica = array(1, 4, 6, 8, 9);
  
  $arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'idCompCnt');
  
$sgf="SELECT
                        numerocontrato,
                        cc.id_unico,
                        cc.nombre, cp.fecha
                      FROM
                        gf_comprobante_pptal cp
                      LEFT JOIN
                        gf_clase_contrato cc ON cp.clasecontrato = cc.id_unico
                      WHERE
                        cp.id_unico = '$pptal'";
                $sgf=$mysqli->query($sgf);
                $sgf= mysqli_fetch_row($sgf);

?>     
 <?php 
             if(!empty($_SESSION['idCompCntV']))
  {
               $queryNumero = "SELECT numero  
                FROM gf_comprobante_cnt  
                WHERE id_unico = $idComprobante";
              $numeroCnt = $mysqli->query($queryNumero);
              $rowNC = mysqli_fetch_row($numeroCnt);
  }
   ?>
    <title>Comprobante CNT</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <!-- select2 -->
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>

    <link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
    
    <style>
/*Estilos tabla*/
table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px}
.dataTables_wrapper .ui-toolbar{padding:2px}
/*Campos dinamicos*/
.campoD:focus {
    border-color: #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
}
.campoD:hover{
    cursor: pointer;
}
/*Campos dinamicos label*/
.valorLabel{
    font-size: 10px;
}
.valorLabel:hover{
    cursor: pointer;
    color:#1155CC;
}
.select2-choice {min-height: 26px; max-height: 26px;}
/*td de la tabla*/
.campos{
    padding: 0px;
    font-size: 10px
}
/*cuerpo*/
body{
    font-size: 10px
}
.form-control{
    padding: 2px;
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
    $.datepicker.setDefaults($.datepicker.regional['es']);
    var fecha = '<?php echo date("d/m/Y", strtotime($sgf[3]));?>';
    $("#fecha").datepicker({changeMonth: true}).val(fecha);
    $("#fechaA").val(fecha)

  });

</script>



<!--MIRAR SI EL COMPROBANTE ESTA BALANCEADO-->
<script type="text/javascript">

  $(document).ready(function()
  {
    //Función que ejecuta consulta para verificar si las fuentes se encuentran balanceadas o no.
    var id= $("#id").val();

    var form_data = { case:21, id: id};
    $.ajax({
      type: "POST",
      url: "consultasBasicas/busquedas.php",
      data: form_data,
      success: function(response)
      {
          console.log(response);
          if(response==1){
              $("#btnEnviar").attr('disabled','disabled');
              
          }
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
      $("#btnEnviar").attr('disabled','disabled');
      
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
      
      $("#btnNuevo").attr('disabled','disabled');
      $("#sltBuscar").attr('disabled','disabled');
      if(y >= 0 && y <= 20 )
      {
        $("#modDesBal").modal('show');
        $("#btnDesBal").focus();
      }
    }
  }
</script>

 <!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">

    </head>
    <body  onMouseMove="coordenadas(event);">        
        <input type="hidden" id="balanceo" >
        <?php if(!empty($_SESSION['cntcxp'])) { ?>
        
        <input type="hidden" id="id" value="<?php echo $_SESSION['cntcxp']?>">
        <?php } ?>
        
        
      <input type="hidden" id="idComprobante" value="<?php echo $idComprobante; $_SESSION['cntcxp'] =$idComprobante?>">
      <input type="hidden" id="id_Comp_Ptal" value="<?php echo $_SESSION['id_comp_pptal_GE'];?>">
      <input type="hidden" id="fechaA" name="fechaA">
        <div class="container-fluid text-left">

            <div class="row content">

                <?php require_once('menu.php'); ?>

              <div class="col-sm-8 text-center" style="margin-top:-22px;">

                    <h2 class="tituloform" align="center" >Comprobante <?php  if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowTC[0]));}?></h2>
                    <div id="volver" style="display:inline-block;">
                    <a href="GENERAR_CUENTA_PAGAR.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none " title="Volver"></a>
                    </div>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(mb_strtolower($rowTC[0])).': '. $rowNC[0]?></h5>
                    
                    <div class="client-form contenedorForma col-sm-12" style="margin-top:-7px;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarComprobanteContable.php" style="margin-bottom:-20px">

                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>

 <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> <!-- Primera Fila -->

  <div class="col-sm-3" align="left"> <!-- Tercero -->
            <input type="hidden" name="terceroB" id="terceroB" required="required" title="Seleccione un tercero">
            <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
            <select name="tercero" id="tercero" onchange="llenar();" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width:150px;" required>
              <?php
                $rowCnt = mysqli_fetch_row($terceroCnt);

                if(in_array($rowCnt[7], $natural))
                {
              ?>
                <option value="<?php echo $rowCnt[0];?>"  selected="selected">
              <?php 
                  echo ucwords(mb_strtolower($rowCnt[1])).' '.ucwords(mb_strtolower($rowCnt[2])).' '.ucwords(mb_strtolower($rowCnt[3])).' '.ucwords(mb_strtolower($rowCnt[4])).' '.$rowCnt[6];
              ?>
              </option> 
              <?php
                  }
                  elseif (in_array($rowCnt[7], $juridica))
                  {
                    ?>
              <option value="<?php echo $rowCnt[0];?>" selected="selected">
                <?php echo ucwords(mb_strtolower($rowCnt[5])).' '.$rowCnt[6];?>
              </option> 
              <?php
                  }


                while($rowTerc = mysqli_fetch_row($tercero))
                {

                  if(in_array($rowTerc[7], $natural))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>" >
                <?php 
                  echo ucwords(mb_strtolower($rowTerc[1])).' '.ucwords(mb_strtolower($rowTerc[2])).' '.ucwords(mb_strtolower($rowTerc[3])).' '.ucwords(mb_strtolower($rowTerc[4])).' '.$rowTerc[6];
                ?>
              </option> 
              <?php
                  }
                  elseif (in_array($rowTerc[7], $juridica))
                  {
                    ?>
              <option value="<?php echo $rowTerc[0];?>" >
                <?php echo ucwords(mb_strtolower($rowTerc[5])).' '.$rowTerc[6];?>
              </option> 
              <?php
                  }
                }
              ?>
            </select>
          </div> <!-- Fin Tercero -->


           <div class="col-sm-3" align="left"> <!-- Descripción -->

            <?php 
             if(!empty($_SESSION['idCompCntV']))
  {
               $queryDesCnt = "SELECT descripcion  
                FROM gf_comprobante_cnt 
                WHERE id_unico = $idComprobante";
              $descripcionCnt = $mysqli->query($queryDesCnt);
              $rowDesCnt = mysqli_fetch_row($descripcionCnt);
              
  }
             ?>

            <label for="nombre" class="control-label" style="" >Descripción:</label><br/>
            <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 5px; width:250px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ><?php  if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowDesCnt[0]));}?></textarea> <!-- readonly="readonly" -->
          </div> <!-- Fin Descripción -->


          <div class="col-sm-3" align="left"> <!-- Tipo Comprobante -->

            <label for="tipoComprobante" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Tipo Comprobante:</label><br>
            <input name="tipoComprobante" id="tipoComprobante" class="form-control input-sm" title="Tipo Comprobante" style="width:180px;" value="<?php  if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowTC[0]));}?>" readonly >

          </div><!-- Fin Tipo Comprobante -->

          <div class="col-sm-3" align="left"> <!-- Número -->

           

            <label for="numeroCnt" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Número Comprobante:</label><br>
            <input name="numeroCnt" id="numeroCnt" class="form-control input-sm" title="Número Comprobante" style="width:180px;" value="<?php  if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowNC[0]));}?>" readonly >

          </div><!-- Fin Número -->

             

       </div> <!-- Fin de la primera fila -->



       <div class="form-group form-inline col-sm-9" style="margin-left:0px"> <!-- Botones --> 
          
            <div class="col-sm-4" align="left"> <!-- Tipo Contrato-->
                <label for="claseContrato" class="control-label" style=""><strong style="color:#03C1FB;"></strong>Clase Contrato:</label><br>
                <select name="claseContrato" id="claseContrato" class="form-control input-sm" title="Clase Contrato" style="width:150px;">
                <?php if(!empty($sgf[1])) { ?>
                    <option value="<?php echo $sgf[1]?>"><?php echo ucwords(mb_strtolower($sgf[2]))?></option>
                    <?php $clasecon= "SELECT id_unico, nombre FROM gf_clase_contrato WHERE id_unico != '$sgf[1]' ORDER BY nombre ASC ";
                    $clasecon = $mysqli->query($clasecon);
                    while ($rowcc = mysqli_fetch_row($clasecon)) { ?>
                    <option value="<?php echo $rowcc[0]?>"><?php echo ucwords(mb_strtolower($rowcc[1]))?></option>    
                <?php } ?><option value="">-</option><?php } else { ?>
                    <option value="">Clase Contrato</option>
                    <?php $clasecon= "SELECT id_unico, nombre FROM gf_clase_contrato ORDER BY nombre ASC ";
                    $clasecon = $mysqli->query($clasecon);
                    while ($rowcc = mysqli_fetch_row($clasecon)) { ?>
                    <option value="<?php echo $rowcc[0]?>"><?php echo ucwords(mb_strtolower($rowcc[1]))?></option>    
                <?php } } ?>

                </select>  
            </div>
          <div class="col-sm-4" align="left" ><!--  Numero -->
                <label class="control-label"><strong style="color:#03C1FB;"></strong>Número Contrato:</label> <br/>
                <?php if(!empty($sgf[0])) { ?>
                <input class="form-control input-sm" type="text" name="numeroContrato" id="numeroContrato" style="width:180px;" title="Ingrese Número Contrato" placeholder="Número Contrato" value="<?php echo $sgf[0]?>" >
                <?php }  else { ?>
                <input class="form-control input-sm" type="text" name="numeroContrato" id="numeroContrato" style="width:180px; " title="Ingrese Número Contrato" placeholder="Número Contrato">
                <?php } ?>
            </div>
          <div class="col-sm-4"  align="left"><!--  Numero -->
                <label class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>
                <input class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:180px; " title="Ingrese Número Contrato" placeholder="Fecha">
                
            </div>
           <script>
               $("#fecha").change(function(){
                    var comp= $("#id_Comp_Ptal").val();
                    var fecha = $("#fecha").val();
                    var num = $('#numeroCnt').val();
                    var form_data = { estruc: 7,  comp:comp, fecha:fecha, num:num};
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
                                    var fechaA=$("#fechaA").val();
                                    $("#fecha").val(fechaA);
                                    $("#myModalAlertErrFec").modal('hide');
                                });

                            }
                        }
                    });
               })
           </script>
      </div>
       <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
              <p>La fecha es inválida. Verifique nuevamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
              Aceptar
              </button>
            </div>
          </div>
        </div>
      </div>
       <div class="form-group form-inline col-sm-3" style="margin-top: -30px"> <!-- Botones --> 
        <div class="col-sm-4" style="margin-top: 19px;">
          <button type="button" id="btnModificar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar" >
            <li class="glyphicon glyphicon-pencil"></li>
          </button>
        </div>                      

         <div class="col-sm-4" style="margin-top: 19px;">
                <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();"><img src="images/hb2.png" style="width: 14px; height: 17.28px"></button> <!--Firma Dactilar-->
          </div>    

        <div class="col-sm-4" style="margin-top: 19px; ">
          <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Imprimir" >
            <li class="glyphicon glyphicon-print"></li>
          </button>  
        </div>


        <script type="text/javascript">
            $(document).ready(function()
            {
              $("#btnImprimir").click(function(){
                window.open('informesPptal/inf_Comp_Cuent_Pagar.php');
              });
            });
        </script>
     </div>

<div class="form-group form-inline col-sm-3" style="margin-top: -30px"> <!-- Botones --> 
          <div class="col-sm-4" style="margin-top: 19px; "> <!-- Botón siguiente -->

              <button type="button" id="btnEnviar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Siguiente" ><li class="glyphicon glyphicon-arrow-right"></li></button> <!-- glyphicon glyphicon-floppy-disk Guardar--> 

        </div> <!-- Fin Botones nuevo -->
        
        <!--#####AGREGAR CUENTA CONTABLE######--->
        <?php if(!empty($_SESSION['idCompCntV'])) { ?>
            <!--#BOTON AGREGAR#-->
            <div class="col-sm-4" style="margin-top: 19px; ">      
                <button type="button" id="btnAgregarCuentaContable" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Siguiente" >
                    <li class="glyphicon glyphicon-plus"></li> Cuenta Contable
                </button> 
            </div>
      </div>
  </form>
            <!--#FIN BOTON AGREGAR#-->
            <!--#MODAL AGREGAR#-->
            <script>
                $(document).ready(function(){
                    $("#btnAgregarCuentaContable").click(function(){
                        var ter = document.getElementById('tercero').value;
                        document.getElementById('slttercero').value =ter;
                        
                        $("#mdlAgregarCuentaC").modal('show');
                    });
                });   
            </script>
            <div class="modal fade" id="mdlAgregarCuentaC" role="dialog" align="center" >
                <div class="modal-dialog" >
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Cuenta Contable</h4>
                        </div>
                        <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="guardarCuenta">
                        <div class="modal-body" style="margin-top: 8px">
                            
                                <div class="form-group" style="margin-top: 5px;">                                    
                                   <label class="control-label" style="display:inline-block; width:140px"><strong class="obligado">*</strong>Cuenta: </label>
                                    <select name="sltcuenta" id="sltcuenta" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione cuenta" required="required">
                                        <option value>Cuenta</option>
                                        <?php $cuentaA = "SELECT id_unico,
                                                codi_cuenta,
                                                nombre 
                                        FROM    gf_cuenta
                                        WHERE   movimiento = 1
                                        OR      centrocosto = 1
                                        OR      auxiliartercero = 1
                                        OR      auxiliarproyecto = 1 
                                        ORDER BY codi_cuenta ASC";
                                        $cuentaA = $mysqli->query($cuentaA);
                                        $val=0;
                                        if(mysqli_num_rows($cuentaA)>0) { 
                                            while ($rowCuentaA = mysqli_fetch_row($cuentaA)) {
                                                $sum = "SELECT SUM(valor) FROM gf_detalle_comprobante WHERE cuenta = $rowCuentaA[0]";
                                                $sum = $mysqli->query($sum);
                                                if(mysqli_num_rows($sum)>0) { 
                                                    $val= mysqli_fetch_row($sum);
                                                    if($val[0]==NULL){$val=0;}else{
                                                    $val = $val[0];}
                                                } else {
                                                    $val = 0;
                                                } ?>
                                        <option value="<?php echo $rowCuentaA[0]?>"><?php echo $rowCuentaA[1].' - '. ucwords(mb_strtolower($rowCuentaA[2]).'- Saldo: $'.$val)?></option>  
                                            <?php }
                                        }?>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#slttercero").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#slttercero").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarTercero.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var tercero = document.getElementById('slttercero');
                                                        if (data==1) {
                                                            tercero.disabled=false; 
                                                        }else if(data==2){
                                                            $("#slttercero").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltcentroc").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltcentroc").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarCentroC.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltcentroc');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltcentroc").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltproyecto").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltproyecto").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultaProyecto.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltproyecto');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltproyecto").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                    </select>
                                </div>  
                                <div class="form-group" style="margin-top:5px;">
                                    <?php                                     
                                    $sql = "SELECT  
                                        IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, 
                                        ter.apellidouno, ter.apellidodos) IS NULL 
                                        OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, 
                                        ter.apellidodos)='' ,(ter.razonsocial),
                                        CONCAT_WS(' ',ter.nombreuno,ter.nombredos,
                                        ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                        ter.id_unico, 
                                        CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' 
                                        FROM gf_tercero ter
                                        LEFT JOIN gf_tipo_identificacion ti 
                                        ON ti.id_unico = ter.tipoidentificacion ORDER BY NOMBRE ASC ";
                                    $rs = $mysqli->query($sql);
                                    ?>
                                    <label class="control-label" style="display:inline-block; width:140px">
                                        <strong class="obligado">*</strong>Tercero
                                    </label>
                                    <select name="slttercero" id="slttercero" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione tercero" required="">
                                        <option value="2">Tercero</option>
                                        <?php 
                                        while($row=  mysqli_fetch_row($rs)){ ?>
                                        
                                        <option value="<?php echo $row[1]?>"><?php echo ucwords(mb_strtolower($row[0].'('.$row[2].')'));?></option>
                                        
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 5px;" >
                                    
                                    <label class="control-label" style="display:inline-block; width:140px">
                                        <strong class="obligado"></strong>Centro Costo:
                                    </label>
                                    <?php 
                                    $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = 12 ORDER BY nombre ASC";
                                    $a = $mysqli->query($sqlCC);
                                    $filaC = mysqli_fetch_row($a);
                                    $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] ORDER BY nombre ASC";
                                    $r = $mysqli->query($sqlCT);
                                    ?>
                                    <select name="sltcentroc" id="sltcentroc" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione centro costo" required="">
                                        
                                            <option value="12">Centro Costo</option>
                                            <option value="<?php echo $filaC[0]; ?>"><?php echo ucwords( (mb_strtolower($filaC[1]))); ?></option>
                                            <?php 
                                            while($fila2=  mysqli_fetch_row($r)){ ?>
                                                <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords( (mb_strtolower($fila2[1]))); ?></option>   
                                            <?php                                          
                                            }
                                        
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 5px;" >
                                    
                                    <label class="control-label" style="display:inline-block; width:140px">
                                        <strong class="obligado"></strong>Proyecto:
                                    </label>
                                    <select name="sltproyecto" id="sltproyecto" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione proyecto" >
                                        <?php 
                                            $sqlP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'" ;
                                            $d = $mysqli->query($sqlP);                                    
                                            $filaP = mysqli_fetch_row($d);
                                            $sqlPY = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $filaP[0]" ;
                                            $X = $mysqli->query($sqlPY);
                                            ?>
                                            <option value="<?php echo $filaP[0]; ?>">Proyecto</option>
                                            <option value="<?php echo $filaP[0]; ?>"><?php echo ucwords( (mb_strtolower($filaP[1]))) ?></option>
                                            <?php                                         
                                            while($fila3 = mysqli_fetch_row($X)){ ?>
                                                <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords( (mb_strtolower($fila3[1]))) ?></option>
                                            <?php
                                            }
                                        
                                        ?>
                                    </select>
                                </div>
                                <script type="text/javascript">                                                                                                                                          
                                    function justNumbers(e){   
                                        var keynum = window.event ? window.event.keyCode : e.which;
                                        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                                        return true;
                                        return /\d/.test(String.fromCharCode(keynum));
                                    }
                                </script>
                                <div class="form-group" style="margin-top:5px;">
                                    <label class="control-label" style="display:inline-block; width:140px">
                                        <strong class="obligado">*</strong>Valor Débito:
                                    </label>
                                    <input type="text" name="txtValorDebito" onkeypress="return justNumbers(event);" id="txtValorDebito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="debito();"/>
                                </div>
                                <div class="form-group" style="margin-top:5px;">
                                    <label class="control-label" style="display:inline-block; width:140px">
                                        <strong class="obligado">*</strong>Valor Crédito:
                                    </label>
                                    <input type="text" name="txtValorCredito" onkeypress="return justNumbers(event);" id="txtValorCredito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="credito();"/>
                                </div>
                                <input type="hidden" name="comprobantecnt" id="comprobantecnt" value="<?php echo $_SESSION['idCompCntV'];?>">
                            
                        </div>
                        
                        <div id="forma-modal" class="modal-footer">
                            <button type="submit" id="guardarCuentaC" onclick="guardarCuentaContable()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                            <button type="button" id="cancelarCuentaC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <script>   
            function debito(){
                var debito = document.getElementById("txtValorDebito").value;
                
                if(debito>0 || debito.length>0 || debito !=''){
                    $("#txtValorCredito").prop('disabled',true);
                    
                } else {
                   $("#txtValorCredito").prop('disabled',false);
                }
            }
            </script>
            <script>
            function credito(){
                var credito = document.getElementById('txtValorCredito').value;
                if(credito>0 || credito.length>0 || credito !=''){
                    $("#txtValorDebito").prop('disabled',true);
                } else {
                     $("#txtValorDebito").prop('disabled',false);
                }
            }
            </script>
            <script>
                function guardarCuentaContable() {
                  var formData = new FormData($("#form")[0]);  
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/registrarCuentaContableCNT.php",
                    data:formData,
                    contentType: false,
                     processData: false,
                    success: function (data) {  
                        console.log(data);
                        if (data==true){
                            $("#modalGuardaCuenta").modal('show');
                            $('#btnGuardarCuenta').click(function(){
                                document.location.reload();
                             });
  
                        } else {
                            $("#modalGuardaCuentaNo").modal('show');
                            $('#btnGuardarCuentaNo').click(function(){
                                document.location.reload();
                             });
                        }
                        }
                    
                });
            }
            </script>
        
            <!--#FIN MODAL AGREGAR#-->
        <div class="modal fade" id="modalGuardaCuenta" role="dialog" align="center" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                   
                    <p>Cuenta Guardada Correctamente</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGuardarCuenta" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalGuardaCuentaNo" role="dialog" align="center" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                   
                    <p>No se ha podido guardar la información</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnGuardarCuentaNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalTotales" role="dialog" align="center" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                   
                    <p>Totales no coinciden.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnmodalTotales" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    
                </div>
            </div>
        </div>
    </div>    
        
        <?php } ?>
        <!--#####FIN AGREGAR CUENTA CONTABLE######--->
      </div>
              </div>
        <script type="text/javascript">

          $(document).ready(function()
          {
            $("#btnModificar").click(function()
            {
              var tercero = $("#tercero").val();
              var descripcion = $("#descripcion").val();
              var claseContrato = $("#claseContrato").val();
              var numeroContrato = $("#numeroContrato").val();
              var fecha = $("#fecha").val();
              var form_data = { estruc: 13, descripcion: descripcion, tercero: tercero, claseContrato:claseContrato,
              numeroContrato:numeroContrato, fecha:fecha};
              $.ajax({
                type: "POST",
                url: "estructura_aplicar_retenciones.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                  if(response == 1)
                  {
                    $("#mdlModExit").modal('show');
                  }
                  else
                  {
                    $("#mdlModError").modal('show');
                  }
                }// Fin success.
              });// Fin Ajax;
            });
          });

        </script>


    <div class="modal fade" id="myModalFirma" role="dialog" align="center" >
        <div class="modal-dialog" >
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Firma Dactilar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                   
                    <img src="images/lectorhuella2.png" style="width: 500px; height: 300px"/><br/>
                    <a href="LISTAR_TERCERO_EMPLEADO_NATURAL2.php">Registrar Huella</a>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar</button>
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
<script>
    function firma(){
        
        $("#myModalFirma").modal('show');
    }
</script>

    <script type="text/javascript">
      
      $(document).ready(function()
      {
        $("#btnEnviar").click(function()
        {
            var diferencia = document.getElementById('diferencia').value;
          if(diferencia !=0){
              $("#modalTotales").modal('show');
              
         
            } 
          
          else {
          var idComP = $("#id_Comp_Ptal").val();
          var form_data = { estruc: 2, sesion: 'id_comp_pptal_GE', id_comp: idComP, nuevo: 'nuevo_GE' };
          $.ajax({
            type: "POST",
            url: "estructura_sesiones.php",
            data: form_data,
            success: function(response)
            {
              document.location = 'GENERAR_EGRESO.php'; // Dejar siempre.
              //window.open('GENERAR_EGRESO.php'); //Para probar.
            }// Fin success.
          });// Fin Ajax;
            } 
        });
      });
    </script>

    <!-- select2 -->
  <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() 
    {
      llenar();
    });
  </script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>

  <script>
  function llenar()
  {
      var tercero = document.getElementById('tercero').value;
      document.getElementById('terceroB').value= tercero;
  }
  </script>

  
                <div class=" contTabla col-sm-8" style="margin-top: 2px">
                    <div class="table-responsive contTabla" >
                        <?php 
                           // if (!empty($_SESSION['idNumeroC'])) 
                          //  {
                                
                            //}
                    
                    ?>
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">
                    <?php 
                    $sumar = 0;
                    $sumaT = 0;
                    ?>
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">                            
                        <thead>
                            <tr>
                        <style>
                            .cabeza{
                                width:auto;
                            }
                        </style>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Cuenta Contable</strong></td>
                                <td class="cabeza"><strong>Tercero</strong></td>
                                <td class="cabeza"><strong>Centro Costo</strong></td>
                                <td class="cabeza"><strong>Proyecto</strong></td>
                                <td class="cabeza"><strong>Débito</strong></td>
                                <td class="cabeza"><strong>Crédito</strong></td>                                
                                <td class="cabeza"><strong>Movimiento Cuenta</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%"></th>
                                <th class="cabeza">Cuenta Contable</th>
                                <th class="cabeza">Tercero</th>
                                <th class="cabeza">Centro Costo</th>
                                <th class="cabeza">Proyecto</th>
                                <th class="cabeza">Débito</th>
                                <th class="cabeza">Crédito</th>
                                <th class="cabeza">Movimiento Cuenta</th>
                            </tr>
                        </thead>
                        <tbody>  
                            <?php 
                            if(!empty($_SESSION['idCompCntV']))
                            {

                                $sql="  
                                SELECT
                                   DT.id_unico,
                                   CT.id_unico as cuenta,
                                   CT.nombre,
                                   CT.codi_cuenta,
                                   CT.naturaleza,
                                   N.id_unico,
                                   N.nombre,
                                   IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
                                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                                ter.id_unico, 
                                                CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',                                   
                                   CC.id_unico,
                                   CC.nombre,
                                   PR.id_unico,
                                   PR.nombre,
                                   DT.valor, 
                                   dcpttal.rubrofuente, 
                                   dcpttal.comprobantepptal, 
                                   DT.detallecomprobantepptal 
                                FROM
                                  gf_detalle_comprobante DT
                                LEFT JOIN
                                  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                LEFT JOIN
                                  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                LEFT JOIN
                                  gf_tercero ter ON DT.tercero = ter.id_unico
                                LEFT JOIN
                                  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                                LEFT JOIN
                                  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                LEFT JOIN
                                  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                LEFT JOIN 
                                  gf_detalle_comprobante_pptal dcpttal ON DT.detallecomprobantepptal = dcpttal.id_unico
                                WHERE (DT.comprobante) = $idComprobante";
                                $rs = $mysqli->query($sql);

                              while ($row = mysqli_fetch_row($rs)) 
                              { 
                                  
                            ?>
                            <tr>
                                <td class="campos oculto">
                                    <?php echo $row[0]; ?>
                                </td>
                                <td class="campos">
                                    <?php if(empty($row[17])) {?>
                                    <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <?php } ?>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);return select(<?php echo $row[0]; ?>)">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a>                                            
                                </td>
                                <!-- Código de cuenta y nombre de la cuenta -->
                                <td class="campos text-left" >
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(mb_strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                    <select style="display: none;padding:2px" class="col-sm-12 campoD" id="sltC<?php echo $row[0]; ?>">
                                        <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                            <?php 
                                            $sqlCTN = "SELECT DISTINCT id_unico,codi_cuenta,nombre FROM gf_cuenta WHERE (codi_cuenta != $row[3]) AND movimiento = 1
                                        OR      centrocosto = 1
                                        OR      auxiliartercero = 1
                                        OR      auxiliarproyecto = 1";
                                            $result = $mysqli->query($sqlCTN);
                                            while ($s = mysqli_fetch_row($result)){
                                                echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                            }
                                            ?>                                                
                                    </select>
                                </td>
                                <!-- Datos de tercero -->
                                <td class="campos text-left">
                                    
                                    <?php echo '<label class="valorLabel" title="'.$row[9].'" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[7]))).'</label>'; ?>
                                    <div id="sltTercero<?php echo $row[0]; ?>" style="display:none">
                                    <select id="stTercero<?php echo $row[0]; ?>" style="display: none; padding: 2px;height:18; width: 150px" class="col-sm-12 campoD" >
                                        <option value="<?php echo $row[8] ?>"><?php echo  (ucwords(mb_strtolower($row[7]))) ?></option>
                                        <?php
                                        $sqlTR = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
                                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',  
                                                ter.id_unico, CONCAT(ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                                                WHERE  ter.id_unico != $row[8] ORDER BY NOMBRE ASC";
                                        $resulta = $mysqli->query($sqlTR);
                                        while($e = mysqli_fetch_row($resulta)){  
                                            echo '<option value="'.$e[1].'">'.ucwords(mb_strtolower($e[0].' - '.$e[2])).'</option>';                                                  
                                        }
                                        ?>
                                    </select>
                                    </div>
                                    <script>
                                function select(id){
                                    var flujo = 'stTercero'+id;
                                    $(".select2_single, #"+flujo).select2();
                                }
                            </script>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(mb_strtolower($row[11]))).'</label>'; ?>
                                    <select id="sltcentroC<?php echo $row[0]; ?>" style="display: none;padding:2px;height:19px" class="col-sm-12 campoD">
                                        <option value="<?php echo $row[10]; ?>"><?php echo ucwords(mb_strtolower($row[11])); ?></option>
                                        <?php
                                        $sqlCCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[10]'";
                                        $g = $mysqli->query($sqlCCT);
                                        while($f = mysqli_fetch_row($g)){
                                            echo '<option value="'.$f[0].'">'.ucwords(mb_strtolower($f[1])).'</option>';
                                        }
                                        ?> 
                                    </select>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(mb_strtolower($row[13]))).'</label>'; ?>
                                    <select style="display: none;padding:2px;height:19px" class="col-sm-12 campoD" id="sltProyecto<?php echo $row[0]; ?>">
                                        <option value="<?php echo $row[12]; ?>"><?php echo ucwords(mb_strtolower($row[13])); ?></option>
                                        <?php 
                                        $sqlCP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[12]";
                                        $result = $mysqli->query($sqlCP);
                                        while ($y = mysqli_fetch_row($result)){
                                            echo '<option value="'.$y[0].'">'.ucwords(mb_strtolower($y[1])).'</option>';
                                        }
                                        ?>
                                        <!-- Validación de campos en la tabla -->                                                                                                                                              
                                    </select>
                                </td>
                                <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                <td class="campos text-right" align="center">

                                    <?php 

                                    if ($row[4] == 1) {
                                        if($row[14] >= 0){
                                            $sumar += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" onkeyup="valorModdebito('.$row[0].');" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$row[14].'" />';
                                        }else{
                                            echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" onkeyup="valorModdebito('.$row[0].');" align="center" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }  
                                    }else if($row[4] == 2){
                                        if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumar += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" onkeyup="valorModdebito('.$row[0].');" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$x.'" />';
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" onkeyup="valorModdebito('.$row[0].');" type="text" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }
                                    }

                                   ?>                                            
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    if ($row[4] == 2) {
                                        if($row[14] >= 0){
                                            $sumaT += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" onkeyup="valorModcredito('.$row[0].');" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$row[14].'" />';                                                                                                
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" onkeyup="valorModcredito('.$row[0].');" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  name="txtCredito'.$row[0].'"  id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }else if($row[4] == 1){
                                       if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumaT += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" onkeyup="valorModcredito('.$row[0].');" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$x.'" />';                                                                                                
                                    }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" onkeyup="valorModcredito('.$row[0].');" class="col-sm-12 text-left campoD" style="display:none;padding:2px;height:19px" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }?>                                    
                                </td>
                                <td class="campos">
                                    <!-- <a href="javascript:void(0)" onclick="abrirdetalleMov()" data-toggle="modal" class="col-sm-6"><li class="glyphicon glyphicon-eye-open"></li></a>
                                    <div > -->
                                    <input type="hidden" name="detpptal<?php echo $row[0]?>" id="detpptal<?php echo $row[0]?>" value="<?php echo $row[16]?>" >
                                    <input type="hidden" name="rubrofuen<?php echo $row[0]?>" id="rubrofuen<?php echo $row[0]?>" value="<?php echo $row[15]?>" >
                                        <div id="detallemov<?php echo $row[0] ?>" style="display:inline">
                                            <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $row[0]?>, <?php echo $row[14]?>);" title="Comprobante detalle movimiento"><i class="glyphicon glyphicon-file"></i></a>                                        
                                        </div>


                                        <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent; display: none">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>"  onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>"  onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                    
                                </td>
                            </tr>
                          <?php 
                            }  
                           
                            
                           
                          }
                          ?>
                        </tbody>
                    </table>
                    </div>
                    <!--### MODIFICAR CUENTA CNT ###-->
                    <script>
                        function modificar(id){
                            var egr = $("#eg").val();
                            if(egr>0){
                                $("#myModalYaEg").modal('show');
                                $("#verYaEg").click(function () {
                                   $("#myModalYaEg").modal('hide');
                                   modificar1(id);
                                });
                                $("#verNoYaEg").click(function () {
                                   $("#myModalYaEg").modal('hide');
                                });
                            } else {
                                modificar1(id);
                            }
                        }
                    </script>
                    <div class="modal fade" id="myModalYaEg" role="dialog" align="center" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                    <p>Comprobante Ya Tiene Egreso, ¿Desea Modificarlo?</p>
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="verYaEg" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                                    <button type="button" id="verNoYaEg" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function modificar1(id){
                            
                    if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != ""))
                        {
                          
                          var debito = 'txtDebito'+$("#idPrevio").val();
                          var debitolbl = 'debitoP'+$("#idPrevio").val();
                          
                          var creditoP = 'txtCredito'+$("#idPrevio").val();
                          var creditoPlbl = 'creditoP'+$("#idPrevio").val();
                          
                          var tercerolbl ='tercero'+$("#idPrevio").val();
                          var terceroS ='sltTercero'+$("#idPrevio").val();
                          
                          var tab = 'tab'+$("#idPrevio").val();
                          var detallemov = 'detallemov'+$("#idPrevio").val();
                          
                            

                            $("#"+debito).css("display", "none");
                            $("#"+debitolbl).css("display", "block");
                            
                            $("#"+terceroS).css("display", "none");
                            $("#"+tercerolbl).css("display", "block");
                            
                            $("#"+creditoP).css("display", "none");
                            $("#"+creditoPlbl).css("display", "block");
                            
                            $("#"+tab).css("display", "none");
                            $("#"+detallemov).css("display", "block");
                          
                        } 
       
                       
                        var debito = 'txtDebito'+id;
                        var debitolbl = 'debitoP'+id;
                        
                        var creditoP = 'txtCredito'+id;
                        var creditoPlbl = 'creditoP'+id;
                        
                        var tercerolbl ='tercero'+id;
                        var terceroS ='sltTercero'+id;
                          
                        var tab = 'tab'+id;
                        var detallemov = 'detallemov'+id;
                        
                        
                      
                        $("#"+debito).css("display", "block");
                        $("#"+debitolbl).css("display", "none");
                        
                        $("#"+creditoP).css("display", "block");
                        $("#"+creditoPlbl).css("display", "none");
                        
                        $("#"+terceroS).css("display", "block");
                        $("#"+tercerolbl).css("display", "none");
                        
                        $("#"+tab).css("display", "block");
                        $("#"+detallemov).css("display", "none");
                       
                        var deb= document.getElementById('txtDebito'+id).value;
                        var cred= document.getElementById('txtCredito'+id).value;
                        
                       if(deb!=0){
                           document.getElementById('txtCredito'+id).disabled=true; 
                       } 
                       
                       if(cred!=0){
                           $("#"+debito).prop('disabled',true);
                       }
                       
                        $("#idActual").val(id);
                    
                        if($("#idPrevio").val() != id)
                          $("#idPrevio").val(id);
                      

                    }
                    </script>
                    <script>
                        function cancelar(id){
                            var cuenta = 'sltC'+id;
                        var cuentalbl = 'cuenta'+id;
                        
                        var tercero = 'sltTercero'+id;
                        var tercerolbl = 'tercero'+id;
                        
                        var centroC = 'sltcentroC'+id;
                        var centroClbl = 'centroC'+id;

                        var proyecto = 'sltProyecto'+id;
                        var proyectolbl = 'proyecto'+id;
                        
                        var debito = 'txtDebito'+id;
                        var debitolbl = 'debitoP'+id;
                        
                        var creditoP = 'txtCredito'+id;
                        var creditoPlbl = 'creditoP'+id;
                        
                        var tab = 'tab'+id;
                        var detallemov = 'detallemov'+id;
                        
                        
                        $("#"+cuenta).css("display", "none");
                        $("#"+cuentalbl).css("display", "block");

                        $("#"+tercero).css("display", "none");
                        $("#"+tercerolbl).css("display", "block");

                        $("#"+centroC).css("display", "none");
                        $("#"+centroClbl).css("display", "block");
                        
                        $("#"+proyecto).css("display", "none");
                        $("#"+proyectolbl).css("display", "block");
                        
                        $("#"+debito).css("display", "none");
                        $("#"+debitolbl).css("display", "block");
                        
                        $("#"+creditoP).css("display", "none");
                        $("#"+creditoPlbl).css("display", "block");
                        
                        $("#"+tab).css("display", "none");
                        $("#"+detallemov).css("display", "block");
                        }
                    </script>
                    <script>   
                        function valorModdebito(id){
                            var debito = $("#txtDebito" + id).val();
                            var cred ='txtCredito'+id;
                            
                            if(debito>0 || debito.length>0 || debito !=''){
                                $("#"+cred).prop('disabled',true);
                                
                            } else {
                               $("#"+cred).prop('disabled',false);
                            }
                        }
                    </script>
                    <script>
                        function valorModcredito(id){
                            var cred = $("#txtCredito" + id).val();
                            var debito ='txtDebito'+id;
                            
                            if(cred>0 || cred.length>0 || cred !=''){
                                $("#"+debito).prop('disabled',true);
                                
                            } else {
                               $("#"+debito).prop('disabled',false);
                            }
                        }
                    </script>
                    <script>
                        function guardarCambios(id){
                            var valorD = document.getElementById('txtDebito'+id).value;
                            var valorC = document.getElementById('txtCredito'+id).value;
                            var terceroM =  document.getElementById('stTercero'+id).value;
                            if (valorD=='' || valorD==0){
                                if(valorC =='' || valorC==0){
                                
                                $("#myModalIngresarV").modal('show');
                                $("#verIngresarV").click(function () {
                                    $("#myModalIngresarV").modal('hide');
                                }); 
                                } else {
                                    guardarg(id);
                                }
                            } else {
                                guardarg(id);
                            }
                        }
                            </script>
                            <script>
                               function guardarg(id){
                                 
                                var valorD = document.getElementById('txtDebito'+id).value;
                                var valorC = document.getElementById('txtCredito'+id).value;
                                var terceroM =  document.getElementById('stTercero'+id).value;
                                if (valorD=='' || valorD==0 ){
                                    
                                    var validar = document.getElementById('txtCredito'+id).value;
                                    
                                } else {
                                   var validar = document.getElementById('txtDebito'+id).value;
                                }
                                
                                var id_rubFue=document.getElementById('rubrofuen'+id).value ;
                                var id_det_comp =document.getElementById('detpptal'+id).value ;
                                
                                var form_data = { proc: 4, id_rubFue: id_rubFue, id_comp: id_det_comp , clase: 15};
                                    $.ajax({
                                      type: "POST",
                                      url: "estructura_comprobante_pptal.php",
                                      data: form_data,
                                      success: function(response)
                                      {  //GUARDAR VALOR   
                                        var form_data = { iddetalle: id, valorD: valorD, valorC: valorC, tercero: terceroM };
                                        $.ajax({
                                          type: "POST",
                                          url: "consultasBasicas/modificar_comprobantecnt_valor.php",
                                          data: form_data,
                                          success: function(response)
                                          {     
                                              console.log(response);
                                              if(response==1){
                                                $("#mdlModExit").modal('show');
                                                $("#btnModExit").click(function () {
                                                   document.location.reload();
                                                });
                                                     
                                              }else {
                                                    $("#mdlModError").modal('show');
                                                    $("#btnModError").click(function () {
                                                       document.location.reload();
                                                    });
                                              }
                                          } //Fin success.
                                        });
                                      } //Fin success.
                                    });
                                }
                            
                        
                    </script>
                    <!--######FIN MODIFICAR DETALLE CNT#####-->
                    <div class="modal fade" id="myModalIngresarV" role="dialog" align="center" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                    <p>Ingrese un valor</p>
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="verIngresarV" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                                    <button type="button" id="verIngresarV" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--######ELIMINAR DETALLE CNT#####-->
                    <script>
                        function eliminar(id){
                            $("#myModalEliminarD").modal('show');
                            $("#verEliminarD").click(function () {
                                $("#myModalEliminarD").modal('hide');
                                $.ajax({
                                    type: "GET",
                                    url: "json/eliminar_gf_CuentaConJson.php?id=" + id,
                                    success: function (data) {
                                        result = JSON.parse(data);
                                            if (result == true) {
                                                $("#myModal1D").modal('show');
                                                $("#ver1D").click(function () {
                                                   document.location.reload();
                                                });
                                            } else { 
                                                $("#myModal2D").modal('show');
                                                $("#ver2DW").click(function () {
                                                    document.location.reload();
                                                });
                                            }
                                    }
                                });
                            });  
                            $("#verEliminarCancelarD").click(function () {
                                $("#myModalEliminarD").modal('hide');

                            })
                        }
                    </script>
                    <div class="modal fade" id="myModalEliminarD" role="dialog" align="center" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                    <p>¿Desea eliminar el registro seleccionado de Cuenta Contable?</p>
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="verEliminarD" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                                    <button type="button" id="verEliminarCancelarD" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="myModal1D" role="dialog" align="center" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                    <p>Información eliminada correctamente.</p>
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="ver1D" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="myModal2D" role="dialog" align="center" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                                </div>
                                <div id="forma-modal" class="modal-footer">
                                    <button type="button" id="ver2D" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--######FIN ELIMINAR DETALLE CNT#####-->
                    <?php 
                    $valorD = $sumar;
                    $valorC = $sumaT;
                    #Diferencia
                    $diferencia = $valorD - $valorC;
                    ?>
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
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if (($valorD) === NULL) { ?>
                                 <label class="control-label valores" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label valores" title="Suma débito"><?php echo number_format($valorD, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                            <?php 
                            if ($valorC === NULL) { ?>
                                <label class="control-label valores" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label valores" title="Suma crédito"><?php echo number_format($valorC, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if ($diferencia === 0) { ?>
                                  <label class="control-label text-right valores" title="Diferencia">0.00</label>                          
                            <?php }else{ ?>
                                  <label class="control-label text-right valores" title="Diferencia"><?php echo number_format($diferencia, 2, '.', ',') ; ?></label>
                                 
                            <?php    
                            }
                            
                            ?>                                  
                                   <input type="hidden" id="diferencia" value="<?php echo $diferencia;?>">
                        </div> 
                    </div>                                       
                </div>

           
            </div>
        </div>


<script type="text/javascript"> //Guarda comprobante
/*  $(document).ready(function()
  {
    $("#btnGuardar").click(function()
    {
      var idComprobante = $("#idComprobante").val();
      var diferencia = $("#diferencia").val();
      var cuentaBancaria = $("#cuentaBancaria").val();

      if(cuentaBancaria != 0 && cuentaBancaria != "")
      {
    var form_data = { estruc: 9, idComprobante: idComprobante, diferencia: diferencia, cuentaBancaria: cuentaBancaria}; 
        $.ajax({
          type: "POST",
          url: "estructura_aplicar_retenciones.php",
          data: form_data,
          success: function(response)
          {
            if(response == 1)
            {
              $("#mdlGuardado").modal('show');
            }
            else
            {
              $("#mdlNoGuardado").modal('show');
            }
            //document.location = 'algo.php'; 
          }//Fin succes.
        }); //Fin ajax. 
      }
      else
      {
        $("#mdlNoCuentBan").modal('show');
      }
    
    });
  }); */
  </script>

        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un tipo de comprobante.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
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
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="mdlNoCuentBan" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No hay una cuenta bancaria seleccionada.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoCuentBan" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales de guardado -->
        <div class="modal fade" id="mdlGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="mdlNoGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardado2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>     

           <div class="modal fade" id="mdlModExit" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnModExit" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>     

           <div class="modal fade" id="mdlModError" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>No se ha podido modificar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnModError" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>     

        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>


        <script type="text/javascript">
  
  $('#btnGuardado').click(function(){
     document.location.reload();
  });

</script>
        
  <script type="text/javascript" >
    function abrirdetalleMov(id, valor){   
        var form_data={                            
        id:id, 
        valor: valor
        };
        $.ajax({
            type: 'POST',
            url: "registrar_GF_DETALLE_EGRESO.php#mdlDetalleMovimiento",
            data:form_data,
            success: function (data) { 
                $("#mdlDetalleMovimiento").html(data);
                $(".mov").modal('show');
            }
        });

    }                                                                                        
</script>      

 <script type="text/javascript">
  
  $('#btnModExit').click(function(){
     document.location.reload();
  });

</script>           
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

  
    <?php require_once './footer.php'; ?>
    </body>        
    <?php require_once './registrar_GF_DETALLE_EGRESO.php'; ?>
</html>
