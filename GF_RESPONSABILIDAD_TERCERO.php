<?php 
require_once 'head_listar.php';
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
$con = new ConexionPDO();
$id_tercero = $_REQUEST['id'];

$ter = $con->Listar("SELECT t.id_unico, CONCAT_WS(' ',t.razonsocial, t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) FROM gf_tercero t WHERE md5(t.id_unico)='$id_tercero'");
#*DATOS TABLA
$row = $con->Listar("SELECT tr.id_unico, rf.nombre,rt.nombre FROM gf_tercero_responsabilidad tr 
LEFT JOIN gf_responsabilidad_fiscal rf ON tr.responsabilidad = rf.id_unico 
LEFT JOIN gf_responsabilidad_tributaria rt ON rt.id_unico=tr.responsabilidad_tributaria
WHERE tr.tercero =".$ter[0][0]);
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
label #responsabilidad-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<title>Registrar Responsabilidades</title>

</head>
<body>
    <div class="container-fluid text-center">	
        <div class="row content">
<?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Registrar Responsabilidades</h2>
                <a href="<?php echo $_SESSION['url']; ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?= ucwords((mb_strtolower($ter[0][1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <form name="form" id="form" method="POST" class="form-inline" enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="tercero" value="<?= $ter[0][0] ?>">
                        <div class="form-group">
                                <label for="sltTipo" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"  style="margin-right:11px;width:140px;"><strong class="obligado">*</strong>Tipo Responsabilidad:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                <select required="required" name="sltTipo" id="sltTipo" class="form-control select2_single" style="width:100px;" title="Seleccione Exportar" >
                                    <option value="">Tipo Responsabilidad</option>              
                                    <option value="1">Fiscal</option>
                                    <option value="2">Tributaria</option>
                                </select>
                                </div>
                                
                                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-right:11px; margin-left:-20px;width:150px; display:none" id="fiscal1">
                                <label for="responsabilidad" class="control-label col-sm-1 col-md-1 col-lg-1 text-right"><strong class="obligado"></strong>Responsabilidad Fiscal:</label>
                                </div>
                                <div class="col-sm-2 col-md-2 col-lg-2" id="fiscal" style="display:none" >
                                 <select name="responsabilidad" id="responsabilidad" class="select2_single form-control col-sm-2" title="Seleccione Responsabilidad" required="required" style="width:250px;  ">
                                           <option value="">Responsabilidad Fiscal</option>
                                           <?php 
                                           $rowr = $con->Listar("SELECT id_unico, nombre 
                                               FROM gf_responsabilidad_fiscal");
                                           for ($r = 0; $r < count($rowr); $r++) {
                                               echo '<option value="'.$rowr[$r][0].'">'.$rowr[$r][1].'</option>';
                                           }
                                           ?>
                                 </select>
                                 <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;" onclick="guardar()">Guardar</button>
                                 </div>

                                 <div class="col-sm-2 col-md-2 col-lg-2" style="margin-right:11px; margin-left:-20px;width:150px; display:none" id="tribu1" >
                                <label for="responsabilidad_tribu" class="control-label col-sm-2 col-md-2 col-lg-2 text-left"><strong class="obligado"></strong>Responsabilidad Tributaria:</label>
                                </div>
                                <div class="col-sm-2 col-md-2 col-lg-2" style="display:none" id="tribu" >
                                 <select name="responsabilidad_tribu" id="responsabilidad_tribu" class="select2_single form-control col-sm-2" title="Seleccione Responsabilidad Tributaria" required="required" style="width:250px;  ">
                                           <option value="">Responsabilidad Tributaria</option>
                                           <?php 
                                           $rowrt = $con->Listar("SELECT id_unico, nombre 
                                               FROM gf_responsabilidad_tributaria
                                               ");
                                           for ($rt = 0; $rt < count($rowrt); $rt++) {
                                               echo '<option value="'.$rowrt[$rt][0].'">'.$rowrt[$rt][1].'</option>';
                                           }
                                           ?>
                                 </select>
                                 
                                 <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;" onclick="guardarT()">Guardar</button>
                                 </div>
                        
                       
                    </form>       
                </div>                               
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">  
                

                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px; " id="tablaF">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%" >
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Responsabilidad Fiscal</strong></td>
                                    <td width="0%"></td>
                                    <td class="cabeza"><strong>Responsabilidad Tributaria</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Responsabilidad Fiscal</th>
                                    <th width="0%"></th>
                                    <th>Responsabilidad Tributaria</th>
                                </tr>
                            </thead>
                            <tbody>  
                                <?php 
                                for ($i = 0; $i < count($row); $i++) {
                                if ($row[$i][1]==null) {
                                    $respF='No hay datos';
                                    $claseF="class=''";
                                 }else{
                                    $respF=$row[$i][1];
                                    $claseF="class='glyphicon glyphicon-trash'";
                                 }
                                 if ($row[$i][2]==null) {
                                    $respT='No hay datos';
                                    $claseT="class=''";
                                 }else{
                                    $respT=$row[$i][2];
                                    $claseT="class='glyphicon glyphicon-trash'";
                                 }
                                    echo '<tr>               
                                        <td style="display: none;">'.ucwords(strtolower($row[$i][0])).'</td>
                                        <td align="center" class="campos">
                                            <a href="#" onclick="javascript:eliminar('.$row[$i][0].',1);"><i title="Eliminar" '.$claseF.'></i></a>
                                        </td>
                                        <td class="campos">'.$respF.'</td>
                                        <td align="center" class="campos">
                                        <a href="#" onclick="javascript:eliminar('.$row[$i][0].',2);"><i title="Eliminar" '.$claseT.'"></i></a>
                                    </td>
                                    <td class="campos">'.ucwords(strtolower($respT)).'</td>
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
        $("#sltTipo").change(function(){
            var tipo = $("#sltTipo").val();
            if(tipo == 1){
                $("#fiscal").css("display", "block");
                $("#fiscal").prop("required", false);
                $("#fiscal1").css("display", "block");
                $("#fiscal1").prop("required", false);
               
                $("#tribu").css("display", "none");
                $("#tribu").prop("required", true);
                $("#tribu1").css("display", "none");
                $("#tribu1").prop("required", true);
              
            } else {
                $("#tribu").css("display", "block");
                $("#tribu").prop("required", true);
                $("#tribu1").css("display", "block");
                $("#tribu1").prop("required", true);
                
                $("#fiscal").css("display", "none");
                $("#fiscal").prop("required", false);
                $("#fiscal1").css("display", "none");
                $("#fiscal1").prop("required", false);
              
              
                
            }
        })
    </script>
    <script>
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true
            });
        });
        function guardar(){
            var responsabilidad = $("#responsabilidad").val();
            var tercero = <?= $ter[0][0] ?>;
            var form_data={responsabilidad :responsabilidad,tercero:tercero};
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                type:'POST', 
                   url:'jsonPptal/gf_tercerosJson.php?action=10',
                   data: form_data,
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
        
        function guardarT(){
            //var formData = new FormData($("#form")[0]);  

            var responsabilidad_tribu = $("#responsabilidad_tribu").val();
            var tercero = <?= $ter[0][0] ?>;
            var form_data={responsabilidad_tribu :responsabilidad_tribu,tercero:tercero};
            jsShowWindowLoad('Guardando Información...');
            $.ajax({
                   type:'POST', 
                   url:'jsonPptal/gf_tercerosJson.php?action=14',
                   data: form_data,
                   success: function(response){
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
        
        function eliminar(id,iden){
            $("#myModal").modal('show');
            $("#ver").click(function() {
                $("#myModal").modal('hide');

                $.ajax({
                    type: "GET",
                    url: "jsonPptal/gf_tercerosJson.php?action=11&id=" + id+"&iden="+iden,
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