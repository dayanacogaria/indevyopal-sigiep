<?php
#28/09/2021 --- Elkin O --- Se creo el formulario registrar

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
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
       
        
        $("#sltFechaAc").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Registrar Hoja De Vida</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Hoja De Vida</h2>
                    <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarHojadevidaJson.php">
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
                                
                                    <label for="txtDocumento" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo. Documento:</label> 
                                    <?php 
                                        $doc = "SELECT id_unico, nombre FROM gf_tipo_documento ORDER BY id_unico ASC";
                                        $doctp = $mysqli->query($doc);
                                    ?>
                                    <select required="required" name="sltDocumento" id="sltDocumento" title="Seleccione Tipo Documento" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="">Tipo Documento</option>
                                        <?php 
                                            while($rowTD = mysqli_fetch_row($doctp))
                                            {
                                                echo "<option value=".$rowTD[0].">".$rowTD[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select> 
                                   <!----------------------------------------------------------------------->
                                    <label for="FechaAc" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Actualización:</label>
                                    <input style="width: 18%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaAc" id="sltFechaAc" step="1" placeholder="Ingrese la fecha">
                          
                                   
                            
                                </div>
                          
                                <!----------------------------------------------------------------------------------------------------->                              
                                <div class="form-group form-inline" style="margin-top:-15px">
                            
                                <label for="sltFolio" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Número Folio :
                               </label>
                               <input required="required" style="width:18%; height: 32px" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtFolio" id="txtFolio" placeholder="Numero Folio">
                              <!----------------------------------------------------------------------->  
                               <label for="sltArchivo" class="col-sm-2 control-label">
                            	<strong class="obligado"></strong>Archivo :
                               </label>
                               <input required="required"   class="col-sm-2 col-md-2 col-lg-2 input-sm" type="hidden" title="Seleccione Documento" id="archivos" name="archivos" required>
                               <input required="required"  class="col-sm-2 " id="file" name="file" type="file"  >
                               
                               
                              <!-----------------------------------------------------------------------> 
                               <label for="No" class="col-sm-1 "></label>
                               <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>   
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                                </div>                     
                            </div> 
                        </form>    
                    </div>
                </div>    
                    
            
                
                <!---------------------------------------------------------------------------------------------------->                        
    
                <div class="form-group form-inline" style="margin-top:5px; ">
                
                    <?php require_once './menu.php'; 
               
                        $sql = "SELECT td.nombre,
                                       Date_format(ed.fechaactualizacion, '%d/%m/%Y'),
                                       ed.numerofolio,
                                       ed.ruta,
                                       e.id_unico,
                                       ed.id_unico,
                                Concat(t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno, ' ', t.apellidodos)
                                FROM   gn_empleado_documento ed
                                       LEFT JOIN gf_tipo_documento td
                                              ON td.id_unico = ed.tipodocumento
                                       LEFT JOIN gn_empleado e
                                              ON e.id_unico = ed.empleado
                                       LEFT JOIN gf_tercero t
                                              ON e.tercero = t.id_unico
                                WHERE  ed.empleado =$idTer
                                ORDER BY ed.id_unico ASC";
                                     

                        $resultado = $mysqli->query($sql);
                    ?>
               
                    <div class="col-sm-10 col-md-10 col-lg-10">
                        <div class="table-responsive" >
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        
                                        <td class="cabeza"><strong>Tipo De Documento</strong></td>
                                        <td class="cabeza"><strong>Fecha De Actualización</strong></td>
                                        <td class="cabeza"><strong>No. Folio</strong></td>
                                        <td class="cabeza"><strong>Archivo</strong></td>
                                    
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        
                                        <th class="cabeza">Tipo De Documento</th>
                                        <th class="cabeza">Fecha De Actualización</th>
                                        <th class="cabeza">No. Folio</th>
                                        <th class="cabeza">Ver Archivo</th>
                                    </tr>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                        
                                        $nomt=$row[0];
                                        $fechaA=$row[1];
                                        $numF=$row[2];
                                        $ruta=$row[3];

                                        
                                        
                                    

                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[5];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_HOJADEVIDA.php?id=<?php echo $row[5];?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>
                                       
                                        <td class="campos"><?php echo $nomt?></td>                
                                        <td class="campos"><?php echo $fechaA?></td>                
                                        <td class="campos"><?php echo $numF?></td>                
                                        <td class="campos"><a href="<?php echo $ruta?>" target="_blank"><i class="glyphicon glyphicon-search"></a></td>                
                                                  
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
                            <p>¿Desea eliminar el registro seleccionado de Hoja de vida?</p>
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
                            url:"json/eliminarHojadevidaJson.php?id="+id,
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
            $("#sltEmpleado").select2();
            $("#sltDocumento").select2();
        </script>
         
    </body>
</html>
