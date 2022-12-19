<?php 
require_once 'head_listar.php';  
require_once('Conexion/conexion.php');
require_once('./jsonPptal/funcionesPptal.php');
require_once('./jsonSistema/funcionCierre.php');
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$num_anno   = anno($_SESSION['anno']);
if(!empty($_GET['reg'])){
    $registro = $_GET['reg'];
    $tipo = $_GET['tip'];
    #############CALCULAR EL NUMERO##############
    $parametroAnno = $_SESSION['anno'];
    $sqlAnno = 'SELECT anno 
            FROM gf_parametrizacion_anno 
            WHERE id_unico = '.$parametroAnno;
    $paramAnno = $mysqli->query($sqlAnno);
    $rowPA = mysqli_fetch_row($paramAnno);
    $numero = $rowPA[0];

    $queryNumComp = 'SELECT MAX(numero) 
            FROM gf_comprobante_pptal 
            WHERE tipocomprobante = '.$tipo .' AND parametrizacionanno = '.$parametroAnno. ' 
            AND numero LIKE \''.$numero.'%\'';
    $numComp = $mysqli->query($queryNumComp);
    $row = mysqli_fetch_row($numComp);
    if($row[0] == 0)
    {
        $numero .= '000001';
    }
    else
    {
        $numero = $row[0] + 1;
    }
    $numero = $numero;
    ################### TRAER TIPO #################
    $tipoC = "SELECT id_unico, UPPER(codigo), LOWER(nombre) "
            . "FROM gf_tipo_comprobante_pptal WHERE id_unico = $tipo";
    $tipoC = $mysqli->query($tipoC);
    $tipoC = mysqli_fetch_row($tipoC);
    $idTipo = $tipoC[0];
    $nombreTipo = $tipoC[1].' - '.ucwords($tipoC[2]);
    
    #################DATOS REGISTRO ############
    $dis = "SELECT
        com.id_unico,
        com.numero,
        DATE_FORMAT(com.fecha, '%d/%m/%Y'),
        com.descripcion,
        UPPER(tip.codigo), 
        (SELECT
            SUM(dcp.valor)
        FROM
            gf_detalle_comprobante_pptal dcp
        WHERE
            dcp.comprobantepptal = com.id_unico
            ) AS valor, 
        IF(CONCAT_WS(' ',
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
     tr.apellidodos)) AS NOMBRE, tr.id_unico, tr.numeroidentificacion, tr.digitoverficacion , 
     cc.id_unico, cc.nombre , com.numerocontrato , com.descripcion 
 
    FROM
        gf_comprobante_pptal com
    LEFT JOIN gf_tipo_comprobante_pptal tip ON
        tip.id_unico = com.tipocomprobante 
    LEFT JOIN 
        gf_tercero tr ON com.tercero = tr.id_unico 
    LEFT JOIN 
        gf_clase_contrato cc ON cc.id_unico = com.clasecontrato 
    WHERE
        md5(com.id_unico) = '$registro'";
    $dis = $mysqli->query($dis);
    $dis = mysqli_fetch_row($dis);
    $iddis = $dis[0];
    $valor = '$'.number_format($dis[5], 2, '.', ',');
    $datosdis = $dis[1].' '.$dis[4].' '.$dis[2].' '.$dis[3].' '.$valor; 
    $descripcionDis = $dis[3];
    if(empty($dis[9])){
        $numI = $dis[8];
    } else {
        $numI = $dis[8].'-'.$dis[9];
    }
    $idtercero = $dis[7];
    $nomTercero = ucwords(mb_strtolower($dis[6])).'-'.$numI;
    $idcc = $dis[10];
    $cc = ucwords(mb_strtolower($dis[11]));
    $numcontrato = $dis[12];
    $descripcion = $dis[13];
} else {
    $registro = "";
}
if(!empty($_GET['mod'])){
   $modificacion = $_GET['mod'];
   $modif = "SELECT com.id_unico,
        com.numero,
        DATE_FORMAT(com.fecha, '%d-%m-%Y'),
        DATE_FORMAT(com.fechavencimiento, '%d-%m-%Y'),
        com.descripcion,
        tip.id_unico, 
        UPPER(tip.codigo), 
        LOWER(tip.nombre),
        (SELECT
            SUM(dcp.valor)
        FROM
            gf_detalle_comprobante_pptal dcp 
        WHERE
            dcp.comprobantepptal = com.id_unico
            ) AS valor, IF(CONCAT_WS(' ',
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
        tr.apellidodos)) AS NOMBRE, tr.id_unico, tr.numeroidentificacion, tr.digitoverficacion , 
        cc.id_unico, cc.nombre , com.numerocontrato
    FROM
        gf_comprobante_pptal com
    LEFT JOIN gf_tipo_comprobante_pptal tip ON
        tip.id_unico = com.tipocomprobante 
    LEFT JOIN 
        gf_tercero tr ON com.tercero = tr.id_unico 
    LEFT JOIN 
        gf_clase_contrato cc ON cc.id_unico = com.clasecontrato 
    WHERE
        md5(com.id_unico) = '$modificacion'";
    $modif = $mysqli->query($modif);
    $modif = mysqli_fetch_row($modif);
    $idModificacion = $modif[0];
    $numeroMod      = $modif[1];
    $fecha          = $modif[2];
    $fechaVen       = $modif[3];
    $descripcion    = $modif[4];
    $idTipo         = $modif[5];
    $nombreTipo     = $modif[6].' - '.ucwords($modif[7]);
    if(empty($modif[12])){
        $numI = $modif[11];
    } else {
        $numI = $modif[11].'-'.$modif[12];
    }
    $idtercero = $modif[10];
    $nomTercero = ucwords(mb_strtolower($modif[9])).'-'.$numI;
    $idcc = $modif[13];
    $cc = ucwords(mb_strtolower($modif[14]));
    $numcontrato = $modif[15];
    
    
    
} else {
    $modificacion ="";
}





?>

<title>Modificación Aprobación Orden</title>

