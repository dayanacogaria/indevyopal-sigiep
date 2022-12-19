<?php 
require_once ('Conexion/conexion.php');
require_once ('head_listar.php');
require_once('./jsonPptal/funcionesPptal.php');
$anno = $_SESSION['anno'];
$num_anno   = anno($_SESSION['anno']);
if(!empty($_SESSION['idCompCntV']))
{
    $idComprobante = $_SESSION['idCompCntV'];
    $_SESSION['nuevo_GE']=1;
    $_SESSION['idCompCnt']=$idComprobante;
    $_SESSION['cntcxp'] =$idComprobante;
    $queryTipoComp = "SELECT  tipCom.nombre , DATE_FORMAT(comCnt.fecha,'%d-%m-%Y'), tipCom.id_unico 
    FROM gf_tipo_comprobante tipCom 
    LEFT JOIN gf_comprobante_cnt comCnt ON tipCom.id_unico = comCnt.tipocomprobante
    WHERE comCnt.id_unico = $idComprobante";
    $tipoCompro = $mysqli->query($queryTipoComp);
    $rowTC = mysqli_fetch_row($tipoCompro);
    $fecha =$rowTC[1];
}
$annio = $_SESSION['anno'];
$arr_sesiones_presupuesto = array('id_compr_pptal', 'id_comprobante_pptal', 'id_comp_pptal_ED', 'id_comp_pptal_ER', 'id_comp_pptal_CP', 'idCompPtalCP', 'idCompCntV', 'id_comp_pptal_GE', 'cntEgreso');
?>     
<title>Comprobante Egreso
</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js">
</script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<style>
  /*Estilos tabla*/
  table.dataTable thead th,table.dataTable thead td{
    padding:1px 18px;
    font-size:10px}
  table.dataTable tbody td,table.dataTable tbody td{
    padding:1px}
  .dataTables_wrapper .ui-toolbar{
    padding:2px}
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
  .select2-choice {
    min-height: 26px;
    max-height: 26px;
  }
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
  /*Esto permite que el texto contenido dentro del div
  no se salga de las medidas del mismo.*/
  .acotado
  {
    white-space: normal;
  }
</style>  
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js">
</script> 
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
    <?php if(empty($fecha)|| $fecha=='') {
      ?>
        $("#fecha").datepicker({ changeMonth: true}).val();
      <?php }
    else {
      ?>
        var fechaI = '<?php echo date("d/m/Y", strtotime($fecha));?>';
      $("#fecha").datepicker({changeMonth: true}).val(fechaI);
      <?php }
    ?>
      $("#fechaAct").val(fecAct);
  });
</script>

