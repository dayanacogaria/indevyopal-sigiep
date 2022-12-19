<?php
require_once ('./Conexion/conexion.php');
require_once ('head_listar.php');
session_start();
$anno  = $_SESSION['anno'];
@$id = $_GET['idE'];

if(empty($id))
{
    $datosTercero =' - ';
    $tercero = "Empleado";    
    $a="inline-block";
}
else
{
    $emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ',t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
    FROM gn_empleado e
    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
    LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
    WHERE md5(e.id_unico) = '$id'";
    $bus = $mysqli->query($emp);
    $busq = mysqli_fetch_row($bus);
    $idT = $busq[0];
    $datosTercero= $busq[2].' ('.$busq[5].')';
    
    $tercero = $datosTercero;
    $a="inline-block";
}
?>

<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltEmpleado-error, #sltTipo-error {
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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val(fecAct);
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
        <title>Registrar Vacaciones</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Vacaciones</h2>
                    <a href="<?php echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarVacacionesJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <label for="sltEmpleado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 15%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                    <?php 
                                        if(empty($id)) {
                                            $emp = "SELECT 						
                                                e.id_unico,
                                                e.tercero,
					                            t.id_unico,
                                                CONCAT_WS(' ',t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos ) 
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                                            $idTer = "";
                                            echo ' <option value="">Empleado</option>';
                                        }  else {
                                            $emp = "SELECT 						
                                                e.id_unico,
                                                e.tercero,
					                            t.id_unico,
                                                CONCAT_WS(' ',t.nombreuno, t.nombredos,  t.apellidouno, t.apellidodos ) 
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                                            $idTer = $idT;
                                            echo ' <option value="'.$idTer.'">'.$tercero.'</option>';
                                        }
                                        $empleado = $mysqli->query($emp);
                                    
                                    while($rowE = mysqli_fetch_row($empleado)){
                                        echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                    } ?>                            	                           	
                                    </select>
                                    
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Novedad:</label>
                                    <select name="sltTipo" id="sltTipo" title="Seleccione Tipo" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" required>
                                        <option value="">Tipo Novedad</option>
                                        <?php  
                                        $tip = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE tipo = 'V'";
                                        $tipon = $mysqli->query($tip);
                                        while($rowT = mysqli_fetch_row($tipon)) {
                            			     echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                        } ?>                            	                           	
                                    </select> 
                                    <label for="txtNumeroA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Número Acto:</label>
                                    <input  name="txtNumeroA" id="txtNumeroA" title="Ingrese Número Acto" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Número Acto">
                                </div>
                                <div class="form-group form-inline" >                              
                                    <label for="sltFechaI" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                                    <input name="sltFechaI" id="sltFechaI" title="Ingrese Fecha Inicio" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" onchange="javascript:fechaInicial();" placeholder="Ingrese la fecha"> 
                                    <label for="sltFechaF" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                                    <input name="sltFechaF" id="sltFechaF" title="Ingrese Fecha Fin" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Ingrese la fecha" disabled="true">  
                                    <label for="sltFechaA" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Acto:</label>
                                    <input name="sltFechaA" id="sltFechaA" title="Ingrese Fecha Acto" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Ingrese la fecha">  
                                </div>
                                <div class="form-group form-inline">
                                    <label for="sltFechaID" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Inicio Disfrute:</label>
                                    <input name="sltFechaID" id="sltFechaID" title="Ingrese Fecha Inicio Disfrute" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" onchange="javascript:fechaDisfrute();" placeholder="Ingrese la fecha">  
                            
                                    <label for="sltFechaFD" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Fin Disfrute:</label>
                                    <input name="sltFechaFD" id="sltFechaFD" title="Ingrese Fecha Fin Disfrute" type="text" style="width: 15%;height: 32px" class="form-control col-sm-1" placeholder="Ingrese la fecha" onchange="javascript:fechaDisfrute();">

                                    <label for="sltPeriodo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Periodo:</label>
                                    <select name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" required>
                                        <option value="">Periodo</option>
                                        <?php  
                                        $tip = "SELECT id_unico, codigointerno FROM gn_periodo WHERE parametrizacionanno = $anno";
                                        $tipon = $mysqli->query($tip);
                                        while($rowT = mysqli_fetch_row($tipon)) {
                                             echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                        } ?>                                                            
                                    </select>
                                </div>
                                <div class="form-group form-inline"> 
                                    <label for="txtDiasD" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Días Disfrute:</label>
                                    <input  name="txtDiasD" id="txtDiasD" title="Ingrese Días Disfrute" type="text" style="width: 15%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" placeholder="Días Disfrute" required="required">                           
                                    <label for="No" class="col-sm-2 col-md-2 col-lg-2 control-label"></label>
                                    <button type="submit" class="btn btn-primary sombra col-sm-2 col-md-2 col-lg-2" style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 0px ;"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_NOVEDAD.php">TIPO NOVEDAD</a>
                                </td>
                            </tr>
                        </tbody>    
                    </table>
                </div>
       
        
        <!---------------------------------------------------------------------------------------------------->                        
    
        <div class="form-group form-inline" style="margin-top:5px;">
                <?php require_once './menu.php'; 
                
                    if(!empty($idTer)){
                
                        $sql = "SELECT  v.id_unico,
                                        v.fechainicio,
                                        v.fechafin,
                                        v.fechainiciodisfrute,
                                        v.fechafindisfrute,
                                        v.numeroacto,
                                        v.fechaacto,
                                        v.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                        v.tiponovedad,
                                        tn.id_unico,
                                        tn.nombre
                                FROM gn_vacaciones v
                                LEFT JOIN	gn_empleado e           ON v.empleado       = e.id_unico
                                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                                LEFT JOIN   gn_tipo_novedad tn      ON v.tiponovedad = tn.id_unico
                                WHERE v.empleado = $idTer";
                        $resultado = $mysqli->query($sql);
                        $nres = mysqli_num_rows($resultado);
                    }else{
                        $nres = 0;
                    }
                ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top : 5px"> 
                    <div class="table-responsive" >
                        <div class="table-responsive" >
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>   
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio Disfrute</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin Disfrute</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Tipo Novedad</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                        <th class="cabeza">Fecha Inicio Disfrute</th>
                                        <th class="cabeza">Fecha Fin Disfrute</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        if($nres > 0){
                                    
                                            while ($row = mysqli_fetch_row($resultado)) {                                         
                                                $vfi = $row[1];
                                                if(!empty($row[1])||$row[1]!=''){
                                                
                                                    $vfi = trim($vfi, '"');
                                                    $fecha_div = explode("-", $vfi);
                                                    $anioi = $fecha_div[0];
                                                    $mesi  = $fecha_div[1];
                                                    $diai  = $fecha_div[2];
                                                    $vfi   = $diai.'/'.$mesi.'/'.$anioi;
                                                }else{
                                                    $vfi='';
                                                }
                                        
                                                $vff = $row[2];
                                                if(!empty($row[2])||$row[2]!=''){
                                            
                                                    $vff = trim($vff, '"');
                                                    $fecha_div = explode("-", $vff);
                                                    $aniof = $fecha_div[0];
                                                    $mesf = $fecha_div[1];
                                                    $diaf = $fecha_div[2];
                                                    $vff = $diaf.'/'.$mesf.'/'.$aniof;
                                                }else{
                                                    $vff='';
                                                }

                                                $vfid = $row[3];
                                                if(!empty($row[3])||$row[3]){
                                            
                                                    $vfid = trim($vfid, '"');
                                                    $fecha_div = explode("-", $vfid);
                                                    $anioid = $fecha_div[0];
                                                    $mesid  = $fecha_div[1];
                                                    $diaid  = $fecha_div[2];
                                                    $vfid   = $diaid.'/'.$mesid.'/'.$anioid;
                                                }else{
                                                    $vfid='';
                                                }
                                        
                                                $vffd = $row[4];
                                                if(!empty($row[4])||$row[4]!=''){
                                            
                                                    $vffd = trim($vffd, '"');
                                                    $fecha_div = explode("-", $vffd);
                                                    $aniofd = $fecha_div[0];
                                                    $mesfd = $fecha_div[1];
                                                    $diafd = $fecha_div[2];
                                                    $vffd = $diafd.'/'.$mesfd.'/'.$aniofd;
                                                }else{
                                                    $vffd='';
                                                }
                                        
                                                $vfa = $row[6];
                                                if(!empty($row[6])||$row[6]!=''){
                                            
                                                    $vfa = trim($vfa, '"');
                                                    $fecha_div = explode("-", $vfa);
                                                    $aniofa = $fecha_div[0];
                                                    $mesfa = $fecha_div[1];
                                                    $diafa = $fecha_div[2];
                                                    $vfa = $diafa.'/'.$mesfa.'/'.$aniofa;
                                                }else{
                                                    $vfa='';
                                                }
                                                                                                                            
                                                $vid    = $row[0];
                                                #$vfi    = $row[1];
                                                #$vff    = $row[2];
                                                #$vfid   = $row[3];
                                                #$vffd   = $row[4];
                                                $vnact  = $row[5];
                                                #$vfact  = $row[6];
                                                $vemp   = $row[7];
                                                $empid  = $row[8];
                                                $empter = $row[9];
                                                $terid  = $row[10];
                                                $ternom = $row[11];
                                                $vtip   = $row[12];
                                                $tnid   = $row[13];
                                                $tnnom  = $row[14];
                                    ?>
                                                <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                        </a>
                                                        <a href="modificar_GN_VACACIONES.php?id=<?php echo md5($row[0]);?>">
                                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                        </a>
                                                    </td>                                        
                                                    <!-- Actualización 24 / 02 16:47 No es necesario mostrar el nombre del empleado
                                                    <td class="campos"><?php #echo $ternom?></td>                
                                                    -->               
                                                    <td class="campos"><?php echo $vnact?></td>                
                                                    <td class="campos"><?php echo $vfa?></td>                
                                                    <td class="campos"><?php echo $tnnom?></td>                
                                                    <td class="campos"><?php echo $vfi?></td>                
                                                    <td class="campos"><?php echo $vff?></td>                   
                                                    <td class="campos"><?php echo $vfid?></td>                
                                                    <td class="campos"><?php echo $vffd?></td>                
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
                            <p>¿Desea eliminar el registro seleccionado de Vacaciones?</p>
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
                            url:"json/eliminarVacacionesJson.php?id="+id,
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
            
                $("#sltEmpleado").select2();
                $("#sltTipo").select2();
                $("#sltPeriodo").select2();
               
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


            function fechaDisfrute(){
                let fid = $('#sltFechaID').val();
                let ffd = $('#sltFechaFD').val();
                if(fid!='' && ffd!=''){
                    
                    var form_data = {action: 2, fid: fid, ffd: ffd};
                    $.ajax({
                        type: "POST",
                        url: "jsonNomina/gn_consultasJson.php",
                        data: form_data,
                        success: function (response)
                        {
                            
                            dias = response;
                            dias = parseFloat(dias) + parseFloat(1);
                            $("#txtDiasD").val(dias);
                        }
                    })

                }
           
            }
        </script>
    </body>
</html>