<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];

@$actividad = $_REQUEST['actividadP'];
if(empty($_GET['id'])) {
    $titulo = "Registrar ";
    $titulo2= ".";
} else {
    $titulo = "Modificar ";
    $id     = $_GET['id'];
    $row    = $con->Listar("SELECT * FROM gy_riesgo
            WHERE md5(id_unico)='$id'");
    $titulo2= $row[0][1];
}
?>

  <!--Titulo de la página-->
<title>Proyecto</title>
</head>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label #nombre-error, #proyecto-error, #categoria-error, #tipo_proyecto-error,
        #fecha-error, #titulo-error, #monto_solicitado-error, #monto_aportado-error,
        #monto_total-error, #ciudad-error {
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
<body>
    <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center" >
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Llamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px">
                <h2 align="center" class="tituloform"><?php echo $titulo.' Proyecto'?></h2>
                <a href="listar_GY_PROYECTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px">.</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; line-height: normal;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 15px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <!--<div class="form-group" style="margin-bottom: 0px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required="required" placeholder="Nombre" title="Ingrese Nombre">
                        </div>
                        <div class="form-group" style="margin-top: -3px;">
                            <label for="proyecto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Proyecto:</label>
                            <select required id="proyecto" name="proyecto" class="select2_single form-control"  style="height: 35px;"  title="Seleccione un Proyecto">
                                <option value="">Proyecto</option>
                               <?php
                               /*$row = $con->Listar("SELECT  id_unico, nombre
                                FROM gf_proyecto
                                ORDER BY nombre ASC ");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                               }*/
                               ?>
                            </select>
                        </div>-->
                        <div class="form-group" style="margin-bottom:0px;">
                            <label for="titulo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Titulo:</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" required="required" placeholder="Titulo" title="Ingrese el Titulo">
                        </div>
                        <div class="form-group" style="margin-top: 5px;">
                            <label for="categoria" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Categoría:</label>
                            <select required id="categoria" name="categoria" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Categoría">
                                <option value="">Categoría</option>
                               <?php
                               $row = $con->Listar("SELECT  id_unico, nombre
                                FROM gy_categoria
                                WHERE compania = '$compania'
                                ORDER BY nombre ASC ");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 5px;">
                            <label for="tipo_proyecto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Proyecto:</label>
                            <select required id="tipo_proyecto" name="tipo_proyecto" class="select2_single form-control"  style="height: 35px;"  title="Seleccione un Tipo Proyecto">
                                <option value="">Tipo Proyecto</option>
                               <?php
                               $row = $con->Listar("SELECT  id_unico, nombre
                                FROM gy_tipo_proyecto
                                WHERE compania = '$compania'
                                ORDER BY nombre ASC ");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 5px;">
                            <label for="fecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                            <input type="text" name="fecha" id="fecha" class="form-control" required="required" placeholder="Fecha" title="Ingrese la Fecha" readonly>
                        </div>
                        
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="monto_solicitado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Monto Solicitado:</label>
                            <input type="text" name="monto_solicitado" id="monto_solicitado" class="form-control input-sm" maxlength="50" style="width:300px; height: 38px" placeholder="Monto Solicitado" onkeypress="return txtValida(event, 'dec', 'monto_solicitado', '2');" title="Ingrese el Monto Solicitado" onkeyup="formatC('monto_solicitado');" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="monto_aportado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Monto Aportado:</label>
                            <input type="text" name="monto_aportado" id="monto_aportado" class="form-control input-sm" maxlength="50" style="width:300px; height: 38px" placeholder="Monto Apartado" onkeypress="return txtValida(event, 'dec', 'monto_aportado', '2');" title="Ingrese el Monto Apartado" onkeyup="formatC('monto_aportado');" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="monto_total" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Monto Total:</label>
                            <input type="text" name="monto_total" id="monto_total" class="form-control input-sm" maxlength="50" style="width:300px; height: 38px" placeholder="Monto Total" onkeypress="return txtValida(event, 'dec', 'monto_total', '2');" title="Ingrese el Monto Total" onkeyup="formatC('monto_total');" required readonly>
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="ciudad" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <select required id="ciudad" name="ciudad" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Ciudad">
                                <option value="">Ciudad</option>
                               <?php
                               $row = $con->Listar("SELECT  c.id_unico, c.nombre, d.nombre
                                FROM gf_ciudad c
                                LEFT JOIN gf_departamento d ON c.departamento = d.id_unico
                                ORDER BY c.nombre ASC ");
                               for ($i = 0; $i < count($row); $i++) {
                                   echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1].' - '.$row[$i][2])).'</option>';
                               }
                               ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 20px; ">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                        <input type="hidden" name="txtact" id="txtact" value="<?php echo $actividad; ?>" >
                    </form>

                </div>
            </div>
            <div class="col-sm-2 col-sm-2 " style="margin-top:-22px">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                            </tr>    
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información Adicional</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GY_TIPO_PROYECTO.php?proyec=1">TIPO PROYECTO</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GY_CATEGORIA.php?proyec=1">CATEGORIA</a>
                                </td>
                            </tr>
                            
                           
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <script>
        $("#monto_aportado").change(function(){
           var ma = $("#monto_aportado").val();
           var ms = $("#monto_solicitado").val();
           
           var x2 = parseFloat(ma.replace(/\,/g, ''));
           
            if(ms===""){
               console.log("hola");
               var T = x2;
            }else{
               var x1 = parseFloat(ms.replace(/\,/g, ''));
               var T  = x1 + x2;
           }
           document.getElementById('monto_total').value=formatV(T);
        });
        
        $("#monto_solicitado").change(function(){
           var ma = $("#monto_aportado").val();
           var ms = $("#monto_solicitado").val();
           
           var x1 = parseFloat(ms.replace(/\,/g, ''));
           
            if(ma===""){
               console.log("hola");
               var T = x1;
            }else{
               var x2 = parseFloat(ma.replace(/\,/g, ''));
               var T  = x1 + x2;
           }
            
           
           document.getElementById('monto_total').value=formatV(T);
        });
    </script>
    <script>
                        function registrar(){
                            var AP = $("#txtact").val();
                            jsShowWindowLoad('Guardando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_proyectoJson.php?action=2",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Guardada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            if(AP == 1){
                                                window.history.go(-1);
                                            }else{
                                                document.location='listar_GY_PROYECTO.php';
                                            }    
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Guardar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>

    <?php require_once('footer.php'); ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({

        allowClear: true
      });

    });
  </script>

  <script>
    $(function(){
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
        $("#fecha").datepicker({changeMonth: true,}).val();
    });
</script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
