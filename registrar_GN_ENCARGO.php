<?php
#02/03/2017 --- Nestor B --- Se modificó la ruta del botón "atrás", la función para que reconozca las tildes y el método para cambiar el formato de fecha para que no genere error cuando venga vacío
#03/03/2017 --- Nestor B --- se agregó la función fecha Inicial para que no permita que la fecha de inicial sea mayor que la fecha de final y se modificó el ancho del formulario listar para ajustarlo con el de registrar
#11/03/2017 --- Nestor --- se modificó la altura del botón atrás y del título informcaión adicional
#14/07/2017 --- Nestor B --- se agregaron validaciones para hacer responsive el formulario
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
<style>
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
    label #sltEmpleado-error, #sltCategoria-error, #sltCargo-error, #sltDependencia-error, #sltTipo-error {
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
       
        
        $("#sltFechaI").datepicker({changeMonth: true}).val();
        $("#sltFechaF").datepicker({changeMonth: true}).val();
        $("#sltFechaA").datepicker({changeMonth: true}).val();
        
        
});
</script>
        <title>Registrar Encargo</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Encargo</h2>
                <a href="<?php echo 'listar_GN_ENCARGO.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEncargoJson.php">
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
                            
                                <label for="sltCategoria" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Categoría:</label>                                
                                <select required="required" name="sltCategoria" id="sltCategoria" title="Seleccione Categoría" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2" >
                                    <?php                             	
                                        $ca = "SELECT id_unico, nombre FROM gn_categoria";
                                        $cat = $mysqli->query($ca);
                                        echo "<option value=''>Categoría</option>";                            		
                                        while($rowC = mysqli_fetch_row($cat)){
                                            echo "<option value=".$rowC[0].">".ucwords(strtolower($rowC[1]))."</option>";
                                        }                            	
                                    ?>                            	                           	
                                </select>
                            
                                <label for="sltCargo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Cargo:</label>
                                <?php 
                                    $ca = "SELECT id_unico, nombre FROM gf_cargo";
                                    $car = $mysqli->query($ca);
                                ?>
                                <select required="required" name="sltCargo" id="sltCargo" title="Seleccione Cargo" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                    <option value="">Cargo</option>
                                    <?php 
                                        while($rowC = mysqli_fetch_row($car))
                                        {
                                            echo "<option value=".$rowC[0].">".$rowC[1]."</option>";
                                        }
                                     ?>
                                </select>
                          
                            </div>
                            <!----------------------------------------------------------------------------------------------------->                              
                            <div class="form-group form-inline">
                            
                                <label for="sltDependencia" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Dependencia:</label>
                                <?php 
                                    $de = "SELECT id_unico, nombre  FROM gf_dependencia";
                                    $dep = $mysqli->query($de);
                                ?>
                                <select required="required" name="sltDependencia" id="sltDependencia" title="Seleccione Dependencia" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                    <option value="">Dependencia</option>
                                    <?php 
                                        while($rowD = mysqli_fetch_row($dep))
                                        {
                                            echo "<option value=".$rowD[0].">".$rowD[1]."</option>";
                                        }
                                    ?>
                                </select>
                                <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo:</label>
                                <?php 
                                    $no = "SELECT id_unico, nombre  FROM gn_tipo_novedad";
                                    $nov = $mysqli->query($no);
                                ?>
                                <select required="required" name="sltTipo" id="sltTipo" title="Seleccione Tipo Novedad" style="width: 14%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                    <option value="">Tipo</option>
                                    <?php 
                                        while($rowTN = mysqli_fetch_row($nov))
                                        {
                                            echo "<option value=".$rowTN[0].">".$rowTN[1]."</option>";
                                        }
                                    ?>
                                </select>
                                
                                <label for="txtNumeroA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>No. Acto:</label>
                                <input onkeypress="return txtValida(event,'num')" style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="txtNumeroA" id="txtNumeroA" step="1" placeholder="No. Acto" >
                            </div>                     
                            <!---------------------------------------------------------------------------------------------------->                              
                            <div class="form-group form-inline" style="margin-top:-30px">                            
                            
                                <!----------Script para invocar Date Picker-->
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $("#datepicker").datepicker();
                                    });
                                </script>
                            
                                <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                                <input style="width:15%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaI" id="sltFechaI" step="1" onchange="javascript:fechaInicial();" placeholder="Ingrese la fecha">                            
                            
                                <label for="sltFechaF" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                                <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaF" id="sltFechaF" step="1" placeholder="Ingrese la fecha" disabled="true">
                            
                                <label for="sltFechaA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Acto:</label>
                                <input style="width:14%; height: 32px;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaA" id="sltFechaA" step="1" placeholder="Ingrese la fecha">
                            </div>      
                        
                            <div class="form-group form-inline" style="margin-top:-30px">                            
                            
                                <label for="No" class="col-sm-10 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            </div>
                        </div>    
                    </form>        
                </div>      
            </div>
            
            <div class="col-sm-8 col-sm-2" style="margin-top:-22px">
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
                                <a class="btn btn-primary btnInfo" href="registrar_GF_CARGO.php">CARGO</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GN_CATEGORIA.php">CATEGORÍA</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="Registrar_GF_DEPENDENCIA.php">DEPENDENCIA</a>
                            </td>
                        </tr>
                    </tbody>        
                </table>
            </div>
            
            <!---------------------------------------------------------------------------------------------------->                        
            <div class="form-group form-inline" style="margin-top:5px;">
                <?php require_once './menu.php'; 
                    if(!empty($idTer)){
                
                        $sql = "SELECT  en.id_unico,
                                        en.numeroacto,
                                        en.fechaacto,
                                        en.fechainicio,
                                        en.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                        en.fechafin,
                                        en.categoria,
                                        c.id_unico,
                                        c.nombre,
                                        en.cargo,
                                        car.id_unico,
                                        car.nombre,
                                        en.dependencia,
                                        d.id_unico,
                                        d.nombre,
                                        en.tiponovedad,
                                        tn.id_unico,
                                        tn.nombre
                                FROM gn_encargo en
                                LEFT JOIN	gn_empleado e         ON en.empleado    = e.id_unico
                                LEFT JOIN   gf_tercero t          ON e.tercero      = t.id_unico
                                LEFT JOIN   gn_tipo_novedad tn    ON en.tiponovedad = tn.id_unico
                                LEFT JOIN   gn_categoria c        ON en.categoria   = c.id_unico
                                LEFT JOIN   gf_cargo car          ON en.cargo       = car.id_unico
                                LEFT JOIN   gf_dependencia d      ON en.dependencia = d.id_unico
                                WHERE en.empleado = $idTer";
                        
                        $resultado = $mysqli->query($sql);
                        $nres = mysqli_num_rows($resultado);
                    }else{
                        $nres = 0;
                    }    
                ?>
                <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top:5px;" >
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <!-- Actualización 24 / 02 09:50: No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Cargo</strong></td>
                                        <td class="cabeza"><strong>Dependencia</strong></td>
                                        <td class="cabeza"><strong>Categoría</strong></td>
                                        <td class="cabeza"><strong>Novedad</strong></td>
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <!-- Actualización 24 / 02 09:50: No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Cargo</th>
                                        <th class="cabeza">Dependencia</th>
                                        <th class="cabeza">Categoría</th>
                                        <th class="cabeza">Novedad</th>
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                    </tr>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        
                                        if($nres > 0){
                                        
                                            while ($row = mysqli_fetch_row($resultado)) { 
                                                $enfact    = $row[2];
                                                if(!empty($row[2])||$row[2]!=''){
                                                    $enfact    = trim($enfact, '"');
                                                    $fecha_div = explode("-", $enfact);
                                                    $aniof     = $fecha_div[0];
                                                    $mesf      = $fecha_div[1];
                                                    $diaf      = $fecha_div[2];
                                                    $enfact    = $diaf.'/'.$mesf.'/'.$aniof;
                                                }else{
                                                    $enfact='';
                                                }
                                        
                                                $enfi      = $row[3];
                                                if(!empty($row[3])||$row[3]!=''){
                                                    $enfi      = trim($enfi, '"');
                                                    $fecha_div = explode("-", $enfi);
                                                    $anioi     = $fecha_div[0];
                                                    $mesi      = $fecha_div[1];
                                                    $diai      = $fecha_div[2];
                                                    $enfi      = $diai.'/'.$mesi.'/'.$anioi;
                                                }else{
                                                    $enfi='';
                                                }
                                        
                                                $enff      = $row[9];
                                                if(!empty($row[9])||$row[9]!=''){
                                                    $enff      = trim($enff, '"');
                                                    $fecha_div = explode("-", $enff);
                                                    $anioff    = $fecha_div[0];
                                                    $mesff     = $fecha_div[1];
                                                    $diaff     = $fecha_div[2];
                                                    $enff     = $diaff.'/'.$mesff.'/'.$anioff;
                                                }else{
                                                    $enff='';
                                                }
                                                                                                        
                                                $enid    = $row[0];
                                                $ennact  = $row[1];
                                                #$enfact = $row[2];
                                                #$enfi   = $row[3];
                                                $enemp   = $row[4];
                                                $empid   = $row[5];
                                                $empter  = $row[6];
                                                $terid   = $row[7];
                                                $ternom  = $row[8];
                                                #$enff   = $row[9];
                                                $encat   = $row[10];
                                                $cid     = $row[11];
                                                $cnom    = $row[12];
                                                $encar   = $row[13];
                                                $carid   = $row[14];
                                                $carnom  = $row[15];
                                                $endep   = $row[16];
                                                $depid   = $row[17];
                                                $depnom  = $row[18];
                                                $entn    = $row[19];
                                                $tnid    = $row[20];
                                                $tnnom   = $row[21];                                        
                                    ?>
                                                <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                        </a>
                                                        <a href="modificar_GN_ENCARGO.php?id=<?php echo md5($row[0]);?>">
                                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                        </a>
                                                    </td>
                                                    <!-- Actualización 24 / 02 09:51: No es necesario mostrar el nombre del empleado
                                                    <td class="campos"><?php #echo $ternom?></td>                
                                                    -->
                                                    <td class="campos"><?php echo $carnom?></td>                   
                                                    <td class="campos"><?php echo $depnom?></td>                   
                                                    <td class="campos"><?php echo $cnom?></td>                   
                                                    <td class="campos"><?php echo $tnnom?></td>                
                                                    <td class="campos"><?php echo $ennact?></td>                
                                                    <td class="campos"><?php echo $enfact?></td>                
                                                    <td class="campos"><?php echo $enfi?></td>                
                                                    <td class="campos"><?php echo $enff?></td>     
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
                        <p>¿Desea eliminar el registro seleccionado de Encargo?</p>
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
                        url:"json/eliminarEncargoJson.php?id="+id,
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
        
        <script type="text/javascript" src="js/select2.js"></script>
            <script type="text/javascript"> 
            
                $("#sltDependencia").select2();
                $("#sltEmpleado").select2();
                $("#sltTipo").select2();
                $("#sltCategoria").select2();
                $("#sltCargo").select2();
       
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
    
</body>

</html>
