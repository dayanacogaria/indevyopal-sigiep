<?php
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
require_once('./jsonPptal/funcionesPptal.php');
$compania   =$_SESSION['compania'];
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
if(!empty($_GET['s'])){
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
            uv.codigo_ruta, p.id_unico, p.codigo_catastral 
        FROM gp_predio1 p
        LEFT JOIN gp_unidad_vivienda uv ON uv.predio = p.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        LEFT JOIN gp_uso u ON uv.uso= u.id_unico 
        LEFT JOIN gp_estrato e ON uv.estrato = e.id_unico 
        LEFT JOIN gp_tipo_manzana tm ON uv.manzana = tm.id_unico 
        LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
        WHERE uv.sector = ".$_GET['s']." ORDER BY cast(p.codigo_catastral as unsigned ) ASC");
}
?>
<head>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <title>Usuarios Acueducto</title>
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
    label#tercero-error, #uso-error,#estrato-error, #sector-error,#ciudad-error,#codigoC-error{
        display: block;
        color: #bd081c;
        font-weight: bold;
        font-style: italic;
    }
    
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left" style="margin-top: -15px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Usuarios Acueducto</h2>
                <?php if(empty($_GET['s'])) { ?>                
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                        <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -5px;margin-left: 300px">
                            <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  50px;">
                                <label for="sector" class="col-sm-12 control-label"><strong class="obligado">*</strong>Sector:</label>
                            </div>
                            <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  50px;">
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
                        </div>
                    </form>
                    <script>
                        $("#sector").change(function(){
                            if($("#sector").val()!=""){
                                document.location ='GP_USUARIOS_ACUEDUCTO.php?s='+$("#sector").val();                            
                            }
                        })
                    </script>
                </div>
                <?php }  else  { ?>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                        <?php  if(empty($_GET['uv'])) { ?>
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                            <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                             <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                                <input type="hidden" name="sectorG" id="sectorG" value="<?php echo $_GET['s']?>">
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="codigoC" class="col-sm-12 control-label"><strong class="obligado">*</strong>Código Catastral:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
                                    <input type="text"  name="codigoC" id="codigoC"  class="form-control col-md-1 col-sm-1 col-lg-2" style="width: 100%" maxlength="100" title="Ingrese Código Catastral"  placeholder="Código Catastral" >
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="tercero" class="col-sm-12 control-label"><strong class="obligado">*</strong>Tercero:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
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
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
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
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  20px;">
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
                                <a href="GP_USUARIOS_ACUEDUCTO.php" style="margin-left:20px;margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></a>
                            </div>
                            <div class="form-group form-inline " style="margin-top: -5px;margin-left: 0px">
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="ciudad" class="col-sm-12 control-label"><strong class="obligado">*</strong>Ciudad:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2" style="margin-left:  0px;">
                                    <select name="ciudad" id="ciudad" class="form-control select2" title="Seleccione Ciudad" style="height: auto " required>
                                        <?php 
                                            echo '<option value="">Ciudad</option>';
                                            $tr = $con->Listar("SELECT c.id_unico, c.nombre, d.nombre 
                                                    FROM gf_ciudad c 
                                                    LEFT JOIN gf_departamento d ON c.departamento=d.id_unico 
                                                    ORDER BY c.nombre ASC");
                                            for ($i = 0; $i < count($tr); $i++) {
                                               echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1].' - '.$tr[$i][2])).'</option>'; 
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="manzana" class="col-sm-12 control-label"><strong class="obligado"></strong>Manzana:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  0px;">
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
                                    <label for="direccion" class="col-sm-12 control-label"><strong class="obligado">*</strong>Dirección</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                    <input type="text"  name="direccion" id="direccion"  class="form-control col-md-1 col-sm-1 col-lg-2" style="width: 100%" maxlength="100" title="Ingrese Dirección"  placeholder="Dirección" >
                                </div>
                                 <div class="form-group form-inline  col-md-1 col-lg-1" style="margin-left:  0px;">
                                    <label for="codigoR" class="col-sm-12 control-label"><strong class="obligado"></strong>Codigo Ruta:</label>
                                </div>
                                <div class="form-group form-inline  col-md-2 col-lg-2"  style="margin-left:  20px;">
                                    <input type="text"  name="codigoR" id="codigoR"  class="form-control col-md-1 col-sm-1 col-lg-2" maxlength="100" title="Ingrese Código Ruta"  placeholder="Código Ruta" style="width:100%">
                                </div>
                                <button type="submit" style="margin-left:20px; margin-top: 0px" type="button"  class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>
                            </div>
                        </form>
                        <script>
                            function guardar(){
                                var formData = new FormData($("#form")[0]);
                                jsShowWindowLoad('Guardando..');
                                $.ajax({
                                    type: 'POST',
                                    url: "jsonServicios/gp_UnidadViviendaJson.php?action=4",
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
                                <div class="form-group form-inline  col-md-3 col-lg-3" style="margin-left:  0px;">
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
                                        <td><strong>Código Catastral</strong></td>
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
                                        <th>Código Catastral</th>
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
                                            <a onclick="javaScript:modificar(<?php echo $row[$i][0]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?php echo $row[$i][16]; ?></td>  
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
                
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#sector").select2();
        $("#tercero").select2();
        $("#uso").select2();
        $("#estrato").select2();
        $("#manzana").select2();
        $("#ciudad").select2();
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