</head>
<script type="text/javascript">

  $(document).ready(function()
  {
    //Función que ejecuta consulta para verificar si el comprobante
    var id= $("#id").val();

    var form_data = { case:21, id: id};
    $.ajax({
      type: "POST",
      url: "consultasBasicas/busquedas.php",
      data: form_data,
      success: function(response)
      {
          console.log(response+'balance');
          if(response==1){
              
              
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

    <body onMouseMove="coordenadas(event);">    
        <input type="hidden" id="balanceo" >
        <?php if(!empty($_SESSION['idCompCntV'])) { ?>
        
        <input type="hidden" id="id" value="<?php echo $_SESSION['idCompCntV']?>">
        <input type="hidden" id="idpptal" value="<?php echo $_SESSION['id_comp_pptal_GE']?>">
            <?php } ?>       
  <input type="hidden" id="idComprobante" value="<?php echo $idComprobante;?>">
  <div class="container-fluid text-left">
    <div class="row content">
      <?php require_once('menu.php'); ?>
      <!-- 1 -->      
      <div class="col-sm-10 text-center" style="margin-top:-22px;">
        <?php 
        if(!empty($_SESSION['idCompCntV']))
        {
            $queryNumero = "SELECT numero , tercero  
            FROM gf_comprobante_cnt  
            WHERE id_unico = $idComprobante";
            $numeroCnt = $mysqli->query($queryNumero);
            $rowNC = mysqli_fetch_row($numeroCnt);
        }
        ?>
        <h2 class="tituloform" align="center" >Comprobante 
          <?php if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowTC[0]));} ?>
        </h2>
        <a href="GENERAR_EGRESO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver">
        </a>
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Egreso: 
          <?php echo $rowNC[0]?>
        </h5>
        <div class="client-form contenedorForma col-sm-12" style="margin-top:-7px;">
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" style="margin-bottom:-20px">
            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
              Los campos marcados con 
              <strong class="obligado">*
              </strong> son obligatorios.
            </p>
            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> 
              <!-- Primera Fila -->
              <div class="col-sm-3" align="left"> 
                <!-- Cuenta Bancaria -->
                <label for="cuentaBancaria" class="control-label" style="">
                  <strong style="color:#03C1FB;">*
                  </strong>Cuenta Bancaria:
                </label>
                <br>
                <select name="cuentaBancaria" id="cuentaBancaria" class="select2_single " title="Cuenta Bancaria" style="width:180px;" required>
                  <?php 
                    $compania = $_SESSION['compania'];
                    $queryCuenBan = "SELECT  ctb.id_unico,
                        CONCAT(CONCAT_WS(' - ',ctb.numerocuenta,ctb.descripcion),' (',c.codi_cuenta,' - ',c.nombre, ')'),
                        c.id_unico 
                    FROM gf_cuenta_bancaria ctb
                    LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria 
                    LEFT JOIN gf_cuenta c ON ctb.cuenta = c.id_unico 
                    WHERE ctbt.tercero ='".$compania."' AND ctb.parametrizacionanno = $anno AND c.id_unico IS NOT NULL ORDER BY ctb.numerocuenta";
                    $cuentaBanc = $mysqli->query($queryCuenBan);
                    if(($cuentaBanc->num_rows) == 0)
                    {
                        echo '<option value>No hay cuentas bancarias.</option>';
                    }
                    else
                    {
                        echo '<option value="">Cuenta Bancaria</option>';
                        while($rowCB = mysqli_fetch_row($cuentaBanc))
                        {
                            $sum = "SELECT SUM(valor) FROM gf_detalle_comprobante dc "
                                    . "LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico "
                                    . "WHERE dc.cuenta = $rowCB[2] AND cn.parametrizacionanno = $annio";
                            $sum = $mysqli->query($sum);
                            if(mysqli_num_rows($sum)>0) { 
                            $val= mysqli_fetch_row($sum);
                            if($val[0]==NULL){$val=0;}else{
                            $val = $val[0];}
                            } else {
                            $val = 0;
                            }
                            echo '<option value="'.$rowCB[0].'">'.ucwords(mb_strtolower($rowCB[1])).' Saldo: $'.number_format($val,2,'.',',').'</option>';
                        }
                    }
                    ?>
                </select>  
                <script>
                       $("#cuentaBancaria").change(function(){
                           var cuenta = $("#cuentaBancaria").val();
                           var form_data={ estruc:8, cuenta:cuenta};
                           $.ajax({
                              type:"POST",
                              url :"jsonPptal/consultas.php",
                              data:form_data,
                              success:function(response){
                                  response = parseInt(response);
                                  if(response<=0){
                                      $("#sinsaldo").modal("show");
                                  }
                              }
                           }); 
                       });
                </script>
              </div>
              <!-- Fin Solicitud aprobada -->
              <div class="col-sm-3" align="left"> 
                <!-- Forma Pago -->
                <label for="formaPago" class="control-label" style="">
                  <strong style="color:#03C1FB;">*</strong>Forma Pago:
                </label>
                <br>
                <select name="formaPago" id="formaPago" class="form-control input-sm" title="Forma de pago" style="width:180px;" required>
                  
                  <?php 
                  if(!empty($_SESSION['idCompCntV'])){
                      $fmp = "SELECT fp.id_unico, fp.nombre 
                        FROM gf_comprobante_cnt cn 
                        LEFT JOIN gf_forma_pago fp ON fp.id_unico = cn.formapago
                        WHERE cn.id_unico =".$_SESSION['idCompCntV'];
                      $fmp = $mysqli->query($fmp);
                      $fmp = mysqli_fetch_row($fmp);
                      IF(!empty($fmp[0])){
                        echo '<option value="'.$fmp[0].'" >'.$fmp[1].'</option>';
                        $queryFormPag = "SELECT id_unico, nombre FROM gf_forma_pago WHERE id_unico != $fmp[0] ORDER BY id_unico";
                      } else {
                        echo '<option value="" >Forma Pago</option>';
                        $queryFormPag = "SELECT id_unico, nombre FROM gf_forma_pago ORDER BY id_unico";
                      }
                  } else {
                    $queryFormPag = "SELECT id_unico, nombre FROM gf_forma_pago ORDER BY id_unico";
                  }
                  $formaPago = $mysqli->query($queryFormPag);
                    while($rowFP = mysqli_fetch_row($formaPago))
                    {echo '<option value="'.$rowFP[0].'" > '.$rowFP[1].' </option>';} 
                    ?>
                </select>  
              </div>
              <!-- Fin Forma Pago -->
              <div class="col-sm-3" align="left"> 
                <!-- Tipo Comprobante -->
                <label for="tipoComprobante" class="control-label" style="">
                  <strong style="color:#03C1FB;">*
                  </strong>Tipo Comprobante:
                </label>
                <br>
                <input name="tipoComprobante" id="tipoComprobante" class="form-control input-sm" title="Tipo Comprobante" style="width:180px;" value="<?php if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowTC[0]));} ?>" readonly >
                <input type="hidden" name="idTipo" id="idTipo"  value="<?php if(!empty($_SESSION['idCompCntV'])){ echo (($rowTC[2]));} ?>"  >
              </div>
              <!-- Fin Tipo Comprobante -->
              <div class="col-sm-3" align="left"> 
                <!-- Número -->
                <label for="numeroCnt" class="control-label" style="">
                  <strong style="color:#03C1FB;">*
                  </strong>Número Comprobante:
                </label>
                <br>
                <input name="numeroCnt" id="numeroCnt" class="form-control input-sm" title="Número Comprobante" style="width:180px;" value="<?php if(!empty($_SESSION['idCompCntV'])){ echo ucwords(mb_strtolower($rowNC[0]));}?>" readonly >
              </div>
              <!-- Fin Número -->
            </div> 
            <!-- Fin de la primera fila -->
            <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: 0px; margin-bottom: 0px;"> 
              <!-- Botones --> 
              <div class="col-sm-3" align="left"> 
                <label for="nombre" class="control-label" style="" ><strong style="color:#03C1FB;">*
                  </strong>Tercero:
                </label>
                <br/>
                <select name="tercero" id="tercero" class="select2_single " title="Tercero" style="width:180px;" required>
                    <?php 
                    if(!empty($_SESSION['idCompCntV']))
                    {
                    $queryDesCnt = "SELECT IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                        (tr.razonsocial),
                        CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico  
                    FROM gf_comprobante_cnt cn
                    LEFT JOIN gf_tercero tr ON cn.tercero =tr.id_unico 
                    WHERE cn.id_unico = $idComprobante";
                    $ter = $mysqli->query($queryDesCnt);
                    $rowDesCnt = mysqli_fetch_row($ter);
                    $tercero = ucwords(mb_strtolower($rowDesCnt[0])).' - '.$rowDesCnt[1]; 
                    $idTercer =$rowDesCnt[2];?>
                    <option value="<?php echo $rowDesCnt[2]?>"><?php echo $tercero ?></option>     
                    <?php } else { ?> 
                    <option>Tercero</option>      
                    <?php } ?>  
                    <?php $qtercero = "SELECT IF(CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos) = '',
                        (tr.razonsocial),
                        CONCAT_WS(' ',
                        tr.nombreuno,
                        tr.nombredos,
                        tr.apellidouno,
                        tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico  
                    FROM  gf_tercero tr 
                    WHERE tr.id_unico != '$idTercer' "
                            . "ORDER BY NOMBRE ASC limit 20";
                    $qtercero =$mysqli->query($qtercero);
                    while ($row1 = mysqli_fetch_row($qtercero)) { ?>
                    <option value="<?php echo $row1[2]?>"><?php echo ucwords(mb_strtolower($row1[0])).' - '.$row1[1]; ?></option> 
                    <?php }?>
                
                </select>
                <!-- readonly="readonly" -->
              </div> 
              <div class="col-sm-3" align="left"> 
                <!-- Descripción -->
                <?php 
                if(!empty($_SESSION['idCompCntV']))
                {
                $queryDesCnt = "SELECT descripcion  
                FROM gf_comprobante_cnt 
                WHERE id_unico = $idComprobante";
                $descripcionCnt = $mysqli->query($queryDesCnt);
                $rowDesCnt = mysqli_fetch_row($descripcionCnt);
                $descripcion =$rowDesCnt[0];
                } else {
                    $descripcion ="";
                }
                ?>
                <label for="nombre" class="control-label" style="" >Descripción:
                </label>
                <br/>
                <textarea  style="margin-left: 0px; margin-top: 0px; margin-bottom: 5px; width:250px; height: 50px; width:180px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"   ><?php  echo $descripcion;?>
                </textarea> 
                <!-- readonly="readonly" -->
              </div> 
              <!-- Fin Descripción -->
              <div class="col-sm-3" >
                <label for="nombre" class="control-label" style="margin-left: -190px;" >
                  <strong style="color:#03C1FB;">*
                  </strong>Fecha:
                </label>
                <br/>
                <input class="form-control input-sm" type="text" name="fecha" id="fecha" readonly="true" style="width:180px; " title="Ingrese Número Contrato" placeholder="Fecha">
                <script>
                  $("#fecha").change(function(){
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
                  })
                </script>
                <script>
                        function fecha1(){
                            var comp= $("#idComprobante").val();
                            var fecha = $("#fecha").val();
                            var num = $('#numeroCnt').val();
                            var form_data = {
                              estruc: 17,  comp:comp, fecha:fecha, num:num};
                            $.ajax({
                              type: "POST",
                              url: "consultasBasicas/validarFechas.php",
                              data: form_data,
                              success: function(response)
                              {
                                console.log(response);
                                if(response == 2)
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
                        }
                </script>        
              </div>
              <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
                      </h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>Fecha Inválida. Verifique nuevamente.
                      </p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="sinsaldo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
                      </h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>La cuenta no tiene saldo. </p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                      <button type="button" id="btnsinsaldo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
                   <!--------MODIFICAR RETENCIONES------------->  
            
              <div class="col-sm-1" style="margin-left:-30px">
                <button type="button" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                  <li class="glyphicon glyphicon-floppy-disk">
                  </li>
                </button>
              </div>  
              <div class="col-sm-1" style="margin-left:-30px">
                <button type="button" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Firma Dactilar" onclick="firma();">
                  <img src="images/hb2.png" style="width: 14px; height: 17.28px">
                </button> 
                <!--Firma Dactilar-->
              </div>                       
              <div class="col-sm-1" style="margin-left:-30px">
                <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Imprimir" >
                  <li class="glyphicon glyphicon-print">
                  </li>
                </button>  
              </div>
              <div class="col-sm-1" style="margin-top: 0px; margin-left:-30px">
              <?php 
                #######BUSCAR SI TIENE RETENCIONES###########
                $ret = "SELECT * FROM gf_retencion WHERE comprobante =".$_SESSION['idCompCntV'];
                $ret = $mysqli->query($ret);
                if(mysqli_num_rows($ret)>0) { 
                    $numr = mysqli_num_rows($ret);
                    $cuentas = '0';
                    $anno = $_SESSION['anno'];
                    $bcuentas = "SELECT DISTINCT cuenta FROM gf_tipo_retencion WHERE  parametrizacionanno = $anno ";
                    $bcuentas = $mysqli->query($bcuentas);
                    if(mysqli_num_rows($bcuentas)>0){
                        while($row = mysqli_fetch_row($bcuentas)){
                            if($row[0]!=""){
                            $cuentas .=','.$row[0];
                        }
                       }
                    }

                    $busc = "SELECT * FROM gf_detalle_comprobante WHERE comprobante = ".$_SESSION['idCompCntV']." AND cuenta IN ($cuentas)";
                    $busc = $mysqli->query($busc);
                    #Comparar Que Las Retenciones Guardadas Sean Iguales A Las Retenciones De las Cuentas
                    $numc = mysqli_num_rows($busc);
                    $en = $numc - $numr;
                    if($en<0){$en = $en*-1;}
                    if($numc == $numr){ ?>
                        <button type="button" id="btnRetencion" onclick="open_modal_r()" class="btn btn-primary sombra glyphicon glyphicon-edit" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar Retenciones" ></button>      
                    <?php } elseif($numr > $numc){ ?>
                        <button type="button" id="btnRetencion" onclick="open_modal_r()" class="btn btn-primary sombra glyphicon glyphicon-edit" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar Retenciones" ></button>      
                    <?PHP } else  { ?>
                        <button type="button" id="btnRetencion" onclick="open_modal_r2(<?php echo $en?>, <?php echo $_SESSION['idCompCntV'];?>)" class="btn btn-primary sombra glyphicon glyphicon-ruble" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Ingresar Retenciones" ></button>         
                <?php } 
                }  else { 
                    $cuentas = '0';
                    $anno = $_SESSION['anno'];
                    $bcuentas = "SELECT DISTINCT cuenta FROM gf_tipo_retencion WHERE  parametrizacionanno = $anno ";
                    $bcuentas = $mysqli->query($bcuentas);
                    if(mysqli_num_rows($bcuentas)>0){
                        while($row = mysqli_fetch_row($bcuentas)){
                            if($row[0]!=""){
                            $cuentas .=','.$row[0];
                        }
                       }
                    }

                    $busc = "SELECT * FROM gf_detalle_comprobante WHERE comprobante = ".$_SESSION['idCompCntV']." AND cuenta IN ($cuentas)";
                    $busc = $mysqli->query($busc);
                    if(mysqli_num_rows($busc)>0){ 
                        $en = mysqli_num_rows($busc);?>
                        <button type="button" id="btnRetencion" onclick="open_modal_r2(<?php echo $en?>, <?php echo $_SESSION['idCompCntV'];?>)" class="btn btn-primary sombra glyphicon glyphicon-ruble" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Ingresar Retenciones" ></button>     
                <?php }  
                }?>  
              </div>
              <!--#BOTON AGREGAR#-->
              <br/>
              <br/>
              <br/>
              <div class="col-sm-1" style="margin-top: 5px; margin-left:-30px">
                <button type="button" id="btnModificarCom" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Modificar Tercero" >
                  <li class="glyphicon glyphicon-pencil">
                  </li>
                </button> 
              </div>
              <div class="col-sm-1" style="margin-top: 5px;margin-left:-17px">      
                <button type="button" id="btnAgregarCuentaContable" class="btn btn-primary sombra" 
                        style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" 
                        title="Siguiente" 
                        
                <li class="glyphicon glyphicon-plus">
                </li> Cuenta Contable
                </button> 
            </div>
                <script type="text/javascript">
                  $(document).ready(function(){
                    $("#btnModificarCom").click(function(){
                      var id_com = $("#idpptal").val();
                      var fecha = $("#fecha").val();
                      var cnt = $("#idComprobante").val();
                      var descripcion =$("#descripcion").val();
                      var tercero =$("#tercero").val();
                      var formaP =$("#formaPago").val();
                      if(fecha ==""|| fecha =='00-00-0000'){
                          $("#myModalAlertErrFec").modal('show');
                      } else {
                      var form_data = {
                        estruc: 5,  id_com: id_com, fecha:fecha, cnt:cnt,
                        descripcion:descripcion, tercero:tercero,formaP:formaP  };
                      $.ajax({
                        type: "POST",
                        url: "estructura_modificar_eliminar_pptal.php",
                        data: form_data,
                        success: function(response)
                        {
                            console.log(response);
                          if(response == 1)
                          {
                            $("#mdlModificadoComExito").modal('show');
                            $('#btnModificadoComExito').click(function()
                            {
                              document.location.reload();
                            });
                          }
                          else 
                          {
                            $("mdlModificadoComError").modal('show');
                          }
                          //document.location.reload();                             
                        }
                        //Fin succes.
                      });
                  
                      //Fin ajax.
                    }
                }
                                               );
                  }
                                   );
                </script>
              <script type="text/javascript">
                $(document).ready(function()
                                  {
                  $("#btnImprimir").click(function(){
                    window.open('informesPptal/inf_Comp_Egreso.php');
                  }
                                         );
                }
                                 );
              </script>
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
                    <script type="text/javascript">
                

              </script>
              
            <input type="hidden" value="<?php echo $rowNC[1]?>" name="tercero" id="tercero">
            </div>  
          <!--#####AGREGAR CUENTA CONTABLE######--->
          <?php if(!empty($_SESSION['idCompCntV'])) { ?>
          <!--#FIN BOTON AGREGAR#-->
          <!--#MODAL AGREGAR#-->
          <script>
            $(document).ready(function(){
              $("#btnAgregarCuentaContable").click(function(){
                var ter = document.getElementById('tercero').value;
                document.getElementById('slttercero').value =ter;
                $("#mdlAgregarCuentaC").modal('show');
              }
                                                  );
            }
                             );
          </script>
          <div class="modal fade" id="mdlAgregarCuentaC" role="dialog" align="center" >
            <div class="modal-dialog" >
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Cuenta Contable
                  </h4>
                </div>
                <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data" action="guardarCuenta">
                  <div class="modal-body" style="margin-top: 8px">
                    <div class="form-group" style="margin-top: 5px;">                                    
                      <label class="control-label" style="display:inline-block; width:140px">
                        <strong class="obligado">*
                        </strong>Cuenta: 
                      </label>
                      <select name="sltcuenta" id="sltcuenta" class="select2_single form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione cuenta" required="required">
                        <option value>Cuenta
                        </option>
                        <?php $cuentaA = "SELECT id_unico,
                            codi_cuenta,
                            nombre 
                            FROM    gf_cuenta
                            WHERE   parametrizacionanno = $anno 
                                AND (movimiento = 1
                            OR      centrocosto = 1
                            OR      auxiliartercero = 1
                            OR      auxiliarproyecto = 1) 
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
                        <option value="<?php echo $rowCuentaA[0]?>">
                          <?php echo $rowCuentaA[1].' - '. ucwords(mb_strtolower($rowCuentaA[2]).'- Saldo: $'.number_format($val,2,'.',','))?>
                        </option>  
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
                              }
                              else{
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
                                  }
                                  else if(data==2){
                                    $("#slttercero").prop('disabled',true);
                                  }
                                }
                              }
                                    );
                            }
                                                  );
                          }
                                           );
                        </script>
                        <script type="text/javascript">
                          $(document).ready(function(){
                            var padre = 0;
                            $("#sltcentroc").prop('disabled',true);
                            $("#sltcuenta").change(function(){
                              if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                padre = 0;
                                $("#sltcentroc").prop('disabled',true);
                              }
                              else{
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
                                  }
                                  else if(data==2){
                                    $("#sltcentroc").prop('disabled',true);
                                  }
                                }
                              }
                                    );
                            }
                                                  );
                          }
                                           );
                        </script>
                        <script type="text/javascript">
                          $(document).ready(function(){
                            var padre = 0;
                            $("#sltproyecto").prop('disabled',true);
                            $("#sltcuenta").change(function(){
                              if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                padre = 0;
                                $("#sltproyecto").prop('disabled',true);
                              }
                              else{
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
                                  }
                                  else if(data==2){
                                    $("#sltproyecto").prop('disabled',true);
                                  }
                                }
                              }
                                    );
                            }
                                                  );
                          }
                                           );
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
                        ON ti.id_unico = ter.tipoidentificacion
                        WHERE ter.compania = $compania ORDER BY NOMBRE ASC limit 20";
                        $rs = $mysqli->query($sql);
                        ?>
                      <label class="control-label" style="display:inline-block; width:140px">
                        <strong class="obligado">*
                        </strong>Tercero
                      </label>
                      <select name="slttercero" id="slttercero" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione tercero" required="">
                        <option value="2">Tercero
                        </option>
                        <?php 
                         while($row=  mysqli_fetch_row($rs)){ ?>
                        <option value="<?php echo $row[1]?>">
                          <?php echo ucwords(mb_strtolower($row[0].'('.$row[2].')'));?>
                        </option>
                        <?php }?>
                      </select>
                    </div>
                    <div class="form-group" style="margin-top: 5px;" >
                      <label class="control-label" style="display:inline-block; width:140px">
                        <strong class="obligado">
                        </strong>Centro Costo:
                      </label>
                      <?php 
                        $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE nombre = 'varios'  and parametrizacionanno = $anno ORDER BY nombre ASC";
                        $a = $mysqli->query($sqlCC);
                        $filaC = mysqli_fetch_row($a);
                        $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] and parametrizacionanno = $anno ORDER BY nombre ASC";
                        $r = $mysqli->query($sqlCT);
                        ?>
                      <select name="sltcentroc" id="sltcentroc" class="select2_single form-control" style="display:inline-block; width:250px; margin-bottom:15px; height:40px" title="Seleccione centro costo" required="">
                        <option value="12">Centro Costo
                        </option>
                        <option value="<?php echo $filaC[0]; ?>">
                          <?php echo ucwords( (mb_strtolower($filaC[1]))); ?>
                        </option>
                        <?php 
                        while($fila2=  mysqli_fetch_row($r)){ ?>
                        <option value="<?php echo $fila2[0]; ?>">
                          <?php echo ucwords( (mb_strtolower($fila2[1]))); ?>
                        </option>   
                        <?php                                          
                            }
                            ?>
                      </select>
                    </div>
                    <div class="form-group" style="margin-top: 5px;" >
                      <label class="control-label" style="display:inline-block; width:140px">
                        <strong class="obligado">
                        </strong>Proyecto:
                      </label>
                      <select name="sltproyecto" id="sltproyecto" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px"  title="Seleccione proyecto" >
                        <?php 
                        $sqlP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'" ;
                        $d = $mysqli->query($sqlP);                                    
                        $filaP = mysqli_fetch_row($d);
                        $sqlPY = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $filaP[0]" ;
                        $X = $mysqli->query($sqlPY);
                        ?>
                        <option value="<?php echo $filaP[0]; ?>">Proyecto
                        </option>
                        <option value="<?php echo $filaP[0]; ?>">
                          <?php echo ucwords( (mb_strtolower($filaP[1]))) ?>
                        </option>
                        <?php                                         
                        while($fila3 = mysqli_fetch_row($X)){ ?>
                        <option value="<?php echo $fila3[0]; ?>">
                          <?php echo ucwords( (mb_strtolower($fila3[1]))) ?>
                        </option>
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
                        <strong class="obligado">*
                        </strong>Valor Débito:
                      </label>
                      <input type="text" name="txtValorDebito" onkeypress="return justNumbers(event);" id="txtValorDebito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="debito();"/>
                    </div>
                    <div class="form-group" style="margin-top:5px;">
                      <label class="control-label" style="display:inline-block; width:140px">
                        <strong class="obligado">*
                        </strong>Valor Crédito:
                      </label>
                      <input type="text" name="txtValorCredito" onkeypress="return justNumbers(event);" id="txtValorCredito" minlength="1" maxlength="50" class="form-control"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeyup="credito();"/>
                    </div>
                    <input type="hidden" name="comprobantecnt" id="comprobantecnt" value="<?php echo $_SESSION['idCompCntV'];?>">
                  </div>
                  <div id="forma-modal" class="modal-footer">
                    <button type="submit" id="guardarCuentaC" onclick="guardarCuentaContable()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar
                    </button>
                    <button type="button" id="cancelarCuentaC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar
                    </button>
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
              }
              else {
                $("#txtValorCredito").prop('disabled',false);
              }
            }
          </script>
          <script>
            function credito(){
              var credito = document.getElementById('txtValorCredito').value;
              if(credito>0 || credito.length>0 || credito !=''){
                $("#txtValorDebito").prop('disabled',true);
              }
              else {
                $("#txtValorDebito").prop('disabled',false);
              }
            }
          </script>
          <script>
            function guardarCuentaContable() {
              var comprobantecnt =$('#idComprobante').val();
              var sltcuenta = $('#sltcuenta').val();
              var txtValorCredito =$('#txtValorCredito').val();
              var txtValorDebito =$('#txtValorDebito').val();
              var slttercero =$('#slttercero').val();
              var sltproyecto =$('#sltproyecto').val();
              var sltcentroc =$('#sltcentroc').val();
              var formData = {
                comprobantecnt:comprobantecnt,sltcuenta:sltcuenta,
                txtValorCredito:txtValorCredito,txtValorDebito:txtValorDebito, slttercero:slttercero,
                sltproyecto:sltproyecto,sltcentroc:sltcentroc}
              $.ajax({
                type: 'POST',
                url: "consultasBasicas/registrarCuentaContableCNT.php",
                data:formData,  
                success: function (data) {
                  console.log(data);
                  if (data==true){
                    $("#modalGuardaCuenta").modal('show');
                    $('#btnGuardarCuenta').click(function(){
                      document.location.reload();
                    }
                                                );
                  }
                  else {
                    $("#modalGuardaCuentaNo").modal('show');
                    $('#btnGuardarCuentaNo').click(function(){
                      document.location.reload();
                    }
                                                  );
                  }
                }
              }
                    );
            }
          </script>
          <!--#FIN MODAL AGREGAR#-->
          <div class="modal fade" id="modalGuardaCuenta" role="dialog" align="center" >
            <div class="modal-dialog" >
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
                  </h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Cuenta Guardada Correctamente
                  </p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardarCuenta" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="modalGuardaCuentaNo" role="dialog" align="center" >
            <div class="modal-dialog" >
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
                  </h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>No se ha podido guardar la información
                  </p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardarCuentaNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
                  </button>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
          </form>
      </div>                    
    </div>  
    <!-- 1 Final col-sm-8 text-center -->
    <div class="modal fade" id="myModalFirma" role="dialog" align="center" >
      <div class="modal-dialog" >
        <div class="modal-content">
          <div id="forma-modal" class="modal-header">
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Firma Dactilar
            </h4>
          </div>
          <div class="modal-body" style="margin-top: 8px">
            <img src="images/lectorhuella2.png" style="width: 500px; height: 300px"/>
            <br/>
            <a href="LISTAR_TERCERO_EMPLEADO_NATURAL2.php">Registrar Huella
            </a>
          </div>
          <div id="forma-modal" class="modal-footer">
            <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Guardar
            </button>
            <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js">
    </script>
    <script>
      function firma(){
        $("#myModalFirma").modal('show');
      }
    </script>
    <div class=" contTabla col-sm-10" style="margin-top: 2px; margin-left: -5px">
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
              <td class="oculto">Identificador
              </td>
              <td width="7%" class="cabeza">
              </td>
              <td class="cabeza">
                <strong>Cuenta Contable
                </strong>
              </td>
              <td class="cabeza">
                <strong>Tercero
                </strong>
              </td>
              <td class="cabeza">
                <strong>Centro Costo
                </strong>
              </td>
              <td class="cabeza">
                <strong>Proyecto
                </strong>
              </td>
              <td class="cabeza">
                <strong>Débito
                </strong>
              </td>
              <td class="cabeza">
                <strong>Crédito
                </strong>
              </td>                                
              <td class="cabeza">
                <strong>Movimiento Cuenta
                </strong>
              </td>
            </tr>
            <tr>
              <th class="oculto">Identificador
              </th>
              <th width="7%">
              </th>
              <th class="cabeza">Cuenta Contable
              </th>
              <th class="cabeza">Tercero
              </th>
              <th class="cabeza">Centro Costo
              </th>
              <th class="cabeza">Proyecto
              </th>
              <th class="cabeza">Débito
              </th>
              <th class="cabeza">Crédito
              </th>
              <th class="cabeza">Movimiento Cuenta
              </th>
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
            IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, 
            ter.apellidouno, ter.apellidodos) IS NULL 
            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, 
            ter.apellidodos)='' ,(ter.razonsocial),
            CONCAT_WS(' ',ter.nombreuno,ter.nombredos,
            ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',  
            ter.id_unico, 
            CONCAT(ter.numeroidentificacion) AS 'TipoD',                                   
            CC.id_unico,
            CC.nombre,
            PR.id_unico,
            PR.nombre,
            DT.valor, 
            DT.conciliado 
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
                  <?php 
                if(!empty($_SESSION['idCompCntV']) )
                    {
                    ##BUSCAR FECHA COMPROBANTE 
                    $fc = "SELECT fecha FROM gf_comprobante_cnt WHERE id_unico = ".$_SESSION['idCompCntV'];
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
                    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 AND cp.anno = $anno";
                    $ci =$mysqli->query($ci);
                    if(mysqli_num_rows($ci)>0){ } else {?>
                    
                   
                <?php if($row[15]==1) { } else { ?>
                <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                  <li class="glyphicon glyphicon-trash">
                  </li>
                </a>
                <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);">
                  <li class="glyphicon glyphicon-edit">
                  </li>
                </a>                             
                <?php } ?>
                   <?php }} ?>  
              </td>
              <!-- Código de cuenta y nombre de la cuenta -->
              <td class="campos text-left" >
                <div class="acotado">
                  <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(mb_strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                    <div id="sltC<?php echo $row[0]; ?>" style="display:none">
                        <select id="sltCuenta<?php echo $row[0]; ?>" style=" padding: 2px;height:18; width: 150px" class="select2_single col-sm-12 campoD" >
                            <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                <?php 
                                $sqlCTN = "SELECT DISTINCT id_unico,"
                                        . "codi_cuenta,nombre "
                                        . "FROM gf_cuenta WHERE (codi_cuenta != $row[3]) 
                                            and parametrizacionanno = $anno 
                                            AND (movimiento = 1
                                            OR      centrocosto = 1
                                            OR      auxiliartercero = 1
                                            OR      auxiliarproyecto = 1) "
                                        . "ORDER BY codi_cuenta ASC";
                                $result = $mysqli->query($sqlCTN);
                                while ($s = mysqli_fetch_row($result)){
                                    echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                }
                                ?>                                                
                        </select>
                    </div>
                </div>
              </td>
              <!-- Datos de tercero -->
              <td class="campos text-left">
                <?php echo '<label class="valorLabel" title="'.$row[9].'" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[7]))).'</label>'; ?>
                <div id="sltTercero<?php echo $row[0]; ?>" style="display: none;">
                    <select id="stTercero<?php echo $row[0]; ?>" style="padding: 2px;height:18" class=" col-sm-12 campoD" onclick="cargarT(<?=$row[0]?>)">
                    <option value="<?php echo $row[8] ?>">
                      <?php echo  (ucwords(mb_strtolower($row[7]))) ?>
                    </option>
                    <?php
                    $sqlTR = "SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, 
                    ter.apellidouno, ter.apellidodos) IS NULL 
                    OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, 
                    ter.apellidodos)='' ,(ter.razonsocial),
                    CONCAT_WS(' ',ter.nombreuno,ter.nombredos,
                    ter.apellidouno,ter.apellidodos)) AS 'NOMBRE',  
                    ter.id_unico, CONCAT(ter.numeroidentificacion) AS 'TipoD' FROM gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                    WHERE  ter.id_unico != $row[8] AND ter.compania = $compania LIMIT 20";
                    $resulta = $mysqli->query($sqlTR);
                    while($e = mysqli_fetch_row($resulta)){  
                    echo '<option value="'.$e[1].'">'.ucwords(mb_strtolower($e[0].' - '.$e[2])).'</option>';                                                  
                    }
                    ?>
                  </select>
                </div>
              </td>
              <!--##############################-->
              <?php 
                #Centro Costo
              //loco
                echo '<td class="campos" align="left" ><div class="acotado" id="dvcostolb'.$row[0].'">';
                echo  ucwords(mb_strtolower($row[11]));
                echo '</div>';
                echo '<div id="dvcosto'.$row[0].'" style="display:none;">';
                echo "<select name='stlcentrox".$row[0]."' id='stlcentrox".$row[0]."' class='select2_single form-control input-sm' title='Seleccione Centro Costo' style='width:120px; height: 38px;' required> ";                                               
                $where ='';
                if (!empty($row[10])){
                    $idccx = $row[10];
                    $where = "AND id_unico != ".$idccx;
                    echo "<option value='$row[10]' selected>$row[11]</option>";
                }else {
                    echo "<option value='' selected>Centro Costo</option>";
                }
                $sqper = "SELECT *
                FROM gf_centro_costo 
                WHERE parametrizacionanno = $anno $where";
                $resper = $mysqli->query($sqper);                                                
                while ($rowcc = mysqli_fetch_row($resper)) {
                        echo "<option value='$rowcc[0]'>$rowcc[1]</option>";
                }
                echo '</select>';
                echo '</div>';
                echo '</td>';
              ?>              
              <!--##############################-->
              <?php 
              #Proyecto
              echo '<td class="campos" align="left" ><div class="acotado" id="dvproyectolb'.$row[0].'">';
              echo ucwords(mb_strtolower($row[13]));
              echo '</div>';

              echo '<div id="dvproyecto'.$row[0].'" style="display:none;">';
              echo "<select name='stlproyectox".$row[0]."' id='stlproyectox".$row[0]."' class='select2_single form-control input-sm' title='Seleccione Proyecto' style='width:120px; height: 38px;' required> ";                                               
              $wherep ='';
              if (!empty($row[12])){
                  $idpx = $row[12];
                  $wherep = "AND id_unico != ".$idpx;
                  echo "<option value='$row[12]' selected>$row[13]</option>";
              }else {
                  echo "<option value='' selected>Proyecto</option>";
              }
              $sqper = "SELECT *
              FROM gf_proyecto 
              WHERE compania = $compania $wherep";
              $resper = $mysqli->query($sqper);                                                
              while ($rowpy = mysqli_fetch_row($resper)) {
                      echo "<option value='$rowpy[0]'>$rowpy[1]</option>";
              }
              echo '</select>';
              echo '</div>';
              echo '</td>';
              ?>
              
              <!-- Campo de valor debito y credito. Validación para imprimir valor -->
              <td class="campos text-right" align="center">
                <?php 
                if ($row[4] == 1) {
                    if($row[14] >= 0){
                        $sumar += $row[14];
                        echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                        echo '<input maxlength="50" align="center" onkeyup="txtValDebit('.$row[0].')" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$row[14].'" />';
                    }else{
                        echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                        echo '<input maxlength="50" type="text" onkeyup="txtValDebit('.$row[0].')" onkeypress="return justNumbers(event)" align="center" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                    }  
                }else if($row[4] == 2){
                        if($row[14] <= 0){
                            $x = (float) substr($row[14],'1');
                            $sumar += $x;
                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                            echo '<input maxlength="50" align="center" onkeyup="txtValDebit('.$row[0].')" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$x.'" />';
                        }else{
                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                            echo '<input maxlength="50" onkeyup="txtValDebit('.$row[0].')" align="center" onkeypress="return justNumbers(event)" type="text" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
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
                        echo '<input maxlength="50" onkeyup="txtValCredit('.$row[0].');" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$row[14].'" />';                                                                                                
                    }else{
                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                        echo '<input maxlength="50" type="text" onkeyup="txtValCredit('.$row[0].');" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  name="txtCredito'.$row[0].'"  id="txtCredito'.$row[0].'" value="0"/>';
                   }
                }else if($row[4] == 1){
                    if($row[14] <= 0){
                        $x = (float) substr($row[14],'1');
                        $sumaT += $x;
                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                        echo '<input maxlength="50" onkeyup="txtValCredit('.$row[0].');" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$x.'" />';                                                                                                
                    }else{
                        echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                        echo '<input maxlength="50" onkeyup="txtValCredit('.$row[0].');" type="text" onkeypress="return justNumbers(event)" class="col-sm-12 text-left campoD" style="display:none;padding:2px;height:19px" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="0"/>';
                    }
                }?>                                    
              </td>
              <td class="campos">
                <div style="display:inline">
                  <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $row[0]?>,<?php echo $row[14]?>);" title="Comprobante detalle movimiento">
                    <i class="glyphicon glyphicon-file">
                    </i>
                  </a>                                        
                </div>
                <div >
                  <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                    <tbody>
                      <tr style="background-color:transparent;">
                        <td style="background-color:transparent;">
                          <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                            <li class="glyphicon glyphicon-floppy-disk">
                            </li>
                          </a>
                        </td>
                        <td style="background-color:transparent;">
                          <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelarx(<?php echo $row[0];?>);" >
                            <i title="Cancelar" class="glyphicon glyphicon-remove" >
                            </i>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
            <?php 
            }  
            }
            ?>
          </tbody>          
        </table>
        <script type="text/javascript">

          function txtValDebit(id)
          {
            //txtCredito + id.
            var valorTxtCredito = $("#txtCredito" + id).val();
            if(valorTxtCredito != 0)
            {
              $("#txtCredito" + id).val(0);
            }
          }
        </script>
        <script type="text/javascript">
          function txtValCredit(id)
          {
            //txtDebito + id.
            var valorTxtDebito = $("#txtDebito" + id).val();
            if(valorTxtDebito != 0)
            {
              $("#txtDebito" + id).val(0);
            }
          }
        </script>
      </div> 
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
              <strong>Totales:
              </strong>
            </label>                                
          </div>
        </div>                        
        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
          <?php 
            if (($valorD) === NULL) { ?>
          <label class="control-label valores" title="Suma débito">0
          </label>                   
          <?php
            }else { ?>
          <label class="control-label valores" title="Suma débito">
            <?php echo number_format($valorD, 2, '.', ',') ?>
          </label>
          <?php }
            ?>
        </div>                        
        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
          <?php 
           if ($valorC === NULL) { ?>
          <label class="control-label valores" title="Suma crédito">0
          </label>
          <?php
        }else{ ?>
          <label class="control-label valores" title="Suma crédito">
            <?php echo number_format($valorC, 2, '.', ','); ?>
          </label>
          <?php
            }
            ?>
        </div>
        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
          <?php 
          if ($diferencia === 0) { ?>
          <label class="control-label text-right valores" title="Diferencia">0.00
          </label>                          
          <?php }else{ ?>
          <label class="control-label text-right valores" title="Diferencia">
            <?php echo number_format($diferencia, 2, '.', ',') ; ?>
          </label>
          <input type="hidden" id="diferencia" value="<?php echo $diferencia;?>">
          <?php    
            }
            ?>                                  
        </div> 
      </div>                                       
    </div>
  </div>
  </div>
