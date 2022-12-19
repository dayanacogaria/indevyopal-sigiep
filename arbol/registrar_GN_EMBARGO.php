<?php
#01/03/2017 --- Nestor B --- se modificó el botón "atrás" y la funcion strtolower por mb_strtolower para que tome las tildes
#03/03/2017 --- Nestor B --- se agregó la función fecha Inicial para que no permita que la fecha de modificación sea mayo que la fehca de cancelación y se modificó el método para cambiar el fomrato de fecha 
#13/03/2017 --- Nestor B --- se modificó la ruta y la altura del botón atrás  y la altura del título de información adicional 
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,': ',t.numeroidentificacion)
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
       
        
        $("#sltFechaE").datepicker({changeMonth: true,}).val();
        $("#sltFechaL").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        
        
});
</script>
        <title>Registrar Embargo</title
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Embargo</h2>
                    <a href="<?php echo 'listar_GN_EMBARGO.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEmbargoJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                            <!----------------------------------------------------------------------------------------------------------------------->
                            <div col-sm-12 col-md-12 col-lg-12>
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
                                    
                                    <label for="sltEntidad" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Entidad:</label>                                
                                    <select required="required" name="sltEntidad" id="sltEntidad" title="Seleccione Entidad" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <?php                             	
                                            $ter = "SELECT          
                                                        t.id_unico,
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
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                                    
                                    <label for="sltFechaE" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Embargo:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaE" id="sltFechaE" step="1"  placeholder="Ingrese la fecha">                            
                          
                                </div>
                                <!-------------------------------------------------------------------------------------------------->                              
                        
                                <div class="form-group form-inline" style="margin-top:-15px">
                            
                                    <label for="sltFechaL" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Liquidar:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaL" id="sltFechaL" step="1"  placeholder="Ingrese la fecha">                            
                                    
                                    <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaI" id="sltFechaI" step="1" onchange="javascript:fechaInicial();" placeholder="Ingrese la fecha">                            
                            
                                    <label for="sltFechaF" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                                    <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaF" id="sltFechaF" step="1"  placeholder="Ingrese la fecha" disabled="true"> 
                                                                                  
                                </div>                     
                                <!---------------------------------------------------------------------------------------------------->                              
                        
                                <div class="form-group form-inline" style="margin-top:-30px">                            
                            
                                    <label for="No" class="col-sm-10 col-md-10  col-lg-10 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra col-sm-12 col-md-12 col-lg-12" style="margin-top:-3px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>  
                                </div>      
                            </div>
                        </form>
                    </div> 
                </div>    
                
                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-23px">
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GF_TERCERO_ENTIDAD_FINANCIERA.php">ENTIDAD</a>
                                </td>
                            </tr>
                            
                    </table>
                </div>
                <!---------------------------------------------------------------------------------------------------->                        
    
                <div class="form-group form-inline" style="margin-top:5px; ">
                
                    <?php require_once './menu.php'; 
                        if(!empty($idTer)){
                            $sql = "SELECT          em.id_unico,
                                                em.empleado,
                                                e.id_unico,
                                                e.tercero,
                                                t.id_unico,
                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                em.entidad,
                                                ter.id_unico,
                                                ter.razonsocial,
                                                em.fechaembargo,
                                                em.fechaliquidar,
                                                em.fechainicio,
                                                em.fechafin                        
                                FROM gn_embargo em	 
                                LEFT JOIN	gn_empleado e                   ON em.empleado = e.id_unico
                                LEFT JOIN   gf_tercero t                    ON e.tercero   = t.id_unico
                                LEFT JOIN   gf_tercero ter                  ON em.entidad  = ter.id_unico
                                WHERE em.empleado = $idTer";
                    
                            $resultado = $mysqli->query($sql);
                            $nres = mysqli_num_rows($resultado);
                        }else{
                            $nres = 0;
                        }    
                    ?>
                
                    <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 5px">
                        <div class="table-responsive" >
                            <div class="table-responsive" >
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>
                                            <!-- Actualización 24 / 02 09:45: No es necesario mostrar el nombre del empleado
                                            <td class="cabeza"><strong>Empleado</strong></td>
                                            -->
                                            <td class="cabeza"><strong>Entidad</strong></td>
                                            <td class="cabeza"><strong>Fecha Embargo</strong></td>
                                            <td class="cabeza"><strong>Fecha Liquidar</strong></td>
                                            <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                            <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th width="7%"></th>           
                                            <!-- Actualización 24 / 02 09:45: No es necesario mostrar el nombre del empleado
                                            <th class="cabeza">Empleado</th>
                                            -->
                                            <th class="cabeza">Entidad</th>
                                            <th class="cabeza">Fecha Embargo</th>
                                            <th class="cabeza">Fecha Liquidar</th>
                                            <th class="cabeza">Fecha Inicio</th>
                                            <th class="cabeza">Fecha Fin</th>
                                        </tr>
                                    
                                    </thead>    
                                    <tbody>
                                        <?php 
                                            if($nres > 0){
                                                while ($row = mysqli_fetch_row($resultado)) { 
                                        
                                                    $emfe = $row[9];
                                                    if(!empty($row[9])||$row[9]!=''){
                                                        $emfe = trim($emfe, '"');
                                                        $fecha_div = explode("-", $emfe);
                                                        $anioe = $fecha_div[0];
                                                        $mese = $fecha_div[1];
                                                        $diae = $fecha_div[2];
                                                        $emfe = $diae.'/'.$mese.'/'.$anioe;
                                                    }else{
                                                        $emfe='';
                                                    }
                                        
                                                    $emfl = $row[10];
                                                    if(!empty($row[10])||$row[10]!=''){
                                                        $emfl = trim($emfl, '"');
                                                        $fecha_div = explode("-", $emfl);
                                                        $aniol = $fecha_div[0];
                                                        $mesl = $fecha_div[1];
                                                        $dial = $fecha_div[2];
                                                        $emfl = $dial.'/'.$mesl.'/'.$aniol;
                                                    }else{
                                                        $emfl='';
                                                    }
                                        
                                                    $emfi = $row[11];
                                                    if(!empty($row[11])||$row[11]!=''){
                                                        $emfi = trim($emfi, '"');
                                                        $fecha_div = explode("-", $emfi);
                                                        $anioi = $fecha_div[0];
                                                        $mesi = $fecha_div[1];
                                                        $diai = $fecha_div[2];
                                                        $emfi = $diai.'/'.$mesi.'/'.$anioi;
                                                    }else{
                                                        $emfi='';
                                                    }
                                        
                                                    $emff = $row[12];
                                                    if(!empty($row[12])||$row[12]!=''){
                                                        $emff = trim($emff, '"');
                                                        $fecha_div = explode("-", $emff);
                                                        $aniof = $fecha_div[0];
                                                        $mesf = $fecha_div[1];
                                                        $diaf = $fecha_div[2];
                                                        $emff = $diaf.'/'.$mesf.'/'.$aniof;
                                                    }else{
                                                        $emff='';
                                                    }
                                        
                                                    $emid   = $row[0];
                                                    $ememp  = $row[1];
                                                    $eid    = $row[2];
                                                    $eter   = $row[3];
                                                    $terid  = $row[4];
                                                    $ternom = $row[5];
                                                    $ement  = $row[6];
                                                    $entid  = $row[7];
                                                    $entnom = $row[8];
                                                    #$emfe   = $row[9];
                                                    #$emfl   = $row[10];
                                                    #$emfi   = $row[11];
                                                    #$emff   = $row[12];

                                            ?>
                                                    <tr>
                                                        <td style="display: none;"><?php echo $row[0]?></td>
                                                        <td>
                                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                            </a>
                                                            <a href="modificar_GN_EMBARGO.php?id=<?php echo md5($row[0]);?>">
                                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                            </a>
                                                        </td>                                        
                                                        <!-- Actualización 24 / 02 09:35: No es necesario mostrar el nombre del empleado
                                                        <td class="campos"><?php #echo $ternom?></td>
                                                        -->
                                                        <td class="campos"><?php echo $entnom?></td>                
                                                        <td class="campos"><?php echo $emfe?></td>                
                                                        <td class="campos"><?php echo $emfl?></td>                
                                                        <td class="campos"><?php echo $emfi?></td>                
                                                        <td class="campos"><?php echo $emff?></td>       
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
                            <p>¿Desea eliminar el registro seleccionado de Embargo?</p>
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
                            url:"json/eliminarEmbargoJson.php?id="+id,
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
                var fechain= document.getElementById('sltFechaI').value;
                var fechafi= document.getElementById('sltFechaF').value;
                var fi = document.getElementById("sltFechaF");
                fi.disabled=false;
       
                $( "#sltFechaF" ).datepicker( "destroy" );
                $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
           
           
            }
        </script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltEntidad").select2();
       
         $("#sltEmpleado").select2();

       
        </script>
    </body>
</html>
