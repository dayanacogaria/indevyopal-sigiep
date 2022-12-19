<?php
#03/03/2017 --- Nestor B --- se modificó el formulario de listar accidente para que coincida con los margenes de registrar
#04/03/2017 --- Nestor B --- se modificó la funcion del datepicker para que no muestre la fecha del pc por defecto y se agregaron librerías para la busqueda rápida en los selects de tipo e institucion   
#10/03/2017 --- Nestor B --- se modificaron el alto de el botón atrar y el titulo de informacipón adicional para que cuadre con el subtitulo y el titulo respectivamente
#13/07/2017 --- Nestor B --- se agregaron validaciones para hacer responsive el formulario

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
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
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>
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
       
        
        $("#sltFechaT").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Registrar Estudio</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Estudio</h2>
                    <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEstudioJson.php">
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
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                                                echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    <!----------------------------------------------------------------------->
                                
                                    <label for="txtTitulo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Título:</label>                                
                                    <input required="required" style="width:18%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtTitulo" id="txtTitulo" placeholder="Título">
                                
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>I. Educativa:</label>
                                    <?php 
                                        $in = "SELECT id_unico, nombre FROM gn_institucion_educativa ORDER BY id_unico ASC";
                                        $ined = $mysqli->query($in);
                                    ?>
                                    <select required="required" name="sltInstE" id="sltInstE" title="Seleccione Institución Educativa" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="">Institución Educativa</option>
                                        <?php 
                                            while($rowIE = mysqli_fetch_row($ined))
                                            {
                                                echo "<option value=".$rowIE[0].">".$rowIE[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>                        
                            
                                </div>
                          
                                <!----------------------------------------------------------------------------------------------------->                              
                                <div class="form-group form-inline" style="margin-top:-15px">
                            
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo:</label>
                                        <?php 
                                            $es = "SELECT id_unico, nombre FROM gn_tipo_estudio ORDER BY id_unico ASC";
                                            $est = $mysqli->query($es);
                                        ?>
                                    <select required="required" name="sltTipo" id="sltTipo" title="Seleccione Tipo" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="">Tipo</option>
                                        <?php 
                                            while($rowTE = mysqli_fetch_row($est))
                                            {
                                                echo "<option value=".$rowTE[0].">".$rowTE[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                            
                                    <label for="FechaT" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>F. Terminación:</label>
                                    <input style="width: 18%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaT" id="sltFechaT" step="1" placeholder="Ingrese la fecha">
                          
                                    <label for="tnxtNumeroS" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Semestres:</label>                            
                                    <input style="width: 15%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="number" name="txtNumeroS" id="txtNumeroS" step="1" value="0">                                                        
                            
                                </div>                     
                                <!---------------------------------------------------------------------------------------------------->                              
                                <div class="form-group form-inline" style="margin-top:-15px">                                                        
                            
                                    <label for="txtTarjetaP" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>T. Profesional:</label>
                                    <input style="width: 18%" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtTarjetaP" id="txtTarjetaP" placeholder="Tarjeta Profesional" onkeypress="return txtValida(event,'num_car')">
                            
                                    <label for="sltGraduado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Graduado SI / NO</label>
                                    <input class="col-sm-1" type="radio" name="es_graduado" id="es_graduado"  value="1" checked>
                                    <input class="col-sm-1" type="radio" name="es_graduado" id="es_graduado" value="2" >
                            
                                    <label for="No" class="col-sm-2 col-md-2 col-lg-2 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                                </div>
                            </div> 
                        </form>    
                    </div>
                </div>    
                    
                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-23px">
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_INSTITUCION_EDUCATIVA.php">INSTITUCION<br/> EDUCATIVA</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_ESTUDIO.php">TIPO</a>
                                </td>
                            </tr>
                            
                     </table>
                </div>
                
                <!---------------------------------------------------------------------------------------------------->                        
    
                <div class="form-group form-inline" style="margin-top:5px; ">
                
                    <?php require_once './menu.php'; 
                
                        $sql = "SELECT      es.id_unico,
                                        es.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                        es.titulo,
                                        es.fechaterminacion,
                                        es.numerosemestres,
                                        es.graduado,
                                        es.tarjetaprofesional,
                                        es.tipo,
                                        te.id_unico,
                                        te.nombre,
                                        es.institucioneducativa,
                                        ie.id_unico,
                                        ie.nombre
                                    FROM gn_estudio es	 
                                    LEFT JOIN	gn_empleado e               ON es.empleado = e.id_unico
                                    LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                                    LEFT JOIN   gn_tipo_estudio te          ON es.tipo = te.id_unico
                                    LEFT JOIN   gn_institucion_educativa ie ON es.institucioneducativa = ie.id_unico
                                    WHERE es.empleado = $idTer";
                        $resultado = $mysqli->query($sql);
                    ?>
               
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <div class="table-responsive" >
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <!-- Actualización 24 / 02 09:57: No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Título</strong></td>
                                        <td class="cabeza"><strong>Fecha Terminación</strong></td>
                                        <td class="cabeza"><strong>No. Semestres</strong></td>
                                        <td class="cabeza"><strong>Es Graduado</strong></td>
                                        <td class="cabeza"><strong>Tarjeta Profesional</strong></td>
                                        <td class="cabeza"><strong>Tipo</strong></td>
                                        <td class="cabeza"><strong>Institución Educativa</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <!-- Actualización 24 / 02 09:57: No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Título</th>
                                        <th class="cabeza">Fecha Terminación</th>
                                        <th class="cabeza">No. Semestres</th>
                                        <th class="cabeza">Es Graduado</th>
                                        <th class="cabeza">Tarjeta Profesional</th>
                                        <th class="cabeza">Tipo</th>
                                        <th class="cabeza">Institución Educativa</th>
                                    </tr>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        $esfec = $row[7];
                                        $esfec = trim($esfec, '"');
                                        $fecha_div = explode("-", $esfec);
                                        $anioe = $fecha_div[0];
                                        $mese = $fecha_div[1];
                                        $diae = $fecha_div[2];
                                        $esfec = $diae.'/'.$mese.'/'.$anioe;
                                    
                                        $esid   = $row[0];
                                        $esemp  = $row[1];
                                        $eid    = $row[2];
                                        $eter   = $row[3];
                                        $tid    = $row[4];
                                        $tnom   = $row[5];
                                        $estit  = $row[6];
                                        #$esfec = $row[7];
                                        $esnum  = $row[8];
                                        $esgrad = $row[9];
                                        $estp   = $row[10];
                                        $estip  = $row[11];
                                        $teid   = $row[12];
                                        $tenom  = $row[13];
                                        $esie   = $row[14];
                                        $ieid   = $row[15];
                                        $ienom  = $row[16];
                                        
                                        if($esgrad==1)
                                            $grad = "SI";
                                        elseif($esgrad==2)
                                            $grad = "NO";

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_ESTUDIO.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                        <!-- Actualización 24 / 02 09:58: No es necesario mostrar el nombre del empleado
                                        <td class="campos"><?php #echo $tnom?></td>                
                                        -->
                                        <td class="campos"><?php echo $estit?></td>                
                                        <td class="campos"><?php echo $esfec?></td>                
                                        <td class="campos"><?php echo $esnum?></td>                
                                        <td class="campos"><?php echo $grad?></td>                
                                        <td class="campos"><?php echo $estp?></td>                
                                        <td class="campos"><?php echo $tenom?></td>                
                                        <td class="campos"><?php echo $ienom?></td>           
                                    </tr>
                                    <?php }
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
                            <p>¿Desea eliminar el registro seleccionado de Estudio?</p>
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
                            url:"json/eliminarEstudioJson.php?id="+id,
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
            $("#sltInstE").select2();
            $("#sltTipo").select2();
        </script>
         
    </body>
</html>
