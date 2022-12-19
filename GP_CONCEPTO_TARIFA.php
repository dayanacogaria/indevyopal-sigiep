<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once 'head_listar.php';


$id     = $_GET['id'];
$con    = new ConexionPDO(); 
#Concepto
$concepto   = "SELECT id_unico, nombre FROM gp_concepto WHERE md5(id_unico)='$id' ";
$concepto   = $mysqli->query($concepto);
$rowCon     = mysqli_fetch_row($concepto);
#Tarifa
$tarifa     = "SELECT t.id_unico, tt.nombre, t.valor FROM gp_tarifa t 
            LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico 
            ORDER BY tt.nombre ASC";
$tarifa     = $mysqli->query($tarifa);

#Unidad
$unidad     = "SELECT eu.id_unico, uf.nombre FROM gf_elemento_unidad eu 
            LEFT JOIN gf_unidad_factor uf ON eu.unidad_empaque = uf.id_unico";
$unidad     = $mysqli->query($unidad);
#LISTAR
$resul      = "SELECT  ct.id_unico, ct.nombre, t.id_unico, tt.nombre, t.valor, 
        ct.concepto , uf.nombre, ct.elemento_unidad
            FROM gp_concepto_tarifa ct 
            LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
            LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa =tt.id_unico 
            LEFT JOIN gf_elemento_unidad eu ON ct.elemento_unidad = eu.id_unico 
            LEFT JOIN gf_unidad_factor uf ON eu.unidad_empaque = uf.id_unico
            WHERE md5(ct.concepto)='$id' ";
$resultado  = $mysqli->query($resul);


