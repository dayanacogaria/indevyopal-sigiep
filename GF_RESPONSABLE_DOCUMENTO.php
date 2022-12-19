<?php
##################################################################################################################################################################
#                                                                                                                           Modificaciones
##################################################################################################################################################################
#24/08/2017 |Erica G. | Añadir campos orden y fechas
##################################################################################################################################################################

require_once ('Conexion/conexion.php');
require_once 'head_listar.php';
$compania       = $_SESSION['compania'];
$id1= $_GET['id1'];
$tipo_doc = "SELECT Id_Unico, Nombre FROM gf_tipo_documento ORDER BY Nombre ASC";
$tipos = $mysqli->query($tipo_doc);
#TERCERO
$tercero ="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
          CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
          (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
          ter.id_unico, ter.numeroidentificacion 
          FROM gf_tercero ter WHERE compania = $compania ORDER BY NOMBRE ASC";
$terceros = $mysqli->query($tercero);
#TIPO RESPONSABLE
$tipo_res = "SELECT Id_Unico, Nombre FROM gf_tipo_responsable ORDER BY Nombre ASC";
$tipos_res = $mysqli->query($tipo_res);
#TIPO RELACION
$tipo_rel = "SELECT id_unico, nombre FROM gg_tipo_relacion ORDER BY nombre ASC";
$tipo_rel= $mysqli->query($tipo_rel);
#LISTAR
$queryCC = "SELECT IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL OR 
            CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)='' ,
            (t.razonsocial),CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos)) AS 'NOMBREC', 
            rd.tercero, rd.tipodocumento, rd.tiporesponsable, 
            td.nombre, tr.nombre, rd.tipo_relacion, trel.id_unico, trel.nombre, t.numeroidentificacion , 
            rd.orden, DATE_FORMAT(rd.fecha_inicio, '%d/%m/%Y'), DATE_FORMAT(rd.fecha_fin, '%d/%m/%Y'),  rd.fecha_inicio, rd.fecha_fin, rd.id_unico   
    FROM gf_responsable_documento rd 
    LEFT JOIN gf_tercero t ON rd.tercero = t.id_unico 
    LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico 
    LEFT JOIN gf_tipo_responsable tr ON rd.tiporesponsable= tr.id_unico 
    LEFT JOIN gg_tipo_relacion trel ON trel.id_unico = rd.tipo_relacion 
    WHERE md5(rd.tipodocumento)= '$id1'";
$resultado = $mysqli->query($queryCC);

