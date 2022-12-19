<?php
require_once ('Conexion/conexion.php');
@session_start();
$compania = $_SESSION['compania'];
$id= $_GET['id'];
//DEPENDENCIA
$dep= "SELECT id_unico, nombre FROM gf_dependencia WHERE md5(id_unico)='$id' ";
$depen= $mysqli->query($dep);
$rowD = mysqli_fetch_row($depen);
//TERCERO
$ter="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', "
        . "ter.id_unico, ter.numeroidentificacion FROM gf_tercero ter WHERE ter.compania = $compania ORDER BY NOMBRE ASC";
$tercero = $mysqli->query($ter);
//ESTADO
$est= "SELECT id_unico, nombre FROM gs_estado_usuario ORDER BY nombre ASC";
$esta= $mysqli->query($est);


//LISTAR

$resul = "SELECT IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)='' ,(t.razonsocial),CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)) AS 'NOMBRE', t.id_unico, t.numeroidentificacion, dr.dependencia, dr.responsable, dr.movimiento, dr.estado, eu.id_unico, eu.nombre FROM gf_dependencia_responsable dr LEFT JOIN gf_tercero t ON dr.responsable = t.id_unico LEFT JOIN gs_estado_usuario eu ON dr.estado = eu.id_unico WHERE md5(dr.dependencia)='$id' ";
$resultado = $mysqli->query($resul);

