<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
?>

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
<title>Registrar Concepto</title>
</head>
<body> 
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px;margin-top:-2px">Registrar Concepto</h2>
                <a href="listar_GF_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Concepto</h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarConceptoJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" required>
                        </div>                     
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sltTipoConcepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                            <select name="sltTipoConcepto" id="sltTipoConcepto" class="select2_single form-control" title="Seleccione tipo de concepto" required="required" >
                                <?php 
                                echo '<option value="">Tipo Concepto</option>';
                                $sql="select id_unico,nombre from gf_clase_concepto";
                                $result=$mysqli->query($sql);
                                while ($row= mysqli_fetch_row($result)){
                                    echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1])).'</option>';
                                }
                                ?>                                
                            </select>
                        </div> 
                        <div class="form-group">
                            <label for="sltTipoServicio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Servicio:</label>
                            <select name="sltTipoServicio" id="sltTipoServicio" class="select2_single form-control" title="Seleccione Servicio" >
                                <?php 
                                echo '<option value="">Tipo Servicio</option>';
                                $sql="select id_unico,nombre from gp_tipo_servicio";
                                $result=$mysqli->query($sql);
                                while ($row= mysqli_fetch_row($result)){
                                    echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1])).'</option>';
                                }
                                ?>                                
                            </select>
                        </div> 
                        <div class="form-group" style="margin-top:-10px">
                        <label for="rdamrt" class="col-sm-5 control-label">Amortizable:</label>
                            <label for="si" class="radio-inline"><input type="radio" name="rdamrt"  value="1" >Si</label>
                            <label for="no" class="radio-inline"><input type="radio" name="rdamrt"  value="2" checked>No</label>
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
    <?php require_once 'footer.php' ?>  
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

