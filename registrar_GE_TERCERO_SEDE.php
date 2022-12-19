<?php
################################################################################
#
#Creado por: Nestor B | 17/10/2017
#
################################################################################

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
    $tercero = "Votante";    
}
else
{
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
    label #sltVotante-error, #sltTipo-error {
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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
        <title>Registrar Votante</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Votante</h2>
                    <a href="<?php echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarVotanteJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                            <!----------------------------------------------------------------------------------------------------------------------->
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <?php 
                                        if(empty($idT))
                                        {
                                            $vot = "SELECT  t.id_unico,
                                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                    FROM gf_tercero t
                                                    LEFT JOIN gf_perfil_tercero pt  ON pt.tercero = t.id_unico
                                                    WHERE pt.perfil = '10' ";
                                            $idTer = "";
                                        }
                                        else
                                        {
                                            $vot = "SELECT  t.id_unico,
                                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                    FROM gf_tercero t
                                                    WHERE t.id_unico = '$idT' ";
                                            $idTer = $idT;
                                        }
                                        $votante = $mysqli->query($vot);
                                    ?>
                            
                                    <label for="sltVotante" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Votante:</label>
                                    <select required="required" name="sltVotante" id="sltVotante" title="Seleccione Votante" style="width: 30%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2">
                                        <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                            <?php 
                                                while($rowV = mysqli_fetch_row($votante))
                                                {
                                                    echo "<option value=".$rowV[0].">".$rowV[1]."</option>";
                                                }
                                            ?>                            	                           	
                                    </select>
                                    <!----------------------------------------------------------------------->
                                    <?php  
                                        $tip = "SELECT id_unico, relacion FROM ge_tipo_relacion ";
                                        $tipon = $mysqli->query($tip);
                                    ?> 
                            
                                    <label for="sltTipo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Tipo Relación:</label>
                                    <select name="sltTipo" id="sltTipo" title="Seleccione Tipo de Relación" style="width: 30%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" required>
                                        <option value="">Tipo Relación</option>
                                        <?php 
                                            while($rowT = mysqli_fetch_row($tipon))
                                            {
                            			echo "<option value=".$rowT[0].">".$rowT[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>                          
                          
                                </div>
                                <!----------------------------------------------------------------------------------------------------->                              
                                
                                <div class="form-group form-inline">
                                                             
                                    <?php  
                                        $votrel = "SELECT t.id_unico, 
                                                       CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                                                FROM gf_tercero t
                                                LEFT JOIN gf_perfil_tercero pt ON pt.tercero = t.id_unico
                                                WHERE pt.perfil = 10";
                                        
                                        $votRE = $mysqli->query($votrel);
                                    ?> 
                            
                                    <label for="sltTer" class="col-sm-2 col-md-2 col-lg-2 control-label">Votante Relacionado:</label>
                                    <select name="sltTer" id="sltTer" title="Seleccione Votante Relacionado" style="width: 30%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" disabled="true">
                                        <option value="">Votante Relacionado</option>
                                        <?php 
                                            while($rowVR = mysqli_fetch_row($votRE))
                                            {
                            			echo "<option value=".$rowVR[0].">".$rowVR[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
                                    
                                    <?php
                                        $sed = "SELECT id_unico , nombre FROM ge_sede";
                                        $se = $mysqli->query($sed);
                                        
                                    ?>
                                    <label for="sltSede" class="col-sm-2 col-md-2 col-lg-2 control-label">Sede:</label>
                                    <select name="sltSede" id="sltSede" title="Seleccione una Sede" style="width: 30%;height: 32px" class="form-control col-sm-2 col-md-2 col-lg-2" disabled="true">
                                        <option value="">Sede</option>
                                        <?php 
                                            while($rowS = mysqli_fetch_row($se))
                                            {
                            			echo "<option value=".$rowS[0].">".$rowS[1]."</option>";
                                            }
                                        ?>                            	                           	
                                    </select>
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
                                    <a class="btn btn-primary btnInfo" href="registrar_TERCERO_CONTACTO_NATURAL.php">VOTANTE</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GE_SEDE.php">SEDE</a>
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
            
                $("#sltVotante").select2();
                $("#sltTipo").select2();
                $("#sltTer").select2();
                $("#sltSede").select2();
               
            </script>
        </div>
        <script>
             $('#sltTipo').click(function(){
                var tipo= document.getElementById('sltTipo').value;
                
                if(tipo == 3){
                    
                    $("#sltTer").prop("disabled", false);
                    $("#sltSede").prop("disabled", true);
                }else if(tipo == 2){
                    $("#sltTer").prop("disabled", true);
                    $("#sltSede").prop("disabled", false);
                }else{
                     $("#sltSede").prop("disabled", true);
                     $("#sltTer").prop("disabled", true);
                }    
            });
        </script>
    </body>
</html>
