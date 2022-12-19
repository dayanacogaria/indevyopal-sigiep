 <?php
#01/03/2017 --- Nestor B --- se modificó el botón "atrás" y la funcion strtolower por mb_strtolower par que tome las tildes
#02/03/2017 --- Nestor B --- se modificó el método para cambiar el formato de fecha para que no genere error si viene vacío
#03/03/2017 --- Nestor B --- se modificó el ancho de listar para que concuerde con el de registrar y se agregó la función fecha Inicial para que no permita que la fecha de modificación sea mayo que la fehca de cancelación
#07/03/2017 --- Nestor B --- se agrego el botón de tipo en menú de archivos adicionales 
#10/03/2017 --- Nestor B --- se modificó el alto del botón guardar para que cuadre con el sutittulo 
#13/07/2017 --- Nestor B --- se agregaron validaciones para hacer responsive el formulario

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
       
        
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaR").datepicker({changeMonth: true}).val();
        
        
});
</script>
        <title>Registrar Afiliación</title>
       <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                        <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Afiliación</h2>
                        <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                        <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                        <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarAfiliacionJson.php">
                                <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         

                                <div class="col-sm-12 col-md-12 col-lg-12">  
                                    <div class="form-group form-inline" style="margin-top:-25px;">
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
                                        <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 12%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2">
                            	           <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                            	           <?php 
                            		            while($rowE = mysqli_fetch_row($empleado))
                                                {
                            			            echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                            		            }
                            	           ?>                            	                           	
                                        </select>
                          
                                        <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Tipo:</label>
                                            <?php 
                                                $af = "SELECT id_unico, nombre  FROM gn_tipo_afiliacion";
                                                $afil = $mysqli->query($af);
                                            ?>
                                        <select name="sltTipo" id="sltTipo" title="Seleccione Tipo" style="width: 13%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2">
                                            <option value="">Tipo</option>
                                            <?php 
                                                while($rowT = mysqli_fetch_row($afil))
                                                {
                                                    echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                                }
                                            ?>
                                        </select> 
                          
                                        <label for="sltTercero" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Entidad:</label>                                
                                        <select name="sltTercero" id="sltTercero" title="Seleccione Entidad" style="width: 13%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2" >
                            	           <?php                             	
                                                $ter = "SELECT          
                                                                t.id_unico,
                                                                t.razonsocial
                                                        FROM gf_perfil_tercero pt 
                                                        LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                                                        WHERE pt.perfil = 11";
                                                $tercero = $mysqli->query($ter);
                            		            echo "<option value=''>Entidad</option>";                            		
                            		            while($rowE = mysqli_fetch_row($tercero)){
                            			            echo "<option value=".$rowE[0].">".ucwords(mb_strtolower($rowE[1]))."</option>";
                            		            }                            	
                            	           ?>                            	                           	
                                        </select>                                     </div>

                                    <div class="form-group form-inline">
                            
                                        <label for="sltEstado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Estado:</label>
                                        <select name="sltEstado" id="sltEstado" title="Seleccione Estado" style="width: 12%;height: 30px" class=" form-control col-sm-2 col-md-2 col-lg-2">
                            
                                            <?php 
                                                $es   = "SELECT id_unico, nombre FROM gn_estado_afiliacion";
                                                $esta = $mysqli->query($es);
                                                echo "<option value=''>Estado</option>";                            		
                            		            while($rowES = mysqli_fetch_row($esta)){
                            			            echo "<option value=".$rowES[0].">".$rowES[1]."</option>";
                            		            }     	                                
                                            ?>
                                        </select>
                            
                                        <!---Script para invocar Date Picker-->
                                        <script type="text/javascript">
                            
                                            $(document).ready(function() {
                                                $("#datepicker").datepicker();
                                            });
                                        </script>
                            
                                        <label for="sltFechaA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Afiliación:</label>
                                        <input style="width:13%;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaA" id="sltFechaA" step="1" onchange="javascript:fechaInicial();" placeholder="Ingrese la fecha">                            
                            
                                        <label for="sltFechaR" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Retiro:</label>
                                        <input  class="col-sm-2 input-sm" type="text" name="sltFechaR" id="sltFechaR" step="1"  placeholder="Ingrese la fecha" disabled="true" style="width: 13%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2"> 

                                    </div>

                                    <div class="form-group form-inline">
                            
                                        <label for="txtCodigoA" class="col-sm-2 control-label"><strong class="obligado"></strong>Código Admin:</label>
                                        <input style="width:12%" class="col-sm-2 input-sm" type="text" name="txtCodigoA" id="txtCodigoA" step="1" placeholder="Código Admin">
                            
                                        <label for="txtObservaciones" class="col-sm-2 control-label"><strong class="obligado"></strong>Observaciones:</label>
                                        <input style="width:23%" class="col-sm-2 input-sm" type="text" name="txtObservaciones" id="txtObservaciones" step="1" placeholder="Observaciones">
                            
                                        <label for="No" class="col-sm-2 control-label"></label>
                                        <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                    </div>      
                                </div>
                            </form>
                        </div>
                </div>
                
                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-22px">
                
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_AFILIACION.php">TIPO</a>
                                </td>
                            </tr>
                    </table>
                </div>        
                            
                
                <!---------------------------------------------------------------------------------------------------->   

                <div class="form-group form-inline" style="margin-top:5px;">
                    <?php require_once './menu.php'; 
                        $sql = "SELECT      a.id_unico,
                                            a.empleado,
                                            e.id_unico,
                                            e.tercero,
                                            t.id_unico,
                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                            a.tipo,
                                            ta.id_unico,
                                            ta.nombre,
                                            a.tercero,
                                            ter.id_unico,
                                            ter.razonsocial,
                                            a.estado,
                                            ea.id_unico,
                                            ea.nombre,
                                            a.codigoadmin,
                                            a.observaciones,
                                            a.fechaafiliacion,
                                            a.fecharetiro
                                FROM gn_afiliacion a	 
                                LEFT JOIN	gn_empleado e           ON a.empleado = e.id_unico
                                LEFT JOIN   gf_tercero t            ON e.tercero = t.id_unico
                                LEFT JOIN   gn_tipo_afiliacion ta   ON a.tipo = ta.id_unico
                                LEFT JOIN   gf_tercero ter          ON a.tercero = ter.id_unico
                                LEFT JOIN   gn_estado_afiliacion ea ON a.estado = ea.id_unico
                                WHERE a.empleado = $idTer";
                    
                        $resultado = $mysqli->query($sql);
                    ?>
                     
                    
                         <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 5px;">
                        
                            <div class="table-responsive" >
                            
                                <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>                                        
                                            <!-- Actualización 24 / 02 09:35: No es necesario mostrar el nombre del empleado
                                            <td class="cabeza"><strong>Empleado</strong></td>
                                            -->
                                            <td class="cabeza"><strong>Código Admin.</strong></td>
                                            <td class="cabeza"><strong>Entidad</strong></td>
                                            <td class="cabeza"><strong>Tipo</strong></td>
                                            <td class="cabeza"><strong>Fecha Afiliación</strong></td>
                                            <td class="cabeza"><strong>Fecha Retiro</strong></td>
                                            <td class="cabeza"><strong>Observaciones</strong></td>
                                            <td class="cabeza"><strong>Estado</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th width="7%"></th>                                        
                                            <!-- Actualización 24 / 02 09:35: No es necesario mostrar el nombre del empleado
                                            <th class="cabeza">Empleado</th>
                                            -->
                                            <th class="cabeza">Código Admin.</th>
                                            <th class="cabeza">Entidad</th>
                                            <th class="cabeza">Tipo</th>
                                            <th class="cabeza">Fecha Afiliación</th>
                                            <th class="cabeza">Fecha Retiro</th>
                                            <th class="cabeza">Observaciones</th>
                                            <th class="cabeza">Estado</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <?php 
                                            while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                            $afa = $row[17];
                                            if(!empty($row[17])||$row[17]){
                                                $afa = trim($afa, '"');
                                                $fecha_div = explode("-", $afa);
                                                $anioa = $fecha_div[0];
                                                $mesa = $fecha_div[1];
                                                $diaa = $fecha_div[2];
                                                $afa = $diaa.'/'.$mesa.'/'.$anioa;
                                            }else{

                                                $afa = '';
                                            }
                                        
                                            $afr = $row[18];
                                            if(!empty($row[18])||$row[18]){
                                                $afr = trim($afr, '"');
                                                $fecha_div = explode("-", $afr);
                                                $anior = $fecha_div[0];
                                                $mesr = $fecha_div[1];
                                                $diar = $fecha_div[2];
                                                $afr = $diar.'/'.$mesr.'/'.$anior;
                                            }else{

                                                $afr = '';
                                            }
                                        
                                            $aid   = $row[0];
                                            $aemp  = $row[1];
                                            $eid   = $row[2];
                                            $eter  = $row[3];
                                            $tid1  = $row[4];
                                            $ter1  = $row[5];
                                            $atip  = $row[6];
                                            $taid  = $row[7];
                                            $tanom = $row[8];
                                            $ater  = $row[9];
                                            $tid2  = $row[10];
                                            $ter2  = $row[11];
                                            $aest  = $row[12];
                                            $eaid  = $row[13];
                                            $eanom = $row[14];
                                            $acod  = $row[15];
                                            $aobs  = $row[16];
                                            #$afa   = $row[17];
                                            #$afr   = $row[18];

                                        ?>
                                        <tr>
                                            <td style="display: none;"><?php echo $row[0]?></td>
                                            <td>
                                                <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                </a>
                                                <a href="modificar_GN_AFILIACION.php?id=<?php echo md5($row[0]);?>">
                                                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                </a>
                                            </td>
                                            <!-- Actualización 24 / 02 09:35: No es necesario mostrar el nombre del empleado
                                            <td class="campos"><?php #echo $ter1?></td>
                                            -->
                                            <td class="campos"><?php echo $acod?></td>                
                                            <td class="campos"><?php echo $ter2?></td>                
                                            <td class="campos"><?php echo $tanom?></td>                
                                            <td class="campos"><?php echo $afa?></td>                
                                            <td class="campos"><?php echo $afr?></td>                
                                            <td class="campos"><?php echo $aobs?></td>                
                                            <td class="campos"><?php echo $eanom?></td>                
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
                            <p>¿Desea eliminar el registro seleccionado de Afiliación?</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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

            <!-- <script type="text/javascript" src="js/menu.js"></script>
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
                            url:"json/eliminarAfiliacionJson.php?id="+id,
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

        <script>
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaA').value;
                var fechafi= document.getElementById('sltFechaR').value;
                var fi = document.getElementById("sltFechaR");
                fi.disabled=false;
       
                $( "#sltFechaR" ).datepicker( "destroy" );
                $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>
        <<script type="text/javascript" src="js/select2.js"></script>
        <script>
           $("#sltEmpleado").select2();
           $("#sltTipo").select2();
           $("#sltTercero").select2();
           $("#sltEstado").select2();
        </script>
    </body>
</html>