<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
$id                 = $_GET['id_cond'];
$compania           = $_SESSION['compania'];
$_SESSION['url']    = 'Modificar_GF_TIPO_DOCUMENTO.php?id_cond='.$id;
$queryCond = "SELECT
                TD.Id_Unico,
                TD.Nombre,
                F.Id_Unico,
                F.Nombre,
                TD.es_obligatorio,
                TD.consecutivo_unico,
                TD.dependencia,
                ci.id_unico,
                TD.clase_informe,
                s.id_unico, 
                s.nombre, 
                ci.nombre , 
                TD.vigencia  
              FROM
                gf_tipo_documento TD
              LEFT JOIN
                gf_formato F ON TD.Formato = F.Id_Unico
              LEFT JOIN 
                gf_clase_informe ci ON TD.clase_informe = ci.id_unico 
              LEFT JOIN 
                gf_dependencia s ON TD.dependencia = s.id_unico
            WHERE md5(TD.Id_Unico) = '$id'"; 
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);

#Formato
$tipo_da = "SELECT Id_Unico, Nombre FROM gf_formato WHERE Id_Unico != '$row[2]' 
    AND compania = $compania ORDER BY Nombre ASC";
$tipo_d =   $mysqli->query($tipo_da);
?>
  <!--Titulo de la página-->
<title>Modificar Tipo Documento</title>
</head>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#nombre-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;

}
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
<body>
    <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center" >
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Llamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Modificar Tipo Documento</h2>
                <a href="listar_GF_TIPO_DOCUMENTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(mb_strtolower( $row[1])); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipo_Documento.php">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower( $row[1])); ?>" required>
                        </div>
                        <div class="form-group" style="margin-top:-10px">
                            <label for="obligatorio" class="col-sm-5 control-label" style="margin-top:-5px"><strong style="color:#03C1FB;">*</strong>¿Es obligatorio?:</label>
                            <?php if($row[4]=='1'){?>
                            <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si es obligatorio" value="1" checked="checked">Sí
                            <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si no es obligatorio" value="2" >No
                            <?php } else { ?>
                            <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si es obligatorio" value="1">Sí
                            <input type="radio" name="obligatorio" id="obligatorio" title="Seleccione si no es obligatorio" value="2" checked="checked">No
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="consecutivo" class="col-sm-5 control-label" style="margin-top:-5px"><strong style="color:#03C1FB;">*</strong>¿Es consecutivo único?:</label>
                            <?php if($row[5]=='1'){?>
                            <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si es consecutivo único" value="1" checked="checked">Sí
                            <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si no es consecutivo único" value="2" >No
                            <?php } else { ?>
                            <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si es consecutivo único" value="1">Sí
                            <input type="radio" name="consecutivo" id="consecutivo" title="Seleccione si no es consecutivo único" value="2" checked="checked">No
                            <?php } ?>
                        </div>
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="formato" class="col-sm-5 control-label">Formato:</label>
                            <select name="formato" id="formato" class="select2_single form-control" title="Seleccione el formato">
                               <?php 
                              if (empty($row[2])) {?>
                                <option value="">Formato</option>
                                <?php while($rowF = mysqli_fetch_row($tipo_d)){ ?>
                                <option value="<?php echo $rowF[0]?>"><?php echo ucwords(mb_strtolower($rowF[1]))?></option>
                              <?php } ?>
                              <?php } else { ?>
                                <option value="<?php echo $row[2]?>"><?php echo ucwords(mb_strtolower($row[3]))?></option>
                                <option value="">-</option>
                              <?php while($rowF = mysqli_fetch_row($tipo_d)){ ?>
                                <option value="<?php echo $rowF[0]?>"><?php echo ucwords(mb_strtolower($rowF[1]))?></option>
                              <?php } } ?>

                            </select> 
                        </div> 
                        <div class="form-group">
                            <label for="clase" class="col-sm-5 control-label">Clase Informe:</label>
                            <select name="clase" id="clase" class="select2_single form-control col-sm-1" title="Seleccione Clase Informe" >
                                <?php 
                                 
                                if(empty($row[7])){
                                    #CLASE
                                    $cl = "SELECT id_unico, nombre FROM gf_clase_informe ORDER BY nombre ASC";
                                    $cl = $mysqli->query($cl);
                                    echo '<option value=""> - </option>';
                                    while($rowC = mysqli_fetch_assoc($cl)){
                                        echo '<option value="'.$rowC['id_unico'].'">'.ucwords((mb_strtolower($rowC['nombre']))).'</option>';
                                    }
                                } else { 
                                    #CLASE
                                    $cl = "SELECT id_unico, nombre FROM gf_clase_informe WHERE id_unico !='$row[7]' ORDER BY nombre ASC";
                                    $cl = $mysqli->query($cl);
                                    echo '<option value="'.$row[7].'">'.ucwords((mb_strtolower($row[11]))).'</option>';
                                    echo '<option value=""> - </option>';
                                    while($rowC = mysqli_fetch_assoc($cl)){ 
                                        echo '<option value="'.$rowC['id_unico'].'">'.ucwords((mb_strtolower($rowC['nombre']))).'</option>';
                                    }
                                }
                                ?>
                                
                            </select> 
                        </div>
                        <div class="form-group">
                            <label for="dependencia" class="col-sm-5 control-label">Dependencia:</label>
                            <select name="dependencia" id="dependencia" class="select2_single form-control col-sm-1" title="Seleccione dependencia" onchange="">
                                <?php 
                                 
                                if(empty($row[9])){
                                    echo '<option value=""> - </option>';
                                    $cl = "SELECT id_unico, nombre FROM gf_dependencia WHERE compania = $compania ORDER BY nombre ASC";
                                } else {
                                    echo '<option value="'.$row[9].'">'.ucwords(mb_strtolower($row[10])).'</option>';
                                    echo '<option value=""> - </option>';
                                    $cl = "SELECT id_unico, nombre FROM gf_dependencia WHERE compania = $compania AND  id_unico !='$row[9]' ORDER BY nombre ASC";
                                }
                                $cl = $mysqli->query($cl);
                                while($rowC = mysqli_fetch_assoc($cl)) { ?>
                                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));?></option>;
                                <?php } ?>
                                
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="vigencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Días Vigencia:</label>
                            <input type="text" name="vigencia" id="vigencia" class="form-control" maxlength="150" title="Ingrese Días Vigencia" onkeypress="return txtValida(event,'num')" placeholder="Días de Vigencia" value="<?php echo $row[12]?>">
                        </div> 
                        <div class="form-group" style="margin-top: 20px; ">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>      
            </div>
            <!-- Botones de consulta -->
            <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
                <table class="tablaC table-condensed" style="margin-left: -3px; ">
                    <thead>
                        
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="GF_RESPONSABLE_DOCUMENTO.php?id1=<?php echo md5($row[0]) ?>"><button class="btn btnInfo btn-primary">RESPONSABLE</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="GG_DOCUMENTO_CARACTERISTICA.php?id1=<?php echo md5($row[0]) ?>"><button class="btn btnInfo btn-primary">CARACTERISTICA</button></a><br/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Fin de Contenedor Principal -->
            <?php require_once('footer.php'); ?>
        </div>
    </div>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
    });
  </script>
</body>
</html>