<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<script type="text/javascript">

 $(document).ready(function()
  {   $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Anterior',
        nextText: 'Siguiente',
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
        yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
        maxDate: '31/12/<?php echo $num_anno?>',
        minDate: '01/01/<?php echo $num_anno?>'
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    <?PHP if(!empty($modificacion)) { ?>
    var fecha = '<?php echo date("d/m/Y", strtotime($fecha));?>';
    var fechaVen = '<?php echo date("d/m/Y", strtotime($fechaVen));?>';
    $("#fecha").datepicker({changeMonth: true}).val(fecha);   
    $("#fechaVen").datepicker({changeMonth: true, minDate : fecha}).val(fechaVen);   
    <?php } else { ?>
    $("#fecha").datepicker({changeMonth: true}).val();   
    $("#fechaVen").datepicker({changeMonth: true}).val();       
    <?php } ?>   

  });

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
<script src="js/md5.pack.js"></script>
</head>
<body>
<div class="container-fluid text-center"  >
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10" style="margin-left: -16px;margin-top: 5px" > 
            <h2 align="center" class="tituloform col-sm-10" style="margin-top: -5px; margin-bottom: 2px;" >Modificación Aprobación Orden De Pago</h2>
            <div class="col-sm-10">
                <div class="client-form contenedorForma col-sm-12"  style=""> <!-- No tenía col-sm-12 -->
                    <form name="form" class="form-horizontal" method="POST" onsubmit="return valida();"  enctype="multipart/form-data" action="jsonPptal/registrar_MOD_APO_COMPROBANTE_PPTALJson.php">
                        <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
                        </p>
                        <div class="form-group form-inline col-sm-12" style="margin-top: 0px; margin-left: -2px; margin-bottom: 0px;"> <!-- Primera Fila -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"><!-- Tipo de comprobante -->
                                <?php if(!empty($modificacion)) { ?>
                                <input type="hidden" name="idModificacion" id="idModificacion" value="<?php echo $idModificacion?>">
                                <?php } ?>
                                <!-- Tipo de comprobante -->
                                <label for="tipoComPtal" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Pptal:</label><br>
                                <select name="tipoComPtal" id="tipoComPtal" class="form-control input-sm" title="Seleccione un tipo de comprobante" style="width:170px; " required>
                                    <?php if(empty($registro) && empty($modificacion)) { ?>
                                        <option value="">Tipo Comprobante</option>
                                        <?php 
                                        $tipo = "SELECT id_unico, codigo, nombre 
                                            FROM gf_tipo_comprobante_pptal 
                                            WHERE clasepptal = 20  
                                            AND tipooperacion != 1 AND vigencia_actual =1 AND compania = $compania 
                                            ORDER BY codigo ASC";
                                        $tipo = $mysqli->query($tipo);
                                        while ($row2 = mysqli_fetch_row($tipo)) { ?>
                                        <option value="<?php echo $row2[0]?>"><?php echo mb_strtoupper($row2[1]).' - '.ucwords(mb_strtolower($row2[2]));?></option>
                                        <?php }?>
                                    <?php } else { 
                                          if(!empty($registro)){ ?>
                                        <option value="<?php echo $idTipo; ?>"><?php echo $nombreTipo; ?></option>
                                          <?php } else {
                                              if(!empty($modificacion)){ ?>
                                                  <option value="<?php echo $idTipo; ?>"><?php echo $nombreTipo; ?></option>

                                              <?php } else { ?>
                                               <option value="">Tipo Comprobante</option>   
                                          <?php }
                                          }
                                    } ?>
                                </select> 
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"> <!-- Tercero -->
                                <label for="tercero" class="control-label" ><strong style="color:#03C1FB;">*</strong>Tercero:</label><br>
                                <select name="tercero" id="tercero" class="select2_single form-control input-sm" title="Seleccione un tipo de comprobante" style="width:170px;" <?php if(empty($_SESSION['id_comp_pptal_MR'])){echo 'autofocus=""';}?>  required>
                                     <?php if(empty($registro) && empty($modificacion)) { ?>
                                        <option value="">Tercero</option>   
                                    <?php } else { 
                                          if(!empty($registro)){ ?>
                                        <option value="<?php echo $idtercero; ?>"><?php echo $nomTercero; ?></option>
                                          <?php } else {
                                              if(!empty($modificacion)){ ?>
                                                  <option value="<?php echo $idtercero; ?>"><?php echo $nomTercero; ?></option>

                                              <?php } else { ?>
                                               <option value="">Tercero</option>   
                                          <?php }
                                          }
                                    } ?>
                                    
                                </select>
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"> <!-- Disponibilidad -->
                                <label for="solicitudAprobada" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Aprobación:</label><br>
                                <select name="solicitudAprobada" id="solicitudAprobada" class="select2_single form-control input-sm" title="Número de Aprobación" style="width:170px;">
                                    <?php if (empty($registro) && empty($modificacion)) { ?>
                                        <option value="">Aprobación</option>
                                        <?php }  else { 
                                            if(!empty($registro)) { ?>
                                            <option value="<?php echo $iddis; ?>"><?php echo $datosdis;?></option>
                                            
                                        <?php } else { 
                                            if(!empty($modificacion)) { ?>
                                            <?php $disA ="SELECT com.id_unico,
                                                    com.numero,
                                                    DATE_FORMAT(com.fecha, '%d/%m/%Y'),
                                                    com.descripcion,
                                                    UPPER(tip.codigo), 
                                                    (SELECT
                                                        SUM(dcp.valor)
                                                    FROM
                                                        gf_detalle_comprobante_pptal dcp
                                                    WHERE
                                                        dcp.comprobantepptal = com.id_unico) AS valor
                                                FROM
                                                    gf_comprobante_pptal com1 
                                                LEFT JOIN gf_detalle_comprobante_pptal dc1 ON com1.id_unico = dc1.comprobantepptal 
                                                LEFT JOIN gf_detalle_comprobante_pptal dca ON dc1.comprobanteafectado = dca.id_unico 
                                                LEFT JOIN gf_comprobante_pptal com ON dca.comprobantepptal = com.id_unico 
                                                LEFT JOIN gf_tipo_comprobante_pptal tip ON
                                                    tip.id_unico = com.tipocomprobante
                                                WHERE
                                                    com1.id_unico =$idModificacion";
                                            $disA = $mysqli->query($disA);
                                            if(mysqli_num_rows($disA)>0){ 
                                                $rowdis = mysqli_fetch_row($disA);
                                            ?>
                                            <option value="<?php echo $rowdis[0]?>"><?php echo $rowdis[1].' '.$rowdis[4].' '.$rowdis[2].' '.$rowdis[3].' $'. number_format($rowdis[5],2,'.',',');?></option>
                                            <?php } else { ?>
                                            <option value="">Aprobación</option>
                                            <?php } } else { ?>
                                            <option value="">Aprobación</option>
                                        <?php } } } ?>
                                </select>  
                            </div>
                            <script>
                                $("#tipoComPtal").change(function(){
                                    var opcion = '<option value="" >Tercero</option>';
                                    var form_data = { estruc: 3, tipo:+$("#tipoComPtal").val() };
                                    $.ajax({
                                      type: "POST",
                                      url: "jsonPptal/consultas.php",
                                      data: form_data,
                                      success: function(response)
                                      {
                                        console.log(response);
                                        if(response == "" || response == 0)
                                        {
                                          var noHay = '<option value="N" >No hay Terceros</option>';
                                          $("#tercero").html(noHay).focus();
                                        }
                                        else
                                        {
                                            opcion += response;
                                            $("#tercero").html(opcion).focus();
                                            
                                        }
                                        }
                                        });
                                    var form_data = { estruc: 2, id_tip_comp:+$("#tipoComPtal").val() };
                                    $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/consultas.php",
                                        data: form_data,
                                        success: function(response)
                                        {       
                                            console.log('aca');
                                            console.log(response);
                                        var numero = parseInt(response);
                                        $("#noDisponibilidad").val(numero);
                                        }
                                    })
                                })
                            </script>
                            <script>
                                $("#tercero").change(function(){
                                    var opcion = '<option value="" >Aprobación</option>';
                                    var form_data = { estruc: 35, tipo:+$("#tipoComPtal").val(), tercero:+$("#tercero").val() };
                                    $.ajax({
                                      type: "POST",
                                      url: "jsonPptal/consultas.php",
                                      data: form_data,
                                      success: function(response)
                                      {
                                        console.log(response);
                                        if(response == "" || response == 0)
                                        {
                                          var noHay = '<option value="N" >No hay Aprobación</option>';
                                          $("#solicitudAprobada").html(noHay).focus();
                                        }
                                        else
                                        {
                                            opcion += response;
                                            $("#solicitudAprobada").html(opcion).focus();
                                        }
                                    }
                                    }); 
                                })
                            </script>
                            <script>
                                $("#solicitudAprobada").change(function(){
                                    var tipo = $("#tipoComPtal").val();
                                    var registro = $("#solicitudAprobada").val();
                                    document.location = 'MODIFICACION_APROBACION.php?reg='+md5(registro)+'&tip='+tipo; 
                                    
                                })
                            </script>
                            <div class="col-sm-1" style="margin-top: 15px;">
                                <a id="btnNuevoComp" class="btn sombra btn-primary" style="width: 40px; margin:  0 auto;" title="Nuevo"><li class="glyphicon glyphicon-plus"></li></a> <!-- Nuevo -->
                            </div>
                            <script>
                                 $("#btnNuevoComp").click(function(){
                                     document.location = 'MODIFICACION_APROBACION.php'; 
                                 });
                                 
                            </script>
                            <div class="col-sm-1" style="margin-top: 15px;"> <!-- Botón guardar -->
                                <button type="submit" id="btnGuardarComp" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" ><li class="glyphicon glyphicon-floppy-disk"></li></button> <!--Guardar-->
                            </div> <!-- Fin Botones nuevo y guardar -->
                            
                            <div class="col-sm-1" style="margin-top: 15px;">
                                <button type="button" id="btnModificar"  class="btn btn-primary sombra" class="modificar" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" ><li class="glyphicon glyphicon-pencil"></li></button> <!--Guardar-->
                            </div>
                            <?php if(!empty($modificacion)) { ?>
                            <script>
                                $("#btnModificar").click(function(){
                                    var id        = $("#idModificacion").val();
                                    var fecha     = $("#fecha").val();
                                    var fechaVen  = $("#fechaVen").val();
                                    var claseC    = $("#claseCont").val();
                                    var numC      = $("#noContrato").val();
                                    var des       = $("#descripcion").val();
                                    if(fecha =="" || fechaVen ==""){
                                        $("#validarMod").modal('show');
                                    } else {
                                        var form_data = { action: 5, fecha:fecha,descripcion:des,
                                            fechaVen:fechaVen, id:id, clasec : claseC, numeroC : numC};
                                            $.ajax({
                                            type: "POST",
                                            url: "jsonPptal/gf_modificacion_disponibilidadRegJson.php",
                                            data: form_data,
                                            success: function(response)
                                            { 
                                                console.log(response);
                                                if(response ==1){
                                                    $("#ModificacionConfirmada").modal('show');
                                                } else {
                                                    $("#ModificacionFallida").modal('show');
                                                }
                                            }
                                        })
                                    }
                                })
                                
                            
                            </script>
                            <?php } ?>
                        </div> 
                        <div class="form-group form-inline col-sm-12" style="margin-top: -10px; margin-left: -2px; margin-bottom: 0px;"> <!-- Primera Fila -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"><!-- Número de disponibilidad -->
                              <label for="noDisponibilidad" class="control-label" style=""><strong style="color:#03C1FB;">*</strong>Número Registro:</label><br/>
                              <?php if(empty($registro) && empty($modificacion)) { ?>    
                                <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly"  required>
                                <?php } else { 
                                    if(!empty($registro)) { ?>
                                <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly"  required value="<?php echo $numero?>"> 
                                    <?php } else {
                                        if(!empty($modificacion)) { ?>
                                <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly"  required value="<?php echo $numeroMod?>">
                                    <?php }  else { ?>
                                    <input class="input-sm" type="text" name="noDisponibilidad" id="noDisponibilidad" class="form-control" style="width:150px;" title="Número de disponibilidad" placeholder="Número Disponibilidad"  readonly="readonly"  required>
                                <?php } } }?>
                              
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"> <!-- Clase de contrato -->
                                <label for="claseCont" class="control-label" ><strong style="color:#03C1FB;">*</strong>Clase Contrato:</label><br>
                                <select name="claseCont" id="claseCont" class="select2_single form-control input-sm" title="Seleccione una clase de contrato" style="width:170px;" required>
                                    <?php if(empty($registro) && empty($modificacion)) { ?>    
                                <option value="">Clase Contrato</option> 
                                <?php $clasec = "SELECT id_unico, LOWER(nombre) FROM gf_clase_contrato"; 
                                $querycc = $mysqli->query($clasec);
                                 while ($row3 = mysqli_fetch_row($querycc)) { ?>
                                        <option value="<?php echo $row3[0]?>"><?php echo ucwords($row3[1])?></option>   
                                        <?php }?>
                                <?php } else { 
                                    if(!empty($registro)) { 
                                        if(!empty($idcc)) { 
                                         $clasec = "SELECT id_unico, LOWER(nombre) FROM gf_clase_contrato WHERE id_unico !='$idcc'";?>
                                        <option value="<?php echo $idcc?>"><?php echo $cc?></option>   
                                        <?php } else { 
                                         $clasec = "SELECT id_unico, LOWER(nombre) FROM gf_clase_contrato";?>   
                                        <option value="">Clase Contrato</option>   
                                        <?php } ?>
                                        <?php $querycc = $mysqli->query($clasec);
                                        while ($row3 = mysqli_fetch_row($querycc)) { ?>
                                        <option value="<?php echo $row3[0]?>"><?php echo ucwords($row3[1])?></option>   
                                        <?php }?>
                                    <?php } else {
                                        if(!empty($modificacion)) { ?>
                                   <?php if(!empty($idcc)) { 
                                         $clasec = "SELECT id_unico, LOWER(nombre) FROM gf_clase_contrato WHERE id_unico !='$idcc'";?>
                                        <option value="<?php echo $idcc?>"><?php echo $cc?></option>   
                                        <?php } else { 
                                         $clasec = "SELECT id_unico, LOWER(nombre) FROM gf_clase_contrato";?>   ?>
                                        <option value="">Clase Contrato</option>   
                                        <?php } ?> 
                                        <?php $querycc = $mysqli->query($clasec);
                                        while ($row3 = mysqli_fetch_row($querycc)) { ?>
                                        <option value="<?php echo $row3[0]?>"><?php echo ucwords($row3[1])?></option>   
                                        <?php }?>
                                    <?php }  else { ?>
                                    <option value="">Clase Contrato</option>   
                                <?php } } }?>
                                    
                                </select>
                            </div> 
                            <div class="col-sm-3" align="left" style="padding-left: 0px;">  <!-- Número de contrato -->
                                <label for="noContrato" class="control-label" ><strong style="color:#03C1FB;">*</strong>No. Contrato:</label><br>
                                <?php if(empty($registro) && empty($modificacion)) { ?>    
                                <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="" required>
                                <?php } else { 
                                    if(!empty($registro)) { 
                                        if(!empty($numcontrato)) { ?>
                                        <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="<?php echo $numcontrato?>" required>
                                        <?php } else { ?>
                                        <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="" required>
                                        <?php }} else {
                                        if(!empty($modificacion)) { 
                                            if(!empty($numcontrato)) { ?>
                                        <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="<?php echo $numcontrato?>" required>
                                        <?php } else { ?>
                                        <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="" required>
                                        <?php } }  else { ?>
                                    <input class="input-sm" type="text" name="noContrato" id="noContrato" class="form-control" style="width:170px;" title="Número de contrato" placeholder="No. Contrato" onkeypress="return txtValida(event,'num_car')" value="" required>
                                <?php } } }?>
                                
                                
                            </div>
                            <div class="col-sm-1" style="margin-top: 15px;" ><!--Imprimir-->
                                <button type="button" id="btnImprimir" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px; margin-left: 0px;" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></button> 
                            </div>
                            <?php 
                                if(!empty($modificacion)){ ?>
                                    <script type="text/javascript">
                                    $(document).ready(function()
                                    {
                                        $("#btnImprimir").prop("disabled",false);
                                        $("#btnImprimir").click(function(){
                                            window.open('informesPptal/inf_Modificaciones.php?id=<?php echo $modificacion;?>&tip=rep');
                                        });
                                    });
                                    </script>
                            <?php } ?>
                            <div class="col-sm-1" style="margin-top: 15px;">
                                    <button type="button" id="btnAgregarDis" class="btn btn-primary sombra" class="AgregarDis" style="background: #00548F; color: #fff; border-color: #1075C1; margin: 0 auto;" title="Agregar Disponibilidad">
                                        <li class="glyphicon glyphicon-pushpin"></li>
                                    </button> 
                            </div>
                            <!--FUNCION AGREGAR REGISTRO-->
                            <script>
                                $("#btnAgregarDis").click(function(){
                                    var opcion = '<option value="" >Aprobación</option>';
                                    var id = $("#idModificacion").val();
                                    var form_data = { estruc: 35, tipo:+$("#tipoComPtal").val(), tercero:+$("#tercero").val() };
                                    $.ajax({
                                      type: "POST",
                                      url: "jsonPptal/consultas.php",
                                      data: form_data,
                                      success: function(response)
                                      { 

                                            if(response == "" || response == 0)
                                            {
                                                var noHay = '<option value="N" >No hay Aprobaciones</option>';
                                                $("#disponibilidad").html(noHay).focus();
                                                $("#comprobantepptal").val(id);
                                                $("#mdlAgregarDis").modal('show');
                                            }
                                            else
                                            {
                                                opcion += response;
                                                $("#disponibilidad").html(opcion).focus();
                                                $("#comprobantepptal").val(id);
                                                $("#mdlAgregarDis").modal('show');
                                            }

                                      }
                                    }); 
                                })
                            </script>
                            <div class="modal fade" id="mdlAgregarDis" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div id="forma-modal" class="modal-header">
                                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar Registro</h4>
                                    </div>
                                    <div class="modal-body" style="margin-top: 8px">
                                        <input type="hidden" name="comprobantepptal" id="comprobantepptal" value="">
                                        <label class="form_label"><strong style="color:#03C1FB;">*</strong>Aprobación: </label>
                                        <select name="disponibilidad" id="disponibilidad" class="select2_single form-control input-sm" title="Número de Registro" style="width:250px;">
                                            <option value="" >Aprobación</option>
                                        </select> 
                                    </div>
                                    <div id="forma-modal" class="modal-footer">
                                        <button type="button" id="guardarDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Agregar</button>
                                      <button type="button" id="cancelarDis" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <script>
                                $("#guardarDis").click(function(){
                                    var dis =$("#disponibilidad").val();
                                    var id = $("#comprobantepptal").val();
                                    if(dis=="" || id==""){
                                        $("#validarMod").modal('show');
                                    } else {
                                        var form_data = { action: 4, dis:dis,id:id};
                                        $.ajax({
                                        type: "POST",
                                        url: "jsonPptal/gf_modificacion_disponibilidadRegJson.php",
                                        data: form_data,
                                        success: function(response)
                                        { 
                                            console.log(response);
                                            if(response ==3){
                                                $("#myModalAlertErrFec").modal('show');
                                            } else {
                                                if(response ==1){
                                                    $("#disCorrecto").modal('show');
                                                } else {
                                                    $("#disCorrectoNo").modal('show');
                                                }
                                            }
                                        }
                                        });
                                    }
                                })
                            </script> 
                            <div class="modal fade" id="disCorrecto" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <p>Modificación Agregada Correctamente</p>
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="btndisCorrecto" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                            Aceptar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="disCorrectoNo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <p>No se ha podido Agregar Modificación</p>
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="btndisCorrectoNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                                            Aceptar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            $("#btndisCorrecto").click(function(){
                                document.location.reload();
                            })
                            $("#btndisCorrectoNo").click(function(){
                                document.location.reload();
                            })
                        </script>
                        <div class="form-group form-inline col-sm-12" style="margin-top: -15px; margin-left: 0px; margin-bottom: 5px;">  <!-- Segunda fila -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"> <!-- Descripción -->
                                <label for="nombre" class="control-label" style="" >Descripción:</label><br/>
                                <?php if(empty($registro) && empty($modificacion)) { ?>    
                                <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ></textarea> 
                                <?php } else { 
                                    if(!empty($registro)) { 
                                        
                                        if(!empty($descripcion)) { ?>
                                        <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ><?php echo $descripcion?></textarea> 
                                        <?php } else { ?>
                                        <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ></textarea> 
                                        <?php }} else {
                                        if(!empty($modificacion)) { 
                                            if(!empty($descripcion)) { ?>
                                        <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ><?php echo $descripcion?></textarea> 
                                        <?php } else { ?>
                                        <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ></textarea> 
                                        <?php } }  else { ?>
                                    <textarea class="col-sm-2" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; height: 50px; width:170px" class="area" rows="2" name="descripcion" id="descripcion"  maxlength="500" placeholder="Descripción"  onkeypress="return validarDes(event, true)" ></textarea> 
                                <?php } } }?>
                                
                            </div> <!-- Fin Descripción -->
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"><!--  Fecha -->
                                <label for="fecha" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label> <br/>
                                <input class="form-control input-sm" type="text" name="fecha" id="fecha" style="width:170px;" title="Ingrese la fecha" placeholder="Fecha" value="" readonly="readonly" >
                            </div>
                            <div class="col-sm-3" align="left" style="padding-left: 0px;"> <!-- Fecha Vencimiento  -->
                                <label for="fechaVen" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha Venc:</label> <br/>
                                <input class="input-sm form-control" type="text" name="fechaVen" id="fechaVen" style="width:170px;" title="Fecha de vencimiento" placeholder="Fecha de vencimiento" value=""  required>  
                            </div>
                            <!--FUNCIONES DE VALIDACION FECHA-->
                            <script type="text/javascript">
                                $("#fecha").change(function()
                                {
                                    var tipComPal = $("#tipoComPtal").val();
                                    if(tipComPal==""){
                                        $("#escogerTipo").modal('show');
                                        $("#fechaVen").val("").focus();
                                        $("#fecha").val("").focus();
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
                                            $("#fechaVen").val("").focus();
                                            $("#fecha").val("").focus();
                                            } else {
                                                fecha1();
                                            }
                                        }
                                    });   
                                    }
                                });
                            </script>
                            <script>
                                function fecha1(){
                                    var tipComPal = $("#tipoComPtal").val();
                                    var fecha = $("#fecha").val();
                                    var num = $("#noDisponibilidad").val();
                                    //YA ESCOGIO MODIFICACION
                                    <?php if(!empty($modificacion)) {    ?>
                                    var disp =$("#solicitudAprobada").val();
                                    var idComPptal = $("#idModificacion").val();
                                    var form_data = { estruc: 22, tipComPal: tipComPal, fecha: fecha, num:num, idComPptal:idComPptal,sol:disp };
                                    <?php } else { 
                                    //SI ESCOGIO DISPONIBILIDAD
                                    if(!empty($registro)) { ?>
                                    
                                    var solici= $("#solicitudAprobada").val();
                                    var form_data = { estruc:21 , tipComPal: tipComPal, fecha: fecha, num:num, solicitud:solici };
                                    <?php } else { ?>
                                    ///SI ESTA VACIA
                                    var form_data = { estruc: 20, tipComPal: tipComPal, fecha: fecha, num:num };
                                    <?php } 
                                    }?>   
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
                            <div class="modal fade" id="escogerTipo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="forma-modal" class="modal-header">
                                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                                        </div>
                                        <div class="modal-body" style="margin-top: 8px">
                                            <p>Escoja un tipo de comprobante</p>
                                        </div>
                                        <div id="forma-modal" class="modal-footer">
                                            <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
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
                            <script type="text/javascript">
                                $('#AceptErrFec').click(function()
                                {
                                    $("#fecha").val("");
                                    $("#fechaVen").val("");
                                });
                            </script>   
                            <div class="col-sm-3" style="margin-top: 23px;" > <!-- Buscar disponibilidad -->
                                <table>
                                    <tr>
                                        <td>
                                            <div class="col-sm-3" style="margin-top: -25px; margin-left:-40px" > <!-- Buscar disponibilidad -->
                                                <label for="noDisponibilidad" class="control-label" style="margin-left:2px"><right>Buscar:</right></label>
                                                <select class="select2_single form-control" name="buscarDisp" id="buscarDisp" style="width:220px">
                                                    <option value="">Modificación</option>
                                                    <?php echo $reg = "SELECT
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
                                                    WHERE tcp.clasepptal = 20 AND tcp.tipooperacion !=1 AND tcp.vigencia_actual =1 
                                                    AND  cp.parametrizacionanno = $anno 
                                                    ORDER BY cp.numero DESC";
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
                                        </td>
                                    </tr>
                                </table>
                                <script>
                                    $("#buscarDisp").change(function(){
                                        var mod = $("#buscarDisp").val();
                                        document.location = 'MODIFICACION_APROBACION.php?mod='+md5(mod); 
                                    })
                                </script>
                            </div>
                        </div><!-- Fin Segunda fila -->
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <script src="js/select/select2.full.js"></script>
            <script>
              $(document).ready(function() {
                $(".select2_single").select2({

                  allowClear: true
                });
              });
            </script>
            <input type="hidden" id="idPrevio" value="">
            <input type="hidden" id="idActual" value="">
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
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza"><strong>Valor Disponible</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%"></th>
                                <th>Rubro</th>
                                <th>Fuente</th>
                                <th>Tercero</th>
                                <th>Valor</th>
                                <th>Valor Disponible</th>
                            </tr>
                            <tbody>
                            <?php 
                            if(!empty($registro)){
                               $datos = "SELECT dcp.id_unico, 
                                        rf.id_unico, 
                                        lower(c.nombre), 
                                        CONCAT(rp.codi_presupuesto,' - ', lower(rp.nombre)), 
                                        CONCAT(f.id_unico, ' - ', lower(f.nombre)) , dcp.valor , 
                                        IF(CONCAT_WS(' ',
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
                                        tr.apellidodos)) AS NOMBRE 
                                        FROM gf_detalle_comprobante_pptal dcp 
                                        LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
                                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                                        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                        LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero 
                                        WHERE dcp.comprobantepptal = $iddis";
                               $datos = $mysqli->query($datos);
                               if(mysqli_num_rows($datos)>0){
                                   while ($row3 = mysqli_fetch_row($datos)) { ?>
                            <tr>
                                <td class="oculto"></td>
                                <td width="7%"></td>
                                <td class="campos" align="left"><?php echo ucwords($row3[3])?></td>
                                <td class="campos" align="left"><?php echo ucwords($row3[4])?></td>
                                <td class="campos" align="left"><?php echo ucwords(mb_strtolower($row3[6]))?></td>
                                
                                <?php ########VERIFICAR SALDO DISPONIBLE########
                                #########TIPO DE OPERACION DEL COMPROBANTE##########
                                $tipoO ="SELECT tipooperacion FROM gf_tipo_comprobante_pptal "
                                        . "WHERE id_unico =".$_GET['tip'];
                                $tipoO =$mysqli->query($tipoO);
                                $tipoO = mysqli_fetch_row($tipoO);
                                $tipoO= $tipoO[0]; 
                                $saldoDisponible =0;
                                $afectado=0;
                                if($tipoO==3){
                                   $valorD = $row3[5];
                                     $afect = "SELECT dc.valor, tc.tipooperacion "
                                            . "FROM gf_detalle_comprobante_pptal dc "
                                            . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                            . "WHERE dc.comprobanteafectado = '$row3[0]'";
                                    $afect = $mysqli->query($afect);
                                    while ($af = mysqli_fetch_row($afect)) {
                                        $to = $af[1];
                                        if(($to == 2) || ($to == 4))
                                        {
                                                $valorD += $af[0];
                                        }
                                        elseif($to == 3)
                                        {
                                                $valorD -= $af[0];
                                        } 
                                        elseif($to == 1){
                                               $valorD -= $af[0];
                                        }

                                    }
                                    
                                    $saldoDisponible = $valorD; 
                                    $valor = $row3[5];
                                } else {
                                    if($tipoO==2){
                                      
                                    $valorDis ="SELECT DISTINCT dcp.id_unico, 
                                                dcp.comprobanteafectado ,dcp.valor, dca.valor 
                                                FROM gf_detalle_comprobante_pptal dcp 
                                                LEFT JOIN gf_detalle_comprobante_pptal dca 
                                                ON dcp.comprobanteafectado = dca.id_unico
                                                WHERE dcp.comprobantepptal = $iddis";
                                    $valorDis = $mysqli->query($valorDis);
                                    $valorDisp=0;
                                    $afectado =0;
                                    while($rowDetComp = mysqli_fetch_row($valorDis))
                                    {
                                        ####AFECTACIONES DISPONIBILIDAD######
                                        $valorD = $rowDetComp[3];
                                        $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                                                . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                                . "WHERE dc.comprobanteafectado = $rowDetComp[1] "
                                                . "and tc.tipooperacion !=1";
                                        $afr = $mysqli->query($afr);
                                        while($row4 = mysqli_fetch_row($afr)){
                                            if($row4[1]==3){
                                                $valorD =$valorD-$row4[0];
                                            } elseif($row4[1]==2){
                                                $valorD =$valorD+$row4[0];
                                            }
                                        }
                                        ####AFECTACIONES REGISTRO######
                                        $valorRep = $rowDetComp[2];
                                        $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                                                . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                                . "WHERE dc.comprobanteafectado = $rowDetComp[0] "
                                                . "and tc.tipooperacion !=1";
                                        $afr = $mysqli->query($afr);
                                        while($row4 = mysqli_fetch_row($afr)){
                                            if($row4[1]==3){
                                                $valorRep =$valorRep-$row4[0];
                                            } elseif($row4[1]==2){
                                                $valorRep =$valorRep+$row4[0];
                                            }
                                        }
                                        
                                        
                                        
                                        
                                        $saldoDis = $valorD-$valorRep;
                                        $valorDisp =$saldoDis;

                                    }
                                    $saldoDisponible = $valorDisp; 
                                    $valor = $row3[5];
                                    } 
                                    
                                }
                                ?>
                                <td class="campos" align="left"><?php echo number_format($valor, 2,'.',',')?></td>
                                <td class="campos" align="left"><?php echo number_format($saldoDisponible, 2, '.', ',')?></td>
                            </tr>   
                                <?php }
                               }
                            } else {
                                if(!empty($modificacion)){
                                 $datos = "SELECT dcp.id_unico, 
                                        rf.id_unico, 
                                        lower(c.nombre), 
                                        CONCAT(rp.codi_presupuesto,' - ', lower(rp.nombre)), 
                                        CONCAT(f.id_unico, ' - ', lower(f.nombre)) , 
                                        dcp.valor , 
                                        IF(CONCAT_WS(' ',
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
                                        tr.apellidodos)) AS NOMBRE , dcp.comprobanteafectado , dcpa.valor 
                                        FROM gf_detalle_comprobante_pptal dcp 
                                        LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
                                        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                                        LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico 
                                        LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico 
                                        LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico 
                                        LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero 
                                        LEFT JOIN gf_detalle_comprobante_pptal dcpa ON dcpa.id_unico = dcp.comprobanteafectado 
                                        WHERE dcp.comprobantepptal = $idModificacion";
                               $datos = $mysqli->query($datos);
                               if(mysqli_num_rows($datos)>0){
                                   while ($row3 = mysqli_fetch_row($datos)) { ?>
                            <tr>
                                <td class="oculto"></td>
                                <td width="7%">
                                    <div class="modElim" style="z-index: 10;">
                                        <a class="eliminar" href="#<?php echo $row3[0];?>" onclick="javascript:eliminarDetComp(<?php echo $row3[0]; ?>)">
                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        <a class="modificar" href="#<?php echo $row3[0];?>" onclick="javascript:modificarDetComp(<?php echo $row3[0]; ?>)">
                                            <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="campos" align="left"><?php echo ucwords($row3[3])?></td>
                                <td class="campos" align="left"><?php echo ucwords($row3[4])?></td>
                                <td class="campos" align="left"><?php echo ucwords(mb_strtolower($row3[6]))?></td>
                                
                                <?php ########VERIFICAR SALDO DISPONIBLE########
                                #########TIPO DE OPERACION DEL COMPROBANTE##########
                                $tipoO ="SELECT tipooperacion FROM gf_tipo_comprobante_pptal "
                                        . "WHERE id_unico =".$idTipo;
                                $tipoO =$mysqli->query($tipoO);
                                $tipoO = mysqli_fetch_row($tipoO);
                                $tipoO= $tipoO[0]; 
                                $saldoDisponible =0;
                                $afectado=0;
                                if($tipoO==3){
                                    $valorD = $row3[8];
                                    $afect = "SELECT dc.valor, tc.tipooperacion, dcpa.valor "
                                            . "FROM gf_detalle_comprobante_pptal dc "
                                            . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                            . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                            . "LEFT JOIN gf_detalle_comprobante_pptal dcpa ON dcpa.id_unico = dc.comprobanteafectado "
                                            . "WHERE dc.comprobanteafectado = '$row3[7]'";
                                    $afect = $mysqli->query($afect);
                                    while ($row4 = mysqli_fetch_row($afect)) {
                                        if(($row4[1] == 2) || ($row4[1] == 4) )
                                        {
                                                $valorD += $row4[0];
                                        }
                                        elseif($row4[1] == 3 || ($row4[1] == 1))
                                        {
                                                $valorD -= $row4[0];
                                        } 
                                        

                                    }
                                    $valorDisp = $valorD;
                                    $saldoDisponible = $valorDisp; 
                                    $valor = $row3[5];
                                } else {
                                    if($tipoO==2){
                                    ###########VALOR REGISTRO###########
                                    $valorDis ="SELECT DISTINCT dcp.id_unico, 
                                                dcp.comprobanteafectado ,dcp.valor, dca.valor 
                                                FROM gf_detalle_comprobante_pptal dcp 
                                                LEFT JOIN gf_detalle_comprobante_pptal dca 
                                                ON dcp.comprobanteafectado = dca.id_unico
                                                WHERE dcp.id_unico = $row3[7]";
                                    $valorDis = $mysqli->query($valorDis);
                                    $valorDisp=0;
                                    $afectado =0;
                                    $valorD=0;
                                    $valorRep=0;
                                    while($rowDetComp = mysqli_fetch_row($valorDis))
                                    {
                                        ####AFECTACIONES DISPONIBILIDAD######
                                        $valorD = $rowDetComp[3];
                                        $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                                                . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                                . "WHERE dc.comprobanteafectado = $rowDetComp[1] "
                                                . "and tc.tipooperacion !=1";
                                        $afr = $mysqli->query($afr);
                                        while($row4 = mysqli_fetch_row($afr)){
                                            if($row4[1]==3){
                                                $valorD =$valorD-$row4[0];
                                            } elseif($row4[1]==2){
                                                $valorD =$valorD+$row4[0];
                                            }
                                        }
                                        ####AFECTACIONES REGISTRO######
                                        $valorRep = $rowDetComp[2];
                                        $afr = "SELECT dc.valor, tc.tipooperacion FROM gf_detalle_comprobante_pptal dc "
                                                . "LEFT JOIN gf_comprobante_pptal cp ON dc.comprobantepptal = cp.id_unico "
                                                . "LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico "
                                                . "WHERE dc.comprobanteafectado = $rowDetComp[0] "
                                                . "and tc.tipooperacion !=1";
                                        $afr = $mysqli->query($afr);
                                        while($row4 = mysqli_fetch_row($afr)){
                                            if($row4[1]==3){
                                                $valorRep =$valorRep-$row4[0];
                                            } elseif($row4[1]==2){
                                                $valorRep =$valorRep+$row4[0];
                                            }
                                        }
                                        
                                        
                                        
                                        
                                        $saldoDis = $valorD-$valorRep;
                                        $valorDisp =$saldoDis;

                                    }
                                    $saldoDisponible = $valorDisp; 
                                    $valor = $row3[5];
                                    
                                     
                                    }
                                }
                                $comparar= $saldoDisponible+$valor;
                                ?>
                                <td class="campos" align="left">
                                  
                                    <div id="divVal<?php echo $row3[0];?>" >
                                        <?php  
                                          echo number_format($row3[5], 2, '.', ',');
                                        ?>
                                    </div>
                                    <table id="tab<?php echo $row3[0];?>" style="padding: 0px;  margin-top: -10px; margin-bottom: -10px;" >
                                        <tr>
                                          <td>
                                            <input type="text" name="valorMod" id="valorMod<?php echo $row3[0];?>" class="fo9rm-control in9put-sm" maxlength="50" style="width:100px; margin-top: -5px; margin-bottom: -5px; " placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valorMod<?php echo $row3[0];?>', '2');" onkeyup="formatC('valorMod<?php echo $row3[0];?>');" value="<?php echo number_format($row3[5], 2, '.', ','); ?>" required>
                                          </td>
                                          <td>
                                            <a href="#<?php echo $row3[0];?>" onclick="javascript:verificarValor('<?php echo $row3[0];?>','<?php echo $comparar;?>');" >
                                              <i title="Guardar Cambios" class="glyphicon glyphicon-floppy-disk" ></i>
                                            </a>
                                          </td>
                                          <td>
                                            <a href="#<?php echo $row3[0];?>" onclick="javascript:cancelarModificacion(<?php echo $row3[0];?>);" >
                                              <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                            </a>
                                          </td>
                                        </tr>
                                    </table>
                                    <script type="text/javascript">
                                        var id = "<?php echo $row3[0];?>";                       
                                        var idValorM = 'valorMod'+id;
                                        var idTab = 'tab'+id;
                                        $("#"+idTab).css("display", "none");
                                    </script>
                                </td>
                                
                                <td class="campos" align="left">
                                    <?php echo number_format($saldoDisponible, 2, '.', ',')?>
                                </td>
                            </tr>   
                                <?php }
                               }   
                                    
                                    
                                    
                                } else {
                                    
                                }
                            }
                            
                            ?>
                        </tbody>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div> <!-- table-responsive contTabla -->
            </div> <!-- Cierra clase table-responsive contTabla  -->
        </div> <!-- Cierra clase col-sm-10 text-left -->
    </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->

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
<script>
        $("#ver1").click(function(){
            document.location.reload();
        })
</script>
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
<script>
        $("#ver2").click(function(){
            document.location.reload();
        })
</script>
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
<script>
        $("#btnModificarConf").click(function(){
            document.location.reload();
        })
</script>
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
<script>
        $("#btnModificarFall").click(function(){
            document.location.reload();
        })
</script>

<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<?php require_once 'footer.php'; ?>
<script type="text/javascript">
function eliminarDetComp(id){
    var result = '';
        $("#myModal").modal('show');
        $("#ver").click(function(){
            $("#mymodal").modal('hide');
            var form_data = { action: 2, id:id};                              
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_modificacion_disponibilidadRegJson.php",
                data: form_data,
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
function modificarDetComp(id){
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
  function guardarModificacion(id) 
  {
    var idCampoValor = 'valorMod'+id;
    var valor = $("#"+idCampoValor).val();
    valor = valor.replace(/\,/g,''); 
    if( ($("#"+idCampoValor).val() == "") || ($("#"+idCampoValor).val() == 0))
    { 
        $("#ModificacionNoValida").modal('show');
    }
    else
    {
        var form_data = { action:3, id: id, valor: valor};
        $.ajax({
            type: "POST",
            url: "jsonPptal/gf_modificacion_disponibilidadRegJson.php",
            data: form_data,
            success: function(response)
            {
                if(response == 1)
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
<script type="text/javascript">
function verificarValor(id,comparacion)
{
    var idValMod = "valorMod"+id;
    var validar = $("#"+idValMod).val();
    validar = parseFloat(validar.replace(/\,/g,'')); //Elimina la coma que separa los miles.
    var valOriginal = parseFloat(comparacion.replace(/\,/g,''));

    if((isNaN(validar)) || (validar == 0) || (validar == ""))
    {
        $("#myModalAlertModInval").modal('show');
    }
    else if(validar > valOriginal)
    {
        $("#myModalAlertMod").modal('show');
    }
    else
    {
    guardarModificacion(id);

    } 
}
</script>
<script type="text/javascript">
  function valida()
  {
    if($("#fechaVen").val() == "" || $("#fecha").val() == "" ||$("#tipoComPtal").val() == "")
    {
      $("#validarMod").modal('show');
      return false;
    }
    
    return true;

  }
</script>
<div class="modal fade" id="validarMod" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Datos Incompletos, Valide Nuevamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnvalidarMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
</body>

<?php 
####################VALIDACION CIERRE######################
if(!empty($modificacion)){
    $cierre = cierre($idModificacion);
    if($cierre ==1){ ?> 
        <script>
            $("#btnGuardarComp").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false);     
            $("#btnModificar").prop("disabled", true);
            $("#btnAgregarDis").prop("disabled", true);
            $(".eliminar").css('display','none');
            $(".modificar").css('display','none');
            
        </script>
    <?php } else { 
        $num = detallesnumpptal($idModificacion);
        if($num>0){ ?>
        <script>
            $("#btnGuardarComp").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false);     
            $("#btnModificar").prop("disabled", false); 
            $(".AgregarDis").css('display','none');
            $("#btnAgregarDis").prop("disabled", true);
            
        </script>
        <?php } else { 
        ?>
        <script>
            $("#btnGuardarComp").prop("disabled", true);
            $("#btnImprimir").prop("disabled", false);     
            $("#btnModificar").prop("disabled", false); 
            $(".AgregarDis").css('display','block');
            $("#btnAgregarDis").prop("disabled", false);
        </script>
    <?php } }
} else { 
    if(!empty($disponibilidad)) { ?>
    <script>
       $("#btnGuardarComp").prop("disabled", false);     
       $("#btnImprimir").prop("disabled", true);     
       $("#btnModificar").prop("disabled", true);     
       $("#btnAgregarDis").prop("disabled", true);
        $(".AgregarDis").css('display','none');
    </script>
    <?php } else { ?>
    <script>
       $("#btnGuardarComp").prop("disabled", false);     
       $("#btnImprimir").prop("disabled", true);     
       $("#btnModificar").prop("disabled", true); 
       $("#btnAgregarDis").prop("disabled", true);
        $(".AgregarDis").css('display','none');
    </script>    
        
    <?php } } ?>
</html>