$idTipoDoc = $_GET['id1'];
$queryT = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE md5(id_unico) = '$idTipoDoc'";
$tipoD = $mysqli->query($queryT);
$rowD = mysqli_fetch_assoc($tipoD);
?>

    <title>Responsable documento</title>
   
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
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
    label #tercero-error, #TipoResponsable-error, #tipoRel-error, #orden-error, #fechaI-error{
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;

    }
    body{
        font-size: 12px;
    }
    </style>
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
       
        
        $("#fechaI").datepicker({changeMonth: true,}).val();
        $("#fechaF").datepicker({changeMonth: true}).val();
        $("#fechaIM").datepicker({changeMonth: true}).val();
        $("#fechaFM").datepicker({changeMonth: true}).val();
        
        
});
</script>
</head>
<body>
    <div class="container-fluid text-center">
	    <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top: 0px">Registrar Responsable Documento</h2>
                <a href="Modificar_GF_TIPO_DOCUMENTO.php?id_cond=<?php echo $_GET['id1'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; margin-top:-5px;vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-20px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo documento:<?php echo ucwords((strtolower($rowD['nombre']))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">         
                    <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarResponsableJson.php">
                        <input type="hidden" id="TipoDocumento" value="<?php echo $rowD['id_unico']?>" name="TipoDocumento">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top:-20px; margin-left:0px">
                            <div class="form-group form-inline " style="margin-left:0px">
                                <label for="tercero" class="control-label" style="width:100px"><strong style="color:#03C1FB;">*</strong>Tercero :</label>
                                <select class="select2_single form-control" style="width:180px"  name="tercero" id="tercero"  title="Seleccione tercero" required >
                                    <option value="">Tercero</option>
                                    <?php while($row = mysqli_fetch_assoc($terceros)){?>
                                    <option value="<?php echo $row['id_unico'] ?>"><?php echo ucwords(mb_strtolower($row['NOMBRE'].' ('.$row["numeroidentificacion"].')'));}?></option>;
                                </select> 
                            </div>
                            <div class="form-group form-inline " style="margin-left: 10px;">
                                <label for="TipoResponsable" class="control-label" style="width:100px"><strong style="color:#03C1FB;">*</strong>Tipo Responsable :</label>
                                <select class="select2_single form-control" style="width:180px"  name="TipoResponsable" id="TipoResponsable" title="Seleccione tipo responsable" required >
                                     <option value="">Tipo Responsable</option>
                                     <?php while($row3 = mysqli_fetch_assoc($tipos_res)){?>
                                     <option value="<?php echo $row3['Id_Unico'] ?>"><?php echo ucwords(mb_strtolower($row3['Nombre']));}?></option>;
                                 </select>
                            </div>
                            <div class="form-group form-inline " style="margin-left: 10px;">
                                <label for="tipoRel" class="control-label" style="width:100px"><strong style="color:#03C1FB;">*</strong>Tipo Relación :</label>
                                <select class="select2_single form-control" style="width:180px" name="tipoRel" id="tipoRel" title="Seleccione tipo relación"  required >
                                     <option value="">Tipo Relación</option>
                                     <?php while($row4 = mysqli_fetch_row($tipo_rel)){?>
                                     <option value="<?php echo $row4[0] ?>"><?php echo ucwords(mb_strtolower($row4[1]));}?></option>;
                                </select>
                                <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top:10px"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top:-20px; margin-left:-10px">
                             <div class="form-group form-inline " style="margin-left: 10px;">
                                <label for="orden" class="control-label" style="width:100px"><strong style="color:#03C1FB;">*</strong>Orden :</label>
                                <input type="text" name="orden" id="orden" class="form-control form-inline " required="required" style="width:180px; display: inline" title="Ingrese orden" placeholder="Orden"/>
                            </div>
                             <div class="form-group form-inline " style="margin-left: 10px;">
                                <label for="fechaI" class="control-label" style="width:100px"><strong style="color:#03C1FB;">*</strong>Fecha Inicio :</label>
                                <input type="text" name="fechaI" id="fechaI" class="form-control form-inline " required="required" style="width:180px; display: inline" title="Seleccione Fecha Inicio" placeholder="Fecha Inicio"/>
                            </div>
                             <div class="form-group form-inline " style="margin-left: 10px;">
                                <label for="fechaF" class="control-label" style="width:100px"><strong style="color:#03C1FB;"></strong>Fecha Fin :</label>
                                <input type="text" name="fechaF" id="fechaF" class="form-control form-inline " style="width:180px; display: inline" title="Seleccione Fecha Fin" placeholder="Fecha Fin"/>
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
                                    <td><strong>Nombre Tercero</strong></td>
                                    <td><strong>Tipo Responsable</strong></td>
                                    <td><strong>Tipo Relación</strong></td>
                                    <td><strong>Orden</strong></td>
                                    <td><strong>Fecha Inicio</strong></td>
                                    <td><strong>Fecha Fin</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre Tercero</th>
                                    <th>Tipo Responsable</th>
                                    <th>Tipo Relación</th>
                                    <th>Orden</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td><a  href="#" onclick='javascript:eliminar(<?php echo $row[15]?>);'><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick='modificarModal(<?php echo $row[15].','.$row[1].','.$row[2].','.$row[3].','. $row[6].',"'.$row[10].'",'.'"'.$row[11].'",','"'.$row[12].'"'?>)'><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                    <td><?php echo (ucwords(mb_strtolower($row[0].'('.$row[9].')'))); ?></td>
                                    <td><?php echo (ucwords(mb_strtolower($row[5]))); ?></td>
                                    <td><?php echo  (ucwords(mb_strtolower($row[8]))); ?></td>
                                    <td><?php echo  $row[10]; ?></td>
                                    <td><?php echo  $row[11]; ?></td>
                                    <td><?php echo  $row[12]; ?></td>
                               </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
	    </div>
    </div>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_single").select2({
            allowClear: true,
        });
        });
    </script>
    <!--  MODAL y opcion  MODIFICAR  informacion  -->
    <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Modificar</h4>
                </div>
                <?php
                #TERCERO
                $ter="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR 
                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                    (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                ter.id_unico, ter.numeroidentificacion FROM gf_tercero ter WHERE ter.compania = $compania ORDER BY NOMBRE ASC";
                $tercero = $mysqli->query($ter);
                #TIPO RESPONSABLE
                $tipoR = "SELECT Id_Unico, Nombre FROM gf_tipo_responsable ORDER BY Nombre ASC";
                $tiposR = $mysqli->query($tipo_res);
                #TIPO RELACION
                $tipoRel = "SELECT id_unico, nombre FROM gg_tipo_relacion ORDER BY nombre ASC";
                $tipoRel= $mysqli->query($tipoRel);
                ?>
                <div class="modal-body ">
                    <form  name="form" method="POST" action="javascript:modificarItem()">
                        <input type="hidden" name="tipoDoc" id="tipoDoc">
                        <input type="hidden" name="tA" id="tA">
                        <input type="hidden" name="tRpA" id="tRpA">
                        <input type="hidden" name="TRelA" id="TRelA">
                        <input type="hidden" name="idM" id="idM">
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tercerom" id="tercerom" class="select2_single form-control" title="Seleccione tercero" required>
                                <?php while ($modTer = mysqli_fetch_row($tercero)) { ?>
                                      <option value="<?php echo $modTer[1]; ?>">
                                        <?php echo ucwords((mb_strtolower($modTer[0]).'('.$modTer[2].')')); ?>
                                      </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Responsable:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoResp" id="tipoResp" class="select2_single form-control" title="Seleccione tipo responsable" required>
                                <?php while ($modTr = mysqli_fetch_row($tiposR)) { ?>
                                      <option value="<?php echo $modTr[0]; ?>">
                                        <?php echo ucwords((mb_strtolower($modTr[1]))); ?>
                                      </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Relacion:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="TipoRela" id="TipoRela" class="select2_single form-control" title="Seleccione tipo relación" required>
                                <?php while ($modRel = mysqli_fetch_row($tipoRel)) { ?>
                                      <option value="<?php echo $modRel[0]; ?>">
                                        <?php echo ucwords((mb_strtolower($modRel[1]))); ?>
                                      </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Orden:</label>
                            <input  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="ordenM" id="ordenM" class="form-control" title="Seleccione Orden" required/>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Fecha Inicio:</label>
                            <input  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="fechaIM" id="fechaIM" class="form-control" title="Fecha Inicio" required/>
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;"></strong>Fecha Fin:</label>
                            <input  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="fechaFM" id="fechaFM" class="form-control" title="Fecha Fin" />
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
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de responsable documento?</p>
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
                    <p>El registro ingresado ya existe..</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function eliminar(id)  {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarResponsableDocJson.php?id="+id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result==true) {
                            $("#myModal1").modal('show');
                        } else {
                            $("#myModal2").modal('show');
                        }
                    }
                });
            });
        }

        function modificarModal(id,tercero,tipodoc,tipoResp, tipoRel, orden, fechaI, fechaF){
            $("#tercerom").val(tercero);
            $("#tA").val(tercero);
            $("#tipoResp").val(tipoResp);
            $("#tRpA").val(tipoResp);
            $("#tipoDoc").val(tipodoc);
            $("#TipoRela").val(tipoRel);
            $("#TRelA").val(tipoRel);
            $("#ordenM").val(orden);
            $("#fechaIM").val(fechaI);
            $("#fechaFM").val(fechaF);
            $("#idM").val(id);
            
            $("#myModalUpdate").modal('show');
        }

        function modificarItem() {
            var result = '';
            var t= document.getElementById('tercerom').value;
            var ta= document.getElementById('tA').value;
            var tDoc= document.getElementById('tipoDoc').value;
            var tRes= document.getElementById('tipoResp').value;
            var tResA= document.getElementById('tRpA').value;
            var tRel= document.getElementById('TipoRela').value;
            var tRelA= document.getElementById('TRelA').value;
            var orden= document.getElementById('ordenM').value;
            var fechaI= document.getElementById('fechaIM').value;
            var fechaF= document.getElementById('fechaFM').value;
            var id= document.getElementById('idM').value;
      
            $.ajax({
                type:"GET",
                url:"json/modificarResponsableDocJson.php?id="+id+ "&t="+t+"&ta="+ta+"&tDoc="+tDoc+"&tRes="+tRes+"&tResA="+tResA+"&tRel="+tRel+"&tRelA="+tRelA+"&orden="+orden+"&fechaI="+fechaI+"&fechaF="+fechaF,
                success: function (data) {
                    result = JSON.parse(data);                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                    if(result=='3'){
                        $("#myModalUpdate").modal('hide');
                        $("#myModal7").modal('show');
                        $("#ver7").click(function(){
                            $("#myModal7").modal('hide');
                            document.location = 'GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo $id1;?>';
                        });
                    }  else {
                        if(result==true) {
                            $("#myModalUpdate").modal('hide');
                            $("#myModal5").modal('show');
                            $("#ver5").click(function(){
                                $("#myModal5").modal('hide');
                                document.location = 'GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo $id1;?>';
                            });
                        }else{
                            $("#myModalUpdate").modal('hide');
                            $("#myModal6").modal('show');
                            $("#ver6").click(function(){
                                $("#myModal6").modal('hide');
                                document.location = 'GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo $id1;?>';
                            });
                        }
                    }
                }
            });
        }

        function modal() {
            $("#myModal").modal('show');
        }

        $('#ver1').click(function(){
            document.location = 'GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo $id1;?>';
        });
    
        $('#ver2').click(function(){
            document.location = 'GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo $id1;?>';
        });
    </script>
</body>
</html>

