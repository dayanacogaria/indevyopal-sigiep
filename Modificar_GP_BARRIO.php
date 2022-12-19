<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#11/02/2019 |Erica G. | Agregar Ciudad
#05/07/2018 |Erica G. | Modificación Código
###################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con = new ConexionPDO();
require_once 'head.php';
$id = " ";
$queryCond = "";
if (isset($_GET["id"])) {
    $id = (($_GET["id"]));
    $queryCond = "SELECT b.id_unico, b.nombre, 
        c.id_unico, c.nombre, d.nombre 
    FROM gp_barrio b 
    LEFT JOIN gf_ciudad c ON b.ciudad = c.id_unico 
    LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
    WHERE md5(b.id_unico) = '$id'";
}
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);
?>
    <title>Modificar Barrio</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script> 
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="js/md5.pack.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <style>
    label #nombre-error, #ciudad-error{ 
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
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Barrio</h2>
                <a href="LISTAR_GP_BARRIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo $row[1]?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="ciudad" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <div class="form-group form-inline  col-md-4 col-lg-4">
                            <select name="ciudad" id="ciudad" class=" form-control select2" title="Seleccione Ciudad" style="height: auto; " required>
                                <?php 
                                if(!empty($row[2])){
                                    echo '<option value="'.$row[2].'">'.ucwords(mb_strtolower($row[3].' - '.$row[4])).'</option>';
                                    $idc = $row[2];
                                } else {
                                    echo '<option value="">Ciudad</option>';
                                    $idc = 0;
                                } 
                                $rows = $con->Listar("SELECT c.id_unico, c.nombre, d.nombre 
                                    FROM gf_ciudad c 
                                    LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
                                    WHERE c.id_unico != $idc 
                                    ORDER BY c.nombre ASC");
                                for ($s = 0; $s < count($rows); $s++) {
                                    echo '<option value="'.$rows[$s][0].'">'.ucwords(mb_strtolower($rows[$s][1].' - '.$rows[$s][2])).'</option>';
                                } ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <div class="form-group form-inline  col-md-4 col-lg-4">
                                <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event, 'car')" maxlength="100" title="Ingrese el nombre" placeholder="Nombre" value="<?php echo $row[1]; ?>" required style="width: 100%">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#ciudad").select2();
    </script>
    <script>
        function modificar(){
            var formData = new FormData($("#form")[0]);
            jsShowWindowLoad('Modificando..');
            $.ajax({
                type: 'POST',
                url: "jsonServicios/gp_BarrioJson.php?action=3",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    jsRemoveWindowLoad();
                    console.log(data);
                    if (data ==true) {
                        $("#mensaje").html("Información Modificada Correctamente");
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            document.location ='LISTAR_GP_BARRIO.php';
                        })
                    } else {
                        $("#mensaje").html("No Se Ha Podido Modificar La Información");
                        $("#mdlMensajes").modal("show");
                        $("#btnAceptar").click(function(){
                            $("#mdlMensajes").modal("hide");
                        })
                    }
                }
            })
        }
    </script>
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
