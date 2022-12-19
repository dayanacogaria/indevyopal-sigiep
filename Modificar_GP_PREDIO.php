<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();

$id = $_GET['id'];

$row = $con->Listar("SELECT 
    p.id_unico, p.codigo_catastral, 
    p.codigo_catastral_anterior, 
    p.matricula_inmobiliaria, 
    p.direccion, p.codigo_sig, 
    p.ciudad, c.nombre, 
    p.barrio, b.nombre, 
    p.ruta, r.nombre, 
    p.tipo_predio, tp.codigo, tp.nombre, 
    p.nombre, p.aniocreacion, 
    p.codigoigac, 
    p.estado, e.codigo, e.nombre, 
    p.estrato as id_estrato, 
    es.codigo as codigo_estrato, 
    es.nombre as nombre_estrato, 
    p.predioaso, pa.codigo_catastral, pa.nombre, 
    p.destino_economico, de.codigo, de.nombre, 
    p.ley_1175 
    FROM gp_predio1 p 
    LEFT JOIN gf_ciudad c               ON c.id_unico  = p.ciudad 
    LEFT JOIN gp_ruta r                 ON r.id_unico  = p.ruta
    LEFT JOIN gp_barrio b               ON b.id_unico  = p.barrio 
    LEFT JOIN gp_tipo_predio tp         ON tp.id_unico = p.tipo_predio
    LEFT JOIN gr_estado_predio e        ON e.id_unico  = p.estado  
    LEFT JOIN gp_estrato es             ON es.id_unico = p.estrato 
    LEFT JOIN gp_predio1 pa             ON pa.id_unico = p.predioaso 
    LEFT JOIN gr_destino_economico de   ON de.id_unico = p.destino_economico 
    WHERE md5(p.id_unico)='$id' ");
//CIUDAD
if(!empty($row[0][6])){
    $ciud= "SELECT c.id_unico, c.nombre, d.nombre 
        FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento=d.id_unico 
        WHERE c.id_unico != ".$row[0][6]."
        ORDER BY c.nombre ASC";
} else {
    $ciud= "SELECT c.id_unico, c.nombre, d.nombre 
        FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento=d.id_unico 
        ORDER BY c.nombre ASC";
}
$ciudad = $mysqli->query($ciud);


//RUTA
if(empty($row[0][10])) {
    $rut= "SELECT id_unico, nombre FROM gp_ruta ORDER BY nombre ASC";
} else {
    $rut= "SELECT id_unico, nombre FROM gp_ruta WHERE id_unico !=".$row[0][10]." ORDER BY nombre ASC";
}

$ruta = $mysqli->query($rut);

//TIPO PREDIO
if(empty($row[0][12])) {
    $tipo= "SELECT id_unico, nombre FROM gp_tipo_predio ORDER BY nombre ASC";
} else {
    $tipo= "SELECT id_unico, nombre FROM gp_tipo_predio WHERE id_unico !=".$row[0][12]." ORDER BY nombre ASC";
}

$tipoPredio = $mysqli->query($tipo);

#ESTRATO
if(empty($row[0][21])) {
    $estrato = "SELECT id_unico, codigo, nombre FROM gp_estrato WHERE tipo_estrato = 2 ORDER BY codigo ASC";
} else {
    $estrato = "SELECT id_unico, codigo, nombre FROM gp_estrato WHERE id_unico !=".$row[0][21]." AND tipo_estrato = 2 ORDER BY codigo ASC";
}
$estrato = $mysqli->query($estrato);

#ESTADO
if(empty($row[0][18])) {
    $estado = "SELECT id_unico, nombre FROM gr_estado_predio ORDER BY nombre ASC";
} else {
    $estado = "SELECT id_unico, nombre FROM gr_estado_predio WHERE id_unico !=".$row[0][18]."  ORDER BY nombre ASC";
}
$estado = $mysqli->query($estado);
        
#PREDIO ASOCIADO
if(empty($row[0][25])) {
    $predio = "SELECT id_unico, codigo_catastral, nombre FROM gp_predio1";
} else {
    $predio = "SELECT id_unico, codigo_catastral, nombre FROM gp_predio1 WHERE id_unico !=".$row[0][25];
}
$predio = $mysqli->query($predio);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<title>Modificar Predio</title>
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
label#codigo_catastral-error, #annio-error,#direccion-error, #codigo-error, #Ciudad-error, #participacion-error,  #barrio-error,  #ruta-error, #tipoPredio-error{
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
         <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Modificar Predio</h2>
            <a href="LISTAR_GP_PREDIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Predio <?php echo $row[0][1];?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javaScript:guardar()">
                    <p align="center" style="margin-bottom: 25px; margin-top: 10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <input type="hidden" value = "<?php echo $row[0][0];?>" name="id" id="id">
                        <div class="col-md-6">
                        <label for="codigo_catastral" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Código Catastral:</label>
                        <input type="text" value = "<?php echo $row[0][1];?>" name="codigo_catastral" id="codigo_catastral" class=" col-md-2 col-sm-2 form-control"  maxlength="30" title="Ingrese el código catastral"  placeholder="Código Catastral" required>
                        </div>
                        <div class="col-md-6">
                        <label for="nombre" class="col-md-4  col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Nombre:</label>
                        <input type="text" value = "<?php echo $row[0][15];?>" name="nombre" id="nombre" class="form-control col-md-2 col-sm-2" maxlength="100"  title="Ingrese Nombre"  placeholder="Nombre" onkeypress="return txtValida(event,'num_car')">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="matricula_inmobiliaria" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Matrícula Inmobiliaria:</label>
                        <input type="text" value = "<?php echo $row[0][3];?>" name="matricula_inmobiliaria" id="matricula_inmobiliaria" class="form-control col-md-2 col-sm-2" maxlength="30"  title="Ingrese Matrícula Inmobiliaria"  placeholder="Matrícula Inmobiliaria">
                        </div>
                        <div class="col-md-6">
                        <label for="annio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Año Creación:</label>
                        <input type="text" value = "<?php echo $row[0][16];?>" name="annio" id="annio" class="form-control col-md-2 col-sm-2" maxlength="4" minlength="4"  title="Ingrese Año de Creación"  placeholder="Año Creación" onkeypress="return txtValida(event,'num')">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="codigo" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Código SIG:</label>
                        <input type="text" value = "<?php echo $row[0][5];?>" name="codigo" id="codigo"  class="form-control col-md-2 col-sm-2" maxlength="100" title="Ingrese Código SIG"  placeholder="Código SIG">
                        </div>
                        <div class="col-md-6">
                        <label for="codigoIG" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Código IGAC:</label>
                        <input type="text" value = "<?php echo $row[0][17];?>" name="codigoIG" id="codigoIG" onkeypress="return txtValida(event,'num_car')" maxlength="50" class="form-control col-md-2 col-sm-2"  title="Ingrese Codigo IGAC"  placeholder="Código IGAC">
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">   
                        <div class="col-md-6">
                        <label for="direccion" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                        <input type="text" value = "<?php echo $row[0][4];?>" name="direccion" id="direccion" onkeypress="return txtValida(event,'direccion')" maxlength="100" class="form-control col-md-2 col-sm-2"  title="Ingrese Dirección"  placeholder="Dirección" required="required" >
                        </div>
                        <div class="col-md-6">
                        <label for="Ciudad" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                        <select name="Ciudad" id="Ciudad" required="required" style="width: 250px" class="form-control select2_single " title="Seleccione Ciudad" required="required">
                            <?php if(empty($row[0][6])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][6].'">'. ucwords(mb_strtolower($row[0][7])).'</option>';
                            }
                            while($rowc = mysqli_fetch_row($ciudad)){?>
                            <option value="<?php echo $rowc[0] ?>"><?php echo ucwords((mb_strtolower($rowc[1].' - '.$rowc[2])));}?></option>;
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: -15px;">
                        <div class="col-md-6">
                        <label for="estrato" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Estrato:</label>
                        <select name="estrato" id="estrato"  style="width: 250px" class="form-control select2_single" title="Seleccione Estrato">
                           <?php 
                           if(empty($row[0]['id_estrato'])) {
                                echo '<option value=""> - </option>'; 
                            } else {
                                echo '<option value="'.$row[0]['id_estrato'].'">'.$row[0]['codigo_estrato'].' - '. ucwords(mb_strtolower($row[0]['nombre_estrato'])).'</option>';
                            }
                            while($rowE = mysqli_fetch_assoc($estrato)){?>
                            <option value="<?php echo $rowE['id_unico'] ?>"><?php echo $rowE['codigo'].' - '.ucwords((mb_strtolower($rowE['nombre'])));}?></option>;
                        </select>
                        </div>
                        <div class="col-md-6">
                        <label for="barrio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Barrio:</label>
                        <select name="barrio" id="barrio"  style="width: 250px" class="form-control select2_single  col-md-2 col-sm-2" title="Seleccione Barrio">
                            <?php if(empty($row[0][8])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][8].'">'. ucwords(mb_strtolower($row[0][9])).'</option>';
                            }?>
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: 0px;">
                        <div class="col-md-6">
                        <label for="estado" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Estado:</label>
                        <select name="estado" id="estado"  style="width: 250px" class="form-control select2_single" title="Seleccione Estado" >
                            <?php if(empty($row[0][19])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][18].'">'.$row[0][19].' - '. ucwords(mb_strtolower($row[0][20])).'</option>';
                            }
                            while($rowe = mysqli_fetch_assoc($estado)){?>
                            <option value="<?php echo $rowe['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowe['nombre'])));}?></option>;
                        </select> 
                        </div>
                        <div class="col-md-6">
                        <label for="ruta" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Ruta:</label>
                        <select name="ruta" id="ruta"  style="width: 250px" class="form-control select2_single" title="Seleccione Ruta">
                            <?php if(empty($row[0][10])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][10].'">'. ucwords(mb_strtolower($row[0][11])).'</option>';
                            }?>
                            <?php while($rowr = mysqli_fetch_assoc($ruta)){?>
                            <option value="<?php echo $rowr['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowr['nombre'])));}?></option>;
                        </select> 
                        </div>
                    </div>
                    <div class="form-group form-inline" style="margin-top: 0px;">
                        <div class="col-md-6">
                        <label for="tipoPredio" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Tipo Predio:</label>
                        <select name="tipoPredio" id="tipoPredio"  style="width: 250px" class="form-control select2_single" title="Seleccione Tipo Predio" >
                            <?php if(empty($row[0][12])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][12].'">'.$row[0][13].' - '. ucwords(mb_strtolower($row[0][14])).'</option>';
                            }
                            while($rowtp = mysqli_fetch_assoc($tipoPredio)){?>
                            <option value="<?php echo $rowtp['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowtp['nombre'])));}?></option>;
                        </select> 
                        </div>
                        <div class="col-md-6">
                        <label for="predioa" class="col-md-4 col-sm-4 control-label"><strong style="color:#03C1FB;"></strong>Predio Asociado:</label>
                        <select name="predioa" id="predioa"  style="width: 250px" class="form-control select2_single col-md-2 col-sm-2" title="Seleccione Predio Asociado">
                            <?php if(empty($row[0][25])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[0][25].'">'.$row[0][26].' - '. ucwords(mb_strtolower($row[0][27])).'</option>';
                            }
                            while($rowpa = mysqli_fetch_row($predio)){?>
                            <option value="<?php echo $rowpa[0] ?>"><?php echo ucwords((mb_strtolower($rowpa[1].' - '.$rowpa[2])));}?></option>;
                        </select> 
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
        <!--Información adicional -->
        <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:36px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="GP_UNIDAD_VIVIENDA.php?predio=<?php echo md5($row[0][0])?>" class="btn btnInfo btn-primary" >Unidad Vivienda</a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
  
<?php require_once 'footer.php';?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="js/select/select2.full.js"></script>

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
<script>
        $("#Ciudad").change(function(){
            var form_data = { action:4, ciudad:$("#Ciudad").val() };
            $.ajax({
                type: "POST",
                url: "jsonServicios/gp_BarrioJson.php",
                data: form_data,
                success: function(response)
                { 
                    $("#barrio").html(response);
                }   
            }); 

        })
        function guardar(){
            var formData = new FormData($("#form")[0]);
            jsShowWindowLoad('Guardando..');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_PredioJson.php?action=3",
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
                            document.location ='LISTAR_GP_PREDIO.php';
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
    
</body>
    
  
  