<script type="text/javascript" src="js/select2.js"></script>
<script>
  $(document).ready(function() {
    $(".select2_single").select2({
      allowClear: true
    });
  });
</script>
<script type="text/javascript">
  $(document).ready(function()
                    {
    $("#btnGuardar").click(function()
                           {
      var idComprobante = $("#idComprobante").val();
      var diferencia = $("#diferencia").val();
      var cuentaBancaria = $("#cuentaBancaria").val();
      var formapago = $("#formaPago").val();
      if(cuentaBancaria != 0 && cuentaBancaria != "")
      {
        /**/  var form_data = {
          estruc: 9, idComprobante: idComprobante, diferencia: diferencia, 
          cuentaBancaria: cuentaBancaria, formapago:formapago};
        $.ajax({
          type: "POST",
          url: "estructura_aplicar_retenciones.php",
          data: form_data,
          success: function(response)
          {
            console.log(response);
            var  result = JSON.parse(response);
            if(result == 1)
            {
              $("#mdlGuardado").modal('show');
            }
            else
            {
              $("#mdlNoGuardado").modal('show');
            }
            //document.location = 'algo.php'; 
          }
          //Fin succes.
        }
              );
        //Fin ajax. 
      }
      else
      {
        $("#mdlNoCuentBan").modal('show');
      }
    }
                          );
  }
                   );
