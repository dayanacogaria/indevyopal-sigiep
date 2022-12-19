<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
if(!empty($_REQUEST['t'])){
    $titulo = "Modificar ";
    $row = $con->Listar("SELECT * FROM gf_plantilla WHERE md5(id_unico) ='".$_GET['id']."'");
} else { 
    $titulo = "Listar ";
    $row = $con->Listar("SELECT * FROM gf_plantilla ");
}
?>
<title>Plantilla</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<body>
    <div class="container-fluid text-center">
        <div class="row content">    
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"><?php echo $titulo.' Plantilla'?></h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Encabezado</strong></td>
                                    <td><strong>Ver</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Encabezado</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                    <tr>
                                        <td style="display: none;"></td>
                                        <td><a onclick="javaScript:modificar(<?php echo $row[$i][0]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <td><?php echo ucwords(mb_strtolower($row[$i][1])); ?></td>
                                        <td>
                                            <div id="lblModificar<?php echo $row[$i][0]?>">
                                            <?php if($row[$i][3]==1){ echo 'Sí';} else { echo 'No';}?>
                                            </div>
                                            <div id="modificar<?php echo $row[$i][0]?>" style="display: none">
                                                <select id="encabezado<?php echo $row[$i][0]?>" name="encabezado<?php echo $row[$i][0]?>" class="form-control" style="width: 150px; height: auto">
                                                    <?php if($row[$i][3]==1){ 
                                                        echo '<option value="1">Sí</option>';
                                                        echo '<option value="2">No</option>';
                                                    } else { 
                                                        echo '<option value="2">No</option>';
                                                        echo '<option value="1">Sí</option>';
                                                    }?>
                                                </select>
                                                <a onclick="modificare(<?php echo $row[$i][0]?>)">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                </a>
                                                <a href="#<?php echo $row[$i][0]; ?>" onclick="javascript:cancelar(<?php echo $row[$i][0]; ?>);" >
                                                    <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td><a href="<?php echo 'documentos/plantillas/'.$row[$i][2]; ?>" target="_blank"><i title="Ver" class="glyphicon glyphicon-search"></i></a>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once ('footer.php'); ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
        function modificar(id){
            $("#lblModificar"+id).css('display', 'none');
            $("#modificar"+id).css('display', 'block');
        }
        function cancelar(id){
            $("#lblModificar"+id).css('display', 'block');
            $("#modificar"+id).css('display', 'none');
        }
        function modificare(id){
            let encabezado = $("#encabezado"+id).val();
            var form_data = {estruc: 36, id: id, encabezado: encabezado};
            $.ajax({
                type: "POST",
                url: "jsonPptal/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response);
                    if (response == 1)
                    {
                        $("#mensaje").html('Información Modificada Correctamente');
                        $("#modalMensajes").modal('show');
                    } else
                    {
                        $("#mensaje").html('No Se Ha Podido Modificar La Información');
                        $("#modalMensajes").modal('show');
                    }
                    $("#Aceptar").click(function(){
                        document.location.reload();
                    })
                }
            });
        }
    </script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



