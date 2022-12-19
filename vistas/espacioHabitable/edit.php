<?php
require './Conexion/conexion.php';
require_once './head.php';
$compania     = $_SESSION['compania'];
list($id, $tipo_id, $tipo_nom, $tipos, $codigo, $descripcion, $conpt_id, $conpt_nom, $conceptos, $dept_id, $dept_nom, $dependencias, $asociados, $aso, $ruta, $estado)
= array(0, 0, "", "", "", "", 0, "", "", 0, "", "", "", 0, "", 0);
if(!empty($_REQUEST['id'])){
    list($id, $tipo_id, $tipo_nom, $codigo, $descripcion, $dept_id, $dept_nom, $id_aso, $nom_aso, $ruta, $estado)
    = array($_REQUEST['id'], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9],$data[10]);
    $aso          = $this->espacio->obtener(md5($data[8]));
    $tipos        = $this->espacio->obtenerTiposDiferentes($tipo_id);
    if(empty($dept_id)){$dept_id=0;}
    $dependencias = $this->espacio->obtenerDependenciasDiferentes($dept_id, $compania);
    if(!empty($aso)){
        $id_aso = $aso[0]; $nom_aso = "$aso[3] $aso[4]";
        $asociados    = $this->espacio->obtenerDiferentesCodigo(md5($id_aso));
    }else{
        $asociados    = $this->espacio->obtenerDiferentesCodigo(md5($id));
    }
}
?>
    <title>Modificar Espacio Habitable</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/desing.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin: 0px 4px 20px;">Modificar Espacios Habitables</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=EspacioHabitable&action=Editar">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <input type="hidden" name="txtId" value="<?php echo $id ?>">
                        <div class="form-group">
                            <label for="nombre" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Espacio:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipo" id="sltTipo" class="form-control select2" required="">
                                    <option value="<?php echo $tipo_id ?>"><?php echo $tipo_nom ?></option>
                                    <?php
                                    $html = "";
                                    while($row = mysqli_fetch_row($tipos)) {
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtCodigo" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Codigo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="100" title="Ingrese codigo" onkeypress="return txtValida(event,'num_car')" placeholder="Codigo" required style="width: 100%" value="<?= $codigo ?>">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtDescripcion" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="1000" title="Ingrese Descripcion" onkeypress="return txtValida(event,'num_car')" placeholder="Descripción" required style="width: 100%" value="<?= $descripcion ?>">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltDependencia" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;"></strong>Dependencia:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltDependencia" id="sltDependencia" class="form-control select2" >
                                    <?php
                                    $html = "";

                                    if(!empty($dept_id)){
                                        $html .= "<option value=\"$dept_id\">$dept_nom</option>";
                                    }else{
                                        $html .= "<option value=\"\">Dependencia</option>";
                                    }

                                    while($row = mysqli_fetch_row($dependencias)) {
                                        $html .= "<option value='$row[0]'>$row[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltAsociado" class="col-sm-5 col-md-5 col-lg-5 control-label">Predecesor:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltAsociado" id="sltAsociado" class="form-control select2">
                                    <?php
                                    $html = "";

                                    if(!empty($id_aso)){
                                        $html .= "<option value=\"$id_aso\">$nom_aso</option>";
                                    }else{
                                        $html .= "<option value=\"\">Predecesor</option>";
                                    }

                                    while($row = mysqli_fetch_row($asociados)) {
                                        $html .= "<option value='$row[0]'>$row[1] $row[2]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sltEstado" class="col-sm-5 col-md-5 col-lg-5 control-label">Estado:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltEstado" id="sltEstado" class="form-control select2" required>
                                    <?php if($estado==1){
                                        echo '<option value="1">Activo</option>';
                                        echo '<option value="2">Inactivo</option>';
                                    } else {
                                        echo '<option value="2">Inactivo</option>';
                                        echo '<option value="1">Activo</option>';
                                    } ?>                                  
                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtRuta" class="col-sm-5 col-md-5 col-lg-5 control-label">Formato:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <div class="btn-group">
                                    <button type="button" id="btnvd" class="btn btn-primary">Video</button>
                                    <button type="button" id="btnurl" class="btn btn-primary" style="border-radius: 0px 4px 4px 0px;">Url</button>
                                    <label style="padding-top: 11px; padding-left: 18px; display:none;" id="lblsize">Max (60MB)</label>
                                    <input type="hidden" id="formato" name="formato">                                        
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="divfile" style="display: none;">
                            <label for="txtRuta" class="col-sm-5 col-md-5 col-lg-5 control-label">Ruta:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5 ">
                                <input type="hidden" name="txtRutaX" id="txtRutaX" value="<?php echo $ruta ?>">
                                <input type="file" name="txtRuta" id="txtRuta" class="form-control" style="width: 100%;" >
                                <input type="text" name="txtUrl" id="txtUrl" class="form-control" title="Ingrese url"  placeholder="URL" style="width: 100%" >
                            </div>                            
                            <div class="col-sm-1 col-md-1 col-lg-1" id="verarchivo">
                                <a href="javascript:void(0)" class="btn btn-primary borde-sombra" target="_blank" title="Ver archivo" id="openvideo"><span class="glyphicon glyphicon-download-alt"></span></a>
                            </div>                            
                        </div>                        
                        <div id="targetLayer" style="display:none;"></div>
                        <div class="form-group">
                            <label for="no" class="col-sm-5 col-md-5 col-lg-5 control-label"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-primary borde-sombra"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </div>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>    
    <?php require_once './vistas/espacioHabitable/video.modal.php'; ?>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>        
        $(document).ready(function (){            
        let file = "<?php echo $ruta ?>";
            if (file === ''){
                $("#verarchivo").hide();
            }                
            $('#form').submit(function(event){                    
                let form = $('#form');
                let formato = $("#formato").val();                
                if (formato == 1 && $('#txtRuta').val()){
                    if (form.valid()){
                        jsShowWindowLoad('Subiendo Video...');
                        event.preventDefault();
                        $('#loader-icon').show();
                        $('#targetLayer').hide();
                        $(this).ajaxSubmit({
                            target: '#targetLayer',                        
                            success:function(){
                                jsRemoveWindowLoad();
                                $('#targetLayer').show();
                            },
                            resetForm: true                        
                        });
                    return false;
                    }else{
                        alert();
                        return false;
                    }                        
                }
            });  
        });
            
        $("#btnurl").click(function () {
           $("#formato").val(1);
           $("#divfile").show();
           $("#txtUrl").hide(); 
           $("#txtRuta").show();
           $("#lblsize").show();
           $("#lblsize").css('color','black');
        });
        
        $("#btnvd").click(function () {
           $("#formato").val(2);           
           $("#divfile").show();
           $("#txtUrl").show(); 
           $("#txtRuta").hide();
           $("#lblsize").hide();
        });
        
        var uploadField = document.getElementById("txtRuta");
        uploadField.onchange = function() {
            if(this.files[0].size > 61000000){
                $("#lblsize").hide();
                $("#lblsize").show(100);
                $("#lblsize").css('color','red');
               this.value = "";
            }else{
                $("#lblsize").css('color','black');
            };
        };
        
        $(".select2").select2();
    </script>
</body>
</html>