</script>
<div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Seleccione un tipo de comprobante.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información eliminada correctamente.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnElminDetCnt" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal2" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="infoM" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información modificada correctamente.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlNoCuentBan" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">          
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No hay una cuenta bancaria seleccionada.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnNoCuentBan" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Modales de guardado -->
<div class="modal fade" id="mdlGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Información guardada correctamente.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuardado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="mdlNoGuardado" role="dialog" align="center"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido guardar la información.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnGuardado2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar
        </button>
      </div>
    </div>
  </div>
</div>   
<div class="modal fade" id="noModifico" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">          
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se ha podido modificar la información.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para indicar que ya existe el elemento -->
<div class="modal fade" id="myModal3" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información
        </h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>La información ya existe.
        </p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar
        </button>
      </div>
    </div>
  </div>
</div>    
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js">
</script>
<script type="text/javascript">
  $('#btnGuardado').click(function(){
    document.location.reload();
    // dejar.
  }
                         );
</script>
<script type="text/javascript" >
  function abrirdetalleMov(id, valor){
    var form_data={
      id:id,
      valor:valor
    };
    $.ajax({
      type: 'POST',
      url: "registrar_GF_DETALLE_EGRESO.php#mdlDetalleMovimiento",
      data:form_data,
      success: function (data) {
        $("#mdlDetalleMovimiento").html(data);
        $(".mov").modal('show');
      }
    }
          );
  }
