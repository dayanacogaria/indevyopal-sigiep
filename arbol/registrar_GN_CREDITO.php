<?php

#01/03/2017 --- Nestor B --- se modifico el método que modifica la fecha al formato dd/mm/aaaa 
#01/03/2017 --- Nestor B --- se modificó el botón de "atras" y la función strtolower para que tome las tildes 
#03/03/2017 --- Nestor B --- se modificó el formulario de listar accidente para que coincida con los margenes de registrar
#04/03/2017 --- Nestor B --- se modificó la funcion del datepicker para que no muestre la fecha del pc por defecto y se agregaron librerías para la busqueda rápida en los selects tipo de crédito y entindad 
#11/03/2017 --- Nestor B --- se modificó la altura del botón atrás y del título de informacion adicional
#06/06/2017 --- Nestor B --- se agregó el campo de concepto, se modifico la consulta que muestra los creditos registrados para el empleado y se modifico el campo de periodo inicial
#16/06/2017 --- Nestor B --- se agregó la validacion de los selects cuanbdo son requeridos y no se se selecciona una opción 
#13/07/2017 --- Nestor B --- se agregaron validaciones para hacer responsive el formulario
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();

$paranno = $_SESSION['anno'];
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno, ' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
if(empty($idT))
{
    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    $a="inline-block";
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>


<style >
    label #sltEmpleado-error, #sltTipo-error, #sltConcepto-error, #sltEntidad-error, #sltPeriodo-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body{
        font-size: 11px;
    }
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
<script>


$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