?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Concepto Tarifa</title>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Concepto Tarifa</h2>
                <a href="Modificar_GP_CONCEPTO.php?id=<?=$id?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Concepto:<?php echo ucwords((mb_strtolower($rowCon[1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_CONCEPTO_TARIFAJson.php">
                        <input type="hidden" id="concepto" value="<?= $rowCon[0] ?>" name="concepto">
                        <input type="hidden" id="id" value="<?= $rowCon[0] ?>" name="id">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-left: 50px">
                            <div class="form-group form-inline " style="    margin-top: -10px;">
                                <label for="nombre" class="control-label col-sm-2" style="width:100px; display: inline;"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                                <input class="form-control" style="display:inline; width: 200px; height: 32px" id="nombre" name="nombre" placeholder="Nombre" title="Ingrese Nombre" onkeypress="return txtValida(event, 'car')" autocomplete="off">  
                            </div>
                            <div class="form-group form-inline ">
                                <label for="tarifa" class="control-label col-sm-2" style="width:100px; display: inline;"><strong style="color:#03C1FB;">*</strong>Tarifa:</label>
                                <select name="tarifa" id="tarifa"  class="select2_single form-control " title="Seleccione tarifa" required  style="display: inline; width: 200px">
                                    <option value="">Tarifa</option>
                                    <?php while ($rowT = mysqli_fetch_row($tarifa)) { 
                                        echo '<option value="'.$rowT[0].'">'.ucwords(mb_strtolower($rowT[1])).' - '.number_format($rowT[2], 2).'</option>';
                                    } ?>
                                </select> 

                            </div>
                            <div class="form-group form-inline ">
                                <label for="unidad" class="control-label col-sm-2" style="width:100px; display: inline;"><strong style="color:#03C1FB;">*</strong>Unidad:</label>
                                <select name="unidad" id="unidad"  class="select2_single form-control" title="Seleccione Unidad" required  style="display: inline; width: 200px">
                                    <option value="">Unidad</option>
                                    <?php 
                                    
                                    while ($rowU = mysqli_fetch_row($unidad)) { ?>
                                        <option value="<?php echo $rowU[0] ?>"><?php echo ucwords(mb_strtolower($rowU[1]));
                                    } ?></option>;
                                </select> 
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: 0px; margin-bottom: 10px; ">Guardar</button>

                            </div>
                        </div>
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Tarifa</strong></td>
                                    <td><strong>Unidad</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Tarifa</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td style="display: none;"><?=$row[0] ?></td>    
                                        <td><a  href="#" onclick="javascript:eliminar(<?=$row[0] ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        </td>
                                        <td><?= $row[1]; ?></td>
                                        <td><?= $row[3] . ' - ' . $row[4]; ?></td> 
                                        <td><?= $row[6]; ?></td> 
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="msjm1"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="msjm2"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                </div>
                <div class="modal-body ">
                    <form  name="form" method="POST" action="javascript:modificarItem()">
                        <input type="hidden" name="idm" id="idm">
                        <input type="hidden" name="conceptom" id="conceptom">
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="nombrem" id="nombrem" class="form-control" title="Ingrese Nombre" required onkeypress="return txtValida(event, 'car')">
                        </div>
                        <div class="form-group" style="margin-top: 13px; margin-left: -10px">
                            <label for="tarifa"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Tarifa:</label></td>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tarifam" id="tarifam" class="select2_single form-control" title="Seleccione tarifa" required>
                                <?php
                                $tarifam = "SELECT t.id_unico, tt.nombre, t.valor FROM gp_tarifa t "
                                        . "LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico "
                                        . "ORDER BY tt.nombre ASC";
                                $tarifam = $mysqli->query($tarifam);
                                while ($rowTa = mysqli_fetch_row($tarifam)) {
                                    ?>
                                    <option value="<?php echo $rowTa[0]; ?>">
                                    <?php echo ucwords((mb_strtolower($rowTa[1]) . ' - ' . $rowTa[2])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px; margin-left: -10px">
                            <label for="tarifa"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Tarifa:</label></td>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tarifam" id="tarifam" class="select2_single form-control" title="Seleccione tarifa" required>
                                <?php
                                $tarifam = "SELECT t.id_unico, tt.nombre, t.valor FROM gp_tarifa t "
                                        . "LEFT JOIN gp_tipo_tarifa tt ON t.tipo_tarifa = tt.id_unico "
                                        . "ORDER BY tt.nombre ASC";
                                $tarifam = $mysqli->query($tarifam);
                                while ($rowTa = mysqli_fetch_row($tarifam)) {
                                    ?>
                                    <option value="<?php echo $rowTa[0]; ?>">
                                    <?php echo ucwords((mb_strtolower($rowTa[1]) . ' - ' . $rowTa[2])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                </div>

                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
                </form>
            </div>
        </div>
    </div>

<?php require_once 'footer.php'; ?>

<script src="js/bootstrap.min.js"></script>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
</script>
    <script type="text/javascript">
        function eliminar(id){
            var result = '';
            $("#msjm1").html('¿Desea Eliminar El Registro Seleccionado?');
            $("#modal1").modal('show');
            $("#ver").click(function () {
                $("#modal1").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminar_GP_CONCEPTO_TARIFAJson.php?id=" + id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result == true) {
                            $("#msjm2").html('Registro Eliminado Correctamente');
                            $("#modal2").modal('show');
                            $("#ver2").click(function () {
                                $("#modal2").modal('hide');
                                document.location.reload();
                            });
                        } else {
                            $("#msjm2").html('No Se Ha Podido Eliminar La Información');
                            $("#modal2").modal('show');
                            $("#ver2").click(function () {
                                $("#modal2").modal('hide');
                                document.location.reload();
                            });
                        }
                    }
                });
            });
        }
        function modificarModal(id, nombre, tarifa, concepto) {

            $("#idm").val(id);
            $("#nombrem").val(nombre);
            $("#tarifam").val(tarifa);
            $("#conceptom").val(concepto);

            $("#myModalUpdate").modal('show');
        }

        function modificarItem()
        {
            var result = '';
            var id = document.getElementById('idm').value;
            var nombre = document.getElementById('nombrem').value;
            var tarifa = document.getElementById('tarifam').value;
            var concepto = document.getElementById('conceptom').value;

            $.ajax({
                type: "GET",
                url: "json/modificar_GP_CONCEPTO_TARIFAJson.php?id=" + id + "&nombre=" + nombre + "&tarifa=" + tarifa + "&concepto=" + concepto,
                success: function (data) {
                    result = JSON.parse(data);
                    $("#myModalUpdate").modal('hide');
                    if (result == '1') {
                        $("#msjm2").html('Información modificada correctamente');
                        $("#modal2").modal('show');
                        $("#ver2").click(function () {
                            document.location.reload();
                        });
                    } else {
                        if (result == '3') {
                            $("#msjm2").html('El registro ingresado ya existe.');
                            $("#modal2").modal('show');
                            $("#ver2").click(function () {
                               document.location.reload();
                            });
                        } else {
                            $("#msjm2").html('La información no se ha podido modificar.');
                            $("#modal2").modal('show');
                            $("#ver2").click(function () {
                                document.location.reload();
                            });
                        }
                    }
                }
            });
        }
    </script> 



</body>
</html>