</script>
<script type="text/javascript">
  function eliminar(id)
  {
    var result = '';
    $("#myModal").modal('show');
    $("#ver").click(function(){
      $("#mymodal").modal('hide');
      $.ajax({
        type:"GET",
        url:"json/eliminarDetalleComprobanteJson.php?id="+id,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true)
            $("#myModal1").modal('show');
          else
            $("#myModal2").modal('show');
        }
      }
            );
    }
                   );
  }
</script>
<script type="text/javascript">
  function modificar(id){
    //loco
    if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
      //var sltcuentaC = 'sltC'+$("#idPrevio").val();
      //var lblCuentaC = 'cuenta'+$("#idPrevio").val();
      var sltTerceroC = 'sltTercero'+$("#idPrevio").val();
      var lblTerceroC = 'tercero'+$("#idPrevio").val();
      var sltCentroCC = 'sltcentroC'+$("#idPrevio").val();
      var lblCentroCC = 'centroC'+$("#idPrevio").val();
      var sltProyectoC = 'sltProyecto'+$("#idPrevio").val();
      var lblProyectoC = 'proyecto'+$("#idPrevio").val();
      var txtDebitoC = 'txtDebito'+$("#idPrevio").val();
      var lblDebitoC = 'debitoP'+$("#idPrevio").val();
      var txtCreditoC = 'txtCredito'+$("#idPrevio").val();
      var lblCreditoC = 'creditoP'+$("#idPrevio").val();
      var guardarC = 'guardar'+$("#idPrevio").val();
      var cancelarC = 'cancelar'+$("#idPrevio").val();
      var tablaC = 'tab'+$("#idPrevio").val();
      
      var cuenta = 'sltC'+$("#idPrevio").val();
      var cuentalbl = 'cuenta'+$("#idPrevio").val();
      //$("#"+sltcuentaC).css('display','none');                               
      //$("#"+lblCuentaC).css('display','block');
      $("#"+sltTerceroC).css('display','none');
      $("#"+lblTerceroC).css('display','block');
      $("#"+sltCentroCC).css('display','none');
      $("#"+lblCentroCC).css('display','block');
      $("#"+sltProyectoC).css('display','none');
      $("#"+lblProyectoC).css('display','block');
      $("#"+txtDebitoC).css('display','none');
      $("#"+lblDebitoC).css('display','block');
      $("#"+txtCreditoC).css('display','none');
      $("#"+lblCreditoC).css('display','block');
      $("#"+guardarC).css('display','none');
      $("#"+cancelarC).css('display','none');
      $("#"+tablaC).css('display','none');
      $("#"+cuenta).css("display", "none");
      $("#"+cuentalbl).css("display", "block");
    }
    //var sltcuenta = 'sltC'+id;
    //var lblCuenta = 'cuenta'+id;
    var sltTercero = 'sltTercero'+id;
    var lblTercero = 'tercero'+id;
    var sltCentroC = 'sltcentroC'+id;
    var lblCentroC = 'centroC'+id;
    var sltProyecto = 'sltProyecto'+id;
    var lblProyecto = 'proyecto'+id;
    var txtDebito = 'txtDebito'+id;
    var lblDebito = 'debitoP'+id;
    var txtCredito = 'txtCredito'+id;
    var lblCredito = 'creditoP'+id;
    var guardar = 'guardar'+id;
    var cancelar = 'cancelar'+id;
    var tabla = 'tab'+id;
    var cuenta ='sltC'+id;
    var cuentalbl ='cuenta'+id; 
    var dvcostolb = 'dvcostolb' + id;
    var dvcosto = 'dvcosto' + id;
    var dvproyectolb = 'dvproyectolb' + id;
    var dvproyecto = 'dvproyecto' + id;

    $("#"+sltTercero).css('display','block');
    $("#"+lblTercero).css('display','none');
    $("#"+sltCentroC).css('display','block');
    $("#"+lblCentroC).css('display','none');
    $("#"+sltProyecto).css('display','block');
    $("#"+lblProyecto).css('display','none');
    $("#"+txtDebito).css('display','block');
    $("#"+lblDebito).css('display','none');
    $("#"+txtCredito).css('display','block');
    $("#"+lblCredito).css('display','none');
    $("#"+guardar).css('display','block');
    $("#"+cancelar).css('display','block');
    $("#"+tabla).css('display','block');
    $("#"+cuenta).css("display", "block");
    $("#"+cuentalbl).css("display", "none");
    $("#" + dvcostolb).css("display", "none");
    $("#" + dvcosto).css("display", "block");
    $("#" + dvproyectolb).css("display", "none");
    $("#" + dvproyecto).css("display", "block");
    
    $("#idActual").val(id);
    if($("#idPrevio").val() != id){
      $("#idPrevio").val(id);
    }
  }
