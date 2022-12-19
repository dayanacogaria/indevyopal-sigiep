<?php
require './Conexion/conexion.php';
require_once './head.php';
$compania     = $_SESSION['compania'];
$dependencias = $this->espacio->obtenerDependencias($compania);
?>
    <title>Registrar Espacio Habitable</title>
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
                <h2 id="forma-titulo3" align="center" style="margin: 0px 4px 20px;">Registrar Espacios Habitables</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="access.php?controller=EspacioHabitable&action=Guardar">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="nombre" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Espacio:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltTipo" id="sltTipo" class="form-control select2" required="">
                                    <option value="">Tipo Espacio</option>
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
                                <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" maxlength="100" title="Ingrese codigo" onkeypress="return txtValida(event,'num_car')" placeholder="Codigo" required style="width: 100%">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="txtDescripcion" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtDescripcion" id="txtDescripcion" class="form-control" maxlength="1000" title="Ingrese Descripcion" onkeypress="return txtValida(event,'num_car')" placeholder="Descripción" required style="width: 100%">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltDependencia" class="col-sm-5 col-md-5 col-lg-5 control-label"><strong style="color:#03C1FB;"></strong>Dependencia:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select name="sltDependencia" id="sltDependencia" class="form-control select2" >
                                    <option value="">Dependencia</option>
                                    <?php
                                    $html = "";
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
                                    <option value="">Predecesor</option>
                                    <?php
                                    $html = "";
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
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
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
                                    <input  type="hidden" id="formato" name="formato">
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
                        </div> 
                        <div id="targetLayer" style="display:none;"></div>
                        <div class="form-group">
                            <label for="no" class="col-sm-5 control-label"></label>
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
    <script src="dist/jquery.validate.js"></script>
    <script src="js/script_validation.js"></script>
    <script src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(document).ready(function (){
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