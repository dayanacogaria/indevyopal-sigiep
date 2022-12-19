<?php 
require_once 'head_listar.php';
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
$con = new ConexionPDO();
$id_empleado = $_REQUEST['id'];
$panno  = $_SESSION['anno'];

$ter = $con->Listar("SELECT e.id_unico,  CONCAT_WS(' ',t.razonsocial, t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) FROM gn_empleado e 
	LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
	WHERE md5(e.id_unico)='$id_empleado'");

#*DATOS TABLA
$row = $con->Listar("SELECT ec.id_unico, cc.nombre, ec.valor, ec.porcentaje FROM gn_empleado_centro_costo ec 
LEFT JOIN gf_centro_costo cc ON ec.centro_costo = cc.id_unico
WHERE ec.empleado =".$ter[0][0]." AND cc.parametrizacionanno = $panno");
?>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<script>
    $().ready(function () {
        var validator = $("#form").validate({
            ignore: "",
            errorPlacement: function (error, element) {

                $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
            },
        });

        $(".cancel").click(function () {
            validator.resetForm();
        });
    });
</script>
<style>
label #centro_costo-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<title>Registrar Empleado Centro de Costo</title>

</head>
<body>
    <div class="container-fluid text-center">	
        <div class="row content">
<?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Empleado Centro de Costo</h2>
                <a href="listar_GN_EMPLEADO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?= ucwords((mb_strtolower($ter[0][1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <form name="form" id="form" method="POST" class="form-inline" enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="tercero" value="<?= $ter[0][0] ?>">
                        <div class="form-group form-inline" style="margin-top:-20px;width: 100%; ">                            
                            <label for="centro_costo" class="control-label col-sm-5" style="text-align: right; margin-top: 10px"><strong style="color:#03C1FB;">*</strong>Centro de Costo:</label>
                            <select name="centro_costo" id="centro_costo" class="select2_single form-control col-sm-5" title="Seleccione Centro de Costo" required="required" style="width:250px;  ">
                                <option value="">Centro de Costo</option>
                                <?php 
                                $rowr = $con->Listar("SELECT id_unico, nombre 
                                    FROM gf_centro_costo  
                                    WHERE id_unico NOT IN (SELECT centro_costo FROM gn_empleado_centro_costo WHERE empleado = ".$ter[0][0].") AND parametrizacionanno = $panno");
                                for ($r = 0; $r < count($rowr); $r++) {
                                    echo '<option value="'.$rowr[$r][0].'">'.$rowr[$r][1].'</option>';
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;">Guardar</button>
                            <input type="hidden" name="MM_insert" >
                        </div>      
                    </form>       
                </div>                               
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Responsabilidad</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Responsabilidad</th>
                                </tr>
                            </thead>
                            <tbody>  
                                <?php 
                                for ($i = 0; $i < count($row); $i++) {
                                    echo '<tr>               
                                        <td style="display: none;">'.$row[$i][0].'</td>
                                        <td align="center" class="campos">
                                            <a href="#" onclick="javascript:eliminar('.$row[$i][0].');"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        </td>
                                        <td class="campos">'.$row[$i][1].'</td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once 'footer.php'; ?>
    
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">      
                    <p>¿Desea eliminar el registro seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="msj"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
   
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
     <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
        function guardar(){
            var formData = new FormData($("#form")[0]);  
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_tercerosJson.php?action=10",
                data:formData,
                contentType: false,
                processData: false,
                success: function(response)
                { 
                    jsRemoveWindowLoad();
                    console.log(response);                    
                    if (response != 0){
                       $("#msj").html('Información Guardada Correctamente');
                       $("#mdlMensaje").modal("show");
                    } else {
                        $("#msj").html('No se ha podido guardar la información');
                       $("#mdlMensaje").modal("show");
                    }
                }
            });
        }       
        
        function eliminar(id){
            $("#myModal").modal('show');
            $("#ver").click(function() {
                $("#myModal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "jsonPptal/gf_tercerosJson.php?action=11&id=" + id,
                    success: function (response) {
                        if (response != 0){
                            $("#msj").html('Información Eliminada Correctamente');
                            $("#mdlMensaje").modal("show");
                        } else {
                             $("#msj").html('No se ha podido eliminar la información');
                            $("#mdlMensaje").modal("show");
                        }
                    }
                });
            });
            
        }
        
        $("#ver1").click(function () {
            document.location.reload();
        });
        $("#ver2").click(function () {
           document.location.reload();
        });
    </script>
</body>
</html>					