</script>
<script type="text/javascript">
  function cancelarx(id){
    //var sltcuenta = 'sltC'+id;
    //var lblCuenta = 'cuenta'+id;
    var sltTercero = 'sltTercero'+id;
    var lblTercero = 'tercero'+id;
    var sltCentroC = 'sltcentroC'+id;
    var lblCentroC = 'centroC'+id;
    var sltProyecto = 'sltProyecto'+id;
    var lblProyecto = 'proyecto'+id;
    var txtDebito = 'txtDebito'+id;
    var lblDebito = 'debitoP'+id;
    var txtCredito = 'txtCredito'+id;
    var lblCredito = 'creditoP'+id;
    var guardar = 'guardar'+id;
    var cancelar = 'cancelar'+id;
    var tabla = 'tab'+id;
    var cuenta ='sltC'+id;
    var cuentalbl ='cuenta'+id; 
    var dvcostolb = 'dvcostolb' + id;
    var dvcosto = 'dvcosto' + id;
    var dvproyectolb = 'dvproyectolb' + id;
    var dvproyecto = 'dvproyecto' + id;    
    //$("#"+sltcuenta).css('display','none');                               
    //$("#"+lblCuenta).css('display','block');
    $("#"+sltTercero).css('display','none');
    $("#"+lblTercero).css('display','block');
    $("#"+sltCentroC).css('display','none');
    $("#"+lblCentroC).css('display','block');
    $("#"+sltProyecto).css('display','none');
    $("#"+lblProyecto).css('display','block');
    $("#"+txtDebito).css('display','none');
    $("#"+lblDebito).css('display','block');
    $("#"+txtCredito).css('display','none');
    $("#"+lblCredito).css('display','block');
    $("#"+guardar).css('display','none');
    $("#"+cancelar).css('display','none');
    $("#"+tabla).css('display','none');
    $("#"+cuenta).css("display", "none");
    $("#"+cuentalbl).css("display", "block");
    $("#" + dvcostolb).css("display", "block");
    $("#" + dvcosto).css("display", "none");
    $("#" + dvproyectolb).css("display", "block");
    $("#" + dvproyecto).css("display", "none");
  }
