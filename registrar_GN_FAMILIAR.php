<?php
#02/03/2017 --- Nestor B --- Se modificó la ruta del botón "atrás" y la función para que renozca las tildes
#10/03/2017 --- Nestor B --- se modificaron el ancho del formulario de listar y  el alto del botón atrás y el titulo de información adicional y se le agrego la libreria de busqueda rápida
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
       
        
        //$("#sltFechaT").datepicker({changeMonth: true,}).val(fecAct);
        
        
});
</script>
        <title>Registrar Familiar</title>
        <!--<link href="css/select/select2.min.css" rel="stylesheet">-->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Familiar</h2>
                    <a href="<?php echo 'modificar_GN_EMPLEADO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFamiliarJson.php">
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
                          
                                    <label for="sltEmpleado" class="col-sm-2 col-md-2 col-lg-2 control-label" ><strong class="obligado">*</strong>Empleado:</label>
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 18%;height: 30px" class="form-control col-sm-1 col-md-1 col-lg-1">
                                        <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                            			echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    <!----------------------------------------------------------------------->
                                    <?php 
                                        $rel = "SELECT id_unico, nombre FROM gn_tipo_relacion";
                                        $relac = $mysqli->query($rel);
                                    ?>
                            
                                    <label for="sltRelacion" class="col-sm-2 col-md-2 col-lg-2 control-label" ><strong class="obligado">*</strong>Relación:</label>
                                    <select required="required" name="sltRelacion" id="sltRelacion" title="Seleccione Relación" style="width: 14%;height: 30px" class="form-control col-sm-1 col-md-1 col-lgl-1">
                                        <option value="">Relación</option>
                                        <?php 
                                            while($rowR = mysqli_fetch_row($relac))
                                            {
                            			echo "<option value=".$rowR[0].">".$rowR[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    <?php 
                                        $ter = "SELECT              t.id_unico,
                                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                FROM gf_perfil_tercero pt 
                                                LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                                                WHERE pt.perfil = 10 AND CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos) IS NOT NULL
                                                ORDER BY t.id_unico";
                        
                                        $tercero = $mysqli->query($ter);
                                    ?>
                            
                                    <label for="sltTercero" class="col-sm-2 col-md-2 col-lg-2 control-label" ><strong class="obligado">*</strong>Contacto:</label>
                                    <select required="required" name="sltTercero" id="sltTercero" title="Seleccione Tercero" style="width: 17%;height: 30px" class="form-control col-sm-1 col-md-1 col-lg-1">
                                        <option value="">Contacto</option>
                                        <?php 
                                            while($rowC = mysqli_fetch_row($tercero))
                                            {
                            			echo "<option value=".$rowC[0].">".$rowC[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                
                                </div>
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 form-group col-sm-push-10 col-md-push-10 col-lg-push-10" style="margin-top: -15px">
                                <label for="No" class="col-sm-2 control-label"></label>
                                <button type="submit" class="btn btn-primary shadow" ><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                            </div>
                    
                        </form>    
                    </div>
                </div>
            
                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-22px" >
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_RELACION.php">RELACIÓN</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_TERCERO_CONTACTO_NATURAL.php">CONTACTO</a>
                                </td>
                            </tr>
                        </tbody>   
                    </table>
                </div>
                <!---------------------------------------------------------------------------------------------------->                        
                <div class="form-group form-inline" style="margin-top:5px; " >
                
                    <?php require_once './menu.php'; 
                        $sql = "SELECT      f.id_unico,
                                    f.empleado,
                                    e.id_unico,
                                    e.tercero,
                                    t.id_unico,
                                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                    f.tiporelacion,
                                    tr.id_unico,
                                    tr.nombre,
                                    f.tercero,
                                    ter.id_unico,
                                    CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)
                                FROM gn_familiar f	 
                                LEFT JOIN	gn_empleado e ON f.empleado = e.id_unico
                                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN   gn_tipo_relacion tr ON f.tiporelacion = tr.id_unico
                                LEFT JOIN   gf_tercero ter ON f.tercero = ter.id_unico
                                WHERE f.empleado = $idTer";
                        $resultado = $mysqli->query($sql);
                    ?>
                    <div class="col-sm-8 col-md-8 col-lg-8 " style="margin-top:5px;"> 
                        <div class="table-responsive" >
                            <div class="table-responsive" >
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>
                                            <!-- Actualización 24 / 02 10:00 No es necesario mostrar el nombre del empleado
                                            <td class="cabeza"><strong>Empleado</strong></td>
                                            -->
                                            <td class="cabeza"><strong>Tipo Relación</strong></td>
                                            <td class="cabeza"><strong>Tercero</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th width="7%"></th>
                                            <!-- Actualización 24 / 02 10:00 No es necesario mostrar el nombre del empleado
                                            <th class="cabeza">Empleado</th>
                                            -->
                                            <th class="cabeza">Tipo Relación</th>
                                            <th class="cabeza">Tercero</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <?php 
                                            while ($row = mysqli_fetch_row($resultado)) {                                         
                                                $femp   = $row[1];
                                                $eid    = $row[1];
                                                $eter   = $row[3];
                                                $tid1   = $row[4];
                                                $ter1   = $row[5];
                                                $frel   = $row[6];
                                                $trid   = $row[7];
                                                $trnom  = $row[8];
                                                $fter   = $row[9];
                                                $tid2   = $row[10];
                                                $ter2   = $row[11];
                                        ?>
                                        <tr>
                                            <td style="display: none;"><?php echo $row[0]?></td>
                                            <td>
                                                <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                </a>
                                                <a href="modificar_GN_FAMILIAR.php?id=<?php echo md5($row[0]);?>">
                                                    <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                </a>
                                            </td>                                        
                                            <!-- Actualización 24 / 02 10:00 No es necesario mostrar el nombre del empleado
                                            <td class="campos"><?php #echo $ter1?></td>                
                                            -->
                                            <td class="campos"><?php echo $trnom?></td>                
                                            <td class="campos"><?php echo $ter2?></td>           
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
                            <p>¿Desea eliminar el registro seleccionado de Familiar?</p>
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
                            url:"json/eliminarFamiliarJson.php?id="+id,
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
            $("#sltRelacion").select2();
            $("#sltTercero").select2();
        </script>
        
 
    </body>
</html>
