<?php
    #####################################################################################
    # ********************************* Modificaciones *********************************#
    #####################################################################################
    #17/12/2018 | Nestor B. | Archivo Creado
    ####/################################################################################
    require_once('Conexion/conexion.php');
    require_once('Conexion/ConexionPDO.php');
    require_once('head.php');
    $con    = new ConexionPDO();
    $anno   = $_SESSION['anno'];
    $compania   = $_SESSION['compania'];

    @$id_PQR  = $_REQUEST['idP'];
    @$id      = $_REQUEST['idD']; 
    if(empty($_GET['idD'])) {
        $titulo = "Registrar ";
        $titulo2= ".";
    } else {
        $titulo = "Modificar ";
        $id     = $_GET['idD'];
        $row    = "SELECT  dtp.id_pqr,
                                        dtp.id_servicio,
                                        ts.nombre,
                                        dtp.id_descripcion,
                                        d.descripcion,
                                        dtp.observaciones,
                                        dtp.id_clase,
                                        c.nombre,
                                        dtp.id_unico_asociado,
                                        dtp.fecha,
                                        pqr.fecha_hora,
                                        dtp.id_unico_asociado,
                                        c.indicador_cierre
                                FROM gpqr_detalle_pqr dtp
                                LEFT JOIN gpqr_descripcion d ON dtp.id_descripcion = d.id_unico
                                LEFT JOIN gpqr_clase c ON dtp.id_clase = c.id_unico
                                LEFT JOIN gpqr_pqr pqr ON dtp.id_pqr = pqr.id_unico
                                LEFT JOIN gp_tipo_servicio ts ON dtp.id_servicio = ts.id_unico
                                WHERE dtp.id_unico='$id'";
        $resDTP = $mysqli->query($row);
        $DetP = mysqli_fetch_row($resDTP); 

        $fecDP = date('d/m/Y', strtotime($DetP[10]));
    }

    $ConPQR = "SELECT fecha_hora FROM gpqr_pqr WHERE id_unico = '$id_PQR'";
    $res = $mysqli->query($ConPQR);
    $rowP = mysqli_fetch_row($res);

    $fecP = date('d/m/Y',strtotime($rowP[0]));
?>

  <!--Titulo de la página-->
