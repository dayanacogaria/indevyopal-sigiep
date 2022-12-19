<?php
require_once 'head.php';
require_once('Conexion/conexion.php');
$id_concepto = " ";
$id_concepto = (($_GET["id_concepto"]));
$queryConcepto = "SELECT c.id_unico,c.nombre,
    cc.id_unico,cc.nombre, 
    c.amortizable, ts.id_unico, ts.nombre  
    FROM gf_concepto c 
    LEFT JOIN gf_clase_concepto cc ON c.clase_concepto = cc.id_unico  
    LEFT JOIN gp_tipo_servicio ts ON c.tipo_servicio = ts.id_unico 
    WHERE md5(c.id_unico) ='$id_concepto'";
$resultado = $mysqli->query($queryConcepto);
$row = mysqli_fetch_row($resultado); ?>

<link href="css/select/select2.min.css" rel="stylesheet">
 <link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>        
    label #nombre-error,#sltTipoConcepto-error{
        display: block;color: #bd081c;font-weight: bold;font-style: italic;
    }
</style>
<script>
$().ready(function() {
    var validator = $("#form").validate({ignore: "",errorPlacement: function(error, element) {
            $( element ).closest( "form" ).find( "label[for='" + element.attr( "id" ) + "']" ).append( error );
        },});
    $(".cancel").click(function() {validator.resetForm();});
});
</script>
<title>Modificar Concepto</title>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:-2px">Modificar Concepto</h2>
                <a href="listar_GF_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[1] ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-top:-15px" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarConceptoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltTipoConcepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                            <select name="sltTipoConcepto" id="sltTipoConcepto" class="select2_single form-control" title="Seleccione tipo de concepto">
                                <?php 
                                if(empty($row[2])){
                                    echo '<option value=""> - </option>';
                                    $sql1="select id_unico,nombre from gf_clase_concepto";
                                }else{
                                    echo '<option value="'.$row[2].'">'.ucfirst(strtolower($row[3])).'</option>';
                                    $sql1 = "select id_unico,nombre from gf_clase_concepto where id_unico !=$row[2]";
                                }                            
                                $result1=$mysqli->query($sql1);
                                while ($fila=mysqli_fetch_row($result1)) {
                                    echo '<option value="'.$fila[0].'">'.ucfirst(strtolower($fila[1])).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sltTipoServicio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Servicio:</label>
                            <select name="sltTipoServicio" id="sltTipoServicio" class="select2_single form-control" title="Seleccione Servicio" >
                                <?php 
                                if(empty($row[5])){
                                    echo '<option value=""> - </option>';
                                    $sql1="select id_unico,nombre from gp_tipo_servicio";
                                }else{
                                    echo '<option value="'.$row[5].'">'.ucfirst(strtolower($row[6])).'</option>';
                                    echo '<option value=""> - </option>';
                                    $sql1 = "select id_unico,nombre from gp_tipo_servicio where id_unico !=$row[5]";
                                } 
                                $result=$mysqli->query($sql1);
                                while ($row= mysqli_fetch_row($result)){
                                    echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1])).'</option>';
                                }
                                ?>                                
                            </select>
                        </div> 
                        <div class="form-group" style="margin-top:-10px">
                            <label for="factorBase" class="col-sm-5 control-label">Amortizable:</label>
                            <?php if ($row[4] == 1){ ?> 
                                <label for="si" class="radio-inline"><input type="radio" name="rdamrt"  value="1" checked>Si</label>
                                <label for="no" class="radio-inline"><input type="radio" name="rdamrt" id="rdamrt"  value="2">No</label>
                            <?php }else if ($row[4] == 2 || empty($row[4])){ ?> 
                                <label for="si" class="radio-inline"><input type="radio" name="rdamrt"  value="1">Si</label>
                                <label for="no" class="radio-inline"><input type="radio" name="rdamrt" id="rdamrt"  value="2" checked>No</label>
                            <?php } ?>
                        </div>
                        
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: 4px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
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
</body>
</html>