<script src="js/jquery-ui.js"></script>
<script>

        $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
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
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#Fecha").datepicker({changeMonth: true,}).val();
        $("#sltPeriodo").datepicker({changeMonth: true}).val();
        
        
});
</script>
   <title>Registrar Crédito</title>
  
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Crédito</h2>
                        <a href="<?php echo 'listar_GN_CREDITO.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                            <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                            <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                                <form name="form" class="form-horizontal" method="POST" id="form"  enctype="multipart/form-data" action="json/registrarCreditoJson.php">
                                    <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                                    <!------------------------------------------------------------------------------------------------------------------------>
                                    <div class="colsm-12 col-md-12 col-lg-12">
                                        <div class="form-group form-inline" style="margin-top:-25px">
                                            <?php 
                                                if(empty($idT))
                                                {
                                                    $emp = "SELECT  e.id_unico,
                                                                    e.tercero,
                                                                    t.id_unico,
                                                                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                            FROM gn_empleado e
                                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                                                    $idTer = "";
                                                }
                                                else
                                                {
                                                    $emp = "SELECT  e.id_unico,
                                                                    e.tercero,
                                                                    t.id_unico,
                                                                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                            FROM gn_empleado e
                                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                                                    $idTer = $idT;
                                                }
                                            
                                                $empleado = $mysqli->query($emp);
                                            ?>
                            
                                            <label for="sltEmpleado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                            <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                                <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                                <?php 
                                                    while($rowE = mysqli_fetch_row($empleado))
                                                    {
                                                        echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                                    }
                                                ?>                            	                           	
                                            </select>
                                            <!------------------------------------------------------------------------>
                                        
                                            <label for="txtNumeroC" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>No. Crédito:</label>
                                            <input required="required" style="width:14%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtNumeroC" id="txtNumeroc" placeholder="Número Crédito" onkeypress="return txtValida(event,'num')">
                                        
                                            <label for="sltEntidad" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Entidad:</label>                                
                                            <select name="sltEntidad" id="sltEntidad" title="Seleccione Entidad" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2" required="required">
                                                <?php                             	
                                                    $ter = "SELECT  t.id_unico,
                                                                    t.razonsocial
                                                            FROM gf_perfil_tercero pt 
                                                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                                                            WHERE pt.perfil = 12";
                                                
                                                    $tercero = $mysqli->query($ter);
                                                    echo "<option value=''>Entidad</option>";                            		
                                                    while($rowE = mysqli_fetch_row($tercero)){
                                                        echo "<option value=".$rowE[0].">".ucwords(strtolower($rowE[1]))."</option>";
                                                    }                            	
                                                ?>                            	                           	
                                            </select>
                          
                                        </div>
                                        <!---------------------------------------------------------------------------------------------------->                              
                                    
                                        <div class="form-group form-inline" style="margin-top:-15px">
                            
                                            <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo:</label>
                                                <?php 
                                                    $af = " SELECT id_unico, nombre
                                                            FROM gn_tipo_proceso_nomina";
                                            
                                                    $afil = $mysqli->query($af);
                                                ?>
                                            <select name="sltTipo" id="sltTipo" title="Seleccione Tipo" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2" required="required">
                                                <option value="">Tipo</option>
                                                <?php 
                                                    while($rowT = mysqli_fetch_row($afil))
                                                    {
                                                        echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                                    }
                                                ?>
                                            </select>
                                            <!----------Script para invocar Date Picker-->
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $("#datepicker").datepicker();
                                                });
                                            </script>
                            
                                            <label for="Fecha" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha:</label>
                            
                                            <input style="width: 14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="Fecha" id="Fecha" placeholder="Ingrese la fecha">                            
                            
                                            <label for="sltPeriodo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Período Inicial:</label>
                                            <?php
                                                $perIni = "SELECT p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico WHERE parametrizacionanno = $paranno AND liquidado = 0 AND tpn.id_unico = 1";
                                                $perinic = $mysqli->query($perIni);
                                
                                            ?>
                                            <select name="sltPeriodo" id="sltPeriodo" title="Selecione Periodo" style="width: 14%; height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2" required="required">
                                                <option value="">Periodo</option>
                                                <?php
                                                    while($perinicial= mysqli_fetch_row($perinic)){
                                                        echo "<option value=".$perinicial[0].">".$perinicial[1]."</option>";
                                                    }
                                                ?>
                                            </select>
                                            <!-- <input style="width:140px" class="col-sm-2 input-sm" type="text" name="sltPeriodo" id="sltPeriodo" step="1" placeholder="Ingrese la fecha">  -->                          
                            
                                        </div>                     
                                        <!---------------------------------------------------------------------------------------------------->                              
                                        
                                        <div class="form-group form-inline" style="margin-top:-15px">
                            
                                            <label for="txtValorCr" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Valor:</label>
                                            <input onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtValorCr');" style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtValorCr" id="txtValorCr"  placeholder="Valor Crédito">
                            
                                            <label for="txtNCuotas" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Cuotas:</label>
                                            <input onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtNCuotas');" style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="number" name="txtNCuotas" id="txtNCuotas" placeholder="No. Cuotas" >
                            
                                            <label for="txtValorCu" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Valor Cuota:</label>
                                            <input onkeypress="return txtValida(event,'num', 'valor', '2');" onkeyup="formatC('txtValorCu');" style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtValorCu" id="txtValorCu"  placeholder="0,00">

                                            <?php

                                                #consulta los conceptos registrados
                                                $concepto = "SELECT id_unico,CONCAT(codigo,' - ',descripcion) FROM gn_concepto ORDER BY id_unico ASC";
                                                $conc = $mysqli->query($concepto);

                                            ?>
                                            
                                        </div>
                                        <div class="form-group form-inline" style="margin-top:-15px">
                                            <label for="sltConcepto" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Concepto:</label>
                                            <select name="sltConcepto" id="sltConcepto" title="Seleccione Concepto" style="width: 18%;height: 30px" class="form-control col-sm-1 col-md-2 col-lg-2" required="required">
                                                <option value="">Concepto</option>

                                                <?php
                                                    while($con = mysqli_fetch_row($conc)){

                                                        echo "<option value=".$con[0].">".$con[1]."</option>";
                                                    } 
                                                ?>
                                            </select>
                                        
                                            <label for="No" class="col-sm-5 control-label"></label>
                                            <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                        </div>
                                    </div>    
                                </form>    
                            </div>
                </div>    
                            
                <div class="col-sm-8 col-sm-2" style="margin-top:-23px">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_CONCEPTO.php">CONCEPTO</a>
                                </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_TERCERO_ENTIDAD_FINANCIERA.php">ENTIDAD</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_PROCESO_NOMINA.php">TIPO</a>
                                    </td>
                                </tr>
                        </tbody>    
                    </table>
                </div>
                <!---------------------------------------------------------------------------------------------------->                        
                <div class="form-group form-inline" style="margin-top:5px;" >
                            
                    <?php 
                        require_once './menu.php'; 
                        
                        if(!empty($idTer)){
                        
                            $sql = "SELECT  c.id_unico,
                                                    c.empleado,
                                                    e.id_unico,
                                                    e.tercero,
                                                    t.id_unico,
                                                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                    c.tipoproceso,
                                                    tpn.id_unico,
                                                    tpn.nombre,
                                                    c.entidad,
                                                    ter.id_unico,
                                                    ter.razonsocial,
                                                    c.numerocredito,
                                                    c.fecha,
                                                    c.periodoinicia,
                                                    c.valorcredito,
                                                    c.numerocuotas,
                                                    c.valorcuota,
                                                    c.periodoinicia,
                                                    p.id_unico,
                                                    p.codigointerno,
                                                    c.concepto,
                                                    co.id_unico,
                                                    co.descripcion
                                            FROM gn_credito c	 
                                            LEFT JOIN	gn_empleado e               ON c.empleado = e.id_unico
                                            LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                                            LEFT JOIN   gn_tipo_proceso_nomina tpn  ON c.tipoproceso = tpn.id_unico
                                            LEFT JOIN   gf_tercero ter              ON c.entidad = ter.id_unico
                                            LEFT JOIN   gn_periodo p                ON c.periodoinicia = p.id_unico
                                            LEFT JOIN   gn_concepto co              ON c.concepto = co.id_unico
                                            WHERE c.empleado = $idTer";
                                    $resultado = $mysqli->query($sql);
                                    $nres = mysqli_num_rows($resultado);
                        }else{
                            $nres = 0;
                        }    
                    ?>
                    <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top:5px;" >
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <!-- Actualización 24 / 02 09:42: No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Entidad</strong></td>
                                        <td class="cabeza"><strong>Concepto</strong></td>
                                        <td class="cabeza"><strong>Tipo Proceso</strong></td>
                                        <td class="cabeza"><strong>No. Crédito</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Valor Crédito</strong></td>
                                        <td class="cabeza"><strong>Período Inicial</strong></td>
                                        <td class="cabeza"><strong>No. Cuotas</strong></td>
                                        <td class="cabeza"><strong>Valor Cuota</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza"   style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <!-- Actualización 24 / 02 09:43: No es necesario mostrar el nombre del empleado
                                        <th class="cabeza"  >Empleado</th>
                                        -->
                                        <th class="cabeza"  >Entidad</th>
                                        <th class="cabeza"  >Concepto</th>
                                        <th class="cabeza"  >Tipo Proceso</th>
                                        <th class="cabeza"  >No. Crédito</th>
                                        <th class="cabeza"  >Fecha</th>
                                        <th class="cabeza"  >Valor Crédito</th>
                                        <th class="cabeza"  >Período Inicial</th>
                                        <th class="cabeza"  >No. Cuotas</th>
                                        <th class="cabeza"  >Valor Cuota</th>
                                    </tr>
                                    
                                </thead>    
                                <tbody>
                                    <?php 
                                    if($nres > 0){
                                        while ($row = mysqli_fetch_row($resultado)) { 
                                       
                                            $cfec = $row[13];
                                            if(!empty($row[13])||$row[13]!=''){
                                        
                                                $cfec = trim($cfec, '"');
                                                $fecha_div = explode("-", $cfec);
                                                $aniof = $fecha_div[0];
                                                $mesf = $fecha_div[1];
                                                $diaf = $fecha_div[2];
                                                $cfec = $diaf.'/'.$mesf.'/'.$aniof;
                                            }else{
                                                $cfec='';
                                            }

                                            $cid   = $row[0];
                                            $cemp  = $row[1];
                                            $eid   = $row[2];
                                            $eter  = $row[3];
                                            $tid1  = $row[4];
                                            $ter1  = $row[5];
                                            $ctip  = $row[6];
                                            $tpid  = $row[7];
                                            $tpnom = $row[8];
                                            $cent  = $row[9];
                                            $tid2  = $row[10];
                                            $ter2  = $row[11];
                                            $cncr  = $row[12];
                                            #$cfec  = $row[13];
                                            #$cper  = $row[14];
                                            $cval  = $row[15];
                                            $cncu  = $row[16];
                                            $cvcu  = $row[17];
                                            $peri  = $row[20];
                                            $descon  = $row[23];
                                    ?>
                                            <tr>
                                                <td style="display: none;"><?php echo $row[0]?></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                    <a href="modificar_GN_CREDITO.php?id=<?php echo md5($row[0]);?>">
                                                        <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                    </a>
                                                </td>
                                                <!-- Actualización 24 / 02 09:35: No es necesario mostrar el nombre del empleado
                                                <td class="campos"><?php #echo $ter1?></td>                
                                                -->
                                                <td class="campos"><?php echo $ter2?></td>
                                                <td class="campos" style="font-size: 10px"><?php echo $descon?></td>
                                                <td class="campos"><?php echo $tpnom?></td>                
                                                <td class="campos"><?php echo $cncr?></td>                
                                                <td class="campos"><?php echo $cfec?></td>                
                                                <td class="campos"><?php echo number_format($cval, 2, '.', ',');?></td>                
                                                <td class="campos"><?php echo $peri?></td>                
                                                <td class="campos"><?php echo $cncu?></td>                
                                                <td class="campos"><?php echo number_format($cvcu, 2, '.', ','); ?></td>           
                                            </tr>
                                  <?php }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
       
                </div>
            </div>    
                                                    
        </div>
        
        <div>
            <?php require_once './footer.php'; ?>
            <div class="modal fade" id="myModal" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>¿Desea eliminar el registro seleccionado de Crédito?</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModal1" role="dialog" align="center">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>Información eliminada correctamente.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                            <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--Script que dan estilo al formulario-->

            <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            <script type="text/javascript">
                function eliminar(id)
                {
                    var result = '';
                    $("#myModal").modal('show');
                    $("#ver").click(function(){
                        $("#mymodal").modal('hide');
                        $.ajax({
                            type:"GET",
                            url:"json/eliminarCreditoJson.php?id="+id,
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
                function recargar()
                {
                    window.location.reload();     
                }
            </script>     
    <!--Actualiza la página-->
            <script type="text/javascript">
    
                $('#ver1').click(function(){ 
                    reload();
                    //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
                    //window.location='../listar_GN_ACCIDENTE.php';
                    window.history.go(-1);        
                });
    
            </script>

            <script type="text/javascript">    
                $('#ver2').click(function(){
                    window.history.go(-1);
                });    
            </script>
        </div>
        
        
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#sltTipo").select2();
            $("#sltEmpleado").select2();
            $("#sltConcepto").select2();
            $("#sltPeriodo").select2();
            $("#sltEntidad").select2();
        </script>
    </body>
</html>