<title>Detalle PQR</title>
</head>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label #sltAfavor-error, #sltClase-error, #txtruta-error, #sltDesc-error, #sltfecha-error,
        #sltTipoS-error {
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
        $("#sltfecha").datepicker({changeMonth: true,}).val();
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
                <h2 align="center" class="tituloform"><?php echo $titulo.' Detalle PQR'?></h2>
                <a href="GPQR_PQR.php?id=<?php echo $id_PQR ?>&mod=1" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px">.</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; line-height: normal;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                        <p align="center" style="margin-bottom: 15px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <?php   if(empty($id)){ ?>
                                    <input type="hidden" name="idPQR" id="idPQR" value="<?php echo $id_PQR ?>">
                                    <!-- Guarda la Fecha del encbezado del PQR  -->
                                    <input type="hidden" name="fechaPQR" id="fechaPQR" value="<?php echo $fecP ?>">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <label for="sltTipoS" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Servicio:</label>
                                        <select required id="sltTipoS" name="sltTipoS" class="select2_single form-control"  style="height: 35px;"  title="Seleccione un Tipo de Servicio">
                                            <option value="">Tipo Servicio</option>
                                           <?php
                                                $unidadV = "SELECT id_unidad_vivienda FROM gpqr_pqr WHERE id_unico = '$id_PQR'";

                                                $resuni = $mysqli->query($unidadV);
                                                $rowU = mysqli_fetch_row($resuni);
                                                if(!empty($rowU[0])){
                                                    $uniVi  =   "SELECT uvs.unidad_vivienda 
                                                                FROM gp_unidad_vivienda_servicio uvs 
                                                                LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                                                WHERE uvms.id_unico = '$rowU[0]'";
                                                    $resuniV = $mysqli->query($uniVi);
                                                    $rowUV = mysqli_fetch_row($resuniV); 

                                                    $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts
                                                                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.tipo_servicio = ts.id_unico
                                                                  
                                                                    WHERE uvs.unidad_vivienda = '$rowUV[0]'";
                                                }else{
                                                    $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts";
                                                }
                                                
                                                $resS = $mysqli->query($row);
                                                while($i = mysqli_fetch_row($resS)) {
                                                    echo '<option value="'.$i[0].'">'.ucwords(mb_strtolower($i[1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="sltDesc" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                        <select required id="sltDesc" name="sltDesc" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Descripción">
                                            <option value="">Descripción</option>
                                           <?php
                                           $row = $con->Listar("SELECT  id_unico, descripcion
                                            FROM gpqr_descripcion
                                            WHERE compania = '$compania'
                                            ORDER BY descripcion ASC ");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>

                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="sltfecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                        <input type="text" name="sltfecha" id="sltfecha" class="form-control" required="required" placeholder="Fecha" title="Ingrese la Fecha" readonly>
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="txtruta" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Archivo:</label>
                                        
                                        <input type="file" class="form-control col-sm-1" id="txtruta" name="txtruta" title="Seleccione un Archivo" style="width:300px;height: 40px" required="">                          
                                       
                                    </div>
                                    
                                   
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="sltDetA" class="col-sm-5 control-label">Detalle Asociado:</label>
                                        <select  id="sltDetA" name="sltDetA" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Ciudad">
                                            <option value="">Detalle Asociado</option>
                                           <?php
                                           $row = $con->Listar("SELECT det.id_unico, des.descripcion FROM gpqr_detalle_pqr det left join gpqr_descripcion des on des.id_unico=det.id_descripcion where det.id_pqr= '$id_PQR'");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="sltClase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                        <select required id="sltClase" name="sltClase" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase">
                                            <option value="">Clase</option>
                                           <?php
                                                $row = $con->Listar("SELECT id_unico, nombre FROM gpqr_clase  WHERE compania= '$compania'");
                                           
                                                for ($i = 0; $i < count($row); $i++) {

                                                    echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: -10px; ">
                                        <label for="sltAfavor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>A Favor:</label>
                                        <select required id="sltAfavor" name="sltAfavor" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Opción" disabled>
                                            <option value="">A Favor</option>
                                           <?php
                                           $row = $con->Listar("SELECT  id_unico, nombre
                                            FROM gpqr_afavor 
                                            WHERE compania = '$compania'
                                            ORDER BY nombre ASC ");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="txtObser" class="col-sm-5 control-label">Observaciones:</label>
                                        <textarea type="text" name="txtObser" id="txtObser" class="form-control"  placeholder="Ingrese las Observaciones" ></textarea>
                                    </div>
                                    
                                    <div class="form-group" style="margin-top: 20px; ">
                                        <label for="no" class="col-sm-5 control-label"></label>
                                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                                    </div>
                                    <input type="hidden" name="MM_insert" >
                                    <input type="hidden" name="txtact" id="txtact" value="<?php echo $actividad; ?>" >
                                    
                        <?php   }else{ ?>
                         
                                    <input type="hidden" name="idPQR" id="idPQR" value="<?php echo $DetP[0] ?>">
                                    <input type="hidden" name="idDPQR" id="idDPQR" value="<?php echo $id ?>">
                                    <!-- Guarda la Fecha del encbezado del PQR  -->
                                    <input type="hidden" name="fechaPQR" id="fechaPQR" value="<?php echo $fecDP ?>">
                                    <div class="form-group" style="margin-bottom:0px;">
                                        <label for="sltTipoS" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Servicio:</label>
                                        <select required id="sltTipoS" name="sltTipoS" class="select2_single form-control"  style="height: 35px;"  title="Seleccione un Tipo de Servicio">
                                            <option value="<?php echo $DetP[1] ?>"><?php echo $DetP[2] ?></option>
                                           <?php
                                                $unidadV = "SELECT id_unidad_vivienda FROM gpqr_pqr WHERE id_unico = '$id_PQR'";

                                                $resuni = $mysqli->query($unidadV);
                                                $rowU = mysqli_fetch_row($resuni);
                                                if(!empty($rowU[0])){
                                                    $uniVi  =   "SELECT uvs.unidad_vivienda 
                                                                FROM gp_unidad_vivienda_servicio uvs 
                                                                LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                                                                WHERE uvms.id_unico = '$rowU[0]'";
                                                    $resuniV = $mysqli->query($uniVi);
                                                    $rowUV = mysqli_fetch_row($resuniV); 

                                                    $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts
                                                                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.tipo_servicio = ts.id_unico
                                                                  
                                                                    WHERE uvs.unidad_vivienda = '$rowUV[0]' AND ts.id_unico != $DetP[1]";
                                                }else{
                                                    $row = "SELECT  ts.id_unico, ts.nombre
                                                                    FROM gp_tipo_servicio ts WHERE ts.id_unico != $DetP[1]";
                                                }
                                                
                                                $resS = $mysqli->query($row);
                                                while($i = mysqli_fetch_row($resS)) {
                                                    echo '<option value="'.$i[0].'">'.ucwords(mb_strtolower($i[1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="sltDesc" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                        <select required id="sltDesc" name="sltDesc" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Descripción">
                                            <option value="<?php echo $DetP[3] ?>"><?php echo $DetP[4] ?></option>
                                           <?php
                                           $row = $con->Listar("SELECT  id_unico, descripcion
                                            FROM gpqr_descripcion
                                            WHERE compania = '$compania' AND id_unico != '$DetP[3]'
                                            ORDER BY descripcion ASC ");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <?php
                                        $fecha_Det = date('d/m/Y', strtotime($DetP[9]));
                                    ?>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="sltfecha" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                                        <input type="text" name="sltfecha" id="sltfecha" class="form-control" required="required" placeholder="Fecha" title="Ingrese la Fecha" readonly value="<?php echo $fecha_Det ?>">
                                    </div>
                                    <?php
                                        $id_Asociado = "SELECT det.id_unico, des.descripcion FROM gpqr_detalle_pqr det left join gpqr_descripcion des on des.id_unico=det.id_descripcion where det.id_unico = '$DetP[11]'";
                                        $res_Id_A = $mysqli->query($id_Asociado);
                                        $rowIA = mysqli_fetch_row($res_Id_A);
                                        if(empty($rowIA[0])){
                                            $Asociado = "Detalle Asociado";
                                        }else{
                                            $Asociado = $rowIA[1];
                                        }
                                    ?>
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="sltDetA" class="col-sm-5 control-label">Detalle Asociado:</label>
                                        <select  id="sltDetA" name="sltDetA" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Ciudad">
                                            <option value="<?php echo $DetP[11] ?>"><?php echo $Asociado?></option>
                                           <?php
                                           $row = $con->Listar("SELECT det.id_unico, des.descripcion FROM gpqr_detalle_pqr det left join gpqr_descripcion des on des.id_unico=det.id_descripcion where det.id_pqr= '$DetP[0]' AND des.id_unico != $rowIA[0] ");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="sltClase" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase:</label>
                                        <select required id="sltClase" name="sltClase" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Clase">
                                            <option value="<?php echo $DetP[6] ?>"><?php echo $DetP[7] ?></option>
                                           <?php
                                                $row = $con->Listar("SELECT id_unico, nombre FROM gpqr_clase  WHERE compania= '$compania' AND id_unico != '$DetP[6]'");
                                           
                                                for ($i = 0; $i < count($row); $i++) {

                                                    echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                                }
                                           ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" style="margin-top: -10px; ">
                                        <label for="sltAfavor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>A Favor:</label>
                                        <select required id="sltAfavor" name="sltAfavor" class="select2_single form-control"  style="height: 35px;"  title="Seleccione una Opción" disabled>
                                            <option value="">A Favor</option>
                                           <?php
                                           $row = $con->Listar("SELECT  id_unico, nombre
                                            FROM gpqr_afavor 
                                            WHERE compania = '$compania' AND id_unico != '$rowAF[0]'
                                            ORDER BY nombre ASC ");
                                           for ($i = 0; $i < count($row); $i++) {
                                               echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                           }
                                           ?>
                                        </select>
                                    </div>

                                    <script>
                                        <?php   if($DetP[12] == 1){ ?>
                                                    $("#sltAfavor").prop('disabled',false);
                                        <?php   } ?>    
                                    </script>

                                    <div class="form-group" style="margin-top: 5px;">
                                        <label for="txtObser" class="col-sm-5 control-label">Observaciones:</label>
                                        <textarea type="text" name="txtObser" id="txtObser" class="form-control"  placeholder="Ingrese las Observaciones" ><?php echo $DetP[5] ?></textarea>
                                    </div>
                                    
                                    <div class="form-group" style="margin-top: 20px; ">
                                        <label for="no" class="col-sm-5 control-label"></label>
                                        <button type="button" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px" onclick="javascript:modDet();">Modificar</button>
                                    </div>
                                    <input type="hidden" name="MM_insert" >
                                    <input type="hidden" name="txtact" id="txtact" value="<?php echo $actividad; ?>" >
                                    <script>
                                        function modDet(){
                                            var AP = 1;
                                            jsShowWindowLoad('Modifcando Datos ...');
                                            var formData = new FormData($("#form")[0]);
                                            $.ajax({
                                                type: 'POST',
                                                url: "jsonPQR/gpqr_detalle_pqrJson.php?action=3",
                                                data:formData,
                                                contentType: false,
                                                processData: false,
                                                success: function(response)
                                                {
                                                    jsRemoveWindowLoad();
                                                    console.log(response);
                                                    if(response==1){
                                                        $("#mensaje").html('Información Modificada Correctamente');
                                                        $("#modalMensajes").modal("show");
                                                        $("#Aceptar").click(function(){
                                                            
                                                                document.location='GPQR_PQR.php?id=<?php echo $id_PQR ?>&mod=1';
                                                              
                                                        })
                                                    } else {
                                                        $("#mensaje").html('No Se Ha Podido Modificar la Información');
                                                        $("#modalMensajes").modal("show");
                                                        $("#Aceptar").click(function(){
                                                            $("#modalMensajes").modal("hide");
                                                        })
                                                    }
                                                }
                                            });
                                        }
                                    </script>
                        <?php   } ?>
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
                                    <a class="btn btn-primary btnInfo" href="GPQR_DESCRIPCION.php?detalle=1">DESCRIPCION</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GPQR_CLASE.php?detalle=1">CLASE</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="GPQR_AFAVOR.php?detalle=1">A FAVOR</a>
                                </td>
                            </tr>
                            
                           
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    <script>
        $("#sltClase").change(function(){
            var idC = $("#sltClase").val();
            $.ajax({
                type:"GET",
                url:"traerIndicadorCierre.php?id="+idC,
                success: function (data) {
                    result = JSON.parse(data);
                    if(result == 1){
                        $("#sltAfavor").prop('disabled', false);
                    }else{
                        $("#sltAfavor").prop('disabled', true);
                    }
                }
            });
        });
    </script>
    
    <script>
        function registrar(){
            var AP = 2;
            jsShowWindowLoad('Guardando Datos ...');
            var formData = new FormData($("#form")[0]);
            $.ajax({
                type: 'POST',
                url: "jsonPQR/gpqr_detalle_pqrJson.php?action=2",
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
                                document.location='GPQR_PQR.php?id=<?php echo $id_PQR ?>&mod=1';
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

    <script> 
        //$("#sltActi").click(function(){
        var fechaIA  = document.getElementById('sltfecha').value;
        var fechaIP  = document.getElementById('fechaPQR').value;
        console.log("fecha pro: "+fechaIP);
        var fia = document.getElementById("sltfecha");
        fia.disabled=false;

        $("#sltfecha").datepicker("destroy");
        $("#sltfecha").datepicker({changeMonth: true, minDate: fechaIP});
        //});
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