</script>
<script type="text/javascript">
  function guardarCambios(id){
    //var sltcuenta = 'sltC'+id;
    var sltTercero = 'stTercero'+id;
    var sltCentroC = 'sltcentroC'+id;
    var sltProyecto = 'sltProyecto'+id;
    var txtDebito = 'txtDebito'+id;
    var txtCredito = 'txtCredito'+id;
    var cuenta = 'sltCuenta'+id;
    var stlcentrox = 'stlcentrox' + id;
    var stlproyectox = 'stlproyectox' + id;
    var form_data = {
      is_ajax:1,
      id:+id,
      //cuenta:$("#"+sltcuenta).val(),
      tercero:$("#"+sltTercero).val(),
      centroC:$("#" + stlcentrox).val(),
      proyecto:$("#" + stlproyectox).val(),
      debito:$("#"+txtDebito).val(),
      credito:$("#"+txtCredito).val(),
      cuenta :$("#"+cuenta).val()
    };
    var result='';
    $.ajax({
      type: 'POST',
      url: "json/modificarDetalleCnt.php",
      data:form_data,
      success: function (data) {
        result = JSON.parse(data);
        if (result==true) {
          $("#infoM").modal('show');
        }
        else {
          
            $("#noModifico").modal('show');
          
        }
      }
    }
          );
  }