require_once 'head_listar.php';
?>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
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
    <style>
        label#responsable-error, #estado-error{
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;
        }

        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        .shadow {box-shadow: 1px 1px 1px 1px gray;color:#fff; border-color:#1075C1;}
    </style>
    <title>Dependencia Responsable</title>
</head>
<body>
    <div class="container-fluid text-center">
	    <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Dependencia Responsable</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Dependencia:<?php echo ucwords((strtolower($rowD[1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_DEPENDENCIARESJson.php">
                        <input type="hidden" id="dependencia" value="<?php echo $rowD[0]?>" name="dependencia">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline">
                            <input type="hidden" name="responsable" id="responsable" title="Seleccione responsable" required="required">
                            <label for="responsable" class="control-label col-sm-2" style="width:100px; display: inline; margin-left: 20px;"><strong style="color:#03C1FB;">*</strong>Responsable:</label>
                            <div class="form-group form-inline " style="margin-top: -15px; margin-left: 30px;">
                                <select name="responsable1" id="responsable1"  required="required" class="select2_single form-control" tabindex="-1" style="display:inline-block; width:250px; margin-bottom:15px;  text-align-last:left;" title="Seleccione Responsable" onchange="llenar();" >
                                    <option value="">Responsable</option>
                                    <?php while($row = mysqli_fetch_row($tercero)){ ?>
                                    <option value="<?php echo $row[1] ?>"><?php echo ucwords((strtolower($row[0].' ('.$row[2].')'))); } ?></option>;
                                </select>
                            </div>
                            <div class="form-group form-inline " style="margin-top: -24px; margin-left: 40px">
                                <label for="movimiento" class="control-label" style="display: inline-block; margin-top: -13px;"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                                <input  type="radio" name="movimiento" id="movimiento"  value="1" >SI
                                <input  type="radio" name="movimiento" id="movimiento" value="2" checked>NO
                            </div>
                            <div class="form-group form-inline " style= "margin-left:60px">
                               <label for="estado" class="control-label col-sm-2 " ><strong style="color:#03C1FB;">*</strong>Estado:</label>
                                <select name="estado" id="estado"  class="form-control"  title="Seleccione estado" required style=" width: 200px; height:35px; margin-left: 10px;">
                                    <option value="">Estado</option>
                                    <?php while($rowE = mysqli_fetch_row($esta)){?>
                                    <option value="<?php echo $rowE[0] ?>"><?php echo ucwords((strtolower($rowE[1]))); } ?></option>;
                                </select>
                                <button type="submit" class="btn btn-primary sombra glyphicon glyphicon-floppy-disk" style=" margin-top: 2px; margin-bottom: 10px; "></button>
                                <input type="hidden" name="MM_insert" >
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
                                    <td><strong>Responsable</strong></td>
                                    <td><strong>Movimiento</strong></td>
                                    <td><strong>Estado</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Responsable</th>
                                    <th>Movimiento</th>
                                    <th>Estado</th>
                                </tr>
                           </thead>
                           <tbody>
                           <?php
                           while($row = mysqli_fetch_row($resultado)){?>
                               <tr>
                                    <td style="display: none;"><?php echo $row[3]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[3].','.$row[4].','.$row[5].','.$row[6]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="modificarModal(<?php echo $row[3].','.$row[4].','.$row[5].','.$row[6]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(strtolower($row[0].'('.$row[2].')'));?></td>
                                    <td>
                                        <?php 
                                        switch ($row[5]) {
                                            case 1: 
                                                echo '<label style="font-weight:normal" id="labelPropietario'.$row[3].$row[4].'">Sí</label>';
                                                break;
                                           case 2:
                                               echo '<label style="font-weight:normal" id="labelPropietario'.$row[3].$row[4].'">No</label>';
                                               break;
                                        } ?>
                                    </td>  
                                    <td><?php echo ucwords(strtolower($row[8]));?></td>
                               </tr>
                           <?php } ?>
                           </tbody>
                       </table>
                   </div>
               </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Dependencia Responsable?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
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
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar</h4>
                </div>
                <?php
                $ter="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,(ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', "
                    . "ter.id_unico, ter.numeroidentificacion FROM gf_tercero ter WHERE ter.compania = $compania ORDER BY NOMBRE ASC";
                $tercero = $mysqli->query($ter);
                $est = "SELECT id_unico, nombre FROM gs_estado_usuario ORDER BY nombre ASC";
                $estado= $mysqli->query($est);
                ?>
                <div class="modal-body ">
                    <form  name="form" method="POST" action="javascript:modificarItem()">
                        <input type="hidden" name="dependenciaM" id="dependenciaM">
                        <input type="hidden" name="respA" id="respA">
                        <input type="hidden" name="movA" id="movA">
                        <input type="hidden" name="estadoA" id="estadoA">
                        <div class="form-group" style="margin-top: 13px;"
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Responsable:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="responsableM" id="responsableM" class="select2_single form-control" title="Seleccione responsable" required>
                                <?php while ($modTer = mysqli_fetch_row($tercero)) { ?>
                                    <option value="<?php echo $modTer[1]; ?>">
                                        <?php echo ucwords((mb_strtolower($modTer[0]).'('.$modTer[2].')')); ?>
                                    </option>
                                    <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label for="valor"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Movimiento:</label></td>
                            <div align="left" style="display:inline-block; width:250px; margin-bottom:15px; height:40px">
                                <input  type="radio" name="movM" id="movM1"  value="1" checked>SI
                                <input  type="radio" name="movM" id="movM2" value="2" checked>NO
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -30px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:36px" name="estadoM" id="estadoM" class="form-control" title="Seleccione estado" required>
                                <?php while ($modTr = mysqli_fetch_row($estado)) { ?>
                                    <option value="<?php echo $modTr[0]; ?>">
                                        <?php echo ucwords((mb_strtolower($modTr[1]))); ?>
                                    </option>
                                    <?php
                                } ?>
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
    <!--  MODAL para los mensajes del  modificar -->
    <div class="modal fade" id="myModal5" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal6" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>La información no se ha podido modificar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal7" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Registro ingresado ya existe.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        function llenar(){
            var tercero = document.getElementById('responsable1').value;
            document.getElementById('responsable').value= tercero;
        }

        $(".select2_single").select2({
            allowClear: true
        });

        function eliminar(dep, res, mov, est) {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminar_GF_DEPENDENCIARESJson.php?dep="+dep+"&res="+res+"&mov="+mov+"&est="+est,
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

        function modificarModal(dep, res, mov, est){
            $("#responsableM").val(res);
            $("#estadoM").val(est);
            document.getElementById('dependenciaM').value =dep;
            document.getElementById('respA').value =res;
            document.getElementById('estadoA').value =est;
            document.getElementById('movA').value =mov;

            if(mov===1){
                document.getElementsByName("movM")[0].checked = true;
            }else {
                document.getElementsByName("movM")[1].checked = true;
            }
              $("#myModalUpdate").modal('show');
        }
      
        function modificarItem() {
            var result = '';
            var respA= document.getElementById('respA').value;
            var respo= document.getElementById('responsableM').value;
            var depend= document.getElementById('dependenciaM').value;
            var estadA= document.getElementById('estadoA').value;
            var est= document.getElementById('estadoM').value;
            var movA = document.getElementById('movA').value;
            if (document.getElementById('movM1').checked) {
                var movi='1';
            } else {
                var movi='2';
            }
            $.ajax({
                type:"GET",
                url:"json/modificar_GF_DEPENDENCIARESJson.php?respA="+respA+"&respo="+respo+"&depend="+depend+"&estadA="+estadA+"&est="+est+"&movA="+movA+"&movi="+movi,
                success: function (data) {
                    result = JSON.parse(data);
                    console.log(result);
                    if(result=='3'){
                        $("#myModalUpdate").modal('hide');
                        $("#myModal7").modal('show');
                        $("#ver7").click(function(){
                            $("#myModal7").modal('hide');
                            document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
                        });
                    } else {
                        if(result==true){
                            $("#myModalUpdate").modal('hide');
                            $("#myModal5").modal('show');
                            $("#ver5").click(function(){
                                $("#myModal5").modal('hide');
                                document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
                            });
                        } else {
                            $("#myModalUpdate").modal('hide');
                            $("#myModal6").modal('show');
                            $("#ver6").click(function(){
                                $("#myModal6").modal('hide');
                                document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
                            });
                        }
                    }
                }
            });
        }

        $('#btnModifico').click(function(){
            document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
        });

        $('#btnNoModifico').click(function(){
            document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
        });

        function modal() {
            $("#myModal").modal('show');
        }

        $('#ver1').click(function(){
            document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
        });

        $('#ver2').click(function(){
            document.location = 'GF_DEPENDENCIA_RESPONSABLE.php?id=<?php echo $id;?>';
        });
    </script>
</body>
</html>


