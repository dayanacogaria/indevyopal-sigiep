<?php

#01/03/2017 --- Nestor B --- se modifico el método que modifica la fecha al formato dd/mm/aaaa 
#01/03/2017 --- Nestor B --- se modificó el botón de "atras" y la función strtolower para que tome las tildes 
#03/03/2017 --- Nestor B --- se modificó el formulario de listar accidente para que coincida con los margenes de registrar
#11/03/2017 --- Nestor B --- se modificó la altura del botón atrás y del título de informacion adicional 
#14/07/2017 --- Nestor B --- se agregaron validaciones para hacer responsive el formulario
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
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
    ?>
    <input type="hidden" name="txtId" value="<?php echo $idT?>">
    <?php
    $tercero = $datosTercero;
    $a="inline-block";
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>

<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltEmpleado-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body{
        font-size: 11px;
    }
    
   /* Estilos de tabla*/
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
       
        
        $("#sltFechaR").datepicker({changeMonth: true,}).val(fecAct);
        
        
});
</script>
        <title>Registrar Accidente</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Accidente</h2>
                    <a href="<?php echo 'listar_GN_ACCIDENTE.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarAccidenteJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                            <!----------------------------------------------------------------------------------------------------------------------->
                            
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <?php 
                                        if(empty($idT))
                                        {
                                            $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                                            $idTer = "";
                                        }
                                        else
                                        {
                                            $emp = "SELECT 						
                                                        e.id_unico,
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
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                            			echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    <!----------------------------------------------------------------------->
                            
                                    <label for="lugaraccidente" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Lugar:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtLugar" id="txtLugar" step="1" placeholder="Lugar">
                            
                                    <label for="txtDiagnostico" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Diagnóstico:</label>                                
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtDiagnostico" id="txtDiagnostico" step="1" placeholder="Diagnóstico">
                                </div>
                                
                                <!----------------------------------------------------------------------------------------------------->                              
                        
                                <div class="form-group form-inline" style="margin-top:-15px">
                            
                                    <label for="txtNumeroR" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Número Radicado:</label>
                                    <input style="width:15%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtNumeroR" id="txtNumeroR" step="1" placeholder="Número Radicado">
                            
                                    <!----------Script para invocar Date Picker-->
                            
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        }); 
                                    </script>
                            
                                    <label for="txtDescripcion" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Descripción:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtDescripcion" id="txtDescripcion" step="1" placeholder="Descripción">                            
                            
                                    <label for="sltRuta" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Ruta Reporte:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtRuta" id="txtRuta" step="1" placeholder="Ruta Reporte">
                                </div>
                                
                                <!---------------------------------------------------------------------------------------------------->                        
                                <!---------------------------------------------------------------------------------------------------->                              
                        
                                <div class="form-group form-inline" style="margin-top:-15px">
                            
                                    <label for="sltFechaR" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha:</label>
                                    <input style="width:15%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaR" id="sltFechaR" step="1" value="<?php echo date("Y-m-d");?>">                            
                            
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Tipo:</label>
                                    <?php 
                                        $ta = "SELECT id_unico, nombre FROM gn_tipo_accidente";
                                        $tac = $mysqli->query($ta);
                                    ?>
                                    <select name="sltTipo" id="sltTipo" title="Seleccione Tipo" style="width: 14%; height: 32px;" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="">Tipo</option>
                                        <?php 
                                            while($rowTA = mysqli_fetch_row($tac))
                                            {
                            			echo "<option value=".$rowTA[0].">".$rowTA[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>                                                        
                            
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Estado:</label>
                                    <?php 
                                        $est = "SELECT id_unico, nombre FROM gn_estado_accidente";
                                        $test = $mysqli->query($est);
                                    ?>
                                    <select name="sltEstado" id="sltEstado" title="Seleccione Estado" style="width: 14%; height: 32px;" class="form-control col-sm-2">
                                        <option value="">Estado</option>
                                        <?php 
                                            while($rowT = mysqli_fetch_row($test))
                                            {
                            			echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>                                                        
                            
                                    <label for="No" class="col-sm-10 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra col-sm-12" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                </div>
                            </div>
                        </form>    
                    </div>
                </div>    
                
                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-22px">
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">EMPLEADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_ACCIDENTE.php">ESTADO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_ACCIDENTE.php">TIPO</a>
                                </td>
                            </tr>
                        </tbody>    
                    </table>
                </div>
                
                <!---------------------------------------------------------------------------------------------------->                        
                
                <div class="form-group form-inline" style="margin-top:5px; ">
                    <?php require_once './menu.php'; 
                        
                    if(!empty($idTer)){
                        $sql = "SELECT          a.id_unico,
                                                a.lugaraccidente,
                                                a.diagnostico,
                                                a.empleado,
                                                e.id_unico,
                                                e.tercero,
                                                t.id_unico,
                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                a.tipoaccidente,
                                                ta.id_unico,
                                                ta.nombre,
                                                a.estado,
                                                ea.id_unico,
                                                ea.nombre,
                                                a.numradicado,
                                                a.fechareporte,
                                                a.descripcion,
                                                a.rutareporte
                                FROM gn_accidente a	 
                                LEFT JOIN	gn_empleado e           ON a.empleado      = e.id_unico
                                LEFT JOIN   gf_tercero t            ON e.tercero       = t.id_unico
                                LEFT JOIN   gn_tipo_accidente ta    ON a.tipoaccidente = ta.id_unico
                                LEFT JOIN   gn_estado_accidente ea  ON a.estado        = ea.id_unico
                                WHERE a.empleado = $idTer";
                        
                        $resultado = $mysqli->query($sql);
                        $nres = mysqli_num_rows($resultado);
                    }else{
                        $nres = 0;
                    }    
                ?>
                
                    <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 5px;">
                        <div class="table-responsive">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <!-- Actualización 24 / 02 09:29: No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Lugar</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>No. Radicado</strong></td>
                                        <td class="cabeza"><strong>Diagnóstico</strong></td>
                                        <td class="cabeza"><strong>Descripción</strong></td>
                                        <td class="cabeza"><strong>Fecha Reporte</strong></td>
                                        <td class="cabeza"><strong>Ruta Reporte</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <!-- Actualización 24 / 02 09:29: No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Lugar</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">No. Radicado</th>
                                        <th class="cabeza">Diagnóstico</th>
                                        <th class="cabeza">Descripción</th>
                                        <th class="cabeza">Fecha Reporte</th>
                                        <th class="cabeza">Ruta Reporte</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        
                                        if($nres > 0){
                                    
                                            while ($row = mysqli_fetch_row($resultado)) { 
                                            
                                                $afr = $row[15];
                                                if(!empty($row[15])||$row[15]!=''){
                                            
                                                    $afr = trim($afr, '"');
                                                    $fecha_div = explode("-", $afr);
                                                    $anior = $fecha_div[0];
                                                    $mesr = $fecha_div[1];
                                                    $diar = $fecha_div[2];
                                                    $afr = $diar.'/'.$mesr.'/'.$anior;
                                                }else{
                                                    $afr='';
                                                }
                                        
                                                $aid    = $row[0];
                                                $alug   = $row[1];
                                                $adia   = $row[2];
                                                $aemp   = $row[3];
                                                $empid  = $row[4];
                                                $empter = $row[5];
                                                $terid  = $row[6];
                                                $ternom = $row[7];
                                                $atip   = $row[8];
                                                $taid   = $row[9];
                                                $tanom  = $row[10];
                                                $aest   = $row[11];
                                                $eatid  = $row[12];
                                                $eatnom = $row[13];
                                                $anumr  = $row[14];
                                                #$afr    = $row[15];
                                                $ades   = $row[16];
                                                $arut   = $row[17];

                                    ?>
                                                <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                        </a>
                                                        <a href="modificar_GN_ACCIDENTE.php?id=<?php echo md5($row[0]);?>">
                                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                        </a>
                                                    </td>
                                                    <!-- Actualización 24 / 02 09:29: No es necesario mostrar el nombre del empleado
                                                    <td class="campos"><?php #echo $ternom?></td>                
                                                    -->
                                                    <td class="campos"><?php echo $alug?></td>                
                                                    <td class="campos"><?php echo $tanom?></td>                
                                                    <td class="campos"><?php echo $eatnom?></td>                
                                                    <td class="campos"><?php echo $anumr?></td>                
                                                    <td class="campos"><?php echo $adia?></td>                
                                                    <td class="campos"><?php echo $ades?></td>                
                                                    <td class="campos"><?php echo $afr?></td>                
                                                    <td class="campos"><?php echo $arut?></td>                
                                                </tr>
                                    <?php   }
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
                            <p>¿Desea eliminar el registro seleccionado de Accidente?</p>
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
                            <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
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
                            url:"json/eliminarAccidenteJson.php?id="+id,
                            success: function (data) {
                                result = JSON.parse(data);
                                if(result==true)
                                {
                                    $("#myModal1").modal('show');
                                }
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
            
            <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
            
                $("#sltEstado").select2();
                $("#sltEmpleado").select2();
                $("#sltTipo").select2();
       
            </script>
        </div>
</body>
</html>
    