</script>
<script type="text/javascript">
  $('#btnModifico').click(function(){
    document.location.reload();
  }
                         );
</script>
<script type="text/javascript">
  $('#btnNoModifico').click(function(){
    document.location.reload();
    // dejar.
  }
                           );
</script>
<script type="text/javascript"> //Eliminado Correctamente.
  $('#btnElminDetCnt').click(function(){
    document.location.reload();
    // dejar.
  }
                            );
</script>
<script type="text/javascript">    
  $('#ver2').click(function(){
    document.location.reload();
    // dejar.
  }
                  );
</script>
<script type="text/javascript" >
  $("#ver3").click(function(){
    document.location.reload();
    // dejar. 
  }
                  );
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
<!--CIERRE 
    ###BUSCAR EL CIERRE MAYOR
    --->
    <?php

    if(!empty($_SESSION['idCompCntV']) )
    {
    ##BUSCAR FECHA COMPROBANTE 
    $fc = "SELECT fecha FROM gf_comprobante_cnt WHERE id_unico = ".$_SESSION['idCompCntV'];
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
    pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 AND cp.anno = $anno";
    $ci =$mysqli->query($ci);
    if(mysqli_num_rows($ci)>0){ ?>
    <script>
    $(document).ready(function()
    {
    $("#btnGuardar").prop("disabled", true);
    $("#btnModificarCom").prop("disabled", true);
    $("#btnAgregarCuentaContable").prop("disabled", true);
    
    });
    </script>
    <?php }} ?>
<?php require_once './footer.php'; ?>
    <script>
        function open_modal_r() {  
              
            var id = $("#id").val();
            var form_data={                            
              id:id 
            };
             $.ajax({
                type: 'POST',
                url: "GF_MODIFICAR_RETENCIONES_MODAL.php#mdlModificarReteciones",
                data:form_data,
                success: function (data) { 
                    $("#mdlModificarReteciones").html(data);
                    $(".movi").modal("show");
                }
            }).error(function(data,textStatus,jqXHR){
                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
            })            
        }
    </script>
    <script>
        function open_modal_r2(numd, comp) {
            
            var id = $("#id").val();
            var form_data = {
                id: id, 
                num: numd, 
                vald :<?php echo $valorD?>,
                comprobante: comp, 
            };
            $.ajax({
                type: 'POST',
                url: "GF_INGRESAR_RETENCIONES_MODAL.php#mdlIngresarRetenciones",
                data: form_data,
                success: function (data) {
                    $("#mdlIngresarRetenciones").html(data);
                    $(".movi1").modal("show");
                }
            }).error(function (data, textStatus, jqXHR) {
                alert('data:' + data + '- estado:' + textStatus + '- jqXHR:' + jqXHR);
            })
        }
    </script>
    <script>
    $('#tercero').on('select2-open', function() { 
        console.log('aaaa');
        buscarTercero('tercero');
    });

    function buscarTercero(campo){
        console.log(campo);
        $('.select2-input').on("keydown", function(e) {
            let term = e.currentTarget.value;
            let form_data4 = {action: 8, term: term};

            $.ajax({
                type:"POST",
                url:"jsonPptal/gf_tercerosJson.php",
                data:form_data4,
                success: function(data){
                    let option = '<option value=""> - </option>';
                    console.log(data);
                     option = option+data;
                    $("#"+campo).html(option);

                }
            }); 
        });

    }
    function cargarT(id){
        $("#stTercero"+id).select2({ placeholder:"Tercero",allowClear: true });
        $("#stTercero"+id).on('select2-open', function() { 
            buscarTercero("stTercero"+id);
        });
    }
    $('#slttercero').on('select2-open', function () {
        buscarTercero('slttercero');
    });
 </script>  
</body>

<?php require_once './GF_MODIFICAR_RETENCIONES_MODAL.php'; ?>
<?php require_once './GF_INGRESAR_RETENCIONES_MODAL.php'; ?>   
<?php require_once './registrar_GF_DETALLE_EGRESO.php'; ?>
</html>