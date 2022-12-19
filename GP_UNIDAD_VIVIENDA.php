<?php
require_once ('Conexion/conexion.php');
require_once ('Conexion/ConexionPDO.php');
require_once 'head_listar.php'; 
$con        = new ConexionPDO();
$predio     = $_GET['predio'];
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
#*** Datos Predio **#
$dp = $con->Listar("SELECT id_unico, codigo_catastral, nombre FROM gp_predio1 
        WHERE md5(id_unico)='".$predio."'");
$id_predio = $dp[0][0];
#** Listar Unidad Vivienda Del Predio ***#
$row = $con->Listar("SELECT uv.id_unico, 
            t.id_unico, IF(CONCAT_WS(' ',
            t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos) = '',
            (t.razonsocial),
            CONCAT_WS(' ',
            t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos)) AS NOMBRE, 
        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
            t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
            u.id_unico, u.nombre, e.id_unico, e.codigo, e.nombre,
            tm.id_unico, tm.nombre, 
            s.id_unico, s.codigo, s.nombre, 
            uv.codigo_ruta 
        FROM gp_unidad_vivienda uv 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_uso u ON uv.uso= u.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uv.predio = $id_predio")
?>
<title>Unidad Vivienda</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/md5.pack.js"></script>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
label#tercero-error, #uso-error,#estrato-error, #sector-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
.client-form input[type="text"] {
    width: 250px;
}
body{
    font-size: 12px;
}
</style>
</head>
<body>   
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Unidad Vivienda</h2>
                <a href="Modificar_GP_PREDIO.php?id=<?php echo $_GET['predio'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px">PREDIO:<?php echo mb_strtoupper($dp[0][1]).' - '. ucwords(mb_strtolower($dp[0][2]))?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <?php  if(empty($_GET['uv'])) { ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                         <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                             <input type="hidden" id="predio" name="predio" value="<?php echo $id_predio;?>">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="tercero" class="col-sm-12 control-label"><strong class="obligado">*</strong>Tercero:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Tercero" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Tercero</option>';
                                        $tr = $con->Listar("SELECT t.id_unico, IF(CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) = '',
                                                (t.razonsocial),
                                                CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos)) AS NOMBRE, 
                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                t.numeroidentificacion, 
                                                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                            FROM gf_tercero t 
                                            WHERE t.compania = $compania");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i][2].'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                             <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="uso" class="col-sm-12 control-label"><strong class="obligado">*</strong>Uso:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="uso" id="uso" class="form-control select2" title="Seleccione Uso" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Uso</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre                                 
                                            FROM gp_uso");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="estrato" class="col-sm-12 control-label"><strong class="obligado">*</strong>Estrato:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  20px;">
                                <select name="estrato" id="estrato" class="form-control select2" title="Seleccione Estrato" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Estrato</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_estrato 
                                            WHERE tipo_estrato = 2 
                                            ORDER BY cast(codigo as unsigned) ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="sector" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  20px;">
                                <select name="sector" id="sector" class="form-control select2" title="Seleccione Sector" style="height: auto " required>
                                    <?php 
                                        echo '<option value="">Sector</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_sector 
                                            ORDER BY codigo ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="manzana" class="col-sm-12 control-label"><strong class="obligado"></strong>Manzana:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="manzana" id="manzana" class="form-control select2" title="Seleccione Manzana" style="height: auto ">
                                    <?php 
                                        echo '<option value="">Manzana</option>';
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre 
                                            FROM gp_manzana ");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                             <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="codigoR" class="col-sm-12 control-label"><strong class="obligado"></strong>Codigo Ruta:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <input type="text"  name="codigoR" id="codigoR"  class="form-control col-md-1 col-sm-1" maxlength="100" title="Ingrese Código Ruta"  placeholder="Código Ruta" style="width:200px">
                            </div>
                            
                        </div>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-right: 20px; text-align: right;margin-bottom:-8px">
                            <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                        </div>
                    </form>
                    <script>
                        function guardar(){
                            var formData = new FormData($("#form")[0]);
                            jsShowWindowLoad('Guardando..');
                            $.ajax({
                                type: 'POST',
                                url: "jsonServicios/gp_UnidadViviendaJson.php?action=1",
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    jsRemoveWindowLoad();
                                    console.log(data);
                                    if (data ==1) {
                                        $("#mensaje").html("Información Guardada Correctamente");
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location.reload();
                                        })
                                    } else {
                                        $("#mensaje").html("No Se Ha Podido Guardar La Información");
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#mdlMensajes").modal("hide");
                                        })
                                    }
                                }
                            })
                        }
                    </script>
                    <?php } else { 
                        $rowuv = $con->Listar("SELECT uv.id_unico, 
                                t.id_unico, IF(CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) = '',
                                (t.razonsocial),
                                CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos)) AS NOMBRE, 
                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                t.numeroidentificacion, 
                                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)), 
                                u.id_unico, u.nombre, e.id_unico, e.codigo, e.nombre,
                                tm.id_unico, tm.nombre, 
                                s.id_unico, s.codigo, s.nombre, 
                                uv.codigo_ruta 
                            FROM gp_unidad_vivienda uv 
                            LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
                            LEFT JOIN gp_uso u ON uv.uso= u.id_unico 
                            LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
                            LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico 
                            LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                            WHERE md5(uv.id_unico)='".$_GET['uv']."'");
                        ?>
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:modificarDatos()">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                         <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                             <input type="hidden" id="predio" name="predio" value="<?php echo $id_predio;?>">
                             <input type="hidden" id="id" name="id" value="<?php echo $rowuv[0][0];?>">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="tercero" class="col-sm-12 control-label"><strong class="obligado">*</strong>Tercero:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="tercero" id="tercero" class="form-control select2" title="Seleccione Tercero" style="height: auto " required>
                                    <?php 
                                        if(empty($rowuv[0][1])) { 
                                            echo '<option value=""> - </option>';
                                            $idt = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][1].'">'.ucwords(mb_strtolower($rowuv[0][2])).' - '.$rowuv[0][3].'</option>'; 
                                            $idt = $rowuv[0][1];
                                        }
                                        $tr = $con->Listar("SELECT t.id_unico, IF(CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) = '',
                                                (t.razonsocial),
                                                CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos)) AS NOMBRE, 
                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                t.numeroidentificacion, 
                                                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
                                            FROM gf_tercero t 
                                            WHERE t.compania = $compania AND t.id_unico != $idt");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i][2].'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                             <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="uso" class="col-sm-12 control-label"><strong class="obligado">*</strong>Uso:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="uso" id="uso" class="form-control select2" title="Seleccione Uso" style="height: auto " required>
                                    <?php 
                                        if(empty($rowuv[0][4])) { 
                                            echo '<option value=""> - </option>';
                                            $idu = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][4].'">'.ucwords(mb_strtolower($rowuv[0][5])).'</option>'; 
                                            $idu = $rowuv[0][4];
                                        }
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre                                 
                                            FROM gp_uso WHERE id_unico != $idu");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="estrato" class="col-sm-12 control-label"><strong class="obligado">*</strong>Estrato:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  20px;">
                                <select name="estrato" id="estrato" class="form-control select2" title="Seleccione Estrato" style="height: auto " required>
                                    <?php 
                                        if(empty($rowuv[0][6])) { 
                                            echo '<option value=""> - </option>';
                                            $ide = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][6].'">'.$rowuv[0][7].' - '.ucwords(mb_strtolower($rowuv[0][8])).'</option>'; 
                                            $ide = $rowuv[0][6];
                                        }
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_estrato 
                                            WHERE tipo_estrato = 2 AND id_unico != $ide  
                                            ORDER BY cast(codigo as unsigned) ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="sector" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  20px;">
                                <select name="sector" id="sector" class="form-control select2" title="Seleccione Sector" style="height: auto " required>
                                    <?php 
                                        if(empty($rowuv[0][11])) { 
                                            echo '<option value=""> - </option>';
                                            $ids = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][11].'">'.$rowuv[0][12].' - '.ucwords(mb_strtolower($rowuv[0][13])).'</option>'; 
                                            $ids = $rowuv[0][11];
                                        }
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre, codigo
                                            FROM gp_sector 
                                            WHERE id_unico != $ids 
                                            ORDER BY codigo ASC");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="manzana" class="col-sm-12 control-label"><strong class="obligado"></strong>Manzana:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <select name="manzana" id="manzana" class="form-control select2" title="Seleccione Manzana" style="height: auto ">
                                    <?php 
                                        if(empty($rowuv[0][9])) { 
                                            echo '<option value=""> - </option>';
                                            $idm = 0;
                                        } else {
                                            echo '<option value="'.$rowuv[0][9].'">'.ucwords(mb_strtolower($rowuv[0][10])).'</option>'; 
                                            echo '<option value=""> - </option>';
                                            $idm = $rowuv[0][9];
                                        }
                                        $tr = $con->Listar("SELECT DISTINCT id_unico, 
                                            nombre 
                                            FROM gp_manzana WHERE id_unico != $idm");
                                        for ($i = 0; $i < count($tr); $i++) {
                                           echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                        }
                                    ?>
                                </select>
                            </div>
                             <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                <label for="codigoR" class="col-sm-12 control-label"><strong class="obligado"></strong>Codigo Ruta:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3"  style="margin-left:  20px;">
                                <input type="text" value="<?php echo $rowuv[0][14]?>" name="codigoR" id="codigoR"  class="form-control col-md-1 col-sm-1" maxlength="100" title="Ingrese Código Ruta"  placeholder="Código Ruta" style="width:200px">
                            </div>
                            
                        </div>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-right: 20px; text-align: right;margin-bottom:-8px">
                            <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Modificar"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>
                        </div>
                    </form>
                    <script>
                        function modificarDatos(){
                            var formData = new FormData($("#form")[0]);
                            jsShowWindowLoad('Modificando..');
                            $.ajax({
                                type: 'POST',
                                url: "jsonServicios/gp_UnidadViviendaJson.php?action=3",
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    jsRemoveWindowLoad();
                                    console.log(data);
                                    if (data ==1) {
                                        $("#mensaje").html("Información Modificada Correctamente");
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location="GP_UNIDAD_VIVIENDA.php?predio="+md5($("#predio").val());
                                        })
                                    } else {
                                        $("#mensaje").html("No Se Ha Podido Modificar La Información");
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#mdlMensajes").modal("hide");
                                        })
                                    }
                                }
                            })
                        }
                    </script>
                    <?php } ?>
                </div>
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Uso</strong></td>
                                    <td><strong>Estrato</strong></td>
                                    <td><strong>Sector</strong></td>
                                    <td><strong>Manzana</strong></td>
                                    <td><strong>Código Ruta</strong></td>
                                    <td><strong>Servicio</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tercero</th>
                                    <th>Uso</th>
                                    <th>Estrato</th>
                                    <th>Sector</th>
                                    <th>Manzana</th>
                                    <th>Código Ruta</th>
                                    <th>Servicio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($row); $i++) { ?>
                                
                                <tr>
                                    <td style="display: none;"><?php echo $row[$i][0]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[$i][0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="javaScript:modificar(<?php echo $id_predio.','.$row[$i][0]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][2])).' - '.$row[$i][3]; ?></td>
                                    <td><?php echo ucwords(mb_strtolower($row[$i][5])); ?></td>  
                                    <td><?php echo $row[$i][7].' - '.ucwords(mb_strtolower($row[$i][8])); ?></td>  
                                    <td><?php echo $row[$i][12].' - '.ucwords(mb_strtolower($row[$i][13])); ?></td>  
                                    <td><?php echo ucwords(mb_strtolower($row[$i][10])); ?></td>  
                                    <td><?php echo $row[$i][14]; ?></td>  
                                    <td><a href="GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo md5($row[$i][0])?>" target="blanck"><i class="glyphicon glyphicon-eye-open"></i></a></td>  
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <script>
                function eliminar(id){
                    $("#modalEliminar").modal("show");
                    $("#AceptarEliminar").click(function(){
                        $("#modalEliminar").modal("hide");
                        var formData = {action:2, id:id};
                        jsShowWindowLoad('Eliminando..');
                        $.ajax({
                            type: 'POST',
                            url: "jsonServicios/gp_UnidadViviendaJson.php",
                            data: formData,
                            success: function (data) {
                                jsRemoveWindowLoad();
                                console.log(data);
                                if (data ==1) {
                                    $("#mensaje").html("Información Eliminada Correctamente");
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html("No Se Ha Podido Eliminar La Información");
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#mdlMensajes").modal("hide");
                                    })
                                }
                            }
                        })
                    });
                    $("#CancelarEliminar").click(function(){
                        $("#modalEliminar").modal("hide");
                    })
                    
                }
            </script>
            <script>
                function modificar(p, id){
                    document.location ='GP_UNIDAD_VIVIENDA.php?predio='+md5(p)+'&uv='+md5(id);
                }
            </script>
            <div class="col-sm-2 text-center" align="center" style="margin-top:-15px">
                <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
                <div  align="center">
                    <a href="registrar_GP_TIPO_SERVICIO.php" class="btn btn-primary btnInfo">TIPO SERVICIO</a>          
                    <a href="registrar_GP_ESTADO_SERVICIO.php" class="btn btn-primary btnInfo">ESTADO SERVICIO</a>          
                </div>
            </div>
	</div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#tercero").select2();
        $("#uso").select2();
        $("#sector").select2();
        $("#estrato").select2();
        $("#manzana").select2();
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
    <div class="modal fade" id="modalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label>¿Desea Eliminar El Registro Seleccionado?</label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="AceptarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="CancelarEliminar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div> 
</body>
</html>


